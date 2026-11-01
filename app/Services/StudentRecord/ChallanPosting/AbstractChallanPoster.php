<?php

namespace App\Services\StudentRecord\ChallanPosting;

use App\Services\StudentRecord\ChallanPosting\Support\ChallanCanceller;
use App\Services\StudentRecord\ChallanPosting\Support\ChallanSchema;
use App\Services\StudentRecord\ChallanPosting\Support\ScholarshipLedgerWriter;
use Carbon\Carbon;
use DB;

/**
 * The shared challan-posting flow, replacing what the FIN_SCH_* procedures did:
 *
 *   1. build a row from the frozen snapshot
 *   2. insert it
 *   3. write the scholarship ledger tables
 *
 * Cancellation is NOT here — it happens on Save, so a saved snapshot can never sit
 * alongside a live challan that disagrees with it. cancellationCriteria() still defines
 * which challan each category owns; Save is simply what applies it.
 *
 * Subclasses override only what differs per category — which challans to cancel,
 * what challan type to write, and the fee columns. That is the whole point of the
 * split: changing the credit-hour rules cannot disturb hostel or full-semester.
 *
 * Scholarships live ROW-WISE and nowhere else: one row per scholarship in
 * STUDENTS_SCHOLARSHIPS_DETAILS / FIN_STUDENTS_SCHOLARSHIPS_HOSTEL_DETAILS, fanned out
 * by ledgerRows() into fin_scholarship_discounts and fin_scholarship_display_history —
 * the two tables the printed challan actually reads. student_challan receives only the
 * TOTAL, in SC_SCHOLARSHIP_AMOUNT. There is deliberately no per-scholarship column on
 * the challan; a report needing that shape derives it from the ledger, as
 * StudentFineController does for the middleware export.
 */
abstract class AbstractChallanPoster
{
    /** @var ChallanCanceller */
    protected $canceller;

    /** @var ScholarshipLedgerWriter */
    protected $ledger;

    public function __construct(
        ChallanCanceller $canceller = null,
        ScholarshipLedgerWriter $ledger = null
    ) {
        $this->canceller = $canceller ?: new ChallanCanceller();
        $this->ledger = $ledger ?: new ScholarshipLedgerWriter();
    }

    // ── per-category hooks ───────────────────────────────────────────────

    /** The SC_CHALLAN_TYPE written. Not always the same as the category. */
    abstract public function challanType();

    /** Narrows a student_challan query to the rows THIS category owns. */
    abstract public function cancellationCriteria();

    /** Fee columns for this category, taken from the snapshot. */
    abstract protected function feeColumns(PostContext $ctx);

    /** Human label used in messages. */
    abstract public function label();

    /** Refuse to post — return a reason, or null to proceed. */
    protected function blockedReason(PostContext $ctx)
    {
        return null;
    }

    /** Category-specific extras (hostel type, days, …). */
    protected function extraColumns(PostContext $ctx)
    {
        return [];
    }

    // ── the flow ─────────────────────────────────────────────────────────

    public function post(PostContext $ctx)
    {
        if ($ctx->rows === null || $ctx->rows->isEmpty()) {
            return PostResult::failure('Nothing staged for ' . $this->label() . '.');
        }

        $blocked = $this->blockedReason($ctx);
        if ($blocked !== null) {
            return PostResult::failure($blocked);
        }

        // 1. build
        //
        // No cancellation here any more. Save retires the existing challan, so that a
        // saved snapshot can never sit alongside a live challan saying something else.
        // By the time Post runs there is nothing left to cancel — cancellationCriteria()
        // is still the authority on WHICH challan a category owns, but Save is what
        // applies it.
        $row = array_merge(
            $this->commonColumns($ctx),
            $this->feeColumns($ctx),
            $this->extraColumns($ctx)
        );

        // 2. insert
        $challanId = $this->insert($row, $ctx);

        // Without an id there is no challan to attach the ledger or the surplus to, and
        // both steps below would skip silently while this still reported success — the
        // grader would be told a challan was created that nothing can find.
        if ($challanId === null) {
            return PostResult::failure(
                'The ' . $this->label() . ' challan was inserted but the database returned no '
                . 'id for it, so nothing has been committed. student_challan.ID is assigned by '
                . 'a BEFORE INSERT trigger on the table; check that the trigger exists and is '
                . 'ENABLED (SELECT trigger_name, status FROM user_triggers WHERE table_name = '
                . "'STUDENT_CHALLAN')."
            );
        }

        $challanNo = $this->resolveChallanNo($challanId);

        // 3. ledger — what makes the printed challan show its "Less Scholarship" lines
        $this->ledger->write(
            $ctx->aaiId,
            $ctx->sessionId,
            $ctx->enrollment,
            $ctx->category,
            $this->ledgerRows($ctx),
            (int) $challanId,
            (int) $challanNo,
            $ctx->isHostel(),
            $ctx->entryUser
        );

        // The trigger may assign no CHALLAN_NO, in which case the id identifies the
        // document — the same fallback chalanfinPreview.php makes when it prints it.
        $message = 'Created the ' . $this->label() . ' challan (#' . ($challanNo ?: $challanId) . ').';

        return PostResult::success($challanId, $challanNo, 0, $message, $this->warnings($ctx));
    }

    /**
     * Non-fatal things the grader should know about this challan.
     *
     * Overridable so a category can report its own; ShortSummerPoster uses it to name
     * courses that had no fee structure and were therefore priced at 0.
     *
     * @return string[]
     */
    protected function warnings(PostContext $ctx)
    {
        return [];
    }

    // ── shared pieces ────────────────────────────────────────────────────

    /**
     * Identity and operational columns — the same for every category.
     * Values come from the student tables and the snapshot, never from a challan.
     */
    protected function commonColumns(PostContext $ctx)
    {
        $now = Carbon::now('Asia/Karachi');
        $prefix = $ctx->isHostel() ? 'sshd' : 'ssd';

        return [
            'SC_APP_ID'              => $ctx->aaiId,
            'SC_ENROLLMENT_NO'       => $ctx->enrollment,
            'SC_SESSION_ID'          => $ctx->sessionId,
            'SC_CHALLAN_TYPE'        => $this->challanType(),
            'SC_SCHOOL_ID'           => $ctx->num($prefix . '_school_id') ?: null,
            'SC_DEPARTMENT_ID'       => $ctx->num($prefix . '_dept_id') ?: null,
            'SC_PROGRAM_ID'          => $ctx->num($prefix . '_prog_id') ?: null,
            'SC_STUDENTNAME'         => $ctx->student['name'],
            'SC_FATHERNAME'          => $ctx->student['father'],
            'SC_GUARDIAN_CNIC'       => $ctx->student['guardian_cnic'],
            'SC_SAI_STUDENT_STATUS'  => $ctx->student['status'] ?: 'Active',
            'SC_STUDENT_STATUS'      => $ctx->val($prefix . '_student_type', $ctx->studentType),
            'SC_CHALLAN_STATUS'      => 'UNPAID',
            'INVOICE_STATUS'         => 'E',
            'FLAG'                   => 'F',
            'SC_SCH_APPROVAL_CHALLAN' => 'Pending',
            'SC_DUE_DATE'            => $ctx->dueDate,
            'SC_VALID_DATE'          => $ctx->validDate,
            'SC_ISSU_DATE'           => $now,
            'SC_VISIBLE_DATE'        => $now,
            'SC_ADMISSION_ROUND'     => $ctx->student['admission_round'],
            'SC_CHALLAN_SOURCE'      => $ctx->sourcePage,
            'SC_PRO_ENTRY_USERNAME'  => $ctx->entryUser,
            'SC_ENTRY_USER_ID'       => $ctx->entryUserId,
            'SC_ENTRY_USER_NAME'     => $ctx->entryUser,
            'CREATED_AT'             => $now,
            'UPDATED_AT'             => $now,
        ];
    }

    /** Rows for fin_scholarship_discounts / fin_scholarship_display_history. */
    protected function ledgerRows(PostContext $ctx)
    {
        $rows = [];
        foreach ($ctx->rows as $row) {
            $isHostel = $ctx->isHostel();
            $type = (int) ($isHostel ? $row->sshd_type : $row->ssd_type);
            $amount = (float) ($isHostel
                ? (isset($row->sshd_amount) ? $row->sshd_amount : 0)
                : (isset($row->ssd_amount) ? $row->ssd_amount : 0));
            $arrear = (float) ($isHostel
                ? (isset($row->sshd_arrear) ? $row->sshd_arrear : 0)
                : (isset($row->ssd_arrear) ? $row->ssd_arrear : 0));

            // An arrears-only head has no tuition waiver, so amount is 0 while arrear
            // is not. Dropping it here would lose FSD_ARREARS and with it the record of
            // which scholarship head funded the waiver — the procedures keep such rows
            // (their cursor reads SSD with ssd_percentage = 0, ssd_arrear > 0).
            if ($type <= 0 || ($amount <= 0 && $arrear <= 0)) {
                continue;
            }

            $rows[] = [
                'type'       => $type,
                'percentage' => (float) ($isHostel ? $row->sshd_percentage : $row->ssd_percentage),
                'amount'     => $amount,
                'arrear'     => $arrear,
            ];
        }

        return $rows;
    }

    /**
     * student_challan.ID is assigned by the DATABASE. It is never chosen here.
     *
     * A BEFORE INSERT trigger on the table owns it — which is why every page that has
     * always written this table (GenerateSingleChallanController, StudentFineController,
     * StudentChallanDetailsController) inserts no ID and still gets one back. Pulling
     * NEXTVAL from PHP and writing ID ourselves would put a second writer on a value the
     * database already owns: two consumers of one sequence, numbers burned on rows the
     * trigger then overwrites, and no single place that decides what a challan's id is.
     *
     * So the id is READ BACK, not assigned:
     *
     *   1. Oracle's RETURNING clause, which hands back whatever the trigger assigned.
     *
     *   2. Re-finding the row by its identity columns, if the driver returned nothing.
     *      Last resort only: it cannot tell our row from an identical earlier one, and
     *      it depends on SC_CHALLAN_SOURCE existing — a column ChallanSchema::filter
     *      quietly drops from the insert when its DDL was never applied.
     *
     * Both go through readId() because the driver's column casing is not guaranteed,
     * and a case mismatch here would read as "no id" rather than as an error.
     */
    protected function insert(array $row, PostContext $ctx)
    {
        $row = ChallanSchema::filter('student_challan', $row);

        // 1. trigger-assigned ID, read back through RETURNING
        $returned = DB::table('student_challan')->insertGetId($row);
        if ((int) $returned > 0) {
            return (int) $returned;
        }

        // 2. re-find the row
        $created = DB::table('student_challan')
            ->where('SC_APP_ID', $ctx->aaiId)
            ->where('SC_SESSION_ID', $ctx->sessionId)
            ->where('SC_CHALLAN_TYPE', $this->challanType())
            ->whereRaw("UPPER(TRIM(NVL(SC_CHALLAN_SOURCE, ''))) = ?", [strtoupper($ctx->sourcePage)])
            ->orderByDesc('id')
            ->first();

        return $this->readId($created);
    }

    /**
     * The challan number the database assigned, or null when it assigned none.
     *
     * CHALLAN_NO belongs to the same trigger that fills ID, so this only reads it. It
     * used to backfill the column with the raw id whenever it read empty, which is an
     * application deciding a challan's number — and worse, a case mismatch on the read
     * was enough to make it overwrite a number the trigger had already assigned.
     *
     * Returning null is not a failure: the ledger falls back to the id for its
     * CHALLAN_ID reference, and chalanfinPreview.php already prints CHALLAN_NO when set
     * and the id otherwise. Nothing downstream needs a number invented for it.
     */
    protected function resolveChallanNo($challanId)
    {
        if ($challanId === null) {
            return null;
        }

        $row = DB::table('student_challan')->where('id', $challanId)->first();

        return $this->readId($row, 'challan_no');
    }

    /**
     * Read an id off a row, whatever case the driver returned the column in.
     *
     * yajra/laravel-oci8 normally lower-cases column names, but that is a driver
     * setting rather than a guarantee, and everything above treats a missing id as a
     * hard stop — so it must not be possible to lose one to casing alone.
     *
     * @return int|null
     */
    protected function readId($row, $column = 'id')
    {
        if (!is_object($row)) {
            return null;
        }

        foreach ([$column, strtoupper($column)] as $candidate) {
            if (isset($row->{$candidate}) && (int) $row->{$candidate} > 0) {
                return (int) $row->{$candidate};
            }
        }

        return null;
    }
}
