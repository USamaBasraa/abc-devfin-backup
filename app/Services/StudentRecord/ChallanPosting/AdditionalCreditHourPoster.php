<?php

namespace App\Services\StudentRecord\ChallanPosting;

/**
 * Replaces FIN_SCH_CREDIT_HOUR for the `additional_Full_semester` category.
 *
 * This category bills the EXTRA credit hours sitting on a student's full-semester
 * challan, so the challan written is a `Full_semester` one carrying SC_ENROLLED_CR_HR
 * — which is exactly how the frozen page identifies it
 * (SC_CHALLAN_TYPE = 'Full_semester' AND SC_ENROLLED_CR_HR > 0).
 */
class AdditionalCreditHourPoster extends CreditHourPoster
{
    public function challanType()
    {
        return 'Full_semester';
    }

    public function label()
    {
        return 'Additional Credit Hr';
    }

    public function cancellationCriteria()
    {
        return function ($q) {
            $q->where('sc_challan_type', 'Full_semester')
              ->whereNotNull('sc_enrolled_cr_hr')
              ->where('sc_enrolled_cr_hr', '>', 0);
        };
    }

    /**
     * SC_ENROLLED_CR_HR is the ONLY thing separating this challan from a plain full-semester
     * one, and PostContext::num() turns a NULL snapshot value into 0.0. A challan written
     * with 0 there lands in a dead zone: not NULL, so FullSemesterPoster will not cancel it;
     * not > 0, so neither this poster's criteria above nor findLiveChallan() can see it. It
     * would be un-retirable for good, and every later additional-credit-hour save for that
     * student would fail because there is no challan left to read the hours from.
     *
     * runSave() now refuses to save an unpriced category at all, so this should be
     * unreachable. It is the backstop that keeps the bad row out of student_challan.
     */
    protected function blockedReason(PostContext $ctx)
    {
        if ($ctx->num('ssd_credit_hours') <= 0) {
            return 'The Additional Credit Hr challan has no credit hours on its saved figures, '
                 . 'so it cannot be posted. Re-generate the additional credit-hour challan for '
                 . 'this student, then save the scholarship again.';
        }

        return parent::blockedReason($ctx);
    }
}
