<?php

namespace App\Http\Controllers\ACL;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use DataTables;

class RfidStudentSubscriptionUtilityController extends Controller
{
    private const CATEGORY_ACL_COLUMN_MAP = [
        'gate' => 'ACL_GATE',
        'gym' => 'ACL_GYM', 
        'hostel' => 'ACL_HOSTEL',
        'lab' => 'ACL_LAB',
    ];

    /**
     * Gate sync test: non-empty = restrict to one enrollment + struck-off filters. '' = off.
     */
    private const GATE_STATUS_SYNC_TEST_ENROLLMENT = '';

    private const GATE_STATUS_SYNC_TEST_MIN_SDPO_SESS_ID = 150;

    private function isAllCategoriesId($requestedCategoryId): bool
    {
        return strtolower(trim((string) $requestedCategoryId)) === 'all';
    }

    /**
     * @return array<int, array{id:int, name:string, acl_column:string}>
     */
    private function getMappedAclGroupContexts(): array
    {
        $out = [];
        foreach (array_keys(self::CATEGORY_ACL_COLUMN_MAP) as $slug) {
            $row = DB::table('ACL_GROUPS')
                ->select('AG_ID as id', 'AG_GROUP_NAME as name')
                ->whereRaw('LOWER(TRIM(AG_GROUP_NAME)) = ?', [$slug])
                ->where('AG_STATUS', 'E')
                ->first();
            if (!empty($row)) {
                $out[] = [
                    'id' => (int) $row->id,
                    'name' => (string) $row->name,
                    'acl_column' => self::CATEGORY_ACL_COLUMN_MAP[$slug],
                ];
            }
        }

        return $out;
    }

    public function index()
    {
        // dd("working");

        $sessions = DB::select("select * from ACADEMIC_SESSIONS where SESS_STATUS = 'E' ORDER BY SESS_ID DESC");

        $months = [
            ['value' => '01', 'label' => 'January'],
            ['value' => '02', 'label' => 'February'],
            ['value' => '03', 'label' => 'March'],
            ['value' => '04', 'label' => 'April'],
            ['value' => '05', 'label' => 'May'],
            ['value' => '06', 'label' => 'June'],
            ['value' => '07', 'label' => 'July'],
            ['value' => '08', 'label' => 'August'],
            ['value' => '09', 'label' => 'September'],
            ['value' => '10', 'label' => 'October'],
            ['value' => '11', 'label' => 'November'],
            ['value' => '12', 'label' => 'December'],
        ];

        // For now, keep only Gym in dropdown.
        $categories = DB::table('ACL_GROUPS')
            ->select('AG_ID as id', 'AG_GROUP_NAME as name')
            ->where('AG_STATUS', 'E')
            ->orderBy('AG_ID')
            ->get();

        $defaultCategoryId = 2;

        $studentApplyStatuses = DB::table('students_apply_info')
            ->select('sai_student_status')
            ->whereNotNull('sai_student_status')
            ->distinct()
            ->orderBy('sai_student_status')
            ->pluck('sai_student_status')
            ->values();

        return view('acl.rfid_student_subscriptions', compact('sessions', 'months', 'categories', 'defaultCategoryId', 'studentApplyStatuses'));
    }

    public function list(Request $request)
    {
        // dd($request->all());

        if ($this->isAllCategoriesId($request->get('category_id'))) {
            $draw = (int) $request->input('draw', 0);

            return response()->json([
                'draw' => $draw,
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'all_categories_hint' => 'Category "All" is for bulk enable/blacklist on every group\'s machines (same student list as Gate). Pick a single category above to load this table.',
            ]);
        }

        $category = $this->resolveCategoryContext($request->get('category_id'));
        $isGateCategory = strtolower(trim($category['name'])) === 'gate';

        if ($isGateCategory) {
            return $this->listGateFromStudentsApplyInfo($request, $category);
        }

        if (strtolower(trim($category['name'])) === 'hostel') {
            return $this->listHostelFromHostelAssign($request, $category);
        }

        $query = DB::table('SUBSCRIPTIONS as sub')
            ->select(
                'sub.ID as id',
                'sub.USER_ID as user_id',
                'sub.STUDENT_FULL_NAME as student_full_name',
                'sub.CURRENT_STATUS as current_status',
                'sub.ENTRY_DATE as subscription_date',
                'sub.EXPIRATION_DATE as expiration_date',
                'sp.SP_CATEGORY as sp_category',
                'sp.SP_PLAN_TENURE as sp_plan_tenure',
                'acs.SESS_NAME as sess_name',
                'sc.SC_CHALLAN_STATUS as sc_challan_status',
                'sc.CALLBACK_PAID_ON as callback_paid_on',
                'sc.response_status as response_status'
            )
            ->leftJoin('SUBSCRIPTION_PLANS as sp', 'sp.SP_ID', 'sub.SP_ID')
            ->leftJoin('ACADEMIC_SESSIONS as acs', 'acs.SESS_ID', 'sub.SESS_ID')
            ->leftJoin('STUDENT_CHALLAN as sc', 'sc.ID', 'sub.CHALLAN_ID')
            ->whereNull('sub.USER_TYPE')
            ->where('sp.SP_CATEGORY', $category['name']);

        if (!empty($request->session)) {
            $query->where('sub.SESS_ID', $request->session);
        }
        if (!empty($request->month)) {
            $query->whereMonth('sub.ENTRY_DATE', (int) $request->month);
        }
        if (!empty($request->subscription_status)) {
            $query->where('sub.CURRENT_STATUS', $request->subscription_status);
        }
        if (!empty($request->payment_status)) {
            $this->applyPaymentStatusFilter($query, $request->payment_status);
        }

        return DataTables::of($query->orderBy('sub.ID', 'desc')->get())->toJson();
    }

    /**
     * Gate RFID grid: subscriptions are gym/other; gate rows come only from students_apply_info.
     */
    private function listGateFromStudentsApplyInfo(Request $request, array $category)
    {
        $query = DB::table('students_apply_info as sai')
            ->leftJoin('students_personal_info as spi', 'spi.spi_id', '=', 'sai.sai_spi_id')
            ->leftJoin('ACADEMIC_SESSIONS as acs', 'acs.SESS_ID', '=', 'sai.sai_sdpo_sess_id')
            ->select(
                'sai.sai_id as id',
                'sai.sai_enrollment_no as user_id',
                DB::raw("NVL(spi.spi_name, TO_CHAR(sai.sai_enrollment_no)) as student_full_name"),
                DB::raw("'" . str_replace("'", "''", $category['name']) . "' as sp_category"),
                DB::raw("CAST(NULL AS VARCHAR2(50)) as sp_plan_tenure"),
                'sai.sai_student_status as current_status',
                'acs.SESS_NAME as sess_name',
                DB::raw("CAST(NULL AS VARCHAR2(20)) as sc_challan_status"),
                DB::raw('CAST(NULL AS TIMESTAMP(0)) as callback_paid_on'),
                DB::raw('CAST(NULL AS VARCHAR2(30)) as response_status')
            );

        if (!empty($request->session)) {
            $query->where('sai.sai_sdpo_sess_id', $request->session);
        }
        if (!empty($request->student_apply_status)) {
            $query->where('sai.sai_student_status', $request->student_apply_status);
        }
        if (!empty($request->subscription_status)) {
            $query->where('sai.sai_student_status', $request->subscription_status);
        }

        return DataTables::of($query->orderBy('sai.sai_id', 'desc')->get())->toJson();
    }

    /**
     * Hostel RFID grid: rows come from hostel_assign (hostel module), not SUBSCRIPTIONS.
     * Enrollment / name: ha_enrolment_no with optional students_apply_info / SPI; challan via ha_challan_id.
     */
    private function listHostelFromHostelAssign(Request $request, array $category)
    {
        $categoryLabel = str_replace("'", "''", $category['name']);

        $query = DB::table('hostel_assign as ha')
            ->leftJoin('students_apply_info as sai', function ($join) {
                $join->whereRaw('UPPER(TRIM(ha.ha_enrolment_no)) = UPPER(TRIM(sai.sai_enrollment_no))');
            })
            ->leftJoin('students_personal_info as spi', 'spi.spi_id', '=', 'sai.sai_spi_id')
            ->leftJoin('ACADEMIC_SESSIONS as acs', 'acs.SESS_ID', '=', 'ha.ha_session')
            ->leftJoin('STUDENT_CHALLAN as sc', 'sc.ID', '=', 'ha.ha_challan_id')
            ->select(
                'ha.ha_id as id',
                DB::raw('NVL(ha.ha_enrolment_no, TO_CHAR(ha.ha_aai_id)) as user_id'),
                DB::raw("NVL(spi.spi_name, NVL(ha.ha_enrolment_no, TO_CHAR(ha.ha_aai_id))) as student_full_name"),
                DB::raw("'" . $categoryLabel . "' as sp_category"),
                DB::raw("CAST(NULL AS VARCHAR2(50)) as sp_plan_tenure"),
                'sai.sai_student_status as current_status',
                'acs.SESS_NAME as sess_name',
                'sc.SC_CHALLAN_STATUS as sc_challan_status',
                'sc.CALLBACK_PAID_ON as callback_paid_on',
                'sc.response_status as response_status'
            )
            ->where(function ($q) {
                $q->where('ha.ha_status', 'E')
                    ->orWhereNull('ha.ha_status');
            })
            ->where(function ($q) {
                $q->whereNotNull('ha.ha_enrolment_no')
                    ->orWhereNotNull('ha.ha_aai_id');
            });

        if (!empty($request->session)) {
            $query->where('ha.ha_session', $request->session);
        }
        if (!empty($request->month)) {
            $query->whereMonth('sc.CALLBACK_PAID_ON', (int) $request->month);
        }
        if (!empty($request->subscription_status)) {
            $query->where('sai.sai_student_status', $request->subscription_status);
        }
        if (!empty($request->payment_status)) {
            $this->applyPaymentStatusFilter($query, $request->payment_status);
        }

        $rows = $query->orderBy('ha.ha_id', 'desc')->get();

        $base = DataTables::of($rows)->toJson();
        $payload = json_decode($base->getContent(), true) ?: [];
        if ($rows->isEmpty()) {
            $payload['hostel_empty_hint'] = 'hostel_assign (hostel module) mein in filters ke saath koi student record nahi mila. '
                . 'Session select karein aur dubara try karein — agar phir bhi khali ho to is session mein hostel allocation / assignment DB mein maujood nahi ho sakta.';
        }

        return response()->json($payload);
    }

    /**
     * Gate machine check (step 0): build list from students_apply_info, store in cache — no device calls yet.
     */
    public function startGateMachineCheck(Request $request)
    {
        if (!in_array((int) auth()->user()->group_id, [2, 644], true)) {
            return response()->json(['success' => false, 'message' => 'Unauthorized access.'], 403);
        }

        $category = $this->resolveCategoryContext($request->get('category_id'));
        if ($this->isAllCategoriesId($request->get('category_id'))) {
            return response()->json(['success' => false, 'message' => 'Machine check is only available when Gate category is selected (not All).'], 422);
        }
        if (strtolower(trim($category['name'])) !== 'gate') {
            return response()->json(['success' => false, 'message' => 'Machine check is only available for Gate category.'], 422);
        }

        $sessionId = $request->get('session');
        $studentApplyStatus = trim((string) $request->get('student_apply_status'));
        if ($studentApplyStatus === '') {
            return response()->json([
                'success' => false,
                'message' => 'Student status is required (session optional).',
            ], 422);
        }

        $machineIps = $this->getMachineIpsByCategoryId((int) $category['id']);
        if (empty($machineIps)) {
            return response()->json(['success' => false, 'message' => 'No active machines found for Gate.'], 404);
        }

        $machineIp = $machineIps[0];

        $enrollments = $this->getGateMachineCheckEnrollmentsFromStudentsApplyInfo(
            $sessionId,
            $studentApplyStatus,
            $request->get('subscription_status')
        );

        $enrollments = array_values(array_unique(array_filter(array_map('trim', $enrollments), function ($e) {
            return $e !== '';
        })));

        if (count($enrollments) === 0) {
            return response()->json([
                'success' => false,
                'message' => 'No students in scope for these filters.',
            ], 422);
        }

        $runId = uniqid('gate_chk_', true);
        $stateTtl = now()->addHours(8);
        // Large roster in its own key: rewriting it on every step can exceed cache value limits (e.g. DB TEXT / Memcached) and abort long runs.
        Cache::put($this->gateMachineCheckRosterCacheKey($runId), $enrollments, $stateTtl);
        $state = [
            'run_id' => $runId,
            'created_by' => (int) auth()->user()->id,
            'stopped' => false,
            'machine_ip' => $machineIp,
            'index' => 0,
        ];

        Cache::put($this->gateMachineCheckCacheKey($runId), $state, $stateTtl);

        return response()->json([
            'success' => true,
            'run_id' => $runId,
            'total' => count($enrollments),
            'machine_ip' => $machineIp,
            'message' => 'Run started. Call step for each student (one ISAPI search per step).',
        ]);
    }

    /**
     * One student: one UserInfo/Search on the Gate device; UI appends result after each response.
     */
    public function processGateMachineCheckStep(Request $request)
    {
        if (!in_array((int) auth()->user()->group_id, [2, 644], true)) {
            return response()->json(['success' => false, 'message' => 'Unauthorized access.'], 403);
        }

        $runId = (string) $request->get('run_id');
        if ($runId === '') {
            return response()->json(['success' => false, 'message' => 'run_id is required.'], 422);
        }

        $cacheKey = $this->gateMachineCheckCacheKey($runId);
        $rosterKey = $this->gateMachineCheckRosterCacheKey($runId);
        $state = Cache::get($cacheKey);
        if (empty($state)) {
            return response()->json(['success' => false, 'message' => 'Run not found or expired.'], 404);
        }
        if ((int) $state['created_by'] !== (int) auth()->user()->id) {
            return response()->json(['success' => false, 'message' => 'This run belongs to another user.'], 403);
        }

        if (!empty($state['enrollments']) && is_array($state['enrollments'])) {
            $enrollments = $state['enrollments'];
            // One-time migration: old runs stored the full roster inside state and rewrote it every step (huge payloads → cache/store failures mid-run).
            if (count($enrollments) > 200) {
                $migrateTtl = now()->addHours(8);
                Cache::put($rosterKey, $enrollments, $migrateTtl);
                unset($state['enrollments']);
                Cache::put($cacheKey, $state, $migrateTtl);
            }
        } else {
            $enrollments = Cache::get($rosterKey);
            if (!is_array($enrollments)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Enrollment list missing from cache (expired or cleared). Please start a new machine check run.',
                ], 404);
            }
        }
        $total = count($enrollments);
        $index = (int) $state['index'];

        if (!empty($state['stopped'])) {
            return response()->json([
                'success' => true,
                'completed' => true,
                'stopped' => true,
                'progress' => ['current' => $index, 'total' => $total],
                'message' => 'Stopped by user.',
            ]);
        }

        if ($index >= $total) {
            return response()->json([
                'success' => true,
                'completed' => true,
                'stopped' => false,
                'progress' => ['current' => $index, 'total' => $total],
                'message' => 'All students checked.',
            ]);
        }

        $rawEnrollment = trim((string) $enrollments[$index]);
        $empNo = $this->employeeNoForMachineSearch($rawEnrollment);
        $machineIp = $state['machine_ip'];
        $r = $this->searchStudentOnMachineExtended($machineIp, $empNo);

        $isRegistered = $r['success'] && $r['found'];
        $isActive = (bool) ($r['active'] ?? false);

        $appendRegistered = null;
        $appendNotRegistered = null;

        if ($isRegistered) {
            $note = $isActive ? ' — active on device' : ' — blacklist/off on device';
            $appendRegistered = $rawEnrollment . $note;
        } else {
            $line = $rawEnrollment . ' (' . $empNo . ')';
            if (!$r['success'] && !empty($r['error'])) {
                $line .= ' — ' . $r['error'];
            }
            $appendNotRegistered = $line;
        }

        $step = [
            'enrollment' => $rawEnrollment,
            'employee_no' => $empNo,
            'registered' => $isRegistered,
            'is_active' => $isActive,
            'reason' => $isRegistered ? null : (!$r['success'] ? 'api_error' : 'not_on_device'),
            'detail' => $isRegistered ? null : (!$r['success'] ? ($r['error'] ?? 'Search failed') : null),
        ];

        $state['index'] = $index + 1;
        $stateTtl = now()->addHours(8);
        if (!empty($state['enrollments'])) {
            Cache::put($cacheKey, $state, $stateTtl);
        } else {
            $stateForCache = $state;
            unset($stateForCache['enrollments']);
            Cache::put($cacheKey, $stateForCache, $stateTtl);
        }

        $completed = $state['index'] >= $total;
        if ($completed && empty($state['enrollments'])) {
            Cache::forget($rosterKey);
        }

        return response()->json([
            'success' => true,
            'completed' => $completed,
            'stopped' => false,
            'progress' => [
                'current' => $state['index'],
                'total' => $total,
                'just_checked' => $index + 1,
            ],
            'machine_ip' => $machineIp,
            'step' => $step,
            'append_registered' => $appendRegistered,
            'append_not_registered' => $appendNotRegistered,
        ]);
    }

    public function stopGateMachineCheck(Request $request)
    {
        $runId = (string) $request->get('run_id');
        if ($runId === '') {
            return response()->json(['success' => false, 'message' => 'run_id is required.'], 422);
        }

        $cacheKey = $this->gateMachineCheckCacheKey($runId);
        $state = Cache::get($cacheKey);
        if (empty($state)) {
            return response()->json(['success' => false, 'message' => 'Run not found or expired.'], 404);
        }
        if ((int) $state['created_by'] !== (int) auth()->user()->id) {
            return response()->json(['success' => false, 'message' => 'This run belongs to another user.'], 403);
        }

        $state['stopped'] = true;
        $stateTtl = now()->addHours(8);
        if (!empty($state['enrollments'])) {
            Cache::put($cacheKey, $state, $stateTtl);
        } else {
            $stateForCache = $state;
            unset($stateForCache['enrollments']);
            Cache::put($cacheKey, $stateForCache, $stateTtl);
        }

        return response()->json([
            'success' => true,
            'run_id' => $runId,
            'message' => 'Gate check stopped.',
        ]);
    }

    /**
     * Start bulk sync run and save plan in cache.
     * Final flow:
     * 1) Disable pass using students_apply_info enrollment list (only if active on machine).
     * 2) Enable pass for paid students.
     */
    public function startBulkSync(Request $request)
    {
        if (!in_array((int) auth()->user()->group_id, [2, 644], true)) {
            return response()->json(['success' => false, 'message' => 'Unauthorized access.'], 403);
        }

        $category = $this->resolveCategoryContext($request->get('category_id'));
        $sessionId = $request->get('session');
        $isAllCategories = $this->isAllCategoriesId($request->get('category_id'));

        if ($isAllCategories) {
            $studentApplyStatus = trim((string) $request->get('student_apply_status'));
            $machineAction = strtolower(trim((string) $request->get('rfid_machine_action')));
            if (empty($sessionId) || $studentApplyStatus === '' || !in_array($machineAction, ['blacklist', 'active'], true)) {
                return response()->json([
                    'success' => false,
                    'message' => 'All categories sync requires Session, Student Status, and Machine action (Blacklist or Active).',
                ], 422);
            }

            $targets = $this->getStudentsForGateStatusSync(
                'Gate',
                $sessionId,
                $studentApplyStatus,
                $request->get('subscription_status')
            );
            $targetSet = collect($targets)->map(fn($v) => $this->normalizeEnrollment($v))->unique()->values();

            if ($targetSet->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No students found for the selected session/status (students_apply_info).',
                ], 422);
            }

            $contexts = $this->getMappedAclGroupContexts();
            $operations = [];
            $totalMachineIps = [];

            foreach ($targetSet as $studentId) {
                foreach ($contexts as $ctx) {
                    $ips = $this->getMachineIpsByCategoryId($ctx['id']);
                    if (empty($ips)) {
                        continue;
                    }
                    foreach ($ips as $ip) {
                        $totalMachineIps[$ip] = true;
                    }
                    $operations[] = [
                        'action' => $machineAction === 'blacklist' ? 'disable_if_active' : 'enable',
                        'student' => $studentId,
                        'acl_column' => $ctx['acl_column'],
                        'machine_ips' => $ips,
                        'category_label' => $ctx['name'],
                    ];
                }
            }

            if (empty($operations)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No active machines found for any mapped category (Gate, Gym, Hostel, Lab).',
                ], 404);
            }

            $toDisable = $machineAction === 'blacklist' ? count($operations) : 0;
            $toEnable = $machineAction === 'active' ? count($operations) : 0;

            $runId = uniqid('rfid_sync_', true);
            $state = [
                'run_id' => $runId,
                'created_by' => (int) auth()->user()->id,
                'stopped' => false,
                'mode' => 'all_gate_status',
                'gate_action' => $machineAction,
                'student_apply_status' => $studentApplyStatus,
                'category' => $category,
                'machine_ips' => [],
                'operations' => $operations,
                'index' => 0,
                'summary' => [
                    'disable_source_count' => 0,
                    'target_paid_count' => $targetSet->count(),
                    'to_enable_count' => $toEnable,
                    'to_disable_count' => $toDisable,
                    'processed' => 0,
                    'enabled_done' => 0,
                    'disabled_done' => 0,
                    'failed' => 0,
                    'machine_active_found' => 0,
                    'machine_not_found' => 0,
                    'already_disabled' => 0,
                ],
            ];

            Cache::put($this->cacheKey($runId), $state, now()->addHours(2));

            return response()->json([
                'success' => true,
                'run_id' => $runId,
                'gate_mode' => true,
                'all_categories_mode' => true,
                'gate_action' => $machineAction,
                'student_apply_status' => $studentApplyStatus,
                'message' => 'All-categories status sync plan created. Running in step-by-step mode.',
                'category_name' => 'All',
                'disable_scope' => 'ALL_GROUPS_GATE_STUDENT_LIST',
                'enable_scope' => 'ALL_GROUPS_GATE_STUDENT_LIST',
                'summary' => $state['summary'],
                'machine_count' => count($totalMachineIps),
                'categories_in_run' => count(array_unique(array_column($operations, 'category_label'))),
                'sample_disable_source' => [],
            ]);
        }

        $machineIps = $this->getMachineIpsByCategoryId((int) $category['id']);

        if (empty($machineIps)) {
            return response()->json(['success' => false, 'message' => 'No active machines found for selected category.'], 404);
        }

        $isGateCategory = strtolower(trim($category['name'])) === 'gate';

        if ($isGateCategory) {
            $studentApplyStatus = trim((string) $request->get('student_apply_status'));
            $machineAction = strtolower(trim((string) $request->get('rfid_machine_action')));
            if (empty($sessionId) || $studentApplyStatus === '' || !in_array($machineAction, ['blacklist', 'active'], true)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gate sync requires Session, Student Status, and Machine action (Blacklist or Active).',
                ], 422);
            }

            $targets = $this->getStudentsForGateStatusSync(
                $category['name'],
                $sessionId,
                $studentApplyStatus,
                $request->get('subscription_status')
            );
            $targetSet = collect($targets)->map(fn($v) => $this->normalizeEnrollment($v))->unique()->values();

            $operations = [];
            foreach ($targetSet as $studentId) {
                $operations[] = [
                    'action' => $machineAction === 'blacklist' ? 'disable_if_active' : 'enable',
                    'student' => $studentId,
                ];
            }

            $toDisable = $machineAction === 'blacklist' ? $targetSet->count() : 0;
            $toEnable = $machineAction === 'active' ? $targetSet->count() : 0;

            $runId = uniqid('rfid_sync_', true);
            $state = [
                'run_id' => $runId,
                'created_by' => (int) auth()->user()->id,
                'stopped' => false,
                'mode' => 'gate_status',
                'gate_action' => $machineAction,
                'student_apply_status' => $studentApplyStatus,
                'category' => $category,
                'machine_ips' => $machineIps,
                'operations' => $operations,
                'index' => 0,
                'summary' => [
                    'disable_source_count' => 0,
                    'target_paid_count' => $targetSet->count(),
                    'to_enable_count' => $toEnable,
                    'to_disable_count' => $toDisable,
                    'processed' => 0,
                    'enabled_done' => 0,
                    'disabled_done' => 0,
                    'failed' => 0,
                    'machine_active_found' => 0,
                    'machine_not_found' => 0,
                    'already_disabled' => 0,
                ],
            ];

            Cache::put($this->cacheKey($runId), $state, now()->addHours(2));

            return response()->json([
                'success' => true,
                'run_id' => $runId,
                'gate_mode' => true,
                'gate_action' => $machineAction,
                'student_apply_status' => $studentApplyStatus,
                'message' => 'Gate status sync plan created. Running in step-by-step mode.',
                'category_name' => $category['name'],
                'disable_scope' => 'GATE_STUDENT_STATUS',
                'enable_scope' => 'GATE_STUDENT_STATUS',
                'summary' => $state['summary'],
                'machine_count' => count($machineIps),
                'sample_disable_source' => [],
            ]);
        }

        if (empty($request->session) || empty($request->category_id) || empty($request->month) || $request->payment_status !== 'Paid') {
            return response()->json([
                'success' => false,
                'message' => 'Session, Category, Month and Payment Status = Paid are required.',
            ], 422);
        }

        $month = $this->resolveMonth($request->get('month'));

        $activeDbStudents = $this->getActiveDatabaseEnrollmentsByAcl($category['acl_column']);
        $targetPaid = $this->getPaidStudentsForFilters($category['name'], $month, $sessionId);

        $disableSet = collect($activeDbStudents)->map(fn($v) => $this->normalizeEnrollment($v))->unique()->values();
        $targetSet = collect($targetPaid)->map(fn($v) => $this->normalizeEnrollment($v))->unique()->values();

        $operations = [];
        foreach ($disableSet as $studentId) {
            $operations[] = ['action' => 'disable_if_active', 'student' => $studentId];
        }
        foreach ($targetSet as $studentId) {
            $operations[] = ['action' => 'enable', 'student' => $studentId];
        }

        $runId = uniqid('rfid_sync_', true);
        $state = [
            'run_id' => $runId,
            'created_by' => (int) auth()->user()->id,
            'stopped' => false,
            'mode' => 'paid_sync',
            'category' => $category,
            'machine_ips' => $machineIps,
            'operations' => $operations,
            'index' => 0,
            'summary' => [
                'disable_source_count' => $disableSet->count(),
                'target_paid_count' => $targetSet->count(),
                'to_enable_count' => $targetSet->count(),
                'to_disable_count' => $disableSet->count(),
                'processed' => 0,
                'enabled_done' => 0,
                'disabled_done' => 0,
                'failed' => 0,
                'machine_active_found' => 0,
                'machine_not_found' => 0,
                'already_disabled' => 0,
            ],
        ];

        Cache::put($this->cacheKey($runId), $state, now()->addHours(2));

        return response()->json([
            'success' => true,
            'run_id' => $runId,
            'gate_mode' => false,
            'message' => 'Bulk sync plan created. Running in step-by-step mode.',
            'category_name' => $category['name'],
            'disable_scope' => 'ALL_SESSIONS',
            'enable_scope' => 'SELECTED_FILTERS',
            'summary' => $state['summary'],
            'machine_count' => count($machineIps),
            'sample_disable_source' => $disableSet->take(10)->values(),
        ]);
    }

    /**
     * Process one operation per call for live log behavior.
     */
    public function processBulkSyncStep(Request $request)
    {
        $runId = (string) $request->get('run_id');
        if ($runId === '') {
            return response()->json(['success' => false, 'message' => 'run_id is required.'], 422);
        }

        $cacheKey = $this->cacheKey($runId);
        $state = Cache::get($cacheKey);
        if (empty($state)) {
            return response()->json(['success' => false, 'message' => 'Sync run not found or expired.'], 404);
        }

        if ((int) $state['created_by'] !== (int) auth()->user()->id) {
            return response()->json(['success' => false, 'message' => 'This sync run belongs to another user.'], 403);
        }
        if (!empty($state['stopped'])) {
            return response()->json([
                'success' => true,
                'completed' => true,
                'stopped' => true,
                'log_line' => 'Sync stopped by user.',
                'summary' => $state['summary'],
                'machine_results' => [],
            ]);
        }

        $index = (int) $state['index'];
        $operations = $state['operations'];

        if (!isset($operations[$index])) {
            return response()->json([
                'success' => true,
                'completed' => true,
                'log_line' => 'Sync completed.',
                'summary' => $state['summary'],
            ]);
        }

        $operation = $operations[$index];
        $action = $operation['action'];
        $student = $operation['student'];
        $aclColumn = $operation['acl_column'] ?? $state['category']['acl_column'];
        $machineIps = $operation['machine_ips'] ?? $state['machine_ips'];
        $rowUpdated = false;
        if ($action === 'disable_if_active') {
            // Step-1 LIVE: Disable only when user exists and is active on machine.
            $stepResult = $this->disableStudentIfActiveOnMachines($student, $aclColumn, $machineIps);
            $machineFailed = $stepResult['machine_failed'];
            $state['summary']['machine_active_found'] += $stepResult['machine_active_found'];
            $state['summary']['machine_not_found'] += $stepResult['machine_not_found'];
            $state['summary']['already_disabled'] += $stepResult['already_disabled'];
            if ($stepResult['disabled_changed']) {
                $state['summary']['disabled_done']++;
                $rowUpdated = true;
            }
            $logLine = $stepResult['log_line'];
        } else {
            // Step-2 LIVE: Enable paid student on all machines and update SISUSERS.
            $stepResult = $this->enableStudentOnAllMachines($student, $aclColumn, $machineIps);
            $machineFailed = $stepResult['machine_failed'];
            if ($stepResult['enabled_changed']) {
                $state['summary']['enabled_done']++;
                $rowUpdated = true;
            }
            $logLine = $stepResult['log_line'];
        }

        $state['summary']['processed']++;
        if ($machineFailed > 0) {
            $state['summary']['failed']++;
        }

        $state['index'] = $index + 1;
        Cache::put($cacheKey, $state, now()->addHours(2));

        $completed = $state['index'] >= count($operations);

        if (!empty($operation['category_label'])) {
            $logLine = '[' . $operation['category_label'] . '] ' . $logLine;
        }

        return response()->json([
            'success' => true,
            'completed' => $completed,
            'log_line' => $logLine,
            'summary' => $state['summary'],
            'action' => $action,
            'student' => $student,
            'row_updated' => $rowUpdated,
            'machine_results' => $stepResult['machine_results'] ?? [],
        ]);
    }

    public function stopBulkSync(Request $request)
    {
        $runId = (string) $request->get('run_id');
        if ($runId === '') {
            return response()->json(['success' => false, 'message' => 'run_id is required.'], 422);
        }

        $cacheKey = $this->cacheKey($runId);
        $state = Cache::get($cacheKey);
        if (empty($state)) {
            return response()->json(['success' => false, 'message' => 'Sync run not found or expired.'], 404);
        }
        if ((int) $state['created_by'] !== (int) auth()->user()->id) {
            return response()->json(['success' => false, 'message' => 'This sync run belongs to another user.'], 403);
        }

        $state['stopped'] = true;
        Cache::put($cacheKey, $state, now()->addHours(2));

        return response()->json([
            'success' => true,
            'run_id' => $runId,
            'message' => 'Sync stopped successfully.',
        ]);
    }
    private function getActiveDatabaseEnrollmentsByAcl(string $aclColumn): array
    {
        $rows = DB::table('SISUSERS')
            ->selectRaw('USERNAME as username')
            ->where('GROUP_ID', 4)
            ->where($aclColumn, 1)
            ->whereNotNull('USERNAME')
            ->get();

        return collect($rows)
            ->map(fn($row) => trim((string) ($row->username ?? $row->USERNAME ?? $row->user_name ?? '')))
            ->filter(fn($value) => $value !== '')
            ->values()
            ->toArray();
    }


    private function cacheKey(string $runId): string
    {
        return 'rfid_bulk_sync_' . $runId;
    }

    private function gateMachineCheckCacheKey(string $runId): string
    {
        return 'rfid_gate_machine_check_' . $runId;
    }

    private function gateMachineCheckRosterCacheKey(string $runId): string
    {
        return 'rfid_gate_machine_check_roster_' . $runId;
    }

    private function getPaidStudentsForFilters(string $categoryName, int $month, $sessionId): array
    {
        if (strtolower(trim($categoryName)) === 'hostel') {
            $query = DB::table('hostel_assign as ha')
                ->join('STUDENT_CHALLAN as sc', 'sc.ID', '=', 'ha.ha_challan_id')
                ->selectRaw('ha.ha_enrolment_no as user_id')
                ->whereNotNull('ha.ha_enrolment_no')
                ->where('sc.SC_CHALLAN_STATUS', 'PAID')
                ->whereNotNull('sc.CALLBACK_PAID_ON')
                ->where('sc.response_status', 'Success')
                ->whereMonth('sc.CALLBACK_PAID_ON', $month)
                ->where('sc.SC_CHALLAN_TYPE', 'Hostel_Fee');

            if (!empty($sessionId)) {
                $query->where('ha.ha_session', $sessionId);
            }

            $rows = $query->get();

            return collect($rows)
                ->map(fn($row) => (string) ($row->user_id ?? $row->USER_ID ?? $row->userId ?? ''))
                ->filter(fn($value) => trim($value) !== '')
                ->unique()
                ->values()
                ->toArray();
        }

        $query = DB::table('SUBSCRIPTIONS as sub')
            ->leftJoin('SUBSCRIPTION_PLANS as sp', 'sp.SP_ID', 'sub.SP_ID')
            ->leftJoin('STUDENT_CHALLAN as sc', 'sc.ID', 'sub.CHALLAN_ID')
            ->selectRaw('sub.USER_ID as user_id')
            ->whereNull('sub.USER_TYPE')
            ->where('sp.SP_CATEGORY', $categoryName)
            ->whereMonth('sub.ENTRY_DATE', $month)
            ->whereNotNull('sc.CALLBACK_PAID_ON')
            ->where('sc.response_status', 'Success')
            ->where('sc.SC_CHALLAN_STATUS', 'PAID');

        if (!empty($sessionId)) {
            $query->where('sub.SESS_ID', $sessionId);
        }

        $rows = $query->get();

        return collect($rows)
            ->map(fn($row) => (string) ($row->user_id ?? $row->USER_ID ?? $row->userId ?? ''))
            ->filter(fn($value) => trim($value) !== '')
            ->values()
            ->toArray();
    }

    /**
     * Gate RFID enrollments: Gym (etc.) use SUBSCRIPTIONS; gate uses only students_apply_info.
     *
     * When {@see self::GATE_STATUS_SYNC_TEST_ENROLLMENT} is non-empty, only that roll number is returned
     * (with struck-off / min SDPO session guards) for safe testing; set it to '' for the full cohort.
     */
    private function getStudentsForGateStatusSync(string $_categoryName, $sessionId, string $studentApplyStatus, $subscriptionStatus = null): array
    {
        $query = DB::table('students_apply_info as sai')
            ->whereNotNull('sai.sai_enrollment_no')
            ->selectRaw('sai.sai_enrollment_no as user_id');

        $testEnrollment = trim((string) self::GATE_STATUS_SYNC_TEST_ENROLLMENT);
        if ($testEnrollment !== '') {
            $normalizedTest = strtolower(str_replace('-', '', $testEnrollment));
            $query->whereIn('sai.sai_student_status', ['Struck off', 'Stuck-Off'])
                ->where('sai.sai_sdpo_sess_id', '>', self::GATE_STATUS_SYNC_TEST_MIN_SDPO_SESS_ID)
                ->whereRaw(
                    'LOWER(TRIM(REPLACE(sai.sai_enrollment_no, \'-\', \'\'))) = ?',
                    [$normalizedTest]
                );
        } else {
            $query->where('sai.sai_student_status', $studentApplyStatus);
        }

        if (!empty($sessionId)) {
            $query->where('sai.sai_sdpo_sess_id', $sessionId);
        }
        if (!empty($subscriptionStatus)) {
            $query->where('sai.sai_student_status', $subscriptionStatus);
        }

        $rows = $query->get();

        return collect($rows)
            ->map(fn($row) => (string) ($row->user_id ?? $row->USER_ID ?? ''))
            ->filter(fn($value) => trim($value) !== '')
            ->values()
            ->toArray();
    }

    /**
     * Enrollments for Gate "Check on machines": always from students_apply_info only (same filters as Gate grid).
     * Does not use {@see getStudentsForGateStatusSync} test-enrollment shortcuts.
     */
    private function getGateMachineCheckEnrollmentsFromStudentsApplyInfo(
        $sessionId,
        string $studentApplyStatus,
        $subscriptionStatus = null
    ): array {
        $query = DB::table('students_apply_info as sai')
            ->whereNotNull('sai.sai_enrollment_no')
            ->selectRaw('sai.sai_enrollment_no as user_id')
            ->where('sai.sai_student_status', $studentApplyStatus)
            ->where('sai.sai_sdpo_sess_id', '>',150);

        if (!empty($sessionId)) {
            $query->where('sai.sai_sdpo_sess_id', $sessionId);
        }
        if (!empty($subscriptionStatus)) {
            $query->where('sai.sai_student_status', $subscriptionStatus);
        }

        $rows = $query->get();

        return collect($rows)
            ->map(fn($row) => (string) ($row->user_id ?? $row->USER_ID ?? ''))
            ->filter(fn($value) => trim($value) !== '')
            ->unique()
            ->values()
            ->toArray();
    }

    private function resolveCategoryContext($requestedCategoryId): array
    {
        if ($this->isAllCategoriesId($requestedCategoryId)) {
            return ['id' => 0, 'name' => 'All', 'acl_column' => null];
        }

        $categoryId = (int) $requestedCategoryId;
        if ($categoryId > 0) {
            $row = DB::table('ACL_GROUPS')
                ->select('AG_ID as id', 'AG_GROUP_NAME as name')
                ->where('AG_ID', $categoryId)
                ->first();
            if (!empty($row)) {
                return [
                    'id' => (int) $row->id,
                    'name' => (string) $row->name,
                    'acl_column' => $this->getAclColumnByCategoryName((string) $row->name),
                ];
            }
        }

        $gymRow = DB::table('ACL_GROUPS')
            ->select('AG_ID as id', 'AG_GROUP_NAME as name')
            ->whereRaw('LOWER(AG_GROUP_NAME) = ?', ['gym'])
            ->first();

        if (!empty($gymRow)) {
            return [
                'id' => (int) $gymRow->id,
                'name' => (string) $gymRow->name,
                'acl_column' => $this->getAclColumnByCategoryName((string) $gymRow->name),
            ];
        }

        return ['id' => 2, 'name' => 'Gym', 'acl_column' => 'ACL_GYM'];
    }

    private function getAclColumnByCategoryName(string $categoryName): string
    {
        return self::CATEGORY_ACL_COLUMN_MAP[strtolower(trim($categoryName))] ?? 'ACL_GYM';
    }

    private function applyPaymentStatusFilter($query, string $paymentStatus): void
    {
        if ($paymentStatus === 'Paid') {
            $query->whereNotNull('sc.CALLBACK_PAID_ON')
                ->where('sc.response_status', 'Success')
                ->where('sc.SC_CHALLAN_STATUS', 'PAID');
            return;
        }
        if ($paymentStatus === 'Unpaid') {
            $query->whereNull('sc.CALLBACK_PAID_ON')
                ->whereNull('sc.response_status')
                ->where('sc.SC_CHALLAN_STATUS', 'UNPAID');
            return;
        }
        $query->where('sc.SC_CHALLAN_STATUS', 'CANCELLED');
    }

    private function normalizeEnrollment(string $enrollment): string
    {
        return strtoupper(str_replace('-', '', trim($enrollment)));
    }

    private function resolveMonth($month): int
    {
        $month = (int) $month;
        return ($month < 1 || $month > 12) ? (int) now()->format('m') : $month;
    }

    private function getMachineIpsByCategoryId(int $categoryId): array
    {
        $rows = DB::table('ACL_GROUPS_DETAILS')
            ->selectRaw('AGD_MACHINE_IP as machine_ip')
            ->where('AGD_AG_ID', $categoryId)
            ->where('AGD_MACHINE_STATUS', 'E')
            ->whereNotNull('AGD_MACHINE_IP')
            ->get();

        return collect($rows)
            ->map(fn($row) => trim((string) ($row->machine_ip ?? '')))
            ->filter(fn($ip) => $ip !== '')
            ->unique()
            ->values()
            ->toArray();
    }

    private function updateSisusersAclColumn(string $normalizedEnrollment, string $aclColumn, bool $shouldEnable, ?int $groupId = null): array
    {
        $query = DB::table('SISUSERS')
            ->whereRaw("LOWER(TRIM(REPLACE(USERNAME, '-', ''))) = ?", [strtolower($normalizedEnrollment)]);

        if ($groupId !== null) {
            $query->where('GROUP_ID', $groupId);
        }

        $rows = $query->update([$aclColumn => $shouldEnable ? 1 : null]);

        return ['updated' => $rows > 0, 'rows' => $rows];
    }

    private function buildMachinePayload(string $employeeNo, string $action): array
    {
        $isEnableAction = $action === 'enable';
        return [
            'UserInfo' => [
                'employeeNo' => $employeeNo,
                'userType' => $isEnableAction ? 'normal' : 'blackList',
                'Valid' => [
                    'enable' => $isEnableAction,
                    'beginTime' => now()->startOfDay()->format('Y-m-d\TH:i:s'),
                    'endTime' => now()->addYears(2)->endOfDay()->format('Y-m-d\TH:i:s'),
                    'timeType' => 'local',
                ],
                'RightPlan' => [['doorNo' => 1, 'planTemplateNo' => '1']],
                'maxOpenDoorTime' => 0,
                'addUser' => false,
                'gender' => 'male',
            ],
        ];
    }

    private function disableStudentIfActiveOnMachines(string $student, string $aclColumn, array $machineIps): array
    {
        $displayStudent = $this->formatEnrollmentForLog($student);
        $foundActive = 0;
        $notFound = 0;
        $alreadyDisabled = 0;
        $failed = 0;
        $disabledOnAnyMachine = false;
        $machineResults = [];
        // User-requested flow: disable DB access first, then process machine disable.
        // Restrict to student group (GROUP_ID=4) for this module.
        $sisusers = $this->updateSisusersAclColumn($student, $aclColumn, false, 4);
        $wasActiveInDatabase = $sisusers['updated'];

        foreach ($machineIps as $machineIp) {
            $search = $this->searchStudentOnMachine($machineIp, $student);
            if (!$search['success']) {
                $failed++;
                $machineResults[] = [
                    'machine_ip' => $machineIp,
                    'status' => 'error',
                    'outcome' => 'search_failed',
                    'message' => 'Machine search failed. ' . ($search['error'] ?? ''),
                ];
                continue;
            }
            if (!$search['found']) {
                $notFound++;
                $machineResults[] = [
                    'machine_ip' => $machineIp,
                    'status' => 'info',
                    'outcome' => 'not_found',
                    'message' => 'Student not found on machine.',
                ];
                continue;
            }
            if (!$search['active']) {
                $alreadyDisabled++;
                $machineResults[] = [
                    'machine_ip' => $machineIp,
                    'status' => 'info',
                    'outcome' => 'already_disabled',
                    'message' => 'Already disabled on machine. No machine update needed.',
                ];
                continue;
            }

            $foundActive++;
            $disablePayload = $this->buildMachinePayload($student, 'disable');
            $update = $this->updateStudentOnMachine($machineIp, $disablePayload);
            if ($update['success']) {
                $disabledOnAnyMachine = true;
                $machineResults[] = [
                    'machine_ip' => $machineIp,
                    'status' => 'success',
                    'outcome' => 'disabled',
                    'message' => 'Student disabled successfully on machine.',
                ];
            } else {
                $failed++;
                $machineResults[] = [
                    'machine_ip' => $machineIp,
                    'status' => 'error',
                    'outcome' => 'disable_failed',
                    'message' => 'Disable request failed on machine. ' . ($update['error'] ?? ''),
                ];
            }
        }
        $disabledChanged = $disabledOnAnyMachine || $sisusers['updated'];

        return [
            'machine_failed' => $failed,
            'machine_active_found' => $foundActive,
            'machine_not_found' => $notFound,
            'already_disabled' => $alreadyDisabled,
            'disabled_changed' => $disabledChanged,
            'machine_results' => $machineResults,
            'log_line' => 'Disable check for ' . $displayStudent
                . ' | Active in database before step: ' . ($wasActiveInDatabase ? 'YES' : 'NO')
                . ' | Active on machine: ' . $foundActive
                . ' | Already disabled on machine: ' . $alreadyDisabled
                . ' | Machine updated: ' . ($disabledOnAnyMachine ? 'YES' : 'NO')
                . ' | Database updated: ' . ($sisusers['updated'] ? 'YES' : 'NO')
                . ' | Machine errors: ' . $failed,
        ];
    }

    private function enableStudentOnAllMachines(string $student, string $aclColumn, array $machineIps): array
    {
        $displayStudent = $this->formatEnrollmentForLog($student);
        $enabledOnAnyMachine = false;
        $failed = 0;
        $machineResults = [];

        $payload = $this->buildMachinePayload($student, 'enable');
        foreach ($machineIps as $machineIp) {
            $update = $this->updateStudentOnMachine($machineIp, $payload);
            if ($update['success']) {
                $enabledOnAnyMachine = true;
                $machineResults[] = [
                    'machine_ip' => $machineIp,
                    'status' => 'success',
                    'outcome' => 'enabled',
                    'message' => 'Student enabled successfully on machine.',
                ];
            } else {
                $failed++;
                $machineResults[] = [
                    'machine_ip' => $machineIp,
                    'status' => 'error',
                    'outcome' => 'enable_failed',
                    'message' => 'Enable request failed on machine. ' . ($update['error'] ?? ''),
                ];
            }
        }

        $sisusers = ['updated' => false];
        if ($enabledOnAnyMachine) {
            $sisusers = $this->updateSisusersAclColumn($student, $aclColumn, true, 4);
        }

        return [
            'machine_failed' => $failed,
            'enabled_changed' => $enabledOnAnyMachine,
            'machine_results' => $machineResults,
            'log_line' => 'Enable step for ' . $displayStudent
                . ' | Machine updated: ' . ($enabledOnAnyMachine ? 'YES' : 'NO')
                . ' | Database updated: ' . ($sisusers['updated'] ? 'YES' : 'NO')
                . ' | Machine errors: ' . $failed,
        ];
    }

    private function formatEnrollmentForLog(string $student): string
    {
        $student = strtoupper(trim($student));

        // Examples:
        // S20260124 -> S2026-0124
        // F2020220  -> F2020-220
        if (preg_match('/^[A-Z]\\d{4}\\d+$/', $student) === 1) {
            return substr($student, 0, 5) . '-' . substr($student, 5);
        }

        return $student;
    }

    private function employeeNoForMachineSearch(string $enrollment): string
    {
        return strtoupper(preg_replace('/\s+/', '', str_replace('-', '', trim($enrollment))));
    }

    /**
     * UserInfo/Search with cond fields matching interactive panel (groupIdList, arrangeType, etc.).
     * Does not change existing {@see searchStudentOnMachine} used by bulk sync.
     */
    private function searchStudentOnMachineExtended(string $machineIp, string $employeeNo): array
    {
        $urls = [
            "https://{$machineIp}/ISAPI/AccessControl/UserInfo/Search?format=json",
            "http://{$machineIp}/ISAPI/AccessControl/UserInfo/Search?format=json",
        ];
        $body = [
            'UserInfoSearchCond' => [
                'searchID' => '0',
                'searchResultPosition' => 0,
                'maxResults' => 1,
                'EmployeeNoList' => [
                    ['employeeNo' => $employeeNo],
                ],
                'fuzzySearch' => '',
                'groupIdList' => [1, 2, 3, 4],
                'arrangeType' => 'personal',
            ],
        ];
        $auth = $this->getMachineAuthByIp($machineIp);
        $lastError = '';

        foreach ($urls as $url) {
            try {
                $request = Http::withHeaders(['Content-Type' => 'application/json'])
                    ->withOptions(['verify' => false])
                    ->timeout(20);

                if (!empty($auth['username']) && !empty($auth['password'])) {
                    $request = $request->withDigestAuth($auth['username'], $auth['password']);
                }

                $response = $request->post($url, $body);

                if (!$response->successful()) {
                    $lastError = 'HTTP ' . $response->status();
                    continue;
                }

                $data = $response->json();
                $searchNode = $data['UserInfoSearch'] ?? null;
                $status = strtoupper((string) ($searchNode['responseStatusStrg'] ?? ''));
                if ($status === 'NO MATCH') {
                    return ['success' => true, 'found' => false, 'active' => false];
                }

                $userInfo = $searchNode['UserInfo'][0] ?? null;
                if (empty($userInfo)) {
                    return ['success' => true, 'found' => false, 'active' => false];
                }

                $userType = strtolower((string) ($userInfo['userType'] ?? ''));
                $validEnable = (bool) ($userInfo['Valid']['enable'] ?? false);
                $isActive = $userType !== 'blacklist' && $userType !== 'blackList' && $validEnable;

                return ['success' => true, 'found' => true, 'active' => $isActive];
            } catch (\Throwable $e) {
                $lastError = $e->getMessage();
            }
        }

        return [
            'success' => false,
            'found' => false,
            'active' => false,
            'error' => trim($lastError),
        ];
    }

    private function searchStudentOnMachine(string $machineIp, string $student): array
    {
        $urls = [
            "https://{$machineIp}/ISAPI/AccessControl/UserInfo/Search?format=json",
            "http://{$machineIp}/ISAPI/AccessControl/UserInfo/Search?format=json",
        ];
        $body = [
            'UserInfoSearchCond' => [
                'searchID' => 'sync',
                'searchResultPosition' => 0,
                'maxResults' => 1,
                'EmployeeNoList' => [
                    ['employeeNo' => $student],
                ],
            ],
        ];
        $auth = $this->getMachineAuthByIp($machineIp);
        $lastError = '';

        foreach ($urls as $url) {
            try {
                $request = Http::withHeaders(['Content-Type' => 'application/json'])
                    ->withOptions(['verify' => false])
                    ->timeout(20);

                if (!empty($auth['username']) && !empty($auth['password'])) {
                    $request = $request->withDigestAuth($auth['username'], $auth['password']);
                }

                $response = $request->post($url, $body);

                if (!$response->successful()) {
                    $lastError = 'HTTP ' . $response->status();
                    continue;
                }

                $data = $response->json();
                $searchNode = $data['UserInfoSearch'] ?? null;
                $status = strtoupper((string) ($searchNode['responseStatusStrg'] ?? ''));
                if ($status === 'NO MATCH') {
                    return ['success' => true, 'found' => false, 'active' => false];
                }

                $userInfo = $searchNode['UserInfo'][0] ?? null;
                if (empty($userInfo)) {
                    return ['success' => true, 'found' => false, 'active' => false];
                }

                $userType = strtolower((string) ($userInfo['userType'] ?? ''));
                $validEnable = (bool) ($userInfo['Valid']['enable'] ?? false);
                $isActive = $userType !== 'blacklist' && $userType !== 'blackList' && $validEnable;

                return ['success' => true, 'found' => true, 'active' => $isActive];
            } catch (\Throwable $e) {
                $lastError = $e->getMessage();
            }
        }

        return [
            'success' => false,
            'found' => false,
            'active' => false,
            'error' => trim($lastError),
        ];
    }

    private function updateStudentOnMachine(string $machineIp, array $payload): array
    {
        $urls = [
            "https://{$machineIp}/ISAPI/AccessControl/UserInfo/Modify?format=json",
            "http://{$machineIp}/ISAPI/AccessControl/UserInfo/Modify?format=json",
        ];
        $auth = $this->getMachineAuthByIp($machineIp);
        $lastError = '';

        foreach ($urls as $url) {
            try {
                $request = Http::withHeaders(['Content-Type' => 'application/json'])
                    ->withOptions(['verify' => false])
                    ->timeout(20);

                if (!empty($auth['username']) && !empty($auth['password'])) {
                    $request = $request->withDigestAuth($auth['username'], $auth['password']);
                }

                $response = $request->put($url, $payload);
                if ($response->successful()) {
                    return ['success' => true];
                }

                $lastError = 'HTTP ' . $response->status();
            } catch (\Throwable $e) {
                $lastError = $e->getMessage();
            }
        }

        return ['success' => false, 'error' => trim($lastError)];
    }

    private function getMachineAuthByIp(string $machineIp): array
    {
        static $authCache = [];

        if (isset($authCache[$machineIp])) {
            return $authCache[$machineIp];
        }

        $row = DB::table('ACL_GROUPS_DETAILS')
            ->selectRaw('AGD_MACHINE_USERNAME as machine_username, AGD_MACHINE_PWD as machine_password')
            ->where('AGD_MACHINE_IP', $machineIp)
            ->where('AGD_MACHINE_STATUS', '=', 'E')

            ->first();

        $authCache[$machineIp] = [
            'username' => trim((string) ($row->machine_username ?? $row->AGD_MACHINE_USERNAME ?? $row->agd_machine_username ?? '')),
            'password' => trim((string) ($row->machine_password ?? $row->AGD_MACHINE_PWD ?? $row->agd_machine_pwd ?? '')),
        ];

        return $authCache[$machineIp];
    }

    public function updateStatus(Request $request)
{
    $ids = $request->ids;

    if (empty($ids)) {

        return response()->json([
            'success' => false,
            'message' => 'No records selected.'
        ]);
    }

    $updated = DB::table('SUBSCRIPTIONS')
        ->whereIn('ID', $ids)
        ->update([
            'CURRENT_STATUS' => $request->subscription_status,
            'UPDATED_AT' => now()
        ]);

    return response()->json([
        'success' => true,
        'message' => $updated . ' records updated successfully.'
    ]);
}

}
