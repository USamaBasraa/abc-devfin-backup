<?php

namespace App\Http\Controllers\Grader\LectureScheduling;

use App\Http\Controllers\Controller;
use App\Models\Grader\CourseAttendance;
use App\Models\Grader\CourseCriterias;
use Illuminate\Http\Request;
use DB;
use App\Models\Grader\CourseOfferingContents;
use App\Models\Grader\MasterTimetable;
use App\sessions;
use App\Models\Grader\CoursesOffered;

class StudentCourseDetailController extends Controller
{
    public function getStudentDetails($co_id){
        $course_id = my_simple_crypt($co_id, 'd' );


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
                co.co_id = ".$course_id."
            OR
                co.parent_co_id = ".$course_id."
        )
        ORDER BY LOWER(spi.spi_name)";


//echo $query;exit();

$result =DB::select($query);
 // echo $query;exit;
     
        return view(
          'grader.StudentDetail.student_detail',['studentsList' => $result]
      );
    }
    function get_criteria_details($co_id){

      $criteria_sql="SELECT
          assessments.assessment_name AS a_name,
          course_criterias.cc_weightage AS cw,
          assessments.id AS a_id,
          get_course_grading_type(course_criterias.co_id) AS course_grading_type,
          course_cd_name,
                      case 
                  when cc_method_id=1 then 'Equal Distribution'
                  when cc_method_id=2 then 'Student Wise Best'
                  when cc_method_id=3 then 'Best On Average'
                  when cc_method_id=4 then 'Instructor Selection'
                  END  as method_name,
                   course_criterias.BEST_OF_NO
        FROM
          course_criterias
          LEFT JOIN assessments ON assessment_id = assessments.id
          LEFT JOIN courses_offered ON course_criterias.co_id = courses_offered.co_id
          LEFT JOIN courses_two ON course_id = courses_offered.co_course_id
        WHERE
        course_criterias.co_id = $co_id
        order by assessments.id";
      $rsCriteria = DB::select($criteria_sql);
     // echo  $criteria_sql;exit;
      return $rsCriteria;
  }
  function weigtage($co_id,$roll_no){

    $summary_sql="SELECT
                assessment_name,
                obtained_marks,
                total_marks,
                (
                SELECT
                gh_grade
                FROM
                grade_history
                LEFT JOIN courses_offered ON gh_co_id = co_id
                WHERE
                gh_sai_enrollment_no = sai_enrollment_no
                AND (gh_co_id = $co_id OR parent_co_id = $co_id)
                AND ROWNUM = 1
                AND gh_exam_fsubmit=1
                ) AS gh_grade,
    (SELECT gh_grading_type
  FROM grade_history
  LEFT JOIN courses_offered
  ON gh_co_id                = co_id
  WHERE gh_sai_enrollment_no = sai_enrollment_no
  AND (gh_co_id              = $co_id
  OR parent_co_id            = $co_id)
  AND ROWNUM                 = 1
  AND gh_exam_fsubmit        =1
  ) AS grading_type
                FROM
                course_marks_summaries
                LEFT JOIN assessments ON course_marks_summaries.assessment_id = assessments.id
                WHERE
                sai_enrollment_no = '$roll_no'
                AND
                co_id = $co_id
                ORDER BY assessments.id";
//  echo $summary_sql;exit;
        $rssummary_sql = DB::select($summary_sql);
return $rssummary_sql;
    }
    function marksdetail($co_id,$roll_no){
     
      $criteria = $this->get_criteria_details($co_id);
      $cr = count($criteria);
     // $a_id=$criteria[0]->a_id;


      $sqlMarksDetails="SELECT *
      FROM COURSE_ASSESSMENT_MARKS
      LEFT JOIN COURSE_CRITERIA_DETAILS
      ON COURSE_ASSESSMENT_MARKS.COURSE_CRITERIA_DETAIL_ID = COURSE_CRITERIA_DETAILS.id
      left join courses_offered on courses_offered.co_id = COURSE_ASSESSMENT_MARKS.co_id
      left join courses_two on courses_offered.co_course_id = courses_two.course_id  
      left join grade_history on courses_offered.co_id = gh_co_id and gh_sai_enrollment_no = '$roll_no'
      LEFT JOIN assessments ON COURSE_ASSESSMENT_MARKS.assessment_id           = assessments.id
      WHERE COURSE_ASSESSMENT_MARKS.SAI_ENROLLMENT_NO      = '$roll_no'
    
      AND COURSE_ASSESSMENT_MARKS.co_id                    =$co_id
      AND CAM_STATUS                                       ='E'
      ORDER BY COURSE_ASSESSMENT_MARKS.assessment_id,cam_number ";
    // echo $sqlMarksDetails;exit;
      $rsMarksDetails = DB::select($sqlMarksDetails);

      $weigtage = $this->weigtage($co_id,$roll_no);
      $total_assessments = count($rsMarksDetails);
      
      // SQL for Student Absent Attendance Rows 
      $sqlAttendanceDetails="SELECT ca_attendance_date,ca_desc
      FROM course_attendance
      WHERE sai_enrollment_no = '$roll_no'
      AND co_id               in (select co_id from courses_offered where co_id = $co_id or parent_co_id = $co_id)
      
      AND ca_attendance       = 'A'";
     // echo $sqlAttendanceDetails;exit;
      $rsAttendanceDetails = DB::select($sqlAttendanceDetails);
      // attendance query ends here
      return view('grader.StudentDetail.student_detail_marks',
          [
              'co_id'=>$co_id,
              'rsMarksDetails'=>$rsMarksDetails,
              'total_assessments'=>$total_assessments,
              'ww' => $weigtage,
              'criteria' =>$criteria,
              'cr'=>$cr,
              'attendance'=>$rsAttendanceDetails
          ]);
  }
 
}
