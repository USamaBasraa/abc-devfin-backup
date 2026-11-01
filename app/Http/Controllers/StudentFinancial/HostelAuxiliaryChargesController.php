<?php

namespace App\Http\Controllers\StudentFinancial;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HostelAuxiliaryChargesController extends Controller
{
    protected array $campusRows = [
        'on_campus' => 'BNU',
        'bedian' => 'BEDIAN',
        'safari' => 'Safari',
    ];

    public function index()
    {
        $sessions = DB::table('ACADEMIC_SESSIONS')
            ->select('SESS_ID as sess_id', 'SESS_NAME as sess_name')
            ->where('SESS_STATUS', 'E')
            ->orderBy('SESS_ID', 'desc')
            ->get();

        return view('StudentFinancial.hostel_auxiliary_charges', compact('sessions'));
    }

    public function getReportData(Request $request)
    {
        try {
            $sessionId = (int) $request->input('session_id');
            if ($sessionId <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Session is required.',
                    'data' => [],
                ], 400);
            }

            $rows = $this->buildReportRows(
                $sessionId,
                $request->input('from_date'),
                $request->input('to_date'),
                (string) $request->input('hostel_type', 'all'),
                (string) $request->input('challan_type', 'all'),
                (string) $request->input('hostel_campus', 'all')
            );

            return response()->json([
                'success' => true,
                'data' => $rows,
            ]);
        } catch (Exception $e) {
            Log::error('Hostel auxiliary charges report error: ' . $e->getMessage(), [
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Unable to load hostel and auxiliary charges report.',
                'error' => $e->getMessage(),
                'data' => [],
            ], 500);
        }
    }

    protected function buildReportRows(
        int $sessionId,
        $fromDate,
        $toDate,
        string $hostelType,
        string $challanType,
        string $hostelCampus
    ): array {
        $campusExpr = $this->mapHostelChallanCampusSql('sc');
        $amountExpr = $this->challanAmountSql('sc');
        $bindings = ['session_id' => $sessionId];

        $activeWhere = "
            AND sc.invoice_status = 'E'
            AND sc.sc_challan_type = 'Hostel_Fee'
            AND sc.sc_session_id = :session_id
            AND sc.sc_hostel_category IN ('BH', 'GH')
            AND UPPER(TRIM(sc.sc_challan_status)) IN ('PAID', 'UNPAID')
            AND NOT EXISTS (
                SELECT 1
                FROM fin_student_cancel_challan cancel_ch
                WHERE cancel_ch.challan_no = sc.challan_no
            )
        ";

        $cancelHostelCategory = $this->cancelChallanHostelCategorySql();
        $cancelHostelFeeType = $this->cancelChallanFeeTypeSql();
        $cancelHasReference = $this->cancelChallanHasReferenceSql();
        $cancelCampusExpr = $this->mapCancelChallanCampusSql();
        $cancelFromSql = $this->cancelChallanFromSql();

        $cancelWhere = "
            AND c.sc_challan_type = 'Hostel_Fee'
            AND c.sc_session_id = :session_id
            AND (
                NOT {$cancelHasReference}
                OR {$cancelHostelCategory} IN ('BH', 'GH')
            )
        ";

        $activeWhere .= $this->appendDateFilter('sc.sc_issu_date', $fromDate, $toDate, $bindings, 'active');
        $cancelWhere .= $this->appendDateFilter(
            'NVL(c.sc_issu_date, c.created_at)',
            $fromDate,
            $toDate,
            $bindings,
            'cancel'
        );

        if ($hostelType !== 'all' && in_array(strtoupper($hostelType), ['BH', 'GH'], true)) {
            $activeWhere .= ' AND sc.sc_hostel_category = :hostel_type ';
            $cancelWhere .= " AND {$cancelHostelCategory} = :hostel_type ";
            $bindings['hostel_type'] = strtoupper($hostelType);
        }

        $challanTypeLabel = 'All';
        if ($challanType === 'auxiliary') {
            $activeWhere .= " AND sc.sc_hostel_fee_type = 'Hostel_other_utilities' ";
            $cancelWhere .= " AND {$cancelHostelFeeType} = 'Hostel_other_utilities' ";
            $challanTypeLabel = 'Auxiliary';
        } elseif ($challanType === 'hostel') {
            $activeWhere .= " AND NVL(sc.sc_hostel_fee_type, '-') <> 'Hostel_other_utilities' ";
            $cancelWhere .= " AND {$cancelHasReference}
                AND NVL({$cancelHostelFeeType}, '-') <> 'Hostel_other_utilities' ";
            $challanTypeLabel = 'Hostel_Fee';
        } else {
            $challanTypeLabel = 'All';
        }

        $campusFilter = $this->normalizeCampusFilter($hostelCampus);
        if ($campusFilter !== null) {
            $activeWhere .= ' AND (' . $campusExpr . ') = :campus_filter ';
            $cancelWhere .= ' AND (' . $cancelCampusExpr . ') = :campus_filter ';
            $bindings['campus_filter'] = $campusFilter;
        }

        $activeRows = DB::select("
            SELECT
                campus_key,
                COUNT(*) AS fee_bills_issued,
                SUM(amount) AS amount,
                SUM(CASE WHEN challan_status = 'PAID' THEN 1 ELSE 0 END) AS paid,
                SUM(CASE WHEN challan_status = 'UNPAID' THEN 1 ELSE 0 END) AS unpaid
            FROM (
                SELECT
                    {$campusExpr} AS campus_key,
                    UPPER(TRIM(sc.sc_challan_status)) AS challan_status,
                    {$amountExpr} AS amount
                FROM student_challan sc
                WHERE 1 = 1
                {$activeWhere}
            ) active_base
            GROUP BY campus_key
        ", $bindings);

        $cancelRows = DB::select("
            SELECT
                campus_key,
                COUNT(*) AS cancelled
            FROM (
                SELECT
                    {$cancelCampusExpr} AS campus_key
                {$cancelFromSql}
                WHERE 1 = 1
                {$cancelWhere}
            ) cancel_base
            GROUP BY campus_key
        ", $bindings);

        $summary = [];
        foreach (array_keys($this->campusRows) as $campusKey) {
            $summary[$campusKey] = [
                'fee_bills_issued' => 0,
                'amount' => 0.0,
                'cancelled' => 0,
                'paid' => 0,
                'unpaid' => 0,
            ];
        }

        foreach ($activeRows as $row) {
            $campusKey = strtolower((string) ($row->campus_key ?? $row->CAMPUS_KEY ?? ''));
            if (!isset($summary[$campusKey])) {
                continue;
            }

            $summary[$campusKey]['fee_bills_issued'] = (int) ($row->fee_bills_issued ?? $row->FEE_BILLS_ISSUED ?? 0);
            $summary[$campusKey]['amount'] = (float) ($row->amount ?? $row->AMOUNT ?? 0);
            $summary[$campusKey]['paid'] = (int) ($row->paid ?? $row->PAID ?? 0);
            $summary[$campusKey]['unpaid'] = (int) ($row->unpaid ?? $row->UNPAID ?? 0);
        }

        foreach ($cancelRows as $row) {
            $campusKey = strtolower((string) ($row->campus_key ?? $row->CAMPUS_KEY ?? ''));
            if (!isset($summary[$campusKey])) {
                continue;
            }

            $summary[$campusKey]['cancelled'] = (int) ($row->cancelled ?? $row->CANCELLED ?? 0);
        }

        $hostelTypeLabel = $hostelType === 'all' ? 'All' : strtoupper($hostelType);
        $rows = [];

        foreach ($this->campusRows as $campusKey => $campusLabel) {
            if ($campusFilter !== null && $campusKey !== $campusFilter) {
                continue;
            }

            $metrics = $summary[$campusKey];
            $rows[] = [
                'hostel_campus' => $campusLabel,
                'challan_type' => $challanTypeLabel,
                'hostel_type' => $hostelTypeLabel,
                'fee_bills_issued' => $metrics['fee_bills_issued'],
                'amount' => number_format($metrics['amount'], 0, '.', ','),
                'amount_raw' => $metrics['amount'],
                'cancelled' => $metrics['cancelled'],
                'paid' => $metrics['paid'],
                'unpaid' => $metrics['unpaid'],
            ];
        }

        return $rows;
    }

    protected function mapHostelChallanCampusSql(string $alias = 'sc'): string
    {
        return "CASE
            WHEN {$alias}.sc_session_type IS NULL
                 OR LOWER(TRIM({$alias}.sc_session_type)) IN ('regular', 'non-regular') THEN 'on_campus'
            WHEN LOWER(TRIM({$alias}.sc_session_type)) LIKE '%bedian%' THEN 'bedian'
            WHEN LOWER(TRIM({$alias}.sc_session_type)) LIKE '%outside%' THEN 'safari'
            ELSE 'on_campus'
        END";
    }

    protected function mapCancelChallanCampusSql(): string
    {
        $sessionType = $this->cancelChallanSessionTypeSql();
        $hasReference = $this->cancelChallanHasReferenceSql();

        return "CASE
            WHEN NOT {$hasReference} THEN 'on_campus'
            WHEN {$sessionType} IS NULL
                 OR LOWER(TRIM({$sessionType})) IN ('regular', 'non-regular') THEN 'on_campus'
            WHEN LOWER(TRIM({$sessionType})) LIKE '%bedian%' THEN 'bedian'
            WHEN LOWER(TRIM({$sessionType})) LIKE '%outside%' THEN 'safari'
            ELSE 'on_campus'
        END";
    }

    protected function cancelChallanFromSql(): string
    {
        return "
            FROM fin_student_cancel_challan c
            LEFT JOIN student_challan sc_ref ON sc_ref.id = c.sc_id
            LEFT JOIN student_challan sc_ref_no ON sc_ref.id IS NULL
                AND c.challan_no IS NOT NULL
                AND sc_ref_no.challan_no = c.challan_no
        ";
    }

    protected function cancelChallanHasReferenceSql(): string
    {
        return '(sc_ref.id IS NOT NULL OR sc_ref_no.id IS NOT NULL)';
    }

    protected function cancelChallanHostelCategorySql(): string
    {
        return 'NVL(sc_ref.sc_hostel_category, sc_ref_no.sc_hostel_category)';
    }

    protected function cancelChallanSessionTypeSql(): string
    {
        return 'NVL(sc_ref.sc_session_type, sc_ref_no.sc_session_type)';
    }

    protected function cancelChallanFeeTypeSql(): string
    {
        return 'NVL(sc_ref.sc_hostel_fee_type, sc_ref_no.sc_hostel_fee_type)';
    }

    protected function challanAmountSql(string $alias = 'sc'): string
    {
        return "NVL(
            TO_NUMBER(NULLIF(REGEXP_REPLACE(TO_CHAR({$alias}.invoiceamount), '[^0-9.]', ''), '')),
            NVL(TO_NUMBER(NULLIF(REGEXP_REPLACE(TO_CHAR({$alias}.sc_amount), '[^0-9.]', ''), '')), 0)
        )";
    }

    protected function appendDateFilter(
        string $column,
        $fromDate,
        $toDate,
        array &$bindings,
        string $prefix
    ): string {
        $sql = '';

        if (!empty($fromDate)) {
            $key = $prefix . '_from_date';
            $sql .= " AND TRUNC({$column}) >= TO_DATE(:{$key}, 'YYYY-MM-DD') ";
            $bindings[$key] = $fromDate;
        }

        if (!empty($toDate)) {
            $key = $prefix . '_to_date';
            $sql .= " AND TRUNC({$column}) <= TO_DATE(:{$key}, 'YYYY-MM-DD') ";
            $bindings[$key] = $toDate;
        }

        return $sql;
    }

    protected function normalizeCampusFilter(string $hostelCampus): ?string
    {
        $campus = strtolower(trim($hostelCampus));

        if ($campus === '' || $campus === 'all') {
            return null;
        }

        $map = [
            'bnu' => 'on_campus',
            'on_campus' => 'on_campus',
            'bedian' => 'bedian',
            'safari' => 'safari',
        ];

        return $map[$campus] ?? null;
    }
}
