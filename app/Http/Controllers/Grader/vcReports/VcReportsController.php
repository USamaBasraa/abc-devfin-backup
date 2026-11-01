<?php

namespace App\Http\Controllers\Grader\vcReports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use App\Models\Grader\Schools;
use App\Models\Grader\AcademicSessions;
use Illuminate\Support\Facades\Validator;

class VcReportsController extends Controller
{
    public function vc_DashBoard(){



        return view('grader.vcReports.vcReports');

    }
    public function getStduents(){

        $totalStudentsSql="
        WITH students AS (
            SELECT
                schools.school_code AS school_code,
                students_apply_info.*
            FROM
                students_apply_info left
                JOIN schools ON students_apply_info.sai_sdpo_school_id = schools.school_id
            WHERE
                1 = 1
                --AND students_apply_info.sai_sdpo_sess_id = 157
                AND sai_student_status = 'Active'
        )
        SELECT
            school_code,
            COUNT(*) AS active_students
        FROM
            students
        GROUP BY
            school_code
        order by active_students desc";
        $totalStudentsBySchools=DB::select($totalStudentsSql);

        $totalStudentsSql2="
                    SELECT
                sai_student_status,
                SUM(
                    CASE
                        WHEN sai_student_status = 'Active'      THEN
                            1
                        WHEN sai_student_status = 'Left'        THEN
                            1
                        WHEN sai_student_status = 'Stuck-Off'   THEN
                            1
                        ELSE
                            0
                    END
                ) AS total
            FROM
                students_apply_info
            WHERE
                sai_student_status IN (
                    'Active',
                    'Left',
                    'Stuck-Off'
                )
                AND sai_sdpo_sess_id = 156
            GROUP BY
                sai_student_status";
        $totalStudentsCount=DB::select($totalStudentsSql2);


        $gradingReport="
        SELECT
        COUNT(gh_grade) AS totalnum,
        gh_grade AS school_code,
        eg_grade_point
        FROM
        grade_history left
        JOIN exam_grades ON eg_grade = gh_grade
        WHERE
        gh_exam_fsubmit = 1
        AND gh_status = 'E'
        AND gh_co_sess_id = 156
        GROUP BY
        gh_grade,
        eg_grade_point
        ORDER BY
        gh_grade ASC
        ";
        $gradingReports=DB::select($gradingReport);
        $enrollmentReport="
                SELECT
                COUNT(DISTINCT sc_sai_enrollment_no) AS totalnum,
                school_code,
                school_id,
                (
                    SELECT
                        COUNT(*)
                    FROM
                        students_apply_info
                    WHERE
                        sai_sdpo_school_id = school_id
                        AND sai_student_status IN (
                            'Active',
                            'Expected-Graduate'
                        )
                ) AS active_students
            FROM
                student_courses left
                JOIN students_apply_info ON sc_sai_enrollment_no = sai_enrollment_no
                LEFT JOIN schools ON sai_sdpo_school_id = school_id
            WHERE
                sc_enrollment_status = 1
                AND sai_student_status IN (
                    'Active',
                    'Expected-Graduate',
                    'Left',
                    'Stuck-Off',
                    'Not Joined',
                    'Struck off'
                )
                AND sc_co_sess_id = 156
            GROUP BY
                school_code,
                school_id
            ORDER BY COUNT(DISTINCT sc_sai_enrollment_no)
        ";
        $enrollmentReports=DB::select($enrollmentReport);


        return response()->json(['status'=>true,
         'data' => $totalStudentsBySchools,
         'totalStudentsCount' => $totalStudentsCount,
         'gradingReports' => $gradingReports,
         'enrollmentReports' => $enrollmentReports,

    ]);


    }


   public function carPoolingReport(Request $request){

    $sessions = \App::call('App\Http\Controllers\Grader\AcademicSessionController@retrieve');
    $schools =  Schools::all();
    return view('CarPulling.Reports.carPoolingVcReport',compact('sessions','schools'));
   }
   public function reportData(Request $request){
 //dd($request->all());
$school = $request->school;
$report_type = $request->report_type;
$from = $request->from;
$to = $request->to;

$school = $request->school;
$report_type = $request->report_type;
$from = $request->from;
$to = $request->to;

$sqlData = "SELECT 
main.school_name,
main.active,
main.university_parking_count,
main.paid_parking_count,
ROUND((main.university_parking_count / main.active) * 100, 0) AS university_parking_percentage,
ROUND((main.paid_parking_count / main.active) * 100, 0) AS paid_parking_percentage,
COALESCE(nbs.nbs_count, 0) AS nbs_count
FROM (
SELECT 
    --COUNT(*) AS university_parking_count,
    SUM(CASE WHEN vr_parking = 'University Parking' THEN 1 ELSE 0 END) AS university_parking_count,
      SUM(CASE WHEN vr_parking = 'Paid Parking' THEN 1 ELSE 0 END) AS paid_parking_count,
    school_name,
    active
FROM (
    SELECT
        distinct vr_username,
        schools.school_id,
        school_name,
        (
            SELECT COUNT(*)
            FROM students_apply_info
            WHERE sai_student_status IN ('Active', 'Expected-Graduate') 
            AND sai_sdpo_school_id = schools.school_id
        ) AS active,vr_parking
    FROM
        students_apply_info
        LEFT JOIN sisusers ON username = LOWER(sai_enrollment_no)
        LEFT JOIN vehicle_registration ON LOWER(sai_enrollment_no) = LOWER(vr_username)
        LEFT JOIN schools ON schools.school_id = students_apply_info.sai_sdpo_school_id
    WHERE
       ( (vr_status = 'E' AND vr_parking = 'University Parking')
        OR (vr_status = 'E' AND vr_parking = 'Paid Parking'))
        AND group_id = 4
        AND sai_student_status IN ('Active', 'Expected-Graduate')";
        if((isset($from) && $from != '') && isset($to) && $to != ''  ){
            $sqlData .= "  AND vehicle_registration.vr_entry_datetime BETWEEN '$from' AND '$to'";
        }
        $sqlData .= " )
GROUP BY 
    school_name,
    active
) main
LEFT JOIN (
SELECT 
    COUNT(DISTINCT ssd_sai_enrollment_no) AS nbs_count,
    school_name
FROM 
    students_scholarships_details
    LEFT JOIN students_apply_info ON sai_enrollment_no = ssd_sai_enrollment_no
    LEFT JOIN schools ON schools.school_id = students_apply_info.sai_sdpo_school_id
WHERE 
    ssd_need_base IS NOT NULL 
    AND LOWER(ssd_sai_enrollment_no) IN (
        SELECT LOWER(vr_username) 
        FROM vehicle_registration 
        WHERE vr_status = 'E'";
         if((isset($from) && $from != '') && isset($to) && $to != ''  ){
            $sqlData .= "  AND vehicle_registration.vr_entry_datetime BETWEEN '$from' AND '$to'";
        }
        //   -- and vehicle_registration.vr_entry_datetime between '01-JUL-23' and '30-JUN-24'
        // --  and vehicle_registration.vr_entry_datetime between '01-JUL-22' and '30-JUN-23'
        $sqlData .= " )
    AND sai_student_status IN ('Active', 'Expected-Graduate')
GROUP BY 
    school_name
) nbs ON main.school_name = nbs.school_name";

          // echo $sqlData;exit;
                $res = DB::select($sqlData);
                //dd($res);
 return  $res;
   }

   function reportDataStudentWise(Request $request){
   
    // $validator = Validator::make($request->all(), [
           
    //     'school' => "required"

    // ],[
    //    'school.required' => 'School Field need to be filled in',
      
    // ]);

    // if ($validator->fails()) {
    //     // 406 is for content not found from user agent
    //     return response()->json(['status' => false, 'message' => $validator->errors()->first()],406);
    // }
    $school = $request->get('school');
     
        $sql = "SELECT
        vr_username,aai_id,
        vehicle_registration.vr_vehicle_no,
        school_name,vr_parking,
       case when (select count(*) from students_scholarships_details where lower(ssd_sai_enrollment_no) = lower(vr_username) and (ssd_need_base != 0 or ssd_need_base not in null) ) > 0 then 'Yes' else 'No NBS' end as check_nbs
    FROM
        vehicle_registration
        LEFT JOIN sisusers ON vr_username = username
        LEFT JOIN students_apply_info ON lower(vr_username) = lower(sai_enrollment_no)
        LEFT JOIN schools ON schools.school_id = students_apply_info.sai_sdpo_school_id
        left join admission_apply_info on lower(cms_sai_enrollment_no) = lower(vr_username)
    WHERE
            group_id = 4
        AND
            vr_status = 'E'";
            if(isset($school) && $school != ''){
                $sql .= "AND students_apply_info.sai_sdpo_school_id = $school";
            }
            $sql .= "AND
            sai_student_status IN (
                'Active','Expected-Graduate'
            )
            and cms_sai_enrollment_no is not null
      
            order by schools.school_id asc";
           //echo $sql;exit;

            return $res = DB::select($sql);



   }


   
   function admissionApplicant(){
    $sessions = \App::call('App\Http\Controllers\Grader\AcademicSessionController@retrieve');
    $schools =  AcademicSessions::all();
    return view('grader.reports.applicantdata',compact('sessions','schools'));
}

function reportDataApplicant(Request $request){
//dd($request->all());
$session = $request->session;
$status = $request->status;
$q_status = $request->q_status;
    $sql = "
    SELECT
        aai_id,
        aai_api_id,
        sess_name,
        api_name,
        prog_name,
        school_name,
        last_qualification,
        obtained_marks, total_marks,aai_status from (
        select     aai_id,
        aai_api_id,
        sess_name,
        api_name,
        prog_name,
        school_name,
        
        (SELECT aq_degree_name FROM admission_qualification WHERE aq_degree_name IN ('Intermediate','A_Level') AND aq_api_id = aai_api_id and rownum = 1) AS last_qualification,
        CASE
            WHEN (SELECT aq_obtained_marks FROM admission_qualification WHERE aq_degree_name = 'Intermediate' AND aq_api_id = aai_api_id) IS NULL THEN 
                (SELECT ADQ_EQUAL_OBTAIN_MARKS FROM admission_qualification_detail WHERE aqd_api_id = aai_api_id AND aqd_degree_name = 'A_Level' AND ROWNUM = 1)
            ELSE
                (SELECT aq_obtained_marks FROM admission_qualification WHERE aq_degree_name = 'Intermediate' AND aq_api_id = aai_api_id)
        END AS obtained_marks,
        CASE
            WHEN (SELECT aq_total_marks FROM admission_qualification WHERE aq_degree_name = 'Intermediate' AND aq_api_id = aai_api_id) IS NULL THEN
                (SELECT ADQ_EQUAL_TOTAL_MARKS FROM admission_qualification_detail WHERE aqd_api_id = aai_api_id AND aqd_degree_name = 'A_Level' AND ROWNUM = 1)
            ELSE
                (SELECT aq_total_marks FROM admission_qualification WHERE aq_degree_name = 'Intermediate' AND aq_api_id = aai_api_id)
        END AS total_marks,aai_status
    FROM
        admission_apply_info
        LEFT JOIN admission_personal_info ON aai_api_id = api_id
        LEFT JOIN academic_sessions ON sess_id = aai_sdpo_sess_id
        LEFT JOIN schools ON school_id = aai_sdpo_school_id
        LEFT JOIN programs ON prog_id = aai_sdpo_prog_id
    WHERE
        aai_sdpo_sess_id = $session";
        if(isset($status) && $status != ''){
            $sql .= "AND aai_status = '$status'";
        }
        $sql .= ")";
        if(isset($q_status) && $q_status != ''){
            $sql .= "where last_qualification = '$q_status'";
        }
       
//  /echo $sql;exit;
$res = DB::select($sql);
//dd($res);
return  $res;

}



}
