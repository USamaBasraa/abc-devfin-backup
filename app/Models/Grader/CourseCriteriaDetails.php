<?php
/**
 * Created by PhpStorm.
 * User: Awais Khan
 * Date: 13-Aug-18
 * Time: 4:48 PM
 */

namespace App\Models\Grader;


use Illuminate\Database\Eloquent\Model;
use App\sessions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CourseCriteriaDetails extends Model
{

    public function fetchAllCriteriaDetails()
    {
        return self::select('ccd.co_id',
            'ccd.assessment_id',
            'ccd.course_criteria_id',
            'ccd.ccd_number',
            'ccd.ccd_weightage',
            'assessments.assessment_name')
            ->join('course_criteria_details', 'course_criteria_details.assessment_id', '=', 'assessments.id')
            ->where([
                ['ccd.ccd_status', 'E'],
            ])
            ->orderby('ccd.assessment_id', 'asc')
            ->orderby('ccd.ccd_number', 'asc')
            ->get();
    }

    public function fetchCriteriaDetails($co_id)
    {

        $loginUser = Auth::user();



        $emp_user = $loginUser::find($loginUser->id)->employees;

       // echo $loginUser->id;exit();

       //  print_r($emp_user);exit();

        if(@$emp_user->emp_school_id==1 and 1==2)
        {

            echo 'yes';
           $query = "SELECT DISTINCT
                            ccd.id,
                            cam_number,
                            total_marks,
                            ccd_details,
                            get_assessment_missing_marks(
                                ccd.co_id,
                                ccd.assessment_id
                            ) AS missing_marks_status
                        FROM
                            course_criteria_details ccd
                            LEFT JOIN course_assessment_marks cam ON
                                ccd.co_id = cam.co_id
                            AND
                                ccd.assessment_id = cam.assessment_id
                            AND
                                ccd.ccd_number = cam.cam_number
                            LEFT JOIN assessments ON cam.assessment_id = assessments.id
                            LEFT JOIN student_courses ON
                                cam.sai_id = student_courses.sc_sai_id
                            AND
                                student_courses.sc_co_id = cam.sc_co_id
                        WHERE
                                student_courses.sc_status = 'A'
                            AND
                                cam.co_id =".$co_id;
// echo $query;exit;
            $result =DB::select($query);
            return json_decode(json_encode($result), true);

        }else{

            if($co_id){

                return  self::select('course_criteria_details.co_id',
                                    'course_criteria_details.id',
                                    'course_criteria_details.ccd_details',
                                    'course_criteria_details.assessment_id',
                                    'course_criteria_details.course_criteria_id',
                                    'course_criteria_details.ccd_number',
                                    'course_criteria_details.ccd_weightage',
                                    'course_criteria_details.google_drive_file_name',
                                    'course_criteria_details.google_drive_url',
                                    'course_criteria_details.file_name',
                                    'course_criteria_details.description as description',
                                    'assessments.assessment_name',
                                    DB::raw('GET_ASSESSMENT_MISSING_MARKS(course_criteria_details.co_id,course_criteria_details.assessment_id) as missing_marks_status'),
                                    DB::raw('(SELECT SUM(obtained_marks)
                                    FROM course_assessment_marks
                                    WHERE co_id                   = COURSE_CRITERIA_DETAILS . CO_ID
                                    AND COURSE_CRITERIA_DETAIL_ID = COURSE_CRITERIA_DETAILS . ID)  as obtained'),
                                     DB::raw('(SELECT SUM(total_marks)
                                     FROM course_assessment_marks
                                     WHERE co_id                   = COURSE_CRITERIA_DETAILS . CO_ID
                                     AND COURSE_CRITERIA_DETAIL_ID = COURSE_CRITERIA_DETAILS . ID)  as total'),
                                     DB::raw("(SELECT count(*)
                                     FROM course_assessment_marks
                                     left join student_courses on student_courses.sc_co_id = course_assessment_marks.sc_co_id 
                                     and student_courses.sc_sai_enrollment_no  = course_assessment_marks.sai_enrollment_no
                                     left join students_apply_info on students_apply_info.sai_enrollment_no = sc_sai_enrollment_no
                                     WHERE 
                                     sc_status in ('A','Block')
                                     AND  students_apply_info.sai_student_status IN ( 'Active', 'Expected-Graduate', 'Left', 'Struck off', 'Stuck-Off')
                                     and sc_enrollment_status = 1
                                     and co_id                   = COURSE_CRITERIA_DETAILS . CO_ID
                                     AND COURSE_CRITERIA_DETAIL_ID = COURSE_CRITERIA_DETAILS . ID
                                     and obtained_marks is null)  as missing_students"),
                                     DB::raw("(SELECT count(*)
                                     FROM course_assessment_marks
                                     left join student_courses on student_courses.sc_co_id = course_assessment_marks.sc_co_id 
                                     and student_courses.sc_sai_enrollment_no  = course_assessment_marks.sai_enrollment_no
                                     WHERE co_id                   = COURSE_CRITERIA_DETAILS . CO_ID
                                     AND COURSE_CRITERIA_DETAIL_ID = COURSE_CRITERIA_DETAILS . ID
                                     and sc_status in ('A','Block')
                                     and sc_enrollment_status = 1
                                     and approval_status = 0)  as missing_approval_students")
                                    )
                            ->join('assessments', 'course_criteria_details.assessment_id', '=', 'assessments.id')
                            ->where([
                                ['course_criteria_details.co_id', $co_id],
                                ['course_criteria_details.ccd_status', 'E'],
                            ])
                            ->orderby('course_criteria_details.assessment_id', 'asc')
                            ->orderby('course_criteria_details.ccd_number', 'asc')
                           ->get();
                         // ->toSql(); exit();
            }
        }
    }

    public function checkmydate($date) {
        if(strpos($date,"-")>0)
        {
            return 1;
        }else{
            return 0;
        }
    }
    function get_assessment_criteria_details($co_id,$assessmentID){
        return self::
        join('assessments', 'course_criteria_details.assessment_id', '=', 'assessments.id')
            ->where([
                ['course_criteria_details.co_id', $co_id],
                ['course_criteria_details.assessment_id', $assessmentID]
            ])
            ->orderby('course_criteria_details.assessment_id', 'asc')
            ->orderby('course_criteria_details.ccd_number', 'asc')
            ->select('COURSE_CRITERIA_DETAILS.id as ccd_id','COURSE_CRITERIA_DETAILS.*','assessments.assessment_name',
                'course_criteria_details.ccd_number','course_criteria_details.course_criteria_id',
                DB::raw('GET_ASSESSMENT_MISSING_MARKS(course_criteria_details.co_id,course_criteria_details.assessment_id,course_criteria_details.ccd_number) as missing_marks_status')
            )
            ->get();
    }
    public function saveCriteriaDetails($ccd_date,$ccd_details,$co_id,$assess_id,$cc_id,$cam_number,$cc_weightage,$action_type,$ccd_id)
    {
        $sess = new sessions();
        $sess->startSession();

        if($this->checkmydate($ccd_date))
        {
            $dateFormat = "TO_DATE('".$ccd_date."','yyyy-mm-dd')";
        }else{
            $dateFormat =  "TO_DATE('".$ccd_date."','mm/dd/yyyy')";
        }

        $final_weightage = $cc_weightage/$cam_number;

        $ccd_details = str_replace( "'", " ", $ccd_details );

        if($action_type=='insert')
        {
            $queryInsert = "INSERT INTO course_criteria_details (
                                                            co_id,
                                                            assessment_id,
                                                            course_criteria_id,
                                                            ccd_number,
                                                            ccd_weightage,
                                                            ccd_details,
                                                            ccd_date,
                                                            ccd_status,
                                                            ccd_entry_user,
                                                            created_at
                                                        ) VALUES (
                                                            ".$co_id.",
                                                            ".$assess_id.",
                                                            ".$cc_id.",
                                                            ".$cam_number.",
                                                            0,
                                                            '".$ccd_details."',
                                                            ".$dateFormat.",
                                                            'E',
                                                            ".$sess->sisuser_id.",
                                                            CURRENT_TIMESTAMP
                                                        )";
            $resultInsert =DB::insert($queryInsert);


        //     $insertArr['co_id'] = $co_id;
        //     $insertArr['assessment_id'] = $assess_id;
        //     $insertArr['course_criteria_id'] = $cc_id;
        //     $insertArr['ccd_number'] = $cam_number;
        //     $insertArr['ccd_weightage'] = 0;
        //     $insertArr['ccd_details'] =            // str_replace( "'", " ", $ccd_details );
        //     $insertArr['ccd_date'] = $dateFormat;
        //     $insertArr['ccd_status'] = 'E';
        //     $insertArr['ccd_entry_user'] = $sess->sisuser_id;
        //   //  $insertArr['created_at'] = 'CURRENT_TIMESTAMP';





        //     $resultInsert = DB::table('course_criteria_details')->insertGetId($insertArr);

            if($resultInsert)
            {
                return 1;
            }else{
                return 0;
            }

        }else if($action_type=='update'){

            $queryUpdate = "UPDATE course_criteria_details
                                                            SET
                                                                ccd_details     = '".$ccd_details."',
                                                                ccd_date        = ".$dateFormat.",
                                                                ccd_entry_user  = ".$sess->sisuser_id.",
                                                                updated_at      = CURRENT_TIMESTAMP
                                                            WHERE
                                                                co_id           = ".$co_id."
                                                            AND
                                                                assessment_id   = ".$assess_id."
                                                            AND
                                                                id = ".$ccd_id;
            $resultUpdate =DB::insert($queryUpdate);
            if($resultUpdate)
            {
                return 1;
            }else{
                return 0;
            }

        }else{
            return 0;
        }
    }
}
