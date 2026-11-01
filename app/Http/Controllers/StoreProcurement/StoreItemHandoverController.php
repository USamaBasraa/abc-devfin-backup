<?php

namespace App\Http\Controllers\StoreProcurement;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Grader\Departments;
use App\Models\StoreProcurement\StoreItemRequest;
use App\Services\StoreProcurement\SpPermissionService;
use App\Services\StoreProcurement\StoreItemsSchemaService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class StoreItemHandoverController extends Controller
{
    public function index(Request $request)
    {
        app(StoreItemsSchemaService::class)->ensure();

        $financial_year = $request->get('financial_year');
        $academic_years = AcademicYear::latest('ay_id')->get();
        $inventory = $this->buildInventory($financial_year ? (int)$financial_year : null);

        return view('store_procurement.store_items.inventory', compact('inventory', 'academic_years', 'financial_year'));
    }

    public function requestsList(Request $request)
    {
        app(StoreItemsSchemaService::class)->ensure();

        $user = auth()->user();
        $deptIds = $user ? app(SpPermissionService::class)->getAccessibleDepartmentIds($user) : [];

        $financial_year = $request->get('financial_year');
        $filter_status  = $request->get('filter_status');
        $filter_dept    = $request->get('filter_dept');

        // If dept filter selected, override dept restriction
        $effectiveDepts = $deptIds;
        if ($filter_dept) {
            $effectiveDepts = [(int) $filter_dept];
        }

        $statuses = ['pending', 'issued', 'acknowledged', 'rejected'];
        if ($filter_status) {
            $statuses = [$filter_status];
        }

        $academic_years = AcademicYear::latest('ay_id')->get();
        $departments    = Departments::orderBy('dept_name')->get();

        $requests = $this->loadRequestsByStatus(
            $statuses, 200, $effectiveDepts,
            $financial_year ? (int)$financial_year : null
        );

        return view('store_procurement.store_items.requests', compact(
            'requests', 'academic_years', 'departments',
            'financial_year', 'filter_status', 'filter_dept'
        ));
    }

    public function issuedList()
    {
        app(StoreItemsSchemaService::class)->ensure();

        $inventory = $this->buildInventory();
        $inventoryMap = [];
        foreach ($inventory as $item) {
            $inventoryMap[(int) $item->store_item_id] = $item;
        }
        $warningByItem = $this->loadWarningQtyByStoreItem();

        $requests = $this->loadRequestsByStatus(['pending', 'issued', 'acknowledged', 'rejected'], 500, null);

        foreach ($requests as $req) {
            $id = (int) ($req->store_item_id ?? 0);
            $inv = $inventoryMap[$id] ?? null;
            $req->available_qty = $inv ? (float) ($inv->available_qty ?? 0) : 0;
            $req->received_qty = $inv ? (float) ($inv->received_qty ?? 0) : 0;
            $req->warning_qty = $id > 0
                ? (float) ($warningByItem[$id] ?? ($inv->warning_qty ?? 0))
                : 0;
        }

        return view('store_procurement.store_items.issued', compact('requests', 'inventory'));
    }

    public function updateWarningQty(Request $request, $storeItemId)
    {
        app(StoreItemsSchemaService::class)->ensure();

        $validator = Validator::make($request->all(), [
            'warning_qty' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $storeItemId = (int) $storeItemId;
        if ($storeItemId <= 0) {
            return response()->json(['success' => false, 'message' => 'Invalid store item.'], 422);
        }

        $warningQty = $request->has('warning_qty') && $request->input('warning_qty') !== ''
            ? (float) $request->warning_qty
            : null;

        try {
            $exists = DB::selectOne(
                'SELECT SBI_ID FROM SP_STORE_BUDGET_ITEMS WHERE SBI_ID = ?',
                [$storeItemId]
            );
            if (!$exists) {
                return response()->json(['success' => false, 'message' => 'Store item not found.'], 404);
            }

            DB::table('SP_STORE_BUDGET_ITEMS')
                ->where('SBI_ID', $storeItemId)
                ->update([
                    'WARNING_QTY' => $warningQty,
                    'UPDATED_AT' => Carbon::now(),
                ]);

            return response()->json([
                'success' => true,
                'message' => 'Stock alert quantity saved.',
                'warning_qty' => $warningQty ?? 0,
            ]);
        } catch (\Throwable $e) {
            Log::error('updateWarningQty failed', ['id' => $storeItemId, 'error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to save alert quantity: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function requestForm(Request $request, $storeItemId = null)
    {
        app(StoreItemsSchemaService::class)->ensure();

        $inventory = $this->buildInventory();
        $user = auth()->user();
        $departments = $user ? app(SpPermissionService::class)->getAccessibleDepartments($user) : collect();
        if ($departments->isEmpty()) {
            $departments = Departments::orderBy('dept_name')->get();
        }

        $selectedItem = null;
        if ($storeItemId) {
            foreach ($inventory as $row) {
                if ((int) $row->store_item_id === (int) $storeItemId) {
                    $selectedItem = $row;
                    break;
                }
            }
        }

        $user = auth()->user();

        return view('store_procurement.store_items.request_form', [
            'inventory' => $inventory,
            'departments' => $departments,
            'selectedItem' => $selectedItem,
            'defaultDeptId' => $user->dept_id ?? null,
            'defaultRequestedBy' => $user->username ?? ($user->name ?? ''),
        ]);
    }

    public function storeRequest(Request $request)
    {
        app(StoreItemsSchemaService::class)->ensure();

        $validator = Validator::make($request->all(), [
            'store_item_id' => 'required|integer',
            'requested_qty' => 'required|numeric|min:0.01',
            'request_dept_id' => 'required|integer',
            'requested_by' => 'required|string|max:150',
            'request_remarks' => 'nullable|string|max:2000',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $inventoryMap = [];
        foreach ($this->buildInventory() as $row) {
            $inventoryMap[(int) $row->store_item_id] = $row;
        }

        $storeItemId = (int) $request->store_item_id;
        if (!isset($inventoryMap[$storeItemId])) {
            return redirect()->back()->with('error', 'Selected store item was not found in inventory.')->withInput();
        }

        $item = $inventoryMap[$storeItemId];
        $requestedQty = (float) $request->requested_qty;
        $available = (float) ($item->available_qty ?? 0);

        if ($requestedQty > $available) {
            return redirect()->back()
                ->with('error', "Requested quantity ({$requestedQty}) exceeds available stock ({$available}).")
                ->withInput();
        }

        $dept = Departments::where('dept_id', $request->request_dept_id)->first();
        $user = auth()->user();

        try {
            DB::table('SP_STORE_ITEM_REQUESTS')->insert([
                'STORE_ITEM_ID' => $storeItemId,
                'ITEM_TITLE' => $item->item_title,
                'UNIT' => $item->unit ?? null,
                'REQUESTED_QTY' => $requestedQty,
                'ISSUED_QTY' => 0,
                'REQUEST_DEPT_ID' => (int) $request->request_dept_id,
                'REQUEST_DEPT_NAME' => $dept->dept_name ?? null,
                'REQUESTED_BY' => $request->requested_by,
                'REQUEST_REMARKS' => $request->request_remarks,
                'STATUS' => 'pending',
                'ENTRY_USERNAME' => $user->username ?? null,
                'ENTRY_DEPT_ID' => $user->dept_id ?? null,
                'CREATED_AT' => Carbon::now(),
                'UPDATED_AT' => Carbon::now(),
            ]);
        } catch (\Throwable $e) {
            Log::error('Store item request create failed', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to save request: ' . $e->getMessage())->withInput();
        }

        return redirect()
            ->route('store-items.requests')
            ->with('success', 'Store item request submitted successfully.');
    }

    public function issueRequest(Request $request, $id)
    {
        app(StoreItemsSchemaService::class)->ensure();

        $validator = Validator::make($request->all(), [
            'issued_qty' => 'nullable|numeric|min:0.01',
            'approval_remarks' => 'nullable|string|max:2000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        try {
            $row = DB::table('SP_STORE_ITEM_REQUESTS')->where('SIR_ID', $id)->first();
            if (!$row) {
                return response()->json([
                    'success' => false,
                    'message' => 'Store item request #SIR-' . $id . ' not found.',
                ], 404);
            }

            $rowNorm = $this->normalizeRow($row);
            $status = strtolower(trim((string) ($rowNorm->status ?? '')));
            if (in_array($status, ['issued', 'acknowledged', 'rejected'], true)) {
                return response()->json([
                    'success' => false,
                    'message' => 'This request is already ' . $status . '.',
                ], 422);
            }

            $inventoryMap = [];
            foreach ($this->buildInventory() as $inv) {
                $inventoryMap[(int) $inv->store_item_id] = $inv;
            }

            $storeItemId = (int) ($rowNorm->store_item_id ?? 0);
            $available = isset($inventoryMap[$storeItemId])
                ? (float) $inventoryMap[$storeItemId]->available_qty
                : 0;

            $issuedQty = $request->filled('issued_qty')
                ? (float) $request->issued_qty
                : (float) ($rowNorm->requested_qty ?? 0);

            if ($available <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot issue item. Available stock is 0.00.',
                ], 422);
            }

            if ($issuedQty <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Issued quantity must be greater than zero.',
                ], 422);
            }

            if ($issuedQty > $available) {
                return response()->json([
                    'success' => false,
                    'message' => "Cannot issue {$issuedQty}. Available stock is only {$available}.",
                ], 422);
            }

            $user = auth()->user();
            DB::table('SP_STORE_ITEM_REQUESTS')
                ->where('SIR_ID', $id)
                ->update([
                    'ISSUED_QTY' => $issuedQty,
                    'STATUS' => 'issued',
                    'APPROVAL_REMARKS' => $request->input('approval_remarks'),
                    'ISSUED_BY' => $user->username ?? ($user->name ?? ''),
                    'ISSUED_AT' => Carbon::now(),
                    'UPDATED_AT' => Carbon::now(),
                ]);

            return response()->json([
                'success' => true,
                'message' => 'Item issued / handed over successfully.',
            ]);
        } catch (\Throwable $e) {
            Log::error('issueRequest failed', ['id' => $id, 'error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to issue item: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function rejectRequest(Request $request, $id)
    {
        app(StoreItemsSchemaService::class)->ensure();

        try {
            $row = DB::table('SP_STORE_ITEM_REQUESTS')->where('SIR_ID', $id)->first();
            if (!$row) {
                return response()->json([
                    'success' => false,
                    'message' => 'Store item request not found.',
                ], 404);
            }

            $rowNorm = $this->normalizeRow($row);
            $status = strtolower(trim((string) ($rowNorm->status ?? '')));
            if (in_array($status, ['issued', 'acknowledged', 'rejected'], true)) {
                return response()->json([
                    'success' => false,
                    'message' => 'This request is already ' . $status . '.',
                ], 422);
            }

            DB::table('SP_STORE_ITEM_REQUESTS')
                ->where('SIR_ID', $id)
                ->update([
                    'STATUS' => 'rejected',
                    'APPROVAL_REMARKS' => $request->input('approval_remarks', 'Rejected'),
                    'UPDATED_AT' => Carbon::now(),
                ]);

            return response()->json([
                'success' => true,
                'message' => 'Request rejected.',
            ]);
        } catch (\Throwable $e) {
            Log::error('rejectRequest failed', ['id' => $id, 'error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to reject request: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function acknowledgeRequest(Request $request, $id)
    {
        app(StoreItemsSchemaService::class)->ensure();

        try {
            $row = DB::table('SP_STORE_ITEM_REQUESTS')->where('SIR_ID', $id)->first();
            if (!$row) {
                return response()->json([
                    'success' => false,
                    'message' => 'Store item request not found.',
                ], 404);
            }

            $rowNorm = $this->normalizeRow($row);
            $status = strtolower(trim((string) ($rowNorm->status ?? '')));

            if ($status === 'acknowledged') {
                return response()->json([
                    'success' => false,
                    'message' => 'This request has already been acknowledged.',
                ], 422);
            }

            if ($status !== 'issued') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only issued store item requests can be acknowledged.',
                ], 422);
            }

            $user = auth()->user();
            $accessibleDeptIds = $user ? app(SpPermissionService::class)->getAccessibleDepartmentIds($user) : [];
            $requestDeptId = (int) ($rowNorm->request_dept_id ?? $rowNorm->entry_dept_id ?? 0);
            $isCreator = $user && !empty($rowNorm->entry_username) && (strtolower((string) ($user->username ?? '')) === strtolower((string) $rowNorm->entry_username));
            $isUserDept = $user && !empty($user->dept_id) && ((int) $user->dept_id === $requestDeptId);

            if (!$isCreator && !$isUserDept && !empty($accessibleDeptIds) && $requestDeptId > 0 && !in_array($requestDeptId, array_map('intval', $accessibleDeptIds), true)) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to acknowledge requests for this department.',
                ], 403);
            }

            DB::table('SP_STORE_ITEM_REQUESTS')
                ->where('SIR_ID', $id)
                ->update([
                    'STATUS' => 'acknowledged',
                    'ACKNOWLEDGED_BY' => $user->username ?? ($user->name ?? 'Dept User'),
                    'ACKNOWLEDGED_AT' => Carbon::now(),
                    'ACKNOWLEDGE_REMARKS' => $request->input('remarks', 'Received and acknowledged'),
                    'UPDATED_AT' => Carbon::now(),
                ]);

            return response()->json([
                'success' => true,
                'message' => 'Store items receipt acknowledged successfully.',
            ]);
        } catch (\Throwable $e) {
            Log::error('acknowledgeRequest failed', ['id' => $id, 'error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to acknowledge request: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Aggregate received qty from gate passes vs issued qty from handover requests.
     */
    private function loadWarningQtyByStoreItem(): array
    {
        $map = [];
        try {
            $rows = DB::select('
                SELECT SBI_ID AS store_item_id, NVL(WARNING_QTY, 0) AS warning_qty
                FROM SP_STORE_BUDGET_ITEMS
                WHERE SBI_ID IS NOT NULL
            ');
            foreach ($rows as $row) {
                $normalized = $this->normalizeRow($row);
                $id = (int) ($normalized->store_item_id ?? 0);
                if ($id > 0) {
                    $map[$id] = (float) ($normalized->warning_qty ?? 0);
                }
            }
        } catch (\Throwable $e) {
            Log::warning('loadWarningQtyByStoreItem failed', ['error' => $e->getMessage()]);
        }

        return $map;
    }

    private function buildInventory(?int $ayId = null): array
    {
        $inventory = [];

        try {
            $ayClause = '';
            $bindings = [];
            if ($ayId) {
                $ayClause = 'AND sbh.AY_ID = ?';
                $bindings[] = $ayId;
            }

            $rows = DB::select("
                SELECT
                    sbi.SBI_ID AS store_item_id,
                    NVL(sbi.ITEM_TITLE, po.ITEM_TITLE) AS item_title,
                    sbi.UNIT AS unit,
                    sbh.HEAD_TITLE AS head_title,
                    NVL(SUM(NVL(gp.DELIVERED_QUANTITY, 0)), 0) AS received_qty
                FROM SP_GATE_PASS gp
                INNER JOIN SP_PURCHASE_ORDER po ON po.PO_ID = gp.PO_ID
                INNER JOIN SP_QUOTATION_REQUEST qr
                    ON qr.PO_ID = po.PO_ID
                   AND LOWER(TRIM(NVL(qr.REQUEST_TYPE, ' '))) = 'store_items'
                LEFT JOIN SP_STORE_BUDGET_ITEMS sbi ON sbi.SBI_ID = qr.STORE_ITEM_ID
                LEFT JOIN SP_STORE_BUDGET_HEADS sbh ON sbh.SBH_ID = NVL(qr.STORE_BH_ID, sbi.SBH_ID)
                WHERE qr.STORE_ITEM_ID IS NOT NULL
                $ayClause
                GROUP BY
                    sbi.SBI_ID,
                    NVL(sbi.ITEM_TITLE, po.ITEM_TITLE),
                    sbi.UNIT,
                    sbh.HEAD_TITLE
                ORDER BY NVL(sbi.ITEM_TITLE, po.ITEM_TITLE)
            ", $bindings);
        } catch (\Throwable $e) {
            Log::error('Store inventory build failed', ['error' => $e->getMessage()]);
            return [];
        }

        $issuedByItem = [];
        try {
            $issuedRows = DB::select("
                SELECT
                    STORE_ITEM_ID AS store_item_id,
                    NVL(SUM(NVL(ISSUED_QTY, 0)), 0) AS issued_qty
                FROM SP_STORE_ITEM_REQUESTS
                WHERE LOWER(TRIM(NVL(STATUS, ' '))) IN ('issued', 'acknowledged')
                GROUP BY STORE_ITEM_ID
            ");
            foreach ($issuedRows as $row) {
                $normalized = $this->normalizeRow($row);
                $key = (int) ($normalized->store_item_id ?? 0);
                if ($key > 0) {
                    $issuedByItem[$key] = (float) ($normalized->issued_qty ?? 0);
                }
            }
        } catch (\Throwable $e) {
            // table may not exist yet
        }

        $pendingByItem = [];
        try {
            $pendingRows = DB::select("
                SELECT
                    STORE_ITEM_ID AS store_item_id,
                    NVL(SUM(NVL(REQUESTED_QTY, 0)), 0) AS pending_qty
                FROM SP_STORE_ITEM_REQUESTS
                WHERE LOWER(TRIM(NVL(STATUS, ' '))) = 'pending'
                GROUP BY STORE_ITEM_ID
            ");
            foreach ($pendingRows as $row) {
                $normalized = $this->normalizeRow($row);
                $key = (int) ($normalized->store_item_id ?? 0);
                if ($key > 0) {
                    $pendingByItem[$key] = (float) ($normalized->pending_qty ?? 0);
                }
            }
        } catch (\Throwable $e) {
            // ignore
        }

        foreach ($rows as $row) {
            $item = $this->normalizeRow($row);
            $id = (int) ($item->store_item_id ?? 0);
            if ($id <= 0) {
                continue;
            }

            $received = (float) ($item->received_qty ?? 0);
            $issued = (float) ($issuedByItem[$id] ?? 0);
            $pending = (float) ($pendingByItem[$id] ?? 0);
            $available = max($received - $issued, 0);

            $item->received_qty = $received;
            $item->issued_qty = $issued;
            $item->pending_qty = $pending;
            $item->available_qty = $available;
            $inventory[] = $item;
        }

        $warningByItem = $this->loadWarningQtyByStoreItem();
        foreach ($inventory as $item) {
            $id = (int) ($item->store_item_id ?? 0);
            $item->warning_qty = $id > 0 ? (float) ($warningByItem[$id] ?? 0) : 0;
        }

        return $inventory;
    }

    private function loadRequestsByStatus(array $statuses, int $limit = 100, ?array $deptIds = null, ?int $ayId = null): array
    {
        try {
            $statusList = array_values(array_filter(array_map(function ($s) {
                return strtolower(trim((string) $s));
            }, $statuses)));

            if (empty($statusList)) {
                return [];
            }

            $placeholders = implode(',', array_fill(0, count($statusList), '?'));
            $bindings = $statusList;

            $deptClause = '';
            if ($deptIds !== null) {
                $cleanedDeptIds = array_values(array_filter(array_map('intval', $deptIds)));
                if (empty($cleanedDeptIds)) {
                    return [];
                }
                $deptPlaceholders = implode(',', array_fill(0, count($cleanedDeptIds), '?'));
                $deptClause = " AND NVL(REQUEST_DEPT_ID, ENTRY_DEPT_ID) IN ($deptPlaceholders)";
                $bindings = array_merge($bindings, $cleanedDeptIds);
            }

            // Financial year filter via STORE_ITEM_ID -> SP_STORE_BUDGET_ITEMS -> SP_STORE_BUDGET_HEADS
            $ayClause = '';
            if ($ayId) {
                $ayClause = " AND STORE_ITEM_ID IN (
                    SELECT sbi.SBI_ID FROM SP_STORE_BUDGET_ITEMS sbi
                    INNER JOIN SP_STORE_BUDGET_HEADS sbh ON sbh.SBH_ID = sbi.SBH_ID
                    WHERE sbh.AY_ID = ?
                )";
                $bindings[] = $ayId;
            }

            $bindings[] = $limit;

            $rows = DB::select("
                SELECT *
                FROM (
                    SELECT
                        SIR_ID AS sir_id,
                        STORE_ITEM_ID AS store_item_id,
                        ITEM_TITLE AS item_title,
                        UNIT AS unit,
                        REQUESTED_QTY AS requested_qty,
                        ISSUED_QTY AS issued_qty,
                        REQUEST_DEPT_ID AS request_dept_id,
                        REQUEST_DEPT_NAME AS request_dept_name,
                        REQUESTED_BY AS requested_by,
                        REQUEST_REMARKS AS request_remarks,
                        STATUS AS status,
                        APPROVAL_REMARKS AS approval_remarks,
                        ISSUED_BY AS issued_by,
                        ISSUED_AT AS issued_at,
                        ACKNOWLEDGED_BY AS acknowledged_by,
                        ACKNOWLEDGED_AT AS acknowledged_at,
                        ACKNOWLEDGE_REMARKS AS acknowledge_remarks,
                        CREATED_AT AS created_at
                    FROM SP_STORE_ITEM_REQUESTS
                    WHERE LOWER(TRIM(NVL(STATUS, ' '))) IN ($placeholders)
                      {$deptClause}
                      {$ayClause}
                    ORDER BY SIR_ID DESC
                )
                WHERE ROWNUM <= ?
            ", $bindings);

            $out = [];
            foreach ($rows as $row) {
                $out[] = $this->normalizeRow($row);
            }
            return $out;
        } catch (\Throwable $e) {
            return [];
        }
    }

    private function normalizeRow($row): object
    {
        $item = [];
        foreach ((array) $row as $key => $value) {
            $item[strtolower($key)] = $value;
        }
        return (object) $item;
    }
}
