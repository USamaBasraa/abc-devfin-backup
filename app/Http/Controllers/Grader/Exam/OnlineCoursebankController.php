<?php

namespace App\Http\Controllers\Grader\Exam;

use App\Http\Controllers\Controller;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class OnlineCoursebankController extends Controller{

  function index($emp_id){ 



        
    return view('grader.exam.online_course_bank',['emp_id'=>$emp_id]);    
  }
  function get_courses($employee_id){

    $sql = "select fc_co_id,get_course_name(fc_co_id) as coursename,get_teachername(fc_co_id) as teachername from faculty_courses where fc_employee_id = 42 and fc_co_sess_id = 152";
    return $res_sql = DB::select($sql);
  
  }
}