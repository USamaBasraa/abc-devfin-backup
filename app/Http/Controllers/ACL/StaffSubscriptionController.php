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
use Response;
use Validator;
use DataTables;
use DateTime;
use Carbon\Carbon;

class StaffSubscriptionController extends Controller{

    function staffSubscriptionForm(){
        $userId = auth()->user()->username;
        $currentMonthStatus_autoYes =  DB::select("
                                        SELECT *
                                        FROM SUBSCRIPTIONS
                                        WHERE USER_ID = ?
                                        AND SUBSCRIPTION_STATUS = 'active'
                                        AND AUTO_SUBSCRIPTION = 'yes'
                                        AND SUBSCRIPTION_DATE BETWEEN TO_DATE(?, 'YYYY-MM-DD HH24:MI:SS') AND TO_DATE(?, 'YYYY-MM-DD HH24:MI:SS')
                                    ", [
                                        $userId,
                                        date('Y-m-01 00:00:00'), // First day of the current month
                                        date('Y-m-t 23:59:59')   // Last day of the current month
                                    ]);
        if($this->isDateBetween25AndEndOfMonth()){
            $user_sub_status =  DB::select("
                                            SELECT *
                                            FROM SUBSCRIPTIONS
                                            WHERE USER_ID = ?
                                            AND SUBSCRIPTION_STATUS = 'active'
                                            AND SUBSCRIPTION_DATE BETWEEN TO_DATE(?, 'YYYY-MM-DD HH24:MI:SS') AND TO_DATE(?, 'YYYY-MM-DD HH24:MI:SS')
                                        ", [
                                            $userId,
                                            date('Y-m-01 00:00:00', strtotime('first day of next month')), // First day of next month
                                            date('Y-m-t H:i:s', strtotime('last day of next month'))  // Last day of next month
                                        ]);
        }else{
            $user_sub_status =  DB::select("
                                            SELECT *
                                            FROM SUBSCRIPTIONS
                                            WHERE USER_ID = ?
                                            AND SUBSCRIPTION_STATUS = 'active'
                                            AND SUBSCRIPTION_DATE BETWEEN TO_DATE(?, 'YYYY-MM-DD HH24:MI:SS') AND TO_DATE(?, 'YYYY-MM-DD HH24:MI:SS')
                                        ", [
                                            $userId,
                                            date('Y-m-01 00:00:00'), // First day of the current month
                                            date('Y-m-t 23:59:59')   // Last day of the current month
                                        ]);
        }


        // dd($currentMonthStatus);
        $is_subscription_expired= '';
        $expiration_date_obj= '';
        if(!empty($user_sub_status[0])){
             // Convert expiration_date to a DateTime object
            $current_date = new DateTime(); // Get the current date and time
            // $current_date = new DateTime('2024-11-01'); // Get the current date and time
            $expiration_date_obj = new DateTime($user_sub_status[0]->expiration_date); // Convert expiration_date to DateTime
            // Check if current date is greater than expiration date
            $is_subscription_expired = $current_date > $expiration_date_obj ? true : false;
        }
        // dd($is_subscription_expired);
        return view('acl.staffSubscriptionForm', [
            'user_sub_status' => $user_sub_status,
            'currentMonthStatus_autoYes' => $currentMonthStatus_autoYes,
            'is_subscription_expired' => $is_subscription_expired,
            'isLastFiveDays' => $this->isDateBetween25AndEndOfMonth(), //if date is between 25 and 31 -> true
            'expiration_date_obj' => $expiration_date_obj
        ]);
    }
    public function addStaffSubscription(Request $request){
        // dd($userId = auth()->user());
        // dd($request->all());
        $currentDateTime = Carbon::now('Asia/Karachi')->format('Y-m-d H:i:s');
        $validator = Validator::make($request->all(),[
            'subscription-status' => 'required'

        ]);
        if($validator->fails()) {
            return Response::json(array(
                'success' => false,
                'errors' => $validator->getMessageBag()->toArray()
            ), 400);
        }
        if(!empty($request->get('auto-subscription'))){
            $auto_subscription = 'yes';
        }else{
            $auto_subscription = 'no';
        }
        $sisuserId = auth()->user()->id;
        $username = auth()->user()->username;
        $full_name = auth()->user()->full_name;
        $newStatus = $request->get('subscription-status');
        // dd($newStatus);
        $spData = DB::select("select SP_SESSION, SP_ID from SUBSCRIPTION_PLANS where SP_PLAN_TENURE ='Monthly' and SP_CATEGORY='Gym'");
        // DB::beginTransaction();
        $mesg = '';
        $success = '';
        // $duplicateCheck = DB::table('SUBSCRIPTIONS')
        //                 ->select( 'id' )
        //                 ->where('USER_ID',  $username)
        //                 ->get();
        $currentMonthSubs = DB::table('SUBSCRIPTIONS')
                            ->select('id')
                            ->where('USER_ID', $username)
                            ->whereBetween('SUBSCRIPTION_DATE', [
                                date('Y-m-01 00:00:00'), // First day of the current month at the start of the day
                                date('Y-m-t 23:59:59')   // Last day of the current month at the end of the day
                            ])
                            ->get();
        $nextMonthSubs = DB::table('SUBSCRIPTIONS')
                            ->select('id')
                            ->where('USER_ID', $username)
                            ->whereBetween('SUBSCRIPTION_DATE', [
                                date('Y-m-01 00:00:00', strtotime('first day of next month')), // First day of next month
                                date('Y-m-t H:i:s', strtotime('last day of next month'))  // Last day of next month
                            ])
                            ->get();
        $islastFiveDays = $this->isDateBetween25AndEndOfMonth();
        if($islastFiveDays){
            if($newStatus == 'active'){//last five days and active
                if(sizeof($nextMonthSubs) == 0 ){//last five days and active and no subscriptions for next month
                    // dd('last five days and active and no subscriptions for next month');
                    // INSERTION CASE for next month
                    $data = [
                        'USER_ID' => $username,
                        'STUDENT_FULL_NAME' => $full_name,
                        'SP_ID' => $spData[0]->sp_id,
                        'SESS_ID' => $spData[0]->sp_session,
                        'USER_TYPE' => 'staff',
                        'CURRENT_STATUS' => $request->get('subscription-status'),//current status is same as subscription-status i.e. active or inactive
                        'SUBSCRIPTION_STATUS' => $request->get('subscription-status'),
                        // 'SUBSCRIPTION_DATE' =>  date('Y-m-01 H:i:s'), // First day of the current month
                        // 'EXPIRATION_DATE' =>  date('Y-m-t H:i:s'),//last Day Of current Month
                        'SUBSCRIPTION_DATE' =>  date('Y-m-01 H:i:s', strtotime('first day of next month')), // First day of the next month
                        'EXPIRATION_DATE' => date('Y-m-t H:i:s', strtotime('last day of next month')), // Last day of the next month
                        'AUTO_SUBSCRIPTION' => $auto_subscription
                    ];
                    $lastID = DB::table('SUBSCRIPTIONS')->insertGetId($data);
                    if($lastID){
                        $mesg = 'Updated Successfully';
                        $success = true;
                        $debugMsg = 'last five days and active and no subscriptions for next month';
                    }
                }else{//last five days and active and subscription present for next month
                    // dd('last five days and active and subscription present for next month');
                // UPDATE CASE for next month
                    $data = [
                        'USER_ID' => $username,
                        'STUDENT_FULL_NAME' => $full_name,
                        'SP_ID' => $spData[0]->sp_id,
                        'SESS_ID' => $spData[0]->sp_session,
                        'USER_TYPE' => 'staff',
                        'CURRENT_STATUS' => $request->get('subscription-status'),//current status is same as subscription-status i.e. active or inactive
                        'SUBSCRIPTION_STATUS' => $request->get('subscription-status'),
                        // 'SUBSCRIPTION_DATE' =>  date('Y-m-01 H:i:s'), // First day of the current month
                        // 'EXPIRATION_DATE' =>  date('Y-m-t H:i:s'),//last Day Of current Month
                        'SUBSCRIPTION_DATE' =>  date('Y-m-01 H:i:s', strtotime('first day of next month')), // First day of the next month
                        'EXPIRATION_DATE' => date('Y-m-t H:i:s', strtotime('last day of next month')), // Last day of the next month
                        'AUTO_SUBSCRIPTION' => $auto_subscription
                    ];
                    $affectedRows = DB::table('SUBSCRIPTIONS')
                                    ->where('id', $nextMonthSubs[0]->id)
                                    ->update($data);

                        if ($affectedRows > 0) {
                            $mesg = 'Updated Successfully';
                            $success = true;
                            $debugMsg = 'last five days and active and subscription present for next month';
                        }
                }
            }else{//last five days and inactive
                if(sizeof($nextMonthSubs) == 0 ){//last five days and inactive and no subscriptions for next month
                    // dd('last five days and inactive and no subscriptions for next month');
                    // INSERTION CASE for next month
                    $data = [
                        'USER_ID' => $username,
                        'STUDENT_FULL_NAME' => $full_name,
                        'SP_ID' => $spData[0]->sp_id,
                        'SESS_ID' => $spData[0]->sp_session,
                        'USER_TYPE' => 'staff',
                        'CURRENT_STATUS' => $request->get('subscription-status'),//current status is same as subscription-status i.e. active or inactive
                        'SUBSCRIPTION_STATUS' => $request->get('subscription-status'),
                        'SUBSCRIPTION_DATE' =>  date('Y-m-01 H:i:s', strtotime('first day of next month')), // First day of the next month
                        'EXPIRATION_DATE' => date('Y-m-t H:i:s', strtotime('last day of next month')), // Last day of the next month
                        'AUTO_SUBSCRIPTION' => $auto_subscription
                    ];
                    $lastID = DB::table('SUBSCRIPTIONS')->insertGetId($data);
                    if($lastID){
                        $mesg = 'Updated Successfully';
                        $success = true;
                        $debugMsg = 'last five days and inactive and no subscriptions for next month';
                    }
                }else{//last five days and inactive and subscriptions preseent for next month
                    // dd('last five days and inactive and subscriptions preseent for next month');
                // UPDATE CASE for next month
                    $data = [
                        'USER_ID' => $username,
                        'STUDENT_FULL_NAME' => $full_name,
                        'SP_ID' => $spData[0]->sp_id,
                        'SESS_ID' => $spData[0]->sp_session,
                        'USER_TYPE' => 'staff',
                        'CURRENT_STATUS' => $request->get('subscription-status'),//current status is same as subscription-status i.e. active or inactive
                        'SUBSCRIPTION_STATUS' => $request->get('subscription-status'),
                        'SUBSCRIPTION_DATE' =>  date('Y-m-01 H:i:s', strtotime('first day of next month')), // First day of the next month
                        'EXPIRATION_DATE' => date('Y-m-t H:i:s', strtotime('last day of next month')), // Last day of the next month
                        'AUTO_SUBSCRIPTION' => $auto_subscription
                    ];
                    $affectedRows = DB::table('SUBSCRIPTIONS')
                                    ->where('id', $nextMonthSubs[0]->id)
                                    ->update($data);

                        if ($affectedRows > 0) {
                            $mesg = 'Updated Successfully';
                            $success = true;
                            $debugMsg = 'last five days and inactive and subscriptions preseent for next month';
                        }
                }

            }
        }else{//current date other than 25 to 31 range
            if(sizeof($currentMonthSubs) == 0 ){//date other than 25 to 31 range no subscriptions for current month
                // dd('date other than 25 to 31 range no subscriptions for current month');
                // INSERTION CASE for current month
                $data = [
                    'USER_ID' => $username,
                    'STUDENT_FULL_NAME' => $full_name,
                    'SP_ID' => $spData[0]->sp_id,
                    'SESS_ID' => $spData[0]->sp_session,
                    'USER_TYPE' => 'staff',
                    'CURRENT_STATUS' => $request->get('subscription-status'),//current status is same as subscription-status i.e. active or inactive
                    'SUBSCRIPTION_STATUS' => $request->get('subscription-status'),
                    'SUBSCRIPTION_DATE' =>  date('Y-m-01 H:i:s'), // First day of the current month
                    'EXPIRATION_DATE' =>  date('Y-m-t H:i:s'),//last Day Of current Month
                    'AUTO_SUBSCRIPTION' => $auto_subscription
                ];
                $lastID = DB::table('SUBSCRIPTIONS')->insertGetId($data);
                if($lastID){
                    $mesg = 'Updated Successfully';
                    $success = true;
                    $debugMsg = 'date other than 25 to 31 range no subscriptions for current month';
                }
            }else{//date other than 25 to 31 range subscriptions present for current month
                // dd('date other than 25 to 31 range subscriptions present for current month');
            // UPDATE CASE for current month
                $data = [
                    'USER_ID' => $username,
                    'STUDENT_FULL_NAME' => $full_name,
                    'SP_ID' => $spData[0]->sp_id,
                    'SESS_ID' => $spData[0]->sp_session,
                    'USER_TYPE' => 'staff',
                    'CURRENT_STATUS' => $request->get('subscription-status'),//current status is same as subscription-status i.e. active or inactive
                    'SUBSCRIPTION_STATUS' => $request->get('subscription-status'),
                    'SUBSCRIPTION_DATE' =>  date('Y-m-01 H:i:s'), // First day of the current month
                    'EXPIRATION_DATE' =>  date('Y-m-t H:i:s'),//last Day Of current Month
                    'AUTO_SUBSCRIPTION' => $auto_subscription
                ];
                $affectedRows = DB::table('SUBSCRIPTIONS')
                                ->where('id', $currentMonthSubs[0]->id)
                                ->update($data);

                    if ($affectedRows > 0) {
                        $mesg = 'Updated Successfully';
                        $success = true;
                        $debugMsg = 'date other than 25 to 31 range subscriptions present for current month';
                    }
            }
        }
        // dd('case miss');
        ////////////////////////////////////////////////////////
        ////////////////////////////////////////////////////////
        // dd($currentMonthSubs);
        // if(sizeof($currentMonthSubs) == 0 ){//no subscriptions for current month
        //     // INSERTION CASE for current month
        //     $data = [
        //         'USER_ID' => $username,
        //         'STUDENT_FULL_NAME' => $full_name,
        //         'SP_ID' => $spData[0]->sp_id,
        //         'SESS_ID' => $spData[0]->sp_session,
        //         'USER_TYPE' => 'staff',
        //         'CURRENT_STATUS' => $request->get('subscription-status'),//current status is same as subscription-status i.e. active or inactive
        //         'SUBSCRIPTION_STATUS' => $request->get('subscription-status'),
        //         'SUBSCRIPTION_DATE' =>  date('Y-m-01 H:i:s'), // First day of the current month
        //         'EXPIRATION_DATE' =>  date('Y-m-t H:i:s'),//last Day Of current Month
        //         'AUTO_SUBSCRIPTION' => $auto_subscription
        //     ];
        //     $lastID = DB::table('SUBSCRIPTIONS')->insertGetId($data);
        //     if($lastID){
        //         // $req_data = [
        //         //     "main_id" => $lastID,
        //         //     "status" => $status,
        //         //     "userId_enrollment" => $sisuserId
        //         // ];
        //         // $requestData = new Request($req_data);
        //         // $response = (new ApiController)->modifyStudentInfo($requestData);
        //         // Check if the external API call was successful
        //         // if ($response['subStatusCode'] === 'ok') {
        //             // You can log the success message or perform further actions here
        //             $mesg = 'Updated Successfully';
        //             $success = true;
        //         // } else {
        //             // Handle the error case
        //             // $mesg = 'You may not registered on machine';
        //             // $success = false;
        //         // }

        //     }

        // }else{// INSERTION CASE for next month
        //     $data = [
        //         'USER_ID' => $username,
        //         'STUDENT_FULL_NAME' => $full_name,
        //         'SP_ID' => $spData[0]->sp_id,
        //         'SESS_ID' => $spData[0]->sp_session,
        //         'USER_TYPE' => 'staff',
        //         'CURRENT_STATUS' => $request->get('subscription-status'),//current status is same as subscription-status i.e. active or inactive
        //         'SUBSCRIPTION_STATUS' => $request->get('subscription-status'),
        //         'SUBSCRIPTION_DATE' =>  date('Y-m-01 H:i:s', strtotime('first day of next month')), // First day of the next month
        //         'EXPIRATION_DATE' => date('Y-m-t H:i:s', strtotime('last day of next month')), // Last day of the next month
        //         'AUTO_SUBSCRIPTION' => $auto_subscription
        //     ];
        //     $lastID = DB::table('SUBSCRIPTIONS')->insertGetId($data);
        //     if($lastID){
        //         // $req_data = [
        //         //     "main_id" => $lastID,
        //         //     "status" => $status,
        //         //     "userId_enrollment" => $sisuserId
        //         // ];
        //         // $requestData = new Request($req_data);
        //         // $response = (new ApiController)->modifyStudentInfo($requestData);
        //         // Check if the external API call was successful
        //         // if ($response['subStatusCode'] === 'ok') {
        //             // You can log the success message or perform further actions here
        //             $mesg = 'Updated Successfully';
        //             $success = true;
        //         // } else {
        //             // Handle the error case
        //             // $mesg = 'You may not registered on machine';
        //             // $success = false;
        //         // }
        //     }
        // }
        // // else{//UPDATE CASE
        // //     $affectedRows = DB::table('SUBSCRIPTIONS')
        // //                 ->where('id', $currentMonthSubs[0]->id)
        // //                 ->update($data);

        // //     if ($affectedRows > 0) {
        // //         // $req_data = [
        // //         //     "main_id" => $currentMonthSubs[0]->id,
        // //         //     "status" =>  $status,
        // //         //     "userId_enrollment" => $sisuserId
        // //         // ];
        // //         // $requestData = new Request($req_data);
        // //         // $response = (new ApiController)->modifyStudentInfo($requestData);
        // //         // Check if the external API call was successful
        // //         // if ($response['subStatusCode'] === 'ok') {
        // //             // You can log the success message or perform further actions here
        // //             $mesg = 'Updated Successfully';
        // //             $success = true;
        // //         // } else {
        // //             // Handle the error case
        // //         //     $mesg = 'You may not registered on machine';
        // //         //     $success = false;
        // //         // }
        // //     }
        // // }



        return response()->json([
            'success' => $success,
            'msg' => $mesg,
            'debugMsg'=>$debugMsg,
            'isLastFiveDays' => $this->isDateBetween25AndEndOfMonth(), //if date is between 25 and 31 -> true
            'newStatus' => $request->get('subscription-status')
        ]);
    }
    public function getStaffSubscriptionList(Request $request){
        $username = auth()->user()->username;
        $data = DB::table('SUBSCRIPTIONS')
            ->select(
                    'ID',
                    'STUDENT_FULL_NAME',
                    'USER_ID',
                    'USER_TYPE',
                    'SUBSCRIPTION_STATUS',
                    'SUBSCRIPTION_DATE',
                    'EXPIRATION_DATE',
                    'AUTO_SUBSCRIPTION',
                    'CURRENT_STATUS'
                    )
                    // ->leftjoin('ACADEMIC_SESSIONS as acs', 'acs.sess_id', 'sp.SP_SESSION')
                    ->where('USER_ID', $username)
                    ->orderBy('CREATED_AT', 'desc')
                    ->get();
                // dd($data);
        return DataTables::of($data)->toJson();
    }
    public function deleteSubscriptionPlan(Request $request){
        $userId = auth()->user()->id;
        $affectedRows =  DB::table('SUBSCRIPTION_PLANS')
                            ->where('SP_ID', $request->mainId)
                            ->update([
                                'SP_STATUS' => 'D',
                                'ENTRY_USER_ID' => $userId,
                            ]);
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
    public function isDateBetween25AndEndOfMonth() {
        $currentDate = strtotime(date('Y-m-d'));
        // $currentDate = strtotime('2024-12-05');
        $startOfRange = strtotime(date('Y-m-25')); // 25th of the current month
        // $startOfRange = strtotime('2024-12-25');
        $endOfRange = strtotime(date('Y-m-t')); // Last day of the current month
        // $endOfRange =  strtotime('2024-12-31');
        if($currentDate >= $startOfRange && $currentDate <= $endOfRange){
            return true;
        }else{
            return false;
        }
    }

}
