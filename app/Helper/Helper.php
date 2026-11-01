<?php
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Models\Grader\SendSms;

function getLoginUser($key=''){


  // print_r(Auth::user());

    if($key == 'active_session_id'){
        return 150;
    }

    if($key == 'active_session_name'){
      return 'Fall 2020';
  }

    if(Auth::user()->id){
      return ($key != '')?Auth::user()->$key:Auth::user();
     // return Auth::user();
    }else{
      return false;
    }
}

if (!function_exists('my_simple_crypt')){

function my_simple_crypt( $string, $action = 'e' ) {
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
  function course_name($co_id){
   $result = DB::select('select get_course_name(co_id) as course_name from courses_offered where co_id='.$co_id);
   return $result[0]->course_name;
  }

}

function checkCourseAccess($co_id,$userToken){
  $checkPermission=" 
                      SELECT
                      fc_co_id,
                      fc_employee_id
                          FROM
                       faculty_courses
                      WHERE 1=1 and
                      fc_co_id in (select case when parent_co_id is null then co_id else parent_co_id end as main_co_id from courses_offered where co_id = '".$co_id."')
                      
                          AND fc_employee_id = (
                      SELECT
                          employee_id
                      FROM
                          sisusers
                      WHERE
                          api_token = '".$userToken."'
                  )";  
              // echo $checkPermission;exit();
 if(count(DB::select($checkPermission))<=0){
   echo 'Wrong Course Access';
   exit;
   //return back();
 }
}

function file_icon_and_color($ext){

    if ($ext=='docx') {
        $icon_and_color =['fa fa-file-word-o','blue'];
   }else if ($ext=='csv' or $ext=='xlsx'){
        $icon_and_color =['fa fa-file-excel-o','green'];
   }else if ($ext=='pptx'){
        $icon_and_color =['fa fa-file-powerpoint-o','#ff6c43'];
   }
   return $icon_and_color;
//    else if ($ext=='pdf'){
//        $link='';
//        $allowed ="fa fa-file-pdf-o";
//        $color='#ff6c43';
//    }else if ($ext!=''){
//        $link='';
//        $allowed ="fa fa-file-text-o";
//        $color='#ff6c43';
//    }else if($ext==''){
//        $allowed="fa fa-file-code-o";
//        $color='#2F3B4D';
//        $link='disabled';
//    }


  }


  
  function sendSMSMessage($mobileNumber, $smsMessageBody){


    if (App::environment('live')) {
      $mask       =   "BNU";
      //send_sms($get_spi_mobile,$new_message,$mask);
      //send_sms('923017169678',$new_message,$mask);

      try {
        $type = "xml";
        $id = "rchbnuro";
        $pass = '786_kareemallahpak';
        $lang = "English";
        $mask = ($mask != "") ? $mask : "BNU";
		
		
	
		
		
        // $data = "id=" . $id . "&pass=" . $pass . "&msg=" . urlencode($smsMessageBody) . "&to=" . $mobileNumber . "&lang=" . $lang . "&mask=" . $mask . "&type=" . $type;
        $data = "id=" . $id . "&pass=" . $pass . "&msg=" . urlencode($smsMessageBody) . "&to=" . $mobileNumber . "&lang=" . $lang . "&mask=" . "BNU" . "&type=" . $type;
    
	  
    	//dd($mobileNumber);
	
			
        $ch = curl_init('http://www.outreach.pk/api/sendsms.php/sendsms/url');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch); // This is the result from Outreach

        curl_close($ch);
    
        $simpleXML = simplexml_load_string($result);
        $code = (int) $simpleXML->code;
        $response = (string) $simpleXML->response;
    
        if ($code === 300) {
            // The request was successful, handle the response here
        } else {
            // Handle the error response
			$procedurenotification = 'send_mail_one_by_one';
         $bindings_notification = [
               'to'           => 'abdullah.khan@bnu.edu.pk',
               'subject'      =>'CMS LOGIN OutREACH',
               'message'      =>'OutREACH Sevice down. Dear Noc team Please look into it.',
           ];
           //print_r( $bindings_notification);exit();
           //execute send_mail_one_by_one('zain.ali@bnu.edu.pk','marks','asd') 
       //$result_notification = DB::executeProcedure($procedurenotification, $bindings_notification);  
	  
            throw new Exception("API Request Failed - Error Code: $code, Error Message: $response");
        }

    } catch (Exception $e) {
        // Handle any exceptions or errors here
        $errorMessage = $e->getMessage();
    $procedurenotification = 'send_mail_one_by_one';
         $bindings_notification = [
               'to'           => 'abdullah.khan@bnu.edu.pk',
               'subject'      =>'CMS LOGIN OutREACH',
               'message'      =>'OutREACH Sevice down. Dear Noc team Please look into it.',
           ];
           //print_r( $bindings_notification);exit();
           //execute send_mail_one_by_one('zain.ali@bnu.edu.pk','marks','asd') 
    //  return $result_notification = DB::executeProcedure($procedurenotification, $bindings_notification);  
       
        // Log or return the error message as needed
    }
    }else{

      Log::info($mobileNumber.' ::--Login-Message-->> '.$smsMessageBody);
    }


  }
  function getURL($api_name){

    if(URL::to('/')=='https://cms.bnu.edu.pk'){
        $base_url            ='https://api.bnu.edu.pk';
    }else{
        $base_url            ='http://mobileapi.bnu.edu.pk';
    }
   // return $base_url;
    $all_URLs = [
        'getNotification'            => $base_url.'/getNotification', 
        'sendNotification'           => $base_url.'/sendNotification', 
        'course_enrolled_students'   => $base_url.'/course_enrolled_students', 
        'student_chat_notification'  => $base_url.'/student_chat_notification'
    ];
    return $all_URLs[$api_name]; 
  }

  
  function sendPush($co_id,$title,$source,$target_page){
	   DB::table('testing')
                 ->insert(
                      ['description' => 'Update Step 2 Helper Start co_id = '.$co_id.'__Title : '.$title. gethostname().'------'.$_SERVER['SERVER_ADDR'].'-- 114__Resonse: start ']
                 );
        $descripton  = $source.' '.$title.' by '. auth()->user()->full_name;
        $apiURL = getURL('sendNotification');
        // dd(auth()->user()->remember_token);
        $postInput = [
        'description' => $descripton,
        'course_co_id' => $co_id,
		'target_page' => $target_page,
        'from_web' => 'yes',
        ];
        $headers =[
        'Authorization' => 'Bearer '.auth()->user()->remember_token,
        'Accept' => 'application/json',
        ];
       
        $response = Http::withHeaders($headers)->post($apiURL, $postInput);
        // dd($response);
		DB::table('testing')
                 ->insert(
                      ['description' => 'Update Step 3 Helper End Response co_id = '.$co_id.'__Title : '.$title. gethostname().'------'.$_SERVER['SERVER_ADDR'].'-- 114__Resonse '.auth()->user()->remember_token]
                 );
		 return auth()->user()->remember_token;exit;
        $statusCode = $response->status();
        
        $responseBody = json_decode($response->getBody()->getContents(), true);
  }
function getStudentSuggestion($studentEnrollment)
{   
    // $studentEnrollment = strtoupper($request->input('studentEnrollment'));
    $isCharacter = !is_numeric($studentEnrollment);
    // dd($studentEnrollment,  $isCharacter);
    // Initialize the query string
    $sql = '';

    if ($isCharacter) {
        $sql = " SELECT
                spi_name,
                school_code,
                school_name,
                dept_name,
                (select aai_id from admission_apply_info where LOWER(cms_sai_enrollment_no)=LOWER(sai_enrollment_no)) as aai_id,
                CONCAT(sai_enrollment_no, '@gmail.com') AS spi_email,
                TO_CHAR(sai_student_status_datetime, 'DD-Mon-YYYY') AS student_status_datetime,
                prog_name,
                spi_mobile,
                sai_enrollment_no as username
                   ,school_id
                    ,dept_id,
                    prog_id,
                    sai_student_status
            FROM students_apply_info sai
            LEFT JOIN students_personal_info spi ON sai.sai_spi_id = spi.spi_id
            LEFT JOIN schools s ON sai.sai_sdpo_school_id = s.school_id
            LEFT JOIN departments d ON sai.sai_sdpo_dept_id = d.dept_id
            LEFT JOIN programs p ON sai.sai_sdpo_prog_id = p.prog_id
            WHERE LOWER(sai.sai_enrollment_no) = LOWER('$studentEnrollment')
        ";

    } else {
        $sql = "
        SELECT
            aai.aai_id,
            api_name AS spi_name,
            school_code,
            dept_name,
            api_imagename,
            school_name,
            prog_name,
            api_mobile as spi_mobile,
            api_email AS spi_email,
            aai_id AS aai_id,
            cms_sai_enrollment_no as username,
            school_id
    ,dept_id,
    prog_id
        FROM admission_apply_info aai
        LEFT JOIN admission_personal_info api ON aai.aai_api_id = api.api_id
        LEFT JOIN schools s ON aai.aai_sdpo_school_id = s.school_id
        LEFT JOIN departments d ON aai.aai_sdpo_dept_id = d.dept_id
        LEFT JOIN programs p ON aai.aai_sdpo_prog_id = p.prog_id
        WHERE aai.aai_id = '$studentEnrollment'
    ";

    }
    // echo $sql;exit;
    // Execute the query
    $data = DB::select($sql);

    // Check if data is not empty and return the first row
    if (!empty($data)) {
        return ($data[0]);
    } else {
        // Return an error response if no data is found
        return ['success' => false, 'usernameError' => 'Invalid Student Enrollment Number'];
    }
}
  

