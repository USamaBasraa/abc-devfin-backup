<?php

namespace App\Services\StudentRecord\ChallanPosting;

/** Outcome of posting one challan. */
class PostResult
{
    /** @var bool */
    public $ok = false;

    /** @var int|null student_challan.ID */
    public $challanId;

    /** @var int|null student_challan.CHALLAN_NO */
    public $challanNo;

    /** @var int how many old challans were cancelled */
    public $cancelled = 0;

    /** @var string human-readable summary, surfaced to the grader */
    public $message = '';

    /** @var string[] non-fatal problems — e.g. a scholarship with no matching column */
    public $warnings = [];

    public static function success($challanId, $challanNo, $cancelled, $message, array $warnings = [])
    {
        $r = new self();
        $r->ok = true;
        $r->challanId = $challanId;
        $r->challanNo = $challanNo;
        $r->cancelled = $cancelled;
        $r->message = $message;
        $r->warnings = $warnings;

        return $r;
    }

    public static function failure($message)
    {
        $r = new self();
        $r->ok = false;
        $r->message = $message;

        return $r;
    }
}
