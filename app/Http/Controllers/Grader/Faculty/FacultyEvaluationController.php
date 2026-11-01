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
use App\sessions;
use Illuminate\Support\Facades\Input;
use App\Models\Grader\Schools;
use App\Models\Grader\Departments;
use App\Models\Grader\FacultyCourses;
use App\Models\Grader\EmpEmployees;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
class FacultyEvaluationController extends Controller{

    function teacherreportListing(){
        $loginUser = Auth::user();
        $sess = new sessions();
        $sess->startSession();
        $profile = new EmpEmployees();
        $emp_user = $loginUser::find($loginUser->id)->employee_id;
        $employee_profile = EmpEmployees::with('user')->where('employee_id', '=',  $emp_user)->first();
       // dd($sess);

        $profile = array(
            'full_name' => $employee_profile->emp_f_name." ".$employee_profile->emp_l_name,
            'emp_imagefile' => $employee_profile->emp_imagefile,
        );


        $sql="SELECT
        *
    FROM
        (SELECT 
        co_id,
        GET_GRADING_STATUS(co_id) as grading_status
    FROM
        courses_offered
        LEFT JOIN faculty_courses ON (
            CASE
                WHEN
                    parent_co_id IS NULL
                OR
                    parent_co_id = 0
                THEN co_id
                ELSE parent_co_id
            END
        ) = fc_co_id
        LEFT JOIN emp_employees ON fc_employee_id = employee_id
    WHERE
            fc_employee_id IS NOT NULL
        AND
            co_status = 'E'
        AND
            fc_status='E'  
        AND
            (parent_co_id is null or parent_co_id=0) 
        AND co_sess_id >=150
        AND emp_email='$employee_profile->emp_email'
        AND fc_remarks is null 
        AND fc_evaluation_status=1
        AND rownum=1 ) where grading_status > 0";
        $rec_ora = DB::select($sql);
        //echo $sql;exit;
        
        $co_id = (count($rec_ora) == 0 ? '' : $rec_ora[0]->co_id);
        
        // Determine which session to use for initial view selection
        $initial_session = request()->get('session');
        if(!$initial_session) {
            if($co_id != '') {
                $course_offered = DB::table('courses_offered')->where('co_id', $co_id)->first();
                if($course_offered) {
                    $initial_session = $course_offered->co_sess_id;
                }
            }
            if(!$initial_session) {
                $initial_session = $sess->active_session;
            }
        }
            
        $sessions = \App::call('App\Http\Controllers\Grader\AcademicSessionController@retrieve');
        
        // Check if URL has view=f2025 parameter (explicit override)
        // OR if we are on session >= 165 and NO view parameter is specified
        $view_param = request()->get('view');
        if($view_param == 'f2025' || ($view_param == '' && $initial_session >= 165)) {
            return view('grader.reports.faculty.faculty_reports_list_wise_f2025',compact('sessions','emp_user','profile','co_id'));
        }
        
        // Otherwise: Old view
        return view('grader.reports.faculty.faculty_reports_list_wise',compact('sessions','emp_user','profile','co_id'));

    }

    ////// attandence faculty  report listing start
    public function courseAttendance(){

        $loginUser = Auth::user();
        //  $sess = new sessions();
         // $sess->startSession();
          $profile = new EmpEmployees();
          $emp_user = $loginUser::find($loginUser->id)->employees->employee_id;
    
          $sessions = \App::call('App\Http\Controllers\Grader\AcademicSessionController@retrieve');
        return view('grader.reports.faculty_attendance',compact('sessions','emp_user'));
    
    }

////// attandence faculty  report end 

    function getResultscourse(Request $request){
        // Check if session >= 165 (F2025 onwards), redirect to new function
        $session = $request->get('session');
        if($session != '' && intval($session) >= 165) {
            return $this->getResultscourseFromF2025($request);
        }

        // Old logic for sessions < 165
        $whereStr = ' 1 = 1 ';
        $whereBycourse = ' 1 = 1 ';
        $sess = new sessions();
        $sess->startSession();
        
        $sess_id = $sess->active_session;
        if( $request->get('session') != '' ){
            $whereStr             .= ' AND fc_co_sess_id = '.$request->get('session');
            $whereBycourse.= ' AND fc_co_sess_id = '.$request->get('session');
        }
        if( $request->get('teacher') != '' ){
            $whereStr .= ' AND fc_employee_id = '.$request->get('teacher');
            $whereBycourse.= ' AND fc_employee_id = '.$request->get('teacher');
        }

        if( $request->get('course') != '' ){
            $whereStr .= ' AND co_id = '.$request->get('course');
            $whereBycourse.= ' AND fc_co_id = '.$request->get('course');
        }else if($request->get('fc_co_id')!= ''){
            $whereStr .= ' AND co_id = '.$request->get('fc_co_id');
            $whereBycourse.= ' AND fc_co_id = '.$request->get('fc_co_id');
        }
       // $whereBycourse.= ' AND fc_co_id = 15507';
       // $whereStr .= ' AND co_id=15507';
        $ora = "SELECT
        *
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
                        fe_co_id = co_id
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
                (select fc_remarks from faculty_courses where 1=1 and $whereBycourse and rownum=1 )  as remarks,
                (
                    SELECT
                        COUNT(*)
                    FROM
                        student_courses
                    WHERE
                        sc_status = 'A'
                        and sc_cms_course_co_id IN (
                            SELECT
                                courses_offered.co_id
                            FROM
                                courses_offered
                            WHERE
                                (
                                        courses_offered.co_id = fc_co_id
                                    -- OR
                                    --     courses_offered.parent_co_id = fc_co_id
                                )

                        )
                )AS total_students,
                CO_ATTENDANCE_PERCENTAGE,
                'N/A' as co_attendance_percentage_date,
				 GET_GRADING_STATUS(fc_co_id) as grading_status

            FROM
                faculty_courses
                LEFT JOIN courses_offered ON fc_co_id = co_id
                LEFT JOIN emp_employees ON employee_id = faculty_courses.fc_employee_id
                LEFT JOIN academic_sessions ON co_sess_id = sess_id
                LEFT JOIN schools ON co_school_id = school_id
                LEFT JOIN departments ON co_dept_id = departments.dept_id
                LEFT JOIN courses_two ON co_course_id = course_id
            WHERE
                    fc_status = 'E'
                    and co_status='E'
                    --  AND combine_course IS NULL
                    and $whereStr
                    and fc_evaluation_status=1
        ) where grading_status > 0";


        //echo  $ora;exit();
       $rec_ora = DB::select($ora);

       if($request->get('result-for') == ''){
        return response()->json(['data' => $rec_ora, 'status'=> true, 'message' => count($rec_ora).' record(s) found!']);
    }

    $content = view('grader.reports.faculty.faculty_evaluation_teacher',['teacher_evaluation'=>$rec_ora])->render();
    response($content)->header('Content-Type', 'application/pdf')->send();
    exit;

    }


    function getCoursesByfaculty(Request $request){
        $tid        =   $request->get('id');
        $sess_id    =   $request->get('sess_id');
        $empEmail = '';
        if(($tid != 0) && ($tid != '')){
            $res        =   EmpEmployees::where('employee_id',$tid)->first();
            $empEmail = $res->emp_email;
        }

        $fc         =   new FacultyCourses;
        $query = "SELECT DISTINCT
                        fc_employee_id,
                        emp_f_name,
                        emp_employees.prefix
                         || ' '
                         || emp_f_name
                         || ' '
                         || emp_l_name AS emp_full_name,
                        emp_email,
                        co_id,
                        co_sess_id,
                        co_school_id,
                        co_dept_id,
                        co_shift_id,
                        co_course_id,
                        course_code,
                        course_cd_name,
                        co_section,
                        co_course_cr
                    FROM
                        courses_offered
                        LEFT JOIN courses_two ON course_id = co_course_id
                        LEFT JOIN faculty_courses ON co_id = fc_co_id
                        LEFT JOIN emp_employees ON fc_employee_id = employee_id
                    WHERE
                            fc_employee_id IS NOT NULL
                        AND
                            co_status = 'E'
                        AND
                            fc_status='E'  
                            AND combine_course is null ";
                        
                        // -- AND
                        // --     (parent_co_id is null or parent_co_id=0) ";

        if ($sess_id) {
            $query .= " AND co_sess_id = " . $sess_id;
        }

        if ($empEmail) {
            $query .= " AND emp_email = '" . $empEmail . "'";
        }

        //echo $query;exit();
        $result = DB::select($query);
        return response()->json(['data' => $result, 'status'=> true, 'message' => count($result).' record(s) found!']);

     }

     function remarksByfaculty(Request $request){
        $course        =   $request->get('course_id');
        $remarks    =   $request->get('remarks');
        $sess = new sessions();
        $sess->startSession();
       $Date=Carbon::now()->toDateTimeString();
          $sel="update faculty_courses set fc_remarks='$remarks',COURSE_EVALUATION='$Date' where fc_co_id=$course and fc_employee_id=$sess->emp_user_id ";
         $rec_ora = DB::insert($sel);
        return response()->json(['data' => $rec_ora, 'status'=> true, 'message' => 'Update Successfully']);

       
     }

     // New function for F2025 onwards (session >= 165) with new evaluation form
     function getResultscourseFromF2025(Request $request){
        $whereStr = ' 1 = 1 ';
        $whereBycourse = ' 1 = 1 ';
        $sess = new sessions();
        $sess->startSession();
        
        $sess_id = $sess->active_session;
        if( $request->get('session') != '' ){
            $whereStr             .= ' AND fc_co_sess_id = '.$request->get('session');
            $whereBycourse.= ' AND fc_co_sess_id = '.$request->get('session');
        }
        if( $request->get('teacher') != '' ){
            $whereStr .= ' AND fc_employee_id = '.$request->get('teacher');
            $whereBycourse.= ' AND fc_employee_id = '.$request->get('teacher');
        }

        if( $request->get('course') != '' ){
            $whereStr .= ' AND co_id = '.$request->get('course');
            $whereBycourse.= ' AND fc_co_id = '.$request->get('course');
        }else if($request->get('fc_co_id')!= ''){
            $whereStr .= ' AND co_id = '.$request->get('fc_co_id');
            $whereBycourse.= ' AND fc_co_id = '.$request->get('fc_co_id');
        }

        $ora = "SELECT
        *
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
                calculate_faculty_evaluation_average(fc_co_id, 'overall') as overall_rating,
                calculate_faculty_evaluation_average(fc_co_id, 'core') as quality_of_teaching,
                calculate_faculty_evaluation_average(fc_co_id, 'support') as course_evaluation,
                calculate_faculty_evaluation_average(fc_co_id, 'overall') as overall_summary,
                calculate_faculty_evaluation_average(fc_co_id, 'core') as core_summary,
                calculate_faculty_evaluation_average(fc_co_id, 'support') as support_summary,
                0 AS exam_and_assessment,
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
                (select fc_remarks from faculty_courses where 1=1 and $whereBycourse and rownum=1 )  as remarks,
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
                'N/A' as co_attendance_percentage_date,
				 GET_GRADING_STATUS(fc_co_id) as grading_status

            FROM
                faculty_courses
                LEFT JOIN courses_offered ON fc_co_id = co_id
                LEFT JOIN emp_employees ON employee_id = faculty_courses.fc_employee_id
                LEFT JOIN academic_sessions ON co_sess_id = sess_id
                LEFT JOIN schools ON co_school_id = school_id
                LEFT JOIN departments ON co_dept_id = departments.dept_id
                LEFT JOIN courses_two ON co_course_id = course_id
            WHERE
                    fc_status = 'E'
                    and co_status='E'
                    --  AND combine_course IS NULL
                    and $whereStr
                    and fc_evaluation_status=1
        ) where grading_status > 0";


        //echo  $ora;exit();
       $rec_ora = DB::select($ora);

       if($request->get('result-for') == ''){
        return response()->json(['data' => $rec_ora, 'status'=> true, 'message' => count($rec_ora).' record(s) found!']);
    }

    $content = view('grader.reports.faculty.faculty_evaluation_teacher_f2025',['teacher_evaluation'=>$rec_ora])->render();
    response($content)->header('Content-Type', 'application/pdf')->send();
    exit;

     }
}
