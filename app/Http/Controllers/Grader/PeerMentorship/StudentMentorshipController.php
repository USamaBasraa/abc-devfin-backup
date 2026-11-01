<?php

namespace App\Http\Controllers\Grader\PeerMentorship;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;

class StudentMentorshipController extends Controller
{

    public function mentorIntimation(Request $request)
    {
        $request->validate([
            'mentor_enrollment' => 'required|string',
            'student_enrollment' => 'required|string',
            'course_id' => 'required',
            'week_day' => 'required|string',
            'from_time' => 'required|string',
            'to_time' => 'required|string',
        ]);

        $mentorEnrollment = $request->mentor_enrollment;
        $studentEnrollment = $request->student_enrollment;
        $courseId = $request->course_id;
        $weekDay = $request->week_day;
        $fromTime = trim($request->from_time);
        $toTime = trim($request->to_time);

        $currentDate = Carbon::now();
        $nextWeekDay = $currentDate->copy()->next($weekDay);

        $sessionIdResult = DB::table('student_courses')
            ->whereRaw('LOWER(sc_sai_enrollment_no) = LOWER(?)', [$studentEnrollment])
            ->where('sc_enrollment_status', 1)
            ->max('sc_co_sess_id');

        if (!$sessionIdResult) {
            return response()->json([
                'success' => false,
                'message' => 'Session could not be determined for this student.',
            ], 400);
        }

        if ($this->introSessionExists($mentorEnrollment, $studentEnrollment, $courseId, $sessionIdResult, $weekDay, $fromTime, $toTime)) {
            return response()->json([
                'success' => false,
                'duplicate' => true,
                'message' => 'Intimation already sent for this mentor, course, and timeslot.',
            ], 200);
        }

        DB::beginTransaction();

        try {
            if (!$this->studentAssignmentExists($mentorEnrollment, $studentEnrollment, $courseId, $sessionIdResult)) {
                DB::table('pm_assign_students')->insert([
                    'as_mentor_id' => $mentorEnrollment,
                    'as_student_id' => $studentEnrollment,
                    'as_course_id' => $courseId,
                    'as_sess_id' => $sessionIdResult,
                    'as_status' => 'E',
                ]);
            }

            DB::table('pm_mentor_session')->insert([
                'pms_mentor_id' => $mentorEnrollment,
                'pms_student_id' => $studentEnrollment,
                'pms_from_time' => $fromTime,
                'pms_to_time' => $toTime,
                'pms_weekday' => $weekDay,
                'pms_sess_id' => $sessionIdResult,
                'pms_session_type' => 'introduction',
                'pms_course_id' => $courseId,
                'pms_date' => $nextWeekDay->toDateString(),
                'pms_entry_datetime' => now(),
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Error inserting request. Please try again later.',
            ], 500);
        }

        $subject = 'Peer Mentorship introductory session scheduled';
        $message = "Your introductory session is scheduled on {$weekDay} from {$fromTime} to {$toTime} in SLASS 002. Please visit the Peer Mentorship section on your CMS portal.";
        $sender = 'Peer mentorship team';

        try {
            foreach ([$mentorEnrollment, $studentEnrollment] as $enrollment) {
                DB::statement('BEGIN send_mail_one_by_one(:to, :subject, :message, :sender); END;', [
                    'to' => $enrollment . '@bnu.edu.pk',
                    'subject' => $subject,
                    'message' => $message,
                    'sender' => $sender,
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Session saved but email failed: ' . $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'success' => true,
            'duplicate' => false,
            'message' => 'Mentorship request submitted successfully.',
        ]);
    }

    private function introSessionExists(
        string $mentorEnrollment,
        string $studentEnrollment,
        $courseId,
        $sessionId,
        string $weekDay,
        string $fromTime,
        string $toTime
    ): bool {
        return DB::table('pm_mentor_session')
            ->whereRaw('LOWER(pms_mentor_id) = LOWER(?)', [$mentorEnrollment])
            ->whereRaw('LOWER(pms_student_id) = LOWER(?)', [$studentEnrollment])
            ->where('pms_course_id', $courseId)
            ->where('pms_sess_id', $sessionId)
            ->whereRaw('LOWER(TRIM(pms_weekday)) = LOWER(TRIM(?))', [$weekDay])
            ->whereRaw('TRIM(pms_from_time) = ?', [$fromTime])
            ->whereRaw('TRIM(pms_to_time) = ?', [$toTime])
            ->where('pms_session_type', 'introduction')
            ->exists();
    }

    private function studentAssignmentExists(
        string $mentorEnrollment,
        string $studentEnrollment,
        $courseId,
        $sessionId
    ): bool {
        return DB::table('pm_assign_students')
            ->whereRaw('LOWER(as_mentor_id) = LOWER(?)', [$mentorEnrollment])
            ->whereRaw('LOWER(as_student_id) = LOWER(?)', [$studentEnrollment])
            ->where('as_course_id', $courseId)
            ->where('as_sess_id', $sessionId)
            ->exists();
    }






    public function submitRequest(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'enrollment_no' => 'required|string',
            'course_ids' => 'required|array',
            'course_ids.*.co_id' => 'required|integer',
            'course_ids.*.course_id' => 'required|integer',
            'remarks' => 'required|string',
        ]);

        // Extract request data
        $enrollmentNo = strtolower($request->input('enrollment_no')); // Ensure case-insensitivity
        $courseIds = $request->input('course_ids');
        $remarks = $request->input('remarks');

        // Retrieve the session ID using a secure and efficient query
        $sessionIdResult = DB::table('student_courses')
            ->whereRaw('lower(sc_sai_enrollment_no) = ?', [$enrollmentNo])
            ->where('sc_enrollment_status', 1)
            ->max('sc_co_sess_id');

        // Check if the session ID was retrieved
        if (!$sessionIdResult) {
            return response()->json([
                'message' => 'Session ID could not be determined for the provided enrollment number.',
            ], 400);
        }

        $username = auth()->user()->id; // Authenticated user
        $currentDatetime = now('Asia/Karachi'); // Current datetime in Karachi timezone

        // Start DB transaction
        DB::beginTransaction();

        try {
            // Insert into the PM_MENTORS_REQUEST table and get the last inserted ID
            $lastInsertedId = DB::table('PM_MENTORS_REQUEST')->insertGetId([
                'MR_ENROLLMENT' => $enrollmentNo,
                'MR_ENTRY_USER_ID' => $username,
                'MR_SESS_ID' => $sessionIdResult,
                'MR_DEAN_APPROVAL' => 'Approved',
                'MR_DEAN_REASON' => 'Student Progress Request',
                'MR_STATUS' => 'E',
            ]);

            // Insert each course into the PM_MENTORS_REQUEST_DETAILS table
            foreach ($courseIds as $course) {
                DB::table('PM_MENTORS_REQUEST_DETAILS')->insert([
                    'MRD_ID' => $lastInsertedId, // Correct field to reference PM_MENTORS_REQUEST
                    'MRD_ENROLLMENT' => $enrollmentNo, // Enrollment number
                    'MRD_ENTRY_DATETIME' => $currentDatetime,
                    'MRD_STUDENT_REQUEST' => 'From Student Performance Tracking Portal: ' .$remarks ,
                    'MRD_SESS_ID' => $sessionIdResult, // Correct session ID
                    'MRD_CO_ID' => $course['co_id'], // Correct field for course ID
                    'MRD_COURSE_ID' => $course['course_id'], // Correct field for course ID
                    'MRD_STATUS' => 'E', // Status
                ]);
            }

            // Commit transaction if everything is successful
            DB::commit();

            // Return success response
            return response()->json([
                'message' => 'Mentorship request submitted successfully.',
            ]);

        } catch (\Exception $e) {
            // Rollback transaction on error
            DB::rollBack();
            return response()->json([
                'message' => 'Error inserting request. Please try again later.',
                'error'=>$e->getMessage(),
            ], 500);
        }
    }

    public function scedualTime(Request $request)
    {


        $request->validate([
            'enrollment_no' => 'required|string',
            // 'mentoring_type' => 'required|string',
           'course_id' => 'required|array',
            // 'course_id.*' => 'integer',
        ]);

        $enrollmentNo = str_replace('enrollment_no=', '', $request->enrollment_no) ;
        $courseIds = $request->course_id;
        $mentoring_type = $request->mentoring_type;
        $fromtiming =  $request->fromtiming;
        $totiming =  $request->totiming;
        $weekdays=  $request->weekdays;
        $remarks = $request->remarks;
        $m_monthly_hours = $request->hourse;
        $entryUserId = auth()->user()->id;
        $maxSessionId = DB::table('student_courses')->max('SC_CO_SESS_ID');
       // dd($request->all());

//dd($entryUserId);
        //try {
           // DB::beginTransaction();
           //dd($request->course_id);
            // Step 1: Insert into pm_mentors table

            $result = DB::table('pm_mentors')
                ->select('M_ENROLLMENT')
                ->whereRaw('LOWER(M_ENROLLMENT) = LOWER(?)', [$enrollmentNo])
                ->first();

            //   dd($result);

            if ($result) {
              //  dd('Mentore not added');
                //echo "Student exists: ";
            } else {

                DB::table('pm_mentors')->insertGetId([
                    'm_enrollment' =>  $enrollmentNo,
                    'm_entry_user_id' => $entryUserId,
                    'm_mentor_type' => 'academic',
                    'm_entry_datetime' => now(),
                    'm_monthly_hours' =>  $m_monthly_hours,
                    'm_sess_id' => $maxSessionId,
                    'M_COMMITTEE_REMARKS' => $remarks,
                    'm_status' => 'E',
                ]);
                DB::commit();
            //   dd('Mentore addesd');
               // echo "Student does not exist.";
            }

            $pmd_id =0;
            $id = DB::table('pm_mentors_details')
                ->whereRaw('LOWER(md_enrollment) = LOWER(?)', [$enrollmentNo])
                ->where('md_co_id', $request->course_id)
                ->value('id');


                if ($id == null){
                    // if ($request->has('course_ids') && is_array($request->course_id)) {
                        foreach ($request->course_id as $courseId) {
                            // dd($courseId);
                            // Fetch the course details from COURSES_OFFERED table
                            $COURSES_OFFERED = DB::table('COURSES_OFFERED')
                                ->where('CO_ID', $courseId)
                                ->first(); // Fetch a single record instead of a collection
                            //    dd($courseId);
                            if ($COURSES_OFFERED) {
                                // dd($courseId, $COURSES_OFFERED->co_course_id);
                                // Insert the new mentor details
                                $newId = DB::table('pm_mentors_details')->insertGetId([
                                    'md_enrollment' => $enrollmentNo,
                                    'md_co_id' => $courseId,
                                    'md_course_id' => $COURSES_OFFERED->co_course_id,
                                    'md_time_alot' => 0, // Default time allocation
                                    'md_entry_datetime' => now(),
                                    'md_sess_id' => $maxSessionId,
                                    'md_status' => 'Active',
                                ]);
                            //    dd($newId );
                                // You can perform additional actions with $newId if needed
                                $id = $newId;
                                $pmd_id = $newId;
                            }
                        }
                        DB::beginTransaction();
                        try {
                            foreach ($weekdays as $i => $weekday) {
                                // Insert mentor timing details
                                $timingId = DB::table('PM_MENTOR_TIMING')->insertGetId([
                                    // 'PMT_MD_ID' => $pmd_id,                      // $newId should be defined earlier
                                    'PMT_FROM_TIME' => $fromtiming[$i],         // Assuming timing is an array of ['from' => 'time', 'to' => 'time']
                                    'PMT_TO_TIME' => $totiming[$i],             // Provide proper 'to' timing
                                    'pmt_sess_id'=> $maxSessionId,
                                    'pmt_weekday' => $weekday,                     // Weekday value
                                    'pmt_enrollment_no' => $enrollmentNo
                                ]);
                            }

                            DB::commit();

                            return response()->json(['success' => 'Added'], 200);

                        }
                        catch (\Exception $e) {
                            // Rollback in case of an error
                          //  DB::rollBack();

                            // Handle the exception
                            return response()->json(['error' => $e->getMessage()], 500);
                        }

                        return  redirect('/mentors_list');

        }else{
            DB::beginTransaction();
            try {
                foreach ($weekdays as $i => $weekday) {
                    // Insert mentor timing details
                    $timingId = DB::table('PM_MENTOR_TIMING')->insertGetId([
                        // 'PMT_MD_ID' => $pmd_id,                      // $newId should be defined earlier
                        'PMT_FROM_TIME' => $fromtiming[$i],         // Assuming timing is an array of ['from' => 'time', 'to' => 'time']
                        'PMT_TO_TIME' => $totiming[$i],             // Provide proper 'to' timing
                        'pmt_sess_id'=> $maxSessionId,
                        'pmt_weekday' => $weekday,                     // Weekday value
                        'pmt_enrollment_no' => $enrollmentNo
                    ]);
                }
                //   dd($timingId);

                // Commit the transaction
                DB::commit();


                return response()->json(['success' => 'Added'], 200);
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

        }

            //    return response()->json(['success' => true], 200);
                return  redirect('/mentors_list');




}

}
