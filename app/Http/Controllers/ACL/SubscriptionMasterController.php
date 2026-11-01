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

class SubscriptionMasterController extends Controller{

        function subscriptionMasterForm(){
            $sessions = DB::select("select * from ACADEMIC_SESSIONS where SESS_STATUS = 'E' ORDER BY SESS_ID DESC");
            // dd($sessions);
            return view('acl.subscriptionMasterForm',compact('sessions'));
        }
        public function addSubscriptionPlan(Request $request){
            // dd($request->all());
            $validator = Validator::make($request->all(),[
                'session' => 'required',
                'category' => 'required',
                'user_type' => 'required',
                'plan' => 'required',
                'regular-pricing' => 'required',
                // 'sports-pricing' => 'required',
                'detail' => ['required', 'max:400'],
                'semester-start' => 'required',
                'semester-end' => 'required',
            ], [
                'session.required' => 'Please select session.',
                'category.required' => 'Please select a category.',
                'user_type.required' => 'Please selec User type.',
                'plan.required' => 'Please selec plan tenure.',
                'regular-pricing.required' => 'Fee for regular students is required.',
                // 'sports-pricing.required' => 'Fee for sports students is required.',
                'detail.required' => 'Details are required.',
                'detail.max' => 'Details cannot exceed 400 characters.',
                'semester-start.required' => 'Semester start date is required.',
                'semester-end.required' => 'Semester end date is required.',
            ]);
            if($validator->fails()) {
                return Response::json(array(
                    'success' => false,
                    'errors' => $validator->getMessageBag()->toArray()
                ), 400);
            }
            // dd(auth()->user()->employee_id);
            $userId = auth()->user()->id;
            $data = [
                'SP_SESSION' => $request->get('session'),
                'SP_CATEGORY' => $request->get('category'),
                'SP_USER_TYPE' =>$request->get('user_type'),
                'SP_PLAN_TENURE' =>$request->get('plan'),
                'SP_PRICING' => $request->get('regular-pricing'),
                'SP_SPORTS_PRICING' => $request->get('sports-pricing'),
                'SP_DETAIL' => $request->get('detail'),
                'SP_DISCOUNT' => $request->get('discount'),
                'SP_SEM_START' => $request->get('semester-start'),
                'SP_SEM_END' => $request->get('semester-end'),
                'ENTRY_USER_ID' => $userId,
                'CREATED_AT' =>  date('Y-m-d H:i:s'),
                'SP_STATUS' => 'E'
            ];
            // DB::beginTransaction();
            $mesg = '';
            $success = '';
            // try {
                if(empty($request->mainId)) {
                    $duplicateCheck = DB::table('SUBSCRIPTION_PLANS')
                                    ->select(
                                            'SP_ID',
                                            'SP_SESSION',
                                            'SP_CATEGORY',
                                            'SP_USER_TYPE',
                                            'SP_PLAN_TENURE',
                                            )
                                            ->where('SP_SESSION', $request->get('session'))
                                            ->where('SP_CATEGORY', $request->get('category'))
                                            ->where('SP_USER_TYPE', $request->get('user_type'))
                                            ->where('SP_PLAN_TENURE', $request->get('plan'))
                                            ->where('SP_STATUS', 'E')
                                            ->get();
                                            // dd($duplicateCheck);
                    if(sizeof($duplicateCheck) == 0 ){
                        // INSERTION CASE
                        DB::table('SUBSCRIPTION_PLANS')->insert($data);
                        $mesg = 'Saved Successfully';
                        $success = true;
                    }else{//UPDATE CASE
                        $affectedRows = DB::table('SUBSCRIPTION_PLANS')
                                    ->where('SP_ID', $duplicateCheck[0]->sp_id)
                                    ->update($data);

                        if ($affectedRows > 0) {
                            $mesg = 'Updated Successfully';
                            $success = true;
                        }
                    }
                }else{
                    $duplicateCheck = DB::table('SUBSCRIPTION_PLANS')
                                    ->select(
                                            'SP_ID',
                                            'SP_SESSION',
                                            'SP_CATEGORY',
                                            'SP_USER_TYPE',
                                            'SP_PLAN_TENURE',
                                            )
                                            ->where('SP_SESSION', $request->get('session'))
                                            ->where('SP_CATEGORY', $request->get('category'))
                                            ->where('SP_USER_TYPE', $request->get('user_type'))
                                            ->where('SP_PLAN_TENURE', $request->get('plan'))
                                            ->where('SP_STATUS', 'E')
                                            ->where('SP_ID', '!=', $request->mainId)
                                            ->get();
                    //  dd($duplicateCheck);
                    if(sizeof($duplicateCheck) == 0 ){
                        $affectedRows = DB::table('SUBSCRIPTION_PLANS')
                                    ->where('SP_ID', $request->mainId)
                                    ->update($data);

                        if ($affectedRows > 0) {

                            $mesg = 'Updated Successfully';
                            $success = true;
                        }
                    }else{
                        $mesg = 'Duplicate entries not allowed!';
                        $success = false;
                    }
                    // Commit the transaction
                    // DB::commit();
                }
                return response()->json([
                    'success' => $success,
                    'msg' => $mesg,
                ]);
        }
        public function getSubscriptionPlanList(Request $request){
            $data = DB::table('SUBSCRIPTION_PLANS as sp')
                ->select(
                        'sp.SP_ID',
                        'acs.SESS_NAME',
                        'sp.SP_CATEGORY',
                        'sp.SP_USER_TYPE',
                        'sp.SP_PLAN_TENURE',
                        'sp.SP_PRICING',
                        'sp.SP_SPORTS_PRICING',
                        'SP_DISCOUNT',
                        'SP_SEM_START',
                        'SP_SEM_END',
                        'sp.SP_DETAIL',
                        'sp.SP_SESSION'
                        )
                        ->leftjoin('ACADEMIC_SESSIONS as acs', 'acs.sess_id', 'sp.SP_SESSION')
                        ->where('SP_STATUS', 'E')
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

}
