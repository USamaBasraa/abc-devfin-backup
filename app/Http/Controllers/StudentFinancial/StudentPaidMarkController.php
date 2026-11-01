<?php
/**
 * Created by PhpStorm.
 * User: Maher Asad
 * Date: 5/14/2019
 * Time: 9:52 AM
 */
namespace App\Http\Controllers\StudentFinancial;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\Console\Helper\Table;
use DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
use App\Models\Grader\Schools;
use App\Models\Grader\Departments;
use App\Models\Grader\FacultyCourses;
use App\Models\Grader\EmpEmployees;
use Response;
use Validator;
use DataTables;
use DateTime;

class StudentPaidMarkController extends Controller{


    function paidmark(){

        return view('StudentFinancial.studentPaidMark');

    }
    public function getStudentFeeRec(Request $request){
        $challanNO = $request->challanNo;


        $sql = "SELECT
                SC_STUDENTNAME As spi_name,
                ID AS buyercodechallanno,
                SC_ENROLLMENT_NO  AS regno,
                sc_app_id AS applicant_id,
                SC_TUITION_FEE_AMNT AS TUTIONFEE,
                INVOICEAMOUNT AS payableAmount,
                TOTAL_AMOUNT_PAYABLE AS TOTALAMOUNTPAYABLE,
                SC_DUE_DATE AS DUEDATE,
                SC_VALID_DATE AS VALIDDATE,
                RESPONSE_AMOUNTPAID AS PAIDAMT,
                RESPONSE_PAIDON AS PAIDON,
                sc_challan_status AS challanstatus,
                SC_PAYMENT_MODE AS paymentmode,
                SC_CHALLAN_TYPE AS FEETYPESUMMARY,
                FINE,
                SC_SESSION_ID As sess_id,
                SC_EFFECTIVE_DATE AS effective_date
                FROM
                    student_challan
                WHERE
                ID = $challanNO
                AND sc_challan_status in ('UNPAID', 'PAID')
                
                 " ;
               // echo $sql;exit;
        $student_challan = DB::select($sql);
        $student_challan_data = '';
        if(!empty($student_challan[0])){
           $student_challan_data =  $student_challan[0];
        }
        // dd($student_challan_data);

        return response()->json(['success' => true, 'student_challan'=>$student_challan_data]);

    }
    public function getStudentMvFeeRec(Request $request){
        $challanNO = $request->challanNo;
        $sql = "SELECT
                STUDENTNAME As spi_name,
                BUYERCODECHALLANNO AS buyercodechallanno,
                REGNO  AS regno,
                PVNOSLIPNOSTUDENTID AS applicant_id,
                TUTIONFEE AS TUTIONFEE,
                TOTALAMOUNTPAYABLE AS payableAmount,
                TOTALAMOUNTPAYABLE AS TOTALAMOUNTPAYABLE,
                DUEDATE AS DUEDATE,
                VALIDDATE AS VALIDDATE,
                PAIDAMT AS PAIDAMT,
                PAIDON AS PAIDON,
                FEEPAYSTATUS AS challanstatus,
                FEETYPE AS paymentmode,
                FEETYPESUMMARY AS FEETYPESUMMARY,
                FINE,
                SESSION_ID As sess_id,
                PAIDON AS effective_date
                FROM
                    MV_FEECHALLANWISESUMMARY
                WHERE
                BUYERCODECHALLANNO = $challanNO
                AND PAIDON is null
                AND FEEPAYSTATUS is null " ;
               // echo $sql;exit;
        $student_challan = DB::select($sql);
        $student_challan_data = '';
        if(!empty($student_challan[0])){
           $student_challan_data =  $student_challan[0];
        }
        // dd($student_challan_data);

        return response()->json(['success' => true, 'student_challan'=>$student_challan_data]);

    }
    public function paidFeeChallan(Request $request) {
        //   dd($request->all());
        $data = $request->all();
        // foreach($data as $val){
        //     dd($val['challanNo']);
        // }
        DB::beginTransaction();

        try {   
            if(!empty($data)){
                foreach($data as $val){
                    $validator = Validator::make($val, [

                        'amountPaid' => 'required|numeric|max:9999999',
                        'paymentMode' => 'required|max:450',
                        'effective_date' => 'required',
                    ], [
                        'amountPaid.required' => ' Amount is required',
                        'amountPaid.numeric' => 'Amount must be a number',
                        'amountPaid.max' => 'Amount cannot exceed 7 digits',
                        'paymentMode.required' => 'Mode of Payment is required',
                        'paymentMode.max' => 'Mode of Payment cannot exceed 450 characters',
                        'effective_date.required' => 'Effefctive Date is required',
                    ]);
                    // Check if validation fails
                    if ($validator->fails()) {
                        $errors = $validator->errors()->toArray();
                        DB::rollBack();
                        return response()->json(['success' => false, 'errors' => $errors], 422);
                    }
                    // $enrollment_no = strtoupper($val['enrollmentNo']);
                    $mode = $val['paymentMode'];
                    $amount = $val['amountPaid'];
                    $effective_date = $val['effective_date'];
                    $fine = $val['fine'];

                    // Use auth()->user()->employee_id instead of $entryUserId
                    // $entryUserId = auth()->user()->id;
                    $dataToUpdate = [
                        // 'sc_enrollment_no' => $enrollment_no,
                        // 'sc_app_id' => $val['aaiId'],
                        // 'sc_session_id' => $val['sessId'],
                        'sc_amount' => $amount,
                        'response_amountPaid' => $amount,
                        'callback_amount' => $amount,
                        'response_netAmount' => $amount,
                        'sc_payment_mode' => $mode,
                        'sc_effective_date' => $effective_date,
                        // 'sc_entry_user_id' => auth()->user()->employee_id,
                        'PAIDMARKED_BY' => auth()->user()->username,
                        'SC_UPDATED_BY_USERNAME' => auth()->user()->username,
                        'UPDATED_AT' => now(),
                        'sc_payType' => 'Account',
                        'response_status' => 'Success',
                        'response_message' => 'Status:00',
                        'response_paidOn' => DB::raw('CURRENT_TIMESTAMP'),
                        'callback_paid_on' => $effective_date,
                        'invoice_status' => 'E',
                        'CALLBACK_AT' => DB::raw('CURRENT_TIMESTAMP'),
                        'sc_challan_status' => 'PAID',
                        'response_time' => DB::raw('CURRENT_TIMESTAMP'),
                        'IS_MANUAL_PAID_MARK' => 1,
                        'fine' => $fine

                    ];
                    $dbUpdateRows =  DB::table('Student_challan')
                                ->where('ID', $val['challanNo'])
                                ->update($dataToUpdate);
                      
                    $dataSt = DB::table('Student_challan')->select('SC_APP_ID', 'SC_CHALLAN_TYPE', 'SC_ENROLLMENT_NO')->where('id', $val['challanNo'])->first();
                    if (!$dataSt->sc_enrollment_no && $dataSt->sc_challan_type == 'Processing_Fee') {
                        $sel = "UPDATE admission_apply_info
                                    SET aai_pf_status = 'Fee Verified',
                                        aai_fee_verification_date = sysdate
                                    WHERE aai_id = ?";

                        $res = DB::update($sel, [$dataSt->sc_app_id]);
                    }
                    // If the update fails, throw an exception to trigger a rollback
                      if ($dbUpdateRows === 0) {
                        throw new \Exception("Failed to update record for Challan No: {$val['challanNo']}");
                    }
                }
                 // Commit the transaction if all updates succeed
                DB::commit();
                //Procedure call in loop on data
                // dd($data);
                foreach($data as $value){
                    try {
                        $procedurenotification = 'UNBLOCK_STUDENT_ONLINE_PAY';
                        $bindings_notification = [
                            'p_enrollment_No' => $value['enrollmentNo'],
                            'p_challan_no' => $value['challanNo']
                        ];
                        $result_notification = DB::executeProcedure($procedurenotification, $bindings_notification);
                    } catch (\Exception $e) {
                        $enrollmentNo = isset($value['enrollmentNo']) ? $value['enrollmentNo'] : '';
                        $challanNo = isset($value['challanNo']) ? $value['challanNo'] : '';
                        \Log::error("Error unblocking student with enrollment number {$enrollmentNo} and challan no {$challanNo}: " . $e->getMessage());
                    }
                }
                return response()->json(['success' => true, 'message' => 'Records Updated successfully'], 200);
            }
        }catch (\Exception $e) {
            // Rollback the transaction if any exception occurs
            DB::rollBack();
            return response()->json(['success' => false, 'errors' => $e->getMessage()], 500);
        }
    }
    public function paidMvFeeChallan(Request $request) {
        //   dd($request->all());
        $data = $request->all();
        // foreach($data as $val){
        //     dd($val['challanNo']);
        // }
        DB::beginTransaction();

        try {
            if(!empty($data)){
                foreach($data as $val){
                    $validator = Validator::make($val, [

                        'amountPaid' => 'required|numeric|max:9999999',
                        'paymentMode' => 'required|max:450',
                        'effective_date' => 'required',
                    ], [
                        'amountPaid.required' => ' Amount is required',
                        'amountPaid.numeric' => 'Amount must be a number',
                        'amountPaid.max' => 'Amount cannot exceed 7 digits',
                        'paymentMode.required' => 'Mode of Payment is required',
                        'paymentMode.max' => 'Mode of Payment cannot exceed 450 characters',
                        'effective_date.required' => 'Effefctive Date is required',
                    ]);
                    // Check if validation fails
                    if ($validator->fails()) {
                        $errors = $validator->errors()->toArray();
                        DB::rollBack();
                        return response()->json(['success' => false, 'errors' => $errors], 422);
                    }
                    // $enrollment_no = strtoupper($val['enrollmentNo']);
                    $mode = $val['paymentMode'];
                    $amount = $val['amountPaid'];
                    $effective_date = $val['effective_date'];

                    // Use auth()->user()->employee_id instead of $entryUserId
                    // $entryUserId = auth()->user()->id;
                    $dataToUpdate = [
                        'PAIDAMT' => $amount,
                        'FEETYPE' => 'Manual_'.$mode,
                        'PAIDON' => $effective_date,
                        // 'ENTRY_USER_ID' => auth()->user()->employee_id,
                        'PAIDMARKED_BY' => auth()->user()->username,
                        'CREATIONDATE' => DB::raw('CURRENT_TIMESTAMP'),
                        'REMARKS' => 'Paid Mark Manually',
                        'FEEPAYSTATUS' => 'Paid'

                    ];
                    $dbUpdateRows =  DB::table('MV_FEECHALLANWISESUMMARY')
                                ->where('BUYERCODECHALLANNO', $val['challanNo'])
                                ->update($dataToUpdate);
                      // If the update fails, throw an exception to trigger a rollback
                      if ($dbUpdateRows === 0) {
                        throw new \Exception("Failed to update record for Challan No: {$val['challanNo']}");
                    }
                }
                 // Commit the transaction if all updates succeed
                DB::commit();
                //Procedure call in loop on data
                foreach($data as $value){
                    try {
                        $procedurenotification = 'UNBLOCK_STUDENT_ONLINE_PAY';
                        $bindings_notification = [
                            'p_enrollment_No' => $value['enrollmentNo'],
                            'p_challan_no' => $value['challanNo']
                        ];
                        $result_notification = DB::executeProcedure($procedurenotification, $bindings_notification);
                    } catch (\Exception $e) {
                        $enrollmentNo = isset($value['enrollmentNo']) ? $value['enrollmentNo'] : '';
                        $challanNo = isset($value['challanNo']) ? $value['challanNo'] : '';
                        \Log::error("Error unblocking student with enrollment number {$enrollmentNo} and challan no {$challanNo}: " . $e->getMessage());
                    }
                }
                return response()->json(['success' => true, 'message' => 'Records Updated successfully'], 200);
            }
        }catch (\Exception $e) {
            // Rollback the transaction if any exception occurs
            DB::rollBack();
            return response()->json(['success' => false, 'errors' => $e->getMessage()], 500);
        }
    }
    public function filterPaidMark(Request $request){
        // dd($request->status);
        $fromClause ='';
        $toClause ='';
        $modeOfPaymentClause ='';
        if(!empty($request->fromData)){
            // $fromClause = 'AND stdnt_chaln.CREATED_AT >= TO_DATE('.$request->fromData.', 'YYYY-MM-DD')';
            $fromDate = date('Y-m-d', strtotime($request->fromData . ' -1 day'));
            $fromClause = "AND callback_paid_on >= TO_DATE('$fromDate', 'YYYY-MM-DD')";
        }
        if(!empty($request->toData)){
            $toDate = date('Y-m-d', strtotime($request->toData . ' +1 day'));
            $toClause = "AND callback_paid_on <= TO_DATE('$toDate', 'YYYY-MM-DD')";
        }
        if(!empty($request->modeOfPayment)){
            $modeOfPaymentClause = "AND sc_payment_mode = '".$request->modeOfPayment."'";
        }
        // dd( $fromClause ,  $toClause,  $modeOfPaymentClause);
        $sql = "SELECT
                SC_STUDENTNAME As spi_name,
                ID AS buyercodechallanno,
                SC_ENROLLMENT_NO  AS regno,
                sc_app_id AS applicant_id,
                SC_TUITION_FEE_AMNT AS TUTIONFEE,
                INVOICEAMOUNT AS payableAmount,
                TOTAL_AMOUNT_PAYABLE AS TOTALAMOUNTPAYABLE,
                SC_DUE_DATE AS DUEDATE,
                SC_VALID_DATE AS VALIDDATE,
                RESPONSE_AMOUNTPAID AS PAIDAMT,
                TO_CHAR(TO_TIMESTAMP_TZ(RESPONSE_PAIDON, 'YYYY-MM-DD HH24:MI:SS TZH:TZM'), 'DD-MM-YYYY') AS PAIDON,
                sc_challan_status AS challanstatus,
                SC_PAYMENT_MODE AS paymentmode,
                SC_CHALLAN_TYPE AS FEETYPESUMMARY,
                FINE,
                SC_SESSION_ID As sess_id
                FROM
                    student_challan
                WHERE
                    IS_MANUAL_PAID_MARK = '1'
                    AND sc_challan_status IN ('PAID')
                    $fromClause
                    $toClause
                    $modeOfPaymentClause
                    ";
                //    echo $sql;exit;
        // dd($sql);
        $student_challan = DB::select($sql);
        // dd($student_challan);
        // $student_challan_data = '';
        // if(!empty($student_challan)){
        //    $student_challan_data =  $student_challan[0];
        // }
        // dd($student_challan);
        return DataTables::of($student_challan)->toJson();
        // return response()->json(['success' => true, 'student_challan'=>$student_challan]);
    }
    public function filterMvPaidMark(Request $request){
        // dd($request->status);
        $fromClause ='';
        $toClause ='';
        $modeOfPaymentClause ='';
        if(!empty($request->fromData)){
            // $fromClause = 'AND stdnt_chaln.CREATED_AT >= TO_DATE('.$request->fromData.', 'YYYY-MM-DD')';
            $fromClause = "AND PAIDON >= TO_DATE('$request->fromData', 'YYYY-MM-DD')";
        }
        if(!empty($request->toData)){
            $toDate = date('Y-m-d', strtotime($request->toData . ' +1 day'));
            $toClause = "AND PAIDON <= TO_DATE('$toDate', 'YYYY-MM-DD')";
        }
        if(!empty($request->modeOfPayment)){
            $modeOfPaymentClause = "AND FEETYPE = 'Manual_".$request->modeOfPayment."'";
        }
        // dd( $fromClause ,  $toClause,  $modeOfPaymentClause);
        $sql ="SELECT *
                FROM (
                    SELECT
                        STUDENTNAME AS spi_name,
                        BUYERCODECHALLANNO AS buyercodechallanno,
                        REGNO AS regno,
                        PVNOSLIPNOSTUDENTID AS applicant_id,
                        TUTIONFEE AS TUTIONFEE,
                        TOTALAMOUNTPAYABLE AS payableAmount,
                        TOTALAMOUNTPAYABLE AS TOTALAMOUNTPAYABLE,
                        DUEDATE AS DUEDATE,
                        VALIDDATE AS VALIDDATE,
                        PAIDAMT AS PAIDAMT,
                        -- PAIDON AS PAIDON,
                       TO_CHAR(PAIDON, 'DD-MM-YYYY') AS PAIDON,
                        FEEPAYSTATUS AS challanstatus,
                        FEETYPE AS paymentmode,
                        FEETYPESUMMARY AS FEETYPESUMMARY,
                        FINE,
                        SESSION_ID AS sess_id
                    FROM
                        MV_FEECHALLANWISESUMMARY
                    WHERE
                        FEETYPE like 'Manual_%' and PAIDON is not null and FEEPAYSTATUS is not null
                        $fromClause
                        $toClause
                        $modeOfPaymentClause
                )
                WHERE ROWNUM <= 500";
                //    echo $sql;exit;
        // dd($sql);
        $student_challan = DB::select($sql);
        // dd($student_challan);
        // $student_challan_data = '';
        // if(!empty($student_challan)){
        //    $student_challan_data =  $student_challan[0];
        // }
        // dd($student_challan);
        return DataTables::of($student_challan)->toJson();
        // return response()->json(['success' => true, 'student_challan'=>$student_challan]);
    }
    
    public function markChallanUnpaid(Request $request) {
        $challanNo = $request->challanNo;
        
        DB::beginTransaction();
        
        try {
            $dataToUpdate = [
        
                'RESPONSE_NETAMOUNT' => null,
                'PAIDMARKED_BY' => auth()->user()->username,
                'callback_paid_on' => null,
                'response_status' => null,
                'sc_challan_status' => 'UNPAID',
                'callback_amount' => 0,
                'CALLBACK_AT' => null,
                'sc_paytype' => null,
                'RESPONSE_MESSAGE' => null,
                'SC_UPDATED_BY_USERNAME' => auth()->user()->username,
                'UPDATED_AT' => now()
            ];
            
            $dbUpdateRows = DB::table('Student_challan')
                        ->where('ID', $challanNo)
                        ->update($dataToUpdate);
            
            if ($dbUpdateRows === 0) {
                throw new \Exception("Failed to update record for Challan No: {$challanNo}");
            }
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Challan has been marked as UNPAID successfully'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    

}

