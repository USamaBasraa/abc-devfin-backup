<?php
/**
 * Created by PhpStorm.
 * User: Maher Asad
 * Date: 5/14/2019
 * Time: 9:52 AM
 */
// \Notification
namespace App\Http\Controllers\Grader\Notification;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Symfony\Component\Console\Helper\Table;
use DB;
use App;
use Illuminate\Support\Facades\Input;
use App\sessions;
use Carbon\Carbon;
use App\Models\UserNotifications;
use Illuminate\Support\Facades\Log;



class NotificationController extends Controller{

    function sendMailOtpAdmission(Request $request){
        // echo "!@3";exit;
        //  $to = $request->get('email');
        //  $subject = 'Bnu Admission Signup OTP';
        //  $message = 'Your OTP is '.$request->get('otp');
        //  $display = 'BNU Admission OTP Verfication';

        if($request->get('otp')){
            $to = $request->get('email');
            $subject = 'Bnu Admission Signup OTP';
            $message = 'Please find below OTP text notification:
                        Your One-Time password OTP for admission to university is '.$request->get('otp').' Please use this code to proceed with your application. Thank You';
            $display = 'BNU Admission OTP Verfication';
    
           }else{
            $to = $request->get('email');
            $subject = 'URGENT: Documents Status for Admission Application';
            $message = 'Dear Applicant,
            Hope this email finds you well.
            We are currently in the process of reviewing your online admission application, and it has come
            to our attention that a few documents are still pending. To ensure the swift processing of your
            application, we kindly request your immediate attention to the following missing documents:
            1. '.$request->get('description').'
            Kindly upload above mentioned documents within the next 24 hours, your cooperation is greatly
            appreciated.
            Best regards,
            Admission Office';
            $display = 'BNU Admission Document Submission';
           }
        
         $procedurenotification = 'send_mail_one_by_one';
         $bindings_notification = [
         'to'           => $to,
         'subject'      => $subject ,
         'message'      => $message,
         'display'      => $display,
         ];
         return $result_notification = DB::executeProcedure($procedurenotification, $bindings_notification);
     }

    function getNotificationInAjax(){
        
        $sess = new sessions();
        $sess->startSession();

        $notifications = new UserNotifications();
        $data =  $notifications->fetch_notification($sess->emp_user_id);

        array_walk($data,function($obj,$index){
            $obj->profile_image = asset('staffimages/'.$obj->un_to_user_id.'.jpg');
        });

        
        return $data;

    }

    function updateViewedNotificationStatus($un_id){

        $updSql = "update user_notifications set viewed_notification = 1 where un_id ='$un_id'";
       // echo $updSql ;exit;
       $result = DB::insert($updSql);   
       if($result){
           echo '1';
       }else{
           echo '0';
       }   


    }

    
	function group_chat($co_id){
		
		 $sql = "select id,MESSAGES_BNU.ENTRY_USER_ID,spi_name,STUDENTS_APPLY_INFO.SAI_ENROLLMENT_NO,message,created_at from MESSAGES_BNU
                left join STUDENTS_APPLY_INFO
                on lower(SAI_ENROLLMENT_NO) = lower(MESSAGES_BNU.ENTRY_USER_ID)
                left join STUDENTS_PERSONAL_INFO
                on sai_spi_id = STUDENTS_PERSONAL_INFO.SPI_ID
                where co_id in (select parent_co_id from courses_offered where co_id = '".$co_id."'
                union all select co_id from courses_offered where  co_id ='".$co_id."') order by id desc";
                
		$data=DB::select($sql);
		return view('grader.course.class_discussion',['co_id'=>$co_id,'data'=>$data]);
	}
	
	function showNotificationPage($co_id){
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
        $sqlselect = "select distinct un_description,emp_employees.emp_imagefile from user_notifications
                        left join emp_employees on un_entry_user_id = employee_id                        
                        where un_co_id='$co_id'  AND un_type = 'notification'  ";
        $resselectun = DB::select($sqlselect);
        //$users=array(); 
        return view('grader.course.course_discussion',['student_data'=>$users,'getnotification'=>$resselectun,'co_id'=>$co_id]);
    }



    /**
       * 
       * Teacher Discussion wall & Notifications
       *
       * @param object $request  description
       * @return html
    */
    function teacherWall(Request $request){

        // echo \Carbon\Carbon::now(); exit;
        $co_id = $request->get('co_id');
         $data =  DB::table('courses_two')
            ->join('courses_offered', 'courses_two.course_id', '=', 'courses_offered.co_course_id')
            ->select('courses_two.course_cd_name')
            ->where('courses_offered.co_id', '=', $co_id)
            ->get();

             $courseStudent = "select sc_sai_enrollment_no ||' '|| spi_name as sc_sai_enrollment_no  from student_courses
             left join students_apply_info on sai_enrollment_no =student_courses.SC_SAI_ENROLLMENT_NO
             left join students_personal_info on students_apply_info.SAI_SPI_ID = students_personal_info.SPI_ID
             where sc_co_id in (select co_id from courses_offered where co_id=$co_id or parent_co_id=$co_id)";
            $courseStudent= DB::select($courseStudent);
            // ->where('sc_co_id', '=', $co_id)
            // ->get();

            //exit;
        return view('grader.wall.teacherwall',['course' => $data, 'courseStudent' => $courseStudent, 'coID' => $co_id]);
    }


    function insertMessage(Request $request){
        
        $sess       = new sessions();
        $sess->startSession();
        $login_id   = $sess->emp_user_id;
        $discussion = strip_tags($request->get('discussion'));

        

        $co_id      = $request->get('co_id');

        if($request->get('pid') != ''){
            $insertAr1['parent_message_id']         = $request->get('pid');
        }

        if($request->get('message_to') != ''){
            $to_user       =   DB::table('sisusers')
            ->where('username',strtolower($request->get('message_to')))
            ->first();
            $insertAr1['to_user_id']      = (String) $to_user->id;
            $insertAr1['private_comment'] = 1;
        }

        $insertAr1['message']         = $discussion;
        $insertAr1['co_id']           = $co_id;
        $insertAr1['entry_user_id']   = $login_id;
        $insertAr1['sender_name']     = $sess->emp_user_name;
        $insertAr1['type']            = 'notification';
        $insertAr1['created_at']      = Carbon::now();
        DB::table('messages_bnu')->insert($insertAr1);


        $insertAr['un_description']     = $discussion;
        $insertAr['un_co_id']           = $co_id;
        $insertAr['un_entry_user_id']   = $login_id;
        $insertAr['un_type']            = 'notification';
        // TARGET_PAGE
        $insertAr['target_page']            = 'notification';
        DB::table('user_notifications')->insert($insertAr);


        if (App::environment('production')) {
            $this->notification($co_id,$discussion,$login_id,$insertAr['target_page']);
         } else { 
            Log::info([$co_id,$discussion,$login_id,$insertAr['target_page']]);
         } 
        return response()->json(array('status' => true, 'success' => 'Data Saved Successfully.'));
    }




    function getMessages(Request $request){
        $sess       =   new sessions();
        $sess->startSession();
        $login_id   =   $sess->emp_user_id;
        $co_id      =   $request->get('co_id');

        $whereArr = array();

        $whereArr['co_id'] = $co_id;


        if($request->get('message_to') != ''){
            $to_user       =   DB::table('sisusers')
            ->where('username',strtolower($request->get('message_to')))
            ->first();
            $whereArr['to_user_id']      = $to_user->id;
            $whereArr['private_comment'] = 1;
        }

      //  print_r($whereArr); exit;

        $data       =   DB::table('messages_bnu')
                        ->where($whereArr)
                        ->orderBy('id', 'desc')
                        ->get()->toArray();



        //print_r($data);exit();                 
                        $nullArray = array_keys(array_column($data, 'parent_message_id'), '0');
                        $mainArray = array();
                        foreach($nullArray as $row):
                           $childKeys = array_keys(array_column($data, 'parent_message_id'), $data[$row]->id);
                           
                           $data[$row]->reply = array();


                             if(count($childKeys) > 0){
                                foreach($childKeys as $childRow):
                                    //  print_r($data[$childRow]);
                                    // $data[$childRow]->time_ago = ($data[$childRow]->created_at)?\Carbon\Carbon::createFromTimeStamp(strtotime($data[$childRow]->created_at))->diffForHumans():'';
                                    
                                 //    echo $data[$childRow]->created_at; exit;
                                    
                                    $data[$childRow]->time_ago = ($data[$childRow]->created_at)? \Carbon\Carbon::parse($data[$childRow]->created_at)->diffForHumans() :'';
                                    $data[$row]->reply[] = (array) $data[$childRow];
                                endforeach;
                            }

                            $node                   = (array) $data[$row];
                            $node['time_ago']       = ($node['created_at'])?\Carbon\Carbon::createFromTimeStamp(strtotime($node['created_at']))->diffForHumans():'';
                            $mainArray[]            = $node;
                        endforeach;
        return response()->json(array('status' => true, 'data' => $mainArray , 'success' => 'Data Saved Successfully.'));
    }


    function notification($co_id,$message,$user_id, $target_page){
        // echo $co_id."___".$message."__".$user_id;exit();
        try {
         $procedurenotification = 'notification';
         $bindings_notification = [
               'co_id'  => $co_id,
               'desc'  => $message,
               'user_id'  =>$user_id,
               'target_page'  =>$target_page,
           ];
      return $result_notification = DB::executeProcedure($procedurenotification, $bindings_notification);    
     } catch (Exception $e) {
         //echo $e;
        // echo 'no';
         // This calls the render() method of `App\Exceptions\Handler`
     }
    }




   
}