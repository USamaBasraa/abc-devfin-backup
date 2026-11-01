<?php

namespace App\Http\Controllers\Grader\BNUReportsGenerate;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\StudentFinancial\GenerateSingleChallanController;
use App\Http\Controllers\Grader\StudentFines\StudentFineController;
use App\Http\Controllers\Grader\BNUReportsGenerate\StudentProfileController;




class StudentProfileDetailsController extends Controller
{


    public function fetchSingleStudentWidgetLink(Request $request)
    {
        $widgetId = $request->widgetId;
        $enrollment = strtoupper($request->enrollment);
        switch ($widgetId) {
            case 'warningLetters':
                $data = $this->getWarningLetters($enrollment);
                break;
            case 'feeChallans':
                $data = $this->getfeeChallans($enrollment);
                break;
            case 'fines':
                $data = $this->getfineChallans($enrollment);
                break;
            case 'courseEnrollment':
                $data = $this->getEnrolledCourses($enrollment);
                break;
            case 'academicHistory':
                $data = $this->getAcademicHistory($enrollment);
                break;
            case 'scholarship':
                $data = $this->getScholarshipDetails($enrollment);
                break;
            case 'attendance':
                $data = $this->getCourseAttendance($enrollment);
                break;
            case 'gymSubscription':
                $data = $this->getGymSubscription($enrollment);
                break;
            case 'libraryBooks':
                $data = $this->getLibraryDetails($enrollment);
                break;
            default:
                return response()->json(['success' => false, 'widgetId' => $widgetId, 'message' => 'Invalid widget ID'], 400);
        }

        return response()->json([
            'success' => true,
            'widgetId' => $widgetId,  // Pass widgetId in response
            'data' => $data
        ]);
    }

    private function getWarningLetters($enrollmentNo)
    {
        $query = "
        SELECT 
            swl.ID, 
            swl.SWL_ENROLLMENT_NO, 
            swl.WARNING_LETTER_TYPE, 
            swl.SWL_FILENAME, 
            swl.ENTRY_TIMESTAMP, 
            swl.swl_filelink, 
            swl.STATUS, 
            swl.WARNING_LETTER_NAME,
            EMP.FULL_NAME
        FROM student_warning_letter swl
        JOIN (
            SELECT EMPLOYEE_ID, EMP_F_NAME || ' ' || EMP_L_NAME AS FULL_NAME
            FROM EMP_EMPLOYEES
        ) EMP ON swl.ENTRY_USER_ID = EMP.EMPLOYEE_ID
        WHERE UPPER(swl.SWL_ENROLLMENT_NO) = ?
    ";

        return DB::select($query, [$enrollmentNo]); // Returning raw results directly
    }
    private function getfeeChallans($enrollmentNo)
    {
        $enrollmentNumber = strtoupper($enrollmentNo);

        $aaiId = app(StudentProfileController::class)->getStudentAaiId($enrollmentNumber);




        $sql = "SELECT Student_challan.*, academic_sessions.sess_code
            FROM Student_challan
            LEFT JOIN academic_sessions ON Student_challan.sc_session_id = academic_sessions.sess_id
            WHERE 1=1   AND  Student_challan.sc_challan_type
            in ('" . implode("','", StudentProfileController::FEE_CHALLAN_TYPES) . "')
            AND invoice_status='E'  ";
        $sql .= " AND Student_challan.sc_app_id = '$aaiId'";

        $sql .= " ORDER BY Student_challan.created_at DESC";
        // echo $sql;exit;
        $challans = DB::select($sql);
        return $challans;

    }
    private function getfineChallans($enrollmentNo)
    {

        $newRequest = new Request([
            'studentEnrollment' => $enrollmentNo
        ]);
        $fineController = app(StudentFineController::class);

        $fines = $fineController->getStudentFineRec($newRequest);

        return $fines;
    }




    public function getEnrolledCourses($enrollmentNo)
    {
        $courses = DB::select("
       SELECT 
           sc.sc_sai_enrollment_no,
           COALESCE(asess.sess_name, asess_sc.sess_name) AS sess_name,
           sc.sc_status,
           get_course_name(sc.sc_co_id) AS course_title,
           get_teachername(sc.sc_co_id) AS teachername,
           sc.sc_co_id
       FROM courses_offered co
       LEFT JOIN student_courses sc ON co.co_id = sc.sc_co_id
       LEFT JOIN academic_sessions asess ON co.co_sess_id = asess.sess_id
       LEFT JOIN academic_sessions asess_sc 
           ON sc.sc_co_sess_id = asess_sc.sess_id
       WHERE sc.sc_sai_enrollment_no = :enrollment_no
       AND sc.sc_enrollment_status = 1
       AND co.co_sess_id = (  
           SELECT MAX(co_sess_id)  
           FROM courses_offered  
           WHERE co_id IN (SELECT sc_co_id FROM student_courses WHERE sc_sai_enrollment_no = :enrollment_no)  
       )
       ORDER BY asess.sess_name DESC
    ", ['enrollment_no' => $enrollmentNo]);

        return $courses;
    }






    public function getAcademicHistory($enrollmentNo)
    {
        return $enrollmentNo;
    }
    public function getScholarshipDetails($enrollmentNo)
    {
        $sql = "
        SELECT 
            CASE 
                WHEN fsd_sch_type IS NULL 
                    THEN FIN_SCHOLARSHIP_DISCOUNTS.FSD_DISCOUNT_PERCENTAGE * 100
                ELSE 
                    FIN_SCHOLARSHIP_DISCOUNTS.FSD_DISCOUNT_PERCENTAGE
            END AS FSD_DISCOUNT_PERCENTAGE,
            FIN_SCHOLARSHIP_DISCOUNTS.FSD_DISCOUNTED_AMOUNT,
            ACADEMIC_SESSIONS.SESS_CODE,
            SCHOLARSHIP_TYPE.SCHOLARSHIP_NAME,
            case when fsd_sch_type is null then 'Semester Fee' else 'Hostel Fee' end as scholarship_type
   
        FROM FIN_SCHOLARSHIP_DISCOUNTS
        LEFT JOIN SCHOLARSHIP_TYPE ON FIN_SCHOLARSHIP_DISCOUNTS.FSD_SCHOLARSHIP_TYPE = SCHOLARSHIP_TYPE.scholarship_id
        LEFT JOIN ACADEMIC_SESSIONS ON FIN_SCHOLARSHIP_DISCOUNTS.FSD_SESION_ID = ACADEMIC_SESSIONS.sess_id
        WHERE FIN_SCHOLARSHIP_DISCOUNTS.FSD_ENROLLMENT_NO = :enrollmentNo
    ";

        $scholarshipDetails = DB::select($sql, ['enrollmentNo' => $enrollmentNo]);

        return $scholarshipDetails;
    }



    public function getCourseAttendance($enrollmentNo)
    {
        $sql = "
        SELECT 
            student_courses.sc_sai_enrollment_no,
            academic_sessions.sess_name,
            academic_sessions.sess_id,
            student_courses.attendance_percentage,
            get_course_name(student_courses.sc_co_id) AS course_title,
            get_teachername(student_courses.sc_co_id) AS teachername,
            student_courses.sc_co_id
        FROM student_courses
        LEFT JOIN academic_sessions ON student_courses.sc_co_sess_id = academic_sessions.sess_id
        LEFT JOIN courses_two ON student_courses.sc_course_id = courses_two.course_id
        WHERE 1=1
        AND student_courses.sc_sai_enrollment_no = :enrollment_no
        and student_courses.SC_ENROLLMENT_STATUS=1
        ORDER BY academic_sessions.sess_id desc
    ";

        return DB::select($sql, ['enrollment_no' => $enrollmentNo]);
    }


    public function getGymSubscription($enrollmentNo)
    {
        $sql = "
        SELECT 
            s.USER_ID AS enrollment_no, 
            academic_sessions.SESS_CODE, 
            s.SUBSCRIPTION_DATE, 
            s.EXPIRATION_DATE, 
            s.CURRENT_STATUS, 
            s.CHALLAN_ID, 
            s.CHALLAN_STATUS, 
            sp.SP_CATEGORY, 
            sp.SP_PLAN_TENURE
        FROM subscriptions s
        LEFT JOIN academic_sessions ON s.sess_id = academic_sessions.sess_id  
        LEFT JOIN SUBSCRIPTION_PLANS sp ON s.sp_id = sp.sp_id
        WHERE s.USER_ID = :enrollment_no
        AND sp.SP_CATEGORY = 'Gym'
        ORDER BY s.EXPIRATION_DATE DESC
    ";

        $GymRecords = DB::select($sql, ['enrollment_no' => $enrollmentNo]);

        return $GymRecords;
    }



    public function getLibraryDetails($enrollmentNo)
    {
        // Updated query to add overdue flag
        $libraryRecords = DB::select(
            "
        SELECT 
            lc.LC_ACCESSION_NO, 
            TO_CHAR(lc.LC_ISSUE_DATE, 'DD-Mon-YYYY') AS LC_ISSUE_DATE,
            lc.LC_ISSUE_NO_OF_DAYS, 
            TO_CHAR(lc.LC_RETURN_DATE, 'DD-Mon-YYYY') AS LC_RETURN_DATE, 
            lc.LC_BOOK_STATUS, 
            TO_CHAR(lc.LC_DUE_DATE, 'DD-Mon-YYYY') AS LC_DUE_DATE,
            -- Calculate the Due Date based on Issue Date and Number of Days Issued
            (lc.LC_ISSUE_DATE + lc.LC_ISSUE_NO_OF_DAYS) AS CALCULATED_DUE_DATE,
            -- Check if the book is overdue
            CASE 
                WHEN lc.LC_BOOK_STATUS = 'Issued' AND (lc.LC_ISSUE_DATE + lc.LC_ISSUE_NO_OF_DAYS) < TRUNC(SYSDATE) THEN 1 
                ELSE 0 
            END AS OVERDUE_STATUS,
            lc.LC_TOTAL_LATE_DAYS as lc_total_late_days,
            lc.LC_FINE_TOTAL as lc_fine_total,
            lc.LC_FINE_STATUS as lc_fine_status,
            lb.library_title
        FROM 
            library_circulation lc
        JOIN 
            library_books lb ON lc.LC_ACCESSION_NO = lb.id
        WHERE 
            upper(lc.sai_enrollment_no) = :enrollmentNo
        ",
            ['enrollmentNo' => $enrollmentNo]
        );



        return $libraryRecords;
    }




}