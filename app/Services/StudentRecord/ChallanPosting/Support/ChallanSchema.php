<?php

namespace App\Services\StudentRecord\ChallanPosting\Support;

use DB;

/**
 * Column-existence checks for student_challan and the scholarship tables.
 *
 * Several of the columns this module writes are added by hand-run DDL scripts that may
 * not have been applied yet, so every write is filtered against the columns that
 * actually exist: a missing one is dropped instead of raising ORA-00904 and losing the
 * whole challan.
 */
class ChallanSchema
{
    /** @var array<string, bool> table.COLUMN => exists */
    private static $cache = [];

    /**
     * Does $table have $column?
     *
     * A plain identifier is matched case-insensitively, because Oracle folds unquoted
     * names to upper case and the query builder does the same — array keys here are
     * written in both cases across the codebase.
     */
    public static function hasColumn($table, $column)
    {
        if (self::hasColumnExact($table, $column)) {
            return true;
        }

        $column = trim((string) $column);
        if (!preg_match('/^[A-Za-z0-9_$#]+$/', $column)) {
            return false;
        }

        return self::hasColumnExact($table, strtoupper($column));
    }

    /**
     * hasColumn()'s engine: ask the TABLE, never the data dictionary.
     *
     * This counted rows in user_tab_columns until 2026-08-25, which answers a different
     * question than the one being asked. That view lists what the connected user OWNS.
     * On the dev database the app connects AS the schema owner, so it happened to agree.
     * In production every application has its own user holding GRANTS on the BNUCMS
     * tables and owning nothing, so the view is empty: every column probe returned false,
     * filter() discarded the whole row, and the insert went out as
     * `insert into "STUDENT_CHALLAN" () values ()` — ORA-00928 with a RETURNING clause,
     * and before that a silent no-op, because Laravel's insert() returns true on an empty
     * array without going near the database. That is what surfaced as "the challan was
     * inserted but its id could not be read back".
     *
     * Selecting the column under an impossible predicate asks the question that actually
     * matters — can THIS connection reach this column — and it needs no owner name, so it
     * works through a grant or a synonym without the schema ever being written down. The
     * two other probes in this codebase already do exactly this, which is precisely why
     * Save kept working on an install where Post could not:
     * ResolvesScholarshipFees::tableColumnExists() and
     * ScholarshipFormController::schemaHasColumn().
     *
     * Both identifiers are interpolated, so both are whitelisted HERE rather than at the
     * caller — hasColumn() passes the raw value in before it validates anything.
     */
    private static function hasColumnExact($table, $column)
    {
        $table  = trim((string) $table);
        $column = trim((string) $column);

        $key = strtoupper($table) . '.' . $column;
        if (array_key_exists($key, self::$cache)) {
            return self::$cache[$key];
        }

        if (!preg_match('/^[A-Za-z0-9_$#.]+$/', $table)
            || !preg_match('/^[A-Za-z0-9_$#]+$/', $column)) {
            return self::$cache[$key] = false;
        }

        try {
            DB::selectOne("SELECT {$column} AS probe_col FROM {$table} WHERE 1 = 0");

            return self::$cache[$key] = true;
        } catch (\Throwable $e) {
            return self::$cache[$key] = false;
        }
    }

    /** Drop every key of $data whose column does not exist on $table. */
    public static function filter($table, array $data)
    {
        $kept = [];
        foreach ($data as $column => $value) {
            if (self::hasColumn($table, $column)) {
                $kept[$column] = $value;
            }
        }

        return $kept;
    }

    /** Test seam — the cache is process-local and never invalidated otherwise. */
    public static function flush()
    {
        self::$cache = [];
    }
}
