<?php

namespace App\Http\Controllers\StoreProcurement;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\StoreProcurement\QuotationRequest;
use App\Models\StoreProcurement\StoreBudgetHead;
use App\Models\StoreProcurement\StoreBudgetItem;
use App\Models\StoreProcurement\VendorCategory;
use App\Services\StoreProcurement\PinnacleApiService;
use App\Services\StoreProcurement\SpPermissionService;
use App\Services\StoreProcurement\StoreItemsSchemaService;
use App\Support\Oracle\ResolvesSynonymObjects;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StoreItemsBudgetController extends Controller
{
    use ResolvesSynonymObjects;

    private const REQUEST_TYPE = 'store_items';
    private const ITEMS_API_ENDPOINT = 'store_items';
    private const FALLBACK_ITEMS_API_ENDPOINT = 'capital_expenditure_items';
    private const CACHE_TTL_SECONDS = 600;

    public function index()
    {
        $this->ensureSchema();

        $departments = app(SpPermissionService::class)->getAccessibleDepartments(auth()->user());
        $academic_years = AcademicYear::latest('ay_id')->get();
        $heads = StoreBudgetHead::with(['items.quotationRequests', 'department'])
            ->where('dept_id', auth()->user()->dept_id)
            ->orderBy('sbh_id', 'desc')
            ->take(25)
            ->get();

        return view('store_procurement.store_items_budget.create', compact('departments', 'academic_years', 'heads'));
    }

    public function search(Request $request, PinnacleApiService $pinnacleApiService)
    {
        $q = trim((string) $request->get('q', ''));
        $items = $this->storeItemOptions($pinnacleApiService);

        if ($q !== '') {
            $needle = mb_strtolower($q);
            $items = array_values(array_filter($items, function (array $item) use ($needle) {
                foreach (['item_id', 'item_title', 'item_type', 'unit', 'app_unit', 'fin_acc01_title'] as $field) {
                    if (mb_stripos((string) ($item[$field] ?? ''), $needle) !== false) {
                        return true;
                    }
                }
                return false;
            }));
        }

        $limit = $q === '' ? 500 : 50;
        return response()->json(array_slice($items, 0, $limit));
    }

    public function store(Request $request)
    {
        $this->ensureSchema();

        $validated = $request->validate([
            'dept_id' => 'required|exists:DEPARTMENTS,DEPT_ID',
            'year_id' => 'required|integer|exists:academic_years,ay_id',
            'remarks' => 'nullable|string|max:2000',
            'items' => 'required|array|min:1',
            'items.*.pinnacle_item_id' => 'nullable|string|max:50',
            'items.*.item_title' => 'required|string|max:1000',
            'items.*.item_type' => 'nullable|string|max:100',
            'items.*.unit' => 'nullable|string|max:50',
            'items.*.app_unit' => 'nullable|string|max:50',
            'items.*.fin_acc01_id' => 'nullable|string|max:50',
            'items.*.fin_acc01_title' => 'nullable|string|max:500',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.rate' => 'required|numeric|min:0',
            'items.*.budget_type' => 'required|in:budgeted,unbudgeted,reappropriated',
            'items.*.purchase_mode' => 'required|in:purchase,rent',
            'items.*.remarks' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            $user = auth()->user();
            $head = StoreBudgetHead::create([
                'dept_id' => (int) $user->dept_id,
                'ay_id' => $validated['year_id'],
                'head_title' => 'Store Items',
                'remarks' => $validated['remarks'] ?? null,
                'entry_username' => $user->username ?? 'system',
                'entry_dept_id' => $user->dept_id,
            ]);

            foreach ($validated['items'] as $item) {
                StoreBudgetItem::create([
                    'sbh_id' => $head->sbh_id,
                    'pinnacle_item_id' => $item['pinnacle_item_id'] ?? null,
                    'item_title' => $item['item_title'],
                    'item_type' => $item['item_type'] ?? null,
                    'unit' => $item['unit'] ?? null,
                    'app_unit' => $item['app_unit'] ?? null,
                    'fin_acc01_id' => $item['fin_acc01_id'] ?? null,
                    'fin_acc01_title' => $item['fin_acc01_title'] ?? null,
                    'quantity' => $item['quantity'],
                    'rate' => $item['rate'],
                    'budget_type' => $item['budget_type'],
                    'purchase_mode' => $item['purchase_mode'] ?? 'purchase',
                    'remarks' => $item['remarks'] ?? null,
                    'ay_id' => $validated['year_id'],
                    'entry_dept_id' => $user->dept_id,
                    'entry_username' => $user->username ?? 'system',
                ]);
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Store items budget created successfully.']);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 422);
        }
    }

    public function requestQuotation(Request $request, $sbhId)
    {
        $this->ensureSchema();

        $itemIds = $request->items ?? [];
        $quantities = $request->quantities ?? [];

        if (empty($itemIds)) {
            return response()->json(['success' => false, 'message' => 'No store items selected.'], 400);
        }

        $user = auth()->user();
        $selectedItems = StoreBudgetItem::where('sbh_id', $sbhId)
            ->whereIn('sbi_id', $itemIds)
            ->get()
            ->keyBy('sbi_id');

        $unmappedItems = [];
        foreach ($itemIds as $itemId) {
            $item = $selectedItems->get((int) $itemId);
            if (!$item) {
                continue;
            }

            $category = $this->categoryForStoreItem($item);
            if (!$category || empty($category->category_id)) {
                $unmappedItems[] = sprintf(
                    '%s (FinAcc01_ID: %s, FinAcc01_Title: %s)',
                    (string) ($item->item_title ?? ('Item #' . $item->sbi_id)),
                    (string) ($item->fin_acc01_id ?? '-'),
                    (string) ($item->fin_acc01_title ?? '-')
                );
            }
        }

        if (!empty($unmappedItems)) {
            return response()->json([
                'success' => false,
                'message' => 'Category mapping missing for selected item(s): ' . implode(' | ', $unmappedItems) . '. Please map these in Vendor Category before sending request.'
            ], 422);
        }

        DB::beginTransaction();
        try {
            foreach ($itemIds as $itemId) {
                $item = $selectedItems->get((int) $itemId);
                if (!$item) {
                    continue;
                }

                $requestedQty = $quantities[$itemId] ?? $item->quantity;
                $category = $this->categoryForStoreItem($item);
                if (!$category || empty($category->category_id)) {
                    throw new \RuntimeException('Category mapping not found for item: ' . ($item->item_title ?? ('Item #' . $item->sbi_id)));
                }
                $parentDeptId = $category->parent_department_id ?? null;
                $parentApprovalRequired = (string) ($category->q_parent_dept_required ?? '0');

                $data = [
                    'request_type' => self::REQUEST_TYPE,
                    'store_bh_id' => $sbhId,
                    'store_item_id' => $item->sbi_id,
                    'item_id' => null,
                    'bh_id' => null,
                    'requested_quantity' => $requestedQty,
                    'budget_quantity' => $item->quantity,
                    'current_version' => 1,
                    'is_revised' => 0,
                    'cat_id' => $category->category_id,
                    'request_department_id' => $user->dept_id,
                    'request_school_id' => $user->school_id,
                    'parent_department_id' => $parentDeptId,
                    'status' => 'pending',
                    'entry_user_id' => auth()->id(),
                    'department_head_approval' => ($parentApprovalRequired === '0' || !$parentDeptId || $parentDeptId == $user->dept_id) ? 'approved' : null,
                    'visibility_to_vendors' => ($parentApprovalRequired === '0' || !$parentDeptId || $parentDeptId == $user->dept_id) ? 'yes' : null,
                    'allow_decline_remarks' => ($parentApprovalRequired === '0' || !$parentDeptId) ? 'Systematically Approved' : null,
                ];

                $existing = QuotationRequest::where('request_type', self::REQUEST_TYPE)
                    ->where('store_item_id', $item->sbi_id)
                    ->where('store_bh_id', $sbhId)
                    ->first();

                if ($existing) {
                    $newVersion = ($existing->current_version ?? 1) + 1;
                    DB::table('SP_QUOTATION_REQUEST_HISTORY')
                        ->where('QR_ID', $existing->qr_id)
                        ->where('IS_CURRENT', 1)
                        ->update(['IS_CURRENT' => 0]);
                    $existing->update([
                        'requested_quantity' => $requestedQty,
                        'budget_quantity' => $item->quantity,
                        'current_version' => $newVersion,
                        'is_revised' => 1,
                        'status' => 'pending',
                        'updated_at' => now(),
                    ]);
                    $qr = $existing;
                    $version = $newVersion;
                } else {
                    $qr = QuotationRequest::create($data);
                    $version = 1;
                }

                DB::table('SP_QUOTATION_REQUEST_HISTORY')->insert([
                    'QR_ID' => $qr->qr_id,
                    'VERSION_NUMBER' => $version,
                    'IS_CURRENT' => 1,
                    'BUDGET_QUANTITY' => $item->quantity,
                    'REQUESTED_QUANTITY' => $requestedQty,
                    'ITEM_ID' => null,
                    'BH_ID' => null,
                    'CAT_ID' => $category->category_id ?? null,
                    'REQUEST_TYPE' => self::REQUEST_TYPE,
                    'STORE_BH_ID' => $sbhId,
                    'STORE_ITEM_ID' => $item->sbi_id,
                    'REQUEST_DEPARTMENT_ID' => $user->dept_id,
                    'REQUEST_SCHOOL_ID' => $user->school_id,
                    'PARENT_DEPARTMENT_ID' => $parentDeptId,
                    'ENTRY_USER_ID' => auth()->id(),
                    'APPROVAL_STATUS' => 'pending',
                    'CREATED_AT' => now(),
                    'UPDATED_AT' => now(),
                ]);
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => count($itemIds) . ' store item(s) sent for quotation request.']);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Request failed: ' . $e->getMessage()], 500);
        }
    }

    private function categoryForStoreItem(StoreBudgetItem $item)
    {
        if ($item->fin_acc01_id) {
            $category = VendorCategory::where('pinnacle_account_id', $item->fin_acc01_id)->first();
            if ($category) {
                return $category;
            }
        }

        if ($item->fin_acc01_title) {
            return VendorCategory::whereRaw('LOWER(category_name) = ?', [mb_strtolower($item->fin_acc01_title)])->first();
        }

        return null;
    }

    private function storeItemOptions(PinnacleApiService $pinnacleApiService): array
    {
        $cacheKey = 'store_procurement.store_items_budget.options.' . self::ITEMS_API_ENDPOINT;
        $cached = Cache::get($cacheKey);
        if (is_array($cached) && !empty($cached)) {
            $hasUsableTitles = false;
            foreach ($cached as $cachedItem) {
                if (!empty($cachedItem['item_title']) && $cachedItem['item_title'] !== '-') {
                    $hasUsableTitles = true;
                    break;
                }
            }

            if (!$hasUsableTitles) {
                Cache::forget($cacheKey);
            } else {
                return $cached;
            }
        }

        $cached = Cache::get($cacheKey);
        if (is_array($cached) && !empty($cached)) {
            return $cached;
        }

        $response = $pinnacleApiService->fetch(self::ITEMS_API_ENDPOINT);
        $records = $this->extractApiRecords($response);
        if (empty($records)) {
            $fallbackResponse = $pinnacleApiService->fetch(self::FALLBACK_ITEMS_API_ENDPOINT);
            $records = $this->extractApiRecords($fallbackResponse);
        }
        $mapped = array_values(array_map(function ($item) {
            $itemId = $this->clean($this->firstValue($item, ['ID', 'Id', 'id', 'ItemID', 'item_id', 'ItemId']));
            $itemTitle = $this->clean($this->firstValue($item, ['Title', 'title', 'ItemTitle', 'item_title', 'Name', 'name', 'Description', 'description']));
            if ($itemTitle === null) {
                $itemTitle = $itemId;
            }

            return [
                'item_id' => $itemId,
                'item_title' => $itemTitle,
                'item_type' => $this->clean($this->firstValue($item, ['ItemType', 'item_type', 'Type', 'type'])),
                'unit' => $this->clean($this->firstValue($item, ['Unit', 'unit', 'UOM', 'uom'])),
                'app_unit' => $this->clean($this->firstValue($item, ['AppUnit', 'app_unit', 'ApprovedUnit', 'approved_unit'])),
                'fin_acc01_id' => $this->clean($this->firstValue($item, ['FinAcc01_ID', 'fin_acc01_id', 'FinAccID', 'fin_acc_id'])),
                'fin_acc01_title' => $this->clean($this->firstValue($item, ['FinAcc01_Title', 'fin_acc01_title', 'FinAccTitle', 'fin_acc_title', 'Category', 'category'])),
            ];
        }, $records));
        $mapped = array_values(array_filter($mapped, function (array $item) {
            return !empty($item['item_id']) || !empty($item['item_title']);
        }));

        if (!empty($mapped)) {
            Cache::put($cacheKey, $mapped, self::CACHE_TTL_SECONDS);
        }

        return $mapped;
    }

    private function extractApiRecords(array $response): array
    {
        if (isset($response['data']) && is_array($response['data'])) {
            return $response['data'];
        }

        if (isset($response['Data']) && is_array($response['Data'])) {
            return $response['Data'];
        }

        if (isset($response['result']) && is_array($response['result'])) {
            return $this->extractApiRecords($response['result']);
        }

        if (array_is_list($response)) {
            return $response;
        }

        return [];
    }

    private function firstValue(array $item, array $keys)
    {
        foreach ($keys as $key) {
            if (array_key_exists($key, $item) && $item[$key] !== null) {
                return $item[$key];
            }
        }

        return null;
    }

    private function clean($value): ?string
    {
        if ($value === null) {
            return null;
        }
        $value = trim((string) $value);
        return $value === '' ? null : $value;
    }

    private function ensureSchema(): void
    {
        // Live-safe: check only (synonyms / privileges). Never CREATE/ALTER.
        try {
            if (!$this->tableExists('SP_STORE_BUDGET_HEADS')) {
                $this->logMissingOracleObject('SP_STORE_BUDGET_HEADS', 'StoreItemsBudgetController');
            }
            if (!$this->tableExists('SP_STORE_BUDGET_ITEMS')) {
                $this->logMissingOracleObject('SP_STORE_BUDGET_ITEMS', 'StoreItemsBudgetController');
            }
        } catch (\Throwable $e) {
            Log::warning('Store items schema check failed.', ['message' => $e->getMessage()]);
        }
    }
}
