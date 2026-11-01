<?php
/**
 * Created by PhpStorm.
 * User: Maher Asad
 * Date: 5/14/2019
 * Time: 9:52 AM
 */

namespace App\Http\Controllers\Grader;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Symfony\Component\Console\Helper\Table;
use DB;
use Illuminate\Support\Facades\Input;


class ReportsController extends Controller
{
    public function course_assessment_report($co_id){

        $sessions       = \App::call('App\Http\Controllers\Grader\AcademicSessionController@retrieve');
        $sql_grading    = "SELECT
                                assessment_name,
                                CASE
                                    WHEN cc_method_id = 1 THEN 'Equal Distribution'
                                    WHEN cc_method_id = 2 THEN 'Student Wise'
                                    WHEN cc_method_id = 3 THEN 'Average Wise'
                                    WHEN cc_method_id = 4 THEN 'Instructior Selection'
                                END
                                AS method_name,
                                (
                                    SELECT
                                        COUNT(*)
                                    FROM
                                        course_criteria_details
                                    WHERE
                                            co_id = course_criterias.co_id
                                        AND
                                            assessment_id = course_criterias.assessment_id
                                ) AS total_enter,
                                course_criterias.best_of_no,
                                course_criterias.cc_weightage
                            FROM
                                course_criterias
                            LEFT JOIN ASSESSMENTS on course_criterias.ASSESSMENT_ID=ASSESSMENTS.id
                            WHERE course_criterias.co_id = $co_id";
        $res_grading =  DB::select($sql_grading);

        $sql1   = "SELECT DISTINCT
                        course_assessment_marks.co_id,
                        assessments.assessment_short_name,
                        course_assessment_marks.assessment_id,
                        assessments.assessment_name,
                        course_assessment_marks.total_marks,
                        cam_number,
                        get_teachername($co_id) AS teacher_name,
                        dept_name,
                        school_name,
                        sess_name
                    FROM
                    course_assessment_marks
                    LEFT JOIN assessments ON assessments.id = course_assessment_marks.assessment_id
                    LEFT JOIN student_courses ON
                        course_assessment_marks.sai_enrollment_no = student_courses.sc_sai_enrollment_no
                        AND
                        course_assessment_marks.sc_co_id = student_courses.sc_co_id
                    LEFT JOIN courses_offered ON courses_offered.co_id = course_assessment_marks.co_id
                    LEFT JOIN departments ON departments.dept_id = courses_offered.co_dept_id
                    LEFT JOIN schools ON schools.school_id = courses_offered.co_school_id
                    LEFT JOIN academic_sessions ON academic_sessions.sess_id = courses_offered.co_sess_id
                    WHERE course_assessment_marks.co_id = $co_id
                    AND sc_status='A'";
        $sql1 .= " ORDER BY assessment_id,cam_number";
        $res1  =  DB::select($sql1);

        $sqlAppacdemicprof = "SELECT
                                student_courses.sc_sai_enrollment_no AS sai_enrollment_no,
                                assessment_name,
                                assessment_short_name,
                                total_marks,
                                obtained_marks,
                                ccd_status,
                                course_cd_name || '-' ||  co_course_cr || '-' || co_section as course_name_2,
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
                                 (SELECT gh_grading_type
                                  FROM grade_history
                                  WHERE grade_history.gh_sai_enrollment_no = course_assessment_marks.sai_enrollment_no
                                  AND grade_history.gh_co_id               = course_assessment_marks.sc_co_id
                                  ) AS grader,
                                spi_name,
                                cam_number,
                                cam_status,
                                cam_grading_status,
	                            ccd_grading_status,
                                get_course_name((CASE
                                WHEN courses_offered.parent_co_id is null  THEN courses_offered.co_id                                
                                ELSE courses_offered.parent_co_id END)) as course_name,
                                (SELECT count(*)
                                FROM grade_history
                                WHERE grade_history.gh_co_id = courses_offered.co_id
                                AND grade_history.gh_co_course_id               = courses_offered.co_course_id
                                and gh_grade not in ('W')
                                )as graded
                            FROM
                                student_courses
                                LEFT JOIN students_apply_info ON student_courses.sc_sai_enrollment_no = students_apply_info.sai_enrollment_no
                                LEFT JOIN students_personal_info ON sai_spi_id = spi_id
                                LEFT JOIN courses_offered ON sc_co_id = courses_offered.co_id
                                LEFT JOIN courses_two ON courses_two.course_id = courses_offered.co_course_id
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
                            WHERE
                                    1 = 1
                                AND
                                    sc_status = 'A'
                                AND
                                    co_status = 'E'
                                AND (
                                        courses_offered.co_id = $co_id
                                    OR
                                        courses_offered.parent_co_id = $co_id
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
                                ) ";
        $sqlAppacdemicprof .= "  ORDER BY TRIM(lower(spi_name)),student_courses.sc_sai_enrollment_no,assessments.id,cam_number ";
    //    echo $sqlAppacdemicprof;
    //   exit();
        $rsAppacdemicprof = DB::select($sqlAppacdemicprof);


        return view('grader.reports.assessment_marks_report',
            [
                'sessions'                   =>$sessions,
                'rsAppacdemicprof'           =>$rsAppacdemicprof,
                'res1'                       =>$res1,
                'res_grading'                =>$res_grading
            ]
        );
    }
    public function enrolledStudents($co_id){

        $query = "SELECT
        co.co_id,
        co.co_course_id,
        co.co_section,
        co.co_course_cr,
        co.parent_co_id,
        (case when  co.parent_co_id is null then co.co_id else  co.parent_co_id end) as main_co_id,
        sc.sc_id,
        sc.sc_co_id,
        sai.sai_id,
        sai.sai_enrollment_no,
        spi.spi_name,
        '' cam_id,
        '' obtained_marks,
        '' total_marks,
        '' cam_details,
        '' cam_marks,
        get_course_name(sc_co_id) as course_name,
        get_teachername(sc_co_id) as teacher_name,
        sc.attendance_percentage ,
        sai_student_status,
        sc_status  ,
        sai_remarks,
        ssr_semester_status
        
    FROM
        student_courses sc
         LEFT JOIN students_semester_record ON
        sc.sc_sai_enrollment_no = students_semester_record.sai_enrollment_no
        AND
        sc.sc_co_sess_id = students_semester_record.sess_id
        LEFT JOIN courses_offered co ON sc_co_id = co.co_id
        LEFT JOIN courses_two ON sc_course_id = course_id
        LEFT JOIN students_apply_info sai ON sc.sc_sai_enrollment_no = sai.sai_enrollment_no
        LEFT JOIN students_personal_info spi ON sai.sai_spi_id = spi.spi_id
    WHERE
          
              students_semester_record.ssr_semester_status = 'Active'
             AND sc.sc_enrollment_status = 1
            AND (
                    sai.sai_student_status IN (
                        'Active','Expected-Graduate','Left','Struck off','Stuck-Off'
                    )
                OR
                    sai.sai_semester_status = 'Defer'
            )
        AND (
                co.co_id = ".$co_id."
            OR
                co.parent_co_id = ".$co_id."
        )
        ORDER BY LOWER(spi.spi_name)";


//    echo $query;exit();

$rsAppacdemicprof =DB::select($query);


        return view('grader.reports.course_enrolled_students',
            [
                'rsAppacdemicprof'           =>$rsAppacdemicprof
            ]
        );
    }
	public function course_content($co_id){

        //        $course_content =  "SELECT
        //        co_course_cr,
        //        tt_class_date,
        //        get_teachername(mtt_co_id),
        //        get_course_name(co_id).
        //    FROM
        //        master_timetable
        //        left join courses_offered on  co_id = mtt_co_id 
        //    WHERE
        //        mtt_co_id = 18167
        //        AND tt_status = 'E'
        //    ORDER BY
        //        tt_class_date"  ;
        
        
                $sql_lectures="SELECT
                tt_id,
                tt_class_date,
                TT_SUBJECT_TIME_FROM_id,
                TT_SUBJECT_TIME_to_id,
                ((TT_SUBJECT_TIME_to_id-TT_SUBJECT_TIME_FROM_id)*30)/60 as TimeDuration,
                TT_DURATION,
                co_course_cr,
                course_code,
                course_cd_name as course_name,
                get_teachername(co_id) as teachername,
                sess_name
            FROM
                master_timetable left
                JOIN courses_offered ON co_id = mtt_co_id
                left JOIN courses_two on course_id = co_course_id
                left join academic_sessions on sess_id  = co_sess_id
            WHERE
                co_id = $co_id";
                //echo  $sql_lectures;exit;
                // and tt_id = 270364 
                $rec_lectures=DB::select($sql_lectures); 
            //dd($rec_lectures);
                $sqlcourseofferingcontent = "select * from course_offering_contents left JOIN courses_offered ON co_id = coc_co_id where co_id = $co_id  ";
                $courseofferingcontent = DB::select($sqlcourseofferingcontent);
        
                array_walk($rec_lectures,function($row,$index) use ($courseofferingcontent){
                    $content_keys = array_keys(array_column($courseofferingcontent,'coc_tt_id'),$row->tt_id);
                    foreach($content_keys as $cc_row):
                            $row->contents[] =  $courseofferingcontent[$cc_row];
                    endforeach;
                });
            
              //   return $rec_lectures;exit;
        
        //dd($rec_lectures);
        
              // $course_contentrec = DB::select($course_content);
        return view('grader.reports.course_content',['course_content' =>$rec_lectures]);   
        }
}