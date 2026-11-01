<?php
/**
 * Created by PhpStorm.
 * User: Nabeel
 * Date: 7/31/2018
 * Time: 3:21 PM
 */

namespace App\Http\Controllers\Grader;

use App\Http\Controllers\Controller as NewController;
use Illuminate\Http\Request;
use Symfony\Component\Console\Helper\Table;

use Illuminate\Support\Facades\Input;
use App\Models\Grader\CourseAttendance;
use App\Models\Grader\CourseMarksSummaries;
use App\Models\Grader\CoursesOffered;
use App\Models\Grader\CourseCriterias;
use App\Models\Grader\CourseAssessmentMarks;
use App\Models\Grader\Schools;
use App\Models\Grader\Departments;
use DB;

class ReportgeneratorController extends NewController
{
    public function index(){
       
        $sessions       = \App::call('App\Http\Controllers\Grader\AcademicSessionController@retrieve');
        $offeredFaculty = \App::call('App\Http\Controllers\Grader\CoursesOfferedController@retrieveFaculty');
        $facultyCourses =[]; // \App::call('App\Http\Controllers\Grader\FacultyCoursesController@retrieveCourses');
        $schools     = new Schools();
        $departments = new Departments();

        //dd($departments->fetchDepartments() );
        
        $student_actual_qry = "SELECT
                                   *
                                FROM
                                course_assessment_marks
                                left join ASSESSMENTS
                                on course_assessment_marks.ASSESSMENT_ID = ASSESSMENTS.id
                            WHERE
                                course_assessment_marks.co_id = 1
                                and sai_enrollment_no not in ('F2014-217')
                             ORDER BY sai_enrollment_no,assessment_id,cam_number";
        $student_actual_marks =  DB::select($student_actual_qry);
        // if($get_co_id>0){
         return view('grader/reportgenerator.reportgenerator',
            [
                'sessions'                      =>$sessions,
                'offeredFaculty'                =>$offeredFaculty,
                'student_actual_marks'         =>$student_actual_marks,
                'facultyCourses'               =>$facultyCourses,
                'schools'                      =>$schools->fetchSchools(),
                'departments'                  =>$departments->fetchDepartments()
            ]
        );
    }
    public function sms(){

        return 'pak';

    }
    public function load_data($courses_id){

      $sql_parent_co_id = "SELECT  * FROM courses_offered WHERE  co_id = $courses_id ";
      $res_parent_co_id =  DB::select($sql_parent_co_id);
      $p_co_id          =   $res_parent_co_id[0]->parent_co_id;

      $student_actual_qry = "SELECT
                                student_courses.sc_sai_enrollment_no AS sai_enrollment_no,
                                assessment_name,
                                assessment_short_name,
                                total_marks,
                                school_code,
                                school_id,
                                course_assessment_marks.assessment_id AS assess_id,
                                approval_status,
                                obtained_marks,
                                cam_grading_status,
	                            ccd_grading_status,
	                            ccd_method_id,
                                (
                                    SELECT
                                        mh_marks
                                    FROM
                                        marks_history
                                    WHERE
                                            marks_history.mh_sai_enrollment_no = course_assessment_marks.sai_enrollment_no
                                        AND
                                            marks_history.mh_co_id = course_assessment_marks.sc_co_id
                                ) AS obtained_marks_history,
                                (
                                    SELECT
                                        gh_grade
                                    FROM
                                        grade_history
                                    WHERE
                                            grade_history.gh_sai_enrollment_no = course_assessment_marks.sai_enrollment_no
                                        AND
                                            grade_history.gh_co_id = course_assessment_marks.sc_co_id
                                ) AS student_grade,
                                spi_name,
                                cam_number,
                                course_criteria_details.co_id,
                                student_courses.sc_co_id,
                                sess_name
                            FROM
                                student_courses
                                LEFT JOIN students_apply_info ON student_courses.sc_sai_enrollment_no = students_apply_info.sai_enrollment_no
                                LEFT JOIN schools ON students_apply_info.sai_sdpo_school_id = schools.school_id
                                LEFT JOIN students_personal_info ON sai_spi_id = spi_id
                                LEFT JOIN courses_offered ON sc_co_id = courses_offered.co_id
                                LEFT JOIN course_criteria_details ON (
                                    CASE
                                        WHEN parent_co_id IS NULL THEN courses_offered.co_id
                                        ELSE parent_co_id
                                    END
                                ) = course_criteria_details.co_id
                                LEFT JOIN course_assessment_marks ON
                                    course_criteria_details.id = course_assessment_marks.course_criteria_detail_id
                                AND
                                    student_courses.sc_sai_enrollment_no = course_assessment_marks.sai_enrollment_no
                                LEFT JOIN assessments ON course_criteria_details.assessment_id = assessments.id
                                LEFT JOIN academic_sessions ON courses_offered.co_sess_id = academic_sessions.sess_id
                            WHERE
                                    1 = 1
                                  
                                AND
                                    sc_status = 'A'
                                AND
                                    co_status = 'E' ";

                                if($p_co_id!=''){
                                    $student_actual_qry .="   AND

                                    sc_sai_enrollment_no in (
                                        select sai_enrollment_no from COURSE_ASSESSMENT_MARKS where sc_co_id = $courses_id
                                    )";
                                }else{
                                    $student_actual_qry .="   AND
                                    sc_sai_enrollment_no in (
                                        select sai_enrollment_no from COURSE_ASSESSMENT_MARKS where co_id = $courses_id
                                    )";

                                }

                    $student_actual_qry .=" AND (
                                        courses_offered.co_id = $courses_id
                                    OR
                                        courses_offered.parent_co_id = $courses_id
                                ) AND (
                                        students_apply_info.sai_student_status IN (
                                            'Active','Expected-Graduate','Left','Struck off','Stuck-Off'
                                        )
                                    OR
                                        students_apply_info.sai_semester_status = 'Defer'
                                ) AND (
                                        students_apply_info.sai_semester_status != 'Freeze'
                                    OR
                                        students_apply_info.sai_semester_status IS NULL
                                )
                                
                            ORDER BY
                                spi_name,
                                student_courses.sc_sai_enrollment_no,
                                assessments.id,
                                course_criteria_details.ccd_number";
       //echo $student_actual_qry;
        //exit();
        $student_actual_marks =  DB::select($student_actual_qry);

        $course_criteria = new CourseCriterias();
        $criteria = $course_criteria->fetchCoursesCriteriaRptg($courses_id);

        // print_r(  $student_actual_marks);exit();
        return view('grader/reportgenerator.course_assessment_marks',
            [
                'student_actual_marks'      =>$student_actual_marks,
                'criterias'                 =>$criteria
            ]
        );
    }
    public function load_data2($courses_id){
        $objCourseAssessmentMarks = new CourseAssessmentMarks();
        $student_actual_marks       = $objCourseAssessmentMarks->getCourseAssessmentMarks($courses_id);
        $student_actual_marks_header = $objCourseAssessmentMarks->getCourseAssessmentMarksHeader($courses_id);

        $sql_school_id = "select co_school_id from COURSES_OFFERED where CO_ID =".$courses_id;
        $obj_school_id =DB::select($sql_school_id);


        return view('grader.reportgenerator.course_assessment_marks2',
            [
                'student_actual_marks'      =>$student_actual_marks,
                'student_actual_marks_header'      =>$student_actual_marks_header,
                'school_id' => $obj_school_id[0]->co_school_id
            ]
        );
    }

   public function student_courses($enrollment_no,$sess_id,$school_id){
                        $student_actual_qry = "SELECT
                                                course_code,
                                                course_cd_name,
                                                sc_pc_section,
                                                sc_pc_course_cr,
                                                sc_sai_enrollment_no,
                                                sc_co_id,
                                                (
                                                    CASE
                                                        WHEN courses_offered.parent_co_id IS NULL THEN student_courses.sc_co_id
                                                        ELSE courses_offered.parent_co_id
                                                    END
                                                ) AS parent_co_id
                                            FROM
                                                student_courses
                                                LEFT JOIN courses_two ON course_id = sc_course_id
                                                LEFT JOIN courses_offered ON sc_co_id = co_id
                                           WHERE
                                                sc_sai_enrollment_no = '$enrollment_no'
                                            and 
                                                sc_co_sess_id = $sess_id
                                            and
                                                sc_co_school_id = $school_id ";
        //return $student_actual_qry;exit();
        $student_actual_marks =  DB::select($student_actual_qry);
        return view('grader.reportgenerator.student_course_marks',
            [
                'student_courses'      =>$student_actual_marks,
            ]
        );
    }
    public function student_profile($enrollment_no){
        $student_profile_query = "SELECT
                                        *
                                    FROM
                                         students_apply_info 
                                        LEFT JOIN students_personal_info ON students_apply_info.sai_spi_id = students_personal_info.spi_id
                                        LEFT JOIN academic_sessions ON sess_id = sai_sdpo_sess_id
                                        LEFT JOIN schools ON school_id = sai_sdpo_school_id
                                        LEFT JOIN departments ON dept_id = sai_sdpo_dept_id
                                        LEFT JOIN programs ON prog_id = sai_sdpo_prog_id
                                        LEFT JOIN shifts ON shift_id = sai_sdpo_shift_id
                                    WHERE
                                            sai_enrollment_no = '$enrollment_no'";
        $student_profile_data =  DB::select($student_profile_query);
        return $student_profile_data;

    }
    public function load_faculty($get_co_id){
        $course_faculty_query = "SELECT
                                    prefix,
                                    emp_f_name,
                                    emp_l_name,
                                    emp_imagefile,
                                    school_name,
                                    dept_name,
                                    CASE emp_employees.emp_type
                                    WHEN '1' THEN 'Staff'
                                    WHEN '2' THEN 'Faculty'
                                    WHEN '3' THEN 'Visiting Faculty'
                                    WHEN '4' THEN 'Dean'
                                    WHEN '5' THEN 'HOD'
                                    ELSE ''
                                    END
                                    AS emp_type
                                    FROM
                                    faculty_courses
                                    LEFT JOIN emp_employees ON faculty_courses.fc_employee_id = emp_employees.employee_id
                                    LEFT JOIN schools ON school_id = emp_employees.emp_school_id
                                    LEFT JOIN departments ON dept_id = emp_employees.emp_dept_id
                                    WHERE
                                    1 = 1
                                    and fc_co_id in (
                                    select co_id from COURSES_OFFERED where co_id = $get_co_id
                                    union all
                                    select parent_co_id from COURSES_OFFERED where co_id = $get_co_id
                                    ) ";
        $course_faculty_data =  DB::select($course_faculty_query);
        return view('grader.reportgenerator.faculty_profile',['course_faculty_data'=>$course_faculty_data]);
        //return $course_faculty_data;
        //print_r($course_faculty_data);
    }
   public function load_student_marks($enrollment_no,$parent_co_id,$sc_co_id){

        //return $this->load_student_percentage($enrollment_no,$parent_co_id);
        $courseOffered        = new CoursesOffered();
        $courseOfferedDetails = $courseOffered->fetch_offered_course_details($sc_co_id);
        $student_marks_qry2="SELECT
            *
        FROM
    course_assessment_marks
    LEFT JOIN courses_offered ON course_assessment_marks.co_id = courses_offered.co_id
    LEFT JOIN courses_two ON course_id = co_course_id
    LEFT JOIN assessments ON course_assessment_marks.assessment_id = assessments.id
    WHERE
        course_assessment_marks.sai_enrollment_no = '$enrollment_no'
        and sc_co_id = '$sc_co_id'";

        $student_marks_qry="SELECT
    assessment_short_name,
    obtained_marks,
    total_marks,
    (
        SELECT
            COUNT(*)
        FROM
            course_criteria_details
        WHERE
                course_criteria_details.co_id = course_assessment_marks.co_id
            AND
                assessment_id = assessments.id
    ) AS total_no,
        CASE
            WHEN cc_method_id = 1   THEN 'Equal Distribution'
            WHEN cc_method_id = 2   THEN 'Student Wise Best'
            WHEN cc_method_id = 3   THEN 'Assessment Wise Best'
            WHEN cc_method_id = 4   THEN 'Instructor Selection'
        END
    AS method_name,
    course_criterias.best_of_no,
    course_assessment_marks.sai_enrollment_no,
    course_criterias.cc_weightage,
   assessments.Assessment_name,
   course_assessment_marks.CAM_GRADING_STATUS,
   concat(ccd_grading_status,CAM_GRADING_STATUS) as marks_status
   
FROM
    
    course_criterias
    LEFT JOIN course_assessment_marks ON
        course_criterias.co_id = course_assessment_marks.co_id
    AND
        course_criterias.assessment_id = course_assessment_marks.assessment_id
         left join COURSE_CRITERIA_DETAILS on course_assessment_marks.COURSE_CRITERIA_DETAIL_ID = COURSE_CRITERIA_DETAILS.id
    LEFT JOIN courses_offered ON course_assessment_marks.co_id = courses_offered.co_id
    LEFT JOIN courses_two ON course_id = co_course_id
    LEFT JOIN assessments ON course_assessment_marks.assessment_id = assessments.id
WHERE
        course_assessment_marks.sai_enrollment_no = '$enrollment_no'
    AND
        sc_co_id = $sc_co_id order by assessments.id";
         $student_actual_marks =  DB::select($student_marks_qry);
        // print_r($this->load_student_percentage($enrollment_no,$sc_co_id));exit();
     //  return $this->load_student_percentage($enrollment_no,$sc_co_id);exit();
          return view('grader.reportgenerator.course_student_raw_marks',
                [
                    'student_profile'               =>$this->student_profile($enrollment_no),
                    'student_actual_marks'          =>$student_actual_marks,
                    'student_percentage_marks'      =>$this->load_student_percentage($enrollment_no,$parent_co_id),
                    'load_student_attendance'       =>$this->load_student_attendance($parent_co_id,$sc_co_id,$enrollment_no),
                    'fetch_offered_course_details'  =>$courseOfferedDetails,
                ]
            );
    }
    public function load_student_percentage($enrollment_no,$sc_co_id){

         $student_marks_qry="SELECT
                            assessment_name,
                            obtained_marks,
                            total_marks,
                            co_id,
                            (
                                SELECT
                                    gh_grade
                                FROM
                                    grade_history
                                    LEFT JOIN courses_offered ON gh_co_id = co_id
                                WHERE
                                        gh_sai_enrollment_no = sai_enrollment_no
                                    AND (
                                            gh_co_id = '$sc_co_id'
                                        OR
                                            parent_co_id = '$sc_co_id'
                                    ) AND
                                        ROWNUM = 1
                            ) AS gh_grade
                        FROM
                            course_marks_summaries
    LEFT JOIN assessments ON course_marks_summaries.assessment_id = assessments.id
                            where 1=1
                             and co_id = '$sc_co_id' ";
        if($enrollment_no!="" && $enrollment_no!="null"){
            $student_marks_qry.= " and sai_enrollment_no='$enrollment_no' ";
        }
            $student_marks_qry.="      order by assessments.id ";
      return  $student_actual_marks =  DB::select($student_marks_qry);
       // return $student_actual_marks;
                           // print_r($student_actual_marks);
    }
    public function load_student_attendance($parent_co_id,$sc_co_id,$enrollment_no){
        $student_course_attendance = new CourseAttendance();
        $attendance_json = $student_course_attendance->getStudentCourseAttendance($parent_co_id,$sc_co_id,$enrollment_no);
        return $attendance_json;
    }
    public function courseMarksSummaries($co_id){


        $courseMarksSummaries = new CourseMarksSummaries();
        $courseMarkSummary = $courseMarksSummaries->getMarksSummary($co_id);
        $couseMarksSummaryRptg = $courseMarksSummaries->getMarksSummaryRptg($co_id);

        $student_marks_qry="SELECT
                                   *
                                FROM
                                course_assessment_marks
                                left join ASSESSMENTS
                                on course_assessment_marks.ASSESSMENT_ID = ASSESSMENTS.id
                            WHERE
                                1=1
                                AND co_id=".$co_id."
                               ORDER BY sai_enrollment_no,assessment_id,cam_number ";
        $student_actual_marks =  DB::select($student_marks_qry);
       // echo $student_marks_qry;exit;

        $course_criteria = new CourseCriterias();
        $criteria = $course_criteria->fetchCoursesCriteriaRptg($co_id);

        return view('grader.reportgenerator.student_course_marks_summary',
            [
                'courseMarksSummaries'=> $couseMarksSummaryRptg,
                'student_actual_marks'=> $student_actual_marks,
                'criteria_summary'=> $criteria

            ]
        );
    }

    public function courseMarksSummaries2($co_id){

        $courseMarksSummaries = new CourseMarksSummaries();
        $courseMarkSummary = $courseMarksSummaries->getMarksSummary($co_id);

        return view('grader.reportgenerator.student_course_marks_summary2',
            [
                'courseMarksSummaries'=> $courseMarkSummary,
            ]
        );
    }
    public function GradingMarksSummaries($co_id){


        $courseMarksSummaries = new CourseMarksSummaries();
        $courseMarkSummary = $courseMarksSummaries->getMarksSummary($co_id);
       //  return $courseMarkSummary;exit();
        //print_r($courseMarksSummaries);exit();

       // dd($courseMarkSummary);
        $student_marks_qry="SELECT
                                   *
                                FROM
                                course_assessment_marks
                                left join ASSESSMENTS
                                on course_assessment_marks.ASSESSMENT_ID = ASSESSMENTS.id
                            WHERE
                                1=1
                                AND co_id=".$co_id."
                               ORDER BY sai_enrollment_no,assessment_id,cam_number ";
        $student_actual_marks =  DB::select($student_marks_qry);

        $sqlCountGradeHistory="SELECT
                                    COUNT(*) gh_count
                                FROM
                                    grade_history
                                WHERE
                                        gh_co_id     
                                                in (
                                        select co_id from courses_offered where co_id=".$co_id." 
                                        or parent_co_id = ".$co_id."
                                        )
                                    AND
                                        gh_grade != 'W'";
        $resCountGradeHistory   =  DB::select($sqlCountGradeHistory);
        $arrayCountGradeHistory = json_decode(json_encode($resCountGradeHistory),true);

        return view('grader.grading.grading_marks_summaries',
            [
                'courseMarksSummaries'  => $courseMarkSummary,
                'student_actual_marks'  => $student_actual_marks,
                'grade_history_entry'   => $arrayCountGradeHistory,

            ]
        );
    }
    public function load_student_profile($enrollment_no)
    {
        return view('student.student_profile',
            [
                'student_profile' =>$this->student_profile($enrollment_no),
            ]
        );
    }
    public function load_criteria($courses_id)
    {
        $course_criteria = new CourseCriterias();
        $criteria = $course_criteria->fetchCoursesCriteriaRptg($courses_id);
        return view('grader/reportgenerator.rpg_course_criteria',
            [
                'criteria'      =>$criteria
            ]
        );
    }
}