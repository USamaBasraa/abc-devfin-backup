<?php

namespace App\Http\Controllers\Grader\BNUReportsGenerate\BI;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Carbon\Carbon;

class BIReportController extends Controller
{
    public function BIReport(Request $request)
    {
        $data = $this->biReportPageData($request);

        if ($request->boolean('bi_ajax')) {
            $charts = collect($data['biDatasets'])->map(function ($d) {
                return $d['charts'] ?? [];
            })->all();

            return response()->json([
                'html' => view('grader.reportgenerator.BI.dashboard-body', $data)->render(),
                'charts' => $charts,
                'firstTabKey' => $data['firstTabKey'],
                'biFilter' => $data['biFilter'],
            ]);
        }

        return view('grader.reportgenerator.BI.index', $data);
    }

    /**
     * AJAX: program-wise breakdown for a school in Admission Grand Summary.
     * GET params: school_id, date (YYYY-MM-DD, optional), sess_id (optional)
     */
    public function biGrandSummarySchoolBreakdown(Request $request): \Illuminate\Http\JsonResponse
    {
        $table    = 'OLAP_ADMISSION_GRAND_SUMMARY_REPORT';
        $schoolId = $request->input('school_id');
        if ($schoolId === null || $schoolId === '') {
            return response()->json(['details' => []]);
        }

        $day = $this->biDateFromRequest($request, ['date', 'date_to', 'date_from']);
        $futureDateError = self::biFutureDateSelectionMessage([
            'date_from' => $day->toDateString(),
        ]);
        if ($futureDateError !== null) {
            return response()->json([
                'details' => [],
                'error' => $futureDateError,
                'school_id' => $schoolId,
                'date' => $day->toDateString(),
            ]);
        }

        try {
            // Detect real column names from schema — avoids Oracle "invalid identifier" errors.
            $columnKeys = \Schema::hasTable($table)
                ? \Schema::getColumnListing($table)
                : [];

            $dims   = $this->biResolveDimensionColumns($table, $columnKeys);
            $metric = $this->biPickMetricColumn($table, $columnKeys);

            $schoolDim = $dims['school'] ?? null;
            $progDim   = $dims['prog']   ?? null;
            $dateCol   = $dims['date']   ?? null;
            $sessCol   = $dims['session_fk'] ?? null;

            if (! $schoolDim || ! $progDim) {
                return response()->json(['details' => [], 'error' => 'Could not detect school/program columns. Found: '.implode(', ', $columnKeys)]);
            }

            $start = (clone $day)->startOfDay()->toDateTimeString();
            $end   = (clone $day)->endOfDay()->toDateTimeString();

            $q = DB::table($table);
            $q->whereRaw($schoolDim.' = ?', [$schoolId]);
            if ($dateCol) {
                $q->whereRaw($dateCol.' >= ?', [$start]);
                $q->whereRaw($dateCol.' <= ?', [$end]);
            }

            $sessId = $request->input('sess_id');
            if ($sessId && $sessCol) {
                $q->whereRaw($sessCol.' = ?', [$sessId]);
            }

            $metricExpr = $metric ? 'SUM('.$metric.')' : 'COUNT(*)';

            $q->selectRaw($progDim.' as prog_key, '.$metricExpr.' as val')
              ->groupBy($progDim)
              ->orderByRaw($metricExpr.' DESC');

            $rows = $q->get();

            $progIsId = $this->biIsProgramIdColumn($progDim);
            $programNames = $progIsId ? DB::table('programs')->pluck('prog_name', 'prog_id') : collect();

            $details = [];
            foreach ($rows as $row) {
                $arr = $this->biRowArrayLower($row);
                $progRaw = $arr['prog_key'] ?? null;
                $count   = (float) ($arr['val'] ?? 0);
                if ($count <= 0) {
                    continue;
                }
                if ($progIsId && $progRaw !== null) {
                    $progLabel = $programNames[$progRaw] ?? ('Program '.$progRaw);
                } else {
                    $progLabel = (string) ($progRaw ?? 'No program');
                }
                $details[] = [
                    'program' => $progLabel,
                    'count'   => $count,
                ];
            }

            return response()->json([
                'school_id'  => $schoolId,
                'date'       => $day->toDateString(),
                'prog_col'   => $progDim,
                'school_col' => $schoolDim,
                'details'    => $details,
                'total'      => count($details),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'details'   => [],
                'error'     => $e->getMessage(),
                'school_id' => $schoolId,
                'date'      => $day->toDateString(),
            ]);
        }
    }

    /**
     * Pick the first valid date from request keys, otherwise use today.
     *
     * @param  array<int, string>  $keys
     */
    private function biDateFromRequest(Request $request, array $keys): Carbon
    {
        foreach ($keys as $key) {
            $value = $request->input($key);
            if ($value === null || $value === '') {
                continue;
            }
            try {
                return Carbon::parse($value);
            } catch (\Throwable $e) {
                break;
            }
        }

        return Carbon::today();
    }

    /**
     * Returns a user-facing message when any selected BI filter date is after today.
     * Empty / invalid values are ignored. Does not alter request state.
     *
     * @param  array{date_from?:mixed, date_to?:mixed, compare_date_a?:mixed, compare_date_b?:mixed}  $dates
     */
    public static function biFutureDateSelectionMessage(array $dates, ?Carbon $today = null): ?string
    {
        $today = ($today ?? Carbon::today())->copy()->startOfDay();
        $keys = ['date_from', 'date_to', 'compare_date_a', 'compare_date_b'];

        foreach ($keys as $key) {
            $value = $dates[$key] ?? null;
            if ($value === null || $value === '') {
                continue;
            }
            try {
                if (Carbon::parse($value)->startOfDay()->gt($today)) {
                    return 'You have selected a future date. Please select a valid past or current date.';
                }
            } catch (\Throwable $e) {
                continue;
            }
        }

        return null;
    }

    /**
     * Empty dataset used when a future date was selected (skip OLAP; avoid zero-row empty copy).
     *
     * @return array<string, mixed>
     */
    private function biFutureDateBlockedDataset(string $table, string $label): array
    {
        return [
            'label' => $label,
            'table' => $table,
            'columns' => [],
            'rows' => collect(),
            'displayed' => 0,
            'total' => null,
            'error' => null,
            'stats' => [
                'row_count' => 0,
                'metric_sum' => null,
                'metric_label' => null,
                'extra_kpis' => [],
                'compare_kpis' => null,
                'range_pivot' => null,
                'fee_challan_summary' => null,
            ],
            'filter_dims' => [],
            'charts' => [],
        ];
    }

    /**
     * Oracle rows can return odd key casing/control chars; normalize once.
     *
     * @return array<string, mixed>
     */
    private function biRowArrayLower($row): array
    {
        $out = [];
        foreach ((array) $row as $k => $v) {
            $out[strtolower(ltrim((string) $k, chr(12)))] = $v;
        }

        return $out;
    }

    private function biIsProgramIdColumn(?string $column): bool
    {
        return in_array(strtolower((string) $column), [
            'prog_id',
            'program_id',
            'sc_prog_id',
            'sc_program_id',
            'sdpo_prog_id',
            'fk_prog_id',
            'sai_sdpo_prog_id',
        ], true);
    }

    private function biFeeStudentStrengthTotal(
        float $paid,
        float $revisedUnpaid,
        float $frozen,
        float $frozenPaid,
        float $scholarshipDateExtend,
        float $springIntake
    ): float {
        return $paid + $revisedUnpaid + $frozen + $frozenPaid + $scholarshipDateExtend + $springIntake;
    }

    /**
     * From/To range and Compare A/B are mutually exclusive.
     * When both are present (often because session auto-fills today into From/To while Compare is set),
     * keep Compare A/B and scope the main grid to Compare date B (single-day snapshot) so range +
     * compare panels are not mixed. When only a range is present, clear leftover compare dates.
     */
    private function biNormalizeExclusiveDateModes(Request $request): void
    {
        $da = $request->input('compare_date_a');
        $db = $request->input('compare_date_b');
        $daSet = $da !== null && $da !== '';
        $dbSet = $db !== null && $db !== '';

        // Incomplete Compare (only A or only B) — drop it so it cannot block today-default / range mode.
        if ($daSet xor $dbSet) {
            $request->query->remove('compare_date_a');
            $request->query->remove('compare_date_b');
            $request->request->remove('compare_date_a');
            $request->request->remove('compare_date_b');
            $request->merge([
                'compare_date_a' => null,
                'compare_date_b' => null,
            ]);
            $daSet = false;
            $dbSet = false;
        }

        $hasCompare = $daSet && $dbSet;

        $df = $request->input('date_from');
        $dt = $request->input('date_to');
        $hasRange = ($df !== null && $df !== '') || ($dt !== null && $dt !== '');

        if ($hasCompare) {
            // Main grid / charts: snapshot on Compare date B. Compare panel strips these and uses A vs B.
            $scopeDay = $db;
            $request->query->set('date_from', $scopeDay);
            $request->query->set('date_to', $scopeDay);
            $request->request->set('date_from', $scopeDay);
            $request->request->set('date_to', $scopeDay);
            $request->merge([
                'date_from' => $scopeDay,
                'date_to' => $scopeDay,
            ]);
            // Form should still show Compare mode (empty From/To), not the internal scope day.
            $request->attributes->set('bi_compare_mode', true);

            return;
        }

        if ($hasRange) {
            $request->query->remove('compare_date_a');
            $request->query->remove('compare_date_b');
            $request->request->remove('compare_date_a');
            $request->request->remove('compare_date_b');
            $request->merge([
                'compare_date_a' => null,
                'compare_date_b' => null,
            ]);
        }
    }

    /**
     * When a session is selected but From/To are empty, default both to today so Apply filters
     * returns the current calendar day's data. Explicit date ranges, Compare A/B mode, and
     * "All sessions" are unchanged.
     */
    private function biDefaultSessionFilterDatesToToday(Request $request): void
    {
        if ($request->attributes->get('bi_compare_mode')) {
            return;
        }

        $sessId = $request->input('sess_id');
        if ($sessId === null || $sessId === '') {
            return;
        }

        // Compare dates present (partial or full): do not inject today into From/To.
        $da = $request->input('compare_date_a');
        $db = $request->input('compare_date_b');
        if (($da !== null && $da !== '') || ($db !== null && $db !== '')) {
            return;
        }

        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        $fromEmpty = $dateFrom === null || $dateFrom === '';
        $toEmpty = $dateTo === null || $dateTo === '';
        if (! $fromEmpty || ! $toEmpty) {
            return;
        }

        $today = Carbon::today()->toDateString();
        $request->query->set('date_from', $today);
        $request->query->set('date_to', $today);
        $request->request->set('date_from', $today);
        $request->request->set('date_to', $today);
        $request->merge([
            'date_from' => $today,
            'date_to' => $today,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function biReportPageData(Request $request): array
    {
        // Normalize before today-default so Compare A/B is not polluted by an injected range.
        $this->biNormalizeExclusiveDateModes($request);
        $this->biDefaultSessionFilterDatesToToday($request);

        $futureDateError = self::biFutureDateSelectionMessage([
            'date_from' => $request->input('date_from'),
            'date_to' => $request->input('date_to'),
            'compare_date_a' => $request->input('compare_date_a'),
            'compare_date_b' => $request->input('compare_date_b'),
        ]);

        $definitions = $this->biReportDefinitions();
        $firstEnabledKey = null;
        foreach ($definitions as $key => $meta) {
            if ($this->biReportIsEnabled($meta)) {
                $firstEnabledKey = $key;
                break;
            }
        }
        $firstTabKey = $firstEnabledKey;
        $requestedTab = (string) $request->input('active_tab', '');
        if (
            $requestedTab !== ''
            && array_key_exists($requestedTab, $definitions)
            && $this->biReportIsEnabled($definitions[$requestedTab])
        ) {
            $firstTabKey = $requestedTab;
        }
        $datasets = [];
        foreach ($definitions as $key => $meta) {
            if (! $this->biReportIsEnabled($meta)) {
                $datasets[$key] = $this->biDisabledReportDataset($meta['table'], $meta['label']);
                continue;
            }
            // Future dates: skip OLAP so the UI can show a clear message instead of zero rows.
            if ($futureDateError !== null) {
                $datasets[$key] = $this->biFutureDateBlockedDataset($meta['table'], $meta['label']);
                continue;
            }
            $datasets[$key] = $this->loadBiReportDataset($meta['table'], $meta['label'], $request);
            if (empty($datasets[$key]['error']) && ! empty($datasets[$key]['columns'])) {
                $chartKeys = Schema::hasTable($meta['table'])
                    ? Schema::getColumnListing($meta['table'])
                    : array_column($datasets[$key]['columns'], 'key');
                $datasets[$key]['charts'] = $this->buildBiChartPayload($meta['table'], $chartKeys, $request);
            } else {
                $datasets[$key]['charts'] = $datasets[$key]['charts'] ?? [];
            }
        }

        $compareMode = (bool) $request->attributes->get('bi_compare_mode');
        $biFilter = [
            // In compare mode From/To are scoped internally to date B — keep the form fields blank.
            'date_from' => $compareMode ? null : $request->input('date_from'),
            'date_to' => $compareMode ? null : $request->input('date_to'),
            'compare_date_a' => $request->input('compare_date_a'),
            'compare_date_b' => $request->input('compare_date_b'),
            'compare_sess_id_a' => $request->input('compare_sess_id_a'),
            'compare_sess_id_b' => $request->input('compare_sess_id_b'),
            'sess_id' => $request->input('sess_id'),
            'school_id' => $request->input('school_id'),
            'dept_id' => $request->input('dept_id'),
            'prog_id' => $request->input('prog_id'),
            'active_tab' => $firstTabKey,
        ];

        return [
            'biDefinitions' => $definitions,
            'biDatasets' => $datasets,
            'filterSchools' => $this->biSchoolsForFilter(),
            'filterSessions' => $this->biSessionsForFilter(),
            'biFilter' => $biFilter,
            'firstTabKey' => $firstTabKey,
            'biFutureDateError' => $futureDateError,
        ];
    }

    /**
     * Schools available for BI filters (same visibility rules as the main report generator).
     *
     * @return \Illuminate\Support\Collection<int, object>
     */
    private function biSchoolsForFilter()
    {
        $loginUserUsername = strtolower(trim(auth()->user()->username));
        $loginUserSchoolId = auth()->user()->school_id;

        $recSchool = DB::table('schools')->orderBy('school_name');

        if ($loginUserUsername === 'ijaz.hussain') {
            $recSchool->whereIn('school_id', [12, 21]);
        } elseif (in_array($loginUserUsername, ['moeedyusuf', 'sumair.shafique', 'bilal.saleem', 'zain.ali'], true)) {
            // all schools
        } else {
            $recSchool->where('school_id', $loginUserSchoolId);
        }

        return $recSchool->get(['school_id', 'school_name']);
    }

    /**
     * Academic sessions for BI filters: Summer 2026 (SUM26) and Fall onward.
     *
     * @return \Illuminate\Support\Collection<int, object>
     */
    private function biSessionsForFilter()
    {
        $table = $this->biAcademicSessionsTable();
        // Spring 2026 = 167; Summer 2026 (SUM26) starts at 168. Prefer lookup by sess_code.
        $minSessId = DB::table($table)->where('sess_code', 'SUM26')->value('sess_id');
        if ($minSessId === null || $minSessId === '') {
            $minSessId = 168;
        }

        return DB::table($table)
            ->where('sess_id', '>=', (int) $minSessId)
            ->orderBy('sess_id', 'desc')
            ->get(['sess_id', 'sess_name', 'sess_code']);
    }

    /**
     * Optional explicit warehouse column names (match your DB catalog). Keys: school, dept, prog, date,
     * school_name, dept_name, prog_name, session (session FK column on the warehouse table), metric
     * (preferred numeric column for chart KPI sums on that table).
     *
     * @return array<string, array<string, string>>
     */
    private function biWarehouseDimensionHints(): array
    {
        $u = static function (string $t) {
            return strtoupper($t);
        };

        return [
            $u('OLAP_ADMISSION_GRAND_SUMMARY_REPORT') => [
                'metric' => 'TOTAL_APPLIED_APPLICANTS',
                'date' => 'ENTRY_DATETIME',
                'session' => 'SESS_ID',
                'school' => 'SCHOOL_ID',
            ],
            $u('OLP_ADMISSION_ANALYSIS') => [
                'session' => 'ADM_SESSION',
                'metric' => 'TOTAL_APPLIED_APPLICANTS',
                'date' => 'ENTRY_DATETIME',
            ],
            $u('OLP_FEE_CHALLAN_SUMMARY') => [
                'date' => 'ENTRY_DATETIME',
            ],
            $u('OLP_GYM_SUBSCRIPTION_SUMMARY_REPORT') => [
                'date' => 'CREATED_ON',
            ],
            $u('OLP_SCHOOL_ATTENDENCE_STUDENTS_SUMMARY') => [
                'date' => 'CREATED_ON',
            ],
            $u('OLP_SCHL_WITHDRAWAL_STD_SUMMARY') => [
                'date' => 'CREATED_ON',
            ],
        ];
    }

    /**
     * @param  array<int, string>  $columnKeys
     * @return array{school: ?string, dept: ?string, prog: ?string, date: ?string, school_name: ?string, dept_name: ?string, prog_name: ?string, session_fk: ?string, ...}
     */
    private function biResolveDimensionColumns(string $table, array $columnKeys): array
    {
        $map = [];
        foreach ($columnKeys as $c) {
            $map[strtolower((string) $c)] = $c;
        }

        $pick = function (array $candidates) use ($map) {
            foreach ($candidates as $c) {
                $k = strtolower((string) $c);
                if (isset($map[$k])) {
                    return $map[$k];
                }
            }

            return null;
        };

        $hints = $this->biWarehouseDimensionHints()[strtoupper($table)] ?? [];

        $school = $hints['school'] ?? $pick([
            'school_id',
            'sc_school_id',
            'sai_sdpo_school_id',
            'sdpo_school_id',
            'fk_school_id',
            'sch_id',
        ]) ?? $this->biFuzzyPickIdColumn($columnKeys, ['school'], ['session', 'house', 'class']);

        $schoolCode = $hints['school_code'] ?? $pick(['school_code', 'sch_code', 'school_cd']);

        $dept = $hints['dept'] ?? $pick([
            'dept_id',
            'department_id',
            'sc_department_id',
            'sc_dept_id',
            'sai_sdpo_dept_id',
            'sdpo_dept_id',
            'fk_dept_id',
        ]) ?? $this->biFuzzyPickIdColumn($columnKeys, ['dept'], ['school', 'session']);

        $dept = $dept ?? $this->biFuzzyPickIdColumn($columnKeys, ['department'], ['school', 'session']);

        $deptCode = $hints['dept_code'] ?? $pick(['dept_code', 'department_code', 'dep_code']);

        $prog = $hints['prog'] ?? $pick([
            'prog_id',
            'program_id',
            'sc_program_id',
            'sc_prog_id',
            'sai_sdpo_prog_id',
            'sdpo_prog_id',
            'fk_prog_id',
        ]) ?? $this->biFuzzyPickIdColumn($columnKeys, ['prog'], ['school', 'session']);

        $prog = $prog ?? $this->biFuzzyPickIdColumn($columnKeys, ['program'], ['school', 'session', 'type']);

        $progCode = $hints['prog_code'] ?? $pick(['prog_code', 'program_code', 'prg_code']);

        $date = null;
        if (! empty($hints['date'])) {
            $want = strtolower((string) $hints['date']);
            foreach ($columnKeys as $c) {
                if (strtolower((string) $c) === $want) {
                    $date = $c;
                    break;
                }
            }
        }
        if (! $date) {
            $date = $this->biDetectDateColumn($columnKeys);
        }

        $schoolName = $hints['school_name'] ?? $pick([
            'school_name',
            'school',
            'sch_name',
            'schoolname',
            'schoo_name',
        ]) ?? $this->biFuzzyPickLabelColumn($columnKeys, ['school'], ['id', 'code']);

        $deptName = $hints['dept_name'] ?? $pick([
            'dept_name',
            'department_name',
            'department',
            'deptname',
        ]) ?? $this->biFuzzyPickLabelColumn($columnKeys, ['dept', 'department'], ['id']);

        $progName = $hints['prog_name'] ?? $pick([
            'prog_name',
            'program_name',
            'program',
            'progname',
        ]) ?? $this->biFuzzyPickLabelColumn($columnKeys, ['program', 'prog'], ['id']);

        if ($school) {
            $schoolName = null;
            $schoolCode = null;
        } elseif ($schoolCode) {
            $schoolName = null;
        }
        if ($dept) {
            $deptName = null;
            $deptCode = null;
        } elseif ($deptCode) {
            $deptName = null;
        }
        if ($prog) {
            $progName = null;
            $progCode = null;
        } elseif ($progCode) {
            $progName = null;
        }

        $sessionFk = $this->biPickAcademicSessionJoinColumn($columnKeys);
        if (! empty($hints['session'])) {
            $hintSession = $hints['session'];
            foreach ($columnKeys as $c) {
                if (strtolower((string) $c) === strtolower((string) $hintSession)) {
                    $sessionFk = $c;
                    break;
                }
            }
        }

        if ($this->biUsesWideSchoolColumns($table)) {
            $school = null;
            $schoolCode = null;
            $schoolName = null;
            $dept = null;
            $deptCode = null;
            $deptName = null;
            $prog = null;
            $progCode = null;
            $progName = null;
        }

        return [
            'school' => $school,
            'dept' => $dept,
            'prog' => $prog,
            'date' => $date,
            'school_name' => $schoolName,
            'dept_name' => $deptName,
            'prog_name' => $progName,
            'school_code' => $schoolCode ?: null,
            'dept_code' => $deptCode ?: null,
            'prog_code' => $progCode ?: null,
            'session_fk' => $sessionFk,
        ];
    }

    /**
     * @param  array<int, string>  $columnKeys
     * @param  array<int, string>  $needles  substrings that must appear (e.g. school)
     * @param  array<int, string>  $avoidIfContains  skip column if any fragment appears (lowercase)
     */
    private function biFuzzyPickIdColumn(array $columnKeys, array $needles, array $avoidIfContains): ?string
    {
        $best = null;
        $bestLen = PHP_INT_MAX;

        foreach ($columnKeys as $c) {
            $l = strtolower((string) $c);
            $ok = true;
            foreach ($needles as $n) {
                if (! Str::contains($l, strtolower($n))) {
                    $ok = false;
                    break;
                }
            }
            if (! $ok) {
                continue;
            }
            if (! preg_match('/(^|_)id$|_id_/', $l) && ! Str::endsWith($l, 'id')) {
                continue;
            }
            foreach ($avoidIfContains as $frag) {
                if (Str::contains($l, strtolower($frag))) {
                    $ok = false;
                    break;
                }
            }
            if (! $ok) {
                continue;
            }
            $len = strlen($l);
            if ($len < $bestLen) {
                $bestLen = $len;
                $best = $c;
            }
        }

        return $best;
    }

    /**
     * Match descriptive columns (e.g. school name) without "_id" suffix.
     *
     * @param  array<int, string>  $needles
     * @param  array<int, string>  $avoidIfContains
     */
    private function biFuzzyPickLabelColumn(array $columnKeys, array $needles, array $avoidIfContains): ?string
    {
        $best = null;
        $bestLen = PHP_INT_MAX;

        foreach ($columnKeys as $c) {
            $l = strtolower((string) $c);
            if (Str::endsWith($l, '_id') || $l === 'id') {
                continue;
            }
            $ok = true;
            foreach ($needles as $n) {
                if (! Str::contains($l, strtolower($n))) {
                    $ok = false;
                    break;
                }
            }
            if (! $ok) {
                continue;
            }
            foreach ($avoidIfContains as $frag) {
                if (Str::contains($l, strtolower($frag))) {
                    $ok = false;
                    break;
                }
            }
            if (! $ok) {
                continue;
            }
            $len = strlen($l);
            if ($len < $bestLen) {
                $bestLen = $len;
                $best = $c;
            }
        }

        return $best;
    }

    /**
     * @param  array<int, string>  $columnKeys
     */
    private function biDetectDateColumn(array $columnKeys): ?string
    {
        $lowerToCol = [];
        foreach ($columnKeys as $c) {
            $lowerToCol[strtolower((string) $c)] = $c;
        }

        $preferred = [
            'challan_date',
            'issue_date',
            'paid_date',
            'due_date',
            'creation_date',
            'report_date',
            'transaction_date',
            'session_date',
            'attendance_date',
            'from_date',
            'to_date',
            'as_on_date',
            'voucher_date',
            'invoice_date',
            'register_date',
            'registration_date',
            'trx_date',
        ];

        foreach ($preferred as $p) {
            if (isset($lowerToCol[$p])) {
                return $lowerToCol[$p];
            }
        }

        $legacy = ['issuedate', 'paidon', 'duedate', 'validdate', 'creationdate', 'creation_date', 'paidondate'];
        foreach ($legacy as $p) {
            if (isset($lowerToCol[$p])) {
                return $lowerToCol[$p];
            }
        }

        $candidates = [];
        foreach ($columnKeys as $c) {
            $l = strtolower((string) $c);
            if (Str::contains($l, 'update') && Str::contains($l, 'date')) {
                continue;
            }
            if (
                Str::contains($l, 'date')
                || Str::endsWith($l, '_at')
                || Str::contains($l, 'timestamp')
                || preg_match('/(^|_)(on|at)$/', $l)
            ) {
                $candidates[] = $c;
            }
        }

        if (count($candidates) === 0) {
            return null;
        }

        usort($candidates, function ($a, $b) {
            return $this->biDateColumnRank($a) <=> $this->biDateColumnRank($b);
        });

        return $candidates[0];
    }

    private function biDateColumnRank(string $column): int
    {
        $l = strtolower($column);
        $priority = ['challan', 'issue', 'paid', 'due', 'creation', 'report', 'attend', 'session', 'from', 'to'];
        foreach ($priority as $i => $word) {
            if (Str::contains($l, $word)) {
                return $i;
            }
        }

        return 100;
    }

    private function biQualifyColumn(?string $fromAlias, string $column): string
    {
        if ($fromAlias === null || $fromAlias === '') {
            return $column;
        }
        if (Str::contains($column, '.')) {
            return $column;
        }
        $g = DB::connection()->getQueryGrammar();

        return $g->wrapTable($fromAlias).'.'.$g->wrap($column);
    }

    /**
     * Default grid ordering: newest warehouse date first when a date column exists.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     */
    private function biOrderGridByLatestDate($query, array $dims, ?string $fromAlias): void
    {
        $d = $dims['date'] ?? null;
        if (! $d) {
            return;
        }
        if ($fromAlias !== null && $fromAlias !== '') {
            $query->orderByRaw($this->biQualifyColumn($fromAlias, $d).' desc');
        } else {
            $query->orderBy($d, 'desc');
        }
    }

    /**
     * Oracle (and some drivers) return column names uppercased; the BI grid uses lowercase keys from Schema.
     *
     * @param  \Illuminate\Support\Collection<int, object>|\Illuminate\Support\Collection<int, array<string, mixed>>  $rows
     * @return \Illuminate\Support\Collection<int, object>
     */
    private function biNormalizeBiRowKeys($rows)
    {
        return $rows->map(function ($row) {
            $arr = [];
            foreach ((array) $row as $k => $v) {
                if (is_string($k)) {
                    $arr[strtolower($k)] = $v;
                } else {
                    $arr[$k] = $v;
                }
            }

            return (object) $arr;
        });
    }

    /**
     * Session master table: Oracle catalogs often expose {@see ACADEMIC_SESSIONS}; other drivers use academic_sessions.
     * Used only by BI report helpers in this controller.
     */
    private function biAcademicSessionsTable(): string
    {
        try {
            $driver = DB::connection()->getDriverName();
        } catch (\Throwable $e) {
            return 'academic_sessions';
        }

        return $driver === 'oracle' ? 'ACADEMIC_SESSIONS' : 'academic_sessions';
    }

    /**
     * Admission OLAP objects that should show session / school / department names via joins.
     */
    private function biIsAdmissionOlapNameJoinTable(string $table): bool
    {
        $u = strtoupper($table);

        return in_array($u, [
            'OLAP_ADMISSION_GRAND_SUMMARY_REPORT',
            'OLP_ADMISSION_ANALYSIS',
        ], true);
    }

    /**
     * Foreign key column on the OLAP row pointing at {@see ACADEMIC_SESSIONS} / academic_sessions (sess_id or sess_code).
     *
     * @param  array<int, string>  $columnKeys
     */
    private function biPickAcademicSessionJoinColumn(array $columnKeys): ?string
    {
        $map = [];
        foreach ($columnKeys as $c) {
            $map[strtolower((string) $c)] = $c;
        }
        $candidates = [
            'sess_id',
            'session_id',
            'adm_session',
            'academic_session_id',
            'roadmap_sess_id',
            'roadmap_session_id',
            'sdpo_sess_id',
            'aai_sdpo_sess_id',
            'sai_sdpo_sess_id',
            'sess_fk',
            'sc_session_id',
            'ffs_sess_id',
            'co_sess_id',
            'enroll_sess_id',
            'enrollment_session_id',
        ];
        foreach ($candidates as $c) {
            if (isset($map[$c])) {
                return $map[$c];
            }
        }

        foreach ($columnKeys as $c) {
            $l = strtolower((string) $c);
            if (Str::contains($l, 'school') && Str::contains($l, 'session')) {
                continue;
            }
            if (preg_match('/sess_fk$|sess.*_id$|_sess_id$|^sess_id$|^session_id$|_session_id$|^adm_session$/', $l)) {
                return $c;
            }
        }

        return null;
    }

    private function biAcademicSessionJoinTargetColumn(string $sessionFkColumn): string
    {
        $l = strtolower($sessionFkColumn);
        if (Str::contains($l, 'code') && (Str::contains($l, 'sess') || Str::contains($l, 'session'))) {
            return 'sess_code';
        }

        return 'sess_id';
    }

    /**
     * Filtered query for admission OLAP tables with joins to ACADEMIC_SESSIONS / academic_sessions, schools, departments (alias o).
     *
     * @param  array<int, string>  $columnKeys
     * @return \Illuminate\Database\Query\Builder|null
     */
    private function biAdmissionOlapFrom(string $table, array $columnKeys, Request $request)
    {
        if (! $this->biIsAdmissionOlapNameJoinTable($table)) {
            return null;
        }

        $dims = $this->biResolveDimensionColumns($table, $columnKeys);
        $sessionCol = $dims['session_fk'];
        $schoolCol = $dims['school'];
        $deptCol = $dims['dept'];
        $progCol = $dims['prog'];

        $q = DB::table($table.' as o');

        if ($sessionCol) {
            $acsTarget = $this->biAcademicSessionJoinTargetColumn($sessionCol);
            $sessTbl = $this->biAcademicSessionsTable();
            $q->leftJoin($sessTbl.' as acs', 'o.'.$sessionCol, '=', 'acs.'.$acsTarget);
        }
        if ($schoolCol) {
            $q->leftJoin('schools as sch', 'o.'.$schoolCol, '=', 'sch.school_id');
        }
        if ($deptCol) {
            $q->leftJoin('departments as dep', 'o.'.$deptCol, '=', 'dep.dept_id');
        }
        if ($progCol) {
            $q->leftJoin('programs as prg', 'o.'.$progCol, '=', 'prg.prog_id');
        }

        $this->biApplyReportFilters($q, $table, $columnKeys, $request, 'o');

        return $q;
    }

    /**
     * @param  array<int, string>  $columnKeys
     */
    private function biPickMetricColumn(string $table, array $columnKeys): ?string
    {
        $hints = $this->biWarehouseDimensionHints()[strtoupper($table)] ?? [];
        if (! empty($hints['metric'])) {
            $want = strtolower((string) $hints['metric']);
            foreach ($columnKeys as $c) {
                if (strtolower((string) $c) === $want) {
                    return $c;
                }
            }
        }

        $skip = [
            'id', 'school_id', 'dept_id', 'prog_id', 'program_id', 'department_id', 'sess_id', 'session_id',
            'adm_session',
            'sc_school_id', 'sc_department_id', 'sc_dept_id', 'sc_program_id', 'sc_prog_id',
            'sai_sdpo_school_id', 'sai_sdpo_dept_id', 'sai_sdpo_prog_id',
            'sdpo_school_id', 'sdpo_dept_id', 'sdpo_prog_id',
            'fk_school_id', 'fk_dept_id', 'fk_prog_id',
            'sai_sdpo_sess_id', 'aai_sdpo_sess_id', 'roadmap_session_id', 'sc_session_id', 'sess_fk',
        ];

        foreach ($columnKeys as $c) {
            $l = strtolower((string) $c);
            if (in_array($l, $skip, true)) {
                continue;
            }
            if (preg_match('/total|applied|registered|fee|amount|paid|collection|count|challan|attendance|absent|present|subscription|verified|complete|incomplete|revenue|balance|due|gym/i', $l)) {
                return $c;
            }
        }

        return null;
    }

    private function biAdmissionAnalysisRequestStrippingMainDates(Request $request): Request
    {
        $reqNoDate = $request->duplicate();
        $reqNoDate->query->remove('date_from');
        $reqNoDate->query->remove('date_to');
        $reqNoDate->request->remove('date_from');
        $reqNoDate->request->remove('date_to');
        $reqNoDate->merge([
            'date_from' => null,
            'date_to' => null,
        ]);
        // Compare / day-slice callers apply their own calendar day. Skip Grand Summary
        // snapshot-day injection (otherwise it intersects with "today" and zeros past compare days).
        $reqNoDate->attributes->set('bi_skip_gs_date', true);

        return $reqNoDate;
    }

    /**
     * Labels aligned with BNU Reports Generator → Admission Analysis Report
     * ({@see resources/views/grader/reportgenerator/BNUReportsGenerator/students.blade.php}).
     *
     * @return array<string, string> lowercase column key => display label
     */
    private function biAdmissionAnalysisLabelMap(): array
    {
        return [
            'registered_on_portal' => 'Number of individuals who registered on the Admission Portal',
            'total_applied_applicants' => 'Total Number of Applications',
            'incomplete_form_submission' => 'Admission Form Not Submitted',
            'fee_verified_not_final_submit' => 'Admission Form Not Submitted but fee paid',
            'completed_form_submission' => 'Admission Form Submitted',
            'processing_fee_not_verified' => 'Admission Fee Not Paid',
            'processing_fee_verified' => 'Admission Fee Paid',
            'documents_not_accepted' => 'Documents Incomplete for Admission Test',
            'documents_accepted' => 'Documents Compelete for Admission Test',
            'applicants_not_appeared_for_test' => 'Applicants Not Appeared For Admission Test',
            'applicants_appeared_for_test' => 'Applicants Appeared For Admission Test',
            'students_status_rejected' => 'Applicants Rejected',
            'admission_offered' => 'Admission Offered',
            'fee_not_paid' => 'Applicants Declined Offer',
            'fee_paid' => 'Applicants Accepted Offer',
            'students_moved_to_cms' => 'BNU Registeration Assigned ID',
            'left_students' => 'Left Students',
            'deferred_students' => 'Defer',
            'active_students' => 'Final Status (Active Students)',
            'current_partial_full_paid' => 'No. of Current Students (Paid / Partial Paid / Unpaid)',
            'challan_not_exists_but_active' => 'Status Active with Cancelled Challan',
            'before_cms' => 'Left Students',
            'after_cms' => 'Left Students After Cut-off Date',
        ];
    }

    /**
     * Funnel display order for Admission Analysis metrics (report-generator order).
     *
     * @return list<string>
     */
    private function biAdmissionAnalysisMetricOrder(): array
    {
        return array_keys($this->biAdmissionAnalysisLabelMap());
    }

    private function biAdmissionAnalysisColumnLabel(string $column): string
    {
        $key = strtolower((string) preg_replace('/[^a-z0-9_]+/i', '', $column));
        $map = $this->biAdmissionAnalysisLabelMap();
        if (isset($map[$key])) {
            return $map[$key];
        }

        return $this->biFormatColumnLabel($column);
    }

    /**
     * Warehouse columns to sum for admission day-slices (exclude keys / date / session FK).
     * Ordered to match Admission Analysis report funnel when possible.
     *
     * @param  array<int, string>  $columnKeys
     * @return array<int, string>
     */
    private function biAdmissionAnalysisComparableMetricColumns(array $columnKeys, array $dims): array
    {
        $dateCol = $dims['date'] ?? null;
        $sessionCol = $dims['session_fk'] ?? null;
        $skip = ['id' => true];
        if ($dateCol) {
            $skip[strtolower((string) $dateCol)] = true;
        }
        if ($sessionCol) {
            $skip[strtolower((string) $sessionCol)] = true;
        }

        $byLower = [];
        foreach ($columnKeys as $c) {
            $l = strtolower((string) $c);
            if (isset($skip[$l])) {
                continue;
            }
            if (Str::startsWith($l, 'bi_')) {
                continue;
            }
            // Skip non-metric dimension keys that may exist on the view.
            if (in_array($l, ['school_id', 'dept_id', 'prog_id', 'program_id', 'department_id', 'adm_session'], true)) {
                continue;
            }
            $byLower[$l] = $c;
        }

        $out = [];
        foreach ($this->biAdmissionAnalysisMetricOrder() as $logical) {
            if (isset($byLower[$logical])) {
                $out[] = $byLower[$logical];
                unset($byLower[$logical]);
            }
        }
        // Prefer only mapped funnel metrics for Admission Analysis display.
        // Any remaining unmapped columns are omitted to keep labels aligned with report generate.

        return $out;
    }

    /**
     * @param  \Illuminate\Database\Query\Builder  $base
     */
    private function biAdmissionAnalysisSumMetricForDay($base, string $dateCol, string $metricCol, Carbon $start, Carbon $end): float
    {
        try {
            $q = clone $base;
            $q->whereRaw($this->biQualifyColumn('o', $dateCol).' >= ?', [$start]);
            $q->whereRaw($this->biQualifyColumn('o', $dateCol).' <= ?', [$end]);
            $sum = $q->sum(DB::raw($this->biQualifyColumn('o', $metricCol)));

            return $sum !== null ? (float) $sum : 0.0;
        } catch (\Throwable $e) {
            return 0.0;
        }
    }

    /**
     * Admission OLAP numeric slices for one calendar day (ENTRY_DATETIME in [start, end]).
     *
     * @param  \Illuminate\Database\Query\Builder  $base
     * @param  array<int, string>  $columnKeys
     * @return list<array{label: string, value: float|int}>
     */
    private function biAdmissionAnalysisMetricDaySums($base, string $dateCol, array $columnKeys, Carbon $start, Carbon $end): array
    {
        $lowerToCol = [];
        foreach ($columnKeys as $c) {
            $lowerToCol[strtolower((string) $c)] = $c;
        }
        $phys = function (string $logical) use ($lowerToCol): ?string {
            return $lowerToCol[strtolower($logical)] ?? null;
        };

        $specs = [
            ['logical' => 'REGISTERED_ON_PORTAL', 'label' => $this->biAdmissionAnalysisColumnLabel('REGISTERED_ON_PORTAL')],
            ['logical' => 'TOTAL_APPLIED_APPLICANTS', 'label' => $this->biAdmissionAnalysisColumnLabel('TOTAL_APPLIED_APPLICANTS')],
            ['logical' => 'INCOMPLETE_FORM_SUBMISSION', 'label' => $this->biAdmissionAnalysisColumnLabel('INCOMPLETE_FORM_SUBMISSION')],
            ['logical' => 'FEE_VERIFIED_NOT_FINAL_SUBMIT', 'label' => $this->biAdmissionAnalysisColumnLabel('FEE_VERIFIED_NOT_FINAL_SUBMIT')],
            ['logical' => 'COMPLETED_FORM_SUBMISSION', 'label' => $this->biAdmissionAnalysisColumnLabel('COMPLETED_FORM_SUBMISSION')],
            ['logical' => 'PROCESSING_FEE_NOT_VERIFIED', 'label' => $this->biAdmissionAnalysisColumnLabel('PROCESSING_FEE_NOT_VERIFIED')],
            ['logical' => 'PROCESSING_FEE_VERIFIED', 'label' => $this->biAdmissionAnalysisColumnLabel('PROCESSING_FEE_VERIFIED')],
        ];

        $out = [];
        foreach ($specs as $spec) {
            $metricCol = $phys($spec['logical']);
            if (! $metricCol) {
                continue;
            }
            try {
                $q = clone $base;
                $q->whereRaw($this->biQualifyColumn('o', $dateCol).' >= ?', [$start]);
                $q->whereRaw($this->biQualifyColumn('o', $dateCol).' <= ?', [$end]);
                $sum = $q->sum(DB::raw($this->biQualifyColumn('o', $metricCol)));
                $out[] = ['label' => $spec['label'], 'value' => $sum !== null ? $sum : 0];
            } catch (\Throwable $e) {
            }
        }

        return $out;
    }

    /**
     * Today-only sums for OLP_ADMISSION_ANALYSIS. Uses the same session/school/dept/program filters as the grid,
     * but ignores date_from/date_to so "today" always means the calendar day in app timezone.
     *
     * @param  array<int, string>  $columnKeys
     * @return list<array{label: string, value: float|int}>
     */
    private function biAdmissionAnalysisTodayKpis(string $table, Request $request, array $dims, array $columnKeys): array
    {
        if (strtoupper($table) !== 'OLP_ADMISSION_ANALYSIS') {
            return [];
        }

        $dateCol = $dims['date'] ?? null;
        if (! $dateCol) {
            return [];
        }

        $base = $this->biAdmissionOlapFrom($table, $columnKeys, $this->biAdmissionAnalysisRequestStrippingMainDates($request));
        if ($base === null) {
            return [];
        }

        $inner = $this->biAdmissionAnalysisMetricDaySums(
            $base,
            $dateCol,
            $columnKeys,
            Carbon::today()->startOfDay(),
            Carbon::today()->endOfDay()
        );

        $out = [];
        foreach ($inner as $row) {
            $out[] = ['label' => 'Today · '.$row['label'], 'value' => $row['value']];
        }

        return $out;
    }

    /**
     * Side-by-side sums for two calendar days (same session/school/dept/program filters; main date range ignored).
     * One row per numeric warehouse column (all except id, entry date, session key).
     *
     * @param  array<int, string>  $columnKeys
     * @return array{date_a: string, date_b: string, rows: list<array{column_key?: string, label: string, val_a: float, val_b: float, delta: float}>, highlight_cards: list<array{column_key: string, label: string, val_a: float, val_b: float, delta: float}>}|null
     */
    private function biAdmissionAnalysisCompareKpis(string $table, Request $request, array $dims, array $columnKeys): ?array
    {
        if (strtoupper($table) !== 'OLP_ADMISSION_ANALYSIS') {
            return null;
        }

        $da = $request->input('compare_date_a');
        $db = $request->input('compare_date_b');
        if ($da === null || $da === '' || $db === null || $db === '') {
            return null;
        }

        $dateCol = $dims['date'] ?? null;
        if (! $dateCol) {
            return null;
        }

        try {
            $dayA = Carbon::parse($da)->startOfDay();
            $dayB = Carbon::parse($db)->startOfDay();
        } catch (\Throwable $e) {
            return null;
        }

        // Compare date B must be strictly greater than Compare date A.
        if (! $dayB->gt($dayA)) {
            return null;
        }

        $strippedRequest = $this->biAdmissionAnalysisRequestStrippingMainDates($request);
        $sessOverrideA = $request->input('compare_sess_id_a') ?: null;
        $sessOverrideB = $request->input('compare_sess_id_b') ?: null;
        $baseA = $this->biAdmissionOlapFrom($table, $columnKeys, $this->biRequestWithSessionOverride($strippedRequest, $sessOverrideA));
        $baseB = $sessOverrideB !== null
            ? $this->biAdmissionOlapFrom($table, $columnKeys, $this->biRequestWithSessionOverride($strippedRequest, $sessOverrideB))
            : $baseA;
        if ($baseA === null) {
            return null;
        }

        $endA = (clone $dayA)->endOfDay();
        $endB = (clone $dayB)->endOfDay();

        $rows = [];
        foreach ($this->biAdmissionAnalysisComparableMetricColumns($columnKeys, $dims) as $col) {
            $va = $this->biAdmissionAnalysisSumMetricForDay($baseA, $dateCol, $col, $dayA, $endA);
            $vb = $this->biAdmissionAnalysisSumMetricForDay($baseB ?? $baseA, $dateCol, $col, $dayB, $endB);
            $rows[] = [
                'column_key' => strtolower((string) $col),
                'label' => $this->biAdmissionAnalysisColumnLabel((string) $col),
                'val_a' => $va,
                'val_b' => $vb,
                'delta' => $vb - $va,
            ];
        }

        if ($rows === []) {
            return null;
        }

        $metric = $this->biPickMetricColumn($table, $columnKeys);
        $schoolCompare = $this->biSchoolWiseCompareRows(
            $table, $columnKeys, $strippedRequest, $dims, $metric,
            $dayA, $dayB, $sessOverrideA, $sessOverrideB
        );

        return [
            'date_a' => $dayA->toDateString(),
            'date_b' => $dayB->toDateString(),
            'rows' => $rows,
            'highlight_cards' => $this->biAdmissionAnalysisCompareHighlightCards($rows),
            'school_compare' => $schoolCompare,
        ];
    }

    /**
     * @param  list<array{column_key?: string, label: string, val_a: float, val_b: float, delta: float}>  $compareRows
     * @return list<array{column_key: string, label: string, val_a: float, val_b: float, delta: float}>
     */
    private function biAdmissionAnalysisCompareHighlightCards(array $compareRows): array
    {
        $byKey = [];
        foreach ($compareRows as $r) {
            $k = strtolower((string) ($r['column_key'] ?? ''));
            if ($k !== '') {
                $byKey[$k] = $r;
            }
        }

        $priority = [
            'total_applied_applicants',
            'registered_on_portal',
            'processing_fee_verified',
            'completed_form_submission',
        ];

        $cards = [];
        foreach ($priority as $wantKey) {
            if (! isset($byKey[$wantKey])) {
                continue;
            }
            $pick = $byKey[$wantKey];
            $cards[] = [
                'column_key' => strtolower((string) ($pick['column_key'] ?? $wantKey)),
                'label' => (string) ($pick['label'] ?? ''),
                'val_a' => (float) ($pick['val_a'] ?? 0),
                'val_b' => (float) ($pick['val_b'] ?? 0),
                'delta' => (float) ($pick['delta'] ?? 0),
            ];
        }

        return $cards;
    }

    /**
     * Fee challan dashboard figures are snapshot-style rows. Use the newest row inside the selected
     * date scope instead of summing every snapshot in a range. Date A/B comparison uses the same
     * {@see biGenericReportCompareKpis} path as other non-admission BI tabs.
     *
     * @param  array<int, string>  $columnKeys
     * @return array{summary: array<string, float>|null}
     */
    private function biFeeChallanSummaryStats(string $table, Request $request, array $dims, array $columnKeys): array
    {
        $empty = ['summary' => null];
        if (strtoupper($table) !== 'OLP_FEE_CHALLAN_SUMMARY') {
            return $empty;
        }

        $row = $this->biLatestFeeChallanSummaryRow($table, $request, $dims, $columnKeys, null, null);
        $summary = $row ? $this->biFeeChallanSummaryFromRow($row, $columnKeys) : null;

        return [
            'summary' => $summary,
        ];
    }

    /**
     * Generic compare for snapshot-style BI reports. Uses latest available snapshot inside each selected day,
     * then sums comparable numeric columns for that snapshot.
     *
     * @param  array<int, string>  $columnKeys
     * @return array{date_a: string, date_b: string, rows: list<array{column_key: string, label: string, val_a: float, val_b: float, delta: float}>, highlight_cards: list<array<string, mixed>>}|null
     */
    private function biGenericReportCompareKpis(string $table, Request $request, array $dims, array $columnKeys): ?array
    {
        if (strtoupper($table) === 'OLP_ADMISSION_ANALYSIS') {
            return null;
        }

        $da = $request->input('compare_date_a');
        $db = $request->input('compare_date_b');
        if ($da === null || $da === '' || $db === null || $db === '') {
            return null;
        }

        $dateCol = $dims['date'] ?? null;
        if (! $dateCol) {
            return null;
        }

        try {
            $dayA = Carbon::parse($da)->startOfDay();
            $dayB = Carbon::parse($db)->startOfDay();
        } catch (\Throwable $e) {
            return null;
        }

        // Compare date B must be strictly greater than Compare date A.
        if (! $dayB->gt($dayA)) {
            return null;
        }

        $metricCols = $this->biGenericComparableMetricColumns($table, $columnKeys, $dims);
        if ($metricCols === []) {
            return null;
        }

        $sessOverrideA = $request->input('compare_sess_id_a') ?: null;
        $sessOverrideB = $request->input('compare_sess_id_b') ?: null;
        $aRows = $this->biLatestSnapshotRowsForDay($table, $request, $dims, $columnKeys, $dayA, $sessOverrideA);
        $bRows = $this->biLatestSnapshotRowsForDay($table, $request, $dims, $columnKeys, $dayB, $sessOverrideB);
        if ($aRows->isEmpty() && $bRows->isEmpty()) {
            return null;
        }

        $rows = [];
        foreach ($metricCols as $col) {
            $va = $this->biSumColumnFromRows($aRows, $col);
            $vb = $this->biSumColumnFromRows($bRows, $col);
            $rows[] = [
                'column_key' => strtolower((string) $col),
                'label' => $this->biReportColumnLabel($table, (string) $col),
                'val_a' => $va,
                'val_b' => $vb,
                'delta' => $vb - $va,
            ];
        }

        $isWideSchoolReport = $this->biUsesWideSchoolColumns($table);
        if ($isWideSchoolReport && $rows !== []) {
            $totalA = array_sum(array_map(static function (array $row): float {
                return (float) ($row['val_a'] ?? 0);
            }, $rows));
            $totalB = array_sum(array_map(static function (array $row): float {
                return (float) ($row['val_b'] ?? 0);
            }, $rows));
            array_unshift($rows, [
                'column_key' => 'total',
                'label' => 'Total',
                'val_a' => $totalA,
                'val_b' => $totalB,
                'delta' => $totalB - $totalA,
            ]);
        }

        $metric = $this->biPickMetricColumn($table, $columnKeys);
        $schoolCompare = $this->biSchoolWiseCompareRows(
            $table, $columnKeys, $request, $dims, $metric,
            $dayA, $dayB, $sessOverrideA, $sessOverrideB
        );

        return [
            'date_a' => $dayA->toDateString(),
            'date_b' => $dayB->toDateString(),
            'rows' => $rows,
            'highlight_cards' => $isWideSchoolReport ? $rows : array_slice($rows, 0, 4),
            'school_compare' => $schoolCompare,
        ];
    }

    /**
     * School-wise side-by-side comparison for two dates.
     * Returns [{school, val_a, val_b, delta}] sorted by val_b desc.
     *
     * @param  array<int, string>  $columnKeys
     * @return list<array{school: string, val_a: float, val_b: float, delta: float}>
     */
    private function biSchoolWiseCompareRows(
        string $table,
        array $columnKeys,
        Request $request,
        array $dims,
        ?string $metric,
        Carbon $dayA,
        Carbon $dayB,
        ?string $sessOverrideA,
        ?string $sessOverrideB
    ): array {
        try {
            $schoolDim = $dims['school'] ?? null;
            $dateCol   = $dims['date']   ?? null;
            if (! $schoolDim || ! $dateCol) {
                return [];
            }

            $metricExpr = $metric ? 'SUM('.$metric.')' : 'COUNT(*)';
            $useOlap    = $this->biIsAdmissionOlapNameJoinTable($table);

            $getTotals = function (Carbon $day, ?string $sessOverride) use (
                $table, $columnKeys, $request, $dims, $schoolDim, $dateCol, $metricExpr, $useOlap, $metric
            ): array {
                $start = (clone $day)->startOfDay()->toDateTimeString();
                $end   = (clone $day)->endOfDay()->toDateTimeString();

                if ($useOlap) {
                    $req = $this->biAdmissionAnalysisRequestStrippingMainDates($request);
                    $req = $this->biRequestWithSessionOverride($req, $sessOverride);
                    $q   = $this->biAdmissionOlapFrom($table, $columnKeys, $req);
                    if ($q === null) {
                        return [];
                    }
                    $q->whereRaw($this->biQualifyColumn('o', $dateCol).' >= ?', [$start]);
                    $q->whereRaw($this->biQualifyColumn('o', $dateCol).' <= ?', [$end]);
                    $qualSchool = $this->biQualifyColumn('o', $schoolDim);
                    $qualMetric = $metric ? $this->biQualifyColumn('o', $metric) : null;
                    $metricExprQ = $qualMetric ? 'SUM('.$qualMetric.')' : 'COUNT(*)';
                    $q->selectRaw($qualSchool.' as school_raw, '.$metricExprQ.' as val');
                    $q->groupBy(DB::raw($qualSchool));
                } else {
                    $req = $this->biAdmissionAnalysisRequestStrippingMainDates($request);
                    $req = $this->biRequestWithSessionOverride($req, $sessOverride);
                    $q   = DB::table($table);
                    $this->biApplyReportFilters($q, $table, $columnKeys, $req, null);
                    $q->whereRaw($this->biQualifyColumn(null, $dateCol).' >= ?', [$start]);
                    $q->whereRaw($this->biQualifyColumn(null, $dateCol).' <= ?', [$end]);
                    $q->selectRaw($schoolDim.' as school_raw, '.$metricExpr.' as val');
                    $q->groupBy($schoolDim);
                }

                $rows = [];
                foreach ($q->get() as $row) {
                    $arr = $this->biRowArrayLower($row);
                    $rawId          = $arr['school_raw'] ?? null;
                    $rows[(string) $rawId] = (float) ($arr['val'] ?? 0);
                }

                return $rows;
            };

            $totalsA = $getTotals($dayA, $sessOverrideA);
            $totalsB = $getTotals($dayB, $sessOverrideB);

            if ($totalsA === [] && $totalsB === []) {
                return [];
            }

            // Resolve school names.
            $schoolDimLower = strtolower((string) $schoolDim);
            $isId   = in_array($schoolDimLower, ['school_id', 'sc_school_id', 'sdpo_school_id', 'fk_school_id', 'sai_sdpo_school_id'], true);
            $isCode = ! $isId && str_contains($schoolDimLower, 'code');
            $namesById   = $isId   ? DB::table('schools')->pluck('school_name', 'school_id') : collect();
            $namesByCode = $isCode ? DB::table('schools')->pluck('school_name', 'school_code') : collect();

            $resolveName = function ($rawId) use ($isId, $isCode, $namesById, $namesByCode): string {
                if ($rawId === null || $rawId === '') {
                    return 'Unknown';
                }
                if ($isId && isset($namesById[$rawId])) {
                    return (string) $namesById[$rawId];
                }
                if ($isCode && isset($namesByCode[$rawId])) {
                    return (string) $namesByCode[$rawId];
                }

                return (string) $rawId;
            };

            $selectedSchool = $request->input('school_id');
            $allKeys = array_unique(array_merge(array_keys($totalsA), array_keys($totalsB)));
            if ($selectedSchool !== null && $selectedSchool !== '') {
                $allKeys = array_values(array_filter($allKeys, static function ($rawId) use ($selectedSchool): bool {
                    return (string) $rawId === (string) $selectedSchool;
                }));
            }
            $out = [];
            foreach ($allKeys as $rawId) {
                $va = $totalsA[$rawId] ?? 0.0;
                $vb = $totalsB[$rawId] ?? 0.0;
                $out[] = [
                    'school' => $resolveName($rawId),
                    'val_a'  => $va,
                    'val_b'  => $vb,
                    'delta'  => $vb - $va,
                ];
            }

            usort($out, static function (array $a, array $b): int {
                return $b['val_b'] <=> $a['val_b'] ?: $b['val_a'] <=> $a['val_a'];
            });

            return $out;
        } catch (\Throwable $e) {
            return [];
        }
    }

    /**
     * @param  array<int, string>  $columnKeys
     * @return array<int, string>
     */
    private function biGenericComparableMetricColumns(string $table, array $columnKeys, array $dims): array
    {
        $skip = [
            'id' => true,
            'label_name' => true,
            'subscription_month' => true,
        ];
        foreach (['date', 'session_fk', 'school', 'dept', 'prog', 'school_name', 'dept_name', 'prog_name', 'school_code', 'dept_code', 'prog_code'] as $dk) {
            if (! empty($dims[$dk])) {
                $skip[strtolower((string) $dims[$dk])] = true;
            }
        }

        $out = [];
        foreach ($columnKeys as $col) {
            $l = strtolower((string) $col);
            if (isset($skip[$l]) || Str::startsWith($l, 'bi_')) {
                continue;
            }
            if ($this->biUsesWideSchoolColumns($table) && $this->biWideSchoolColumnLabel((string) $col) !== null) {
                $out[] = $col;
                continue;
            }
            if (preg_match('/total|count|paid|unpaid|challan|student|attendance|present|absent|subscription|fee|amount|balance|collection/i', $l)) {
                $out[] = $col;
            }
        }

        return array_values(array_slice($out, 0, 60));
    }

    /**
     * Build a cloned request with sess_id overridden — used for cross-session comparison.
     */
    private function biRequestWithSessionOverride(Request $request, ?string $overrideSessId): Request
    {
        if ($overrideSessId === null || $overrideSessId === '') {
            return $request;
        }
        $cloned = $request->duplicate();
        $cloned->query->set('sess_id', $overrideSessId);
        $cloned->request->set('sess_id', $overrideSessId);

        return $cloned;
    }

    /**
     * @param  array<int, string>  $columnKeys
     * @return \Illuminate\Support\Collection<int, object>
     */
    private function biLatestSnapshotRowsForDay(string $table, Request $request, array $dims, array $columnKeys, Carbon $day, ?string $sessionOverride = null)
    {
        $dateCol = $dims['date'] ?? null;
        if (! $dateCol) {
            return collect();
        }

        try {
            $filterRequest = $this->biAdmissionAnalysisRequestStrippingMainDates($request);
            $filterRequest = $this->biRequestWithSessionOverride($filterRequest, $sessionOverride);
            $base = DB::table($table);
            $this->biApplyReportFilters($base, $table, $columnKeys, $filterRequest, null);
            $start = $day->copy()->startOfDay()->format('Y-m-d H:i:s');
            $end = $day->copy()->endOfDay()->format('Y-m-d H:i:s');
            $base->whereRaw($this->biQualifyColumn(null, $dateCol).' >= ?', [$start]);
            $base->whereRaw($this->biQualifyColumn(null, $dateCol).' <= ?', [$end]);

            $latest = (clone $base)->orderBy($dateCol, 'desc')->value($dateCol);
            if ($latest === null || $latest === '') {
                return collect();
            }

            return (clone $base)->where($dateCol, $latest)->get();
        } catch (\Throwable $e) {
            return collect();
        }
    }

    /**
     * @param  \Illuminate\Support\Collection<int, object>  $rows
     */
    private function biSumColumnFromRows($rows, string $column): float
    {
        $sum = 0.0;
        foreach ($rows as $row) {
            $sum += (float) (data_get($row, $column) ?? data_get($row, strtolower($column)) ?? data_get($row, strtoupper($column)) ?? 0);
        }

        return $sum;
    }

    /**
     * @param  array<int, string>  $columnKeys
     */
    private function biLatestFeeChallanSummaryRow(string $table, Request $request, array $dims, array $columnKeys, ?Carbon $start, ?Carbon $end)
    {
        try {
            $q = DB::table($table);
            $filterRequest = ($start && $end) ? $this->biAdmissionAnalysisRequestStrippingMainDates($request) : $request;
            $this->biApplyReportFilters($q, $table, $columnKeys, $filterRequest, null);

            $dateCol = $dims['date'] ?? null;
            if ($start && $end && $dateCol) {
                $q->where($dateCol, '>=', $start);
                $q->where($dateCol, '<=', $end);
            }
            $this->biOrderGridByLatestDate($q, $dims, null);

            return $q->first();
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * @param  array<int, string>  $columnKeys
     * @return array<string, float>
     */
    private function biFeeChallanSummaryFromRow($row, array $columnKeys): array
    {
        $rowMap = [];
        foreach ((array) $row as $k => $v) {
            $rowMap[$this->biNormalizeMetricKey((string) $k)] = $v;
        }

        $pick = function (array $names, ?int $fallbackIndex = null) use ($row, $columnKeys, $rowMap): float {
            foreach ($names as $name) {
                $nk = $this->biNormalizeMetricKey((string) $name);
                if (array_key_exists($nk, $rowMap)) {
                    return (float) ($rowMap[$nk] ?? 0);
                }
                foreach ($columnKeys as $colKey) {
                    if ($this->biNormalizeMetricKey((string) $colKey) === $nk) {
                        return (float) (data_get($row, $colKey) ?? data_get($row, strtolower((string) $colKey)) ?? data_get($row, strtoupper((string) $colKey)) ?? 0);
                    }
                }
            }
            if ($fallbackIndex !== null && isset($columnKeys[$fallbackIndex])) {
                $colKey = $columnKeys[$fallbackIndex];

                return (float) (data_get($row, $colKey) ?? data_get($row, strtolower((string) $colKey)) ?? data_get($row, strtoupper((string) $colKey)) ?? 0);
            }

            return 0.0;
        };

        $deferred = $pick(['total_deferred_students', 'deferred_students', 'deferred'], 0);
        $cancelled = $pick(['cancelled', 'canceled', 'cancelled_challan', 'canceled_challan', 'total_cancelled_challan', 'total_canceled_challan'], 1);
        $totalWithCancelled = $pick(['total_fee_challan_cancelled', 'total_fee_challan_canceled', 'total_fee_challan_plus_cancelled', 'total_fee_challan_plus_canceled', 'total_fee_challan'], 2);
        $totalPaidUnpaid = $pick(['total_paid_unpaid_challan', 'total_paid_unpaid', 'paid_unpaid_challan'], 4);
        if ($totalPaidUnpaid <= 0 && $totalWithCancelled > 0) {
            $totalPaidUnpaid = max(0, $totalWithCancelled - $cancelled);
        }
        $paid = $pick(['total_paid', 'paid', 'paid_challan', 'total_paid_challan'], 5);
        $unpaid = $pick(['total_unpaid', 'unpaid', 'unpaid_challan', 'total_unpaid_challan'], 6);
        if ($unpaid <= 0 && $totalPaidUnpaid > 0) {
            $unpaid = max(0, $totalPaidUnpaid - $paid);
        }
        $workloadCompleted = $pick(['workload_completed', 'total_workload_completed'], 7);
        $notEnrolled = $pick(['not_enrolled', 'total_not_enrolled'], 9);
        $enrolledUnpaid = $pick(['enrolled_unpaid', 'total_enrolled_unpaid'], 10);
        $revisedUnpaid = $pick(['challan_revised_unpaid', 'revised_unpaid', 'total_challan_revised_unpaid'], 11);
        $scholarshipDateExtend = $pick(['scholarship_date_extend', 'scholarship_date_extended', 'scholarship_date_ext', 'date_extend', 'date_extended', 'date_ext', 'scholarship', 'total_scholarship_date_extend'], 12);
        $springIntake = $pick(['spring_intake', 'spring_intake_students', 'total_spring_intake'], null);
        $otherEnrolled = $pick(['other_enrolled', 'other_students', 'fee_other_enrolled'], null);
        $frozen = $pick(['total_frozen_students', 'frozen_students', 'frozen'], 14);
        $frozenPaid = $pick(['frozen_paid', 'frozen_paid_students', 'total_frozen_paid', 'dormant_frozen_paid'], null);
        $frozenUnpaid = $pick(['frozen_unpaid', 'frozen_unpaid_students', 'total_frozen_unpaid', 'dormant_frozen_unpaid'], null);
        $studentStrength = $this->biFeeStudentStrengthTotal(
            $paid,
            $revisedUnpaid,
            $frozen,
            $frozenPaid,
            $scholarshipDateExtend,
            $springIntake
        );

        return [
            'deferred' => $deferred,
            'cancelled' => $cancelled,
            'total_with_cancelled' => $totalWithCancelled,
            'total_paid_unpaid' => $totalPaidUnpaid,
            'paid' => $paid,
            'unpaid' => $unpaid,
            'workload_completed' => $workloadCompleted,
            'not_enrolled' => $notEnrolled,
            'enrolled_unpaid' => $enrolledUnpaid,
            'revised_unpaid' => $revisedUnpaid,
            'scholarship_date_extend' => $scholarshipDateExtend,
            'spring_intake' => $springIntake,
            'other_enrolled' => $otherEnrolled,
            'student_strength' => $studentStrength,
            'frozen' => $frozen,
            'frozen_paid' => $frozenPaid,
            'frozen_unpaid' => $frozenUnpaid,
        ];
    }

    private function biNormalizeMetricKey(string $key): string
    {
        return strtolower((string) preg_replace('/[^a-z0-9]+/i', '', $key));
    }

    /**
     * Executive-style matrix: metric rows × calendar-day columns for the dashboard date range,
     * using the same session / school / dept / programme filters as the grid (warehouse date column only;
     * main from/to bounds are ignored for the base slice, identical to Compare KPI semantics).
     *
     * Built with one grouped query per request (not per-cell) for performance.
     *
     * @param  array<int, string>  $columnKeys
     * @return array{days: list<string>, range_label: string, rows: list<array{label: string, values: list<float>}>}|null
     */
    private function biAdmissionAnalysisRangePivot(string $table, Request $request, array $dims, array $columnKeys): ?array
    {
        if (strtoupper($table) !== 'OLP_ADMISSION_ANALYSIS') {
            return null;
        }

        $df = $request->input('date_from');
        $dt = $request->input('date_to');
        if ($df === null || $df === '' || $dt === null || $dt === '') {
            return null;
        }

        $dateCol = $dims['date'] ?? null;
        if (! $dateCol) {
            return null;
        }

        try {
            $start = Carbon::parse($df)->startOfDay();
            $last = Carbon::parse($dt)->startOfDay();
        } catch (\Throwable $e) {
            return null;
        }

        if ($last->lt($start)) {
            $tmp = $start->copy();
            $start = $last->copy()->startOfDay();
            $last = $tmp->startOfDay();
        }

        $maxSpan = 45;
        $days = [];
        $cursor = $start->copy()->startOfDay();
        $guard = 0;
        while ($cursor->lte($last) && $guard++ < $maxSpan) {
            $days[] = $cursor->toDateString();
            $cursor->addDay();
        }

        // Allow a single calendar day (session filter defaults From/To to today).
        if ($days === []) {
            return null;
        }

        $metricCols = $this->biAdmissionAnalysisComparableMetricColumns($columnKeys, $dims);
        if ($metricCols === []) {
            return null;
        }

        $metricCols = array_values(array_slice($metricCols, 0, 50));

        $base = $this->biAdmissionOlapFrom($table, $columnKeys, $this->biAdmissionAnalysisRequestStrippingMainDates($request));
        if ($base === null) {
            return null;
        }

        $dQual = $this->biQualifyColumn('o', $dateCol);

        try {
            $driver = DB::connection()->getDriverName();
        } catch (\Throwable $e) {
            $driver = 'mysql';
        }

        if ($driver === 'oracle') {
            $daySql = 'TRUNC('.$dQual.')';
        } elseif ($driver === 'pgsql') {
            $daySql = 'CAST('.$dQual.' AS DATE)';
        } else {
            $daySql = 'DATE('.$dQual.')';
        }

        $sumParts = [];
        foreach ($metricCols as $idx => $mc) {
            $sumParts[] = 'SUM('.$this->biQualifyColumn('o', $mc).') AS piv_'.$idx;
        }

        $dq = clone $base;
        $dq->whereRaw($dQual.' >= ?', [$start->copy()->startOfDay()]);
        $dq->whereRaw($dQual.' <= ?', [$last->copy()->endOfDay()]);
        $dq->selectRaw($daySql.' AS piv_day, '.implode(', ', $sumParts));
        $dq->groupBy(DB::raw($daySql));
        $dq->orderBy(DB::raw($daySql));

        try {
            $got = $dq->get();
        } catch (\Throwable $e) {
            return null;
        }

        $byDayMetric = [];
        foreach ($got as $rowObj) {
            $low = [];
            foreach ((array) $rowObj as $rk => $rv) {
                if (is_string($rk)) {
                    $low[strtolower(ltrim((string) $rk, chr(12)))] = $rv;
                }
            }
            if (! isset($low['piv_day']) || $low['piv_day'] === null) {
                continue;
            }
            try {
                $dKey = Carbon::parse($low['piv_day'])->toDateString();
            } catch (\Throwable $e) {
                continue;
            }

            foreach ($metricCols as $idx => $_mc) {
                $vk = isset($low['piv_'.$idx]) ? $low['piv_'.$idx] : null;
                $byDayMetric[$dKey][$idx] = $vk !== null ? (float) $vk : 0.0;
            }
        }

        $metricRows = [];
        foreach ($metricCols as $idx => $mc) {
            $vals = [];
            foreach ($days as $dStr) {
                $vals[] = isset($byDayMetric[$dStr][$idx]) ? (float) $byDayMetric[$dStr][$idx] : 0.0;
            }
            $metricRows[] = [
                'label' => $this->biAdmissionAnalysisColumnLabel((string) $mc),
                'values' => $vals,
            ];
        }

        $rangeLabel = $start->toDateString() === $last->toDateString()
            ? $start->toDateString()
            : $start->toDateString().' – '.$last->toDateString();

        return [
            'days' => $days,
            'range_label' => $rangeLabel,
            'rows' => $metricRows,
        ];
    }

    /**
     * @param  \Illuminate\Database\Query\Builder  $query
     * @param  array<int, string>  $columnKeys
     */
    private function biApplyReportFilters($query, string $table, array $columnKeys, Request $request, ?string $fromAlias = null): void
    {
        $dims = $this->biResolveDimensionColumns($table, $columnKeys);
        $qc = function (string $col) use ($fromAlias) {
            return $this->biQualifyColumn($fromAlias, $col);
        };
        $qualified = $fromAlias !== null && $fromAlias !== '';
        $whereEq = function (string $col, $value) use ($query, $qc, $qualified) {
            if ($qualified) {
                $query->whereRaw($qc($col).' = ?', [$value]);
            } else {
                $query->where($col, $value);
            }
        };
        $whereCompare = function (string $col, string $op, $value) use ($query, $qc, $qualified) {
            if ($qualified) {
                $query->whereRaw($qc($col).' '.$op.' ?', [$value]);
            } else {
                $query->where($col, $op, $value);
            }
        };

        $sessId = $request->input('sess_id');
        if ($sessId !== null && $sessId !== '') {
            $sessionCol = $dims['session_fk'] ?? null;
            if ($sessionCol) {
                $target = $this->biAcademicSessionJoinTargetColumn($sessionCol);
                if ($target === 'sess_id') {
                    $whereEq($sessionCol, (int) $sessId);
                } else {
                    $code = DB::table($this->biAcademicSessionsTable())->where('sess_id', (int) $sessId)->value('sess_code');
                    if ($code !== null && $code !== '') {
                        $whereEq($sessionCol, $code);
                    }
                }
            }
        }

        $schoolId = $request->input('school_id');
        if ($schoolId !== null && $schoolId !== '') {
            if ($dims['school']) {
                $whereEq($dims['school'], (int) $schoolId);
            } elseif ($dims['school_code']) {
                $code = DB::table('schools')->where('school_id', (int) $schoolId)->value('school_code');
                if ($code !== null && $code !== '') {
                    $whereEq($dims['school_code'], $code);
                }
            } elseif ($dims['school_name']) {
                $name = DB::table('schools')->where('school_id', (int) $schoolId)->value('school_name');
                if ($name !== null && $name !== '') {
                    $whereEq($dims['school_name'], $name);
                }
            }
        }

        $deptId = $request->input('dept_id');
        if ($deptId !== null && $deptId !== '') {
            if ($dims['dept']) {
                $whereEq($dims['dept'], (int) $deptId);
            } elseif ($dims['dept_code']) {
                $code = DB::table('departments')->where('dept_id', (int) $deptId)->value('dept_code');
                if ($code !== null && $code !== '') {
                    $whereEq($dims['dept_code'], $code);
                }
            } elseif ($dims['dept_name']) {
                $name = DB::table('departments')->where('dept_id', (int) $deptId)->value('dept_name');
                if ($name !== null && $name !== '') {
                    $whereEq($dims['dept_name'], $name);
                }
            }
        }

        $progId = $request->input('prog_id');
        if ($progId !== null && $progId !== '') {
            if ($dims['prog']) {
                $whereEq($dims['prog'], (int) $progId);
            } elseif ($dims['prog_code']) {
                $code = DB::table('programs')->where('prog_id', (int) $progId)->value('prog_code');
                if ($code !== null && $code !== '') {
                    $whereEq($dims['prog_code'], $code);
                }
            } elseif ($dims['prog_name']) {
                $name = DB::table('programs')->where('prog_id', (int) $progId)->value('prog_name');
                if ($name !== null && $name !== '') {
                    $whereEq($dims['prog_name'], $name);
                }
            }
        }

        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        if ($dims['date'] && ! $request->attributes->get('bi_skip_gs_date')) {
            // Grand Summary is a daily snapshot warehouse: never apply a multi-day range
            // (that duplicates programmes across days and often yields an empty chart day).
            if (strtoupper($table) === 'OLAP_ADMISSION_GRAND_SUMMARY_REPORT') {
                $day = $this->biGrandSummaryResolveSnapshotDay($request, $table, $dims, $columnKeys);
                $start = (clone $day)->startOfDay()->format('Y-m-d H:i:s');
                $end = (clone $day)->endOfDay()->format('Y-m-d H:i:s');
                $whereCompare($dims['date'], '>=', $start);
                $whereCompare($dims['date'], '<=', $end);
            } else {
                if ($dateFrom) {
                    $whereCompare($dims['date'], '>=', Carbon::parse($dateFrom)->startOfDay()->format('Y-m-d H:i:s'));
                }
                if ($dateTo) {
                    $whereCompare($dims['date'], '<=', Carbon::parse($dateTo)->endOfDay()->format('Y-m-d H:i:s'));
                }
            }
        }
    }

    /**
     * Pick the Grand Summary snapshot day: prefer To date, else From date, else today.
     * If that day has no rows for the current session/school/dept/program filters, use the
     * latest available day inside the From–To window (when provided).
     *
     * @param  array<int, string>  $columnKeys
     */
    private function biGrandSummaryResolveSnapshotDay(Request $request, string $table, array $dims, array $columnKeys): Carbon
    {
        $preferred = $request->input('date_to') ?: $request->input('date_from');
        try {
            $day = $preferred ? Carbon::parse($preferred)->startOfDay() : Carbon::today();
        } catch (\Throwable $e) {
            $day = Carbon::today();
        }

        if ($this->biGrandSummaryDayHasRows($request, $table, $dims, $columnKeys, $day)) {
            return $day;
        }

        $df = $request->input('date_from');
        $dt = $request->input('date_to');
        if (($df === null || $df === '') && ($dt === null || $dt === '')) {
            return $day;
        }

        try {
            $rangeStart = $df ? Carbon::parse($df)->startOfDay() : (clone $day)->subDays(45)->startOfDay();
            $rangeEnd = $dt ? Carbon::parse($dt)->endOfDay() : (clone $day)->endOfDay();
        } catch (\Throwable $e) {
            return $day;
        }

        if ($rangeEnd->lt($rangeStart)) {
            $tmp = $rangeStart->copy();
            $rangeStart = $rangeEnd->copy()->startOfDay();
            $rangeEnd = $tmp->endOfDay();
        }

        $latest = $this->biGrandSummaryLatestDayInRange($request, $table, $dims, $columnKeys, $rangeStart, $rangeEnd);

        return $latest ?: $day;
    }

    /**
     * @param  array<int, string>  $columnKeys
     */
    private function biGrandSummaryDayHasRows(Request $request, string $table, array $dims, array $columnKeys, Carbon $day): bool
    {
        try {
            $q = $this->biGrandSummaryFilterBaseWithoutDates($request, $table, $dims, $columnKeys);
            $dateCol = $dims['date'] ?? null;
            if (! $dateCol) {
                return (clone $q)->limit(1)->exists();
            }
            $start = (clone $day)->startOfDay()->format('Y-m-d H:i:s');
            $end = (clone $day)->endOfDay()->format('Y-m-d H:i:s');
            $q->whereRaw($this->biQualifyColumn(null, $dateCol).' >= ?', [$start]);
            $q->whereRaw($this->biQualifyColumn(null, $dateCol).' <= ?', [$end]);

            return (clone $q)->limit(1)->exists();
        } catch (\Throwable $e) {
            return true; // do not block preferred day on probe errors
        }
    }

    /**
     * @param  array<int, string>  $columnKeys
     */
    private function biGrandSummaryLatestDayInRange(
        Request $request,
        string $table,
        array $dims,
        array $columnKeys,
        Carbon $rangeStart,
        Carbon $rangeEnd
    ): ?Carbon {
        $dateCol = $dims['date'] ?? null;
        if (! $dateCol) {
            return null;
        }
        try {
            $q = $this->biGrandSummaryFilterBaseWithoutDates($request, $table, $dims, $columnKeys);
            $qc = $this->biQualifyColumn(null, $dateCol);
            $q->whereRaw($qc.' >= ?', [$rangeStart->format('Y-m-d H:i:s')]);
            $q->whereRaw($qc.' <= ?', [$rangeEnd->format('Y-m-d H:i:s')]);
            try {
                $driver = DB::connection()->getDriverName();
            } catch (\Throwable $e) {
                $driver = 'mysql';
            }
            if ($driver === 'oracle') {
                $dayExpr = 'TRUNC('.$qc.')';
            } elseif ($driver === 'pgsql') {
                $dayExpr = 'CAST('.$qc.' AS DATE)';
            } else {
                $dayExpr = 'DATE('.$qc.')';
            }
            $raw = $q->selectRaw('MAX('.$dayExpr.') as max_day')->value('max_day');
            if ($raw === null || $raw === '') {
                return null;
            }

            return Carbon::parse($raw)->startOfDay();
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Session / school / dept / program filters for Grand Summary probes (dates applied by caller).
     *
     * @param  array<int, string>  $columnKeys
     * @return \Illuminate\Database\Query\Builder
     */
    private function biGrandSummaryFilterBaseWithoutDates(Request $request, string $table, array $dims, array $columnKeys)
    {
        $req = $request->duplicate();
        $req->query->remove('date_from');
        $req->query->remove('date_to');
        $req->request->remove('date_from');
        $req->request->remove('date_to');
        $req->merge(['date_from' => null, 'date_to' => null]);
        // Prevent resolveSnapshotDay recursion while probing for available days.
        $req->attributes->set('bi_skip_gs_date', true);

        $from = $this->biAdmissionOlapFrom($table, $columnKeys, $req);
        if ($from !== null) {
            return $from;
        }

        $q = DB::table($table);
        $this->biApplyReportFilters($q, $table, $columnKeys, $req, null);

        return $q;
    }

    /**
     * Grand Summary warehouses often replay the same programmes across many snapshots. Chart/detail SUM(metric)
     * must use one day snapshot: selected filter date when present, otherwise today's calendar bounds.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     */
    private function biApplyGrandSummaryChartDayScope(string $table, $query, Request $request, array $dims, ?string $fromAlias): void
    {
        // Dates already applied as a single snapshot day inside biApplyReportFilters for this table.
        if (strtoupper((string) $table) === 'OLAP_ADMISSION_GRAND_SUMMARY_REPORT') {
            return;
        }
        $dateCol = $dims['date'] ?? null;
        if (! $dateCol) {
            return;
        }
        $selectedDate = $request->input('date_to') ?: $request->input('date_from');
        try {
            $day = $selectedDate ? Carbon::parse($selectedDate) : Carbon::today();
        } catch (\Throwable $e) {
            $day = Carbon::today();
        }
        $start = (clone $day)->startOfDay()->format('Y-m-d H:i:s');
        $end = (clone $day)->endOfDay()->format('Y-m-d H:i:s');
        $qc = $this->biQualifyColumn($fromAlias, $dateCol);
        $query->whereRaw($qc.' >= ?', [$start]);
        $query->whereRaw($qc.' <= ?', [$end]);
    }

    /**
     * @param  array<int, string>  $columnKeys
     * @return array<string, mixed>
     */
    private function buildBiChartPayload(string $table, array $columnKeys, Request $request): array
    {
        if ($this->biUsesWideSchoolColumns($table)) {
            return $this->biWideSchoolChartPayload($table, $columnKeys, $request);
        }

        $dims = $this->biResolveDimensionColumns($table, $columnKeys);
        $metric = $this->biPickMetricColumn($table, $columnKeys);
        $grammar = DB::connection()->getQueryGrammar();
        $grandSummary = strtoupper($table) === 'OLAP_ADMISSION_GRAND_SUMMARY_REPORT';

        $makeBase = function () use ($table, $columnKeys, $request, $dims, $grandSummary) {
            $from = $this->biAdmissionOlapFrom($table, $columnKeys, $request);
            if ($from !== null) {
                if ($grandSummary) {
                    $this->biApplyGrandSummaryChartDayScope($table, $from, $request, $dims, 'o');
                }

                return $from;
            }
            $q = DB::table($table);
            $this->biApplyReportFilters($q, $table, $columnKeys, $request, null);
            if ($grandSummary) {
                $this->biApplyGrandSummaryChartDayScope($table, $q, $request, $dims, null);
            }

            return $q;
        };

        $useO = $this->biIsAdmissionOlapNameJoinTable($table);
        $oWrap = $grammar->wrapTable('o');
        $dimCol = function (string $c) use ($useO, $oWrap, $grammar) {
            return $useO ? $oWrap.'.'.$grammar->wrap($c) : $grammar->wrap($c);
        };
        $groupExpr = function (string $c) use ($useO, $oWrap, $grammar) {
            return $useO ? DB::raw($oWrap.'.'.$grammar->wrap($c)) : $c;
        };
        $whereNotNullCol = function (string $c) use ($useO) {
            return $useO ? DB::raw($this->biQualifyColumn('o', $c)) : $c;
        };

        $out = [];

        $schoolAppliedTitle = $grandSummary ? 'Total applied applicants by school' : 'Totals by school';

        $schoolDim = $dims['school'] ?? $dims['school_code'] ?? $dims['school_name'] ?? null;
        if ($schoolDim) {
            try {
                $col = $schoolDim;
                $q = $makeBase();
                if ($metric) {
                    $q->selectRaw($dimCol($col).' as dim_key, SUM('.$dimCol($metric).') as val');
                } else {
                    $q->selectRaw($dimCol($col).' as dim_key, COUNT(*) as val');
                }
                $rows = $q->groupBy($groupExpr($col))->orderByDesc('val')->limit(14)->get();
                if ($rows->isNotEmpty()) {
                    $out['by_school'] = $this->biChartBarsWithSchoolLabels($rows, $schoolAppliedTitle);
                }
            } catch (\Throwable $e) {
                try {
                    $col = $schoolDim;
                    $q = $makeBase();
                    $q->selectRaw($dimCol($col).' as dim_key, COUNT(*) as val');
                    $rows = $q->groupBy($groupExpr($col))->orderByDesc('val')->limit(14)->get();
                    if ($rows->isNotEmpty()) {
                        $out['by_school'] = $this->biChartBarsWithSchoolLabels($rows, 'Row counts by school');
                    }
                } catch (\Throwable $e2) {
                    // ignore chart
                }
            }
        }

        $deptPieTitle = $grandSummary ? 'Share of total applied by department (top 12)' : 'Share by department (top 12)';

        $deptDim = $dims['dept'] ?? $dims['dept_code'] ?? $dims['dept_name'] ?? null;
        $progDim = $dims['prog'] ?? $dims['prog_code'] ?? $dims['prog_name'] ?? null;
        if ($grandSummary && $schoolDim) {
            // Dedicated direct query for school → program breakdown.
            // Avoids the complex joined $makeBase() builder to prevent Oracle alias ORDER BY issues.
            $out['by_school_details'] = $this->biGrandSummaryDirectSchoolDetails(
                $table, $request, $dims, $metric, $columnKeys
            );
        } elseif ($schoolDim && ($deptDim || $progDim)) {
            try {
                $q = $makeBase();
                $detailSelects = [$dimCol($schoolDim).' as school_key'];
                $detailGroups = [$groupExpr($schoolDim)];
                if ($deptDim) {
                    $detailSelects[] = $dimCol($deptDim).' as dept_key';
                    $detailGroups[] = $groupExpr($deptDim);
                } else {
                    $detailSelects[] = "'No department' as dept_key";
                }
                if ($progDim) {
                    $detailSelects[] = $dimCol($progDim).' as prog_key';
                    $detailGroups[] = $groupExpr($progDim);
                } else {
                    $detailSelects[] = "'No program' as prog_key";
                }
                if ($metric) {
                    $q->selectRaw(implode(', ', $detailSelects).', SUM('.$dimCol($metric).') as val');
                } else {
                    $q->selectRaw(implode(', ', $detailSelects).', COUNT(*) as val');
                }
                $detailRows = $q
                    ->groupBy(...$detailGroups)
                    ->orderByDesc('val')
                    ->limit(1500)
                    ->get();
                $out['by_school_details'] = $this->biGrandSummarySchoolDetailsPayload(
                    $detailRows,
                    (bool) ($dims['school'] ?? false),
                    $deptDim ? (bool) ($dims['dept'] ?? false) : false,
                    $progDim ? (bool) ($dims['prog'] ?? false) : false,
                    (bool) ($dims['school_code'] ?? false),
                    (bool) ($dims['school_name'] ?? false),
                    $deptDim ? (bool) ($dims['dept_code'] ?? false) : false,
                    $deptDim ? (bool) ($dims['dept_name'] ?? false) : false,
                    $progDim ? (bool) ($dims['prog_code'] ?? false) : false,
                    $progDim ? (bool) ($dims['prog_name'] ?? false) : false
                );
            } catch (\Throwable $e) {
                $out['by_school_details'] = [];
            }
        }
        if ($deptDim) {
            try {
                $col = $deptDim;
                $q = $makeBase();
                if ($metric) {
                    $q->selectRaw($dimCol($col).' as dim_key, SUM('.$dimCol($metric).') as val');
                } else {
                    $q->selectRaw($dimCol($col).' as dim_key, COUNT(*) as val');
                }
                $rows = $q->whereNotNull($whereNotNullCol($col))->groupBy($groupExpr($col))->orderByDesc('val')->limit(12)->get();
                if ($rows->isNotEmpty()) {
                    $out['by_dept_pie'] = $this->biChartPieWithDeptLabels($rows, $deptPieTitle, (bool) ($dims['dept'] ?? false), (bool) ($dims['dept_code'] ?? false));
                }
            } catch (\Throwable $e) {
                try {
                    $col = $deptDim;
                    $q = $makeBase();
                    $q->selectRaw($dimCol($col).' as dim_key, COUNT(*) as val');
                    $rows = $q->whereNotNull($whereNotNullCol($col))->groupBy($groupExpr($col))->orderByDesc('val')->limit(12)->get();
                    if ($rows->isNotEmpty()) {
                        $out['by_dept_pie'] = $this->biChartPieWithDeptLabels($rows, 'Row counts by department', (bool) ($dims['dept'] ?? false), (bool) ($dims['dept_code'] ?? false));
                    }
                } catch (\Throwable $e2) {
                }
            }
        }

        $dateDim = $dims['date'] ?? null;
        if ($dateDim && $metric && ! $grandSummary) {
            try {
                $driver = DB::connection()->getDriverName();
                $q = $makeBase();
                if ($driver === 'oracle') {
                    $dayExpr = 'TRUNC('.$dimCol($dateDim).')';
                } else {
                    $dayExpr = 'DATE('.$dimCol($dateDim).')';
                }
                $q->selectRaw($dayExpr.' as dim_key, SUM('.$dimCol($metric).') as val');
                $q->groupBy(DB::raw($dayExpr));
                $q->orderBy(DB::raw($dayExpr));
                $chartRows = $q->limit(120)->get();
                if ($chartRows->isNotEmpty()) {
                    $labels = [];
                    $values = [];
                    foreach ($chartRows as $r) {
                        $labels[] = $this->biFormatChartDateLabel($r->dim_key);
                        $values[] = round((float) $r->val, 2);
                    }
                    $out['by_date_line'] = [
                        'title' => 'Daily '.$this->biFormatColumnLabel((string) $metric).' (filtered)',
                        'labels' => $labels,
                        'values' => $values,
                    ];
                }
            } catch (\Throwable $e) {
            }
        }

        return $out;
    }

    /**
     * @param  \Illuminate\Support\Collection<int, object>  $rows
     */
    private function biChartBarsWithSchoolLabels($rows, string $title): array
    {
        $namesById = DB::table('schools')->pluck('school_name', 'school_id');
        $namesByCode = DB::table('schools')->pluck('school_name', 'school_code');
        $labels = [];
        $values = [];
        $ids    = [];
        foreach ($rows as $r) {
            $id = $r->dim_key;
            if ($id === null || $id === '') {
                $labels[] = '—';
            } elseif (is_numeric($id)) {
                $labels[] = $namesById[$id] ?? ('School '.$id);
            } elseif (isset($namesByCode[$id])) {
                $labels[] = $namesByCode[$id];
            } else {
                $labels[] = (string) $id;
            }
            $values[] = round((float) $r->val, 2);
            $ids[]    = $id;
        }

        return [
            'type' => 'bar',
            'title' => $title,
            'labels' => $labels,
            'ids'    => $ids,
            'values' => $values,
        ];
    }

    /**
     * @param  \Illuminate\Support\Collection<int, object>  $rows
     */
    private function biChartPieWithDeptLabels($rows, string $title, bool $lookupDeptId = true, bool $lookupDeptCode = false): array
    {
        $byId = DB::table('departments')->pluck('dept_name', 'dept_id');
        $byCode = DB::table('departments')->pluck('dept_name', 'dept_code');
        $series = [];
        foreach ($rows as $r) {
            $id = $r->dim_key;
            $label = (string) ($id ?? '—');
            if ($lookupDeptCode && $id !== null && $id !== '' && isset($byCode[$id])) {
                $label = $byCode[$id];
            } elseif ($lookupDeptId && $id !== null && $id !== '' && is_numeric($id)) {
                $label = $byId[(int) $id] ?? ('Dept '.$id);
            }
            $series[] = [
                'name' => $label,
                'value' => round((float) $r->val, 2),
            ];
        }

        return [
            'type' => 'pie',
            'title' => $title,
            'series' => $series,
        ];
    }

    /**
     * @param  \Illuminate\Support\Collection<int, object>  $rows
     * @return array<string, list<array{department: string, program: string, count: float}>>
     */
    /**
     * Simple direct query for OLAP_ADMISSION_GRAND_SUMMARY_REPORT school → program breakdown.
     * Avoids the complex joined query builder to ensure Oracle compatibility.
     *
     * @param  array<int, string>  $columnKeys
     * @return array<string, list<array{department: string, program: string, count: float}>>
     */
    private function biGrandSummaryDirectSchoolDetails(
        string $table,
        Request $request,
        array $dims,
        ?string $metric,
        array $columnKeys
    ): array {
        try {
            $schoolDim = $dims['school'] ?? null;
            $progDim   = $dims['prog']   ?? null;
            $deptDim   = $dims['dept']   ?? null;

            if (! $schoolDim) {
                return [];
            }

            $dateCol = $dims['date'] ?? null;

            $q = DB::table($table);

            // Apply session filter.
            $sessId  = $request->input('sess_id');
            $sessCol = $dims['session_fk'] ?? null;
            if ($sessId && $sessCol) {
                $target = $this->biAcademicSessionJoinTargetColumn($sessCol);
                if ($target === 'sess_id') {
                    $q->where($sessCol, (int) $sessId);
                } else {
                    $code = DB::table($this->biAcademicSessionsTable())->where('sess_id', (int) $sessId)->value('sess_code');
                    if ($code !== null && $code !== '') {
                        $q->where($sessCol, $code);
                    }
                }
            }

            // Apply school filter.
            $schoolId = $request->input('school_id');
            if ($schoolId !== null && $schoolId !== '' && $schoolDim) {
                $q->where($schoolDim, (int) $schoolId);
            }

            // Apply day scope (same snapshot resolver as the main Grand Summary filters).
            if ($dateCol) {
                $day = $this->biGrandSummaryResolveSnapshotDay($request, $table, $dims, $columnKeys);
                $q->where($dateCol, '>=', (clone $day)->startOfDay()->format('Y-m-d H:i:s'));
                $q->where($dateCol, '<=', (clone $day)->endOfDay()->format('Y-m-d H:i:s'));
            }

            // Build SELECT / GROUP BY.
            $grammar = DB::connection()->getQueryGrammar();
            $wSchool = $grammar->wrap($schoolDim);
            $wProg   = $progDim ? $grammar->wrap($progDim) : null;
            $wDept   = $deptDim ? $grammar->wrap($deptDim) : null;

            $selectParts = [$wSchool.' as school_key'];
            $groupCols   = [$schoolDim];

            if ($wDept) {
                $selectParts[] = $wDept.' as dept_key';
                $groupCols[]   = $deptDim;
            } else {
                $selectParts[] = "'No department' as dept_key";
            }

            if ($wProg) {
                $selectParts[] = $wProg.' as prog_key';
                $groupCols[]   = $progDim;
            } else {
                $selectParts[] = "'No program' as prog_key";
            }

            $wMetric       = $metric ? $grammar->wrap($metric) : null;
            $selectParts[] = $wMetric ? 'SUM('.$wMetric.') as val' : 'COUNT(*) as val';
            $metricExpr    = $wMetric ? 'SUM('.$wMetric.')' : 'COUNT(*)';

            $q->selectRaw(implode(', ', $selectParts));
            $q->groupBy(...$groupCols);
            $q->orderByRaw($metricExpr.' DESC');
            $q->limit(1500);

            $rows = $q->get();

            $schoolIsId = $schoolDim && in_array(strtolower($schoolDim), ['school_id', 'sc_school_id', 'sdpo_school_id', 'fk_school_id'], true);
            $schoolIsCode = ! $schoolIsId && $schoolDim && str_contains(strtolower($schoolDim), 'code');
            $schoolIsName = ! $schoolIsId && ! $schoolIsCode;
            $deptIsId = $deptDim && in_array(strtolower($deptDim), ['dept_id', 'department_id', 'sc_dept_id', 'sdpo_dept_id', 'fk_dept_id'], true);
            $progIsId = $progDim && in_array(strtolower($progDim), ['prog_id', 'program_id', 'sc_prog_id', 'sc_program_id', 'sdpo_prog_id', 'fk_prog_id'], true);

            return $this->biGrandSummarySchoolDetailsPayload(
                $rows,
                $schoolIsId,
                $deptDim ? $deptIsId : false,
                $progDim ? $progIsId : false,
                $schoolIsCode,
                $schoolIsName,
                false,
                false,
                false,
                false
            );
        } catch (\Throwable $e) {
            return [];
        }
    }

    private function biGrandSummarySchoolDetailsPayload(
        $rows,
        bool $schoolIsId,
        bool $deptIsId,
        bool $progIsId,
        bool $schoolIsCode = false,
        bool $schoolIsName = false,
        bool $deptIsCode = false,
        bool $deptIsName = false,
        bool $progIsCode = false,
        bool $progIsName = false
    ): array {
        $schoolsById = $schoolIsId ? DB::table('schools')->pluck('school_name', 'school_id') : collect();
        $schoolsByCode = $schoolIsCode ? DB::table('schools')->pluck('school_name', 'school_code') : collect();
        $departmentsById = $deptIsId ? DB::table('departments')->pluck('dept_name', 'dept_id') : collect();
        $departmentsByCode = $deptIsCode ? DB::table('departments')->pluck('dept_name', 'dept_code') : collect();
        $programsById = $progIsId ? DB::table('programs')->pluck('prog_name', 'prog_id') : collect();
        $programsByCode = $progIsCode ? DB::table('programs')->pluck('prog_name', 'prog_code') : collect();

        $norm = function ($value): string {
            $v = mb_strtolower(trim((string) $value));
            $v = str_replace('&', ' and ', $v);

            return (string) preg_replace('/[^a-z0-9]+/i', '', $v);
        };

        $rowVal = function ($row, string $logical): mixed {
            $arr = $this->biRowArrayLower($row);

            return $arr[strtolower($logical)] ?? null;
        };

        $payload = [];
        foreach ($rows as $row) {
            $schoolRaw = $rowVal($row, 'school_key');
            $deptRaw = $rowVal($row, 'dept_key');
            $progRaw = $rowVal($row, 'prog_key');
            $valRaw = $rowVal($row, 'val');

            if ($schoolIsId && $schoolRaw !== null && $schoolRaw !== '') {
                $school = $schoolsById[$schoolRaw] ?? ('School '.$schoolRaw);
            } elseif ($schoolIsCode && $schoolRaw !== null && $schoolRaw !== '' && isset($schoolsByCode[$schoolRaw])) {
                $school = $schoolsByCode[$schoolRaw];
            } elseif ($schoolIsName || (! $schoolIsId && ! $schoolIsCode)) {
                $school = (string) ($schoolRaw ?? 'School');
            } else {
                $school = (string) ($schoolRaw ?? 'School');
            }

            if ($deptIsId && $deptRaw !== null && $deptRaw !== '') {
                $department = $departmentsById[$deptRaw] ?? ('Department '.$deptRaw);
            } elseif ($deptIsCode && $deptRaw !== null && $deptRaw !== '' && isset($departmentsByCode[$deptRaw])) {
                $department = $departmentsByCode[$deptRaw];
            } elseif ($deptIsName || (! $deptIsId && ! $deptIsCode)) {
                $department = (string) ($deptRaw ?? 'No department');
            } else {
                $department = (string) ($deptRaw ?? 'No department');
            }

            if ($progIsId && $progRaw !== null && $progRaw !== '') {
                $program = $programsById[$progRaw] ?? ('Program '.$progRaw);
            } elseif ($progIsCode && $progRaw !== null && $progRaw !== '' && isset($programsByCode[$progRaw])) {
                $program = $programsByCode[$progRaw];
            } elseif ($progIsName || (! $progIsId && ! $progIsCode)) {
                $program = (string) ($progRaw ?? 'No program');
            } else {
                $program = (string) ($progRaw ?? 'No program');
            }

            $schoolKey = $norm($school);
            if (! isset($payload[$schoolKey])) {
                $payload[$schoolKey] = [];
            }
            $payload[$schoolKey][] = [
                'department' => $department !== '' ? $department : 'No department',
                'program' => $program !== '' ? $program : 'No program',
                'count' => (float) ($valRaw ?? 0),
            ];
        }

        return $payload;
    }

    private function biUsesWideSchoolColumns(string $table): bool
    {
        return in_array(strtoupper($table), [
            'OLP_SCHOOL_ATTENDENCE_STUDENTS_SUMMARY',
            'OLP_SCHL_WITHDRAWAL_STD_SUMMARY',
        ], true);
    }

    /**
     * Some summaries store schools as wide metric columns (SCHOOL_1, SCHOOL_12_21, ...).
     *
     * @param  array<int, string>  $columnKeys
     * @return array<string, mixed>
     */
    private function biWideSchoolChartPayload(string $table, array $columnKeys, Request $request): array
    {
        $dims = $this->biResolveDimensionColumns($table, $columnKeys);
        $base = DB::table($table);
        $this->biApplyReportFilters($base, $table, $columnKeys, $request, null);
        if (strtoupper($table) === 'OLP_SCHL_WITHDRAWAL_STD_SUMMARY' && ! empty($dims['date'])) {
            $selectedDate = $request->input('date_to') ?: $request->input('date_from');
            try {
                $day = $selectedDate ? Carbon::parse($selectedDate) : Carbon::today();
            } catch (\Throwable $e) {
                $day = Carbon::today();
            }
            $base->whereRaw($this->biQualifyColumn(null, $dims['date']).' >= ?', [(clone $day)->startOfDay()]);
            $base->whereRaw($this->biQualifyColumn(null, $dims['date']).' <= ?', [(clone $day)->endOfDay()]);
        }
        $this->biOrderGridByLatestDate($base, $dims, null);

        try {
            $row = $base->first();
        } catch (\Throwable $e) {
            return [];
        }

        if (! $row) {
            return [];
        }

        $labels = [];
        $values = [];
        foreach ($columnKeys as $col) {
            $label = $this->biWideSchoolColumnLabel((string) $col);
            if ($label === null) {
                continue;
            }
            $labels[] = $label;
            $values[] = (float) (data_get($row, $col) ?? data_get($row, strtolower((string) $col)) ?? data_get($row, strtoupper((string) $col)) ?? 0);
        }

        if ($labels === []) {
            return [];
        }

        return [
            'by_school' => [
                'type' => 'bar',
                'title' => strtoupper($table) === 'OLP_SCHL_WITHDRAWAL_STD_SUMMARY'
                    ? 'Scholarship withdrawal totals by school'
                    : 'Attendance totals by school',
                'labels' => $labels,
                'values' => $values,
            ],
        ];
    }

    private function biWideSchoolColumnLabel(string $column): ?string
    {
        $key = strtolower($column);
        $key = str_replace(['school_', 'school'], '', $key);
        $key = trim($key, '_');
        if ($key === '' || ! preg_match('/^\d+(?:_\d+)*$/', $key)) {
            return null;
        }

        static $schoolsById = null;
        if ($schoolsById === null) {
            $schoolsById = DB::table('schools')->pluck('school_name', 'school_id')->all();
        }

        $ids = array_filter(explode('_', $key), static function ($id) {
            return $id !== '';
        });
        $labels = [];
        foreach ($ids as $id) {
            $labels[] = $schoolsById[$id] ?? ('School '.$id);
        }

        return implode(' / ', $labels);
    }

    private function biReportColumnLabel(string $table, string $column): string
    {
        if (strtoupper($table) === 'OLP_ADMISSION_ANALYSIS') {
            return $this->biAdmissionAnalysisColumnLabel($column);
        }

        if ($this->biUsesWideSchoolColumns($table)) {
            $schoolLabel = $this->biWideSchoolColumnLabel($column);
            if ($schoolLabel !== null) {
                return $schoolLabel;
            }
        }

        return $this->biFormatColumnLabel($column);
    }

    /**
     * Metadata for BI warehouse / reporting tables (views).
     * Set enabled=false to show the tab as disabled (visible but not clickable / no data load).
     *
     * @return array<string, array{label: string, table: string, description: string, enabled: bool}>
     */
    private function biReportDefinitions(): array
    {
        return [
            'admission_analysis' => [
                'label' => 'Admission Analysis',
                'table' => 'OLP_ADMISSION_ANALYSIS',
                'description' => 'Operational admission metrics and funnel analysis. Session, school, and department names are joined from academic_sessions, schools, and departments where those keys exist on the view.',
                'enabled' => true,
            ],
            'admission_grand_summary' => [
                'label' => 'Admission Grand Summary',
                'table' => 'OLAP_ADMISSION_GRAND_SUMMARY_REPORT',
                'description' => 'Consolidated admission totals and breakdowns. Session, school, and department names are joined from academic_sessions, schools, and departments where those keys exist on the view.',
                'enabled' => true,
            ],
            'fee_challan_summary' => [
                'label' => 'Fee Challan Summary',
                'table' => 'OLP_FEE_CHALLAN_SUMMARY',
                'description' => 'Challan-level fee collection and status summary.',
                'enabled' => false,
            ],
            'gym_subscription_summary' => [
                'label' => 'Gym Subscription Summary',
                'table' => 'OLP_GYM_SUBSCRIPTION_SUMMARY_REPORT',
                'description' => 'Gym subscription counts and subscription health.',
                'enabled' => false,
            ],
            'scholarship_withdrawal_summary' => [
                'label' => ' Withdrawal Summary',
                'table' => 'OLP_SCHL_WITHDRAWAL_STD_SUMMARY',
                'description' => 'Withdrawal student summary.',
                'enabled' => false,
            ],
            'school_attendance_summary' => [
                'label' => 'School Attendance (Students)',
                'table' => 'OLP_SCHOOL_ATTENDENCE_STUDENTS_SUMMARY',
                'description' => 'School-wise student attendance summary (source table name retains original spelling).',
                'enabled' => false,
            ],
        ];
    }

    private function biReportIsEnabled(array $meta): bool
    {
        return ($meta['enabled'] ?? true) === true;
    }

    /**
     * Empty dataset placeholder for disabled report tabs.
     *
     * @return array<string, mixed>
     */
    private function biDisabledReportDataset(string $table, string $label): array
    {
        return [
            'label' => $label,
            'table' => $table,
            'columns' => [],
            'rows' => collect(),
            'displayed' => 0,
            'total' => null,
            'error' => null,
            'disabled' => true,
            'stats' => [
                'row_count' => 0,
                'metric_sum' => null,
                'metric_label' => null,
                'extra_kpis' => [],
                'compare_kpis' => null,
                'range_pivot' => null,
                'fee_challan_summary' => null,
            ],
            'filter_dims' => [],
            'charts' => [],
        ];
    }

    /**
     * @return array{
     *     label: string,
     *     table: string,
     *     columns: list<array{key: string, label: string}>,
     *     rows: \Illuminate\Support\Collection,
     *     displayed: int,
     *     total: int|null,
     *     stats: array{row_count: int, metric_sum: float|int|null, metric_label: string|null, extra_kpis: list<array{label: string, value: float|int}>},
     *     filter_dims: array<string, string|null>,
     *     error: string|null
     * }
     */
    private function loadBiReportDataset(string $table, string $label, Request $request, int $maxRows = 1500): array
    {
        $empty = [
            'label' => $label,
            'table' => $table,
            'columns' => [],
            'rows' => collect(),
            'displayed' => 0,
            'total' => null,
            'error' => null,
            'stats' => ['row_count' => 0, 'metric_sum' => null, 'metric_label' => null, 'extra_kpis' => [], 'compare_kpis' => null, 'range_pivot' => null, 'fee_challan_summary' => null],
            'filter_dims' => [
                'school' => null,
                'dept' => null,
                'prog' => null,
                'date' => null,
                'school_name' => null,
                'dept_name' => null,
                'prog_name' => null,
                'school_code' => null,
                'dept_code' => null,
                'prog_code' => null,
                'session_fk' => null,
            ],
        ];

        try {
            if (! Schema::hasTable($table)) {
                $empty['error'] = 'This table or view is not present in the connected database. Confirm the OLAP/OLP objects exist and naming matches your DB driver (case sensitivity).';

                return $empty;
            }

            $columnKeys = Schema::getColumnListing($table);

            $admissionFrom = $this->biAdmissionOlapFrom($table, $columnKeys, $request);
            if ($admissionFrom !== null) {
                $dims = $this->biResolveDimensionColumns($table, $columnKeys);
                $empty['filter_dims'] = $dims;

                $sessionCol = $dims['session_fk'];
                $schoolCol = $dims['school'];
                $deptCol = $dims['dept'];
                $progCol = $dims['prog'];

                $selects = ['o.*'];
                if ($sessionCol) {
                    $selects[] = 'acs.sess_name as bi_session_name';
                }
                if ($schoolCol) {
                    $selects[] = 'sch.school_name as bi_school_name';
                }
                if ($deptCol) {
                    $selects[] = 'dep.dept_name as bi_department_name';
                }
                if ($progCol) {
                    $selects[] = 'prg.prog_name as bi_program_name';
                }

                $grid = clone $admissionFrom;
                $grid->select($selects);
                $this->biOrderGridByLatestDate($grid, $dims, 'o');
                // Admission BI — Admission Analysis & Admission Grand Summary: one UI row per warehouse row; no PHP duplication.
                // Grand Summary rows that look “repeated” for one school/dept are usually different programs (or null prog); grain comes from the Oracle view.
                $rows = $this->biNormalizeBiRowKeys($grid->limit($maxRows)->get());

                if ($rows->isNotEmpty() && count($columnKeys) === 0) {
                    $columnKeys = array_keys((array) $rows->first());
                    $columnKeys = array_values(array_diff($columnKeys, [
                        'bi_session_name',
                        'bi_school_name',
                        'bi_department_name',
                        'bi_program_name',
                    ]));
                }

                $columns = [];
                foreach ($columnKeys as $col) {
                    if ($sessionCol && $col === $sessionCol) {
                        $columns[] = ['key' => 'bi_session_name', 'label' => 'Academic session'];
                        continue;
                    }
                    $columns[] = [
                        'key' => $col,
                        'label' => $this->biReportColumnLabel($table, (string) $col),
                    ];
                    if ($schoolCol && $col === $schoolCol) {
                        $columns[] = ['key' => 'bi_school_name', 'label' => 'School'];
                    }
                    if ($deptCol && $col === $deptCol) {
                        $columns[] = ['key' => 'bi_department_name', 'label' => 'Department'];
                    }
                    if ($progCol && $col === $progCol) {
                        $columns[] = ['key' => 'bi_program_name', 'label' => 'Program'];
                    }
                }
                if ($sessionCol && ! collect($columns)->contains(function ($c) {
                    return $c['key'] === 'bi_session_name';
                })) {
                    $columns[] = ['key' => 'bi_session_name', 'label' => 'Academic session'];
                }
                if ($schoolCol && ! collect($columns)->contains(function ($c) {
                    return $c['key'] === 'bi_school_name';
                })) {
                    $columns[] = ['key' => 'bi_school_name', 'label' => 'School'];
                }
                if ($deptCol && ! collect($columns)->contains(function ($c) {
                    return $c['key'] === 'bi_department_name';
                })) {
                    $columns[] = ['key' => 'bi_department_name', 'label' => 'Department'];
                }
                if ($progCol && ! collect($columns)->contains(function ($c) {
                    return $c['key'] === 'bi_program_name';
                })) {
                    $columns[] = ['key' => 'bi_program_name', 'label' => 'Program'];
                }

                if (strtoupper($table) === 'OLAP_ADMISSION_GRAND_SUMMARY_REPORT') {
                    $hideIds = array_values(array_filter([
                        $schoolCol,
                        $deptCol,
                        $dims['prog'] ?? null,
                    ]));
                    foreach ($columnKeys as $ck) {
                        if (strtolower((string) $ck) === 'id') {
                            $hideIds[] = $ck;
                            break;
                        }
                    }
                    if ($hideIds !== []) {
                        $hideLower = array_map(function ($h) {
                            return strtolower((string) $h);
                        }, $hideIds);
                        $columns = array_values(array_filter($columns, function ($c) use ($hideLower) {
                            return ! in_array(strtolower((string) ($c['key'] ?? '')), $hideLower, true);
                        }));
                    }
                }

                $stats = [
                    'row_count' => 0,
                    'metric_sum' => null,
                    'metric_label' => null,
                    'extra_kpis' => [],
                    'compare_kpis' => null,
                    'range_pivot' => null,
                    'fee_challan_summary' => null,
                ];
                $stats['extra_kpis'] = $this->biAdmissionAnalysisTodayKpis($table, $request, $dims, $columnKeys);
                $stats['compare_kpis'] = $this->biAdmissionAnalysisCompareKpis($table, $request, $dims, $columnKeys);
                if ($stats['compare_kpis'] === null) {
                    $stats['compare_kpis'] = $this->biGenericReportCompareKpis($table, $request, $dims, $columnKeys);
                }
                if (strtoupper($table) === 'OLP_ADMISSION_ANALYSIS') {
                    // Compare mode scopes From/To to date B internally; skip the day-matrix so only A vs B shows.
                    if (! $request->attributes->get('bi_compare_mode')) {
                        $stats['range_pivot'] = $this->biAdmissionAnalysisRangePivot($table, $request, $dims, $columnKeys);
                    }
                }

                $total = null;
                try {
                    $total = (clone $admissionFrom)->count();
                } catch (\Throwable $e) {
                    $total = null;
                }

                $stats['row_count'] = $total !== null ? (int) $total : (int) $rows->count();

                $metricCol = $this->biPickMetricColumn($table, $columnKeys);
                if ($metricCol) {
                    try {
                        $stats['metric_sum'] = (clone $admissionFrom)->sum(DB::raw($this->biQualifyColumn('o', $metricCol)));
                        $stats['metric_label'] = $this->biFormatColumnLabel((string) $metricCol);
                    } catch (\Throwable $e) {
                        try {
                            $stats['metric_sum'] = (clone $admissionFrom)->sum($metricCol);
                        } catch (\Throwable $e2) {
                            $stats['metric_sum'] = null;
                            $stats['metric_label'] = null;
                        }
                    }
                }

                return [
                    'label' => $label,
                    'table' => $table,
                    'columns' => $columns,
                    'rows' => $rows,
                    'displayed' => $rows->count(),
                    'total' => $total,
                    'error' => null,
                    'stats' => $stats,
                    'filter_dims' => $dims,
                ];
            }

            $dims = $this->biResolveDimensionColumns($table, $columnKeys);
            $empty['filter_dims'] = $dims;

            $base = DB::table($table);
            $this->biApplyReportFilters($base, $table, $columnKeys, $request, null);
            $this->biOrderGridByLatestDate($base, $dims, null);

            $rows = (clone $base)->limit($maxRows)->get();

            if ($rows->isNotEmpty() && count($columnKeys) === 0) {
                $columnKeys = array_keys((array) $rows->first());
                $dims = $this->biResolveDimensionColumns($table, $columnKeys);
                $empty['filter_dims'] = $dims;
                $base = DB::table($table);
                $this->biApplyReportFilters($base, $table, $columnKeys, $request, null);
                $this->biOrderGridByLatestDate($base, $dims, null);
                $rows = (clone $base)->limit($maxRows)->get();
            }

            $columns = [];
            foreach ($columnKeys as $col) {
                $columns[] = [
                    'key' => $col,
                    'label' => $this->biReportColumnLabel($table, (string) $col),
                ];
            }

            $metric = $this->biPickMetricColumn($table, $columnKeys);
            $stats = [
                'row_count' => 0,
                'metric_sum' => null,
                'metric_label' => null,
                'extra_kpis' => [],
                'compare_kpis' => null,
                'range_pivot' => null,
                'fee_challan_summary' => null,
            ];
            try {
                $stats['row_count'] = (clone $base)->count();
            } catch (\Throwable $e) {
                $stats['row_count'] = $rows->count();
            }

            if ($metric) {
                try {
                    $stats['metric_sum'] = (clone $base)->sum($metric);
                    $stats['metric_label'] = $this->biFormatColumnLabel((string) $metric);
                } catch (\Throwable $e) {
                    $stats['metric_sum'] = null;
                }
            }
            $stats['compare_kpis'] = $this->biGenericReportCompareKpis($table, $request, $dims, $columnKeys);
            $stats['fee_challan_summary'] = $this->biFeeChallanSummaryStats($table, $request, $dims, $columnKeys);

            $total = null;
            try {
                $total = (clone $base)->count();
            } catch (\Throwable $e) {
                $total = null;
            }

            return [
                'label' => $label,
                'table' => $table,
                'columns' => $columns,
                'rows' => $rows,
                'displayed' => $rows->count(),
                'total' => $total,
                'error' => null,
                'stats' => $stats,
                'filter_dims' => $dims,
            ];
        } catch (\Throwable $e) {
            $empty['error'] = $e->getMessage();

            return $empty;
        }
    }

    /**
     * @param  mixed  $raw
     */
    private function biFormatChartDateLabel($raw): string
    {
        if ($raw instanceof Carbon) {
            return $raw->format('Y-m-d');
        }
        if ($raw instanceof \DateTimeInterface) {
            return $raw->format('Y-m-d');
        }
        if (is_string($raw)) {
            return substr($raw, 0, 10);
        }

        return (string) $raw;
    }

    private function biFormatColumnLabel(string $column): string
    {
        $label = str_replace('_', ' ', $column);

        return mb_convert_case($label, MB_CASE_TITLE, 'UTF-8');
    }
}
