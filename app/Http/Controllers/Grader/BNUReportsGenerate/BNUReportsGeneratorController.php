<?php

namespace App\Http\Controllers\Grader\BNUReportsGenerate;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class BNUReportsGeneratorController extends Controller
{


    public function index()
    {
        // Fetch academic sessions, ordered by sess_id descending


        // $loginUserUsername = auth()->user()->username;
        // $loginUserSchoolId = auth()->user()->school_id;

        // Get Academic Sessions
        $recsqlAcademicSessions = DB::table('academic_sessions')
            ->where('sess_id', '>', 161)
            ->orderBy('sess_id', 'DESC')
            ->get();
        // dd($loginUserUsername);
        // Get Schools
        $loginUserUsername = strtolower(trim(auth()->user()->username));
        $loginUserSchoolId = auth()->user()->school_id;

        $recSchool = DB::table('schools');

        // ✅ Force override for ijaz.hussian

        if ($loginUserUsername === 'ijaz.hussain') {
            // dd('123');
            $recSchool = $recSchool->whereIn('school_id', [12, 21]);
        }
        // ✅ These users see all schools
        elseif (in_array($loginUserUsername, ['moeedyusuf', 'sumair.shafique', 'bilal.saleem', 'zain.ali'])) {
            // no filter
        }
        // ✅ Other users – only their school
        else {
            $recSchool = $recSchool->where('school_id', $loginUserSchoolId);
        }

        $recSchool = $recSchool->get();

        // Retrieve distinct aai_pf_status
        $distinctPfStatus = DB::table('admission_apply_info')
            ->select('aai_pf_status')
            ->where('aai_finalsubmit', 1)
            ->whereNotNull('aai_pf_status')
            ->distinct()
            ->get();

        // Retrieve distinct aai_qualification_status
        $distinctQualificationStatus = DB::table('admission_apply_info')
            ->select('aai_qualification_status')
            ->where('aai_finalsubmit', 1)
            ->whereNotNull('aai_qualification_status')
            ->distinct()
            ->orderBy('aai_qualification_status', 'asc')
            ->get();
        // dd($distinctQualificationStatus);
        // Retrieve distinct aai_status
        $distinctStatus = DB::table('admission_apply_info')
            ->select('aai_status')
            ->where('aai_finalsubmit', 1)
            ->whereNotNull('aai_status')
            ->distinct()
            ->get();

        // Retrieve unique student statuses
        $distinctStudentStatuses = DB::table('students_apply_info')
            ->select('sai_student_status')
            ->groupBy('sai_student_status')
            ->orderByRaw('min(sai_student_status) ASC')
            ->get();

        // Return the view with all the retrieved data
        return view('grader.reportgenerator.BNUReportsGenerator.index', [
            'academic_session' => $recsqlAcademicSessions,
            'recSchool' => $recSchool,
            'distinctPfStatus' => $distinctPfStatus,
            'distinctQualificationStatus' => $distinctQualificationStatus,
            'distinctStatus' => $distinctStatus,
            'studentStatus' => $distinctStudentStatuses,
        ]);
    }
    public function admissionReportData()
    {
        // Fetch academic sessions, ordered by sess_id descending


        // $loginUserUsername = auth()->user()->username;
        // $loginUserSchoolId = auth()->user()->school_id;

        // Get Academic Sessions
        $recsqlAcademicSessions = DB::table('academic_sessions')
            ->where('sess_id', '>', 161)
            ->orderBy('sess_id', 'DESC')
            ->get();
        // dd($loginUserUsername);
        // Get Schools
        $loginUserUsername = strtolower(trim(auth()->user()->username));
        $loginUserSchoolId = auth()->user()->school_id;

        $recSchool = DB::table('schools');

        // ✅ Force override for ijaz.hussian

        if ($loginUserUsername === 'ijaz.hussain') {
            // dd('123');
            $recSchool = $recSchool->whereIn('school_id', [12, 21]);
        }
        // ✅ These users see all schools
        elseif (in_array($loginUserUsername, ['moeedyusuf', 'sumair.shafique', 'bilal.saleem', 'zain.ali', 'naureen.mirza'])) {
            // no filter
        }
        // ✅ Other users – only their school
        else {
            $recSchool = $recSchool->where('school_id', $loginUserSchoolId);
        }

        $recSchool = $recSchool->get();

        // Retrieve distinct aai_pf_status
        $distinctPfStatus = DB::table('admission_apply_info')
            ->select('aai_pf_status')
            ->where('aai_finalsubmit', 1)
            ->whereNotNull('aai_pf_status')
            ->distinct()
            ->get();

        // Retrieve distinct aai_qualification_status
        $distinctQualificationStatus = DB::table('admission_apply_info')
            ->select('aai_qualification_status')
            ->where('aai_finalsubmit', 1)
            ->whereNotNull('aai_qualification_status')
            ->distinct()
            ->orderBy('aai_qualification_status', 'asc')
            ->get();
        // dd($distinctQualificationStatus);
        // Retrieve distinct aai_status
        $distinctStatus = DB::table('admission_apply_info')
            ->select('aai_status')
            ->where('aai_finalsubmit', 1)
            ->whereNotNull('aai_status')
            ->distinct()
            ->get();

        // Retrieve unique student statuses
        $distinctStudentStatuses = DB::table('students_apply_info')
            ->select('sai_student_status')
            ->groupBy('sai_student_status')
            ->orderByRaw('min(sai_student_status) ASC')
            ->get();

        // Return the view with all the retrieved data
        return view('grader.reportgenerator.BNUReportsGenerator.admissionReport', [
            'academic_session' => $recsqlAcademicSessions,
            'recSchool' => $recSchool,
            'distinctPfStatus' => $distinctPfStatus,
            'distinctQualificationStatus' => $distinctQualificationStatus,
            'distinctStatus' => $distinctStatus,
            'studentStatus' => $distinctStudentStatuses,
        ]);
    }
    public function getDepartments($school_id)
    {
        // Use Query Builder to fetch departments
        $departments = DB::table('departments')
            ->where('dept_school_id', $school_id)
            ->select('dept_id', 'dept_name')
            ->get();

        return response()->json($departments);
    }

    public function getPrograms(Request $request)
    {
        // Validate the incoming parameters

        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'department_id' => 'required|integer', // Ensure it exists in the departments table
            'school_id' => 'required|integer', // Ensure it exists in the schools table
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }
        $department_id = $request->input('department_id');
        $school_id = $request->input('school_id');
        $programs = DB::table('programs')
            ->where('prog_dept_id', $department_id)
            ->where('prog_school_id', $school_id)
            ->where('prog_status', 'E')
            ->distinct()
            ->select('prog_id', 'prog_name')
            ->get();

        return response()->json($programs);
    }

    public function getCourses(Request $request)
    {
        // Validate incoming parameters

        // dd($request->all());

        $validator = Validator::make($request->all(), [
            'program_id' => 'required|integer', // Ensure it exists in the programs table
            'academic_session_id' => 'required|integer', // You can add more validation rules as needed
            'school_id' => 'required|integer',
            'department_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $program_id = $request->input('program_id');
        $academic_session_id = $request->input('academic_session_id');
        $school_id = $request->input('school_id');
        $department_id = $request->input('department_id');

        // Fetch courses based on the provided parameters using Query Builder
        $courses = DB::table('courses_offered')
            ->where('co_sess_id', $academic_session_id)
            ->where('co_school_id', $school_id)
            ->where('co_dept_id', $department_id)
            ->where('co_status', 'E')
            ->select('co_id', DB::raw('get_course_name(co_id) as course_name')) // Call the Oracle function here
            ->get();

        return response()->json($courses);
    }









    public function getActiveStudents(Request $request)
    {
        // Validate incoming request parameters
        $request->validate([
            'sai_sdpo_sess_id' => 'nullable|integer',
            'sai_sdpo_school_id' => 'nullable|integer',
            'sai_sdpo_dept_id' => 'nullable|integer',
            'academic_session' => 'nullable|integer',
            'student_status' => 'nullable|string',
            'program_id' => 'nullable|integer',
        ]);

        // Get the filter values
        $sessId = $request->input('sai_sdpo_sess_id');
        $schoolId = $request->input('sai_sdpo_school_id');
        $deptId = $request->input('sai_sdpo_dept_id');
        $academicSession = $request->input('academic_session');
        $student_status = $request->input('student_status');
        $program_id = $request->input('program_id');

        // Build the query
        $query = DB::table('students_apply_info AS sai')
            ->leftJoin('schools AS s', 's.school_id', '=', 'sai.sai_sdpo_school_id')
            ->leftJoin('departments AS d', 'd.dept_id', '=', 'sai.sai_sdpo_dept_id')
            ->leftJoin('students_personal_info AS spi', 'spi.spi_id', '=', 'sai.sai_spi_id');

        if (!empty($sessId)) {
            $query->where('sai.sai_sdpo_sess_id', $sessId); // Otherwise, use sessId filter
        }

        if (!empty($schoolId)) {
            $query->where('sai.sai_sdpo_school_id', $schoolId);
        }
        if (!empty($deptId)) {
            $query->where('sai.sai_sdpo_dept_id', $deptId);
        }

        if (!empty($program_id)) {
            $query->where('sai.sai_sdpo_prog_id', $program_id);
        }

        $query->where('sai.sai_student_status', $student_status ?: 'Active');

        $students = $query->select('sai.sai_enrollment_no', 's.school_name', 'd.dept_name', 'spi.spi_name', 'sai.sai_student_status')
            ->orderBy('sai.sai_sdpo_sess_id', 'asc')
            ->get();

        // Check if students data is empty
        if ($students->isEmpty()) {
            return response()->json([
                'status' => 'success',
                'message' => 'No students found.',
                'data' => []
            ]);
        }

        // Return the results as JSON
        return response()->json([
            'status' => 'success',
            'data' => $students,
        ]);
    }








    public function studentAdmissionData(Request $request)
    {
        $request->validate([
            'roadmapSession' => 'required',
        ], [
            'roadmapSession.required' => 'The roadmap session field is required.',
        ]);





        //     // Start the main query
// $query = "SELECT
// total_applied_students - COALESCE(fee_status, 0) AS Admission_Form_Submitted,
// COALESCE(fee_status, 0) - COALESCE(qualification_status, 0) AS Incomplete_for_Admission_Test,
// COALESCE(qualification_status, 0)-students_status AS Admission_Fee_Paid_rejected,
// COALESCE(students_status, 0)-students_moved_to_cms AS selected_but_Fee_not_paid,
//  COALESCE(left_students, 0) + COALESCE(deferred_students, 0) AS left_students ,
// (COALESCE(students_moved_to_cms, 0)-(COALESCE(left_students, 0) + COALESCE(deferred_students, 0) )) As BNU_Registration_ID,


        // (total_applied_students - COALESCE(fee_status, 0)) +
// (COALESCE(fee_status, 0) - COALESCE(qualification_status, 0)) +
// (COALESCE(qualification_status, 0) - students_status) +
// (COALESCE(students_status, 0) - students_moved_to_cms) +
// (COALESCE(students_moved_to_cms, 0) - (COALESCE(left_students, 0) + COALESCE(deferred_students, 0))) +
// (COALESCE(left_students, 0) + COALESCE(deferred_students, 0)) AS total_summary,
// total_applicants ";

        // if ($request->has('selectedDate') && !empty($request->selectedDate)) {
// $query.=",COALESCE(beforeCMS, 0) AS before_CMS,
// COALESCE(afterCMS, 0) AS after_CMS ";
// }


        // $query.="FROM (
// SELECT
//     COUNT(*) AS total_applied_students,
//     COUNT(CASE WHEN aai_pf_status = 'Fee Verified' THEN 1 END) AS fee_status,
//     COUNT(CASE WHEN aai_qualification_status IS NOT NULL AND aai_qualification_status = " .
//         ($request->qualification_status ?? "'Accepted'") . " THEN 1 END) AS qualification_status,
//     COUNT(CASE WHEN aai_status = " .  ($request->status ?? "'Selected'") . "  THEN 1 END) AS students_status,
//     COUNT(CASE WHEN cms_sai_enrollment_no IS NOT NULL THEN 1 END) AS students_moved_to_cms,
//     COUNT(CASE WHEN  sai_student_status in ('Left','Stuck-Off') THEN 1 END) AS left_students,
//     COUNT(CASE WHEN  sai_student_status = 'Defer' THEN 1 END) AS deferred_students,
//        COUNT( distinct aai_api_id) AS total_applicants ";

        //        if ($request->has('selectedDate') && !empty($request->selectedDate)) {
//         $selectedDate = $request->selectedDate;  // Get the selected date from the request

        //         // Convert the selectedDate format 'YYYY-MM-DD' to 'DD-MON-YY 12.00.00 PM'
//         $formattedSelectedDate = \Carbon\Carbon::createFromFormat('Y-m-d', $selectedDate)
//             ->format('d-M-y 12.00.00 A');  // Static time '12.00.00' for the given date (without fractional seconds)

        //         // Add query logic using the formatted date
//         $query .= ", COUNT(CASE WHEN sai_student_status IN ('Left', 'Stuck-Off', 'Defer')
//             AND sai_student_status_datetime <= TO_DATE('$formattedSelectedDate', 'DD-MON-RR HH.MI.SS AM') THEN 1 END) AS beforeCMS,
//             COUNT(CASE WHEN sai_student_status IN ('Left', 'Stuck-Off', 'Defer')
//             AND sai_student_status_datetime > TO_DATE('$formattedSelectedDate', 'DD-MON-RR HH.MI.SS AM')
//             AND sai_student_status_datetime <= CURRENT_TIMESTAMP THEN 1 END) AS afterCMS ";
//     }



        //  $query .="FROM admission_apply_info
// LEFT JOIN students_apply_info ON admission_apply_info.aai_id = students_apply_info.sai_id
// WHERE aai_finalsubmit = 1 AND aai_sdpo_sess_id = '{$request->roadmapSession}'";


        // // Close subquery
// $query .= ")";

        // echo $query;exit;




        //    echo $query;exit;
        // dd($request->all());

        // $results = DB::select($query);
        // dd($results);

        // -----------------------------------------------------------------------------------------




        $roadmapSession = $request->roadmapSession;
        $schoolId = $request->schoolId;
        $departmentId = $request->departmentId;
        $programId = $request->programId;


        $sessionCode = DB::select("select SESS_CODE from academic_sessions where SESS_ID = :roadmapSession", [':roadmapSession' => $roadmapSession]);
        $sessionCode = $sessionCode[0]->sess_code;


        $params = [':roadmapSession' => $roadmapSession];



        // Add filter parameters if they exist
        $filterConditions = '';
        if (!empty($schoolId)) {
            $params[':schoolId'] = $schoolId;
            $filterConditions .= ' AND aai_sdpo_school_id = :schoolId';
        }
        if (!empty($departmentId)) {
            $params[':departmentId'] = $departmentId;
            $filterConditions .= ' AND aai_sdpo_dept_id = :departmentId';
        }
        if (!empty($programId)) {
            $params[':programId'] = $programId;
            $filterConditions .= ' AND aai_sdpo_prog_id = :programId';
        }

        $dateFilter = '';

        if ($request->has('selectedDate') && !empty($request->selectedDate)) {
            $selectedDate = Carbon::createFromFormat('Y-m-d', $request->selectedDate)
                ->format('d-M-y 12.00.00 A');

            $dateFilter = ",
            COUNT(CASE WHEN sai_student_status IN ('Left', 'Stuck-Off', 'Defer') 
            AND sai_student_status_datetime <= TO_DATE(:selectedDate, 'DD-MON-RR HH.MI.SS AM') THEN 1 END) AS beforeCMS,
            COUNT(CASE WHEN sai_student_status IN ('Left', 'Stuck-Off', 'Defer') 
            AND sai_student_status_datetime > TO_DATE(:selectedDate, 'DD-MON-RR HH.MI.SS AM') 
            AND sai_student_status_datetime <= CURRENT_TIMESTAMP THEN 1 END) AS afterCMS";

            $params[':selectedDate'] = $selectedDate;
        }
          // AND aai_status not IN ( 'Waiting', 'Schedule_Changed','Left','Defer') comment from line no 497 in test_appeared where condition
        $query = "WITH applied_applicants AS (
        SELECT
           
            COUNT(
            CASE
                WHEN  aai_finalsubmit = 1 THEN
                    1
            END
             )        AS total_applied_finalsubmit,
            COUNT(CASE WHEN aai_pf_status = 'Fee Verified' and aai_finalsubmit = 1  THEN 1 END) AS fee_verified,
            COUNT(
            CASE
                WHEN aai_pf_status = 'Fee Verified' and aai_finalsubmit is null THEN
                    1
            END
        )        AS fee_verified_not_final_submit,
            COUNT(CASE WHEN aai_pf_status != 'Fee Verified' and aai_finalsubmit = 1 THEN 1 END) AS fee_not_verified,
            COUNT(CASE WHEN aai_qualification_status = 'Accepted'  AND aai_pf_status = 'Fee Verified'     and aai_finalsubmit = 1 THEN 1 END) AS qualification_status_Accepted,
            COUNT(CASE WHEN aai_status = 'Selected' and aai_finalsubmit = 1 AND aai_test_marks IS NOT NULL THEN 1 END) AS students_status_Selected,
            COUNT(CASE WHEN AAI_PF_STATUS='Fee Verified'  and (aai_status not in ('Selected','Waiting','Schedule_Changed','Left','Defer')) and AAI_QUALIFICATION_STATUS ='Accepted' AND aai_finalsubmit = 1 AND aai_test_marks IS NOT NULL THEN 1 END) AS students_status_Rejected,
            COUNT(  CASE WHEN cms_sai_enrollment_no IS NOT NULL and aai_finalsubmit = 1   and aai_status not in ('Schedule_Changed','Waiting') THEN 1 END) AS students_moved_to_cms, -- F2025-0408  it has schedule_changed and waiting status
            COUNT(CASE WHEN sai_student_status IN ('Left', 'Stuck-Off') THEN 1 END) AS left_students,
            COUNT(CASE WHEN sai_student_status = 'Defer' THEN 1 END) AS deferred_students
            $dateFilter
        FROM admission_apply_info
        LEFT JOIN students_apply_info ON admission_apply_info.aai_id = students_apply_info.sai_id
    
    
        WHERE   aai_sdpo_sess_id = :roadmapSession" . $filterConditions . "
    ),
  
    test_appeared AS (
        SELECT COUNT(aai_test_marks) AS applicants_appeared_for_test
        FROM admission_apply_info
        WHERE aai_sdpo_sess_id = :roadmapSession" . $filterConditions . "
        AND aai_test_marks IS NOT NULL
        AND aai_finalsubmit = 1
        AND aai_qualification_status = 'Accepted'
        AND (
        aai_status NOT IN ('Schedule_Changed', 'Left', 'Defer')
        OR aai_status IS NULL
        )
        AND aai_pf_status = 'Fee Verified'
       
    ),
    
      current_active_student AS (
        select count(distinct(aai_id)) as total_active_students 
        FROM   STUDENTS_APPLY_INFO
            LEFT JOIN students_PERSONAL_INFO  ON sai_spi_id = spi_id
            LEFT JOIN ADMISSION_APPLY_INFO ON aai_api_id = STUDENTS_APPLY_INFO.SAI_API_ID
                AND LOWER(CMS_SAI_ENROLLMENT_NO) = LOWER(SAI_ENROLLMENT_NO)
        WHERE SAI_STUDENT_STATUS='Active'
        and sai_sdpo_sess_id=:roadmapSession
        $filterConditions
    ),
    challan_paid AS (




        select count(distinct(SC_APP_ID)) as total_challan_paid from student_challan
        left join admission_apply_info on aai_id=sc_app_id
        left join schools on school_id=SC_SCHOOL_ID
        LEFT JOIN departments ON dept_id = sc_department_id
        left join  programs on prog_id=SC_PROGRAM_ID
        where SC_SESSION_ID=:roadmapSession
        and aai_sdpo_sess_id=:roadmapSession
        and sc_challan_type IN ( 'Full_semester', 'Fee', 'Short/Summer_Course')
        and (sc_enrollment_no like '%" . $sessionCode . "%' or sc_enrollment_no is null)
        and sc_challan_status='PAID'
        and aai_status='Selected'
        and aai_sdpo_sess_id=:roadmapSession
        $filterConditions

 

    ),


     challan_paid_unpaid_partial AS (
     select count(distinct(aai_id)) as total_challan_paid_unpaid_partial   
        FROM   STUDENTS_APPLY_INFO
            LEFT JOIN students_PERSONAL_INFO  ON sai_spi_id = spi_id
            LEFT JOIN ADMISSION_APPLY_INFO ON aai_api_id = STUDENTS_APPLY_INFO.SAI_API_ID
                AND LOWER(CMS_SAI_ENROLLMENT_NO) = LOWER(SAI_ENROLLMENT_NO)
        WHERE SAI_STUDENT_STATUS='Active'
            and sai_sdpo_sess_id=:roadmapSession 
        and aai_id  in (select distinct(SC_APP_ID) from student_challan
        where SC_SESSION_ID=:roadmapSession
        and sc_challan_type IN ('Full_semester', 'Fee', 'Short/Summer_Course')
        and (sc_enrollment_no like '%" . $sessionCode . "%' or sc_enrollment_no is null)
        and sc_challan_status in ('PAID','UNPAID')
        and CHECK_FEE_INSTALLMENTS in (0,1))
        $filterConditions
    ),
     challan_not_exists_but_active   AS (
     select count(distinct(aai_id)) as total_challan_not_exists_but_active   
        FROM   STUDENTS_APPLY_INFO
            LEFT JOIN students_PERSONAL_INFO  ON sai_spi_id = spi_id
            LEFT JOIN ADMISSION_APPLY_INFO ON aai_api_id = STUDENTS_APPLY_INFO.SAI_API_ID
                AND LOWER(CMS_SAI_ENROLLMENT_NO) = LOWER(SAI_ENROLLMENT_NO)
        WHERE SAI_STUDENT_STATUS='Active'
            and sai_sdpo_sess_id=:roadmapSession 
        and aai_id  not in (select distinct(SC_APP_ID) from student_challan
        where SC_SESSION_ID=:roadmapSession
        and sc_challan_type IN ('Full_semester', 'Fee', 'Short/Summer_Course')
        and (sc_enrollment_no like '%" . $sessionCode . "%' or sc_enrollment_no is null)
        and sc_challan_status in ('PAID','UNPAID')
        and CHECK_FEE_INSTALLMENTS in (0,1))
        $filterConditions
    ),

    
    
    challan_paid_cancelled AS (

       
    select count(distinct(SC_APP_ID)) as total_cancelled_challan_paid from BNUCMS.FIN_STUDENT_CANCEL_CHALLAN fsc
    left join admission_apply_info on aai_id=sc_app_id
    left join schools on school_id=SC_SCHOOL_ID
    LEFT JOIN departments ON dept_id = sc_department_id
    left join  programs on prog_id=SC_PROGRAM_ID
    where SC_SESSION_ID=:roadmapSession
    and aai_sdpo_sess_id=:roadmapSession
    and sc_challan_type IN ('Full_semester', 'Fee', 'Short/Summer_Course')
    and (sc_enrollment_no like '%" . $sessionCode . "%' OR sc_enrollment_no is null)
    and sc_challan_status='PAID'
    $filterConditions
    AND fsc.sc_app_id NOT IN (
            SELECT DISTINCT sc2.sc_app_id
            FROM student_challan sc2
            LEFT JOIN admission_apply_info aai2    ON aai2.aai_id = sc2.sc_app_id
        LEFT JOIN schools s2       ON s2.school_id = sc2.sc_school_id
            LEFT JOIN departments d2  ON d2.dept_id = sc2.sc_department_id
            LEFT JOIN programs p2  ON p2.prog_id = sc2.sc_program_id
            WHERE sc2.sc_session_id = :roadmapSession
            AND aai2.aai_sdpo_sess_id = :roadmapSession
            AND sc2.sc_challan_type IN ('Full_semester', 'Fee', 'Short/Summer_Course')
            AND (sc2.sc_enrollment_no LIKE '%" . $sessionCode . "%' OR sc2.sc_enrollment_no IS NULL)
            AND sc2.sc_challan_status='PAID'
        $filterConditions
  )
    )

    SELECT 
        (SELECT COUNT(DISTINCT aai_api_id) FROM admission_apply_info WHERE aai_sdpo_sess_id = :roadmapSession" . $filterConditions . ") AS registered_on_portal,
        (SELECT COUNT(*) FROM admission_apply_info WHERE aai_sdpo_sess_id = :roadmapSession" . $filterConditions . ") AS total_applied_applicants,
        fee_verified_not_final_submit,
        (SELECT COUNT(*) FROM admission_apply_info WHERE aai_sdpo_sess_id = :roadmapSession" . $filterConditions . ") - applied_applicants.total_applied_finalsubmit - applied_applicants.fee_verified_not_final_submit AS incomplete_form_submission,
        applied_applicants.total_applied_finalsubmit AS completed_form_submission,
        (applied_applicants.total_applied_finalsubmit - applied_applicants.fee_verified) AS processing_fee_not_verified,
        applied_applicants.fee_verified AS processing_fee_verified,
        (applied_applicants.fee_verified - applied_applicants.qualification_status_Accepted) AS documents_not_accepted,
        applied_applicants.qualification_status_Accepted AS documents_accepted,
        (applied_applicants.qualification_status_Accepted - test_appeared.applicants_appeared_for_test) AS applicants_not_appeared_for_test,
        test_appeared.applicants_appeared_for_test,
        applied_applicants.students_status_Rejected,
        applied_applicants.students_status_Selected AS admission_offered,
       
        ((applied_applicants.students_status_Selected) - (challan_paid.total_challan_paid) 
        - (challan_paid_cancelled.total_cancelled_challan_paid)) AS fee_not_paid,

        ((challan_paid.total_challan_paid) 
        + (challan_paid_cancelled.total_cancelled_challan_paid)
        )
        AS fee_paid,
    
        (challan_paid_unpaid_partial.total_challan_paid_unpaid_partial) as current_partial_full_paid,
        (challan_not_exists_but_active.total_challan_not_exists_but_active) as challan_not_exists_but_active,

        COALESCE(applied_applicants.students_moved_to_cms, 0) AS students_moved_to_cms,
        applied_applicants.left_students AS left_students,
        applied_applicants.deferred_students AS deferred_students,
        current_active_student.total_active_students AS active_students
        
        
        ";

        if ($request->has('selectedDate') && !empty($request->selectedDate)) {
            $query .= ",
            COALESCE(applied_applicants.beforeCMS, 0) AS before_CMS,
            COALESCE(applied_applicants.afterCMS, 0) AS after_CMS";
        }

        $query .= " FROM applied_applicants, test_appeared, challan_paid, challan_paid_cancelled, challan_paid_unpaid_partial, challan_not_exists_but_active, current_active_student";

        // Debug: Show the query with parameters substituted for SQL Developer
        // $debugQuery = $query;
        // foreach ($params as $key => $value) {
        //     if (is_string($value)) {
        //         $debugQuery = str_replace($key, "'" . $value . "'", $debugQuery);
        //     } else {
        //         $debugQuery = str_replace($key, $value, $debugQuery);
        //     }
        // }
        // echo "Query for SQL Developer:\n";
        // echo $debugQuery;
        // echo "\n\nOriginal Parameters:\n";
        // echo json_encode($params, JSON_PRETTY_PRINT);
        // exit;

        // Execute Query
        $results = DB::select($query, $params);
        // (COALESCE(applied_applicants.students_status_Selected, 0) - COALESCE(challan_paid.total_challan_paid, 0) - COALESCE(challan_paid_cancelled.total_cancelled_challan_paid, 0)) AS fee_not_paid,
        // (COALESCE(challan_paid.total_challan_paid, 0) + COALESCE(challan_paid_cancelled.total_cancelled_challan_paid, 0)) AS fee_paid,



        return response()->json([
            'status' => true,
            'studentData' => $results
        ]);
    }

    public function getLeftStudents(Request $request)
    {





        //     $sql = "SELECT
//             COUNT(sai_student_status) AS total_students,
//             schools.school_code,
//             schools.school_id,
//             COUNT(CASE WHEN sai_student_status = 'Left' THEN 1 END) AS left_students,
//             COUNT(CASE WHEN sai_student_status = 'Defer' THEN 1 END) AS defer_students,
//             COUNT(CASE WHEN sai_student_status = 'Active' THEN 1 END) AS active_students,
//             COUNT(CASE WHEN sai_student_status = 'Stuck-Off' THEN 1 END) AS stuck_off,
//             COUNT(CASE WHEN sai_student_status = 'Not Joined' THEN 1 END) AS not_joined
//         FROM schools
//         LEFT JOIN students_apply_info
//             ON students_apply_info.sai_sdpo_school_id = schools.school_id
//             AND students_apply_info.sai_sdpo_sess_id = :sessionId";

        // if ($request->has('selectedDate') && !empty($request->selectedDate)) {
//     // Format the selected date
//     $selectedDate = $request->selectedDate; // Assume the format is 'YYYY-MM-DD'
//     $formattedSelectedDate = \Carbon\Carbon::createFromFormat('Y-m-d', $selectedDate)
//         ->format('d-M-y 12.00.00 A'); // Convert to Oracle-compatible format

        //     // Add the dynamic condition for sai_student_status_datetime
//     if ($request->before_cms) {
//         $sql .= " AND sai_student_status_datetime <= TO_DATE('$formattedSelectedDate', 'DD-MON-RR HH.MI.SS AM')";
//     }

        //     if ($request->after_cms) {
//         $sql .= " AND sai_student_status_datetime > TO_DATE('$formattedSelectedDate', 'DD-MON-RR HH.MI.SS AM')
//                   AND sai_student_status_datetime <= CURRENT_TIMESTAMP";
//     }
// }

        // // Add condition for specific schools
// $sql .= " WHERE schools.school_code IN ('SVAD', 'SMC', 'SCIT', 'SE', 'SA', 'IP', 'SMS', 'SLASS')";

        // $sql .= " GROUP BY schools.school_code,schools.school_id";

        // // Define the parameters for binding
// $params = ['sessionId' => $request->sessionId];

        //     $getresult = DB::select($sql, $params);


        // echo $sql;exit;








        $sql = "SELECT
            COUNT(sai.sai_student_status) AS total_students,
            sch.school_code,
            sch.school_id,
            COUNT(
                CASE
                    WHEN sai.sai_student_status = 'Left' THEN 1
                END
            ) AS left_students,
            COUNT(
                CASE
                    WHEN sai.sai_student_status = 'Defer' THEN 1
                END
            ) AS defer_students,
            COUNT(
                CASE
                    WHEN sai.sai_student_status = 'Active' THEN 1
                END
            ) AS active_students,
            COUNT(
                CASE
                    WHEN sai.sai_student_status = 'Stuck-Off' THEN 1
                END
            ) AS stuck_off,
            COUNT(
                CASE
                    WHEN sai.sai_student_status = 'Not Joined' THEN 1
                END
            ) AS not_joined,
            -- Left enrolled ratio
            COUNT(
                CASE
                    WHEN sai.sai_student_status = 'Left'
                         AND EXISTS (
                             SELECT 1
                             FROM student_courses sc
                             WHERE sc.SC_SAI_ENROLLMENT_NO = sai.SAI_ENROLLMENT_NO
                         ) THEN 1
                END
            ) || '/' ||
            COUNT(
                CASE
                    WHEN sai.sai_student_status = 'Left'
                         AND NOT EXISTS (
                             SELECT 1
                             FROM student_courses sc
                             WHERE sc.SC_SAI_ENROLLMENT_NO = sai.SAI_ENROLLMENT_NO
                         ) THEN 1
                END
            ) AS \"Left(Enrolled/Not enrolled)\",
            -- Defer enrolled ratio
            COUNT(
                CASE
                    WHEN sai.sai_student_status = 'Defer'
                         AND EXISTS (
                             SELECT 1
                             FROM student_courses sc
                             WHERE sc.SC_SAI_ENROLLMENT_NO = sai.SAI_ENROLLMENT_NO
                         ) THEN 1
                END
            ) || '/' ||
            COUNT(
                CASE
                    WHEN sai.sai_student_status = 'Defer'
                         AND NOT EXISTS (
                             SELECT 1
                             FROM student_courses sc
                             WHERE sc.SC_SAI_ENROLLMENT_NO = sai.SAI_ENROLLMENT_NO
                         ) THEN 1
                END
            ) AS \"Defer(Enrolled/Not enrolled)\"
        FROM
            schools sch
            LEFT JOIN students_apply_info sai ON sai.sai_sdpo_school_id = sch.school_id
                                                AND sai.sai_sdpo_sess_id = :sessionId
        WHERE
            sch.school_code IN ('SVAD', 'SMC', 'SCIT', 'SE', 'SA', 'IP', 'SMS', 'SLASS')";







        // Add selectedDate filter if provided
        if ($request->has('selectedDate') && !empty($request->selectedDate)) {
            // Format the selected date
            $selectedDate = $request->selectedDate; // Assume the format is 'YYYY-MM-DD'
            $formattedSelectedDate = \Carbon\Carbon::createFromFormat('Y-m-d', $selectedDate)
                ->format('d-M-y 12.00.00 A'); // Convert to Oracle-compatible format

            // Add the dynamic condition for sai_student_status_datetime
            if ($request->before_cms) {
                $sql .= " AND SAI_STUDENT_STATUS_DATETIME <= TO_DATE('$formattedSelectedDate', 'DD-MON-RR HH.MI.SS AM')";
            }

            if ($request->after_cms) {
                $sql .= " AND SAI_STUDENT_STATUS_DATETIME > TO_DATE('$formattedSelectedDate', 'DD-MON-RR HH.MI.SS AM')
                          AND SAI_STUDENT_STATUS_DATETIME <= CURRENT_TIMESTAMP";
            }
        }
        $sql .= " GROUP BY sch.school_code, sch.school_id";

        // Define parameters for binding
        $params = ['sessionId' => $request->sessionId];

        // Execute the query
        $getresult = DB::select($sql, $params);

















        $sqlLeftDate = "SELECT
    TO_CHAR(SAI_STUDENT_STATUS_DATETIME, 'YYYY-MM') AS change_month,
    COUNT(CASE WHEN sai_sdpo_school_id = '1' THEN sai_enrollment_no ELSE NULL END) AS school_1,
    COUNT(CASE WHEN sai_sdpo_school_id = '2' THEN sai_enrollment_no ELSE NULL END) AS school_2,
    COUNT(CASE WHEN sai_sdpo_school_id = '3' THEN sai_enrollment_no ELSE NULL END) AS school_3,
    COUNT(CASE WHEN sai_sdpo_school_id = '4' THEN sai_enrollment_no ELSE NULL END) AS school_4,
    COUNT(CASE WHEN sai_sdpo_school_id = '5' THEN sai_enrollment_no ELSE NULL END) AS school_5,
    COUNT(CASE WHEN sai_sdpo_school_id = '6' THEN sai_enrollment_no ELSE NULL END) AS school_6,
    COUNT(CASE WHEN sai_sdpo_school_id = '7' THEN sai_enrollment_no ELSE NULL END) AS school_7,
    COUNT(CASE WHEN sai_sdpo_school_id = '12' THEN sai_enrollment_no ELSE NULL END) AS school_12,
    COUNT(CASE WHEN sai_sdpo_school_id = '21' THEN sai_enrollment_no ELSE NULL END) AS school_21
FROM (
    SELECT
        sai_sdpo_school_id,
        sai_student_status,
        sai_enrollment_no,
        SAI_STUDENT_STATUS_DATETIME,
        LAG(sai_student_status) OVER (
            PARTITION BY sai_enrollment_no
            ORDER BY SAI_STUDENT_STATUS_DATETIME
        ) AS previous_status
    FROM students_apply_info
    WHERE sai_sdpo_sess_id = $request->sessionId
      AND sai_student_status IN ('Left')
) subquery
WHERE
    sai_student_status != previous_status OR previous_status IS NULL
    AND sai_enrollment_no IN (
        SELECT sai_enrollment_no
        FROM students_apply_info
        WHERE sai_student_status IN ('Left')
    )
";

        // Add selectedDate filter if provided
        if ($request->has('selectedDate') && !empty($request->selectedDate)) {
            // Format the selected date
            $selectedDate = $request->selectedDate; // Assume the format is 'YYYY-MM-DD'
            $formattedSelectedDate = \Carbon\Carbon::createFromFormat('Y-m-d', $selectedDate)
                ->format('d-M-y 12.00.00 A'); // Convert to Oracle-compatible format

            // Add the dynamic condition for sai_student_status_datetime
            if ($request->before_cms) {
                $sqlLeftDate .= " AND SAI_STUDENT_STATUS_DATETIME <= TO_DATE('$formattedSelectedDate', 'DD-MON-RR HH.MI.SS AM')";
            }

            if ($request->after_cms) {
                $sqlLeftDate .= " AND SAI_STUDENT_STATUS_DATETIME > TO_DATE('$formattedSelectedDate', 'DD-MON-RR HH.MI.SS AM')
                          AND SAI_STUDENT_STATUS_DATETIME <= CURRENT_TIMESTAMP";
            }
        }

        $sqlLeftDate .= "
GROUP BY TO_CHAR(SAI_STUDENT_STATUS_DATETIME, 'YYYY-MM')
ORDER BY change_month";








        // echo $sqlLeftDate;exit;
        $getresultLeft = DB::select($sqlLeftDate);

        return response()->json([
            'status' => true,
            'studentData' => $getresult,
            'studentLeftData' => $getresultLeft
        ]);
    }






    public function getTransferredStudentsSchoolWise(Request $request)
    {

        $school_id = $request->school_id;
        $sessionId = $request->sessionId;


        // dd($request->all());

        // $data=DB::select("SELECT
//     schools.school_code AS school_name,
//     dept_name,
//     total_data.total_students,
//     total_data.enrolled_students,
//     total_data.total_students - total_data.enrolled_students AS not_enrolled
// FROM
//     (
//       SELECT
//             sai_sdpo_school_id,
//             sai_sdpo_dept_id,
//             COUNT(*) AS total_students,
//             (
//                 SELECT
//                     COUNT(DISTINCT sc.sc_sai_enrollment_no)
//                 FROM
//                     student_courses sc
//                 WHERE
//                     sc.sc_co_sess_id = $sessionId
//                     AND sc.sc_enrollment_status = 1
//                     AND sc.sc_sai_enrollment_no IN (
//                         SELECT
//                             sai_enrollment_no
//                         FROM
//                             students_apply_info
//                         WHERE
//                             sai_sdpo_sess_id = $sessionId
//                             AND sai_student_status = 'Active'
//                             AND sai_sdpo_school_id = sai_out.sai_sdpo_school_id
//                              AND sai_sdpo_dept_id = sai_out.sai_sdpo_dept_id
//                     )
//             ) AS enrolled_students,
//             (
//                 SELECT
//                     COUNT(DISTINCT sc.sc_sai_enrollment_no)
//                 FROM
//                     student_courses sc
//                 WHERE
//                     sc.sc_co_sess_id = $sessionId
//                     AND sc.sc_enrollment_status = 1
//                     AND sc.sc_status = 'A'
//                     AND sc.sc_sai_enrollment_no IN (
//                         SELECT
//                             sai_enrollment_no
//                         FROM
//                             students_apply_info
//                         WHERE
//                             sai_sdpo_sess_id = $sessionId
//                             AND sai_student_status = 'Active'
//                             AND sai_sdpo_school_id = sai_out.sai_sdpo_school_id
//                             AND sai_sdpo_dept_id = sai_out.sai_sdpo_dept_id
//                     )
//             ) AS enrolled_but_with_change_status
//         FROM
//             students_apply_info sai_out
//         WHERE
//             sai_sdpo_sess_id =$sessionId
//             AND sai_student_status = 'Active'
//         GROUP BY
//             sai_sdpo_school_id,sai_sdpo_dept_id
//     ) total_data
//     LEFT JOIN schools ON total_data.sai_sdpo_school_id = schools.school_id
//      LEFT JOIN departments ON total_data.sai_sdpo_dept_id = departments.dept_id
//      where lower(schools.school_id)=lower('$school_id')
// ORDER BY
//     total_data.sai_sdpo_school_id");





        $data = DB::select("SELECT
    schools.school_code AS school_name,
    dept_name,
    prog_name,
    total_data.total_students,
    total_data.enrolled_students,
    total_data.total_students - total_data.enrolled_students AS not_enrolled
FROM
    (
      SELECT
            sai_sdpo_school_id,
            sai_sdpo_dept_id,
            sai_sdpo_prog_id,
            COUNT(*) AS total_students,
            (
                SELECT
                    COUNT(DISTINCT sc.sc_sai_enrollment_no)
                FROM
                    student_courses sc
                WHERE
                    sc.sc_co_sess_id = $sessionId
                    AND sc.sc_enrollment_status = 1
                    AND sc.sc_sai_enrollment_no IN (
                        SELECT
                            sai_enrollment_no
                        FROM
                            students_apply_info
                        WHERE
                            sai_sdpo_sess_id = $sessionId
                            AND sai_student_status = 'Active'
                            AND sai_sdpo_school_id = sai_out.sai_sdpo_school_id
                             AND sai_sdpo_dept_id = sai_out.sai_sdpo_dept_id
                             AND sai_sdpo_prog_id = sai_out.sai_sdpo_prog_id
                    )
            ) AS enrolled_students,
            (
                SELECT
                    COUNT(DISTINCT sc.sc_sai_enrollment_no)
                FROM
                    student_courses sc
                WHERE
                    sc.sc_co_sess_id = $sessionId
                    AND sc.sc_enrollment_status = 1
                    AND sc.sc_status = 'A'
                    AND sc.sc_sai_enrollment_no IN (
                        SELECT
                            sai_enrollment_no
                        FROM
                            students_apply_info
                        WHERE
                            sai_sdpo_sess_id = $sessionId
                            AND sai_student_status = 'Active'
                            AND sai_sdpo_school_id = sai_out.sai_sdpo_school_id
                            AND sai_sdpo_dept_id = sai_out.sai_sdpo_dept_id
                             AND sai_sdpo_prog_id = sai_out.sai_sdpo_prog_id
                    )
            ) AS enrolled_but_with_change_status
        FROM
            students_apply_info sai_out
        WHERE
            sai_sdpo_sess_id = $sessionId
            AND sai_student_status = 'Active'
        GROUP BY
            sai_sdpo_school_id,sai_sdpo_dept_id, sai_sdpo_prog_id
    ) total_data
    LEFT JOIN schools ON total_data.sai_sdpo_school_id = schools.school_id
     LEFT JOIN departments ON total_data.sai_sdpo_dept_id = departments.dept_id
        LEFT JOIN programs ON total_data.sai_sdpo_prog_id = programs.prog_id
        where lower(schools.school_id)=lower('$school_id')
ORDER BY
    total_data.sai_sdpo_school_id");


        // dd(1111);
        return response()->json(['data' => $data]);




    }


    public function courseAttendanceReport(Request $request)
    {
        // dd(111111111111);
        // Accessing the form data from the request
        $formData = $request->input('formData');
        $roadmapSession = $formData['roadmapSession'] ?? null;
        $sessionId = $formData['academicSession'] ?? null;
        $loginUserUsername = auth()->user()->username;
        $loginUserSchoolId = auth()->user()->school_id;


        // Priority is given to the roadmapSession if both are provided
        //$sessionId = $roadmapSession ?: $academicSession;

        // Start building the SQL query with placeholders for sessionId
        $sql2 = "
    WITH left_student_courses AS (
        SELECT
            sai.sai_enrollment_no,
            s.school_id,
            s.school_name,
            s.school_code,
            p.prog_name
        FROM
            students_apply_info sai
        LEFT JOIN schools s ON s.school_id = sai.sai_sdpo_school_id
        LEFT JOIN programs p ON p.prog_id = sai.sai_sdpo_prog_id
        WHERE 1=1
            AND sai.sai_enrollment_no IN (
                SELECT
                    sc.sc_sai_enrollment_no
                FROM
                    student_courses sc
                WHERE
                    sc.sc_co_sess_id = :sessionId
                    AND sc.sc_enrollment_status = 1
                    AND sc.sc_id IN (
                    SELECT gh.gh_sc_id
                    FROM grade_history gh
                    WHERE gh.is_star = '*'
                    AND gh.gh_co_sess_id = :sessionId
                )
                    GROUP BY sc.sc_sai_enrollment_no
            HAVING COUNT(CASE WHEN sc.sc_status = 'Withdrawal' THEN 1 END) > 2
            )
            " . ($roadmapSession ? "AND sai.sai_sdpo_sess_id = :roadmapSession" : "") . "
          
        )

        SELECT
            s.school_id,
            s.school_name,
            s.school_code,
            COALESCE(COUNT(lsc.sai_enrollment_no), 0) AS left_students
        FROM
            schools s
        LEFT JOIN
            left_student_courses lsc ON s.school_id = lsc.school_id
        WHERE
            s.school_id NOT IN (8, 9, 10, 11,22)
        GROUP BY
            s.school_id, s.school_name, s.school_code
        ORDER BY
            s.school_id ASC";

        $sql = "SELECT 
            
            s.school_id AS school_id,
            s.school_name,
            s.school_code,
            NVL(COUNT(t.sc_sai_enrollment_no), 0) AS left_students
        FROM schools s
        LEFT JOIN (
            SELECT 
                distinct sc.sc_sai_enrollment_no,
                sai.sai_sdpo_school_id
            FROM student_courses sc
            JOIN students_apply_info sai 
                ON sai.sai_enrollment_no = sc.sc_sai_enrollment_no
            WHERE sc.sc_co_sess_id = :sessionId
            AND sc.sc_enrollment_status = 1
            AND sai.sai_student_status IN ('Active', 'Expected-Graduate')";
        if ($roadmapSession) {
            $sql .= " AND sai.sai_sdpo_sess_id = :roadmapSession";
        }
        $sql .= " AND (sc.total_classes - sc.total_presents) >= (sc.allowed_absents / 2)
        ) t ON t.sai_sdpo_school_id = s.school_id
        WHERE s.school_id NOT IN (8,9,10,11,22)";

        if (!in_array($loginUserUsername, ['moeedyusuf', 'sumair.shafique', 'bilal.saleem', 'zain.ali', 'arshad.khurshid'])) {
            if ($loginUserSchoolId == 12) {
                $loginUserSchoolId = [12, 21]; // make it an array
            }
            if (is_array($loginUserSchoolId)) {
                $ids = implode(',', $loginUserSchoolId);
                $sql .= " AND s.school_id IN ($ids)";
            } else {
                $sql .= " AND s.school_id = $loginUserSchoolId";
            }
        }

        $sql .= "GROUP BY s.school_code, school_name,s.school_id
        ORDER BY s.school_id";

        //   echo $sql;exit;




        $sqlfinal = "WITH left_student_courses AS (
            SELECT
                sai.sai_enrollment_no,
                sai.sai_sdpo_school_id AS school_id
            FROM
                students_apply_info sai
            WHERE
                sai.sai_enrollment_no IN (
                    SELECT
                        sc.sc_sai_enrollment_no
                    FROM
                        student_courses sc
                    WHERE
                        sc.sc_co_sess_id = :sessionId
                        AND sc.sc_enrollment_status = 1
                        AND sc.sc_id IN (
                            SELECT gh.gh_sc_id
                            FROM grade_history gh
                            WHERE gh.is_star = '*'
                            AND gh.gh_co_sess_id = :sessionId
                        )
                    GROUP BY
                        sc.sc_sai_enrollment_no
                    HAVING
                        COUNT(CASE WHEN sc.sc_status = 'Withdrawal' THEN 1 END) > 2
                )
        ),
        absent_student_courses AS (
            SELECT 
                sai.sai_sdpo_school_id AS school_id,
                sc.sc_sai_enrollment_no
            FROM student_courses sc
            JOIN students_apply_info sai 
                ON sai.sai_enrollment_no = sc.sc_sai_enrollment_no
            WHERE sc.sc_co_sess_id = :sessionId
            AND sc.sc_enrollment_status = 1
            AND sai.sai_student_status IN ('Active', 'Expected-Graduate')
            AND (sc.total_classes - sc.total_presents) >= (sc.allowed_absents / 2)
        )
        SELECT 
            s.school_id,
            s.school_name,
            s.school_code,

            -- ✅ LEFT students count
            (SELECT COUNT(DISTINCT lsc.sai_enrollment_no)
            FROM left_student_courses lsc
            WHERE lsc.school_id = s.school_id) AS left_students,

            -- ✅ ABSENT students count (matches your 235 logic)
            (SELECT COUNT(ascq.sc_sai_enrollment_no)
            FROM absent_student_courses ascq
            WHERE ascq.school_id = s.school_id) AS absent_students

        FROM schools s
        WHERE s.school_id NOT IN (8, 9, 10, 12, 22)
        ORDER BY s.school_id";

        try {
            // Execute the query with the sessionId and potentially roadmapSession as bindings
            $bindings = ['sessionId' => $sessionId];
            if ($roadmapSession) {
                $bindings['roadmapSession'] = $roadmapSession;
            }


            // echo DB::getQueryLog();exit;

            $results = DB::select($sql, $bindings);

            // Return the results as JSON
            return response()->json($results);
        } catch (\Exception $e) {
            // Handle errors by returning a 500 error response
            return response()->json([
                'error' => 'An error occurred while fetching the report',
                'message' => $e->getMessage()
            ], 500);
        }
    }


    public function courseAttendanceReportWithdraw(Request $request)
    {
        // dd(111111111111);
        // Accessing the form data from the request
        $formData = $request->input('formData');
        $roadmapSession = $formData['roadmapSession'] ?? null;
        $sessionId = $formData['academicSession'] ?? null;
        $loginUserUsername = auth()->user()->username;
        $loginUserSchoolId = auth()->user()->school_id;


        // Priority is given to the roadmapSession if both are provided
        //$sessionId = $roadmapSession ?: $academicSession;

        // Start building the SQL query with placeholders for sessionId
        $sql = "
    WITH left_student_courses AS (
        SELECT
            sai.sai_enrollment_no,
            s.school_id,
            s.school_name,
            s.school_code,
            p.prog_name
        FROM
            students_apply_info sai
        LEFT JOIN schools s ON s.school_id = sai.sai_sdpo_school_id
        LEFT JOIN programs p ON p.prog_id = sai.sai_sdpo_prog_id
        WHERE 1=1
            AND sai.sai_enrollment_no IN (
                SELECT
                    sc.sc_sai_enrollment_no
                FROM
                    student_courses sc
                WHERE
                    sc.sc_co_sess_id = :sessionId
                    AND sc.sc_enrollment_status = 1
                    AND sc.sc_id IN (
                    SELECT gh.gh_sc_id
                    FROM grade_history gh
                    WHERE gh.is_star = '*'
                    AND gh.gh_co_sess_id = :sessionId
                )
                    GROUP BY sc.sc_sai_enrollment_no
            HAVING COUNT(CASE WHEN sc.sc_status = 'Withdrawal' THEN 1 END) > 2
            )
            " . ($roadmapSession ? "AND sai.sai_sdpo_sess_id = :roadmapSession" : "") . "
          
        )

        SELECT
            s.school_id,
            s.school_name,
            s.school_code,
            COALESCE(COUNT(lsc.sai_enrollment_no), 0) AS left_students
        FROM
            schools s
        LEFT JOIN
            left_student_courses lsc ON s.school_id = lsc.school_id
        WHERE
            s.school_id NOT IN (8, 9, 10, 11,22)
        GROUP BY
            s.school_id, s.school_name, s.school_code
        ORDER BY
            s.school_id ASC";



        try {
            // Execute the query with the sessionId and potentially roadmapSession as bindings
            $bindings = ['sessionId' => $sessionId];
            if ($roadmapSession) {
                $bindings['roadmapSession'] = $roadmapSession;
            }


            // echo DB::getQueryLog();exit;

            $results = DB::select($sql, $bindings);

            // Return the results as JSON
            return response()->json($results);
        } catch (\Exception $e) {
            // Handle errors by returning a 500 error response
            return response()->json([
                'error' => 'An error occurred while fetching the report',
                'message' => $e->getMessage()
            ], 500);
        }
    }


    public function schoolWiseStudentsWithdraw(Request $request)
    {
        $request->validate([
            'school_id' => 'required|string|max:255',
        ]);

        $school_id = $request->input('school_id');
        $roadmapSession = $request->input('roadmapSession');
        $sessionId = $request->input('academicSession');

        // Base SQL query
        $sql = "
        SELECT
            sai.sai_enrollment_no,
            s.school_name,
            s.school_code,
            p.prog_name,
            s.school_id,
            sai.sai_student_status,
            ( SELECT COUNT(*)
              FROM student_courses sc2
              WHERE sc2.sc_sai_enrollment_no = sai_enrollment_no
              AND sc2.sc_enrollment_status = 1
              AND sc2.sc_co_sess_id = :sessionId
            ) as total_courses,
            ( SELECT COUNT(*)
              FROM student_courses sc2
              WHERE sc2.sc_sai_enrollment_no = sai_enrollment_no
              AND sc2.sc_enrollment_status = 1
              AND sc2.sc_status = 'Withdrawal'
              AND sc2.sc_co_sess_id = :sessionId
            ) as total_withdrawn
        FROM students_apply_info sai
        LEFT JOIN schools s ON s.school_id = sai.sai_sdpo_school_id
        LEFT JOIN programs p ON p.prog_id = sai.sai_sdpo_prog_id
        WHERE
             sai.sai_enrollment_no IN (
                SELECT sc.sc_sai_enrollment_no
                FROM student_courses sc
                WHERE sc.sc_co_sess_id = :sessionId
                AND sc.sc_enrollment_status = 1
               AND sc.sc_id IN (
                    SELECT gh.gh_sc_id
                    FROM grade_history gh
                    WHERE gh.is_star = '*'
                    AND gh.gh_co_sess_id = :sessionId
                )
            GROUP BY sc.sc_sai_enrollment_no
            HAVING COUNT(CASE WHEN sc.sc_status = 'Withdrawal' THEN 1 END) > 2
            )
            AND s.school_id = :schoolId
            AND sai.sai_enrollment_no IN (
                SELECT gh.gh_sai_enrollment_no
                FROM grade_history gh
                WHERE gh.is_star = '*'
                AND gh.gh_co_sess_id = :sessionId
            )
    ";

        // Bindings
        $bindings = [
            'sessionId' => $sessionId,
            'schoolId' => $school_id,
        ];

        // Conditionally add roadmap session
        if (!empty($roadmapSession)) {
            $sql .= " AND sai.sai_sdpo_sess_id = :roadmapSession";
            $bindings['roadmapSession'] = $roadmapSession;
        }
        // echo $sql;exit;
        try {
            $results = DB::select($sql, $bindings);

            if (empty($results)) {
                return response()->json(['message' => 'No data found for this session'], 404);
            }

            return response()->json($results);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred while fetching the report',
                'message' => $e->getMessage()
            ], 500);
        }
    }




    public function schoolWiseStudents(Request $request)
    {
        $request->validate([
            'school_id' => 'required|string|max:255',
        ]);

        $school_id = $request->input('school_id');
        $roadmapSession = $request->input('roadmapSession');
        $sessionId = $request->input('academicSession');

        // Base SQL query
        $sql = "
        SELECT
            sai.sai_enrollment_no,
            s.school_name,
            s.school_code,
            p.prog_name,
            s.school_id,
            sai.sai_student_status,
            ( SELECT COUNT(*)
              FROM student_courses sc2
              WHERE sc2.sc_sai_enrollment_no = sai_enrollment_no
              AND sc2.sc_enrollment_status = 1
              AND sc2.sc_co_sess_id = :sessionId
            ) as total_courses,
            ( SELECT COUNT(*)
              FROM student_courses sc2
              WHERE sc2.sc_sai_enrollment_no = sai_enrollment_no
              AND sc2.sc_enrollment_status = 1
              AND sc2.sc_status = 'Withdrawal'
              AND sc2.sc_co_sess_id = :sessionId
            ) as total_withdrawn
        FROM students_apply_info sai
        LEFT JOIN schools s ON s.school_id = sai.sai_sdpo_school_id
        LEFT JOIN programs p ON p.prog_id = sai.sai_sdpo_prog_id
        WHERE
             sai.sai_enrollment_no IN (
                SELECT sc.sc_sai_enrollment_no
                FROM student_courses sc
                WHERE sc.sc_co_sess_id = :sessionId
                AND sc.sc_enrollment_status = 1
               AND sc.sc_id IN (
                    SELECT gh.gh_sc_id
                    FROM grade_history gh
                    WHERE gh.is_star = '*'
                    AND gh.gh_co_sess_id = :sessionId
                )
            GROUP BY sc.sc_sai_enrollment_no
            HAVING COUNT(CASE WHEN sc.sc_status = 'Withdrawal' THEN 1 END) > 2
            )
            AND s.school_id = :schoolId
            AND sai.sai_enrollment_no IN (
                SELECT gh.gh_sai_enrollment_no
                FROM grade_history gh
                WHERE gh.is_star = '*'
                AND gh.gh_co_sess_id = :sessionId
            )
    ";

        $sql = "SELECT 
    distinct sc.sc_sai_enrollment_no as sai_enrollment_no,spi_mobile,
    s.school_name,
    s.school_code,
    p.prog_name,
    s.school_id,
    sai_student_status,
      sai_sdpo_school_id,
    (select  count(*) from student_courses sc1 where sc1.sc_enrollment_status = 1 and sc1.sc_co_sess_id = 165
    and sc1.sc_sai_enrollment_no = sc.sc_sai_enrollment_no )as total_courses,
    (    SELECT COUNT(*) 
    FROM student_courses sc1
    WHERE sc1.sc_enrollment_status = 1
      AND sc1.sc_co_sess_id = :sessionId
      AND sc1.sc_sai_enrollment_no = sc.sc_sai_enrollment_no
      AND (sc1.total_classes - sc1.total_presents) >= (sc1.allowed_absents / 2) ) as total_withdrawn

    FROM student_courses sc
    LEFT JOIN students_apply_info sai 
        ON sai.sai_enrollment_no = sc.sc_sai_enrollment_no
    left join students_personal_info on sai_spi_id = spi_id
    LEFT JOIN schools s 
        ON sai.sai_sdpo_school_id = s.school_id
    LEFT JOIN programs p 
        ON sai.sai_sdpo_prog_id = p.prog_id
    WHERE sc.sc_co_sess_id = :sessionId
    AND sc.sc_enrollment_status = 1
    AND sai.sai_student_status IN ('Active', 'Expected-Graduate')
    AND (sc.total_classes - sc.total_presents) >= (sc.allowed_absents / 2)
    and sai_sdpo_school_id = :schoolId";
        if ($roadmapSession) {
            $sql .= " AND sai.sai_sdpo_sess_id = :roadmapSession";
        }
        $sql .= " ORDER BY sai.sai_sdpo_school_id, sc.sc_sai_enrollment_no";

        // Bindings
        $bindings = [
            'sessionId' => $sessionId,
            'schoolId' => $school_id,
        ];

        // Conditionally add roadmap session
        if (!empty($roadmapSession)) {
            // $sql .= " AND sai.sai_sdpo_sess_id = :roadmapSession";
            $bindings['roadmapSession'] = $roadmapSession;
        }
        //   echo $sql;exit;
        try {
            $results = DB::select($sql, $bindings);

            if (empty($results)) {
                return response()->json(['message' => 'No data found for this session'], 404);
            }

            return response()->json($results);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred while fetching the report',
                'message' => $e->getMessage()
            ], 500);
        }
    }



    public function enrolledCourses(Request $request)
    {
        //  dd($request->all());

        $enrollmentNo = $request->input('enrollment_no');
        $sessionId = $request->input('session_id'); // Retrieve session ID from the request

        // dd($sessionId);

        // Prepare the SQL query using parameter binding for security
        $sql = "SELECT
            student_courses.sc_sai_enrollment_no,
            total_classes,
            total_presents,
            total_classes-total_presents as total_absents,
            allowed_absents,
            academic_sessions.sess_name,
            academic_sessions.sess_id,
            student_courses.attendance_percentage,
            student_courses.sc_status,
            get_course_name(student_courses.sc_co_id) AS course_title,
            get_teachername(student_courses.sc_co_id) AS teachername,
            student_courses.sc_co_id,
            (select gh_grade from grade_history where gh_sai_enrollment_no = sc_sai_enrollment_no and gh_sc_id = sc_id) as grade,
            (select gh_desc from grade_history where gh_sai_enrollment_no = sc_sai_enrollment_no and gh_sc_id = sc_id) as gh_desc
        FROM
            student_courses
        LEFT JOIN
            academic_sessions ON student_courses.sc_co_sess_id = academic_sessions.sess_id
        LEFT JOIN
            courses_two ON student_courses.sc_course_id = courses_two.course_id
        WHERE
            1=1
            and sc_enrollment_status = 1
            AND student_courses.sc_sai_enrollment_no = :enrollment_no
            --AND student_courses.attendance_percentage IS NOT NULL
            AND student_courses.sc_co_sess_id = :session_id  -- Filter by session ID
        ORDER BY
            academic_sessions.sess_id ASC
    ";
        //    echo $sql;exit;
        // Execute the query with parameter binding
        $data = DB::select($sql, [
            'enrollment_no' => $enrollmentNo,
            'session_id' => $sessionId
        ]);

        // Return the results as JSON
        return response()->json($data);
    }


    public function enrolledCoursesWithdraw(Request $request)
    {
        //  dd($request->all());

        $enrollmentNo = $request->input('enrollment_no');
        $sessionId = $request->input('session_id'); // Retrieve session ID from the request

        // dd($sessionId);

        // Prepare the SQL query using parameter binding for security
        $sql = "SELECT
            student_courses.sc_sai_enrollment_no,
            total_classes,
            total_presents,
            total_classes-total_presents as total_absents,
            allowed_absents,
            academic_sessions.sess_name,
            academic_sessions.sess_id,
            student_courses.attendance_percentage,
            student_courses.sc_status,
            get_course_name(student_courses.sc_co_id) AS course_title,
            get_teachername(student_courses.sc_co_id) AS teachername,
            student_courses.sc_co_id,
            (select gh_grade from grade_history where gh_sai_enrollment_no = sc_sai_enrollment_no and gh_sc_id = sc_id) as grade,
            (select gh_desc from grade_history where gh_sai_enrollment_no = sc_sai_enrollment_no and gh_sc_id = sc_id) as gh_desc
        FROM
            student_courses
        LEFT JOIN
            academic_sessions ON student_courses.sc_co_sess_id = academic_sessions.sess_id
        LEFT JOIN
            courses_two ON student_courses.sc_course_id = courses_two.course_id
        WHERE
            1=1
            and sc_enrollment_status = 1
            AND student_courses.sc_sai_enrollment_no = :enrollment_no
            --AND student_courses.attendance_percentage IS NOT NULL
            AND student_courses.sc_co_sess_id = :session_id  -- Filter by session ID
        ORDER BY
            academic_sessions.sess_id ASC
    ";
        //    echo $sql;exit;
        // Execute the query with parameter binding
        $data = DB::select($sql, [
            'enrollment_no' => $enrollmentNo,
            'session_id' => $sessionId
        ]);

        // Return the results as JSON
        return response()->json($data);
    }






    public function StudentProfile($studentEnrollment)
    {
        // Fetch the student by their enrollment number

        $studentProfile = DB::table('students_apply_info')
            ->leftJoin('students_personal_info', 'students_apply_info.sai_spi_id', '=', 'students_personal_info.spi_id')
            ->leftJoin('schools', 'students_apply_info.sai_sdpo_school_id', '=', 'schools.school_id')
            ->leftJoin('departments', 'students_apply_info.sai_sdpo_dept_id', '=', 'departments.dept_id')
            ->leftJoin('programs', 'students_apply_info.sai_sdpo_prog_id', '=', 'programs.prog_id')
            ->where('students_apply_info.sai_enrollment_no', $studentEnrollment)
            ->select(
                'students_apply_info.sai_enrollment_no as enrollment_no',
                'students_apply_info.sai_student_status as student_status',
                'students_personal_info.spi_name as student_name',
                'students_personal_info.spi_fathername as father_name',
                'students_personal_info.spi_mobile as mobile_number',
                'students_personal_info.spi_email as email',
                'schools.school_name as school_name',
                'departments.dept_name as department_name',
                'programs.prog_name as program_name',
                'programs.prog_degree_level as degree_level'
            )
            ->first();
        $timetable = DB::table('student_courses')
            ->select(
                'course_cd_name',
                DB::raw('get_teachername(sc_co_id) AS teachername'),
                'tt_subject_day',
                'tt_subject_time_from',
                'tt_subject_time_to',
                'sc_sai_enrollment_no'
            )
            ->leftJoin('courses_offered', 'co_id', '=', 'sc_co_id')
            ->leftJoin('timetable', function ($join) {
                $join->on('tt_co_id', '=', 'co_id')
                    ->orOn('tt_co_id', '=', 'parent_co_id');
            })
            ->leftJoin('courses_two', 'course_id', '=', 'co_course_id')
            ->leftJoin('ACADEMIC_SESSIONS', 'sess_id', '=', 'sc_co_sess_id')
            ->whereNotIn('sc_co_id', function ($query) use ($studentEnrollment) {
                $query->select('gh_co_id')
                    ->from('grade_history')
                    ->where('gh_sai_enrollment_no', $studentEnrollment)
                    ->whereNotNull('gh_co_id')
                    ->where('gh_co_id', '>', 0)
                    ->where('gh_exam_fsubmit', 1);
            })
            ->where('sc_sai_enrollment_no', $studentEnrollment)
            ->where('sc_enrollment_status', 1)
            ->whereIn('sc_status', ['Approve', 'A'])
            ->where('co_status', 'E')
            ->get();



        return view(
            'grader.reportgenerator.BNUReportsGenerator.studentProfile',
            [
                'studentProfile' => $studentProfile,
                'enrollmentNo' => $studentEnrollment,
                'challans' => $this->fineChallanGeneratedList($studentEnrollment),
                'warningLetters' => $this->checkWarningLetter($studentEnrollment),
                'attendanceDetails' => $this->getAttendanceDetails($studentEnrollment),
                'timetable' => $timetable
            ]
        );
    }



    public function fineChallanGeneratedList($studentEnrollment)
    {

        $sql = "SELECT
    sc.id,
    sc.sc_enrollment_no,
    sc.sc_app_id,
    sess.sess_name,
    fc.fc_category_name AS category_name,
    fcd.fcd_violation_type AS violation_type,
    sc.sc_amount,
    sc.sc_challan_status,
    sc.invoiceamount,
    sc.sc_challan_type,
    sc.sc_entry_user_id,
    sc.sc_entry_user_name AS fined_by
FROM
    student_challan sc
LEFT JOIN
    fine_category fc ON sc.sc_category_id = fc.id
LEFT JOIN
    academic_sessions sess ON sc.sc_session_id = sess.sess_id
LEFT JOIN
    fine_category_details fcd ON sc.sc_violation_type_id = fcd.id
WHERE  1=1

    AND sc.sc_enrollment_no='$studentEnrollment'
";
        // echo $sql;exit;
        return $challansRec = DB::select($sql);
        //    sc.sc_challan_type = 'Fine'
    }



    public function checkWarningLetter($enrollmentNo)
    {
        // dd($enrollmentNo);
        $checkRecords = DB::table('student_warning_letter')
            ->select(
                'id',
                'swl_enrollment_no',
                'warning_letter_type',
                'swl_filename',
                'entry_timestamp',
                'status',
                'warning_letter_name',
                DB::raw('emp_f_name || \' \' || emp_l_name AS full_name')
            )
            ->join('emp_employees', 'student_warning_letter.ENTRY_USER_ID', '=', 'emp_employees.employee_id')
            ->where('swl_enrollment_no', strtoupper($enrollmentNo))
            ->get();

        return $checkRecords;
    }

    public function getAttendanceDetails($enrollmentNo)
    {
        $sql = "
        SELECT
            get_course_name(sc_co_id) AS course_name,
            get_teachername(sc_co_id) AS faculty,
            total_classes,
            total_presents,
            (total_classes - total_presents) AS absents
        FROM
            student_courses
        WHERE
            sc_sai_enrollment_no = ?
    ";

        // Use parameterized query to prevent SQL injection
        $result = DB::select($sql, [$enrollmentNo]);

        // Return the result
        return $result;
    }



    public function getStudentsTransferredToCMS(Request $request)
    {
        // Retrieve sessionId from the request
        $sessionId = $request->input('sessionId');

        // Use parameterized query to avoid SQL injection
        $sql = "
        SELECT
            schools.school_id,
            schools.school_code as school_name,
            total_data.total_students,
            total_data.enrolled_students,
            total_data.total_students - total_data.enrolled_students AS not_enrolled
        FROM (
            SELECT
                sai_sdpo_school_id,
                COUNT(*) AS total_students,
                (
                    SELECT
                        COUNT(DISTINCT sc.sc_sai_enrollment_no)
                    FROM
                        student_courses sc
                    WHERE
                        sc.sc_co_sess_id = :sessionId
                        AND sc.sc_enrollment_status = 1
                        AND sc.sc_sai_enrollment_no IN (
                            SELECT
                                sai_enrollment_no
                            FROM
                                students_apply_info
                            WHERE
                                sai_sdpo_sess_id = :sessionId
                                AND sai_student_status = 'Active'
                                AND sai_sdpo_school_id = sai_out.sai_sdpo_school_id
                        )
                ) AS enrolled_students,
                (
                    SELECT
                        COUNT(DISTINCT sc.sc_sai_enrollment_no)
                    FROM
                        student_courses sc
                    WHERE
                        sc.sc_co_sess_id = :sessionId
                        AND sc.sc_enrollment_status = 1
                        AND sc.sc_status = 'A'
                        AND sc.sc_sai_enrollment_no IN (
                            SELECT
                                sai_enrollment_no
                            FROM
                                students_apply_info
                            WHERE
                                sai_sdpo_sess_id = :sessionId
                                AND sai_student_status = 'Active'
                                AND sai_sdpo_school_id = sai_out.sai_sdpo_school_id
                        )
                ) AS enrolled_but_with_change_status
            FROM
                students_apply_info sai_out
            WHERE
                sai_sdpo_sess_id = :sessionId
                AND sai_student_status = 'Active'
            GROUP BY
                sai_sdpo_school_id
        ) total_data
        LEFT JOIN schools ON total_data.sai_sdpo_school_id = schools.school_id
        ORDER BY total_data.sai_sdpo_school_id
    ";
        // echo $sql;exit;
        // Execute the query with parameter binding
        $result = DB::select($sql, ['sessionId' => $sessionId]);

        // Return the result as JSON
        return response()->json($result);
    }









    public function LeftStudentsSchoolWise(Request $request)
    {

        $school_id = $request->school_id;
        $sessionId = $request->sessionId;

        $data = DB::select("SELECT
        schools.school_code AS school_name,
        dept_name,
        prog_name,
        total_data.total_students,
        total_data.enrolled_students,
        total_data.total_students - total_data.enrolled_students AS not_enrolled
    FROM
        (
          SELECT
                sai_sdpo_school_id,
                sai_sdpo_dept_id,
                sai_sdpo_prog_id,
                COUNT(*) AS total_students,
                (
                    SELECT
                        COUNT(DISTINCT sc.sc_sai_enrollment_no)
                    FROM
                        student_courses sc
                    WHERE
                        sc.sc_co_sess_id = $sessionId
                        AND sc.sc_enrollment_status = 1
                        AND sc.sc_sai_enrollment_no IN (
                            SELECT
                                sai_enrollment_no
                            FROM
                                students_apply_info
                            WHERE
                                sai_sdpo_sess_id = $sessionId
                                AND sai_student_status = 'Active'
                                AND sai_sdpo_school_id = sai_out.sai_sdpo_school_id
                                 AND sai_sdpo_dept_id = sai_out.sai_sdpo_dept_id
                                 AND sai_sdpo_prog_id = sai_out.sai_sdpo_prog_id
                        )
                ) AS enrolled_students,
                (
                    SELECT
                        COUNT(DISTINCT sc.sc_sai_enrollment_no)
                    FROM
                        student_courses sc
                    WHERE
                        sc.sc_co_sess_id = $sessionId
                        AND sc.sc_enrollment_status = 1
                        AND sc.sc_status = 'A'
                        AND sc.sc_sai_enrollment_no IN (
                            SELECT
                                sai_enrollment_no
                            FROM
                                students_apply_info
                            WHERE
                                sai_sdpo_sess_id = $sessionId
                                AND sai_student_status = 'Active'
                                AND sai_sdpo_school_id = sai_out.sai_sdpo_school_id
                                AND sai_sdpo_dept_id = sai_out.sai_sdpo_dept_id
                                 AND sai_sdpo_prog_id = sai_out.sai_sdpo_prog_id
                        )
                ) AS enrolled_but_with_change_status
            FROM
                students_apply_info sai_out
            WHERE
                sai_sdpo_sess_id = $sessionId
                AND sai_student_status = 'Active'
            GROUP BY
                sai_sdpo_school_id,sai_sdpo_dept_id, sai_sdpo_prog_id
        ) total_data
        LEFT JOIN schools ON total_data.sai_sdpo_school_id = schools.school_id
         LEFT JOIN departments ON total_data.sai_sdpo_dept_id = departments.dept_id
            LEFT JOIN programs ON total_data.sai_sdpo_prog_id = programs.prog_id
            where lower(schools.school_id)=lower('$school_id')
    ORDER BY
        total_data.sai_sdpo_school_id");


        // dd(1111);
        return response()->json(['data' => $data]);




    }



    public function studentsEnrollmentStatusSchoolWise(Request $request)
    {


        $school_id = $request->school_id;
        $sessionId = $request->sessionId;




        $data = DB::select("SELECT
    COUNT(sai_student_status) AS total_students,
    prog_school_id,
    prog_dept_id,
    school_code,
    dept_name,
    programs.prog_name,
    COUNT(
        CASE
            WHEN sai_student_status = 'Left' THEN
                1
        END
    )                         AS left_students,
    COUNT(
        CASE
            WHEN sai_student_status = 'Defer' THEN
                1
        END
    )                         AS defer_students,
    COUNT(
        CASE
            WHEN sai_student_status = 'Active' THEN
                1
        END
    )                         AS active_students,
    COUNT(
        CASE
            WHEN sai_student_status = 'Stuck-Off' THEN
                1
        END
    )                         AS stuck_off,
    COUNT(
        CASE
            WHEN sai_student_status = 'Not Joined' THEN
                1
        END
    )                         AS not_joined
FROM
    programs
    LEFT JOIN students_apply_info ON students_apply_info.sai_sdpo_prog_id = programs.prog_id
                                     AND students_apply_info.sai_sdpo_sess_id =  $sessionId
    LEFT JOIN schools ON prog_school_id = school_id
    LEFT JOIN departments ON prog_dept_id = dept_id
WHERE
    prog_school_id NOT IN ( 8, 9, 10 )
    and prog_school_id=$school_id
GROUP BY
    programs.prog_name,
    prog_school_id,
    prog_dept_id,
    school_code,
    dept_name,
    programs.prog_id
ORDER BY
    prog_school_id ASC");





        return response()->json(['data' => $data]);







    }


















    public function generateGrandSummaryReport(Request $request)
    {
        $roadmapSession = $request->roadmapSession;


        // 🔹 Step 1: Truncate the table first to remove existing data
        try {
            DB::statement("TRUNCATE TABLE admission_grand_summary_report");
        } catch (\Exception $e) {
            // If truncate fails, try delete as fallback
            DB::statement("DELETE FROM admission_grand_summary_report");
        }

        // 🔹 Step 2: Call Oracle Procedure with enhanced retry logic
        $maxRetries = 5;
        $baseDelay = 3; // seconds

        for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
            try {
                DB::statement("
                BEGIN 
                    sp_admission_grand_summary_rpt(:p_sess_id); 
                END;",
                    ['p_sess_id' => $roadmapSession]
                );
                break; // Success, exit retry loop
            } catch (\Exception $e) {
                if (strpos($e->getMessage(), 'ORA-00054') !== false && $attempt < $maxRetries) {
                    // Resource busy error, wait with exponential backoff
                    $delay = $baseDelay * $attempt; // 3, 6, 9, 12 seconds
                    sleep($delay);
                    continue;
                } else {
                    // Other error or max retries reached
                    throw $e;
                }
            }
        }

        // 🔹 Step 2: Fetch aggregated report data
        $data = DB::select("
        SELECT * 
        FROM (
            SELECT
                agr.school_id AS schoolId,
                agr.dept_id   AS departmentId,
                agr.prog_id   AS programId,

                s.school_code,
                s.school_name,
                d.dept_name,
                p.prog_name,

                agr.registered_on_portal             AS reg_portal,
                agr.total_applied_applicants         AS total_apps,
                agr.incomplete_form_submission       AS form_not_sub,
                agr.fee_verified_not_final_submit    AS form_not_sub_paid,
                agr.completed_form_submission        AS form_sub,
                agr.processing_fee_not_verified      AS proc_fee_not_paid,
                agr.processing_fee_verified          AS proc_fee_paid,
                agr.documents_not_accepted           AS docs_incomplete,
                agr.documents_accepted               AS docs_complete,
                agr.applicants_not_appeared_for_test AS not_appeared_test,
                agr.applicants_appeared_for_test     AS appeared_test,
                agr.students_status_rejected         AS rejected,
                agr.admission_offered                AS offered,
                agr.fee_not_paid                     AS declined,
                agr.fee_paid                         AS accepted,
                agr.students_moved_to_cms            AS reg_assigned,
                agr.left_students                    AS left_students,
                agr.deferred_students                AS deferred_students,
                agr.active_students                  AS active,
                agr.current_partial_full_paid        AS current_partial_full_paid,
                agr.challan_not_exists_but_active    AS challan_not_exists_but_active
            FROM admission_grand_summary_report agr
            LEFT JOIN schools s 
                   ON s.school_id = agr.school_id
            LEFT JOIN departments d 
                   ON d.dept_id = agr.dept_id
                  AND d.dept_school_id = agr.school_id
            LEFT JOIN programs p 
                   ON p.prog_id = agr.prog_id
                  AND p.prog_dept_id = agr.dept_id
                  AND p.prog_school_id = agr.school_id
        ) q
        WHERE NVL(q.total_apps, 0) > 0
    ");
        // Generate PDF with the data
        $pdfContent = $this->generateGrandSummaryPDF($data, $roadmapSession);

        // Get authenticated username for filename
        $username = auth()->user()->name ?? 'user';
        $username = preg_replace('/[^a-zA-Z0-9_-]/', '_', $username); // Sanitize username for filename

        // Return PDF for inline viewing in browser
        return response($pdfContent)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="admission_grand_summary_report_' . $username . '.pdf"');
    }




    private function generateGrandSummaryPDF($data, $roadmapSession)
    {
        // Include FPDF library
        require_once(public_path('fpdf/fpdf.php'));

        // Calculate page width based on table content
        $headers = [
            'Sr No',
            'School',
            'Department',
            'Program',
            'Total Apps',
            'Form Not Sub',
            'Form Not Paid',
            'Form Sub',
            'Fee Not Paid',
            'Fee Paid',
            'Docs Incomplete',
            'Docs Complete',
            'Not Appeared',
            'Appeared Test',
            'Rejected',
            'Offered',
            'Declined',
            'Accepted',
            'Reg Assigned',
            'Left',
            'Defer',
            'Active',
            'Partial/Full Paid',
            'No Challan Active'
        ];

        $baseWidths = [
            12,
            18,
            50,
            60,
            27,
            27,
            27,
            27,
            27,
            27,
            27,
            27,
            27,
            27,
            22,
            22,
            22,
            22,
            22,
            22,
            22,
            22,
            27,
            27
        ];

        $totalTableWidth = array_sum($baseWidths);
        $pageWidth = $totalTableWidth + 20; // Add 20mm margin

        // Create PDF object with auto-calculated width
        $pdf = new \FPDF('L', 'mm', array($pageWidth, 297)); // Auto width based on content

        // Set auto page break with more space utilization
        $pdf->SetAutoPageBreak(true, 15);

        // Enable page numbering
        $pdf->AliasNbPages();

        // Add first page
        $pdf->AddPage();

        // Add header to first page
        $this->addGrandSummaryHeader($pdf, $roadmapSession);

        // Use the already calculated headers and widths from above

        // Create table headers with auto-calculated widths
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->SetFillColor(240, 240, 240);

        foreach ($headers as $index => $header) {
            $width = $baseWidths[$index];
            $isLast = ($index === count($headers) - 1);
            $pdf->Cell($width, 8, $header, '1', $isLast ? 1 : 0, 'C', true);
        }

        // Add data rows
        $pdf->SetFont('Arial', '', 8);
        $schoolSerialNumber = 1;

        // Initialize totals array for all data columns
        $totals = [
            'total_apps' => 0,
            'form_not_sub' => 0,
            'form_not_sub_paid' => 0,
            'form_sub' => 0,
            'proc_fee_not_paid' => 0,
            'proc_fee_paid' => 0,
            'docs_incomplete' => 0,
            'docs_complete' => 0,
            'not_appeared_test' => 0,
            'appeared_test' => 0,
            'rejected' => 0,
            'offered' => 0,
            'declined' => 0,
            'accepted' => 0,
            'reg_assigned' => 0,
            'left_students' => 0,
            'deferred_students' => 0,
            'active' => 0,
            'partial_full_paid' => 0,
            'no_challan_active' => 0
        ];

        // If we have real data, use it, otherwise use dummy data
        if (!empty($data)) {
            // Group data by school, then department, then program
            $groupedData = [];
            foreach ($data as $row) {
                $schoolCode = $row->school_code ?? 'N/A';
                $deptName = $row->dept_name ?? 'N/A';
                $progName = $row->prog_name ?? 'N/A';

                if (!isset($groupedData[$schoolCode])) {
                    $groupedData[$schoolCode] = [];
                }
                if (!isset($groupedData[$schoolCode][$deptName])) {
                    $groupedData[$schoolCode][$deptName] = [];
                }
                $groupedData[$schoolCode][$deptName][] = $row;
            }

            // Generate grouped table with proper merging
            foreach ($groupedData as $schoolCode => $departments) {
                $schoolRowCount = 0;
                foreach ($departments as $deptName => $programs) {
                    $schoolRowCount += count($programs);
                }

                $isFirstSchoolRow = true;

                foreach ($departments as $deptName => $programs) {
                    $deptRowCount = count($programs);
                    $isFirstDeptRow = true;

                    foreach ($programs as $index => $row) {
                        // Check if we need a new page (optimized for maximum space usage)
                        if ($pdf->GetY() > 270) {
                            $pdf->AddPage();
                            $this->addGrandSummaryHeader($pdf, $roadmapSession);

                            // Re-add table headers with auto widths
                            $pdf->SetFont('Arial', 'B', 9);
                            $pdf->SetFillColor(240, 240, 240);

                            foreach ($headers as $index => $header) {
                                $width = $baseWidths[$index];
                                $isLast = ($index === count($headers) - 1);
                                $pdf->Cell($width, 8, $header, '1', $isLast ? 1 : 0, 'C', true);
                            }
                            $pdf->SetFont('Arial', '', 8);
                        }

                        // Serial Number - only on first row of each school
                        if ($isFirstSchoolRow) {
                            $pdf->Cell($baseWidths[0], 6 * $schoolRowCount, $schoolSerialNumber, '1', 0, 'C');
                        } else {
                            // Skip serial number column for subsequent rows
                            $pdf->SetX($pdf->GetX() + $baseWidths[0]);
                        }

                        // School Code - merged cell only on first row of school
                        if ($isFirstSchoolRow) {
                            // Draw merged cell with proper borders
                            $schoolText = substr($schoolCode, 0, 15);
                            $pdf->Cell($baseWidths[1], 6 * $schoolRowCount, $schoolText, '1', 0, 'C');
                        } else {
                            // Skip school column for subsequent rows to avoid internal borders
                            $pdf->SetX($pdf->GetX() + $baseWidths[1]);
                        }

                        // Department - merged cell only on first row of department
                        if ($isFirstDeptRow) {
                            // Draw merged cell with proper borders
                            $deptText = substr($deptName, 0, 30);
                            $pdf->Cell($baseWidths[2], 6 * $deptRowCount, $deptText, '1', 0, 'C');
                        } else {
                            // Skip department column for subsequent rows to avoid internal borders
                            $pdf->SetX($pdf->GetX() + $baseWidths[2]);
                        }

                        // Program - always unique (no merging)
                        $progText = substr($row->prog_name ?? 'N/A', 0, 35);
                        $pdf->Cell($baseWidths[3], 6, $progText, '1', 0, 'L');

                        // All data columns in correct order using auto widths
                        $dataFields = [
                            $row->total_apps ?? '0',
                            $row->form_not_sub ?? '0',
                            $row->form_not_sub_paid ?? '0',
                            $row->form_sub ?? '0',
                            $row->proc_fee_not_paid ?? '0',
                            $row->proc_fee_paid ?? '0',
                            $row->docs_incomplete ?? '0',
                            $row->docs_complete ?? '0',
                            $row->not_appeared_test ?? '0',
                            $row->appeared_test ?? '0',
                            $row->rejected ?? '0',
                            $row->offered ?? '0',
                            $row->declined ?? '0',
                            $row->accepted ?? '0',
                            $row->reg_assigned ?? '0',
                            $row->left_students ?? '0',
                            $row->deferred_students ?? '0',
                            $row->active ?? '0',
                            $row->current_partial_full_paid ?? '0',
                            $row->challan_not_exists_but_active ?? '0'
                        ];

                        // Add to totals
                        $totals['total_apps'] += (int) ($row->total_apps ?? 0);
                        $totals['form_not_sub'] += (int) ($row->form_not_sub ?? 0);
                        $totals['form_not_sub_paid'] += (int) ($row->form_not_sub_paid ?? 0);
                        $totals['form_sub'] += (int) ($row->form_sub ?? 0);
                        $totals['proc_fee_not_paid'] += (int) ($row->proc_fee_not_paid ?? 0);
                        $totals['proc_fee_paid'] += (int) ($row->proc_fee_paid ?? 0);
                        $totals['docs_incomplete'] += (int) ($row->docs_incomplete ?? 0);
                        $totals['docs_complete'] += (int) ($row->docs_complete ?? 0);
                        $totals['not_appeared_test'] += (int) ($row->not_appeared_test ?? 0);
                        $totals['appeared_test'] += (int) ($row->appeared_test ?? 0);
                        $totals['rejected'] += (int) ($row->rejected ?? 0);
                        $totals['offered'] += (int) ($row->offered ?? 0);
                        $totals['declined'] += (int) ($row->declined ?? 0);
                        $totals['accepted'] += (int) ($row->accepted ?? 0);
                        $totals['reg_assigned'] += (int) ($row->reg_assigned ?? 0);
                        $totals['left_students'] += (int) ($row->left_students ?? 0);
                        $totals['deferred_students'] += (int) ($row->deferred_students ?? 0);
                        $totals['active'] += (int) ($row->active ?? 0);
                        $totals['partial_full_paid'] += (int) ($row->current_partial_full_paid ?? 0);
                        $totals['no_challan_active'] += (int) ($row->challan_not_exists_but_active ?? 0);

                        foreach ($dataFields as $index => $value) {
                            $widthIndex = $index + 4; // Start from index 4 (after Sr No, School, Department, Program)
                            $isLast = ($index === count($dataFields) - 1);
                            $pdf->Cell($baseWidths[$widthIndex], 6, $value, '1', $isLast ? 1 : 0, 'C');
                        }

                        $isFirstSchoolRow = false;
                        $isFirstDeptRow = false;
                    }
                }

                // Increment serial number for next school
                $schoolSerialNumber++;
            }

            // Add totals row
            $pdf->SetFont('Arial', 'B', 8);
            $pdf->SetFillColor(240, 240, 240);

            // Check if we need a new page for totals
            if ($pdf->GetY() > 270) {
                $pdf->AddPage();
                $this->addGrandSummaryHeader($pdf, $roadmapSession);

                // Re-add table headers
                $pdf->SetFont('Arial', 'B', 9);
                $pdf->SetFillColor(240, 240, 240);

                foreach ($headers as $index => $header) {
                    $width = $baseWidths[$index];
                    $isLast = ($index === count($headers) - 1);
                    $pdf->Cell($width, 8, $header, '1', $isLast ? 1 : 0, 'C', true);
                }
                $pdf->SetFont('Arial', 'B', 8);
            }

            // Totals row
            $pdf->Cell($baseWidths[0], 8, '', '1', 0, 'C', true); // Empty Sr No
            $pdf->Cell($baseWidths[1], 8, '', '1', 0, 'C', true); // Empty School
            $pdf->Cell($baseWidths[2], 8, '', '1', 0, 'C', true); // Empty Department
            $pdf->Cell($baseWidths[3], 8, 'TOTAL', '1', 0, 'C', true); // Program column with TOTAL

            // Total values for each data column
            $totalFields = [
                $totals['total_apps'],
                $totals['form_not_sub'],
                $totals['form_not_sub_paid'],
                $totals['form_sub'],
                $totals['proc_fee_not_paid'],
                $totals['proc_fee_paid'],
                $totals['docs_incomplete'],
                $totals['docs_complete'],
                $totals['not_appeared_test'],
                $totals['appeared_test'],
                $totals['rejected'],
                $totals['offered'],
                $totals['declined'],
                $totals['accepted'],
                $totals['reg_assigned'],
                $totals['left_students'],
                $totals['deferred_students'],
                $totals['active'],
                $totals['partial_full_paid'],
                $totals['no_challan_active']
            ];

            foreach ($totalFields as $index => $value) {
                $widthIndex = $index + 4; // Start from index 4 (after Sr No, School, Department, Program)
                $isLast = ($index === count($totalFields) - 1);
                $pdf->Cell($baseWidths[$widthIndex], 8, $value, '1', $isLast ? 1 : 0, 'C', true);
            }

        } else {
            // No data found - show professional message
            $pdf->SetFont('Arial', 'I', 12);
            $pdf->SetTextColor(128, 128, 128);
            $pdf->Cell(0, 20, 'No Records Found', 0, 1, 'C');
            $pdf->SetTextColor(0, 0, 0);
        }

        // Output PDF as string
        return $pdf->Output('', 'S');
    }









    private function addGrandSummaryHeader($pdf, $roadmapSession)
    {
        // BNU Logo (compact)
        $pdf->Image(public_path('fpdf/logoimages/bnulogo1.jpg'), 15, 3, 12, 15);

        // Letter Head (compact)
        $pdf->SetXY(30, 5);
        $pdf->SetFont('times', 'B', 14);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(0, 0, 'Beaconhouse National University', 0, 0, 'L');

        $pdf->SetXY(30, 11);
        $pdf->SetFont('times', '', 10);
        $pdf->Cell(0, 0, '13-km Raiwind Road, Lahore 54400, Pakistan', 0, 0, 'L');

        $pdf->SetXY(30, 15);
        $pdf->Cell(0, 3, 'Tel: 92-42-38100156 Email: info@bnu.edu.pk', 0, 1, 'L');

        // Report Title (compact)
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(0, 8, 'Grand Summary Report - Admission', 0, 1, 'C');

        // Set position below header (reduced from 50 to 35)
        $pdf->SetY(35);
    }










}
