<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Grader\AcademicSessions;
use App\Http\Controllers\Grader\LectureScheduling\TimeTableController;
use App\Http\Controllers\Grader\VisitingFacultyController;
use App\Http\Controllers\Grader\ShortCourses\ShortCoursesController;
use App\Http\Controllers\Grader\UniversityEvent\UniversityEventController;
use App\Http\Controllers\Grader\Notification\NotificationController;
use App\Http\Controllers\Grader\FacultyCoursesController;
use App\Http\Controllers\Grader\CourseContentController;
use App\Http\Controllers\Grader\CourseAttendanceController;
use App\Http\Controllers\Grader\Lecture\LectureContentController;
use App\Http\Controllers\Grader\Chat\ChatController;
use App\Http\Controllers\Grader\GradingController;
use App\Http\Controllers\Grader\OnlineExamController;
use App\Http\Controllers\Grader\LectureScheduling\StudyMaterialController;
use App\Http\Controllers\Grader\LectureScheduling\EvaluationController;
use App\Http\Controllers\Grader\CourseAssessmentMarksController;
use App\Http\Controllers\Grader\AcademicSessionController;
use App\Http\Controllers\Grader\Faculty\FacultyReportsController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Notification\NotiAddRemoveController;
use App\Http\Controllers\Grader\EnrollmentStartStopController;
use App\Http\Controllers\Grader\GoogleDrive\GoogleDrivesController;
use App\Http\Controllers\Grader\StudentRecord\StudentRecordController;
use App\Http\Controllers\Grader\StudentRecord\StudentRecordRevisedController;
use App\Http\Controllers\Grader\Financial\StundentsFinancialController;
use App\Http\Controllers\CarPullingController;
use App\Http\Controllers\Grader\EmpEmployeesController;
use App\Http\Controllers\Grader\TaskManagement\TaskManagementController;
use App\Http\Controllers\Grader\Eportal\EportalController;
use App\Http\Controllers\StudentFinancial\FeeStructureController;
use App\Http\Controllers\StudentFinancial\GenerateBulkChallanController;
use App\Http\Controllers\StudentFinancial\GenerateCreditHrBulkChallanController;
use App\Http\Controllers\StudentFinancial\StudentCreditsDataController;
use App\Http\Controllers\StudentFinancial\FeeInstallmentController;
use App\Http\Controllers\StudentFinancial\MasterScholarshipController;
use App\Http\Controllers\StudentFinancial\ScholarshipCriteriaController;
use App\Http\Controllers\StudentFinancial\ScholarshipPolicyController;
use App\Http\Controllers\StudentFinancial\AdmissionScholarshipCriteriaController;
use App\Http\Controllers\StudentFinancial\AdmissionIndividualScholarshipCriteriaController;
use App\Http\Controllers\StudentFinancial\AdmissionScholarshipApprovalController;
use App\Http\Controllers\StudentFinancial\FeeAdjustmentController;
use App\Http\Controllers\StudentFinancial\MiddlewareChallanRectificationController;
use App\Http\Controllers\StudentFinancial\MasterScholarshipTypeController;
use App\Http\Controllers\StudentFinancial\DonationMasterController;
use App\Http\Controllers\StudentFinancial\CourseExemptedController;
use App\Http\Controllers\ACL\ApiController;
use App\Http\Controllers\Grader\PeerMentorship\PeerMentorshipController;
use App\Http\Controllers\StudentFinancial\HostelFeeStructureController;
use App\Http\Controllers\StudentFinancial\ChallanReportController;
use App\Http\Controllers\StudentFinancial\ChallanSummaryReportController;
use App\Http\Controllers\Grader\BNUReportsGenerate\StudentProfileDetailsController;
use App\Http\Controllers\Grader\PagePermissionsController;
use App\Http\Controllers\Grader\StudentDocumentsController;
use App\Http\Controllers\Grader\scholarship\NBSFormController;
use App\Http\Controllers\Grader\HodReportController;
use App\Http\Controllers\Grader\StudentFines\StudentFineController;
use App\Http\Controllers\ACL\RfidSetupController;
use App\Http\Controllers\Grader\Hostel\HostelController;
use App\Http\Controllers\Grader\Hostel\HostelStatsDashboardController;
use App\Http\Controllers\Grader\Hostel\HostelSummaryController;
use App\Http\Controllers\Grader\Hostel\HostelBedAssignmentController;
use App\Http\Controllers\Grader\Reports\ReportsGeneratorController;
use App\Http\Controllers\Grader\GeneralEvents\EventsMasterController;
use App\Http\Controllers\Grader\GeneralEvents\EventsRegistrationsController;
use App\Http\Controllers\StudentFinancial\GenerateConvocationBulkChallanController;
use App\Http\Controllers\StudentFinancial\StudentChallanDetailsController;
use App\Http\Controllers\StudentFinancial\EnrollmentSummaryReportController;





Route::POST('/visiting_faculty/get_departments', [VisitingFacultyController::class, 'getDepartments']);
Route::POST('/visiting_faculty/get_programs', [VisitingFacultyController::class, 'getPrograms']);
Route::POST('/visiting_faculty/get_courses', [VisitingFacultyController::class, 'getCourses']);
Route::POST('/visiting_faculty/get_faculty_list', [VisitingFacultyController::class, 'getFacultyList']);
Route::POST('/visiting_faculty/store', [VisitingFacultyController::class, 'store']);

Route::post('studentFileupload', [StudyMaterialController::class, 'FileUploadStudent']);
Route::post('/update_assessmentmarks_all', [CourseAssessmentMarksController::class, 'update_marks_all']);

Route::post('teacher-notification', [ChatController::class, 'fcm']);

//access token
Route::get('faculty/access-token', [AcademicSessionController::class, 'updateToken']);
Route::post('/get-student-profile_cr', [StudentRecordController::class, 'getProfile2']);
//-------------------- Academic session Dedails ------------------------//
//june 2025

Route::post('academic_session_store', [AcademicSessionController::class, 'store']);
Route::post('get-sessions', [AcademicSessionController::class, 'getSessions2']);
Route::post('/academic-session/{id}/delete', [AcademicSessionController::class, 'destroy']);
Route::get('/academic-session/edit/{id}', [AcademicSessionController::class, 'edit']);
Route::get('/get-calendar-details/{id}', [AcademicSessionController::class, 'getCalendarDetails']);

Route::post('/update-date/{id}', [AcademicSessionController::class, 'update']);

//-------------------- Academic session Dedails END------------------------//
// Task Management All Routes
Route::get('task-management/get', [TaskManagementController::class, 'getData']);
Route::get('task-management/getCategories', [TaskManagementController::class, 'getTaskCategory'])->name('getTaskCategory');

Route::get('send-otp', [NotificationController::class, 'sendMailOtpAdmission']);

Route::POST('/short_courses/store', [ShortCoursesController::class, 'store']);
Route::POST('/short_courses/delete_course', [ShortCoursesController::class, 'delete_course']);
Route::POST('/short_courses/getEmail', [ShortCoursesController::class, 'getEmail']);
Route::POST('/short_courses/updateDiscount', [ShortCoursesController::class, 'updateDiscount']);
Route::POST('/short_courses/get_short_courses_list', [ShortCoursesController::class, 'getShortCoursesList']);
Route::POST('/short_courses/get_applicants_list', [ShortCoursesController::class, 'getApplicantsList']);
Route::POST('/short_courses/approval', [ShortCoursesController::class, 'approval']);
Route::POST('/short_courses/paidMark', [ShortCoursesController::class, 'paidMark']);
Route::POST('/short_courses/updateIndDiscount', [ShortCoursesController::class, 'updateIndDiscount']);
Route::POST('/short_courses/updateIndapproval', [ShortCoursesController::class, 'updateIndapproval']);
Route::POST('/short_courses/delete_applicant', [ShortCoursesController::class, 'deleteApplicant']);

Route::prefix('api')->group(function () {
    Route::get('/get-mentors', [PeerMentorshipController::class, 'getMentors']);
    Route::get('/get-mentor-details', [PeerMentorshipController::class, 'getMentorDetails']);
});
Route::get('get-student-challan-data', [StudentRecordController::class, 'getStudentChallanData']);
Route::get('get-courses-offered', [StudentRecordController::class, 'getOfferedCourses']);

Route::post('update-hostel-type', [StudentRecordController::class, 'updateHoltelType']);


Route::group(['middleware' => ['auth:api']], function () {
    Route::get("get-student-enrolled-courses", [ReportsGeneratorController::class, 'getStudentEnrolledCoursesData']);
    //event management
    Route::get("UniversityEvent/event-list", [UniversityEventController::class, 'eventList']);
    Route::delete("UniversityEvent/delete", [UniversityEventController::class, 'eventDelete']);
    Route::post("UniversityEvent/other-event-list", [UniversityEventController::class, 'checkOtherEvents']);
    Route::post("UniversityEvent/update-event-status", [UniversityEventController::class, 'updateEventStatus']);
    Route::get("UniversityEvent/event-approval", [UniversityEventController::class, 'eventApproval']);

    Route::post("UniversityEvent/store", [UniversityEventController::class, 'store']);
    Route::post("UniversityEvent/list", [UniversityEventController::class, 'index']);
    Route::post("UniversityEvent/get-room-list", [UniversityEventController::class, 'getRoomList']);

    Route::post('insert-car-pulling', [CarPullingController::class, 'insertRequest']);
    Route::post('update-car-pulling2', [CarPullingController::class, 'updateRequest']);
    Route::post('update-Capacity', [CarPullingController::class, 'updateCapacity']);
    Route::post('remove-Capacity', [CarPullingController::class, 'removeCapacity']);
    Route::get('get-car-pulling', [CarPullingController::class, 'getRequest']);
    Route::get('get-car-pulling2', [CarPullingController::class, 'getRequest2']);
    Route::post('car-pulling-message', [CarPullingController::class, 'sendChat']);
    Route::get('get-car-pulling-message', [CarPullingController::class, 'getMessage']);
    Route::post('car-pulling-timings', [CarPullingController::class, 'car_pulling_timings']);
    Route::get('get-car-pulling-inbox', [CarPullingController::class, 'getMessage']);
    Route::get('edit-car-pulling', [CarPullingController::class, 'editCarPull']);
    Route::get('/get-user-profile/{roll_no}', [CarPullingController::class, 'getUserProfile']);
    Route::post('/save-car_registration', [CarPullingController::class, 'insertRecord']);
    Route::post('/del-car-registration', [CarPullingController::class, 'delRecord']);
    Route::post('/upd-car-registration-parking', [CarPullingController::class, 'updParkingStatus']);
    Route::get('get-car-registration', [CarPullingController::class, 'getCarRegistration']);
    Route::post('pooling_request_aceept_cancel', [CarPullingController::class, 'pooling_request_aceept_cancel']);
    Route::get('get-direct-car-registration', [CarPullingController::class, 'getDirectCarRegistration']);
    Route::get('edit-direct-car-registration', [CarPullingController::class, 'editDirectCarRegistration']);
    Route::post('/save-direct-car_registration', [CarPullingController::class, 'insertDirectRecord']);
    Route::post('/update-direct-car_registration', [CarPullingController::class, 'updateDirectRecord']);



    Route::post('car-pooling-feedback', [CarPullingController::class, 'addFeedback']);

    Route::post('file-upload', [GoogleDrivesController::class, 'UploadFile']);

    Route::post('teacher-insert-message', [NotificationController::class, 'insertMessage']);
    Route::post('teacher-coursecontent', [CourseContentController::class, 'course_contentPost']);
    Route::post('teacher-coursecontent/desc', [CourseContentController::class, 'course_DescPost']);
    Route::post('teacher-uploadFile', [CourseContentController::class, 'course_filePost']);
    Route::get('teacher-removeFile/{id?}', [CourseContentController::class, 'course_fileremove']);

    Route::prefix('/attendance')->group(function () {
        Route::get('attendance/{co_id}', [LectureController::class, 'course_attendance_index']);
        Route::post('final-submit-attendance/{mid}', [CourseAttendanceController::class, 'final_submit_attendance']);
    });
    Route::get('/timetable/faculty', [TimeTableController::class, 'getTimeTable_Data']);

    Route::prefix('/cms-guidline')->group(function () {
        Route::get('get-data', [TimeTableController::class, 'getGuidline']);
    });
    Route::get('/get-sessions', [AcademicSessionController::class, 'getsessions']);
    Route::get('/timetable/student', [TimeTableController::class, 'getStudentTimeTable_Data']);
    Route::post('create-otp', [FacultyCoursesController::class, 'createCode']);
    Route::post('update-details', [FacultyCoursesController::class, 'UpdateDetails']);

    // "course-content" Its should replace with "lecture-content"
    // Route::prefix('/course-content')->group(function () {
    //     Route::post('upload-file', [CourseContentController::class,'uploadFileWithApi'] );
    //     Route::get('get', [CourseContentController::class,'getCourseContent'] );
    //     Route::post('update-content', [CourseContentController::class,'updateLectureContent'] );
    // });

    Route::prefix('/uploads-video')->group(function () {

        Route::post('add_faculity_course_material/{co_id?}/{id?}', [StudyMaterialController::class, 'add_faculity_course_material_by_id']);
        Route::post('remove_faculity_course_material/{co_id?}/{id?}', [StudyMaterialController::class, 'remove_faculity_course_material_by_id']);
    });

    Route::prefix('/lecture-content')->group(function () {
        Route::post('fileupload_google', [StudyMaterialController::class, 'uploadFileGoogle']);
        Route::post('upload-file', [StudyMaterialController::class, 'uploadFileWithApi']);

        #TODO please check what the issue with this
        Route::get('get', [CourseContentController::class, 'getCourseContent']);
        Route::get('get-lecture-content', [CourseContentController::class, 'getCourseContentMain']);


        Route::post('add-content', [LectureContentController::class, 'addLectureContent']);
        Route::post('update-content', [LectureContentController::class, 'updateLectureContent']);
    });

    Route::prefix('notification')->group(function () {
        Route::post('send', [NotificationController::class, 'sendNotification']);
        Route::get('get', [NotificationController::class, 'getMessageNotifi']);
        Route::get('get-by-last-message-id', [NotificationController::class, 'checkMessageByLastMsg']);
    });
    Route::prefix('message')->group(function () {
        Route::post('send', [ChatController::class, 'sendMessage']);
        Route::get('get', [ChatController::class, 'getMessage']);
        Route::get('get-by-last-message-id', [ChatController::class, 'checkMessageByLastMsg']);
        Route::post('read', [ChatController::class, 'readMessage']);
        Route::get('course_message_count', [ChatController::class, 'course_message_count']);
        Route::post('update', [ChatController::class, 'updateMessage']);
        Route::post('reply', [ChatController::class, 'replyMessage']);
        Route::post('question', [ChatController::class, 'questionMessage']);
    });

    // REPORTS WORK
    Route::get('department/get', [FacultyReportsController::class, 'getDepartment']);
    Route::get('program/get', [FacultyReportsController::class, 'getProgram']);
    Route::get('teacher/get', [FacultyReportsController::class, 'getTeacher']);
    Route::get('teacher_courses/get', [FacultyReportsController::class, 'getCoursesByTeacher']);

    Route::prefix('exam')->group(function () {
        Route::post('create-exam', [OnlineExamController::class, 'InsertExam'])->name('create-exam');
        Route::post('update-exam/{id?}', [OnlineExamController::class, 'UpdateExam'])->name('update-exam');
        Route::get('EditExam/{id?}', [OnlineExamController::class, 'edit_exam'])->name('EditExam');
    });



    Route::prefix('course')->group(function () {
        Route::get('get-student-by-course', [ChatController::class, 'getStudentByCourse']);
    });


    Route::get('get-course-grading-process-details/{co_id?}', [GradingController::class, 'get_process_details']);
    // Updating Course Assessment Marks
    Route::get('get-course-course-assessment-count/{co_id?}/{assessment_id?}', [EvaluationController::class, 'getassessmentcount']);
    Route::post('upload-assignment-file', [EvaluationController::class, 'uploadAssignemntFile']);
    Route::post('delete-assignment-file', [EvaluationController::class, 'deleteAssignemntFile']);
    Route::post('download-assignment-file', [EvaluationController::class, 'downloadZipFile']);
    Route::post('remove-assignment-file-directry', [EvaluationController::class, 'removeDirectry']);

    Route::get('teacher/approval/{co_id?}/{CID?}', [EvaluationController::class, 'sendNoti']);

    Route::get('check-course-combine-faculty/{course_id?}', [EvaluationController::class, 'checkCombineFaculty']);
    // Route::get('combine-course/{co_id?}/{current_co_id?}', [EvaluationController::class,'combineCourse']);




    Route::prefix('teacher')->group(function () {
        Route::post('/update_assessmentmarks/{id?}/{enrollment?}/{marks?}/{updated_at?}/{co_id?}', [CourseAssessmentMarksController::class, 'update_marks']);
        Route::post('/update-totalmarks-desc', [CourseAssessmentMarksController::class, 'updateDescMarks']);
    });


    Route::prefix('welcome')->group(function () {
        Route::post('save-welcome-note-confirmation', [HomeController::class, 'confirmWelcomeNoteRead']);
    });

    Route::prefix('notification')->group(function () {
        Route::post('/save-notification', [NotiAddRemoveController::class, 'saveNotification']);
        // getNotificationCreateByUser
        Route::get('/get-create-by-user', [NotiAddRemoveController::class, 'getNotificationCreateByUser']);
        // publish_notification
        Route::get('/publish_notification/{id}', [NotiAddRemoveController::class, 'publishNotification']);
        Route::post('/publish_notification/{id}', [NotiAddRemoveController::class, 'publishNotification']);
        // get-users-will-receive-noti
        //  Route::get('/get-users-will-receive-noti', [NotiAddRemoveController::class, 'getUserForNotification']);
    });
    Route::get('/get-applicant-information/{sess}/{school}/{dept}/{prog}', [EnrollmentStartStopController::class, 'getApplicantInformation']);
    Route::get('/get-group-pages-management', [EnrollmentStartStopController::class, 'getGroupPagesManagmentRec']);
    Route::post('/save-enrollment-start-stop', [EnrollmentStartStopController::class, 'insertRecord']);
    Route::post('/delete-enrollment/{id}', [EnrollmentStartStopController::class, 'deleteEnrollment']);
    Route::get('/get-enrollment-status', [EnrollmentStartStopController::class, 'checkEnrollmentStatus']);
    Route::get('/get-enrollment-status', [EnrollmentStartStopController::class, 'checkEnrollmentStatus']);


    // Student Finanacial Api work by bilal saleem
    Route::post('/save-student-record', [StudentRecordController::class, 'insertRecord']);
    Route::post('/delete-student-record/{id}', [StudentRecordController::class, 'deletRecord']);
    Route::post('/save-software-record', [StudentRecordController::class, 'insertSoftware']);
    Route::post('/delete-software-record/{id}', [StudentRecordController::class, 'deletSoftware']);
    Route::post('/approved-challan-req/{scd_id}', [StundentsFinancialController::class, 'approvedChallan']);
    Route::post('/update-record-procedure/{sess_id}', [StundentsFinancialController::class, 'updateRecordProcedure']);
});



// VC-Dashbaord Routes
use App\Http\Controllers\Grader\vcReports\VcReportsController;

Route::get('total-students-school-wise', [VcReportsController::class, 'getStduents']);
// Route::post('assign-to-message', [VcReportsController::class, 'assignToMessages']);

// Route::post('assign-to-message', function(){
//     return 'ok';
// });
// Route::post('assign-to-message', [VcReportsController::class, 'assignToMessages']);
Route::post('assign-to-message', [VcReportsController::class, 'assignToMessages']);

// Route::post('/staff/assign-to-message', [VcReportsController::class,'assignToMessages'])->name('staff/assign-to-message');



Route::post('add-emp-employee', [EmpEmployeesController::class, 'addEmpEmployee']);
Route::post('get-emp-employee', [EmpEmployeesController::class, 'getEmpData']);
Route::post('update-status-employee', [EmpEmployeesController::class, 'updateEmpStatus']);
Route::post('createTA', [EmpEmployeesController::class, 'createTa']);
Route::get('search-employee', [EmpEmployeesController::class, 'searchEmployee'])->name('search-employee');



//Feestructure
Route::POST('/masterFeeStructure/get_masterFee_list', [FeeStructureController::class, 'getMasterFeeList']);
Route::POST('/masterFeeStructure/check_fee_challan', [FeeStructureController::class, 'checkFeeStructureChallan']);
Route::POST('/masterFeeStructure/delete_feeData', [FeeStructureController::class, 'delete_feeData'])->name('delete_feeData');
//callan report
Route::POST('/get-challan-report', [ChallanReportController::class, 'getChallanReport']);
Route::POST('/get-challan-summary', [ChallanReportController::class, 'getChallanSummary']);
Route::POST('/get-challan-summary-report', [ChallanSummaryReportController::class, 'getChallanSummaryReport']);



//hostelFeestructure
Route::POST('/hostelFeeStructure/get_hostelFee_list', [HostelFeeStructureController::class, 'getMasterFeeList']);
Route::POST('/hostelFeeStructure/delete_hostelfeeData', [HostelFeeStructureController::class, 'delete_feeData'])->name('delete_hostelfeeData');
Route::POST('/hostelFeeStructure/check_hostel_fee_challan', [HostelFeeStructureController::class, 'checkHostelFeeStructureChallan']);





Route::get('getProgramFeeStructureForm/get', [FeeStructureController::class, 'getProgramFeeStructureForm']);
Route::get('getDepartmentsFeeStructureForm/get', [FeeStructureController::class, 'getDepartmentsFeeStructureForm']);

//General Events
Route::POST('/get_event_list', [EventsMasterController::class, 'getEventList']);
Route::POST('/delete_event', [EventsMasterController::class, 'deleteEvent']);
Route::GET('/events/{id}/qr.png', [EventsMasterController::class, 'eventQrImage'])->where('id', '[0-9]+');
Route::POST('/get_reg_list', [EventsRegistrationsController::class, 'getRegistrationList']);
//Donation Record
Route::POST('/get_donation_list', [DonationMasterController::class, 'donationRecordList']);
Route::POST('/delete_donation_rec', [DonationMasterController::class, 'deleteDonation']);


//RFID MACHINES
Route::POST('/get_machine_list', [RfidSetupController::class, 'getMachineList']);

//master scholarship
Route::POST('/master-scholarship-form/get_scholarship_list', [MasterScholarshipController::class, 'getScholarshipList']);
Route::POST('/master-scholarship-form/delete_scholarship', [MasterScholarshipController::class, 'delete_scholarship']);
//master scholarship type
Route::post('master-scholarshiptype-form/getscholarshiptype', [MasterScholarshipTypeController::class, 'getMasterScholarshipType']);
Route::post('master-scholarshiptype-form/deletescholarshiptype', [MasterScholarshipTypeController::class, 'deleteMasterScholarshipType']);
//master fee adjsutment
Route::POST('/master-feeadjustment-form/getMasterFeeAdjustmentList', [FeeAdjustmentController::class, 'getMasterFeeAdjustmentList']);
Route::POST('/master-feeadjustment-form/delete_masterFeeAdjustment', [FeeAdjustmentController::class, 'delete_masterFeeAdjustment']);
// course exempted
Route::post('/course-exempted/list', [CourseExemptedController::class, 'list']);
Route::post('/course-exempted/delete', [CourseExemptedController::class, 'destroy']);
Route::post('/course-exempted/get-courses', [CourseExemptedController::class, 'getCoursesBySession']);
//fee adjustment
Route::post('/feeadjustment/get-challan-rec', [FeeAdjustmentController::class, 'getChallanRec'])->name('getChallanRec');
Route::post('/feeadjustment/get-enrollment-rec', [FeeAdjustmentController::class, 'getEnrollmentRec'])->name('getEnrollmentRec');
Route::POST('/feeadjustment/getFeeAdjustmentList', [FeeAdjustmentController::class, 'getFeeAdjustmentList']);
Route::POST('/feeadjustment/getAudFeeAdjustmentList', [FeeAdjustmentController::class, 'getAudFeeAdjustmentList']);
Route::POST('/feeadjustment/changeStatusFeeAdjustment', [FeeAdjustmentController::class, 'changeStatusFeeAdjustment'])->name('changeStatusFeeAdjustment');
Route::POST('/feeadjustment/delete_feeAdjustment', [FeeAdjustmentController::class, 'delete_adjustment'])->name('delete_feeAdjustment');
//Middleware Challan Rectification
Route::post('/challan-rectification/get-challan-ver', [MiddlewareChallanRectificationController::class, 'getChallanVer'])->name('getChallanVer');
Route::post('/challan-rectification/get-version-data', [MiddlewareChallanRectificationController::class, 'getVersionData'])->name('getVersionData');
Route::post('/challan-rectification/edit-scholarhsip', [MiddlewareChallanRectificationController::class, 'ediScholarship'])->name('ediScholarship');
//generate Bulk challan
Route::POST('/generate-fee-challan/get_student_list', [GenerateBulkChallanController::class, 'get_student_list']);
Route::POST('/generate-fee-challan/get_challan_list', [GenerateBulkChallanController::class, 'get_challan_list']);
Route::POST('/generate-fee-challan/get_feeAdjusted_list', [GenerateBulkChallanController::class, 'get_feeAdjusted_list']);
Route::GET('/generate-fee-challan/print_challan', [GenerateBulkChallanController::class, 'print_challan']);
//merit scholarship approve
Route::POST('/approve-merit-scholarship-students', [GenerateBulkChallanController::class, 'approveMeritStudents'])->name('approve-merit-scholarship-students');
//generate Credit Hr Bulk challan
Route::POST('/generate-creditfee-challan/get_student_list', [GenerateCreditHrBulkChallanController::class, 'get_student_list']);
Route::POST('/generate-creditfee-challan/get_challan_list', [GenerateCreditHrBulkChallanController::class, 'get_challan_list']);
Route::POST('/generate-creditfee-challan/check_fee_structure', [GenerateCreditHrBulkChallanController::class, 'checkFeeStructure']);
Route::POST('/generate-creditfee-challan/get_feeAdjusted_list', [GenerateCreditHrBulkChallanController::class, 'get_feeAdjusted_list']);
Route::POST('/creditHr-data-entry', [GenerateCreditHrBulkChallanController::class, 'creditHr_data_entry']);
Route::GET('/get-exempt-courses', [GenerateCreditHrBulkChallanController::class, 'getExemptCourses']);
Route::POST('/generate-creditfee-challan/get-enrollment-student-courses', [GenerateCreditHrBulkChallanController::class, 'getEnrollmentStudentCourses']);
Route::POST('/student-credits-data/lookup', [StudentCreditsDataController::class, 'lookup']);
Route::POST('/student-credits-data/update', [StudentCreditsDataController::class, 'update']);
Route::POST('/student-credits-data/generate-challan', [StudentCreditsDataController::class, 'generateChallan']);
Route::POST('/generate-creditfee-challan/delete-enrollment-course-exemption', [GenerateCreditHrBulkChallanController::class, 'deleteEnrollmentCourseExemption']);
Route::GET('/generate-creditfee-challan/print_challan', [GenerateCreditHrBulkChallanController::class, 'print_challan']);
//Enrollment Summary Report
Route::POST('/enrollment-summary/get_student_list', [EnrollmentSummaryReportController::class, 'get_student_list']);
//Convocation Fee Challan
Route::POST('/get-securityDeposit-student-list', [GenerateConvocationBulkChallanController::class, 'get_student_list']);
Route::POST('/generate-convocationfee-challan/get_challan_list', [GenerateConvocationBulkChallanController::class, 'get_challan_list']);
Route::GET('/generate-convocationfee-challan/print_challan', [GenerateConvocationBulkChallanController::class, 'print_challan']);
//Challan Detail Summary
Route::POST('/get-challans-data', [StudentChallanDetailsController::class, 'getChallansData']);
Route::POST('/get-fee-global-summary', [StudentChallanDetailsController::class, 'getFeeGlobalSummaryData']);
//Installments
Route::POST('/get-installment-dates', [FeeInstallmentController::class, 'getInstallmentDates']);
Route::POST('/delete-installment-dates', [FeeInstallmentController::class, 'deleteInstallmentDates']);
Route::POST('/get-installment-dates-admission', [FeeInstallmentController::class, 'getInstallmentDates_admission']);
Route::POST('/delete-installment-dates-admission', [FeeInstallmentController::class, 'deleteInstallmentDates_admission']);
Route::POST('/check-installment-admission-challan', [FeeInstallmentController::class, 'checkInstallmentAdmissionChallan']);
//scholarship criteria
Route::POST('/master-scholarship-criteria/get_scholarshipCriteria_list', [ScholarshipCriteriaController::class, 'getScholarshipCriteriaList']);
Route::POST('/master-scholarship-criteria/delete_scholarshipCriteria', [ScholarshipCriteriaController::class, 'delete_scholarshipCriteria']);
//scholarship Policy
Route::POST('/get_scholarshipPolicy_list', [ScholarshipPolicyController::class, 'getScholarshipPolicyList']);
Route::POST('/delete_scholarshipPolicy', [ScholarshipPolicyController::class, 'delete_scholarshipPolicy']);
//admission Individual scholarship criteria
Route::POST('/admission-individual-scholarship-criteria/filter_applicants', [AdmissionIndividualScholarshipCriteriaController::class, 'filter_admission_applicants']);
//admission scholarship criteria
Route::POST('/master-admission-scholarship-criteria/get_scholarshipCriteria_list', [AdmissionScholarshipCriteriaController::class, 'getScholarshipCriteriaList']);
Route::POST('/master-admission-scholarship-criteria/delete_scholarshipCriteria', [AdmissionScholarshipCriteriaController::class, 'delete_scholarshipCriteria']);
//admission scholarship approval
Route::POST('/admission-scholarship-approval/filter_applicants', [AdmissionScholarshipApprovalController::class, 'filter_admission_schlrshp_applicants']);
Route::GET('/admission-scholarship-approval/updateApprovalStatus', [AdmissionScholarshipApprovalController::class, 'updateApprovalStatus']);
//student record management
Route::post('/save-student-record', [StudentRecordController::class, 'insertRecord']);
Route::post('/get-scholarship-record', [StudentRecordController::class, 'getSchlrshipDetailRecord']);
Route::post('/sync-scholarship-enrollment-from-admission', [StudentRecordController::class, 'syncScholarshipEnrollmentFromAdmission']);
Route::get('/get-hostel-feeStructure/{session?}/{hostelfeeStruct_type?}/{student_id?}', [StudentRecordController::class, 'getHostelFeeStructure'])->name('get-hostel-feeStructure');
Route::get('/get-hostelScholarship-student/{session?}/{student_id?}/{scholarship_id?}', [StudentRecordController::class, 'getHostelScholarship_student'])->name('get-hostelScholarship-student');
Route::post('/delete-student-record', [StudentRecordController::class, 'deletRecord']);
Route::get('/get-academic-feeStructure/{session?}/{student_id?}', [StudentRecordController::class, 'getAcademicFeeStructure'])->name('get-academic-feeStructure');
Route::post('/save-and-post-scholarships', [StudentRecordController::class, 'saveAndPostScholarships']);
Route::post('/delete-scholarship-row', [StudentRecordController::class, 'deleteScholarshipRow']);

// student record management — REVISED grid page (StudentRecordRevisedController).
// Everything else the revised page needs is served by the routes above, unchanged.
// NOTE: like the routes above, these sit outside the auth:api group.
Route::get('/srr/fee-matrix/{session?}/{student_id?}', [StudentRecordRevisedController::class, 'getFeeMatrix'])->name('srr-fee-matrix');
Route::get('/srr/previous-arrears/{session?}/{student_id?}', [StudentRecordRevisedController::class, 'getPreviousArrears'])->name('srr-previous-arrears');
Route::post('/srr/save-and-post', [StudentRecordRevisedController::class, 'saveAndPost'])->name('srr-save-and-post');   // deprecated, kept for older clients
// Save stages a frozen snapshot in SSD / SSHD; Post applies that snapshot to student_challan.
Route::post('/srr/save', [StudentRecordRevisedController::class, 'saveSnapshot'])->name('srr-save');
Route::post('/srr/post', [StudentRecordRevisedController::class, 'postSnapshot'])->name('srr-post');
// The tabular summary reads the saved snapshot back, so it can never disagree with
// the challan preview or with what Post writes.
Route::get('/srr/staged/{session?}/{student_id?}', [StudentRecordRevisedController::class, 'getStaged'])->name('srr-staged');


Route::prefix('eportal')->group(function () {
    Route::post('rollback', [EportalController::class, 'rollbackStatus'])->name('eportal_request_rollback');
});

Route::group(['middleware' => ['auth:api']], function () {

    Route::controller(ApiController::class)->group(function () {
        Route::post('modify-student-info', 'modifyStudentInfo')->name('modify-student-info');
        Route::post('add-student', 'addStudent')->name('add-student');
        Route::post('assign-group', 'assignGroup')->name('assign-group');
        Route::post('get-user-groups', 'getUserGroups')->name('get-user-groups');
    });


    Route::controller(StudentProfileDetailsController::class)->group(function () {
        Route::get('/fetch-single-student-widget-link', 'fetchSingleStudentWidgetLink')->name('fetch-single-student-widget-link');
    });

    Route::prefix('page-permissions')->group(function () {
        Route::get('/pages', [PagePermissionsController::class, 'getPages'])->name('page-permissions');
        Route::get('/group-pages', [PagePermissionsController::class, 'getGroupPages'])->name('page-permissions');
        Route::post('/store', [PagePermissionsController::class, 'store'])->name('page-permissions');
    });




    // ==============================
    // NBS Form Management
    // ==============================
    Route::controller(NBSFormController::class)->group(function () {
        Route::get('/get-data-nbs-form', 'getNBSFormData')->name('get-data-nbs-form');
        Route::post('/update-scholarship-editable', 'updateScholarshipEditable')->name('update-scholarship-editable');
        Route::post('/update-scholarship-approval', 'updateScholarshipApproval')->name('update-scholarship-approval');
        Route::post('/approve-scholarship-applicant', 'approveScholarshipApplicant')->name('approve-scholarship-applicant');
        Route::get('/get-data-nbs-form-hod-approval', 'getNBSFormDataHodApproval')->name('get-data-nbs-form-hod-approval');
        Route::post('/disapprove-scholarship-applicant', 'disapproveScholarshipApplicant')->name('disapprove-scholarship-applicant');
        Route::get('/get-scholarship-remarks', 'getScholarshipRemarks')->name('get-scholarship-remarks');
        Route::post('/hod-approve-scholarship', 'hodApproveScholarship')->name('hod-approve-scholarship');
        Route::get('/get-applicant-documents', 'getApplicantDocuments')->name('get-applicant-documents');
        Route::post('/nbs-form-update-profile-data', 'updateProfileData')->name('nbs-form-update-profile-data');
        Route::post('/nbs-form-update-siblings', 'updateSiblingsData')->name('nbs-form-update-siblings');
        Route::post('/nbs-form-update-employed', 'updateEmployedData')->name('nbs-form-update-employed');
        Route::post('/nbs-form-update-unemployed', 'updateUnemployedData')->name('nbs-form-update-unemployed');
        Route::post('/nbs-form-update-transport', 'updateTransport')->name('nbs-form-update-transport');
        Route::get('/nbs-form-get-data-ro', 'getNBSFormDataRO')->name('nbs-form-get-data-ro');
        Route::get('/get-applicant-hod-preview', 'getApplicantHodPreview')->name('get-applicant-hod-preview');
    });


    Route::controller(HodReportController::class)->group(function () {
        Route::get('/adm_schedules', 'adm_schedules')->name('adm_schedules');
    });



    Route::controller(StudentFineController::class)->group(function () {
        // Challan

        Route::post('/insert-table-data', 'insertData')->name('insert-table-data');
        Route::post('/insert-table-data-summer', 'insertDataSummer')->name('insert-table-data-summer');
    });









    // ==============================
    // Student Document Verification & Management
    // ==============================
    Route::controller(StudentDocumentsController::class)->group(function () {
        Route::post('/update-doc-status', 'updateDocStatus')->name('update-doc-status');                              // Update status of a document
        Route::post('/send-document-notification', 'sendDocumentNotification')->name('send-document-notification');   // Send notification to student
        Route::get('/get-applicant-admission-information', 'getApplicantInformation')->name('get-applicant-admission-information');       // Fetch applicant info for verification
        Route::get('/fetch-previous-uploads', 'fetchPreviousUploads')->name('fetch-previous-uploads');                // Get previously uploaded files
        Route::get('/delete-uploaded-student-doc', 'deleteUploadedStudentDoc')->name('delete-uploaded-student-doc');  // Delete a file uploaded by student
        Route::post('/download-student-documents-bundle', 'DownloadDocumentBundle')->name('download-student-documents-bundle'); // Bulk download documents
        Route::get('/doc-verification-get-school', 'getSchool')->name('doc-verification-get-school');
    });

    Route::get('/get-academic-sessions', function () {
        return AcademicSessions::getAcademicSessions();
    });
    Route::get('/get-academic-sessions-schools', function () {
        return AcademicSessions::getAcademicSessionsSchool();
    });
    Route::get('/get-academic-sessions-departments/{school_id?}', function ($school_id) {
        return AcademicSessions::getAcademicSessionsDepartments($school_id);
    });
    Route::get('/get-academic-sessions-programs/{school_id?}/{dept_id?}', function ($school_id, $dept_id) {
        return AcademicSessions::getAcademicSessionsPrograms($school_id, $dept_id);
    });



    //-------------- HOSTEL ROUTES ------------------------------------//
    Route::controller(HostelBedAssignmentController::class)->group(function () {
        Route::get('hostel-bed-assignment/room-data', 'getRoomData')->name('hostel-bed-assignment.room-data');
        Route::post('hostel-bed-assignment/assign', 'assignBed')->name('hostel-bed-assignment.assign');
        Route::get('hostel-bed-assignment/vacate-details', 'getVacateDetails')->name('hostel-bed-assignment.vacate-details');
        Route::post('hostel-bed-assignment/vacate', 'vacateBed')->name('hostel-bed-assignment.vacate');
        Route::get('hostel-bed-assignment/paid-unassigned', 'getPaidUnassigned')->name('hostel-bed-assignment.paid-unassigned');
    });

    Route::controller(HostelController::class)->group(function () {
        Route::post('get-hostel-applicants-data', 'getHostelApplicantsData')->name('getHostelApplicantsData');
        Route::post('search-hostel-applicant', 'searchHostelApplicant')->name('search-hostel-applicant');
        Route::post('grant-hostel-consent', 'grantHostelConsent')->name('grant-hostel-consent');
        Route::post('search-hostel-student', 'searchHostelStudent')->name('search-hostel-student');
        Route::post('add-hostel-student', 'addHostelStudent')->name('add-hostel-student');
        Route::post('get-hostel-students-data', 'getHostelStudentsData')->name('getHostelStudentsData');
        Route::get('get-paid-students-by-gender', 'getPaidStudentsByGender')->name('getPaidStudentsByGender');
        Route::post('allow-old-applicant/{enrollmentNo}', 'allowOldApplicant')->name('allow-old-applicant');
        Route::post('allow-new-applicant/{enrollmentNo}', 'allowNewApplicant')->name('allow-new-applicant');
        Route::post('reject-old-applicant/{enrollmentNo}', 'rejectOldApplicant')->name('reject-old-applicant');
        Route::post('reject-new-applicant/{enrollmentNo}', 'rejectNewApplicant')->name('reject-new-applicant');
        Route::get('get-usernames-for-search', 'getUsernamesForSearch')->name('get-usernames-for-search');
    });

    Route::controller(HostelStatsDashboardController::class)->group(function () {
        // Hostel Dashboard Stats Routes
        Route::post('hostel-dashboard/get-stats', 'getDashboard')->name('hostel-dashboard.get-stats');
        Route::post('hostel-dashboard/get-stats-json', 'getStatsJson')->name('hostel-dashboard.get-stats-json');
        Route::post('hostel-dashboard/drill-down-list', 'getDrillDownList')->name('hostel-dashboard.drill-down-list');
    });

    Route::controller(HostelSummaryController::class)->group(function () {
        // Hostel Summary Routes
        Route::get('hostel-summary/get-summary', 'getSummary')->name('hostel-summary.get-summary');
        Route::get('hostel-summary/get-challan-list', 'getHostelChallanList')->name('hostel-summary.get-challan-list');
        Route::get('hostel-summary/get-campus-matrix', 'getCampusMatrixSummary')->name('hostel-summary.get-campus-matrix');
        Route::get('hostel-summary/get-vacate-preview', 'getVacatePreview')->name('hostel-summary.get-vacate-preview');
        Route::get('hostel-summary/get-bulk-analysis', 'getBulkVacateAnalysis')->name('hostel-summary.get-bulk-analysis');
        Route::post('hostel-summary/rollover-vacate', 'performSessionRollover')->name('hostel-summary.rollover-vacate');

        Route::post('hostel-summary/vacate', 'vacateHostel')->name('hostel-summary.vacate');
        Route::get('hostel-summary/get-logs', 'getLogs')->name('hostel-summary.get-logs');
        Route::get('hostel-summary/export-excel', 'exportExcel')->name('hostel-summary.export-excel');
        Route::get('hostel-summary/export-pdf', 'exportPdf')->name('hostel-summary.export-pdf');
    });
});
