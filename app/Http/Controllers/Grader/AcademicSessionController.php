<?php
/**
 * Created by PhpStorm.
 * User: Awais Khan
 * Date: 07-Jun-18
 * Time: 12:43 PM
 */

namespace App\Http\Controllers\Grader;
use App\Http\Controllers\Controller;
use App\sessions;
use App\Models\User;
use Illuminate\Support\Facades\View;
use App\Models\Grader\AcademicSessions;
use DB;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class AcademicSessionController extends Controller
{

    private $sortBy = 'id';
    private $sortDirection = 'ASC';

    private $sessions;

    public function __construct(AcademicSessions $sessions)
    {
        $this->session = $sessions;
    }

    public function index()
    {
        $sessions = $this->session->fetch();
        return view('grader.common.sessions', compact('sessions'));
    }
    public function schlrshipSessionRetrieve()
    {
        // $sessions = DB::table('ACADEMIC_SESSIONS')
        // ->where('SESS_STATUS', 'E')
        // ->where('SESS_CODE', 'NOT LIKE', 'SUM%')
        // ->orderBy('SESS_ID', 'desc')
        // ->get();
        $sessions = DB::table('ACADEMIC_SESSIONS')
        ->where('SESS_STATUS', 'E')
        ->orderBy('SESS_ID', 'desc')
        ->get();

        return $sessions;
    }
    public function retrieve()
    {
        return $sessions = $this->session->fetch();
    }
    public function retrieveTopSessions($count=6)
    {
        return $sessions = $this->session->fetchTopSession($count);
    }
    function getsessions(){
        //dd(auth()->user());
        $employee_id=auth()->user()->employee_id;
        $sess_id=auth()->user()->profile_settings->active_session;
        $sql="SELECT
        count(*) as total,sess_name,academic_sessions.SESS_ID
    FROM
        academic_sessions
        LEFT JOIN faculty_courses ON sess_id = faculty_courses.fc_co_sess_id
    WHERE
    1=1
       --     sess_id =$sess_id
        AND
            faculty_courses.fc_employee_id =  $employee_id
        AND
            fc_status = 'E' group by sess_name,academic_sessions.SESS_ID order by academic_sessions.sess_id desc";
        return $result =DB::select($sql);
        //return DB::table('ACADEMIC_SESSIONS')->orderBy('sess_id', 'desc')->get();
    }

    public function set_sesion(Request $request)
    {

        $session_array = (explode("-",$request->sel_active_session));
        if($session_array){
            $_SESSION['active_session_id']=$session_array[0];
            $_SESSION['active_session_name']=$session_array[1];
            $user=new User();
            $user = $user::find(auth()->user()->id);

            $user->saveProfileSetting(auth()->user()->id,'active_session',$session_array[0]);
            //dd(auth()->user()->id);
            return View::make('set_active_session', ['active_session_id' => $session_array[0],'active_session_name' => $session_array[1]]);
        }else{
            return 0;
        }



    }

	public function updateToken(Request $request){

        $password=Session::get('tokenPassword');

        $res = Http::post('https://api.bnu.edu.pk/do-login', [
			'username' => auth()->user()->username,
			'password' => $password,
			'device_token' => 'web_login',
		]);


        return $post = json_decode($res->getBody()->getContents());

      // dd( $post);


    }

//june 25 2025

public function index2()
{

    $academicYears = DB::table('ACADEMIC_YEARS')
    ->orderBy('AY_ID', 'desc')
    ->get();
   // $sessions = DB::table('ACADEMIC_SESSIONS')->get();

   $details = DB::table('ACADEMIC_SESSIONS_DETAIL')
   ->select([
    'ACADEMIC_SESSIONS_DETAIL.*',
    DB::raw("
        CASE
            WHEN ACADEMIC_SESSIONS_DETAIL.SESSION_ID = 166 THEN 'Spring 2026'
            ELSE (SELECT SESS_NAME FROM ACADEMIC_SESSIONS WHERE SESS_ID = ACADEMIC_SESSIONS_DETAIL.SESSION_ID)
        END AS session_name
    "),
    DB::raw("(SELECT AY_NAME FROM ACADEMIC_YEARS WHERE AY_ID = ACADEMIC_SESSIONS_DETAIL.ACADEMIC_YEAR_ID) AS year_name"),
    DB::raw("
            (SELECT C_NAME FROM CALENDARS WHERE C_ID = ACADEMIC_SESSIONS_DETAIL.description) AS description
        "),
])

->get()
->map(function ($item) {

    // Format the dates
    $item->from_date = Carbon::parse($item->from_date)->format('Y-m-d');
    if($item->end_date){
    $item->end_date = Carbon::parse($item->end_date)->format('Y-m-d');
    }
    return $item;
});
$calendars = DB::table('calendars')->get();
    // dd($details);

    return view('grader.academic_calender.index', compact('academicYears','details' ,'calendars' ));
}
public function getSessions2(Request $request)
{
    // dd($request->all());
    $sessions = DB::table('ACADEMIC_SESSIONS')
        ->where('SESS_AY_ID', $request->academic_year_id)
        ->orderBy('SESS_NAME')
        ->get();
       // Check for academic_year_id == 121
       if ($request->academic_year_id == 121) {
        $exists = $sessions->contains(function ($session) {
            return isset($session->SESS_NAME) && $session->SESS_NAME === 'Spring 2026';
        });
    //sess_id: "164", sess_code: "SUM25", sess_name: "Summer 2025", sess_ay_id: "121
        if (!$exists) {
            $sessions->push((object)[
                'sess_id' => 166, // placeholder ID
                'sess_code'=> "S26",
                'sess_name' => 'Spring 2026',
                'sess_ay_id' => 121,
            ]);
        }
    }
    return response()->json($sessions);
}


public function store(Request $request)
{
    // dd($request->all());
    $request->validate([
        'academic_year_id' => 'required',
        'session_id' => 'required',
        // 'event_name' => 'required|string',
        // 'start_date' => 'required|date',
        'calendar_id' => 'required|numeric',
        'start_dates' => 'required|array',
        'dates.*' => 'nullable|date', // each date
    ]);

    DB::beginTransaction();

    try {

        // Save each calendar detail with date
                foreach ($request->start_dates as $cd_id => $startDate) {
                $endDate = $request->end_dates[$cd_id] ?? null;

                if ($startDate ) {
                    // Get event title from calendar_details table
                    $title = DB::table('calendar_details')
                        ->where('cd_id', $cd_id)
                        ->value('cd_title');

                    DB::table('ACADEMIC_SESSIONS_DETAIL')->insert([
                        'academic_year_id' => $request->academic_year_id,
                        'session_id' => $request->session_id,
                        'event_type' => 'calendar',
                        'event_name' => $title,
                        'from_date' => $startDate,
                        'end_date' => $endDate, // <- Save end date here
                        'cd_id' => $cd_id,
                        'description' => $request->calendar_id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        // dd('commit');
        DB::commit();

        return redirect()->back()->with('success', 'Event and calendar details saved successfully!');
    } catch (\Exception $e) {
        // dd($e);
    //    dd('rollBack');
       DB::rollBack();
        return redirect()->back()->with('error', 'Something went wrong: ' . $e->getMessage());
    }
}
public function destroy(Request $request)
{
    // dd( $request->id);

    $id = $request->id;
    DB::delete('DELETE FROM ACADEMIC_SESSIONS_DETAIL WHERE id = ?', [$id]);

    return redirect()->back()->with('success', 'Event deleted successfully.');
}
public function edit($id)
{
    $editData = DB::table('ACADEMIC_SESSIONS_DETAIL')->where('id', $id)->first();
    $academicYears = DB::table('ACADEMIC_YEARS')->get();
    $sessions = DB::table('ACADEMIC_SESSIONS')
    ->where('SESS_AY_ID', $editData->academic_year_id)
    ->orderBy('SESS_NAME')
    ->get();
    $details = DB::table('ACADEMIC_SESSIONS_DETAIL')->get();

    return view('grader.academic_calender.index', compact('editData', 'academicYears','sessions', 'details'));
}

public function update(Request $request,$id)
{
    // dd($id);
    // dd($request->new_date);
DB::table('ACADEMIC_SESSIONS_DETAIL')->where('id', $id)->update([

    'from_date'        => $request->start_date,
    'end_date'        => $request->end_date,

]);
return  redirect()->route('academic-session');
}



public function getCalendarDetails($id)
{
    // dd('test');
return DB::table('calendar_details')->where('cd_calendar_id', $id)->get();

}


}
