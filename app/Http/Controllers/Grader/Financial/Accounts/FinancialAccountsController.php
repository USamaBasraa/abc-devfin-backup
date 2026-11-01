<?php

namespace App\Http\Controllers\Grader\Financial\Accounts;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Grader\Schools;
use DataTables;
use DB;

class FinancialAccountsController extends Controller
{
   
    function getDetail(){

      $Scholarship_Type = "select * from Scholarship_Type";
      $scholarship = DB::select($Scholarship_Type);  

       $sessions = \App::call('App\Http\Controllers\Grader\AcademicSessionController@retrieve');
      $schools =  Schools::all()->where('school_status','E');
      //dd($schools);
      return view(
        'grader.financial.accounts.student-financial-accounts',['sessions'=>$sessions,'schools'=>$schools,'Scholarship_Type'=> $scholarship]
      );
       
    }

    function getData(Request $request){

      // return $request->all();
    
       $whereStr = '';
       if( $request->get('session') != '' ){
           $whereStr .= ' AND students_semester_record.sess_id = '.$request->get('session');
       }
       if( $request->get('session-for') != '' ){
           $whereStr .= ' AND sai_sdpo_sess_id = '.$request->get('session-for');
       }
       if( $request->get('schID') == 'all' ){
           $whereStr .= ' AND is_scholor_ship > 0 ';
       }
       if( $request->get('schID') != '' && $request->get('schID') != 'all' ){
           $whereStr .= " and upper(students_semester_record.sai_enrollment_no) in (
                           select upper(ssd_sai_enrollment_no) from students_scholarships_details 
                           where ssd_type = '".$request->get("schID")."'
                       )";
       }
       
    
       if($request->get('get_type')!=''){
          $whereStr .= " and scd_status = '".$request->get('get_type')."' ";
       }
       if($request->get('get_type') == 'challan_not_generated'){
        $whereStr .= 'and feechallanwisesummary.buyercodechallanno is null ';
       }
       if($request->get('get_type') == 'challan_generated'){
           $whereStr .= 'and feechallanwisesummary.buyercodechallanno is not null ';
       }
       if($request->get('student_status') != ''){
           $whereStr .= " AND CHALLAN_GENERATE = '".$request->get('student_status')."' ";
       }
       if( $request->get('schoolID') != '' ){
           $whereStr .= ' AND sai_sdpo_school_id='.$request->get('schoolID').'';
       }
       if( $request->get('depID') != '' && $request->get('depID') != 'null' ){
           $whereStr .= ' AND sai_sdpo_dept_id='.$request->get('depID').'';
       }
       if($request->get('result-for')){
          if($request->get('record_type')=='challan_wise'){
              //$whereStr .= " AND CHALLAN_GENERATE = 'Allowed'  AND total_pending_challans<=0 ";
              $whereStr .= "  AND total_pending_challans<=0 ";
          }elseif($request->get('record_type')=='challan_generated'){
              $whereStr .= " AND feechallanwisesummary.buyercodechallanno is not null ";
          }elseif($request->get('record_type')=='challan_not_generated'){
              $whereStr .= " AND feechallanwisesummary.buyercodechallanno is null ";
          }
           
       }
       if($request->get('page-from')=='accounts'){
         // $whereStr .= " AND CHALLAN_GENERATE = 'Allowed'  AND total_pending_challans<=0 ";
          $whereStr .= "   AND total_pending_challans<=0 ";
      }

       $sel = "SELECT
               cgpa(students_semester_record.sai_enrollment_no) as  cgpa,
               academic_sessions.sess_code,
               students_semester_record.sess_id,
               students_semester_record.sai_enrollment_no,
               spi_name,
               prog_name,
               entry_datetime,
               students_challan_details.scd_description,
               students_challan_details.scd_entry_username,
               scd_req_details,
               scd_update_username,
               students_semester_record.id,
               academic_sessions.sess_code as challan_session,
               student_session.sess_code as student_session,
               CASE
                   WHEN prog_degree_level = 'Undergraduate'
                        AND sai_sdpo_prog_id = 8
                        AND students_semester_record.semester_completed > 10 THEN
                       'PER CREDIT'
                   WHEN prog_degree_level = 'Undergraduate'
                        AND sai_sdpo_prog_id != 8
                        AND students_semester_record.semester_completed > 8 THEN
                       'PER CREDIT'
                   WHEN prog_degree_level = 'Postgraduate'
                        AND students_semester_record.semester_completed > 4 THEN
                       'PER CREDIT'
                   ELSE
                       'FULL_SEMESTER'
               END                  AS fee_type,
                                   scd_id,
                                   scd_challan_no,
                                   scd_duedate,
                                   scd_status,
                                   'challan_wise' as display_type,
               students_semester_record.semester_completed,
               students_semester_record.enrolled_courses,
               ssr_semester_status  AS semester_status,
               students_semester_record.scd_earned_credithour,
               students_semester_record.scd_required_credithour,
               students_semester_record.is_scholor_ship,
               school_name,
               sai_id,
               spi_email,
               school_code,
               dept_name,
               total_pending_challans,
               challan_generate ,
              scd_sess_id,
              students_challan_details.challan_type,
               CHALLAN_DESCRIPTION,
               TO_CHAR(challan_request_time, 'DD-MM-YYYY HH:MI AM')      AS challan_request_time
           FROM
               students_semester_record
               LEFT JOIN students_apply_info ON students_apply_info.sai_enrollment_no = students_semester_record.sai_enrollment_no
               LEFT JOIN students_personal_info ON students_personal_info.spi_id = students_apply_info.sai_spi_id
               LEFT JOIN academic_sessions ON academic_sessions.sess_id = students_semester_record.sess_id
               LEFT JOIN academic_sessions student_session ON student_session.sess_id = students_apply_info.sai_sdpo_sess_id
               LEFT JOIN schools ON school_id = sai_sdpo_school_id
               LEFT JOIN departments ON dept_id = sai_sdpo_dept_id
               LEFT JOIN programs ON prog_id = sai_sdpo_prog_id
               LEFT JOIN students_challan_details on students_challan_details.scd_sess_id = students_semester_record.sess_id 
               and students_challan_details.scd_sai_enrollment_no = students_semester_record.sai_enrollment_no
               and scd_id > 9586 
              ";  
   
               $sel .= "
                    WHERE sai_student_status = 'Active' 
                    AND students_semester_record.ssr_semester_status = 'Active'
                    $whereStr 
                    and sai_sdpo_sess_id not in (select max(sess_id) from academic_sessions)";

                    if( $request->get('get_type') == 'challan_wise' ){
                        $sel .= " ORDER  by   CASE 
                        WHEN scd_status='installment_request' THEN '1'
                        WHEN scd_status='Pending' then '2' 
                        else '3'
                        end  ASC ";
                        }else{
                            $sel .= ' ORDER BY CHALLAN_REQUEST_TIME desc';
               }  
    // echo $sel;exit();
       $res=DB::select($sel);

       if($request->get('result-for')){
           return view('grader.financial.accounts.student-financial-excel',['result'=>$res,'totalCount'=>count($res).'record(s) found!']);
       }else if($request->get('result-for-account')){
          return $this->getDataAccounts();
       }else{
           return response()->json(['data' => $res, 'status'=> true, 'message' => count($res).' record(s) found!']);

       }
  
      
   }

   function updateRecord(Request $request){
      
        if($request->get('checkval')==""){
            return response()->json(['data' => [], 'status'=> true, 'message' => '0 record(s) updated!']);
        }
      
        $scdID=($request->get('checkval')!=""?$request->get('checkval'):exit());

    
      foreach($scdID as $rec){
        //  $res=DB::table('students_challan_details')
        //      ->where('scd_id', $rec)
        //      ->update(['scd_status' => "Completed"]);


             $res=DB::table('students_challan_details')
             ->where('scd_id', trim($rec))
             ->update(
                     [
                         'scd_status'            => "Completed",
                         'scd_update_username'   => $userID=auth()->user()->username,
                         'scd_req_details'       => date("Y-m-d h:i:sa")
                     ]
                 );     

            $sql_student_request = "INSERT INTO students_challan_requests (
                SCR_SAI_ENROLLMENT_NO,
                scr_challan_no,
                scr_request_type,
                scr_description,
                scr_sess_id,
                scr_entry_datetime
            ) 
            select 
                    scd_sai_enrollment_no,
                    requested_against_challan_id,
                    challan_type,
                    'Completed by FO',
                    scd_sess_id,
                    current_timestamp
                from students_challan_details 
                WHERE scd_id = $rec
            ";
           // echo $sql_student_request;exit();
            $sql_insert_run = DB::statement($sql_student_request);
      }
      return response()->json(['data' => $res, 'status'=> true, 'message' => count($scdID).' record(s) updated!']);
      
   }
 
}
