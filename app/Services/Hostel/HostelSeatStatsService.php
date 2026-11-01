<?php

namespace App\Services\Hostel;

use Illuminate\Support\Facades\DB;

/**
 * Single source of truth for session-wise hostel seat statistics.
 *
 * Available  = active beds − beds assigned in this session (ha_hb_id set)
 * Allowed    = hostel_assign rows for session, allowed, no bed yet (ha_hb_id null)
 * Unpaid/Paid = pipeline rows with challan status
 * Remaining  = Available − Allowed (per location)
 *
 * Dashboard allowance stats are session-wide (Applicants + Students) so both
 * pages show the same Available / Unpaid / Paid / Remaining numbers.
 */
class HostelSeatStatsService
{
    public function getVacantBedsByGender(int $sessionId, ?string $location = null): array
    {
        return [
            'male_vacant_seats' => $this->getVacantBedsForGender($sessionId, 'male', $location),
            'female_vacant_seats' => $this->getVacantBedsForGender($sessionId, 'female', $location),
        ];
    }

    public function getVacantBedsForGender(int $sessionId, string $gender, ?string $location = null): int
    {
        $genderType = $this->genderType($gender);

        $totalBedsQuery = DB::table('hostel_beds')
            ->join('hostel_rooms', 'hostel_beds.hb_room_id', '=', 'hostel_rooms.hr_id')
            ->join('hostel_floor', 'hostel_rooms.hr_hf_id', '=', 'hostel_floor.hf_id')
            ->join('HOSTEL_TYPE as ht', 'hostel_rooms.hr_ht_id', '=', 'ht.ht_id')
            ->where('ht.ht_hostel_type', $genderType)
            ->where('ht.ht_status', 'E')
            ->where('hostel_rooms.hr_status', 'E')
            ->where('hostel_floor.hf_status', 'E');

        if ($location) {
            $this->applyHostelLocationFilter($totalBedsQuery, 'ht.ht_location', $location);
        }

        $totalBeds = (int) $totalBedsQuery->count('hostel_beds.hb_bed_number');

        $assignedBedsQuery = DB::table('hostel_assign as ha')
            ->join('hostel_beds as hb', 'ha.ha_hb_id', '=', 'hb.hb_id')
            ->join('hostel_rooms as hr', 'hb.hb_room_id', '=', 'hr.hr_id')
            ->join('hostel_floor as hf', 'hr.hr_hf_id', '=', 'hf.hf_id')
            ->join('HOSTEL_TYPE as ht', 'hr.hr_ht_id', '=', 'ht.ht_id')
            ->where('ha.ha_session', $sessionId)
            ->where('ha.ha_status', 'E')
            ->whereNotNull('ha.ha_hb_id')
            ->where('ht.ht_hostel_type', $genderType)
            ->where('ht.ht_status', 'E');

        if ($location) {
            $this->applyHostelLocationFilter($assignedBedsQuery, 'ht.ht_location', $location);
        }

        $assignedBeds = (int) $assignedBedsQuery->distinct()->count('ha.ha_hb_id');

        return max(0, $totalBeds - $assignedBeds);
    }

    public function getAllowanceStats(int $sessionId, string $gender, string $context = 'applicant'): array
    {
        $genderKey = strtolower($gender) === 'female' ? 'female' : 'male';
        $genderParam = ucfirst($genderKey);

        $allowanceStats = [
            'on_campus' => $this->emptyLocationStat('ON CAMPUS'),
            'off_campus' => $this->emptyLocationStat('OFF CAMPUS'),
        ];

        foreach (['on_campus', 'off_campus'] as $locKey) {
            $vacant = $this->getVacantBedsForGender($sessionId, $genderKey, $locKey);
            $allowanceStats[$locKey]['physical_vacancy'] = $vacant;
            $allowanceStats[$locKey]['available_seats'] = $vacant;
        }

        // Always session-wide so Applicants & Students Summary Stats match.
        foreach ($this->getPipelineRowsByLocation($sessionId, $genderParam, 'all') as $stat) {
            $pref = $this->normalizePipelineLocation($stat->location_preference ?? 'on_campus');
            if (!isset($allowanceStats[$pref])) {
                continue;
            }
            $allowanceStats[$pref]['allowed'] += (int) ($stat->unassigned_offers ?? 0);
            $allowanceStats[$pref]['unpaid'] += (int) ($stat->unpaid_offers ?? 0);
            $allowanceStats[$pref]['paid'] += (int) ($stat->paid_offers ?? 0);
        }

        foreach ($allowanceStats as $key => &$stat) {
            $stat['remaining'] = max(0, $stat['available_seats'] - $stat['allowed']);
        }
        unset($stat);

        return $allowanceStats;
    }

    public function getPipelineAllowedTotal(int $sessionId, string $gender, string $context = 'applicant'): int
    {
        $stats = $this->getAllowanceStats($sessionId, $gender, $context);

        return (int) $stats['on_campus']['allowed'] + (int) $stats['off_campus']['allowed'];
    }

    public function getPaidNotAssignedCount(int $sessionId, string $gender, string $context = 'applicant'): int
    {
        $genderKey = strtolower($gender) === 'female' ? 'female' : 'male';
        $genderParam = ucfirst($genderKey);

        // Session-wide (applicants + students), de-duplicated by ha_id.
        $row = DB::selectOne("
            SELECT COUNT(*) AS total FROM (
                SELECT ha.ha_id
                FROM hostel_assign ha
                JOIN admission_apply_info aai ON ha.ha_aai_id = aai.aai_id
                JOIN admission_personal_info api ON api.api_id = NVL(ha.ha_api_id, aai.aai_api_id)
                JOIN student_challan sc ON ha.ha_challan_id = sc.id
                WHERE ha.ha_session = ?
                  AND ha.ha_status = 'E'
                  AND ha.ha_hb_id IS NULL
                  AND ha.ha_aai_id IS NOT NULL
                  AND api.api_allow_hostel = 'Allowed'
                  AND UPPER(TRIM(api.api_gender)) = UPPER(?)
                  AND aai.aai_status = 'Selected'
                  AND sc.sc_challan_type = 'Hostel_Fee'
                  AND sc.sc_challan_status = 'PAID'

                UNION

                SELECT ha.ha_id
                FROM hostel_assign ha
                JOIN students_apply_info sai ON UPPER(ha.ha_enrolment_no) = UPPER(sai.sai_enrollment_no)
                JOIN students_personal_info spi ON sai.sai_spi_id = spi.spi_id
                JOIN hostel_applicants happ ON UPPER(happ.happ_enrollment_no) = UPPER(ha.ha_enrolment_no)
                    AND happ.happ_session = ha.ha_session
                    AND happ.happ_allowed_status = 'Allowed'
                JOIN student_challan sc ON ha.ha_challan_id = sc.id
                WHERE ha.ha_session = ?
                  AND ha.ha_status = 'E'
                  AND ha.ha_hb_id IS NULL
                  AND UPPER(TRIM(spi.spi_gender)) = UPPER(?)
                  AND sc.sc_challan_type = 'Hostel_Fee'
                  AND sc.sc_challan_status = 'PAID'
            ) paid_pipeline
        ", [$sessionId, $genderParam, $sessionId, $genderParam]);

        return (int) ($row->total ?? 0);
    }

    public function getGenderSeatSummary(int $sessionId, string $gender, string $context = 'applicant'): array
    {
        $genderKey = strtolower($gender) === 'female' ? 'female' : 'male';
        $vacant = $this->getVacantBedsByGender($sessionId);
        $vacantKey = $genderKey === 'female' ? 'female_vacant_seats' : 'male_vacant_seats';
        $vacantSeats = (int) ($vacant[$vacantKey] ?? 0);

        // Session-wide seat capacity (same on both pages).
        $paidNotAssigned = $this->getPaidNotAssignedCount($sessionId, $genderKey, 'all');
        $pipelineAllowed = $this->getPipelineAllowedTotal($sessionId, $genderKey, 'all');

        return [
            'vacant_seats' => $vacantSeats,
            'available_seats' => max(0, $vacantSeats - $paidNotAssigned),
            'allowed_count' => $pipelineAllowed,
            'remaining_seats' => max(0, $vacantSeats - $pipelineAllowed),
        ];
    }

    private function getPipelineRowsByLocation(int $sessionId, string $genderParam, string $context): array
    {
        if ($context === 'student') {
            return $this->runStudentPipelineQuery($sessionId, $genderParam);
        }

        if ($context === 'applicant') {
            return $this->runApplicantPipelineQuery($sessionId, $genderParam);
        }

        // Combined session-wide pipeline.
        // Deduplicate by ha_id; if a row matches both sources, prefer student
        // location preference (COALESCE ha + hostel_applicants) so off-campus
        // is not lost when ha preference is null.
        return DB::select("
            SELECT
                location_preference,
                COUNT(DISTINCT ha_id) AS total_offers,
                COUNT(DISTINCT CASE WHEN ha_hb_id IS NULL THEN ha_id END) AS unassigned_offers,
                COUNT(DISTINCT CASE WHEN ha_challan_id IS NOT NULL AND sc_challan_status = 'UNPAID' THEN ha_id END) AS unpaid_offers,
                COUNT(DISTINCT CASE WHEN sc_challan_status = 'PAID' THEN ha_id END) AS paid_offers
            FROM (
                SELECT
                    ha_id,
                    MAX(ha_hb_id) AS ha_hb_id,
                    MAX(ha_challan_id) AS ha_challan_id,
                    NVL(
                        MAX(CASE WHEN src = 2 THEN location_preference END),
                        MAX(CASE WHEN src = 1 THEN location_preference END)
                    ) AS location_preference,
                    MAX(sc_challan_status) AS sc_challan_status
                FROM (
                    SELECT
                        1 AS src,
                        ha.ha_id,
                        ha.ha_hb_id,
                        ha.ha_challan_id,
                        NVL(ha.ha_hostel_location_preference, 'on_campus') AS location_preference,
                        sc.sc_challan_status
                    FROM hostel_assign ha
                    JOIN admission_apply_info aai ON ha.ha_aai_id = aai.aai_id
                    JOIN admission_personal_info api ON api.api_id = NVL(ha.ha_api_id, aai.aai_api_id)
                    LEFT JOIN student_challan sc ON ha.ha_challan_id = sc.id
                    WHERE ha.ha_session = ?
                      AND ha.ha_status = 'E'
                      AND ha.ha_res_next_sess IS NULL
                      AND ha.ha_aai_id IS NOT NULL
                      AND api.api_allow_hostel = 'Allowed'
                      AND UPPER(TRIM(api.api_gender)) = UPPER(?)
                      AND aai.aai_status = 'Selected'

                    UNION ALL

                    SELECT
                        2 AS src,
                        ha.ha_id,
                        ha.ha_hb_id,
                        ha.ha_challan_id,
                        NVL(ha.ha_hostel_location_preference, happ.happ_hostel_location_preference) AS location_preference,
                        sc.sc_challan_status
                    FROM hostel_assign ha
                    JOIN students_apply_info sai ON UPPER(ha.ha_enrolment_no) = UPPER(sai.sai_enrollment_no)
                    JOIN students_personal_info spi ON sai.sai_spi_id = spi.spi_id
                    JOIN hostel_applicants happ ON UPPER(happ.happ_enrollment_no) = UPPER(ha.ha_enrolment_no)
                        AND happ.happ_session = ha.ha_session
                        AND happ.happ_allowed_status = 'Allowed'
                    LEFT JOIN student_challan sc ON ha.ha_challan_id = sc.id
                    WHERE ha.ha_session = ?
                      AND ha.ha_status = 'E'
                      AND ha.ha_res_next_sess IS NULL
                      AND UPPER(TRIM(spi.spi_gender)) = UPPER(?)
                ) raw_pipeline
                GROUP BY ha_id
            ) pipeline
            GROUP BY location_preference
        ", [$sessionId, $genderParam, $sessionId, $genderParam]);
    }

    private function runStudentPipelineQuery(int $sessionId, string $genderParam): array
    {
        return DB::select("
            SELECT
                NVL(ha.ha_hostel_location_preference, happ.happ_hostel_location_preference) AS location_preference,
                COUNT(DISTINCT ha.ha_id) AS total_offers,
                COUNT(DISTINCT CASE WHEN ha.ha_hb_id IS NULL THEN ha.ha_id END) AS unassigned_offers,
                COUNT(DISTINCT CASE WHEN ha.ha_challan_id IS NOT NULL AND sc.sc_challan_status = 'UNPAID' THEN ha.ha_id END) AS unpaid_offers,
                COUNT(DISTINCT CASE WHEN sc.sc_challan_status = 'PAID' THEN ha.ha_id END) AS paid_offers
            FROM hostel_assign ha
            JOIN students_apply_info sai ON UPPER(ha.ha_enrolment_no) = UPPER(sai.sai_enrollment_no)
            JOIN students_personal_info spi ON sai.sai_spi_id = spi.spi_id
            JOIN hostel_applicants happ ON UPPER(happ.happ_enrollment_no) = UPPER(ha.ha_enrolment_no)
                AND happ.happ_session = ha.ha_session
                AND happ.happ_allowed_status = 'Allowed'
            LEFT JOIN student_challan sc ON ha.ha_challan_id = sc.id
            WHERE ha.ha_session = ?
              AND ha.ha_status = 'E'
              AND ha.ha_res_next_sess IS NULL
              AND UPPER(TRIM(spi.spi_gender)) = UPPER(?)
            GROUP BY NVL(ha.ha_hostel_location_preference, happ.happ_hostel_location_preference)
        ", [$sessionId, $genderParam]);
    }

    private function runApplicantPipelineQuery(int $sessionId, string $genderParam): array
    {
        return DB::select("
            SELECT
                NVL(ha.ha_hostel_location_preference, 'on_campus') AS location_preference,
                COUNT(DISTINCT ha.ha_id) AS total_offers,
                COUNT(DISTINCT CASE WHEN ha.ha_hb_id IS NULL THEN ha.ha_id END) AS unassigned_offers,
                COUNT(DISTINCT CASE WHEN ha.ha_challan_id IS NOT NULL AND sc.sc_challan_status = 'UNPAID' THEN ha.ha_id END) AS unpaid_offers,
                COUNT(DISTINCT CASE WHEN sc.sc_challan_status = 'PAID' THEN ha.ha_id END) AS paid_offers
            FROM hostel_assign ha
            JOIN admission_apply_info aai ON ha.ha_aai_id = aai.aai_id
            JOIN admission_personal_info api ON api.api_id = NVL(ha.ha_api_id, aai.aai_api_id)
            LEFT JOIN student_challan sc ON ha.ha_challan_id = sc.id
            WHERE ha.ha_session = ?
              AND ha.ha_status = 'E'
              AND ha.ha_res_next_sess IS NULL
              AND ha.ha_aai_id IS NOT NULL
              AND api.api_allow_hostel = 'Allowed'
              AND UPPER(TRIM(api.api_gender)) = UPPER(?)
              AND aai.aai_status = 'Selected'
            GROUP BY NVL(ha.ha_hostel_location_preference, 'on_campus')
        ", [$sessionId, $genderParam]);
    }

    private function emptyLocationStat(string $label): array
    {
        return [
            'label' => $label,
            'available_seats' => 0,
            'allowed' => 0,
            'unpaid' => 0,
            'paid' => 0,
            'remaining' => 0,
            'physical_vacancy' => 0,
        ];
    }

    private function applyHostelLocationFilter($query, string $column, string $location): void
    {
        $location = strtolower(trim($location));

        if ($location === 'on_campus') {
            $query->whereRaw(
                "UPPER(REPLACE(REPLACE(TRIM({$column}), ' ', '_'), '-', '_')) IN ('ON_CAMPUS')"
            );
            return;
        }

        if (in_array($location, ['off_campus', 'out_of_campus'], true)) {
            $query->whereRaw(
                "UPPER(REPLACE(REPLACE(TRIM({$column}), ' ', '_'), '-', '_')) IN ('OFF_CAMPUS', 'OUT_OF_CAMPUS')"
            );
        }
    }

    private function normalizePipelineLocation(string $pref): string
    {
        $pref = strtolower(trim($pref));
        $pref = str_replace([' ', '-'], '_', $pref);

        if ($pref === 'both') {
            return 'on_campus';
        }

        if (
            in_array($pref, ['off_campus', 'out_of_campus'], true)
            || strpos($pref, 'off_campus') !== false
            || strpos($pref, 'out_of_campus') !== false
        ) {
            return 'off_campus';
        }

        return 'on_campus';
    }

    private function genderType(string $gender): int
    {
        return strtolower($gender) === 'female' ? 2 : 1;
    }

    /**
     * Sync hostel_beds.hb_is_occupied after any assign / vacate operation.
     *
     * Rule:
     *   - If ANY active session (ha_status='E') still has this bed assigned  → 'Y'
     *   - Otherwise                                                           → 'N'
     *
     * This is the single source of truth for hb_is_occupied.
     * Call this instead of writing hb_is_occupied directly in controllers.
     */
    public static function syncBedPhysicalStatus(int $bedId): void
    {
        $activeAssignment = DB::table('hostel_assign')
            ->where('ha_hb_id', $bedId)
            ->where('ha_status', 'E')
            ->whereNotNull('ha_hb_id')
            ->orderByDesc('ha_session')
            ->first();

        DB::table('hostel_beds')
            ->where('hb_id', $bedId)
            ->update([
                'hb_is_occupied'   => $activeAssignment ? 'Y' : 'N',
                'hb_enrollment_no' => $activeAssignment ? $activeAssignment->ha_enrolment_no : null,
            ]);
    }
}
