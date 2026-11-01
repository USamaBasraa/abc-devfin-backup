<?php

namespace App\Http\Controllers\Grader\Hostel;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\StudentFinancial\GenerateSingleChallanController;
use Illuminate\Support\Facades\DB;

/**
 * HostelAllocationController
 * 
 * Handles automatic hostel seat allocation and challan generation
 * when "Disable Map Display" checkbox is enabled during applicant allowance.
 */
class HostelAllocationController extends Controller
{
    /**
     * Allocate seat and generate challan for an applicant
     * 
     * @param array $applicantData - Applicant information (aai_id/enrollment_no, gender, assignment_id)
     * @param int $sessionId - Academic session ID
     * @param string $location - Hostel location preference ('on_campus', 'off_campus', or 'both')
     * @param string $applicantType - 'new' or 'old'
     * @return array - Allocation result with success status and details
     */
    public function allocateSeatAndGenerateChallan($applicantData, $sessionId, $location, $applicantType)
    {
        // DB::beginTransaction(); // Removed to use parent transaction

        try {
            // Step 1: Find available bed
            // We use sharedLock to prevent race conditions during reading
            $bed = $this->findAvailableBed($sessionId, $applicantData['gender'], $location);

            if (!$bed) {
                $locationLabel = ucwords(str_replace('_', ' ', $location));
                // DB::rollBack();
                return [
                    'success' => false,
                    'message' => "No available beds in {$locationLabel}. Please try a different location or contact administration."
                ];
            }

            // Step 2: Assign bed to applicant
            $bedAssignResult = $this->assignBedToApplicant($bed->hb_id, $applicantData['assignment_id']);

            if (!$bedAssignResult['success']) {
                // DB::rollBack();
                return [
                    'success' => false,
                    'message' => $bedAssignResult['error']
                ];
            }


            // $targetSessionType = $this->resolveSessionTypeFromBedId($bed->hb_id);
            $targetSessionType = $applicantData['session_type'];

            if (!$targetSessionType) {
                return [
                    'success' => false,
                    'message' => 'Unable to determine hostel session type for the selected bed.',
                ];
            }

            // Step 3: Cancel unpaid challans that do not match the target session type
            $cancelResult = $this->cancelMismatchedUnpaidHostelChallans(
                $sessionId,
                $applicantType,
                $applicantData,
                $targetSessionType
            );

            if (!$cancelResult['success']) {
                return [
                    'success' => false,
                    'message' => $cancelResult['error'],
                ];
            }

            $challanLocationSwitched = !empty($cancelResult['cancelled']);

            // Step 4: Reuse existing challan or generate a new one
            $existingChallan = $this->findExistingHostelChallan(
                $sessionId,
                $applicantType,
                $applicantData,
                $targetSessionType
            );

            if ($existingChallan) {
                $challan = $this->formatChallanDetails($existingChallan);
                $challanLinkResult = $this->linkChallanToAssignment($applicantData['assignment_id'], $challan['id']);

                if (!$challanLinkResult['success']) {
                    return [
                        'success' => false,
                        'message' => $challanLinkResult['error']
                    ];
                }

                return [
                    'success' => true,
                    'assignment_id' => $applicantData['assignment_id'],
                    'allocationDetails' => [
                        'location' => ucwords(str_replace('_', ' ', $location)),
                        'hostel' => $bed->ht_name,
                        'floor' => $bed->hf_floor_name,
                        'room' => $bed->hr_room_number,
                        'bed' => $bed->hb_bed_number,
                        'bed_id' => $bed->hb_id
                    ],
                    'challanDetails' => $challan,
                    'challanAlreadyExists' => true,
                    'message' => $challanLocationSwitched
                        ? 'Seat allocated. Previous unpaid hostel challan cancelled and existing challan linked.'
                        : 'Seat allocated. Existing hostel challan reused — no duplicate generated.',
                ];
            }

            $challan = $this->generateHostelChallan(
                $applicantData,
                $sessionId,
                $applicantType,
                $bed->hb_id,
                $targetSessionType
            );

            if (!$challan || isset($challan['error'])) {
                // DB::rollBack();
                $errorMsg = isset($challan['error']) ? $challan['error'] : 'Failed to generate hostel fee challan';
                return [
                    'success' => false,
                    'message' => $errorMsg
                ];
            }

            // Step 4: Link challan to assignment
            $challanLinkResult = $this->linkChallanToAssignment($applicantData['assignment_id'], $challan['id']);

            if (!$challanLinkResult['success']) {
                // DB::rollBack();
                return [
                    'success' => false,
                    'message' => $challanLinkResult['error']
                ];
            }

            // Step 5: Return success with details
            // DB::commit();

            return [
                'success' => true,
                'assignment_id' => $applicantData['assignment_id'],
                'allocationDetails' => [
                    'location' => ucwords(str_replace('_', ' ', $location)),
                    'hostel' => $bed->ht_name,
                    'floor' => $bed->hf_floor_name,
                    'room' => $bed->hr_room_number,
                    'bed' => $bed->hb_bed_number,
                    'bed_id' => $bed->hb_id
                ],
                'challanDetails' => $challan,
                'message' => $challanLocationSwitched
                    ? 'Seat allocated. Previous unpaid hostel challan cancelled and new challan generated.'
                    : 'Seat allocated and challan generated successfully',
            ];

        } catch (\Exception $e) {
            // DB::rollBack();
            return [
                'success' => false,
                'message' => 'Allocation error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Find an available bed for allocation
     * 
     * @param int $sessionId - Academic session ID
     * @param string $gender - 'male' or 'female'
     * @param string $location - 'on_campus', 'off_campus', or 'both'
     * @return object|null - Bed details or null if not found
     */
    private function findAvailableBed($sessionId, $gender, $location)
    {
        // Convert gender to hostel type (1=Male, 2=Female)
        $genderType = ($gender === 'male') ? 1 : 2;

        // Convert location to database format
        $locationCondition = '';
        if ($location !== 'both') {
            if ($location === 'on_campus') {
                $locationCondition = "AND UPPER(TRIM(ht.ht_location)) = 'ON_CAMPUS'";
            } else {
                $locationCondition = "AND UPPER(TRIM(ht.ht_location)) IN ('OFF_CAMPUS', 'OUT_OF_CAMPUS')";
            }
        }

        // Build raw SQL query.
        // Availability is determined session-wise from hostel_assign, NOT from the
        // global hb_is_occupied flag — a bed occupied in session 167 must still be
        // assignable in session 168 (different row in hostel_assign).
        $sql = "
            SELECT hb.hb_id, 
                   hb.hb_bed_number, 
                   hr.hr_room_no as hr_room_number,
                   hf.hf_name as hf_floor_name,
                   ht.ht_name,
                   ht.ht_location
            FROM hostel_beds hb
            INNER JOIN hostel_rooms hr ON hb.hb_room_id = hr.hr_id
            INNER JOIN hostel_floor hf ON hr.hr_hf_id = hf.hf_id
            INNER JOIN HOSTEL_TYPE ht ON hr.hr_ht_id = ht.ht_id
            WHERE ht.ht_hostel_type = :genderType
            AND ht.ht_status = 'E'
            AND hr.hr_status = 'E'
            AND hf.hf_status = 'E'
            AND hb.hb_id NOT IN (
                SELECT ha.ha_hb_id
                FROM hostel_assign ha
                WHERE ha.ha_session  = :sessionId
                  AND ha.ha_status   = 'E'
                  AND ha.ha_hb_id   IS NOT NULL
            )
            {$locationCondition}
            ORDER BY ht.ht_name, hf.hf_name, hr.hr_room_no, hb.hb_bed_number
            FETCH FIRST 1 ROWS ONLY
        ";


        // Prepare bindings
        $bindings = [
            'genderType' => $genderType,
            'sessionId'  => $sessionId,
        ];

        // Execute query
        $result = DB::select($sql, $bindings);

        return !empty($result) ? $result[0] : null;
    }

    /**
     * Assign bed to applicant in hostel_assign table
     * 
     * @param int $bedId - Hostel bed ID
     * @param int $assignmentId - Hostel assignment record ID
     * @return array - ['success' => bool, 'error' => string]
     */
    private function assignBedToApplicant($bedId, $assignmentId)
    {
        try {
            $sql = "UPDATE hostel_assign 
                    SET ha_hb_id = :bedId 
                    WHERE ha_id = :assignmentId";

            $updated = DB::update($sql, [
                'bedId' => $bedId,
                'assignmentId' => $assignmentId
            ]);

            if ($updated === 0) {
                return [
                    'success' => false,
                    'error' => 'No assignment record found with ID: ' . $assignmentId
                ];
            }

            return ['success' => true];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'Bed assignment failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Generate hostel fee challan using stored procedure
     * Based on updateBedStatus from reference implementation
     * 
     * @param array $applicantData - Applicant information
     * @param int $sessionId - Academic session ID
     * @param string $applicantType - 'new' or 'old'
     * @param int $bedId - Assigned bed ID
     * @return array|null - Challan details or null on failure
     */
    private function generateHostelChallan($applicantData, $sessionId, $applicantType, $bedId, $targetSessionType = null)
    {
        try {

            // dd($applicantData, $sessionId, $applicantType, $bedId, $targetSessionType);

            $visibilityType = '';
            // $targetSessionType = $targetSessionType ?: $this->resolveSessionTypeFromBedId($bedId);

            if (!$targetSessionType) {
                return ['error' => 'Unable to determine hostel session type for challan generation'];
            }

            // Get applicant details based on type
            if ($applicantType === 'new') {
                $sql = "SELECT * FROM admission_apply_info WHERE aai_id = :aai_id";
                $result = DB::select($sql, ['aai_id' => $applicantData['aai_id']]);
                $applicantDetails = !empty($result) ? $result[0] : null;

                if (!$applicantDetails) {
                    return ['error' => 'Applicant details not found (New Student)'];
                }

                $st_school_id = $applicantDetails->aai_sdpo_school_id;
                $st_dept_id = $applicantDetails->aai_sdpo_dept_id;
                $st_prog_id = $applicantDetails->aai_sdpo_prog_id;
                $aai_id = $applicantData['aai_id'];
                $api_id = $applicantData['api_id'];
                $enrollment_no = null;
                $studentType = 'New_Admission';

            } else {
                // Old student
                $sql = "SELECT sai.*, spi.* 
                        FROM students_apply_info sai
                        INNER JOIN students_personal_info spi ON sai.sai_spi_id = spi.spi_id
                        WHERE UPPER(sai.sai_enrollment_no) = UPPER(:enrollment_no)";
                $cleanEnrollment = strtoupper(trim($applicantData['enrollment_no']));
                $result = DB::select($sql, ['enrollment_no' => $cleanEnrollment]);
                $applicantDetails = !empty($result) ? $result[0] : null;

                // dd($applicantDetails);
                if (!$applicantDetails) {
                    return ['error' => 'Applicant details not found (Old Student)'];
                }

                $st_school_id = $applicantDetails->sai_sdpo_school_id;
                $st_dept_id = $applicantDetails->sai_sdpo_dept_id;
                $st_prog_id = $applicantDetails->sai_sdpo_prog_id;
                $aai_id = null;
                $api_id = null;
                $enrollment_no = strtoupper($applicantData['enrollment_no']); // Ensure Uppercase
                $studentType = 'Existing';  // New_Admission because student is not existing hostel in current session
            }

            $existingChallan = $this->findExistingHostelChallan(
                $sessionId,
                $applicantType,
                $applicantData,
                $targetSessionType
            );

            if ($existingChallan) {
                return $this->formatChallanDetails($existingChallan);
            }

            // Get academic year name
            $sql = "SELECT sess_name FROM academic_sessions WHERE sess_id = :sessionId";
            $result = DB::select($sql, ['sessionId' => $sessionId]);
            $academicYearName = !empty($result) ? $result[0]->sess_name : null;
            $username = auth()->user()->username;
            // Determine visibility type based on applicant type
            if ($applicantType === 'new') {
                $visibilityType = 'admission_applicants';
            } else {
                $visibilityType = 'regular_students';
            }

            // Get form visibility for dates with correct visibility type
            $sql = "SELECT * FROM hostel_form_visibility_periods 
                    WHERE sess_id = :sessionId 
                    AND visibility_type = :visibilityType";

            $result = DB::select($sql, [
                'sessionId' => $sessionId,
                'visibilityType' => $visibilityType
            ]);

            $hostelFormVisibility = !empty($result) ? $result[0] : null;

            if (!$hostelFormVisibility) {
                return ['error' => "Hostel form visibility/dates not found for session {$sessionId} and type {$visibilityType}"];
            }

            $dueDate = \Carbon\Carbon::parse($hostelFormVisibility->due_date)->format('Y-m-d');
            $validDate = \Carbon\Carbon::parse($hostelFormVisibility->valid_date)->format('Y-m-d');

            // Get hostel type from the assigned bed
            $sql = "SELECT ht.ht_id, ht.ht_name, ht.ht_location
                    FROM hostel_beds hb
                    INNER JOIN hostel_rooms hr ON hb.hb_room_id = hr.hr_id
                    INNER JOIN HOSTEL_TYPE ht ON hr.hr_ht_id = ht.ht_id
                    WHERE hb.hb_id = :bedId";
            $result = DB::select($sql, ['bedId' => $bedId]);
            $bedDetails = !empty($result) ? $result[0] : null;

            if (!$bedDetails) {
                return ['error' => 'Bed details not found'];
            }
            $hostelTypeId = $bedDetails->ht_id;

            // Determine hostel type string (Regular, Regular_outside, etc.)
            // $hostel_type = $this->getHostelTypeById($hostelTypeId);

            $hostel_type = $targetSessionType;

            // dd($hostel_type);
            if (!$hostel_type) {
                return ['error' => 'Invalid Hostel Type derived from Bed'];
            }



            // dd($studentType, $sessionId, $hostel_type);
            // Get fee structure for this specific hostel type
            $sql = "SELECT * FROM fin_hostel_fee_structure 
                    WHERE fhfs_session = :sessionId 
                    AND UPPER(TRIM(fhfs_session_type)) = :hostelType 
                    and fhfs_student_type = :studentType
                    AND fhfs_status = 'E'";
            $result = DB::select($sql, [
                'sessionId' => $sessionId,
                'hostelType' => strtoupper(trim($hostel_type)),
                'studentType' => $studentType
            ]);

            $feeStructure = !empty($result) ? $result[0] : null;

            // dd($feeStructure);

            if (!$feeStructure) {
                return ['error' => "Fee structure not found for {$hostel_type} in this session"];
            }

            $securityFee = $feeStructure->fhfs_hostel_security_fee ?? 0;
            $pStudentType = $this->buildNewAdmissionStudentType($academicYearName);

            // Day-wise vs full semester: non-zero days → Hostel_day_wise
            $noOfDays = (int) ($applicantData['no_of_days'] ?? 0);
            if ($noOfDays < 0) {
                $noOfDays = 0;
            }
            $ffsStudentType = $noOfDays > 0 ? 'Hostel_day_wise' : 'Hostel_Full_semster';

            // Prepare procedure parameters (exact same as reference)
            $bindings = [
                'p_schol_id' => $st_school_id,
                'p_dept_id' => $st_dept_id,
                'p_prog_id' => $st_prog_id,
                'p_session' => $sessionId,
                'p_due_date' => $dueDate,
                'p_valid_date' => $validDate,
                'p_ffs_student_type' => $ffsStudentType,
                'p_hostel_security' => $securityFee,
                //remove this line
                //  'p_studentType' => $studentType,
                'p_hostel_studentType' =>  $applicantType === 'new' ? 'New_Admission' : 'Existing',
                'p_no_of_days' => $noOfDays,
                'refrigeratorCheckBox' => 'No',
                'conditionerCheckBox' => 'No',
                'electricHeaterCheckBox' => 'No',
                'p_remarks' => 'Auto-generated by system for ' . $hostel_type,
                'p_AAI_ID' => ($applicantType === 'new') ? $aai_id : $enrollment_no,
                'session_type' => $hostel_type,
                'p_entry_username' => $username,
                'p_studentType' =>  $applicantType === 'new' ? $pStudentType : 'Existing',
            ];

            // dd($bindings);

            // Call stored procedure to generate challan
            $procedureName = 'fin_new_std_hostel_chln_gen';
            $result = DB::executeProcedure($procedureName, $bindings);

            if (!$result) {
                return ['error' => 'Challan generation procedure returned failure'];
            }

            // Get generated challan ID (same logic as reference)
            if ($applicantType === 'new') {
                $sql = "SELECT * FROM student_challan 
                        WHERE sc_app_id = :aai_id 
                        AND sc_session_id = :sessionId 
                        AND TRIM(sc_challan_type) IN ('Hostel_Fee', 'Hostel Fee')
                        ORDER BY id DESC 
                        FETCH FIRST 1 ROWS ONLY";
                $result = DB::select($sql, ['aai_id' => $aai_id, 'sessionId' => $sessionId]);
                $check_generated_challan = !empty($result) ? $result[0] : null;

                // Try without session check if not found
                if (!$check_generated_challan) {
                    $sql = "SELECT * FROM student_challan 
                            WHERE sc_app_id = :aai_id 
                            AND TRIM(sc_challan_type) IN ('Hostel_Fee', 'Hostel Fee')
                            ORDER BY id DESC 
                            FETCH FIRST 1 ROWS ONLY";
                    $result = DB::select($sql, ['aai_id' => $aai_id]);
                    $check_generated_challan = !empty($result) ? $result[0] : null;
                }
            } else {
                $sql = "SELECT * FROM student_challan 
                        WHERE sc_enrollment_no = :enrollment_no 
                        AND sc_session_id = :sessionId 
                        AND TRIM(sc_challan_type) IN ('Hostel_Fee', 'Hostel Fee')
                        ORDER BY id DESC 
                        FETCH FIRST 1 ROWS ONLY";
                $result = DB::select($sql, ['enrollment_no' => $enrollment_no, 'sessionId' => $sessionId]);
                $check_generated_challan = !empty($result) ? $result[0] : null;

                // Try without session check if not found
                if (!$check_generated_challan) {
                    $sql = "SELECT * FROM student_challan 
                            WHERE sc_enrollment_no = :enrollment_no 
                            AND TRIM(sc_challan_type) IN ('Hostel_Fee', 'Hostel Fee')
                            ORDER BY id DESC 
                            FETCH FIRST 1 ROWS ONLY";
                    $result = DB::select($sql, ['enrollment_no' => $enrollment_no]);
                    $check_generated_challan = !empty($result) ? $result[0] : null;
                }
            }

            if (!$check_generated_challan) {
                return ['error' => 'Challan generated but not found in student_challan table'];
            }


            $lastInsertedChallanId = $check_generated_challan->id;

            // Sync bed flag via shared service so it reflects ALL active sessions.
            \App\Services\Hostel\HostelSeatStatsService::syncBedPhysicalStatus((int) $bedId);

            return [
                'id' => $lastInsertedChallanId,
                'challan_number' => $check_generated_challan->sc_challan_number ?? 'N/A',
                'amount' => $check_generated_challan->invoiceamount ?? 0,
                'due_date' => $dueDate,
                'status' => $check_generated_challan->sc_challan_status ?? 'UNPAID'
            ];

        } catch (\Exception $e) {
            return ['error' => 'Exception in Challan Gen: ' . $e->getMessage()];
        }
    }

    private function buildNewAdmissionStudentType(?string $sessionName): string
    {
        if (!$sessionName || trim($sessionName) === '') {
            return 'New Admission SPRING';
        }

        $season = strtoupper(strtok(trim($sessionName), ' '));

        return 'New Admission ' . $season;
    }

    /**
     * Helper function to map hostel ID to its type string (from reference)
     */
    private function getHostelTypeById($hostelTypeId)
    {
        if ($hostelTypeId == 1 || $hostelTypeId == 2) {
            return 'Regular';
        } elseif ($hostelTypeId == 3 || $hostelTypeId == 4) {
            return 'Regular_outside';
        } elseif ($hostelTypeId == 5 || $hostelTypeId == 6) {
            return 'Regular_outside_bedian';
        }

        return null;
    }


    /**
     * Link generated challan to hostel assignment
     * 
     * @param int $assignmentId - Hostel assignment record ID
     * @param int $challanId - Student challan ID
     * @return array - ['success' => bool, 'error' => string]
     */
    private function linkChallanToAssignment($assignmentId, $challanId)
    {
        try {
            $sql = "UPDATE hostel_assign 
                    SET ha_challan_id = :challanId 
                    WHERE ha_id = :assignmentId";

            DB::update($sql, [
                'challanId' => $challanId,
                'assignmentId' => $assignmentId
            ]);

            return ['success' => true];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'Challan linking failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Cancel unpaid hostel challans that do not match the target session type.
     */
    public function cancelMismatchedUnpaidHostelChallans($sessionId, $applicantType, array $applicantData, $targetSessionType)
    {
        $conflictingChallans = $this->findMismatchedUnpaidHostelChallans(
            $sessionId,
            $applicantType,
            $applicantData,
            $targetSessionType
        );

        if ($conflictingChallans->isEmpty()) {
            return ['success' => true, 'cancelled' => false];
        }

        $targetLabel = $this->normalizeSessionType($targetSessionType) ?? 'unknown';
        $cancelled = false;

        foreach ($conflictingChallans as $conflictingChallan) {
            $cancelResult = $this->cancelHostelChallanRecord(
                $conflictingChallan,
                $applicantType,
                $applicantData,
                'Auto Cancel Hostel Challan on hostel session type change to' . $targetLabel
            );

            if (!$cancelResult['success']) {
                return $cancelResult;
            }

            $cancelled = true;
        }

        return ['success' => true, 'cancelled' => $cancelled];
    }

    /**
     * Find unpaid Hostel_Fee challans that do not match the target session type.
     */
    private function findMismatchedUnpaidHostelChallans($sessionId, $applicantType, array $applicantData, $targetSessionType)
    {
        $query = DB::table('student_challan')
            ->where('sc_session_id', $sessionId)
            ->whereRaw("TRIM(sc_challan_type) IN ('Hostel_Fee', 'Hostel Fee')")
            ->whereRaw("UPPER(NVL(sc_challan_status, 'UNPAID')) = 'UNPAID'");

        if (!$this->applyApplicantIdentityToChallanQuery($query, $applicantType, $applicantData)) {
            return collect();
        }

        return $query
            ->orderByDesc('id')
            ->get()
            ->filter(function ($challan) use ($targetSessionType) {
                return !$this->sessionTypesMatch($challan->sc_session_type ?? null, $targetSessionType);
            })
            ->values();
    }

    /**
     * Find an existing Hostel_Fee challan for this student/session and session type (PAID or UNPAID).
     */
    public function findExistingHostelChallan($sessionId, $applicantType, array $applicantData, $targetSessionType)
    {
        $query = DB::table('student_challan')
            ->where('sc_session_id', $sessionId)
            ->whereRaw("TRIM(sc_challan_type) IN ('Hostel_Fee', 'Hostel Fee')")
            ->where(function ($q) {
                $q->whereRaw("UPPER(NVL(sc_challan_status, 'UNPAID')) IN ('PAID', 'UNPAID')");
            });

        if (!$this->applyApplicantIdentityToChallanQuery($query, $applicantType, $applicantData)) {
            return null;
        }

        return $query
            ->orderByDesc('id')
            ->get()
            ->first(function ($challan) use ($targetSessionType) {
                return $this->sessionTypesMatch($challan->sc_session_type ?? null, $targetSessionType);
            });
    }

    private function resolveSessionTypeFromBedId($bedId): ?string
    {
        $result = DB::select(
            "SELECT ht.ht_id
             FROM hostel_beds hb
             INNER JOIN hostel_rooms hr ON hb.hb_room_id = hr.hr_id
             INNER JOIN HOSTEL_TYPE ht ON hr.hr_ht_id = ht.ht_id
             WHERE hb.hb_id = :bedId",
            ['bedId' => $bedId]
        );

        if (empty($result)) {
            return null;
        }

        return $this->getHostelTypeById($result[0]->ht_id);
    }

    private function normalizeSessionType(?string $sessionType): ?string
    {
        if ($sessionType === null) {
            return null;
        }

        $normalized = strtolower(str_replace('_', '-', trim($sessionType)));

        return $normalized === '' ? null : $normalized;
    }

    private function sessionTypesMatch(?string $existingSessionType, ?string $targetSessionType): bool
    {
        $existing = $this->normalizeSessionType($existingSessionType);
        $target = $this->normalizeSessionType($targetSessionType);

        if ($target === null) {
            return $existing === null;
        }

        if ($existing === $target) {
            return true;
        }

        // Legacy challans without session type are treated as on-campus regular.
        if ($target === 'regular' && $existing === null) {
            return true;
        }

        return false;
    }

    private function applyApplicantIdentityToChallanQuery($query, $applicantType, array $applicantData): bool
    {
        if ($applicantType === 'new') {
            $aaiId = $applicantData['aai_id'] ?? null;
            $enrollment = $applicantData['enrollment_no'] ?? null;

            if (!$enrollment && $aaiId) {
                $enrollment = DB::table('admission_apply_info')
                    ->where('aai_id', $aaiId)
                    ->value('cms_sai_enrollment_no');
            }

            if (!$aaiId && !$enrollment) {
                return false;
            }

            $query->where(function ($q) use ($aaiId, $enrollment) {
                if ($aaiId) {
                    $q->where('sc_app_id', $aaiId);
                }
                if ($enrollment) {
                    $method = $aaiId ? 'orWhere' : 'where';
                    $q->{$method}(DB::raw('UPPER(sc_enrollment_no)'), strtoupper(trim($enrollment)));
                }
            });

            return true;
        }

        $enrollment = strtoupper(trim($applicantData['enrollment_no'] ?? ''));
        if ($enrollment === '') {
            return false;
        }

        $query->where(DB::raw('UPPER(sc_enrollment_no)'), $enrollment);

        return true;
    }

    private function cancelHostelChallanRecord($challan, $applicantType, array $applicantData, string $reason)
    {
        try {
            $enrollmentNo = $this->resolveChallanEnrollmentForCancel($challan, $applicantType, $applicantData);

            if ($enrollmentNo === null || $enrollmentNo === '') {
                return [
                    'success' => false,
                    'error' => 'Unable to resolve enrollment for challan cancellation.',
                ];
            }

            $challanRequest = new Request([
                'id' => $challan->id,
                'cancell_type' => 'CANCELLED',
                'remarks' => $reason,
                'enrollment_no' => is_numeric($enrollmentNo) ? $enrollmentNo : strtoupper((string) $enrollmentNo),
            ]);

            $cancelChallans = new GenerateSingleChallanController();
            $response = $cancelChallans->cancelChallans($challanRequest);
            $responseData = method_exists($response, 'getData') ? $response->getData(true) : [];

            if (empty($responseData['success'])) {
                return [
                    'success' => false,
                    'error' => $responseData['message'] ?? 'Failed to cancel existing hostel challan.',
                ];
            }

            return ['success' => true, 'cancelled' => true];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'Challan cancellation failed: ' . $e->getMessage(),
            ];
        }
    }

    private function resolveChallanEnrollmentForCancel($challan, $applicantType, array $applicantData)
    {
        if ($applicantType === 'new') {
            return $applicantData['aai_id'] ?? $challan->sc_app_id ?? null;
        }

        return strtoupper(trim($applicantData['enrollment_no'] ?? $challan->sc_enrollment_no ?? ''));
    }

    /**
     * @param object $row
     */
    private function formatChallanDetails($row, $dueDate = null)
    {
        return [
            'id' => $row->id,
            'challan_number' => $row->sc_challan_number ?? 'N/A',
            'amount' => $row->sc_amount ?? 0,
            'due_date' => $dueDate ?? ($row->sc_due_date ?? null),
            'status' => $row->sc_challan_status ?? 'UNPAID',
            'existing' => true,
        ];
    }
}
