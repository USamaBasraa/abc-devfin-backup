<?php

namespace App\Http\Controllers\StoreProcurement;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\StoreProcurement\QuotationRequest;
use Illuminate\Http\Request;

class StoreItemsQuotationProcessController extends Controller
{
    public function create(Request $request)
    {
        $userSchoolId = auth()->user()->school_id;
        $userDeptId = auth()->user()->dept_id;
        $selectedYearId = $request->get('financial_year');

        $academic_years = AcademicYear::latest('ay_id')->get();

        $quotationsQuery = QuotationRequest::with([
                'storeBudgetItem',
                'storeBudgetHead.academicYear',
                'vendorQuotations',
            ])
            ->where('request_type', 'store_items')
            ->when($userSchoolId == 11, function ($query) use ($userDeptId) {
                return $query->where('request_department_id', $userDeptId);
            }, function ($query) use ($userSchoolId) {
                return $query->where('request_school_id', $userSchoolId);
            });

        if (!empty($selectedYearId)) {
            $quotationsQuery->whereHas('storeBudgetHead', function ($q) use ($selectedYearId) {
                $q->where('ay_id', $selectedYearId);
            });
        }

        $quotations = $quotationsQuery->orderBy('created_at', 'desc')->get();

        return view('store_procurement.store_items_quotation_request.quotation_request', compact('quotations', 'academic_years', 'selectedYearId'));
    }

    public function viewVendorQuotations($id)
    {
        $quotation = QuotationRequest::with([
            'vendorCategory',
            'vendorQuotations.vendor',
            'vendorQuotations.purchaseOrder',
            'recommendations.department',
            'recommendations.vendorQuotations.vendor',
            'storeBudgetItem',
            'storeBudgetHead.department',
            'purchaseOrder',
        ])->where('request_type', 'store_items')
          ->findOrFail($id);

        if ($quotation->storeBudgetItem) {
            $quotation->setRelation('budgetItem', $quotation->storeBudgetItem);
        }

        if ($quotation->storeBudgetHead) {
            $quotation->setRelation('budgetHead', $quotation->storeBudgetHead);
        }

        return view('store_procurement.purchase_order.related_vendor_quotations', compact('quotation'));
    }
}
