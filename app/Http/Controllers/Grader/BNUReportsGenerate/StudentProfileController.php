<?php

namespace App\Http\Controllers\Grader\BNUReportsGenerate;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use App\Models\Grader\Schools;


class StudentProfileController extends Controller
{
    public const FEE_CHALLAN_TYPES = [
        'Processing_Fee', 
        'Fee', 
        'Full_semester', 
        'Hostel_Fee', 
        'Convocation_Fee', 
        'shortcourse', 
        'Fee_Refund', 
        'misc_fee', 
        'Credit_hours'
    ];

    public function index(Request $request)
    {


        $schools =  Schools::all(); 

        // Return the view with all the retrieved data
        return view('grader.reportgenerator.BNUReportsGenerator.studentDataLink',
    [
        'schools'=>$schools
    ]);
}


function getStudentAaiId($studentEnrollment) {
    $studentRecord = DB::table('admission_apply_info')
        ->select('aai_id')
        ->where(DB::raw('upper("CMS_SAI_ENROLLMENT_NO")'), strtoupper($studentEnrollment))
        ->first();
    if ($studentRecord) {
        return $studentRecord->aai_id;
    } else {
        return null;
    }
}

public function liveSearch(Request $request)
{
    $search = strtolower($request->input('query'));

    if (!$search) {
        return response()->json([]);
    }

    // Filters from request
    $school_id = $request->input('school_id');
    $dept_id = $request->input('dept_id');
    $prog_id = $request->input('prog_id');

    // Base SQL Query
    $sql = "
        SELECT sai.sai_enrollment_no AS enrollment_no, 
               spi.spi_name AS student_name, 
               spi.spi_nic,
               sai.sai_sdpo_school_id,
               sai.sai_sdpo_dept_id,
               sai.sai_sdpo_prog_id
        FROM students_apply_info sai
        LEFT JOIN STUDENTS_PERSONAL_INFO spi ON sai.sai_api_id = spi.api_id
        WHERE (LOWER(sai.sai_enrollment_no) LIKE ? 
            OR LOWER(spi.spi_name) LIKE ? 
            OR LOWER(spi.spi_nic) LIKE ?)";

    // Parameters for binding
    $params = ["%{$search}%", "%{$search}%", "%{$search}%"];

    // Apply Filters Dynamically
    if (!empty($school_id)) {
        $sql .= " AND sai.sai_sdpo_school_id = ?";
        $params[] = $school_id;
    }
    if (!empty($dept_id)) {
        $sql .= " AND sai.sai_sdpo_dept_id = ?";
        $params[] = $dept_id;
    }
    if (!empty($prog_id)) {
        $sql .= " AND sai.sai_sdpo_prog_id = ?";
        $params[] = $prog_id;
    }

    // Replace LIMIT with FETCH FIRST for Oracle
    $sql .= " ORDER BY sai.sai_sdpo_sess_id DESC FETCH FIRST 20 ROWS ONLY";

    // Execute Raw SQL Query
    $results = DB::select($sql, $params);

    return response()->json($results);
}





public function fetchAllStudentWidgetLink(Request $request)
    {
        $enrollmentNo = strtoupper($request->input('enrollment')); // Convert to uppercase for consistency

        // Fetch multiple widgets (extendable)
        $widgets = array_merge(
            $this->getAcademicHistoryWidgets($enrollmentNo),
            $this->getStudentWarningWidgets($enrollmentNo),
            $this->getStudentFeeChallanWidgets($enrollmentNo),
            $this->getStudentFineWidgets($enrollmentNo),
            $this->getEnrolledCoursesWidgets($enrollmentNo),
            $this->getScholarshipWidgets($enrollmentNo),
            $this->getAttendanceWidgets($enrollmentNo),
            $this->getGymWidgets($enrollmentNo),
            $this->getLibraryWidgets($enrollmentNo),
            $this->getCounselingCenterWidgets($enrollmentNo),
            $this->getDisciplineCommitteeWidgets($enrollmentNo),

            // $this->getStudentAbsenceWidgets($enrollmentNo) // Example of adding another widget
        );

        return response()->json($widgets);
    }

    public function getStudentWarningWidgets($enrollmentNo)
    {
        $warningData = DB::select("
            SELECT
                COUNT(*) AS total_count,
                TO_CHAR(MAX(entry_timestamp), 'DD Month YYYY') AS last_issued
            FROM student_warning_letter
            WHERE swl_enrollment_no = ?
        ", [$enrollmentNo]);

        // Handle null case
        $warning = $warningData[0] ?? (object) ['total_count' => 0, 'last_issued' => 'N/A'];

        return [
            [
                'id' => 'warningLetters',
                'icon' => 'mdi mdi-alert-circle',
                'color' => 'text-danger',
                'title' => 'Warning Letters',
                'value' => "{$warning->total_count} Total",
                'details' => "Last Issued Date: <b>{$warning->last_issued}</b>",
            ],
        ];
    }

    public function getStudentFeeChallanWidgets($enrollmentNo)
    {

        $aai_id = $this->getStudentAaiId($enrollmentNo);
        
        $feeChallanData = DB::select("
        SELECT
            COUNT(*) AS total_challans,
            SUM(CASE WHEN sc_challan_status = 'PAID' THEN 1 ELSE 0 END) AS total_paid,
            SUM(CASE WHEN sc_challan_status = 'UNPAID' THEN 1 ELSE 0 END) AS total_unpaid
        FROM student_challan
        WHERE sc_app_id = ?
        AND sc_challan_type IN ('" . implode("','", self::FEE_CHALLAN_TYPES) . "')
        AND invoice_status='E'
    ", [$aai_id]);


        // Handle null case
        $feeChallan = $feeChallanData[0] ?? (object) ['total_challans' => 0, 'total_paid' => 0, 'total_unpaid' => 0];

        return [
            [
                'id' => 'feeChallans',
                'icon' => 'fa fa-gavel',
                'color' => 'text-primary',
                'title' => 'Fee Challans',
                'value' => "{$feeChallan->total_challans} Total",
                'details' => "Paid: <b>{$feeChallan->total_paid}</b>, Unpaid: <b>{$feeChallan->total_unpaid}</b>",
            ],
        ];
    }



    public function getStudentFineWidgets($enrollmentNo)
{
    $feeFineData = DB::select("
        SELECT
            COUNT(*) AS total_fines,
            SUM(CASE WHEN sc_challan_status = 'PAID' THEN 1 ELSE 0 END) AS total_fines_paid,
            SUM(CASE WHEN sc_challan_status = 'UNPAID' THEN 1 ELSE 0 END) AS total_fines_unpaid
        FROM student_challan
        WHERE sc_enrollment_no = ?
        AND sc_challan_type = 'Fine'
    ", [$enrollmentNo]);

    $fine = $feeFineData[0] ?? (object) ['total_fines' => 0, 'total_fines_paid' => 0, 'total_fines_unpaid' => 0];

    return [
        [
            'id' => 'fines',
            'icon' => 'fa fa-balance-scale',
            'color' => 'text-danger',
            'title' => 'Fines',
            'value' => "{$fine->total_fines} Total",
            'details' => "Paid: <b>" . ($fine->total_fines_paid ?? 0) . "</b>, Unpaid: <b>" . ($fine->total_fines_unpaid ?? 0) . "</b>",
        ],
    ];
}



public function getEnrolledCoursesWidgets($enrollmentNo)
{
    // Fetch enrolled courses with only required columns
    $courses = DB::select("
    SELECT 
        co.co_sess_id, 
        asess.sess_name,
        COUNT(sc.sc_co_id) AS total_enrolled_students
    FROM courses_offered co
    LEFT JOIN student_courses sc ON co.co_id = sc.sc_co_id
    LEFT JOIN academic_sessions asess ON co.co_sess_id = asess.sess_id
    WHERE sc.sc_sai_enrollment_no = :enrollment_no
    and sc.SC_ENROLLMENT_STATUS=1
    AND co.co_sess_id = (
        SELECT MAX(co_sess_id)
        FROM courses_offered
        WHERE co_id IN (SELECT sc_co_id FROM student_courses WHERE sc_sai_enrollment_no = :enrollment_no)
    )
    GROUP BY co.co_sess_id, asess.sess_name
    ORDER BY asess.sess_name DESC
", ['enrollment_no' => $enrollmentNo]);

    // Extract values safely
    $totalCourses = $courses[0]->total_enrolled_students ?? 0;
    $latestSemester = $courses[0]->sess_name ?? 'N/A';

    return [
        [
            'id' => 'courseEnrollment',
            'icon' => 'mdi mdi-book-open-page-variant',
            'color' => 'text-info',
            'title' => 'Course Enrollment',
            'value' => $totalCourses, // Dynamic count of enrolled courses
            'details' => 'Latest Semester: <b>' . $latestSemester . '</b>',
        ],
    ];
}


public function getAcademicHistoryWidgets($enrollmentNo)
{
    // Fetch CGPA and latest session name
    $academicData = DB::select("
        SELECT 
            (SELECT cgpa(:enrollment_no) FROM dual) AS cgpa, -- Fetch CGPA
            (SELECT sess_name 
             FROM academic_sessions 
             WHERE sess_id = (
                 SELECT MAX(sc_co_sess_id) 
                 FROM student_courses 
                 WHERE sc_sai_enrollment_no = :enrollment_no
             )
            ) AS session_name -- Fetch Latest Session Name
        FROM dual
    ", ['enrollment_no' => $enrollmentNo]);

    // Ensure data is available
    $academic = $academicData[0] ?? (object) ['cgpa' => 'N/A', 'session_name' => 'N/A'];

    return [
        [
            'id' => 'academicHistory',
            'icon' => 'mdi mdi-school',
            'color' => 'text-primary',
            'title' => 'Academic History',
            'value' => "CGPA: <b>{$academic->cgpa}</b>",
            'details' => "Latest Semester: <b>{$academic->session_name}</b>",
        ],
    ];
}


public function getScholarshipWidgets($enrollmentNo)
{
    // Fetch scholarship existence and types using LISTAGG for Oracle
    $scholarshipData = DB::select("
        SELECT 
            CASE 
                WHEN COUNT(*) > 0 THEN 'Yes' 
                ELSE 'No' 
            END AS has_scholarship,
            COALESCE(LISTAGG(SCHOLARSHIP_TYPE.SCHOLARSHIP_NAME, ', ') WITHIN GROUP (ORDER BY SCHOLARSHIP_TYPE.SCHOLARSHIP_NAME), 'N/A') AS types
        FROM FIN_SCHOLARSHIP_DISCOUNTS
        left join SCHOLARSHIP_TYPE on FSD_SCHOLARSHIP_TYPE=scholarship_id
        WHERE FSD_ENROLLMENT_NO = :enrollmentNo
    ", ['enrollmentNo' => $enrollmentNo]);

    // Extract results
    $hasScholarship = $scholarshipData[0]->has_scholarship ?? 'No';
    $scholarshipTypes = $scholarshipData[0]->types ?? 'N/A';
    // $scholarshipTypes =  'N/A';

    // Return scholarship widget
    return [
        [
            'id' => 'scholarship',
            'icon' => 'mdi mdi-cash-multiple',
            'color' => 'text-success',
            'title' => 'Scholarship',
            'value' => $hasScholarship,
            // 'details' => $hasScholarship === 'Yes' ? "Type: <b>{$scholarshipTypes}</b>" : '',
            'details' => "Type: <b>" . (strlen($scholarshipTypes) > 20 ? substr($scholarshipTypes, 0, strpos($scholarshipTypes, ' ', 20) ?: 20) . "..." : $scholarshipTypes) . "</b>"
        ],
    ];
}


public function getAttendanceWidgets($enrollmentNo)
{
    
    $sql = "
        SELECT
            student_courses.sc_sai_enrollment_no,
            academic_sessions.sess_name,
            academic_sessions.sess_id,
            student_courses.attendance_percentage,
            get_course_name(sc_co_id) AS course_title,
            get_teachername(sc_co_id) AS teachername,
            student_courses.sc_co_id
        FROM student_courses
        LEFT JOIN academic_sessions ON student_courses.sc_co_sess_id = academic_sessions.sess_id
        LEFT JOIN courses_two ON sc_course_id = course_id
        WHERE 1=1
        AND sc_sai_enrollment_no = :sai_enrollment_no
        and student_courses.SC_ENROLLMENT_STATUS=1
        AND attendance_percentage IS NOT NULL
        ORDER BY sess_id ASC
    ";

    $attendanceRecords = DB::select($sql, ['sai_enrollment_no' => $enrollmentNo]);

    // Initialize variables
    $totalAttendance = 0;
    $totalSubjects = count($attendanceRecords);
    $subjectsBelow75 = 0;

    // Process attendance data
    foreach ($attendanceRecords as $record) {
        $totalAttendance += $record->attendance_percentage;
        if ($record->attendance_percentage < 75) {
            $subjectsBelow75++;
        }
    }

    // Calculate Overall Attendance
    $overallAttendance = $totalSubjects > 0 ? round($totalAttendance / $totalSubjects, 2) : 0;

    // Return widget data
    return [[
        'id' => 'attendance',
        'icon' => 'mdi mdi-calendar-check',
        'color' => 'text-warning',
        'title' => 'Attendance',
        'value' => "Overall: <span>{$overallAttendance}%</span>",
        'details' => "Courses Below 75%: <b>{$subjectsBelow75}</b>",
    ]];
}


public function getGymWidgets($enrollmentNo)
{
    $sql = "
        SELECT MAX(s.EXPIRATION_DATE) AS latest_expiry_date
        FROM subscriptions s
        LEFT JOIN SUBSCRIPTION_PLANS sp ON s.sp_id = sp.sp_id
        WHERE s.USER_ID = :enrollment_no
        AND sp.SP_CATEGORY = 'Gym'
    ";

    $GymRecords = DB::select($sql, ['enrollment_no' => $enrollmentNo]);

    // Extract expiration date if available
    $latestExpiryDate = $GymRecords[0]->latest_expiry_date ?? null;

    // Determine widget values
    $status = $latestExpiryDate ? "Yes" : "No";
    $expiryDate = $latestExpiryDate ? date("d M Y", strtotime($latestExpiryDate)) : "N/A";

    // Return widget data
    return [[
        'id' => 'gymSubscription',
        'icon' => 'mdi mdi-dumbbell',
        'color' => 'text-primary',
        'title' => 'Gym Subscription',
        'value' => $status,
        'details' => "Expires: <b>{$expiryDate}</b>",
    ]];
}




public function getLibraryWidgets($enrollmentNo)
{
    // Raw SQL query to get the count of issued books and overdue books for Oracle
    $sql = "
        SELECT 
            COUNT(CASE WHEN LC_BOOK_STATUS IN ('Issued', 'Received') THEN 1 END) AS issued_count,
            COUNT(CASE 
                    WHEN LC_BOOK_STATUS = 'Issued' AND 
                         (LC_ISSUE_DATE + LC_ISSUE_NO_OF_DAYS) < SYSDATE 
                    THEN 1 END) AS overdue_count
        FROM 
            library_circulation
        WHERE 
            upper(sai_enrollment_no) = ?
    ";

    // Execute the query and fetch the results
    $result = DB::select($sql, [$enrollmentNo]);

    // Extract the counts from the result
    $issuedCount = $result[0]->issued_count ?? 0;
    $overdueCount = $result[0]->overdue_count ?? 0;

    // Return widget data
    return [[
        'id' => 'libraryBooks',
        'icon' => 'mdi mdi-library',
        'color' => 'text-info',
        'title' => 'Library Books',
        'value' => 'Issued: <span>' . $issuedCount . '</span>',
        'details' => 'Overdue: <b>' . $overdueCount . '</b>',
    ]];
}



public function getCounselingCenterWidgets($enrollmentNo)
{
    // Example data, replace with actual data from the database or logic as needed
    $issuedCount = 5; // Example value for issued items
    $overdueCount = 2; // Example value for overdue items
    
    // Return widget data
    return [
      
        [
            'id' => 'counselings',
            'icon' => 'mdi mdi-calendar-check',
            'color' => 'text-success',
            'title' => 'Counseling Appointments',
            'value' => 'Under Development', 
            'details' => '',
        ],
        
        // [
        //     'id' => 'counselings',
        //     'icon' => 'mdi mdi-calendar-check',
        //     'color' => 'text-success',
        //     'title' => 'Counseling Appointments',
        //     'value' => 'Upcoming: <span>3</span>',
        //     'details' => 'Completed: <b>7</b>',
        // ],
       
    ];
}

public function getDisciplineCommitteeWidgets($enrollmentNo)
{
  
    // Return widget data
    return [
      
        [
            'id' => 'disciplineCommittee',
            'icon' => 'mdi mdi-gavel', 
            'color' => 'text-warning',
            'title' => 'Discipline Committee',
            'value' => 'Under Development',
            'details' => '',
        ],
        
        
        // [
        //     'id' => 'counselings',
        //     'icon' => 'mdi mdi-calendar-check',
        //     'color' => 'text-success',
        //     'title' => 'Counseling Appointments',
        //     'value' => 'Upcoming: <span>3</span>',
        //     'details' => 'Completed: <b>7</b>',
        // ],
       
    ];
}









public function dummyWidgets(){
    $widgets = [
        [
            'id' => 'warningLetters',
            'icon' => 'mdi mdi-alert-circle',
            'color' => 'text-danger',
            'title' => 'Warning Letters',
            'value' => '5 Total',
            'details' => 'Last Issued Date: <b>15 July 2024</b>',
        ],
        [
            'id' => 'feeChallans',
            'icon' => 'fa fa-gavel',
            'color' => 'text-primary',
            'title' => 'Fee Challans',
            'value' => '10 Total',
            'details' => 'Paid: <b>7</b>, Unpaid: <b>3</b>',
        ],
        [
            'id' => 'academicHistory',
            'icon' => 'fa fa-graduation-cap',
            'color' => 'text-success',
            'title' => 'Academic History',
            'value' => 'CGPA: <span>3.8</span>',
            'details' => 'Last Semester: <b>Spring 2025</b>',
        ],
        [
            'id' => 'attendance',
            'icon' => 'mdi mdi-calendar-check',
            'color' => 'text-warning',
            'title' => 'Attendance',
            'value' => 'Overall: <span>95%</span>',
            'details' => 'Subjects Below 75%: <b>1</b>',
        ],
        [
            'id' => 'fines',
            'icon' => 'fa fa-balance-scale',
            'color' => 'text-danger',
            'title' => 'Fines',
            'value' => '3 Total',
            'details' => 'Paid: <b>1</b>, Unpaid: <b>2</b>',
        ],
        [
            'id' => 'courseEnrollment',
            'icon' => 'mdi mdi-book-open-page-variant',
            'color' => 'text-info',
            'title' => 'Course Enrollment',
            'value' => '5',
            'details' => 'Semester: <b>Fall 2025</b>',
        ],
        [
            'id' => 'scholarship',
            'icon' => 'mdi mdi-cash-multiple',
            'color' => 'text-success',
            'title' => 'Scholarship',
            'value' => '50%',
            'details' => 'Type: <b>Merit-Based, Umaisa,....</b>',

        ],
        [
            'id' => 'libraryBooks',
            'icon' => 'mdi mdi-library',
            'color' => 'text-info',
            'title' => 'Library Books',
            'value' => 'Issued: <span>4</span>',
            'details' => 'Overdue: <b>1</b>',
        ],
        [
            'id' => 'hostel',
            'icon' => 'mdi mdi-home-outline',
            'color' => 'text-warning',
            'title' => 'Hostel',
            'value' => 'Room: <span>101</span>',
            'details' => 'Building: <b>Boys/Girls</b>',
        ],
        [
            'id' => 'performance',
            'icon' => 'mdi mdi-chart-line',
            'color' => 'text-primary',
            'title' => 'Performance',
            'value' => 'CGPA: <span>3.8</span>',
            'details' => 'Class Average: <b>3.5</b>',
        ],
        [
            'id' => 'gymSubscription',
            'icon' => 'mdi mdi-dumbbell',
            'color' => 'text-primary',
            'title' => 'Gym Subscription',
            'value' => 'Active',
            'details' => 'Expires: <b>31 Dec 2025</b>',
        ],
        // [
        //     'id' => 'peerMentorship',
        //     'icon' => 'mdi mdi-account-multiple-outline',
        //     'color' => 'text-success',
        //     'title' => 'Peer Mentorship',
        //     'value' => '<span>Mentor</span>',
        //     'details' => 'Assigned Mentees: <b>3</b>',
        // ],
        [
            'id' => 'studyMaterials',
            'icon' => 'fa fa-file-o',
            'color' => 'text-primary',
            'title' => 'Study Materials',
            'value' => '12 Uploaded',
            'details' => 'Pending: <b>2</b>, New Files: <b>3</b>',
        ],
        // [
        //     'id' => 'healthWellness',
        //     'icon' => 'mdi mdi-heart-pulse',
        //     'color' => 'text-danger',
        //     'title' => 'Health & Wellness',
        //     'value' => '5 Workouts',
        //     'details' => 'Last Checkup: <b>15 July 2025</b>',
        // ],
        // [
        //     'id' => 'dailyTimetable',
        //     'icon' => 'mdi mdi-timetable',
        //     'color' => 'text-warning',
        //     'title' => 'Daily Timetable',
        //     'value' => '3 Classes',
        //     'details' => 'First Class: <b>9:00 AM</b>',
        // ],

    ];

    return $widgets;
}




}
