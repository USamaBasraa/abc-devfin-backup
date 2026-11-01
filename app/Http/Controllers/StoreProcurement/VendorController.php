<?php
/**
 *
 * User: Arsalan Ahmed
 * Date: 29/07/2025
 * Time: 9:52 AM
 */
namespace App\Http\Controllers\StoreProcurement;

use App\Http\Controllers\Controller;
use App\Models\Grader\Departments;
use App\Models\Grader\EmpEmployees;
use App\Models\Grader\FacultyCourses;
use App\Models\Grader\Schools;
use App\Models\StoreProcurement\Sp_vendor;
use App\Models\StoreProcurement\VendorCategory;
use App\Models\StoreProcurement\VendorMasterCategory;
use App\Services\StoreProcurement\PinnacleVendorSyncService;
use App\Support\Oracle\ResolvesSynonymObjects;
use DataTables;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Response;
use Symfony\Component\Console\Helper\Table;
use Throwable;
use Validator;

class VendorController extends Controller
{
    use ResolvesSynonymObjects;

    private const MANUALLY_TRACKED_FIELDS = [
        'vendor_name',
        'vendor_contact_person',
        'vendor_phone',
        'vendor_email',
        'vendor_address',
        'vendor_category_id',
        'vendor_registration_date',
        'supplier',
        'ntn_cnic',
        'attn',
        'beneficiary_name',
        'bank_name',
        'bank_iban',
    ];

    public function index()
    {
        $this->ensureVendorSchema();

        $vendors = Sp_vendor::all();
        $categories = VendorCategory::orderBy('category_name')->get();
        $masterCategories = VendorMasterCategory::orderBy('title')->get();

        return view('store_procurement.vendor.index', compact('vendors', 'categories', 'masterCategories'));
    }

    public function create()
    {
        $this->ensureVendorSchema();

        $vendors = Sp_vendor::all();
        $categories = VendorCategory::orderBy('category_name')->get();
        $masterCategories = VendorMasterCategory::orderBy('title')->get();

        return view('store_procurement.vendor.vendor_create', compact('vendors', 'categories', 'masterCategories'));
    }

    public function show($id)
    {
        $this->ensureVendorSchema();

        $vendor = DB::table('sp_vendors')->where('vendor_id', $id)->first();

        if (!$vendor) {
            return response()->json(['message' => 'Vendor not found'], 404);
        }

        $data = [];
        foreach ((array) $vendor as $key => $value) {
            $data[strtolower($key)] = $value;
        }

        $data['is_edited'] = (int) ($data['is_edited'] ?? 0);
        $data['edited_fields'] = $this->normalizeEditedFields($data['edited_fields'] ?? null);
        $data['vendor_category_id'] = $data['vendor_category_id'] ?? null;

        return response()->json($data);
    }

    public function store(Request $request)
    {
        $this->ensureVendorSchema();

        $validated = $request->validate([
            'vendor_name' => 'required|string|max:150',
            'vendor_contact_person' => 'required|string|max:100',
            'vendor_phone' => 'required|string|max:20',
            'vendor_email' => 'required|email|max:100',
            'vendor_category_id' => 'required|exists:sp_vendor_category,category_id',
            'vendor_registration_date' => 'required|date',
            'vendor_address' => 'required|string|max:200',
            'supplier' => 'required|string|max:255',
            'ntn_cnic' => 'required|string|max:50',
            'attn' => 'required|string|max:255',
            'beneficiary_name' => 'required|string|max:255',
            'bank_name' => 'required|string|max:255',
            'bank_iban' => 'required|string|max:50',
        ]);

        $validated['is_edited'] = 0;
        $validated['edited_fields'] = json_encode([]);

        Sp_vendor::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Vendor added successfully.',
        ]);
    }

    public function update(Request $request)
    {
        $this->ensureVendorSchema();

        $validated = $request->validate([
            'vendor_id' => 'required|integer',
            'vendor_name' => 'required|string|max:255',
            'vendor_contact_person' => 'required|string|max:100',
            'vendor_phone' => 'required|string|max:20',
            'vendor_email' => 'required|email|max:100',
            'vendor_category_id' => 'required|exists:sp_vendor_category,category_id',
            'vendor_registration_date' => 'required|date',
            'vendor_address' => 'required|string|max:200',
            'supplier' => 'required|string|max:255',
            'ntn_cnic' => 'required|string|max:50',
            'attn' => 'required|string|max:255',
            'beneficiary_name' => 'required|string|max:255',
            'bank_name' => 'required|string|max:255',
            'bank_iban' => 'required|string|max:50',
        ]);

        $vendor = DB::table('sp_vendors')
            ->where('vendor_id', $validated['vendor_id'])
            ->first();

        if (!$vendor) {
            return response()->json(['message' => 'Vendor not found'], 404);
        }

        $updatedValues = [
            'vendor_name' => $validated['vendor_name'],
            'vendor_contact_person' => $validated['vendor_contact_person'],
            'vendor_phone' => $validated['vendor_phone'],
            'vendor_email' => $validated['vendor_email'],
            'vendor_address' => $validated['vendor_address'],
            'vendor_category_id' => $validated['vendor_category_id'],
            'vendor_registration_date' => $validated['vendor_registration_date'],
            'supplier' => $validated['supplier'],
            'ntn_cnic' => $validated['ntn_cnic'],
            'attn' => $validated['attn'],
            'beneficiary_name' => $validated['beneficiary_name'],
            'bank_name' => $validated['bank_name'],
            'bank_iban' => $validated['bank_iban'],
        ];

        $existingEditedFields = $this->normalizeEditedFields($vendor->edited_fields ?? null);
        $newEditedFields = $this->detectEditedFields((array) $vendor, $updatedValues);
        $mergedEditedFields = array_values(array_unique(array_merge($existingEditedFields, $newEditedFields)));

        $updatedValues['is_edited'] = empty($mergedEditedFields) ? (int) ($vendor->is_edited ?? 0) : 1;
        $updatedValues['edited_fields'] = json_encode($mergedEditedFields);

        DB::table('sp_vendors')
            ->where('vendor_id', $validated['vendor_id'])
            ->update($updatedValues);

        return response()->json(['message' => 'Vendor updated successfully!']);
    }

    public function delete($id)
    {
        DB::table('sp_vendors')->where('vendor_id', $id)->delete();

        return response()->json(['message' => 'Vendor deleted successfully!']);
    }

    public function syncPinnacleChunk(Request $request, PinnacleVendorSyncService $syncService)
    {
        $records = $request->input('records', []);

        if (!is_array($records)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid vendor records payload.',
            ], 422);
        }

        $result = $syncService->syncChunk($records);

        return response()->json([
            'status' => 'success',
            'message' => 'Vendor chunk synced successfully.',
            'summary' => $result,
        ]);
    }

    public function list()
    {
        $this->ensureVendorSchema();

        return response()->json(
            Sp_vendor::with(['category', 'masterCategory'])->get()->map(function ($vendor) {
                return [
                    'vendor_id' => $vendor->vendor_id,
                    'vendor_name' => $vendor->vendor_name,
                    'vendor_contact_person' => $vendor->vendor_contact_person,
                    'vendor_email' => $vendor->vendor_email,
                    'vendor_phone' => $vendor->vendor_phone,
                    'vendor_address' => $vendor->vendor_address,
                    'vendor_registration_date' => $vendor->vendor_registration_date,
                    'pinnacle_supplier_id' => $vendor->pinnacle_supplier_id ?? null,
                    'vendor_master_id' => $vendor->vendor_master_id,
                    'category_name' => $vendor->masterCategory->title ?? ($vendor->category->category_name ?? null),
                    'master_category_name' => $vendor->masterCategory->title ?? null,
                    'vendor_category_id' => $vendor->vendor_category_id ?? null,
                    'vendor_category_name' => $vendor->category->category_name ?? null,
                    'vendor_category_pinnacle_account_id' => $vendor->category->pinnacle_account_id ?? null,
                    'supplier' => $vendor->supplier,
                    'ntn_cnic' => $vendor->ntn_cnic,
                    'attn' => $vendor->attn,
                    'beneficiary_name' => $vendor->beneficiary_name,
                    'bank_name' => $vendor->bank_name,
                    'bank_iban' => $vendor->bank_iban,
                    'is_edited' => (int) ($vendor->is_edited ?? 0),
                    'edited_fields' => $this->normalizeEditedFields($vendor->edited_fields ?? null),
                ];
            })
        );
    }

    public function masterCategories()
    {
        $this->ensureVendorSchema();

        return response()->json([
            'data' => VendorMasterCategory::orderBy('title')
                ->get(['master_id', 'title'])
                ->map(function ($category) {
                    return [
                        'master_id' => $category->master_id,
                        'title' => $category->title,
                    ];
                }),
        ]);
    }

    public function storeMasterCategory(Request $request)
    {
        $this->ensureVendorSchema();

        $validated = $request->validate([
            'title' => 'required|string|max:255|unique:sp_vendor_master_category,title',
        ]);

        $masterIdRows = DB::select("SELECT SP_VENDOR_MASTER_CATEGORY_SEQ.NEXTVAL AS NEXT_ID FROM DUAL");
        $masterId = $masterIdRows[0]->next_id ?? $masterIdRows[0]->NEXT_ID ?? null;

        DB::table('sp_vendor_master_category')->insert([
            'master_id' => $masterId,
            'title' => trim($validated['title']),
            'created_at' => DB::raw('SYSTIMESTAMP'),
            'created_by' => auth()->user()->username ?? auth()->user()->name ?? 'system',
        ]);

        return response()->json([
            'message' => 'Vendor category created successfully.',
            'data' => [
                'master_id' => $masterId,
                'title' => trim($validated['title']),
            ],
        ]);
    }

    public function updateMasterCategory(Request $request, $id)
    {
        $this->ensureVendorSchema();

        $validated = $request->validate([
            'title' => 'required|string|max:255|unique:sp_vendor_master_category,title,' . $id . ',master_id',
        ]);

        $updated = DB::table('sp_vendor_master_category')
            ->where('master_id', $id)
            ->update([
                'title' => trim($validated['title']),
                'updated_at' => DB::raw('SYSTIMESTAMP'),
                'updated_by' => auth()->user()->username ?? auth()->user()->name ?? 'system',
            ]);

        if (!$updated) {
            return response()->json(['message' => 'Vendor category not found.'], 404);
        }

        return response()->json([
            'message' => 'Vendor category updated successfully.',
            'data' => [
                'master_id' => $id,
                'title' => trim($validated['title']),
            ],
        ]);
    }

    private function ensureVendorSchema(): void
    {
        // Live: facultyconn uses bnucms objects via public synonyms. Never CREATE/ALTER here.
        try {
            if (!$this->tableExists('SP_VENDORS')) {
                $this->logMissingOracleObject('SP_VENDORS', 'VendorController');
                return;
            }

            if (!$this->tableExists('SP_VENDOR_MASTER_CATEGORY')) {
                $this->logMissingOracleObject('SP_VENDOR_MASTER_CATEGORY', 'VendorController');
            }

            foreach (['IS_EDITED', 'EDITED_FIELDS', 'VENDOR_MASTER_ID'] as $column) {
                if (!$this->columnExists('SP_VENDORS', $column)) {
                    $this->logMissingOracleObject('SP_VENDORS.' . $column, 'VendorController');
                }
            }

            if ($this->columnExists('SP_VENDORS', 'IS_EDITED')) {
                DB::statement("UPDATE SP_VENDORS SET IS_EDITED = 0 WHERE IS_EDITED IS NULL");
            }

            if ($this->columnExists('SP_VENDORS', 'EDITED_FIELDS')) {
                DB::statement("UPDATE SP_VENDORS SET EDITED_FIELDS = '[]' WHERE EDITED_FIELDS IS NULL");
            }
        } catch (Throwable $e) {
            \Log::warning('ensureVendorSchema check failed', ['error' => $e->getMessage()]);
        }
    }

    private function defaultLegacyVendorCategoryId()
    {
        if (!$this->columnExists('SP_VENDORS', 'VENDOR_CATEGORY_ID')) {
            return null;
        }

        $rows = DB::select("
            SELECT CATEGORY_ID
            FROM SP_VENDOR_CATEGORY
            ORDER BY CATEGORY_ID
            FETCH FIRST 1 ROWS ONLY
        ");

        return $rows[0]->category_id ?? $rows[0]->CATEGORY_ID ?? null;
    }

    private function detectEditedFields(array $originalVendor, array $updatedValues): array
    {
        $editedFields = [];

        foreach (self::MANUALLY_TRACKED_FIELDS as $field) {
            $original = $this->normalizeFieldValue($originalVendor[$field] ?? null);
            $updated = $this->normalizeFieldValue($updatedValues[$field] ?? null);

            if ($original !== $updated) {
                $editedFields[] = $field;
            }
        }

        return $editedFields;
    }

    private function normalizeEditedFields($value): array
    {
        if (is_array($value)) {
            return array_values(array_filter($value, 'is_string'));
        }

        if ($value === null || $value === '') {
            return [];
        }

        $decoded = json_decode((string) $value, true);

        if (!is_array($decoded)) {
            return [];
        }

        return array_values(array_filter($decoded, 'is_string'));
    }

    private function normalizeFieldValue($value): ?string
    {
        if ($value === null) {
            return null;
        }

        if (is_string($value)) {
            $trimmed = trim($value);

            return $trimmed === '' ? null : $trimmed;
        }

        return trim((string) $value);
    }
}
