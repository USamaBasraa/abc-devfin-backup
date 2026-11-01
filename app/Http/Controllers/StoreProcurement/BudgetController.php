<?php

namespace App\Http\Controllers\StoreProcurement;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StoreProcurement\Department;
use App\Models\StoreProcurement\BudgetHead;
use App\Models\StoreProcurement\BudgetItem;
use App\Models\StoreProcurement\QuotationRequest;
use App\Models\StoreProcurement\VendorCategory;
use App\Models\AcademicYear;
use App\Models\Grader\Departments;
use App\Services\StoreProcurement\BudgetAnnexureSyncService;
use App\Services\StoreProcurement\PinnacleApiService;
use App\Services\StoreProcurement\SpPermissionService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\StoreProcurement\QuotationChat;


class BudgetController extends Controller
{
    private const BUDGET_ITEMS_API_ENDPOINT = 'capital_expenditure_items';
    private const BUDGET_ITEMS_CACHE_TTL_SECONDS = 600;

    /**
     * Departments the user can access: own dept + granted rights.
     */
    private function getDepartments()
    {
        return app(SpPermissionService::class)->getAccessibleDepartments(auth()->user());
    }

    /**
     * Attach quotation / PO / chat flags to budget list items.
     * Checkbox is disabled if THIS item or a related cross-dept duplicate
     * (same pin_id, or same item_title under same account/head) already has a quotation request.
     */
    private function enrichBudgetItemsWithQuotationState($budgets, $username = null): void
    {
        $allItemIds = [];

        foreach ($budgets as $budget) {
            foreach ($budget->items ?? [] as $item) {
                if (!empty($item->item_id)) {
                    $allItemIds[] = (int) $item->item_id;
                }
            }
        }

        $allItemIds = array_values(array_unique($allItemIds));

        if (empty($allItemIds)) {
            return;
        }

        $itemModels = BudgetItem::with('budgetHead')
            ->whereIn('item_id', $allItemIds)
            ->get()
            ->keyBy('item_id');

        $pinIds = $itemModels->pluck('pin_id')->filter(function ($pinId) {
            return trim((string) $pinId) !== '';
        })->unique()->values()->all();

        $titleKeys = [];
        foreach ($itemModels as $model) {
            $title = strtolower(trim((string) ($model->item_title ?? '')));
            if ($title === '') {
                continue;
            }

            $accountId = strtolower(trim((string) ($model->budgetHead->account_id ?? '')));
            $headTitle = strtolower(trim((string) ($model->budgetHead->head_title ?? '')));
            $key = ($accountId !== '' ? 'a:' . $accountId : 'h:' . $headTitle) . '|' . $title;
            $titleKeys[$key] = true;
        }

        $sentItemIds = QuotationRequest::whereIn('item_id', $allItemIds)
            ->pluck('item_id')
            ->map(function ($id) {
                return (int) $id;
            })
            ->unique()
            ->values()
            ->all();

        $sentPinIds = [];
        if (!empty($pinIds)) {
            $sentPinIds = DB::table('sp_quotation_request as qr')
                ->join('sp_budget_items as bi', 'bi.item_id', '=', 'qr.item_id')
                ->whereIn('bi.pin_id', $pinIds)
                ->whereNotNull('bi.pin_id')
                ->pluck('bi.pin_id')
                ->map(function ($pinId) {
                    return trim((string) $pinId);
                })
                ->filter()
                ->unique()
                ->values()
                ->all();
        }

        $sentTitleKeys = [];
        if (!empty($titleKeys)) {
            $relatedRows = DB::table('sp_quotation_request as qr')
                ->join('sp_budget_items as bi', 'bi.item_id', '=', 'qr.item_id')
                ->join('sp_budget_heads as bh', 'bh.bh_id', '=', 'bi.bh_id')
                ->select('bi.item_title', 'bh.account_id', 'bh.head_title')
                ->get();

            foreach ($relatedRows as $row) {
                $title = strtolower(trim((string) ($row->item_title ?? $row->ITEM_TITLE ?? '')));
                if ($title === '') {
                    continue;
                }

                $accountId = strtolower(trim((string) ($row->account_id ?? $row->ACCOUNT_ID ?? '')));
                $headTitle = strtolower(trim((string) ($row->head_title ?? $row->HEAD_TITLE ?? '')));
                $key = ($accountId !== '' ? 'a:' . $accountId : 'h:' . $headTitle) . '|' . $title;

                if (isset($titleKeys[$key])) {
                    $sentTitleKeys[$key] = true;
                }
            }
        }

        $sentItemLookup = array_fill_keys($sentItemIds, true);
        $sentPinLookup = array_fill_keys($sentPinIds, true);

        foreach ($budgets as $budget) {
            foreach ($budget->items ?? [] as $item) {
                $budgetItem = $itemModels->get((int) $item->item_id);
                $item->quotationRequests = $budgetItem ? $budgetItem->quotationRequests : collect([]);

                $item->has_po = false;
                $item->po_id = null;
                $item->po_number = null;
                $item->unread_chat_count = 0;
                $item->primary_qr_id = null;
                $item->already_sent = false;

                $pinId = trim((string) ($budgetItem->pin_id ?? $item->pin_id ?? ''));
                $title = strtolower(trim((string) ($budgetItem->item_title ?? $item->item_title ?? '')));
                $accountId = strtolower(trim((string) (
                    $budgetItem->budgetHead->account_id
                        ?? $budget->account_id
                        ?? ''
                )));
                $headTitle = strtolower(trim((string) (
                    $budgetItem->budgetHead->head_title
                        ?? $budget->head_title
                        ?? ''
                )));
                $titleKey = $title !== ''
                    ? (($accountId !== '' ? 'a:' . $accountId : 'h:' . $headTitle) . '|' . $title)
                    : null;

                if (
                    isset($sentItemLookup[(int) $item->item_id])
                    || ($pinId !== '' && isset($sentPinLookup[$pinId]))
                    || ($titleKey && isset($sentTitleKeys[$titleKey]))
                ) {
                    $item->already_sent = true;
                }

                foreach ($item->quotationRequests as $qr) {
                    if ($item->primary_qr_id === null) {
                        $item->primary_qr_id = $qr->qr_id;
                    }

                    $chatQuery = QuotationChat::where('qr_id', $qr->qr_id)->where('is_read', 0);
                    if ($username) {
                        $chatQuery->where('entry_username', '!=', $username);
                    }
                    $item->unread_chat_count += $chatQuery->count();

                    $firstChat = QuotationChat::where('qr_id', $qr->qr_id)
                        ->orderBy('created_at', 'asc')
                        ->first();
                    if ($firstChat) {
                        $item->chat_started_at = $firstChat->entry_username
                            ? date('d-M-Y h:i A', strtotime($firstChat->created_at))
                            : null;
                    }

                    if ($qr->po_id) {
                        $po = \App\Models\StoreProcurement\PurchaseOrder::find($qr->po_id);
                        if ($po && $po->po_status == 'generated') {
                            $item->has_po = true;
                            $item->po_id = $po->po_id;
                            $item->po_number = $po->po_no;
                            break;
                        }
                    }
                }
            }
        }
    }

    /**
     * True when this item or a related cross-dept duplicate already has a quotation request.
     */
    private function itemAlreadyHasQuotationRequest(BudgetItem $item): bool
    {
        if (QuotationRequest::where('item_id', $item->item_id)->exists()) {
            return true;
        }

        $item->loadMissing('budgetHead');

        $pinId = trim((string) ($item->pin_id ?? ''));
        if ($pinId !== '') {
            $relatedPinItemIds = BudgetItem::where('pin_id', $pinId)->pluck('item_id');
            if ($relatedPinItemIds->isNotEmpty()
                && QuotationRequest::whereIn('item_id', $relatedPinItemIds)->exists()) {
                return true;
            }
        }

        $title = trim((string) ($item->item_title ?? ''));
        if ($title === '') {
            return false;
        }

        $accountId = trim((string) ($item->budgetHead->account_id ?? ''));
        $headTitle = trim((string) ($item->budgetHead->head_title ?? ''));

        $relatedQuery = BudgetItem::where('item_title', $title)
            ->where('item_id', '!=', $item->item_id)
            ->whereHas('budgetHead', function ($query) use ($accountId, $headTitle) {
                if ($accountId !== '') {
                    $query->where('account_id', $accountId);
                } else {
                    $query->where('head_title', $headTitle);
                }
            });

        $relatedIds = $relatedQuery->pluck('item_id');

        return $relatedIds->isNotEmpty()
            && QuotationRequest::whereIn('item_id', $relatedIds)->exists();
    }

    /**
     * Get user-friendly validation message
     */
    private function getValidationMessage($originalMessage, $fieldName)
    {
        // Extract the actual validation error from the original message
        if (stripos($originalMessage, 'required') !== false) {
            return 'required';
        } elseif (stripos($originalMessage, 'must not be greater than') !== false) {
            // Extract number after "greater than" - look for pattern like "greater than 200 characters"
            preg_match('/greater than (\d+) (\w+)/i', $originalMessage, $matches);
            $maxLength = $matches[1] ?? '';
            return "max {$maxLength}";
        } elseif (stripos($originalMessage, 'must be at least') !== false) {
            // Extract number after "at least" - look for pattern like "at least 1."
            preg_match('/at least (\d+)/i', $originalMessage, $matches);
            $min = $matches[1] ?? '';
            return "min {$min}";
        } elseif (stripos($originalMessage, 'numeric') !== false) {
            return 'invalid number';
        } elseif (stripos($originalMessage, 'invalid') !== false || stripos($originalMessage, 'selected') !== false) {
            return 'invalid';
        } else {
            // Remove field name from message if present
            $message = str_replace(['The items.', '.'], '', $originalMessage);
            $message = preg_replace('/^[a-z_]+\s+/', '', $message);
            return $message;
        }
    }

      public function edit($id)
        {
            $item = BudgetItem::with('budgetHead')->findOrFail($id);

            // Add budget head info for dropdown selection
            $data = $item->toArray();
            if ($item->budgetHead) {
                $data['budget_head_title'] = $item->budgetHead->head_title;
                $data['account_id'] = $item->budgetHead->account_id ?? null;
            }

            // Ensure bh_cat_id is present (Oracle casing / relation fallback)
            $bhCatId = $item->bh_cat_id ?? $data['bh_cat_id'] ?? $data['BH_CAT_ID'] ?? null;
            if (empty($bhCatId) && !empty($data['account_id'])) {
                $category = VendorCategory::where('pinnacle_account_id', $data['account_id'])->first();
                if ($category) {
                    $bhCatId = $category->category_id;
                    $data['budget_head_title'] = $data['budget_head_title']
                        ?? ($category->category_name . ' (' . ($category->budget_type ?? '') . ')');
                }
            }
            $data['bh_cat_id'] = $bhCatId;
            $data['ay_id'] = $item->ay_id ?? $data['ay_id'] ?? $data['AY_ID'] ?? null;

            return response()->json($data);
        }


        public function destroy($id)
        {
            // dd($id);
            // $item = BudgetItem::findOrFail($id);
            // $item->delete();

                // Find the budget item first
    $item = BudgetItem::findOrFail($id);

    // Keep its related budget head id before deletion
    $headId = $item->bh_id;

    // Delete the item
    $item->delete();

    // Check if any items remain under this head
    $remainingItems = BudgetItem::where('bh_id', $headId)->count();

    if ($remainingItems === 0) {
        // No items left — delete the head too
        BudgetHead::where('bh_id', $headId)->delete();
    }

            return response()->json(['message' => 'Item deleted successfully']);
        }
        public function update(Request $request, $id)
        {
            // Validate update request
            $validated = $request->validate([
                'items.0.item_title' => 'required|string|max:1000',
                'items.0.item_id' => 'nullable',
                'items.0.item_brand' => 'nullable|string|max:1000',
                'items.0.item_model' => 'nullable|string|max:1000',
                'items.0.quantity' => 'required|numeric|min:1',
                'items.0.rate' => 'required|numeric|min:0',
                'items.0.budget_type' => 'required|in:budgeted,unbudgeted,reappropriated',
                'items.0.purchase_mode' => 'required|in:purchase,rent',
                'items.0.remarks' => 'nullable|string|max:255',
            ], [
                'items.0.item_title.required' => 'Item title is required.',
                'items.0.item_title.max' => 'Item title must not be greater than 1000 characters.',
                'items.0.item_brand.max' => 'Brand must not be greater than 1000 characters.',
                'items.0.item_model.max' => 'Model must not be greater than 1000 characters.',
                'items.0.quantity.required' => 'Quantity is required.',
                'items.0.quantity.min' => 'Quantity must be at least 1.',
                'items.0.rate.required' => 'Rate is required.',
                'items.0.rate.min' => 'Rate must be 0 or greater.',
                'items.0.budget_type.required' => 'Budget type is required.',
                'items.0.budget_type.in' => 'Invalid budget type selected.',
                'items.0.purchase_mode.required' => 'Purchase mode is required.',
                'items.0.purchase_mode.in' => 'Invalid purchase mode selected.',
                'items.0.remarks.max' => 'Remarks must not be greater than 255 characters.',
            ]);

            try {
                $item = BudgetItem::findOrFail($id);

                $item->update([
                    'item_title' => $validated['items'][0]['item_title'],
                    'brand' => $validated['items'][0]['item_brand'] ?? null,
                    'model' => $validated['items'][0]['item_model'] ?? null,
                    'quantity' => $validated['items'][0]['quantity'],
                    'rate' => $validated['items'][0]['rate'],
                    'budget_type' => $validated['items'][0]['budget_type'],
                    'purchase_mode' => $validated['items'][0]['purchase_mode'],
                    'remarks' => $validated['items'][0]['remarks'] ?? null,
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Item updated successfully!'
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error: ' . $e->getMessage()
                ], 422);
            }
        }




    /////////////////////
        public function index()
        {
            // @dd("working");
            $departments = $this->getDepartments();
            $categories = VendorCategory::all();
            $academic_years = AcademicYear::latest('ay_id')->get();

            return view('store_procurement.budget.create', compact('departments','categories','academic_years'));
        }
         public function search(Request $request, PinnacleApiService $pinnacleApiService)
    {
        $q = trim((string) $request->get('q', ''));
        $categoryId = $request->get('category_id');
        $items = $this->budgetItemOptions($pinnacleApiService);

        if (!empty($categoryId)) {
            $category = VendorCategory::find($categoryId);
            $categoryNeedle = $this->normalizeBudgetItemSearchValue($category->category_name ?? '');

            if ($categoryNeedle !== '') {
                $items = array_values(array_filter($items, function (array $item) use ($categoryNeedle) {
                    foreach (['fin_acc_title', 'item_category'] as $field) {
                        $value = $this->normalizeBudgetItemSearchValue($item[$field] ?? '');

                        if ($value !== '' && (mb_stripos($value, $categoryNeedle) !== false || mb_stripos($categoryNeedle, $value) !== false)) {
                            return true;
                        }
                    }

                    return false;
                }));
            }
        }

        if ($q !== '') {
            $needle = mb_strtolower($q);
            $items = array_values(array_filter($items, function (array $item) use ($needle) {
                foreach (['item_title', 'item_type', 'unit', 'app_unit', 'fin_acc_title', 'item_category'] as $field) {
                    if (mb_stripos((string) ($item[$field] ?? ''), $needle) !== false) {
                        return true;
                    }
                }

                return false;
            }));
        }

        return response()->json($items);
    }

    private function budgetItemOptions(PinnacleApiService $pinnacleApiService): array
    {
        $cacheKey = 'store_procurement.budget.item_options.' . self::BUDGET_ITEMS_API_ENDPOINT;
        $cached = Cache::get($cacheKey);

        if (is_array($cached) && !empty($cached)) {
            return $cached;
        }

        $response = $pinnacleApiService->fetch(self::BUDGET_ITEMS_API_ENDPOINT);
        $records = $this->extractBudgetItemRecords($response);

        if (empty($records)) {
            Cache::forget($cacheKey);
            return [];
        }

        $mapped = array_values(array_map(function ($item) {
            return [
                'item_id' => $item['ID'] ?? $item['Id'] ?? $item['id'] ?? null,
                'item_title' => $this->cleanBudgetItemValue($item['Title'] ?? $item['title'] ?? null),
                'item_type' => $this->cleanBudgetItemValue($item['ItemType'] ?? $item['item_type'] ?? null),
                'unit' => $this->cleanBudgetItemValue($item['Unit'] ?? $item['unit'] ?? null),
                'app_unit' => $this->cleanBudgetItemValue($item['AppUnit'] ?? $item['app_unit'] ?? null),
                'fin_acc_title' => $this->cleanBudgetItemValue($item['FinAcc01_Title'] ?? $item['fin_acc01_title'] ?? null),
                'item_category' => $this->cleanBudgetItemValue($item['Category'] ?? $item['category'] ?? null),
                'brand' => null,
                'model' => null,
                'remarks' => null,
                'rate' => null,
                'budget_type' => null,
                'purchase_mode' => null,
            ];
        }, $records));

        if (!empty($mapped)) {
            Cache::put($cacheKey, $mapped, self::BUDGET_ITEMS_CACHE_TTL_SECONDS);
        }

        return $mapped;
    }

    private function normalizeBudgetItemSearchValue($value): string
    {
        $value = mb_strtolower(trim((string) $value));
        $value = preg_replace('/\([^)]*\)/', '', $value);
        $value = preg_replace('/[^a-z0-9]+/i', ' ', $value);

        return trim(preg_replace('/\s+/', ' ', $value));
    }

    private function extractBudgetItemRecords(array $response): array
    {
        if (isset($response['data']) && is_array($response['data'])) {
            return $response['data'];
        }

        if (isset($response['Data']) && is_array($response['Data'])) {
            return $response['Data'];
        }

        if (array_is_list($response)) {
            return $response;
        }

        return [];
    }

    private function cleanBudgetItemValue($value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    // Show form
    public function create()
    {
        $departments = $this->getDepartments();
        $categories = VendorCategory::all();
        $academic_years = AcademicYear::latest('ay_id')->get();

        return view('store_procurement.budget.create', compact('departments','categories','academic_years'));
    }

    public function syncFromApi(Request $request, BudgetAnnexureSyncService $syncService)
    {
        // dd($request->all());
        $user = auth()->user();
        $deptId = (int) $user->dept_id;
        $ayId = $request->filled('year_id') ? (int) $request->input('year_id') : null;

        if ($deptId <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Your account does not have a valid department assigned.',
            ], 422);
        }

        try {
            $summary = $syncService->sync($deptId, $ayId, $user->username ?? 'system');

            $headsTotal = ($summary['heads_created'] ?? 0) + ($summary['heads_updated'] ?? 0);
            $itemsTotal = ($summary['items_created'] ?? 0) + ($summary['items_updated'] ?? 0);

            if ($headsTotal === 0 && $itemsTotal === 0) {
                return response()->json([
                    'success' => false,
                    'message' => $summary['message'] ?? 'No budget records were synced.',
                    'summary' => $summary,
                ], 422);
            }

            return response()->json([
                'success' => true,
                'message' => sprintf(
                    'Synced %d budget head(s) and %d item(s) from Pinnacle.',
                    $headsTotal,
                    $itemsTotal
                ),
                'summary' => $summary,
            ]);
        } catch (\Throwable $exception) {
            Log::error('Budget sync from Pinnacle failed.', [
                'dept_id' => $deptId,
                'message' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString(),
            ]);

            $rawMsg = $exception->getMessage();
            $friendlyMessage = 'Unable to sync budget from Pinnacle at this time. Please try again later.';

            if (stripos($rawMsg, 'ORA-00001') !== false || stripos($rawMsg, 'unique constraint') !== false) {
                $friendlyMessage = 'Some budget items already exist in the system. The records could not be duplicated.';
            } elseif (stripos($rawMsg, 'ORA-') !== false || stripos($rawMsg, 'SQLSTATE') !== false) {
                $friendlyMessage = 'A database error occurred while saving the budget. Please try again or contact support.';
            } elseif (stripos($rawMsg, 'cURL') !== false || stripos($rawMsg, 'timeout') !== false || stripos($rawMsg, 'Connection') !== false) {
                $friendlyMessage = 'Unable to connect to the Pinnacle server. Please check network connectivity and try again.';
            }

            return response()->json([
                'success' => false,
                'message' => $friendlyMessage,
            ], 500);
        }
    }


public function reappropriateAcross(Request $request)
{
    $isExistingItem = !empty($request->existing_item_id);

    // === STEP 2: Validation ===
    if ($isExistingItem) {
        $request->validate([
            'sources' => 'required|array|min:1',
            'target_bh_id' => 'required',
            'existing_item_id' => 'required',
        ]);
    } else {
        $request->validate([
            'sources' => 'required|array|min:1',
            'target_bh_id' => 'required',
            'new_item_title' => 'required',
            'quantity' => 'required|numeric|min:1',
            'rate' => 'required|numeric|min:1',
        ]);
    }

    DB::beginTransaction();

    try {
        $totalTransferAmount = 0;
        $new_item_id = 0;

        // === STEP 4: Deduct from source items ===
        foreach ($request->sources as $sourceItem) {
            $item = BudgetItem::find($sourceItem['id']);

            if (!$item) {
                throw new \Exception("Source item not found.");
            }

            $currentAmount = $item->quantity * $item->rate;

            if ($sourceItem['amount'] > $currentAmount) {
                throw new \Exception("Amount not available for: {$item->item_title}");
            }

            $remaining = $currentAmount - $sourceItem['amount'];
            $newRate = $remaining / $item->quantity;

            $item->rate = $newRate;
            $item->save();

            $totalTransferAmount += $sourceItem['amount'];
        }

        // === STEP 5: Update or Create Target Item ===
        if ($isExistingItem) {
            $targetItem = BudgetItem::find($request->existing_item_id);

            if (!$targetItem) {
                throw new \Exception("Target item not found.");
            }

            $targetCurrentAmount = $targetItem->quantity * $targetItem->rate;
            $targetNewAmount = $targetCurrentAmount + $totalTransferAmount;

            $targetItem->rate = $targetNewAmount / $targetItem->quantity;
            $targetItem->save();

            $to_item_id = $targetItem->item_id;
        } else {
            $new_item = BudgetItem::create([
                'item_title' => $request->new_item_title,
                'brand' => $request->brand,
                'model' => $request->model,
                'quantity' => $request->quantity,
                'rate' => $request->rate,
                'remarks' => $request->remarks,
                'budget_type' => 'reappropriated',
                'bh_id' => $request->target_bh_id,
                'entry_username' => auth()->user()->username,
            ]);

            $to_item_id = $new_item->item_id;
        }

        // === STEP 6: Insert history records ===
        foreach ($request->sources as $source) {
            DB::table('sp_reappropriate_history')->insert([
                'from_item_id' => $source['id'],
                'to_item_id' => $to_item_id,
                'amount' => $source['amount'],
                'budgetary_head_id' => $request->target_bh_id,
                'entry_user_id' => auth()->user()->username,
                'school_id' => $request->school_id ?? null,
                'department_id' => $request->department_id ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        DB::commit();
        return response()->json(['message' => 'Re-appropriation successful!']);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['message' => 'Error: ' . $e->getMessage()], 422);
    }
}







public function reappropriateAcross1(Request $request)
{
    // dd($request->all());
    // === STEP 1: Check what user wants to do ===
    // Is user adding to existing item or creating new item?
    $isExistingItem = !empty($request->existing_item_id);

    // === STEP 2: Validation - Check if data is correct ===
    if ($isExistingItem) {
        // If adding to existing item
        $request->validate([
            'sources' => 'required|array|min:1',
            'target_bh_id' => 'required',
            'existing_item_id' => 'required',
        ]);
    } else {
        // If creating new item
        $request->validate([
            'sources' => 'required|array|min:1',
            'target_bh_id' => 'required',
            'new_item_title' => 'required',
            'quantity' => 'required|numeric|min:1',
            'rate' => 'required|numeric|min:1',
        ]);
    }

    // === STEP 3: Start database transaction - Rollback if error occurs ===
    DB::beginTransaction();
$new_item_id = 0.0;
    try {
        // === STEP 4: Process source items (reduce their amounts) ===
        $totalTransferAmount = 0; // Total amount being transferred

        foreach ($request->sources as $sourceItem) {
            // Get item from database
            $item = BudgetItem::find($sourceItem['id']);

            // Calculate item's current amount (quantity x rate)
            $currentAmount = $item->quantity * $item->rate;

            // Check: Is transfer amount available?
            if ($sourceItem['amount'] > $currentAmount) {
                throw new \Exception("Amount not available for: {$item->item_title}");
            }

            // Calculate remaining amount (current - transfer)
            $remaining = $currentAmount - $sourceItem['amount'];

            // Calculate new rate (remaining amount / quantity)
            $newRate = $remaining / $item->quantity;

            // Update item rate
            $item->rate = $newRate;
            $item->save();

            // Add to total transfer amount
            $totalTransferAmount = $totalTransferAmount + $sourceItem['amount'];
        }

        // === STEP 5: Update or Create target item ===
        if ($isExistingItem) {
            // --- Increase existing item amount ---

            // Get target item from database
            $targetItem = BudgetItem::find($request->existing_item_id);

            // Get target item current amount
            $targetCurrentAmount = $targetItem->quantity * $targetItem->rate;

            // Calculate new amount (current + transferred)
            $targetNewAmount = $targetCurrentAmount + $totalTransferAmount;

            // Update rate (new amount / quantity)
            $targetItem->rate = $targetNewAmount / $targetItem->quantity;
            $targetItem->save();

        } else {
            // --- Create new item ---

           $new_item = BudgetItem::create([
                'item_title' => $request->new_item_title,
                'brand' => $request->brand,
                'model' => $request->model,
                'quantity' => $request->quantity,
                'rate' => $request->rate,
                'remarks' => $request->remarks,
                'budget_type' => 'reappropriated',
                'bh_id' => $request->target_bh_id,
                'entry_username' => auth()->user()->username,
            ]);
             $new_item_id = $new_item->item_id ;
        }


// dd($new_item_id->item_id);
        // === STEP 6: All successful - Save changes ===
        DB::commit();
        // return response()->json(['message' => 'Re-appropriation successful!']);

    } catch (\Exception $e) {
        // === If any error occurs, rollback all changes ===
        DB::rollBack();
        return response()->json(['message' => 'Error: ' . $e->getMessage()], 422);
    }


if($new_item_id == 0)
{
$to_item_id = $request->existing_item_id ;
}
else
    $to_item_id = $new_item_id->item_id ;




try {
        foreach ($request->sources as $source) {
            DB::table('sp_reappropriate_history')->insert([
                'from_item_id' => $source['id'],
                // 'to_item_id' => $request->existing_item_id,
                'to_item_id' => $to_item_id,
                'amount' => $source['amount'],
                'budgetary_head_id' => $request->target_bh_id,
                'entry_user_id' => auth()->user()->username, // assuming user is logged in
                'school_id' => $request->school_id ?? null,
                'department_id' => $request->department_id ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
       DB::commit();
         return response()->json(['message' => 'Re-appropriation successful!']);

        // return response()->json([
        //     'status' => 'success',
        //     'message' => 'Reappropriation records saved successfully.'
        // ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 422);
            // return response()->json([
        //     'status' => 'error',
        //     'message' => 'Failed to save records: ' . $e->getMessage()
        // ], 500);
    }












}



public function sendForApproval(Request $request)
{
    try {
    $item = BudgetItem::find($request->item_id);
    // dd( $item->budget_type);

$reason = $request->request_reason;
$budget_type = $item->budget_type;
//  dd($budget_type);

    if (!$item) {
        return response()->json(['success' => false, 'message' => 'Item not found.']);
    }

  // Update approval status safely
    $item->send_reappropriation_approval_reapprop = 'YES'; // ✅ use string value in quotes
    $item->reason_for_approval = $reason; // ✅ value from request
    $item->requested_budget_type = $budget_type ; // ✅ value from request
    $item->reappropriation_approval = 'pending' ; // ✅ value from request

    $item->updated_at = now();

    $item->save();



    return response()->json(['success' => true, 'message' => 'Item sent for approval successfully.']);
} catch (\Exception $e) {
    return response()->json([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage(),
    ]);
}

}















public function list()
{
    // @dd("working");
    $userDeptIds = $this->getDepartments()->pluck('dept_id')->toArray();

    // Use raw SQL query from model - by default show budgets of departments from getDepartments()
    $budgets = BudgetHead::getBudgetsWithItems($userDeptIds);
    // dd($budgets);

    // Load quotation requests and PO data for all items
    $user = auth()->user();
    $username = $user ? ($user->username ?? $user->EMP_AD_USERNAME ?? $user->name ?? null) : null;
    $unreadChatItems = [];

    $this->enrichBudgetItemsWithQuotationState($budgets, $username);

    foreach ($budgets as $budget) {
        foreach ($budget->items as $item) {
            if (($item->unread_chat_count ?? 0) > 0) {
                $unreadChatItems[] = (object) [
                    'item_id' => $item->item_id,
                    'item_title' => $item->item_title,
                    'budget_head' => $budget->head_title ?? $budget->head_title ?? '',
                    'count' => $item->unread_chat_count,
                    'qr_id' => $item->primary_qr_id,
                ];
            }
        }
    }

    $totalUnreadChats = collect($unreadChatItems)->sum(function ($item) {
        return $item->count;
    });

    $academic_years = AcademicYear::latest('ay_id')->get();
    $departments = $this->getDepartments();

    // Get vendor categories for Budget Head filter dropdown
    $vendor_categories = VendorCategory::orderBy('category_name')
                                       ->get();

    return view('store_procurement.budget.list', compact(
        'budgets',
        'academic_years',
        'departments',
        'vendor_categories',
        'unreadChatItems',
        'totalUnreadChats'
    )
);
}


    public function getReappropriationHistory($itemId)
{
    // $history = DB::table('sp_reappropriate_history')
    //     ->where('to_item_id', $itemId)
    //     ->orderBy('created_at', 'desc')
    //     ->get();
     $history = DB::table('sp_reappropriate_history as rh')
        ->join('sp_budget_items as bi', 'rh.from_item_id', '=', 'bi.item_id' )
        // ->join('sp_vendor_category as vc', 'bi.BH_CAT_ID', '=', 'vc.category_id')
        ->select(
            'rh.id',
            'rh.from_item_id',
            'bi.item_title as from_item_title',
            // 'vc.category_name',
            'rh.to_item_id',
            'rh.amount',
            'rh.budgetary_head_id',
            'rh.entry_user_id',
            'rh.school_id',
            'rh.department_id',
            'rh.created_at'
        )
        ->where('rh.to_item_id', $itemId)
        ->orderBy('rh.created_at', 'desc')
        ->get();



    return response()->json(['data' => $history]);
}


    /**
     * Filter budgets by academic year, department, and category
     * Simple method - Uses existing model method for filtering
     */
    public function filterByAcademicYear(Request $request)
    {
        // Step 1: Get user's departments
        $userDeptIds = $this->getDepartments()->pluck('dept_id')->toArray();

        // Step 2: Get filter values from request (or null if not provided)
        $yearId = $request->year_id ?? null;
        $deptId = $request->dept_id ?? null;
        $categoryId = $request->category_id ?? null;

        // Step 3: Determine which departments to query
        // If user selected specific department, use only that one
        // Otherwise use all user's departments
        if ($deptId) {
            if (!in_array((int) $deptId, array_map('intval', $userDeptIds), true)) {
                abort(403, 'You do not have access to this department budget.');
            }
            $deptIdsToQuery = [$deptId];
        } else {
            $deptIdsToQuery = $userDeptIds; // All user's departments
        }

        // Step 4: Use model method to get filtered budgets
        // This method handles all the complex SQL - we just pass parameters
        $budgets = BudgetHead::getBudgetsWithItems(
            $deptIdsToQuery,  // Department(s) to query
            $yearId,          // Academic year filter
            null,             // Budget head ID (not used in filters)
            $categoryId       // Category filter
        );

        // Load quotation requests, PO, and chat data so the filtered view
        // matches the main list (View / Revise) instead of the old Edit UI.
        $user = auth()->user();
        $username = $user ? ($user->username ?? $user->EMP_AD_USERNAME ?? $user->name ?? null) : null;

        $this->enrichBudgetItemsWithQuotationState($budgets, $username);

        // Step 5: Return HTML for the filtered budgets
        return view('store_procurement.budget.partials.budget_list', compact('budgets'))->render();
    }

    // Store head + items
    public function store(Request $request)
    {

        // Comprehensive validation with custom messages
        $validator = \Validator::make($request->all(), [
            'dept_id' => 'required|exists:DEPARTMENTS,DEPT_ID',
            'year_id' => 'required|integer|exists:academic_years,ay_id',
            'head_title' => 'required|string|max:150',
            'bh_cat_id' => 'required|integer',
            'remarks' => 'nullable|string|max:2000',
            'items' => 'required|array|min:1',
            'items.*.item_title' => 'required|string|max:1000',
            'items.*.item_brand' => 'nullable|string|max:1000',
            'items.*.item_model' => 'nullable|string|max:1000',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.rate' => 'required|numeric|min:0',
            'items.*.budget_type' => 'required|in:budgeted,unbudgeted,reappropriated',
            'items.*.purchase_mode' => 'required|in:purchase,rent',
            'items.*.remarks' => 'nullable|string|max:255',
        ], [
            'dept_id.required' => 'Department is required.',
            'dept_id.exists' => 'Invalid department selected.',
            'year_id.required' => 'Academic year is required.',
            'year_id.exists' => 'Invalid academic year selected.',
            'head_title.required' => 'Budget head title is required.',
            'bh_cat_id.required' => 'Budget category is required.',
            'items.required' => 'At least one budget item is required.',
            'items.min' => 'At least one budget item must be added.',
        ]);

        if ($validator->fails()) {
            // Transform error messages to be user-friendly
            $errors = $validator->errors();
            $transformedErrors = new \Illuminate\Support\MessageBag();

            foreach ($errors->messages() as $key => $messages) {
                if (preg_match('/^items\.(\d+)\.(.+)$/', $key, $matches)) {
                    $index = $matches[1];
                    $field = $matches[2];
                    $itemNumber = $index + 1;

                    foreach ($messages as $message) {
                        // Create user-friendly message
                        $validationMsg = $this->getValidationMessage($message, $field);
                        $newMessage = "#{$itemNumber}: {$validationMsg}";
                        $transformedErrors->add($key, $newMessage);
                    }
                } else {
                    // Keep non-item errors as-is
                    foreach ($messages as $message) {
                        $transformedErrors->add($key, $message);
                    }
                }
            }

            if ($request->ajax()) {
                return response()->json([
                    'errors' => $transformedErrors
                ], 422);
            }
            return back()->withErrors($transformedErrors)->withInput();
        }

        $validated = $validator->validated();

        DB::beginTransaction();
        try {
            $user = auth()->user();
            $deptId = (int) $user->dept_id;

            if ($deptId <= 0) {
                throw new \Exception('Your account does not have a valid department assigned.');
            }

            // Save budget head
            $head = BudgetHead::create([
                'dept_id' => $deptId,
                'head_title' => $validated['head_title'],
                'remarks' => $validated['remarks'] ?? null,
                'ay_id' => $validated['year_id'],
                'entry_username' => auth()->user()->username,

            ]);

            // Save items
            foreach ($validated['items'] as $item) {
                BudgetItem::create([
                    'bh_id' => $head->bh_id,
                    'bh_cat_id' => $validated['bh_cat_id'],
                    'item_title' => $item['item_title'],
                    'brand' => $item['item_brand'] ?? null,
                    'model' => $item['item_model'] ?? null,
                    'quantity' => $item['quantity'],
                    'rate' => $item['rate'],
                    'budget_type' => $item['budget_type'],
                    'purchase_mode' => $item['purchase_mode'],
                    'remarks' => $item['remarks'] ?? null,
                    'ay_id' => $validated['year_id'],
                    'entry_username' => auth()->user()->username,
                ]);
            }

            DB::commit();

            // Return JSON response for AJAX
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Budget created successfully!'
                ]);
            }

            return redirect()->route('budget.index')->with('success', 'Budget created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();

            // Return JSON error for AJAX
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error: ' . $e->getMessage()
                ], 422);
            }

            return back()->with('error', 'Error: ' . $e->getMessage())->withInput();
        }
    }



    public function requestQuotation(Request $request, $bh_id)
{
    // dd($request->all(),  $bh_id);
    $itemIds = $request->items ?? [];
    $quantities = $request->quantities ?? [];
//    dd( $itemIds);
    if (empty($itemIds)) {
        return response()->json([
            'success' => false,
            'message' => 'No items selected'
        ], 400);
    }

    $user = auth()->user();
    $user_dept_id = $user->dept_id;
    $user_school_id = $user->school_id;

    DB::beginTransaction(); // ✅ Start Transaction

    try {

        foreach ($itemIds as $itemId) {

            $item = BudgetItem::find($itemId);
            if (!$item) {
                continue;
            }

            $ownRequestExists = QuotationRequest::where('item_id', $itemId)->exists();
            if (!$ownRequestExists && $this->itemAlreadyHasQuotationRequest($item)) {
                DB::rollBack();

                return response()->json([
                    'success' => false,
                    'message' => 'Quotation request already sent for "' . $item->item_title . '" from another department mapping.',
                ], 422);
            }

            $requested_qty = $quantities[$itemId] ?? $item->quantity;
            $budget_qty = $item->quantity;

            $category = VendorCategory::find($item->bh_cat_id);
            $parent_dept_id = $category->parent_department_id ?? null;
            $parentApprovalRequired = (string) ($category->q_parent_dept_required ?? '1');
            $headApprovalRequired = (string) ($category->q_head_approval_required ?? '0') === '1';

            $existingRequest = QuotationRequest::where('item_id', $itemId)
                ->where('bh_id', $bh_id)
                ->first();

            if ($existingRequest) {
                $currentVersion = $existingRequest->current_version ?? 1;
                $newVersion = $currentVersion + 1;

                DB::table('SP_QUOTATION_REQUEST_HISTORY')
                    ->where('QR_ID', $existingRequest->qr_id)
                    ->where('IS_CURRENT', 1)
                    ->update(['IS_CURRENT' => 0]);

                $existingRequest->update([
                    'requested_quantity' => $requested_qty,
                    'budget_quantity' => $budget_qty,
                    'current_version' => $newVersion,
                    'is_revised' => 1,
                    'status' => 'pending',
                    'updated_at' => now(),
                ]);

                DB::table('SP_QUOTATION_REQUEST_HISTORY')->insert([
                    'QR_ID' => $existingRequest->qr_id,
                    'VERSION_NUMBER' => $newVersion,
                    'IS_CURRENT' => 1,
                    'BUDGET_QUANTITY' => $budget_qty,
                    'REQUESTED_QUANTITY' => $requested_qty,
                    'ITEM_ID' => $itemId,
                    'BH_ID' => $bh_id,
                    'CAT_ID' => $item->bh_cat_id,
                    'REQUEST_DEPARTMENT_ID' => $user_dept_id,
                    'REQUEST_SCHOOL_ID' => $user_school_id,
                    'PARENT_DEPARTMENT_ID' => $parent_dept_id,
                    'ENTRY_USER_ID' => auth()->id(),
                    'APPROVAL_STATUS' => 'pending',
                    'CREATED_AT' => now(),
                    'UPDATED_AT' => now(),
                ]);

            } else {

                if($parentApprovalRequired == "0"){
                   $data = [
                    'bh_id' => $bh_id,
                    'item_id' => $itemId,
                    'requested_quantity' => $requested_qty,
                    'budget_quantity' => $budget_qty,
                    'current_version' => 1,
                    'is_revised' => 0,
                    'cat_id' => $item->bh_cat_id,
                    'request_department_id' => $user_dept_id,
                    'request_school_id' => $user_school_id,
                    'parent_department_id' => $parent_dept_id,
                    'status' => 'pending',
                    'department_head_approval' => 'approved',
                    'visibility_to_vendors' => 'yes',
                    'allow_decline_remarks' => 'Systematically Approved',
                    'entry_user_id' => auth()->id(),
                ];
                }
                else{
                    //    dd('else');
                $data = [
                    'bh_id' => $bh_id,
                    'item_id' => $itemId,
                    'requested_quantity' => $requested_qty,
                    'budget_quantity' => $budget_qty,
                    'current_version' => 1,
                    'is_revised' => 0,
                    'cat_id' => $item->bh_cat_id,
                    'request_department_id' => $user_dept_id,
                    'request_school_id' => $user_school_id,
                    'parent_department_id' => $parent_dept_id,
                    'status' => 'pending',
                    'parent_dept_reviewed' => 'NO',
                    'entry_user_id' => auth()->id(),
                ];
            }
                if ($headApprovalRequired) {
                    $data['department_head_approval'] = null;
                    unset($data['visibility_to_vendors'], $data['allow_decline_remarks']);
                } elseif ($parent_dept_id && $parent_dept_id == $user_dept_id) {
                    $data['department_head_approval'] = 'approved';
                }

                $quotationRequest = QuotationRequest::create($data);

                DB::table('SP_QUOTATION_REQUEST_HISTORY')->insert([
                    'QR_ID' => $quotationRequest->qr_id,
                    'VERSION_NUMBER' => 1,
                    'IS_CURRENT' => 1,
                    'BUDGET_QUANTITY' => $budget_qty,
                    'REQUESTED_QUANTITY' => $requested_qty,
                    'ITEM_ID' => $itemId,
                    'BH_ID' => $bh_id,
                    'CAT_ID' => $item->bh_cat_id,
                    'REQUEST_DEPARTMENT_ID' => $user_dept_id,
                    'REQUEST_SCHOOL_ID' => $user_school_id,
                    'PARENT_DEPARTMENT_ID' => $parent_dept_id,
                    'ENTRY_USER_ID' => auth()->id(),
                    'APPROVAL_STATUS' => 'pending',
                    'CREATED_AT' => now(),
                    'UPDATED_AT' => now(),
                ]);
            }
        }

        DB::commit(); // ✅ Commit if all OK

        return response()->json([
            'success' => true,
            'message' => count($itemIds) . ' items sent for quotation request.'
        ]);

    } catch (\Exception $e) {

        DB::rollBack(); // ❌ Rollback on failure

        return response()->json([
            'success' => false,
            'message' => 'Request failed. Transaction rolled back.',
            'error' => $e->getMessage()
        ], 500);
    }
}

    /**
     * Edit Quotation Request - Create Revision
     */
    public function editQuotation(Request $request, $bh_id, $qr_id)
    {
        $validated = $request->validate([
            'requested_quantity' => 'required|numeric|min:1',
            'item_id' => 'required|integer',
        ], [
            'requested_quantity.required' => 'Requested quantity is required.',
            'requested_quantity.min' => 'Requested quantity must be at least 1.',
            'item_id.required' => 'Item ID is required.',
        ]);

        DB::beginTransaction();

        try {
            $existingRequest = QuotationRequest::findOrFail($qr_id);
            $item = BudgetItem::findOrFail($validated['item_id']);
            $category = VendorCategory::find($item->bh_cat_id);
            $parent_dept_id = $category->parent_department_id ?? null;

            $currentVersion = $existingRequest->current_version ?? 1;
            $newVersion = $currentVersion + 1;

            DB::table('SP_QUOTATION_REQUEST_HISTORY')
                ->where('QR_ID', $qr_id)
                ->where('IS_CURRENT', 1)
                ->update(['IS_CURRENT' => 0]);

            $existingRequest->update([
                'requested_quantity' => $validated['requested_quantity'],
                'budget_quantity' => $item->quantity,
                'current_version' => $newVersion,
                'is_revised' => 1,
                'status' => 'pending',
                'updated_at' => now(),
            ]);

            DB::table('SP_QUOTATION_REQUEST_HISTORY')->insert([
                'QR_ID' => $qr_id,
                'VERSION_NUMBER' => $newVersion,
                'IS_CURRENT' => 1,
                'BUDGET_QUANTITY' => $item->quantity,
                'REQUESTED_QUANTITY' => $validated['requested_quantity'],
                'ITEM_ID' => $validated['item_id'],
                'BH_ID' => $bh_id,
                'CAT_ID' => $item->bh_cat_id,
                'REQUEST_DEPARTMENT_ID' => $existingRequest->request_department_id,
                'REQUEST_SCHOOL_ID' => $existingRequest->request_school_id,
                'PARENT_DEPARTMENT_ID' => $parent_dept_id,
                'ENTRY_USER_ID' => auth()->id(),
                'APPROVAL_STATUS' => 'pending',
                'CREATED_AT' => now(),
                'UPDATED_AT' => now(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Quotation request updated successfully! Revision #' . $newVersion . ' created.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 422);
        }
    }

    /**
     * Get Quotation Request History
     */
    public function getQuotationHistory($qr_id)
    {
        try {
            $history = DB::table('SP_QUOTATION_REQUEST_HISTORY')
                ->where('QR_ID', $qr_id)
                ->orderBy('VERSION_NUMBER', 'desc')
                ->get()
                ->map(function($item) {
                    // Get username from entry_user_id - check if property exists
                    $username = 'N/A';
                    $entryUserId = null;

                    // Check both uppercase and lowercase column names
                    if (isset($item->ENTRY_USER_ID)) {
                        $entryUserId = $item->ENTRY_USER_ID;
                    } elseif (isset($item->entry_user_id)) {
                        $entryUserId = $item->entry_user_id;
                    }

                    if ($entryUserId) {
                        $user = DB::table('users')->where('id', $entryUserId)->first();
                        if ($user) {
                            $username = $user->username ?? $user->name ?? 'N/A';
                        }
                    }

                    // Get all fields with fallback for both cases
                    return [
                        'version_number' => $item->VERSION_NUMBER ?? $item->version_number ?? 0,
                        'budget_quantity' => $item->BUDGET_QUANTITY ?? $item->budget_quantity ?? 0,
                        'requested_quantity' => $item->REQUESTED_QUANTITY ?? $item->requested_quantity ?? 0,
                        'is_current' => $item->IS_CURRENT ?? $item->is_current ?? 0,
                        'created_at' => $item->CREATED_AT ?? $item->created_at ?? null,
                        'entry_username' => $username
                    ];
                });

            return response()->json(['history' => $history]);
        } catch (\Exception $e) {
            return response()->json([
                'history' => [],
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // DataTables endpoint for budget items
    public function getBudgetItemsData(Request $request)
    {
        $query = BudgetItem::with(['budgetHead', 'budgetHead.department', 'budgetHead.academicYear'])
            ->select('sp_budget_items.*');

        // Apply filters
        if ($request->academic_year) {
            $query->where('ay_id', $request->academic_year);
        }

        if ($request->budget_head) {
            $query->where('bh_cat_id', $request->budget_head);
        }

        // Get user's department permissions
        $userDeptIds = $this->getDepartments()->pluck('dept_id')->toArray();
        $query->whereHas('budgetHead', function($q) use ($userDeptIds) {
            $q->whereIn('dept_id', $userDeptIds);
        });

        return datatables()->of($query)
            ->addIndexColumn()
            ->addColumn('academic_year', function($item) {
                return $item->budget_year ?? ($item->budgetHead ? $item->budgetHead->budget_year : 'N/A');
            })
            ->addColumn('budget_head', function($item) {
                return $item->budgetHead ? $item->budgetHead->head_title : 'N/A';
            })
            ->addColumn('brand', function($item) {
                return $item->brand ?? '-';
            })
            ->addColumn('model', function($item) {
                return $item->model ?? '-';
            })
            ->addColumn('rate', function($item) {
                return 'Rs. ' . number_format($item->rate, 2);
            })
            ->addColumn('amount', function($item) {
                return 'Rs. ' . number_format($item->quantity * $item->rate, 2);
            })
            ->addColumn('budget_type', function($item) {
                $class = $item->budget_type == 'budgeted' ? 'success' : 'warning';
                return '<span class="badge badge-'.$class.'">'.ucfirst($item->budget_type).'</span>';
            })
            ->addColumn('purchase_mode', function($item) {
                $class = $item->purchase_mode == 'purchase' ? 'primary' : 'info';
                return '<span class="badge badge-'.$class.'">'.ucfirst($item->purchase_mode).'</span>';
            })
            ->addColumn('created_by', function($item) {
                return $item->entry_username ?? 'N/A';
            })
            ->addColumn('actions', function($item) {
                return '
                    <button class="btn btn-sm btn-primary" onclick="editBudget('.$item->item_id.')" title="Edit">
                        <i class="fa fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-danger" onclick="deleteBudget('.$item->item_id.')" title="Delete">
                        <i class="fa fa-trash"></i>
                    </button>
                ';
            })
            ->rawColumns(['budget_type', 'purchase_mode', 'actions'])
            ->make(true);
    }

    // Get total amount for filtered items
    public function getBudgetItemsTotal(Request $request)
    {
        $query = BudgetItem::with('budgetHead');

        // Apply filters
        if ($request->academic_year) {
            $query->where('ay_id', $request->academic_year);
        }

        if ($request->budget_head) {
            $query->where('bh_cat_id', $request->budget_head);
        }

        // Get user's department permissions
        $userDeptIds = $this->getDepartments()->pluck('dept_id')->toArray();
        $query->whereHas('budgetHead', function($q) use ($userDeptIds) {
            $q->whereIn('dept_id', $userDeptIds);
        });

        $total = $query->get()->sum(function($item) {
            return $item->quantity * $item->rate;
        });

        return response()->json(['total' => $total]);
    }

    // Edit Budget Head with all items
    public function editBudgetHead($bhId)
    {
        $head = BudgetHead::with('items')->findOrFail($bhId);
        return response()->json($head);
    }

    // Update Budget Head with all items
    public function updateBudgetHead(Request $request, $bhId)
    {
        $validator = \Validator::make($request->all(), [
            'dept_id' => 'required|exists:DEPARTMENTS,DEPT_ID',
            'year_id' => 'required|integer|exists:academic_years,ay_id',
            'head_title' => 'required|string|max:150',
            'bh_cat_id' => 'required|integer',
            'remarks' => 'nullable|string|max:2000',
            'items' => 'required|array|min:1',
            'items.*.item_title' => 'required|string|max:1000',
            'items.*.item_brand' => 'nullable|string|max:1000',
            'items.*.item_model' => 'nullable|string|max:1000',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.rate' => 'required|numeric|min:0',
            'items.*.budget_type' => 'required|in:budgeted,unbudgeted,reappropriated',
            'items.*.purchase_mode' => 'required|in:purchase,rent',
            'items.*.remarks' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            // Transform error messages to be user-friendly
            $errors = $validator->errors();
            $transformedErrors = new \Illuminate\Support\MessageBag();

            foreach ($errors->messages() as $key => $messages) {
                if (preg_match('/^items\.(\d+)\.(.+)$/', $key, $matches)) {
                    $index = $matches[1];
                    $field = $matches[2];
                    $itemNumber = $index + 1;

                    foreach ($messages as $message) {
                        // Create user-friendly message
                        $validationMsg = $this->getValidationMessage($message, $field);
                        $newMessage = "#{$itemNumber}: {$validationMsg}";
                        $transformedErrors->add($key, $newMessage);
                    }
                } else {
                    // Keep non-item errors as-is
                    foreach ($messages as $message) {
                        $transformedErrors->add($key, $message);
                    }
                }
            }

            return response()->json([
                'errors' => $transformedErrors
            ], 422);
        }

        $validated = $validator->validated();

        DB::beginTransaction();
        try {
            $head = BudgetHead::findOrFail($bhId);

            // Update budget head
            $head->update([
                'head_title' => $validated['head_title'],
                'remarks' => $validated['remarks'] ?? null,
                'ay_id' => $validated['year_id']
            ]);

            // Delete old items
            BudgetItem::where('bh_id', $bhId)->delete();

            // Add new items
            foreach ($validated['items'] as $item) {
                BudgetItem::create([
                    'bh_id' => $head->bh_id,
                    'bh_cat_id' => $validated['bh_cat_id'],
                    'item_title' => $item['item_title'],
                    'brand' => $item['item_brand'] ?? null,
                    'model' => $item['item_model'] ?? null,
                    'quantity' => $item['quantity'],
                    'rate' => $item['rate'],
                    'budget_type' => $item['budget_type'],
                    'purchase_mode' => $item['purchase_mode'],
                    'remarks' => $item['remarks'] ?? null,
                    'ay_id' => $validated['year_id'],
                    'entry_username' => auth()->user()->username,
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Budget Head updated successfully!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 422);
        }
    }

    // Get budget cards data (for card-based view)
    public function getBudgetCardsData(Request $request)
    {
        // dd('123');
        $deptId = (int) auth()->user()->dept_id;

        if ($deptId <= 0) {
            return response()->json(['budgets' => []]);
        }

        // Get budget heads with items for the logged-in user's department only
        $query = BudgetHead::with(['items', 'academicYear'])
            ->where('dept_id', $deptId);

                // dd($query->toSql());
        // Apply filters
        if ($request->academic_year) {
            $query->where('ay_id', $request->academic_year);
        }

        if ($request->budget_head) {
            $query->whereHas('items', function($q) use ($request) {
                $q->where('bh_cat_id', $request->budget_head);
            });
        }

        $budgetHeads = $query->orderBy('bh_id', 'desc')->get();

        $budgets = [];

        foreach ($budgetHeads as $head) {
            // Filter items if budget_head filter is applied
            $items = $head->items;
            if ($request->budget_head) {
                $items = $items->where('bh_cat_id', $request->budget_head);
            }

            if ($items->isEmpty()) {
                continue;
            }

            $budgets[] = [
                'bh_id' => $head->bh_id,
                'head_title' => $head->head_title,
                'academic_year' => $head->budget_year,
                'items' => $items->map(function($item) {
                    return [
                        'item_id' => $item->item_id,
                        'item_title' => $item->item_title,
                        'brand' => $item->brand,
                        'model' => $item->model,
                        'quantity' => $item->quantity,
                        'rate' => $item->rate,
                        'budget_type' => $item->budget_type,
                        'purchase_mode' => $item->purchase_mode ?? 'purchase',
                        'created_by' => $item->entry_username ?? 'N/A'
                    ];
                })->values()->all()
            ];
        }

        return response()->json(['budgets' => $budgets]);
    }

    // Get grouped budget items (Budget Head + Items hierarchical)
    public function getBudgetItemsGrouped(Request $request)
    {
        $userDeptIds = $this->getDepartments()->pluck('dept_id')->toArray();

        // Get budget heads with items
        $query = BudgetHead::with(['items', 'academicYear'])
            ->whereIn('dept_id', $userDeptIds);

        // Apply filters
        if ($request->academic_year) {
            $query->where('ay_id', $request->academic_year);
        }

        if ($request->budget_head) {
            $query->whereHas('items', function($q) use ($request) {
                $q->where('bh_cat_id', $request->budget_head);
            });
        }

        $budgetHeads = $query->orderBy('bh_id', 'desc')->get();

        $data = [];

        foreach ($budgetHeads as $head) {
            // Filter items if budget_head filter is applied
            $items = $head->items;
            if ($request->budget_head) {
                $items = $items->where('bh_cat_id', $request->budget_head);
            }

            if ($items->isEmpty()) {
                continue;
            }

            // Calculate total for this head
            $headTotal = $items->sum(function($item) {
                return $item->quantity * $item->rate;
            });

            // Add Budget Head Row
            $data[] = [
                'type' => 'budget_head',
                'bh_id' => $head->bh_id,
                'academic_year' => $head->budget_year,
                'title' => $head->head_title,
                'brand' => '',
                'model' => '',
                'quantity' => $items->sum('quantity'),
                'rate' => '',
                'amount' => 'Rs. ' . number_format($headTotal, 2),
                'budget_type' => '',
                'purchase_mode' => '',
                'created_by' => '',
                'items_count' => $items->count(),
                'actions' => '
                    <button class="btn btn-sm btn-warning" onclick="editBudgetHead('.$head->bh_id.')" title="Edit Budget Head">
                        <i class="fa fa-edit"></i> Edit Head
                    </button>
                '
            ];

            // Add Item Rows
            foreach ($items as $item) {
                $data[] = [
                    'type' => 'item',
                    'item_id' => $item->item_id,
                    'bh_id' => $head->bh_id,
                    'academic_year' => '',
                    'title' => $item->item_title,
                    'brand' => $item->brand ?? '-',
                    'model' => $item->model ?? '-',
                    'quantity' => $item->quantity,
                    'rate' => 'Rs. ' . number_format($item->rate, 2),
                    'amount' => 'Rs. ' . number_format($item->quantity * $item->rate, 2),
                    'budget_type' => '<span class="badge badge-'.($item->budget_type == 'budgeted' ? 'success' : 'warning').'">'.ucfirst($item->budget_type).'</span>',
                    'purchase_mode' => '<span class="badge badge-'.($item->purchase_mode == 'purchase' ? 'primary' : 'info').'">'.ucfirst($item->purchase_mode).'</span>',
                    'created_by' => $item->entry_username ?? 'N/A',
                    'items_count' => 0,
                    'actions' => '
                        <button class="btn btn-sm btn-primary" onclick="editBudget('.$item->item_id.')" title="Edit Item">
                            <i class="fa fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="deleteBudget('.$item->item_id.')" title="Delete Item">
                            <i class="fa fa-trash"></i>
                        </button>
                    '
                ];
            }
        }

        return response()->json(['data' => $data]);
    }

    /**
     * Get all items for a specific budget head
     * Used in reappropriation modal to populate dropdown
     *
     * @param int $bhCatId Budget Head Category ID
     * @return \Illuminate\Http\JsonResponse
     */
    public function getItemsByBudgetHead($bhId)
    {
        try {
            $bhId = (int) $bhId;
            $rawExcludeIds = request()->input('exclude_item_ids', []);
            $excludeIds = [];
            foreach ((array) $rawExcludeIds as $rawId) {
                if (is_numeric($rawId)) {
                    $excludeIds[] = (int) $rawId;
                }
            }
            $excludeIds = array_values(array_unique($excludeIds));

            $bindings = [$bhId];
            $sql = "
                SELECT
                    ITEM_ID AS id,
                    ITEM_TITLE AS item_title,
                    QUANTITY AS quantity,
                    RATE AS rate,
                    BH_ID AS bh_id,
                    (NVL(QUANTITY, 0) * NVL(RATE, 0)) AS amount
                FROM SP_BUDGET_ITEMS
                WHERE BH_ID = ?
            ";

            if (!empty($excludeIds)) {
                $placeholders = implode(',', array_fill(0, count($excludeIds), '?'));
                $sql .= " AND ITEM_ID NOT IN ($placeholders)";
                $bindings = array_merge($bindings, $excludeIds);
            }

            $sql .= " ORDER BY ITEM_TITLE";

            $rows = DB::select($sql, $bindings);
            $items = [];
            foreach ($rows as $row) {
                $item = [];
                foreach ((array) $row as $key => $value) {
                    $item[strtolower($key)] = $value;
                }
                $items[] = $item;
            }

            return response()->json($items);
        } catch (\Throwable $e) {
            Log::error('getItemsByBudgetHead failed', [
                'bh_id' => $bhId,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Failed to load items: ' . $e->getMessage(),
            ], 500);
        }
    }



/**
 * Load dynamic content for the cross-head re-appropriation modal.
 */



public function loadReappropriateModal(Request $request)
{
    $rawIds = $request->input('item_ids', []);
    $itemIds = [];
    foreach ((array) $rawIds as $rawId) {
        if (is_numeric($rawId)) {
            $itemIds[] = (int) $rawId;
        }
    }
    $itemIds = array_values(array_unique($itemIds));

    if (empty($itemIds)) {
        return response()->json(['message' => 'Please select at least one budget item.'], 422);
    }

    $items = BudgetItem::with(['budgetHead.department'])
        ->whereIn('item_id', $itemIds)
        ->get();

    if ($items->isEmpty()) {
        return response()->json(['message' => 'Selected budget items could not be found.'], 404);
    }

    $userDeptIds = $this->getDepartments()->pluck('dept_id')->filter()->map(function ($id) {
        return (int) $id;
    })->values()->all();

    $ayIds = $items->map(function ($item) {
        return optional($item->budgetHead)->ay_id;
    })->filter()->unique()->values()->all();

    // Target = all budget heads user can access (same depts / academic year as sources)
    $targetBudgetHeads = collect();
    if (!empty($userDeptIds)) {
        $targetBudgetHeads = BudgetHead::with('department')
            ->whereIn('dept_id', $userDeptIds)
            ->when(!empty($ayIds), function ($query) use ($ayIds) {
                $query->whereIn('ay_id', $ayIds);
            })
            ->orderBy('head_title')
            ->get();

        // Fallback: if AY filter yields nothing, still show dept heads
        if ($targetBudgetHeads->isEmpty()) {
            $targetBudgetHeads = BudgetHead::with('department')
                ->whereIn('dept_id', $userDeptIds)
                ->orderBy('head_title')
                ->get();
        }
    }

    $orderedItems = $items->sortBy(function ($item) use ($itemIds) {
        $index = array_search($item->item_id, $itemIds, true);
        return $index === false ? PHP_INT_MAX : $index;
    })->values();

    // Preload target-head items so dropdown works without a fragile AJAX round-trip
    $targetHeadIds = $targetBudgetHeads->map(function ($head) {
        return (int) ($head->bh_id ?? $head->BH_ID ?? 0);
    })->filter()->unique()->values()->all();

    $itemsByHead = [];
    if (!empty($targetHeadIds)) {
        $placeholders = implode(',', array_fill(0, count($targetHeadIds), '?'));
        $targetRows = DB::select("
            SELECT
                ITEM_ID AS id,
                ITEM_TITLE AS item_title,
                QUANTITY AS quantity,
                RATE AS rate,
                BH_ID AS bh_id,
                (NVL(QUANTITY, 0) * NVL(RATE, 0)) AS amount
            FROM SP_BUDGET_ITEMS
            WHERE BH_ID IN ($placeholders)
            ORDER BY ITEM_TITLE
        ", $targetHeadIds);

        foreach ($targetRows as $row) {
            $normalized = [];
            foreach ((array) $row as $key => $value) {
                $normalized[strtolower($key)] = $value;
            }

            $headKey = (string) ($normalized['bh_id'] ?? '');
            $itemId = $normalized['id'] ?? null;
            if ($headKey === '' || $itemId === null || $itemId === '') {
                continue;
            }

            $qty = (float) ($normalized['quantity'] ?? 0);
            $rate = (float) ($normalized['rate'] ?? 0);
            $itemsByHead[$headKey][] = [
                'id' => (int) $itemId,
                'item_title' => $normalized['item_title'] ?? '-',
                'quantity' => $qty,
                'rate' => $rate,
                'bh_id' => (int) $headKey,
                'amount' => isset($normalized['amount']) ? (float) $normalized['amount'] : ($qty * $rate),
            ];
        }
    }

    $html = view('store_procurement.budget.partials.reappropriate_modal_content', [
        'sourceItems' => $orderedItems,
        'targetBudgetHeads' => $targetBudgetHeads,
        'itemsByHead' => $itemsByHead,
    ])->render();

    return response()->json(['html' => $html]);
}

/**
 * Load budget items by budget head title (used by reappropriation "existing item" dropdown).
 */
public function getItemsByBudgetHeadTitle(Request $request)
{
    $headTitle = trim((string) $request->input('head_title', ''));
    if ($headTitle === '') {
        return response()->json([]);
    }

    $userDeptIds = $this->getDepartments()->pluck('dept_id')->filter()->map(function ($id) {
        return (int) $id;
    })->values()->all();

    $rawExcludeIds = $request->input('exclude_item_ids', []);
    $excludeIds = [];
    foreach ((array) $rawExcludeIds as $rawId) {
        if (is_numeric($rawId)) {
            $excludeIds[] = (int) $rawId;
        }
    }
    $excludeIds = array_values(array_unique($excludeIds));

    $headIds = BudgetHead::query()
        ->when(!empty($userDeptIds), function ($query) use ($userDeptIds) {
            $query->whereIn('dept_id', $userDeptIds);
        })
        ->whereRaw('LOWER(TRIM(head_title)) = ?', [strtolower($headTitle)])
        ->pluck('bh_id')
        ->all();

    if (empty($headIds)) {
        return response()->json([]);
    }

    $itemsQuery = BudgetItem::whereIn('bh_id', $headIds)
        ->select(
            'item_id as id',
            'item_title',
            'quantity',
            'rate',
            'bh_id',
            DB::raw('(quantity * rate) as amount')
        );

    if (!empty($excludeIds)) {
        $itemsQuery->whereNotIn('item_id', $excludeIds);
    }

    return response()->json($itemsQuery->orderBy('item_title')->get());
}

public function getItemsByCategory($categoryId)
{
    $rawExcludeIds = request()->input('exclude_item_ids', []);
    $excludeIds = [];
    foreach ((array) $rawExcludeIds as $rawId) {
        if (is_numeric($rawId)) {
            $excludeIds[] = (int) $rawId;
        }
    }
    $excludeIds = array_values(array_unique($excludeIds));

    $itemsQuery = BudgetItem::where('bh_cat_id', $categoryId)
        ->select(
            'item_id as id',
            'item_title',
            'quantity',
            'rate',
            'bh_id',
            DB::raw('COALESCE(REAPPROPRIATION_APPROVAL, reappropriation_approval) as reappropriation_approval'),
            DB::raw('COALESCE(REAPPROPRIATION_APPROVAL_REMARKS, reappropriation_approval_remarks) as reappropriation_approval_remarks'),
            'send_reappropriation_approval_reapprop',
            DB::raw('COALESCE(REQUESTED_BUDGET_TYPE, requested_budget_type) as requested_budget_type'),
            DB::raw('COALESCE(REASON_FOR_APPROVAL, reason_for_approval) as reason_for_approval'),
            DB::raw('COALESCE(VC_BUDGET_UPDATE_FLAG, vc_budget_update_flag) as vc_budget_update_flag'),
            DB::raw('(quantity * rate) as amount')
        )
        ->orderBy('item_title');

    if (!empty($excludeIds)) {
        $itemsQuery->whereNotIn('item_id', $excludeIds);
    }

    $items = $itemsQuery->get();

    return response()->json($items);
}

}
