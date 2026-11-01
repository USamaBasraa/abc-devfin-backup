<?php
namespace App\Http\Controllers\Grader\Chat;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\sessions;
use App\Models\User;
use Illuminate\Support\Facades\View;
use App\Models\Grader\Chat as ChatModel;
use App\Http\Requests\ChatRequest;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Messaging\CloudMessage;
use App\Models\Grader\AcademicSessions;
use DB;


class ChatController extends Controller{
    

function courseStreamView(Request $request,$coID=''){

      $data['course_name'] = DB::selectOne("select get_course_name($coID) as course from dual")->course;
      $data['coID']       = $coID;
      $data['unID']       =$request->get('topic');
      

        return view('grader.chat.course_chat',$data);
    }

    function TaStreamView(Request $request){      
      $co_idd       = $request->get('co_id');       
      $coID        = my_simple_crypt($co_idd, 'd');
      $data['course_name'] = DB::selectOne("select get_course_name($coID) as course from dual")->course;
      $data['coID']       = $coID; 
      return view('grader.chat.faculty_notification',$data);
  }
 function StreamView(Request $request,$coID=''){
      //$coID=$request->input('co_id');
      //$coID        = my_simple_crypt($coID, 'd');
      $data['course_name'] = DB::selectOne("select get_course_name($coID) as course from dual")->course;
      $data['coID']       = $coID;
      

        return view('grader.chat.faculty_notification',$data);
    }

 function courseStreamView1(Request $request,$coID=''){

      $data['course_name'] = DB::selectOne("select get_course_name($coID) as course from dual")->course;
      $data['coID']       = $coID;
      $data['unID']       =$request->get('topic');
      

        return view('grader.chat.course_chat',$data);
    }

    function courseStreamView2(Request $request,$coID=''){
	
      $data['course_name'] = DB::selectOne("select get_course_name($coID) as course from dual")->course;
      $data['coID']       = $coID;
        return view('grader.chat.course_chat2',$data);
    }


   // 19045
/*      old functions
    public function index(){
        $sessions = $this->session->fetch();
        return view('grader.common.sessions', compact('sessions'));
    }

    function courseStreamView(){
        return view('grader.chat.course_chat');
    }


    function getMessage(Request $request){
        $co_id          = HealthRecord::my_simple_decrypt($request->get('co_id'));
        $sisID          = auth()->user()->id;
        $rawWhereString = "(co_id = $co_id AND private_comment IS null) OR (to_user_id = '$sisID' AND private_comment = 1)";
        $data           = ChatModel::whereRaw($rawWhereString)->orderBy('id', 'asc')->get()->toArray();
        $nullArray      = array_keys(array_column($data, 'parent_message_id'),'0');
    
          $mainArray = array();
          foreach($nullArray as $row):
              $childKeys = array_keys(array_column($data, 'parent_message_id'), $data[$row]['id']);
              $data[$row]['reply'] = array();
                if(count($childKeys) > 0){
                  foreach($childKeys as $childRow):
                      $data[$childRow]['time_ago']  = ($data[$childRow]['created_at'])?\Carbon\Carbon::createFromTimeStamp(strtotime($data[$childRow]['created_at']))->diffForHumans():'';
                      $data[$row]['reply'][]        = (array) $data[$childRow];
                  endforeach;
              }
              $node                   = (array) $data[$row];
              $node['time_ago']       = ($node['created_at'])?\Carbon\Carbon::createFromTimeStamp(strtotime($node['created_at']))->diffForHumans():'';
              $mainArray[]            = $node;
          endforeach;
      return response()->json(['data'=> $mainArray, 'status' => true, 'message' => 'Messages!']);
    }
    */






////////////////// for react purpose Bilal saleem code ends here//////////////////

function list_message(Request $request){
$co_id = $request->get('id');

//dd($co_id);
//$co_id =HealthRecord::my_simple_decrypt($co_id);
//  echo \Carbon\Carbon::now(); exit;

if($co_id == ''){
    exit;
}
return view('chat.list',array('co_id' => $co_id, 'viewFor' => 'all'));
}

function fcm(Request $request){
	
	//return $request->all();
	
if($request->input('co_id')!= ""){

  $deviceTokenSql_no = "select * from sisusers_device_tokens
where lower(username) in (
select lower(sc_sai_enrollment_no) from student_courses 
where student_courses.sc_cms_course_co_id =  ".$request->input('co_id')."
)";

$deviceTokenSql = "select * from sisusers_device_tokens";
  $res = DB::select($deviceTokenSql);
if(count($res)> 0){
  foreach($res as $row){

    $token = $row->device_token;
    

 	//echo  $token;

  $title = $request->input('title')!=""?$request->input('title') : 'CMS';
  $body  = $request->input('message')!=""?$request->input('message') : 'Message';
  $imageUrl = 'http://lorempixel.com/400/200/';
  $message = CloudMessage::new(); 
  $messaging = app('firebase.messaging');
  // 

  $notification = Notification::fromArray([
      'title' => $title,
      'body' => $body,
      'image' => $imageUrl,
  ]);

$message = CloudMessage::withTarget('token', $token)
  ->withNotification($notification) // optional
  ->withData(['name' => 'Ahmed Shabbir', 'age' => '30']) // optional
;

try {
   $messaging->send($message);
}
catch (\Throwable $t) {
    echo "caught!\n";
    echo $t->getMessage(). "\n";
	// echo $t->getMessage(), " at ", $t->getFile(), ":", $t->getLine(), "\n";
}

		//try {
//		 //  $messaging->send($message);
//		   echo '*****____'.$token.'<br>';
//		} catch (Throwable $e) {
//			report($e);
//			return false;
//		}
  	}
  }
  else{
  	echo '0 device';
  }
}
}

function direct_message(Request $request){
$co_id = $request->get('id');
if($co_id == ''){
  abort(404,'Course not found!');
}
return view('chat.list',array('co_id' => $co_id, 'viewFor' => 'direct'));
}


      function sendMessage(ChatRequest $request){

          //  $request->validate()
          //  if ($validator->fails()) {
          //     return response()->json(array('status' => false,'data'=> [], 'message' => $validator->errors()->first()));
          //  }


          $insertArr['message']           = strip_tags($request->get('message'));
          $insertArr['co_id']             = my_simple_crypt($request->get('co_id'),'d');
          $insertArr['entry_user_id']     = auth()->user()->id;
          $insertArr['sender_username']   = auth()->user()->username;
          $insertArr['sender_name']       = auth()->user()->username;

          $insertArr['created_at']       = date('Y-m-d H:i:s');


          $viewFor                    = $request->get('viewFor');
          if($viewFor == 'direct'){
              $insertArr['private_comment']   = 1;
              $insertArr['to_user_id']        = $request->get('to_user_id');
          }
          $obj            = ChatModel::create($insertArr);
          $obj->time_ago  = \Carbon\Carbon::createFromTimeStamp(strtotime($obj->created_at))->diffForHumans();
          return response()->json(['data'=> $obj, 'status' => true, 'message' => 'Message Send!']);
      }

/*
function getMessage(Request $request){
$co_id = HealthRecord::my_simple_decrypt($request->get('co_id'));
$chats = ChatModel::where('co_id',$co_id)->orderBy('id', 'asc')->get();
return response()->json(['data'=> $chats, 'status' => true, 'message' => 'Messages!']);
}
*/



      function getMessage(Request $request){
          $co_id          = my_simple_crypt($request->get('co_id'),'d');
          $sisID          = auth()->user()->id;

          $studentSisID = $request->get('to_user_id');

        if($studentSisID != ''){
        //  echo $studentSisID; exit;
           $rawWhereString = "co_id = $co_id AND to_user_id = '$sisID' AND entry_user_id = '$studentSisID' AND private_comment = 1";
        //   echo $rawWhereString; exit;
        }else{
          $rawWhereString = "(co_id = $co_id AND private_comment IS null) OR (to_user_id = '$sisID' AND private_comment = 1)";
        }
          $data           = ChatModel::whereRaw($rawWhereString)->orderBy('id', 'asc')->get()->toArray();

      //   print_r($data); exit;


      $nullArray      = array_keys(array_column($data, 'co_id'),$co_id);
      $mainArray = array();
        foreach($nullArray as $row):
            $childKeys = array_keys(array_column($data, 'parent_message_id'), $data[$row]['id']);
            $data[$row]['reply'] = array();
              if(count($childKeys) > 0){
                foreach($childKeys as $childRow):
                    $data[$childRow]['time_ago']  = ($data[$childRow]['created_at'])?\Carbon\Carbon::createFromTimeStamp(strtotime($data[$childRow]['created_at']))->diffForHumans():'';
                    $data[$row]['reply'][]        = (array) $data[$childRow];
                endforeach;
            }
            $node                   = (array) $data[$row];
            $node['time_ago']       = ($node['created_at'])?\Carbon\Carbon::createFromTimeStamp(strtotime($node['created_at']))->diffForHumans():'';
            $mainArray[]            = $node;
        endforeach;


          // $nullArray      = array_keys(array_column($data, 'parent_message_id'),'0');
          // $mainArray = array();
          //   foreach($nullArray as $row):
          //       $childKeys = array_keys(array_column($data, 'parent_message_id'), $data[$row]['id']);
          //       $data[$row]['reply'] = array();
          //         if(count($childKeys) > 0){
          //           foreach($childKeys as $childRow):
          //               $data[$childRow]['time_ago']  = ($data[$childRow]['created_at'])?\Carbon\Carbon::createFromTimeStamp(strtotime($data[$childRow]['created_at']))->diffForHumans():'';
          //               $data[$row]['reply'][]        = (array) $data[$childRow];
          //           endforeach;
          //       }
          //       $node                   = (array) $data[$row];
          //       $node['time_ago']       = ($node['created_at'])?\Carbon\Carbon::createFromTimeStamp(strtotime($node['created_at']))->diffForHumans():'';
          //       $mainArray[]            = $node;
          //   endforeach;
          return response()->json(['data'=> $mainArray, 'status' => true, 'message' => 'Messages!']);
      }

      function checkMessageByLastMsg(Request $request){
          $co_id          = my_simple_crypt($request->get('co_id'),'d');
          $sisID          = auth()->user()->id;
          $studentSisID   = $request->get('to_user_id');
          $lastMessageID  = (int) $request->get('last-message');

          if($lastMessageID == 0){
            return response()->json(['data'=> [], 'status' => true, 'message' => 'Messages!']);
          }


          // return ChatModel::all();

        //  echo $lastMessageID; exit;
// select * from (co_id = $co_id AND private_comment IS null) OR (to_user_id = 18167 AND private_comment = 1) AND id > 14034

          if($studentSisID != ''){
            $rawWhereString = "co_id = $co_id AND to_user_id = '$sisID' AND entry_user_id = $studentSisID AND private_comment = 1 AND id > ".$lastMessageID;
          }else{
              $rawWhereString = " ((co_id = $co_id AND private_comment IS null) OR (to_user_id = '$sisID' AND private_comment = 1) ) AND id > ".$lastMessageID;
          }
          $data           = ChatModel::whereRaw($rawWhereString)->orderBy('id', 'asc')->get()->toArray();

          


          // if( count($data) == 0 ){
          //   return response()->json(['data'=> [], 'status' => true, 'message' => 'Messages!']);
          // }



          $nullArray      = array_keys(array_column($data, 'co_id'),$co_id);
          $mainArray = array();
            foreach($nullArray as $row):
                $childKeys = array_keys(array_column($data, 'parent_message_id'), $data[$row]['id']);
                $data[$row]['reply'] = array();
                  if(count($childKeys) > 0){
                    foreach($childKeys as $childRow):
                        $data[$childRow]['time_ago']  = ($data[$childRow]['created_at'])?\Carbon\Carbon::createFromTimeStamp(strtotime($data[$childRow]['created_at']))->diffForHumans():'';
                        $data[$row]['reply'][]        = (array) $data[$childRow];
                    endforeach;
                }
                $node                   = (array) $data[$row];
                $node['time_ago']       = ($node['created_at'])?\Carbon\Carbon::createFromTimeStamp(strtotime($node['created_at']))->diffForHumans():'';
                $mainArray[]            = $node;
            endforeach;
          return response()->json(['data'=> $mainArray, 'status' => true, 'message' => 'Messages!']);




          // $nullArray      = array_keys(array_column($data, 'parent_message_id'),'0');
          // $mainArray = array();
          //   foreach($nullArray as $row):
          //       $childKeys = array_keys(array_column($data, 'parent_message_id'), $data[$row]['id']);
          //       $data[$row]['reply'] = array();
          //         if(count($childKeys) > 0){
          //           foreach($childKeys as $childRow):
          //               $data[$childRow]['time_ago']  = ($data[$childRow]['created_at'])?\Carbon\Carbon::createFromTimeStamp(strtotime($data[$childRow]['created_at']))->diffForHumans():'';
          //               $data[$row]['reply'][]        = (array) $data[$childRow];
          //           endforeach;
          //       }
          //       $node                   = (array) $data[$row];
          //       $node['time_ago']       = ($node['created_at'])?\Carbon\Carbon::createFromTimeStamp(strtotime($node['created_at']))->diffForHumans():'';
          //       $mainArray[]            = $node;
          //   endforeach;
          // return response()->json(['data'=> $mainArray, 'status' => true, 'message' => 'Messages!']);

          



      }




function getMessageDirect(Request $request){
// PRIVATE_COMMENT
$co_id      = my_simple_crypt($request->get('co_id'),'d');
$data       = ChatModel::where('co_id',$co_id)->where('private_comment',1)->orderBy('id', 'asc')->get()->toArray();

return $data;

}

function readMessage(Request $request){
  $idsArr = $request->get('messageID');
  $arr    = explode(',', $idsArr);
  foreach($arr as $id):
    $chat       = ChatModel::find($id);
    if($chat->seen_record == NULL){
      $chat->seen_record = json_encode([auth()->user()->username]);
    }else{
          $json_array = json_decode(  $chat->seen_record , true );
          if( ($json_array) && !(in_array(auth()->user()->username,$json_array) ) ){
            $json_array[]      = auth()->user()->username;
            $chat->seen_record = json_encode($json_array);
          }
    }
    $chat->save();
  endforeach;
  return response()->json(['data'=> [], 'status' => true, 'message' => 'Messages!']);
}


function course_message_count(Request $request){
$data = ChatModel::select(DB::raw('count(*) as co_total_message,co_id'))
->whereIn('co_id', $request->get('co_id') )
->where('entry_user_id','!=',auth()->user()->username)
->whereRaw(" (SEEN_RECORD IS NULL OR SEEN_RECORD NOT LIKE '%".auth()->user()->username."%') ")
->groupBy('co_id')
->get();

return response()->json(['data'=> $data, 'status' => true, 'message' => 'Messages!']);
}



function updateMessage(Request $request){
  $msgID = $request->get('msg-id');
  $msg = ChatModel::where('id',$msgID)->where('entry_user_id',"".auth()->user()->id."")->first();
  if($msg){
    $msg->message = $request->get('message');
    $msg->save();
    return response()->json(['data'=> $msg, 'status' => true, 'message' => 'Messages Updat!']);
  }else{
    return response()->json(['data'=> array(), 'status' => false, 'message' => 'Action Fail!']);
  }
}

//// zain 

//// zain 

function questionMessage(Request $request){
  $ques_tt_id   =   $request->get('ques_tt_id');
  $mesg         =   $request->get('question_text');
  $co_id        =   $request->get('co_id');
  $current_date =   date('Y-m-d H:i:s');
  $enrollmentNo =   strtoupper(auth()->user()->username);

  $ora_update   =   DB::table('Messages_bnu')->insert([
        'MESSAGE'=> $mesg,
        'ENTRY_USER_ID'=>$enrollmentNo,
        'sender_name'=>$enrollmentNo,
        'created_at'=> $current_date,
        'co_id'=> $co_id,
        'type'=> 'coursematerial',
        'tt_id'=> $ques_tt_id
  ]);
  return response()->json(['data'=> $ques_tt_id, 'status' => true, 'message' => 'Question inserted!']);
}




function replyMessage(Request $request){
   $reply_mesg_tt_id   =   $request->get('reply-mesg-tt_id');
  $msgID            =   $request->get('reply-msg-id');
  $mesg             =   $request->get('message');
  $co_id            =   $request->get('co_id');
  $co_id            =   my_simple_crypt($co_id,'d');
  $current_date     =   date('Y-m-d H:i:s');
  $enrollmentNo     =   strtoupper(auth()->user()->username);
  $toUserID         =   $request->get('reply-to-user-id');

  $ora_update       =   DB::table('Messages_bnu')->insert([
      'PARENT_MESSAGE_ID' => $msgID,
      'MESSAGE'=> $mesg,
      'ENTRY_USER_ID'=>auth()->user()->id,
      'sender_name'=>$enrollmentNo,
      'created_at'=> $current_date,
       'tt_id'=> $reply_mesg_tt_id,
      'co_id'=> $co_id,
      'to_user_id' => $toUserID
  ]);

  return response()->json(['data'=> $msgID, 'status' => true, 'message' => 'Messages inserted!']);
  exit();

}

      function getStudentByCourse(Request $request){ 

        // DB::table('student_courses')->where($where)->get();
        $where['cms_co_id'] = $request->get('co_id');

       $data =  DB::select('select sisusers.username, sisusers.id, GET_STUDENT_NAME(SC_SAI_ENROLLMENT_NO) as name from student_courses join sisusers on student_courses.sc_sai_enrollment_no = upper(sisusers.username) where student_courses.sc_cms_course_co_id = '.$request->get('co_id'));

            

            // $data = DB::table('student_courses')->leftJoin('sisusers', function($join){
            //                     $join->on(DB::raw("student_courses.sc_sai_enrollment_no = sisusers.username",DB::raw(''),DB::raw(''))); 
            //                 })->where($where)->get();

            return response()->json([
                'status' => true,
                'data' => $data
            ]);
      }





    
} // end class