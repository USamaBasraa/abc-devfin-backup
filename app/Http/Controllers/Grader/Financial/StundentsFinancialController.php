<?php

namespace App\Http\Controllers\Grader\Financial;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Grader\Schools;
use DataTables;
use DB;

class StundentsFinancialController extends Controller
{

    function getDetail(){

        $Scholarship_Type = "select * from Scholarship_Type";
        $scholarship = DB::select($Scholarship_Type);

         $sessions = \App::call('App\Http\Controllers\Grader\AcademicSessionController@retrieve');
        $schools =  Schools::all()->where('school_status','E');
    // dd($schools);
        return view(
          'grader.financial.student-financial',['sessions'=>$sessions,'schools'=>$schools,'Scholarship_Type'=> $scholarship]
        );

    }

    function getData(Request $request){

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
         if($request->get('get_type') == 'challan_not_generated'){
             $whereStr .= 'and mv_FEECHALLANWISESUMMARY.buyercodechallanno is null ';
         }
         if($request->get('get_type') == 'challan_generated'){
             $whereStr .= 'and mv_FEECHALLANWISESUMMARY.buyercodechallanno is not null ';
         }
        //  if($request->get('student_status') != '' && $request->get('student_status') != 'requested_and_pending'){
        //      $whereStr .= " AND CHALLAN_GENERATE = '".$request->get('student_status')."' ";
        //  }
         if($request->get('student_status') == 'requested_and_pending'){
            $whereStr .= "  and scd_status in ('installment_request','Pending') ";
         }
         if( $request->get('schoolID') != '' ){
             $whereStr .= ' AND sai_sdpo_school_id='.$request->get('schoolID').'';
         }
         if( $request->get('depID') != '' && $request->get('depID') != 'null' ){
             $whereStr .= ' AND sai_sdpo_dept_id='.$request->get('depID').'';
         }
        
         if($request->get('enrollment') != ''){
            $whereStr .= " AND students_semester_record.sai_enrollment_no='".strtoupper($request->get('enrollment'))."' ";
         }else{
            $whereStr .= " and sai_student_status = 'Active' AND students_semester_record.ssr_semester_status = 'Active'";
         }
         
             
         if($request->get('result-for')){
            if($request->get('record_type')=='challan_wise'){
               // $whereStr .= " AND CHALLAN_GENERATE = 'Allowed'  AND total_pending_challans<=0 ";
            }elseif($request->get('record_type')=='challan_generated'){
                $whereStr .= " AND mv_FEECHALLANWISESUMMARY.buyercodechallanno is not null ";
            }elseif($request->get('record_type')=='challan_not_generated'){
                $whereStr .= " AND mv_FEECHALLANWISESUMMARY.buyercodechallanno is null ";
            }

         }
         if($request->get('page-from')=='accounts'){
            $whereStr .= " AND CHALLAN_GENERATE = 'Allowed'  AND total_pending_challans<=0 ";
        }
       //  return $doit;exit();
         $sel = "SELECT
                    cgpa(students_semester_record.sai_enrollment_no) as  cgpa,
                    scd_status,
                    CASE WHEN scd_status='installment_request' THEN 'Requested'
                WHEN scd_status='Pending' then 'Allowed'
                else 'Generated'
                end as  challan_generate,
                academic_sessions.sess_code,
                students_semester_record.sess_id,
                students_semester_record.sai_enrollment_no,
                spi_name,
                prog_name,
                students_semester_record.id,
                academic_sessions.sess_code as challan_session,
                student_session.sess_code as student_session,
                entry_datetime,
                (select count(*) from student_courses where sc_sai_enrollment_no = students_semester_record.sai_enrollment_no and sc_co_sess_id = students_semester_record.sess_id and sc_enrollment_status = 1 and  sc_status = 'A') as enrolled_students,
                (SELECT sum(sc_pc_course_cr) FROM student_courses WHERE sc_sai_enrollment_no = students_semester_record.sai_enrollment_no AND sc_co_sess_id = students_semester_record.sess_id AND sc_enrollment_status = 1 AND sc_status = 'A') AS total_cr,
                students_challan_details.scd_description,
                students_challan_details.scd_description,
                students_challan_details.scd_entry_username,
                scd_req_details,
                scd_update_username,
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
                                    mv_FEECHALLANWISESUMMARY.duedate,
                                    mv_FEECHALLANWISESUMMARY.validdate,
                                    mv_FEECHALLANWISESUMMARY.paidamt,
                                    mv_FEECHALLANWISESUMMARY.paidon,
                                    mv_FEECHALLANWISESUMMARY.buyercodechallanno,
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
                LEFT JOIN mv_FEECHALLANWISESUMMARY ON mv_FEECHALLANWISESUMMARY.regno = students_semester_record.sai_enrollment_no
                    and mv_FEECHALLANWISESUMMARY.regno is not null
                     and mv_FEECHALLANWISESUMMARY.session_id = academic_sessions.sess_code
                LEFT JOIN students_challan_details on students_challan_details.scd_sess_id = students_semester_record.sess_id
                and students_challan_details.scd_sai_enrollment_no = students_semester_record.sai_enrollment_no
                and scd_id > 9586
                ";

                if($request->get('page-from')=='accounts'){
                    //$whereStr .= " and scd_status='installment_request' ";
                }
                 // if( $request->get('get_type') == 'challan_wise' ){
                 //     $sel .= "
                 //        LEFT JOIN students_challan_details ON students_challan_details.scd_challan_no = mv_FEECHALLANWISESUMMARY.buyercodechallanno                                  ";
                 //     }
                 $sel .= "

             WHERE 1=1 
             $whereStr
             and sai_sdpo_sess_id not in (select max(sess_id) from academic_sessions)
             and students_semester_record.sai_enrollment_no not in (select distinct regno from mv_FEECHALLANWISESUMMARY where session_id = 'F2023' and paidon is null and regno is not null)";

             if( $request->get('get_type') == 'challan_wise' ){
                 $sel .= " ORDER  by   CASE
                 WHEN scd_status='installment_request' THEN '1'
                 WHEN scd_status='Pending' then '2'
                 else '3'
                 end  ASC ";
                 }else{
                     $sel .= ' ORDER BY CHALLAN_REQUEST_TIME desc';
                 }
//echo $sel;exit;

   // and students_semester_record.sai_enrollment_no in ('F2020-010','F2020-599','F2022-674','F2019-097')

         $res=DB::select($sel);

         if($request->get('result-for')){
             return view('grader.financial.accounts.student-financial-excel',['result'=>$res,'totalCount'=>count($res).'record(s) found!']);
         }else if($request->get('result-for-account')){
            return $this->getDataAccounts();
         }else{
             return response()->json(['data' => $res, 'status'=> true, 'message' => count($res).' record(s) found!']);

         }


     }
     function getDataAccounts(){

        $sel="SELECT
        students_challan_details.challan_type,
         students_challan_details.scd_id,
                         academic_sessions.sess_code,
                         students_semester_record.sess_id,
                         students_semester_record.sai_enrollment_no,
                         spi_name,
                         prog_name,
                         students_semester_record.id,
                         challan_due_date,challan_valid_date,
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
                         challan_generate
                     FROM
                         students_challan_details
                         LEFT JOIN students_apply_info ON students_apply_info.sai_enrollment_no = students_challan_details.scd_sai_enrollment_no
                         LEFT JOIN students_personal_info ON students_personal_info.spi_id = students_apply_info.sai_spi_id
                         LEFT JOIN academic_sessions ON academic_sessions.sess_id = scd_sess_id
                         LEFT JOIN academic_sessions student_session ON student_session.sess_id = students_apply_info.sai_sdpo_sess_id
                         LEFT JOIN schools ON school_id = sai_sdpo_school_id
                         LEFT JOIN departments ON dept_id = sai_sdpo_dept_id
                         LEFT JOIN programs ON prog_id = sai_sdpo_prog_id
                         LEFT JOIN students_semester_record ON students_semester_record.sai_enrollment_no = students_apply_info.sai_enrollment_no
                         and students_semester_record.sess_id = scd_sess_id
                         where students_challan_details.scd_challan_no is  null
                         ";

            $res=DB::select($sel);
          //  echo $sel;exit;
            //dd($res);
        return view('grader.financial.accounts.student-financial-excel',['result'=>$res,'totalCount'=>count($res).'record(s) found!']);

     }

     function exportExcel(Request $request){

     $session=$request->get('session');

     $sql="select * from academic_sessions where sess_id = $session";
     $sql_res=DB::select($sql);
     $sesCode=$sql_res[0]->sess_code;
        $whereString = '';

         if( $request->get('print') == 'accounts' ){
             $whereString .= " AND sai_student_status = 'Active' ";
         }else{
            $whereString .= " AND sai_student_status in ('Active','Expected-Graduate')";
         }

      $sel="SELECT
      STUDENTS_APPLY_INFO.SAI_PROGRAMID,
      school_name,
      hostel_records.HOR_SAI_ENROLLMENT_NO,
      hostel_records.HOR_ROOM_ID,
      hor_category,
      spi_name,
      sai_student_status,sai_id
    FROM
        hostel_records
        LEFT JOIN students_semester_record ON
            students_semester_record.sai_enrollment_no = hostel_records.hor_sai_enrollment_no
        AND
            sess_id = $session
         LEFT JOIN STUDENTS_APPLY_INFO ON STUDENTS_APPLY_INFO.sai_enrollment_no = hostel_records.hor_sai_enrollment_no
         LEFT JOIN schools ON sai_sdpo_school_id = school_id
         LEFT JOIN students_personal_info ON students_personal_info.spi_id = students_apply_info.sai_spi_id

    WHERE
            students_semester_record.total_pending_challans <= 0

            $whereString
        AND
            hor_sai_enrollment_no NOT IN (
                SELECT
                    upper(regno)
                FROM
                    mv_feechallanwisesummary
                WHERE
                        session_id = '$sesCode'
                    AND
                        hostelfee > 0
            )
            order by hor_category asc
            ";
        //echo $sel;exit;
        $res=DB::select($sel);

       return view('grader.financial.accounts.student-financial-hostel-excel',['result'=>$res,'totalCount'=>count($res).'record(s) found!']);


     }
    function student_scholorshaip_details($regno){

        $sql = "select
                    students_scholarships_details.ssd_percentage,
                    scholarship_type.scholarship_name,
                    scholarship_type.SCHOLARSHIP_ID
                from scholarship_type
                left join students_scholarships_details on ssd_type = scholarship_id and ssd_sai_enrollment_no = '".$regno."'
                ORDER BY scholarship_type.SCHOLARSHIP_ID ASC";
          //  return $sql;
        return    $data = DB::select($sql);

    }

    function genChallan(Request $request){

        $sql = "select * from account_finance_communications order by entry_datetime desc";
        $res = DB::select($sql);

        return response()->json(['data' => $res, 'status'=> true, 'message' => 'Data Found found!']);
    }


    function insert_financeCommunication(Request $request){

       $all_ids_string= str_replace(array("\r", "\n", " "), '_',$request->input('request_ids'));
       $all_ids = explode("_",$all_ids_string);

 
    foreach($all_ids as $request_id)
      {
         $get_requestd_id = trim($request_id);

        $res=DB::table('students_challan_details')
        ->where('scd_id', trim($get_requestd_id))
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
                WHERE scd_id = '$get_requestd_id'
            ";
    try {
        $sql_insert_run = DB::statement($sql_student_request);
    } catch (\Exception $e) {
      //  echo $sql_student_request;
    }

   
    
//echo 'pak';exit();
    
    


      }

        $userID=auth()->user()->username;
        $session = $request->get('sessionID');
        $email='zain.ali@bnu.edu.pk';
        

      
        $sess_sql = "select sess_name from academic_sessions where sess_id = '$session'";
        $resSessSql = DB::select($sess_sql);
        $session_name = $resSessSql[0]->sess_name;

        if($userID=='mussawar.hussain' or $userID=='kamran.tahir' or $userID=='abdul.waheed' ){
            $email = array("mussawar.hussain@bnu.edu.pk","bilal.saleem@bnu.edu.pk","saqib.jamil@bnu.edu.pk","muhammad.nabeel@bnu.edu.pk","ahmad.arfeen@bnu.edu.pk","adnan.nafees@bnu.edu.pk","asif_nadeem@bnu.edu.pk","mussawar.hussain@bnu.edu.pk");
        }else{
            $email = array("zain.ali@bnu.edu.pk","mussawar.hussain@bnu.edu.pk");

        }
        try{
            foreach ($email as $row){
                // echo $row;
                $procedurenotification = 'send_mail_one_by_one';
                $message = 'The Challans has been Successfully generated for '.$session_name.'  ';
                $bindings_notification = [
                    'to'           => $row,
                    'subject'      => 'Generate Challan Request',
                    'message'      => $message.' '.$all_ids_string,
                ];

              // $result_notification = DB::executeProcedure($procedurenotification, $bindings_notification);

            }

        } catch( Exception $e ){
            echo "some error" . $e->getMessage();
        }



        $desc = $request->get('description').' '.$all_ids_string;
        $user_id = auth()->user()->username;

        $ins = "insert into account_finance_communications(
                AFC_DESCRIPTION,
                AFC_ENTRY_USERNAME
            ) values (
                '$desc',
                '$user_id'
            )";
            // echo $ins;exit;
        $res_ins = DB::insert($ins);

        if($res_ins){
            return response()->json(['status'=> true, 'message' => 'Data Inserted Successfully!']);
        }

    }





        //  $selInsert="INSERT INTO students_challan_details (
        //     scd_sai_enrollment_no,
        //     scd_sess_id,
        //     scd_status,
        //     scd_description,
        //     scd_entry_username,
        //     scd_school_id,
        //     scd_prog_id
        // ) SELECT
        //     sai_enrollment_no,
        //     '".$request->get('session_id')."',
        //     'Pending',
        //     'Generate Challan',
        //     '".$userID."',
        //     sai_sdpo_school_id,
        //     sai_sdpo_prog_id
        // FROM
        //     students_apply_info
        // WHERE
        //     sai_student_status = 'Active'";

        // $res=DB::insert($selInsert);




        function print_challan(Request $request){
            $challan_no = $request->get('id');

            $data_qry="select * from mv_FEECHALLANWISESUMMARY where BuyerCodeChallanNo=$challan_no";
            $data = DB::select($data_qry);
           // $data_det_qry="select * from mv_FEECHALLANWISESUMMARY where BuyerCodeChallanNo=$challan_no";
           // $data_detail = DB::select($data_det_qry);
            return view('grader.financial.print_challan',array('obj' => $data,'details' => $data));
        }


        function RequestChallan(Request $request){

            $scd_id = $request->get('scd_id');
            $no_of_challan = $request->get('challanNo');
            $description = $request->get('desc-text');
            $userID = auth()->user()->username;
            $session=$request->get('sesID');
            $regNO=$request->get('regNo');

            $against_challan = ($request->get('upChallan')!=""?' against challan'.$request->get('upChallan'):' New Challan ');

            $scd_description = $request->get('challanNo')." ".$request->get('challan_type')." ".$against_challan." ".$request->get('desc-text') ;//challans of type ".$request->get('challan_type')." ".$request->get('desc-text');
           // echo $scd_description;exit();
           // $challan_description = $no_of_challan.' '.$against_challan.' '.$scd_description;




             $sel_update="update students_semester_record
                        set challan_generate = 'Allowed',
                        challan_description = '".$scd_description."',
                        CHALLAN_REQUEST_TIME = current_timestamp
                        where sess_id =$session
                        and sai_enrollment_no='$regNO'";
            $res = DB::statement($sel_update);

            if($userID=='mussawar.hussain' or $userID=='kamran.tahir' or $userID=='abdul.waheed' ){
                $email = array("bilal.saleem@bnu.edu.pk","saqib.jamil@bnu.edu.pk","ahmad.arfeen@bnu.edu.pk","muhammad.nabeel@bnu.edu.pk","adnan.nafees@bnu.edu.pk","asif_nadeem@bnu.edu.pk","mussawar.hussain@bnu.edu.pk");
            }else{
                $email = array("zain.ali@bnu.edu.pk","mussawar.hussain@bnu.edu.pk");

            }
            try{
                foreach ($email as $row){
                    // echo $row;
                    $procedurenotification = 'send_mail_one_by_one';
                   // $message = 'The Challans has been Successfully generated for '.$session_name.'  ';
                    $bindings_notification = [
                        'to'           => $row,
                        'subject'      => 'Generate Challan Request',
                        'message'      => $scd_description,
                    ];
                   // $result_notification = DB::executeProcedure($procedurenotification, $bindings_notification);

                }

            } catch( Exception $e ){
                echo "some error" . $e->getMessage();
            }

          $loop_no = $request->get('challanNo');
           // exit();
            while($loop_no>0){

                $sql_student_request = "INSERT INTO students_challan_requests (
                    SCR_SAI_ENROLLMENT_NO,
                    scr_challan_no,
                    scr_request_type,
                    scr_description,
                    scr_sess_id,
                    scr_entry_datetime,
                    installmentno
                )
                values (
                    '$regNO',
                    '".$request->get('upChallan')."',
                    '".$request->get('challan_type')."',
                    'forward_to_fo',
                    '".$session."',
                    CURRENT_TIMESTAMP,
                    '".$loop_no."'
                )";
               // echo $sql_student_request;exit();
                $sql_insert_run = DB::statement($sql_student_request);


                $loop_no--;
            }


                    $check_sql = "select * from students_challan_details
                    where scd_id > 9586
                    and requested_against_challan_id = '".$request->get('upChallan')."' ";
                    //echo $check_sql;exit();
                    $result_check_sql = DB::select($check_sql);
                    if(count($result_check_sql)>0 && $request->get('upChallan')!='0'){

                        $update_sql = "update students_challan_details set scd_status ='Pending'
                                       where requested_against_challan_id = '".$request->get('upChallan')."'";
                         $sql_insert_run = DB::statement($update_sql);

                    }else{

                  $sql_insert = "INSERT INTO students_challan_details (
                        scd_sai_enrollment_no,
                        scd_sess_id,
                        scd_status,
                        scd_entry_username,
                        challan_due_date,
                        challan_valid_date,
                        challan_type,
                        scd_sess_code,
                        scd_description,
                        requested_against_challan_id
                    ) SELECT
                        sai_enrollment_no,
                        students_semester_record.sess_id,
                        'Pending',
                        '".$userID."',
                        '".$request->get('due_date')."',
                        '".$request->get('valid_date')."',
                        '".$request->get('challan_type')."',
                        academic_sessions.sess_code,
                        '".$scd_description."',
                        '".$request->get('upChallan')."'
            from students_semester_record
            left join academic_sessions on academic_sessions.sess_id = students_semester_record.sess_id
            where students_semester_record.sess_id = $session and students_semester_record.sai_enrollment_no='$regNO'
            and 0 = (
                select count(*) from students_challan_details ssr_sub
                where ssr_sub.scd_sai_enrollment_no = students_semester_record.sai_enrollment_no
                and ssr_sub.challan_type='".$request->get('challan_type')."'
                and ssr_sub.scd_sess_id=$session
                and status='D'
                )";
                if($request->get('challan_type')=='HOSTELFEE'){
                    $sql_insert.=" and sai_enrollment_no in (
                        select hostel_records.hor_sai_enrollment_no from hostel_records
                        ) ";
                }else{
                 //   $sql_insert.=" AND CHALLAN_GENERATE = 'Allowed' ";
                }
                $sql_insert_run = DB::statement($sql_insert);
            }
            if($res){
                return response()->json(['status' => true, 'message' => 'Data Updated Sucessfully']);
            }else{
                return response()->json(['status' => false, 'message' => 'Data Not Updated Sucessfully']);
            }

        }


        function approvedChallan(Request $request,$scd_id){
            $status=$request->get('status');
            if($status=='disapprove'){
                $sql="update STUDENTS_SEMESTER_RECORD set challan_generate='NotAllowed' where id=$scd_id";
                $res = DB::insert($sql);
                return response()->json(['status' => true, 'message' => 'Challan DisApproved Sucessfully']);
            }else if($status=='approve'){
                $sql="update STUDENTS_SEMESTER_RECORD set challan_generate='Allowed' where id=$scd_id";
                $res = DB::insert($sql);
                return response()->json(['status' => true, 'message' => 'Challan DisApproved Sucessfully']);
            }
            $updChallanApproval = "UPDATE students_challan_details
            SET
                scd_status          = 'Approved'
            WHERE
            scd_id = $scd_id ";
            $res = DB::insert($updChallanApproval);
            if($res){
                return response()->json(['status' => true, 'message' => 'Challan Approved Sucessfully']);
            }else{
                return response()->json(['status' => false, 'message' => 'Challan Not Approved Sucessfully']);
            }
        }
        function updateRecordProcedure($sess_id){

            $procedureUpdateRecord = 'finance_record_update';
            $bindings_notification = [
                  'var_sess_id'  => $sess_id,
            ];
            $result_notification = DB::executeProcedure($procedureUpdateRecord, $bindings_notification);

            $UpdateChallan = 'update_challans';
            $binding_updateChallan = [
                  'var_sess_id'  => $sess_id,
            ];
            $result_updatechallan= DB::executeProcedure($UpdateChallan, $binding_updateChallan);


            if($result_notification && $result_updatechallan){
                return response()->json(['status' => true, 'message' => 'Both Record Updated Sucessfully']);
            }else{
                return response()->json(['status' => false, 'message' => 'Record Not Updated Sucessfully']);
            }
        }

        function getStudentRequest(){

            //$getRequest = "select * from students_challan_requests where scr_status = 'E'";
            $getRequest = "SELECT
            *
            FROM
                students_semester_record
                LEFT JOIN students_apply_info ON students_apply_info.sai_enrollment_no = students_semester_record.sai_enrollment_no
                LEFT JOIN students_personal_info ON students_personal_info.spi_id = students_apply_info.sai_spi_id
                LEFT JOIN academic_sessions ON academic_sessions.sess_id = students_semester_record.sess_id
                LEFT JOIN mv_FEECHALLANWISESUMMARY ON mv_FEECHALLANWISESUMMARY.regno = students_semester_record.sai_enrollment_no
                                                AND mv_FEECHALLANWISESUMMARY.regno IS NOT NULL
                                                AND mv_FEECHALLANWISESUMMARY.session_id = academic_sessions.sess_code
                LEFT JOIN students_challan_details ON students_challan_details.scd_sess_id = students_semester_record.sess_id
                                                    AND students_challan_details.scd_sai_enrollment_no = students_semester_record.sai_enrollment_no
            WHERE
                    sai_student_status = 'Active'
                AND students_semester_record.ssr_semester_status = 'Active'
                AND students_semester_record.sess_id = 157
                AND challan_generate = 'Allowed'
                AND total_pending_challans <= 0
            -- AND scd_status = 'installment_request'
                AND sai_sdpo_sess_id NOT IN (
                    SELECT
                        MAX(sess_id)
                    FROM
                        academic_sessions
                )";
            $result = DB::select($getRequest);
            $countResult = count($result);

            return response()->json(['data' => $countResult, 'status'=> true, 'message' => 'Data Found found!']);


        }

        function challanStatusExcel(Request $request){

            if($request->get('report_type')=='all_challan_list'){
              return  $this->all_challan_list($request->get('session'));
            }
            else if($request->get('report_type')=='challan_by_school'){
              return  $this->school_wise_challan_summary($request->get('session'));
            }
            else{
                return  $this->all_challan_student_list($request->get('session'));
            }

        }

        function school_wise_challan_summary($session_id){

            $sql="
            SELECT
            school_code,
            payment_status,
            COUNT(*) AS total_challan
        FROM
            (
                SELECT
                    CASE
                    WHEN fee_challan_summary_vw.total_generated <> fee_challan_summary_vw.total_paid
                         AND fee_challan_summary_vw.total_paid > 0 THEN
                    'Partially Paid'
                    WHEN fee_challan_summary_vw.total_generated = total_paid THEN
                    'Fully Paid'
                    WHEN fee_challan_summary_vw.total_paid = 0               THEN
                    'Unpaid'
                    END AS payment_status,
                    school_code
                FROM
                    fee_challan_summary_vw
                    LEFT JOIN students_apply_info ON fee_challan_summary_vw.regno = students_apply_info.sai_enrollment_no
                    LEFT JOIN schools ON students_apply_info.sai_sdpo_school_id = schools.school_id
                    LEFT JOIN academic_sessions ON academic_sessions.sess_code = fee_challan_summary_vw.session_id
                WHERE
                    regno IS NOT NULL
        
                    AND sess_id = '".$session_id."'
            )
        WHERE
            school_code IS NOT NULL
        GROUP BY
            school_code,
            payment_status
        ORDER BY
            school_code,
            payment_status

            ";

            $challan_status=DB::select($sql);

            return view('grader.financial.accounts.challan-status-school-wise',['challan_status'=>$challan_status]);
        }


        function all_challan_student_list($session_id){

            // getting data from fee_challan_summary_vw  view not table
            $challan_status_sql= "select * 
            from fee_challan_summary_vw 
            where sess_id='".$session_id."'
            and regno is not null";
            $challan_status=DB::select($challan_status_sql);
            return view('grader.financial.accounts.challan-student-wise',['challan_status'=>$challan_status]);


        }
        function all_challan_list($session_id){
            $sql = "SELECT
            regno,
            challan_generate,
            mv_FEECHALLANWISESUMMARY.buyercodechallanno,
            mv_FEECHALLANWISESUMMARY.paidon,schools.school_id,department_id,school,department,

            (
                SELECT
                    scd_status
                FROM
                    students_challan_details
                WHERE
                        scd_sai_enrollment_no = regno
                    AND
                        scd_status = 'installment_request'
                        and scd_id > 9586
                        and rownum =1
            ) AS requestfromstudent,
            (
                SELECT
                    scd_status
                FROM
                    students_challan_details
                WHERE
                        scd_sai_enrollment_no = regno
                    AND
                        scd_status = 'Pending'
                        and scd_id > 9586
                        and rownum =1
            ) AS rosentrequest,
            (
                SELECT
                    scd_status
                FROM
                    students_challan_details
                WHERE
                        scd_sai_enrollment_no = regno
                    AND
                        scd_status = 'Completed'
                        and scd_id > 9586
                        and rownum =1
            ) AS completed,
            (select count(*) from mv_FEECHALLANWISESUMMARY where session_id ='S2023' and paidon is null and regno != ' ') as unpaid,
            (select count(*) from mv_FEECHALLANWISESUMMARY where session_id ='S2023' and paidon is not null and regno != ' ') as paid
        FROM
            mv_FEECHALLANWISESUMMARY
            LEFT JOIN academic_sessions ON sess_code = session_id
            LEFT JOIN students_semester_record ON
                regno = sai_enrollment_no
            AND
                academic_sessions.sess_id = students_semester_record.sess_id
                left join schools on mv_FEECHALLANWISESUMMARY.school_id = schools.school_code
                left join students_apply_info on students_apply_info.sai_enrollment_no = students_semester_record.sai_enrollment_no
        WHERE
                academic_sessions.sess_id = $session_id
                and regno is not null ";

          $rec = DB::select($sql);
          return view('grader.financial.accounts.challan-status-excel',['result'=>$rec,'challan_summary'=>$this->challan_summary_session_wise($session_id)]);

        }

        function challan_summary_session_wise($session_id){
         return   $challan_summary= DB::select(
                "SELECT
                        COUNT(*) total_callan,
                        payment_ststus
                        FROM
                        (
                        SELECT
                        CASE
                        WHEN total_generated <> total_paid
                        AND total_paid > 0 THEN
                        'Partially Paid'
                        WHEN total_generated = total_paid THEN
                        'Fully Paid'
                        WHEN total_paid = 0               THEN
                        'Unpaid'
                        END AS payment_ststus
                        FROM
                        fee_challan_summary_vw
                        left join academic_sessions on sess_code = fee_challan_summary_vw.session_id
                        WHERE  academic_sessions.sess_id = $session_id
                        )
                        GROUP BY
                        payment_ststus
                ");
        }





}
