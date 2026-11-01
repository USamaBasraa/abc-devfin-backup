<?php

namespace App\Http\Controllers\Grader\StudentRecord\Concerns;

use App\Services\StudentRecord\Fees\CreditHourRepeatFeeCalculator;
use App\Services\StudentRecord\Fees\SummerCourseFeeCalculator;
use Carbon\Carbon;
use DB;

/**
 * Shared scholarship fee-structure / challan-procedure logic for the REVISED
 * Student Record page (StudentRecordRevisedController).
 *
 * ---------------------------------------------------------------------------
 * THIS IS A DELIBERATE COPY.
 *
 * The canonical implementation lives in the `private` methods of
 * App\Http\Controllers\Grader\StudentRecord\StudentRecordController. That file
 * is frozen (the original page must keep working byte-for-byte), and PHP does
 * not let a subclass reach a parent's private methods, so the logic is copied
 * here rather than shared.
 *
 * DRIFT WARNING: when a rule changes in StudentRecordController, it must be
 * changed here too. Quirks are copied AS-IS on purpose — e.g.
 * resolveScholarshipChallanExecutionContext() keys on
 * strcasecmp($studentType, 'Existing') while the rest of the file uses
 * isEnrolledStudentType(). "Fixing" it here would make new-admission challan
 * regeneration diverge from the old page.
 *
 * Two intentional ADDITIONS over the original:
 *   1. resolveAcademicFeeStructure() also returns `arrearComponents` — the four
 *      one-time fee amounts that make up `oneTimeFeeTotal`, so the revised grid
 *      can cap each arrears component against its own fee-structure value.
 *   2. getArrearsRowsForStudent() — raw (targetable) fin_other_scholarships rows,
 *      which the original getStudentArrearsBreakdown() cannot give because it
 *      GROUP BYs the primary key away.
 *
 * Constants are exposed as methods, not `const`: constants in traits require
 * PHP 8.2 and this project supports ^7.3.
 * ---------------------------------------------------------------------------
 */
trait ResolvesScholarshipFees
{
    /**
     * Student types that ARE enrolled (looked up by enrollment no in
     * students_apply_info) but are NOT 'Existing' — they differ only in how they
     * match in fin_fee_structure, see resolveFeeStructureStudentType().
     *
     * 'New without Registration' is NOT here: that student has no enrollment
     * number yet and is looked up in admission_apply_info by AAI_ID.
     */
    protected function newAdmissionStudentTypes()
    {
        return ['New Admission FALL', 'New Admission SPRING', 'New Admission SUMMER'];
    }

    /**
     * Challan categories that can carry arrears (one-time fee waivers held in
     * FIN_OTHER_SCHOLARSHIPS.FOS_CHALLAN_CATEGORY).
     *
     * additional_Full_semester is deliberately excluded — it bills only the extra
     * credit hours on an existing full-semester challan, whose one-time fees are
     * already waived by that challan's own Full_semester arrears.
     *
     * Must stay in sync with StudentRecordController::ARREARS_CHALLAN_CATEGORIES
     * and ScholarshipAssignController::ARREARS_CHALLAN_CATEGORIES.
     */
    protected function arrearsChallanCategories()
    {
        return ['Full_semester', 'Credit_hours', 'Short/Summer_Course'];
    }

    /**
     * Challan categories whose challans are regenerated per credit-hour by the
     * procedure. These are the only categories that CAN lock — the lock itself is
     * conditional, see isScholarshipRowLockedByChallan().
     */
    protected function lockedChallanCategories()
    {
        return ['additional_Full_semester', 'Credit_hours'];
    }

    /**
     * Intake ranges on FIN_FEE_STRUCTURE were only introduced from the session
     * AFTER this one, so they are meaningless for older sessions.
     */
    protected function intakeRangeFromSession()
    {
        return 167;
    }

    /**
     * Sessions that charge for REPEAT credit hours (at half the per-credit rate),
     * on top of the enrolled credit hours:
     *   164 = Summer 2025, 165 = Fall 2025, 166 = Spring 2026.
     */
    protected function repeatCreditSessions()
    {
        return [164, 165, 166];
    }

    /** The default challan category for rows written before the column existed. */
    protected function defaultChallanCategory()
    {
        return 'Full_semester';
    }

    /**
     * Additional credit hours from a page's own STAGED snapshot, when the live challan
     * they were read from no longer exists.
     *
     * Only reached for additional_Full_semester, and only after the live-challan lookup
     * has come up empty — which is exactly the window between Save (which cancels and
     * deletes that challan) and Post (which re-issues it).
     *
     * Opt-in: the default is "no fallback", so the frozen StudentRecordController keeps
     * behaving byte-for-byte as it does today. StudentRecordRevisedController overrides it.
     */
    protected function stagedAdditionalCreditHours($session, $studentId)
    {
        return 0.0;
    }

    // ------------------------------------------------------------------ //
    // Student type helpers
    // ------------------------------------------------------------------ //

    protected function isEnrolledStudentType($studentType)
    {
        $studentType = trim((string) $studentType);
        return $studentType === 'Existing'
            || in_array($studentType, $this->newAdmissionStudentTypes(), true);
    }

    /**
     * The FFS_STUDENT_TYPE to match in fin_fee_structure (compared upper-cased).
     *
     *   Existing                  -> 'EXISTING'
     *   New Admission FALL/…      -> that exact value, e.g. 'NEW ADMISSION FALL'
     *   New without Registration  -> derived from the selected session's season,
     *                                e.g. session "Fall 2026" -> 'NEW ADMISSION FALL'
     */
    protected function resolveFeeStructureStudentType($studentType, $session)
    {
        $studentType = trim((string) $studentType);

        if (in_array($studentType, $this->newAdmissionStudentTypes(), true)) {
            return strtoupper($studentType);
        }
        if ($studentType === 'Existing') {
            return 'EXISTING';
        }

        $sessName = DB::table('academic_sessions')->where('sess_id', $session)->value('sess_name');
        $season = $sessName ? strtoupper(strtok(trim($sessName), ' ')) : '';
        return 'NEW ADMISSION ' . $season;
    }

    /**
     * A student is "summer admission" when their enrollment number contains SUM
     * (e.g. SUM24-0021). New admissions have no enrollment number yet and are
     * treated as summer-admission (tuition based).
     */
    protected function isSummerAdmissionEnrollment($studentType, $enrollment)
    {
        if (!$this->isEnrolledStudentType($studentType)) {
            return true;
        }
        return stripos((string) $enrollment, 'SUM') !== false;
    }

    protected function challanCategoryHasArrears($challanCategory)
    {
        return in_array(trim((string) $challanCategory), $this->arrearsChallanCategories(), true);
    }

    protected function isLockedChallanCategory($challanCategory)
    {
        return in_array(trim((string) $challanCategory), $this->lockedChallanCategories(), true);
    }

    // ------------------------------------------------------------------ //
    // Fee structure
    // ------------------------------------------------------------------ //

    /**
     * Repeat credit hours for a student in a session, from FIN_STUDENTS_CREDITS_DATA.
     * Returns 0 when the session does not bill repeat credits, or there is no row.
     *
     * NO LONGER USED for pricing. The revised page now reproduces the procedure's own
     * per-course repeat loop (CreditHourRepeatFeeCalculator), which prices each repeated
     * course at the rate of the school that OFFERS it — something this figure, a bare
     * credit-hour total, cannot express. Kept only as the mirror of
     * StudentRecordController::resolveRepeatCreditHours(); do not reintroduce it into the
     * amount maths.
     */
    protected function resolveRepeatCreditHours($session, $enrollmentNo)
    {
        if (!in_array((int) $session, $this->repeatCreditSessions(), true) || empty($enrollmentNo)) {
            return 0.0;
        }

        $repeat = DB::table('FIN_STUDENTS_CREDITS_DATA')
            ->whereRaw('UPPER(TRIM(FSCD_REGNO)) = ?', [strtoupper(trim((string) $enrollmentNo))])
            ->where('FSCD_STD_ENROLLMENT_SESSION', $session)
            ->orderByDesc('FSCD_ID')
            ->value('FSCD_STD_REPEAT_CREDITS');

        return max(0.0, (float) $repeat);
    }

    /**
     * Apply the fee-structure intake-range match to a fin_fee_structure query.
     *
     *  session >  167 : the student's intake (roadmap) session must fall inside the
     *                   row's range. Rows with no range defined (NULL) still match.
     *  session <= 167 : the intake range is NOT compared at all — these sessions
     *                   predate intake ranges.
     */
    /**
     * Should Short/Summer_Course tuition be priced course by course?
     *
     * OFF by default so ScholarshipFormController — which shares this trait — keeps its
     * existing flat "total credit hours x the student's own rate" behaviour untouched.
     * StudentRecordRevisedController overrides this to true: it reimplements
     * FIN_PRO_SUMMER_CREDIT_HOUR, whose basis is per course at the OFFERING school's
     * rate, and the two disagree whenever a student takes courses from another school.
     */
    protected function usesPerCourseSummerPricing()
    {
        return false;
    }

    protected function applyFeeStructureIntakeRange($query, $session, $roadmapSess)
    {
        if ((int) $session <= $this->intakeRangeFromSession()) {
            return $query;
        }

        if (!empty($roadmapSess)) {
            $query->where(function ($q) use ($roadmapSess) {
                $q->whereNull('FFS_INTAKE_FROM_SESS')
                  ->orWhere('FFS_INTAKE_FROM_SESS', '<=', $roadmapSess);
            })->where(function ($q) use ($roadmapSess) {
                $q->whereNull('FFS_INTAKE_TO_SESS')
                  ->orWhere('FFS_INTAKE_TO_SESS', '>=', $roadmapSess);
            });
        }

        return $query;
    }

    /**
     * Resolve the academic fee structure for a student by challan category.
     *
     *   - Full_semester            -> FFS_TUTIONFEE_AMT (arrears apply)
     *   - Credit_hours             -> FFS_CREDHR_AMT, requires enrolled courses
     *   - additional_Full_semester -> FFS_CREDHR_AMT, requires an UNPAID
     *                                 Full_semester challan with enrolled credit hours
     *   - Short/Summer_Course      -> TWO rows may exist; chosen by enrollment no:
     *       SUM… / new admission -> the row with FFS_TUTIONFEE_AMT > 0
     *       Fall / Spring        -> the row with FFS_CREDHR_AMT  > 0
     *
     * ADDITION over StudentRecordController: the successful result also carries
     * `arrearComponents` (admission / security / activity / medical) taken from the
     * SAME matched fee row that produced `oneTimeFeeTotal`, so the revised grid can
     * cap each arrears component individually. No extra query.
     */
    protected function resolveAcademicFeeStructure($session, $student_id, $studentType, $challanCategory = 'Full_semester')
    {
        $isExisting = $this->isEnrolledStudentType($studentType);

        // 1) student school / dept / program + roadmap session
        if ($isExisting) {
            $stu = DB::table('students_apply_info')
                ->select(
                    'SAI_SDPO_SCHOOL_ID as school_id',
                    'SAI_SDPO_DEPT_ID as dept_id',
                    'SAI_SDPO_PROG_ID as prog_id',
                    'SAI_SDPO_SESS_ID as roadmap_sess'
                )
                ->whereRaw('LOWER(SAI_ENROLLMENT_NO) = ?', [strtolower((string) $student_id)])
                ->first();
        } else {
            $stu = DB::table('admission_apply_info')
                ->select(
                    'AAI_SDPO_SCHOOL_ID as school_id',
                    'AAI_SDPO_DEPT_ID as dept_id',
                    'AAI_SDPO_PROG_ID as prog_id',
                    'AAI_SDPO_SESS_ID as roadmap_sess'
                )
                ->where('AAI_ID', $student_id)
                ->first();
        }

        if (empty($stu)) {
            return ['ok' => false, 'reason' => 'no_fee_structure', 'message' => 'Student record not found.'];
        }

        // 2) challan category -> ffs_fee_type + credit-hour checks
        $creditHours       = null;
        $repeatCredits     = 0.0;     // repeated credit hours, for display only
        $repeat            = null;    // CreditHourRepeatFeeCalculator result, per-credit categories
        $creditBased       = false;   // per-credit amount math
        $summerCreditBased = false;   // per-credit amount math, summer fee row
        $amountPredicate   = null;    // disambiguates the two Short/Summer rows
        $summer            = null;    // SummerCourseFeeCalculator result, summer only

        // Arrears applicability is INDEPENDENT of the amount math: Credit_hours uses
        // per-credit pricing AND carries arrears, while additional_Full_semester uses
        // per-credit pricing and carries none.
        $arrearsApplicable = $this->challanCategoryHasArrears($challanCategory);

        switch ($challanCategory) {
            case 'Credit_hours':
                $creditBased = true;
                $ffsFeeType  = 'Credit_hours';
                $creditHours = (float) DB::table('student_courses')
                    ->where('SC_CO_SESS_ID', $session)
                    ->whereRaw('LOWER(SC_SAI_ENROLLMENT_NO) = ?', [strtolower((string) $student_id)])
                    ->where('SC_ENROLLMENT_STATUS', 1)
                    ->whereIn('SC_STATUS', ['Withdrawal', 'Block', 'A'])
                    ->sum('SC_PC_COURSE_CR');
                if ($isExisting) {
                    // Repeated courses are priced ONE BY ONE at the offering school's rate,
                    // reproducing FIN_CREDIT_HR_WISE_CHALLAN's v_total_repat loop. The old
                    // "FSCD repeat credits x the student's own rate / 2" could not see the
                    // offering school at all, and so mis-priced a repeat taken elsewhere.
                    $repeat = (new CreditHourRepeatFeeCalculator())->calculate(
                        $session,
                        $student_id,
                        isset($stu->roadmap_sess) ? $stu->roadmap_sess : null,
                        DB::table('programs')->where('prog_id', $stu->prog_id)->value('PROG_DEGREE_LEVEL')
                    );
                    $repeatCredits = $repeat['creditHours'];
                }
                if (!$creditHours || $creditHours <= 0) {
                    return ['ok' => false, 'reason' => 'no_enrollment', 'message' => 'Student is not enrolled in any course for this session — scholarship not allowed.'];
                }
                // The degree-level credit limit is enforced ONLY when the student has
                // no existing Credit_hours challan for the session.
                $hasCreditChallan = DB::table('student_challan')
                    ->where('SC_SESSION_ID', $session)
                    ->where('SC_CHALLAN_TYPE', 'Credit_hours')
                    ->where(function ($q) use ($student_id) {
                        if (is_numeric($student_id)) {
                            $q->where('SC_APP_ID', $student_id);
                        } else {
                            $q->whereRaw('LOWER(SC_ENROLLMENT_NO) = ?', [strtolower((string) $student_id)]);
                        }
                    })
                    ->exists();
                if (!$hasCreditChallan) {
                    $degreeLevel = DB::table('programs')->where('prog_id', $stu->prog_id)->value('PROG_DEGREE_LEVEL');
                    $isPostgrad  = in_array(strtolower((string) $degreeLevel), ['postgraduate', 'doctorate']);
                    $creditLimit = $isPostgrad ? 6 : 9.5;
                    if ($creditHours > $creditLimit) {
                        return [
                            'ok'       => false,
                            'reason'   => 'credit_out_of_range',
                            'switchTo' => 'Full_semester',
                            'message'  => 'Enrolled credit hours (' . $creditHours . ') exceed the credit-hour limit for ' . ($degreeLevel ?: 'this program') . ' (max ' . $creditLimit . '). Challan Category switched to Full Semester.',
                        ];
                    }
                }
                break;

            case 'additional_Full_semester':
                $creditBased = true;
                $ffsFeeType  = 'Credit_hours';
                $challan = DB::table('student_challan')
                    ->select('SC_ENROLLED_CR_HR')
                    ->whereRaw('LOWER(SC_ENROLLMENT_NO) = ?', [strtolower((string) $student_id)])
                    ->where('SC_SESSION_ID', $session)
                    ->where('SC_CHALLAN_TYPE', 'Full_semester')
                    ->whereNotNull('SC_ENROLLED_CR_HR')
                    ->where('SC_ENROLLED_CR_HR', '!=', 0)
                    ->where('SC_CHALLAN_STATUS', 'UNPAID')
                    ->orderByDesc('id')
                    ->first();
                // SC_ENROLLED_CR_HR is the procedure's V_sc_HR — enrolled hours MINUS the
                // 18 (UG) or 12 (PG) covered by the full-semester challan. It is read from
                // the challan rather than recounted, because nothing records WHICH courses
                // make up the additional hours.
                //
                // FALLBACK: Save cancels this very challan, and fin_bulk_challan_cancellation
                // DELETEs the row — FIN_STUDENT_CANCEL_CHALLAN does not carry
                // SC_ENROLLED_CR_HR, so the figure is unrecoverable from there. Between Save
                // and Post there is therefore no live challan to read, and without this the
                // page would tell the grader to "generate the additional credit-hour challan
                // first" for a student who has one staged and waiting to be posted.
                //
                // The live challan always wins; the staged snapshot is consulted only when
                // there is none. Opt-in via a hook so the frozen page keeps its behaviour.
                $creditHours = !empty($challan)
                    ? (float) $challan->sc_enrolled_cr_hr
                    : (float) $this->stagedAdditionalCreditHours($session, $student_id);

                if ($creditHours <= 0) {
                    return ['ok' => false, 'reason' => 'no_challan', 'message' => 'Please generate the additional credit-hour challan for this student first.'];
                }
                // The procedure adds the same per-course repeat total on this branch, so it
                // is added here too. Previously this category carried no repeat fee at all.
                $repeat = (new CreditHourRepeatFeeCalculator())->calculate(
                    $session,
                    $student_id,
                    isset($stu->roadmap_sess) ? $stu->roadmap_sess : null,
                    DB::table('programs')->where('prog_id', $stu->prog_id)->value('PROG_DEGREE_LEVEL')
                );
                $repeatCredits = $repeat['creditHours'];
                break;

            case 'Short/Summer_Course':
                $ffsFeeType = 'Short/Summer_Course';
                // Two rows can share this fee type. Pick by enrollment number, and
                // filter on the populated amount so the choice is never left to
                // Oracle's row order. NVL is required: `FFS_CREDHR_AMT > 0` is
                // UNKNOWN (not false) for a NULL column, silently dropping the row.
                if ($this->isSummerAdmissionEnrollment($studentType, $student_id)) {
                    $amountPredicate = 'NVL(FFS_TUTIONFEE_AMT, 0) > 0';
                } else {
                    $summerCreditBased = true;
                    $amountPredicate   = 'NVL(FFS_CREDHR_AMT, 0) > 0';

                    if ($this->usesPerCourseSummerPricing()) {
                        // Summer tuition priced COURSE BY COURSE, each at the rate of the
                        // school that OFFERS it — reproducing FIN_PRO_SUMMER_CREDIT_HOUR.
                        // A flat "credits x the student's own rate" mis-prices anyone
                        // taking courses from another school.
                        $summer = (new SummerCourseFeeCalculator())->calculate(
                            $session,
                            $student_id,
                            $stu->school_id,
                            $stu->dept_id,
                            $stu->prog_id,
                            isset($stu->roadmap_sess) ? $stu->roadmap_sess : null,
                            DB::table('programs')->where('prog_id', $stu->prog_id)->value('PROG_DEGREE_LEVEL')
                        );

                        if (empty($summer['ok'])) {
                            return ['ok' => false, 'reason' => $summer['reason'], 'message' => $summer['message']];
                        }

                        $creditHours = (float) $summer['creditHours'];
                    } else {
                        $creditHours = (float) DB::table('student_courses')
                            ->where('SC_CO_SESS_ID', $session)
                            ->whereRaw('LOWER(SC_SAI_ENROLLMENT_NO) = ?', [strtolower((string) $student_id)])
                            ->where('SC_ENROLLMENT_STATUS', 1)
                            ->whereIn('SC_STATUS', ['Withdrawal', 'Block', 'A'])
                            ->sum('SC_PC_COURSE_CR');
                        if (!$creditHours || $creditHours <= 0) {
                            return ['ok' => false, 'reason' => 'no_enrollment', 'message' => 'Student is not enrolled in any course for this session — scholarship not allowed.'];
                        }
                    }
                    // No degree-level credit limit on the summer path.
                }
                break;

            case 'Full_semester':
            default:
                $ffsFeeType = 'Full_semester';
                break;
        }

        // 3) fee-structure row for the resolved fee type
        $query = DB::table('fin_fee_structure')
            ->select(
                'FFS_ID as ffs_id',
                'FFS_TUTIONFEE_AMT as fee_base',
                'FFS_CREDHR_AMT as credhr_amt',
                'FFS_ADMISSION_AMT as admission_amt',
                'FFS_SECURITY_AMT as security_amt',
                'FFS_ACTIVITY_AMT as activity_amt',
                'FFS_MEDICAL_AMT as medical_amt'
            )
            ->where('ffs_sess_id', $session)
            ->where('ffs_school_id', $stu->school_id)
            ->where('ffs_dept_id', $stu->dept_id)
            ->where('ffs_prog_id', $stu->prog_id)
            ->where('ffs_fee_type', $ffsFeeType)
            ->where('ffs_status', 'E');

        if (!empty($amountPredicate)) {
            $query->whereRaw($amountPredicate);
        }

        $query->whereRaw('UPPER(ffs_student_type) = ?', [
            $this->resolveFeeStructureStudentType($studentType, $session),
        ]);

        $this->applyFeeStructureIntakeRange($query, $session, isset($stu->roadmap_sess) ? $stu->roadmap_sess : null);

        // Deterministic pick: newest wins when a key is (mis)configured with duplicates.
        $fee = $query->orderByDesc('FFS_ID')->first();
        if (empty($fee)) {
            $sessName = DB::table('academic_sessions')->where('sess_id', $session)->value('sess_name');
            $needed = 'Fee Type "' . $ffsFeeType . '"';
            if ($amountPredicate) {
                $needed .= $summerCreditBased
                    ? ' with a Per Credit Fee greater than 0'
                    : ' with a Tuition Fee greater than 0';
            }
            return [
                'ok'      => false,
                'reason'  => 'no_fee_structure',
                'message' => 'No fee structure found for ' . $needed
                    . ' in ' . ($sessName ?: 'the selected session')
                    . ' for this student\'s school / department / program. Please define it in the Master Fee Structure form.',
            ];
        }

        // 4) build the structured result.
        //
        // The one-time fee total is the arrears CAP and comes from the SAME fee row
        // that priced the scholarship. For Credit_hours the Master Fee Structure form
        // keeps Tuition/Admission/Security at 0 and lets the user set Medical +
        // Activity — so in practice the Credit_hours cap is Medical + Activity.
        // Categories without arrears report a cap of 0 and zeroed components.
        $components = $arrearsApplicable
            ? [
                'admission' => (float) $fee->admission_amt,
                'security'  => (float) $fee->security_amt,
                'activity'  => (float) $fee->activity_amt,
                'medical'   => (float) $fee->medical_amt,
            ]
            : ['admission' => 0.0, 'security' => 0.0, 'activity' => 0.0, 'medical' => 0.0];

        $oneTime = array_sum($components);

        if ($creditBased || $summerCreditBased) {
            $perCredit = (float) $fee->credhr_amt;
            // FIN_CREDIT_HR_WISE_CHALLAN:
            //   V_FFS_CREDHR_T_AMT = V_FFS_CREDHR_AMT * V_sc_HR + v_total_repat
            // Base tuition is the STUDENT'S OWN per-credit rate times their billable hours.
            // Only the repeat surcharge is priced per course at the offering school's rate —
            // see CreditHourRepeatFeeCalculator for why that distinction matters.
            $repeatFee = ($repeat !== null) ? (float) $repeat['totalFee'] : 0.0;
            $base = ($perCredit * $creditHours) + $repeatFee;

            $result = [
                'ok'                => true,
                'reason'            => null,
                'message'           => '1',
                'feeType'           => $ffsFeeType,
                // ffsId + school/dept/prog make a saved snapshot traceable back to the
                // exact fin_fee_structure row that priced it.
                'ffsId'             => isset($fee->ffs_id) ? $fee->ffs_id : null,
                'schoolId'          => $stu->school_id,
                'deptId'            => $stu->dept_id,
                'progId'            => $stu->prog_id,
                'referenceFee'      => $perCredit,
                'referenceLabel'    => 'Per Credit Fee',
                'amountBase'        => $base,
                'feeLineTotal'      => $base,
                'creditHours'       => $creditHours,
                'repeatCredits'     => $repeatCredits,
                'oneTimeFeeTotal'   => $oneTime,
                'arrearsApplicable' => $arrearsApplicable,
                'arrearComponents'  => $components,
            ];

            // The repeat surcharge, itemised. The grid shows the courses and the schools
            // that priced them, because a repeat taken at another school is charged at a
            // rate the grader cannot look up anywhere on this page.
            if ($repeat !== null && $repeat['totalFee'] > 0) {
                $result['repeatFee']       = $repeat['totalFee'];
                $result['repeatCourses']   = $repeat['courses'];
                $result['unpricedCourses'] = $repeat['unpricedCourses'];
            } elseif ($repeat !== null && !empty($repeat['unpricedCourses'])) {
                // Repeats found, none priceable — the challan is short by their fee.
                $result['unpricedCourses'] = $repeat['unpricedCourses'];
            }

            // Summer overrides the flat per-credit maths with the per-course total.
            // There is no single rate to quote, so the reference cell shows the total
            // and the course list explains it.
            if ($summer !== null) {
                $result['referenceFee']    = $summer['tuitionTotal'];
                $result['referenceLabel']  = 'Summer Courses';
                $result['amountBase']      = $summer['tuitionTotal'];
                $result['feeLineTotal']    = $summer['tuitionTotal'];
                $result['summerCourses']   = $summer['courses'];
                $result['courseCount']     = $summer['courseCount'];
                $result['repeatFee']       = $summer['repeatFee'];
                $result['summerActivity']  = $summer['activityFee'];
                $result['unpricedCourses'] = $summer['unpricedCourses'];
            }

            return $result;
        }

        $tuition = (float) $fee->fee_base;

        return [
            'ok'                => true,
            'reason'            => null,
            'message'           => '1',
            'feeType'           => $ffsFeeType,
            'ffsId'             => isset($fee->ffs_id) ? $fee->ffs_id : null,
            'schoolId'          => $stu->school_id,
            'deptId'            => $stu->dept_id,
            'progId'            => $stu->prog_id,
            'referenceFee'      => $tuition,
            'referenceLabel'    => 'Tuition Fee',
            'amountBase'        => $tuition,
            'feeLineTotal'      => $tuition,
            'creditHours'       => null,
            'repeatCredits'     => 0,
            'oneTimeFeeTotal'   => $oneTime,
            'arrearsApplicable' => $arrearsApplicable,
            'arrearComponents'  => $components,
        ];
    }

    // ------------------------------------------------------------------ //
    // Arrears (fin_other_scholarships)
    // ------------------------------------------------------------------ //

    /**
     * ADDITION over StudentRecordController.
     *
     * Raw, targetable fin_other_scholarships rows for a student in a session.
     *
     * getStudentArrearsBreakdown() cannot be used to drive an upsert: it GROUP BYs
     * the primary key away, and it matches on UPPER(TRIM(fos_enrollment_no)) ONLY
     * when an enrollment is present — so rows written with an app id but no
     * enrollment are invisible to it while still being found by the write path,
     * which would silently overwrite them.
     *
     * The student predicate here is the same one the write path uses:
     * fos_app_id OR UPPER(TRIM(fos_enrollment_no)).
     */
    protected function getArrearsRowsForStudent($aaiId, $sessionId, $enrollmentNo = null)
    {
        if (empty($sessionId) || (empty($aaiId) && empty($enrollmentNo))) {
            return [];
        }

        return DB::table('fin_other_scholarships')
            ->select(
                'id',
                'fos_scholarship_type',
                DB::raw("NVL(fos_challan_category, '" . $this->defaultChallanCategory() . "') as fos_challan_category"),
                DB::raw('NVL(fos_admission_fee, 0) as fos_admission_fee'),
                DB::raw('NVL(fos_security_fee, 0) as fos_security_fee'),
                DB::raw('NVL(fos_activity_charges, 0) as fos_activity_charges'),
                DB::raw('NVL(fos_medical_insurance, 0) as fos_medical_insurance'),
                DB::raw('NVL(fos_stipend, 0) as fos_stipend')
            )
            ->where('fos_sess_id', $sessionId)
            ->where(function ($q) use ($aaiId, $enrollmentNo) {
                if (!empty($aaiId)) {
                    $q->where('fos_app_id', $aaiId);
                }
                if (!empty($enrollmentNo)) {
                    $q->orWhereRaw('UPPER(TRIM(fos_enrollment_no)) = ?', [strtoupper(trim((string) $enrollmentNo))]);
                }
            })
            ->orderBy('id')
            ->get()
            ->all();
    }

    /**
     * Locate the single fin_other_scholarships row for (student, session, type,
     * challan category), using EXACTLY the predicate
     * ScholarshipAssignController::submitScholarship() treats as a duplicate.
     *
     * An equality-only match on fos_app_id would insert a second row alongside a
     * legacy NULL-category or enrollment-only row, and getStudentArrearsBreakdown()
     * SUMs duplicates — silently doubling the student's arrears.
     */
    protected function findArrearsRow($aaiId, $enrollmentNo, $sessionId, $schType, $challanCategory)
    {
        return DB::table('fin_other_scholarships')
            ->where('fos_sess_id', $sessionId)
            ->where('fos_scholarship_type', $schType)
            ->whereRaw('NVL(fos_challan_category, ?) = ?', [$this->defaultChallanCategory(), $challanCategory])
            ->where(function ($q) use ($aaiId, $enrollmentNo) {
                if (!empty($aaiId)) {
                    $q->where('fos_app_id', $aaiId);
                }
                if (!empty($enrollmentNo)) {
                    $q->orWhereRaw('UPPER(TRIM(fos_enrollment_no)) = ?', [strtoupper(trim((string) $enrollmentNo))]);
                }
            })
            ->orderBy('id')
            ->first();
    }

    // ------------------------------------------------------------------ //
    // Challan procedures
    // ------------------------------------------------------------------ //

    /**
     * Whether a saved credit-hour scholarship row is immutable (no edit / delete).
     *
     * The lock is CONDITIONAL: it applies only when the student's regenerated
     * challan actually carries a scholarship amount for this session.
     */
    protected function isScholarshipRowLockedByChallan($challanCategory, $appId, $enrollment, $sessionId)
    {
        $cat = trim((string) $challanCategory);
        if (!in_array($cat, $this->lockedChallanCategories(), true)) {
            return false;
        }

        $q = DB::table('student_challan')
            ->where('SC_SESSION_ID', $sessionId)
            ->whereRaw('NVL(SC_SCHOLARSHIP_AMOUNT, 0) > 0')
            // A CANCELLED challan releases the lock.
            ->whereRaw("UPPER(TRIM(NVL(SC_CHALLAN_STATUS, 'X'))) NOT IN ('CANCELLED', 'CANCEL FOR REISSUANCE')")
            ->where(function ($w) use ($appId, $enrollment) {
                if (!empty($appId)) {
                    $w->where('SC_APP_ID', $appId);
                }
                if (!empty($enrollment)) {
                    $w->orWhereRaw('UPPER(TRIM(SC_ENROLLMENT_NO)) = ?', [strtoupper(trim((string) $enrollment))]);
                }
            });

        if ($cat === 'additional_Full_semester') {
            $q->where('SC_CHALLAN_TYPE', 'Full_semester')->where('SC_ENROLLED_CR_HR', '>', 0);
        } else {   // Credit_hours
            $q->where('SC_CHALLAN_TYPE', 'Credit_hours');
        }

        return $q->exists();
    }

    /**
     * Resolve the challan, student metadata and installment date pair required to
     * execute the scholarship challan procedures.
     *
     * NOTE the strcasecmp($studentType, 'Existing') below: copied verbatim from
     * StudentRecordController::resolveScholarshipChallanExecutionContext(). It does
     * NOT use isEnrolledStudentType(), so a 'New Admission FALL' student takes the
     * admission_apply_info branch here. Do not "fix" it — the old page behaves this
     * way and the two must not diverge.
     */
    protected function resolveScholarshipChallanExecutionContext($aai_id, $sessions, $challanCategory, $studentType, $enrollmentNo = null)
    {
        $challanCategory = trim((string) $challanCategory);
        $studentType = trim((string) $studentType);
        $isExisting = strcasecmp($studentType, 'Existing') === 0;
        $normalizedEnrollment = strtoupper(trim((string) $enrollmentNo));

        $challanQuery = DB::table('student_challan')
            ->select(
                'id',
                'challan_no',
                'SC_APP_ID',
                'SC_SESSION_ID',
                'SC_CHALLAN_TYPE',
                'SC_STUDENT_STATUS',
                'SC_ENROLLED_CR_HR',
                'SC_ENROLLMENT_NO'
            )
            ->where('SC_APP_ID', $aai_id)
            ->where('SC_SESSION_ID', $sessions);

        if ($challanCategory === 'additional_Full_semester') {
            $challanQuery->where('SC_CHALLAN_TYPE', 'Full_semester')
                ->where('SC_ENROLLED_CR_HR', '>', 0);
        } else {
            $challanQuery->where('SC_CHALLAN_TYPE', $challanCategory);
        }

        // Credit-hour categories regenerate an EXISTING challan, so they require a
        // PRESENT (UNPAID) one.
        $isCreditHourCategory = in_array($challanCategory, $this->lockedChallanCategories(), true);
        if ($isCreditHourCategory) {
            $challanQuery->whereRaw("UPPER(TRIM(NVL(SC_CHALLAN_STATUS, 'X'))) = 'UNPAID'");
        }

        $challan = $challanQuery->first();
        if (empty($challan)) {
            if ($isCreditHourCategory) {
                $label = $challanCategory === 'additional_Full_semester' ? 'Additional Credit Hr' : 'Credit Hour';
                return [
                    'ok' => false,
                    'reason' => 'credit_challan_required',
                    'message' => 'No unpaid ' . $label . ' challan exists for this student in the selected session. Please (re)generate the credit-hour challan first, then Save & Post.',
                ];
            }
            return [
                'ok' => false,
                'reason' => 'no_challan',
                'message' => 'Scholarship detail saved successfully. No existing challan was found for the selected category/session, so challan regeneration procedure was not executed.',
            ];
        }

        $scholarshipStudentType = isset($challan->sc_student_status) ? $challan->sc_student_status : null;
        if (empty($scholarshipStudentType)) {
            return [
                'ok' => false,
                'reason' => 'missing_student_type',
                'message' => 'Scholarship process failed because the existing challan student status could not be resolved.',
            ];
        }

        // Dates come from FIN_CHALLAN_INSTALLMENTS; on any gap fall back to today + 7.
        $installment = null;

        if ($isExisting) {
            $lookupEnrollment = $normalizedEnrollment !== ''
                ? $normalizedEnrollment
                : strtoupper((string) (isset($challan->sc_enrollment_no) ? $challan->sc_enrollment_no : ''));

            $studentInfo = DB::table('students_apply_info')
                ->select(
                    'SAI_SDPO_SCHOOL_ID as school_id',
                    'SAI_SDPO_DEPT_ID as dept_id',
                    'SAI_SDPO_PROG_ID as prog_id'
                )
                ->whereRaw('LOWER(SAI_ENROLLMENT_NO) = ?', [strtolower($lookupEnrollment)])
                ->first();

            if (!empty($studentInfo)) {
                $installment = $this->resolveFutureInstallmentPair(
                    $sessions,
                    $studentInfo->school_id,
                    $studentInfo->dept_id,
                    $studentInfo->prog_id,
                    'CMS',
                    $scholarshipStudentType
                );
            }
        } else {
            $studentInfo = DB::table('admission_apply_info')
                ->select(
                    'AAI_SDPO_SCHOOL_ID as school_id',
                    'AAI_SDPO_DEPT_ID as dept_id',
                    'AAI_SDPO_PROG_ID as prog_id',
                    'AAI_ADMISSION_SCH_ID as admission_sch_id'
                )
                ->where('AAI_ID', $aai_id)
                ->first();

            if (!empty($studentInfo)) {
                $admissionRound = DB::table('admission_schedules')
                    ->where('ADMISSION_SCH_ID', $studentInfo->admission_sch_id)
                    ->value('ADMISSION_SCH_ROUND');

                $admissionTypeMap = ['1' => 'early', '2' => 'regular', '3' => 'rolling'];
                $admissionType = isset($admissionTypeMap[(string) $admissionRound])
                    ? $admissionTypeMap[(string) $admissionRound]
                    : (string) $admissionRound;
                $admissionTypeCandidates = array_values(array_filter(array_unique([
                    (string) $admissionRound,
                    $admissionType,
                ])));

                $installment = $this->resolveFutureInstallmentPair(
                    $sessions,
                    $studentInfo->school_id,
                    $studentInfo->dept_id,
                    $studentInfo->prog_id,
                    'Admission',
                    $scholarshipStudentType,
                    $admissionTypeCandidates
                );
            }
        }

        if (!empty($installment['ok'])) {
            $dueDate = $installment['due_date'];
            $validDate = $installment['valid_date'];
        } else {
            $dueDate = Carbon::now('Asia/Karachi')->addDays(7)->toDateString();
            $validDate = Carbon::now('Asia/Karachi')->addDays(7)->toDateString();
        }

        $procedureEnrollment = $normalizedEnrollment !== ''
            ? $normalizedEnrollment
            : (string) (isset($challan->sc_enrollment_no) ? $challan->sc_enrollment_no : '');

        return [
            'ok' => true,
            'procedure_name' => $this->resolveScholarshipProcedureName($challanCategory, $studentType, $procedureEnrollment),
            'challan_category' => $challanCategory,
            'challan_student_status' => $scholarshipStudentType,
            'challan' => $challan,
            'due_date' => $dueDate,
            'valid_date' => $validDate,
        ];
    }

    /**
     * Which stored procedure regenerates the challan for a scholarship.
     * p_sch_type stays the challan category in every case.
     */
    protected function resolveScholarshipProcedureName($challanCategory, $studentType, $enrollment)
    {
        if ($challanCategory === 'Full_semester') {
            return 'FIN_SCH_FULL_SEMESTER';
        }
        if ($challanCategory === 'Short/Summer_Course') {
            return $this->isSummerAdmissionEnrollment($studentType, $enrollment)
                ? 'FIN_SCH_FULL_SEMESTER'
                : 'FIN_SCH_CREDIT_HOUR';
        }
        // additional_Full_semester / Credit_hours
        return 'FIN_SCH_CREDIT_HOUR';
    }

    /**
     * Apply saved scholarship rows to an existing student_challan (no Oracle procedure).
     *
     * Reads students_scholarships_details for the category, recalculates
     * SC_SCHOLARSHIP_AMOUNT + INVOICEAMOUNT, and syncs discount/history rows.
     */
    protected function applyAcademicScholarshipToStudentChallan(
        int $aaiId,
        int $sessionId,
        string $challanCategory,
        string $studentType,
        ?string $enrollmentNo,
        string $entryUser
    ): array {
        $context = $this->resolveScholarshipChallanExecutionContext(
            $aaiId,
            $sessionId,
            $challanCategory,
            $studentType,
            $enrollmentNo
        );

        if (empty($context['ok'])) {
            return [
                'ok' => false,
                'reason' => isset($context['reason']) ? $context['reason'] : 'challan_error',
                'message' => isset($context['message']) ? $context['message'] : 'Challan could not be resolved.',
            ];
        }

        $contextChallan = $context['challan'];
        $challanId = (int) ($contextChallan->id ?? $contextChallan->ID ?? 0);
        if (!$challanId) {
            return [
                'ok' => false,
                'reason' => 'no_challan',
                'message' => 'Scholarship could not be applied because the challan id was missing.',
            ];
        }

        $challan = DB::table('student_challan')->where('id', $challanId)->first();
        if (empty($challan)) {
            return [
                'ok' => false,
                'reason' => 'no_challan',
                'message' => 'No existing challan was found for the selected category/session.',
            ];
        }

        $status = strtoupper(trim((string) ($challan->sc_challan_status ?? $challan->SC_CHALLAN_STATUS ?? '')));
        if (in_array($status, ['CANCELLED', 'CANCEL FOR REISSUANCE'], true)) {
            return [
                'ok' => false,
                'reason' => 'no_challan',
                'message' => 'The matched challan is cancelled — scholarship was not applied.',
            ];
        }

        $scholarshipRows = DB::table('students_scholarships_details')
            ->where('ssd_app_id', $aaiId)
            ->where('ssd_sess_id', $sessionId)
            ->whereRaw("NVL(status, 'E') = 'E'")
            ->whereRaw("NVL(ssd_challan_category, 'Full_semester') = ?", [$challanCategory])
            ->get();

        if ($scholarshipRows->isEmpty()) {
            return [
                'ok' => false,
                'reason' => 'no_scholarships',
                'message' => 'No active scholarship rows found for ' . $challanCategory . '.',
            ];
        }

        $scholarshipBase = $this->resolveChallanScholarshipBase($challan, $challanCategory);
        if ($scholarshipBase <= 0) {
            return [
                'ok' => false,
                'reason' => 'no_fee_base',
                'message' => 'Challan fee base is zero for ' . $challanCategory . '.',
            ];
        }

        $totalScholarship = 0.0;
        $totalArrear = 0.0;
        $discountRows = [];
        foreach ($scholarshipRows as $row) {
            $pct = (float) ($row->ssd_percentage ?? $row->SSD_PERCENTAGE ?? 0);
            $storedAmount = $row->ssd_amount ?? $row->SSD_AMOUNT ?? null;
            $amount = ($storedAmount !== null && (float) $storedAmount > 0)
                ? (float) $storedAmount
                : round($scholarshipBase * ($pct / 100), 2);
            $totalScholarship += $amount;
            $totalArrear += (float) ($row->ssd_arrear ?? $row->SSD_ARREAR ?? 0);
            $discountRows[] = [
                'type' => (int) ($row->ssd_type ?? $row->SSD_TYPE ?? 0),
                'percentage_fraction' => round($pct / 100, 10),
                'discount_amount' => $amount,
            ];
        }

        $totalScholarship = min(round($totalScholarship, 2), round($scholarshipBase, 2));
        $totalArrear = round($totalArrear, 2);
        $invoiceAmount = $this->calculateChallanInvoiceAfterScholarship($challan, $totalScholarship, $totalArrear);

        $update = [
            'SC_SCHOLARSHIP_AMOUNT' => $totalScholarship,
            'SC_SSD_ARREAR' => $totalArrear,
            'INVOICEAMOUNT' => $invoiceAmount,
            'UPDATED_AT' => now(),
        ];
        if ($this->studentChallanColumnExists('SC_PRO_ENTRY_USERNAME')) {
            $update['SC_PRO_ENTRY_USERNAME'] = $entryUser;
        }

        DB::table('student_challan')->where('id', $challanId)->update($update);

        $resolvedEnrollment = $enrollmentNo !== null && $enrollmentNo !== ''
            ? strtoupper($enrollmentNo)
            : strtoupper(trim((string) ($challan->sc_enrollment_no ?? $challan->SC_ENROLLMENT_NO ?? '')));
        $challanNo = (int) ($challan->challan_no ?? $challan->CHALLAN_NO ?? 0);

        $this->syncAcademicScholarshipDiscounts(
            $aaiId,
            $sessionId,
            $resolvedEnrollment !== '' ? $resolvedEnrollment : null,
            $challanCategory,
            $discountRows,
            $challanNo > 0 ? $challanNo : $challanId,
            $challan
        );
        $this->syncAcademicScholarshipDisplayHistory(
            $aaiId,
            $sessionId,
            $challanCategory,
            $discountRows,
            $challanId,
            $challanNo,
            $entryUser,
            $resolvedEnrollment !== '' ? $resolvedEnrollment : null
        );

        return ['ok' => true];
    }

    /**
     * Apply hostel scholarship rows to the existing hostel challan (no procedure).
     */
    protected function applyHostelScholarshipToStudentChallan(
        int $aaiId,
        int $sessionId,
        ?string $enrollmentNo,
        string $entryUser
    ): bool {
        $challan = DB::table('student_challan')
            ->where('SC_SESSION_ID', $sessionId)
            ->where('SC_APP_ID', $aaiId)
            ->where('SC_CHALLAN_TYPE', 'Hostel_Fee')
            ->whereIn('SC_HOSTEL_FEE_TYPE', ['Hostel_Full_semster', 'Hostel_day_wise'])
            ->whereRaw("UPPER(TRIM(NVL(SC_CHALLAN_STATUS, 'X'))) NOT IN ('CANCELLED', 'CANCEL FOR REISSUANCE')")
            ->orderByDesc('id')
            ->first();

        if (empty($challan)) {
            return false;
        }

        $hostelRows = DB::table('fin_students_scholarships_hostel_details')
            ->where('sshd_app_id', $aaiId)
            ->where('sshd_sess_id', $sessionId)
            ->whereRaw("NVL(TRIM(sshd_status), 'E') = 'E'")
            ->get();

        if ($hostelRows->isEmpty()) {
            return false;
        }

        $hostelBase = (float) ($challan->ffs_hostel_amt ?? $challan->FFS_HOSTEL_AMT ?? 0);
        $totalScholarship = 0.0;
        $totalArrear = 0.0;
        $discountRows = [];

        foreach ($hostelRows as $row) {
            $pct = (float) ($row->sshd_percentage ?? $row->SSHD_PERCENTAGE ?? 0);
            $storedAmount = $row->sshd_amount ?? $row->SSHD_AMOUNT ?? null;
            $amount = ($storedAmount !== null && (float) $storedAmount > 0)
                ? (float) $storedAmount
                : round($hostelBase * ($pct / 100), 2);
            $totalScholarship += $amount;
            $totalArrear += (float) ($row->sshd_arrear ?? $row->SSHD_ARREAR ?? 0);
            $discountRows[] = [
                'type' => (int) ($row->sshd_type ?? $row->SSHD_TYPE ?? 0),
                'percentage_fraction' => round($pct / 100, 10),
                'discount_amount' => $amount,
            ];
        }

        $totalScholarship = min(round($totalScholarship, 2), round(max(0, $hostelBase), 2));
        $totalArrear = round($totalArrear, 2);
        $security = (float) ($challan->ffs_security_amt ?? $challan->FFS_SECURITY_AMT ?? 0);
        $invoiceAmount = max(0, round(($hostelBase - $totalScholarship) + $security - $totalArrear, 2));

        $update = [
            'SC_SCHOLARSHIP_AMOUNT' => $totalScholarship,
            'SC_SSD_ARREAR' => $totalArrear,
            'INVOICEAMOUNT' => $invoiceAmount,
            'UPDATED_AT' => now(),
        ];
        if ($this->studentChallanColumnExists('SC_PRO_ENTRY_USERNAME')) {
            $update['SC_PRO_ENTRY_USERNAME'] = $entryUser;
        }

        $challanId = (int) ($challan->id ?? $challan->ID);
        DB::table('student_challan')->where('id', $challanId)->update($update);

        $resolvedEnrollment = $enrollmentNo !== null && $enrollmentNo !== ''
            ? strtoupper($enrollmentNo)
            : strtoupper(trim((string) ($challan->sc_enrollment_no ?? $challan->SC_ENROLLMENT_NO ?? '')));
        $challanNo = (int) ($challan->challan_no ?? $challan->CHALLAN_NO ?? 0);

        foreach ($discountRows as $row) {
            $this->upsertScholarshipDiscountRow(
                $aaiId,
                $sessionId,
                $resolvedEnrollment !== '' ? $resolvedEnrollment : null,
                (int) $row['type'],
                (float) $row['percentage_fraction'],
                (float) $row['discount_amount'],
                $challanNo > 0 ? $challanNo : $challanId,
                'hostel',
                false
            );
        }

        DB::table('fin_scholarship_display_history')
            ->where('fsd_aai_id', $aaiId)
            ->where('fsd_sesion_id', $sessionId)
            ->where('FSD_SCH_TYPE', 'hostel')
            ->where(function ($q) use ($challanId, $challanNo) {
                $q->where('challan_id', $challanId);
                if ($challanNo > 0) {
                    $q->orWhere('challan_id', $challanNo);
                }
            })
            ->whereNull('fsdh_version')
            ->delete();

        foreach ($discountRows as $row) {
            if ($row['type'] <= 0 || $row['discount_amount'] <= 0) {
                continue;
            }
            $this->insertScholarshipDisplayHistoryRow([
                'FSD_ENROLLMENT_NO' => $resolvedEnrollment !== '' ? $resolvedEnrollment : null,
                'FSD_SCHOLARSHIP_TYPE' => $row['type'],
                'FSD_DISCOUNT_PERCENTAGE' => $row['percentage_fraction'],
                'FSD_DISCOUNTED_AMOUNT' => $row['discount_amount'],
                'FSD_SESION_ID' => $sessionId,
                'FSD_AAI_ID' => $aaiId,
                'FSD_SCH_TYPE' => 'hostel',
                'CHALLAN_ID' => $challanId,
                'ENTRY_DATETIME' => now(),
                'FSDH_VERSION' => null,
                'FSDH_UPDATED_BY' => $entryUser,
                'FSDH_UPDATED_AT' => now(),
                'FLAG' => 'F',
            ]);
        }

        return true;
    }

    protected function resolveChallanScholarshipBase($challan, string $challanCategory): float
    {
        $category = trim($challanCategory);

        if ($category === 'Hostel_Fee') {
            return (float) ($challan->ffs_hostel_amt ?? $challan->FFS_HOSTEL_AMT ?? 0);
        }

        if (in_array($category, ['Credit_hours', 'additional_Full_semester'], true)) {
            $tuition = (float) ($challan->sc_tuition_fee_amnt ?? $challan->SC_TUITION_FEE_AMNT ?? 0);
            if ($tuition > 0) {
                return $tuition;
            }
            $perCredit = (float) ($challan->ffs_credhr_amt ?? $challan->FFS_CREDHR_AMT ?? 0);
            $credits = (float) ($challan->sc_enrolled_cr_hr ?? $challan->SC_ENROLLED_CR_HR ?? 0);

            return $perCredit * $credits;
        }

        $installmentTuition = $challan->sc_installment_tuition_fee_amnt ?? $challan->SC_INSTALLMENT_TUITION_FEE_AMNT ?? null;
        if ($installmentTuition !== null && (float) $installmentTuition > 0) {
            return (float) $installmentTuition;
        }

        return (float) ($challan->sc_tuition_fee_amnt ?? $challan->SC_TUITION_FEE_AMNT ?? 0);
    }

    protected function calculateChallanInvoiceAfterScholarship($challan, float $scholarshipAmount, float $arrearAmount = 0.0): float
    {
        $admission = (float) ($challan->ffs_admission_amt ?? $challan->FFS_ADMISSION_AMT ?? 0);
        $security = (float) ($challan->ffs_security_amt ?? $challan->FFS_SECURITY_AMT ?? 0);
        $medical = (float) ($challan->ffs_medical_amt ?? $challan->FFS_MEDICAL_AMT ?? 0);
        $activity = (float) ($challan->sc_activity_amt ?? $challan->SC_ACTIVITY_AMT ?? 0);
        $tuition = (float) (
            $challan->sc_installment_tuition_fee_amnt
            ?? $challan->SC_INSTALLMENT_TUITION_FEE_AMNT
            ?? $challan->sc_tuition_fee_amnt
            ?? $challan->SC_TUITION_FEE_AMNT
            ?? 0
        );
        $hostel = (float) ($challan->ffs_hostel_amt ?? $challan->FFS_HOSTEL_AMT ?? 0);
        $surplus = (float) ($challan->sc_surplus_amount ?? $challan->SC_SURPLUS_AMOUNT ?? 0);
        $surplusDeduction = $surplus > 0 ? $surplus : 0.0;

        $invoice = $admission + $security + $medical + $activity + $tuition + $hostel
            - $scholarshipAmount
            - $surplusDeduction
            - $arrearAmount;

        return max(0, round($invoice, 2));
    }

    protected function syncAcademicScholarshipDiscounts(
        int $aaiId,
        int $sessionId,
        ?string $enrollmentNo,
        string $challanCategory,
        array $discountRows,
        int $challanReference,
        $challan = null
    ): void {
        $isInstallment = false;
        if ($challan !== null) {
            $isInstallment = (int) (
                $challan->check_fee_installments
                ?? $challan->CHECK_FEE_INSTALLMENTS
                ?? 0
            ) > 0;
        }

        foreach ($discountRows as $row) {
            $this->upsertScholarshipDiscountRow(
                $aaiId,
                $sessionId,
                $enrollmentNo,
                (int) $row['type'],
                (float) $row['percentage_fraction'],
                (float) $row['discount_amount'],
                $challanReference,
                null,
                $isInstallment
            );
        }
    }

    /**
     * Delete + insert one fin_scholarship_discounts row (academic or hostel).
     */
    protected function upsertScholarshipDiscountRow(
        int $aaiId,
        int $sessionId,
        ?string $enrollmentNo,
        int $scholarshipType,
        float $percentageFraction,
        float $discountAmount,
        int $challanReference,
        ?string $schType = null,
        bool $isInstallment = false
    ): void {
        if ($scholarshipType <= 0) {
            return;
        }

        $discountAmount = round($discountAmount, 2);
        if ($discountAmount <= 0 && $percentageFraction <= 0) {
            return;
        }

        $delete = DB::table('fin_scholarship_discounts')
            ->where('fsd_aai_id', $aaiId)
            ->where('fsd_sesion_id', $sessionId)
            ->where('fsd_scholarship_type', $scholarshipType);

        if ($schType === 'hostel') {
            $delete->whereRaw("UPPER(NVL(FSD_SCH_TYPE, '')) = 'HOSTEL'");
        } else {
            $delete->whereRaw('FSD_SCH_TYPE IS NULL');
        }
        $delete->delete();

        $insert = [
            'fsd_enrollment_no' => $enrollmentNo,
            'fsd_scholarship_type' => $scholarshipType,
            'fsd_discount_percentage' => $percentageFraction,
            'fsd_sesion_id' => $sessionId,
            'fsd_aai_id' => $aaiId,
            'CHALLAN_NO' => $challanReference,
            'FSD_ENTRY_DATETIME' => now(),
            'VERSION' => 1,
        ];

        if ($schType === 'hostel') {
            $insert['FSD_SCH_TYPE'] = 'hostel';
        }

        if ($isInstallment) {
            $insert['fsd_sch_instal_amnt'] = $discountAmount;
        } else {
            $insert['fsd_discounted_amount'] = $discountAmount;
        }

        $this->insertScholarshipDiscountRow($insert);
    }

    protected function insertScholarshipDiscountRow(array $discountData): void
    {
        // ID is assigned by the database, never here — see AbstractChallanPoster::insert().
        DB::table('fin_scholarship_discounts')->insert($discountData);
    }

    protected function syncAcademicScholarshipDisplayHistory(
        int $aaiId,
        int $sessionId,
        string $challanCategory,
        array $discountRows,
        int $challanId,
        int $challanNo,
        string $entryUser,
        ?string $enrollmentNo = null
    ): void {
        DB::table('fin_scholarship_display_history')
            ->where('fsd_aai_id', $aaiId)
            ->where('fsd_sesion_id', $sessionId)
            ->whereNull('fsdh_version')
            ->where(function ($q) use ($challanCategory) {
                $q->where('ssd_challan_category', $challanCategory)
                    ->orWhereNull('ssd_challan_category');
            })
            ->where(function ($q) use ($challanId, $challanNo) {
                $q->where('challan_id', $challanId);
                if ($challanNo > 0) {
                    $q->orWhere('challan_id', $challanNo);
                }
            })
            ->delete();

        foreach ($discountRows as $row) {
            if ($row['type'] <= 0 || $row['discount_amount'] <= 0) {
                continue;
            }

            $this->insertScholarshipDisplayHistoryRow([
                'FSD_ENROLLMENT_NO' => $enrollmentNo,
                'FSD_AAI_ID' => $aaiId,
                'FSD_SESION_ID' => $sessionId,
                'FSD_SCHOLARSHIP_TYPE' => $row['type'],
                'FSD_DISCOUNT_PERCENTAGE' => $row['percentage_fraction'],
                'FSD_DISCOUNTED_AMOUNT' => $row['discount_amount'],
                'FSD_SCH_TYPE' => null,
                'SSD_CHALLAN_CATEGORY' => $challanCategory,
                'CHALLAN_ID' => $challanId,
                'FSDH_VERSION' => null,
                'ENTRY_DATETIME' => now(),
                'FSDH_UPDATED_BY' => $entryUser,
                'FSDH_UPDATED_AT' => now(),
                'FLAG' => 'F',
            ]);
        }
    }

    protected function insertScholarshipDisplayHistoryRow(array $historyData): void
    {
        // ID is assigned by the database, never here — see AbstractChallanPoster::insert().
        DB::table('fin_scholarship_display_history')->insert($historyData);
    }

    /** @var array<string, bool> */
    private $studentChallanColumnCache = [];

    protected function studentChallanColumnExists(string $column): bool
    {
        $key = strtoupper(trim($column));
        if (array_key_exists($key, $this->studentChallanColumnCache)) {
            return $this->studentChallanColumnCache[$key];
        }

        try {
            DB::selectOne("SELECT {$column} AS probe_col FROM student_challan WHERE 1 = 0");

            return $this->studentChallanColumnCache[$key] = true;
        } catch (\Throwable $e) {
            return $this->studentChallanColumnCache[$key] = false;
        }
    }

    /** @var array<string, bool> */
    private $tableColumnCache = [];

    /**
     * Does $table have $column? Probed once per table+column and cached.
     *
     * The snapshot columns are added by a hand-run DDL script, so the page must keep
     * working on an install where the ALTER has not been applied yet — the same
     * degradation strategy ScholarshipFormController uses for its bulk columns.
     *
     * Both identifiers are interpolated into SQL, so they are whitelisted first.
     */
    protected function tableColumnExists(string $table, string $column): bool
    {
        $key = strtoupper(trim($table)) . '.' . strtoupper(trim($column));
        if (array_key_exists($key, $this->tableColumnCache)) {
            return $this->tableColumnCache[$key];
        }

        if (!preg_match('/^[A-Za-z0-9_$#.]+$/', $table) || !preg_match('/^[A-Za-z0-9_$#]+$/', $column)) {
            return $this->tableColumnCache[$key] = false;
        }

        try {
            DB::selectOne("SELECT {$column} AS probe_col FROM {$table} WHERE 1 = 0");

            return $this->tableColumnCache[$key] = true;
        } catch (\Throwable $e) {
            return $this->tableColumnCache[$key] = false;
        }
    }

    /** Drop any key of $data whose column does not exist on $table. */
    protected function filterExistingColumns(string $table, array $data): array
    {
        $kept = [];
        foreach ($data as $column => $value) {
            if ($this->tableColumnExists($table, $column)) {
                $kept[$column] = $value;
            }
        }

        return $kept;
    }

    /**
     * Apply scholarship rows to student_challan (direct update — no Oracle procedure).
     *
     * Shared by Student Record (Revised) Save & Post and Scholarship Bulk Post.
     */
    protected function executeScholarshipChallanProcedures(
        $aaiId,
        $sessionId,
        $studentType,
        $enrollmentNo,
        array $academicCategories,
        $runHostel = false,
        array $options = []
    ) {
        $aaiId = (int) $aaiId;
        $sessionId = (int) $sessionId;
        $studentType = trim((string) $studentType);
        $enrollmentNo = $enrollmentNo !== null ? trim((string) $enrollmentNo) : null;
        $entryUser = isset($options['entry_user']) && $options['entry_user'] !== ''
            ? (string) $options['entry_user']
            : (auth()->user()->username ?? 'system');
        $fatalCreditCategories = array_map('strval', (array) ($options['fatal_credit_categories'] ?? []));

        $categories = [];
        foreach ($academicCategories as $key => $value) {
            if (is_int($key) && is_string($value)) {
                $categories[] = trim($value);
            } else {
                $categories[] = trim((string) $key);
            }
        }
        $categories = array_values(array_unique(array_filter($categories)));

        $executed = false;
        $hostelExecuted = false;
        $procedureStarted = false;
        $executedCategories = [];
        $messages = [];

        foreach ($categories as $challanCategory) {
            try {
                $procedureStarted = true;
                $result = $this->applyAcademicScholarshipToStudentChallan(
                    $aaiId,
                    $sessionId,
                    $challanCategory,
                    $studentType,
                    $enrollmentNo,
                    $entryUser
                );
            } catch (\Throwable $ex) {
                throw new \RuntimeException(
                    'Scholarship process failed while updating challan. Error: ' . $ex->getMessage(),
                    0,
                    $ex
                );
            }

            if (empty($result['ok'])) {
                $reason = isset($result['reason']) ? $result['reason'] : null;

                if ($reason === 'no_challan') {
                    $messages[] = isset($result['message']) ? $result['message'] : ('No challan for ' . $challanCategory);
                    continue;
                }

                if ($reason === 'credit_challan_required') {
                    if (in_array($challanCategory, $fatalCreditCategories, true)) {
                        throw new \RuntimeException(isset($result['message']) ? $result['message'] : 'Credit hour challan required.');
                    }
                    $messages[] = isset($result['message']) ? $result['message'] : 'Credit hour challan required.';
                    continue;
                }

                throw new \RuntimeException(isset($result['message']) ? $result['message'] : 'Challan update failed.');
            }

            $executed = true;
            $executedCategories[] = $challanCategory;
        }

        if ($runHostel) {
            $procedureStarted = true;
            $hostelEnrollment = $enrollmentNo !== null && $enrollmentNo !== ''
                ? $enrollmentNo
                : (string) $aaiId;
            try {
                $hostelExecuted = $this->applyHostelScholarshipToStudentChallan(
                    $aaiId,
                    $sessionId,
                    $hostelEnrollment,
                    $entryUser
                );
            } catch (\Throwable $ex) {
                throw new \RuntimeException(
                    'Hostel scholarship process failed while updating challan. Error: ' . $ex->getMessage(),
                    0,
                    $ex
                );
            }
        }

        return [
            'executed' => $executed,
            'hostel_executed' => $hostelExecuted,
            'procedure_started' => $procedureStarted,
            'executed_categories' => $executedCategories,
            'messages' => $messages,
        ];
    }

    /**
     * Nearest future installment pair: among the five (due, valid) pairs, keep
     * those whose valid date is today or later and pick the smallest due date.
     */
    protected function resolveFutureInstallmentPair($sessions, $schoolId, $deptId, $progId, $flag, $challanStudentType, $admissionTypeCandidates = null)
    {
        $installment = null;
        $lastError = null;

        try {
            $query = DB::table('fin_challan_installments')
                ->select(
                    'FCI_FIRST_DUEDATE as first_due_date',
                    'FCI_FIRST_VALIDDATE as first_valid_date',
                    'FCI_SECOND_DUEDATE as second_due_date',
                    'FCI_SECOND_VALIDDATE as second_valid_date',
                    'FCI_THIRD_DUEDATE as third_due_date',
                    'FCI_THIRD_VALIDDATE as third_valid_date',
                    'FCI_FOURTH_DUEDATE as fourth_due_date',
                    'FCI_FOURTH_VALIDDATE as fourth_valid_date',
                    'FCI_FIFTH_DUEDATE as fifth_due_date',
                    'FCI_FIFTH_VALIDDATE as fifth_valid_date'
                )
                ->where('FCI_SESSION_ID', $sessions)
                ->where('FCI_SCHOOL_ID', $schoolId)
                ->where('FCI_DEPT_ID', $deptId)
                ->where('FCI_PROG_ID', $progId)
                ->where('FCI_STATUS', 'E')
                ->where('FCI_FLAG', $flag);

            if ($flag === 'Admission') {
                $query->where('FCI_STUDENT_TYPE', $challanStudentType);
                if (!empty($admissionTypeCandidates)) {
                    $query->where(function ($q) use ($admissionTypeCandidates) {
                        foreach (array_values((array) $admissionTypeCandidates) as $index => $candidate) {
                            $candidateValue = strtolower(trim((string) $candidate));
                            if ($candidateValue === '') {
                                continue;
                            }
                            if ($index === 0) {
                                $q->whereRaw('LOWER(TRIM(FCI_ADMISSION_TYPE)) = ?', [$candidateValue]);
                            } else {
                                $q->orWhereRaw('LOWER(TRIM(FCI_ADMISSION_TYPE)) = ?', [$candidateValue]);
                            }
                        }
                    });
                }
            }

            $installment = $query->first();
        } catch (\Throwable $e) {
            $lastError = $e;
        }

        if (empty($installment)) {
            return [
                'ok' => false,
                'reason' => 'no_installment',
                'message' => 'Scholarship could not be saved because no active future installment due date/valid date was found for the selected student, session, program, and challan category.' . ($lastError ? ' Error: ' . $lastError->getMessage() : ''),
            ];
        }

        $pairs = [
            [isset($installment->first_due_date) ? $installment->first_due_date : null, isset($installment->first_valid_date) ? $installment->first_valid_date : null],
            [isset($installment->second_due_date) ? $installment->second_due_date : null, isset($installment->second_valid_date) ? $installment->second_valid_date : null],
            [isset($installment->third_due_date) ? $installment->third_due_date : null, isset($installment->third_valid_date) ? $installment->third_valid_date : null],
            [isset($installment->fourth_due_date) ? $installment->fourth_due_date : null, isset($installment->fourth_valid_date) ? $installment->fourth_valid_date : null],
            [isset($installment->fifth_due_date) ? $installment->fifth_due_date : null, isset($installment->fifth_valid_date) ? $installment->fifth_valid_date : null],
        ];

        $today = Carbon::now('Asia/Karachi')->startOfDay();
        $selected = null;

        foreach ($pairs as $pair) {
            $dueValue = $pair[0];
            $validValue = $pair[1];
            if (empty($dueValue) || empty($validValue)) {
                continue;
            }

            try {
                $dueDate = Carbon::parse($dueValue)->startOfDay();
                $validDate = Carbon::parse($validValue)->startOfDay();
            } catch (\Throwable $e) {
                continue;
            }

            if ($validDate->lt($today)) {
                continue;
            }

            if (empty($selected) || $dueDate->lt($selected['due_date'])) {
                $selected = ['due_date' => $dueDate, 'valid_date' => $validDate];
            }
        }

        if (empty($selected)) {
            return [
                'ok' => false,
                'reason' => 'no_future_installment',
                'message' => 'Scholarship could not be saved because no active future installment due date/valid date was found for the selected student, session, program, and challan category.',
            ];
        }

        return [
            'ok' => true,
            'due_date' => $selected['due_date']->toDateString(),
            'valid_date' => $selected['valid_date']->toDateString(),
        ];
    }

    /**
     * Would the challan PREVIEW find installment dates for this programme, or fall
     * back to today + 7?
     *
     * ── THIS IS A THIRD COPY ─────────────────────────────────────────────────────
     * Deliberately mirrors the date block in the two PHP renderers:
     *   \\172.20.250.243\php\staff\chalanfinPreview.php     (~lines 1570-1622)
     *   \\172.20.250.243\php\staff\chalanfinBulkPreview.php (~lines 1613-1665)
     * which are byte-identical to each other. The point of this method is to predict
     * what those files will print, so it must stay bug-for-bug faithful to them.
     * ─────────────────────────────────────────────────────────────────────────────
     *
     * It is NOT resolveFutureInstallmentPair() above, and must not be merged with it.
     * Both now narrow by FCI_FLAG, but that one additionally filters FCI_STUDENT_TYPE
     * and FCI_ADMISSION_TYPE on the Admission branch, which the renderers do not. Adding
     * those here would report "no dates defined" for a programme whose rows the preview
     * will happily use, contradicting the warning on the very next screen.
     *
     * $fciFlag must be derived the way the renderers derive it — 'CMS' when the student
     * resolved out of students_apply_info by enrollment, 'Admission' when they only
     * resolved out of admission_apply_info by app id. Do not hard-code it: an applicant
     * has no CMS row, so pinning it to 'CMS' strands every admission challan on today+7.
     *
     * The pair rule, matching the renderers as corrected on 21 Aug 2026:
     *   - EVERY active row for the programme contributes its five pairs, not just the
     *     first. The renderers used to read $inst[0] of an unordered result, so with two
     *     rows the answer was whichever one Oracle happened to return first.
     *   - BOTH the due date and the valid date must still be ahead of today. Screening
     *     on the valid date alone let a challan be issued with a due date already past,
     *     which fines the student the moment they receive it.
     *
     * Do not add FCI_STUDENT_TYPE / FCI_ADMISSION_TYPE here unless BOTH renderers change
     * too. FCI_FLAG is already in step with them, as of 21 Aug 2026.
     *
     * $studentType and $admissionTypeCandidates are the ONE exception to that rule, and
     * they are opt-in: omit them and this behaves exactly as it always has, which is what
     * every renderer-backed caller does. They exist for the Admission-office branch of the
     * Student Record Revised page, where narrowing is the whole requirement — that path
     * does not predict a renderer, it decides the dates outright, so there is no second
     * side to contradict. See StudentRecordRevisedController::admissionInstallmentLookup().
     *
     * Both are matched case-insensitively on a TRIMmed value. FCI_STUDENT_TYPE is stored
     * as 'New Admission FALL' while the mapper that produces it returns
     * 'NEW ADMISSION FALL', and FCI_ADMISSION_TYPE holds either the round number or its
     * word — an exact comparison silently matches nothing and strands the challan on
     * today + 7, which is precisely the bug this parameter was added to fix.
     *
     * 'ok' is THREE-valued: true = dates found, false = it will fall back to today + 7,
     * null = the lookup failed and we do not know. Callers must not warn on null.
     */
    protected function previewInstallmentAvailability(
        $sessionId,
        $schoolId,
        $deptId,
        $progId,
        $fciFlag = 'CMS',
        $studentType = null,
        array $admissionTypeCandidates = []
    ): array {
        // The renderers guard with `if ($schoolId && $deptId && $progId)` and never
        // query when any is falsy, so today + 7 stands. Reported separately because
        // the remedy is a missing student record, not a missing fee installment.
        if (empty($sessionId) || empty($schoolId) || empty($deptId) || empty($progId)) {
            return ['ok' => false, 'reason' => 'no_program'];
        }

        try {
            // EVERY active row, not the first. A programme routinely has more than one
            // (per flag, per admission round), and reading only one silently discards
            // the other's dates — which is how a challan ends up on today + 7 while a
            // perfectly good pair sits in the row nobody looked at.
            // TO_CHAR for the same reason the renderers do it: these are read back with
            // Carbon::parse(), which falls through to strtotime(), which reads a
            // slash-separated date as AMERICAN m/d/Y. Under an NLS_DATE_FORMAT of
            // DD/MM/YYYY every pair would fail to parse and the answer would silently
            // become "no usable dates" — a warning about nothing.
            $query = DB::table('fin_challan_installments')
                ->select(
                    DB::raw("TO_CHAR(FCI_FIRST_DUEDATE,    'YYYY-MM-DD') as first_due_date"),
                    DB::raw("TO_CHAR(FCI_FIRST_VALIDDATE,  'YYYY-MM-DD') as first_valid_date"),
                    DB::raw("TO_CHAR(FCI_SECOND_DUEDATE,   'YYYY-MM-DD') as second_due_date"),
                    DB::raw("TO_CHAR(FCI_SECOND_VALIDDATE, 'YYYY-MM-DD') as second_valid_date"),
                    DB::raw("TO_CHAR(FCI_THIRD_DUEDATE,    'YYYY-MM-DD') as third_due_date"),
                    DB::raw("TO_CHAR(FCI_THIRD_VALIDDATE,  'YYYY-MM-DD') as third_valid_date"),
                    DB::raw("TO_CHAR(FCI_FOURTH_DUEDATE,   'YYYY-MM-DD') as fourth_due_date"),
                    DB::raw("TO_CHAR(FCI_FOURTH_VALIDDATE, 'YYYY-MM-DD') as fourth_valid_date"),
                    DB::raw("TO_CHAR(FCI_FIFTH_DUEDATE,    'YYYY-MM-DD') as fifth_due_date"),
                    DB::raw("TO_CHAR(FCI_FIFTH_VALIDDATE,  'YYYY-MM-DD') as fifth_valid_date")
                )
                ->where('FCI_SESSION_ID', $sessionId)
                ->where('FCI_SCHOOL_ID', $schoolId)
                ->where('FCI_DEPT_ID', $deptId)
                ->where('FCI_PROG_ID', $progId)
                ->where('FCI_STATUS', 'E')
                // Mirrors the renderers, which derive the same flag from which identity
                // branch resolved the student. Both sides must carry this or the warning
                // and the document go back to describing different rows.
                ->where('FCI_FLAG', $fciFlag);

            // Opt-in only — see the docblock. Every renderer-backed caller omits both and
            // gets the unnarrowed query it has always had.
            $studentType = trim((string) $studentType);
            if ($studentType !== '') {
                $query->whereRaw('UPPER(TRIM(FCI_STUDENT_TYPE)) = ?', [strtoupper($studentType)]);
            }

            $candidates = array_values(array_filter(array_map(
                function ($c) { return strtolower(trim((string) $c)); },
                $admissionTypeCandidates
            ), function ($c) { return $c !== ''; }));

            if (!empty($candidates)) {
                $query->where(function ($q) use ($candidates) {
                    foreach ($candidates as $index => $candidate) {
                        if ($index === 0) {
                            $q->whereRaw('LOWER(TRIM(FCI_ADMISSION_TYPE)) = ?', [$candidate]);
                        } else {
                            $q->orWhereRaw('LOWER(TRIM(FCI_ADMISSION_TYPE)) = ?', [$candidate]);
                        }
                    }
                });
            }

            $rows = $query->get();
        } catch (\Throwable $e) {
            // ok => null, NOT false. "We could not look" and "there is nothing there"
            // are different answers, and only the second one is worth interrupting the
            // operator over. Callers treat null as do-not-block. Cached like any other
            // result so a broken query is asked once, not once per row.
            return ['ok' => null, 'reason' => 'probe_failed'];
        }

        if (count($rows) === 0) {
            return ['ok' => false, 'reason' => 'no_installment'];
        }

        $pairs = [];
        foreach ($rows as $row) {
            foreach (['first', 'second', 'third', 'fourth', 'fifth'] as $slot) {
                $due = $slot . '_due_date';
                $valid = $slot . '_valid_date';
                $pairs[] = [
                    isset($row->{$due}) ? $row->{$due} : null,
                    isset($row->{$valid}) ? $row->{$valid} : null,
                ];
            }
        }

        $today = Carbon::now('Asia/Karachi')->startOfDay();
        $selected = null;

        foreach ($pairs as $pair) {
            if (empty($pair[0]) || empty($pair[1])) {
                continue;
            }

            try {
                $dueDate = Carbon::parse($pair[0])->startOfDay();
                $validDate = Carbon::parse($pair[1])->startOfDay();
            } catch (\Throwable $e) {
                continue;
            }

            // BOTH dates must still be ahead. A past due date is not a usable deadline:
            // the challan carries "Fine @ Rs.100/- per day after the due date", so
            // issuing one already expired fines the student on the day they receive it.
            // Today itself counts as ahead — a challan due today is payable today.
            if ($dueDate->lt($today) || $validDate->lt($today)) {
                continue;
            }

            if (empty($selected) || $dueDate->lt($selected['due_date'])) {
                $selected = ['due_date' => $dueDate, 'valid_date' => $validDate];
            }
        }

        if (empty($selected)) {
            // The row exists, so somebody did define dates — they have simply all
            // expired. Worth its own reason: the operator is looking for a row that
            // is there, not one that is missing.
            return ['ok' => false, 'reason' => 'no_future_installment'];
        }

        return [
            'ok' => true,
            'due_date' => $selected['due_date']->toDateString(),
            'valid_date' => $selected['valid_date']->toDateString(),
        ];
    }

    /**
     * The date a preview will carry when no installment pair is found. Kept in one
     * place so the modal's wording and the fallback can never drift apart.
     */
    protected function previewFallbackDate(): string
    {
        return Carbon::now('Asia/Karachi')->addDays(7)->toDateString();
    }

    /**
     * The programme a challan is priced against, and which installment flag applies.
     *
     * Resolution order is the renderers' order — students_apply_info by enrollment
     * first, admission_apply_info by app id on a miss — and the branch that wins also
     * decides the flag: an enrolled student is billed off the CMS rows, an applicant
     * off the Admission ones.
     *
     * Not derived from student_type. The renderers have no student_type to consult, and
     * this has to answer the same way they do.
     */
    protected function previewProgrammeAndFlag($enrollment, $aaiId): array
    {
        // COMPLETE, not merely present. A fresh admission has a students_apply_info row
        // carrying the enrollment number but no school / department / programme — those
        // stay on admission_apply_info until enrolment completes. Accepting that row
        // anyway left the challan with no programme, so the installment lookup was
        // skipped entirely and the document silently dated itself today + 7.
        $complete = function ($row) {
            return !empty($row)
                && !empty($row->school_id)
                && !empty($row->dept_id)
                && !empty($row->prog_id);
        };

        $info = null;
        $flag = 'Admission';

        if (!empty($enrollment)) {
            $sai = DB::table('students_apply_info')
                ->select('SAI_SDPO_SCHOOL_ID as school_id', 'SAI_SDPO_DEPT_ID as dept_id', 'SAI_SDPO_PROG_ID as prog_id')
                ->whereRaw('LOWER(SAI_ENROLLMENT_NO) = ?', [strtolower((string) $enrollment)])
                ->first();
            if ($complete($sai)) {
                $info = $sai;
                $flag = 'CMS';
            }
        }

        if (empty($info) && !empty($aaiId)) {
            $aai = DB::table('admission_apply_info')
                ->select('AAI_SDPO_SCHOOL_ID as school_id', 'AAI_SDPO_DEPT_ID as dept_id', 'AAI_SDPO_PROG_ID as prog_id')
                ->where('AAI_ID', $aaiId)
                ->first();
            if ($complete($aai)) {
                $info = $aai;
            }
        }

        return [
            'school_id' => $info ? $info->school_id : null,
            'dept_id'   => $info ? $info->dept_id : null,
            'prog_id'   => $info ? $info->prog_id : null,
            'flag'      => $flag,
        ];
    }

    /**
     * Due / valid dates for a challan, resolved EXACTLY as the preview resolves them.
     *
     * This is the single source of truth for both pages' Post. The preview renderers
     * decide a challan's dates from fin_challan_installments alone and fall back to
     * today + 7; posting used to run a different rule — inheriting the dates of the
     * challan being replaced, and querying installments with different filters — so a
     * document could be approved showing one deadline and written with another.
     *
     * Deliberate consequence: a cancelled-and-reissued challan no longer keeps its
     * original deadlines. It gets the current nearest installment, which is what the
     * operator was shown and signed off on.
     */
    protected function challanDatesLikePreview($sessionId, $enrollment, $aaiId): array
    {
        $ids = $this->previewProgrammeAndFlag($enrollment, $aaiId);

        $res = $this->previewInstallmentAvailability(
            $sessionId, $ids['school_id'], $ids['dept_id'], $ids['prog_id'], $ids['flag']
        );

        if (!empty($res['ok'])) {
            return ['due' => $res['due_date'], 'valid' => $res['valid_date']];
        }

        $fallback = $this->previewFallbackDate();

        return ['due' => $fallback, 'valid' => $fallback];
    }

    /**
     * Hostel challan work for a saved scholarship: cancel the existing hostel
     * challan and regenerate it with the scholarship deduction.
     */
    protected function runHostelChallanProcedures($aai_id, $enrollment_aaiId, $academicSession): bool
    {
        $hostelExisitngChallan = DB::table('STUDENT_CHALLAN')
            ->select('*')
            ->where('SC_SESSION_ID', $academicSession)
            ->where('SC_APP_ID', $aai_id)
            ->where('SC_CHALLAN_TYPE', 'Hostel_Fee')
            ->whereIn('SC_HOSTEL_FEE_TYPE', ['Hostel_Full_semster', 'Hostel_day_wise'])
            ->first();

        if (!empty($hostelExisitngChallan)) {   // cancel the existing challan, then regenerate
            DB::executeProcedure('fin_bulk_challan_cancellation', [
                'P_SC_CHALLAN_ID'  => $hostelExisitngChallan->challan_no,
                'P_ENROLLMENT_NO'  => ctype_digit((string) $hostelExisitngChallan->sc_enrollment_no) ? null : $hostelExisitngChallan->sc_enrollment_no,
                'P_SC_APP_D'       => $hostelExisitngChallan->sc_app_id,
                'P_CANCELLED_USER' => auth()->user()->username,
                'P_REMARKS'        => 'For scholarship revision / entry',
            ]);

            DB::executeProcedure('fin_new_std_hostel_chln_gen', [
                'p_schol_id'             => $hostelExisitngChallan->sc_school_id,
                'p_dept_id'              => $hostelExisitngChallan->sc_department_id,
                'p_prog_id'              => $hostelExisitngChallan->sc_program_id,
                'p_session'              => $hostelExisitngChallan->sc_session_id,
                'p_due_date'             => $hostelExisitngChallan->sc_due_date,
                'p_valid_date'           => $hostelExisitngChallan->sc_valid_date,
                'p_ffs_student_type'     => $hostelExisitngChallan->sc_hostel_fee_type,
                'p_hostel_security'      => $hostelExisitngChallan->ffs_security_amt,
                'p_hostel_studentType'   => $hostelExisitngChallan->ffs_security_amt > 0 ? 'New_Admission' : 'Existing',
                'p_no_of_days'           => $hostelExisitngChallan->sc_hostel_days,
                'refrigeratorCheckBox'   => 'NO',
                'conditionerCheckBox'    => 'NO',
                'electricHeaterCheckBox' => 'NO',
                'hostelRemarks'          => 'For scholarship revision / entry',
                'p_AAI_ID'               => strtoupper((string) $enrollment_aaiId),
                'session_type'           => $hostelExisitngChallan->sc_session_type,
                'p_entry_username'       => auth()->user()->username,
                'p_studentType'          => $hostelExisitngChallan->sc_student_status,
            ]);

            return true;
        }

        return false;
    }

    // ------------------------------------------------------------------ //
    // Persistence
    // ------------------------------------------------------------------ //

    /**
     * Insert / update a single scholarship row (no procedures).
     *
     * Academic uniqueness is per (student, session, scholarship type, CHALLAN
     * CATEGORY): the same scholarship may be awarded under two different challan
     * categories, which discount two different challans and are capped separately.
     * Legacy rows have a NULL SSD_CHALLAN_CATEGORY -> treated as Full_semester.
     *
     * A locked existing academic row is skipped silently (same as the old page).
     */
    protected function persistScholarshipRow($tile, $aai_id, $enrollment, $sessions, $snapshot = null)
    {
        $type            = isset($tile['sch_type']) ? $tile['sch_type'] : null;
        $percentage      = isset($tile['sch_percentage']) ? $tile['sch_percentage'] : 0;
        $arrear          = isset($tile['sch_arrear']) ? $tile['sch_arrear'] : 0;
        $details         = isset($tile['sch_details']) ? $tile['sch_details'] : null;
        $rowId           = isset($tile['id']) ? $tile['id'] : null;
        $challanCategory = isset($tile['challan_category']) ? $tile['challan_category'] : $this->defaultChallanCategory();
        $username        = auth()->user()->username;
        $snapshot        = is_array($snapshot) ? $snapshot : [];

        if (strtolower(isset($tile['category']) ? $tile['category'] : '') === 'hostel') {
            $amount    = isset($tile['sch_amount']) ? $tile['sch_amount'] : 0;
            $hostelfee = isset($tile['hostelfee']) ? $tile['hostelfee'] : 0;

            // Snapshot columns are dropped silently when the DDL has not been run.
            $extra = $this->filterExistingColumns('fin_students_scholarships_hostel_details', $snapshot);

            $existing = !empty($rowId)
                ? DB::table('fin_students_scholarships_hostel_details')->where('sshd_id', $rowId)->first()
                : DB::table('fin_students_scholarships_hostel_details')
                    ->where('SSHD_APP_ID', $aai_id)
                    ->where('SSHD_SESS_ID', $sessions)
                    ->where('sshd_type', $type)
                    ->first();

            if (!empty($existing)) {
                DB::table('fin_students_scholarships_hostel_details')
                    ->where('sshd_id', $existing->sshd_id)
                    ->update(array_merge([
                        'sshd_type'            => $type,
                        'sshd_percentage'      => $percentage,
                        'SSHD_AMOUNT'          => $amount,
                        'SSHD_HOSTEL_FEE'      => $hostelfee,
                        'SSHD_ARREAR'          => $arrear,
                        'sshd_details'         => $details,
                        'SSHD_STATUS'          => 'E',
                        'SSHD_entry_username'  => $username,
                        'SSHD_UPDATE_DATETIME' => Carbon::now('Asia/Karachi'),
                    ], $extra));

                return $existing->sshd_id;
            }

            DB::table('fin_students_scholarships_hostel_details')->insert(array_merge([
                'sshd_sai_enrollment_no' => $enrollment ? strtoupper($enrollment) : null,
                'sshd_type'              => $type,
                'sshd_percentage'        => $percentage,
                'SSHD_AMOUNT'            => $amount,
                'SSHD_HOSTEL_FEE'        => $hostelfee,
                'SSHD_ARREAR'            => $arrear,
                'SSHD_APP_ID'            => $aai_id,
                'SSHD_STATUS'            => 'E',
                'SSHD_DETAILS'           => $details,
                'SSHD_SESS_ID'           => $sessions,
                'SSHD_entry_username'    => $username,
                'SSHD_ENTRY_DATETIME'    => Carbon::now('Asia/Karachi'),
            ], $extra));

            return null;
        }

        $extra = $this->filterExistingColumns('Students_Scholarships_Details', $snapshot);

        $existing = !empty($rowId)
            ? DB::table('Students_Scholarships_Details')->where('SSD_ID', $rowId)->first()
            : DB::table('Students_Scholarships_Details')
                ->where('SSD_APP_ID', $aai_id)
                ->where('ssd_type', $type)
                ->where('ssd_sess_id', $sessions)
                ->where('STATUS', 'E')
                ->whereRaw("NVL(SSD_CHALLAN_CATEGORY, 'Full_semester') = ?", [$challanCategory])
                ->first();

        if (!empty($existing)) {
            // A re-post must not overwrite a row whose challan already carries a
            // scholarship amount (Additional Credit Hr / Credit Hour).
            if ($this->isScholarshipRowLockedByChallan(
                    isset($existing->ssd_challan_category) ? $existing->ssd_challan_category : null,
                    isset($existing->ssd_app_id) ? $existing->ssd_app_id : $aai_id,
                    isset($existing->ssd_sai_enrollment_no) ? $existing->ssd_sai_enrollment_no : $enrollment,
                    isset($existing->ssd_sess_id) ? $existing->ssd_sess_id : $sessions)) {
                return null;
            }

            DB::table('Students_Scholarships_Details')
                ->where('SSD_ID', $existing->ssd_id)
                ->update(array_merge([
                    'ssd_sai_enrollment_no' => $enrollment,
                    'ssd_app_id'            => $aai_id,
                    'ssd_type'              => $type,
                    'ssd_percentage'        => $percentage,
                    'SSD_ARREAR'            => $arrear,
                    'ssd_details'           => $details,
                    'ssd_sess_id'           => $sessions,
                    'ssd_project'           => 'SCH_PRO_NEW',
                    'SSD_CHALLAN_CATEGORY'  => $challanCategory,
                    'scd_entry_username'    => $username,
                    'UPDATE_DATETIME'       => now()->toDateTimeString(),
                ], $extra));

            return $existing->ssd_id;
        }

        DB::table('Students_Scholarships_Details')->insert(array_merge([
            'ssd_sai_enrollment_no' => $enrollment,
            'ssd_app_id'            => $aai_id,
            'ssd_type'              => $type,
            'ssd_percentage'        => $percentage,
            'SSD_ARREAR'            => $arrear,
            'ssd_details'           => $details,
            'ssd_sess_id'           => $sessions,
            'ssd_project'           => 'SCH_PRO_NEW',
            'SSD_CHALLAN_CATEGORY'  => $challanCategory,
            'scd_entry_username'    => $username,
        ], $extra));

        return null;
    }

    /**
     * Remove a scholarship by ZEROING it, never by deleting the row.
     *
     * A scholarship row is the record of what a student was awarded. Deleting it takes
     * that history with it, leaves any challan it already discounted standing with the
     * discount still on it and nothing to explain why, and destroys the SOURCE_PAGE stamp
     * that decides which page is allowed to edit that student.
     *
     * So "remove" and "edit down to 0%" are the same operation: percentage and amount go
     * to zero, the row stays active, and it is re-queued as PENDING so the posting step
     * regenerates the challan without the discount.
     *
     * Mirrors ScholarshipFormController::saveStudentScholarships()'s remove branch, which
     * has always worked this way — this is the two pages converging on one behaviour.
     *
     * @param  string $sourcePage the caller's page tag, so ownership follows the edit
     * @return int number of rows affected (0 when the id no longer exists)
     */
    protected function zeroScholarshipRow($recordId, $category, $sourcePage)
    {
        $recordId = (int) $recordId;
        if ($recordId <= 0) {
            return 0;
        }

        $username = auth()->user()->username ?? 'system';

        if (strtolower(trim((string) $category)) === 'hostel') {
            return DB::table('fin_students_scholarships_hostel_details')
                ->where('SSHD_ID', $recordId)
                ->update($this->filterExistingColumns('fin_students_scholarships_hostel_details', [
                    'SSHD_PERCENTAGE'       => 0,
                    'SSHD_AMOUNT'           => 0,
                    'SSHD_ARREAR'           => 0,
                    'SSHD_BULK_POST_STATUS' => 'PENDING',
                    'SSHD_SOURCE_PAGE'      => $sourcePage,
                    'SSHD_ENTRY_USERNAME'   => $username,
                    'SSHD_UPDATE_DATETIME'  => Carbon::now('Asia/Karachi'),
                ]));
        }

        return DB::table('Students_Scholarships_Details')
            ->where('SSD_ID', $recordId)
            ->update($this->filterExistingColumns('Students_Scholarships_Details', [
                'SSD_PERCENTAGE'       => 0,
                'SSD_AMOUNT'           => 0,
                'SSD_ARREAR'           => 0,
                'SSD_BULK_POST_STATUS' => 'PENDING',
                'SSD_SOURCE_PAGE'      => $sourcePage,
                'scd_entry_username'   => $username,
                'UPDATE_DATETIME'      => now()->toDateTimeString(),
            ]));
    }
}
