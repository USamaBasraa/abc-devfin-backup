<?php

namespace App\Http\Controllers\Grader\Hostel;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\Console\Helper\Table;
use DB;
use Illuminate\Support\Facades\Input;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Exception;

use App\Http\Controllers\StudentFinancial\GenerateSingleChallanController;

Class HostelController{
    Public function showHostelApplicants(){
        $groupId = auth()->user()->group_id;
        $session = DB::table('academic_sessions')
        ->select('SESS_ID', 'SESS_NAME')
        ->orderBy('SESS_ID', 'desc')
        ->get();


        return view('Hostel.hostel_applicants', [
            'session' => $session
        ]);
    }


    Public function showHostelStudents(){
        $groupId = auth()->user()->group_id;
        $session = DB::table('academic_sessions')
        ->select('SESS_ID', 'SESS_NAME')
        ->orderBy('SESS_ID', 'desc')
        ->get();



        return view('Hostel.hostel_students', [
            'session' => $session
        ]);
    }



    public function getHostelApplicantsData(Request $request)
    {

        $sessionId = $request->input('session_id');
        $admission_schedule_id = $request->input('admission_schedule_id');

        // Dynamic where clause for admission schedule round
        $where = '';
        $where = $admission_schedule_id ? ' admission_schedules.admission_sch_round = ' . $admission_schedule_id : '';

        // Get seat counts using consistent function
        $seatCounts = $this->getSeatCounts($sessionId);
        $male_vacant_seats = $seatCounts['male_vacant_seats'];
        $female_vacant_seats = $seatCounts['female_vacant_seats'];

                // Base SQL query
        $baseQuery = "
            SELECT
                CASE WHEN ap.API_ALLOW_HOSTEL IS NULL THEN 'Not Allowed' ELSE 'Allowed' END AS ALLOWED_STATUS,
                aa.aai_id,
                aa.aai_api_id,
                aa.CMS_SAI_ENROLLMENT_NO,
                ap.API_NAME,
                CASE
                    WHEN ads.admission_sch_round = 1 THEN 'Early Round'
                    WHEN ads.admission_sch_round = 2 THEN 'Regular Round'
                    WHEN ads.admission_sch_round = 3 THEN 'Rolling Round'
                    ELSE 'Unknown'
                END AS admission_sch_round,
                ap.API_GENDER,
                ap.API_PERMANENTADDRESS,
                ap.API_PERMANENTCITY,
                aa.aai_apply_date,
                ap.API_ISHOSTEL,
                aa.aai_sdpo_sess_id,
                ha.ha_disable_map_display,

             (
                SELECT MAX(scc.sc_challan_status)
                FROM student_challan scc
                WHERE scc.SC_SESSION_ID   = :p_session_id
                  AND scc.SC_CHALLAN_TYPE = 'Hostel_Fee'
                  AND scc.SC_APP_ID       = aa.AAI_ID
            ) AS sc_challan_status,

                /* Scholarship details, formatted percent */
                (
                    SELECT LISTAGG(
                               st.SCHOLARSHIP_NAME || ': ' ||
                               TO_CHAR(ROUND(fsd.FSD_DISCOUNT_PERCENTAGE * 100, 2)) || '%',
                               ', '
                           ) WITHIN GROUP (ORDER BY st.SCHOLARSHIP_NAME)
                    FROM fin_scholarship_discounts fsd
                    LEFT JOIN scholarship_type st
                           ON fsd.FSD_SCHOLARSHIP_TYPE = st.SCHOLARSHIP_ID
                    WHERE fsd.FSD_SESION_ID = :p_session_id
                      AND fsd.FSD_AAI_ID    = aa.AAI_ID
                ) AS SCHOLARSHIP_DETAILS,

                /* Last degree marks (add ORDER BY if there can be multiple detail rows per degree) */
                (
                    SELECT CASE
                             WHEN aq.aq_obtained_marks IS NULL THEN
                                  aq.aq_degree_name || ': ' || (
                                      SELECT aqd.ADQ_EQUAL_OBTAIN_MARKS
                                      FROM admission_qualification_detail aqd
                                      WHERE aqd.aqd_api_id      = aq.aq_api_id
                                        AND aqd.aqd_degree_name = aq.aq_degree_name
                                        AND ROWNUM = 1
                                  )
                             ELSE aq.aq_degree_name || ': ' || TO_CHAR(aq.aq_obtained_marks)
                           END
                    FROM admission_qualification aq
                    WHERE aq.aq_api_id = aa.AAI_API_ID
                      AND aq.aq_id = (
                          SELECT MAX(aq2.aq_id)
                          FROM admission_qualification aq2
                          WHERE aq2.aq_api_id = aa.AAI_API_ID
                      )
                ) AS last_degree_marks

            FROM admission_apply_info aa
            JOIN admission_personal_info ap
              ON aa.AAI_API_ID = ap.API_ID
            LEFT JOIN hostel_assign ha
              ON aa.aai_id = ha.ha_aai_id
            LEFT JOIN admission_schedules ads
              ON aa.aai_admission_sch_id = ads.ADMISSION_SCH_ID

            WHERE aa.AAI_STATUS = 'Selected'
              AND ap.API_ISHOSTEL = 'Yes'
              AND aa.AAI_SDPO_SESS_ID = :p_session_id";

        // Add admission schedule filter if needed
        if (!empty($where)) {
            $baseQuery .= " AND ads.admission_sch_round = :p_admission_schedule_id";
        }

        // Add ordering
        $baseQuery .= " ORDER BY CASE WHEN LOWER(ap.API_PERMANENTCITY) = 'lahore' THEN 1 ELSE 0 END, aa.aai_apply_date";

        // Prepare parameters
        $params = ['p_session_id' => $sessionId];
        if (!empty($where)) {
            $params['p_admission_schedule_id'] = $admission_schedule_id;
        }

        // Execute query
        $query = DB::select($baseQuery, $params);

        // Convert to collection for easier processing
        $applicants = collect($query);

        // Filter by gender
        $maleApplicants = $applicants->filter(fn($applicant) => $applicant->api_gender == 'Male');
        $femaleApplicants = $applicants->filter(fn($applicant) => $applicant->api_gender == 'Female');

        // Count allowed applicants by gender
        $maleAllowedCount = $maleApplicants->filter(function($applicant) {
            return $applicant->allowed_status == 'Allowed';
        })->count();

        $femaleAllowedCount = $femaleApplicants->filter(function($applicant) {
            return $applicant->allowed_status == 'Allowed';
        })->count();

        // Count applicants with consent given (challan generated)
        $maleChallanCount = $maleApplicants->filter(function($applicant) {
            return $applicant->allowed_status == 'Allowed' && !empty($applicant->sc_challan_status);
        })->count();

        $femaleChallanCount = $femaleApplicants->filter(function($applicant) {
            return $applicant->allowed_status == 'Allowed' && !empty($applicant->sc_challan_status);
        })->count();

        // Count applicants with paid challans
        $malePaidCount = $maleApplicants->filter(function($applicant) {
            return $applicant->allowed_status == 'Allowed' && $applicant->sc_challan_status == 'PAID';
        })->count();

        $femalePaidCount = $femaleApplicants->filter(function($applicant) {
            return $applicant->allowed_status == 'Allowed' && $applicant->sc_challan_status == 'PAID';
        })->count();

        // Count applicants that can be vacated (allowed but no challan or unpaid)
        $maleVacateCount = $maleApplicants->filter(function($applicant) {
            return $applicant->allowed_status == 'Allowed' &&
                   ($applicant->sc_challan_status != 'PAID' || empty($applicant->sc_challan_status));
        })->count();

        $femaleVacateCount = $femaleApplicants->filter(function($applicant) {
            return $applicant->allowed_status == 'Allowed' &&
                   ($applicant->sc_challan_status != 'PAID' || empty($applicant->sc_challan_status));
        })->count();

        // Return response
        return response()->json([
            'maleApplicants' => $maleApplicants->toArray(),
            'femaleApplicants' => $femaleApplicants->toArray(),
            'maleCount' => $maleApplicants->count(),
            'femaleCount' => $femaleApplicants->count(),
            'male_vacant_seats' => $male_vacant_seats,
            'female_vacant_seats' => $female_vacant_seats,
            'male_allowed_count' => $maleAllowedCount,
            'female_allowed_count' => $femaleAllowedCount,
            'male_challan_count' => $maleChallanCount,
            'female_challan_count' => $femaleChallanCount,
            'male_paid_count' => $malePaidCount,
            'female_paid_count' => $femalePaidCount,
            'male_vacate_count' => $maleVacateCount,
            'female_vacate_count' => $femaleVacateCount,
            'new_count' => $applicants->count(),
        ]);
    }




        public function getHostelStudentsData(Request $request)
    {
        $sessionId = $request->input('session_id');

        // Get seat counts using consistent function (same as applicants)
        $seatCounts = $this->getSeatCounts($sessionId);
        $male_vacant_seats = $seatCounts['male_vacant_seats'];
        $female_vacant_seats = $seatCounts['female_vacant_seats'];

        // Get hostel students using raw SQL
        $students = DB::select("
            SELECT
                happ.happ_allowed_status,
                happ.happ_enrollment_no,
                spi.SPI_NAME               AS student_name,
                spi.SPI_GENDER             AS gender,
                spi.SPI_PERMANENTADDRESS   AS permanent_address,
                spi.SPI_PERMANENTCITY      AS permanent_city,
                happ.happ_entry_date       AS apply_date,
                spi.SPI_GENDER             AS spi_gender,
                spi.SPI_ISHOSTEL           AS is_hostel,
                happ.happ_session          AS session_id,

                (
                    SELECT MAX(scc.sc_challan_status)
                    FROM student_challan scc
                    WHERE scc.SC_SESSION_ID   = :p_session_id
                      AND scc.SC_CHALLAN_TYPE = 'Hostel_Fee'
                      AND UPPER(scc.sc_enrollment_no) = UPPER(happ.happ_enrollment_no)
                ) AS sc_challan_status

            FROM hostel_applicants happ
            LEFT JOIN students_apply_info sai
                   ON UPPER(happ.happ_enrollment_no) = UPPER(sai.sai_enrollment_no)
            LEFT JOIN students_personal_info spi
                   ON spi.spi_id = sai.sai_spi_id
            LEFT JOIN hostel_assign hassign
                   ON UPPER(hassign.ha_enrolment_no) = UPPER(happ.happ_enrollment_no)
            WHERE happ.happ_session = :p_session_id
        ", ['p_session_id' => $sessionId]);

        // Convert to collection for easier processing
        $students = collect($students);


        // Filter by gender (using the correct field name from new query)
        $maleStudents = $students->filter(fn($s) => strtolower($s->gender) == 'male');
        $femaleStudents = $students->filter(fn($s) => strtolower($s->gender) == 'female');

        // Count allowed students by gender
        $maleAllowedCount = $maleStudents->filter(function($student) {
            return $student->happ_allowed_status == 'Allowed';
        })->count();

        $femaleAllowedCount = $femaleStudents->filter(function($student) {
            return $student->happ_allowed_status == 'Allowed';
        })->count();

        // Count students with consent given (challan generated)
        $maleChallanCount = $maleStudents->filter(function($student) {
            return $student->happ_allowed_status == 'Allowed' && !empty($student->sc_challan_status);
        })->count();

        $femaleChallanCount = $femaleStudents->filter(function($student) {
            return $student->happ_allowed_status == 'Allowed' && !empty($student->sc_challan_status);
        })->count();

        // Count students with paid challans
        $malePaidCount = $maleStudents->filter(function($student) {
            return $student->happ_allowed_status == 'Allowed' && $student->sc_challan_status == 'PAID';
        })->count();

        $femalePaidCount = $femaleStudents->filter(function($student) {
            return $student->happ_allowed_status == 'Allowed' && $student->sc_challan_status == 'PAID';
        })->count();

        // Count students that can be vacated (allowed but no challan or unpaid)
        $maleVacateCount = $maleStudents->filter(function($student) {
            return $student->happ_allowed_status == 'Allowed' &&
                   ($student->sc_challan_status != 'PAID' || empty($student->sc_challan_status));
        })->count();

        $femaleVacateCount = $femaleStudents->filter(function($student) {
            return $student->happ_allowed_status == 'Allowed' &&
                   ($student->sc_challan_status != 'PAID' || empty($student->sc_challan_status));
        })->count();

        // Return as JSON
        return response()->json([
            'maleStudents' => $maleStudents->values(),
            'femaleStudents' => $femaleStudents->values(),
            'maleCount' => $maleStudents->count(),
            'femaleCount' => $femaleStudents->count(),
            'totalCount' => $students->count(),
            'male_vacant_seats' => $male_vacant_seats,
            'female_vacant_seats' => $female_vacant_seats,
            'male_allowed_count' => $maleAllowedCount,
            'female_allowed_count' => $femaleAllowedCount,
            'male_challan_count' => $maleChallanCount,
            'female_challan_count' => $femaleChallanCount,
            'male_paid_count' => $malePaidCount,
            'female_paid_count' => $femalePaidCount,
            'male_vacate_count' => $maleVacateCount,
            'female_vacate_count' => $femaleVacateCount,
        ]);
    }










    public function allowOldApplicant(Request $request, $enrollmentNo)
    {
        $seatReservationMapDisplay = $request->input('seat_reservation_map_display') ? 'Yes' : 'No';
        $sessionId = $request->input('session_id');

        // Validate session ID is provided
        if (!$sessionId) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Session ID is required']);
            }
            return redirect()->back()->with('error', 'Session ID is required');
        }

        // Get applicant gender for seat validation (for hostel students/old applicants)
        $applicantGender = DB::table('hostel_applicants')
            ->join('students_apply_info', DB::raw('LOWER(hostel_applicants.happ_enrollment_no)'), '=', DB::raw('LOWER(students_apply_info.sai_enrollment_no)'))
            ->join('students_personal_info', 'students_personal_info.spi_id', '=', 'students_apply_info.sai_spi_id')
            ->where('hostel_applicants.happ_enrollment_no', $enrollmentNo)
            ->value('students_personal_info.spi_gender');

        if (!$applicantGender) {
            return response()->json(['success' => false, 'message' => 'Applicant gender not found for enrollment: ' . $enrollmentNo]);
        }

        // Check seat availability using separate function
        $seatValidation = $this->checkSeatAvailability($sessionId, $applicantGender, 'old');
        // dd($seatValidation);

        if (!$seatValidation['available']) {
            return response()->json([
                'success' => false,
                'message' => $seatValidation['message'],
                'errorType' => 'seat_validation',
                'availableSeats' => $seatValidation['availableSeats'],
                'totalAllowed' => $seatValidation['allowedCount'],
                'newAllowed' => $seatValidation['newAllowedCount'],
                'oldAllowed' => $seatValidation['oldAllowedCount'],
                'canVacate' => $seatValidation['unpaidApplicants']
            ]);
        }

        DB::beginTransaction();

        try {
            // Define the query with parameter binding
            $query = "UPDATE hostel_applicants
                SET happ_allowed_status = 'Allowed'
                WHERE LOWER(happ_enrollment_no) = LOWER('$enrollmentNo')
            ";
            // dd($query,$enrollmentNo);
            // Execute the query with parameter binding
            DB::update($query );

            // Commit the transaction if successful
            DB::commit();

            // Use session ID from request instead of getting from database
            $userId = strtolower(auth()->user()->id);

            DB::table('hostel_assign')->insert([
                'ha_enrolment_no' => $enrollmentNo,
                'ha_session' => $sessionId,
                'ha_status' => 'E',
                'ha_disable_map_display' => $seatReservationMapDisplay,
                'ha_entry_user_id' => $userId
                // You can remove 'happ_entry_user_id' and 'happ_entry_date' if they are not needed or manage them accordingly.
            ]);




            return response()->json(['success' => true, 'message' => 'Applicant allowed successfully']);
        } catch (Exception $e) {
            // Rollback the transaction if an error occurs
            DB::rollBack();

            // Log the error message (optional)
            Log::error('Failed to update hostel applicant status: ' . $e->getMessage());

            return response()->json(['success' => false, 'message' => 'Error allowing applicant: ' . $e->getMessage()]);
        }
    }
    public function allowNewApplicant(Request $request, $enrollmentNo)
    {
        // dd(auth()->user());
        DB::beginTransaction(); // Start the transaction
        $aaiApiId = DB::table('admission_apply_info')
                ->where('aai_id', $enrollmentNo)
                ->value('aai_api_id');

        $seatReservationMapDisplay = $request->input('seat_reservation_map_display') == '1' ? 'Yes' : 'No';
        $sessionId = $request->input('session_id');

        // Validate session ID is provided
        if (!$sessionId) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Session ID is required']);
            }
            return redirect()->back()->with('error', 'Session ID is required');
        }

        // Get applicant gender for seat validation
        $applicantGender = DB::table('admission_personal_info')
            ->where('api_id', $aaiApiId)
            ->value('api_gender');

        if (!$applicantGender) {
            return response()->json(['success' => false, 'message' => 'Applicant gender not found']);
        }

                // Check seat availability using separate function
        $seatValidation = $this->checkSeatAvailability($sessionId, $applicantGender, 'new');


        if (!$seatValidation['available']) {
            return response()->json([
                'success' => false,
                'message' => $seatValidation['message'],
                'errorType' => 'seat_validation',
                'availableSeats' => $seatValidation['availableSeats'],
                'totalAllowed' => $seatValidation['allowedCount'],
                'newAllowed' => $seatValidation['newAllowedCount'],
                'oldAllowed' => $seatValidation['oldAllowedCount'],
                'canVacate' => $seatValidation['unpaidApplicants']
            ]);
        }

        // dd($request->all());
        // dd( $request->input('seat_reservation_map_display'));
        try {
            // Define the query with parameter binding
            $query = "
            UPDATE admission_personal_info
            SET api_allow_hostel = 'Allowed'
            WHERE api_id = :aaiApiId
            ";
    // dd($query,$enrollmentNo);
            // Execute the query with parameter binding
            DB::update($query, ['aaiApiId' => $aaiApiId]);

            // Use session ID from request instead of getting from database
            $userId = strtolower(auth()->user()->id);

            DB::table('hostel_assign')->insert([
                'ha_aai_id' => $enrollmentNo,
                'ha_api_id' => $aaiApiId ,
                'ha_disable_map_display' => $seatReservationMapDisplay,
                'ha_session' => $sessionId,
                'ha_status' => 'E',
                'ha_entry_user_id' => $userId
                // You can remove 'happ_entry_user_id' and 'happ_entry_date' if they are not needed or manage them accordingly.
            ]);
            DB::commit(); // Commit the transaction if successful

            return response()->json(['success' => true, 'message' => 'Applicant allowed successfully']);
        } catch (Exception $e) {
            DB::rollBack(); // Rollback the transaction on error

            // Log the error message (optional)
            Log::error('Failed to allow applicant: ' . $e->getMessage());

            return response()->json(['success' => false, 'message' => 'Error allowing applicant: ' . $e->getMessage()]);
        }
    }

    /**
     * Get consistent seat counts for both applicants and students
     */
    private function getSeatCounts($sessionId)
    {
        // Count total vacant male hostel beds (this is the available seats)
        $male_vacant_seats = DB::table('hostel_beds')
            ->join('hostel_rooms', 'hostel_beds.hb_room_id', '=', 'hostel_rooms.hr_id')
            ->where('hostel_rooms.hr_ht_id', 1) // Male hostel type
            ->where('hostel_beds.hb_is_occupied', 'N')
            ->where('hostel_rooms.hr_status', 'E')
            ->count('hostel_beds.hb_bed_number');

        // Count total vacant female hostel beds (this is the available seats)
        $female_vacant_seats = DB::table('hostel_beds')
            ->join('hostel_rooms', 'hostel_beds.hb_room_id', '=', 'hostel_rooms.hr_id')
            ->where('hostel_rooms.hr_ht_id', 2) // Female hostel type
            ->where('hostel_beds.hb_is_occupied', 'N')
            ->where('hostel_rooms.hr_status', 'E')
            ->count('hostel_beds.hb_bed_number');

        return [
            'male_vacant_seats' => $male_vacant_seats,
            'female_vacant_seats' => $female_vacant_seats
        ];
    }







    /**
 * Check seat availability for applicants (combined validation for new + old)
 */
private function checkSeatAvailability($sessionId, $applicantGender, $applicantType = 'new')
{
    // 1) Normalize inputs
    $gender = strtolower(trim($applicantGender)); // 'male' | 'female'
    if (!in_array($gender, ['male', 'female'], true)) {
        return [
            'available' => false,
            'message' => "Invalid gender: {$applicantGender}",
            'availableSeats' => 0,
            'allowedCount' => 0,
            'newAllowedCount' => 0,
            'oldAllowedCount' => 0,
            'unpaidApplicants' => 0,
        ];
    }
    // 2) Available seats for this gender
    $seatCounts = $this->getSeatCounts($sessionId);
    $availableSeats = $gender === 'male'
        ? ($seatCounts['male_vacant_seats'] ?? 0)
        : ($seatCounts['female_vacant_seats'] ?? 0);

    // 3) Count already allowed (NEW + OLD) — use DISTINCT to avoid over-counts from joins
    $newAllowedCount = DB::table('admission_personal_info as api')
        ->join('admission_apply_info as aai', 'api.api_formid', '=', 'aai.aai_api_form_id')
        ->where('api.api_allow_hostel', 'Allowed')
        ->where('aai.aai_sdpo_sess_id', $sessionId)
        ->whereRaw('LOWER(api.api_gender) = ?', [$gender])
        ->distinct('aai.aai_id')                 // choose a stable unique column for "new" applicants
        ->count('aai.aai_id');

    $oldAllowedCount = DB::table('hostel_applicants as happ')
        ->join('students_apply_info as sai', DB::raw('UPPER(happ.happ_enrollment_no)'), '=', DB::raw('UPPER(sai.sai_enrollment_no)'))
        ->join('students_personal_info as spi', 'spi.spi_id', '=', 'sai.sai_spi_id')
        ->where('happ.happ_allowed_status', 'Allowed')
        ->where('happ.happ_session', $sessionId)
        ->whereRaw('LOWER(spi.spi_gender) = ?', [$gender])
        ->distinct('happ.happ_enrollment_no')    // stable unique for "old" applicants
        ->count('happ.happ_enrollment_no');

    // 4) Combined allowed
    $totalAllowedCount = $newAllowedCount + $oldAllowedCount;

    // 5) If full (or over), check unpaid challans we could vacate (NEW + OLD)
    if ($totalAllowedCount >= $availableSeats) {
        $genderText = $gender; // already 'male'/'female'

        // NEW: unpaid challans (distinct AAI)
        $newUnpaidApplicants = DB::table('admission_personal_info as api')
            ->join('admission_apply_info as aai', 'api.api_formid', '=', 'aai.aai_api_form_id')
            ->join('hostel_assign as ha', 'aai.aai_id', '=', 'ha.ha_aai_id')
            ->leftJoin('student_challan as sc', 'sc.id', '=', 'ha.ha_challan_id')
            ->where('api.api_allow_hostel', 'Allowed')
            ->where('aai.aai_sdpo_sess_id', $sessionId)
            ->whereRaw('LOWER(api.api_gender) = ?', [$gender])
            ->where(function ($q) {
                $q->where('sc.sc_challan_status', '!=', 'PAID')
                  ->orWhereNull('sc.sc_challan_status');
            })
            ->distinct('aai.aai_id')
            ->count('aai.aai_id');

        // OLD: unpaid challans (distinct enrollment)
        $oldUnpaidApplicants = DB::table('hostel_applicants as happ')
            ->join('students_apply_info as sai', 'happ.happ_enrollment_no', '=', 'sai.sai_enrollment_no')
            ->join('students_personal_info as spi', 'spi.spi_id', '=', 'sai.sai_spi_id')
            ->join('hostel_assign as ha', 'ha.ha_enrolment_no', '=', 'happ.happ_enrollment_no')
            ->leftJoin('student_challan as sc', 'sc.id', '=', 'ha.ha_challan_id')
            ->where('happ.happ_allowed_status', 'Allowed')
            ->where('happ.happ_session', $sessionId)
            ->whereRaw('LOWER(spi.spi_gender) = ?', [$gender])
            ->where(function ($q) {
                $q->where('sc.sc_challan_status', '!=', 'PAID')
                  ->orWhereNull('sc.sc_challan_status');
            })
            ->distinct('happ.happ_enrollment_no')
            ->count('happ.happ_enrollment_no');

        $totalUnpaidApplicants = $newUnpaidApplicants + $oldUnpaidApplicants;

        $message = "Cannot allow more {$genderText} applicants. "
                 . "Available seats: {$availableSeats}, Total already allowed: {$totalAllowedCount}.";
        if ($totalUnpaidApplicants > 0) {
            $message .= " You can vacate seats who haven't given consent yet or having unpaid challan to allow more seats.";
        }

        return [
            'available' => false,
            'message' => $message,
            'availableSeats' => $availableSeats,
            'allowedCount' => $totalAllowedCount,
            'newAllowedCount' => $newAllowedCount,
            'oldAllowedCount' => $oldAllowedCount,
            'unpaidApplicants' => $totalUnpaidApplicants,
        ];
    }

    // 6) Seats are available
    return [
        'available' => true,
        'message' => 'Seats available',
        'availableSeats' => $availableSeats,
        'allowedCount' => $totalAllowedCount,
        'newAllowedCount' => $newAllowedCount,
        'oldAllowedCount' => $oldAllowedCount,
        'unpaidApplicants' => 0,
    ];
}

    /**
     * Common function to cancel hostel challan
     */
    private function cancelHostelChallan($enrollmentNo, $sessionId, $applicantType = 'new')
    {
        // Use different column based on applicant type
        $query = DB::table('hostel_assign')->where('ha_session', $sessionId);

        if ($applicantType == 'new') {
            $query->where('ha_aai_id', $enrollmentNo);
        } else {
            $query->where(DB::raw('upper(ha_enrolment_no)'), strtoupper($enrollmentNo));
        }

        $getChallanNo = $query->first();



        if (!empty($getChallanNo->ha_challan_id) && $getChallanNo->ha_challan_id != null && $getChallanNo->ha_challan_id != '') {
            $challanNo = $getChallanNo->ha_challan_id;


            // Get challan status
            $challanData = DB::table('student_challan')->where('id', $challanNo)->first();
            $scChallanStatus = !empty($challanData) ? $challanData->sc_challan_status : '';


            DB::table('Student_challan')
            ->where('id', $challanNo)
            ->update([

                'sc_remarks' => 'Auto Cancel Hostel Challan on Applicant Rejected Manually by '.auth()->user()->username
            ]);
            // Base request data
            $data = [
                'id' => $challanNo,
                'cancell_type' => 'CANCELLED',
                'remarks' => 'Auto Cancel Hostel Challan on Applicant Rejected Manually by '.auth()->user()->username,
            ];

            // Only add enrollment_no for new applicants or if we can find the app ID
            if ($data['cancell_type'] === 'CANCELLED') {
                $data['enrollment_no'] =strtoupper($enrollmentNo);
            }


            // Create Laravel request object
            $challanRequest = new Request($data);

            // Call cancel function with error handling
            try {
                $cancelChallans = new GenerateSingleChallanController();
                $cancelChallanResponse = $cancelChallans->cancelChallans($challanRequest);
                // dd($cancelChallanResponse);
            } catch (Exception $challanException) {
                throw new Exception('Challan cancellation failed: ' . $challanException->getMessage());
            }

            // Check if challan cancellation was successful
            $responseData = $cancelChallanResponse->getData(true);

            if (!$responseData || !isset($responseData['success']) || !$responseData['success']) {
                $errorMessage = isset($responseData['message']) ? $responseData['message'] : 'Failed to cancel challan';
                throw new Exception($errorMessage);
            }
        }
    }

    public function rejectNewApplicant(Request $request, $enrollmentNo)
    {
        $aaiApiId = DB::table('admission_apply_info')
                ->where('aai_id', $enrollmentNo)
                ->value('aai_api_id');

        $seatReservationMapDisplay = $request->input('seat_reservation_map_display') == '1' ? 'Yes' : 'No';
        $sessionId = $request->input('session_id');

        // Validate session ID is provided
        if (!$sessionId) {
            return response()->json(['success' => false, 'message' => 'Session ID is required']);
        }

        try {
            DB::beginTransaction();

            // Cancel challan using common function
            $this->cancelHostelChallan($enrollmentNo, $sessionId, 'new');

            // Update the applicant status to empty string (remove hostel allowance)
            $query = "UPDATE admission_personal_info SET api_allow_hostel = '' WHERE api_id = :aaiApiId";
            DB::update($query, ['aaiApiId' => $aaiApiId]);

            // Use session ID from request instead of getting from database
            $userId = strtolower(auth()->user()->id);

            // Update or insert hostel assignment record
            DB::table('hostel_assign')
                ->where('ha_aai_id', $enrollmentNo)
                ->where('ha_session', $sessionId)
                ->delete();

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Applicant rejected successfully']);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to reject applicant: ' . $e->getMessage());

            return response()->json(['success' => false, 'message' => 'Error rejecting applicant: ' . $e->getMessage()]);
        }
    }

    public function rejectOldApplicant(Request $request, $enrollmentNo)
    {
        $seatReservationMapDisplay = $request->input('seat_reservation_map_display') == '1' ? 'Yes' : 'No';
        $sessionId = $request->input('session_id');

        // Validate session ID is provided
        if (!$sessionId) {
            return response()->json(['success' => false, 'message' => 'Session ID is required']);
        }

        try {
            DB::beginTransaction();

            // Cancel challan using common function
            $this->cancelHostelChallan($enrollmentNo, $sessionId, 'old');

            // Update the applicant status to empty string (remove hostel allowance)
            $query = "UPDATE hostel_applicants SET happ_allowed_status = '' WHERE LOWER(happ_enrollment_no) = LOWER(:enrollmentNo)";
            DB::update($query, ['enrollmentNo' => $enrollmentNo]);

            // Use session ID from request instead of getting from database
            $userId = strtolower(auth()->user()->id);

            // Update or insert hostel assignment record
            DB::table('hostel_assign')
                ->where('ha_enrolment_no', $enrollmentNo)
                ->where('ha_session', $sessionId)
                ->delete();

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Hostel applicant rejected successfully']);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to reject hostel applicant: ' . $e->getMessage());

            return response()->json(['success' => false, 'message' => 'Error rejecting applicant: ' . $e->getMessage()]);
        }
    }

public function vacateHostel(){
    $username = strtolower(auth()->user()->username);
    $hostel_type = DB::table('HOSTEL_TYPE')
    ->where('HT_STATUS', 'E')
    ->get();
    $floors_de = DB::table('HOSTEL_FLOOR')
            ->where('HF_STATUS', 'E')
            ->get();
          // Fetch all rooms
          $data = DB::table('hostel_rooms')
        //   ->where('HR_HT_ID',$condition)
          ->get();
        //   dd($data);
          // Fetch all beds
          $beds = DB::table('hostel_beds')->get();
        //   dd($beds);
          // Add beds to the corresponding rooms
          foreach ($data as $room) {
            $room->beds = $beds->where('hb_room_id', $room->hr_id)->values();
        }

        $rooms = json_decode($data , true);
        $floors =   json_decode($floors_de , true);
    if (json_last_error() === JSON_ERROR_NONE) {
        $rooms ;
    } else {
        // Handle JSON decode error
        return response()->json(['error' => 'Invalid JSON data'], 400);
    }
    // dd($beds);
    //dd( $room);
    return view('Hostel.vacate_bed',
    compact('floors_de',
     'rooms',
     'hostel_type',


    ));
}


public function addRoom(Request $request)
{
    $request->validate([
        'room_number' => 'required|string|max:255',
        'room_capacity' => 'required|integer|min:1',
        'hostel_type' => 'required|integer|exists:hostel_type,ht_id',
        'floor' => 'required|integer|exists:hostel_floor,hf_id'
    ]);

    try {
        DB::beginTransaction();

        if ($request->filled('room_id')) {
            // UPDATE existing room
            $roomId = $request->input('room_id');

            DB::table('HOSTEL_ROOMS')
                ->where('HR_ID', $roomId)
                ->update([
                    'HR_HT_ID' => $request->input('hostel_type'),
                    'HR_ROOM_NO' => $request->input('room_number'),
                    'HR_CAPACITY' => $request->input('room_capacity'),
                    'HR_HF_ID' => $request->input('floor'),
                    'HR_AC_AVAILABILITY' => strtoupper($request->input('ac_availability')),
                    'HR_HEATER_AVAILABILITY' => strtoupper($request->input('heater_availability')),
                    'HR_REFRIGERATOR_AVAILABILITY' => strtoupper($request->input('refrigerator_availability')),
                ]);

            // Adjust Beds
            $currentBedCount = DB::table('HOSTEL_BEDS')
                ->where('HB_ROOM_ID', $roomId)
                ->count();

            $newCapacity = (int) $request->input('room_capacity');

            if ($newCapacity > $currentBedCount) {
                // Add extra beds
                for ($i = $currentBedCount + 1; $i <= $newCapacity; $i++) {
                    DB::table('HOSTEL_BEDS')->insert([
                        'HB_BED_NUMBER' => $i,
                        'HB_IS_OCCUPIED' => 'N',
                        'HB_ROOM_ID' => $roomId,
                    ]);
                }
            } elseif ($newCapacity < $currentBedCount) {
                // Check if any of the extra beds are occupied
                $occupiedCount = DB::table('HOSTEL_BEDS')
                    ->where('HB_ROOM_ID', $roomId)
                    ->where('HB_BED_NUMBER', '>', $newCapacity)
                    ->where('HB_IS_OCCUPIED', 'Y')
                    ->count();

                if ($occupiedCount > 0) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Room capacity cannot be reduced. Some of the extra beds are already occupied.'
                    ]);
                }

                // Delete unoccupied extra beds
                DB::table('HOSTEL_BEDS')
                    ->where('HB_ROOM_ID', $roomId)
                    ->where('HB_BED_NUMBER', '>', $newCapacity)
                    ->where('HB_IS_OCCUPIED', 'N')
                    ->delete();
            }
        } else {
            // INSERT new room
            $roomId = DB::table('HOSTEL_ROOMS')->insertGetId([
                'HR_HT_ID' => $request->input('hostel_type'),
                'HR_ROOM_NO' => $request->input('room_number'),
                'HR_CAPACITY' => $request->input('room_capacity'),
                'HR_HF_ID' => $request->input('floor'),
                'HR_AC_AVAILABILITY' => strtoupper($request->input('ac_availability')),
                'HR_HEATER_AVAILABILITY' => strtoupper($request->input('heater_availability')),
                'HR_REFRIGERATOR_AVAILABILITY' => strtoupper($request->input('refrigerator_availability')),
                'HR_STATUS' => 'E',
                'HR_ENTRY_USER_ID' => auth()->id(),
                'HR_ENTRY_DATE_TIME' => now(),
            ], 'HR_ID');


            // Add beds
            for ($i = 1; $i <= $request->input('room_capacity'); $i++) {
                DB::table('HOSTEL_BEDS')->insert([
                    'HB_BED_NUMBER' => $i,
                    'HB_IS_OCCUPIED' => 'N',
                    'HB_ROOM_ID' => $roomId,
                ]);
            }
        }

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => $request->filled('room_id')
                ? 'Room updated successfully!'
                : 'Room and beds added successfully!'
        ]);
    } catch (Exception $e) {
        DB::rollBack();

        return response()->json([
            'success' => false,
            'message' => 'An error occurred. Please try again.',
            'error' => $e->getMessage()
        ], 500);
    }
}






public function vacateBedStatus(Request $request)
{
    $username = strtolower(auth()->user()->employee_id);
    $bedId = $request->input('id');
    $isOccupied = $request->input('is_occupied'); // Should be 'N' for vacating

    // Update the bed status in the database
    DB::table('HOSTEL_BEDS')
        ->where('hb_id', $bedId)
        ->update(['hb_is_occupied' => $isOccupied]);

        $updated1 = DB::table('hostel_assign')
        ->where('ha_hb_id', $bedId)
        ->update(['ha_hb_id' => null,
        'ha_entry_user_id' => $username]);

    return response()->json(['message' => 'Bed status updated successfully']);
}
    // Function to enable the room
    public function enableRoom($id)
    {
        $status= DB::table('hostel_rooms')
        ->where('hr_id',$id)
        ->update(['hr_status' => 'E']);
        // dd($status);
        if($status){
            return response()->json(['success' => 'Room enabled successfully']);
        }else
        return response()->json(['error' => 'not updated'], 404);
    }

    // Function to disable the room
    public function disableRoom($id)
    {

       $status= DB::table('hostel_rooms')
        ->where('hr_id',$id)
        ->update(['hr_status' => 'D']);
        // dd($status);
        if($status){
            return response()->json(['success' => 'Room enabled successfully']);
        }else
        return response()->json(['error' => 'not updated'], 404);
    }


    // Function to enable the room
    public function enableAC($id)
    {
        $status= DB::table('hostel_rooms')
        ->where('hr_id',$id)
        ->update(['hr_ac_availability' => 'E']);
        // dd($status);
        if($status){
            return response()->json(['success' => 'Room enabled successfully']);
        }else
        return response()->json(['error' => 'not updated'], 404);
    }

    // Function to disable the room
    public function disableAC($id)
    {

       $status= DB::table('hostel_rooms')
        ->where('hr_id',$id)
        ->update(['hr_ac_availability' => 'D']);
        // dd($status);
        if($status){
            return response()->json(['success' => 'Room enabled successfully']);
        }else
        return response()->json(['error' => 'not updated'], 404);
    }





public function showFacilities(){

     $sessions = DB::table('academic_sessions')
     ->select('SESS_ID', 'SESS_NAME')
     ->orderBy('SESS_ID', 'desc')
     ->get();
     $hostelNames = DB::table('hostel_facilities')
     ->select('HF_NAME')
     ->distinct()
     ->get();



    $facilities = DB::table('HOSTEL_FACILITIES')->get();

    return view('Hostel.hostel_facilities',compact('sessions', 'facilities','hostelNames'));
}

public function submitFacilities(Request $request)
{
    $validated = $request->validate([
        'facility_name' => 'required|string|max:255',
        'facility_dues' => 'required|numeric',
        'session' => 'required|string|max:100',
        'status' => 'required|string|in:enabled,disabled',
    ]);

    // Insert the facility into the database
    DB::table('HOSTEL_FACILITIES')->insert([
        'HF_NAME' => $validated['facility_name'],
        'HF_COST' => $validated['facility_dues'],
        'HF_SESSION' => $validated['session'],
        'HF_STATUS' => $validated['status'],
        'HF_ENTRYDATETIME' => now(),
        'HF_ENTRY_USER_ID' => auth()->id(), // Assuming you have user authentication
    ]);

    return redirect()->back()->with('success', 'Facility added successfully.');
}
public function editFacility(Request $request)
{
    $validated = $request->validate([
        'facility_name' => 'required|string|max:255',
        'facility_dues' => 'required|numeric',
        'session' => 'required|string|max:100',
        'status' => 'required|string|max:50',
    ]);

    DB::table('HOSTEL_FACILITIES')
        ->where('HF_ID', $request->facility_id)
        ->update([
            'HF_NAME' => $validated['facility_name'],
            'HF_COST' => $validated['facility_dues'],
            'HF_SESSION' => $validated['session'],
            'HF_STATUS' => $validated['status'],
        ]);

    return redirect()->back()->with('success', 'Facility updated successfully.');
}

public function enableFacility($id)
{
    DB::table('HOSTEL_FACILITIES')
        ->where('HF_ID', $id)
        ->update(['HF_STATUS' => 'enabled']);

    return redirect()->back()->with('success', 'Facility enabled successfully.');
}

public function disableFacility($id)
{
    DB::table('HOSTEL_FACILITIES')
        ->where('HF_ID', $id)
        ->update(['HF_STATUS' => 'disabled']);

    return redirect()->back()->with('success', 'Facility disabled successfully.');
}

public function hostel_form_visibility()
{
    $session = DB::table('academic_sessions')
        ->select('sess_id', 'sess_name')
        ->orderBy('sess_id', 'desc')
        ->get();

    $data = DB::table('hostel_form_visibility_periods')
        ->select(
            'hostel_form_visibility_periods.id',
            'hostel_form_visibility_periods.start_date',
            'hostel_form_visibility_periods.end_date',
            'hostel_form_visibility_periods.sess_id',
            'hostel_form_visibility_periods.visibility_type',
            'hostel_form_visibility_periods.due_date',
            'hostel_form_visibility_periods.valid_date',
            'hostel_form_visibility_periods.entry_username',
            'hostel_form_visibility_periods.entry_datetime',
            'hostel_form_visibility_periods.updated_by',
            'hostel_form_visibility_periods.updated_at',
            'hostel_form_visibility_periods.remarks',
            'academic_sessions.sess_name'
        )
        ->leftJoin('academic_sessions', 'hostel_form_visibility_periods.sess_id', '=', 'academic_sessions.sess_id')
        ->orderBy('sess_id', 'desc')
        ->get();

    return view('Hostel.hostel_form_visibility', compact('session', 'data'));
}

public function submit_hostel_form_visibility(Request $request)
{
    $request->validate([
        'start_date' => 'required|date',
        'end_date' => 'required|date|after_or_equal:start_date',
        'session' => 'required|numeric',
        'visibility_type' => 'required|in:admission_applicants,regular_students',
        'due_date' => 'required|date|after_or_equal:start_date',
        'valid_date' => 'required|date|after_or_equal:start_date',
        'remarks' => 'nullable|string|max:500',
    ]);

    try {
        DB::beginTransaction();

        $data = [
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'sess_id' => $request->session,
            'visibility_type' => $request->visibility_type,
            'due_date' => $request->due_date,
            'valid_date' => $request->valid_date,
            'remarks' => $request->remarks,
        ];

        if ($request->filled('id')) {
            // Update existing record
            $data['updated_by'] = strtolower(auth()->user()->username);
            $data['updated_at'] = now();

            DB::table('hostel_form_visibility_periods')
                ->where('id', $request->id)
                ->update($data);

            $message = 'Hostel form visibility period updated successfully.';
        } else {
            // Insert new record
            $data['entry_username'] = strtolower(auth()->user()->username);
            $data['entry_datetime'] = now();

            DB::table('hostel_form_visibility_periods')->insert($data);
            $message = 'Hostel form visibility period added successfully.';
        }

        DB::commit();
        return redirect()->route('hostel-form-visibility')->with('success', $message);

    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('Error in submit_hostel_form_visibility: ' . $e->getMessage());
        return redirect()->back()
            ->withInput()
            ->with('error', 'An error occurred. Please try again.');
    }
}

public function delete_hostel_form_visibility(Request $request)
{
    try {
        DB::beginTransaction();

        $deleted = DB::table('hostel_form_visibility_periods')
            ->where('id', $request->id)
            ->delete();

        DB::commit();

        if ($deleted) {
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'Record not found.']);

    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('Error in delete_hostel_form_visibility: ' . $e->getMessage());
        return response()->json(['success' => false, 'message' => 'An error occurred while deleting the record.']);
    }
}



public function showBulkAssignForm()
{
    return view('Hostel.hostel_bulk_assign');
}

public function bulkAssign(Request $request)
{
    $request->validate([
        'excel_file' => 'required|file|mimes:xlsx,xls,csv'
    ]);

    try {
        $file = $request->file('excel_file');
        $allRecords = [];
        $results = [];
        $chunkSize = 20;

        // First collect all valid records
        if ($file->getClientOriginalExtension() == 'csv') {
            $handle = fopen($file->getPathname(), 'r');
            fgetcsv($handle); // Skip header row

            while (($row = fgetcsv($handle)) !== false) {
                if (!empty($row[0])) {
                    $allRecords[] = [
                        'enrollment_no' => trim($row[0]),
                        'room_no' => trim($row[1]),
                        'bed_no' => trim($row[2])
                    ];
                }
            }
            fclose($handle);
        } else {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getPathname());
            $worksheet = $spreadsheet->getActiveSheet();
            $highestRow = $worksheet->getHighestRow();

            for ($row = 2; $row <= $highestRow; $row++) {
                $enrollmentNo = trim($worksheet->getCell('A' . $row)->getValue());
                if (!empty($enrollmentNo)) {
                    $allRecords[] = [
                        'enrollment_no' => $enrollmentNo,
                        'room_no' => trim($worksheet->getCell('B' . $row)->getValue()),
                        'bed_no' => trim($worksheet->getCell('C' . $row)->getValue())
                    ];
                }
            }
        }

        // Process records in chunks
        $chunks = array_chunk($allRecords, $chunkSize);
        $processedCount = 0;

        foreach ($chunks as $chunk) {
            DB::beginTransaction();
            try {
                foreach ($chunk as $record) {
                    $result = $this->processHostelAssignment(
                        $record['bed_no'],
                        $record['enrollment_no'],
                        $record['room_no']
                    );

                    $results[] = [
                        'enrollment_no' => $record['enrollment_no'],
                        'room_no' => $record['room_no'],
                        'bed_no' => $record['bed_no'],
                        'status' => $result ? 'success' : 'error',
                        'message' => $result ? 'Successfully assigned' : 'Failed to assign'
                    ];

                    if ($result) {
                        $processedCount++;
                    }
                }
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                \Log::error('Chunk processing error: ' . $e->getMessage());

                // Add error results for this chunk
                foreach ($chunk as $record) {
                    $results[] = [
                        'enrollment_no' => $record['enrollment_no'],
                        'room_no' => $record['room_no'],
                        'bed_no' => $record['bed_no'],
                        'status' => 'error',
                        'message' => 'Chunk processing failed: ' . $e->getMessage()
                    ];
                }
            }

            // Add a small delay between chunks to prevent overwhelming the database
            usleep(100000); // 100ms delay
        }

        $message = "Processed {$processedCount} out of " . count($allRecords) . " records successfully";
        return redirect()->back()
            ->with('success', $message)
            ->with('results', $results);

    } catch (\Exception $e) {
        return redirect()->back()
            ->with('error', 'Error processing file: ' . $e->getMessage());
    }
}

private function processHostelAssignment($bedNo, $enrollmentNo, $roomNo)
{
    try {
        // Convert parameters to appropriate types
        $bedNo = (int)$bedNo;
        $roomNo = (int)$roomNo;

        $procedureName = 'ASSIGN_HOSTEL_Procedure';
        $bindings = [
            'p_enrollment_no' => (string)$enrollmentNo,
            'p_room_no' => $roomNo,
            'p_bed_no' => $bedNo,
            'p_hostel_category' => 2,
            'p_session' => '163', // Changed to string as it was in original procedure
        ];

        $result = DB::executeProcedure($procedureName, $bindings);

        // Log successful execution
        \Log::info("Hostel Assignment Success for enrollment: {$enrollmentNo}, room: {$roomNo}, bed: {$bedNo}");

        return true;
    } catch (\Exception $e) {
        // Log the error with detailed information
        \Log::error("Hostel Assignment Error for enrollment: {$enrollmentNo}, room: {$roomNo}, bed: {$bedNo}. Error: " . $e->getMessage());
        return false;
    }
}

public function validateFile(Request $request)
{
    try {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls,csv'
        ]);

        $file = $request->file('excel_file');
        $records = [];

        if ($file->getClientOriginalExtension() == 'csv') {
            $handle = fopen($file->getPathname(), 'r');
            fgetcsv($handle); // Skip header

            while (($row = fgetcsv($handle)) !== false) {
                if (!empty($row[0])) {
                    $records[] = [
                        'enrollment_no' => trim($row[0]),
                        'room_no' => trim($row[1]),
                        'bed_no' => trim($row[2])
                    ];
                }
            }
            fclose($handle);
        } else {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getPathname());
            $worksheet = $spreadsheet->getActiveSheet();
            $highestRow = $worksheet->getHighestRow();

            for ($row = 2; $row <= $highestRow; $row++) {
                $enrollmentNo = trim($worksheet->getCell('A' . $row)->getValue());
                if (!empty($enrollmentNo)) {
                    $records[] = [
                        'enrollment_no' => $enrollmentNo,
                        'room_no' => trim($worksheet->getCell('B' . $row)->getValue()),
                        'bed_no' => trim($worksheet->getCell('C' . $row)->getValue())
                    ];
                }
            }
        }

        if (empty($records)) {
            return response()->json([
                'valid' => false,
                'message' => 'No valid records found in file'
            ]);
        }

        // Store records in cache for processing
        $fileId = uniqid('hostel_', true);
        Cache::put($fileId, [
            'records' => $records,
            'processed' => 0
        ], now()->addHours(1));

        return response()->json([
            'valid' => true,
            'totalRecords' => count($records),
            'fileId' => $fileId
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'valid' => false,
            'message' => 'Error processing file: ' . $e->getMessage()
        ], 422);
    }
}

public function processChunk(Request $request)
{
    try {
        $fileId = $request->input('fileId');
        $cacheData = Cache::get($fileId);

        if (!$cacheData) {
            return response()->json([
                'error' => 'File data expired or not found'
            ], 404);
        }

        $records = $cacheData['records'];
        $processed = $cacheData['processed'];
        $chunkSize = 20;
        $results = [];
        $successCount = 0;
        $failedCount = 0;

        // Get next chunk of records
        $chunk = array_slice($records, $processed, $chunkSize);

        if (empty($chunk)) {
            Cache::forget($fileId);
            return response()->json([
                'completed' => true,
                'processed' => 0,
                'results' => [],
                'summary' => [
                    'success' => $successCount,
                    'failed' => $failedCount
                ]
            ]);
        }

        DB::beginTransaction();
        try {
            foreach ($chunk as $record) {
                $result = $this->processHostelAssignment(
                    $record['bed_no'],
                    $record['enrollment_no'],
                    $record['room_no']
                );

                if ($result) {
                    $successCount++;
                } else {
                    $failedCount++;
                }

                $results[] = [
                    'enrollment_no' => $record['enrollment_no'],
                    'room_no' => $record['room_no'],
                    'bed_no' => $record['bed_no'],
                    'status' => $result ? 'success' : 'error',
                    'message' => $result ? 'Successfully assigned' : 'Failed to assign'
                ];
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            foreach ($chunk as $record) {
                $results[] = [
                    'enrollment_no' => $record['enrollment_no'],
                    'room_no' => $record['room_no'],
                    'bed_no' => $record['bed_no'],
                    'status' => 'error',
                    'message' => 'Error: ' . $e->getMessage()
                ];
                $failedCount++;
            }
        }

        // Update processed count in cache
        $newProcessed = $processed + count($chunk);
        Cache::put($fileId, [
            'records' => $records,
            'processed' => $newProcessed
        ], now()->addHours(1));

        return response()->json([
            'completed' => $newProcessed >= count($records),
            'processed' => count($chunk),
            'results' => $results,
            'summary' => [
                'success' => $successCount,
                'failed' => $failedCount
            ]
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Error processing chunk: ' . $e->getMessage()
        ], 500);
    }
}


public function hostelUpdateCheckinAndCheckoutDate(){
    $academicYear = DB::table('academic_sessions')
    ->select('SESS_ID', 'SESS_NAME')
    ->orderBy('SESS_ID', 'desc')
    ->get();

    return view('Hostel.hostel_update_checkin_and_checkout_date', compact('academicYear'));
}
public function showCheckinCheckout()
{
    if(request()->ajax()) {
        $records = DB::table('HOSTEL_ALLOCATION_HISTORY as h')
            ->leftJoin('academic_sessions as a', 'h.HAH_SESSION_ID', '=', 'a.sess_id')
            ->select(
                'h.ID',
                'h.HAH_SESSION_ID as session_id',
                'h.HAH_STUDENT_ENROLL_NO as student_enroll_no',
                'h.HAH_APP_ID as app_id',
                'h.HAH_CHECKIN_DATE as checkin_date',
                'h.HAH_CHECKOUT_DATE as checkout_date',
                'h.HAH_ENTRY_USERNAME as entry_username',
                'h.HAH_ENTRY_DATETIME as entry_datetime',
                'a.sess_name'
            )
            ->orderBy('h.ID', 'desc')
            ->get();

        return response()->json(['data' => $records]);
    }

    $academicYear = DB::table('academic_sessions')
        ->select('sess_id', 'sess_name')
        ->orderBy('sess_id', 'desc')
        ->get();

    return view('Hostel.hostel_update_checkin_and_checkout_date', compact('academicYear'));
}

public function saveCheckinCheckout(Request $request)
{
    try {
        $validator = Validator::make($request->all(), [
            'session_id' => 'required|numeric',
            'student_enroll_no' => 'required|string|max:30',
            'checkin_date' => 'required|date',
            'checkout_date' => 'nullable|date|after:checkin_date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Check for duplicate entry
        $duplicateCheck = DB::table('HOSTEL_ALLOCATION_HISTORY')
            ->where('HAH_SESSION_ID', $request->session_id)
            ->where(function($query) use ($request) {
                $query->where('HAH_STUDENT_ENROLL_NO', $request->student_enroll_no)
                    ->orWhere('HAH_APP_ID', $request->app_id);
            });

        // If updating, exclude current record from duplicate check
        if($request->id) {
            $duplicateCheck->where('ID', '!=', $request->id);
        }

        $duplicate = $duplicateCheck->first();

        if($duplicate) {
            return response()->json([
                'success' => false,
                'message' => 'A record already exists for this student in the selected session.',
                'duplicate_record' => $duplicate
            ], 422);
        }

        $data = [
            'HAH_SESSION_ID' => $request->session_id,
            'HAH_STUDENT_ENROLL_NO' => $request->student_enroll_no,
            'HAH_APP_ID' => $request->app_id,
            'HAH_CHECKIN_DATE' => $request->checkin_date,
            'HAH_CHECKOUT_DATE' => $request->checkout_date,
            'HAH_ENTRY_USERNAME' => auth()->user()->username,
            'HAH_ENTRY_DATETIME' => DB::raw('SYSDATE'),
            'HAH_ENTRY_TIMESTAMP' => DB::raw('SYSTIMESTAMP')
        ];

        if($request->id) {
            // Update
            DB::table('HOSTEL_ALLOCATION_HISTORY')
                ->where('ID', $request->id)
                ->update($data);
            $message = 'Record updated successfully';
        } else {
            // Insert
            DB::table('HOSTEL_ALLOCATION_HISTORY')->insert($data);
            $message = 'Record added successfully';
        }

        return response()->json([
            'success' => true,
            'message' => $message
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'An error occurred while saving the record',
            'error' => $e->getMessage()
        ], 500);
    }
}

public function deleteCheckinCheckout($id)
{
    try {
        DB::table('HOSTEL_ALLOCATION_HISTORY')
            ->where('ID', $id)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'Record deleted successfully'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'An error occurred while deleting the record',
            'error' => $e->getMessage()
        ], 500);
    }
}







}
