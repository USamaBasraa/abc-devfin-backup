<?php

namespace App\Http\Controllers\StudentFinancial\Concerns;

use App\Services\StudentRecord\ChallanPosting\Support\SurplusAdjuster;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Freezes the fee picture behind a bulk scholarship save.
 *
 * ── THIS IS A SECOND COPY ────────────────────────────────────────────────────
 * The arithmetic here mirrors StudentRecordRevisedController::runSave() (step 8,
 * around lines 1000-1271). The two were deliberately kept separate rather than
 * extracted, so a change to fee/surplus rules MUST be applied in BOTH places or
 * the single and bulk pages will silently disagree again.
 * ─────────────────────────────────────────────────────────────────────────────
 *
 * Why a refresh pass rather than building the snapshot inline with each write:
 * the per-challan figures (tuition discount, payable, surplus) describe the WHOLE
 * category, not one row, so they can only be computed once every row's percentage
 * is known. updateRow() saves a single row and saveStudentScholarships() saves a
 * partial set, so neither payload is the complete picture — re-reading the student
 * from the database afterwards is the only way both paths get a correct snapshot.
 *
 * One-time-fee waivers (arrears) are mirrored here too. They used to be hard-coded to
 * zero on the grounds that the bulk page had no arrears grid — the Bulk Update form's
 * drawer has had one for a while, so that assumption was silently charging students the
 * full admission/security/activity/medical fee on every bulk-posted challan.
 */
trait BuildsScholarshipSnapshots
{
    /** Value written to SSD_SOURCE_PAGE / SSHD_SOURCE_PAGE. */
    abstract protected function sourcePage(): string;

    /**
     * Recompute and store the frozen snapshot for one student in one session.
     *
     * Call INSIDE the caller's transaction, after the percentages have been written.
     * Returns a report the caller can surface: which categories resolved, which did
     * not, and the surplus that was frozen.
     *
     * @return array{ok:bool, categories:array<string,bool>, warnings:array<int,string>, surplus_pending:float}
     */
    protected function refreshScholarshipSnapshot(
        int $sessionId,
        ?int $aaiId,
        string $enrollment,
        string $studentType = 'Existing'
    ): array {
        $enrollment = strtoupper(trim($enrollment));
        $warnings = [];
        $resolved = [];

        if ($sessionId <= 0 || ($enrollment === '' && !$aaiId)) {
            return ['ok' => false, 'categories' => [], 'warnings' => ['Student or session missing.'], 'surplus_pending' => 0.0];
        }

        $academicRows = $this->snapshotAcademicRows($sessionId, $aaiId, $enrollment);
        $hostelRows = $this->snapshotHostelRows($sessionId, $aaiId, $enrollment);

        if (empty($academicRows) && empty($hostelRows)) {
            return ['ok' => true, 'categories' => [], 'warnings' => [], 'surplus_pending' => 0.0];
        }

        $savedAt = Carbon::now('Asia/Karachi');
        $savedBy = auth()->user()->username ?? 'system';
        $sdpo = $this->snapshotStudentSdpo($studentType, $enrollment !== '' ? $enrollment : (string) $aaiId);
        $feeRef = $enrollment !== '' ? $enrollment : (string) $aaiId;

        // ── group academic rows by challan category ──
        $byCategory = [];
        foreach ($academicRows as $row) {
            $cat = trim((string) ($row->ssd_challan_category ?? $row->SSD_CHALLAN_CATEGORY ?? '')) ?: 'Full_semester';
            $byCategory[$cat][] = $row;
        }

        // ── resolve each category's fee basis once ──
        $feeFor = [];
        $effectiveType = [];
        foreach (array_keys($byCategory) as $cat) {
            $res = $this->resolveSnapshotFee($sessionId, $feeRef, $cat);
            $feeFor[$cat] = $res['fee'];
            $effectiveType[$cat] = $res['student_type'];
            $resolved[$cat] = $res['fee'] !== null;
            if ($res['fee'] === null) {
                $label = $this->academicCategoryLabels()[$cat] ?? $cat;
                $warnings[] = $enrollment . ': ' . $label . ' — '
                    . trim((string) $res['message'])
                    . ' (saved, but not priced).';
            }
        }

        // ── per-category tuition discount: every scholarship row in that category ──
        $categoryTuitionDisc = [];
        foreach ($byCategory as $cat => $rows) {
            $fee = $feeFor[$cat];
            $base = $fee ? (float) $fee['amountBase'] : 0.0;
            $sum = 0.0;
            foreach ($rows as $row) {
                $pct = (float) ($row->ssd_percentage ?? $row->SSD_PERCENTAGE ?? 0);
                $sum += round(($pct / 100) * $base, 2);
            }
            $categoryTuitionDisc[$cat] = $sum;
        }

        // ── per-category one-time-fee waivers (arrears) ──
        //
        // FIN_OTHER_SCHOLARSHIPS holds ABSOLUTE rupee amounts per (scholarship type,
        // challan category) — there is no percentage to re-derive. A challan line is
        // discounted once IN TOTAL, so the types are summed within a category, the same
        // rule StudentRecordRevisedController::runSave() follows.
        //
        // Loaded here and NOT inside the write loop below: getArrearsRowsForStudent()
        // is not category-scoped, so calling it per category would re-run an identical
        // query once per challan.
        //
        // The Bulk Update form's drawer is where these are entered —
        // saveStudentScholarships() validates them against the fee structure and calls
        // persistArrearsComponents() BEFORE this refresh runs, so the rows read here are
        // already the ones just saved.
        $categoryComponentDisc = [];
        foreach ($this->getArrearsRowsForStudent($aaiId, $sessionId, $enrollment !== '' ? $enrollment : null) as $arrearRow) {
            $arrearCat = trim((string) ($arrearRow->fos_challan_category ?? $arrearRow->FOS_CHALLAN_CATEGORY ?? ''))
                ?: $this->defaultChallanCategory();

            // additional_Full_semester is excluded by challanCategoryHasArrears(): it
            // bills only the extra credit hours on a full-semester challan whose
            // one-time fees the Full_semester arrears already waive. A category with no
            // SSD row has no snapshot to write the figures onto.
            if (!$this->challanCategoryHasArrears($arrearCat) || !isset($byCategory[$arrearCat])) {
                continue;
            }
            if (!isset($categoryComponentDisc[$arrearCat])) {
                $categoryComponentDisc[$arrearCat] = ['admission' => 0.0, 'security' => 0.0, 'medical' => 0.0, 'activity' => 0.0];
            }

            // fos_stipend is deliberately NOT summed: it is a payment TO the student,
            // not the waiver of a challan line, and no fee component caps it.
            $categoryComponentDisc[$arrearCat]['admission'] += (float) ($arrearRow->fos_admission_fee ?? $arrearRow->FOS_ADMISSION_FEE ?? 0);
            $categoryComponentDisc[$arrearCat]['security'] += (float) ($arrearRow->fos_security_fee ?? $arrearRow->FOS_SECURITY_FEE ?? 0);
            $categoryComponentDisc[$arrearCat]['medical'] += (float) ($arrearRow->fos_medical_insurance ?? $arrearRow->FOS_MEDICAL_INSURANCE ?? 0);
            $categoryComponentDisc[$arrearCat]['activity'] += (float) ($arrearRow->fos_activity_charges ?? $arrearRow->FOS_ACTIVITY_CHARGES ?? 0);
        }

        // ── surplus, frozen here so the preview shows the post-Post challan ──
        //
        // Walk order must match the order Post writes in — academicCategoryLabels()
        // order, hostel last — or a two-challan post would spend the surplus
        // differently from the preview that was accepted.
        $payableByCategory = [];
        foreach (array_keys($this->academicCategoryLabels()) as $cat) {
            if (!isset($byCategory[$cat])) {
                continue;
            }
            $payableByCategory[$cat] = $this->snapshotCategoryPayable($feeFor[$cat], $categoryTuitionDisc, $categoryComponentDisc, $cat);
        }

        $hostelFee = 0.0;
        $hostelDisc = 0.0;
        foreach ($hostelRows as $row) {
            $hostelFee = max($hostelFee, (float) ($row->sshd_hostel_fee ?? $row->SSHD_HOSTEL_FEE ?? 0));
            $hostelDisc += (float) ($row->sshd_amount ?? $row->SSHD_AMOUNT ?? 0);
        }
        if (!empty($hostelRows)) {
            $payableByCategory['Hostel_Fee'] = round(max(0.0, $hostelFee - min($hostelDisc, $hostelFee)), 2);
        }

        $surplusPending = 0.0;
        $surplusByCategory = [];
        if (!empty($payableByCategory)) {
            $adjuster = new SurplusAdjuster();
            $surplusPending = $adjuster->pendingSurplus($aaiId, $enrollment !== '' ? $enrollment : null);
            $surplusByCategory = $adjuster->simulate($surplusPending, $payableByCategory);
        }

        $zeroSurplus = ['available' => 0.0, 'consumed' => 0.0, 'invoice' => null, 'remaining' => 0.0];

        // ── write the academic snapshot ──
        foreach ($byCategory as $cat => $rows) {
            $fee = $feeFor[$cat];
            $base = $fee ? (float) $fee['amountBase'] : 0.0;
            $surplus = $surplusByCategory[$cat] ?? $zeroSurplus;

            $components = is_array($fee['arrearComponents'] ?? null) ? $fee['arrearComponents'] : [];
            $actual = [
                'admission' => (float) ($components['admission'] ?? 0),
                'security' => (float) ($components['security'] ?? 0),
                'medical' => (float) ($components['medical'] ?? 0),
                'activity' => (float) ($components['activity'] ?? 0),
            ];

            // The one-time fees carry their own waiver, exactly as on the single-student
            // page. Hard-coding these to zero meant a grader could enter an admission-fee
            // waiver in the Bulk Update drawer, have it saved, and then post a challan
            // that charged the fee in full.
            $disc = $categoryComponentDisc[$cat]
                ?? ['admission' => 0.0, 'security' => 0.0, 'medical' => 0.0, 'activity' => 0.0];

            // A discount can never exceed its own line, so payable never goes negative.
            // Each line is rounded BEFORE it is summed, so the stored one-time total is
            // the sum of the printed lines to the paisa.
            $lineDisc = [];
            $linePayable = [];
            $linePct = [];
            foreach ($actual as $component => $lineActual) {
                $d = min((float) $disc[$component], $lineActual);
                $lineDisc[$component] = round($d, 2);
                $linePayable[$component] = round($lineActual - $d, 2);
                $linePct[$component] = $lineActual > 0 ? round(($d * 100) / $lineActual, 4) : 0;
            }

            $tuitionDisc = min($categoryTuitionDisc[$cat], $base);
            $tuitionPayable = round($base - $tuitionDisc, 2);
            $tuitionPct = $base > 0 ? round(($tuitionDisc * 100) / $base, 4) : 0;

            $oneTimeActual = array_sum($actual);
            $oneTimeDisc = array_sum($lineDisc);
            $oneTimePayable = array_sum($linePayable);
            $oneTimePct = $oneTimeActual > 0 ? round(($oneTimeDisc * 100) / $oneTimeActual, 4) : 0;

            $totalActual = round($base + $oneTimeActual, 2);
            $totalDisc = round($tuitionDisc + $oneTimeDisc, 2);
            $totalPayable = round($tuitionPayable + $oneTimePayable, 2);
            $totalPct = $totalActual > 0 ? round(($totalDisc * 100) / $totalActual, 4) : 0;

            $categoryPayload = [
                'SSD_SOURCE_PAGE' => $this->sourcePage(),
                'SSD_SAVED_BY' => $savedBy,
                'SSD_SAVED_AT' => $savedAt,
                // The type that actually priced this category, which is not always the
                // one requested — see resolveSnapshotFee().
                'SSD_STUDENT_TYPE' => $effectiveType[$cat] ?? $studentType,
                'SSD_SCHOOL_ID' => $sdpo ? $sdpo->school_id : null,
                'SSD_DEPT_ID' => $sdpo ? $sdpo->dept_id : null,
                'SSD_PROG_ID' => $sdpo ? $sdpo->prog_id : null,
                // The challan fallback carries no ffs row, so these can be absent.
                'SSD_FFS_ID' => $fee['ffsId'] ?? null,
                'SSD_FEE_TYPE' => $fee['feeType'] ?? null,
                'SSD_TUITION_FEE' => ($fee && ($fee['referenceLabel'] ?? '') === 'Tuition Fee') ? $fee['referenceFee'] : null,
                'SSD_PER_CREDIT_FEE' => ($fee && ($fee['referenceLabel'] ?? '') === 'Per Credit Fee') ? $fee['referenceFee'] : null,
                'SSD_CREDIT_HOURS' => $fee['creditHours'] ?? null,
                'SSD_REPEAT_CREDIT_HOURS' => $fee['repeatCredits'] ?? null,

                'SSD_TUITION_FEE_ACTUAL' => $base,
                'SSD_ADMISSION_FEE_ACTUAL' => $actual['admission'],
                'SSD_SECURITY_FEE_ACTUAL' => $actual['security'],
                'SSD_MEDICAL_FEE_ACTUAL' => $actual['medical'],
                'SSD_ACTIVITY_FEE_ACTUAL' => $actual['activity'],
                'SSD_ONETIME_FEE_ACTUAL' => round($oneTimeActual, 2),
                'SSD_TOTAL_FEE_ACTUAL' => $totalActual,

                'SSD_TUITION_DISC_PCT' => $tuitionPct,
                'SSD_ADMISSION_DISC_PCT' => $linePct['admission'],
                'SSD_SECURITY_DISC_PCT' => $linePct['security'],
                'SSD_MEDICAL_DISC_PCT' => $linePct['medical'],
                'SSD_ACTIVITY_DISC_PCT' => $linePct['activity'],
                'SSD_ONETIME_DISC_PCT' => $oneTimePct,
                'SSD_TOTAL_DISC_PCT' => $totalPct,

                'SSD_TUITION_DISC_AMT' => round($tuitionDisc, 2),
                'SSD_ADMISSION_DISC_AMT' => $lineDisc['admission'],
                'SSD_SECURITY_DISC_AMT' => $lineDisc['security'],
                'SSD_MEDICAL_DISC_AMT' => $lineDisc['medical'],
                'SSD_ACTIVITY_DISC_AMT' => $lineDisc['activity'],
                'SSD_ONETIME_DISC_AMT' => round($oneTimeDisc, 2),
                'SSD_TOTAL_DISC_AMT' => $totalDisc,

                'SSD_TUITION_PAYABLE' => $tuitionPayable,
                'SSD_ADMISSION_PAYABLE' => $linePayable['admission'],
                'SSD_SECURITY_PAYABLE' => $linePayable['security'],
                'SSD_MEDICAL_PAYABLE' => $linePayable['medical'],
                'SSD_ACTIVITY_PAYABLE' => $linePayable['activity'],
                'SSD_ONETIME_PAYABLE' => round($oneTimePayable, 2),
                'SSD_TOTAL_PAYABLE' => $totalPayable,

                // SSD_TOTAL_PAYABLE stays PRE-surplus so the line maths is unaffected;
                // SSD_INVOICE_AMOUNT is the figure that reaches INVOICEAMOUNT.
                'SSD_SURPLUS_AVAILABLE' => round((float) $surplus['available'], 2),
                'SSD_SURPLUS_PENDING' => round($surplusPending, 2),
                'SSD_SURPLUS_CONSUMED' => round((float) $surplus['consumed'], 2),
                'SSD_INVOICE_AMOUNT' => $surplus['invoice'] === null
                    ? $totalPayable
                    : round((float) $surplus['invoice'], 2),

                'SSD_COURSE_COUNT' => ($fee && isset($fee['courseCount'])) ? $fee['courseCount'] : null,
                'SSD_REPEAT_FEE' => ($fee && isset($fee['repeatFee'])) ? $fee['repeatFee'] : null,
            ];

            foreach ($rows as $row) {
                $ssdId = (int) ($row->ssd_id ?? $row->SSD_ID);
                $pct = (float) ($row->ssd_percentage ?? $row->SSD_PERCENTAGE ?? 0);

                // SSD_AMOUNT is this ROW's own discount; every other figure above
                // describes the whole challan and repeats identically on each row.
                $payload = $categoryPayload;
                $payload['SSD_AMOUNT'] = round(($pct / 100) * $base, 2);

                // Never seize a row another page owns. This loop runs over EVERY active
                // row of the student and session — not just the ones that were saved — so
                // re-stamping SOURCE_PAGE here would silently transfer ownership of rows
                // that were never touched, and defeat the read-only rule that reads it.
                if ($this->snapshotRowIsForeign($row, 'ssd_source_page')) {
                    unset($payload['SSD_SOURCE_PAGE']);
                }

                DB::table('students_scholarships_details')
                    ->where('ssd_id', $ssdId)
                    ->update($this->filterExistingColumns('students_scholarships_details', $payload));
            }
        }

        // ── write the hostel snapshot ──
        if (!empty($hostelRows)) {
            $surplus = $surplusByCategory['Hostel_Fee'] ?? $zeroSurplus;
            $hostelType = $this->snapshotHostelAssignmentType($aaiId, $enrollment, $sessionId);

            $cappedDisc = min($hostelDisc, $hostelFee);
            $hostelPayable = round($hostelFee - $cappedDisc, 2);
            $hostelPct = $hostelFee > 0 ? round(($cappedDisc * 100) / $hostelFee, 4) : 0;

            $hostelPayload = [
                'SSHD_SOURCE_PAGE' => $this->sourcePage(),
                'SSHD_SAVED_BY' => $savedBy,
                'SSHD_SAVED_AT' => $savedAt,
                'SSHD_STUDENT_TYPE' => $studentType,
                'SSHD_SCHOOL_ID' => $sdpo ? $sdpo->school_id : null,
                'SSHD_DEPT_ID' => $sdpo ? $sdpo->dept_id : null,
                'SSHD_PROG_ID' => $sdpo ? $sdpo->prog_id : null,
                'SSHD_HOSTEL_TYPE_ID' => $hostelType ? $hostelType->ht_id : null,
                'SSHD_HOSTEL_TYPE_NAME' => $hostelType ? $hostelType->ht_name : null,

                'SSHD_HOSTEL_FEE_ACTUAL' => $hostelFee,
                'SSHD_HOSTEL_DISC_PCT' => $hostelPct,
                'SSHD_HOSTEL_DISC_AMT' => round($cappedDisc, 2),
                'SSHD_HOSTEL_PAYABLE' => $hostelPayable,
                'SSHD_TOTAL_FEE_ACTUAL' => $hostelFee,
                'SSHD_TOTAL_DISC_AMT' => round($cappedDisc, 2),
                'SSHD_TOTAL_PAYABLE' => $hostelPayable,

                'SSHD_SURPLUS_AVAILABLE' => round((float) $surplus['available'], 2),
                'SSHD_SURPLUS_PENDING' => round($surplusPending, 2),
                'SSHD_SURPLUS_CONSUMED' => round((float) $surplus['consumed'], 2),
                'SSHD_INVOICE_AMOUNT' => $surplus['invoice'] === null
                    ? $hostelPayable
                    : round((float) $surplus['invoice'], 2),
            ];

            foreach ($hostelRows as $row) {
                $payload = $hostelPayload;
                // Same reason as the academic loop above: do not take ownership of a row
                // that belongs to another page just because it shares a student.
                if ($this->snapshotRowIsForeign($row, 'sshd_source_page')) {
                    unset($payload['SSHD_SOURCE_PAGE']);
                }

                DB::table('fin_students_scholarships_hostel_details')
                    ->where('sshd_id', (int) ($row->sshd_id ?? $row->SSHD_ID))
                    ->update($this->filterExistingColumns('fin_students_scholarships_hostel_details', $payload));
            }
        }

        return [
            'ok' => true,
            'categories' => $resolved,
            'warnings' => $warnings,
            'surplus_pending' => round($surplusPending, 2),
        ];
    }

    /**
     * Resolve a category's fee basis the same way the amount is priced.
     *
     * MUST mirror computeAcademicScholarshipAmount()'s ladder. A plain
     * resolveAcademicFeeStructure(..., 'Existing', ...) is not enough: plenty of
     * programs carry only a 'New Admission <SEASON>' row for a session, in which case
     * the amount would price correctly while the snapshot silently stayed empty and
     * the challan showed "cannot post".
     *
     * @return array{fee: array|null, student_type: string, message: string}
     */
    protected function resolveSnapshotFee(int $sessionId, string $feeRef, string $category): array
    {
        $typesToTry = ['Existing'];
        $sessName = DB::table('academic_sessions')->where('sess_id', $sessionId)->value('sess_name');
        $season = $sessName ? strtoupper(strtok(trim((string) $sessName), ' ')) : '';
        if ($season !== '') {
            $typesToTry[] = 'New Admission ' . $season;
        }

        $firstMessage = null;
        foreach ($typesToTry as $type) {
            $res = $this->resolveAcademicFeeStructure($sessionId, $feeRef, $type, $category);
            if (!empty($res['ok']) && (float) ($res['amountBase'] ?? 0) > 0) {
                return ['fee' => $res, 'student_type' => $type, 'message' => ''];
            }
            if ($firstMessage === null) {
                $firstMessage = trim((string) ($res['message'] ?? ''));
            }
        }

        // Last resort for Full Tuition: the live challan, then a loose fee-structure row.
        if ($category === 'Full_semester') {
            $fallback = $this->resolveFullTuitionFallback($sessionId, $feeRef);
            if (!empty($fallback['ok']) && (float) ($fallback['amountBase'] ?? 0) > 0) {
                return ['fee' => $fallback, 'student_type' => 'Existing', 'message' => ''];
            }
        }

        return [
            'fee' => null,
            'student_type' => 'Existing',
            'message' => $firstMessage !== '' && $firstMessage !== null
                ? $firstMessage
                : 'fee structure could not be resolved',
        ];
    }

    /**
     * A category's payable BEFORE surplus, floored at 0 — the figure
     * FIN_PRO_ADJUST_CHALLAN_SURPLUS compared against.
     *
     * The one-time waiver has to come off here too, not just in the written snapshot.
     * This is what the surplus simulation prices the challan at, and a figure higher
     * than SSD_TOTAL_PAYABLE freezes SSD_SURPLUS_CONSUMED / SSD_INVOICE_AMOUNT against
     * a payable the challan no longer has — surplus spent against nothing.
     */
    protected function snapshotCategoryPayable($fee, array $tuitionDisc, array $componentDisc, string $category): float
    {
        if (empty($fee) || empty($fee['ok'])) {
            return 0.0;
        }

        $base = (float) $fee['amountBase'];
        $tDisc = min((float) ($tuitionDisc[$category] ?? 0.0), $base);

        $disc = $componentDisc[$category]
            ?? ['admission' => 0.0, 'security' => 0.0, 'medical' => 0.0, 'activity' => 0.0];

        $oneTimeActual = 0.0;
        $oneTimeDisc = 0.0;
        foreach ((is_array($fee['arrearComponents'] ?? null) ? $fee['arrearComponents'] : []) as $component => $actual) {
            $oneTimeActual += (float) $actual;
            // Same per-line cap the snapshot applies, or this would price the challan
            // lower than the snapshot does whenever a waiver exceeds its own line.
            $oneTimeDisc += min((float) ($disc[$component] ?? 0), (float) $actual);
        }

        return round(max(0.0, ($base - $tDisc) + ($oneTimeActual - $oneTimeDisc)), 2);
    }

    /**
     * Does this row already belong to a DIFFERENT page?
     *
     * Blank or missing means unclaimed — those are fair game, and treating them as
     * foreign would mean the vast majority of legacy rows never got a snapshot stamp.
     * Only a non-empty value that is not ours counts as somebody else's.
     */
    protected function snapshotRowIsForeign($row, string $column): bool
    {
        $current = $row->{$column}
            ?? $row->{strtoupper($column)}
            ?? null;

        $current = strtoupper(trim((string) $current));

        return $current !== '' && $current !== strtoupper($this->sourcePage());
    }

    /** Active academic rows for this student + session, keyed however Oracle cases them. */
    protected function snapshotAcademicRows(int $sessionId, ?int $aaiId, string $enrollment): array
    {
        return DB::table('students_scholarships_details')
            ->where('ssd_sess_id', $sessionId)
            ->where('status', 'E')
            ->where(function ($q) use ($aaiId, $enrollment) {
                if ($aaiId) {
                    $q->where('ssd_app_id', $aaiId);
                }
                if ($enrollment !== '') {
                    $q->orWhereRaw('UPPER(TRIM(ssd_sai_enrollment_no)) = ?', [$enrollment]);
                }
            })
            ->orderBy('ssd_id')
            ->get()
            ->all();
    }

    protected function snapshotHostelRows(int $sessionId, ?int $aaiId, string $enrollment): array
    {
        return DB::table('fin_students_scholarships_hostel_details')
            ->where('sshd_sess_id', $sessionId)
            ->whereRaw("NVL(TRIM(sshd_status), 'E') = 'E'")
            ->where(function ($q) use ($aaiId, $enrollment) {
                if ($aaiId) {
                    $q->where('sshd_app_id', $aaiId);
                }
                if ($enrollment !== '') {
                    $q->orWhereRaw('UPPER(TRIM(sshd_sai_enrollment_no)) = ?', [$enrollment]);
                }
            })
            ->orderBy('sshd_id')
            ->get()
            ->all();
    }

    /** Mirrors StudentRecordRevisedController::resolveStudentSdpo(). */
    protected function snapshotStudentSdpo(string $studentType, string $studentId)
    {
        if ($this->isEnrolledStudentType($studentType)) {
            return DB::table('students_apply_info')
                ->select('SAI_SDPO_SCHOOL_ID as school_id', 'SAI_SDPO_DEPT_ID as dept_id', 'SAI_SDPO_PROG_ID as prog_id')
                ->whereRaw('LOWER(SAI_ENROLLMENT_NO) = ?', [strtolower($studentId)])
                ->first();
        }

        return DB::table('admission_apply_info')
            ->select('AAI_SDPO_SCHOOL_ID as school_id', 'AAI_SDPO_DEPT_ID as dept_id', 'AAI_SDPO_PROG_ID as prog_id')
            ->where('AAI_ID', $studentId)
            ->first();
    }

    /** Mirrors StudentRecordRevisedController::resolveHostelAssignmentType(). */
    protected function snapshotHostelAssignmentType(?int $aaiId, string $enrollment, int $sessionId)
    {
        $assignment = (new \App\Services\Hostel\HostelAssignmentResolver())
            ->resolve($sessionId, $aaiId, $enrollment);

        if (!$assignment['assigned']) {
            return null;
        }

        return (object) [
            'ht_id'   => $assignment['htId'],
            'ht_name' => $assignment['htName'],
        ];
    }
}
