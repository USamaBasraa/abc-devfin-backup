<?php

namespace App\Http\Controllers\StudentFinancial\Support;

use App\Http\Controllers\StudentFinancial\ScholarshipFormController;

/**
 * Lets MeritCalculationController re-freeze a student's challan snapshot using
 * ScholarshipFormController's OWN implementation.
 *
 * ---------------------------------------------------------------------------
 * WHY A SUBCLASS AND NOT A COPY
 *
 * Adding a merit row changes a figure that describes the WHOLE challan, not the
 * row: SSD_TUITION_DISC_AMT, SSD_ONETIME_*, SSD_TOTAL_* and the surplus columns
 * are each the sum over every scholarship the student holds in that session.
 * Leaving them at what they were before the merit award is what produced both
 * complaints on the Bulk Post screen:
 *
 *   "total mismatch"  getStagedSummary() flags snapshot_stale when the awards it
 *                     sums disagree with the stored SSD_TUITION_DISC_AMT. A
 *                     student on Need Based 50% had a snapshot describing 50%;
 *                     adding Merit 25% made the awards come to 75% while the
 *                     stamp still said 50%.
 *
 *   "cannot post"     priced is `ssd_total_fee_actual > 0` on the head row. A
 *                     student whose ONLY award is merit had no row with that
 *                     column set at all, so the challan read as unpriced.
 *
 * The numbers therefore have to match that page's arithmetic exactly. Any second
 * implementation of it — however carefully copied — is a thing that can drift,
 * and the whole point is that these two screens must never disagree. Extending
 * the controller gives us its refreshScholarshipSnapshot() verbatim, together
 * with sourcePage() ('SCHOLARSHIP_BULK', which is what the merit rows are
 * stamped with), academicCategoryLabels() and resolveFullTuitionFallback().
 *
 * Nothing here is routed. It is a plain object used for its inherited method.
 *
 * ---------------------------------------------------------------------------
 * DEPLOYING THIS FILE
 *
 * It is a NEW file in a NEW directory, and this project ships an OPTIMISED
 * Composer autoloader (vendor/composer/autoload_classmap.php carries the app's
 * classes). The classmap is not authoritative, so PSR-4 still resolves a file
 * the map has never seen — but only once the file is actually on the server.
 *
 * So when moving this to another environment: copy the whole
 * app/Http/Controllers/StudentFinancial/Support/ directory, not just the files
 * that changed. "Class ... MeritSnapshotRefresher not found" means the
 * directory did not travel. If the file IS there and the error persists, that
 * install's autoloader is classmap-authoritative and needs
 * `composer dump-autoload -o` once.
 * ---------------------------------------------------------------------------
 */
final class MeritSnapshotRefresher extends ScholarshipFormController
{
    /**
     * Recompute and store one student's frozen snapshot for one session.
     *
     * Every active scholarship row of that student is re-read and re-stamped, so
     * merit and any need-based / employee award they also hold end up described
     * by one consistent set of challan totals.
     *
     * 'Existing' is the right student type: a merit student is by definition
     * enrolled (STUDENTS_APPLY_INFO, status Active or Expected-Graduate). The
     * inherited resolveSnapshotFee() still falls back to 'New Admission <SEASON>'
     * on its own when a programme carries only that fee row.
     *
     * @return array{ok:bool, categories:array<string,bool>, warnings:array<int,string>, surplus_pending:float}
     */
    public function refreshFor(int $sessionId, ?int $aaiId, string $enrollment): array
    {
        return $this->refreshScholarshipSnapshot($sessionId, $aaiId, $enrollment, 'Existing');
    }
}
