<?php

namespace App\Services\StudentRecord\ChallanPosting;

use App\Services\StudentRecord\Fees\SummerCourseFeeCalculator;
use DB;

/**
 * Replaces FIN_SCH_FULL_SEMESTER (cancellation set, scholarship handling) and the
 * challan-writing half of FIN_PRO_SUMMER_CREDIT_HOUR (the fee columns) for the
 * `Short/Summer_Course` category.
 *
 * Summer tuition is priced COURSE BY COURSE, each course at the rate of the school
 * that offers it, by App\Services\StudentRecord\Fees\SummerCourseFeeCalculator. That
 * runs at Save; this class only reads the frozen total off the snapshot.
 *
 * Two quirks of the procedure are reproduced on purpose, because the printed challan
 * and downstream reports depend on them:
 *
 *   - FFS_CREDHR_AMT receives the TOTAL summer tuition, not a per-credit rate
 *     (FIN_PRO_SUMMER_CREDIT_HOUR passes V_FFS_CREDHR_T_AMT into that column).
 *   - SC_INSTALLMENT_TUITION_FEE_AMNT receives the same total; it is the column
 *     fee_table() in chalanfin.php actually prints the Tuition Fee line from.
 *
 * NOT reproduced (see the plan): the procedure computed v_repeat_fee inside its course
 * loop without accumulating, so SC_REPEAT_AMOUNT_5 held only the LAST course's
 * surcharge. We store the sum across every repeated course.
 */
class ShortSummerPoster extends FullSemesterPoster
{
    public function challanType()
    {
        return 'Short/Summer_Course';
    }

    public function label()
    {
        return 'Short / Summer Course';
    }

    /**
     * Name any summer course that had no fee structure.
     *
     * Such a course is charged NOTHING and the tuition total is quietly short by its
     * fee — the procedure logged it to a `testing` table and moved on. This is a
     * read-only re-check: the posted figures still come from the frozen snapshot, only
     * the message is derived here.
     */
    protected function warnings(PostContext $ctx)
    {
        if (empty($ctx->enrollment)) {
            return [];
        }

        $stu = DB::table('students_apply_info')
            ->select(
                'SAI_SDPO_SCHOOL_ID as school_id',
                'SAI_SDPO_DEPT_ID as dept_id',
                'SAI_SDPO_PROG_ID as prog_id',
                'SAI_SDPO_SESS_ID as roadmap_sess'
            )
            ->whereRaw('LOWER(SAI_ENROLLMENT_NO) = ?', [strtolower((string) $ctx->enrollment)])
            ->first();

        if (empty($stu)) {
            return [];
        }

        $summer = (new SummerCourseFeeCalculator())->calculate(
            $ctx->sessionId,
            $ctx->enrollment,
            $stu->school_id,
            $stu->dept_id,
            $stu->prog_id,
            $stu->roadmap_sess,
            DB::table('programs')->where('prog_id', $stu->prog_id)->value('PROG_DEGREE_LEVEL')
        );

        if (empty($summer['unpricedCourses'])) {
            return [];
        }

        return ['not priced (no Short/Summer_Course fee structure for the offering school): '
            . implode(', ', $summer['unpricedCourses'])];
    }

    protected function feeColumns(PostContext $ctx)
    {
        $tuition = $ctx->num('ssd_tuition_fee_actual');

        return [
            'SC_INSTALLMENT_TUITION_FEE_AMNT' => $tuition,
            'SC_TUITION_FEE_AMNT'    => $tuition,
            // The total, not a rate — matching the procedure. See the class docblock.
            'FFS_CREDHR_AMT'         => $tuition,
            'SC_ENROLLED_CR_HR'      => $ctx->num('ssd_credit_hours'),
            'SC_REPEAT_AMOUNT_5'     => $ctx->num('ssd_repeat_fee'),
            'FFS_ADMISSION_AMT'      => $ctx->num('ssd_admission_fee_actual'),
            'FFS_SECURITY_AMT'       => $ctx->num('ssd_security_fee_actual'),
            'FFS_MEDICAL_AMT'        => $ctx->num('ssd_medical_fee_actual'),
            'SC_ACTIVITY_AMT'        => $ctx->num('ssd_activity_fee_actual'),
            'SC_SCHOLARSHIP_AMOUNT'  => $ctx->num('ssd_total_disc_amt'),
            'SC_SSD_ARREAR'          => $ctx->num('ssd_onetime_disc_amt'),
            'INVOICEAMOUNT'          => $ctx->num('ssd_total_payable'),
            // The procedure passed p_Installment_allowed => 'NO' for summer.
            'SC_INSTALLMENT_ALLOWED' => 'NO',
        ];
    }
}
