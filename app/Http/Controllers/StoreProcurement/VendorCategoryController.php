<?php
namespace App\Http\Controllers\StoreProcurement;

use App\Http\Controllers\Controller;
use App\Support\Oracle\ResolvesSynonymObjects;
use Illuminate\Http\Request;
use App\Models\StoreProcurement\VendorCategory;
use DB;

class VendorCategoryController extends Controller
{
    use ResolvesSynonymObjects;

    private const SYNC_DEFAULT_PARENT_DEPARTMENT_ID = 0;
    private const SYNC_DEFAULT_BUDGET_TYPE = 'CapEx';

    public function index() 
    {
        $this->ensureVendorCategorySyncColumn();

        $departments = DB::table('DEPARTMENTS')
         ->where('DEPT_STATUS', 'E')
            ->select('DEPT_ID as dept_id', 'DEPT_NAME as dept_name')
            ->orderBy('DEPT_NAME')
            ->get();
            
        // Get users/employees list
        $select_list = "prefix, employee_id as id, emp_f_name || ' ' || emp_l_name as full_name, emp_dept_id, dept_name, school_code";

        $sql = "SELECT $select_list FROM EMP_EMPLOYEES
                LEFT JOIN schools ON school_id = emp_school_id
                LEFT JOIN departments ON dept_id = emp_dept_id
                WHERE emp_status = 'E'
                ORDER BY employee_id DESC";

        $users = DB::select($sql);
            
        return view('store_procurement.vendor.vendor_category_create', compact('departments', 'users'));
    }

    public function list()
    {
          $this->ensureVendorCategorySyncColumn();

          $categories = DB::table('SP_VENDOR_CATEGORY as c')
        ->leftJoin('DEPARTMENTS as d', 'c.parent_department_id', '=', 'd.DEPT_ID')
        ->select('c.*', 'd.DEPT_NAME as parent_department_name')
        ->get();
            
       return response()->json($categories);
    }

    // DataTable endpoint
    public function getCategoryData(Request $request)
    {
        $this->ensureVendorCategorySyncColumn();

        try {
            // Get pagination parameters
            $start = $request->input('start', 0);
            $length = $request->input('length', 10);
            $searchValue = $request->input('search.value', '');
            $orderColumn = $request->input('order.0.column', 0);
            $orderDir = $request->input('order.0.dir', 'desc');

            // Build query
            $query = VendorCategory::with(['parentDepartment']);

            // Search
            if (!empty($searchValue)) {
                $query->where(function($q) use ($searchValue) {
                    $q->where('category_name', 'like', "%{$searchValue}%")
                      ->orWhere('category_description', 'like', "%{$searchValue}%")
                      ->orWhere('budget_type', 'like', "%{$searchValue}%");
                });
            }

            // Total records
            $totalRecords = VendorCategory::count();
            $filteredRecords = $query->count();

            // Get data with pagination
            $categories = $query->skip($start)
                                ->take($length)
                                ->orderBy('category_id', $orderDir)
                                ->get();

            // Format data
            $data = [];
            foreach ($categories as $index => $cat) {
                $budgetTypeClass = $cat->budget_type == 'CapEx' ? 'primary' : 'success';
                $budgetTypeLabel = $cat->budget_type ?? 'N/A';

                $headApproval = $cat->q_head_approval_required == 1
                    ? '<span class="badge badge-success"><i class="fa fa-check"></i> Yes</span>'
                    : '<span class="badge badge-secondary"><i class="fa fa-times"></i> No</span>';

                $data[] = [
                    'DT_RowIndex' => $start + $index + 1,
                    'pinnacle_account_id' => $cat->pinnacle_account_id ?? '-',
                    'source' => !empty($cat->pinnacle_account_id)
                        ? '<span class="badge badge-info">Pinnacle</span>'
                        : '<span class="badge badge-secondary">Manual</span>',
                    'category_name' => '<strong>' . e($cat->category_name) . '</strong>',
                    'category_description' => $cat->category_description ?? '-',
                    'parent_department_id' => $cat->parent_department_id,
                    'q_parent_dept_users' => $cat->q_parent_dept_users ?? '',
                    'budget_type' => '<span class="badge badge-' . $budgetTypeClass . '">' . e($budgetTypeLabel) . '</span>',
                    'head_approval' => $headApproval,
                    'category_id' => $cat->category_id,
                ];
            }

            return response()->json([
                'draw' => intval($request->input('draw')),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'draw' => intval($request->input('draw', 1)),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => $e->getMessage()
            ]);
        }
    }

    public function store(Request $request)
    {
        $this->ensureVendorCategorySyncColumn();

        // Comprehensive validation
        $validated = $request->validate([
            'category_name' => 'required|string|max:100|unique:sp_vendor_category,category_name',
            'category_description' => 'nullable|string|max:500',
            'parent_department_id' => 'required|integer',
            'budget_type' => 'required|in:CapEx,OpEx',
            'q_parent_dept_required' => 'nullable|boolean',
            'q_head_approval_required' => 'nullable|boolean',
            'q_parent_dept_users' => 'nullable|array',
            'q_parent_dept_users.*' => 'integer',
            'q_head_approval_users' => 'nullable|array',
            'q_head_approval_users.*' => 'integer',
        ], [
            'category_name.required' => 'Category name is required.',
            'category_name.unique' => 'Category name already exists.',
            'category_name.max' => 'Category name cannot exceed 100 characters.',
            'parent_department_id.required' => 'Relevant executing department is required.',
            'budget_type.required' => 'Budget type is required.',
            'budget_type.in' => 'Invalid budget type selected.',
        ]);

        try {
            // Convert arrays to comma-separated strings
            $parentDeptUsers = $request->has('q_parent_dept_users') && is_array($request->q_parent_dept_users) 
                ? implode(',', $request->q_parent_dept_users) 
                : null;
                
            $headApprovalUsers = $request->has('q_head_approval_users') && is_array($request->q_head_approval_users) 
                ? implode(',', $request->q_head_approval_users) 
                : null;
            
            VendorCategory::create([
                'category_name' => $validated['category_name'],
                'category_description' => $validated['category_description'] ?? null,
                'parent_department_id' => $validated['parent_department_id'],
                'budget_type' => $validated['budget_type'],
                'q_parent_dept_required' => $request->has('q_parent_dept_required') ? 1 : 0,
                'q_head_approval_required' => $request->has('q_head_approval_required') ? 1 : 0,
                'q_parent_dept_users' => $parentDeptUsers,
                'q_head_approval_users' => $headApprovalUsers,
                'entry_usrname' => auth()->user()->username
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Category created successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 422);
        }
    }

    public function edit($id)
    {
        $this->ensureVendorCategorySyncColumn();
        $category = VendorCategory::findOrFail($id);
        return response()->json($category);
    }

    /**
     * Inline row update for Relevant Executing Department + Reviewers.
     */
    public function quickUpdate(Request $request, $id)
    {
        $category = VendorCategory::findOrFail($id);

        if ($request->has('parent_department_id')) {
            $category->parent_department_id = (int) $request->parent_department_id;
        }

        if ($request->has('q_parent_dept_users')) {
            $users = $request->input('q_parent_dept_users', []);
            if (is_string($users)) {
                $users = array_filter(array_map('trim', explode(',', $users)));
            }
            if (!is_array($users)) {
                $users = [];
            }
            $users = array_values(array_filter($users, function ($v) {
                return $v !== null && $v !== '';
            }));
            $category->q_parent_dept_users = !empty($users) ? implode(',', $users) : null;
            $category->q_parent_dept_required = !empty($users) ? 1 : 0;
        }

        $category->save();

        return response()->json([
            'success' => true,
            'message' => 'Updated successfully.',
            'q_parent_dept_required' => $category->q_parent_dept_required,
        ]);
    }

    public function update(Request $request, $id)
    {
        $this->ensureVendorCategorySyncColumn();

        // Comprehensive validation
        $validated = $request->validate([
            'category_name' => 'required|string|max:100|unique:sp_vendor_category,category_name,' . $id . ',category_id',
            'category_description' => 'nullable|string|max:500',
            'parent_department_id' => 'required|integer',
            'budget_type' => 'required|in:CapEx,OpEx',
            'q_parent_dept_required' => 'nullable|boolean',
            'q_head_approval_required' => 'nullable|boolean',
            'q_parent_dept_users' => 'nullable|array',
            'q_parent_dept_users.*' => 'integer',
            'q_head_approval_users' => 'nullable|array',
            'q_head_approval_users.*' => 'integer',
        ], [
            'category_name.required' => 'Category name is required.',
            'category_name.unique' => 'Category name already exists.',
            'parent_department_id.required' => 'Relevant executing department is required.',
            'budget_type.required' => 'Budget type is required.',
        ]);

        try {
        $category = VendorCategory::findOrFail($id);
            
            // Convert arrays to comma-separated strings
            $parentDeptUsers = $request->has('q_parent_dept_users') && is_array($request->q_parent_dept_users) 
                ? implode(',', $request->q_parent_dept_users) 
                : null;
                
            $headApprovalUsers = $request->has('q_head_approval_users') && is_array($request->q_head_approval_users) 
                ? implode(',', $request->q_head_approval_users) 
                : null;
            
            $category->update([
                'category_name' => $validated['category_name'],
                'category_description' => $validated['category_description'] ?? null,
                'parent_department_id' => $validated['parent_department_id'],
                'budget_type' => $validated['budget_type'],
                'q_parent_dept_required' => $request->has('q_parent_dept_required') ? 1 : 0,
                'q_head_approval_required' => $request->has('q_head_approval_required') ? 1 : 0,
                'q_parent_dept_users' => $parentDeptUsers,
                'q_head_approval_users' => $headApprovalUsers,
                'entry_usrname' => (auth()->user()->username)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Category updated successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 422);
        }
    }

    public function destroy($id)
    {
        $this->ensureVendorCategorySyncColumn();
        try {
            $category = VendorCategory::findOrFail($id);
            $category->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Category deleted successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 422);
        }
    }

    public function syncPinnacleChunk(Request $request)
    {
        $this->ensureVendorCategorySyncColumn();

        $records = $request->input('records', []);

        if (!is_array($records)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid category records payload.',
            ], 422);
        }

        $inserted = 0;
        $updated = 0;
        $skipped = 0;

        foreach ($records as $record) {
            $accountId = $this->stringValue($record['Account_ID'] ?? null, 50);
            $accountTitle = $this->stringValue($record['Account_Title'] ?? null, 100);

            if ($accountId === null || $accountTitle === null) {
                $skipped++;
                continue;
            }

            $existing = VendorCategory::where('pinnacle_account_id', $accountId)->first();

            if ($existing) {
                $existing->update([
                    'category_name' => $accountTitle,
                ]);
                $updated++;
                continue;
            }

            VendorCategory::create([
                'pinnacle_account_id' => $accountId,
                'category_name' => $accountTitle,
                'category_description' => null,
                'parent_department_id' => self::SYNC_DEFAULT_PARENT_DEPARTMENT_ID,
                'budget_type' => self::SYNC_DEFAULT_BUDGET_TYPE,
                'q_parent_dept_required' => 0,
                'q_head_approval_required' => 0,
                'q_parent_dept_users' => null,
                'q_head_approval_users' => null,
                'entry_usrname' => auth()->user()->username ?? 'system',
            ]);
            $inserted++;
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Category chunk synced successfully.',
            'summary' => [
                'inserted' => $inserted,
                'updated' => $updated,
                'skipped' => $skipped,
                'processed' => $inserted + $updated + $skipped,
            ],
        ]);
    }

    private function ensureVendorCategorySyncColumn(): void
    {
        // Live: facultyconn → public synonyms → bnucms tables. No DDL from app.
        try {
            if (!$this->tableExists('SP_VENDOR_CATEGORY')) {
                $this->logMissingOracleObject('SP_VENDOR_CATEGORY', 'VendorCategoryController');
                return;
            }

            if (!$this->columnExists('SP_VENDOR_CATEGORY', 'PINNACLE_ACCOUNT_ID')) {
                $this->logMissingOracleObject('SP_VENDOR_CATEGORY.PINNACLE_ACCOUNT_ID', 'VendorCategoryController');
            }
        } catch (\Throwable $e) {
            \Log::warning('ensureVendorCategorySyncColumn check failed', ['error' => $e->getMessage()]);
        }
    }

    private function stringValue($value, int $maxLength): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        return substr($value, 0, $maxLength);
    }
}
