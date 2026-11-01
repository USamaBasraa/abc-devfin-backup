<?php

namespace App\Http\Controllers\StudentFinancial;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use DB;
use DataTables;
use App\Models\Grader\Schools;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;
class StudentChallanDetailsController extends Controller
{

    public function index(Request $request)
{
    $entryUserId = auth()->user()->id;
    $groupId = auth()->user()->group_id;

    if ($groupId == 644 || auth()->user()->username == 'asif_nadeem') {
        $query = DB::table('fine_category')->select('id', 'fc_category_name')->where('fc_status', 'E');
    } else {
        $query = DB::table('fine_category_permissions')
            ->leftJoin('fine_category', 'fine_category_permissions.fc_id', '=', 'fine_category.id')
            ->select('fine_category.id', 'fine_category.fc_category_name')
            ->where('FCP_STATUS', 'E')
            ->where('fine_category_permissions.fcp_allowed_user', $entryUserId);
    }

    $fine_categories = $query->get();

    return view('grader.StudentFines.masterForm', ['fine_categories' => $fine_categories]);
}

protected function getDefaultEnrolledStudentsSessionId(): int
{
    $sessionId = DB::table('ACADEMIC_SESSIONS')
        ->where('SESS_STATUS', 'E')
        ->orderByDesc('SESS_ID')
        ->value('SESS_ID');

    return (int) ($sessionId ?: 165);
}

protected function enrolledStudentsActiveChallanFilter(string $alias, int $sessionId): string
{
    return "
              AND {$alias}.sc_challan_type IN ('Credit_hours', 'Full_semester')
              AND {$alias}.sc_session_id = {$sessionId}
              AND NOT EXISTS (
                  SELECT 1
                  FROM fin_student_cancel_challan cancel_ch
                  WHERE cancel_ch.challan_no = {$alias}.challan_no
              )";
}

protected function normalizeEnrolledStudents(array $students): array
{
    return array_map(function ($row) {
        $r = (array) $row;

        return (object) [
            'student_id'           => $r['student_id'] ?? $r['STUDENT_ID'] ?? null,
            'student_reg'          => $r['student_reg'] ?? $r['STUDENT_REG'] ?? null,
            'school_name'          => $r['school_name'] ?? $r['SCHOOL_NAME'] ?? null,
            'prog_name'            => $r['prog_name'] ?? $r['PROG_NAME'] ?? null,
            'semester_no'          => $r['semester_no'] ?? $r['SEMESTER_NO'] ?? null,
            'no_of_enrolled_cr_hr' => $r['no_of_enrolled_cr_hr'] ?? $r['NO_OF_ENROLLED_CR_HR'] ?? 0,
            'remaining_cr_hr'      => $r['remaining_cr_hr'] ?? $r['REMAINING_CR_HR'] ?? 0,
            'gross_amount'         => $r['gross_amount'] ?? $r['GROSS_AMOUNT'] ?? 0,
            'scholarship_count'    => $r['scholarship_count'] ?? $r['SCHOLARSHIP_COUNT'] ?? null,
            'arrears'              => $r['arrears'] ?? $r['ARREARS'] ?? 0,
            'issue_date'           => $r['issue_date'] ?? $r['ISSUE_DATE'] ?? null,
            'sai_student_status'   => $r['sai_student_status'] ?? $r['SAI_STUDENT_STATUS'] ?? null,
        ];
    }, $students);
}

protected function buildEnrolledStudentsWhereClause(int $sessionId, ?Request $request = null): array
{
    $where = [];
    $bindings = [];

    if ($request !== null) {
        if ($request->filled('school')) {
            $where[] = 'sai.sai_sdpo_school_id = ?';
            $bindings[] = (int) $request->school;
        }

        if ($request->filled('department_id')) {
            $where[] = 'sai.sai_sdpo_dept_id = ?';
            $bindings[] = (int) $request->department_id;
        }

        if ($request->filled('program')) {
            $where[] = 'sai.sai_sdpo_prog_id = ?';
            $bindings[] = (int) $request->program;
        }

        if ($request->filled('semester_no')) {
            $where[] = "get_semester_no_cusome(sai.sai_enrollment_no, {$sessionId}) = ?";
            $bindings[] = trim($request->semester_no);
        }

        if ($request->filled('student_id')) {
            $where[] = '(CASE WHEN aai.aai_id IS NULL THEN sai.sai_id ELSE aai.aai_id END) = ?';
            $bindings[] = (int) $request->student_id;
        }

        if ($request->filled('registered_number')) {
            $where[] = 'UPPER(sai.sai_enrollment_no) LIKE ?';
            $bindings[] = '%' . strtoupper(trim($request->registered_number)) . '%';
        }

        if ($request->filled('from_date') && $request->filled('to_date')) {
            $where[] = "TRUNC(challan.issue_date) BETWEEN TO_DATE(?, 'YYYY-MM-DD') AND TO_DATE(?, 'YYYY-MM-DD')";
            $bindings[] = $request->from_date;
            $bindings[] = $request->to_date;
        } elseif ($request->filled('from_date')) {
            $where[] = "TRUNC(challan.issue_date) >= TO_DATE(?, 'YYYY-MM-DD')";
            $bindings[] = $request->from_date;
        } elseif ($request->filled('to_date')) {
            $where[] = "TRUNC(challan.issue_date) <= TO_DATE(?, 'YYYY-MM-DD')";
            $bindings[] = $request->to_date;
        }
    }

    return [$where, $bindings];
}

protected function fetchEnrolledStudents(int $sessionId, array $where, array $bindings): array
{
    $challanSessionFilter = $this->enrolledStudentsActiveChallanFilter('ch', $sessionId);
    $whereSql = count($where) > 0 ? 'WHERE ' . implode(' AND ', $where) : '';

    $sql = "
        WITH course_agg AS (
            SELECT
                sc.sc_sai_enrollment_no AS enrollment_no,
                SUM(sc.sc_pc_course_cr) AS no_of_enrolled_cr_hr
            FROM student_courses sc
            WHERE sc.sc_co_sess_id = ?
              AND sc.sc_enrollment_status = 1
            GROUP BY sc.sc_sai_enrollment_no
            HAVING SUM(sc.sc_pc_course_cr) > 0
        ),

        challan_agg AS (
            SELECT
                ch.sc_enrollment_no AS enrollment_no,
                SUM(ch.sc_tuition_fee_amnt) AS gross_amount,
                SUM(ch.sc_ssd_arrear) AS arrears,
                MAX(ch.sc_issu_date) AS issue_date
            FROM student_challan ch
            WHERE 1 = 1
              {$challanSessionFilter}
            GROUP BY ch.sc_enrollment_no
        ),

        scholarship_labels AS (
            SELECT DISTINCT
                ch.sc_enrollment_no AS enrollment_no,
                CASE
                    WHEN NVL(fsd.fsd_discount_percentage, 0) > 0 THEN
                        st.scholarship_name || ' (' ||
                        CASE
                            WHEN fsd.fsd_discount_percentage <= 1
                                THEN TO_CHAR(ROUND(fsd.fsd_discount_percentage * 100))
                            ELSE TO_CHAR(ROUND(fsd.fsd_discount_percentage))
                        END || '%)'
                    ELSE st.scholarship_name
                END AS scholarship_label
            FROM student_challan ch
            INNER JOIN fin_scholarship_display_history fsd
                ON (fsd.challan_id = ch.id OR fsd.challan_id = ch.challan_no)
            INNER JOIN scholarship_type st
                ON st.scholarship_id = fsd.fsd_scholarship_type
            WHERE 1 = 1
              {$challanSessionFilter}
              AND fsd.fsd_sesion_id = ?
              AND fsd.fsd_sch_type IS NULL
              AND (
                  NVL(fsd.fsd_discounted_amount, 0) > 0
                  OR NVL(fsd.fsd_sch_instal_amnt, 0) > 0
              )
        ),

        scholarship_agg AS (
            SELECT
                enrollment_no,
                LISTAGG(scholarship_label, ', ') WITHIN GROUP (ORDER BY scholarship_label) AS scholarship_count
            FROM scholarship_labels
            GROUP BY enrollment_no
        )

        SELECT
            CASE WHEN aai.aai_id IS NULL THEN sai.sai_id ELSE aai.aai_id END AS student_id,
            sai.sai_enrollment_no AS student_reg,
            sch.school_name,
            prog.prog_name,
            get_semester_no_cusome(sai.sai_enrollment_no, {$sessionId}) AS semester_no,
            NVL(course.no_of_enrolled_cr_hr, 0) AS no_of_enrolled_cr_hr,
            NVL(sai.sai_prog_total_credits, 0) - NVL(sai.sai_earn_credit, 0) AS remaining_cr_hr,
            NVL(challan.gross_amount, 0) AS gross_amount,
            scholarship.scholarship_count,
            NVL(challan.arrears, 0) AS arrears,
            TO_CHAR(challan.issue_date, 'DD-MON-RR', 'NLS_DATE_LANGUAGE=ENGLISH') AS issue_date,
            sai.sai_student_status AS sai_student_status

        FROM students_apply_info sai

        INNER JOIN course_agg course
            ON course.enrollment_no = sai.sai_enrollment_no

        LEFT JOIN admission_apply_info aai
            ON aai.cms_sai_enrollment_no = sai.sai_enrollment_no

        LEFT JOIN schools sch
            ON sch.school_id = sai.sai_sdpo_school_id

        LEFT JOIN programs prog
            ON prog.prog_id = sai.sai_sdpo_prog_id

        LEFT JOIN challan_agg challan
            ON challan.enrollment_no = sai.sai_enrollment_no

        LEFT JOIN scholarship_agg scholarship
            ON scholarship.enrollment_no = sai.sai_enrollment_no

        {$whereSql}

        ORDER BY
            sch.school_name,
            prog.prog_name,
            sai.sai_enrollment_no
    ";

    $allBindings = array_merge([$sessionId, $sessionId], $bindings);

    return DB::select($sql, $allBindings);
}

public function enrolledStudents()
{
    $sessionId = $this->getDefaultEnrolledStudentsSessionId();

    [$where, $bindings] = $this->buildEnrolledStudentsWhereClause($sessionId);
    $students = $this->normalizeEnrolledStudents(
        $this->fetchEnrolledStudents($sessionId, $where, $bindings)
    );

    $schools = DB::table('schools')
    ->select('school_id', 'school_name')
    ->where('school_status', 'E')
    ->orderBy('school_id')
    ->get();

    $programs = DB::table('programs')
        ->select('prog_id', 'prog_name')
        ->orderBy('prog_name')
        ->get();

    $semesters = DB::table('students_apply_info')
    ->selectRaw('DISTINCT get_semester_no(sai_enrollment_no) AS semester_no')
    ->where('sai_student_status', 'Active')
    ->whereNotNull('sai_enrollment_no')
    ->orderBy('semester_no')
    ->pluck('semester_no');



   $sessions =  DB::table('ACADEMIC_SESSIONS')
    ->select('SESS_ID as sess_id', 'SESS_NAME as sess_name')
    ->where('SESS_STATUS', 'E')
    ->orderBy('SESS_ID', 'desc')
    ->get();

    // dd($students);


      return view('StudentFinancial.enrolled_students', compact(
        'students',
        'schools',
        'programs',
        'semesters',
        'sessions',
        'sessionId'
    ));
}


public function agingReport()
{
      $schools = DB::table('schools')
    ->select('school_id', 'school_name')
    ->where('school_status', 'E')
    ->orderBy('school_id')
    ->get();

       $sessions =  DB::table('ACADEMIC_SESSIONS')
    ->select('SESS_ID as sess_id', 'SESS_NAME as sess_name')
    ->where('SESS_STATUS', 'E')
    ->orderBy('SESS_ID', 'desc')
    ->get();

    //   $feeTypes = DB::table('student_challan')
    //     ->select('sc_challan_type')
    //     ->whereNotNull('sc_challan_type')
    //     ->distinct()
    //     ->orderBy('sc_challan_type')
    //     ->get();

    $scholarshipTypes = DB::table('scholarship_type')
        ->select('scholarship_id', 'scholarship_name')
        ->orderBy('scholarship_name')
        ->get();

      return view('StudentFinancial.aging_report', compact(  'schools',
        'sessions',
        'scholarshipTypes'));
}

public function agingReportData(Request $request)
{
    try {
        $draw = (int) $request->input('draw', 1);
        $start = max((int) $request->input('start', 0), 0);
        $length = (int) $request->input('length', 10);
        $search = trim((string) $request->input('search.value', ''));

        $baseQuery = $this->getAgingReportBaseQuery();
        [$where, $bindings] = $this->buildAgingReportFilters($request, $search);

        $totalRecords = DB::selectOne("
            SELECT COUNT(*) AS total
            $baseQuery
        ")->total ?? 0;

        $filteredRecords = DB::selectOne("
            SELECT COUNT(*) AS total
            $baseQuery
            $where
        ", $bindings)->total ?? 0;

        if ($request->boolean('export_all')) {
            $start = 0;
            $length = max((int) $filteredRecords, 0);
        } elseif ($request->boolean('for_export')) {
            // Chunked full export (Excel/PDF): allow larger pages than UI pagination.
            if ($length < 1 || $length > 5000) {
                $length = 5000;
            }
        } elseif ($length < 1 || $length > 100) {
            $length = 10;
        }

        $orderBy = $this->buildAgingReportOrderBy($request);

        $sql = "
            SELECT
                student_id,
                challan_no,
                issue_date,
                due_date,
                student_reg,
                school,
                sess_code,
                fee_type,
                receivable_amount,
                challan_status,
                aging
            FROM (
                SELECT q.*, ROWNUM rnum
                FROM (
                    {$this->getAgingReportInnerSelectSql($this->resolveAgingReportScholarshipId($request))}
                    $baseQuery
                    $where

                    ORDER BY {$orderBy}
                ) q
                WHERE ROWNUM <= :end_row
            )
            WHERE rnum > :start_row
        ";

        $bindings['start_row'] = $start;
        $bindings['end_row'] = $start + $length;

        $data = DB::select($sql, $bindings);

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => (int) $totalRecords,
            'recordsFiltered' => (int) $filteredRecords,
            'data' => $data,
        ]);
    } catch (\Throwable $e) {
        Log::error('Aging Report Data Error', [
            'message' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => $e->getFile(),
        ]);

        return response()->json([
            'draw' => (int) $request->input('draw', 1),
            'recordsTotal' => 0,
            'recordsFiltered' => 0,
            'data' => [],
            'error' => 'Aging report data load nahi ho raha.',
        ], 500);
    }
}

public function filterAgingReport(Request $request)
{
    $baseQuery = $this->getAgingReportBaseQuery();
    [$where, $bindings] = $this->buildAgingReportFilters($request);

    $challans = DB::select("
        SELECT
            student_id,
            challan_no,
            issue_date,
            due_date,
            student_reg,
            school,
            sess_code,
            fee_type,
            receivable_amount,
            challan_status,
            aging
        FROM (
            {$this->getAgingReportInnerSelectSql($this->resolveAgingReportScholarshipId($request))}
            $baseQuery
            $where
            ORDER BY sc.sc_issu_date DESC, sc.challan_no DESC
        )
    ", $bindings);

    return response()->json([
        'status' => true,
        'data' => $challans
    ]);
}

public function exportAgingReport(Request $request)
{
    try {
        $search = trim((string) $request->input('search', $request->input('search.value', '')));
        $baseQuery = $this->getAgingReportBaseQuery();
        [$where, $bindings] = $this->buildAgingReportFilters($request, $search);

        $filename = 'aging-report-' . date('Y-m-d-His') . '.csv';
        $scholarshipId = $this->resolveAgingReportScholarshipId($request);

        return response()->streamDownload(function () use ($baseQuery, $where, $bindings, $scholarshipId) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'Student ID',
                'Challan #',
                'Issue Date',
                'Due Date',
                'Student Reg',
                'School',
                'Session',
                'Fee Type',
                'Receivable Amount',
                'Challan Status',
                'Aging',
            ]);

            $chunkSize = 5000;
            $offset = 0;

            while (true) {
                $sql = "
                    SELECT
                        student_id,
                        challan_no,
                        issue_date,
                        due_date,
                        student_reg,
                        school,
                        sess_code,
                        fee_type,
                        receivable_amount,
                        challan_status,
                        aging
                    FROM (
                        SELECT q.*, ROWNUM rnum
                        FROM (
                            {$this->getAgingReportInnerSelectSql($scholarshipId)}
                            $baseQuery
                            $where
                            ORDER BY sc.sc_issu_date DESC, sc.challan_no DESC
                        ) q
                        WHERE ROWNUM <= :end_row
                    )
                    WHERE rnum > :start_row
                ";

                $chunkBindings = array_merge($bindings, [
                    'start_row' => $offset,
                    'end_row' => $offset + $chunkSize,
                ]);

                $rows = DB::select($sql, $chunkBindings);

                if (empty($rows)) {
                    break;
                }

                foreach ($rows as $row) {
                    fputcsv($handle, [
                        $row->student_id,
                        $row->challan_no,
                        $row->issue_date,
                        $row->due_date,
                        $row->student_reg,
                        $row->school,
                        $row->sess_code,
                        $row->fee_type,
                        $row->receivable_amount,
                        $row->challan_status,
                        $row->aging,
                    ]);
                }

                if (count($rows) < $chunkSize) {
                    break;
                }

                $offset += $chunkSize;
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    } catch (\Throwable $e) {
        \Log::error('Aging Report Export Error', [
            'message' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => $e->getFile(),
        ]);

        return response()->json([
            'status' => false,
            'message' => 'Aging report export failed.',
        ], 500);
    }
}

public function exportAgingReportData(Request $request)
{
    try {
        $search = trim((string) $request->input('search', $request->input('search.value', '')));
        $baseQuery = $this->getAgingReportBaseQuery();
        [$where, $bindings] = $this->buildAgingReportFilters($request, $search);

        $chunkSize = 5000;
        $offset = 0;
        $allRows = [];
        $scholarshipId = $this->resolveAgingReportScholarshipId($request);

        while (true) {
            $sql = "
                SELECT
                    student_id,
                    challan_no,
                    issue_date,
                    due_date,
                    student_reg,
                    school,
                    sess_code,
                    fee_type,
                    receivable_amount,
                    challan_status,
                    aging
                FROM (
                    SELECT q.*, ROWNUM rnum
                    FROM (
                        {$this->getAgingReportInnerSelectSql($scholarshipId)}
                        $baseQuery
                        $where
                        ORDER BY sc.sc_issu_date DESC, sc.challan_no DESC
                    ) q
                    WHERE ROWNUM <= :end_row
                )
                WHERE rnum > :start_row
            ";

            $chunkBindings = array_merge($bindings, [
                'start_row' => $offset,
                'end_row' => $offset + $chunkSize,
            ]);

            $rows = DB::select($sql, $chunkBindings);

            if (empty($rows)) {
                break;
            }

            foreach ($rows as $row) {
                $allRows[] = $row;
            }

            if (count($rows) < $chunkSize) {
                break;
            }

            $offset += $chunkSize;
        }

        return response()->json([
            'status' => true,
            'data' => $this->normalizeAgingReportRows($allRows),
        ]);
    } catch (\Throwable $e) {
        Log::error('Aging Report Export Data Error', [
            'message' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => $e->getFile(),
        ]);

        return response()->json([
            'status' => false,
            'message' => 'Unable to fetch aging report records for export.',
        ], 500);
    }
}

private function normalizeAgingReportRows(array $rows)
{
    return array_map(function ($row) {
        return [
            'student_id' => $row->student_id ?? null,
            'challan_no' => $row->challan_no ?? null,
            'issue_date' => $row->issue_date ?? null,
            'due_date' => $row->due_date ?? null,
            'student_reg' => $row->student_reg ?? null,
            'school' => $row->school ?? null,
            'sess_code' => $row->sess_code ?? null,
            'fee_type' => $row->fee_type ?? null,
            'receivable_amount' => $row->receivable_amount ?? null,
            'challan_status' => $row->challan_status ?? null,
            'aging' => $row->aging ?? null,
        ];
    }, $rows);
}

private function getAgingReportDefaultFeeTypes()
{
    return [
        'Full_semester',
        'Short/Summer_Course',
        'Credit_hours',
        'Hostel_Fee',
        'Fine',
        'Convocation_Fee',
        'misc_fee',
    ];
}

private function getAgingReportBaseQuery()
{
    $feeTypes = array_map(function ($feeType) {
        return "'" . str_replace("'", "''", $feeType) . "'";
    }, $this->getAgingReportDefaultFeeTypes());

    $feeTypeList = implode(', ', $feeTypes);

    return "
        FROM student_challan sc

        LEFT JOIN students_apply_info sai
               ON sai.sai_enrollment_no = sc.sc_enrollment_no

        LEFT JOIN schools sch
               ON sch.school_id = sc.sc_school_id

        LEFT JOIN academic_sessions acs
               ON acs.sess_id = sc.sc_session_id

        WHERE sc.invoice_status = 'E'
          AND UPPER(sc.sc_challan_status) = 'UNPAID'
          AND sc.sc_session_id >= 157
          AND sc.sc_challan_type IN ($feeTypeList)
    ";
}

private function resolveAgingReportScholarshipId(Request $request): ?int
{
    if (!$request->filled('scholarship_type')) {
        return null;
    }

    $scholarshipId = (int) $request->scholarship_type;

    return $scholarshipId > 0 ? $scholarshipId : null;
}

/**
 * Selected scholarship type amount for this unpaid challan.
 * Falls back to challan receivable when no scholarship filter is applied.
 */
private function getAgingReportReceivableAmountSql(?int $scholarshipId = null): string
{
    if ($scholarshipId) {
        return "
            NVL((
                SELECT SUM(
                    CASE
                        WHEN fsd.fsd_sch_instal_amnt IS NULL
                            THEN NVL(fsd.fsd_discounted_amount, 0)
                        ELSE NVL(ROUND(fsd.fsd_sch_instal_amnt), 0)
                    END
                )
                FROM fin_scholarship_display_history fsd
                WHERE (fsd.challan_id = sc.id OR fsd.challan_id = sc.challan_no)
                  AND fsd.fsd_sesion_id = sc.sc_session_id
                  AND (
                      fsd.fsd_aai_id = sc.sc_app_id
                      OR UPPER(NVL(fsd.fsd_enrollment_no, '')) = UPPER(NVL(sc.sc_enrollment_no, ''))
                  )
                  AND fsd.fsd_scholarship_type = {$scholarshipId}
                  AND (
                      NVL(fsd.fsd_discounted_amount, 0) > 0
                      OR NVL(fsd.fsd_sch_instal_amnt, 0) > 0
                  )
            ), 0)
        ";
    }

    return "
        NVL(
            TO_NUMBER(NULLIF(REGEXP_REPLACE(TO_CHAR(sc.invoiceamount), '[^0-9.]', ''), '')),
            TO_NUMBER(NULLIF(REGEXP_REPLACE(TO_CHAR(sc.sc_amount), '[^0-9.]', ''), ''))
        )
    ";
}

private function getAgingReportInnerSelectSql(?int $scholarshipId = null)
{
    $receivableAmountSql = $this->getAgingReportReceivableAmountSql($scholarshipId);

    return "
        SELECT
               sc.sc_app_id AS student_id,
               sc.challan_no AS challan_no,
               TO_CHAR(sc.sc_issu_date, 'DD-MON-YYYY') AS issue_date,
               TO_CHAR(sc.sc_due_date, 'DD-MON-YYYY') AS due_date,
               sc.sc_enrollment_no AS student_reg,
               NVL(sch.school_name, '-') AS school,
               NVL(acs.sess_code, '-') AS sess_code,
               sc.sc_challan_type AS fee_type,

               TO_CHAR(
                   {$receivableAmountSql},
                   'FM999G999G999G999'
               ) AS receivable_amount,

               sc.sc_challan_status AS challan_status,

               CASE
                   WHEN sc.sc_due_date IS NULL THEN NULL
                   WHEN UPPER(sc.sc_challan_status) = 'PAID' THEN 0
                   ELSE TRUNC(SYSDATE) - TRUNC(sc.sc_due_date)
               END AS aging
    ";
}

private function buildAgingReportFilters(Request $request, $search = '')
{
    $where = "";
    $bindings = [];

    if ($request->filled('from_date')) {
        $where .= " AND TRUNC(sc.CREATED_AT) >= TO_DATE(:from_date, 'YYYY-MM-DD') ";
        $bindings['from_date'] = $request->from_date;
    }

    if ($request->filled('to_date')) {
        $where .= " AND TRUNC(sc.CREATED_AT) <= TO_DATE(:to_date, 'YYYY-MM-DD') ";
        $bindings['to_date'] = $request->to_date;
    }

    if ($request->filled('session')) {
        $where .= " AND sc.sc_session_id = :session_id ";
        $bindings['session_id'] = $request->session;
    }

    if ($request->filled('school')) {
        $where .= " AND sc.sc_school_id = :school_id ";
        $bindings['school_id'] = $request->school;
    }

    if ($request->filled('fee_type')) {
        $where .= " AND sc.sc_challan_type = :fee_type ";
        $bindings['fee_type'] = $request->fee_type;
    }

    if ($request->filled('scholarship_type')) {
        // Only unpaid challans that actually reflect a scholarship amount on the
        // challan itself AND had the selected scholarship type applied to them.
        $where .= "
            AND NVL(sc.sc_scholarship_amount, 0) > 0
            AND EXISTS (
                SELECT 1
                FROM fin_scholarship_display_history fsd
                WHERE (fsd.challan_id = sc.id OR fsd.challan_id = sc.challan_no)
                  AND fsd.fsd_sesion_id = sc.sc_session_id
                  AND (
                      fsd.fsd_aai_id = sc.sc_app_id
                      OR UPPER(NVL(fsd.fsd_enrollment_no, '')) = UPPER(NVL(sc.sc_enrollment_no, ''))
                  )
                  AND fsd.fsd_scholarship_type = :scholarship_id
                  AND (
                      NVL(fsd.fsd_discounted_amount, 0) > 0
                      OR NVL(fsd.fsd_sch_instal_amnt, 0) > 0
                  )
            )
        ";

        $bindings['scholarship_id'] = (int) $request->scholarship_type;
    }

    $search = trim((string) $search);

    if ($search !== '') {
        $where .= "
            AND (
                LOWER(TO_CHAR(sc.sc_app_id)) LIKE LOWER(:search)
                OR LOWER(TO_CHAR(sc.challan_no)) LIKE LOWER(:search)
                OR LOWER(sc.sc_enrollment_no) LIKE LOWER(:search)
                OR LOWER(sch.school_name) LIKE LOWER(:search)
                OR LOWER(acs.sess_code) LIKE LOWER(:search)
                OR LOWER(TO_CHAR(sc.sc_session_id)) LIKE LOWER(:search)
                OR LOWER(sc.sc_challan_type) LIKE LOWER(:search)
                OR LOWER(sc.sc_challan_status) LIKE LOWER(:search)
            )
        ";

        $bindings['search'] = '%' . $search . '%';
    }

    return [$where, $bindings];
}

private function buildAgingReportOrderBy(Request $request): string
{
    $receivableAmountSql = $this->getAgingReportReceivableAmountSql(
        $this->resolveAgingReportScholarshipId($request)
    );

    $agingSql = "CASE
        WHEN sc.sc_due_date IS NULL THEN NULL
        WHEN UPPER(sc.sc_challan_status) = 'PAID' THEN 0
        ELSE TRUNC(SYSDATE) - TRUNC(sc.sc_due_date)
    END";

    $orderableColumns = [
        0 => 'sc.sc_app_id',
        1 => 'sc.challan_no',
        2 => 'sc.sc_issu_date',
        3 => 'sc.sc_due_date',
        4 => 'sc.sc_enrollment_no',
        5 => 'sch.school_name',
        6 => 'acs.sess_code',
        7 => 'sc.sc_challan_type',
        8 => $receivableAmountSql,
        9 => 'sc.sc_challan_status',
        10 => $agingSql,
    ];

    $orders = $request->input('order', []);
    $columns = $request->input('columns', []);
    $orderParts = [];

    foreach ($orders as $order) {
        $columnIndex = (int) ($order['column'] ?? 0);
        $direction = strtolower($order['dir'] ?? 'asc') === 'desc' ? 'DESC' : 'ASC';

        if (!isset($orderableColumns[$columnIndex])) {
            continue;
        }

        if (isset($columns[$columnIndex]['orderable']) && $columns[$columnIndex]['orderable'] === 'false') {
            continue;
        }

        $orderParts[] = $orderableColumns[$columnIndex] . ' ' . $direction . ' NULLS LAST';
    }

    if (empty($orderParts)) {
        return 'sc.sc_issu_date DESC NULLS LAST, sc.challan_no DESC NULLS LAST';
    }

    return implode(', ', $orderParts);
}

public function feeGlobalSummary()
{
    $sessions = \App::call('App\Http\Controllers\Grader\AcademicSessionController@retrieve');
        $schools =  DB::table('schools')
                        ->select('school_id', 'school_name', 'school_code')
                        ->where('school_status', 'E')
                        ->where('school_id', '!=', 11)//management
                        ->get();
    // dd($sessions);
      return view('StudentFinancial.fee_global_summary', compact('sessions', 'schools'));
}

public function getFeeGlobalSummaryData(Request $request)
{
    // Filters for the active (student_challan alias sc) and cancelled
    // (fin_student_cancel_challan alias c) branches of the all_challans CTE.
    $activeWhere = '';
    $cancelWhere = '';
    // Applied after the per-student dedup, against the resolved school id.
    $schoolFilter = '';

    if ($request->filled('session')) {
        $session = (int) $request->session;
        $activeWhere .= " AND sc.sc_session_id = $session";
        $cancelWhere .= " AND c.sc_session_id = $session";
    }

    if ($request->filled('from_date')) {
        $activeWhere .= " AND TRUNC(sc.created_at) >= TO_DATE('$request->from_date', 'YYYY-MM-DD') ";
        $cancelWhere .= " AND TRUNC(c.created_at) >= TO_DATE('$request->from_date', 'YYYY-MM-DD') ";
    }

    if ($request->filled('to_date')) {
        $activeWhere .= " AND TRUNC(sc.created_at) <= TO_DATE('$request->to_date', 'YYYY-MM-DD') ";
        $cancelWhere .= " AND TRUNC(c.created_at) <= TO_DATE('$request->to_date', 'YYYY-MM-DD') ";
    }

    if ($request->filled('fee_type')) {
        $feeType = str_replace("'", "''", $request->fee_type);
        $activeWhere .= " AND sc.sc_challan_type = '$feeType'";
        $cancelWhere .= " AND c.sc_challan_type = '$feeType'";
    } else {
        $activeWhere .= " AND sc.sc_challan_type IN (
            'Full_semester',
            'Short/Summer_Course',
            'Credit_hours'
        ) ";
        $cancelWhere .= " AND c.sc_challan_type IN (
            'Full_semester',
            'Short/Summer_Course',
            'Credit_hours'
        ) ";
    }

    if ($request->filled('school')) {
        $school = (int) $request->school;
        $schoolFilter = " WHERE NVL(sw.sc_school_id, aai.aai_sdpo_school_id) = $school";
    }

    // Every challan (live + cancelled) is unioned, then collapsed to ONE row per
    // student (sc_app_id + session + challan_type) via GROUP BY. MAX() on each fee
    // field de-dups cancelled-and-regenerated versions of the same student's
    // challan, so nothing is double counted.
    // Reported columns: admission + activity + medical + tuition, plus the
    // scholarship amount, and Total = those four fees MINUS the scholarship.
    $sql = "
WITH all_challans AS (
    /* Active challans */
    SELECT
        sc.sc_app_id,
        sc.sc_enrollment_no,
        sc.sc_session_id,
        sc.sc_challan_type,
        sc.sc_school_id,
        sc.challan_no,
        sc.created_at,
        NVL(sc.ffs_admission_amt, 0) AS admission_fee,
        NVL(sc.sc_activity_amt, 0) AS activity_charges,
        NVL(sc.ffs_medical_amt, 0) AS medical_insurance,
        NVL(sc.ffs_security_amt, 0) AS security_fee,
        NVL(sc.sc_installment_tuition_fee_amnt, NVL(sc.sc_tuition_fee_amnt, 0)) AS tuition_fee,
        NVL(sc.ffs_hostel_amt, 0) AS hostel_fee,
        NVL(sc.ffs_credhr_amt, 0) AS credit_hour_fee,
        NVL(sc.fine, 0) AS fine_amount,
        NVL(sc.sc_discount, 0) AS discount_amount,
        NVL(sc.sc_scholarship_amount, 0) AS scholarship_amount,
        NVL(sc.sc_discount_registration, 0) AS registration_discount,
        NVL(sc.sc_surplus_amount, 0) AS surplus_amount,
        NVL(sc.sc_ssd_arrear, 0) AS ssd_arrear,
        'ACTIVE' AS record_source
    FROM student_challan sc
    WHERE 1 = 1
        $activeWhere

    UNION ALL

    /* Cancelled challans */
    SELECT
        c.sc_app_id,
        c.sc_enrollment_no,
        c.sc_session_id,
        c.sc_challan_type,
        c.sc_school_id,
        c.challan_no,
        c.created_at,
        NVL(c.ffs_admission_amt, 0) AS admission_fee,
        NVL(c.sc_activity_amt, 0) AS activity_charges,
        NVL(c.ffs_medical_amt, 0) AS medical_insurance,
        NVL(c.ffs_security_amt, 0) AS security_fee,
        NVL(c.sc_installment_tuition_fee_amnt, NVL(c.sc_tuition_fee_amnt, 0)) AS tuition_fee,
        NVL(c.ffs_hostel_amt, 0) AS hostel_fee,
        NVL(c.ffs_credhr_amt, 0) AS credit_hour_fee,
        NVL(c.fine, 0) AS fine_amount,
        0 AS discount_amount,
        NVL(c.sc_scholarship_amount, 0) AS scholarship_amount,
        0 AS registration_discount,
        NVL(c.sc_surplus_amount, 0) AS surplus_amount,
        0 AS ssd_arrear,
        'CANCELLED' AS record_source
    FROM fin_student_cancel_challan c
    WHERE 1 = 1
        $cancelWhere
),
student_wise AS (
    SELECT
        ac.sc_app_id,
        MAX(ac.sc_enrollment_no) AS sc_enrollment_no,
        ac.sc_session_id,
        ac.sc_challan_type,
        MAX(ac.sc_school_id) KEEP (DENSE_RANK LAST ORDER BY ac.created_at, ac.challan_no) AS sc_school_id,
        COUNT(*) AS total_challan_versions,
        MAX(ac.admission_fee) AS admission_fee,
        MAX(ac.activity_charges) AS activity_charges,
        MAX(ac.medical_insurance) AS medical_insurance,
        MAX(ac.security_fee) AS security_fee,
        MAX(ac.tuition_fee) AS tuition_fee,
        MAX(ac.hostel_fee) AS hostel_fee,
        MAX(ac.credit_hour_fee) AS credit_hour_fee,
        MAX(ac.fine_amount) AS fine_amount,
        MAX(ac.discount_amount) AS discount_amount,
        MAX(ac.scholarship_amount) AS scholarship_amount,
        MAX(ac.registration_discount) AS registration_discount,
        MAX(ac.surplus_amount) AS surplus_amount,
        MAX(ac.ssd_arrear) AS ssd_arrear
    FROM all_challans ac
    GROUP BY
        ac.sc_app_id,
        ac.sc_session_id,
        ac.sc_challan_type
),
student_with_school AS (
    SELECT
        sw.*,
        NVL(s.school_code, 'NO SCHOOL') AS school_code
    FROM student_wise sw
    LEFT JOIN admission_apply_info aai
           ON aai.aai_id = sw.sc_app_id
    LEFT JOIN schools s
           ON s.school_id = NVL(sw.sc_school_id, aai.aai_sdpo_school_id)
    $schoolFilter
)
SELECT
    school_code AS schoold,
    SUM(admission_fee) AS admission_fee,
    SUM(activity_charges) AS activity_charges,
    SUM(medical_insurance) AS medical_insurance,
    SUM(tuition_fee) AS tuition_fee,
    SUM(scholarship_amount) AS scholarship_amount,
    SUM(
          admission_fee
        + activity_charges
        + medical_insurance
        + tuition_fee
        - scholarship_amount
    ) AS total
FROM student_with_school
GROUP BY school_code
ORDER BY school_code
    ";
    try {
        $data = DB::select($sql);

        $data = array_map(function ($row) {
            $row = (array) $row;

            return [
                'schoold' => $row['schoold'] ?? $row['SCHOOLD'] ?? '',
                'admission_fee' => (float) ($row['admission_fee'] ?? $row['ADMISSION_FEE'] ?? 0),
                'activity_charges' => (float) ($row['activity_charges'] ?? $row['ACTIVITY_CHARGES'] ?? 0),
                'medical_insurance' => (float) ($row['medical_insurance'] ?? $row['MEDICAL_INSURANCE'] ?? 0),
                'tuition_fee' => (float) ($row['tuition_fee'] ?? $row['TUITION_FEE'] ?? 0),
                'scholarship_amount' => (float) ($row['scholarship_amount'] ?? $row['SCHOLARSHIP_AMOUNT'] ?? 0),
                'total' => (float) ($row['total'] ?? $row['TOTAL'] ?? 0),
            ];
        }, $data);

        return response()->json([
            'status' => true,
            'data' => $data,
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => 'Unable to load fee global summary.',
            'data' => [],
            'error' => $e->getMessage(),
        ], 500);
    }
}

public function filterEnrolledStudents(Request $request)
{
    $validator = Validator::make($request->all(), [
        'from_date'         => ['nullable', 'date_format:Y-m-d'],
        'to_date'           => ['nullable', 'date_format:Y-m-d'],
        'school'            => ['nullable', 'integer'],
        'department_id'     => ['nullable', 'integer'],
        'program'           => ['nullable', 'integer'],
        'semester_no'       => ['nullable', 'string', 'max:100'],
        'session'           => ['nullable', 'integer'],
        'student_id'        => ['nullable', 'integer'],
        'registered_number' => ['nullable', 'string', 'max:100'],
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status'  => false,
            'message' => 'Invalid filter data.',
            'errors'  => $validator->errors(),
        ], 422);
    }

    $sessionId = $request->filled('session')
        ? (int) $request->session
        : $this->getDefaultEnrolledStudentsSessionId();

    [$where, $bindings] = $this->buildEnrolledStudentsWhereClause($sessionId, $request);

    try {
        $students = $this->normalizeEnrolledStudents(
            $this->fetchEnrolledStudents($sessionId, $where, $bindings)
        );

        return response()->json([
            'status' => true,
            'data'   => $students,
        ]);
    } catch (\Throwable $e) {
        report($e);

        return response()->json([
            'status'  => false,
            'message' => 'Data filter nahi ho raha.',
        ], 500);
    }
}

public function filterEnrolledStudents02062026(Request $request)
{
    $validator = Validator::make($request->all(), [
        'from_date'         => ['nullable', 'date_format:Y-m-d'],
        'to_date'           => ['nullable', 'date_format:Y-m-d'],
        'school'            => ['nullable', 'integer'],
        'department_id'     => ['nullable', 'integer'],
        'program'           => ['nullable', 'integer'],
        'semester_no'       => ['nullable', 'string', 'max:100'],
        'session'           => ['nullable', 'integer'],
        'student_id'        => ['nullable', 'integer'],
        'registered_number' => ['nullable', 'string', 'max:100'],
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status'  => false,
            'message' => 'Invalid filter data.',
            'errors'  => $validator->errors(),
        ], 422);
    }

    $sessionId = (int) ($request->session ?: 165);

    $where = [];
    $bindings = [];

    /*
     * Bindings for CTEs:
     * 1. student_courses session
     * 2. student_challan session
     * 3. scholarship session
     */
    $bindings[] = $sessionId;
    $bindings[] = $sessionId;
    $bindings[] = $sessionId;

    $where[] = "sai.sai_student_status = ?";
    $bindings[] = 'Active';

    if ($request->filled('school')) {
        $where[] = "sai.sai_sdpo_school_id = ?";
        $bindings[] = (int) $request->school;
    }

    if ($request->filled('department_id')) {
        $where[] = "sai.sai_sdpo_dept_id = ?";
        $bindings[] = (int) $request->department_id;
    }

    if ($request->filled('program')) {
        $where[] = "sai.sai_sdpo_prog_id = ?";
        $bindings[] = (int) $request->program;
    }

    if ($request->filled('semester_no')) {
        $where[] = "get_semester_no(sai.sai_enrollment_no) = ?";
        $bindings[] = trim($request->semester_no);
    }

    if ($request->filled('student_id')) {
        $where[] = "aai.aai_id = ?";
        $bindings[] = (int) $request->student_id;
    }

    if ($request->filled('registered_number')) {
        $where[] = "UPPER(sai.sai_enrollment_no) LIKE ?";
        $bindings[] = '%' . strtoupper(trim($request->registered_number)) . '%';
    }

    /*
     * From Date / To Date filter on issue_date.
     * User can filter by one field or multiple fields.
     * No restriction.
     */
    if ($request->filled('from_date') && $request->filled('to_date')) {
        $where[] = "TRUNC(challan.issue_date) BETWEEN TO_DATE(?, 'YYYY-MM-DD') AND TO_DATE(?, 'YYYY-MM-DD')";
        $bindings[] = $request->from_date;
        $bindings[] = $request->to_date;
    } elseif ($request->filled('from_date')) {
        $where[] = "TRUNC(challan.issue_date) >= TO_DATE(?, 'YYYY-MM-DD')";
        $bindings[] = $request->from_date;
    } elseif ($request->filled('to_date')) {
        $where[] = "TRUNC(challan.issue_date) <= TO_DATE(?, 'YYYY-MM-DD')";
        $bindings[] = $request->to_date;
    }

    $whereSql = 'WHERE ' . implode(' AND ', $where);

    $sql = "
        WITH course_agg AS (
            SELECT
                sc.sc_sai_enrollment_no AS enrollment_no,
                SUM(sc.sc_pc_course_cr) AS no_of_enrolled_cr_hr
            FROM student_courses sc
            WHERE sc.sc_co_sess_id = ?
              AND sc.sc_enrollment_status = 1
            GROUP BY sc.sc_sai_enrollment_no
        ),

        challan_agg AS (
            SELECT
                ch.sc_enrollment_no AS enrollment_no,
                SUM(ch.sc_tuition_fee_amnt) AS gross_amount,
                SUM(ch.sc_ssd_arrear) AS arrears,
                MAX(ch.sc_issu_date) AS issue_date
            FROM student_challan ch
            WHERE ch.sc_challan_type IN ('Credit_hours', 'Full_semester')
              AND ch.sc_session_id = ?
            GROUP BY ch.sc_enrollment_no
        ),

        scholarship_agg AS (
            SELECT
                ssd.ssd_sai_enrollment_no AS enrollment_no,
                COUNT(ssd.ssd_sai_enrollment_no) AS scholarship_count
            FROM students_scholarships_details ssd
            WHERE ssd.ssd_sess_id = ?
            GROUP BY ssd.ssd_sai_enrollment_no
        )

        SELECT
            aai.aai_id AS student_id,
            sai.sai_enrollment_no AS student_reg,
            sch.school_name AS school_name,
            prog.prog_name AS prog_name,
            get_semester_no(sai.sai_enrollment_no) AS semester_no,

            NVL(course.no_of_enrolled_cr_hr, 0) AS no_of_enrolled_cr_hr,

            NVL(sai.sai_prog_total_credits, 0) - NVL(sai.sai_earn_credit, 0) AS remaining_cr_hr,

            NVL(challan.gross_amount, 0) AS gross_amount,

            NVL(scholarship.scholarship_count, 0) AS scholarship_count,

            NVL(challan.arrears, 0) AS arrears,

            TO_CHAR(
                challan.issue_date,
                'DD-MON-RR',
                'NLS_DATE_LANGUAGE=ENGLISH'
            ) AS issue_date,

            sai.sai_student_status AS sai_student_status

        FROM students_apply_info sai

        LEFT JOIN admission_apply_info aai
            ON aai.cms_sai_enrollment_no = sai.sai_enrollment_no

        LEFT JOIN schools sch
            ON sch.school_id = sai.sai_sdpo_school_id

        LEFT JOIN programs prog
            ON prog.prog_id = sai.sai_sdpo_prog_id

        LEFT JOIN course_agg course
            ON course.enrollment_no = sai.sai_enrollment_no

        LEFT JOIN challan_agg challan
            ON challan.enrollment_no = sai.sai_enrollment_no

        LEFT JOIN scholarship_agg scholarship
            ON scholarship.enrollment_no = sai.sai_enrollment_no

        $whereSql

        ORDER BY
            sch.school_name,
            prog.prog_name,
            sai.sai_enrollment_no
    ";

    try {
        $students = DB::select($sql, $bindings);

        return response()->json([
            'status' => true,
            'data'   => $students,
        ]);
    } catch (\Throwable $e) {
        report($e);

        return response()->json([
            'status'  => false,
            'message' => 'Data filter nahi ho raha.',
        ], 500);
    }
}

public function filterEnrolledStudents_backup(Request $request)
{

// dd("set Filter");
dd($request->all());

    $sessionId = $request->session ?? 165;

    $where = " WHERE sai_student_status IN ('Active') ";

    if (!empty($request->school)) {
        $where .= " AND sai_sdpo_school_id = " . (int) $request->school;
    }

    if (!empty($request->department_id)) {
        $where .= " AND sai_sdpo_dept_id = " . (int) $request->department_id;
    }

    if (!empty($request->program)) {
        $where .= " AND sai_sdpo_prog_id = " . (int) $request->program;
    }

    if (!empty($request->semester_no)) {
        $where .= " AND get_semester_no(sai_enrollment_no) = " . (int) $request->semester_no;
    }

    if (!empty($request->student_id)) {
        $where .= " AND aai_id = " . (int) $request->student_id;
    }

    if (!empty($request->registered_number)) {
        $regNo = strtoupper(trim($request->registered_number));
        $where .= " AND UPPER(sai_enrollment_no) LIKE '%" . str_replace("'", "''", $regNo) . "%'";
    }

    if (!empty($request->from_date) && !empty($request->to_date)) {
        $where .= " AND TRUNC(students_apply_info.created_at) BETWEEN
            TO_DATE('" . $request->from_date . "', 'YYYY-MM-DD')
            AND TO_DATE('" . $request->to_date . "', 'YYYY-MM-DD')";
    } elseif (!empty($request->from_date)) {
        $where .= " AND TRUNC(students_apply_info.created_at) >=
            TO_DATE('" . $request->from_date . "', 'YYYY-MM-DD')";
    } elseif (!empty($request->to_date)) {
        $where .= " AND TRUNC(students_apply_info.created_at) <=
            TO_DATE('" . $request->to_date . "', 'YYYY-MM-DD')";
    }

    $sql = "
        SELECT
            aai_id AS student_id,
            sai_enrollment_no AS student_reg,
            school_name,
            prog_name,
            get_semester_no(sai_enrollment_no) AS semester_no,

            (
                SELECT SUM(sc_pc_course_cr)
                FROM student_courses
                WHERE sc_co_sess_id = $sessionId
                  AND sc_enrollment_status = 1
                  AND sc_sai_enrollment_no = sai_enrollment_no
            ) AS no_of_enrolled_cr_hr,

            SAI_PROG_TOTAL_CREDITS - SAI_EARN_CREDIT AS remaining_cr_hr,

            (
                SELECT SUM(SC_TUITION_FEE_AMNT)
                FROM student_challan
                WHERE sc_challan_type IN ('Credit_hours', 'Full_semester')
                  AND sc_session_id = $sessionId
                  AND sc_enrollment_no = sai_enrollment_no
            ) AS gross_amount,

            (
                SELECT COUNT(ssd_sai_enrollment_no)
                FROM students_scholarships_details
                WHERE ssd_sess_id = $sessionId
                  AND ssd_sai_enrollment_no = sai_enrollment_no
            ) AS scholarship_count,

            (
                SELECT SUM(SC_SSD_ARREAR)
                FROM student_challan
                WHERE sc_challan_type IN ('Credit_hours', 'Full_semester')
                  AND sc_session_id = $sessionId
                  AND sc_enrollment_no = sai_enrollment_no
            ) AS arrears,

            sai_student_status

        FROM students_apply_info

        LEFT JOIN admission_apply_info
            ON cms_sai_enrollment_no = sai_enrollment_no

        LEFT JOIN schools
            ON school_id = sai_sdpo_school_id

        LEFT JOIN programs
            ON prog_id = sai_sdpo_prog_id

        $where
    ";

    $students = DB::select($sql);

    return response()->json([
        'status' => true,
        'data' => $students
    ]);
}

public function getDepartments($school_id)
{
    $departments = Department::where('dept_school_id', $school_id)
        ->pluck('dept_name', 'dept_id');

    return response()->json($departments);
}


    public function submitFine(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fineCategoryOption' => 'required',
            'violationType' => 'required',
            'fineAmount' => 'required',
        ], [
            'fineCategoryOption.required' => 'Category is required',
            'violationType.required' => 'Violation Type is required',
            'fineAmount.required' => 'Fine Amount is required',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return response()->json(['success' => false, 'errors' => $errors], 422);
        }

        try {
            // Validate the request data as needed
            $fineCategoryOption = $request->fineCategoryOption;
            $violationType = $request->violationType;
            $fineAmount = $request->fineAmount;
            $id = $request->id;

            // Use the auth() helper to get the authenticated user's ID
            $entryUserId = auth()->user()->employee_id; // Replace 'employee_id' with the actual user ID field in your users table

            // Perform the database operation
            if ($request->verifyEdit == '1') {
                // Update existing fine record
                $updateData = [
                    'FC_ID' => $fineCategoryOption,
                    'fcd_violation_type' => $violationType,
                    'fcd_fine_amount' => $fineAmount,
                    'entry_user_id' => $entryUserId,
                    'fcd_status' => 'E',
                ];

                $updated = DB::table('fine_category_details')
                    ->where('id', $id)
                    ->update($updateData);

                if (!$updated) {
                    return response()->json(['message' => 'Failed to update fine'], 500);
                } else {
                    return response()->json(['success' => true, 'message' => 'Fine updated successfully', 'challanRec' => $this->getFinesData()]);
                }
            } else {
                // Insert new fine record
                $dbInsert = DB::table('fine_category_details')->insert([
                    'fc_id' => $fineCategoryOption,
                    'fcd_violation_type' => $violationType,
                    'fcd_fine_amount' => $fineAmount,
                    'entry_user_id' => $entryUserId,
                    'fcd_status' => 'E'
                ]);

                if (!$dbInsert) {
                    throw new \Exception('DB Insertion Failed');
                } else {
                    return response()->json(['success' => true, 'message' => 'Fine inserted successfully', 'challanRec' => $this->getFinesData()]);
                }
            }
        } catch (\Exception $e) {
            // Handle exceptions
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }




    public function challanGeneratedList(Request $request)
    {

        $challan_types= DB::table('student_challan')
                            ->select('sc_challan_type')
                            ->distinct()
                            ->whereNotIn('sc_challan_type', ['Processing_Fee', 'Fine', 'Fee'])
                            ->orderBy('sc_challan_type')
                            ->get();
        $mapped_challantype = [];
        if(!empty($challan_types)){
            foreach ($challan_types as $row) {
                $key = $row->sc_challan_type;
                $value = ucwords(str_replace(['_', '/'], ' ', $key));
                $mapped_challantype[$key] = $value;
            }
        }
        // dd($mapped_challantype);


        $sessions = DB::table('student_challan')
                            ->select('sc_session_id','SESS_NAME')
                            ->join('academic_sessions', 'academic_sessions.sess_id', '=', 'student_challan.sc_session_id')
                            ->whereNotIn('sc_challan_type', ['Processing_Fee', 'Fine', 'Fee'])
                            ->distinct()
                            ->orderBy('sc_session_id', 'desc')
                            ->get();
            // dd($sessions);
        $sql = "SELECT distinct emp_ad_username, EMPLOYEE_ID FROM EMP_EMPLOYEES
                join student_challan on student_challan.SC_ENTRY_USER_ID = EMP_EMPLOYEES.EMPLOYEE_ID
                    WHERE student_challan.sc_challan_type not in ('Processing_Fee', 'Fine', 'Fee') order by emp_ad_username"
                    ;
        $users = DB::select($sql);

        return view('StudentFinancial.challanDetails', compact('mapped_challantype','sessions', 'users'));
    }
    public function getChallansData(Request $request){
        $session_condi = '';
        $challanType_condi= '';
        $date_condi = '';

        if(!empty($request->session)){
            $session_condi =    "AND student_challan.sc_session_id = $request->session";
        }else{
            $session_condi =    "AND student_challan.sc_session_id in ( 165,164,163,162,161,160)";
        }

        if(!empty($request->challan_type)){
            $challanType_condi =     "AND student_challan.sc_challan_type = '$request->challan_type'";
        }else{
            $challanType_condi =     "AND student_challan.sc_challan_type not in ('Processing_Fee', 'Fine', 'Fee')";
        }

        if (!empty($request->fromDate) && !empty($request->toDate)) {
            $date_condi = "AND student_challan.SC_ISSU_DATE BETWEEN TO_DATE('{$request->fromDate}', 'YYYY-MM-DD') AND TO_DATE('{$request->toDate}', 'YYYY-MM-DD')";
        } elseif (!empty($request->fromDate)) {
            $date_condi = "AND student_challan.SC_ISSU_DATE >= TO_DATE('{$request->fromDate}', 'YYYY-MM-DD')";
        } elseif (!empty($request->toDate)) {
            $date_condi = "AND student_challan.SC_ISSU_DATE <= TO_DATE('{$request->toDate}', 'YYYY-MM-DD')";
        }

        $sql="SELECT
                sai_enrollment_no as sc_enrollment_no,
                academic_sessions.sess_name,
                student_challan.sc_amount,
                student_challan.sc_challan_status  AS sc_challan_status,
                student_challan.invoiceamount,
                student_challan.sc_challan_type,
                student_challan.challan_no    AS challan_id,
                student_challan.created_at,
                sai_student_status as student_status
            FROM
                student_challan
                join ADMISSION_APPLY_INFo on aai_id=sc_app_id
            join ADMISSION_PERSONAL_INFO on aai_api_id=api_id
            join STUDENTS_APPLY_INFO on SAI_API_ID=api_id
            AND LOWER(CMS_SAI_ENROLLMENT_NO) = LOWER(SAI_ENROLLMENT_NO)
                LEFT JOIN academic_sessions ON sc_session_id = academic_sessions.sess_id
            WHERE
                    1 = 1
                $session_condi
                $challanType_condi
                $date_condi
            ORDER BY
                student_challan.created_at DESC";

        // echo $sql;exit;
        $challansRec=DB::select($sql);

        $cancelled_challansRec = $this->getCancelChallanData($request);
        // dd($challansRec[0], $cancelled_challansRec[0]);
        $challansRec = array_merge($challansRec, $cancelled_challansRec);

        return DataTables::of($challansRec)->toJson();
    }
    public function getCancelChallanData($request){
        $session_condi = '';
        $challanType_condi= '';
        $date_condi = '';

        if(!empty($request->session)){
            $session_condi =    "AND fin_student_cancel_challan.sc_session_id = $request->session";
        }else{
            $session_condi =    "AND fin_student_cancel_challan.sc_session_id in ( 165,164,163,162,161,160)";
        }

        if(!empty($request->challan_type)){
            $challanType_condi =     "AND fin_student_cancel_challan.sc_challan_type = '$request->challan_type'";
        }else{
            $challanType_condi =     "AND fin_student_cancel_challan.sc_challan_type not in ('Processing_Fee', 'Fine', 'Fee')";
        }

        if (!empty($request->fromDate) && !empty($request->toDate)) {
            $date_condi = "AND fin_student_cancel_challan.CREATED_AT BETWEEN TO_DATE('{$request->fromDate}', 'YYYY-MM-DD') AND TO_DATE('{$request->toDate}', 'YYYY-MM-DD')";
        } elseif (!empty($request->fromDate)) {
            $date_condi = "AND fin_student_cancel_challan.CREATED_AT >= TO_DATE('{$request->fromDate}', 'YYYY-MM-DD')";
        } elseif (!empty($request->toDate)) {
            $date_condi = "AND fin_student_cancel_challan.CREATED_AT <= TO_DATE('{$request->toDate}', 'YYYY-MM-DD')";
        }
        $sql="SELECT
                sai_enrollment_no as sc_enrollment_no,
                academic_sessions.sess_name,
                fin_student_cancel_challan.sc_amount,
                'CANCELLED'              AS sc_challan_status,
                fin_student_cancel_challan.invoiceamount,
                fin_student_cancel_challan.sc_challan_type,
                fin_student_cancel_challan.sc_id    AS challan_id,
                fin_student_cancel_challan.created_at,
                sai_student_status as student_status
            FROM
                fin_student_cancel_challan
                join ADMISSION_APPLY_INFo on aai_id=sc_app_id
            join ADMISSION_PERSONAL_INFO on aai_api_id=api_id
            join STUDENTS_APPLY_INFO on SAI_API_ID=api_id
            AND LOWER(CMS_SAI_ENROLLMENT_NO) = LOWER(SAI_ENROLLMENT_NO)
                LEFT JOIN academic_sessions ON sc_session_id = academic_sessions.sess_id
            WHERE
                    1 = 1
                $session_condi
                $challanType_condi
                $date_condi
            ORDER BY
                fin_student_cancel_challan.created_at DESC";

        // echo $sql;exit;
        $challansRec=DB::select($sql);
        return $challansRec;
    }
    function blockedStudentFineList(){

        return view('grader.StudentFines.blockedStudents');
    }


    function getBlockedStudents(){
        $sql = "SELECT DISTINCT
                    sc_sai_enrollment_no,spi_name,sc_status
                FROM
                    student_courses
                    left join students_apply_info on sai_enrollment_no = sc_sai_enrollment_no
                    left join students_personal_info on sai_spi_id = spi_id

                WHERE
                    sc_co_sess_id = 162
                    AND sc_status = 'Block'";
        $res = DB::select($sql);
        // $response = [
        //     'blockedSTudents' => $res,
        // ];
        return response()->json($res);
    }





public function insertData(Request $request)
{
    // Validate the request
    $request->validate([
        'action' => 'required|string',
    ]);

    $username = auth()->user()->username;
   // Allowed usernames and group IDs
   $allowedUsernames = ['asif_nadeem']; // Add all allowed usernames
   $allowedGroupIds = [644]; // Add all allowed group IDs


//    $response = in_array($username, $allowedUsernames) || in_array(auth()->user()->group_id, $allowedGroupIds)
//    ? $this->getChallanStudents()
//    : $this->getChallanStudentsExcel();`

   $response = $this->getChallanStudents();


    $data = json_decode($response->getContent(), true);





    // Check if data is empty
    if (empty($data)) {
        return response()->json(['error' => 'No data returned from getChallanStudents.'], 400);
    }

    // Separate columns into two arrays: general and scholarships
    $columnNames = [];
    $scholarshipNames = [];

    foreach ($data as &$row) {
        // Add main keys of the row to column names, excluding 'scholarships'
        foreach (array_keys($row) as $key) {
            if ($key !== 'scholarships') {
                $sanitizedKey = strtolower(preg_replace('/[^A-Za-z0-9_]/', '_', $key));
                if (!in_array($sanitizedKey, $columnNames)) {
                    $columnNames[] = $sanitizedKey;
                }
            }
        }

        // dd($columnNames);
        // Handle scholarships dynamically
        if (isset($row['scholarships']) && is_array($row['scholarships'])) {
            foreach ($row['scholarships'] as $scholarship) {
                if (isset($scholarship['scholarship_name'])) {
                    $scholarshipColumn = strtolower(preg_replace('/[^A-Za-z0-9_]/', '_', $scholarship['scholarship_name']));
                    if (!in_array($scholarshipColumn, $scholarshipNames)) {
                        $scholarshipNames[] = $scholarshipColumn;
                    }

                    // Add the discounted amount under the scholarship column
                    $row[$scholarshipColumn] = $scholarship['fsd_discounted_amount'] ?? null;
                }
            }
            unset($row['scholarships']); // Remove the scholarships array
        }
    }

    // Combine columns for insertion/export
    $allColumns = array_merge($columnNames, $scholarshipNames);

// dd($allColumns);

    // Handle insert action
    if ($request->action === 'insert') {
        $tableName = 'cms_fee.challan_data_new';

        // Drop the table if it exists
        $dropTableSql = "BEGIN
                            EXECUTE IMMEDIATE 'DROP TABLE $tableName';
                         EXCEPTION
                            WHEN OTHERS THEN
                                IF SQLCODE != -942 THEN
                                    RAISE;
                                END IF;
                         END;";
        DB::connection('oracle_secondary')->statement($dropTableSql);

        // Create the table dynamically
        $createTableSql = "CREATE TABLE $tableName (id NUMBER GENERATED BY DEFAULT AS IDENTITY PRIMARY KEY";
        foreach ($allColumns as $column) {
            $createTableSql .= ", $column VARCHAR2(2500)";
        }
        $createTableSql .= ")";
        DB::connection('oracle_secondary')->statement($createTableSql);

        // Insert data into the table
        foreach ($data as $row) {
            $mappedRow = array_fill_keys($allColumns, "NULL");

            foreach ($row as $key => $value) {
                $sanitizedKey = strtolower(preg_replace('/[^A-Za-z0-9_]/', '_', $key));
                if (in_array($sanitizedKey, $allColumns)) {
                    $mappedRow[$sanitizedKey] = $value === null ? "NULL" : "'" . str_replace("'", "''", (string)$value) . "'";
                }
            }

            $columns = implode(', ', array_keys($mappedRow));
            $values = implode(', ', array_values($mappedRow));

            $insertSql = "INSERT INTO $tableName ($columns) VALUES ($values)";
            DB::connection('oracle_secondary')->statement($insertSql);
        }

        // Update specific rows
        $sel="UPDATE challan_data_new
        SET valid_date = CASE
                        WHEN challan_no IN ('13211','13209','13207','13199')
    and version=1 THEN TO_DATE('31-JAN-2025', 'DD-MON-YYYY')
                        END,due_date = CASE
                            WHEN challan_no IN ('13211','13209','13207','13199') THEN TO_DATE('17-JAN-2025', 'DD-MON-YYYY')
                        END
            WHERE challan_no IN ('13211','13209','13207','13199')
    and version=1";

    DB::connection('oracle_secondary')->statement($sel);

    $sql="UPDATE challan_data_new
        SET SCHOLARSHIP_ARREAR =0
            WHERE SCHOLARSHIP_ARREAR > 0";

    DB::connection('oracle_secondary')->statement($sql);

    $sql1 = "UPDATE challan_data_new
                SET
                    app_id = CASE
                        WHEN challan_type = 'shortcourse' THEN 'SC' || app_id
                        WHEN challan_type = 'donation' THEN 'D' || app_id
                        WHEN challan_type = 'misc_fee' THEN 'M' || app_id
                        ELSE app_id
                    END,
                    CHANGE_TIME = CASE
                        WHEN CHANGE_TIME IS NULL THEN created_at
                        ELSE CHANGE_TIME
                    END
                WHERE challan_type IN ('shortcourse', 'donation', 'misc_fee')";

    DB::connection('oracle_secondary')->statement($sql1);

        // Grant SELECT permissions
        $grantSql1 = "GRANT SELECT ON $tableName TO FEE_DB_LINK";
        $grantSql2 = "GRANT SELECT ON $tableName TO cms_fee_view";
        DB::connection('oracle_secondary')->statement($grantSql1);
        DB::connection('oracle_secondary')->statement($grantSql2);

        return response()->json(['success' => 'Data export successfully in Middleware database.']);
    }


    // Define the folder path for CSV export
    $folderPath = public_path('student_challan_utility');
    if (!File::exists($folderPath)) {
        File::makeDirectory($folderPath, 0755, true);
    }

    // Generate a file name with the current date and time
    $username = auth()->user()->username;
    $fileName = 'challan_data_' . $username . '_' . Carbon::now()->format('Ymd_His') . '.csv';
    $filePath = $folderPath . '/' . $fileName;

    // Column mapping for headers
    $columnMapping = [
        'issue_date' => 'Issue Date',
        'due_date' => 'Due Date',
        'valid_date' => 'Valid Date',
        'challan_no' => 'Challan No',
        'sess_id' => 'Session',
        'challan_type' => 'Challan Type',
        'installments' => 'Installments',
        'regno' => 'Reg No',
        'app_id' => 'Student ID',
        'studentname' => 'Name',
        'sess_code' => 'Session',
        'schl_code' => 'School',
        'prog_name' => 'Program',
        'admission_amt' => 'Adm Amt',
        'security_amt' => 'Sec Amt',
        'medical_amt' => 'Med Amt',
        'activity_amt' => 'Act Amt',
        'tuition_fee_amnt' => 'Tui Amt',
        'surplus_amount' => 'Sur Amt',
        'hostel_amount' => 'Hostel Amount',
        'invoice_amount' => 'Invoice',
        'fine' => 'Fine',
        'paid_amount' => 'Total Paid',
        'enrolled_credits'=> 'Enrolled Credits',
        'challan_status' => 'Status'
    ];

    // Add scholarships to column mapping
    foreach ($scholarshipNames as $scholarship) {
        $columnMapping[$scholarship] = ucwords(str_replace('_', ' ', $scholarship));
    }

    // Determine columns based on user
    if (in_array($username, $allowedUsernames) || in_array(auth()->user()->group_id, $allowedGroupIds)) {
        // Allowed users get all columns
        $exportColumns = $allColumns;
        $headers = array_map(function ($col) use ($columnMapping) {
            return $columnMapping[$col] ?? $col; // Map headers if possible
        }, $exportColumns);
    } else {


        // Not allowed users get specific columns
        $exportColumns = array_merge(
            [
                'issue_date', 'due_date', 'valid_date', 'challan_no', 'sess_id','challan_type', 'installments', 'regno', 'app_id', 'studentname',
                'sess_code', 'schl_code', 'prog_name', 'admission_amt', 'security_amt', 'medical_amt', 'activity_amt',
                'tuition_fee_amnt', 'surplus_amount','hostel_amount'
            ],
            $scholarshipNames,
            [
                'invoice_amount', 'fine', 'paid_amount','enrolled_credits', 'challan_status'
            ]
        );
        $headers = array_map(function ($col) use ($columnMapping) {
            return $columnMapping[$col] ?? $col; // Map headers if possible
        }, $exportColumns);
    }

    // Open the file for writing
    $file = fopen($filePath, 'w');

    // Write headers
    fputcsv($file, $headers);

    // Write rows
    foreach ($data as $row) {
        $rowData = [];
        foreach ($exportColumns as $column) {
            $rowData[] = $row[$column] ?? ''; // Use empty string for missing columns
        }
        fputcsv($file, $rowData);
    }

    fclose($file);

    // Return the file URL
    return response()->json([
        'success' => 'The Excel file has been generated successfully and is ready for download.',
        'file_url' => url('student_challan_utility/' . $fileName),
    ]);
}



public function insertDataSummer(Request $request)
{
    $request->validate([
        'action' => 'required|string',
        'session_id' => 'required|string',
    ]);


    $username = auth()->user()->username;
    // $allowedUsernames = ['asif_nadeem'];
    // $allowedGroupIds = [644];

    // Fetch data from updated SQL function
    $response = $this->getChallanStudentsSummer($request->session_id);
    $data = json_decode($response->getContent(), true);

    if (empty($data)) {
        return response()->json(['error' => 'No data returned from getChallanStudentsSummer.'], 400);
    }
    // dd($data);

    // Get all unique column names from result
    $allColumns = [];
    foreach ($data as $row) {
        foreach (array_keys($row) as $key) {
            $sanitizedKey = strtolower(preg_replace('/[^A-Za-z0-9_]/', '_', $key));
            if (!in_array($sanitizedKey, $allColumns)) {
                $allColumns[] = $sanitizedKey;
            }
        }
    }

    if ($request->action === 'insert') {
        $tableName = 'cms_fee.summer_challan_breakup';

        $dropTableSql = "BEGIN
                            EXECUTE IMMEDIATE 'DROP TABLE $tableName';
                         EXCEPTION
                            WHEN OTHERS THEN
                                IF SQLCODE != -942 THEN
                                    RAISE;
                                END IF;
                         END;";
        DB::connection('oracle_secondary')->statement($dropTableSql);

        $createTableSql = "CREATE TABLE $tableName (id NUMBER GENERATED BY DEFAULT AS IDENTITY PRIMARY KEY";
        foreach ($allColumns as $column) {
            $createTableSql .= ", $column VARCHAR2(3000)";
        }
        $createTableSql .= ")";
        DB::connection('oracle_secondary')->statement($createTableSql);

        foreach ($data as $row) {
            $mappedRow = array_fill_keys($allColumns, "NULL");
            foreach ($row as $key => $value) {
                $sanitizedKey = strtolower(preg_replace('/[^A-Za-z0-9_]/', '_', $key));
                if (in_array($sanitizedKey, $allColumns)) {
                    $mappedRow[$sanitizedKey] = $value === null ? "NULL" : "'" . str_replace("'", "''", (string)$value) . "'";
                }
            }

            $columns = implode(', ', array_keys($mappedRow));
            $values = implode(', ', array_values($mappedRow));
            $insertSql = "INSERT INTO $tableName ($columns) VALUES ($values)";
            DB::connection('oracle_secondary')->statement($insertSql);
           // Grant SELECT permissions
        $grantSql1 = "GRANT SELECT ON $tableName TO FEE_DB_LINK";
        $grantSql2 = "GRANT SELECT ON $tableName TO cms_fee_view";
        DB::connection('oracle_secondary')->statement($grantSql1);
        DB::connection('oracle_secondary')->statement($grantSql2);
        }


        return response()->json(['success' => 'Summer Data exported successfully to Middleware database.']);
    }

    // CSV Export (no scholarship logic)
    $folderPath = public_path('student_challan_utility');
    if (!File::exists($folderPath)) {
        File::makeDirectory($folderPath, 0755, true);
    }

    $fileName = 'summer_challan_breakup_' . $username . '_' . Carbon::now()->format('Ymd_His') . '.csv';
    $filePath = $folderPath . '/' . $fileName;

    $file = fopen($filePath, 'w');

    // Header
    fputcsv($file, $allColumns);

    // Data rows
    foreach ($data as $row) {
        $rowData = [];
        foreach ($allColumns as $column) {
            $originalKey = array_keys($row)[array_search($column, array_map(function ($k) {
                return strtolower(preg_replace('/[^A-Za-z0-9_]/', '_', $k));
            }, array_keys($row))) ?? -1] ?? null;

            $rowData[] = $originalKey !== null ? $row[$originalKey] : '';
        }
        fputcsv($file, $rowData);
    }

    fclose($file);

    return response()->json([
        'success' => 'The Summer Challan Breakup file has been generated successfully.',
        'file_url' => url('student_challan_utility/' . $fileName),
    ]);
}

    function students_fee_challan(){

        $sessions = $this->getSessions();


        // Filter only sessions where sess_name contains "Summer"
    $summerSessions = collect($sessions)->filter(function ($session) {
        return stripos($session->sess_name, 'summer') !== false;
    })->values();



        return view('grader.StudentFines.feeChallans', ['sessions' => $summerSessions]);
    }

   function getChallanStudents()
{
    $sql = "SELECT *  FROM fee_challan_data WHERE PIN_FEECHALLANNO < 1000000";
  //WHERE PIN_FEECHALLANNO < 1000000 and SCHOLARSHIP_ARREAR>0
    $res = DB::select($sql);

    foreach ($res as $key => $row) {

        // --- Scholarships ---
        $res2 = [];

        if (in_array($row->challan_type, ['Full_semester', 'Fee', 'Short/Summer_Course']) && $row->enrolled_credits == 0 && ($row->challan_sch>0 || $row->scholarship_arrear>0)) {

            $sql2 = "SELECT
                        st.scholarship_name,
                        CASE
                            WHEN fsd.fsd_sch_instal_amnt IS NULL
                            THEN (fsd.fsd_discounted_amount + COALESCE((
                                SELECT SUM(ssd.SSD_ARREAR)
                                FROM students_scholarships_details ssd
                                WHERE ssd.SSD_TYPE = fsd.fsd_scholarship_type
                                  AND ssd.ssd_sess_id = :session_id
                                  AND ssd.ssd_app_id = :enrollment_no
                                  AND ssd.STATUS = 'E'
                                  AND ssd.SSD_ARREAR > 0
                            ), 0))
                            ELSE (ROUND(fsd.fsd_sch_instal_amnt) + COALESCE((
                                SELECT SUM(ssd.SSD_ARREAR)
                                FROM students_scholarships_details ssd
                                WHERE ssd.SSD_TYPE = fsd.fsd_scholarship_type
                                  AND ssd.ssd_sess_id = :session_id
                                  AND ssd.ssd_app_id = :enrollment_no
                                  AND ssd.STATUS = 'E'
                                  AND ssd.SSD_ARREAR > 0
                            ), 0))
                        END AS fsd_discounted_amount
                    FROM fin_scholarship_display_history fsd
                    LEFT JOIN scholarship_type st
                        ON st.scholarship_id = fsd.fsd_scholarship_type
                    WHERE fsd.fsd_aai_id = :enrollment_no
                      AND fsd.fsd_sesion_id = :session_id
                      AND fsd.FSD_SCH_TYPE IS NULL AND fsd.challan_id=:challanNo";

            $res2 = DB::select($sql2, [
                'enrollment_no' => $row->app_id,
                'session_id'    => $row->sess_id,
                'challanNo'    => $row->challan_no
            ]);

        } elseif ($row->challan_type == 'Hostel_Fee' && ($row->challan_sch>0 ||  $row->scholarship_arrear>0) ) {

            $sql2 = "
            SELECT
                        st.scholarship_name,
                        CASE
                            WHEN fsd.fsd_sch_instal_amnt IS NULL
                            THEN (fsd.fsd_discounted_amount + COALESCE((
                                SELECT SUM(ssd.SShD_ARREAR)
                                FROM FIN_STUDENTS_SCHOLARSHIPS_HOSTEL_DETAILS ssd
                                WHERE ssd.SShD_TYPE = fsd.fsd_scholarship_type
                                  AND ssd.sshd_sess_id = :session_id
                                  AND ssd.sshd_app_id = :enrollment_no
                                  AND ssd.sshd_STATUS = 'E'
                                  AND ssd.SShD_ARREAR > 0
                            ), 0))
                            ELSE (ROUND(fsd.fsd_sch_instal_amnt) + COALESCE((
                                SELECT SUM(ssd.SShD_ARREAR)
                                FROM FIN_STUDENTS_SCHOLARSHIPS_HOSTEL_DETAILS ssd
                                WHERE ssd.SShD_TYPE = fsd.fsd_scholarship_type
                                  AND ssd.sshd_sess_id = :session_id
                                  AND ssd.sshd_app_id = :enrollment_no
                                  AND ssd.sshd_STATUS = 'E'
                                  AND ssd.SShD_ARREAR > 0
                            ), 0))
                        END AS fsd_discounted_amount
                    FROM fin_scholarship_display_history fsd
                    LEFT JOIN scholarship_type st
                        ON st.scholarship_id = fsd.fsd_scholarship_type
                    WHERE fsd.fsd_aai_id = :enrollment_no
                      AND fsd.fsd_sesion_id = :session_id
                      AND fsd.FSD_SCH_TYPE = 'hostel' AND fsd.challan_id=:challanNo";

            $res2 = DB::select($sql2, [
                'enrollment_no' => $row->app_id,
                'session_id'    => $row->sess_id,
                'challanNo'    => $row->challan_no
            ]);
        }

        $row->scholarships = !empty($res2) ? array_map(function ($scholarship) {
            return [
                'scholarship_name'      => $scholarship->scholarship_name ?? null,
                'fsd_discounted_amount' => $scholarship->fsd_discounted_amount ?? null,
            ];
        }, $res2) : null;
    }

    return response()->json($res);
}



    function getChallanStudentsSummer($session_id){
                $sql = " WITH student_prog_level AS (
                SELECT DISTINCT
                    sai.SAI_ENROLLMENT_NO,
                    prog.PROG_DEGREE_LEVEL
                FROM students_apply_info sai
                JOIN programs prog ON sai.SAI_SDPO_PROG_ID = prog.PROG_ID
            ),
            valid_challan AS (
                SELECT *
                FROM (
                    SELECT sc.*,
                        ROW_NUMBER() OVER (
                            PARTITION BY sc.sc_enrollment_no, sc.sc_session_id
                            ORDER BY sc.id DESC
                        ) AS rn
                    FROM AUD_BNUCMS.STUDENT_CHALLAN_AUD sc
                    WHERE sc.sc_challan_type = 'Short/Summer_Course'
                    AND sc.sc_enrolled_cr_hr IS NOT NULL
                    AND dml_operation = 'I'
                            AND TRUNC (created_at) =TO_DATE ('2025-10-13', 'YYYY-MM-DD')
                )
                WHERE rn = 1
            ),
            scs AS (
                SELECT DISTINCT
                    dmy.REGNO AS sc_sai_enrollment_no,
                    dmy.COURSE_ID,
                    e.sc_cms_course_co_id,
                    co.co_course_cr AS credit_hours,
                    co.co_school_id AS school_id,
                    dmy.COURSE_STATUS AS co_status
                FROM student_courses e
                LEFT JOIN dummy_summer_enrollment dmy ON e.sc_sai_enrollment_no = dmy.REGNO
                                            AND dmy.COURSE_ID = e.SC_CMS_COURSE_CO_ID
                JOIN courses_offered co  ON e.sc_cms_course_co_id = co.co_id
                WHERE e.sc_enrollment_status = 1
                AND e.SC_STATUS IN ('A', 'Block', 'Withdrawal')
                AND e.sc_course_id NOT IN (1533, 6123, 6825, 6824, 9542)
                AND dmy.status = 'E'
                and sess_id=:session_id
            ),
            valid_fee_structure AS (
                SELECT *
                FROM (
                    SELECT fs.*,
                        ROW_NUMBER() OVER (
                            PARTITION BY fs.ffs_school_id, fs.ffs_sess_id
                            ORDER BY
                                CASE
                                    WHEN fs.ffs_degree_level = (
                                        SELECT spl.PROG_DEGREE_LEVEL
                                        FROM student_prog_level spl
                                        WHERE ROWNUM = 1
                                    ) THEN 1
                                    ELSE 2
                                END,
                                fs.ffs_entry_datetime DESC
                        ) AS rn
                    FROM fin_fee_structure fs
                    WHERE fs.ffs_fee_type = 'Short/Summer_Course'
                    AND fs.ffs_status = 'E'
                    AND fs.ffs_credhr_amt > 0
                    AND fs.ffs_sess_id = :session_id
                )
                WHERE rn = 1
            )

            SELECT
                scs.sc_sai_enrollment_no AS REG_NO,
                spl.PROG_DEGREE_LEVEL AS DEGREE_LEVEL,
                course_code,
                course_cd_name AS course_name,
                sc.id AS challan_no,
                sc.invoiceamount AS Total_challan_amount,
                sc.SC_ACTIVITY_AMT AS Activity_amount,
                co.co_course_cr AS credit_hours,
                co.co_school_id AS CMS_school_id,
                scho.SCHOOL_CODE AS OFFERED_SCHOOL,
                scho.RODB_SCHOOL_ID AS SCHOOL_ID,
                scs.co_status,
                CASE
                    WHEN scs.co_status = 'Repeat/Failure' THEN
                        ROUND(co.co_course_cr * fs.ffs_credhr_amt * 1.5, 0)
                    ELSE
                        ROUND(co.co_course_cr * fs.ffs_credhr_amt, 0)
                END AS course_fee
            FROM scs
            JOIN courses_offered co  ON scs.sc_cms_course_co_id = co.co_id
            LEFT JOIN schools scho   ON co.CO_SCHOOL_ID = scho.SCHOOL_ID
            LEFT JOIN courses_two    ON co.co_course_id = courses_two.COURSE_ID
            JOIN valid_challan sc    ON sc.sc_enrollment_no = scs.sc_sai_enrollment_no
                                    AND sc.sc_session_id = co.co_sess_id
            JOIN student_prog_level spl ON scs.sc_sai_enrollment_no = spl.SAI_ENROLLMENT_NO
            JOIN valid_fee_structure fs ON fs.ffs_school_id = co.co_school_id
                                        AND fs.ffs_sess_id = co.co_sess_id
            WHERE co.co_sess_id = :session_id";
            // parent course wise courses offered by school challan generation first time
            if($session_id == 164){
                $sql .= " union all select * from BNUCMS.V_OLD_SUMMER_CHALLAN_BREAKUP";
            }



        $res = DB::select($sql, ['session_id' => $session_id]);
        return response()->json($res);
    }





    function getChallanStudentsExcel(){
        $sql = "SELECT
            sc_issu_date as issue_date,
            sc_due_date as due_date,
            sc_valid_date as valid_date,
            challan_no as challan_no ,
            sc_session_id as sess_id,
            sc_challan_type as challan_type,
            check_fee_installments as installments,
            sc_enrollment_no as regno ,
            sc_app_id as app_id ,
            sc_studentname as studentname,
            sess_name as sess_code,
            prog_name as prog_name,
            school_code as schl_code,
            ffs_admission_amt as admission_amt,
            ffs_security_amt as security_amt,
            ffs_medical_amt as medical_amt,
            sc_activity_amt as activity_amt,
            sc_installment_tuition_fee_amnt as tuition_fee_amnt,
            sc_surplus_amount as surplus_amount,
            ffs_hostel_amt as hostel_amount,
            invoiceamount as invoice_amount,
            fine as fine,
            callback_amount as paid_amount,
            case when sc_enrolled_cr_hr is null then 0 else sc_enrolled_cr_hr end  AS enrolled_credits,
            sc_challan_status as challan_status
        FROM
            student_challan
        left join academic_sessions on sess_id = sc_session_id
        left join schools on school_id = sc_school_id
        left join programs on prog_id = sc_program_id
        WHERE
            sc_challan_type IN ('Full_semester', 'Fee', ' Full_semester','Hostel_Fee','Credit_hours')
            AND sc_session_id > 162";

            $res = DB::select($sql);

            // Loop through the main results to fetch additional scholarship data
            foreach ($res as $key => $row) {
                // Check the challan type and fetch scholarships accordingly
                if (in_array($row->challan_type, ['Full_semester', 'Fee','Short/Summer_Course']) && $row->enrolled_credits == 0)                    {
                    $sql2 = "SELECT
                                scholarship_name,
                                CASE
                                    WHEN fsd_sch_instal_amnt IS NULL
                                    THEN fsd_discounted_amount
                                    ELSE ROUND(fsd_sch_instal_amnt)
                                END AS fsd_discounted_amount
                            FROM fin_scholarship_discounts
                            LEFT JOIN scholarship_type ON scholarship_id = fsd_scholarship_type
                            WHERE fsd_aai_id = :enrollment_no
                             and fsd_sesion_id = :session_id
                            AND FSD_SCH_TYPE is null";

                    $res2 = DB::select($sql2, ['enrollment_no' => $row->app_id,'session_id' => $row->sess_id]);
                }
                elseif ($row->challan_type == 'Hostel_Fee') {
                    $sql2 = "SELECT
                                scholarship_name,
                                CASE
                                    WHEN fsd_sch_instal_amnt IS NULL
                                    THEN fsd_discounted_amount
                                    ELSE ROUND(fsd_sch_instal_amnt)
                                END AS fsd_discounted_amount
                            FROM fin_scholarship_discounts
                            LEFT JOIN scholarship_type ON scholarship_id = fsd_scholarship_type
                            WHERE fsd_aai_id = :enrollment_no
                              and fsd_sesion_id = :session_id
                            AND FSD_SCH_TYPE = 'hostel'";

                    $res2 = DB::select($sql2, ['enrollment_no' => $row->app_id,'session_id' => $row->sess_id]);
                }
                else {
                    continue; // Skip to the next iteration if no conditions match
                }

                // Store scholarship details if found
                $row->scholarships = !empty($res2) ? array_map(function ($scholarship) {
                    return [
                        'scholarship_name' => $scholarship->scholarship_name ?? null,
                        'fsd_discounted_amount' => $scholarship->fsd_discounted_amount ?? null,
                    ];
                }, $res2) : null;
            }

        return response()->json($res);
    }



    public function deleteFine(Request $request){
        DB::beginTransaction();

        try {
            // DB::table('fine_category_details')
            //     ->where('ID', $request->mainId)
            //     ->delete();

            DB::table('fine_category_details')
                ->where('ID', $request->mainId)
                ->update(['fcd_status' => 'D']);

            DB::commit();
            return response()->json([
                    'success' => true,
                    'data' => 'Deleted Successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'data' => 'Some problem occured'
            ]);
            // Handle the exception as needed
        }

    }

    public function getFinesData(){
        $sql="select fine_category_details.id as fcd_id, fine_category.id,fine_category.fc_category_name,fine_category_details.fcd_violation_type,fine_category_details.fcd_fine_amount
        from fine_category_details
        left join fine_category on fine_category.id=fine_category_details.fc_id
        where fcd_status = 'E'
        order by fine_category_details.created_at desc
        ";
        $data=DB::select($sql);
        return $data;
    }
    public function getFineCategories(){
        // dd(auth()->user());
        $entryUserId = auth()->user()->employee_id;
        $groupId = auth()->user()->group_id;

        if ($groupId == 644 || auth()->user()->username == 'asif_nadeem') {
            $query = DB::table('fine_category')
                    ->select('id', 'fc_category_name',  'FC_DESCRIPTION')->where('fc_status','E')->orderBy('id', 'desc');
        } else {
            $query = DB::table('fine_category_permissions')
                ->leftJoin('fine_category', 'fine_category_permissions.fc_id', '=', 'fine_category.id')
                ->select('fine_category.id', 'fine_category.fc_category_name')
                ->where('FCP_STATUS', 'E')
                ->where('fine_category_permissions.fcp_allowed_user', $entryUserId);
        }

        return  $fine_categories = $query->get();
    }
    public function studentFineCategory(Request $request){
        $sql="select dept_id,dept_name from departments";

        $sql="select id,fc_category_name, fc_description from fine_category where fc_status='E'";
        $categories=DB::select($sql);
        $getUsers=$this->getUsers();
        // dd($getUsers);
        $fine_categories=DB::select($sql);
        $fineCategoryUser = DB::table('fine_category_permissions')
        ->select('id', 'fc_id','employee_id', 'prefix', 'emp_f_name', 'emp_l_name','entry_user_id','fcp_effective_from') // Selecting specific columns including 'id'
        ->leftJoin('emp_employees', 'fine_category_permissions.fcp_allowed_user', '=', 'emp_employees.employee_id')
        ->where('fcp_status', 'E')
        ->get();

        // echo $sql;exit;
        // return   $fine_categories;
        return view('grader.StudentFines.addFineCategory',compact('categories','getUsers','fineCategoryUser'));
    }



    public function fineCategoryPermission(Request $request)
    {
        // Validate and sanitize inputs as needed
        $validatedData = $request->validate([
            'category' => 'required|integer',
            'user' => 'required|integer',
            'effective_from' => 'required|date'

        ]);

        // dd($request->all());
        // Insert data using Eloquent ORM
        $result = DB::table('fine_category_permissions')->insert([
            'fc_id' => $validatedData['category'],
            'fcp_status' => 'E',
            'fcp_allowed_user' => $validatedData['user'],
            'fcp_effective_from' => $validatedData['effective_from'],
            'entry_user_id' => auth()->user()->employee_id,
        ]);

        if ($result) {
            return response()->json(['status' => true, 'message' => 'Data inserted successfully']);
        } else {
            return response()->json(['status' => false, 'message' => 'Failed to insert data'], 500);
        }
    }

    public function deleteFineCategoryPermission(Request $request)
    {
        // Validate and sanitize inputs as needed for deletion
        $validatedData = $request->validate([
            'id' => 'required|integer',
        ]);

        // Delete data from fine_category_permissions table
        // $deleted = DB::table('fine_category_permissions')
        //             ->where('id', $validatedData['id'])
        //             ->delete();

        $deletedRow = DB::table('fine_category_permissions')
                    ->where('id', $validatedData['id'])
                    ->update(['fcp_status' => 'D']);

        if ($deletedRow>0) {
            return response()->json(['status' => true, 'message' => 'Data deleted successfully']);
        } else {
            return response()->json(['status' => false, 'message' => 'Failed to delete data'], 500);
        }
    }

    function getUsers(){
        $epcddetails = "select employee_id,prefix,emp_f_name,emp_l_name from emp_employees";
        //echo $epcddetails;exit;
       return  $resEpcdetails = DB::select($epcddetails);
    }

    public function addFineCategory(Request $request)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'categoryName' => 'required',
        ], [
            'categoryName.required' => 'Category is required',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return response()->json(['success' => false, 'errors' => $errors], 422);
        }

        try {
            $categoryName = $request->input('categoryName');
            $description = $request->input('description');
            $entryUserId = auth()->user()->employee_id;

            if ($request->input('verifyEdit') == '1') {
                $id = $request->input('id'); // Ensure 'id' is passed in the request for update

                $updateData = [
                    'fc_category_name' => $categoryName,
                    'fc_description' => $description,
                    'entry_user_id' => $entryUserId,
                    'entry_user_id' => $entryUserId,
                    'fc_status' => 'E'
                ];

                $updated = DB::table('fine_category')
                    ->where('id', $id)
                    ->update($updateData);

                if (!$updated) {
                    return response()->json(['message' => 'Failed to update fine'], 500);
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Fine updated successfully',
                    'challanRec' => $this->getFineCategories()
                ]);
            } else {
                $dbInsert = DB::table('fine_category')->insert([
                    'fc_category_name' => $categoryName,
                    'fc_description' => $description,
                    'entry_user_id' => $entryUserId,
                    'fc_status' => 'E'
                ]);

                if (!$dbInsert) {
                    throw new \Exception('DB Insertion Failed');
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Fine category inserted successfully',
                    'challanRec' => $this->getFineCategories()
                ]);
            }
        } catch (QueryException $e) {
            return response()->json(['success' => false, 'message' => 'Database Error: ' . $e->getMessage()]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function deleteFineCategory(Request $request){
        DB::beginTransaction();

        try {
              // First, delete related records in the child table
            // DB::table('fine_category_details')
            // ->where('FC_ID', $request->mainId)
            // ->delete();

            // DB::table('fine_category_permissions')
            //     ->where('FC_ID', $request->mainId)
            //     ->delete();


            // Then, delete the record in the parent table
            // DB::table('fine_category')
            //     ->where('ID', $request->mainId)
            //     ->delete();

            DB::table('fine_category_details')
            ->where('FC_ID', $request->mainId)
            ->update(['fcd_status' => 'D']);

            DB::table('fine_category_permissions')
            ->where('FC_ID', $request->mainId)
            ->update(['fcp_status' => 'D']);

            DB::table('fine_category')
                ->where('ID', $request->mainId)
                ->update(['fc_status' => 'D']);

            DB::commit();
            return response()->json([
                    'success' => true,
                    'data' => 'Deleted Successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'data' => 'Some problem occured'
            ]);
            // Handle the exception as needed
        }

    }
    public function getStudentFineRec(Request $request){
        $studentEnrollment = $request->studentEnrollment;
        $sql="

                SELECT sisusers.id, username, email,group_id,
                emp_f_name,emp_l_name , prefix, emp_f_name, emp_contact_no, emp_l_name emp_full_name,emp_email ,imagename emp_photo ,emp_school_id ,emp_sch.school_name AS emp_school_name,emp_dept.dept_name AS emp_dept_name,
                sai_spi_id, emp_employees.*,
                COALESCE(sai_spi_id,0) AS spi_id ,
                spi_enroll_prefix , spi_enroll_id , spi_name, spi_imagename, spi_fathername,

                spi_sess_id,
                sai_sdpo_sess_id   AS SPI_ENROLLMENT_SESSION,
                sai_sdpo_school_id,
                emp_employees.emp_type as sis_emp_type,
                sai_sdpo_dept_id   AS spi_dept_id ,
                sai_sdpo_prog_id   AS spi_prog_id ,
                sai_sdpo_shift_id,
                sai_sdpo_shift_id AS spi_shift_id ,
                sai_semester      AS sai_active_semester,
                sai_enrollment_no AS SPI_ENROLLMENT_NO ,
                sai_student_status, spi_mobile,  spi_email,
                (SELECT
                MAX(sc_co_sess_id)
                FROM
                    student_courses) spi_active_sess_id,
                (SELECT max(gpm_active_sessl_id) as gpm_active_sessl_id FROM group_pages_management WHERE gpm_school_id = sai_sdpo_school_id ) AS active_enroll_sess, api_token
            FROM sisusers
            LEFT JOIN emp_employees           ON LOWER(email)      = LOWER(emp_email)
            LEFT JOIN schools emp_sch         ON emp_school_id     = emp_sch.school_id
            LEFT JOIN departments emp_dept    ON emp_dept_id       = emp_dept.dept_id
            LEFT JOIN students_apply_info     ON LOWER(username)   = LOWER(sai_enrollment_no)
            LEFT JOIN students_personal_info  ON sai_spi_id        = spi_id
                                            AND sai_student_status!='Transferred'


         WHERE lower(sisusers.username)=lower('$studentEnrollment')  ";
         $data=DB::select($sql);
         $data=$data[0];


         $entryUserId = auth()->user()->id;
         $groupIds = [644];//privelliged group ids
         $usernames = ['asif_nadeem', 'esha.abid'];//privelliged usernames

         $groupId = auth()->user()->group_id;//current user id
         $username = auth()->user()->username;//current username

         $query = "
             SELECT
                 id,
                 sc_enrollment_no,
                 sc_app_id,
                 (SELECT sess_code FROM academic_sessions WHERE sess_id = sc_session_id) AS sess_name,
                 (SELECT sess_id FROM academic_sessions WHERE sess_id = sc_session_id) AS sess_id,
                 (SELECT fc_category_name FROM fine_category WHERE id = sc_category_id) AS category_name,
                (SELECT id FROM fine_category WHERE id = sc_category_id) AS category_id,
                 (SELECT fcd_violation_type FROM fine_category_details
                     WHERE fc_id = sc_category_id AND id = sc_violation_type_id) AS violation_type,
                (SELECT id FROM fine_category_details
                     WHERE fc_id = sc_category_id AND id = sc_violation_type_id) AS violation_id,
                 sc_amount,
                 created_at,
                 sc_challan_status,
                 sc_paid_on,
                 sc_feechallanno,
                 sc_validate_date,
                 SC_DUE_DATE
             FROM
                 student_challan
             WHERE
                 sc_enrollment_no = UPPER('$studentEnrollment')
                 and sc_challan_type='Fine'
                 ";

         if (!in_array($groupId, $groupIds) && !in_array($username, $usernames)) {
             $query .= " AND sc_entry_user_id = '" . auth()->user()->employee_id . "'";
         }

         $query .= " ORDER BY created_at DESC";
        //  echo $query;exit;

         $student_challan = DB::select($query);
        //  dd($data, $student_challan);






         return response()->json(['success' => true, 'getFinesData' => $data, 'student_challan'=>$student_challan,  'fineCategories'=>$this->getFineCategories(), 'sessions'=>$this->getSessions()]);

    }
    public function StudentFine(Request $request){

        return view('grader.StudentFines.studentFine');
    }
    protected function getSessions(){
        $sessions = DB::table('ACADEMIC_SESSIONS')
        ->select('*')
        ->where('SESS_STATUS', 'E')
        ->orderBy('SESS_ID', 'desc')
        ->get();
        return $sessions;
    }
    public function violationFines(Request $request){
        // dd($request->all());
        $fine = DB::table('fine_category_details')
        ->select('fcd_fine_amount')
        ->where('id', $request->violationType)
        ->where('fc_id', $request->fineCategoryOption)
        ->first();
        return $fine;
    }

    public function violationtypes(Request $request) {
        try {
            $fine = DB::table('fine_category_details')
                ->where('fc_id', $request->fineCategoryOption)
                ->where('fcd_status', 'E')
                ->get();
            return response()->json(['success' => true, 'data' => $fine]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error fetching violation types', 'error' => $e->getMessage()]);
        }
    }

    public function generateFineChallan(Request $request) {
        try {
            $userGroupId = auth()->user()->group_id;
            $requireSessionOnlyWhen = in_array($userGroupId, [644, 324]);//software team and accounts team
            $validator = Validator::make($request->all(), [
                'enrollment_no' => 'required',
                'session' => $requireSessionOnlyWhen ? 'required' : 'nullable',
                //'active_sess_id' => 'required',
                'fineCategoryOption' => 'required',
                'violationType' => 'required',
                'fineAmount' => 'required',
            ], [
                'enrollment_no.required' => 'enrollment no is required',
                'session.required' => 'Session is required',
                // 'active_sess_id.required' => 'active session id is required',
                'fineCategoryOption.required' => 'Category Option is required',
                'violationType.required' => 'Violation Type is required',
                'fineAmount.required' => 'Fine Amount is required',
            ]);

            // Check if validation fails
            if ($validator->fails()) {
                $errors = $validator->errors()->toArray();
                return response()->json(['success' => false, 'errors' => $errors], 422);
            }


            $enrollment_no = strtoupper($request->enrollment_no);
            $active_sess_id = $request->active_sess_id;
            if ($requireSessionOnlyWhen) {
                $session = $request->session;
            } else {
                $session = DB::table('student_courses')
                             ->max('sc_co_sess_id');
            }
            $fineCategoryOption = $request->fineCategoryOption;
            $violationType = $request->violationType;
            $fineAmount = $request->fineAmount;



            // $api_id = DB::table('students_apply_info')
            // ->leftJoin('admission_apply_info', 'students_apply_info.sai_api_id', '=', 'admission_apply_info.aai_api_id')
            // ->where('students_apply_info.sai_enrollment_no', $enrollment_no)
            // ->select('admission_apply_info.aai_id')
            // ->first();

            $api_id = DB::table('students_apply_info')
            ->leftJoin('admission_apply_info', function($join) {
                $join->on('students_apply_info.sai_api_id', '=', 'admission_apply_info.aai_api_id')
                     ->whereColumn('students_apply_info.sai_enrollment_no', '=', 'admission_apply_info.cms_sai_enrollment_no');
            })
            ->where('students_apply_info.sai_enrollment_no', $enrollment_no)
            ->selectRaw('COALESCE(admission_apply_info.aai_id, students_apply_info.sai_id) as aai_id')
            ->first();

           // dd($api_id);
            if (!$api_id) {
                return response()->json(['success' => false, 'message' => 'No API ID Found']);
            }

            // Use auth()->user()->employee_id instead of $entryUserId
            $entryUserId = auth()->user()->employee_id;

            $sql = "select sai_sdpo_school_id,
                    sai_sdpo_dept_id,
                    sai_sdpo_prog_id,
                    spi_name,
                    spi_fathername,
                    spi_guardian_cnic,
                    sai_student_status
                    from students_apply_info
                    left join students_personal_info on sai_spi_id = spi_id
                    where sai_enrollment_no = '$enrollment_no'";
            $res = DB::select($sql);

            //  dd($request->all()); // Uncomment this line for debugging if needed
            $dbInsert = DB::table('student_challan')->insertGetId([
                'sc_enrollment_no' => $enrollment_no,
                'sc_session_id' => $session,
                'sc_app_id' => $api_id->aai_id,
                'sc_category_id' => $fineCategoryOption,
                'sc_violation_type_id' => $violationType,
                'sc_amount' => $fineAmount,
                'invoiceamount' => $fineAmount,
                'sc_challan_type' => 'Fine',
                'sc_installment_allowed' => 'NO',
                'sc_student_status' => 'Existing',
                'sc_entry_user_id' => auth()->user()->employee_id,
                'sc_entry_user_name' => auth()->user()->username,
                'sc_payType' => 'ZindigiAPP',
                'sc_school_id' => $res[0]->sai_sdpo_school_id,
                'sc_department_id' => $res[0]->sai_sdpo_dept_id,
                'sc_program_id' => $res[0]->sai_sdpo_prog_id,
                'sc_studentname' => $res[0]->spi_name,
                'sc_fathername' => $res[0]->spi_fathername,
                'sc_guardian_cnic' => $res[0]->spi_guardian_cnic,
                'sc_sai_student_status' => $res[0]->sai_student_status,
                'sc_valid_date' => Carbon::now()->addMonths(6),
                'sc_due_date' => Carbon::now()->addDays(10)

            ]);

            $affected = DB::table('student_challan')
                        ->where('id',  $dbInsert)
                        ->update([
                         'challan_no'=> $dbInsert
                        ]);

            if (!$dbInsert) {
                return response()->json(['success' => false, 'message' => 'Challan Generation failed']);
            } else {
                return response()->json(['success' => true, 'message' => 'Challan Generated']);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error generating fine challan', 'error' => $e->getMessage()]);
        }
    }
    public function updateFineChallan(Request $request) {
        // dd($request->all());
        try {

            $validator = Validator::make($request->all(), [
                'edit_sess_id' => 'required',
                'edit_fineAmount' => 'required',
                'challan_id' => 'required',

            ], [
                'edit_sess_id.required' => 'session is required',
                'edit_fineAmount.required' => 'Fine amount is required',
                'challan_id.required' => 'Challan no is required',

            ]);

            // Check if validation fails
            if ($validator->fails()) {
                $errors = $validator->errors()->toArray();
                return response()->json(['success' => false, 'errors' => $errors], 422);
            }


            $session = $request->edit_sess_id;
            $fine = $request->edit_fineAmount;
            $challan_id = $request->challan_id;

            //  dd($request->all()); // Uncomment this line for debugging if needed
            $dbUpdateRow = DB::table('student_challan')
                ->where('id', $challan_id) // Add the condition to match the row by its ID
                ->update([
                    'sc_session_id' => $session,
                    'sc_amount' => $fine,
                    'invoiceamount' => $fine,
                ]);

            if ($dbUpdateRow > 0) {
                return response()->json(['success' => true, 'message' => 'Challan Updated']);
            } else {
                return response()->json(['success' => false, 'message' => 'Challan Generation failed']);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error generating fine challan', 'error' => $e->getMessage()]);
        }
    }
    public function fineChallanStudentList(Request $request){
        // dd(1111111111111);
        $student_challan = DB::select("
                        select
                        (prefix || ' ' || emp_f_name || ' ' ||emp_l_name) as entry_username,
                        id,
                        sc_enrollment_no,
                        (select sess_code from academic_sessions where sess_id=sc_session_id) as sess_name,
                        (select fc_category_name from fine_category where id=sc_category_id) as category_name,
                        (select fcd_violation_type from fine_category_details where fc_id=sc_category_id and id=sc_violation_type_id) violation_type ,
                        sc_amount,
                        created_at,sc_challan_status,sc_paid_on,
                        challan_no as sc_feechallanno
                        ,sc_validate_date
                        from student_challan st
                        left join emp_employees on st.sc_entry_user_id=emp_employees.employee_id
                ");
                $sql = " SELECT
                *
                 FROM
                fine_category";
                $resSql = DB::select($sql);

                    return view('grader.StudentFines.studentList',compact('student_challan','resSql'));
            }

        public function getStudentFineList(Request $request){

            $studentID = $request->studentID; // Assuming $request is an instance of Illuminate\Http\Request
             $challanNO = $request->get('challanNO');
              $fromDate = $request->get('fromDate'); // Assuming 'fromDate' is the parameter for the starting date                ,

             $toDate = $request->get('toDate');
             $paymentType = $request->get('paymenttype');
             $catgoryType = $request->get('catgoryType');
            // dd($paymentType);
           $studentQuery = "
           SELECT
               (prefix || ' ' || emp_f_name || ' ' ||emp_l_name) AS entry_username,
               id,
               sc_app_id,
               tranfee,
               callback_amount,
               callback_at,

                 (
                    CASE
                        WHEN sc_challan_type = 'Fee' THEN  (
                            SELECT
                                validdate
                            FROM
                                mv_feechallanwisesummary
                            WHERE
                                buyercodechallanno = challan_no

                        ) else sc_valid_date
                    END
                ) AS sc_validate_date,
                (
                    CASE
                        WHEN sc_challan_type = 'Fee' THEN  (
                        SELECT
                            duedate
                        FROM
                            mv_feechallanwisesummary
                        WHERE
                            buyercodechallanno = challan_no
                        ) else sc_due_date
                    end
                ) AS duedate,
               student_challan.sc_enrollment_no,
               (SELECT sess_code FROM academic_sessions WHERE sess_id=sc_session_id) AS sess_name,
               (SELECT fc_category_name FROM fine_category WHERE id = sc_category_id) AS category_name,
               sc_challan_type,
                (SELECT fcd_violation_type FROM fine_category_details WHERE fc_id=sc_category_id AND id=sc_violation_type_id) AS violation_type,
                 CASE
                    WHEN sc_amount IS NOT NULL THEN sc_amount
                    ELSE CASE
                        WHEN REGEXP_LIKE(invoiceamount, '^[0-9]+(\.[0-9]+)?$') THEN TO_NUMBER(invoiceamount)
                        ELSE NULL
                    END
                END AS sc_amount,
               created_at,
               sc_challan_status,
               response_paidon,
               challan_no as sc_feechallanno,
               response_status,
               sc_paytype,
               CASE
                    WHEN REGEXP_LIKE(invoiceamount, '^[0-9]+(\.[0-9]+)?$') THEN TO_NUMBER(invoiceamount)
                    ELSE NULL
                END AS invoiceamount,
               fine,
               sc_payment_mode,
                (select course_title from   SC_SHORTCOURSE_APPLIED
                 LEFT JOIN SC_SHORTCOURSES ON SC_SHORTCOURSES.id = course_id
                where     to_char(applicant_id) = student_challan.sc_enrollment_no and course_id is not null and rownum = 1) as course_name

           FROM
               student_challan
           LEFT JOIN
               emp_employees ON student_challan.sc_entry_user_id=emp_employees.employee_id
           WHERE
                sc_challan_type in ('Full_semester','Admission_Fee','Fine','Fee','Processing_Fee','short_Fee','Hostel_Fee','Success_Lab','Summer_School','Gym','Credit_hours','Short/Summer_Course','Fee_Refund','shortcourse', 'donation', 'misc_fee') and sc_challan_status ='PAID'
                and invoice_status = 'E'";

        $bindings = [];

        if (isset($studentID) && $studentID !== null) {
        //    $studentQuery .= " AND sc_enrollment_no = ?";
        //    $bindings[] = $studentID;
            $studentQuery .= " AND (sc_enrollment_no = '$studentID' OR to_char(sc_app_id) = '$studentID')";
        }
        if (isset($paymentType) && $paymentType !== null) {
           // $studentQuery .= " AND sc_paytype = '$paymentType'";
            // $bindings[] = $paymentType;

            if ($paymentType == 'Askari') {
                $studentQuery .= " AND sc_paytype IN ('Askari', 'ASKRI')";
            } elseif (!empty($paymentType)) {
                $studentQuery .= " AND sc_paytype = ?";
                $bindings[] = $paymentType;
            }
        }
        if (isset($challanNO) && $challanNO !== null) {
           $studentQuery .= " AND challan_no = ? ";
           $bindings[] = $challanNO;
        }
        if (isset($catgoryType) && $catgoryType !== null) {
            $studentQuery .= " AND sc_category_id = ? ";
            $bindings[] = $catgoryType;
        }

        if (isset($fromDate) && isset($toDate)) {
            // Assuming 'fromDate' and 'toDate' are in 'Y-m-d' format
            // $studentQuery .= " AND callback_at BETWEEN ? AND ?";
            // $bindings[] = Carbon::parse($fromDate)->format('Y-m-d');
            // $bindings[] = Carbon::parse($toDate)->format('Y-m-d');

            $date = Carbon::createFromFormat('Y-m-d', $fromDate);
            $formattedDate = $date->format('d-M-y');
            $finalFromDate = strtoupper($formattedDate); // Output: 03-Apr-24
            $date = Carbon::createFromFormat('Y-m-d', $toDate);
            $formattedDate2 = $date->format('d-M-y');
            $finalToDate = strtoupper($formattedDate2);


            $studentQuery .= " AND TRUNC(callback_at) BETWEEN TO_DATE('$finalFromDate', 'DD-MON-RR') AND TO_DATE('$finalToDate', 'DD-MON-RR')";
        }

      //  echo $studentQuery;exit;

        $student = DB::select($studentQuery, $bindings);

             return response()->json(['success' => true, 'student_challan'=>$student]);

        }

        public function reSyncnStudentList(Request $request){
            $challanNO = $request->get('challanNO');

            $validator = Validator::make($request->all(), [
                'fromDate' => 'required',
                'toDate' => 'required',
            ]);

            if ($request->get('type') == 'ubl_card') {
                return $this->handleUBLsync($fromDate, $toDate);
            }

            // Check if validation fails
            if ($validator->fails()) {
                $errors = $validator->errors()->toArray();
                return response()->json(['success' => false, 'errors' => $errors], 422);
            }
           $fromDate = Carbon::parse($request->get('fromDate'))->format("Y-m-d");
           $toDate = Carbon::parse($request->get('toDate'))->format("Y-m-d");

           $blinqToken = $this->getFinalToken();
           $blinqInvoice = $this->genInvoiceOnBlinq($blinqToken, $fromDate,$toDate);



           if(count($blinqInvoice)>0){

               foreach($blinqInvoice as $rec){

                   $blinqInvoiceStatus = $this->genInvoiceStatusOnBlinq( $blinqToken,$rec["InvoiceNumber"]);
                  //dd( $blinqInvoiceStatus[0]["PaymentCode"],$blinqInvoiceStatus );
                   //  $sel= DB::select("select * from student_challan where challan_no='79388'");
                   $callbackDate = (isset($blinqInvoiceStatus[0]["InvoicePaidOn"]) && $blinqInvoiceStatus[0]["InvoicePaidOn"] != "") ? $blinqInvoiceStatus[0]["InvoicePaidOn"] : $blinqInvoiceStatus[0]["InvoiceIssueDate"];

                   // if($sel[0]->id){
                    //    $affected = DB::table('student_challan')
                    //    ->where('paymentcode',  $blinqInvoiceStatus[0]["PaymentCode"])
                    //    ->update([
                    //     'invoice_status'=>'E',
                    //     'sc_payment_mode'=> $blinqInvoiceStatus[0]["PaymentMode"],
                    //     'response_status'=>'Success',
                    //     'sc_paytype'=>'blinq_app',
                    //     'callback_at' => $callbackDate->format('Y-m-d H:i:s'),
                    //     'callback_paid_on' => $callbackDate->format('Y-m-d H:i:s'),
                    //     'callback_amount' =>$blinqInvoiceStatus[0]["InvoiceAmount"]

                    //    ]);
                        $amount=$rec["AmountPaid"];
                        $paymentCode=$blinqInvoiceStatus[0]["PaymentCode"];
                        $paymentMode=$blinqInvoiceStatus[0]["PaymentMode"];
                        $InvoiceNumber=$rec["InvoiceNumber"];



                       $sql = "UPDATE student_challan
                       SET
                           callback_paid_on = to_Date('$callbackDate', 'Day, DD Month YYYY HH24:MI:SS'),
                           callback_amount ='$amount',
                           sc_payment_mode='$paymentMode',
                           CALLBACK_AT = to_Date('$callbackDate', 'Day, DD Month YYYY HH24:MI:SS'),
                           response_status ='Success',
                           sc_paytype='blinq_app',
                           invoice_status='E',
                           sc_challan_status ='PAID'
                       WHERE paymentcode ='$paymentCode'";

                      // echo $sql;exit;
                         $res = DB::insert($sql);

                         $studentRec = DB::table('student_challan')
                         ->select('*')
                         ->where('paymentcode', $paymentCode)
                         ->first();
                         echo print_r($InvoiceNumber, true) . '<br>';
                         if ($studentRec && $studentRec->sc_enrollment_no) {

                            $procedurenotification = 'UNBLOCK_STUDENT_ONLINE_PAY';
                            $bindings_notification = [
                                'enrollment_No' => $studentRec->sc_enrollment_no,
                                'challan_no'    => $InvoiceNumber
                            ];

                            // $result_notification = DB::executeProcedure($procedurenotification, $bindings_notification);

                         }




                   // };
               }


           }

           if (!$sql) {
            return response()->json(['success' => false, 'message' => 'Challan Sync failed']);
            } else {
                return response()->json(['success' => true, 'message' => 'Challan Sync']);
            }

        }


private function handleUBLsync($fromDate, $toDate)
{


    // Fetch challans within date range
    $results = DB::table('student_challan')
            ->orWhere('clicktopayurl', 'like', '%https://ipg.comtrust.ae/%')
            ->whereNull('callback_paid_on')
            ->orWhere('sc_paytype', 'ubl_card')
            ->where('invoice_status', 'E')
            ->get();


    foreach ($results as $challan) {
         $transactionID = $challan->invoicenumber; // fallback or make dynamic

        $payload = [
            "QueryTransaction" => [
                "TransactionID" => $transactionID,
                "Customer" => "BEACONHOUSE UNIVERSITY",
                "UserName" => "BHU_Zain",
                "Password" => "BeAcon(}@House{){BHU"
            ]
        ];

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post('https://ipg.comtrust.ae:2443', $payload);

            $json = $response->json();

            if ($response->status() === 200 && isset($json['Transaction']['Status']['Name']) && in_array($json['Transaction']['Status']['Name'], ['Closed', 'Open'])) {
                $date = $json['Transaction']['Details']['Detail'][2]['Date'] ?? null;
                $amount = $json['Transaction']['Transaction']['Amount']['Value'] ?? null;
                $invoiceNumber = $json['Transaction']['Transaction']['OrderID'] ?? null;



                $sql = "UPDATE student_challan
                SET
                    callback_paid_on = TO_TIMESTAMP(?, 'YYYY-MM-DD HH24:MI:SS'),
                    callback_amount = ?,
                    callback_at = TO_TIMESTAMP(?, 'YYYY-MM-DD HH24:MI:SS'),
                    response_status = 'Success',
                    sc_challan_status = 'PAID',
                    sc_paytype = 'ubl_card',
                    invoice_status = 'E'
                WHERE id = ?";


              $res= DB::update($sql, [$date, $amount, $date, $invoiceNumber]);
              //dd( $date, $amount,$invoiceNumber,$res);
            }



        } catch (\Exception $e) {
            $results[] = [
                'challan_no' => $challan->invoicenumber,
                'transaction_id' => $transactionID,
                'error' => $e->getMessage(),
            ];
        }
    }

    return response()->json([
        'success' => true,
        'data' => $results
    ]);
    return response()->json(['success' => true, 'message' => 'Challan Sync (UBL) - logic pending']);
}


    protected function getFinalToken(){
            $blinqToken = '';
            $blinq = $this->getTokenFromDB();
            $this->getTokenFromDB();
            $valid_till = Carbon::parse($blinq->valid_till);
            // dd($valid_till,Carbon::now().',Carbon::parse($valid_till)->lessThan(Carbon::now()));
            if($valid_till != null && !Carbon::parse($valid_till)->lessThan(Carbon::now())){
              $blinqToken  = $blinq->auth_token;
            }else{
                $blinqToken = $this->getToken();
                if(!$blinqToken->original['error']){
                    $blinqToken = $blinqToken->original['token'];
                }else{
                    return false;
                }
            }
            return $blinqToken;
        }

        protected function  getTokenFromDB(){
            $sql = "Select * from epay where provider  = 'Blinq' and account_type='forward_charges' ";

            $res = DB::select($sql);
            return $res[0];
        }


        protected function genInvoiceOnBlinq($token,$from,$to){


       // dd(response()->json($data));
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'token' => $token
            ])->get('https://api.blinq.pk/invoice/getpaidinvoices?startDate='.$from.'&endDate='.$to);

            if($response->successful()){

                return $response['ResponseDetail'];
           }

        }

        protected function genInvoiceStatusOnBlinq($token,$invoiceNumber){

        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'token' => $token
        ])->get('https://api.blinq.pk/invoice/getstatus?InvoiceNumber='.$invoiceNumber);
        if($response->successful()){
        //dd($response['ResponseDetail']);
            return $response['ResponseDetail'];
       }

    }

    protected function  getToken(){
        $sql = "Select * from epay where provider  = 'Blinq' and account_type='forward_charges'";
        $res = DB::select($sql);

        $clientID = $res[0]->clientid ;
        $clientSecret = $res[0]->clientsecret;

        $response = Http::withHeaders([
            // Any additional headers you may need
            'Accept' => 'application/json',
        ])->post('https://api.blinq.pk/api/Auth', [
            'ClientID' => $clientID,
            'ClientSecret' => $clientSecret,
            // Any other query parameters required by the API
        ]);
        $validTill = $this->updateToken($response->header('token'),$response->header('TokenExpiry'));

        if($response->successful()){
            return response()->json([
                    'error' =>false,
                    'token' =>$response->header('token'),
                    'valid_till' => $validTill

                ]);
        }else{
            return false;
        }
    }
    protected function updateToken($token,$time){
        $currentTime = Carbon::now();
        $validTill = $currentTime->addMinutes($time);
        $validTill = $validTill->format('y-M-d h:i:s');
        // dd($validTill);
        $sql ="UPDATE epay set AUTH_TOKEN = ' $token ', VALID_TILL = ' $validTill ' where provider = 'Blinq' and account_type='forward_charges'";
        // echo $sql ; exit;
        $res = DB::update($sql);
        return $validTill ;
    }
    public function getPrograms($dept_id)
{
    $programs = DB::table('programs')
        ->select('prog_id', 'prog_name')
        ->where('prog_dept_id', $dept_id)
        ->orderBy('prog_name')
        ->get();

    return response()->json($programs);
}
    public function cancelChallans(Request $request) {
        // Get the challan ID and remarks from the request
        // dd($request->all());
        $id = $request->challanId;
        $remarks = $request->remarks;

        try {
            // Start a database transaction
            DB::beginTransaction();

            // Update the challan status to 'CANCELLED'
            DB::table('student_challan')
                ->where('id', $id)
                ->update([
                    'SC_CHALLAN_STATUS' => 'CANCELLED',
                    'UPDATED_AT' => Carbon::now(),
                    'sc_cancelled_by_user' => auth()->user()->username,
                    'sc_cancel_remarks' => $remarks
                ]);

            // Commit the transaction
            DB::commit();

            // Return a successful response
            return response()->json(['success' => true, 'message' => 'Challan Cancelled Successfully']);
        } catch (Exception $e) {
            // Rollback the transaction in case of an error
            DB::rollBack();

            // Return an error response with the exception message
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function activateChallans(Request $request){
        // dd($request->challanId);
        $id = $request->challanId;
        // $user_id = auth()->user()->username;
        try {
            // Start a database transaction
            DB::beginTransaction();
            // Update the challan status to 'CANCELLED'
            DB::table('student_challan')
                ->where('id', $id)
                 ->update([
                        'SC_CHALLAN_STATUS' => 'UNPAID',
                        'UPDATED_AT' => Carbon::now(),
                        'sc_cancelled_by_user' => auth()->user()->username,
                        ]);
            // Commit the transaction
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Challan Activated Successfully']);
        } catch (Exception $e) {
            // Rollback the transaction on error
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
