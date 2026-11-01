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
use App\Models\Grader\CourseContent;

class LecturesController extends Controller{



  public function Lectures_Listing(Request $request,$get_co_id){


    $co_id        = my_simple_crypt($get_co_id, 'd');
    $course_name  = course_name($co_id);
    $tt_id        =$request->get('tt_id');

    # Get Course Lectures List Date Wise with Details
    $CourseAttendanceModel  = new CourseAttendance();
    $all_classes            = $CourseAttendanceModel->loadClassDatesModelDashboard($co_id, 0);

    if(count($all_classes)==0){
     // return 'Semester Timetable not Created';
    }

    # Get Course Contents Enter by Teacher in Each lectures
    $sqlcourseofferingcontent = "select * from COURSE_OFFERING_CONTENTS where coc_co_id =" . $co_id . " order by coc_id asc";
    $courseofferingcontent    = DB::select($sqlcourseofferingcontent);

    # Bootom will Attach Each Lectures content array in each row
    array_walk($all_classes, function ($row, $index) use ($course_name, $courseofferingcontent) {
      $content_keys = array_keys(array_column($courseofferingcontent, 'coc_tt_id'), $row->tt_id);
      $row->course_name = $course_name;
      $row->courseofferingcontent  = [];
      foreach ($content_keys as $cc_row) :
        $row->courseofferingcontent[] =  $courseofferingcontent[$cc_row];
      endforeach;
    });

    $courseCriteria   = new CourseCriterias();
    $course_criteria  = $courseCriteria->fetchCoursesCriteria($co_id);
    $content          = $this->course_content_record(19045);

    $action = 'Faculty';

    if( isset($_GET['show-modal']) ){
       return view('grader/LectureScheduling.lecture_model', compact('co_id', 'all_classes', 'content', 'get_co_id', 'course_criteria'));
    }

    return view('grader/LectureScheduling.lectures', compact('tt_id','co_id', 'all_classes', 'content', 'get_co_id', 'course_criteria','action'));

  }
  public function toDayLecture()
  {
    $sess = new sessions();
    $sess->startSession();
    if(auth()->user()->group_id == 825){
      //echo "123";exit;
              $ora  = "select * from daily_lectures where lecture_status='ACTIVE'
              and mtt_co_id in (
                  select fc_co_id from
                  FACULTY_COURSES
                  where FC_EMPLOYEE_ID=".$sess->emp_user_id."
                  and fc_status='E'
              )
              and mtt_co_id in (
                  select co_id from COURSE_CRITERIAS
              )
              and mtt_co_id in (
                select co_id from ta_rolls where employee_id = ".auth()->user()->employee_id." and categ_name = 11
            )";
            $lecture_rec = DB::select($ora);

    }else{
            $ora = "select * from daily_lectures where lecture_status='ACTIVE'
              and mtt_co_id in (
                  select fc_co_id from
                  FACULTY_COURSES
                  where FC_EMPLOYEE_ID=".$sess->emp_user_id."
                  and fc_status='E'
              )
              and mtt_co_id in (
                  select co_id from COURSE_CRITERIAS
              )";

              // echo $ora ;exit;
              $lecture_rec = DB::select($ora);
    }
    array_walk($lecture_rec, function (&$row, $index) {
      $courseOfferingContents  = new CourseOfferingContents();
      $row->contents = $courseOfferingContents->lecture_contents($row->tt_id);
    });
    return view('grader.LectureScheduling.todaylectures', compact('lecture_rec'));
  }
  function course_content_record($coID){
    return CourseContent::getCourseContentsByCoID($coID);
  }
  public function get_content(){
      $co_id    = 17657;
      $content  = $this->course_content_record(17657);
      return view('grader/LectureScheduling.content_model', compact('content', 'co_id'));
      dd($content);
  }
}
