<?php
namespace App\Http\Controllers\Grader\Eportal;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use DB;

class EportalMessageController extends Controller
{
    
    public function getUserMessages(Request $request){

        // $tmTitle = DB::table('eportal_request')
        //     ->join('eportal_categories', 'eportal_request.ec_id', '=', 'eportal_categories.id')
        //     ->where('eportal_request.id', $request->input('reqid'))
        //     ->value('eportal_categories.ec_title');
        $tmTitle = DB::table('eportal_request')
        ->select(
            'eportal_categories.ec_title',
            DB::raw('(SELECT parent_request.ec_id FROM eportal_request parent_request WHERE parent_request.id = eportal_request.parent_er_id) as parent_req_id')
        )
        ->join('eportal_categories', 'eportal_request.ec_id', '=', 'eportal_categories.id')
        ->where('eportal_request.id', $request->input('reqid'))
        ->first();
      
        // dd($tmTitle);
         //dd($request->all());
         $sql = "SELECT
        messages_bnu.id,
        (select  full_name from sisusers where  username= messages_bnu.entry_user_id and rownum=1) as message_to,
        messages_bnu.message,
        messages_bnu.parent_message_id,
        messages_bnu.entry_user_id,
        TO_CHAR(messages_bnu.created_at, 'DD-MM-YY')  || ' at ' || TO_CHAR(messages_bnu.created_at, 'HH:MI AM')  AS time_created
       
        FROM
        messages_bnu
        LEFT JOIN eportal_request ON eportal_request.id = messages_bnu.parent_message_id
        WHERE 1=1
        and  messages_bnu.type = 'eportal'
        and messages_bnu.parent_message_id='".$request->input('reqid')."'
         order by messages_bnu.id desc
        ";
        //echo $sql;exit;
        $sqlSelect=DB::select($sql);
        DB::table('messages_bnu')
            ->where('parent_message_id', $request->input('reqid'))
            ->update([
                'seen_record' => DB::raw("CONCAT(seen_record,',". auth()->user()->employee_id."')")
            ]);
        return response()->json(['status' => true, 'data' =>  $sqlSelect, 'tmTitle' =>  $tmTitle->ec_title, 'parent_req_id' =>  $tmTitle->parent_req_id]);

    }
    function SendMsg(Request $request){
        //  dd(auth()->user());
      // dd($request->all());
          // Insert the form data into the admission_form table
          // $sql = "select * from emp_employees where employee_id = '".$request->input('to_user_id')."'";
          // $sql = "SELECT
          //             *
          //         FROM
          //             emp_employees
          //             left join task_management on employee_id = tm_entry_user or employee_id = task_management.TASK_CLIENT_ID
          //         WHERE
          //             employee_id = '".$request->input('to_user_id')."'
          //             and task_management.id = '".$request->input('taskIdInput')."'";  
                     
          // $res = DB::select($sql);       
          // $toUserEmail = $res[0]->emp_email;
          // $empName     = $res[0]->prefix.' '.$res[0]->emp_f_name.' '.$res[0]->emp_l_name;
          // $tmTitle     = $res[0]->tm_title;
          $sendMessageToStudent = $request->has('sendMessageToStudent') ? '' : '1';
          $entryData = [
              'message' => $request->input('messageTextarea'),
              'parent_message_id' => $request->input('RequestID'),
              'PRIVATE_COMMENT' => $sendMessageToStudent,
              'to_user_id' => '',
              'entry_user_id' => auth()->user()->username,
              'type' => "eportal",
              'created_at' => now() // Add current timestamp
          ];
          DB::table('messages_bnu')->insert($entryData);
          //$to = $toUserEmail;
        //  $subject = 'You recieved message from '.auth()->user()->full_name.' ('.$tmTitle.')';
          //$message =  $request->input('messageTextarea').'<br> Sent By: '. auth()->user()->full_name;
       //   $this->send_mail($to,$subject,$message);
          return response()->json(['status'=>true,
              'data' => 'Message send successfully'
          ]);
        }
  

}
