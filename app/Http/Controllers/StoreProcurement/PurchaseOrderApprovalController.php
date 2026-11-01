<?php
namespace App\Http\Controllers\StoreProcurement;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\StoreProcurement\PurchaseOrder;


class PurchaseOrderApprovalController extends Controller
{
    // public function index()
    // {
    //     $purchaseOrders = PurchaseOrder::all();


    //     return view('store_procurement.purchase_order.approval', compact('purchaseOrders'));
    // }
    public function index()
    {
        $purchaseOrders = PurchaseOrder::with('vendor')->get();

        $pendingOrders = $purchaseOrders->where('approvel_status', 'pending');
        $approvedOrders = $purchaseOrders->where('approvel_status', 'approved');
        $rejectedOrders = $purchaseOrders->where('approvel_status', 'rejected');
        //  dd($pendingOrders);
        return view('store_procurement.purchase_order.approval', compact('pendingOrders', 'approvedOrders', 'rejectedOrders'));
    }
    public function approve(Request $request)
    {
        // dd($request->all());
        // dd($request->id);
        $po = PurchaseOrder::findOrFail($request->po_id);
        // dd($po);
       $check = $po->update([
            'approvel_status' => 'approved',
            'approvel_date' => now()
        ]);
        //    dd($check);
        return response()->json(['message' => 'Purchase Order approved successfully']);
    }

    public function reject(Request $request)
    {
        $request->validate([
            'remarks' => 'required|string|max:255'
        ]);

        $po = PurchaseOrder::findOrFail($request->po_id);
        $po->update([
            'approvel_status' => 'rejected',
            'approvel_date' => now(),
            'reject_remarks' => $request->remarks
        ]);

        return response()->json(['message' => 'Purchase Order rejected successfully']);
    }


}
