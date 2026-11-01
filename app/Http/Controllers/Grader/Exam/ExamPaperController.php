<?php

namespace App\Http\Controllers\Grader\Exam;

use App\Http\Controllers\Controller;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ExamPaperController extends Controller{

  function header($co_id){ 

    
     $fc_co_id=my_simple_crypt($co_id, 'd' );
     

  $course_name = DB::selectOne("select get_course_name($fc_co_id) as value from dual");
         $course_name=$course_name->value;

        
    return view('grader.exam.header',['fc_co_id'=>$co_id,'course_name'=> $course_name]);    
  }
  public function masterFormExamAllow(Request $request)
  {
    //   $entryUserId = auth()->user()->id;
    //   $groupId = auth()->user()->group_id;

    $examPapers = DB::table('EXAM_PAPERS')
            ->select('ID', 'TITLE')
            ->orderBy('ID', 'desc')
            ->limit(20)
            ->get();
    // dd($examPapers);
      return view('grader.exam.masterFormAllowExamIPs', ['examPapers' => $examPapers]);
  }
  public function updateAllowExamIps(Request $request)
  {
    // dd($request->all());
    $validator = Validator::make($request->all(), [
        'examID' => 'required',
        'vlans' => ['nullable', 'regex:/^[0-9\s|.]+$/'],
        'ips' => ['nullable', 'regex:/^[0-9\s|.]+$/'],
    ], [
        'examID.required' => 'Exam title is required',
        'vlans.regex' => 'VLAN field may only contain numbers, spaces, dots, and the pipe symbol. (example 172.20.250.36|172.20.250.43)',
        'ips.regex' => 'IPs field may only contain numbers, spaces, dots, and the pipe symbol. (example 172.20.250.36|172.20.250.43)',
    ]);

    // Check if validation fails
    if ($validator->fails()) {
        $errors = $validator->errors()->toArray();
        return response()->json(['success' => false, 'errors' => $errors], 422);
    }
    $allowedVlans_encoded='';
    $allowedIPs_encoded='';
    if(!empty($request->input('vlans'))){
        $allowedVlans = explode('|', $request->input('vlans'));
        $allowedVlans_encoded = json_encode($allowedVlans);
    }
    if(!empty($request->input('ips'))){
        $allowedIps = explode('|', $request->input('ips'));
        $allowedIPs_encoded = json_encode($allowedIps);
    }
    $updateData = [
        'ALLOWED_VLAN' => $allowedVlans_encoded,
        'ALLOWED_IP' => $allowedIPs_encoded,
        'ALLOWED_PREMISES' => $request->premises,
        'ALLOWED_BY_USERID' => auth()->user()->id,
    ];
    // dd($updateData);
    $updated = DB::table('EXAM_PAPERS')
                ->where('id', $request->examID)
                ->update($updateData);
    // dd($updated);
    if (!$updated) {
        return response()->json(['message' => 'Failed to update Exam'], 500);
    } else {
        return response()->json(['success' => true, 'message' => 'Exam updated successfully']);
    }
  }
}