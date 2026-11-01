<?php

namespace App\Http\Controllers\Grader\Eportal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use DB;
use Storage;
use Illuminate\Support\Facades\Session;
use Hypweb\Flysystem\GoogleDrive\GoogleDriveAdapter;
use Google_Client;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class VCePortalController extends Controller
{
    function getDashboadrd()
    {
        $employee_id = auth()->user()->employee_id;
        $allowedAllReq = array("8996", "3455");
        $requster_sql = "SELECT
                eportal_request.id er_id,
                eportal_request.ER_REQUEST_FILE,
                eportal_request.ER_REQUEST_STATUS,
                eportal_request.ec_id          AS id,
                (select EC_TITLE from EPORTAL_CATEGORIES WHERE id = EPORTAL_REQUEST.EC_ID)as title,
                EPORTAL_REQUEST.ER_DESCRIPTION as description,
                username,
                email,
                spi_name as full_name,
                father_name,
                spi_mobile as mobile,
                school,
                programs,
                dept_id,
                prog_id,
                group_id
                FROM EPORTAL_REQUEST
                JOIN sisusers ON lower(EPORTAL_REQUEST.ER_SAI_ENROLLMENT_NO) = lower(sisusers.USERNAME)
                LEFT JOIN students_apply_info ON lower(students_apply_info.sai_enrollment_no) = lower(eportal_request.er_sai_enrollment_no)
                LEFT JOIN students_personal_info ON spi_id = sai_spi_id
                where   eportal_request.ER_REQUEST_STATUS = 'Pending'
                ";
        $requster_info = DB::select($requster_sql);

        // dd($requster_info);
        $EpCateg = "SELECT DISTINCT
        ( ec_title ),
        eportal_categories.id,
        (select count(*) from eportal_request where eportal_request.ec_id = eportal_categories.id and er_request_status = 'Pending' ) as total_req
    FROM
        eportal_request
        LEFT JOIN eportal_categories ON ec_id = eportal_categories.id
        left join eportal_request_details on er_id = eportal_request.id
    WHERE
        ec_status = 'E' and er_ecd_resolving_user = $employee_id";



        //$EpCateg = "select * from EPORTAL_CATEGORIES where ec_status = 'E'";
        // echo $requster_info; exit;
        $resEpCateg = DB::select($EpCateg);
        $staffMembers = $this->get_drop_down_users(8996);

        return view('ePortal.VCePortal.eportal_dashboard', ['res' => $requster_info, 'resEpCateg' => $resEpCateg, 'staffMembers' => $staffMembers]);
    }

    function getReport()
    {
        $sql = "select * from eportal_request";
        $res = DB::select($sql);

        $deptSql = "select * from departments where dept_id in (23,41,61,24)";
        $resDeptSql = DB::select($deptSql);
        return view('ePortal.VCePortal.eportal_report', ['res' => $res, 'dept' => $resDeptSql]);
    }
    function getSummary(Request $request)
    {
        $from_date = $request->input('from_date');
        $to_date = $request->input('to_date');

        $where = "er.ec_id NOT IN (9, 84)";
        $bindings = [];

        if ($from_date) {
            $where .= " AND substr(er.er_entry_datetime, 1, 10) >= ?";
            $bindings[] = $from_date;
        }

        if ($to_date) {
            $where .= " AND substr(er.er_entry_datetime, 1, 10) <= ?";
            $bindings[] = $to_date;
        }

        $sql = "SELECT
        subquery.ec_title,
        subquery.total_requests,
        subquery.count_resolving_before_escalation,
        round((subquery.count_resolving_before_escalation / subquery.total_requests * 100),0) AS resolving_before_escalation_percentage,
        subquery.count_resolving_after_escalation,
        round((subquery.count_resolving_after_escalation / subquery.total_requests * 100),0) AS resolving_after_escalation_percentage,
        subquery.pending_request
    FROM
        (
            SELECT
                COUNT(*) AS total_requests,
                er.ec_id,
                ec_title,
                SUM(
                    CASE
                        WHEN (
                            (er.er_resolving_datetime <= er.er_escalation_time AND er.er_request_status NOT IN ('Pending'))
                            OR
                            (er.er_escalation_time IS NULL AND er.er_request_status IN ('Completed', 'Declined'))
                        ) THEN 1
                        ELSE 0
                    END
                ) AS count_resolving_before_escalation,
                SUM(CASE WHEN er.er_resolving_datetime > er.er_escalation_time AND er.er_request_status IN ('Completed', 'Declined') THEN 1 ELSE 0 END) AS count_resolving_after_escalation,
                SUM(CASE WHEN er.er_request_status IN ('Pending') THEN 1 ELSE 0 END) AS pending_request
            FROM
                eportal_request er
                LEFT JOIN eportal_categories ON er.ec_id = eportal_categories.id
            WHERE
                $where
            GROUP BY
                er.ec_id, ec_title
            order by ec_id asc
        ) subquery";

        $res = DB::select($sql, $bindings);

        return view('ePortal.VCePortal.eportal_summary', [
            'res' => $res,
            'from_date' => $from_date,
            'to_date' => $to_date
        ]);
    }
    function getcatories(Request $request)
    {
        $sel = $request->input('sel');

        $sql = "select id,
        ec_title,
            (select count(*) from eportal_request where ec_id = eportal_categories.id) as count from eportal_categories where 1 = 1";
        if ($sel == 23) {
            $sql .= " and id in (1,2,3,9,10,11,12,13,14)";
        } else if ($sel == 41) {
            $sql .= " and id in (6,4)";
        } else if ($sel == 61) {
            $sql .= " and id in (18,25)";
        } else if ($sel == 24) {
            $sql .= " and id in (61,82)";
        }
        //echo $sql;exit;
        return $res = DB::select($sql);
    }
    function fetchReport(Request $request)
    {
        $sel = $request->input('sel');
        $categ = $request->input('categ');
        // echo $sel;exit;
        $sql = "SELECT
                    eportal_request.id as er_id,
                    er_sai_enrollment_no,
                    spi_name || ' (' || er_sai_enrollment_no || ')' as student_name,
                    er_entry_datetime,
                    school_code,
                    er_resolving_datetime,
                     coalesce(er_escalation_time, to_timestamp_tz('01-01-1900 12:00:00 AM +00:00', 'DD-MM-YYYY HH:MI:SS AM TZH:TZM')) AS er_escalation_time,
                    
                    ec_title,er_request_status
                FROM
                    eportal_request
                    LEFT JOIN students_apply_info ON sai_enrollment_no = er_sai_enrollment_no
                    LEFT JOIN students_personal_info ON sai_spi_id = spi_id
                    left join schools on school_id = sai_sdpo_school_id
                    left join eportal_categories on eportal_categories.id =ec_id
                where ec_id not in (83,9)";
        if ($categ == 0) {
            if ($sel == 23) {
                $sql .= " and ec_id in (1,2,3,9,10,11,12,13,14)";
            } else if ($sel == 41) {
                $sql .= " and ec_id in (6,4)";
            } else if ($sel == 61) {
                $sql .= " and ec_id in (18,25)";
            } else if ($sel == 24) {
                $sql .= " and ec_id in (61,82)";
            }
        } else {
            $sql .= " and ec_id in $categ";
        }
        //$sql .= " and ec_id in $sel";

        $sql .= " order by er_resolving_datetime desc";
        //echo $sql;exit;
        return $res = DB::select($sql);
    }

    function get_drop_down_users($employee_id)
    {
        $sql = "SELECT
        employee_id     AS id,
        to_char(emp_dept_id)     AS parent_id,
        prefix ||' '
        || emp_f_name
        || ' '
        || emp_l_name
        ||' : '||
        school_code
        ||' : '||
        dept_name
        ||' : '||
        case
            when emp_type=4 then 'Dean'
            when emp_type=5 then 'HOD'
            else '' end as name,
             '3'             AS ordername,
        'EMPLOYEE' as group_name
        FROM
            emp_employees
            left join schools on school_id = emp_school_id
            left join departments on dept_id = emp_employees.emp_dept_id
        UNION ALL
        SELECT
            ID     AS id,
        tmg_group_name    AS parent_id,
        task_management_group.tmg_group_name as name,
                'G'             AS ordername,
                tmg_group_name as group_name
        FROM
        task_management_group
        where  task_management_group.tmg_entery_user_id=$employee_id";
        //echo $sql;exit;
        return $obj_users = DB::select($sql);
    }

    function getCateg(Request $request)
    {
        // echo "!23";exit;
        $ec_id = $request->input('ec_id');
        $sel = $request->input('sel');
        $taskstatus = $request->input('taskStatus');
        $employee_id = auth()->user()->employee_id;
        $student = $request->input('student');
        $requster_sql = "SELECT
        eportal_request.ec_id,
		ec_title,
        eportal_request.id er_id,
        eportal_request.ER_REQUEST_FILE,
        eportal_request.ER_REQUEST_STATUS,
        eportal_request. GOOGLE_DRIVE_FILE_NAME,
        eportal_request.GOOGLE_DRIVE_URL,
        er_remarks,
        eportal_request.ec_id          AS id,
        er_student_upload_challan,
        eportal_request.ER_ESCALATION_TIME as esclation_time,
        eportal_request.ER_entry_datetime as erdatetime,
        (select EC_TITLE from EPORTAL_CATEGORIES WHERE id = EPORTAL_REQUEST.EC_ID)as title,
        EPORTAL_REQUEST.ER_DESCRIPTION as description,
        username,
        email,
        spi_name as full_name,
        father_name,
        spi_mobile as mobile,
        school,
        programs,
        dept_id,
        prog_id,
        group_id
        FROM EPORTAL_REQUEST
        Left JOIN sisusers ON lower(EPORTAL_REQUEST.ER_SAI_ENROLLMENT_NO) = lower(sisusers.USERNAME)
        LEFT JOIN students_apply_info ON lower(students_apply_info.sai_enrollment_no) = lower(eportal_request.er_sai_enrollment_no)
        LEFT JOIN students_personal_info ON spi_id = sai_spi_id
        left join eportal_categories on ec_id = eportal_categories.id
        left join eportal_request_details on er_id = eportal_request.id
        where   1=1 and er_ecd_resolving_user = 8996 ";
        if ($taskstatus == 'Completed') {
            $requster_sql .= " and eportal_request.ER_REQUEST_STATUS in ('$taskstatus','Declined')";
        } else {
            $requster_sql .= " and eportal_request.ER_REQUEST_STATUS in ('$taskstatus','Forward')";
        }
        if ($sel != 'Pending') {
            if ($ec_id != '0') {
                $requster_sql .= " and ec_id = $ec_id";
            }
        }
        if ($student != 'noStudent') {
            $requster_sql .= " and (LOWER(er_sai_enrollment_no) LIKE '%' || LOWER('$student') || '%' or LOWER(spi_name) LIKE '%' || LOWER('$student') || '%')";
        }
        $requster_sql .= " order by eportal_request.ER_RESOLVING_DATETIME desc";

        //echo $requster_sql;exit;



        return $requster_info = DB::select($requster_sql);
        return response()->json([
            'data' => $requster_info,
            'status' => true
        ]);
    }

    function getForwardUser(Request $request)
    {
        //dd($request->all());
        $er_id = $request->get('er_id');
        $sql = "SELECT
                    prefix || emp_f_name || ' ' || emp_l_name as full_name,
                 (SELECT
                        (er_request_status)
                        FROM
                            eportal_request
                        WHERE
                            
                                parent_er_id = '$er_id'
                                and rownum = 1
                                ) as forwarduserstatus,
                (
                    SELECT
                        ( ER_REMARKS )
                    FROM
                        eportal_request
                    WHERE
                            parent_er_id = '$er_id'
                        AND
                            ROWNUM = 1
                ) AS forwarduserremarks
                FROM
                    eportal_request_details
                    LEFT JOIN emp_employees ON employee_id = er_ecd_resolving_user
                WHERE
                    er_id in  (select max( id) from eportal_request where id=$er_id or parent_er_id='$er_id')
                    AND
                        er_ecd_resolving_user NOT IN 8996";
        // echo $sql;exit;
        $res = DB::select($sql);
        return response()->json(['data' => $res, 'status' => true]);

    }
    public function getApprovalAuthorityRequestReport(Request $request)
    {

        $id = $request->input('id');

        //  dd(auth()->user());
        // $sqlApprovalAuthority="select * from eportal_request_details"
        // left join departments on eportal_request_details.er_ecd_dep=departments.dept_id
        // where  er_ecd_approval_user is not null and er_id='".$id."'";
        $sqlApprovalAuthority = "

    SELECT
    er_id,
    emp_res.emp_f_name || emp_res.emp_l_name AS res_full_name,
    res_dept.dept_name as res_dept,
    appr.emp_f_name || appr.emp_l_name AS full_name,
    appr_dept.dept_name AS department,
    erd_status,er_remarks,
    er_request_status,

    (
        SELECT
            eportal_categories.ec_title
        FROM
            eportal_request
            LEFT JOIN eportal_categories ON eportal_request.ec_id = eportal_categories.id
        WHERE
                eportal_request.id = eportal_request_details.er_id
            AND
                ROWNUM = 1
    ) AS title,
    er_ecd_remarks,
    er_escalation_time,
    er_entry_datetime,
    eportal_request_details.er_ecd_appr_datetime,
    er_resolving_datetime,er_description
FROM
    eportal_request_details
    left join eportal_request on eportal_request_details.er_id = eportal_request.id
    LEFT JOIN departments appr_dept ON eportal_request_details.er_ecd_dep = appr_dept.dept_id
    LEFT JOIN emp_employees appr ON er_ecd_approval_user = appr.employee_id
    LEFT JOIN emp_employees emp_res ON er_ecd_resolving_user = emp_res.employee_id
    LEFT JOIN departments res_dept ON emp_res.emp_dept_id = res_dept.dept_id
WHERE
er_ecd_completion_days is null
and er_ecd_viewer_user is null
 AND (
        CASE
            WHEN eportal_request.ec_id = 86 AND er_ecd_instruction_categories IS NULL THEN 1
            WHEN eportal_request.ec_id = 6 AND er_ecd_viewer_user IS NULL AND er_ecd_instruction_categories IS NULL THEN 1
            WHEN eportal_request.ec_id in (23,18,25) AND er_ecd_viewer_user IS NULL THEN 1
            WHEN eportal_request.ec_id IN (115, 91, 85, 88,101,100,98,97,99,96,83,81,82,87,89,90,92,111,85,103,104,105,106,107,108,109,113,112,114,88,93,116,120,119) AND er_ecd_resolving_user IS NOT NULL THEN 1
            WHEN eportal_request.ec_id IN (12,2,3,1,61,9,11,13,15) AND  er_ecd_completion_days IS NULL THEN 1
            ELSE 0
        END = 1
    )
    and er_id  = $id
    order by er_ecd_approval_user asc
";//$id";
//echo $sqlApprovalAuthority; //exit();
        $approvalAuthority = DB::select($sqlApprovalAuthority);
        return response()->json([
            'data' => $approvalAuthority,
            'status' => true
        ]);
    }

    public function getApprovalAuthorityRequest(Request $request)
    {

        $id = $request->input('id');

        //  dd(auth()->user());
        // $sqlApprovalAuthority="select * from eportal_request_details"
        // left join departments on eportal_request_details.er_ecd_dep=departments.dept_id
        // where  er_ecd_approval_user is not null and er_id='".$id."'";
        $sqlApprovalAuthority = "

    SELECT
    er_id,
    emp_res.emp_f_name || emp_res.emp_l_name AS res_full_name,
    res_dept.dept_name as res_dept,
    appr.emp_f_name || appr.emp_l_name AS full_name,
    appr_dept.dept_name AS department,
    erd_status,
    er_request_status,

    (
        SELECT
            eportal_categories.ec_title
        FROM
            eportal_request
            LEFT JOIN eportal_categories ON eportal_request.ec_id = eportal_categories.id
        WHERE
                eportal_request.id = eportal_request_details.er_id
            AND
                ROWNUM = 1
    ) AS title,
    er_ecd_remarks,
    er_escalation_time,
    er_entry_datetime,
    eportal_request_details.er_ecd_appr_datetime,
    er_resolving_datetime
FROM
    eportal_request_details
    left join eportal_request on eportal_request_details.er_id = eportal_request.id
    LEFT JOIN departments appr_dept ON eportal_request_details.er_ecd_dep = appr_dept.dept_id
    LEFT JOIN emp_employees appr ON er_ecd_approval_user = appr.employee_id
    LEFT JOIN emp_employees emp_res ON er_ecd_resolving_user = emp_res.employee_id
    LEFT JOIN departments res_dept ON emp_res.emp_dept_id = res_dept.dept_id
WHERE
er_ecd_completion_days is null
and er_ecd_viewer_user is null
  AND (
        CASE
            WHEN eportal_request.ec_id = 86 AND er_ecd_instruction_categories IS NULL THEN 1
            WHEN eportal_request.ec_id = 6 AND er_ecd_viewer_user IS NULL AND er_ecd_instruction_categories IS NULL THEN 1
            WHEN eportal_request.ec_id in (23,18,25) AND er_ecd_viewer_user IS NULL THEN 1
            WHEN eportal_request.ec_id IN (115, 91, 85, 88,101,100,98,97,99,96,83,81,82,87,89,90,92,111,85,103,104,105,106,107,108,109,113,112,114,88,93,116,120,119) AND er_ecd_resolving_user IS NOT NULL THEN 1
            WHEN eportal_request.ec_id IN (12,2,3,1,61,9,11,13,15) AND  er_ecd_completion_days IS NULL THEN 1
            ELSE 0
        END = 1
    )
    and er_id  = $id
    order by er_ecd_approval_user asc
";//$id";
//echo $sqlApprovalAuthority; exit();
        $approvalAuthority = DB::select($sqlApprovalAuthority);
        return response()->json([
            'data' => $approvalAuthority,
            'status' => true
        ]);
    }


    function getApproval()
    {
        $employee_id = auth()->user()->employee_id;
        $allowedAllReq = array("8996", "3455");
        $requster_sql = "SELECT
            eportal_request.id er_id,

               eportal_request.ER_REQUEST_STATUS,
                eportal_request.ec_id          AS id,
                eportal_request.ER_REQUEST_FILE          AS ER_REQUEST_FILE,
                eportal_request_details.id as erd_id,
               (select EC_TITLE from EPORTAL_CATEGORIES WHERE id = EPORTAL_REQUEST.EC_ID)as title,
               EPORTAL_REQUEST.ER_DESCRIPTION as description,
               username,
               email,
               spi_name as full_name,
                father_name,
                spi_mobile as mobile,
               school,
               programs,
               dept_id,
               prog_id,
               group_id,
               (select count(*) from eportal_request ep
                left join EPORTAL_REQUEST_DETAILS on ep.id = EPORTAL_REQUEST_DETAILS.er_id
                where ep.ec_id = eportal_request.ec_id  and EPORTAL_REQUEST_DETAILS.erd_status = 'Pending'  and er_ecd_approval_user is not null) as count_request,
                ER_REQUEST_FILE
               FROM EPORTAL_REQUEST
               left JOIN sisusers ON lower(eportal_request.er_sai_enrollment_no) = lower(sisusers.username)
               LEFT JOIN students_apply_info ON lower(students_apply_info.sai_enrollment_no) = lower(eportal_request.er_sai_enrollment_no)
               LEFT JOIN students_personal_info ON spi_id = sai_spi_id
               left join EPORTAL_REQUEST_DETAILS on er_id = eportal_request.id
               WHERE EPORTAL_REQUEST_DETAILS.erd_status in ('Pending','Rejected')";
        if ($employee_id != 8996) {
            $requster_sql .= "and er_ecd_approval_user = $employee_id";
        }

        $requster_sql .= "and er_ecd_approval_user is not null";
        $requster_info = DB::select($requster_sql);
        // echo $requster_sql;exit;

        $EpCateg = "SELECT
            count(*) as total_rec,ec_id,ec_title,eportal_categories.id
        FROM
            eportal_request_details
            left join eportal_request on er_id  = eportal_request.id
            left join eportal_categories on eportal_request.ec_id = eportal_categories.id
        WHERE
            erd_status = 'Pending'";

        if (!in_array(auth()->user()->employee_id, $allowedAllReq)) {
            $EpCateg .= " and er_ecd_approval_user = $employee_id";
        }
        $EpCateg .= " group by ec_id,ec_title,eportal_categories.id";
        //$EpCateg = "select * from EPORTAL_CATEGORIES where ec_status = 'E'";
        //echo $EpCateg; exit;
        $resEpCateg = DB::select($EpCateg);



        return view('ePortal.VCePortal.eportal_tracking', ['resEpCateg' => $resEpCateg]);
    }

    function getApprCateg(Request $request)
    {



        $employee_id = auth()->user()->employee_id;
        $taskstatus = $request->input('taskStatus');
        $ec_id = $request->input('ec_id');
        $sel = $request->input('sel');
        $allowedAllReq = array("8996", "3455","8395");

        // dd($request->all());
        if ($employee_id == 8996 || $employee_id == 822 || $employee_id == 8395) {
            // $requster_sql .= "and eportal_request.ec_id NOT IN (41)";
            // $requster_sql .= "and eportal_request.er_request_status = 'Pending' ";
            $requster_sql = "SELECT
                        distinct eportal_request.er_sai_enrollment_no,ec_id,
                        case when spi_name  is null then 
                            (select api_name from admission_apply_info 
                            left join admission_personal_info on aai_api_id = api_id
                            where aai_id = er_sai_enrollment_no) else spi_name end
                        AS full_name,
                        eportal_request.id as er_id,
                        case when school  is null then 
                            (select school_name from admission_apply_info 
                            left join admission_personal_info on aai_api_id = api_id
                            left join schools on aai_sdpo_school_id = school_id
                            where aai_id = er_sai_enrollment_no) else school end
                        AS school,       
                        case when programs  is null then 
                            (select prog_name from admission_apply_info 
                            left join admission_personal_info on aai_api_id = api_id
                            left join programs on aai_sdpo_prog_id = prog_id
                            where aai_id = er_sai_enrollment_no) else programs end
                        AS programs,
                        er_request_status,
                        er_description as description,
                        spi_mobile as mobile,
                         google_drive_url,
                        er_request_file,
                        (
                            SELECT
                                emp_employees.emp_email
                            FROM
                                emp_employees
                            WHERE
                                emp_employees.emp_dept_id = eportal_categories.ec_department_id
                                AND emp_type IN ( 5 )
                                AND emp_status = 'E'
                                AND ROWNUM = 1
                        )AS emp_email,
                        eportal_request.er_description,
                        (
                            SELECT
                                prefix
                                || ' '
                                || emp_employees.emp_f_name
                                || ' '
                                || emp_employees.emp_l_name
                            FROM
                                emp_employees
                            WHERE
                                emp_employees.emp_dept_id = eportal_categories.ec_department_id
                                AND emp_type IN ( 5 )
                                AND emp_status = 'E'
                                AND ROWNUM = 1
                        )               AS sender_name,
                        eportal_categories.ec_title,
                        eportal_request.er_escalation_time,
                        eportal_request.er_entry_datetime,
                        eportal_request.er_description,
                        systimestamp    AS currenttime,
                        'vc@bnu.edu.pk' AS vc
                    FROM
                        eportal_request
                        LEFT JOIN eportal_categories ON eportal_request.ec_id = eportal_categories.id
                   left JOIN sisusers ON lower(eportal_request.er_sai_enrollment_no) = lower(sisusers.username)
                   LEFT JOIN students_apply_info ON lower(students_apply_info.sai_enrollment_no) = lower(eportal_request.er_sai_enrollment_no)
                   LEFT JOIN students_personal_info ON spi_id = sai_spi_id
                   left join EPORTAL_REQUEST_DETAILS on er_id = eportal_request.id
                   left join schools on students_personal_info.spi_school_id=schools.school_id
                   left join programs on students_personal_info.spi_prog_id=programs.prog_id
                    WHERE
                        1 = 1
                        AND eportal_request.ec_id NOT IN ( 41,102,9,84,120)";
            if ($taskstatus == 'Approved') {
                $requster_sql .= " AND eportal_request.er_request_status in ('Completed','Declined')";
            } else {

                $requster_sql .= " AND systimestamp > eportal_request.er_escalation_time
                             AND eportal_request.er_request_status = ('Pending')
                              order by eportal_request.ec_id asc";
            }

        }

        //echo $requster_sql;exit;
        return $requster_info = DB::select($requster_sql);
    }
    function markCompleted(Request $request)
    {

        $status = $request->get('action');
        $remarks = $request->get('remarks');
        $id = $request->get('er_id');
        $ec_id = $request->get('ec_id');
        $stdroll = $request->get('stdroll');
        $email = strtoupper($stdroll) . '@bnu.edu.pk';
        $ec_title = $request->get('ec_title');
        $users = $request->get('users');
        $currentDateTime = Carbon::now('Asia/Karachi')->format('Y-m-d H:i:s');
        $employeeName = [];
        $allow = 0;




        if ($status == 'Forward') {

            $chkalreadyForwarded = "select * from eportal_request where ec_id=83 and er_request_status in ('Forward','Pending') and er_sai_enrollment_no = upper('$stdroll')";
            $reschkalreadyForwarded = DB::select($chkalreadyForwarded);


            if (count($reschkalreadyForwarded)) {
                return response()->json(['message' => 'Already Forwarded', 'allow' => $allow, 'existArr' => [], 'status' => false]);
            }

            if (empty($users)) {
                return response()->json(['message' => 'Please Select Forwarding User', 'allow' => $allow, 'existArr' => [], 'status' => false]);
            }

            $sqlEportalReq = "select * from eportal_request where ec_id=$ec_id and er_request_status in ('Forward','Pending') and er_sai_enrollment_no = upper('$stdroll')";
            $ressqlEportalReq = DB::select($sqlEportalReq);
            //echo $sqlEportalReq.'<br>';exit;
            if (count($ressqlEportalReq) == 1) {

                $insertEportalRequest = "INSERT INTO eportal_request (
                ec_id,
                er_sai_enrollment_no,
                er_request_status,
                er_description,
                er_entry_datetime,
                er_entry_user_id,
                ER_ESCALATION_TIME,
				ER_REQUEST_FILE,
                parent_er_id
            ) SELECT
                '83',
                er_sai_enrollment_no,
                'Pending',
                er_description,
                CURRENT_TIMESTAMP,
                er_entry_user_id,
				sysdate+2,
				ER_REQUEST_FILE,
                '$id'
            FROM
                eportal_request
            WHERE
                id = '$id'";
                $resinsertEportalRequest = DB::insert($insertEportalRequest);

                // echo $insertEportalRequest;exit;


            }
            $latestErId = DB::table('eportal_request')
                ->orderBy('id', 'desc')
                ->value('id');
            // exit;

            $latestErId = ($latestErId == '') ? $id : $latestErId;


            foreach ($users as $row) {

                $userid = $row;//$array[1];


                $sql = "SELECT
                *
                FROM
                eportal_request 
                        left join eportal_request_details on er_id = eportal_request.id 
                        left JOIN emp_employees ON employee_id = er_ecd_resolving_user
                WHERE
                er_ecd_resolving_user = '$userid'
                    AND
                    parent_er_id ='$id'";
                //echo $sql;exit;

                $resSql = DB::select($sql);

                if (count($resSql) >= 1) {
                    $allow = 0;
                    $employeeName[] = $resSql[0]->emp_f_name . ' ' . $resSql[0]->emp_l_name;
                    //return response()->json(['message' => 'Request User already exist!','allow'=>$allow,'existArr'=>$employeeName, 'status' => true]);
                    //return response()->json(['message' => '0', 'status' => false,'allow'=>$allow,'existArr'=>$employeeName, ]);
                } else {
                    $allow = 1;
                }


                // dd(count($resSql));
                // $string = $user;
                // $array = explode('|', $string);
                // $dept = $array[0];

                if ($allow == 1) {

                    $pattern = '/[^a-zA-Z0-9]+/';
                    $messageForwarded = preg_replace($pattern, ' ', $request->input('remarksToForward'));
                    // Trim only the first and last space
                    $messageForwarded = preg_replace('/^\s+/', '', $messageForwarded);
                    $messageForwarded = preg_replace('/\s+$/', '', $messageForwarded);


                    $insertIntoEePortalRequestDetailsSql = "
                INSERT INTO eportal_request_details (
                er_id,
                er_ecd_type,
                ER_ECD_APPROVAL_USER,
                er_ecd_status,
                er_ecd_dep,
                er_ecd_category_type,
                er_ecd_completion_days,
                er_ecd_completion_hours,
                er_ecd_instruction_categories,
                er_ecd_input_form_type,
                er_ecd_fee_challan_uplodes,
                er_ecd_fee,
                er_ecd_upload_type,
                erd_sai_enrollment_no,
                erd_status,
                er_ECD_RESOLVING_USER,
            
                er_ecd_vc_remarks,
                er_ecd_vc_forward_time
                )
                SELECT
                    '" . $latestErId . "',
                    ecd_type,
                    ECD_APPROVAL_USER,
                    ecd_status,
                    ecd_dep_id,
                    ecd_category_type,
                    ecd_completion_days,
                    ecd_completion_hours,
                    ecd_instruction_categories,
                    ecd_input_form_type,
                    ecd_fee_challan_uplodes,
                    ecd_fee,
                    ecd_upload_type,
                    '" . strtoupper($stdroll) . "',
                    'Pending',
                    '$userid',                
                    '$messageForwarded',
                    sysdate
                FROM
                        EPORTAL_CATEGORIES_DETAILS
                    WHERE
                       ec_id=$ec_id
                        and ecd_completion_days is null 
                ";
                    // echo $insertIntoEePortalRequestDetailsSql;exit;
                    //dd($request->all());
                    $insertIntoEePortalRequestDetails = DB::statement($insertIntoEePortalRequestDetailsSql);
                    //dd(count($resSql),$latestErId,$users,$allow);

                    $sendEmail = "select emp_email from emp_employees where employee_id = $userid";
                    $resEmail = DB::select($sendEmail);
                    $email = $resEmail[0]->emp_email;
                    $display = 'Eportal Request Task From VC';
                    $ec_title = $ec_title;
                    //dd($message); 
                    if ($messageForwarded) {
                        $remarks = $messageForwarded;
                    } else {
                        $remarks = 'Eportal Request Forwarded by VC';
                    }

                    //dd($remarks);

                    $this->notificationOnForward($email, $ec_title, $remarks, $display);

                }

                $pattern = '/[^a-zA-Z0-9]+/';
                $message = preg_replace($pattern, ' ', $request->input('remarks'));

                // Trim only the first and last space
                $message = preg_replace('/^\s+/', '', $message);
                $message = preg_replace('/\s+$/', '', $message);

                DB::table('EPORTAL_REQUEST')
                    ->where('ID', $id)
                    ->update([

                        'ER_REMARKS' => $message,
                        'ER_REQUEST_STATUS' => 'Completed',
                        'er_resolving_datetime' => $currentDateTime,
                        'er_remarks_user_id' => auth()->user()->employee_id
                    ]);
                //dd( $id,count($resSql),$latestErId,$users,$allow);
            }

            if ($allow == 0) {
                return response()->json(['message' => 'Request User already exist!', 'allow' => $allow, 'existArr' => $employeeName, 'status' => true]);
            } else {
                return response()->json(['message' => 'Request Forward Successfully', 'allow' => $allow, 'existArr' => [], 'status' => true]);

            }


        } elseif ($status == 'justForward') {


            if (empty($users)) {
                return response()->json(['message' => 'Please Select Forwarding User', 'allow' => $allow, 'existArr' => [], 'status' => false]);
            }

            $sqlEportalReq = "select * from eportal_request where ec_id=$ec_id and er_request_status in ('Forward','Pending') and er_sai_enrollment_no = upper('$stdroll')";
            $ressqlEportalReq = DB::select($sqlEportalReq);

            $sqlCheckAlreadyForward = "select * from eportal_request where parent_er_id = '" . $ressqlEportalReq[0]->id . "'";
            $ressqlCheckAlreadyForward = DB::select($sqlCheckAlreadyForward);
            // echo count($ressqlCheckAlreadyForward);exit;
            // echo $sqlEportalReq.'<br>';exit;
            if (count($ressqlCheckAlreadyForward) == '0') {
                if (count($ressqlEportalReq) >= 1) {

                    $insertEportalRequest = "INSERT INTO eportal_request (
                    ec_id,
                    er_sai_enrollment_no,
                    er_request_status,
                    er_description,
                    er_entry_datetime,
                    er_entry_user_id,
                    ER_ESCALATION_TIME,
                    ER_REQUEST_FILE,
                    parent_er_id
                ) SELECT
                    '83',

                    er_sai_enrollment_no,
                    'Pending',
                    er_description,
                    CURRENT_TIMESTAMP,
                    er_entry_user_id,
                    sysdate+2,
                    ER_REQUEST_FILE,
                    '$id'
                FROM
                    eportal_request
                WHERE
                    id = '$id'";
                    $resinsertEportalRequest = DB::insert($insertEportalRequest);

                    // echo $insertEportalRequest;exit;


                }
            }
            $latestErId = DB::table('eportal_request')
                ->orderBy('id', 'desc')
                ->value('id');
            // exit;

            $latestErId = ($latestErId == '') ? $id : $latestErId;
            //print_r($users);

            foreach ($users as $row) {

                $userid = $row;//$array[1];
                $sql = "SELECT
            *
            FROM
               eportal_request
                    left join eportal_request_details on er_id = eportal_request.id
                    left JOIN emp_employees ON employee_id = er_ecd_resolving_user
            WHERE
            er_ecd_resolving_user = '$userid'
                AND
                 parent_er_id ='$id'";
                //echo $sql;exit;

                $resSql = DB::select($sql);

                if (count($resSql) >= 1) {
                    $allow = 0;
                    $employeeName[] = $resSql[0]->emp_f_name . ' ' . $resSql[0]->emp_l_name;
                    //return response()->json(['message' => 'Request User already exist!','allow'=>$allow,'existArr'=>$employeeName, 'status' => true]);
                    //return response()->json(['message' => '0', 'status' => false,'allow'=>$allow,'existArr'=>$employeeName, ]);
                } else {
                    $allow = 1;
                }
                // dd(count($resSql));
                // $string = $user;
                // $array = explode('|', $string);
                // $dept = $array[0];
                if ($allow == 1) {

                    $pattern = '/[^a-zA-Z0-9]+/';
                    $messageForwarded = preg_replace($pattern, ' ', $request->input('remarksToForward'));
                    // Trim only the first and last space
                    $messageForwarded = preg_replace('/^\s+/', '', $messageForwarded);
                    $messageForwarded = preg_replace('/\s+$/', '', $messageForwarded);

                    $insertIntoEePortalRequestDetailsSql = "
            INSERT INTO eportal_request_details (
            er_id,
            er_ecd_type,
            ER_ECD_APPROVAL_USER,
            er_ecd_status,
            er_ecd_dep,
            er_ecd_category_type,
            er_ecd_completion_days,
            er_ecd_completion_hours,
            er_ecd_instruction_categories,
            er_ecd_input_form_type,
            er_ecd_fee_challan_uplodes,
            er_ecd_fee,
            er_ecd_upload_type,
            erd_sai_enrollment_no,
            erd_status,
            er_ECD_RESOLVING_USER,

            er_ecd_vc_remarks,
            er_ecd_vc_forward_time
            )
            SELECT
                '" . $latestErId . "',
                ecd_type,
                ECD_APPROVAL_USER,
                ecd_status,
                ecd_dep_id,
                ecd_category_type,
                ecd_completion_days,
                ecd_completion_hours,
                ecd_instruction_categories,
                ecd_input_form_type,
                ecd_fee_challan_uplodes,
                ecd_fee,
                ecd_upload_type,
                '" . strtoupper($stdroll) . "',
                'Pending',
                '$userid',
                '$messageForwarded',
                sysdate
            FROM
                    EPORTAL_CATEGORIES_DETAILS
                WHERE
                   ec_id = $ec_id
                    and ecd_completion_days is null
            ";
                    // echo $insertIntoEePortalRequestDetailsSql;exit;
                    //dd($request->all());
                    $insertIntoEePortalRequestDetails = DB::statement($insertIntoEePortalRequestDetailsSql);
                    //dd(count($resSql),$latestErId,$users,$allow);

                    $sendEmail = "select emp_email from emp_employees where employee_id = $userid";
                    $resEmail = DB::select($sendEmail);
                    $email = $resEmail[0]->emp_email;
                    $display = 'Eportal Request Task From VC';
                    $ec_title = $ec_title;
                    //dd($message);
                    if ($messageForwarded) {
                        $remarks = $messageForwarded;
                    } else {
                        $remarks = 'Eportal Request Forwarded by VC';
                    }

                    //dd($remarks);

                    // $this->notificationOnForward($email, $ec_title, $remarks, $display);
                    // try {
                    //     $this->notificationOnForward($email, $ec_title, $remarks, $display);
                    // } catch (Exception $e) {
                    //     // Handle the exception here
                    //     echo 'An exception occurred: ' . $e->getMessage();
                    // }

                }

                $pattern = '/[^a-zA-Z0-9]+/';
                $message = preg_replace($pattern, ' ', $request->input('remarks'));

                // Trim only the first and last space
                $message = preg_replace('/^\s+/', '', $message);
                $message = preg_replace('/\s+$/', '', $message);

                DB::table('EPORTAL_REQUEST')
                    ->where('ID', $id)
                    ->update([
                        'ER_REMARKS' => $message,
                        'ER_REQUEST_STATUS' => 'Pending',
                        'er_resolving_datetime' => $currentDateTime,
                        'er_remarks_user_id' => auth()->user()->employee_id
                    ]);
                //dd( $id,count($resSql),$latestErId,$users,$allow);
            }

            if ($allow == 0) {
                return response()->json(['message' => 'Request User already exist!', 'allow' => $allow, 'existArr' => $employeeName, 'status' => true]);
            } else {
                return response()->json(['message' => 'Request Forward Successfully', 'allow' => $allow, 'existArr' => [], 'status' => true]);

            }

        }
        // echo $insertIntoEePortalRequestDetailsSql;exit;
        // dd($request->all());

        try {
            // $request->validate([
            //     'remarks' => 'required',
            //     'file' => 'required|file|mimes:pdf,jpg,jpeg,png',
            // ]);
            $newEcdId = $request->input('er_id');
            $obj = DB::table('EPORTAL_REQUEST')
                ->select(
                    'ID',
                    'EC_ID',
                    'ER_ISSUANCE_FILE',
                    'GOOGLE_DRIVE_FILE_NAME',
                    'GOOGLE_DRIVE_URL'
                )
                ->where('ID', '=', $newEcdId)
                ->first();
            // dd($obj);

            // Check if the file exists on Google Drive and delete it if it does
            $fileExists = Storage::disk('google')->exists($obj->google_drive_file_name);
            if ($fileExists) {
                Storage::disk('google')->delete($obj->google_drive_file_name);
            }

            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $fileName = time() . "ePortal:" . $file->getClientOriginalName();
                $newFileName = time() . '_' . $fileName;
                $path = $file->storeAs('models', $newFileName);


                // dd($newFileName);

                // Upload the file to Google Drive
                $upload = Storage::disk('google')->putFileAs("", $file, $fileName);
                $url = Storage::disk('google')->url($upload);
                $details = Storage::disk("google")->getMetadata($upload);
                $file_name = $details['path'];
                $pattern = '/[^a-zA-Z0-9]+/';
                $message = preg_replace($pattern, ' ', $request->input('remarks'));

                // Trim only the first and last space
                $message = preg_replace('/^\s+/', '', $message);
                $message = preg_replace('/\s+$/', '', $message);

                // Update the database with file information
                DB::table('EPORTAL_REQUEST')
                    ->where('ID', $newEcdId)
                    ->update([
                        'google_drive_file_name' => $file_name,
                        'google_drive_url' => $url,
                        'er_issuance_file' => $fileName,
                        'ER_REMARKS' => $message,
                        'ER_REQUEST_STATUS' => $request->input('action'),
                        'er_resolving_datetime' => Carbon::now(),
                        'er_remarks_user_id' => auth()->user()->employee_id
                    ]);
                $display = 'E-Portal Application Completion';
                // $this->notificationOnCompletion($email,$ec_title,$remarks,$display);
                //$this->notificationStatus($newEcdId,'completion');
                return response()->json(['message' => 'File uploaded successfully', 'allow' => 1, 'existArr' => [], 'status' => true, 'resstatus' => $status]);
            } else {

                $message = $request->input('remarks');
                // dd($request->input('remarks'));
                // $pattern = '/[^a-zA-Z0-9]+/';
                // $message = preg_replace($pattern, ' ', $request->input('remarks'));

                // // Trim only the first and last space
                // $message = preg_replace('/^\s+/', '', $message);
                // $message = preg_replace('/\s+$/', '', $message);
                DB::table('EPORTAL_REQUEST')
                    ->where('ID', $newEcdId)
                    ->update([
                        'ER_REMARKS' => $message,
                        'ER_REQUEST_STATUS' => $request->input('action'),
                        'er_resolving_datetime' => Carbon::now(),
                        'er_remarks_user_id' => auth()->user()->employee_id
                    ]);
                $display = 'E-Portal Application Completion';
                // $this->notificationOnCompletion($email,$ec_title,$remarks,$display);
                //$this->notificationStatus($newEcdId,'completion');
                // in case of decline er request status should return complete to redirect to complete tab
                if ($request->input('action') == 'Declined') {
                    $status = 'Completed';
                }
                return response()->json(['message' => 'Request Completed Successfully', 'allow' => 1, 'existArr' => [], 'status' => true, 'resstatus' => $status]);
            }
        } catch (ValidationException $e) {
            $res = ['status' => false, 'message' => 'Validation error: ' . $e->getMessage()];
            return response()->json($res);
        } catch (Exception $e) {
            $res = ['status' => false, 'message' => 'An error occurred: ' . $e->getMessage()];
            return response()->json($res);
        }

    }
    function notificationOnForward($to, $subject, $message, $display)
    {
        // echo $to.'   '.$subject.'   '.$message.'   '.$display;exit;
        DB::table('testing')
            ->insert(
                ['description' => $display . ' To: ' . $to . ' || Subject: ' . $subject . ' || Message: ' . $message]
            );
        $procedurenotification = 'send_mail_one_by_one';
        $bindings_notification = [
            'to' => $to,
            'subject' => $subject,
            'message' => $message,
            'display' => $display,
        ];
        return $result_notification = DB::executeProcedure($procedurenotification, $bindings_notification);
    }

    function markApproval(Request $request)
    {
        // dd($request->all());
        $status = $request->get('action');
        $remarks = $request->get('remarks');
        $id = $request->get('erd_id');
        $er_id = $request->get('er_id');
        $approval_user = auth()->user()->employee_id;
        if ($request->input('action') == 'futher_action') {
            $status = 'Pending';
        }

        $pattern = '/[^a-zA-Z0-9]+/';
        $message = preg_replace($pattern, ' ', $remarks);

        // Trim only the first and last space
        $message = preg_replace('/^\s+/', '', $message);
        $message = preg_replace('/\s+$/', '', $message);



        $requster_sql = " UPDATE
    eportal_request_details
    SET
        erd_status = '$status',
        er_ecd_approval_user = '$approval_user',
        ER_ECD_REMARKS = '$message',
        er_ecd_appr_datetime = SYSDATE
    WHERE
        id = $id";
        //echo $requster_sql;exit;
        $res = DB::insert($requster_sql);
        // in case of decline er request status should return complete to redirect to complete tab
        if ($request->input('action') == 'Declined') {
            $status = 'Approved';
        } else if ($request->input('action') == 'futher_action') {
            $status = 'Pending';
            $this->notificationFunctionNeeded($er_id, $approval_user);
        }

        return response()->json(['data' => $res, 'message' => "Request " . $status . " Successfully", 'resstatus' => $status]);

        // $res = array('status' => true, 'message' => "Request."$status". Successfully");
        // return response()->json($res);
        //return view('ePortal.eportal_dashboard');
    }
    function notificationStatus($id, $status)
    {
        $procedureName = 'eportal_notification_user';
        $bindings = [
            'req_id' => $id,
            'status' => $status,
        ];
        //return $result = DB::executeProcedure($procedureName, $bindings);
    }
    function notificationOnCompletion($to, $subject, $message, $display)
    {
        // echo $to.'   '.$subject.'   '.$message.'   '.$display;exit;
        DB::table('testing')
            ->insert(
                ['description' => $display . ' To: ' . $to . ' || Subject: ' . $subject . ' || Message: ' . $message]
            );
        $procedurenotification = 'send_mail_one_by_one';
        $bindings_notification = [
            'to' => $to,
            'subject' => $subject,
            'message' => $message,
            'display' => $display,
        ];
        //return $result_notification = DB::executeProcedure($procedurenotification, $bindings_notification);
    }
    function notificationFunctionNeeded($id, $sender_id)
    {
        $procedureName = 'eportal_further_action';
        $bindings = [
            'req_id' => $id,
            'sender_id' => $sender_id,
        ];
        //return $result = DB::executeProcedure($procedureName, $bindings);
    }

    public function rollbackStatus(Request $request)
    {

        $pattern = '/[^a-zA-Z0-9]+/';
        $message = preg_replace($pattern, ' ', $request->remarks);

        // Trim only the first and last space
        $message = preg_replace('/^\s+/', '', $message);
        $message = preg_replace('/\s+$/', '', $message);

        $requster_sql = " UPDATE
         eportal_request
         SET
             er_request_status = 'Pending',
             er_remarks = '$message'
         WHERE
             id = $request->id";
        $requster_info = DB::insert($requster_sql);
        $res = array('status' => true, 'message' => 'Record Update successfully!');
        return response()->json($res);
    }
    public function rejectStatus(Request $request)
    {
        // dd($request->id);
        $pattern = '/[^a-zA-Z0-9]+/';
        $message = preg_replace($pattern, ' ', $request->remarks);

        // Trim only the first and last space
        $message = preg_replace('/^\s+/', '', $message);
        $message = preg_replace('/\s+$/', '', $message);
        $requster_sql = " UPDATE
         eportal_request
         SET
             er_request_status = 'Rejected',
             er_remarks = ' $message'
         WHERE
             id = $request->id";
        $requster_info = DB::insert($requster_sql);
        $res = array('status' => true, 'message' => 'Record Update successfully!');
        return response()->json($res);
    }









    function completed()
    {
        $sql = "SELECT
        eportal_request.id cate_id,
        eportal_request.ER_REQUEST_STATUS,
        eportal_request. GOOGLE_DRIVE_FILE_NAME,
        eportal_request.GOOGLE_DRIVE_URL,
         eportal_request.ec_id          AS id,
        (select EC_TITLE from EPORTAL_CATEGORIES WHERE id = EPORTAL_REQUEST.EC_ID)as title,
        EPORTAL_REQUEST.ER_DESCRIPTION as description,
        username,
        email,
        spi_name as full_name,
        father_name,
        spi_mobile as mobile,
        school,
        programs,
        dept_id,
        prog_id,
        group_id
        FROM EPORTAL_REQUEST
        JOIN sisusers ON lower(EPORTAL_REQUEST.ER_SAI_ENROLLMENT_NO) = lower(sisusers.USERNAME)
        where   eportal_request.ER_REQUEST_STATUS = 'Completed' ";

        // echo $sql;exit;
        $res = DB::select($sql);
        // dd( $res);
        return view('ePortal.eportal_completed', ['res' => $res,]);
    }

    function approved()
    {
        $employee_id = auth()->user()->employee_id;
        $sql = "SELECT
        eportal_request.id cate_id,
        eportal_request.ER_REQUEST_STATUS,
         eportal_request.ec_id          AS id,
        (select EC_TITLE from EPORTAL_CATEGORIES WHERE id = EPORTAL_REQUEST.EC_ID)as title,
        EPORTAL_REQUEST.ER_DESCRIPTION as description,
        username,
        email,
        spi_name as full_name,
        father_name,
        spi_mobile as mobile,
        school,
        programs,
        dept_id,
        prog_id,
        group_id
        FROM EPORTAL_REQUEST
        left JOIN sisusers ON lower(eportal_request.er_sai_enrollment_no) = lower(sisusers.username)
        LEFT JOIN students_apply_info ON lower(students_apply_info.sai_enrollment_no) = lower(eportal_request.er_sai_enrollment_no)
        LEFT JOIN students_personal_info ON spi_id = sai_spi_id
        left join EPORTAL_REQUEST_DETAILS on er_id = eportal_request.id
        WHERE EPORTAL_REQUEST_DETAILS.erd_status = 'Approved'
        AND   er_ecd_approval_user IS NOT NULL";
        if ($employee_id != 8996) {
            $sql .= " and er_ecd_approval_user = $employee_id";
        }

        //echo $sql;exit;
        $res = DB::select($sql);
        // dd( $res);
        return view('ePortal.eportal_approved', ['res' => $res,]);
    }









    public function uploadFile(Request $request)
    {

        // dd($request->all());


        try {
            $request->validate([
                'new_remarks' => 'required',
                'file' => 'required|file|mimes:pdf,jpg,jpeg,png',
            ]);



            $newEcdId = $request->input('new_ecd_id');
            $obj = DB::table('EPORTAL_REQUEST')
                ->select(
                    'ID',
                    'EC_ID',
                    'ER_ISSUANCE_FILE',
                    'GOOGLE_DRIVE_FILE_NAME',
                    'GOOGLE_DRIVE_URL'
                )
                ->where('ID', '=', $newEcdId)
                ->first();
            // dd($obj);

            // Check if the file exists on Google Drive and delete it if it does
            $fileExists = Storage::disk('google')->exists($obj->google_drive_file_name);
            if ($fileExists) {
                Storage::disk('google')->delete($obj->google_drive_file_name);
            }

            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $fileName = time() . "ePortal:" . $file->getClientOriginalName();
                $newFileName = time() . '_' . $fileName;
                $path = $file->storeAs('models', $newFileName);


                // dd($newFileName);
                $pattern = '/[^a-zA-Z0-9]+/';
                $message = preg_replace($pattern, ' ', $request->input('new_remarks'));

                // Trim only the first and last space
                $message = preg_replace('/^\s+/', '', $message);
                $message = preg_replace('/\s+$/', '', $message);
                // Upload the file to Google Drive
                $upload = Storage::disk('google')->putFileAs("", $file, $fileName);
                $url = Storage::disk('google')->url($upload);
                $details = Storage::disk("google")->getMetadata($upload);
                $file_name = $details['path'];

                // Update the database with file information
                DB::table('EPORTAL_REQUEST')
                    ->where('ID', $newEcdId)
                    ->update([
                        'google_drive_file_name' => $file_name,
                        'google_drive_url' => $url,
                        'er_issuance_file' => $fileName,
                        'ER_REMARKS' => $message,
                        'ER_REQUEST_STATUS' => $request->input('approval_status')
                    ]);

                return response()->json(['message' => 'File uploaded successfully', 'status' => true]);
            } else {
                return response()->json(['message' => 'No file uploaded', 'status' => false]);
            }
        } catch (ValidationException $e) {
            $res = ['status' => false, 'message' => 'Validation error: ' . $e->getMessage()];
            return response()->json($res);
        } catch (Exception $e) {
            $res = ['status' => false, 'message' => 'An error occurred: ' . $e->getMessage()];
            return response()->json($res);
        }
    }

    public function fetchStaffData(Request $request)
    {
        // Retrieve the selected staff ID from the request
        $selectedValue = $request->input('staffId');
        $values = explode('|', $selectedValue);
        $selectedId = $values[0];
        $selectedEmployee = $values[1];
        $ordername = $values[2];

        $select_list = "prefix,employee_id,emp_f_name,emp_l_name,dept_name,school_code";
        //    if($ordername=="G"){
        //          $sql = "          SELECT
        //                    $select_list
        //      FROM
        //          emp_employees
        //          left join schools on school_id = emp_school_id
        //          left join departments on dept_id = emp_employees.emp_dept_id
        //          WHERE 1=1
        //          AND EMPLOYEE_ID IN (
        //                select tmgu_employee_id from task_management_group_user
        //                left join task_management_group tmg on tmg.id = TMGU_TMG_GROUP_ID 
        //                where TMGU_TMG_GROUP_ID = $selectedEmployee
        //          )
        //      ";
        //      //".auth()->user()->employee_id."
        //    }else{
        $sql = "SELECT $select_list FROM EMP_EMPLOYEES 
           left join schools on school_id = emp_school_id
           left join departments on dept_id = emp_dept_id
            WHERE  emp_status = 'E' and employee_id = $selectedEmployee";
        // }
        $sql .= " ORDER BY EMP_DEPT_ID ";
        //echo $sql;exit;
        $staffData = DB::select($sql);
        return $staffData;
    }
}
