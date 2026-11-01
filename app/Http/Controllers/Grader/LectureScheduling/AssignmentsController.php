<?php

namespace App\Http\Controllers\Grader\LectureScheduling;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use App\Models\Grader\CourseCriterias;
class AssignmentsController extends Controller
{
 public function index($get_co_id){

    $co_id = my_simple_crypt($get_co_id, 'd' );
   // $co_id=13047;
    
    $sql = "select ccd_details,ccd_number,due_date,assessment_name,get_course_name($co_id) as course_name,co_id ,course_criteria_details.id,marks
    ,(select max(total_marks) from course_assessment_marks
    where course_assessment_marks.co_id = course_criteria_details.co_id
    and course_criteria_details.id = course_assessment_marks.course_criteria_detail_id
    ) as abc,description,file_original_name,course_criteria_details.late_submission_allowed,course_criteria_details.re_submission_allowed,file_name
    from course_criteria_details
    left join assessments on assessment_id = assessments.id
    where co_id = $co_id
   AND FACULTY_COURSE_UPLOAD_ENTRY = '1'
    order by ccd_number";
  //echo $sql;exit();
    $res = DB::select($sql);
    $countres = count($res);

    $countsql=" select count(assessment_id) as count,assessment_id,assessment_name
    from course_criteria_details
    left join assessments on course_criteria_details.assessment_id = assessments.id
    where co_id = $co_id
     AND FACULTY_COURSE_UPLOAD_ENTRY = '1'
    group by assessment_id,assessment_name";
$rescountsql = DB::select($countsql);

$action = 'Faculty';

 
    $courseCriteria = new CourseCriterias();
    $get_criteria = $courseCriteria->fetchCoursesCriteria($co_id);
    return view('grader/LectureScheduling.course_assignment',[
        
    'all_uploads'=>$res,
    'count'=>$rescountsql,
    'chkassess'=>$countres,
    'get_co_id'=>$get_co_id,
    'criteria'=>$get_criteria,
    'action'=>$action
    ]);

    }

}
