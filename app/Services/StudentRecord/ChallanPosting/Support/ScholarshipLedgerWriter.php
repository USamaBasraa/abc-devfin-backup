<?php

namespace App\Services\StudentRecord\ChallanPosting\Support;

use Carbon\Carbon;
use DB;

/**
 * Writes the two tables the printed challan reads for its "Less Scholarship" lines.
 * Without them the student's challan shows a discounted total with nothing to explain
 * it. Both store the percentage as a FRACTION (50% => 0.5) — the renderer multiplies
 * back up by 100.
 *
 * The two tables have DIFFERENT lifecycles, and getting this wrong destroys data:
 *
 *   fin_scholarship_display_history  APPEND-ONLY, one row per challan.
 *       chalanfin.php:1403-1411 joins it on
 *           challan_id = student_challan.id OR challan_id = student_challan.challan_no
 *         AND fsdh_version IS NULL
 *       and its print loop does no de-duplication of its own. CHALLAN_ID is therefore
 *       the ONLY thing separating one challan's lines from another's. All three
 *       procedures insert and never delete; a replaced challan gets a new id, so its
 *       predecessor's rows simply stop matching. FSDH_VERSION must stay NULL — a
 *       non-null value belongs to the rectification flow and is invisible here.
 *
 *   fin_scholarship_discounts        REPLACE-IN-PLACE, per scholarship type.
 *       Joined without any challan predicate (chalanfin.php:1500-1508) and used only
 *       as a fallback, so it is a per-student-per-session ledger. The procedures
 *       delete and re-insert one scholarship type at a time; anything wider erases the
 *       sibling category's rows mid-post.
 */
class ScholarshipLedgerWriter
{
    /**
     * Challan categories the procedures do NOT record in fin_scholarship_discounts.
     *
     * FIN_SCH_CREDIT_HOUR has that INSERT commented out (lines 582-601), and
     * chalanfin.php correspondingly disables its discounts fallback for credit-hour
     * challans — so writing them would add rows the old flow never created, visible to
     * every report that reads the discount ledger.
     */
    private function categoriesWithoutDiscountRow()
    {
        return ['Credit_hours', 'additional_Full_semester'];
    }

    /**
     * @param  array  $rows  [['type'=>int,'percentage'=>float(0-100),'amount'=>float,'arrear'=>float], …]
     */
    public function write($aaiId, $sessionId, $enrollment, $challanCategory, array $rows, $challanId, $challanNo, $isHostel, $entryUser)
    {
        $reference = $challanNo > 0 ? $challanNo : $challanId;
        if ($reference <= 0) {
            return;
        }

        $writesDiscounts = $isHostel
            || !in_array($challanCategory, $this->categoriesWithoutDiscountRow(), true);

        foreach ($rows as $row) {
            $type   = (int) $row['type'];
            $amount = round((float) $row['amount'], 2);
            $pct    = (float) $row['percentage'];
            $arrear = isset($row['arrear']) ? (float) $row['arrear'] : 0;

            // An arrears-only head carries a zero tuition waiver. It still gets both
            // rows: FSD_ARREARS is what records which head the waiver was given under.
            if ($type <= 0 || ($amount <= 0 && $pct <= 0 && $arrear <= 0)) {
                continue;
            }

            if ($writesDiscounts) {
                // Per scholarship type, exactly as the procedures scope it. Deleting
                // any wider would wipe the sibling category's rows — a legal
                // {Full_semester, additional_Full_semester} post writes both.
                //
                // The insert runs even at a zero amount, matching the procedure: its
                // loop inserts V_AMOUNT * (pct/100), which is 0 for an arrears-only
                // head. chalanfin.php's discount fallback ignores zero rows.
                $this->clearDiscount($aaiId, $sessionId, $type, $isHostel);
                $this->insertDiscount($aaiId, $sessionId, $enrollment, $type, $pct / 100, $amount, $reference, $isHostel);
            }

            // fin_scholarship_display_history is APPEND-ONLY in all three procedures.
            // Nothing is deleted: each challan owns a distinct CHALLAN_ID and
            // chalanfin.php joins on it (plus FSDH_VERSION IS NULL), so a replaced
            // challan's rows simply stop matching. Deleting here destroyed the
            // previous challan's printed "Less Scholarship" lines.
            $this->insertHistory(
                $aaiId, $sessionId, $enrollment, $type, $pct / 100, $amount, $arrear,
                $challanId, $challanNo, $challanCategory, $isHostel, $entryUser
            );
        }
    }

    /**
     * Retire this scholarship type's discount row before re-inserting it.
     *
     * fin_scholarship_discounts is a per-student-per-session ledger — no reader
     * filters it by challan — so replace-in-place is correct here, unlike the history
     * table. Scope copied from FIN_SCH_FULL_SEMESTER:255-258 and
     * fin_new_std_hostel_chln_gen_:212-216.
     */
    private function clearDiscount($aaiId, $sessionId, $scholarshipType, $isHostel)
    {
        DB::table('fin_scholarship_discounts')
            ->where('fsd_aai_id', $aaiId)
            ->where('fsd_sesion_id', $sessionId)
            ->where('fsd_scholarship_type', $scholarshipType)
            ->where(function ($q) use ($isHostel) {
                if ($isHostel) {
                    $q->whereRaw("UPPER(NVL(FSD_SCH_TYPE, '')) = 'HOSTEL'");
                } else {
                    $q->whereRaw("(FSD_SCH_TYPE IS NULL OR UPPER(FSD_SCH_TYPE) <> 'HOSTEL')");
                }
            })
            ->delete();
    }

    private function insertDiscount($aaiId, $sessionId, $enrollment, $type, $fraction, $amount, $reference, $isHostel)
    {
        $row = [
            'fsd_enrollment_no'       => $enrollment,
            'fsd_aai_id'              => $aaiId,
            'fsd_sesion_id'           => $sessionId,
            'fsd_scholarship_type'    => $type,
            'fsd_discount_percentage' => round($fraction, 10),
            'fsd_discounted_amount'   => $amount,
            'CHALLAN_NO'              => $reference,
            'FSD_ENTRY_DATETIME'      => Carbon::now('Asia/Karachi'),
            'VERSION'                 => 1,
        ];
        if ($isHostel) {
            $row['FSD_SCH_TYPE'] = 'hostel';
        }

        $this->insertLedgerRow('fin_scholarship_discounts', $row);
    }

    private function insertHistory($aaiId, $sessionId, $enrollment, $type, $fraction, $amount, $arrear, $challanId, $challanNo, $challanCategory, $isHostel, $entryUser)
    {
        $row = [
            'FSD_ENROLLMENT_NO'       => $enrollment,
            'FSD_AAI_ID'              => $aaiId,
            'FSD_SESION_ID'           => $sessionId,
            'FSD_SCHOLARSHIP_TYPE'    => $type,
            'FSD_DISCOUNT_PERCENTAGE' => round($fraction, 10),
            'FSD_DISCOUNTED_AMOUNT'   => $amount,
            'FSD_ARREARS'             => $arrear,
            'CHALLAN_ID'              => $challanNo > 0 ? $challanNo : $challanId,
            'FSDH_VERSION'            => null,
            'ENTRY_DATETIME'          => Carbon::now('Asia/Karachi'),
            'FSDH_UPDATED_BY'         => $entryUser,
            'FSDH_UPDATED_AT'         => Carbon::now('Asia/Karachi'),
            'FLAG'                    => 'F',
        ];
        $row['FSD_SCH_TYPE'] = $isHostel ? 'hostel' : null;
        if (!$isHostel) {
            $row['SSD_CHALLAN_CATEGORY'] = $challanCategory;
        }

        $this->insertLedgerRow('fin_scholarship_display_history', $row);
    }

    /**
     * The ID of both ledger tables is assigned by the database, never here — the same
     * rule student_challan follows in AbstractChallanPoster::insert(). Nothing reads
     * these rows back by id, so there is nothing to return either.
     */
    private function insertLedgerRow($table, array $row)
    {
        $row = ChallanSchema::filter($table, $row);
        if (empty($row)) {
            return;
        }

        DB::table($table)->insert($row);
    }
}
