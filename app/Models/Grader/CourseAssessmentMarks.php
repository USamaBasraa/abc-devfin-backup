<?php

namespace App\Models\Grader;

use Illuminate\Database\Eloquent\Model;
use DB;
use App\sessions;
use App\Models\Grader\SendSms;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Grader\CourseCriteriaDetailController;

class CourseAssessmentMarks extends Model
{
    protected $fillable = [ 'sai_enrollment_no' ];


    public function Courseassessmentdetail($co_id)
    {
        if($co_id) {

            $query = "select * from course_assessment_marks
                                    left join students_apply_info on course_assessment_marks.SAI_ENROLLMENT_NO=students_apply_info.SAI_ENROLLMENT_NO
                                    left join STUDENTS_PERSONAL_INFO on sai_spi_id= spi_id
                                    where   co_id = $co_id";

            $result =DB::select($query);
            return json_decode(json_encode($result), true);

        }
    }

	public function AssessmentChecking($course_criteria_details,$co_id)
    {
            $query = "SELECT
                        course_assessment_marks.id,
                        course_assessment_marks.co_id,
                        course_assessment_marks.assessment_id,
                        course_assessment_marks.course_criteria_id,
                        course_assessment_marks.cam_number,
                        students_apply_info.sai_enrollment_no,
                        course_assessment_marks.submission_file_name,
                        spi_name,
                        --course_assessment_marks.google_drive,
                        teacher_comments,
                        total_marks,
                        obtained_marks,
                        submission_status,
                        course_criteria_details.id AS course_criteria_details_id,
                        course_criteria_details.file_name,
                        course_criteria_details.file_original_name
                    FROM
                        student_courses
                        LEFT JOIN students_apply_info ON student_courses.sc_sai_enrollment_no = students_apply_info.sai_enrollment_no
                        LEFT JOIN students_personal_info ON sai_spi_id = spi_id
                        LEFT JOIN course_criteria_details ON $course_criteria_details = course_criteria_details.id
                        
                        LEFT JOIN course_assessment_marks ON
                            course_assessment_marks.sai_enrollment_no = student_courses.sc_sai_enrollment_no
                        AND
                            course_assessment_marks.course_criteria_detail_id = course_criteria_details.id
                    WHERE
                            student_courses.sc_co_id IN (
                                SELECT
                                    courses_offered.co_id
                                FROM
                                    courses_offered
                                WHERE
                                        courses_offered.co_id = $co_id
                                    OR
                                        courses_offered.parent_co_id = $co_id
                            )
                        AND sc_status in ('A','Block')
                    AND student_courses.SC_ENROLLMENT_STATUS = 1 ";

        $result =DB::select($query);

        $sql = "select * from FACULTY_COURSE_UPLOADS
                    where FACULTY_COURSE_UPLOADS.co_id in (select co_id from courses_offered where co_id=$co_id or parent_co_id=$co_id) ";
        $courseofferingcontent    = DB::select($sql);

        array_walk($result, function ($row, $index) use ($courseofferingcontent) {
        $content_keys = array_keys(array_column($courseofferingcontent, 'cam_id'), $row->id);
        $row->courseofferingcontent  = [];
        foreach ($content_keys as $cc_row) :
            $row->courseofferingcontent[] =  $courseofferingcontent[$cc_row];
        endforeach;
        });

         return json_decode(json_encode($result), true);


    }





//    <!--------------------------------->


    public function fetchCriteriaDetailsSvad($co_id)
    {
        if($co_id){
                return self::select('course_assessment_marks.cam_number as ccd_number',
                                    'assessments.assessment_name',
                                    'course_assessment_marks.assessment_id',
                                    'course_assessment_marks.total_marks as ccd_weightage')
                    ->distinct()
                    ->join('assessments', 'course_assessment_marks.assessment_id', '=', 'assessments.id')
                    ->where([
                        ['course_assessment_marks.co_id', $co_id],
                    ])
                    ->orderby('course_assessment_marks.assessment_id', 'asc')
                    ->orderby('course_assessment_marks.cam_number', 'asc')
                    ->get();
            }
    }

    public function fetchAssessmentList($co_id,$ass_id)
    {

       $query = "SELECT DISTINCT
                        cam.assessment_id,
                        regexp_replace(
                            ass.assessment_name,
                            '( *[[:punct:]])',
                            ''
                        ) assessment_name,
                        cam.cam_number,
                        cc.id course_criteria_id
                    FROM
                        course_assessment_marks cam
                        LEFT JOIN assessments ass ON cam.assessment_id = ass.id
                        LEFT JOIN course_criterias cc ON
                            cam.assessment_id = cc.assessment_id
                        AND
                            cc.co_id = cam.co_id
                    WHERE
                            cam.co_id = ".$co_id."
                        AND
                            cam.assessment_id = ".$ass_id."
                        AND
                        cam.cam_status = 'E'
                    ORDER BY cam_number";
        //return $query;
        $result =DB::select($query);
        return json_decode(json_encode($result), true);
    }
    public function getCourseAssessmentMarks($course_co_id){
        $query="SELECT
                                     sc_id,
                                     students_apply_info.sai_enrollment_no
                                    ,assessments.assessment_short_name
                                    ,course_assessment_marks.cam_number
                                    ,course_assessment_marks.total_marks
                                    ,assessments.assessment_name
                                    ,students_personal_info.spi_name
                                    ,course_assessment_marks.obtained_marks
                                    ,assessments.id as assessment_id
                                    ,course_criteria_details.ccd_status
                                    ,course_assessment_marks.cam_status
                                    ,(SELECT
                                        COUNT(*)
                                    FROM
                                        course_assessment_marks
                                    WHERE
                                            co_id = courses_offered.co_id
                                        AND
                                            sai_enrollment_no = student_courses.sc_sai_enrollment_no
                                        AND
                                            Obtained_Marks is null) missing_flag
                                FROM
                                    student_courses
                                    LEFT JOIN students_apply_info ON student_courses.sc_sai_enrollment_no = students_apply_info.sai_enrollment_no
                                    LEFT JOIN students_personal_info ON sai_spi_id = spi_id
                                    LEFT JOIN courses_offered ON sc_co_id = courses_offered.co_id
                                    LEFT JOIN course_criteria_details ON (
                                        CASE
                                            WHEN parent_co_id IS NULL THEN courses_offered.co_id
                                            ELSE parent_co_id
                                        END
                                    ) = course_criteria_details.co_id
                                    LEFT JOIN course_assessment_marks ON course_criteria_details.id = course_assessment_marks.course_criteria_detail_id
                                                                     AND student_courses.sc_sai_enrollment_no = course_assessment_marks.sai_enrollment_no
                                    LEFT JOIN assessments ON course_criteria_details.assessment_id = assessments.id
                                    LEFT JOIN STUDENTS_SEMESTER_RECORD on STUDENTS_SEMESTER_RECORD.sai_enrollment_no=student_courses.sc_sai_enrollment_no
                                    AND students_semester_record.sess_id=student_courses.sc_co_sess_id
                                WHERE
                                         1 = 1
                                    AND sc_status in ('A','Block')
                                    AND sc_enrollment_status = 1
                                    AND co_status = 'E'
                                    AND ( courses_offered.co_id =  $course_co_id OR courses_offered.parent_co_id =  $course_co_id )
                                    AND ( students_apply_info.sai_student_status IN ('Active','Expected-Graduate','Left','Struck off','Stuck-Off')
                                        OR students_apply_info.sai_semester_status = 'Defer' )
                                    AND ( students_apply_info.sai_semester_status != 'Freeze' OR  students_apply_info.sai_semester_status IS NULL )
									and (select count(*) from GRADE_HISTORY where gh_sai_enrollment_no = sc_sai_enrollment_no
                                            and gh_co_sess_id = courses_offered.co_sess_id
                                            and gh_co_course_id = courses_offered.CO_COURSE_ID
                                     ) = 0
                                    AND ssr_semester_status='Active'
                                ORDER BY student_courses.sc_sai_enrollment_no,assessments.id,course_criteria_details.ccd_number ";

     // echo $query;
     // exit();
        return $student_actual_marks =  DB::select($query);

    }
    public function fetchStudentsListNew($co_id)
    {

           $query = "SELECT
                        co.co_id,
                        co.co_course_id,
                        co.co_section,
                        co.co_course_cr,
                        co.parent_co_id,
                        sc.sc_id,
                        sc.sc_co_id,
                        sai.sai_id,
                        sai.sai_enrollment_no,
                        spi.spi_name,
                        '' cam_id,
                        '' obtained_marks,
                        '' total_marks,
                        '' cam_details,
                        '' cam_marks
                    FROM
                        student_courses sc
                         LEFT JOIN students_semester_record ON
                        sc.sc_sai_enrollment_no = students_semester_record.sai_enrollment_no
                        AND
                        sc.sc_co_sess_id = students_semester_record.sess_id
                        LEFT JOIN courses_offered co ON sc_co_id = co.co_id
                        LEFT JOIN courses_two ON sc_course_id = course_id
                        LEFT JOIN students_apply_info sai ON sc.sc_sai_enrollment_no = sai.sai_enrollment_no
                        LEFT JOIN students_personal_info spi ON sai.sai_spi_id = spi.spi_id
                    WHERE
                            sc_status in ('A','Block')
                             AND students_semester_record.ssr_semester_status = 'Active'
                             AND sc.sc_enrollment_status = 1
                            AND (
                                    sai.sai_student_status IN (
                                        'Active','Expected-Graduate','Left','Struck off','Stuck-Off'
                                    )
                                OR
                                    sai.sai_semester_status = 'Defer'
                            )
                        AND (
                                co.co_id = ".$co_id."
                            OR
                                co.parent_co_id = ".$co_id."
                        )
                        ORDER BY spi.spi_name";


            //echo $query;exit();

            $result =DB::select($query);
            return json_decode(json_encode($result), true);


    }
    public function fetchStudentsList($co_id,$ass_type,$num)
    {

        
        if($num=='new'){
            $query = "SELECT
                        co.co_id,
                        co.co_course_id,
                        co.co_section,
                        co.co_course_cr,
                        co.parent_co_id,
                        sc.sc_id,
                        sc.sc_co_id,
                        sai.sai_id,
                        sai.sai_enrollment_no,
                        spi.spi_name,
                        '' cam_id,
                        '' obtained_marks,
                        '' total_marks,
                        '' cam_details,
                        '' cam_marks
                    FROM
                        student_courses sc
                         LEFT JOIN students_semester_record ON
                        sc.sc_sai_enrollment_no = students_semester_record.sai_enrollment_no
                        AND
                        sc.sc_co_sess_id = students_semester_record.sess_id
                        LEFT JOIN courses_offered co ON sc_co_id = co.co_id
                        LEFT JOIN courses_two ON sc_course_id = course_id
                        LEFT JOIN students_apply_info sai ON sc.sc_sai_enrollment_no = sai.sai_enrollment_no
                        LEFT JOIN students_personal_info spi ON sai.sai_spi_id = spi.spi_id
                    WHERE
                            sc_status in ('A','Block')
                            AND students_semester_record.ssr_semester_status in ('Active','Expected-Graduate')
                             AND sc.sc_enrollment_status = 1
                            AND (
                                    sai.sai_student_status IN (
                                        'Active','Expected-Graduate','Left','Struck off','Stuck-Off'
                                    )
                                OR
                                    sai.sai_semester_status = 'Defer'
                            )
                        AND (
                                co.co_id = ".$co_id."
                            OR
                                co.parent_co_id = ".$co_id."
                        )
                        ORDER BY TRIM(lower(spi.spi_name))";


           //echo $query;exit();

            $result =DB::select($query);
            return json_decode(json_encode($result), true);

        }else{

            $query = "SELECT
                        ccd.assessment_id,
                        assessment_name,
                        ccd.ccd_number,
                        cam.id,
                        ccd.id ccd_id,
                        ccd.ccd_details cam_details,
                        TO_CHAR(ccd.ccd_date,'yyyy-mm-dd') cam_date,
                        co.co_id,
                        co.co_course_id,
                        co.co_section,
                        co.co_course_cr,
                        co.parent_co_id,
                        sc.sc_id,
                        sc.sc_co_id,
                        sai.sai_id,
                        sai.sai_enrollment_no,
                        spi.spi_name,
                        cam.id cam_id,
                        cam.obtained_marks,
                        cam.total_marks,
                        cam.approval_status,
                        cam.updated_at,
                        get_assessment_total_students(cam.co_id,cam.assessment_id,cam.cam_number  ) total_students,
                        get_assessment_tot_app_std( cam.co_id,cam.assessment_id,cam.cam_number ) total_approved_students,
                        get_course_name(ccd.co_id) as course_name
                    FROM
                        student_courses sc
                        LEFT JOIN students_semester_record ON
                        sc.sc_sai_enrollment_no = students_semester_record.sai_enrollment_no
                        AND
                        sc.sc_co_sess_id = students_semester_record.sess_id
                        LEFT JOIN courses_offered co ON sc_co_id = co.co_id
                        LEFT JOIN students_apply_info sai ON sc.sc_sai_enrollment_no = sai.sai_enrollment_no
                        LEFT JOIN students_personal_info spi ON sai_spi_id = spi_id
                        LEFT JOIN course_criteria_details ccd ON ccd.co_id = (
                            CASE
                                WHEN (
                                    SELECT
                                        cos1.parent_co_id
                                    FROM
                                        courses_offered cos1
                                    WHERE
                                        cos1.co_id = sc.sc_co_id
                                ) IS NOT NULL THEN (
                                    SELECT
                                        cos2.parent_co_id
                                    FROM
                                        courses_offered cos2
                                    WHERE
                                        cos2.co_id = sc.sc_co_id
                                )
                                ELSE sc.sc_co_id
                            END
                        )
                        LEFT JOIN course_assessment_marks cam ON
                            ccd.id = cam.course_criteria_detail_id
                        AND
                            sc.sc_sai_enrollment_no = cam.sai_enrollment_no
                        LEFT JOIN assessments ON ccd.assessment_id = assessments.id
                        WHERE
                            (
                                    sc.sc_co_id = ".$co_id."
                                OR
                                    sc.sc_co_id IN (
                                        SELECT
                                            cos3.co_id
                                        FROM
                                            courses_offered cos3
                                        WHERE
                                            cos3.parent_co_id = ".$co_id."
                                    )
                            ) AND
                                sc_status in ('A','Block')
                            AND
                                sc.sc_enrollment_status = 1
                                AND students_semester_record.ssr_semester_status in ('Active','Expected-Graduate')

                            AND (
                                    sai.sai_student_status IN (
                                        'Active','Expected-Graduate','Left','Struck off','Stuck-Off'
                                    )
                                OR
                                    sai.sai_semester_status = 'Defer'
                            )
                             AND
                                ccd.assessment_id = ".$ass_type."
                            AND
                                ccd.ccd_number = ".$num."
                        ORDER BY TRIM(lower(spi.spi_name))";
         //echo $query;exit(); 
            $result =DB::select($query);


            return json_decode(json_encode($result), true);

        }
    }
    public function fetchApprovalStudentsList($co_id,$ass_type,$num)
    {

        $loginUser = Auth::user();
        $emp_user = $loginUser::find($loginUser->id)->employees;

        if(isset($co_id)){
            $query = "SELECT
                        co.co_id,
                        co.co_course_id,
                        co.co_section,
                        co.co_course_cr,
                        co.parent_co_id,
                        sc.sc_id,
                        sc.sc_co_id,
                        sai.sai_id,
                        sai.sai_enrollment_no,
                        spi.spi_name,
                        cam.id cam_id,
                        cam.obtained_marks,
                        cam.total_marks,
                        cam.cam_details,
                        (SELECT
                                COUNT(*)
                            FROM
                                course_assessment_marks tab2
                            WHERE
                                    tab2.sc_co_id = cam.sc_co_id
                                AND
                                    tab2.assessment_id = cam.assessment_id
                                AND
                                    tab2.cam_number = cam.cam_number
                                AND
                                    tab2.cam_marks_status=1
                        ) missing_marks,
                        cam.cam_marks_status,
                        to_char(cam.cam_date, 'yyyy-mm-dd')cam_date,
                        cam.approval_status,
                        GET_ASSESSMENT_TOTAL_STUDENTS(cam.co_id,assessment_id,cam_number) total_students,
                        GET_ASSESSMENT_TOT_APP_STD(cam.co_id,assessment_id,cam_number) total_approved_students
                    FROM
                        student_courses sc
                        LEFT JOIN courses_offered co ON sc_co_id = co.co_id
                        LEFT JOIN courses_two ON sc_course_id = course_id
                        LEFT JOIN students_apply_info sai ON sc.sc_sai_enrollment_no = sai.sai_enrollment_no
                        LEFT JOIN students_personal_info spi ON sai.sai_spi_id = spi.spi_id
                        LEFT JOIN course_assessment_marks cam ON
                            sc.sc_sai_enrollment_no = cam.sai_enrollment_no
                        AND
                            sc.sc_co_id = cam.sc_co_id
                    WHERE
                        sc_status in ('A','Block')
                            AND
                                sc.sc_enrollment_status = 1
                            AND (
                                    sai.sai_student_status IN (
                                        'Active','Expected-Graduate','Left','Struck off','Stuck-Off'
                                    )
                                OR
                                    sai.sai_semester_status = 'Defer'
                            ) AND (
                                    sai.sai_semester_status != 'Freeze'
                                OR
                                    sai.sai_semester_status IS NULL
                            )
                              AND
                                  co.co_id IN (SELECT co_id FROM courses_offered WHERE
                                      co_id = ".$co_id." or parent_co_id = ".$co_id." and co_status = 'E')

                        AND (
                                assessment_id IS NULL
                            OR
                                assessment_id = ".$ass_type."
                        )
                        AND cam_number = ".$num."
                        ORDER BY spi.spi_name";

            // echo $query;
            // exit();
            $result =DB::select($query);
            return json_decode(json_encode($result), true);
        }
    }
    public function getCriteriaID($co_id,$assessment_id)
    {
        $queryCriteria = "SELECT
                                    id
                                FROM
                                    course_criterias
                                WHERE
                                        co_id = ".$co_id."
                                    AND
                                        assessment_id = ".$assessment_id;

        $result =DB::select($queryCriteria);
        $cc_id =  json_decode(json_encode($result),true);
        return $cc_id[0]['id'];
    }
    public function getCriteriaWeightage($co_id,$assessment_id)
    {
        $queryCriteria = "SELECT
                                    cc_weightage
                                FROM
                                    course_criterias
                                WHERE
                                        co_id = ".$co_id."
                                    AND
                                        assessment_id = ".$assessment_id;

        $result =DB::select($queryCriteria);
        $cc_id =  json_decode(json_encode($result),true);
        return $cc_id[0]['cc_weightage'];
    }
    public function getMaxCamNumber($co_id,$assessment_id)
    {
        $queryMaxCam = "SELECT
                            COALESCE(MAX(cam_number),0)+1 cam_number
                        FROM
                            course_assessment_marks
                        WHERE
                                co_id = ".$co_id."
                                    AND
                                        assessment_id = ".$assessment_id;

        $resultCam =DB::select($queryMaxCam);
        $c_number =  json_decode(json_encode($resultCam),true);
        return $c_number[0]['cam_number'];
    }
    public function getCriteriaDetailsID($cc_id,$ccd_number)
    {
        $query = "SELECT
                        id
                        FROM
                            course_criteria_details
                        WHERE
                                course_criteria_id = ".$cc_id."
                            AND
                                ccd_number = ".$ccd_number;

        $result = DB::select($query);
        $ccd_id =  json_decode(json_encode($result),true);
        return $ccd_id[0]['id'];

    }
    public function storeStudentsMarks(array $data)
    {

          //  print_r($data);exit;

        $marks = 0;
        $count=0;
        $count1=0;
        $flag=0;
        $old_obt=0;

        $enrollment_no_update='';
        $enrollment_no_enter='';
        $co_id=$data['co_id'];
        $sess = new sessions();
        $sess->startSession();
        $criteriaID         = $this->getCriteriaID($data['co_id'],$data['assessment_id']);
        $criteriaWeightage  = $this->getCriteriaWeightage($data['co_id'],$data['assessment_id']);
        $ccd_status         = 0;

        //Insert Criteria Details
        if($data['ccd_id']=='' and $data['form_change_flag']==1)
        {
            $action     = "insert";
            $cam_number = $this->getMaxCamNumber($data['co_id'],$data['assessment_id']);
            $ccd_status = CourseCriteriaDetailController::storeCriteriaDetails($data['cam_date'], $data['cam_details'], $data['co_id'], $data['assessment_id'], $criteriaID, $cam_number, $criteriaWeightage,$action,$data['ccd_id']);
        }else if($data['form_change_flag']==1){
            $action     = "update";
            $cam_number = $data['cam_number'];
            $ccd_status = CourseCriteriaDetailController::storeCriteriaDetails($data['cam_date'], $data['cam_details'], $data['co_id'], $data['assessment_id'], $criteriaID, $data['cam_number'], $criteriaWeightage, $action,$data['ccd_id']);
        }

        $total_record = count($data['sai_enrollment_no']);

        $marksError = true; // #desc check if insert case then marks is enter


        if($ccd_status==1)
        {
            $null_marks = 0;
            for($i=0;$i<$total_record;$i++)
            {

                if($data['obt_marks'][$i]=='')
                {
                    $null_marks = $null_marks+1;
                }
                if(($data['obt_marks'][$i]!=$data['old_obt_marks'][$i])or($data['total_marks']!=$data['old_total_marks']))
                {
                    if($data['obt_marks'][$i]<=$data['total_marks'])
                    {
                        if($data['cam_id'][$i]!='null')
                        {

                            if($data['old_obt_marks'][$i] !=''){
                                   $flag=1;
                            $enrollment_no_update .= $data['sai_enrollment_no'][$i]. '('.$data['obt_marks'][$i].'/'.$data['total_marks']. ')'. ' , ';
                             $count++;

                         }

                          if($data['old_obt_marks'][$i] ==''){
                                   $old_obt=1;
                            $enrollment_no_enter .= $data['sai_enrollment_no'][$i]. '('.$data['obt_marks'][$i].'/'.$data['total_marks']. ')'. ' , ';
                             $count1++;

                         }

                            $marks = self::findOrFail($data['cam_id'][$i]);
                            $marks -> total_marks           = $data['total_marks'];
                            $marks -> obtained_marks        = $data['obt_marks'][$i];
                            if($data['obt_marks'][$i]==null){
                                $marks -> cam_marks_status  = 1;
								//$marks -> submission_status = 'YES';
                            }else{
                                $marks -> cam_marks_status  = 0;
								$marks -> submission_status = 'GRADED';
                            }
                            $marks -> approval_status       = 0;
                            $marks -> cam_status            = 'E';
                            if($data['obt_marks'][$i]!=$data['old_obt_marks'][$i])
                            {
                                $marks -> cam_desc          = 'Update';
                            }
                            $marks -> cam_entry_user        = $sess->sisuser_id;
                            $marks->save();

                        }else{
                            if($ccd_status)
                            {
//                                echo $criteriaID."----".$cam_number."----".$data['obt_marks'][$i];exit();

                                $marksError = false;

                                $marks = new self();
                                $marks -> co_id                 = $data['co_id'];
                                $marks -> sc_co_id              = $data['sc_co_id'][$i];
                                $marks -> assessment_id		    = $data['assessment_id'];
                                $marks -> course_criteria_id	= $criteriaID;
                                $marks -> sai_id                = $data['sai_id'][$i];
                                $marks -> sai_enrollment_no     = $data['sai_enrollment_no'][$i];
                                $marks -> total_marks           = $data['total_marks'];
                                $marks -> obtained_marks        = $data['obt_marks'][$i];
                                $marks -> cam_number            = $cam_number;
                                if($data['obt_marks'][$i]==null){
                                    $marks -> cam_marks_status  = 1;
                                }else{
                                    $marks -> cam_marks_status  = 0;
                                }
                                $marks -> approval_status       = 0;
                                $marks -> cam_status            = 'E';
                                $marks -> cam_desc              = 'Insert';
                                $marks -> course_criteria_detail_id= $this->getCriteriaDetailsID($criteriaID,$cam_number);
                                $marks -> cam_entry_user        = $sess->sisuser_id;
                                $marks->save();
                            }else{
                                return 0;
                            }
                        }
                    }else{
                        return 'C';
                    }
                }
            }
        }

/////////////////////////send sms and email marks changed ////////////////////////////////
$qu="select  employee_id,emp_email,concat(prefix,concat(emp_f_name,concat(' ',emp_l_name))) AS Emp_FullName from faculty_courses  left join emp_employees on fc_employee_id = emp_employees.employee_id where fc_co_id=$co_id and faculty_courses.FC_STATUS='E' and emp_email like '%@%' and employee_id not in (1605)  ";

                 $rs = DB::select($qu);

 //////////////////////////////////////////////////////////////
        if($flag==1){

            $sms = new SendSms();
            $login_id = $sess->emp_user_id;
            $phone_no=$sess->phone_no;
            $countlength = strlen($phone_no);
            $chknumber = substr($phone_no,0,2);

            $get_course_teacher=$sms->get_course_teacher($co_id, $login_id);

              $mesg='@'.date("l")." ". date("d-m-Y H:i:s");
                $subject='Student Marks change Alert !'.$get_course_teacher;
	$note='If you are not getting the sms on your cell no please email your phone No to
cmshelpdesk@bnu.edu.pk';
                $mess=$count.' Students Marks Changed '.$enrollment_no_update .' in '.$data['cam_details'].' '.$mesg.'  '. $get_course_teacher.'                   Note: ' .$note;

                if($countlength == 12){
                    if($chknumber == 92){
                        foreach ($rs as $key ) {
                              $teach_name=$key->emp_fullname;
                              $employee_id=$key->employee_id;
                              $emp_email=$key->emp_email;
							    $to=$emp_email;
                               $subject='Student Marks change Alert !'.$get_course_teacher;
                           $sms->teacher_marks_change_sendmail($to ,$subject,$mess);

                           $sms->send_sms( $count. ' '.' Student Marks change Alert ! ID '. $enrollment_no_update .' in '.$data['cam_details'].'  '. $get_course_teacher.' '.$mesg,$phone_no);

                         }


            $flag=0;
              }
           }
         }
////////////////////////////Enter New Marks Send Email/////////////////////////////

         if($old_obt==1){

            $sms = new SendSms();
            $login_id = $sess->emp_user_id;
             $phone_no=$sess->phone_no;
			 $employee_email = $sess->emp_email;
            $countlength = strlen($phone_no);
            $chknumber = substr($phone_no,0,2);
               $get_course_teacher=$sms->get_course_teacher($co_id, $login_id);

               $mesg='If your cell no is converted from one  Service provider to another than please contact to  cmshelpdesk@bnu.edu.pk.';
                $subject='Student Enter Marks Alert !'.$get_course_teacher;
                $mess=$count1.' Students Marks Entered '.$enrollment_no_enter .' in '.$data['cam_details'].'  '. $get_course_teacher.' '.$mesg;
                        foreach ($rs as $key ) {
							$to=$employee_email;
                         $sms->teacher_marks_change_sendmail($to ,$subject,$mess);

                        }
                    $old_obt=0;
         }
////////// //////////////////////////////////////////////// // #desc



        if(!$marks)
        {
            if($ccd_status==0)
            {
                return 0;
            }else{
                return 'D';
            }

        }else{
            return 'A-'.$null_marks;
            //return $null_marks;
        }


    }
    public function storeStudentsApproval(array $data)
    {
//        dd($data);
        $total_record = count($data['cam_id']);
        $checkCam = 0;
        $marks = '';

        for($i=0;$i<$total_record;$i++)
        {
            $sess = new sessions();
            $sess->startSession();

            if($data['cam_id'][$i]!='null')
            {
                $checkCam = 1;
                if(($data['obt_marks'][$i]!='null'))
                {
                    if(($data['approval_status'][$i]==null)or($data['approval_status'][$i]==0))
                    {
                        $marks = self::findOrFail($data['cam_id'][$i]);
                        $marks -> approval_status  = 1;
                        $marks -> cam_desc         = 'HOD Approval';
                        $marks->approval_hod_id=$sess->emp_user_id;
                        $marks -> cam_entry_user   = $sess->sisuser_id;
                        $marks->save();
                    }
                }
            }
        }
        if(!$marks){
            if($checkCam!=0){
                return 0;
            }else{
                return 2;
            }
        }else{
            return 1;
        }
    }
    public function getCourseAssessmentMarksHeader($course_co_id){
        $query="SELECT
                DISTINCT
                    course_assessment_marks.co_id,assessments.assessment_short_name,
                      course_assessment_marks.assessment_id,
                      assessments.assessment_name,
                      course_assessment_marks.total_marks,
                      cam_number,
                      get_teachername(course_assessment_marks.co_id) AS teacher_name,
                      dept_name
            FROM course_assessment_marks
              LEFT JOIN assessments ON assessments.id = course_assessment_marks.assessment_id
              LEFT JOIN student_courses on course_assessment_marks.SAI_ENROLLMENT_NO = student_courses.SC_SAI_ENROLLMENT_NO and course_assessment_marks.sc_co_id = student_courses.SC_CO_ID
              LEFT JOIN courses_offered ON courses_offered.co_id = course_assessment_marks.co_id
              LEFT JOIN departments ON departments.dept_id = courses_offered.co_dept_id
            WHERE course_assessment_marks.co_id = $course_co_id  and sc_status='A'
 ORDER BY assessment_id,cam_number ";
        return $student_actual_marks = DB::select($query);

    }
}
