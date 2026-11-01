<?php

namespace App\Http\Controllers\Grader;

use Illuminate\Http\Request;
use App\Models\UserNotifications;
use DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;


class VisitingFacultyController extends Controller
{
	public function index()
	{
		$schools = $this->getSchools("'12'");
		//dd($schools);
		//$departments = $this->getDepartments("'12'");
		//$programs = $this->getPrograms("'12'");
		return view('grader.visiting_faculty.index', compact("schools"));
		//return view('grader.visiting_faculty.index',compact("schools","departments","programs"));
	}

	public function store(Request $request)
	{
		$data = [
			'faculty_name' => $request->name,
			'cnic' => $request->cnic,
			'terminal_qualification' => $request->qualification,
			'vf_school_id' => $request->SCHOOL_ID,
			'vf_dept_id' => $request->DEPT_ID,
			'vf_prog_id' => $request->PROG_ID,
			'vf_course_id' => $request->course_id,
			'credit_hours' => $request->credit_hr,
			'required_teaching_hours' => $request->required_teaching_hours,
			'completed_teaching_hours' => $request->completed_teaching_hours,
            'vf_email' => $request->email,
            'vf_contact_no' => $request->contact_no,
            'vf_institute_of_terminal_degree' => $request->institute,
            'vf_course_code' => $request->course_code,
		];

		// Insert query
		$res = DB::table('visiting_faculty')->insert($data);
		if ($res) {
			return response()->json([
				"msg" => "Data Save Successfully.",

			]);
		}
	}

	protected function getSchools($ids)
	{

		$sql = "SELECT * FROM SCHOOLS WHERE SCHOOL_ID IN ($ids)";
		//echo $sql;exit;
		$res = DB::select($sql);
		return $res;
	}

	protected function getDepartments(Request $request)
	{
		$sql = "SELECT * FROM DEPARTMENTS WHERE DEPT_SCHOOL_ID = '$request->school_id'";
		//echo $sql;exit;
		$res = DB::select($sql);
		return $res;
	}
	protected function getPrograms(Request $request)
	{
		$sql = "SELECT * FROM PROGRAMS WHERE PROG_DEPT_ID ='$request->dept_id'";
		//echo $sql;exit;
		$res = DB::select($sql);
		return $res;
	}

	protected function getCourses(Request $request)
	{
		$sql = "select distinct courses.course_id, courses.course_name from program_courses left join courses ON pc_course_id = courses.course_id where program_courses.pc_prog_id ='$request->prog_id'";
		//echo $sql;exit;
		$res = DB::select($sql);
		return $res;
	}


	protected function getFacultyList(Request $request)
	{
		$sql = "SELECT * from visiting_faculty
        left join schools on vf_school_id = schools.school_id
        left join departments on vf_dept_id = departments.dept_id
        left join programs on vf_prog_id = programs.prog_id
        left join courses on vf_course_id = courses.course_id ORDER BY visiting_faculty.FACULTY_ID DESC";
		//echo $sql;exit;
		$res = DB::select($sql);
		return $res;
	}
}
