<?php
namespace App\Http\Controllers\Reports;
use App\Http\Controllers\Controller;

use App\Http\Requests;
use App\Models\Grader\AcademicSessions;


class FacultyAttendanceController extends Controller{

    // private $sortBy = 'id';
    // private $sortDirection = 'ASC';
    // private $sessions;

    // public function __construct(AcademicSessions $sessions)
    // {
    //     $this->session = $sessions;
    // }

    // public function index()
    // {
    //     $sessions = $this->session->fetch();
    //     return view('common.sessions', compact('sessions'));
    // }

    // public function retrieve()
    // {
    //     return $sessions = $this->session->fetch();
    // }

    // public function retrieveTopSessions($count=6)
    // {
    //     return $sessions = $this->session->fetchTopSession($count);
    // }


    function faculty_course_attendance(){
        return view('grader.reports.faculty_course_attendance');
    }



}