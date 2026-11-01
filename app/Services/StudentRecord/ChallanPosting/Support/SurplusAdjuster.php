<?php

namespace App\Services\StudentRecord\ChallanPosting\Support;

use Carbon\Carbon;
use DB;

/**
 * Replaces FIN_PRO_ADJUST_CHALLAN_SURPLUS.
 *
 * When a PAID challan is cancelled the money becomes "surplus" — a pending balance on
 * fin_fee_adjustment. The procedure spent that balance against the newly generated
 * challan, cutting INVOICEAMOUNT (often to zero) and recording what was consumed on
 * student_challan.SC_SURPLUS_AMOUNT.
 *
 * The same arithmetic serves two callers, which is the point of this class:
 *
 *   Save -> simulate()  read-only, so the preview can show the challan as it WILL be
 *   Post -> apply()     writes fin_fee_adjustment
 *
 * They share calculate(), so a previewed figure and a posted figure cannot drift apart.
 *
 * Column-name traps, all confirmed against live code:
 *   - the status column is FF_AMNT_STATUS (FF_, not FFA_)
 *   - FFA_AMOUNT_STATUS holds 'Pending', 'pending' and 'Paid' in real data — compare
 *     case-insensitively
 *   - FFA_SC_CHALLAN_NO holds student_challan.ID, not CHALLAN_NO
 *   - FFA_ID is assigned by Oracle; no PHP insert supplies one and there is no sequence
 *   - surplus is NOT session-scoped (FFA_SESS_ID is unused everywhere)
 *
 * Surplus belongs to the STUDENT, not to a fee type — fin_fee_adjustment has no
 * academic/hostel dimension — so one balance is spent against whichever challans are
 * being posted, hostel included. This is a DELIBERATE divergence from the legacy code:
 * fin_new_std_hostel_chln_gen never called the surplus procedure, and
 * GenerateSingleChallanController::applySurplusToChallansBulk() blocks hostel fee types
 * outright, which left a hostel-only student's credit permanently unspendable.
 *
 * Save simulates the split across all posted challans in posting order (academic first,
 * hostel last) and freezes each one's share; Post spends exactly the frozen share so a
 * partial post cannot let one challan swallow another's slice.
 */
class SurplusAdjuster
{
    /**
     * The student's spendable surplus rows, oldest first.
     *
     * DIVERGENCE, deliberate: the legacy readers SUM these but then consume only the
     * first row (FeeAdjustmentController.php:594 uses $sql[0]), silently stranding the
     * rest. We spend them in order until the challan is covered.
     *
     * NULL statuses count as spendable. FIN_BULK_CHALLAN_CANCELLATION's INSERT branch
     * (e.g. line 233) supplies only ffa_enrollment_no / ffa_adjustment_category /
     * ffa_surplus_amount / ffa_remarks / ffa_app_id — the three status columns are left
     * NULL. Demanding 'PENDING'/'E'/'E' would make a student's very first cancellation
     * credit invisible. GenerateSingleChallanController::getSurplusAdjustmentsByAppId()
     * has the same NULL tolerance for the same reason.
     *
     * @return \Illuminate\Support\Collection
     */
    public function pendingRows($aaiId, $enrollment = null)
    {
        return DB::table('fin_fee_adjustment')
            ->select('ffa_id', 'ffa_surplus_amount', 'ffa_adjust_amount', 'ffa_app_id', 'ffa_enrollment_no')
            ->where(function ($q) use ($aaiId, $enrollment) {
                $q->where('ffa_app_id', $aaiId);
                if (!empty($enrollment)) {
                    $q->orWhereRaw('LOWER(TRIM(ffa_enrollment_no)) = ?', [strtolower(trim((string) $enrollment))]);
                }
            })
            ->whereRaw("(ffa_amount_status IS NULL OR UPPER(TRIM(ffa_amount_status)) = 'PENDING')")
            ->whereRaw("(ffa_status IS NULL OR UPPER(TRIM(ffa_status)) = 'E')")
            ->whereRaw("(ff_amnt_status IS NULL OR UPPER(TRIM(ff_amnt_status)) = 'E')")
            ->whereRaw('NVL(ffa_surplus_amount, 0) > 0')
            ->orderBy('ffa_id')
            ->get();
    }

    /** Total spendable surplus — what the procedure's SUM(...) returned. */
    public function pendingSurplus($aaiId, $enrollment = null)
    {
        $total = 0.0;
        foreach ($this->pendingRows($aaiId, $enrollment) as $row) {
            $total += (float) $row->ffa_surplus_amount;
        }

        return round($total, 2);
    }

    /**
     * The whole of the procedure's arithmetic, and the only copy of it.
     *
     * @param  float  $available  spendable surplus
     * @param  float  $payable    the challan total BEFORE surplus
     * @return array{available:float, consumed:float, invoice:float, remaining:float}
     */
    public function calculate($available, $payable)
    {
        $available = round(max(0.0, (float) $available), 2);
        // The procedure floors the payable at 0 before comparing.
        $payable = round(max(0.0, (float) $payable), 2);

        $consumed = min($available, $payable);

        return [
            'available' => $available,
            'consumed'  => round($consumed, 2),
            'invoice'   => round($payable - $consumed, 2),
            'remaining' => round($available - $consumed, 2),
        ];
    }

    /**
     * Read-only walk over several challans against one running balance.
     *
     * Post applies the challans in order and each sees what the previous one left, so
     * Save must simulate in that SAME order or the preview will not match.
     *
     * @param  array  $payables  [challanCategory => payableBeforeSurplus], in posting order
     * @return array             [challanCategory => calculate() result]
     */
    public function simulate($available, array $payables)
    {
        $balance = round(max(0.0, (float) $available), 2);
        $out = [];

        foreach ($payables as $category => $payable) {
            $result = $this->calculate($balance, $payable);
            $out[$category] = $result;
            $balance = $result['remaining'];
        }

        return $out;
    }

    /**
     * Spend surplus against one freshly written challan.
     *
     * Mirrors GenerateSingleChallanController::applyPartialSurplusToChallan() — same
     * status transitions, same FFA_ADJUST_AMOUNT accumulation — but consumes across
     * every pending row instead of a single caller-nominated one.
     *
     * Assumes it is already inside a transaction; postSnapshot() opens one.
     *
     * @param  float|null  $planned  the amount the grader ACCEPTED this challan would
     *                               absorb. When given, exactly that is spent (capped
     *                               at what is really available) instead of recomputing
     *                               `min(available, payable)`. Without it, posting one
     *                               challan out of a previewed pair would let it
     *                               swallow the other's share of a shared balance.
     * @return array the calculate() result actually applied
     */
    public function apply($aaiId, $enrollment, $challanId, $payable, $entryUser, $entryUserId = null, $planned = null)
    {
        $rows = $this->pendingRows($aaiId, $enrollment);
        $available = 0.0;
        foreach ($rows as $row) {
            $available += (float) $row->ffa_surplus_amount;
        }

        $result = $this->calculate($available, $payable);

        if ($planned !== null) {
            // Never more than the accepted figure, and never more than exists or than
            // the challan is worth — calculate() has already applied those two ceilings.
            $consumed = min($result['consumed'], round(max(0.0, (float) $planned), 2));
            $result = [
                'available' => $result['available'],
                'consumed'  => $consumed,
                'invoice'   => round(max(0.0, (float) $payable) - $consumed, 2),
                'remaining' => round($result['available'] - $consumed, 2),
            ];
        }

        if ($result['consumed'] <= 0) {
            return $result;
        }

        $now = Carbon::now('Asia/Karachi');
        $left = $result['consumed'];

        foreach ($rows as $row) {
            if ($left <= 0) {
                break;
            }

            $rowAvailable = round((float) $row->ffa_surplus_amount, 2);
            $take = min($rowAvailable, $left);
            $newBalance = round($rowAvailable - $take, 2);
            $exhausted = ($newBalance <= 0);

            $update = [
                'FFA_SURPLUS_AMOUNT' => $newBalance,
                'FFA_SC_CHALLAN_NO'  => $challanId,      // student_challan.ID, not CHALLAN_NO
                'FFA_AMOUNT_STATUS'  => $exhausted ? 'Paid' : 'Pending',
                'FFA_STATUS'         => $exhausted ? 'D' : 'E',
                'FF_AMNT_STATUS'     => $exhausted ? 'D' : 'E',
                'FFA_ADJUST_AMOUNT'  => round((float) (isset($row->ffa_adjust_amount) ? $row->ffa_adjust_amount : 0) + $take, 2),
                'FFA_UPDATED_BY'     => $entryUser,
                'FFA_UPDATED_AT'     => $now,
            ];
            if ($entryUserId !== null) {
                $update['FFA_ENTRY_USERID'] = $entryUserId;
            }

            $safe = ChallanSchema::filter('fin_fee_adjustment', $update);

            // ChallanSchema caches a failed user_tab_columns probe as "column absent".
            // If that ever swallowed FFA_SURPLUS_AMOUNT we would cut the challan's
            // invoice while leaving the balance untouched — the same money would then
            // be spendable again. Refuse rather than lose track of it.
            if (!array_key_exists('FFA_SURPLUS_AMOUNT', $safe)) {
                throw new \RuntimeException(
                    'Refusing to adjust surplus: FFA_SURPLUS_AMOUNT is not writable on '
                    . 'fin_fee_adjustment, so the balance could not be reduced.'
                );
            }

            $affected = DB::table('fin_fee_adjustment')
                ->where('ffa_id', $row->ffa_id)
                ->update($safe);

            // A no-op UPDATE here is the worst failure this class has: the challan
            // records SC_SURPLUS_AMOUNT and its invoice is cut, but the balance stays
            // Pending on the Fee Adjustment page and the SAME money can be spent again
            // on the next challan. Never let that pass silently.
            if ($affected < 1) {
                throw new \RuntimeException(
                    'Surplus adjustment did not update fin_fee_adjustment row '
                    . var_export($row->ffa_id, true) . ' (challan ' . $challanId . '). '
                    . 'Nothing has been committed.'
                );
            }

            $left = round($left - $take, 2);
        }

        // Every rupee reported as consumed must have come off a real row.
        if ($left > 0.005) {
            throw new \RuntimeException(
                'Surplus adjustment could only spend Rs '
                . number_format($result['consumed'] - $left, 2) . ' of the Rs '
                . number_format($result['consumed'], 2) . ' applied to challan '
                . $challanId . '. Nothing has been committed.'
            );
        }

        return $result;
    }
}
