<?php

namespace App\Services\StudentRecord\ChallanPosting;

/**
 * Replaces fin_new_std_hostel_chln_gen.
 *
 * The procedure cancelled the student's Hostel_Fee challan and regenerated it. It had
 * three branches (Hostel_Full_semster / Hostel_day_wise / other utilities); only the
 * first two are relevant here, and which one applies is carried on the snapshot
 * (SSHD_FEE_STRUCTURE_TYPE) rather than recomputed.
 *
 * Note the procedure hard-coded refrigerator / conditioner / heater to 'NO' on
 * regeneration, silently dropping those charges. We instead carry them across from
 * the challan being replaced, so a hostel scholarship no longer wipes them.
 */
class HostelPoster extends AbstractChallanPoster
{
    public function challanType()
    {
        return 'Hostel_Fee';
    }

    public function label()
    {
        return 'Hostel';
    }

    public function cancellationCriteria()
    {
        return function ($q) {
            $q->where('sc_challan_type', 'Hostel_Fee')
              ->whereIn('sc_hostel_fee_type', ['Hostel_Full_semster', 'Hostel_day_wise']);
        };
    }

    /**
     * ACTUAL hostel fee; the scholarship prints below as a negative "Less Scholarship"
     * line and INVOICEAMOUNT carries the accepted total. Same rule as the academic
     * posters — see FullSemesterPoster::feeColumns().
     */
    protected function feeColumns(PostContext $ctx)
    {
        return [
            'FFS_HOSTEL_AMT'         => $ctx->num('sshd_hostel_fee_actual'),
            'FFS_SECURITY_AMT'       => $ctx->num('sshd_security_fee_actual'),
            'SC_SCHOLARSHIP_AMOUNT'  => $ctx->num('sshd_total_disc_amt'),
            'SC_SSD_ARREAR'          => $ctx->num('sshd_arrear'),
            'INVOICEAMOUNT'          => $ctx->num('sshd_total_payable'),
            'SC_INSTALLMENT_ALLOWED' => 'NO',
        ];
    }

    /**
     * Attributes inherited from the challan this replaces.
     *
     * They come from the SNAPSHOT, not from student_challan: Save cancels the old
     * challan and fin_bulk_challan_cancellation DELETEs the row, so by the time Post
     * runs there is nothing left to read. Save captures them into SSHD immediately
     * before cancelling — see captureHostelChallanCarry() — precisely because
     * FIN_STUDENT_CANCEL_CHALLAN does not preserve any of these columns.
     *
     * SSHD_HOSTEL_FEE_TYPE (full-semester vs day-wise) is a different axis from
     * SSHD_FEE_STRUCTURE_TYPE (the page's Regular / Non-Regular dropdown).
     */
    protected function extraColumns(PostContext $ctx)
    {
        $columns = [
            'SC_HOSTEL_FEE_TYPE'    => $ctx->val('sshd_hostel_fee_type', 'Hostel_Full_semster'),
            'SC_HOSTEL_FEE_DETAILS' => 'Hostel Fee',
        ];

        // The student is in the same room with the same utilities; nothing on this page
        // changes them. A missing value is left out rather than written as NULL, so a
        // pre-DDL install degrades to the column's own default.
        foreach ([
            'SC_HOSTEL_CATEGORY' => 'sshd_hostel_category',
            'SC_HOSTEL_DAYS'     => 'sshd_hostel_days',
            'SC_SESSION_TYPE'    => 'sshd_session_type',
            'SC_REFRIGERATOR'    => 'sshd_refrigerator',
            'SC_CONDITIONER'     => 'sshd_conditioner',
            'SC_ELECTRIC'        => 'sshd_electric',
        ] as $target => $source) {
            $value = $ctx->val($source, null);
            if ($value !== null) {
                $columns[$target] = $value;
            }
        }

        return $columns;
    }
}
