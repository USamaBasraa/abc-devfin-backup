<?php
/**
 * Created by PhpStorm.
 * User: bilal.saleem
 * Date: 22/01/2020
 * Time: 10:38 AM
 */
namespace App\Http\Controllers\Grader;

use App\Http\Controllers\Controller;
use DB;

class CourseMarksSummaryReportController extends Controller
{
    public function course_assessment_summary_report($co_id){

        $sql_grading = "select assessment_name
		, case 
		when cc_method_id = 1 then 'Equal Distribution'
		when cc_method_id = 2 then 'Student Wise'
		when cc_method_id = 3 then 'Average Wise'
		when cc_method_id = 4 then 'Instructior Selection'
		end as method_name
		, (select count(*) from course_criteria_details 
		where co_id =course_criterias.co_id 
		and assessment_id =course_criterias.assessment_id) as total_enter
		, course_criterias.best_of_no
		, course_criterias.cc_weightage
		
		
		from course_criterias
		LEFT JOIN ASSESSMENTS on course_criterias.ASSESSMENT_ID=ASSESSMENTS.id
		where course_criterias.co_id = $co_id";

        $res_grading =  DB::select($sql_grading);

        $assess_weightage = "select assessment_name,assessment_short_name
		, course_criterias.cc_weightage
		from course_criterias
		LEFT JOIN ASSESSMENTS on course_criterias.ASSESSMENT_ID=ASSESSMENTS.id
		where course_criterias.co_id = $co_id
		order by assessment_id";

        $res_assess_weightage =  DB::select($assess_weightage);

        $sql1 = "SELECT
		DISTINCT  course_cd_name||' '||course_code||' | '||CO_COURSE_CR||' | '||CO_SECTION as full_course_name,
		course_marks_summaries.co_id,assessments.assessment_short_name,
		course_marks_summaries.assessment_id,
		assessments.assessment_name,
		course_marks_summaries.total_marks,
		
		get_teachername($co_id) AS teacher_name,
		dept_name,
		GET_COURSE_GRADING_TYPE($co_id) as  grading_type,school_name,sess_name
		FROM course_marks_summaries
		LEFT JOIN assessments ON assessments.id = course_marks_summaries.assessment_id
		LEFT JOIN student_courses on course_marks_summaries.SAI_ENROLLMENT_NO = student_courses.SC_SAI_ENROLLMENT_NO and course_marks_summaries.sc_co_id = student_courses.SC_CO_ID
		LEFT JOIN courses_offered ON courses_offered.co_id = course_marks_summaries.co_id
		LEFT JOIN departments ON departments.dept_id = courses_offered.co_dept_id
		LEFT JOIN COURSES_TWO on co_course_id = course_id
		LEFT JOIN schools ON schools.school_id = courses_offered.co_school_id
		LEFT JOIN academic_sessions ON academic_sessions.sess_id = courses_offered.co_sess_id
		WHERE course_marks_summaries.co_id = $co_id
		and sc_status='A'
		";
        if(isset($_REQUEST['assessment_id']) && $_REQUEST['assessment_id']!="" && $_REQUEST['assessment_id']!="undefined"){
            $sql1 .= " and assessment_id = ".$_REQUEST['assessment_id'] ;
        }
        $sql1 .= " ORDER BY assessment_id";

        $res_sql1 =  DB::select($sql1);


        $sqlAppacdemicprof = "SELECT sc_status,sai_student_status,students_apply_info.sai_id,courses_offered.co_id
,parent_co_id,student_courses.sc_sai_enrollment_no AS 		sai_enrollment_no,
	assessment_name,
	assessment_short_name,
	total_marks,
	obtained_marks,
	(SELECT mh_marks
	FROM marks_history
	WHERE marks_history.mh_sai_enrollment_no = course_marks_summaries.sai_enrollment_no
	AND marks_history.mh_co_id               = course_marks_summaries.sc_co_id
	) AS obtained_marks_history,
	concat((SELECT gh_grade
	FROM grade_history
	WHERE grade_history.gh_sai_enrollment_no = course_marks_summaries.sai_enrollment_no
	AND grade_history.gh_co_id               = course_marks_summaries.sc_co_id
	),'') as abc,
	(select gh_grading_type from grade_history
   	WHERE grade_history.gh_sai_enrollment_no = course_marks_summaries.sai_enrollment_no
  	AND grade_history.gh_co_id               = course_marks_summaries.sc_co_id) as grader,
  	gh_grade AS student_grade,
	spi_name,
	eg_id,student_courses.sc_co_id,
    GET_COURSE_NAME(student_courses.sc_co_id) as course_name_studentwise
	FROM student_courses
	LEFT JOIN students_apply_info
	ON student_courses.sc_sai_enrollment_no = students_apply_info.sai_enrollment_no
	LEFT JOIN students_personal_info
	ON sai_spi_id = spi_id
	LEFT JOIN courses_offered
	ON sc_co_id = courses_offered.co_id
	LEFT JOIN COURSE_CRITERIAS ON 
    (CASE WHEN PARENT_CO_ID is null then courses_offered.co_id else parent_co_id end) = COURSE_CRITERIAS.CO_ID
	LEFT JOIN course_marks_summaries
	ON student_courses.sc_sai_enrollment_no = course_marks_summaries.sai_enrollment_no
	AND student_courses.sc_co_id            = course_marks_summaries.sc_co_id
	AND COURSE_CRITERIAS.ASSESSMENT_ID =  course_marks_summaries.ASSESSMENT_ID
	LEFT JOIN assessments
	ON COURSE_CRITERIAS.assessment_id       = assessments.id
	  LEFT JOIN grade_history on grade_history.gh_co_id = student_courses.sc_co_id 
  and grade_history.gh_sai_enrollment_no = student_courses.sc_sai_enrollment_no
  LEFT JOIN EXAM_GRADES ON GH_GRADE = EG_GRADE
	WHERE 1                                        = 1
	and sc_enrollment_status=1
	AND co_status                                  = 'E'
	AND ( courses_offered.co_id                    = $co_id
	OR courses_offered.parent_co_id                = $co_id )
	
	 ";

        if(isset($_REQUEST['assessment_id']) && $_REQUEST['assessment_id']!="" && $_REQUEST['assessment_id']!="undefined"){
            $sqlAppacdemicprof .= " and course_criteria_details.assessment_id = ".$_REQUEST['assessment_id'] ;
        }
        if(isset($_REQUEST['print']) && $_REQUEST['print']=="2"){
            $sqlAppacdemicprof .= " and eg_grade is not null ";
        }
        if(isset($_REQUEST['sort']) && $_REQUEST['sort']=="2"){
            $sqlAppacdemicprof .= "  ORDER BY spi_name,sc_sai_enrollment_no,assessments.id,co_id desc ";
        }elseif(isset($_REQUEST['sort']) && $_REQUEST['sort']=="3"){
            $sqlAppacdemicprof .= "  ORDER BY sc_co_id,sc_sai_enrollment_no,assessments.id,co_id desc  ";
        }else{
            $sqlAppacdemicprof .= "  ORDER BY gh_co_course_id,sc_sai_enrollment_no,assessments.id,co_id desc ";
        }

        $res_sqlAppacdemicprof =  DB::select($sqlAppacdemicprof);
        //echo $sqlAppacdemicprof;exit;


        return view('grader.reports.assessment_summary_marks_report',
            [
                'res_grading'       =>$res_grading,
                'assess_weightage'  =>$res_assess_weightage,
                'res1'              =>$res_sql1,
                'rsAppacdemicprof'  =>$res_sqlAppacdemicprof
            ]
        );



    }
}