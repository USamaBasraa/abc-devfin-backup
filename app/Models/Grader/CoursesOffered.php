<?php

namespace App\Models\Grader;

use Illuminate\Database\Eloquent\Model;
use DB;
use Illuminate\Http\Request;
use App\sessions;
use Illuminate\Support\Facades\Auth;

class CoursesOffered extends Model
{
    protected $primaryKey = 'co_id';
    protected $table = 'courses_offered';
    public $timestamps = false;
    protected $fillable = [
        'co_id', 'co_sess_id', 'co_school_id',
    ];

    protected $hidden = [
        'co_id',
    ];
    public function schools()
    {
        return $this->hasOne('App\Models\Schools', 'school_id', 'co_school_id');
    }
    public function fetch($id=0)
    {
        if($id){
            //TBD
        }else{
            //TBD
        }
    }
    //attendance Management Page Faculty
    public function fetchFaculty($sess_id=0,$school_id=0,$dept_id=0)
    {

        $query = "SELECT DISTINCT
                        FACULTY_COURSES.FC_EMPLOYEE_ID,
                        FACULTY_COURSES.FC_CO_SESS_ID,
                        EMP_EMPLOYEES.PREFIX,
                        EMP_EMPLOYEES.EMP_F_NAME,
                        EMP_EMPLOYEES.EMP_L_NAME,
                        EMP_EMPLOYEES.EMP_EMAIL,
                        emp_f_name
                        || ' '
                        || emp_l_name AS emp_full_name,
                        emp_email
                FROM
                    FACULTY_COURSES
                    INNER JOIN EMP_EMPLOYEES ON FACULTY_COURSES.FC_EMPLOYEE_ID = EMP_EMPLOYEES.EMPLOYEE_ID
                    LEFT JOIN courses_offered ON faculty_courses.fc_co_id = courses_offered.co_id
                WHERE
                    (
                        FACULTY_COURSES.FC_CO_SESS_ID = ".$sess_id."
                    ) 
                    AND
                        faculty_courses.fc_status =  'E'
                    AND 
                        courses_offered.co_status =  'E'
                    AND
                        fc_co_id IN (
                                    SELECT
                                        co_id
                                    FROM
                                        courses_offered
                                    WHERE
                                            co_sess_id = '$sess_id'
                                        AND
                                            co_dept_id = '$dept_id'
                                        AND
                                            parent_co_id IS NULL
                                         AND co_status = 'E'
                                    )
                      
                ORDER BY EMP_EMPLOYEES.EMP_F_NAME ASC";

        //echo $query;
        //exit();

        $result =DB::select($query);
        return json_decode(json_encode($result), true);

    }

    public function fetch_offered_course_details($co_id=0)
    {
        $query = "select * from courses_offered
                    left join ACADEMIC_SESSIONS
                    on ACADEMIC_SESSIONS.SESS_ID=courses_offered.CO_SESS_ID
                    left join SCHOOLS on courses_offered.CO_SCHOOL_ID = SCHOOLS.SCHOOL_ID
                    left join DEPARTMENTS on courses_offered.CO_DEPT_ID =DEPARTMENTS.DEPT_ID
                    left join courses_two on courses_offered.CO_COURSE_ID= courses_two.COURSE_ID
                    where co_id=$co_id ";
        $result = DB::select($query);
        return $result;
    }

    public function fetchFacultyCourses($sess_id=0,$employee_id)
    {
        $sessions_values = new Sessions();
        $sessions_values->startSession();
        $all_session = new sessions();
        $query = "SELECT
                    fc_co_id,
                    fc_co_course_id,
                    course_code,
                    course_cd_name,
                    fc_co_id
                    || ' '
                    || ' '
                    || course_code
                    || ' '
                    || course_cd_name
                    || ' '
                    || COURSES_OFFERED.CO_SECTION AS course_full_name
                    FROM
                    faculty_courses
                    LEFT JOIN COURSES_OFFERED ON faculty_courses.fc_co_id = COURSES_OFFERED.CO_ID
                    LEFT JOIN courses_two ON fc_co_course_id = course_id
                    WHERE
                    1 = 1";
        if ($sess_id)
        {
            $query .= " AND fc_co_sess_id = ".$sess_id;
        }
        if ($employee_id)
        {
            $query .= " AND fc_employee_id = ".$employee_id;
        }

        $result =DB::select($query);
        return $result;

    }

    public function fetchTeacherCourses($sess_id,$fc_emp_id)
    {
        $loginUser = Auth::user();
         $emp_user = $loginUser::find($loginUser->id)->employees;
        $emp_email =$loginUser::find($loginUser->id)->email;
        $sess = new sessions();
        $sess->startSession();
         $session_id = $sess_id;//auth()->user()->profile_settings->active_session;

        return self::select('faculty_courses.fc_id',
                            'courses_offered.co_id AS course_offer_id',
                            'courses_offered.parent_co_id',
                            'courses_offered.co_school_id',
                            'courses_offered.co_dept_id',
                            'courses_offered.co_shift_id',
                            'courses_offered.co_section',
                            'courses_offered.co_course_cr',
                            'courses_two.course_id',
                            'courses_two.course_code',
                            'courses_two.course_cd_name',
                            DB::raw('GET_ASSESSMENT_COUNT(courses_offered.co_id,3) as mid_count'),
                            DB::raw('GET_ASSESSMENT_COUNT(courses_offered.co_id,4) as final_count')
                        )
            ->join('courses_two', 'courses_offered.co_course_id', '=', 'courses_two.course_id')
            ->join('faculty_courses',DB::raw("(CASE
                                                WHEN
                                                    parent_co_id IS NULL
                                                OR
                                                    parent_co_id = 0
                                                THEN co_id
                                                ELSE parent_co_id
                                            END)"),     '=', 'fc_co_id')
            ->join('emp_employees', 'fc_employee_id',   '=', 'employee_id')
            ->join('staff_rights', 'sr_dept_id',        '=', 'co_dept_id')
            ->where([
                ['parent_co_id'                 ,NULL],
                ['co_sess_id'                   , $session_id],
                ['courses_offered.co_status'    , 'E'],
                ['staff_rights.sr_sis_id'       , $emp_email],
                ['faculty_courses.fc_status', 'E'],
                ['emp_employees.employee_id'    , $fc_emp_id],
            ])
            ->whereNotNull('faculty_courses.fc_employee_id')
            ->distinct()
            ->get();
           //->toSql();

    }
    //ReportGenerator Faculty
    public function fetchFacultyDepCourses($sess_id=0,$school_id=0,$employee_id=0,$dept_id=0)
    {
        $sessions_values = new Sessions();
        $sessions_values->startSession();
        $all_session = new sessions();
        $sqlCoursesOffered = "SELECT DISTINCT fc_employee_id,
							emp_f_name,
							emp_f_name||' '||emp_l_name AS emp_full_name,
							emp_email,
							co_id,
							co_sess_id,
							co_school_id,
							co_dept_id,
							co_shift_id,
							CO_COURSE_ID,
							COURSE_CODE,
							COURSE_CD_NAME,
							CO_SECTION,
							CO_COURSE_CR,
							parent_co_id,
                            cc.cc_file_name AS course_contents_url
							  FROM courses_offered
							  LEFT JOIN courses_two on course_id = co_course_id
							  LEFT JOIN faculty_courses ON (CASE 
                                 						WHEN parent_co_id IS NULL or parent_co_id = 0 THEN co_id 
                                 						ELSE parent_co_id
                             							END)=fc_co_id
								LEFT JOIN emp_employees ON fc_employee_id = employee_id
                                LEFT JOIN course_contents cc ON cc.cc_co_id = co_id
							WHERE fc_employee_id IS NOT NULL
                            AND co_status = 'E' ";
        if ($sess_id)
        {
            $sqlCoursesOffered .= " AND co_sess_id = ".$sess_id;
        }
        if ($school_id)
        {
            $sqlCoursesOffered .= " AND  co_school_id = ".$school_id;
        }
        if ($dept_id)
        {
            $sqlCoursesOffered .= " AND  co_dept_id = ".$dept_id;
        }
        if ($employee_id)
        {
            $sqlCoursesOffered .= " AND fc_employee_id = ".$employee_id;
        }
        //echo $sqlCoursesOffered;
        //exit();
        $result =DB::select($sqlCoursesOffered);
        return $result;
    }

    public function fetchOfferedCoursesFaculty($sess_id=0,$school_id=0,$dept_id=0)
    {
        $sqlSelectFaculty = "SELECT DISTINCT fc_employee_id,
                                                    employee_id,
                                                    emp_f_name,
                                                    emp_f_name||' '||emp_l_name AS emp_full_name,
                                                    emp_email
                                FROM courses_offered
                                LEFT JOIN faculty_courses ON (CASE 
                                                                WHEN parent_co_id IS NULL or parent_co_id = 0 THEN co_id
                                                                ELSE parent_co_id
                                                                END)=fc_co_id
                                LEFT JOIN emp_employees ON fc_employee_id = employee_id
                                WHERE fc_employee_id IS NOT NULL ";
        if(isset($sess_id) and $sess_id != "") {
            $sqlSelectFaculty .= " AND co_sess_id=" . $sess_id . " ";
        }
        if (isset($school_id) and $school_id != "") {
            $sqlSelectFaculty .= " AND co_school_id=" . $school_id . " ";
        }
        if (isset($dept_id) and $dept_id != "") {
            $sqlSelectFaculty .= " AND co_dept_id=" . $dept_id . " ";
        }
        $sqlSelectFaculty .= " ORDER BY emp_full_name";
        //echo $sqlSelectFaculty;
        // exit;
        $result =DB::select($sqlSelectFaculty);
        return $result;
    }


}
