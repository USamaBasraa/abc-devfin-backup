<?php

namespace App\Models\Grader;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;
use DataTables;
class StudentFinancial extends Model
{
    use HasFactory;

    public function fetchChallan($user_id){

     // $user_id='S2022-142';
         $sel="SELECT * from mv_FEECHALLANWISESUMMARY where regno='$user_id'  order by buyercodechallanno desc";
         $result = DB::select($sel);

        $sel_requests="select * from (
                            SELECT
                                scd_sai_enrollment_no as student_id,
                                scd_status as request_status,
                                scd_entry_username as entry_username,
                                entry_datetime as entry_datetime,
                                challan_type as forward_type,
                                'detail log' AS log_type
                            FROM
                                students_challan_details
                                union all
                            select 
                            scr_sai_enrollment_no as student_id,
                                scr_request_type as request_status,
                                scr_sai_enrollment_no as entry_username,
                                scr_entry_datetime as entry_datetime,
                                scr_request_type as forward_type,
                                'request log' AS log_type
                            from students_challan_requests 
                            )
                            where student_id = '$user_id'
                            order by entry_datetime,log_type  ";
         $result_requests = DB::select($sel_requests);
          $sql = "SELECT * from STUDENTS_SCHOLARSHIPS_DETAILS
                   left join SCHOLARSHIP_TYPE on SCHOLARSHIP_TYPE.SCHOLARSHIP_ID=STUDENTS_SCHOLARSHIPS_DETAILS.SSD_TYPE
                  where SSD_SAI_ENROLLMENT_NO='$user_id' and ssd_type is not null";
           $res    = DB::select($sql);

           array_walk($result, function ($row, $index) use ($res) {
            $content_keys = array_keys(array_column($res, 'ssd_sai_enrollment_no'), $row->regno);
            $row->scholarship  = [];
            foreach ($content_keys as $cc_row) :
              $row->scholarship[] =  $res[$cc_row];
            endforeach;
            });

            array_walk($result, function ($row, $index) use ($result_requests) {
                $content_keys_req = array_keys(array_column($result_requests, 'student_id'), $row->regno);
                $row->requests  = [];
                foreach ($content_keys_req as $cc_row) :
                  $row->requests[] =  $result_requests[$cc_row];
                endforeach;
                });

       return $result; 
    }
    public function fetchChallanStd($user_id,$sessID){

      // $user_id='S2022-142';
  //     SELECT
  //     scd_sai_enrollment_no as regno,
  //     to_char(requested_against_challan_id)     AS buyercodechallanno,
  //     to_char(sess_code)||' Student Request'     AS session_id,
  //     'student_request'                         AS request_type
  // FROM
  //     students_challan_details
  //     LEFT JOIN academic_sessions ON academic_sessions.sess_id = students_challan_details.scd_sess_id
  // WHERE
  //     scd_id > 9586
  //     and scd_sess_id = $sessID
  //     and scd_sai_enrollment_no = '$user_id'
  //     and scd_status='installment_request'
  // UNION ALL

      $sel = "SELECT
  regno,
      to_char(mv_feechallanwisesummary.buyercodechallanno)       AS buyercodechallanno,
      to_char(mv_feechallanwisesummary.session_id)||' Ro Request for student'               AS session_id,
      'ro_request'                                            AS request_type
  FROM
      mv_feechallanwisesummary
      LEFT JOIN academic_sessions ON academic_sessions.sess_code = mv_feechallanwisesummary.session_id
  where regno is not null 
  and  regno = '$user_id'
              AND
                  sess_id = $sessID
              AND
                  paidon IS NULL   
     ";

      //  $sel="SELECT
      //    *
      //     FROM
      //         feechallanwisesummary
      //         LEFT JOIN academic_sessions ON academic_sessions.sess_code = feechallanwisesummary.session_id
      //     WHERE
      //             regno = '$user_id'
      //         AND
      //             sess_id = $sessID
      //         AND
      //             paidon IS NULL    
      //     ORDER BY buyercodechallanno DESC";
          return $result = DB::select($sel);
          
     }
}
