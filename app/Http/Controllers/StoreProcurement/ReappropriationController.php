<?php

namespace App\Http\Controllers\StoreProcurement;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\StoreProcurement\BudgetItem;
use Illuminate\Support\Facades\DB;

class ReappropriationController extends Controller
{
    public function index()
    {
    //     $pendingItems = BudgetItem::where('budget_type', 'reappropriated')
    //         ->where('reappropriation_approval', 'Pending')
    //          ->with('department:id,dept_name')
    //         ->get();

    //     $approvedItems = BudgetItem::where('budget_type', 'reappropriated')
    //         ->where('reappropriation_approval', 'Approved')
    //         ->get();

    //     $declinedItems = BudgetItem::where('budget_type', 'reappropriated')
    //         ->where('reappropriation_approval', 'declined')
    //         ->get();
    //


    // REASON_FOR_APPROVAL
    $user = auth()->user()->username;
    //   dd($user);

if($user != 'moeedyusuf')
    {
        abort(403, 'You do not have permission to access Store Procurement module.');

    }
    else
$pendingItems = DB::table('sp_budget_items')
    ->join('sp_budget_heads', 'sp_budget_items.BH_ID', '=', 'sp_budget_heads.bh_id')
    ->join('departments', 'departments.dept_id', '=', 'sp_budget_heads.dept_id')
    ->join('schools', 'departments.dept_school_id', '=', 'schools.school_id')
    ->select(
        'sp_budget_items.*',
        'sp_budget_heads.*',
        'departments.dept_name',
        'schools.school_name',
        'sp_budget_items.REQUESTED_BUDGET_TYPE as BUDGET_TYPEi'
    )
        ->where(function($query) {
                $query->where('sp_budget_items.REQUESTED_BUDGET_TYPE', 'reappropriated')
                    ->orWhere('sp_budget_items.REQUESTED_BUDGET_TYPE', 'unbudgeted')
                    ->orWhere('sp_budget_items.REQUESTED_BUDGET_TYPE', 'Partial Re-appropriate')
                    ->orWhere('sp_budget_items.REQUESTED_BUDGET_TYPE', 'Partial un-Budgeted')


                    ;
            })
    ->where('sp_budget_items.send_reappropriation_approval_reapprop', 'YES')
    ->whereRaw('LOWER(sp_budget_items.reappropriation_approval) = ?', ['pending'])

    ->get();
    // dd($pendingItems );

$approvedItems = DB::table('sp_budget_items')
    ->join('sp_budget_heads', 'sp_budget_items.BH_ID', '=', 'sp_budget_heads.bh_id')
    ->join('departments', 'departments.dept_id', '=', 'sp_budget_heads.dept_id')
    ->join('schools', 'departments.dept_school_id', '=', 'schools.school_id')
    ->select(
        'sp_budget_items.*',
        'sp_budget_heads.*',
        'departments.dept_name',
        'schools.school_name',
        'sp_budget_items.REQUESTED_BUDGET_TYPE as BUDGET_TYPEi'
    )
        ->where(function($query) {
                $query->where('sp_budget_items.REQUESTED_BUDGET_TYPE', 'reappropriated')
                    ->orWhere('sp_budget_items.REQUESTED_BUDGET_TYPE', 'unbudgeted')
                    ->orWhere('sp_budget_items.REQUESTED_BUDGET_TYPE', 'Partial Re-appropriate')
                    ->orWhere('sp_budget_items.REQUESTED_BUDGET_TYPE', 'Partial un-Budgeted')


                    ;
            })
    ->where('sp_budget_items.send_reappropriation_approval_reapprop', 'YES')
    ->where('sp_budget_items.reappropriation_approval', 'Approved')
    ->get();

$declinedItems = DB::table('sp_budget_items')
    ->join('sp_budget_heads', 'sp_budget_items.BH_ID', '=', 'sp_budget_heads.bh_id')
    ->join('departments', 'departments.dept_id', '=', 'sp_budget_heads.dept_id')
    ->join('schools', 'departments.dept_school_id', '=', 'schools.school_id')
    ->select(
        'sp_budget_items.*',
        'sp_budget_heads.*',
        'departments.dept_name',
        'schools.school_name',
        'sp_budget_items.REQUESTED_BUDGET_TYPE as BUDGET_TYPEi'
    )
    ->where(function($query) {
                $query->where('sp_budget_items.REQUESTED_BUDGET_TYPE', 'reappropriated')
                    ->orWhere('sp_budget_items.REQUESTED_BUDGET_TYPE', 'unbudgeted')
                      ->orWhere('sp_budget_items.REQUESTED_BUDGET_TYPE', 'Partial Re-appropriate')
                    ->orWhere('sp_budget_items.REQUESTED_BUDGET_TYPE', 'Partial un-Budgeted')
                    ;
            })
    ->where('sp_budget_items.send_reappropriation_approval_reapprop', 'YES')
    ->where('sp_budget_items.reappropriation_approval', 'declined')
    ->get();

            // dd($approvedItems);

        return view('store_procurement.reappropriation.approval_list', compact(
            'pendingItems', 'approvedItems', 'declinedItems'
        ));
    }

    // public function approve(Request $request)
    // {
    //     $item = BudgetItem::find($request->item_id);
    //     if (!$item) {
    //         return response()->json(['success' => false, 'message' => 'Item not found.']);
    //     }

    //     $item->reappropriation_approval = 'approved';
    //     $item->save();

    //     return response()->json(['success' => true, 'message' => 'Item approved successfully.']);
    // }
        // Approve an item via AJAX
    public function approve(Request $request)
    {
         $user = auth()->user()->username;
        // dd($request->remarks);
        try {
            $item = BudgetItem::find($request->item_id);

            if (!$item) {
                return response()->json(['success' => false, 'message' => 'Item not found.']);
            }

            // if (strtolower($item->budget_type) !== 'reappropriated') {
            //     return response()->json(['success' => false, 'message' => 'Invalid budget type.']);
            // }

            $item->reappropriation_approval = 'Approved';
            $item->reappropriation_approval_remarks = //'Remarks from '. $user.' '.
            $request->remarks ;
            $item->save();

            return response()->json(['success' => true, 'message' => 'Item approved successfully.']);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function decline(Request $request)
    {
        $item = BudgetItem::find($request->item_id);
        if (!$item) {
            return response()->json(['success' => false, 'message' => 'Item not found.']);
        }

        $item->reappropriation_approval = 'declined';
        $item->reappropriation_approval_remarks = $request->remarks;
        $item->save();

        return response()->json(['success' => true, 'message' => 'Item declined successfully.']);
    }
}
