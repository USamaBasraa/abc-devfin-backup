<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class CommonController extends Controller
{
    public function check_route(Request $request){
        $status = 'ok';
        return response()->json(['data' => $status ]);
    }
    public function check_token(Request $request){
        $query=(
            " select
            email,remember_token
            from sisusers
            where email='ijaz.hussain@bnu.edu.pk' ");
            $status=DB::select($query);

        return response()->json($status );
    }

    public function get_offered_courses(Request $request){
       if($request->input('session_id')!=""){
        $query="
        SELECT
            co_id,
            get_course_name(co_id) as course_name,
            get_teachername(co_id) as teacher_name,
            get_grading_status(co_id) as grading_status
        FROM
          courses_offered
        where parent_co_id is null 
        and co_sess_id=".$request->input('session_id');
        if($request->input('school_id')!=""){
            $query.=" and co_school_id=".$request->input('school_id');
        }
        if($request->input('department_id')!=""){
            $query.=" and co_dept_id=".$request->input('department_id');
        }
        if($request->input('co_id')!=""){
            $query.=" and co_id=".$request->input('co_id');
        }
        if($request->input('faculty_employee_id')!=""){
            $query.=" and co_id in 
                    (
                        select fc_co_id from faculty_courses 
                        where fc_employee_id=".$request->input('faculty_employee_id')."
                    )";
        }
        //echo $query;
        $courses = DB::select($query);
        return response()->json(['data' => $courses ]);
    }else{
        return response()->json(['data' => 'Please Select Session' ]);
    }
       
    }
    public function sessions(Request $request){
        $query="
        SELECT
        *
       FROM
       academic_sessions
    
         order by sess_id desc";
        $sessions = DB::select($query);
        return response()->json(['data' => $sessions ]);
    }

    public function schools(Request $request){
        $query="
            SELECT
              *
             FROM
                 schools
        ";
        $schools = DB::select($query);
        return response()->json(['data' => $schools ]);
    }

    public function departments(Request $request,$school_id){
        $query="
           
        SELECT  * FROM  departments where dept_school_id='$school_id'


        ";
        $departments = DB::select($query);
        return response()->json(['data' => $departments ]);
    }
    

    public function courses_offered_by_session(Request $request,$session_id){
    
        $query="
        SELECT
            co_id,
            get_course_name(co_id) as course_name,
            get_teachername(co_id) as teacher_name
        FROM
          courses_offered
        where co_sess_id='$session_id'
        and parent_co_id is null
        ";
        $courses = DB::select($query);
        return response()->json(['data' => $courses ]);
    }
    public function get_teachers(Request $request){
           
       $query="
                SELECT 
                employee_id, 
                prefix||''||trim(emp_f_name)||' '||emp_l_name as faculty_name
                FROM emp_employees
                WHERE 1=1 ";
                $query.=" AND
                            employee_id IN (
                            SELECT DISTINCT
                            fc_employee_id
                            FROM
                            faculty_courses
                            WHERE
                            EMPLOYEE_ID not in (1605)
                            and fc_co_sess_id ='".$request->input('session_id')."'  ";
                        
                        if($request->input('school_id')!=""){
                            $query.=" and fc_co_school_id=".$request->input('school_id');
                        }
                        if($request->input('department_id')!=""){
                            $query.=" and fc_co_dept_id=".$request->input('department_id');
                        }
                        if($request->input('co_id')!=""){
                            $query.=" and fc_co_id=".$request->input('co_id');
                        }
                                       
                $query.=" ) ";                                            
    $query.="order by emp_school_id,emp_dept_id,emp_type ";


           

        $data=DB::Select($query);
        return response()->json(['data' => $data ]);
    }
    public function courses_offered_by_session_and_school(Request $request,$session_id,$school_id){

         $query="
        SELECT
            co_id,
            get_course_name(co_id) as course_name,
            get_teachername(co_id) as teacher_name
        FROM
          courses_offered
        where co_sess_id='$session_id' and co_school_id='$school_id'
        and parent_co_id is null
        ";
        $courses = DB::select($query);
        return response()->json(['data' => $courses ]);
    }

public function courses_offered_by_session_and_school_and_department(Request $request,$session_id,$school_id,$dept_id){
    $query="
    
    
            SELECT
            co_id,
            get_course_name(co_id) as course_name,
            get_teachername(co_id) as teacher_name
                FROM
                courses_offered
                where co_sess_id='$session_id' and co_school_id='$school_id' and co_dept_id='$dept_id'
                and parent_co_id is null


    ";
    // return $query;
    $courses = DB::select($query);
    return response()->json(['data' => $courses ]);
}

    public function get_time_slots(Request $request,$co_id){
       
        $query=" select 
                tt_id,
                tt_class_date,
                tt_subject_time_from,
                tt_subject_time_to,
                (select count(*) from course_attendance where mtt_id = tt_id) as attendance_marked
                from master_timetable 
                where tt_status='E'
                and sysdate >= tt_class_date
                and mtt_co_id = $co_id
                order by tt_class_date";
       // return $query;
        $time_slots = DB::select($query);

        return response()->json(['data' => $time_slots ]);
    }
public function get_students_from_time_slots(Request $request,$tt_id,$co_id){
       
        $query="
                        SELECT
                    course_attendance.id                   AS ca_id,
                    case when ca_hod_attendance='EX' then 'Exempted' else ca_attendance end as ca_attendance,
                    sc_co_id,
                    student_courses.sc_sai_enrollment_no   AS sai_enrollment_no,
                    spi_name,
                    sc_pc_course_cr,
                    course_cd_name,
                    ca_teacher_remarks,
                    ca_hod_attendance,
                    
                    (
                        SELECT
                            COUNT(*)
                        FROM
                            course_attendance left
                            JOIN master_timetable ON course_attendance.mtt_id = master_timetable.tt_id
                        WHERE
                            course_attendance.sai_enrollment_no = sc_sai_enrollment_no
                            AND course_attendance.ca_attendance = 'P'
                            AND course_attendance.co_id = student_courses.sc_co_id
                            AND mtt_co_id = '$co_id'
                    ) AS presents,
                    (
                        SELECT
                            COUNT(*)
                        FROM
                            course_attendance left
                            JOIN master_timetable
                                                    on course_attendance.mtt_id = master_timetable.tt_id
                        WHERE
                            course_attendance.sai_enrollment_no = sc_sai_enrollment_no
                            AND course_attendance.ca_attendance = 'A'
                            AND ca_hod_attendance is null
                            AND course_attendance.co_id = student_courses.sc_co_id
                            AND mtt_co_id = '$co_id'
                    ) AS absents,
                    (
                        SELECT
                            COUNT(*)
                        FROM
                            course_attendance left
                            JOIN master_timetable ON course_attendance.mtt_id = master_timetable.tt_id
                        WHERE
                            course_attendance.sai_enrollment_no = sc_sai_enrollment_no
                            AND ca_hod_attendance = 'EX'
                            AND course_attendance.co_id = student_courses.sc_co_id
                            AND mtt_co_id = '$co_id'
                    ) AS absents_ex,
                    (
                        SELECT
                            COUNT(*)
                        FROM
                            course_attendance
                        WHERE
                            course_attendance.sai_enrollment_no = sc_sai_enrollment_no
                            AND course_attendance.ca_attendance = 'A'
                            AND course_attendance.co_id = student_courses.sc_co_id
                            AND ca_hod_attendance IS NULL
                    ) AS alert_absents,
                    (
                        SELECT
                            co_late_yesno
                        FROM
                            courses_offered
                        WHERE
                            co_id = '$co_id'
                    ) AS co_late_yesno,
                    (
                        SELECT
                            co_req_classes
                        FROM
                            courses_offered
                        WHERE
                            co_id = '$co_id'
                    ) AS co_req_classes,
                    (
                        SELECT
                            co_total_classes
                        FROM
                            courses_offered
                        WHERE
                            co_id = '$co_id'
                    ) AS co_total_classes,
                    (
                        SELECT
                            COUNT(*)
                        FROM
                            course_attendance
                        WHERE
                            course_attendance.mtt_id = '$tt_id'
                    ) AS attendance_marked,
                    (
                        SELECT
                            COUNT(DISTINCT mtt_id)
                        FROM
                            course_attendance
                        WHERE
                            mtt_id IN (
                                SELECT
                                    tt_id
                                FROM
                                    master_timetable
                                WHERE
                                    mtt_co_id = '$co_id'
                            )
                    ) AS delivered_classes,
                    sai_student_status,
                    sc_status
                FROM
                    student_courses left
                    JOIN courses_offered ON sc_co_id = co_id
                    LEFT JOIN academic_sessions ON sc_co_sess_id = sess_id
                    LEFT JOIN courses_two ON sc_course_id = course_id
                    LEFT JOIN course_defination ON course_cd_id = cd_id
                    LEFT JOIN students_apply_info ON sc_sai_enrollment_no = sai_enrollment_no
                    LEFT JOIN students_personal_info ON sai_spi_id = spi_id
                    LEFT JOIN course_attendance ON student_courses.sc_sai_enrollment_no = course_attendance.sai_enrollment_no
                                                AND student_courses.sc_co_id = course_attendance.co_id
                                                AND mtt_id = '$tt_id'
                WHERE
                    sc_enrollment_status = 1
                    AND sc_status NOT IN (
                        'Withdrawal'
                    )
                    AND ( sc_co_id = '$co_id' OR parent_co_id = '$co_id' )
                        
                ORDER BY
                    spi_name
                        ";
                // echo  $query; exit;
        $students = DB::select($query);

        DB::update("update course_attendance 
        set ca_status='E'
        where mtt_id='".$tt_id."'");

        DB::update("update faculty_course_attendance 
        set fca_status='E'
        where mtt_id='".$tt_id."'");

        return response()->json(['data' => $students,'attendance_status' => $students[0]->attendance_marked]);
    }
public function get_student_attendance_details(Request $request){
        //return $request->all();
        $query="SELECT
        course_attendance.id                  AS ca_id,
        to_char(ca_attendance_date) as ca_attendance_date,
        CASE
            WHEN ca_hod_attendance = 'EX' THEN
                'Exempted'
            ELSE
                ca_attendance
        END AS ca_attendance,
        courses_offered.co_id                 AS sc_co_id,
        course_attendance.sai_enrollment_no   AS sai_enrollment_no,
        spi_name,
        courses_offered.co_course_cr          AS sc_pc_course_cr,
        get_course_name(courses_offered.co_id) AS course_cd_name,
        ca_teacher_remarks,
        ca_hod_attendance,
        (
            SELECT
                COUNT(*)
            FROM
                course_attendance left
                JOIN master_timetable ON course_attendance.mtt_id = master_timetable.tt_id
            WHERE
                course_attendance.sai_enrollment_no = sc_sai_enrollment_no
                AND course_attendance.ca_attendance = 'P'
                AND course_attendance.co_id = student_courses.sc_co_id
        ) AS presents,
        (
            SELECT
                COUNT(*)
            FROM
                course_attendance left
                JOIN master_timetable ON course_attendance.mtt_id = master_timetable.tt_id
            WHERE
                course_attendance.sai_enrollment_no = sc_sai_enrollment_no
                AND course_attendance.ca_attendance = 'A'
                AND ca_hod_attendance IS NULL
                AND course_attendance.co_id = student_courses.sc_co_id
                AND mtt_co_id = courses_offered.co_id
        ) AS absents,
        (
            SELECT
                COUNT(*)
            FROM
                course_attendance left
                JOIN master_timetable ON course_attendance.mtt_id = master_timetable.tt_id
            WHERE
                course_attendance.sai_enrollment_no = sc_sai_enrollment_no
                AND ca_hod_attendance = 'EX'
                AND course_attendance.co_id = student_courses.sc_co_id
                AND mtt_co_id = courses_offered.co_id
        ) AS absents_ex,
        (
            SELECT
                COUNT(*)
            FROM
                course_attendance
            WHERE
                course_attendance.sai_enrollment_no = sc_sai_enrollment_no
                AND course_attendance.ca_attendance = 'A'
                AND course_attendance.co_id = student_courses.sc_co_id
                AND ca_hod_attendance IS NULL
        ) AS alert_absents,
        sai_student_status,
        sc_status
    FROM
        course_attendance left
        JOIN courses_offered ON courses_offered.co_id = course_attendance.co_id
        LEFT JOIN students_apply_info ON students_apply_info.sai_enrollment_no = course_attendance.sai_enrollment_no
        LEFT JOIN students_personal_info ON students_personal_info.spi_id = students_apply_info.sai_spi_id
        LEFT JOIN student_courses ON student_courses.sc_sai_enrollment_no = students_apply_info.sai_enrollment_no
                                     AND student_courses.sc_co_sess_id = courses_offered.co_sess_id
                                     and student_courses.sc_co_id = course_attendance.co_id
    WHERE
    lower(course_attendance.sai_enrollment_no) = '".strtolower($request->input('en_no'))."'
        AND courses_offered.co_sess_id = '".$request->input('session_id')."'
		and sc_status='A'
        ";
//       -- and sc_co_id = 24618";   
//return $query; 
        $students = DB::select($query);
        
        return response()->json(['data' => $students,'attendance_status' =>'1']);
    }
    public function update_course_attendance(Request $request,$ca_id){
        
        
        $attendance_status=$request->attendance_status;
        $teacher_remarks=$request->exemption_remarks;
        $exemption_remarks  = $teacher_remarks;
        $entry_user_id      = $ca_id; 
       // echo $ca_id."__".$attendance_status;
        if($attendance_status=='EX'){
            DB::table('course_attendance')
            ->where('id', $ca_id)
            ->update([
            'ca_hod_attendance'     => $attendance_status,
            'ca_hod_remarks'        => $exemption_remarks,
            'ca_hod_id'             => $entry_user_id,
            'ca_hod_entry_datetime' => now()
            ]);   
        }else{ 
            if($attendance_status=='P'){ 
                DB::table('course_attendance')
                ->where('id', $ca_id)
                ->update([ 
                'ca_attendance'         => 'P',
                'ca_entry_user_id'      => $entry_user_id,
                'ca_entry_datetime'     => now(),
                'ca_hod_attendance'     => '',
                'ca_hod_remarks'        => '',
                'ca_hod_id'             => ''
                ]);  
            }else{
                DB::table('course_attendance')
                ->where('id', $ca_id)
                ->update([
                'ca_attendance'     => 'A',
                'ca_entry_user_id'  => $entry_user_id,
                'ca_hod_attendance'     => '',
                'ca_hod_remarks'        => '',
                'ca_hod_id'             => '',
                'ca_entry_datetime' => now()
                ]);
            }
        } 
      
        return response()->json(['success'=>'Attendance Updated Successfully']);
    }
}
