<?php

namespace App\Models\Grader;

use Illuminate\Database\Eloquent\Model;
use DB;

class SendSms extends Model
{

    public function send_sms($message,$phone_no){
            return false;
    //     echo $message.'|'.$phone_no; exit;

        $to=$phone_no;
		//$to='923347280669';
       // $message='teacher change marks '.date("l")." ". date("h:m:ss d-m-Y");;

        $type = "xml";
        $id = "rchbnuro";
        $pass ='786_kareemallahpak';
        $lang = "English";
        $mask ="BNU";


        //$message='nadeem.alivi login @'.date("l")." ". date("d-m-Y");
        //$to='923017169678';923347280669
        $data = "id=".$id."&pass=".$pass."&msg=".urlencode($message)."&to=".$to."&lang=".$lang."&mask=".$mask."&type=".$type;
        // Send the POST request with cURL
        $ch = curl_init('http://www.outreach.pk/api/sendsms.php/sendsms/url');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch); //This is the result from Outreach
        curl_close($ch);


    }


public function get_course_teacher($co_id,$login_id){


        $qry1="select listagg(concat(prefix,concat(emp_f_name,concat(' ',emp_l_name))), ',') within group (order by emp_f_name) AS Emp_FullName from emp_employees where employee_id=$login_id ";
            $rset1 = DB::select($qry1);
            $get_teacher = $rset1['0']->emp_fullname;

           $qry="select GET_COURSE_NAME($co_id) as cn from dual";

            $rset = DB::select($qry);
            $get_course = $rset['0']->cn;
            return $get_course_teacher = $get_teacher .' '.' in '.$get_course;
    }


    public function get_teacher_course($co_id,$login_id){


        $qry1="select listagg(concat(prefix,concat(emp_f_name,concat(' ',emp_l_name))), ',') within group (order by emp_f_name) AS Emp_FullName from emp_employees where employee_id=$login_id ";
            $rset1 = DB::select($qry1);
            $get_teacher = $rset1['0']->emp_fullname;

           $qry="select GET_COURSE_NAME($co_id) as cn from dual";

            $rset = DB::select($qry);
            $get_course = $rset['0']->cn;
            return $get_course_teacher = $get_course.' '.'Students Grade Posted by '. ' '.$get_teacher;
    }

  public function teacher_marks_change_sendmail($to,$subject,$message){

      
         $procedurenotification = 'send_mail_one_by_one';
         $bindings_notification = [
               'to'           => $to,
               'subject'      => $subject,
               'message'      =>$message,
           ];
           //print_r( $bindings_notification);exit();
           //execute send_mail_one_by_one('zain.ali@bnu.edu.pk','marks','asd') 
      return $result_notification = DB::executeProcedure($procedurenotification, $bindings_notification);    
    
    }


}	