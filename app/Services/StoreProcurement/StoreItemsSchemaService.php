<?php

namespace App\Services\StoreProcurement;

use App\Support\Oracle\ResolvesSynonymObjects;
use Illuminate\Support\Facades\Log;

/**
 * Live-safe schema checks only.
 * Tables live in bnucms and are used via public synonyms by facultyconn.
 * App never runs CREATE/ALTER on live.
 */
class StoreItemsSchemaService
{
    use ResolvesSynonymObjects;

    public function ensure(): bool
    {
        try {
            foreach ([
                'SP_STORE_BUDGET_HEADS',
                'SP_STORE_BUDGET_ITEMS',
                'SP_QUOTATION_REQUEST',
            ] as $table) {
                if (!$this->tableExists($table)) {
                    $this->logMissingOracleObject($table, 'StoreItemsSchemaService::ensure');
                    return false;
                }
            }

            $this->ensureHandoverTables();

            return $this->columnExists('SP_QUOTATION_REQUEST', 'REQUEST_TYPE');
        } catch (\Throwable $e) {
            Log::warning('Store items schema ensure failed.', ['message' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Department handover / issue request records for received store items.
     */
    public function ensureHandoverTables(): bool
    {
        try {
            if (!$this->tableExists('SP_STORE_ITEM_REQUESTS')) {
                $this->logMissingOracleObject('SP_STORE_ITEM_REQUESTS', 'StoreItemsSchemaService::ensureHandoverTables');
                return false;
            }

            return true;
        } catch (\Throwable $e) {
            Log::warning('Store item handover schema ensure failed.', ['message' => $e->getMessage()]);
            return false;
        }
    }

    public function hasRequestTypeColumn(): bool
    {
        try {
            return $this->columnExists('SP_QUOTATION_REQUEST', 'REQUEST_TYPE');
        } catch (\Throwable $e) {
            return false;
        }
    }
}
