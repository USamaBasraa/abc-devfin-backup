<?php

namespace App\Http\Controllers\Grader;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Grader\CoursesOffered;
use App\Models\Grader\FacultyCourses;
class CoursesOfferedController extends Controller
{
    private $sortBy = 'id';
    private $sortDirection = 'ASC';

    private $coursesOffered;

    public function __construct(CoursesOffered $CoursesOffered)
    {
        $this->coursesOffered = $CoursesOffered;
    }

    public function index()
    {
        $offering = $this->coursesOffered->fetch();
        return view('grader.common.schools', compact('offering'));
    }

    public function retrieve()
    {
        return $this->coursesOffered->fetch();
    }
    public function retrieveFaculty($sess_id=0,$school_id=0,$dept_id=0)
    {
        return $this->coursesOffered->fetchFaculty($sess_id,$school_id,$dept_id);
    }
     public function retrieveCourses($sess_id=0,$employee_id=0)
    {
        return $this->coursesOffered->fetchFacultyCourses($sess_id,$employee_id);
    }
	 public function retrieveFacultyDepCourses($sess_id=0,$school_id=0,$employee_id=0,$dept_id=0)
    {
        return $this->coursesOffered->fetchFacultyDepCourses($sess_id,$school_id,$employee_id,$dept_id);
    }
    public function retrieveOfferedCoursesFaculty($sess_id=0,$school_id=0,$dept_id=0)
    {
        return $this->coursesOffered->fetchOfferedCoursesFaculty($sess_id,$school_id,$dept_id);
    }
	 public function retrievefacultylist($sessId){
        $facultyCourse = new FacultyCourses();
        return $facultyList = $facultyCourse->fetchDepartmentFaculty($sessId);
       
    }
    public function retrieveTeacherCourses($sess_id,$fc_emp_id)
    {
        return $coursesList = $this->coursesOffered->fetchTeacherCourses($sess_id,$fc_emp_id);
    }

}
