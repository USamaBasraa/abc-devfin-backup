<?php

namespace App\Services\StudentRecord\Fees;

use DB;

/**
 * The repeat-course surcharge on a credit-hour challan, priced per course at the rate of
 * the school that OFFERS it.
 *
 * Reproduces the `v_total_repat` loop of the Oracle procedure FIN_CREDIT_HR_WISE_CHALLAN
 * (the loop appears four times in that procedure, identically, once per branch).
 *
 * ---------------------------------------------------------------------------
 * WHAT THE PROCEDURE ACTUALLY DOES — worth being precise about, because it is easy to
 * assume the per-school lookup prices the whole challan.
 *
 *     V_FFS_CREDHR_AMT   = the STUDENT'S OWN school/dept/program per-credit rate
 *     V_FFS_CREDHR_T_AMT = V_FFS_CREDHR_AMT * V_sc_HR  +  v_total_repat
 *
 * Base tuition is the student's own rate times their billable hours. The per-course,
 * per-offering-school lookup runs ONLY inside the repeat loop: v_course_fee stays 0 unless
 * the course is found in GRADE_HISTORY. So a student reading across two schools is charged
 * their own school's rate for everything, EXCEPT any course they are repeating, which is
 * charged at the offering school's rate.
 *
 * That is why this class prices repeats only, and why resolveAcademicFeeStructure() keeps
 * pricing the base off the student's own fee-structure row.
 * ---------------------------------------------------------------------------
 *
 * It replaces the cruder repeat term the page used before — FSCD_STD_REPEAT_CREDITS times
 * the student's own rate, halved — which had no course attribution and could not see the
 * offering school's rate at all.
 *
 * Three deliberate divergences from the procedure, all safe:
 *   - the procedure writes `AND ROWNUM = 1 ... ORDER BY ffs_entry_datetime DESC`, where
 *     Oracle applies ROWNUM BEFORE the sort and so returns an arbitrary row. Ordering is
 *     applied properly here, so "newest wins" actually holds
 *   - degree level is matched case-insensitively; the data holds 'Undergraduate' /
 *     'Undergradute' / 'Graduate' inconsistently and the procedure's `=` misses those
 *   - the enrollment number is matched case-insensitively, as everywhere else in the app
 */
class CreditHourRepeatFeeCalculator
{
    /** GRADE_HISTORY is only consulted from this session onward. */
    const GRADE_HISTORY_FROM_SESSION = 163;

    /**
     * Up to and including this session a repeated course is charged at HALF the rate.
     * From 167 onward it is charged in full. FIN_CREDIT_HR_WISE_CHALLAN line 196.
     */
    const HALF_RATE_UNTIL_SESSION = 166;

    /** @var array<string, float|null> memo for the per-school rate lookup */
    private $rateCache = [];

    /**
     * @return array{
     *   totalFee:float, courses:array, courseCount:int,
     *   creditHours:float, unpricedCourses:array
     * }
     */
    public function calculate($sessionId, $enrollmentNo, $roadmapSess = null, $degreeLevel = null)
    {
        $courses = $this->enrolledCourses($sessionId, $enrollmentNo);
        if (empty($courses)) {
            return $this->empty();
        }

        $repeatIds = $this->repeatedCourseIds($sessionId, $enrollmentNo, $courses);
        if (empty($repeatIds)) {
            return $this->empty();
        }

        $halfRate = ((int) $sessionId <= self::HALF_RATE_UNTIL_SESSION);

        $priced = [];
        $unpriced = [];
        $totalFee = 0.0;
        $creditHours = 0.0;

        foreach ($courses as $course) {
            if (!isset($repeatIds[(string) $course->course_id])) {
                continue;
            }

            $credits = (float) $course->credit_hours;
            $rate = $this->rateForSchool($sessionId, $course->school_id, $roadmapSess, $degreeLevel);

            // The procedure leaves v_course_fee at 0 for these and carries on. It is
            // reported instead of swallowed, so a missing fee structure is fixable while
            // the grader is still looking at the student.
            if ($rate === null || $rate <= 0) {
                $unpriced[] = trim(($course->course_code ? $course->course_code . ' — ' : '') . $course->course_name);
                continue;
            }

            $fee = $credits * $rate * ($halfRate ? 0.5 : 1.0);

            $totalFee    += $fee;
            $creditHours += $credits;

            $priced[] = [
                'course_id'   => $course->course_id,
                'course_code' => $course->course_code,
                'course_name' => $course->course_name,
                'school_id'   => $course->school_id,
                'school_name' => $course->school_name,
                'credits'     => round($credits, 2),
                'rate'        => round($rate, 2),
                'half_rate'   => $halfRate,
                'fee'         => round($fee, 2),
            ];
        }

        return [
            'totalFee'        => round($totalFee, 2),
            'courses'         => $priced,
            'courseCount'     => count($priced),
            'creditHours'     => round($creditHours, 2),
            'unpricedCourses' => $unpriced,
        ];
    }

    // ---------------------------------------------------------------- //

    /**
     * The procedure's cursor, verbatim in filters.
     *
     * Note what is NOT here: no course_exempted exclusion (unlike the summer calculator),
     * because the procedure does not apply one. Copied as-is so the page cannot price a
     * course the challan did not.
     */
    private function enrolledCourses($sessionId, $enrollmentNo)
    {
        if (empty($enrollmentNo)) {
            return [];
        }

        return DB::table('student_courses as e')
            ->join('courses_offered as co', 'e.sc_cms_course_co_id', '=', 'co.co_id')
            ->leftJoin('courses_two as crs', 'co.co_course_id', '=', 'crs.course_id')
            ->leftJoin('schools as sch', 'co.co_school_id', '=', 'sch.school_id')
            ->select(
                // The procedure matches GRADE_HISTORY on student_courses.SC_COURSE_ID,
                // NOT on courses_offered.CO_COURSE_ID. They are not always the same value,
                // so this must stay as it is.
                'e.sc_course_id as course_id',
                'co.co_school_id as school_id',
                'sch.school_name as school_name',
                'e.sc_pc_course_cr as credit_hours',
                'crs.course_code as course_code',
                'crs.course_cd_name as course_name'
            )
            ->whereRaw('LOWER(e.sc_sai_enrollment_no) = ?', [strtolower(trim((string) $enrollmentNo))])
            ->where('e.sc_enrollment_status', 1)
            ->whereIn('e.sc_status', ['A', 'Block', 'Withdrawal'])
            ->where('e.sc_co_sess_id', $sessionId)
            ->where('co.co_status', 'E')
            // Hard-coded in the procedure. Carried over rather than reasoned about: if it
            // is dropped there, drop it here too.
            ->where('e.sc_cms_course_co_id', '<>', 10433)
            ->get()
            ->all();
    }

    /** Which of these courses the student has taken in an EARLIER session. */
    private function repeatedCourseIds($sessionId, $enrollmentNo, array $courses)
    {
        $ids = [];
        foreach ($courses as $course) {
            $ids[] = $course->course_id;
        }
        $ids = array_values(array_unique(array_filter($ids)));
        if (empty($ids)) {
            return [];
        }

        $rows = DB::table('grade_history')
            ->select('gh_co_course_id')
            ->whereRaw('LOWER(gh_sai_enrollment_no) = ?', [strtolower(trim((string) $enrollmentNo))])
            ->whereIn('gh_co_course_id', $ids)
            ->where('gh_co_sess_id', '>=', self::GRADE_HISTORY_FROM_SESSION)
            ->where('gh_co_sess_id', '<>', $sessionId)
            ->get();

        $found = [];
        foreach ($rows as $row) {
            $found[(string) $row->gh_co_course_id] = true;
        }

        return $found;
    }

    /** The per-credit rate of the school that offers the course. */
    private function rateForSchool($sessionId, $schoolId, $roadmapSess, $degreeLevel)
    {
        $key = $sessionId . '|' . $schoolId . '|' . strtolower((string) $degreeLevel);
        if (array_key_exists($key, $this->rateCache)) {
            return $this->rateCache[$key];
        }

        $rate = $this->lookupRate($sessionId, $schoolId, $roadmapSess, $degreeLevel);
        if ($rate === null && !empty($degreeLevel)) {
            $rate = $this->lookupRate($sessionId, $schoolId, $roadmapSess, null);
        }

        return $this->rateCache[$key] = $rate;
    }

    private function lookupRate($sessionId, $schoolId, $roadmapSess, $degreeLevel)
    {
        $query = DB::table('fin_fee_structure')
            ->where('ffs_school_id', $schoolId)
            ->where('ffs_sess_id', $sessionId)
            ->where('ffs_fee_type', 'Credit_hours')
            ->where('ffs_status', 'E')
            ->whereRaw('NVL(ffs_credhr_amt, 0) > 0');

        if (!empty($degreeLevel)) {
            $query->whereRaw('UPPER(TRIM(NVL(ffs_degree_level, ?))) = ?', [
                strtoupper(trim((string) $degreeLevel)),
                strtoupper(trim((string) $degreeLevel)),
            ]);
        }

        $this->applyIntakeRange($query, $sessionId, $roadmapSess);

        $value = $query->orderByDesc('ffs_entry_datetime')->orderByDesc('ffs_id')->value('ffs_credhr_amt');

        return $value === null ? null : (float) $value;
    }

    /**
     * Same rule as ResolvesScholarshipFees::applyFeeStructureIntakeRange(), duplicated here
     * because this service must not depend on a Grader controller trait. Sessions up to 167
     * predate intake ranges and are not compared.
     */
    private function applyIntakeRange($query, $sessionId, $roadmapSess)
    {
        if ((int) $sessionId <= 167 || empty($roadmapSess)) {
            return $query;
        }

        return $query->where(function ($q) use ($roadmapSess) {
            $q->whereNull('FFS_INTAKE_FROM_SESS')->orWhere('FFS_INTAKE_FROM_SESS', '<=', $roadmapSess);
        })->where(function ($q) use ($roadmapSess) {
            $q->whereNull('FFS_INTAKE_TO_SESS')->orWhere('FFS_INTAKE_TO_SESS', '>=', $roadmapSess);
        });
    }

    private function empty()
    {
        return [
            'totalFee'        => 0.0,
            'courses'         => [],
            'courseCount'     => 0,
            'creditHours'     => 0.0,
            'unpricedCourses' => [],
        ];
    }
}
