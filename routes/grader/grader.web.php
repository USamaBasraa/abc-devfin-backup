<?php

#------ OLD WEB CONTROLLER
use Illuminate\Http\Request;
use App\Http\Controllers\Grader\CourseMarksSummaryReportController;
use App\Http\Controllers\Grader\UniversityEvent\UniversityEventController;
use App\Http\Controllers\Grader\ShortCourses\ShortCoursesController;
use App\Http\Controllers\Grader\VisitingFacultyController;
use App\Http\Controllers\Grader\FacultyCoursesController;
use App\Http\Controllers\Grader\ReportgeneratorController;
use App\Http\Controllers\Grader\ReportsController;
use App\Http\Controllers\Grader\CourseAssessmentMarksController;
use App\Http\Controllers\Grader\AcademicSessionController;
use App\Http\Controllers\Grader\CoursesOfferedController;
use App\Http\Controllers\Grader\Notification\NotificationController;
use App\Http\Controllers\Grader\Exam\ExamPaperController;
use App\Http\Controllers\Grader\Exam\OnlineCoursebankController;
use App\Http\Controllers\Grader\OnlineExamController;
use App\Http\Controllers\Grader\AttendanceManagementController;
use App\Http\Controllers\Grader\DepartmentController;
use App\Http\Controllers\Grader\NewOnlineExamController;
use App\Http\Controllers\Grader\CourseCriteriasController;
use App\Http\Controllers\Grader\CourseCriteriaDetailController;
use App\Http\Controllers\Grader\HodReportController;
use App\Http\Controllers\Grader\GradingController;
use App\Http\Controllers\Grader\AddQuestionController;
use App\Http\Controllers\Grader\LectureController;
use App\Http\Controllers\Grader\Faculty\FacultyEvaluationController;
use App\Http\Controllers\Grader\Faculty\FacultyReportsController;
use App\Http\Controllers\Grader\AssessmentController;
use App\Http\Controllers\Grader\CourseContentController;
use App\Http\Controllers\Grader\CourseAttendanceController;
use App\Http\Controllers\Grader\LectureScheduling\StudyMaterialController;
use App\Http\Controllers\Grader\LectureScheduling\AssignmentsController;
use App\Http\Controllers\Grader\LectureScheduling\EvaluationController;
use App\Http\Controllers\Grader\LectureScheduling\TimeTableController;
use App\Http\Controllers\Grader\Stream\StreamController;
use App\Http\Controllers\Grader\Chat\ChatController; 
use App\Http\Controllers\Auth\GoogleLoginController;
use App\Http\Controllers\ACL\RfidSetupController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Grader\Reports\ExamReportController;
use App\Http\Controllers\Grader\EnrollmentStartStopController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Grader\Financial\StundentsFinancialController;
use App\Http\Controllers\Grader\Financial\Accounts\FinancialAccountsController;
use App\Http\Controllers\Grader\StudentRecord\StudentRecordController;
use App\Http\Controllers\Grader\StudentRecord\StudentRecordRevisedController;
use App\Http\Controllers\Grader\TA\DashBoardController;
use App\Models\Grader\StudentFinancial;
use App\Http\Controllers\testingController;
use App\Http\Controllers\Grader\vcReports\VcReportsController;
use App\Http\Controllers\Grader\LaptopMaintenanceController;
use App\Http\Controllers\Grader\StaffOvertimeController;
use App\Http\Controllers\Grader\EmpEmployeesController;
use App\Http\Controllers\CarPullingController;
use App\Http\Controllers\SideBarController;
use App\Http\Controllers\Grader\TaskManagement\TaskManagementController;
use App\Http\Controllers\Grader\Eportal\EportalController;
use App\Http\Controllers\Grader\Eportal\masterForm\EportalCategoryController;
use App\Http\Controllers\Grader\Eportal\EportalMessageController;
use App\Http\Controllers\Grader\Eportal\masterForm\EportalPermissionController;
use App\Http\Controllers\Grader\Eportal\VCePortalController;
use App\Http\Controllers\Grader\Eportal\eportalRequest\EportalRequestController;
use App\Http\Controllers\Grader\Eportal\AdminePortalController;
use App\Http\Controllers\Grader\Reports\ReportsGeneratorController;
use App\Http\Controllers\Grader\StudentFines\StudentFineController;
use App\Http\Controllers\hr\StaffExitInterviewController;
use App\Http\Controllers\Grader\Eportal\EportalHolidaysController;
use App\Http\Controllers\Grader\BNUReportsGenerate\BNUReportsGeneratorController;
use App\Http\Controllers\Grader\BNUReportsGenerate\BI\BIReportController;
use App\Http\Controllers\StudentFinancial\FeeStructureController;
use App\Http\Controllers\StudentFinancial\GenerateBulkChallanController;
use App\Http\Controllers\StudentFinancial\GenerateCreditHrBulkChallanController;
use App\Http\Controllers\StudentFinancial\StudentCreditsDataController;
use App\Http\Controllers\StudentFinancial\EnrollmentSummaryReportController;
use App\Http\Controllers\StudentFinancial\GenerateConvocationBulkChallanController;
use App\Http\Controllers\StudentFinancial\FeeInstallmentController;
use App\Http\Controllers\StudentFinancial\MasterInstallmentController;
use App\Http\Controllers\StudentFinancial\MasterScholarshipController;
use App\Http\Controllers\StudentFinancial\ScholarshipCriteriaController;
use App\Http\Controllers\StudentFinancial\ScholarshipPolicyController;
use App\Http\Controllers\StudentFinancial\AdmissionScholarshipCriteriaController;
use App\Http\Controllers\StudentFinancial\AdmissionIndividualScholarshipCriteriaController;
use App\Http\Controllers\StudentFinancial\FeeAdjustmentController;
use App\Http\Controllers\StudentFinancial\MiddlewareChallanRectificationController;
use App\Http\Controllers\StudentFinancial\FeePolicyController;
use App\Http\Controllers\StudentFinancial\ScholarshipAssignController;
use App\Http\Controllers\StudentFinancial\ScholarshipFormController;
use App\Http\Controllers\StudentFinancial\FeeWaiveOffController;
use App\Http\Controllers\StudentFinancial\ScholarshipAssignApprovalController;
use App\Http\Controllers\StudentFinancial\AdmissionScholarshipApprovalController;
use App\Http\Controllers\StudentFinancial\GenerateSingleChallanController;
use App\Http\Controllers\StudentFinancial\MeritCalculationController;
use App\Http\Controllers\StudentFinancial\StudentPaidMarkController;
use App\Http\Controllers\StudentFinancial\StudentLedgerController;
use App\Http\Controllers\StudentFinancial\ChallanDetailReportController;
use App\Http\Controllers\StudentFinancial\StudentChallanDetailsController;
use App\Http\Controllers\StudentFinancial\HostelAuxiliaryChargesController;
use App\Http\Controllers\StudentFinancial\CourseExemptedController;
use App\Http\Controllers\Grader\Hostel\HostelLeaveController;
use App\Http\Controllers\Grader\PeerMentorship\StudentMentorshipController;
use App\Http\Controllers\Grader\PeerMentorship\PeerMentorshipController;
use App\Http\Controllers\Grader\StudentDocumentsController;
use App\Http\Controllers\Announcements\AnnouncementController;
use App\Http\Controllers\StudentFinancial\HostelFeeStructureController;
use App\Http\Controllers\StudentFinancial\ChallanReportController;
use App\Http\Controllers\StudentFinancial\ChallanSummaryReportController;
use App\Http\Controllers\StudentFinancial\MedicalPremiumReportController;
use App\Http\Controllers\StudentFinancial\DonationMasterController;
use App\Http\Controllers\Grader\BNUReportsGenerate\StudentProfileController;
use App\Http\Controllers\Grader\DeanHonor\DeanHonorController;
use App\Http\Controllers\Grader\Hostel\HostelController;
use App\Http\Controllers\Grader\Hostel\HostelManagementController;
use App\Http\Controllers\Grader\Hostel\HostelSummaryController;
use App\Http\Controllers\Grader\Hostel\HostelBedAssignmentController;
use App\Http\Controllers\survey\surveyController;
use App\Http\Controllers\survey\StaffSurveyController;
use App\Http\Controllers\survey\SurveyAdminController;
use App\Http\Controllers\survey\ProductAssignmentController;
use App\Http\Controllers\survey\ProductEntryController;
use App\Http\Controllers\survey\StudentExchangeController;
use App\Http\Controllers\Grader\antiInspect\AllowedInspectUserController;
use App\Http\Controllers\ErrorLogsController;
use App\Http\Controllers\Grader\ExamSectionController;
use App\Http\Controllers\Grader\PagePermissionsController;
use App\Http\Controllers\Grader\scholarship\NBSFormController;
use App\Http\Controllers\Grader\scholarship\ScholarshipDashboardController;
use App\Http\Controllers\Grader\GeneralEvents\EventsMasterController;
use App\Http\Controllers\Grader\GeneralEvents\EventsRegistrationsController;
use App\Http\Controllers\Grader\Eportal\HodDeanListController;
use App\Http\Controllers\Grader\Hostel\HostelReportController;
use App\Http\Controllers\Grader\Hostel\AdmissionNewApplicantsHostelReportController;
use App\Http\Controllers\UniversityReportController;
use App\Http\Controllers\Grader\CarPoolingAdminController;
use App\Http\Controllers\admission\AdmissionSurveyController;
use App\Http\Controllers\StudentFinancial\ManualScholarshipChallanController;
use App\Http\Controllers\StudentFinancial\MeritProgramMappingController;


Route::view('menu', 'menu');
/*
|--------------------------------------------------------------------------
| Lecture Scheduling
|--------------------------------------------------------------------------
*/

// only task page routes
use App\Http\Controllers\Grader\TaskManagement\TaskController;
use Jenssegers\Agent\Facades\Agent;


// ────── PUBLIC ROUTES NOT PROTECTED INSIDE MIDDLEWARE ──────
// ─── Laravel Auth Routes (login, register, etc.) ───
Auth::routes();
// ─── Custom Login via Admission or Welcome Links ───
Route::get('/admission/{emp_sisuser_id?}/{adm_id?}', [HomeController::class, 'custom_login_admission'])->name('/admission');
Route::get('/welcome/{emp_sisuser_id?}', [HomeController::class, 'custom_login'])->name('/welcome');
// ─── Cache, Config, View Clear (Use with caution, should be protected in production) ───
Route::get('/view-clear', function () {
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    // Artisan::call('config:cache');
    Artisan::call('view:clear');

    return Redirect::to('/');
});
// Displays the server's IP and the client's IP address. This is useful for debugging or testing deployment.
Route::get('/testserverip', [
    'as' => '/testserverip',
    function (Request $request) {
        return $_SERVER['SERVER_ADDR'] . '__ system ip =' . $request->ip(); // View::make('test');
    }
]);
Route::get('/error', [
    'as' => '/error',
    function () {
        return View::make('error');
    }
]);

// Get IP address
Route::get('/mac', function (Request $request) {
    echo \Request::ip();
    // You can enhance this as needed
});

// Settings/Profile View
Route::get('/settings', function () {
    return view('grader.profile.profile');
});
// ─── Serve Images from /public/docs ───
Route::get('images/{filename}', function ($filename) {
    $path = public_path('docs/' . $filename);

    if (!File::exists($path)) {
        abort(404);
    }

    $file = File::get($path);
    $type = File::mimeType($path);

    $response = Response::make($file, 200);
    $response->header("Content-Type", $type);

    return $response;
});

// Serve Protected Images from Storage
Route::get('/abc/{filename}', function ($filename) {
    $path = storage_path('app/protected_images/' . $filename);

    if (!File::exists($path)) {
        abort(404);
    }

    $file = File::get($path);
    $type = File::mimeType($path);

    $response = Response::make($file, 200);
    $response->header("Content-Type", $type);

    return $response;
})->where('filename', '[\w\d\-\_\.]+');


// ────── PUBLIC ROUTES (No Auth Required) ──────
Route::get('sidebar', [SideBarController::class, 'sidebar']);
Route::get('autocomplete', [CarPullingController::class, 'autocomplete']);

// ─── FacultyCoursesController: OTP and Profile Update ───
Route::controller(App\Http\Controllers\Grader\FacultyCoursesController::class)->group(function () {
    Route::post('create-otp', 'createCode')->name('otp.create');
    Route::post('update-details', 'UpdateDetails');
});

// ─── LoginController: OTP Authentication ───
Route::controller(App\Http\Controllers\Auth\LoginController::class)->group(function () {
    Route::post('otp/generate', 'generate')->name('otp.generate');
    Route::post('otp/verification', 'verification')->name('otp.verification');
    Route::post('otp/login', 'loginWithOtp')->name('otp.getlogin');
});
// ─── Logout & Device Token Security ───
Route::get('/new_logout', [GoogleLoginController::class, 'new_logout']);

Route::get('/unauthorized', function () {
    return view('errors.404'); // You can create this view
})->name('unauthorized');
///TEMPORARITLY OUT OF AUTH
// TaskController routes
Route::post('dboard_submit', [TaskController::class, 'store']);
Route::prefix('staff')->group(function () {
    Route::get('task-list', [TaskController::class, 'dashboard']);
    Route::get('fetch_staff_data', [TaskController::class, 'fetchStaffData'])->name('fetch_staff_data');
    Route::get('getGroupUser', [TaskController::class, 'getGroupUser'])->name('getGroupUser');

    Route::get('updateDraftStatus', [TaskController::class, 'updateDraftStatus'])->name('updateDraftStatus');
    Route::get('showPersonalTaskList', [TaskController::class, 'showPersonalTaskList'])->name('showPersonalTaskList');
    Route::get('showImmediateTaskList', [TaskController::class, 'showImmediateTaskList'])->name('showImmediateTaskList');
    Route::get('showTaskList', [TaskController::class, 'showTaskList'])->name('showTaskList');
    Route::get('showTodoTaskList', [TaskController::class, 'showTodoTaskList'])->name('showTodoTaskList');
    Route::get('getGroupTaskEmployee/{id?}', [TaskController::class, 'getGroupTaskEmployee'])->name('getGroupTaskEmployee');
});

Route::get('/staff-satisfaction-survey', [StaffSurveyController::class, 'staffSatisfaction'])->name('staffSatisfaction');
Route::post('/set-staff-satisfaction-survey-flag', [StaffSurveyController::class, 'setStaffSatisfactionSurveyFlag'])->name('set-staff-satisfaction-survey-flag');
Route::post('/submit-staff-satisfaction-survey', [StaffSurveyController::class, 'submitStaffSatisfactionSurvey'])->name('submit-staff-satisfaction-survey');

Route::get('/staff-health-insurance-survey', [StaffSurveyController::class, 'staffHealthInsuranceSurvey'])->name('staffHealthInsuranceSurvey');
Route::post('/set-staff-health-insurance-survey-flag', [StaffSurveyController::class, 'setStaffHealthInsuranceSurveyFlag'])->name('set-staff-health-insurance-survey-flag');
Route::post('/submit-staff-health-insurance-survey', [StaffSurveyController::class, 'submitStaffHealthInsuranceSurvey'])->name('submit-staff-health-insurance-survey');


//PUBLIC ROUTES END


// ────── AUTH-PROTECTED ROUTES ──────
Route::middleware(['auth'])->group(function () {
    // Faculty Goal undertaking agreement (web/session auth — avoids SPA catch-all 405)
    Route::post('/faculty-incentive/save-agreement', [HomeController::class, 'confirmFacultyIncentiveAgreement']);

    // University Event Routes
    Route::prefix('UniversityEvent')->group(function () {
        Route::get('register', [UniversityEventController::class, 'register']);
        Route::get('calendar', [UniversityEventController::class, 'viewInCalendar']);
        Route::get('event-approval', [UniversityEventController::class, 'eventApproval']);
    });

    // Faculty Reports
    Route::get('/faculty/course_offering', [FacultyReportsController::class, 'course_offering']);
    Route::get('/faculty/course_offering_report', [FacultyReportsController::class, 'course_offering_report'])
        ->name('course-offering-report.index');

    // Task Management Routes
    Route::prefix('task-management')->group(function () {
        Route::get('get', [TaskManagementController::class, 'getData']);
        Route::get('getCategories', [TaskManagementController::class, 'getTaskCategory'])->name('getTaskCategory');
    });

    // Staff Task Routes
    Route::prefix('staff')->group(function () {
        Route::get('ProjectBoard', [TaskManagementController::class, 'ProjectBoard']);
        Route::get('task-list2', [TaskManagementController::class, 'dashboard2']);
        Route::post('updateTask', [TaskManagementController::class, 'updateTask'])->name('updateTask');
        Route::get('deleteTask', [TaskManagementController::class, 'deleteTask'])->name('deleteTask');
        Route::get('addDraft', [TaskManagementController::class, 'addDraft'])->name('addDraft');
        Route::post('updateTaskRow', [TaskManagementController::class, 'updateTaskRow'])->name('updateTaskRow');
        Route::get('editTask', [TaskManagementController::class, 'editTask'])->name('editTask');
        Route::get('rollBack', [TaskManagementController::class, 'rollBack'])->name('rollBack');
        Route::get('task-listing', [TaskManagementController::class, 'createTask'])->name('task-listing');
        Route::get('taskBoard', [TaskManagementController::class, 'taskBoard']);
        Route::get('taskBoard2', [TaskManagementController::class, 'taskBoard2']);
        Route::get('get-taskboard', [TaskManagementController::class, 'getTaskBoard'])->name('staff/gettaskboard');
        Route::post('updateTaskAll', [TaskManagementController::class, 'updateTaskAll'])->name('updateTaskAll');
        Route::post('updateTaskAllTodo', [TaskManagementController::class, 'updateTaskAllTodo'])->name('updateTaskAllTodo');
        Route::post('updateTaskAllPersonalTodo', [TaskManagementController::class, 'updateTaskAllPersonalTodo'])->name('updateTaskAllPersonalTodo');
        Route::post('updateTaskAllImmediateTodo', [TaskManagementController::class, 'updateTaskAllImmediateTodo'])->name('updateTaskAllImmediateTodo');
        Route::get('dashboard_demand', [TaskManagementController::class, 'dashboard_demand']);
        Route::get('todo-list', [TaskManagementController::class, 'TodoDashboard']);
        Route::get('personal_todo-list', [TaskManagementController::class, 'personalTodoDashboard']);
        Route::get('immediate_todo-list', [TaskManagementController::class, 'immediateTodoDashboard']);
        Route::post('delete-selected-todo', [TaskManagementController::class, 'deleteSelected'])->name('delete-selected-todo');
        Route::post('send-message-to-user', [TaskManagementController::class, 'sendMsg'])->name('staff/assign-to-message');
        Route::get('get-user-messages', [TaskManagementController::class, 'getUserMessages'])->name('staff/get-user-messages');
        Route::get('tmsortOrder', [TaskManagementController::class, 'tmsortOrder'])->name('tmsortOrder');
        Route::post('update-task-status', [TaskManagementController::class, 'updateTaskStatus'])->name('staff/update-task-status');
    });

    // SharePoint Portal Check
    Route::get('/share-point-portal', [SideBarController::class, 'checkSharePoinIp']);

    // Visiting Faculty and Short Courses
    Route::get('/visiting_faculty', [VisitingFacultyController::class, 'index']);
    Route::get('harassment-prevention', fn() => view('grader.harassment.harassment_prevention'));
    Route::get('/short_courses', [ShortCoursesController::class, 'index']);
    Route::get('/shortCourse_applicants', [ShortCoursesController::class, 'applicants']);

    // Student & Evaluation
    Route::get('verify-student-cgpa/{roll?}/{session?}', [StudentRecordController::class, 'verifyStudentCgpa'])->name('verify-student-cgpa');
    Route::get('teacher/course_evaluation/{id?}', [EvaluationController::class, 'index'])->name('teacher/course_evaluation');
    Route::post('/combine-course', [EvaluationController::class, 'combineCourse'])->name('combine-course');
    Route::get('transfer-marks/{co_id?}/{assess_name?}/{cnum?}', [EvaluationController::class, 'transferMarks'])->name('transfer-marks');

    // General Routes
    Route::post('tododboard_submit', [TaskManagementController::class, 'todostore']);
    Route::post('fetch_staff_data', [TaskManagementController::class, 'fetchStaffData'])->name('fetch_staff_data');
    Route::get('fetch-tasks', [TaskManagementController::class, 'fetchTasks'])->name('fetch-tasks');
    Route::get('fetch_todo_list', [TaskManagementController::class, 'fetch_todo_list']);
    Route::get('/move-to-student', [App\Http\Controllers\Grader\FacultyCoursesController::class, 'redirectUserByApiToken']);
    Route::get('/login-user-by-keys', [LoginController::class, 'getUserLoginByApiToken']);
    Route::post('/set_sesion', [AcademicSessionController::class, 'set_sesion']);
    Route::get('/d-login/{token}', [GoogleLoginController::class, 'loginWithApiToken']);
    Route::get('/search-registered-carByMobile', [LoginController::class, 'mobileLogin']);
    // Admin-only routes (still empty)
    Route::prefix('/')->middleware(['auth:web', 'can:isAdmin,id'])->group(function () {
        // Define admin routes here
    });
    Route::get('/teacher/assessment-marks/{id}/{ass_type}/{num}/{ccd_id}', [CourseAssessmentMarksController::class, 'assessment_marks'])->name('/teacher/assessment-marks');
    // Course-specific protected routes
    Route::match(['get', 'post'], '/todaylectures/{employee_id?}', [App\Http\Controllers\Grader\LectureScheduling\LecturesController::class, 'toDayLecture'])->name('todaylectures');
    Route::middleware(['auth', 'checkcourse'])->group(function () {

        // Assessment Marks Routes

        Route::get('/teacher/update_totalmarks/{ccd_id?}/{marks?}', [CourseAssessmentMarksController::class, 'update_totalmarks'])->name('/teacher/update_totalmarks');
        Route::post('/teacher/marks-save', [CourseAssessmentMarksController::class, 'save_marks'])->name('/teacher/marks-save');

        // Lecture Scheduling Routes

        Route::match(['get', 'post'], '/Lectures/{co_id?}', [App\Http\Controllers\Grader\LectureScheduling\LecturesController::class, 'Lectures_Listing'])->name('Lectures');
        Route::match(['get', 'post'], '/Timetable/{co_id?}', [App\Http\Controllers\Grader\LectureScheduling\TimeTableController::class, 'gettimetable'])->name('Timetable');
        Route::match(['get', 'post'], '/StudentCourseDetail/{co_id?}', [App\Http\Controllers\Grader\LectureScheduling\StudentCourseDetailController::class, 'getStudentDetails'])->name('StudentCourseDetail');
        Route::match(['get', 'post'], '/marksdetail/{co_id?}/{roll_no?}', [App\Http\Controllers\Grader\LectureScheduling\StudentCourseDetailController::class, 'marksdetail'])->name('marksdetail');
        // faculty
        Route::get('reportgenerator/load_faculty/{get_co_id?}', [ReportgeneratorController::class, 'load_faculty'])->name('load_faculty');
        Route::get('faculty_comments_show/{co_id?}', [FacultyReportsController::class, 'faculty_comments'])->name('faculty_comments_show');
        Route::get('faculty_course_detail_report/{co_id}', [FacultyReportsController::class, 'faculty_courseDetail'])->name('faculty_course_detail_report');
        Route::get('teacher/delete_faculity_course_content/{id}/{co_id}', [FacultyCoursesController::class, 'delete_faculity_course_content']);
    });
    Route::post('uploadassessment', [AssessmentController::class, 'uploadPostAssessment'])->name('uploadassessment');

    Route::get('lecture/{co_id}', [LectureController::class, 'lectureWiseView']);
    // Faculty Dashboard & Ramzan Response
    Route::get('/home2', [FacultyCoursesController::class, 'retrieveDashboardCourses2']);
    Route::post('/save-ramzan-response', [FacultyCoursesController::class, 'saveRamzanResponse'])->name('save-ramzan-response');

    // ─── Car Pooling Routes ───
    Route::get('car-pooling', [CarPullingController::class, 'index']);
    Route::get('car_pooling_report', [CarPullingController::class, 'car_pooling_report']);
    Route::get('car-pooling-details', [CarPullingController::class, 'car_pulling_details']);
    Route::get('car-pooling-inbox', [CarPullingController::class, 'carpullingInbox']);
    Route::get('car-registration', [CarPullingController::class, 'carRegistration']);
    Route::post('/deleteCarPooling', [CarPullingController::class, 'deleteCarPooling'])->name('deleteCarPooling');
    Route::get('search-registered-car', [CarPullingController::class, 'searchRegistration']);
    Route::get('direct-registered-car', [CarPullingController::class, 'directRegistration']);
    Route::post('CarPollingStudentDetails', [CarPullingController::class, 'CarPollingStudentDetails']);
    Route::post('deleteStudentCarPooling', [CarPullingController::class, 'deleteStudentCarPooling']);
    Route::get('car-pooling-pdf', [CarPullingController::class, 'carPoolingPdf']);
    Route::get('car-pooling-feedback', [CarPullingController::class, 'pooling_feedback']);
    Route::get('car-pooling-chat', [CarPullingController::class, 'carpullingInboxchat']);

    // ─── Car Pooling Admin Routes ───
    Route::prefix('admin/car-pooling')->group(function () {
        Route::get('dashboard', [CarPoolingAdminController::class, 'dashboard'])->name('admin.car-pooling.dashboard');
        Route::get('area-analytics', [CarPoolingAdminController::class, 'areaAnalytics'])->name('admin.car-pooling.area-analytics');
        Route::get('capacity-report', [CarPoolingAdminController::class, 'capacityReport'])->name('admin.car-pooling.capacity-report');
        Route::get('feedback', [CarPoolingAdminController::class, 'feedbackManagement'])->name('admin.car-pooling.feedback');
    });

    Route::get('car-pooling-search', [CarPullingController::class, 'car_pulling_details'])->name('car-pooling-search');

    // ─── Employee Info ───
    Route::get('get-dept', [EmpEmployeesController::class, 'getDept']);
    // ─── TA (Teaching Assistant) Routes ───
    Route::get('teacher/ta_course_content', [DashBoardController::class, 'ta_course_content'])->name('teacher/ta_course_content');
    Route::match(['get', 'post'], '/taLectures', [DashBoardController::class, 'taLectures_Listing'])->name('taLectures');

    Route::get('teacher/ta_online_exam', [DashBoardController::class, 'taonlineexam'])->name('teacher/ta_online_exam');
    Route::match(['get', 'post'], '/TaStudentCourseDetail', [DashBoardController::class, 'TagetStudentDetails'])->name('TaStudentCourseDetail');
    Route::match(['get', 'post'], '/TaTimetable', [DashBoardController::class, 'Tagettimetable'])->name('TaTimetable');
    Route::get('/ta_assessment_marks_report', [DashBoardController::class, 'ta_course_assessment_report'])->name('/ta_assessment_marks_report');
    Route::get('teacher/tacourse_evaluation', [DashBoardController::class, 'taindex'])->name('teacher/tacourse_evaluation');

    // ─── HOD Report Data Routes ───
    Route::get('/easyui', [HodReportController::class, 'easyui']);
    Route::get('/sessions', [HodReportController::class, 'sessions']);
    Route::get('/shift', [HodReportController::class, 'shift']);
    Route::get('/course_offering', [HodReportController::class, 'course_offering']);
    Route::get('/adm_schedules', [HodReportController::class, 'adm_schedules']);
    Route::get('/schools', [HodReportController::class, 'schools']);
    Route::get('/schoolsloginUser', [HodReportController::class, 'schoolsloginUserOnly']);
    Route::get('/teacher', [HodReportController::class, 'teacher']);
    Route::get('/departments', [HodReportController::class, 'departments']);
    Route::get('/get_departments_by_school', [HodReportController::class, 'departmentbySchool']);
    Route::get('/get_programs_by_school', [HodReportController::class, 'departmentbyPrograms']);
    Route::get('/programs', [HodReportController::class, 'programs']);
    Route::get('/activeschools', [HodReportController::class, 'activeschools']);
    Route::get('/activeprograms', [HodReportController::class, 'activeprograms']);
    Route::get('/studentStatus', [HodReportController::class, 'studentStatus']);
    Route::get('/activedepartments', [HodReportController::class, 'activedepartments']);
    Route::get('/course_offering_exam', [HodReportController::class, 'course_offering_exam']);
    Route::get('/get_top_sessions', [HodReportController::class, 'get_top_sessions']);
    Route::get('/page_name', [HodReportController::class, 'page_name']);

    Route::prefix('devoice-token')->group(function () {
        Route::get('/check', [GoogleLoginController::class, 'checkDevoiceToken']);
        Route::post('/submit', [GoogleLoginController::class, 'submitSecurityCode']);
        Route::get('/resend', [GoogleLoginController::class, 'sendAgainSecurityCode']);
    });

    // ─── Attendance Routes ───
    Route::prefix('attendance')->group(function () {
        Route::get('lecture/{co_id}', [LectureController::class, 'lectureWiseView']);
    });

    // ─── Quick Utility Checks ───
    Route::get('/login-with-id', [App\Http\Controllers\HodReportController::class, 'loginWithID']);
    Route::get('/check-user', [App\Http\Controllers\HodReportController::class, 'checkUser']);

    // ─── Other Public Routes ───
    Route::get('/itrc', 'HomeController@itrc')->name('/itrc');
    Route::get('/logout', 'Auth\LoginController@logout')->name('/logout');
    Route::get('/check_session', 'HomeController@check_login_session')->name('/check_session');
    // ─── System Cache & Config Clear ───
    Route::get('/clear', function () {
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('config:cache');
        Artisan::call('view:clear');
        return "Cleared!";
    });

    Route::prefix('u-notification')->group(function () {
        Route::get('get', [NotificationController::class, 'getNotificationInAjax']);
    });

    Route::get('/user_notifications', function () {
        $petani = DB::table('user_notifications')->get();
        return view('teacher.teacher_dashboard2', ['petani' => $petani]);
    });

    /*
    |--------------------------------------------------------------------------
    | Common Route Groups
    |--------------------------------------------------------------------------
    */
    Route::group(['Common'], function () {

        Route::group(['AcademicSessions'], function () {
            Route::get('sessions/get', [AcademicSessionController::class, 'retrieve'])->name('sessions/get');
        });

        Route::group(['Schools'], function () {
            Route::get('CoursesOffered/getFacultyDepCourses/{sess_id?}/{school_id?}/{employee_id?}/{dept_id?}', [CoursesOfferedController::class, 'retrieveFacultyDepCourses'])->name('CoursesOffered/getFacultyDepCourses');
        });

        Route::group(['Departments'], function () {
            Route::get('departments/get/{id}', [DepartmentController::class, 'retrieve'])->name('departments/get/{id}');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Course Offering & Faculty Course Routes
    |--------------------------------------------------------------------------
    */
    Route::group(['CourseOffering'], function () {

        Route::group(['FacultyCourses'], function () {
            Route::get('facultyCourses/get/{sess_id}/{dept_id}/{email}', [FacultyCoursesController::class, 'retrieve'])->name('facultyCourses/get/{id}');
        });

        Route::group(['CoursesOffered'], function () {
            Route::get('CoursesOffered/getFaculty/{sess_id?}/{school_id?}/{dept_id?}', [CoursesOfferedController::class, 'retrieveFaculty'])->name('CoursesOffered/getFaculty');
            Route::get('CoursesOffered/getCourses/{sess_id?}/{employee_id?}', [CoursesOfferedController::class, 'retrieveCourses'])->name('CoursesOffered/getCourses');
            Route::get('CoursesOffered/getFacultyDepCourses/{sess_id?}/{school_id?}/{dept_id?}/{employee_id?}', [CoursesOfferedController::class, 'retrieveFacultyDepCourses'])->name('CoursesOffered/getFacultyDepCourses');
            Route::get('CoursesOffered/getOfferedCoursesFaculty/{sess_id?}/{school_id?}/{dept_id?}', [CoursesOfferedController::class, 'retrieveOfferedCoursesFaculty'])->name('CoursesOffered/getOfferedCoursesFaculty');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Attendance Management Routes
    |--------------------------------------------------------------------------
    */
    Route::get('attendance/management/grid/{co_id}', [CourseAttendanceController::class, 'retrieveHodAttendance'])->name("attendance/management/grid/");
    Route::get('attendance/management/details/{co_id}/{sai_id}', [CourseAttendanceController::class, 'retrieveAttendanceDetails'])->name("attendance/management/details/");
    Route::post('attendance/management/update', [CourseAttendanceController::class, 'storeAttendanceDetails'])->name("attendance/management/update/");

    /*
    |--------------------------------------------------------------------------
    | 🔹 All Teacher-Specific Routes (Grouped Under /teacher)
    |--------------------------------------------------------------------------
    */
    Route::prefix('teacher')->group(function () {

        // Assessment Approval List
        Route::get('courses/approve/list/{co_id}/{ass_type}/{num}', [CourseAssessmentMarksController::class, 'retrieveApprovalStudentsList'])->name('teacher/courses/students/list/');

        // Course Content & Assignments
        Route::get('delete_course_content/{coc_id?}/{coc_tt_id?}', [FacultyCoursesController::class, 'delete_course_content']);
        Route::get('check_assignment/{course_criteria_detail_id?}/{id?}', [FacultyCoursesController::class, 'check_assignment'])->name('teacher/check_assignment');
        Route::get('course_content/{co_id?}', [CourseContentController::class, 'course_content'])->name('teacher/course_content');
    });

    // Course Content Update (remains outside teacher/ prefix as original)
    Route::post('updtcontent/{coc_id?}', [CourseContentController::class, 'updtcontent'])->name('updtcontent');
    // This route retrieves academic session data, typically relevant to authenticated users (faculty/admin)
    Route::get('/get_top_sessions/{count?}', [AcademicSessionController::class, 'retrieveTopSessions'])->name('/get_top_sessions/{count?}');
    /**
     * ------------------------
     * Attendance Management
     * ------------------------
     */
    Route::prefix('attendance/management')->group(function () {
        Route::get('/', [AttendanceManagementController::class, 'index'])->name("attendance/management");
    });

    /**
     * ------------------------
     * Attendance Marking
     * ------------------------
     */
    Route::prefix('attendance')->group(function () {
        Route::get('attendance_new/{co_id?}', 'Attendance\CourseAttendanceNewController@attendanceNew');
        Route::get('AttendanceLoad/{get_tt_id?}/{co_id?}/{class_status?}', [CourseAttendanceController::class, 'loadAttendance'])->name("attendance/AttendanceLoad");
        Route::get('loadClassDates/{get_co_id?}/{tt_id?}', [CourseAttendanceController::class, 'loadClassSlotDetail'])->name('loadClassDates');
        Route::get('course_attendance_summary/{co_id?}', [CourseAttendanceController::class, 'course_attendance_summary'])->name("course_attendance_summary");
        Route::get('attendance_slot_summary/{co_id?}', [CourseAttendanceController::class, 'attendance_slot_summary'])->name('attendance_slot_summary');

        // Full attendance workflow
        Route::get('attendance/{co_id?}/{slot_id?}/{slot_date?}', [CourseAttendanceController::class, 'index'])->name('attendance/attendance');
        Route::get('/load_studentlist_attendance/{co_id?}/{slot_id?}', [CourseAttendanceController::class, 'load_studentlist_attendance'])->name('load_studentlist_attendance');
        Route::get('/update_student_attendance/{ca_id?}/{status?}', [CourseAttendanceController::class, 'update_student_attendance'])->name('update_student_attendance');
        Route::get('/check_all_absent/{slot_id}', [CourseAttendanceController::class, 'checkAllAbsent'])->name('attendance.check_all_absent');
        Route::get('/final_submit_attendance/{mtt_id?}', [CourseAttendanceController::class, 'final_submit_attendance'])->name('final_submit_attendance');
        Route::post('/update_slot_contents', [CourseAttendanceController::class, 'update_slot_contents'])->name('update_slot_contents');

        Route::post('attendance_post', [CourseAttendanceController::class, 'submitAttendance'])->name("attendance/attendance_post");
        Route::post('attendance_preview', [CourseAttendanceController::class, 'submitAttendancePreview'])->name("attendance/attendance_preview");

        Route::get('student_course_attendance/{co_id?}/{sai_enrollment_no?}/{attendance_type?}', [CourseAttendanceController::class, 'student_course_attendance'])->name("student_course_attendance");
    });
    /**
     * ------------------------
     * Teacher Course Routes
     * ------------------------
     */
    Route::prefix('teacher')->group(function () {
        Route::get('videouploaddrive/{co_id?}/{lec_id?}/{tt_id?}', [StudyMaterialController::class, 'lecture_UploadVideoView'])->name('teacher/videouploaddrive');
        Route::get('coursesold', [FacultyCoursesController::class, 'retrieveDashboardCourses'])->name('teacher/coursesold');
        Route::get('courses', [FacultyCoursesController::class, 'retrieveDashboardCourses2'])->name('teacher/courses');
        Route::get('courses2', [FacultyCoursesController::class, 'retrieveDashboardCourses'])->name('teacher/courses2');
        Route::get('course_discussion/{co_id?}', [FacultyCoursesController::class, 'course_discussion'])->name('teacher/course_discussion');
        Route::get('course_site/{co_id?}', [FacultyCoursesController::class, 'course_site'])->name('teacher/course_site');
        Route::get('course_material/{co_id?}', [StudyMaterialController::class, 'course_material'])->name('teacher/course_material');
        Route::get('course_assignment/{co_id?}', [AssignmentsController::class, 'index']);
        Route::get('group_chat/{co_id?}', [NotificationController::class, 'group_chat'])->name('teacher/group_chat');
        Route::get('wall', [NotificationController::class, 'teacherWall']);

        // Course Assessment
        Route::get('courses/checkCriteria/{co_id}', [CourseCriteriasController::class, 'dashboardCoursesCriteria'])->name('teacher/courses/checkCriteria');
        Route::get('courses/assessment/list/{co_id}/{ass_id}', [CourseAssessmentMarksController::class, 'retrieveAssessmentList'])->name('teacher/courses/assessment/list/');
        Route::get('courses/students/list/{co_id}/{ass_type}/{num}', [CourseAssessmentMarksController::class, 'retrieveStudentsList'])->name('teacher/courses/students/list/');

        Route::post('courses/store/marks', [CourseAssessmentMarksController::class, 'storeMarks'])->name('teacher/courses/store/marks');
        Route::post('courses/marks-enter', [CourseAssessmentMarksController::class, 'marks_enter'])->name('teacher/courses/marks-enter');
        Route::post('courses/store/marksnew', [CourseAssessmentMarksController::class, 'storet'])->name('teacher/courses/store/marksnew');
        Route::post('courses/update_criteria_details/', [CourseAssessmentMarksController::class, 'updateCriteriaDetails'])->name('teacher/courses/update_criteria_details');
        Route::post('courses/finalize_result/', [CourseAssessmentMarksController::class, 'resultFinalization'])->name('teacher/courses/finalize_result/');
        Route::get('courses/delete_marks_summaries/{co_id}', [CourseAssessmentMarksController::class, 'deleteCourseMarksSummaries'])->name('teacher/courses/delete_marks_summaries/');
        Route::post('courses/mark_i_grade', [CourseAssessmentMarksController::class, 'mark_i_grade'])->name('teacher/courses/mark_i_grade');

        // HOD Course Approval
        Route::get('courses/approval', [FacultyCoursesController::class, 'retrieveDepartmentFaculty'])->name('teacher/courses/approval');
        Route::get('faculty/list/{sess_id}', [CoursesOfferedController::class, 'retrievefacultylist'])->name('teacher/faculty/list');
        Route::get('courses/list/{sess_id}/{fc_emp_id}', [CoursesOfferedController::class, 'retrieveTeacherCourses'])->name('teacher/courses/list');
        Route::post('courses/store/approval', [CourseAssessmentMarksController::class, 'storeMarksApproval'])->name('teacher/courses/store/approval');

        // Course Criteria
        Route::get('courses_criteria', [CourseCriteriasController::class, 'index'])->name('teacher/courses_criteria');
        Route::get('courses_criteria_store/{co_id}/{assessment_name}', [CourseCriteriasController::class, 'criteria_store'])->name('teacher/courses_criteria_store/{co_id}/{assessment_name}');
        Route::post('courses_criteria_save', [CourseCriteriasController::class, 'criteria_save'])->name('teacher/courses_criteria_save');
        Route::get('load_criteria/{co_id?}/{course_id?}', [CourseCriteriasController::class, 'load_criteria'])->name('teacher/load_criteria/{co_id}');

        // Video upload
        Route::post('videoupload', [FacultyCoursesController::class, 'video_save'])->name('videoupload');
        Route::post('fileupload', [FacultyCoursesController::class, 'uploadFileWithApi'])->name('fileupload');
    });

    Route::post('teacher/stdupdatemarks', [FacultyCoursesController::class, 'stdupdatemarks_assignment'])->name('teacher/stdupdatemarks');
    
    
      Route::get('timetable/faculty', [TimeTableController::class,'getStudentTable']);
    // ------------------------------
    // Course Criteria Details Routes
    // ------------------------------
    Route::group(['CourseCriteriaDetails'], function () {
        Route::get('course/criteria_details/{co_id}', [CourseCriteriaDetailController::class, 'retrieveCriteriaDetails'])
            ->name('course/criteria_details/');
        Route::get('course/assessment_criteria_details/{co_id?}/{assessmentid?}', [CourseCriteriaDetailController::class, 'assessment_criteria_details']);
    });

    // ------------------------------
    // HOD Course Approval Routes
    // ------------------------------
    Route::group(['HODCourseApproval'], function () {
        Route::get('teacher/courses/approval', [FacultyCoursesController::class, 'retrieveDepartmentFaculty'])
            ->name('teacher/courses/approval');
        Route::get('teacher/faculty/list/{sess_id}', [CoursesOfferedController::class, 'retrievefacultylist'])
            ->name('teacher/faculty/list');
        Route::get('teacher/courses/list/{sess_id}/{fc_emp_id}', [CoursesOfferedController::class, 'retrieveTeacherCourses'])
            ->name('teacher/courses/list');
        Route::post('teacher/courses/store/approval', [CourseAssessmentMarksController::class, 'storeMarksApproval'])
            ->name('teacher/courses/store/approval');
    });

    // ------------------------------
    // Excel Export Route
    // ------------------------------
    Route::group(['PhpExcel'], function () {
        Route::get('print_excel', 'ExcelController@print_excel')->name('print_excel');
    });

    // ------------------------------
    // Grading Routes
    // ------------------------------
    Route::group(['Grading'], function () {
        Route::get('grading/grading/{co_id?}', [GradingController::class, 'index'])->name('grading');
        Route::post('grading/post_grade/{co_id?}', [GradingController::class, 'post_grade'])->name('post_grade');
        Route::get('reportgenerator/GradingMarksSummaries/{co_id?}', [ReportgeneratorController::class, 'GradingMarksSummaries'])
            ->name('GradingMarksSummaries');
    });

    // ------------------------------
    // Exam Report Routes
    // ------------------------------
    Route::get('reports/faculty/list', [FacultyReportsController::class, 'reportListing']);
    Route::get('reports/faculty/get-results', [FacultyReportsController::class, 'getResults']);
    Route::get('reports/faculty-evaluation', [FacultyEvaluationController::class, 'teacherreportListing'])->name('faculty_evaluation_listing');

    // Course-wise feedback report
    Route::get('course-wise-download-faculty-feedback-report', [FacultyReportsController::class, 'downloadCourseWiseReport'])
        ->name('reports.coursewise');

    // Faculty-wise feedback reports
    Route::get('faculty-wise-download-faculty-feedback-report', [FacultyReportsController::class, 'downloadFacultyWiseReport'])
        ->name('reports.facultywise');

    Route::get('faculty-wise-download-faculty-feedback-report-list', [FacultyReportsController::class, 'downloadFacultyWiseReportList'])
        ->name('reports.facultywise.list');

    // ------------------------------
    // Mobile Announcements
    // ------------------------------
    Route::controller(AnnouncementController::class)->group(function () {
        Route::get('/announcements', 'index')->name('announcements.index');
        Route::post('/announcements', 'store')->name('announcements.store');
        Route::put('/announcements/disable/{id}', 'disable')->name('announcements.disable');
        Route::put('/announcements/enable/{id}', 'enable')->name('announcements.enable');
    });


    /**
     * ------------------------
     * Class Discussions
     * ------------------------
     */
    Route::post('classdiscussion/{co_id?}', 'FacultyCoursesController@classdiscussion')->name('classdiscussion');
    Route::post('send-message-to-student-by-teacher', 'FacultyCoursesController@sendMessageToStudentByTeacher');
    Route::get('get_messages_by_teacher', 'FacultyCoursesController@getMessageByTeacher');

    Route::controller(StundentsFinancialController::class)->prefix('staff')->group(function () {
        Route::get('/student-financial', 'getDetail');
        Route::get('/get-Data', 'getData');
        Route::get('/generate-challan', 'genChallan');
        Route::get('/print-challan', 'print_challan');
        Route::post('/challan-request', 'RequestChallan');
        Route::post('/export-to-excel', 'exportExcel');
        Route::post('/export-to-excel-accounts', 'exportExcelAccounts');
        Route::post('/get-communication', 'insert_financeCommunication');
        Route::get('/challan-status-excel', 'challanStatusExcel');

        Route::get('get-challan', function (Request $request) {
            $obj = new StudentFinancial();
            return $obj->fetchChallan($request->get('userID'));
        });

        Route::get('get-challan-user', function (Request $request) {
            $obj = new StudentFinancial();
            return $obj->fetchChallanStd($request->get('userID'), $request->get('sessID'));
        });
    });


    // Student Record Routes
    Route::controller(StudentRecordController::class)->prefix('student-record')->group(function () {

        Route::get('/student-record', 'getRecord');
        Route::post('/update-student-challan-dates', 'updateDates')->name('update-student-challan-dates');
        Route::post('/student-cancel-challan-bulk', 'cancelBulkChallan');

        Route::get('/student-course-disable', 'cancelCourse');
        Route::post('/update-course_status', 'updateCourseStatus')->name('update-course_status');
    });

    

    // Standalone Route
    Route::get('softwarelist', [StudentRecordController::class, 'getSoftware']);

    // Financial Accounts Routes
    Route::controller(FinancialAccountsController::class)->prefix('accounts')->group(function () {

        Route::get('/student-financial', 'getDetail');
        Route::get('/get-Data', 'getData');
        Route::post('/update-record', 'updateRecord');
    });

    /*
    |--------------------------------------------------------------------------
    | Report Generator Routes
    |--------------------------------------------------------------------------
    */
    Route::group(['prefix' => 'reportgenerator'], function () {
        Route::get('student_profile/{enrollment_no?}', [ReportgeneratorController::class, 'load_student_profile'])->name('student_profile');
        Route::get('reportgenerator', [ReportgeneratorController::class, 'index'])->name('reportgenerator/reportgenerator');
        Route::get('load_marks/{course_id?}', [ReportgeneratorController::class, 'load_data'])->name('load_marks');
        Route::get('load_marks2/{course_id?}', [ReportgeneratorController::class, 'load_data2'])->name('load_marks2');
        Route::get('student_courses/{enrollment_no?}/{sess_id?}/{school_id?}', [ReportgeneratorController::class, 'student_courses'])->name('student_courses');
        Route::get('load_student_marks/{enrollment_no?}/{parent_co_id?}/{sc_co_id?}', [ReportgeneratorController::class, 'load_student_marks'])->name('load_student_marks');
        Route::get('courseMarksSummaries/{co_id?}', [ReportgeneratorController::class, 'courseMarksSummaries'])->name('courseMarksSummaries');
        Route::get('courseMarksSummaries2/{co_id?}', [ReportgeneratorController::class, 'courseMarksSummaries2'])->name('courseMarksSummaries2');
        Route::get('load_student_percentage/{enrollment_no?}/{parent_co_id?}', [ReportgeneratorController::class, 'load_student_percentage'])->name('load_student_percentage');
        Route::get('load_criteria/{course_id?}', [ReportgeneratorController::class, 'load_criteria'])->name('load_criteria');
    });

    // Separate route (not in the prefix) for loading student attendance
    Route::get('load_student_attendance/{parent_co_id}/{course_co_id}/{enrollment_no}', [ReportgeneratorController::class, 'load_student_attendance'])->name('load_student_attendance');


    /*
    |--------------------------------------------------------------------------
    | Assessment Setup Routes
    |--------------------------------------------------------------------------
    */
    Route::group(['prefix' => 'course'], function () {
        Route::get('assessment', [AssessmentController::class, 'retrieveAssessments'])->name('course/assessment');
        Route::get('assessment/order/{id}', [AssessmentController::class, 'retrieveAssessments'])->name('course/assessment/order/{id}');
        Route::post('assessment/create', [AssessmentController::class, 'retrieveAssessments'])->name('course/assessment/create');

        Route::get('SchoolAssessment', [SchoolCriteriaController::class, 'retrieveSchoolCriterias'])->name('course/SchoolAssessment');
        Route::get('SchoolCriteria/order/{id}', [SchoolCriteriaController::class, 'retrievemaxOrder'])->name('course/SchoolCriteria/order/{id}');
        Route::post('SchoolCriteria/create', [SchoolCriteriaController::class, 'create'])->name('course/SchoolCriteria/create');
    });


    /*
    |--------------------------------------------------------------------------
    | Reports Routes
    |--------------------------------------------------------------------------
    */
    Route::group(['Reports'], function () {
        Route::get('/assessment_summary_marks_report/{co_id}', [CourseMarksSummaryReportController::class, 'course_assessment_summary_report'])->name('/assessment_summary_marks_report');
        Route::get('/assessment_marks_report/{co_id}', [ReportsController::class, 'course_assessment_report'])->name('/assessment_marks_report');
        Route::get('/course_content/{co_id}', [ReportsController::class, 'course_content'])->name('/course_content');
        Route::get('/course_enrolled_students/{co_id}', [ReportsController::class, 'enrolledStudents'])->name('/course_enrolled_students');
    });

    // --------------------------
    // Exam & CGPA Reports
    // --------------------------
    Route::get('/repeating-course', [ExamReportController::class, 'repeating']);
    Route::get('/graduate-summary-report', [ExamReportController::class, 'graduateSummaryReport']);
    Route::get('/student/get-repeat-course', [ExamReportController::class, 'repeating_list']);
    Route::get('reports/exam/cgpa-summary', [ExamReportController::class, 'retrievelist']);
    Route::get('reports/exam/cgpa-summary-report', [ExamReportController::class, 'getreport']);

    // --------------------------
    // Faculty Evaluation & Reports
    // --------------------------
    Route::get('reports/faculty-course/get-results', [FacultyEvaluationController::class, 'getResultscourse']);
    Route::get('reports/faculty-course-chart/get-results', [FacultyEvaluationController::class, 'getResultsBychart']);
    Route::get('teacher_courses/get-result', [FacultyEvaluationController::class, 'getCoursesByfaculty']);
    Route::get('teacher_remarks/get-result', [FacultyEvaluationController::class, 'remarksByfaculty']);

    // --------------------------
    // Faculty Reports
    // --------------------------
    Route::get('/contacts', [FacultyReportsController::class, 'contacts']);
    Route::get('/cvt', [FacultyReportsController::class, 'cvt']);
    Route::get('teacher/faculty_course_detail', [FacultyReportsController::class, 'faculty_course_detail']);
    Route::get('teacher/academic_history2', [FacultyReportsController::class, 'academic_history']);
    Route::get('teacher/nbs_report', [FacultyReportsController::class, 'nbs_report']);
    Route::post('save-scholarship-input', [FacultyReportsController::class, 'saveScholarshipInput']);
    Route::get('/reports/scholarship_criteria_report', [FacultyReportsController::class, 'schCriteriaReport']);

    // --------------------------
    // Department/Program/Teacher Fetch
    // --------------------------
    Route::get('department/get', [FacultyReportsController::class, 'getDepartment']);
    Route::get('program/get', [FacultyReportsController::class, 'getProgram']);
    Route::get('teacher/get', [FacultyReportsController::class, 'getTeacher']);
    Route::get('teacher_courses/get', [FacultyReportsController::class, 'getCoursesByTeacher']);

    // --------------------------
    // Course Criteria Management
    // --------------------------
    Route::get('course/get_criterias/{id}', [CourseCriteriasController::class, 'get_course_criterias'])->name('course/get_criterias');
    Route::get('course/get_assessment_count/{co_id}/{assesment_id}', [CourseCriteriasController::class, 'get_assessment_count'])->name('course/get_assessment_count');
    Route::get('course/criteria_selected/{course_id}', [CourseCriteriasController::class, 'coursecriteria_selected'])->name('course/criteria_selected');

    Route::post('course/update_single_criteria_method', [CourseCriteriasController::class, 'update_single_criteria_method'])->name('course/update_single_criteria_method');
    Route::post('course/update_criteria_best_of', [CourseCriteriasController::class, 'update_criteria_best_of'])->name('course/update_criteria_best_of');
    Route::post('course/update_instructor_select_criteria', [CourseCriteriasController::class, 'update_instructor_select_criteria'])->name('course/update_instructor_select_criteria');
    Route::post('delete_criteria_assessment/{co_id}/{assessment_id}', [FacultyCoursesController::class, 'delete_criteria_assessment'])->name('delete_criteria_assessment');

    // --------------------------
    // Financial / Fee / Scholarship Routes
    // --------------------------
    Route::post('installment-admission-rounds', [FeeInstallmentController::class, 'getAdmissionRounds']);
    Route::post('addFeeStructure', [FeeStructureController::class, 'add']);
    Route::post('addHostelFeeStructure', [HostelFeeStructureController::class, 'add']);
    Route::post('addScholarship', [MasterScholarshipController::class, 'addScholarship']);
    Route::post('addChallan', [GenerateBulkChallanController::class, 'addChallan']);
    Route::post('add-credit-challan', [GenerateCreditHrBulkChallanController::class, 'addChallan']);
    Route::post('add-convocation-challan', [GenerateConvocationBulkChallanController::class, 'addConvocationChallan']);
    Route::post('add-fee-installment', [FeeInstallmentController::class, 'addInstallment']);
    Route::post('add-fee-installment-admission', [FeeInstallmentController::class, 'addInstallment_admission']);
    Route::get('master-installment-form', [MasterInstallmentController::class, 'masterInstallmentForm']);
    Route::post('add-installment', [MasterInstallmentController::class, 'addInstallment']);
    Route::post('unpaid-installment-scope-stats', [FeeInstallmentController::class, 'getUnpaidInstallmentScopeStats']);
    Route::post('preview-update-unpaid-installment-challan-dates', [FeeInstallmentController::class, 'previewUpdateUnpaidInstallmentChallanDates']);
    Route::post('update-unpaid-installment-challan-dates', [FeeInstallmentController::class, 'updateUnpaidInstallmentChallanDates']);
    Route::post('addMasterFeeAdjustment', [FeeAdjustmentController::class, 'addMasterFeeAdjustment']);
    Route::post('addfeeadjustment', [FeeAdjustmentController::class, 'addfeeAdjustment']);
    Route::post('add-scholarship-criteria', [ScholarshipCriteriaController::class, 'addScholarshipCriteria']);
    Route::post('add-admissionsIndividualScholarship-criteria', [AdmissionIndividualScholarshipCriteriaController::class, 'addAdmissionScholarshipCriteria']);
    Route::post('add-admissionsScholarship-criteria', [AdmissionScholarshipCriteriaController::class, 'addAdmissionScholarshipCriteria']);
    Route::post('course-exempted/store', [CourseExemptedController::class, 'store']);
    Route::post('course-exempted/get-courses', [CourseExemptedController::class, 'getCoursesBySession']);
    Route::post('course-exempted/import', [CourseExemptedController::class, 'importFromSession']);
    Route::post('course-exempted/list', [CourseExemptedController::class, 'list']);
    Route::post('course-exempted/delete', [CourseExemptedController::class, 'destroy']);

    // --------------------------
    // Manual Paid Marking
    // --------------------------
    Route::post('get-student-fee-rec-paid-mark', [StudentPaidMarkController::class, 'getStudentFeeRec'])->name('get-student-fee-rec-paid-mark');
    Route::post('get-student-Mvfee-rec-paid-mark', [StudentPaidMarkController::class, 'getStudentMvFeeRec'])->name('get-student-Mvfee-rec-paid-mark');
    Route::post('paid-student-fee-challan-paid-mark', [StudentPaidMarkController::class, 'paidFeeChallan'])->name('paid-student-fee-challan-paid-mark');
    Route::post('paid-student-mvFeeChallan-paid-mark', [StudentPaidMarkController::class, 'paidMvFeeChallan'])->name('paid-student-mvFeeChallan-paid-mark');
    Route::get('filter-paid-mark', [StudentPaidMarkController::class, 'filterPaidMark'])->name('filter-paid-mark');
    Route::get('filter-mvpaid-mark', [StudentPaidMarkController::class, 'filterMvPaidMark'])->name('filter-mvpaid-mark');
    Route::post('mark-challan-unpaid', [StudentPaidMarkController::class, 'markChallanUnpaid'])->name('mark-challan-unpaid');

    // --------------------------
    // Student Ledger
    // --------------------------
    Route::get('get-legder', [StudentLedgerController::class, 'getLedger']);
    Route::get('get-scholarship-detail', [StudentLedgerController::class, 'getScholarshipDetail']);
    Route::get('get-school-scholarship-report', [StudentLedgerController::class, 'getSchoolScholarshipReport']);
    Route::get('get-scholarship-detail-report', [StudentLedgerController::class, 'getScholarshipDetailReport']);
    Route::get('ledger_challan_preview', [StudentLedgerController::class, 'ledger_challan_preview']);

    // --------------------------
    // Misc Faculty Reports
    // --------------------------
    Route::get('teacher/course_attendance_detail', [FacultyReportsController::class, 'courseAttendanceDetail']);
    Route::get('teacher/student_semester_performance', [FacultyReportsController::class, 'studentSemesterPerformance']);
    Route::get('teacher/acadmic_history', [FacultyReportsController::class, 'academicHistory']);
    Route::get('teacher/graduate_survey_report', [FacultyReportsController::class, 'surveyReport']);
    Route::get('teacher/faculty_course_attendance_report', [FacultyReportsController::class, 'facultyCourseAttendance']);
    Route::get('teacher/attendance_summary_dept', [FacultyReportsController::class, 'facultyAttendancesummary']);
    Route::get('teacher/student_assessment_report', [FacultyReportsController::class, 'studentAssessment']);
    Route::get('teacher/grading_status', [FacultyReportsController::class, 'gradingStatus']);
    Route::get('teacher/enrollment_status', [FacultyReportsController::class, 'enrollmentStatus']);
    Route::get('teacher/applicant_academic_profile', [FacultyReportsController::class, 'ApplicantProfile']);
    Route::get('teacher/applicant_academic_profile_status', [FacultyReportsController::class, 'ApplicantProfileStatus']);
    Route::get('admission_applicant_status', [FacultyReportsController::class, 'AdmissionStatus']);
    Route::get('studentCheckNBS', [FacultyReportsController::class, 'checkStudentNBS']);
    Route::get('studentHealthReport', [FacultyReportsController::class, 'checkStudentHealth']);
    Route::get('studentZeroHealthReport', [FacultyReportsController::class, 'checkStudentZeroReport']);
    Route::get('gymSubscriptionReport', [FacultyReportsController::class, 'gymReport']);
    Route::get('/get-applicants-details', [FacultyReportsController::class, 'getScholarshipDetails']);
    Route::get('get-report-gym', [FacultyReportsController::class, 'getGymReport']);
    Route::post('update-status-gym', [FacultyReportsController::class, 'updateGymStatus'])->name('update-status-gym');
    Route::get('hostel-survey-summary', [FacultyReportsController::class, 'hostelSurveyIndex']);
    Route::get('grading_status_report', [FacultyReportsController::class, 'grading_report']);
    Route::get('teacher/admission_enrollment_status', [FacultyReportsController::class, 'yearwiseEnrollmentStatus']);
    Route::get('teacher/hr_attendance_report', [FacultyReportsController::class, 'HRAttendanceReport']);

    // --------------------------
    // Staff Exit Interview
    // --------------------------
    Route::get('staff-exit-interview-form', [StaffExitInterviewController::class, 'staffExitInterview']);
    Route::get('staff-exit-request-list', [StaffExitInterviewController::class, 'showList']);
    Route::post('staff-exit-interview-submit', [StaffExitInterviewController::class, 'store'])->name('staff-exit-interview-submit');
    Route::post('/employees-exit/accept/{id}', [StaffExitInterviewController::class, 'acceptReceivedStatus']);
    Route::get('lowerstaff_exist_form', [StaffExitInterviewController::class, 'lowerStaffExitInterview']);
    Route::post('lower-staff-exist-interview-submit', [StaffExitInterviewController::class, 'lowerStaffStore'])->name('lower-staff-exist-interview-submit');
    Route::get('university-leaving-report', [StaffExitInterviewController::class, 'index']);
    Route::post('/university-leaving-requests/filter', [StaffExitInterviewController::class, 'filter']);
    Route::get('/university-leaving-requests/pdf', [StaffExitInterviewController::class, 'generatePDF'])->name('university-leaving-requests.pdf');
    Route::get('pms-report', [StaffExitInterviewController::class, 'create']);
    Route::get('pms-report/excel', [StaffExitInterviewController::class, 'generatePmsExcel'])->name('pms-report.excel');

    // --------------------------
    // Utility & Misc
    // --------------------------
    Route::post('search-exit-person', [EportalRequestController::class, 'searchExitPerson'])->name('search-exit-person');
    Route::post('faculty_course_attendance_report_action', [FacultyReportsController::class, 'facultyCourseAttendanceReport']);
    Route::post('set_session', [FacultyCoursesController::class, 'set_session']);
    Route::get('viewed_notification/{un_id?}', [NotificationController::class, 'updateViewedNotificationStatus']);

    //--------------    STREAM  ------------------------------------//

    Route::prefix('course-stream')->group(function () {
        Route::get('view', [StreamController::class, 'courseStreamView']);
    });

    Route::get('course/chat/{id}', [ChatController::class, 'courseStreamView']);
    Route::get('course/chat2/{id?}', [ChatController::class, 'courseStreamView2']);
    Route::get('course/notification/{id?}', [ChatController::class, 'StreamView'])->name('course/notification');
    Route::get('course/ta_notification', [ChatController::class, 'TaStreamView'])->name('course/ta_notification');

    Route::get('enrollment-start-stop', [EnrollmentStartStopController::class, 'index'])->name("enrollment-start-stop");

    Route::get('road-map-wise-offering', [EnrollmentStartStopController::class, 'waitListEnrollmentOffering'])->name("road-map-wise-offering");
    Route::get('road-map-wise-offering2', [EnrollmentStartStopController::class, 'waitListEnrollmentOffering2'])->name("road-map-wise-offering2");
    Route::get('/get-roadmap-sessions-students', [EnrollmentStartStopController::class, 'getRoadmapStudents'])->name("/get-roadmap-sessions-students");
    Route::get('/get-students', [EnrollmentStartStopController::class, 'getActiveStudentsBySchool']);
    Route::post('/courses-offered/insert', [EnrollmentStartStopController::class, 'insertCoursesOffered'])->name('courses.insert');
    Route::post('/delete-multiple-courses', [EnrollmentStartStopController::class, 'deleteMultiple']);
    Route::get('/get-session-data', [EnrollmentStartStopController::class, 'getSessionData']);
    Route::get('/reports/student-wise-roadmap', [EnrollmentStartStopController::class, 'studentWiseRoadmapExcel'])->name('reports.student_wise_roadmap');
    Route::get('/get-courses-enrollment', [EnrollmentStartStopController::class, 'getCourses']);
    Route::post('/submit-session-courses', [EnrollmentStartStopController::class, 'submitCourses']);

    Route::get('/roadmap-sessions', [EnrollmentStartStopController::class, 'getRoadmapData'])->name('roadmap-sessions');
    Route::get('/get-session-data-prog', [EnrollmentStartStopController::class, 'getSessionDataProgramWise']);
    // Route::get('/view-session-data', [EnrollmentStartStopController::class, 'viewSessionData']);

    Route::post('/save-course', [EnrollmentStartStopController::class, 'saveCourse'])->name('save.course');
    Route::get('/get-next-session', [EnrollmentStartStopController::class, 'getNextSession']);
    Route::post('/roadmap/store', [EnrollmentStartStopController::class, 'roadmapofferingStore'])->name('roadmap.store');
    Route::post('/roadmap/store2', [EnrollmentStartStopController::class, 'roadmapofferingStore2'])->name('roadmap.store2');
    Route::get('/roadmap/data', [EnrollmentStartStopController::class, 'getroadMapOfferingData'])->name('roadmap.data');
    Route::get('/roadmap/verify-data', [EnrollmentStartStopController::class, 'verifyroadMapOfferingData'])->name('roadmap.verifydata');
    Route::get('/roadmap-offering/{id}/edit', [EnrollmentStartStopController::class, 'edit']);
    Route::get('/roadmap-offering/{id}/edit2', [EnrollmentStartStopController::class, 'edit2']);
    Route::post('/roadmap-offering-delete/{id}', [EnrollmentStartStopController::class, 'destroy'])->name('roadmap.offering.destroy');
    Route::post('/roadmap-offering-delete2/{id}', [EnrollmentStartStopController::class, 'destroy'])->name('roadmap.offering.destroy2');
    Route::get('road-map-wise-verification', [EnrollmentStartStopController::class, 'verifyEnrollmentOffering'])->name("road-map-wise-verification");
    // Route::get('admission_qualification', [EnrollmentStartStopController::class, 'admissionQualification'])->name("admission_qualification");
    // Route::get('doc-verifications', [EnrollmentStartStopController::class, 'documentVerification'])->name("doc-verifications");

    //--------------    HOSTEL LEAVE  ------------------------------------//

    Route::get('leave-requests', [HostelLeaveController::class, 'showPendingRequests'])->name('leave-requests');
    Route::post('leave-requests-approve/{id}', [HostelLeaveController::class, 'approveRequest'])->name('leave-requests-approve');
    Route::post('leave-requests-decline/{id}', [HostelLeaveController::class, 'declineRequest'])->name('leave-requests-decline');


    //--------------    VC DASHBOARD  ------------------------------------//

    Route::get('vc-dashboard', [VcReportsController::class, 'vc_DashBoard']);
    Route::get('car-sticker-report', [VcReportsController::class, 'carPoolingReport'])->name('car-sticker-report');
    Route::post('vc-car-pooling-report', [VcReportsController::class, 'reportData'])->name('vc-car-pooling-report');
    Route::post('vc-car-pooling-report-studentwise', [VcReportsController::class, 'reportDataStudentWise'])->name('vc-car-pooling-report-studentwise');
    Route::get('admission_applicant_data', [VcReportsController::class, 'admissionApplicant'])->name('admission_applicant_data');
    Route::post('applicant-report', [VcReportsController::class, 'reportDataApplicant'])->name('applicant-report');

    // Peer Mentorship Core
    Route::get('/pm-scholarship-students', [PeerMentorshipController::class, 'fetchScholarshipStudents']);
    Route::post('/schedule-interview', [PeerMentorshipController::class, 'scheduleInterview'])->name('schedule-interview');
    Route::post('/mark-interviewed', [PeerMentorshipController::class, 'markInterviewedSchedule'])->name('mark-interviewed');

    Route::post('/pm-gradebook-details', [PeerMentorshipController::class, 'getGradebookDetails'])->name('pm-gradebook-details');
    Route::get('/gradebook/{enrollment_no}', [PeerMentorshipController::class, 'showGradebook'])->name('gradebook');
    Route::post('/gradebook/mark-mentored', [PeerMentorshipController::class, 'markMentored'])->name('gradebook.markMentored');

    Route::post('/makeMentor', [PeerMentorshipController::class, 'makeMentored'])->name('makeMentor');
    Route::post('/make-admin-mentor', [PeerMentorshipController::class, 'makeAdminMentor']);

    Route::post('/mentor_slot', [PeerMentorshipController::class, 'mentorSlot'])->name('mentor_slot');
    Route::post('/schedule-submit', [PeerMentorshipController::class, 'store'])->name('schedule-submit');

    Route::controller(PeerMentorshipController::class)
        ->prefix('interview_schedules')
        ->name('interview_schedules.')
        ->group(function () {
            Route::get('/', 'index')->name('index');          // show list
            Route::post('/{id}/mark', 'markInterviewed')->name('mark'); // update
        });

    // Peer Mentorship Pages & Lists
    Route::get('/mentors_list', [PeerMentorshipController::class, 'showMentorsPage'])->name('mentors_list');
    Route::get('/get-mentors-list', [PeerMentorshipController::class, 'getMentorsData'])->name('get-mentors-list');
    Route::get('/search-courses', [PeerMentorshipController::class, 'searchCourses'])->name('search-courses');
    Route::post('/update-mentor-type', [PeerMentorshipController::class, 'updateMentorType'])
        ->name('update-mentor-type');

    // Admin Views
    Route::get('/admin-mentors-list', [PeerMentorshipController::class, 'showAdminMentors'])->name('admin-mentors-list');
    Route::get('/get-admin-mentors-list', [PeerMentorshipController::class, 'getAdminMentorsList'])->name('get-admin-mentors-list');
    Route::get('/pm-search-employees', [PeerMentorshipController::class, 'searchPmEmployees'])->name('pm-search-employees');
    Route::get('/admin-mentor-assignments', [PeerMentorshipController::class, 'getAdminMentorAssignments'])->name('admin-mentor-assignments');
    Route::post('/admin-mentor-assign', [PeerMentorshipController::class, 'assignAdminMentorStudent'])->name('admin-mentor-assign');
    Route::post('/admin-mentor-assign-batch', [PeerMentorshipController::class, 'assignAdminMentorBatch'])->name('admin-mentor-assign-batch');
    Route::post('/admin-mentor-assignment-status', [PeerMentorshipController::class, 'updateAdminMentorAssignmentStatus'])->name('admin-mentor-assignment-status');
    Route::get('/admin-staff-mentor-requests', [PeerMentorshipController::class, 'getAdminStaffMentorRequests'])->name('admin-staff-mentor-requests');
    Route::get('/my-assigned-mentors', [PeerMentorshipController::class, 'showMyAssignedMentors'])->name('my-assigned-mentors');
    Route::get('/get-my-assigned-mentors', [PeerMentorshipController::class, 'getMyAssignedMentorsList'])->name('get-my-assigned-mentors');
    Route::get('/staff-mentor-request-status', [PeerMentorshipController::class, 'getStaffMentorRequestStatus'])->name('staff-mentor-request-status');
    Route::get('/my-staff-mentor-requests', [PeerMentorshipController::class, 'getMyStaffMentorRequests'])->name('my-staff-mentor-requests');
    Route::post('/submit-staff-mentor-request', [PeerMentorshipController::class, 'submitStaffMentorRequest'])->name('submit-staff-mentor-request');
    Route::get('/get-pm-student-basic-info', [PeerMentorshipController::class, 'getPmStudentBasicInfo'])->name('get-pm-student-basic-info');
    Route::post('/submit-employee-mentor-feedback', [PeerMentorshipController::class, 'submitEmployeeMentorFeedback'])->name('submit-employee-mentor-feedback');
    Route::get('/be_mentors_list', [PeerMentorshipController::class, 'showbeMentors'])->name('be_mentors_list');
    Route::get('/get-be-mentors-list', [PeerMentorshipController::class, 'getBeMentorsList'])->name('get-be-mentors-list');
    Route::get('/be_mentors_list_courses', [PeerMentorshipController::class, 'be_mentors_list_courses'])->name('be_mentors_list_courses');
    Route::get('/get-mentor-details', [PeerMentorshipController::class, 'getMentorDetails']);

    // Requests & Student Handling
    Route::get('/pm-students-requests', [PeerMentorshipController::class, 'RequestIndex']);
    Route::get('/getCourseMentors/{course_id}', [PeerMentorshipController::class, 'getCourseMentors']);
    Route::get('/pm-peer-mentorship', [PeerMentorshipController::class, 'index'])->name('peer-mentorship.index');
    Route::post('/pm-request-courses', [PeerMentorshipController::class, 'requestCourses'])->name('pm-request-courses');
    Route::get('/get-requested-courses', [PeerMentorshipController::class, 'getRequestedCourses'])->name('get-requested-courses');
    Route::get('/get-student-requests', [PeerMentorshipController::class, 'getMentorRequest']);
    Route::get('/get-scholarship-students', [PeerMentorshipController::class, 'getScholarshipStudents']);
    Route::get('/marksdetailMentor/{co_id}/{roll_no}', [PeerMentorshipController::class, 'marksdetailMentor'])->name('marksdetailMentor');

    // Remarks & Approvals
    Route::post('/updateHodRemarks', [PeerMentorshipController::class, 'updateRemarks'])->name('updateHodRemarks');
    Route::post('/update-approval-status', [PeerMentorshipController::class, 'committeeApprovel'])->name('update-approval-status');

    // Timetable & Sessions
    Route::post('/pm-timetable', [PeerMentorshipController::class, 'pmTimttable'])->name('pm-timetable');
    Route::post('/re-timetable', [PeerMentorshipController::class, 'reschTimttable'])->name('re-timetable');
    Route::post('/timetable-sessions', [PeerMentorshipController::class, 'createBiweeklySessions'])->name('timetable-sessions');
    Route::get('/view-timetable-mentor', [PeerMentorshipController::class, 'mentorCompletetimetable'])->name('view-timetable-mentor');

    // Feedback & Intro Session
    Route::get('/intro-session', [PeerMentorshipController::class, 'introSession'])->name('intro.session');
    Route::get('/feedback/{sessionId}', [PeerMentorshipController::class, 'getFeedback'])->name('get.feedback');
    Route::post('/feedback/save-approvals', [PeerMentorshipController::class, 'saveFeedbackApprovals'])->name('feedback.save-approvals');

    //18/11/25
    Route::post('/pm-cr-remove-hold', [PeerMentorshipController::class, 'removeHold']);
    Route::post('/putOnHoldRequest', [PeerMentorshipController::class, 'putOnHoldRequest'])->name('putOnHoldRequest');
    Route::post('pm-cr-put-on-hold', [PeerMentorshipController::class, 'coursePutOnHoldRequest']);
    Route::delete('/pm-delete-session/{id}', [PeerMentorshipController::class, 'deleteSession']);
    Route::post('/removeHoldRequest', [PeerMentorshipController::class, 'removeStdHold']);




    //--------------    HOSTEL LEAVE ROUTES  ------------------------------------//

    Route::get('leave-requests', [HostelLeaveController::class, 'showPendingRequests'])->name('leave-requests');
    Route::post('leave-requests-approve/{id}', [HostelLeaveController::class, 'approveRequest'])->name('leave-requests-approve');
    Route::post('leave-requests-decline/{id}', [HostelLeaveController::class, 'declineRequest'])->name('leave-requests-decline');

    // Permissions & Approval
    Route::post('add_permistion', [EportalPermissionController::class, 'permistionGranted'])->name('add_permistion');
    Route::get('ep_permissions', [EportalPermissionController::class, 'showForm'])->name('ep_permissions');
    Route::post('delete-ecdr', [EportalPermissionController::class, 'deleteEcdr'])->name('delete-ecdr');

    // Dashboard & Reports
    Route::get('eportal-dashboard', [EportalController::class, 'getDashboadrd'])->name('eportal-dashboard');
    Route::get('eportal-report-student', [EportalController::class, 'getReportStudentExit'])->name('eportal-report-student');
    Route::get('eportal-report-student-pdf', function () {
        return view('ePortal.studentExitFormAllpdf');
    })->name('eportal-report-student-pdf');
    Route::get('eportal-report-student-excel', function () {
        
        return view('ePortal.studentExitFormAllExcel');
    })->name('eportal-report-student-excel');
    Route::get('eportal-report-staff', [EportalController::class, 'getReport'])->name('eportal-report-staff');
    Route::get('fetch_report2', [EportalController::class, 'fetchReport'])->name('fetch_report2');

    // Approval Workflow
    Route::get('eportal-approval', [EportalController::class, 'getApproval'])->name('eportal-approval');
    Route::get('eportal-approval-authority', [EportalController::class, 'getApprovalAuthorityRequest'])->name('eportal-approval-authority');
    Route::get('eportal_completed', [EportalController::class, 'completed'])->name('eportal_completed');
    Route::get('eportal_approved', [EportalController::class, 'approved'])->name('eportal_approved');
    Route::get('eportal/fetch_staff_data', [EportalController::class, 'fetchStaffData'])->name('eportal/fetch_staff_data');
    Route::post('eportal_request_complete', [EportalController::class, 'markCompleted'])->name('eportal_request_complete');
    Route::post('eportal_request_approved1', [EportalController::class, 'uploadFile'])->name('eportal_request_approved');
    Route::post('eportal_request_approved', [EportalController::class, 'markApproval'])->name('eportal_request_approved');
    Route::get('eportal_request_reject', [EportalController::class, 'rejectStatus'])->name('eportal_request_reject');
    Route::post('eportal_request_rollback', [EportalController::class, 'rollbackStatus'])->name('eportal_request_rollback');
    Route::get('exam-report-student', [EportalController::class, 'getExamReportStudentExit'])->name('exam-report-student');


    //-------------- EPORTAL HOD/DEAN LIST  ROUTES ------------------------------------//
    Route::controller(HodDeanListController::class)->group(function () {
        Route::get('eportal-hod-dean-list', 'index')->name('eportal-hod-dean-list');
        Route::get('eportal-hod-dean-list/get-all', 'getAllHodDeanList')->name('eportal-hod-dean-list.get-all');
        Route::get('eportal-hod-dean-list/employees/{schoolId}', 'getEmployeesBySchool')->name('eportal-hod-dean-list.employees');
        Route::post('eportal-hod-dean-list/check', 'checkHodDean')->name('eportal-hod-dean-list.check');
        Route::post('eportal-hod-dean-list/store', 'store')->name('eportal.hod-dean.store');
    });






    // Categories - Custom & Resourceful
    Route::get('ep_categories', [EportalCategoryController::class, 'index'])->name('ep_categories');
    Route::get('ep_categories_create', [EportalCategoryController::class, 'create'])->name('ep_categories_create');
    Route::get('ep_categories_show', [EportalCategoryController::class, 'show'])->name('ep_categories_show');
    Route::post('ep_categories_edit', [EportalCategoryController::class, 'edit'])->name('ep_categories_edit');
    Route::post('categories_add', [EportalCategoryController::class, 'store'])->name('categories_add');

    // Categories - RESTful routes
    Route::get('categories', [EportalCategoryController::class, 'index'])->name('categories.index');
    Route::get('categories/create', [EportalCategoryController::class, 'create'])->name('categories.create');
    Route::post('categories', [EportalCategoryController::class, 'store'])->name('categories.store');
    Route::get('categories/{id}', [EportalCategoryController::class, 'show'])->name('categories.show');
    Route::get('categories/{id}/edit', [EportalCategoryController::class, 'edit'])->name('categories.edit');
    Route::put('categories/{id}', [EportalCategoryController::class, 'update'])->name('categories.update');
    Route::delete('categories/{id}', [EportalCategoryController::class, 'destroy'])->name('categories.destroy');

    // Category Fetch APIs
    Route::get('get_categ', [EportalController::class, 'getCateg'])->name('get_categ');
    Route::get('get_categ_appr', [EportalController::class, 'getApprCateg'])->name('get_categ_appr');

    // Messaging
    Route::post('/eportal/send-message-to-user', [EportalMessageController::class, 'sendMsg'])->name('staff/assign-to-message');
    Route::get('/eportal/get-user-messages', [EportalMessageController::class, 'getUserMessages'])->name('staff/get-user-messages');
    //-------------- VC EPORTAL ROUTES ------------------------------------//
    Route::controller(VCePortalController::class)->prefix('vc')->group(function () {
        Route::get('/eportal-tracking', 'getApproval')->name('eportal-approval');
        Route::get('/eportal-dashboard', 'getDashboadrd')->name('eportal-dashboard');
        Route::get('/eportal-report', 'getReport')->name('eportal-report');
        Route::get('/eportal-summary', 'getSummary')->name('eportal-summary');
        Route::get('/fetch_report', 'fetchReport')->name('fetch_report');
        Route::get('/fetch_staff_data', 'fetchStaffData')->name('vc/fetch_staff_data');

        // Category & Approval
        Route::get('get_categ', 'getCateg')->name('vc/get_categ');
        Route::get('get_forward_user', 'getForwardUser')->name('vc/get_forward_user');
        Route::get('get_categ_appr', 'getApprCateg')->name('vc/get_categ_appr');
        Route::get('eportal-approval-authority', 'getApprovalAuthorityRequest')->name('eportal-approval-authority');
        Route::get('eportal-approval-authority-report', 'getApprovalAuthorityRequestReport')->name('eportal-approval-authority-report');
        Route::get('get_categories', 'getcatories')->name('vc/get_categories');
        Route::post('eportal_request_complete', 'markCompleted')->name('vc/eportal_request_complete');
    });

    //-------------- ADMIN EPORTAL ROUTES ------------------------------------//
    Route::controller(AdminePortalController::class)->prefix('admin')->group(function () {
        Route::get('/eportal-tracking', 'getApproval')->name('eportal-approval');
        Route::get('/eportal-dashboard', 'getDashboadrd')->name('eportal-dashboard');
        Route::get('/eportal-report', 'getReport')->name('eportal-report');
        Route::get('/fetch_report', 'fetchReport')->name('fetch_report');
        Route::get('/fetch_staff_data', 'fetchStaffData')->name('admin/fetch_staff_data');

        // Category & Approval
        Route::get('get_categ', 'getCateg')->name('admin/get_categ');
        Route::get('get_forward_user', 'getForwardUser')->name('admin/get_forward_user');
        Route::get('get_categ_appr', 'getApprCateg')->name('admin/get_categ_appr');
        Route::get('eportal-approval-authority', 'getApprovalAuthorityRequest')->name('eportal-approval-authority');
        Route::get('eportal-approval-authority-report', 'getApprovalAuthorityRequestReport')->name('eportal-approval-authority-report');
        Route::get('get_categories', 'getcatories')->name('admin/get_categories');
        Route::post('eportal_request_complete', 'markCompleted')->name('admin/eportal_request_complete');
    });

    //-------------- EPORTAL REQUEST ROUTES ------------------------------------//
    Route::controller(EportalRequestController::class)->group(function () {
        Route::get('/eportal-request', 'index')->name('eportal-request');
        Route::post('/add-ePortal-request', 'submitRrequest')->name('eportal-request');
        Route::get('/get-departments', 'getDept')->name('get-departments');
        Route::get('/get-employees', 'getUserData')->name('get-employees');
        Route::get('/e-portal-pending', 'pedningRequst')->name('e-portal-pending');
        Route::get('/e-portal-completed', 'completedRequest')->name('e-portal-completed');
        Route::get('/eportal-application-status', 'completedRequest')->name('eportal-application-status');
    });

    //-------------- HOSTEL ROUTES ------------------------------------//

    Route::get('hostel-bed-assignment', [HostelBedAssignmentController::class, 'index'])
        ->name('hostel-bed-assignment');

    // Hostel PDF Report Route
    Route::get('hostel/pdf-report', [HostelReportController::class, 'generateHostelPDFReport'])->name('hostel.pdf.report');

    Route::get('/new-hostel-report', [AdmissionNewApplicantsHostelReportController::class, 'sessionPage']);

    Route::get(
        '/new-hostel-report/students/{sessionId}',
        [AdmissionNewApplicantsHostelReportController::class, 'studentsAjax']
    );

    // Legacy report URLs (backward compatible with older prod links)
    Route::get(
        '/hostel-report/{sessionId}/students/{type}',
        [AdmissionNewApplicantsHostelReportController::class, 'students']
    );




    // Hostel Type & Floor Management Routes
    Route::controller(HostelManagementController::class)->group(function () {
        Route::get('hostel-management', 'index')->name('hostel-management');
        Route::post('hostel-management/save-hostel-type', 'saveHostelType')->name('hostel-management.save-hostel-type');
        Route::post('hostel-management/save-floor', 'saveFloor')->name('hostel-management.save-floor');
        Route::get('hostel-management/get-hostel-type/{id}', 'getHostelType')->name('hostel-management.get-hostel-type');
        Route::get('hostel-management/get-floor/{id}', 'getFloor')->name('hostel-management.get-floor');
        Route::post('hostel-management/toggle-hostel-type-status/{id}', 'toggleHostelTypeStatus')->name('hostel-management.toggle-hostel-type-status');
        Route::post('hostel-management/toggle-floor-status/{id}', 'toggleFloorStatus')->name('hostel-management.toggle-floor-status');
        Route::post('hostel-management/delete-hostel-type/{id}', 'deleteHostelType')->name('hostel-management.delete-hostel-type');
        Route::post('hostel-management/delete-floor/{id}', 'deleteFloor')->name('hostel-management.delete-floor');
    });


    // Hostel Summary Routes
    Route::controller(HostelSummaryController::class)->group(function () {
        Route::get('hostel-summary', 'index')->name('hostel-summary');
        Route::post('hostel-summary/search-student', 'searchStudent')->name('hostel-summary.search-student');
    });

    Route::controller(HostelController::class)->group(function () {
        // Hostel Applicant Routes
        Route::get('hostel-applicants', 'showHostelApplicants');
        Route::get('hostel-students', 'showHostelStudents');
        Route::post('vacate-bed-status', 'vacateBedStatus')->name('vacate-bed-status');
        Route::get('hostel-vacate', 'vacateHostel');
        Route::post('occupy-bed-status', 'occupyBedStatus')->name('occupy-bed-status');
        Route::get('export-applicants/{session_id}', 'exportApplicants');
        // Hostel Facility Routes
        Route::get('facilities', 'showFacilities');
        Route::post('submit-facilities', 'submitFacilities')->name('submit.facilities');
        Route::post('edit-facility', 'editFacility')->name('edit.facility');
        Route::post('enable-facility/{id}', 'enableFacility')->name('enable.facility');
        Route::post('disable-facility/{id}', 'disableFacility')->name('disable.facility');
        // Hostel Room Routes
        Route::post('add-room', 'addRoom')->name('addRoom');
        Route::get('hostel-categories', 'addHostelTypeView');
        // Hostel Room Enable/Disable
        Route::post('room_enable/{id}', 'enableRoom')->name('room_enable');
        Route::post('room_disable/{id}', 'disableRoom')->name('room_disable');
        // Hostel Form Visibility
        Route::get('hostel-form-visibility', 'hostel_form_visibility')->name('hostel-form-visibility');
        Route::post('submit-hostel-form-visibility', 'submit_hostel_form_visibility')->name('submit-hostel-form-visibility');
        Route::delete('delete-hostel-form-visibility', 'delete_hostel_form_visibility')->name('delete-hostel-form-visibility');
        Route::get('/hostel/checkin-checkout', 'showCheckinCheckout')->name('hostel.checkin-checkout');
        Route::post('/hostel/checkin-checkout/save', 'saveCheckinCheckout')->name('hostel.checkin-checkout.save');
        Route::delete('/hostel/checkin-checkout/delete/{id}', 'deleteCheckinCheckout')->name('hostel.checkin-checkout.delete');
    });

    //-------------- REPORT GENERATOR ROUTES ------------------------------------//
    Route::controller(ReportsGeneratorController::class)->group(function () {

        // Report Index
        Route::get('/reports/report-generator', 'index')->name('reports/report-generator');
        Route::get('/reports/get-course-marks-sheet', 'marksdetail')->name('reports.get-course-marks-sheet');

        // Department & Student Records
        Route::get('/get-dept-list/{schoolID?}', 'getdept')->name('get-dept-list');
        Route::post('/reports/get-students-rec', 'getStudentRec')->name('reports/get-students-rec');
        Route::get('/reports/get-students-grade-book', 'studentGradeBook')->name('reports/get-students-grade-book');
        Route::get('/reports/get-students-courses-rec', 'studentCourses')->name('reports/get-students-courses-rec');
        Route::get('courses/student-enrolled', 'getStudentEnrolledCourses')->name('courses/student-enrolled');
        Route::get('/reports/get-dept-rec', 'getDepartments')->name('reports/get-dept-rec');

        // Comments & Warnings
        Route::post('/reports/send-faculty-student-comment', 'sendComment')->name('reports/send-faculty-student-comment');
        Route::get('/reports/get-faculty-student-comment/{sendCommentTo}', 'getComments')->name('reports/get-faculty-student-comment');
        Route::get('/reports/get-student-warning-letter/{sendTo}', 'checkWarningLetter')->name('reports/get-student-warning-letter');
        Route::get('/reports/get-student-scholarship-details/{sendTo}', 'checkScholarship')->name('reports/get-student-scholarship-details');
        Route::post('/reports/send-warning-letter-to-student', 'sendWarningLetter')->name('reports/send-warning-letter-to-student');
        Route::post('/reports/send-warning-letters-to-multiple-students', 'sendWarningLettersToMultiple')->name('reports/send-warning-letters-to-multiple-students');
        Route::delete('/reports/delete-warning-letter', 'deleteWarningLetter')->name('reports/delete-warning-letter');

        // GPA & Enrollment
        Route::get('/reports/get-student-gpa-rec/{enrollmentNo}', 'studentGPArec')->name('reports/get-student-gpa-rec');
        Route::get('/reports/get-current-enrolled-student-courses', 'getCurrentEnrolledCourses')->name('reports/get-current-enrolled-student-courses');
    });
    //-------------- STUDENT FINE ROUTES ------------------------------------//
    Route::controller(StudentFineController::class)->group(function () {
        Route::get('/student-fine-sub-categories', 'index')->name('student-fine-sub-categories');
        Route::post('/submit-student-fine', 'submitFine')->name('submit-student-fine');
        Route::post('/delete-student-fine', 'deleteFine')->name('delete-student-fine');
        Route::get('/get-fine-data', 'getFinesData')->name('get-fine-data');

        // Categories
        Route::get('/student-fine-categories', 'studentFineCategory')->name('student-fine-categories');
        Route::post('/add-student-fine-categories', 'addFineCategory')->name('add-student-fine-categories');
        Route::get('/get-student-fine-categories', 'getFineCategories')->name('get-student-fine-categories');
        Route::post('/delete-fine-category', 'deleteFineCategory')->name('delete-fine-category');

        // Students & Fines
        Route::get('/students-fine', 'StudentFine')->name('students-fine');
        Route::post('/get-student-fine-rec', 'getStudentFineRec')->name('get-student-fine-rec');
        Route::get('/get-student-fine-list', 'getStudentFineList')->name('get-student-fine-list');
        Route::get('/blocked-student-list', 'blockedStudentFineList')->name('blocked-student-list');
        Route::get('/get-blocked-student-list', 'getBlockedStudents')->name('get-blocked-student-list');

        // Challan


        Route::get('/get-challan-student-list', 'getChallanStudents')->name('get-challan-student-list');
        Route::post('/generate-student-fine-challan', 'generateFineChallan')->name('generate-student-fine-challan');
        Route::post('/update-student-fine-challan', 'updateFineChallan')->name('update-student-fine-challan');
        Route::get('/fine-challan-student-list', 'fineChallanStudentList')->name('fine-challan-student-list');
        Route::get('/fine-challan-generated-list', 'fineChallanGeneratedList')->name('fine-challan-generated-list');

        // Payment & Fee
        Route::get('/student-paid-mark', 'paidmark')->name('student-paid-mark');
        Route::post('/get-student-fee-rec', 'getStudentFeeRec')->name('get-student-fee-rec');
        Route::post('/paid-student-fee-challan', 'paidFeeChallan')->name('paid-student-fee-challan');

        // Challan Actions
        Route::post('/student-cancel-challan', 'cancelChallans')->name('student-cancel-challan');
        Route::post('/student-activate-challan', 'activateChallans')->name('student-activate-challan');
        Route::get('/sync-student-list', 'reSyncnStudentList')->name('sync-student-list');

        // Permissions
        Route::post('/fine-category-permission', 'fineCategoryPermission')->name('fine-category-permission');
        Route::post('/delete-fine-category-permission', 'deleteFineCategoryPermission')->name('delete-fine-category-permission');

        // Violation Checks
        Route::get('/check-violation-fines', 'violationFines')->name('check-violation-fines');
        Route::get('/check-violation-types', 'violationtypes')->name('check-violation-types');
    });

    //-------------- FINANCIAL MANAGEMENT ROUTES ------------------------------------//
    Route::middleware(['verify.group.access'])->group(function () { //ALL MAIN PAGES OF STUDENT FINANCIALS
        Route::get('/finance/fee-policy', [FeePolicyController::class, 'index'])->name('finance/fee-policy');
        Route::get('master-scholarship-criteria', [ScholarshipCriteriaController::class, 'masterSclrshpCriteriaForm']);
        Route::get('master-scholarship-policy', [ScholarshipPolicyController::class, 'masterSclrshpPolicyForm']);
        Route::get('/finance/assign-scholarship-index', [ScholarshipAssignController::class, 'index'])->name('finance/assign-scholarship-index');
        Route::get('/finance/scholarship-form', [ScholarshipFormController::class, 'index'])->name('finance/scholarship-form');
        Route::get('/finance/verify-scholarship-index', [ScholarshipAssignApprovalController::class, 'index'])->name('finance/verify-scholarship-index');
        Route::get('/finance/other-scholarship-index', [ScholarshipAssignController::class, 'indexOtherScholarship'])->name('finance/other-scholarship-index');
        Route::get('studentLedger', [StudentLedgerController::class, 'studentLedger'])->name('studentLedger');
        Route::get('enrollment-summary-report', [EnrollmentSummaryReportController::class, 'enrollmentSummaryReport']);
        Route::get('challan-detail-report', [ChallanDetailReportController::class, 'challanDetailReport']);
        Route::get('challan-generated-details', [StudentChallanDetailsController::class, 'challanGeneratedList'])->name('challan-generated-details');
        Route::get('middleware-challan_rectification-form', [MiddlewareChallanRectificationController::class, 'middlewareChallanRectificationForm']);
        Route::get('challan-report', [ChallanReportController::class, 'challanReport']);
        Route::get('merit-scholarship-entry', [GenerateBulkChallanController::class, 'handleMeritScholarshipEntry']);
        Route::get('master-fee-structure', [FeeStructureController::class, 'masterFeeForm']);
        Route::get('master-scholarship-form', [MasterScholarshipController::class, 'masterScholarshipForm']);
        Route::get('master-hostelfee-structure', [HostelFeeStructureController::class, 'masterHostelFeeForm']);
        Route::get('master-feeadjustment-form', [FeeAdjustmentController::class, 'masterFeeAdjustmentForm']);
        Route::get('feeadjustment', [FeeAdjustmentController::class, 'feeAdjustment']);
        Route::get('generate-fee-challan', [GenerateBulkChallanController::class, 'generateFeeChallan']);
        Route::get('generate-credithrfee-challan', [GenerateCreditHrBulkChallanController::class, 'generateFeeChallan']);
        Route::get('/finance/merit-calculation', [MeritCalculationController::class, 'index'])->name('finance.merit-calculation');
        Route::post('/finance/merit-calculation/status',   [MeritCalculationController::class, 'verificationStatus'])->name('finance.merit-calculation.status');
        Route::post('/finance/merit-calculation/calculate', [MeritCalculationController::class, 'calculate'])->name('finance.merit-calculation.calculate');
        Route::post('/finance/merit-calculation/save', [MeritCalculationController::class, 'saveVerified'])->name('finance.merit-calculation.save');
        Route::post('/finance/merit-calculation/post', [MeritCalculationController::class, 'postScholarship'])->name('finance.merit-calculation.post');
        Route::post('/finance/merit-calculation/diagnose', [MeritCalculationController::class, 'diagnose'])->name('finance.merit-calculation.diagnose');
        Route::get('/finance/merit-calculation/programs',[MeritProgramMappingController::class, 'index'])->name('finance.merit-calculation.programs');
        Route::post('/finance/merit-calculation/programs',[MeritProgramMappingController::class, 'save'])->name('finance.merit-calculation.programs.save');
        Route::post('/finance/merit-calculation/programs/restore',[MeritProgramMappingController::class, 'restore'])->name('finance.merit-calculation.programs.restore');
        Route::get('student-credits-data', [StudentCreditsDataController::class, 'index'])->name('student-credits-data');
        Route::get('generate-convocation-challan', [GenerateConvocationBulkChallanController::class, 'convocationChallanPage']);
        Route::get('/finance/generate-single-challan', [GenerateSingleChallanController::class, 'index'])->name('finance/generate-single-challan');
        Route::get('installment-form', [FeeInstallmentController::class, 'installmentForm']);
        Route::get('student-manual-paid-mark', [StudentPaidMarkController::class, 'paidmark'])->name('student-manual-paid-mark');
        Route::get('student-record/student-cancel-challan', [StudentRecordController::class, 'challanBulkCancel']);
        Route::get('merit-scholarship-challan-handle', [GenerateBulkChallanController::class, 'handleMeritScholarshipChallan']);
        Route::get('installment-form-admission', [FeeInstallmentController::class, 'installmentForm_admission']);
        Route::get('admission-scholarship-criteria', [AdmissionScholarshipCriteriaController::class, 'masterAdmissionSclrshpCriteriaForm']);
        Route::get('admission-individual-scholarship-criteria', [AdmissionIndividualScholarshipCriteriaController::class, 'masterAdmissionSclrshpCriteriaForm']);
        Route::get('/finance/verify-admissionScholarship', [AdmissionScholarshipApprovalController::class, 'index'])->name('admissionScholarshipApproval');
        Route::get('donation-record-form', [DonationMasterController::class, 'donationRecordForm']);
        Route::get('course-exempted', [CourseExemptedController::class, 'index'])->name('course-exempted');
        Route::get('emp-employee-details', [EmpEmployeesController::class, 'employeeDetails'])->name('emp-employee-details');
        Route::get('emp-employee-card/{employee_id}', [EmpEmployeesController::class, 'printEmployeeCard'])->name('emp-employee-card');
        Route::get('student-record/student-record-revised', [StudentRecordRevisedController::class, 'getRecord'])->name('student-record-revised');

    });
    Route::get('challan-summary-report', [ChallanSummaryReportController::class, 'challanSummaryReport']);
    Route::get('scholarship-report', [StudentLedgerController::class, 'scholarshipReport'])->name('scholarshipReport');
    Route::get('enrolled-students', [StudentChallanDetailsController::class, 'enrolledStudents']);
    Route::get('aging-report', [StudentChallanDetailsController::class, 'agingReport']);
    Route::get('fee-global-summary', [StudentChallanDetailsController::class, 'feeGlobalSummary']);
    Route::get('hostel-auxiliary-charges', [HostelAuxiliaryChargesController::class, 'index']);
    Route::post('hostel-auxiliary-charges/data', [HostelAuxiliaryChargesController::class, 'getReportData'])->name('hostel.auxiliary.charges.data');
    Route::post('aging-report-data', [StudentChallanDetailsController::class, 'agingReportData'])->name('aging.report.data');
    Route::post('aging-report-filter', [StudentChallanDetailsController::class, 'filterAgingReport'])->name('aging.report.filter');
    Route::post('aging-report-export', [StudentChallanDetailsController::class, 'exportAgingReport'])->name('aging.report.export');
    Route::post('aging-report-export-data', [StudentChallanDetailsController::class, 'exportAgingReportData'])->name('aging.report.export.data');
    Route::get('/get-departments/{school_id}', [StudentChallanDetailsController::class, 'getDepartments']);
    Route::get('/get-programs/{dept_id}', [StudentChallanDetailsController::class, 'getPrograms']);
    Route::post('/enrolled-students/filter', [StudentChallanDetailsController::class, 'filterEnrolledStudents'])->name('enrolled.students.filter');
    //Challan Detail Report  other routes
    Route::post('get-challan-detail', [ChallanDetailReportController::class, 'getLedger']);
    Route::post('challan-detail-bulk-pdf', [ChallanDetailReportController::class, 'bulkChallanPdfLauncher']);

    Route::get('medical-premium-report', [MedicalPremiumReportController::class, 'index']);
    Route::get('fee-waiveoff-form', [FeeWaiveOffController::class, 'fee_waiveoff_form_index'])->name('fee-waiveoff-form');
    //DONATIONS
    Route::post('add-donation-record', [DonationMasterController::class, 'addDonation']);

    Route::post('get-dashboard-data', [ScholarshipDashboardController::class, 'getDashboardData'])->name('getDashboardData');
    Route::get('get-schoolwise-count', [ScholarshipDashboardController::class, 'getSchoolwiseCounts'])->name('getSchoolwiseCounts');
    Route::get('get-schoolcount-details', [ScholarshipDashboardController::class, 'getSchoolCountDetails'])->name('getSchoolCountDetails');



    // Import Student Security Depost List
    Route::post('import-student-security-data', [GenerateConvocationBulkChallanController::class, 'importStudentSecurityData'])->name('import-student-security-data');


    //ScholarshipPolicy Routes
    Route::post('add-scholarship-policy', [ScholarshipPolicyController::class, 'addScholarshipPolicy']);

    // Fee Structures

    // Scholarship Master Forms
    Route::get('master-scholarship-type', [MasterScholarshipTypeController::class, 'masterScholarshipTypeForm']);
    ///// manual scholarship challan routes
    Route::middleware(['auth', 'verify.group.access'])->group(function () {
        Route::get('/finance/generate-single-challan', [GenerateSingleChallanController::class, 'index'])->name('finance/generate-single-challan');

        Route::get(
            '/finance/manual-scholarship-challan',
            [ManualScholarshipChallanController::class, 'index']
        )
            ->name('finance.manual-scholarship-challan');

        Route::post(
            '/finance/manual-scholarship-challan/search',
            [ManualScholarshipChallanController::class, 'searchStudent']
        )
            ->name('finance.manual-scholarship-challan.search');

        Route::post(
            '/finance/manual-scholarship-challan/store',
            [ManualScholarshipChallanController::class, 'store']
        )
            ->name('finance.manual-scholarship-challan.store');

    });

    // Fee Adjustment

    // Challan Generation

    // Installment Forms

    // Ledger & Manual Paid Mark

    // Admission Scholarship

    // Merit Challan Generation
    Route::post('submit-merit-scholarship-challan', [GenerateBulkChallanController::class, 'submitMeritScholarshipChallan']);
    Route::get('get-student-meritscholarship-challan-approved', [GenerateBulkChallanController::class, 'get_student_meritScholarship_approved']);

    // Challan Approval
    Route::get('scholarship-challan-approval', [GenerateBulkChallanController::class, 'scholarshipChallanApproval']);
    Route::post('submit-scholarship-challan-approval', [GenerateBulkChallanController::class, 'submit_scholarship_challan_approval']);
    Route::get('get-challan-approval', [GenerateBulkChallanController::class, 'get_challan_approval']);
    //// ENTRY MERIT SCHOLARSHIP RO
    Route::post('add-merit-students', [GenerateBulkChallanController::class, 'addMeritScholarshipStudents']);
    Route::get('get-student-list-merit-scholarship', [GenerateBulkChallanController::class, 'get_student_list_merit_scholarship']);
    Route::post('post-merit-scholarship-students', [GenerateBulkChallanController::class, 'postMeritScholarshipStudents']);
    // CHALLAN REPORT


    //-------------- FEE POLICY ROUTES ------------------------------------//
    Route::controller(FeePolicyController::class)->group(function () {
        Route::post('/finance/get-fee-policy', 'getPolicyRec')->name('finance/get-fee-policy');
        Route::post('/finance/submit-fee-policy', 'storeFeePolicy')->name('finance/submit-fee-policy');
        Route::delete('/finance/delete-fee-policy', 'deleteFeePolicy')->name('finance.delete-fee-policy');
    });


    //-------------- SCHOLARSHIP ASSIGNMENT ROUTES ------------------------------------//
    Route::controller(ScholarshipAssignController::class)->group(function () {
        Route::post('/finance/get-student-rec', 'getStudentRec')->name('finance/get-student-rec');
        Route::post('/finance/submit-student-scholarship', 'submitStudentScholarship')->name('finance/submit-student-scholarship');
        Route::get('/finance/delete-student-scholarship-rec', 'deleteStudentScholarshipRec')->name('finance/delete-student-scholarship-rec');
        Route::get('/finance/get-student-scholership-list', 'scholarshipStudentList')->name('finance/get-student-scholership-list');

        // Other Scholarships
        Route::get('/finance/other-scholarship-student-rec', 'getStudentProfile')->name('finance/other-scholarship-student-rec');
        Route::post('/finance/submit-other-scholarship', 'submitScholarship')->name('finance/submit-other-scholarship');
        Route::get('/finance/edit-other-scholarship/{id}', 'editScholarship')->name('finance/edit-other-scholarship');
    });
    //-------------- SCHOLARSHIP FORM (FILTER + LIST) ------------------------------------//
    Route::controller(ScholarshipFormController::class)->group(function () {
        Route::get('/finance/scholarship-bulk-post', 'postingIndex')->name('finance.scholarship-bulk-post');
        Route::post('/finance/scholarship-form/students', 'getStudents')->name('finance.scholarship-form.students');
        Route::post('/finance/scholarship-form/student-detail', 'getStudentDetail')->name('finance.scholarship-form.student-detail');
        Route::post('/finance/scholarship-form/fee-matrix', 'getFeeMatrix')->name('finance.scholarship-form.fee-matrix');
        Route::post('/finance/scholarship-form/previous-scholarships', 'getPreviousScholarships')->name('finance.scholarship-form.previous-scholarships');
        Route::post('/finance/scholarship-form/import-previous', 'importPreviousSessionStudents')->name('finance.scholarship-form.import-previous');
        Route::post('/finance/scholarship-form/search-students', 'searchActiveStudents')->name('finance.scholarship-form.search-students');
        Route::post('/finance/scholarship-form/add-student', 'resolveStudentForAward')->name('finance.scholarship-form.add-student');
        Route::post('/finance/scholarship-form/update-row', 'updateRow')->name('finance.scholarship-form.update-row');
        Route::post('/finance/scholarship-form/save-student', 'saveStudentScholarships')->name('finance.scholarship-form.save-student');
        Route::post('/finance/scholarship-form/preview-challan', 'previewChallan')->name('finance.scholarship-form.preview-challan');
        Route::post('/finance/scholarship-bulk-post/pending', 'getPendingRecords')->name('finance.scholarship-bulk-post.pending');
        Route::post('/finance/scholarship-bulk-post/staged-summary', 'getStagedSummary')->name('finance.scholarship-bulk-post.staged-summary');
        Route::post('/finance/scholarship-bulk-post/scholarship-list', 'getSessionScholarshipList')->name('finance.scholarship-bulk-post.scholarship-list');
        Route::post('/finance/scholarship-bulk-post/acknowledge-list', 'acknowledgeScholarshipList')->name('finance.scholarship-bulk-post.acknowledge-list');
        Route::post('/finance/scholarship-bulk-post/post', 'postPendingScholarships')->name('finance.scholarship-bulk-post.post');
        Route::post('/finance/scholarship-bulk-post/resync-surplus', 'resyncSurplusPending')->name('finance.scholarship-bulk-post.resync-surplus');
    });
    //-------------- Fee Waiver ROUTES ------------------------------------//
    Route::controller(FeeWaiveOffController::class)->group(function () {
        // Fee Waiver
        Route::get('fee-waive-student-rec', 'getStudentProfile')->name('fee-waive-student-rec');
        Route::post('submit-fee-waive', 'submitFeeWaive')->name('submit-fee-waive');
        Route::get('edit-fee-waiver/{id}', 'editFeeWaiver')->name('edit-fee-waiver');
    });

    //-------------- SCHOLARSHIP APPROVAL ROUTES ------------------------------------//
    Route::controller(ScholarshipAssignApprovalController::class)->group(function () {
        Route::post('/finance/update-scholarship-status', 'updateApprovalStatus')->name('finance/update-scholarship-status');
    });
    //-------------- SINGLE CHALLAN GENERATION ROUTES ------------------//
    Route::controller(GenerateSingleChallanController::class)->group(function () {
        Route::post('/finance/submit-single-challan-generate', 'submitSingleChallanGenerate')->name('finance/submit-single-challan-generate');
        Route::post('/finance/search-student-challans-available', 'searchStudentChallans')->name('finance/search-student-challans-available');
        Route::post('/finance/student-cancel-challans', 'cancelChallans')->name('finance/student-cancel-challans');
        Route::get('/finance/student-filter-challans', 'filterChallans')->name('finance/student-filter-challans');
        Route::get('/finance/student-get-fee-structure', 'getFeeStructure')->name('finance/student-get-fee-structure');
        Route::post('/finance/student-surplus-adjustments', 'getSurplusAdjustmentsByAppId')->name('finance/student-surplus-adjustments');
        Route::post('/finance/student-surplus-adjust-bulk', 'applySurplusToChallansBulk')->name('finance/student-surplus-adjust-bulk');
    });

    //-------------- STUDENT PROFILE ROUTES ------------------//
    Route::get('get-student-profile/{roll?}', [StudentRecordController::class, 'getprofileMgt'])->name('get-student-profile');
    Route::get('get-student-profile/{roll?}/{studentType?}/{session?}/{sch_type?}', [StudentRecordController::class, 'getprofileMgt']);
    // Same profile payload, but the REVISED page's box accepts an AAI id as well as an
    // enrollment no. Kept as its own route so the frozen student_record page keeps the
    // exact lookup it has always had — see StudentRecordRevisedController::getStudentProfileRevised().
    Route::get('get-student-profile-revised/{roll?}/{studentType?}/{session?}/{sch_type?}',
        [StudentRecordRevisedController::class, 'getStudentProfileRevised'])
        ->name('get-student-profile-revised');

    //-------------- BI REPORT ROUTES ------------------//
    Route::controller(BIReportController::class)->group(function () {
        Route::get('/reports/BIReport', 'BIReport')->name('reports/BIReport');
        Route::get('/reports/bi-grand-summary-school-breakdown', 'biGrandSummarySchoolBreakdown')->name('reports/bi-grand-summary-school-breakdown');
    });

    //-------------- BNU ADMISSION REPORTS ROUTES ------------------//
    Route::controller(BNUReportsGeneratorController::class)->group(function () {
        Route::get('/reports/bnu-admission-reports-generator', 'index')->name('reports/bnu-admission-reports-generator');
        Route::get('/reports/bnu-admission-reports-generator-detail', 'admissionReportData')->name('reports/bnu-admission-reports-generator-detail');
        Route::get('/get-departments/{school_id}', 'getDepartments');
        Route::get('/get-programs', 'getPrograms');
        Route::get('/get-courses', 'getCourses');
        Route::post('/get-active-students', 'getActiveStudents')->name('get-active-students');
        Route::get('/student-admission-data', 'studentAdmissionData')->name('student-admission-data');
        Route::get('/left-student-admission-data', 'getLeftStudents')->name('left-student-admission-data');
        Route::get('/student-enrollment-status-school-wise-data', 'studentsEnrollmentStatusSchoolWise')->name('student-enrollment-status-school-wise-data');
        Route::get('/enrollment-status-student-admission-school-wise-data', 'LeftStudentsSchoolWise')->name('enrollment-status-student-admission-school-wise-data');
        Route::get('/get-compare-data', 'comparePage')->name('get-compare-data');
        Route::get('/students-transferred-to-cms-admission-data', 'getStudentsTransferredToCMS')->name('students-transferred-to-cms-admission-data');
        Route::get('/compare-admission-data', 'compareAdmissionData')->name('compare-admission-data');
        Route::get('/course-attendance-report', 'courseAttendanceReport')->name('course-attendance-report');
        Route::get('/course-attendance-report-withdraw', 'courseAttendanceReportWithdraw')->name('course-attendance-report-withdraw');
        Route::get('/reports-generator-enrolled-courses', 'enrolledCourses')->name('reports-generator-enrolled-courses');
        Route::get('/reports-generator-enrolled-courses-withdraw', 'enrolledCoursesWithdraw')->name('reports-generator-enrolled-courses-withdraw');
        Route::get('/reports-generator-school-students', 'schoolWiseStudents')->name('reports-generator-school-students');
        Route::get('/reports-generator-school-students-withdraw', 'schoolWiseStudentsWithdraw')->name('reports-generator-school-students-withdraw');
        Route::get('/reports-generator-student-profile/{enrollmentNo}', 'StudentProfile')->name('reports-generator-student-profile');
        Route::post('/generate-grand-summary-report', 'generateGrandSummaryReport')->name('generate-grand-summary-report');
    });
    // Add page permissions routes
    Route::prefix('page-permissions')->group(function () {
        Route::get('/', [PagePermissionsController::class, 'index'])->name('page-permissions');
    });

    Route::controller(RfidSetupController::class)->group(function () {
        Route::get('rfid-assignment-form', 'rfidAssignmentForm')->name('rfid-assignment-form');
        Route::get('auto-search', 'autoSearch')->name('auto-search');
        Route::post('search-user', 'searchUser')->name('search-user');
        Route::post('search-user-machine', 'searchUser_fromMachine')->name('search-user-machine');
        Route::post('update-user-machine', 'updateUser_inMachine')->name('update-user-machine');
        Route::post('add-rfid', 'addRfid')->name('add-rfid');
    });
});
//AUTH END



// ────── AUTH-PROTECTED ROUTES ──────
Route::middleware(['auth', 'security.headers'])->group(function () {
    // ==============================
    // Online Exams (Teacher Side)
    // ==============================
    // done *******************************************
    Route::get('teacher/online_exam/{co_id?}/{course_id?}', [OnlineExamController::class, 'onlineexam'])->name('teacher/online_exam');
    Route::get('teacher/new_online_exam/{co_id?}', [NewOnlineExamController::class, 'newonlineexam'])->name('teacher/new_online_exam');
    Route::get('teacher/online_exam_view', [OnlineExamController::class, 'onlineexamview'])->name('teacher/online_exam_view');
    Route::get('teacher/check_paper', [OnlineExamController::class, 'checkpaper'])->name('teacher/check_paper');
    Route::post('teacher/update-exam-status', [OnlineExamController::class, 'studentpaper'])->name('teacher/update-exam-status');
    Route::get('teacher/finalexam/{id?}/{co_id?}/{sch_id}', [OnlineExamController::class, 'exam_final'])->name('teacher/finalexam');
    // done *******************************************
    Route::get('teacher/exam_view_students/{co_id?}/{course_id?}', [OnlineExamController::class, 'viewExamStudents'])->name('teacher/exam_view_students');
    Route::get('teacher/exam_view_applicants/{adm_sch_id?}', [OnlineExamController::class, 'viewAdmissionApplicants'])->name('teacher/exam_view_applicants');
    Route::get('teacher/exam_participants/{type}/{id}/{course_id?}', [OnlineExamController::class, 'examParticipantsContent'])->name('teacher/exam_participants');
    Route::get('teacher/exam_participants_pdf/{type}/{id}/{course_id?}', [OnlineExamController::class, 'exportExamParticipantsPdf'])->name('teacher/exam_participants_pdf');
    Route::get('teacher/exam_participants_excel/{type}/{id}/{course_id?}', [OnlineExamController::class, 'exportExamParticipantsExcel'])->name('teacher/exam_participants_excel');
    Route::get('teacher/applicant_list_report/{adm_sch_id}', [OnlineExamController::class, 'exportApplicantListReport'])->name('teacher/applicant_list_report');
    Route::get('teacher/applicant_password/{adm_sch_id?}/{aai_id?}', [OnlineExamController::class, 'getApplicantPassword'])->name('teacher/applicant_password');
    Route::get('teacher/send_applicant_exam_email/{adm_sch_id?}/{aai_id?}', [OnlineExamController::class, 'sendApplicantExamEmail'])->name('teacher/send_applicant_exam_email');
    // ==============================
    // Exam Questions: Add / Update / Delete
    // ==============================
    Route::post('insertexamquestion', [OnlineExamController::class, 'insertquestion'])->name('insertexamquestion');
    Route::post('updateexamquestion/{id?}', [OnlineExamController::class, 'updatequestion'])->name('updateexamquestion');
    Route::get('EditExam/{id?}', [OnlineExamController::class, 'edit_exam'])->name('EditExam');

    Route::post('teacher/add_questionbank/{co_id?}', [AddQuestionController::class, 'insert_questionbank'])->name('teacher/add_questionbank');
    Route::post('teacher/add_question/{co_id?}', [AddQuestionController::class, 'insert_question'])->name('teacher/add_question');
    Route::get('teacher/delete_question/{id?}', [AddQuestionController::class, 'delete_question'])->name('teacher/delete_question');
    Route::get('teacher/delete_examquestion/{id?}', [AddQuestionController::class, 'delete_examquestion'])->name('teacher/delete_examquestion');
    Route::post('teacher/edit_question', [AddQuestionController::class, 'ques_update'])->name('teacher/edit_question');
    Route::post('teacher/question_status', [AddQuestionController::class, 'ques_status'])->name('teacher/question_status');
    // ==============================
    // Question Banks & Paper Setup
    // ==============================
    Route::get('teacher/question_bank/{co_id?}', [AddQuestionController::class, 'question_bank'])->name('teacher/question_bank');
    Route::get('teacher/quiz_question/{co_id?}', [AddQuestionController::class, 'exam_questionbank'])->name('teacher/quiz_question');
    Route::get('teacher/view_paper/{co_id?}', [AddQuestionController::class, 'viewpaper'])->name('teacher/view_paper');
    Route::get('teacher/exam_question/{id?}/{exam_id?}', [AddQuestionController::class, 'exam_question'])->name('teacher/exam_question');
    // ==============================
    // Exam Marks, Timing & Advanced Settings
    // ==============================
    Route::get('teacher/quiz_marks/{co_id}', [AddQuestionController::class, 'marks_management'])->name('teacher/quiz_marks');
    Route::get('teacher/quiz_0_marks/{co_id}', [AddQuestionController::class, 'marks_management'])->name('teacher/quiz_0_marks');
    Route::get('teacher/time_management/{co_id?}', [AddQuestionController::class, 'time_management'])->name('teacher/time_management');
    Route::post('teacher/update_time_mgt/{id?}/{time?}', [AddQuestionController::class, 'time_exam_update'])->name('teacher/update_time_mgt');
    Route::get('teacher/update_marks/{id?}/{marks?}/{neg?}/{paper_id?}', [AddQuestionController::class, 'marks_update'])->name('teacher/update_marks');
    Route::post('advancesetting/{paper_id?}', [OnlineExamController::class, 'advance_setting'])->name('advancesetting');
    Route::post('updatequesmarks/{ceqp_id?}/{roll_no?}/{paper_id?}/{obt_marks?}', [OnlineExamController::class, 'update_ques_marks'])->name('updatequesmarks');
    Route::post('teacher/updateexamsection/{id?}/{QuizId?}', [AddQuestionController::class, 'exam_section'])->name('teacher/updateexamsection');
    Route::post('teacher/teacherremarks', [OnlineExamController::class, 'teacher_remarks'])->name('teacher/teacherremarks');
    // ==============================
    // Exam Results & Views
    // ==============================
    Route::get('viewstudentresult2/{id?}', [OnlineExamController::class, 'viewresult'])->name('viewstudentresult2');
    Route::get('viewstudentresult/{id?}', [OnlineExamController::class, 'viewresult2'])->name('viewstudentresult');
    Route::get('viewstudentresultcopy/{id?}', [OnlineExamController::class, 'viewresultcopy'])->name('viewstudentresultcopy');
    Route::get('teacher/adm_online_result/{adm_sch_id?}', [OnlineExamController::class, 'onlineresult_admission'])->name('teacher/adm_online_result');
    Route::get('teacher/online_result/{fc_co_id?}', [OnlineExamController::class, 'onlineresult'])->name('teacher/online_result');
    // ==============================
    // Downloads & CMS Transfers
    // ==============================
    Route::get('DownloadPaperWord/{id?}', [OnlineExamController::class, 'DownloadPaperWord'])->name('DownloadPaperWord');
    Route::post('move_to_cms', [OnlineExamController::class, 'move_to_cms'])->name('move_to_cms');
    Route::get('movetocms/{id?}', [OnlineExamController::class, 'tocms'])->name('movetocms');
    // ==============================
    // Attendance & Info
    // ==============================
    Route::get('checkstudentattendance/{paper_id?}/{co_id?}', [OnlineExamController::class, 'CheckStudentAttendance'])->name('checkstudentattendance');
    Route::get('getcountassessment/{assess_id?}/{co_id?}', [OnlineExamController::class, 'getcount'])->name('getcountassessment');
    Route::get('teacher/course_exam_bank/{emp_id?}', [OnlineCoursebankController::class, 'index'])->name('teacher/course_exam_bank');
    Route::get('get-emp-courses/{employee_id?}', [OnlineCoursebankController::class, 'get_courses'])->name('get-emp-courses');
    // ==============================
    // Admission Exams
    // ==============================
    Route::get('/getcount_admexam/{id}', [OnlineExamController::class, 'getadmcount'])->name('/getcount_admexam');
    Route::get('teacher/admission_header_exam/{adm_sch_id?}', [OnlineExamController::class, 'retrieveexam'])->name('teacher/admission_header_exam');
    Route::get('teacher/admission_online_exam/{adm_sch_id?}', [OnlineExamController::class, 'admissiononlineexam'])->name('teacher/admission_online_exam');
    Route::get('teacher/adm_online_exam/{adm_sch_id?}', [OnlineExamController::class, 'onlineexam_admission'])->name('teacher/adm_online_exam');
    Route::post('insert_admission_exam', [OnlineExamController::class, 'insert_adm_exam'])->name('insert_admission_exam');
    Route::get('teacher/transfer_exam_questions', [OnlineExamController::class, 'transferExamQuestionsPage'])->name('teacher/transfer_exam_questions');
    Route::get('teacher/transfer-exam/sessions', [OnlineExamController::class, 'getTransferSessions'])->name('teacher/transfer-exam/sessions');
    Route::get('teacher/transfer-exam/schools', [OnlineExamController::class, 'getTransferSchools'])->name('teacher/transfer-exam/schools');
    Route::get('teacher/transfer-exam/departments/{school_id}', [OnlineExamController::class, 'getTransferDepartments'])->name('teacher/transfer-exam/departments');
    Route::get('teacher/transfer-exam/programs/{school_id}/{dept_id}', [OnlineExamController::class, 'getTransferPrograms'])->name('teacher/transfer-exam/programs');
    Route::get('teacher/transfer-exam/session-programs/{sess_id}/{school_id}/{dept_id}', [OnlineExamController::class, 'getTransferSessionPrograms'])->name('teacher/transfer-exam/session-programs');
    Route::get('teacher/transfer-exam/admission-schedules', [OnlineExamController::class, 'getTransferAdmissionSchedules'])->name('teacher/transfer-exam/admission-schedules');
    Route::get('teacher/transfer-exam/exam-papers/{sch_id}', [OnlineExamController::class, 'getTransferExamPapers'])->name('teacher/transfer-exam/exam-papers');
    Route::get('teacher/transfer-exam/preview', [OnlineExamController::class, 'getTransferPreview'])->name('teacher/transfer-exam/preview');
    Route::post('teacher/transfer-exam/execute', [OnlineExamController::class, 'executeTransferExamQuestions'])->name('teacher/transfer-exam/execute');
    // ==============================
    // Exam Paper Header & IP Access
    // ==============================
    Route::get('exam/header/{co_id?}', [ExamPaperController::class, 'header'])->name('exam/header');
    Route::get('exam/allow-exam-ips', [ExamPaperController::class, 'masterFormExamAllow'])->name('exam/allow-exam-ips');
    Route::post('exam/update-allow-exam-ips', [ExamPaperController::class, 'updateAllowExamIps'])->name('exam/update-allow-exam-ips');
    // ==============================
    // Student Document Verification & Management
    // ==============================
    Route::controller(StudentDocumentsController::class)->group(function () {
        Route::get('/doc-verifications', 'documentVerification')->name('doc-verifications');                          // View verification dashboard
        Route::get('/admission_qualification', 'admissionQualification')->name('admission_qualification');            // View admission qualification
    });
    // ==============================
    // University Holidays Management
    // ==============================
    Route::controller(EportalHolidaysController::class)->group(function () {
        Route::get('/university-holidays-management', 'index')->name('university-holidays-management');         // Holiday listing
        Route::post('/submit-university-holidays-management', 'submitHoliday')->name('university-holidays-management'); // Add or update holidays
    });
    // ==============================
    // Student Mentorship (Peer Mentoring)
    // ==============================
    Route::controller(StudentMentorshipController::class)->group(function () {
        Route::get('/peer-mentorship', 'RequestIndex')->name('peer-mentorship.index');                           // Mentorship dashboard
        Route::post('/peer-mentorship-submit-courses', 'submitRequest')->name('peer-mentorship-submit-courses'); // Submit mentorship course request
        Route::post('/peer-mentorship/request-courses')->name('peer-mentorship.request-courses');                // Get available mentorship courses
        Route::post('/peer-mentorship/schedule-timming')->name('peer-mentorship.schedule-timming');              // Schedule mentorship timing
        Route::post('/send-intimation', 'mentorIntimation')->name('send-intimation');                            // Notify mentor/student
    });
    // ==============================
    // Student Profile & Widgets
    // ==============================
    Route::controller(StudentProfileController::class)->group(function () {
        // Route::get('/student-profile-rec', 'index')->name('student-profile-rec');                          // Student profile dashboard
        Route::get('/fetch-all-student-widget-link', 'fetchAllStudentWidgetLink')->name('fetch-all-student-widget-link'); // Fetch all widget links
        Route::get('/live-search-student', 'liveSearch')->name('live-search-student');                     // Live search student
    });
    // ==============================
    // Dean Honor & Merit List Management
    // ==============================
    Route::controller(DeanHonorController::class)->group(function () {
        // Route::get('/dean-honor', 'index')->name('dean-honor');                                                    // Dean honor index
        Route::get('/update-test-slip-date', 'updateTestSlip')->name('update-test-slip-date');                     // Update test slip date

        Route::get('/filter-test-slip-records', 'filterTestSlipRecords')->name('filter-test-slip-records');

        Route::get('/dean-honor-student-information', 'getStudentInformation')->name('dean-honor-student-information'); // Fetch honor student info
        Route::get('/student-merit-list', 'getapplicantInformation')->name('student-merit-list');                  // Get merit list
        Route::post('/submit-deanhonor-list', 'submitDeanHonorList')->name('submit-deanhonor-list');              // Submit honor list (version 1)
        Route::post('/submit-deanhonor-list2', 'submitDeanHonorList2')->name('submit-deanhonor-list2');           // Submit honor list (version 2)
        Route::post('/approve-deanhonor-list', 'approveDeanHonorList')->name('approve-deanhonor-list');           // Approve honor list
        Route::post('/dean-honor-oldData', 'getDeanHonor_oldData')->name('dean-honor-oldData');                    // Fetch old honor data
        Route::post('/dean-honor-newData', 'getDeanHonor_newData')->name('dean-honor-newData');                    // Fetch new honor data
    });
    // ==============================
    // Survey Module (Library & Job Description)
    // ==============================
    Route::controller(SurveyController::class)->group(function () {
        Route::get('/library-survey', 'librarysurveyPage')->name('library-survey');                                     // Library survey page
        Route::post('/submit-library-survey', 'librarySurvey')->name('submit-library-survey');                          // Submit library survey
        Route::post('/set-survey-flag', 'setSurveyFlag')->name('set-survey-flag');                                      // Set survey flag

        Route::post('/set-job-description-survey-flag', 'setJobDescriptionSurveyFlag')->name('set-job-description-survey-flag'); // Set job survey flag
        Route::get('/job-description/form', 'jobDescriptionForm')->name('job-description.form');                        // Job description form
        Route::post('/survey/job-description', 'storeJobDescription')->name('survey/job-description');



        // Progressive Form Saving Routes
        Route::post('/survey/save-section-a', 'saveSectionA')->name('survey.save-section-a');                           // Save Section A
        Route::post('/survey/save-section-b', 'saveSectionB')->name('survey.save-section-b');                           // Save Section B
        Route::post('/survey/save-section-c', 'saveSectionC')->name('survey.save-section-c');                           // Save Section C
        Route::post('/survey/save-section-d', 'saveSectionD')->name('survey.save-section-d');                           // Save Section D
        Route::post('/survey/save-section-e', 'saveSectionE')->name('survey.save-section-e');                           // Save Section E
        Route::get('/survey/get-section-data/{section}', 'getSectionData')->name('survey.get-section-data');
    });
    // ==============================
    // Allowed Inspect User Management
    // ==============================
    Route::controller(AllowedInspectUserController::class)->group(function () {
        Route::get('/allowed-inspect-users', 'index')->name('allowed.inspect.users');               // List allowed users
        Route::post('/store-allowed-inspect-user', 'store')->name('store.allowed.inspect.user');    // Add user to allowed list
        Route::delete('/delete-allowed-inspect-user/{id}', 'delete')->name('delete.allowed.inspect.user'); // Delete allowed user
    });
    // ==============================
    // Error Logs Viewer
    // ==============================
    Route::controller(ErrorLogsController::class)->group(function () {
        Route::get('/error-logs', 'index')->name('error-logs');             // View system errors
        Route::get('/error-logs-clear', 'index')->name('error.logs.clear'); // Clear error logs (same method used here)
    });
    // ==============================
    // Exam Section Management
    // ==============================
    Route::controller(ExamSectionController::class)->group(function () {
        Route::get('/exam-sections', 'index')->name('exam.sections');                    // List sections
        Route::post('/manage-exam-section', 'manageExamSection')->name('manage.exam.section'); // Add/edit exam section
        Route::post('/delete-exam-section', 'deleteExamSection')->name('delete.exam.section'); // Delete section
    });

    // ==============================
    // External Events Managment
    // ==============================
    Route::controller(EventsMasterController::class)->group(function () {
        Route::get('event-master-form', [EventsMasterController::class, 'eventMasterForm']);
        Route::post('add-event', [EventsMasterController::class, 'addEvent']);
        Route::get('event-registrations', [EventsRegistrationsController::class, 'eventRegistrations']);
    });

    // Assignment Update and check route

    Route::get('EditAssessment/{id?}', [FacultyCoursesController::class, 'EditAssessment'])->name('EditAssessment');
    Route::get('checkAssessment/{co_id?}/{assessment_id?}', [FacultyCoursesController::class, 'checkAssessment'])->name('checkAssessment');



    // ==============================
    // NBS Form Management
    // ==============================

    // Route::middleware(['verify.group.access'])->group(function () {
    Route::middleware([])->group(function () {


        // Exit Form Report Routes
        Route::controller(App\Http\Controllers\Grader\ExitFormReportController::class)->group(function () {
            Route::get('/exit-form-report', 'index')->name('grader.exit-form-report.index');
            Route::get('/student-staff-exit-form-report', 'studentStaffExitReport')->name('student-staff-exit-form-report');
            Route::get('/staff-exit-form', 'faculty_comments')->name('staff-exit-form');
            Route::get('/staff-exit-form-details', 'faculty_comments_overall')->name('staff-exit-form-details');
            Route::get('/exit-form-report/overall', 'overallReport')->name('exit-form-report');
            Route::get('/exit-form-report/{id}', 'show')->name('grader.exit-form-report.show');
            Route::get('/exit-form-report/{id}/complete', 'completeReport')->name('grader.exit-form-report.complete');
            Route::get('/exit-form-report/export/{format?}', 'export')->name('grader.exit-form-report.export');
        });

        // ==============================
        // NBS Form Management
        // ==============================
        Route::controller(NBSFormController::class)->group(function () {
            Route::get('/nbs-ro-approval', 'index')->name('nbs-ro-approval');
            Route::get('/nbs-admission-approval', 'index_admission')->name('nbs-admission-approval'); // New route
            Route::get('/nbs-hod-approval', 'hod_approval')->name('nbs-hod-approval');
            Route::get('/nbs-form-update-profile', 'updateProfileIndex')->name('nbs-form-update-profile');
            // ✅ NEW ROUTE (POST)
            Route::post('/nbs-final-percentage', 'saveFinalPercentage')->name('nbs-final-percentage');
        });

        //-------------- STUDENT PROFILE ROUTES ------------------//
        Route::controller(StudentProfileController::class)->group(function () {
            Route::get('/student-profile-rec', 'index')->name('student-profile-rec');                                             // Live search student
        });



        Route::controller(AcademicSessionController::class)->group(function () {
            Route::get('/academic-session', 'index2')->name('academic-session');
        });

        Route::controller(DeanHonorController::class)->group(function () {
            Route::get('/dean-honor', 'index')->name('dean-honor');                                                    // Dean honor index
        });

        //-------------- Middleware DB Data Routes ------------------------------------//
        Route::controller(StudentFineController::class)->group(function () {
            Route::get('/students_fee_challan', 'students_fee_challan')->name('students_fee_challan');
        });
    });
});

//////////////////////
////////////
///task Management working//////////////
Route::middleware(['auth'])->group(function () {

    Route::get('/admission-survey', [AdmissionSurveyController::class, 'index'])->name('admission.survey');

    Route::get("UniversityEvent/register", [UniversityEventController::class, 'register']);
    Route::get("UniversityEvent/calendar", [UniversityEventController::class, 'viewInCalendar']);
    Route::get("UniversityEvent/event-approval", [UniversityEventController::class, 'eventApproval']);

    Route::get('/faculty/course_offering', [FacultyReportsController::class, 'course_offering']);
    Route::get('task-management/get', [TaskManagementController::class, 'getData']);
    Route::get('task-management/getCategories', [TaskManagementController::class, 'getTaskCategory'])->name('getTaskCategory');

    Route::get('/staff/ProjectBoard', [TaskManagementController::class, 'ProjectBoard']);
    Route::get('/staff/task-list2', [TaskManagementController::class, 'dashboard2']);
    Route::post('/staff/updateTask', [TaskManagementController::class, 'updateTask'])->name('updateTask');
    Route::get('/staff/deleteTask', [TaskManagementController::class, 'deleteTask'])->name('deleteTask');
    Route::get('/staff/addDraft', [TaskManagementController::class, 'addDraft'])->name('addDraft');
    Route::post('/staff/updateTaskRow', [TaskManagementController::class, 'updateTaskRow'])->name('updateTaskRow');
    Route::get('/staff/editTask', [TaskManagementController::class, 'editTask'])->name('editTask');
    Route::get('/staff/rollBack', [TaskManagementController::class, 'rollBack'])->name('rollBack');
    Route::get('/staff/task-listing', [TaskManagementController::class, 'createTask'])->name('task-listing');
    Route::get('/staff/taskBoard', [TaskManagementController::class, 'taskBoard']);
    Route::get('/staff/taskBoard2', [TaskManagementController::class, 'taskBoard2']);
    Route::get('/staff/gettaskboard', [TaskManagementController::class, 'getTaskBoard'])->name('staff/gettaskboard');
    Route::post('/staff/updateTaskAll', [TaskManagementController::class, 'updateTaskAll'])->name('updateTaskAll');
    Route::post('/staff/updateTaskAllTodo', [TaskManagementController::class, 'updateTaskAllTodo'])->name('updateTaskAllTodo');
    Route::post('/staff/updateTaskAllPersonalTodo', [TaskManagementController::class, 'updateTaskAllPersonalTodo'])->name('updateTaskAllPersonalTodo');
    Route::post('/staff/updateTaskAllImmediateTodo', [TaskManagementController::class, 'updateTaskAllImmediateTodo'])->name('updateTaskAllImmediateTodo');
    Route::get('/staff/dashboard_demand', [TaskManagementController::class, 'dashboard_demand']);
    Route::get('/staff/todo-list', [TaskManagementController::class, 'TodoDashboard']);
    Route::get('/staff/personal_todo-list', [TaskManagementController::class, 'personalTodoDashboard']);
    Route::get('/staff/immediate_todo-list', [TaskManagementController::class, 'immediateTodoDashboard']);
    Route::post('tododboard_submit', [TaskManagementController::class, 'todostore']);
    Route::post('fetch_staff_data', [TaskManagementController::class, 'fetchStaffData'])->name('fetch_staff_data');
    Route::get('/fetch-tasks', [TaskManagementController::class, 'fetchTasks'])->name('fetch-tasks');
    Route::get('fetch_todo_list', [TaskManagementController::class, 'fetch_todo_list']);
    Route::post('/staff/update-task-status', [TaskManagementController::class, 'updateTaskStatus'])->name('staff/update-task-status');
    // Route::post('/staff/assign-to-message', [TaskManagementController::class,'SendMsg'])->name('staff/assign-to-message');
    // use App\Http\Controllers\Grader\vcReports\VcReportsController;
    Route::post('/staff/send-message-to-user', [TaskManagementController::class, 'sendMsg'])->name('staff/assign-to-message');
    Route::get('/staff/get-user-messages', [TaskManagementController::class, 'getUserMessages'])->name('staff/get-user-messages');
    Route::get('/user-messages', [TaskManagementController::class, 'userMessages'])->name('user-messages');
    Route::get('/staff/tmsortOrder', [TaskManagementController::class, 'tmsortOrder'])->name('tmsortOrder');
    // Route::get('get-student-profile/{roll?}', [StudentRecordController::class,'getprofileMgt'])->name('get-student-profile');
    // Route::get('get-student-profile/{roll?}/{studentType?}', [StudentRecordController::class,'getprofileMgt'])->name('get-student-profile');
    Route::get('verify-student-cgpa/{roll?}/{session?}', [StudentRecordController::class, 'verifyStudentCgpa'])->name('verify-student-cgpa');
    Route::post('/staff/delete-selected-todo', [TaskManagementController::class, 'deleteSelected'])->name('delete-selected-todo');

    // Laptop Maintenance Routes
    Route::get('/laptop-maintenance/admin', [LaptopMaintenanceController::class, 'adminIndex'])->name('laptop-maintenance.admin');
    Route::post('/laptop-maintenance/mark-complete', [LaptopMaintenanceController::class, 'markComplete'])->name('laptop-maintenance.mark-complete');
    Route::get('/laptop-maintenance/request/{id}', [LaptopMaintenanceController::class, 'getRequestDetails'])->name('laptop-maintenance.request.details');

    //Staff Harassment Form Routes
    Route::get('/staff-harassment/admin', [StaffSurveyController::class, 'harassmentIndex'])->name('staff-harassment.admin');
    Route::post('/staff-harassment/mark-complete', [StaffSurveyController::class, 'harassmentMarkComplete'])->name('staff-harassment.mark-complete');
    Route::get('/admin/download-file/{id}', [StaffSurveyController::class, 'downloadFile'])->name('admin.file.download');
    // Route::get('/staff-harassment/request/{id}', [StaffSurveyController::class, 'harassmentRequestDetails'])->name('staff-harassment.request.details');

    Route::get('staff-harassment-form', [StaffSurveyController::class, 'index'])->name('staff-harassment-form');
    Route::match(['get', 'post'], '/staff-harassment-form-new', [StaffSurveyController::class, 'create'])->name('staff-harassment-form-new');
    Route::post('staff-harassment-submit', [StaffSurveyController::class, 'store'])->name('staff.harassment.submit');
    Route::get('staff-harassment-details/{id}', [StaffSurveyController::class, 'show'])->name('staff.harassment.details');

    Route::get('/student-surveys/admin', [SurveyAdminController::class, 'allSurveysAdmin'])
        ->name('student-surveys.admin');

    Route::get('/student-surveys/admin/data', [SurveyAdminController::class, 'manageSurveysData'])
        ->name('student-surveys.admin.data');

    Route::get('/student-surveys/admin/{id}', [SurveyAdminController::class, 'manageSurveysShow'])
        ->name('student-surveys.admin.show');

    Route::put('/student-surveys/admin/{id}', [SurveyAdminController::class, 'manageSurveysUpdate'])
        ->name('student-surveys.admin.update');

    Route::delete('/student-surveys/admin/{id}', [SurveyAdminController::class, 'manageSurveysDestroy'])
        ->name('student-surveys.admin.destroy');

    // Product Assignment Routes
    Route::prefix('product-assignments')
        ->name('product_assignments.')
        ->group(function () {

            Route::get('/', [ProductAssignmentController::class, 'productAssignmentIndex'])->name('index');
            Route::get('/data', [ProductAssignmentController::class, 'getData'])->name('data');
            Route::post('/', [ProductAssignmentController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [ProductAssignmentController::class, 'edit'])->name('edit');
            Route::put('/{id}', [ProductAssignmentController::class, 'update'])->name('update');
            Route::delete('/{id}', [ProductAssignmentController::class, 'destroy'])->name('destroy');
            Route::get('/product-auto-search', [ProductAssignmentController::class, 'productAutoSearch'])->name('product-auto-search');
            Route::post('/product-search-user', [ProductAssignmentController::class, 'productSearchUser'])->name('product-search-user');
        });

    // Product Entry Routes
    Route::prefix('product-entry')->name('product_entry.')->group(function () {
        Route::get('/', [ProductEntryController::class, 'productEntryIndex'])->name('index');
        Route::get('/data', [ProductEntryController::class, 'getData'])->name('data');
        Route::post('/', [ProductEntryController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [ProductEntryController::class, 'edit'])->name('edit');
        Route::put('/{id}', [ProductEntryController::class, 'update'])->name('update');
        Route::delete('/{id}', [ProductEntryController::class, 'destroy'])->name('destroy');
    });

    //student exchange program admin routes
    Route::get('/student-exchange-admin', [StudentExchangeController::class, 'exchangeIndex'])->name('student-exchange-admin');
    Route::post('/student-exchange-mark-complete', [StudentExchangeController::class, 'exchangeMarkComplete'])->name('student-exchange-mark-complete');
    Route::get('/admin/exchange/{id}/pdf', [StudentExchangeController::class, 'exchangeApplicationPdf'])->name('admin.exchange.pdf');
    // Route::get('/student-exchange-request-details/{id}', [StaffSurveyController::class, 'exchangeRequestDetails'])->name('student-exchange-request-details');

    //Staff Overtime Form Routes
    Route::get('overtime-form', [App\Http\Controllers\StaffOvertimeController::class, 'index'])->name('overtime-form');
    Route::get('overtime-approval', [App\Http\Controllers\StaffOvertimeController::class, 'approval'])->name('overtime-approval');
    Route::post('overtime-form-submit', [App\Http\Controllers\StaffOvertimeController::class, 'store'])->name('overtime-form-submit');
    Route::post('overtime-update-status', [App\Http\Controllers\StaffOvertimeController::class, 'updateStatus'])->name('overtime-update-status');
    Route::get('overtime-export-pdf', [App\Http\Controllers\StaffOvertimeController::class, 'exportPdf'])->name('overtime-export-pdf');
    Route::get('overtime-export-excel', [App\Http\Controllers\StaffOvertimeController::class, 'exportExcel'])->name('overtime-export-excel');
    Route::get('/university-report', [UniversityReportController::class, 'index'])
        ->name('university.report');
    Route::get('/fee-challan-report', [UniversityReportController::class, 'challanReport']);
});
