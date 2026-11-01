<?php
namespace App\Http\Controllers\StoreProcurement;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StoreProcurement\VendorCategory;
use DB;
use App\Models\StoreProcurement\Sp_vendor;
use App\Models\StoreProcurement\QuotationRequest;
use Illuminate\Support\Facades\Validator; // ← Make sure this is included

class ParentDeptApprovController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $employeeId = $this->resolveEmployeeId($user);
        $allowed = $this->parentApproverCategories($employeeId);

        if ($employeeId === '' || empty($allowed['category_ids'])) {
            return view('store_procurement.parent_dept_approval.index', [
                'pending' => [],
                'reviewed' => [],
            ]);
        }

        $rows = $this->lowercaseRows(DB::select(
            $this->parentReviewSql(count($allowed['category_ids'])),
            $allowed['category_ids']
        ));

        $pending = [];
        $reviewed = [];
        foreach ($rows as $row) {
            $flag = strtoupper(trim((string) ($row->parent_dept_reviewed ?? '')));
            if ($flag === 'YES') {
                $reviewed[] = $row;
            } else {
                $pending[] = $row;
            }
        }

        return view('store_procurement.parent_dept_approval.index', compact('pending', 'reviewed'));
    }

    private function resolveEmployeeId($user): string
    {
        if (!$user) {
            return '';
        }

        foreach (['employee_id', 'EMPLOYEE_ID', 'emp_id', 'EMP_ID'] as $key) {
            $val = trim((string) ($user->{$key} ?? ''));
            if ($val !== '') {
                return $val;
            }
        }

        $username = trim((string) ($user->username ?? $user->EMP_AD_USERNAME ?? ''));
        if ($username === '') {
            return '';
        }

        $row = DB::selectOne(
            "SELECT EMPLOYEE_ID AS employee_id FROM EMP_EMPLOYEES WHERE LOWER(EMP_AD_USERNAME) = LOWER(?) AND ROWNUM = 1",
            [$username]
        );

        return trim((string) (optional($row)->employee_id ?? ''));
    }

    /**
     * Categories where logged-in employee is a parent-dept reviewer.
     * Q_PARENT_DEPT_REQUIRED is NUMBER — never bind Y/YES (ORA-01722).
     */
    private function parentApproverCategories(string $employeeId): array
    {
        $wanted = trim($employeeId);
        if ($wanted === '') {
            return ['category_ids' => [], 'account_ids' => []];
        }

        $categories = DB::select("
            SELECT
                CATEGORY_ID AS category_id,
                Q_PARENT_DEPT_USERS AS q_parent_dept_users,
                PINNACLE_ACCOUNT_ID AS pinnacle_account_id
            FROM SP_VENDOR_CATEGORY
            WHERE Q_PARENT_DEPT_REQUIRED = 1
        ");

        $categoryIds = [];
        $accountIds = [];
        foreach ($this->lowercaseRows($categories) as $category) {
            $users = array_filter(array_map('trim', explode(',', (string) ($category->q_parent_dept_users ?? ''))));
            $matched = false;
            foreach ($users as $userId) {
                if ($userId === $wanted || (ctype_digit($userId) && ctype_digit($wanted) && (int) $userId === (int) $wanted)) {
                    $matched = true;
                    break;
                }
            }
            if (!$matched) {
                continue;
            }
            $categoryIds[] = (int) $category->category_id;
            $account = trim((string) ($category->pinnacle_account_id ?? ''));
            if ($account !== '') {
                $accountIds[] = $account;
            }
        }

        return [
            'category_ids' => array_values(array_unique($categoryIds)),
            'account_ids' => array_values(array_unique($accountIds)),
        ];
    }

    /**
     * Real relation: SP_BUDGET_HEADS.ACCOUNT_ID = SP_VENDOR_CATEGORY.PINNACLE_ACCOUNT_ID
     * Item fields come from SP_BUDGET_ITEMS (QR.MODEL_NO does not exist — use bi.MODEL).
     */
    private function parentReviewSql(int $categoryCount): string
    {
        $placeholders = implode(',', array_fill(0, max($categoryCount, 1), '?'));

        return "
            SELECT
                qr.QR_ID AS qr_id,
                bi.ITEM_TITLE AS item_title,
                bi.BRAND AS brand,
                bi.MODEL AS model,
                bi.QUANTITY AS quantity,
                bi.RATE AS rate,
                bh.HEAD_TITLE AS head_title,
                vc.CATEGORY_NAME AS category_name,
                NVL(d.DEPT_NAME, bi.ENTRY_USERNAME) AS entry_usrname,
                bi.BUDGET_TYPE AS budget_type,
                qr.PARENT_DEPARTMENT_REMARKS AS parent_department_remarks,
                qr.SPECIFICATIONS_PRNT_DEPT AS specifications_prnt_dept,
                qr.PARENT_DEPT_REVIEWED AS parent_dept_reviewed
            FROM SP_QUOTATION_REQUEST qr
            INNER JOIN SP_BUDGET_ITEMS bi ON bi.ITEM_ID = qr.ITEM_ID
            INNER JOIN SP_BUDGET_HEADS bh ON bh.BH_ID = NVL(qr.BH_ID, bi.BH_ID)
            INNER JOIN SP_VENDOR_CATEGORY vc
                ON TRIM(vc.PINNACLE_ACCOUNT_ID) = TRIM(bh.ACCOUNT_ID)
               AND vc.CATEGORY_ID IN ($placeholders)
            LEFT JOIN DEPARTMENTS d ON d.DEPT_ID = qr.REQUEST_DEPARTMENT_ID
            WHERE (
                    UPPER(TRIM(NVL(qr.DEPARTMENT_HEAD_APPROVAL, 'PENDING'))) = 'APPROVED'
                    OR vc.Q_HEAD_APPROVAL_REQUIRED IS NULL
                    OR TRIM(TO_CHAR(vc.Q_HEAD_APPROVAL_REQUIRED)) = '0'
              )
              AND (
                    qr.PARENT_DEPT_REVIEWED IS NULL
                    OR UPPER(TRIM(qr.PARENT_DEPT_REVIEWED)) IN ('NO', 'YES')
              )
            ORDER BY qr.QR_ID DESC
        ";
    }

    public function itemRecieved(){
        $user = auth()->user();
        $employee_id = (string) ($user->employee_id ?? $user->EMPLOYEE_ID ?? '');

        $sql = "SELECT
                    ai.ASSET_ITEM_ID AS asset_item_id,
                    ai.ASSET_ID AS asset_id,
                    ai.SERIAL_NUMBER AS serial_number,
                    ai.UNIQUE_ASSET_CODE AS unique_asset_code,
                    a.PO_ID AS po_id,
                    a.WARRANTY_PERIOD AS warranty_period,
                    a.NAME AS name,
                    a.ITEM_TITLE AS item_title
                FROM SP_ASSET_ITEMS ai
                LEFT JOIN SP_ASSETS a ON ai.ASSET_ID = a.ASSET_ID
                WHERE ai.ASSIGN_TO = ? AND ai.ALLOCATION_STATUS = 'Request Sent'";
        $resSql = $this->fillMissingSerials($this->lowercaseRows(DB::select($sql, [$employee_id])));

        $sqlrecieved = "SELECT
                    ai.ASSET_ITEM_ID AS asset_item_id,
                    ai.ASSET_ID AS asset_id,
                    ai.SERIAL_NUMBER AS serial_number,
                    ai.UNIQUE_ASSET_CODE AS unique_asset_code,
                    a.PO_ID AS po_id,
                    a.WARRANTY_PERIOD AS warranty_period,
                    a.NAME AS name,
                    a.ITEM_TITLE AS item_title
                FROM SP_ASSET_ITEMS ai
                LEFT JOIN SP_ASSETS a ON ai.ASSET_ID = a.ASSET_ID
                WHERE ai.ASSIGN_TO = ? AND ai.ALLOCATION_STATUS = 'Allocated'";
        $resSqlsqlrecieved = $this->fillMissingSerials($this->lowercaseRows(DB::select($sqlrecieved, [$employee_id])));

        return view('store_procurement.parent_dept_approval.item_recieved', compact('resSql','resSqlsqlrecieved'));
    }

    private function lowercaseRows(array $rows): array
    {
        $out = [];
        foreach ($rows as $row) {
            $item = [];
            foreach ((array) $row as $key => $value) {
                $item[strtolower($key)] = $value;
            }
            $out[] = (object) $item;
        }
        return $out;
    }

    /**
     * SP_ASSET_ITEMS.SERIAL_NUMBER is often empty. Serials live on
     * SP_GATE_PASS_ITEMS as a comma-separated list for the same PO.
     */
    private function fillMissingSerials(array $rows): array
    {
        $assetIds = [];
        foreach ($rows as $row) {
            if (trim((string) ($row->serial_number ?? '')) === '' && !empty($row->asset_id)) {
                $assetIds[(string) $row->asset_id] = $row->asset_id;
            }
        }
        if ($assetIds === []) {
            return $rows;
        }

        $ids = array_values($assetIds);
        $placeholders = implode(',', array_fill(0, count($ids), '?'));

        $gateRows = $this->lowercaseRows(DB::select("
            SELECT a.ASSET_ID AS asset_id, gpi.SERIAL_NUMBER AS serial_number
            FROM SP_ASSETS a
            INNER JOIN SP_GATE_PASS gp ON gp.PO_ID = a.PO_ID
            INNER JOIN SP_GATE_PASS_ITEMS gpi ON gpi.GP_ID = gp.GP_ID
            WHERE a.ASSET_ID IN ($placeholders)
        ", $ids));

        $serialsByAsset = [];
        foreach ($gateRows as $gate) {
            foreach (array_filter(array_map('trim', explode(',', (string) ($gate->serial_number ?? '')))) as $serial) {
                $serialsByAsset[(string) $gate->asset_id][] = $serial;
            }
        }

        $itemRows = $this->lowercaseRows(DB::select("
            SELECT ASSET_ITEM_ID AS asset_item_id, ASSET_ID AS asset_id
            FROM SP_ASSET_ITEMS
            WHERE ASSET_ID IN ($placeholders)
            ORDER BY ASSET_ITEM_ID
        ", $ids));

        $indexByItem = [];
        $counters = [];
        foreach ($itemRows as $item) {
            $key = (string) $item->asset_id;
            $counters[$key] = $counters[$key] ?? 0;
            $indexByItem[(string) $item->asset_item_id] = $counters[$key];
            $counters[$key]++;
        }

        foreach ($rows as $row) {
            if (trim((string) ($row->serial_number ?? '')) !== '') {
                continue;
            }
            $list = $serialsByAsset[(string) $row->asset_id] ?? [];
            $idx = $indexByItem[(string) ($row->asset_item_id ?? '')] ?? null;
            $serial = ($idx !== null && isset($list[$idx])) ? $list[$idx] : (count($list) === 1 ? $list[0] : '');
            if ($serial === '' && $list !== []) {
                $serial = implode(', ', $list);
            }
            if ($serial === '') {
                continue;
            }
            $row->serial_number = $serial;
            if (!empty($row->asset_item_id) && strpos($serial, ',') === false) {
                DB::table('SP_ASSET_ITEMS')
                    ->where('ASSET_ITEM_ID', $row->asset_item_id)
                    ->whereNull('SERIAL_NUMBER')
                    ->update(['SERIAL_NUMBER' => $serial]);
            }
        }

        return $rows;
    }

    public function resourceAcknow(Request $request){
        // dd($request->all());
        // 1. Validation
        $validator = Validator::make($request->all(), [
            'reciever_remarks' => 'required|string|max:255',
            'asset_item_id' => 'required|integer',
            'sr_no' => 'nullable|string|max:100'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // 2. Update the table
        $update = [
            'reciever_acknowkegment' => $request->reciever_remarks,
            'reciever_acknow_time' => DB::raw('CURRENT_TIMESTAMP'),
            'ALLOCATION_STATUS' => 'Allocated',
        ];
        if ($request->filled('sr_no')) {
            $update['SERIAL_NUMBER'] = $request->sr_no;
        }

        $updated = DB::table('SP_ASSET_ITEMS')
            ->where('ASSET_ITEM_ID', $request->asset_item_id)
            ->update($update);

        // 3. Return JSON
        if ($updated) {
            return response()->json([
                'success' => true,
                'message' => 'Acknowledgment saved successfully.'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'No record updated. Please check asset ID and serial number.'
            ]);
        }
    }

   public function saveRemarks(Request $request, $id)
{
    $request->validate([
        'parent_department_remarks' => 'required|string',
        'specifications_prnt_dept' => 'required|string',
    ]);

    $updated = DB::table('sp_quotation_request')
        ->where('qr_id', $id)
        ->update([
            'parent_department_remarks' => $request->parent_department_remarks,
            'specifications_prnt_dept' => $request->specifications_prnt_dept,
            'visibility_to_vendors' => 'yes',
            'parent_dept_reviewed' => 'YES',
        ]);

    if ($updated) {
        return response()->json(['success' => true, 'message' => 'Remarks added successfully']);
    } else {
        return response()->json(['success' => false, 'message' => 'Failed to update record']);
    }
}




    ///
  public function index34()
    {
        $pending = DB::select("
            SELECT * FROM sp_quotation_request
            JOIN sp_budget_items ON sp_quotation_request.ITEM_ID = sp_budget_items.ITEM_ID
            JOIN sp_vendor_category ON BH_CAT_ID = CATEGORY_ID
            WHERE ENTRY_USRNAME = 'muhammad.hamid'
              AND Q_PARENT_DEPT_REQUIRED = 1
              AND PARENT_DEPT_REVIEWED = 'NO'
        ");

        $reviewed = DB::select("
            SELECT * FROM sp_quotation_request
            JOIN sp_budget_items ON sp_quotation_request.ITEM_ID = sp_budget_items.ITEM_ID
            JOIN sp_vendor_category ON BH_CAT_ID = CATEGORY_ID
            WHERE ENTRY_USRNAME = 'muhammad.hamid'
              AND Q_PARENT_DEPT_REQUIRED = 1
              AND PARENT_DEPT_REVIEWED = 'YES'
        ");

        return view('store_procurement.parent_dept_approval.index', compact('pending', 'reviewed'));
    }








    ////////////
    public function index1()
    {
       $user = auth()->user();
    //    dd($user);

       $user_dept = $user->dept_id;
       $user_school = $user->school_id;
       $user_name = $user->username;


// dd( $user_dept);
        // $approvalUserIds = DB::table('sp_approval_masters')
        //     ->select('APPROVAL_USER_ID')
        //     ->where('SCHOOL_ID', $user_school)
        //     ->first();
        if($user_school == 11){
             $approvalUser = DB::table('sp_approval_masters as sam')
            ->join('emp_employees as ee', 'sam.APPROVAL_USER_ID', '=', 'ee.EMPLOYEE_ID')
            ->select( 'ee.EMP_AD_USERNAME')
            ->where('sam.DEPT_ID', $user_dept)
            ->first();


        }
        else {
            $approvalUser = DB::table('sp_approval_masters as sam')
            ->join('emp_employees as ee', 'sam.APPROVAL_USER_ID', '=', 'ee.EMPLOYEE_ID')
            ->select( 'ee.EMP_AD_USERNAME')
            ->where('sam.SCHOOL_ID', $user_school)
            ->first();

        }


        // dd($user_dept = $user->dept_id );

if ($approvalUser && $approvalUser->emp_ad_username == $user_name) {
    $pendingQuery = QuotationRequest::with(['vendorCategory', 'budgetItem', 'budgetHead', 'requestDepartment'])
        ->where(function($query) {
            $query->whereNull('department_head_approval')
                  ->orWhere('department_head_approval', 'pending');
        })
        ->where('request_department_id', $user_dept);




        $approvedQuery = QuotationRequest::with(['vendorCategory', 'budgetItem', 'budgetHead', 'requestDepartment'])
            ->where('department_head_approval', 'approved')
            ->where('request_department_id', $user_dept);
            // ->where('parent_department_id', $user_dept);
            ;

        $rejectedQuery = QuotationRequest::with(['vendorCategory', 'budgetItem', 'budgetHead', 'requestDepartment'])
            ->where('department_head_approval', 'rejected')
            ->where('request_department_id', $user_dept);
            // ->where('parent_department_id', $user_dept);
            ;

        // Pagination (lazy loading) with distinct page names per tab
        $pendingRequests = $pendingQuery
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'pending_page');

        $approvedRequests = $approvedQuery
            ->orderBy('updated_at', 'desc')
            ->paginate(10, ['*'], 'approved_page');

        $rejectedRequests = $rejectedQuery
            ->orderBy('updated_at', 'desc')
            ->paginate(10, ['*'], 'rejected_page');

        return view('store_procurement.parent_dept_approval.index', compact('pendingRequests', 'approvedRequests', 'rejectedRequests'));
        } else {
             abort(403, 'You do not have permission to access Store Procurement module.');
    // Optional: handle case where no approval user found or doesn't match
    // $pendingQuery = QuotationRequest::query(); // or leave empty
}
    }

    public function approve(Request $request)
    {
        $request->validate([
            'qr_id' => 'required|exists:sp_quotation_request,qr_id',
            'head_approval_remarks' => 'required|string|min:5|max:2000'
        ]
        );

        $id = $request->qr_id;
        $quotationRequest = QuotationRequest::findOrFail($id);

        // Check if already processed
        if ($quotationRequest->department_head_approval === 'approved') {
            return response()->json(['message' => 'This request is already approved'], 400);
        }

        $quotationRequest->update([
            'department_head_approval' => 'approved',
            'visibility_to_vendors' => 'yes',
            'head_approval_remarks' => $request->head_approval_remarks,
        ]);

        return response()->json(['message' => 'Request approved successfully']);
    }

    public function reject(Request $request)
    {
        $request->validate([
            'qr_id' => 'required|exists:sp_quotation_request,qr_id',
            'head_approval_remarks' => 'required|string|min:5|max:2000'
        ], [
            'head_approval_remarks.required' => 'Rejection remarks are required',
            'head_approval_remarks.min' => 'Remarks must be at least 5 characters',
            'head_approval_remarks.max' => 'Remarks cannot exceed 2000 characters'
        ]);

        $id = $request->qr_id;
        $quotationRequest = QuotationRequest::findOrFail($id);

        // Check if already processed
        if ($quotationRequest->department_head_approval === 'rejected') {
            return response()->json(['message' => 'This request is already rejected'], 400);
        }

        $quotationRequest->update([
            'department_head_approval' => 'rejected',
            'visibility_to_vendors' => 'no',
            'head_approval_remarks' => $request->head_approval_remarks,
        ]);

        return response()->json(['message' => 'Request rejected successfully']);
    }
}
