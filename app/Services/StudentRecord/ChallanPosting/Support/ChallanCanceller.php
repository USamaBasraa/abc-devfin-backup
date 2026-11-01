<?php

namespace App\Services\StudentRecord\ChallanPosting\Support;

use DB;

/**
 * The one Oracle procedure still used: fin_bulk_challan_cancellation.
 *
 * Everything else the FIN_SCH_* procedures did is now PHP, but cancellation is kept
 * because it is what moves a row out of student_challan into fin_student_cancel_challan
 * and there is no PHP equivalent.
 *
 * Parameter spelling follows the procedures themselves — FIN_SCH_FULL_SEMESTER calls
 * it with P_SC_CHALLAN_ID => rec.id, i.e. student_challan.ID (not CHALLAN_NO). Note
 * StudentRecordController::cancelBulkChallan() uses a different spelling
 * (challan_no / enrollemnt_no / app_id) for the same procedure; oci8 binds by NAME,
 * so only one can be correct and the procedure source settles it.
 */
class ChallanCanceller
{
    /**
     * Cancel every challan matching $criteria.
     *
     * @param  callable  $criteria  applied to a student_challan query builder
     * @return array{count:int, ids:int[]}
     */
    public function cancelWhere($aaiId, $sessionId, $enrollment, callable $criteria, $entryUser, $remarks)
    {
        $query = DB::table('student_challan')
            ->select('id', 'challan_no', 'sc_enrollment_no', 'sc_app_id')
            ->where('sc_app_id', $aaiId)
            ->where('sc_session_id', $sessionId)
            // A cancelled row must never be cancelled twice.
            ->whereRaw("UPPER(TRIM(NVL(sc_challan_status, 'X'))) NOT IN ('CANCELLED', 'CANCEL FOR REISSUANCE')");

        $criteria($query);

        $rows = $query->orderBy('id')->get();
        $ids = [];

        foreach ($rows as $row) {
            DB::executeProcedure('fin_bulk_challan_cancellation', [
                'P_SC_CHALLAN_ID'  => $row->id,
                'P_ENROLLMENT_NO'  => ctype_digit((string) $row->sc_enrollment_no) ? null : $row->sc_enrollment_no,
                'P_SC_APP_D'       => $row->sc_app_id,
                'P_CANCELLED_USER' => $entryUser,
                'P_REMARKS'        => $remarks,
            ]);
            $ids[] = (int) $row->id;
        }

        return ['count' => count($ids), 'ids' => $ids];
    }

    /** Does a PAID challan match these criteria? Used to block the reissue case. */
    public function hasPaid($aaiId, $sessionId, callable $criteria)
    {
        $query = DB::table('student_challan')
            ->where('sc_app_id', $aaiId)
            ->where('sc_session_id', $sessionId)
            ->whereRaw("UPPER(TRIM(NVL(sc_challan_status, 'UNPAID'))) = 'PAID'");

        $criteria($query);

        return $query->exists();
    }
}
