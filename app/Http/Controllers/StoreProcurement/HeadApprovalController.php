<?php
namespace App\Http\Controllers\StoreProcurement;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StoreProcurement\VendorCategory;
use DB;
use App\Models\StoreProcurement\Sp_vendor;
use App\Models\StoreProcurement\QuotationRequest;

class HeadApprovalController extends Controller
{
    public function index(){
        // @dd("working");
        $user = auth()->user();
        // dd($user->dept_id);

        $employeeId = trim((string) ($user->employee_id ?? $user->EMPLOYEE_ID ?? ''));
        $qrIds = $employeeId === '' ? [] : $this->headApprovalRequestIds($employeeId);

        $base = QuotationRequest::with(['vendorCategory', 'budgetItem', 'budgetHead', 'requestDepartment', 'storeBudgetItem', 'storeBudgetHead'])
            ->whereIn('qr_id', $qrIds === [] ? [-1] : $qrIds);

        $pendingRequests = (clone $base)
            ->where(function ($query) {
                $query->whereNull('department_head_approval')
                    ->orWhereRaw("LOWER(TRIM(department_head_approval)) IN ('pending', 'no')");
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $approvedRequests = (clone $base)
            ->whereRaw("LOWER(TRIM(department_head_approval)) = 'approved'")
            ->orderBy('updated_at', 'desc')
            ->get();

        $rejectedRequests = (clone $base)
            ->whereRaw("LOWER(TRIM(department_head_approval)) = 'rejected'")
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('store_procurement.head_approvals.index', compact('pendingRequests', 'approvedRequests', 'rejectedRequests'));
    }

    /**
     * Requests whose budget head department has this employee as APPROVAL_USER_ID.
     * SP_QUOTATION_REQUEST -> SP_BUDGET_ITEMS.bh_id -> SP_BUDGET_HEADS.dept_id -> SP_APPROVAL_MASTERS.
     */
    private function headApprovalRequestIds(string $employeeId): array
    {
        $rows = DB::select("
            SELECT DISTINCT qr.QR_ID AS qr_id
            FROM SP_QUOTATION_REQUEST qr
            LEFT JOIN SP_BUDGET_ITEMS bi ON bi.ITEM_ID = qr.ITEM_ID
            INNER JOIN SP_BUDGET_HEADS bh ON bh.BH_ID = NVL(qr.BH_ID, bi.BH_ID)
            INNER JOIN SP_APPROVAL_MASTERS sam ON sam.DEPT_ID = bh.DEPT_ID
            WHERE TO_CHAR(sam.APPROVAL_USER_ID) = ?
        ", [$employeeId]);

        $ids = [];
        foreach ($rows as $row) {
            $id = $row->qr_id ?? $row->QR_ID ?? null;
            if ($id) {
                $ids[] = (int) $id;
            }
        }

        return $ids;
    }


    public function approve(Request $request)
    {

        $request->validate([
            'qr_id' => 'required|exists:sp_quotation_request,qr_id',
            'head_approval_remarks' => 'required|string|min:5|max:2000'
        ], [
            'head_approval_remarks.required' => 'Approval remarks are required',
            'head_approval_remarks.min' => 'Remarks must be at least 5 characters',
            'head_approval_remarks.max' => 'Remarks cannot exceed 2000 characters'
        ]);

        $id = $request->qr_id;
        $quotationRequest = QuotationRequest::findOrFail($id);
        // dd($quotationRequest->cat_id);

       $aproval_required = DB::table('sp_vendor_category')
    ->where('CATEGORY_ID', $quotationRequest->cat_id)
    ->value('Q_PARENT_DEPT_REQUIRED');
        // dd($aproval_required );

        // Check if already processed
        if ($quotationRequest->department_head_approval === 'approved') {
            return response()->json(['message' => 'This request is already approved'], 400);
        }
   if($aproval_required != 1){
        $quotationRequest->update([
            'department_head_approval' => 'approved',
            'visibility_to_vendors' => 'yes',
            'head_approval_remarks' => $request->head_approval_remarks,
        ]);
        }
        else {
            $quotationRequest->update([
            'department_head_approval' => 'approved',
            'head_approval_remarks' => $request->head_approval_remarks,
             ]);
            }



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
