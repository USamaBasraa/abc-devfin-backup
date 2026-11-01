<?php

namespace App\Services\StudentRecord\ChallanPosting;

/**
 * Everything a poster needs for one challan, resolved once by the caller.
 *
 * The staged rows are the frozen snapshot the grader previewed and accepted; posters
 * read amounts from them and never recompute, so the posted challan is the document
 * that was approved.
 */
class PostContext
{
    /** @var int */
    public $aaiId;

    /** @var string|null enrollment no, or null for a new admission (numeric id) */
    public $enrollment;

    /** @var string the raw form value — enrollment no OR aai id */
    public $enrollmentRaw;

    /** @var int */
    public $sessionId;

    /** @var string Existing | New Admission FALL | … */
    public $studentType;

    /** @var string Full_semester | Credit_hours | additional_Full_semester | Short/Summer_Course | Hostel_Fee */
    public $category;

    /** @var \Illuminate\Support\Collection staged SSD or SSHD rows */
    public $rows;

    /** @var array name / father / guardian_cnic / school / department / programme … */
    public $student;

    /** @var string */
    public $dueDate;

    /** @var string */
    public $validDate;

    /** @var string */
    public $entryUser;

    /** @var int|null */
    public $entryUserId;

    /** @var string tags the challan and the staged rows, e.g. RECORD_REVISED */
    public $sourcePage;

    /** @var object|null the live challan being replaced, when there is one */
    public $existingChallan;

    public function __construct(array $attributes = [])
    {
        foreach ($attributes as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
    }

    public function isHostel()
    {
        return $this->category === 'Hostel_Fee';
    }

    /**
     * The row that speaks for the challan.
     *
     * The per-challan figures (TOTAL_DISC_AMT, TOTAL_PAYABLE, the *_ACTUAL fees) are
     * written identically onto EVERY scholarship row of the group by
     * refreshScholarshipSnapshot(), so any stamped row will do. "Any stamped row" is the
     * load-bearing part: rows created by pages that write only a percentage — the merit
     * entry and approval flows — carry NULL in those columns, and this used to return
     * rows[0] blindly. A student holding two awards whose unstamped one had the lower
     * ssd_id therefore posted with SC_SCHOLARSHIP_AMOUNT = 0 and INVOICEAMOUNT = the full
     * fee: every scholarship silently dropped off the issued challan.
     *
     * So: prefer a row that actually carries the figures. TOTAL_FEE_ACTUAL is the test
     * because it is non-zero on every priced challan and absent on an unstamped row, and
     * an unpriced challan is already blocked upstream. Falling back to rows[0] keeps the
     * old behaviour when nothing is stamped, so a genuinely unpriced group still reads as
     * zero rather than as no row at all.
     */
    public function head()
    {
        if (!$this->rows || $this->rows->isEmpty()) {
            return null;
        }

        $column = $this->isHostel() ? 'sshd_total_fee_actual' : 'ssd_total_fee_actual';

        foreach ($this->rows as $row) {
            $value = $row->{$column} ?? $row->{strtoupper($column)} ?? null;
            if ($value !== null && (float) $value > 0) {
                return $row;
            }
        }

        return $this->rows[0];
    }

    /** Read a numeric column off the head row. */
    public function num($column)
    {
        $head = $this->head();

        return ($head && isset($head->{$column})) ? (float) $head->{$column} : 0.0;
    }

    /** Read a column off the head row, with a fallback. */
    public function val($column, $default = null)
    {
        $head = $this->head();

        return ($head && isset($head->{$column}) && $head->{$column} !== '') ? $head->{$column} : $default;
    }
}
