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
use App\Services\Hostel\HostelSeatStatsService;

class HostelController
{
    public function showHostelApplicants()
    {
        $groupId = auth()->user()->group_id;
        $session = DB::table('academic_sessions')
            ->select('SESS_ID', 'SESS_NAME')
            ->orderBy('SESS_ID', 'desc')
            ->get();


        return view('Hostel.hostel_applicants', [
            'session' => $session
        ]);
    }


    public function showHostelStudents()
    {
        $groupId = auth()->user()->group_id;
        $session = DB::table('academic_sessions')
            ->select('SESS_ID', 'SESS_NAME')
            ->orderBy('SESS_ID', 'desc')
            ->get();



        return view('Hostel.hostel_students', [
            'session' => $session
        ]);
    }



    /**
     * ===================================================================================
     * HOSTEL APPLICANTS DATA PROCESSING FUNCTION  (public function getHostelApplicantsData)
     * ===================================================================================
     * 
     * ⚠️  CRITICAL DOCUMENTATION - DO NOT DELETE OR MODIFY ⚠️
     * This comment block contains essential business logic documentation.
     * Deleting this will break the understanding of complex hostel management system.
     * 
     * PURPOSE: This function fetches and processes hostel applicant data with complex
     * filtering, seat availability calculations, and payment status tracking.
     * 
     * COMPLETE PROCESS FLOW:
     * 
     * 1. INPUT VALIDATION & FILTERING:
     *    - Validates session_id, admission_schedule_id, and hostel_type
     *    - Handles external hostel type separately (redirects to getExternalHostelApplicantsData)
     *    - Builds dynamic WHERE clauses based on filters
     * 
     * 2. SEAT AVAILABILITY CALCULATION:
     *    - Calls getSeatCounts() to get available male/female hostel beds
     *    - Uses hostel_beds, hostel_rooms, and hostel_floor tables
     *    - Filters by hostel type (male=1, female=2) and occupancy status
     * 
     * 3. COMPLEX SQL QUERY EXECUTION:
     *    - Executes a complex CTE (Common Table Expression) query
     *    - Joins multiple tables: admission_apply_info, admission_personal_info, 
     *      hostel_assign, admission_schedules, student_challan
     *    - Includes scholarship details and last degree marks
     *    - Filters by session, admission schedule, and hostel type
     * 
     * 4. DATA PROCESSING & STATISTICS:
     *    - Filters applicants by gender (Male/Female)
     *    - Creates "currently allowed" group (excluding paid applicants)
     *    - Calculates various counts:
     *      * Allowed Count: Currently allowed applicants (excluding paid)
     *      * Consent Count: Applicants who gave consent but haven't paid
     *      * Paid Count: Always 0 (since paid are excluded from stats)
     *      * Vacate Count: Applicants who can be vacated (unpaid or no challan)
     * 
     * 5. RESPONSE FORMATTING:
     *    - Returns JSON response with all data
     *    - Includes applicant arrays, counts, and seat availability
     *    - Used by frontend for displaying tables and statistics
     * 
     * KEY LOGIC:
     * - Paid applicants are excluded from "allowed" count for seat calculation
     * - All statistics are calculated only from currently allowed applicants
     * - This prevents confusion between overall stats and current active stats
     * 
     * EXAMPLE SCENARIO:
     * - 100 total allowed applicants
     * - 20 of them paid (excluded from stats)
     * - 80 currently allowed (shown in stats)
     * - From 80: 50 gave consent, 30 can be vacated
     * 
     * 🔒 PROTECTED DOCUMENTATION - REQUIRED FOR SYSTEM UNDERSTANDING 🔒
     * ===================================================================================
     */
    public function getHostelApplicantsData(Request $request)
    {
        // STEP 1: Extract and validate input parameters
        $sessionId = $request->input('session_id');
        $admission_schedule_id = $request->input('admission_schedule_id');
        $hostel_type = $request->input('hostel_type');

        // Dynamic where clause for admission schedule round
        $where = '';
        $where = $admission_schedule_id ? ' admission_schedules.admission_sch_round = ' . $admission_schedule_id : '';

        // Check if external hostel type is selected
        if ($hostel_type === 'regular_outside') {
            return $this->getExternalHostelApplicantsData($request);
        }

        // Dynamic where clause for hostel type (for regular/university hostel)
        $hostelTypeWhere = '';
        if ($hostel_type === 'regular') {
            $hostelTypeWhere = " AND EXISTS (
                SELECT 1 FROM student_challan sc 
                WHERE sc.SC_APP_ID = aa.AAI_ID 
                AND sc.SC_SESSION_ID = :p_session_id 
                AND sc.SC_CHALLAN_TYPE = 'Hostel_Fee'
                AND sc.SC_SESSION_TYPE = :p_hostel_type
            )";
        }

        // Session-wise seat stats (shared with summary dashboard)
        $seatStats = app(HostelSeatStatsService::class);
        $maleSeatSummary = $seatStats->getGenderSeatSummary($sessionId, 'male', 'applicant');
        $femaleSeatSummary = $seatStats->getGenderSeatSummary($sessionId, 'female', 'applicant');
        $male_vacant_seats = $maleSeatSummary['vacant_seats'];
        $female_vacant_seats = $femaleSeatSummary['vacant_seats'];
        $male_available_adjusted = $maleSeatSummary['available_seats'];
        $female_available_adjusted = $femaleSeatSummary['available_seats'];
        $malePipelineAllowed = $maleSeatSummary['allowed_count'];
        $femalePipelineAllowed = $femaleSeatSummary['allowed_count'];

        // Base SQL query
        $baseQuery = "
            WITH {$this->hostelApplicantsLookupCtesSql()}
            SELECT 
                CASE 
                    WHEN ap.API_ALLOW_HOSTEL IS NULL THEN 'Not Allowed' 
                    WHEN ap.API_ALLOW_HOSTEL = 'Rejected' THEN 'Rejected'
                    ELSE 'Allowed' 
                END AS ALLOWED_STATUS,
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
                ha.ha_hostel_location_preference AS hostel_location_preference,
                hc.sc_challan_status,
                fs.full_semester_challan_status,
                sch.scholarship_details,
                CASE
                    WHEN aq.aq_obtained_marks IS NULL THEN
                        aq.aq_degree_name || ': ' || (
                            SELECT aqd.ADQ_EQUAL_OBTAIN_MARKS
                            FROM admission_qualification_detail aqd
                            WHERE aqd.aqd_api_id = aq.aq_api_id
                              AND aqd.aqd_degree_name = aq.aq_degree_name
                              AND ROWNUM = 1
                        )
                    ELSE aq.aq_degree_name || ': ' || TO_CHAR(aq.aq_obtained_marks)
                END AS last_degree_marks,
                ht.ht_name AS hostel_name,
                hf.hf_name AS floor_name,
                hr.hr_room_no AS room_no,
                hb.hb_bed_number AS bed_no
            FROM admission_apply_info aa
            JOIN admission_personal_info ap 
              ON aa.AAI_API_ID = ap.API_ID
            LEFT JOIN hostel_challan hc
              ON hc.sc_app_id = aa.AAI_ID
            LEFT JOIN fs_challan fs
              ON fs.sc_app_id = aa.AAI_ID
            LEFT JOIN scholarships sch
              ON sch.fsd_aai_id = aa.AAI_ID
            LEFT JOIN last_qual lq
              ON lq.aq_api_id = aa.AAI_API_ID
            LEFT JOIN admission_qualification aq
              ON aq.aq_id = lq.max_aq_id
            LEFT JOIN hostel_assign ha 
              ON aa.aai_id = ha.ha_aai_id
             AND ha.ha_session = :p_session_id
            LEFT JOIN hostel_beds hb
              ON ha.ha_hb_id = hb.hb_id
            LEFT JOIN hostel_rooms hr
              ON hb.hb_room_id = hr.hr_id
            LEFT JOIN hostel_floor hf
              ON hr.hr_hf_id = hf.hf_id
            LEFT JOIN HOSTEL_TYPE ht
              ON hr.hr_ht_id = ht.ht_id
            LEFT JOIN admission_schedules ads 
              ON aa.aai_admission_sch_id = ads.ADMISSION_SCH_ID

            WHERE aa.AAI_STATUS = 'Selected'
              AND ap.API_ISHOSTEL = 'Yes'
              AND aa.AAI_SDPO_SESS_ID = :p_session_id";

        // Add admission schedule filter if needed
        if (!empty($where)) {
            $baseQuery .= " AND ads.admission_sch_round = :p_admission_schedule_id";
        }

        // Add hostel type filter if needed
        if (!empty($hostelTypeWhere)) {
            $baseQuery .= $hostelTypeWhere;
        }

        // Add ordering
        $baseQuery .= " ORDER BY CASE WHEN LOWER(ap.API_PERMANENTCITY) = 'lahore' THEN 1 ELSE 0 END, aa.aai_apply_date";

        // Prepare parameters
        $params = ['p_session_id' => $sessionId];
        if (!empty($where)) {
            $params['p_admission_schedule_id'] = $admission_schedule_id;
        }
        if (!empty($hostelTypeWhere)) {
            $params['p_hostel_type'] = $hostel_type;
        }

        // Execute query
        $query = DB::select($baseQuery, $params);

        // Convert to collection for easier processing
        $applicants = collect($query);

        // STEP 1: Filter applicants by gender (Male and Female)
        $maleApplicants = $applicants->filter(fn($applicant) => strtoupper(trim($applicant->api_gender ?? '')) == 'MALE');
        $femaleApplicants = $applicants->filter(fn($applicant) => strtoupper(trim($applicant->api_gender ?? '')) == 'FEMALE');

        // STEP 2: Get currently allowed applicants (excluding paid ones completely from stats)
        // This creates a group of applicants who are allowed but haven't paid yet
        // Example: If 100 are allowed and 20 paid, this group will have 80 applicants
        // DEBUG: This step excludes paid applicants from all stats calculations
        $maleCurrentlyAllowed = $maleApplicants->filter(function ($applicant) {
            return $applicant->allowed_status == 'Allowed' && $applicant->sc_challan_status != 'PAID';
        });

        $femaleCurrentlyAllowed = $femaleApplicants->filter(function ($applicant) {
            return $applicant->allowed_status == 'Allowed' && $applicant->sc_challan_status != 'PAID';
        });

        // DEBUG: Log the filtering results for troubleshooting
        // \Log::info('Male Currently Allowed Count: ' . $maleCurrentlyAllowed->count());
        // \Log::info('Female Currently Allowed Count: ' . $femaleCurrentlyAllowed->count());

        // STEP 3: Calculate new simplified stats structure
        // Available Seats - Allowed = Remaining

        // STEP 3A: Count currently allowed applicants (excluding paid ones)
        // This count is used for seat availability calculation
        $maleAllowedCount = $maleCurrentlyAllowed->count();
        $femaleAllowedCount = $femaleCurrentlyAllowed->count();

        // STEP 3B: Remaining will be handled on frontend (Remaining = Available - Allowed)

        // STEP 3C: Legacy stats (keeping for backward compatibility but not used in new UI)
        $maleChallanCount = $maleCurrentlyAllowed->filter(function ($applicant) {
            return !empty($applicant->sc_challan_status) && $applicant->sc_challan_status != 'PAID';
        })->count();

        $femaleChallanCount = $femaleCurrentlyAllowed->filter(function ($applicant) {
            return !empty($applicant->sc_challan_status) && $applicant->sc_challan_status != 'PAID';
        })->count();

        $malePaidCount = $maleCurrentlyAllowed->filter(function ($applicant) {
            return $applicant->sc_challan_status == 'PAID';
        })->count();

        $femalePaidCount = $femaleCurrentlyAllowed->filter(function ($applicant) {
            return $applicant->sc_challan_status == 'PAID';
        })->count();

        $maleVacateCount = $maleCurrentlyAllowed->filter(function ($applicant) {
            return $applicant->sc_challan_status != 'PAID' || empty($applicant->sc_challan_status);
        })->count();

        $femaleVacateCount = $femaleCurrentlyAllowed->filter(function ($applicant) {
            return $applicant->sc_challan_status != 'PAID' || empty($applicant->sc_challan_status);
        })->count();

        // Return response with new simplified stats structure
        return response()->json([
            'maleApplicants' => $maleApplicants->toArray(),
            'femaleApplicants' => $femaleApplicants->toArray(),
            'maleCount' => $maleApplicants->count(),
            'femaleCount' => $femaleApplicants->count(),

            // NEW SIMPLIFIED STATS STRUCTURE
            // Controller provides Available Seats already adjusted by (paid but not assigned)
            'male_available_seats' => $male_available_adjusted,
            'female_available_seats' => $female_available_adjusted,
            'male_allowed_count' => $malePipelineAllowed,
            'female_allowed_count' => $femalePipelineAllowed,
            'male_remaining_seats' => max(0, $male_vacant_seats - $malePipelineAllowed),
            'female_remaining_seats' => max(0, $female_vacant_seats - $femalePipelineAllowed),
            // Frontend will compute remaining seats

            // LEGACY STATS (keeping for backward compatibility)
            'male_vacant_seats' => $male_vacant_seats,
            'female_vacant_seats' => $female_vacant_seats,
            'male_challan_count' => $maleChallanCount,
            'female_challan_count' => $femaleChallanCount,
            'male_paid_count' => $malePaidCount,
            'female_paid_count' => $femalePaidCount,
            'male_vacate_count' => $maleVacateCount,
            'female_vacate_count' => $femaleVacateCount,
            // Ensure total count matches the sum of observable gender categories to avoid confusion
            'new_count' => $maleApplicants->count() + $femaleApplicants->count(),
        ]);
    }

    /**
     * Search a selected applicant by AAI ID or enrollment for manual hostel allow/assign.
     * Does not require API_ISHOSTEL = 'Yes' (unlike the consent-based list).
     */
    public function searchHostelApplicant(Request $request)
    {
        $request->validate([
            'session_id' => 'required|integer',
            'search' => 'required|string|max:50',
        ]);

        $sessionId = (int) $request->input('session_id');
        $search = trim($request->input('search'));

        if ($search === '') {
            return response()->json(['success' => false, 'message' => 'Please enter Applicant ID or Enrollment No.']);
        }

        $applicant = DB::selectOne("
            SELECT
                CASE
                    WHEN ap.API_ALLOW_HOSTEL IS NULL THEN 'Not Allowed'
                    WHEN ap.API_ALLOW_HOSTEL = 'Rejected' THEN 'Rejected'
                    ELSE 'Allowed'
                END AS allowed_status,
                aa.aai_id,
                aa.AAI_SDPO_SESS_ID AS aai_sdpo_sess_id,
                aa.CMS_SAI_ENROLLMENT_NO,
                ap.API_NAME,
                ap.API_GENDER,
                ap.API_ISHOSTEL,
                CASE
                    WHEN ads.admission_sch_round = 1 THEN 'Early Round'
                    WHEN ads.admission_sch_round = 2 THEN 'Regular Round'
                    WHEN ads.admission_sch_round = 3 THEN 'Rolling Round'
                    ELSE 'Unknown'
                END AS admission_sch_round,
                ap.API_PERMANENTCITY,
                aa.aai_apply_date,
                ha.ha_disable_map_display,
                ha.ha_hostel_location_preference AS hostel_location_preference,
                (
                    SELECT MAX(scc.sc_challan_status)
                    FROM student_challan scc
                    WHERE scc.SC_SESSION_ID = :p_session_id
                      AND scc.SC_CHALLAN_TYPE = 'Hostel_Fee'
                      AND scc.SC_APP_ID = aa.AAI_ID
                      AND scc.SC_SESSION_TYPE != 'regular_outside'
                ) AS sc_challan_status,
                (
                    SELECT scc.challan_no
                    FROM student_challan scc
                    WHERE scc.id = (
                        SELECT MAX(scc2.id)
                        FROM student_challan scc2
                        WHERE scc2.SC_SESSION_ID = :p_session_id
                          AND scc2.SC_CHALLAN_TYPE = 'Hostel_Fee'
                          AND scc2.SC_APP_ID = aa.AAI_ID
                          AND scc2.SC_SESSION_TYPE != 'regular_outside'
                          AND NVL(scc2.sc_challan_status, 'X') != 'CANCELLED'
                          AND scc2.sc_challan_status = 'UNPAID'
                    )
                ) AS sc_challan_no,
                (
                    SELECT NVL(scc.sc_amount, scc.invoiceamount)
                    FROM student_challan scc
                    WHERE scc.id = (
                        SELECT MAX(scc2.id)
                        FROM student_challan scc2
                        WHERE scc2.SC_SESSION_ID = :p_session_id
                          AND scc2.SC_CHALLAN_TYPE = 'Hostel_Fee'
                          AND scc2.SC_APP_ID = aa.AAI_ID
                          AND scc2.SC_SESSION_TYPE != 'regular_outside'
                          AND NVL(scc2.sc_challan_status, 'X') != 'CANCELLED'
                          AND scc2.sc_challan_status = 'UNPAID'
                    )
                ) AS sc_challan_amount,
                (
                    SELECT scc.sc_scholarship_amount
                    FROM student_challan scc
                    WHERE scc.id = (
                        SELECT MAX(scc2.id)
                        FROM student_challan scc2
                        WHERE scc2.SC_SESSION_ID = :p_session_id
                          AND scc2.SC_CHALLAN_TYPE = 'Hostel_Fee'
                          AND scc2.SC_APP_ID = aa.AAI_ID
                          AND scc2.SC_SESSION_TYPE != 'regular_outside'
                          AND NVL(scc2.sc_challan_status, 'X') != 'CANCELLED'
                          AND scc2.sc_challan_status = 'UNPAID'
                    )
                ) AS sc_scholarship_amount,
                (
                    SELECT LISTAGG(
                               st.SCHOLARSHIP_NAME || ': ' ||
                               CASE
                                   WHEN fsd.FSD_DISCOUNT_PERCENTAGE >= 1 THEN
                                       TO_CHAR(ROUND(fsd.FSD_DISCOUNT_PERCENTAGE, 2)) || '%'
                                   ELSE
                                       TO_CHAR(ROUND(fsd.FSD_DISCOUNT_PERCENTAGE * 100, 2)) || '%'
                               END,
                               ', '
                           ) WITHIN GROUP (ORDER BY st.SCHOLARSHIP_NAME)
                    FROM fin_scholarship_discounts fsd
                    LEFT JOIN scholarship_type st
                           ON fsd.FSD_SCHOLARSHIP_TYPE = st.SCHOLARSHIP_ID
                    WHERE fsd.FSD_SESION_ID = :p_session_id
                      AND fsd.FSD_AAI_ID = aa.AAI_ID
                ) AS scholarship_details,
                ht.ht_name AS hostel_name,
                hf.hf_name AS floor_name,
                hr.hr_room_no AS room_no,
                hb.hb_bed_number AS bed_no
            FROM admission_apply_info aa
            JOIN admission_personal_info ap ON aa.AAI_API_ID = ap.API_ID
            LEFT JOIN hostel_assign ha
              ON aa.aai_id = ha.ha_aai_id
             AND ha.ha_session = :p_session_id
             AND ha.ha_status = 'E'
            LEFT JOIN hostel_beds hb ON ha.ha_hb_id = hb.hb_id
            LEFT JOIN hostel_rooms hr ON hb.hb_room_id = hr.hr_id
            LEFT JOIN hostel_floor hf ON hr.hr_hf_id = hf.hf_id
            LEFT JOIN HOSTEL_TYPE ht ON hr.hr_ht_id = ht.ht_id
            LEFT JOIN admission_schedules ads ON aa.aai_admission_sch_id = ads.ADMISSION_SCH_ID
            WHERE aa.AAI_STATUS = 'Selected'
              AND (
                    TO_CHAR(aa.aai_id) = :p_search_id
                 OR UPPER(TRIM(aa.CMS_SAI_ENROLLMENT_NO)) = UPPER(TRIM(:p_search_text))
              )
                 and aa.aai_sdpo_sess_id = :p_session_id
        ", [
            'p_session_id' => $sessionId,
            'p_search_id' => $search,
            'p_search_text' => $search,
        ]);

        if (!$applicant) {
            return response()->json([
                'success' => false,
                'message' => 'No selected applicant found with the given ID or enrollment.',
            ]);
        }

        // if ((int) ($applicant->aai_sdpo_sess_id ?? 0) !== $sessionId) {
        //     $actualSession = DB::table('academic_sessions')
        //         ->where('sess_id', $applicant->aai_sdpo_sess_id)
        //         ->value('sess_name');

        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Applicant belongs to session "' . ($actualSession ?: ('ID ' . $applicant->aai_sdpo_sess_id)) . '". Please select that academic session and search again.',
        //     ]);
        // }

        $gender = strtolower(trim($applicant->api_gender ?? ''));
        if (!in_array($gender, ['male', 'female'], true)) {
            return response()->json([
                'success' => false,
                'message' => 'Applicant gender is missing or invalid.',
            ]);
        }

        $ishostel = strtoupper(trim($applicant->api_ishostel ?? ''));
        $inConsentList = strcasecmp(trim($applicant->api_ishostel ?? ''), 'Yes') === 0
            || in_array($ishostel, ['Y', 'YES'], true);

        return response()->json([
            'success' => true,
            'applicant' => [
                'aai_id' => $applicant->aai_id,
                'cms_sai_enrollment_no' => $applicant->cms_sai_enrollment_no,
                'api_name' => $applicant->api_name,
                'api_gender' => $applicant->api_gender,
                'admission_sch_round' => $applicant->admission_sch_round,
                'api_permanentcity' => $applicant->api_permanentcity,
                'aai_apply_date' => $applicant->aai_apply_date,
                'allowed_status' => $applicant->allowed_status,
                'api_ishostel' => $applicant->api_ishostel,
                'hostel_location_preference' => $applicant->hostel_location_preference,
                'sc_challan_status' => $applicant->sc_challan_status,
                'sc_challan_no' => $applicant->sc_challan_no,
                'sc_challan_amount' => $applicant->sc_challan_amount,
                'sc_scholarship_amount' => $applicant->sc_scholarship_amount,
                'scholarship_details' => $applicant->scholarship_details,
                'ha_disable_map_display' => $applicant->ha_disable_map_display,
                'hostel_name' => $applicant->hostel_name,
                'floor_name' => $applicant->floor_name,
                'room_no' => $applicant->room_no,
                'bed_no' => $applicant->bed_no,
                'in_consent_list' => $inConsentList,
            ],
        ]);
    }

    /**
     * Grant hostel consent (API_ISHOSTEL = 'Yes') so applicant appears in consent-based list.
     */
    public function grantHostelConsent(Request $request)
    {
        $request->validate([
            'session_id' => 'required|integer',
            'aai_id' => 'required|integer',
        ]);

        $sessionId = (int) $request->input('session_id');
        $aaiId = (int) $request->input('aai_id');

        $applicant = DB::table('admission_apply_info as aa')
            ->join('admission_personal_info as ap', 'aa.AAI_API_ID', '=', 'ap.API_ID')
            ->leftJoin('admission_schedules as ads', 'aa.aai_admission_sch_id', '=', 'ads.ADMISSION_SCH_ID')
            ->where('aa.aai_id', $aaiId)
            ->where('aa.AAI_STATUS', 'Selected')
            ->select(
                'aa.aai_id',
                'aa.AAI_SDPO_SESS_ID as aai_sdpo_sess_id',
                'aa.CMS_SAI_ENROLLMENT_NO as cms_sai_enrollment_no',
                'ap.API_ID as api_id',
                'ap.API_NAME as api_name',
                'ap.API_GENDER as api_gender',
                'ap.API_ISHOSTEL as api_ishostel',
                'ap.API_PERMANENTCITY as api_permanentcity',
                'ads.admission_sch_round'
            )
            ->first();

        if (!$applicant) {
            return response()->json([
                'success' => false,
                'message' => 'No selected applicant found with the given Applicant ID.',
            ]);
        }

        if ((int) ($applicant->aai_sdpo_sess_id ?? 0) !== $sessionId) {
            $actualSession = DB::table('academic_sessions')
                ->where('sess_id', $applicant->aai_sdpo_sess_id)
                ->value('sess_name');

            return response()->json([
                'success' => false,
                'message' => 'Applicant belongs to session "' . ($actualSession ?: ('ID ' . $applicant->aai_sdpo_sess_id)) . '". Please select that academic session first.',
            ]);
        }

        $ishostel = trim($applicant->api_ishostel ?? '');
        if (strcasecmp($ishostel, 'Yes') === 0 || in_array(strtoupper($ishostel), ['Y', 'YES'], true)) {
            return response()->json([
                'success' => true,
                'already_consented' => true,
                'message' => 'Applicant already has hostel consent and should appear in the list below.',
                'applicant' => [
                    'aai_id' => $applicant->aai_id,
                    'api_name' => $applicant->api_name,
                    'api_ishostel' => $applicant->api_ishostel,
                ],
            ]);
        }

        DB::table('admission_personal_info')
            ->where('api_id', $applicant->api_id)
            ->update(['api_ishostel' => 'Yes']);

        return response()->json([
            'success' => true,
            'already_consented' => false,
            'message' => 'Hostel consent granted successfully. Applicant will now appear in the list below.',
            'applicant' => [
                'aai_id' => $applicant->aai_id,
                'cms_sai_enrollment_no' => $applicant->cms_sai_enrollment_no,
                'api_name' => $applicant->api_name,
                'api_gender' => $applicant->api_gender,
                'api_permanentcity' => $applicant->api_permanentcity,
                'api_ishostel' => 'Yes',
                'admission_sch_round' => $applicant->admission_sch_round,
            ],
        ]);
    }

    /**
     * Manual search for continuing students by enrollment no or student ID.
     * Does not require SPI_ISHOSTEL = 'Yes' (unlike the consent-based list).
     */
    public function searchHostelStudent(Request $request)
    {
        $request->validate([
            'session_id' => 'required|integer',
            'search' => 'required|string|max:50',
        ]);

        $sessionId = (int) $request->input('session_id');
        $search = trim($request->input('search'));

        if ($search === '') {
            return response()->json(['success' => false, 'message' => 'Please enter Enrollment No. or Student ID.']);
        }

        $student = DB::selectOne("
            SELECT
                sai.sai_id,
                sai.sai_enrollment_no,
                spi.spi_name AS student_name,
                spi.spi_gender AS gender,
                spi.spi_permanentcity AS permanent_city,
                spi.spi_ishostel AS is_hostel,
                COALESCE(happ.happ_allowed_status, 'Not Allowed') AS allowed_status,
                happ.happ_hostel_location_preference AS hostel_location_preference,
                happ.happ_entry_date AS apply_date,
                hassign.ha_disable_map_display,
                CASE WHEN happ.happ_enrollment_no IS NOT NULL THEN 1 ELSE 0 END AS in_hostel_list,
                (
                    SELECT MAX(scc.sc_challan_status)
                    FROM student_challan scc
                    WHERE scc.SC_SESSION_ID = :p_session_id
                      AND scc.SC_CHALLAN_TYPE = 'Hostel_Fee'
                      AND UPPER(scc.sc_enrollment_no) = UPPER(sai.sai_enrollment_no)
                ) AS sc_challan_status,
                (
                    SELECT scc.challan_no
                    FROM student_challan scc
                    WHERE scc.id = (
                        SELECT MAX(scc2.id)
                        FROM student_challan scc2
                        WHERE scc2.SC_SESSION_ID = :p_session_id
                          AND scc2.SC_CHALLAN_TYPE = 'Hostel_Fee'
                          AND UPPER(scc2.sc_enrollment_no) = UPPER(sai.sai_enrollment_no)
                          AND NVL(scc2.sc_challan_status, 'X') != 'CANCELLED'
                          AND scc2.sc_challan_status = 'UNPAID'
                    )
                ) AS sc_challan_no,
                (
                    SELECT NVL(scc.sc_amount, scc.invoiceamount)
                    FROM student_challan scc
                    WHERE scc.id = (
                        SELECT MAX(scc2.id)
                        FROM student_challan scc2
                        WHERE scc2.SC_SESSION_ID = :p_session_id
                          AND scc2.SC_CHALLAN_TYPE = 'Hostel_Fee'
                          AND UPPER(scc2.sc_enrollment_no) = UPPER(sai.sai_enrollment_no)
                          AND NVL(scc2.sc_challan_status, 'X') != 'CANCELLED'
                          AND scc2.sc_challan_status = 'UNPAID'
                    )
                ) AS sc_challan_amount,
                (
                    SELECT scc.sc_scholarship_amount
                    FROM student_challan scc
                    WHERE scc.id = (
                        SELECT MAX(scc2.id)
                        FROM student_challan scc2
                        WHERE scc2.SC_SESSION_ID = :p_session_id
                          AND scc2.SC_CHALLAN_TYPE = 'Hostel_Fee'
                          AND UPPER(scc2.sc_enrollment_no) = UPPER(sai.sai_enrollment_no)
                          AND NVL(scc2.sc_challan_status, 'X') != 'CANCELLED'
                          AND scc2.sc_challan_status = 'UNPAID'
                    )
                ) AS sc_scholarship_amount,
                ht.ht_name AS hostel_name,
                hf.hf_name AS floor_name,
                hr.hr_room_no AS room_no,
                hb.hb_bed_number AS bed_no
            FROM students_apply_info sai
            JOIN students_personal_info spi ON spi.spi_id = sai.sai_spi_id
            LEFT JOIN hostel_applicants happ
                ON UPPER(happ.happ_enrollment_no) = UPPER(sai.sai_enrollment_no)
               AND happ.happ_session = :p_session_id
            LEFT JOIN hostel_assign hassign
                ON UPPER(hassign.ha_enrolment_no) = UPPER(sai.sai_enrollment_no)
               AND hassign.ha_session = :p_session_id
            LEFT JOIN hostel_beds hb ON hassign.ha_hb_id = hb.hb_id
            LEFT JOIN hostel_rooms hr ON hb.hb_room_id = hr.hr_id
            LEFT JOIN hostel_floor hf ON hr.hr_hf_id = hf.hf_id
            LEFT JOIN HOSTEL_TYPE ht ON hr.hr_ht_id = ht.ht_id
            WHERE (
                    UPPER(sai.sai_enrollment_no) = UPPER(:p_search)
                 OR TO_CHAR(sai.sai_id) = :p_search
            )
        ", [
            'p_session_id' => $sessionId,
            'p_search' => $search,
        ]);

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'No continuing student found with the given Enrollment No. or Student ID.',
            ]);
        }

        $gender = strtolower(trim($student->gender ?? ''));
        if (!in_array($gender, ['male', 'female'], true)) {
            return response()->json([
                'success' => false,
                'message' => 'Student gender is missing or invalid.',
            ]);
        }

        $ishostel = strtoupper(trim($student->is_hostel ?? ''));
        $inConsentList = strcasecmp(trim($student->is_hostel ?? ''), 'Yes') === 0
            || in_array($ishostel, ['Y', 'YES'], true);

        return response()->json([
            'success' => true,
            'student' => [
                'sai_id' => $student->sai_id,
                'sai_enrollment_no' => $student->sai_enrollment_no,
                'student_name' => $student->student_name,
                'gender' => $student->gender,
                'permanent_city' => $student->permanent_city,
                'apply_date' => $student->apply_date,
                'allowed_status' => $student->allowed_status,
                'is_hostel' => $student->is_hostel,
                'hostel_location_preference' => $student->hostel_location_preference,
                'sc_challan_status' => $student->sc_challan_status,
                'sc_challan_no' => $student->sc_challan_no,
                'sc_challan_amount' => $student->sc_challan_amount,
                'sc_scholarship_amount' => $student->sc_scholarship_amount,
                'ha_disable_map_display' => $student->ha_disable_map_display,
                'hostel_name' => $student->hostel_name,
                'floor_name' => $student->floor_name,
                'room_no' => $student->room_no,
                'bed_no' => $student->bed_no,
                'in_consent_list' => $inConsentList,
                'in_hostel_list' => (bool) $student->in_hostel_list,
            ],
        ]);
    }

    /**
     * Add a continuing student into hostel_applicants for the selected session.
     * Prevents duplicate rows for the same enrollment + session.
     */
    public function addHostelStudent(Request $request)
    {
        $request->validate([
            'session_id' => 'required|integer',
            'enrollment_no' => 'required_without:sai_id|nullable|string|max:50',
            'sai_id' => 'required_without:enrollment_no|nullable|integer',
        ]);

        $sessionId = (int) $request->input('session_id');
        $enrollmentNo = trim((string) $request->input('enrollment_no', ''));
        $saiId = $request->input('sai_id');

        $studentQuery = DB::table('students_apply_info as sai')
            ->join('students_personal_info as spi', 'spi.spi_id', '=', 'sai.sai_spi_id')
            ->select(
                'sai.sai_id',
                'sai.sai_enrollment_no',
                'spi.spi_name as student_name',
                'spi.spi_gender as gender',
                'spi.spi_permanentcity as permanent_city',
                'spi.spi_ishostel as is_hostel'
            );

        if ($saiId) {
            $studentQuery->where('sai.sai_id', (int) $saiId);
        } else {
            $studentQuery->whereRaw('UPPER(TRIM(sai.sai_enrollment_no)) = UPPER(TRIM(?))', [$enrollmentNo]);
        }

        $student = $studentQuery->first();

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'No continuing student found with the given Enrollment No. or Student ID.',
            ]);
        }

        $gender = strtolower(trim($student->gender ?? ''));
        if (!in_array($gender, ['male', 'female'], true)) {
            return response()->json([
                'success' => false,
                'message' => 'Student gender is missing or invalid.',
            ]);
        }

        $exists = DB::table('hostel_applicants')
            ->whereRaw('UPPER(TRIM(happ_enrollment_no)) = UPPER(TRIM(?))', [$student->sai_enrollment_no])
            ->where('happ_session', $sessionId)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => true,
                'already_exists' => true,
                'message' => 'Student is already in the hostel list for this session.',
                'student' => [
                    'sai_id' => $student->sai_id,
                    'sai_enrollment_no' => $student->sai_enrollment_no,
                    'student_name' => $student->student_name,
                    'gender' => $student->gender,
                    'permanent_city' => $student->permanent_city,
                    'is_hostel' => $student->is_hostel,
                    'in_hostel_list' => true,
                ],
            ]);
        }

        $entryUserId = auth()->user()->employee_id ?? auth()->user()->id;

        DB::table('hostel_applicants')->insert([
            'happ_enrollment_no' => $student->sai_enrollment_no,
            'happ_session' => $sessionId,
            'happ_entry_date' => now(),
            'happ_entry_user_id' => $entryUserId,
        ]);

        return response()->json([
            'success' => true,
            'already_exists' => false,
            'message' => 'Student added to hostel list successfully.',
            'student' => [
                'sai_id' => $student->sai_id,
                'sai_enrollment_no' => $student->sai_enrollment_no,
                'student_name' => $student->student_name,
                'gender' => $student->gender,
                'permanent_city' => $student->permanent_city,
                'is_hostel' => $student->is_hostel,
                'in_hostel_list' => true,
            ],
        ]);
    }

    public function getHostelStudentsData(Request $request)
    {
        $sessionId = $request->input('session_id');

        $seatStats = app(HostelSeatStatsService::class);
        $maleSeatSummary = $seatStats->getGenderSeatSummary($sessionId, 'male', 'student');
        $femaleSeatSummary = $seatStats->getGenderSeatSummary($sessionId, 'female', 'student');
        $male_vacant_seats = $maleSeatSummary['vacant_seats'];
        $female_vacant_seats = $femaleSeatSummary['vacant_seats'];
        $malePipelineAllowed = $maleSeatSummary['allowed_count'];
        $femalePipelineAllowed = $femaleSeatSummary['allowed_count'];

        /**
         * Students list for a session = merge of:
         *  A) Consent/request in hostel_applicants for THIS session
         *     - pending / not Allowed → always shown
         *     - Allowed → only if matching hostel_assign exists for THIS session
         *       (drops stale Allowed rows with no assign, e.g. old-session leftovers)
         *  B) Active hostel_assign for THIS session not already covered by A
         *
         * Challan prefers hostel_assign.ha_challan_id → student_challan.
         */
        $sessionId = (int) $sessionId;

        // A) Consent / request for THIS session
        $consentStudents = DB::select("
            SELECT
                NVL(TO_CHAR(happ.happ_allowed_status), 'Not Allowed') AS happ_allowed_status,
                NVL(TO_CHAR(happ.happ_enrollment_no), TO_CHAR(sai.sai_enrollment_no)) AS happ_enrollment_no,
                NVL(
                    TO_CHAR(hassign.ha_hostel_location_preference),
                    TO_CHAR(happ.happ_hostel_location_preference)
                ) AS hostel_location_preference,
                spi.SPI_NAME               AS student_name,
                spi.SPI_GENDER             AS gender,
                spi.SPI_PERMANENTADDRESS   AS permanent_address,
                spi.SPI_PERMANENTCITY      AS permanent_city,
                happ.happ_entry_date       AS apply_date,
                spi.SPI_GENDER             AS spi_gender,
                spi.SPI_ISHOSTEL           AS is_hostel,
                happ.happ_session          AS session_id,
                hassign.ha_disable_map_display AS ha_disable_map_display,
                hassign.ha_id              AS ha_id,
                hassign.ha_hb_id           AS ha_hb_id,
                hassign.ha_challan_id      AS ha_challan_id,
                NVL(
                    TO_CHAR(sc.sc_challan_status),
                    (
                        SELECT MAX(TO_CHAR(scc.sc_challan_status))
                        FROM student_challan scc
                        WHERE scc.SC_SESSION_ID   = ?
                          AND scc.SC_CHALLAN_TYPE = 'Hostel_Fee'
                          AND UPPER(TRIM(TO_CHAR(scc.sc_enrollment_no))) = UPPER(TRIM(TO_CHAR(sai.sai_enrollment_no)))
                    )
                ) AS sc_challan_status,
                ht.ht_name AS hostel_name,
                hf.hf_name AS floor_name,
                hr.hr_room_no AS room_no,
                hb.hb_bed_number AS bed_no
            FROM hostel_applicants happ
            JOIN students_apply_info sai
                   ON UPPER(TRIM(TO_CHAR(happ.happ_enrollment_no))) = UPPER(TRIM(TO_CHAR(sai.sai_enrollment_no)))
            JOIN students_personal_info spi
                   ON spi.spi_id = sai.sai_spi_id
            LEFT JOIN hostel_assign hassign
                   ON UPPER(TRIM(TO_CHAR(hassign.ha_enrolment_no))) = UPPER(TRIM(TO_CHAR(happ.happ_enrollment_no)))
                  AND hassign.ha_session = ?
                  AND hassign.ha_status = 'E'
            LEFT JOIN student_challan sc
                   ON sc.id = hassign.ha_challan_id
            LEFT JOIN hostel_beds hb
                   ON hassign.ha_hb_id = hb.hb_id
            LEFT JOIN hostel_rooms hr
                   ON hb.hb_room_id = hr.hr_id
            LEFT JOIN hostel_floor hf
                   ON hr.hr_hf_id = hf.hf_id
            LEFT JOIN HOSTEL_TYPE ht
                   ON hr.hr_ht_id = ht.ht_id
            WHERE happ.happ_session = ?
              AND LOWER(TRIM(TO_CHAR(spi.SPI_GENDER))) IN ('male', 'female')
              AND (
                    UPPER(TRIM(NVL(TO_CHAR(happ.happ_allowed_status), 'NOT ALLOWED'))) != 'ALLOWED'
                    OR hassign.ha_id IS NOT NULL
              )
        ", [$sessionId, $sessionId, $sessionId]);

        // B) Active assignment for THIS session, not already covered by consent rows
        $assignedOnlyStudents = DB::select("
            SELECT
                NVL(TO_CHAR(happ.happ_allowed_status), 'Allowed') AS happ_allowed_status,
                NVL(TO_CHAR(ha.ha_enrolment_no), TO_CHAR(sai.sai_enrollment_no)) AS happ_enrollment_no,
                NVL(
                    TO_CHAR(ha.ha_hostel_location_preference),
                    TO_CHAR(happ.happ_hostel_location_preference)
                ) AS hostel_location_preference,
                spi.SPI_NAME               AS student_name,
                spi.SPI_GENDER             AS gender,
                spi.SPI_PERMANENTADDRESS   AS permanent_address,
                spi.SPI_PERMANENTCITY      AS permanent_city,
                happ.happ_entry_date       AS apply_date,
                spi.SPI_GENDER             AS spi_gender,
                spi.SPI_ISHOSTEL           AS is_hostel,
                ha.ha_session              AS session_id,
                ha.ha_disable_map_display  AS ha_disable_map_display,
                ha.ha_id                   AS ha_id,
                ha.ha_hb_id                AS ha_hb_id,
                ha.ha_challan_id           AS ha_challan_id,
                NVL(
                    TO_CHAR(sc.sc_challan_status),
                    (
                        SELECT MAX(TO_CHAR(scc.sc_challan_status))
                        FROM student_challan scc
                        WHERE scc.SC_SESSION_ID   = ?
                          AND scc.SC_CHALLAN_TYPE = 'Hostel_Fee'
                          AND UPPER(TRIM(TO_CHAR(scc.sc_enrollment_no))) = UPPER(TRIM(TO_CHAR(sai.sai_enrollment_no)))
                    )
                ) AS sc_challan_status,
                ht.ht_name AS hostel_name,
                hf.hf_name AS floor_name,
                hr.hr_room_no AS room_no,
                hb.hb_bed_number AS bed_no
            FROM hostel_assign ha
            JOIN students_apply_info sai
                   ON UPPER(TRIM(TO_CHAR(ha.ha_enrolment_no))) = UPPER(TRIM(TO_CHAR(sai.sai_enrollment_no)))
            JOIN students_personal_info spi
                   ON spi.spi_id = sai.sai_spi_id
            LEFT JOIN hostel_applicants happ
                   ON UPPER(TRIM(TO_CHAR(happ.happ_enrollment_no))) = UPPER(TRIM(TO_CHAR(ha.ha_enrolment_no)))
                  AND happ.happ_session = ?
            LEFT JOIN student_challan sc
                   ON sc.id = ha.ha_challan_id
            LEFT JOIN hostel_beds hb
                   ON ha.ha_hb_id = hb.hb_id
            LEFT JOIN hostel_rooms hr
                   ON hb.hb_room_id = hr.hr_id
            LEFT JOIN hostel_floor hf
                   ON hr.hr_hf_id = hf.hf_id
            LEFT JOIN HOSTEL_TYPE ht
                   ON hr.hr_ht_id = ht.ht_id
            WHERE ha.ha_session = ?
              AND ha.ha_status = 'E'
              AND ha.ha_enrolment_no IS NOT NULL
              AND happ.happ_enrollment_no IS NULL
              AND LOWER(TRIM(TO_CHAR(spi.SPI_GENDER))) IN ('male', 'female')
        ", [$sessionId, $sessionId, $sessionId]);

        // Convert to collection for easier processing
        $students = collect($consentStudents)
            ->concat($assignedOnlyStudents)
            ->filter(function ($student) use ($sessionId) {
                return (int) ($student->session_id ?? 0) === $sessionId;
            })
            ->unique(function ($student) {
                return strtoupper(trim((string) ($student->happ_enrollment_no ?? '')));
            })
            ->values();


        // Filter by gender (using the correct field name from new query)
        $maleStudents = $students->filter(fn($s) => strtolower($s->gender) == 'male');
        $femaleStudents = $students->filter(fn($s) => strtolower($s->gender) == 'female');

        // Count allowed students by gender (EXCLUDE PAID applicants from allowed count)
        $maleAllowedCount = $maleStudents->filter(function ($student) {
            return $student->happ_allowed_status == 'Allowed' && $student->sc_challan_status != 'PAID';
        })->count();

        $femaleAllowedCount = $femaleStudents->filter(function ($student) {
            return $student->happ_allowed_status == 'Allowed' && $student->sc_challan_status != 'PAID';
        })->count();

        // Count students with consent given (challan generated) - only UNPAID allowed students
        $maleChallanCount = $maleStudents->filter(function ($student) {
            return $student->happ_allowed_status == 'Allowed' &&
                !empty($student->sc_challan_status) &&
                $student->sc_challan_status != 'PAID';
        })->count();

        $femaleChallanCount = $femaleStudents->filter(function ($student) {
            return $student->happ_allowed_status == 'Allowed' &&
                !empty($student->sc_challan_status) &&
                $student->sc_challan_status != 'PAID';
        })->count();

        // Count students with paid challans
        $malePaidCount = $maleStudents->filter(function ($student) {
            return $student->happ_allowed_status == 'Allowed' && $student->sc_challan_status == 'PAID';
        })->count();

        $femalePaidCount = $femaleStudents->filter(function ($student) {
            return $student->happ_allowed_status == 'Allowed' && $student->sc_challan_status == 'PAID';
        })->count();

        // Count students that can be vacated (allowed but no challan or unpaid)
        $maleVacateCount = $maleStudents->filter(function ($student) {
            return $student->happ_allowed_status == 'Allowed' &&
                ($student->sc_challan_status != 'PAID' || empty($student->sc_challan_status));
        })->count();

        $femaleVacateCount = $femaleStudents->filter(function ($student) {
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
            'male_available_seats' => $maleSeatSummary['available_seats'],
            'female_available_seats' => $femaleSeatSummary['available_seats'],
            'male_allowed_count' => $malePipelineAllowed,
            'female_allowed_count' => $femalePipelineAllowed,
            'male_remaining_seats' => $maleSeatSummary['remaining_seats'],
            'female_remaining_seats' => $femaleSeatSummary['remaining_seats'],
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
        // Step 1: Get data from the request
        $seatReservationMapDisplay = $request->input('seat_reservation_map_display', '0');
        $sessionId = $request->input('session_id');
        $admissionScheduleId = $request->input('admission_schedule_id');

        // Step 2: Validate that Session ID is not empty
        if (!$sessionId) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Session ID is required']);
            }
            return redirect()->back()->with('error', 'Session ID is required');
        }

        // Step 3: Ensure student exists in hostel_applicants for this session (manual search support)
        $hostelApplicantExists = DB::table('hostel_applicants')
            ->whereRaw('UPPER(happ_enrollment_no) = UPPER(?)', [$enrollmentNo])
            ->where('happ_session', $sessionId)
            ->exists();

        if (!$hostelApplicantExists) {
            $studentInfo = DB::table('students_apply_info as sai')
                ->join('students_personal_info as spi', 'spi.spi_id', '=', 'sai.sai_spi_id')
                ->whereRaw('UPPER(sai.sai_enrollment_no) = UPPER(?)', [$enrollmentNo])
                ->select('sai.sai_enrollment_no')
                ->first();

            if (!$studentInfo) {
                return response()->json(['success' => false, 'message' => 'Student not found for enrollment: ' . $enrollmentNo]);
            }

            $entryUserId = auth()->user()->employee_id ?? auth()->user()->id;

            DB::table('hostel_applicants')->insert([
                'happ_enrollment_no' => $studentInfo->sai_enrollment_no,
                'happ_session' => (int) $sessionId,
                'happ_entry_date' => now(),
                'happ_entry_user_id' => $entryUserId,
            ]);
        }

        // Step 4: Find the gender of the applicant to check seats
        $applicantGender = DB::table('hostel_applicants')
            ->join('students_apply_info', DB::raw('UPPER(hostel_applicants.happ_enrollment_no)'), '=', DB::raw('UPPER(students_apply_info.sai_enrollment_no)'))
            ->join('students_personal_info', 'students_personal_info.spi_id', '=', 'students_apply_info.sai_spi_id')
            ->where('hostel_applicants.happ_enrollment_no', $enrollmentNo)
            ->value('students_personal_info.spi_gender');

        if (!$applicantGender) {
            return response()->json(['success' => false, 'message' => 'Applicant gender not found for enrollment: ' . $enrollmentNo]);
        }

        // Step 4: Get and Validate Hostel Location Requirement BEFORE checking availability
        $hostelLocation = $request->input('hostel_location');
        if (!$hostelLocation || !in_array($hostelLocation, ['on_campus', 'off_campus', 'both'])) {
            return response()->json([
                'success' => false,
                'error' => 'Please select hostel location preference (On Campus, Off Campus, or Both)'
            ], 422);
        }

        $assignmentType = $request->input('hostel_assignment_type');
        $allowedAssignmentTypes = [
            'regular',
            'non-regular',
            'regular_outside',
            'non-regular_outside',
        ];

        if (!$assignmentType || !in_array($assignmentType, $allowedAssignmentTypes, true)) {
            return response()->json([
                'success' => false,
                'message' => 'Please select assignment type (Regular or Non Regular)'
            ], 422);
        }


        $isNonRegularAssignment = in_array($assignmentType, ['non-regular', 'non-regular_outside'], true);
        $noOfDays = (int) $request->input('no_of_days', 0);

        if ($isNonRegularAssignment) {
            if ($noOfDays < 1 || $noOfDays > 365) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please enter number of days between 1 and 365 for Non Regular assignment'
                ], 422);
            }
        } else {
            $noOfDays = 0;
        }

        // Backend seat validation is now disabled - relying on frontend validation using dashboard stats

        // Step 5: Start a database transaction to ensure data safety
        DB::beginTransaction();

        try {
            // Step 6: Update the applicant status to 'Allowed' and save location preference
            $query = "UPDATE hostel_applicants
                SET happ_allowed_status = 'Allowed',
                    happ_hostel_location_preference = :hostelLocation
                WHERE UPPER(happ_enrollment_no) = UPPER(:enrollmentNo)
                  AND happ_session = :sessionId
            ";

            DB::update($query, [
                'hostelLocation' => $hostelLocation,
                'enrollmentNo' => $enrollmentNo,
                'sessionId' => (int) $sessionId,
            ]);

            $userId = strtolower(auth()->user()->id);

            $username = auth()->user()->username ?? auth()->user()->id;

            $aaiId = DB::table('admission_apply_info')
                ->whereRaw('UPPER(cms_sai_enrollment_no) = UPPER(?)', [$enrollmentNo])
                ->value('aai_id');

            // Step 7: Save information into the assignment table.
            // ha_hb_id is explicitly set to null here so that re-allowing a student
            // for a new session never carries forward a stale bed from a prior run.
            // The bed assignment step happens separately on the bed-assignment page.
            DB::table('hostel_assign')->updateOrInsert(
                [
                    'ha_enrolment_no' => $enrollmentNo,
                    'ha_session' => $sessionId
                ],
                [
                    'ha_status' => 'E',
                    'ha_hb_id' => null,
                    'ha_aai_id' => $aaiId,
                    'ha_disable_map_display' => $seatReservationMapDisplay == '1' ? 'Yes' : 'No',
                    'ha_map_display_user_entry' => $username,
                    'ha_hostel_location_preference' => $hostelLocation,
                    'ha_hostel_session_type' => $assignmentType,
                    'ha_entry_user_id' => $userId
                ]
            );

            // Step 8: Get the assignment record
            $assignment = DB::table('hostel_assign')
                ->where('ha_enrolment_no', $enrollmentNo)
                ->where('ha_session', $sessionId)
                ->first();

            // Step 9: Check if auto-allocation is enabled (checkbox checked)
            $autoGenerateChallan = $seatReservationMapDisplay == '1'
                || filter_var($request->input('auto_generate_challan'), FILTER_VALIDATE_BOOLEAN);

            if ($autoGenerateChallan) {
                if (!$assignment) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Hostel assignment record could not be created. Please try again.'
                    ]);
                }

                $applicantData = [
                    'enrollment_no' => $enrollmentNo,
                    'gender' => strtolower(trim($applicantGender)) === 'female' ? 'female' : 'male',
                    'assignment_id' => $assignment->ha_id,
                    'session_type' => $assignmentType,
                    'no_of_days' => $noOfDays,
                ];

                $allocationController = new \App\Http\Controllers\Grader\Hostel\HostelAllocationController();


                // Call Allocation Controller
                $allocationResult = $allocationController->allocateSeatAndGenerateChallan(
                    $applicantData,
                    $sessionId,
                    $hostelLocation,
                    'old'
                );

                // Check allocation result
                if (!$allocationResult['success']) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => $allocationResult['message']
                    ]);
                }

                DB::commit();

                // Return success with allocation details
                return response()->json([
                    'success' => true,
                    'message' => $allocationResult['message'] ?? 'Applicant allowed and seat allocated successfully',
                    'autoAssigned' => true,
                    'challanAlreadyExists' => !empty($allocationResult['challanAlreadyExists']),
                    'allocationDetails' => $allocationResult['allocationDetails'],
                    'challanDetails' => $allocationResult['challanDetails']
                ]);

            } else {
                // Manual process - no auto-allocation
                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Applicant allowed successfully',
                    'autoAssigned' => false
                ]);
            }

        } catch (Exception $e) {
            // Step 10: If there is an error, undo everything
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error allowing applicant: ' . $e->getMessage()]);
        }
    }
    public function allowNewApplicant(Request $request, $enrollmentNo)
    {
        DB::beginTransaction();

        try {
            $aaiApiId = DB::table('admission_apply_info')
                ->where('aai_id', $enrollmentNo)
                ->value('aai_api_id');

            if (!$aaiApiId) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Applicant not found for ID: ' . $enrollmentNo]);
            }

            $seatReservationMapDisplay = $request->input('seat_reservation_map_display', '0');
            $sessionId = $request->input('session_id');
            $admissionScheduleId = $request->input('admission_schedule_id');

            if (!$sessionId) {
                DB::rollBack();
                if ($request->ajax()) {
                    return response()->json(['success' => false, 'message' => 'Session ID is required']);
                }
                return redirect()->back()->with('error', 'Session ID is required');
            }

            $applicantGender = DB::table('admission_personal_info')
                ->where('api_id', $aaiApiId)
                ->value('api_gender');

            if (!$applicantGender) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Applicant gender not found']);
            }

            $hostelLocation = $request->input('hostel_location');

            if (!$hostelLocation || !in_array($hostelLocation, ['on_campus', 'off_campus', 'both'])) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Please select hostel location preference (On Campus, Off Campus, or Both)'
                ], 422);
            }

            $assignmentType = $request->input('hostel_assignment_type');
            $allowedAssignmentTypes = ['regular', 'non-regular', 'regular_outside', 'non-regular_outside'];

            if (!$assignmentType || !in_array($assignmentType, $allowedAssignmentTypes, true)) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Please select assignment type (Regular or Non Regular)'
                ], 422);
            }

            $isNonRegularAssignment = in_array($assignmentType, ['non-regular', 'non-regular_outside'], true);
            $noOfDays = (int) $request->input('no_of_days', 0);

            if ($isNonRegularAssignment) {
                if ($noOfDays < 1 || $noOfDays > 365) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Please enter number of days between 1 and 365 for Non Regular assignment'
                    ], 422);
                }
            } else {
                $noOfDays = 0;
            }

            // Backend seat validation is now disabled - relying on frontend validation using dashboard stats

            // Step 8: Update the personal info table to set status as 'Allowed'
            $query = "
                UPDATE admission_personal_info
                SET api_allow_hostel = 'Allowed'
                WHERE api_id = :aaiApiId
            ";
            DB::update($query, ['aaiApiId' => $aaiApiId]);

            $userId = strtolower(auth()->user()->id);
            $username = auth()->user()->username ?? auth()->user()->id;

            // Step 9: Record the assignment in the hostel_assign table.
            // ha_hb_id is explicitly cleared so re-allowing a new applicant for a
            // different session never carries over a stale bed reference.
            DB::table('hostel_assign')->updateOrInsert(
                [
                    'ha_aai_id' => $enrollmentNo,
                    'ha_session' => $sessionId
                ],
                [
                    'ha_api_id' => $aaiApiId,
                    'ha_hb_id' => null,
                    'ha_disable_map_display' => $seatReservationMapDisplay == '1' ? 'Yes' : 'No',
                    'ha_map_display_user_entry' => $username,
                    'ha_hostel_location_preference' => $hostelLocation,
                    'ha_hostel_session_type' => $assignmentType,
                    'ha_status' => 'E',
                    'ha_entry_user_id' => $userId
                ]
            );

            // Step 10: Get the assignment record
            $assignment = DB::table('hostel_assign')
                ->where('ha_aai_id', $enrollmentNo)
                ->where('ha_session', $sessionId)
                ->first();

            // Step 11: Check if auto-allocation is enabled (checkbox checked)
            $autoGenerateChallan = $seatReservationMapDisplay == '1'
                || filter_var($request->input('auto_generate_challan'), FILTER_VALIDATE_BOOLEAN);

            if ($autoGenerateChallan) {
                if (!$assignment) {
                    DB::rollBack();
                    return response()->json(['success' => false, 'message' => 'Hostel assignment record could not be created. Please try again.']);
                }

                $enrollment = DB::table('admission_apply_info')
                    ->where('aai_id', $enrollmentNo)
                    ->value('cms_sai_enrollment_no');

                $applicantData = [
                    'aai_id' => $enrollmentNo,
                    'api_id' => $aaiApiId,
                    'enrollment_no' => $enrollment,
                    'gender' => strtolower(trim($applicantGender)) === 'female' ? 'female' : 'male',
                    'assignment_id' => $assignment->ha_id,
                    'session_type' => $assignmentType,
                    'no_of_days' => $noOfDays,
                ];


                $allocationController = new \App\Http\Controllers\Grader\Hostel\HostelAllocationController();

                // Call Allocation Controller
                $allocationResult = $allocationController->allocateSeatAndGenerateChallan(
                    $applicantData,
                    $sessionId,
                    $hostelLocation,
                    'new'
                );

                // Check allocation result
                if (!$allocationResult['success']) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => $allocationResult['message']
                    ]);
                }

                DB::commit();

                // Return success with allocation details
                return response()->json([
                    'success' => true,
                    'message' => $allocationResult['message'] ?? 'Applicant allowed and seat allocated successfully',
                    'autoAssigned' => true,
                    'challanAlreadyExists' => !empty($allocationResult['challanAlreadyExists']),
                    'allocationDetails' => $allocationResult['allocationDetails'],
                    'challanDetails' => $allocationResult['challanDetails']
                ]);

            } else {
                // Manual process - no auto-allocation
                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Applicant allowed successfully',
                    'autoAssigned' => false
                ]);
            }

        } catch (Exception $e) {
            // Step 12: If something went wrong, cancel all changes
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error allowing applicant: ' . $e->getMessage()]);
        }
    }
    /**
     * Get external hostel applicants data with specific query
     */
    private function getExternalHostelApplicantsData($request)
    {
        $sessionId = $request->input('session_id');
        $admission_schedule_id = $request->input('admission_schedule_id');

        // Note: Admission schedule filter not applicable for external hostel query

        // Base SQL query for external hostel applicants (API_ALLOW_HOSTEL IS NULL)
        $baseQuery = "
        WITH {$this->hostelApplicantsLookupCtesSql()}
        SELECT DISTINCT(admission_apply_info.AAI_ID) as aai_id,
            admission_apply_info.CMS_SAI_ENROLLMENT_NO as cms_sai_enrollment_no,
            ap.API_NAME as api_name,
            ap.API_GENDER as api_gender,
            ap.API_PERMANENTADDRESS as api_permanentaddress,
            ap.API_PERMANENTCITY as api_permanentcity,
            admission_apply_info.AAI_APPLY_DATE as aai_apply_date,
            admission_apply_info.AAI_SDPO_SESS_ID as aai_sdpo_sess_id,
            CASE 
                WHEN ap.API_ALLOW_HOSTEL IS NULL THEN 'Not Allowed' 
                WHEN ap.API_ALLOW_HOSTEL = 'Rejected' THEN 'Rejected'
                ELSE 'Allowed' 
            END AS allowed_status,
            CASE
                WHEN ads.admission_sch_round = 1 THEN 'Early Round'
                WHEN ads.admission_sch_round = 2 THEN 'Regular Round'
                WHEN ads.admission_sch_round = 3 THEN 'Rolling Round'
                ELSE 'Unknown'
            END AS admission_sch_round,
            hc.sc_challan_status,
            fs.full_semester_challan_status,
            sch.scholarship_details,
            CASE
                WHEN aq.aq_obtained_marks IS NULL THEN
                    aq.aq_degree_name || ': ' || (
                        SELECT aqd.ADQ_EQUAL_OBTAIN_MARKS
                        FROM admission_qualification_detail aqd
                        WHERE aqd.aqd_api_id = aq.aq_api_id
                          AND aqd.aqd_degree_name = aq.aq_degree_name
                          AND ROWNUM = 1
                    )
                ELSE aq.aq_degree_name || ': ' || TO_CHAR(aq.aq_obtained_marks)
            END AS last_degree_marks
        FROM admission_apply_info
        JOIN admission_personal_info ap ON admission_apply_info.AAI_API_ID = ap.API_ID
        JOIN student_challan ON admission_apply_info.AAI_ID = student_challan.SC_APP_ID
        LEFT JOIN hostel_challan hc ON hc.sc_app_id = admission_apply_info.AAI_ID
        LEFT JOIN fs_challan fs ON fs.sc_app_id = admission_apply_info.AAI_ID
        LEFT JOIN scholarships sch ON sch.fsd_aai_id = admission_apply_info.AAI_ID
        LEFT JOIN last_qual lq ON lq.aq_api_id = admission_apply_info.AAI_API_ID
        LEFT JOIN admission_qualification aq ON aq.aq_id = lq.max_aq_id
        LEFT JOIN admission_schedules ads ON admission_apply_info.aai_admission_sch_id = ads.ADMISSION_SCH_ID
        WHERE admission_apply_info.AAI_SDPO_SESS_ID = :p_session_id
          AND ap.API_ALLOW_HOSTEL IS NULL
          AND admission_apply_info.AAI_STATUS = 'Selected'
          AND student_challan.SC_SESSION_TYPE = 'regular_outside'
          AND student_challan.SC_CHALLAN_TYPE = 'Hostel_Fee'";

        // No additional filters needed for external hostel query

        // Add ordering
        $baseQuery .= " ORDER BY CASE WHEN LOWER(ap.API_PERMANENTCITY) = 'lahore' THEN 1 ELSE 0 END, admission_apply_info.AAI_APPLY_DATE";

        // Prepare parameters
        $params = ['p_session_id' => $sessionId];

        // Execute query
        $query = DB::select($baseQuery, $params);

        // Convert to collection for easier processing
        $applicants = collect($query);

        // Filter by gender
        $maleApplicants = $applicants->filter(fn($applicant) => $applicant->api_gender == 'Male');
        $femaleApplicants = $applicants->filter(fn($applicant) => $applicant->api_gender == 'Female');

        // Count allowed applicants by gender
        $maleAllowedCount = $maleApplicants->filter(function ($applicant) {
            return $applicant->sc_challan_status == 'PAID';
        })->count();

        $femaleAllowedCount = $femaleApplicants->filter(function ($applicant) {
            return $applicant->sc_challan_status == 'PAID';
        })->count();

        // Count applicants with challan generated
        $maleChallanCount = $maleApplicants->filter(function ($applicant) {
            return !empty($applicant->sc_challan_status);
        })->count();

        $femaleChallanCount = $femaleApplicants->filter(function ($applicant) {
            return !empty($applicant->sc_challan_status);
        })->count();

        // Count applicants with paid challans
        $malePaidCount = $maleApplicants->filter(function ($applicant) {
            return $applicant->sc_challan_status == 'PAID';
        })->count();

        $femalePaidCount = $femaleApplicants->filter(function ($applicant) {
            return $applicant->sc_challan_status == 'PAID';
        })->count();

        // Count applicants that can be vacated (paid but external)
        $maleVacateCount = $maleApplicants->filter(function ($applicant) {
            return $applicant->sc_challan_status == 'PAID';
        })->count();

        $femaleVacateCount = $femaleApplicants->filter(function ($applicant) {
            return $applicant->sc_challan_status == 'PAID';
        })->count();

        // Return response with same structure as main function
        return response()->json([
            'maleApplicants' => $maleApplicants->toArray(),
            'femaleApplicants' => $femaleApplicants->toArray(),
            'maleCount' => $maleApplicants->count(),
            'femaleCount' => $femaleApplicants->count(),
            'male_vacant_seats' => 0, // Not applicable for external
            'female_vacant_seats' => 0, // Not applicable for external
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

    /**
     * Get consistent seat counts for both applicants and students
     * Optimized to filter by location if provided
     */
    private function getSeatCounts($sessionId, $location = null)
    {
        return app(HostelSeatStatsService::class)->getVacantBedsByGender((int) $sessionId, $location);
    }
    /**
     * Get paid students by gender
     */
    public function getPaidStudentsByGender(Request $request)
    {
        try {
            // Get current academic session
            $currentSession = DB::table('academic_sessions')
                ->where('sess_name', 'NOT LIKE', '%Summer%')
                ->where('sess_name', 'NOT LIKE', '%summer%')
                ->orderBy('sess_id', 'desc')
                ->first();

            if (!$currentSession) {
                return response()->json([
                    'success' => false,
                    'message' => 'No active academic session found'
                ]);
            }
            $sessionId = $currentSession->sess_id;
            $gender = $request->input('gender');
            $hostelTypeId = $request->input('hostel_type_id');
            $hostelLocation = $request->input('hostel_location');

            // Build the query with optional location filter
            $locationFilter = '';
            $params = [
                'p_session_id' => $sessionId,
                'p_gender' => ucfirst($gender)
            ];

            // Add location filter if hostel_type_id and hostel_location are provided
            if ($hostelTypeId && $hostelLocation) {
                // Filter by hostel type location
                // For assigned students: check room's hostel type location matches
                // For unassigned students: show them (can't filter by location since no room assigned)
                $locationFilter = " AND (
                    hb.HB_ID IS NULL 
                    OR EXISTS (
                        SELECT 1 
                        FROM HOSTEL_ROOMS hr2
                        JOIN HOSTEL_TYPE ht2 ON hr2.HR_HT_ID = ht2.HT_ID
                        WHERE hr2.HR_ID = hr.HR_ID
                        AND ht2.HT_LOCATION = :p_hostel_location
                        AND ht2.HT_STATUS = 'E'
                    )
                )";
                $params['p_hostel_location'] = $hostelLocation;
            }

            // Get paid students by gender with hostel room assignment info.
            // Room assignment is resolved from hostel_assign filtered to this session
            // so that a bed occupied in a prior session does not appear as assigned here.
            $students = DB::select("
                SELECT DISTINCT(aai.AAI_ID),
                    aai.CMS_SAI_ENROLLMENT_NO as id,
                    api.API_NAME as name,
                    'PAID' as challan_status,
                    hb.HB_ENROLLMENT_NO as hostel_enrollment,
                    hr.HR_ROOM_NO as room_number,
                    hf.HF_NAME as floor_name,
                    CASE 
                        WHEN ha.HA_HB_ID IS NOT NULL THEN 'Assigned'
                        ELSE 'Not Assigned'
                    END as room_status,
                    CASE 
                        WHEN ha.HA_HB_ID IS NOT NULL THEN 1
                        ELSE 0
                    END as sort_order
                FROM ADMISSION_APPLY_INFO aai
                JOIN ADMISSION_PERSONAL_INFO api ON aai.AAI_API_ID = api.API_ID
                JOIN STUDENT_CHALLAN sc ON aai.AAI_ID = sc.SC_APP_ID
                LEFT JOIN HOSTEL_ASSIGN ha ON ha.HA_AAI_ID = aai.AAI_ID
                    AND ha.HA_SESSION = :p_session_id2
                    AND ha.HA_STATUS = 'E'
                    AND ha.HA_HB_ID IS NOT NULL
                LEFT JOIN HOSTEL_BEDS hb ON ha.HA_HB_ID = hb.HB_ID
                LEFT JOIN HOSTEL_ROOMS hr ON hb.HB_ROOM_ID = hr.HR_ID
                LEFT JOIN HOSTEL_FLOOR hf ON hr.HR_HF_ID = hf.HF_ID
                WHERE aai.AAI_SDPO_SESS_ID = :p_session_id
                AND api.API_ALLOW_HOSTEL = 'Allowed'
                AND api.API_GENDER = :p_gender
                AND aai.AAI_STATUS = 'Selected'
                AND sc.SC_CHALLAN_STATUS = 'PAID'
                AND sc.SC_CHALLAN_TYPE = 'Hostel_Fee'
                " . $locationFilter . "
                ORDER BY 
                    sort_order,
                    api.API_NAME
            ", array_merge($params, ['p_session_id2' => $params['p_session_id']]));

            return response()->json([
                'success' => true,
                'students' => $students,
                'count' => count($students),
                'gender' => $gender
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading paid students: ' . $e->getMessage()
            ]);
        }
    }








    /**
     * Check seat availability for applicants (combined validation for new + old)
     */
    private function checkSeatAvailability($sessionId, $applicantGender, $applicantType = 'new', $location = null)
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

        // dd($gender);
        // 2) Available seats for this gender (and location if specified)
        $seatCounts = $this->getSeatCounts($sessionId, $location);
        $availableSeats = $gender === 'male'
            ? ($seatCounts['male_vacant_seats'] ?? 0)
            : ($seatCounts['female_vacant_seats'] ?? 0);

        // dd($seatCounts);
        // 3) Count already allowed (NEW + OLD) — use DISTINCT to avoid over-counts from joins
        // Exclude PAID challan applicants using subqueries
        $newAllowedQuery = DB::table('admission_personal_info as api')
            ->join('admission_apply_info as aai', 'api.api_formid', '=', 'aai.aai_api_form_id')
            ->join('hostel_assign as ha', 'aai.aai_id', '=', 'ha.ha_aai_id')
            ->leftJoin('student_challan as sc', 'sc.id', '=', 'ha.ha_challan_id')
            ->where('api.api_allow_hostel', 'Allowed')
            ->where('ha.ha_session', $sessionId)
            ->where('ha.ha_status', 'E')
            ->where(function ($q) {
                $q->whereNull('sc.sc_session_type')
                    ->orWhere('sc.sc_session_type', '!=', 'regular_outside');
            })
            ->where('aai.aai_sdpo_sess_id', $sessionId)
            ->whereRaw('LOWER(api.api_gender) = ?', [$gender]);

        if ($location && $location !== 'both') {
            $newAllowedQuery->where('ha.ha_hostel_location_preference', $location);
        }

        $newAllowedCount = $newAllowedQuery->whereNotIn('aai.aai_id', function ($query) use ($sessionId) {
            $query->select('sc_app_id')
                ->from('student_challan')
                ->where('sc_session_id', $sessionId)
                ->where('sc_challan_type', 'Hostel_Fee')
                ->where('sc_challan_status', 'PAID')
                ->whereNotNull('sc_app_id');
        })
            ->distinct('aai.aai_id')
            ->count('aai.aai_id');

        $oldAllowedCount = DB::table('hostel_applicants as happ')
            ->join('students_apply_info as sai', DB::raw('UPPER(happ.happ_enrollment_no)'), '=', DB::raw('UPPER(sai.sai_enrollment_no)'))
            ->join('students_personal_info as spi', 'spi.spi_id', '=', 'sai.sai_spi_id')
            ->join('hostel_assign as ha', 'ha.ha_enrolment_no', '=', 'happ.happ_enrollment_no')
            ->leftJoin('student_challan as sc', 'sc.id', '=', 'ha.ha_challan_id')
            ->where('happ.happ_allowed_status', 'Allowed')
            ->where('happ.happ_session', $sessionId)
            ->where('ha.ha_session', $sessionId)
            ->where('ha.ha_status', 'E');

        // Filter by location preference if provided
        if ($location && $location !== 'both') {
            $oldAllowedCount->where('happ.happ_hostel_location_preference', $location);
        }

        $oldAllowedCount = $oldAllowedCount
            ->where(function ($q) {
                $q->whereNull('sc.sc_session_type')
                    ->orWhere('sc.sc_session_type', '!=', 'regular_outside');
            })
            ->whereRaw('LOWER(spi.spi_gender) = ?', [$gender])
            // Exclude OLD applicants with PAID challans using enrollment_no
            ->whereNotIn(DB::raw('UPPER(happ.happ_enrollment_no)'), function ($query) use ($sessionId) {
                $query->select(DB::raw('UPPER(sc_enrollment_no)'))
                    ->from('student_challan')
                    ->where('sc_session_id', $sessionId)
                    ->where('sc_challan_type', 'Hostel_Fee')
                    ->where('sc_challan_status', 'PAID')
                    ->whereNotNull('sc_enrollment_no');
            })
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
                ->where('ha.ha_session', $sessionId)
                ->where('ha.ha_status', 'E')
                ->where(function ($q) {
                    $q->whereNull('sc.sc_session_type')
                        ->orWhere('sc.sc_session_type', '!=', 'regular_outside');
                })
                ->where('aai.aai_sdpo_sess_id', $sessionId)
                ->whereRaw('LOWER(api.api_gender) = ?', [$gender])
                ->where(function ($q) {
                    $q->where('sc.sc_challan_status', '!=', 'PAID')
                        ->orWhereNull('sc.sc_challan_status');
                })
                // Exclude NEW applicants with PAID challans using aai_id
                ->whereNotIn('aai.aai_id', function ($query) use ($sessionId) {
                    $query->select('sc_app_id')
                        ->from('student_challan')
                        ->where('sc_session_id', $sessionId)
                        ->where('sc_challan_type', 'Hostel_Fee')
                        ->where('sc_challan_status', 'PAID')
                        ->whereNotNull('sc_app_id');
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
                ->where('ha.ha_session', $sessionId)
                ->where('ha.ha_status', 'E')
                ->where(function ($q) {
                    $q->whereNull('sc.sc_session_type')
                        ->orWhere('sc.sc_session_type', '!=', 'regular_outside');
                })
                ->where('happ.happ_session', $sessionId)
                ->whereRaw('LOWER(spi.spi_gender) = ?', [$gender])
                ->where(function ($q) {
                    $q->where('sc.sc_challan_status', '!=', 'PAID')
                        ->orWhereNull('sc.sc_challan_status');
                })
                // Exclude OLD applicants with PAID challans using enrollment_no
                ->whereNotIn(DB::raw('UPPER(happ.happ_enrollment_no)'), function ($query) use ($sessionId) {
                    $query->select(DB::raw('UPPER(sc_enrollment_no)'))
                        ->from('student_challan')
                        ->where('sc_session_id', $sessionId)
                        ->where('sc_challan_type', 'Hostel_Fee')
                        ->where('sc_challan_status', 'PAID')
                        ->whereNotNull('sc_enrollment_no');
                })
                ->distinct('happ.happ_enrollment_no')
                ->count('happ.happ_enrollment_no');

            $totalUnpaidApplicants = $newUnpaidApplicants + $oldUnpaidApplicants;

            $locationText = ($location && $location !== 'both') ? ' for ' . ucwords(str_replace('_', ' ', $location)) : '';
            $message = "Cannot allow more {$genderText} applicants{$locationText}. "
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
        // Get hostel challan directly from STUDENT_CHALLAN table
        $query = DB::table('STUDENT_CHALLAN')
            ->where('SC_SESSION_ID', $sessionId)
            ->where('SC_CHALLAN_TYPE', 'Hostel_Fee')
            ->where('CHECK_FEE_INSTALLMENTS', 0)
            ->where('SC_CHALLAN_STATUS', 'UNPAID');

        // Check if enrollment number is numeric
        if (is_numeric($enrollmentNo)) {
            // If numeric, use SC_APP_ID
            $query->where('SC_APP_ID', $enrollmentNo);
        } else {
            // If not numeric, use SC_ENROLLMENT_NO
            $query->where('SC_ENROLLMENT_NO', $enrollmentNo);
        }

        $hostelChallan = $query->first();
        if (empty($hostelChallan) || empty($hostelChallan->id)) {
            return null;
        }

        // Cancel the challan
        $data = [
            'id' => $hostelChallan->id,
            'cancell_type' => 'CANCELLED',
            'remarks' => 'Auto Cancel Hostel Challan on Applicant Rejected by ' . auth()->user()->username,
            'enrollment_no' => strtoupper($enrollmentNo)
        ];

        $challanRequest = new Request($data);

        try {
            $cancelChallans = new GenerateSingleChallanController();
            $cancelChallans->cancelChallans($challanRequest);
        } catch (Exception $e) {
            \Log::error('Challan cancellation failed: ' . $e->getMessage());
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
            // Cancel challan using common function (if exists)
            $this->cancelHostelChallan($enrollmentNo, $sessionId, 'Reject the new applicant hostel consent');
            // Update the applicant status to Rejected
            $query = "UPDATE admission_personal_info SET api_allow_hostel = 'Rejected' WHERE api_id = :aaiApiId";
            DB::update($query, ['aaiApiId' => $aaiApiId]);

            // Use session ID from request instead of getting from database
            $userId = strtolower(auth()->user()->id);

            // Check if bed is assigned and vacate it
            $assignment = DB::table('hostel_assign')
                ->where('ha_aai_id', $enrollmentNo)
                ->where('ha_session', $sessionId)
                ->first();

            $bedIdToSync = $assignment->ha_hb_id ?? null;

            // Delete hostel assignment record
            DB::table('hostel_assign')
                ->where('ha_aai_id', $enrollmentNo)
                ->where('ha_session', $sessionId)
                ->delete();

            // Sync bed flag AFTER delete so other-session check is accurate.
            if ($bedIdToSync) {
                \App\Services\Hostel\HostelSeatStatsService::syncBedPhysicalStatus((int) $bedIdToSync);
            }
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Applicant rejected successfully']);
        } catch (Exception $e) {
            DB::rollBack();

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

            // Update the applicant status to Rejected
            $query = "UPDATE hostel_applicants SET happ_allowed_status = 'Rejected' WHERE UPPER(happ_enrollment_no) = UPPER(:enrollmentNo)";
            DB::update($query, ['enrollmentNo' => $enrollmentNo]);

            // Use session ID from request instead of getting from database
            $userId = strtolower(auth()->user()->id);

            // Check if bed is assigned and vacate it
            $assignment = DB::table('hostel_assign')
                ->where('ha_enrolment_no', $enrollmentNo)
                ->where('ha_session', $sessionId)
                ->first();

            $bedIdToSync = $assignment->ha_hb_id ?? null;

            // Delete hostel assignment record
            DB::table('hostel_assign')
                ->where('ha_enrolment_no', $enrollmentNo)
                ->where('ha_session', $sessionId)
                ->delete();

            // Sync bed flag AFTER delete so other-session check is accurate.
            if ($bedIdToSync) {
                \App\Services\Hostel\HostelSeatStatsService::syncBedPhysicalStatus((int) $bedIdToSync);
            }

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Hostel applicant rejected successfully']);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json(['success' => false, 'message' => 'Error rejecting applicant: ' . $e->getMessage()]);
        }
    }

    public function vacateHostel()
    {
        $username = strtolower(auth()->user()->username);
        $hostel_type = DB::table('HOSTEL_TYPE')
            ->where('HT_STATUS', 'E')
            ->select('ht_id', 'ht_name', 'ht_status', 'ht_location')
            ->get();

        // Floors ko hostel type ke saath load karein
        $floors_de = DB::table('HOSTEL_FLOOR')
            ->where('HF_STATUS', 'E')
            ->select('hf_id', 'hf_name', 'HF_HOSTEL_TYPE', 'HF_NEW_HOSTEL_TYPE')
            ->get();

        // Fetch academic sessions (excluding summer sessions)
        $academic_sessions = DB::table('academic_sessions')
            ->where('sess_name', 'NOT LIKE', '%Summer%')
            ->where('sess_name', 'NOT LIKE', '%summer%')
            ->orderBy('sess_id', 'desc')
            ->get();

        // Get current academic session using getAcademicYear function
        $current_academic_session = $this->getAcademicYear();

        // Fetch all rooms
        $data = DB::table('hostel_rooms')->get();

        // Fetch all beds
        $beds = DB::table('hostel_beds')->get();

        // Add beds to the corresponding rooms
        foreach ($data as $room) {
            $room->beds = $beds->where('hb_room_id', $room->hr_id)->values();
        }

        $rooms = json_decode($data, true);
        $floors = json_decode($floors_de, true);

        if (json_last_error() === JSON_ERROR_NONE) {
            $rooms;
        } else {
            return response()->json(['error' => 'Invalid JSON data'], 400);
        }



        return view('Hostel.vacate_bed', compact('floors_de', 'rooms', 'hostel_type', 'academic_sessions', 'current_academic_session'));
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

            // Extract numeric part from room number (e.g., "Room 101" -> 101)
            $roomNumberInput = $request->input('room_number');
            preg_match('/\d+/', $roomNumberInput, $matches);

            if (empty($matches)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Room number must contain at least one numeric digit.'
                ], 422);
            }

            $roomNumber = (int) $matches[0]; // Convert to integer for NUMBER column

            if ($request->filled('room_id')) {
                // UPDATE existing room
                $roomId = $request->input('room_id');

                DB::table('HOSTEL_ROOMS')
                    ->where('HR_ID', $roomId)
                    ->update([
                        'HR_HT_ID' => $request->input('hostel_type'),
                        'HR_ROOM_NO' => $roomNumber,
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
                    // Get hb_ids of the beds that would be removed (those beyond new capacity).
                    $extraBedIds = DB::table('HOSTEL_BEDS')
                        ->where('HB_ROOM_ID', $roomId)
                        ->where('HB_BED_NUMBER', '>', $newCapacity)
                        ->pluck('hb_id');

                    // A bed is considered occupied when it has ANY active assignment in ANY
                    // session — we must not delete a bed that has history records.
                    $occupiedCount = DB::table('hostel_assign')
                        ->whereIn('ha_hb_id', $extraBedIds)
                        ->where('ha_status', 'E')
                        ->count();

                    if ($occupiedCount > 0) {
                        DB::rollBack();
                        return response()->json([
                            'success' => false,
                            'message' => 'Room capacity cannot be reduced. Some of the extra beds are already assigned in an active session.'
                        ]);
                    }

                    // Delete only beds that have no assignment history.
                    DB::table('HOSTEL_BEDS')
                        ->whereIn('hb_id', $extraBedIds)
                        ->where('HB_IS_OCCUPIED', 'N')
                        ->delete();
                }
            } else {
                // INSERT new room
                $roomId = DB::table('HOSTEL_ROOMS')->insertGetId([
                    'HR_HT_ID' => $request->input('hostel_type'),
                    'HR_ROOM_NO' => $roomNumber,
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
        $bedId    = $request->input('id');
        // session_id is now required so the vacate is scoped to the correct session.
        // Frontend must pass the currently-selected session.
        $sessionId = $request->input('session_id');

        try {
            DB::beginTransaction();

            if ($sessionId) {
                // Null-out the bed reference only for the given session so that
                // assignments in other sessions remain intact (history is preserved).
                DB::table('hostel_assign')
                    ->where('ha_hb_id', $bedId)
                    ->where('ha_session', $sessionId)
                    ->update([
                        'ha_hb_id'         => null,
                        'ha_entry_user_id' => $username,
                    ]);
            } else {
                // Fallback: clear across ALL sessions (legacy behaviour when no session passed).
                DB::table('hostel_assign')
                    ->where('ha_hb_id', $bedId)
                    ->update([
                        'ha_hb_id'         => null,
                        'ha_entry_user_id' => $username,
                    ]);
            }

            // Smart sync via shared service — keeps hb_is_occupied accurate
            // across all sessions without touching other sessions' data.
            \App\Services\Hostel\HostelSeatStatsService::syncBedPhysicalStatus((int) $bedId);

            DB::commit();
            return response()->json(['message' => 'Bed status updated successfully']);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to vacate bed: ' . $e->getMessage()], 500);
        }
    }

    public function occupyBedStatus(Request $request)
    {
        /*
         * STEP-WISE PROCESS FOR OCCUPYING A HOSTEL BED:
         * 
         * Step 1: Validate input data (bed_id, student_input) and get current academic session
         * Step 2: Check if bed is already occupied
         * Step 3: Determine if student_input is numeric (app_id) or character (enrollment_no)
         * Step 4: Get both app_id and enrollment_no using helper functions
         * Step 5: Check challan status - ensure hostel fee is PAID for the session
         * Step 6: Check if record exists in hostel_assign against app_id
         * Step 7: Check/Insert into hostel_applicants table if missing
         * Step 8: Check if student already has assignment in any room for this session
         * Step 9: Update existing hostel_assign record with bed_id and missing fields
         * Step 10: Update HOSTEL_BEDS table to mark bed as occupied
         * 
         * All operations are wrapped in database transaction for data integrity
         */

        // Step 1: Validate input data (bed_id, student_input)
        $validated = $request->validate([
            'bed_id' => 'required|integer|exists:HOSTEL_BEDS,hb_id',
            'student_input' => 'required|string|max:50'
        ]);

        $username = strtolower(auth()->user()->employee_id);
        $bedId = $validated['bed_id'];
        $studentInput = $validated['student_input'];

        // Get current academic session using getAcademicYear function
        $academicData = $this->getAcademicYear();

        if (!$academicData->sess_id) {
            return response()->json(['error' => 'No academic session found'], 400);
        }

        $sessionId = $academicData->sess_id;
        // dd($sessionId);
        try {
            DB::beginTransaction();

            // Step 2: Check if bed is already assigned in THIS session (session-aware check).
            // We intentionally do NOT rely on the global hb_is_occupied flag here because that
            // flag is shared across sessions; a bed occupied in session 167 must still be
            // assignable in session 168 so that history is preserved in hostel_assign.
            $bed = DB::table('HOSTEL_BEDS')->where('hb_id', $bedId)->first();
            if (!$bed) {
                return response()->json(['error' => 'Bed not found'], 404);
            }

            $existingSessionAssignment = DB::table('hostel_assign')
                ->where('ha_hb_id', $bedId)
                ->where('ha_session', $sessionId)
                ->where('ha_status', 'E')
                ->first();

            if ($existingSessionAssignment) {
                return response()->json(['error' => 'Bed is already occupied in this session'], 400);
            }

            // Step 3: Determine if student_input is numeric (app_id) or character (enrollment_no)
            $challanController = new GenerateSingleChallanController();
            $isCharacter = !is_numeric($studentInput);

            // Step 4: Get both app_id and enrollment_no using helper functions
            if ($isCharacter) {
                $appId = $challanController->getAppIdByEnrollmentNumber((strtoupper($studentInput)));
                $enrollmentNo = $studentInput; // Keep the original enrollment number
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

            $maxSessId = DB::table('student_courses')
                ->where('SC_SAI_ID', $appId)
                ->max('SC_CO_SESS_ID');


            // Step 5: Check challan status - ensure hostel fee is PAID for the session
            $challanStatus = DB::table('student_challan')
                ->where('sc_app_id', $appId)
                ->where('sc_challan_type', 'Hostel_Fee')
                ->where('sc_session_id', $maxSessId)
                ->where('sc_challan_status', 'PAID')
                ->orderBy('id', 'desc')
                ->value('sc_challan_status');

            if ($challanStatus !== 'PAID') {
                DB::rollBack();
                return response()->json(['error' => 'Hostel paid fee challan not found'], 400);
            }

            // Step 6: Check if record exists in hostel_assign against app_id
            $existingAssignByAppId = DB::table('hostel_assign')
                ->where(function ($query) use ($appId, $enrollmentNo) {
                    $query->where('ha_aai_id', $appId)
                        ->orWhere('ha_enrolment_no', $enrollmentNo);
                })
                ->where('ha_session', $sessionId)
                ->first();

            // Step 7: Check/Insert into hostel_applicants table if missing
            $hostelApplicant = DB::table('hostel_applicants')
                ->where('happ_enrollment_no', $enrollmentNo)
                ->where('happ_allowed_status', 'Allowed')
                ->where('happ_session', $sessionId)
                ->first();

            if (!$hostelApplicant) {
                // Insert new entry in hostel_applicants table
                $insertApplicant = DB::table('hostel_applicants')->insert([
                    'happ_enrollment_no' => $enrollmentNo,
                    'happ_allowed_status' => 'Allowed',
                    'happ_session' => $sessionId,
                    'happ_entry_date' => now(),
                    'happ_entry_user_id' => $username
                ]);

                if (!$insertApplicant) {
                    DB::rollBack();
                    return response()->json(['error' => 'Failed to insert hostel applicant record'], 500);
                }
            }

            // dd($existingAssignByAppId);

            // Step 8: Check if student already has assignment in any room for this session
            $existingAssign = DB::table('hostel_assign')
                ->join('HOSTEL_BEDS', 'hostel_assign.ha_hb_id', '=', 'HOSTEL_BEDS.hb_id')
                ->join('HOSTEL_ROOMS', 'HOSTEL_BEDS.hb_room_id', '=', 'HOSTEL_ROOMS.hr_id')
                ->where('hostel_assign.ha_enrolment_no', $enrollmentNo)
                ->where('hostel_assign.ha_session', $sessionId)
                ->where('hostel_assign.ha_status', 'E')

                ->first();
            //dd($existingAssign);

            if ($existingAssign) {
                // Get building name based on hostel type
                $buildingName = $existingAssign->hr_ht_id == 1 ? 'Boys Hostel' : 'Girls Hostel';

                DB::rollBack();
                return response()->json([
                    'error' => "Student already has an entry in {$buildingName}, Room {$existingAssign->hr_room_no} for this session."
                ], 400);
            }

            // Step 9: Update existing hostel_assign record with bed_id and missing fields
            $assignRecord = DB::table('hostel_assign')
                ->where(function ($query) use ($enrollmentNo, $appId) {
                    $query->where('ha_enrolment_no', $enrollmentNo)
                        ->orWhere('ha_aai_id', $appId);
                })
                ->where('ha_session', $sessionId)
                ->first();

            // dd($assignRecord  );
            if ($assignRecord === null) {

                // 👉 INSERT
                $insert = DB::table('hostel_assign')->insert([
                    'ha_enrolment_no' => $enrollmentNo,
                    // 'ha_aai_id'         => $appId,
                    'ha_hb_id' => $bedId,
                    'ha_session' => $sessionId,
                    'ha_entry_user_id' => $username

                ]);

                if (!$insert) {
                    DB::rollBack();
                    return response()->json(['error' => 'Failed to insert hostel assignment'], 500);
                }

            } else {
                // if ($assignRecord) {
                // Update existing record - ensure both enrollment and app_id are present
                $updateData = [
                    'ha_hb_id' => $bedId,
                    'ha_entry_user_id' => $username
                ];

                // Add missing fields
                if (empty($assignRecord->ha_enrolment_no)) {
                    $updateData['ha_enrolment_no'] = $enrollmentNo;
                }
                if (empty($assignRecord->ha_aai_id)) {
                    $updateData['ha_aai_id'] = $appId;
                }

                $assignUpdate = DB::table('hostel_assign')
                    ->where(function ($query) use ($enrollmentNo, $appId) {
                        $query->where('ha_enrolment_no', $enrollmentNo)
                            ->orWhere('ha_aai_id', $appId);
                    })
                    ->where('ha_session', $sessionId)
                    ->update($updateData);

                if (!$assignUpdate) {
                    DB::rollBack();
                    return response()->json(['error' => 'Failed to update hostel assignment'], 500);
                }
            }
            // Step 10: Update HOSTEL_BEDS table to mark bed as occupied
            $bedUpdate = DB::table('HOSTEL_BEDS')
                ->where('hb_id', $bedId)
                ->update([
                    'hb_is_occupied' => 'Y',
                    'hb_enrollment_no' => $enrollmentNo
                ]);

            if (!$bedUpdate) {
                DB::rollBack();
                return response()->json(['error' => 'Failed to update bed status'], 500);
            }

            DB::commit();

            return response()->json(['message' => 'Bed occupied successfully']);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'An error occurred while occupying the bed: ' . $e->getMessage()], 500);
        }
    }
    // Function to enable the room
    public function enableRoom($id)
    {
        $status = DB::table('hostel_rooms')
            ->where('hr_id', $id)
            ->update(['hr_status' => 'E']);
        // dd($status);
        if ($status) {
            return response()->json(['success' => 'Room enabled successfully']);
        } else
            return response()->json(['error' => 'not updated'], 404);
    }

    // Function to disable the room
    public function disableRoom($id)
    {

        $status = DB::table('hostel_rooms')
            ->where('hr_id', $id)
            ->update(['hr_status' => 'D']);
        // dd($status);
        if ($status) {
            return response()->json(['success' => 'Room enabled successfully']);
        } else
            return response()->json(['error' => 'not updated'], 404);
    }


    // Function to enable the room
    public function enableAC($id)
    {
        $status = DB::table('hostel_rooms')
            ->where('hr_id', $id)
            ->update(['hr_ac_availability' => 'E']);
        // dd($status);
        if ($status) {
            return response()->json(['success' => 'Room enabled successfully']);
        } else
            return response()->json(['error' => 'not updated'], 404);
    }

    // Function to disable the room
    public function disableAC($id)
    {

        $status = DB::table('hostel_rooms')
            ->where('hr_id', $id)
            ->update(['hr_ac_availability' => 'D']);
        // dd($status);
        if ($status) {
            return response()->json(['success' => 'Room enabled successfully']);
        } else
            return response()->json(['error' => 'not updated'], 404);
    }





    public function showFacilities()
    {

        $sessions = DB::table('academic_sessions')
            ->select('SESS_ID', 'SESS_NAME')
            ->orderBy('SESS_ID', 'desc')
            ->get();
        $hostelNames = DB::table('hostel_facilities')
            ->select('HF_NAME')
            ->distinct()
            ->get();



        $facilities = DB::table('HOSTEL_FACILITIES')->get();

        return view('Hostel.hostel_facilities', compact('sessions', 'facilities', 'hostelNames'));
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
            ->take(3)
            ->get();

        // Get all active hostel types for dropdown
        $hostelTypes = DB::table('HOSTEL_TYPE')
            ->where('HT_STATUS', 'E')
            ->select('HT_ID as ht_id', 'HT_NAME as ht_name', 'HT_LOCATION as ht_location')
            ->orderBy('HT_NAME', 'asc')
            ->get();

        // Get all visibility periods
        $data = DB::table('hostel_form_visibility_periods')
            ->leftJoin('academic_sessions', 'hostel_form_visibility_periods.sess_id', '=', 'academic_sessions.sess_id')
            ->select(
                'hostel_form_visibility_periods.id',
                'hostel_form_visibility_periods.start_date',
                'hostel_form_visibility_periods.end_date',
                'hostel_form_visibility_periods.sess_id',
                'hostel_form_visibility_periods.visibility_type',
                'hostel_form_visibility_periods.hostel_type_id',
                'hostel_form_visibility_periods.due_date',
                'hostel_form_visibility_periods.valid_date',
                'hostel_form_visibility_periods.remarks',
                'academic_sessions.sess_name'
            )
            ->orderBy('sess_id', 'desc')
            ->get();

        // Get hostel type names and locations for each record
        // hostel_type_id can be comma-separated string like "1,2,3" or single value "1" or "0"
        foreach ($data as $record) {
            $hostelTypeId = $record->hostel_type_id;

            // Check if empty or 0
            if (empty($hostelTypeId) || $hostelTypeId == '0') {
                $record->hostel_type_name = 'All Hostels';
                $record->hostel_type_location = 'All Locations';
            } else {
                // Split comma-separated values
                $hostelIds = explode(',', $hostelTypeId);
                $hostelNames = array();
                $hostelLocations = array();

                // Get name and location for each hostel id
                foreach ($hostelIds as $id) {
                    $id = trim($id);
                    if ($id != '' && $id != '0') {
                        $hostelType = DB::table('HOSTEL_TYPE')
                            ->where('HT_ID', $id)
                            ->select('HT_NAME as ht_name', 'HT_LOCATION as ht_location')
                            ->first();
                        if ($hostelType) {
                            $hostelNames[] = $hostelType->ht_name;

                            // Format location
                            $locationText = 'on campus';
                            if ($hostelType->ht_location == 'off_campus' || $hostelType->ht_location == 'out_of_campus') {
                                $locationText = 'off campus';
                            }
                            $hostelLocations[] = $locationText;
                        }
                    }
                }

                // Join all names with comma
                if (count($hostelNames) > 0) {
                    $record->hostel_type_name = implode(', ', $hostelNames);
                    $record->hostel_type_location = implode(', ', $hostelLocations);
                } else {
                    $record->hostel_type_name = 'N/A';
                    $record->hostel_type_location = 'N/A';
                }
            }
        }

        return view('Hostel.hostel_form_visibility', compact('session', 'data', 'hostelTypes'));
    }

    public function submit_hostel_form_visibility(Request $request)
    {
        try {
            $request->validate([
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'session' => 'required|numeric',
                'visibility_type' => 'required|in:admission_applicants,regular_students,already_existing_hostel_students',
                'hostel_type_id' => 'required|array',
                'hostel_type_id.*' => 'numeric',
                'due_date' => 'required|date|after_or_equal:start_date',
                'valid_date' => 'required|date|after_or_equal:start_date',
                'remarks' => 'nullable|string|max:500',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Return JSON error response if AJAX request
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }
            // For normal form submission, Laravel will redirect with errors
            throw $e;
        }

        try {
            DB::beginTransaction();

            // Get hostel type ids from request (array from Select2)
            $hostelTypeIds = $request->input('hostel_type_id');

            // Convert array to comma-separated string
            $hostelTypeIdString = '0'; // Default: All Hostels

            // Check if hostel_type_id exists and is array
            if ($hostelTypeIds && is_array($hostelTypeIds) && count($hostelTypeIds) > 0) {
                // Check if "All Hostels" (0) is in the array
                $hasAllHostels = in_array('0', $hostelTypeIds) || in_array(0, $hostelTypeIds);

                if ($hasAllHostels) {
                    // If "All Hostels" is selected, use only "0"
                    $hostelTypeIdString = '0';
                } else {
                    // Remove empty values and convert to comma-separated string
                    $filteredIds = array();
                    foreach ($hostelTypeIds as $id) {
                        $id = trim($id);
                        if ($id != '' && $id != '0') {
                            $filteredIds[] = $id;
                        }
                    }

                    // If any specific hostel selected, use them
                    if (count($filteredIds) > 0) {
                        $hostelTypeIdString = implode(',', $filteredIds);
                    }
                }
            }

            $data = [
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'sess_id' => $request->session,
                'visibility_type' => $request->visibility_type,
                'hostel_type_id' => $hostelTypeIdString,
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

            // Return JSON error response if AJAX request
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => 'An error occurred: ' . $e->getMessage()
                ], 500);
            }

            // Return redirect with error for normal form submission
            return redirect()->back()
                ->withInput()
                ->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function delete_hostel_form_visibility(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
        ]);

        try {
            DB::beginTransaction();

            $deleted = DB::table('hostel_form_visibility_periods')
                ->where('id', $request->id)
                ->delete();

            DB::commit();

            if ($deleted) {
                return response()->json([
                    'success' => true,
                    'message' => 'Record deleted successfully.',
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Record not found.',
            ], 404);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ], 500);
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
            $bedNo = (int) $bedNo;
            $roomNo = (int) $roomNo;

            $procedureName = 'ASSIGN_HOSTEL_Procedure';
            $bindings = [
                'p_enrollment_no' => (string) $enrollmentNo,
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


    public function hostelUpdateCheckinAndCheckoutDate()
    {
        $academicYear = DB::table('academic_sessions')
            ->select('SESS_ID', 'SESS_NAME')
            ->orderBy('SESS_ID', 'desc')
            ->get();

        return view('Hostel.hostel_update_checkin_and_checkout_date', compact('academicYear'));
    }
    public function showCheckinCheckout()
    {
        if (request()->ajax()) {
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
                ->where(function ($query) use ($request) {
                    $query->where('HAH_STUDENT_ENROLL_NO', $request->student_enroll_no)
                        ->orWhere('HAH_APP_ID', $request->app_id);
                });

            // If updating, exclude current record from duplicate check
            if ($request->id) {
                $duplicateCheck->where('ID', '!=', $request->id);
            }

            $duplicate = $duplicateCheck->first();

            if ($duplicate) {
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

            if ($request->id) {
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



    function getAcademicYear($excludePattern = '%SUM%')
    {
        // Query to get the academic year by the maximum SESS_ID
        $academicData = DB::table('academic_sessions')
            ->where('SESS_ID', function ($query) {
                $query->select(DB::raw('MAX(SESS_ID)'))
                    ->from('academic_sessions');
            })
            ->where('SESS_NAME', 'not like', $excludePattern)
            ->select('SESS_NAME', 'SESS_ID')
            ->first();

        return $academicData; // Return the academic data
    }


     /**
     * Session-wise lookup CTEs shared by hostel applicant queries.
     * Each CTE is scanned once per request, then joined — not once per applicant row.
     */
     private function hostelApplicantsLookupCtesSql()
     {
         return "
             hostel_challan AS (
                 SELECT sc_app_id, MAX(sc_challan_status) AS sc_challan_status
                 FROM student_challan
                 WHERE sc_session_id = :p_session_id
                   AND sc_challan_type = 'Hostel_Fee'
                 GROUP BY sc_app_id
             ),
             fs_challan AS (
                 SELECT
                     sc_app_id,
                     CASE
                         WHEN MAX(NVL(check_fee_installments, 0)) >= 1 THEN
                             CASE
                                 WHEN SUM(CASE WHEN NVL(check_fee_installments, 0) = 1 AND sc_challan_status = 'PAID' THEN 1 ELSE 0 END) = 0
                                     THEN 'UNPAID'
                                 WHEN SUM(CASE WHEN sc_challan_status = 'PAID' THEN 1 ELSE 0 END) = COUNT(*)
                                     THEN 'PAID'
                                 ELSE 'PARTIAL_PAID'
                             END
                         ELSE
                             CASE
                                 WHEN SUM(CASE WHEN sc_challan_status = 'PAID' THEN 1 ELSE 0 END) = COUNT(*)
                                     THEN 'PAID'
                                 WHEN SUM(CASE WHEN sc_challan_status = 'PAID' THEN 1 ELSE 0 END) > 0
                                     THEN 'PARTIAL_PAID'
                                 ELSE 'UNPAID'
                             END
                     END AS full_semester_challan_status
                 FROM student_challan
                 WHERE sc_session_id = :p_session_id
                   AND UPPER(TRIM(sc_challan_type)) = 'FULL_SEMESTER'
                   AND sc_student_status = 'New Admission FALL'
                   AND NVL(sc_challan_status, 'X') != 'CANCELLED'
                   AND NVL(invoice_status, 'E') = 'E'
                 GROUP BY sc_app_id
             ),
             scholarships AS (
                 SELECT
                     fsd.FSD_AAI_ID,
                     LISTAGG(
                         st.SCHOLARSHIP_NAME || ': ' ||
                         TO_CHAR(ROUND(fsd.FSD_DISCOUNT_PERCENTAGE * 100, 2)) || '%',
                         ', '
                     ) WITHIN GROUP (ORDER BY st.SCHOLARSHIP_NAME) AS scholarship_details
                 FROM fin_scholarship_discounts fsd
                 LEFT JOIN scholarship_type st
                        ON fsd.FSD_SCHOLARSHIP_TYPE = st.SCHOLARSHIP_ID
                 WHERE fsd.FSD_SESION_ID = :p_session_id
                 GROUP BY fsd.FSD_AAI_ID
             ),
             last_qual AS (
                 SELECT aq.aq_api_id, MAX(aq.aq_id) AS max_aq_id
                 FROM admission_qualification aq
                 WHERE EXISTS (
                     SELECT 1
                     FROM admission_apply_info aa2
                     JOIN admission_personal_info ap2 ON aa2.aai_api_id = ap2.api_id
                     WHERE aa2.aai_api_id = aq.aq_api_id
                       AND aa2.aai_status = 'Selected'
                       AND ap2.api_ishostel = 'Yes'
                       AND aa2.aai_sdpo_sess_id = :p_session_id
                 )
                 GROUP BY aq.aq_api_id
             )
         ";
     }

}
