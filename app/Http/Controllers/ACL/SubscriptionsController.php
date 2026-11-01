<?php

namespace App\Http\Controllers\ACL;

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
use Illuminate\Support\Facades\Http;
use Response;
use Validator;
use DataTables;
use DateTime;
use App\Http\Controllers\ACL\ApiController;

class SubscriptionsController extends Controller{

        function userSubscriptions(){
                // Make the PUT request to the external API
                // $data = [
                //             "UserInfo" => [
                //                 "employeeNo" => "79",
                //                 "name" => "Baig",
                //                 "userType" => "normal",
                //                 "Valid" => [
                //                     "enable" => true,
                //                     "beginTime" => "2024-09-19T17:30:08",
                //                     "endTime" => "2024-10-19T17:30:08"
                //                 ],
                //                 "doorRight" => "1",
                //                 "RightPlan" => [
                //                     [
                //                         "doorNo" => 1,
                //                         "planTemplateNo" => "1"
                //                     ]
                //                 ]
                //             ]
                //         ];
                // $data = [
                //     "UserInfoSearchCond" => [
                //         "searchID" => "0",
                //         /*required, string type, search ID, which is used to confirm the upper-level
                //         platform or system. If the platform or the system is the same one during two
                //         searches, the search history will be saved in the memory to speed up next
                //         searching*/
                //         "searchResultPosition" => 0,
                //         /*required, integer32 type, the start position of the search result in the
                //         result list. When there are multiple records and you cannot get all search
                //         results at a time, you can search for the records after the specified position
                //         next time*/
                //         "maxResults" => 1,
                //         /*required, integer32 type, maximum number of search results. If maxResults
                //         exceeds the range returned by the device capability, the device will return the
                //         maximum number of search results according to the device capability and will
                //         not return an error message*/
                //         "EmployeeNoList" => [
                //             [
                //                 /*optional, person ID list (if this node does not exist or is empty, it
                //                 indicates searching for all person information)*/
                //                 "employeeNo" => "78"
                //                 /*optional, string type, employee No. (person ID)*/
                //             ]
                //         ],
                //         "fuzzySearch" => "",
                //         /*optional, string, key words for fuzzy search*/
                //         "groupIdList" => [1, 2, 3, 4],
                //         /*optional, array of int, range of the department No. of local time and
                //         attendance*/
                //         "arrangeType" => "personal"
                //         /*optional, string, shift schedule type: "" (shift schedule by individual).
                //         Currently only the shift schedule by individual is supported. If this node
                //         exists, it indicates searching for all persons with shift schedule by
                //         individual*/
                //     ]
                // ];
                // $data = [
                //     "UserInfo"=>[
                //         "employeeNo"=>"79",
                //         /*required, string, employee No. (person ID)*/
                //         "name"=>"BAIG HAS CHANGED to DEX",
                //         /*optional, string, person name*/
                //         "userType"=>"normal",
                //         /*required, string, person type=> "normal"-normal person (household), "visitor",
                //         "blackList"-person in blocklist*/
                //         /*"closeDelayEnabled"=> "false",
                //         /*optional, boolean, whether to enable door close delay=> "true"-yes, "false"-
                //         no*/
                //         "Valid"=>[
                //             /*required, parameters of the effective period, the effective period can be a
                //             period of time between 1970-01-01 00:00:00 and 2037-12-31 23:59:59*/
                //             "enable"=> true,
                //             /*required, boolean, whether to enable the effective period=> "false"-disable,
                //             "true"-enable. If this node is set to "false", the effective period is
                //             permanent*/
                //             "beginTime"=>"2024-09-20T17:30:08",
                //             /*required, start time of the effective period (if timeType does not exist or
                //             is "local", the beginTime is the device local time, e.g., 2017-08-01T17:30:08;
                //             if timeType is "UTC", the beginTime is UTC time, e.g.,
                //             2017-08-01T17:30:08+08:00)*/
                //             "endTime"=>"2024-10-20T17:30:08",
                //             /*required, end time of the effective period (if timeType does not exist or is
                //             "local", the endTime is the device local time, e.g., 2017-08-01T17:30:08; if
                //             timeType is "UTC", the endTime is UTC time, e.g., 2017-08-01T17:30:08+08:00)*/
                //             "timeType"=>"local"
                //             /*optional, string, time type: "local"- device local time, "UTC"- UTC time*/
                //         ]
                //     ]
                // ];
                //         $response = Http::withDigestAuth('admin', 'AbcBnu123')
                //                     ->withHeaders([
                //                         'Accept' => 'application/json',
                //                     ])
                //                     ->put('http://172.20.138.29/ISAPI/AccessControl/UserInfo/Modify?format=json', $data);
                //             // Check if the external API call was successful
                //             dd($response);
                //             if ($response->successful()) {
                //                 // You can log the success message or perform further actions here
                //                 $mesg .= ' and external API call succeeded';
                //             } else {
                //                 // Handle the error case
                //                 $mesg .= ' but external API call failed';
                //                 $success = false;
                //             }
            ////////////////
            $sessions = DB::select("select * from ACADEMIC_SESSIONS where SESS_STATUS = 'E' ORDER BY SESS_ID DESC");
            // dd($sessions);
            return view('acl.allSubscriptions',compact('sessions'));
        }
        public function getSubscriptionList(Request $request){
            // dd($request->all());
            $data = DB::table('SUBSCRIPTIONS as sub')
                ->select(
                        'acs.SESS_NAME',

                        'sub.ID',
                        'sub.USER_ID',
                        'sub.STUDENT_FULL_NAME',
                        'sub.NOTIFY_EMAIL_EXPIRY',
                        'sub.SUBSCRIPTION_DATE',
                        'sub.EXPIRATION_DATE',
                        'sub.CURRENT_STATUS',

                        'sp.SP_CATEGORY',
                        'sp.SP_USER_TYPE',//this is coming from subscriptionplan table all plans are for students only
                        'sp.SP_PLAN_TENURE',
                        'sp.SP_PRICING',
                        'sp.SP_SPORTS_PRICING',
                        'sp.SP_DETAIL',
                        'CHALLAN_ID',
                        'sc.SC_AMOUNT',
                        'sc.SC_GYM_FEE',

                        'sc.CALLBACK_PAID_ON',
                        'sc.SC_CHALLAN_STATUS',
                        'sc.response_status',

                        'sa.SA_ID as discount_id',
                        'sa.SA_SPORTS_CATEGORY',
                        'scholshp.SCHOLARSHIP_NAME',

                        )
                        ->leftjoin('SUBSCRIPTION_PLANS as sp', 'sp.sp_id', 'sub.sp_id')
                        ->leftjoin('ACADEMIC_SESSIONS as acs', 'acs.sess_id', 'sp.SP_SESSION')
                        ->leftjoin('STUDENT_CHALLAN as sc', 'sc.id', 'sub.CHALLAN_ID')
                        ->leftJoin('SPORTS_APPLICANTS as sa', function ($join) {
                            $join->on(DB::raw('LOWER(sa.SA_ENROLMENT_NO)'), '=', DB::raw('LOWER(sub.USER_ID)'))
                                ->whereColumn('sa.SA_SESS_ID', 'sub.SESS_ID');
                        })
                        ->leftjoin('SCHOLARSHIP_TYPE as scholshp', 'scholshp.SCHOLARSHIP_ID', 'sa.SA_SCHOLARSHIP_CATEGORY')
                        ->whereNull('sub.USER_TYPE');//this column contain null in case of student
                        // ->where('CURRENT_STATUS', '!=', 'Declined');//excluding declined subscriptions
                        if(!empty($request->session)){
                            $data = $data->where('sp.SP_SESSION', $request->session);
                        }
                        if(!empty($request->category)){
                            $data = $data->where('sp.SP_CATEGORY', $request->category);
                        }
                        if(!empty($request->user_type)){
                            $data = $data->where('sp.SP_USER_TYPE', $request->user_type);
                        }
                        if(!empty($request->plan)){
                            $data = $data->where('sp.SP_PLAN_TENURE', $request->plan);
                        }
                        if(!empty($request->plan)){
                            $data = $data->where('sp.SP_PLAN_TENURE', $request->plan);
                        }
                        if(!empty($request->subscription_status)){
                            $data = $data->where('sub.CURRENT_STATUS', $request->subscription_status);
                        }
                        if(!empty($request->payment_status)){
                            if($request->payment_status == 'Paid'){
                                $data = $data->whereNotNull('sc.CALLBACK_PAID_ON')
                                                ->where('sc.response_status', 'Success')
                                                ->where('sc.SC_CHALLAN_STATUS', 'PAID');
                            }else if($request->payment_status == 'Unpaid'){
                                $data = $data->whereNull('sc.CALLBACK_PAID_ON')
                                                ->whereNull('sc.response_status')
                                                ->where('sc.SC_CHALLAN_STATUS', 'UNPAID');
                            }else{
                                $data = $data->where('sc.SC_CHALLAN_STATUS', 'CANCELLED');
                            }
                        }
                        $data =  $data->orderBy('sub.id', 'desc')
                                ->get();
                        // dd($data);
            return DataTables::of($data)->toJson();
        }
        public function updateSubscription(Request $request){
            // dd($request->all());
            $validator = Validator::make($request->all(),[
                'status' => 'required',
                'userId_enrollment' => 'required'

            ]);
            if($validator->fails()) {
                return Response::json(array(
                    'success' => false,
                    'errors' => $validator->getMessageBag()->toArray()
                ), 400);
            }
            $userId = auth()->user()->id;
            $username = auth()->user()->username;
            $subscription_id = $request->main_id;
            $newSubdata = [
                'CURRENT_STATUS' => $request->get('status'),
                // 'SUBSCRIPTION_DATE' =>  date('Y-m-d H:i:s'),
                // 'EXPIRATION_DATE' => $expiryDate,
                'UPDATED_BY_USERID' => $userId,
                'UPDATED_AT' =>  date('Y-m-d H:i:s')
            ];
            $subscriptionData= DB::table('SUBSCRIPTIONS')
                                ->select('*')
                                ->join('subscription_plans', 'subscription_plans.sp_id', 'SUBSCRIPTIONS.sp_id')
                                ->join('student_challan', 'student_challan.challan_no', 'SUBSCRIPTIONS.CHALLAN_ID')
                                ->where('SUBSCRIPTIONS.ID', $subscription_id)->first();
            $discountEntry = DB::table('SPORTS_APPLICANTS')
                                ->where('SA_ENROLMENT_NO', $subscriptionData->user_id)
                                ->where('SA_SESS_ID', $subscriptionData->sess_id)
                                ->first();
            // dd($subscriptionData, $discountEntry);
                // dd($request->userId_enrollment, 'gym', $request->status, $userData->subscription_date, $userData->expiration_date);
            // $response = (new ApiController)->modifyStudentInfo($request->userId_enrollment, 'gym', $request->status, $userData->subscription_date, $userData->expiration_date);
            // $api_data = [
            //     'enrollment' => $request->userId_enrollment,
            //     'groupName' => 'gym',
            //     'status' => $request->status,
            //     'start' => $userData->subscription_date,
            //     'end' => $userData->expiration_date,
            //     ];
            //  // Create a new request instance
            // $request = Request::create('/api/modify-student-info', 'POST', $api_data);

            // // Call the target controller method
            // $response = app(ApiController::class)->modifyStudentInfo($request);
            // // dd($response->getData()->results);
            // $response = $response->getData()->results;
            // // Check if the external API call was successful
            // if(!empty($response)){
            //     foreach($response as $value){
            //         if ($value->subStatusCode !== 'ok') {
            //             // Handle the error case
            //             $mesg = 'Student may not registered on machine '.(!empty($value->machine_ip) ? $value->machine_ip : '').' or the machine is offline';
            //             $success = false;
            //             return response()->json([
            //                 'success' => $success,
            //                 'msg' => $mesg,
            //             ]);

            //         }
            //     }
                $success = false;
                $mesg = 'Some problem occurred';
                if ($request->challan_status == 'UNPAID' && $request->status == 'Declined') {
                    // Start a DB transaction
                    DB::beginTransaction();
                    try {
                        // Update the SUBSCRIPTIONS table
                        $subsAffected = DB::table('SUBSCRIPTIONS')
                            ->where('ID', $subscription_id)
                            ->update($newSubdata);

                        // Update the STUDENT_CHALLAN table
                       $challanAffected = DB::table('STUDENT_CHALLAN')
                                            ->where('CHALLAN_NO', $subscriptionData->challan_id)
                                            ->update([
                                                'INVOICEAMOUNT' => DB::raw(
                                                    "TO_CHAR(
                                                        GREATEST(
                                                            NVL(TO_NUMBER(INVOICEAMOUNT), 0)
                                                            - NVL(SC_GYM_FEE, 0),
                                                            0
                                                        )
                                                    )"
                                                ),
                                                'SC_GYM_FEE' => 0,
                                                'SC_UPDATED_BY_USERNAME' => $username,
                                                'UPDATED_AT' => now()->format('Y-m-d H:i:s'),
                                            ]);


                        // Commit transaction if both updates succeeded (or if at least one succeeded, as per your previous logic)
                        if ($subsAffected > 0 || $challanAffected > 0) {
                            DB::commit();
                            $mesg = 'Updated Successfully';
                            $success = true;
                        } else {
                            // No rows affected, rollback for consistency
                            DB::rollBack();
                        }
                    } catch (\Exception $e) {
                        DB::rollBack();
                        // Log the error if needed: \Log::error($e);
                        $mesg = 'Error: ' . $e->getMessage();
                    }
                }else {
                    $mesg = 'Invalid request conditions.';
                }

                if ($request->challan_status == 'UNPAID' && $request->status == 'Pending' && $subscriptionData->current_status == 'Declined'){
                     // Start a DB transaction
                    DB::beginTransaction();
                    try {
                        // Update the SUBSCRIPTIONS table
                        $subsAffected = DB::table('SUBSCRIPTIONS')
                            ->where('ID', $subscription_id)
                            ->update($newSubdata);

                        // Update the STUDENT_CHALLAN table
                        $affectedRows = 0;
                        if(!empty($discountEntry)){
                            $gymFee = $subscriptionData->sp_sports_pricing;
                        }else{
                            $gymFee = $subscriptionData->sp_pricing;
                        }
                        $gymFee = max(0, $gymFee);
                        $data = [
                                'sc_gym_fee' => $gymFee,
                                'SC_AMOUNT' => $gymFee,
                                 'INVOICEAMOUNT' => DB::raw(
                                                            "TO_CHAR(
                                                                GREATEST(
                                                                    NVL(TO_NUMBER(INVOICEAMOUNT), 0) + {$gymFee},
                                                                    0
                                                                )
                                                            )"
                                                        ),
                                'UPDATED_AT' => now()
                            ];
                        // dd($data);
                        $challanAffected = DB::table('STUDENT_CHALLAN')
                            ->where('ID', $subscriptionData->challan_id)
                            ->update($data);


                        // Commit transaction if both updates succeeded (or if at least one succeeded, as per your previous logic)
                        if ($subsAffected > 0 || $challanAffected > 0) {
                            DB::commit();
                            $mesg = 'Updated Successfully';
                            $success = true;
                        } else {
                            // No rows affected, rollback for consistency
                            DB::rollBack();
                        }
                    } catch (\Exception $e) {
                        DB::rollBack();
                        // Log the error if needed: \Log::error($e);
                        $mesg = 'Error: ' . $e->getMessage();
                    }
                }else {
                    $mesg = 'Invalid request conditions.';
                }
                return response()->json([
                    'success' => $success,
                    'msg' => $mesg,
                ]);
            // }else{
            //     $mesg = 'ACL group not assigned or student is not active';
            //     $success = false;
            //     return response()->json([
            //         'success' => $success,
            //         'msg' => $mesg,
            //     ]);
            // }

        }
        public function updateSubscriptionChallan(Request $request){
            $validator = Validator::make($request->all(),[
                'mainId' => 'required|numeric'

            ]);
            if($validator->fails()) {
                return Response::json(array(
                    'success' => false,
                    'errors' => $validator->getMessageBag()->toArray()
                ), 400);
            }
            $userId = auth()->user()->id;
            $username = auth()->user()->username;
            $subscription_id = $request->mainId;
            $subscriptionData= DB::table('SUBSCRIPTIONS')
                                ->select('*')
                                ->join('subscription_plans', 'subscription_plans.sp_id', 'SUBSCRIPTIONS.sp_id')
                                ->join('student_challan', 'student_challan.challan_no', 'SUBSCRIPTIONS.CHALLAN_ID')
                                ->where('SUBSCRIPTIONS.ID', $subscription_id)->first();
            $discountEntry = DB::table('SPORTS_APPLICANTS')
                                ->where('SA_ENROLMENT_NO', $subscriptionData->user_id)
                                ->where('SA_SESS_ID', $subscriptionData->sess_id)
                                ->first();
            $affectedRows = 0;
            if(!empty($discountEntry)){
                $sp_pricing = (float) $subscriptionData->sp_pricing;
                $sp_sports_pricing = (float) $subscriptionData->sp_sports_pricing;

                $data = [
                    'SC_GYM_FEE' => $sp_sports_pricing,
                    'SC_AMOUNT' => $sp_sports_pricing,
                    'SC_UPDATED_BY_USERNAME' => $username,
                ];

                if ($subscriptionData->sc_gym_fee != $subscriptionData->sp_sports_pricing) {
                    $data['INVOICEAMOUNT'] = DB::raw(
                        "TO_CHAR(
                            GREATEST(
                                NVL(TO_NUMBER(INVOICEAMOUNT), 0)
                                - {$sp_pricing}
                                + {$sp_sports_pricing},
                                0
                            )
                        )"
                    );
                }

                $affectedRows = DB::table('STUDENT_CHALLAN')
                    ->where('ID', $subscriptionData->challan_id)
                    ->update($data);
            }




            if ($affectedRows > 0) {
                $mesg = 'Updated Successfully';
                $success = true;
            } else {
                $mesg = 'No changes made';
                $success = false;
            }

            return response()->json([
                'success' => $success,
                'msg' => $mesg,
            ]);
        }
        public function deleteSubscription(Request $request){
            $userId = auth()->user()->id;
            if($request->current_status != 'Declined'){
                return response()->json([
                    'success' => false,
                    'data' => 'Only declined subscriptions can be deleted'
                ]);
            }
            $affectedRows = DB::table('SUBSCRIPTIONS')
                            ->where('ID', $request->mainId)
                            ->delete();
            if ($affectedRows > 0) {
                return response()->json([
                    'success' => true,
                    'data' => 'Deleted Successfully'
            ]);
            } else {
                return response()->json([
                    'success' => false,
                    'data' => 'Some problem occured'
                ]);
            }

        }
        ////////////////////Staff Subscriptions////////////////////////
        function staffSubscriptions(){

            $sessions = DB::select("select * from ACADEMIC_SESSIONS where SESS_STATUS = 'E' ORDER BY SESS_ID DESC");
            // dd($sessions);
            return view('acl.allStaffSubscriptions',compact('sessions'));
        }
        public function getStaffSubscriptionList(Request $request){
            // dd($request->all());
            $status = $request->status;
            $monthYear = $request->month;
            $month_Year = explode('-', $monthYear);
            $data = DB::table('SUBSCRIPTIONS as sub')
                    ->select(
                        'sub.ID',
                        'sub.USER_ID',
                        'sub.STUDENT_FULL_NAME',
                        'sub.SUBSCRIPTION_DATE',
                        'sub.EXPIRATION_DATE',
                        'sub.SUBSCRIPTION_STATUS'
                        )
                        ->where('sub.USER_TYPE', 'staff');//this column contain staff in case of staff/faculty
                        if(!empty($month_Year[0] && !empty($month_Year[1]))){
                            $data = $data->whereMonth('sub.SUBSCRIPTION_DATE', $month_Year[0])
                                    ->whereYear('sub.SUBSCRIPTION_DATE', $month_Year[1]);
                        }
                        if(!empty($status)){
                            $data = $data->where('sub.SUBSCRIPTION_STATUS', $status);

                        }

                        $data = $data->orderBy('sub.SUBSCRIPTION_DATE', 'asc')
                                ->get();
            // dd($data);
            return DataTables::of($data)->toJson();
        }

}
