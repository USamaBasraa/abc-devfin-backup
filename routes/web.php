<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\GoogleLoginController;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Controllers\Grader\FacultyCoursesController;
use App\Http\Controllers\Grader\NotificationController;
use App\Http\Controllers\Grader\LectureScheduling\TimeTableController;
use App\Http\Controllers\Grader\Chat\ChatController;
use App\Http\Controllers\CommonController;
use App\Http\Controllers\SideBarController;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Messaging\CloudMessage;
use App\Http\Controllers\Grader\vcReports\VcReportsController;
use App\Http\Controllers\Grader\Reports\SurveyReportController;
use App\Http\Controllers\Grader\Eportal\EportalController;
use App\Http\Controllers\ACL\SubscriptionMasterController;
use App\Http\Controllers\ACL\StaffSubscriptionController;
use App\Http\Controllers\ACL\SubscriptionsController;
use App\Http\Controllers\ACL\SportsApplicantsController;
use App\Http\Controllers\ACL\RfidStudentSubscriptionUtilityController;
use App\Http\Controllers\StudentFinancial\GenerateSingleChallanController;
use App\Http\Controllers\StudentFinancial\MeritCalculationController;

use App\Http\Controllers\LDAPUserController;
use App\Http\Controllers\GoogleDriveController;


Route::get('/createUser',  [LDAPUserController::class, 'createUser'] );


//Route::post('/create-user', 'LDAPUserController@createUser');
// use App\Http\Controllers\TestController;

//Route::get('/fcm', function () {
//
//    $title = 'My Notification Title';
//    $body = 'My Notification Body';
//    $imageUrl = 'http://lorempixel.com/400/200/';
//    $message = CloudMessage::new();
//    $messaging = app('firebase.messaging');
//    //
//
//    $notification = Notification::fromArray([
//        'title' => $title,
//        'body' => $body,
//        'image' => $imageUrl,
//    ]);
//    // $notification = Notification::create($title, $body);
//    // $message = $message->withNotification($notification);
//
//    $deviceToken = 'dOeFN2oBTByPT0bpO7CxrN:APA91bHz_da8BTpPopS7oH8nJQQ5NQOkfLmau2atYWQDGN1BkFxWXfxRrTImde6gzOlLzopLVsApZ3T787f9i7bam1yuw7uWms0kNSrKyKJ-yEp6Pcmuzg9UBEdaEifJ6smlgirLpsrS';
//
//$message = CloudMessage::withTarget('token', $deviceToken)
//    ->withNotification($notification) // optional
//    ->withData(['name' => 'Ahmed Shabbir', 'age' => '30']) // optional
//;
//
//// $message = CloudMessage::fromArray([
////     'token' => $deviceToken,
////     'notification' => [/* Notification data as array */], // optional
////     'data' => [/* data array */], // optional
//// ]);
//
//$messaging->send($message);
//
//});
//Route::get('/fcm',  [ChatController::class, 'fcm'] )->name('fcm');

Route::get('/fcm', function(){
return view('grader.teacher.add_notification');
});

Route::get('/farhan', function(){
    return 'hi farhan';
    });

// testing route for local storage testing






    Route::get('/addNotifications', [NotificationController::class, 'index'])
    ->name('addNotifications');

Route::post('/notifications', [NotificationController::class, 'store'])
    ->name('notifications.store');

Route::put('/notifications/{notification}', [NotificationController::class, 'update'])
    ->where('notification', '[0-9]+')
    ->name('notifications.update');

Route::patch('/notifications/{notification}/toggle', [NotificationController::class, 'toggle'])
    ->where('notification', '[0-9]+')
    ->name('notifications.toggle');

Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy'])
    ->where('notification', '[0-9]+')
    ->name('notifications.destroy');

Route::patch(
    '/notifications/read-all',
    [NotificationController::class, 'markAllAsRead']
)->name('notifications.readAll');

 Route::patch(
    '/notifications/{notification}/read',
    [NotificationController::class, 'markAsRead']
)->name('notifications.read');





    Route::get('/addNotifications', [NotificationController::class, 'index'])
    ->name('addNotifications');

Route::post('/notifications', [NotificationController::class, 'store'])
    ->name('notifications.store');

Route::put('/notifications/{notification}', [NotificationController::class, 'update'])
    ->where('notification', '[0-9]+')
    ->name('notifications.update');

Route::patch('/notifications/{notification}/toggle', [NotificationController::class, 'toggle'])
    ->where('notification', '[0-9]+')
    ->name('notifications.toggle');

Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy'])
    ->where('notification', '[0-9]+')
    ->name('notifications.destroy');

Route::patch(
    '/notifications/read-all',
    [NotificationController::class, 'markAllAsRead']
)->name('notifications.readAll');

 Route::patch(
    '/notifications/{notification}/read',
    [NotificationController::class, 'markAsRead']
)->name('notifications.read');


Route::get('/unauthorized', function () {
    return view('errors.404'); // You can create this view
})->name('unauthorized');

Route::middleware(['auth', 'verify.group.access'])->group(function () {
    Route::get('/finance/generate-single-challan', [GenerateSingleChallanController::class, 'index'])->name('finance/generate-single-challan');

});
Route::get('/ls', function(){
    return view('localstorage');
});
Route::get('/sidebardata',  [SideBarController::class, 'sidebardata'] );



    Route::get('/getDataajax', function(Request $request){
       // return $request->get('token');



      // $sqlll = "select * from car_pooling where lower(cp_vehical_no) like '%".strtolower($request->get('carno'))."%'";
Route::middleware(['auth'])->group(function () {
    Route::get('/finance/merit-calculation',
        [MeritCalculationController::class, 'index'])
        ->name('finance.merit-calculation');

    Route::post('/finance/merit-calculation/calculate',
        [MeritCalculationController::class, 'calculate'])
        ->name('finance.merit-calculation.calculate');
});

        $sqlll = "select * from groups_module_pages
        where gmp_group_id = ".$request->get('token')."
        order by gmp_module_name desc";
        $users = DB::select($sqlll);
        return json_encode($users);
//        return json_decode(json_encode($users), true);
        exit();
        $menuItems = [];
        foreach($users as $user){
            //$menuItems[] = $user->gmp_module_name;
            $menuItem = [
                'title' => $user->gmp_module_name,
                'link' => '#',
            ];
            $submenuItem = [
                'title' => $user->gmp_module_name,
                'link' => '#',
              ];

              // Add the submenu item to the submenu array
              $submenu[] = $submenuItem;
            $menuItem['submenu'] = $submenu;

    // Add the menu item to the menu items array
            $menuItems[] = $menuItem;

        }
        //$menuItems[] = $menuItem;
        //        '".$request->input('token')."'
       // return $menuItems;
        return json_decode(json_encode($users), true);
//print_r($myArrayJSON);
        });



Route::get('/clear', function () {
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('config:cache');
    Artisan::call('view:clear');
    return "Cleared!";
});

Route::get('/filedownload',function (Request $request){
   $files= Storage::disk("google")->allFiles();
   $filename=$files[0];
   $url= Storage::disk('google')->url($filename);
   dump("Download url ",$url);
    dd($files);
});

Route::get('/delete',function (Request $request){
    $details=Storage::disk("google")->directories();
    dd(Storage::disk("google")->url('1jUxOqSxSmBCbumhwsiboePmkwtLTNSQN')->store(' ','hello'));
    $direct=Storage::disk('google')->directories('1jUxOqSxSmBCbumhwsiboePmkwtLTNSQN');
    $ee=Storage::disk("google")->directories('1KxWZ1T6uOvzT0KxWaouxk3yz6zj_ELHK')->put('al.txt','hello ');
        dump("Download url ",$dir);
        dd($ee);
    $delete = Storage::disk('google')->allfiles();
 });

Route::get('/glogin',  [TimeTableController::class, 'googleLogin'] )->name('glogin');

Route::post('/fileupload_google',  [TimeTableController::class, 'uploadFile'] )->name('fileupload_google');
Route::post('/do-login',  [LoginController::class, 'api_login'] );
Route::get('/relative', function () {
    return View::make('home')->with('id', 2);
    return view('home', ['name' => 'user1']);
});

Route::get('university-leaving-form/{id}', [EportalController::class,'showUniversityLeavingForm']);




Route::prefix('google')->group(function () {

    Route::get('/auth/redirect', function () {

        if(isset(auth()->user()->id)){
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return Socialite::driver('google')->with(['hd' => 'bnu.edu.pk'],["prompt" => "select_account"])->redirect();
        }else{
            return Socialite::driver('google')->with(['hd' => 'bnu.edu.pk'], ["prompt" => "select_account"])->redirect();
        }
    });
    Route::get('/auth/callback', [GoogleLoginController::class, 'loginWithGoogle']);
});

Route::post('upload-file',[GoogleLoginController::class,'uploadFileUsingAccessToken']) ;

Route::get('/dlogin', [LoginController::class, 'directLogin']);
Auth::routes(); 

  

Route::middleware(['auth'])->group(function () {

    Route::get('/', [FacultyCoursesController::class, 'retrieveDashboardCourses2']);
    Route::get('/home',  [FacultyCoursesController::class, 'retrieveDashboardCourses2'] )->name('home');
    Route::get('/notificationDetail/{id}',[FacultyCoursesController::class, 'notificationDetail'])->name('notificationDetail');
    Route::get('/allNotification',[FacultyCoursesController::class, 'allNotifications'])->name('allNotification');
    Route::get('/addNotification',[FacultyCoursesController::class, 'addNotifications'])->name('addNotification');
    
    Route::get('survey_report', [SurveyReportController::class, 'surveyReport']);

    Route::controller(SubscriptionMasterController::class)->group(function () {
        Route::get('subscription-master-form', 'subscriptionMasterForm')->name('subscription-master-form');
        Route::post('add-subscription-plan', 'addSubscriptionPlan')->name('add-subscription-plan');
        Route::post('delete-subscriptionPlan', 'deleteSubscriptionPlan')->name('delete-subscriptionPlan');
        Route::post('get-subscriptionPlan-list', 'getSubscriptionPlanList')->name('get-subscriptionPlan-list');
    });
    Route::controller(SubscriptionsController::class)->group(function () {
        Route::get('user-subscriptions', 'userSubscriptions')->name('user-subscriptions');
        Route::get('staff-subscriptions', 'staffSubscriptions')->name('staff-subscriptions');
        Route::post('update-subscription', 'updateSubscription')->name('update-subscription');
        Route::post('update-subscription-challan', 'updateSubscriptionChallan')->name('update-subscription-challan');
        Route::post('get-subscription-list', 'getSubscriptionList')->name('get-subscription-list');
        Route::post('get-staffSubscription-list', 'getStaffSubscriptionList')->name('get-staffSubscription-list');
        Route::post('delete-subscription', 'deleteSubscription')->name('delete-subscription');
    });
    Route::controller(RfidStudentSubscriptionUtilityController::class)->group(function () {
        Route::get('rfid-student-subscriptions', 'index')->name('rfid-student-subscriptions.index');
        Route::post('rfid-student-subscriptions/list', 'list')->name('rfid-student-subscriptions.list');
         Route::post('rfid-student-subscriptions/update-status', 'updateStatus')->name('rfid-student-subscriptions.update-status');
        Route::post('rfid-student-subscriptions/bulk-sync/start', 'startBulkSync')->name('rfid-student-subscriptions.bulk-sync.start');
        Route::post('rfid-student-subscriptions/bulk-sync/step', 'processBulkSyncStep')->name('rfid-student-subscriptions.bulk-sync.step');
        Route::post('rfid-student-subscriptions/bulk-sync/stop', 'stopBulkSync')->name('rfid-student-subscriptions.bulk-sync.stop');
        Route::post('rfid-student-subscriptions/gate-machine-check/start', 'startGateMachineCheck')->name('rfid-student-subscriptions.gate-machine-check.start');
        Route::post('rfid-student-subscriptions/gate-machine-check/step', 'processGateMachineCheckStep')->name('rfid-student-subscriptions.gate-machine-check.step');
        Route::post('rfid-student-subscriptions/gate-machine-check/stop', 'stopGateMachineCheck')->name('rfid-student-subscriptions.gate-machine-check.stop');
    });
  
    Route::controller(SportsApplicantsController::class)->group(function () {
        Route::get('sports-applicant-form', 'sportsApplicantForm')->name('sports-applicant-form');
        Route::get('auto-search-student', 'autoSearch')->name('auto-search-student');
        Route::post('search-student','searchStudent')->name('search-student');
        Route::post('add-sports-applicant','addSportsApplicant')->name('add-sports-applicant');
        Route::post('delete-sport-applicant', 'deleteSportApplicant')->name('delete-sport-applicant');
        Route::post('get-sport-applicant-list', 'getSportApplicanLlist')->name('get-sport-applicant-list');
        Route::post('check-challan', 'checkChallan')->name('check-challan');
    });

    Route::controller(StaffSubscriptionController::class)->group(function () {
        Route::get('staff-subscription-form', 'staffSubscriptionForm')->name('staff-subscription-form');
        Route::post('add-staff-subscription', 'addStaffSubscription')->name('add-staff-subscription');
        Route::post('get-staff-subscription-list', 'getStaffSubscriptionList')->name('get-staff-subscription-list');
    });

    // SQL Query Routes
    Route::get('/sql-query', [App\Http\Controllers\SqlQueryController::class, 'index'])->name('sql-query.index');
    Route::post('/sql-query/execute', [App\Http\Controllers\SqlQueryController::class, 'executeQuery'])->name('sql-query.executeQuery');
});

Route::prefix('/')->group(base_path('routes/grader/grader.web.php'));

Route::group(['domain' => 'staff.bnu.edu.pk'], function(){ });
// Route::group(['domain' => 'library.bnu.edu.pk'], function(){
//     Route::prefix('library1')->group(base_path('routes/library/library.web.php'));
// });


// Route::get('testapi', function(){
//     return 'Pakistan';
// });
// Route::get('testapi', [TestController::class,'testAPI']);


Route::prefix('/')->group(base_path('routes/procurement/procurement.web.php'));


Route::prefix('library')->group(base_path('routes/library/library.web.php'));
Route::prefix('/')->group(base_path('routes/alumni/alumni.web.php'));

Route::get('/{path}', function () {

    return view('welcome');
 }
 )->where('path','.*');


Route::get('/build',function(){
    return 'Pakistan';
});









