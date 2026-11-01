<?php
/**
 * Created by PhpStorm.
 * User: Maher Asad
 * Date: 5/14/2019
 * Time: 9:52 AM
 */
namespace App\Http\Controllers\Grader\Reports;

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


class ExamReportController extends Controller{

public function retrievelist(){
//dd('ff');
    $sessions = \App::call('App\Http\Controllers\Grader\AcademicSessionController@retrieve');
    $schools =  Schools::all();
    return view('grader.reports.exam.cgpa_summary',compact('sessions','schools'));
 
}

public function getreport(Request $request){

    $whereStr = ' 1 = 1 ';
    $sess_from=$request->get('from_session');
    $session_to=$request->get('to_session');
    $school=$request->get('school');
    $dept=$request->get('department');

    if( $request->get('school') != '' ){
        $whereStr             .= ' AND sai_sdpo_school_id = '.$request->get('school');
        
    }
    if( $request->get('department') != '' ){
        $whereStr .= ' AND sai_sdpo_dept_id = '.$request->get('department');
    
    }

    $sel="SELECT
                students_semester_record.SAI_ENROLLMENT_NO,
                students_semester_record.SSR_CGPA,
                students_semester_record.SSR_SGPA,
                Schools.SCHOOL_NAME,
                departments.DEPT_NAME,
                students_apply_info.SAI_PROG_TOTAL_CREDITS,
                students_apply_info.SAI_EARN_CREDIT,
                students_apply_info.sai_id,
                students_personal_info.spi_name,
                students_apply_info.sai_student_status,  
                programs.prog_degree_level,
                students_apply_info.sai_cgpa,
    (SELECT SUM(gh3.gh_co_course_cr)
					FROM grade_history gh3
					WHERE gh3.gh_sai_enrollment_no=students_semester_record.sai_enrollment_no
					AND gh3.gh_exemption_status   ='Yes'
				  ) AS sai_transfer_cr
        FROM
            academic_sessions
            LEFT JOIN students_semester_record ON academic_sessions.sess_id = students_semester_record.sess_id
            LEFT JOIN students_apply_info ON students_apply_info.sai_enrollment_no = students_semester_record.sai_enrollment_no
            LEFT JOIN students_personal_info ON spi_id = students_apply_info.sai_spi_id
            LEFT JOIN Schools on school_id=sai_sdpo_school_id
            LEFT JOIN departments on departments.dept_id=sai_sdpo_dept_id
            left join programs on prog_id=sai_sdpo_prog_id

        WHERE
                academic_sessions.sess_id BETWEEN  $sess_from AND $session_to
                AND
                $whereStr

                AND
                students_semester_record.ssr_status = 'E'

        ORDER BY students_semester_record.sai_enrollment_no DESC";


$rec_ses= DB::select($sel);

$que="select *from ACADEMIC_SESSIONS where sess_id between $sess_from and $session_to order by sess_id desc";
$rec_que= DB::select($que);
    return view('grader.reports.exam.cgpa_summary_report',compact('rec_ses','rec_que','sess_from','session_to'));
}

public function repeating(){

    return view('grader.reports.exam.student_repeating_list');

}

public function graduateSummaryReport(){
    $sql = "select * from (
            SELECT
                sai_enrollment_no,
                spi_name,
                school_name,
                dept_name,
                prog_name,
                (
                    SELECT
                        COUNT(DISTINCT sai_enrollment_no)
                    FROM
                        survey_evaluation_record
                    WHERE
                        lower(students_apply_info.sai_enrollment_no) = lower(survey_evaluation_record.sai_enrollment_no)
                ) as submit
            FROM
                students_apply_info
                LEFT JOIN students_personal_info ON sai_spi_id = spi_id
                LEFT JOIN schools ON sai_sdpo_school_id = school_id
                LEFT JOIN departments ON sai_sdpo_dept_id = dept_id
                LEFT JOIN programs ON sai_sdpo_prog_id = prog_id
            WHERE
                sai_student_status in ('Expected-Graduate','Convocated-19'))
                order by submit desc";
    $resSql = DB::select($sql);            

    return view('grader.reports.exam.graduate_summary_report',['res'=>$resSql]);
}


public function repeating_list(Request $request){

    //dd('dd');
    $enrollment_no=$request->get('enrollment');
    $sql="select *from students_apply_info
            left join students_personal_info on sai_spi_id=spi_id
            left join schools on school_id=sai_sdpo_school_id
            where sai_enrollment_no='".$enrollment_no."' ";
    $rec_sql= DB::select($sql);
     $query="SELECT
    --count(*),
    sess_code,
    mt.GH_CO_COURSE_ID,mt.gh_sai_enrollment_no,course_cd_name,gh_grade,gh_co_course_cr,gh_course_code,
    (select count(*) from GRADE_HISTORY st where st.GH_CO_COURSE_ID = mt.GH_CO_COURSE_ID and st.gh_sai_enrollment_no = mt.gh_sai_enrollment_no) as rec
    FROM grade_history mt
    left join academic_sessions  on mt.gh_co_sess_id=sess_id
    left join COURSES_TWO on course_id = mt.GH_CO_COURSE_ID
    where gh_sai_enrollment_no='".$enrollment_no."'";


    $rec_que= DB::select($query);
    
    if($request->get('result-for') == ''){
        return response()->json(['data' => $rec_que, 'std' => $rec_sql,'status'=> true, 'message' => count($rec_que).' record(s) found!']);
    }else if($request->get('result-for') == 'print'){
        return view('grader.reports.exam.repeating_course_report',['rowsSQL'=>$rec_que]);

    }

}

}