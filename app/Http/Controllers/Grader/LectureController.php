<?php
namespace App\Http\Controllers\Grader;

use App\Http\Controllers\Controller;



// use Illuminate\View\Middleware\ShareErrorsFromSession;
use Illuminate\Http\Request;
// use App\Models\FacultyCourses;
// use App\Models\CourseAttendance;
// use App\Models\CourseAssessmentMarks;
// use App\sessions;
// use App\Models\Schools;
// use App\Models\EmpEmployees;
// use App\Models\Departments;
// use App\Models\UserNotifications;
// use DB;
// use App\Models\SendSms;
// use App\Models\CourseCriterias;
// use App\Models\CourseCriteriaDetails;
// Use Exception;
// use Illuminate\Support\Facades\Log;



class LectureController extends Controller{

    private $sortBy = 'id';
    private $sortDirection = 'ASC';

    private $facultyCourse;

    // public function __construct(FacultyCourses $facultyCourses)
    // {
    //     $this->facultyCourse = $facultyCourses;
    //     //$this->middleware('auth');
    // }


    function lectureWiseView(Request $request, $coID){
        return view('grader.attendance/lecture_wise_view',['coID' => $coID]);
    }


    public  function course_attendance_index(Request $request,$co_id){


        $get_slot_list = $this->loadClassDates($co_id,$tt_id=0);
       if(count($get_slot_list)==0){
           return '<div style="margin-left: auto; margin-right: auto; text-align: center;">Timetable not created please contact to Registrar Office</div>';
       }else {
           $get_slot_ip = $get_slot_list[0]->room_ip;
           $slot_ip_yes = "";
           $slot_id_now = "";
           foreach ($get_slot_list as $row_slot_list) {
                if ($row_slot_list->today_class_status == "YES") {
                   if ($request->ip() == $row_slot_list->room_ip or 1==1) {
                   //     echo $row_slot_list->today_class_status."__".$request->ip()."__".$row_slot_list->room_ip;
                       $slot_ip_yes = "YES";
                   } else {
                       $slot_ip_yes = "NO";
                   }
                    $slot_id_now = $row_slot_list->tt_id;

               }
           }
           return view('grader.attendance/attendance',
               [
                   'class_date' => $this->loadClassDates($co_id, $tt_id = 0),
                   'slot_ip_yes' => $slot_ip_yes,
                   'slot_id_now' => $slot_id_now
               ]
           );
       }
    }
   


    function notification($co_id,$message,$user_id,$target_page){
        // echo $co_id."___".$message."__".$user_id;exit();
        try {
         $procedurenotification = 'notification';
         $bindings_notification = [
               'co_id'  => $co_id,
               'desc'  => $message,
               'user_id'  =>$user_id,
               'target_page'=>$target_page
 
           ];
      return $result_notification = DB::executeProcedure($procedurenotification, $bindings_notification);    
     } catch (Exception $e) {
         //echo $e;
       //  echo 'no';
         // This calls the render() method of `App\Exceptions\Handler`
     }
    }
    public function retrieve($sess_id=0,$dept_id=0,$email='')
    {
        return $this->facultyCourse->fetch($sess_id,$dept_id,$email);
    }

    public function retrieveCourses()
    {
        return $this->facultyCourse->fetchDashboardCourses();
    }

    public function check_assignment($course_criteria_detail_id,$co_id)
    {
        $courseCriteria = new CourseCriterias();
        $get_criteria = $courseCriteria->fetchCoursesCriteria($co_id);

        $my_course_attendance = new CourseAssessmentMarks();
        $student_data         = $my_course_attendance->AssessmentChecking($course_criteria_detail_id,$co_id);
        return view('grader.teacher.check_assignment_stu',['course_criteria'=>$get_criteria,'show_student_data'=>$student_data,'new_assessment_entry'=>$course_criteria_detail_id]);

      // return $student_data;




//        $my_course_attendance = new CourseAttendance();
//        $student_data                  = $my_course_attendance->show_students(210979,11166,'NO');
//        return view('teacher.check_assignment_stu',['show_student_data'=>$student_data]);
    }

    /**
     * @return FacultyCourses
     */
    public function course_contentPost(Request $request)
    {
        $file_full_name='';
        $file_name='';
        $sess = new sessions();
        $sess->startSession();


        $fc_co_id = $request->get('fc_co_id');
        $description = $request->get('description');
        $course_id = $request->get('course_id');
        $fc_co_sess = $request->get('fc_co_sess');
        $Lectno = $request->get('Lectno');
        $lec_content = $request->get('lec_content');
        $id = $request->get('id');

        $con_id = $request->get('con_id');
        $assessment= $request->get('assessment');
        $weightage = $request->get('weightage');
        $login_id = $sess->emp_user_id;
        $sess_id = $sess->active_session;

        if($request->final_status==1){
           // dd($request->final_status);
            $fc_course_id=$request->courses_idd;
            $sqlupdatecontent = "UPDATE COURSE_CONTENTS
                                      SET final_status = '1'
                                          
                                      WHERE  cc_course_id='$fc_course_id'";
           // echo $sqlupdatecontent;exit();
            $result = DB::insert($sqlupdatecontent);

        }
            if($description!='' && $id==''){
                //dd($description);
                 $sqlcourseassessment = "select *from COURSE_CONTENTS where cc_course_id='$course_id' and cc_type='description'";
        
        // echo $sqlcourseweightage;exit;
         $result = DB::select($sqlcourseassessment);
                $totassessment = count($result);
                
                 if($totassessment>0){
                    $res = array('status' => true, 'message' => 'Description already added!');
                     return response()->json($res);
                    exit();
                }
                $sqlcourseContent = "INSERT INTO COURSE_CONTENTS(	
													  
												      
														CC_CO_ID,
														CC_COURSE_ID,
														CC_LECTURE_NO,
														CC_SESSION_ID,
														CC_TEACHER_ID,
														CC_LECTURE_CONTENT,
														CC_ENTRY_USER_ID,
														CC_FILE_ORIGINAL_NAME,
														CC_FILE_NAME,
														CC_ENTRY_DATETIME,
														CC_STATUS,
														CC_LECTURE_Description,
														CC_TYPE
													   
																								  
												  ) VALUES (
												      
														'$fc_co_id',
														'$course_id',
														'$Lectno',
														'$sess_id',
														'$sess->emp_user_id',		
														'$description',														
														'$login_id',
														'$file_full_name',
														'$file_name',
														CURRENT_TIMESTAMP,
														'E',
														'$description',
														'description'
														)";

                $result = DB::insert($sqlcourseContent);
                 //echo $sqlcourseContent;exit();
            }
            if($lec_content!='' && $con_id==''){
                //dd($lec_content);
                if ($request->hasFile('uploadfile')) {
                    //dd('hh');
                    ////////File Uplaod Code////////
                    $file = $request->file('uploadfile');
                    $file_name = $file->getClientOriginalName();
                    $name = $file->getClientOriginalName();
                    //$file_full_name = $request->get('file_course_id') . '_'.'_' .'_'. $sess->emp_user_id . '.' . $file->getClientOriginalExtension();
                    $image['filePath'] = $name;

                    $file->move(public_path() . '/docs/', $file_name);
                    $filepath =( public_path() . '/docs/'. $file_name);

                    ////////File Uplaod Code End Here////////

                    $extension = $file->getClientOriginalExtension();
                    $fileSize = $file->getSize();


                    $valid_extension = array("csv");
                    $maxFileSize = 2097152;


                    // Check file extension
                    if(in_array(strtolower($extension),$valid_extension)) {

                        // Check file size
                        if ($fileSize <= $maxFileSize) {


                            // Reading file
                            $file = fopen($filepath, "r");

                            $importData_arr = array();
                            $i = 0;

                            while (($filedata = fgetcsv($file, 1000,',' )) !== FALSE) {
                                $num = count($filedata);

                                // Skip first row (Remove below comment if you want to skip the first row)
//                                if ($i == 0) {
//                                    $i++;
//                                    continue;
//                                }
                                for ($c = 0; $c < $num; $c++) {
                                    $importData_arr[$i][] = $filedata [$c];
                                }
                                $i++;
                            }
                            fclose($file);
                            // print_r($importData_arr);
                            //exit();
                            // Insert to MySQL database
                            foreach ($importData_arr as $importData) {

                                $sqlcourseContent = "INSERT INTO COURSE_CONTENTS(	
													  
												      
														CC_CO_ID,
														CC_COURSE_ID,
														CC_LECTURE_NO,
														CC_SESSION_ID,
														CC_TEACHER_ID,
														CC_LECTURE_CONTENT,
														CC_ENTRY_USER_ID,
														CC_FILE_ORIGINAL_NAME,
														CC_FILE_NAME,
														CC_ENTRY_DATETIME,
														CC_STATUS,
														CC_LECTURE_Description,
														CC_TYPE
													   
																								  
												  ) VALUES (
												       
														'$fc_co_id',
														'$course_id',
														'$Lectno',
														'$sess_id',
														'$sess->emp_user_id',		
														'$importData[0]',														
														'$login_id',
														'$file_full_name',
														'$file_name',
														CURRENT_TIMESTAMP,
														'E',
														'$description',
														'content'
														)";
                                $result = DB::insert($sqlcourseContent);

                            }
                        }

                    }


                }
                $sqlcourseContent = "INSERT INTO COURSE_CONTENTS(	
													  
												      
														CC_CO_ID,
														CC_COURSE_ID,
														CC_LECTURE_NO,
														CC_SESSION_ID,
														CC_TEACHER_ID,
														CC_LECTURE_CONTENT,
														CC_ENTRY_USER_ID,
														CC_FILE_ORIGINAL_NAME,
														CC_FILE_NAME,
														CC_ENTRY_DATETIME,
														CC_STATUS,
														CC_LECTURE_Description,
														CC_TYPE
													   
																								  
												  ) VALUES (
												       
														'$fc_co_id',
														'$course_id',
														'$Lectno',
														'$sess_id',
														'$sess->emp_user_id',		
														'$lec_content',														
														'$login_id',
														'$file_full_name',
														'$file_name',
														CURRENT_TIMESTAMP,
														'E',
														'$description',
														'content'
														)";
                $result = DB::insert($sqlcourseContent);
                // echo $sqlcourseContent;exit();
            }
            if($assessment!=''){
        $sqlcourseassessment = "select *from COURSE_CONTENTS where cc_course_id='$course_id' and cc_assessments='$assessment'";
        
       // echo $sqlcourseassessment;exit;
            $result = DB::select($sqlcourseassessment);
                $totassessment = count($result);
                if($totassessment>0){
                    $res = array('status' => true, 'message' => 'Assessment already added!');
                     return response()->json($res);
                    exit();
                }


                $sqlcourseweightage = "INSERT INTO COURSE_CONTENTS(	
													  
												      
														CC_CO_ID,
														CC_COURSE_ID,
														CC_LECTURE_NO,
														CC_SESSION_ID,
														CC_TEACHER_ID,
														CC_LECTURE_CONTENT,
														CC_ENTRY_USER_ID,
														CC_FILE_ORIGINAL_NAME,
														CC_FILE_NAME,
														CC_ENTRY_DATETIME,
														CC_STATUS,
														CC_LECTURE_Description,
														CC_TYPE,
														CC_WEIGHTAGE,
														CC_ASSESSMENTS
														
													   
																								  
												  ) VALUES (
												       
														'$fc_co_id',
														'$course_id',
														'$Lectno',
														'$sess_id',
														'$sess->emp_user_id',		
														'$lec_content',														
														'$login_id',
														'$file_full_name',
														'$file_name',
														CURRENT_TIMESTAMP,
														'E',
														'$description',
														'assessment',
														'$weightage',
														'$assessment'
														)";
                $result = DB::insert($sqlcourseweightage);
                // echo $sqlcourseContent;exit();

            }
            if ($id!=''&& $con_id=='') {
               // dd('des edit');
                $sqlupdatecontent = "UPDATE COURSE_CONTENTS
                                      SET CC_LECTURE_NO = '$Lectno',
                                          CC_LECTURE_CONTENT = '$description'
                                          
                                      WHERE  id='$id'";
                $result = DB::insert($sqlupdatecontent);
               // echo $sqlupdatecontent;exit;
            }
            if($con_id!=''&& $id==''){
               //dd($lec_content);
                $sqlupdatecontent = "UPDATE COURSE_CONTENTS
                                      SET CC_LECTURE_NO = '$Lectno',
                                          CC_LECTURE_CONTENT = '$lec_content'
                                      WHERE  id='$con_id'";
                //echo $sqlupdatecontent;exit;
                $result = DB::insert($sqlupdatecontent);
            }

    }
    ////delete content

    function delete_faculity_course_content($id)
    {
        $obj = DB::table('course_contents')->where('ID', '=', $id)->delete();

        $res = array('status' => true, 'message' => 'Attachment Delete successfully!');
        return response()->json($res);


    }
    public function course_content_record($co_id)
    {
        $course = "select co_course_id from courses_offered where co_id = $co_id ";
        $resultcourse = DB::select($course);
        $my_course_id =  $resultcourse[0]->co_course_id;
        // exit();
        $sql="

select * from (
SELECT id,cc_type,
cc_lecture_no,
cc_lecture_content,
    id as coc_id,
     1 as type,
      (SELECT COUNT(*)
    FROM course_offering_contents
    WHERE coc_course_content_id = COURSE_CONTENTS.id 
    and course_offering_contents.coc_continuation=1
    and coc_content_type = 1
    AND coc_co_id = $co_id
    )
    AS coc_continuation,
      (SELECT COUNT(*)
    FROM course_offering_contents
    WHERE coc_course_content_id = COURSE_CONTENTS.id 
    and course_offering_contents.coc_continuation=1
    and course_offering_contents.coc_covered = 1
    AND coc_co_id = $co_id
    and coc_content_type = 1)  AS coc_covered,
     1 as coc_content_type
    
FROM COURSE_CONTENTS
where cc_course_id = $my_course_id 
  union all
 SELECT
    (SELECT MIN(id) FROM COURSE_CONTENTS WHERE cc_co_id = $co_id
    )         AS tt_id,
    'content' AS cc_type,
    (SELECT MIN(id) FROM COURSE_CONTENTS WHERE cc_co_id = $co_id
    )                AS cc_lecture_no,
    COC_DETAILS AS cc_lecture_content,
    coc_course_content_id AS coc_id,
    2 as type,
    to_number(coc_continuation),
    COALESCE (to_number(coc_covered),0),
    2 as coc_content_type
  FROM COURSE_OFFERING_CONTENTS
  WHERE coc_co_id       = $co_id
  AND COC_DETAILS IS NOT NULL
  and coc_content_type = 2
    and coc_content_entry_type = 2

 
  )
  
order by id    


";

        $result = DB::select($sql);

        // echo $sql;exit;
        $teacher_courses = $this->facultyCourse->fetchDashboardCourses($co_id);
        return $result;
    }
    public function course_content($co_id)
    {

        $sqlassessment = "select * from Assessments order by id";
        $sqlassessment = DB::select($sqlassessment);

        $course_idd='';
        $teacher_courses = $this->facultyCourse->fetchDashboardCourses();

        foreach ($teacher_courses as $teacher_course){
            if($teacher_course->fc_co_id==$co_id){

                $course_idd=$teacher_course->fc_co_course_id;
            }
        }
        $sql="select ASSESSMENTS.assessment_name,COURSE_CONTENTS.cc_weightage,cc_type,cc_lecture_content,COURSE_CONTENTS.id,COURSE_CONTENTS.final_status from COURSE_CONTENTS
                LEFT JOIN ASSESSMENTS ON COURSE_CONTENTS.CC_ASSESSMENTS = ASSESSMENTS.ID
                where COURSE_CONTENTS.CC_COURSE_ID=$course_idd order by ASSESSMENTS.id ";
        $result = DB::select($sql);
        $countcontents = count($result);

       // dd($result);
        $sqlcontent="select * from COURSE_CONTENTS where CC_COURSE_ID=$course_idd and cc_type='content' order by id";
        $sqlcontentresult = DB::select($sqlcontent);
       // echo $sqlcontent;exit;

        return view('grader.teacher.course_content',['teacher_courses'=>$teacher_courses,
                                                    'courses_content'=>$result,
                                                    'countcontents'=>$countcontents,
                                                    'course_id'=>$co_id,
                                                    'assessments'=>$sqlassessment,
                                                    'sqlcontentresult'=>$sqlcontentresult]);
    }


    /**
     * @return stdupdatemarks_assignment
     */
    ////////// update markx /////
     public function checkAssessment($co_id, $assessment_id)
    {

        $sqlcontent="select * from messages_bnu where co_id=$co_id and COURSE_ASSESSMENT_ID='$assessment_id' order by id";
        return $sqlcontentresult = DB::select($sqlcontent);


    }   

    public function stdupdatemarks_assignment(Request $request)
    {
       
        $sess = new sessions();
        $sess->startSession();
        $flag=0;
        $login_id   = $sess->emp_user_id;
        $obmarks = $request->get('ob-marks');
        $comment = $request->get('comment');
        $idkl = $request->get('idkl');
        $course_criteria_id = $request->get('course_criteria_id');
        $co_id = $request->get('co_id');
        $sai_enrollment = $request->get('sai_enrollment');
        $total_marks= $request->get('total_marks');
        $assess= $request->get('assess_id');
        $cam_number= $request->get('assess_number');
        $file = $request->file('uploadfile');

        $sql_assessment = "select assessment_name,ccd_number from course_criteria_details 
        left join assessments on course_criteria_details.assessment_id = assessments.id
        where co_id = $co_id and course_criteria_id = $course_criteria_id and ccd_number = $cam_number and assessment_id = $assess";

        $result_assessment = DB::select($sql_assessment);
        $assessment_name = $result_assessment[0]->assessment_name;
        $camnumber = $result_assessment[0]->ccd_number;
   ////////////////////////////////////////////////////////////////
         $query="select OBTAINED_MARKS from Course_assessment_marks WHERE  id='$idkl'";
        $rset = DB::select($query);
         $get_markx = $rset['0']->obtained_marks;
         if($get_markx !=''){
                $flag=1;
         }

/////////////////////////////////////////////////////////////////////////

          $sqlupdateassignmentmarks = "UPDATE Course_assessment_marks
                                      SET OBTAINED_MARKS = '$obmarks',
                                          TEACHER_COMMENTS = '$comment',
                                          SUBMISSION_STATUS='GRADED'
                                      WHERE  id='$idkl'";
      $result = DB::insert($sqlupdateassignmentmarks);


      /////send sms to faculty ////////////////////////////////////
       $sms = new SendSms();
       $get_course_teacher=$sms->get_course_teacher($co_id, $login_id);

  if(CourseCriteriasController::checkLockStatus($co_id)!=1 && $flag==1){
          
           //$get_course_teacher=$sms->get_course_teacher($co_id, $login_id);
            $employee_email = $sess->emp_email;
             $phone_no=$sess->phone_no;
            $countlength = strlen($phone_no);
            $chknumber = substr($phone_no,0,2);

         if($countlength == 12){
            if($chknumber == 92){
                $to=$employee_email;
                $subject='Student Marks Changed Alert !'.$get_course_teacher;
                $mess=' Student Marks Changed '.$sai_enrollment .' in '.$assessment_name.' no '.$camnumber.' ( '.$obmarks.'/'.$total_marks. ' ) '.$get_course_teacher;


          $sms->send_sms('Marks change Alert ! ID '. $sai_enrollment .' in '.$assessment_name.' no '.$camnumber.' '.$obmarks.'/'.$total_marks. ' '.$get_course_teacher,$phone_no);

            $sms->teacher_marks_change_sendmail($to ,$subject,$mess);

            $flag=0;
              }
           }

        }else if($flag==0){

                //$to=$employee_email;
                $subject='Student Marks Enter Alert !'.$get_course_teacher;
                $mess=' Student Marks Entered '.$sai_enrollment .' in '.$assessment_name.' no '.$camnumber.' ( '.$obmarks.'/'.$total_marks. ' ) '.$get_course_teacher;
             //$sms->teacher_marks_change_sendmail($to ,$subject,$mess);


        }
/////send sms to faculty //////
      



        $procedurenotification = 'student_notification';
        $bindings_notification = [
            'main_co_id'           => $co_id,
            'message'              => $obmarks.' / '.$total_marks.' Updated by teacher in'.' '.$assessment_name.' no '.$camnumber,
            'short_message'        =>'Marks Enter',
            'student_enrollment_no'=>$sai_enrollment,
            'entry_user_id'        =>$login_id,
            'targe_page'           =>'assignment',
            'send_type'            =>'student',

        ];

        

     $result_notification = DB::executeProcedure($procedurenotification, $bindings_notification);
   // print_r($bindings_notification);exit;
    
        return response()->json(['success'=>'Marks Saved Successfully.']);
    }


    public function course_discussion($co_id)
    {
        
        $sql_student_data="SELECT
                            *
                        FROM
                            student_courses
                            LEFT JOIN courses_offered ON sc_co_id = co_id
                            LEFT JOIN courses_two on course_id = courses_offered.CO_COURSE_ID
                            LEFT JOIN students_apply_info ON student_courses.sc_sai_enrollment_no = students_apply_info.sai_enrollment_no
                            LEFT JOIN students_personal_info ON students_apply_info.sai_spi_id = students_personal_info.spi_id
                        WHERE
                                1 = 1
                            AND 
                                sc_enrollment_status=1
                            AND 
                                 sc_status not in ('Withdrawal')
                            AND (
                                    sc_co_id = $co_id
                                OR
                                    parent_co_id = $co_id
                                )
                            AND  sai_enrollment_no is not null   
                        ORDER BY spi_name";
                       
        $users=DB::select($sql_student_data);

       //  print_r($users); exit;

        $sqlselect = "select distinct un_description,emp_employees.emp_imagefile from user_notifications
                        left join emp_employees on un_entry_user_id = employee_id                        
                        where un_co_id='$co_id'
						and un_type ='notification' ";

                        
        $resselectun = DB::select($sqlselect);
		
		 
		
        //$users=array(); 
        return view('grader.course.course_discussion',['student_data'=>$users,'getnotification'=>$resselectun,'co_id'=>$co_id]);
    }
       public function classdiscussion(Request $request,$co_id){


        $sess       = new sessions();
        $sess->startSession();
        $login_id   = $sess->emp_user_id;
        $discussion = $request->get('discussion');


        $insertAr['un_description']     = $discussion;
        $insertAr['un_co_id']           = $co_id;
        $insertAr['un_entry_user_id']   = $login_id;
        $insertAr['un_type']            = 'notification';
        DB::table('user_notifications')->insert($insertAr);
		
		$this->notification($co_id,$discussion,$login_id,'notification');
		
		$sqlStudentApplication = "INSERT INTO messages_bnu(
                                                   message,
                                                   co_id,
                                                   entry_user_id) VALUES (
                                                     '".$discussion."',
                                                   '$co_id',
                                                   '".$login_id."'                                               
                                                   )";
        $result = DB::insert($sqlStudentApplication);

        return response()->json(array('success' => 'Data Saved Successfully.'));

    }
    function sendMessageToStudentByTeacher(Request $request){

        $sess = new sessions();
        $sess->startSession();
        $login_id = $sess->emp_user_id;
        $discussion = $request->get('message');

       //  $sql    = "insert into user_notifications( un_description,un_co_id,un_entry_user_id) VALUES ( '$discussion', '$co_id', '$login_id')";
        $sis_id = DB::table('sisusers')->select('id')->where('username','=',strtolower($request->get('username')))->first();

        DB::table('user_notifications')->insert([
            'un_description' => $sess->emp_user_name." message : ".$discussion,
            'un_to_user_id' => $sis_id->id,
            'un_entry_user_id' => $login_id,
            'un_co_id' => $request->get('co_id'),
            'un_message_id' => $request->get('username'),
            'un_type' => 'message'
        ]);


        return response()->json(['success' => 'Data Saved Successfully.']);

    }



    function getMessageByTeacher(Request $request){
        
        $sess = new sessions();
        $sess->startSession();
        $sis_id         = DB::table('sisusers')->select('id')->where('username','=',strtolower($request->get('student-id')))->first();
        // print_r($sis_id); exit;
        $whereArr       = array('un_to_user_id' => $sis_id->id, 'un_entry_user_id' => $sess->emp_user_id);
        $notifications  = DB::table('user_notifications')->select('*')
        ->where($whereArr)
        ->get();

        return response()->json(['status' => true, 'message' => 'Data!', 'data' => $notifications]);
    }


    function delete_faculity_course_material_by_id($attachmentID){
        $obj = DB::table('faculty_course_uploads')->where('ID', '=', $attachmentID)->first();

        if($obj->type == 442){
            $path = public_path() . '/docs/' . $obj->file_original_name;
            @unlink($path);
        }else{

            $path = public_path() . '/docs/' . $obj->file_name;
            @unlink($path);


        }

        //print_r($obj); exit;

        DB::table('faculty_course_uploads')->where('ID', '=', $attachmentID)->delete();

        $res = array('status' => true, 'message' => 'Attachment Delete successfully!');
        return response()->json($res);
    }



    public function course_site($get_co_id)
    {
        function my_simple_crypt( $string, $action = 'e' ) {
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

        $co_id = my_simple_crypt($get_co_id, 'd' );
       /// echo $co_id;exit();
        $sess = new sessions();
        $sess->startSession();
     //    $login_id = $sess->emp_user_id;

        $obj = DB::table('faculty_courses')->where('FC_CO_ID', '=', $co_id)->where('FC_EMPLOYEE_ID', '=', $sess->emp_user_id)->first();


        if(!($obj)){
            abort(404, 'Course not found!.'); exit;
        }


        $courseCriteriaDetails = new CourseCriteriaDetails();
        // $get_criteriadetails = $courseCriteriaDetails->fetchCriteriaDetails(8267);
        $get_criteriadetails = $courseCriteriaDetails->fetchCriteriaDetails($co_id);
       // return $get_criteriadetails;exit();

        //return view('teacher.course_site'
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
       // echo $sql;exit();
        $res = DB::select($sql);
        $countres = count($res);



        $sql2 = "select title,
    entry_date_time,
      co_id,
    FACULTY_COURSE_UPLOADS.id
    from FACULTY_COURSE_UPLOADS where co_id =  $co_id";
        $resfiles = DB::select($sql2);
        // return $res2;


        $countsql=" select count(assessment_id) as count,assessment_id,assessment_name 
                    from course_criteria_details
                    left join assessments on course_criteria_details.assessment_id = assessments.id
                    where co_id = $co_id
                     AND FACULTY_COURSE_UPLOAD_ENTRY = '1'
                    group by assessment_id,assessment_name";
        $rescountsql = DB::select($countsql);

        $courseCriteria = new CourseCriterias();
        $get_criteria = $courseCriteria->fetchCoursesCriteria($co_id);

     //   return view('teacher.view_assignment',['all_files'=>$resfiles,'all_uploads'=>$res,'criteria'=>$get_criteria,'count'=>$rescountsql,'chkassess'=>$countres]);



     


       $coursecontent = $this->course_content_record($co_id);

        $sql_uploads = "select * from FACULTY_COURSE_UPLOADS where co_id = $co_id";
        $course_handouts = DB::select($sql_uploads);

        $CourseAttendanceModel  = new CourseAttendance();
        $get_slot_list = $CourseAttendanceModel->loadClassDatesModelDashboard($co_id,0);
        //return $get_slot_list;exit();
        // return view('teacher.course_site');

        $courseCriteria = new CourseCriterias();
        $get_criteria = $courseCriteria->fetchCoursesCriteria($co_id);
    // $get_criteria = $courseCriteria->fetchCoursesCriteria($co_id);


        $courseAssessmentMarks =  new CourseAssessmentMarks();
        $studentsList = $courseAssessmentMarks->fetchStudentsListNew($co_id);

        $sqlcourseofferingcontent = "select * from COURSE_OFFERING_CONTENTS where coc_co_id =  $co_id";
        $resfilescourseofferingcontent = DB::select($sqlcourseofferingcontent);



       return view('grader.teacher.course_site',['course_handouts'=>$course_handouts,
                                                'all_files'=>$resfiles,
                                                'all_uploads'=>$res,
                                                'criteria'=>$get_criteria,
                                                'count'=>$rescountsql,
                                                'chkassess'=>$countres,
                                                'studentsList'=>$studentsList,
                                                'get_criteria_details'=>$get_criteriadetails,
                                                'get_criteria'=>$get_criteria,
                                                'all_classes'=>$get_slot_list,
                                                'course_criteria'=>$get_criteria,
                                                'criteria_details'=>$get_criteriadetails,
                                                'content'=>$coursecontent,
                                                'courseofferingcontent'=>$resfilescourseofferingcontent]);
    }
//
//    // $CourseAttendanceModel  = new CourseAttendance();
//    //$get_slot_list = $CourseAttendanceModel->loadClassDatesModel(10972,0);
//$sess = new sessions();
//$sess->startSession();
//$faculty_school         = new Schools();
//$faculty_department     = new Departments();
//$profile                = new EmpEmployees();
//    // echo 'love';
//
//$employee_profile = EmpEmployees::with('user')->where('employee_id','=',$sess->emp_user_id)->first();
//    // dd($employee_profile);
//
//    // echo $sess->emp_user_id;exit();
//
//
//$all_notifications = new UserNotifications();
//$get_all_notifications = $all_notifications->fetch_notification($sess->emp_user_id);
//
//$profile = array(
//'full_name'         => $sess->emp_user_name,
//'school'            => $faculty_school->fetch()[$sess->emp_school_id],
//'department'        => $faculty_department->fetch()[$sess->emp_dept_id],
//'emp_imagefile'     => $employee_profile->emp_imagefile,
//'email'             => $employee_profile->emp_email,
//'mobile'            => $employee_profile->emp_contact_no,
//
//);
//
//$courseCriteria = new CourseCriterias();
//$get_criteria = $courseCriteria->fetchCoursesCriteria($co_id);
//    // $get_criteria = $courseCriteria->fetchCoursesCriteria($co_id);
//
//$courseCriteriaDetails = new CourseCriteriaDetails();
//    // $get_criteriadetails = $courseCriteriaDetails->fetchCriteriaDetails(8267);
//$get_criteriadetails = $courseCriteriaDetails->fetchCriteriaDetails($co_id);
//
//    // dd($get_criteriadetails);
//    //  return $get_criteriadetails;
//    // return $co_id;
//    // exit();
//
//$courseAssessmentMarks =  new CourseAssessmentMarks();
//$studentsList = $courseAssessmentMarks->fetchStudentsListNew($co_id);
//    // return $studentsList;exit();
//    // return $studentsList = $this->courseMarks->fetchStudentsList($co_id,$ass_type,$num);
//    // exit();
//$teacher_courses = $this->facultyCourse->fetchDashboardCourses();
//return view('teacher.view_assginment2',
//[
//'teacher_courses'=>$teacher_courses,
//'profile'=>$profile,
//'studentsList'=>$studentsList,
//'get_criteria_details'=>$get_criteriadetails,
//'get_criteria'=>$get_criteria,
//'all_notifications'=>$get_all_notifications
//]);
    /////////////////upload video & file/////////////

    public function uploadvideo(Request $request,$co_id,$lec_id,$tt_id)
    {
        $course_data='';
        //echo $co_id;
        $teacher_courses = $this->facultyCourse->fetchDashboardCourses();
        foreach($teacher_courses as $teacher_course){
            if($teacher_course->fc_co_id==$co_id){
                $course_id=$teacher_course->fc_co_course_id;
                 $sql_uploads = "select * from FACULTY_COURSE_UPLOADS where (co_id ='$course_id' or co_id='$co_id' )";

                $course_data = DB::select($sql_uploads);


            }
        }
       // print_r($teacher_courses);
       // dd($course_data);
       // $course_id=$teacher_courses[0]->fc_co_course_id;

        return view('grader.teacher.course_videoupload',['teacher_courses'=>$teacher_courses,'fc_co_id'=>$co_id,'course_data'=>$course_data,'lec_id'=>$lec_id,'tt_id'=>$tt_id]);
    }


    function add_faculity_course_material_by_id($co_id,$attachmentID){

        $sess = new sessions();
        $sess->startSession();
        $login_id = $sess->emp_user_id;
         $sql_update="UPDATE FACULTY_COURSE_UPLOADS
                SET CO_ID = '$co_id',
                    video_status = 1
                 where id='$attachmentID'";

        $course_update = DB::insert($sql_update);

        $this->notification($co_id,'Video Uploaded  ',$sess->emp_user_id,'coursematerial'); 
    }
    function remove_faculity_course_material_by_id($co_id,$attachmentID){


        $course_idd='';
        $teacher_courses = $this->facultyCourse->fetchDashboardCourses();

        foreach ($teacher_courses as $teacher_course){
            if($teacher_course->fc_co_id==$co_id){

                $course_idd=$teacher_course->fc_co_course_id;
            }
        }
        $sql_update="UPDATE FACULTY_COURSE_UPLOADS
                SET CO_ID = '$course_idd',
                    video_status = 0
                 where id='$attachmentID'";
        //echo $co_id."___".$attachmentID."___".$course_idd;
        //exit();

        $course_update = DB::insert($sql_update);

        // exit();
        // $res = array
        // return response()->json('sucess' => 'Attachment Video successfully!');
    }



    public function video_save(Request $request)
    {

        //dd('hello');

        $sess = new sessions();
        $sess->startSession();
        $login_id = $sess->emp_user_id;
        //dd( 'grgrg');
        $match='';
        $title = $request->get('video_title');
        $video_link = $request->get('videolink');
        $video_lec_no = $request->get('video_lec_no');
        $video_mtt_id = $request->get('video_mtt_id');
        $course_id = $request->get('course_id');
        $co_id = $request->get('co_id');

        if($video_link!=''){
           // dd('link add');
            $sqlUploadvideo = "INSERT INTO FACULTY_COURSE_UPLOADS(	
													  
														TITLE,
														DESCRIPTION,
														DUE_DATE,
														MARKS,
														LATE_SUBMISSION_ALLOWED,
														RE_SUBMISSION_ALLOWED,
														FILE_NAME,
														FILE_ORIGINAL_NAME,
														ENTRY_USER_ID,
													    TYPE,
													    CO_ID,
														ENTRY_DATE_TIME	,
														mtt_id,
														lecture_no,
														video_status
													 								  
												  ) VALUES (
												       
														'$title',
														'',
														'',														
														'',		
														'',														
														'',
														'$video_link',
														'$video_link',
														'$login_id',
														'443',
														'$course_id',
														CURRENT_TIMESTAMP,
														'$video_mtt_id',
														'$video_lec_no',
														'0'
														)";
            //echo $sqlUploadvideo;exit;
            $result = DB::insert($sqlUploadvideo);

            return response()->json(['success'=>'You have successfully upload file.']);
        }else if($video_link==''){

           //dd('video upload');
            if ($request->hasFile('file1')) {
                ////////File Uplaod Code////////
                $file = $request->file('file1');
                $file_name = $file->getClientOriginalName();
                $name = $file->getClientOriginalName();
                $file_full_name = $request->get('course_id').'_'.$file_name;
                $image['filePath'] = $name;
                $file->move(public_path() . '/docs/', $file_full_name);
                ////////File Uplaod Code End Here////////

            }
            $sqlUploadvideo = "INSERT INTO FACULTY_COURSE_UPLOADS(	
													  
														TITLE,
														DESCRIPTION,
														DUE_DATE,
														MARKS,
														LATE_SUBMISSION_ALLOWED,
														RE_SUBMISSION_ALLOWED,
														FILE_NAME,
														FILE_ORIGINAL_NAME,
														ENTRY_USER_ID,
													    TYPE,
													    CO_ID,
														ENTRY_DATE_TIME	,
														mtt_id,
														 lecture_no,
														video_status									  
												  ) VALUES (
												       
														'$title',
														'',
														'',														
														'',		
														'',														
														'',
														'$file_full_name',
														'$file_name',
														'$login_id',
														'444',
														'$course_id',
														CURRENT_TIMESTAMP,
														'$video_mtt_id',
														'$video_lec_no',
														'0'
														)";
            //echo $sqlUploadvideo;exit;
            $result = DB::insert($sqlUploadvideo);

            return response()->json(['success'=>'You have successfully upload file.']);
        }

        exit();


    }


    public function file_save(Request $request)
    {
        
        $sess = new sessions();
        $sess->startSession();
        $login_id = $sess->emp_user_id;
        //dd($request->course_id);
        $title = $request->get('file_title');
        $file_mtt_id = $request->get('file_mtt_id');
        $file_lec_no = $request->get('file_lec_no');
        $course_id = $request->get('file_course_id');

        date_default_timezone_set('Asia/karachi');
        $curr_time =date("His");

        $sqlsel = "  SELECT count(id)+1 as count  FROM FACULTY_COURSE_UPLOADS where co_id=$course_id and type=442";
        $result = DB::select($sqlsel);

        foreach ($result as $key) {
            $count=$key->count;
        }

        if ($request->hasFile('uploadfile')) {
            //dd('hh');
            ////////File Uplaod Code////////
            $file = $request->file('uploadfile');
            $file_name = $file->getClientOriginalName();
            $name = $file->getClientOriginalName();
            $file_full_name = $request->get('file_course_id') . '_'.$count.'_' .$curr_time.'_'. $sess->emp_user_id . '.' . $file->getClientOriginalExtension();
            $image['filePath'] = $name;

            ////////File Uplaod Code End Here////////

        }

        $sqlUploadfile = "INSERT INTO FACULTY_COURSE_UPLOADS(	
													  
														TITLE,
														DESCRIPTION,
														DUE_DATE,
														MARKS,
														LATE_SUBMISSION_ALLOWED,
														RE_SUBMISSION_ALLOWED,
														FILE_NAME,
														FILE_ORIGINAL_NAME,
														ENTRY_USER_ID,
													    TYPE,
													    CO_ID,
													    ENTRY_DATE_TIME,
													    lecture_no,
													    mtt_id
																								  
												  ) VALUES (
												       
														'$title',
														'',
														'',														
														'',		
														'',	
														'',
														'$file_name',
														'$file_full_name',
														'$sess->emp_user_id',
														'442',
														'$course_id',
														CURRENT_TIMESTAMP,
														'$file_lec_no',
														'$file_mtt_id'
														
														)";
        $result = DB::insert($sqlUploadfile);
    if($result==1){


       $file->move(public_path() . '/docs/', $file_full_name);
        if(file_exists(public_path() . '/docs/'.$file_full_name)) {
            $this->notification($course_id,'File Uploaded : '.$title,$sess->emp_user_id,'coursematerial');
            $data = array('status' => true, 'message' =>1);
            return response()->json($data);

        }else{

            $data = array('status' => true, 'message' => ' No Uploaded File Due to Network !');
            return response()->json($data);
        }


       // return response()->json(['success' => 'Data Saved Successfully.']);
    }else{
        $data = array('status' => true, 'message' =>0);
        return response()->json($data);
    }


        //exit;
        //return response()->json(['error' => 'Data Save Not Successfully.']);

    }

    public function send_sms($message){
        $sess = new sessions();
        $sess->startSession();
        $login_id = $sess->emp_user_id;

        //$message=' login @ grader '.date("l")." ". date("d-m-Y");

        $to = "";
        if($login_id=='3555' || $login_id=='42' || $login_id=='459' || $login_id=='539' ||  $login_id=='401' ||  $login_id=='239'  ||  $login_id=='475' ||  $login_id=='4055'){

            if($login_id=='459'){//'natash.ali'
                $to='923219464209,923017169678';
            }elseif($login_id=='539'){//'saad.saleem'
                $to='923364850065,923017169678';
            }elseif($login_id=='401'){//'shazia'
                $to='923454223437,923017169678';
            }elseif($login_id=='239'){//'huda.sarfraz'
                $to='923214513897,923017169678';
            }elseif($login_id=='475'){//'amna.humayun'
                $to='923244789674,923017169678';
            }elseif($login_id=='4055'){//nadeem_alvi
                $to='923414000140,923017169678';
            }elseif($login_id=='3555'){//zain.ali
                $to='923347280669,923017169678';
            }else{
                $to='923017169678';	//$to='923017169678';
                $message=' mna login @ grader '.date("l")." ". date("d-m-Y");;
            }
        }



        $type = "xml";
        $id = "rchbnuro";
        $pass ='786_kareemallahpak';
        $lang = "English";
        $mask ="BNU";


        //$message='nadeem.alivi login @'.date("l")." ". date("d-m-Y");
        //$to='923017169678';
        $data = "id=".$id."&pass=".$pass."&msg=".urlencode($message)."&to=".$to."&lang=".$lang."&mask=".$mask."&type=".$type;
        // Send the POST request with cURL
        $ch = curl_init('http://www.outreach.pk/api/sendsms.php/sendsms/url');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch); //This is the result from Outreach
        curl_close($ch);


    }

    /////////////////upload file &video///////////////////
    public function retrieveDashboardCourses2(Request $request)
    {
       $sess = new sessions();
        $sess->startSession();
        $emp_user = $sess->emp_dept_id;
        if($_SESSION["sisuser_id"] == 5193 or $_SESSION["sisuser_id"] == 5195 or $emp_user==13 or $emp_user==81 or $emp_user==101 or $emp_user==23 or $emp_user==42){
            $login_name = $sess->emp_user_name;
            $login_ip = $sess->login_ip;
            $mac = '';
            $description = 'login on Grader ';
            $pc = $_SERVER['HTTP_USER_AGENT'];
            $client_id = 2;
            $query_log = "insert into login_log(user_id,password,login_ip,status,pc,mac,description) values ('" . $login_name . "','','" . $login_ip . "','Success','" . $pc . "','" . $mac . "','" . $description . "')";
            $row_teacher_courses = DB::insert($query_log);

            $CourseAttendanceModel = new CourseAttendance();
            $get_slot_list = $CourseAttendanceModel->loadClassDatesModel(10972, 0);

            //return $get_slot_list;

            $sess = new sessions();
            $sess->startSession();
            $faculty_school = new Schools();
            $faculty_department = new Departments();
            $profile = new EmpEmployees();
            // echo 'love';

            $employee_profile = EmpEmployees::with('user')->where('employee_id', '=', $sess->emp_user_id)->first();

            $all_notifications = new UserNotifications();
            $get_all_notifications = $all_notifications->fetch_notification($sess->emp_user_id);

            $profile = array(
                'full_name' => $sess->emp_user_name,
                'school' => $faculty_school->fetch()[$sess->emp_school_id],
                'department' => $faculty_department->fetch()[$sess->emp_dept_id],
                'emp_imagefile' => $employee_profile->emp_imagefile,
                'email' => $employee_profile->emp_email,
                'mobile' => $employee_profile->emp_contact_no,

            );


            $teacher_courses = $this->facultyCourse->fetchDashboardCourses();

            $teacher_timetable = "SELECT
                                        get_teachername(mtt_co_id) AS teacher_name,
                                        get_course_name(mtt_co_id) AS course_name,
                                        tt_subject_day,
                                        tt_subject_time_from,
                                        tt_subject_time_to,
                                        tt_class_date
                                    FROM
                                        master_timetable
                                    WHERE
                                            1 = 1
                                        AND
                                            substr(tt_class_date,1,10) = substr(
                                                TRIM(SYSDATE),
                                                1,
                                                10
                                            )
                                        AND
                                            mtt_co_id IN (
                                                SELECT
                                                    faculty_courses.fc_co_id
                                                FROM
                                                    faculty_courses
                                                WHERE  fc_co_sess_id = '".$sess->active_session."'
                                                AND    faculty_courses.fc_employee_id = '".$sess->emp_user_id."'
                                                AND    fc_status = 'E'
                                            )
                                        AND
                                            tt_status = 'E'
                                    ORDER BY tt_class_date";
            $row_teacher_courses = DB::select($teacher_timetable);


            // return $row_teacher_courses;exit();
            return view('grader.teacher.teacher_dashboard2',
                [
                    'teacher_courses' => $teacher_courses,
                    'all_classes' => $get_slot_list,
                    'profile' => $profile,
                    'timetable' => $row_teacher_courses,
                    'all_notifications' => $get_all_notifications,

                ]);

        }else{

            return redirect('teacher/coursesold');

        }
    }
    public function retrieveDashboardCourses(Request $request){
        
      //  exit();

      

        $sess = new sessions();
        $sess->startSession();
        $login_id = $sess->emp_user_id;

        //  echo  $group_id = $sess->emp_dept_id;exit;
        $login_name=$sess->emp_user_name;
        $login_ip=$sess->login_ip;
        $mac = '';
        $description = 'login on Grader ';
        $pc = $_SERVER['HTTP_USER_AGENT'];
        $client_id = 2;
        $query_log="insert into login_log(user_id,password,login_ip,status,pc,mac,description) values ('".$login_name."','','".$login_ip."','Success','".$pc."','".$mac."','".$description."')";
        $row_teacher_courses = DB::insert($query_log);
        $co_id = $request->get('co_id');

        if($co_id != ''){
            $courseList = $this->facultyCourse->fetchDashboardCourses($co_id);
        }else{
            $courseList = $this->facultyCourse->fetchDashboardCourses();
        }



        //return $courseList;exit();
        $report = ([
            'success' => 'Data saved Successfully',
            'error' => false,
            'error_details' => false
        ]);
        // echo "as"; exit;
        return view('grader.teacher.courses', compact('courseList','report'));
    }

    public function retrieveDepartmentFaculty()
    {
        $facultyList = $this->facultyCourse->fetchDepartmentFaculty();
        return view('grader.teacher.course_hod_approval', compact('facultyList'));
    }

    public function retrieveTeacherCourses($sess_id,$fc_emp_id)
    {
        return $coursesList = $this->facultyCourse->fetchTeacherCourses($sess_id,$fc_emp_id);
    }

    public function uploadPostAssessment(Request $request,$fcu_id)
    {
        
        $sess = new sessions();
        $sess->startSession();
        $login_id = $sess->emp_user_id;


        ////////File Uplaod Code////////
        if ($request->hasFile('uploadfile2')) {
            $this->validate($request, [
                'uploadfile2'=>'required|max:50000|mimes:xlsx,docx'
            ]);
            $file = $request->file('uploadfile2');
           // echo $file->getClientOriginalExtension();
            $file_name = $file->getClientOriginalName();
            $name = $file->getClientOriginalName();
            $image['filePath'] = $name;
            $file->move(public_path() . '/docs/', $name);
            $file_full_name = $request->get('co_id') . '_' . $sess->emp_user_id . '_' . $file->getClientOriginalName();
        }else{
            $file_full_name = '';
            $file_name = '';


        }


        ////////File Uplaod Code End Here////////

        $title = $request->get('title2');
        $description = $request->get('description');
        $date = $request->get('duedate');
        $marks = $request->get('marks');
        $latesub = $request->get('latesub');
        $co_id = $request->get('co_id');
        $resub = $request->get('resub');
        $type = $request->get('assessment_id');
        if($fcu_id == 'insert'){
        $parts = explode('`', $type);

        $ccd_id = $parts[1];
         $assess_id = $parts[0];
        }
     //  echo $co_id = $request->get('co_id');
	 //  exit();
        $timestamp = strtotime($date);
        $newDate = date('Y-M-d', $timestamp);
        //$newDate = date('y-M-d', $timestamp);
        if($fcu_id == 'insert') {

                $sqlselect = "select * from course_criteria_details where co_id = $co_id and assessment_id = $assess_id";
                $resultselect = DB::select($sqlselect);
                $countresult = count($resultselect)+1;



                $sqlCCD = "INSERT INTO course_criteria_details(	
                                                        CCD_DETAILS,
														DESCRIPTION,
														DUE_DATE,
														MARKS,
														LATE_SUBMISSION_ALLOWED,
														RE_SUBMISSION_ALLOWED,
														FILE_NAME,
														FILE_ORIGINAL_NAME,
													    co_id,
														assessment_id,
														course_criteria_id,
														ccd_number,
														ccd_weightage,
														FACULTY_COURSE_UPLOAD_ENTRY,
														ccd_status
																								  
												  ) VALUES (
												        '$title',
														'$description',
														'$newDate',														
														'$marks',		
														'$latesub',														
														'$resub',
														'$file_full_name',
														'$file_name',												       													'$co_id',
														'$assess_id',
														'$ccd_id',
														'$countresult',
														'0',
														'1',
														'E'												
														
														)";
                $resultCCD = DB::insert($sqlCCD);
            $mess = 'Upload Assessment : '.$title;

           # this call function of notificaiton to send notificaotn in this page                                         
            $this->notification($co_id,$mess,$sess->emp_user_id);                                         
           #----------- end of notificaton funciton ------------------------                                         
           

                if($resultCCD){
                    $sqlcoursecriteriadetail = "select id from course_criteria_details 
                       where co_id = $co_id 
                       and assessment_id = $assess_id 
                       and ccd_number=$countresult";
                    $resultcoursecriteriadetail = DB::select($sqlcoursecriteriadetail);
                    //$resultcoursecriteriadetail[0]->id;
                    foreach($resultcoursecriteriadetail as $rec){
                        $co_criteria_det_id = $rec->id;
                    }
                    if($resultcoursecriteriadetail) {

                        $sqlCAM = "INSERT INTO course_assessment_marks
                    (
                    co_id,
                    sc_co_id,
                    assessment_id,
                    course_criteria_id,
                    TOTAL_MARKS,
                    cam_number,
                    SAI_ENROLLMENT_NO,
                    sai_id,
                    course_criteria_detail_id,
                    cam_status,
                    SUBMISSION_STATUS
                    )
                    SELECT 
                        '$co_id',
                         sc_co_id,
                        '$assess_id' as assessment_id,
                        '$ccd_id' as cc_id,
                        '$marks' as total_marks,
                        '$countresult' as cam_number,
                        SC_SAI_ENROLLMENT_NO,
                        sai_id,
                        '$co_criteria_det_id',
                        'E',
                        'NO'
                      
                    FROM STUDENT_COURSES
                    LEFT JOIN students_apply_info ON SC_SAI_ENROLLMENT_NO = SAI_ENROLLMENT_NO
                    WHERE SC_CO_ID IN
                    (SELECT CO_ID FROM COURSES_OFFERED WHERE CO_ID = $co_id
                    OR PARENT_CO_ID = $co_id
                    )
                    AND SC_STATUS IN ('A','Block')
                     and sai_id is not null";
                    $resultCAM = DB::insert($sqlCAM);
                       // echo $sqlCAM;exit;
                    }
                }
           // echo $sqlUploadAssignment;exit;
            return response()->json(['success' => 'Data Saved Successfully.']);
        }else{

            $mess = 'Assessment Modified : '.$title;

            # this call function of notificaiton to send notificaotn in this page                                         
            $this->notification($co_id,$mess,$sess->emp_user_id);                                         
            #----------- end of notificaton funciton ------------------------ 

            if($file_full_name == '' and $file_name == '') {


                $updtsql = "update course_criteria_details 
                        set   CCD_DETAILS              =  '$title',
                              DESCRIPTION              = '$description',
                              DUE_DATE                 = '$newDate',
                              MARKS                    = '$marks',	
                              LATE_SUBMISSION_ALLOWED  = '$latesub',	
                              RE_SUBMISSION_ALLOWED    = '$resub',                              
                              ASSESSMENT_ID            = '$type'
                        where id                       = $fcu_id";
                //echo $updtsql;exit;
                $res = DB::update($updtsql);

            }else {

                $updtsql = "update course_criteria_details 
                        set   CCD_DETAILS              =  '$title',
                              DESCRIPTION              = '$description',
                              DUE_DATE                 = '$newDate',
                              MARKS                    = '$marks',	
                              LATE_SUBMISSION_ALLOWED  = '$latesub',	
                              RE_SUBMISSION_ALLOWED    = '$resub', 
                              FILE_NAME                = '$file_full_name',
                              FILE_ORIGINAL_NAME       = '$file_name',   
                              ASSESSMENT_ID            = '$type'
                        where id                       = $fcu_id";

                $res = DB::update($updtsql);

            }
            if($res){

                 $sqlcheckstdmarks = "select MAX(obtained_marks) as obtained 
                                    from course_assessment_marks 
                                    where co_id = $co_id
                                    and course_criteria_detail_id = $fcu_id ";
                //echo $sqlcheckstdmarks;exit;
                $rescheckstdmarks = DB::select($sqlcheckstdmarks);
                $entered_obtain_marks = $rescheckstdmarks[0]->obtained;

                if($entered_obtain_marks <= $marks or $entered_obtain_marks =='') {

                    $updtcammarks = "update course_assessment_marks 
                        set   TOTAL_MARKS              =  '$marks'
                        where course_criteria_detail_id                       = $fcu_id";

                    $rescammarks = DB::update($updtcammarks);
                    return 1;
                }else{
                    return 0;
                }

            }


            return response()->json(['success' => 'Data Update Successfully.']);
        }
    }
    public function EditAssessment($id){

//        $ora = "select id,title,description,file_name,marks,late_submission_allowed,re_submission_allowed,type,TO_CHAR(due_date,'YYYY-MM-DD') as due_date from faculty_course_uploads where id = $id ";
//        $rec_ora = DB::select($ora);
//        return $rec_ora;

$ora = "select id,ccd_details,description,file_name,file_original_name,marks,is_assignment
,late_submission_allowed,re_submission_allowed,assessment_id
,TO_CHAR(due_date,'YYYY-MM-DD') as due_date
,(select max(total_marks) from course_assessment_marks 
where course_assessment_marks.co_id = course_criteria_details.co_id
and course_assessment_marks.course_criteria_detail_id = course_criteria_details.id
) as total_marks
 from course_criteria_details where id = $id ";
$rec_ora = DB::select($ora);

return $rec_ora;
    }

    public function viewAssessmenttest($co_id){

       // $CourseAttendanceModel  = new CourseAttendance();
        //$get_slot_list = $CourseAttendanceModel->loadClassDatesModel(10972,0);
        $sess = new sessions();
        $sess->startSession();
        $faculty_school         = new Schools();
        $faculty_department     = new Departments();
        $profile                = new EmpEmployees();
        // echo 'love';

        $employee_profile = EmpEmployees::with('user')->where('employee_id','=',$sess->emp_user_id)->first();
        // dd($employee_profile);

        // echo $sess->emp_user_id;exit();


        $all_notifications = new UserNotifications();
        $get_all_notifications = $all_notifications->fetch_notification($sess->emp_user_id);

        $profile = array(
            'full_name'         => $sess->emp_user_name,
            'school'            => $faculty_school->fetch()[$sess->emp_school_id],
            'department'        => $faculty_department->fetch()[$sess->emp_dept_id],
            'emp_imagefile'     => $employee_profile->emp_imagefile,
            'email'             => $employee_profile->emp_email,
            'mobile'            => $employee_profile->emp_contact_no,

        );

        $courseCriteria = new CourseCriterias();
        $get_criteria = $courseCriteria->fetchCoursesCriteria($co_id);
       // $get_criteria = $courseCriteria->fetchCoursesCriteria($co_id);

        $courseCriteriaDetails = new CourseCriteriaDetails();
       // $get_criteriadetails = $courseCriteriaDetails->fetchCriteriaDetails(8267);
        $get_criteriadetails = $courseCriteriaDetails->fetchCriteriaDetails($co_id);

       // dd($get_criteriadetails);
      //  return $get_criteriadetails;
       // return $co_id;
     // exit();

        $sql_uploads = "select * from FACULTY_COURSE_UPLOADS";
        $course_handouts = DB::select($sql_uploads);

        $courseAssessmentMarks =  new CourseAssessmentMarks();
        $studentsList = $courseAssessmentMarks->fetchStudentsListNew($co_id);
       // return $studentsList;exit();
        // return $studentsList = $this->courseMarks->fetchStudentsList($co_id,$ass_type,$num);
       // exit();
        $teacher_courses = $this->facultyCourse->fetchDashboardCourses();
        return view('grader.teacher.view_study_material',
            [
                'teacher_courses'=>$teacher_courses,
                 'course_handouts'=>$course_handouts,
                'studentsList'=>$studentsList,
                'get_criteria_details'=>$get_criteriadetails,
                'get_criteria'=>$get_criteria,
                'all_notifications'=>$get_all_notifications
            ]);

    }
    public function viewAssessment($co_id){

        $sql = "select ccd_details,due_date,assessment_name,get_course_name($co_id) as course_name,co_id ,course_criteria_details.id,marks
                from course_criteria_details
                left join assessments on assessment_id = assessments.id
                where co_id = $co_id
                AND FACULTY_COURSE_UPLOAD_ENTRY = '1'
                and ccd_details is not null";
       // echo $sql;exit();
        $res = DB::select($sql);
        $countres = count($res);

        $sql2 = "select title,
    entry_date_time,
      co_id,
    FACULTY_COURSE_UPLOADS.id
    from FACULTY_COURSE_UPLOADS where co_id =  $co_id";
        $resfiles = DB::select($sql2);
       // return $res2;
        //exit();

        $countsql=" select count(assessment_id) as count,assessment_id,assessment_name 
                    from course_criteria_details
                    left join assessments on course_criteria_details.assessment_id = assessments.id
                    where co_id = $co_id
                     AND FACULTY_COURSE_UPLOAD_ENTRY = '1'
                    group by assessment_id,assessment_name";
        $rescountsql = DB::select($countsql);

        $courseCriteria = new CourseCriterias();
        $get_criteria = $courseCriteria->fetchCoursesCriteria($co_id);

        return view('grader.teacher.view_assignment',['all_files'=>$resfiles,'all_uploads'=>$res,'criteria'=>$get_criteria,'count'=>$rescountsql,'chkassess'=>$countres]);

    }
    public function insertlecturecontent(Request $request,$tt_id){
        // echo $tt_id;exit;
        $getcontent = $request->get('getcontent');
        $id = $request->get('cc_id');
        $coc_content_type = $request->get('coc_content_type');
        $mtt_co_id = $request->get('mtt_co_id');
        $desc = $request->get('desc');

        $description=$getcontent.' '.$desc;


        $sqlcontinuation = "update COURSE_OFFERING_CONTENTS
        set coc_covered = 1,coc_continuation = 1
        where coc_co_id = '$mtt_co_id'
        and coc_continuation = 1
        and coc_covered is null
        and coc_continuation is not null";
        //echo $sqlcontinuation;exit;
        $res2 = DB::update($sqlcontinuation);


        $sql_cc_id = "update COURSE_OFFERING_CONTENTS
        set coc_covered =null,coc_continuation = 1
        where coc_co_id = '$mtt_co_id'
        and COC_COURSE_CONTENT_ID=".$id."
        and coc_content_type=".$coc_content_type;
        //echo $sqlcontinuation;exit;
        $res_cc_id = DB::update($sql_cc_id);


        $sqlinsertcontent = "INSERT INTO COURSE_OFFERING_CONTENTS(	
                                                      COC_DETAILS,
                                                       COC_TT_ID,
                                                       coc_co_id,
                                                       coc_continuation,
                                                       COC_COURSE_CONTENT_ID,
                                                       coc_content_type,
                                                       coc_content_entry_type
																								  
												  ) VALUES (
												      '$description',
												      '$tt_id',
												      '$mtt_co_id',
												      '1',
												      $id,
												      '".$coc_content_type."',
												      '1'
														
												  )";
        // echo  $sqlinsertcontent;exit;
        $resultCCD = DB::insert($sqlinsertcontent);


    }
    public function upadtenewlecturecontent(Request $request,$tt_id)
    {
        $add_new_desc = $request->get('add_new_desc');
        $mtt_co_id = $request->get('co_id');

        $sqlnewlecturecontent="select count(*)+1 as total from course_offering_contents where coc_content_type = 2";
        $ressqlnewlecturecontent = DB::select($sqlnewlecturecontent);
        $total_count =  $ressqlnewlecturecontent[0]->total;

        $sqlcontinuation = "update COURSE_OFFERING_CONTENTS
        set coc_covered = 1,coc_continuation = 1
        where coc_co_id = '$mtt_co_id'
        and coc_continuation = 1
        and coc_covered is null
        and coc_continuation is not null";
        //echo $sqlcontinuation;exit;
        $res2 = DB::update($sqlcontinuation);


        $sqladdnewinsertcontent = "INSERT INTO COURSE_OFFERING_CONTENTS(
                                                      COC_DETAILS,
                                                       COC_TT_ID,
                                                       coc_co_id,
                                                       coc_content_type,
                                                       coc_course_content_id,
                                                       coc_continuation

												  ) VALUES (
												      '$add_new_desc',
												      '$tt_id',
												      '$mtt_co_id',
												      '2',
												      '$total_count',
												      '1'


														)";
        //  echo  $sqladdnewinsertcontent;exit;
        $resultaddnewinsertcontent = DB::insert($sqladdnewinsertcontent);






    }
    public static function delete_criteria_assessment($co_id,$assessment_id)
    {

        $query_delete = "DELETE FROM COURSE_ASSESSMENT_MARKS 
                                WHERE assessment_id = $assessment_id
                                and  co_id IN (
                                    SELECT
                                        co_id
                                    FROM
                                        courses_offered
                                    WHERE
                                            co_id = ".$co_id."
                                        OR
                                            parent_co_id = ".$co_id."
                                ) ";
        // echo  $query_delete;exit();
        DB::delete($query_delete);

        $course_criterias  = "delete from course_criterias  WHERE co_id = $co_id  and assessment_id=$assessment_id ";
        DB::update($course_criterias);
        $COURSE_CRITERIA_DETAILS  = "delete from COURSE_CRITERIA_DETAILS  WHERE co_id = $co_id  and assessment_id=$assessment_id ";
        DB::update($COURSE_CRITERIA_DETAILS);

       
    }
    public function delete_course_content($coc_id,$coc_tt_id){

        $delcoursecontent = "delete from course_offering_contents where coc_id = $coc_id and coc_tt_id = $coc_tt_id";
        $resultcoursecontent = DB::delete($delcoursecontent);
        return 1;
    }

}
