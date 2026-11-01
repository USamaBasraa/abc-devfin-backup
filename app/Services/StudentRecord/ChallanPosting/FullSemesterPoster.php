<?php

namespace App\Services\StudentRecord\ChallanPosting;

/**
 * Replaces FIN_SCH_FULL_SEMESTER for the `Full_semester` category.
 *
 * The procedure cancelled every Full_semester / Short/Summer_Course challan with
 * `sc_enrolled_cr_hr IS NULL` and inserted a fresh one. That NULL check is what keeps
 * a full-semester posting from cancelling the student's credit-hour challan, and it
 * is reproduced exactly.
 */
class FullSemesterPoster extends AbstractChallanPoster
{
    public function challanType()
    {
        return 'Full_semester';
    }

    public function label()
    {
        return 'Full Semester';
    }

    public function cancellationCriteria()
    {
        return function ($q) {
            $q->whereIn('sc_challan_type', ['Full_semester', 'Short/Summer_Course'])
              ->whereNull('sc_enrolled_cr_hr');
        };
    }

    /**
     * Fee columns carry the ACTUAL (undiscounted) fee from the snapshot, and every
     * waiver prints below as a negative line — the scholarships from
     * fin_scholarship_display_history, the arrears from SC_SSD_ARREAR. INVOICEAMOUNT
     * is the accepted total, never recomputed.
     *
     * fee_table() in chalanfin.php prints "Total Amount Payable" straight from
     * INVOICEAMOUNT and never sums the rows, so actual-fee lines are what make the
     * document reconcile on its own face:
     *
     *   292,000 + 2,000 + 6,500 - 204,400 - 58,400 - 7,650 = 30,050 = INVOICEAMOUNT
     *
     * The preview (chalanfinPreview.php) maps the same columns, so the posted challan
     * is line-for-line the document that was accepted.
     */
    protected function feeColumns(PostContext $ctx)
    {
        $tuition = $ctx->num('ssd_tuition_fee_actual');

        return [
            // Tuition prints from SC_INSTALLMENT_TUITION_FEE_AMNT (see fee_table()
            // in chalanfin.php) — leaving it 0 drops the line from the challan.
            'SC_INSTALLMENT_TUITION_FEE_AMNT' => $tuition,
            'SC_TUITION_FEE_AMNT'    => $tuition,
            'FFS_ADMISSION_AMT'      => $ctx->num('ssd_admission_fee_actual'),
            'FFS_SECURITY_AMT'       => $ctx->num('ssd_security_fee_actual'),
            'FFS_MEDICAL_AMT'        => $ctx->num('ssd_medical_fee_actual'),
            'SC_ACTIVITY_AMT'        => $ctx->num('ssd_activity_fee_actual'),
            'SC_SCHOLARSHIP_AMOUNT'  => $ctx->num('ssd_total_disc_amt'),
            'SC_SSD_ARREAR'          => $ctx->num('ssd_onetime_disc_amt'),
            'INVOICEAMOUNT'          => $ctx->num('ssd_total_payable'),
            'SC_INSTALLMENT_ALLOWED' => 'YES',
        ];
    }
}
