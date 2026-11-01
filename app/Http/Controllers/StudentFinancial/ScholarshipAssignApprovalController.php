<?php

namespace App\Http\Controllers\StudentFinancial;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Grader\Schools;
use App\Models\Grader\Departments;
use DB;

class ScholarshipAssignApprovalController extends Controller
{
    //
    function index(Request $request){

        $sessions = \App::call('App\Http\Controllers\Grader\AcademicSessionController@retrieve');
        // dd($sessions);
        $scholarships = DB::table('scholarship_type')
        ->select('scholarship_id', 'scholarship_name')
        ->get();
        $schools =  Schools::all();
        $departments = array();
        // dd($scholarships);
        $controller = new ScholarshipAssignController();
        $studentScholarshipRec = $controller->getStudentScholarshipRec();
        // dd($studentScholarshipRec);

        // dd($studentScholarshipRec);

        return view('StudentFinancial.scholarshipApproval',compact('scholarships','sessions','schools','departments','studentScholarshipRec'));
    }
    public function updateApprovalStatus(Request $request)
{
    // Retrieve authenticated user
    $user = auth()->user()->username;


    // return response()->json(['message' => 'Overall Scholarship Percentage is greater than 100%'], 400);

    // return response()->json(['status' => 'success', 'message' => 'Approval status updated successfully']);


    $updatedRecord = DB::table('fin_scholarship_assigned')->where('fsa_id', $request->input('fsa_id'))->first();

    // dd($updatedRecord);

    $totalScholarship = DB::table('STUDENTS_SCHOLARSHIPS_DETAILS')
    ->where('ssd_sai_enrollment_no', strtoupper($updatedRecord->fsa_enrollment_no))
    ->where('ssd_sess_id', $updatedRecord->fsa_sess_id)
    ->whereNotIn('ssd_type', [$updatedRecord->fsa_schlr_ship_id])
    ->sum('ssd_percentage');

    $updatedScholarship=$updatedRecord->fsa_percentage;


      // Check if total scholarship + new scholarship is greater than 100
      if (($totalScholarship + $updatedScholarship) > 100) {
        // If greater than 100%, return "Overall Scholarship Percentage is greater than 100%"
        return response()->json(['message' => 'Overall Scholarship Percentage is greater than 100%'], 400);
    }



    // Start a transaction
    DB::beginTransaction();

    try {
        // Retrieve and update the record in the fin_scholarship_assigned table
        $affected = DB::table('fin_scholarship_assigned')
                      ->where('fsa_id', $request->input('fsa_id'))
                      ->update([
                          'fsa_ro_status' => $request->input('action') === 'approve' ? 'Approved' : 'Disapproved',
                          'fsa_approval_user' => $user
                      ]);

        if (!$affected) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'error' => 'Record not found'], 404);
        }

        // Retrieve the updated record to determine the new status
        $updatedRecord = DB::table('fin_scholarship_assigned')->where('fsa_id', $request->input('fsa_id'))->first();
        $newStatus = $updatedRecord->fsa_ro_status;
        $newScholarshipAmount = $updatedRecord->fsa_given_scholarship_amount;

        if ($request->input('action') === 'approve') {
            // Update or insert a record into students_scholarships_details table
            DB::table('students_scholarships_details')
                ->updateOrInsert(
                    [
                        'ssd_sai_enrollment_no' => $updatedRecord->fsa_enrollment_no,
                        'ssd_sess_id' => $updatedRecord->fsa_sess_id,
                        'ssd_type' => $updatedRecord->fsa_schlr_ship_id,
                    ],
                    [
                        'ssd_percentage' => $updatedRecord->fsa_percentage,
                        'scd_entry_username' => $user,
                    ]
                );

            // Check paid and unpaid rows
            $paidRows = DB::table('student_challan')
                ->where('sc_session_id', $updatedRecord->fsa_sess_id)
                ->where('sc_enrollment_no', $updatedRecord->fsa_enrollment_no)
                ->whereNotNull('callback_at')
                ->whereNotNull('callback_paid_on')
                ->where('sc_challan_status', 'PAID')
                ->count();

            $unpaidRows = DB::table('student_challan')
                ->where('sc_session_id', $updatedRecord->fsa_sess_id)
                ->where('sc_enrollment_no', $updatedRecord->fsa_enrollment_no)
                ->whereNotNull('callback_at')
                ->whereNotNull('callback_paid_on')
                ->where('sc_challan_status', 'UNPAID')
                ->count();

            if ($paidRows > 0 && $unpaidRows == 0) {
                // $sumActualAmount = DB::table('student_challan')
                //     ->where('sc_session_id', $updatedRecord->fsa_sess_id)
                //     ->where('sc_enrollment_no', $updatedRecord->fsa_enrollment_no)
                //     ->whereNotNull('callback_at')
                //     ->whereNotNull('callback_paid_on')
                //     ->where('sc_challan_status', 'PAID')
                //     ->select('actual_amount')
                //     ->distinct('actual_amount');

                    $sel=DB::select("select distinct sc_tuition_fee_amnt from student_challan where sc_enrollment_no ='$updatedRecord->fsa_enrollment_no' and sc_session_id=$updatedRecord->fsa_sess_id and  callback_at is not null  and callback_paid_on is not null  and sc_challan_status ='PAID' ");
                    $sumActualAmount=$sel[0]->sc_tuition_fee_amnt;

                $sumInvoiceAmount = DB::table('student_challan')
                    ->where('sc_session_id', $updatedRecord->fsa_sess_id)
                    ->where('sc_enrollment_no', $updatedRecord->fsa_enrollment_no)
                    ->whereNotNull('callback_at')
                    ->whereNotNull('callback_paid_on')
                    ->where('sc_challan_status', 'PAID')
                    ->sum('invoiceamount');

                $challanType = DB::table('student_challan')
                    ->where('sc_session_id', $updatedRecord->fsa_sess_id)
                    ->where('sc_enrollment_no', $updatedRecord->fsa_enrollment_no)
                    ->whereNotNull('callback_at')
                    ->whereNotNull('callback_paid_on')
                    ->where('sc_challan_status', 'PAID')
                    ->value('sc_challan_type');

                DB::table('fin_fee_adjustment')->insert([
                    'ffa_enrollment_no' => $updatedRecord->fsa_enrollment_no,
                    // 'ffa_sess_id' => $updatedRecord->fsa_sess_id,
                    'ffa_actual_amount' => $sumActualAmount,
                    'ffa_invoice_amount' => $sumInvoiceAmount,
                    'ffa_surplus_amount' => $newScholarshipAmount,
                    'ffa_challan_type' => $challanType
                ]);
            } else {
                // If there are 'UNPAID' rows, call the procedure
                DB::executeProcedure('update_challan_farhan', [
                    'enrollment_no' => $updatedRecord->fsa_enrollment_no,
                    'session' => $updatedRecord->fsa_sess_id,
                ]);
            }
        }

        // Commit the transaction
        DB::commit();

        return response()->json(['status' => 'success', 'message' => 'Approval status updated successfully']);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['status' => 'error', 'error' => 'An error occurred while updating approval status', 'message' => $e->getMessage()], 500);
    }
}


}
