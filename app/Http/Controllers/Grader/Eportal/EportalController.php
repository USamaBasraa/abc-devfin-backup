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

class EportalController extends Controller
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
        // $EpCateg = "SELECT 
        //             ec.ec_title,
        //             ec.id,
        //             (
        //                 SELECT COUNT(*) 
        //                 FROM eportal_request er 
        //                 WHERE er.ec_id = ec.id
        //                 AND er.er_request_status = 'Pending'
        //             ) AS total_req
        //         FROM eportal_categories ec
        //         LEFT JOIN eportal_categories_details ecd 
        //             ON ec.id = ecd.ec_id
        //         WHERE ec.ec_status = 'E'";

        //     if (!in_array(auth()->user()->employee_id, $allowedAllReq)) {
        //         $EpCateg .= " AND ecd.ECD_RESOLVING_USER = '$employee_id'";
        //     }


        $EpCateg = "SELECT 
        (
            SELECT COUNT(*) 
            FROM eportal_request_details erd
            LEFT JOIN eportal_request er ON er.id = erd.er_id
            WHERE er.ec_id = ec.id
              AND erd.erd_status = 'Pending'
              AND erd.er_ecd_resolving_user = $employee_id
                ) AS total_req,
                ec.id AS ec_id,
                ec.ec_title,
                ec.id
            FROM eportal_categories ec
            LEFT JOIN eportal_categories_details ecd 
                ON ec.id = ecd.ec_id
            WHERE ec.ec_status = 'E'";

        if (!in_array(auth()->user()->employee_id, $allowedAllReq)) {
            $EpCateg .= " AND ecd.ECD_RESOLVING_USER = $employee_id";
        }

        // echo $EpCateg; exit;
        // $resEpCateg = DB::select($EpCateg);

        // echo $EpCateg;exit;
        //$EpCateg = "select * from EPORTAL_CATEGORIES where ec_status = 'E'";
        // echo $requster_info; exit;
        $resEpCateg = DB::select($EpCateg);
        // dd($resEpCateg);

        return view('ePortal.eportal_dashboard', ['res' => $requster_info, 'resEpCateg' => $resEpCateg]);
    }
    function getCateg(Request $request)
    {
        $ec_id = $request->input('ec_id');
        $sel = $request->input('sel');
        $taskstatus = $request->input('taskStatus');
        $employee_id = auth()->user()->employee_id;
        $student = $request->input('student');

        // Professional Pagination Parameters
        $page = $request->input('page', 1);
        $perPage = $request->input('per_page', 100); // Default 100 records per page
        $offset = ($page - 1) * $perPage;

        $specificFilter = $request->input('specific_filter');

        $requster_sql = "SELECT
        eportal_request.ec_id,
		ec_title,
        er_sai_enrollment_no,
        eportal_request.id er_id,
        eportal_request.ER_REQUEST_FILE,
        eportal_request.ER_REQUEST_STATUS,
        eportal_request. GOOGLE_DRIVE_FILE_NAME,
        eportal_request.GOOGLE_DRIVE_URL,
        eportal_request.ec_id          AS id,
        eportal_request.ER_ESCALATION_TIME as esclation_time,
        eportal_request.ER_entry_datetime as erdatetime,
        eportal_request.parent_er_id,
       (SELECT parent_request.ec_id FROM EPORTAL_REQUEST parent_request WHERE parent_request.id = eportal_request.parent_er_id) as parent_req_id,
        er_student_upload_challan,
        (select EC_TITLE from EPORTAL_CATEGORIES WHERE id = EPORTAL_REQUEST.EC_ID)as title,
        EPORTAL_REQUEST.ER_DESCRIPTION as description,
        username,
        email,
        CASE 
            WHEN eportal_request.ec_id = 120 AND eportal_request.er_sai_enrollment_no IS NOT NULL THEN
                (SELECT api_name || ' (' || aai_id || ')'
                 FROM admission_personal_info 
                 LEFT JOIN admission_apply_info ON api_id = aai_api_id  
                 WHERE 
                 
                  (   
                    TO_CHAR(aai_id) = eportal_request.er_sai_enrollment_no 
                      OR 
                    TO_CHAR(cms_sai_enrollment_no) = eportal_request.er_sai_enrollment_no
                       )


                 AND ROWNUM = 1)
            WHEN spi_name IS NULL THEN 
                (SELECT emp_f_name || ' ' || emp_l_name 
                 FROM emp_employees 
                 WHERE lower(emp_ad_username) = lower(username) 
                 AND rownum = 1)
            ELSE 
                spi_name 
        END AS full_name,
        father_name,
         spi_mobile as mobile,
        school,
        programs,
        dept_id,
        prog_id,
        sai_sdpo_school_id as school_id,
            eportal_request.er_applicant_request_type as er_applicant_request_type,


 JSON_VALUE(eportal_request.er_bank_details, '$.beneficiary_name') AS beneficiary_name,
            JSON_VALUE(eportal_request.er_bank_details, '$.relationship') AS relationship,
            COALESCE(JSON_VALUE(eportal_request.er_bank_details, '$.beneficiary_cnic'), JSON_VALUE(eportal_request.er_bank_details, '$.cnic_number')) AS cnic_number,
            JSON_VALUE(eportal_request.er_bank_details, '$.bank_name') AS bank_name,
            JSON_VALUE(eportal_request.er_bank_details, '$.iban') AS iban,
            JSON_VALUE(eportal_request.er_bank_details, '$.bank_account_title') AS bank_account_title,
            JSON_VALUE(eportal_request.er_bank_details, '$.mobile_number') AS mobile_number,
            JSON_VALUE(eportal_request.er_bank_details, '$.original_tuition_fee') AS original_tuition_fee,
            JSON_VALUE(eportal_request.er_bank_details, '$.original_medical_fee') AS original_medical_fee,
            JSON_VALUE(eportal_request.er_bank_details, '$.original_activity_fee') AS original_activity_fee,
            JSON_VALUE(eportal_request.er_bank_details, '$.refund_tuition_fee') AS refund_tuition_fee,
            JSON_VALUE(eportal_request.er_bank_details, '$.refund_medical_fee') AS refund_medical_fee,
            JSON_VALUE(eportal_request.er_bank_details, '$.refund_activity_fee') AS refund_activity_fee,
            JSON_VALUE(eportal_request.er_bank_details, '$.total_refund_amount') AS total_refund_amount,
            JSON_VALUE(eportal_request.er_bank_details, '$.days_since_commencement') AS days_since_commencement,
            JSON_VALUE(eportal_request.er_bank_details, '$.refund_percentage') AS refund_percentage,
            JSON_VALUE(eportal_request.er_bank_details, '$.security_amt') AS security_amt,
            JSON_VALUE(eportal_request.er_bank_details, '$.refund_security_fee') AS refund_security_fee,
            JSON_VALUE(eportal_request.er_bank_details, '$.scholarship_amount') AS scholarship_amount,
            CASE 
                WHEN JSON_EXISTS(eportal_request.er_bank_details, '$.hostel_fee_original') 
                THEN JSON_VALUE(eportal_request.er_bank_details, '$.hostel_fee_original') 
            END AS hostel_fee_original,
            CASE 
                WHEN JSON_EXISTS(eportal_request.er_bank_details, '$.hostel_fee_refundable')
                THEN JSON_VALUE(eportal_request.er_bank_details, '$.hostel_fee_refundable')
            END AS hostel_fee_refundable,

           email,
            eportal_request.er_applicant_request_type as er_applicant_request_type,
        CASE 
            WHEN school IS NULL THEN (
                SELECT school_code 
                FROM schools 
                LEFT JOIN admission_apply_info ON school_id = aai_sdpo_school_id 
                WHERE TO_CHAR(aai_id) = eportal_request.er_sai_enrollment_no 
                AND ROWNUM = 1
            ) 
            ELSE school 
            END AS school,
            case when programs is null then (select prog_name from programs left join admission_apply_info on prog_id=aai_sdpo_prog_id where TO_CHAR(aai_id) = eportal_request.er_sai_enrollment_no and rownum=1) else programs end as programs,
         
        group_id,
        er_ecd_vc_remarks,
        er_remarks,
        er_ecd_viewer_user,
        ( select count(*) from eportal_request_details where er_ecd_approval_user is not null and er_id =eportal_request.id and erd_status = 'Pending' ) as chkapproval,
        (
        SELECT
            COUNT(*)
        FROM
            eportal_request_details
        WHERE
                er_ecd_approval_user IS NOT NULL
            AND
                er_id = eportal_request.id
            AND
                erd_status = 'Declined'
        ) AS chkdeclined,sai_student_status,reinitiate_time
        FROM EPORTAL_REQUEST
        Left JOIN sisusers ON lower(EPORTAL_REQUEST.ER_SAI_ENROLLMENT_NO) = lower(sisusers.USERNAME)
        LEFT JOIN students_apply_info ON lower(students_apply_info.sai_enrollment_no) = lower(eportal_request.er_sai_enrollment_no)
        LEFT JOIN students_personal_info ON spi_id = sai_spi_id
        left join eportal_categories on ec_id = eportal_categories.id
        left join eportal_request_details on er_id = eportal_request.id
        where   1=1   and ec_id not in(84)   and (er_ecd_resolving_user = '$employee_id' or er_ecd_viewer_user = '$employee_id')";
        // Handle status filters with specific filter support
        if ($specificFilter == 'completed_only') {
            $requster_sql .= "and eportal_request.ER_REQUEST_STATUS in ('Completed','Declined')";
        } elseif ($specificFilter == 'declined_only' || $taskstatus == 'Declined') {
            $requster_sql .= "and eportal_request.ER_REQUEST_STATUS in ('Declined','Completed')";
        } elseif ($taskstatus == 'Completed') {
            $requster_sql .= "and eportal_request.ER_REQUEST_STATUS in ('Completed','Declined')";
        } else {
            $requster_sql .= "and eportal_request.ER_REQUEST_STATUS in ('$taskstatus','Forward')";
            // $requster_sql .= "and eportal_request.ER_REQUEST_STATUS = '$taskstatus'";
        }
        if ($sel != 'Pending') {
            if ($ec_id != '0') {
                $requster_sql .= "and ec_id = $ec_id";
            }
        }
        if ($student != 'noStudent') {
            $requster_sql .= " and lower(er_sai_enrollment_no) = lower('$student')";
        }


        if ($taskstatus == 'Completed') {
            $requster_sql .= "order by er_resolving_datetime desc";
        } else {
            $requster_sql .= "order by er_entry_datetime desc";
            // $requster_sql .= "and eportal_request.ER_REQUEST_STATUS = '$taskstatus'";
        }

        // Build separate optimized COUNT query (without ORDER BY and complex fields)
        $countSql = "SELECT COUNT(DISTINCT eportal_request.id) as total_count
        FROM EPORTAL_REQUEST
        Left JOIN sisusers ON lower(EPORTAL_REQUEST.ER_SAI_ENROLLMENT_NO) = lower(sisusers.USERNAME)
        LEFT JOIN students_apply_info ON lower(students_apply_info.sai_enrollment_no) = lower(eportal_request.er_sai_enrollment_no)
        LEFT JOIN students_personal_info ON spi_id = sai_spi_id
        left join eportal_categories on ec_id = eportal_categories.id
        left join eportal_request_details on er_id = eportal_request.id
        where   1=1   and ec_id not in(84)   and (er_ecd_resolving_user = '$employee_id' or er_ecd_viewer_user = '$employee_id')";

        // Add same filters as main query (without ORDER BY)
        if ($specificFilter == 'completed_only') {
            $countSql .= "and eportal_request.ER_REQUEST_STATUS in ('Completed','Declined')";
        } elseif ($specificFilter == 'declined_only' || $taskstatus == 'Declined') {
            $countSql .= "and eportal_request.ER_REQUEST_STATUS in ('Declined','Completed')";
        } elseif ($taskstatus == 'Completed') {
            $countSql .= "and eportal_request.ER_REQUEST_STATUS in ('Completed','Declined')";
        } else {
            $countSql .= "and eportal_request.ER_REQUEST_STATUS in ('$taskstatus','Forward')";
        }
        if ($sel != 'Pending') {
            if ($ec_id != '0') {
                $countSql .= "and ec_id = $ec_id";
            }
        }
        if ($student != 'noStudent') {
            $countSql .= " and lower(er_sai_enrollment_no) = lower('$student')";
        }


        // Execute count query first
        $totalResult = DB::select($countSql);
        $totalRecords = $totalResult[0]->total_count ?? 0;

        // Add Oracle 12c+ pagination to main query
        $requster_sql .= " OFFSET $offset ROWS FETCH FIRST $perPage ROWS ONLY";

        // echo $requster_sql;exit;
        $requster_info = DB::select($requster_sql);

        // Prepare response data
        $responseData = [
            'data' => $requster_info,
            'pagination' => [
                'current_page' => (int) $page,
                'per_page' => (int) $perPage,
                'total' => (int) $totalRecords,
                'last_page' => ceil($totalRecords / $perPage),
                'from' => $offset + 1,
                'to' => min($offset + $perPage, $totalRecords),
                'has_more_pages' => ($page * $perPage) < $totalRecords
            ],
            'status' => true
        ];

        // Return paginated response


        return response()->json($responseData)
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
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
         emp_f_name || emp_l_name as full_name,
         dept_name as department,
         erd_status,
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
         er_ecd_appr_datetime,
         er_ecd_remarks
            FROM
                eportal_request_details
                LEFT JOIN departments ON eportal_request_details.er_ecd_dep = departments.dept_id
                left join EMP_EMPLOYEES on er_ecd_approval_user=employee_id
                WHERE
                 er_ecd_approval_user is not null
                 and er_ecd_viewer_user is null
                AND er_id = '" . $id . "'
    ";
        // echo $sqlApprovalAuthority; exit();
        $approvalAuthority = DB::select($sqlApprovalAuthority);
        return response()->json([
            'data' => $approvalAuthority,
            'status' => true
        ])->header('Cache-Control', 'no-cache, no-store, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
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
               case when spi_name is null then (select emp_f_name || ' ' || emp_l_name from emp_employees where emp_ad_username = username) else spi_name end as full_name,
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

        //     $EpCateg = "SELECT
        //     count(*) as total_rec,ec_id,ec_title,eportal_categories.id
        // FROM
        //     eportal_request_details
        //     left join eportal_request on er_id  = eportal_request.id
        //     left join eportal_categories on eportal_request.ec_id = eportal_categories.id
        // WHERE
        //     erd_status = 'Pending'";

        // if (!in_array(auth()->user()->employee_id,$allowedAllReq))
        // {
        // $EpCateg .= " and er_ecd_approval_user = $employee_id";
        // }
        // $EpCateg .= " group by ec_id,ec_title,eportal_categories.id";
        // //$EpCateg = "select * from EPORTAL_CATEGORIES where ec_status = 'E'";
        // // echo $EpCateg; exit;
        // $resEpCateg = DB::select($EpCateg);

        $EpCateg = "SELECT 
        (
            SELECT COUNT(*) 
            FROM eportal_request_details erd
            LEFT JOIN eportal_request er ON er.id = erd.er_id
            WHERE er.ec_id = ec.id
              AND erd.erd_status = 'Pending'
              AND erd.er_ecd_approval_user = $employee_id
                ) AS total_rec,
                ec.id AS ec_id,
                ec.ec_title,
                ec.id
            FROM eportal_categories ec
            LEFT JOIN eportal_categories_details ecd 
                ON ec.id = ecd.ec_id
            WHERE ec.ec_status = 'E'";

        if (!in_array(auth()->user()->employee_id, $allowedAllReq)) {
            $EpCateg .= " AND ecd.ECD_APPROVAL_USER = $employee_id";
        }

        // echo $EpCateg; exit;
        $resEpCateg = DB::select($EpCateg);









        return view('ePortal.eportal_approval', ['resEpCateg' => $resEpCateg]);
    }



    function getApprCateg(Request $request)
    {
        $employee_id = auth()->user()->employee_id;
        $taskstatus = $request->input('taskStatus');
        $request_status = $request->input('request_status');
        $student = $request->input('student');
        $ec_id = $request->input('ec_id');
        $sel = $request->input('sel');
        $fromDate = $request->input('fromDate');
        $toDate = $request->input('toDate');
        $allowedAllReq = array("8996", "3455");

        // Professional Pagination Parameters
        $page = (int) $request->input('page', 1);
        $perPage = (int) $request->input('per_page', 100); // Default 100 records per page
        if ($page < 1)
            $page = 1;
        $offset = ($page - 1) * $perPage;


        // dd($request->all());
        $requster_sql = "SELECT
        eportal_request.id er_id,
			ec_title,
           eportal_request.ER_REQUEST_STATUS,
            eportal_request.ec_id,
            eportal_request.ec_id          AS id,
            eportal_request.ER_ESCALATION_TIME as esclation_time,
            eportal_request.ER_REQUEST_FILE          AS ER_REQUEST_FILE,
            eportal_request.ER_NOMINEE_NAME,
            eportal_request.ER_NOMINEE_CNIC,
            eportal_request.ER_NOMI_MOBILE,
            eportal_request_details.id as erd_id,
            er_student_upload_challan,
            eportal_request.ER_entry_datetime as erdatetime,
            er_remarks,
           (select EC_TITLE from EPORTAL_CATEGORIES WHERE id = EPORTAL_REQUEST.EC_ID)as title,
           EPORTAL_REQUEST.ER_DESCRIPTION as description,
        --    username,
            COALESCE(username, eportal_request.er_sai_enrollment_no) as username,
           email,
        --    spi_name as full_name,
          CASE
               WHEN spi_name IS NULL THEN (
                   SELECT api_name
                   FROM admission_apply_info
                   LEFT JOIN admission_personal_info ON aai_api_id = api_id
                   WHERE TO_CHAR(aai_id) = eportal_request.er_sai_enrollment_no
                   AND ROWNUM = 1
               )
               ELSE spi_name
           END AS full_name,
           father_name,
           spi_mobile as mobile,
        --    school,
        --    programs,
         CASE
               WHEN school IS NULL THEN (
                   SELECT school_name
                   FROM admission_apply_info
                   LEFT JOIN schools ON aai_sdpo_school_id = school_id
                   WHERE TO_CHAR(aai_id) = eportal_request.er_sai_enrollment_no
                   AND ROWNUM = 1
               )
               ELSE school
           END AS school,
           CASE
               WHEN programs IS NULL THEN (
                   SELECT prog_name
                   FROM programs
                   LEFT JOIN admission_apply_info ON prog_id = aai_sdpo_prog_id
                   WHERE TO_CHAR(aai_id) = eportal_request.er_sai_enrollment_no
                   AND ROWNUM = 1
               )
               ELSE programs
           END AS programs,
           dept_id,

            eportal_request.er_applicant_request_type as er_applicant_request_type,

            JSON_VALUE(eportal_request.er_bank_details, '$.beneficiary_name') AS beneficiary_name,
            JSON_VALUE(eportal_request.er_bank_details, '$.relationship') AS relationship,
            COALESCE(JSON_VALUE(eportal_request.er_bank_details, '$.beneficiary_cnic'), JSON_VALUE(eportal_request.er_bank_details, '$.cnic_number')) AS cnic_number,
            JSON_VALUE(eportal_request.er_bank_details, '$.bank_name') AS bank_name,
            JSON_VALUE(eportal_request.er_bank_details, '$.iban') AS iban,
            JSON_VALUE(eportal_request.er_bank_details, '$.bank_account_title') AS bank_account_title,
            JSON_VALUE(eportal_request.er_bank_details, '$.mobile_number') AS mobile_number,
            JSON_VALUE(eportal_request.er_bank_details, '$.original_tuition_fee') AS original_tuition_fee,
            JSON_VALUE(eportal_request.er_bank_details, '$.original_medical_fee') AS original_medical_fee,
            JSON_VALUE(eportal_request.er_bank_details, '$.original_activity_fee') AS original_activity_fee,
            JSON_VALUE(eportal_request.er_bank_details, '$.refund_tuition_fee') AS refund_tuition_fee,
            JSON_VALUE(eportal_request.er_bank_details, '$.refund_medical_fee') AS refund_medical_fee,
            JSON_VALUE(eportal_request.er_bank_details, '$.refund_activity_fee') AS refund_activity_fee,
            JSON_VALUE(eportal_request.er_bank_details, '$.total_refund_amount') AS total_refund_amount,
            JSON_VALUE(eportal_request.er_bank_details, '$.days_since_commencement') AS days_since_commencement,
            JSON_VALUE(eportal_request.er_bank_details, '$.refund_percentage') AS refund_percentage,
            JSON_VALUE(eportal_request.er_bank_details, '$.security_amt') AS security_amt, 
            JSON_VALUE(eportal_request.er_bank_details, '$.refund_security_fee') AS refund_security_fee,
            JSON_VALUE(eportal_request.er_bank_details, '$.scholarship_amount') AS scholarship_amount,
            CASE 
                WHEN JSON_EXISTS(eportal_request.er_bank_details, '$.hostel_fee_original') 
                THEN JSON_VALUE(eportal_request.er_bank_details, '$.hostel_fee_original') 
            END AS hostel_fee_original,
            CASE 
                WHEN JSON_EXISTS(eportal_request.er_bank_details, '$.hostel_fee_refundable')
                THEN JSON_VALUE(eportal_request.er_bank_details, '$.hostel_fee_refundable')
            END AS hostel_fee_refundable,
            

           prog_id,
           sai_student_status,
           group_id,
           erd_status,
           (select count(*) from eportal_request ep
            left join EPORTAL_REQUEST_DETAILS on ep.id = EPORTAL_REQUEST_DETAILS.er_id
            where ep.ec_id = eportal_request.ec_id  and EPORTAL_REQUEST_DETAILS.erd_status = 'Pending'  and er_ecd_approval_user is not null) as count_request,
             (
        SELECT
            COUNT(*)
        FROM
            eportal_request_details
        WHERE
                er_ecd_approval_user IS NOT NULL
            AND
                er_id = eportal_request.id
            AND
                erd_status = 'Declined'
    ) AS chkdeclined,
            ER_REQUEST_FILE,
            (select emp_f_name || ' ' || emp_l_name as full_name from eportal_request_details
            left join emp_employees on employee_id = er_ecd_resolving_user
            where er_id = eportal_request.id and er_ecd_resolving_user is not null and rownum =1) as resolving_name,
            er_ecd_remarks
           FROM EPORTAL_REQUEST
           left JOIN sisusers ON lower(eportal_request.er_sai_enrollment_no) = lower(sisusers.username)
           LEFT JOIN students_apply_info ON lower(students_apply_info.sai_enrollment_no) = lower(eportal_request.er_sai_enrollment_no)
           LEFT JOIN students_personal_info ON spi_id = sai_spi_id
           left join EPORTAL_REQUEST_DETAILS on er_id = eportal_request.id
		   left join eportal_categories on ec_id = eportal_categories.id
           WHERE 1=1";
        if ($taskstatus == 'Approved') {
            $requster_sql .= "and EPORTAL_REQUEST_DETAILS.erd_status in ('$taskstatus','Declined')";
        } else {
            $requster_sql .= "and EPORTAL_REQUEST_DETAILS.erd_status in ('$taskstatus')";
        }
        if (!empty($request_status) && auth()->user()->username == 'somia.maqsood') {
            $requster_sql .= "and eportal_request.er_request_status = '$request_status'";
        }
        if (!in_array(auth()->user()->employee_id, $allowedAllReq)) {
            $requster_sql .= " and er_ecd_approval_user = $employee_id";
        }
        //    if($employee_id != 8996){
        //      $requster_sql .= " and er_ecd_approval_user = $employee_id";
        //    }
        $requster_sql .= " and er_ecd_approval_user is not null";
        // if($sel != 'Pending'){
        if ($ec_id != '0') {
            $requster_sql .= " and ec_id = $ec_id";
        }
        // }
        if ($student != 'noStudent') {
            $requster_sql .= " and lower(er_sai_enrollment_no) = lower('$student')";
        }
        if ($fromDate != '0' && $toDate != '0' && !empty($fromDate) && !empty($toDate)) {
            $requster_sql .= "
                AND TRUNC(
                    TO_DATE(SUBSTR(eportal_request.ER_ENTRY_DATETIME, 1, 19), 'YYYY-MM-DD HH24:MI:SS')
                ) BETWEEN TO_DATE('$fromDate', 'YYYY-MM-DD')
                AND TO_DATE('$toDate', 'YYYY-MM-DD')
            ";
        }

        // Build separate optimized COUNT query (without ORDER BY)
        $countSql = "SELECT COUNT(DISTINCT eportal_request.id) as total_count
        FROM EPORTAL_REQUEST
        left JOIN sisusers ON lower(eportal_request.er_sai_enrollment_no) = lower(sisusers.username)
        LEFT JOIN students_apply_info ON lower(students_apply_info.sai_enrollment_no) = lower(eportal_request.er_sai_enrollment_no)
        LEFT JOIN students_personal_info ON spi_id = sai_spi_id
        left join EPORTAL_REQUEST_DETAILS on er_id = eportal_request.id
        left join eportal_categories on ec_id = eportal_categories.id
        WHERE 1=1";

        // Add same filters as main query (without ORDER BY)
        if ($taskstatus == 'Approved') {
            $countSql .= "and EPORTAL_REQUEST_DETAILS.erd_status in ('$taskstatus','Declined')";
        } else {
            $countSql .= "and EPORTAL_REQUEST_DETAILS.erd_status in ('$taskstatus')";
        }
        if (!empty($request_status) && auth()->user()->username == 'somia.maqsood') {
            $countSql .= "and eportal_request.er_request_status = '$request_status'";
        }
        if (!in_array(auth()->user()->employee_id, $allowedAllReq)) {
            $countSql .= " and er_ecd_approval_user = $employee_id";
        }
        $countSql .= " and er_ecd_approval_user is not null";
        if ($sel != 'Pending') {
            if ($ec_id != '0') {
                $countSql .= " and ec_id = $ec_id";
            }
        }
        if ($student != 'noStudent') {
            $countSql .= " and lower(er_sai_enrollment_no) = lower('$student')";
        }
        if ($fromDate != '0' && $toDate != '0' && !empty($fromDate) && !empty($toDate)) {
            $countSql .= "
                AND TRUNC(
                    TO_DATE(SUBSTR(eportal_request.ER_ENTRY_DATETIME, 1, 19), 'YYYY-MM-DD HH24:MI:SS')
                ) BETWEEN TO_DATE('$fromDate', 'YYYY-MM-DD')
                AND TO_DATE('$toDate', 'YYYY-MM-DD')
            ";
        }

        // dd($request->all()  );
        // Execute count query first
        $totalResult = DB::select($countSql);
        $totalRecords = $totalResult[0]->total_count ?? 0;

        // Add Oracle 12c+ pagination to main query
        $requster_sql .= " order by er_ecd_appr_datetime desc";
        $requster_sql .= " OFFSET $offset ROWS FETCH FIRST $perPage ROWS ONLY";

        //echo $requster_sql;exit;
        $requster_info = DB::select($requster_sql);

        // Prepare response data
        $responseData = [
            'data' => $requster_info,
            'pagination' => [
                'current_page' => (int) $page,
                'per_page' => (int) $perPage,
                'total' => (int) $totalRecords,
                'last_page' => ceil($totalRecords / $perPage),
                'from' => $offset + 1,
                'to' => min($offset + $perPage, $totalRecords),
                'has_more_pages' => ($page * $perPage) < $totalRecords
            ],
            'status' => true
        ];

        // Return paginated response
        return response()->json($responseData);
    }

    function markCompleted(Request $request)
    {
        $status = $request->get('action');
        $remarks = $request->get('remarks');
        $id = $request->get('er_id');
        $stdroll = $request->get('stdroll');
        $chkApplicant = $request->get('admissionApplicant');
        $applicantEmail = $request->get('EmailUser');
        //    dd($request->all());
        $ec_title = $request->get('ec_title');
        if ($status == 'Forward' || $status == 'justForward') {

            $newEcId = $request->user_id ?? null;

            if (!$newEcId) {
                return response()->json([
                    'status' => false,
                    'message' => 'No category selected for forwarding'
                ]);
            }

            try {
                DB::statement("
                    BEGIN 
                        forward_eportal_by_category(:old_id, :new_ec_id, :remarks); 
                    END;
                ", [
                    'old_id' => $request->er_id,
                    'new_ec_id' => $newEcId,
                    'remarks' => trim($request->remarksToForward)
                ]);

                   /*forwarding email start*/
                
                /*
                * Get the new category title.
                * Do not use the old category title coming from the request.
                */

                // dd($newEcId);
                $newCategoryTitle = DB::table('EPORTAL_CATEGORIES')
                ->where('ID', $newEcId)
                            ->value('EC_TITLE');

                /*
                * Fetch all employees assigned to the selected category.
                *
                * This includes:
                * - Approval users
                * - Resolving users
                * - Viewer users
                */
                $categoryUsers = DB::table('EPORTAL_CATEGORIES_DETAILS')
                    ->where('EC_ID', $newEcId)
                    ->select(
                        'ECD_APPROVAL_USER',
                        'ECD_RESOLVING_USER'
                    )
                    ->get();

                    // dd($categoryUsers);

                /*
                * Create one unique employee ID list.
                */
                $employeeIds = $categoryUsers
                    ->flatMap(function ($categoryDetail) {
                        return [
                            $categoryDetail->ecd_approval_user,
                            $categoryDetail->ecd_resolving_user
                        ];
                    })
                    ->filter(function ($employeeId) {
                        return !empty($employeeId);
                    })
                    ->unique()
                    ->values();
                    // dd($employeeIds);

                /*
                * Fetch employee emails.
                */
                $employees = collect();

                if ($employeeIds->isNotEmpty()) {
                    $employees = DB::table('EMP_EMPLOYEES')
                        ->whereIn('EMPLOYEE_ID', $employeeIds->all())
                        ->whereNotNull('EMP_EMAIL')
                        ->select(
                            'EMPLOYEE_ID',
                            'EMP_EMAIL'
                        )
                        ->get();
                }

                //  dd($employees);

                /*
                * Send forwarding email notification.
                */
                $display = 'E-Portal Request Task Forwarded';

                foreach ($employees as $employee) {
                    try {
                        $this->notificationOnForward(
                            $employee->emp_email,
                            $newCategoryTitle ?? 'E-Portal Request',
                            $request->remarksToForward ?: 'E-Portal request forwarded',
                            $display
                        );
                    } catch (\Throwable $mailException) {
                        /*
                        * Request has already been forwarded.
                        * Log email failure without rolling back forwarding.
                        */
                        \Log::error('E-Portal forwarding email failed', [
                            'old_er_id'    => $request->er_id,
                            'new_ec_id'    => $newEcId,
                            'employee_id'  => $employee->employee_id,
                            'email'        => $employee->emp_email,
                            'error'        => $mailException->getMessage()
                        ]);
                    }
                }

                /*send email end*/

                return response()->json([
                    'status' => true,
                    'message' => 'Request forwarded successfully'
                ]);

            } catch (\Exception $e) {
                return response()->json([
                    'status' => false,
                    'message' => $e->getMessage()
                ]);
            }
        }
        if ($chkApplicant == 'admission' && $stdroll == 'null') {
            //echo "!23";
            $sql = "select aai_api_email from admission_apply_info where TO_CHAR(aai_id) = $applicantEmail";
            $resSql = DB::select($sql);
            $email = $resSql[0]->aai_api_email;

        } else {
            // echo "!@34";
            $email = strtolower($stdroll) . '@bnu.edu.pk';
        }

        try {
            //////////////
            if ($status == 'Declined') {
                $updateEscaltion = "update eportal_request set er_escalation_time = '' where id = $id";
                $updateEscaltion = DB::insert($updateEscaltion);
            }
            // elseif($status == 'Pending'){//for rollback

            //     $updateStatus = "UPDATE eportal_request SET er_request_status = 'Pending' WHERE id = $er_id";///if rollback update resolver side pending
            //     DB::update($updateStatus);
            // }
            /////////////////
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
                    'GOOGLE_DRIVE_URL',
                    'ER_SAI_ENROLLMENT_NO',
                    'er_applicant_request_type'
                )
                ->where('ID', '=', $newEcdId)
                ->first();
            // dd($obj);

            // Check if the file exists on Google Drive and delete it if it does
            // $fileExists = Storage::disk('google')->exists($obj->google_drive_file_name);
            // if ($fileExists) {
            //     Storage::disk('google')->delete($obj->google_drive_file_name);
            // }

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

                // Update the database with file information
                DB::table('EPORTAL_REQUEST')
                    ->where('ID', $newEcdId)
                    ->update([
                        'google_drive_file_name' => $file_name,
                        'google_drive_url' => $url,
                        'er_issuance_file' => $fileName,
                        'ER_REMARKS' => $request->input('remarks'),
                        'ER_REQUEST_STATUS' => $request->input('action'),
                        'er_resolving_datetime' => Carbon::now('Asia/Karachi')->format('Y-m-d H:i:s')
                    ]);
                $display = 'E-Portal Application Completion';
                $this->notificationOnCompletion($email, $ec_title, $remarks, $display);
                //$this->notificationStatus($newEcdId,'completion');
                return response()->json(['message' => 'File uploaded successfully', 'status' => true, 'resstatus' => $status]);
            } else {

                // only work for catgory_id= 120 for refund and deferment case marks sttaus as Left
                // dd($obj);
                // Skip this logic for rollback (Pending action)
                if ($obj->ec_id == 120 && $status != 'Declined' && $status != 'Pending') {

                    $eportal_category_id = $obj->ec_id;

                    // Properly quoting the value inside SQL
                    // $sql = "SELECT cms_sai_enrollment_no FROM admission_apply_info WHERE aai_id = ?";

                    if (!is_numeric($obj->er_sai_enrollment_no)) {
                        $sql = "SELECT cms_sai_enrollment_no 
                                FROM admission_apply_info 
                                WHERE cms_sai_enrollment_no = ?";
                    } else {
                        $sql = "SELECT cms_sai_enrollment_no 
                                FROM admission_apply_info 
                                WHERE aai_id = ?";
                    }
                    $resSql = DB::select($sql, [$obj->er_sai_enrollment_no]);

                    if (!empty($resSql)) {
                        $cms_sai_enrollment_no = $resSql[0]->cms_sai_enrollment_no;
                        // dd($cms_sai_enrollment_no);
                        // Correctly update using DB::update() and safe binding
                        if ($obj->er_applicant_request_type == 'refund') {
                            $status = 'Left';
                        }
                        if ($obj->er_applicant_request_type == 'deferment') {
                            $status = 'Defer';
                        }

                        $updateSql = "UPDATE students_apply_info SET SAI_STUDENT_STATUS = '$status' WHERE TO_CHAR(sai_enrollment_no) = ?";
                        DB::update($updateSql, [$cms_sai_enrollment_no]);
                        $updateAdmissionApplyInfoSql = "UPDATE admission_apply_info SET aai_status = '$status' WHERE TO_CHAR(cms_sai_enrollment_no) = ?";
                        DB::update($updateAdmissionApplyInfoSql, [$cms_sai_enrollment_no]);
                    }
                }
                DB::table('EPORTAL_REQUEST')
                    ->where('ID', $newEcdId)
                    ->update([
                        'ER_REMARKS' => $request->input('remarks'),
                        'ER_REQUEST_STATUS' => $request->input('action'),
                        'er_resolving_datetime' => Carbon::now('Asia/Karachi')->format('Y-m-d H:i:s')
                    ]);
                $display = 'E-Portal Application Completion';
                $this->notificationOnCompletion($email, $ec_title, $remarks, $display);
                //$this->notificationStatus($newEcdId,'completion');

                // Custom messages based on action (before modifying status)
                $resolverid = auth()->user()->employee_id;

                DB::table('EPORTAL_REQUEST_DETAILS')
                    ->where('ER_ID', $newEcdId)
                    ->where('ER_ECD_RESOLVING_USER', $resolverid)
                    ->update([
                        'ER_ECD_REMARKS' => $request->input('remarks'),
                        'ERD_STATUS' => $request->input('action'),
                        'ER_ECD_APPR_DATETIME' => Carbon::now('Asia/Karachi')->format('Y-m-d H:i:s')
                    ]);
                $action = $request->input('action');


                if ($action == 'Pending') {
                    $message = 'Request rolled back to pending status successfully!';
                } elseif ($action == 'Declined') {
                    $message = 'Request declined successfully!';
                } elseif ($action == 'Completed') {
                    $message = 'Request Completed Successfully';
                    // Add Left status info only for category 120 when completing
                    if ($obj->ec_id == 120) {
                        $message .= ' - Status marked as "Left"';
                    }
                } else {
                    $message = 'Request updated successfully!';
                }

                // in case of decline, er request status should return complete to redirect to complete tab
                if ($action == 'Declined') {
                    $status = 'Completed';
                }

                return response()->json(['message' => $message, 'status' => false, 'resstatus' => $status]);
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
        // Get Logged-in User's School
        $emp_id = auth()->user()->employee_id;
        $userSchool = DB::table('EMP_EMPLOYEES')->where('employee_id', $emp_id)->value('emp_school_id');

        // Check if we have a staffId (direct lookup) or a term (Select2 search)
        $term = $request->input('staffId'); // Select2 sends 'term', but we mapped it to 'staffId' in JS

        // If it's a pipe-separated ID (legacy logic from VC)
        if (strpos($term, '|') !== false) {
            $values = explode('|', $term);
            $selectedEmployee = $values[1];
            $whereClause = "employee_id = $selectedEmployee";
        }
        // If it's a numeric ID
        else if (is_numeric($term)) {
            $whereClause = "employee_id = $term";
        }
        // If it's a text search (Name)
        else {
            // Sanitize query to prevent SQL injection issues with raw queries (though binding is better, this is legacy style)
            // Using UPPER for case-insensitive search if needed, but let's stick to simple LIKE
            $whereClause = "(lower(emp_f_name) LIKE lower('%$term%') OR lower(emp_l_name) LIKE lower('%$term%'))";
        }

        // Fully qualify columns to avoid ORA-00918 ambiguous column error
        $select_list = "EMP_EMPLOYEES.prefix, EMP_EMPLOYEES.employee_id, EMP_EMPLOYEES.emp_f_name, EMP_EMPLOYEES.emp_l_name, departments.dept_name, schools.school_code, sisusers.email as emp_work_email";

        $sql = "SELECT
                id,
                ec_title,
                school_code,
                dept_name,
                dept_id,
                school_id,
                school_name,
                eportal_dept_name,
                EPORTAL_DEPT_ORDER,
                CATEGORY_ORDER

            FROM
                eportal_categories
                LEFT JOIN schools ON schools.school_id = ec_school_id
                LEFT JOIN departments dept ON ec_department_id = dept_id
            WHERE
                id IN (
                    SELECT DISTINCT
                        ec_id
                    FROM
                        eportal_categories_details
                    WHERE
                        ec_id NOT IN (84, 4, 86)
                )
                and
                ec_status='E'";

        if ($term) {
            $sql .= " AND LOWER(ec_title) LIKE LOWER('%$term%')";
        }

        $sql .= " ORDER BY
                    TO_NUMBER(eportal_dept_order) ASC,
                    TO_NUMBER(category_order) ASC";

        $staffData = DB::select($sql);
        return $staffData;
    }

    function getReportStudentExit(Request $request)
    {
        return view('ePortal.eportal_report_studentexit');
    }

    function markApproval(Request $request)
    {

        $currentDateTime = Carbon::now('Asia/Karachi')->format('Y-m-d H:i:s');

        // dd($request->all());
        $status = $request->get('action');
        $remarks = $request->get('remarks');
        $id = $request->get('erd_id');//eportal request details main id
        $er_id = $request->get('er_id');//eportal request main id
        $approval_user = auth()->user()->employee_id;
        $sql = "select * from eportal_request where id = $er_id";
        $resSql = DB::select($sql);
        //if($resSql[0]->ec_id == 86 && $status == 'Declined'){
        if ($status == 'Declined') {
            $updateEscaltion = "update eportal_request set er_escalation_time = '' where id = $er_id";
            $updateEscaltion = DB::insert($updateEscaltion);

            $updateErRemakrs = "UPDATE eportal_request SET er_remarks = '$remarks' WHERE id = $er_id";///if declined update resolver remarks as approver remarks
            DB::update($updateErRemakrs);

            $updateStatus = "UPDATE eportal_request SET er_request_status = 'Declined' WHERE id = $er_id";///if declined update resolver side declined 
            DB::update($updateStatus);
        } elseif ($request->input('action') == 'Pending') {//for rollback 

            $updateStatus = "UPDATE eportal_request SET er_request_status = 'Pending' WHERE id = $er_id";///if rollback update resolver side pending
            DB::update($updateStatus);
        }
        if ($request->input('action') == 'futher_action') {
            $status = 'Pending';
        }
        $requster_sql = " UPDATE
    eportal_request_details
    SET
        erd_status = '$status',
        er_ecd_approval_user = '$approval_user',
        ER_ECD_REMARKS = '$remarks',
        er_ecd_appr_datetime =  '$currentDateTime'
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

        // Custom messages based on action
        $message = '';
        if ($request->input('action') == 'Completed') {
            $message = 'Request completed successfully!';
        } elseif ($request->input('action') == 'Declined') {
            $message = 'Request declined successfully!';
        } elseif ($request->input('action') == 'Pending') {
            $message = 'Request rolled back to pending status successfully!';
        } else {
            $message = "Request " . $status . " successfully!";
        }

        return response()->json(['data' => $res, 'message' => $message, 'resstatus' => $status])
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');

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
        return $result = DB::executeProcedure($procedureName, $bindings);
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
        return $result_notification = DB::executeProcedure($procedurenotification, $bindings_notification);
    }
    function notificationFunctionNeeded($id, $sender_id)
    {
        $procedureName = 'eportal_further_action';
        $bindings = [
            'req_id' => $id,
            'sender_id' => $sender_id,
        ];
        return $result = DB::executeProcedure($procedureName, $bindings);
    }

    public function rollbackStatus(Request $request)
    {

        $requster_sql = " UPDATE
         eportal_request
         SET
             er_request_status = 'Pending',
             er_remarks = '$request->remarks'
         WHERE
             id = $request->id";
        $requster_info = DB::insert($requster_sql);
        $res = array('status' => true, 'message' => 'Record Update successfully!');
        return response()->json($res);
    }
    public function rejectStatus(Request $request)
    {
        // dd($request->id);
        $requster_sql = " UPDATE
         eportal_request
         SET
             er_request_status = 'Rrejected',
             er_remarks = '$request->remarks'
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
                        'ER_REMARKS' => $request->input('new_remarks'),
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

    function getReport()
    {
        $sql = "select * from eportal_request";
        $res = DB::select($sql);

        // $deptSql = "select * from departments where dept_id in (23,41,61,24)";
        // $resDeptSql = DB::select($deptSql);

        $sel = auth()->user()->dept_id;

        $deptSql = "select id,
        ec_title,
        (select count(*) from eportal_request where ec_id = eportal_categories.id) as count from eportal_categories where 1 = 1";
        if ($sel == 23) {
            $deptSql .= " and id in (1,2,3,9,10,11,12,13,14)";
        } else if ($sel == 41) {
            $deptSql .= " and id in (86,99,85)";
        } else if ($sel == 61) {
            $deptSql .= " and id in (18,25)";
        } else if ($sel == 24) {
            $deptSql .= " and id in (61,82,117,105,106,107,108,109,110,100)";
        } else if ($sel == 20) {
            $deptSql .= " and id in (88,104,87,101,89)";
        }
        // echo $deptSql;exit;
        $resDeptSql = DB::select($deptSql);
        return view('ePortal.eportal_report_staff', ['res' => $res, 'dept' => $resDeptSql]);
    }
    function fetchReport(Request $request)
    {
        $sel = $request->input('sel');
        $reqStatus = $request->input('reqStatus');
        //echo $sel;exit;
        $sql = "SELECT
                    eportal_request.id as er_id,
                    er_sai_enrollment_no,
                    spi_name || ' (' || er_sai_enrollment_no || ')' as student_name,
                    (
                    SELECT LISTAGG(
                    ee1.emp_f_name || '  ' || ee1.emp_l_name || ' , ' ||
                    ee2.emp_f_name || '  ' || ee2.emp_l_name,
                    ', ')
                            WITHIN GROUP (ORDER BY erd.er_id)
                        FROM eportal_request_details erd
                        LEFT JOIN emp_employees ee1 ON ee1.employee_id = erd.er_ecd_approval_user
                        LEFT JOIN emp_employees ee2 ON ee2.employee_id = erd.er_ecd_resolving_user
                        WHERE erd.er_id = eportal_request.id
                        AND erd.er_ecd_viewer_user IS NULL
                        AND erd.er_ecd_instruction_categories IS NULL
                        AND erd.er_ecd_completion_days IS NULL
                        AND erd.erd_status = 'Pending'
                    ) AS pending,
                    er_entry_datetime,
                    school_code,
                    er_resolving_datetime,
                    er_escalation_time,ec_title,er_request_status
                FROM
                    eportal_request
                    LEFT JOIN students_apply_info ON sai_enrollment_no = er_sai_enrollment_no
                    LEFT JOIN students_personal_info ON sai_spi_id = spi_id
                    left join schools on school_id = sai_sdpo_school_id
                    left join eportal_categories on eportal_categories.id =ec_id
                where ec_id not in (83)";

        if (auth()->user()->dept_id == 23) {
            if ($sel == 0) {
                $sql .= " and ec_id in (1,2,3,9,10,11,12,13,14)";
            } else {
                $sql .= " and ec_id in $sel";
            }
        } else if (auth()->user()->dept_id == 41) {
            if ($sel == 0) {
                $sql .= " and ec_id in (86,99,85)";
            } else {
                $sql .= " and ec_id in $sel";
            }
        } else if (auth()->user()->dept_id == 61) {
            if ($sel == 0) {
                $sql .= " and ec_id in (18,25)";
            } else {
                $sql .= " and ec_id in $sel";
            }
        } else if (auth()->user()->dept_id == 24) {
            if ($sel == 0) {
                $sql .= " and ec_id in (61,82,117,105,106,107,108,109,110,100)";
            } else {
                $sql .= " and ec_id in $sel";
            }
        } else if (auth()->user()->dept_id == 20) {
            if ($sel == 0) {
                $sql .= " and ec_id in (88,104,87,101,89)";
            } else {
                $sql .= " and ec_id in $sel";
            }
        }
        if (isset($reqStatus)) {
            if ($reqStatus == 'Completed') {
                $sql .= " AND er_request_status = 'Completed'";
            } elseif ($reqStatus == 'Pending') {
                $sql .= " AND er_request_status = 'Pending'";
            } elseif ($reqStatus == 'Declined') {
                $sql .= " AND er_request_status = 'Declined'";
            }
        }

        //$sql .= " and ec_id in $sel";

        $sql .= " order by er_resolving_datetime desc";
        // echo $sql;exit;
        return $res = DB::select($sql);
    }

    public function showCheckinCheckout()
    {
        if (request()->ajax()) {
            $records = DB::table('HOSTEL_ALLOCATION_HISTORY as h')
                ->leftJoin('academic_sessions as a', 'h.HAH_SESSION_ID', '=', 'a.sess_id')
                ->leftJoin('student_fee_structure as sfs', function ($join) {
                    $join->on('h.HAH_SESSION_ID', '=', 'sfs.sfs_session_id')
                        ->on('h.HAH_STUDENT_ENROLL_NO', '=', 'sfs.sfs_student_id');
                })
                ->select(
                    'h.ID',
                    'h.HAH_SESSION_ID as session_id',
                    'h.HAH_STUDENT_ENROLL_NO as student_enroll_no',
                    'h.HAH_APP_ID as app_id',
                    'h.HAH_CHECKIN_DATE as checkin_date',
                    'h.HAH_CHECKOUT_DATE as checkout_date',
                    'h.HAH_ENTRY_USERNAME as entry_username',
                    'h.HAH_ENTRY_DATETIME as entry_datetime',
                    'a.sess_name',
                    DB::raw('CASE 
                        WHEN sfs.hostel_fee_original IS NOT NULL 
                        AND sfs.hostel_fee_refundable IS NOT NULL 
                        THEN 1 ELSE 0 END as has_hostel_fee')
                )
                ->orderBy('h.ID', 'desc')
                ->get();

            return response()->json(['data' => $records]);
        }

        $academicYear = DB::table('academic_sessions')
            ->select('sess_id', 'sess_name')
            ->orderBy('sess_id', 'desc')
            ->get();

        return view('Hostel.hostel_update_checkin_and_checkout_date', compact('academicYear'));
    }
    function getExamReportStudentExit(Request $request)
    {
        // dd("working");
        return view('ePortal.exam_eportal_report_studentexit');
    }
      function notificationOnForward($to, $subject, $message, $display)
    {

        // Debug/Logging
        DB::table('testing')->insert([
            'description' => $display . ' To: ' . $to . ' || Subject: ' . $subject . ' || Message: ' . $message
        ]);

        $procedurenotification = 'send_mail_one_by_one';
        $bindings_notification = [
            'to' => $to,
            'subject' => $subject,
            'message' => $message,
            'display' => $display,
        ];

        

        return $result_notification = DB::executeProcedure($procedurenotification, $bindings_notification); 
    }


}
