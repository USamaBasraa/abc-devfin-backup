<?php

namespace App\Http\Controllers\StudentFinancial;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Grader\Schools;
use App\Models\Grader\Departments;
use DB;
use Carbon\Carbon;
use Validator;
use App\Http\Controllers\StudentFinancial\GenerateSingleChallanController;

class FeeWaiveOffController extends Controller
{
    public function fee_waiveoff_form_index(Request $request){
        $ro_users=['somia.maqsood', 'haider.raza', 'talha.sarfraz', 'abdul.waheed'];
        $admission_users=['asbah.rehman', 'sumaira.arshad', 'sidra.tanveer'];
        $usernames_ro_str = "'" . implode("','", $ro_users) . "'";
        $usernames_adm_str = "'" . implode("','", $admission_users) . "'";

        $waiverData = DB::table('FEE_WAIVER_DETAILS')
                        ->leftJoin('ACADEMIC_SESSIONS', 'FEE_WAIVER_DETAILS.FWD_SESSION_ID', '=', 'ACADEMIC_SESSIONS.sess_id')
                        ->leftJoin('scholarship_type', 'FEE_WAIVER_DETAILS.FWD_SCHOLARSHIP_ID', '=', 'scholarship_type.scholarship_id');

        // if (in_array(auth()->user()->username, $ro_users)) {
        //     $scholarshipsData= $scholarshipsData->whereIn('fin_other_scholarships.ENTRY_USERNAME',  $ro_users);
        // } else  if (in_array(auth()->user()->username, $admission_users)) {
        //     $scholarshipsData= $scholarshipsData->whereIn('fin_other_scholarships.ENTRY_USERNAME', $admission_users);
        // }
        $waiverData= $waiverData->get();
        // dd($waiverData);
        return view('StudentFinancial.feeWaiveoffForm', ['waiverData' => $waiverData, 'firstload' => 1]);
    }
    function getStudentProfile(Request $request){
        $getStudent = new GenerateSingleChallanController();
        $getStudentRec = $getStudent->getStudentRec($request);

        // Retrieve sessions using the AcademicSessionController
        $sessions = \App::call('App\Http\Controllers\Grader\AcademicSessionController@retrieve');
        $sessions = $sessions->toArray();

        $sqlchallanTypes = "select distinct sc_challan_type from student_challan where sc_challan_type is not null";
        $challanTypes = DB::select($sqlchallanTypes);
        $challanTypesMap = [];

            foreach ($challanTypes as $row) {
                if ($row->sc_challan_type === 'Hostel_Fee') {
                    $key = $row->sc_challan_type;                 // original value
                    $label = ucwords(str_replace('_', ' ', strtolower($key)));

                    $challanTypesMap[$key] = $label;
                    break; // Exit the loop after finding the first match
                }
            }

        $scholarships = DB::select("select SCHOLARSHIP_ID, SCHOLARSHIP_NAME from Scholarship_Type where scholarship_doner_type is null and SCHOLARSHIP_DESC='hostel'");

        $waiverByEnrollmentNo= $this->getWaiverByEnrollmentNo($request);
        // dd($waiverByEnrollmentNo);
        // dd($getStudentRec);
        // Initialize an empty array for departments (if you plan to use this in the future)
        $departments = array();
        return response()->json([
            'status' => 'success',
            'studentRec' => $getStudentRec,
            'sessions' => $sessions,
            'challanTypes' => $challanTypesMap,
            'scholarships' => $scholarships,
            'getWaiverByEnrollmentNo' => $waiverByEnrollmentNo
        ]);


    }
    public function submitFeeWaive(Request $request){
        $username = auth()->user()->username;

        // ✅ Manual validator (DO NOT use $request->validate)
        $validator = Validator::make(
            $request->all(),
            [
                'fos_app_id'             => 'required|numeric',
                'sessionId'              => 'required|numeric',
                'hostelite_type'         => 'required',
                'challanType'            => 'required',
                'waive_fee_type'         => 'required',
                'remarks'         => 'required',

                'uni_waiver_amount'      => 'nullable|numeric|required_without:scholarshipId',
                'scholarshipId'          => 'nullable|numeric|required_without:uni_waiver_amount|required_with:schlrshp_waiver_amount',
                'schlrshp_waiver_amount' => 'nullable|numeric|required_with:scholarshipId',
            ],
            [
                // fos_app_id
                'fos_app_id.required' => 'Application ID is required.',
                'fos_app_id.numeric'  => 'Application ID must be a number.',

                // sessionId
                'sessionId.required' => 'Session is required.',
                'sessionId.numeric'  => 'Session must be a valid number.',

                // hostelite_type
                'hostelite_type.required' => 'Please select the hostelite type.',

                // challanType
                'challanType.required' => 'Challan type is required.',

                // waive_fee_type
                'waive_fee_type.required' => 'Please select the fee waiver type.',

                // remarks
                'remarks.required' => 'Remarks cannot be empty.',

                // University waiver
                'uni_waiver_amount.required_without' =>
                    'University waiver amount is required when no scholarship is selected.',
                'uni_waiver_amount.numeric' =>
                    'University waiver amount must be a valid number.',

                // Scholarship
                'scholarshipId.required_without' =>
                    'Please select a scholarship when university waiver amount is not provided.',
                'scholarshipId.numeric' =>
                    'Invalid scholarship selected.',
                'scholarshipId.required_with' =>
                    'Scholarship is required when scholarship waiver amount is provided.',

                // Scholarship waiver amount
                'schlrshp_waiver_amount.required_with' =>
                    'Scholarship waiver amount is required when a scholarship is selected.',
                'schlrshp_waiver_amount.numeric' =>
                    'Scholarship waiver amount must be a valid number.',
            ]
        );

        // ✅ Handle validation errors in SAME response format
        if ($validator->fails()) {
            $messages = '';

            foreach ($validator->errors()->all() as $error) {
                $messages .= "<p>{$error}</p>";
            }

            return response()->json([
                'success' => false,
                'msg' => $messages,
            ]);
        }
         $new_uni_waiver = $request->uni_waiver_amount;
         $new_schlrshp_waiver = $request->schlrshp_waiver_amount;
        // ------------------ BUSINESS LOGIC ------------------

        $unique = [
            'FWD_APP_ID'        => $request->fos_app_id,
            'FWD_ENROLLMENT_NO' => $request->fos_enrollment_no,
            'FWD_SESSION_ID'    => $request->sessionId,
            'FWD_CHALLAN_TYPE'  => $request->challanType,
            'FWD_STATUS'  => 'E',
        ];

        $data = [
            'FWD_HOSTELITE_TYPE'           => $request->hostelite_type,
            'FWD_UNI_WAIVER_AMOUNT'        => $request->uni_waiver_amount,
            'FWD_SCHLRSHIP_WAIVER_AMOUNT'  => $request->schlrshp_waiver_amount,
            'FWD_WAIVE_FEE_TYPE'           => $request->waive_fee_type,
            'FWD_REMARKS'                  => $request->remarks,
            'FWD_SCHOLARSHIP_ID'           => $request->scholarshipId
        ];
        try {
            DB::beginTransaction();

            // ------------------ FETCH EXISTING CHALLAN ------------------
            $hostelExisitngChallan = DB::table('STUDENT_CHALLAN')
                ->where('SC_SESSION_ID', $request->sessionId)
                ->where('SC_APP_ID', $request->fos_app_id)
                ->where('SC_CHALLAN_TYPE', 'Hostel_Fee')
                ->where('sc_challan_status', 'UNPAID')
                ->whereIn('SC_HOSTEL_FEE_TYPE', ['Hostel_Full_semster'])
                ->first();

            // ------------------ UPDATE HOSTEL CHALLAN (IF EXISTS) ------------------
            if ($hostelExisitngChallan) {

                // 🔒 NEGATIVE SAFE SECURITY AMOUNT
                $securityAmount = max(
                    0,
                    (int) $hostelExisitngChallan->ffs_security_amt
                    - (int) ($request->schlrshp_waiver_amount ?? 0)
                    - (int) ($request->uni_waiver_amount ?? 0)
                );

                $bindings = [
                    'p_schol_id'            => $hostelExisitngChallan->sc_school_id,
                    'p_dept_id'             => $hostelExisitngChallan->sc_department_id,
                    'p_prog_id'             => $hostelExisitngChallan->sc_program_id,
                    'p_session'             => $hostelExisitngChallan->sc_session_id,
                    'p_due_date'            => $hostelExisitngChallan->sc_due_date,
                    'p_valid_date'          => $hostelExisitngChallan->sc_valid_date,
                    'p_ffs_student_type'    => $hostelExisitngChallan->sc_hostel_fee_type,
                    'p_hostel_security'     => $securityAmount,
                    'p_hostel_studentType'  => $request->hostelite_type,
                    'p_no_of_days'          => $hostelExisitngChallan->sc_hostel_days,
                    'refrigeratorCheckBox'  => 'NO',
                    'conditionerCheckBox'   => 'NO',
                    'electricHeaterCheckBox'=> 'NO',
                    'hostelRemarks'         => 'For Fee Waive off revision / entry -- Remarks: '.$request->remarks,
                    'p_AAI_ID'              => $request->hostelite_type === 'Existing'
                                                ? strtoupper($request->fos_enrollment_no)
                                                : $request->fos_app_id,
                    'session_type'          => $hostelExisitngChallan->sc_session_type,
                    'challan_no'            => $hostelExisitngChallan->challan_no,
                    'p_entry_username'      => auth()->user()->username,
                    'p_studentType'         => $hostelExisitngChallan->sc_student_status,
                ];

                DB::executeProcedure('fin_new_std_hostel_chln_updt', $bindings);
            }

            // ------------------ INSERT / UPDATE FEE_WAIVER_DETAILS ------------------
            if (empty($request->updateId)) {

                $duplicate = DB::table('FEE_WAIVER_DETAILS')
                    ->where($unique)
                    ->first();

                if (!$duplicate) {
                    DB::table('FEE_WAIVER_DETAILS')->insert(
                        array_merge($unique, $data, [
                            'FWD_ENTRY_USERNAME' => $username,
                            'FWD_ENTRY_TIME'     => now(),
                        ])
                    );
                    $message = 'Saved Successfully';
                } else {
                    DB::table('FEE_WAIVER_DETAILS')
                    ->where('FWD_ID', $duplicate->fwd_id)
                    ->update([
                        'FWD_HOSTELITE_TYPE' => $request->hostelite_type,

                        'FWD_UNI_WAIVER_AMOUNT' =>
                            DB::raw('NVL(FWD_UNI_WAIVER_AMOUNT,0) + ' . (int)$request->uni_waiver_amount),

                        'FWD_SCHLRSHIP_WAIVER_AMOUNT' =>
                            DB::raw('NVL(FWD_SCHLRSHIP_WAIVER_AMOUNT,0) + ' . (int)$request->schlrshp_waiver_amount),

                        'FWD_WAIVE_FEE_TYPE' => $request->waive_fee_type,
                        'FWD_REMARKS' => $request->remarks,
                        'FWD_SCHOLARSHIP_ID' => $request->scholarshipId,

                        'FWD_UPDATED_BY'  => $username,
                        'FWD_UPDATE_TIME' => now(),
                    ]);

                    $message = 'Updated Successfully';
                }

            } else {

                $duplicate = DB::table('FEE_WAIVER_DETAILS')
                    ->where($unique)
                    ->where('FWD_ID', '!=', $request->updateId)
                    ->first();

                if ($duplicate) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'msg' => 'Data already exists',
                    ]);
                }

                DB::table('FEE_WAIVER_DETAILS')
                    ->where('FWD_ID', $request->updateId)
                    ->update([
                        'FWD_HOSTELITE_TYPE' => $request->hostelite_type,

                        'FWD_UNI_WAIVER_AMOUNT' =>
                            DB::raw('NVL(FWD_UNI_WAIVER_AMOUNT,0) + ' . (int)$request->uni_waiver_amount),

                        'FWD_SCHLRSHIP_WAIVER_AMOUNT' =>
                            DB::raw('NVL(FWD_SCHLRSHIP_WAIVER_AMOUNT,0) + ' . (int)$request->schlrshp_waiver_amount),

                        'FWD_WAIVE_FEE_TYPE' => $request->waive_fee_type,
                        'FWD_REMARKS' => $request->remarks,
                        'FWD_SCHOLARSHIP_ID' => $request->scholarshipId,

                        'FWD_UPDATED_BY'  => $username,
                        'FWD_UPDATE_TIME' => now(),
                    ]);


                $message = 'Edited Successfully';
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'msg' => $message,
            ]);

        } catch (\Throwable $e) {

            DB::rollBack();

            \Log::error('Fee Waiver Transaction Failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'msg' => 'An unexpected error occurred while processing the fee waiver.',
            ]);
        }
    }

    public function getWaiverByEnrollmentNo(Request $request){
        // Validate the input
        $request->validate([
            'studentEnrollment' => 'required|string|max:255',
        ]);
        $studentEnrollment = $request->studentEnrollment;
        // Retrieve scholarships based on the given fos_enrollment_no with joins
        $waiverData = DB::table('FEE_WAIVER_DETAILS')
                        ->leftJoin('ACADEMIC_SESSIONS', 'FEE_WAIVER_DETAILS.FWD_SESSION_ID', '=', 'ACADEMIC_SESSIONS.sess_id')
                        ->leftJoin('scholarship_type', 'FEE_WAIVER_DETAILS.FWD_SCHOLARSHIP_ID', '=', 'scholarship_type.scholarship_id')

                    ->where(function ($q) use ($studentEnrollment) {
                            // If $studentEnrollment is numeric, search app_id
                            if (is_numeric($studentEnrollment)) {
                                $q->where('FWD_APP_ID', $studentEnrollment);
                            } else {
                                // Otherwise, search enrollment_no
                                $q->where('FWD_ENROLLMENT_NO', $studentEnrollment);
                            }
                        })
                        ->get();

        return $waiverData;

    }
    public function editFeeWaiver($id){
        // Find the scholarship record by ID
        $waiverDetails = DB::table('FEE_WAIVER_DETAILS')
            ->where('FWD_ID', $id)  // Make sure 'id' is in lowercase
            ->first();
        if ($waiverDetails) {
            // Return the waiverDetails details as a response
            return response()->json([
                'status' => 'success',
                'data' => $waiverDetails
            ]);
        } else {
            // If no scholarship found, return error response
            return response()->json([
                'status' => 'error',
                'message' => 'Fee waiver not found'
            ]);
        }
    }


}
