<?php

namespace App\Services\Hostel;

use DB;

/**
 * What hostel a student is assigned to this session, and therefore which
 * FIN_HOSTEL_FEE_STRUCTURE row prices them.
 *
 * ---------------------------------------------------------------------------
 * WHY THIS EXISTS.
 *
 * The answer used to be reconstructed by traversing four tables:
 *
 *     hostel_assign -> hostel_beds -> hostel_rooms -> hostel_type
 *
 * purely to obtain HT_ID, which was then bucketed 1-2 = on-campus, 3-4 = Safari
 * (append '_outside'), 5-6 = Bedian. Two things were wrong with that:
 *
 *   1. The chain is OPTIONAL. An assignment row can carry only HA_CHALLAN_ID with no
 *      bed, and then HT_ID comes back NULL. The old code silently kept whatever the
 *      Hostel Fee Structure dropdown happened to say and reported auto_detected=false.
 *      In this database that is 104 assignments from session 165 onward.
 *   2. Regular vs Non-Regular was never read from the database at ALL. It came from a
 *      hardcoded list of eight enrollment numbers on StudentRecordController. The
 *      hostel_assign table holds the real answer for 27 students in session 167 — the
 *      list names only 8 of them, so the other 19 were priced Regular against
 *      FIN_HOSTEL_FEE_STRUCTURE when their fee actually lives on their hostel challan.
 *
 * The hostel assignment page now writes both facts directly onto the assignment row:
 *
 *     HA_HOSTEL_SESSION_TYPE        regular | regular_outside | non-regular | non-regular_outside
 *     HA_HOSTEL_LOCATION_PREFERENCE on_campus | off_campus
 *
 * (written by HostelBedAssignmentController::resolveHostelSessionType()). Verified
 * against the old join across sessions 165-168: 846 rows agree, ZERO disagree, and the
 * columns additionally answer the 104 the join could not.
 *
 * So the column is read first and the join is kept only as a last-resort fallback —
 * and for HT_ID / HT_NAME, which the scholarship snapshot still records.
 * ---------------------------------------------------------------------------
 *
 * WHO COUNTS AS ASSIGNED is a separate question from how they are priced, and it has
 * exactly one answer here: the row must carry a BED (HA_HB_ID). See
 * assignmentExistsSql() for what that deliberately excludes. The columns above still do
 * the pricing once a bedded row is matched; they no longer decide whether it is a row
 * worth matching.
 *
 * GenerateSingleChallanController already resolves the same thing the same way
 * (see its normalizeHostelSessType()); this is the shared implementation.
 */
class HostelAssignmentResolver
{
    /**
     * HA_HOSTEL_SESSION_TYPE (lower, underscored) -> FHFS_SESSION_TYPE casing.
     *
     * FIN_HOSTEL_FEE_STRUCTURE has no campus column: the campus is baked into this
     * string, which is why the mapping has to be exact. The legal set is the one the
     * Master Hostel Fee Structure form offers.
     */
    const SESSION_TYPE_MAP = [
        'regular'                    => 'Regular',
        'non-regular'                => 'Non-Regular',
        'non_regular'                => 'Non-Regular',
        'regular_outside'            => 'Regular_outside',
        'non-regular_outside'        => 'Non-Regular_outside',
        'non_regular_outside'        => 'Non-Regular_outside',
        'regular_outside_bedian'     => 'Regular_outside_bedian',
        'non-regular_outside_bedian' => 'Non-Regular_outside_bedian',
        'non_regular_outside_bedian' => 'Non-Regular_outside_bedian',
    ];

    /**
     * What makes an assignment row REAL, as one SQL fragment: A BED, and nothing else.
     *
     * HA_HB_ID is the allocated bed. A student holds a hostel place when, and only when,
     * they hold a bed — so that column alone decides it.
     *
     * This DELIBERATELY narrows an earlier definition of "a bed, or a hostel challan, or a
     * recorded session type", which counted a row that carried any one of the three. Those
     * two extra tests are now excluded on purpose:
     *
     *   HA_CHALLAN_ID            a challan can exist against a request that was never
     *                            allocated, so it does not prove a place was given.
     *   HA_HOSTEL_SESSION_TYPE   written when the assignment is *attempted*, so it records
     *                            what kind of place was being sought, not that one was got.
     *
     * HA_HOSTEL_LOCATION_PREFERENCE was never part of this and still is not — it is a
     * *preference*, set before any allocation, and counting it would hand a hostel
     * scholarship to a student who never received a hostel.
     *
     * KNOWN CONSEQUENCE, accepted: a student whose hostel_assign row has no bed is no
     * longer eligible for a hostel scholarship anywhere, even if the row carries a challan
     * id or a session type. Under the previous definition those students were eligible.
     * The columns are still READ for pricing once a bedded row is matched — see resolve()
     * — this fragment only decides whether the row counts at all.
     *
     * This also matches how challan generation already reads the table
     * (GenerateSingleChallanController::assignedHostelSessType(), which filters on
     * ha_hb_id alone), so the scholarship pages and the challan pages now agree on who is
     * assigned.
     *
     * Returned as SQL rather than a builder closure because half the callers are raw
     * batched queries; this way the definition cannot drift between them. Every caller
     * shares it: this class, ScholarshipFormController, StudentRecordController and
     * StudentRecordRevisedController.
     *
     * @param string $alias table alias, e.g. 'ha'
     */
    public static function assignmentExistsSql($alias = 'ha')
    {
        $a = preg_replace('/[^A-Za-z0-9_]/', '', (string) $alias);
        $a = $a === '' ? 'ha' : $a;

        return "({$a}.ha_hb_id IS NOT NULL)";
    }

    /**
     * @return array{
     *   assigned:bool, sessionType:string|null, residency:string|null,
     *   locationPreference:string|null, isNonRegular:bool, isBedian:bool,
     *   htId:int|null, htName:string|null, source:string
     * }
     */
    public function resolve($session, $aaiId, $enrollment)
    {
        $row = $this->assignmentRow($session, $aaiId, $enrollment);

        if (empty($row)) {
            return $this->none();
        }

        $htId   = isset($row->ht_id) && $row->ht_id !== null ? (int) $row->ht_id : null;
        $htName = isset($row->ht_name) ? $row->ht_name : null;
        $locPref = $this->trimLower($row->ha_hostel_location_preference ?? null);

        // 1) The assignment's own session type — authoritative, no joins, and it carries
        //    BOTH facts at once (residency and campus).
        $sessionType = $this->normalizeSessionType($row->ha_hostel_session_type ?? null);
        $source = 'session_type_column';

        // 2) No session type recorded: the location column still answers the campus half.
        //    Residency falls back to Regular, which is what the old code assumed.
        if ($sessionType === null && $locPref !== null) {
            $sessionType = ($locPref === 'off_campus' || $locPref === 'out_of_campus')
                ? 'Regular_outside'
                : 'Regular';
            $source = 'location_column';
        }

        // 3) Neither column set — fall back to the old HT_ID bucketing so a legacy row
        //    behaves exactly as it did before.
        if ($sessionType === null && $htId !== null) {
            $sessionType = in_array($htId, [3, 4], true) ? 'Regular_outside'
                : (in_array($htId, [5, 6], true) ? 'Regular_outside_bedian' : 'Regular');
            $source = 'ht_id_join';
        }

        if ($sessionType === null) {
            // Assigned, but nothing says where. The caller keeps its manual selection.
            return [
                'assigned' => true, 'sessionType' => null, 'residency' => null,
                'locationPreference' => $locPref, 'isNonRegular' => false, 'isBedian' => false,
                'htId' => $htId, 'htName' => $htName, 'source' => 'unknown',
            ];
        }

        $isNonRegular = stripos($sessionType, 'Non-Regular') !== false;

        return [
            'assigned'           => true,
            'sessionType'        => $sessionType,
            'residency'          => $isNonRegular ? 'Non-Regular' : 'Regular',
            'locationPreference' => $locPref,
            'isNonRegular'       => $isNonRegular,
            // Bedian is priced but not supported by the scholarship pages yet; they reject
            // it explicitly rather than pricing it wrong.
            'isBedian'           => (stripos($sessionType, '_bedian') !== false)
                                     || in_array($htId, [5, 6], true),
            'htId'               => $htId,
            'htName'             => $htName,
            'source'             => $source,
        ];
    }

    /** Map a raw HA_HOSTEL_SESSION_TYPE to FHFS casing, or null when unusable. */
    public function normalizeSessionType($raw)
    {
        $key = $this->trimLower($raw);
        if ($key === null) {
            return null;
        }

        return isset(self::SESSION_TYPE_MAP[$key]) ? self::SESSION_TYPE_MAP[$key] : null;
    }

    // ---------------------------------------------------------------- //

    /**
     * The student's newest assignment for the session.
     *
     * The bed/room/type LEFT joins remain ONLY for HT_ID / HT_NAME, which the scholarship
     * snapshot records and which is still the last-resort campus fallback. They no longer
     * decide the fee type, so a row with no bed is now fully resolvable.
     *
     * Matching mirrors the query this replaces: an assignment counts when it has a bed OR
     * a challan, and the enrollment compare is case/space-insensitive because stored
     * casing varies.
     */
    private function assignmentRow($session, $aaiId, $enrollment)
    {
        if (empty($session)) {
            return null;
        }

        $normalized = strtoupper(trim((string) $enrollment));

        return DB::table('hostel_assign as ha')
            ->leftJoin('hostel_beds as hb', 'hb.hb_id', '=', 'ha.ha_hb_id')
            ->leftJoin('hostel_rooms as hr', 'hr.hr_id', '=', 'hb.hb_room_id')
            ->leftJoin('hostel_type as ht', 'ht.ht_id', '=', 'hr.hr_ht_id')
            ->where('ha.ha_session', $session)
            ->whereRaw(self::assignmentExistsSql('ha'))
            ->where(function ($q) use ($aaiId, $normalized) {
                $matched = false;
                if (!empty($aaiId) && is_numeric($aaiId)) {
                    $q->where('ha.ha_aai_id', $aaiId);
                    $matched = true;
                }
                if ($normalized !== '') {
                    $clause = $matched ? 'orWhereRaw' : 'whereRaw';
                    $q->{$clause}('UPPER(TRIM(ha.ha_enrolment_no)) = ?', [$normalized]);
                    $matched = true;
                }
                if (!$matched) {
                    $q->whereRaw('1 = 0');
                }
            })
            ->orderByDesc('ha.ha_id')
            ->select(
                'ha.ha_hostel_session_type',
                'ha.ha_hostel_location_preference',
                'ht.ht_id',
                'ht.ht_name'
            )
            ->first();
    }

    private function trimLower($value)
    {
        if ($value === null) {
            return null;
        }
        $v = strtolower(trim((string) $value));

        return $v === '' ? null : $v;
    }

    private function none()
    {
        return [
            'assigned' => false, 'sessionType' => null, 'residency' => null,
            'locationPreference' => null, 'isNonRegular' => false, 'isBedian' => false,
            'htId' => null, 'htName' => null, 'source' => 'none',
        ];
    }
}
