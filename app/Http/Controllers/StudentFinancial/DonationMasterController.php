<?php


namespace  App\Http\Controllers\StudentFinancial;
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

class DonationMasterController extends Controller{

        function donationRecordForm(){
            $sessions = \App::call('App\Http\Controllers\Grader\AcademicSessionController@retrieve');
            return view('StudentFinancial.donation_record_form',compact('sessions'));

        }
        public function addDonation(Request $request){
            // dd($request->all());
           $validator = Validator::make($request->all(), [
                // Donor details
                'f_donor_name'       => 'required|string|max:255',
                'l_donor_name'       => 'required|string|max:255',
                'donor_email'        => 'required|email|max:255',
                'donor_number'       => 'required|string|max:20',   // phone as string; your sample is long
                'donor_account'     => 'required|string|max:50',
                'donor_ntn'         => 'required|string|max:50',
                'donor_cnic'        => 'required|string|max:50',
                'donor_address'     => 'required|string|max:500',
                'donor_country'     => 'required|string|max:500',
                'donor_city'        => 'required|string|max:500',

                // General donation/session
                'session'          => 'required',
                'donation_type'    => 'required',   // e.g. Zakat, Sadqa, etc.

                // Payment / deposit info
                'amount_donated'   => 'required|numeric|min:0',
                'bank_deposited'   => 'required|string|max:255',
                'bank_branch'      => 'required|string|max:255',
                'date_deposit'     => 'required|date',
                'deposit_type'     => 'required|string|max:50',   // e.g. Zakat / Other

                // Student-related fields (optional if donor can give without mapping to a student)
                'student_name'       => 'nullable|string|max:255',
                'registration_number'=> 'nullable|string|max:50',
                'student_program'      => 'nullable|string|max:255',
                'student_session'    => 'nullable|integer',
                'student_allocation' => 'nullable|string|max:255',

                // Internal verification / status
                'internal_verification' => 'required|string|max:255',
                'internal_date'         => 'required|date',
                'internal_remarks'      => 'nullable|string|max:500',
                'final_status'          => 'required|string|max:255', // e.g. "Acknowledgment Sent to Donor"

            ], [

                // Custom messages for main donor fields
                'f_donor_name.required'    => 'Please enter the donor first name.',
                'l_donor_name.required'    => 'Please enter the donor last name.',
                'donor_email.required'     => 'Please enter the donor email.',
                'donor_email.email'        => 'Please enter a valid email address.',
                'donor_number.required'    => 'Please enter the donor contact number.',
                'donor_number.max'         => 'The contact number may not be greater than 20 characters.',
                'donor_account.required'   => 'Please enter the donor account name/identifier.',
                'donor_cnic.required'      => 'Please enter the donor CNIC.',
                'donor_address.required'   => 'Please enter the donor address.',
                'donor_country.required'   => 'Please enter the donor country.',
                'donor_city.required'      => 'Please enter the donor city.',

                'session.required'       => 'Please select a session.',

                'donation_type.required' => 'Please select the donation type.',

                'amount_donated.required'=> 'Please enter the donated amount.',
                'amount_donated.numeric' => 'The donated amount must be a valid number.',
                'amount_donated.min'     => 'The donated amount cannot be negative.',

                'bank_deposited.required'=> 'Please specify the bank where the amount was deposited.',
                'bank_branch.required'   => 'Please specify the bank branch.',
                'date_deposit.required'  => 'Please provide the deposit date.',
                'date_deposit.date'      => 'The deposit date must be a valid date.',

                'deposit_type.required'  => 'Please select the deposit type.',

                // Optional: some messages for student/internal fields
                'internal_date.date'     => 'The internal verification date must be a valid date.',

            ]);

            if ($validator->fails()) {
                return Response::json([
                    'success' => false,
                    'error'   => $validator->getMessageBag()->toArray()
                ], 400);
            }
            $username = auth()->user()->username;

            $data = [
                // Donor details
                'FIRST_NAME'          => $request->f_donor_name,
                'Last_NAME'           => $request->l_donor_name,
                'EMAIL'               => $request->donor_email,
                'PHONE'               => $request->donor_number,
                'DONOR_ACCOUNT'       => $request->donor_account,
                'DONOR_NTN'           => $request->donor_ntn,
                'CNIC'                => $request->donor_cnic,
                'DONOR_ADDRESS'       => $request->donor_address,
                'COUNTRY'             => $request->donor_country,
                'CITY'                => $request->donor_city,

                // Donation info
                'SESSION_ID'          => $request->session,
                'DONATION_TYPE'       => $request->donation_type,

                'CURRENCY'            => 'PKR',
                'AMOUNT'              => $request->amount_donated,
                'BANK_DEPOSITED'      => $request->bank_deposited,
                'BANK_BRANCH'         => $request->bank_branch,
                'DATE_DEPOSIT'        => $request->date_deposit,
                'DEPOSIT_TYPE'        => $request->deposit_type,

                // Student info
                'STUDENT_NAME'        => $request->student_name,
                'ENROLLMENT_NO'       => $request->registration_number,
                'STUDENT_PROGRAM'     => $request->student_program,
                'STUDENT_SESSION'     => $request->student_session,
                'STUDENT_ALLOCATION'  => $request->student_allocation,

                // Internal verification
                'INTERNAL_VERIFICATION' => $request->internal_verification,
                'INTERNAL_DATE'          => $request->internal_date,
                'INTERNAL_REMARKS'       => $request->internal_remarks,

                'FINAL_STATUS'        => $request->final_status,
                'ENTRY_USERNAME'      => $username,
                'SOURCE_TYPE'      => 'cms',

                'CREATED_AT'          => now(),
                'UPDATED_AT'          => now(),
            ];
            // dd($dat?a);
            // INSERT vs UPDATE
            // dd($request->mainId);
            if (empty($request->mainId)) {

                    DB::table('DONATIONS')->insert($data);
                    $resp = [true, 'Saved Successfully'];

            } else {

                    $data['UPDATED_AT'] = now();

                    DB::table('DONATIONS')
                        ->where('ID', $request->mainId)
                        ->update($data);

                    $resp = [true, 'Updated Successfully'];
            }

            return response()->json([
                'success' => $resp[0],
                'msg'     => $resp[1],
            ]);

        }
        protected function donationRecordList(Request $request){
            // dd($request->all());
            $data = DB::table('DONATIONS as d')
                ->select('d.ID as donation_id','d.*','sc.*')
                ->leftjoin ('student_challan as sc', 'd.CHALLAN_ID', '=', 'sc.challan_no');

                // if(!empty($request->school_sel) ){
                //     $data =  $data->where('ev.E_SCHOOL_ID', $request->school_sel);
                // }
                // if(!empty($request->department)){
                //     $data =  $data->where('ev.E_DEPT_ID', $request->department);
                // }
                // if(!empty($request->url)){
                //     $data =  $data->where('ev.E_URL', $request->url);
                // }
                // // if(!empty($request->status)){
                // //     $data =  $data->where('ev.E_STATUS', $request->status);
                // // }
                // if(!empty($request->reg_fee)){
                //     $data =  $data->where('ev.E_REGISTRATION_FEE', $request->reg_fee);
                // }
                // if(!empty($request->event_type)){
                //     $data =  $data->where('ev.E_EVENT_TYPE', $request->event_type);
                // }
                $data =  $data->orderBy('d.ID', 'desc')->get();

            //    dd($data);
            return DataTables::of($data)->toJson();
        }
        public function deleteDonation(Request $request){
            // dd($request->all());
            $affectedRows = DB::table('DONATIONS')
                ->where('ID', $request->mainId)
                ->delete();  // PERMANENT DELETE
            // dd($affectedRows);
            if ($affectedRows > 0) {
                return response()->json([
                    'success' => true,
                    'data'    => 'Record deleted permanently'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'data'    => 'Deletion failed — record not found'
                ]);
            }
        }

}
