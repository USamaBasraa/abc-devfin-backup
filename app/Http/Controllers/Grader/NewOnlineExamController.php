<?php
/**
 * Created by PhpStorm.
 * User: bilal.saleem
 * Date: 30/03/2020
 * Time: 11:30 AM
 */

namespace App\Http\Controllers\Grader;
use Illuminate\Http\Request;
use DB;
use App\Models\CourseCriterias;
use App\Models\CourseCriteriaDetails;
use App\sessions;


class NewOnlineExamController extends Controller
{


public function retrieveexam(Request $request,$adm_sch_id ){


//echo 'bilalsaleem'.$adm_sch_id;exit;
  $fc_co_id=$this->my_simple_crypt(13047, 'd' );
  $co_id=$this->my_simple_crypt(13047, 'e' );


//dd('hello');
return view('grader.exam.admission_exam_header',['fc_co_id'=>$co_id,'adm_sch_id'=>$adm_sch_id]);


}


public function viewresultcopy($id){
   //  echo  $id;exit;
      $course_name='';

      $sqlcount = "SELECT count(*) as total_ques
      FROM EXAM_STUDENT_PAPER
      left join EXAM_QUESTION_BANK on EXAM_QUESTION_BANK.id=EXAM_STUDENT_PAPER.question_bank_id
      where 1=1
      --and SAI_ENROLLMENT_NO='F2019-350'
      and COURSE_EXAM_PAPERS_ID=$id
      and EXAM_QUESTION_BANK.EXAM_QUES_TYPE in (2,4)
      and EXAM_STUDENT_PAPER.MARKS_ENTER_STATUS is null";
      $ressqlcount = DB::select($sqlcount);
    // echo $sqlcount;exit();

      $sql = "SELECT
              exam_student_attendance.description,
              final_obtained_marks,
              exam_student_attendance.exam_status,
              exam_student_results_view.sai_enrollment_no,
              spi_name,
              correct,
              wrong,
              missed,
              correct_marks,
              wrong_marks,
              missed_marks,
              exam_papers.id,
  (select student_entery_datetime from EXAM_STUDENT_ATTENDANCE where exam_id = $id and sai_enrollment_no =  exam_student_results_view.sai_enrollment_no ) as last_activity_time,
  (select exam_remaining_time from EXAM_STUDENT_ATTENDANCE where exam_id = $id and sai_enrollment_no =  exam_student_results_view.sai_enrollment_no ) as remaining_time,
               (SELECT count(*) as total_ques
FROM EXAM_STUDENT_PAPER
left join EXAM_QUESTION_BANK on EXAM_QUESTION_BANK.id=EXAM_STUDENT_PAPER.question_bank_id
where SAI_ENROLLMENT_NO=exam_student_results_view.sai_enrollment_no and COURSE_EXAM_PAPERS_ID=$id and EXAM_QUESTION_BANK.EXAM_QUES_TYPE in (2,4)
and EXAM_STUDENT_PAPER.MARKS_ENTER_STATUS is null) as result_status,
(select sum(question_marks) from EXAM_QUESTION_PAPER where EXAM_QUESTION_PAPER.EXAM_PAPERS_ID =exam_student_results_view.course_exam_papers_id) as get_total_marks
          FROM
              exam_student_results_view
              LEFT JOIN students_apply_info ON exam_student_results_view.sai_enrollment_no = students_apply_info.sai_enrollment_no
              LEFT JOIN students_personal_info ON students_apply_info.sai_spi_id = students_personal_info.spi_id
              LEFT JOIN exam_papers ON exam_papers.id = exam_student_results_view.course_exam_papers_id
              LEFT JOIN exam_student_attendance ON
                  exam_papers.id = exam_student_attendance.exam_id
              AND
                  exam_student_attendance.sai_enrollment_no = students_apply_info.sai_enrollment_no
              where COURSE_EXAM_PAPERS_ID = $id order by spi_name ";
  //echo $sql;exit();
      $users = DB::select($sql);

      $sql="select *from exam_papers where id=$id";
      $sqlresult = DB::select($sql);

      if($sqlresult){
          $co_id=$sqlresult[0]->co_id;
          $course_name = DB::selectOne("select get_course_name($co_id) as value from dual");
          $course_name=$course_name->value;
        }


      return view('grader.teacher.view_result_copy',['result'=>$users,'course_name'=>$course_name,'sqlresult'=>$sqlresult]);

    }
	public function onlineexamview(){
     // $decrypt_co_id=$this->my_simple_crypt($co_id, 'd' );
      // $users = DB::table('exam_papers')
      // ->where('co_id','=',$decrypt_co_id)
      // ->orderBy('id', 'desc')
      // ->get();

       $sql = "select id,
      get_course_name(co_id) as course_name,
      get_teachername(co_id) as teacher_name,
      CO_ID,
      TITLE,
      TOTAL_QUESTIONS,
      EXAM_PATTERN,
      EXAM_DATE,
      EXAM_START_TIME,
      EXAM_DURATION,
      ENTRY_DATE_TIME,
      ENTRY_USER,
      STATUS,
      EXAM_END_TIME,
      PERCENTAGE_STATUS,
      COURSE_CRITERIA_ID,
      ASSESSMENT_ID,
      ASSESSMENT_NO,
      FINAL_SUBMIT,
      NEGATIVE_MARKS,
      PAPER_ATTMP_TYPE,
      RE_ATTEMPT,
      RE_VIEW,
      EXAM_FILE,
      COPY_PASTE_ALLOWED,
      (select count(*) from
            EXAM_question_paper
           where exam_papers_id= exam_papers.ID
           ) as  student_exam_question,
           exam_answer_file,
           (select count(*) from exam_student_attendance where exam_id = exam_papers.ID
) as paper_start
      from exam_papers
      order by exam_date desc";
      $users  = DB::select($sql);

      // $courseCriteria = new CourseCriterias();
      // $get_criteria = $courseCriteria->fetchCoursesCriteria($decrypt_co_id);

      // $course_name = DB::selectOne("select get_course_name($decrypt_co_id) as value from dual");
      // $course_name=$course_name->value;

      return view('grader.teacher.online_exam_view',['result'=>$users]);
    }
  function my_simple_crypt( $string, $action = 'd' ) {
    // you may change these values to your own
    $secret_key = 'my_simple_secret_key';
    $secret_iv = 'my_simple_secret_iv';

    $output = false;
    $encrypt_method = "AES-256-CBC";
    $key = hash( 'sha256', $secret_key );
    $iv = substr( hash( 'sha256', $secret_iv ), 0, 16 );

    if( $action == 'e' ) {
        $output = base64_encode( openssl_encrypt( $string, $encrypt_method, $key, 0, $iv ) );
    }
    else if( $action == 'd' ){
        $output = openssl_decrypt( base64_decode( $string ), $encrypt_method, $key, 0, $iv );
    }

    return $output;
  }

     function notification($co_id,$message,$user_id,$target_page){

        try {
         $procedurenotification = 'notification';
         $bindings_notification = [
               'co_id'       => $co_id,
               'desc'        => $message,
               'user_id'     =>$user_id,
             'target_page'  =>$target_page,

           ];
           //print_r( $bindings_notification);exit();
      return $result_notification = DB::executeProcedure($procedurenotification, $bindings_notification);
     } catch (Exception $e) {
         echo $e;
        // echo 'no';
         // This calls the render() method of `App\Exceptions\Handler`
     }
    }

public function exam_final($id,$co_id,$adm_sch_id){
//echo $adm_sch_id;exit;
  $sess = new sessions();
  $sess->startSession();
  $login_id = $sess->emp_user_id;
  $co_id=$this->my_simple_crypt($co_id,'d');
 //echo $id.' - '.$co_id;

$sql = "select id,total_questions,(select count(*)  from exam_question_paper where exam_papers_id = exam_papers.id) as defined
,title,exam_start_time,exam_duration,re_attempt,re_view,copy_paste_allowed,exam_answer_file from exam_papers where id = $id";
 $res_sql  = DB::select($sql);
//echo $sql;exit;
 $res_sql[0]->defined;

$res_sql[0]->total_questions;
$exam_name = $res_sql[0]->title;
$dueDate = $res_sql[0]->exam_start_time;
$duration = $res_sql[0]->exam_duration;


if($res_sql[0]->re_attempt == 1){
  $re_attempt = ' Re Attemp of questions are allowed';
}else{
  $re_attempt = ' Re Attemp of questions are not allowed';
}
if($res_sql[0]->re_view == 1){
  $re_view = ' Re View of questions summaries are allowed';
} else {
  $re_view = ' Re View of questions summaries are not allowed';
}
if($res_sql[0]->copy_paste_allowed == 'Yes'){
  $copy_paste = ' Copy Paste is allowed in this exam';
} else {
  $copy_paste = ' Copy Paste is not allowed in this exam';
}
if($res_sql[0]->exam_answer_file == 1){
  $exam_answer_file = ' File Uploading is required';
} else {
  $exam_answer_file = ' File Uploading in not required';
}


     // if($res_sql[0]->total_questions < $res_sql[0]->defined){

     //  return 1;
     // }else{

          DB::table('exam_papers')
                      ->where('id', $id)
                      ->update([

                        'final_submit' => 1
                        ]);

   $mess = 'Exam Name  : '.$exam_name.'
	  '.' And Exam Date Time is :'.$dueDate . '
	  '.' Duration of Exam is :'.$duration.' '.'Mint'. '
	  '.$re_attempt.',
	  '.$re_view.',
	  '.$copy_paste.'
    and '.' '.$exam_answer_file;
    
    
    // try {
    //   //echo '123';
    //      # this call function of notificaiton to send notificaotn in this page
    //   if($co_id !=0){
    //     $this->notification($co_id ,$mess,$login_id,'exam');
    //   }
    // } catch (Exception $e) {
    // //  echo '1234';
    //  // echo $e;
    //   // echo 'no';
    //     // This calls the render() method of `App\Exceptions\Handler`
    // }

   # this call function of notificaiton to send notificaotn in this page
  if($co_id !=0){
    $this->notification($co_id ,$mess,$login_id,'exam');

  }


}

//////////////////admission exam /////////////////////////////


public function onlineexam_admission($adm_sch_id){
     //echo "bilal".$adm_sch_id;exit;
     // $decrypt_co_id=$this->my_simple_crypt($co_id, 'd' );
      $adm_sch_name ='';
      // $users = DB::table('exam_papers')
      // ->where('co_id','=',$decrypt_co_id)
      // ->orderBy('id', 'desc')
      // ->get();


        $sql_sch_name  = "select admission_sch_name from admission_schedules where admission_sch_id =$adm_sch_id";
        $adm_sch_name  = DB::select($sql_sch_name);



      $sql = "select id,
      CO_ID,
      TITLE,
      TOTAL_QUESTIONS,
      EXAM_PATTERN,
      EXAM_DATE,
      EXAM_START_TIME,
      EXAM_DURATION,
      ENTRY_DATE_TIME,
      ENTRY_USER,
      STATUS,
      EXAM_END_TIME,
      PERCENTAGE_STATUS,
      COURSE_CRITERIA_ID,
      ASSESSMENT_ID,
      ASSESSMENT_NO,
      FINAL_SUBMIT,
      NEGATIVE_MARKS,
      PAPER_ATTMP_TYPE,
      RE_ATTEMPT,
      RE_VIEW,
      EXAM_FILE,
      exam_paper_admission_sch_id,
      COPY_PASTE_ALLOWED,
      (select count(*) from
            EXAM_question_paper
           where exam_papers_id= exam_papers.ID
           ) as  student_exam_question,
           exam_answer_file,
           (select count(*) from exam_student_attendance where exam_id = exam_papers.ID
) as paper_start
      from exam_papers where EXAM_PAPER_ADMISSION_SCH_ID =$adm_sch_id
      order by id desc";
   echo $sql;exit;
      $users  = DB::select($sql);


      return view('grader.teacher.online_exam',['co_id'=>'','result'=>$users,'course_criteria'=>'','course_name'=> '','adm_sch_id'=>$adm_sch_id,'adm_sch_name'=>$adm_sch_name]);
    }





public function insert_adm_exam(Request $request){

           $sess = new sessions();
          $sess->startSession();
          $login_id = $sess->emp_user_id;

          $co_id          = $request->get('co_id');
          $exam_name      = $request->get('exam_name');
          $total_question = $request->get('total_question');
          $papper_pattern = $request->get('papper_pattern');
          $dueDate        = $request->get('duedate');
          $duetime        = $request->get('duetime');
          $duration       = $request->get('duration');
          $percentage_status       = $request->get('percentage_status');
          $assessment_id       = $request->get('assessment_id');
          $assessment_no       = $request->get('assessment_no');
          $file_full_name          = $request->get('edit_file');
          $adm_sch_id          = $request->get('adm_sch_id');
         // $decrypt_co_id=$this->my_simple_crypt($co_id, 'd' );


          if ($request->hasFile('uploadfile')) {
            $this->validate($request, [
                'uploadfile'=>'required|max:50000|mimes:xlsx,docx,pdf'
            ]);
            $file = $request->file('uploadfile');
           // echo $file->getClientOriginalExtension();
            $file_name = $file->getClientOriginalName();
            $name = $file->getClientOriginalName();
            $image['filePath'] = $name;
            $file_full_name = $decrypt_co_id . '_' . $login_id . '_' .  $exam_name . '.' . $file->getClientOriginalExtension();
            $file->move(public_path() . '/docs/',  $file_full_name);
          }

       // exit;
          $time = (String) $dueDate.' '.$duetime;
          $time = new \Carbon\Carbon($time);


        DB::table('exam_papers')->insert(
            [

                'title' => $exam_name,
                'total_questions' => $total_question,
                'exam_pattern' => $papper_pattern,
                'exam_date' => $dueDate,
                'exam_start_time' => $time,
                'exam_duration' => $duration,
                'percentage_status' => $percentage_status,
                'assessment_id' => $assessment_id,
                'COURSE_CRITERIA_ID' => $adm_sch_id,
                'assessment_no' => $assessment_no,
                'co_id' => 0,
                're_attempt' => '1',
                're_view' => '0' ,
                'PAPER_ATTMP_TYPE' => 'all_in_one',
                'exam_file' => $file_full_name,
                'EXAM_PAPER_ADMISSION_SCH_ID' => $adm_sch_id
                ]
        );

      //   DB::table('course_criteria_details')->insert(
      //     [
      //         'co_id' => $decrypt_co_id,
      //         'ccd_details' => $exam_name,
      //         'ccd_date' => $dueDate,
      //         'assessment_id' => $assessment_id,
      //         'COURSE_CRITERIA_ID' => $id,
      //         'ccd_number' => $assessment_no

      //         ]
      // );
            $time=substr($time,11,10);
      // return response()->json(['success'=>'Marks Saved Successfully.']);
         $mess = 'Exam Name  : '.$exam_name.' \n'.'And Exam Date is :'.$dueDate .' \n'.'start Time :'.$time . ' \n'.'Duration of Exam is :'.$duration.' '.'Mint';

           # this call function of notificaiton to send notificaotn in this page
//            $this->notification($decrypt_co_id ,$mess,$login_id,'exam');

    }






/////////////////////////////admission exam end ////////////////////////
public function newonlineexam($co_id){
 // echo '1230';exit;
  //echo "bilal".$adm_sch_id;exit;
   $decrypt_co_id=$this->my_simple_crypt($co_id, 'd' );
   $adm_sch_name ='';
   // $users = DB::table('exam_papers')
   // ->where('co_id','=',$decrypt_co_id)
   // ->orderBy('id', 'desc')
   // ->get();
   // if($adm_sch_id != 'course'){

   //   $sql_sch_name  = "select admission_sch_name from admission_schedules where admission_sch_id =$adm_sch_id";
   //   $adm_sch_name  = DB::select($sql_sch_name);

   // }

   $sql = "select id,
   CO_ID,
   TITLE,
   TOTAL_QUESTIONS,
   EXAM_PATTERN,
   EXAM_DATE,
   EXAM_START_TIME,
   EXAM_DURATION,
   ENTRY_DATE_TIME,
   ENTRY_USER,
   STATUS,
   EXAM_END_TIME,
   PERCENTAGE_STATUS,
   COURSE_CRITERIA_ID,
   ASSESSMENT_ID,
   ASSESSMENT_NO,
   FINAL_SUBMIT,
   NEGATIVE_MARKS,
   PAPER_ATTMP_TYPE,
   RE_ATTEMPT,
   RE_VIEW,
   EXAM_FILE,
   COPY_PASTE_ALLOWED,
   exam_paper_admission_sch_id,
   (select count(*) from
         EXAM_question_paper
        where exam_papers_id= exam_papers.ID
        ) as  student_exam_question,
        exam_answer_file,
        (select count(*) from exam_student_attendance where exam_id = exam_papers.ID
) as paper_start
   from exam_papers where co_id=$decrypt_co_id
   order by id desc";
 //echo $sql;exit;
   $users  = DB::select($sql);



   $courseCriteria = new CourseCriterias();
   $get_criteria = $courseCriteria->fetchCoursesCriteria($decrypt_co_id);

//dd($get_criteria);
   $course_name = DB::selectOne("select get_course_name($decrypt_co_id) as value from dual");
   $course_name=$course_name->value;

   return view('grader.teacher.new_online_exam',['co_id'=>$co_id,'result'=>$users,'course_criteria'=>$get_criteria,'course_name'=> $course_name,'adm_sch_id'=>'','adm_sch_name'=>'']);
 }


    public function insertquestion(Request $request){

          $sess = new sessions();
          $sess->startSession();
          $login_id = $sess->emp_user_id;

          $co_id          = $request->get('co_id');
          $exam_name      = $request->get('exam_name');
          $total_question = $request->get('total_question');
          $papper_pattern = $request->get('papper_pattern');
          $dueDate        = $request->get('duedate');
          $duetime        = $request->get('duetime');
          $duration       = $request->get('duration');
          $percentage_status       = $request->get('percentage_status');
          $assessment_id       = $request->get('assessment_id');
          $assessment_no       = $request->get('assessment_no');
          $file_full_name          = $request->get('edit_file');

          $decrypt_co_id=$this->my_simple_crypt($co_id, 'd' );


          if ($request->hasFile('uploadfile')) {
            $this->validate($request, [
                'uploadfile'=>'required|max:50000|mimes:xlsx,docx,pdf,png,jpg,jpeg'
            ]);
            $file = $request->file('uploadfile');
           // echo $file->getClientOriginalExtension();
            $file_name = $file->getClientOriginalName();
            $name = $file->getClientOriginalName();
            $image['filePath'] = $name;
            $file_full_name = $decrypt_co_id . '_' . $login_id . '_' .  $exam_name . '.' . $file->getClientOriginalExtension();
            $file->move(public_path() . '/docs/',  $file_full_name);
          }

       // exit;
          $time = (String) $dueDate.' '.$duetime;
          $time = new \Carbon\Carbon($time);


          $sql="select id from course_criterias where co_id = $decrypt_co_id and assessment_id = $assessment_id";
          $resultsql = DB::select($sql);
          foreach($resultsql as $row){
            $id = $row->id;
          }


        DB::table('exam_papers')->insert(
            [
                'co_id' => $decrypt_co_id,
                'title' => $exam_name,
                'total_questions' => $total_question,
                'exam_pattern' => $papper_pattern,
                'exam_date' => $dueDate,
                'exam_start_time' => $time,
                'exam_duration' => $duration,
                'percentage_status' => $percentage_status,
                'assessment_id' => $assessment_id,
                'COURSE_CRITERIA_ID' => $id,
                'assessment_no' => $assessment_no,
                're_attempt' => '1',
                're_view' => '0' ,
                'PAPER_ATTMP_TYPE' => 'all_in_one',
                'exam_file' => $file_full_name,
                 'EXAM_PAPER_ADMISSION_SCH_ID' => 0

                ]
        );

      //   DB::table('course_criteria_details')->insert(
      //     [
      //         'co_id' => $decrypt_co_id,
      //         'ccd_details' => $exam_name,
      //         'ccd_date' => $dueDate,
      //         'assessment_id' => $assessment_id,
      //         'COURSE_CRITERIA_ID' => $id,
      //         'ccd_number' => $assessment_no

      //         ]
      // );
            $time=substr($time,11,10);
      // return response()->json(['success'=>'Marks Saved Successfully.']);
         $mess = 'Exam Name  : '.$exam_name.' \n'.'And Exam Date is :'.$dueDate .' \n'.'start Time :'.$time . ' \n'.'Duration of Exam is :'.$duration.' '.'Mint';

           # this call function of notificaiton to send notificaotn in this page
//            $this->notification($decrypt_co_id ,$mess,$login_id,'exam');

    }
    public function edit_exam($id){

      $sql = "select * from exam_papers where id = $id";
     return $res_sql = DB::select($sql);
    }

    public function updatequestion(Request $request,$id){
          $sess = new sessions();
          $sess->startSession();
          $login_id = $sess->emp_user_id;

      $co_id          = $request->get('co_id');
      $exam_name      = $request->get('exam_name');
      $total_question = $request->get('total_question');
      $papper_pattern = $request->get('papper_pattern');
      $dueDate        = $request->get('duedate');
      $duetime        = $request->get('duetime');
      $duration       = $request->get('duration');
      $percentage_status       = $request->get('percentage_status');
      $file_full_name          = $request->get('edit_file');


      $decrypt_co_id=$this->my_simple_crypt($co_id, 'd' );

      if ($request->hasFile('uploadfile')) {
        $this->validate($request, [
            'uploadfile'=>'required|max:50000|mimes:xlsx,docx'
        ]);
        $file = $request->file('uploadfile');
       // echo $file->getClientOriginalExtension();
        $file_name = $file->getClientOriginalName();
        $name = $file->getClientOriginalName();
        $image['filePath'] = $name;
        $file_full_name = $decrypt_co_id . '_' . $login_id . '_' .  $exam_name . '.' . $file->getClientOriginalExtension();
        $file->move(public_path() . '/docs/', $file_full_name);
      }
      $time = (String) $dueDate.' '.$duetime;
      $time = new \Carbon\Carbon($time);


      DB::table('exam_papers')
            ->where('id', $id)
            ->update([
              'co_id' => $decrypt_co_id,
              'title' => $exam_name,
              'total_questions' => $total_question,
              'exam_pattern' => $papper_pattern,
              'exam_date' => $dueDate,
              'exam_start_time' => $time,
              'exam_duration' => $duration,
              'percentage_status' => $percentage_status,
              'exam_file' => $file_full_name
              ]);

              $sql = "select id,total_questions,(select count(*)  from exam_question_paper where exam_papers_id = exam_papers.id) as defined
              ,title,exam_start_time,exam_duration,re_attempt,re_view,copy_paste_allowed,exam_answer_file from exam_papers where id = $id";
               $res_sql  = DB::select($sql);
              //echo $res_sql[0]->re_attempt;exit;


              if($res_sql[0]->re_attempt == 1){
                $re_attempt = 'Re Attemp of questions are allowed';
              }else{
                $re_attempt = 'Re Attemp of questions are not allowed';
              }
              if($res_sql[0]->re_view == 1){
                $re_view = 'Re View of questions summaries are allowed';
              } else {
                $re_view = 'Re View of questions summaries are not allowed';
              }
              if($res_sql[0]->copy_paste_allowed == 'Yes'){
                $copy_paste = 'Copy Paste is allowed in this exam';
              } else {
                $copy_paste = 'Copy Paste is not allowed in this exam';
              }
              if($res_sql[0]->exam_answer_file == 1){
                $exam_answer_file = 'File Uploading is required';
              } else {
                $exam_answer_file = 'File Uploading in not required';
              }

                $time=substr($time,11,10);
                $mess = 'Exam Name  : '.$exam_name.' \n'.'And Exam Date Time is :'.$dueDate . ' \n'.'Duration of Exam is :'.$duration.' '.'Mint'. ' \n'.$re_attempt.', '.$re_view.', '.$copy_paste.'and'.$exam_answer_file;

           # this call function of notificaiton to send notificaotn in this page
           // $this->notification($decrypt_co_id ,$mess,$login_id,'exam');


    }
    public function getcount($assess_id,$co_id){
      $decrypt_co_id=$this->my_simple_crypt($co_id, 'd' );
      $sql="select count(*)+1 as count from exam_papers where co_id = $decrypt_co_id and assessment_id = $assess_id ";
   return   $resultsql = DB::select($sql);

    }

 public function getadmcount($adm_sch_id){

      $sql="select count(*)+1 as count from exam_papers where EXAM_PAPER_ADMISSION_SCH_ID = $adm_sch_id";
   return   $resultsql = DB::select($sql);

    }



    public function onlineresult($fc_co_id){

      $co_id=$this->my_simple_crypt($fc_co_id, 'd' );
      $users = DB::table('exam_papers')
      ->where('co_id','=',$co_id)
      ->orderBy('id', 'desc')
      ->get();
      
     // print_r($users);
    $courseCriteriadetail = new CourseCriteriaDetails();
   $get_criteriadetail = $courseCriteriadetail->fetchCriteriaDetails($co_id);

   $courseCriteria = new CourseCriterias();
   $get_criteria = $courseCriteria->fetchCoursesCriteria($co_id);
//dd($get_criteriadetail);

        $course_name = DB::selectOne("select get_course_name($co_id) as value from dual");
        $course_name=$course_name->value;

         return view('grader.teacher.online_result',['result'=>$users,'course_name'=>$course_name,'criteria_detail'=>$get_criteriadetail,'get_criteria'=>$get_criteria ]);

    }
    public function onlineresult_admission($adm_sch_id){
    //  echo $adm_sch_id;exit;
      //$sql="select count(*)+1 as count from exam_papers where co_id = $co_id and assessment_id = $assess_id ";
     // $co_id=$this->my_simple_crypt($fc_co_id, 'd' );
      $users = DB::table('exam_papers')
      ->where('EXAM_PAPER_ADMISSION_SCH_ID','=',$adm_sch_id)
      ->orderBy('id', 'desc')
      ->get();

        // $course_name = DB::selectOne("select get_course_name($co_id) as value from dual");
        // $course_name=$course_name->value;

      return view('grader.teacher.online_result',['result'=>$users,'course_name'=>'' ]);

    }
    public function DownloadPaperWord($exam_id){

    //  echo $exam_id;
      //exit();

      $sql="SELECT
                *
      FROM
          exam_student_paper
          LEFT JOIN exam_question_bank ON exam_student_paper.question_bank_id = exam_question_bank.id
          LEFT JOIN exam_question_paper ON exam_student_paper.course_exam_papers_id = exam_question_paper.exam_papers_id
          RIGHT JOIN exam_papers ON
              exam_student_paper.course_exam_papers_id = exam_papers.id
          AND
              exam_student_paper.question_bank_id = exam_question_paper.exam_question_bank_id
          LEFT JOIN EXAM_SECTIONS on exam_section_id =EXAM_SECTIONS.id
      WHERE
              course_exam_papers_id = $exam_id

      ORDER BY sai_enrollment_no";
     // echo $sql;exit();
      $row = DB::select($sql);
     // print_r($row);exit();
      return view('grader.exam/exam_export_word',['row'=>$row]);


    }
    public function move_to_cms(Request $request){

        $quiz_detail        = $request->get('quiz_detail');
        $exam_id            = $request->get('cms_exam_id');
        $co_id              = $request->get('cms_co_id');
        $assessment_id      = $request->get('cms_assessment_id');
        $error_message      = 'Some Problem with the data, Please contact ITRC';

        $sess = new sessions();
        $sess->startSession();
        $login_id = $sess->emp_user_id;

        $check_recod     = "select * from exam_papers where id =".$exam_id."  and move_to_cms='No'";
        $rec_check_recod = DB::select($check_recod);

        if(count($rec_check_recod)>0){
             $sql_max = "select * from COURSE_CRITERIA_DETAILS where co_id=".$co_id." and assessment_id = ".$assessment_id;
             $cam_no = (count(DB::select($sql_max))+1);

        $DbRaw=" select id from COURSE_CRITERIAS where co_id=$co_id and ASSESSMENT_ID=$assessment_id ";

        $RecDbRaw = DB::select($DbRaw);
        # New Entry in Course Criteria detaila and get the inserted it
        try{
            $course_criteria_id = $RecDbRaw[0]->id;
                $vars = [
                'co_id'              => $co_id,
                'ASSESSMENT_ID'      => $assessment_id,
                'COURSE_CRITERIA_ID' => $course_criteria_id,
                'CCD_NUMBER'         => $cam_no,
                'ccd_details'        => $quiz_detail,
                'CCD_STATUS'         => 'E',
                'CCD_WEIGHTAGE'      => '0',
                'CCD_ENTRY_USER'     => $sess->emp_user_id
            ];
            $course_criteria_detail_id = DB::table('COURSE_CRITERIA_DETAILS')->insertGetId($vars);
        }catch (\Exception $e) {
            return  $error_message;
            exit();
        }
      $add="INSERT INTO course_assessment_marks (
        co_id,
        sc_co_id,
        assessment_id,
        course_criteria_id,
        sai_id,
        sai_enrollment_no,
        total_marks,
        obtained_marks,
        cam_number,
        cam_desc,
        cam_status,
        cam_entry_user,
        created_at,
        updated_at,
        course_criteria_detail_id,
        cam_date,
        cam_details
    )
    select
        exam_papers.co_id
        ,sc_co_id
        ,EXAM_PAPERS.assessment_id
        ,$course_criteria_id
        ,sai_id
        ,SC_SAI_ENROLLMENT_NO
        ,(select SUM(exam_question_paper.QUESTION_MARKS) FROM exam_question_paper where exam_papers_id = EXAM_PAPERS.ID) AS TOTAL_MARKS
        ,obtained_marks
        ,$cam_no
        ,'Online Exam' as cam_details
        ,'E'
        ,$login_id
        ,current_timestamp
        ,current_timestamp
        ,$course_criteria_detail_id
        ,to_Date(sysdate) as cam_date
        ,'$quiz_detail'
    from EXAM_PAPERS
   -- left join courses_offered on (courses_offered.co_id = EXAM_PAPERS.co_id or courses_offered.parent_co_id = EXAM_PAPERS.co_id)
    --left join STUDENT_COURSES on (sc_co_id = courses_offered.co_id or sc_co_id = courses_offered.parent_co_id)
    left join courses_offered on courses_offered.co_id = EXAM_PAPERS.co_id
    left join STUDENT_COURSES on (sc_co_id = courses_offered.co_id or sc_co_id in (select co_id from courses_offered where parent_co_id = exam_papers.co_id))
    left join exam_result on paper_id = EXAM_PAPERS.id and lower(student_id) = lower(STUDENT_COURSES.SC_SAI_ENROLLMENT_NO)
    LEFT JOIN students_apply_info ON lower(students_apply_info.sai_enrollment_no) = lower(STUDENT_COURSES.SC_SAI_ENROLLMENT_NO)
    where id = $exam_id
    and sc_status='A'";
   // echo $add;exit();
                try {
                    if(DB::insert($add)){
                        # if marks enter in course_assessment_marks then it run the bottom
                        # so that same exam will not again move to cms
                        DB::table('exam_papers')
                        ->where('id', $exam_id)
                        ->update([
                        'move_to_cms' => $course_criteria_detail_id
                        ]);
                        echo '1';
                    }
                } catch (\Exception $e) {

                CourseCriteriaDetails::destroy($course_criteria_detail_id);
                return $error_message;
               //  return $e->getMessage();
                }
    }else{
           echo 'Already moved';
       }
    }
    public function viewresult2($id){

       $exam_id=$this->my_simple_crypt($id, 'd' );
      // echo $exam_id;exit();
        $test="SELECT
        course_exam_papers_id,
        exam_student_paper.sai_enrollment_no,
        answer_status,
        ceqp_id,
        question_marks,
        exam_ques_type,
            CASE
                WHEN
                    exam_ques_type IN (
                        1,3
                    )
                AND
                    answer_status = 'T'
                THEN question_marks
                WHEN
                    exam_ques_type IN (
                        1,3
                    )
                AND
                    answer_status IN (
                        'M','F'
                    )
                THEN 0
                ELSE coalesce(question_obtained_marks,0)
            END
        AS obtained_marks,
        question_obtained_marks,
        answer_given,
        spi_name,
        COALESCE(marks_enter_status,0) as marks_enter_status,
        coalesce(exam_student_attendance.exam_status,0) as exam_status,
        description
        FROM
    exam_student_paper
    LEFT JOIN exam_question_paper ON
        exam_question_paper.exam_papers_id = exam_student_paper.course_exam_papers_id
    AND
        exam_student_paper.question_bank_id = exam_question_paper.exam_question_bank_id
    LEFT JOIN exam_question_bank ON exam_question_bank.id = exam_student_paper.question_bank_id
    LEFT JOIN students_apply_info ON lower(students_apply_info.sai_enrollment_no) = lower(exam_student_paper.sai_enrollment_no)
    LEFT JOIN students_personal_info ON spi_id = sai_spi_id
    LEFT JOIN exam_student_attendance ON
        exam_question_paper.exam_papers_id = exam_student_attendance.exam_id
    AND
    lower(exam_student_attendance.sai_enrollment_no) = lower(students_apply_info.sai_enrollment_no)
WHERE
    exam_student_paper.course_exam_papers_id = $exam_id
        ORDER BY spi_name,exam_student_paper.sai_enrollment_no,exam_ques_type DESC";
      //   echo $test;exit();

        $objtest = DB::select($test);

        return view('grader.teacher.view_result2',['test'=>$objtest]);

    }
    public function viewresult($id){



        $exam_id=$this->my_simple_crypt($id, 'd' );
        $course_name='';


       // echo  $id."___".$exam_id;//$_SESSION['15181'];
      //  exit();


        $sqlcount = "SELECT count(*) as total_ques
        FROM EXAM_STUDENT_PAPER
        left join EXAM_QUESTION_BANK on EXAM_QUESTION_BANK.id=EXAM_STUDENT_PAPER.question_bank_id
        where 1=1
        --and SAI_ENROLLMENT_NO='F2019-350'
        and COURSE_EXAM_PAPERS_ID=$exam_id
        and EXAM_QUESTION_BANK.EXAM_QUES_TYPE in (2,4)
        and EXAM_STUDENT_PAPER.MARKS_ENTER_STATUS is null";
        $ressqlcount = DB::select($sqlcount);
       // echo $sqlcount;exit();

       $sql = "SELECT
      exam_student_attendance.description,
      final_obtained_marks,
      exam_student_attendance.exam_status,
      exam_student_results_view.sai_enrollment_no,exam_paper_admission_sch_id,exam_student_results_view.sai_enrollment_no,
          CASE
              WHEN exam_paper_admission_sch_id > 0 THEN (
                  SELECT
                      api_name
                  FROM
                      admission_apply_info
                      LEFT JOIN admission_personal_info ON aai_api_id = admission_personal_info.api_id
                  WHERE
                      admission_apply_info.aai_id = exam_student_results_view.sai_enrollment_no
              )
              ELSE spi_name
          END
      AS spi_name,
      spi_name as mmname,
      correct,
      wrong,
      missed,
      total_questions,
      correct_marks,
      wrong_marks,
      missed_marks,
      exam_papers.id,
      exam_paper_admission_sch_id,
      (
          SELECT
              COUNT(*) AS total_ques
          FROM
              exam_student_paper
              LEFT JOIN exam_question_bank ON exam_question_bank.id = exam_student_paper.question_bank_id
          WHERE
                  sai_enrollment_no = exam_student_results_view.sai_enrollment_no
              AND
                  course_exam_papers_id = $exam_id
              AND
                  exam_question_bank.exam_ques_type IN (
                      2,4
                  )
              AND
                  exam_student_paper.marks_enter_status IS NULL
      ) AS result_status,
      CASE
              WHEN exam_paper_admission_sch_id > 0 THEN (
          SELECT
              api_mobile
          FROM
              admission_apply_info
              LEFT JOIN admission_personal_info ON aai_api_id = admission_personal_info.api_id
          WHERE
              aai_id = exam_student_results_view.sai_enrollment_no
      ) ELSE SPI_MOBILE END AS mobile_no,
      (
          SELECT
              SUM(question_marks)
          FROM
              exam_question_paper
          WHERE
              exam_question_paper.exam_papers_id = exam_student_results_view.course_exam_papers_id
      ) AS get_total_marks
  FROM
      exam_student_results_view
      LEFT JOIN students_apply_info ON LOWER(exam_student_results_view.sai_enrollment_no) = LOWER(students_apply_info.sai_enrollment_no)
      LEFT JOIN students_personal_info ON students_apply_info.sai_spi_id = students_personal_info.spi_id
      LEFT JOIN exam_papers ON exam_papers.id = exam_student_results_view.course_exam_papers_id
      LEFT JOIN exam_student_attendance ON
          exam_papers.id = exam_student_attendance.exam_id
      AND
          exam_student_attendance.sai_enrollment_no = students_apply_info.sai_enrollment_no
  WHERE
      course_exam_papers_id = $exam_id --1478
  ORDER BY spi_name";

        $users = DB::select($sql);
        $co_id='';
        $sql="select *from exam_papers where id=$exam_id";
        $sqlresult = DB::select($sql);

        if($sqlresult){
            $co_id=$sqlresult[0]->co_id;
            $course_name = DB::selectOne("select get_course_name($co_id) as value from dual");
            $course_name=$course_name->value;
          }

       $sqlf="SELECT count(*) as gh_exam_fsubmit FROM grade_history WHERE gh_co_id IN (SELECT co_id FROM courses_offered WHERE co_id = $co_id OR parent_co_id = $co_id)";
       $sqlfresult = DB::select($sqlf);

        return view('grader.teacher.view_result',['result'=>$users,'course_name'=>$course_name,'sqlresult'=>$sqlresult,'sqlfresult'=>$sqlfresult]);

      }
     public function oldviewresult($id){
        $exam_id=$this->my_simple_crypt($id, 'd' );

      $course_name='';

      $sqlcount = "SELECT count(*) as total_ques
      FROM EXAM_STUDENT_PAPER
      left join EXAM_QUESTION_BANK on EXAM_QUESTION_BANK.id=EXAM_STUDENT_PAPER.question_bank_id
      where 1=1
      --and SAI_ENROLLMENT_NO='F2019-350'
      and COURSE_EXAM_PAPERS_ID=$exam_id
      and EXAM_QUESTION_BANK.EXAM_QUES_TYPE in (2,4)
      and EXAM_STUDENT_PAPER.MARKS_ENTER_STATUS is null";
      $ressqlcount = DB::select($sqlcount);
     // echo $sqlcount;exit();

     $sql = "SELECT
    exam_student_attendance.description,
    final_obtained_marks,
    exam_student_attendance.exam_status,
    exam_student_results_view.sai_enrollment_no,exam_paper_admission_sch_id,exam_student_results_view.sai_enrollment_no,
        CASE
            WHEN exam_paper_admission_sch_id > 0 THEN (
                SELECT
                    api_name
                FROM
                    admission_apply_info
                    LEFT JOIN admission_personal_info ON aai_api_id = admission_personal_info.api_id
                WHERE
                    admission_apply_info.aai_id = exam_student_results_view.sai_enrollment_no
            )
            ELSE spi_name
        END
    AS spi_name,
    spi_name as mmname,
    correct,
    wrong,
    missed,
    total_questions,
    correct_marks,
    wrong_marks,
    missed_marks,
    exam_papers.id,
    exam_paper_admission_sch_id,
    (
        SELECT
            COUNT(*) AS total_ques
        FROM
            exam_student_paper
            LEFT JOIN exam_question_bank ON exam_question_bank.id = exam_student_paper.question_bank_id
        WHERE
                sai_enrollment_no = exam_student_results_view.sai_enrollment_no
            AND
                course_exam_papers_id = $exam_id
            AND
                exam_question_bank.exam_ques_type IN (
                    2,4
                )
            AND
                exam_student_paper.marks_enter_status IS NULL
    ) AS result_status,
    CASE
            WHEN exam_paper_admission_sch_id > 0 THEN (
        SELECT
            api_mobile
        FROM
            admission_apply_info
            LEFT JOIN admission_personal_info ON aai_api_id = admission_personal_info.api_id
        WHERE
            aai_id = exam_student_results_view.sai_enrollment_no
    ) ELSE SPI_MOBILE END AS mobile_no,
    (
        SELECT
            SUM(question_marks)
        FROM
            exam_question_paper
        WHERE
            exam_question_paper.exam_papers_id = exam_student_results_view.course_exam_papers_id
    ) AS get_total_marks
FROM
    exam_student_results_view
    LEFT JOIN students_apply_info ON LOWER(exam_student_results_view.sai_enrollment_no) = LOWER(students_apply_info.sai_enrollment_no)
    LEFT JOIN students_personal_info ON students_apply_info.sai_spi_id = students_personal_info.spi_id
    LEFT JOIN exam_papers ON exam_papers.id = exam_student_results_view.course_exam_papers_id
    LEFT JOIN exam_student_attendance ON
        exam_papers.id = exam_student_attendance.exam_id
    AND
        exam_student_attendance.sai_enrollment_no = students_apply_info.sai_enrollment_no
WHERE
    course_exam_papers_id = $exam_id --1478
ORDER BY spi_name";

   //echo $sql;exit;
      $users = DB::select($sql);
      //1562
        $test="SELECT *
        FROM
            exam_student_paper
        where course_exam_papers_id =1528
        order by SAI_ENROLLMENT_NO desc";
        $objtest = DB::select($test);
        return view('grader.teacher.view_result',['test'=>$objtest,'result'=>$users,'course_name'=>$course_name,'sqlresult'=>$sqlresult,'sqlfresult'=>$sqlfresult]);

        $co_id='';

      $sql="select *from exam_papers where id=$exam_id";
      $sqlresult = DB::select($sql);

      if($sqlresult){
          $co_id=$sqlresult[0]->co_id;
          $course_name = DB::selectOne("select get_course_name($co_id) as value from dual");
          $course_name=$course_name->value;
        }
 $sqlf="SELECT count(*) as gh_exam_fsubmit FROM grade_history WHERE gh_co_id IN (SELECT co_id FROM courses_offered WHERE co_id = $co_id OR parent_co_id = $co_id)";
 $sqlfresult = DB::select($sqlf);
     //echo  $sqlfresult[0]->gh_exam_fsubmit ;exit();

      return view('grader.teacher.view_result',['test'=>$objtest,'result'=>$users,'course_name'=>$course_name,'sqlresult'=>$sqlresult,'sqlfresult'=>$sqlfresult]);

    }
    public function checkpaper(Request $request,$roll_no){
      $id        = $request->get('id');
      $decrypt_roll_no=$this->my_simple_crypt($roll_no, 'd' );
      $decrypt_id=$this->my_simple_crypt($id,'d');
    $sqlcheckpaper = "select
                            question_bank_id,
                            course_exam_papers_id,
                            ceqp_id,
                            spi_name,question,
                            answer_status,
                            exam_student_paper.answer_given,
                            correct,get_course_name(co_id) as course_name,
                            oa,
                            ob,
                            oc,
                            od,
                            oe,
                            option_f,
                            og,
                            oh,
                            oi,
                            exam_student_paper.sai_enrollment_no,exam_question_bank.exam_ques_type,
                            QUESTION_OBTAINED_MARKS,
                            question_marks,
                            teacher_remarks,
                            exam_student_attendance.answer_file,
                            exam_student_paper.answer_file as one_in_one
      from exam_student_paper
      left join exam_question_bank on exam_student_paper.question_bank_id = exam_question_bank.id
      left join students_apply_info on exam_student_paper.sai_enrollment_no = students_apply_info.sai_enrollment_no
      left join students_personal_info on students_apply_info.sai_spi_id = students_personal_info.spi_id
      LEFT JOIN exam_question_paper ON exam_student_paper.question_bank_id          = exam_question_paper.exam_question_bank_id and exam_question_paper.EXAM_PAPERS_ID  = course_exam_papers_id
      left join exam_student_attendance on exam_student_paper.course_exam_papers_id =  exam_student_attendance.exam_id
      and exam_student_paper.sai_enrollment_no = exam_student_attendance.sai_enrollment_no
      where exam_student_paper.sai_enrollment_no = '$decrypt_roll_no'
      and course_exam_papers_id = $decrypt_id
      order by answer_status, exam_ques_type desc";
      // echo  $sqlcheckpaper;exit;
      $users = DB::select($sqlcheckpaper);
      $sql="select *from exam_papers where id=$decrypt_id";
      $sqlresult = DB::select($sql);
      return view('teacher.check_paper',['result'=>$users,'sqlresult'=>$sqlresult]);

    }
    public function tocms($id){
      $exam_id=$this->my_simple_crypt($id, 'd' );

      $sqlselect = "select * from exam_papers where id = $exam_id and exam_move_cms = 'NO' ";
      $users = DB::select($sqlselect);
      $move_cms = count($users);

      if($move_cms == 1){

        $procedurenotification = 'exam_marks_move_cms';
        $bindings_notification = [
        'exam_id'           => $exam_id,
        ];
        $result_notification = DB::executeProcedure($procedurenotification, $bindings_notification);

        DB::table('exam_papers')
        ->where('id', $exam_id)
        ->update([
          'exam_move_cms' => 'YES'
          ]);

        return 1;

      }else{

        return 0;
      }
    }
    public function advance_setting(Request $request,$paper_id){
     //echo $paper_id;exit;
    $reattempt        = $request->get('re_attempt');
    $review        = $request->get('re_view');
    $exam_pap_type        = $request->get('exam_paper_type');
    $neg_marks        = $request->get('neg_marks');
    $ccp        = $request->get('ccp');
    $neg_marks_hidden        = $request->get('neg_marks_hidden');
    $file_require        = $request->get('file_require');
    if($neg_marks != $neg_marks_hidden){
    $sql_count_neg_marks = "select sum(neg_marks) as total from exam_question_paper where exam_papers_id = $paper_id";
    $res_count_neg_marks = DB::select($sql_count_neg_marks);

    $countneg = $res_count_neg_marks[0]->total;

     if($countneg > 0){

      return 1;
     }else{

     DB::table('exam_papers')
     ->where('id', $paper_id)
     ->update([
       're_attempt' => $reattempt,
       're_view' => $review ,
       'paper_attmp_type' => $exam_pap_type,
       'NEGATIVE_MARKS' => $neg_marks ,
       'copy_paste_allowed' => $ccp,
       'exam_answer_file' => $file_require
       ]);

    }
  }
    DB::table('exam_papers')
     ->where('id', $paper_id)
     ->update([
       're_attempt' => $reattempt,
       're_view' => $review ,
       'paper_attmp_type' => $exam_pap_type,
       'copy_paste_allowed' => $ccp,
       'exam_answer_file' => $file_require
       ]);



  }
  public function update_ques_marks(Request $request,$ceqp_id,$roll_no,$paper_id,$obt_marks)
  {

     DB::table('exam_student_paper')
      ->where('ceqp_id','=', $ceqp_id)
      ->where('sai_enrollment_no','=', $roll_no)
      ->where('course_exam_papers_id','=', $paper_id)
      ->update([
        'QUESTION_OBTAINED_MARKS' => $obt_marks,
        'MARKS_ENTER_STATUS' => 1
        ]);


     }

     public function studentpaper(Request $request){
     // dd($request->all());


        $enroll_no          = $request->get('roll_no');
        $paper_id           = $request->get('paper_id');
        $desc               = $request->get('desc');
        $paper_status       = $request->get('paper_status');

         // echo $paper_status;exit();
if($paper_status>5){
    $exam_paper_status = 0 ;
    if($paper_status==7){
        $exam_paper_status = 3 ;
    }elseif($paper_status == 8){
        $exam_paper_status = 4 ;
    }
    DB::table('exam_student_attendance')
    ->where('exam_id','=', $paper_id)
    ->update([
      'exam_status' => $exam_paper_status,
      'description' => $desc
      ]);
}else{
    DB::table('exam_student_attendance')
    ->where('sai_enrollment_no','=', $enroll_no)
    ->where('exam_id','=', $paper_id)
    ->update([
      'exam_status' => $paper_status,
      'description' => $desc
      ]);
}


     }

     public function teacher_remarks(Request $request)
  {
    $comment=$request->get('comments');
    $ceqp_id=$request->get('cepq');

     DB::table('exam_student_paper')
      ->where('ceqp_id','=', $ceqp_id)

      ->update([
        'teacher_remarks' => $comment
        ]);


     }
     public function CheckStudentAttendance(Request $request,$id,$co_id)
     {

        if($co_id != '0'){

         $co_id=$this->my_simple_crypt($co_id, 'd' );

        }

       $sql = "SELECT
       CASE
         WHEN EXTRACT(SECOND FROM diff)      < 0
         AND EXTRACT(SECOND FROM before_time)>0
         AND SUBSTR(exam_date,0,9)           = SUBSTR(sysdate,0,9)
         THEN 'start'
         WHEN SUBSTR(exam_date,0,9) < SUBSTR(sysdate,0,9)
         THEN 'passed'
         WHEN SUBSTR(exam_date,0,9)          = SUBSTR(sysdate,0,9)
         AND EXTRACT(SECOND FROM diff)       > 0
         AND EXTRACT(SECOND FROM before_time)>0
         THEN 'passed'
         ELSE 'Scheduled'
       END          AS status



     FROM
       (SELECT co_id,
         title,
         total_questions,
         percentage_status,
         exam_pattern,
         exam_date,
         exam_start_time,
         exam_end_time,
         exam_duration,
         exam_papers.id AS exam_paper_id,
         systimestamp - to_timestamp(exam_start_time + (1 / 1440 * exam_duration) ) diff,
         systimestamp - to_timestamp(exam_start_time) before_time
       FROM exam_papers
       WHERE 1          =1
       AND final_submit in (0,1)
       and exam_papers.id = $id
       AND ( co_id      =$co_id
       OR co_id         =
         (SELECT parent_co_id FROM courses_offered WHERE co_id = $co_id
         ))
       )
     ";
  // echo $sql;exit;
    $users = DB::select($sql);

   return $status = $users[0]->status;




     }
}
