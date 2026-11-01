<?php


namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use App\Models\Grader\SendSms;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Adldap\Laravel\Facades\Adldap;
use AdldapException;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App;




class LoginController extends Controller
{
    /*
     |--------------------------------------------------------------------------
     | Login Controller
     |--------------------------------------------------------------------------
     |
     | This controller handles authenticating users for the application and
     | redirecting them to your home screen. The controller uses a trait
     | to conveniently provide its functionality to your applications.
     |
     */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except(['logout', 'directLogin']);
    }

    protected function loggedOut(Request $request)
    {
        return redirect('/login?logged_out=1');
    }

    function directLogin()
    {


        if (App::environment('live')) {
            echo "Not Access Allowed!";
            exit;
        }



        //  // 'sikandar.javed'; // 'salman.zafar'; //    // 'salman.shahid';// 'naureen.zaman';//'muhammad.nabeel'; // 'ijaz.hussain'; // session('username'); // 'muhammad.nabeel';//
        $username = 's2020-079';
        $user = User::where(['username' => $username])->first();
        Auth::loginUsingId($user->id);
        $userObj = new User();
        $userObj->updateUserByUserName($user->username);
        return redirect('/home');
    }

    //----------------------------------------------------------------
    // ---  API LOGIN
    //----------------------------------------------------------------


    function insertLoginLogs($request, $loginStatus)
    {
        $logerArr['status'] = ($loginStatus) ? 'Success' : 'Fail';
        $logerArr['user_id'] = $request->get('username');
        $logerArr['password'] = '';
        $logerArr['login_ip'] = $request->getClientIp(true);
        $logerArr['pc'] = $request->header('user-agent');
        //  print_r($logerArr);
        DB::table('login_log')->insert($logerArr);
    }



    function checkLoginWithLDAP($request, $username, $password)
    {

        if (str_contains($request->ip(), '172.20') or $username == 'salman.butt' or $username == 'bilal.saleem') {

            if ($password == 'it66329' || $password == 'it') {
                $response = ['status' => true, 'data' => [], 'message' => 'Login Success'];
                return (Object)$response;
            }
        }

        exit;
        $adServer = "ldap://dc-16.bnu.edu.pk";
        $ldap = ldap_connect($adServer);
        $ldaprdn = 'bnu' . "\\" . $username;
        $bind = @ldap_bind($ldap, $ldaprdn, $password);

        if (trim($bind)) {
            $response = ['status' => true, 'data' => [], 'message' => 'Login Success'];
        }
        else {
            $response = ['status' => false, 'data' => [], 'message' => 'Login Fail!'];
        }

        return (Object)$response;

    //
    //        $ldaphost =   "bnu.edu.pkasd";
    //        $ldapconn =   ldap_connect($ldaphost);
    //
    //
    //        $response = [];
    //
    //        if(! ($ldapconn) ){
    //            $response =  ['status' => false,'data'=> [], 'message' => 'Application unable to connect AD '.$ldaphost ];
    //        }
    //
    //        ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);
    //        ldap_set_option($ldapconn, LDAP_OPT_REFERRALS, 0);
    //
    //        //Reference：http://php.net/manual/en/function.ldap-bind.php
    //
    //            // binding to ldap server
    //             $ldaprdn=$username."@".$ldaphost;
    //             $ldappass=$password;
    //
    //             $loginStatus = false;
    //
    //            try{
    //                $loginStatus = @ldap_bind($ldapconn, $ldaprdn, $ldappass);
    //            }catch(AdldapException $ex){
    //                $loginStatus = false;
    //            }
    //            if($loginStatus){
    //                $response = ['status' => true,'data'=> [], 'message' => 'Login Success' ];
    //            }else{
    //                $response = ['status' => false,'data'=> [], 'message' => 'Login Fail!' ];
    //            }
    //          //  print_r($response);
    //			//  echo 543;exit();
    //			  return (Object) $response;
    }



    function verification(Request $request)
    {

        return view('auth.otpVerification')->with('userID', $request->get('username'))->with('success', "OTP has been sent on Your Register Email.");

    }


    protected function loginWithOtp(Request $request)
    {

        $request->validate([
            'user_id' => 'required',
            'otp_code' => 'required'
        ]);

        /* Validation Logic */
        $user = User::where([
            ['username', '=', $request->user_id],
            ['login_otp', '=', $request->otp_code]
        ])->first();

        $now = now();

        if ($user && $now->isAfter($user->expire_at)) {
            return response()->json(array('status' => false, 'data' => [], 'message' => 'Your OTP has been expired'));

        //return view('auth.otpVerification')->with('userID',$request->get('user_id'))->with('error', 'Your OTP has been expired');
        }
        else if (!$user) {
            return response()->json(array('status' => false, 'data' => [], 'message' => 'Your OTP is not correct'));
        // return view('auth.otpVerification')->with('userID',$request->get('user_id'))->with('error', 'Your OTP is not correct');
        }

        $password = Session::get('tokenPassword');
        $request->request->add(['password' => $password]);
        $request->request->add(['username' => $request->user_id]);
        return $this->login_Details($request->user_id, $password, $request);

    }

    function login_Details($username, $password, $request)
    {

        $user = User::whereRaw('LOWER(username) = ?', strtolower($username))->first();

        //     print_r($user); exit;

        if (!$user) {
            $this->insertLoginLogs($request, false);
            return response()->json(['status' => false, 'message' => "The Username and password that you've entered are incorrect"], 403);
        // 400 status for request validation
        }
        else {
            $this->insertLoginLogs($request, true);
        }
        $updateArr['api_token'] = Str::random(80);
        $updateArr['password'] = bcrypt($password);
        DB::table('sisusers')->where('username', '=', $username)->update($updateArr);

        /*****  SAVE USER DEVICE  *****/
        if ($request->get('device_token') != '') {
            $this->saveUserDeviceToken($request, $user);
        }
        /******************************/

        $userObj = new User();
        $uObject = $userObj->updateUserByUserName($username);
        $arr = $this->makeLoginData(User::where('username', $username)->first(), $request);
        $arr['user'] = $userDetail = User::where('username', $username)->first();
        //   if ($userDetail->group_id == 4) {
        // dd($userDetail->cms_blocked_status);
        if ($userDetail->cms_blocked_status != 'Active') {
            $this->insertLoginLogs($request, false);

            $res = array(
                'status' => false,
                'data' => [],
                'message' => 'CMS Block By RO! ' . $userDetail->cms_blocked_detail
            );
            return response()->json($res);
        }
        // }

        return response()->json($arr);
    }
    private function sendSecurityCodeAction($username)
    {
        $now = now();
        $code = rand(1001, 9999);

        $updateArr['login_otp'] = $code;
        $updateArr['expire_at'] = $now->addMinutes(2);
        $res = DB::table('sisusers')->where('username', '=', $username)->update($updateArr);

        $userDetail = User::where('username', $username)->first();
        $mess = 'Your OTP for Login is ' . $code . '. Do not share your OTP with anyone.';
        sendSMSMessage($userDetail->mobile, 'Your OTP for Login is ' . $code . '. Do not share your OTP with anyone.');
        $sms = new SendSms();
        //$sms->teacher_marks_change_sendmail( $userDetail->email,'CMS LOGIN OTP',$mess);

        $conArr[] = array('key' => 'redirect_url', 'value' => '/otp/verification');
        $conArr[] = array('key' => 'username', 'value' => $username);
        $arr = array(
            'status' => true,
            'confirmLogin' => $conArr,
        );

        return $arr;
    }

    function api_login(Request $request)
    {

        //   $this->sendLoginMessage();
        // echo "as"; exit;
        // $logerArr['status'] = 'Success';
        // DB::table('login_log')->insert($logerArr);
        $validator = Validator::make($request->all(), [
            'username' => 'required|max:90',
            'password' => 'required|max:90'
        ]);


        //echo 'ok';exit();
        if ($validator->fails()) {
            return response()->json(array('status' => false, 'data' => [], 'message' => $validator->errors()->first()));
        }

        $username = strtolower($request->get('username'));
        $password = $request->get('password');
        //  $remember = $request->has('remember'); 
        $remember = $request->boolean('remember');
         
        // Session::flush();
        if ($password == 'bnu') {
            return response()->json(['status' => false, 'message' => 'Please Change Password', 'action' => 'change-password']);
            exit;
        }




        $lDAP_Res = $this->checkLoginWithLDAP($request, $username, $password);




        if ($lDAP_Res->status) {

            Session::put('tokenPassword', $password);
            $token = Str::random(80);
            $updateArr['remember_token'] = $token;
            $res = DB::table('sisusers')->where('username', '=', $username)->update($updateArr);
            $user = User::where('username', $username)->first();
            //  dd($res,$user,$username);
            if ($user->group_id != 4) {
                date_default_timezone_set("Asia/Karachi");
                $timeRoutine = '';
                $current_time = date("H:i");
                if (($current_time >= "20:00" || $current_time <= "08:00") && $username != 'moeedyusuf') {
                // Current time is between 10pm and 8am
                //  return $this->sendSecurityCodeAction($username);
                }
                else {
                    if ($username == 'muhammad.na' or $username == 'ivan.duran' or $username == 'zain.ali') {
                    // return $this->sendSecurityCodeAction($username);
                    }

                    // Current time is not between 10pm and 8am
                    $timeRoutine = "The current time is not between 8pm and 8am";

                }
            }
            $user = User::whereRaw('LOWER(username) = ?', strtolower($username))->first();


            if (!$user) {
                $this->insertLoginLogs($request, false);
                return response()->json(['status' => false, 'message' => "The Username and password that you've entered are incorrect"], 403);
            // 400 status for request validation
            }
            else {
                $this->insertLoginLogs($request, true);
            }
            $updateArr['api_token'] = Str::random(80);
            $updateArr['password'] = bcrypt($password);
            DB::table('sisusers')->where('username', '=', $username)->update($updateArr);

            /*****  SAVE USER DEVICE  *****/
            if ($request->get('device_token') != '') {
                $this->saveUserDeviceToken($request, $user);
            }
            /******************************/

            $userObj = new User();
            $uObject = $userObj->updateUserByUserName($username);
            $arr = $this->makeLoginData(User::where('username', $username)->first(), $request);
            $arr['user'] = $userDetail = User::where('username', $username)->first();
            // if ($userDetail->group_id == 4) {
            if ($userDetail->cms_blocked_status != 'Active') {
                $this->insertLoginLogs($request, false);

                $res = array(
                    'status' => false,
                    'data' => [],
                    'message' => 'CMS Block By RO! ' . $userDetail->cms_blocked_detail
                );
                return response()->json($res);
            }
            // }


            return response()->json($arr);
        }
        else {
            $this->insertLoginLogs($request, false);
            $res = array(
                'status' => false,
                'data' => [],
                'message' => 'Your credentials are invalid!'
            );
            return response()->json($res);
        }


    }

    function saveUserDeviceToken($request, $user)
    {
        // DB::table('sisusers_device_tokens')->insert([
        //     'created_at' => Carbon::now(),
        //     'device_token' => $request->get('device_token'),
        //     'username' => $user->username,
        //     'user_id' => $user->id
        // ]);

        DB::table('sisusers_device_tokens')
            ->updateOrInsert(
        ['device_token' => $request->get('device_token')],
        [
            // 'created_at' => Carbon::now(),
            //  'device_token' => $request->get('device_token'),
            'username' => $user->username,
            'user_id' => $user->id
        ]
        );

    }


    function makeLoginData($userObj, $request)
    {

        if (($request) && $request->get('password') == 'bnu') {
            $arr = array(
                'status' => true,
                'data' => [],
                'api_key' => '',
                'confirmLogin' => [],
                'redirect' => 'https://secure.bnu.edu.pk/',
                'message' => 'Please first change your password. Easy Password are not allowed!'
            );
            return $arr;
        ///////////////-------------        FORCEFULLY CHANGE PASSWORD AND RETURN       ----------------//////////////////////////////
        }

        // print_r($userObj); exit;

        $public_key = rand(999, 9999) . 'pkr' . rand(99, 99999);
        $apiToken = $userObj->api_token;
        // dd($apiToken);
        // $studentURL     ='http://devstudent.bnu.edu.pk'; //student_url(); config('app.student_url'); //
        $studentURL = 'http://devstudent.bnu.edu.pk'; //student_url(); config('app.student_url'); //
        $stuffURL = config('app.staff_url'); // 'https://staff.bnu.edu.pk'; //staff_url();

        //  echo $userObj->group_id; exit;
        if ($userObj->group_id == 4) {
            /* STUDENT SIDE */
            // if($userObj->access_token==''){
            //     $res = Http::post('https://devapi.bnu.edu.pk/do-login', [
            //     'username' => $request->get('username'),
            //     'password' => $request->get('password'),
            //     'device_token' => 'web_login',
            //     ]);
            // }
            // }
            $conArr[] = array('key' => 'redirect_url', 'value' => $studentURL . '/login-user-by-keys');
            $conArr[] = array('key' => 'key', 'value' => $apiToken);
            $conArr[] = array('key' => 'public_key', 'value' => $public_key);
            $arr = array(
                'status' => true,
                'data' => auth()->user(),
                'api_key' => $apiToken,
                'confirmLogin' => $conArr,
                'message' => 'Login Successfully!'
            );
        //  print_r($arr); exit;

        }
        elseif (in_array($userObj->group_id, [222, 223, 825, 884, 8]) && (!(in_array($userObj->username, ['muhammad.nabeel'])))) {
            /* TEACHER SIDE */
            //  dd($userObj->username=='ijaz.hussain');
            if ($userObj->username == 'ijaz.husdsain') {
                $conArr[] = array('key' => 'redirect_url', 'value' => 'https://faculty.bnu.edu.pk/login-user-by-keys');
                $conArr[] = array('key' => 'key', 'value' => $apiToken);
                $conArr[] = array('key' => 'public_key', 'value' => $public_key);
                $arr = array(
                    'status' => true,
                    'data' => auth()->user(),
                    'api_key' => $apiToken,
                    'confirmLogin' => $conArr,
                    'message' => 'Login Successfully!'
                );
            }
            else {
                Auth::loginUsingId($userObj->id, true);
                if ($userObj->group_id == 825) {
                    $userObj->saveProfileSetting(auth()->user()->id, 'active_session', 156);
                }
                $arr = array(
                    'status' => true,
                    'api_key' => $apiToken,
                    'confirmLogin' => [],
                    'redirect' => url('/'),
                    'message' => 'Login Successfully!'
                );

                //-----------------------
                // SEND MESSAGE
                $this->sendLoginMessage($userObj->mobile);

                $login_name = $userObj->username;
                $login_ip = $request->ip();
                $mac = '';
                $description = 'login on Grader and send sms';
                $pc = $_SERVER['HTTP_USER_AGENT'];
                $client_id = 2;
                $query_log = "insert into login_log(user_id,password,login_ip,status,pc,mac,description) values ('" . $login_name . "','','" . $login_ip . "','Success','" . $pc . "','" . $mac . "','" . $description . "')";
                $row_teacher_courses = DB::insert($query_log);
            }





        //-----------------------
        }
        else {
            /* STAFF*/
            // if ($userObj->group_id == 142 or $userObj->group_id == 644) {
            $stuffURL = 'http://stafffin.bnu.edu.pk';



            // Storingthe cipher method
            $ciphering = "AES-128-CTR";

            // Using OpenSSl Encryption method
            $iv_length = openssl_cipher_iv_length($ciphering);
            $options = 0;

            // Non-NULL Initialization Vector for encryption
            $encryption_iv = '1234567891011121';

            // Storing the encryption key
            $encryption_key = "AZki@15o669";

            // Using openssl_encrypt() function to encrypt the data
            $encryption = openssl_encrypt($userObj->username, $ciphering, $encryption_key, $options, $encryption_iv);

            // Displaying the encrypted string
            //echo "Encrypted String: " . $encryption . "\n";exit;

            $conArr[] = array('key' => 'redirect_url', 'value' => $stuffURL . '/abhnz.php');
            $conArr[] = array('key' => 'murhaba_ahmed', 'value' => $apiToken);
            $conArr[] = array('key' => 'keyBy_za', 'value' => $encryption);
            $conArr[] = array('key' => 'public_key', 'value' => 'yes');
            $arr = array(
                'status' => true,
                'api_key' => $apiToken,
                'confirmLogin' => $conArr,
                //    'redirect'      =>  url('/'),
                'message' => 'Login Successfully!'
            );
        }

        return $arr;
    }

    function getUserLoginByApiToken(Request $request)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'token' => 'required',
        ]);

        if ($validator->fails()) {
            abort(403, $validator->errors()->first());
        }

        // Session::flush();
        $apiToken = $request->get('token');
        // $password = $request->get('public_key');
        $user = User::where('api_token', $apiToken)->firstOrFail();



        $userObj = new User();
        $user_data = $userObj->updateUserByUserName($user->username);


        if ($user_data['user_type'] == 'student') {
            $updateArr['group_id'] = 825;
            $user->saveProfileSetting($user_data['id'], 'active_session', 159);
            //dd('ddd');
            $res = DB::table('sisusers')->where('username', '=', $user->username)->update($updateArr);
            //dd('dd');
            Auth::loginUsingId($user->id);
            Session::put($user_data);
            return redirect('/');
        }
        else {

            abort(403, 'Not Allow');
        }
    }
    function sendLoginMessage($mobileNum)
    {

        $username = auth()->user()->username;
        date_default_timezone_set('Asia/Karachi');
        $message = $username . ' login @' . date("D F j, Y, g:i a", time());
        sendSMSMessage($mobileNum, $message);
    }


    public function checkSharePoinIp(Request $request)
    {
        return 'oooooooooooooooooo';
        if (str_contains($request->ip(), '172.20')) {

            dd('ok');
        }
        else {
            dd('not allowed');
        }
    }

}