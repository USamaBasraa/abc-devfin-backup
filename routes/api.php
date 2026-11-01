<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CommonController;
use App\Http\Controllers\Grader\EmpEmployeesController;
use App\Http\Controllers\Grader\CourseAttendanceController;
use App\Http\Controllers\ImageUploadController;
use App\Http\Controllers\GoogleDriveController;




/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/



 Route::post('/upload_img',[ImageUploadController::class,'upload_img']);
    Route::get('/checkusertoken',[ImageUploadController::class,'check_user_token']);

Route::middleware(['auth:api'])->group(function () {
    //--------- hamid work start here ------------
    Route::get('/get_employee_data',[EmpEmployeesController::class,'get_employee_data']);
    Route::get('/ta_rights',[EmpEmployeesController::class,'ta_rights_assign']);
    Route::get('/updateapproval',[EmpEmployeesController::class,'update_TA']);
	
    // ............... hamid work end here...........
});

Route::prefix('/')->group(base_path('routes/procurement/procurement.api.php'));


// Route::group(['domain' => 'grader.bnu.edu.pk'], function(){
    Route::prefix('/')->group(base_path('routes/grader/grader.api.php'));
    Route::prefix('/library')->group(base_path('routes/library/library.api.php'));
// });

Route::prefix('/')->group(base_path('routes/alumni/alumni.api.php'));



// React ---------------------------------------------
Route::get('/get_student_attendance_details',[CommonController::class,'get_student_attendance_details']);
Route::get('/check_route'       ,[CommonController::class,'check_route']);
Route::get('/get_current_login_employee',[EmpEmployeesController::class,'get_current_login_employee']);
Route::get('/getdata',[UsersController::class,'index']);
Route::POST('/update/{id}',[UsersController::class,'update']);
Route::get('/edit/{id}',[UsersController::class,'edit']);
Route::POST('/delete/{id}',[UsersController::class,'delete']);

Route::get('/get_offered_courses',[CommonController::class,'get_offered_courses']);
Route::get('/create_attendance'  ,[CourseAttendanceController::class, 'create_atendance_by_api']);//->name('attendance');
Route::get('/get_teachers'       ,[CommonController::class,'get_teachers']);
Route::get('/token_verification'       ,[CommonController::class,'check_token']);

Route::get('/get_cms_categories',[EmpEmployeesController::class,'get_categories']);

Route::get('/addNetFaculty',[EmpEmployeesController::class,'addNetFaculty']);

Route::get('/getsessions',[CommonController::class,'sessions']);
Route::get('/getschools',[CommonController::class,'schools']);
Route::get('/getdepartments/{school_id}',[CommonController::class,'departments']);
Route::get('/getteachers/{session_id}/{school_id}/{dept_id}',[CommonController::class,'teachers_list_by_session']);
Route::get('/coursesdetails/{id}',[CommonController::class,'courses_offered_by_session']);
Route::get('/courses_by_school_id/{session_id}/{school_id}',[CommonController::class,'courses_offered_by_session_and_school']);
Route::get('/courses_by_dept_id/{session_id}/{school_id}/{dept_id}',[CommonController::class,'courses_offered_by_session_and_school_and_department']);
Route::get('/get_time_slots/{co_id}',[CommonController::class,'get_time_slots']);
Route::get('/get_students_from_time_slots/{tt_id}/{co_id}',[CommonController::class,'get_students_from_time_slots']);
Route::POST('/update_course_attendance/{id}',[CommonController::class,'update_course_attendance']);


Route::POST('/gd-file-upload', [GoogleDriveController::class, 'uploadFileGD']);




