<?php

namespace App\Http\Controllers\StoreProcurement;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\StoreProcurement\BudgetHead;
use App\Models\StoreProcurement\BudgetItem;
use App\Models\StoreProcurement\QuotationRequest;
use App\Models\StoreProcurement\VendorCategory;
use App\Models\StoreProcurement\VendorQuotation;
use App\Services\StoreProcurement\SpPermissionService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BudgetItemDetailController extends Controller
{
    public function index()
    {
        $filters = $this->buildFilters();

        $deptFilter = [];
        if (!empty($filters['department_id'])) {
            $deptFilter = [(int) $filters['department_id']];
        } else {
            $deptFilter = array_map('intval', app(SpPermissionService::class)
                ->getAccessibleDepartmentIds(auth()->user()));
        }

        $rawBudgets = BudgetHead::getBudgetsWithItems(
            $deptFilter,
            $filters['academic_year_id'],
            null,
            $filters['category_id']
        );

        $budgets = [];
        $totalItems = 0;

        foreach ($rawBudgets as $budget) {
            $departmentName = '-';
            if (!empty($budget->department) && !empty($budget->department->dept_name)) {
                $departmentName = $budget->department->dept_name;
            } elseif (!empty($budget->dept_name)) {
                $departmentName = $budget->dept_name;
            }

            $parentDepartmentName = '-';
            if (!empty($budget->parent_department) && !empty($budget->parent_department->dept_name)) {
                $parentDepartmentName = $budget->parent_department->dept_name;
            } elseif (!empty($budget->parent_dept_name)) {
                $parentDepartmentName = $budget->parent_dept_name;
            }

            $itemsList = [];
            $rawItems = $budget->items ?? [];

            if ($rawItems instanceof Collection) {
                $rawItems = $rawItems->all();
            } elseif (!is_array($rawItems)) {
                $rawItems = [];
            }

            foreach ($rawItems as $rawItem) {
                $itemsList[] = [
                    'bh_id' => $budget->bh_id ?? null,
                    'dept_id' => $budget->dept_id ?? null,
                    'item_id' => $rawItem->item_id ?? null,
                    'item_title' => $rawItem->item_title ?? '-',
                    'brand' => $rawItem->brand ?? '-',
                    'model' => $rawItem->model ?? '-',
                    'quantity' => $rawItem->quantity ?? 0,
                    'rate' => $rawItem->rate ?? 0,
                    'budget_type' => $rawItem->budget_type ?? '-',
                    'remarks' => $rawItem->remarks ?? ($rawItem->item_remarks ?? null),
                    'department_name' => $departmentName,
                    'parent_department_name' => $parentDepartmentName,
                ];
            }

            if (!empty($itemsList)) {
                $budgets[] = [
                    'bh_id' => $budget->bh_id ?? null,
                    'dept_id' => $budget->dept_id ?? null,
                    'head_title' => $budget->head_title ?? '-',
                    'department_name' => $departmentName,
                    'parent_department_name' => $parentDepartmentName,
                    'items' => $itemsList,
                ];

                $totalItems += count($itemsList);
            }
        }

        $dropdowns = $this->getFilterDropdownData();

        return view('store_procurement.budget_item_details.index', array_merge(
            $dropdowns,
            [
                'budgets' => $budgets,
                'totalItems' => $totalItems,
                'selectedFilters' => $filters,
            ]
        ));
    }

    public function show(Request $request)
    {
        $itemId = $request->get('item_id');
        if (empty($itemId)) {
            return response()->json([
                'view' => '<div class="py-4 text-center text-danger">Item identifier missing.</div>',
            ]);
        }

        $item = BudgetItem::with([
            'budgetHead.department',
            'budgetHead.academicYear',
            'vendorCategory',
            'department',
        ])->find($itemId);

        if (!$item) {
            return response()->json([
                'view' => '<div class="py-4 text-center text-danger">Budget item not found.</div>',
            ]);
        }

        $accessibleDeptIds = array_map(
            'intval',
            app(SpPermissionService::class)->getAccessibleDepartmentIds(auth()->user())
        );
        $itemDeptId = (int) ($item->budgetHead->dept_id ?? $item->entry_dept_id ?? 0);
        if (!empty($accessibleDeptIds) && $itemDeptId > 0 && !in_array($itemDeptId, $accessibleDeptIds, true)) {
            return response()->json([
                'view' => '<div class="py-4 text-center text-danger">You do not have access to this item.</div>',
            ], 403);
        }

        $quotationRequest = QuotationRequest::where('item_id', $itemId)
            ->orderByDesc('qr_id')
            ->first();

        $vendorSubmissions = $quotationRequest
            ? $this->buildVendorSubmissions((int) $quotationRequest->qr_id)
            : [];

        $overview = [
            'item_id' => $item->item_id,
            'item_title' => $item->item_title ?? '-',
            'head_title' => optional($item->budgetHead)->head_title ?? '-',
            'brand' => $item->brand ?? '-',
            'model' => $item->model ?? '-',
            'category' => optional($item->vendorCategory)->category_name ?? '-',
            'quantity' => $item->quantity ?? 0,
            'rate' => (float) ($item->rate ?? 0),
            'total' => ((float) ($item->quantity ?? 0)) * ((float) ($item->rate ?? 0)),
            'department' => optional(optional($item->budgetHead)->department)->dept_name
                ?? optional($item->department)->dept_name
                ?? '-',
            'budget_type' => $item->budget_type ?? '-',
            'remarks' => $item->remarks ?? '-',
            'academic_year' => optional(optional($item->budgetHead)->academicYear)->ay_name ?? '-',
            'has_quotation_request' => (bool) $quotationRequest,
            'qr_id' => optional($quotationRequest)->qr_id,
        ];

        $view = view('store_procurement.budget_item_details.partials.item_detail', [
            'overview' => $overview,
            'vendorSubmissions' => $vendorSubmissions,
            'uid' => 'item-' . $itemId,
        ])->render();

        return response()->json(['view' => $view]);
    }

    private function buildVendorSubmissions(int $qrId): array
    {
        $allQuotations = VendorQuotation::where('quotation_request_id', $qrId)
            ->whereHas('vendor')
            ->with('vendor')
            ->orderBy('vendor_id')
            ->orderByDesc('created_at')
            ->get();

        if ($allQuotations->isEmpty()) {
            return [];
        }

        $submissions = [];

        foreach ($allQuotations->groupBy('vendor_id') as $vendorId => $vendorQuotations) {
            $vendor = $vendorQuotations->first()->vendor;
            $versions = $this->versionsForVendor((int) $vendorId, $qrId, $vendorQuotations);

            if (empty($versions)) {
                continue;
            }

            $latest = $versions[0];
            $submissions[] = [
                'vendor_id' => (int) $vendorId,
                'vendor_name' => $vendor->vendor_name
                    ?? $vendor->VENDOR_NAME
                    ?? ('Vendor #' . $vendorId),
                'latest_rate' => $latest['rate'] ?? 0,
                'latest_price' => $latest['price'] ?? 0,
                'versions' => $versions,
            ];
        }

        return $submissions;
    }

    private function versionsForVendor(int $vendorId, int $qrId, Collection $currentQuotations): array
    {
        $versions = [];

        try {
            $historyVersions = DB::table('SP_VENDOR_QUOTATIONS_HISTORY')
                ->where('VENDOR_ID', $vendorId)
                ->where('QUOTATION_REQUEST_ID', $qrId)
                ->orderByDesc('VERSION_NUMBER')
                ->get();
        } catch (\Throwable $e) {
            Log::warning('Unable to read SP_VENDOR_QUOTATIONS_HISTORY', [
                'qr_id' => $qrId,
                'vendor_id' => $vendorId,
                'error' => $e->getMessage(),
            ]);
            $historyVersions = collect();
        }

        foreach ($historyVersions as $version) {
            $versions[] = $this->mapVersionRow($version);
        }

        if (!empty($versions)) {
            return $versions;
        }

        // Fallback: current quotation rows when history is empty.
        $index = $currentQuotations->count();
        foreach ($currentQuotations as $quotation) {
            $versions[] = $this->mapVersionRow($quotation, $index);
            $index--;
        }

        return $versions;
    }

    private function mapVersionRow($row, ?int $fallbackVersion = null): array
    {
        $versionNumber = $this->rowValue($row, 'version_number')
            ?? $this->rowValue($row, 'qr_version')
            ?? $fallbackVersion
            ?? 1;

        $submittedAt = $this->rowValue($row, 'created_at') ?? $this->rowValue($row, 'updated_at');
        $submittedLabel = '-';
        if (!empty($submittedAt)) {
            try {
                $submittedLabel = Carbon::parse($submittedAt)->format('d-M-Y');
            } catch (\Throwable $e) {
                $submittedLabel = (string) $submittedAt;
            }
        }

        $rate = (float) ($this->rowValue($row, 'rate') ?? 0);
        $price = (float) ($this->rowValue($row, 'price') ?? 0);
        $quantity = $this->rowValue($row, 'quantity')
            ?? $this->rowValue($row, 'quantity_quoted');
        if ($price <= 0 && $rate > 0 && is_numeric($quantity)) {
            $price = $rate * (float) $quantity;
        }

        $advanceRequired = strtolower((string) ($this->rowValue($row, 'advance_required') ?? 'no'));
        $isCurrent = (int) ($this->rowValue($row, 'is_current') ?? 0);
        $attachment = $this->rowValue($row, 'attachment');

        return [
            'version_number' => (int) $versionNumber,
            'is_current' => $isCurrent === 1,
            'item_title' => $this->rowValue($row, 'item_title') ?? '-',
            'brand' => $this->rowValue($row, 'brand') ?? '-',
            'model' => $this->rowValue($row, 'model') ?? '-',
            'quantity' => $quantity,
            'rate' => $rate,
            'price' => $price,
            'currency' => $this->rowValue($row, 'currency') ?? 'PKR',
            'advance_required' => in_array($advanceRequired, ['1', 'yes', 'y', 'true'], true) ? 'Yes' : 'No',
            'advance_percent' => $this->rowValue($row, 'advance_percent'),
            'delivery_time' => $this->rowValue($row, 'delivery_time') ?? '-',
            'warranty' => $this->rowValue($row, 'warranty') ?? '-',
            'specifications' => $this->rowValue($row, 'specifications') ?? '-',
            'remarks' => $this->rowValue($row, 'remarks') ?? '-',
            'attachment' => $attachment,
            'attachment_url' => $this->attachmentUrl($attachment),
            'submitted_at' => $submittedLabel,
            'status_label' => $isCurrent === 1 ? 'Latest' : 'Submitted',
        ];
    }

    private function attachmentUrl($path): ?string
    {
        $path = trim((string) $path);
        if ($path === '') {
            return null;
        }

        if (preg_match('#^https?://#i', $path)) {
            return $path;
        }

        return asset(ltrim($path, '/'));
    }

    private function rowValue($row, string $column)
    {
        if (is_array($row)) {
            foreach ([$column, strtoupper($column), strtolower($column)] as $key) {
                if (array_key_exists($key, $row) && $row[$key] !== null && $row[$key] !== '') {
                    return $row[$key];
                }
            }

            return null;
        }

        if (!is_object($row)) {
            return null;
        }

        foreach ([$column, strtoupper($column), strtolower($column)] as $key) {
            if (isset($row->{$key}) && $row->{$key} !== null && $row->{$key} !== '') {
                return $row->{$key};
            }
        }

        if (method_exists($row, 'getAttribute')) {
            $value = $row->getAttribute($column);
            if ($value !== null && $value !== '') {
                return $value;
            }
        }

        return null;
    }

    private function buildFilters(): array
    {
        $departmentId = request()->get('department');
        $accessibleDeptIds = app(SpPermissionService::class)
            ->getAccessibleDepartmentIds(auth()->user());

        if ($departmentId && !in_array((int) $departmentId, array_map('intval', $accessibleDeptIds), true)) {
            abort(403, 'You do not have access to this department budget.');
        }

        return [
            'academic_year_id' => request()->get('academic_year'),
            'department_id' => $departmentId,
            'category_id' => request()->get('category'),
        ];
    }

    private function getFilterDropdownData(): array
    {
        return [
            'academicYears' => AcademicYear::latest('ay_id')->get(),
            'departments' => app(SpPermissionService::class)->getAccessibleDepartments(auth()->user()),
            'categories' => VendorCategory::orderBy('category_name')->get(),
        ];
    }
}
