<?php

namespace App\Http\Controllers\StudentFinancial;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Grader\Schools;
use DB;
use DataTables;

/**
 * Challan Summary Report page (route: challan-summary-report).
 *
 * Split out of ChallanReportController so this report can evolve independently
 * of the challan-report page. Everything the summary-report route needs lives
 * here; the shared view-level / normalisation helpers are duplicated (kept small)
 * so the two controllers stay fully decoupled.
 */
class ChallanSummaryReportController extends Controller
{
    //////Financial Report Challan Summary Report//////
    function challanSummaryReport()
    {
        $sessions = \App::call('App\Http\Controllers\Grader\AcademicSessionController@retrieve');
        // dd($sessions);
        $user = Auth::user()->username;
        $school_id = auth()->user()->school_id;
        $allowedUsers = [
            'ijaz.hussain', 'bushra.rahman', 'shafaat.ahmed',
            'tahir.kamran', 'omer.hassan', 'rashid.rana', 'ruhi.khalid', 'amra.raza', 'shabana', 'zainabsanik'
        ];
        if (!in_array($user, $allowedUsers)) {
            $schools = Schools::where('school_status', 'E')->get();
        } else {
            $schools = Schools::where('school_id', $school_id)->get();
        }

        $departments = array(); // $this->school->fetch();
        $programs = DB::select("select * from Programs where PROG_STATUS = 'E'");
        $query = 'select * from fin_fee_category';
        return view('StudentFinancial.challan_summary_report', compact('sessions', 'schools', 'departments', 'programs'));
    }

    protected function getChallanSummaryReport(Request $request)
    {
        return $this->getFullSemChallanSummaryReport($request);
    }

    protected function getFullSemChallanSummaryReport(Request $request)
    {
        $allowedTypes = ['Existing', 'New Admission FALL', 'New Admission SPRING'];
        $selectedType = trim($request->studentTypeOption ?? '');
        $viewLevel = $this->resolveSummaryViewLevel($request);

        // Any student type selected → original single-set report (pre-grouping behaviour)
        if ($selectedType !== '') {
            $studentType = in_array($selectedType, $allowedTypes, true)
                ? $selectedType
                : $selectedType;
            $data = $this->runSummaryReportQuery($request, $studentType);
            return $this->summaryReportJsonResponse($data, false, $viewLevel);
        }

        // No student type selected → show all groups side by side + consolidated
        $typePrefixes = [
            'Existing' => 'existing',
            'New Admission FALL' => 'fall',
            'New Admission SPRING' => 'spring',
        ];

        $merged = [];
        foreach ($allowedTypes as $type) {
            $rows = $this->runSummaryReportQuery($request, $type);
            foreach ($rows as $row) {
                $key = $this->fullSemSummaryRowKey($row, $viewLevel);
                if (!isset($merged[$key])) {
                    $merged[$key] = $this->emptyGroupedSummaryRow($row, $viewLevel);
                }
                $this->applyMetricsToGroupedRow($merged[$key], $row, $typePrefixes[$type]);
            }
        }

        $data = [];
        foreach ($merged as $row) {
            $data[] = $this->computeConsolidatedMetrics($row);
        }

        usort($data, function ($a, $b) use ($viewLevel) {
            $sortKeys = ['sess_name', 'school_name'];
            if ($viewLevel === 'program') {
                $sortKeys[] = 'dept_name';
                $sortKeys[] = 'prog_name';
            }

            return array_map(function ($key) use ($a, $b) {
                return $a[$key] ?? '';
            }, $sortKeys) <=> array_map(function ($key) use ($a, $b) {
                return $b[$key] ?? '';
            }, $sortKeys);
        });

        return $this->summaryReportJsonResponse($data, true, $viewLevel);
    }

    protected function summaryReportJsonResponse($data, $grouped, string $viewLevel = 'program')
    {
        $normalized = $this->normalizeSummaryRows($data);
        $response = DataTables::of($normalized)->toJson()->getData(true);
        $response['grouped'] = $grouped;
        $response['summaryViewLevel'] = $viewLevel;

        return response()->json($response);
    }

    protected function resolveSummaryViewLevel(Request $request): string
    {
        $level = strtolower(trim($request->summaryViewLevel ?? 'school'));

        return $level === 'program' ? 'program' : 'school';
    }

    protected function normalizeSummaryRows($rows)
    {
        return array_map(function ($row) {
            $normalized = [];
            foreach ((array) $row as $key => $value) {
                $normalized[strtolower($key)] = $value;
            }

            return $normalized;
        }, $rows);
    }

    /**
     * Query for the challan-summary-report page. STUDENT-STATUS based.
     *
     * Population (Issued): distinct students who have a challan in
     * AUD_BNUCMS.FIN_STUDENT_CHALLAN_VER matching the selected session / school /
     * student-type (SC_STUDENT_STATUS) / fee-type (SC_CHALLAN_TYPE). The version
     * table is reduced to the latest version per CHALLAN_NO, then tallied per student.
     *
     * Classification is driven by the STUDENT's status, not the challan's:
     *   Active    = students_apply_info.SAI_STUDENT_STATUS = 'Active' (is_active = 1).
     *               Any number of cancel/reissue cycles on their challans is irrelevant.
     *   Cancelled = every other SAI status, incl. no student record (is_active = 0).
     * Paid/unpaid buckets are computed over ACTIVE students only, so:
     *   Cancelled + Active           = Issued students
     *   Full Paid + Partial + Unpaid = Active   (active students with a live latest challan)
     *   Full Paid + Partial          = Total Paid
     */
    protected function runSummaryReportQuery(Request $request, ?string $studentType = null)
    {
        $session_condition = '';
        $school_condition = '';
        $dept_condition = '';
        $prog_condition = '';
        $studentTypeOption_condition = '';
        $admissionRound_condition = '';

        if (!empty($request->session_sel)) {
            $session_condition = "AND v.SC_SESSION_ID = '$request->session_sel'";
        }

        if (!empty($request->school_sel) && !is_array(json_decode($request->school_sel))) {
            $school_condition = "AND v.SC_SCHOOL_ID = '$request->school_sel'";
        }

        if (!empty($request->department) && !is_array(json_decode($request->department))) {
            $dept_condition = "AND v.SC_DEPARTMENT_ID = '$request->department'";
        }

        if (!empty($request->program) && !is_array(json_decode($request->program))) {
            $programs_id = $request->program;
            $array_of_sch_dep_prog = explode('~~', $programs_id);
            $prog_condition = "AND v.SC_PROGRAM_ID = '$array_of_sch_dep_prog[2]'";
        }

        if (!empty($request->sch_round)) {
            $safeRound = str_replace("'", "''", $request->sch_round);
            $admissionRound_condition = "AND LOWER(TRIM(v.SC_ADMISSION_ROUND)) = LOWER('{$safeRound}')";
        }

        if ($studentType !== null) {
            // A "..._withouReg" option is not a real SC_STUDENT_STATUS value: it
            // means the base status AND the student has no registration, i.e. no
            // CMS enrollment number on their admission record. Strip the suffix,
            // match on the base status, and add the no-enrollment check via
            // admission_apply_info.
            if (strpos($studentType, '_withouReg') !== false) {
                $baseStudentType = explode('_', $studentType)[0];
                $safeStudentType = str_replace("'", "''", $baseStudentType);
                $studentTypeOption_condition = "AND UPPER(TRIM(v.SC_STUDENT_STATUS)) = UPPER('{$safeStudentType}')
                        AND EXISTS (
                            SELECT 1
                            FROM admission_apply_info aai
                            WHERE aai.aai_id = v.SC_APP_ID
                              AND aai.cms_sai_enrollment_no IS NULL
                        )";
            } else {
                $safeStudentType = str_replace("'", "''", $studentType);
                $studentTypeOption_condition = "AND UPPER(TRIM(v.SC_STUDENT_STATUS)) = UPPER('{$safeStudentType}')";
            }
        }

        // Fee type must match getChallanReport() / challan-report summary:
        // Full_semester in the UI covers Full_semester + legacy Fee + Short/Summer_Course.
        $feeType = $request->feeType ?? null;
        if (!empty($feeType)) {
            $safeFeeType = str_replace("'", "''", $feeType);
            if ($safeFeeType === 'Full_semester') {
                $feeType_condition = "AND v.SC_CHALLAN_TYPE IN ('Full_semester', 'Fee', 'Short/Summer_Course')";
            } else {
                $feeType_condition = "AND v.SC_CHALLAN_TYPE = '{$safeFeeType}'";
            }
        } else {
            $feeType_condition = "AND v.SC_CHALLAN_TYPE IN ('Full_semester', 'Fee', 'Short/Summer_Course', 'Credit_hours', 'Hostel_Fee')";
        }

        // Student-status based classification.
        //   Active   = the STUDENT is active (students_apply_info.SAI_STUDENT_STATUS = 'Active').
        //              Cancel/reissue churn on their challans is irrelevant — as long as the
        //              student is active they count as Active.
        //   Cancelled = every other SAI status (Expected-Graduate, Defer, Left, Struck-off,
        //              Drop-out, Not Joined, ... and students with no student record → is_active 0).
        // The paid/unpaid buckets partition the ACTIVE students by what they have paid,
        // so Full Paid + Partial + Unpaid = Active exactly:
        //   Full Paid = paid something, nothing outstanding
        //   Partial   = paid something, still has an unpaid amount
        //   Unpaid    = paid nothing (their challan is unpaid OR cancelled-and-not-yet-reissued)
        $metricsSql = "
            COUNT(DISTINCT ps.SC_APP_ID) AS TOTAL_STUDENTS,
            COUNT(DISTINCT CASE WHEN NVL(ss.is_active, 0) = 0 THEN ps.SC_APP_ID END) AS CANCELLED,
            COUNT(DISTINCT CASE WHEN NVL(ss.is_active, 0) = 1 THEN ps.SC_APP_ID END) AS ACTIVE,
            COUNT(DISTINCT CASE WHEN NVL(ss.is_active, 0) = 1 AND ps.paid_cnt > 0 AND ps.unpaid_cnt = 0 THEN ps.SC_APP_ID END) AS FULLPAID,
            COUNT(DISTINCT CASE WHEN NVL(ss.is_active, 0) = 1 AND ps.paid_cnt > 0 AND ps.unpaid_cnt > 0 THEN ps.SC_APP_ID END) AS INSTALLMENT,
            COUNT(DISTINCT CASE WHEN NVL(ss.is_active, 0) = 1 AND ps.paid_cnt = 0 THEN ps.SC_APP_ID END) AS UNPAID,
            COUNT(DISTINCT CASE WHEN NVL(ss.is_active, 0) = 1 AND ps.paid_cnt > 0 THEN ps.SC_APP_ID END) AS TOTALPAID
        ";

        $viewLevel = $this->resolveSummaryViewLevel($request);

        // The per-student tally is grouped at the same granularity the report renders
        // at. If we always split by department/program, a student whose challans span
        // two programs would produce two rows in a School-Wise report and their paid/
        // unpaid tallies would be split, mis-bucketing them. Keeping the tally
        // granularity == the view level keeps each student a single row per group.
        if ($viewLevel === 'school') {
            $studentLevelCols = "SC_APP_ID,
                    SC_SESSION_ID,
                    SC_SCHOOL_ID";
            $latestLevelCols = "v.SC_APP_ID,
                        v.SC_SESSION_ID,
                        v.SC_SCHOOL_ID";
            $detailSelect = '';
            $detailJoins = '';
            $groupBySql = "ACD1.SESS_NAME,
                SCHOOL.SCHOOL_NAME,
                SCHOOL.SCHOOL_CODE,
                ps.SC_SCHOOL_ID";
            $orderBySql = "ACD1.SESS_NAME ASC,
                SCHOOL.SCHOOL_NAME ASC";
        } else {
            $studentLevelCols = "SC_APP_ID,
                    SC_SESSION_ID,
                    SC_SCHOOL_ID,
                    SC_DEPARTMENT_ID,
                    SC_PROGRAM_ID";
            $latestLevelCols = "v.SC_APP_ID,
                        v.SC_SESSION_ID,
                        v.SC_SCHOOL_ID,
                        v.SC_DEPARTMENT_ID,
                        v.SC_PROGRAM_ID";
            $detailSelect = "DEPT.DEPT_NAME,
                PROG.PROG_NAME,";
            $detailJoins = "LEFT JOIN DEPARTMENTS DEPT ON DEPT.DEPT_ID = ps.SC_DEPARTMENT_ID
            LEFT JOIN PROGRAMS PROG ON PROG.PROG_ID = ps.SC_PROGRAM_ID";
            $groupBySql = "ACD1.SESS_NAME,
                SCHOOL.SCHOOL_NAME,
                SCHOOL.SCHOOL_CODE,
                ps.SC_SCHOOL_ID,
                DEPT.DEPT_NAME,
                PROG.PROG_NAME";
            $orderBySql = "ACD1.SESS_NAME ASC,
                SCHOOL.SCHOOL_NAME ASC,
                DEPT.DEPT_NAME ASC,
                PROG.PROG_NAME ASC";
        }

        $sql = "SELECT
                ACD1.SESS_NAME,
                SCHOOL.SCHOOL_NAME,
                SCHOOL.SCHOOL_CODE,
                ps.SC_SCHOOL_ID,
                {$detailSelect}
                {$metricsSql}
            FROM (
                SELECT
                    {$studentLevelCols},
                    SUM(CASE WHEN UPPER(TRIM(SC_CHALLAN_STATUS)) = 'PAID'   THEN 1 ELSE 0 END) AS paid_cnt,
                    SUM(CASE WHEN UPPER(TRIM(SC_CHALLAN_STATUS)) = 'UNPAID' THEN 1 ELSE 0 END) AS unpaid_cnt
                FROM (
                    SELECT
                        {$latestLevelCols},
                        v.SC_CHALLAN_STATUS,
                        ROW_NUMBER() OVER (
                            PARTITION BY v.CHALLAN_NO
                            ORDER BY v.VERSION DESC, v.VER_ID DESC
                        ) AS rn
                    FROM AUD_BNUCMS.FIN_STUDENT_CHALLAN_VER v
                        WHERE 1=1
                        {$feeType_condition}
                        {$session_condition}
                        {$school_condition}
                        {$dept_condition}
                        {$prog_condition}
                        {$studentTypeOption_condition}
                        {$admissionRound_condition}
                ) latest
                WHERE latest.rn = 1
                GROUP BY
                    {$studentLevelCols}
            ) ps
            LEFT JOIN (
                SELECT
                    aai.aai_id AS app_id,
                    MAX(CASE WHEN UPPER(TRIM(sai.sai_student_status)) = 'ACTIVE' THEN 1 ELSE 0 END) AS is_active
                FROM admission_apply_info aai
                LEFT JOIN admission_personal_info api
                       ON api.api_id = aai.aai_api_id
                LEFT JOIN students_apply_info sai
                    ON sai.SAI_API_ID = api.API_ID --AND sai.sai_enrollment_no = aai.cms_sai_enrollment_no
                --LEFT JOIN students_personal_info spi
                       --ON spi.spi_id = sai.sai_spi_id
                GROUP BY aai.aai_id
            ) ss ON ss.app_id = ps.SC_APP_ID
            LEFT JOIN ACADEMIC_SESSIONS ACD1 ON ACD1.SESS_ID = ps.SC_SESSION_ID
            LEFT JOIN SCHOOLS SCHOOL ON SCHOOL.SCHOOL_ID = ps.SC_SCHOOL_ID
            {$detailJoins}
            GROUP BY
                {$groupBySql}
            ORDER BY
                {$orderBySql}
            ";
            // dd($sql);
        return $this->normalizeSummaryRows(DB::select($sql));
    }

    protected function fullSemSummaryRowKey($row, string $viewLevel = 'program')
    {
        $parts = [
            $this->fullSemSummaryField($row, 'sess_name'),
            $this->fullSemSummaryField($row, 'school_name'),
        ];

        if ($viewLevel === 'program') {
            $parts[] = $this->fullSemSummaryField($row, 'dept_name');
            $parts[] = $this->fullSemSummaryField($row, 'prog_name');
        }

        return implode('|', $parts);
    }

    protected function fullSemSummaryField($row, $field)
    {
        if (is_array($row)) {
            return $row[$field] ?? '';
        }

        return $row->$field ?? $row->{strtoupper($field)} ?? '';
    }

    protected function emptyGroupedSummaryRow($row, string $viewLevel = 'program')
    {
        $metrics = ['total_students', 'cancelled', 'active', 'fullpaid', 'installment', 'totalpaid', 'unpaid'];
        $prefixes = ['existing', 'fall', 'spring', 'consolidated'];
        $emptyRow = [
            'sess_name' => $this->fullSemSummaryField($row, 'sess_name'),
            'school_name' => $this->fullSemSummaryField($row, 'school_name'),
            'dept_name' => $viewLevel === 'program' ? $this->fullSemSummaryField($row, 'dept_name') : '',
            'prog_name' => $viewLevel === 'program' ? $this->fullSemSummaryField($row, 'prog_name') : '',
        ];

        foreach ($prefixes as $prefix) {
            foreach ($metrics as $metric) {
                $emptyRow[$prefix . '_' . $metric] = 0;
            }
        }

        return $emptyRow;
    }

    protected function applyMetricsToGroupedRow(array &$target, $source, $prefix)
    {
        $source = (array) $source;
        $metrics = ['total_students', 'cancelled', 'active', 'fullpaid', 'installment', 'totalpaid', 'unpaid'];

        foreach ($metrics as $metric) {
            $target[$prefix . '_' . $metric] = (int) ($source[$metric] ?? 0);
        }
    }

    protected function computeConsolidatedMetrics(array $row)
    {
        $metrics = ['total_students', 'cancelled', 'active', 'fullpaid', 'installment', 'totalpaid', 'unpaid'];
        $prefixes = ['existing', 'fall', 'spring'];

        foreach ($metrics as $metric) {
            $sum = 0;
            foreach ($prefixes as $prefix) {
                $sum += (int) ($row[$prefix . '_' . $metric] ?? 0);
            }
            $row['consolidated_' . $metric] = $sum;
        }

        return $row;
    }
}
