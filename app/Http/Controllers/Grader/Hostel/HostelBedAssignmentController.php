<?php

namespace App\Http\Controllers\Grader\Hostel;

use App\Http\Controllers\Controller;
use App\Http\Controllers\StudentFinancial\GenerateSingleChallanController;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HostelBedAssignmentController extends Controller
{
    private const ON_CAMPUS_SESSION_TYPES = ['regular', 'non-regular'];

    private const OFF_CAMPUS_SESSION_TYPES = [
        'regular_outside',
        'non-regular_outside',
        'regular_outside_bedian',
        'non-regular_outside_bedian',
    ];
    public function index()
    {
        $hostelTypes = DB::table('HOSTEL_TYPE')
            ->where('HT_STATUS', 'E')
            ->select('ht_id', 'ht_name', 'ht_status', 'ht_location')
            ->get();

        $floors = DB::table('HOSTEL_FLOOR')
            ->where('HF_STATUS', 'E')
            ->select('hf_id', 'hf_name', 'HF_HOSTEL_TYPE', 'HF_NEW_HOSTEL_TYPE')
            ->get();

        $sessions = DB::table('academic_sessions')
            ->where('sess_id', '>', 164)
            ->orderBy('sess_id', 'desc')
            ->get(['sess_id', 'sess_name']);

        $defaultSessionId = optional(auth()->user()->profile_settings)->active_session ?? null;
        if (!$defaultSessionId && $sessions->isNotEmpty()) {
            $defaultSessionId = $sessions->first()->sess_id;
        }

        $hostelLocations = $hostelTypes
            ->pluck('ht_location')
            ->filter(fn ($location) => filled($location))
            ->unique()
            ->sort()
            ->values();

        return view('Hostel.hostel_bed_assignment', [
            'hostel_type' => $hostelTypes,
            'hostel_locations' => $hostelLocations,
            'floors_de' => $floors,
            'academic_sessions' => $sessions,
            'default_session_id' => $defaultSessionId,
        ]);
    }

    /**
     * Session-wise room/bed map — occupancy strictly from hostel_assign.ha_session.
     */
    public function getRoomData(Request $request)
    {
        $request->validate([
            'session_id' => 'required|integer',
            'hostel_type_id' => 'nullable|integer',
            'floor_id' => 'nullable|integer',
        ]);

        $sessionId = (int) $request->input('session_id');
        $hostelTypeId = $request->input('hostel_type_id');
        $floorId = $request->input('floor_id');

        $session = DB::table('academic_sessions')
            ->where('sess_id', $sessionId)
            ->first(['sess_id', 'sess_name']);

        $roomsQuery = DB::table('hostel_rooms as hr')
            ->where('hr.hr_status', '!=', 'D')
            ->orderBy('hr.hr_room_no');

        if ($hostelTypeId) {
            $roomsQuery->where('hr.hr_ht_id', $hostelTypeId);
        }
        if ($floorId) {
            $roomsQuery->where('hr.hr_hf_id', $floorId);
        }

        $rooms = $roomsQuery->get();
        $roomIds = $rooms->pluck('hr_id');

        $beds = DB::table('hostel_beds')
            ->whereIn('hb_room_id', $roomIds)
            ->orderBy('hb_bed_number')
            ->get()
            ->groupBy('hb_room_id');

        $bedIds = $beds->flatten()->pluck('hb_id')->filter()->values();

        $assignments = collect();
        if ($bedIds->isNotEmpty()) {
            $assignments = $this->getSessionAssignmentsForBeds($sessionId, $bedIds)->keyBy('ha_hb_id');
        }

        $result = $rooms->map(function ($room) use ($beds, $assignments) {
            $roomBeds = ($beds[$room->hr_id] ?? collect())->map(function ($bed) use ($assignments) {
                $assign = $assignments->get($bed->hb_id);
                return [
                    'hb_id' => $bed->hb_id,
                    'hb_bed_number' => $bed->hb_bed_number,
                    'is_occupied' => $assign ? 'Y' : 'N',
                    'enrollment_no' => $assign->ha_enrolment_no ?? null,
                    'student_name' => $assign->student_name ?? null,
                    'ha_id' => $assign->ha_id ?? null,
                ];
            })->values();

            return [
                'hr_id' => $room->hr_id,
                'hr_room_no' => $room->hr_room_no,
                'hr_capacity' => $room->hr_capacity,
                'hr_ht_id' => $room->hr_ht_id,
                'hr_hf_id' => $room->hr_hf_id,
                'hr_status' => $room->hr_status,
                'hr_ac_availability' => $room->hr_ac_availability ?? 'NO',
                'hr_heater_availability' => $room->hr_heater_availability ?? 'NO',
                'hr_refrigerator_availability' => $room->hr_refrigerator_availability ?? 'NO',
                'beds' => $roomBeds,
            ];
        });

        $stats = $this->getSessionSeatStats($sessionId, $hostelTypeId, $floorId);

        return response()->json([
            'success' => true,
            'session_id' => $sessionId,
            'session_name' => $session->sess_name ?? null,
            'assignment_source' => 'hostel_assign',
            'rooms' => $result,
            'stats' => $stats,
        ]);
    }

    public function assignBed(Request $request)
    {
        $validated = $request->validate([
            'bed_id' => 'required|integer|exists:HOSTEL_BEDS,hb_id',
            'student_input' => 'required|string|max:50',
            'session_id' => 'required|integer',
            'hostel_location' => 'required|string|max:50',
            'hostel_assignment_type' => 'required|string|in:regular,non_regular,non-regular',
            'hostel_session_type' => 'nullable|string|max:50',
            'checkin_date' => 'nullable|date',
        ]);

        $username = strtolower(auth()->user()->employee_id ?? auth()->user()->username);
        $bedId = (int) $validated['bed_id'];
        $studentInput = $validated['student_input'];
        $sessionId = (int) $validated['session_id'];
        $hostelLocation = strtolower(trim($validated['hostel_location']));
        $assignmentTypeUi = strtolower(trim($validated['hostel_assignment_type']));
        $checkinDate = $validated['checkin_date'] ?? null;

        // Prefer server-side resolve from location + Regular/Non Regular
        $hostelSessionType = $this->resolveHostelSessionType($hostelLocation, $assignmentTypeUi);
        if (!$hostelSessionType) {
            return response()->json([
                'error' => 'Invalid hostel location / assignment type combination.',
            ], 422);
        }

        try {
            DB::beginTransaction();

            $bedOccupiedInSession = DB::table('hostel_assign')
                ->where('ha_hb_id', $bedId)
                ->where('ha_session', $sessionId)
                ->whereNotNull('ha_hb_id')
                ->exists();

            if ($bedOccupiedInSession) {
                return response()->json(['error' => 'This bed is already assigned for the selected session.'], 400);
            }

            $bedHostel = DB::table('hostel_beds as hb')
                ->join('hostel_rooms as hr', 'hb.hb_room_id', '=', 'hr.hr_id')
                ->join('hostel_type as ht', 'hr.hr_ht_id', '=', 'ht.ht_id')
                ->where('hb.hb_id', $bedId)
                ->first(['ht.ht_location', 'ht.ht_hostel_type', 'ht.ht_name']);

            if ($bedHostel && $bedHostel->ht_location && !$this->hostelLocationsMatch($hostelLocation, $bedHostel->ht_location)) {
                DB::rollBack();
                return response()->json([
                    'error' => 'Selected bed does not belong to the chosen hostel location.',
                ], 400);
            }

            $challanController = new GenerateSingleChallanController();
            $isCharacter = !is_numeric($studentInput);

            if ($isCharacter) {
                $appId = $challanController->getAppIdByEnrollmentNumber(strtoupper($studentInput));
                $enrollmentNo = $studentInput;
                if (!$appId) {
                    DB::rollBack();
                    return response()->json(['error' => 'No APP ID Found'], 404);
                }
            } else {
                $appId = (int) $studentInput;
                $enrollmentNo = $challanController->getEnrollmentNumberByAppId($appId);
                if (!$enrollmentNo) {
                    DB::rollBack();
                    return response()->json(['error' => 'No Enrollment Number Found'], 404);
                }
            }

            $hostelGenderType = filled($bedHostel->ht_hostel_type ?? null) ? (int) $bedHostel->ht_hostel_type : null;
            if ($hostelGenderType !== null) {
                $studentGender = $this->getStudentGender($enrollmentNo, $appId);
                $studentGenderType = $this->resolveGenderType($studentGender);
                        //    dd($studentGenderType);
                if ($studentGenderType === null) {
                    DB::rollBack();
                    return response()->json([
                        'error' => 'Student gender could not be verified from personal info.',
                    ], 400);
                }

                if ($studentGenderType !== $hostelGenderType) {
                    $hostelName = $bedHostel->ht_name ?? ($hostelGenderType === 1  ? 'Boys Hostel' : 'Girls Hostel');
                    DB::rollBack();
                    return response()->json([
                        'error' => $this->genderTypeLabel($studentGenderType) . ' student cannot be assigned to '
                            . $hostelName . ' (' . $this->genderTypeLabel($hostelGenderType) . ' hostel only).',
                    ], 400);
                }
            }

            $challanQuery = DB::table('student_challan')
                ->where(function ($q) use ($appId, $enrollmentNo) {
                    $q->where('sc_app_id', $appId)
                        ->orWhere(DB::raw('UPPER(sc_enrollment_no)'), strtoupper($enrollmentNo));
                })
                ->where('sc_challan_type', 'Hostel_Fee')
                ->where('sc_session_id', $sessionId)
                // ->where('sc_challan_status', 'PAID')
                ;
                //   dd($challanQuery);
            $this->applyHostelLocationChallanFilter($challanQuery, $hostelLocation);

            $challanPaid = $challanQuery->exists();


            $hostelApplicant = DB::table('hostel_applicants')
                ->where(DB::raw('UPPER(happ_enrollment_no)'), strtoupper($enrollmentNo))
                ->where('happ_allowed_status', 'Allowed')
                ->where('happ_session', $sessionId)
                ->first();

            if (!$hostelApplicant) {
                DB::table('hostel_applicants')->insert([
                    'happ_enrollment_no' => $enrollmentNo,
                    'happ_allowed_status' => 'Allowed',
                    'happ_session' => $sessionId,
                    'happ_hostel_location_preference' => $hostelLocation,
                    'happ_entry_date' => now(),
                    'happ_entry_user_id' => $username,
                ]);
            } else {
                DB::table('hostel_applicants')
                    ->where('happ_enrollment_no', $hostelApplicant->happ_enrollment_no)
                    ->where('happ_session', $sessionId)
                    ->update([
                        'happ_hostel_location_preference' => $hostelLocation,
                    ]);
            }

            // Match existing assignment by enrollment OR aai_id (new applicants are keyed by ha_aai_id).
            $existingAssign = DB::table('hostel_assign as ha')
                ->join('hostel_beds as hb', 'ha.ha_hb_id', '=', 'hb.hb_id')
                ->join('hostel_rooms as hr', 'hb.hb_room_id', '=', 'hr.hr_id')
                ->where('ha.ha_session', $sessionId)
                ->where('ha.ha_status', 'E')
                ->whereNotNull('ha.ha_hb_id')
                ->where(function ($q) use ($enrollmentNo, $appId) {
                    $q->whereRaw('UPPER(TRIM(ha.ha_enrolment_no)) = ?', [strtoupper(trim($enrollmentNo))])
                        ->orWhere('ha.ha_aai_id', $appId);
                })
                ->select('hr.hr_room_no', 'hr.hr_ht_id')
                ->first();

            if ($existingAssign) {
                $buildingName = $existingAssign->hr_ht_id == 1 ? 'Boys Hostel' : 'Girls Hostel';
                DB::rollBack();
                return response()->json([
                    'error' => "Student already assigned in {$buildingName}, Room {$existingAssign->hr_room_no} for this session.",
                ], 400);
            }

            $assignRecord = DB::table('hostel_assign')
                ->where('ha_session', $sessionId)
                ->where(function ($q) use ($enrollmentNo, $appId) {
                    $q->whereRaw('UPPER(TRIM(ha_enrolment_no)) = ?', [strtoupper(trim($enrollmentNo))])
                        ->orWhere('ha_aai_id', $appId);
                })
                ->orderByRaw('CASE WHEN ha_hb_id IS NOT NULL THEN 0 ELSE 1 END')
                ->first();

            if ($assignRecord === null) {
                DB::table('hostel_assign')->insert([
                    'ha_enrolment_no' => strtoupper(trim($enrollmentNo)),
                    'ha_aai_id' => $appId,
                    'ha_hb_id' => $bedId,
                    'ha_session' => $sessionId,
                    'ha_status' => 'E',
                    'ha_hostel_location_preference' => $hostelLocation,
                    'ha_hostel_session_type' => $hostelSessionType,
                    'ha_entry_user_id' => $username,
                ]);
            } else {
                DB::table('hostel_assign')
                    ->where('ha_id', $assignRecord->ha_id)
                    ->update([
                        'ha_hb_id' => $bedId,
                        'ha_enrolment_no' => $assignRecord->ha_enrolment_no ?: $enrollmentNo,
                        'ha_aai_id' => $assignRecord->ha_aai_id ?: $appId,
                        'ha_status' => 'E',
                        'ha_hostel_location_preference' => $hostelLocation,
                        'ha_hostel_session_type' => $hostelSessionType,
                        'ha_entry_user_id' => $username,
                    ]);
            }

            $this->syncBedPhysicalStatus($bedId);

            if ($checkinDate) {
                $this->upsertHostelAllocationCheckin(
                    $sessionId,
                    $enrollmentNo,
                    $appId,
                    $checkinDate
                );
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Bed assigned successfully for this session.',
                'hostel_session_type' => $hostelSessionType,
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

    public function getVacateDetails(Request $request)
    {
        $request->validate([
            'session_id' => 'required|integer',
            'bed_id' => 'required|integer',
        ]);

        $sessionId = (int) $request->input('session_id');
        $bedId = (int) $request->input('bed_id');

        $assignment = DB::table('hostel_assign')
            ->where('ha_hb_id', $bedId)
            ->where('ha_session', $sessionId)
            ->whereNotNull('ha_hb_id')
            ->first();

        if (!$assignment) {
            return response()->json(['success' => false, 'error' => 'No assignment found for this bed in the selected session.'], 404);
        }

        $enrollmentNo = trim((string) ($assignment->ha_enrolment_no ?? ''));
        $appId = $assignment->ha_aai_id ?? null;

        $history = DB::table('HOSTEL_ALLOCATION_HISTORY')
            ->where('HAH_SESSION_ID', $sessionId)
            ->where(function ($query) use ($enrollmentNo, $appId) {
                if ($enrollmentNo !== '') {
                    $query->whereRaw('UPPER(TRIM(HAH_STUDENT_ENROLL_NO)) = ?', [strtoupper($enrollmentNo)]);
                }
                if ($appId) {
                    $query->orWhere('HAH_APP_ID', $appId);
                }
            })
            ->orderByDesc('ID')
            ->first();

        $checkinDate = null;
        if ($history && filled($history->hah_checkin_date ?? $history->HAH_CHECKIN_DATE ?? null)) {
            $checkinDate = date('Y-m-d', strtotime((string) ($history->hah_checkin_date ?? $history->HAH_CHECKIN_DATE)));
        }

        return response()->json([
            'success' => true,
            'enrollment_no' => $enrollmentNo ?: null,
            'app_id' => $appId,
            'checkin_date' => $checkinDate,
            'history_id' => $history->id ?? $history->ID ?? null,
        ]);
    }

    public function vacateBed(Request $request)
    {
        $validated = $request->validate([
            'bed_id' => 'required|integer|exists:HOSTEL_BEDS,hb_id',
            'session_id' => 'required|integer',
            'checkin_date' => 'required|date',
            'checkout_date' => 'required|date|after_or_equal:checkin_date',
        ]);

        $bedId = (int) $validated['bed_id'];
        $sessionId = (int) $validated['session_id'];
        $checkinDate = $validated['checkin_date'];
        $checkoutDate = $validated['checkout_date'];
        $username = strtolower(auth()->user()->employee_id ?? auth()->user()->username);

        try {
            DB::beginTransaction();

            $assignment = DB::table('hostel_assign')
                ->where('ha_hb_id', $bedId)
                ->where('ha_session', $sessionId)
                ->whereNotNull('ha_hb_id')
                ->first();

            if (!$assignment) {
                DB::rollBack();
                return response()->json(['error' => 'No assignment found for this bed in the selected session.'], 404);
            }

            $enrollmentNo = trim((string) ($assignment->ha_enrolment_no ?? ''));
            $appId = $assignment->ha_aai_id ?? null;

            $this->upsertHostelAllocationCheckout(
                $sessionId,
                $enrollmentNo,
                $appId,
                $checkinDate,
                $checkoutDate
            );

            DB::table('hostel_assign')
                ->where('ha_id', $assignment->ha_id)
                ->update([
                    'ha_hb_id' => null,
                    'ha_entry_user_id' => $username,
                ]);

            $this->syncBedPhysicalStatus($bedId);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Bed vacated successfully for this session.']);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

    public function getPaidUnassigned(Request $request)
    {
        $request->validate([
            'session_id' => 'required|integer',
            'gender' => 'required|in:male,female,Male,Female',
            'hostel_type_id' => 'nullable|integer',
            'hostel_location' => 'nullable|string',
        ]);

        $sessionId = (int) $request->input('session_id');
        $gender = ucfirst(strtolower($request->input('gender')));
        $hostelLocation = strtolower(trim((string) $request->input('hostel_location')));

        try {
            // Paid + allowed students who do NOT already have an active bed in hostel_assign.
            // Assignment may be stored by enrollment and/or ha_aai_id (new applicants).
            $newStudents = DB::table('admission_apply_info as aai')
                ->join('admission_personal_info as api', 'aai.aai_api_id', '=', 'api.api_id')
                ->join('student_challan as sc', function ($join) use ($sessionId) {
                    $join->on('aai.aai_id', '=', 'sc.sc_app_id')
                        ->where('sc.sc_challan_type', 'Hostel_Fee')
                        ->where('sc.sc_challan_status', 'PAID')
                        ->where('sc.sc_session_id', $sessionId);
                });

            if ($hostelLocation) {
                $this->applyHostelLocationChallanFilter($newStudents, $hostelLocation, 'sc.sc_session_type');
            }

            $newStudents = $newStudents
                ->where('aai.aai_sdpo_sess_id', $sessionId)
                ->where('api.api_allow_hostel', 'Allowed')
                ->where('api.api_gender', $gender)
                ->where('aai.aai_status', 'Selected')
                ->whereNotExists(function ($q) use ($sessionId) {
                    $q->select(DB::raw(1))
                        ->from('hostel_assign as ha')
                        ->where('ha.ha_session', $sessionId)
                        ->where('ha.ha_status', 'E')
                        ->whereNotNull('ha.ha_hb_id')
                        ->where(function ($match) {
                            $match->whereRaw(
                                'aai.cms_sai_enrollment_no IS NOT NULL AND UPPER(TRIM(ha.ha_enrolment_no)) = UPPER(TRIM(aai.cms_sai_enrollment_no))'
                            )
                                ->orWhereColumn('ha.ha_aai_id', 'aai.aai_id')
                                ->orWhereColumn('ha.ha_api_id', 'aai.aai_api_id');
                        });
                })
                ->select(
                    'aai.cms_sai_enrollment_no as id',
                    'api.api_name as name',
                    DB::raw('NULL as room_number'),
                    DB::raw("'Not Assigned' as room_status")
                );

            $oldStudents = DB::table('hostel_applicants as happ')
                ->join('students_apply_info as sai', function ($join) {
                    $join->whereRaw('UPPER(TRIM(happ.happ_enrollment_no)) = UPPER(TRIM(sai.sai_enrollment_no))');
                })
                ->join('students_personal_info as spi', 'sai.sai_spi_id', '=', 'spi.spi_id')
                ->join('student_challan as sc', function ($join) use ($sessionId) {
                    $join->whereRaw('UPPER(TRIM(sc.sc_enrollment_no)) = UPPER(TRIM(sai.sai_enrollment_no))')
                        ->where('sc.sc_challan_type', 'Hostel_Fee')
                        ->where('sc.sc_challan_status', 'PAID')
                        ->where('sc.sc_session_id', $sessionId);
                });

            if ($hostelLocation) {
                $this->applyHostelLocationChallanFilter($oldStudents, $hostelLocation, 'sc.sc_session_type');
            }

            $oldStudents = $oldStudents
                ->where('happ.happ_session', $sessionId)
                ->where('happ.happ_allowed_status', 'Allowed')
                ->whereRaw('LOWER(TRIM(spi.spi_gender)) = ?', [strtolower($gender)])
                ->whereNotExists(function ($q) use ($sessionId) {
                    $q->select(DB::raw(1))
                        ->from('hostel_assign as ha')
                        ->where('ha.ha_session', $sessionId)
                        ->where('ha.ha_status', 'E')
                        ->whereNotNull('ha.ha_hb_id')
                        ->whereRaw(
                            'UPPER(TRIM(ha.ha_enrolment_no)) = UPPER(TRIM(happ.happ_enrollment_no))'
                        );
                })
                ->select(
                    'sai.sai_enrollment_no as id',
                    'spi.spi_name as name',
                    DB::raw('NULL as room_number'),
                    DB::raw("'Not Assigned' as room_status")
                );

            $students = $newStudents->union($oldStudents)->get()
                ->unique(function ($student) {
                    return strtoupper(trim((string) ($student->id ?? '')));
                })
                ->filter(function ($student) {
                    return filled($student->id ?? null);
                })
                ->values();

            return response()->json([
                'success' => true,
                'students' => $students,
                'count' => $students->count(),
                'gender' => strtolower($gender),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading students: ' . $e->getMessage(),
            ], 500);
        }
    }

    private function getSessionAssignmentsForBeds($sessionId, $bedIds)
    {
        return DB::table('hostel_assign as ha')
            ->where('ha.ha_session', $sessionId)
            ->where('ha.ha_status', 'E')       // only active assignments count as occupied
            ->whereNotNull('ha.ha_hb_id')
            ->whereIn('ha.ha_hb_id', $bedIds)
            ->select(
                'ha.ha_id',
                'ha.ha_hb_id',
                'ha.ha_enrolment_no',
                DB::raw("COALESCE(
                    (SELECT api.api_name FROM admission_personal_info api WHERE api.api_id = ha.ha_api_id),
                    (SELECT api.api_name FROM admission_apply_info aai
                     JOIN admission_personal_info api ON aai.aai_api_id = api.api_id
                     WHERE UPPER(aai.cms_sai_enrollment_no) = UPPER(ha.ha_enrolment_no)),
                    (SELECT spi.spi_name FROM students_personal_info spi
                     JOIN students_apply_info sai ON spi.spi_id = sai.sai_spi_id
                     WHERE UPPER(sai.sai_enrollment_no) = UPPER(ha.ha_enrolment_no))
                ) as student_name")
            )
            ->get();
    }

    private function getSessionSeatStats($sessionId, $hostelTypeId = null, $floorId = null)
    {
        $totalBedsQuery = DB::table('hostel_beds as hb')
            ->join('hostel_rooms as hr', 'hb.hb_room_id', '=', 'hr.hr_id')
            ->where('hr.hr_status', 'E');

        if ($hostelTypeId) {
            $totalBedsQuery->where('hr.hr_ht_id', $hostelTypeId);
        }
        if ($floorId) {
            $totalBedsQuery->where('hr.hr_hf_id', $floorId);
        }

        $totalBeds = $totalBedsQuery->count();

        $assignedQuery = DB::table('hostel_assign as ha')
            ->join('hostel_beds as hb', 'ha.ha_hb_id', '=', 'hb.hb_id')
            ->join('hostel_rooms as hr', 'hb.hb_room_id', '=', 'hr.hr_id')
            ->where('ha.ha_session', $sessionId)
            ->where('ha.ha_status', 'E')
            ->whereNotNull('ha.ha_hb_id');

        if ($hostelTypeId) {
            $assignedQuery->where('hr.hr_ht_id', $hostelTypeId);
        }
        if ($floorId) {
            $assignedQuery->where('hr.hr_hf_id', $floorId);
        }

        $assignedBeds = $assignedQuery->distinct()->count('ha.ha_hb_id');

        return [
            'total_beds' => $totalBeds,
            'assigned_beds' => $assignedBeds,
            'available_beds' => max(0, $totalBeds - $assignedBeds),
        ];
    }

    /**
     * Match paid hostel challan sc_session_type to the selected hostel location.
     */
    private function applyHostelLocationChallanFilter($query, string $hostelLocation, string $sessionTypeColumn = 'sc_session_type'): void
    {
        $location = strtolower(trim($hostelLocation));

        if (in_array($location, ['off_campus', 'out_of_campus'], true)) {
            $query->whereRaw(
                'LOWER(TRIM(' . $sessionTypeColumn . ')) IN (' . $this->sessionTypeSqlInList(self::OFF_CAMPUS_SESSION_TYPES) . ')'
            );
            return;
        }

        if ($location === 'on_campus') {
            $query->where(function ($q) use ($sessionTypeColumn) {
                $q->whereNull($sessionTypeColumn)
                    ->orWhereRaw(
                        'LOWER(TRIM(' . $sessionTypeColumn . ')) IN (' . $this->sessionTypeSqlInList(self::ON_CAMPUS_SESSION_TYPES) . ')'
                    );
            });
        }
    }

    private function hostelLocationsMatch(string $selectedLocation, string $bedLocation): bool
    {
        $selected = strtolower(trim($selectedLocation));
        $bed = strtolower(trim($bedLocation));

        if ($selected === $bed) {
            return true;
        }

        $offCampusLocations = ['off_campus', 'out_of_campus'];

        return in_array($selected, $offCampusLocations, true)
            && in_array($bed, $offCampusLocations, true);
    }

    /**
     * Resolve ha_hostel_session_type from top filter location + Regular/Non Regular choice.
     */
    private function resolveHostelSessionType(string $hostelLocation, string $assignmentTypeUi): ?string
    {
        $location = strtolower(trim($hostelLocation));
        $type = strtolower(trim($assignmentTypeUi));
        $isOffCampus = in_array($location, ['off_campus', 'out_of_campus'], true);
        $isNonRegular = in_array($type, ['non_regular', 'non-regular'], true);

        if (!in_array($location, ['on_campus', 'off_campus', 'out_of_campus'], true)) {
            return null;
        }

        if ($isNonRegular) {
            return $isOffCampus ? 'non-regular_outside' : 'non-regular';
        }

        if (in_array($type, ['regular'], true)) {
            return $isOffCampus ? 'regular_outside' : 'regular';
        }

        return null;
    }

    /**
     * Resolve student gender from continuing or new admission personal info.
     */
    private function getStudentGender(string $enrollmentNo, ?int $appId = null): ?string
    {
        $gender = null;

        if (filled($enrollmentNo)) {
            $gender = DB::table('students_apply_info as sai')
                ->join('students_personal_info as spi', 'sai.sai_spi_id', '=', 'spi.spi_id')
                ->where(DB::raw('UPPER(sai.sai_enrollment_no)'), strtoupper($enrollmentNo))
                ->where('sai.SAI_STUDENT_STATUS' , 'Active')
                ->value('spi.spi_gender');
        }
//  dd($gender,123);
        if (!$gender && $appId) {
            $gender = DB::table('admission_apply_info as aai')
                ->join('admission_personal_info as api', 'aai.aai_api_id', '=', 'api.api_id')
                ->where('aai.aai_id', $appId)
                ->value('api.api_gender');
        }

        if (!$gender && filled($enrollmentNo)) {
            $gender = DB::table('admission_apply_info as aai')
                ->join('admission_personal_info as api', 'aai.aai_api_id', '=', 'api.api_id')
                ->where(DB::raw('UPPER(aai.cms_sai_enrollment_no)'), strtoupper($enrollmentNo))
                ->value('api.api_gender');
        }




        return filled($gender) ? trim((string) $gender) : null;
    }

    /**
     * Normalize gender to hostel type: 1 = Boys, 2 = Girls.
     */
    private function resolveGenderType(?string $gender): ?int
    {


        if (!filled($gender)) {
            return null;
        }

        $normalized = strtolower(trim($gender));
        // dd($normalized);

        if (in_array($normalized, ['male','category x', 'boys', 'boy', 'm', 'bh', '1'], true)) {
            return 1;
        }

        if (in_array($normalized, ['female','category x', 'girls', 'girl', 'f', 'gh', '2'], true)) {
            return 2;
        }

        // dd($normalized);

        // if (in_array($normalized, ['category x', 'category', 'X',  '3'], true)) {
        //     //    dd($gender);

        //     return 3;
        // }

        return null;
    }

    private function genderTypeLabel(int $genderType): string
    {
        return $genderType === 1 ? 'Male' : 'Female';
    }

    /**
     * Upsert optional hostel check-in into HOSTEL_ALLOCATION_HISTORY.
     */
    private function upsertHostelAllocationCheckin(int $sessionId, string $enrollmentNo, $appId, string $checkinDate): void
    {
        $entryUsername = auth()->user()->username
            ?? auth()->user()->employee_id
            ?? auth()->user()->id;

        $existing = DB::table('HOSTEL_ALLOCATION_HISTORY')
            ->where('HAH_SESSION_ID', $sessionId)
            ->where(function ($query) use ($enrollmentNo, $appId) {
                $query->whereRaw('UPPER(TRIM(HAH_STUDENT_ENROLL_NO)) = ?', [strtoupper(trim($enrollmentNo))]);
                if ($appId) {
                    $query->orWhere('HAH_APP_ID', $appId);
                }
            })
            ->first();

        $data = [
            'HAH_SESSION_ID' => $sessionId,
            'HAH_STUDENT_ENROLL_NO' => $enrollmentNo,
            'HAH_APP_ID' => $appId,
            'HAH_CHECKIN_DATE' => $checkinDate,
            'HAH_ENTRY_USERNAME' => $entryUsername,
            'HAH_ENTRY_DATETIME' => DB::raw('SYSDATE'),
            'HAH_ENTRY_TIMESTAMP' => DB::raw('SYSTIMESTAMP'),
        ];

        if ($existing) {
            DB::table('HOSTEL_ALLOCATION_HISTORY')
                ->where('ID', $existing->id ?? $existing->ID)
                ->update([
                    'HAH_STUDENT_ENROLL_NO' => $enrollmentNo,
                    'HAH_APP_ID' => $appId ?: ($existing->hah_app_id ?? $existing->HAH_APP_ID ?? null),
                    'HAH_CHECKIN_DATE' => $checkinDate,
                    'HAH_ENTRY_USERNAME' => $entryUsername,
                    'HAH_ENTRY_DATETIME' => DB::raw('SYSDATE'),
                    'HAH_ENTRY_TIMESTAMP' => DB::raw('SYSTIMESTAMP'),
                ]);
            return;
        }

        DB::table('HOSTEL_ALLOCATION_HISTORY')->insert($data);
    }

    /**
     * Upsert check-in / check-out on bed removal.
     */
    private function upsertHostelAllocationCheckout(
        int $sessionId,
        string $enrollmentNo,
        $appId,
        string $checkinDate,
        string $checkoutDate
    ): void {
        $entryUsername = auth()->user()->username
            ?? auth()->user()->employee_id
            ?? auth()->user()->id;

        $existing = DB::table('HOSTEL_ALLOCATION_HISTORY')
            ->where('HAH_SESSION_ID', $sessionId)
            ->where(function ($query) use ($enrollmentNo, $appId) {
                if ($enrollmentNo !== '') {
                    $query->whereRaw('UPPER(TRIM(HAH_STUDENT_ENROLL_NO)) = ?', [strtoupper(trim($enrollmentNo))]);
                }
                if ($appId) {
                    $query->orWhere('HAH_APP_ID', $appId);
                }
            })
            ->orderByDesc('ID')
            ->first();

        $data = [
            'HAH_SESSION_ID' => $sessionId,
            'HAH_STUDENT_ENROLL_NO' => $enrollmentNo ?: null,
            'HAH_APP_ID' => $appId,
            'HAH_CHECKIN_DATE' => $checkinDate,
            'HAH_CHECKOUT_DATE' => $checkoutDate,
            'HAH_ENTRY_USERNAME' => $entryUsername,
            'HAH_ENTRY_DATETIME' => DB::raw('SYSDATE'),
            'HAH_ENTRY_TIMESTAMP' => DB::raw('SYSTIMESTAMP'),
        ];

        if ($existing) {
            DB::table('HOSTEL_ALLOCATION_HISTORY')
                ->where('ID', $existing->id ?? $existing->ID)
                ->update($data);
            return;
        }

        DB::table('HOSTEL_ALLOCATION_HISTORY')->insert($data);
    }

    private function sessionTypeSqlInList(array $types): string
    {
        return implode(', ', array_map(
            fn ($type) => "'" . strtolower($type) . "'",
            $types
        ));
    }

    /**
     * Keep hostel_beds in sync — delegates to the shared service method
     * so every controller uses exactly the same rule.
     */
    private function syncBedPhysicalStatus($bedId): void
    {
        \App\Services\Hostel\HostelSeatStatsService::syncBedPhysicalStatus((int) $bedId);
    }
}
