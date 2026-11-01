<?php

namespace App\Http\Controllers\StoreProcurement;
use App\Models\StoreProcurement\SpAssetAllocation;
use App\Models\StoreProcurement\SpAssetAllocationHistory;
use App\Models\StoreProcurement\SpAssetItem;
use App\Services\StoreProcurement\PinnacleApiService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;


class AssetAllocationController extends Controller
{
       // Show non-allocated items
    public function index()
    {

        // $select_list = "prefix, employee_id as id, emp_f_name || ' ' || emp_l_name as full_name, emp_dept_id, dept_name, school_code";

        // $sql = "SELECT $select_list FROM EMP_EMPLOYEES
        //         LEFT JOIN schools ON school_id = emp_school_id
        //         LEFT JOIN departments ON dept_id = emp_dept_id
        //         WHERE emp_status = 'E'
        //         ORDER BY employee_id DESC";

        // $users = DB::select($sql);
        $items = SpAssetItem::where('allocation_status', 'Not Allocated')->get();
        return view('store_procurement.assets.non_allocated', compact('items'));
    }

    // Allocate item
//     public function allocate(Request $request)
//     {
//         $request->validate([
//             'asset_item_id'   => 'required|exists:sp_asset_items,asset_item_id',
//             'department_id'   => 'required|numeric',
//             'location'        => 'required|string|max:255',
//         ]);


// // dd($request->all());



//         // Find item
//         $item = SpAssetItem::findOrFail($request->asset_item_id);

//         // Update current allocation
//         $item->update([
//             'allocation_status'   => 'Allocated',
//             'allocation_department' => $request->department_id,
//             'allocation_location' => $request->location,
//         ]);

//         // Insert into history
//         SpAssetAllocationHistory::create([
//             'asset_item_id'    => $item->asset_item_id,
//             'serial_number'    => $item->serial_number,
//             'department_id'    => $request->department_id,
//             'allocated_location' => $request->location,
//             'allocation_status' => 'Allocated',
//             'remarks'          => $request->remarks,
//         ]);

//         return response()->json(['success' => true, 'message' => 'Asset allocated successfully']);
//     }






public function allocate2(Request $request)
{
    //  dd($request->all());
    $request->validate([
        'asset_item_ids'        => 'required|string', // comma-separated IDs
        'allocation_department' => 'required|numeric',
        'assignee_user_id'      => 'nullable|numeric',
        'allocation_location'   => 'required|string|max:255',
        'remarks'               => 'nullable|string',
    ]);

    // Convert comma-separated IDs to array
    $assetItemIds = array_map('trim', explode(',', $request->asset_item_ids));

    foreach ($assetItemIds as $itemId) {
        $item = SpAssetItem::find($itemId);

        if (!$item) {
            continue; // skip invalid IDs
        }

        // Update asset item allocation details
        $item->update([
            'allocation_status'     => 'Allocated',
            'allocation_department' => $request->allocation_department,
            'allocation_location'   => $request->allocation_location,
            'assign_to'      => $request->assignee_user_id,
        ]);

        // Insert allocation history
        SpAssetAllocationHistory::create([
            'asset_item_id'       => $item->asset_item_id,
            'serial_number'       => $item->serial_number,
            'department_id'       => $request->allocation_department,
            'allocated_location'  => $request->allocation_location,
            'allocation_status'   => 'Allocated',
            'remarks'             => $request->remarks,
            'allocated_to_user_id'    => $request->assignee_user_id,
        ]);
    }
    $updatedItems[] = $item;
    return response()->json([
        'success' => true,
        'message' => 'Assets allocated successfully.',
        'allocated_ids'     => $item->asset_item_id,
        'department_id'       => $request->allocation_department,
        'allocated_location'  => $request->allocation_location
    ]);
}

public function allocate(Request $request){
    $request->validate([
        'asset_item_ids'        => 'required|string', // comma-separated IDs
        'allocation_department' => 'required|numeric',
        'assignee_user_id'      => 'nullable|numeric',
        'allocation_location'   => 'required|string|max:255',
        'remarks'               => 'nullable|string',
    ]);
    
    // Convert comma-separated IDs to array
    $assetItemIds = array_map('trim', explode(',', $request->asset_item_ids));

    $updatedItems = []; // initialize array
    // dd($assetItemIds);
    foreach ($assetItemIds as $itemId) {
        $item = SpAssetItem::find($itemId);

        if (!$item) {
            continue; // skip invalid IDs
        }

        $inspection = strtolower(trim((string) ($item->inspection_status ?? '')));
        if ($inspection !== 'passed') {
            return response()->json([
                'success' => false,
                'message' => 'Pehle QC Inspection Pass karein. Serial: ' . ($item->serial_number ?? $itemId),
            ], 422);
        }

        // Update asset item allocation details
        $item->update([
            'allocation_status'     => 'Request Sent',
            'allocation_department' => $request->allocation_department,
            'allocation_location'   => $request->allocation_location,
            'assign_to'             => $request->assignee_user_id,
        ]);

        // Insert allocation history
        SpAssetAllocationHistory::create([
            'asset_item_id'          => $item->asset_item_id,
            'serial_number'          => $item->serial_number,
            'department_id'          => $request->allocation_department,
            'allocated_location'     => $request->allocation_location,
            'allocation_status'      => 'Request Sent',
            'remarks'                => $request->remarks,
            'allocated_to_user_id'   => $request->assignee_user_id,
        ]);

        $buildingName = trim((string) $request->allocation_location);
        try {
            $locations = app(PinnacleApiService::class)->fetch('fixed_asset_locations');
            $records = [];
            if (isset($locations['data']) && is_array($locations['data'])) {
                $records = $locations['data'];
            } elseif (is_array($locations) && (function_exists('array_is_list') ? array_is_list($locations) : array_keys($locations) === range(0, count($locations) - 1))) {
                $records = $locations;
            }
            foreach ($records as $loc) {
                if (!is_array($loc)) {
                    continue;
                }
                $id = (string) ($loc['ID'] ?? $loc['Id'] ?? $loc['id'] ?? '');
                if ($id !== '' && $id === (string) $request->allocation_location) {
                    $title = trim((string) ($loc['Title'] ?? $loc['title'] ?? ''));
                    $building = trim((string) ($loc['Building_Title'] ?? ''));
                    $buildingName = trim($title . ($building !== '' ? ' — ' . $building : ''));
                    break;
                }
            }
        } catch (\Throwable $e) {
            // keep raw location id/name
        }

        $deptSql = "select dept_name from departments where dept_id = ?";
        $sqldepartmentSql = DB::select($deptSql, [$request->allocation_department]);
        $deptName = $sqldepartmentSql[0]->dept_name ?? $sqldepartmentSql[0]->DEPT_NAME ?? '';

        $empName = '';
        if (!empty($request->assignee_user_id)) {
            $empSql = "select emp_f_name || ' ' || emp_l_name as emp_name from emp_employees where employee_id = ?";
            $sqlemployeeSql = DB::select($empSql, [$request->assignee_user_id]);
            $empName = $sqlemployeeSql[0]->emp_name ?? $sqlemployeeSql[0]->EMP_NAME ?? '';
        }

        // Push updated item to array
        $updatedItems[] = [
            'asset_item_id'      => $item->asset_item_id,
            'serial_number'      => $item->serial_number,
            'department_id'      => $deptName,
            'emp_name'           => $empName,
            'allocated_location' => $buildingName,
        ];
    }

    return response()->json([
        'success' => true,
        'message' => 'Assets allocated successfully.',
        'allocated_items' => $updatedItems,
    ]);
}

public function reallocate(Request $request)
{
    $request->validate([
        'asset_item_ids' => 'required|string',
        'allocation_department' => 'required|numeric',
        'assignee_user_id' => 'required|numeric',
        'allocation_location' => 'required|string|max:255',
        'remarks' => 'nullable|string',
    ]);

    $assetItemIds = array_values(array_filter(array_map('trim', explode(',', $request->asset_item_ids))));
    $updated = 0;

    foreach ($assetItemIds as $itemId) {
        $item = SpAssetItem::find($itemId);
        if (!$item) {
            continue;
        }

        $inspection = strtolower(trim((string) ($item->inspection_status ?? '')));
        if ($inspection !== 'passed') {
            return response()->json([
                'success' => false,
                'message' => 'Only QC Passed serials can be reallocated.',
            ], 422);
        }

        $currentStatus = strtolower(trim((string) ($item->allocation_status ?? '')));
        if (!in_array($currentStatus, ['allocated', 'request sent', 'reallocated'], true) && empty($item->assign_to)) {
            return response()->json([
                'success' => false,
                'message' => 'Select a serial that is already allocated.',
            ], 422);
        }

        $oldName = 'the previous employee';
        if (!empty($item->assign_to)) {
            $oldRows = DB::select(
                "SELECT TRIM(EMP_F_NAME || ' ' || EMP_L_NAME) AS emp_name FROM EMP_EMPLOYEES WHERE EMPLOYEE_ID = ?",
                [$item->assign_to]
            );
            $old = $oldRows[0] ?? null;
            $foundName = trim((string) ($old->emp_name ?? $old->EMP_NAME ?? ''));
            if ($foundName !== '') {
                $oldName = $foundName;
            }
        }

        $item->update([
            'allocation_status' => 'Request Sent',
            'allocation_department' => $request->allocation_department,
            'allocation_location' => $request->allocation_location,
            'assign_to' => $request->assignee_user_id,
        ]);

        $note = 'Reallocated from ' . $oldName . '.';
        if (trim((string) $request->remarks) !== '') {
            $note .= ' ' . trim((string) $request->remarks);
        }

        SpAssetAllocationHistory::create([
            'asset_item_id' => $item->asset_item_id,
            'serial_number' => $item->serial_number,
            'department_id' => $request->allocation_department,
            'allocated_location' => $request->allocation_location,
            'allocation_status' => 'Reallocated',
            'remarks' => $note,
            'allocated_to_user_id' => $request->assignee_user_id,
        ]);
        $updated++;
    }

    if ($updated === 0) {
        return response()->json([
            'success' => false,
            'message' => 'No selected serial could be reallocated.',
        ], 422);
    }

    return response()->json([
        'success' => true,
        'message' => 'Reallocated ' . $updated . ' serial' . ($updated === 1 ? '' : 's') . '. The previous assignment stays in the history.',
    ]);
}

public function deallocate(Request $request){
    $request->validate([
        'asset_item_id' => 'required|integer|exists:sp_asset_items,asset_item_id',
    ]);

    $asset = SpAssetItem::find($request->asset_item_id);

    if (!$asset) {
        return response()->json(['success' => false, 'message' => 'Asset not found']);
    }
    $asset->allocation_status = 'Available';   // or null if you want completely empty
    $asset->allocation_department = null;
    $asset->allocation_location = null;
    $asset->assign_to = null;          // if your column name is ASSIGN_TO
    $asset->allocation_date = null;
    $asset->save();

    SpAssetAllocationHistory::create([
        'asset_item_id'          => $request->asset_item_id,
        'serial_number'          => '',
        'department_id'          => '',
        'allocated_location'     => '',
        'allocation_status'      => 'De-Allocate',
        'remarks'                => '',
        'allocated_to_user_id'   => '',
    ]);

    return response()->json(['success' => true, 'message' => 'Asset successfully deallocated']);
}


    public function history($assetItemId)
    {
        $history = SpAssetAllocation::where('asset_item_id', $assetItemId)
            ->orderBy('allocation_date', 'asc')
            ->get();

        return view('assets.allocation_history', compact('history'));
    }
}
