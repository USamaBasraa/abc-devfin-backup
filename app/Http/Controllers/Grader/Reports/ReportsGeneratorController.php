<?php

namespace App\Http\Controllers\Grader\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StudentCourses;
use DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Session;
class ReportsGeneratorController extends Controller
{

    public function index(Request $request)
    {
        // Academic sessions (newest first)
        $recsqlAcademicSessions = DB::table('academic_sessions')
            ->orderBy('sess_id', 'desc')
            ->get();

        $recSchool = [];
        $groupId = auth()->user()->group_id;
        $schoolId = auth()->user()->school_id;
        $username = auth()->user()->username;


        $schoolIds = is_array($schoolId) ? $schoolId : [$schoolId];


        // Allowed usernames list
        $allowedUsernames = [
            'hifsa.farooq',
            'zara.amjad',
            'arez.ali',
            'ahsan.fazal',
            'arsalan.sulahri',
            'sara.assad',
            'ghammaz.husnain',
            'zeeshan.sarwar',
            'abdul.qadir',
            'saima.zaigham',
            'nouman.ali',
            'amna.humayun',
            'nabeel.ahsan',
            'haris.iftikhar',
            'osama.tariq',
            'huda.sarfraz',
            'haider.ali1',
            'asim.irshad',
            'noreen.khalid',
            'muhammad.ali1',
            'ameer.hamza',
            'farhan.ali',
            'talia.noreen',
            'ammar.faiz',
            'aatiqa.sheikh',
            'umaima.mohsin',
            'sana.aziz',
            'zoona.kundi',
            'neha.qasim',
            'werdah.munib',
            'tabina.sirhindi',
            'harris.badar',
            'zeeshan.zaighum',
            'nasir.ali',
            'usman.rana',
            'sheharyar.zahid',
            'warda.shah',
            'nosharwhan.adil',
            'naveed.aasim',
            'farahat.ali',
            'rabia.noor',
            'sarmed.cheema',
            'safdar.abbas1',
            'bnu_hr',
            'reema.qadeer',
            'sumaiya.naumaan',
            'natash.ali',
            'muhammad.adeel',
            'ayesha.seher',
            'zaman.aziz',
            'shazil.ali',
            'sheeza.batool',
            'nimra.abbas',
            'noreen.khalid',
            'ali.hafeez',
            'hamna.anwar',
            
            'sania.khalid',
            'muhammad.adeel' 
        ];

        // Case 1: Group 644 → All schools
        if ($groupId == 644 || $username == 'noreen' || $username == 'neha.qasim' || $username == 'hina.razzaq' || $username == 'bnu_hr') {
            $recSchool = DB::table('schools')->get();
        }
        // Case 2: Dean, Deans SCIT, HOD Ijaz, or allowed usernames
        elseif (
            in_array($groupId, [223, 364, 445])
            || in_array($username, $allowedUsernames)
            || $username == 'noreen'
            || $username == 'neha.qasim'
            || $username == 'hina.razzaq'

        ) {
            if ($groupId == 223) {
                // If schoolId = 1 → only that school
                if ($schoolId == 1) {
                    $schoolIds = [1];
                } else {
                    // Otherwise include user’s school + 21
                    $schoolIds = is_array($schoolId) ? $schoolId : [$schoolId,12];
                    $schoolIds[] = 21;
                }
            }

            // Fetch schools safely for Oracle
            $recSchool = DB::table('schools')
                ->whereIn('school_id', $schoolIds)
                ->get();
        }

        return view('grader.reports.reportsGenerator.index', [
            'academic_session' => $recsqlAcademicSessions,
            'recSchool' => $recSchool
        ]);
    }


    function marksdetail(Request $request)
    {

        // dd($request->all());
        $co_id = $request->co_id;
        $enrollment_no = strtolower(trim($request->enrollment_no));
        // dd($enrollment_no);


        $criteria = $this->get_criteria_details($co_id);
        $cr = count($criteria);
        // dd($criteria);
        if ($criteria) {
            $a_id = $criteria[0]->a_id;



            $sqlMarksDetails = "
        SELECT *
        FROM COURSE_ASSESSMENT_MARKS cam
        LEFT JOIN COURSE_CRITERIA_DETAILS ccd
            ON cam.COURSE_CRITERIA_DETAIL_ID = ccd.id
        LEFT JOIN courses_offered co
            ON co.co_id = cam.co_id
        LEFT JOIN courses_two ct
            ON co.co_course_id = ct.course_id

        LEFT JOIN assessments a
            ON cam.assessment_id = a.id
            WHERE LOWER(cam.SAI_ENROLLMENT_NO) = :enrollment_no
          AND cam.co_id = :co_id
          AND cam.CAM_STATUS = 'E'
        ORDER BY cam.assessment_id, cam.cam_number
    ";
            // dd($sqlMarksDetails);
            $rsMarksDetails = DB::select($sqlMarksDetails, [
                'enrollment_no' => $enrollment_no,
                'co_id' => $co_id
            ]);



            $weigtage = $this->weigtage($co_id);
            $total_assessments = count($rsMarksDetails);

            // dd()

            return response()->json([
                'co_id' => $co_id,
                'marks_details' => $rsMarksDetails,
                'total_assessments' => $total_assessments,
                'weightage' => $weigtage,
                'criteria' => $criteria,
                'cr' => $cr
            ]);
        }
        return response()->json([
            'co_id' => [],
            'marks_details' => [],
            'total_assessments' => [],
            'weightage' => [],
            'criteria' => [],
            'cr' => []
        ]);
    }



    function weigtage($co_id)
    {

        $summary_sql = "SELECT
                            assessment_name,
                            obtained_marks,
                            total_marks,
                            (
                            SELECT
                            gh_grade
                            FROM
                            grade_history
                            LEFT JOIN courses_offered ON gh_co_id = co_id
                            WHERE
                            gh_sai_enrollment_no = sai_enrollment_no
                            AND (gh_co_id = $co_id OR parent_co_id = $co_id)
                            AND ROWNUM = 1
                            AND gh_exam_fsubmit=1
                            ) AS gh_grade,
                (SELECT gh_grading_type
            FROM grade_history
            LEFT JOIN courses_offered
            ON gh_co_id                = co_id
            WHERE gh_sai_enrollment_no = sai_enrollment_no
            AND (gh_co_id              = 13043
            OR parent_co_id            = 13043)
            AND ROWNUM                 = 1
            AND gh_exam_fsubmit        =1
            ) AS grading_type
                            FROM
                            course_marks_summaries
                            LEFT JOIN assessments ON course_marks_summaries.assessment_id = assessments.id
                            WHERE
                            sai_enrollment_no = '" . Session::get('enrollment_no') . "'
                            AND
                            co_id = $co_id
                            ORDER BY assessments.id";
        //   echo $summary_sql;exit;
        $rssummary_sql = DB::select($summary_sql);
        return $rssummary_sql;
    }


    function get_criteria_details($co_id)
    {

        $criteria_sql = "SELECT
						assessments.assessment_name AS a_name,
						course_criterias.cc_weightage AS cw,
						assessments.id AS a_id,
						get_course_grading_type(course_criterias.co_id) AS course_grading_type,
						course_cd_name,
                        case
		                when cc_method_id=1 then 'Equal Distribution'
		                when cc_method_id=2 then 'Student Wise Best'
		                when cc_method_id=3 then 'Best On Average'
		                when cc_method_id=4 then 'Instructor Selection'
		                END  as method_name,
               			course_criterias.BEST_OF_NO
					FROM
						course_criterias
						LEFT JOIN assessments ON assessment_id = assessments.id
						LEFT JOIN courses_offered ON course_criterias.co_id = courses_offered.co_id
						LEFT JOIN courses_two ON course_id = courses_offered.co_course_id
					WHERE
					course_criterias.co_id = $co_id
					order by assessments.id";
        $rsCriteria = DB::select($criteria_sql);
        return $rsCriteria;
    }

    function getStudentEnrolledCourses()
    {

        //echo "------".Session::get('enrollment_no');
        return view('grader.reportgenerator.student_enrolled_courses_list');

    }

    function getStudentEnrolledCoursesData(Request $request)
    {
        // echo "------".auth()->user()->username; //Session::get('enrollment_no');
        $rollno = $request->get('enrollment_no');
        $day = $request->get('day');
        $result = StudentCourses::getStudentEnrolledCourses($rollno, $day);
        array_walk($result, function (&$val, $index) {
            $val->sc_status_show = ($val->sc_status) ? 'Approved' : $val->sc_status;
        });
        $arr = array(
            'status' => true,
            'data' => $result,
            'message' => 'TimeTable!'
        );
        return response()->json($arr);
    }



    public function indexPsychiatrist(Request $request)
    {
        $sqlAcademicSessions = "select * from academic_sessions order by sess_id desc";
        $sqlSchools = "select * from schools";
        $recSchool = DB::select($sqlSchools);
        $recsqlAcademicSessions = DB::select($sqlAcademicSessions);


        return view('grader.reports.reportsGenerator.addPsychiatristRemarks');
    }


    public function getdept(Request $request, $schoolId)
    {
        $sql = " select dept_id,dept_name,dept_school_id from departments where dept_school_id='$schoolId'";
        $data = DB::select($sql);
        return $data;
    }
    public function getStudentRec(Request $request)
    {
        $academic_session = $request->academic_session;
        $school = $request->school;
        $department = $request->departments;
        $course = $request->course;
        // dd($request->all());

        $sql = "SELECT
        NVL(CAST(cgpa(students_apply_info.sai_enrollment_no) AS NUMBER), 0) AS cgpa,
        (SELECT COUNT(*)
         FROM student_courses
         WHERE sc_sai_enrollment_no = sai_enrollment_no
           AND sc_enrollment_status = 1
           AND sc_co_sess_id = (
               SELECT MAX(sc_co_sess_id)
               FROM student_courses
           )) AS enrollment_status,

        sai_enrollment_no AS enrollment_no,
        sai_programid AS degree_title,
        students_personal_info.spi_name AS student_name,
        sai_student_status as student_status,
        (SELECT COUNT(*)
         FROM STUDENT_WARNING_LETTER
         WHERE swl_enrollment_no = students_apply_info.sai_enrollment_no and status='E') AS total_warning_letters,
        (SELECT COUNT(*)
         FROM students_scholarships_details
         WHERE ssd_sai_enrollment_no = sai_enrollment_no
           AND ssd_sess_id IS NOT NULL) AS scholarship_details
    FROM students_apply_info
    LEFT JOIN students_personal_info
    ON students_apply_info.sai_spi_id = students_personal_info.spi_id
    WHERE sai_student_status IN ('Active', 'Expected-Graduate')";

        if ($academic_session != 'All') {
            $sql .= " AND sai_sdpo_sess_id = '$academic_session' ";
        }

        if ($school) {
            $sql .= " AND sai_sdpo_school_id = '$school' ";
        }

        if ($department) {
            $sql .= " AND sai_sdpo_dept_id = '$department' ";
        }
        // $sql .= " order by sai_id desc";
        $sql .= " order by cgpa asc";
        // echo $sql; exit;
        $data = DB::select($sql);
        return $data;
    }
    public function studentGradeBook(Request $request)
    {

        $sel_enrollment_no = $request->sel_enrollment_no;
        // dd('111111');
        return view('grader.reports.reportsGenerator.studentGradeBook', ['pt_sai_enroll_no' => $sel_enrollment_no]);
    }
    public function studentCourses(Request $request)
    {
        //dd($request->all());
        $sai_enrollment_no = $request->sai_enrollment_no;

        $sql = "  select
        student_courses.sc_sai_enrollment_no,
        academic_sessions.sess_name,
        academic_sessions.sess_id,
        student_courses.attendance_percentage,
        --total_attendance_percentage(student_courses.sc_co_id,student_courses.sc_sai_enrollment_no) as attendance_percentage,
        get_course_name(sc_co_id) as course_title,
        get_teachername(sc_co_id) as teachername,
        student_courses.sc_co_id
        from student_courses
        left join academic_sessions on student_courses.sc_co_sess_id=academic_sessions.sess_id
            left join courses_two on sc_course_id = course_id
        where 1=1
        and sc_status='A'
          and   sc_sai_enrollment_no = '$sai_enrollment_no'
          and attendance_percentage is not null
          order by sess_id asc


        ";
        //echo $sql;exit;
        // dd('1111');
        $data = DB::select($sql);
        return $data;
    }


    public function getDepartments(Request $request)
    {
        //    dd($request->all());
        $departments = DB::select("select * from departments
        where dept_school_id='" . $request->schoolId . "'");
        return $departments;

    }

    public function sendComment(Request $request)
    {
        $sendComment = $request->input('sendComment');
        $sendCommentTo = $request->input('sendCommentTo');

        // dd($request->all());
        // // Create a validator manually
        $validator = Validator::make($request->all(), [
            'sendComment' => 'required|min:10',
        ], [
            'sendComment.required' => 'The Comment field is required.',
            'sendComment.min' => 'The Comment must be at least :min characters.',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return response()->json(['success' => false, 'errors' => $errors], 422);
        }

        DB::table('messages_bnu')->insert([
            'MESSAGE' => $sendComment, // Use the variable directly
            'MESSAGE_TYPE' => 'FacultyComment',
            'ENTRY_USER_ID' => auth()->user()->employee_id,
            'TO_USER_ID' => $request->input('sendCommentTo'),
            'CREATED_AT' => now()->timezone('Asia/Karachi'),
        ]);

        try {
            // Your notification creation code
            return response()->json(['success' => true, 'message' => 'Notification created successfully', 'getComments' => $this->getComments($sendCommentTo)]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error creating notification', 'error' => $e->getMessage()]);
        }
    }


    public function getComments($sendCommentTo)
    {
        // dd($sendCommentTo);
        // Fetch selected columns for the given TO_USER_ID using raw SQL
        $notifications = DB::table('messages_bnu')
            ->select('MESSAGE', 'ENTRY_USER_ID', 'TO_USER_ID', 'CREATED_AT', DB::raw('emp_f_name || \' \' || emp_l_name AS full_name'))
            ->join('emp_employees', 'ENTRY_USER_ID', '=', 'employee_id')
            ->where('TO_USER_ID', $sendCommentTo)
            ->where('MESSAGE_TYPE', 'FacultyComment')
            ->get();


        return $notifications;
    }
    public function sendWarningLetter(Request $request)
    {

        $warningOption = $request->warningOption;
        $sendWarningLetterTo = $request->sendWarningLetterTo;
        $warning_letter_name = '';
        $fileName = '';
        $originalFileName = '';
        $upload = null;
        $url = '';
        $baseUrl = url('/');

        if ($request->hasFile('file')) {
            // Get the original file name
            $originalFileName = $request->file('file')->getClientOriginalName();
            // Get the file extension
            $fileExtension = $request->file('file')->getClientOriginalExtension();
            // Generate a unique filename
            $fileName = uniqid() . '.' . $fileExtension;
            $directoryPath = public_path('docs');
            // Ensure the directory exists
            if (!file_exists($directoryPath)) {
                mkdir($directoryPath, 0777, true); // Create directory with permissions
            }
            $file = $request->file('file');
            $file->move($directoryPath, $fileName);
            if (file_exists($directoryPath . '/' . $fileName)) {
                $upload = true;
                $url = $baseUrl . '/docs/' . $fileName;

                // dd($url);
            } else {
                return response()->json([
                    'message' => 'File not uploaded!',
                    'status' => false
                ]);
            }
        }

        // Loop through each warning option and insert data into the database
        // Check if $warningOption is set and is an array
        if (isset($warningOption) && is_array($warningOption)) {
            foreach ($warningOption as $option) {
                if ($option == '1') {
                    $warning_letter_name = 'Unsatisfactory Academic Performance';
                }
                // Prepare the insert data array
                $insertData = [
                    'swl_enrollment_no' => $sendWarningLetterTo,
                    'warning_letter_type' => $option,
                    'warning_letter_name' => isset($warning_letter_name) ? $warning_letter_name : '',
                    'swl_filename' => $originalFileName, // Insert original file name
                    'swl_file_id' => $upload,
                    'swl_filelink' => $url,
                    'entry_user_id' => auth()->user()->employee_id
                ];

                // Insert data into the database
                DB::table('student_warning_letter')->insert($insertData);
            }
        } else {
            // If $warningOption is empty or not an array, insert with null values
            $insertData = [
                'swl_enrollment_no' => $sendWarningLetterTo,
                'warning_letter_type' => null,
                'warning_letter_name' => null,
                'swl_filename' => $originalFileName, // Insert original file name
                'swl_file_id' => $fileName,
                'swl_filelink' => $url,
                'entry_user_id' => auth()->user()->employee_id
            ];

            // Insert data into the database
            DB::table('student_warning_letter')->insert($insertData);
        }

        try {
            // Your notification creation code
            return response()->json(['success' => true, 'message' => 'Warning letter issued successfully', 'warningLetters' => $this->checkWarningLetter($sendWarningLetterTo)]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error creating Warning letter', 'error' => $e->getMessage()]);
        }
    }
    public function checkWarningLetter($enrollmentNo)
    {
        // dd($enrollmentNo);
        $checkRecords = DB::table('student_warning_letter')
            ->select(
                'id',
                'swl_enrollment_no',
                'warning_letter_type',
                'swl_filename',
                'swl_filelink',
                'swl_file_id',
                'entry_timestamp',
                'status',
                'warning_letter_name',
                DB::raw('emp_f_name || \' \' || emp_l_name AS full_name')
            )
            ->join('emp_employees', 'student_warning_letter.ENTRY_USER_ID', '=', 'emp_employees.employee_id')
            ->where('swl_enrollment_no', strtoupper($enrollmentNo))
            ->get();

        return $checkRecords;
    }
    function checkScholarship($enrollmentNo)
    {
        // $checkRecords = DB::table('students_scholarships_details')

        // ->where('ssd_sai_enrollment_no', strtoupper($enrollmentNo))
        // ->get();

        $checkRecords = "SELECT
    ssd_sai_enrollment_no,sess_name,scholarship_name,students_scholarships_details.ssd_percentage
FROM
    students_scholarships_details
    left join scholarship_type on ssd_type = scholarship_id
    left join academic_sessions on ssd_sess_id = sess_id
WHERE
    ssd_sai_enrollment_no = '$enrollmentNo'
    and ssd_sess_id is not null";
        $res = DB::select($checkRecords);
        // $notifications = DB::table('messages_bnu')
        // ->select('MESSAGE', 'ENTRY_USER_ID', 'TO_USER_ID', 'CREATED_AT', DB::raw('emp_f_name || \' \' || emp_l_name AS full_name'))
        // ->join('emp_employees', 'ENTRY_USER_ID', '=', 'employee_id')
        // ->where('TO_USER_ID', $sendCommentTo)
        // ->where('MESSAGE_TYPE', 'FacultyComment')
        // ->get();
        return $res;
    }


    public function studentGPArec($enrollmentNo)
    {

        $query = "SELECT SUM(gh_grade_point*gh_co_course_cr) as total_points,
								SUM(gh_co_course_cr) as total_cr,
								gh_co_sess_id,
								sess_code
						FROM grade_history
						left join academic_sessions
						on sess_id = gh_co_sess_id
						WHERE gh_sai_enrollment_no='" . $enrollmentNo . "'
						  and gh_grade not in ('W','P','I')

						GROUP BY gh_co_sess_id,sess_code
						ORDER BY gh_co_sess_id";

        $result = DB::select($query);
        return $result;

    }
    // Controller function for sending psychiatrist student comment
    public function sendPsychiatristComment(Request $request)
    {
        $sendComment = $request->input('sendPsychiatrist');
        $sendCommentTo = strtoupper($request->input('sendPsychiatristTo'));
        $getAllRec = strtoupper($request->input('getAllRec'));

        // Create a validator manually
        $validator = Validator::make($request->all(), [
            'sendPsychiatrist' => 'required|min:5',
        ], [
            'sendPsychiatrist.required' => 'The Comment field is required.',
            'sendPsychiatrist.min' => 'The Comment must be at least :min characters.',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return response()->json(['success' => false, 'errors' => $errors], 422);
        }

        try {
            // Insert comment into database
            DB::table('messages_bnu')->insert([
                'MESSAGE' => $sendComment,
                'MESSAGE_TYPE' => 'PsychiatristComment',
                'ENTRY_USER_ID' => auth()->user()->employee_id,
                'TO_USER_ID' => $sendCommentTo,
                'CREATED_AT' => now()->timezone('Asia/Karachi'),
            ]);

            // Get updated list of comments
            $getPsychiatristComments = $this->getPsychiatristComments($sendCommentTo, $getAllRec);

            // Return success response with updated comments
            return response()->json(['success' => true, 'message' => 'Notification created successfully', 'getComments' => $getPsychiatristComments]);
        } catch (\Exception $e) {
            // Log the exception message
            // Log::error('Error creating notification: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error creating notification', 'error' => $e->getMessage()]);

            // Return error response
            return response()->json(['success' => false, 'message' => 'Error creating notification', 'error' => $e->getMessage()]);
        }
    }

    // Controller function for getting psychiatrist student comments
    public function getPsychiatristComments($sendCommentTo, $getAllRec)
    {
        //    dd($sendCommentTo,$getAllRec);
        // Fetch selected columns for the given TO_USER_ID using raw SQL
        $notifications = DB::table('messages_bnu')
            ->select(
                'messages_bnu.MESSAGE',
                'messages_bnu.ENTRY_USER_ID',
                'messages_bnu.TO_USER_ID',
                'messages_bnu.CREATED_AT',
                DB::raw('emp_employees.emp_f_name || \' \' || emp_employees.emp_l_name AS full_name'),
                'emp_employees.emp_ad_username',
                DB::raw('(SELECT departments.dept_name FROM departments WHERE departments.dept_id = emp_employees.emp_dept_id) AS dept_name')
            )
            ->join('emp_employees', 'messages_bnu.ENTRY_USER_ID', '=', 'emp_employees.employee_id')
            ->where('messages_bnu.TO_USER_ID', strtoupper($sendCommentTo))
            ->where('messages_bnu.MESSAGE_TYPE', 'PsychiatristComment');
        if ($getAllRec != 'allRec') {
            $notifications = $notifications->where('messages_bnu.ENTRY_USER_ID', auth()->user()->employee_id);
        }
        // ->where('messages_bnu.ENTRY_USER_ID', auth()->user()->employee_id)
        $notifications = $notifications->orderBy('messages_bnu.CREATED_AT', 'desc')
            ->get();

        return $notifications;
    }

    public function getStudentData(Request $request)
    {
        $studentEnrollment = strtoupper($request->studentEnrollment);
        $getAllRec = strtoupper($request->getAllRec);
        $sql = "

    SELECT sisusers.id, username, email,group_id,
    emp_f_name,emp_l_name , prefix, emp_f_name, emp_contact_no, emp_l_name emp_full_name,emp_email ,imagename emp_photo ,emp_school_id ,emp_sch.school_name AS emp_school_name,emp_dept.dept_name AS emp_dept_name,
    sai_spi_id, emp_employees.*,
    COALESCE(sai_spi_id,0) AS spi_id ,
    spi_enroll_prefix , spi_enroll_id , spi_name, spi_imagename, spi_fathername,

    spi_sess_id,
    sai_sdpo_sess_id   AS SPI_ENROLLMENT_SESSION,
    sai_sdpo_school_id,
    emp_employees.emp_type as sis_emp_type,
    sai_sdpo_dept_id   AS spi_dept_id ,
    sai_sdpo_prog_id   AS spi_prog_id ,
    sai_sdpo_shift_id,
    sai_sdpo_shift_id AS spi_shift_id ,
    sai_semester      AS sai_active_semester,
    sai_enrollment_no AS SPI_ENROLLMENT_NO ,
    sai_student_status, spi_mobile,  spi_email,
    (SELECT
    MAX(sc_co_sess_id)
    FROM
        student_courses) spi_active_sess_id,
    (SELECT max(gpm_active_sessl_id) as gpm_active_sessl_id FROM group_pages_management WHERE gpm_school_id = sai_sdpo_school_id ) AS active_enroll_sess, api_token
  FROM sisusers
  LEFT JOIN emp_employees           ON LOWER(email)      = LOWER(emp_email)
  LEFT JOIN schools emp_sch         ON emp_school_id     = emp_sch.school_id
  LEFT JOIN departments emp_dept    ON emp_dept_id       = emp_dept.dept_id
  LEFT JOIN students_apply_info     ON LOWER(username)   = LOWER(sai_enrollment_no)
  LEFT JOIN students_personal_info  ON sai_spi_id        = spi_id
                                   AND sai_student_status!='Transferred'


     WHERE lower(sisusers.username)=lower('$studentEnrollment')  ";
        $data = DB::select($sql);
        $data = $data[0];
        $getPsychiatristComments = $this->getPsychiatristComments($studentEnrollment, $getAllRec);

        return response()->json(['success' => true, 'StudentData' => $data, 'psychiatristComments' => $getPsychiatristComments]);
    }

    public function sendWarningLettersToMultiple(Request $request)
    {
        $warningOption = $request->warningOption; // Array of warning options
        $multipleWarningLetterData = json_decode($request->multipleWarningLetterData, true); // Array of student IDs
        $originalFileName = '';
        $fileName = '';
        $upload = null;
        $url = '';


        if ($request->hasFile('file')) {
            // Get the original file name
            $originalFileName = $request->file('file')->getClientOriginalName();
            // Get the file extension
            $fileExtension = $request->file('file')->getClientOriginalExtension();
            // Generate a unique filename
            $fileName = uniqid() . '.' . $fileExtension;
            $file = $request->file('file');

            // Upload the file to local storage in public/docs
            $upload = $file->storeAs('docs', $fileName, 'public');

            if (!$upload) {
                return response()->json([
                    'message' => 'File not uploaded!',
                    'status' => false
                ]);
            }

            // Get the URL of the uploaded file
            $url = asset('storage/docs/' . $fileName);
        }

        // Check if $warningOption is set and is an array
        if (isset($warningOption) && is_array($warningOption)) {
            // Loop through each student ID and each warning option
            foreach ($multipleWarningLetterData as $studentID) {
                foreach ($warningOption as $option) {
                    $warning_letter_name = '';

                    // Determine warning letter name based on the option
                    if ($option == '1') {
                        $warning_letter_name = 'Unsatisfactory Academic Performance';
                    }

                    // Prepare the insert data array
                    $insertData = [
                        'swl_enrollment_no' => $studentID,
                        'warning_letter_type' => $option,
                        'warning_letter_name' => $warning_letter_name,
                        'swl_filename' => $originalFileName, // Insert original file name
                        'swl_file_id' => $upload,
                        'swl_filelink' => $url,
                        'entry_user_id' => auth()->user()->employee_id
                    ];

                    // Insert data into the database
                    DB::table('student_warning_letter')->insert($insertData);
                }
            }
        } else {
            // If $warningOption is empty or not an array, insert with null values
            foreach ($multipleWarningLetterData as $studentID) {
                $insertData = [
                    'swl_enrollment_no' => $studentID,
                    'warning_letter_type' => null,
                    'warning_letter_name' => null,
                    'swl_filename' => $originalFileName, // Insert original file name
                    'swl_file_id' => $upload,
                    'swl_filelink' => $url,
                    'entry_user_id' => auth()->user()->employee_id
                ];

                // Insert data into the database
                DB::table('student_warning_letter')->insert($insertData);
            }
        }

        try {
            // Your notification creation code
            return response()->json([
                'success' => true,
                'message' => 'Warning letters issued successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating Warning letters',
                'error' => $e->getMessage()
            ]);
        }
    }

    public function getCurrentEnrolledCourses(Request $request)
    {
        $request->validate([
            'enrollment_no' => 'required|string',
        ]);

        $enrollment_no = $request->input('enrollment_no');
        $sql = " SELECT
            get_course_name(sc_co_id) as course_title,
            sc_co_id as co_id,
            sc_course_id as course_id,
            get_teachername(sc_co_id) as faculty_name
        FROM student_courses
        WHERE lower(sc_sai_enrollment_no) = lower('$enrollment_no')
        AND sc_enrollment_status = 1
        AND sc_co_sess_id = (
            SELECT MAX(sc_co_sess_id)
            FROM student_courses) ";

        $courses = DB::select($sql);
        return response()->json($courses);
    }
    public function deleteWarningLetter(Request $request)
    {
        $id = $request->input('id');
        $enrollmentNo = $request->input('enrollment_no');

        // dd($request->all());
        $record = DB::table('student_warning_letter')
            ->where('id', $id)
            ->whereRaw('LOWER(swl_enrollment_no) = ?', [strtolower($enrollmentNo)])
            ->first(); // Get the record

        if (!$record) {
            return response()->json(['success' => false, 'message' => 'Warning letter not found.']);
        }
        $updated = DB::table('student_warning_letter')
            ->where('id', $id)
            ->whereRaw('LOWER(swl_enrollment_no) = ?', [strtolower($enrollmentNo)])
            ->update(['status' => 'D']); // Mark as deleted

        if ($updated) {
            $updatedRecords = $this->checkWarningLetter($record->swl_enrollment_no);
            return response()->json(['success' => true, 'message' => 'Warning letter marked as deleted successfully.', 'updatedRecords' => $updatedRecords]);
        } else {
            return response()->json(['success' => false, 'message' => 'Warning letter could not be updated.']);
        }
    }

}
