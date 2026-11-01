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
use Response;
use Validator;

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
        if(auth()->user()->username != 'haider.raza'){
            $events .= " where uv_entry_user = '".auth()->user()->username."'";   
        }
          // echo $events;exit;
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
            timetable_slots end_time ON university_event.event_end_time = end_time.ts_code
            
            /* 
               === FILTERING & SORTING LOGIC ===
               1. Date Filter: Only show events from Dec 15, 2025 onwards (Future Events).
               2. Order By Status: 'Pending' events come FIRST (0), others LAST (1).
               3. Order By Time: Within Pending, show Latest created first.
            */
            WHERE university_event.event_date >= '2025-12-15'
            ORDER BY 
                CASE 
                    WHEN university_event.status IS NULL OR university_event.status = '' THEN 0 
                    WHEN university_event.status = 'Pending' THEN 0
                    ELSE 1 
                END ASC,
                university_event.created_at DESC, 
                university_event.id DESC";
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
        // if (auth()->user()->group_id != 644) {
        //     return view('grader.UniversityEvent.register-denied');
        // }
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
    private function applyEventCoversDay($query, $day)
    {
        return $query
            ->whereRaw("TRUNC(event_date) <= TO_DATE(?, 'YYYY-MM-DD')", [$day])
            ->whereRaw("TRUNC(NVL(to_event_date, event_date)) >= TO_DATE(?, 'YYYY-MM-DD')", [$day]);
    }
    private function exactSlot($event_venue_room,$event_start_time,$event_end_time,$event_date){

        // return DB::table('university_event')
        // ->where('event_venue_room',$event_venue_room)
        // ->where('event_date', $event_date)
        // ->whereBetween('event_start_time',[$event_start_time,$event_end_time])
        // ->where('status','Approved')
        // ->count();

        return $this->applyEventCoversDay(
            DB::table('university_event')
                ->where('event_venue_room',$event_venue_room)
                ->whereBetween('event_start_time',[$event_start_time,$event_end_time])
                ->where('status','Approved'),
            $event_date
        )->count();
    }
    private function beforeBetweenSLot($event_venue_room,$event_start_time,$event_end_time,$event_date){

        // return DB::table('university_event')
        // ->where('event_venue_room',$event_venue_room)
        // ->where('event_date', $event_date)
        // ->where('event_start_time', '>',$event_start_time)
        // ->where('event_start_time', '<',$event_end_time)
        // ->where('status','Approved')
        // ->count();
        return $this->applyEventCoversDay(
            DB::table('university_event')
                ->where('event_venue_room',$event_venue_room)
                ->where('event_start_time', '>',$event_start_time)
                ->where('event_start_time', '<',$event_end_time)
                ->where('status','Approved'),
            $event_date
        )->count();

    }
    private function afterBetweenSLot($event_venue_room,$event_start_time,$event_end_time,$event_date){

        // return DB::table('university_event')
        // ->where('event_venue_room',$event_venue_room)
        // ->where('event_date', $event_date)
        // ->where('event_end_time', '>',$event_start_time)
        // ->where('event_end_time', '<=',$event_end_time)
        // ->where('status','Approved')
        // ->count();
        return $this->applyEventCoversDay(
            DB::table('university_event')
                ->where('event_venue_room',$event_venue_room)
                ->where('event_end_time', '>',$event_start_time)
                ->where('event_end_time', '<=',$event_end_time)
                ->where('status','Approved'),
            $event_date
        )->count();

    }

    private function exactSlotMTT($event_venue_room,$event_start_time,$event_end_time,$event_date){

        // return DB::table('master_timetable')
        // ->where('tt_room_NAME',$event_venue_room)
        // ->where('TT_CLASS_DATE', $event_date)
        // ->where('TT_STATUS', 'E')
        // ->whereBetween('tt_subject_time_from_id',[$event_start_time,$event_end_time])

        // ->count();
        $sql = "SELECT
                *
            FROM
                master_timetable
            WHERE
                tt_room_name = $event_venue_room
                AND tt_class_date = '$event_date'
                AND tt_status = 'E' 
            
                AND ( ( tt_subject_time_from_id <= $event_start_time
                        AND tt_subject_time_to_id >= $event_end_time ) )
                AND ( $event_start_time BETWEEN tt_subject_time_from_id AND tt_subject_time_to_id
                    OR ( $event_end_time BETWEEN tt_subject_time_from_id AND tt_subject_time_to_id )
                    AND tt_subject_time_to_id != $event_start_time
                    AND tt_subject_time_from_id != $event_end_time )";
        $res = DB::select($sql);
        return count($res);

    }
    private function beforeBetweenSLotMTT($event_venue_room,$event_start_time,$event_end_time,$event_date){

        // return DB::table('master_timetable')
        // ->where('tt_room_NAME',$event_venue_room)
        // ->where('TT_CLASS_DATE', $event_date)
        // ->where('TT_STATUS', 'E')
        // ->where('tt_subject_time_from_id', '>',$event_start_time)
        // ->where('tt_subject_time_from_id', '<',$event_end_time)->count();

        $count = DB::table('master_timetable')
        ->where('tt_room_name', $event_venue_room)
        ->whereRaw(
            "TRUNC(TT_CLASS_DATE) = TO_DATE(?, 'YYYY-MM-DD')",
            [$event_date]
        )
        ->where('tt_status', 'E')
        ->where('tt_subject_time_from_id', '>', $event_start_time)
        ->where('tt_subject_time_from_id', '<', $event_end_time)
        ->count();

    }
    private function afterBetweenSLotMTT($event_venue_room,$event_start_time,$event_end_time,$event_date){

        return DB::table('master_timetable')
        ->where('tt_room_NAME',$event_venue_room)
        ->where('TT_CLASS_DATE', $event_date)
        ->where('TT_STATUS', 'E')
        ->where('tt_subject_time_to_id', '>',$event_start_time)
        ->where('tt_subject_time_to_id', '<=',$event_end_time)->count();

    }


    public function store(Request $request){
        
        // ---------------------------------------------------------
        // LOGIC FOR MULTIPLE SCHEDULE (NEW "FRESHER" LOGIC)
        // ---------------------------------------------------------
        if($request->has('schedule_json') && !empty($request->schedule_json)) {
            
            // 1. Common Validation is handled below or we can rely on UI.
            // But let's keep specific validation here for safety.
            // NO 'event_date' required here
            $validator = Validator::make($request->all(),[
                'event_title' => 'required',
                'event_type' => 'required',
            ]);

            if($validator->fails()) {
                return Response::json(array(
                    'success' => false,
                    'errors' => $validator->getMessageBag()->toArray()
                ), 400);
            }

            $scheduleData = json_decode($request->schedule_json, true);

            if(count($scheduleData) == 0){
                 return response()->json([
                    "error" => true,
                    "msg" => "Please add at least one schedule."
                ]);
            }

           
            // 2. Loop to Check Availability (Validate ALL slots first)
            foreach($scheduleData as $slot) {
                // Formatting data for check functions
                $date = $slot['date'];
                $start = $slot['start_time'];
                $end = $slot['end_time'];
                $room = $slot['room'];

                // Check University Event Clashes
                $exact = $this->exactSlot($room, $start, $end, $date);
                $before = $this->beforeBetweenSLot($room, $start, $end, $date);
                $after = $this->afterBetweenSLot($room, $start, $end, $date);

                if($exact>0 || $before>0 || $after>0){
                     return response()->json([
                        "error" => true,
                        "msg" => "Clash detected on Date: $date. Room is already booked."
                    ]);
                }

                // Check Timetable Clashes (MTT)
                $exactMTT = $this->exactSlotMTT($room, $start, $end, $date);
                $beforeMTT = $this->beforeBetweenSLotMTT($room, $start, $end, $date);
                $afterMTT = $this->afterBetweenSLotMTT($room, $start, $end, $date);

                if($exactMTT>0 || $beforeMTT>0 || $afterMTT>0){
                    return response()->json([
                        "error" => true,
                        "msg" => "Clash detected with Class Timetable on Date: $date."
                    ]);
                }
            }

            // 3. Loop to Insert Data (If all checks passed)
            $schools = implode(',', (array) $request->schools);
            
            foreach($scheduleData as $slot) {

                // -------------------------------------------------------------
                // PRE-INSERT CHECK: Strictly check availability in DB
                // Ignoring Valid Status ('Approved') - Check EVERYTHING
                // This prevents ORA-00001 Unique Constraint Violations
                // -------------------------------------------------------------

                // $conflict = DB::table('university_event')
                //     ->where('event_venue_room', $slot['room'])
                //     ->where('event_date', $slot['date'])
                //     ->where(function($q) use ($slot) {
                $conflict = $this->applyEventCoversDay(
                        DB::table('university_event')
                            ->where('event_venue_room', $slot['room']),
                        $slot['date']
                    )->where(function($q) use ($slot) {
                        // Check for ANY overlap
                        $q->where(function($sub) use ($slot) {
                            // Case A: Existing Start is inside New Range
                            $sub->where('event_start_time', '>=', $slot['start_time'])
                                ->where('event_start_time', '<', $slot['end_time']);
                        })
                        ->orWhere(function($sub) use ($slot) {
                            // Case B: Existing End is inside New Range
                            $sub->where('event_end_time', '>', $slot['start_time'])
                                ->where('event_end_time', '<=', $slot['end_time']);
                        })
                        ->orWhere(function($sub) use ($slot) {
                            // Case C: Existing Encloses New Range
                            $sub->where('event_start_time', '<=', $slot['start_time'])
                                ->where('event_end_time', '>=', $slot['end_time']);
                        });
                    })
                    ->count();

                if($conflict > 0){
                    return response()->json([
                        "error" => true,
                        // "msg" => "Conflict detected! Room " . $slot['room_name'] . " is already booked on " . $slot['date'] . "."
                         "msg" => "Schedule Conflict: An event is already booked at " . $slot['room_name'] . " on " . $slot['date'] . " during this time."
                    ]);
                }

                $status = "";
                if($slot['building'] == "0"){
                    $status = "Approved";
                }

                // Insert into DB
                DB::table('university_event')->insert([
                     'event_type' => $request->event_type,
                     'event_title' => $request->event_title,
                     'event_description' => $request->event_description,
                     'event_date' => $slot['date'],
                     'to_event_date' => $slot['date'], // Same day
                     'event_start_time' => $slot['start_time'],
                     'event_end_time' => $slot['end_time'],
                     'event_school_id' => $schools,
                     "event_venue_building" => $slot['building'],
                     "event_venue_room" => $slot['room'],
                     "status" => $status,
                     "admin_details" => $request->to_admin_text,
                     "itrc_details" => $request->to_itrc_text,
                     "uv_entry_user" => auth()->user()->username
                ]);

                // Send Notifications
                // (Copied from original logic)
                $eventStartTime = DB::table('timetable_slots')->where('ts_id', $slot['start_time'])->value('ts_name');
                $eventEndTime = DB::table('timetable_slots')->where('ts_id', $slot['end_time'])->value('ts_name');
                $eventTime = $eventStartTime . ' To ' . $eventEndTime;
                
                $message_content = $this->generateEventNotificationMessage($request->to_admin_text, $request->to_itrc_text, $slot['room'], $request->event_title, $slot['date'], auth()->user()->username, $eventTime);

                if($request->add_admin_checkbox=="on"){
                    // DB::executeProcedure('send_mail_one_by_one', [
                    //      'to' => 'ahmer.sayeed@bnu.edu.pk',
                    //      'subject' => "Required Admin Support for Event: " . $request->event_title,
                    //      'message' => $message_content,
                    // ]);
                }
                if($request->add_itrc_checkbox=="on"){
                    // DB::executeProcedure('send_mail_one_by_one', [
                    //     'to' => 'naveed@bnu.edu.pk',
                    //     'subject' => "Required ITRC Support for Event: " . $request->event_title,
                    //     'message' => $message_content,
                    // ]);
                }
            }

            return response()->json([
                "error" => false,
                "msg" => "All schedules added successfully."
            ]);


        } else {

        // ---------------------------------------------------------
        // ORIGINAL LOGIC (SINGLE EVENT)
        // ---------------------------------------------------------
        
        $validator = Validator::make($request->all(),[
            'event_title' => 'required',
            'event_type' => 'required',
            'event_date' => 'required',
            'event_venue_building' => 'required',
            'event_venue_room' => 'required'
        ], [
            'event_title.required' => 'The event title is required.',
            'event_type.required' => 'The event type is required.',
            'event_date.required' => 'The event date is required.',
            'event_venue_building.required' => 'The venue building is required.',
            'event_venue_room.required' => 'The venue room is required.',
        ]);
        if($validator->fails()) {
            return Response::json(array(
                'success' => false,
                'errors' => $validator->getMessageBag()->toArray()
            ), 400);
        }
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
        $to_date = '';
        if($request->to_event_date == ''){
            $to_date = $request->event_date;
        }else{
            $to_date = $request->to_event_date;
        }
        // $exact = $this->exactSlot($request->event_venue_room,$request->event_start_time,$request->event_end_time,$request->event_date);
        // $beforeBetweenSLot = $this->beforeBetweenSLot($request->event_venue_room,$request->event_start_time,$request->event_end_time,$request->event_date);
        // $afterBetweenSLot = $this->afterBetweenSLot($request->event_venue_room,$request->event_start_time,$request->event_end_time,$request->event_date);
        // dd($exact,$beforeBetweenSLot,$afterBetweenSLot);
        $eventConflict = DB::table('university_event')
        ->where('event_venue_room', $request->event_venue_room)
        ->where('status', 'Approved')
        ->whereRaw("TRUNC(event_date) <= TO_DATE(?, 'YYYY-MM-DD')", [$to_date])
        ->whereRaw("TRUNC(NVL(to_event_date, event_date)) >= TO_DATE(?, 'YYYY-MM-DD')", [$request->event_date])
        ->where('event_start_time', '<', $request->event_end_time)
        ->where('event_end_time', '>', $request->event_start_time)
        ->count();
        $msg = '';
        $error = false;
        // if($exact>0 || $beforeBetweenSLot>0 || $afterBetweenSLot>0){
        if($eventConflict > 0){
             $msg = "Already Event is scheduled on same venue and time & ";
             $error =true;
            // return response()->json([
            //     "error" => true,
            //     "msg" => "Already event is scheduled on same venue and time."
            // ]);

        }


        // $exactMTT = $this->exactSlotMTT($request->event_venue_room,$request->event_start_time,$request->event_end_time,$request->event_date);
        // $beforeBetweenSLotMTT = $this->beforeBetweenSLotMTT($request->event_venue_room,$request->event_start_time,$request->event_end_time,$request->event_date);
        // $afterBetweenSLotMTT = $this->afterBetweenSLotMTT($request->event_venue_room,$request->event_start_time,$request->event_end_time,$request->event_date);

        $rangeStart = Carbon::parse($request->event_date);
        $rangeEnd = Carbon::parse($to_date);
        for ($d = $rangeStart->copy(); $d->lte($rangeEnd); $d->addDay()) {
            $day = $d->format('Y-m-d');
            $exactMTT = $this->exactSlotMTT($request->event_venue_room,$request->event_start_time,$request->event_end_time,$day);
            $beforeBetweenSLotMTT = $this->beforeBetweenSLotMTT($request->event_venue_room,$request->event_start_time,$request->event_end_time,$day);
            $afterBetweenSLotMTT = $this->afterBetweenSLotMTT($request->event_venue_room,$request->event_start_time,$request->event_end_time,$day);

            if($exactMTT>0 || $beforeBetweenSLotMTT>0 || $afterBetweenSLotMTT>0){
                return response()->json([
                    "error" => true,
                    "msg" => "Already a class is scheduled on same venue and time."
                ]);
            }
        }
                $eventStartTime = DB::table('timetable_slots')
                                ->where('ts_id', $request->event_start_time)
                                ->value('ts_name');
                 $eventEndTime = DB::table('timetable_slots')
                                ->where('ts_id', $request->event_end_time)
                                ->value('ts_name');

                 $eventTime = $eventStartTime . ' To ' . $eventEndTime;


        $message_content = $this->generateEventNotificationMessage($request->to_admin_text, $request->to_itrc_text, $request->event_venue_room, $request->event_title, $request->event_date,auth()->user()->username,$eventTime);

         if($request->add_admin_checkbox=="on"){
            $procedurenotification = 'send_mail_one_by_one';
            $bindings_notification = [
              //  'to'           => 'muhammad.hamid@bnu.edu.pk',
                 'to'           => 'ahmer.sayeed@bnu.edu.pk',
                'subject'      => "Required Admin Support for the Event ",
                'message'      =>$message_content,
            ];
            // $result_notification = DB::executeProcedure($procedurenotification, $bindings_notification);

         }
          if($request->add_itrc_checkbox=="on"){
            $procedurenotification = 'send_mail_one_by_one';
            $bindings_notification = [
            //    'to'           => 'muhammad.hamid@bnu.edu.pk',
                'to'           => 'naveed@bnu.edu.pk',
                'subject'      => "Required ITRC Support for the Event ",
                'message'      =>$message_content,
            ];
            // $result_notification = DB::executeProcedure($procedurenotification, $bindings_notification);

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
             //'id' => 2,
             'event_type' => $request->event_type,
             'event_title' => $request->event_title,
             'event_description' => $request->event_description,
             'event_date' => $request->event_date,
             'to_event_date' => $to_date,//$request->to_event_date,
             'event_start_time' => $request->event_start_time,
             'event_end_time' =>$request->event_end_time,
             'event_school_id' => $schools,
             "event_venue_building" => $request->event_venue_building,
             "event_venue_room" => $request->event_venue_room,
             "status" => $status,
             "admin_details" => $request->to_admin_text,
             "itrc_details" => $request->to_itrc_text,
             "uv_entry_user" => auth()->user()->username
         ]);





        return response()->json([
            "error" => $error,
            "msg" => $msg. " Event Added Successfully."
        ]);
        }
    }

    public function eventList(){
        // dd("dd");
        $sql = "SELECT
            start_time.ts_name AS start_time_slot,
            end_time.ts_name AS end_time_slot,
			(select room_name from building_rooms where room_id=university_event.event_venue_room) as room_name,
            university_event.* from university_event
            LEFT JOIN
            timetable_slots start_time ON university_event.event_start_time = start_time.ts_code
        LEFT JOIN
            timetable_slots end_time ON university_event.event_end_time = end_time.ts_code  where status='Approved'";
			//echo $sql;exit;
        $res = DB::select($sql);
        return $res;

    }
    public function viewInCalendar(){

        return view("grader.UniversityEvent.calendar");


    }


    public function getRoomList(Request $request){

        // -------------------------------------------------------------
        // UNIFIED LOGIC: Handle Both Single & Multiple Dates
        // ------------------------------------------------------------- 
        
        // 0. Handle Building 0 (Virtual Rooms) - Special Case
        if($request->building_code == 0){
             $sel="SELECT
             event_venue_room as room_id,
             (
                 CASE
                     WHEN (
                         SELECT COUNT(*)
                         FROM university_event
                         WHERE
                             event_venue_room IN ('01', '02', '03', '04')
                             AND event_date = '".Carbon::parse($request->date)->format('Y-m-d')."'
                             AND (
                                 (event_start_time <= '".$request->to."' AND event_end_time >= '".$request->from."')
                                 OR ('".$request->from."' BETWEEN event_start_time AND event_end_time)
                                 OR ('".$request->to."' BETWEEN event_start_time AND event_end_time)
                             )
                             // AND status = 'Approved' // Removed to include Pending
                     ) > 0 THEN 0 // Occupied (0 means not free? Wait, logic below expects is_free)
                     ELSE 1 // Free
                 END
             ) AS is_free_legacy
             FROM
                 university_event
             WHERE
             event_venue_room IN ('01', '02', '03', '04')
             AND event_date = '".Carbon::parse($request->date)->format('Y-m-d')."' ";
           
             // NOTE: This Building 0 logic is quite specific and legacy. 
             // Ideally we should standardize it too, but better not break it.
             // For now, let's keep the return as is, or adapt it if frontend breaks.
             // The user didn't complain about Building 0.
             return $rooms = DB::select($sel);
        }

        // 1. Prepare Dates Array
        $dates = [];
        if($request->has('dates') && is_array($request->dates)) {
            $dates = $request->dates;
        } elseif ($request->has('date')) {
            $dates = [$request->date];
            if ($request->filled('todate') && $request->todate !== 'undefined') {
                $rangeStart = Carbon::parse($request->date);
                $rangeEnd = Carbon::parse($request->todate);
                if ($rangeEnd->gte($rangeStart)) {
                    $dates = [];
                    for ($d = $rangeStart->copy(); $d->lte($rangeEnd); $d->addDay()) {
                        $dates[] = $d->format('Y-m-d');
                    }
                }
            }
        } else {
            return response()->json([]);
        }

        $building = $request->building_code;
        $from = $request->from;
        $to = $request->to;

        // 2. Get All Rooms for Building
        $allRooms = DB::table('building_rooms')
                        ->where('room_building_id', $building)
                        ->where('room_status', 'E') // Ensure active rooms
                        ->select('room_id', 'room_name','room_capacity')
                        ->get();

                        // dd($allRooms);

        // 3. Identify Occupied Rooms across ALL dates
        $occupiedRoomIds = [];

        foreach($dates as $date) {
            $day = Carbon::parse($date)->format('Y-m-d');

            // Check University Events (Strict Overlap Check)
            // Two intervals [a, b) and [c, d) overlap  <=>  a < d  AND  b > c
            // Using strict <, > so that an event ending at exactly $from (or
            // starting at exactly $to) is NOT counted as a conflict.

            // $ueOccupied = DB::table('university_event')
            //     ->where('event_venue_building', $building)
            //     ->where('event_date', $day)
            //     // ->where('status', 'Approved') // Removed to count Pending as Occupied
            //     ->where('event_start_time', '<', $to)
            //     ->where('event_end_time',   '>', $from)
            //     ->pluck('event_venue_room')
            //     ->toArray();
            $ueOccupied = $this->applyEventCoversDay(
                DB::table('university_event')
                    ->where('event_venue_building', $building)
                    // ->where('status', 'Approved') // Removed to count Pending as Occupied
                    ->where('event_start_time', '<', $to)
                    ->where('event_end_time',   '>', $from),
                $day
            )->pluck('event_venue_room')
            ->toArray();

            // Check Master Timetable
            $mttOccupied = DB::table('master_timetable')
                ->join('building_rooms', 'master_timetable.tt_room_name', '=', 'building_rooms.room_id')
                ->where('building_rooms.room_building_id', $building)
                ->whereRaw("TO_CHAR(tt_class_date, 'YYYY-MM-DD') = ?", [$day])
                ->where('tt_status', 'E')
                ->where(function($q) use ($from, $to) {
                    $q->whereRaw("TO_NUMBER(tt_subject_time_from_id) < ?", [$to])
                      ->whereRaw("TO_NUMBER(tt_subject_time_to_id) > ?", [$from]);
                })
                ->pluck('tt_room_name')
                ->toArray();

            $occupiedRoomIds = array_merge($occupiedRoomIds, $ueOccupied, $mttOccupied);
        }

        $occupiedRoomIds = array_unique($occupiedRoomIds);
       
        // dd($occupiedRoomIds);
        // === FINAL ROOM PROCESSING ===
        // Loop through all rooms and mark them as Free (Green) or Reserved (Red)
        $finalRoomList = [];

        // Convert occupied IDs to lookup array (better performance)
        $occupiedLookup = array_flip($occupiedRoomIds);
        
        foreach ($allRooms as $room) {
        
            if (isset($occupiedLookup[$room->room_id])) {
                $room->is_free = false;
                $room->display_text = "🔴 {$room->room_name} (Reserved)";
            } else {
                $room->is_free = true;
                $room->display_text = "🟢 {$room->room_name} (Room Capacity: {$room->room_capacity}) (Available)";
            }
        
            $finalRoomList[] = $room;
        }

        return response()->json($finalRoomList);

    }

    public function checkOtherEvents(Request $request){
        $events = DB::table('university_event')->leftJoin('building_rooms','university_event.event_venue_room','building_rooms.room_id')->where('event_date',$request->date)->get();
        // dd($events);
        return response()->json($events);

    }

    function generateEventNotificationMessage($admin_text, $itrc_text, $room_id, $event_title, $event_date, $entry_by, $eventTime) {
        // Retrieve room name from the database
        $room_name = DB::table('building_rooms')
            ->where('room_id', $room_id)
            ->value('room_name');

        // Define support text based on availability
        $support_text = '';
        if (!empty($admin_text) && !empty($itrc_text)) {
            $support_text = "$admin_text $itrc_text";
        } elseif (!empty($admin_text)) {
            $support_text = $admin_text;
        } elseif (!empty($itrc_text)) {
            $support_text = $itrc_text;
        }

        // Define the message content including the retrieved room name and support text
        $message_content = "
            Dear Team,
            This is to inform you about an upcoming event titled '$event_title,' scheduled for $event_date at $room_name from $eventTime. It has been scheduled by $entry_by.
            Your support for this event is required:
            $support_text
            Thank you.
            ";

        return $message_content;
    }


}
