<?php

namespace App\Http\Controllers\StoreProcurement;

use App\Http\Controllers\Controller;
use App\Models\Grader\Departments;
use App\Services\StoreProcurement\PinnacleApiService;
use App\Support\Oracle\ResolvesSynonymObjects;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class CostCenterMappingController extends Controller
{
    use ResolvesSynonymObjects;

    private const COST_CENTER_ENDPOINT = 'cost_centers';
    private const COST_CENTER_CACHE_KEY = 'store_procurement.cost_centers.api';
    private const COST_CENTER_CACHE_TTL_SECONDS = 600;
    // Must be uppercase: live Oracle grammar quotes identifiers as-is.
    // Lowercase "cost_center_mapping" causes ORA-00942 (synonym is COST_CENTER_MAPPING).
    private const MAPPING_TABLE = 'COST_CENTER_MAPPING';

    /** @var bool|null runtime cache: whether CMS_DEPT_IDS is usable */
    private $deptIdsColumnSupported = null;

    private function resolvedMappingTable(): string
    {
        static $resolved = null;

        if ($resolved !== null) {
            return $resolved;
        }

        // 1) Public synonym / default schema
        if ($this->tableExists(self::MAPPING_TABLE)) {
            return $resolved = self::MAPPING_TABLE;
        }

        // 2) Owner schema used elsewhere in this app (facultyconn → bnucms)
        foreach (['BNUCMS', 'bnucms'] as $schema) {
            $qualified = strtoupper($schema) . '.' . self::MAPPING_TABLE;
            try {
                DB::select('SELECT 1 FROM ' . $qualified . ' WHERE 1 = 0');
                return $resolved = $qualified;
            } catch (Throwable $exception) {
                // try next
            }
        }

        // Fall back to synonym name so error messages stay clear.
        return $resolved = self::MAPPING_TABLE;
    }

    private function mappingQuery()
    {
        $table = $this->resolvedMappingTable();

        // Schema-qualified names must not be over-quoted as one identifier.
        if (strpos($table, '.') !== false) {
            return DB::table(DB::raw($table));
        }

        return DB::table($table);
    }

    private function oracleize(array $payload): array
    {
        $mapped = [];

        foreach ($payload as $key => $value) {
            $mapped[strtoupper((string) $key)] = $value;
        }

        return $mapped;
    }

    public function index()
    {
        $schools = DB::table('SCHOOLS')
            ->select('SCHOOL_ID AS school_id', 'SCHOOL_NAME AS school_name')
            ->where('SCHOOL_STATUS', 'E')
            ->orderBy('SCHOOL_ORDER')
            ->orderBy('SCHOOL_NAME')
            ->get();

        $departments = Departments::select('dept_id', 'dept_name', 'dept_school_id')
            ->orderBy('dept_name')
            ->get();

        return view('store_procurement.cost_center_mapping.index', compact('schools', 'departments'));
    }

    public function data(Request $request, PinnacleApiService $pinnacleApiService)
    {
        if ($request->boolean('refresh')) {
            Cache::forget(self::COST_CENTER_CACHE_KEY);
        }

        $dbRows = $this->safeMappingRows();

        try {
            $apiRows = $this->costCentersDataset($pinnacleApiService);
        } catch (ConnectionException $exception) {
            Log::error('Cost center API connection failed.', ['message' => $exception->getMessage()]);

            if (!empty($dbRows)) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Showing saved data. API is currently unreachable.',
                    'data' => $dbRows,
                ]);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Unable to connect to cost center API: ' . $exception->getMessage(),
                'data' => [],
            ], 503);
        } catch (RequestException $exception) {
            Log::error('Cost center API request failed.', [
                'status' => optional($exception->response)->status(),
                'message' => $exception->getMessage(),
            ]);

            if (!empty($dbRows)) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Showing saved data. API request failed.',
                    'data' => $dbRows,
                ]);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Cost center API request failed.',
                'data' => [],
            ], 502);
        } catch (Throwable $exception) {
            Log::error('Unexpected cost center API error.', ['message' => $exception->getMessage()]);

            if (!empty($dbRows)) {
                return response()->json([
                    'status' => 'success',
                    'data' => $dbRows,
                ]);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Unable to load cost centers: ' . $exception->getMessage(),
                'data' => [],
            ], 500);
        }

        if (empty($apiRows)) {
            if (!empty($dbRows)) {
                return response()->json([
                    'status' => 'success',
                    'data' => $dbRows,
                ]);
            }

            return response()->json([
                'status' => 'success',
                'data' => [],
                'message' => 'No cost centers found. Click "Refresh from API" to import.',
            ]);
        }

        $syncMessage = null;

        // Only sync Pinnacle metadata when explicitly refreshing from API.
        // Normal page loads must NOT rewrite rows — that was wiping/obscuring CMS mappings.
        if ($request->boolean('refresh')) {
            try {
                $this->syncFromApi($apiRows);
                $dbRows = $this->safeMappingRows();
            } catch (Throwable $exception) {
                Log::warning('Cost center database sync failed.', ['message' => $exception->getMessage()]);
                $syncMessage = 'Loaded from API. Database sync failed — mappings will not be saved until the table exists.';
            }
        }

        $response = [
            'status' => 'success',
            'data' => $this->mergeApiWithMappings($apiRows, $dbRows),
        ];

        if ($syncMessage) {
            $response['message'] = $syncMessage;
        }

        return response()->json($response)
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate')
            ->header('Pragma', 'no-cache');
    }

    public function store(Request $request)
    {
        $this->ensureDeptIdsColumn();

        $schemaError = $this->assertMappingColumns();
        if ($schemaError) {
            Log::error('Cost center mapping schema error on save.', ['message' => $schemaError]);

            return response()->json([
                'status' => 'error',
                'message' => $schemaError,
            ], 500);
        }

        $validated = $request->validate([
            'cost_center_id' => 'required|string|max:20',
            'cost_center_title' => 'nullable|string|max:255',
            'school_id' => 'nullable|string|max:20',
            'school_title' => 'nullable|string|max:255',
            'cms_school_id' => 'nullable|integer|exists:SCHOOLS,school_id',
            'cms_dept_id' => 'nullable|integer|exists:DEPARTMENTS,dept_id',
            'cms_dept_ids' => 'nullable|array',
            'cms_dept_ids.*' => 'integer|exists:DEPARTMENTS,dept_id',
        ]);

        $costCenterId = trim((string) $validated['cost_center_id']);
        $cmsSchoolId = $validated['cms_school_id'] ?? null;
        $cmsDeptIds = $this->normalizeDeptIds($validated['cms_dept_ids'] ?? ($validated['cms_dept_id'] ?? null));
        $cmsDeptId = $cmsDeptIds[0] ?? null;

        if ($cmsSchoolId === null || $cmsSchoolId === '') {
            if (!$this->clearMapping($costCenterId)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unable to remove mapping.',
                ], 500);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Cost center mapping removed.',
            ]);
        }

        if (empty($cmsDeptIds)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Please select at least one department.',
            ], 422);
        }

        $validDeptCount = DB::table('DEPARTMENTS')
            ->where('DEPT_SCHOOL_ID', $cmsSchoolId)
            ->whereIn('DEPT_ID', $cmsDeptIds)
            ->count();

        if ($validDeptCount !== count($cmsDeptIds)) {
            return response()->json([
                'status' => 'error',
                'message' => 'One or more selected departments do not belong to the selected school.',
            ], 422);
        }

        $saveResult = $this->saveMapping($costCenterId, [
            'title' => $this->cleanValue($validated['cost_center_title'] ?? null),
            'school_id' => $this->cleanValue($validated['school_id'] ?? null),
            'school_title' => $this->cleanValue($validated['school_title'] ?? null),
            'cms_school_id' => (int) $cmsSchoolId,
            'cms_dept_id' => $cmsDeptId,
            'cms_dept_ids' => implode(',', $cmsDeptIds),
        ]);

        if (!$saveResult['ok']) {
            return response()->json([
                'status' => 'error',
                'message' => $saveResult['message'] ?? 'Unable to save mapping.',
            ], 500);
        }

        $saved = $this->findSavedMapping($costCenterId);

        if (!$saved || $saved['cms_school_id'] === null || $saved['cms_school_id'] === '') {
            Log::error('Cost center mapping save reported success but CMS mapping was not persisted.', [
                'cost_center_id' => $costCenterId,
                'cms_school_id' => $cmsSchoolId,
                'cms_dept_ids' => $cmsDeptIds,
                'save_result' => $saveResult,
                'saved' => $saved,
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Mapping did not persist in database. Please contact support.',
                'debug' => config('app.debug') ? [
                    'cost_center_id' => $costCenterId,
                    'save_result' => $saveResult,
                    'saved' => $saved,
                ] : null,
            ], 500);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Cost center mapped successfully.',
            'data' => $saved,
        ]);
    }

    private function safeMappingRows(): array
    {
        try {
            return $this->mappingRows();
        } catch (Throwable $exception) {
            Log::warning('Cost center mapping table read failed.', ['message' => $exception->getMessage()]);

            return [];
        }
    }

    private function mappingRows(): array
    {
        $this->ensureDeptIdsColumn();

        $hasDeptIds = $this->supportsDeptIdsColumn();

        // Read mapping table directly — avoid JOIN failures hiding saved cms_* values.
        $baseColumns = ['ID', 'TITLE', 'SCHOOL_ID', 'SCHOOL_TITLE', 'CMS_SCHOOL_ID', 'CMS_DEPT_ID'];

        try {
            $query = $this->mappingQuery()->select($baseColumns)->orderBy('ID');
            if ($hasDeptIds) {
                $query->addSelect('CMS_DEPT_IDS');
            }
            $rows = $query->get();
        } catch (Throwable $exception) {
            // Column probe can be wrong on synonym setups — retry without CMS_DEPT_IDS.
            Log::warning('Mapping read with CMS_DEPT_IDS failed; retrying without it.', [
                'message' => $exception->getMessage(),
            ]);
            $hasDeptIds = false;
            $this->rememberDeptIdsSupport(false);
            $rows = $this->mappingQuery()->select($baseColumns)->orderBy('ID')->get();
        }

        $schoolIds = $rows->pluck('cms_school_id')->filter(function ($value) {
            return $value !== null && $value !== '';
        })->unique()->values();

        // Oracle PDO CASE_LOWER exposes aliases as school_id/school_name — not SCHOOL_ID.
        try {
            $schoolNames = $schoolIds->isEmpty()
                ? collect()
                : DB::table('SCHOOLS')
                    ->whereIn('SCHOOL_ID', $schoolIds)
                    ->select('SCHOOL_ID as school_id', 'SCHOOL_NAME as school_name')
                    ->get()
                    ->pluck('school_name', 'school_id');
        } catch (Throwable $exception) {
            Log::warning('School name lookup failed for cost center mapping.', [
                'message' => $exception->getMessage(),
            ]);
            $schoolNames = collect();
        }

        return $rows->map(function ($row) use ($schoolNames, $hasDeptIds) {
            if (!$hasDeptIds && !isset($row->cms_dept_ids) && !isset($row->CMS_DEPT_IDS)) {
                $row->cms_dept_ids = null;
            }

            $formatted = $this->formatRow($row);
            $schoolId = $formatted['cms_school_id'];

            $formatted['cms_school_name'] = $schoolId !== null
                ? ($schoolNames[$schoolId] ?? $schoolNames[(string) $schoolId] ?? null)
                : null;

            return $formatted;
        })->values()->all();
    }

    private function formatRow($row): array
    {
        $id = $this->rowValue($row, 'id');
        $cmsSchoolId = $this->rowValue($row, 'cms_school_id');
        $cmsDeptId = $this->rowValue($row, 'cms_dept_id');
        $deptIds = $this->normalizeDeptIds($this->rowValue($row, 'cms_dept_ids'));

        if (empty($deptIds) && $cmsDeptId !== null && $cmsDeptId !== '') {
            $deptIds = [(int) $cmsDeptId];
        }

        try {
            $deptNames = $this->departmentNamesForIds($deptIds);
        } catch (Throwable $exception) {
            Log::warning('Department name lookup failed for cost center mapping.', [
                'message' => $exception->getMessage(),
                'dept_ids' => $deptIds,
            ]);
            $deptNames = [];
        }

        return [
            'cost_center_id' => $id !== null ? (string) $id : '',
            'cost_center_code' => $id !== null ? (string) $id : '',
            'cost_center_title' => $this->rowValue($row, 'title'),
            'school_id' => $this->rowValue($row, 'school_id'),
            'school_title' => $this->rowValue($row, 'school_title'),
            'cms_school_id' => $cmsSchoolId !== null && $cmsSchoolId !== '' ? (int) $cmsSchoolId : null,
            'cms_dept_id' => $deptIds[0] ?? ($cmsDeptId !== null && $cmsDeptId !== '' ? (int) $cmsDeptId : null),
            'cms_dept_ids' => $deptIds,
            'cms_school_name' => $this->rowValue($row, 'cms_school_name'),
            'cms_dept_name' => !empty($deptNames) ? implode(', ', $deptNames) : $this->rowValue($row, 'cms_dept_name'),
        ];
    }

    private function rowValue($row, string $key)
    {
        if (is_array($row)) {
            foreach ([$key, strtoupper($key), strtolower($key)] as $candidate) {
                if (array_key_exists($candidate, $row) && $row[$candidate] !== null && $row[$candidate] !== '') {
                    return $row[$candidate];
                }
            }

            return null;
        }

        foreach ([$key, strtoupper($key), strtolower($key)] as $candidate) {
            if (isset($row->{$candidate}) && $row->{$candidate} !== null && $row->{$candidate} !== '') {
                return $row->{$candidate};
            }
        }

        return null;
    }

    private function normalizeIdKey($id): string
    {
        $id = trim((string) $id);

        if ($id === '') {
            return '';
        }

        // Oracle NUMBER columns drop leading zeros ("03006" <=> "3006").
        if (preg_match('/^\d+$/', $id)) {
            return ltrim($id, '0') ?: '0';
        }

        return $id;
    }

    private function idsMatch($left, $right): bool
    {
        return $this->normalizeIdKey($left) !== ''
            && $this->normalizeIdKey($left) === $this->normalizeIdKey($right);
    }

    private function mergeApiWithMappings(array $apiRows, array $dbRows): array
    {
        $dbById = [];

        foreach ($dbRows as $row) {
            $key = $this->normalizeIdKey($row['cost_center_id'] ?? '');
            if ($key === '') {
                continue;
            }
            $dbById[$key] = $row;
        }

        return array_values(array_map(function (array $apiRow) use ($dbById) {
            $key = $this->normalizeIdKey($apiRow['cost_center_id'] ?? '');
            $dbRow = $key !== '' ? ($dbById[$key] ?? []) : [];

            return array_merge($apiRow, [
                'cms_school_id' => $dbRow['cms_school_id'] ?? null,
                'cms_dept_id' => $dbRow['cms_dept_id'] ?? null,
                'cms_dept_ids' => $dbRow['cms_dept_ids'] ?? (
                    !empty($dbRow['cms_dept_id']) ? [(int) $dbRow['cms_dept_id']] : []
                ),
                'cms_school_name' => $dbRow['cms_school_name'] ?? null,
                'cms_dept_name' => $dbRow['cms_dept_name'] ?? null,
            ]);
        }, $apiRows));
    }

    private function syncFromApi(array $costCenters): void
    {
        foreach ($costCenters as $costCenter) {
            $costCenterId = trim((string) ($costCenter['cost_center_id'] ?? ''));
            if ($costCenterId === '') {
                continue;
            }

            // Only update Pinnacle metadata. Never touch cms_school_id / cms_dept_*.
            $apiFields = $this->oracleize([
                'title' => $costCenter['cost_center_title'],
                'school_id' => $costCenter['school_id'],
                'school_title' => $costCenter['school_title'],
            ]);

            $existing = $this->mappingQuery()->where('ID', $costCenterId)->first();

            // Also match numeric IDs that lost leading zeros in Oracle.
            if (!$existing && preg_match('/^\d+$/', $costCenterId)) {
                $normalized = $this->normalizeIdKey($costCenterId);
                $candidates = $this->mappingQuery()->select('ID')->get();
                foreach ($candidates as $candidate) {
                    if ($this->normalizeIdKey($this->rowValue($candidate, 'id')) === $normalized) {
                        $existing = $candidate;
                        break;
                    }
                }
            }

            if ($existing) {
                $existingId = $this->rowValue($existing, 'id');
                $this->mappingQuery()
                    ->where('ID', $existingId)
                    ->update($apiFields);
            } else {
                $this->mappingQuery()->insert(array_merge(
                    ['ID' => $costCenterId],
                    $apiFields
                ));
            }
        }
    }

    private function findExistingMappingId(string $costCenterId)
    {
        $existing = $this->mappingQuery()->where('ID', $costCenterId)->first();

        if ($existing) {
            return $this->rowValue($existing, 'id');
        }

        // Oracle may store "03006" as 3006 when ID is numeric.
        if (preg_match('/^\d+$/', $costCenterId)) {
            $normalized = $this->normalizeIdKey($costCenterId);
            $padded = str_pad($normalized, strlen($costCenterId), '0', STR_PAD_LEFT);

            foreach ([$normalized, $padded, (int) $normalized] as $candidateId) {
                $existing = $this->mappingQuery()->where('ID', $candidateId)->first();
                if ($existing) {
                    return $this->rowValue($existing, 'id');
                }
            }

            foreach ($this->mappingQuery()->select('ID')->get() as $candidate) {
                if ($this->normalizeIdKey($this->rowValue($candidate, 'id')) === $normalized) {
                    return $this->rowValue($candidate, 'id');
                }
            }
        }

        return null;
    }

    private function findSavedMapping(string $costCenterId): ?array
    {
        try {
            $rowId = $this->findExistingMappingId($costCenterId);

            if ($rowId === null) {
                return null;
            }

            $hasDeptIds = $this->supportsDeptIdsColumn();
            $columns = ['ID', 'TITLE', 'SCHOOL_ID', 'SCHOOL_TITLE', 'CMS_SCHOOL_ID', 'CMS_DEPT_ID'];
            if ($hasDeptIds) {
                $columns[] = 'CMS_DEPT_IDS';
            }

            try {
                $row = $this->mappingQuery()
                    ->select($columns)
                    ->where('ID', $rowId)
                    ->first();
            } catch (Throwable $exception) {
                $this->rememberDeptIdsSupport(false);
                $row = $this->mappingQuery()
                    ->select(['ID', 'TITLE', 'SCHOOL_ID', 'SCHOOL_TITLE', 'CMS_SCHOOL_ID', 'CMS_DEPT_ID'])
                    ->where('ID', $rowId)
                    ->first();
                $hasDeptIds = false;
            }

            if (!$row) {
                return null;
            }

            if (!$hasDeptIds && !isset($row->cms_dept_ids) && !isset($row->CMS_DEPT_IDS)) {
                $row->cms_dept_ids = null;
            }

            $formatted = $this->formatRow($row);

            if (!empty($formatted['cms_school_id'])) {
                $school = DB::table('SCHOOLS')
                    ->where('SCHOOL_ID', $formatted['cms_school_id'])
                    ->select('SCHOOL_NAME as school_name')
                    ->first();
                $formatted['cms_school_name'] = $school->school_name ?? $school->SCHOOL_NAME ?? null;
            }

            return $formatted;
        } catch (Throwable $exception) {
            Log::warning('Cost center mapping direct read failed.', [
                'cost_center_id' => $costCenterId,
                'message' => $exception->getMessage(),
            ]);

            return null;
        }
    }

    private function saveMapping(string $costCenterId, array $payload): array
    {
        try {
            $this->ensureDeptIdsColumn();

            $deptIdsValue = $payload['cms_dept_ids'] ?? null;
            $cmsSchoolId = $payload['cms_school_id'] ?? null;
            $cmsDeptId = $payload['cms_dept_id'] ?? null;

            $meta = $this->oracleize(array_filter([
                'title' => $payload['title'] ?? null,
                'school_id' => $payload['school_id'] ?? null,
                'school_title' => $payload['school_title'] ?? null,
            ], function ($value) {
                return $value !== null;
            }));

            $cms = $this->oracleize([
                'cms_school_id' => $cmsSchoolId,
                'cms_dept_id' => $cmsDeptId,
            ]);

            $selectedDeptCount = count($this->normalizeDeptIds($deptIdsValue));
            $wroteDeptIds = false;

            // Always persist multi-dept list when supported/available.
            if ($deptIdsValue !== null && $deptIdsValue !== '') {
                $cms['CMS_DEPT_IDS'] = $deptIdsValue;
            }

            $existingId = $this->findExistingMappingId($costCenterId);

            if ($existingId !== null) {
                $rowId = $existingId;

                // Keep Pinnacle metadata fresh, but never let metadata update block CMS save.
                try {
                    if (!empty($meta)) {
                        $this->mappingQuery()->where('ID', $rowId)->update($meta);
                    }
                } catch (Throwable $metaException) {
                    Log::warning('Cost center metadata update skipped.', [
                        'cost_center_id' => $costCenterId,
                        'message' => $metaException->getMessage(),
                    ]);
                }
            } else {
                $rowId = preg_match('/^\d+$/', $costCenterId)
                    ? $this->normalizeIdKey($costCenterId)
                    : $costCenterId;

                try {
                    $this->mappingQuery()->insert(array_merge(
                        ['ID' => $rowId],
                        $meta,
                        $cms
                    ));
                    $wroteDeptIds = array_key_exists('CMS_DEPT_IDS', $cms);
                } catch (Throwable $insertException) {
                    // Retry without multi-dept column if it does not exist.
                    unset($cms['CMS_DEPT_IDS']);
                    $this->rememberDeptIdsSupport(false);
                    $this->mappingQuery()->insert(array_merge(
                        ['ID' => $rowId],
                        $meta,
                        $cms
                    ));
                    $wroteDeptIds = false;

                    if ($selectedDeptCount > 1) {
                        return [
                            'ok' => false,
                            'message' => 'Multiple departments require column COST_CENTER_MAPPING.CMS_DEPT_IDS. Ask DBA to add VARCHAR2 column CMS_DEPT_IDS.',
                        ];
                    }

                    Log::warning('Insert without CMS_DEPT_IDS after column error.', [
                        'cost_center_id' => $costCenterId,
                        'message' => $insertException->getMessage(),
                    ]);
                }
            }

            // Critical write: CMS mapping columns (including multi-dept list).
            try {
                $this->mappingQuery()
                    ->where('ID', $rowId)
                    ->update($cms);
                $wroteDeptIds = array_key_exists('CMS_DEPT_IDS', $cms) ? true : $wroteDeptIds;
                if (array_key_exists('CMS_DEPT_IDS', $cms)) {
                    $this->rememberDeptIdsSupport(true);
                }
            } catch (Throwable $updateException) {
                unset($cms['CMS_DEPT_IDS']);
                $this->rememberDeptIdsSupport(false);
                $this->mappingQuery()
                    ->where('ID', $rowId)
                    ->update($cms);
                $wroteDeptIds = false;

                if ($selectedDeptCount > 1) {
                    return [
                        'ok' => false,
                        'message' => 'Multiple departments require column COST_CENTER_MAPPING.CMS_DEPT_IDS. Ask DBA to add VARCHAR2 column CMS_DEPT_IDS. Error: ' . $updateException->getMessage(),
                    ];
                }

                Log::warning('Update without CMS_DEPT_IDS after column error.', [
                    'cost_center_id' => $costCenterId,
                    'message' => $updateException->getMessage(),
                ]);
            }

            $saved = $this->findSavedMapping((string) $rowId);
            if (!$saved) {
                $saved = $this->findSavedMapping($costCenterId);
            }

            if (!$saved || $saved['cms_school_id'] === null || $saved['cms_school_id'] === '') {
                // Last-resort raw read for clearer diagnostics.
                $rawSchoolId = $this->mappingQuery()->where('ID', $rowId)->value('CMS_SCHOOL_ID');

                return [
                    'ok' => false,
                    'message' => 'Unable to save mapping: CMS school was not written to COST_CENTER_MAPPING.'
                        . ($rawSchoolId === null || $rawSchoolId === ''
                            ? ' (CMS_SCHOOL_ID still empty after UPDATE)'
                            : ' (saved but could not re-read mapped row)'),
                    'row_id' => $rowId,
                    'raw_cms_school_id' => $rawSchoolId,
                    'saved' => $saved,
                ];
            }

            // If multi-dept was requested but only one came back, column is missing/unreadable.
            $savedDeptCount = count($saved['cms_dept_ids'] ?? []);
            if ($selectedDeptCount > 1 && $savedDeptCount < $selectedDeptCount && !$wroteDeptIds) {
                return [
                    'ok' => false,
                    'message' => 'Multiple departments require column COST_CENTER_MAPPING.CMS_DEPT_IDS. Ask DBA to add VARCHAR2 column CMS_DEPT_IDS.',
                    'saved' => $saved,
                ];
            }

            return [
                'ok' => true,
                'row_id' => $rowId,
                'saved' => $saved,
            ];
        } catch (Throwable $exception) {
            Log::error('Cost center mapping save failed.', [
                'cost_center_id' => $costCenterId,
                'message' => $exception->getMessage(),
            ]);

            return [
                'ok' => false,
                'message' => 'Unable to save mapping: ' . $exception->getMessage(),
            ];
        }
    }

    private function clearMapping(string $costCenterId): bool
    {
        try {
            $this->ensureDeptIdsColumn();

            $payload = [
                'CMS_SCHOOL_ID' => null,
                'CMS_DEPT_ID' => null,
            ];

            if ($this->supportsDeptIdsColumn()) {
                $payload['CMS_DEPT_IDS'] = null;
            }

            $updated = $this->mappingQuery()
                ->where('ID', $costCenterId)
                ->update($payload);

            if (!$updated && preg_match('/^\d+$/', $costCenterId)) {
                $normalized = $this->normalizeIdKey($costCenterId);
                foreach ($this->mappingQuery()->select('ID')->get() as $candidate) {
                    if ($this->normalizeIdKey($this->rowValue($candidate, 'id')) === $normalized) {
                        $this->mappingQuery()
                            ->where('ID', $this->rowValue($candidate, 'id'))
                            ->update($payload);
                        break;
                    }
                }
            }

            return true;
        } catch (Throwable $exception) {
            Log::error('Cost center mapping clear failed.', [
                'cost_center_id' => $costCenterId,
                'message' => $exception->getMessage(),
            ]);

            return false;
        }
    }

    private function normalizeDeptIds($value): array
    {
        if ($value === null || $value === '') {
            return [];
        }

        if (!is_array($value)) {
            $value = preg_split('/\s*,\s*/', (string) $value) ?: [];
        }

        return array_values(array_unique(array_filter(array_map(function ($id) {
            $id = trim((string) $id);

            return $id !== '' && is_numeric($id) ? (int) $id : null;
        }, $value))));
    }

    private function departmentNamesForIds(array $deptIds): array
    {
        if (empty($deptIds)) {
            return [];
        }

        // Use lowercase aliases — Oracle CASE_LOWER breaks pluck('DEPT_NAME', 'DEPT_ID').
        $rows = DB::table('DEPARTMENTS')
            ->whereIn('DEPT_ID', $deptIds)
            ->select('DEPT_ID as dept_id', 'DEPT_NAME as dept_name')
            ->get()
            ->pluck('dept_name', 'dept_id');

        $names = [];

        foreach ($deptIds as $deptId) {
            $name = $rows[$deptId] ?? $rows[(string) $deptId] ?? null;
            if ($name) {
                $names[] = $name;
            }
        }

        return $names;
    }

    private function ensureDeptIdsColumn(): void
    {
        static $ensured = false;

        if ($ensured) {
            return;
        }

        $ensured = true;

        // Live: facultyconn → public synonyms → bnucms. No CREATE/ALTER from app.
        try {
            if (!$this->tableExists(self::MAPPING_TABLE)) {
                $this->logMissingOracleObject(self::MAPPING_TABLE, 'CostCenterMappingController');
                return;
            }

            foreach (['CMS_SCHOOL_ID', 'CMS_DEPT_ID', 'CMS_DEPT_IDS'] as $column) {
                if (!$this->columnExists(self::MAPPING_TABLE, $column)) {
                    $this->logMissingOracleObject(self::MAPPING_TABLE . '.' . $column, 'CostCenterMappingController');
                }
            }

            // Warm the runtime support flag from a real probe.
            $this->supportsDeptIdsColumn();
        } catch (Throwable $exception) {
            Log::warning('Unable to check cost_center_mapping table.', [
                'message' => $exception->getMessage(),
            ]);
        }
    }

    private function supportsDeptIdsColumn(): bool
    {
        if ($this->deptIdsColumnSupported !== null) {
            return $this->deptIdsColumnSupported;
        }

        try {
            $table = $this->resolvedMappingTable();
            DB::select('SELECT CMS_DEPT_IDS FROM ' . $table . ' WHERE 1 = 0');
            return $this->deptIdsColumnSupported = true;
        } catch (Throwable $exception) {
            // Fall back to dictionary/synonym helper.
        }

        try {
            return $this->deptIdsColumnSupported = $this->columnExists(self::MAPPING_TABLE, 'CMS_DEPT_IDS');
        } catch (Throwable $exception) {
            return $this->deptIdsColumnSupported = false;
        }
    }

    private function rememberDeptIdsSupport(bool $supported): void
    {
        $this->deptIdsColumnSupported = $supported;
    }

    private function assertMappingColumns(): ?string
    {
        if (!$this->tableExists(self::MAPPING_TABLE)) {
            return 'Table cost_center_mapping is missing (or not visible via synonym).';
        }

        if (!$this->columnExists(self::MAPPING_TABLE, 'CMS_SCHOOL_ID')) {
            return 'Column COST_CENTER_MAPPING.CMS_SCHOOL_ID is missing. Ask DBA to add it.';
        }

        if (!$this->columnExists(self::MAPPING_TABLE, 'CMS_DEPT_ID')) {
            return 'Column COST_CENTER_MAPPING.CMS_DEPT_ID is missing. Ask DBA to add it.';
        }

        return null;
    }

    private function costCentersDataset(PinnacleApiService $pinnacleApiService): array
    {
        $cached = Cache::get(self::COST_CENTER_CACHE_KEY);

        if (is_array($cached) && !empty($cached)) {
            return $cached;
        }

        $response = $pinnacleApiService->fetch(self::COST_CENTER_ENDPOINT);

        $records = $this->extractApiRecords($response);

        $mapped = array_values(array_filter(array_map(function ($item) {
            $id = $this->firstValue($item, ['ID', 'Id', 'id', 'CostCenterID', 'cost_center_id', 'CostCenterId']);
            $code = $this->firstValue($item, ['Code', 'code', 'CostCenterCode', 'cost_center_code', 'CC_CODE', 'CostCenter']);
            $title = $this->firstValue($item, ['Title', 'title', 'Name', 'name', 'CostCenterTitle', 'cost_center_title', 'Description', 'description']);
            $schoolId = $this->firstValue($item, ['School_ID', 'school_id', 'SchoolId', 'SchoolID']);
            $schoolTitle = $this->firstValue($item, ['School_Title', 'school_title', 'SchoolTitle']);

            $id = $this->cleanValue($id) ?: $this->cleanValue($code);
            $code = $this->cleanValue($code) ?: $id;
            $title = $this->cleanValue($title) ?: $code;

            if (!$id) {
                return null;
            }

            return [
                'cost_center_id' => (string) $id,
                'cost_center_code' => $code,
                'cost_center_title' => $title,
                'school_id' => $this->cleanValue($schoolId),
                'school_title' => $this->cleanValue($schoolTitle),
            ];
        }, $records)));

        if (!empty($mapped)) {
            Cache::put(self::COST_CENTER_CACHE_KEY, $mapped, self::COST_CENTER_CACHE_TTL_SECONDS);
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

        if ($this->isListArray($response)) {
            return $response;
        }

        return [];
    }

    private function isListArray(array $array): bool
    {
        if ($array === []) {
            return true;
        }

        if (function_exists('array_is_list')) {
            return array_is_list($array);
        }

        return array_keys($array) === range(0, count($array) - 1);
    }

    private function firstValue(array $row, array $keys)
    {
        foreach ($keys as $key) {
            if (array_key_exists($key, $row) && $row[$key] !== null && trim((string) $row[$key]) !== '') {
                return $row[$key];
            }
        }

        return null;
    }

    private function cleanValue($value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }
}
