<?php

namespace App\Http\Controllers\StoreProcurement;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Grader\Departments;
use App\Models\StoreProcurement\QuotationRequest;
use App\Models\StoreProcurement\Sp_vendor;
use App\Models\StoreProcurement\VendorCategory;
use App\Models\StoreProcurement\VendorEmailLog;
use App\Services\StoreProcurement\StoreItemsRfqPdfService;
use App\Services\StoreProcurement\StoreItemsSchemaService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class StoreItemsQuotationController extends Controller
{
    public function spQuotations()
    {
        $vendors = Sp_vendor::orderBy('vendor_name')->get();
        $departments = Departments::orderBy('dept_name')->get();
        $budgetHeads = VendorCategory::orderBy('category_name')->get();
        $academic_years = AcademicYear::latest('ay_id')->get();
        $requestType = 'store_items';
        $pageTitle = 'Store Items Vendor Quotations';

        $hasStoreItemsSchema = app(StoreItemsSchemaService::class)->ensure();
        $requests = $hasStoreItemsSchema
            ? QuotationRequest::where('department_head_approval', 'approved')
                ->where('visibility_to_vendors', 'yes')
                ->where('request_type', 'store_items')
                ->orderBy('created_at', 'desc')
                ->get()
            : collect();

        foreach ($requests as $request) {
            $request->load(['storeBudgetItem', 'storeBudgetHead.department', 'vendorCategory', 'chatMessages']);
            if ($request->storeBudgetItem) {
                $request->setRelation('budgetItem', $request->storeBudgetItem);
            }
            if ($request->storeBudgetHead) {
                $request->setRelation('budgetHead', $request->storeBudgetHead);
            }
        }

        return view('store_procurement.quotation_request.sp_quotation_submit', compact('vendors', 'departments', 'budgetHeads', 'academic_years', 'requests', 'requestType', 'pageTitle'));
    }

    public function downloadRfqPdf(Request $request, $qrId, StoreItemsRfqPdfService $pdfService)
    {
        $quotationRequest = QuotationRequest::with([
            'storeBudgetItem',
            'storeBudgetHead.department',
            'requestDepartment',
        ])->where('request_type', 'store_items')->findOrFail($qrId);

        $requestedRfqNo = $request->query('rfq_no');
        if ($requestedRfqNo) {
            $decoded = base64_decode($requestedRfqNo, true);
            $requestedRfqNo = $decoded !== false ? $decoded : null;
        }

        $latestEmailLog = VendorEmailLog::where('ve_qr_id', $qrId)
            ->with('vendor')
            ->orderBy('ve_sent_at', 'desc')
            ->first();

        $rfqNo = $requestedRfqNo ?: ($latestEmailLog->ve_rfq_no ?? ('STORE-RFQ-' . $qrId));
        $bidCloseDate = $latestEmailLog
            ? Carbon::parse($latestEmailLog->ve_sent_at)->format('d-M-Y h:i A')
            : '-';

        $pdf = $pdfService->build($quotationRequest, optional($latestEmailLog)->vendor, $rfqNo, $bidCloseDate);

        return response($pdf['content'], 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $pdf['filename'] . '"',
        ]);
    }
}
