<?php

namespace App\Models\Grader;

use Illuminate\Database\Eloquent\Model;
use DB;
use App\sessions;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\Models\Grader\CoursesOffered;
use App\Models\StudentsApplyInfos;
use Exception;

class CourseAttendance extends Model
{
    protected $table = 'course_attendance';
    protected $guarded = [];

    public function CreateCourseAttendance_RO($co_id, $slot_id, $slot_date, $user_id)
    {

        // $sess = new sessions();
        //  $sess->startSession();

        $sql = "  SELECT
         sc_sai_enrollment_no,
         sc_co_id,
         TO_DATE('" . $slot_date . "','yyyy-mm-dd HH24:MI:SS'),
         $slot_id,
         case when sc_status='Block' then 'A' else 'P' end as attendance_type,
         'PENDING',
         current_timestamp,
         current_timestamp
         FROM
         student_courses
         LEFT JOIN courses_offered ON sc_co_id = co_id
         WHERE
             1 = 1
         AND sc_enrollment_status = 1
         AND sc_status NOT IN ('Withdrawal')
         AND (sc_co_id = $co_id OR parent_co_id = $co_id)";
        //  echo $sql;exit;
        $rec = DB::select($sql);

        // echo count($rec);exit;

        if (count($rec) > 0) {


            $check_sql = "select
         (select count(*) from COURSE_ATTENDANCE where mtt_id = $slot_id) as total_students,
         (select count(*) from FACULTY_COURSE_ATTENDANCE where mtt_id = $slot_id) as total_faculty
         from dual";


            // echo $check_sql;exit();
            $data = DB::select($check_sql);

            if ($data[0]->total_students == 0 && $data[0]->total_faculty == 0) {
                $faculty_insert = "insert into
         faculty_course_Attendance
         (
             employee_id,
             co_id,
             mtt_id,
             fca_attendance_date,
             fca_status
         )
         values (
             " . $user_id . ",
             " . $co_id . ",
             " . $slot_id . ",
             TO_DATE('" . $slot_date . "','yyyy-mm-dd HH24:MI:SS'),
             'E'
         )";

                $student_insert = "
         INSERT INTO course_attendance (
             sai_enrollment_no,
             co_id,
             ca_attendance_date,
             mtt_id,
             ca_attendance,
             ca_status,
             created_at,
           updated_at
         )
         SELECT
         sc_sai_enrollment_no,
         sc_co_id,
         TO_DATE('" . $slot_date . "','yyyy-mm-dd HH24:MI:SS'),
         $slot_id,
         case when sc_status='Block' or sai_student_status in ('Left','Struck off','Stuck-Off') then 'A' else 'P' end as attendance_type,        
         'E',
         current_timestamp,
         current_timestamp
     FROM
         student_courses
         LEFT JOIN courses_offered ON sc_co_id = co_id
         left join students_apply_info on sc_sai_enrollment_no = sai_enrollment_no
     WHERE
             1 = 1
         AND sc_enrollment_status = 1
         AND sc_status NOT IN ('Withdrawal')
         AND (sc_co_id = $co_id OR parent_co_id = $co_id)
       ";

                try {
                    DB::insert($student_insert);
                    DB::insert($faculty_insert);
                    DB::commit();
                } catch (\Throwable $e) {
                    DB::rollback();
                    throw $e;
                }
            }
        }




    }




    public function CreateCourseAttendance($co_id, $slot_id, $slot_date, $rfid)
    {

        $sess = new sessions();
        $sess->startSession();

        /*
        $sql = "  SELECT
        sc_sai_enrollment_no,
        sc_co_id,
        TO_DATE('".$slot_date."','yyyy-mm-dd HH24:MI:SS'),
        $slot_id,
        case when sc_status='Block' then 'A' else 'P' end as attendance_type,
        'PENDING',
        current_timestamp,
        current_timestamp
        FROM
        student_courses
        LEFT JOIN courses_offered ON sc_co_id = co_id
        WHERE
            1 = 1
        AND sc_enrollment_status = 1
        AND sc_status NOT IN ('Withdrawal')
        AND (sc_co_id = $co_id OR parent_co_id = $co_id)";
         //  echo $sql;exit;
        $rec = DB::select($sql);

        // echo count($rec);exit;

        if(count($rec)>0){


        $check_sql = "select
        (select count(*) from COURSE_ATTENDANCE where mtt_id = $slot_id) as total_students,
        (select count(*) from FACULTY_COURSE_ATTENDANCE where mtt_id = $slot_id) as total_faculty
        from dual";


       // echo $check_sql;exit();
        $data = DB::select($check_sql);

       if($data[0]->total_students==0 && $data[0]->total_faculty==0){
        $faculty_insert = "insert into
        faculty_course_Attendance
        (
            employee_id,
            co_id,
            mtt_id,
            fca_attendance_date,
            fca_status
        )
        values (
            ".$sess->emp_user_id.",
            ".$co_id.",
            ".$slot_id.",
            TO_DATE('".$slot_date."','yyyy-mm-dd HH24:MI:SS'),
            'PENDING'
        )";

        $student_insert = "
        INSERT INTO course_attendance (
            sai_enrollment_no,
            co_id,
            ca_attendance_date,
            mtt_id,
            ca_attendance,
            ca_status,
            created_at,
            updated_at
        )
        SELECT
        sc_sai_enrollment_no,
        sc_co_id,
        TO_DATE('".$slot_date."','yyyy-mm-dd HH24:MI:SS'),
        $slot_id,
        case when sc_status='Block' or sai_student_status in ('Left','Struck off','Stuck-Off') then 'A' else 'P' end as attendance_type,       
        'PENDING',
        current_timestamp,
        current_timestamp
    FROM
        student_courses
        LEFT JOIN courses_offered ON sc_co_id = co_id
        left join students_apply_info on sc_sai_enrollment_no = sai_enrollment_no
    WHERE
            1 = 1
        AND sc_enrollment_status = 1
        AND sc_status NOT IN ('Withdrawal')
        AND (sc_co_id = $co_id OR parent_co_id = $co_id)
      ";

            try {
                DB::insert($student_insert);
                DB::insert($faculty_insert);
                DB::commit();
            } catch (\Throwable $e) {
                DB::rollback();
                throw $e;
            }
        }
    }
    */

        // 1. Check & Create Faculty Attendance if missing
        $check_faculty_sql = "select count(*) as total_faculty from FACULTY_COURSE_ATTENDANCE where mtt_id = $slot_id";
        $faculty_data = DB::select($check_faculty_sql);

        if ($faculty_data[0]->total_faculty == 0) {
            $faculty_insert = "insert into
            faculty_course_Attendance
            (
                employee_id,
                co_id,
                mtt_id,
                fca_attendance_date,
                fca_status
            )
            values (
                " . $sess->emp_user_id . ",
                " . $co_id . ",
                " . $slot_id . ",
                TO_DATE('" . $slot_date . "','yyyy-mm-dd HH24:MI:SS'),
                'PENDING'
            )";
            try {
                DB::insert($faculty_insert);
            } catch (\Throwable $e) {
                // Ignore if already exists (concurrency safe-ish)
            }
        }

        // 2. Insert Students who DO NOT have attendance for this slot
        $student_insert = "
        INSERT INTO course_attendance (
            sai_enrollment_no,
            co_id,
            ca_attendance_date,
            mtt_id,
            ca_attendance,
            ca_status,
            created_at,
            updated_at
        )
        SELECT
        sc_sai_enrollment_no,
        sc_co_id,
        TO_DATE('" . $slot_date . "','yyyy-mm-dd HH24:MI:SS'),
        $slot_id,
        case when sc_status='Block' or sai_student_status in ('Left','Struck off','Stuck-Off') then 'A' else '" . ($rfid ? 'A' : 'P') . "' end as attendance_type,       
        'PENDING',
        current_timestamp,
        current_timestamp
    FROM
        student_courses
        LEFT JOIN courses_offered ON sc_co_id = co_id
        left join students_apply_info on sc_sai_enrollment_no = sai_enrollment_no
    WHERE
            1 = 1
        AND sc_enrollment_status = 1
        AND sc_status NOT IN ('Withdrawal')
        AND (
            student_courses.sc_co_id = $co_id
            OR courses_offered.parent_co_id = $co_id
            OR courses_offered.parent_co_id IN (
                SELECT co_id
                FROM courses_offered
                WHERE parent_co_id = $co_id
            )
        )
        AND NOT EXISTS (
            SELECT 1 FROM course_attendance ca 
            WHERE ca.sai_enrollment_no = student_courses.sc_sai_enrollment_no 
            AND ca.mtt_id = $slot_id
        )
      ";
    //   AND (sc_co_id = $co_id OR parent_co_id = $co_id)
        try {
            DB::insert($student_insert);
            // DB::commit(); // Removing explicit commit as we didn't start a transaction block explicitly here and DB::insert usually auto-commits in Laravel unless manually managing transactions
        } catch (\Throwable $e) {
            // DB::rollback();
            // throw $e;
        }
    }

    public function CreateCourseAttendance33($co_id, $slot_id, $slot_date)
    {
        $add_slot = "
        SELECT
           sc_sai_enrollment_no,sc_co_id
        FROM
            student_courses
            LEFT JOIN courses_offered ON sc_co_id = co_id
        WHERE
                1 = 1
            AND
                sc_enrollment_status = 1
            AND
                sc_status NOT IN (
                    'Withdrawal'
                )
            AND (
                    sc_co_id = $co_id
                OR
                    parent_co_id = $co_id
            ) ";

        $rec = DB::select($add_slot);
        //echo count($rec);exit();
        // echo 'pak';exit();

        foreach ($rec as $key) {
            $data = array(
                'sai_enrollment_no' => $key->sc_sai_enrollment_no
                ,
                'co_id' => $key->sc_co_id
                ,
                'ca_attendance_date' => $slot_date
                ,
                'mtt_id' => $slot_id
                ,
                'ca_attendance' => 'P'
                ,
                'ca_status' => 'PENDING'
                ,
                'created_at' => date('Y-m-d H:i:s')
                ,
                'updated_at' => date('Y-m-d H:i:s')
            );



            // print_r($data);
            // exit();

            try {
                DB::table('course_attendance')->insert($data);
            } catch (\Exception $e) {
                // do some thing
            }
        }




    }

    //HOD ATTENDANCE EXEMPTION PAGE
    public function fetchHodAttendance($co_id)
    {
        $query = "SELECT
                        sc_co_id,
                        student_courses.sc_sai_enrollment_no AS sai_enrollment_no,
                        sc_sai_id,
                        spi_name,
                        sc_pc_course_cr,
                        (SELECT COUNT(*)
                          FROM course_attendance
                          WHERE course_attendance.sai_enrollment_no = sc_sai_enrollment_no
                          AND course_attendance.ca_attendance       = 'P'
                          AND course_attendance.co_id               = student_courses.sc_co_id
                          AND mtt_id                               IN
                            (SELECT tt_id
                            FROM master_timetable
                            WHERE tt_status='E'
                            AND mtt_co_id IN
                              (SELECT co_id FROM courses_offered WHERE co_id=sc_co_id
                              UNION
                              SELECT parent_co_id FROM courses_offered WHERE co_id=sc_co_id
                              )
                            )
                          ) AS presents,
                          (SELECT COUNT(*)
                          FROM course_attendance
                          WHERE course_attendance.sai_enrollment_no = sc_sai_enrollment_no
                          AND (course_attendance.ca_attendance      = 'A')
                          AND course_attendance.co_id               = student_courses.sc_co_id
                          AND mtt_id                               IN
                            (SELECT tt_id
                            FROM master_timetable
                            WHERE tt_status='E'
                            AND mtt_co_id IN
                              (SELECT co_id FROM courses_offered WHERE co_id=sc_co_id
                              UNION
                              SELECT parent_co_id FROM courses_offered WHERE co_id=sc_co_id
                              )
                            )
                          ) AS absents,
                          (SELECT COUNT(ca_attendance)
                          FROM course_attendance
                          WHERE co_id            =sc_co_id
                          AND sai_enrollment_no  =sc_sai_enrollment_no
                          AND ca_attendance     IN ('A')
                          AND ca_hod_attendance IS NULL
                          AND mtt_id            IN
                            (SELECT tt_id
                            FROM master_timetable
                            WHERE tt_status='E'
                            AND mtt_co_id IN
                              (SELECT co_id FROM courses_offered WHERE co_id=sc_co_id
                              UNION
                              SELECT parent_co_id FROM courses_offered WHERE co_id=sc_co_id
                              )
                            )
                          )AS total_abs,
                        co_total_classes,
                        co_req_classes
                    FROM
                        student_courses
                        LEFT JOIN courses_offered ON sc_co_id = co_id
                        LEFT JOIN academic_sessions ON sc_co_sess_id = sess_id
                        LEFT JOIN courses_two ON sc_course_id = course_id
                        LEFT JOIN course_defination ON course_cd_id = cd_id
                        LEFT JOIN students_apply_info ON sc_sai_enrollment_no = sai_enrollment_no
                        LEFT JOIN students_personal_info ON sai_spi_id = spi_id
                    WHERE
                        (
                                sai_student_status = 'Active'
                            OR
                                sai_student_status = 'Expected-Graduate'
                            OR
                                sai_semester_status = 'Defer'
                        ) 	AND sc_enrollment_status=1
--                          AND sc_co_id=" . $co_id . "
                          AND
                            sc_co_id in (select co_id from courses_offered where co_id = " . $co_id . " or parent_co_id = " . $co_id . ")
                            ORDER BY spi_name";
        //echo $query;
        // exit();
        $result = DB::select($query);
        return json_decode(json_encode($result), true);
    }
    //HOD ATTENDANCE EXEMPTION DETAILS OF STUDENT POPUP
    public function fetchAttendanceDetails($co_id, $sai_id)
    {
        $query = "Select id,
                        get_course_name(co_id) as course_name,
						co_id,
						sai_enrollment_no,
						CASE ca_attendance
                            WHEN 'P' THEN 'Present'
                            WHEN 'A' THEN 'Absent'
                            WHEN 'EX' THEN 'Exempted'
                            ELSE ''
                        END ca_attendance,
                        CASE ca_hod_attendance
                            WHEN 'P' THEN 'Present'
                            WHEN 'A' THEN 'Absent'
                            WHEN 'EX' THEN 'Exempted'
                            ELSE ''
                        END ca_hod_attendance,
						to_char(ca_attendance_date, 'DD/MM/YYYY') ca_attendance_date,
						ca_teacher_remarks,
						ca_hod_id,
						ca_hod_remarks,
						(select co_req_classes from COURSES_OFFERED WHERE courses_offered.co_id = course_attendance.co_id) as co_req_classes,
						(select co_total_classes from COURSES_OFFERED WHERE courses_offered.co_id = course_attendance.co_id) as co_total_classes
				from course_attendance
				where 1=1";

        if (isset($co_id) and $co_id != '') {
            //            $query .=" AND co_id = ".$co_id;
            //$query .=" AND co_id IN (select co_id from courses_offered where co_id = ".$co_id." or parent_co_id = ".$co_id.") ";
        }

        if (isset($sai_id) and $sai_id != '') {
            $query .= " AND mtt_id IN
					  (SELECT tt_id
					  FROM master_timetable
					  WHERE tt_status='E'
					  AND mtt_co_id IN
						(SELECT co_id FROM courses_offered WHERE co_id=course_attendance.co_id  and co_sess_id = 155
						UNION
						SELECT parent_co_id FROM courses_offered WHERE co_id=course_attendance.co_id  and co_sess_id = 155 
						)
					  )";
        }

        $query .= " AND ( lower(sai_enrollment_no)='" . strtolower($sai_id) . "')";
        $query .= " ORDER BY     ca_attendance_date,co_id desc";
        //echo $query;exit();
        $result = DB::select($query);
        return json_decode(json_encode($result), true);
    }
    public function saveExemption(array $data)
    {
        $sql = "SELECT
                    COUNT(*) as counts
                FROM
                    grade_history
                WHERE
                    gh_sai_enrollment_no = '" . $data['sel_sai_id'] . "'
                    and gh_co_id = '" . $data['sc_co_id'] . "'";
        // echo $sql;exit;
        $rec = DB::select($sql);
        $rec[0]->counts;
        if ($rec[0]->counts == 0) {
            $exemption = null;
            if (isset($data['id'])) {
                $exemption = self::find($data['id']);

                if ($data['ca_hod_attendance'] == 'EX') {
                    $exemption->ca_hod_attendance = $data['ca_hod_attendance'];
                    $exemption->ca_hod_remarks = $data['ca_hod_remarks'];
                    $exemption->ca_hod_id = "123";
                    $exemption->ca_entry_user_id = "123";
                    $exemption->save();

                    return response()->json([
                        'success' => 'Data saved Successfully',
                        'error' => false,
                        'error_details' => false
                    ]);
                } else if ($data['ca_hod_attendance'] == 'A') {
                    $exemption->ca_attendance = "A";
                    $exemption->ca_hod_attendance = NULL;
                    $exemption->ca_hod_remarks = NULL;
                    $exemption->ca_hod_id = "123";
                    $exemption->ca_entry_user_id = "123";
                    $exemption->save();

                    return response()->json([
                        'success' => 'Data saved Successfully',
                        'error' => false,
                        'error_details' => false
                    ]);
                } else if ($data['ca_hod_attendance'] == 'P') {
                    $exemption->ca_attendance = "P";
                    $exemption->ca_hod_attendance = NULL;
                    $exemption->ca_hod_remarks = NULL;
                    $exemption->ca_hod_id = "123";
                    $exemption->ca_entry_user_id = "123";
                    $exemption->save();

                    return response()->json([
                        'success' => 'Data saved Successfully',
                        'error' => false,
                        'error_details' => false
                    ]);

                } else {
                    return response()->json([
                        'success' => false,
                        'error' => true,
                        'error_details' => 'Data Missing'
                    ]);
                }

            } else {
                return response()->json([
                    'success' => false,
                    'error' => true,
                    'error_details' => 'ID is NULL'
                ]);
            }
        } else {

            return response()->json([
                'success' => false,
                'error' => true,
                'error_details' => 'Data Not Saved, Course already graded, Please re instate student for exemption'
            ]);
        }

    }
    public function show_students($get_tt_id, $co_id, $class_status)
    {
        // echo '123';exit;
        $sql_student_data = "SELECT
                            spi_name,
                            students_apply_info.sai_enrollment_no,
                            (
                                SELECT
                                    ca_attendance
                                FROM
                                    course_attendance
                                WHERE
                                        ROWNUM = 1
                                    AND
                                        course_attendance.sai_enrollment_no = students_apply_info.sai_enrollment_no
                                    AND
                                        mtt_id = $get_tt_id
                            ) AS attendance_status,
                            (
                                SELECT
                                    COUNT(*)
                                FROM
                                    course_attendance
                                WHERE
                                        course_attendance.ca_attendance = 'P'
                                    AND
                                        course_attendance.sai_enrollment_no = student_courses.sc_sai_enrollment_no
                                    AND
                                        course_attendance.co_id = student_courses.sc_co_id
                            ) AS presents,
                            (
                                SELECT
                                    COUNT(*)
                                FROM
                                    course_attendance
                                WHERE
                                        course_attendance.ca_attendance = 'A'
                                    AND
                                        course_attendance.sai_enrollment_no = student_courses.sc_sai_enrollment_no
                                    AND
                                        ca_hod_id IS NULL
                                    AND
                                        course_attendance.co_id = student_courses.sc_co_id
                            ) AS absents,
                            (
                                SELECT
                                    COUNT(*)
                                FROM
                                    course_attendance
                                WHERE
                                        course_attendance.ca_attendance = 'A'
                                    AND
                                        course_attendance.sai_enrollment_no = student_courses.sc_sai_enrollment_no
                                    AND
                                        ca_hod_attendance IS NOT NULL
                                    AND
                                        ca_hod_attendance='EX'
                                    AND
                                        course_attendance.co_id = student_courses.sc_co_id
                            ) AS exempted,
                            sai_student_status,
                            sc_co_id,
                            (SELECT  co_total_classes FROM courses_offered WHERE co_id = $co_id and co_status='E') AS co_total_classes,
                            (SELECT  co_req_classes FROM courses_offered WHERE co_id = $co_id and co_status='E') AS co_req_classes,
                            '$class_status' as  class_status,
                             course_cd_name,
                             course_code,
                             sc_status,
                            ssr_semester_status
                        FROM
                            student_courses
                            LEFT JOIN courses_offered ON sc_co_id = co_id
                            LEFT JOIN courses_two on course_id = courses_offered.CO_COURSE_ID
                            LEFT JOIN students_apply_info ON student_courses.sc_sai_enrollment_no = students_apply_info.sai_enrollment_no
                            LEFT JOIN students_personal_info ON students_apply_info.sai_spi_id = students_personal_info.spi_id
                             left join STUDENTS_SEMESTER_RECORD on STUDENTS_SEMESTER_RECORD.sai_enrollment_no = student_courses.sc_sai_enrollment_no
                            and STUDENTS_SEMESTER_RECORD.sess_id=courses_offered.CO_SESS_ID
                        WHERE
                                1 = 1
                            AND
                                sc_enrollment_status=1
                            AND
                                 sc_status not in ('Withdrawal')
                            AND (
                                    sc_co_id = $co_id
                                OR
                                    parent_co_id = $co_id
                                )
                            AND  students_apply_info.sai_enrollment_no is not null
                        ORDER BY spi_name";

        // echo $sql_student_data;exit;
        $users = DB::select($sql_student_data);
        return $users;
    }
    public function store($sai_enrollment_no, $ca_attendance, $slot_date_id, $sc_co_id, $course_co_id)
    {
        $sessions_values = new Sessions();
        $sessions_values->startSession();

        $loginUser = Auth::user();
        $emp_user = $loginUser::find($loginUser->id)->employees;

        $courseOffered = new CoursesOffered(); // object of courses offered
        $students_apply_info = new StudentsApplyInfos();

        // $rec =  $students_apply_info::find('F2013-080')->sai_id;
        // print_r($rec);

        // exit();

        //echo $loginUser->id. ' '.$emp_user->employee_id;
        //  exit();



        $slot_data_sql = "select mtt_co_id,tt_class_date from master_timetable where tt_id = " . $slot_date_id;
        $obj_data_sql = DB::select($slot_data_sql);

        $co_id = $obj_data_sql[0]->mtt_co_id;
        $attendance_date = $obj_data_sql[0]->tt_class_date;

        // echo $co_id."__".$attendance_date;exit();

        // $get_co_id = $obj_data_sql->mtt_co_id;
        // $total_records = count($get_all_data);
        $total_records = count($sai_enrollment_no);
        //print_r($sai_enrollment_no);
        //       echo $total_records;
        //exit();
        for ($array_id = 0; $array_id < $total_records; $array_id++) {

            $course_attendance = new self;
            $course_attendance->fill(
                [
                    'sai_enrollment_no' => $sai_enrollment_no[$array_id],
                    'sai_id' => $students_apply_info::find($sai_enrollment_no[$array_id])->sai_id,
                    'sai_sdpo_sess_id' => $students_apply_info::find($sai_enrollment_no[$array_id])->sai_sdpo_sess_id,
                    'sai_sdpo_school_id' => $students_apply_info::find($sai_enrollment_no[$array_id])->sai_sdpo_school_id,
                    'sai_sdpo_dept_id' => $students_apply_info::find($sai_enrollment_no[$array_id])->sai_sdpo_dept_id,
                    'sai_sdpo_prog_id' => $students_apply_info::find($sai_enrollment_no[$array_id])->sai_sdpo_prog_id,
                    'sai_sdpo_shift_id' => $students_apply_info::find($sai_enrollment_no[$array_id])->sai_sdpo_shift_id,
                    'co_id' => $sc_co_id[$array_id],
                    'co_sess_id' => $courseOffered::find($course_co_id)->co_sess_id,
                    'co_school_id' => $courseOffered::find($course_co_id)->co_school_id,
                    'co_dept_id' => $courseOffered::find($course_co_id)->co_dept_id,
                    'co_shift_id' => $courseOffered::find($course_co_id)->co_shift_id,
                    'ca_attendance' => $ca_attendance[$array_id],
                    'ca_teacher_id' => $sessions_values->emp_user_id,
                    'mtt_id' => $slot_date_id,
                    'CA_ATTENDANCE_DATE' => $attendance_date,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')

                ]
            );

            $course_attendance->save();
        }



        $values = array(
            'uid_' => $loginUser->id,
            'employee_id' => $sessions_values->emp_user_id,
            'emp_email' => $loginUser->email,
            'co_id' => $course_co_id,
            'co_sess_id' => $courseOffered::find($course_co_id)->co_sess_id,
            'co_school_id' => $courseOffered::find($course_co_id)->co_school_id,
            'co_dept_id' => $courseOffered::find($course_co_id)->co_dept_id,
            'co_shift_id' => $courseOffered::find($course_co_id)->co_shift_id,
            'mtt_id' => $slot_date_id,
            'fca_attendance_date' => $attendance_date,
            'fca_status' => 'E',
            'fca_entry_user_id' => $loginUser->id
        );
        DB::table('faculty_course_attendance')->insert($values);


        /* $faculty_course_attendance = new self;
         $faculty_course_attendance->fill(
             [
                 'sai_enrollment_no'     =>$sai_enrollment_no[$array_id],
                 'ca_attendance'         =>$ca_attendance[$array_id],
                 'mtt_id'                =>$slot_date_id,
                 'CA_ATTENDANCE_DATE'    =>$attendance_date,
                 'co_id'                 =>$sc_co_id[$array_id],
             ]
         );*/

        // $faculty_course_attendance->save();

        return "1";

    }
    public function getStudentCourseAttendance($parent_co_id, $sc_co_id, $enrollment_no)
    {
        $student_attendance = "SELECT co_id,
                                    co_total_classes,
                                    course_cd_name,
                                    (SELECT COUNT(ca_attendance) total_present FROM
                                      course_attendance
                                      WHERE co_id = " . $sc_co_id . "
                                      AND course_attendance.sai_enrollment_no = students_apply_info.sai_enrollment_no
                                      AND ca_attendance IN ('P')
                                      AND mtt_id IN (SELECT tt_id FROM master_timetable
                                                      WHERE tt_status = 'E'
                                                      AND mtt_co_id IN ( SELECT co_id FROM courses_offered
                                                                          WHERE co_id = " . $sc_co_id . "
                                                                          UNION
                                                                          SELECT parent_co_id FROM courses_offered
                                                                          WHERE co_id = " . $sc_co_id . "
                                                                        )
                                                      )
                                    ) AS total_student_present,
                                    ( SELECT COUNT(DISTINCT(mtt_id) ) total_held
                                      FROM faculty_course_attendance
                                      WHERE co_id IN ( SELECT co_id FROM courses_offered
                                                        WHERE co_id = " . $sc_co_id . "
                                                        UNION ALL
                                                        SELECT parent_co_id FROM courses_offered
                                                        WHERE co_id = " . $sc_co_id . "  AND co_request_status = 'Approved'
                                                        UNION
                                                        SELECT co_id FROM courses_offered
                                                        WHERE parent_co_id = " . $sc_co_id . "
                                                        AND co_request_status = 'Approved'
                                                      )
                                    ) AS total_classes_held,
                                    ( SELECT COUNT(ca_attendance) total_absent
                                      FROM course_attendance WHERE co_id = " . $sc_co_id . "
                                      AND course_attendance.sai_enrollment_no = students_apply_info.sai_enrollment_no
                                      AND ca_attendance IN ('A')
                                      AND ca_hod_attendance IS NULL
                                      AND mtt_id IN ( SELECT tt_id FROM master_timetable
                                                      WHERE tt_status = 'E'
                                                      AND mtt_co_id IN ( SELECT co_id FROM courses_offered
                                                                          WHERE co_id = " . $sc_co_id . "
                                                                          UNION
                                                                          SELECT parent_co_id FROM courses_offered
                                                                          WHERE co_id = " . $sc_co_id . "
                                                                        )
                                                      )
                                    ) AS total_absent,
                                    ( SELECT COUNT(ca_attendance) total_exmp
                                      FROM course_attendance
                                      WHERE co_id = " . $sc_co_id . "
                                      AND course_attendance.sai_enrollment_no = students_apply_info.sai_enrollment_no
                                      AND ca_hod_id IS NOT NULL
                                      AND ca_hod_attendance != 'A'
                                      AND mtt_id IN ( SELECT tt_id FROM master_timetable
                                                      WHERE tt_status = 'E'
                                                      AND mtt_co_id IN ( SELECT co_id FROM courses_offered
                                                                          WHERE co_id = " . $sc_co_id . "
                                                                          UNION
                                                                          SELECT parent_co_id FROM courses_offered
                                                                          WHERE co_id = " . $sc_co_id . "
                                                                        )
                                                      )
                                    ) AS total_exmp
                              FROM courses_offered
                              LEFT JOIN STUDENTS_APPLY_INFO on sai_enrollment_no='" . $enrollment_no . "'
                              LEFT JOIN courses_two on course_id = co_course_id
                              WHERE co_id = " . $parent_co_id . " ";

        $attendance_json = DB::select($student_attendance);
        // return $student_attendance;exit();
        return json_decode(json_encode($attendance_json), true);
        //return $attendance_json;
    }
    public function get_course_attendance_summary($co_id)
    {
        $query = "SELECT
                CO_TOTAL_CLASSES,
                CO_REQ_CLASSES,
                total_attendance_held(co_id) AS delived_classes,
                (CO_TOTAL_CLASSES*CO_REQ_CLASSES/100) as  required_classes,
                CO_TOTAL_CLASSES-((CO_TOTAL_CLASSES*CO_REQ_CLASSES/100)) as allowed_absents
            FROM
                courses_offered
            WHERE
                co_id = $co_id
            and co_status='E'
              ";

        $result = DB::select($query);
        return $result;
    }
    public function attendance_slot_summary($co_id)
    {
        $query = "SELECT
                CO_TOTAL_CLASSES,
                CO_REQ_CLASSES,
                total_attendance_held(co_id) AS delived_classes,
                (CO_TOTAL_CLASSES*CO_REQ_CLASSES/100) as  required_classes,
                CO_TOTAL_CLASSES-((CO_TOTAL_CLASSES*CO_REQ_CLASSES/100)) as allowed_absents
            FROM
                courses_offered
            WHERE
                co_id = $co_id
            and co_status='E'
              ";
        $result = DB::select($query);
        return $result;
    }
    public function slot_summary($tt_id)
    {
        $full_tt_id = explode("~", $tt_id);
        $query = "SELECT
                                (
                                SELECT
                                COUNT(ca_attendance)
                                FROM
                                course_attendance
                                WHERE
                                mtt_id = $full_tt_id[0]
                                AND
                                ca_attendance = 'P'
                                ) AS total_presents,
                                (
                                SELECT
                                COUNT(ca_attendance)
                                FROM
                                course_attendance
                                WHERE
                                mtt_id = $full_tt_id[0]
                                AND
                                ca_attendance = 'A'
                                ) AS total_absents
                                FROM
                                dual  ";
        $result = DB::select($query);
        return $result;
    }
    public function check_class($employee_id)
    {
        $query = "SELECT
    *
FROM
    (
        SELECT
            room_name,
            tt_sess_id,
            mtt_co_id,
            tt_id,
            tt_class_date,
            get_slotname(tt_subject_time_from_id) AS start_time,
            get_slotname(tt_subject_time_to_id) AS end_time,
                CASE
                    WHEN
                        ( get_time_difference(ts_time_stamp) - 30 ) < 0
                    AND
                        ( (
                            SELECT
                                get_time_difference(ts_time_stamp)
                            FROM
                                timetable_slots
                            WHERE
                                ts_id = master_timetable.tt_subject_time_from_id
                        ) + 10 ) > 0
                    AND
                        substr(tt_class_date,0,10) = substr(TO_DATE(SYSDATE),0,10)
                    THEN 'YES'
                    ELSE 'NO'
                END
            AS today_class_status,
            get_time_difference(ts_time_stamp) - 30 AS time_left,
            (
                SELECT
                    get_time_difference(ts_time_stamp)
                FROM
                    timetable_slots
                WHERE
                    ts_id = master_timetable.tt_subject_time_from_id
            ) + 10 AS time_start,
            tt_subject_time_to_id,
            room_ip
        FROM
            master_timetable
            LEFT JOIN timetable_slots ON tt_subject_time_to_id = ts_id
            LEFT JOIN building_rooms ON master_timetable.tt_room_name = building_rooms.room_id
            LEFT JOIN courses_offered ON master_timetable.mtt_co_id = courses_offered.co_id
        WHERE
                1 = 1
            AND
                tt_class_date <= TO_DATE(SYSDATE)
            AND
                mtt_co_id IN (
                    SELECT
                        co_id
                    FROM
                        courses_offered
                        LEFT JOIN academic_sessions ON co_sess_id = sess_id
                        LEFT JOIN schools ON co_school_id = school_id
                        LEFT JOIN courses_two ON co_course_id = course_id
                    WHERE
                            1 = 1
                        AND
                            co_sess_id = (
                                SELECT
                                    MAX(sess_id)
                                FROM
                                    course_attendance
                                WHERE
                                    1 = 1
                            )
                        AND
                            co_status = 'E'
                        AND
                            co_id IN (
                                SELECT
                                    fc_co_id
                                FROM
                                    faculty_courses
                                    LEFT JOIN emp_employees ON fc_employee_id = employee_id
                                WHERE
                                    faculty_courses.fc_employee_id = 350
                            )
                )
    )
WHERE
        1 = 1
        and  (
                time_left < 0
            AND
                to_number(time_start) > 0   AND
                today_class_status = 'YES'
				 and tt_sess_id = (select max(co_sess_id) from course_attendance)
        )

     "; //echo $query;exit();
        $result = DB::select($query);
        return $result; //and tt_id =113301
    }
    public function loadClassDatesModelDashboard($get_co_id, $tt_id)
    {

        $class_date = "SELECT
                                *
                            FROM
                                (
                                    SELECT
                                        tt_id,
                                        tt_class_date,
                                        get_slotname(tt_subject_time_from_id) AS start_time,
                                        get_slotname(tt_subject_time_to_id) AS end_time,
                                        CASE
                                                WHEN
                                                    ( get_time_difference(ts_time_stamp) - 30 ) < 0
                                                AND
                                                    ( (
                                                        SELECT
                                                            get_time_difference(ts_time_stamp)
                                                        FROM
                                                            timetable_slots
                                                        WHERE
                                                            ts_id = master_timetable.tt_subject_time_from_id
                                                    ) + 10 ) > 0
                                                AND
                                                    substr(tt_class_date,0,10) = substr(TO_DATE(SYSDATE),0,10)
                                                THEN 'YES'
                                                ELSE 'NO'
                                            END
                                        AS today_class_status,
                                            CASE
                                                WHEN tt_class_date < TO_DATE(SYSDATE) THEN 'ALLOWED'
                                                WHEN
                                                    ( get_time_difference(ts_time_stamp) - 30 ) < 0
                                                AND
                                                    ( (
                                                        SELECT
                                                            get_time_difference(ts_time_stamp)
                                                        FROM
                                                            timetable_slots
                                                        WHERE
                                                            ts_id = master_timetable.tt_subject_time_from_id
                                                    ) + 10 ) > 0
                                                AND
                                                    substr(tt_class_date,0,10) = substr(TO_DATE(SYSDATE),0,10)
                                                THEN 'ALLOWED'
                                                WHEN
                                                    ( get_time_difference(ts_time_stamp) - 30 ) > 0
                                                AND
                                                    ( (
                                                        SELECT
                                                            get_time_difference(ts_time_stamp)
                                                        FROM
                                                            timetable_slots
                                                        WHERE
                                                            ts_id = master_timetable.tt_subject_time_from_id
                                                    ) + 10 ) > 0
                                                AND
                                                    substr(tt_class_date,0,10) = substr(TO_DATE(SYSDATE),0,10)
                                                THEN 'ALLOWED'
                                                ELSE 'NO'
                                            END
                                        AS if_allowed,
                                        get_time_difference(ts_time_stamp) - 30 AS time_left,
                                        (
                                            SELECT
                                                get_time_difference(ts_time_stamp)
                                            FROM
                                                timetable_slots
                                            WHERE
                                                ts_id = master_timetable.tt_subject_time_from_id
                                        ) + 10 AS time_start,
                                        ts_time_stamp,
                                        tt_subject_time_to_id,
                                        TO_CHAR(TO_DATE(tt_class_date),'DAY') AS class_day_name,
                                        tt_room_id,
                                        rfid_mechine_ip,
                                        tt_room_name,
                                        room_ip,
                                        room_name,
                                        mtt_co_id,
										case when substr(tt_class_date,1,10)>=substr(trim(sysdate),1,10) then 'coming' else 'past' end as checkclass,
                                        tt_subject_time_from_id,
                                         (select count(*) from course_attendance where mtt_id = tt_id and  co_id in
                                       (select co_id from courses_offered where co_id = mtt_co_id or parent_co_id = mtt_co_id)) as attendance_marked,
                                       mtt_class_detail
                                    FROM
                                        master_timetable
                                        LEFT JOIN timetable_slots ON tt_subject_time_to_id = ts_id
                                        LEFT JOIN building_rooms ON master_timetable.tt_room_name = building_rooms.room_id
                                        LEFT JOIN courses_offered ON master_timetable.mtt_co_id = courses_offered.co_id
                                    WHERE
                                            1 = 1
                                        AND
                                            tt_status = 'E'
                                                                              AND
                                            mtt_co_id = $get_co_id
                                )
                            WHERE
                                    1 = 1
                               ";

        $class_date .= "  ORDER BY tt_class_date,tt_subject_time_from_id,start_time desc ";
        //echo $class_date;exit;
        $result = DB::select($class_date);

        return $result;
        // }
    }
    public function loadClassDatesModel($get_co_id, $tt_id)
    {
        $class_date = "SELECT
                                *
                            FROM
                                (
                                    SELECT
                                        tt_id,
                                        tt_class_date,
                                        get_slotname(tt_subject_time_from_id) AS start_time,
                                        get_slotname(tt_subject_time_to_id) AS end_time,
                                        CASE
                                                WHEN
                                                    ( get_time_difference(ts_time_stamp) - 30 ) < 0
                                                AND
                                                    ( (
                                                        SELECT
                                                            get_time_difference(ts_time_stamp)
                                                        FROM
                                                            timetable_slots
                                                        WHERE
                                                            ts_id = master_timetable.tt_subject_time_from_id
                                                    ) + 10 ) > 0
                                                AND
                                                    substr(tt_class_date,0,10) = substr(TO_DATE(SYSDATE),0,10)
                                                THEN 'YES'
                                                ELSE 'NO'
                                            END
                                        AS today_class_status,
                                            CASE
                                                WHEN tt_class_date < TO_DATE(SYSDATE) THEN 'ALLOWED'
                                                WHEN
                                                    ( get_time_difference(ts_time_stamp) - 30 ) < 0
                                                AND
                                                    ( (
                                                        SELECT
                                                            get_time_difference(ts_time_stamp)
                                                        FROM
                                                            timetable_slots
                                                        WHERE
                                                            ts_id = master_timetable.tt_subject_time_from_id
                                                    ) + 10 ) > 0
                                                AND
                                                    substr(tt_class_date,0,10) = substr(TO_DATE(SYSDATE),0,10)
                                                THEN 'ALLOWED'
                                                WHEN
                                                    ( get_time_difference(ts_time_stamp) - 30 ) > 0
                                                AND
                                                    ( (
                                                        SELECT
                                                            get_time_difference(ts_time_stamp)
                                                        FROM
                                                            timetable_slots
                                                        WHERE
                                                            ts_id = master_timetable.tt_subject_time_from_id
                                                    ) + 10 ) > 0
                                                AND
                                                    substr(tt_class_date,0,10) = substr(TO_DATE(SYSDATE),0,10)
                                                THEN 'ALLOWED'
                                                ELSE 'NO'
                                            END
                                        AS if_allowed,
                                        get_time_difference(ts_time_stamp) - 30 AS time_left,
                                        (
                                            SELECT
                                                get_time_difference(ts_time_stamp)
                                            FROM
                                                timetable_slots
                                            WHERE
                                                ts_id = master_timetable.tt_subject_time_from_id
                                        ) + 10 AS time_start,
                                        ts_time_stamp,
                                        tt_subject_time_to_id,
                                        TO_CHAR(TO_DATE(tt_class_date),'DAY') AS class_day_name,
                                        tt_room_id,
                                        tt_room_name,
                                        room_ip,
                                        rfid_mechine_ip,
                                        room_name,
                                        mtt_co_id,
                                       tt_subject_time_from_id,
                                       (select count(*) from FACULTY_COURSE_ATTENDANCE where FACULTY_COURSE_ATTENDANCE.mtt_id =$tt_id and tt_status='E') as attendance_mark_status
                                    FROM
                                        master_timetable
                                        LEFT JOIN timetable_slots ON tt_subject_time_to_id = ts_id
                                        LEFT JOIN building_rooms ON master_timetable.tt_room_name = building_rooms.room_id
                                        LEFT JOIN courses_offered ON master_timetable.mtt_co_id = courses_offered.co_id
                                    WHERE
                                            1 = 1
                                        AND
                                            tt_status = 'E'
                                        AND
                                            tt_class_date <= TO_DATE(SYSDATE)
                                        AND
                                            mtt_co_id = $get_co_id
                                )
                            WHERE
                                    1 = 1
                                AND (
                                    (
                                            time_left < 0
                                        AND
                                            to_number(time_start) > 0
                                        AND
                                            today_class_status = 'YES'
                                    ) OR
                                        today_class_status = 'NO'
                                ) AND
                                    if_allowed = 'ALLOWED' ";
        if ($tt_id != "" && $tt_id > 0) {
            $class_date .= " AND tt_id = '$tt_id' ";
        }
        $class_date .= "  ORDER BY tt_class_date,tt_subject_time_from_id desc ";


        //  echo $class_date;exit();

        $result = DB::select($class_date);
        return $result;
        // }
    }
    public function student_course_attendance_model($co_id, $sai_enrollment_no, $attendance_type)
    {

        $class_date = "SELECT
                                *
                            FROM
                                course_attendance
                            WHERE
                                    co_id = $co_id
                                AND
                                    sai_enrollment_no = '$sai_enrollment_no'
                                ";
        if ($attendance_type == "P") {
            $class_date .= " AND ca_attendance = '$attendance_type' ";
        }
        if ($attendance_type == "A") {
            $class_date .= " AND ca_attendance = '$attendance_type' and  CA_HOD_ATTENDANCE is null ";
        }
        if ($attendance_type == "EX") {
            $class_date .= " AND CA_HOD_ATTENDANCE = '$attendance_type' ";
        }

        $class_date .= " order by CA_ATTENDANCE_DATE asc ";
        // if($get_co_id>0){
        $result = DB::select($class_date);
        return $result;
        // }
    }


}
