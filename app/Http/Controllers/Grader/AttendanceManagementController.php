<?php


namespace App\Http\Controllers\Grader;
use App\Http\Controllers\Controller;

use App\Http\Requests;


class AttendanceManagementController extends Controller
{
    public function index()
    {
    //    / echo "123";exit;
    
    if(auth()->user()->employee_id == 512 || auth()->user()->employee_id == 875 || auth()->user()->employee_id == 3455 || auth()->user()->employee_id == 42){
        $sessions = \App::call('App\Http\Controllers\Grader\AcademicSessionController@retrieve');
        $schools = \App::call('App\Http\Controllers\Grader\SchoolController@retrieve');
        $departments = \App::call('App\Http\Controllers\Grader\DepartmentController@retrieve');
        $facultyCourses = \App::call('App\Http\Controllers\Grader\FacultyCoursesController@retrieve');
        $offeredFaculty = \App::call('App\Http\Controllers\Grader\CoursesOfferedController@retrieveFaculty');
        return view('grader.attendance.management',compact('sessions','schools','departments','facultyCourses','offeredFaculty'));
    }else{
        return 'Unauthorized User';
    }

    }

}
