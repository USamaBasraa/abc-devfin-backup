<?php

namespace App\Http\Controllers\Grader\StudentRecord\Concerns;

use DB;

/**
 * Which session a Student Record page carries scholarships forward FROM.
 *
 * ---------------------------------------------------------------------------
 * SUMMER IS SKIPPED.
 *
 * Sessions run Spring -> Summer -> Fall, so the row immediately before Fall 2026
 * is Summer 2026 — a short optional term most students never enrol in. Reading
 * from it meant "Import Previous Scholarships" on a Fall student returned the
 * handful of summer holders instead of the Spring cohort, which is the set that
 * actually continues.
 *
 * Both markers are tested because either could be the one that is maintained:
 * the code is SUM<yy> and the name is "Summer <year>" on every summer row in the
 * table. NOT LIKE rather than a list, so additional summers need no change.
 *
 * This is the same rule the bulk page applies in
 * App\Http\Controllers\StudentFinancial\ScholarshipFormController::carryForwardSessionsQuery().
 * Keep the two in step — if one changes, the single and bulk pages start
 * importing from different sessions.
 *
 * Ordering stays strictly SESS_ID, because there is no date column anywhere in
 * this module, and there is still no SESS_STATUS filter, so the resolved session
 * can be one the picker does not list.
 */
trait ResolvesCarryForwardSession
{
    /**
     * Sessions a carry-forward may read from — everything except summer.
     */
    protected function carryForwardSessionsQuery()
    {
        return DB::table('academic_sessions')
            ->whereRaw("UPPER(TRIM(NVL(sess_code, ''))) NOT LIKE 'SUM%'")
            ->whereRaw("UPPER(TRIM(NVL(sess_name, ''))) NOT LIKE 'SUMMER%'");
    }

    /**
     * The non-summer session immediately before $sessionId, or null.
     *
     * Skipping cannot strand a caller in practice — a summer session is always
     * preceded by a Spring — and every caller already handles a null.
     */
    protected function resolveCarryForwardSession($sessionId = null)
    {
        if (empty($sessionId)) {
            return null;
        }

        return $this->carryForwardSessionsQuery()
            ->select('sess_id', 'sess_code', 'sess_name')
            ->where('sess_id', '<', $sessionId)
            ->orderByDesc('sess_id')
            ->first();
    }
}
