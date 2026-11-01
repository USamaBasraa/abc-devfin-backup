<?php

namespace App\Http\Controllers\StoreProcurement;

use App\Http\Controllers\Controller;
use App\Models\StoreProcurement\SpAssetItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AssetInspectionController extends Controller
{
    public function updateInspection(Request $request, $id)
    {
        try {
            $request->validate([
                'inspection_status' => 'required|in:Passed,Failed',
                'physical_condition' => 'nullable|string|max:100',
                'item_packing' => 'nullable|string|max:50',
                'damage_found' => 'nullable|string|max:50',
                'specification_meets' => 'nullable|string|max:50',
                'inspection_remarks' => 'nullable|string|max:2000',
            ]);

            $itemId = (int) $id;
            if ($itemId <= 0) {
                return response()->json(['success' => false, 'message' => 'Invalid asset item ID.'], 422);
            }

            $item = SpAssetItem::find($itemId);
            if (!$item) {
                return response()->json(['success' => false, 'message' => 'Asset item not found.'], 404);
            }

            $this->saveInspection($item, $request);

            $status = $request->inspection_status;
            $fresh = DB::table('SP_ASSET_ITEMS')->where('ASSET_ITEM_ID', $itemId)->first();

            return response()->json([
                'success' => true,
                'message' => 'QC saved. ' . ($status === 'Passed' ? 'Ab allocation kar sakte hain.' : 'Item marked Failed.'),
                'item' => [
                    'asset_item_id' => $itemId,
                    'serial_number' => $this->rowValue($fresh, 'serial_number'),
                    'inspection_status' => $this->rowValue($fresh, 'inspection_status') ?: $status,
                    'unique_asset_code' => $this->rowValue($fresh, 'unique_asset_code'),
                    'allocation_status' => $this->rowValue($fresh, 'allocation_status'),
                    'physical_condition' => $request->input('physical_condition'),
                    'item_packing' => $request->input('item_packing'),
                    'damage_found' => $request->input('damage_found'),
                    'specification_meets' => $request->input('specification_meets'),
                    'inspection_remarks' => $request->input('inspection_remarks'),
                ],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => collect($e->errors())->flatten()->first() ?: 'Validation failed.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'QC failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function updateInspectionBulk(Request $request)
    {
        try {
            $request->validate([
                'item_ids' => 'required|array|min:1',
                'item_ids.*' => 'integer',
                'inspection_status' => 'required|in:Passed,Failed',
                'physical_condition' => 'nullable|string|max:100',
                'item_packing' => 'nullable|string|max:50',
                'damage_found' => 'nullable|string|max:50',
                'specification_meets' => 'nullable|string|max:50',
                'inspection_remarks' => 'nullable|string|max:2000',
            ]);

            $saved = 0;
            foreach ($request->input('item_ids', []) as $id) {
                $item = SpAssetItem::find((int) $id);
                if (!$item) {
                    continue;
                }
                $this->saveInspection($item, $request);
                $saved++;
            }

            if ($saved === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No selected serial numbers could be updated.',
                ], 422);
            }

            $status = $request->inspection_status;
            return response()->json([
                'success' => true,
                'message' => 'QC saved for ' . $saved . ' serial number' . ($saved === 1 ? '' : 's') . '. '
                    . ($status === 'Passed' ? 'You can allocate them now.' : 'They are marked failed.'),
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => collect($e->errors())->flatten()->first() ?: 'Please complete the QC form.',
            ], 422);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'QC could not be saved. Please try again.',
            ], 500);
        }
    }

    private function saveInspection($item, Request $request): void
    {
        $status = $request->inspection_status;
        $itemId = (int) ($item->asset_item_id ?? $item->ASSET_ITEM_ID);
        $coreUpdate = ['INSPECTION_STATUS' => $status];

        if ($status === 'Passed') {
            $coreUpdate['UNIQUE_ASSET_CODE'] = 'AST-' . $itemId;
            $coreUpdate['ALLOCATION_STATUS'] = 'Available';
        } else {
            $coreUpdate['ALLOCATION_STATUS'] = 'Replacement';
        }

        DB::table('SP_ASSET_ITEMS')->where('ASSET_ITEM_ID', $itemId)->update($coreUpdate);

        $qcUpdate = array_filter([
            'PHYSICAL_CONDITION' => $request->input('physical_condition'),
            'ITEM_PACKING' => $request->input('item_packing'),
            'DAMAGE_FOUND' => $request->input('damage_found'),
            'SPECIFICATION_MEETS' => $request->input('specification_meets'),
            'INSPECTION_REMARKS' => $request->input('inspection_remarks'),
        ], function ($v) {
            return $v !== null && $v !== '';
        });

        if (!empty($qcUpdate)) {
            try {
                DB::table('SP_ASSET_ITEMS')->where('ASSET_ITEM_ID', $itemId)->update($qcUpdate);
            } catch (\Throwable $e) {
                // optional QC columns
            }
        }

        $this->syncGatePassInspectedFlag($item, $status);

        try {
            $assetId = $item->asset_id ?? $item->ASSET_ID;
            $pending = DB::table('SP_ASSET_ITEMS')
                ->where('ASSET_ID', $assetId)
                ->where(function ($q) {
                    $q->whereNull('INSPECTION_STATUS')
                        ->orWhereRaw("UPPER(TRIM(INSPECTION_STATUS)) <> 'PASSED'");
                })
                ->count();
            DB::table('SP_ASSETS')->where('ASSET_ID', $assetId)->update([
                'INSPECTION_STATUS' => $pending === 0 ? 'Passed' : 'Pending',
            ]);
        } catch (\Throwable $e) {
            // ignore parent update
        }
    }

    /**
     * Keep SP_GATE_PASS_ITEMS.INSPECTED_FLAG in sync with QC result.
     * Gate pass stores SERIAL_NUMBER as comma-separated list per GPI row.
     */
    private function syncGatePassInspectedFlag($assetItem, string $status): void
    {
        try {
            $flag = ($status === 'Passed') ? 'passed' : 'failed';
            $serial = trim((string) ($assetItem->serial_number ?? ''));

            $asset = DB::table('SP_ASSETS')->where('ASSET_ID', $assetItem->asset_id)->first();
            if (!$asset) {
                return;
            }
            $poId = $asset->po_id ?? $asset->PO_ID ?? null;
            if ($poId === null || $poId === '') {
                return;
            }

            $gpItems = DB::table('SP_GATE_PASS_ITEMS as gpi')
                ->join('SP_GATE_PASS as gp', 'gp.GP_ID', '=', 'gpi.GP_ID')
                ->where('gp.PO_ID', $poId)
                ->select('gpi.GPI_ID', 'gpi.SERIAL_NUMBER', 'gpi.INSPECTED_FLAG')
                ->get();

            foreach ($gpItems as $gpi) {
                $gpiId = $gpi->GPI_ID ?? $gpi->gpi_id ?? null;
                $serialCsv = (string) ($gpi->SERIAL_NUMBER ?? $gpi->serial_number ?? '');
                $serials = array_values(array_filter(array_map('trim', explode(',', $serialCsv))));

                $matches = false;
                if ($serial !== '' && !empty($serials)) {
                    $matches = in_array($serial, $serials, true);
                } elseif ($serial === '' && empty($serials)) {
                    // OpEx / no serial — update all GPI for this PO
                    $matches = true;
                }

                if (!$matches || !$gpiId) {
                    continue;
                }

                if (count($serials) <= 1) {
                    DB::table('SP_GATE_PASS_ITEMS')
                        ->where('GPI_ID', $gpiId)
                        ->update([
                            'INSPECTED_FLAG' => $flag,
                            'INSPECTION_REMARKS' => request()->input('inspection_remarks'),
                        ]);
                    continue;
                }

                // Multi-serial GPI: only mark passed when every serial is Passed on asset items
                $assetId = $assetItem->asset_id;
                $itemStatuses = DB::table('SP_ASSET_ITEMS')
                    ->where('ASSET_ID', $assetId)
                    ->whereIn('SERIAL_NUMBER', $serials)
                    ->pluck('INSPECTION_STATUS')
                    ->map(function ($s) {
                        return strtolower(trim((string) $s));
                    })
                    ->all();

                // Also handle Oracle uppercase column via raw if pluck empty
                if (empty($itemStatuses)) {
                    $rows = DB::table('SP_ASSET_ITEMS')
                        ->where('ASSET_ID', $assetId)
                        ->whereIn('SERIAL_NUMBER', $serials)
                        ->get();
                    $itemStatuses = [];
                    foreach ($rows as $r) {
                        $itemStatuses[] = strtolower(trim((string) ($r->inspection_status ?? $r->INSPECTION_STATUS ?? '')));
                    }
                }

                if (count($itemStatuses) < count($serials)) {
                    // Not all serials inspected yet — keep pending unless this one failed
                    if ($flag === 'failed') {
                        DB::table('SP_GATE_PASS_ITEMS')->where('GPI_ID', $gpiId)->update([
                            'INSPECTED_FLAG' => 'failed',
                            'INSPECTION_REMARKS' => request()->input('inspection_remarks'),
                        ]);
                    }
                    continue;
                }

                $allPassed = !in_array(false, array_map(function ($s) {
                    return $s === 'passed';
                }, $itemStatuses), true);
                $anyFailed = in_array('failed', $itemStatuses, true);

                $gpiFlag = $anyFailed ? 'failed' : ($allPassed ? 'passed' : 'pending');
                DB::table('SP_GATE_PASS_ITEMS')->where('GPI_ID', $gpiId)->update([
                    'INSPECTED_FLAG' => $gpiFlag,
                    'INSPECTION_REMARKS' => request()->input('inspection_remarks'),
                ]);
            }
        } catch (\Throwable $e) {
            // Do not fail QC save if gate-pass sync has issues; log message in response path
        }
    }

    private function rowValue($row, string $column)
    {
        if (!is_object($row)) {
            return null;
        }
        foreach ([$column, strtoupper($column), strtolower($column)] as $key) {
            if (isset($row->{$key}) && $row->{$key} !== '') {
                return $row->{$key};
            }
        }
        return null;
    }
}
