<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class StudentCourses extends Model
{
    protected $primaryKey = 'un_id';
    
	public function student_file($url,$enrollment , $ccd_id,$id,$description,$co_id,$fileName)
    {

         // dd( $request->all());
            // $std_username= session('username');
             $std_username= strtoupper(auth()->user()->username);
            
           //  dd($std_username , $ccd_id,$name,$description,$co_id);
					$query_user="select  fc_employee_id from FACULTY_COURSES where 1=1 and (fc_co_id=$co_id or
											fc_co_id = (select parent_co_id from courses_offered where co_id =$co_id and parent_co_id is not null)) and rownum=1 and fc_status='E'";
               // echo	$query_user;exit;
                    $result_user= DB::select($query_user);
    
                    $user_id= $result_user[0]->fc_employee_id;
                    
                    $query="SELECT * FROM course_criteria_details  where id=$ccd_id ";
                    $result= DB::select($query);
                    //print_r(  $result);exit;
                     $due_datee=$result[0]->due_date;
                     $late_submission_allowed=$result[0]->late_submission_allowed;

                     $due_day=date("d-m-Y", strtotime(substr($due_datee,0,11)));
                   
                    date_default_timezone_set('Asia/karachi');
                     $curr_time =date("His");
                    $curr_date=date("d-m-Y", strtotime(substr($curr_time,0,11)));
                       $current_date = date('Y-m-d H:i:s');
					   
					   $today=strtotime($curr_date);
					   $duedatee=strtotime($due_day);
					   
                     if($duedatee < $today AND $late_submission_allowed =='No'){
                       
                        $data = array('status' => false, 'message' => 'Not Uploaded due to Date has passed !');
                        return response()->json($data);
                     }else{
				 ///////////////////////////////////////////////
                // dd($curr_date.'_'.$due_day.'_'.$late_submission_allowed);

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
             
                   $que_result= DB::select($que);
                 
                   if($que_result[0]->rec ==0){
                      // dd('bb');
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
                                            submission_status
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
                                            'NO'
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

                        //    $ora_update=DB::table('course_assessment_marks')
                        //                 ->where('sai_enrollment_no', $enrollment)
                        //                 ->where('course_criteria_detail_id',$ccd_id)
                        //                 ->update(['submission_status' => "YES",
                        //                             'submission_file_name'=>$name,
                        //                             'submission_time'=> $current_date,
                        //                             'student_comment'=>$description
                                        
                        //                 ]);

                        $res=DB::table('course_assessment_marks')->where('sai_enrollment_no', $enrollment)
                        ->where('course_criteria_detail_id',$ccd_id)->first();
                       
        
                        $ora_update=DB::table('course_assessment_marks')
                        ->where('sai_enrollment_no', $enrollment)
                        ->where('course_criteria_detail_id',$ccd_id)
                        ->update(['submission_status' => "YES",
                                    'submission_time'=> $current_date,
                                    //'student_comment'=>$description
                        ]);
                      
                       // dd($ora_update);
                                        $ora_insert= DB::table('FACULTY_COURSE_UPLOADS')->insertGetId([
                                            'cam_id' => $res->id,
                                            'co_id' => $co_id,
                                            'google_drive' => $url,
                                            'type'=>446,
                                            'ENTRY_DATE_TIME'=>$current_date,
                                            'DESCRIPTION'=>$description,
                                            'FILE_ORIGINAL_NAME'=>$id,
                                            'sai_enrollment_no'=>$enrollment,
                                            'file_name'=>$fileName
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
                  $result_notification = DB::executeProcedure($procedurenotification, $bindings_notification);
                
                  return response()->json(array('status' => true,'data'=> [], 'message' => 'file Upload Successfully'));

                  //return 1;
                  //print_r($bindings_notification);
                   }else{
                   // return 0;
                    return response()->json(array('status' => false,'data'=> [], 'message' => 'file Not uploaded !' ));

                   }

           }    ///else end/////
                    
          
    }
	///// file upload student end //////////////
    static function getStudentEnrolledCourses($whereArr,$day){
        
            // dd($whereArr,$day);
            //  $spi_enorllment_no = 'F2018-411';
            $enrollmentNo = strtoupper($whereArr); // Session::get('enrollment_no');
            // exit;
            $sqlEnrolledCourses = "SELECT
            sc_co_id,
            co_id,
            parent_co_id,
            co_section as sc_pc_section,
            sc_pc_course_cr,
            sc_co_id,
            sc_status,
            sc_desc,
            sc_advisor_approve,
            course_cd_name,
            get_teachername(sc_co_id) AS teachername,
            tt_subject_day,
            tt_subject_time_from,
            tt_subject_time_to,
          
            case when co_dept_id = 5 then 
            get_room_name((select distinct(tt_building_name) from master_timetable where mtt_co_id = sc_co_id))
            else get_room_name(tt_building_name) end AS building_name,
            case when co_dept_id = 5 then 
            get_room_name((select distinct(tt_room_name) from master_timetable where mtt_co_id = sc_co_id))
            else get_room_name(tt_room_name) end AS room_name,
          
            (
                SELECT
                    COUNT(*)
                FROM
                    timetable
                WHERE
                    tt_co_id IN (
                        co_id,parent_co_id
                    )
            ) AS total_rows,
            sc_sai_enrollment_no,
            sess_name
        FROM
            student_courses
            LEFT JOIN courses_offered ON co_id = sc_co_id
            LEFT JOIN timetable ON
                tt_co_id = co_id
            OR
                tt_co_id = parent_co_id
            LEFT JOIN courses_two ON course_id = co_course_id
            LEFT JOIN ACADEMIC_SESSIONS ON sess_id = sc_co_sess_id
        WHERE 1 = 1
        AND sc_co_id NOT IN
        (SELECT gh_co_id
        FROM grade_history
        WHERE gh_sai_enrollment_no=sc_sai_enrollment_no
        AND gh_co_id             IS NOT NULL
        AND gh_co_id              >0
        AND gh_exam_fsubmit       =1
        )
        AND sc_sai_enrollment_no ='".$enrollmentNo."'";

        if (!empty($day)) {
            $sqlEnrolledCourses .= " AND tt_subject_day = '".$day."'";
        }

        $sqlEnrolledCourses .= "  and sc_enrollment_status=1 
		
        and sc_status in ('Approve','A') 
        and co_status = 'E'";
        //  echo $sqlEnrolledCourses; exit;

        return DB::select($sqlEnrolledCourses);
    }

}

// namespace App;



