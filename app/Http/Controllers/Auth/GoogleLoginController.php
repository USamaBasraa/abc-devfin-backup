<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\SendSms;
use App\Http\Controllers\Grader\EmpEmployeesController;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Symfony\Component\HttpFoundation\Cookie;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use DB;

use Illuminate\Http\Response;
use App\Exceptions\InvalidOrderException;
use Google\Service\AdMob\App;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

// https://stackoverflow.com/questions/12909332/how-to-logout-of-an-application-where-i-used-oauth2-to-login-with-google
// https://towardsdatascience.com/8-common-data-structures-every-programmer-must-know-171acf6a1a42
class GoogleLoginController extends Controller
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
    public $image = '';
    protected $cookieKey = 'bnu';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(){
        $this->middleware('guest')->except(['logout', 'loginWithGoogle', 'new_logout', 'loginWithApiToken', 'checkDeviceVarification', 'checkDevoiceToken']);
    }


    function new_logout(Request $request){

        $loginWith = session('loginWith');

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->flush();
        $request->session()->regenerate();
        $request->session()->regenerateToken();
        
        if($loginWith == 'google'){
            return redirect('https://myaccount.google.com/');
        }else{
            return redirect('/login')->withSuccess(['Logout successfully!']);
        }
        
    }


    function checkDeviceVarification($user){
        $key             =  $this->cookieKey . md5($user->id);
        $deviceCookie    =  request()->cookie($key);
        if (($deviceCookie != '') && ($deviceCookie == $user->username)) {
            return true;
        } else {
            return false;
        }
    }


    function checkDevoiceToken(Request $request){
      //  echo "as"; exit;

        $username   = session('username'); // 'ijaz.hussain';
        $user       = User::where(['username' => $username])->first();

        if ($this->checkDeviceVarification($user)) {
            // auth()->logoutOtherDevices($request, $user);
            // Auth::logoutOtherDevices();

            // auth()->user()->group_id
            // getLoginUser('group_id');

            if (!($user->updated_at)) {
              //  $this->updateInfo($user->id);
            }
            Auth::loginUsingId($user->id);
            return redirect('/');
        } else {
            if (session('mobile_code') != '') {
                // send code
               //  $code =  $this->sendSecurityCode();
            }
            $code =  $this->sendSecurityCodeAction();
            return view('auth.device_verification', ['code' => $code['code']]);
        }
    }

    function submitSecurityCode(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'mobile_code' => [
                'required',
                function ($attribute, $value, $fail) {
                    if ($value != session('mobile_code')) {
                        $fail('Token not match.');
                    }
                }
            ]
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first('mobile_code')]);
        }

        $username   = session('username'); //'ijaz.hussain';
        //  $username = 'ijaz.hussain';
        $code       = $request->get('mobile_code');

        if ($code == session('mobile_code')) {
            $user = User::where(['username' => $username])->first();
            // if(!($user->updated_at)){
            //     $this->updateInfo($user->id);
            // }
            // Auth::loginUsingId($user->id);


            $userObj = new User();
            $userObj->updateUserByUserName($user->id);


            $key = $this->cookieKey . md5($user->id);
            $cookie = cookie($key, $username, 15);
            return response(['status' => true, 'message' => 'Code Match And Login Successfully'])->cookie($cookie);
        }
    }



    function sendSecurityCodeAction(){

        $username   = session('username');
        $userObj    = new User();
        $userData   = $userObj->updateUserByUserName($username);
        $code       = rand(1001, 9999);
       //  echo $userData['mobile']; exit;
        if($userData['mobile']){
            // send code
            session(['mobile_code' => $code]);
            $sms        = new SendSms();
            $message    = 'Security Code '.$code.' is use for CMS';
           //  $sms->send_sms($message,$userData['mobile']);
            return array('code' => $code, 'mobile' => $userData['mobile']);
        }else{
            return false;
        }




    }

    function sendAgainSecurityCode(Request $request){
        $data = $this->sendSecurityCodeAction();
        return response(['status' => true, 'message' => 'New Security send to your mobile Number.', 'data' => $data]);
    }




    function loginWithGoogle(Request $request){
            //  try {
            $googleUser = Socialite::driver('google')->stateless()->user();
            //  $username   = str_replace('@bnu.edu.pk', '', $googleUser->email);
            $user       = User::whereRaw( " lower(email) = '".$googleUser->email."' ")->first();
              if ( isset($googleUser->user['hd']) && ($googleUser->user['hd'] == 'bnu.edu.pk') && ($user)) {
                  $user->access_token=$googleUser->token;
                  $user->save();
                  session(['loginWith' => 'google']);
                  $userObj = new User();
                  $userObj->updateUserByUserName($user->username);
                  
  
                  $studentURL = config('app.student_url');
                  $stuffURL = config('app.staff_url');
                  $user = User::where('username',$user->username)->first();
                  if($user->group_id == 4){
                      return redirect($studentURL.'/login-user-by-keys?key='.$user->api_token);
                  }elseif(($user->group_id == 222) || ($user->group_id == 223)){
                      session(['username' => $user->username]);
                      Auth::loginUsingId($user->id);
                      return redirect('/');
                  }else{
                      return redirect($stuffURL.'/yaraheem_yakareem786.php?public_key='.$user->api_token.'&key='.$user->api_token.'&murhaba_ahmed='.$user->api_token);
                  } 
              } else {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->flush();
                $request->session()->regenerate();
                $request->session()->regenerateToken();
                 return redirect('/login')->withErrors(['Error ', 'Your Email address '. $googleUser->email. ' is not register or conflict with CMS. Please Discuss with ITRC. Or call 0301-7169678']);
              }
          // } catch (Exception $e) {
          //     echo 'Message: ' . $e->getMessage();
          //     exit;
          // }
      } // fun end


    function loginWithApiToken($token = ''){
        if ($token == '') {
            return 'Token Not Found!';
        }
        $user = User::where(['api_token' => $token])->first();
        if ($user) {
          //   $this->updateInfo($user->id);
            Auth::loginUsingId($user->id);
            return redirect('/');
        }
        return 'No User Found!';
    }
}
