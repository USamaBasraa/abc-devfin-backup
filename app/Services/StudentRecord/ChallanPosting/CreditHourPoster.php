<?php

namespace App\Services\StudentRecord\ChallanPosting;

/**
 * Replaces FIN_SCH_CREDIT_HOUR for the `Credit_hours` category.
 *
 * Two deliberate differences from the procedure:
 *
 *  - The procedure UPDATEd the existing credit-hour challan in place. Per the agreed
 *    design every category now cancels and inserts, so the flow is uniform.
 *  - Its paid-only branch fell through BOTH reissue conditions for this category
 *    (FIN_SCH_CREDIT_HOUR.sql:364 and :390), so the paid challan was cancelled and
 *    nothing was reissued — the run then died on its own -20003 guard. We reissue from
 *    the accepted snapshot instead, and the cancelled challan's money comes back as
 *    surplus which SurplusAdjuster then spends against the new one.
 *
 * The `sc_enrolled_cr_hr IS NOT NULL` filter is the mirror image of the full-semester
 * poster's IS NULL, and is what keeps the two from cancelling each other's challans.
 */
class CreditHourPoster extends AbstractChallanPoster
{
    public function challanType()
    {
        return 'Credit_hours';
    }

    public function label()
    {
        return 'Credit Hour';
    }

    public function cancellationCriteria()
    {
        return function ($q) {
            $q->where('sc_challan_type', 'Credit_hours')
              ->whereNotNull('sc_enrolled_cr_hr');
        };
    }

    /** ACTUAL fee per line; the waivers print below as negatives. See FullSemesterPoster. */
    protected function feeColumns(PostContext $ctx)
    {
        $tuition = $ctx->num('ssd_tuition_fee_actual');

        return [
            'SC_INSTALLMENT_TUITION_FEE_AMNT' => $tuition,
            'SC_TUITION_FEE_AMNT'    => $tuition,
            'FFS_CREDHR_AMT'         => $ctx->num('ssd_per_credit_fee'),
            'SC_ENROLLED_CR_HR'      => $ctx->num('ssd_credit_hours'),
            'FFS_ADMISSION_AMT'      => $ctx->num('ssd_admission_fee_actual'),
            'FFS_SECURITY_AMT'       => $ctx->num('ssd_security_fee_actual'),
            'FFS_MEDICAL_AMT'        => $ctx->num('ssd_medical_fee_actual'),
            'SC_ACTIVITY_AMT'        => $ctx->num('ssd_activity_fee_actual'),
            'SC_SCHOLARSHIP_AMOUNT'  => $ctx->num('ssd_total_disc_amt'),
            'SC_SSD_ARREAR'          => $ctx->num('ssd_onetime_disc_amt'),
            // The repeat-course surcharge, already included in the tuition above. Recorded
            // separately because FIN_CREDIT_HR_WISE_CHALLAN writes it to this column and the
            // reissued challan must carry the same breakdown as the one it replaces.
            'SC_REPEAT_AMOUNT_5'     => $ctx->num('ssd_repeat_fee'),
            'INVOICEAMOUNT'          => $ctx->num('ssd_total_payable'),
            'SC_INSTALLMENT_ALLOWED' => 'NO',
        ];
    }
}
