<?php

namespace App\Http\Controllers;


use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Grader\EmpEmployeesController;
use DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
       $this->middleware('auth')->except(['custom_login','custom_login_admission']);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(){
         return view('home');
    }


    function confirmWelcomeNoteRead(Request $request){
        // print_r($request->all()); exit;
        $data = auth()->user()->welcome_notes;
        
        $arr = json_decode($data);
        $newArr = array();
        if(json_last_error() === JSON_ERROR_NONE){
            /// echo "NO Error";
            $newArr     = $arr;
            $newArr[]   = $request->get('note_key');
        }else{
            $newArr[]   = $request->get('note_key');
        }
        $dataJson = json_encode($newArr); 

        User::where('id', auth()->user()->id)->update([ 'welcome_notes' => $dataJson]);

        return response()->json(['status' => true]);


    }

    public function confirmFacultyIncentiveAgreement(Request $request)
    {
        User::where('id', auth()->user()->id)->update([
            'faculty_incentive_agreement' => 1,
        ]);

        return response()->json(['status' => true]);
    }



    public function custom_login($token=0){
        if($token == '0'){
            abort(404, 'Token not found !');
        }

        $user = User::where('remember_token',$token)->first();
        if( $user ){
            Auth::loginUsingId($user->id);
            DB::table('sisusers')->where('id',auth()->user()->id)->update(['remember_token' => $token] );
            
            return redirect('/');
        }else{
            abort(403, 'Token not match. Please referesh staff portal and try again !');
        }
    }

    
    public function custom_login_admission( $token=0,$id ){
       
      
        // $user = $request->get('user'); 
    //    echo $token;exit();
     if(Auth::user()){
         $user = new User();
         $user_id = $user->check_token($token);
        //print_r($user_id);
            // echo $user_id[0]->id.''.Auth::user()->id;exit();
         
             if($user_id[0]->id == Auth::user()->id){
                 $loginUser  = Auth::user();
                 $emp_user   = $loginUser::find($user_id[0]->id)->employees;

             }else{
             Auth::logout();
             $user = new User();
             $user_id = $user->check_token($token);
             
             Auth::loginUsingId($user_id[0]->id,true);
                 $loginUser = Auth::user();
                 $emp_user = $loginUser::find($user_id[0]->id)->employees;

         }
         
     }else{
         Auth::logout();
         $user       = new User();
         $user_id    = $user->check_token($token);
         Auth::loginUsingId($user_id[0]->id,true);
         $loginUser = Auth::user();
         $emp_user = $loginUser::find($user_id[0]->id)->employees;  
     }

     $empCont = new EmpEmployeesController();
     $dept_hod_status = $empCont->checkHod($emp_user->emp_dept_id)[0]->dept_hod_status;

     $_SESSION["active_session_id"]      ='162';
     $_SESSION["active_session_name"]    ='Fall 202';
     $_SESSION["sisuser_id"]             =$loginUser->id;
     $_SESSION["login_ip"]               =$loginUser->login_ip;
     $_SESSION["emp_group_id"]           =$loginUser->group_id;
     $_SESSION["remember_token"]           =$token;
     $_SESSION["emp_school_id"]          =$emp_user->emp_school_id;
     $_SESSION["emp_dept_id"]            =$emp_user->emp_dept_id;
     $_SESSION["emp_user_name"]          =$emp_user->emp_f_name." ".$emp_user->emp_l_name;
     $_SESSION["emp_image"]              =$emp_user->emp_imagefile;
     $_SESSION["emp_user_id"]            =$emp_user->employee_id;
     $_SESSION["emp_type"]               =$emp_user->emp_type;
     $_SESSION["dept_hod_status"]        =$dept_hod_status;
     $_SESSION["phone_no"]             =$emp_user->emp_contact_no;
      $_SESSION["emp_email"]               =$emp_user->emp_email;
    //  print_r($emp_user); exit();
    //echo $emp_user->emp_contact_no ;exit();
  
 
    //    return view('welcome_grader_exam',['adm_sch_id'=>$id ]);  
    return view('grader.exam.admission_exam_header',['adm_sch_id'=>$id ]);  
    
 }

   
}
