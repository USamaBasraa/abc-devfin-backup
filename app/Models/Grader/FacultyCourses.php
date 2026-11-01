<?php

namespace App\Models\Grader;

use Illuminate\Database\Eloquent\Model;
use DB;
use App\sessions;


class FacultyCourses extends Model
{
    protected $fillable = [
        'fc_id', 'fc_employee_id', 'fc_co_id',
    ];

    protected $hidden = [
        'fc_id',
    ];

    //For Courses Drop down
    public function fetch($sess_id = 0, $dept_id = 0, $email = '')
    {
        $query = "SELECT DISTINCT
                        fc_employee_id,
                        emp_f_name,
                        emp_employees.prefix
                         || ' '
                         || emp_f_name
                         || ' '
                         || emp_l_name AS emp_full_name,
                        emp_email,
                        co_id,
                        co_sess_id,
                        co_school_id,
                        co_dept_id,
                        co_shift_id,
                        co_course_id,
                        course_code,
                        course_cd_name,
                        co_section,
                        co_course_cr
                    FROM
                        courses_offered
                        LEFT JOIN courses_two ON course_id = co_course_id
                        -- LEFT JOIN faculty_courses ON (
                        --     CASE
                        --         WHEN
                        --             parent_co_id IS NULL
                        --         OR
                        --             parent_co_id = 0
                        --         THEN co_id
                        --         ELSE parent_co_id
                        --     END
                        -- ) = fc_co_id
                        LEFT JOIN faculty_courses ON co_id = fc_co_id
                        LEFT JOIN emp_employees ON fc_employee_id = employee_id
                    WHERE
                            fc_employee_id IS NOT NULL
                        AND
                            co_status = 'E'";

        if ($sess_id) {
            $query .= " AND co_sess_id = " . $sess_id;
        }
        if ($dept_id) {
            $query .= " AND co_dept_id = " . $dept_id;
        }
        if ($email) {
            $query .= " AND emp_email = '" . $email . "'";
        }

        //echo $query;
        //exit();
        $result = DB::select($query);
        return json_decode(json_encode($result), true);
    }

    public function fetchDashboardCourses($co_id = '')
    {
        $sess = new sessions();
        $sess->startSession();

// echo $sess->active_session_id;exit;
//dd(auth()->user()->profile_settings->active_session);
//['faculty_courses.fc_co_sess_id', $sess->active_session_id],

        $whereArr = [
            ['faculty_courses.fc_employee_id', $sess->emp_user_id],
            ['faculty_courses.fc_co_sess_id', auth()->user()->profile_settings->active_session],
            ['faculty_courses.fc_status', 'E'],
            ['courses_offered.co_status', 'E']];

            if($co_id != ''){
                $whereArr[] = ['faculty_courses.fc_co_id',$co_id];
            }
        

        return self::select('faculty_courses.fc_id',
            'faculty_courses.fc_co_id',
            'faculty_courses.fc_co_school_id',
            'faculty_courses.fc_co_sess_id',
            \DB::raw('check_course_criterias(fc_co_id) as course_criteria'),
            \DB::raw('check_course_summary(fc_co_id) as course_first_process'),
            \DB::raw('check_final_grade(fc_co_id) as check_final'),
            \DB::raw('GET_COURSE_GRADING_TYPE(fc_co_id) as course_grading_type'),
            // \DB::raw('co_grading_type as course_grading_type'),
            'faculty_courses.fc_co_dept_id',
            'faculty_courses.fc_co_shift_id',
            'faculty_courses.fc_co_course_id',
            'courses_two.course_cd_name',
            'courses_two.course_code',
            'courses_offered.co_course_cr',
            'courses_offered.co_section')
            ->join('courses_two', 'faculty_courses.fc_co_course_id', '=', 'courses_two.course_id')
            ->join('courses_offered', 'faculty_courses.fc_co_id', '=', 'courses_offered.co_id')
            ->where($whereArr)
            ->orderby('faculty_courses.fc_co_id', 'asc')
             ->get();//->toSql();

    }
   // (select count(*)from student_courses where sc_co_id="COURSES_OFFERED".CO_ID and student_courses.SC_CO_SESS_ID="COURSES_OFFERED".CO_SESS_ID) as total_student
    public function pak(){
        echo 'tt0000';
    }
 public function fetchDepartmentFaculty($sessId)
    {


        $sess = new sessions();
        $sess->startSession();

         $session_id = ($sessId==0)?auth()->user()->profile_settings->active_session:$sessId;

        //dd( $session_id);
        //    print_r($sess); exit;
        $courses = '';
        if(auth()->user()->username == 'imran.sharif'){
            $courses = "co_dept_id in (101,81,161,201,202,203)";
        }else if(auth()->user()->username == 'hafsa.tanveer'){
            $courses = "co_dept_id in (101,202)";
        }else if(auth()->user()->username == 'shabana'){
            $courses = "co_dept_id in (14,16,264,15,18)";
        }else if(auth()->user()->username == 'bushra.rahman'){
            $courses = "co_dept_id in (11,12)";
        }else if(auth()->user()->username == 'rabia.noor'){
            $courses = "co_dept_id in (11,262,201,81)";
        }else{
            $courses = "co_dept_id = $sess->emp_dept_id";
        }

        //dd($this->getParentDept($sess->emp_dept_id));
        return  self::select('faculty_courses.fc_employee_id',
                            'faculty_courses.fc_co_sess_id',
                            'emp_employees.prefix',
                            'emp_employees.emp_f_name',
                            'emp_employees.emp_l_name',
                            'emp_employees.emp_email')
                    ->join('emp_employees', 'faculty_courses.fc_employee_id', '=', 'emp_employees.employee_id')
                    ->join('courses_offered', 'faculty_courses.fc_co_id', '=', 'courses_offered.co_id')
                    ->where([
                        ['faculty_courses.fc_co_sess_id', $session_id],
                        ['faculty_courses.fc_status', 'E'],
                        ['courses_offered.co_status', 'E'],
                    ])
                    ->whereRaw('fc_co_id IN (
                                            SELECT
                                                co_id
                                            FROM
                                                courses_offered
                                            WHERE
                                                    co_sess_id = '.$session_id.'
                                                AND                                                    
                                                    '.$courses.'
                                                AND
                                                    parent_co_id IS NULL
                                            )'
                                )
                    ->orderby('emp_employees.emp_f_name', 'asc')
                    ->distinct()
                    ->get();
                //->toSql();
       // exit;
    }



}
