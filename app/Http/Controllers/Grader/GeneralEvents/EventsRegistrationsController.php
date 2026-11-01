<?php
/**
 * Created by PhpStorm.
 * User: Maher Asad
 * Date: 5/14/2019
 * Time: 9:52 AM
 */
namespace  App\Http\Controllers\Grader\GeneralEvents;
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

class EventsRegistrationsController extends Controller{

        function eventRegistrations(){
            $sessions = \App::call('App\Http\Controllers\Grader\AcademicSessionController@retrieve');
            $schools =  Schools::all();
            $departments = array(); // $this->school->fetch();
            $facultyCourses = \App::call('App\Http\Controllers\Grader\FacultyCoursesController@retrieve');
            $offeredFaculty = \App::call('App\Http\Controllers\Grader\CoursesOfferedController@retrieveFaculty');
            $programs =  DB::select("select * from Programs where PROG_STATUS = 'E'");
            $currentuserName = auth()->user()->username;
            // $usernames = [
            //         "shabana" => '(1,4)',//graduation conference ID and Tour ID
            //         "ghulam.ghouse"  => '(2)',// python workshop ID
            //         "zainab.ahmed"   => '(3)',// Mandarin methods ID BCPR
            //     ];
            $condition='';
            if(auth()->user()->group_id != 644){
                $condition = "and lower(E_ENTRY_USER) = '".strtolower($currentuserName)."'";
            }

            $query = "select * from events where 1=1 ". $condition ;
            $events = DB::select($query);
            // dd($events);
            return view('general_events.event_registrations',compact('sessions','schools','departments','facultyCourses','offeredFaculty','events','programs'));

        }
        protected function getRegistrationList(Request $request){
            // dd($request->all());
            $eventCondition = "";
            $currentuserName = auth()->user()->username;
            if(!empty($request->event)){
                $eventCondition = " and er.ER_EVENT_ID = $request->event ";
            }
            $sql = "SELECT
                        e.e_id,
                        e.E_EVENT_NAME,
                        er.er_first_name,
                        er.er_last_name,
                        er.er_email,
                        er.ER_MOBILE,
                        er.ER_ORGANIZATION,
                        er.ER_DEPARTMENT,
                        er.er_participation_type,
                        NVL(sc.sc_challan_status, '-') AS challan_status
                    FROM
                        event_registrations er
                        left join events e on e.e_id = er.ER_EVENT_ID
                        left JOIN student_challan sc ON sc_app_id = er_id and sc_challan_type = 'misc_fee'
                    WHERE
                        er_department not like '%test%'
                        AND er_first_name not like '%test%'
                        AND er_last_name not like '%test%'
                        AND lower(e.E_ENTRY_USER) = '".strtolower($currentuserName)."'
                        $eventCondition
                        --and sc_challan_status = 'PAID'
                        order by ER_EVENT_ID";
                        // dd( $sql);
                $data = DB::select($sql);


            //    dd($data);
            return DataTables::of($data)->toJson();
        }
        // public function deleteEvent(Request $request){
        //     $userId = auth()->user()->username;
        //     $affectedRows =  DB::table('EVENTS')
        //                         ->where('E_ID', $request->mainId)
        //                         ->update([
        //                             'E_STATUS' => 'D',
        //                             'E_ENTRY_USER' => $userId,
        //                         ]);
        //     if ($affectedRows > 0) {
        //         return response()->json([
        //             'success' => true,
        //             'data' => 'Inactive Successfully'
        //     ]);
        //     } else {
        //         return response()->json([
        //             'success' => false,
        //             'data' => 'Some problem occured'
        //         ]);
        //     }

        // }

}
