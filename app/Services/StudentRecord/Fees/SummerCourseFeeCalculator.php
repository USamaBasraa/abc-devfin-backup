<?php

namespace App\Services\StudentRecord\Fees;

use DB;

/**
 * Replaces the pricing half of FIN_PRO_SUMMER_CREDIT_HOUR.
 *
 * A summer challan is priced COURSE BY COURSE, and each course is charged at the rate
 * of the school that OFFERS it (courses_offered.CO_SCHOOL_ID) — not the student's own
 * school. For a student taking courses across schools the two differ, which is why the
 * flat "total credit hours x the student's own rate" the page used before could not
 * reproduce what students were historically billed.
 *
 * A plain service rather than another trait method: both the fee resolver (Save) and
 * ShortSummerPoster (Post) need it, and they live in different namespaces.
 *
 * Deliberate divergences from the procedure, all flagged in the plan:
 *   - a course with no fee structure is REPORTED (unpricedCourses) instead of being
 *     silently charged 0 into a `testing` log table
 *   - repeatFee accumulates across every repeated course; the procedure overwrote
 *     v_repeat_fee each iteration and stored only the LAST course's surcharge
 */
class SummerCourseFeeCalculator
{
    /** GRADE_HISTORY is only consulted from this session onward. */
    const GRADE_HISTORY_FROM_SESSION = 163;

    /**
     * The 1.5x repeat surcharge applied only up to this session. From 167 onward a
     * repeated course is charged at the plain rate — see the IF at
     * FIN_PRO_SUMMER_CREDIT_HOUR line 172.
     */
    const REPEAT_SURCHARGE_UNTIL_SESSION = 166;

    /** @var array<string, float|null> memo for the per-school rate lookup */
    private $rateCache = [];

    /**
     * @return array{
     *   ok:bool, reason:string|null, message:string,
     *   courses:array, courseCount:int, creditHours:float,
     *   tuitionTotal:float, activityFee:float, repeatFee:float,
     *   unpricedCourses:array
     * }
     */
    public function calculate($sessionId, $enrollmentNo, $schoolId, $deptId, $progId, $roadmapSess = null, $degreeLevel = null)
    {
        $courses = $this->enrolledCourses($sessionId, $enrollmentNo);

        if (empty($courses)) {
            return $this->failure('no_enrollment',
                'Student is not enrolled in any billable course for this session — scholarship not allowed.');
        }

        $repeatIds = $this->repeatedCourseIds($sessionId, $enrollmentNo, $courses);
        $surcharge = ((int) $sessionId <= self::REPEAT_SURCHARGE_UNTIL_SESSION);

        $priced = [];
        $unpriced = [];
        $tuitionTotal = 0.0;
        $repeatFee = 0.0;

        foreach ($courses as $course) {
            $credits = (float) $course->credit_hours;
            $rate = $this->rateForSchool($sessionId, $course->school_id, $roadmapSess, $degreeLevel);

            if ($rate === null || $rate <= 0) {
                $unpriced[] = trim(($course->course_code ? $course->course_code . ' — ' : '') . $course->course_name);
                continue;
            }

            $isRepeat = isset($repeatIds[(string) $course->course_id]);
            $fee = $credits * $rate;
            if ($isRepeat && $surcharge) {
                $fee *= 1.5;
            }
            if ($isRepeat) {
                $repeatFee += $credits * $rate * 0.5;
            }

            $tuitionTotal += $fee;
            $priced[] = [
                'course_id'   => $course->course_id,
                'course_code' => $course->course_code,
                'course_name' => $course->course_name,
                'school_id'   => $course->school_id,
                'credits'     => round($credits, 2),
                'rate'        => round($rate, 2),
                'is_repeat'   => $isRepeat,
                'fee'         => round($fee, 2),
            ];
        }

        if (empty($priced)) {
            return $this->failure('no_fee_structure',
                'No Short/Summer_Course fee structure with a Per Credit Fee greater than 0 was found for '
                . 'the school(s) offering this student\'s courses. Please define it in the Master Fee Structure form.');
        }

        return [
            'ok'              => true,
            'reason'          => null,
            'message'         => '1',
            'courses'         => $priced,
            'courseCount'     => count($priced),
            // SC_ENROLLED_CR_HR: the procedure sums credits with a slightly WIDER filter
            // than the pricing cursor (no enrollment-pattern exclusion), so it is taken
            // separately rather than from the priced set.
            'creditHours'     => $this->enrolledCreditHours($sessionId, $enrollmentNo),
            'tuitionTotal'    => round($tuitionTotal, 2),
            'activityFee'     => $this->activityFee($sessionId, $schoolId, $deptId, $progId, $roadmapSess, $degreeLevel),
            'repeatFee'       => round($repeatFee, 2),
            'unpricedCourses' => $unpriced,
        ];
    }

    // ---------------------------------------------------------------- //

    /**
     * The billable summer courses. This filter is the invariant used across the app:
     * enrolled (status 1), an active-ish course status, the session's own offerings,
     * the offering itself enabled, and not exempted for this student.
     */
    private function enrolledCourses($sessionId, $enrollmentNo)
    {
        // courses_two is the catalogue (see GenerateCreditHrBulkChallanController:1740);
        // it is joined only to name the course in warnings, never for pricing.
        return DB::table('student_courses as e')
            ->join('courses_offered as co', 'e.sc_cms_course_co_id', '=', 'co.co_id')
            ->leftJoin('courses_two as crs', 'co.co_course_id', '=', 'crs.course_id')
            ->select(
                'co.co_course_id as course_id',
                'co.co_school_id as school_id',
                'e.sc_pc_course_cr as credit_hours',
                'crs.course_code as course_code',
                'crs.course_cd_name as course_name'
            )
            ->whereRaw('LOWER(e.sc_sai_enrollment_no) = ?', [strtolower(trim((string) $enrollmentNo))])
            ->where('e.sc_enrollment_status', 1)
            ->whereIn('e.sc_status', ['A', 'Block', 'Withdrawal'])
            ->where('e.sc_co_sess_id', $sessionId)
            ->whereRaw("UPPER(NVL(e.sc_sai_enrollment_no, '')) NOT LIKE '%SUM%'")
            ->where('co.co_status', 'E')
            ->whereRaw(
                "NOT EXISTS (SELECT 1 FROM course_exempted x
                              WHERE x.ce_session_id = ?
                                AND LOWER(TRIM(x.ce_enrollment_no)) = ?
                                AND x.ce_course_id = co.co_course_id
                                AND UPPER(TRIM(NVL(x.ce_status, 'E'))) = 'E')",
                [$sessionId, strtolower(trim((string) $enrollmentNo))]
            )
            ->get()
            ->all();
    }

    /**
     * SC_ENROLLED_CR_HR. The procedure's second SELECT drops the '%SUM%' exclusion, so
     * this total can legitimately exceed the sum of the priced courses.
     */
    private function enrolledCreditHours($sessionId, $enrollmentNo)
    {
        $total = DB::table('student_courses as e')
            ->join('courses_offered as co', 'e.sc_cms_course_co_id', '=', 'co.co_id')
            ->whereRaw('LOWER(e.sc_sai_enrollment_no) = ?', [strtolower(trim((string) $enrollmentNo))])
            ->where('e.sc_enrollment_status', 1)
            ->whereIn('e.sc_status', ['A', 'Block', 'Withdrawal'])
            ->where('e.sc_co_sess_id', $sessionId)
            ->where('co.co_status', 'E')
            ->whereRaw(
                "NOT EXISTS (SELECT 1 FROM course_exempted x
                              WHERE x.ce_session_id = ?
                                AND LOWER(TRIM(x.ce_enrollment_no)) = ?
                                AND x.ce_course_id = co.co_course_id
                                AND UPPER(TRIM(NVL(x.ce_status, 'E'))) = 'E')",
                [$sessionId, strtolower(trim((string) $enrollmentNo))]
            )
            ->sum('e.sc_pc_course_cr');

        return round((float) $total, 2);
    }

    /** Which of these courses the student has taken before. */
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
            ->get();

        $found = [];
        foreach ($rows as $row) {
            $found[(string) $row->gh_co_course_id] = true;
        }

        return $found;
    }

    /**
     * The per-credit rate for the school that offers the course.
     *
     * Degree level is a preference, not a filter: the procedure tries it first and
     * falls back to a structure without it. Values are inconsistent in the data
     * ('Undergraduate' / 'Undergradute' / 'Graduate' / …), so it is matched
     * case-insensitively and a miss simply falls through.
     */
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
            ->where('ffs_fee_type', 'Short/Summer_Course')
            ->where('ffs_status', 'E')
            ->whereRaw('NVL(ffs_credhr_amt, 0) > 0');

        if (!empty($degreeLevel)) {
            $query->whereRaw('UPPER(TRIM(NVL(ffs_degree_level, ?))) = ?', [
                strtoupper(trim((string) $degreeLevel)),
                strtoupper(trim((string) $degreeLevel)),
            ]);
        }

        $this->applyIntakeRange($query, $sessionId, $roadmapSess);

        // The procedure ranks an intake-range match first, then newest. The range is
        // already a filter here, so newest-first is the remaining tiebreak.
        $value = $query->orderByDesc('ffs_entry_datetime')->orderByDesc('ffs_id')->value('ffs_credhr_amt');

        return $value === null ? null : (float) $value;
    }

    /** One activity charge, from the STUDENT's own school/dept/program structure. */
    private function activityFee($sessionId, $schoolId, $deptId, $progId, $roadmapSess, $degreeLevel)
    {
        $lookup = function ($withDegreeLevel) use ($sessionId, $schoolId, $deptId, $progId, $roadmapSess, $degreeLevel) {
            $query = DB::table('fin_fee_structure')
                ->where('ffs_school_id', $schoolId)
                ->where('ffs_dept_id', $deptId)
                ->where('ffs_prog_id', $progId)
                ->where('ffs_sess_id', $sessionId)
                ->where('ffs_fee_type', 'Short/Summer_Course')
                ->where('ffs_status', 'E')
                ->whereRaw('NVL(ffs_credhr_amt, 0) > 0');

            if ($withDegreeLevel && !empty($degreeLevel)) {
                $query->whereRaw('UPPER(TRIM(NVL(ffs_degree_level, ?))) = ?', [
                    strtoupper(trim((string) $degreeLevel)),
                    strtoupper(trim((string) $degreeLevel)),
                ]);
            }

            $this->applyIntakeRange($query, $sessionId, $roadmapSess);

            return $query->orderByDesc('ffs_entry_datetime')->orderByDesc('ffs_id')->value('ffs_activity_amt');
        };

        $value = $lookup(true);
        if ($value === null) {
            $value = $lookup(false);
        }

        return round((float) $value, 2);
    }

    /**
     * Same rule as ResolvesScholarshipFees::applyFeeStructureIntakeRange(), duplicated
     * here because this service must not depend on a Grader controller trait.
     * Sessions up to 167 predate intake ranges and are not compared.
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

    private function failure($reason, $message)
    {
        return [
            'ok'              => false,
            'reason'          => $reason,
            'message'         => $message,
            'courses'         => [],
            'courseCount'     => 0,
            'creditHours'     => 0.0,
            'tuitionTotal'    => 0.0,
            'activityFee'     => 0.0,
            'repeatFee'       => 0.0,
            'unpricedCourses' => [],
        ];
    }
}
