<?php
namespace App\Http\Controllers\Grader\UniversityEvent;
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
use Auth;
use Carbon\Carbon;

class UniversityEventController extends Controller{

    public function index(){
        // dd("dd");
        $events = "SELECT
            start_time.ts_name AS start_time_slot,
            end_time.ts_name AS end_time_slot,
            university_event.*,
            building_rooms.*
        FROM
            university_event
        LEFT JOIN
            building_rooms ON university_event.event_venue_room = building_rooms.room_id
        LEFT JOIN
            timetable_slots start_time ON university_event.event_start_time = start_time.ts_code
        LEFT JOIN
            timetable_slots end_time ON university_event.event_end_time = end_time.ts_code";
        // dd(DB::select($events));
        // DB::table('university_event')
        // ->leftJoin('building_rooms','university_event.event_venue_room','building_rooms.room_id')
        // ->leftJoin('timetable_slots as start_time','university_event.event_start_time','start_time.ts_code')
        // ->leftJoin('timetable_slots as end_time','university_event.event_end_time','end_time.ts_code')
        // ->get();
        //  dd($events);
        return response()->json(DB::select($events));
    }
    public function eventApproval(){
        // dd("dd");
        $events = "SELECT
        start_time.ts_name AS start_time_slot,
        end_time.ts_name AS end_time_slot,
        university_event.*,
        building_rooms.*
        FROM
            university_event
        LEFT JOIN
            building_rooms ON university_event.event_venue_room = building_rooms.room_id
        LEFT JOIN
            timetable_slots start_time ON university_event.event_start_time = start_time.ts_code
        LEFT JOIN
            timetable_slots end_time ON university_event.event_end_time = end_time.ts_code  Order by university_event.id Desc";
                $events = DB::select($events);
        // dd($events);
            if(request()->ajax()){
                return response()->json($events);
        }
        return view("grader.UniversityEvent.EventApproval",compact("events"));
    }
    public function updateEventStatus(Request $request){
        // dd($request->input());
        $sql= DB::table("university_event")->where("id",$request->id)->update([
                'remarks' => $request->remarks,
                'status' => $request->status
        ]);
        return response()->json([
            'error' => false,
            "message" => "Status Updated Successfully."
        ]);
    }
    public function register(){
        $schoolsSql = "SELECT * FROM SCHOOLS";
        $schools = DB::Select($schoolsSql);
        $buildingSql = "select * from buildings where building_campus_name = 'TAROGIL CAMPUS'";
        $buildings =DB::Select($buildingSql);
        $timeslots = DB::table("timetable_slots")->orderBy('ts_code', 'asc')->get();
        // dd($timeslots);
        // dd($timeslots);
        // dd($buildings);
        return view("grader.UniversityEvent.register",compact("schools","buildings","timeslots"));
    }
    function eventDelete(Request $request){
        //dd($request->get('eventId'));
        DB::table('university_event')->where('id', $request->get('eventId'))->delete();
        return response()->json([
            "error" => true,
            "msg" => "record Deleted Successfully."
        ]);
    }
    private function endTimecheckSLot($check_time,$req_time){

        $check_time = Carbon::parse($check_time);
        $check_hours = $check_time->hour;
        $check_minutes = $check_time->minute;

        $req_time = Carbon::parse($req_time);
        $req_hours = $req_time->hour;
        $req_minutes = $req_time->minute;


        if($req_hours ==  $check_hours){
            if($req_minutes ==  $check_minutes ){
                return true;
            }else{
                // dd("dd");
                return false;
            }
        }elseif($req_hours <  $check_hours)
        {
            if($req_minutes <=  $check_minutes ){
                return true;
            }else{
                // dd("dd");
                return false;
            }

        }else{
            return false;
        }
    }
    private function checkSLot($check_time,$req_time){

        $check_time = Carbon::parse($check_time);
        $check_hours = $check_time->hour;
        $check_minutes = $check_time->minute;

        $req_time = Carbon::parse($req_time);
        $req_hours = $req_time->hour;
        $req_minutes = $req_time->minute;


        if($req_hours ==  $check_hours){
            if($req_minutes >=  $check_minutes ){
                return true;
            }else{
                // dd("dd");
                return false;
            }
        }elseif($req_hours <  $check_hours)
        {
            if($req_minutes <=  $check_minutes ){
                return true;
            }else{
                // dd("dd");
                return false;
            }

        }else{
            return false;
        }
    }
    private function exactSlot($event_venue_room,$event_start_time,$event_end_time,$event_date){

        return DB::table('university_event')
        ->where('event_venue_room',$event_venue_room)
        ->where('event_date', $event_date)
        ->whereBetween('event_start_time',[$event_start_time,$event_end_time])
        ->where('status','Approved')
        ->count();

    }
    private function beforeBetweenSLot($event_venue_room,$event_start_time,$event_end_time,$event_date){

        return DB::table('university_event')
        ->where('event_venue_room',$event_venue_room)
        ->where('event_date', $event_date)
        ->where('event_start_time', '>',$event_start_time)
        ->where('event_start_time', '<',$event_end_time)
        ->where('status','Approved')
        ->count();

    }
    private function afterBetweenSLot($event_venue_room,$event_start_time,$event_end_time,$event_date){

        return DB::table('university_event')
        ->where('event_venue_room',$event_venue_room)
        ->where('event_date', $event_date)
        ->where('event_end_time', '>',$event_start_time)
        ->where('event_end_time', '<=',$event_end_time)
        ->where('status','Approved')
        ->count();

    }

    private function exactSlotMTT($event_venue_room,$event_start_time,$event_end_time,$event_date){

        return DB::table('master_timetable')
        ->where('tt_room_NAME',$event_venue_room)
        ->where('TT_CLASS_DATE', $event_date)
        ->whereBetween('tt_subject_time_from_id',[$event_start_time,$event_end_time])

        ->count();

    }
    private function beforeBetweenSLotMTT($event_venue_room,$event_start_time,$event_end_time,$event_date){

        return DB::table('master_timetable')
        ->where('tt_room_NAME',$event_venue_room)
        ->where('TT_CLASS_DATE', $event_date)
        ->where('tt_subject_time_from_id', '>',$event_start_time)
        ->where('tt_subject_time_from_id', '<',$event_end_time)->count();

    }
    private function afterBetweenSLotMTT($event_venue_room,$event_start_time,$event_end_time,$event_date){

        return DB::table('master_timetable')
        ->where('tt_room_NAME',$event_venue_room)
        ->where('TT_CLASS_DATE', $event_date)
        ->where('tt_subject_time_to_id', '>',$event_start_time)
        ->where('tt_subject_time_to_id', '<=',$event_end_time)->count();

    }


    public function store(Request $request){
// dd($request->all());
        //    dd(Carbon::parse($request->event_date.' '.$request->event_start_time),$request->event_start_time);
        $schools = implode(',', (array) $request->schools);
        // $sql = DB::table('university_event')
        // ->where('event_venue_room',$request->event_venue_room)
        // ->where('event_start_time', '<',$request->event_start_time)
        // ->where('event_start_time', '>',$request->event_end_time)

        // ->where('event_date', $request->event_date)
        // ->where('event_date', $request->event_date)
        // ->where('event_date', $request->event_date)
        // ->whereBetween('event_start_time',[$request->event_start_time,$request->event_end_time])
        // ->toSql();

        // dd($request->event_end_time);
        $exact = $this->exactSlot($request->event_venue_room,$request->event_start_time,$request->event_end_time,$request->event_date);
        $beforeBetweenSLot = $this->beforeBetweenSLot($request->event_venue_room,$request->event_start_time,$request->event_end_time,$request->event_date);
        $afterBetweenSLot = $this->afterBetweenSLot($request->event_venue_room,$request->event_start_time,$request->event_end_time,$request->event_date);
        // dd($exact,$beforeBetweenSLot,$afterBetweenSLot);
        $msg = '';
        $error = false;
        if($exact>0 || $beforeBetweenSLot>0 || $afterBetweenSLot>0){
             $msg = "Already Event is scheduled on same venue and time & ";
             $error =true;
            // return response()->json([
            //     "error" => true,
            //     "msg" => "Already event is scheduled on same venue and time."
            // ]);

        }
        $exactMTT = $this->exactSlotMTT($request->event_venue_room,$request->event_start_time,$request->event_end_time,$request->event_date);
        $beforeBetweenSLotMTT = $this->beforeBetweenSLotMTT($request->event_venue_room,$request->event_start_time,$request->event_end_time,$request->event_date);
        $afterBetweenSLotMTT = $this->afterBetweenSLotMTT($request->event_venue_room,$request->event_start_time,$request->event_end_time,$request->event_date);

        if($exactMTT>0 || $beforeBetweenSLotMTT>0 || $afterBetweenSLotMTT>0){
            // $msg = "Already Event is scheduled on same venue and time & ";
            // $error =true;
            return response()->json([
                "error" => true,
                "msg" => "Already a class is scheduled on same venue and time."
            ]);

        }
        // foreach($sql as $a){
        //     // dd($a->event_start_time);
        //     if($this->checkSLot($a->event_start_time,Carbon::parse($request->event_date.' '.$request->event_start_time)) || $this->endTimecheckSLot($a->event_end_time,Carbon::parse($request->event_date.' '.$request->event_start_time))){
        //         return response()->json([
        //             "error" => true,
        //             "msg" => "Already Event is scheduled on same venue."
        //         ]);
        //     }

        // }
        // $sql1 = "select * from master_timetable where tt_class_date = TO_DATE('".Carbon::parse($request->event_date)->format("d-M-Y")."', 'DD-Mon-YYYY')
        //     and tt_room_name = '$request->event_venue_room'";
        // // dd($sql1);
        // $res = DB::select($sql1);
        // // dd($res);
        // foreach($res as $a){
        //     // dd($a->event_start_time);
        //     if($this->checkSLot($a->tt_subject_time_from ,Carbon::parse($request->event_date.' '.$request->event_start_time)) || $this->checkSLot($a->tt_subject_time_to ,Carbon::parse($request->event_date.' '.$request->event_start_time))){
        //         return response()->json([
        //             "error" => true,
        //             "msg" => "Already Class is scheduled on same venue."
        //         ]);
        //     }

        // }

        // foreach($sql1 as $a){
        //     // dd($a->event_start_time);
        //     if($this->checkSLot($a->event_start_time,Carbon::parse($request->event_date.' '.$request->event_start_time)) || $this->checkSLot($a->event_end_time,Carbon::parse($request->event_date.' '.$request->event_start_time))){
        //         return response()->json([
        //             "error" => true,
        //             "msg" => "Already Event is scheduled on same venue."
        //         ]);
        //     }

        // }

    

         if($request->add_admin_checkbox=="on"){
            $procedurenotification = 'send_mail_one_by_one';
            $bindings_notification = [
                'to'           => 'zain.ali@bnu.edu.pk',
                // 'to'           => $to,
                'subject'      => "Required Admin Support for the Event ",
                'message'      =>$request->to_admin_text,
            ];
         }
        // if($sql){
        //     return response()->json([
        //         "error" => true,
        //         "msg" => "Already Event is scheduled on same venue."
        //     ]);
        // }
        $status="";
        if($request->event_venue_building=="0"){
            $status="Approved";
        }

       $res = DB::table('university_event')->insert([
            // 'id' => 2,
            'event_type' => $request->event_type,
            'event_title' => $request->event_title,
            'event_description' => $request->event_description,
            'event_date' => $request->event_date,
            'to_event_date' => $request->to_event_date,
            'event_start_time' => $request->event_start_time,
            'event_end_time' =>$request->event_end_time,
            'event_school_id' => $schools,
            "event_venue_building" => $request->event_venue_building,
            "event_venue_room" => $request->event_venue_room,
            "status" => $status,
            "admin_details" => $request->to_admin_text,
            "uv_entry_user" => auth()->user()->username
        ]);
        return response()->json([
            "error" => $error,
            "msg" => $msg. " Event Added Successfully."
        ]);

    }

    public function eventList(){
        // dd("dd");
        $sql = "SELECT
            start_time.ts_name AS start_time_slot,
            end_time.ts_name AS end_time_slot,
            university_event.* from university_event
            LEFT JOIN
            timetable_slots start_time ON university_event.event_start_time = start_time.ts_code
        LEFT JOIN
            timetable_slots end_time ON university_event.event_end_time = end_time.ts_code  where status='Approved'";
        $res = DB::select($sql);
        return $res;

    }
    public function viewInCalendar(){

        return view("grader.UniversityEvent.calendar");


    }


    public function getRoomList(Request $request){
        // dd($request->input());
        $sql="SELECT  room_id,room_name,
        (SELECT COUNT(*) FROM master_timetable WHERE tt_room_NAME=building_rooms.room_id AND TT_CLASS_DATE='".Carbon::parse($request->date)->format('Y-m-d')."' AND (master_timetable.tt_subject_time_from_id = '".$request->from."' or master_timetable.tt_subject_time_to_id = '".$request->to."' ) )  as is_occupied_by_class,
        (SELECT COUNT(*) FROM university_event WHERE event_venue_room=building_rooms.room_id AND event_date='".Carbon::parse($request->date)->format('Y-m-d')."' AND (university_event.event_start_time = '".$request->from."' or university_event.event_end_time = '".$request->to."')  and status = 'Approved') as is_occupied_by_ue
        from building_rooms where building_rooms.room_building_id = '$request->building_code'";


    //echo $sql;exit;
        return $rooms = DB::select($sql);
//dd($rooms);


                 return response()->json([
                     'rooms' => $rooms,

                ], 200);



        return $rooms;

    }

    public function checkOtherEvents(Request $request){
        $events = DB::table('university_event')->leftJoin('building_rooms','university_event.event_venue_room','building_rooms.room_id')->where('event_date',$request->date)->get();
        // dd($events);
        return response()->json($events);

    }


}
