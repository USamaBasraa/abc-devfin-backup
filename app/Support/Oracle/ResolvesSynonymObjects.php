<?php

namespace App\Support\Oracle;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use Throwable;

/**
 * Live-safe Oracle object checks for apps that connect as a non-owner user
 * (e.g. facultyconn) while tables live in another schema (e.g. bnucms)
 * and are exposed via public synonyms.
 *
 * Never runs DDL — live connect users typically have DML only.
 */
trait ResolvesSynonymObjects
{
    protected function oracleIdent(string $name): string
    {
        $name = strtoupper(trim($name));

        if (!preg_match('/^[A-Z][A-Z0-9_$#]*$/', $name)) {
            throw new InvalidArgumentException('Invalid Oracle identifier: ' . $name);
        }

        return $name;
    }

    protected function tableExists(string $tableName): bool
    {
        $table = $this->oracleIdent($tableName);

        try {
            DB::select("SELECT 1 FROM {$table} WHERE 1 = 0");

            return true;
        } catch (Throwable $probeException) {
            // Fall through to dictionary views.
        }

        try {
            $rows = DB::select("
                SELECT COUNT(*) AS TOTAL FROM (
                    SELECT 1 AS X FROM ALL_TABLES WHERE TABLE_NAME = :t1
                    UNION ALL
                    SELECT 1 FROM ALL_SYNONYMS WHERE SYNONYM_NAME = :t2
                    UNION ALL
                    SELECT 1 FROM USER_SYNONYMS WHERE SYNONYM_NAME = :t3
                )
            ", [
                't1' => $table,
                't2' => $table,
                't3' => $table,
            ]);

            return (int) ($rows[0]->total ?? $rows[0]->TOTAL ?? 0) > 0;
        } catch (Throwable $exception) {
            return false;
        }
    }

    protected function columnExists(string $tableName, string $columnName): bool
    {
        $table = $this->oracleIdent($tableName);
        $column = $this->oracleIdent($columnName);

        try {
            DB::select("SELECT {$column} FROM {$table} WHERE 1 = 0");

            return true;
        } catch (Throwable $probeException) {
            // Fall through to dictionary views (resolve synonyms to owner.table).
        }

        try {
            $rows = DB::select("
                SELECT COUNT(*) AS TOTAL
                FROM ALL_TAB_COLUMNS C
                WHERE C.COLUMN_NAME = :column_name
                  AND (
                      C.TABLE_NAME = :table_name
                      OR EXISTS (
                          SELECT 1
                          FROM ALL_SYNONYMS S
                          WHERE S.SYNONYM_NAME = :synonym_name
                            AND S.TABLE_NAME = C.TABLE_NAME
                            AND (S.TABLE_OWNER IS NULL OR S.TABLE_OWNER = C.OWNER)
                      )
                      OR EXISTS (
                          SELECT 1
                          FROM USER_SYNONYMS S
                          WHERE S.SYNONYM_NAME = :user_synonym_name
                            AND S.TABLE_NAME = C.TABLE_NAME
                            AND (S.TABLE_OWNER IS NULL OR S.TABLE_OWNER = C.OWNER)
                      )
                  )
            ", [
                'column_name' => $column,
                'table_name' => $table,
                'synonym_name' => $table,
                'user_synonym_name' => $table,
            ]);

            return (int) ($rows[0]->total ?? $rows[0]->TOTAL ?? 0) > 0;
        } catch (Throwable $exception) {
            return false;
        }
    }

    protected function sequenceExists(string $sequenceName): bool
    {
        $sequence = $this->oracleIdent($sequenceName);

        try {
            $rows = DB::select("
                SELECT COUNT(*) AS TOTAL FROM (
                    SELECT 1 AS X FROM ALL_SEQUENCES WHERE SEQUENCE_NAME = :s1
                    UNION ALL
                    SELECT 1 FROM ALL_SYNONYMS WHERE SYNONYM_NAME = :s2
                    UNION ALL
                    SELECT 1 FROM USER_SYNONYMS WHERE SYNONYM_NAME = :s3
                )
            ", [
                's1' => $sequence,
                's2' => $sequence,
                's3' => $sequence,
            ]);

            return (int) ($rows[0]->total ?? $rows[0]->TOTAL ?? 0) > 0;
        } catch (Throwable $exception) {
            return false;
        }
    }

    protected function columnIsNullable(string $tableName, string $columnName): bool
    {
        $table = $this->oracleIdent($tableName);
        $column = $this->oracleIdent($columnName);

        try {
            $rows = DB::select("
                SELECT C.NULLABLE
                FROM ALL_TAB_COLUMNS C
                WHERE C.COLUMN_NAME = :column_name
                  AND (
                      C.TABLE_NAME = :table_name
                      OR EXISTS (
                          SELECT 1
                          FROM ALL_SYNONYMS S
                          WHERE S.SYNONYM_NAME = :synonym_name
                            AND S.TABLE_NAME = C.TABLE_NAME
                            AND (S.TABLE_OWNER IS NULL OR S.TABLE_OWNER = C.OWNER)
                      )
                      OR EXISTS (
                          SELECT 1
                          FROM USER_SYNONYMS S
                          WHERE S.SYNONYM_NAME = :user_synonym_name
                            AND S.TABLE_NAME = C.TABLE_NAME
                            AND (S.TABLE_OWNER IS NULL OR S.TABLE_OWNER = C.OWNER)
                      )
                  )
                FETCH FIRST 1 ROWS ONLY
            ", [
                'column_name' => $column,
                'table_name' => $table,
                'synonym_name' => $table,
                'user_synonym_name' => $table,
            ]);

            if (empty($rows)) {
                return true;
            }

            $nullable = $rows[0]->nullable ?? $rows[0]->NULLABLE ?? 'Y';

            return strtoupper((string) $nullable) === 'Y';
        } catch (Throwable $exception) {
            return true;
        }
    }

    protected function logMissingOracleObject(string $object, string $context = ''): void
    {
        Log::warning('Oracle object not reachable via connect user (synonym/privileges).', [
            'object' => $object,
            'context' => $context,
            'hint' => 'Live connect user (e.g. facultyconn) has no owned objects; tables are in bnucms via public synonyms. Do not run DDL from the app.',
        ]);
    }
}
