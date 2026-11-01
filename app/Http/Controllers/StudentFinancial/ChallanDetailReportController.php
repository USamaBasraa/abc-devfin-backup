<?php
/**
 * Created by PhpStorm.
 * User: Maher Asad
 * Date: 5/14/2019
 * Time: 9:52 AM
 */
namespace App\Http\Controllers\StudentFinancial;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request; 
use Symfony\Component\Console\Helper\Table;
use DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
use App\Models\Grader\Schools;
use App\Models\Grader\Departments;
use App\Models\Grader\FacultyCourses;
use App\Models\Grader\EmpEmployees;
use Response;
use Validator;
use DataTables;
use DateTime;

class ChallanDetailReportController extends Controller{

    function challanDetailReport(){
        // dd("www");
        $sessions = DB::select("select * from ACADEMIC_SESSIONS where SESS_STATUS = 'E' order by SESS_ID desc");
        $challan_type = DB::select("select distinct sc_challan_type from student_challan where sc_challan_type is not null");
        $challanTypesArray = [];
        foreach ($challan_type as $row) {
            $key = $row->sc_challan_type;
            // Replace - and _ with space, make first letter uppercase
            $value = ucwords(str_replace(['_', '-'], ' ', $key));
            $challanTypesArray[$key] = $value;
        }
        // dd($challanTypesArray);
        // dd($sessions);
        return view('StudentFinancial.challanDetailReport',compact('sessions', 'challanTypesArray'));
    }

    public function getLedger(Request $request)
    {
        $data = $this->getFeeSummaryData($request);

        // dd($data);
        return DataTables::of($data)->toJson();
    }

    /**
     * Returns a minimal HTML page that POSTs bulk_json to staff chalanfinBulk.php (one challan per PDF page).
     *
     * Either POST selection_json (array of {challan_id, stp}) from the DataTable, or omit it and pass the same
     * filter fields as /get-challan-detail (from/to challan optional).
     */
    public function bulkChallanPdfLauncher(Request $request)
    {
        $staffUrl = rtrim((string) config('app.staff_url'), '/');
        if ($staffUrl === '') {
            return response('Staff URL is not configured. Set STAFF_URL in .env (same base as single challan preview).', 500);
        }

        $selectionRaw = $request->input('selection_json');
        if ($selectionRaw !== null && $selectionRaw !== '') {
            $decoded = json_decode($selectionRaw, true);
            if (!is_array($decoded)) {
                return response('Invalid selection_json.', 422);
            }
            $items = $this->normalizeBulkPdfItemsFromSelection($decoded);
        } else {
            $rows = $this->getFeeSummaryData($request);
            $items = $this->buildBulkPdfItemsFromFeeRows($rows);
        }

        if (count($items) === 0) {
            return response('No printable challans found. Fine-type rows are skipped (use single preview for those). For old (Pinnacle) challans the buyer code must be present.', 422);
        }

        $maxBulk = 500;
        if (count($items) > $maxBulk) {
            return response(
                'Too many challans (' . count($items) . ') for one PDF. Narrow filters or selection (maximum ' . $maxBulk . ' per run).',
                422
            );
        }

        usort($items, function ($a, $b) {
            $ca = $a['challan_id'];
            $cb = $b['challan_id'];
            if (is_numeric($ca) && is_numeric($cb)) {
                return $ca <=> $cb;
            }

            return strcmp((string) $ca, (string) $cb);
        });

        $bulkJson = json_encode($items);
        $action = $staffUrl . '/chalanfinBulk.php';

        return response()->view('StudentFinancial.challanBulkPdfLauncher', compact('action', 'bulkJson'));
    }

    /**
     * @param  array<int, mixed>  $decoded
     * @return array<int, array{challan_id: mixed, stp: string}>
     */
    protected function normalizeBulkPdfItemsFromSelection(array $decoded): array
    {
        $items = [];
        $seen = [];
        foreach ($decoded as $it) {
            if (!is_array($it) || !array_key_exists('challan_id', $it)) {
                continue;
            }
            $cid = $it['challan_id'];
            if ($cid === null || $cid === '') {
                continue;
            }
            $stp = (isset($it['stp']) && $it['stp'] === 'old') ? 'old' : 'new';
            $ck = $stp . '|' . (string) $cid;
            if (isset($seen[$ck])) {
                continue;
            }
            $seen[$ck] = true;
            $items[] = [
                'challan_id' => is_numeric($cid) ? (int) $cid : $cid,
                'stp' => $stp,
            ];
        }

        return $items;
    }

    /**
     * @param  array<int, object>|\Illuminate\Support\Collection  $rows
     * @return array<int, array{challan_id: mixed, stp: string}>
     */
    protected function buildBulkPdfItemsFromFeeRows($rows): array
    {
        $items = [];
        $seen = [];
        foreach ($rows as $r) {
            $type = isset($r->sc_challan_type) ? (string) $r->sc_challan_type : '';
            if (strcasecmp($type, 'Fine') === 0) {
                continue;
            }
            $sid = isset($r->session_id) ? (int) $r->session_id : 0;
            $stp = ($sid === 0) ? 'old' : 'new';
            if ($stp === 'old') {
                $cid = isset($r->buyercodechallanno) ? $r->buyercodechallanno : null;
            } else {
                $cid = isset($r->cms_challan_id) ? $r->cms_challan_id : null;
            }
            if ($cid === null || $cid === '') {
                continue;
            }
            $ck = $stp . '|' . (string) $cid;
            if (isset($seen[$ck])) {
                continue;
            }
            $seen[$ck] = true;
            $items[] = [
                'challan_id' => is_numeric($cid) ? (int) $cid : $cid,
                'stp' => $stp,
            ];
        }

        return $items;
    }

    /**
     * Latest-version lookup via grouped join (avoids per-row correlated subquery on fee_challan_data).
     * Filters are pushed into the subquery so large challan ranges stay performant.
     */
    protected function buildLatestVersionJoin(Request $request, bool $isEnrollment, array &$bindings): string
    {
        $where = 'WHERE 1=1';

        if (!empty($request->session_sel)) {
            $where .= " AND fcd.SESS_ID = ? ";
            $bindings[] = $request->session_sel;
        }

        if ($request->challanStatus == 'paid') {
            $where .= " AND fcd.CHALLAN_STATUS = 'PAID' ";
        } elseif ($request->challanStatus == 'unpaid') {
            $where .= " AND fcd.CHALLAN_STATUS = 'UNPAID' ";
        }

        if (!empty($request->enrollment)) {
            $enrollment = strtoupper(trim($request->enrollment));
            if ($isEnrollment) {
                $where .= " AND fcd.REGNO = ? ";
            } else {
                $where .= " AND fcd.APP_ID = ? ";
            }
            $bindings[] = $enrollment;
        }

        if (!empty($request->from_challan) && !empty($request->to_challan)) {
            $where .= " AND fcd.CHALLAN_NO BETWEEN ? AND ? ";
            $bindings[] = $request->from_challan;
            $bindings[] = $request->to_challan;
        } elseif (!empty($request->from_challan)) {
            $where .= " AND fcd.CHALLAN_NO >= ? ";
            $bindings[] = $request->from_challan;
        } elseif (!empty($request->to_challan)) {
            $where .= " AND fcd.CHALLAN_NO <= ? ";
            $bindings[] = $request->to_challan;
        }

        if (!empty($request->challan_type)) {
            $where .= " AND fcd.CHALLAN_TYPE = ? ";
            $bindings[] = $request->challan_type;
        }

        if (!empty($request->from_date) && !empty($request->to_date)) {
            $where .= " AND TRUNC(fcd.ISSUE_DATE)
                BETWEEN TO_DATE(?, 'YYYY-MM-DD')
                AND TO_DATE(?, 'YYYY-MM-DD') ";
            $bindings[] = $request->from_date;
            $bindings[] = $request->to_date;
        } elseif (!empty($request->from_date)) {
            $where .= " AND TRUNC(fcd.ISSUE_DATE) >= TO_DATE(?, 'YYYY-MM-DD') ";
            $bindings[] = $request->from_date;
        } elseif (!empty($request->to_date)) {
            $where .= " AND TRUNC(fcd.ISSUE_DATE) <= TO_DATE(?, 'YYYY-MM-DD') ";
            $bindings[] = $request->to_date;
        }

        return "
                        INNER JOIN (
                            SELECT
                                CHALLAN_NO,
                                MAX(VERSION) AS MAX_VERSION
                            FROM fee_challan_data fcd
                            {$where}
                            GROUP BY CHALLAN_NO
                        ) latest
                            ON latest.CHALLAN_NO = sc.CHALLAN_NO
                            AND latest.MAX_VERSION = sc.VERSION
                ";
    }

 protected function getFeeSummaryData(Request $request)
    {
        $bindings = [];
        if($this->isEnrollmentNo($request->enrollment)){
            $latestVersionJoin = $this->buildLatestVersionJoin($request, true, $bindings);
            $sql = "SELECT
                        CASE
                            WHEN sc.challan_no <= 185088 THEN
                                sc.PIN_FEECHALLANNO
                            ELSE
                                sc.challan_no
                        END                               AS buyercodechallanno,
                        sc.CHALLAN_NO                             AS cms_challan_id,
                        sc.REGNO               AS regno,
                        sc.SESS_ID                  AS session_id,
                        sess.sess_name                    AS session_name,
                        sc.PAID_DATE                     AS paidon,
                        sc.APP_ID                      AS pvnoslipnostudentid,
                        sc.INVOICE_AMOUNT           AS totalamountpayable,
                        sc.ISSUE_DATE                   AS issuedate,
                        sc.CHALLAN_STATUS              AS challanstatus,
                        sc.CHALLAN_TYPE                AS sc_challan_type,
                        sc.CHALLAN_TYPE                AS finetype,
                        sc.INVOICE_AMOUNT                  AS totalamountpayable,
                        nvl(sc.PAID_AMOUNT, 0)        AS paidamt,
                        sc.DUE_DATE                    AS duedate,
                        sc.VALID_DATE               AS validdate,
                        sc.STUDENT_STATUS              AS studenttype,
                        sc.STUDENT_STATUS              AS studenttypenew,
                        spi.spi_fathername               AS parentguardianname,
                        sc.INSTALLMENTS         AS installments,
                        sch.school_name                   AS school,
                        dept.dept_name                    AS department,
                        prog.prog_name                    AS program,
                        spi.spi_name                      AS studentname,
                        spi.spi_guardian_cnic             AS parentguardiancnic,
                        spi.spi_permanentaddress          AS permanentaddress,
                        sai.sai_semester                  AS semester,
                        sai.sai_student_status            AS sai_student_status
                    FROM
                        fee_challan_data         sc
                        {$latestVersionJoin}
                        LEFT JOIN students_apply_info     sai ON sai.sai_enrollment_no = sc.REGNO
                        LEFT JOIN students_personal_info  spi ON spi.spi_id = sai.sai_spi_id
                        INNER JOIN academic_sessions       sess ON sess.sess_id = sc.SESS_ID
                        INNER JOIN schools                 sch ON sch.school_id = sai.sai_sdpo_school_id
                        INNER JOIN departments             dept ON dept.dept_id = sai.sai_sdpo_dept_id
                        INNER JOIN programs                prog ON prog.prog_id = sai.sai_sdpo_prog_id
                    WHERE 1=1
                ";
                if (!empty($request->session_sel)) {
                    $sql .= "and sc.SESS_ID = ? ";
                    $bindings[] = $request->session_sel;
                }
                // Add filter based on challanStatus
                if ($request->challanStatus == 'paid') {
                    $sql .= "and sc.CHALLAN_STATUS = 'PAID'";
                } elseif ($request->challanStatus == 'unpaid') {
                    $sql .= "and sc.CHALLAN_STATUS = 'UNPAID'";
                }

                if (!empty($request->enrollment)) {
                    $sql .= "and  TO_CHAR(sc.REGNO) = UPPER(?)";
                    $bindings[] = $request->enrollment;
                }

                if (!empty($request->from_challan) && !empty($request->to_challan)) {
                    $sql .= " AND sc.CHALLAN_NO BETWEEN ? AND ? ";
                    $bindings[] = $request->from_challan;
                    $bindings[] = $request->to_challan;
                }
                elseif (!empty($request->from_challan)) {
                    $sql .= " AND sc.CHALLAN_NO >= ? ";
                    $bindings[] = $request->from_challan;
                }
                elseif (!empty($request->to_challan)) {
                    $sql .= " AND sc.CHALLAN_NO <= ? ";
                    $bindings[] = $request->to_challan;
                }

                if (!empty($request->challan_type)) {
                    $sql .= " AND sc.CHALLAN_TYPE = ? ";
                    $bindings[] = $request->challan_type;
                }

                 // **************** from date, to date filter start ****************
        if (!empty($request->from_date) && !empty($request->to_date)) {

    $sql .= " AND TRUNC(sc.ISSUE_DATE)
              BETWEEN TO_DATE(?, 'YYYY-MM-DD')
              AND TO_DATE(?, 'YYYY-MM-DD') ";
    $bindings[] = $request->from_date;
    $bindings[] = $request->to_date;

                } elseif (!empty($request->from_date)) {

                    $sql .= " AND TRUNC(sc.ISSUE_DATE)
                            >= TO_DATE(?, 'YYYY-MM-DD') ";
                    $bindings[] = $request->from_date;

                } elseif (!empty($request->to_date)) {

                    $sql .= " AND TRUNC(sc.ISSUE_DATE)
                            <= TO_DATE(?, 'YYYY-MM-DD') ";
                    $bindings[] = $request->to_date;
                }
        // ************** from date, to date filter end ******************


            //    echo  $sql;exit;
            // dd($sql);
        }else{
            $latestVersionJoin = $this->buildLatestVersionJoin($request, false, $bindings);
            $sql = "SELECT
                        CASE
                            WHEN sc.challan_no <= 185088 THEN
                                sc.PIN_FEECHALLANNO
                            ELSE
                                sc.challan_no
                        END                               AS buyercodechallanno,
                        sc.CHALLAN_NO                             AS cms_challan_id,
                        sc.REGNO               AS regno,
                        sc.SESS_ID                  AS session_id,
                        sess.sess_name                    AS session_name,
                        sc.PAID_DATE                     AS paidon,
                        sc.APP_ID                      AS pvnoslipnostudentid,
                        sc.INVOICE_AMOUNT           AS totalamountpayable,
                        sc.ISSUE_DATE                   AS issuedate,
                        sc.CHALLAN_STATUS              AS challanstatus,
                        sc.CHALLAN_TYPE                AS sc_challan_type,
                        sc.CHALLAN_TYPE                AS finetype,
                        sc.INVOICE_AMOUNT                  AS totalamountpayable,
                        nvl(sc.PAID_AMOUNT, 0)        AS paidamt,
                        sc.DUE_DATE                    AS duedate,
                        sc.VALID_DATE               AS validdate,
                        sc.STUDENT_STATUS              AS studenttype,
                        sc.STUDENT_STATUS              AS studenttypenew,
                        api.api_fathername                AS parentguardianname,
                        sc.INSTALLMENTS         AS installments,
                        sch.school_name                   AS school,
                        dept.dept_name                    AS department,
                        prog.prog_name                    AS program,
                        api.api_name                      AS studentname,
                        api.api_guardian_cnic             AS parentguardiancnic,
                        api.api_permanentaddress          AS permanentaddress,
                        NULL                              AS semester,
                        aai.aai_status                    AS sai_student_status
                    FROM
                        fee_challan_data          sc
                        {$latestVersionJoin}
                        LEFT JOIN admission_apply_info     aai ON aai.aai_id = sc.APP_ID
                        LEFT JOIN admission_personal_info  api ON api.api_id = aai.aai_api_id
                        INNER JOIN academic_sessions        sess ON sess.sess_id = sc.SESS_ID
                        INNER JOIN schools                  sch ON sch.school_id = aai.aai_sdpo_school_id
                        INNER JOIN departments              dept ON dept.dept_id = aai.aai_sdpo_dept_id
                        INNER JOIN programs                 prog ON prog.prog_id = aai.aai_sdpo_prog_id
                    where 1=1
                ";
                if (!empty($request->session_sel)) {
                    $sql .= " AND sc.SESS_ID = ? ";
                    $bindings[] = $request->session_sel;
                }
                // Add filter based on challanStatus
                if ($request->challanStatus == 'paid') {
                    $sql .= "and sc.CHALLAN_STATUS = 'PAID'";
                } elseif ($request->challanStatus == 'unpaid') {
                    $sql .= "and sc.CHALLAN_STATUS = 'UNPAID'";
                }

                if (!empty($request->enrollment)) {
                    $sql .= "and TO_CHAR(sc.APP_ID) = UPPER(?) ";
                    $bindings[] = $request->enrollment;
                }

                if (!empty($request->from_challan) && !empty($request->to_challan)) {
                    $sql .= " AND sc.challan_no BETWEEN ? AND ? ";
                    $bindings[] = $request->from_challan;
                    $bindings[] = $request->to_challan;
                }
                elseif (!empty($request->from_challan)) {
                    $sql .= " AND sc.challan_no >= ? ";
                    $bindings[] = $request->from_challan;
                }
                elseif (!empty($request->to_challan)) {
                    $sql .= " AND sc.challan_no <= ? ";
                    $bindings[] = $request->to_challan;
                }

                if (!empty($request->challan_type)) {
                    $sql .= " AND sc.CHALLAN_TYPE = ? ";
                    $bindings[] = $request->challan_type;
                }
                // ***************** from date, to date filter start **********************
                if (!empty($request->from_date) && !empty($request->to_date)) {

                $sql .= " AND TRUNC(sc.ISSUE_DATE)
                        BETWEEN TO_DATE(?, 'YYYY-MM-DD')
                        AND TO_DATE(?, 'YYYY-MM-DD') ";
                $bindings[] = $request->from_date;
                $bindings[] = $request->to_date;

                } elseif (!empty($request->from_date)) {

                    $sql .= " AND TRUNC(sc.ISSUE_DATE)
                            >= TO_DATE(?, 'YYYY-MM-DD') ";
                    $bindings[] = $request->from_date;

                } elseif (!empty($request->to_date)) {

                    $sql .= " AND TRUNC(sc.ISSUE_DATE)
                            <= TO_DATE(?, 'YYYY-MM-DD') ";
                    $bindings[] = $request->to_date;
                }
                // **************** from date, to date filter end ***********************
        }
        //    echo  $sql;exit;
        $data = DB::select($sql, $bindings);
        return $data;
    }


    function isEnrollmentNo($enrollment) {
        // Check if the input is a valid string and contains a hyphen
        if (is_string($enrollment) && strpos($enrollment, '-') !== false) {
            return true;
        }
        return false;
    }

}
