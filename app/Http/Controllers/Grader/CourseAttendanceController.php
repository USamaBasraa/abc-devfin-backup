<?php

namespace App\Http\Controllers\Grader;

use App\Http\Controllers\Controller;
use App\Models\Grader\CourseAttendance;
use DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use App\Jobs\WithdrawonShortAttendance;
use App\Http\Requests\Attendance;
use App\Http\Requests\StoreAttendanceManagement;
//use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Response;
use App\sessions;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\Models\Grader\CoursesOffered;
use App\Models\Grader\MasterTimetable;
use App\Models\Grader\AttendanceSlots;
use App\Models\Grader\StudentsApplyInfos;
use App\Http\Middleware\TrustProxies;
use App\Event\Attendance as event_attendance;
use App\Models\Grader\SendSms as SMS;


class CourseAttendanceController extends Controller
{
    private $sortBy = 'id';
    private $sortDirection = 'ASC';

    private $courseAttendance;

    public function __construct(CourseAttendance $courseAttendance)
    {
        $this->courseAttendance = $courseAttendance;
    }


    function attendanceNew()
    {
        return view('grader.attendance/attendance_new');
    }
    public function index(Request $request, $co_id, $slot_id, $slot_date)
    {
        $courseAttendance = new CourseAttendance;
        $get_slot_list = $this->loadClassDates($co_id, $tt_id = 0);
        $rfid_ip = null;
        // dd($get_slot_list,$slot_date);
        foreach ($get_slot_list as $slot) {
            
            if ($slot->tt_class_date == $slot_date && !empty($slot->rfid_mechine_ip)) {
                $rfid_ip = $slot->rfid_mechine_ip;
                break;
            }
        }
        //    dd($rfid_ip);
        $courseAttendance->CreateCourseAttendance($co_id, $slot_id, $slot_date, $rfid_ip);
        // dd($get_slot_list);
        // $courseAttendance->CreateCourseAttendance($co_id, $slot_id, $slot_date, $get_slot_list[0]->rfid_mechine_ip);
        if (count($get_slot_list) == 0) {
            return '<div style="margin-left: auto; margin-right: auto; text-align: center;">Timetable not created please contact to Registrar Office</div>';
        } else {
            $get_slot_ip = $get_slot_list[0]->room_ip;
            $slot_ip_yes = "";
            $slot_id_now = "";
            $ts_time_stamp = "00:00:00";

            // echo $request->ip();
            // echo '<pre>';
            // print_r($get_slot_list);
            // echo '</pre>';

            // exit();

            // echo 'ok';exit();

            foreach ($get_slot_list as $row_slot_list) {
                if ($row_slot_list->today_class_status == "YES" && $row_slot_list->tt_id == $slot_id) {

                    // echo $request->ip()."___".$row_slot_list->room_ip;exit();//$row_slot_list->tt_id."____".$slot_id;exit();

                    // echo $row_slot_list->room_name; exit();
                    if ($request->ip() == $row_slot_list->room_ip or $row_slot_list->room_name == 'Online') {
                        //  echo $row_slot_list->today_class_status."__".$request->ip()."__".$row_slot_list->room_ip;
                        $slot_ip_yes = "YES~" . $row_slot_list->room_name;
                        $ts_time_stamp = $row_slot_list->ts_time_stamp;
                    } else {
                        $slot_ip_yes = "NO~" . $row_slot_list->room_name;
                    }
                    $slot_id_now = $row_slot_list->tt_id;

                }
            }
            $course_name = course_name($co_id);
        }

        return view('grader.attendance.student_attendance', compact('slot_ip_yes', 'co_id', 'slot_id', 'course_name', 'slot_id_now', 'get_slot_list', 'ts_time_stamp'));

    }

    public function load_studentlist_attendance($co_id, $slot_id)
    {


        $sql_class_status = "SELECT * FROM DAILY_LECTURES WHERE TT_ID = $slot_id ";
        $var_class_status = DB::select($sql_class_status)[0]->lecture_status;

        $sql = "SELECT
                (
                    SELECT
                        count(*)
                    FROM
                        student_challan
                    WHERE
                        lower(sc_enrollment_no) = lower(sc_sai_enrollment_no)
                        AND sc_challan_type IN ('Full_semester', 'Fine')
                        AND (
                            CASE 
                                WHEN sc_challan_type = 'Full_semester'
                                    AND SYSDATE > sc_valid_date
                                    AND callback_paid_on IS NULL
                                THEN 1
                                WHEN sc_challan_type = 'Fine'
                                    AND SYSDATE > sc_due_date
                                    AND callback_paid_on IS NULL
                                THEN 1
                                ELSE 0
                            END
                        ) = 1
                ) AS paid_status,
                sc_status,
                sc_block_status,
                sc_id,
                sc_sai_enrollment_no
            FROM
                student_courses
            WHERE
                sc_co_id IN (
                    SELECT
                        co_id
                    FROM
                        courses_offered
                    WHERE
                        co_id = $co_id
                        OR parent_co_id = $co_id
                )
                AND sc_status = 'Block'
                AND sc_block_status = 'Auto'";
        $resSql = DB::select($sql);
        //echo $sql;exit;

        foreach ($resSql as $row) {
            $scId = $row->sc_id;
            if ($row->paid_status == 0) {
                //  $updScStatus = "update student_courses set sc_status = 'A',sc_block_status = '',sc_desc='' where sc_id = $scId ";
                $updScStatus = "  update student_courses set sc_status = 'A',sc_block_status = '',sc_desc='Active When Faculty Open Attendance page' where sc_sai_enrollment_no ='$row->sc_sai_enrollment_no' and sc_status='Block' and sc_block_status='Auto'";
                $resupdScStatus = DB::insert($updScStatus);
                //echo $updScStatus;
            }
        }

        $sql = "SELECT
        $slot_id as slot_id,
        spi_name,
        students_apply_info.sai_enrollment_no,
        student_courses.total_presents as presents,
        student_courses.TOTAL_CLASSES,
        student_courses.ATTENDANCE_PERCENTAGE,
        (student_courses.total_classes-student_courses.total_presents) as  absents,
        (
            SELECT
                id
            FROM
                course_attendance
            WHERE
                    ROWNUM = 1
                AND
                    course_attendance.sai_enrollment_no = students_apply_info.sai_enrollment_no
                AND
                    mtt_id = $slot_id
        ) AS ca_id,
        (
            SELECT
                ca_status
            FROM
                course_attendance
            WHERE
                    ROWNUM = 1
                AND
                    course_attendance.sai_enrollment_no = students_apply_info.sai_enrollment_no
                AND
                    mtt_id = $slot_id
        ) AS ca_status,
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
                    mtt_id = $slot_id
        ) AS attendance_status,
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
                    ca_hod_attendance = 'EX'
                AND
                    course_attendance.co_id = student_courses.sc_co_id
        ) AS exempted,
        sai_student_status,
        sc_co_id,
        (
            SELECT
                co_total_classes
            FROM
                courses_offered
            WHERE
                    co_id = $co_id
                AND
                    co_status = 'E'
        ) AS co_total_classes,
        (
        SELECT
            count(*)
        FROM
            FACULTY_COURSE_ATTENDANCE
        WHERE
             FACULTY_COURSE_ATTENDANCE.co_id = $co_id
            AND
                fca_status = 'E'
        ) AS delivered_classess,
        (
            SELECT
                co_req_classes
            FROM
                courses_offered
            WHERE
                    co_id = $co_id
                AND
                    co_status = 'E'
        ) AS co_req_classes,
        'NO' AS class_status,
        course_cd_name,
        course_code,
        sc_status,
        ssr_semester_status,
        allowed_absents,sc_desc,sc_block_status
    FROM
        student_courses
        LEFT JOIN courses_offered ON sc_co_id = co_id
        LEFT JOIN courses_two ON course_id = courses_offered.co_course_id
        LEFT JOIN students_apply_info ON student_courses.sc_sai_enrollment_no = students_apply_info.sai_enrollment_no
        LEFT JOIN students_personal_info ON students_apply_info.sai_spi_id = students_personal_info.spi_id
        LEFT JOIN students_semester_record ON
            students_semester_record.sai_enrollment_no = student_courses.sc_sai_enrollment_no
        AND
            students_semester_record.sess_id = courses_offered.co_sess_id
    WHERE
            1 = 1
        AND
            sc_enrollment_status = 1
        AND
            sc_status NOT IN (
                'Withdrawal'
            )
        AND (
            student_courses.sc_co_id = $co_id
            OR courses_offered.parent_co_id = $co_id
            OR courses_offered.parent_co_id IN (
                SELECT co_id
                FROM courses_offered
                WHERE parent_co_id = $co_id
            )
        )
        -- AND (
        --         sc_co_id = $co_id
        --     OR
        --         parent_co_id = $co_id
        -- ) 
        AND students_apply_info.sai_enrollment_no IS NOT NULL
    ORDER BY LOWER(spi_name)";
        $student_data = DB::select($sql);

        //echo  $sql;exit();
        return view('grader.attendance.students_attendance_list', compact('var_class_status', 'student_data'));

    }
    public function update_student_attendance($ca_id, $status)
    {


        if (
            DB::table('course_attendance')
                ->where(['id' => $ca_id, 'ca_status' => 'PENDING'])
                ->update(
                    [
                        'ca_attendance' => $status,
                        'updated_at' => date('Y-m-d H:i:s')
                    ]
                )
        ) {


            return 1;
        }
    }

    public function checkAllAbsent($slot_id)
    {
        $data = DB::selectOne("
            SELECT
                COUNT(*) AS total,
                SUM(CASE WHEN ca_attendance = 'A' THEN 1 ELSE 0 END) AS total_absent
            FROM course_attendance
            WHERE mtt_id = ?
        ", [$slot_id]);

        $total = $data ? (int) $data->total : 0;
        $totalAbsent = $data ? (int) $data->total_absent : 0;

        return response()->json([
            'all_absent' => $total > 0 && $total === $totalAbsent,
        ]);
    }

    /*
     *
     *   USE IN API
     *
     */
    public function final_submit_attendance($mtt_id)
    {

        $courseOffered = new CoursesOffered(); // object of courses offered
        $masterTimetable = new MasterTimetable(); // object of MasterTimetable
        $co_id = $masterTimetable::find($mtt_id)->mtt_co_id;

        // event(new event_attendance($rec));
        $courseOfferedData = $courseOffered::find($co_id)->first();

        DB::table('faculty_course_attendance')
            ->where('mtt_id', $mtt_id)
            ->update(
                [
                    'fca_status' => 'E',
                    'updated_at' => date('Y-m-d H:i:s')
                ]
            );
        // Course Attendance Final Submit Update
        DB::table('course_attendance')
            ->where('mtt_id', $mtt_id)
            ->update(
                [
                    'ca_status' => 'E',
                    'ca_entry_datetime' => date('Y-m-d H:i:s')
                ]
            );


        //     $final_submit = 'COURSE_ATTENDANCE_UPDATE';
        //     $parameters = [
        //         'co_id'           => $co_id
        //     ];
        //   $resultAttendance = DB::executeProcedure($final_submit, $parameters);
        // WithdrawonShortAttendance::dispatch($rec)->delay(now()->addMinutes(1));

        //   if(){
        // if get some red zone then call this job
        //   }
        // sc_sai_enrollment_no,(total_classes-total_presents) as absents,
        $withdrawSTudentList = "select * from(
                    SELECT
					sc_sai_enrollment_no,
                        TOTAL_ATTENDANCE_ABSENT(sc_co_id,sc_sai_enrollment_no) as absents,                       
                        allowed_absents,
                        spi_mobile,
						sc_cms_course_co_id,get_course_name(sc_cms_course_co_id) as course_name,offering_source
                    FROM
                        student_courses
                    left join students_apply_info on sai_enrollment_no = sc_sai_enrollment_no
                    left join students_personal_info on sai_spi_id = spi_id
                    WHERE
                        sc_co_id IN (
                            SELECT
                                co_id
                            FROM
                                courses_offered
                            WHERE
                                co_id =      $co_id    
                                OR parent_co_id =      $co_id    
                        )
                         AND sc_status NOT IN ('Withdrawal')
						 AND SC_ENROLLMENT_STATUS=1
                        )
                        where absents >= allowed_absents-1";

        $rec = DB::select($withdrawSTudentList);

        WithdrawonShortAttendance::dispatch($rec, auth()->user()->employee_id, $co_id)->delay(now()->addMinutes(1));



        return response()->json(['message' => 'Attendance Saved Successfully . . .']);

    }




    public function update_slot_contents(Request $request)
    {
        $select = "select coc_details from course_offering_contents where coc_tt_id=" . $request->input('slot_id');
        $rec_select = DB::select($select);
        if (count($rec_select) > 0) {
            $insert = "update COURSE_OFFERING_CONTENTS set COC_DETAILS = '" . $request->input('course_contents') . "'
                where coc_tt_id = " . $request->input('slot_id');
            DB::insert($insert);
        } else {
            $insert = "insert into COURSE_OFFERING_CONTENTS(COC_DETAILS,coc_co_id,coc_tt_id) values ('" . $request->input('course_contents') . "','" . $request->input('co_id') . "','" . $request->input('slot_id') . "')";
            DB::insert($insert);
        }
    }



    public function index2(Request $request, $co_id)
    {

        $get_slot_list = $this->loadClassDates($co_id, $tt_id = 0);

        if (count($get_slot_list) == 0) {
            return '<div style="margin-left: auto; margin-right: auto; text-align: center;">Timetable not created please contact to Registrar Office</div>';
        } else {
            $get_slot_ip = $get_slot_list[0]->room_ip;
            $slot_ip_yes = "";
            $slot_id_now = "";
            foreach ($get_slot_list as $row_slot_list) {
                if ($row_slot_list->today_class_status == "YES") {
                    if ($request->ip() == $row_slot_list->room_ip) {
                        //     echo $row_slot_list->today_class_status."__".$request->ip()."__".$row_slot_list->room_ip;
                        $slot_ip_yes = "YES";
                    } else {
                        $slot_ip_yes = "NO";
                    }
                    $slot_id_now = $row_slot_list->tt_id;

                }
            }
            return view(
                'grader/attendance/attendance',
                [
                    'class_date' => $this->loadClassDates($co_id, $tt_id = 0),
                    'slot_ip_yes' => $slot_ip_yes,
                    'slot_id_now' => $slot_id_now,
                    'course_name' => course_name($co_id)
                ]
            );
        }
    }

    ////////////////////////////////new//////////////////////////////////
    public function index1(Request $request, $co_id)
    {


        $get_slot_list = $this->loadClassDates($co_id, $tt_id = 0);
        if (count($get_slot_list) == 0) {
            return '<div style="margin-left: auto; margin-right: auto; text-align: center;">Timetable not created please contact to Registrar Office</div>';
        } else {
            $get_slot_ip = $get_slot_list[0]->room_ip;
            $slot_ip_yes = "";
            $slot_id_now = "";
            foreach ($get_slot_list as $row_slot_list) {

                if ($row_slot_list->today_class_status == "YES") {
                    if ($request->ip() == $row_slot_list->room_ip) {
                        //     echo $row_slot_list->today_class_status."__".$request->ip()."__".$row_slot_list->room_ip;
                        $slot_ip_yes = "YES";
                    } else {
                        $slot_ip_yes = "NO";
                    }
                    $slot_id_now = $row_slot_list->tt_id;

                }
            }
            return view(
                'grader/attendance/attendance',
                [
                    'class_date' => $this->loadClassDates($co_id, $tt_id = 0),
                    'slot_ip_yes' => $slot_ip_yes,
                    'slot_id_now' => $slot_id_now
                ]
            );
        }
    }

    //////////////////////////////new end //////////////////////////////////////

    public function course_attendance_summary($co_id)
    {
        $get_course_attendance_summary = $this->courseAttendance->get_course_attendance_summary($co_id);
        return $get_course_attendance_summary;
    }
    public function loadAttendance($get_tt_id, $co_id, $class_status)
    {

        $full_tt_id = explode("~", $get_tt_id);
        //$full_tt_id = $get_tt_id;
        // $get_slot_date      =   Input::get('slot_date_id');
        $get_slot_date = request()->get('slot_date_id');
        $get_slot_and_date = explode('~', $get_slot_date);

        $student_data = $this->courseAttendance->show_students($full_tt_id[0], $co_id, $class_status);
        //210979__11070__NO
        //echo $full_tt_id[0]."__".$co_id."__".$class_status;
        //return $student_data;
        // exit();
        $get_course_attendance_summary = $this->courseAttendance->get_course_attendance_summary($co_id);
        $attendance_mark = 0;
        $attendance_present = 0;
        $attendance_absent = 0;
        foreach ($student_data as $row_student_date) {
            if ($row_student_date->attendance_status == "P") {
                $attendance_present++;
            } else if ($row_student_date->attendance_status == "A") {
                $attendance_absent++;
            }
        }
        return view(
            'grader.attendance.attendance_students',
            [
                'student_data' => $student_data,
                'attendance_present' => $attendance_present,
                'attendance_absent' => $attendance_absent,
                'get_course_attendance_summary' => $get_course_attendance_summary
            ]
        );
    }
    public function attendance_slot_summary($get_tt_id)
    {
        //  echo 'ok';exit();
        $get_attendance_slot_summary = $this->courseAttendance->slot_summary($get_tt_id);
        return $get_attendance_slot_summary;

    }
    public function student_course_attendance($co_id, $sai_enrollment_no, $attendance_type)
    {
        $student_course_attendance = $this->courseAttendance->student_course_attendance_model($co_id, $sai_enrollment_no, $attendance_type);
        return view(
            'grader/attendance/student_course_attendance',
            [
                'student_course_attendance' => $student_course_attendance
            ]
        );
    }
    public function submitAttendancePreview(Attendance $request)
    {
        $all_data = Input::all();
        $ca_attendance = $all_data['ca_attendance'];
        $total_presents = 0;
        $total_absents = 0;
        for ($rows_num = 0; $rows_num < count($all_data['ca_attendance']); $rows_num++) {
            if ($all_data['ca_attendance'][$rows_num] == "P") {
                $total_presents++;
            } else if ($all_data['ca_attendance'][$rows_num] == "A") {
                $total_absents++;
            }
        }

        $students_apply_info = new StudentsApplyInfos();
        $rec = $students_apply_info::find('F2013-080')->sai_id;


        echo 'Total : ' . count($ca_attendance) . '<br> Present : ' . $total_presents . '<br> Absent :' . $total_absents;
    }
    public function submitAttendance(Attendance $request)
    {
        $all_data = Input::all();
        //print_r($all_data['slot_co_id']);exit();
        $course_co_id = $all_data['slot_co_id'];
        $sai_enrollment_no = $all_data['sai_enrollment_no'];
        $ca_attendance = $all_data['ca_attendance'];
        $slot_id_combine = explode("~", $all_data['slot_date_id']);
        $slot_date_id = $slot_id_combine[0];
        $sc_co_id = $all_data['sc_co_id'];
        $store_attendance = $this->courseAttendance->store($sai_enrollment_no, $ca_attendance, $slot_date_id, $sc_co_id, $course_co_id);
        return $store_attendance;
    }
    public function loadClassDates($get_co_id, $tt_id)
    {
        return $this->courseAttendance->loadClassDatesModel($get_co_id, $tt_id);
    }
    public function loadClassSlotDetail(Request $request, $get_co_id, $tt_id)
    {
        $get_date = $this->courseAttendance->loadClassDatesModel($get_co_id, $tt_id);
        if ($get_date[0]->today_class_status == "YES") {
            if ($get_date[0]->room_ip == $request->ip()) {
                $class_status = "IP_YES";
            } else {
                $class_status = "IP_NO";//.$get_date[0]->room_ip."==".$request->ip();
            }
        } else {
            $class_status = "NO_CLASS";
        }

        return $class_status;

    }
    public function retrieveHodAttendance($co_id)
    {
        // echo '123';exit;
        return $this->courseAttendance->fetchHodAttendance($co_id);
    }
    public function retrieveAttendanceDetails($co_id, $sai_id)
    {
        //echo "123";exit;
        //return 'ok';
        return $this->courseAttendance->fetchAttendanceDetails($co_id, $sai_id);
    }
    public function storeAttendanceDetails(StoreAttendanceManagement $request)
    {
        $data = $request->all();
        return $this->courseAttendance->saveExemption($data);
    }
    public function storeAttendanceAll(Request $request)
    {

        return $request->all();

    }
    public function check_class($employee_id)
    {
        $get_check_class = $this->courseAttendance->check_class($employee_id);
        return $get_check_class;
    }

    public function create_atendance_by_api(Request $request)
    {
        $co_id = $request->input('co_id');
        $slot_id = $request->input('slot_id');
        $slot_date = $request->input('slot_date');
        $user_id = $request->input('user_id');
        $courseAttendance = new CourseAttendance;
        $courseAttendance->CreateCourseAttendance_RO($co_id, $slot_id, $slot_date, $user_id);
        return response()->json(['success' => 'Attendance Created Successfully']);
    }
}
