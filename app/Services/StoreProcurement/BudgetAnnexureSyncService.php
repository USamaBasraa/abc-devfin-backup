<?php

namespace App\Services\StoreProcurement;

use App\Models\AcademicYear;
use App\Models\StoreProcurement\BudgetHead;
use App\Models\StoreProcurement\BudgetItem;
use App\Support\Oracle\ResolvesSynonymObjects;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class BudgetAnnexureSyncService
{
    use ResolvesSynonymObjects;

    private const API_ENDPOINT = 'budget_annexure';

    public function sync(int $deptId, ?int $ayId = null, ?string $username = null): array
    {
        $this->ensureColumns();

        $records = $this->extractRecords(
            app(PinnacleApiService::class)->fetch(self::API_ENDPOINT)
        );

        if (empty($records)) {
            return [
                'heads_created' => 0,
                'heads_updated' => 0,
                'items_created' => 0,
                'items_updated' => 0,
                'filtered' => true,
                'message' => 'No records returned from Pinnacle API.',
            ];
        }

        $filteredRecords = $this->filterByDepartment($records, $deptId);
        $filtered = count($filteredRecords) !== count($records);

        if (empty($filteredRecords)) {
            return [
                'heads_created' => 0,
                'heads_updated' => 0,
                'items_created' => 0,
                'items_updated' => 0,
                'filtered' => $filtered,
                'message' => 'No budget records matched this department. Map the cost center to dept '
                    . $deptId . ' on the Cost Center Mapping page, then sync again.',
            ];
        }

        $ayId = $ayId ?: (int) AcademicYear::latest('ay_id')->value('ay_id');
        $username = $username ?: 'system';
        $mappingIndex = $this->mappingsForDept($deptId);

        $stats = [
            'heads_created' => 0,
            'heads_updated' => 0,
            'items_created' => 0,
            'items_updated' => 0,
            'filtered' => $filtered,
        ];

        DB::beginTransaction();

        try {
            $grouped = collect($filteredRecords)->groupBy(function (array $row) {
                $accountId = trim((string) ($row['Account_ID'] ?? ''));
                $costCenterId = $this->normalizeCostCenterId($row['CostCenter_ID'] ?? '');

                return $accountId . '|' . $costCenterId;
            });

            foreach ($grouped as $groupKey => $rows) {
                $firstRow = $rows->first();
                $accountId = trim((string) ($firstRow['Account_ID'] ?? ''));

                if ($accountId === '') {
                    continue;
                }

                $mapping = $this->resolveMappingForRow($firstRow, $mappingIndex);

                if ($mapping === null || empty($mapping['cms_dept_id'])) {
                    continue;
                }

                $headDeptId = (int) $mapping['cms_dept_id'];
                $accountTitle = $this->cleanValue($firstRow['Account_Title'] ?? null) ?? $accountId;
                $headTitle = mb_substr($accountTitle, 0, 150);

                $head = BudgetHead::where('account_id', $accountId)
                    ->where('dept_id', $headDeptId)
                    ->where('ay_id', $ayId)
                    ->first();

                if ($head) {
                    $head->update([
                        'dept_id' => $headDeptId,
                        'account_title' => $accountTitle,
                        'head_title' => $headTitle,
                        'school_id' => $mapping['school_id'],
                        'school_title' => $mapping['school_title'],
                    ]);
                    $stats['heads_updated']++;
                } else {
                    $head = BudgetHead::create([
                        'dept_id' => $headDeptId,
                        'account_id' => $accountId,
                        'account_title' => $accountTitle,
                        'head_title' => $headTitle,
                        'school_id' => $mapping['school_id'],
                        'school_title' => $mapping['school_title'],
                        'ay_id' => $ayId,
                        'entry_username' => $username,
                    ]);
                    $stats['heads_created']++;
                }

                foreach ($rows as $row) {
                    $itemStats = $this->syncItem($head->bh_id, $row, $ayId, $username);
                    $stats['items_created'] += $itemStats['created'];
                    $stats['items_updated'] += $itemStats['updated'];
                }
            }

            DB::commit();

            return $stats;
        } catch (Throwable $exception) {
            DB::rollBack();

            Log::error('Budget annexure sync failed.', [
                'dept_id' => $deptId,
                'message' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }

    private function syncItem(int $bhId, array $row, int $ayId, string $username): array
    {
        $pinId = $this->cleanValue($row['Item_ID'] ?? null);
        $itemTitle = $this->cleanValue($row['Item_Title'] ?? null);

        if ($itemTitle === null) {
            return ['created' => 0, 'updated' => 0];
        }

        [$quantity, $rate] = $this->resolveQuantityAndRate($row);

        // AMOUNT is a virtual column (quantity * rate) — do not insert it directly.
        $payload = [
            'bh_id' => $bhId,
            'item_title' => $itemTitle,
            'quantity' => $quantity,
            'rate' => $rate,
            'remarks' => $this->cleanValue($row['Remarks'] ?? null),
            'budget_type' => 'budgeted',
            'purchase_mode' => 'purchase',
            'ay_id' => $ayId,
            'entry_username' => $username,
        ];

        if ($this->hasBudgetDatesColumns()) {
            $payload['budget_from'] = $this->parseDateValue($row['BudgetFrom'] ?? $row['budget_from'] ?? null);
            $payload['budget_to'] = $this->parseDateValue($row['BudgetTo'] ?? $row['budget_to'] ?? null);
        }

        if ($pinId !== null) {
            $payload['pin_id'] = $pinId;
            // Since PIN_ID is unique across the table (UK_SP_BUDGET_ITEMS_PIN_ID),
            // match by pin_id first to prevent duplicate key violation
            $existing = BudgetItem::where('pin_id', $pinId)->first();
        } else {
            $existing = BudgetItem::where('bh_id', $bhId)->where('item_title', $itemTitle)->first();
        }

        if ($existing) {
            $existing->update($payload);

            return ['created' => 0, 'updated' => 1];
        }

        BudgetItem::create($payload);

        return ['created' => 1, 'updated' => 0];
    }

    private function filterByDepartment(array $records, int $deptId): array
    {
        $mappedCostCenterIds = $this->mappedCostCenterIdsForDept($deptId);

        if (empty($mappedCostCenterIds)) {
            return [];
        }

        return array_values(array_filter($records, function (array $row) use ($mappedCostCenterIds) {
            return $this->rowMatchesMappedCostCenters($row, $mappedCostCenterIds);
        }));
    }

    private function mappedCostCenterIdsForDept(int $deptId): array
    {
        try {
            return DB::table('cost_center_mapping')
                ->whereNotNull('cms_school_id')
                ->get()
                ->filter(function ($row) use ($deptId) {
                    return in_array($deptId, $this->deptIdsFromMappingRow($row), true);
                })
                ->pluck('id')
                ->map(function ($id) {
                    return $this->normalizeCostCenterId($id);
                })
                ->filter()
                ->unique()
                ->values()
                ->all();
        } catch (Throwable $exception) {
            Log::warning('Cost center mapping lookup failed during budget sync.', [
                'dept_id' => $deptId,
                'message' => $exception->getMessage(),
            ]);

            return [];
        }
    }

    private function rowMatchesMappedCostCenters(array $row, array $mappedCostCenterIds): bool
    {
        $rowCostCenterId = $this->normalizeCostCenterId($row['CostCenter_ID'] ?? '');

        if ($rowCostCenterId === '') {
            return false;
        }

        foreach ($mappedCostCenterIds as $mappedId) {
            if ($this->costCenterIdsMatch($rowCostCenterId, $mappedId)) {
                return true;
            }
        }

        return false;
    }

    private function costCenterIdsMatch(string $left, string $right): bool
    {
        if ($left === $right) {
            return true;
        }

        $leftStripped = ltrim($left, '0') ?: '0';
        $rightStripped = ltrim($right, '0') ?: '0';

        return $leftStripped === $rightStripped;
    }

    private function normalizeCostCenterId($value): string
    {
        return trim((string) $value);
    }

    private function extractRecords(array $response): array
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

    private function mappingsForDept(int $deptId): array
    {
        try {
            $rows = DB::table('cost_center_mapping')
                ->whereNotNull('cms_school_id')
                ->get()
                ->filter(function ($row) use ($deptId) {
                    return in_array($deptId, $this->deptIdsFromMappingRow($row), true);
                });

            $index = [];

            foreach ($rows as $row) {
                $mapping = [
                    'school_id' => $this->cleanValue($row->school_id ?? null),
                    'school_title' => $this->cleanValue($row->school_title ?? null),
                    'cms_dept_id' => $deptId,
                ];

                $normalizedId = $this->normalizeCostCenterId($row->id ?? '');
                if ($normalizedId === '') {
                    continue;
                }

                $index[$normalizedId] = $mapping;

                $strippedId = ltrim($normalizedId, '0') ?: '0';
                if ($strippedId !== $normalizedId) {
                    $index[$strippedId] = $mapping;
                }
            }

            return $index;
        } catch (Throwable $exception) {
            Log::warning('Cost center mapping preload failed during budget sync.', [
                'dept_id' => $deptId,
                'message' => $exception->getMessage(),
            ]);

            return [];
        }
    }

    private function deptIdsFromMappingRow($row): array
    {
        $ids = [];

        $csv = trim((string) ($row->cms_dept_ids ?? ''));
        if ($csv !== '') {
            foreach (preg_split('/\s*,\s*/', $csv) ?: [] as $id) {
                if ($id !== '' && is_numeric($id)) {
                    $ids[] = (int) $id;
                }
            }
        }

        if (empty($ids) && !empty($row->cms_dept_id)) {
            $ids[] = (int) $row->cms_dept_id;
        }

        return array_values(array_unique($ids));
    }

    private function resolveMappingForRow(array $row, array $mappingIndex): ?array
    {
        $costCenterId = $this->normalizeCostCenterId($row['CostCenter_ID'] ?? '');

        if ($costCenterId === '' || empty($mappingIndex)) {
            return null;
        }

        if (isset($mappingIndex[$costCenterId])) {
            return $mappingIndex[$costCenterId];
        }

        $strippedId = ltrim($costCenterId, '0') ?: '0';
        if (isset($mappingIndex[$strippedId])) {
            return $mappingIndex[$strippedId];
        }

        foreach ($mappingIndex as $mappedId => $mapping) {
            if ($this->costCenterIdsMatch($costCenterId, (string) $mappedId)) {
                return $mapping;
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

    private function resolveQuantityAndRate(array $row): array
    {
        $quantity = (float) ($row['BudgetedQty'] ?? 0);
        $rate = (float) ($row['BudgetedRate'] ?? 0);
        $amount = (float) ($row['BudgetedAmount'] ?? 0);

        if ($quantity > 0) {
            if ($rate <= 0 && $amount > 0) {
                $rate = $amount / $quantity;
            }

            return [$quantity, $rate];
        }

        if ($amount > 0) {
            return [1, $amount];
        }

        if ($rate > 0) {
            return [1, $rate];
        }

        return [0, 0];
    }

    private function parseDateValue($value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);
        if ($value === '') {
            return null;
        }

        try {
            return Carbon::parse($value)->format('Y-m-d H:i:s');
        } catch (Throwable $exception) {
            return null;
        }
    }

    private function hasBudgetDatesColumns(): bool
    {
        return $this->columnExists('SP_BUDGET_ITEMS', 'BUDGET_FROM')
            && $this->columnExists('SP_BUDGET_ITEMS', 'BUDGET_TO');
    }

    private function ensureColumns(): void
    {
        // Live: facultyconn → synonyms → bnucms. Never ALTER from app.
        foreach (['ACCOUNT_ID', 'ACCOUNT_TITLE', 'SCHOOL_ID', 'SCHOOL_TITLE'] as $column) {
            if (!$this->columnExists('SP_BUDGET_HEADS', $column)) {
                $this->logMissingOracleObject('SP_BUDGET_HEADS.' . $column, 'BudgetAnnexureSyncService');
            }
        }

        foreach (['BUDGET_FROM', 'BUDGET_TO'] as $column) {
            if (!$this->columnExists('SP_BUDGET_ITEMS', $column)) {
                $this->logMissingOracleObject('SP_BUDGET_ITEMS.' . $column, 'BudgetAnnexureSyncService');
            }
        }
    }
}
