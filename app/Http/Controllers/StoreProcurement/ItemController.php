<?php

namespace App\Http\Controllers\StoreProcurement;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StoreProcurement\Item;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Services\StoreProcurement\PinnacleApiService;

class ItemController extends Controller
{
    private const ITEMS_API_ENDPOINT = 'capital_expenditure_items';
    private const ITEMS_CACHE_TTL_SECONDS = 600;

    public function index()
    {
        return view('store_procurement.items.index');
    }

    public function data(Request $request, PinnacleApiService $pinnacleApiService)
    {
        if ($request->boolean('lazy')) {
            return $this->lazyData($request, $pinnacleApiService);
        }

        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);
        $searchValue = trim((string) $request->input('search.value', ''));
        $orderColumn = (int) $request->input('order.0.column', 1);
        $orderDir = strtolower((string) $request->input('order.0.dir', 'desc')) === 'asc' ? 'asc' : 'desc';

        $columnMap = [
            0 => 'item_id',
            1 => 'item_title',
            2 => 'item_type',
            3 => 'item_category',
            4 => 'brand',
            5 => 'model',
            6 => 'budget_type',
            7 => 'status',
        ];

        $query = DB::table('sp_items');
        $totalRecords = DB::table('sp_items')->count();

        if ($searchValue !== '') {
            $query->where(function ($q) use ($searchValue) {
                $likeValue = '%' . strtolower($searchValue) . '%';
                $q->whereRaw('LOWER(item_title) LIKE ?', [$likeValue])
                    ->orWhereRaw('LOWER(item_type) LIKE ?', [$likeValue])
                    ->orWhereRaw('LOWER(NVL(item_category, \'\')) LIKE ?', [$likeValue])
                    ->orWhereRaw('LOWER(NVL(brand, \'\')) LIKE ?', [$likeValue])
                    ->orWhereRaw('LOWER(NVL(model, \'\')) LIKE ?', [$likeValue])
                    ->orWhereRaw('LOWER(NVL(budget_type, \'\')) LIKE ?', [$likeValue])
                    ->orWhereRaw('LOWER(status) LIKE ?', [$likeValue]);
            });
        }

        $filteredRecords = (clone $query)->count();
        $orderBy = $columnMap[$orderColumn] ?? 'item_id';

        $items = $query->orderBy($orderBy, $orderDir)
            ->offset($start)
            ->limit($length)
            ->get();

        $data = $items->map(function ($item) {
            return [
                'item_id' => $item->item_id,
                'item_title' => $item->item_title,
                'item_type' => $item->item_type,
                'item_category' => $item->item_category,
                'brand' => $item->brand,
                'model' => $item->model,
                'budget_type' => $item->budget_type,
                'status' => $item->status,
                'remarks' => $item->remarks,
            ];
        });

        return response()->json([
            'draw' => (int) $request->input('draw'),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data,
        ]);
    }

    private function lazyData(Request $request, PinnacleApiService $pinnacleApiService)
    {
        $offset = max((int) $request->input('offset', 0), 0);
        $limit = (int) $request->input('limit', 100);
        $limit = $limit > 0 ? min($limit, 200) : 100;
        $searchValue = trim((string) $request->input('q', ''));

        $items = $this->apiItemsDataset($pinnacleApiService);

        if ($searchValue !== '') {
            $needle = mb_strtolower($searchValue);
            $items = array_values(array_filter($items, function (array $item) use ($needle) {
                $haystacks = [
                    $item['api_item_id'] ?? '',
                    $item['item_title'] ?? '',
                    $item['item_type'] ?? '',
                    $item['unit'] ?? '',
                    $item['app_unit'] ?? '',
                    $item['fin_acc_title'] ?? '',
                    $item['item_category'] ?? '',
                    $item['brand'] ?? '',
                    $item['model'] ?? '',
                    $item['budget_type'] ?? '',
                    $item['status'] ?? '',
                ];

                foreach ($haystacks as $haystack) {
                    if ($haystack !== null && mb_stripos((string) $haystack, $needle) !== false) {
                        return true;
                    }
                }

                return false;
            }));
        }

        $total = count($items);
        $items = array_slice($items, $offset, $limit);

        $loaded = $offset + count($items);

        return response()->json([
            'data' => $items,
            'total' => $total,
            'loaded' => $loaded,
            'next_offset' => $loaded,
            'has_more' => $loaded < $total,
        ]);
    }

    private function apiItemsDataset(PinnacleApiService $pinnacleApiService): array
    {
        $cacheKey = 'store_procurement.items.api.' . self::ITEMS_API_ENDPOINT;
        $cached = Cache::get($cacheKey);

        if (is_array($cached) && !empty($cached)) {
            return $cached;
        }

        $response = $pinnacleApiService->fetch(self::ITEMS_API_ENDPOINT);
        $records = $this->extractApiRecords($response);

        if (empty($records)) {
            Cache::forget($cacheKey);
            return [];
        }

        $mapped = array_values(array_map(function ($item) {
            return [
                'item_id' => $item['ID'] ?? $item['Id'] ?? $item['id'] ?? null,
                'api_item_id' => $item['ID'] ?? $item['Id'] ?? $item['id'] ?? null,
                'item_title' => $this->cleanApiValue($item['Title'] ?? $item['title'] ?? null),
                'item_type' => $this->cleanApiValue($item['ItemType'] ?? $item['item_type'] ?? null),
                'unit' => $this->cleanApiValue($item['Unit'] ?? $item['unit'] ?? null),
                'app_unit' => $this->cleanApiValue($item['AppUnit'] ?? $item['app_unit'] ?? null),
                'fin_acc_title' => $this->cleanApiValue($item['FinAcc01_Title'] ?? $item['fin_acc01_title'] ?? null),
                'item_category' => $this->cleanApiValue($item['Category'] ?? $item['category'] ?? null),
                'brand' => $this->cleanApiValue($item['Brand'] ?? $item['brand'] ?? null),
                'model' => $this->cleanApiValue($item['Model'] ?? $item['model'] ?? null),
                'budget_type' => $this->cleanApiValue($item['BudgetType'] ?? $item['budget_type'] ?? null),
                'status' => $this->cleanApiValue($item['Status'] ?? $item['status'] ?? null),
                'remarks' => $this->cleanApiValue($item['Remarks'] ?? $item['remarks'] ?? null),
            ];
        }, $records));

        if (!empty($mapped)) {
            Cache::put($cacheKey, $mapped, self::ITEMS_CACHE_TTL_SECONDS);
        }

        return $mapped;
    }

    private function extractApiRecords(array $response): array
    {
        if (isset($response['data']) && is_array($response['data'])) {
            return $response['data'];
        }

        if (isset($response['Data']) && is_array($response['Data'])) {
            return $response['Data'];
        }

        if (array_is_list($response)) {
            return $response;
        }

        return [];
    }

    private function cleanApiValue($value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    public function create()
    {
        return view('store_procurement.items.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            // 'bh_cat_id'  => 'required|numeric',
            'item_title' => 'required|string|max:1000',
            'item_type'  => 'required|string',
            'status'     => 'required|string',
        ]);

        DB::table('sp_items')->insert([
            'bh_cat_id'      => $request->bh_cat_id,
            'item_title'     => $request->item_title,
            'item_type'      => $request->item_type,
            'item_category'  => $request->item_category,
            'brand'          => $request->brand,
            'model'          => $request->model,
            'budget_type'    => $request->budget_type,
            'status'         => $request->status,
            'remarks'        => $request->remarks,
            'entry_username' => auth()->user()->username ?? 'system',
        ]);

        return redirect()->route('items.index')
            ->with('success', 'Item created successfully');
    }

    public function edit($id)
    {
        $item = DB::table('sp_items')->where('item_id', $id)->first();

        abort_if(!$item, 404);

        if (request()->ajax()) {
            return response()->json($item);
        }

        return view('store_procurement.items.edit', compact('item'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'item_title' => 'required|string|max:1000',
            'item_type'  => 'required|string',
            'status'     => 'required|string',
        ]);

        DB::table('sp_items')
            ->where('item_id', $id)
            ->update([
                'bh_cat_id'      => $request->bh_cat_id,
                'item_title'     => $request->item_title,
                'item_type'      => $request->item_type,
                'item_category'  => $request->item_category,
                'brand'          => $request->brand,
                'model'          => $request->model,
                'budget_type'    => $request->budget_type,
                'status'         => $request->status,
                'remarks'        => $request->remarks,
                'updated_at'     => DB::raw('systimestamp'),
            ]);

        return redirect()->route('items.index')
            ->with('success', 'Item updated successfully');
    }

    public function destroy($id)
    {
        DB::table('sp_items')->where('item_id', $id)->delete();

        return redirect()->route('items.index')
            ->with('success', 'Item deleted successfully');
    }
//     public function create()
//     {
//         // resources/views/store_procurement/Items/create.blade.php
//        // dd('sdfdsfsdf');
//         return view('store_procurement.items.create');
//     }

//     public function store(Request $request)
//     {
//         $request->validate([
//             'bh_cat_id'      => 'required|numeric',
//             'item_title'     => 'required|string|max:1000',
//             'item_type'      => 'required|string|max:50',
//             'item_category'  => 'nullable|string|max:100',
//             'brand'          => 'nullable|string|max:100',
//             'model'          => 'nullable|string|max:100',
//             'budget_type'    => 'nullable|string|max:50',
//             'status'         => 'required|string|max:10',
//             'remarks'        => 'nullable|string|max:1000',
//         ]);

//         DB::transaction(function () use ($request) {

//              DB::table('sp_items')->insert([
//         // 'bh_cat_id'      => $request->bh_cat_id,
//         'item_title'     => $request->item_title,
//         'item_type'      => $request->item_type,
//         'item_category'  => $request->item_category,
//         'brand'          => $request->brand,
//         'model'          => $request->model,
//         'budget_type'    => $request->budget_type,
//         'status'         => $request->status,
//         'remarks'        => $request->remarks,
//         'entry_username' => auth()->user()->username ?? 'system',
//     ]);
//         });

//         return redirect()
//             ->route('items.index')
//             ->with('success', 'Item saved successfully.');
//     }


//     public function index()
//     {
//         $items = DB::table('sp_items')
//             ->orderBy('item_id', 'desc')
//             ->get();

//         return view('store_procurement.items.index', compact('items'));
//     }

//     // public function create()
//     // {
//     //     return view('store_procurement.items.create');
//     // }

//     // public function store(Request $request)
//     // {
//     //     $request->validate([
//     //         'bh_cat_id'  => 'required|numeric',
//     //         'item_title' => 'required|string|max:1000',
//     //         'item_type'  => 'required|string',
//     //         'status'     => 'required|string',
//     //     ]);

//     //     DB::table('sp_items')->insert([
//     //         'bh_cat_id'      => $request->bh_cat_id,
//     //         'item_title'     => $request->item_title,
//     //         'item_type'      => $request->item_type,
//     //         'item_category'  => $request->item_category,
//     //         'brand'          => $request->brand,
//     //         'model'          => $request->model,
//     //         'budget_type'    => $request->budget_type,
//     //         'status'         => $request->status,
//     //         'remarks'        => $request->remarks,
//     //         'entry_username' => auth()->user()->username ?? 'system',
//     //     ]);

//     //     return redirect()->route('items.index')
//     //         ->with('success', 'Item created successfully');
//     // }

//     public function edit($id)
//     {
//         $item = DB::table('sp_items')->where('item_id', $id)->first();

//         abort_if(!$item, 404);

//         return view('store_procurement.items.edit', compact('item'));
//     }

//     public function update(Request $request, $id)
//     {
//         $request->validate([
//             'bh_cat_id'  => 'required|numeric',
//             'item_title' => 'required|string|max:1000',
//             'item_type'  => 'required|string',
//             'status'     => 'required|string',
//         ]);

//         DB::table('sp_items')
//             ->where('item_id', $id)
//             ->update([
//                 'bh_cat_id'      => $request->bh_cat_id,
//                 'item_title'     => $request->item_title,
//                 'item_type'      => $request->item_type,
//                 'item_category'  => $request->item_category,
//                 'brand'          => $request->brand,
//                 'model'          => $request->model,
//                 'budget_type'    => $request->budget_type,
//                 'status'         => $request->status,
//                 'remarks'        => $request->remarks,
//                 'updated_at'     => DB::raw('systimestamp'),
//             ]);

//         return redirect()->route('store_procurement.items.index')
//             ->with('success', 'Item updated successfully');
//     }

//     public function destroy($id)
//     {
//         DB::table('sp_items')->where('item_id', $id)->delete();

//         return redirect()->route('store_procurement.items.index')
//             ->with('success', 'Item deleted successfully');
//     }
}
