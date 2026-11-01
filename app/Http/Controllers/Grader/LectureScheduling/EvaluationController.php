<?php

namespace App\Http\Controllers\Grader\LectureScheduling;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Grader\CourseCriterias;
use App\Models\Grader\CourseCriteriaDetails;
use Illuminate\Support\Facades\DB; // ✅ Correct
use Storage;
use Illuminate\Support\Facades\Session;
use Hypweb\Flysystem\GoogleDrive\GoogleDriveAdapter;
use Google_Client;
use Illuminate\Support\Facades\File;
use App\Models\Grader\SendSms;
use Illuminate\Support\Facades\Http;

class EvaluationController extends Controller
{
    public function index($get_co_id)
    {   
    
        // This not allowing TA to enter in grading
        if (auth()->user()->group_id != 825) {
            $co_id = my_simple_crypt($get_co_id, 'd');
           //echo $co_id;exit();
            $courseCriteria = new CourseCriterias();
            $get_criteria = $courseCriteria->fetchCoursesCriteria($co_id);
            $course_name = course_name($co_id);

            $courseCriteriaDetails = new CourseCriteriaDetails();
            $get_criteriadetails = $courseCriteriaDetails->fetchCriteriaDetails($co_id);

            // print_r($get_criteriadetails);
            //exit();

            $approval_status = "select  get_final_approval_status($co_id) as status from dual";
            $res_approval_status = DB::select($approval_status);
            // echo $co_id;exit;
            // print_r($res_approval_status);exit;
            $checkCountFiles = DB::table('course_criteria_details')
                ->select(DB::raw('CASE WHEN COUNT(*) = COUNT(CASE WHEN google_drive_url IS NOT NULL AND google_drive_file_name IS NOT NULL THEN 1 END) THEN 1 ELSE 0 END AS result'))
                ->where('co_id', $co_id)
                ->whereNotIn('assessment_id', [15])
                ->value('result');
            $checkNullFiles = DB::table('course_criteria_details')
                ->select('course_criteria_details.assessment_id as assessment_id', 'course_criteria_details.ccd_details as ccd_details', 'assessments.assessment_name as assessment_name')
                ->leftJoin('assessments', 'course_criteria_details.assessment_id', '=', 'assessments.id')
                ->where('co_id', $co_id)
                ->whereNull('google_drive_url')
                ->whereNull('google_drive_file_name')
                ->whereNotIn('assessment_id', [15])
                ->get();

            $getCourseId = "select co_course_id,get_course_grading_type(co_id) as grading_type from courses_offered where co_id = $co_id";
            // echo $getCourseId;exit;
            $resgetCourseId = DB::select($getCourseId);

            $chkParentDone = "select * from courses_offered where co_id  = $co_id and combine_course is not null";
            // echo $chkParentDone;exit;
            $reschkParentDone = DB::select($chkParentDone);


            $getParentCourse = "select * from courses_offered where parent_co_id  = $co_id and combine_course is not null";
            $resgetParentCourse = DB::select($getParentCourse);


            // dd(auth()->user()->employee_id);
            $checkCombineCourse = "select count(*) as count,fc_employee_id,fc_co_course_id from faculty_courses
            where fc_employee_id = '" . auth()->user()->employee_id . "' and fc_co_sess_id = 166 and fc_co_course_id = '" . $resgetCourseId[0]->co_course_id . "'
            group by fc_employee_id,fc_co_course_id having count(*)>1";

            $rescheckCombineCourse = DB::select($checkCombineCourse);

             // echo $rescheckCombineCourse[0]->fc_co_course_id;exit;
             if(count($rescheckCombineCourse) > 0){
                $chkFacultyCombineCourse = "select count(*) as count from courses_offered 
                                            left join faculty_courses on fc_co_id = co_id
                                            where co_course_id = '" . $rescheckCombineCourse[0]->fc_co_course_id . "'
                                            and co_sess_id = 166
                                            and fc_employee_id = '" . $rescheckCombineCourse[0]->fc_employee_id . "'
                                            and combine_course is not null";
                                            // echo $chkFacultyCombineCourse;exit;
                $reschkFacultyCombineCourse = DB::select($chkFacultyCombineCourse);
    
                $CombineButtonAppears =  $reschkFacultyCombineCourse[0]->count;
            }
            else{
                $CombineButtonAppears = 0;
            }

            DB::statement("
                BEGIN
                    UPDATE_CHILD_ASSESSMENTS_AUTO(?);
                END;
            ", [$co_id]);




            return view('grader/LectureScheduling.course_evaluation', [
                'criteria' => $get_criteria,
                'get_criteria_details' => $get_criteriadetails,
                'get_criteria' => $get_criteria,
                'course_criteria' => $get_criteria,
                'criteria_details' => $get_criteriadetails,
                'course_name' => $course_name,
                'get_co_id' => $get_co_id,
                'CombineButtonAppears' => $CombineButtonAppears,
                'co_id' => $co_id,
                'result' => $checkCountFiles,
                'chkParentDone' => count($reschkParentDone),
                'checkCount' => count($rescheckCombineCourse),
                'getParentCourse' => count($resgetParentCourse),
                'checkNullFiles' => $checkNullFiles,
                'courseId' => $resgetCourseId,
                'status' => $res_approval_status[0]->status
            ]);
        } else {
            return redirect('/home');
        }
    }

    function checkCombineFaculty($course_id)
    {
        $employee_id = auth()->user()->employee_id;

        $sqlchkCriteria = "select * from courses_offered where co_course_id = $course_id and co_sess_id = 166  and co_status = 'E'
                        and co_id in (select fc_co_id from faculty_courses where fc_employee_id = $employee_id       and fc_co_sess_id = 166
            and fc_status = 'E')";
        $ressqlchkCriteria = DB::select($sqlchkCriteria);
        // echo $sqlchkCriteria;exit;
        $assessmentData = [];
        $firstSet = null;
        $differenceFound = false;
        // dd($ressqlchkCriteria);
        foreach ($ressqlchkCriteria as $row) {
            $co_id = $row->co_id;

            // Get assessment_ids for current co_id
            $courseCriteriaDetails = "SELECT assessment_id FROM course_criteria_details WHERE co_id = $co_id";
            // echo $courseCriteriaDetails;exit;
            $results = DB::select($courseCriteriaDetails);

            // Extract and sort the assessment_ids
            $assessmentIds = array_map(function ($item) {
                return $item->assessment_id;
            }, $results);

            sort($assessmentIds); // sort for reliable comparison
            $assessmentStr = implode(',', $assessmentIds); // convert to string for comparison
            //   dd($assessmentStr);
            if ($firstSet === null) {
                // Save the first set of assessment_ids as reference
                $firstSet = $assessmentStr;
                $firstCoId = $co_id;
            } elseif ($firstSet !== $assessmentStr) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Respected faculty, please ensure that the number of assessments are equal for the same course across different sections before proceeding with the combined grading process',
                    'details' => [
                        'co_id_1' => [
                            'co_id' => $firstCoId,
                            'assessments' => explode(',', $firstSet),
                        ],
                        'co_id_2' => [
                            'co_id' => $co_id,
                            'assessments' => explode(',', $assessmentStr),
                        ]
                    ]
                ]);
            }
        }

        $firstTotal = null;
        $firstCoId = null;

        foreach ($ressqlchkCriteria as $row) {
            $co_id = $row->co_id;

            // Get SUM of total_marks for this co_id
            $query = "
                SELECT SUM(DISTINCT cam.total_marks) AS total 
                FROM course_assessment_marks cam
                JOIN course_criteria_details ccd ON cam.course_criteria_detail_id = ccd.id
                WHERE ccd.co_id = $co_id
            ";

            $result = DB::selectOne($query);
            $totalMarks = floatval($result->total ?? 0);

            if ($firstTotal === null) {
                $firstTotal = $totalMarks;
                $firstCoId = $co_id;
            } else {
                if ($firstTotal !== $totalMarks) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Respected faculty, please ensure that the total marks of each assessment is equal across all sections of the same course.',
                        'details' => [
                            'co_id_1' => [
                                'co_id' => $firstCoId,
                                'total_marks' => $firstTotal
                            ],
                            'co_id_2' => [
                                'co_id' => $co_id,
                                'total_marks' => $totalMarks
                            ]
                        ]
                    ]);
                }
            }
        }
        // dd($employee_id,$course_id);
        $sql = "SELECT
                COUNT(*) as count,
                fc_employee_id,
                fc_co_course_id
            FROM
                faculty_courses
            WHERE
                fc_co_sess_id = 166
                and fc_co_course_id = $course_id
                
            group BY
                fc_employee_id,
                fc_co_course_id
                having count(*)>1";
        $res = DB::select($sql);
        //echo $res[0]->count;exit;
        if ($res[0]->count > 1) {
            // $chkcourses = "select get_course_name(co_id),co_id,parent_co_id from courses_offered where co_course_id = $course_id and co_status = 'E' and co_sess_id =166 and co_id in (
            //                 select fc_co_id from faculty_courses where fc_employee_id = $employee_id and fc_status = 'E'
            //                 )
            //                 AND co_id > (
            //                     SELECT
            //                         MIN(co_id)
            //                     FROM
            //                         courses_offered
            //                     WHERE
            //                         co_course_id = $course_id
            //                         AND co_sess_id = 166
            //                         AND co_id IN (
            //                             SELECT
            //                                 fc_co_id
            //                             FROM
            //                                 faculty_courses
            //                             WHERE
            //                                 fc_employee_id = $employee_id
            //                                 AND fc_status = 'E'
            //                     )
            //                 )";
            // return $resChkCourses = DB::select($chkcourses);
            $chkcourses = "select get_course_name(co_id),co_id,parent_co_id from courses_offered where co_course_id = $course_id and co_status = 'E' and co_sess_id =166 and co_id in (
                select fc_co_id from faculty_courses where fc_employee_id = $employee_id and fc_status = 'E'
                )
                AND (
                co_id IN (45824, 45825)
                OR co_id > (
                    SELECT
                        MIN(co_id)
                    FROM
                        courses_offered
                    WHERE
                        co_course_id = $course_id
                        AND co_sess_id = 166
                        AND co_id IN (
                            SELECT
                                fc_co_id
                            FROM
                                faculty_courses
                            WHERE
                                fc_employee_id = $employee_id
                                AND fc_status = 'E'
                        )
                )
            )";
                // echo $chkcourses;exit;
            return $resChkCourses = DB::select($chkcourses);
        }

    }

    // function combineCourse($co_id,$current_co_id){
    //    // echo $co_id.'---'.$current_co_id;exit;
    //     $resUpdate = "update courses_offered set parent_co_id = $current_co_id where co_id = $co_id";
    //     $resResUpdate = DB::insert($resUpdate);

    //      //return $resupdateMarks;


    // }

    // public function combineCourse(Request $request) {
    //     // dd('123');
    //      $co_ids = $request->input('co_ids'); // Array of co_ids
    //      $current_co_id = $request->input('current_co_id'); // Current course ID
    //      //dd($co_ids,$current_co_id );
    //      if (is_array($co_ids) && !empty($co_ids)) {
    //          foreach ($co_ids as $co_id) {
    //              $resUpdate = "UPDATE courses_offered SET parent_co_id = $current_co_id,combine_course='by_faculty' WHERE co_id = $co_id";
    //              DB::insert($resUpdate); // Execute the query
    //          }
    //          return response()->json(['success' => true, 'message' => 'Courses updated successfully.']);
    //      }

    //      return response()->json(['success' => false, 'message' => 'Invalid input.']);
    //  }
    public function combineCourse(Request $request)
    {
        // dd(auth()->user()->employee_id);
        $co_ids = $request->input('co_ids'); // Array of co_ids
        $current_co_id = $request->input('current_co_id'); // Current course ID
        $course_id = $request->input('course_id');
        $currentLoginEmpId = auth()->user()->employee_id;

        $facultyCourseIdsSql = "select fc_co_id from faculty_courses where fc_employee_id = $currentLoginEmpId and fc_status = 'E' and fc_co_course_id = 
        $course_id and fc_co_sess_id = 166";

        // if (is_array($co_ids) && !empty($co_ids)) {
        //     foreach ($co_ids as $co_id) {
        //         // dd($co_ids );
        //         $resUpdate = "UPDATE courses_offered SET parent_co_id = $current_co_id,combine_course='by_faculty' 
        //         WHERE co_id = $co_id or parent_co_id = $co_id";
        //         echo $resUpdate;exit;


        //         DB::insert($resUpdate); // Execute the query
        //     }
        //    //return response()->json(['success' => true, 'message' => 'Courses updated successfully.']);
        // }
        // foreach ($resparent_co_id as $row) {

        //     $sqlParentCoId = "SELECT parent_co_id FROM courses_offered WHERE co_id = $row->fc_co_id";
        //     $resParentCoId = DB::select($sqlParentCoId);
        //     if($resParentCoId[0]->parent_co_id != null){
        //         $oldParentCoId = $resParentCoId[0]->parent_co_id;
        //     }else{
        //         $oldParentCoId = 'null';
        //     }
        //     $resUpdate = "UPDATE courses_offered SET old_parent_co_id = $oldParentCoId,parent_co_id = $current_co_id,combine_course='by_faculty'
        //                   WHERE (co_id = $row->fc_co_id or parent_co_id = $row->fc_co_id) and co_id not in $current_co_id";
        //     //  echo $resUpdate;


        //     DB::insert($resUpdate); // Execute the query

        // }
        DB::update("
            UPDATE courses_offered
            SET old_parent_co_id = parent_co_id
            WHERE old_parent_co_id IS NULL
              AND parent_co_id IS NOT NULL
              AND co_id != $current_co_id
              AND (
                  co_id IN ($facultyCourseIdsSql)
                  OR parent_co_id IN ($facultyCourseIdsSql)
              )
        ");

        DB::update("
            UPDATE courses_offered
            SET parent_co_id = $current_co_id,
                combine_course = 'by_faculty'
            WHERE co_id != $current_co_id
              AND (
                  co_id IN ($facultyCourseIdsSql)
                  OR parent_co_id IN ($facultyCourseIdsSql)
              )
        ");
        //exit;
        return response()->json(['success' => false, 'message' => 'Invalid input.']);
    }
    function transferMarks2($co_id)
    {
        // dd($co_id);
        // $co_id = $request->get('selectedCoId');
        // $current_co_id = $request->get('currentCoId');
        $employee_id = auth()->user()->employee_id;
        $co_course_idSql = "select * from courses_offered where co_id = $co_id";
        $resco_course_idSql = DB::select($co_course_idSql);


        $chkcourses = "select get_course_name(co_id),co_id,parent_co_id from courses_offered where co_course_id = '" . $resco_course_idSql[0]->co_course_id . "' and co_sess_id =166 and co_id in (
            select fc_co_id from faculty_courses where fc_employee_id = $employee_id and fc_status = 'E'
            )
            AND co_id > (
                SELECT
                    MIN(co_id)
                FROM
                    courses_offered
                WHERE
                    co_course_id = '" . $resco_course_idSql[0]->co_course_id . "'
                    AND co_sess_id = 166
                    AND co_id IN (
                        SELECT
                            fc_co_id
                        FROM
                            faculty_courses
                        WHERE
                            fc_employee_id = $employee_id
                            AND fc_status = 'E'
                )
            )";
        //echo $chkcourses;exit;
        $resChkCourses = DB::select($chkcourses);

        foreach ($resChkCourses as $res) {


            $sql = "select assessment_id,ccd_number from course_criteria_details where co_id = $res->co_id";
            $resSql = DB::select($sql);
            // dd($resSql);
            foreach ($resSql as $row) {

                $updateMarks = "UPDATE course_assessment_marks cam1
                    SET cam1.obtained_marks = (
                        SELECT cam2.obtained_marks 
                        FROM course_assessment_marks cam2   
                        WHERE cam2.co_id =  $res->co_id
                        AND cam2.assessment_id = $row->assessment_id
                        AND cam2.cam_number = $row->ccd_number
                        and  cam1.sai_enrollment_no=cam2.sai_enrollment_no
                    )
                    WHERE cam1.sc_co_id = $res->co_id
                    and cam1.co_id = $co_id
                    AND cam1.assessment_id = $row->assessment_id
                    AND cam1.cam_number = $row->ccd_number ";
                //echo $updateMarks.'<br>';
                $resupdateMarks = DB::insert($updateMarks);
            }
        }
        echo "1";
    }

    function transferMarks($co_id, $assess, $cnum)
    {
        // dd($co_id);
        // $co_id = $request->get('selectedCoId');
        // $current_co_id = $request->get('currentCoId');
        $employee_id = auth()->user()->employee_id;
        $co_course_idSql = "select * from courses_offered where co_id = $co_id";
        $resco_course_idSql = DB::select($co_course_idSql);
        $courseId = $resco_course_idSql[0]->co_course_id;

        $chkcourses = "select sai_enrollment_no from course_assessment_marks where assessment_id = $assess and cam_number = $cnum 
                    and co_id in (select co_id from courses_offered where co_id =$co_id or parent_co_id = $co_id)
                    AND assessment_id = $assess
                    AND cam_number = $cnum
                    and obtained_marks is null";
        //echo $chkcourses;exit;
        $resChkCourses = DB::select($chkcourses);


        foreach ($resChkCourses as $row) {
            // dd($res->co_id, $co_id);
            $rollNO = $row->sai_enrollment_no;
            $updateMarks = "UPDATE course_assessment_marks cam_null
                            SET obtained_marks = (
                                SELECT cam_notnull.obtained_marks
                                FROM course_assessment_marks cam_notnull
                                WHERE cam_notnull.sai_enrollment_no = cam_null.sai_enrollment_no
                                AND cam_notnull.assessment_id = cam_null.assessment_id
                                AND cam_notnull.cam_number = cam_null.cam_number
                                AND cam_notnull.sc_co_id IN (
                                        SELECT co_id FROM courses_offered WHERE co_course_id = $courseId and co_sess_id = 166
                                    )
                                AND cam_notnull.obtained_marks IS NOT NULL
                                FETCH FIRST 1 ROWS ONLY
                            )
                            WHERE cam_null.obtained_marks IS NULL
                            AND cam_null.assessment_id = $assess
                            AND cam_null.cam_number = $cnum
                            AND cam_null.co_id IN (
                                    SELECT co_id FROM courses_offered WHERE co_id = $co_id OR parent_co_id = $co_id
                                )
                            AND cam_null.sai_enrollment_no = '$rollNO'";
            // echo $updateMarks.'<br>';
            $resupdateMarks = DB::insert($updateMarks);
        }


        echo "1";
    }

    function getassessmentcount($decrypt_co_id, $assess_id)
    {

        $sql = "select count(*)+1 as count from COURSE_CRITERIA_DETAILS where co_id = $decrypt_co_id and assessment_id = $assess_id ";
        //echo $sql;exit;
        return $resultsql = DB::select($sql);
    }
    function sendNoti(Request $request, $coID, $cID)
    {
        $sms = new SendSms();

        // courses are offered both school 12 and 21
        $sqlchk = "SELECT co_school_id, co_dept_id FROM courses_offered WHERE co_id = $coID";
        $reschk = DB::select($sqlchk);
        if ($reschk[0]->co_school_id == 12 || $reschk[0]->co_school_id == 21) {
            $deptIdJoin = 81;
        } elseif ($reschk[0]->co_school_id == 4) {
            $deptIdJoin = 11;
        } else {
            $deptIdJoin = 'courses_offered.co_dept_id';
        }
        $sel = "select 
                    emp_contact_no,
                    emp_email
                from courses_offered
                join EMP_EMPLOYEES on $deptIdJoin = EMP_EMPLOYEES.EMP_DEPT_ID
                join sisusers on emp_email = email
                where co_id = $coID  
                and emp_status = 'E' 
                and group_id = 223
                and emp_email not in ('software@bnu.edu.pk')";
        $sql = DB::select($sel);

        $assID = ($request->get('assID') == 3 ? 'Mid Term' : 'Final Term');

        $get_course = $sms->get_course_teacher($coID, auth()->user()->employee_id);
        $subject = 'Student Marks Approval Alert ! in ' . $assID . ' ' . $get_course;
        $res = $sms->send_sms($subject, $sql[0]->emp_contact_no);
        $result = $sms->teacher_marks_change_sendmail($sql[0]->emp_email, 'Request Student Marks Approval', $subject);


        if ($res) {
            $res = array('status' => true, 'message' => 'Message Send to HOD');
            return response()->json($res);

        }
    }



    function uploadAssignemntFile(Request $request)
    {
        // dd($request->all());
        try {
            $validatedData = $request->validate([
                'description' => 'required',
                'file' => 'required|file|mimes:pdf,xlsx,pptx,docx,zip,rar',
            ]);
            // if file exist delelete it first then move next

            // $obj = DB::table('course_criteria_details')->where('id', '=', $request->input('course_criteria_detail_id'))->first();
            $obj = DB::table('course_criteria_details')
                ->select(
                    'course_criteria_details.id',
                    'course_criteria_details.co_id',
                    'course_criteria_details.assessment_id',
                    'course_criteria_details.ccd_details',
                    'course_criteria_details.file_name',
                    'course_criteria_details.google_drive_url',
                    'course_criteria_details.google_drive_file_name',
                    DB::raw('assessments.assessment_name || \' - \' || course_criteria_details.ccd_number AS assessment_name')
                )
                ->join('assessments', 'course_criteria_details.assessment_id', '=', 'assessments.id')
                ->where('course_criteria_details.id', '=', $request->input('course_criteria_detail_id'))
                ->first();


            //return $obj->assessment_name." : ".$request->file('file')->getClientOriginalName();

            $file = Storage::disk('google')->exists($obj->google_drive_file_name);
            if ($file) {
                Storage::disk('google')->delete($obj->google_drive_file_name);
            }

            $description = $validatedData['description'];
            $file = $validatedData['file'];
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $fileName = $obj->assessment_name . " : " . $file->getClientOriginalName();
                $fileExtension = $file->getClientOriginalExtension();
                $newFileName = time() . '_' . $fileName;
                $path = $file->storeAs('models', $newFileName);
            }
            // upload file process
            $upload = Storage::disk('google')->putFileAs("", $request->file("file"), $fileName);
            $url = Storage::disk('google')->url($upload);
            $details = Storage::disk("google")->getMetadata($upload);
            $file_name = $details['path'];
            // dd($details,$url);
            DB::table('course_criteria_details')
                ->where('id', $request->input('course_criteria_detail_id'))
                ->where('co_id', $request->input('cr_co_id'))
                ->update([
                    'google_drive_file_name' => $file_name,
                    'google_drive_url' => $url,
                    'file_name' => $obj->assessment_name . " : " . $file->getClientOriginalName(),
                    'description' => $request->input('description'),
                    'ccd_entry_user' => auth()->user()->id
                ]);
            return response()->json(['message' => 'File uploaded successfully', 'status' => true]);
        } catch (ValidationException $e) {
            $res = array('status' => false, 'message' => 'An error occurred: ' . $e->getMessage());
            return response()->json($res);
        } catch (Exception $e) {
            $res = array('status' => false, 'message' => 'An error occurred: ' . $e->getMessage());
            return response()->json($res);
        }
    }

    public function deleteAssignemntFile(Request $request)
    {
        try {
            $obj = DB::table('course_criteria_details')->where('id', '=', $request->input('course_criteria_detail_id'))->first();

            $file = Storage::disk('google')->exists($obj->google_drive_file_name);

            if ($file) {
                Storage::disk('google')->delete($obj->google_drive_file_name);
            }
            DB::table('course_criteria_details')
                ->where('id', $request->input('course_criteria_detail_id'))
                ->where('co_id', $request->input('cr_co_id'))
                ->update([
                    'google_drive_file_name' => '',
                    'google_drive_url' => '',
                    'description' => '',
                    'file_name' => '',
                    'ccd_entry_user' => auth()->user()->id
                ]);
            return response()->json(['message' => 'File deleted successfully', 'status' => true]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred: ' . $e->getMessage(), 'status' => false]);
        }
    }
    public function downloadZipFile(Request $request)
    {
        $coId = $request->input('co_id');
        $coName = str_replace('-', '_', $request->input('co_name'));
        $files = DB::table('course_criteria_details')->where('co_id', $coId)->get();
        $userId = auth()->user()->id;

        $userFolderPath = public_path("downloadBundle/{$userId}");
        $courseFolderPath = "{$userFolderPath}/{$coName}";

        // Create the folders
        if (!File::exists($userFolderPath)) {
            File::makeDirectory($userFolderPath, 0777, true);
        }
        if (!File::exists($courseFolderPath)) {
            File::makeDirectory($courseFolderPath, 0777, true);
        }

        // Download files from Google Drive
        foreach ($files as $file) {
            $fileUrl = $file->google_drive_url;
            $fileName = $file->file_name;

            if (empty($fileUrl)) {
                continue; // Skip empty URLs
            }

            $extension = pathinfo($fileName, PATHINFO_EXTENSION);
            $fileName = preg_replace('/\.(?!' . $extension . ')/', '', $fileName);

            $contents = file_get_contents($fileUrl);
            $fileDest = "{$courseFolderPath}/{$fileName}";
            File::put($fileDest, $contents);
        }

        // Additional reports
        $protocol = $request->isSecure() ? 'https' : 'http';
        $baseUrl = $request->root();

        $fileUrls = [
            "{$baseUrl}/reports/assessment_marks_report/{$coId}",
            "{$baseUrl}/assets/cms/reports/course_wise_attendance_report.php?action=grader&ca_course_id_name={$coId}&cwa_report_type=1",
            "{$baseUrl}/faculty_course_detail_report/{$coId}"
        ];

        $reportNames = [
            'Assessment Marks Report.pdf',
            'Course Wise Attendance Report.pdf',
            'Faculty Course Detail Report.pdf'
        ];

        // Get current request's User-Agent (browser ka UA)
        $currentUserAgent = $request->header('User-Agent');
        // Agar for some reason empty ho, to fallback Chrome ka UA
        if (empty($currentUserAgent)) {
            $currentUserAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36';
        }
        // Download reports with cookies + headers
        foreach ($fileUrls as $index => $fileUrl) {
            $fileName = $reportNames[$index];

            $response = Http::withOptions([
                'verify' => false // ignore SSL issues
            ])->withCookies($_COOKIE, parse_url($baseUrl, PHP_URL_HOST))
                ->withHeaders([
                    'User-Agent' => $currentUserAgent,
                    'Accept' => 'application/pdf,application/octet-stream;q=0.9,*/*;q=0.8',
                ])
                ->get($fileUrl);

            if ($response->successful() && strpos($response->body(), '%PDF') === 0) {
                file_put_contents("{$courseFolderPath}/{$fileName}", $response->body());
            } else {
                // Save as debug if not PDF
                $cleanFileName = preg_replace('/[^A-Za-z0-9._-]/', '_', $fileName);
                file_put_contents("{$courseFolderPath}/404__{$cleanFileName}.html", $response->body());
            }
        }

        // Create the zip file
        $zipName = "{$coName}.zip";
        $zipPath = "{$userFolderPath}/{$zipName}";
        $command = "cd {$userFolderPath} && zip -r {$zipName} {$coName}";
        exec($command);

        $zipUrl = url("downloadBundle/{$userId}/{$zipName}");

        return response()->json([
            'message' => 'Download URL generated successfully',
            'status' => true,
            'url' => $zipUrl
        ]);
    }

    public function removeDirectry(Request $request)
    {
        $userFolderPath = public_path('downloadBundle/' . auth()->user()->id);
        if (File::exists($userFolderPath)) {
            File::deleteDirectory($userFolderPath);
        }
        return response()->json([
            'message' => 'File Directory Updated',
            'status' => true,
        ]);
    }
}
