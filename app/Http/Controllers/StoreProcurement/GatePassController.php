<?php
namespace App\Http\Controllers\StoreProcurement;

use App\Http\Controllers\Controller;
use App\Models\StoreProcurement\GatePass;
use App\Models\StoreProcurement\GatePassItem;
use App\Models\StoreProcurement\SpAsset;
use App\Models\StoreProcurement\SpAssetItem;
use App\Models\StoreProcurement\PurchaseOrder;
use Illuminate\Http\Request;
use DB;

class GatePassController extends Controller
{
public function create($po_id){

    $po = PurchaseOrder::with('items')->findOrFail($po_id);
    return view('store_procurement.gate_pass.create', compact('po'));

    // return view('store_procurement.gate_pass.create', compact('po'));
}

public function store2(Request $request){
    dd($request->get('gp_id'));
    $request->validate([
        'delivery_person'       => 'required|string|max:150',
        'cnic'                  => 'required|string|max:20',
        'contact_no'            => 'required|string|max:20',
        'vehicle_no'            => 'required|string|max:20',
        // 'remarks'               => 'required|string|max:255',

        // items validation
        'items'                 => 'required|array|min:1',
        'items.*.item_id'       => 'required|integer',
        'items.*.delivered_qty' => 'required|integer|min:1',
        'items.*.serials'       => 'required|array',
        'items.*.serials.*'     => 'required|string|max:100',
    ]);

    // ✅ Custom logic: ensure serial count matches delivered_qty
    // ✅ Custom validation: serial count must match delivered_qty
    foreach ($request->items as $index => $item) {


        $deliveredQty = (int) $item['delivered_qty'];
        $serialCount = isset($item['serials']) ? count($item['serials']) : 0;
        // dd($deliveredQty,$serialCount);
        if ($serialCount !== $deliveredQty) {

            // dd('123312323666');
            return response()->json([
                'success' => false,
                'message' => "Number of enetered serial numbers ($serialCount) must equal delivered quantity ($deliveredQty)."
            ], 422);
        }
    }

    // dd($request->all());
    $deliveredQty = (int) $request->items[0]['delivered_qty'];
    $order_quantity = (int) $request->order_quantity;

    $totalDelivered = collect($request->items)->sum('delivered_qty');

    // Determine delivery status
    $remaining_qty = 0 ;
    $deliveryStatus = 'Pending';
    if ($order_quantity > $deliveredQty) {
        $remaining_qty = $order_quantity - $deliveredQty;
        $deliveryStatus = 'Partially Delivered';
    } elseif ($deliveredQty  == $order_quantity) {
        $deliveryStatus = 'Completed';
    }

    //    dd($deliveryStatus);
    //    dd($deliveredQty);
    //    dd($request->order_quantity);
    //    dd(count($request->items[0]));

    PurchaseOrder::where('PO_ID', $request->po_id)
        ->update(['DELIVERY_STATUS' => $deliveryStatus,
            'delivered_quantity' => $deliveredQty,
            'remaining_quantity'  => $remaining_qty

    ]);
    // Save Gate Pass
    $gatePass = GatePass::create([
        'PO_ID'          => $request->po_id,
        'DELIVERY_PERSON'=> $request->delivery_person,
        'CNIC'           => $request->cnic,
        'CONTACT_NO'     => $request->contact_no,
        'VEHICLE_NO'     => $request->vehicle_no,
        'REMARKS'        => $request->remarks,
        'ENTRY_USER_ID'  => auth()->id(),
        'DELIVERY_STATUS' => $deliveryStatus,
        'DELIVERED_QUANTITY' => $deliveredQty,
        // 'delivery_remarks' => $order_quantity,
    ]);

    // dd($gatePass->gp_id);
    foreach ($request->items as $item) {
        GatePassItem::create([
            'GP_ID'          => $gatePass->gp_id,
            'ITEM_ID'        => $item['item_id'],
            'SERIAL_NUMBER'  => implode(',', $item['serials']), // join array into string
            'QUANTITY'       => $item['delivered_qty'],         // use delivered_qty
            'INSPECTED_FLAG' => 'pending',
        ]);
    }
    //  dd( $gatePass);


        // Save into sp_ASSETS (parent)
    $asset = SpAsset::create([
        'asset_code'        => 'AST-' . uniqid(), // auto-generate code
        'po_id'             => $request->po_id,
        'grn_id'            => null,  // set later after GRN approval
        'name'              => 'Item-' . $item['item_id'],
        'order_quantity'    => $order_quantity,
        'received_quantity' => $item['delivered_qty'],
        'received_status'   => 'Partial', // or 'Full' if logic says so
        'inspection_status' => 'Pending',
        'status'            => 'Active',
        // newly added columns
        'qr_id'               => $request->qr_id ?? null,
        'vq_id'               => $request->vq_id ?? null,
        'bh_id'               => $request->budget_head_id ?? null, // map correctly to your form field
        'item_remarks'        => $request->item_remarks ?? null,
        'budget_year_id'      => $request->budget_year_id ?? null,
        'budget_type'         => $request->budget_type ?? null,
        'item_title'          => $request->item_title ?? null,
        'total_price'         => $request->total_price ?? null,
        'currency'            => $request->currency ?? null,
        'vendor_id'           => $request->vendor_id ?? null,
        'warranty_period'     => $request->warranty ?? null,
        'vendor_quotation_id' => $request->vendor_quotation_id ?? null,
    ]);

    // Save into sp_ASSET_ITEMS (children)
    foreach ($item['serials'] as $serial) {
        SpAssetItem::create([
            'asset_id'            => $asset->asset_id,
            'serial_number'       => $serial,
            'allocation_status'   => 'Available',
            'inspection_status'   => 'Pending',
            'scrap_status'        => 'Not Scrapped',
            'status'              => 'Active',
        ]);
    }

    // dd($asset);
    //   dd($gatePass);

    // return redirect()->route('gatepass.show', $gatePass->gp_id)
    return response()->json([
        'success' => true,
        'message' => 'Gate Pass created successfully!',
        'redirect_url' => route('gatepass.list', $gatePass->gp_id)
    ]);
}

public function store(Request $request)
{
    $gpId = $request->filled('gp_id') ? $request->get('gp_id') : null;
    $budgetType = $request->input('budget_type');
    $isStoreItems = $this->isStoreItemsRequest($request);

    // CapEx serials only for non-store CapEx; store items never need asset/serials
    $needsSerials = (!$isStoreItems && $budgetType === 'CapEx');

    $rules = [
        'po_id'                 => 'required',
        'delivery_person'       => 'required|string|max:150',
        'cnic'                  => 'required|string|max:20',
        'contact_no'            => 'required|string|max:20',
        'vehicle_no'            => 'required|string|max:20',
        'items'                 => 'required|array|min:1',
        'items.*.item_id'       => 'nullable',
        'items.*.store_item_id' => 'nullable',
        'items.*.delivered_qty' => 'required|integer|min:1',
    ];

    if ($needsSerials) {
        $rules['items.*.serials']   = 'required|array|min:1';
        $rules['items.*.serials.*'] = 'required|string|max:100';
    } else {
        $rules['items.*.serials']   = 'nullable|array';
        $rules['items.*.serials.*'] = 'nullable|string|max:100';
    }

    try {
        $request->validate($rules);
    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'success' => false,
            'message' => collect($e->errors())->flatten()->first() ?: 'Validation failed.',
            'errors'  => $e->errors(),
        ], 422);
    }

    // Each line must have either item_id or store_item_id (store POs use store_item_id)
    foreach ($request->items as $index => $item) {
        $itemId = $item['item_id'] ?? null;
        $storeItemId = $item['store_item_id'] ?? null;
        if (($itemId === null || $itemId === '') && ($storeItemId === null || $storeItemId === '')) {
            return response()->json([
                'success' => false,
                'message' => 'Item reference missing for line ' . ($index + 1) . '.',
            ], 422);
        }
    }

    if ($needsSerials) {
        foreach ($request->items as $item) {
            $deliveredQty = (int) $item['delivered_qty'];
            $serialCount  = isset($item['serials']) ? count(array_filter($item['serials'], function ($s) {
                return $s !== null && $s !== '';
            })) : 0;

            if ($serialCount !== $deliveredQty) {
                return response()->json([
                    'success' => false,
                    'message' => "Number of entered serial numbers ($serialCount) must equal delivered quantity ($deliveredQty)."
                ], 422);
            }
        }
    }

    $deliveredQty   = (int) $request->items[0]['delivered_qty'];
    $order_quantity = (int) $request->order_quantity;
    $remaining_qty  = max($order_quantity - $deliveredQty, 0);
    $deliveryStatus = $deliveredQty < $order_quantity ? 'Partially Delivered' : 'Completed';

    try {
        DB::beginTransaction();

        PurchaseOrder::where('PO_ID', $request->po_id)->update([
            'DELIVERY_STATUS'    => $deliveryStatus,
            'delivered_quantity' => $deliveredQty,
            'remaining_quantity' => $remaining_qty
        ]);

        if ($gpId) {
            $gatePass = GatePass::findOrFail($gpId);
            $gatePass->update([
                'DELIVERY_PERSON'    => $request->delivery_person,
                'CNIC'               => $request->cnic,
                'CONTACT_NO'         => $request->contact_no,
                'VEHICLE_NO'         => $request->vehicle_no,
                'REMARKS'            => $request->remarks,
                'DELIVERY_STATUS'    => $deliveryStatus,
                'DELIVERED_QUANTITY' => $deliveredQty,
            ]);

            GatePassItem::where('GP_ID', $gpId)->delete();

            foreach ($request->items as $item) {
                $serials = array_values(array_filter($item['serials'] ?? [], function ($s) {
                    return $s !== null && $s !== '';
                }));

                GatePassItem::create([
                    'GP_ID'          => $gpId,
                    'ITEM_ID'        => $this->resolveGatePassItemId($item),
                    'SERIAL_NUMBER'  => !empty($serials) ? implode(',', $serials) : null,
                    'QUANTITY'       => $item['delivered_qty'],
                    'INSPECTED_FLAG' => 'pending',
                ]);

                // Store items: quantity + gate pass only — no asset code
                if (!$isStoreItems) {
                    $this->syncAssetForItem($request, $item, $serials, $order_quantity);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Gate Pass updated successfully!',
                'gp_id'   => $gatePass->gp_id,
                'po_id'   => $request->po_id,
            ]);
        }

        $gatePass = GatePass::create([
            'PO_ID'              => $request->po_id,
            'DELIVERY_PERSON'    => $request->delivery_person,
            'CNIC'               => $request->cnic,
            'CONTACT_NO'         => $request->contact_no,
            'VEHICLE_NO'         => $request->vehicle_no,
            'REMARKS'            => $request->remarks,
            'ENTRY_USER_ID'      => auth()->id(),
            'DELIVERY_STATUS'    => $deliveryStatus,
            'DELIVERED_QUANTITY' => $deliveredQty,
        ]);

        foreach ($request->items as $item) {
            $serials = array_values(array_filter($item['serials'] ?? [], function ($s) {
                return $s !== null && $s !== '';
            }));

            GatePassItem::create([
                'GP_ID'          => $gatePass->gp_id,
                'ITEM_ID'        => $this->resolveGatePassItemId($item),
                'SERIAL_NUMBER'  => !empty($serials) ? implode(',', $serials) : null,
                'QUANTITY'       => $item['delivered_qty'],
                'INSPECTED_FLAG' => 'pending',
            ]);

            // Store items: no SpAsset / asset code
            if (!$isStoreItems) {
                $this->syncAssetForItem($request, $item, $serials, $order_quantity, true);
            }
        }

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Gate Pass created successfully!',
            'gp_id'   => $gatePass->gp_id,
            'po_id'   => $request->po_id,
            'redirect_url' => route('gatepass.index'),
        ]);
    } catch (\Exception $e) {
        DB::rollBack();

        return response()->json([
            'success' => false,
            'message' => 'Failed to save gate pass: ' . $e->getMessage(),
        ], 500);
    }
}

private function isStoreItemsRequest(Request $request): bool
{
    $flag = strtolower(trim((string) $request->input('is_store_items', '')));
    if (in_array($flag, ['1', 'true', 'yes'], true)) {
        return true;
    }

    $reqType = strtolower(trim((string) $request->input('request_type', '')));
    if ($reqType === 'store_items') {
        return true;
    }

    foreach ($request->input('items', []) as $item) {
        if (!empty($item['store_item_id'])) {
            return true;
        }
    }

    return false;
}

private function resolveGatePassItemId(array $item)
{
    $itemId = $item['item_id'] ?? null;
    if ($itemId !== null && $itemId !== '') {
        return $itemId;
    }

    $storeItemId = $item['store_item_id'] ?? null;
    if ($storeItemId !== null && $storeItemId !== '') {
        return $storeItemId;
    }

    return null;
}

/**
 * Create or update SpAsset + SpAssetItem for a gate pass line item.
 */
private function syncAssetForItem(Request $request, array $item, array $serials, $order_quantity, $forceCreate = false)
{
    $existingAsset = $forceCreate
        ? null
        : SpAsset::where('po_id', $request->po_id)->first();

    $receivedStatus = ((int) $item['delivered_qty'] >= (int) $order_quantity) ? 'Full' : 'Partial';

    if ($existingAsset) {
        $existingAsset->update([
            'received_quantity' => $item['delivered_qty'],
            'received_status'   => $receivedStatus,
            'inspection_status' => 'Pending',
            'status'            => 'Active',
            'item_remarks'      => $request->item_remarks ?? null,
            'budget_type'       => $request->budget_type ?? null,
            'total_price'       => $request->total_price ?? null,
            'currency'          => $request->currency ?? null,
            'warranty_period'   => $request->warranty ?? null,
        ]);

        SpAssetItem::where('asset_id', $existingAsset->asset_id)->delete();

        foreach ($serials as $serial) {
            SpAssetItem::create([
                'asset_id'          => $existingAsset->asset_id,
                'serial_number'     => $serial,
                'allocation_status' => 'Available',
                'inspection_status' => 'Pending',
                'scrap_status'      => 'Not Scrapped',
                'status'            => 'Active',
            ]);
        }

        return;
    }

    $asset = SpAsset::create([
        'asset_code'          => 'AST-' . uniqid(),
        'po_id'               => $request->po_id,
        'grn_id'              => null,
        'name'                => 'Item-' . $item['item_id'],
        'order_quantity'      => $order_quantity,
        'received_quantity'   => $item['delivered_qty'],
        'received_status'     => $receivedStatus,
        'inspection_status'   => 'Pending',
        'status'              => 'Active',
        'qr_id'               => $request->qr_id ?? null,
        'vq_id'               => $request->vq_id ?? null,
        'bh_id'               => $request->budget_head_id ?? null,
        'item_remarks'        => $request->item_remarks ?? null,
        'budget_year_id'      => $request->budget_year_id ?? null,
        'budget_type'         => $request->budget_type ?? null,
        'item_title'          => $request->item_title ?? null,
        'total_price'         => $request->total_price ?? null,
        'currency'            => $request->currency ?? null,
        'vendor_id'           => $request->vendor_id ?? null,
        'warranty_period'     => $request->warranty ?? null,
        'vendor_quotation_id' => $request->vendor_quotation_id ?? null,
    ]);

    foreach ($serials as $serial) {
        SpAssetItem::create([
            'asset_id'          => $asset->asset_id,
            'serial_number'     => $serial,
            'allocation_status' => 'Available',
            'inspection_status' => 'Pending',
            'scrap_status'      => 'Not Scrapped',
            'status'            => 'Active',
        ]);
    }
}





public function show($gp_id)
{
    // $gatePass = GatePass::with(['purchaseOrder.vendor', 'items.purchaseOrderItem'])->findOrFail($gp_id);
    // dd($gatePass);
    // return view('store_procurement.gate_pass.show', compact('gatePass'));
    $gatePass = GatePass::with(['items'])->findOrFail($gp_id);

    return response()->json($gatePass);
}
public function index()
{
    // dd('test');
    // Approved POs without gate pass
    $purchaseOrders = PurchaseOrder::with('vendor')
        ->where('approvel_status', 'approved')
        ->whereNotNull('po_no')
        ->where(function ($query) {
        $query->where('delivery_status', 'Partially Delivered')
              ->orWhereNull('delivery_status');
                     })
        // ->whereDoesntHave('gatePass') // assuming relation gatePass()
        ->orderBy('po_date', 'desc')
        ->get();
        // dd($purchaseOrders);

    // POs with gate pass already created
    $createdGatePasses = PurchaseOrder::with(['vendor', 'gatePass'])
        ->where('approvel_status', 'approved')
        ->whereNotNull('po_no')
        ->whereHas('gatePass')
        ->orderBy('po_date', 'desc')
        ->get();


    return view('store_procurement.gate_pass.index', compact('purchaseOrders', 'createdGatePasses'));
}

public function UpdateSerial(Request $request)
{
    $request->validate([
        'id' => 'required|integer',
    ]);

    $id = $request->id;

    try {
        \DB::table('sp_purchase_order')
            ->where('po_id', $id)
            ->update([
                'delivery_status'     => 'pending',
                'delivered_quantity'  => '',
                'remaining_quantity'  => '',
                'APPROVEL_STATUS'     => ''
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Record updated successfully.'
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error updating record: ' . $e->getMessage()
        ]);
    }
}


public function getPODetails($po_id)
{
    try {
        $po = PurchaseOrder::with([
                'vendor',
                'items',
                'quotationRequests.budgetItem',
                'quotationRequests.storeBudgetItem',
            ])
            ->findOrFail($po_id);

        // Prefer PO.budget_type; fallback via category from budget item / QR
        $budgetType = $po->budget_type ?? $po->BUDGET_TYPE ?? null;

        if ($budgetType === null || $budgetType === '') {
            $categoryId = null;

            $firstItem = $po->items->first();
            if ($firstItem) {
                $categoryId = $firstItem->bh_cat_id ?? $firstItem->BH_CAT_ID ?? null;
            }

            if (!$categoryId) {
                $qr = $po->quotationRequests->first();
                if ($qr) {
                    $categoryId = $qr->cat_id
                        ?? $qr->CAT_ID
                        ?? optional($qr->budgetItem)->bh_cat_id
                        ?? optional($qr->budgetItem)->BH_CAT_ID
                        ?? null;
                }
            }

            if ($categoryId) {
                $cat = DB::table('sp_vendor_category')
                    ->where('category_id', $categoryId)
                    ->first();
                if ($cat) {
                    $budgetType = $cat->budget_type ?? $cat->BUDGET_TYPE ?? null;
                }
            }
        }

        // Store-items PO: take budget_type from store budget item if still empty
        $isStoreItems = false;
        foreach ($po->quotationRequests as $qr) {
            $reqType = strtolower(trim((string) ($qr->request_type ?? $qr->REQUEST_TYPE ?? '')));
            if ($reqType === 'store_items' || !empty($qr->store_item_id) || !empty($qr->STORE_ITEM_ID)) {
                $isStoreItems = true;
                if (($budgetType === null || $budgetType === '') && $qr->storeBudgetItem) {
                    $budgetType = $qr->storeBudgetItem->budget_type
                        ?? $qr->storeBudgetItem->BUDGET_TYPE
                        ?? $budgetType;
                }
            }
        }

        // Latest gatepass for this PO
        $latestGatepass = DB::table('sp_gate_pass')
            ->where('po_id', $po_id)
            ->orderByDesc('created_at')
            ->first();

        if ($latestGatepass) {
            $serials = DB::table('sp_gate_pass_items')
                ->where('gp_id', $latestGatepass->gp_id ?? $latestGatepass->GP_ID ?? null)
                ->pluck('serial_number');

            $serialNumbers = [];
            foreach ($serials as $serialString) {
                if ($serialString === null || $serialString === '') {
                    continue;
                }
                $serialNumbers = array_merge(
                    $serialNumbers,
                    array_map('trim', explode(',', (string) $serialString))
                );
            }

            $latestGatepass->serial_numbers = array_values(array_filter($serialNumbers, function ($s) {
                return $s !== '';
            }));

            if (!isset($latestGatepass->gp_id) && isset($latestGatepass->GP_ID)) {
                $latestGatepass->gp_id = $latestGatepass->GP_ID;
            }
        }

        $po->latest_gatepass = $latestGatepass;

        return response()->json([
            'po' => $po,
            'budget_type' => $budgetType,
            'is_store_items' => $isStoreItems,
        ]);
    } catch (\Throwable $e) {
        \Log::error('getPODetails failed', [
            'po_id' => $po_id,
            'error' => $e->getMessage(),
        ]);

        return response()->json([
            'message' => 'Failed to fetch Purchase Order details: ' . $e->getMessage(),
        ], 500);
    }
}

//     public function index()
// {
//     // Only show approved POs that don’t already have a gate pass
//     $purchaseOrders = PurchaseOrder::with('vendor')
//         ->where('approvel_status', 'approved')
//         ->orderBy('po_date', 'desc')
//         ->get();

//         // dd($purchaseOrders);

//     return view('store_procurement.gate_pass.index', compact('purchaseOrders'));
// }

public function list()
{
     
    $gatePasses = GatePass::with(['purchaseOrder.vendor', 'items'])
        ->orderBy('GP_ID', 'desc')
        ->paginate(10); // paginate for easier viewing
        //  dd($gatePasses);
    return view('store_procurement.gate_pass.list', compact('gatePasses'));
}

/**
 * Store Requisitions: list store items whose PO already has a gate pass.
 */
public function storeItemsGatePassList()
{
    $gatePassItems = [];

    try {
        $rows = DB::select("
            SELECT
                gp.GP_ID AS gp_id,
                gp.DELIVERY_PERSON AS delivery_person,
                gp.CNIC AS cnic,
                gp.CONTACT_NO AS contact_no,
                gp.VEHICLE_NO AS vehicle_no,
                gp.DELIVERED_QUANTITY AS delivered_quantity,
                gp.DELIVERY_STATUS AS delivery_status,
                gp.REMARKS AS remarks,
                gp.CREATED_AT AS gate_pass_date,
                po.PO_ID AS po_id,
                po.PO_NO AS po_no,
                po.QUANTITY AS po_quantity,
                po.TOTAL_PRICE AS total_price,
                po.CURRENCY AS currency,
                po.ITEM_TITLE AS po_item_title,
                v.VENDOR_NAME AS vendor_name,
                qr.QR_ID AS qr_id,
                NVL(sbi.ITEM_TITLE, po.ITEM_TITLE) AS item_title,
                NVL(qr.REQUESTED_QUANTITY, sbi.QUANTITY) AS requested_quantity,
                sbi.SBI_ID AS store_item_id,
                sbi.UNIT AS unit,
                sbh.HEAD_TITLE AS head_title
            FROM SP_GATE_PASS gp
            INNER JOIN SP_PURCHASE_ORDER po ON po.PO_ID = gp.PO_ID
            INNER JOIN SP_QUOTATION_REQUEST qr
                ON qr.PO_ID = po.PO_ID
               AND LOWER(TRIM(NVL(qr.REQUEST_TYPE, ' '))) = 'store_items'
            LEFT JOIN SP_STORE_BUDGET_ITEMS sbi ON sbi.SBI_ID = qr.STORE_ITEM_ID
            LEFT JOIN SP_STORE_BUDGET_HEADS sbh ON sbh.SBH_ID = NVL(qr.STORE_BH_ID, sbi.SBH_ID)
            LEFT JOIN SP_VENDORS v ON v.VENDOR_ID = po.VENDOR_ID
            ORDER BY gp.GP_ID DESC
        ");

        foreach ($rows as $row) {
            $item = [];
            foreach ((array) $row as $key => $value) {
                $item[strtolower($key)] = $value;
            }
            $gatePassItems[] = (object) $item;
        }
    } catch (\Throwable $e) {
        \Log::error('storeItemsGatePassList failed', ['error' => $e->getMessage()]);
    }

    return view('store_procurement.store_items.gate_pass_list', [
        'gatePassItems' => $gatePassItems,
    ]);
}

}
