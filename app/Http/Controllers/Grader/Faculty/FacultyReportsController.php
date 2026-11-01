<?php
/**
 * Created by PhpStorm.
 * User: Maher Asad
 * Date: 5/14/2019
 * Time: 9:52 AM
 */
namespace App\Http\Controllers\Grader\Faculty;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\Console\Helper\Table;
use DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
use App\Models\Grader\Schools;
use App\Models\Grader\Departments;
use App\Models\Grader\FacultyCourses;
use App\Models\Grader\EmpEmployees;
use Carbon\Carbon;

class FacultyReportsController extends Controller
{

    function reportListing()
    {
        // dd(11111);
        $school_id = auth()->user()->school_id;
        $group_id = auth()->user()->group_id;

        // Array 1: Users who can access this page
        $authorizedUsers = ['noreen', 'ijaz.hussain', 'bushra.rahman', 'shafaat.ahmed', 'naseer.ahmed', 'muddassar.javed', 'anam.amin', 'tahir.kamran', 'kiran.khan', 'amra.raza'];

        // Array 2: Users who can see ALL schools (subset of authorized users)
        $superAdminUsers = ['noreen', 'ijaz.hussain', 'bushra.rahman', 'shafaat.ahmed', 'naseer.ahmed', 'muddassar.javed', 'anam.amin', 'tahir.kamran', 'kiran.khan'];

        // Array 3: Users who can see ALL sessions (noreen, muddassar.javed, anam.amin)
        // $allSessionUsers = ['noreen', 'muddassar.javed', 'anam.amin'];

        // Check if user is authorized to access this page
        if (in_array(Auth::user()->username, $authorizedUsers) || $group_id == 644) {
            // Get all sessions first
            $allSessions = \App::call('App\Http\Controllers\Grader\AcademicSessionController@retrieve');

            // Filter sessions based on user
            // if (in_array(Auth::user()->username, $allSessionUsers) || $group_id == 644) {
            // These users get ALL sessions (including Software Team - group_id 644)
            $sessions = $allSessions;
            // } else {
            //     // Other authorized users get only top 2 latest sessions (excluding summer)
            //     $sessions = [];
            //     $count = 0;

            //     foreach ($allSessions as $key => $value) {
            //         // Skip summer sessions
            //         if (strpos(strtolower($value), 'summer') !== false) {
            //             continue;
            //         }

            //         $sessions[$key] = $value;
            //         $count++;

            //         // Only take top 2
            //         if ($count >= 2) {
            //             break;
            //         }
            //     }
            // }

            // Check if user can see all schools or just their own
            if (in_array(Auth::user()->username, $superAdminUsers) || $group_id == 644) {
                // Super admin users and Software Team - can see all schools
                $sql = "select * from schools where school_status = 'E'";
            } else {
                // Regular authorized users - can only see their own school
                $sql = "select * from schools where school_id = $school_id";
            }

            $schools = DB::select($sql);
            $departments = array(); // $this->school->fetch();
            $facultyCourses = \App::call('App\Http\Controllers\Grader\FacultyCoursesController@retrieve');
            $offeredFaculty = \App::call('App\Http\Controllers\Grader\CoursesOfferedController@retrieveFaculty');
            return view('grader.reports.faculty.faculty_reports_list', compact('sessions', 'schools', 'departments', 'facultyCourses', 'offeredFaculty'));
        } else {
            abort(403, 'Unauthorized | You are not allowed');
        }
    }

    function getDepartment(Request $request)
    {
        $where[] = ['dept_id', '>', '0'];
        $where[] = ['dept_status', '=', 'E'];
        if ($request->get('school_id') != '') {
            $where[] = ['dept_school_id', '=', $request->get('school_id')];
        }
        $departments = DB::table('departments')->where($where)->orderBy('dept_id', 'asc')->get();
        return response()->json(['data' => $departments, 'status' => true, 'message' => count($departments) . ' record(s) found!']);
    }

    function faculty_course_detail()
    {

        $sessions = \App::call('App\Http\Controllers\Grader\AcademicSessionController@retrieve');
        $schools = app('App\Http\Controllers\Grader\StaffRightsController')->retrive_faculty_schools(Auth::user()->email);
        //        return $schools;
        $departments = array(); // $this->school->fetch();
        $facultyCourses = \App::call('App\Http\Controllers\Grader\FacultyCoursesController@retrieve');
        $offeredFaculty = \App::call('App\Http\Controllers\Grader\CoursesOfferedController@retrieveFaculty');
        return view('grader.reports.faculty.faculty_course_detail', compact('sessions', 'schools', 'departments', 'facultyCourses', 'offeredFaculty'));



    }

    function nbs_report()
    {

        $sessions = \App::call('App\Http\Controllers\Grader\AcademicSessionController@retrieve');
        $schools = app('App\Http\Controllers\Grader\StaffRightsController')->retrive_faculty_schools(Auth::user()->email);
        //        return $schools;
        $departments = array(); // $this->school->fetch();
        $facultyCourses = \App::call('App\Http\Controllers\Grader\FacultyCoursesController@retrieve');
        $offeredFaculty = \App::call('App\Http\Controllers\Grader\CoursesOfferedController@retrieveFaculty');
        return view('grader.reports.faculty.nbs_report', compact('sessions', 'schools', 'departments', 'facultyCourses', 'offeredFaculty'));



    }

    public function saveScholarshipInput(Request $request)
    {
        $validated = $request->validate([
            'applicant_id' => 'required|numeric',
            'percentage' => 'required|string|max:55',
            'remarks' => 'nullable|string|max:600',
        ]);

        $applicantId = $validated['applicant_id'];
        $username = auth()->user()->username;

        // Check if this user has already entered data for this applicant
        $existing = DB::table('scholarship_percentage_details')
            ->where('aai_id', $applicantId)
            ->where('entry_username', $username)
            ->first();

        if ($existing) {
            // Update the existing record for this user and applicant
            DB::table('scholarship_percentage_details')
                ->where('id', $existing->id)
                ->update([
                    'percentage' => $validated['percentage'],
                    'remarks' => $validated['remarks'],
                    'sess_id' => $request->get('sessionId'),
                    'updated_at' => Carbon::now(),
                ]);
        } else {
            // Insert a new record for this user and applicant
            DB::table('scholarship_percentage_details')->insert([
                'aai_id' => $applicantId,
                'percentage' => $validated['percentage'],
                'remarks' => $validated['remarks'],
                'sess_id' => $request->get('sessionId'),
                'entry_username' => $username,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Data saved successfully.']);
    }



    public function schCriteriaReport(Request $request)
    {
        // Collect request input safely
        $ad_sess_id = $request->input('ad_sess_id');
        $ad_school_id = $request->input('ad_school_id');
        $admission_sch = $request->input('admission_sch');
        $scholarship_type = $request->input('scholarship_type');
        $scholarship_filter_type = $request->input('scholarship_filter_type');
        $selected_filter_type = $request->input('selected_filter_type');
        $status_filter_type = $request->input('status_filter_type');

        $sqladmissiondata = "
            WITH scholershipStudent AS (
                SELECT DISTINCT
                    api_id,
                    admission_apply_info.aai_id,
                    api_name,
                    api_email,
                    api_mobile,
                    school_code,
                    api_transgenderbase,
                    api_sportsbase,
                    aai_pf_status,
                    aai_qualification_status,
                    api_needbase_scholarship,
                    api_bnuinstitutionalpatronage,
                    api_vcscholarship,
                    api_deansbase,
                    aai_test_totalmarks,
                    aai_test_marks,
                    aai_interview_totalmarks,
                    aai_interview_marks,
                    aai_programid,
                    remarks,
                    percentage,
                    ROUND(((aai_test_marks + aai_interview_marks) /
                        CASE WHEN (aai_test_totalmarks + aai_interview_totalmarks) = 0 THEN 1 ELSE (aai_test_totalmarks + aai_interview_totalmarks) END) * 100 * 0.30, 2) AS interview_test_percentage,

                    (
                        SELECT DISTINCT aq_obtained_marks
                        FROM admission_qualification
                        WHERE aq_api_id = api_id AND aq_degree_name = 'Matric'
                    ) AS matric_obtained_marks,

                    (
                        SELECT DISTINCT aq_degree_institute
                        FROM admission_qualification
                        WHERE aq_api_id = api_id AND aq_degree_name IN ('Matric') AND ROWNUM = 1
                    ) AS last_institute_matric,

                    (
                        SELECT DISTINCT aq_degree_institute
                        FROM admission_qualification
                        WHERE aq_api_id = api_id AND aq_degree_name IN ('A_Level', 'Intermediate') AND ROWNUM = 1
                    ) AS last_institute,

                    (
                        SELECT DISTINCT aq_total_marks
                        FROM admission_qualification
                        WHERE aq_api_id = api_id AND aq_degree_name = 'Matric'
                    ) AS matric_total_marks,

                    (
                        SELECT DISTINCT ROUND((aq_obtained_marks / CASE WHEN aq_total_marks = 0 THEN 1 ELSE aq_total_marks END) * 100, 2)
                        FROM admission_qualification
                        WHERE aq_api_id = api_id AND aq_degree_name = 'Matric'
                    ) AS matric_percentage,

                    (
                        SELECT DISTINCT ROUND((aq_obtained_marks / CASE WHEN aq_total_marks = 0 THEN 1 ELSE aq_total_marks END) * 100 * 0.35, 2)
                        FROM admission_qualification
                        WHERE aq_api_id = api_id AND aq_degree_name = 'Matric'
                    ) AS matric_weightage,

                    (
                        SELECT DISTINCT aq_obtained_marks
                        FROM admission_qualification
                        WHERE aq_api_id = api_id AND aq_degree_name = 'Intermediate'
                    ) AS intermediate_obtained_marks,

                    (
                        SELECT DISTINCT aq_total_marks
                        FROM admission_qualification
                        WHERE aq_api_id = api_id AND aq_degree_name = 'Intermediate'
                    ) AS intermediate_total_marks,

                    (
                        SELECT DISTINCT ROUND((aq_obtained_marks / CASE WHEN aq_total_marks = 0 THEN 1 ELSE aq_total_marks END) * 100, 2)
                        FROM admission_qualification
                        WHERE aq_api_id = api_id AND aq_degree_name = 'Intermediate'
                    ) AS intermediate_percentage,

                    (
                        SELECT DISTINCT ROUND((aq_obtained_marks / CASE WHEN aq_total_marks = 0 THEN 1 ELSE aq_total_marks END) * 100 * 0.35, 2)
                        FROM admission_qualification
                        WHERE aq_api_id = api_id AND aq_degree_name = 'Intermediate'
                    ) AS intermediate_weightage,

                    (
                        SELECT DISTINCT adq_equal_obtain_marks
                        FROM admission_qualification_detail
                        WHERE aqd_api_id = api_id AND aqd_degree_name = 'O_Level'
                    ) AS O_level_obtained_marks,

                    (
                        SELECT DISTINCT adq_equal_total_marks
                        FROM admission_qualification_detail
                        WHERE aqd_api_id = api_id AND aqd_degree_name = 'O_Level'
                    ) AS O_level_total_marks,

                    (
                        SELECT DISTINCT ROUND((adq_equal_obtain_marks / CASE WHEN adq_equal_total_marks = 0 THEN 1 ELSE adq_equal_total_marks END) * 100, 2)
                        FROM admission_qualification_detail
                        WHERE aqd_api_id = api_id AND aqd_degree_name = 'O_Level'
                    ) AS O_level_percentage,

                    (
                        SELECT DISTINCT ROUND((adq_equal_obtain_marks / CASE WHEN adq_equal_total_marks = 0 THEN 1 ELSE adq_equal_total_marks END) * 100 * 0.35, 2)
                        FROM admission_qualification_detail
                        WHERE aqd_api_id = api_id AND aqd_degree_name = 'O_Level'
                    ) AS O_level_weightage,

                    (
                        SELECT DISTINCT adq_equal_obtain_marks
                        FROM admission_qualification_detail
                        WHERE aqd_api_id = api_id AND aqd_degree_name = 'A_Level'
                    ) AS A_level_obtained_marks,

                    (
                        SELECT DISTINCT adq_equal_total_marks
                        FROM admission_qualification_detail
                        WHERE aqd_api_id = api_id AND aqd_degree_name = 'A_Level'
                    ) AS A_level_total_marks,

                    (
                        SELECT DISTINCT ROUND((adq_equal_obtain_marks / CASE WHEN adq_equal_total_marks = 0 THEN 1 ELSE adq_equal_total_marks END) * 100, 2)
                        FROM admission_qualification_detail
                        WHERE aqd_api_id = api_id AND aqd_degree_name = 'A_Level'
                    ) AS A_level_percentage,

                    (
                        SELECT DISTINCT ROUND((adq_equal_obtain_marks / CASE WHEN adq_equal_total_marks = 0 THEN 1 ELSE adq_equal_total_marks END) * 100 * 0.35, 2)
                        FROM admission_qualification_detail
                        WHERE aqd_api_id = api_id AND aqd_degree_name = 'A_Level'
                    ) AS A_level_weightage,

                    CASE 
                        WHEN admission_sch_round = 1 THEN 'Early Admissions'
                        WHEN admission_sch_round = 2 THEN 'Regular Admissions'
                        ELSE 'Rolling Admissions' 
                    END AS admission_category

                FROM admission_personal_info
                LEFT JOIN admission_apply_info ON admission_personal_info.api_id = admission_apply_info.aai_api_id
                LEFT JOIN admission_schedules ON admission_apply_info.aai_admission_sch_id = admission_schedules.admission_sch_id
                LEFT JOIN schools ON admission_apply_info.aai_sdpo_school_id = schools.school_id
                left join scholarship_percentage_details on scholarship_percentage_details.aai_id = admission_apply_info.aai_id
                AND entry_username = '" . auth()->user()->username . "'
                WHERE (
                    api_transgenderbase = 'Yes'
                    OR api_deansbase = 'Yes'
                    OR api_sportsbase = 'Yes'
                    OR api_bnuinstitutionalpatronage = 'Yes'
                    OR api_needbase_scholarship = 'Yes'
                    OR api_vcscholarship = 'Yes'
                )
        ";

        // Filters
        if (!empty($ad_sess_id)) {
            $sqladmissiondata .= " AND aai_sdpo_sess_id = $ad_sess_id";
        }

        if (!empty($ad_school_id)) {
            $sqladmissiondata .= " AND aai_sdpo_school_id = $ad_school_id";
        }

        if (!empty($admission_sch)) {
            $sqladmissiondata .= " AND admission_sch_round = $admission_sch";
        }

        if (!empty($selected_filter_type) && $selected_filter_type == 'Yes') {
            $sqladmissiondata .= " AND aai_status = 'Selected'";
        }

        $sqladmissiondata .= ") 
            SELECT scholershipStudent.*,
            COALESCE(interview_test_percentage, 0) + 
            COALESCE(matric_weightage, 0) + 
            COALESCE(intermediate_weightage, 0) + 
            COALESCE(O_level_weightage, 0) + 
            COALESCE(A_level_weightage, 0) AS total_qualification_score
            FROM scholershipStudent 
            WHERE 1 = 1";

        if (!empty($scholarship_type)) {
            if ($scholarship_filter_type === 'Yes') {
                $sqladmissiondata .= " AND $scholarship_type = 'Yes'";
            } elseif ($scholarship_filter_type === 'No') {
                $sqladmissiondata .= " AND $scholarship_type = 'No'";
            } else {
                $sqladmissiondata .= " AND ($scholarship_type = 'Yes' OR $scholarship_type = 'No')";
            }
        }

        if (!empty($status_filter_type)) {
            if ($status_filter_type == "Verified Students") {
                $sqladmissiondata .= " AND aai_qualification_status = 'Accepted' AND aai_pf_status = 'Fee Verified'";
            } elseif ($status_filter_type == "Non Verified Students") {
                $sqladmissiondata .= " AND (aai_pf_status NOT IN ('Fee Verified'))";
            }
        }

        // Execute query
        $data = DB::select($sqladmissiondata);

        return response()->json([
            'status' => true,
            'data' => $data
        ]);
    }

    function academic_history()
    {

        $sessions = \App::call('App\Http\Controllers\Grader\AcademicSessionController@retrieve');
        $schools = app('App\Http\Controllers\Grader\StaffRightsController')->retrive_faculty_schools(Auth::user()->email);
        //        return $schools;
        $departments = array(); // $this->school->fetch();
        $facultyCourses = \App::call('App\Http\Controllers\Grader\FacultyCoursesController@retrieve');
        $offeredFaculty = \App::call('App\Http\Controllers\Grader\CoursesOfferedController@retrieveFaculty');
        return view('grader.reports.faculty.academic_history2', compact('sessions', 'schools', 'departments', 'facultyCourses', 'offeredFaculty'));



    }

    function getProgram(Request $request)
    {
        $where[] = ['prog_id', '>', '0'];
        if ($request->get('dept_id') != '') {
            $where[] = ['prog_dept_id', '=', $request->get('dept_id')];
        }
        $programs = DB::table('programs')->where($where)->orderBy('prog_name', 'asc')->get();
        return response()->json(['data' => $programs, 'status' => true, 'message' => count($programs) . ' record(s) found!']);
    }


    function getTeacher(Request $request)
    {

        $name = strtolower($request->get('query'));
        $depID = strtolower($request->get('depID'));
        $schoolID = strtolower($request->get('school_id'));


        $sql = "SELECT employee_id AS id, (emp_f_name || ' ' || emp_l_name) AS name
        FROM emp_employees
        WHERE 1 = 1
        AND employee_id IN (
            SELECT fc_employee_id 
            FROM faculty_courses 
            WHERE fc_co_sess_id = '" . strtolower($request->get('session_sel')) . "'";

        if ($schoolID != '') {
            $sql .= " AND fc_co_school_id = " . intval($schoolID);
        }

        if ($depID != '') {
            $sql .= " AND fc_co_dept_id = " . intval($depID);
        }

        $sql .= ")";


        //  echo $sql;exit();

        //  if($depID != ''){
        //      $sql        .= "  and fc_co_dept_id =".$depID;
        //  }



        //if($name != ''){
        //     $sql        .= " AND lower(emp_f_name||' '||emp_l_name)  like '%".$name."%'";
        // }
        // if($depID != ''){
        //     $sql        .= " AND department_id = ".$depID;
        // }
        //echo $sql; exit();


        $rec_ora = DB::select($sql);
        return response()->json(['data' => $rec_ora, 'status' => true, 'message' => count($rec_ora) . ' record(s) found!']);
    }

    // function getTeacherByDepID(Request $request){
    //     $id         = strtolower($request->get('depID'));
    //     $sql        = "select employee_id as id, (emp_f_name||' '||emp_l_name) as name from emp_employees where department_id  = ".$id;
    //     $rec_ora    = DB::select($sql);
    //     return response()->json(['data' => $rec_ora, 'status'=> true, 'message' => count($rec_ora).' record(s) found!']);
    // }

    function faculty_evaluation()
    {
        $ora = "SELECT
        get_teachername(fc_co_id) AS teacher_name,
        get_course_name(fc_co_id) AS course_name
        fc_co_id,
        quality_of_teaching,
        course_evaluation,
        exam_and_assessment,
        overall_rating,
        fc_employee_id,
        courses_offered.co_course_cr,
        (
            SELECT
                COUNT(*)
            FROM
                student_courses
            WHERE
                sc_co_id = fc_co_id
        ) AS total_students,
        ( (
            SELECT
                COUNT(*)
            FROM
                student_courses
            WHERE
                sc_co_id = fc_co_id
        ) ) AS total_evaluators,
        quality_of_teaching AS student_attendance
    FROM
        faculty_courses
        LEFT JOIN courses_offered ON fc_co_id = co_id
    WHERE
            1 = 1
        --AND fc_employee_id = 537
        AND
            fc_co_sess_id = 148
        AND
            fc_co_school_id = 11
    ORDER BY fc_employee_id";
        //    echo $ora;exit();
        $rec_ora = DB::select($ora);
        return view('grader.reports.faculty_evaluation', ['teacher_evaluation' => $rec_ora]);
    }
    // resources\views\grader\reports\faculty\faculty_evaluation.blade.php


    function getResults(Request $request)
    {


        if ($request->report_type == 'old') {
            return $this->getOldResults($request);
        } else if ($request->report_type == 'f2025onward') {
            return $this->getFromF2025Results($request);
        } else if ($request->report_type == 'new') {
            return $this->getOldResults($request);

        }
    }

    function getOldResults(Request $request)
    {
        $whereStr = ' 1 = 1 ';
        if ($request->get('session') != '') {
            $whereStr .= ' AND fc_co_sess_id = ' . $request->get('session');
        }
        if ($request->get('school') != '') {
            $whereStr .= ' AND fc_co_school_id = ' . $request->get('school');
        }
        if ($request->get('department') != '') {
            $whereStr .= ' AND fc_co_dept_id = ' . $request->get('department');
        }
        if ($request->get('tid') != '') {
            $whereStr .= ' AND fc_employee_id = ' . $request->get('tid');
        }
        if ($request->get('teacher') != '') {
            $whereStr .= ' AND fc_employee_id = ' . $request->get('teacher');
        }
        if ($request->get('course') != '') {
            $whereStr .= ' AND co_id = ' . $request->get('course');
        }
        $whereStr .= ' and COMBINE_COURSE is null  ';
        $whereStr .= "and co_offering_type  in ('1') and co_custom=1 and co_status='E' ";

        $ora = "SELECT
            sess_name,
            school_code,
            school_name,
            dept_name,
            fc_co_id,
            teacher_name,
            course_code,
            course_cd_name,
            course_name,
            total_students,
            feedback_submitted,

    (
        SELECT
            COUNT(DISTINCT fe.sai_enrollment_no)
        FROM
            faculty_evaluation fe
        LEFT JOIN evaluations e ON fe.evaluation_id = e.id
        WHERE
            fe.fe_co_id = fc_co_id
            AND e.faculty_feedback_type = 'old'
    ) AS old_feedback_submitted,

     (
        SELECT
            COUNT(DISTINCT fe.sai_enrollment_no)
        FROM
            faculty_evaluation fe
        LEFT JOIN evaluations e ON fe.evaluation_id = e.id
        WHERE
            fe.fe_co_id = fc_co_id
            AND fe.evaluation_id != 32
            AND ( e.faculty_feedback_type != 'old')
            
             
        ) AS new_feedback_submitted,

            co_course_cr,
            quality_of_teaching,
            course_evaluation,
            exam_and_assessment,
            overall_rating,
            CO_ATTENDANCE_PERCENTAGE,
            '125' AS total,
            '100' AS want_course_again
        FROM
            (
                SELECT
                    sess_name,
                    school_code,
                    school_name,
                    dept_name,
                    course_code,
                    course_cd_name,
                    prefix
                     || ''
                     || emp_f_name
                     || ' '
                     || emp_l_name AS teacher_name,
                    get_course_name(fc_co_id) AS course_name,
                    fc_co_id,
                    courses_offered.co_course_cr,
                    (
                        SELECT
                            COUNT(DISTINCT sai_enrollment_no)
                        FROM
                            faculty_evaluation
                        WHERE
                            fe_co_id = fc_co_id
                    ) AS feedback_submitted,
                    get_faculty_evaluation(co_id,'A',30) AS quality_of_teaching,
                    get_faculty_evaluation(co_id,'B',25) AS course_evaluation,
                    get_faculty_evaluation(co_id,'D',10) AS exam_and_assessment,
                    round(
                        ( ( (get_faculty_evaluation(co_id,'A',30) + get_faculty_evaluation(co_id,'B',25) + get_faculty_evaluation(co_id,'D',10) ) / 3) ),
                        2
                    ) AS overall_rating,
                    (
                        SELECT
                            COUNT(*)
                        FROM
                            course_attendance
                        WHERE
                                course_attendance.co_id IN (
                                    SELECT
                                        courses_offered.co_id
                                    FROM
                                        courses_offered
                                    WHERE
                                            courses_offered.co_id = fc_co_id
                                        OR
                                            courses_offered.parent_co_id = fc_co_id
                                )
                            AND (
                                course_attendance.ca_attendance = 'A'
                            )
                    ) AS ta,
                    (
                        SELECT
                            COUNT(*)
                        FROM
                            course_attendance
                        WHERE
                                course_attendance.co_id IN (
                                    SELECT
                                        courses_offered.co_id
                                    FROM
                                        courses_offered
                                    WHERE
                                            courses_offered.co_id = fc_co_id
                                        OR
                                            courses_offered.parent_co_id = fc_co_id
                                )
                            AND (
                                    course_attendance.ca_attendance = 'P'
                                OR
                                    course_attendance.ca_hod_attendance = 'EX'
                            )
                    ) AS tp,
                    (
                        SELECT
                            COUNT(*)
                        FROM
                            student_courses
                        WHERE
                            sc_co_id IN (
                                SELECT
                                    courses_offered.co_id
                                FROM
                                    courses_offered
                                WHERE
                                    (
                                            courses_offered.co_id = fc_co_id
                                        OR
                                            courses_offered.parent_co_id = fc_co_id
                                    ) AND
                                        sc_status = 'A'
                            )
                    )AS total_students,
                    CO_ATTENDANCE_PERCENTAGE,
                'N/A' AS CO_ATTENDANCE_PERCENTAGE_DATE

                FROM
                    faculty_courses
                    LEFT JOIN courses_offered ON fc_co_id = co_id
                    LEFT JOIN emp_employees ON employee_id = faculty_courses.fc_employee_id
                    LEFT JOIN academic_sessions ON co_sess_id = sess_id
                    LEFT JOIN schools ON co_school_id = school_id
                    LEFT JOIN departments ON co_dept_id = departments.dept_id
                    LEFT JOIN courses_two ON co_course_id = course_id
                WHERE
                        fc_status = 'E' and $whereStr
            )
            order by teacher_name";

        //  echo $ora;exit;
        $rec_ora = DB::select($ora);

        if ($request->get('result-for') == '') {
            return response()->json(['data' => $rec_ora, 'status' => true, 'message' => count($rec_ora) . ' record(s) found!']);
        }

        return view('grader.reports.faculty.faculty_evaluation', ['teacher_evaluation' => $rec_ora]);
    }


    function getNewResults(Request $request)
    {

    }

    function getFromF2025Results(Request $request)
    {

        // dd(111);
        $whereStr = ' 1 = 1 ';
        if ($request->get('session') != '') {
            $whereStr .= ' AND fc_co_sess_id = ' . $request->get('session');
        }
        if ($request->get('school') != '') {
            $whereStr .= ' AND fc_co_school_id = ' . $request->get('school');
        }
        if ($request->get('department') != '') {
            $whereStr .= ' AND fc_co_dept_id = ' . $request->get('department');
        }
        if ($request->get('tid') != '') {
            $whereStr .= ' AND fc_employee_id = ' . $request->get('tid');
        }
        if ($request->get('teacher') != '') {
            $whereStr .= ' AND fc_employee_id = ' . $request->get('teacher');
        }
        if ($request->get('course') != '') {
            $whereStr .= ' AND co_id = ' . $request->get('course');
        }
        // $whereStr .= ' and COMBINE_COURSE is null  ';
        $whereStr .= "
       
         and co_status='E' ";

        // =======================================================================
        // EXCLUDE TEACHING ASSISTANTS (TAs)
        // =======================================================================
        $whereStr .= $this->getTaExclusionConditions();

        $ora = "SELECT
            sess_name,
            school_code,
            school_name,
            dept_name,
            fc_co_id,
            calculate_faculty_evaluation_average(fc_co_id, 'overall') as overall_summary,
            calculate_faculty_evaluation_average(fc_co_id, 'core') as core_summary,
            calculate_faculty_evaluation_average(fc_co_id, 'support') as support_summary,
            teacher_name,
            course_code,
            course_cd_name,
            course_name,
            total_students,
            feedback_submitted,

    (
        SELECT
            COUNT(DISTINCT fe.sai_enrollment_no)
        FROM
            faculty_evaluation fe
        LEFT JOIN evaluations e ON fe.evaluation_id = e.id
        WHERE
            fe.fe_co_id = fc_co_id
            AND e.faculty_feedback_type = 'old'
    ) AS old_feedback_submitted,

     (
        SELECT
            COUNT(DISTINCT fe.sai_enrollment_no)
        FROM
            faculty_evaluation fe
        LEFT JOIN evaluations e ON fe.evaluation_id = e.id
        WHERE
            fe.fe_co_id = fc_co_id
            AND fe.evaluation_id != 32
            AND ( e.faculty_feedback_type != 'old')
            
             
        ) AS new_feedback_submitted,

            co_course_cr,
            quality_of_teaching,
            course_evaluation,
            exam_and_assessment,
            overall_rating,
            CO_ATTENDANCE_PERCENTAGE,
            '125' AS total,
            '100' AS want_course_again
        FROM
            (
                SELECT
                    sess_name,
                    school_code,
                    school_name,
                    dept_name,
                    course_code,
                    course_cd_name,
                    prefix
                     || ''
                     || emp_f_name
                     || ' '
                     || emp_l_name AS teacher_name,
                    get_course_name(fc_co_id) AS course_name,
                    fc_co_id,
                    courses_offered.co_course_cr,
                    (
                        SELECT COUNT(DISTINCT fe.sai_enrollment_no)
                        FROM faculty_evaluation fe
                        JOIN evaluations e ON fe.evaluation_id = e.id
                        WHERE fe.fe_co_id = fc_co_id
                          AND e.faculty_feedback_type = 'new'
                          AND e.evaluation_category_section = 'E'
                          AND EXISTS (
                              SELECT 1
                              FROM student_courses sc
                              WHERE sc.sc_sai_enrollment_no = fe.sai_enrollment_no
                                AND sc.sc_status = 'A'
                                AND (
                                       sc.sc_co_id = fc_co_id 
                                    OR sc.sc_co_id IN (SELECT c.co_id FROM courses_offered c WHERE c.parent_co_id = fc_co_id)
                                    OR sc.SC_CMS_COURSE_CO_ID = fc_co_id
                                )
                          )
                    ) AS feedback_submitted,
                    get_faculty_evaluation(co_id,'A',30) AS quality_of_teaching,
                    get_faculty_evaluation(co_id,'B',25) AS course_evaluation,
                    get_faculty_evaluation(co_id,'D',10) AS exam_and_assessment,
                    round(
                        ( ( (get_faculty_evaluation(co_id,'A',30) + get_faculty_evaluation(co_id,'B',25) + get_faculty_evaluation(co_id,'D',10) ) / 3) ),
                        2
                    ) AS overall_rating,
                    (
                        SELECT
                            COUNT(*)
                        FROM
                            course_attendance
                        WHERE
                                course_attendance.co_id IN (
                                    SELECT
                                        courses_offered.co_id
                                    FROM
                                        courses_offered
                                    WHERE
                                            courses_offered.co_id = fc_co_id
                                        OR
                                            courses_offered.parent_co_id = fc_co_id
                                )
                            AND (
                                course_attendance.ca_attendance = 'A'
                            )
                    ) AS ta,
                    (
                        SELECT
                            COUNT(*)
                        FROM
                            course_attendance
                        WHERE
                                course_attendance.co_id IN (
                                    SELECT
                                        courses_offered.co_id
                                    FROM
                                        courses_offered
                                    WHERE
                                            courses_offered.co_id = fc_co_id
                                        OR
                                            courses_offered.parent_co_id = fc_co_id
                                )
                            AND (
                                    course_attendance.ca_attendance = 'P'
                                OR
                                    course_attendance.ca_hod_attendance = 'EX'
                            )
                    ) AS tp,
                    (
                        SELECT COUNT(DISTINCT sc.sc_sai_enrollment_no)
                        FROM student_courses sc
                        WHERE sc.sc_status = 'A'
                          AND (
                                 sc.sc_co_id = fc_co_id 
                              OR sc.sc_co_id IN (SELECT c.co_id FROM courses_offered c WHERE c.parent_co_id = fc_co_id)
                              OR sc.SC_CMS_COURSE_CO_ID = fc_co_id
                          )
                    ) AS total_students,
                    CO_ATTENDANCE_PERCENTAGE,
                'N/A' AS CO_ATTENDANCE_PERCENTAGE_DATE

                FROM
                    faculty_courses
                    LEFT JOIN courses_offered ON fc_co_id = co_id
                    LEFT JOIN emp_employees ON employee_id = faculty_courses.fc_employee_id
                    LEFT JOIN academic_sessions ON co_sess_id = sess_id
                    LEFT JOIN schools ON co_school_id = school_id
                    LEFT JOIN departments ON co_dept_id = departments.dept_id
                    LEFT JOIN courses_two ON co_course_id = course_id
                WHERE
                        fc_status = 'E' and $whereStr
            )
            order by teacher_name";

        //  echo $ora;exit;
        $rec_ora = DB::select($ora);

        if ($request->get('result-for') == '') {
            return response()->json(['data' => $rec_ora, 'status' => true, 'message' => count($rec_ora) . ' record(s) found!']);
        }


        return view('grader.reports.faculty.faculty_evaluation_from_F2025', ['teacher_evaluation' => $rec_ora]);
    }

    private function getTaExclusionConditions()
    {
        // List of specific TA usernames to exclude
        $excluded_usernames = [
            'zuha.khalid',
            'aila.ayyaz',
            'emaan.sarwar',
            'hassan.saghir',
            'eisha.ahmad',
            'ahmad.saleem',
            'abdul.rehman.imran',
            'aimanirfan.ahmad',
            'dawood.bhatti',
            'zulqarnain.butt',
            'abubakar.mehmood',
            'rana.ans',
            'yaseen.nazim'
        ];

        // Convert all to UPPERCASE for case-insensitive comparison
        $excluded_usernames = array_map('strtoupper', $excluded_usernames);

        // Create quoted string for SQL IN clause
        $username_list = "'" . implode("','", $excluded_usernames) . "'";

        // Return SQL fragment using UPPER()
        return " AND UPPER(emp_employees.emp_ad_username) NOT LIKE 'F20%-%' 
                 AND UPPER(emp_employees.emp_ad_username) NOT LIKE 'S20%-%' 
                 AND UPPER(emp_employees.emp_ad_username) NOT LIKE 'SUM20%-%' 
                 AND UPPER(emp_employees.emp_ad_username) NOT IN ($username_list) ";
    }


    function faculty_comments($co_id)
    {
        $faculty_id = Auth::user()->employee_id;
        //   echo $co_id ;exit;
        $ora = "SELECT
             fe_evaluation_value as comments,
             sess_name,
             school_name,
             dept_name,
             course_code,
             courses_two.course_cd_name,
             get_teachername(fe_co_id) as teacher_name,
             co_course_cr
         FROM
             faculty_evaluation
             LEFT JOIN courses_offered ON co_id = fe_co_id
             LEFT JOIN academic_sessions ON co_sess_id = sess_id
             LEFT JOIN schools ON co_school_id = school_id
             LEFT JOIN departments ON co_dept_id = departments.dept_id
             LEFT JOIN courses_two ON co_course_id = course_id
         WHERE
                 fe_co_id = $co_id
             AND
                 -- 12 for old comments, 32 for new form comments (F2025 onwards)
                 evaluation_id IN (12, 32)
                 AND fe_faculty_id = $faculty_id 
            --  AND
            --      fc_status = 'E'
                 ";
        //  echo $ora;exit;
        $rec_ora = DB::select($ora);

        $content = view('grader.reports.faculty.faculty_comments', ['teacher_evaluation' => $rec_ora])->render();

        return response($content)
        ->header('Content-Type', 'application/pdf')
        ->header('Content-Disposition', 'inline; filename="faculty_comments_' . $co_id . '.pdf"')
        ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
        ->header('Pragma', 'no-cache')
        ->header('Expires', '0');
    }
    function contacts()
    {
        return view('grader.reportgenerator.contacts');
    }
    function cvt()
    {
        return view('grader.reportgenerator.cvt');
    }

    function getCoursesByTeacher(Request $request)
    {
        $tid = $request->get('id');
        $sess_id = $request->get('sess_id');
        $dept_id = $request->get('dept_id');

        $dept_id = (($dept_id != 0) && ($dept_id != '')) ? $dept_id : 0;

        $empEmail = '';
        if (($tid != 0) && ($tid != '')) {
            $res = EmpEmployees::where('employee_id', $tid)->first();
            $empEmail = $res->emp_email;
        }


        $fc = new FacultyCourses;
        $facultyCourses = $fc->fetch($sess_id, $dept_id, $empEmail);
        return response()->json(['data' => $facultyCourses, 'status' => true, 'message' => count($facultyCourses) . ' record(s) found!']);
    }

    public function courseAttendanceDetail()
    {
        return view('grader.reports.faculty.course_attendance_detail');
    }
    public function studentSemesterPerformance()
    {
        return view('grader/reports.faculty.student_semester_performance');
    }
    public function academicHistory()
    {
        return view('grader/reports/faculty.academic_history');
    }
    public function surveyReport()
    {
        return view('grader/reports/faculty.surveyReport');
    }
    public function facultyCourseAttendance()
    {
        return view('grader.reports.faculty.faculty_course_attendance');
    }

    public function facultyAttendancesummary()
    {
        return view('grader.reports.faculty.attendance_summary_dept_wise');
    }
    public function studentAssessment()
    {
        return view('grader.reports.faculty.student_assessment_report');
    }

    public function ApplicantProfile()
    {
        return view('grader.reports.faculty.applicant_academic_profile');
    }
    public function ApplicantProfileStatus()
    {
        return view('grader.reports.faculty.applicant_academic_profile_status');
    }
    public function AdmissionStatus()
    {
        return view('grader.reports.faculty.applicant_academic_vc');
    }
    public function checkStudentNBS()
    {
        return view('grader.reports.faculty.checkStudentNbs');
    }
    public function checkStudentHealth()
    {
        $sql = "SELECT DISTINCT
                        students_health_record.sai_enrollment_no,spi_name,school_name,prog_name,sai_sdpo_school_id,shr_file
                    FROM
                        students_health_record
                        left  JOIN students_health_detail  on  students_health_record.sai_enrollment_no = shd_enrollment_no
                        left  JOIN students_apply_info  on  students_health_record.sai_enrollment_no = students_apply_info.sai_enrollment_no
                        left  JOIN students_personal_info  on  students_apply_info.sai_spi_id = spi_Id
                        left  JOIN schools  on  schools.school_id = students_apply_info.sai_sdpo_school_id
                        left  JOIN programs  on  programs.prog_id = students_apply_info.sai_sdpo_prog_id
                    WHERE
                        shr_flag = 'Health_Appraisal_Form'
                        and sai_sdpo_sess_id = 162
                        and shr_file is  null
                        order by sai_sdpo_school_id asc";

        $resSql = DB::select($sql);
        return view('grader.reports.faculty.checkStudentHealth', ['res' => $resSql]);
    }
    public function checkStudentZeroReport()
    {
        $sql = "select
                    DISTINCT student_feedbacks.sai_enrollment_no,spi_name,school_name,prog_name,sai_sdpo_school_id
                            FROM
                                student_feedbacks

                                        left  JOIN students_apply_info  on  student_feedbacks.sai_enrollment_no = students_apply_info.sai_enrollment_no
                                        left  JOIN students_personal_info  on  students_apply_info.sai_spi_id = spi_Id
                                        left  JOIN schools  on  schools.school_id = students_apply_info.sai_sdpo_school_id
                                        left  JOIN programs  on  programs.prog_id = students_apply_info.sai_sdpo_prog_id
                            WHERE
                                sf_type = 'zero-week'
                                and sai_sdpo_sess_id = 162
                        order by sai_sdpo_school_id asc";

        $resSql = DB::select($sql);
        return view('grader.reports.faculty.checkStudentZeroWeek', ['res' => $resSql]);
    }
    public function gymReport2()
    {
        $sql = "WITH subscription_months (
                sp_id,
                user_id,
                user_type,
                student_full_name,
                callback_paid_on,
                subscription_date,
                expiration_date,
                subscription_month,
                sess_id
            ) AS (
                SELECT
                    sp_id,
                    user_id,
                    user_type,
                    student_full_name,
                    callback_paid_on,
                    subscription_date,
                    expiration_date,
                    TO_CHAR(subscription_date, 'YYYY-MM') AS subscription_month,
                    sess_id
                FROM
                    subscriptions
                WHERE
                    sp_id = 2
                UNION ALL
                SELECT
                    sm.sp_id,
                    sm.user_id,
                    sm.user_type,
                    sm.student_full_name,
                    sm.callback_paid_on,
                    sm.subscription_date,
                    sm.expiration_date,
                    TO_CHAR(ADD_MONTHS(TO_DATE(sm.subscription_month, 'YYYY-MM'), 1), 'YYYY-MM'),
                    sm.sess_id
                FROM
                    subscription_months sm
                WHERE
                    ADD_MONTHS(TO_DATE(sm.subscription_month, 'YYYY-MM'), 1) <= sm.expiration_date
            )
            
            SELECT
                'Total Staff' AS label,
                TO_CHAR(SUBSCRIPTION_DATE, 'YYYY-MM') AS subscription_month,
                COUNT(*) AS total_count,
                COUNT(*) AS paid_count,
                0 AS unpaid_count
            FROM
                subscriptions
            WHERE
                user_type IS NOT NULL
            GROUP BY
                TO_CHAR(SUBSCRIPTION_DATE, 'YYYY-MM')
            
            UNION ALL
            
            -- Total Scholarship Applicants with session match
            SELECT
                'Total Scholarship Applicants' AS label,
                TO_CHAR(S.SUBSCRIPTION_DATE, 'YYYY-MM') AS subscription_month,
                COUNT(*) AS total_count,
                COUNT(CASE WHEN S.callback_paid_on IS NOT NULL THEN 1 END) AS paid_count,
                COUNT(CASE WHEN S.callback_paid_on IS NULL THEN 1 END) AS unpaid_count
            FROM
                bnucms.subscriptions S
            WHERE
                S.user_type IS NULL
                AND EXISTS (
                    SELECT 1
                    FROM bnucms.sports_applicants A
                    WHERE A.sa_enrolment_no = S.user_id
                      AND A.sa_student_category = 'Scholarship'
                      AND A.sa_sess_id = S.sess_id
                )
            GROUP BY
                TO_CHAR(S.SUBSCRIPTION_DATE, 'YYYY-MM')
            
            UNION ALL
            
            -- Total Sports Applicants with session match
            SELECT
                'Total Sports Applicants' AS label,
                TO_CHAR(S.SUBSCRIPTION_DATE, 'YYYY-MM') AS subscription_month,
                COUNT(*) AS total_count,
                COUNT(CASE WHEN S.callback_paid_on IS NOT NULL THEN 1 END) AS paid_count,
                COUNT(CASE WHEN S.callback_paid_on IS NULL THEN 1 END) AS unpaid_count
            FROM
                bnucms.subscriptions S
            WHERE
                S.user_type IS NULL
                AND EXISTS (
                    SELECT 1
                    FROM bnucms.sports_applicants A
                    WHERE A.sa_enrolment_no = S.user_id
                      AND A.sa_student_category = 'Sports'
                      AND A.sa_sess_id = S.sess_id
                )
            GROUP BY
                TO_CHAR(S.SUBSCRIPTION_DATE, 'YYYY-MM')
            
            UNION ALL
            
            -- Semester Wise Students (already filtered by sp_id = 2 and session_id included)
            SELECT
                'Semester Wise Students' AS label,
                SM.subscription_month,
                COUNT(*) AS total_count,
                COUNT(CASE WHEN SM.callback_paid_on IS NOT NULL THEN 1 END) AS paid_count,
                COUNT(CASE WHEN SM.callback_paid_on IS NULL THEN 1 END) AS unpaid_count
            FROM
                subscription_months SM
            WHERE
                SM.sp_id = 2
                AND SM.callback_paid_on IS NOT NULL
            GROUP BY
                SM.subscription_month
            
            UNION ALL
            
            -- Month Wise Students (Non-sports only) with session exclusion
            SELECT
                'Month Wise Students' AS label,
                TO_CHAR(S.SUBSCRIPTION_DATE, 'YYYY-MM') AS subscription_month,
                COUNT(*) AS total_count,
                COUNT(CASE WHEN S.callback_paid_on IS NOT NULL THEN 1 END) AS paid_count,
                COUNT(CASE WHEN S.callback_paid_on IS NULL THEN 1 END) AS unpaid_count
            FROM
                bnucms.subscriptions S
            WHERE
                S.user_type IS NULL
                AND S.sp_id = 1
                AND NOT EXISTS (
                    SELECT 1
                    FROM bnucms.sports_applicants A
                    WHERE A.sa_enrolment_no = S.user_id
                      AND A.sa_sess_id = S.sess_id
                )
            GROUP BY
                TO_CHAR(S.SUBSCRIPTION_DATE, 'YYYY-MM')
            
            ORDER BY
                subscription_month DESC";
        $res = DB::select($sql);
        return view('grader.reports.faculty.gym_report', ['res' => $res]);
    }
    public function gymReport()
    {

        return view('grader.reports.faculty.gym_report');
    }

    public function getGymReport()
    {

        $sql = "WITH subscription_months AS (
    /* =========================================================
       SEMESTER GYM EXPANSION (sp_id = 2, sess_id = 166)
       ========================================================= */
    SELECT
        s.sp_id,
        s.user_id,
        s.user_type,
        s.student_full_name,
        s.callback_paid_on,
        s.subscription_date,
        s.expiration_date,
        s.sess_id,
        TO_CHAR(
            ADD_MONTHS(TRUNC(s.subscription_date, 'MM'), lvl - 1),
            'YYYY-MM'
        ) AS subscription_month
    FROM bnucms.subscriptions s
    CROSS JOIN (
        SELECT LEVEL lvl FROM dual CONNECT BY LEVEL <= 12
    )
    WHERE
        s.sp_id = 2
        AND s.sess_id = 166
        AND ADD_MONTHS(TRUNC(s.subscription_date, 'MM'), lvl - 1)
            <= TRUNC(s.expiration_date, 'MM')
)

SELECT *
FROM (

    /* =========================
       TOTAL STAFF
       ========================= */
    SELECT
        'Total Staff' AS label,
        TO_CHAR(s.subscription_date, 'YYYY-MM') AS subscription_month,
        COUNT(*) AS total_count,
        COUNT(CASE WHEN s.callback_paid_on IS NOT NULL THEN 1 END) AS paid_count,
        COUNT(CASE WHEN s.callback_paid_on IS NULL THEN 1 END) AS unpaid_count
    FROM bnucms.subscriptions s
    WHERE s.user_type IS NOT NULL
    GROUP BY TO_CHAR(s.subscription_date, 'YYYY-MM')

    UNION ALL

    /* =========================
       TOTAL SCHOLARSHIP APPLICANTS
       ========================= */
    SELECT
        'Total Scholarship Applicants',
        TO_CHAR(s.subscription_date, 'YYYY-MM'),
        COUNT(*),
        COUNT(CASE WHEN s.callback_paid_on IS NOT NULL THEN 1 END),
        COUNT(CASE WHEN s.callback_paid_on IS NULL THEN 1 END)
    FROM bnucms.subscriptions s
    WHERE
        s.user_type IS NULL
        AND EXISTS (
            SELECT 1
            FROM bnucms.sports_applicants a
            WHERE a.sa_enrolment_no = s.user_id
              AND a.sa_student_category = 'Scholarship'
              AND a.sa_sess_id = s.sess_id
        )
    GROUP BY TO_CHAR(s.subscription_date, 'YYYY-MM')

    UNION ALL

    /* =========================
       TOTAL SPORTS APPLICANTS
       ========================= */
    SELECT
        'Total Sports Applicants',
        TO_CHAR(s.subscription_date, 'YYYY-MM'),
        COUNT(*),
        COUNT(CASE WHEN s.callback_paid_on IS NOT NULL THEN 1 END),
        COUNT(CASE WHEN s.callback_paid_on IS NULL THEN 1 END)
    FROM bnucms.subscriptions s
    WHERE
        s.user_type IS NULL
        AND EXISTS (
            SELECT 1
            FROM bnucms.sports_applicants a
            WHERE a.sa_enrolment_no = s.user_id
              AND a.sa_student_category = 'Sports'
              AND a.sa_sess_id = s.sess_id
        )
    GROUP BY TO_CHAR(s.subscription_date, 'YYYY-MM')

    UNION ALL

    /* =========================
       SEMESTER WISE STUDENTS (FIXED)
       ========================= */
    SELECT
        'Semester Wise Students',
        sm.subscription_month,
        COUNT(DISTINCT sm.user_id) AS total_count,
        COUNT(DISTINCT CASE WHEN sm.callback_paid_on IS NOT NULL THEN sm.user_id END) AS paid_count,
        COUNT(DISTINCT CASE WHEN sm.callback_paid_on IS NULL THEN sm.user_id END) AS unpaid_count
    FROM subscription_months sm
    GROUP BY sm.subscription_month

    UNION ALL

    /* =========================
       MONTH WISE STUDENTS (NON-SPORTS)
       ========================= */
    SELECT
        'Month Wise Students',
        TO_CHAR(s.subscription_date, 'YYYY-MM'),
        COUNT(*),
        COUNT(CASE WHEN s.callback_paid_on IS NOT NULL THEN 1 END),
        COUNT(CASE WHEN s.callback_paid_on IS NULL THEN 1 END)
    FROM bnucms.subscriptions s
    WHERE
        s.user_type IS NULL
        AND s.sp_id = 1
        AND NOT EXISTS (
            SELECT 1
            FROM bnucms.sports_applicants a
            WHERE a.sa_enrolment_no = s.user_id
              AND a.sa_sess_id = s.sess_id
        )
    GROUP BY TO_CHAR(s.subscription_date, 'YYYY-MM')

)
ORDER BY subscription_month DESC";
        $res = DB::select($sql);

        return response()->json(['data' => $res]);


    }

    public function getScholarshipDetails(Request $request)
    {

        $type = $request->type;
        $month = $request->month;

        // Convert to Carbon date
        $startDate = Carbon::parse($month . '-01')->startOfMonth()->toDateString();  // 2025-11-01
        $endDate = Carbon::parse($month . '-01')->endOfMonth()->toDateString();    // 2025-11-30

        // For testing
        // dd($startDate, $endDate);
        if ($type === "scholarship") {
            $resultSql = "SELECT * FROM (
                        WITH SCH AS (
                            SELECT DISTINCT
                                a.sa_enrolment_no,
                                aai.aai_id,
                                st.scholarship_name,

                                (SELECT COUNT(*) 
                                FROM subscriptions 
                                WHERE user_id = a.sa_enrolment_no 
                                AND TRUNC(entry_date) BETWEEN TO_DATE(:start_date, 'YYYY-MM-DD') 
                                AND TO_DATE(:end_date, 'YYYY-MM-DD')
                                ) AS count_current,

                                CASE 
                                    WHEN (SELECT COUNT(*) 
                                        FROM subscriptions 
                                        WHERE user_id = a.sa_enrolment_no 
                                        AND TRUNC(entry_date) BETWEEN TO_DATE(:start_date, 'YYYY-MM-DD') 
                                        AND TO_DATE(:end_date, 'YYYY-MM-DD')
                                        ) > 0 
                                    THEN 'subscribed' 
                                    ELSE 'unsubscribed' 
                                END AS subscription_status,

                                (SELECT challan_status
                                FROM subscriptions 
                                WHERE user_id = a.sa_enrolment_no 
                                AND TRUNC(entry_date) BETWEEN TO_DATE(:start_date, 'YYYY-MM-DD') 
                                AND TO_DATE(:end_date, 'YYYY-MM-DD')
                                AND ROWNUM = 1
                                ) AS challan_status

                            FROM bnucms.sports_applicants a
                            LEFT JOIN students_apply_info sai 
                                ON sai.sai_enrollment_no = a.sa_enrolment_no
                            LEFT JOIN students_personal_info spi 
                                ON sai.sai_spi_id = spi.spi_id
                            LEFT JOIN admission_apply_info aai 
                                ON aai.aai_api_id = sai.sai_api_id
                            LEFT JOIN students_scholarships_details ssd 
                                ON ssd.ssd_app_id = aai.aai_id
                            LEFT JOIN scholarship_type st 
                                ON ssd.ssd_type = st.scholarship_id
                            WHERE a.sa_student_category = 'Scholarship'
                            AND a.sa_sess_id = 165
                            AND ssd.ssd_sess_id = 165
                            AND ssd_percentage > 0
                        )
                        SELECT 
                            sa_enrolment_no,
                            aai_id,
                            LISTAGG(scholarship_name, ', ') WITHIN GROUP (ORDER BY scholarship_name) AS scholarship_list,
                            subscription_status,
                            CASE 
                                WHEN subscription_status = 'unsubscribed' THEN NULL 
                                ELSE challan_status 
                            END AS challan_status
                        FROM SCH
                        GROUP BY 
                            sa_enrolment_no,
                            aai_id,
                            subscription_status,
                            challan_status
                    ) 
                    WHERE subscription_status = 'subscribed'
                    order by challan_status";
        } else if ($type === "sports") {
            $resultSql = "SELECT
                                s.user_id,
                                sa.sa_sports_category,
                                s.challan_status
                            FROM
                                subscriptions s
                            JOIN
                                bnucms.sports_applicants sa
                                ON sa.sa_enrolment_no = s.user_id
                                AND sa.sa_student_category = 'Sports'
                                AND sa.sa_sess_id = s.sess_id
                            WHERE
                                s.user_id IN (
                                    SELECT sa_enrolment_no
                                    FROM bnucms.sports_applicants
                                    WHERE sa_student_category = 'Sports'
                                    AND sa_sess_id = 165
                                    GROUP BY sa_enrolment_no
                                )
                                AND s.sess_id = 165
                                    AND TRUNC(entry_date) BETWEEN TO_DATE(:start_date, 'YYYY-MM-DD') 
                                                            AND TO_DATE(:end_date, 'YYYY-MM-DD')
                            ORDER BY s.challan_status";
            //  echo $resultSql;exit;
        }

        $result = DB::select($resultSql, [
            'start_date' => $startDate,
            'end_date' => $endDate,
        ]);

        return response()->json($result);
    }

    public function updateGymStatus()
    {
        // echo "!@3";exit;
        $query = "UPDATE BNUCMS.SUBSCRIPTIONS s
                SET 
                    s.CALLBACK_PAID_ON = (
                        SELECT sc.CALLBACK_PAID_ON
                        FROM student_Challan sc
                        WHERE sc.id = s.CHALLAN_ID
                    ),
                    s.CHALLAN_STATUS = (
                        SELECT sc.SC_CHALLAN_STATUS
                        FROM student_Challan sc
                        WHERE sc.id = s.CHALLAN_ID
                    )
                WHERE s.user_type IS NULL
                AND s.CHALLAN_ID IN (SELECT sc.id FROM student_Challan sc where SC_CHALLAN_STATUS = 'PAID')
                and s.CALLBACK_PAID_ON is null
            ";

        DB::statement($query);

        return response()->json(['message' => 'Subscriptions updated successfully']);


    }
    public function grading_report()
    {
        return view('grader.reports.faculty.grading_status_report');
    }
    public function yearwiseEnrollmentStatus()
    {
        return view('grader.reports.faculty.applicant_academic_enrollment_vc');
    }
    public function gradingStatus()
    {
        return view('grader.reports.faculty.grading_status');
    }

    public function enrollmentStatus()
    {
        return view('grader.reports.faculty.enrollment_status');
    }

    public function HRAttendanceReport()
    {
        $sql = "select hr_employee_id from emp_employees where employee_id = " . auth()->user()->employee_id . "";
        $rec = DB::select($sql);
        $hr_emp_id = $rec[0]->hr_employee_id;

        return view('grader.reports.faculty.hr_attendance', ['hr_employee_id' => $hr_emp_id]);
    }

    function faculty_courseDetail($co_id)
    {

        $assess_weightage = "select co_course_cr,get_course_name(course_criterias.co_id) as course_cd_name,get_teachername(course_criterias.co_id) as teacher_name,assessment_name,assessment_short_name,sess_name,school_name
        , course_criterias.cc_weightage
        from course_criterias
        LEFT JOIN ASSESSMENTS on course_criterias.ASSESSMENT_ID=ASSESSMENTS.id
        left join courses_offered on courses_offered.co_id = course_criterias.co_id
        left join academic_sessions on sess_id = co_sess_id
        left join schools on school_id = co_school_id
        where course_criterias.co_id = $co_id
        order by assessment_id
        ";

        $res_assess_weightage = DB::select($assess_weightage);

        $course_contents = "select * from COURSE_OFFERING_CONTENTS where coc_co_id = $co_id
        ";

        $res_course_contents = DB::select($course_contents);
        //print_r($rec_ora); exit;
        return view('grader.reports.faculty.faculty_course_detail_report', ['teacher_evaluation' => $res_assess_weightage, 'contents' => $res_course_contents]);




    }
    function course_offering()
    {
        $school_id = auth()->user()->school_id;

        $sql = "select * from (
          SELECT
              check_course_content(co_id) AS content_status,
              get_course_name(co_id) AS course_name,
              get_teachername(co_id) AS teacher_name,
              courses_offered.*
          FROM
              courses_offered
          WHERE
                  co_sess_id = 168
              AND
 					parent_co_id IS NULL";
        $allowed_schools = array("644");
        if (!in_array(auth()->user()->group_id, $allowed_schools)) {
            $sql .= " AND co_school_id = $school_id ";
        }
        $sql .= " order by content_status asc)";        //echo $sql;exit;
        $rec = DB::select($sql);
        return view('grader.course.course_offering', compact('rec'));
    }
    public function course_offering_report(Request $request)
    {
        // 1️⃣ Get all sessions
        $sessions = \App::call('App\Http\Controllers\Grader\AcademicSessionController@retrieve');

        // 2️⃣ Get active schools
        $schools = DB::table('schools')
            ->select('school_id', 'school_name')
            ->where('school_status', 'E')
            ->get();

        // 3️⃣ Get latest session if not selected
        $latestSession = DB::table('courses_offered')->max('co_sess_id');
        $selectedSession = $request->get('session', $latestSession);
        $selectedSchool = $request->get('school');
        $selectedDept = $request->get('department');
        // dd($selectedDept );

        // 4️⃣ Build SQL dynamically
        $sql = "
            SELECT 
                check_course_content(co_id) AS content_status,
                co_id,
                (select cc_file_name from course_contents where  cc_type= 'file' and cc_co_id = co_id) as file_path,
                get_course_name(co_id) AS course_name,
                get_teachername(co_id) AS teacher_name,
                courses_offered.*
            FROM courses_offered
            WHERE parent_co_id IS NULL
            AND co_sess_id = :session_id
        ";

        $params = ['session_id' => $selectedSession];

        if (!empty($selectedSchool)) {
            $sql .= " AND co_school_id = :school_id";
            $params['school_id'] = $selectedSchool;
        }

        if (!empty($selectedDept)) {
            $sql .= " AND co_dept_id = :dept_id";
            $params['dept_id'] = $selectedDept;
        }

        $sql .= " ORDER BY content_status ASC";

        // 5️⃣ Execute main query
        $rec = DB::select($sql, $params);

        // 6️⃣ Get departments for the selected school
        $departments = [];
        if (!empty($selectedSchool)) {
            $departments = DB::table('departments')
                ->where('dept_school_id', $selectedSchool)
                ->where('dept_status', 'E')
                ->select('dept_id', 'dept_name')
                ->get();
        }
        // 7️⃣ Filters
        $filters = [
            'user_session' => $selectedSession,
            'school' => $selectedSchool,
            'department' => $selectedDept,
        ];

        // 8️⃣ Return to view
        return view('grader.course.course_offering_list', compact(
            'rec',
            'schools',
            'sessions',
            'filters',
            'departments'
        ));
    }

    public function facultyCourseAttendanceReport(Request $request)
    {

        $tac_end_date = $request->get('tac_end_date');
        //dd($tac_end_date);
        $dept_id = $request->get('facultyWload_dept_id');
        $school_id = $request->get('facultyWload_school_id');
        $sess_id = $request->get('facultyWload_sess_id');
        $fco_attendance_type = $request->get('fco_attendance_type');

        $SQL = "SELECT *
            FROM
              (SELECT tt_subject_day,
                ( SELECT ts_name FROM timetable_slots WHERE ts_id = tt_subject_time_from_id
                ) AS tt_subject_time_from,
                ( SELECT ts_name FROM timetable_slots WHERE ts_id = tt_subject_time_to_id
                )                          AS tt_subject_time_to,
                get_teachername(mtt_co_id) AS faculty_name,
                tt_subject_name            AS makeup_desc,
                tt_subject_name,
                tt_teachername,
                course_cd_name,
                courses_offered.co_section AS co_section,
                (SELECT
                  CASE COUNT(*)
                    WHEN 0
                    THEN 'NOT DELIVERED'
                    ELSE 'DELIVERED'
                  END AS component_a
                FROM course_attendance
                WHERE ca_attendance_date =  to_Date('$tac_end_date','YYYY-MM-DD')
                AND mtt_id               = tt_id
                ) AS class_status,
                (SELECT MAX(CREATED_AT) FROM COURSE_ATTENDANCE WHERE mtt_id = tt_id
                ) AS attendance_marked,
                dept_name
              FROM master_timetable
              LEFT JOIN courses_offered
              ON mtt_co_id = co_id
              LEFT JOIN courses_two
              ON course_id = co_course_id
              LEFT JOIN departments
              ON dept_id        = co_dept_id
              WHERE tt_status   = 'E'
              AND co_status     ='E'
              AND tt_class_date =  to_Date('$tac_end_date','YYYY-MM-DD')
              AND mtt_co_id    IS NOT NULL
              AND mtt_co_id    IN
                (SELECT co_id
                FROM courses_offered
                WHERE 1          =1
                AND co_sess_id   = $sess_id
                AND co_school_id = $school_id
                AND co_dept_id  IN ($dept_id)
                )";
        if ($fco_attendance_type == "2") {
            $SQL .= " AND
                            tt_id IN (
                                SELECT DISTINCT
                                    mtt_id
                                FROM
                                    course_attendance
                                WHERE
                                    ca_attendance_date = date'" . $tac_end_date . "'
                            )";
        } else if ($fco_attendance_type == "3") {
            $SQL .= "AND
                            tt_id not IN (
                                SELECT DISTINCT
                                    mtt_id
                                FROM
                                    course_attendance
                                WHERE
                                    ca_attendance_date = date'" . $tac_end_date . "'
                            )";
        }




        $SQL .= ") ORDER BY class_status";
        // echo $SQL;exit;
        $resfiles = DB::select($SQL);


        return view('grader.reports.faculty.faculty_course_attendance_report', ['all_files' => $resfiles, 'tac_end_date' => $tac_end_date]);
    }

    public function hostelSurveyIndex(Request $request)
    {
        $sql = "
                SELECT
        sfd_title AS question,
        SUM(CASE WHEN sfd_desc = 'NO' THEN 1 ELSE 0 END) AS no_count,
        SUM(CASE WHEN sfd_desc = 'YES' THEN 1 ELSE 0 END) AS yes_count,
        SUM(CASE WHEN sfd_desc = 'In Al Kabir apartments/homes adjacent to BNU' THEN 1 ELSE 0 END) AS ak_apartments,
        SUM(CASE WHEN sfd_desc = 'In Book & Bed hostel across the street from BNU' THEN 1 ELSE 0 END) AS book_bed_count,
        SUM(CASE WHEN sfd_desc = 'In the BNU on campus hostel facility' THEN 1 ELSE 0 END) AS on_campus,
        SUM(CASE WHEN sfd_desc = 'Somewhere else' THEN 1 ELSE 0 END) AS somewhere_else
    FROM
        student_feedbacks
        WHERE
        sfd_title IN ('Is your permanent hometown Lahore?', 'Where are you currently residing?', 'Will you be installing an Air Conditioner in the room if you are allotted a hostel room by BNU?')
        AND sfd_desc != 'In the BNU on campus hostel facility'
    GROUP BY
        sfd_title
    ORDER BY
        sfd_title
            ";
        // Execute the query
        $res = DB::select($sql);
        $totalCountSql = "
                SELECT COUNT(DISTINCT sai_enrollment_no) AS total_distinct_students
                FROM student_feedbacks
                WHERE sf_type = 'hostelsurvay'";

        $totalCountResult = DB::select($totalCountSql);
        $totalDistinctStudents = $totalCountResult[0]->total_distinct_students;

        return view('grader.reports.faculty.student_hostel_survey_report', [
            'res' => $res,
            'totalDistinctStudents' => $totalDistinctStudents
        ]);
    }



    public function downloadCourseWiseReport(Request $request)
    {

        $course = $request->course;




        // Fetch all required data in a single controller
        $courseEvaluations = DB::select("
                SELECT
                    fe_co_id AS course_id,
                    evaluations.evaluation_question AS evaluation_criteria,
                    CASE
                        WHEN evaluations.id = 17 THEN 0.5
                        WHEN evaluations.id = 18 THEN 0.75
                        WHEN evaluations.id = 19 THEN 2.0
                        WHEN evaluations.id = 20 THEN 2.0
                        WHEN evaluations.id = 21 THEN 3.0
                        WHEN evaluations.id = 22 THEN 2.0
                        WHEN evaluations.id = 23 THEN 0.75
                        WHEN evaluations.id = 24 THEN 1.5
                        WHEN evaluations.id = 25 THEN 1.0
                        WHEN evaluations.id = 26 THEN 0.5
                        WHEN evaluations.id = 27 THEN 1.0
                        WHEN evaluations.id = 28 THEN 1.0
                        WHEN evaluations.id = 29 THEN 3.0
                        WHEN evaluations.id = 30 THEN 1.0
                        ELSE NULL
                    END AS weightage,
                    get_faculty_evaluation_new(fe_co_id, 70) AS average_score,
                    ROUND(
                        get_faculty_evaluation_new(fe_co_id, 70) *
                        CASE
                            WHEN evaluations.id = 17 THEN 0.5
                            WHEN evaluations.id = 18 THEN 0.75
                            WHEN evaluations.id = 19 THEN 2.0
                            WHEN evaluations.id = 20 THEN 2.0
                            WHEN evaluations.id = 21 THEN 3.0
                            WHEN evaluations.id = 22 THEN 2.0
                            WHEN evaluations.id = 23 THEN 0.75
                            WHEN evaluations.id = 24 THEN 1.5
                            WHEN evaluations.id = 25 THEN 1.0
                            WHEN evaluations.id = 26 THEN 0.5
                            WHEN evaluations.id = 27 THEN 1.0
                            WHEN evaluations.id = 28 THEN 1.0
                            WHEN evaluations.id = 29 THEN 3.0
                            WHEN evaluations.id = 30 THEN 1.0
                            ELSE NULL
                        END, 2
                    ) AS weighted_score
                FROM
                    faculty_evaluation
                LEFT JOIN evaluations ON faculty_evaluation.evaluation_id = evaluations.id
                WHERE
                    fe_co_id = $course
                    AND evaluations.evaluation_category_section = 'E'
                    AND evaluations.faculty_feedback_type = 'new'
                    AND faculty_evaluation.evaluation_id NOT IN (31, 32)
                GROUP BY
                    fe_co_id, evaluations.evaluation_question, evaluations.id
                ORDER BY
                    evaluations.id
            ");

        $comments = DB::table('faculty_evaluation')
            ->where('faculty_evaluation.evaluation_id', 32)
            ->where('fe_co_id', $course)
            ->pluck('fe_evaluation_value');

        $courseDetails = DB::selectOne("
                SELECT
                    schools.school_name AS school_name,
                    departments.dept_name AS department_name,
                    get_teachername(courses_offered.co_id) AS faculty_name,
                    (
                             select count(*)
                            from student_courses
                            where sc_co_id in (
                                select courses_offered.co_id
                                from courses_offered
                                where ( courses_offered.co_id = $course
                                    or courses_offered.parent_co_id = $course)
                                and sc_status = 'A'
                            )
                    ) AS total_students,
                    (
                       SELECT COUNT(DISTINCT sai_enrollment_no)
                        FROM faculty_evaluation
                        LEFT JOIN evaluations ON faculty_evaluation.evaluation_id = evaluations.id
                        WHERE
                            faculty_evaluation.fe_co_id = courses_offered.co_id
                            AND evaluations.faculty_feedback_type = 'new'
                            AND faculty_evaluation.evaluation_id != 32
                    ) AS no_of_evaluators,
                    courses_offered.co_course_cr AS course_credit_hours,
                    student_courses.sc_course_code AS course_code,
                    student_courses.sc_course_title AS course_title
                FROM
                    courses_offered
                LEFT JOIN schools ON courses_offered.co_school_id = schools.school_id
                LEFT JOIN departments ON courses_offered.co_dept_id = departments.dept_id
                LEFT JOIN student_courses ON student_courses.sc_co_id = courses_offered.co_id
                
                WHERE
                    courses_offered.co_id = $course

                     and student_courses.sc_enrollment_status=1
                    and student_courses.SC_STATUS='A'

            ");

        $evaluationCounts = DB::selectOne("
                SELECT
                    evaluations.evaluation_question AS question,
                    COUNT(CASE WHEN fe_evaluation_value = 1 THEN 1 END) AS no_count,
                    COUNT(CASE WHEN fe_evaluation_value = 2 THEN 1 END) AS yes_count
                FROM
                    faculty_evaluation
                LEFT JOIN evaluations ON faculty_evaluation.evaluation_id = evaluations.id
                WHERE
                    fe_co_id = $course
                    AND evaluations.evaluation_category_section = 'E'
                    AND evaluations.faculty_feedback_type = 'new'
                    AND faculty_evaluation.evaluation_id IN (31)
                GROUP BY evaluations.evaluation_question
            ");

        return view('grader.reports.faculty.faculty_evaluation_course_wise', compact('courseEvaluations', 'evaluationCounts', 'comments', 'courseDetails'));
    }



    public function downloadFacultyWiseReport(Request $request)
    {
        $session = $request->session;
        $department = $request->department;

        // Fetch the data using parameterized query
        $data = DB::selectOne("
            SELECT
                (SELECT sess_name FROM academic_sessions WHERE sess_id = :sess_id) AS sess_name,
                (SELECT dept_name FROM departments WHERE dept_id = :dept_id) AS dept_name,
                (
                    SELECT COUNT(DISTINCT faculty_courses.fc_employee_id)
                    FROM faculty_courses
                    LEFT JOIN student_courses ON faculty_courses.fc_co_id = student_courses.sc_co_id
                    WHERE faculty_courses.fc_co_sess_id = :sess_id2
                    AND faculty_courses.fc_co_dept_id = :dept_id2
                    AND student_courses.sc_faculty_feedback_type = 'new'
                ) AS total_no_of_faculty
            FROM DUAL
        ", [
            'sess_id' => $session,
            'dept_id' => $department,
            'sess_id2' => $session,
            'dept_id2' => $department,
        ]);



        // Pass the data to the Blade view
        return view('grader.reports.faculty.faculty_evaluation_faculty_wise', compact('data'));
    }

    public function downloadFacultyWiseReportList(Request $request)
    {
        // Extract input parameters
        $session = $request->session;
        $school = $request->school;
        $department = $request->department;
        $report_type = $request->report_type;
        $teacher = $request->teacher;

        // dd($request->all());
        // Fetch the co_id values dynamically based on the query
        // Fetch the co_id values dynamically based on the query
        $getcoIDs = DB::select("
       SELECT fc_co_id
       FROM faculty_courses
       LEFT JOIN student_courses ON fc_co_id = sc_co_id
       WHERE 1 = 1
       AND sc_faculty_feedback_type = 'new'
       AND fc_co_school_id = ?
       AND fc_co_sess_id = ?
       GROUP BY fc_co_id
   ", [$school, $session]);


        //    dd($getcoIDs);
        if (!empty($teacher)) {
            $getcoIDs = DB::select("
           SELECT fc_co_id
           FROM faculty_courses
           WHERE fc_employee_id = ?
           AND fc_co_sess_id = ?
           AND fc_co_id IN (
               SELECT sc_co_id
               FROM student_courses
               WHERE sc_faculty_feedback_type = 'new'
           )
       ", [$teacher, $session]);
        }

        // Convert the result to an array of IDs
        $fe_co_ids = array_map(function ($item) {
            // Check if the field is `sc_co_id` or `fc_co_id`
            return $item->sc_co_id ?? $item->fc_co_id;
        }, $getcoIDs);

        // Check if there are any IDs; if not, return an empty response
        if (empty($fe_co_ids)) {
            return response()->json(['message' => 'No data found'], 404);
        }

        // Create a comma-separated placeholder string for the IN clause
        $placeholders = implode(',', array_fill(0, count($fe_co_ids), '?'));

        // dd($fe_co_ids);
        // Define the query to fetch report data
        $sql = "
        SELECT
            fe_co_id AS course_id,
            evaluations.evaluation_question AS evaluation_criteria,
            get_teachername(fe_co_id) AS faculty_name,
            (
                SELECT co_course_cr
                FROM courses_offered
                WHERE courses_offered.co_id = faculty_evaluation.fe_co_id
                AND ROWNUM = 1
            ) AS course_credit_hours,
            (SELECT sc_course_code
             FROM student_courses
             WHERE sc_co_id = fe_co_id AND ROWNUM = 1) AS course_code,
            (SELECT sc_course_title
             FROM student_courses
             WHERE sc_co_id = fe_co_id AND ROWNUM = 1) AS course_title,
            (
                SELECT COUNT(*)
                FROM student_courses
                WHERE
                    sc_enrollment_status = 1
                    AND sc_co_id = fe_co_id
                    AND sc_status = 'A'
            ) AS total_students,
              COUNT(DISTINCT faculty_evaluation.sai_enrollment_no) AS no_of_evaluators,
            CASE
                WHEN evaluations.id = 17 THEN 0.5
                WHEN evaluations.id = 18 THEN 0.75
                WHEN evaluations.id = 19 THEN 2.0
                WHEN evaluations.id = 20 THEN 2.0
                WHEN evaluations.id = 21 THEN 3.0
                WHEN evaluations.id = 22 THEN 2.0
                WHEN evaluations.id = 23 THEN 0.75
                WHEN evaluations.id = 24 THEN 1.5
                WHEN evaluations.id = 25 THEN 1.0
                WHEN evaluations.id = 26 THEN 0.5
                WHEN evaluations.id = 27 THEN 1.0
                WHEN evaluations.id = 28 THEN 1.0
                WHEN evaluations.id = 29 THEN 3.0
                WHEN evaluations.id = 30 THEN 1.0
                ELSE NULL
            END AS weightage,
            get_faculty_evaluation_new(fe_co_id, 70) AS average_score,
            ROUND(
                get_faculty_evaluation_new(fe_co_id, 70) * 
                CASE
                    WHEN evaluations.id = 17 THEN 0.5
                    WHEN evaluations.id = 18 THEN 0.75
                    WHEN evaluations.id = 19 THEN 2.0
                    WHEN evaluations.id = 20 THEN 2.0
                    WHEN evaluations.id = 21 THEN 3.0
                    WHEN evaluations.id = 22 THEN 2.0
                    WHEN evaluations.id = 23 THEN 0.75
                    WHEN evaluations.id = 24 THEN 1.5
                    WHEN evaluations.id = 25 THEN 1.0
                    WHEN evaluations.id = 26 THEN 0.5
                    WHEN evaluations.id = 27 THEN 1.0
                    WHEN evaluations.id = 28 THEN 1.0
                    WHEN evaluations.id = 29 THEN 3.0
                    WHEN evaluations.id = 30 THEN 1.0
                    ELSE NULL
                END, 2
            ) AS weighted_score,
            (SELECT
                COUNT(CASE WHEN fe_evaluation_value = 2 THEN 1 END)
             FROM
                faculty_evaluation sub_fe
             LEFT JOIN evaluations sub_ev
                ON sub_fe.evaluation_id = sub_ev.id
             WHERE
                sub_fe.fe_co_id = faculty_evaluation.fe_co_id
                AND sub_ev.evaluation_category_section = 'E'
                AND sub_ev.faculty_feedback_type = 'new'
                AND sub_fe.evaluation_id IN (31)
            ) AS yes_count,
            (SELECT
                COUNT(CASE WHEN fe_evaluation_value = 1 THEN 1 END)
             FROM
                faculty_evaluation sub_fe
             LEFT JOIN evaluations sub_ev
                ON sub_fe.evaluation_id = sub_ev.id
             WHERE
                sub_fe.fe_co_id = faculty_evaluation.fe_co_id
                AND sub_ev.evaluation_category_section = 'E'
                AND sub_ev.faculty_feedback_type = 'new'
                AND sub_fe.evaluation_id IN (31)
            ) AS no_count
        FROM
            faculty_evaluation
        LEFT JOIN evaluations
            ON faculty_evaluation.evaluation_id = evaluations.id
        WHERE
            fe_co_id IN ($placeholders)
            AND evaluations.evaluation_category_section = 'E'
            AND evaluations.faculty_feedback_type = 'new'
            AND faculty_evaluation.evaluation_id NOT IN (31, 32)
        GROUP BY
            fe_co_id, evaluations.evaluation_question, evaluations.id
        ORDER BY
            evaluations.id
    ";

        // Bind the parameters dynamically
        $res = DB::select($sql, $fe_co_ids);

        // Return response as JSON
        return response()->json($res);
    }






}
