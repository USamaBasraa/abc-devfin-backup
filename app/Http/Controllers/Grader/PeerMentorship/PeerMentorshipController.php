<?php

namespace App\Http\Controllers\Grader\PeerMentorship;

use App\Http\Controllers\Controller;



use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class PeerMentorshipController extends Controller
{
    private const PMSA_STATUS_ASSIGNED = 'assigned';

    private const PMSA_STATUS_NON_ASSIGNED = 'non assigned';

    private const PMSF_FEEDBACK_FROM_ADMIN = 'admin';

    private const PMSF_SATISFACTION_YES = 'satisfaction';

    private const PMSF_SATISFACTION_NO = 'no satisfaction';

    private const STAFF_REQUEST_STATUS_PENDING = 'Pending';

    private const STAFF_REQUEST_STATUS_COMPLETED = 'Completed';

    private const FACULTY_GROUP_ID = 222;

    public function fetchScholarshipStudents()
    {
        $currentSessionId = DB::table('student_courses')->max('SC_CO_SESS_ID');

        return view('peer_mentorship.scholarship_students', compact('currentSessionId'));
    }
///ars
    function getScholarshipStudents(Request $request){

        // dd($request->all());
        $maxSessionId = DB::table('student_courses')->max('SC_CO_SESS_ID');
        $session = $request->sess_id;
        $effectiveSessionId = $session ?: $maxSessionId;
        $school = $request->school_id;
        $sql = "SELECT 
    t.sai_enrollment_no,
    t.sai_student_status,
     (
        SELECT TO_CHAR(is_interview_date, 'YYYY-MM-DD') || ' ' || is_interview_time

        FROM interview_schedules 
        WHERE LOWER(is_enrollment_no) = LOWER(t.sai_enrollment_no)
        AND sess_id = '".$effectiveSessionId."'
        ORDER BY id DESC
        FETCH FIRST 1 ROWS ONLY
    ) AS interview_datetime,
     (
        SELECT TO_CHAR(is_interview_date, 'YYYY-MM-DD')

        FROM interview_schedules
        WHERE LOWER(is_enrollment_no) = LOWER(t.sai_enrollment_no)
        AND sess_id = '".$effectiveSessionId."'
        ORDER BY id DESC
        FETCH FIRST 1 ROWS ONLY
    ) AS interview_date,
     (
        SELECT is_interview_time

        FROM interview_schedules
        WHERE LOWER(is_enrollment_no) = LOWER(t.sai_enrollment_no)
        AND sess_id = '".$effectiveSessionId."'
        ORDER BY id DESC
        FETCH FIRST 1 ROWS ONLY
    ) AS interview_time,
     (
        SELECT interviewed

        FROM interview_schedules
        WHERE LOWER(is_enrollment_no) = LOWER(t.sai_enrollment_no)
        AND sess_id = '".$effectiveSessionId."'
        ORDER BY id DESC
        FETCH FIRST 1 ROWS ONLY
    ) AS interviewed,
    LISTAGG(t.scholarship_display, ', ') 
        WITHIN GROUP (ORDER BY t.scholarship_display) AS scholarships,
    SUM(t.ssd_percentage) AS total_percentage,
    t.school_code,
    t.dept_name,
    t.prog_name
FROM (
    SELECT DISTINCT 
        sai.sai_enrollment_no,
        sai.sai_student_status,
        sm.scholarship_name || ' (' || ssd.ssd_percentage || '%)' AS scholarship_display,
        ssd.ssd_percentage,
        school_code,
        dept_name,
        prog_name
    FROM
        students_apply_info sai
    JOIN
        students_scholarships_details ssd 
        ON sai.sai_enrollment_no = ssd.SSD_SAI_ENROLLMENT_NO
    JOIN
        scholarship_type sm 
        ON ssd.SSD_TYPE = sm.SCHOLARSHIP_ID
    LEFT JOIN 
        schools ON sai.sai_sdpo_school_id = school_id
    LEFT JOIN 
        programs ON sai.sai_sdpo_prog_id = prog_id
    LEFT JOIN 
        departments ON sai.sai_sdpo_dept_id = dept_id
    WHERE
        sai.sai_student_status = 'Active'
        AND ssd.ssd_sess_id IS NOT NULL
        AND sai.sai_enrollment_no NOT IN (
            SELECT m_enrollment
            FROM pm_mentors
            WHERE m_sess_id = '".$effectiveSessionId."'
        )
        AND ssd.ssd_percentage >= 25";
                   


        $sql.= "  AND ssd.ssd_sess_id='".$effectiveSessionId."'";
        if(isset($school) and $school !=""){
            $sql.= "  AND sai_sdpo_school_id='".$school."'";
        }

        $sql .= ") t";
        
        $sql .= " GROUP BY 
    t.sai_enrollment_no, 
    t.sai_student_status,
    t.school_code,
    t.dept_name,
    t.prog_name";
    $sql .= " order by  total_percentage desc";


        // echo $sql;exit;
        $students = DB::select($sql);

        // Count of students
        return response()->json($students);
    }



    public function showGradebook($enrollment_no)
    {
        $parsedEnrollmentNo = str_replace('enrollment_no=', '', $enrollment_no);

       
        // Fetch gradebook data using the provided query
        $gradebook = DB::select("
            SELECT
                id,
                session_name,
                course,
                grade,
                gh_co_id,
                gh_co_sess_id,
                faculty_feedback,
                CASE WHEN grade='NA' THEN title || ' ( Feedback Required ) ' ELSE title END as title
            FROM (
                SELECT
                    gh_sai_enrollment_no AS id,
                    sess_name AS session_name,
                    gh_cd_name || ' ' || gh_course_code AS title,
                    gh_co_course_cr,
                    gh_cd_name || ' ' || gh_course_code AS course,
                    CASE
                        WHEN (SELECT COUNT(*) FROM faculty_evaluation fe
                              WHERE LOWER(fe.sai_enrollment_no) = LOWER(grade_history.gh_sai_enrollment_no)
                              AND fe_co_id = gh_co_id) = 0 AND faculty_feedback = 1 THEN 'NA'
                        ELSE gh_grade
                    END AS grade,
                    gh_co_id,
                    gh_co_sess_id,
                    faculty_feedback
                FROM grade_history
                LEFT JOIN student_courses ON sc_co_id = gh_co_id AND LOWER(sc_sai_enrollment_no) = LOWER(grade_history.gh_sai_enrollment_no)
                LEFT JOIN academic_sessions ON gh_co_sess_id = sess_id
                WHERE LOWER(gh_sai_enrollment_no) = LOWER(:enrollment_no)
                  AND ((GH_REV_APPROVAL_STATUS=3 AND GH_REV_APPROVAL_FSUBMIT=1) OR (gh_exam_fsubmit=1))
                  AND gh_status='E'
                  AND gh_co_id != 0
            )
            ORDER BY gh_co_sess_id ASC
        ", ['enrollment_no' => $parsedEnrollmentNo]);
    //    dd($gradebook);

        $sqlTimeslots = "select * from timetable_slots";
        $ressqlTimeslots = DB::select($sqlTimeslots);
        // return view('peer_mentorship.gradebook', compact('gradebook', 'enrollment_no','ressqlTimeslots'));
       return view('peer_mentorship.marking_course', compact('gradebook', 'enrollment_no','ressqlTimeslots'));
    }

    /**
     * CO_IDs (courses_offered) this enrollment already mentors in the given session.
     *
     * @return array<int, int|string>
     */
    private function getMentorAssignedCoIdsForSession(string $enrollmentNo, $sessionId): array
    {
        if ($sessionId === null || $sessionId === '') {
            return [];
        }

        $rows = DB::table('pm_mentors_details')
            ->whereRaw('LOWER(md_enrollment) = LOWER(?)', [$enrollmentNo])
            ->where('md_sess_id', $sessionId)
            ->whereNotNull('md_co_id')
            ->pluck('md_co_id');

        return $rows->map(function ($id) {
            return (int) $id;
        })->unique()->values()->all();
    }

    public function getGradebookDetails(Request $request)
    {
        $request->validate([
            'enrollment_no' => 'required|string',
            'sess_id' => 'nullable',
        ]);

        $parsedEnrollmentNo = str_replace('enrollment_no=', '', $request->enrollment_no);
        $sessionId = $request->input('sess_id');

        $gradebook = DB::select("
            SELECT
                id,
                session_name,
                course,
                grade,
                gh_co_id,
                gh_co_sess_id,
                faculty_feedback,
                CASE WHEN grade='NA' THEN title || ' ( Feedback Required ) ' ELSE title END as title
            FROM (
                SELECT
                    gh_sai_enrollment_no AS id,
                    sess_name AS session_name,
                    gh_cd_name || ' ' || gh_course_code AS title,
                    gh_co_course_cr,
                    gh_cd_name || ' ' || gh_course_code AS course,
                    CASE
                        WHEN (SELECT COUNT(*) FROM faculty_evaluation fe
                              WHERE LOWER(fe.sai_enrollment_no) = LOWER(grade_history.gh_sai_enrollment_no)
                              AND fe_co_id = gh_co_id) = 0 AND faculty_feedback = 1 THEN 'NA'
                        ELSE gh_grade
                    END AS grade,
                    gh_co_id,
                    gh_co_sess_id,
                    faculty_feedback
                FROM grade_history
                LEFT JOIN student_courses ON sc_co_id = gh_co_id AND LOWER(sc_sai_enrollment_no) = LOWER(grade_history.gh_sai_enrollment_no)
                LEFT JOIN academic_sessions ON gh_co_sess_id = sess_id
                WHERE LOWER(gh_sai_enrollment_no) = LOWER(:enrollment_no)
                  AND ((GH_REV_APPROVAL_STATUS=3 AND GH_REV_APPROVAL_FSUBMIT=1) OR (gh_exam_fsubmit=1))
                  AND gh_status='E'
                  AND gh_co_id != 0
            )
            ORDER BY gh_co_sess_id ASC
        ", ['enrollment_no' => $parsedEnrollmentNo]);

        $timeslots = DB::select("select * from timetable_slots");
        $mentorCoIds = $this->getMentorAssignedCoIdsForSession($parsedEnrollmentNo, $sessionId);

        return response()->json([
            'success' => true,
            'gradebook' => $gradebook,
            'timeslots' => $timeslots,
            'mentor_co_ids' => $mentorCoIds,
        ]);
    }



    public function makeMentored(Request $request)
    {


        $request->validate([
            'enrollment_no' => 'required|string',
            'sess_id' => 'required',
            // 'mentoring_type' => 'required|string',
           'course_id' => 'required|array',
            // 'course_id.*' => 'integer',
        ]);

        $enrollmentNo = str_replace('enrollment_no=', '', $request->enrollment_no);
        $courseIds = array_values(array_filter((array) $request->course_id, function ($v) {
            return $v !== null && $v !== '';
        }));
        $fromtiming = (array) $request->fromtiming;
        $totiming = (array) $request->totiming;
        $weekdays = (array) $request->weekdays;
        $remarks = $request->remarks;
        $m_monthly_hours = $request->hourse;
        $entryUserId = auth()->user()->id;
        $sessionId = $request->sess_id;

        if ($courseIds === []) {
            return response()->json(['error' => 'No courses selected.'], 422);
        }

        DB::beginTransaction();

        try {
            // pm_mentors.id → stored as pm_mentors_details.m_id and PM_MENTOR_TIMING.pmt_m_id
            $mId = DB::table('pm_mentors')
                ->whereRaw('LOWER(m_enrollment) = LOWER(?)', [$enrollmentNo])
                ->where('m_sess_id', $sessionId)
                ->value('id');

            if (! $mId) {
                $mId = DB::table('pm_mentors')->insertGetId([
                    'm_enrollment' => $enrollmentNo,
                    'm_entry_user_id' => $entryUserId,
                    'm_mentor_type' => 'academic',
                    'm_entry_datetime' => now(),
                    'm_monthly_hours' => $m_monthly_hours,
                    'm_sess_id' => $sessionId,
                    'M_COMMITTEE_REMARKS' => $remarks,
                    'm_status' => 'E',
                ]);
            }

            // One pm_mentors_details row per CO_ID; each row id → PM_MENTOR_TIMING.pmt_md_id
            $mdIdsForTiming = [];
            foreach ($courseIds as $courseId) {
                $mdId = DB::table('pm_mentors_details')
                    ->whereRaw('LOWER(md_enrollment) = LOWER(?)', [$enrollmentNo])
                    ->where('md_co_id', $courseId)
                    ->where('md_sess_id', $sessionId)
                    ->value('id');

                if (! $mdId) {
                    $courseOffered = DB::table('COURSES_OFFERED')
                        ->where('CO_ID', $courseId)
                        ->first();

                    if (! $courseOffered) {
                        continue;
                    }

                    $mdId = DB::table('pm_mentors_details')->insertGetId([
                        'm_id' => $mId,
                        'md_enrollment' => $enrollmentNo,
                        'md_co_id' => $courseId,
                        'md_course_id' => $courseOffered->co_course_id,
                        'md_time_alot' => 0,
                        'md_entry_datetime' => now(),
                        'md_sess_id' => $sessionId,
                        'md_status' => 'E',
                    ]);
                } else {
                    DB::table('pm_mentors_details')
                        ->where('id', $mdId)
                        ->update(['m_id' => $mId]);
                }

                $mdIdsForTiming[] = $mdId;
            }

            $mdIdsForTiming = array_values(array_unique(array_filter($mdIdsForTiming)));

            if ($mdIdsForTiming === []) {
                DB::rollBack();

                return response()->json(['error' => 'Could not resolve mentor course rows (check CO_ID / COURSES_OFFERED).'], 422);
            }

            foreach ($mdIdsForTiming as $mdId) {
                foreach ($weekdays as $i => $weekday) {
                    if (! isset($fromtiming[$i], $totiming[$i])) {
                        continue;
                    }

                    DB::table('PM_MENTOR_TIMING')->insertGetId([
                        'pmt_m_id' => $mId,
                        'pmt_md_id' => $mdId,
                        'PMT_FROM_TIME' => $fromtiming[$i],
                        'PMT_TO_TIME' => $totiming[$i],
                        'pmt_sess_id' => $sessionId,
                        'pmt_weekday' => $weekday,
                        'pmt_enrollment_no' => $enrollmentNo,
                    ]);
                }
            }

            DB::commit();

            return response()->json(['success' => 'Added'], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['error' => $e->getMessage()], 500);
        }



}

    public function makeAdminMentor(Request $request)
    {
        // dd($request->enrollment_no);
        $request->validate([
            'enrollment_no' => 'required|string',
            'sess_id' => 'required',
           
        ]);

        $enrollmentNo = str_replace('enrollment_no=', '', $request->enrollment_no) ;
        // $courseIds = $request->course_id;
        $mentoring_type = $request->mentoring_type;

        // dd($request->all());
       
        $remarks = $request->m_committee_remarks;
        // $m_monthly_hours = $request->hourse;
        $entryUserId = auth()->user()->id;
        $sessionId = $request->sess_id;
       // dd($request->all());


            $result = DB::table('pm_mentors')
                ->select('M_ENROLLMENT')
                ->whereRaw('LOWER(M_ENROLLMENT) = LOWER(?)', [$enrollmentNo])
                ->first();

            //   dd($result);

            if ($result) {
              //  dd('Mentore not added');
               
            } else {

                DB::table('pm_mentors')->insertGetId([
                    'm_enrollment' =>  $enrollmentNo,
                    'm_entry_user_id' => $entryUserId,
                    'm_mentor_type' => 'admin',
                    'm_entry_datetime' => now(),
                    // 'm_monthly_hours' =>  $m_monthly_hours,
                    'm_sess_id' => $sessionId,
                    'M_COMMITTEE_REMARKS' => $remarks,
                    'm_status' => 'E',
                ]);
                DB::commit();
            //  return  redirect('/admin-mentors-list');
            return response()->json(['success' => 'Added'], 200);
            }


    } 









public function mentorSlot(Request $request)
{
    $request->validate([
        'enrollment_no' => 'required|string',
        'sess_id' => 'nullable',
        'md_id' => 'array',
        'timing_id' => 'array',
        'weekdays' => 'array',
        'fromtiming' => 'array',
        'totiming' => 'array',
        'deleted_timing_ids' => 'array',
    ]);

    $enrollmentNo = str_replace('enrollment_no=', '', $request->enrollment_no);
    $sessionId = $request->input('sess_id') ?: DB::table('student_courses')->max('SC_CO_SESS_ID');

    $mId = DB::table('pm_mentors')
        ->whereRaw('LOWER(m_enrollment) = LOWER(?)', [$enrollmentNo])
        ->where('m_sess_id', $sessionId)
        ->value('id');

    if (! $mId) {
        return response()->json(['error' => 'Mentor record not found for this session.'], 422);
    }

    DB::beginTransaction();

    try {
        foreach ((array) $request->input('deleted_timing_ids', []) as $delId) {
            if (! $delId) {
                continue;
            }
            DB::table('pm_mentor_timing')
                ->where('id', $delId)
                ->whereRaw('LOWER(pmt_enrollment_no) = LOWER(?)', [$enrollmentNo])
                ->delete();
        }

        $mdIds = (array) $request->input('md_id', []);
        $timingIds = (array) $request->input('timing_id', []);
        $weekdays = (array) $request->input('weekdays', []);
        $fromtiming = (array) $request->input('fromtiming', []);
        $totiming = (array) $request->input('totiming', []);

        foreach ($weekdays as $i => $weekday) {
            if ($weekday === '' || $weekday === null) {
                continue;
            }

            $mdId = $mdIds[$i] ?? null;
            if (! $mdId) {
                continue;
            }

            $mdValid = DB::table('pm_mentors_details')
                ->where('id', $mdId)
                ->whereRaw('LOWER(md_enrollment) = LOWER(?)', [$enrollmentNo])
                ->exists();

            if (! $mdValid) {
                continue;
            }

            $payload = [
                'pmt_m_id' => $mId,
                'pmt_md_id' => $mdId,
                'PMT_FROM_TIME' => $fromtiming[$i] ?? null,
                'PMT_TO_TIME' => $totiming[$i] ?? null,
                'pmt_sess_id' => $sessionId,
                'pmt_weekday' => $weekday,
                'pmt_enrollment_no' => $enrollmentNo,
            ];

            $existingTimingId = $timingIds[$i] ?? null;
            if ($existingTimingId) {
                DB::table('pm_mentor_timing')
                    ->where('id', $existingTimingId)
                    ->whereRaw('LOWER(pmt_enrollment_no) = LOWER(?)', [$enrollmentNo])
                    ->update($payload);
            } else {
                DB::table('pm_mentor_timing')->insertGetId($payload);
            }
        }

        DB::commit();

        return response()->json(['success' => 'Saved'], 200);
    } catch (\Exception $e) {
        DB::rollBack();

        return response()->json(['error' => $e->getMessage()], 500);
    }
}


    public function markMentored(Request $request)
    {


        // Validate input
        $request->validate([
            'enrollment_no' => 'required|string',
            'mentoring_type' => 'required|string',
         // 'course_id' => 'required|array',
            'course_id.*' => 'integer',
        ]);
     //  dd($request);

    //  $parsedEnrollmentNo = str_replace('enrollment_no=', '', $request->enrollment_no);
        $enrollmentNo = str_replace('enrollment_no=', '', $request->enrollment_no) ;
        $courseIds = $request->course_id;
        $mentoring_type = $request->mentoring_type;
        $fromtiming =  $request->fromtiming;
        $totiming =  $request->totiming;
        $weekdays=  $request->weekdays;
        $remarks = $request->remarks;
        $entryUserId = auth()->user()->id;
        $maxSessionId = DB::table('student_courses')->max('SC_CO_SESS_ID');
        // dd($enrollmentNo );




            $result = DB::table('pm_mentors')
                ->select('M_ENROLLMENT')
                ->whereRaw('LOWER(M_ENROLLMENT) = LOWER(?)', [$enrollmentNo])
                ->first();

             //  dd($result);

            if ($result) {
              //  dd('Mentore not added');
                //echo "Student exists: ";
            } else {
                DB::table('pm_mentors')->insertGetId([
                    'm_enrollment' => $enrollmentNo,
                    'm_entry_user_id' => $entryUserId,
                    'm_mentor_type' => $mentoring_type,
                    'm_entry_datetime' => now(),
                    'm_sess_id' => $maxSessionId,
                    'M_COMMITTEE_REMARKS' => $remarks,
                    'm_status' => 'E',
                ]);
                DB::commit();
              // dd('Mentore addesd');
               // echo "Student does not exist.";
            }

            $pmd_id =0;
            $id = DB::table('pm_mentors_details')
                ->whereRaw('LOWER(md_enrollment) = LOWER(?)', [$enrollmentNo])
                ->where('md_co_id', $request->course_id)
                ->value('id');


           //     dd($id);
        //    $newId = 0;



                if ($id == null){

                   // dd($id);
                    if($request->course_id != null ){

                        $COURSES_OFFERED = DB::table('COURSES_OFFERED')
                            ->where('CO_ID', $request->course_id)
                            ->get();
                    

                    $newId = DB::table('pm_mentors_details')->insertGetId([
                        'md_enrollment' => $enrollmentNo,
                        'md_co_id' => $request->course_id,
                        'md_course_id' => $COURSES_OFFERED[0]->co_course_id,
                        'md_time_alot' => 0, // Default time allocation, can be updated later
                        'md_entry_datetime' => now(),
                        'md_sess_id'=> $maxSessionId,
                        'md_status' => 'Active',
                    ]);
               // }
                  $id = $newId ;
                  $pmd_id = $newId;

                DB::commit();
                //dd($newId);

                //    }
            }

        }else{

            $pmd_id = $id;


        }

            DB::beginTransaction();

            try {
                foreach ($weekdays as $i => $weekday) {
                    // Insert mentor timing details
                    $timingId = DB::table('PM_MENTOR_TIMING')->insertGetId([
                        'PMT_MD_ID' => $pmd_id,                      // $newId should be defined earlier
                        'PMT_FROM_TIME' => $fromtiming[$i],         // Assuming timing is an array of ['from' => 'time', 'to' => 'time']
                        'PMT_TO_TIME' => $totiming[$i],             // Provide proper 'to' timing
                        'pmt_sess_id'=> $maxSessionId,
                        'pmt_weekday' => $weekday,                     // Weekday value
                        'pmt_enrollment_no' => $enrollmentNo
                    ]);
                }




                // Commit the transaction
                DB::commit();
              // dd($timingId);
                // Debug or return the new mentor detail ID
               // dd($newId);
            }
            catch (\Exception $e) {
                // Rollback in case of an error
              //  DB::rollBack();

                // Handle the exception
                return response()->json(['error' => $e->getMessage()], 500);
            }


                //   $path = '/mentors_list';
                //   return URL::to($path);

                //   return route('/mentors_list');


                return  redirect('/mentors_list');



        //    return redirect()->back()->with('success', 'Mentor and courses added successfully!');
        // } catch (\Exception $e) {
        //     DB::rollBack();
        //     return redirect()->back()->withErrors(['error' => 'An error occurred while marking as mentored.']);
        // }

}
public function showMentorsPage()
{
    $currentSessionId = DB::table('student_courses')->max('SC_CO_SESS_ID');
    $timeslots = DB::select('SELECT * FROM timetable_slots');

    return view('peer_mentorship.get_mentor', compact('timeslots', 'currentSessionId'));
}



public function updateMentorType(Request $request)
{
        $request->validate([
            'enrollment' => 'required|string',
            'role' => 'required|in:academic,admin,both',
            'sess_id' => 'required',
            'remarks' => 'nullable|string|max:4000',
        ]);

        $enrollment = $request->input('enrollment');
        $sessionId = $request->input('sess_id');
        $remarks = $request->input('remarks');

        $update = [
            'm_mentor_type' => $request->input('role'),
        ];

        if ($remarks !== null && $remarks !== '') {
            $update['m_committee_remarks'] = $remarks;
        }

        $affected = DB::table('pm_mentors')
            ->whereRaw('LOWER(m_enrollment) = LOWER(?)', [$enrollment])
            ->where('m_sess_id', $sessionId)
            ->update($update);

        if ($affected === 0) {
            return response()->json(['success' => false, 'message' => 'No mentor row found for this session.'], 422);
        }

        return response()->json(['success' => true]);
}


public function searchCourses(Request $request)
{
    $searchTerm = $request->input('searchTerm');



    // Fetch courses based on the search term
    $courses = DB::table('courses_two')
    ->select('course_id', 'course_code', 'course_cd_name')
    ->whereRaw('LOWER(course_code) LIKE ?', ["%".strtolower($searchTerm)."%"])
    ->orWhereRaw('LOWER(course_cd_name) LIKE ?', ["%".strtolower($searchTerm)."%"])
    ->limit(20)
    ->get();


    return response()->json($courses);
}

public function scheduleInterview(Request $request)
{


$enrollment_no = $request->data['enrollmentNo'];
$interview_date = $request->data['is_interview_date'];
$interview_time = $request->data['is_interview_time'];
$sess_id = $request->data['sess_id'] ?? null;
$isReschedule = false;


    try {
        $existingSchedule = DB::table('interview_schedules')
            ->whereRaw('LOWER(is_enrollment_no) = LOWER(?)', [$enrollment_no]);

        if ($sess_id) {
            $existingSchedule->where('sess_id', $sess_id);
        }

        $existingSchedule = $existingSchedule->orderBy('id', 'desc')->first();
        $isReschedule = (bool) $existingSchedule;

        if ($existingSchedule) {
            DB::table('interview_schedules')
                ->where('id', $existingSchedule->id)
                ->update([
                    'is_interview_date' => $interview_date,
                    'is_interview_time' => $interview_time,
                    'sess_id' => $sess_id,
                ]);
        } else {
            DB::table('interview_schedules')->insert([
                'is_interview_date' => $interview_date,
                'is_interview_time' => $interview_time,
                'is_enrollment_no'  => $enrollment_no,
                'sess_id' => $sess_id,
                'is_created_at' => now(),
            ]);
        }
        // return response()->json(['success' => true]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ], 500);
    }



    $email = $enrollment_no . '@bnu.edu.pk';
    $subject = $isReschedule
        ? 'Peer Mentorship Interview Rescheduled'
        : 'Peer Mentorship Interview Scheduled';
    $emailTitle = $isReschedule ? 'Interview Rescheduled' : 'Interview Scheduled';
    $emailIntro = $isReschedule
        ? 'Your Peer Mentorship interview has been rescheduled. Please review the updated details below.'
        : 'Your Peer Mentorship interview has been scheduled. Please review the details below.';
    $dateLabel = $isReschedule ? 'New Date' : 'Date';
    $timeLabel = $isReschedule ? 'New Time' : 'Time';

    $message = "Peer Mentorship - {$emailTitle}\n"
        . "====================================\n\n"
        . "Dear Student,\n\n"
        . "{$emailIntro}\n\n"
        . "+----------------+------------------------------+\n"
        . "| {$dateLabel}          | {$interview_date}\n"
        . "| {$timeLabel}          | {$interview_time}\n"
        . "| Venue          | SLASS 002, Double Basement\n"
        . "+----------------+------------------------------+\n\n"
        . "Please be available on time. If you have any questions, contact the Peer Mentorship Team.\n\n"
        . "Regards,\n"
        . "Peer Mentorship Team";
    $sender = 'Peer Mentorship Team';
    
    try {
        DB::statement("BEGIN send_mail_one_by_one(:to, :subject, :message, :sender); END;", [
            'to' => $email,
            'subject' => $subject,
            'message' => $message,
            'sender' => $sender
        ]);
        return response()->json([
            'success' => true,
            'message' => 'success: '
        ], 200);


    } catch (\Exception $e) {
        // Log::error('Email Procedure Error: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ], 500);
    }







}


public function markInterviewedSchedule(Request $request)
{
    $request->validate([
        'enrollment_no' => 'required|string',
        'sess_id' => 'required',
        'remarks' => 'required|string',
    ]);

    $schedule = DB::table('interview_schedules')
        ->whereRaw('LOWER(is_enrollment_no) = LOWER(?)', [$request->enrollment_no])
        ->where('sess_id', $request->sess_id)
        ->orderBy('id', 'desc')
        ->first();

    if (!$schedule) {
        return response()->json([
            'success' => false,
            'message' => 'Interview schedule not found for the selected session.',
        ], 404);
    }

    DB::table('interview_schedules')
        ->where('id', $schedule->id)
        ->update([
            'interviewed' => 1,
            'is_remarks' => $request->remarks,
        ]);

    return response()->json([
        'success' => true,
        'message' => 'Interview marked as interviewed successfully.',
    ]);
}


public function be_mentors_list_courses(Request $request)
{
    $enrollment_no = $request->md_enrollment_no;



    $timetable = DB::table('student_courses')
    ->select(
        'course_cd_name',
        DB::raw('get_teachername(sc_co_id) AS teachername'),
        'tt_subject_day',
        'tt_subject_time_from',
        'tt_subject_time_to',
        'sc_sai_enrollment_no'
    )
    ->leftJoin('courses_offered', 'co_id', '=', 'sc_co_id')
    ->leftJoin('timetable', function ($join) {
        $join->on('tt_co_id', '=', 'co_id')
             ->orOn('tt_co_id', '=', 'parent_co_id');
    })
    ->leftJoin('courses_two', 'course_id', '=', 'co_course_id')
    ->leftJoin('ACADEMIC_SESSIONS', 'sess_id', '=', 'sc_co_sess_id')
    ->whereNotIn('sc_co_id', function ($query) use ($enrollment_no) {
        $query->select('gh_co_id')
              ->from('grade_history')
              ->where('gh_sai_enrollment_no', $enrollment_no)
              ->whereNotNull('gh_co_id')
              ->where('gh_co_id', '>', 0)
              ->where('gh_exam_fsubmit', 1);
    })
    ->where('sc_sai_enrollment_no', $enrollment_no)
    ->where('sc_enrollment_status', 1)
    ->whereIn('sc_status', ['Approve', 'A'])
    ->where('co_status', 'E')
    ->get();


    $data = DB::table('PM_MENTORS')
        ->select(
            'PM_MENTORS.m_enrollment as enrollment_no',
            'PM_MENTORS.m_sess_id',
            'academic_sessions.sess_code',
            'PM_MENTORS.m_mentor_type',
            DB::raw('get_course_name(PM_MENTORS_DETAILS.md_co_id) as course_title'),
            'PM_MENTORS_DETAILS.credit_hrs',
            'PM_MENTORS_DETAILS.md_co_id',
            DB::raw("(
                SELECT gh_grade
                FROM grade_history
                WHERE gh_sai_enrollment_no = PM_MENTORS.m_enrollment
                  AND gh_co_id = PM_MENTORS_DETAILS.md_co_id
                  AND ROWNUM = 1
            ) AS course_grade")
        )
        ->leftJoin('PM_MENTORS_DETAILS', 'PM_MENTORS.id', '=', 'PM_MENTORS_DETAILS.m_id')
        ->leftJoin('academic_sessions', 'PM_MENTORS.m_sess_id', '=', 'academic_sessions.sess_id')
        ->where('PM_MENTORS.m_enrollment', '=', $enrollment_no)
        ->whereNotNull('PM_MENTORS_DETAILS.md_co_id')
        ->get();

    return response()->json([
        'success' => true,
        'courses' => $data,
        'timetable' => $timetable
    ]);
}

public function getMentorsData(Request $request)
{
        $maxSessionId = DB::table('student_courses')->max('SC_CO_SESS_ID');
        $sessionInput = $request->input('sess_id');
        $effectiveSessionId = is_numeric($sessionInput) ? (int) $sessionInput : (int) $maxSessionId;

        if (! $effectiveSessionId) {
            return response()->json(['mentors' => []]);
        }

        $schoolId = $request->input('school_id');
        $courseId = $request->input('course_id');

        $bindings = [$effectiveSessionId];

        $mentorsSql = "
        SELECT
            LISTAGG(pm_mentors_details.md_course_id, ',') WITHIN GROUP (ORDER BY pm_mentors_details.md_course_id) AS course_ids,
            pm_mentors.m_enrollment,
            sisusers.full_name,
            pm_mentors.m_committee_remarks,
            pm_mentors.m_mentor_type,
            sisusers.programs,
            sisusers.department
        FROM pm_mentors
        JOIN sisusers
            ON LOWER(sisusers.username) = LOWER(pm_mentors.m_enrollment)
        LEFT JOIN pm_mentors_details
            ON LOWER(pm_mentors_details.md_enrollment) = LOWER(pm_mentors.m_enrollment)
        WHERE
            (pm_mentors.m_be_mentor_approval IS NULL
            OR pm_mentors.m_be_mentor_approval = 'Approved')
            AND pm_mentors.m_sess_id = ?
            AND pm_mentors.m_mentor_type IN ('academic','both')
        ";

        if ($schoolId !== null && $schoolId !== '') {
            $mentorsSql .= "
            AND EXISTS (
                SELECT 1
                FROM students_apply_info sai_sch
                WHERE LOWER(sai_sch.sai_enrollment_no) = LOWER(pm_mentors.m_enrollment)
                  AND sai_sch.sai_sdpo_school_id = ?
            )";
            $bindings[] = $schoolId;
        }

        if (! empty($courseId)) {
            $mentorsSql .= "
            AND EXISTS (
                SELECT 1
                FROM pm_mentors_details pmdc
                WHERE LOWER(pmdc.md_enrollment) = LOWER(pm_mentors.m_enrollment)
                  AND LOWER(pmdc.md_course_id) LIKE LOWER(?)
            )";
            $bindings[] = '%'.$courseId.'%';
        }

        $mentorsSql .= "
        GROUP BY
            pm_mentors.m_enrollment,
            sisusers.full_name,
            pm_mentors.m_committee_remarks,
            pm_mentors.m_mentor_type,
            sisusers.programs,
            sisusers.department
    ";

        $mentors = DB::select($mentorsSql, $bindings);

        return response()->json(['mentors' => $mentors]);
}






    public function showAdminMentors()
    {
        $currentSessionId = DB::table('student_courses')->max('SC_CO_SESS_ID');
        $timeslots = DB::select('SELECT * FROM timetable_slots');

        return view('peer_mentorship.admin_get_mentor', compact('currentSessionId', 'timeslots'));
    }

    public function getAdminMentorsList(Request $request)
    {
        $maxSessionId = DB::table('student_courses')->max('SC_CO_SESS_ID');
        $sessionInput = $request->input('sess_id');
        $effectiveSessionId = is_numeric($sessionInput) ? (int) $sessionInput : (int) $maxSessionId;

        if (! $effectiveSessionId) {
            return response()->json(['mentors' => []]);
        }

        $schoolId = $request->input('school_id');
        $bindings = [$effectiveSessionId];

        $mentorsSql = "
            SELECT
                pm_mentors.m_enrollment,
                sisusers.full_name,
                pm_mentors.m_committee_remarks,
                pm_mentors.m_mentor_type,
                sisusers.department,
                CASE
                    WHEN EXISTS (
                        SELECT 1
                        FROM pm_mentor_staff_assign pmsa
                        INNER JOIN pm_mentors pm_sa ON pm_sa.id = pmsa.pmsa_m_id
                            AND pm_sa.m_sess_id = pmsa.pmsa_sess_id
                        WHERE LOWER(pm_sa.m_enrollment) = LOWER(pm_mentors.m_enrollment)
                          AND pm_sa.m_sess_id = pm_mentors.m_sess_id
                          AND LOWER(TRIM(pmsa.pmsa_status)) = 'assigned'
                    ) THEN 'assigned'
                    ELSE 'non assigned'
                END AS staff_assign_status
            FROM pm_mentors
            JOIN sisusers ON LOWER(sisusers.username) = LOWER(pm_mentors.m_enrollment)
            WHERE (pm_mentors.m_be_mentor_approval IS NULL OR pm_mentors.m_be_mentor_approval = 'Approved')
            AND pm_mentors.m_mentor_type IN ('admin', 'both')
            AND pm_mentors.m_sess_id = ?
        ";

        if ($schoolId !== null && $schoolId !== '') {
            $mentorsSql .= "
            AND EXISTS (
                SELECT 1
                FROM students_apply_info sai_sch
                WHERE LOWER(sai_sch.sai_enrollment_no) = LOWER(pm_mentors.m_enrollment)
                  AND sai_sch.sai_sdpo_school_id = ?
            )";
            $bindings[] = $schoolId;
        }

        $mentors = DB::select($mentorsSql, $bindings);

        return response()->json(['mentors' => $mentors]);
    }

    public function searchPmEmployees(Request $request)
    {
        $term = trim((string) $request->input('q', $request->input('term', '')));
        if (strlen($term) < 2) {
            return response()->json(['results' => []]);
        }

        $like = '%'.strtolower($term).'%';
        $rows = DB::select("
            SELECT
                emp.employee_id,
                emp.prefix,
                emp.emp_f_name,
                emp.emp_l_name,
                dept.dept_name,
                sch.school_code
            FROM emp_employees emp
            LEFT JOIN schools sch ON sch.school_id = emp.emp_school_id
            LEFT JOIN departments dept ON dept.dept_id = emp.emp_dept_id
            WHERE emp.emp_status = 'E'
            AND (
                LOWER(emp.emp_f_name || ' ' || emp.emp_l_name) LIKE ?
                OR LOWER(TO_CHAR(emp.employee_id)) LIKE ?
                OR LOWER(NVL(emp.prefix, '')) LIKE ?
            )
            AND ROWNUM <= 25
            ORDER BY emp.emp_f_name, emp.emp_l_name
        ", [$like, $like, $like]);

        $results = array_map(function ($row) {
            $name = trim(($row->prefix ?? '').' '.($row->emp_f_name ?? '').' '.($row->emp_l_name ?? ''));
            $meta = trim(($row->school_code ?? '').' · '.($row->dept_name ?? ''));

            return [
                'id' => (int) $row->employee_id,
                'text' => $name.' ('.$row->employee_id.')'.($meta ? ' — '.$meta : ''),
            ];
        }, $rows);

        return response()->json(['results' => $results]);
    }

    public function getAdminMentorAssignments(Request $request)
    {
        $adminEnrollment = $request->input('mentor_enrollment');
        $maxSessionId = DB::table('student_courses')->max('SC_CO_SESS_ID');
        $sessionInput = $request->input('sess_id');
        $sessionId = is_numeric($sessionInput) ? (int) $sessionInput : (int) $maxSessionId;

        if (! $adminEnrollment || ! $sessionId) {
            return response()->json(['assignments' => []]);
        }

        $mId = DB::table('pm_mentors')
            ->whereRaw('LOWER(m_enrollment) = LOWER(?)', [$adminEnrollment])
            ->where('m_sess_id', $sessionId)
            ->value('id');

        if (! $mId) {
            return response()->json(['assignments' => []]);
        }

        $rows = DB::select("
            SELECT
                pmsa.id AS md_id,
                pmsa.pmsa_enrollment AS enrollment,
                pmsa.pmsa_emp_id AS employee_id,
                pmsa.pmsa_status,
                pmsa.pmsa_description AS md_description,
                emp.prefix,
                emp.emp_f_name,
                emp.emp_l_name,
                dept.dept_name,
                sch.school_code,
                pmt.id AS timing_id,
                pmt.pmt_weekday,
                wd.day_name AS week_day,
                ts_from.ts_name AS from_time,
                ts_to.ts_name AS to_time,
                pmt.pmt_from_time AS from_time_id,
                pmt.pmt_to_time AS to_time_id,
                (
                    SELECT fb.pmsf_remarks
                    FROM pm_session_feedback fb
                    WHERE fb.pmsf_pms_id = pmsa.id
                      AND LOWER(TRIM(fb.pmsf_feedback_from)) = 'admin'
                    ORDER BY fb.pmsf_entry_datetime DESC NULLS LAST, fb.id DESC
                    FETCH FIRST 1 ROW ONLY
                ) AS feedback_remarks,
                (
                    SELECT fb.pmsf_satisfaction
                    FROM pm_session_feedback fb
                    WHERE fb.pmsf_pms_id = pmsa.id
                      AND LOWER(TRIM(fb.pmsf_feedback_from)) = 'admin'
                    ORDER BY fb.pmsf_entry_datetime DESC NULLS LAST, fb.id DESC
                    FETCH FIRST 1 ROW ONLY
                ) AS feedback_satisfaction,
                (
                    SELECT fb.pmsf_entry_datetime
                    FROM pm_session_feedback fb
                    WHERE fb.pmsf_pms_id = pmsa.id
                      AND LOWER(TRIM(fb.pmsf_feedback_from)) = 'admin'
                    ORDER BY fb.pmsf_entry_datetime DESC NULLS LAST, fb.id DESC
                    FETCH FIRST 1 ROW ONLY
                ) AS feedback_entry_datetime
            FROM pm_mentor_staff_assign pmsa
            LEFT JOIN emp_employees emp ON emp.employee_id = pmsa.pmsa_emp_id
            LEFT JOIN departments dept ON dept.dept_id = emp.emp_dept_id
            LEFT JOIN schools sch ON sch.school_id = emp.emp_school_id
            LEFT JOIN pm_mentor_timing pmt ON pmt.pmt_pmsa_id = pmsa.id AND pmt.pmt_sess_id = pmsa.pmsa_sess_id
            LEFT JOIN week_days wd ON wd.id = pmt.pmt_weekday
            LEFT JOIN timetable_slots ts_from ON ts_from.ts_id = pmt.pmt_from_time
            LEFT JOIN timetable_slots ts_to ON ts_to.ts_id = pmt.pmt_to_time
            WHERE pmsa.pmsa_m_id = ?
              AND pmsa.pmsa_sess_id = ?
              AND LOWER(TRIM(pmsa.pmsa_status)) IN ('assigned', 'non assigned')
            ORDER BY emp.emp_f_name, emp.emp_l_name, pmsa.id, pmt.id
        ", [$mId, $sessionId]);

        $grouped = [];
        foreach ($rows as $row) {
            $key = (string) $row->md_id;
            if (! isset($grouped[$key])) {
                $empName = trim(($row->prefix ?? '').' '.($row->emp_f_name ?? '').' '.($row->emp_l_name ?? ''));
                $grouped[$key] = [
                    'md_id' => $row->md_id,
                    'enrollment' => $row->enrollment,
                    'employee_id' => $row->employee_id,
                    'employee_name' => $empName,
                    'dept_name' => $row->dept_name,
                    'school_code' => $row->school_code,
                    'description' => $row->md_description,
                    'status' => $this->normalizePmsaStatus($row->pmsa_status ?? null),
                    'feedback_remarks' => $row->feedback_remarks,
                    'feedback_satisfaction' => $row->feedback_satisfaction
                        ? strtolower(trim((string) $row->feedback_satisfaction))
                        : null,
                    'feedback_entry_datetime' => $row->feedback_entry_datetime,
                    'slots' => [],
                ];
            }
            if ($row->timing_id) {
                $grouped[$key]['slots'][] = [
                    'timing_id' => $row->timing_id,
                    'week_day' => $row->week_day,
                    'pmt_weekday' => $row->pmt_weekday,
                    'from_time' => $row->from_time,
                    'to_time' => $row->to_time,
                    'from_time_id' => $row->from_time_id,
                    'to_time_id' => $row->to_time_id,
                ];
            }
        }

        return response()->json(['assignments' => array_values($grouped)]);
    }

    public function updateAdminMentorAssignmentStatus(Request $request)
    {
        $request->validate([
            'assignment_id' => 'required|integer',
            'admin_mentor_enrollment' => 'required|string',
            'sess_id' => 'required',
            'status' => 'required|in:assigned,non assigned',
        ]);

        $assignmentId = (int) $request->input('assignment_id');
        $adminEnrollment = trim((string) $request->input('admin_mentor_enrollment'));
        $sessionId = (int) $request->input('sess_id');
        $newStatus = strtolower(trim((string) $request->input('status')));

        $mId = DB::table('pm_mentors')
            ->whereRaw('LOWER(m_enrollment) = LOWER(?)', [$adminEnrollment])
            ->where('m_sess_id', $sessionId)
            ->value('id');

        if (! $mId) {
            return response()->json(['message' => 'Admin mentor record not found for this session.'], 404);
        }

        $assignment = DB::table('pm_mentor_staff_assign')
            ->where('id', $assignmentId)
            ->where('pmsa_m_id', $mId)
            ->where('pmsa_sess_id', $sessionId)
            ->first();

        if (! $assignment) {
            return response()->json(['message' => 'Assignment not found.'], 404);
        }

        DB::table('pm_mentor_staff_assign')
            ->where('id', $assignmentId)
            ->update(['pmsa_status' => $newStatus]);

        return response()->json([
            'success' => true,
            'message' => $newStatus === self::PMSA_STATUS_ASSIGNED
                ? 'Employee mentor marked as assigned.'
                : 'Employee mentor marked as non assigned.',
            'status' => $newStatus,
        ]);
    }

    public function showMyAssignedMentors()
    {
        $employeeId = $this->resolveLoggedInEmployeeId();
        if (! $employeeId) {
            abort(403, 'Employee login required to view assigned mentors.');
        }

        $currentSessionId = DB::table('student_courses')->max('SC_CO_SESS_ID');

        return view('peer_mentorship.my_assigned_mentors', compact('currentSessionId', 'employeeId'));
    }

    public function getMyAssignedMentorsList(Request $request)
    {
        $employeeId = $this->resolveLoggedInEmployeeId();
        if (! $employeeId) {
            return response()->json(['message' => 'Employee login required.'], 403);
        }

        $maxSessionId = DB::table('student_courses')->max('SC_CO_SESS_ID');
        $sessionInput = $request->input('sess_id');
        $effectiveSessionId = is_numeric($sessionInput) ? (int) $sessionInput : (int) $maxSessionId;

        if (! $effectiveSessionId) {
            return response()->json(['mentors' => [], 'count' => 0]);
        }

        $rows = DB::select("
            SELECT
                pmsa.id AS assignment_id,
                pmsa.pmsa_enrollment AS mentor_enrollment,
                pmsa.pmsa_status,
                pmsa.pmsa_description,
                pmsa.pmsa_entry_datetime,
                pm.m_mentor_type,
                pm.m_committee_remarks,
                su.full_name AS mentor_student_name,
                su.programs AS mentor_program,
                su.department AS mentor_department,
                pmt.id AS timing_id,
                pmt.pmt_weekday,
                wd.day_name AS week_day,
                ts_from.ts_name AS from_time,
                ts_to.ts_name AS to_time
            FROM pm_mentor_staff_assign pmsa
            JOIN pm_mentors pm ON pm.id = pmsa.pmsa_m_id AND pm.m_sess_id = pmsa.pmsa_sess_id
            LEFT JOIN sisusers su ON LOWER(su.username) = LOWER(pmsa.pmsa_enrollment)
            LEFT JOIN pm_mentor_timing pmt ON pmt.pmt_pmsa_id = pmsa.id AND pmt.pmt_sess_id = pmsa.pmsa_sess_id
            LEFT JOIN week_days wd ON wd.id = pmt.pmt_weekday
            LEFT JOIN timetable_slots ts_from ON ts_from.ts_id = pmt.pmt_from_time
            LEFT JOIN timetable_slots ts_to ON ts_to.ts_id = pmt.pmt_to_time
            WHERE pmsa.pmsa_emp_id = ?
              AND pmsa.pmsa_sess_id = ?
              AND LOWER(TRIM(pmsa.pmsa_status)) = ?
            ORDER BY su.full_name, pmsa.pmsa_enrollment, pmsa.id, pmt.id
        ", [$employeeId, $effectiveSessionId, self::PMSA_STATUS_ASSIGNED]);

        $grouped = [];
        foreach ($rows as $row) {
            $key = (string) $row->assignment_id;
            if (! isset($grouped[$key])) {
                $grouped[$key] = [
                    'assignment_id' => $row->assignment_id,
                    'mentor_enrollment' => $row->mentor_enrollment,
                    'mentor_student_name' => $row->mentor_student_name,
                    'mentor_program' => $row->mentor_program,
                    'mentor_department' => $row->mentor_department,
                    'mentor_type' => $row->m_mentor_type,
                    'committee_remarks' => $row->m_committee_remarks,
                    'description' => $row->pmsa_description,
                    'status' => $this->normalizePmsaStatus($row->pmsa_status ?? null),
                    'entry_datetime' => $row->pmsa_entry_datetime,
                    'slots' => [],
                ];
            }
            if ($row->timing_id) {
                $grouped[$key]['slots'][] = [
                    'week_day' => $row->week_day,
                    'from_time' => $row->from_time,
                    'to_time' => $row->to_time,
                ];
            }
        }

        $mentors = array_values($grouped);

        return response()->json([
            'mentors' => $mentors,
            'count' => count($mentors),
        ]);
    }

    public function getStaffMentorRequestStatus(Request $request)
    {
        $employeeId = $this->resolveLoggedInEmployeeId();
        if (! $employeeId) {
            return response()->json(['message' => 'Employee login required.'], 403);
        }

        $maxSessionId = (int) DB::table('student_courses')->max('SC_CO_SESS_ID');
        $sessionInput = $request->input('sess_id');
        $effectiveSessionId = is_numeric($sessionInput) ? (int) $sessionInput : $maxSessionId;

        if (! $effectiveSessionId) {
            return response()->json([
                'can_submit' => false,
                'has_pending' => false,
                'pending_request' => null,
                'session_id' => null,
            ]);
        }

        $pendingRequest = $this->findPendingStaffMentorRequest($employeeId, $effectiveSessionId);

        return response()->json([
            'can_submit' => $pendingRequest === null,
            'has_pending' => $pendingRequest !== null,
            'pending_request' => $pendingRequest,
            'session_id' => $effectiveSessionId,
            'requester_type' => $this->resolveStaffFacultyRequestType(),
        ]);
    }

    public function getMyStaffMentorRequests(Request $request)
    {
        $employeeId = $this->resolveLoggedInEmployeeId();
        if (! $employeeId) {
            return response()->json(['message' => 'Employee login required.'], 403);
        }

        $employeeKey = (string) $employeeId;

        $rows = DB::table('pm_mentors_request as mr')
            ->join('pm_mentors_request_details as mrd', 'mrd.mrd_id', '=', 'mr.id')
            ->leftJoin('academic_sessions as acs', 'acs.sess_id', '=', 'mr.mr_sess_id')
            ->where('mr.mr_enrollment', $employeeKey)
            ->whereNotNull('mr.mr_staff_faculty_request')
            ->select(
                'mr.id as request_id',
                'mr.mr_sess_id as session_id',
                'acs.sess_name as session_name',
                'mr.mr_staff_faculty_request as status',
                'mr.mr_requested_by as requested_by',
                'mr.mr_entry_datetime as request_date',
                'mrd.mrd_student_request as remarks'
            )
            ->orderByDesc('mr.mr_entry_datetime')
            ->get();

        return response()->json([
            'requests' => $rows,
            'count' => $rows->count(),
        ]);
    }

    public function submitStaffMentorRequest(Request $request)
    {
        $employeeId = $this->resolveLoggedInEmployeeId();
        if (! $employeeId) {
            return response()->json(['message' => 'Employee login required.'], 403);
        }

        $request->validate([
            'remarks' => 'required|string|min:10|max:200',
            'sess_id' => 'nullable',
        ]);

        $maxSessionId = (int) DB::table('student_courses')->max('SC_CO_SESS_ID');
        $sessionInput = $request->input('sess_id');
        $effectiveSessionId = is_numeric($sessionInput) ? (int) $sessionInput : $maxSessionId;

        if (! $effectiveSessionId) {
            return response()->json(['message' => 'Session could not be determined.'], 422);
        }

        if ($this->findPendingStaffMentorRequest($employeeId, $effectiveSessionId)) {
            return response()->json([
                'message' => 'Your previous request is still pending. You can submit a new request once it has been completed.',
            ], 422);
        }

        $employeeKey = (string) $employeeId;
        $requestedBy = $this->resolveStaffFacultyRequestType();
        $remarks = trim((string) $request->input('remarks'));
        $currentDatetime = now('Asia/Karachi');

        DB::beginTransaction();

        try {
            $requestId = DB::table('pm_mentors_request')->insertGetId([
                'mr_enrollment' => $employeeKey,
                'mr_entry_user_id' => $employeeId,
                'mr_sess_id' => $effectiveSessionId,
                'mr_status' => 'E',
                'mr_staff_faculty_request' => self::STAFF_REQUEST_STATUS_PENDING,
                'mr_requested_by' => $requestedBy,
                'mr_entry_datetime' => $currentDatetime,
            ]);

            DB::table('pm_mentors_request_details')->insert([
                'mrd_id' => $requestId,
                'mrd_enrollment' => $employeeKey,
                'mrd_sess_id' => $effectiveSessionId,
                'mrd_co_id' => $requestId,
                'mrd_course_id' => 0,
                'mrd_student_request' => $remarks,
                'mrd_status' => 'E',
                'mrd_requested_by' => $requestedBy,
                'mrd_entry_datetime' => $currentDatetime,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Mentor request submitted successfully.',
                'request_id' => $requestId,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Request submit failed. Please try again.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    private function resolveStaffFacultyRequestType(): string
    {
        $groupId = (int) (auth()->user()->group_id ?? 0);

        return $groupId === self::FACULTY_GROUP_ID ? 'faculty' : 'staff';
    }

    /**
     * @return array<string, mixed>|null
     */
    private function findPendingStaffMentorRequest(int $employeeId, int $sessionId): ?array
    {
        $employeeKey = (string) $employeeId;

        $row = DB::table('pm_mentors_request as mr')
            ->leftJoin('pm_mentors_request_details as mrd', 'mrd.mrd_id', '=', 'mr.id')
            ->where('mr.mr_enrollment', $employeeKey)
            ->where('mr.mr_sess_id', $sessionId)
            ->whereRaw('LOWER(mr.mr_staff_faculty_request) = ?', [strtolower(self::STAFF_REQUEST_STATUS_PENDING)])
            ->select(
                'mr.id as request_id',
                'mr.mr_sess_id as session_id',
                'mr.mr_staff_faculty_request as status',
                'mr.mr_requested_by as requested_by',
                'mr.mr_entry_datetime as request_date',
                'mrd.mrd_student_request as remarks'
            )
            ->orderByDesc('mr.id')
            ->first();

        if (! $row) {
            return null;
        }

        return [
            'request_id' => $row->request_id,
            'session_id' => $row->session_id,
            'status' => $row->status,
            'requested_by' => $row->requested_by,
            'request_date' => $row->request_date,
            'remarks' => $row->remarks,
        ];
    }

    public function getAdminStaffMentorRequests(Request $request)
    {
        $maxSessionId = (int) DB::table('student_courses')->max('SC_CO_SESS_ID');
        $sessionInput = $request->input('sess_id');
        $effectiveSessionId = is_numeric($sessionInput) ? (int) $sessionInput : $maxSessionId;

        if (! $effectiveSessionId) {
            return response()->json([
                'requests' => [],
                'pending_count' => 0,
                'completed_count' => 0,
            ]);
        }

        $statusFilter = strtolower(trim((string) $request->input('status', 'pending')));
        $statusValue = $statusFilter === 'completed'
            ? self::STAFF_REQUEST_STATUS_COMPLETED
            : self::STAFF_REQUEST_STATUS_PENDING;

        $rows = DB::select("
            SELECT
                mr.id AS request_id,
                mr.mr_enrollment AS employee_id,
                mr.mr_requested_by AS request_type,
                mr.mr_staff_faculty_request AS status,
                mr.mr_entry_datetime AS request_date,
                mrd.mrd_student_request AS description,
                TRIM(NVL(emp.prefix, '') || ' ' || NVL(emp.emp_f_name, '') || ' ' || NVL(emp.emp_l_name, '')) AS employee_name,
                dept.dept_name AS department,
                acs.sess_name AS session_name,
                (
                    SELECT pmsa.pmsa_enrollment
                    FROM pm_mentor_staff_assign pmsa
                    WHERE pmsa.pmsa_emp_id = TO_NUMBER(mr.mr_enrollment)
                      AND pmsa.pmsa_sess_id = mr.mr_sess_id
                      AND pmsa.pmsa_entry_datetime >= mr.mr_entry_datetime
                    ORDER BY pmsa.id DESC
                    FETCH FIRST 1 ROWS ONLY
                ) AS assigned_mentor_enrollment,
                (
                    SELECT su.full_name
                    FROM pm_mentor_staff_assign pmsa
                    INNER JOIN sisusers su ON LOWER(su.username) = LOWER(pmsa.pmsa_enrollment)
                    WHERE pmsa.pmsa_emp_id = TO_NUMBER(mr.mr_enrollment)
                      AND pmsa.pmsa_sess_id = mr.mr_sess_id
                      AND pmsa.pmsa_entry_datetime >= mr.mr_entry_datetime
                    ORDER BY pmsa.id DESC
                    FETCH FIRST 1 ROWS ONLY
                ) AS assigned_mentor_name
            FROM pm_mentors_request mr
            INNER JOIN pm_mentors_request_details mrd ON mrd.mrd_id = mr.id
            LEFT JOIN emp_employees emp ON TO_CHAR(emp.employee_id) = mr.mr_enrollment
            LEFT JOIN departments dept ON dept.dept_id = emp.emp_dept_id
            LEFT JOIN academic_sessions acs ON acs.sess_id = mr.mr_sess_id
            WHERE mr.mr_staff_faculty_request IS NOT NULL
              AND mr.mr_sess_id = ?
              AND LOWER(mr.mr_staff_faculty_request) = LOWER(?)
            ORDER BY mr.mr_entry_datetime DESC
        ", [$effectiveSessionId, $statusValue]);

        $pendingCount = (int) DB::table('pm_mentors_request')
            ->where('mr_sess_id', $effectiveSessionId)
            ->whereNotNull('mr_staff_faculty_request')
            ->whereRaw('LOWER(mr_staff_faculty_request) = ?', [strtolower(self::STAFF_REQUEST_STATUS_PENDING)])
            ->count();

        $completedCount = (int) DB::table('pm_mentors_request')
            ->where('mr_sess_id', $effectiveSessionId)
            ->whereNotNull('mr_staff_faculty_request')
            ->whereRaw('LOWER(mr_staff_faculty_request) = ?', [strtolower(self::STAFF_REQUEST_STATUS_COMPLETED)])
            ->count();

        return response()->json([
            'requests' => $rows,
            'count' => count($rows),
            'pending_count' => $pendingCount,
            'completed_count' => $completedCount,
            'session_id' => $effectiveSessionId,
        ]);
    }

    /**
     * @return array{success:bool, message?:string}
     */
    private function completeStaffMentorRequest(int $requestId, ?string $assignedMentorEnrollment = null): array
    {
        $requestRow = DB::table('pm_mentors_request')
            ->where('id', $requestId)
            ->whereNotNull('mr_staff_faculty_request')
            ->first();

        if (! $requestRow) {
            return ['success' => false, 'message' => 'Staff mentor request not found.'];
        }

        if (strtolower(trim((string) ($requestRow->mr_staff_faculty_request ?? ''))) !== strtolower(self::STAFF_REQUEST_STATUS_PENDING)) {
            return ['success' => false, 'message' => 'This request is not pending.'];
        }

        $update = [
            'mr_staff_faculty_request' => self::STAFF_REQUEST_STATUS_COMPLETED,
        ];

        if ($assignedMentorEnrollment !== null && trim($assignedMentorEnrollment) !== '') {
            $update['mr_committee_remarks'] = 'Assigned mentor: '.trim($assignedMentorEnrollment);
        }

        DB::table('pm_mentors_request')
            ->where('id', $requestId)
            ->update($update);

        return ['success' => true];
    }

    public function submitEmployeeMentorFeedback(Request $request)
    {
        $employeeId = $this->resolveLoggedInEmployeeId();
        if (! $employeeId) {
            return response()->json(['message' => 'Employee login required.'], 403);
        }

        $request->validate([
            'assignment_id' => 'required|integer',
            'sess_id' => 'required',
            'satisfaction' => 'required|in:satisfaction,no satisfaction',
            'remarks' => 'nullable|string|max:4000',
        ]);

        $assignmentId = (int) $request->input('assignment_id');
        $sessionId = (int) $request->input('sess_id');
        $satisfaction = strtolower(trim((string) $request->input('satisfaction')));
        $remarks = trim((string) $request->input('remarks', ''));

        $assignment = DB::table('pm_mentor_staff_assign')
            ->where('id', $assignmentId)
            ->where('pmsa_emp_id', $employeeId)
            ->where('pmsa_sess_id', $sessionId)
            ->first();

        if (! $assignment) {
            return response()->json(['message' => 'Assignment not found for this session.'], 404);
        }

        if (strtolower(trim((string) ($assignment->pmsa_status ?? ''))) !== self::PMSA_STATUS_ASSIGNED) {
            return response()->json(['message' => 'Feedback can only be submitted for assigned mentors.'], 422);
        }

        DB::beginTransaction();

        try {
            DB::table('pm_session_feedback')->insert([
                'pmsf_pms_id' => $assignmentId,
                'pmsf_remarks' => $remarks !== '' ? $remarks : null,
                'pmsf_satisfaction' => $satisfaction,
                'pmsf_feedback_from' => self::PMSF_FEEDBACK_FROM_ADMIN,
            ]);

            DB::table('pm_mentor_staff_assign')
                ->where('id', $assignmentId)
                ->update(['pmsa_status' => self::PMSA_STATUS_NON_ASSIGNED]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Feedback submitted successfully. Assignment marked as non assigned.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    private function resolveLoggedInEmployeeId(): ?int
    {
        $user = auth()->user();
        if (! $user) {
            return null;
        }

        $employeeId = $user->employee_id ?? null;
        if ($employeeId) {
            return (int) $employeeId;
        }

        $employee = $user->employees ?? null;
        if ($employee && ! empty($employee->employee_id)) {
            return (int) $employee->employee_id;
        }

        return null;
    }

    public function getPmStudentBasicInfo(Request $request)
    {
        $employeeId = $this->resolveLoggedInEmployeeId();
        if (! $employeeId) {
            return response()->json(['message' => 'Employee login required.'], 403);
        }

        $enrollment = trim((string) $request->input('enrollment', ''));
        if ($enrollment === '') {
            return response()->json(['message' => 'Enrollment is required.'], 422);
        }

        $maxSessionId = DB::table('student_courses')->max('SC_CO_SESS_ID');
        $sessionInput = $request->input('sess_id');
        $effectiveSessionId = is_numeric($sessionInput) ? (int) $sessionInput : (int) $maxSessionId;

        $isAssigned = DB::table('pm_mentor_staff_assign')
            ->where('pmsa_emp_id', $employeeId)
            ->where('pmsa_sess_id', $effectiveSessionId)
            ->whereRaw('LOWER(pmsa_enrollment) = LOWER(?)', [$enrollment])
            ->whereIn('pmsa_status', [self::PMSA_STATUS_ASSIGNED, self::PMSA_STATUS_NON_ASSIGNED])
            ->exists();

        if (! $isAssigned) {
            return response()->json(['message' => 'This mentor is not assigned to you for the selected session.'], 403);
        }

        $student = DB::table('students_apply_info')
            ->leftJoin('students_personal_info', 'students_apply_info.sai_spi_id', '=', 'students_personal_info.spi_id')
            ->leftJoin('schools', 'students_apply_info.sai_sdpo_school_id', '=', 'schools.school_id')
            ->leftJoin('departments', 'students_apply_info.sai_sdpo_dept_id', '=', 'departments.dept_id')
            ->leftJoin('programs', 'students_apply_info.sai_sdpo_prog_id', '=', 'programs.prog_id')
            ->whereRaw('LOWER(students_apply_info.sai_enrollment_no) = LOWER(?)', [$enrollment])
            ->select(
                'students_apply_info.sai_enrollment_no as enrollment_no',
                'students_apply_info.sai_student_status as student_status',
                'students_personal_info.spi_name as student_name',
                'students_personal_info.spi_fathername as father_name',
                'students_personal_info.spi_mobile as mobile_number',
                'students_personal_info.spi_email as email',
                'schools.school_name as school_name',
                'departments.dept_name as department_name',
                'programs.prog_name as program_name',
                'programs.prog_degree_level as degree_level',
                DB::raw("(SELECT SSR_SEMESTER_STATUS FROM students_semester_record WHERE SAI_ENROLLMENT_NO = students_apply_info.sai_enrollment_no ORDER BY SESS_ID DESC FETCH FIRST 1 ROWS ONLY) as semester_status")
            )
            ->first();

        if (! $student) {
            return response()->json(['message' => 'Student record not found.'], 404);
        }

        return response()->json([
            'student' => [
                'enrollment_no' => $student->enrollment_no,
                'student_name' => $student->student_name,
                'student_status' => $student->student_status,
                'semester_status' => $student->semester_status,
                'father_name' => $student->father_name,
                'mobile_number' => $student->mobile_number,
                'email' => $student->email,
                'school_name' => $student->school_name,
                'department_name' => $student->department_name,
                'program_name' => $student->program_name,
                'degree_level' => $student->degree_level,
                'photo_url' => 'https://cms.bnu.edu.pk/students_pics/'.strtoupper($student->enrollment_no).'.jpg',
            ],
        ]);
    }

    public function assignAdminMentorStudent(Request $request)
    {
        $request->validate([
            'admin_mentor_enrollment' => 'required|string',
            'employee_id' => 'required|integer',
            'sess_id' => 'required',
            'weekday' => 'nullable',
            'from_time' => 'nullable',
            'to_time' => 'nullable',
            'description' => 'nullable|string|max:4000',
        ]);

        $result = $this->processAdminMentorEmployeeAssignments(
            $request->input('admin_mentor_enrollment'),
            (int) $request->input('sess_id'),
            [[
                'employee_id' => (int) $request->input('employee_id'),
                'description' => $request->input('description'),
                'weekday' => $request->input('weekday'),
                'from_time' => $request->input('from_time'),
                'to_time' => $request->input('to_time'),
            ]]
        );

        if (! empty($result['errors'])) {
            return response()->json(['message' => $result['errors'][0]['message']], 422);
        }

        if ($result['assigned_count'] === 0) {
            $skipMsg = $result['skipped'][0]['message'] ?? 'Assignment failed.';

            return response()->json(['message' => $skipMsg], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Employee mentor assigned successfully.',
            'assigned_count' => $result['assigned_count'],
            'skipped' => $result['skipped'],
        ]);
    }

    public function assignAdminMentorBatch(Request $request)
    {
        $employees = $request->input('employees', []);
        if (is_string($employees)) {
            $decoded = json_decode($employees, true);
            $employees = is_array($decoded) ? $decoded : [];
        }

        $request->merge(['employees' => $employees]);

        $request->validate([
            'admin_mentor_enrollment' => 'required|string',
            'sess_id' => 'required',
            'employees' => 'required|array|min:1',
            'employees.*.employee_id' => 'required|integer',
            'employees.*.description' => 'nullable|string|max:4000',
            'employees.*.weekday' => 'nullable',
            'employees.*.from_time' => 'nullable',
            'employees.*.to_time' => 'nullable',
            'staff_request_id' => 'nullable|integer',
        ]);

        $result = $this->processAdminMentorEmployeeAssignments(
            $request->input('admin_mentor_enrollment'),
            (int) $request->input('sess_id'),
            $employees
        );

        if (! empty($result['errors'])) {
            return response()->json([
                'message' => $result['errors'][0]['message'],
                'errors' => $result['errors'],
            ], 422);
        }

        $assigned = $result['assigned_count'];
        $skipped = $result['skipped'];

        if ($assigned === 0) {
            $skipText = collect($skipped)->pluck('message')->filter()->implode(' ');

            return response()->json([
                'message' => $skipText ?: 'No employees were assigned. They may already be assigned for this session.',
                'skipped' => $skipped,
            ], 422);
        }

        $staffRequestId = $request->input('staff_request_id');
        if ($staffRequestId) {
            $completeResult = $this->completeStaffMentorRequest(
                (int) $staffRequestId,
                (string) $request->input('admin_mentor_enrollment')
            );

            if (! $completeResult['success']) {
                return response()->json([
                    'message' => ($completeResult['message'] ?? 'Assignment saved but request could not be marked completed.')
                        .' Please refresh and verify.',
                    'assigned_count' => $assigned,
                    'skipped' => $skipped,
                ], 422);
            }
        }

        $message = $assigned === 1
            ? '1 employee assigned successfully.'
            : $assigned.' employees assigned successfully.';

        if ($staffRequestId) {
            $message = 'Mentor assigned and staff request marked as completed.';
        }

        if (count($skipped) > 0) {
            $message .= ' '.count($skipped).' skipped.';
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'assigned_count' => $assigned,
            'skipped' => $skipped,
        ]);
    }

    /**
     * @param  array<int, array{employee_id:int, description?:string, weekday?:mixed, from_time?:mixed, to_time?:mixed}>  $employees
     * @return array{assigned_count:int, skipped:array, errors:array}
     */
    private function processAdminMentorEmployeeAssignments(string $adminEnrollment, int $sessionId, array $employees): array
    {
        $mId = DB::table('pm_mentors')
            ->whereRaw('LOWER(m_enrollment) = LOWER(?)', [$adminEnrollment])
            ->where('m_sess_id', $sessionId)
            ->value('id');

        if (! $mId) {
            return [
                'assigned_count' => 0,
                'skipped' => [],
                'errors' => [['message' => 'Admin mentor record not found for this session.']],
            ];
        }

        $assignedCount = 0;
        $skipped = [];
        $errors = [];
        $seenInBatch = [];

        DB::beginTransaction();

        try {
            foreach ($employees as $index => $item) {
                $employeeId = (int) ($item['employee_id'] ?? 0);
                $label = 'Employee #'.($index + 1);

                if (! $employeeId) {
                    $errors[] = ['message' => $label.': invalid employee id.'];

                    continue;
                }

                if (isset($seenInBatch[$employeeId])) {
                    $skipped[] = [
                        'employee_id' => $employeeId,
                        'message' => 'Duplicate in this list.',
                    ];

                    continue;
                }
                $seenInBatch[$employeeId] = true;

                $timingError = $this->validateAdminMentorTimingFields(
                    $item['weekday'] ?? null,
                    $item['from_time'] ?? null,
                    $item['to_time'] ?? null
                );

                if ($timingError) {
                    $errors[] = ['employee_id' => $employeeId, 'message' => $label.': '.$timingError];

                    continue;
                }

                $employeeExists = DB::table('emp_employees')
                    ->where('employee_id', $employeeId)
                    ->where('emp_status', 'E')
                    ->exists();

                if (! $employeeExists) {
                    $skipped[] = [
                        'employee_id' => $employeeId,
                        'message' => 'Employee not found or inactive.',
                    ];

                    continue;
                }

                $duplicate = DB::table('pm_mentor_staff_assign')
                    ->where('pmsa_m_id', $mId)
                    ->where('pmsa_sess_id', $sessionId)
                    ->where('pmsa_emp_id', $employeeId)
                    ->whereIn('pmsa_status', [
                        self::PMSA_STATUS_ASSIGNED,
                        self::PMSA_STATUS_NON_ASSIGNED,
                        'E',
                    ])
                    ->exists();

                if ($duplicate) {
                    $skipped[] = [
                        'employee_id' => $employeeId,
                        'message' => 'Already assigned for this session.',
                    ];

                    continue;
                }

                try {
                    $this->insertAdminMentorEmployeeAssignment(
                        $mId,
                        $adminEnrollment,
                        $sessionId,
                        $employeeId,
                        $item['description'] ?? null,
                        $item['weekday'] ?? null,
                        $item['from_time'] ?? null,
                        $item['to_time'] ?? null
                    );

                    $assignedCount++;
                } catch (\Exception $e) {
                    if (stripos($e->getMessage(), 'ORA-00001') !== false) {
                        $skipped[] = [
                            'employee_id' => $employeeId,
                            'message' => 'Already assigned for this session.',
                        ];

                        continue;
                    }

                    throw $e;
                }
            }

            if (! empty($errors)) {
                DB::rollBack();

                return ['assigned_count' => 0, 'skipped' => $skipped, 'errors' => $errors];
            }

            DB::commit();

            return ['assigned_count' => $assignedCount, 'skipped' => $skipped, 'errors' => []];
        } catch (\Exception $e) {
            DB::rollBack();

            return [
                'assigned_count' => 0,
                'skipped' => [],
                'errors' => [['message' => $e->getMessage()]],
            ];
        }
    }

    private function validateAdminMentorTimingFields($weekday, $fromTime, $toTime): ?string
    {
        $hasWeekday = $weekday !== null && $weekday !== '';
        $hasFrom = $fromTime !== null && $fromTime !== '';
        $hasTo = $toTime !== null && $toTime !== '';
        $hasAnyTiming = $hasWeekday || $hasFrom || $hasTo;
        $hasFullTiming = $hasWeekday && $hasFrom && $hasTo;

        if ($hasAnyTiming && ! $hasFullTiming) {
            return 'Provide day, from time, and to time together, or leave all empty.';
        }

        if ($hasFullTiming && (int) $fromTime >= (int) $toTime) {
            return 'End time must be after start time.';
        }

        return null;
    }

    private function insertAdminMentorEmployeeAssignment(
        int $mId,
        string $adminEnrollment,
        int $sessionId,
        int $employeeId,
        ?string $description,
        $weekday,
        $fromTime,
        $toTime
    ): void {
        $hasWeekday = $weekday !== null && $weekday !== '';
        $hasFrom = $fromTime !== null && $fromTime !== '';
        $hasTo = $toTime !== null && $toTime !== '';
        $hasFullTiming = $hasWeekday && $hasFrom && $hasTo;

        $descriptionText = ($description !== null && trim($description) !== '') ? trim($description) : null;

        if ($descriptionText !== null) {
            DB::insert(
                'INSERT INTO pm_mentor_staff_assign (pmsa_m_id, pmsa_enrollment, pmsa_sess_id, pmsa_emp_id, pmsa_description, pmsa_status)
                 VALUES (?, ?, ?, ?, ?, ?)',
                [$mId, $adminEnrollment, $sessionId, $employeeId, $descriptionText, self::PMSA_STATUS_ASSIGNED]
            );
        } else {
            DB::insert(
                'INSERT INTO pm_mentor_staff_assign (pmsa_m_id, pmsa_enrollment, pmsa_sess_id, pmsa_emp_id, pmsa_status)
                 VALUES (?, ?, ?, ?, ?)',
                [$mId, $adminEnrollment, $sessionId, $employeeId, self::PMSA_STATUS_ASSIGNED]
            );
        }

        $pmsaId = (int) DB::table('pm_mentor_staff_assign')
            ->where('pmsa_m_id', $mId)
            ->whereRaw('LOWER(pmsa_enrollment) = LOWER(?)', [$adminEnrollment])
            ->where('pmsa_sess_id', $sessionId)
            ->where('pmsa_emp_id', $employeeId)
            ->orderByDesc('id')
            ->value('id');

        if (! $pmsaId) {
            throw new \RuntimeException('Could not resolve pm_mentor_staff_assign id after insert.');
        }

        if ($hasFullTiming) {
            DB::insert(
                'INSERT INTO pm_mentor_timing (pmt_m_id, pmt_pmsa_id, pmt_from_time, pmt_to_time, pmt_sess_id, pmt_weekday, pmt_enrollment_no)
                 VALUES (?, ?, ?, ?, ?, ?, ?)',
                [$mId, $pmsaId, (int) $fromTime, (int) $toTime, $sessionId, (int) $weekday, $adminEnrollment]
            );
        }
    }

    private function normalizePmsaStatus(?string $storedStatus): string
    {
        $normalized = strtolower(trim((string) $storedStatus));

        if ($normalized === self::PMSA_STATUS_NON_ASSIGNED) {
            return self::PMSA_STATUS_NON_ASSIGNED;
        }

        return self::PMSA_STATUS_ASSIGNED;
    }

    public function showbeMentors()
    {
        $currentSessionId = DB::table('student_courses')->max('SC_CO_SESS_ID');

        return view('peer_mentorship.get_be_mentor', compact('currentSessionId'));
    }

    public function getBeMentorsList(Request $request)
    {
        $maxSessionId = DB::table('student_courses')->max('SC_CO_SESS_ID');
        $sessionInput = $request->input('sess_id');
        $effectiveSessionId = is_numeric($sessionInput) ? (int) $sessionInput : (int) $maxSessionId;

        if (! $effectiveSessionId) {
            return response()->json([]);
        }

        $approvalInput = strtolower((string) $request->input('approval_status', 'pending'));
        $isDeclinedList = in_array($approvalInput, ['declined', 'decline', 'rejected'], true);

        $schoolId = $request->input('school_id');
        // Four interview_schedules subqueries each bind sess_id, then main sess_id.
        $bindings = array_fill(0, 4, $effectiveSessionId);
        $bindings[] = $effectiveSessionId;

        $approvalClause = $isDeclinedList
            ? "pm.m_be_mentor_approval = 'Declined'"
            : "pm.m_be_mentor_approval = 'PENDING'";

        $sql = "SELECT DISTINCT
            pm.m_enrollment,
            pm.m_be_mentor_approval,
            su.full_name,
            pm.m_committee_remarks,
            pm.m_mentor_type,
            (
                SELECT TO_CHAR(is_interview_date, 'YYYY-MM-DD') || ' ' || is_interview_time
                FROM interview_schedules
                WHERE LOWER(is_enrollment_no) = LOWER(pm.m_enrollment)
                  AND sess_id = ?
                ORDER BY id DESC
                FETCH FIRST 1 ROWS ONLY
            ) AS interview_datetime,
            (
                SELECT TO_CHAR(is_interview_date, 'YYYY-MM-DD')
                FROM interview_schedules
                WHERE LOWER(is_enrollment_no) = LOWER(pm.m_enrollment)
                  AND sess_id = ?
                ORDER BY id DESC
                FETCH FIRST 1 ROWS ONLY
            ) AS interview_date,
            (
                SELECT is_interview_time
                FROM interview_schedules
                WHERE LOWER(is_enrollment_no) = LOWER(pm.m_enrollment)
                  AND sess_id = ?
                ORDER BY id DESC
                FETCH FIRST 1 ROWS ONLY
            ) AS interview_time,
            (
                SELECT interviewed
                FROM interview_schedules
                WHERE LOWER(is_enrollment_no) = LOWER(pm.m_enrollment)
                  AND sess_id = ?
                ORDER BY id DESC
                FETCH FIRST 1 ROWS ONLY
            ) AS interviewed
        FROM pm_mentors pm
        JOIN sisusers su ON LOWER(su.username) = LOWER(pm.m_enrollment)
        WHERE {$approvalClause}
          AND pm.m_sess_id = ?";

        if ($schoolId !== null && $schoolId !== '') {
            $sql .= "
            AND EXISTS (
                SELECT 1
                FROM students_apply_info sai_sch
                WHERE LOWER(sai_sch.sai_enrollment_no) = LOWER(pm.m_enrollment)
                  AND sai_sch.sai_sdpo_school_id = ?
            )";
            $bindings[] = $schoolId;
        }

        return response()->json(DB::select($sql, $bindings));
    }

    public function committeeApprovel(Request $request)
    {
        $request->validate([
            'enrollment' => 'required',
            'status' => 'required|in:Approved,Declined',
            'remarks' => 'required',
            'sess_id' => 'required',
            'mentor_type' => 'required|in:academic,admin,both',
        ]);

        $approvalValue = $request->input('status') === 'Approved' ? 'Approved' : 'Declined';

        $updated = DB::table('pm_mentors')
            ->whereRaw('LOWER(m_enrollment) = LOWER(?)', [$request->input('enrollment')])
            ->where('m_sess_id', $request->input('sess_id'))
            ->update([
                'm_be_mentor_approval' => $approvalValue,
                'm_committee_remarks' => $request->input('remarks'),
                'm_mentor_type' => $request->input('mentor_type'),
            ]);

        if (! $updated) {
            return response()->json([
                'success' => false,
                'message' => 'Mentor request not found for the selected session.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Approval status updated successfully.',
        ]);
    }

















public function getMentorDetails(Request $request)
{
    $mentorEnrollment = $request->input('mentor_enrollment');
    $studentEnrollment = $request->input('student_enrollment');
    $courseId = $request->input('course_id');
    $sessionId = $request->input('sess_id') ?: DB::table('student_courses')->max('SC_CO_SESS_ID');

    if (!$mentorEnrollment) {
        return response()->json(['message' => 'mentor_enrollment is required.'], 422);
    }

    $mentorSlots = $this->fetchMentorTimingSlots($mentorEnrollment, $sessionId, $courseId);
    $coursePayload = $this->buildMentorCoursesWithSlots($mentorEnrollment, $sessionId);

    $timetableSlotOptions = DB::table('timetable_slots')
        ->selectRaw('ts_id AS from_id, ts_id AS to_id, ts_name AS from_name, ts_name AS to_name')
        ->whereBetween('ts_id', [5, 21])
        ->orderBy('ts_id')
        ->get();

    $response = [
        'session_id' => $sessionId,
        'courses' => $coursePayload['courses'],
        'mentorDetails' => $mentorSlots,
        'mentorSlots' => $mentorSlots,
        'unassigned_slots' => $coursePayload['unassigned_slots'],
        'timetableSlotsFrom' => $timetableSlotOptions,
    ];

    if ($studentEnrollment) {
        $studentTimetable = $this->fetchStudentTimetableSlots($studentEnrollment, $sessionId);
        $mentorSlotsWithStatus = $this->annotateMentorSlotsAvailability($mentorSlots, $studentTimetable);
        $selectableSlots = array_values(array_filter($mentorSlotsWithStatus, function ($slot) {
            return !empty($slot['is_selectable']);
        }));
        $freeSlots = array_values(array_filter($mentorSlotsWithStatus, function ($slot) {
            return !empty($slot['is_free']);
        }));
        $partialSlots = array_values(array_filter($mentorSlotsWithStatus, function ($slot) {
            return !empty($slot['is_partial']);
        }));

        $studentScheduleView = $this->buildStudentScheduleView($studentTimetable, $mentorSlotsWithStatus);
        $slotSummary = $this->computeSlotSummary($mentorSlotsWithStatus, $studentScheduleView);

        $response['mentorDetails'] = $mentorSlotsWithStatus;
        $response['mentorSlots'] = $mentorSlotsWithStatus;
        $response['studentTimetable'] = $studentScheduleView;
        $response['studentSlots'] = $studentScheduleView;
        $response['freeSlots'] = $freeSlots;
        $response['partialSlots'] = $partialSlots;
        $response['selectableSlots'] = $selectableSlots;
        $response['slotSummary'] = $slotSummary;
    }

    return response()->json($response);
}

/**
 * Mentor availability rows (JOINs, optional course filter, de-duplicated by day + time range).
 */
private function fetchMentorTimingSlots(string $mentorEnrollment, $sessionId, $courseId = null): array
{
    $query = DB::table('pm_mentor_timing as pmt')
        ->leftJoin('week_days as wd', 'wd.id', '=', 'pmt.pmt_weekday')
        ->leftJoin('timetable_slots as ts_from', 'ts_from.ts_id', '=', 'pmt.pmt_from_time')
        ->leftJoin('timetable_slots as ts_to', 'ts_to.ts_id', '=', 'pmt.pmt_to_time')
        ->select([
            'pmt.id as timing_id',
            'pmt.pmt_md_id',
            'pmt.pmt_weekday',
            'wd.day_name as week_day',
            'ts_from.ts_name as from_time',
            'ts_to.ts_name as to_time',
            DB::raw('pmt.pmt_from_time AS from_time_id'),
            DB::raw('pmt.pmt_to_time AS to_time_id'),
        ])
        ->whereRaw('LOWER(pmt.pmt_enrollment_no) = LOWER(?)', [$mentorEnrollment])
        ->whereNull('pmt.pmt_pmsa_id');

    if ($sessionId) {
        $query->where('pmt.pmt_sess_id', $sessionId);
    }

    if ($courseId) {
        $query->whereIn('pmt.pmt_md_id', function ($sub) use ($mentorEnrollment, $sessionId, $courseId) {
            $sub->select('id')
                ->from('pm_mentors_details')
                ->whereRaw('LOWER(md_enrollment) = LOWER(?)', [$mentorEnrollment])
                ->where('md_course_id', $courseId);
            if ($sessionId) {
                $sub->where('md_sess_id', $sessionId);
            }
        });
    }

    $seen = [];
    $slots = [];
    foreach ($query->get() as $row) {
        $key = ($row->pmt_weekday ?? '') . '|' . ($row->from_time_id ?? '') . '|' . ($row->to_time_id ?? '');
        if (isset($seen[$key])) {
            continue;
        }
        $seen[$key] = true;
        $slots[] = [
            'timing_id' => $row->timing_id,
            'pmt_md_id' => $row->pmt_md_id,
            'pmt_weekday' => $row->pmt_weekday,
            'week_day' => $row->week_day,
            'from_time' => $row->from_time,
            'to_time' => $row->to_time,
            'from_time_id' => $row->from_time_id,
            'to_time_id' => $row->to_time_id,
        ];
    }

    usort($slots, function ($a, $b) {
        $dayCmp = $this->weekdaySortOrder($a['week_day'] ?? '') <=> $this->weekdaySortOrder($b['week_day'] ?? '');
        if ($dayCmp !== 0) {
            return $dayCmp;
        }
        return ((int) ($a['from_time_id'] ?? 0)) <=> ((int) ($b['from_time_id'] ?? 0));
    });

    return $slots;
}

/**
 * Student weekly timetable (distinct day + time from upcoming classes).
 * Uses parent_co_id enrollment logic and 30-min slot IDs (06:30 AM = 0).
 */
private function fetchStudentTimetableSlots(string $studentEnrollment, $sessionId): array
{
    $sql = "
        WITH enrolled_courses AS (
            SELECT DISTINCT
                CASE
                    WHEN co.parent_co_id IS NULL OR co.parent_co_id = 0
                    THEN co.co_id
                    ELSE co.parent_co_id
                END AS main_co_id
            FROM student_courses sc
            JOIN courses_offered co ON co.co_id = sc.sc_co_id
            WHERE sc.sc_sai_enrollment_no = ?
              AND sc.sc_co_sess_id = ?
              AND sc.sc_status IN ('A', 'Block')
              AND sc.sc_enrollment_status = '1'
        ),
        weekly_tt AS (
            SELECT DISTINCT
                mt.tt_subject_day AS day_name,
                mt.tt_subject_time_from AS from_time,
                mt.tt_subject_time_to AS to_time,
                ROUND(
                    (
                        TO_NUMBER(TO_CHAR(TO_DATE(mt.tt_subject_time_from, 'HH:MI AM'), 'HH24')) * 60
                        + TO_NUMBER(TO_CHAR(TO_DATE(mt.tt_subject_time_from, 'HH:MI AM'), 'MI'))
                        - 390
                    ) / 30
                ) AS from_time_id,
                ROUND(
                    (
                        TO_NUMBER(TO_CHAR(TO_DATE(mt.tt_subject_time_to, 'HH:MI AM'), 'HH24')) * 60
                        + TO_NUMBER(TO_CHAR(TO_DATE(mt.tt_subject_time_to, 'HH:MI AM'), 'MI'))
                        - 390
                    ) / 30
                ) AS to_time_id,
                CASE UPPER(mt.tt_subject_day)
                    WHEN 'MONDAY' THEN 1
                    WHEN 'TUESDAY' THEN 2
                    WHEN 'WEDNESDAY' THEN 3
                    WHEN 'THURSDAY' THEN 4
                    WHEN 'FRIDAY' THEN 5
                    WHEN 'SATURDAY' THEN 6
                    WHEN 'SUNDAY' THEN 7
                END AS day_order
            FROM master_timetable mt
            JOIN enrolled_courses ec ON ec.main_co_id = mt.mtt_co_id
            WHERE mt.tt_status = 'E'
              AND TRUNC(mt.tt_class_date) >= TRUNC(SYSDATE)
        )
        SELECT
            day_name,
            from_time,
            to_time,
            from_time_id,
            to_time_id
        FROM weekly_tt
        ORDER BY day_order, from_time_id, to_time_id";

    return array_map(function ($row) {
        $day = $row->day_name ?? null;

        return [
            'week_day' => $day,
            'from_time' => $row->from_time,
            'to_time' => $row->to_time,
            'from_time_id' => $row->from_time_id,
            'to_time_id' => $row->to_time_id,
            'tt_subject_day' => $day,
            'tt_subject_time_from' => $row->from_time,
            'tt_subject_time_to' => $row->to_time,
        ];
    }, DB::select($sql, [$studentEnrollment, $sessionId]));
}

private function annotateMentorSlotsAvailability(array $mentorSlots, array $studentTimetable): array
{
    return array_map(function ($slot) use ($studentTimetable) {
        $status = $this->resolveMentorSlotAvailability($slot, $studentTimetable);

        $selectable = in_array($status, ['Free', 'Partially Busy'], true);

        return array_merge($slot, [
            'availability_status' => $status,
            'status_label' => $status === 'Free' ? 'Free' : ($status === 'Partially Busy' ? 'Partial' : 'Busy'),
            'is_free' => $status === 'Free',
            'is_partial' => $status === 'Partially Busy',
            'is_selectable' => $selectable,
            'between_exists' => $status,
        ]);
    }, $mentorSlots);
}

private function resolveMentorSlotAvailability(array $mentorSlot, array $studentTimetable): string
{
    $mentorRange = $this->slotTimeRange($mentorSlot);
    if (!$mentorRange) {
        return 'Free';
    }

    $mentorDay = $mentorSlot['week_day'] ?? '';
    $status = 'Free';
    foreach ($studentTimetable as $studentSlot) {
        if (strcasecmp($mentorDay, $studentSlot['week_day'] ?? '') !== 0) {
            continue;
        }

        $studentRange = $this->slotTimeRange($studentSlot);
        if (!$studentRange) {
            continue;
        }
        if ($mentorRange['from'] >= $studentRange['to'] || $mentorRange['to'] <= $studentRange['from']) {
            continue;
        }
        if ($mentorRange['from'] >= $studentRange['from'] && $mentorRange['to'] <= $studentRange['to']) {
            return 'Busy';
        }
        $status = 'Partially Busy';
    }

    return $status;
}

/**
 * Student panel: in-class rows, one consolidated row per partial mentor window, and mutual-free windows.
 */
private function buildStudentScheduleView(array $studentClasses, array $mentorSlots): array
{
    $items = [];
    $partialMentorKeys = [];

    foreach ($mentorSlots as $mentorSlot) {
        if (($mentorSlot['availability_status'] ?? '') !== 'Partially Busy') {
            continue;
        }

        $overlappingClasses = array_values(array_filter($studentClasses, function ($classSlot) use ($mentorSlot) {
            return $this->slotsOverlap($classSlot, $mentorSlot);
        }));

        $partialKey = $this->scheduleSlotKey($mentorSlot);
        if (isset($partialMentorKeys[$partialKey])) {
            continue;
        }
        $partialMentorKeys[$partialKey] = true;

        $displayClassSlot = $this->resolvePartialOverlapDisplaySlot($mentorSlot, $overlappingClasses);

        $items[] = array_merge($displayClassSlot, [
            'entry_type' => 'partial_conflict',
            'availability_status' => 'Partially Busy',
            'status_label' => 'Partial overlap',
            'overlap_class_count' => count($overlappingClasses),
        ]);
    }

    foreach ($studentClasses as $classSlot) {
        $overlap = $this->resolveOverlapWithMentorSlots($classSlot, $mentorSlots);
        if ($overlap === 'Partially Busy') {
            continue;
        }

        $status = $overlap === 'None' ? 'Busy' : $overlap;
        $items[] = array_merge($classSlot, [
            'entry_type' => 'class',
            'availability_status' => $status,
            'status_label' => $status === 'Busy' ? 'In class' : 'In class',
        ]);
    }

    foreach ($mentorSlots as $mentorSlot) {
        if (($mentorSlot['availability_status'] ?? '') !== 'Free') {
            continue;
        }
        if ($this->slotOverlapsAny($mentorSlot, $studentClasses)) {
            continue;
        }

        $items[] = array_merge($mentorSlot, [
            'entry_type' => 'mutual_free',
            'availability_status' => 'Free',
            'status_label' => 'Free',
        ]);
    }

    usort($items, function ($a, $b) {
        $dayCmp = $this->weekdaySortOrder($a['week_day'] ?? '') <=> $this->weekdaySortOrder($b['week_day'] ?? '');
        if ($dayCmp !== 0) {
            return $dayCmp;
        }
        return ((int) ($a['from_time_id'] ?? 0)) <=> ((int) ($b['from_time_id'] ?? 0));
    });

    return $items;
}

/**
 * Student partial row shows the overlapping class time, not the mentor window.
 */
private function resolvePartialOverlapDisplaySlot(array $mentorSlot, array $overlappingClasses): array
{
    if ($overlappingClasses === []) {
        return $mentorSlot;
    }

    if (count($overlappingClasses) === 1) {
        return $overlappingClasses[0];
    }

    $mentorRange = $this->slotTimeRange($mentorSlot);
    $bestClass = $overlappingClasses[0];
    $bestOverlapMinutes = -1;

    foreach ($overlappingClasses as $classSlot) {
        $classRange = $this->slotTimeRange($classSlot);
        if (!$mentorRange || !$classRange) {
            continue;
        }

        $overlapStart = max($mentorRange['from'], $classRange['from']);
        $overlapEnd = min($mentorRange['to'], $classRange['to']);
        $overlapMinutes = $overlapEnd - $overlapStart;
        if ($overlapMinutes > $bestOverlapMinutes) {
            $bestOverlapMinutes = $overlapMinutes;
            $bestClass = $classSlot;
        }
    }

    return $bestClass;
}

private function scheduleSlotKey(array $slot): string
{
    $range = $this->slotTimeRange($slot);

    return strtolower(trim($slot['week_day'] ?? '')) . '|'
        . ($range['from'] ?? (int) ($slot['from_time_id'] ?? 0)) . '|'
        . ($range['to'] ?? (int) ($slot['to_time_id'] ?? 0));
}

private function slotTimeRange(array $slot): ?array
{
    $from = $this->timeLabelToMinutes($slot['from_time'] ?? $slot['tt_subject_time_from'] ?? '');
    $to = $this->timeLabelToMinutes($slot['to_time'] ?? $slot['tt_subject_time_to'] ?? '');
    if ($from === null || $to === null || $to <= $from) {
        return null;
    }

    return ['from' => $from, 'to' => $to];
}

private function timeLabelToMinutes(string $timeLabel): ?int
{
    $timeLabel = trim($timeLabel);
    if ($timeLabel === '') {
        return null;
    }

    $parsed = strtotime($timeLabel);
    if ($parsed === false) {
        return null;
    }

    return ((int) date('G', $parsed)) * 60 + (int) date('i', $parsed);
}

private function resolveOverlapWithMentorSlots(array $slot, array $mentorSlots): string
{
    $status = 'None';
    foreach ($mentorSlots as $mentorSlot) {
        if (!$this->slotsOverlap($slot, $mentorSlot)) {
            continue;
        }
        if (($mentorSlot['availability_status'] ?? '') === 'Busy') {
            return 'Busy';
        }
        if (($mentorSlot['availability_status'] ?? '') === 'Partially Busy') {
            $status = 'Partially Busy';
        }
    }

    return $status;
}

private function computeSlotSummary(array $mentorSlots, array $studentView): array
{
    $countStatuses = function (array $slots): array {
        $counts = ['Free' => 0, 'Partially Busy' => 0, 'Busy' => 0];
        foreach ($slots as $slot) {
            $status = $slot['availability_status'] ?? 'Free';
            if (!isset($counts[$status])) {
                $counts[$status] = 0;
            }
            $counts[$status]++;
        }

        return $counts;
    };

    $mentor = $countStatuses($mentorSlots);
    $student = $countStatuses($studentView);

    return [
        'mentor_free' => $mentor['Free'] ?? 0,
        'mentor_partial' => $mentor['Partially Busy'] ?? 0,
        'mentor_busy' => $mentor['Busy'] ?? 0,
        'student_free' => $student['Free'] ?? 0,
        'student_partial' => $student['Partially Busy'] ?? 0,
        'student_busy' => $student['Busy'] ?? 0,
        'selectable' => ($mentor['Free'] ?? 0) + ($mentor['Partially Busy'] ?? 0),
    ];
}

private function slotOverlapsAny(array $slot, array $others): bool
{
    foreach ($others as $other) {
        if ($this->slotsOverlap($slot, $other)) {
            return true;
        }
    }

    return false;
}

private function slotsOverlap(array $a, array $b): bool
{
    if (strcasecmp($a['week_day'] ?? '', $b['week_day'] ?? '') !== 0) {
        return false;
    }

    $aRange = $this->slotTimeRange($a);
    $bRange = $this->slotTimeRange($b);
    if ($aRange && $bRange) {
        return !($aRange['from'] >= $bRange['to'] || $aRange['to'] <= $bRange['from']);
    }

    $aFrom = (int) ($a['from_time_id'] ?? 0);
    $aTo = (int) ($a['to_time_id'] ?? 0);
    $bFrom = (int) ($b['from_time_id'] ?? 0);
    $bTo = (int) ($b['to_time_id'] ?? 0);

    if (!$aFrom || !$aTo || !$bFrom || !$bTo) {
        return false;
    }

    return !($aFrom >= $bTo || $aTo <= $bFrom);
}

private function buildMentorCoursesWithSlots(string $mentorEnrollment, $sessionId): array
{
    $coursesSql = "
        SELECT MD_ENROLLMENT, id,
            md_time_alot,
            get_course_name(MD_CO_ID) AS course_code,
            get_course_name(MD_CO_ID) AS course_name,
            MD_CO_ID, MD_COURSE_ID, MD_TIME_ALOT
        FROM pm_mentors_details
        WHERE LOWER(MD_ENROLLMENT) = LOWER(?)";
    $bindings = [$mentorEnrollment];
    if ($sessionId) {
        $coursesSql .= ' AND md_sess_id = ?';
        $bindings[] = $sessionId;
    }
    $courses = DB::select($coursesSql, $bindings);

    $timingQuery = DB::table('pm_mentor_timing as pmt')
        ->leftJoin('week_days as wd', 'wd.id', '=', 'pmt.pmt_weekday')
        ->leftJoin('timetable_slots as ts_from', 'ts_from.ts_id', '=', 'pmt.pmt_from_time')
        ->leftJoin('timetable_slots as ts_to', 'ts_to.ts_id', '=', 'pmt.pmt_to_time')
        ->select([
            'pmt.id as timing_id',
            'pmt.pmt_md_id',
            'pmt.pmt_weekday',
            'wd.day_name as week_day',
            'ts_from.ts_name as from_time',
            'ts_to.ts_name as to_time',
            DB::raw('pmt.pmt_from_time AS from_time_id'),
            DB::raw('pmt.pmt_to_time AS to_time_id'),
        ])
        ->whereRaw('LOWER(pmt.pmt_enrollment_no) = LOWER(?)', [$mentorEnrollment])
        ->whereNull('pmt.pmt_pmsa_id');

    if ($sessionId) {
        $timingQuery->where('pmt.pmt_sess_id', $sessionId);
    }

    $slotsByMdId = [];
    $unassignedSlots = [];
    foreach ($timingQuery->get() as $slot) {
        $row = (array) $slot;
        $mdKey = $slot->pmt_md_id ?? null;
        if ($mdKey) {
            $slotsByMdId[$mdKey] = $slotsByMdId[$mdKey] ?? [];
            $slotsByMdId[$mdKey][] = $row;
        } else {
            $unassignedSlots[] = $row;
        }
    }

    $coursesWithSlots = [];
    foreach ($courses as $course) {
        $mdId = $course->id;
        $coursesWithSlots[] = [
            'id' => $mdId,
            'md_co_id' => $course->md_co_id ?? null,
            'course_name' => $course->course_name ?? $course->course_code ?? '—',
            'md_time_alot' => $course->md_time_alot ?? 0,
            'slots' => $slotsByMdId[$mdId] ?? [],
        ];
    }

    return [
        'courses' => $coursesWithSlots,
        'unassigned_slots' => $unassignedSlots,
    ];
}

private function weekdaySortOrder(?string $day): int
{
    $order = [
        'Monday' => 1,
        'Tuesday' => 2,
        'Wednesday' => 3,
        'Thursday' => 4,
        'Friday' => 5,
        'Saturday' => 6,
        'Sunday' => 7,
    ];

    return $order[$day ?? ''] ?? 8;
}


public function getStudentTimetable(Request $request)
{
    $enrollment = $request->get('enrollment_no');

    $timetable = DB::table('student_courses')
        ->select(
            'course_cd_name',
            DB::raw('get_teachername(sc_co_id) AS teachername'),
            'tt_subject_day',
            'tt_subject_time_from',
            'tt_subject_time_to',
            'sc_sai_enrollment_no'
        )
        ->leftJoin('courses_offered', 'co_id', '=', 'sc_co_id')
        ->leftJoin('timetable', function ($join) {
            $join->on('tt_co_id', '=', 'co_id')
                 ->orOn('tt_co_id', '=', 'parent_co_id');
        })
        ->leftJoin('courses_two', 'course_id', '=', 'co_course_id')
        ->leftJoin('ACADEMIC_SESSIONS', 'sess_id', '=', 'sc_co_sess_id')
        ->whereNotIn('sc_co_id', function ($query) use ($enrollment) {
            $query->select('gh_co_id')
                  ->from('grade_history')
                  ->where('gh_sai_enrollment_no', $enrollment)
                  ->whereNotNull('gh_co_id')
                  ->where('gh_co_id', '>', 0)
                  ->where('gh_exam_fsubmit', 1);
        })
        ->where('sc_sai_enrollment_no', $enrollment)
        ->where('sc_enrollment_status', 1)
        ->whereIn('sc_status', ['Approve', 'A'])
        ->where('co_status', 'E')
        ->get();

    return response()->json($timetable);
}




function mentorCompletetimetable(Request $request){

$studentRollNo = $request->get('enrollment');
$mentorRollNo = $request->get('mentorRoll');
$maxSessionId = DB::table('student_courses')->max('SC_CO_SESS_ID');
 //dd($mentorRollNo);
$mentorTimeSlots = "SELECT
COUNT(*) AS subject_count,
tt_subject_day,
tt_subject_time_from,
tt_subject_time_from_id,
    tt_subject_time_to_id,
    mtt_co_id,
tt_subject_time_to
FROM
master_timetable
WHERE
tt_status = 'E' and
tt_teach_course_type is null and
tt_subject_day not in ('Saturday','Sunday') and
mtt_co_id    IN (
    SELECT
        case when sc_co_id = sc_cms_course_co_id then sc_co_id else sc_cms_course_co_id end as sc_co_id
    FROM
        student_courses
    WHERE
        lower(sc_sai_enrollment_no) = lower('$mentorRollNo')
        AND sc_co_sess_id = $maxSessionId
        AND sc_enrollment_status = 1
)
GROUP BY
tt_subject_day,
tt_subject_time_from,
tt_subject_time_from_id,
tt_subject_time_to_id,
mtt_co_id,
tt_subject_time_to
ORDER BY
CASE
    WHEN tt_subject_day = 'Monday' THEN 1
    WHEN tt_subject_day = 'Tuesday' THEN 2
    WHEN tt_subject_day = 'Wednesday' THEN 3
    WHEN tt_subject_day = 'Thursday' THEN 4
    WHEN tt_subject_day = 'Friday' THEN 5
    WHEN tt_subject_day = 'Saturday' THEN 6
    WHEN tt_subject_day = 'Sunday' THEN 7
    ELSE 8 -- Handles unexpected values
END";
// echo $mentorTimeSlots;exit;
// dd($mentorTimeSlots);
$resmentorTimeSlots = DB::select($mentorTimeSlots);


$studentTimeSlots = "SELECT
COUNT(*) AS subject_count,
tt_subject_day,
tt_subject_time_from,
tt_subject_time_from_id,
    tt_subject_time_to_id,
    mtt_co_id,
tt_subject_time_to
FROM
master_timetable
WHERE
tt_status = 'E' and
tt_teach_course_type is null and
tt_subject_day not in ('Saturday','Sunday') and
mtt_co_id    IN (
    SELECT
        case when sc_co_id = sc_cms_course_co_id then sc_co_id else sc_cms_course_co_id end as sc_co_id
    FROM
        student_courses
    WHERE
        lower(sc_sai_enrollment_no) = lower('$studentRollNo')
        AND sc_co_sess_id = $maxSessionId
        AND sc_enrollment_status = 1
)
GROUP BY
tt_subject_day,
tt_subject_time_from,
tt_subject_time_from_id,
tt_subject_time_to_id,
mtt_co_id,
tt_subject_time_to
ORDER BY
CASE
    WHEN tt_subject_day = 'Monday' THEN 1
    WHEN tt_subject_day = 'Tuesday' THEN 2
    WHEN tt_subject_day = 'Wednesday' THEN 3
    WHEN tt_subject_day = 'Thursday' THEN 4
    WHEN tt_subject_day = 'Friday' THEN 5
    WHEN tt_subject_day = 'Saturday' THEN 6
    WHEN tt_subject_day = 'Sunday' THEN 7
    ELSE 8 -- Handles unexpected values
END";
// echo $studentTimeSlots;exit;
// dd($studentTimeSlots);
$resSTudentTimeSlots = DB::select($studentTimeSlots);
return view('peer_mentorship.view_timetable', compact('studentRollNo', 'mentorRollNo','resmentorTimeSlots','resSTudentTimeSlots'));
    // return view('peer_mentorship.view_timetable');

}

public function getMentors()
{

    $mentors = DB::select("SELECT pm_mentors.m_enrollment, sisusers.full_name, pm_mentors.m_committee_remarks, pm_mentors.m_mentor_type
                           FROM pm_mentors
                           JOIN sisusers ON LOWER(sisusers.username) = LOWER(pm_mentors.m_enrollment)");


    return response()->json($mentors);
    // return view('peer_mentorship.get_mentor', compact('mentors'));
}


public function markedMentor(Request $request)
{
//    dd($request->all());

    // Validate input
    $request->validate([
        'enrollment_no' => 'required|string',
        'mentoring_type' => 'required|string',
     // 'course_id' => 'required|array',
        'course_id.*' => 'integer',
    ]);
 //  dd($request);
    $enrollmentNo = $request->enrollment_no;
    $courseIds = $request->course_id;
    $mentoring_type = $request->mentoring_type;
    $fromtiming =  $request->fromtiming;
    $totiming =  $request->totiming;
    $weekdays=  $request->weekdays;
    $remarks = $request->remarks;
    $entryUserId = auth()->user()->id;
    $maxSessionId = DB::table('student_courses')->max('SC_CO_SESS_ID');




        $result = DB::table('pm_mentors')
            ->select('M_ENROLLMENT')
            ->whereRaw('LOWER(M_ENROLLMENT) = LOWER(?)', [$enrollmentNo])
            ->first();

          //  dd($result);

        if ($result) {
          //  dd('Mentore not added');
            //echo "Student exists: ";
        } else {
            DB::table('pm_mentors')->insertGetId([
                'm_enrollment' => $enrollmentNo,
                'm_entry_user_id' => $entryUserId,
                'm_mentor_type' => $mentoring_type,
                'm_entry_datetime' => now(),
                'm_sess_id' => $maxSessionId,
               // 'M_TIMING' => $timing,
               // 'm_weekdays'=> $weekdays,
                'M_COMMITTEE_REMARKS' => $remarks,
                'm_status' => 'E',
            ]);
            DB::commit();
          // dd('Mentore addesd');
           // echo "Student does not exist.";
        }


        $id = DB::table('pm_mentors_details')
            ->whereRaw('LOWER(md_enrollment) = LOWER(?)', [$enrollmentNo])
            ->where('md_co_id', $request->course_id)
            ->value('id');

       //     dd($id);
       $newId = 0;


            if ($id == null){
               // dd($id);
                if($request->course_id != null ){

                    $COURSES_OFFERED = DB::table('COURSES_OFFERED')
                        ->where('CO_ID', $request->course_id)
                        ->get();
                   



                $newId = DB::table('pm_mentors_details')->insertGetId([
                    'md_enrollment' => $enrollmentNo,
                    'md_co_id' => $request->course_id,
                    'md_course_id' => $COURSES_OFFERED[0]->co_course_id,
                    'md_time_alot' => 0, // Default time allocation, can be updated later
                    'md_entry_datetime' => now(),
                    'md_sess_id'=> $maxSessionId,
                    'md_status' => 'Active',
                ]);
           // }
              $id = $newId ;

            DB::commit();
            //dd($newId);

            }








        DB::beginTransaction();

        try {
            foreach ($weekdays as $i => $weekday) {
                // Insert mentor timing details
                $timingId = DB::table('PM_MENTOR_TIMING')->insertGetId([
                    'PMT_M_ID' => $result,                      // $newId should be defined earlier
                    'PMT_FROM_TIME' => $fromtiming[$i],         // Assuming timing is an array of ['from' => 'time', 'to' => 'time']
                    'PMT_TO_TIME' => $totiming[$i],             // Provide proper 'to' timing
                    'pmt_sess_id'=> $maxSessionId,
                    'pmt_weekday' => $weekday,                     // Weekday value
                    'pmt_enrollment_no' => $enrollmentNo,
                ]);
            }




            // Commit the transaction
            DB::commit();
       
        } catch (\Exception $e) {
            // Rollback in case of an error
          //  DB::rollBack();

            // Handle the exception
            return response()->json(['error' => $e->getMessage()], 500);
        }






              }
      


            return  redirect('/mentors_list');



 
}

public function RequestIndex()
{
    $currentSessionId = DB::table('student_courses')->max('SC_CO_SESS_ID');

    return view('peer_mentorship.requests_index', compact('currentSessionId'));
}



function getMentorRequest(Request $request)
{
    $maxSessionId = DB::table('student_courses')->max('SC_CO_SESS_ID');
    $sessionInput = $request->input('session_id') ?? $request->input('sess_id');
    $effectiveSessionId = is_numeric($sessionInput) ? (int) $sessionInput : (int) $maxSessionId;

    if (! $effectiveSessionId) {
        return response()->json([]);
    }

    $status = $request->input('status', 'E');
    $schoolId = $request->input('school_id');

    // $data = DB::table('pm_mentors_request')
    // ->join('sisusers', DB::raw('LOWER(sisusers.username)'), '=', DB::raw('LOWER(pm_mentors_request.mr_enrollment)'))
    // ->distinct()
    // ->select(
    //     DB::raw('UPPER(pm_mentors_request.mr_enrollment) as mr_enrollment'),
    //     'sisusers.FULL_NAME',
    //     'sisusers.PROGRAMS'
    //     // 'pm_mentors_request.*'
    // )
    // ->where('MR_SESS_ID',$maxSessionId)
    // ->where('MR_STATUS', $request->status)
    // ->get();
    //    $data = DB::table('pm_mentors_request')
    // ->join('sisusers', function ($join) {
    //     $join->on(
    //         DB::raw('LOWER(sisusers.username)'),
    //         '=',
    //         DB::raw('LOWER(pm_mentors_request.mr_enrollment)')
    //     );
    // })
    // ->distinct()
    // ->select(
    //     DB::raw('UPPER(pm_mentors_request.mr_enrollment) as mr_enrollment'),
    //     'sisusers.FULL_NAME',
    //     'sisusers.PROGRAMS'
    // )
    // ->where('pm_mentors_request.MR_SESS_ID', $maxSessionId)
    // ->where('pm_mentors_request.MR_STATUS', $request->status)
    // // ->whereNotIn(DB::raw('LOWER(pm_mentors_request.mr_enrollment)'), function ($query) use ($maxSessionId) {
    // ->whereNotIn(DB::raw('pm_mentors_request.id'), function ($query) use ($maxSessionId) {
    //     // $query->select(DB::raw('LOWER(MRD_ENROLLMENT)'))
    //     $query->select(DB::raw('pm_mentors_request.id'))
    //         ->from('PM_MENTORS_REQUEST_DETAILS')
    //         ->join('PM_ASSIGN_STUDENTS', function ($join) {
    //             $join->on(
    //                 DB::raw('LOWER(MRD_ENROLLMENT)'),
    //                 '=',
    //                 DB::raw('LOWER(AS_STUDENT_ID)')
    //             )
    //             ->whereColumn('MRD_COURSE_ID', 'AS_COURSE_ID');
    //         })
    //         ->where('MRD_SESS_ID', $maxSessionId);
    // })
    // ->orderBy('sisusers.PROGRAMS', 'desc')
    // ->get();
    $query = DB::table('pm_mentors_request')
        ->distinct()
        ->select(
            DB::raw('UPPER(pm_mentors_request.mr_enrollment) as mr_enrollment'),
            'sisusers.full_name',
            'sisusers.programs',
            'sisusers.department'
        )
        ->join('sisusers', function ($join) {
            $join->on(
                DB::raw('LOWER(sisusers.username)'),
                '=',
                DB::raw('LOWER(pm_mentors_request.mr_enrollment)')
            );
        })
        ->where('pm_mentors_request.mr_sess_id', $effectiveSessionId)
        ->where('pm_mentors_request.mr_status', $status)
        ->whereNotIn('pm_mentors_request.id', function ($sub) use ($effectiveSessionId) {
            $sub->select('pm_mentors_request_details.id')
                ->from('pm_mentors_request_details')
                ->join('pm_assign_students', function ($join) {
                    $join->on(
                        DB::raw('LOWER(mrd_enrollment)'),
                        '=',
                        DB::raw('LOWER(as_student_id)')
                    );
                })
                ->where('mrd_sess_id', $effectiveSessionId)
                ->whereColumn('mrd_course_id', 'as_course_id');
        });

    if ($schoolId !== null && $schoolId !== '') {
        $query->whereExists(function ($sub) use ($schoolId) {
            $sub->select(DB::raw(1))
                ->from('students_apply_info as sai_sch')
                ->whereRaw('LOWER(sai_sch.sai_enrollment_no) = LOWER(pm_mentors_request.mr_enrollment)')
                ->where('sai_sch.sai_sdpo_school_id', $schoolId);
        });
    }

    $data = $query->orderBy('sisusers.programs', 'desc')->get();

    return response()->json($data);
}
public function removeStdHold(Request $request)
{
    $enrollment = strtoupper($request->input('enrollment'));

    $query = DB::table('pm_mentors_request')
        ->whereRaw('UPPER(mr_enrollment) = ?', [$enrollment])
        ->where('mr_status', 'H');

    if ($request->filled('session_id')) {
        $query->where('mr_sess_id', $request->input('session_id'));
    }

    $updated = $query->update(['mr_status' => 'E']);

if ($updated) {
    return response()->json([
        'success' => true,
        'message' => 'Request put on hold successfully.'
    ]);
} else {
    return response()->json([
        'success' => false,
        'message' => 'Failed to update or already on hold.'
    ], 400);
}
    return response()->json(['message' => 'Hold removed successfully']);
}


public function putOnHoldRequest(Request $request)
{
    $enrollment = strtoupper($request->input('enrollment'));

    $query = DB::table('pm_mentors_request')
        ->whereRaw('UPPER(mr_enrollment) = ?', [$enrollment])
        ->where('mr_status', 'E');

    if ($request->filled('session_id')) {
        $query->where('mr_sess_id', $request->input('session_id'));
    }

    $updated = $query->update(['mr_status' => 'H']);

    if ($updated) {
        return response()->json([
            'success' => true,
            'message' => 'Request put on hold successfully.'
        ]);
    } else {
        return response()->json([
            'success' => false,
            'message' => 'Failed to update or already on hold.'
        ], 400);
    }
}


public function deleteSession($id)
{
    $deleted = DB::table('pm_mentor_session')->where('id', $id)->delete();
    if ($deleted) {
        return response()->json(['success' => true]);
    }
 
    return response()->json(['success' => false, 'message' => 'Delete failed'], 400);
}


public function CoursePutOnHoldRequest(Request $request)
{
    $enrollment = strtoupper($request->input('enrollment'));
    $courseId   = $request->input('course_id');

    $updated = DB::table('PM_MENTORS_REQUEST_DETAILS')
        ->whereRaw('UPPER(MRD_ENROLLMENT) = ?', [$enrollment])
        ->where('MRD_COURSE_ID', $courseId)
        ->where('MRD_STATUS', 'E')   // only update if currently active
        ->update(['MRD_STATUS' => 'H']);

    if ($updated) {
        return response()->json([
            'success' => true,
            'message' => 'Course request put on hold successfully.'
        ]);
    } else {
        return response()->json([
            'success' => false,
            'message' => 'Failed to update or already on hold.'
        ], 400);
    }
}

public function removeHold(Request $request)
{
    $enrollment = strtoupper($request->input('enrollment'));
    $courseId   = $request->input('course_id');

    $updated = DB::table('PM_MENTORS_REQUEST_DETAILS')
        ->whereRaw('UPPER(MRD_ENROLLMENT) = ?', [$enrollment])
        ->where('MRD_COURSE_ID', $courseId)
        ->where('MRD_STATUS', 'H')
        ->update(['MRD_STATUS' => 'E']);

    if ($updated) {
        return response()->json(['message' => 'Hold removed successfully.']);
    }

    return response()->json(['message' => 'Failed to remove hold.'], 400);
}




















/**
 * @return \Illuminate\Support\Collection
 */
private function fetchRequestedCoursesForEnrollment(string $enrollment, int $sessionId)
{
    return DB::table('pm_mentors_request_details')
        ->select(
            'SC_COURSE_TITLE',
            'SC_COURSE_CODE',
            'SC_FACULTY_NAME',
            'SC_COURSE_ID',
            'SC_CO_ID',
            'mrd_student_request',
            'mrd_dean_approval',
            'mrd_enrollment',
            'mrd_status',
            DB::raw("(
                SELECT LISTAGG(as_mentor_id, ',') WITHIN GROUP (ORDER BY as_mentor_id)
                FROM pm_assign_students
                WHERE AS_COURSE_ID = mrd_course_id
                AND LOWER(AS_STUDENT_ID) = LOWER(?)
            ) AS mentor_id")
        )
        ->leftJoin('student_courses', 'pm_mentors_request_details.MRD_CO_ID', '=', 'student_courses.SC_CO_ID')
        ->whereRaw('LOWER(SC_SAI_ENROLLMENT_NO) = LOWER(?)', [$enrollment, $enrollment])
        ->whereRaw('LOWER(mrd_enrollment) = LOWER(?)', [$enrollment])
        ->where('mrd_sess_id', $sessionId)
        ->get();
}

public function getRequestedCourses(Request $request)
{
    $request->validate([
        'enrollment' => 'required|string',
        'session_id' => 'nullable',
        'sess_id' => 'nullable',
    ]);

    $enrollment = $request->input('enrollment');
    $maxSessionId = (int) DB::table('student_courses')->max('SC_CO_SESS_ID');
    $sessionInput = $request->input('session_id') ?? $request->input('sess_id');
    $effectiveSessionId = is_numeric($sessionInput) ? (int) $sessionInput : $maxSessionId;

    if (! $effectiveSessionId) {
        return response()->json(['success' => false, 'message' => 'Session is required.', 'courses' => []], 422);
    }

    $courses = $this->fetchRequestedCoursesForEnrollment($enrollment, $effectiveSessionId);

    return response()->json([
        'success' => true,
        'enrollment' => strtoupper($enrollment),
        'session_id' => $effectiveSessionId,
        'courses' => $courses,
    ]);
}

public function requestCourses(Request $request)
{
        $enrollment = $request->enrollment;
        $maxSessionId = DB::table('student_courses')->max('SC_CO_SESS_ID');
        $sessionInput = $request->input('session_id') ?? $request->input('sess_id');
        $effectiveSessionId = is_numeric($sessionInput) ? (int) $sessionInput : (int) $maxSessionId;

        $courses = $this->fetchRequestedCoursesForEnrollment($enrollment, $effectiveSessionId);

   return view('peer_mentorship.requested_courses', compact('courses', 'enrollment', 'effectiveSessionId'));

}

function updateRemarks(Request $request){

    $upd = "update pm_mentors_request set
             mr_dean_reason='$request->reason',
             mr_dean_approval='$request->actionType',
             mr_dean_entry_time=CURRENT_TIMESTAMP
             where mr_enrollment= '$request->enrollmentNo'";
    //echo $upd;exit;
    return $resUpd = DB::update($upd);
}

public function getCourseMentors(Request $request, $courseId)
{
    $maxSessionId = DB::table('student_courses')->max('SC_CO_SESS_ID');
    $sessionInput = $request->input('sess_id') ?? $request->input('session_id');
    $effectiveSessionId = is_numeric($sessionInput) ? (int) $sessionInput : (int) $maxSessionId;

    if (! $effectiveSessionId) {
        return response()->json([]);
    }

    $sql = "
        SELECT DISTINCT
            pmd.md_enrollment,
            su.full_name,
            pm.m_monthly_hours
        FROM pm_mentors_details pmd
        INNER JOIN pm_mentors pm
            ON LOWER(pm.m_enrollment) = LOWER(pmd.md_enrollment)
           AND pm.m_sess_id = pmd.md_sess_id
           AND (
               pm.m_be_mentor_approval = 'Approved'
               OR pm.m_be_mentor_approval IS NULL
           )
        INNER JOIN sisusers su
            ON LOWER(su.username) = LOWER(pmd.md_enrollment)
        WHERE pmd.md_course_id = :courseId
          AND pmd.md_sess_id = :sessId
    ";

    $mentors = DB::select($sql, [
        'courseId' => $courseId,
        'sessId' => $effectiveSessionId,
    ]);

    return response()->json($mentors);
}




function marksdetailMentor($co_id,$roll_no){
    $criteria = $this->get_criteria_details($co_id);
    $studentRollNo = strtoupper($roll_no);
    //  echo $roll_no;exit;
    $cr = count($criteria);
    $a_id=$criteria[0]->a_id;

    $sqlMarksDetails="SELECT *
    FROM COURSE_ASSESSMENT_MARKS
    LEFT JOIN COURSE_CRITERIA_DETAILS
    ON COURSE_ASSESSMENT_MARKS.COURSE_CRITERIA_DETAIL_ID = COURSE_CRITERIA_DETAILS.id
    left join courses_offered on courses_offered.co_id = COURSE_ASSESSMENT_MARKS.co_id
    left join courses_two on courses_offered.co_course_id = courses_two.course_id
    left join grade_history on courses_offered.co_id = gh_co_id and gh_sai_enrollment_no = '".$studentRollNo."'
    LEFT JOIN assessments ON COURSE_ASSESSMENT_MARKS.assessment_id           = assessments.id
    WHERE COURSE_ASSESSMENT_MARKS.SAI_ENROLLMENT_NO      = '".$studentRollNo."'

    AND COURSE_ASSESSMENT_MARKS.co_id                    =$co_id
    AND CAM_STATUS                                       ='E'
    ORDER BY COURSE_ASSESSMENT_MARKS.assessment_id,cam_number ";
  // echo $sqlMarksDetails;exit;
    $rsMarksDetails = DB::select($sqlMarksDetails);

    $weigtage = '';//$this->weigtage($co_id,$studentRollNo);
    $total_assessments = count($rsMarksDetails);
    return view('peer_mentorship.marksDetail',
        [
            'co_id'=>$co_id,
            'rsMarksDetails'=>$rsMarksDetails,
            'total_assessments'=>$total_assessments,
            'ww' => $weigtage,
            'criteria' =>$criteria,
            'cr'=>$cr
        ]);
}

function get_criteria_details($co_id){

    $criteria_sql="SELECT
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
               // echo  $criteria_sql;exit;
    $rsCriteria = DB::select($criteria_sql);
    return $rsCriteria;
}


function weigtage($co_id,$studentRollNo){

    $summary_sql="SELECT
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
  AND (gh_co_id              = $co_id
  OR parent_co_id            = $co_id)
  AND ROWNUM                 = 1
  AND gh_exam_fsubmit        =1
  ) AS grading_type
                FROM
                course_marks_summaries
                LEFT JOIN assessments ON course_marks_summaries.assessment_id = assessments.id
                WHERE
                sai_enrollment_no = '".$studentRollNo."'
                AND
                co_id = $co_id
                ORDER BY assessments.id";
     //  echo $summary_sql;exit;
        $rssummary_sql = DB::select($summary_sql);
return $rssummary_sql;
    }

    public function store(Request $request)
    {
    
        // dd($request->all());
        // Validate incoming request
        $validatedData = $request->validate([
            'sessions' => 'required|array',
            'sessions.*.date' => 'required|date',
           
        ]);
    
        $sessions = $validatedData['sessions'];
        $createdSessions = 0;
        $courseId = $request->course_id;
        $mentorId = $request->mentor_id;
        $studentId = $request->student_id;
        // $remarks = $request->remarks;
        $maxSessionId = DB::table('student_courses')->max('SC_CO_SESS_ID');
    
    
        foreach ($sessions as $session) {
            $sessionDate = Carbon::parse($session['date']);
           
            $sessionDate = Carbon::parse($sessionDate); // e.g. '2025-07-03'
            $weekdayName = $sessionDate->format('l'); // Outputs: "Monday", "Tuesday", etc.
            DB::table('pm_mentor_session')->insert([
                  'pms_mentor_id' => $mentorId,
                  'pms_student_id' => $studentId,
                  'pms_from_time' => $session['from_time'],
                  'pms_to_time' => $session['to_time'],
                  'pms_weekday' => $weekdayName,
                  'pms_course_id' => $courseId,
                  'pms_date' => $sessionDate,
                  'rescedual_remarks' => 'Manually added',
                  'pm_attendance' => 'Pending',
                  'pms_sess_id' => $maxSessionId

              ]);
    
           
    
            $createdSessions++;
        }
        return redirect()->back()->with('success', 'Schedule created successfully');
    
       
    } 






    public function introSession(Request $request)
    {
        $maxSessionId = DB::table('student_courses')->max('SC_CO_SESS_ID');
        $sessionInput = $request->input('sess_id') ?? $request->input('session_id');
        $effectiveSessionId = is_numeric($sessionInput) ? (int) $sessionInput : (int) $maxSessionId;
        $schoolId = $request->input('school_id');
        $statusFilter = $request->input('attendance');

        $query = DB::table('pm_mentor_session')
            ->select(
                'pm_mentor_session.id',
                'pms_course_id as course_id',
                'pms_md_id AS mentor_id',
                'pms_mentor_id AS mentor_m_id',
                'pms_student_id AS student_id',
                'pms_from_time AS from_time',
                'pm_attendance',
                'pms_to_time AS to_time',
                'pms_weekday AS weekday',
                'pms_sess_id AS session_id',
                DB::raw('(SELECT course_cd_name FROM courses_two WHERE courses_two.course_id = pm_mentor_session.pms_course_id) AS course_name'),
                'pms_date AS session_date',
                'pms_entry_datetime AS entry_timestamp'
            )
            ->where('pms_sess_id', $effectiveSessionId);

        if ($schoolId !== null && $schoolId !== '') {
            $query->whereExists(function ($sub) use ($schoolId) {
                $sub->select(DB::raw(1))
                    ->from('students_apply_info as sai_sch')
                    ->whereRaw('LOWER(sai_sch.sai_enrollment_no) = LOWER(pm_mentor_session.pms_student_id)')
                    ->where('sai_sch.sai_sdpo_school_id', $schoolId);
            });
        }

        if ($statusFilter !== null && $statusFilter !== '' && $statusFilter !== 'All') {
            if (strcasecmp($statusFilter, 'Pending') === 0) {
                $query->where(function ($q) {
                    $q->whereNull('pm_attendance')
                        ->orWhere('pm_attendance', '')
                        ->orWhereRaw("LOWER(TRIM(pm_attendance)) = 'pending'");
                });
            } else {
                $query->whereRaw('LOWER(TRIM(pm_attendance)) = ?', [strtolower($statusFilter)]);
            }
        }

        $sessions = $query->orderBy('pms_date', 'DESC')->get();

        $countBase = function () use ($effectiveSessionId, $schoolId) {
            $q = DB::table('pm_mentor_session')->where('pms_sess_id', $effectiveSessionId);
            if ($schoolId !== null && $schoolId !== '') {
                $q->whereExists(function ($sub) use ($schoolId) {
                    $sub->select(DB::raw(1))
                        ->from('students_apply_info as sai_sch')
                        ->whereRaw('LOWER(sai_sch.sai_enrollment_no) = LOWER(pm_mentor_session.pms_student_id)')
                        ->where('sai_sch.sai_sdpo_school_id', $schoolId);
                });
            }

            return $q;
        };

        $countsView = [
            'total' => $countBase()->count(),
            'pending' => $countBase()->where(function ($q) {
                $q->whereNull('pm_attendance')
                    ->orWhere('pm_attendance', '')
                    ->orWhereRaw("LOWER(TRIM(pm_attendance)) = 'pending'");
            })->count(),
            'present' => $countBase()->whereRaw("LOWER(TRIM(pm_attendance)) = 'present'")->count(),
            'absent' => $countBase()->whereRaw("LOWER(TRIM(pm_attendance)) = 'absent'")->count(),
        ];

        $ressqlTimeslots = DB::select('select * from timetable_slots');

        return view('peer_mentorship.introductory_session', [
            'sessions' => $sessions,
            'ressqlTimeslots' => $ressqlTimeslots,
            'currentSessionId' => $effectiveSessionId,
            'schoolId' => $schoolId,
            'attendanceFilter' => $statusFilter ?? 'All',
            'sessionCounts' => $countsView,
        ]);
    }

    function pmTimttable(Request $request){
       // dd($request->all());
        $weeksDuaration = $request->get('weeks');
        $dayname = $request->get('dayname');
        $from_time = $request->get('fromtime');
        $to_time = $request->get('totime');
    }


 
    function reschTimttable(Request $request){
        $request->validate([
            'rowID' => 'required|integer',
            'date' => 'required|date|after_or_equal:today',
            'remarks' => 'nullable|string|max:500',
            'from_time' => 'nullable|string|max:20',
            'to_time' => 'nullable|string|max:20',
        ]);

        $rowId = $request->get('rowID');
        $date = $request->get('date');
        $remarks = $request->get('remarks');
        $fromTime = trim((string) $request->input('from_time', ''));
        $toTime = trim((string) $request->input('to_time', ''));

        if (($fromTime !== '' && $toTime === '') || ($fromTime === '' && $toTime !== '')) {
            return response()->json([
                'success' => false,
                'message' => 'Please choose both From and To time, or leave both empty to keep current.',
            ], 422);
        }

        if ($fromTime !== '' && $toTime !== '') {
            $fromMin = self::timeSlotLabelToMinutes($fromTime);
            $toMin = self::timeSlotLabelToMinutes($toTime);
            if ($fromMin === null || $toMin === null) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid time format selected.',
                ], 422);
            }
            if ($fromMin >= $toMin) {
                return response()->json([
                    'success' => false,
                    'message' => 'From time must be earlier than To time.',
                ], 422);
            }
        }

        $maxSessionId = DB::table('student_courses')->max('SC_CO_SESS_ID');

        $session = DB::table('pm_mentor_session')->where('id', $rowId)->first();
        if (!$session) {
            return response()->json(['success' => false, 'message' => 'Session not found.'], 404);
        }

        $wasAbsent = strcasecmp(trim((string) ($session->pm_attendance ?? '')), 'Absent') === 0;

        $updatePayload = [
            'pms_sess_id' => $maxSessionId,
            'pms_date' => $date,
            'rescedual_remarks' => $remarks,
            'pm_attendance' => 'Pending',
        ];

        if ($fromTime !== '') {
            $updatePayload['pms_from_time'] = $fromTime;
        }
        if ($toTime !== '') {
            $updatePayload['pms_to_time'] = $toTime;
        }

        DB::table('pm_mentor_session')
            ->where('id', $rowId)
            ->update($updatePayload);

        if ($wasAbsent) {
            DB::table('pm_session_feedback')
                ->where('pmsf_pms_id', $rowId)
                ->delete();
        }

        $std = DB::table('pm_mentor_session')
            ->select('pms_mentor_id', 'pms_student_id', 'pms_from_time', 'pms_to_time')
            ->where('id', $rowId)
            ->first();

        $timingLine = $std ? (" from {$std->pms_from_time} to {$std->pms_to_time}") : '';
        $email = $std->pms_mentor_id . '@bnu.edu.pk';
        $subject = 'Peer Mentorship introductory session re-scheduled';
        $message = "Your introductory session is re-scheduled on {$date}{$timingLine} in SLASS 002 double basement. Please visit the Peer Mentorship section on your CMS portal.";
        $sender = 'Peer mentorship team';
        
        try {
            DB::statement("BEGIN send_mail_one_by_one(:to, :subject, :message, :sender); END;", [
                'to' => $email,
                'subject' => $subject,
                'message' => $message,
                'sender' => $sender
            ]);
            return response()->json([
                'success' => true,
                'message' => 'success: '
            ], 200);


        } catch (\Exception $e) {
            // Log::error('Email Procedure Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    $email = $std->pms_student_id . '@bnu.edu.pk';
    
        try {
            DB::statement("BEGIN send_mail_one_by_one(:to, :subject, :message, :sender); END;", [
                'to' => $email,
                'subject' => $subject,
                'message' => $message,
                'sender' => $sender
            ]);
            return response()->json([
                'success' => true,
                'message' => 'success: '
            ], 200);


        } catch (\Exception $e) {
            // Log::error('Email Procedure Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }





}


    public function createBiweeklySessions(Request $request, $weeksToLoop = 0)
    {

        // dd($request->all()  );
        $weeksToLoop = $request->weeksToLoop;
        //  dd($rowId);
        // Parameters
        $remarks = "Scheduled remarks"; // Your remarks
        $rowId =$request->row_id;
        $session = DB::table('pm_mentor_session')->where('id', $rowId)->first();
        $maxSessionId = DB::table('student_courses')->max('SC_CO_SESS_ID');
        // dd($session);
        if (!$session) {
            return response()->json(['status' => 'error', 'message' => 'Session not found.'], 404);
        }

        // Extract pms_weekday and map it to weekday number
        $pmsWeekday = $session->pms_weekday; // Example: "Monday"
        $weekdayMap = [
            "Sunday" => 0,
            "Monday" => 1,
            "Tuesday" => 2,
            "Wednesday" => 3,
            "Thursday" => 4,
            "Friday" => 5,
            "Saturday" => 6,
        ];

        if (!isset($weekdayMap[$pmsWeekday])) {
            return response()->json(['status' => 'error', 'message' => 'Invalid weekday in database.'], 400);
        }

        $weekdayNumber = $weekdayMap[$pmsWeekday];

        // Set the start date (today or any desired date)
        $startDate = Carbon::now();

        // Align start date to the required weekday
        while ($startDate->dayOfWeek != $weekdayNumber) {
            $startDate->addDay();
        }

        // Loop to insert biweekly sessions
        for ($i = 0; $i < $weeksToLoop; $i++) {
            // Calculate session date
            $sessionDate = $startDate->copy()->addWeeks($i * 2)->toDateString();

            // Insert query
            DB::insert("
                INSERT INTO pm_mentor_session (
                    pms_mentor_id, pms_sess_id, pms_student_id,
                    pms_from_time, pms_to_time, pms_weekday,
                    pms_course_id, pms_date, rescedual_remarks, pm_attendance
                )
                SELECT
                    pms_mentor_id, $maxSessionId, pms_student_id,
                    pms_from_time, pms_to_time, pms_weekday,
                    pms_course_id, ?, ?, 'Pending'
                FROM pm_mentor_session
                WHERE id = ?
            ", [$sessionDate, $remarks, $rowId]);
        }




///working w
             $std = DB::table('pm_mentor_session')
            ->select('pms_mentor_id', 'pms_student_id')
            ->where('id', $rowId)
            ->first();



            //    dd($std);
                $email = $std->pms_mentor_id . '@bnu.edu.pk';
            $subject = 'Peer Mentorship introductory session re-scheduled';
            $message = "Your sessions are scheduled in SLASS 002 double basement. Please visit the Peer Mentorship section on your CMS portal.";
            $sender = 'Peer mentorship team';
            
            try {
                DB::statement("BEGIN send_mail_one_by_one(:to, :subject, :message, :sender); END;", [
                    'to' => $email,
                    'subject' => $subject,
                    'message' => $message,
                    'sender' => $sender
                ]);
                return response()->json([
                    'success' => true,
                    'message' => 'success: '
                ], 200);


            } catch (\Exception $e) {
                // Log::error('Email Procedure Error: ' . $e->getMessage());
                return response()->json([
                    'success' => false,
                    'message' => 'Error: ' . $e->getMessage()
                ], 500);
            }
        $email = $std->pms_student_id . '@bnu.edu.pk';
        
            try {
                DB::statement("BEGIN send_mail_one_by_one(:to, :subject, :message, :sender); END;", [
                    'to' => $email,
                    'subject' => $subject,
                    'message' => $message,
                    'sender' => $sender
                ]);
                return response()->json([
                    'success' => true,
                    'message' => 'success: '
                ], 200);


            } catch (\Exception $e) {
                // Log::error('Email Procedure Error: ' . $e->getMessage());
                return response()->json([
                    'success' => false,
                    'message' => 'Error: ' . $e->getMessage()
                ], 500);
            }


  

        return response()->json(['status' => 'success', 'message' => 'Biweekly sessions created successfully!']);
    }














    public function getFeedback($sessionId)
    {
        $mentorRows = DB::table('pm_session_feedback as fb')
            ->leftJoin('timetable_slots as ts_from', 'ts_from.ts_id', '=', 'fb.pmsf_from_time')
            ->leftJoin('timetable_slots as ts_to', 'ts_to.ts_id', '=', 'fb.pmsf_to_time')
            ->select([
                'fb.id as feedback_id',
                'fb.pmsf_pms_id as pms_id',
                'fb.pmsf_remarks as remarks',
                'fb.pmsf_hours_required as hours_required',
                'fb.pmsf_entry_datetime as entry_datetime',
                'fb.pmsf_date as proposed_date',
                'fb.pmsf_from_time as from_time_id',
                'fb.pmsf_to_time as to_time_id',
                'ts_from.ts_name as from_time',
                'ts_to.ts_name as to_time',
                'fb.pmsf_admin_approval_status as approval_status',
            ])
            ->where('fb.pmsf_pms_id', $sessionId)
            ->where('fb.pmsf_feedback_from', 'mentor')
            ->orderBy('fb.pmsf_date')
            ->orderBy('fb.pmsf_from_time')
            ->get();

        $mentorSummary = null;
        if ($mentorRows->isNotEmpty()) {
            // Prefer the mentor summary row (remarks/hours without a proposed date).
            $summaryRow = $mentorRows->first(function ($row) {
                return empty($row->proposed_date);
            }) ?? $mentorRows->first();
            $mentorSummary = [
                'remarks' => $summaryRow->remarks,
                'hours_required' => $summaryRow->hours_required,
                'entry_datetime' => $summaryRow->entry_datetime,
            ];
        }

        // Mentor feedback may include a summary row (remarks/hours only) with no date — exclude from proposed list.
        $proposedSessions = $mentorRows
            ->filter(function ($row) {
                return !empty($row->proposed_date);
            })
            ->map(function ($row) {
                $status = strtolower(trim($row->approval_status ?? 'pending'));
                if ($status === '' || !in_array($status, ['pending', 'approved', 'declined'], true)) {
                    $status = 'pending';
                }
                return [
                    'feedback_id' => $row->feedback_id,
                    'date' => $row->proposed_date,
                    'from_time' => $row->from_time ?: $row->from_time_id,
                    'to_time' => $row->to_time ?: $row->to_time_id,
                    'approval_status' => $status,
                    'locked' => in_array($status, ['approved', 'declined'], true),
                ];
            })
            ->values();

        $studentFeedback = DB::table('pm_session_feedback')
            ->select('pmsf_remarks as remarks', 'pmsf_satisfaction as satisfaction', 'pmsf_entry_datetime as entry_datetime')
            ->where('pmsf_pms_id', $sessionId)
            ->where('pmsf_feedback_from', 'student')
            ->first();

        return response()->json([
            'mentor' => $mentorSummary,
            'proposed_sessions' => $proposedSessions,
            'student' => $studentFeedback,
        ]);
    }

    /**
     * Approve / decline mentor-proposed session dates from feedback.
     * For each "approved" row, a new pm_mentor_session is created based on the source session.
     */
    public function saveFeedbackApprovals(Request $request)
    {
        $request->validate([
            'session_id' => 'required|integer',
            'decisions' => 'required|array',
            'decisions.*.feedback_id' => 'required|integer',
            'decisions.*.status' => 'required|in:approved,declined,pending',
        ]);

        $sessionId = (int) $request->input('session_id');
        $decisions = $request->input('decisions');

        $baseSession = DB::table('pm_mentor_session')->where('id', $sessionId)->first();
        if (!$baseSession) {
            return response()->json(['success' => false, 'message' => 'Source session not found.'], 404);
        }

        $maxSessionId = DB::table('student_courses')->max('SC_CO_SESS_ID');

        $weekdayMap = [
            0 => 'Sunday', 1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday',
            4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday',
        ];

        $created = 0;
        $declined = 0;
        $skipped = 0;
        $errors = [];

        DB::beginTransaction();
        try {
            foreach ($decisions as $decision) {
                $feedbackId = (int) $decision['feedback_id'];
                $status = strtolower($decision['status']);

                $feedback = DB::table('pm_session_feedback as fb')
                    ->leftJoin('timetable_slots as ts_from', 'ts_from.ts_id', '=', 'fb.pmsf_from_time')
                    ->leftJoin('timetable_slots as ts_to', 'ts_to.ts_id', '=', 'fb.pmsf_to_time')
                    ->select([
                        'fb.id',
                        'fb.pmsf_date as proposed_date',
                        'fb.pmsf_from_time as from_time_id',
                        'fb.pmsf_to_time as to_time_id',
                        'ts_from.ts_name as from_time',
                        'ts_to.ts_name as to_time',
                        'fb.pmsf_admin_approval_status as current_status',
                    ])
                    ->where('fb.id', $feedbackId)
                    ->where('fb.pmsf_pms_id', $sessionId)
                    ->first();

                if (!$feedback) {
                    $skipped++;
                    continue;
                }

                $currentStatus = strtolower(trim($feedback->current_status ?? 'pending'));
                if ($currentStatus === '' || $currentStatus === 'null') {
                    $currentStatus = 'pending';
                }
                if (in_array($currentStatus, ['approved', 'declined'], true)) {
                    if ($status !== $currentStatus) {
                        $errors[] = "Slot #{$feedbackId} is already {$currentStatus} and cannot be changed.";
                    }
                    $skipped++;
                    continue;
                }

                DB::table('pm_session_feedback')
                    ->where('id', $feedbackId)
                    ->update(['pmsf_admin_approval_status' => $status]);

                if ($status === 'declined') {
                    $declined++;
                    continue;
                }
                if ($status !== 'approved') {
                    continue;
                }

                $proposedDate = $feedback->proposed_date;
                if (!$proposedDate) {
                    $errors[] = "Feedback #{$feedbackId} has no proposed date.";
                    continue;
                }

                $carbonDate = \Carbon\Carbon::parse($proposedDate);
                $weekdayName = $weekdayMap[$carbonDate->dayOfWeek] ?? $baseSession->pms_weekday;

                $exists = DB::table('pm_mentor_session')
                    ->where('pms_mentor_id', $baseSession->pms_mentor_id)
                    ->where('pms_student_id', $baseSession->pms_student_id)
                    ->where('pms_course_id', $baseSession->pms_course_id)
                    ->where('pms_date', $carbonDate->toDateString())
                    ->where('pms_from_time', $feedback->from_time)
                    ->where('pms_to_time', $feedback->to_time)
                    ->exists();

                if ($exists) {
                    $skipped++;
                    continue;
                }

                DB::table('pm_mentor_session')->insert([
                    'pms_mentor_id' => $baseSession->pms_mentor_id,
                    'pms_student_id' => $baseSession->pms_student_id,
                    'pms_from_time' => $feedback->from_time ?: $baseSession->pms_from_time,
                    'pms_to_time' => $feedback->to_time ?: $baseSession->pms_to_time,
                    'pms_weekday' => $weekdayName,
                    'pms_sess_id' => $maxSessionId,
                    'pms_session_type' => 'mentoring',
                    'pms_course_id' => $baseSession->pms_course_id,
                    'pms_date' => $carbonDate->toDateString(),
                    'pms_entry_datetime' => now(),
                    'pm_attendance' => 'Pending',
                ]);
                $created++;
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error saving approvals: ' . $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'success' => true,
            'created' => $created,
            'declined' => $declined,
            'skipped' => $skipped,
            'errors' => $errors,
            'message' => "Created {$created}, declined {$declined}" . ($skipped ? ", skipped {$skipped} (duplicate / invalid)" : '') . '.',
        ]);
    }






    // interview

    public function index()
    {
         $schedules = DB::table('interview_schedules')
            ->select('IS_INTERVIEW_TIME', 'IS_ENROLLMENT_NO', 'interview_schedules.*')
            ->where('IS_INTERVIEW_DATE', '>', DB::raw("TO_DATE('2025-06-30', 'YYYY-MM-DD')"))
            ->orderBy('id', 'desc')
            ->get();
        return view('peer_mentorship.interview_schedules', compact('schedules'));
    }

    public function markInterviewed($id)
    {
        DB::table('interview_schedules')
            ->where('id', $id)
            ->update(['interviewed' => 1]); // assuming column name is 'interviewed'

        return redirect()->route('interview_schedules.index')
            ->with('success', 'Interview marked as interviewed successfully!');
    }

    private static function timeSlotLabelToMinutes(?string $label): ?int
    {
        if ($label === null || trim($label) === '') {
            return null;
        }
        $label = trim($label);
        if (!preg_match('/^(\d{1,2}):(\d{2})\s*(AM|PM)$/i', $label, $m)) {
            return null;
        }
        $h = (int) $m[1];
        $min = (int) $m[2];
        $ap = strtoupper($m[3]);
        if ($ap === 'PM' && $h !== 12) {
            $h += 12;
        }
        if ($ap === 'AM' && $h === 12) {
            $h = 0;
        }

        return $h * 60 + $min;
    }

}
