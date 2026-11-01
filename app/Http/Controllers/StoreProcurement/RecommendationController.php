<?php

namespace App\Http\Controllers\StoreProcurement;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StoreProcurement\QuotationRequest;
use App\Models\StoreProcurement\VendorQuotation;
use App\Models\StoreProcurement\Recommendation;
use Illuminate\Support\Facades\Auth;

class RecommendationController extends Controller
{
    /**
     * Show quotation request details with quotations and recommendations
     */
    public function show($id)
    {
        $quotationRequest = QuotationRequest::with([
            'vendorCategory',
            'vendorQuotations.vendor',
            'recommendations.department'
        ])->findOrFail($id);

        return view('store_procurement.quotation_request.show', compact('quotationRequest'));
    }

    /**
     * Store recommendation for a quotation request
     */
    public function store(Request $request)
    {


        $request->validate([
            'qr_id' => 'required|exists:sp_quotation_request,qr_id',
            'vq_id' => 'required|exists:sp_vendor_quotations,vq_id',
            'recommended_flag' => 'required|in:recommended,not_recommended,conditional',
            'remarks' => 'nullable|string|max:500',
        ]);
        // dd($request->vq_id);

        $user = Auth::user();

        // ensure only one recommendation per department per request
        $existing = Recommendation::where('qr_id', $request->qr_id)
            ->where('department_id', $user->dept_id)
            ->first();

        if ($existing) {
            return response()->json([
                'message' => 'Your department has already submitted a recommendation.'
            ], 422);
        }

        $rec = Recommendation::create([
            'qr_id' => $request->qr_id,
            'department_id' => $user->dept_id,
            'recommended_flag' => $request->recommended_flag,
            'remarks' => $request->remarks,
            'entry_user_id' => $user->id,
            'vq_id' => $request->vq_id,
        ]);

        return response()->json([
            'message' => 'Recommendation submitted successfully!',
            'data' => $rec
        ]);
    }
}
