<?php

namespace App\Models\Grader;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use DB;
use App\sessions;
use App\Models\Grader\SendSms;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class GoogleDrives extends Model
{
    


    public function student_file($url,$enrollment , $ccd_id,$name,$description,$co_id)
    {
                 $std_username= session('username');
      //dd($std_username , $ccd_id,$name,$description,$co_id);
					$query_user="select  fc_employee_id from FACULTY_COURSES where 1=1 and (fc_co_id=$co_id or
											fc_co_id = (select parent_co_id from courses_offered where co_id =$co_id and parent_co_id is not null)) and rownum=1 and fc_status='E'";
                    $result_user= DB::select($query_user);
    
                    $user_id= $result_user[0]->fc_employee_id;
                    
                    $query="SELECT * FROM course_criteria_details  where id=$ccd_id ";
                    $result= DB::select($query);
                    //print_r(  $result);
                     $due_datee=$result[0]->due_date;
                     $late_submission_allowed=$result[0]->late_submission_allowed;

                     $due_day=date("d-m-Y", strtotime(substr($due_datee,0,11)));
                   
                    date_default_timezone_set('Asia/karachi');
                     $curr_time =date("His");
                    $curr_date=date("d-m-Y", strtotime(substr($curr_time,0,11)));
                  
                   // dd('aa');
                    $current_date = date('Y-m-d H:i:s');

                     if($due_day < $curr_date AND $late_submission_allowed =='No'){
                       // dd($curr_date.$due_day);
                        $data = array('status' => true, 'message' => 'Not Uploaded due to Date has passed !');
                        return response()->json($data);
                     }else{
				 ///////////////////////////////////////////////

                    $ccd_sel="SELECT * FROM course_criteria_details  where id=$ccd_id ";
                    $sel_result= DB::select($ccd_sel);
                       
                        $assess_id='';
                        $total_marks='';
                        $cam_no='';
                        foreach($sel_result as $rec){
                            $assess_id=$rec->assessment_id;
                            $total_marks=$rec->marks;
                            $cam_no=$rec->ccd_number;
                            $co_criteria_det_id = $rec->course_criteria_id;
                        }

                   $que="SELECT count(*) as rec FROM  course_assessment_marks where sai_enrollment_no='$enrollment' and sc_co_id in (select co_id from courses_offered where co_id =  $co_id or parent_co_id = $co_id) and course_criteria_detail_id=$ccd_id";
				  // echo $que; exit ();
                   $que_result= DB::select($que);
                   if($que_result[0]->rec ==0){
                       //dd('bb');
                        $sqlCAM = "INSERT INTO course_assessment_marks (
                                            co_id,
                                            sc_co_id,
                                            assessment_id,
                                            course_criteria_id,
                                            total_marks,
                                            cam_number,
                                            sai_enrollment_no,
                                            sai_id,
                                            course_criteria_detail_id,
                                            cam_status,
                                            submission_status,
                                            google_drive
                                        ) SELECT
                                            CASE 
                                                WHEN CO.PARENT_CO_ID IS NULL THEN CO.CO_ID
                                                WHEN CO.PARENT_CO_ID = 0 THEN CO.CO_ID
                                                ELSE CO.PARENT_CO_ID
                                                END 
                                            AS MAIN_CO_ID,   
                                            sc_co_id,
                                            '$assess_id' as assessment_id,
                                            '$co_criteria_det_id' as cc_id,
                                            '$total_marks' as total_marks,
                                            '$cam_no' as cam_number,
                                            sc_sai_enrollment_no,
                                            sai_id,
                                            '$ccd_id',
                                            'E',
                                            'NO',
                                            '$url'
                                        FROM
                                            student_courses
                                            LEFT JOIN courses_offered co on co.co_id = sc_co_id
                                            LEFT JOIN students_apply_info ON sc_sai_enrollment_no = sai_enrollment_no
                                        WHERE 1=1
                                        AND (SC_CO_ID = $co_id or parent_co_id = $co_id)
                                        AND sc_sai_enrollment_no='$enrollment'
                                        AND SC_ENROLLMENT_STATUS=1
                                        AND sc_status IN ('A','Block')
                                        AND sai_id IS NOT NULL";

                        $resultCAM = DB::insert($sqlCAM);
                    }

                    //dd('cc');
                           $ora_update=DB::table('course_assessment_marks')
                                        ->where('sai_enrollment_no', $enrollment)
                                        ->where('course_criteria_detail_id',$ccd_id)
                                        ->update(['submission_status' => "YES",
                                                    'submission_file_name'=>$name,
                                                    'submission_time'=> $current_date,
                                                    'student_comment'=>$description,
                                                    'google_drive'=>$url
                                        ]);
     
                   if($ora_update==1){

                   $procedurenotification = 'student_notification';
                          $bindings_notification = [
                                'main_co_id'  => $co_id,
                                'message'  => $enrollment .' '.'Upload Assginment by student',
                                'short_message'  =>'Upload Assginment',
                                 'student_enrollment_no'=>$enrollment,
                                 'entry_user_id'=>$user_id,
                                 'targe_page' =>'assignment',
                                 'send_type'=>'teacher',

                            ];
                            //print_r($bindings_notification);
                  //$result_notification = DB::executeProcedure($procedurenotification, $bindings_notification);
                  return 1;
                  //print_r($bindings_notification);
                   }else{
                    return 0;
                   }

           }    ///else end/////
                    
          
    }
	///// file upload student end //////////////


    
}
