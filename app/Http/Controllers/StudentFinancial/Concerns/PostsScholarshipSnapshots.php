<?php

namespace App\Http\Controllers\StudentFinancial\Concerns;

use App\Services\StudentRecord\ChallanPosting\ChallanPosterFactory;
use App\Services\StudentRecord\ChallanPosting\PostContext;
use App\Services\StudentRecord\ChallanPosting\Support\ChallanCanceller;
use App\Services\StudentRecord\ChallanPosting\Support\SurplusAdjuster;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Writes accepted bulk challans from the frozen snapshot.
 *
 * ── THIS IS A SECOND COPY ────────────────────────────────────────────────────
 * Mirrors StudentRecordRevisedController::postSnapshot() (around lines 1597-1884)
 * and the private helpers below it. Kept separate on purpose; a change to posting
 * rules MUST be applied in BOTH places or the two pages diverge again.
 * ─────────────────────────────────────────────────────────────────────────────
 *
 * Difference from the single page, by design: that page cancels the student's live
 * challans at Save, this one cancels at Post inside the same transaction as the
 * insert. At bulk scale a half-finished Save must not be able to leave hundreds of
 * students with no challan at all. The surplus is unaffected by the change because
 * pendingSurplus() already excludes credit a cancellation will mint.
 */
trait PostsScholarshipSnapshots
{
    abstract protected function sourcePage(): string;

    /**
     * Post one student's accepted challans, in the order Save simulated the surplus.
     *
     * Each CATEGORY is its own transaction. It used to be one transaction for the whole
     * student, which meant a hostel failure rolled back that student's academic challan
     * too — 44 hostel challans failed this way and took nothing with them only because
     * the academic side had already committed via the cancellation procedure.
     *
     * Nothing is ever skipped silently: every accepted category comes back with a status
     * and, when it did not post, the reason. The caller reports them all.
     *
     * @param  array<int, string> $acceptedCategories challan categories to write
     * @return array<int, array{category:string,status:string,reason:?string,messages:array<int,string>}>
     */
    protected function postAcceptedChallansForStudent(
        int $aaiId,
        string $enrollment,
        int $sessionId,
        string $studentType,
        array $acceptedCategories
    ): array {
        $results = [];
        $record = function ($category, $status, $reason = null, array $messages = []) use (&$results) {
            $results[] = [
                'category' => $category,
                'status' => $status,
                'reason' => $reason,
                'messages' => $messages,
            ];
        };

        if (!$aaiId) {
            foreach ($acceptedCategories as $category) {
                $record($category, 'failed', 'Student application id could not be resolved.');
            }

            return $results;
        }

        $enrollment = strtoupper(trim($enrollment));
        $enrollmentForChallan = $enrollment !== '' ? $enrollment : null;
        $user = auth()->user()->username ?? 'system';
        $now = Carbon::now('Asia/Karachi');

        // Same walk order Save used when it simulated the split. Any other order and a
        // two-challan post spends the surplus differently from the accepted preview.
        $walk = array_keys($this->academicCategoryLabels());
        $walk[] = 'Hostel_Fee';

        $work = [];
        foreach ($walk as $category) {
            if (!in_array($category, $acceptedCategories, true)) {
                continue;
            }
            $rows = $this->stagedRowsForCategory($aaiId, $enrollment, $sessionId, $category);
            if (!empty($rows)) {
                $work[$category] = collect($rows);
                continue;
            }
            // The drop that used to be invisible: an accepted category whose rows are no
            // longer ours to post. Say which of the two reasons it was.
            $record($category, 'skipped', $this->stagedSkipReason($aaiId, $enrollment, $sessionId, $category));
        }

        // An accepted category outside the known walk can never reach $work, so name it
        // rather than letting it fall off the end.
        foreach ($acceptedCategories as $category) {
            if (!in_array($category, $walk, true)) {
                $record($category, 'skipped', 'Unknown challan category "' . $category . '" — nothing was posted for it.');
            }
        }

        if (empty($work)) {
            return $results;
        }

        $adjuster = new SurplusAdjuster();
        $factory = new ChallanPosterFactory();
        $canceller = new ChallanCanceller();

        // ── pre-flight ──
        //
        // cancelWhere() runs fin_bulk_challan_cancellation, an Oracle procedure that
        // COMMITs internally and DELETEs from student_challan. Once it has run, nothing
        // Laravel does can put the challan back — DB::rollBack() is a no-op against it.
        // So every check that CAN be made before the cancel is made here, and a category
        // that fails one is dropped without its live challan ever being touched.
        //
        // The totals test below is the one that matters most. The posters read
        // TOTAL_DISC_AMT / TOTAL_PAYABLE off one row (PostContext::head()) because
        // refreshScholarshipSnapshot() repeats them on every row of the challan. When that
        // stamp is stale — a row written by a page that stores only a percentage, a row
        // whose category moved — the challan would be issued with a discount that no longer
        // matches what the student holds. That is how removing one of two scholarships
        // could post a challan showing none at all.
        //
        // Refuse rather than recompute. Recomputing here would rewrite SSD_SURPLUS_PENDING
        // and quietly defeat the freeze check below, and would post figures the grader
        // never reviewed — the whole point of a staged snapshot is that what is posted is
        // what was accepted.
        foreach (array_keys($work) as $category) {
            if ($factory->make($category) === null) {
                $record($category, 'skipped', 'No challan poster is defined for "' . $category . '".');
                unset($work[$category]);
                continue;
            }

            if ($this->scholarshipPctOverCap($work[$category], $category === 'Hostel_Fee')) {
                $record($category, 'skipped',
                    'Scholarship total exceeds 100% for the '
                    . ($this->academicCategoryLabels()[$category] ?? $category)
                    . ' challan. Nothing was cancelled or posted. Reduce the combined '
                    . 'percentage on the Bulk Update form, then review and post again.');
                unset($work[$category]);
                continue;
            }

            if (!$this->stagedTotalsAgree($category, $work[$category])) {
                $record($category, 'skipped',
                    'The stored total for the ' . ($this->academicCategoryLabels()[$category] ?? $category)
                    . ' challan no longer matches this student\'s scholarship rows. Nothing was '
                    . 'cancelled or posted. Re-save this student on the Bulk Update form, then '
                    . 'review and post again.');
                unset($work[$category]);
            }
        }

        if (empty($work)) {
            return $results;
        }

        // ── surplus freeze check, once, before anything is cancelled ──
        //
        // Deliberately per STUDENT and not per category: posting one category spends
        // surplus, so re-checking before the second category would fail against a figure
        // this very run moved. The staged value is the one Save froze for both.
        $frozenPending = null;
        foreach ($work as $category => $rows) {
            // head(), not first(). Rows written by the merit-entry and approval flows
            // carry only a percentage and NULL figures, so rows[0] can read NULL here and
            // send the whole check to sleep — see PostContext::head()'s docblock, which
            // exists because the same assumption once posted challans with every
            // scholarship dropped off them.
            $head = (new PostContext(['category' => $category, 'rows' => $rows]))->head();
            if ($head === null) {
                continue;
            }
            $column = ($category === 'Hostel_Fee') ? 'sshd_surplus_pending' : 'ssd_surplus_pending';
            $value = $head->{$column} ?? $head->{strtoupper($column)} ?? null;
            if ($value !== null) {
                $frozenPending = (float) $value;
                break;
            }
        }
        if ($frozenPending !== null) {
            $livePending = $adjuster->pendingSurplus($aaiId, $enrollmentForChallan);
            if (round($frozenPending, 2) !== round($livePending, 2)) {
                $reason = 'Surplus changed since the challan was previewed (previewed Rs '
                    . number_format($frozenPending, 2) . ', now Rs ' . number_format($livePending, 2)
                    . '). Nothing was cancelled or posted. Re-save this student on the Bulk Update '
                    . 'form, then review and post again.';
                foreach (array_keys($work) as $category) {
                    $record($category, 'skipped', $reason);
                }

                return $results;
            }
        }

        try {
            $student = $this->resolveStudentForChallanBulk($studentType, $enrollmentForChallan, $aaiId, $sessionId);
        } catch (\Throwable $e) {
            foreach (array_keys($work) as $category) {
                $record($category, 'failed', $e->getMessage());
            }

            return $results;
        }

        foreach ($work as $category => $rows) {
            $messages = [];
            $cancelled = false;

            try {
                DB::beginTransaction();

                // ── who authorised these figures ──
                //
                // Written BEFORE the challan, deliberately: Accept and Post are separate
                // acts, and a post that throws half way must still leave a record of who
                // signed off. SAVED_BY and POSTED_BY cannot stand in — saving is not
                // approval, and POSTED_BY only ever exists for a post that succeeded.
                //
                // Mirrors StudentRecordRevisedController::postSnapshot(), which stamps the
                // same two columns at the same point for the same reason.
                $this->stampOwnedRows($aaiId, $sessionId, $category, ($category === 'Hostel_Fee')
                    ? ['SSHD_ACK_BY' => $user, 'SSHD_ACK_AT' => $now]
                    : ['SSD_ACK_BY' => $user, 'SSD_ACK_AT' => $now], $enrollment);

                $poster = $factory->make($category);

                // Hostel carry-over lives only on the live challan and
                // fin_bulk_challan_cancellation DELETEs it — capture before cancelling.
                //
                // Filtered to columns that actually exist BEFORE the emptiness test, not
                // after. captureHostelCarryBulk() always returns its full key set, so the
                // old order let an all-dropped payload through to ->update([]), which
                // Oracle renders as `set  where ...` and rejects with ORA-01747. Nulls go
                // too: a live challan with no hostel attributes must not blank the ones
                // already staged. Same shape as stampOwnedRows().
                $hostelCarry = ($category === 'Hostel_Fee')
                    ? $this->captureHostelCarryBulk($aaiId, $enrollmentForChallan, $sessionId)
                    : [];
                $hostelCarry = $this->filterExistingColumns(
                    'fin_students_scholarships_hostel_details',
                    array_filter($hostelCarry, function ($value) {
                        return $value !== null;
                    })
                );

                $existing = $this->findLiveChallanBulk($aaiId, $enrollmentForChallan, $sessionId, $category);
                $dates = $this->resolveChallanDatesBulk($aaiId, $enrollmentForChallan, $sessionId, $studentType, $existing);

                $canceller->cancelWhere(
                    $aaiId,
                    $sessionId,
                    $enrollmentForChallan,
                    $poster->cancellationCriteria(),
                    $user,
                    'Cancelled on Post from Scholarship Bulk Post — replacement issued'
                );
                // Past this point a failure cannot be undone — see the catch below.
                $cancelled = true;

                if (!empty($hostelCarry)) {
                    DB::table('fin_students_scholarships_hostel_details')
                        ->where('SSHD_APP_ID', $aaiId)
                        ->where('SSHD_SESS_ID', $sessionId)
                        ->update($hostelCarry);

                    // Re-read so the poster sees the carried attributes.
                    $rows = collect($this->stagedRowsForCategory($aaiId, $enrollment, $sessionId, 'Hostel_Fee'));
                }

                $result = $poster->post(new PostContext([
                    'aaiId' => $aaiId,
                    'enrollment' => $enrollmentForChallan,
                    'enrollmentRaw' => $enrollment !== '' ? $enrollment : (string) $aaiId,
                    'sessionId' => $sessionId,
                    'studentType' => $studentType,
                    'category' => $category,
                    'rows' => $rows,
                    'student' => $student,
                    'dueDate' => $dates['due'],
                    'validDate' => $dates['valid'],
                    'entryUser' => $user,
                    'entryUserId' => auth()->id(),
                    'sourcePage' => $this->sourcePage(),
                    'existingChallan' => $existing,
                ]));

                // A poster refusing (e.g. a PAID credit-hour challan) fails THIS category
                // only. The student's other categories are separate transactions and are
                // unaffected.
                if (!$result->ok) {
                    throw new \RuntimeException($result->message);
                }

                $messages[] = $result->message;
                foreach ($result->warnings as $warning) {
                    $messages[] = $warning;
                }

                // ── surplus: spend exactly what was accepted, not a recomputation ──
                if ($result->challanId !== null) {
                    $isHostel = ($category === 'Hostel_Fee');
                    // head(), not first() — same reason as the freeze check above. Reading
                    // an unstamped row here gave $payable = 0, which made apply() consume
                    // nothing and then wrote INVOICEAMOUNT = 0 straight over the challan
                    // the poster had just priced correctly.
                    $head = (new PostContext(['category' => $category, 'rows' => $rows]))->head();

                    $payableCol = $isHostel ? 'sshd_total_payable' : 'ssd_total_payable';
                    $consumedCol = $isHostel ? 'sshd_surplus_consumed' : 'ssd_surplus_consumed';

                    $payable = (float) ($head->{$payableCol} ?? $head->{strtoupper($payableCol)} ?? 0);
                    $plannedRaw = $head->{$consumedCol} ?? $head->{strtoupper($consumedCol)} ?? null;
                    $planned = $plannedRaw !== null ? (float) $plannedRaw : null;

                    $applied = $adjuster->apply(
                        $aaiId,
                        $enrollmentForChallan,
                        $result->challanId,
                        $payable,
                        $user,
                        auth()->id(),
                        $planned
                    );

                    // Only when surplus was actually spent. With consumed = 0 the invoice
                    // equals the payable the poster already wrote, so this is a no-op at
                    // best — and one fewer statement that can throw after the cancel.
                    if ($applied['consumed'] > 0) {
                        DB::table('student_challan')
                            ->where('id', $result->challanId)
                            ->update([
                                'SC_SURPLUS_AMOUNT' => $applied['consumed'],
                                'INVOICEAMOUNT' => $applied['invoice'],
                                'SC_UPDATED_BY_USERNAME' => $user,
                                'UPDATED_AT' => $now,
                            ]);
                    }

                    // Keep the snapshot honest — the preview reads these columns.
                    $this->stampOwnedRows($aaiId, $sessionId, $category, $isHostel
                        ? ['SSHD_SURPLUS_CONSUMED' => $applied['consumed'], 'SSHD_INVOICE_AMOUNT' => $applied['invoice']]
                        : ['SSD_SURPLUS_CONSUMED' => $applied['consumed'], 'SSD_INVOICE_AMOUNT' => $applied['invoice']], $enrollment);

                    if ($applied['consumed'] > 0) {
                        $messages[] = 'Adjusted Rs ' . number_format($applied['consumed'], 2)
                            . ' of surplus against the ' . $poster->label() . ' challan.';
                    }
                }

                // ── which challan these rows produced ──
                //
                // chalanfinPreview.php prints CHALLAN_NO when it is set and falls back to
                // the challan's ID otherwise, so without this a bulk-posted challan is
                // identified differently from a single-posted one. It also removes the need
                // to trace a scholarship back to its document by re-matching on student +
                // session + category, which is the loose matching that causes trouble
                // elsewhere.
                //
                // Academic only: fin_students_scholarships_hostel_details has no
                // SSHD_CHALLAN_NO column, so there is nothing to write for hostel and
                // filterExistingColumns() would drop it silently anyway. The single page's
                // hostel branch is inert for the same reason — a shared gap, not a new one.
                if ($result->challanNo !== null && $category !== 'Hostel_Fee') {
                    $this->stampOwnedRows($aaiId, $sessionId, $category, [
                        'CHALLAN_NO' => $result->challanNo,
                    ], $enrollment);
                }

                // Clear staging for THIS category only — an unaccepted challan stays PENDING.
                //
                // Scoped to rows this page owns. The challan is COMPOSED from every active
                // row of the category, foreign ones included, because its discount is the sum
                // of all the student's awards — but clearing a foreign row's staging is a
                // different act entirely. It used to un-stage work the single Student Record
                // page had saved and not yet posted, which killed that grader's Accept → Post
                // path and, since that page cancels at Save, could leave them with neither
                // their staged figures nor a way to re-issue them.
                $this->stampOwnedRows($aaiId, $sessionId, $category, ($category === 'Hostel_Fee')
                    ? ['SSHD_BULK_POST_STATUS' => null, 'SSHD_POSTED_BY' => $user, 'SSHD_POSTED_AT' => $now]
                    : ['SSD_BULK_POST_STATUS' => null, 'SSD_POSTED_BY' => $user, 'SSD_POSTED_AT' => $now], $enrollment);

                DB::commit();

                $record($category, 'posted', null, array_values(array_unique($messages)));
            } catch (\Throwable $e) {
                // Guarded: the cancellation procedure has already COMMITted by this point,
                // so Laravel's transaction counter and Oracle's actual state disagree and a
                // bare rollBack() can itself throw. Losing the real error to a rollback
                // error would put us straight back to failures nobody can diagnose.
                try {
                    DB::rollBack();
                } catch (\Throwable $ignore) {
                    // deliberately swallowed — $e below is the error worth reporting
                }

                $reason = $e->getMessage();

                // The rollback above cannot reach the cancellation: cancelWhere() drives an
                // Oracle procedure that COMMITs internally. If we got past it, this student
                // has lost that challan and it will NOT come back on its own. Say so plainly
                // rather than reporting a generic failure the grader cannot act on.
                if ($cancelled) {
                    $reason .= ' — WARNING: the previous ' . ($this->academicCategoryLabels()[$category] ?? $category)
                        . ' challan was already cancelled and could NOT be restored (the cancellation '
                        . 'procedure commits inside Oracle). This student currently has no such challan '
                        . 'and must be re-issued.';
                }

                $record($category, 'failed', $reason);

                \Log::error('Bulk scholarship post failed', [
                    'enrollment' => $enrollment !== '' ? $enrollment : null,
                    'aai_id' => $aaiId,
                    'session_id' => $sessionId,
                    'category' => $category,
                    'cancelled_before_failure' => $cancelled,
                    'exception' => get_class($e),
                    'message' => $e->getMessage(),
                    'file' => $e->getFile() . ':' . $e->getLine(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }
        }

        return $results;
    }

    /**
     * Why an accepted category produced no postable rows.
     *
     * stagedRowsForCategory() returns [] for two quite different situations and the
     * grader needs to be told which: rows that are simply not there, and rows that are
     * there but belong to someone else. Reported instead of dropped — this silence is
     * what let 44 hostel challans disappear from a 390-challan run without a word.
     */
    protected function stagedSkipReason(int $aaiId, string $enrollment, int $sessionId, string $category): string
    {
        $label = $this->academicCategoryLabels()[$category] ?? $category;

        $hostel = ($category === 'Hostel_Fee');
        $table = $hostel ? 'fin_students_scholarships_hostel_details' : 'students_scholarships_details';
        $statusColumn = $hostel ? 'sshd_bulk_post_status' : 'ssd_bulk_post_status';
        $sourceColumn = $hostel ? 'sshd_source_page' : 'ssd_source_page';

        $query = DB::table($table);
        if ($hostel) {
            $query->where('sshd_sess_id', $sessionId)
                ->whereRaw("NVL(TRIM(sshd_status), 'E') = 'E'");
        } else {
            $query->where('ssd_sess_id', $sessionId)
                ->where('status', 'E')
                ->whereRaw("NVL(ssd_challan_category, 'Full_semester') = ?", [$category]);
        }
        $this->applyStagedIdentity($query, $aaiId, $enrollment !== '' ? $enrollment : null, $hostel);

        $rows = $query->get()->all();

        if (empty($rows)) {
            return 'No active ' . $label . ' scholarship rows are staged for this student — nothing to post.';
        }

        foreach ($rows as $row) {
            $status = strtoupper(trim((string) ($row->{$statusColumn} ?? $row->{strtoupper($statusColumn)} ?? '')));
            if ($status === 'PENDING') {
                $source = trim((string) ($row->{$sourceColumn} ?? $row->{strtoupper($sourceColumn)} ?? ''));

                return 'The ' . $label . ' rows are staged by another page (' . ($source !== '' ? $source : 'unknown')
                    . '), so this page will not post them. Post them from that page instead.';
            }
        }

        return 'The ' . $label . ' challan is no longer marked PENDING — it looks already posted, '
            . 'or it was un-staged after you accepted it. Reload the list and check.';
    }

    /**
     * Every active row of one challan — NOT only the PENDING ones.
     *
     * Staging is per row, but a challan is per (student, challan category): its
     * discount is the sum of every active scholarship in that category. Re-saving one
     * scholarship marks only that row PENDING, so filtering on PENDING here would hand
     * the poster a partial set and it would issue a challan missing the other
     * scholarships. The snapshot totals are already computed across all active rows
     * (see BuildsScholarshipSnapshots::refreshScholarshipSnapshot), so the row set must
     * match them.
     *
     * The PENDING + source-page test still decides WHETHER this challan is ours to
     * post — it just no longer decides which rows make it up.
     */
    protected function stagedRowsForCategory(int $aaiId, string $enrollment, int $sessionId, string $category): array
    {
        if ($category === 'Hostel_Fee') {
            $rows = DB::table('fin_students_scholarships_hostel_details')
                ->where('sshd_sess_id', $sessionId)
                ->whereRaw("NVL(TRIM(sshd_status), 'E') = 'E'")
                ->where(function ($w) use ($aaiId, $enrollment) {
                    $w->where('sshd_app_id', $aaiId);
                    if ($enrollment !== '') {
                        $w->orWhereRaw('UPPER(TRIM(sshd_sai_enrollment_no)) = ?', [$enrollment]);
                    }
                })
                ->orderBy('sshd_id')
                ->get()
                ->all();

            return $this->hasPendingRow($rows, 'sshd_bulk_post_status', 'sshd_source_page') ? $rows : [];
        }

        $rows = DB::table('students_scholarships_details')
            ->where('ssd_sess_id', $sessionId)
            ->where('status', 'E')
            ->whereRaw("NVL(ssd_challan_category, 'Full_semester') = ?", [$category])
            ->where(function ($w) use ($aaiId, $enrollment) {
                $w->where('ssd_app_id', $aaiId);
                if ($enrollment !== '') {
                    $w->orWhereRaw('UPPER(TRIM(ssd_sai_enrollment_no)) = ?', [$enrollment]);
                }
            })
            ->orderBy('ssd_id')
            ->get()
            ->all();

        return $this->hasPendingRow($rows, 'ssd_bulk_post_status', 'ssd_source_page') ? $rows : [];
    }

    /**
     * The ONE write-back for a posted challan's rows — staging, surplus, ACK, challan no.
     *
     * There used to be two of these. stampStagedSnapshot() matched the student, session
     * and category and nothing else; this one added an ownership guard. They drifted
     * because nothing forced them to agree, and the unguarded one was the one clearing
     * BULK_POST_STATUS — so a bulk post silently un-staged rows the single Student Record
     * page had saved and not yet posted. Collapsed into a single method so there is one
     * ownership rule with nothing left to drift.
     *
     * The rule, shared with requeueChallanCategory() and hasPendingRow(): a BLANK source
     * is unclaimed and writable, a FOREIGN one is left alone. Blank must stay writable —
     * 13k legacy rows carry no tag, and requeueChallanCategory() deliberately does not
     * stamp one, so a strict guard would leave those PENDING forever.
     *
     * Note the asymmetry this preserves, which is deliberate: the challan is COMPOSED
     * from every active row of the category including foreign ones, because its discount
     * is the sum of all the student's awards (see stagedRowsForCategory()). Reading a
     * foreign row is correct; writing to one is not.
     *
     * Identity matches the select — app_id OR enrolment — so anything postable is also
     * stampable. Columns the DDL has not created are dropped by filterExistingColumns(),
     * so this degrades to a no-op rather than ORA-00904 on a database that is behind.
     */
    protected function stampOwnedRows(int $aaiId, int $sessionId, string $category, array $update, ?string $enrollment = null): void
    {
        $hostel = ($category === 'Hostel_Fee');
        $table = $hostel ? 'fin_students_scholarships_hostel_details' : 'students_scholarships_details';

        $update = $this->filterExistingColumns($table, $update);
        if (empty($update)) {
            return;
        }

        $sourceColumn = $hostel ? 'sshd_source_page' : 'ssd_source_page';
        $sourceExists = $hostel ? $this->hasSshdSourcePageColumn() : $this->hasSsdSourcePageColumn();

        $query = DB::table($table);

        if ($hostel) {
            $query->where('SSHD_SESS_ID', $sessionId);
        } else {
            $query->where('SSD_SESS_ID', $sessionId)
                ->whereRaw("NVL(SSD_CHALLAN_CATEGORY, 'Full_semester') = ?", [$category]);
        }

        // Same identity rule as the select — an enrolment-only row is postable, so it
        // must be stampable too, or its ACK and challan number are silently lost.
        $this->applyStagedIdentity($query, $aaiId, $enrollment, $hostel);

        if ($sourceExists) {
            $query->whereRaw(
                "UPPER(TRIM(NVL({$sourceColumn}, ''))) IN ('', ?)",
                [strtoupper($this->sourcePage())]
            );
        }

        $query->update($update);
    }

    /**
     * Does the discount this challan will be posted with match the awards the student
     * actually holds?
     *
     * What the discount SHOULD be is each row's percentage against the challan's own fee
     * base — deliberately computed from PERCENTAGE, not from the stored per-row AMOUNT.
     * AMOUNT is only written by a Save on this page, so a row created by the merit-entry
     * or approval flows has a real percentage and a zero amount; summing amounts would
     * then agree with a zero total and wave through a challan that silently drops every
     * scholarship.
     *
     * That expectation is compared against TUITION_DISC_AMT, not TOTAL_DISC_AMT. The
     * percentages describe the tuition waiver and nothing else — one-time-fee arrears are
     * absolute rupee amounts out of FIN_OTHER_SCHOLARSHIPS with no percentage to
     * re-derive, so they sit outside this test rather than inside it. Comparing against
     * the total instead would fail every student holding an arrears waiver.
     *
     * The head's own arithmetic is then checked separately: the posters write
     * SC_SCHOLARSHIP_AMOUNT straight from TOTAL_DISC_AMT, so a total that does not equal
     * tuition + one-time must still be caught. Rows staged before arrears reached this
     * page have one-time 0 and total == tuition, so they pass both clauses unchanged.
     *
     * Same arithmetic as refreshScholarshipSnapshot(), so re-saving the student produces
     * exactly the figure this expects. Half a rupee of slack absorbs the rounding.
     */
    protected function stagedTotalsAgree(string $category, $rows): bool
    {
        if (!$rows || $rows->isEmpty()) {
            return true;
        }

        $hostel = ($category === 'Hostel_Fee');
        $pctColumn = $hostel ? 'sshd_percentage' : 'ssd_percentage';

        $read = static function ($row, string $column) {
            return (float) ($row->{$column} ?? $row->{strtoupper($column)} ?? 0);
        };

        $ctx = new PostContext(['category' => $category, 'rows' => $rows]);
        $head = $ctx->head();
        if ($head === null) {
            return false;
        }

        $base = $hostel
            ? ($read($head, 'sshd_hostel_fee_actual') ?: $read($head, 'sshd_hostel_fee'))
            : $read($head, 'ssd_tuition_fee_actual');

        // No base means the challan is unpriced, which is blocked on its own account.
        // Nothing useful to compare here, so do not manufacture a second failure.
        if ($base <= 0) {
            return true;
        }

        $expected = 0.0;
        foreach ($rows as $row) {
            $expected += round(($read($row, $pctColumn) / 100) * $base, 2);
        }

        // The snapshot caps the discount at the fee — match that, or a student on 100%
        // plus a second award would look permanently inconsistent.
        $expected = min($expected, $base);

        // Hostel has no tuition/one-time split — the whole fee is the tuition analogue,
        // and there is no SSHD_TUITION_DISC_AMT column to compare against.
        if ($hostel) {
            return abs($expected - $read($head, 'sshd_total_disc_amt')) <= 0.5;
        }

        if (abs($expected - $read($head, 'ssd_tuition_disc_amt')) > 0.5) {
            return false;
        }

        return abs(($read($head, 'ssd_tuition_disc_amt') + $read($head, 'ssd_onetime_disc_amt'))
            - $read($head, 'ssd_total_disc_amt')) <= 0.5;
    }

    /**
     * Combined scholarship % on this challan, counting only awards with pct > 0.
     * A 0% row is a queued removal and must not block Post.
     */
    protected function scholarshipPctOverCap($rows, bool $hostel): bool
    {
        $pctColumn = $hostel ? 'sshd_percentage' : 'ssd_percentage';
        $sum = 0.0;
        foreach ($rows as $row) {
            $pct = (float) ($row->{$pctColumn} ?? $row->{strtoupper($pctColumn)} ?? 0);
            if ($pct > 0) {
                $sum += $pct;
            }
        }

        return $sum > 100.0001;
    }

    /** Is at least one row of this challan staged by THIS page? */
    protected function hasPendingRow(array $rows, string $statusColumn, string $sourceColumn): bool
    {
        foreach ($rows as $row) {
            $status = strtoupper(trim((string) ($row->{$statusColumn} ?? $row->{strtoupper($statusColumn)} ?? '')));
            if ($status !== 'PENDING') {
                continue;
            }

            $source = trim((string) ($row->{$sourceColumn} ?? $row->{strtoupper($sourceColumn)} ?? ''));
            // No source tag recorded (pre-DDL row) — treat it as ours rather than
            // stranding it, matching how the schema guards degrade elsewhere.
            if ($source === '' || strtoupper($source) === $this->sourcePage()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Identify a challan's rows the SAME way stagedRowsForCategory() selects them.
     *
     * That query matches `app_id OR enrolment`; every write-back used to match app_id
     * alone. A row carrying only an enrolment number — 1,063 of them across 644 students
     * — was therefore selected, posted and ledger-written, and then never cleared,
     * because `NULL = :aaiId` is never true. BULK_POST_STATUS stayed 'PENDING', the
     * challan reappeared on the next Load Pending, and posting it again wrote a second
     * set of ledger rows for the same award. It could not converge: the only thing that
     * clears the flag could never match the row.
     *
     * Anything postable must be clearable, so select and clear share this predicate.
     */
    protected function applyStagedIdentity($query, int $aaiId, ?string $enrollment, bool $hostel)
    {
        $appColumn = $hostel ? 'sshd_app_id' : 'ssd_app_id';
        $enrolColumn = $hostel ? 'sshd_sai_enrollment_no' : 'ssd_sai_enrollment_no';
        $enrollment = strtoupper(trim((string) $enrollment));

        return $query->where(function ($w) use ($aaiId, $enrollment, $appColumn, $enrolColumn) {
            $w->where($appColumn, $aaiId);
            if ($enrollment !== '') {
                $w->orWhereRaw("UPPER(TRIM({$enrolColumn})) = ?", [$enrollment]);
            }
        });
    }

    /** Mirrors StudentRecordRevisedController::findLiveChallan(). */
    protected function findLiveChallanBulk(int $aaiId, ?string $enrollment, int $sessionId, string $category)
    {
        $q = DB::table('student_challan')
            ->where('SC_SESSION_ID', $sessionId)
            ->where(function ($w) use ($aaiId, $enrollment) {
                $w->where('SC_APP_ID', $aaiId);
                if (!empty($enrollment)) {
                    $w->orWhereRaw('UPPER(TRIM(SC_ENROLLMENT_NO)) = ?', [strtoupper(trim($enrollment))]);
                }
            })
            ->whereRaw("UPPER(TRIM(NVL(SC_CHALLAN_STATUS, 'X'))) NOT IN ('CANCELLED', 'CANCEL FOR REISSUANCE')");

        if ($category === 'Hostel_Fee') {
            $q->where('SC_CHALLAN_TYPE', 'Hostel_Fee')
                ->whereIn('SC_HOSTEL_FEE_TYPE', ['Hostel_Full_semster', 'Hostel_day_wise']);
        } elseif ($category === 'additional_Full_semester') {
            $q->where('SC_CHALLAN_TYPE', 'Full_semester')->whereRaw('NVL(SC_ENROLLED_CR_HR, 0) > 0');
        } else {
            $q->where('SC_CHALLAN_TYPE', $category);
        }

        return $q->orderByDesc('id')->first();
    }

    /** Mirrors StudentRecordRevisedController::captureHostelChallanCarry(). */
    protected function captureHostelCarryBulk(int $aaiId, ?string $enrollment, int $sessionId): array
    {
        $challan = $this->findLiveChallanBulk($aaiId, $enrollment, $sessionId, 'Hostel_Fee');
        if (empty($challan)) {
            return [];
        }

        $pick = function ($column) use ($challan) {
            $value = $challan->{$column} ?? $challan->{strtoupper($column)} ?? null;

            return ($value !== null && $value !== '') ? $value : null;
        };

        return [
            'SSHD_HOSTEL_FEE_TYPE' => $pick('sc_hostel_fee_type'),
            'SSHD_HOSTEL_CATEGORY' => $pick('sc_hostel_category'),
            'SSHD_HOSTEL_DAYS' => $pick('sc_hostel_days'),
            'SSHD_SESSION_TYPE' => $pick('sc_session_type'),
            'SSHD_REFRIGERATOR' => $pick('sc_refrigerator'),
            'SSHD_CONDITIONER' => $pick('sc_conditioner'),
            'SSHD_ELECTRIC' => $pick('sc_electric'),
        ];
    }

    /**
     * Mirrors StudentRecordRevisedController::resolveChallanDates() — which is now a
     * one-line delegation to the same shared resolver this calls, so the two pages and
     * the two preview renderers all answer identically.
     *
     * The inheritance rules this used to apply (the live challan's dates, then the
     * cancelled challan's) are gone deliberately: chalanfinBulkPreview.php consults
     * neither, so honouring them here meant approving a document showing one deadline
     * and writing another. It also no longer restricts the lookup to 'Existing'
     * students, which silently put every applicant on today + 7.
     *
     * $studentType and $existing are kept for the call sites but are not consulted; the
     * preview has neither, so neither may influence the answer.
     */
    protected function resolveChallanDatesBulk(int $aaiId, ?string $enrollment, int $sessionId, string $studentType, $existing): array
    {
        return $this->challanDatesLikePreview($sessionId, $enrollment, $aaiId);
    }

    protected function lastCancelledChallanDatesBulk(int $aaiId, ?string $enrollment, int $sessionId): ?array
    {
        try {
            $row = DB::table('fin_student_cancel_challan')
                ->select('sc_due_date', 'sc_valid_date')
                ->where('sc_session_id', $sessionId)
                ->where(function ($q) use ($aaiId, $enrollment) {
                    $q->where('sc_app_id', $aaiId);
                    if (!empty($enrollment)) {
                        $q->orWhereRaw('UPPER(TRIM(sc_enrollment_no)) = ?', [strtoupper(trim($enrollment))]);
                    }
                })
                ->whereNotNull('sc_due_date')
                ->whereNotNull('sc_valid_date')
                ->orderByDesc('challan_cancelled_datetime')
                ->first();
        } catch (\Throwable $e) {
            return null;
        }

        return $row ? ['due' => $row->sc_due_date, 'valid' => $row->sc_valid_date] : null;
    }

    /** Mirrors StudentRecordRevisedController::resolveStudentForChallan(). */
    protected function resolveStudentForChallanBulk(string $studentType, ?string $enrollment, int $aaiId, int $sessionId): array
    {
        $sessionName = DB::table('academic_sessions')->where('sess_id', $sessionId)->value('sess_name');

        $row = null;
        if ($this->isEnrolledStudentType($studentType) && !empty($enrollment)) {
            $row = DB::table('students_apply_info as sai')
                ->leftJoin('students_personal_info as spi', 'sai.sai_spi_id', '=', 'spi.spi_id')
                ->leftJoin('schools as s', 'sai.sai_sdpo_school_id', '=', 's.school_id')
                ->leftJoin('departments as d', 'sai.sai_sdpo_dept_id', '=', 'd.dept_id')
                ->leftJoin('programs as p', 'sai.sai_sdpo_prog_id', '=', 'p.prog_id')
                ->whereRaw('LOWER(sai.sai_enrollment_no) = ?', [strtolower($enrollment)])
                ->select(
                    'spi.spi_name as name',
                    'spi.spi_fathername as father',
                    'spi.spi_guardian_cnic as guardian_cnic',
                    'sai.sai_enrollment_no as enrollment',
                    'sai.sai_student_status as student_status',
                    's.school_name', 'd.dept_name', 'p.prog_name'
                )
                ->first();
        }

        if (empty($row)) {
            $row = DB::table('admission_apply_info as aai')
                ->leftJoin('admission_personal_info as api', 'aai.aai_api_id', '=', 'api.api_id')
                ->leftJoin('schools as s', 'aai.aai_sdpo_school_id', '=', 's.school_id')
                ->leftJoin('departments as d', 'aai.aai_sdpo_dept_id', '=', 'd.dept_id')
                ->leftJoin('programs as p', 'aai.aai_sdpo_prog_id', '=', 'p.prog_id')
                ->where('aai.aai_id', $aaiId)
                ->select(
                    'api.api_name as name',
                    'api.api_fathername as father',
                    'api.api_guardian_cnic as guardian_cnic',
                    'aai.cms_sai_enrollment_no as enrollment',
                    DB::raw("'Active' as student_status"),
                    's.school_name', 'd.dept_name', 'p.prog_name'
                )
                ->first();
        }

        return [
            'name' => ($row && $row->name) ? $row->name : '',
            'father' => ($row && $row->father) ? $row->father : '',
            'guardian_cnic' => ($row && $row->guardian_cnic) ? $row->guardian_cnic : '',
            'enrollment' => ($row && $row->enrollment) ? $row->enrollment : $enrollment,
            'app_id' => $aaiId,
            'programme' => ($row && $row->prog_name) ? $row->prog_name : '',
            'school' => ($row && $row->school_name) ? $row->school_name : '',
            'department' => ($row && $row->dept_name) ? $row->dept_name : '',
            'session_name' => $sessionName ?: '',
            'status' => ($row && $row->student_status) ? $row->student_status : 'Active',
            'admission_round' => $this->resolveAdmissionRoundTextBulk($aaiId),
        ];
    }

    /** ADMISSION_SCH_ROUND 1/2/3 -> Early/Regular/Rolling, as the procedures did. */
    protected function resolveAdmissionRoundTextBulk(int $aaiId): ?string
    {
        $round = DB::table('admission_apply_info as aai')
            ->leftJoin('admission_schedules as sch', 'sch.admission_sch_id', '=', 'aai.aai_admission_sch_id')
            ->where('aai.aai_id', $aaiId)
            ->value('sch.admission_sch_round');

        $map = ['1' => 'Early', '2' => 'Regular', '3' => 'Rolling'];

        return $map[(string) $round] ?? null;
    }
}
