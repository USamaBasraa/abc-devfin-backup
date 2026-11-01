<?php

namespace App\Http\Controllers\StoreProcurement;

use App\Models\StoreProcurement\SpAsset;
use App\Models\StoreProcurement\VendorCategory;
use App\Services\StoreProcurement\PinnacleApiService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Illuminate\Support\Facades\Cache;

class AssetController extends Controller
{
    public function index()
    {
        $assets = SpAsset::orderBy('asset_id', 'desc')->paginate(20);

        // QC status from SP_GATE_PASS_ITEMS.INSPECTED_FLAG (source of truth)
        $qcByAsset = [];
        try {
            $rows = DB::select("
                SELECT
                    a.ASSET_ID AS asset_id,
                    COUNT(gpi.GPI_ID) AS gpi_count,
                    SUM(CASE
                        WHEN gpi.INSPECTED_FLAG IS NULL
                          OR LOWER(TRIM(gpi.INSPECTED_FLAG)) IN ('pending', 'no', 'n')
                        THEN 1 ELSE 0 END) AS pending_count,
                    SUM(CASE
                        WHEN LOWER(TRIM(gpi.INSPECTED_FLAG)) IN ('passed', 'pass', 'yes', 'y')
                        THEN 1 ELSE 0 END) AS passed_count,
                    SUM(CASE
                        WHEN LOWER(TRIM(gpi.INSPECTED_FLAG)) IN ('failed', 'fail')
                        THEN 1 ELSE 0 END) AS failed_count
                FROM SP_ASSETS a
                INNER JOIN SP_GATE_PASS gp ON gp.PO_ID = a.PO_ID
                INNER JOIN SP_GATE_PASS_ITEMS gpi ON gpi.GP_ID = gp.GP_ID
                GROUP BY a.ASSET_ID
            ");
            foreach ($rows as $row) {
                $aid = (int) ($row->asset_id ?? $row->ASSET_ID ?? 0);
                if (!$aid) {
                    continue;
                }
                $qcByAsset[$aid] = [
                    'pending' => (int) ($row->pending_count ?? $row->PENDING_COUNT ?? 0),
                    'passed' => (int) ($row->passed_count ?? $row->PASSED_COUNT ?? 0),
                    'failed' => (int) ($row->failed_count ?? $row->FAILED_COUNT ?? 0),
                ];
            }
        } catch (\Throwable $e) {
            $qcByAsset = [];
        }

        $itemQcByAsset = [];
        try {
            $itemQcRows = DB::select("
                SELECT
                    ASSET_ID AS asset_id,
                    SUM(CASE
                        WHEN INSPECTION_STATUS IS NULL
                          OR LOWER(TRIM(INSPECTION_STATUS)) IN ('pending', 'no', 'n')
                        THEN 1 ELSE 0 END) AS pending_count,
                    SUM(CASE
                        WHEN LOWER(TRIM(INSPECTION_STATUS)) IN ('passed', 'pass', 'yes', 'y')
                        THEN 1 ELSE 0 END) AS passed_count,
                    SUM(CASE
                        WHEN LOWER(TRIM(INSPECTION_STATUS)) IN ('failed', 'fail')
                        THEN 1 ELSE 0 END) AS failed_count
                FROM SP_ASSET_ITEMS
                GROUP BY ASSET_ID
            ");
            foreach ($itemQcRows as $row) {
                $aid = (int) ($row->asset_id ?? $row->ASSET_ID ?? 0);
                if ($aid) {
                    $itemQcByAsset[$aid] = [
                        'pending' => (int) ($row->pending_count ?? $row->PENDING_COUNT ?? 0),
                        'passed' => (int) ($row->passed_count ?? $row->PASSED_COUNT ?? 0),
                        'failed' => (int) ($row->failed_count ?? $row->FAILED_COUNT ?? 0),
                    ];
                }
            }
        } catch (\Throwable $e) {
            $itemQcByAsset = [];
        }

        $readyByAsset = [];
        try {
            $readyRows = DB::select("
                SELECT ASSET_ID AS asset_id, COUNT(*) AS ready_count
                FROM SP_ASSET_ITEMS
                WHERE UPPER(TRIM(INSPECTION_STATUS)) = 'PASSED'
                  AND (ALLOCATION_STATUS IS NULL
                       OR UPPER(TRIM(ALLOCATION_STATUS)) NOT IN ('ALLOCATED', 'REQUEST SENT'))
                GROUP BY ASSET_ID
            ");
            foreach ($readyRows as $row) {
                $aid = (int) ($row->asset_id ?? $row->ASSET_ID ?? 0);
                if ($aid) {
                    $readyByAsset[$aid] = (int) ($row->ready_count ?? $row->READY_COUNT ?? 0);
                }
            }
        } catch (\Throwable $e) {
            $readyByAsset = [];
        }

        $allocByAsset = [];
        try {
            $allocRows = DB::select("
                SELECT
                    ai.ASSET_ID AS asset_id,
                    TRIM(ee.EMP_F_NAME || ' ' || ee.EMP_L_NAME) AS emp_name,
                    d.DEPT_NAME AS dept_name,
                    ai.ALLOCATION_STATUS AS allocation_status
                FROM SP_ASSET_ITEMS ai
                LEFT JOIN EMP_EMPLOYEES ee ON ee.EMPLOYEE_ID = ai.ASSIGN_TO
                LEFT JOIN DEPARTMENTS d ON d.DEPT_ID = ai.ALLOCATION_DEPARTMENT
                WHERE ai.ASSIGN_TO IS NOT NULL
                   OR ai.ALLOCATION_DEPARTMENT IS NOT NULL
            ");
            foreach ($allocRows as $row) {
                $aid = (int) ($row->asset_id ?? $row->ASSET_ID ?? 0);
                if (!$aid) {
                    continue;
                }
                $name = trim((string) ($row->emp_name ?? $row->EMP_NAME ?? ''));
                $dept = trim((string) ($row->dept_name ?? $row->DEPT_NAME ?? ''));
                $status = trim((string) ($row->allocation_status ?? $row->ALLOCATION_STATUS ?? ''));
                $label = $name !== '' ? $name : 'Unassigned';
                if ($dept !== '') {
                    $label .= ' — ' . $dept;
                }
                if ($status !== '') {
                    $label .= ' (' . $status . ')';
                }
                $allocByAsset[$aid][] = $label;
            }
        } catch (\Throwable $e) {
            $allocByAsset = [];
        }

        foreach ($assets as $asset) {
            $id = (int) $asset->asset_id;
            $qc = $qcByAsset[$id] ?? null;

            if ($qc === null) {
                $qc = $itemQcByAsset[$id] ?? null;
            }

            if ($qc === null) {
                $asset->pending_qc_count = 1;
                $asset->qc_status = 'pending';
            } elseif ($qc['pending'] > 0) {
                $asset->pending_qc_count = $qc['pending'];
                $asset->qc_status = 'pending';
            } elseif ($qc['failed'] > 0 && $qc['passed'] === 0) {
                $asset->pending_qc_count = 0;
                $asset->qc_status = 'failed';
            } elseif ($qc['passed'] > 0) {
                $asset->pending_qc_count = 0;
                $asset->qc_status = 'passed';
            } else {
                $asset->pending_qc_count = 1;
                $asset->qc_status = 'pending';
            }

            $asset->ready_allocate_count = ($asset->qc_status === 'passed')
                ? ($readyByAsset[$id] ?? 0)
                : 0;
            $asset->allocation_lines = $allocByAsset[$id] ?? [];
            $asset->allocated_count = count($asset->allocation_lines);
        }

        return view('store_procurement.assets.index', compact('assets'));
    }

    public function show2($id)
    {
        $select_list = "prefix, employee_id as id, emp_f_name || ' ' || emp_l_name as full_name, emp_dept_id, dept_name, school_code";
        $sql = "SELECT $select_list FROM EMP_EMPLOYEES
                LEFT JOIN schools ON school_id = emp_school_id
                LEFT JOIN departments ON dept_id = emp_dept_id
                WHERE emp_status = 'E'
                ORDER BY employee_id DESC";
        $users = DB::select($sql);
        $departments = DB::table('departments')->select('dept_id', 'dept_name')->get();
        $asset = SpAsset::with('items')->findOrFail($id);
        return view('store_procurement.assets.show', compact('asset', 'departments', 'users'));
    }

    public function show($id)
    {
        $select_list = "prefix, employee_id as id, emp_f_name || ' ' || emp_l_name as full_name, emp_dept_id, dept_name, school_code";
        $sql = "SELECT $select_list FROM EMP_EMPLOYEES
                LEFT JOIN schools ON school_id = emp_school_id
                LEFT JOIN departments ON dept_id = emp_dept_id
                WHERE emp_status = 'E'
                ORDER BY employee_id DESC";
        $users = DB::select($sql);
        $departments = DB::table('departments')->select('dept_id', 'dept_name')->get();

        $assetRow = DB::table('SP_ASSETS')->where('ASSET_ID', $id)->first();
        if (!$assetRow) {
            return response()->json([
                'success' => false,
                'message' => 'Asset not found.',
                'asset' => [],
                'items' => [],
                'departments' => $departments,
                'users' => $users,
                'resbuildingsQL' => [],
            ], 404);
        }

        $header = $this->normalizeRow($assetRow);
        $items = $this->assetItemsForQc((int) $id, $header);

        $assetPayload = [];
        if (empty($items)) {
            $assetPayload[] = $header;
        } else {
            foreach ($items as $item) {
                $assetPayload[] = array_merge($header, $item);
            }
        }

        try {
            $resbuildingsQL = $this->fixedAssetLocations();
        } catch (\Throwable $e) {
            $resbuildingsQL = [];
        }
        $rfq = $this->rfqForAsset($header);

        return response()->json([
            'asset' => $assetPayload,
            'items' => $items,
            'allocation_history' => $this->allocationHistory((int) $id),
            'rfq' => $rfq,
            'request_department_id' => $rfq['request_department_id'] ?? null,
            'departments' => $departments,
            'users' => $users,
            'resbuildingsQL' => $resbuildingsQL,
        ]);
    }

    /**
     * Serials for the QC dropdown.
     * Prefer SP_ASSET_ITEMS. If none exist, create them from SP_GATE_PASS_ITEMS.SERIAL_NUMBER.
     */
    private function assetItemsForQc(int $assetId, array $header): array
    {
        $items = $this->fetchAssetItems($assetId);
        if (!empty($items)) {
            return $items;
        }

        $poId = $header['po_id'] ?? null;
        if ($poId !== null && $poId !== '') {
        try {
            $gpRows = DB::select("
                SELECT gpi.SERIAL_NUMBER AS serial_number
                FROM SP_GATE_PASS_ITEMS gpi
                INNER JOIN SP_GATE_PASS gp ON gp.GP_ID = gpi.GP_ID
                WHERE gp.PO_ID = ?
            ", [$poId]);
        } catch (\Throwable $e) {
            return [];
        }

        $serials = [];
        foreach ($gpRows as $row) {
            $csv = (string) ($this->normalizeRow($row)['serial_number'] ?? '');
            foreach (array_filter(array_map('trim', explode(',', $csv))) as $serial) {
                $serials[$serial] = $serial;
            }
        }

        if (empty($serials)) {
            $serials[''] = '';
        }

        foreach ($serials as $serial) {
            try {
                DB::table('SP_ASSET_ITEMS')->insert([
                    'ASSET_ID' => $assetId,
                    'SERIAL_NUMBER' => $serial !== '' ? $serial : null,
                    'ALLOCATION_STATUS' => 'Available',
                    'INSPECTION_STATUS' => 'Pending',
                    'SCRAP_STATUS' => 'Not Scrapped',
                    'STATUS' => 'Active',
                ]);
            } catch (\Throwable $e) {
                // skip row if insert fails
            }
        }

        $items = $this->fetchAssetItems($assetId);
            if (!empty($items)) {
                return $items;
            }
        }

        $this->ensureManualAssetItems($assetId, $header, '', 0);

        return $this->fetchAssetItems($assetId);
    }

    private function ensureManualAssetItems(int $assetId, array $header, string $serialInput, int $quantity): void
    {
        if ($assetId <= 0) {
            return;
        }

        try {
            $existing = DB::select(
                "SELECT COUNT(*) AS item_count FROM SP_ASSET_ITEMS WHERE ASSET_ID = ?",
                [$assetId]
            );
            $countRow = $this->normalizeRow($existing[0] ?? []);
            if ((int) ($countRow['item_count'] ?? 0) > 0) {
                return;
            }
        } catch (\Throwable $e) {
            return;
        }

        $serials = array_values(array_filter(array_map('trim', preg_split('/[\r\n,]+/', $serialInput) ?: [])));
        $qty = $quantity;
        if ($qty <= 0) {
            $qty = (int) ($header['received_quantity'] ?? 0);
        }
        if ($qty <= 0) {
            $qty = (int) ($header['order_quantity'] ?? 0);
        }
        $total = max(count($serials), $qty, 1);
        $assetCode = trim((string) ($header['asset_code'] ?? ''));

        for ($i = 0; $i < $total; $i++) {
            $serial = $serials[$i] ?? '';
            if ($serial === '') {
                $serial = $assetCode !== ''
                    ? ($total === 1 ? $assetCode : $assetCode . '-' . ($i + 1))
                    : null;
            }
            try {
                DB::table('SP_ASSET_ITEMS')->insert([
                    'ASSET_ID' => $assetId,
                    'SERIAL_NUMBER' => $serial,
                    'ALLOCATION_STATUS' => 'Available',
                    'INSPECTION_STATUS' => 'Pending',
                    'SCRAP_STATUS' => 'Not Scrapped',
                    'STATUS' => 'Active',
                ]);
            } catch (\Throwable $e) {
                // skip a row that cannot be inserted
            }
        }
    }

    private function fetchAssetItems(int $assetId): array
    {
        try {
            $rows = DB::select("
                SELECT
                    ai.ASSET_ITEM_ID,
                    ai.SERIAL_NUMBER,
                    ai.INSPECTION_STATUS,
                    ai.UNIQUE_ASSET_CODE,
                    ai.ALLOCATION_STATUS,
                    ai.ALLOCATION_LOCATION,
                    TRIM(ee.EMP_F_NAME || ' ' || ee.EMP_L_NAME) AS employee_name
                FROM SP_ASSET_ITEMS ai
                LEFT JOIN EMP_EMPLOYEES ee ON ee.EMPLOYEE_ID = ai.ASSIGN_TO
                WHERE ai.ASSET_ID = ?
                ORDER BY ai.ASSET_ITEM_ID
            ", [$assetId]);
        } catch (\Throwable $e) {
            return [];
        }

        $locationMap = $this->locationNameMap();
        $items = [];
        foreach ($rows as $row) {
            $item = $this->normalizeRow($row);
            $locId = trim((string) ($item['allocation_location'] ?? ''));
            $item['location_name'] = $locationMap[$locId] ?? ($locId !== '' ? $locId : '');
            $items[] = $item;
        }

        return $items;
    }

    private function allocationHistory(int $assetId): array
    {
        try {
            $rows = DB::select("
                SELECT
                    h.SERIAL_NUMBER AS serial_number,
                    h.ALLOCATION_STATUS AS allocation_status,
                    h.ALLOCATED_LOCATION AS allocation_location,
                    h.REMARKS AS remarks,
                    h.CREATED_AT AS created_at,
                    TRIM(ee.EMP_F_NAME || ' ' || ee.EMP_L_NAME) AS employee_name,
                    d.DEPT_NAME AS dept_name
                FROM SP_ASSET_ALLOCATION_HISTORY h
                INNER JOIN SP_ASSET_ITEMS ai ON ai.ASSET_ITEM_ID = h.ASSET_ITEM_ID
                LEFT JOIN EMP_EMPLOYEES ee ON ee.EMPLOYEE_ID = h.ALLOCATED_TO_USER_ID
                LEFT JOIN DEPARTMENTS d ON d.DEPT_ID = h.DEPARTMENT_ID
                WHERE ai.ASSET_ID = ?
                ORDER BY h.CREATED_AT DESC
            ", [$assetId]);
        } catch (\Throwable $e) {
            return [];
        }

        $locationMap = $this->locationNameMap();
        $history = [];
        foreach ($rows as $row) {
            $entry = $this->normalizeRow($row);
            $locId = trim((string) ($entry['allocation_location'] ?? ''));
            $entry['location_name'] = $locationMap[$locId] ?? ($locId !== '' ? $locId : '');
            $history[] = $entry;
        }

        return $history;
    }

    private function locationNameMap(): array
    {
        $map = [];
        foreach ($this->fixedAssetLocations() as $location) {
            $id = trim((string) ($location['building_id'] ?? ''));
            $name = trim((string) ($location['building_name'] ?? ''));
            if ($id !== '') {
                $map[$id] = $name !== '' ? $name : $id;
            }
        }
        return $map;
    }

    private function fixedAssetLocations(): array
    {
        $cacheKey = 'store_procurement.fixed_asset_locations';
        $cached = Cache::get($cacheKey);
        if (is_array($cached) && !empty($cached)) {
            return $cached;
        }

        try {
            $response = app(PinnacleApiService::class)->fetch('fixed_asset_locations');
        } catch (\Throwable $e) {
            return [];
        }

        $records = [];
        if (isset($response['data']) && is_array($response['data'])) {
            $records = $response['data'];
        } elseif (isset($response['Data']) && is_array($response['Data'])) {
            $records = $response['Data'];
        } elseif (is_array($response) && $this->isListArray($response)) {
            $records = $response;
        }

        $locations = [];
        foreach ($records as $item) {
            if (!is_array($item)) {
                continue;
            }
            $id = trim((string) ($item['ID'] ?? $item['Id'] ?? $item['id'] ?? ''));
            $title = trim((string) ($item['Title'] ?? $item['title'] ?? ''));
            $building = trim((string) ($item['Building_Title'] ?? $item['building_title'] ?? ''));
            $floor = trim((string) ($item['FloorNo'] ?? $item['floor_no'] ?? ''));
            if ($id === '' && $title === '') {
                continue;
            }
            $labelParts = array_values(array_filter([$title, $building, $floor]));
            $locations[] = [
                'building_id' => $id !== '' ? $id : $title,
                'building_name' => !empty($labelParts) ? implode(' — ', $labelParts) : $id,
            ];
        }

        usort($locations, function ($a, $b) {
            return strcasecmp($a['building_name'], $b['building_name']);
        });

        if (!empty($locations)) {
            Cache::put($cacheKey, $locations, 600);
        }

        return $locations;
    }

    private function isListArray(array $array): bool
    {
        if (function_exists('array_is_list')) {
            return array_is_list($array);
        }
        if ($array === []) {
            return true;
        }
        return array_keys($array) === range(0, count($array) - 1);
    }

    private function rfqForAsset(array $header): array
    {
        $rfq = [
            'item_title' => $header['item_title'] ?? $header['name'] ?? '',
            'brand' => $header['brand'] ?? '',
            'model' => $header['model'] ?? '',
            'specifications' => $header['item_remarks'] ?? '',
            'remarks' => '',
            'request_department_id' => null,
        ];

        $qrId = $header['qr_id'] ?? null;
        if (!$qrId) {
            return $rfq;
        }

        try {
            $rows = DB::select("
                SELECT
                    qr.ITEM_NAME AS item_name,
                    qr.BRAND AS brand,
                    qr.MODEL AS model,
                    qr.REMARKS AS remarks,
                    qr.SPECIFICATIONS_PRNT_DEPT AS specifications,
                    qr.REQUEST_DEPARTMENT_ID AS request_department_id,
                    bi.ITEM_TITLE AS item_title,
                    bi.BRAND AS item_brand,
                    bi.MODEL AS item_model,
                    bi.REMARKS AS item_remarks
                FROM SP_QUOTATION_REQUEST qr
                LEFT JOIN SP_BUDGET_ITEMS bi ON bi.ITEM_ID = qr.ITEM_ID
                WHERE qr.QR_ID = ?
            ", [$qrId]);

            if (!empty($rows)) {
                $row = $this->normalizeRow($rows[0]);
                $rfq['item_title'] = $row['item_title'] ?: ($row['item_name'] ?: $rfq['item_title']);
                $rfq['brand'] = $row['item_brand'] ?: ($row['brand'] ?: $rfq['brand']);
                $rfq['model'] = $row['item_model'] ?: ($row['model'] ?: $rfq['model']);
                $rfq['specifications'] = $row['specifications'] ?: ($row['item_remarks'] ?: $rfq['specifications']);
                $rfq['remarks'] = $row['remarks'] ?? '';
                $rfq['request_department_id'] = $row['request_department_id'] ?? null;
            }
        } catch (\Throwable $e) {
            // RFQ box falls back to asset fields
        }

        return $rfq;
    }

    private function normalizeRow($row): array
    {
        if (!$row) {
            return [];
        }
        $out = [];
        foreach ((array) $row as $key => $value) {
            $out[strtolower($key)] = $value;
        }
        return $out;
    }

    public function searchEmployees(Request $request)
    {
        $term = trim((string) $request->get('q', ''));
        if (mb_strlen($term) < 2) {
            return response()->json([]);
        }

        $like = '%' . mb_strtoupper($term) . '%';
        $deptId = trim((string) $request->get('dept_id', ''));
        $deptSql = '';
        $bindings = [$like, $like, $like];
        if ($deptId !== '') {
            $deptSql = ' AND emp_dept_id = ?';
            $bindings[] = $deptId;
        }
        try {
            $rows = DB::select("
                SELECT * FROM (
                    SELECT
                        employee_id AS id,
                        TRIM(prefix || ' ' || emp_f_name || ' ' || emp_l_name) AS full_name,
                        emp_ad_username AS username,
                        dept_name
                    FROM EMP_EMPLOYEES
                    LEFT JOIN DEPARTMENTS ON dept_id = emp_dept_id
                    WHERE emp_status = 'E'
                      AND (
                        UPPER(emp_f_name || ' ' || emp_l_name) LIKE ?
                        OR UPPER(NVL(emp_ad_username, ' ')) LIKE ?
                        OR TO_CHAR(employee_id) LIKE ?
                      )
                      {$deptSql}
                    ORDER BY emp_f_name
                ) WHERE ROWNUM <= 15
            ", $bindings);
        } catch (\Throwable $e) {
            return response()->json([]);
        }

        $out = [];
        foreach ($rows as $row) {
            $out[] = $this->normalizeRow($row);
        }

        return response()->json($out);
    }

    public function create()
    {
        $categories = VendorCategory::all();

        return view('store_procurement.assets.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'asset_code' => 'required|max:50',
            'item_title' => 'required|max:1000',
            'head_title' => 'required|max:1000',
            'description' => 'nullable|string|max:2000',
            'serial_number' => 'nullable|string|max:2000',
            'order_quantity' => 'nullable|numeric',
            'received_quantity' => 'nullable|numeric',
            'unit_price' => 'nullable|numeric',
            'total_price' => 'nullable|numeric',
        ]);

        $blankToNull = function ($value) {
            return ($value === null || $value === '') ? null : $value;
        };

        $totalPrice = $blankToNull($request->total_price);
        if (is_numeric($request->unit_price) && is_numeric($request->order_quantity)) {
            $totalPrice = round((float) $request->unit_price * (float) $request->order_quantity, 2);
        }

        $duplicate = DB::select(
            "SELECT ASSET_ID FROM SP_ASSETS WHERE UPPER(TRIM(ASSET_CODE)) = UPPER(TRIM(?)) AND ROWNUM = 1",
            [$request->asset_code]
        );
        if (!empty($duplicate)) {
            $message = 'This asset code is already used. Please enter a different asset code.';
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $message], 422);
            }
            return redirect()->back()->withInput()->with('error', $message);
        }

        try {
            $asset = SpAsset::create([
                'asset_code' => $request->asset_code,
                'name' => $request->item_title,
                'item_title' => $request->item_title,
                'category' => $blankToNull($request->head_title),
                'brand' => $blankToNull($request->brand),
                'model' => $blankToNull($request->model),
                'order_quantity' => $blankToNull($request->order_quantity),
                'received_quantity' => $blankToNull($request->received_quantity),
                'received_status' => $request->received_status ?: 'Pending',
                'inspection_status' => $request->inspection_status ?: 'Pending',
                'warranty_period' => $blankToNull($request->warranty_period),
                'currency' => $blankToNull($request->currency),
                'total_price' => $totalPrice,
                'item_remarks' => $blankToNull($request->description ?: $request->item_remarks),
                'status' => 'Active',
            ]);

            $assetId = (int) ($asset->asset_id ?? 0);
            if ($assetId <= 0) {
                $found = DB::select(
                    "SELECT ASSET_ID AS asset_id FROM SP_ASSETS WHERE ASSET_CODE = ? ORDER BY ASSET_ID DESC",
                    [$request->asset_code]
                );
                $assetId = (int) ($this->normalizeRow($found[0] ?? [])['asset_id'] ?? 0);
            }
            $qty = (int) ($request->received_quantity ?: $request->order_quantity ?: 1);
            $this->ensureManualAssetItems($assetId, [
                'asset_code' => $request->asset_code,
                'received_quantity' => $request->received_quantity,
                'order_quantity' => $request->order_quantity,
            ], (string) $request->input('serial_number', ''), $qty);
        } catch (\Throwable $e) {
            $message = $this->friendlyAssetSaveError($e->getMessage());
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $message], 422);
            }
            return redirect()->back()->withInput()->with('error', $message);
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Asset saved successfully!',
                'asset_id' => $asset->asset_id ?? null,
            ]);
        }

        return redirect()->back()->with('success', 'Asset saved successfully!');
    }

    private function friendlyAssetSaveError(string $raw): string
    {
        $text = strtoupper($raw);

        if (strpos($text, 'ORA-00001') !== false) {
            return 'This asset code is already used. Please enter a different asset code.';
        }
        if (strpos($text, 'ORA-01400') !== false || strpos($text, 'ORA-01407') !== false) {
            return 'A required field is missing. Please fill Asset Code, Budget Head Title, and Item Title.';
        }
        if (strpos($text, 'ORA-12899') !== false || strpos($text, 'ORA-01401') !== false) {
            return 'One of the values is too long. Please shorten it and try again.';
        }
        if (strpos($text, 'ORA-01722') !== false || strpos($text, 'ORA-01858') !== false || strpos($text, 'ORA-01861') !== false) {
            return 'A number or date is not valid. Please check quantity, price, and dates.';
        }

        return 'Asset could not be saved. Please check the form and try again.';
    }

    public function manualList()
    {
        $rows = DB::select("
            SELECT ASSET_ID, ASSET_CODE, ITEM_TITLE, NAME, ITEM_REMARKS,
                   ORDER_QUANTITY, RECEIVED_QUANTITY, RECEIVED_STATUS,
                   INSPECTION_STATUS, TOTAL_PRICE, CURRENCY
            FROM SP_ASSETS
            ORDER BY ASSET_ID DESC
        ");

        $assets = [];
        foreach ($rows as $row) {
            $item = [];
            foreach ((array) $row as $key => $value) {
                $item[strtolower($key)] = $value;
            }
            $assets[] = [
                'asset_code' => $item['asset_code'] ?? '',
                'item_title' => $item['item_title'] ?: ($item['name'] ?? ''),
                'description' => $item['item_remarks'] ?? '',
                'order_quantity' => $item['order_quantity'] ?? '',
                'received_quantity' => $item['received_quantity'] ?? '',
                'received_status' => $item['received_status'] ?? '',
                'inspection_status' => $item['inspection_status'] ?? '',
                'total_price' => $item['total_price'] ?? '',
                'currency' => $item['currency'] ?? '',
            ];
        }

        return response()->json(['success' => true, 'assets' => $assets]);
    }
}
