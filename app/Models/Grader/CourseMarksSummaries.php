<?php
/**
 * Created by PhpStorm.
 * User: Maher.asad
 * Date: 8/7/2018
 * Time: 1:45 PM
 */

namespace App\Models\Grader;
use Illuminate\Database\Eloquent\Model;
use DB;

class CourseMarksSummaries extends Model
{
    public function getMarksGradingView($co_id){
        $marksSummary = "SELECT
                                students_apply_info.sai_enrollment_no,
                                spi_name,
                                gh_grade,
                                mh_marks,
                                $co_id as co_id
                        FROM
                        student_courses
                        LEFT JOIN students_apply_info ON student_courses.sc_sai_enrollment_no = students_apply_info.sai_enrollment_no
                        LEFT JOIN students_personal_info ON sai_spi_id = spi_id
                        LEFT JOIN grade_history ON
                            student_courses.sc_sai_id = grade_history.gh_sai_id
                            AND
                            sc_co_id = gh_co_id
                            AND
                            sc_id = gh_sc_id
                        LEFT JOIN marks_history ON
                            student_courses.sc_sai_id = marks_history.mh_sai_id
                            AND
                            sc_co_id = mh_co_id
                        LEFT JOIN students_semester_record ON students_semester_record.sai_enrollment_no = student_courses.sc_sai_enrollment_no
                           and students_semester_record.sess_id=student_courses.sc_co_sess_id
                        WHERE
                        1 = 1
                        AND ssr_semester_status='Active'
                        AND (
                        students_apply_info.sai_student_status IN (
                        'Active','Expected-Graduate','Left','Struck off','Stuck-Off'
                        )
                        OR
                        students_apply_info.sai_semester_status = 'Defer'
                        ) AND (
                        students_apply_info.sai_semester_status != 'Freeze'
                        OR
                        students_apply_info.sai_semester_status IS NULL
                        ) AND
                        sc_status IN ('A','Block')
                        AND
                        student_courses.sc_enrollment_status = 1
                        AND
                        student_courses.sc_co_id IN (
                        SELECT
                        co_id
                        FROM
                        courses_offered
                        WHERE
                        co_id = $co_id
                        OR
                        parent_co_id = $co_id
                        AND
                        co_status = 'E'
                                )";
	   //  return $marksSummary;
        $marksSummary_record = DB::select($marksSummary);
        return $marksSummary_record;
    }
    public function getMarksSummaryRptg($co_id){

        $marksSummary = "SELECT
                            sc_sai_enrollment_no as sai_enrollment_no,
                            spi_name,
                            assessments.assessment_name,
                            assessments.assessment_short_name,
                            cc_weightage AS total_marks,
                            obtained_marks,
                            get_course_grading_type($co_id) AS course_grading_type,
                            round(mh_converted_marks,2) AS mh_converted_marks,
                            mh_marks,
                            gh_grade
                        FROM
                            student_courses
                            LEFT JOIN courses_offered ON student_courses.sc_co_id = courses_offered.co_id
                            LEFT JOIN students_apply_info ON student_courses.sc_sai_enrollment_no = students_apply_info.sai_enrollment_no
                            LEFT JOIN students_personal_info ON sai_api_id = api_id
                            LEFT JOIN course_criterias ON (case when parent_co_id is null then courses_offered.co_id  else parent_co_id end) = course_criterias.co_id
                            LEFT JOIN course_marks_summaries ON
                                courses_offered.co_id = course_marks_summaries.sc_co_id
                            AND
                                student_courses.sc_sai_enrollment_no = course_marks_summaries.sai_enrollment_no
                            AND
                                course_criterias.assessment_id = course_marks_summaries.ASSESSMENT_ID
                            LEFT JOIN assessments ON course_criterias.assessment_id = assessments.id
                            left join MARKS_HISTORY on student_courses.SC_SAI_ENROLLMENT_NO=marks_history.MH_SAI_ENROLLMENT_NO and student_courses.sc_co_id = marks_history.MH_CO_ID
                            LEFT JOIN GRADE_HISTORY on student_courses.SC_SAI_ENROLLMENT_NO=GRADE_HISTORY.GH_SAI_ENROLLMENT_NO and student_courses.sc_co_id = GRADE_HISTORY.GH_CO_ID
                              LEFT JOIN students_semester_record ON students_semester_record.sai_enrollment_no = student_courses.sc_sai_enrollment_no
                           and students_semester_record.sess_id=student_courses.sc_co_sess_id

                        WHERE
                                1 = 1
                                 AND ssr_semester_status='Active'
                            AND (
                                    courses_offered.co_id = '$co_id'
                                OR
                                    parent_co_id = '$co_id'
                            ) AND
                                co_status = 'E'
                            AND (
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
                            course_criterias.assessment_id";
     //  echo $marksSummary;
       // exit();
        $marksSummary_record = DB::select($marksSummary);
        return $marksSummary_record;
    }
       public function getMarksSummary($co_id){
        $marksSummary = "SELECT
     course_criterias.co_id,obtained_marks,sc_sai_enrollment_no,assessment_short_name,total_marks,assessment_name,spi_name,course_criterias.assessment_id
FROM
    student_courses
    LEFT JOIN students_apply_info ON student_courses.sc_sai_enrollment_no = students_apply_info.sai_enrollment_no
    LEFT JOIN students_personal_info ON sai_spi_id = spi_id
    LEFT JOIN courses_offered ON sc_co_id = courses_offered.co_id
    LEFT JOIN course_criterias ON (
        CASE
            WHEN (parent_co_id IS NULL or parent_co_id = 0) THEN courses_offered.co_id
            ELSE parent_co_id
        END
    ) = course_criterias.co_id
    LEFT JOIN course_marks_summaries ON        course_criterias.co_id = course_marks_summaries.co_id
    AND         course_criterias.assessment_id = course_marks_summaries.assessment_id
   AND         student_courses.sc_sai_enrollment_no = course_marks_summaries.sai_enrollment_no
    LEFT JOIN assessments ON course_criterias.assessment_id = assessments.id
    LEFT JOIN students_semester_record ON students_semester_record.sai_enrollment_no = student_courses.sc_sai_enrollment_no
    and students_semester_record.sess_id=student_courses.sc_co_sess_id
WHERE
        1 = 1 AND SC_STATUS IN ('A','Block')
		AND sc_enrollment_status=1
        AND ssr_semester_status='Active'
		and sc_sai_enrollment_no is not null
                            AND
                                (courses_offered.co_id = '$co_id' OR courses_offered.parent_co_id = '$co_id')
                            AND
                                co_status = 'E'
                            AND (
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
                             sc_sai_enrollment_no,
                             course_criterias.assessment_id";
							// return $marksSummary ;
        $marksSummary_record = DB::select($marksSummary);
        return $marksSummary_record;
   }

    public function load_student_percentage($enrollment_no,$sc_co_id){

            $student_marks_qry="select * from course_marks_summaries
                left join assessments
                on course_marks_summaries.assessment_id = assessments.id
                where sai_enrollment_no='$enrollment_no'";
                $student_actual_marks =  DB::select($student_marks_qry);
                return $student_actual_marks;

    }

}
