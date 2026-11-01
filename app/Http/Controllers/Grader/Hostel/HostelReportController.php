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

Class HostelReportController{




    /**
     * Get consistent seat counts for both applicants and students
     */
    private function getSeatCounts($sessionId)
    {
        // Count total vacant male hostel beds (this is the available seats)
        // Use floor's new hostel type like vacate_bed.blade.php does for filtering floors
        $male_vacant_seats = DB::table('hostel_beds')
            ->join('hostel_rooms', 'hostel_beds.hb_room_id', '=', 'hostel_rooms.hr_id')
            ->join('hostel_floor', 'hostel_rooms.hr_hf_id', '=', 'hostel_floor.hf_id')
            ->where('hostel_floor.hf_new_hostel_type', 1) // Male hostel type (new hostel type)
            ->where('hostel_beds.hb_is_occupied', 'N')
            ->where('hostel_rooms.hr_status', 'E')
            ->where('hostel_floor.hf_status', 'E')
            ->count('hostel_beds.hb_bed_number');

        // Count total vacant female hostel beds (this is the available seats)
        // Use floor's new hostel type like vacate_bed.blade.php does for filtering floors
        $female_vacant_seats = DB::table('hostel_beds')
            ->join('hostel_rooms', 'hostel_beds.hb_room_id', '=', 'hostel_rooms.hr_id')
            ->join('hostel_floor', 'hostel_rooms.hr_hf_id', '=', 'hostel_floor.hf_id')
            ->where('hostel_floor.hf_new_hostel_type', 2) // Female hostel type (new hostel type)
            ->where('hostel_beds.hb_is_occupied', 'N')
            ->where('hostel_rooms.hr_status', 'E')
            ->where('hostel_floor.hf_status', 'E')
            ->count('hostel_beds.hb_bed_number');

        return [
            'male_vacant_seats' => $male_vacant_seats,
            'female_vacant_seats' => $female_vacant_seats
        ];
    }




    public function generateHostelPDFReport()
    {
        // Include FPDF library
        require_once(public_path('fpdf/fpdf.php'));

        // Create PDF object
        $pdf = new \FPDF('P', 'mm', 'A4');

        // Set PDF metadata for proper tab title
        $pdf->SetTitle('Hostel Management Report');
        $pdf->SetAuthor('BNU Hostel Management System');
        $pdf->SetSubject('Hostel Statistics and Room Assignment Report');

        // Set auto page break
        $pdf->SetAutoPageBreak(true, 20);

        // Enable page numbering
        $pdf->AliasNbPages();

        // Add first page
        $pdf->AddPage();

        // Add header
        $this->addHostelReportHeader($pdf);

        // Get current session
        $currentSession = DB::table('academic_sessions')
            ->where('sess_name', 'NOT LIKE', '%Summer%')
            ->where('sess_name', 'NOT LIKE', '%summer%')
            ->orderBy('sess_id', 'desc')
            ->first();

        $sessionId = $currentSession ? $currentSession->sess_id : 165; // Default to 165 if no session found
        $sessionName = $currentSession ? $currentSession->sess_name : 'Session 165'; // Get session name

        // Get all hostel statistics
        $stats = $this->getHostelStatistics($sessionId);

        // Add statistics to PDF
        $this->addHostelStatistics($pdf, $stats, $sessionId, $sessionName);

        // Output PDF for inline viewing
        $filename = 'Hostel_Management_Report_Session_' . $sessionId . '_' . date('Y-m-d') . '.pdf';
        $pdf->Output($filename, 'I'); // 'I' for inline viewing
    }

    private function addHostelReportHeader($pdf)
    {
        // BNU Logo
        $pdf->Image(public_path('fpdf/logoimages/bnulogo1.jpg'), 15, 5, 15, 20);

        // Letter Head
        $pdf->SetXY(35, 10);
        $pdf->SetFont('times', 'B', 18);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(0, 0, 'Beaconhouse National University', 0, 0, 'L');

        $pdf->SetXY(35, 17);
        $pdf->SetFont('times', '', 12);
        $pdf->Cell(0, 0, '13-km Raiwind Road, Lahore 54400, Pakistan', 0, 0, 'L');

        $pdf->SetXY(35, 22);
        $pdf->Cell(0, 5, 'Tel: 92-42-38100156 Email: info@bnu.edu.pk', 0, 1, 'L');

        $pdf->ln(1);
        $pdf->Ln();

        // Report Title
        $pdf->SetFont('Arial', 'B', 18);
        $pdf->Cell(0, 12, 'BNU Hostel Report', 0, 1, 'C');

        $pdf->SetY(50);
    }

    private function getHostelStatistics($sessionId)
    {
        // Get seat counts
        $seatCounts = $this->getSeatCounts($sessionId);


        // Get students data
        $students = DB::select("
            SELECT
                happ.happ_allowed_status,
                happ.happ_enrollment_no,
                spi.SPI_NAME AS student_name,
                spi.SPI_GENDER AS gender,
                happ.happ_entry_date AS apply_date,
                happ.happ_session AS session_id,
                scc.sc_challan_status AS sc_challan_status,
                scc.id AS challan_id
            FROM hostel_applicants happ
            LEFT JOIN students_apply_info sai ON UPPER(happ.happ_enrollment_no) = UPPER(sai.sai_enrollment_no)
            LEFT JOIN students_personal_info spi ON spi.spi_id = sai.sai_spi_id
            LEFT JOIN (
                SELECT
                    sc_enrollment_no,
                    sc_challan_status,
                    id,
                    ROW_NUMBER() OVER (PARTITION BY sc_enrollment_no ORDER BY id DESC) as rn
                FROM student_challan
                WHERE SC_SESSION_ID = :p_session_id
                  AND SC_CHALLAN_TYPE = 'Hostel_Fee'
                  AND CHECK_FEE_INSTALLMENTS = 0
            ) scc ON UPPER(scc.sc_enrollment_no) = UPPER(happ.happ_enrollment_no) AND scc.rn = 1
            WHERE happ.happ_session = :p_session_id
        ", ['p_session_id' => $sessionId]);

        $students = collect($students);

        // Get new applicants data
        $newApplicants = DB::select("
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
                ap.API_GENDER,
                aa.aai_apply_date,
                ap.API_ISHOSTEL,
                ha.ha_disable_map_display,
                (
                    SELECT MAX(scc.sc_challan_status)
                    FROM student_challan scc
                    WHERE scc.SC_SESSION_ID = :p_session_id
                      AND scc.SC_CHALLAN_TYPE = 'Hostel_Fee'
                      AND scc.CHECK_FEE_INSTALLMENTS = 0
                      AND UPPER(scc.sc_enrollment_no) = UPPER(aa.CMS_SAI_ENROLLMENT_NO)
                ) AS sc_challan_status
            FROM admission_apply_info aa
            LEFT JOIN admission_personal_info ap ON aa.AAI_API_ID = ap.API_ID
            LEFT JOIN hostel_assign ha ON aa.AAI_ID = ha.HA_AAI_ID AND ha.HA_SESSION = :p_session_id
            WHERE aa.AAI_SDPO_SESS_ID = :p_session_id
              AND ap.API_ISHOSTEL = 'Y'
        ", ['p_session_id' => $sessionId]);

        $newApplicants = collect($newApplicants);

        // Calculate statistics
        $maleStudents = $students->filter(fn($s) => strtolower($s->gender) == 'male');
        $femaleStudents = $students->filter(fn($s) => strtolower($s->gender) == 'female');

        $maleApplicants = $newApplicants->filter(fn($a) => strtolower($a->API_GENDER) == 'male');
        $femaleApplicants = $newApplicants->filter(fn($a) => strtolower($a->API_GENDER) == 'female');

        // Counts - Allowed students (excluding paid ones)
        $maleAllowedCount = $maleStudents->filter(fn($s) => $s->happ_allowed_status == 'Allowed' && $s->sc_challan_status != 'PAID')->count();
        $femaleAllowedCount = $femaleStudents->filter(fn($s) => $s->happ_allowed_status == 'Allowed' && $s->sc_challan_status != 'PAID')->count();

        // Challan status counts using proper SQL logic
        // Male Paid Count
        $malePaidCount = DB::select("
            SELECT COUNT(*) as paid_count
            FROM STUDENT_CHALLAN
            WHERE SC_SESSION_ID = ?
              AND SC_CHALLAN_TYPE = 'Hostel_Fee'
              AND SC_CHALLAN_STATUS = 'PAID'
            AND (SC_SESSION_TYPE = 'regular' OR SC_SESSION_TYPE IS NULL)
             AND SC_HOSTEL_CATEGORY = 'BH'

        ", [$sessionId])[0]->paid_count;

        // Female Paid Count
        $femalePaidCount = DB::select("
            SELECT COUNT(*) as paid_count
            FROM STUDENT_CHALLAN
            WHERE SC_SESSION_ID = ?
              AND SC_CHALLAN_TYPE = 'Hostel_Fee'
              AND SC_CHALLAN_STATUS = 'PAID'
              AND (SC_SESSION_TYPE = 'regular' OR SC_SESSION_TYPE IS NULL)
             AND SC_HOSTEL_CATEGORY = 'GH'

        ", [$sessionId])[0]->paid_count;



          // male Paid Count out side
            $malePaidCount_out_side = DB::select("
            SELECT COUNT(*) as paid_count
            FROM STUDENT_CHALLAN
            WHERE SC_SESSION_ID = ?
              AND SC_CHALLAN_TYPE = 'Hostel_Fee'
              AND SC_CHALLAN_STATUS = 'PAID'
            AND SC_SESSION_TYPE = 'regular_outside'
             AND SC_HOSTEL_CATEGORY = 'BH'

        ", [$sessionId])[0]->paid_count;

        // Female Paid Count out side
        $femalePaidCount_out_side = DB::select("
            SELECT COUNT(*) as paid_count
            FROM STUDENT_CHALLAN
            WHERE SC_SESSION_ID = ?
              AND SC_CHALLAN_TYPE = 'Hostel_Fee'
              AND SC_CHALLAN_STATUS = 'PAID'
              AND SC_SESSION_TYPE = 'regular_outside'
             AND SC_HOSTEL_CATEGORY = 'GH'

        ", [$sessionId])[0]->paid_count;







        // Male Unpaid Count
        $maleUnpaidCount = DB::select("
            SELECT COUNT(*) as unpaid_count
            FROM STUDENT_CHALLAN
            WHERE SC_SESSION_ID = ?
              AND SC_CHALLAN_TYPE = 'Hostel_Fee'
              AND SC_CHALLAN_STATUS = 'UNPAID'
              AND SC_APP_ID IN (
                SELECT HA_AAI_ID
                FROM HOSTEL_ASSIGN
                JOIN ADMISSION_APPLY_INFO ON HOSTEL_ASSIGN.HA_AAI_ID = ADMISSION_APPLY_INFO.AAI_ID
                JOIN ADMISSION_PERSONAL_INFO ON AAI_API_ID = API_ID
                WHERE API_GENDER = 'Male'
                  AND HA_SESSION = ?
              )
        ", [$sessionId, $sessionId])[0]->unpaid_count;

        // Female Unpaid Count
        $femaleUnpaidCount = DB::select("
            SELECT COUNT(*) as unpaid_count
            FROM STUDENT_CHALLAN
            WHERE SC_SESSION_ID = ?
              AND SC_CHALLAN_TYPE = 'Hostel_Fee'
              AND SC_CHALLAN_STATUS = 'UNPAID'
              AND SC_APP_ID IN (
                SELECT HA_AAI_ID
                FROM HOSTEL_ASSIGN
                JOIN ADMISSION_APPLY_INFO ON HOSTEL_ASSIGN.HA_AAI_ID = ADMISSION_APPLY_INFO.AAI_ID
                JOIN ADMISSION_PERSONAL_INFO ON AAI_API_ID = API_ID
                WHERE API_GENDER = 'Female'
                  AND HA_SESSION = ?
              )
        ", [$sessionId, $sessionId])[0]->unpaid_count;





        // Male Unpaid Count outside
        $maleUnpaidCountOutside = DB::select("
            SELECT COUNT(*) as unpaid_count
            FROM STUDENT_CHALLAN
            join students_apply_info on students_apply_info.sai_id = SC_APP_ID
            WHERE SC_SESSION_ID = ?
              AND SC_CHALLAN_TYPE = 'Hostel_Fee'
              AND SC_CHALLAN_STATUS = 'UNPAID'
              AND SC_SESSION_TYPE ='regular_outside'
               AND SC_HOSTEL_CATEGORY = 'BH'

        ", [$sessionId])[0]->unpaid_count;

        // Female Unpaid Count outside
        $femaleUnpaidCountOutside = DB::select("
            SELECT COUNT(*) as unpaid_count

            FROM STUDENT_CHALLAN
           join students_apply_info on students_apply_info.sai_id = SC_APP_ID
            WHERE SC_SESSION_ID = ?
              AND SC_CHALLAN_TYPE = 'Hostel_Fee'
              AND SC_CHALLAN_STATUS = 'UNPAID'
              AND SC_SESSION_TYPE ='regular_outside'
               AND SC_HOSTEL_CATEGORY = 'GH'

        ", [$sessionId])[0]->unpaid_count;


        $maleRejectedCount = $maleStudents->filter(fn($s) => $s->happ_allowed_status == 'Rejected')->count();
        $femaleRejectedCount = $femaleStudents->filter(fn($s) => $s->happ_allowed_status == 'Rejected')->count();

        // Get total rooms and beds by category
        $totalRooms = DB::table('hostel_rooms')
            ->where('hr_status', 'E')
            ->count();
             $totalDisableRooms = DB::table('hostel_rooms')
            ->where('hr_status', 'D')
            ->count();
             $totalMaleDisableRooms = DB::table('hostel_rooms')
            ->where('hr_status', 'D')
            ->where('HR_HT_ID', 1)
            ->count();
             $totalFemaleDisableRooms = DB::table('hostel_rooms')
            ->where('hr_status', 'D')
            ->where('HR_HT_ID', 2)
            ->count();
            // dd($totalFemaleDisableRooms);

        $totalBeds = DB::table('hostel_beds')
            ->join('hostel_rooms', 'hostel_beds.hb_room_id', '=', 'hostel_rooms.hr_id')
            ->join('hostel_floor', 'hostel_rooms.hr_hf_id', '=', 'hostel_floor.hf_id')
            ->where('hostel_rooms.hr_status', 'E')
            ->where('hostel_floor.hf_status', 'E')
            ->count();

        // Male beds (new hostel type = 1)
        $maleTotalBeds = DB::table('hostel_beds')
            ->join('hostel_rooms', 'hostel_beds.hb_room_id', '=', 'hostel_rooms.hr_id')
            ->join('hostel_floor', 'hostel_rooms.hr_hf_id', '=', 'hostel_floor.hf_id')
            ->where('hostel_floor.hf_new_hostel_type', 1) // Male hostel type
            ->where('hostel_rooms.hr_status', 'E')
            ->where('hostel_floor.hf_status', 'E')
            ->count();

        // Female beds (new hostel type = 2)
        $femaleTotalBeds = DB::table('hostel_beds')
            ->join('hostel_rooms', 'hostel_beds.hb_room_id', '=', 'hostel_rooms.hr_id')
            ->join('hostel_floor', 'hostel_rooms.hr_hf_id', '=', 'hostel_floor.hf_id')
            ->where('hostel_floor.hf_new_hostel_type', 2) // Female hostel type
            ->where('hostel_rooms.hr_status', 'E')
            ->where('hostel_floor.hf_status', 'E')
            ->count();

        // Assigned beds
        $maleAssignedBeds = DB::table('hostel_beds')
            ->join('hostel_rooms', 'hostel_beds.hb_room_id', '=', 'hostel_rooms.hr_id')
            ->join('hostel_floor', 'hostel_rooms.hr_hf_id', '=', 'hostel_floor.hf_id')
            ->where('hostel_floor.hf_new_hostel_type', 1) // Male hostel type
            ->where('hostel_beds.hb_is_occupied', 'Y')
            ->where('hostel_rooms.hr_status', 'E')
            ->where('hostel_floor.hf_status', 'E')
            ->count();

        $femaleAssignedBeds = DB::table('hostel_beds')
            ->join('hostel_rooms', 'hostel_beds.hb_room_id', '=', 'hostel_rooms.hr_id')
            ->join('hostel_floor', 'hostel_rooms.hr_hf_id', '=', 'hostel_floor.hf_id')
            ->where('hostel_floor.hf_new_hostel_type', 2) // Female hostel type
            ->where('hostel_beds.hb_is_occupied', 'Y')
            ->where('hostel_rooms.hr_status', 'E')
            ->where('hostel_floor.hf_status', 'E')
            ->count();
            // dd($femaleAssignedBeds);

        // Free beds
        $maleFreeBeds = $maleTotalBeds - $maleAssignedBeds;
        $femaleFreeBeds = $femaleTotalBeds - $femaleAssignedBeds;

        // Paid but not assigned - Use raw SQL to handle data type conversion properly
        $malePaidNotAssigned = DB::select("
            SELECT COUNT(DISTINCT aai.AAI_ID) as count
            FROM ADMISSION_APPLY_INFO aai
            JOIN ADMISSION_PERSONAL_INFO api ON aai.AAI_API_ID = api.API_ID
            JOIN STUDENT_CHALLAN sc ON aai.AAI_ID = sc.SC_APP_ID
            LEFT JOIN HOSTEL_BEDS hb ON (
                (upper(aai.CMS_SAI_ENROLLMENT_NO) = upper(hb.HB_ENROLLMENT_NO))
                OR (TO_CHAR(aai.AAI_ID) = hb.HB_ENROLLMENT_NO)
            ) AND hb.HB_IS_OCCUPIED = 'Y'
            WHERE aai.AAI_SDPO_SESS_ID = ?
              AND api.API_ALLOW_HOSTEL = 'Allowed'
              AND aai.AAI_STATUS = 'Selected'
              AND api.API_GENDER = 'Male'
              AND sc.SC_CHALLAN_STATUS = 'PAID'
              AND sc.SC_CHALLAN_TYPE = 'Hostel_Fee'
              AND hb.HB_ENROLLMENT_NO IS NULL
        ", [$sessionId])[0]->count;

        $femalePaidNotAssigned = DB::select("
            SELECT COUNT(DISTINCT aai.AAI_ID) as count
            FROM ADMISSION_APPLY_INFO aai
            JOIN ADMISSION_PERSONAL_INFO api ON aai.AAI_API_ID = api.API_ID
            JOIN STUDENT_CHALLAN sc ON aai.AAI_ID = sc.SC_APP_ID
            LEFT JOIN HOSTEL_BEDS hb ON (
                (upper(aai.CMS_SAI_ENROLLMENT_NO) = upper(hb.HB_ENROLLMENT_NO))
                OR (TO_CHAR(aai.AAI_ID) = hb.HB_ENROLLMENT_NO)
            ) AND hb.HB_IS_OCCUPIED = 'Y'
            WHERE aai.AAI_SDPO_SESS_ID = ?
              AND api.API_ALLOW_HOSTEL = 'Allowed'
              AND aai.AAI_STATUS = 'Selected'
              AND api.API_GENDER = 'Female'
              AND sc.SC_CHALLAN_STATUS = 'PAID'
              AND sc.SC_CHALLAN_TYPE = 'Hostel_Fee'
              AND hb.HB_ENROLLMENT_NO IS NULL
        ", [$sessionId])[0]->count;
        // dd($femalePaidNotAssigned);

        return [
            'session_id' => $sessionId,
            'male_vacant_seats' => $seatCounts['male_vacant_seats'],
            'female_vacant_seats' => $seatCounts['female_vacant_seats'],
            'male_students_count' => $maleStudents->count(),
            'female_students_count' => $femaleStudents->count(),
            'male_applicants_count' => $maleApplicants->count(),
            'female_applicants_count' => $femaleApplicants->count(),
            'male_allowed_count' => $maleAllowedCount,
            'female_allowed_count' => $femaleAllowedCount,
            'male_paid_count' => $malePaidCount,
            'female_paid_count' => $femalePaidCount,
            'male_unpaid_count' => $maleUnpaidCount,
            'male_unpaid_countOutside' => $maleUnpaidCountOutside,
            //out side
            'female_paid_count_out_side' => $femalePaidCount_out_side,
            'male_paid_count_out_side' => $malePaidCount_out_side,
            'female_unpaid_count' => $femaleUnpaidCount,
            'female_unpaid_countOutside' => $femaleUnpaidCountOutside,
            'male_rejected_count' => $maleRejectedCount,
            'female_rejected_count' => $femaleRejectedCount,
            'total_rooms' => $totalRooms,
            'total_disable_rooms' => $totalDisableRooms,
            'total_beds' => $totalBeds,
            'male_total_beds' => $maleTotalBeds,


            'male_total_desable_beds' => $totalMaleDisableRooms,
            'female_total_desable_beds' =>  $totalFemaleDisableRooms,






            'female_total_beds' => $femaleTotalBeds,
            'male_assigned_beds' => $maleAssignedBeds,
            'female_assigned_beds' => $femaleAssignedBeds,
            'male_free_beds' => $maleFreeBeds,
            'female_free_beds' => $femaleFreeBeds,
            'male_paid_not_assigned' => $malePaidNotAssigned,
            'female_paid_not_assigned' => $femalePaidNotAssigned,
        ];
    }

    private function addHostelStatistics($pdf, $stats, $sessionId, $sessionName)
    {
        // Title
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(0, 10, 'Session: ' . $sessionName, 0, 1, 'C');
        $pdf->Ln(5);

        // Set header style
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->SetFillColor(200, 220, 255); // light blue background
        $pdf->Cell(30, 8, 'Hostel Type', 1, 0, 'C', true);
        $pdf->Cell(30, 8, 'Total Seats', 1, 0, 'C', true);
        $pdf->Cell(35, 8, 'Available Seats', 1, 0, 'C', true);
        $pdf->Cell(35, 8, 'Assigned Seats', 1, 0, 'C', true);
        $pdf->Cell(30, 8, 'Paid', 1, 0, 'C', true);
        $pdf->Cell(30, 8, 'Unpaid', 1, 1, 'C', true);

        // Normal rows (data)
        $pdf->SetFont('Arial', '', 10);

        $pdf->Cell(30, 8, 'Male Inside', 1, 0, 'L');
        $pdf->Cell(30, 8, $stats['male_total_beds'], 1, 0, 'C');
        $pdf->Cell(35, 8, $stats['male_free_beds'], 1, 0, 'C');
        $pdf->Cell(35, 8, $stats['male_assigned_beds'], 1, 0, 'C');
        $pdf->Cell(30, 8, $stats['male_paid_count'], 1, 0, 'C');
        $pdf->Cell(30, 8, $stats['male_unpaid_count'], 1, 1, 'C');

        $pdf->Cell(30, 8, 'Female Inside', 1, 0, 'L');
        $pdf->Cell(30, 8, $stats['female_total_beds'], 1, 0, 'C');
        $pdf->Cell(35, 8, $stats['female_free_beds'], 1, 0, 'C');
        $pdf->Cell(35, 8, $stats['female_assigned_beds'], 1, 0, 'C');
        $pdf->Cell(30, 8, $stats['female_paid_count'], 1, 0, 'C');
        $pdf->Cell(30, 8, $stats['female_unpaid_count'], 1, 1, 'C');


        // Add some spacing before next table
        $pdf->Ln(8);

        // Header for Outside Students
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->SetFillColor(200, 220, 255); // light blue
        $pdf->Cell(30, 8, 'Hostel Type', 1, 0, 'C', true);
        $pdf->Cell(30, 8, 'Total Seats', 1, 0, 'C', true);
        $pdf->Cell(35, 8, 'Available Seats', 1, 0, 'C', true);
        $pdf->Cell(35, 8, 'Assigned Seats', 1, 0, 'C', true);
        $pdf->Cell(30, 8, 'Paid', 1, 0, 'C', true);
        $pdf->Cell(30, 8, 'Unpaid', 1, 1, 'C', true);

        // Normal rows (Outside Male)
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(30, 8, 'Male Outside', 1, 0, 'L');
        $pdf->Cell(30, 8, '-', 1, 0, 'C'); // No total seats info, so placeholder
        $pdf->Cell(35, 8, '-', 1, 0, 'C'); // No available seats info
        $pdf->Cell(35, 8, '-', 1, 0, 'C'); // No assigned seats info
        $pdf->Cell(30, 8, $stats['male_paid_count_out_side'], 1, 0, 'C');
        $pdf->Cell(30, 8, $stats['male_unpaid_countOutside'], 1, 1, 'C');

        // Normal rows (Outside Female)
        $pdf->Cell(30, 8, 'Female Outside', 1, 0, 'L');
        $pdf->Cell(30, 8, '-', 1, 0, 'C'); // No total seats info
        $pdf->Cell(35, 8, '-', 1, 0, 'C'); // No available seats info
        $pdf->Cell(35, 8, '-', 1, 0, 'C'); // No assigned seats info
        $pdf->Cell(30, 8, $stats['female_paid_count_out_side'], 1, 0, 'C');
        $pdf->Cell(30, 8, $stats['female_unpaid_countOutside'], 1, 1, 'C');





        // // Normal rows (Outside Male)
        // $pdf->SetFont('Arial', '', 10);
        // $pdf->Cell(30, 8, 'Male Bhadiyan', 1, 0, 'L');
        // $pdf->Cell(30, 8, '-', 1, 0, 'C'); // No total seats info, so placeholder
        // $pdf->Cell(35, 8, '-', 1, 0, 'C'); // No available seats info
        // $pdf->Cell(35, 8, '-', 1, 0, 'C'); // No assigned seats info
        // $pdf->Cell(30, 8, $stats['male_paid_count_out_side'], 1, 0, 'C');
        // $pdf->Cell(30, 8, $stats['male_unpaid_countOutside'], 1, 1, 'C');

        // // Normal rows (Outside Female)
        // $pdf->Cell(30, 8, 'Female Bhadiyan', 1, 0, 'L');
        // $pdf->Cell(30, 8, '-', 1, 0, 'C'); // No total seats info
        // $pdf->Cell(35, 8, '-', 1, 0, 'C'); // No available seats info
        // $pdf->Cell(35, 8, '-', 1, 0, 'C'); // No assigned seats info
        // $pdf->Cell(30, 8, $stats['female_paid_count_out_side'], 1, 0, 'C');
        // $pdf->Cell(30, 8, $stats['female_unpaid_countOutside'], 1, 1, 'C');




/////////////

// Section Heading
        $pdf->Ln(8);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetFillColor(220, 220, 220);
        $pdf->Cell(0, 10, '2. BNU INSIDE ROOM & BED', 1, 1, 'L', true);

        // Column Headers
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->SetFillColor(200, 220, 255); // light blue header
        $pdf->Cell(60, 8, 'Category', 1, 0, 'C', true);
        $pdf->Cell(40, 8, 'Male', 1, 0, 'C', true);
        $pdf->Cell(40, 8, 'Female', 1, 0, 'C', true);
        $pdf->Cell(40, 8, 'Total', 1, 1, 'C', true);

        // Data Rows
        $pdf->SetFont('Arial', '', 10);

        // Total Rooms
        $pdf->Cell(60, 8, 'Total Rooms', 1, 0, 'L');
        $pdf->Cell(40, 8, $stats['male_total_beds']/2, 1, 0, 'C'); // no male/female split
        $pdf->Cell(40, 8, $stats['female_total_beds']/2, 1, 0, 'C'); // no male/female split
        $pdf->Cell(40, 8, $stats['total_rooms'], 1, 1, 'C');

                // Total Rooms
        $pdf->Cell(60, 8, 'Total Desable Rooms', 1, 0, 'L');
        $pdf->Cell(40, 8, $stats['male_total_desable_beds'], 1, 0, 'C'); // no male/female split
        $pdf->Cell(40, 8, $stats['female_total_desable_beds'], 1, 0, 'C'); // no male/female split
        $pdf->Cell(40, 8, $stats['total_disable_rooms'], 1, 1, 'C');

        // Total Beds
        $pdf->Cell(60, 8, 'Total Enable Beds', 1, 0, 'L');
        $pdf->Cell(40, 8, $stats['male_total_beds'], 1, 0, 'C');
        $pdf->Cell(40, 8, $stats['female_total_beds'], 1, 0, 'C');
        $pdf->Cell(40, 8, $stats['total_beds'], 1, 1, 'C');

        // Assigned Beds
        $pdf->Cell(60, 8, 'Assigned Beds', 1, 0, 'L');
        $pdf->Cell(40, 8, $stats['male_assigned_beds'], 1, 0, 'C');
        $pdf->Cell(40, 8, $stats['female_assigned_beds'], 1, 0, 'C');
        $pdf->Cell(40, 8, $stats['male_assigned_beds'] + $stats['female_assigned_beds'], 1, 1, 'C');

        // Free Beds
        $pdf->Cell(60, 8, 'Free Beds', 1, 0, 'L');
        $pdf->Cell(40, 8, $stats['male_free_beds'], 1, 0, 'C');
        $pdf->Cell(40, 8, $stats['female_free_beds'], 1, 0, 'C');
        $pdf->Cell(40, 8, $stats['male_free_beds'] + $stats['female_free_beds'], 1, 1, 'C');

        // Paid Not Assigned
        $pdf->Cell(60, 8, 'Paid Not Assigned Beds', 1, 0, 'L');
        $pdf->Cell(40, 8, $stats['male_paid_not_assigned'], 1, 0, 'C');
        $pdf->Cell(40, 8, $stats['female_paid_not_assigned'], 1, 0, 'C');
        $pdf->Cell(40, 8, $stats['male_paid_not_assigned'] + $stats['female_paid_not_assigned'], 1, 1, 'C');



























        // // Challan Status Section
        // $pdf->SetFont('Arial', 'B', 12);
        // $pdf->SetFillColor(220, 220, 220);
        // $pdf->Cell(0, 8, '1. CHALLAN STATUS', '1', 1, 'L', true);

        // $pdf->SetFont('Arial', '', 10);
        // // Row 1: Paid (including paid not assigned beds)
        // $malePaidTotal = $stats['male_paid_count'] ;
        // // + $stats['male_paid_not_assigned'];
        // $femalePaidTotal = $stats['female_paid_count'];
        // //  + $stats['female_paid_not_assigned'];

        // // Row 1: Paid (including paid not assigned beds)
        // $malePaidTotalOutSide = $stats['male_paid_count_out_side'] ;
        // // + $stats['male_paid_not_assigned'];
        // $femalePaidTotalOutSide = $stats['female_paid_count_out_side'];
        // //  + $stats['female_paid_not_assigned'];
        // // First Row: Main Headers
        // $pdf->Cell(90, 6, 'Male', 1, 0, 'C');   // Span for Male
        // $pdf->Cell(90, 6, 'Female', 1, 1, 'C'); // Span for Female

        // $pdf->Cell(60, 6, 'Inside Paid (Male)', '1', 0, 'L');
        // $pdf->Cell(30, 6, $malePaidTotal, '1', 0, 'C');
        // $pdf->Cell(60, 6, 'Inside Paid (Female)', '1', 0, 'L');
        // $pdf->Cell(30, 6, $femalePaidTotal, '1', 1, 'C');


        //         // Row 2: Unpaid
        // $pdf->Cell(60, 6, 'Inside Unpaid (Male)', '1', 0, 'L');
        // $pdf->Cell(30, 6, $stats['male_unpaid_count'], '1', 0, 'C');
        // $pdf->Cell(60, 6, 'Inside Unpaid (Female)', '1', 0, 'L');
        // $pdf->Cell(30, 6, $stats['female_unpaid_count'], '1', 1, 'C');


        // $pdf->Cell(60, 6, 'Outside Paid (Male)', '1', 0, 'L');
        // $pdf->Cell(30, 6, $malePaidTotalOutSide, '1', 0, 'C');
        // $pdf->Cell(60, 6, 'Outside Paid (Female)', '1', 0, 'L');
        // $pdf->Cell(30, 6, $femalePaidTotalOutSide, '1', 1, 'C');



        // // Row 2: Unpaid
        // $pdf->Cell(60, 6, 'Outside Unpaid (Male)', '1', 0, 'L');
        // $pdf->Cell(30, 6, $stats['male_unpaid_countOutside'], '1', 0, 'C');
        // $pdf->Cell(60, 6, 'Outside Unpaid (Female)', '1', 0, 'L');
        // $pdf->Cell(30, 6, $stats['female_unpaid_countOutside'], '1', 1, 'C');


        // $pdf->Ln(3);

        // // New table for totals with space above
        // $pdf->SetFont('Arial', 'B', 12);
        // $pdf->SetFillColor(220, 220, 220);
        // $pdf->Cell(0, 8, 'TOTALS Paid/Unpaid', '1', 1, 'L', true);


        //         $pdf->SetFont('Arial', '', 10);
        // $pdf->Cell(60, 6, 'Total Paid Inside', '1', 0, 'L');
        // $pdf->Cell(30, 6, $malePaidTotal + $femalePaidTotal, '1', 0, 'C');
        // $pdf->Cell(60, 6, 'Total Unpaid Inside', '1', 0, 'L');
        // $pdf->Cell(30, 6, $stats['male_unpaid_count'] + $stats['female_unpaid_count'], '1', 1, 'C');


        //         $pdf->SetFont('Arial', '', 10);
        // $pdf->Cell(60, 6, 'Total Paid Outside', '1', 0, 'L');
        // $pdf->Cell(30, 6, $malePaidTotalOutSide + $femalePaidTotalOutSide, '1', 0, 'C');
        // $pdf->Cell(60, 6, 'Total Unpaid Outside', '1', 0, 'L');
        // $pdf->Cell(30, 6, $stats['male_unpaid_countOutside'] + $stats['female_unpaid_countOutside'], '1', 1, 'C');


        // $pdf->Ln(3);

        // // Room & Bed Statistics Section
        // $pdf->SetFont('Arial', 'B', 12);
        // $pdf->SetFillColor(220, 220, 220);
        // $pdf->Cell(0, 8, '2.BNU INSIDE ROOM & BED', '1', 1, 'L', true);

        // $pdf->SetFont('Arial', '', 10);
        //         // First Row: Main Headers
        // $pdf->Cell(90, 6, 'Male', 1, 0, 'C');   // Span for Male
        // $pdf->Cell(90, 6, 'Female', 1, 1, 'C'); // Span for Female

        // $pdf->Cell(60, 6, 'Total Rooms', '1', 0, 'L');
        // $pdf->Cell(30, 6, $stats['total_rooms'], '1', 0, 'C');
        // $pdf->Cell(60, 6, 'Total Beds', '1', 0, 'L');
        // $pdf->Cell(30, 6, $stats['total_beds'], '1', 1, 'C');

        // $pdf->Cell(60, 6, 'Male Total Beds', '1', 0, 'L');
        // $pdf->Cell(30, 6, $stats['male_total_beds'], '1', 0, 'C');
        // $pdf->Cell(60, 6, 'Female Total Beds', '1', 0, 'L');
        // $pdf->Cell(30, 6, $stats['female_total_beds'], '1', 1, 'C');

        // $pdf->Cell(60, 6, 'Male Assigned Beds', '1', 0, 'L');
        // $pdf->Cell(30, 6, $stats['male_assigned_beds'], '1', 0, 'C');
        // $pdf->Cell(60, 6, 'Female Assigned Beds', '1', 0, 'L');
        // $pdf->Cell(30, 6, $stats['female_assigned_beds'], '1', 1, 'C');

        // $pdf->Cell(60, 6, 'Male Free Beds', '1', 0, 'L');
        // $pdf->Cell(30, 6, $stats['male_free_beds'], '1', 0, 'C');
        // $pdf->Cell(60, 6, 'Female Free Beds', '1', 0, 'L');
        // $pdf->Cell(30, 6, $stats['female_free_beds'], '1', 1, 'C');

        // $pdf->Cell(60, 6, 'Male Paid Not Assigned Bed', '1', 0, 'L');
        // $pdf->Cell(30, 6, $stats['male_paid_not_assigned'], '1', 0, 'C');
        // $pdf->Cell(60, 6, 'Female Paid Not Assigned Bed', '1', 0, 'L');
        // $pdf->Cell(30, 6, $stats['female_paid_not_assigned'], '1', 1, 'C');

        // Footer
        $pdf->Ln(10);
        $pdf->SetFont('Arial', 'I', 8);
        $pdf->Cell(0, 5, 'Generated on: ' . date('d M Y') . ' by BNU CMS', 0, 1, 'C');
        $pdf->Cell(0, 5, 'Beaconhouse National University - Lahore', 0, 1, 'C');
    }


 public function hostelStudentsList()
    {
        // Female Students (GH)
        $femaleStudents = DB::table('STUDENT_CHALLAN')
            ->join('hostel_beds', 'HB_ENROLLMENT_NO', '=', 'SC_ENROLLMENT_NO')
            ->join('hostel_rooms', 'HB_ROOM_ID', '=', 'HR_ID')
            ->join('students_apply_info', 'SAI_ENROLLMENT_NO', '=', 'SC_ENROLLMENT_NO')
            ->join('students_personal_info', 'SAI_SPI_ID', '=', 'SPI_ID')
            ->join('HOSTEL_FLOOR', 'HR_HF_ID', '=', 'HF_ID')
            ->select(
                'HB_ENROLLMENT_NO',
                'HR_ROOM_NO',
                'hf_name',
                'sc_challan_status',
                'SPI_NAME'
            )
            ->where('HB_IS_OCCUPIED', 'Y')
            ->where('SC_SESSION_ID', 165)
            ->where(function ($q) {
                $q->where('SC_SESSION_TYPE', 'regular')
                  ->orWhereNull('SC_SESSION_TYPE');
            })
            ->where('SC_CHALLAN_TYPE', 'Hostel_Fee')
            // ->where('SC_CHALLAN_STATUS', 'PAID')
            ->where('SC_HOSTEL_CATEGORY', 'GH')
            ->get();

        // Male Students (BH)
        $maleStudents = DB::table('STUDENT_CHALLAN')
            ->join('hostel_beds', 'HB_ENROLLMENT_NO', '=', 'SC_ENROLLMENT_NO')
            ->join('hostel_rooms', 'HB_ROOM_ID', '=', 'HR_ID')
            ->join('students_apply_info', 'SAI_ENROLLMENT_NO', '=', 'SC_ENROLLMENT_NO')
            ->join('students_personal_info', 'SAI_SPI_ID', '=', 'SPI_ID')
            ->join('HOSTEL_FLOOR', 'HR_HF_ID', '=', 'HF_ID')
            ->select(
                'HB_ENROLLMENT_NO',
                'HR_ROOM_NO',
                'hf_name',
                'sc_challan_status',
                'SPI_NAME'
            )
            ->where('HB_IS_OCCUPIED', 'Y')
            ->where('SC_SESSION_ID', 165)
            ->where(function ($q) {
                $q->where('SC_SESSION_TYPE', 'regular')
                  ->orWhereNull('SC_SESSION_TYPE');
            })
            ->where('SC_CHALLAN_TYPE', 'Hostel_Fee')
            // ->where('SC_CHALLAN_STATUS', 'PAID')
            ->where('SC_HOSTEL_CATEGORY', 'BH')
            ->get();

        return view('Hostel.students_list', compact('femaleStudents', 'maleStudents'));
    }


}
