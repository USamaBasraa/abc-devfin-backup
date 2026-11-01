<?php


namespace App\Http\Controllers\Grader;
use App\Http\Controllers\Controller;
use DB;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\sessions;
use Yajra\DataTables\Facades\DataTables;


class EnrollmentStartStopController extends Controller
{
    public function index()
    { 
     
        return view('enrollment.enrollment_start_stop');

    }

    public function verifyEnrollmentOffering(){
          $authUser = auth()->user()->employee_id; 
         if (!in_array($authUser, [512, 3455])) {
            abort(403, 'Unauthorized User.');
        }
       $request = Request::capture(); // or: request()
        $hodReport = new HodReportController();
        $activeSchools = $hodReport->activeschools($request);
        $activeDeparments = $hodReport->activedepartments($request);
        $activePrograms = $hodReport->programs($request);
        $sessions = $hodReport->sessions();
        $masterCourses = $hodReport->masterCourses();
        $courseType = $hodReport->courseType();
       return view('enrollment.verify_offering', compact('activeSchools','activeDeparments','activePrograms','sessions','masterCourses','courseType'));
    }

    public function waitListEnrollmentOffering(){
 
        $request = Request::capture(); // or: request()
        $hodReport = new HodReportController();
        $activeSchools = $hodReport->schoolsloginUserOnly($request);
        $activeDeparments = $hodReport->activedepartments($request);
        $activePrograms = $hodReport->programs($request);
        $sessions = $hodReport->roadmapsessions();
        $masterCourses = $hodReport->masterCourses();
        $courseType = $hodReport->courseType();
        $activeStudents = "select sai_enrollment_no from students_apply_info where sai_student_status in ('Active','Expected-Graduate')";
        $resactiveStudents = DB::select($activeStudents);
        return view('enrollment.enrollment_wait_list_offering', compact('activeSchools','activeDeparments','activePrograms','sessions','masterCourses','courseType','resactiveStudents'));
    }

    public function insertCoursesOffered(Request $request)
    {
        // dd($request->all());
        $activeSessionId = 168;//$request->input('active_session_id');
        $activeSchoolId = $request->input('schoolID');
        $departmentId = $request->input('departmentId');
        // dd($activeSessionId,$activeSchoolId);
        try {
            // Execute the Oracle stored procedure
            DB::statement('BEGIN PR_INSERT_COURSES_OFFERED(:sess_id,:school_id,:department_id); END;', [
                'sess_id'   => $activeSessionId,
                'school_id' => $activeSchoolId,
                'department_id' => $departmentId
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Courses moved to Course Offering Page successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }


    public function getSessionData(Request $request){
      
        $sessId = $request->get('session_id');
        $enrollNo = $request->get('enrollNo');

        

        $getStudentProgram = "select * from students_apply_info where sai_enrollment_no = '$enrollNo'";
        // echo   $getStudentProgram;exit;
        $resgetStudentProgram = DB::select($getStudentProgram);

        $studentProgId = $resgetStudentProgram[0]->sai_sdpo_prog_id;
        $studentSchoolId = $resgetStudentProgram[0]->sai_sdpo_school_id;
        $studentDeptId = $resgetStudentProgram[0]->sai_sdpo_dept_id;
        $studentSessId = $resgetStudentProgram[0]->sai_sdpo_sess_id;

        // dd($studentProgId,$studentSchoolId);

        $checkStudentinRoadmapWiseStudent = "select count(*) as count from roadmap_wise_offering_stdwise 
                                            where rwf_sai_enrollment_no ='$enrollNo'";
        $rescheckStudentinRoadmapWiseStudent = DB::select($checkStudentinRoadmapWiseStudent);

        if($rescheckStudentinRoadmapWiseStudent[0]->count > 0){
            $getOfferedRoadmapCourses = "SELECT
                                        course_cd_name,sess_name,active_session_id as sess_id,roadmap_wise_offering_stdwise.course_id,rwf_sai_enrollment_no,id,
                                        (
                                            SELECT LISTAGG(gh_grade, ', ') WITHIN GROUP (ORDER BY gh_grade)
                                            FROM grade_history
                                            WHERE gh_co_course_id = roadmap_wise_offering_stdwise.course_id
                                            AND gh_sai_enrollment_no = '$enrollNo'
                                        ) AS grades
                                    FROM
                                        roadmap_wise_offering_stdwise
                                        left join courses_two on courses_two.course_id = roadmap_wise_offering_stdwise.course_id
                                        left join roadmap_sessions on sess_id = ACTIVE_SESSION_ID
                                    WHERE
                                            school_id = $studentSchoolId
                                        AND rdmap_session_id >= $studentSessId
                                        and program_id = $studentProgId
                                        and   RWF_SAI_ENROLLMENT_NO = '$enrollNo'
                                        order by sess_id";

        }else{
            // $getOfferedRoadmapCourses = "SELECT
            //                             course_cd_name,sess_name,active_session_id as sess_id,roadmap_wise_offering.course_id,
            //                             (
            //                                 SELECT LISTAGG(gh_grade, ', ') WITHIN GROUP (ORDER BY gh_grade)
            //                                 FROM grade_history
            //                                 WHERE gh_co_course_id = roadmap_wise_offering.course_id
            //                                 AND gh_sai_enrollment_no = '$enrollNo'
            //                             ) AS grades
            //                         FROM
            //                             roadmap_wise_offering
            //                             left join courses_two on courses_two.course_id = roadmap_wise_offering.course_id
            //                             left join roadmap_sessions on sess_id = ACTIVE_SESSION_ID
            //                         WHERE
            //                                 school_id = $studentSchoolId
            //                             AND rdmap_session_id >= $studentSessId
            //                             and program_id = $studentProgId
            //                             order by sess_id";

             $getOfferedRoadmapCourses = "SELECT
                                        course_cd_name,sess_name,active_session_id as sess_id,roadmap_wise_offering.course_id,
                                        (
                                            SELECT LISTAGG(gh_grade, ', ') WITHIN GROUP (ORDER BY gh_grade)
                                            FROM grade_history
                                            WHERE gh_co_course_id = roadmap_wise_offering.course_id
                                            AND gh_sai_enrollment_no = '$enrollNo'
                                          

                                        ) AS grades
                                    FROM
                                        roadmap_wise_offering
                                        left join courses_two on courses_two.course_id = roadmap_wise_offering.course_id
                                        left join roadmap_sessions on sess_id = ACTIVE_SESSION_ID
                                    WHERE
                                            school_id = $studentSchoolId
                                        AND (
                                                ( $studentSessId >= 165 AND rdmap_session_id >= $studentSessId )
                                            OR ( $studentSessId < 165  AND rdmap_session_id =  $studentSessId )
                                            )
                                        and program_id = $studentProgId
                                        order by sess_id";

        }
        
        // echo $getOfferedRoadmapCourses;exit;
        $resgetOfferedRoadmapCourses = DB::select($getOfferedRoadmapCourses);

        $studentCurrentEnrollment = "select get_course_name(sc_co_id) as course_name,sc_pc_course_cr from student_courses where sc_sai_enrollment_no = '$enrollNo'
        and sc_co_sess_id in (select max(sc_co_sess_id) from student_courses where sc_sai_enrollment_no = '$enrollNo')";
        $resstudentCurrentEnrollment = DB::select($studentCurrentEnrollment);

        return response()->json([
            'studentSessId' => $studentSessId,
            'studentProgId' => $studentProgId,
            'studentSchoolId' => $studentSchoolId,
            'studentDeptId' => $studentDeptId,
            'enrollNo' => $enrollNo,
            'data' => $resgetOfferedRoadmapCourses,
            'studentEnrollment' => $resstudentCurrentEnrollment
        ]);

        // dd($studentProgId);
    }  

     public function getSessionDataProgramWise(Request $request){
      
         $sessId = $request->get('session_id');
          $schoolId = $request->get('schoolId');
        $studentProgId = $request->get('progId');      
        $enrollNo = '';  
         $studentSchoolId = '';
         $studentSessId='';
        

        $getStudentProgram = "select * from students_apply_info where sai_enrollment_no = '$enrollNo'";
       
            $getOfferedRoadmapCourses = "SELECT
                                        course_cd_name,sess_name,active_session_id as sess_id,roadmap_wise_offering.course_id
                                    FROM
                                        roadmap_wise_offering
                                        left join courses_two on courses_two.course_id = roadmap_wise_offering.course_id
                                        left join roadmap_sessions on sess_id = ACTIVE_SESSION_ID
                                    WHERE
                                            school_id = $schoolId
                                        --  and active_session_id > 165
                                         and rdmap_session_id =  $sessId
                                        and program_id = $studentProgId
                                        order by sess_id,course_cd_name";

     

        
                                    //    echo $getOfferedRoadmapCourses;exit;
        $resgetOfferedRoadmapCourses = DB::select($getOfferedRoadmapCourses);

       

        // dd($resgetOfferedRoadmapCourses);

        return response()->json([
            // 'studentSessId' => $studentSessId,
            'studentProgId' => $studentProgId,
           
            // 'enrollNo' => $enrollNo,
            'data' => $resgetOfferedRoadmapCourses
        ]);

        // dd($studentProgId);
    }  

    public function getCourses(){
        $courses = DB::table('courses_two')
            ->select(
                'course_id',
                DB::raw("course_code || ' - ' || course_cd_name AS course_cd_name")
            )
            ->get();

        return response()->json($courses);
    }

    public function submitCourses(Request $request){
        $courses = $request->input('courses', []);
        //dd($request->all());
        if (empty($courses)) {
            return response()->json(['error' => 'No courses selected']);
        }

        $insertData = [];

        foreach ($courses as $course) {
            // Check if this course already exists for this student/session
            $exists = DB::table('roadmap_wise_offering_stdwise')
                ->where('ACTIVE_SESSION_ID', $course['sess_id'])
                ->where('RDMAP_SESSION_ID', $course['student_sess'])
                ->where('SCHOOL_ID', $course['student_school'])
                ->where('DEPARTMENT_ID', $course['student_dept'])
                ->where('PROGRAM_ID', $course['student_prog'])
                ->where('COURSE_ID', $course['course_id'])
                ->where('RWF_SAI_ENROLLMENT_NO', $request->get('enroll_no'))
                ->exists();

            if (!$exists) {
                $insertData[] = [
                    'ACTIVE_SESSION_ID'      => $course['sess_id'],
                    'RDMAP_SESSION_ID'       => $course['student_sess'],
                    'SCHOOL_ID'              => $course['student_school'],
                    'DEPARTMENT_ID'          => $course['student_dept'],
                    'PROGRAM_ID'             => $course['student_prog'],
                    'SEMESTER_ID'            => $course['semester'],
                    'COURSE_ID'              => $course['course_id'],
                    'STATUS'                 => 'E',
                    'ENTRY_USER_ID'          => auth()->user()->employee_id,
                    'CREATED_AT'             => now(),
                    'UPDATED_AT'             => now(),
                    'COURSE_TYPE'            => 1,
                    'RWF_SAI_ENROLLMENT_NO'  => $request->get('enroll_no'),
                ];
            }
        }

        if (empty($insertData)) {
            return response()->json(['error' => 'Selected courses are already added']);
        }

        // Insert only new rows
        DB::table('roadmap_wise_offering_stdwise')->insert($insertData);

        return response()->json(['success' => true, 'message' => 'Courses inserted successfully']);
    }

    public function saveCourse(Request $request){
        // Validate inputs (optional but recommended)
        $request->validate([
            'student_roll_no'       => 'required|string',
            'active_session'        => 'required',
            
            'course_id'             => 'required',
            'semester'              => 'required',
            'course_offering_type'  => 'required',
        ]);
        $enrollNo  = $request->input('student_roll_no');
        $getStudentProgram = "select * from students_apply_info where sai_enrollment_no = '$enrollNo'";
        // echo   $getStudentProgram;exit;
        $resgetStudentProgram = DB::select($getStudentProgram);

        $studentProgId = $resgetStudentProgram[0]->sai_sdpo_prog_id;
        $studentSchoolId = $resgetStudentProgram[0]->sai_sdpo_school_id;
        $studentDeptId = $resgetStudentProgram[0]->sai_sdpo_dept_id;
        $studentSessId = $resgetStudentProgram[0]->sai_sdpo_sess_id;

        // Insert a single record
        DB::table('roadmap_wise_offering_stdwise')->insert([
            'ACTIVE_SESSION_ID'      => $request->input('active_session'),
            'RDMAP_SESSION_ID'       => $studentSessId,
            'SCHOOL_ID'              => $studentSchoolId, // optional
            'DEPARTMENT_ID'          => $studentDeptId,   // optional
            'PROGRAM_ID'             => $studentProgId,   // optional
            'SEMESTER_ID'            => $request->input('semester'),
            'COURSE_ID'              => $request->input('course_id'),
            'STATUS'                 => 'E',
            'ENTRY_USER_ID'          => auth()->user()->employee_id,
            'CREATED_AT'             => now(),
            'UPDATED_AT'             => now(),
            'COURSE_TYPE'            => $request->input('courseType_new'),
            'THEORY_CREDIT_HOURS'    => $request->input('theoryHours_new'),
            'COURSE_OFFERING_TYPE'   => $request->input('course_offering_type'),
            'RWF_SAI_ENROLLMENT_NO'  => $request->input('student_roll_no'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Course saved successfully'
        ]);
    }

    public function getNextSession(Request $request) {
        $studentSess = $request->get('student_sess');

        // Get the last session in roadmap_sessions
        $lastSession = DB::table('roadmap_sessions')
            ->orderBy('sess_id', 'desc')
            ->first();

        $nextSessId = $lastSession ? $lastSession->sess_id + 1 : 1;
        $nextSessName = "Session " . $nextSessId;

        return response()->json([
            'next_sess_id' => $nextSessId,
            'next_sess_name' => $nextSessName
        ]);
    }

    public function deleteMultiple(Request $request){
        $rows = $request->rows;

        if (empty($rows)) {
            return response()->json([
                'status' => 'error',
                'message' => 'No rows selected'
            ]);
        }

        foreach ($rows as $row) {

            DB::table('roadmap_wise_offering_stdwise')
                ->where('id', $row['id'])   // delete using id from array
                ->where('rwf_sai_enrollment_no', $row['enroll_no']) // double check enrollment
                ->delete();
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Selected courses deleted successfully'
        ]);
    }

    // public function waitListEnrollmentOffering2(){
 
    //     $request = Request::capture(); // or: request()
    //     $hodReport = new HodReportController();
    //     $activeSchools = $hodReport->schoolsloginUserOnly($request);
    //     $activeDeparments = $hodReport->activedepartments($request);
    //     $activePrograms = $hodReport->programs($request);
    //     $sessions = $hodReport->sessions();
    //     $masterCourses = $hodReport->masterCourses();
    //     $courseType = $hodReport->courseType();
    //     return view('enrollment.enrollment_wait_list_offering2', compact('activeSchools','activeDeparments','activePrograms','sessions','masterCourses','courseType'));
    // }
    public function waitListEnrollmentOffering2(){
 
        $request = Request::capture(); // or: request()
        $hodReport = new HodReportController();
        $activeSchools = $hodReport->schoolsloginUserOnly($request);
        $activeDeparments = $hodReport->activedepartments($request);
        $activePrograms = $hodReport->programs($request);
        $sessions = $hodReport->roadmapsessions();
        $masterCourses = $hodReport->masterCourses();
        $courseType = $hodReport->courseType();
        $activeStudents = "select sai_enrollment_no from students_apply_info where sai_student_status in ('Active','Expected-Graduate')";
        $resactiveStudents = DB::select($activeStudents);
        return view('enrollment.enrollment_wait_list_offering2', compact('activeSchools','activeDeparments','activePrograms','sessions','masterCourses','courseType','resactiveStudents'));
    }
    public function getRoadmapStudents(Request $request){
     
      $sql = "select count(*) as count from students_apply_info where sai_sdpo_sess_id = $request->roadmap_session and sai_sdpo_prog_id = $request->program_id
      and sai_student_status in ('Active','Expected-Graduate')";
      //  echo $sql;exit;
      $ressql = DB::select($sql);
      return response()->json($ressql[0]);
    }
    public function getroadMapOfferingData(){
        
       $data = DB::table('roadmap_wise_offering')
        ->leftJoin('roadmap_sessions as active_session', 'roadmap_wise_offering.active_session_id', '=', 'active_session.sess_id')
        ->leftJoin('roadmap_sessions as roadmap_session', 'roadmap_wise_offering.rdmap_session_id', '=', 'roadmap_session.sess_id')
        ->leftJoin('schools', 'schools.school_id', '=', 'roadmap_wise_offering.school_id')
        ->leftJoin('departments', 'departments.dept_id', '=', 'roadmap_wise_offering.department_id')
        ->leftJoin('programs', 'programs.prog_id', '=', 'roadmap_wise_offering.program_id')
        ->leftJoin('courses_two', 'courses_two.course_id', '=', 'roadmap_wise_offering.course_id')
        ->leftJoin('course_types', 'course_types.ct_id', '=', 'roadmap_wise_offering.course_type')
        ->leftJoin('course_section_count', 'course_section_count.rwf_id', '=', 'roadmap_wise_offering.id')
        ->select(
        'roadmap_wise_offering.id',
        'active_session.sess_name as active_session_name',
        'roadmap_session.sess_name as roadmap_session_name',
        'schools.school_code',
        'departments.dept_name',
        'programs.prog_name',
        'roadmap_wise_offering.semester_id',
        'roadmap_wise_offering.shift_id',
        // 'courses_two.course_cd_name',
        DB::raw("courses_two.course_code || ' ' || courses_two.course_cd_name as course_cd_name"),
        'course_types.ct_name',
        'roadmap_wise_offering.theory_credit_hours',
        'roadmap_wise_offering.course_id',
        'roadmap_wise_offering.practical_credit_hours',
        'roadmap_wise_offering.course_offering_type',
        'roadmap_wise_offering.course_capacity',
        'roadmap_wise_offering.co_total_classes',
        'roadmap_wise_offering.co_req_classes',
        'roadmap_wise_offering.co_req_present',
        'roadmap_wise_offering.co_custom',

        // 👇 Combine multiple sections into one string like "A(32), B(27)"
        DB::raw("LISTAGG(course_section_count.course_section || '(' || course_section_count.course_section_count || ')', ', ') 
                 WITHIN GROUP (ORDER BY course_section_count.course_section) as sections_summary")
        )
        ->when(!in_array(auth()->user()->employee_id, [3455, 512,735]), function ($query) {
            if (in_array(auth()->user()->employee_id, [9189, 9258, 9328,371,350,2955,2935,6254,6975,9151,9153,9619,9641,9398,9487,9620])) {
                $query->whereIn('roadmap_wise_offering.school_id', [12, 21]);
            } else {
                $query->where('roadmap_wise_offering.school_id', auth()->user()->school_id);
            }
        })
        ->groupBy(
            'roadmap_wise_offering.id',
            'active_session.sess_name',
            'roadmap_session.sess_name',
            'schools.school_code',
            'roadmap_wise_offering.department_id',
            'departments.dept_name',
            'programs.prog_dept_id',
            'programs.prog_name',
            'roadmap_wise_offering.shift_id',
            'roadmap_wise_offering.semester_id',
            'courses_two.course_code',
            'courses_two.course_cd_name',
            'course_types.ct_name',
            'roadmap_wise_offering.theory_credit_hours',
            'roadmap_wise_offering.course_id',
            'roadmap_wise_offering.practical_credit_hours',
            'roadmap_wise_offering.course_offering_type',
            'roadmap_wise_offering.course_capacity',
            'roadmap_wise_offering.co_total_classes',
            'roadmap_wise_offering.co_req_classes',
            'roadmap_wise_offering.co_req_present',
            'roadmap_wise_offering.co_custom'
        )
        ->orderBy('roadmap_wise_offering.id', 'desc')
        ->get();

        return DataTables::of($data)->make(true);

        // return response()->json($data);
    }

      public function verifyroadMapOfferingData(Request $request)
{
    // Get filters from request
    $session_id         = $request->get('session_id');
    $roadmap_session_id = $request->get('roadmap_session_id');
    $school_id          = $request->get('school_id');
    $dept_id            = $request->get('dept_id');
    $prog_id            = $request->get('prog_id');

    // Build query with filters
    $query = "
        SELECT
            ac1.sess_name AS active_session,
            ac2.sess_name AS roadmap_session,
            s.school_code,
            d.dept_name,
            p.prog_name,
            rwo.course_id,
            c.course_code || ' ' || c.course_cd_name AS course_name,
            CASE
                WHEN (
                    SELECT COUNT(*)
                    FROM program_courses pc
                    WHERE pc.pc_active_sess = rwo.active_session_id
                      AND pc.pc_sess_id     = rwo.rdmap_session_id
                      AND pc.pc_school_id   = rwo.school_id
                      AND pc.pc_dept_id     = rwo.department_id
                      AND pc.pc_prog_id     = rwo.program_id
                      AND pc.pc_course_id   = rwo.course_id
                ) > 0 THEN 'Yes'
                ELSE 'No'
            END AS course_offered
        FROM roadmap_wise_offering rwo
        LEFT JOIN academic_sessions ac1 ON ac1.sess_id = rwo.active_session_id
        LEFT JOIN academic_sessions ac2 ON ac2.sess_id = rwo.rdmap_session_id
        LEFT JOIN schools s             ON rwo.school_id    = s.school_id
        LEFT JOIN departments d         ON rwo.department_id = d.dept_id
        LEFT JOIN programs p            ON rwo.program_id    = p.prog_id
        LEFT JOIN courses_two c         ON c.course_id       = rwo.course_id
        WHERE 1=1
    ";

    // Apply filters only if present
    $params = [];

    if ($session_id) {
        $query .= " AND rwo.active_session_id = :session_id";
        $params['session_id'] = $session_id;
    }
    if ($roadmap_session_id) {
        $query .= " AND rwo.rdmap_session_id = :roadmap_session_id";
        $params['roadmap_session_id'] = $roadmap_session_id;
    }
    if ($school_id) {
        $query .= " AND rwo.school_id = :school_id";
        $params['school_id'] = $school_id;
    }
    if ($dept_id) {
        $query .= " AND rwo.department_id = :dept_id";
        $params['dept_id'] = $dept_id;
    }
    if ($prog_id) {
        $query .= " AND rwo.program_id = :prog_id";
        $params['prog_id'] = $prog_id;
    }

    // Execute
    $res = DB::select($query, $params);

    return DataTables::of($res)->make(true);
}

    public function edit($id){
        
        $roadmap = DB::table('roadmap_wise_offering')
        ->where('id', $id)
        ->first();

        $sections = DB::table('course_section_count')
            ->where('rwf_id', $id)
            ->get();

        return response()->json([
            'id'                     => $roadmap->id,
            'active_session_id'      => $roadmap->active_session_id,
            'rdmap_session_id'       => $roadmap->rdmap_session_id,
            'school_id'              => $roadmap->school_id,
            'department_id'          => $roadmap->department_id,
            'program_id'             => $roadmap->program_id,
            'semester_id'            => $roadmap->semester_id,
            'course_id'              => $roadmap->course_id,
            'course_type'            => $roadmap->course_type,
            'theory_credit_hours'    => $roadmap->theory_credit_hours,
            // 'practical_credit_hours' => $roadmap->practical_credit_hours,
            'course_offering_type'   => $roadmap->course_offering_type,
            'course_capacity'        => $roadmap->course_capacity,
            'co_total_classes'       => $roadmap->co_total_classes,
            'co_req_classes'         => $roadmap->co_req_classes,
            'co_req_present'         => $roadmap->co_req_present,
            'co_custom'              => $roadmap->co_custom,
            'sections'               => $sections // Send sections as an array
        ]);
    }

    // public function edit2($id){
    //     $roadmap = DB::table('roadmap_wise_offering')
    //         ->where('id', $id)
    //         ->first();

    //     if (!$roadmap) {
    //         return response()->json(['message' => 'Record not found.'], 404);
    //     }

    //     // Get all sections for this roadmap offering
    //     $sections = DB::table('course_section_count')
    //         ->where('rwf_id', $id)
    //         ->get();

    //     // Attach assigned students per section
    //     $sectionsWithStudents = $sections->map(function ($sec) use ($id) {
    //         $students = DB::table('course_section_students')
    //             ->where('rwf_id', $id)
    //             ->where('course_section', $sec->course_section) // or section_id if numeric
    //             ->pluck('sai_enrollment_no'); // or ->pluck('student_id')

    //         $sec->assigned_students = $students; // add collection of roll_no's
    //         return $sec;
    //     });

    //     return response()->json([
    //         'id'                  => $roadmap->id,
    //         'active_session_id'   => $roadmap->active_session_id,
    //         'rdmap_session_id'    => $roadmap->rdmap_session_id,
    //         'school_id'           => $roadmap->school_id,
    //         'department_id'       => $roadmap->department_id,
    //         'program_id'          => $roadmap->program_id,
    //         'semester_id'         => $roadmap->semester_id,
    //         'course_id'           => $roadmap->course_id,
    //         'course_type'         => $roadmap->course_type,
    //         'theory_credit_hours' => $roadmap->theory_credit_hours,
    //         'sections'            => $sectionsWithStudents
    //     ]);
    // }

        public function edit2($id)
    {
        $roadmap = DB::table('roadmap_wise_offering')
            ->where('id', $id)
            ->first();

        if (!$roadmap) {
            return response()->json(['message' => 'Record not found.'], 404);
        }

        // Get all sections for this roadmap offering
        $sections = DB::table('course_section_count')
            ->where('rwf_id', $id)
            ->get();

        // Attach assigned students per section
        $sectionsWithStudents = $sections->map(function ($sec) use ($id) {
            $students = DB::table('course_section_students')
                ->where('rwf_id', $id)
                ->where('course_section', $sec->course_section) // or section_id if numeric
                ->pluck('sai_enrollment_no'); // or ->pluck('student_id')

            $sec->assigned_students = $students; // add collection of roll_no's
            return $sec;
        });

        return response()->json([
            'id'                  => $roadmap->id,
            'active_session_id'   => $roadmap->active_session_id,
            'rdmap_session_id'    => $roadmap->rdmap_session_id,
            'school_id'           => $roadmap->school_id,
            'department_id'       => $roadmap->department_id,
            'program_id'          => $roadmap->program_id,
            'semester_id'         => $roadmap->semester_id,
            'course_id'           => $roadmap->course_id,
            'course_type'         => $roadmap->course_type,
            'theory_credit_hours' => $roadmap->theory_credit_hours,
            'sections'            => $sectionsWithStudents
        ]);
    }

    
    public function destroy($id){
        DB::table('roadmap_wise_offering')->where('id', $id)->delete();
        return response()->json(['status' => 'deleted']);
    }

    // public function destroy2($id){
    //     DB::transaction(function () use ($id) {
    //         // Delete from course_section_students
    //         DB::table('course_section_students')
    //             ->where('rwf_id', $id)
    //             ->delete();

    //         // Delete from course_section_count
    //         DB::table('course_section_count')
    //             ->where('rwf_id', $id)
    //             ->delete();

    //         // Finally delete from roadmap_wise_offering
    //         DB::table('roadmap_wise_offering')
    //             ->where('id', $id)
    //             ->delete();
    //     });

    //     return response()->json(['status' => 'deleted']);
    // }

    public function destroy2($id){
        DB::transaction(function () use ($id) {
            // Delete from course_section_students
            DB::table('course_section_students')
                ->where('rwf_id', $id)
                ->delete();

            // Delete from course_section_count
            DB::table('course_section_count')
                ->where('rwf_id', $id)
                ->delete();

            // Finally delete from roadmap_wise_offering
            DB::table('roadmap_wise_offering')
                ->where('id', $id)
                ->delete();
        });

        return response()->json(['status' => 'deleted']);
    }
    public function roadmapofferingStoreOld(Request $request){
        // dd($request->all());
        $validated = $request->validate(
            [
                'session_id' => 'required|numeric',
                'roadmap_session_id' => 'required|numeric',
                'school_id' => 'required|numeric',
                'department_id' => 'required|numeric',
                'program_id' => 'required|numeric',
                'semester_id' => 'required|numeric',
                // 'course_id' => 'required|numeric',
                'course_id' => 'required|array',
                'course_id.*' => [
                    'required',
                    'regex:/^(E[1-3]|S[1-4]|\d+)$/'
                ],
                
                'course_offering_type' => 'required|numeric',
                // 'course_capacity'      => 'required|numeric|min:1',
                // 'co_total_classes'     => 'required|numeric|min:1',
                // 'co_req_classes'       => 'required|numeric|min:0|max:100',
                // 'co_req_present'       => 'required|numeric|min:0',
                'course_type' => 'required',
                // 'co_custom'            => 'required|numeric|in:1,2',
                'theory_credit_hours' => 'required|numeric',
                'section_names' => 'required|array',
                'section_names.*' => 'required|string|distinct',

                
                'section_students' => 'required|array',
                'section_students.*' => 'required|numeric|min:1',

                // newly added fields:
               
                
            ],
            [
                'session_id.required'           => 'Please select an active session.',       
                'roadmap_session_id.required'   => 'Please select a roadmap session.',         
                'school_id.required'            => 'Please select a school.',        
                'department_id.required'        => 'Please select a department.',         
                'program_id.required'           => 'Please select a program.',        
                'semester_id.required'          => 'Please select a semester.',         
                'course_id.required'            => 'Please select a course.',                   
                'course_offering_type.required' => 'Please select a course offering type.',
                // 'course_capacity.required'      => 'Please enter the class capacity.',
                // 'course_capacity.min'           => 'Class capacity must be at least 1.',
                // 'co_total_classes.required'     => 'Please enter the total number of classes.',
                // 'co_total_classes.min'          => 'Total classes must be at least 1.',
                // 'co_req_classes.required'       => 'Please enter the required attendance percentage.',
                // 'co_req_classes.min'            => 'Attendance percentage cannot be negative.',
                // 'co_req_classes.max'            => 'Attendance percentage cannot exceed 100%.',
                // 'co_req_present.required'       => 'Please enter the required number of presents.',
                'course_type.required'          => 'Please select a course type.',
                // 'co_custom.required'            => 'Please select if the course is custom.',
                // 'co_custom.in'                  => 'Invalid option for course custom field.',
                'theory_credit_hours.required'  => 'Please select a theory Cr Hr.',
                'section_names.required' => 'Please add at least one section.',
                'section_names.*.required' => 'Section name is required.',
                'section_names.*.distinct' => 'Section names must be unique.',
                'section_students.required' => 'Please enter student count for each section.',
                'section_students.*.required' => 'Student count is required for each section.',
                'section_students.*.numeric' => 'Student count must be a number.',
                'section_students.*.min' => 'Each section must have at least 1 student.',         
                
            ]
        );

        // ✅ Additional custom validation
        $totalSectionStudents = array_sum($request->section_students);
        $studentCountValue = (int) $request->studentCountValue;

        if ($totalSectionStudents > $studentCountValue) {
            return response()->json([
                'errors' => [
                    'section_students' => ['Total students in all sections cannot exceed ' . $studentCountValue . '.']
                ]
            ], 422);
        }


        $data = [
            'active_session_id'      => $request->session_id,
            'rdmap_session_id'       => $request->roadmap_session_id,
            'school_id'              => $request->school_id,
            'department_id'          => $request->department_id,
            'program_id'             => $request->program_id,
            'semester_id'            => $request->semester_id,
            'course_id'              => $request->course_id,
            'course_type'            => $request->course_type,
            'theory_credit_hours'    => $request->theory_credit_hours,
            // 'practical_credit_hours' => $request->practical_credit_hours,
            'course_offering_type'   => $request->course_offering_type,
            'course_capacity'        => $request->course_capacity,
            'co_total_classes'       => $request->co_total_classes,
            'co_req_classes'         => $request->co_req_classes,
            'co_req_present'         => $request->co_req_present,
            'co_custom'              => $request->co_custom,
            'status'                 => 'E',
        ];

        if ($request->get('formId')) {
            // UPDATE
            DB::table('roadmap_wise_offering')
                ->where('id', $request->get('formId'))
                ->update(array_merge($data, [
                    'updated_at'     => now(),
                    'entry_user_id' => auth()->id(),
                ]));

            // Delete old section records before re-inserting
            DB::table('course_section_count')
                ->where('rwf_id', $request->get('formId'))
                ->delete();

            // Insert new sections if provided
            if ($request->has('section_names')) {
                foreach ($request->section_names as $index => $sectionName) {
                    DB::table('course_section_count')->insert([
                        'rwf_id'            => $request->get('formId'),
                        'course_id'            => $request->course_id,
                        'course_section'       => $sectionName,
                        'course_section_count' => $request->section_students[$index] ?? 0,
                    ]);
                }
            }

            return response()->json(['message' => 'Updated']);
        } else {

            // dd($request->has('section_names'));exit;
            // INSERT
            // Insert main roadmap record and get its ID
            $rwfId = DB::table('roadmap_wise_offering')->insertGetId(array_merge($data, [
                'entry_user_id'  => auth()->id(),
                'entry_datetime' => now(),
                'created_at'     => now(),
            ]));

            // Insert sections if provided
            if ($request->has('section_names')) {
                foreach ($request->section_names as $index => $sectionName) {
                    DB::table('course_section_count')->insert([
                        'rwf_id'               => $rwfId, // FK to roadmap_wise_offering.id
                        'course_id'            => $request->course_id,
                        'course_section'       => $sectionName,
                        'course_section_count' => $request->section_students[$index] ?? 0,
                    ]);
                }
            }

            return response()->json(['message' => 'Saved']);
        }
    }
     public function roadmapofferingStore(Request $request){

        $validated = $request->validate(
            [
                'session_id'            => 'required|numeric',
                'roadmap_session_id'    => 'required|numeric',
                'school_id'             => 'required|numeric',
                'department_id'         => 'required|numeric',
                'program_id'            => 'required|numeric',
                'semester_id'           => 'required|numeric',
                'shift_id'              => 'required|numeric',
                
                'course_id' => 'required|array',
                'course_id.*' => [
                    'required',
                    'regex:/^(E[1-3]|S[1-4]|\d+)$/'
                ],
                'course_type'           => 'required',
                'theory_credit_hours'   => 'required|numeric',
                'section_names'         => 'required|array',
                'section_names.*'       => 'required|string|distinct',
                // optionally validate these if you want stricter checks:
                // 'course_offering_type' => 'required|numeric',
                // 'course_capacity'      => 'nullable|numeric|min:0',
                // 'co_total_classes'     => 'nullable|numeric|min:0',
                // 'co_req_classes'       => 'nullable|numeric|min:0|max:100',
                // 'co_req_present'       => 'nullable|numeric|min:0',
                // 'co_custom'            => 'nullable|numeric|in:0,1,2',
            ],
            [
                'course_id.required' => 'Please select at least one course.',
                'course_id.*.numeric' => 'Invalid course selected.',
                'section_names.required' => 'Please add at least one section.',
            ]
        );

        // ------------------------
        // UPDATE MODE (single existing formId)
        // ------------------------
        if ($request->formId) {

            $formId = $request->formId;

            // Update main roadmap record (common fields)
            DB::table('roadmap_wise_offering')
                ->where('id', $formId)
                ->update([
                    'active_session_id'   => $request->session_id,
                    'rdmap_session_id'    => $request->roadmap_session_id,
                    'school_id'           => $request->school_id,
                    'department_id'       => $request->department_id,
                    'program_id'          => $request->program_id,
                    'semester_id'         => $request->semester_id,
                    // course_id is typically not changed in update mode; use first selected course if provided
                    'course_id'           => is_array($request->course_id) ? ($request->course_id[0] ?? null) : $request->course_id,
                    'course_type'         => $request->course_type,
                    'theory_credit_hours' => $request->theory_credit_hours,

                    // <-- newly added fields for update
                    'course_offering_type' => $request->course_offering_type,
                    'course_capacity'      => $request->course_capacity,
                    'co_total_classes'     => $request->co_total_classes,
                    'co_req_classes'       => $request->co_req_classes,
                    'co_req_present'       => $request->co_req_present,
                    'co_custom'            => $request->co_custom,

                    'updated_at'          => now(),
                    'entry_user_id'       => auth()->id(),
                    'shift_id'            => $request->shift_id,
                ]);

            // Remove previous sections & students
            DB::table('course_section_students')->where('rwf_id', $formId)->delete();
            DB::table('course_section_count')->where('rwf_id', $formId)->delete();

            // Reinsert new sections & students (for the single roadmap record)
            foreach ($request->section_names as $index => $sectionName) {

                $cscId = DB::table('course_section_count')->insertGetId([
                    'rwf_id'               => $formId,
                    'course_id'            => is_array($request->course_id) ? ($request->course_id[0] ?? null) : $request->course_id,
                    'course_section'       => $sectionName,
                    'course_section_count' => (int)($request->section_students[$index] ?? 0),
                ]);

                $students = $request->input("students.$index", []);
                foreach ($students as $enr) {
                    DB::table('course_section_students')->insert([
                        'rwf_id'             => $formId,
                        'csc_id'             => $cscId,
                        'course_id'          => is_array($request->course_id) ? ($request->course_id[0] ?? null) : $request->course_id,
                        'course_section'     => $sectionName,
                        'sai_enrollment_no'  => $enr,
                        'status'             => 'Active',
                        'entry_user_id'      => auth()->id(),
                        'entry_datetime'     => now(),
                        'created_at'         => now(),
                    ]);
                }
            }

            return response()->json(['message' => 'Updated']);
        }

        // ------------------------
        // INSERT MODE — MULTIPLE COURSES
        // ------------------------
        foreach ($request->course_id as $singleCourseId) {

            // Insert one roadmap record per selected course
            $rwfId = DB::table('roadmap_wise_offering')->insertGetId([
                'active_session_id'    => $request->session_id,
                'rdmap_session_id'     => $request->roadmap_session_id,
                'school_id'            => $request->school_id,
                'department_id'        => $request->department_id,
                'program_id'           => $request->program_id,
                'semester_id'          => $request->semester_id,
                'course_id'            => $singleCourseId,
                'course_type'          => $request->course_type,
                'theory_credit_hours'  => $request->theory_credit_hours,

                // <-- newly added fields for insert
                'course_offering_type' => $request->course_offering_type,
                'course_capacity'      => $request->course_capacity,
                'co_total_classes'     => $request->co_total_classes,
                'co_req_classes'       => $request->co_req_classes,
                'co_req_present'       => $request->co_req_present,
                'co_custom'            => $request->co_custom,

                'status'               => 'E',
                'entry_user_id'        => auth()->id(),
                'entry_datetime'       => now(),
                'created_at'           => now(),
                'shift_id'            => $request->shift_id,
            ]);

            // Insert sections & their students for this course
            foreach ($request->section_names as $index => $sectionName) {

                $cscId = DB::table('course_section_count')->insertGetId([
                    'rwf_id'               => $rwfId,
                    'course_id'            => $singleCourseId,
                    'course_section'       => $sectionName,
                    'course_section_count' => (int)($request->section_students[$index] ?? 0),
                ]);

                $students = $request->input("students.$index", []);
                foreach ($students as $enr) {
                    DB::table('course_section_students')->insert([
                        'rwf_id'             => $rwfId,
                        'csc_id'             => $cscId,
                        'course_id'          => $singleCourseId,
                        'course_section'     => $sectionName,
                        'sai_enrollment_no'  => $enr,
                        'status'             => 'Active',
                        'entry_user_id'      => auth()->id(),
                        'entry_datetime'     => now(),
                        'created_at'         => now(),
                    ]);
                }
            }
        }

        return response()->json(['message' => 'Saved']);
    }


    // public function roadmapofferingStore2(Request $request){
    //         // dd($request->get('section_names'));
    //         $validated = $request->validate(
    //         [
    //             'session_id' => 'required|numeric',
    //             'roadmap_session_id' => 'required|numeric',
    //             'school_id' => 'required|numeric',
    //             'department_id' => 'required|numeric',
    //             'program_id' => 'required|numeric',
    //             'semester_id' => 'required|numeric',
    //             'course_id' => 'required|numeric',
    //             'course_type' => 'required',
    //             'theory_credit_hours' => 'required|numeric',
    //             'section_names' => 'required|array',
    //             'section_names.*' => 'required|string|distinct',
    //         ],
    //         [
    //             'session_id.required' => 'Please select a session.',       
    //             'roadmap_session_id.required' => 'Please select a roadmap session.',         
    //             'school_id.required' => 'Please select a school.',        
    //             'department_id.required' => 'Please select a department.',         
    //             'program_id.required' => 'Please select a program.',        
    //             'semester_id.required' => 'Please select a semester.',         
    //             'course_id.required' => 'Please select a course.',       
    //             'course_type.required' => 'Please select a course type.',
    //             'theory_credit_hours.required' => 'Please select a Theory Cr Hr.',
    //             'section_names.required' => 'Please add at least one section.',
    //             'section_names.*.distinct' => 'Section names must be unique.',
                
    //         ]
    //     );


    //     $data = [
    //         'active_session_id'      => $request->session_id,
    //         'rdmap_session_id'       => $request->roadmap_session_id,
    //         'school_id'              => $request->school_id,
    //         'department_id'          => $request->department_id,
    //         'program_id'             => $request->program_id,
    //         'semester_id'            => $request->semester_id,
    //         'course_id'              => $request->course_id,
    //         'course_type'            => $request->course_type,
    //         'theory_credit_hours'    => $request->theory_credit_hours,
    //         // 'practical_credit_hours' => $request->practical_credit_hours,
    //         'status'                 => 'E',
    //     ];

    //     if ($request->get('formId')) {
    //         $formId = $request->get('formId');

    //         // UPDATE main roadmap record
    //         DB::table('roadmap_wise_offering')
    //             ->where('id', $formId)
    //             ->update(array_merge($data, [
    //                 'updated_at'    => now(),
    //                 'entry_user_id' => auth()->id(),
    //             ]));

    //         // Delete old section + student records before re-inserting
    //         DB::table('course_section_students')->where('rwf_id', $formId)->delete();
    //         DB::table('course_section_count')->where('rwf_id', $formId)->delete();

    //         // Insert new sections + students
    //         if ($request->has('section_names')) {
    //             foreach ($request->section_names as $index => $sectionName) {
    //                 // insert section
    //                 $cscId = DB::table('course_section_count')->insertGetId([
    //                     'rwf_id'               => $formId,
    //                     'course_id'            => $request->course_id,
    //                     'course_section'       => $sectionName,
    //                     'course_section_count' => count($request->input("students.$index", [])),
    //                 ]);

    //                 // insert assigned students for this section
    //                 $enrollmentNos = $request->input("students.$index", []); // array of ENR nos
    //                 foreach ($enrollmentNos as $enr) {
    //                     DB::table('course_section_students')->insert([
    //                         'rwf_id'            => $formId,
    //                         'csc_id'            => $cscId,
    //                         'course_id'         => $request->course_id,
    //                         'course_section'    => $sectionName,
    //                         'sai_enrollment_no' => $enr,
    //                         'status'            => 'Active',
    //                         'entry_user_id'     => auth()->id(),
    //                         'entry_datetime'    => now(),
    //                         'created_at'        => now(),
    //                     ]);
    //                 }
    //             }
    //         }

    //         return response()->json(['message' => 'Updated']);
    //     }else {

    //         // dd($request->has('section_names'));exit;
    //         // INSERT
    //         // Insert main roadmap record and get its ID
    //         $rwfId = DB::table('roadmap_wise_offering')->insertGetId(array_merge($data, [
    //             'entry_user_id'  => auth()->id(),
    //             'entry_datetime' => now(),
    //             'created_at'     => now(),
    //         ]));

    //         // Insert sections if provided
    //         // if ($request->has('section_names')) {
    //         //     foreach ($request->section_names as $index => $sectionName) {
    //         //         DB::table('course_section_count')->insert([
    //         //             'rwf_id'               => $rwfId, // FK to roadmap_wise_offering.id
    //         //             'course_id'            => $request->course_id,
    //         //             'course_section'       => $sectionName,
    //         //             'course_section_count' => $request->section_students[$index] ?? 0,
    //         //         ]);
    //         //     }
    //         // }

    //         // Insert sections
    //         if ($request->has('section_names')) {
    //             foreach ($request->section_names as $index => $sectionName) {
    //                 $cscId = DB::table('course_section_count')->insertGetId([
    //                     'rwf_id'               => $rwfId,
    //                     'course_id'            => $request->course_id,
    //                     'course_section'       => $sectionName,
    //                     'course_section_count' => (int)($request->section_students[$index] ?? 0),
    //                 ]);

    //                 // this will now be TRUE when a section has students
    //                 // dd($request->has("students.$index"));

    //                 $enrollmentNos = $request->input("students.$index", []); // array of ENR nos
    //                 foreach ($enrollmentNos as $enr) {
    //                     DB::table('course_section_students')->insert([
    //                         'rwf_id'             => $rwfId,
    //                         'csc_id'             => $cscId,
    //                         'course_id'          => $request->course_id,
    //                         'course_section'     => $sectionName,
    //                         'sai_enrollment_no'  => $enr,
    //                         'status'             => 'Active',
    //                         'entry_user_id'      => auth()->id(),
    //                         'entry_datetime'     => now(),
    //                         'created_at'         => now(),
    //                     ]);
    //                 }
    //             }
    //         }

    //         return response()->json(['message' => 'Saved']);
    //     }
    // }

    public function roadmapofferingStore2(Request $request){
        // ------------------------
        // VALIDATION
        // ------------------------
        $validated = $request->validate(
            [
                'session_id' => 'required|numeric',
                'roadmap_session_id' => 'required|numeric',
                'school_id' => 'required|numeric',
                'department_id' => 'required|numeric',
                'program_id' => 'required|numeric',
                'semester_id' => 'required|numeric',

                'course_id' => 'required|array',
                'course_id.*' => 'required|numeric',

                'course_type' => 'required',
                'theory_credit_hours' => 'required|numeric',

            
            ],
            [
                'session_id.required' => 'Please select a session.',
                'roadmap_session_id.required' => 'Please select a roadmap session.',
                'school_id.required' => 'Please select a school.',
                'department_id.required' => 'Please select a department.',
                'program_id.required' => 'Please select a program.',
                'semester_id.required' => 'Please select a semester.',

                'course_id.required' => 'Please select at least one course.',
                'course_id.*.numeric' => 'Invalid course selected.',

                'course_type.required' => 'Please select a course type.',
                'theory_credit_hours.required' => 'Please select a Theory Cr Hr.',

            
            ]
        );

        // ------------------------
        // UPDATE MODE
        // ------------------------
        if ($request->formId) {

            $formId = $request->formId;

            // Update roadmap record (use FIRST course if multiple)
            DB::table('roadmap_wise_offering')
                ->where('id', $formId)
                ->update([
                    'active_session_id'    => $request->session_id,
                    'rdmap_session_id'     => $request->roadmap_session_id,
                    'school_id'            => $request->school_id,
                    'department_id'        => $request->department_id,
                    'program_id'           => $request->program_id,
                    'semester_id'          => $request->semester_id,
                    'course_id'            => $request->course_id[0], // use first one only
                    'course_type'          => $request->course_type,
                    'theory_credit_hours'  => $request->theory_credit_hours,

                    'updated_at'           => now(),
                    'entry_user_id'        => auth()->id(),
                ]);

            // Delete old section + student records
            DB::table('course_section_students')->where('rwf_id', $formId)->delete();
            DB::table('course_section_count')->where('rwf_id', $formId)->delete();

            // Insert updated sections and students
            if (!empty($request->section_names)) {
                foreach ($request->section_names as $index => $sectionName) {

                    $cscId = DB::table('course_section_count')->insertGetId([
                        'rwf_id'               => $formId,
                        'course_id'            => $request->course_id[0], // first course
                        'course_section'       => $sectionName,
                        'course_section_count' => (int)($request->section_students[$index] ?? 0),
                    ]);

                    $students = $request->input("students.$index", []);
                    foreach ($students as $enr) {
                        DB::table('course_section_students')->insert([
                            'rwf_id'            => $formId,
                            'csc_id'            => $cscId,
                            'course_id'         => $request->course_id[0],
                            'course_section'    => $sectionName,
                            'sai_enrollment_no' => $enr,
                            'status'            => 'Active',
                            'entry_user_id'     => auth()->id(),
                            'entry_datetime'    => now(),
                            'created_at'        => now(),
                        ]);
                    }
                }
            }

            return response()->json(['message' => 'Updated']);
        }

        // ------------------------
        // INSERT MODE — MULTIPLE COURSES
        // ------------------------
        foreach ($request->course_id as $singleCourseId) {

            // Insert main roadmap record
            $rwfId = DB::table('roadmap_wise_offering')->insertGetId([
                'active_session_id'    => $request->session_id,
                'rdmap_session_id'     => $request->roadmap_session_id,
                'school_id'            => $request->school_id,
                'department_id'        => $request->department_id,
                'program_id'           => $request->program_id,
                'semester_id'          => $request->semester_id,
                'course_id'            => $singleCourseId,
                'course_type'          => $request->course_type,
                'theory_credit_hours'  => $request->theory_credit_hours,
                'status'               => 'E',

                'entry_user_id'        => auth()->id(),
                'entry_datetime'       => now(),
                'created_at'           => now(),
            ]);

            // Insert sections and students for each selected course
            if($request->section_names){
                foreach ($request->section_names as $index => $sectionName) {

                    $cscId = DB::table('course_section_count')->insertGetId([
                        'rwf_id'               => $rwfId,
                        'course_id'            => $singleCourseId,
                        'course_section'       => $sectionName,
                        'course_section_count' => (int)($request->section_students[$index] ?? 0),
                    ]);

                    $students = $request->input("students.$index", []);
                    foreach ($students as $enr) {
                        DB::table('course_section_students')->insert([
                            'rwf_id'             => $rwfId,
                            'csc_id'             => $cscId,
                            'course_id'          => $singleCourseId,
                            'course_section'     => $sectionName,
                            'sai_enrollment_no'  => $enr,
                            'status'             => 'Active',
                            'entry_user_id'      => auth()->id(),
                            'entry_datetime'     => now(),
                            'created_at'         => now(),
                        ]);
                    }
                }
            }
        }

        return response()->json(['message' => 'Saved']);
    }

      public function getActiveStudentsBySchool(Request $request){
        $school_id  = $request->get('school_id');       
        $session  = $request->get('sess_id');  
        $program_id  = $request->get('prog_id');   
        $students = DB::table('students_apply_info')
            ->leftJoin('students_personal_info', 'students_apply_info.sai_spi_id', '=', 'students_personal_info.spi_id')
            ->leftJoin('schools', 'students_apply_info.sai_sdpo_school_id', '=', 'schools.school_id')
            ->leftJoin('departments', 'students_apply_info.sai_sdpo_dept_id', '=', 'departments.dept_id')
            ->leftJoin('programs', 'students_apply_info.sai_sdpo_prog_id', '=', 'programs.prog_id')
            ->select(
                'sai_enrollment_no',
                'sai_student_status',
                'students_personal_info.spi_name',
                'schools.school_code',
                'departments.dept_name',
                'programs.prog_name'
            )
            // ->where('students_apply_info.sai_sdpo_sess_id', $session)        
            ->where('students_apply_info.sai_sdpo_school_id', $school_id)
            // ->where('students_apply_info.sai_sdpo_prog_id', $program_id)
             
            ->whereIn('students_apply_info.sai_student_status', ['Active', 'Expected-Graduate'])
            ->orderBy('students_apply_info.sai_sdpo_prog_id', 'desc')
            ->get();

        return response()->json($students);
    }
	
	
    public function admissionQualification()
    { 
     
        return view('enrollment.admissionQualification');

    }

    public function getGroupPagesManagmentRec(Request $request){        

        $enrolement_rights_query="SELECT GPM_ID,ID,
        GPM_ACTIVE_SESSL_ID,
        GPM_ROADMAP_SESS_ID,       
        GPM_SCHOOL_ID,
        GPM_SAI_ENROLLMENT_NO,	
        start_date_time,
        end_date_time,
        (SELECT SESS_NAME FROM ACADEMIC_SESSIONS WHERE SESS_ID=GPM_ROADMAP_SESS_ID
          ) AS RD_SESS_NAME,
        (SELECT SESS_NAME FROM ACADEMIC_SESSIONS WHERE SESS_ID=GPM_ACTIVE_SESSL_ID
        ) AS SESS_NAME,
        GPM_SCHOOL_ID,
        (SELECT SCHOOL_CODE FROM SCHOOLS WHERE SCHOOL_ID=GPM_SCHOOL_ID
        ) AS SCHOOL_NAME,
        GPM_DEPT_ID,
        (SELECT DEPT_NAME FROM DEPARTMENTS WHERE DEPT_ID=REPLACE(GPM_DEPT_ID,'undefined','0')
        ) AS DEPT_NAME,
        GPM_PROG_ID,
        (SELECT PROG_NAME FROM PROGRAMS WHERE PROG_ID=REPLACE(GPM_PROG_ID,'undefined','0')
        ) AS PROGRAME_NAME,
        GPM_PAGE_ID,
      (SELECT DISTINCT page_name FROM pages WHERE page_id=GPM_PAGE_ID
      ) AS PAGE_NAME,
        GPM_SHIFT_ID,
        (SELECT SHIFT_NAME FROM SHIFTS WHERE SHIFT_ID=GPM_SHIFT_ID
        ) AS GPM_SHIFT_NAME
      FROM GROUP_PAGES_MANAGEMENT where GPM_ID>1 and gpm_page_id in (321,381)";
        $res = DB::select($enrolement_rights_query);
  //echo  $enrolement_rights_query;exit;
    // print_r($res);exit;
        return response()->json($res);

    } 
    public function getApplicantInformation($session,$school,$dept,$prog){        
      $username = auth()->user()->username;
      $sql = "SELECT
      admission_apply_info.aai_sdpo_prog_id,
      admission_apply_info.aai_sdpo_sess_id,
      admission_personal_info.api_id,
      admission_personal_info.api_mobile,
      admission_personal_info.api_name,
      admission_personal_info.api_formid,
      admission_personal_info.api_nic,
      admission_personal_info.api_guardian_cnic,
      admission_personal_info.api_fathername,
      admission_personal_info.api_imagename,
      admission_apply_info.aai_id,
      admission_apply_info.aai_api_form_id,
      programs.prog_title AS aai_programid,
      programs.prog_title AS aai_degreecode,
      admission_apply_info.aai_interview_marks,
      admission_apply_info.aai_interview_totalmarks,
      admission_apply_info.aai_api_email AS uemail,
      admission_apply_info.aai_pf_remarks,
      admission_apply_info.aai_qualification_status,
      admission_apply_info.aai_qualification_remarks,
        admission_apply_info.cms_sai_enrollment_no,
      sai_student_status,
      TO_CHAR(TRIM(sai_remarks) ) AS sai_remarks,
      (
          SELECT
              admission_schedules.admission_sch_status
          FROM
              admission_schedules
          WHERE
              admission_schedules.admission_sch_id = admission_apply_info.aai_admission_sch_id
      ) AS sch_status
      FROM
      admission_personal_info
      LEFT JOIN admission_apply_info ON api_id = aai_api_id
      LEFT JOIN programs ON aai_sdpo_prog_id = prog_id
      LEFT JOIN students_apply_info ON
          api_id = sai_api_id
      AND
          sai_sdpo_sess_id = (
              SELECT
                  MAX(sai_sub.sai_sdpo_sess_id)
              FROM
                  students_apply_info sai_sub
              WHERE
                  sai_sub.sai_api_id = admission_apply_info.aai_api_id
          )
      LEFT JOIN admission_schedules ON admission_schedules.admission_sch_id = admission_apply_info.aai_admission_sch_id
      WHERE
          admission_personal_info.api_id = admission_apply_info.aai_api_id
      AND
          admission_apply_info.aai_pf_status = 'Fee Verified'
      AND
          aai_pf_status = 'Fee Verified'
      AND
          admission_sch_status = 'E'    
            and  admission_apply_info.cms_sai_enrollment_no is not null
      AND
          aai_sdpo_sess_id = $session
      AND
          aai_sdpo_school_id = $school
      AND
          aai_sdpo_dept_id = $dept
    AND
          aai_sdpo_prog_id = $prog";
      // if($username != 'haider.raza' && $username != 'bilal.saleem'){
        // $sql .= " AND aai_sdpo_school_id IN (SELECT emp_school_id FROM emp_employees WHERE emp_ad_username = '$username')";
      // }
    //   echo $sql;exit;
      $res = DB::select($sql);
      return response()->json($res);

    } 

  public function documentVerification(Request $request){

    // dd($request->all());

    $api_id = $request->get('api_id');
    $prog_id = $request->get('prog_id');
    // $admissionDocument=DB::select('select * from admission_document where ad_api_id = "'.$request->get('api_id').'"');
    // echo $admissionDocument;

    // $sql = "select * from admission_document
    // left join admission_qualification on admission_document.ad_api_id=admission_qualification.aq_api_id
    //  where ad_api_id = $api_id and ad_sdpo_prog_id=$prog_id";
    $sql = "SELECT
    (
        SELECT
            aq_result_status
        FROM
            admission_qualification
        WHERE
            aq_degree_name = ad_type
            AND aq_api_id = ad_api_id
    ) AS result_status,
    (
    SELECT
        MAX(aai_api_email)
    FROM
        admission_apply_info
    WHERE
        aai_api_id = ad_api_id
    ) AS student_email,
    (
    SELECT
        api_name
    FROM
        admission_personal_info
    WHERE
        api_id = ad_api_id
    ) AS applicant_name,
        admission_document.*
    FROM
    admission_document
     WHERE admission_document.ad_type NOT IN ('uploadFile')
      and ad_api_id = $api_id and ad_sdpo_prog_id=$prog_id";
    $admissionDocument = DB::select($sql);
     //echo $sql;exit();
    // dd($admissionDocument);
    // $user = new User();
    // $appliedCriteria = $user->getAppliedCriteria(74620);
    $record = DB::table('admission_personal_info')
    ->where('api_id', $api_id)
    ->first();


    return view('enrollment/docVerification',['admissionDocument'=>$admissionDocument,'record'=>$record]);
   
  } 
    public function insertRecord(Request $request){

      $sess = new sessions();
      $sess->startSession();
      $login_id = $sess->emp_user_id;
      
      $sess_id          = $request->get('sess_id');
      $school_id        = $request->get('school_id');
      $roadmap_session  = $request->get('roadmap_session');
      $dept_id          = $request->get('department_id');
      $prog_id          = $request->get('program_id');
      $shift_id         = $request->get('shift_id');
      $reg_no           = $request->get('reg_no');
      $page_right_id    = $request->get('page_right_id');
      $startDate        = $request->get('start_date');
      $startTime        = $request->get('start_time');  
      $endDate          = $request->get('end_date');
      $endTime          = $request->get('end_time');      
      $sess_name        = $request->get('sess_name');
      //$page_name          = $request->get('page_name'); 

      $Stime         = (String) $startDate.' '.$startTime;
      $startDateTime = new \Carbon\Carbon($Stime);   

      $Etime         = (String) $endDate.' '.$endTime;
      $endDateTime   = new \Carbon\Carbon($Etime);  

      if($sess_id == ''){
        return response()->json(array('status' => false,'data'=> [], 'message' => 'Please Select Enrollment Session',
          'action' => [ 'type' => 'redirect', 'url' => 'abc' ]));
      }  
      if($school_id == ''){
        return response()->json(array('status' => false,'data'=> [], 'message' => 'Please Select School',
          'action' => [ 'type' => 'redirect', 'url' => 'abc' ]));
      } 
      if($page_right_id == ''){
        return response()->json(array('status' => false,'data'=> [], 'message' => 'Please Select Page Rights',
          'action' => [ 'type' => 'redirect', 'url' => 'abc' ]));
      }  
      
      if($page_right_id == 321){
        $action = 'enrollment';
      }
      else if($page_right_id == 381){    
        $action = 'adddrop';    
      }

     
      
      $lastInsertGPM_ID = DB::table('group_pages_management')->insertGetId(
        [
         'gpm_active_sessl_id' => $sess_id,
         'gpm_roadmap_sess_id' => $roadmap_session,
         'gpm_sai_enrollment_no' => $reg_no,
         'gpm_school_id' => $school_id,
         'gpm_dept_id' => $dept_id,
         'gpm_prog_id'=> $prog_id,
         'gpm_shift_id'=> $shift_id,
         'gpm_page_id'=>$page_right_id,
         'GPM_ENTRY_USER_ID'=>$login_id,
         'start_date_time'=>$startDateTime,
         'end_date_time'=>$endDateTime,
         'gpm_sess_name'=>$sess_name,
         'gpm_page_name'=>$action
        ]
      );
       //dd($lastInsertGPM_ID);
     
      // $updtsql="update STUDENTS_APPLY_INFO set SAI_ENROLLMENT_SESSION_ID = ".$sess_id."
      //           ,SAI_ENROLLMENT_PAGE_NAME = '$action' 
      //           ,SAI_ENROLLMENT_SESSION_NAME = '$lastInsertGPM_ID'
      //           where SAI_STUDENT_STATUS in ('Expected-Graduate','Exp. Grad.','Active')";
      //           if(isset($school_id) and $school_id !=""){
      //             $updtsql.= "  AND sai_sdpo_school_id='".$school_id."'";
      //           }
      //           if(isset($roadmap_session) and $roadmap_session !=""){
      //             $updtsql.= "  AND sai_sdpo_sess_id='".$roadmap_session."'";
      //           }
      //           if(isset($reg_no) and $reg_no !=""){
      //             $updtsql.= "  AND sai_enrollment_no='".$reg_no."'";
      //           }	
      // //echo $updtsql; exit;
      // $resupdtsql = DB::insert($updtsql);      

      // $updateSisuserGpmid = DB::table('SISUSERS') ->where('gpm_id', $lastInsertGPM_ID)->update([
      //   'gpm_id' => $lastInsertGPM_ID           
      // ]);

      $updt = "update sisusers set gpm_id = $lastInsertGPM_ID where lower(username) in (select lower(sai_enrollment_no) from  STUDENTS_APPLY_INFO 
      where SAI_STUDENT_STATUS in ('Expected-Graduate','Exp. Grad.','Active')";
     
      if(isset($school_id) and $school_id !=""){
        $updt.= "  AND sai_sdpo_school_id='".$school_id."'";
      }
      if(isset($dept_id) and $dept_id !=""){
        $updt.= "  AND sai_sdpo_dept_id='".$dept_id."'";
      }
      if(isset($prog_id) and $prog_id !=""){
        $updt.= "  AND sai_sdpo_prog_id='".$prog_id."'";
      }     
      if(isset($roadmap_session) and $roadmap_session !=""){
        $updt.= "  AND sai_sdpo_sess_id='".$roadmap_session."'";
      }
      if(isset($reg_no) and $reg_no !=""){
        $updt.= "  AND lower(sai_enrollment_no)=lower('".$reg_no."')";
      }	
      $updt.= ")";
      // echo $updt;exit;
      $result = DB::insert($updt);
    
      $procedureName = 'sulmansb';
      $bindings = [
          'sess_id'  =>$sess_id,
          'school_id'  =>$school_id
      ];
      $result = DB::executeProcedure($procedureName, $bindings);

      if($result){
          return response()->json(array('status' => true,'data'=> [], 'message' => $action.' Starts Successfully',
            'action' => [ 'type' => 'redirect', 'url' => 'abc' ]));
      }  

    }

    public function deleteEnrollment($id){

      // echo $id;exit;
      $delGPM_ID = DB::table('group_pages_management')->where('id', $id)->delete();
      //  $del = "delete from group_pages_management where id is not null;";
      if($delGPM_ID){

        $updSisusers = DB::table('sisusers')
              ->where('gpm_id', $id)
              ->update([
                  'gpm_id'=> 0
              ]);

        return response()->json(array('status' => true,'data'=> [], 'message' =>  'Enrollment Closed Successfully'));
      }  

    }

    public function checkEnrollmentStatus(){

      $sql_enrollment = "select count(*) as total,school_code from sisusers 
      left join students_apply_info on lower(sai_enrollment_no)=lower(username)
      left join group_pages_management on sisusers.gpm_id = group_pages_management.id
      left join schools on schools.school_id = students_apply_info.sai_sdpo_school_id
      where sisusers.gpm_id != 0
      group by school_code";
      $res_sql_enrollment = DB::select($sql_enrollment);	
   
      //echo $res_sql_enrollment;exit;

      return response()->json($res_sql_enrollment);

    }

    public function getRoadmapData(){   
        $request = Request::capture(); // or: request()
        $hodReport = new HodReportController();
        $activeSchools = $hodReport->schoolsloginUserOnly($request);
        $activeDeparments = $hodReport->activedepartments($request);
        $activePrograms = $hodReport->programs($request);
        $sessions = $hodReport->roadmapsessions();
        return view('enrollment.viewroadmap',compact('activeSchools','activeDeparments','activePrograms','sessions'));
    }

}
