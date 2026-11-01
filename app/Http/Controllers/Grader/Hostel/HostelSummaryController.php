<?php

namespace App\Http\Controllers\Grader\Hostel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Exception;
use App\Http\Controllers\StudentFinancial\GenerateSingleChallanController;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class HostelSummaryController extends Controller
{
    private const ON_CAMPUS_SESSION_TYPES = ['regular', 'non-regular'];

    private const OFF_CAMPUS_SESSION_TYPES = [
        'regular_outside',
        'non-regular_outside',
        'regular_outside_bedian',
        'non-regular_outside_bedian',
    ];

    /**
     * Show the main hostel summary page
     */
    public function index()
    {
        // Simple logic: Load sessions starting from ID 166
        $sessions = DB::table('academic_sessions')
            ->select('sess_id', 'sess_name')
            ->where('sess_id', '>=', 166)
            ->orderBy('sess_id', 'desc')
            ->get();

        $hostelTypes = DB::table('hostel_type')
            ->where('ht_status', 'E')
            ->orderBy('ht_name')
            ->get(['ht_id', 'ht_name', 'ht_location', 'ht_hostel_type']);

        $hostelLocations = $hostelTypes
            ->pluck('ht_location')
            ->filter(fn ($location) => filled($location))
            ->unique()
            ->sort()
            ->values();

        return view('Hostel.Summary.hostel_summary', [
            'sessions' => $sessions,
            'hostelTypes' => $hostelTypes,
            'hostelLocations' => $hostelLocations,
        ]);
    }

    /**
     * Get hostel summary data with filters
     */
    public function getSummary(Request $request)
    {
        try {
            $sessionId = $request->input('session_id');
            $hostelTypeFilter = $request->input('hostel_type', 'all'); // 'all', 'on_campus', 'off_campus'
            $statusFilter = $request->input('status', 'all'); // 'all', 'reserved', 'paid', 'available'

            if (!$sessionId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Session ID is required'
                ], 400);
            }

            // Get all hostels
            $hostelTypes = DB::table('hostel_type')
                ->where('ht_status', 'E')
                ->get();

            $summaryData = [];
            $totalSeats = 0;
            $totalOccupied = 0;
            $totalVacant = 0;
            $totalPaid = 0;
            $totalReserved = 0;

            foreach ($hostelTypes as $hostel) {
                // Apply hostel type filter - use exact database values (on_campus, off_campus)
                if ($hostelTypeFilter != 'all') {
                    $hostelLocation = $hostel->ht_location ?? '';
                    if ($hostelTypeFilter == 'on_campus' && $hostelLocation != 'on_campus') {
                        continue;
                    }
                    if ($hostelTypeFilter == 'off_campus' && $hostelLocation != 'off_campus') {
                        continue;
                    }
                }

                // Get total capacity (total beds)
                $totalBeds = DB::table('hostel_beds')
                    ->join('hostel_rooms', 'hostel_beds.hb_room_id', '=', 'hostel_rooms.hr_id')
                    ->join('hostel_floor', 'hostel_rooms.hr_hf_id', '=', 'hostel_floor.hf_id')
                    ->where('hostel_floor.hf_new_hostel_type', $hostel->ht_id)
                    ->where('hostel_rooms.hr_status', 'E')
                    ->where('hostel_floor.hf_status', 'E')
                    ->count();

                // Get paid students count
                $paidCount = DB::table('hostel_assign as ha')
                    ->join('student_challan as sc', 'ha.ha_challan_id', '=', 'sc.id')
                    ->join('hostel_beds as hb', 'ha.ha_hb_id', '=', 'hb.hb_id')
                    ->join('hostel_rooms as hr', 'hb.hb_room_id', '=', 'hr.hr_id')
                    ->where('ha.ha_session', $sessionId)
                    ->where('sc.sc_challan_status', 'PAID')
                    ->where('sc.sc_challan_type', 'Hostel_Fee')
                    ->where('hr.hr_ht_id', $hostel->ht_id)
                    ->count(DB::raw('DISTINCT ha.ha_id'));

                // Get reserved (unpaid) students count
                $reservedCount = DB::table('hostel_assign as ha')
                    ->leftJoin('student_challan as sc', 'ha.ha_challan_id', '=', 'sc.id')
                    ->join('hostel_beds as hb', 'ha.ha_hb_id', '=', 'hb.hb_id')
                    ->join('hostel_rooms as hr', 'hb.hb_room_id', '=', 'hr.hr_id')
                    ->where('ha.ha_session', $sessionId)
                    ->where(function ($q) {
                        $q->where('sc.sc_challan_status', '!=', 'PAID')
                            ->orWhereNull('sc.sc_challan_status');
                    })
                    ->where('hr.hr_ht_id', $hostel->ht_id)
                    ->count(DB::raw('DISTINCT ha.ha_id'));

                $occupied = $paidCount + $reservedCount;
                $vacant = $total = DB::table('hostel_beds as hb')
                            ->join('hostel_rooms as hr', 'hb.hb_room_id', '=', 'hr.hr_id')
                            ->join('hostel_floor as hf', 'hr.hr_hf_id', '=', 'hf.hf_id')
                            ->where('hr.hr_status', 'E')
                            ->where('hf.hf_status', 'E')
                            ->where('hf.hf_new_hostel_type', $hostel->ht_id)
                            ->where('hb.HB_IS_OCCUPIED', 'N')
                            ->count();


                // Apply status filter
                if ($statusFilter != 'all') {
                    if ($statusFilter == 'paid' && $paidCount == 0) {
                        continue;
                    }
                    if ($statusFilter == 'reserved' && $reservedCount == 0) {
                        continue;
                    }
                    if ($statusFilter == 'available' && $vacant == 0) {
                        continue;
                    }
                }

                // Get student details for this hostel
                $students = $this->getHostelStudents($sessionId, $hostel->ht_id);

                $summaryData[] = [
                    'hostel_id' => $hostel->ht_id,
                    'hostel_name' => $hostel->ht_name,
                    'hostel_location' => $hostel->ht_location ?? '',
                    'total_seats' => $totalBeds,
                    'paid' => $paidCount,
                    'reserved' => $reservedCount,
                    'occupied' => $occupied,
                    'vacant' => $vacant,
                    'students' => $students
                ];

                $totalSeats += $totalBeds;
                $totalOccupied += $occupied;
                $totalVacant += $vacant;
                $totalPaid += $paidCount;
                $totalReserved += $reservedCount;
            }

            return response()->json([
                'success' => true,
                'summary' => $summaryData,
                'totals' => [
                    'total_seats' => $totalSeats,
                    'total_occupied' => $totalOccupied,
                    'total_vacant' => $totalVacant,
                    'total_paid' => $totalPaid,
                    'total_reserved' => $totalReserved
                ]
            ]);
        } catch (Exception $e) {
            // Log the error for debugging

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while loading summary. Please try again.',
                'error' => $e->getMessage(), // Include actual error message
                'error_line' => $e->getLine(), // Include line number
                'error_file' => basename($e->getFile()) // Include file name
            ], 500);
        }
    }

    /**
     * Get student details for a specific hostel
     */
    private function getHostelStudents($sessionId, $hostelId)
    {
        $students = DB::table('hostel_assign as ha')
            ->leftJoin('student_challan as sc', 'ha.ha_challan_id', '=', 'sc.id')
            ->leftJoin('hostel_beds as hb', 'ha.ha_hb_id', '=', 'hb.hb_id')
            ->leftJoin('hostel_rooms as hr', 'hb.hb_room_id', '=', 'hr.hr_id')
            ->leftJoin('hostel_floor as hf', 'hr.hr_hf_id', '=', 'hf.hf_id')
            ->where('ha.ha_session', $sessionId)
            ->where('hr.hr_ht_id', $hostelId)
            ->select(
                'ha.ha_enrolment_no',
                'ha.ha_aai_id',
                'sc.sc_challan_status',
                'sc.id as challan_id',
                'hf.hf_name as floor_name',
                'hr.hr_room_no as room_no',
                'hb.hb_bed_number as bed_no',
                DB::raw("COALESCE(
                    (SELECT api_name FROM admission_personal_info WHERE api_id = ha.ha_api_id),
                    (SELECT spi_name FROM students_personal_info spi
                     JOIN students_apply_info sai ON spi.spi_id = sai.sai_spi_id
                     WHERE UPPER(sai.sai_enrollment_no) = UPPER(ha.ha_enrolment_no) OR UPPER(sai.sai_enrollment_no) = UPPER(TO_CHAR(ha.ha_aai_id)))
                ) as student_name"),
                DB::raw("COALESCE(
                    (SELECT api_email FROM admission_personal_info WHERE api_id = ha.ha_api_id),
                    (SELECT spi_email FROM students_personal_info spi
                     JOIN students_apply_info sai ON spi.spi_id = sai.sai_spi_id
                     WHERE UPPER(sai.sai_enrollment_no) = UPPER(ha.ha_enrolment_no) OR UPPER(sai.sai_enrollment_no) = UPPER(TO_CHAR(ha.ha_aai_id)))
                ) as student_email")
            )
            ->get();

        return $students;
    }

    /**
     * Get preview data before vacate (for confirmation modal)
     */
    public function getVacatePreview(Request $request)
    {
        try {
            $sessionId = $request->input('session_id');
            $hostelId = $request->input('hostel_id'); // null for all hostels

            if (!$sessionId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Session ID is required'
                ], 400);
            }



            // Get all students who will be vacated (non-paid) using common query method
            $query = $this->getNonPaidStudentsQuery($sessionId, $hostelId)
                ->leftJoin('hostel_floor as hf', 'hr.hr_hf_id', '=', 'hf.hf_id');

            $studentsToVacate = $query->select(
                'ha.ha_enrolment_no',
                'ha.ha_aai_id',
                'ht.ht_name as hostel_name',
                'hf.hf_name as floor_name',
                'hr.hr_room_no as room_no',
                'hb.hb_bed_number as bed_no',
                DB::raw("COALESCE(
                    (SELECT api_name FROM admission_personal_info WHERE api_id = ha.ha_api_id),
                    (SELECT spi_name FROM students_personal_info spi
                     JOIN students_apply_info sai ON spi.spi_id = sai.sai_spi_id
                     WHERE UPPER(sai.sai_enrollment_no) = UPPER(ha.ha_enrolment_no) OR UPPER(sai.sai_enrollment_no) = UPPER(TO_CHAR(ha.ha_aai_id)))
                ) as student_name")
            )
                ->get();

            // Get paid students count (who will be kept)
            $paidQuery = DB::table('hostel_assign as ha')
                ->join('student_challan as sc', 'ha.ha_challan_id', '=', 'sc.id')
                ->join('hostel_beds as hb', 'ha.ha_hb_id', '=', 'hb.hb_id')
                ->join('hostel_rooms as hr', 'hb.hb_room_id', '=', 'hr.hr_id')
                ->where('ha.ha_session', $sessionId)
                ->where('sc.sc_challan_status', 'PAID')
                ->where('sc.sc_challan_type', 'Hostel_Fee');

            if ($hostelId) {
                $paidQuery->where('hr.hr_ht_id', $hostelId);
            }

            $paidCount = $paidQuery->count(DB::raw('DISTINCT ha.ha_id'));

            return response()->json([
                'success' => true,
                'students_to_vacate' => $studentsToVacate,
                'total_to_vacate' => count($studentsToVacate),
                'paid_kept' => $paidCount
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get query for non-paid students to vacate (common method)
     */
    private function getNonPaidStudentsQuery($sessionId, $hostelId = null)
    {
        $query = DB::table('hostel_assign as ha')
            ->leftJoin('student_challan as sc', 'ha.ha_challan_id', '=', 'sc.id')
            ->leftJoin('hostel_beds as hb', 'ha.ha_hb_id', '=', 'hb.hb_id')
            ->leftJoin('hostel_rooms as hr', 'hb.hb_room_id', '=', 'hr.hr_id')
            ->leftJoin('hostel_type as ht', 'hr.hr_ht_id', '=', 'ht.ht_id')
            ->where('ha.ha_session', $sessionId)
            ->whereNotNull('ha.ha_hb_id')
            ->where(function ($q) {
                $q->where('sc.sc_challan_status', '!=', 'PAID')
                    ->orWhereNull('sc.sc_challan_status');
            });

        if ($hostelId) {
            $query->where('hr.hr_ht_id', $hostelId);
        }

        return $query;
    }

    /**
     * Vacate hostel seats (except paid students)
     */
    public function vacateHostel(Request $request)
    {
        try {
            $sessionId = $request->input('session_id');
            $hostelId = $request->input('hostel_id');
            $remarks = $request->input('remarks', '');

            // Validation (ID is REQUIRED now to prevent mass delete)
            if (!$sessionId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Session ID is required'
                ], 400);
            }

            if (empty($hostelId)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Hostel selection is required. Cannot vacate all hostels at once.'
                ], 400);
            }

            // Validate hostel ID if provided
            if ($hostelId && !is_numeric($hostelId)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid Hostel ID'
                ], 400);
            }

            // Validate hostel exists if provided
            if ($hostelId) {
                $hostelExists = DB::table('hostel_type')
                    ->where('ht_id', $hostelId)
                    ->where('ht_status', 'E')
                    ->exists();

                if (!$hostelExists) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Hostel not found or inactive'
                    ], 404);
                }
            }

            DB::beginTransaction();

            // Get all non-paid students to vacate using common query method
            $query = $this->getNonPaidStudentsQuery($sessionId, $hostelId);

            $studentsToVacate = $query->select(
                'ha.ha_id',
                'ha.ha_enrolment_no',
                'ha.ha_aai_id',
                'ha.ha_hb_id',
                'hb.hb_room_id as hb_room_id',
                'hr.hr_id as hr_id',
                'ht.ht_id as hostel_id',
                'ht.ht_name as hostel_name'
            )
                ->get();

            // Count before processing
            $totalToVacate = count($studentsToVacate);
            $totalVacated = 0;
            $paidKept = 0;

            // Debug: Verify query results
            if ($totalToVacate == 0) {
                DB::commit();
                return response()->json([
                    'success' => true,
                    'message' => 'No students found to vacate',
                    'total_vacated' => 0,
                    'paid_kept' => $paidKept
                ]);
            }

            // Get paid count
            $paidQuery = DB::table('hostel_assign as ha')
                ->join('student_challan as sc', 'ha.ha_challan_id', '=', 'sc.id')
                ->join('hostel_beds as hb', 'ha.ha_hb_id', '=', 'hb.hb_id')
                ->join('hostel_rooms as hr', 'hb.hb_room_id', '=', 'hr.hr_id')
                ->where('ha.ha_session', $sessionId)
                ->where('sc.sc_challan_status', 'PAID')
                ->where('sc.sc_challan_type', 'Hostel_Fee');

            if ($hostelId) {
                $paidQuery->where('hr.hr_ht_id', $hostelId);
            }

            $paidKept = $paidQuery->count(DB::raw('DISTINCT ha.ha_id'));

            // Vacate each student - same logic as vacateBedStatus
            $username = strtolower(auth()->user()->employee_id ?? auth()->user()->username);
            $studentsList = []; // Array to collect student IDs

            foreach ($studentsToVacate as $student) {
                // Null checks - skip if no bed assigned
                if (empty($student->ha_id) || empty($student->ha_hb_id)) {
                    continue;
                }

                // Count student before processing (they have bed assigned from query)
                $totalVacated++;

                // Collect student ID for log
                $studentId = null;
                if (!empty($student->ha_enrolment_no)) {
                    $studentId = $student->ha_enrolment_no;
                } elseif (!empty($student->ha_aai_id)) {
                    $studentId = $student->ha_aai_id;
                }
                if ($studentId) {
                    $studentsList[] = $studentId;
                }

                try {
                    // Cancel challan if exists and is UNPAID
                    if (!empty($student->ha_enrolment_no) || !empty($student->ha_aai_id)) {
                        $enrollmentNo = $student->ha_enrolment_no ?: $student->ha_aai_id;
                        $this->cancelHostelChallan($enrollmentNo, $sessionId);
                    }

                    // Get room_id from bed before updating
                    $roomId = null;
                    $bed = DB::table('hostel_beds')
                        ->where('hb_id', $student->ha_hb_id)
                        ->first();
                    if ($bed && !empty($bed->hb_room_id)) {
                        $roomId = $bed->hb_room_id;
                    }

                    // Update hostel_assign - set ha_hb_id to null
                    DB::table('hostel_assign')
                        ->where('ha_id', $student->ha_id)
                        ->whereNotNull('ha_hb_id')
                        ->update([
                            'ha_hb_id' => null,
                            'ha_entry_user_id' => $username
                        ]);

                    // Update bed status to 'N' (not occupied)
                    DB::table('hostel_beds')
                        ->where('hb_id', $student->ha_hb_id)
                        ->update(['hb_is_occupied' => 'N']);

                    // Update room occupied beds count
                    if (!empty($roomId)) {
                        DB::table('hostel_rooms')
                            ->where('hr_id', $roomId)
                            ->where('hr_occupied_beds', '>', 0)
                            ->decrement('hr_occupied_beds', 1);
                    }
                } catch (Exception $e) {
                    // Continue with next student on error (count already incremented)
                    continue;
                }
            }

            // Save log (don't let log failure rollback the transaction)
            try {
                $hostelName = 'All Hostels';
                if ($hostelId) {
                    $hostelName = DB::table('hostel_type')
                        ->where('ht_id', $hostelId)
                        ->value('ht_name');
                    if (empty($hostelName)) {
                        $hostelName = 'Unknown Hostel';
                    }
                }

                // Convert students list array to comma-separated string
                $studentsListString = !empty($studentsList) ? implode(',', $studentsList) : null;

                DB::table('hostel_vacate_logs')->insert([
                    'hvl_session_id' => $sessionId,
                    'hvl_hostel_id' => $hostelId,
                    'hvl_hostel_name' => $hostelName,
                    'hvl_vacated_by' => auth()->user()->username ?? 'system',
                    'hvl_vacated_at' => Carbon::now(),
                    'hvl_total_vacated' => $totalVacated,
                    'hvl_paid_kept' => $paidKept,
                    'hvl_students_list' => $studentsListString,
                    'hvl_remarks' => $remarks,
                    'hvl_created_at' => Carbon::now(),
                    'hvl_updated_at' => Carbon::now()
                ]);
            } catch (Exception $logError) {
                // Don't fail the transaction if log fails
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Hostel vacated successfully',
                'total_vacated' => $totalVacated,
                'paid_kept' => $paidKept
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            // Log the error for debugging
            Log::error('Error in vacateHostel: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while vacating hostel. Please try again or contact administrator.',
                'error' => $e->getMessage(),
                'error_line' => $e->getLine(),
                'error_file' => basename($e->getFile())
            ], 500);
        }
    }

    /**
     * Cancel hostel challan for a student
     * Returns true on success, false on failure, null if no challan found
     */
    private function cancelHostelChallan($enrollmentNo, $sessionId)
    {
        try {
            if (empty($enrollmentNo) || empty($sessionId)) {
                return false;
            }

            $query = DB::table('student_challan')
                ->where('sc_session_id', $sessionId)
                ->where('sc_challan_type', 'Hostel_Fee')
                ->where('check_fee_installments', 0)
                ->where('sc_challan_status', 'UNPAID');

            if (is_numeric($enrollmentNo)) {
                $query->where('sc_app_id', $enrollmentNo);
            } else {
                $query->where('sc_enrollment_no', $enrollmentNo);
            }

            $hostelChallan = $query->first();

            if (empty($hostelChallan) || empty($hostelChallan->id)) {
                // No challan found - this is OK, return null
                return null;
            }

            $data = [
                'id' => $hostelChallan->id,
                'cancell_type' => 'CANCELLED',
                'remarks' => 'Auto Cancel Hostel Challan on Hostel Vacate by ' . (auth()->user()->username ?? 'system'),
                'enrollment_no' => strtoupper($enrollmentNo)
            ];

            $challanRequest = new Request($data);

            $cancelChallans = new GenerateSingleChallanController();
            $cancelChallans->cancelChallans($challanRequest);

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Get vacate logs
     */
    public function getLogs(Request $request)
    {
        try {
            $dateFrom = $request->input('date_from');
            $dateTo = $request->input('date_to');

            $query = DB::table('hostel_vacate_logs')
                ->orderBy('hvl_vacated_at', 'desc');

            if ($dateFrom) {
                $query->whereDate('hvl_vacated_at', '>=', $dateFrom);
            }

            if ($dateTo) {
                $query->whereDate('hvl_vacated_at', '<=', $dateTo);
            }

            $logs = $query->get();

            return response()->json([
                'success' => true,
                'logs' => $logs
            ]);
        } catch (Exception $e) {
            // Log the error for debugging
            Log::error('Error in getLogs: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while loading logs. Please try again.',
                'error' => $e->getMessage(),
                'error_line' => $e->getLine(),
                'error_file' => basename($e->getFile())
            ], 500);
        }
    }

    /**
     * Export summary to Excel
     */
    public function exportExcel(Request $request)
    {
        // This will be implemented with Excel export library
        // For now, return JSON
        return $this->getSummary($request);
    }

    /**
     * Export summary to PDF
     */
    public function exportPdf(Request $request)
    {
        // This will be implemented with PDF export library
        // For now, return JSON
        return $this->getSummary($request);
    }
    /**
     * Funtion: Get Bulk Vacate Analysis
     * Purpose: This function calculates how many students need to be vacated from hostels.
     *
     * Logic:
     * 1. It checks the Total Beds in each hostel.
     * 2. It checks how many beds are currently Occupied.
     * 3. It identifies "Safe Students" (Students who have PAID fee for the selected session).
     * 4. It calculates "To Vacate" count = Occupied Beds - Safe Students.
     *
     * This helps the user decide which hostels need action.
     */
    public function getBulkVacateAnalysis(Request $request)
    {
        try {
            // Step 1: Get Session ID from request
            $sessionId = $request->input('session_id');

            if (!$sessionId) {
                return response()->json(['success' => false, 'message' => 'Session ID is required'], 400);
            }

            // Step 2: Get all Active Hostels
            $hostels = DB::table('hostel_type')->where('ht_status', 'E')->get();
            $analysis = [];

            foreach ($hostels as $hostel) {
                // 1. Total Beds (Raw SQL)
                $totalBeds = DB::selectOne("
                    SELECT COUNT(*) as total
                    FROM hostel_beds hb
                    JOIN hostel_rooms hr ON hb.hb_room_id = hr.hr_id
                    JOIN hostel_floor hf ON hr.hr_hf_id = hf.hf_id
                    WHERE hf.hf_new_hostel_type = :hostel_id
                    AND hr.hr_status = 'E'
                    AND hf.hf_status = 'E'
                ", ['hostel_id' => $hostel->ht_id])->total;

                // 2. Currently Occupied Beds (Raw SQL)
                $occupiedBeds = DB::selectOne("
                    SELECT COUNT(*) as occupied
                    FROM hostel_beds hb
                    JOIN hostel_rooms hr ON hb.hb_room_id = hr.hr_id
                    JOIN hostel_floor hf ON hr.hr_hf_id = hf.hf_id
                    WHERE hf.hf_new_hostel_type = :hostel_id
                    AND hb.hb_is_occupied = 'Y'
                    AND hr.hr_status = 'E'
                    AND hf.hf_status = 'E'
                ", ['hostel_id' => $hostel->ht_id])->occupied;

                // 3. Free Beds (Raw SQL) - Explicitly from DB
                $freeBeds = DB::selectOne("
                    SELECT COUNT(*) as free
                    FROM hostel_beds hb
                    JOIN hostel_rooms hr ON hb.hb_room_id = hr.hr_id
                    JOIN hostel_floor hf ON hr.hr_hf_id = hf.hf_id
                    WHERE hf.hf_new_hostel_type = :hostel_id
                    AND hb.hb_is_occupied = 'N'
                    AND hr.hr_status = 'E'
                    AND hf.hf_status = 'E'
                ", ['hostel_id' => $hostel->ht_id])->free;

                // 4. Paid Students (Safe List) (Raw SQL)
                // Note: Using student names subquery logic in select
                $safeStudents = DB::select("
                    SELECT
                        ha.ha_enrolment_no,
                        ha.ha_aai_id,
                        hr.hr_room_no,
                        hb.hb_bed_number,
                        hf.hf_name as floor_name,
                        ht.ht_name as hostel_name,
                        COALESCE(
                            (SELECT api_name FROM admission_personal_info WHERE api_id = ha.ha_api_id),
                            (SELECT spi_name FROM students_personal_info spi
                             JOIN students_apply_info sai ON spi.spi_id = sai.sai_spi_id
                             WHERE (UPPER(sai.sai_enrollment_no) = UPPER(ha.ha_enrolment_no)
                                OR UPPER(sai.sai_enrollment_no) = UPPER(TO_CHAR(ha.ha_aai_id)))
                             AND ROWNUM <= 1)
                        ) as student_name
                    FROM hostel_assign ha
                    JOIN student_challan sc ON ha.ha_challan_id = sc.id
                    JOIN hostel_beds hb ON ha.ha_hb_id = hb.hb_id
                    JOIN hostel_rooms hr ON hb.hb_room_id = hr.hr_id
                    JOIN hostel_floor hf ON hr.hr_hf_id = hf.hf_id
                    JOIN hostel_type ht ON hr.hr_ht_id = ht.ht_id
                    WHERE ha.ha_session = :session_id
                    AND sc.sc_challan_status = 'PAID'
                    AND sc.sc_challan_type = 'Hostel_Fee'
                    AND hr.hr_ht_id = :hostel_id
                ", ['session_id' => $sessionId, 'hostel_id' => $hostel->ht_id]);

                $safeCount = count($safeStudents);

                // 4. Calculate Vacate Count (Occupied - Paid)
                // We use raw sql to get the occupied bed IDs for this hostel
                $occupiedBedIds = DB::select("
                    SELECT hb.hb_id
                    FROM hostel_beds hb
                    JOIN hostel_rooms hr ON hb.hb_room_id = hr.hr_id
                    JOIN hostel_floor hf ON hr.hr_hf_id = hf.hf_id
                    WHERE hf.hf_new_hostel_type = :hostel_id
                    AND hb.hb_is_occupied = 'Y'
                    AND hr.hr_status = 'E'
                    AND hf.hf_status = 'E'
                ", ['hostel_id' => $hostel->ht_id]);

                // Get Safe Bed IDs to compare
                $safeBedIds = DB::select("
                    SELECT ha.ha_hb_id
                    FROM hostel_assign ha
                    JOIN student_challan sc ON ha.ha_challan_id = sc.id
                    WHERE ha.ha_session = :session_id
                    AND sc.sc_challan_status = 'PAID'
                    AND sc.sc_challan_type = 'Hostel_Fee'
                ", ['session_id' => $sessionId]); // Global safe check (or per hostel)

                // Extract arrays for diff
                $occArr = array_map(function ($x) {
                    return $x->hb_id;
                }, $occupiedBedIds);
                $safeArr = array_map(function ($x) {
                    return $x->ha_hb_id;
                }, $safeBedIds);

                // Count difference
                $vacateCount = 0;
                foreach ($occArr as $bedId) {
                    if (!in_array($bedId, $safeArr)) {
                        $vacateCount++;
                    }
                }

                $analysis[] = [
                    'hostel_id' => $hostel->ht_id,
                    'hostel_name' => $hostel->ht_name,
                    'total_beds' => $totalBeds,
                    'occupied_beds' => $occupiedBeds,
                    'free_beds' => $freeBeds,
                    'safe_count' => $safeCount,
                    'vacate_count' => $vacateCount,
                    'safe_students' => $safeStudents
                ];
            }

            return response()->json([
                'success' => true,
                'analysis' => $analysis
            ]);

        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Function: Perform Session Rollover (Bulk Vacate)
     * Purpose: This function actually REMOVES students who haven't paid.
     *
     * Process Steps:
     * 1. Free up Beds: Resets the 'Occupied' status of beds to 'No', EXCEPT for Paid students.
     * 2. Recalculate Rooms: Updates the room counters to match the new empty beds.
     * 3. Cancel Challans: Finds unpaid challans for vacated students and cancels them (using a loop).
     * 4. Log History: Keeps a record of who performed this action.
     */
    public function performSessionRollover(Request $request)
    {
        try {
            $sessionId = $request->input('session_id');
            $hostelId = $request->input('hostel_id');
            $remarks = $request->input('remarks');

            if (!$sessionId)
                return response()->json(['success' => false, 'message' => 'Session ID is required'], 400);
            if (!$hostelId)
                return response()->json(['success' => false, 'message' => 'Hostel ID is required for safety.'], 400);

            // Start Transaction: Ensures all changes happen together, or none at all (Safety)
            DB::beginTransaction();

            // 1. UPDATE BEDS: Reset hb_is_occupied = 'N'
            // Criteria: Currently 'Y' AND in Enabled Room/Floor AND (NOT IN Safe List of Target Session)
            // Specific Hostel Only

            // Build the Safe List Subquery
            $safeSubQuery = "
                SELECT ha.ha_hb_id
                FROM hostel_assign ha
                JOIN student_challan sc ON ha.ha_challan_id = sc.id
                WHERE ha.ha_session = :session_id
                AND sc.sc_challan_status = 'PAID'
                AND sc.sc_challan_type = 'Hostel_Fee'
            ";

            $params = [
                'session_id' => $sessionId,
                'hostel_id' => $hostelId
            ];

            // Execute Bulk Update
            $updateSql = "
                UPDATE hostel_beds hb
                SET hb.hb_is_occupied = 'N'
                WHERE hb.hb_is_occupied = 'Y'
                AND hb.hb_id IN (
                    SELECT hb2.hb_id
                    FROM hostel_beds hb2
                    JOIN hostel_rooms hr ON hb2.hb_room_id = hr.hr_id
                    JOIN hostel_floor hf ON hr.hr_hf_id = hf.hf_id
                    WHERE hr.hr_status = 'E'
                    AND hf.hf_status = 'E'
                    AND hr.hr_ht_id = :hostel_id
                )
                AND hb.hb_id NOT IN ($safeSubQuery)
            ";

            // Execute Update and get Count
            $updatedCount = DB::update($updateSql, $params);

            // 2. RECALCULATE Room Counts
            // Critical: Ensure hr_occupied_beds matches actual hb_is_occupied count

            $recalcSql = "
                UPDATE hostel_rooms hr
                SET hr.hr_occupied_beds = (
                    SELECT COUNT(*)
                    FROM hostel_beds hb
                    WHERE hb.hb_room_id = hr.hr_id
                    AND hb.hb_is_occupied = 'Y'
                )
                WHERE hr.hr_status = 'E'
                AND hr.hr_ht_id = :rec_hostel_id
            ";

            // Execute recalc
            $recalcParams = ['rec_hostel_id' => $hostelId];
            DB::update($recalcSql, $recalcParams);


            // 3. CANCEL UNPAID CHALLANS (Using Existing Function)
            // Fetch students with Unpaid challans for this session

            $uSql = "
                SELECT DISTINCT ha.ha_enrolment_no
                FROM hostel_assign ha
                JOIN student_challan sc ON ha.ha_challan_id = sc.id
                JOIN hostel_beds hb ON ha.ha_hb_id = hb.hb_id
                JOIN hostel_rooms hr ON hb.hb_room_id = hr.hr_id
                WHERE ha.ha_session = :session_id
                AND hb.hb_is_occupied = 'Y'
                AND hr.hr_ht_id = :can_hostel_id
                AND sc.sc_challan_status = 'UNPAID'
                AND sc.sc_challan_type = 'Hostel_Fee'
            ";

            $uParams = [
                'session_id' => $sessionId,
                'can_hostel_id' => $hostelId
            ];

            $unpaidStudents = DB::select($uSql, $uParams);
            $cancelledCount = 0;

            foreach ($unpaidStudents as $std) {
                if (!empty($std->ha_enrolment_no)) {
                    // Call existing function
                    $res = $this->cancelHostelChallan($std->ha_enrolment_no, $sessionId);
                    if ($res)
                        $cancelledCount++;
                }
            }


            // Log
            try {
                $hostelName = 'Specific Hostel';
                // Could fetch name but ID is fine
                DB::table('hostel_vacate_logs')->insert([
                    'hvl_session_id' => $sessionId,
                    'hvl_hostel_id' => $hostelId,
                    'hvl_hostel_name' => "Hostel ID: $hostelId (Rollover SQL)",
                    'hvl_vacated_by' => auth()->user()->username ?? 'system',
                    'hvl_vacated_at' => Carbon::now(),
                    'hvl_total_vacated' => $updatedCount,
                    'hvl_paid_kept' => 0,
                    'hvl_students_list' => 'Bulk SQL Reset',
                    'hvl_remarks' => ($remarks ?? 'Session Rollover Bulk SQL') . " | Cancelled Challans: $cancelledCount",
                    'hvl_created_at' => Carbon::now(),
                    'hvl_updated_at' => Carbon::now()
                ]);
            } catch (Exception $e) { /* Ignore */
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => "Successfully vacated beds and cancelled unpaid challans.", 'total_vacated' => $updatedCount]);

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Campus-wise BH/GH matrix: Issued (challans) and Resident (paid + bed).
     */
    public function getCampusMatrixSummary(Request $request)
    {
        try {
            $sessionId = (int) $request->input('session_id');
            if ($sessionId <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Session is required',
                ], 400);
            }

            $matrix = $this->buildCampusMatrixSummary(
                $sessionId,
                $request->input('from_date'),
                $request->input('to_date'),
                $request->input('hostel_type', 'all')
            );

            return response()->json([
                'success' => true,
                'matrix' => $matrix,
            ]);
        } catch (Exception $e) {
            Log::error('Hostel campus matrix summary error: ' . $e->getMessage(), [
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Unable to load hostel campus summary.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    protected function mapHostelChallanCampusSql(string $alias = 'sc'): string
    {
        return "CASE
            WHEN {$alias}.sc_session_type IS NULL
                 OR LOWER(TRIM({$alias}.sc_session_type)) IN ('regular', 'non-regular') THEN 'on_campus'
            WHEN LOWER(TRIM({$alias}.sc_session_type)) LIKE '%bedian%' THEN 'bedian'
            WHEN LOWER(TRIM({$alias}.sc_session_type)) LIKE '%outside%' THEN 'safari'
            ELSE 'on_campus'
        END";
    }

    protected function emptyCampusMatrixRow(): array
    {
        $campus = ['issued' => 0, 'resident' => 0];

        return [
            'on_campus' => $campus,
            'bedian' => $campus,
            'safari' => $campus,
        ];
    }

    protected function buildCampusMatrixSummary(int $sessionId, $fromDate, $toDate, string $hostelTypeFilter = 'all'): array
    {
        $matrix = [
            'BH' => $this->emptyCampusMatrixRow(),
            'GH' => $this->emptyCampusMatrixRow(),
        ];

        $campusExpr = $this->mapHostelChallanCampusSql('sc');
        $bindings = ['session_id' => $sessionId];
        $issuedWhere = "
            AND sc.sc_challan_type = 'Hostel_Fee'
            AND sc.invoice_status = 'E'
            AND sc.sc_session_id = :session_id
            AND sc.sc_hostel_category IN ('BH', 'GH')
            AND UPPER(TRIM(sc.sc_challan_status)) IN ('PAID', 'UNPAID')
            AND NOT EXISTS (
                SELECT 1
                FROM fin_student_cancel_challan cancel_ch
                WHERE cancel_ch.challan_no = sc.challan_no
            )
        ";

        if (!empty($fromDate)) {
            $issuedWhere .= " AND TRUNC(sc.sc_issu_date) >= TO_DATE(:from_date, 'YYYY-MM-DD') ";
            $bindings['from_date'] = $fromDate;
        }

        if (!empty($toDate)) {
            $issuedWhere .= " AND TRUNC(sc.sc_issu_date) <= TO_DATE(:to_date, 'YYYY-MM-DD') ";
            $bindings['to_date'] = $toDate;
        }

        if ($hostelTypeFilter !== 'all') {
            $issuedWhere .= ' AND (' . $campusExpr . ') = :campus_filter ';
            $bindings['campus_filter'] = $hostelTypeFilter;
        }

        $issuedRows = DB::select("
            SELECT category, campus, COUNT(*) AS total
            FROM (
                SELECT
                    sc.sc_hostel_category AS category,
                    {$campusExpr} AS campus
                FROM student_challan sc
                WHERE 1 = 1
                {$issuedWhere}
            ) issued_base
            GROUP BY category, campus
        ", $bindings);

        foreach ($issuedRows as $row) {
            $category = strtoupper((string) ($row->category ?? ''));
            $campus = (string) ($row->campus ?? '');
            if (!isset($matrix[$category][$campus])) {
                continue;
            }
            $matrix[$category][$campus]['issued'] = (int) ($row->total ?? 0);
        }

        $residentBindings = ['session_id' => $sessionId];
        $residentWhere = "
            AND ha.ha_session = :session_id
            AND ha.ha_hb_id IS NOT NULL
            AND hb.hb_is_occupied = 'Y'
            AND sc.sc_challan_type = 'Hostel_Fee'
            AND sc.invoice_status = 'E'
            AND UPPER(sc.sc_challan_status) = 'PAID'
            AND sc.sc_hostel_category IN ('BH', 'GH')
            AND NOT EXISTS (
                SELECT 1
                FROM fin_student_cancel_challan cancel_ch
                WHERE cancel_ch.challan_no = sc.challan_no
            )
        ";

        if (!empty($fromDate)) {
            $residentWhere .= " AND TRUNC(sc.sc_issu_date) >= TO_DATE(:from_date, 'YYYY-MM-DD') ";
            $residentBindings['from_date'] = $fromDate;
        }

        if (!empty($toDate)) {
            $residentWhere .= " AND TRUNC(sc.sc_issu_date) <= TO_DATE(:to_date, 'YYYY-MM-DD') ";
            $residentBindings['to_date'] = $toDate;
        }

        if ($hostelTypeFilter !== 'all') {
            $residentWhere .= ' AND (' . $campusExpr . ') = :campus_filter ';
            $residentBindings['campus_filter'] = $hostelTypeFilter;
        }

        $residentRows = DB::select("
            SELECT category, campus, COUNT(DISTINCT ha_id) AS total
            FROM (
                SELECT
                    ha.ha_id,
                    sc.sc_hostel_category AS category,
                    {$campusExpr} AS campus
                FROM hostel_assign ha
                INNER JOIN student_challan sc
                    ON sc.id = ha.ha_challan_id
                INNER JOIN hostel_beds hb
                    ON hb.hb_id = ha.ha_hb_id
                WHERE 1 = 1
                {$residentWhere}
            ) resident_base
            GROUP BY category, campus
        ", $residentBindings);

        foreach ($residentRows as $row) {
            $category = strtoupper((string) ($row->category ?? ''));
            $campus = (string) ($row->campus ?? '');
            if (!isset($matrix[$category][$campus])) {
                continue;
            }
            $matrix[$category][$campus]['resident'] = (int) ($row->total ?? 0);
        }

        return $matrix;
    }

    /**
     * Session + hostel wise issued hostel challan list with scholarship info.
     */
    public function getHostelChallanList(Request $request)
    {
        try {
            $request->validate([
                'session_id' => 'required|integer',
                'hostel_location' => 'required|string|max:50',
                'hostel_gender' => 'nullable|string|in:boys,girls,Boys,Girls,male,female,Male,Female,1,2',
                'hostel_id' => 'nullable|integer',
            ]);

            $sessionId = (int) $request->input('session_id');
            $hostelLocation = strtolower(trim($request->input('hostel_location')));
            $genderInput = $request->input('hostel_gender');
            $genderType = filled($genderInput) ? $this->resolveHostelGenderType((string) $genderInput) : null;
            $hostelId = $request->input('hostel_id') ? (int) $request->input('hostel_id') : null;

            $hostel = null;
            if ($hostelId) {
                $hostel = DB::table('hostel_type')
                    ->where('ht_id', $hostelId)
                    ->where('ht_status', 'E')
                    ->first(['ht_id', 'ht_name', 'ht_location', 'ht_hostel_type']);

                if (!$hostel) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Hostel not found',
                    ], 404);
                }

                if ($genderType !== null && (int) ($hostel->ht_hostel_type ?? 0) !== $genderType) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Selected hostel does not match the chosen gender.',
                    ], 400);
                }

                if (!$this->hostelLocationsMatch($hostelLocation, $hostel->ht_location ?? '')) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Selected hostel does not match the chosen location.',
                    ], 400);
                }
            }

            if ($genderType === 1) {
                $hostelCategory = 'BH';
                $hostelLabel = 'Boys Hostel';
            } elseif ($genderType === 2) {
                $hostelCategory = 'GH';
                $hostelLabel = 'Girls Hostel';
            } else {
                $hostelCategory = null;
                $hostelLabel = 'All Hostels';
            }
            $scholarshipMatchSql = $this->hostelScholarshipChallanMatchSql();

            $query = DB::table('student_challan as sc')
                ->leftJoin('hostel_assign as ha', function ($join) {
                    $join->on('ha.ha_session', '=', 'sc.sc_session_id')
                        ->whereRaw('UPPER(ha.ha_enrolment_no) = UPPER(sc.sc_enrollment_no)');
                })
                ->leftJoin('hostel_beds as hb', 'ha.ha_hb_id', '=', 'hb.hb_id')
                ->leftJoin('hostel_rooms as hr', 'hb.hb_room_id', '=', 'hr.hr_id')
                ->leftJoin('hostel_floor as hf', 'hr.hr_hf_id', '=', 'hf.hf_id')
                ->where('sc.sc_session_id', $sessionId)
                ->where('sc.sc_challan_type', 'Hostel_Fee')
                ->whereRaw("NVL(sc.sc_challan_status, 'X') != 'CANCELLED'")
                ->whereRaw("NVL(sc.invoice_status, 'E') = 'E'");

            if ($hostelCategory !== null) {
                $query->whereRaw('UPPER(TRIM(sc.sc_hostel_category)) = ?', [strtoupper($hostelCategory)]);
            }

            $this->applyHostelLocationChallanFilter($query, $hostelLocation, 'sc.sc_session_type');

            $students = $query->select(
                'sc.id as challan_id',
                'sc.challan_no',
                'sc.sc_enrollment_no as enrollment_no',
                'sc.sc_app_id as applicant_id',
                'sc.sc_session_type as session_type',
                DB::raw('NVL(sc.sc_amount, sc.invoiceamount) as challan_amount'),
                'sc.sc_scholarship_amount as scholarship_amount',
                'sc.sc_challan_status as challan_status',
                'sc.sc_due_date as due_date',
                'sc.sc_paid_on as paid_date',
                'hf.hf_name as floor_name',
                'hr.hr_room_no as room_no',
                'hb.hb_bed_number as bed_no',
                DB::raw("COALESCE(
                    (SELECT spi.spi_name FROM students_apply_info sai
                     JOIN students_personal_info spi ON sai.sai_spi_id = spi.spi_id
                     WHERE UPPER(sai.sai_enrollment_no) = UPPER(sc.sc_enrollment_no)),
                    (SELECT api.api_name FROM admission_apply_info aai
                     JOIN admission_personal_info api ON aai.aai_api_id = api.api_id
                     WHERE aai.aai_id = sc.sc_app_id)
                ) as student_name"),
                DB::raw("(
                    SELECT LISTAGG(
                        st.scholarship_name || NVL2(sshd.sshd_percentage, ' (' || sshd.sshd_percentage || '%)', ''),
                        ', '
                    ) WITHIN GROUP (ORDER BY st.scholarship_name)
                    FROM fin_students_scholarships_hostel_details sshd
                    JOIN scholarship_type st ON st.scholarship_id = sshd.sshd_type
                    WHERE sshd.sshd_sess_id = sc.sc_session_id
                      AND NVL(sshd.sshd_status, 'E') = 'E'
                      AND LOWER(st.scholarship_desc) = 'hostel'
                      AND {$scholarshipMatchSql}
                ) as scholarship_names")
            )
                ->orderBy('sc.challan_no')
                ->get();

            $issued = $students->count();
            $paid = $students->where('challan_status', 'PAID')->count();
            $unpaid = $students->filter(function ($row) {
                return strtoupper((string) ($row->challan_status ?? '')) !== 'PAID';
            })->count();
            $withScholarship = $students->filter(function ($row) {
                return ((float) ($row->scholarship_amount ?? 0)) > 0 || filled($row->scholarship_names);
            })->count();

            $session = DB::table('academic_sessions')
                ->where('sess_id', $sessionId)
                ->value('sess_name');

            return response()->json([
                'success' => true,
                'session_id' => $sessionId,
                'session_name' => $session,
                'hostel' => [
                    'id' => $hostel->ht_id ?? $hostelId,
                    'name' => $hostel->ht_name ?? $hostelLabel,
                    'location' => $hostelLocation,
                    'gender' => $genderType === 1 ? 'boys' : ($genderType === 2 ? 'girls' : 'all'),
                ],
                'stats' => [
                    'issued' => $issued,
                    'paid' => $paid,
                    'unpaid' => $unpaid,
                    'with_scholarship' => $withScholarship,
                ],
                'students' => $students->values(),
            ]);
        } catch (Exception $e) {
            Log::error('Error in getHostelChallanList: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error loading hostel challan list.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Search student hostel history by student ID (enrollment number or applicant ID)
     */
    public function searchStudent(Request $request)
    {
        try {
            $studentId = trim((string) $request->input('student_id'));

            if ($studentId === '') {
                return response()->json([
                    'success' => false,
                    'message' => 'Student ID is required',
                ], 400);
            }

            $identity = $this->resolveStudentSearchIdentity($studentId);
            if (!$identity['enrollment_no'] && !$identity['applicant_id']) {
                return response()->json([
                    'success' => false,
                    'message' => 'No student record found for this ID',
                ], 404);
            }

            $query = DB::table('hostel_assign as ha')
                ->leftJoin('student_challan as sc', function ($join) {
                    $join->whereRaw($this->hostelChallanJoinSql());
                })
                ->leftJoin('hostel_beds as hb', 'ha.ha_hb_id', '=', 'hb.hb_id')
                ->leftJoin('hostel_rooms as hr', 'hb.hb_room_id', '=', 'hr.hr_id')
                ->leftJoin('hostel_floor as hf', 'hr.hr_hf_id', '=', 'hf.hf_id')
                ->leftJoin('hostel_type as ht', 'hr.hr_ht_id', '=', 'ht.ht_id')
                ->leftJoin('academic_sessions as sess', 'ha.ha_session', '=', 'sess.sess_id')
                ->where(function ($q) {
                    $q->whereNotNull('ha.ha_challan_id')
                        ->orWhereNotNull('ha.ha_hb_id');
                });

            if ($identity['enrollment_no']) {
                $query->where(DB::raw('UPPER(ha.ha_enrolment_no)'), strtoupper($identity['enrollment_no']));
            } else {
                $query->whereExists(function ($sub) use ($identity) {
                    $sub->select(DB::raw(1))
                        ->from('admission_apply_info as aai')
                        ->whereColumn(DB::raw('UPPER(aai.cms_sai_enrollment_no)'), DB::raw('UPPER(ha.ha_enrolment_no)'))
                        ->where('aai.aai_id', $identity['applicant_id']);
                });
            }

            $scholarshipMatchSql = $this->hostelScholarshipSessionMatchSql();

            $assignments = $query->select(
                'ha.ha_id',
                'ha.ha_enrolment_no',
                'ha.ha_hb_id',
                'ha.ha_session',
                'sess.sess_name as session_name',
                'ht.ht_name as hostel_name',
                'ht.ht_location as hostel_location',
                'hf.hf_name as floor_name',
                'hr.hr_room_no as room_no',
                'hb.hb_bed_number as bed_no',
                'sc.id as challan_id',
                'sc.challan_no as challan_no',
                'sc.sc_challan_status as challan_status',
                'sc.sc_session_type as challan_session_type',
                DB::raw('NVL(sc.sc_amount, sc.invoiceamount) as challan_amount'),
                'sc.sc_scholarship_amount as challan_scholarship_amount',
                'sc.sc_due_date as challan_due_date',
                'sc.sc_paid_on as challan_paid_date',
                DB::raw("(
                    SELECT LISTAGG(
                        st.scholarship_name || NVL2(sshd.sshd_percentage, ' (' || sshd.sshd_percentage || '%)', ''),
                        ', '
                    ) WITHIN GROUP (ORDER BY st.scholarship_name)
                    FROM fin_students_scholarships_hostel_details sshd
                    JOIN scholarship_type st ON st.scholarship_id = sshd.sshd_type
                    WHERE sshd.sshd_sess_id = ha.ha_session
                      AND NVL(sshd.sshd_status, 'E') = 'E'
                      AND LOWER(st.scholarship_desc) = 'hostel'
                      AND {$scholarshipMatchSql}
                ) as scholarship_names"),
                DB::raw("(
                    SELECT NVL(SUM(NVL(sshd.sshd_arrear, 0)), 0)
                    FROM fin_students_scholarships_hostel_details sshd
                    JOIN scholarship_type st ON st.scholarship_id = sshd.sshd_type
                    WHERE sshd.sshd_sess_id = ha.ha_session
                      AND NVL(sshd.sshd_status, 'E') = 'E'
                      AND LOWER(st.scholarship_desc) = 'hostel'
                      AND {$scholarshipMatchSql}
                ) as scholarship_arrear")
            )
                ->orderBy('ha.ha_session', 'desc')
                ->get();

            if ($assignments->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hostel records found for this student ID',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'student_info' => $this->buildStudentProfile($studentId, $identity),
                'hostel_history' => $assignments,
            ]);
        } catch (Exception $e) {
            Log::error('Error in searchStudent: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while searching for student. Please try again.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    private function resolveStudentSearchIdentity(string $studentId): array
    {
        $challanController = new GenerateSingleChallanController();

        if (is_numeric($studentId)) {
            $appId = (int) $studentId;
            $enrollmentNo = $challanController->getEnrollmentNumberByAppId($appId);

            return [
                'enrollment_no' => $enrollmentNo,
                'applicant_id' => $appId,
            ];
        }

        $enrollmentNo = strtoupper(trim($studentId));

        return [
            'enrollment_no' => $enrollmentNo,
            'applicant_id' => $challanController->getAppIdByEnrollmentNumber($enrollmentNo),
        ];
    }

    private function buildStudentProfile(string $studentId, array $identity): array
    {
        $challanController = new GenerateSingleChallanController();
        $profile = $challanController->getStudentRec(new Request(['studentEnrollment' => $studentId]));

        $studentInfo = [
            'student_id' => $identity['enrollment_no'] ?: ($identity['applicant_id'] ?: $studentId),
            'enrollment_no' => $identity['enrollment_no'],
            'applicant_id' => $identity['applicant_id'],
            'student_name' => null,
            'student_email' => null,
            'mobile' => null,
            'gender' => null,
            'father_name' => null,
            'school' => null,
            'department' => null,
            'program' => null,
            'student_status' => null,
            'student_type' => $identity['enrollment_no'] ? 'Existing Student' : 'New Applicant',
        ];

        if (is_object($profile)) {
            $studentInfo['student_name'] = $profile->spi_name ?? null;
            $studentInfo['enrollment_no'] = $profile->username ?? $studentInfo['enrollment_no'];
            $studentInfo['applicant_id'] = $profile->aai_id ?? $studentInfo['applicant_id'];
            $studentInfo['student_email'] = $profile->spi_personal_email ?? $profile->spi_email ?? null;
            $studentInfo['mobile'] = $profile->spi_mobile ?? null;
            $studentInfo['school'] = $profile->school_name ?? null;
            $studentInfo['department'] = $profile->dept_name ?? null;
            $studentInfo['program'] = $profile->prog_name ?? null;
            $studentInfo['student_status'] = $profile->sai_student_status ?? null;
            $studentInfo['student_id'] = $studentInfo['enrollment_no'] ?: $studentInfo['applicant_id'];
        }

        if ($identity['enrollment_no']) {
            $existingStudent = DB::table('students_apply_info as sai')
                ->join('students_personal_info as spi', 'sai.sai_spi_id', '=', 'spi.spi_id')
                ->where(DB::raw('UPPER(sai.sai_enrollment_no)'), strtoupper($identity['enrollment_no']))
                ->first([
                    'spi.spi_gender as gender',
                    'spi.spi_fathername as father_name',
                    'spi.spi_email as personal_email',
                    'spi.spi_name as student_name',
                    'spi.spi_mobile as mobile',
                ]);

            if ($existingStudent) {
                $studentInfo['gender'] = $existingStudent->gender ?? $studentInfo['gender'];
                $studentInfo['father_name'] = $existingStudent->father_name ?? $studentInfo['father_name'];
                $studentInfo['student_name'] = $studentInfo['student_name'] ?: $existingStudent->student_name;
                $studentInfo['mobile'] = $studentInfo['mobile'] ?: $existingStudent->mobile;
                $studentInfo['student_email'] = $studentInfo['student_email'] ?: $existingStudent->personal_email;
            }
        } elseif ($identity['applicant_id']) {
            $newApplicant = DB::table('admission_apply_info as aai')
                ->join('admission_personal_info as api', 'aai.aai_api_id', '=', 'api.api_id')
                ->where('aai.aai_id', $identity['applicant_id'])
                ->first([
                    'api.api_gender as gender',
                    'api.api_fathername as father_name',
                    'api.api_email as personal_email',
                    'api.api_name as student_name',
                    'api.api_mobile as mobile',
                    'aai.cms_sai_enrollment_no as enrollment_no',
                ]);

            if ($newApplicant) {
                $studentInfo['gender'] = $newApplicant->gender ?? $studentInfo['gender'];
                $studentInfo['father_name'] = $newApplicant->father_name ?? $studentInfo['father_name'];
                $studentInfo['student_name'] = $studentInfo['student_name'] ?: $newApplicant->student_name;
                $studentInfo['mobile'] = $studentInfo['mobile'] ?: $newApplicant->mobile;
                $studentInfo['student_email'] = $studentInfo['student_email'] ?: $newApplicant->personal_email;
                $studentInfo['enrollment_no'] = $studentInfo['enrollment_no'] ?: $newApplicant->enrollment_no;
            }
        }

        return $studentInfo;
    }

    private function resolveHostelGenderType(string $gender): int
    {
        $normalized = strtolower(trim($gender));

        if (in_array($normalized, ['boys', 'male', 'bh', '1'], true)) {
            return 1;
        }

        return 2;
    }

    private function hostelLocationsMatch(string $selectedLocation, string $hostelLocation): bool
    {
        $selected = strtolower(trim($selectedLocation));
        $location = strtolower(trim($hostelLocation));

        if ($selected === $location) {
            return true;
        }

        $offCampusLocations = ['off_campus', 'out_of_campus'];

        return in_array($selected, $offCampusLocations, true)
            && in_array($location, $offCampusLocations, true);
    }

    private function hostelChallanJoinSql(string $haAlias = 'ha', string $scAlias = 'sc'): string
    {
        $fallback = $this->hostelChallanFallbackIdSubquery($haAlias);

        return "{$scAlias}.id = NVL({$haAlias}.ha_challan_id, {$fallback})";
    }

    private function hostelChallanFallbackIdSubquery(string $haAlias = 'ha'): string
    {
        return "(
            SELECT sc2.id
            FROM student_challan sc2
            WHERE sc2.sc_session_id = {$haAlias}.ha_session
              AND sc2.sc_challan_type = 'Hostel_Fee'
              AND NVL(sc2.sc_challan_status, 'X') != 'CANCELLED'
              AND NVL(sc2.invoice_status, 'E') = 'E'
              AND (
                  UPPER(sc2.sc_enrollment_no) = UPPER({$haAlias}.ha_enrolment_no)
                  OR sc2.sc_app_id IN (
                      SELECT aai.aai_id
                      FROM admission_apply_info aai
                      WHERE UPPER(aai.cms_sai_enrollment_no) = UPPER({$haAlias}.ha_enrolment_no)
                  )
              )
            ORDER BY sc2.id DESC
            FETCH FIRST 1 ROW ONLY
        )";
    }

    private function hostelScholarshipSessionMatchSql(): string
    {
        return "(
            UPPER(sshd.sshd_sai_enrollment_no) = UPPER(ha.ha_enrolment_no)
            OR sshd.sshd_app_id = sc.sc_app_id
            OR sshd.sshd_app_id IN (
                SELECT aai.aai_id
                FROM admission_apply_info aai
                WHERE UPPER(aai.cms_sai_enrollment_no) = UPPER(ha.ha_enrolment_no)
            )
        )";
    }

    private function hostelScholarshipChallanMatchSql(): string
    {
        return "(
            UPPER(sshd.sshd_sai_enrollment_no) = UPPER(sc.sc_enrollment_no)
            OR sshd.sshd_app_id = sc.sc_app_id
            OR sshd.sshd_app_id IN (
                SELECT aai.aai_id
                FROM admission_apply_info aai
                WHERE UPPER(aai.cms_sai_enrollment_no) = UPPER(sc.sc_enrollment_no)
            )
        )";
    }

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

    private function sessionTypeSqlInList(array $types): string
    {
        return implode(', ', array_map(
            fn ($type) => "'" . strtolower($type) . "'",
            $types
        ));
    }
}
