<?php

namespace App\Services\StudentRecord\Fees;

use DB;

/**
 * Is this student a CREDIT HOUR case — i.e. has Full Semester stopped applying?
 *
 * A student on 12 credit hours gets a Full Semester challan and scholarships are applied
 * against it. They drop a course, enrolled hours fall to 9, and the Credit Hour challan
 * page CANCELS the Full Semester challan and issues a Credit Hours one. From that moment
 * the Full Semester scholarship rows are history: they must stay on record and on screen,
 * but they can no longer be edited, saved, previewed or posted.
 *
 * Nothing in the scholarship page knew that. resolveAcademicFeeStructure() only demotes
 * Credit_hours when hours EXCEED the limit; there is no rule in the other direction, so
 * Full_semester always resolved ok and the grader hit
 * '"Credit Hr" and "Full Semester" cannot be applied together in the same session.'
 * with no way out. This class is that missing rule.
 *
 * ---------------------------------------------------------------------------
 * THE CRITERIA ARE NOT INVENTED HERE.
 *
 * They reproduce the cancel_challan() condition of the ladder in
 * App\Http\Controllers\StudentFinancial\GenerateCreditHrBulkChallanController
 * ::add_creditHr_challans() (lines 416-509) — the page that actually retires the
 * Full Semester challan. cancel_challan() removes SC_CHALLAN_TYPE 'Full_semester' AND
 * 'Short/Summer_Course', which is why both categories go dormant together.
 *
 * Only the branches that CANCEL count. The ladder also issues a credit-hour challan
 * without cancelling — enrolled > 18 (UG) / > 12 (PG), and the repeat-credit branches —
 * and in those the Full Semester challan lives on, so they are not dormancy triggers.
 *
 * DRIFT WARNING: if that ladder's thresholds change, change them here too.
 * ---------------------------------------------------------------------------
 *
 * Where the numbers come from:
 *   enrolled credits            LIVE, from student_courses
 *   earned / allowed / semester FIN_STUDENTS_CREDITS_DATA (FSCD)
 *
 * Enrolled credits are recounted rather than read from FSCD on purpose. FSCD is refreshed
 * by hand from the Student Credits Data page, and the whole scenario this class exists for
 * is a DROP — if FSCD is stale it still reads 12 and nothing would go dormant, which is the
 * exact bug. The other three figures have no live source and are taken as they are; without
 * an FSCD row only the credit-hour half of the ladder can be evaluated.
 */
class CreditHourCaseResolver
{
    /**
     * Enrolled credit hours at or below this make the student a credit-hour case, and the
     * Full Semester / Short-Summer challan is cancelled.
     *
     * The ladder's literal. It deliberately differs from the 9.5 / 6 degree-level limits in
     * ResolvesScholarshipFees::resolveAcademicFeeStructure(), which gate whether Credit_hours
     * may be SELECTED. Leaving both as they are means a student on exactly 9.5cr is not a
     * dormancy case yet can still be given a Credit Hr scholarship. Reconciling the two
     * thresholds is a finance decision, not a code cleanup.
     */
    const CREDIT_CASE_MAX_CREDITS = 9;

    /** Categories the ladder's cancel_challan() retires. */
    public static function supersededCategories()
    {
        return ['Full_semester', 'Short/Summer_Course'];
    }

    /**
     * @return array{
     *   isCreditHourCase:bool, reason:string|null, message:string|null,
     *   enrolledCredits:float, supersedes:array
     * }
     */
    public function resolve($sessionId, $enrollmentNo, $degreeLevel = null)
    {
        $enrolled = $this->enrolledCreditHours($sessionId, $enrollmentNo);

        // Not enrolled at all is not a credit-hour case: Credit_hours already resolves
        // ok=false with "not enrolled in any course", and greying out Full Semester on top
        // would leave the grader with no usable category whatsoever.
        if ($enrolled <= 0) {
            return $this->notACase($enrolled);
        }

        if ($enrolled <= self::CREDIT_CASE_MAX_CREDITS) {
            return $this->isACase(
                $enrolled,
                'credits_within_limit',
                'Enrolled credit hours (' . $this->trim($enrolled) . ') are within the credit-hour limit, '
                . 'so this student is billed on a Credit Hr challan. Previously applied scholarships are '
                . 'kept on record but can no longer be changed or saved.'
            );
        }

        // The second cancel branch needs FSCD. No row -> only the rule above applies.
        $credits = $this->creditsRow($sessionId, $enrollmentNo);
        if (empty($credits)) {
            return $this->notACase($enrolled);
        }

        $currentSemester = (float) $this->prop($credits, 'fscd_std_current_semester');
        $allowedSemester = (float) $this->prop($credits, 'fscd_std_allowed_semester');
        $earned          = (float) $this->prop($credits, 'fscd_std_earned_credits');
        $allowedCredits  = (float) $this->prop($credits, 'fscd_std_allowed_credits');

        // Ladder: current_semester > allowed_semester AND enrolled + earned >= allowed_credits
        // -> cancel_challan() then a credit-hour challan. The student has reached the credits
        // their programme allows, so whatever is left is billed per credit.
        if ($allowedSemester > 0 && $allowedCredits > 0
            && $currentSemester > $allowedSemester
            && ($enrolled + $earned) >= $allowedCredits) {
            return $this->isACase(
                $enrolled,
                'allowed_credits_reached',
                'This student has reached the credit total allowed for their programme ('
                . $this->trim($enrolled + $earned) . ' of ' . $this->trim($allowedCredits)
                . ') beyond the allowed semesters, so they are billed on a Credit Hr challan. '
                . 'Previously applied scholarships are kept on record but can no longer be '
                . 'changed or saved.'
            );
        }

        return $this->notACase($enrolled);
    }

    // ---------------------------------------------------------------- //

    /**
     * The same count resolveAcademicFeeStructure() uses for Credit_hours, so the dormancy
     * decision and the fee it is decided against can never come from different figures.
     */
    private function enrolledCreditHours($sessionId, $enrollmentNo)
    {
        if (empty($enrollmentNo)) {
            return 0.0;
        }

        return (float) DB::table('student_courses')
            ->where('SC_CO_SESS_ID', $sessionId)
            ->whereRaw('LOWER(SC_SAI_ENROLLMENT_NO) = ?', [strtolower(trim((string) $enrollmentNo))])
            ->where('SC_ENROLLMENT_STATUS', 1)
            ->whereIn('SC_STATUS', ['Withdrawal', 'Block', 'A'])
            ->sum('SC_PC_COURSE_CR');
    }

    /** The student's FSCD row for this session, keyed exactly as the credit-hour page keys it. */
    private function creditsRow($sessionId, $enrollmentNo)
    {
        if (empty($enrollmentNo)) {
            return null;
        }

        return DB::table('FIN_STUDENTS_CREDITS_DATA')
            ->whereRaw('LOWER(TRIM(FSCD_REGNO)) = ?', [strtolower(trim((string) $enrollmentNo))])
            ->where('FSCD_STD_ENROLLMENT_SESSION', $sessionId)
            ->orderByDesc('FSCD_ID')
            ->first();
    }

    /** Oracle casing varies between drivers; read a column whichever way it came back. */
    private function prop($row, $column)
    {
        foreach ([$column, strtoupper($column), strtolower($column)] as $key) {
            if (isset($row->{$key})) {
                return $row->{$key};
            }
        }

        return null;
    }

    private function isACase($enrolled, $reason, $message)
    {
        return [
            'isCreditHourCase' => true,
            'reason'           => $reason,
            'message'          => $message,
            'enrolledCredits'  => $enrolled,
            'supersedes'       => self::supersededCategories(),
        ];
    }

    private function notACase($enrolled)
    {
        return [
            'isCreditHourCase' => false,
            'reason'           => null,
            'message'          => null,
            'enrolledCredits'  => $enrolled,
            'supersedes'       => [],
        ];
    }

    /** 9.0 reads as a typo in a message; 9 does not. */
    private function trim($number)
    {
        return rtrim(rtrim(number_format((float) $number, 2, '.', ''), '0'), '.');
    }
}
