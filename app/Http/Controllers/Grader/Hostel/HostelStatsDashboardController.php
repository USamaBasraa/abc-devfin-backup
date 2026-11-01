<?php

namespace App\Http\Controllers\Grader\Hostel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Exception;
use App\Services\Hostel\HostelSeatStatsService;

class HostelStatsDashboardController extends Controller
{
    /**
     * Get the HTML dashboard for hostel statistics
     * This is rendered via AJAX in the main hostel applicants/students page.
     */
    public function getDashboard(Request $request)
    {
        try {
            $sessionId = $request->input('session_id');
            $gender = strtolower($request->input('gender', 'male')); // 'male' or 'female'
            $genderParam = ucfirst($gender); // 'Male' or 'Female'
            $htGender = ($gender == 'male') ? 1 : 2; // 1=Boys, 2=Girls
            // dd($genderParam);
            if (!$sessionId) {
                return '<div class="alert alert-info text-center">Please select a session to view dashboard.</div>';
            }

            // 1. Fetch Hostel Types (Raw Query)
            $hostelTypes = DB::select("SELECT * FROM HOSTEL_TYPE WHERE HT_STATUS = 'E' AND HT_HOSTEL_TYPE = ?", [$htGender]);

            // 2. Overall Stats Calculation (Using Raw SQL)
            // Confirmed Paid
            $overallConfirmedPaid = DB::selectOne("
                SELECT COUNT(DISTINCT ha.ha_id) as total
                FROM hostel_assign ha
                JOIN student_challan sc ON ha.ha_challan_id = sc.id
                LEFT JOIN admission_personal_info api ON ha.ha_api_id = api.api_id
                LEFT JOIN students_apply_info sai ON ha.ha_enrolment_no = sai.sai_enrollment_no
                LEFT JOIN students_personal_info spi ON sai.sai_spi_id = spi.spi_id
                WHERE ha.ha_session = ?
                AND ha.ha_status = 'E'
                AND sc.sc_challan_status = 'PAID'
                AND sc.sc_challan_type = 'Hostel_Fee'

            ", [$sessionId]);

            // Pending Unpaid
            $overallPendingUnpaid = DB::selectOne("
                SELECT COUNT(DISTINCT ha.ha_id) as total
                FROM hostel_assign ha
                LEFT JOIN student_challan sc ON ha.ha_challan_id = sc.id
                LEFT JOIN admission_personal_info api ON ha.ha_api_id = api.api_id
                LEFT JOIN students_apply_info sai ON ha.ha_enrolment_no = sai.sai_enrollment_no
                LEFT JOIN students_personal_info spi ON sai.sai_spi_id = spi.spi_id
                WHERE ha.ha_session = ?
                AND ha.ha_status = 'E'
                AND (sc.sc_challan_status != 'PAID' OR sc.sc_challan_status IS NULL)

            ", [$sessionId]);

            // 3. Location-based Stats Calculation
            $locationStats = [
                'ON CAMPUS' => [
                    'capacity' => 0,
                    'occupied' => 0,
                    'paid_unassigned' => 0,
                    'net_available' => 0
                ],
                'OFF CAMPUS' => [
                    'capacity' => 0,
                    'occupied' => 0,
                    'paid_unassigned' => 0,
                    'net_available' => 0
                ]
            ];

            // Fetch Paid Unassigned Count Grouped by Location Preference
            // Value in DB: 'on_campus', 'off_campus', 'both'
            $paidUnassignedGroups = DB::select("
                SELECT ha.ha_hostel_location_preference, COUNT(DISTINCT ha.ha_id) as total
                FROM hostel_assign ha
                JOIN student_challan sc ON ha.ha_challan_id = sc.id
                LEFT JOIN admission_personal_info api ON ha.ha_api_id = api.api_id
                LEFT JOIN students_apply_info sai ON ha.ha_enrolment_no = sai.sai_enrollment_no
                LEFT JOIN students_personal_info spi ON sai.sai_spi_id = spi.spi_id
                WHERE ha.ha_session = ?
                AND ha.ha_status = 'E'
                AND sc.sc_challan_status = 'PAID'
                AND sc.sc_challan_type = 'Hostel_Fee'
                AND ha.ha_hb_id IS NULL
                GROUP BY ha.ha_hostel_location_preference
            ", [$sessionId]);

            //dd($paidUnassignedGroups);
            foreach ($paidUnassignedGroups as $group) {
                // Map DB values to Array Keys
                $pref = $group->ha_hostel_location_preference;

                // Logic: 
                // on_campus -> ON CAMPUS
                // off_campus -> OFF CAMPUS
                // both -> We'll add to ON CAMPUS for safety (or display separately, but for net availability we need to deduct). 
                // Let's assume 'both' deducts from ON CAMPUS as primary choice.

                if ($pref == 'off_campus') {
                    $locationStats['OFF CAMPUS']['paid_unassigned'] += $group->total;
                } else {
                    // on_campus, both, or null (assume on campus default)
                    $locationStats['ON CAMPUS']['paid_unassigned'] += $group->total;
                }
            }

            $dashboardData = [];

            // Loop through each hostel type and calculate stats
            foreach ($hostelTypes as $type) {
                // Capacity
                $capacity = DB::selectOne("
                    SELECT COUNT(hb.hb_bed_number) as total
                    FROM hostel_beds hb
                    JOIN hostel_rooms hr ON hb.hb_room_id = hr.hr_id
                    JOIN hostel_floor hf ON hr.hr_hf_id = hf.hf_id
                    WHERE hf.hf_new_hostel_type = ?
                    AND hr.hr_status = 'E'
                    AND hf.hf_status = 'E'
                ", [$type->ht_id]);

                // Physical Occupancy (Real-time currently residing)
                $physicalOccupied = DB::selectOne("
                    SELECT COUNT(hb.hb_bed_number) as total
                    FROM hostel_beds hb
                    JOIN hostel_rooms hr ON hb.hb_room_id = hr.hr_id
                    JOIN hostel_floor hf ON hr.hr_hf_id = hf.hf_id
                    WHERE hr.hr_ht_id = ?
                    AND hb.HB_IS_OCCUPIED = 'Y'
                    AND hr.hr_status = 'E'
                    AND hf.hf_status = 'E'
                ", [$type->ht_id]);

                // Confirmed Paid for this hostel (Session specific)
                $confirmed = DB::selectOne("
                    SELECT COUNT(DISTINCT ha.ha_id) as total
                    FROM hostel_assign ha
                    JOIN student_challan sc ON ha.ha_challan_id = sc.id
                    JOIN hostel_beds hb ON ha.ha_hb_id = hb.hb_id
                    JOIN hostel_rooms hr ON hb.hb_room_id = hr.hr_id
                    WHERE ha.ha_session = ?
                AND ha.ha_status = 'E'
                    AND sc.sc_challan_status = 'PAID'
                    AND sc.sc_challan_type = 'Hostel_Fee'
                    AND hr.hr_ht_id = ?
                ", [$sessionId, $type->ht_id]);

                // Pending Unpaid for this hostel (Session specific)
                $pending = DB::selectOne("
                    SELECT COUNT(DISTINCT ha.ha_id) as total
                    FROM hostel_assign ha
                    LEFT JOIN student_challan sc ON ha.ha_challan_id = sc.id
                    JOIN hostel_beds hb ON ha.ha_hb_id = hb.hb_id
                    JOIN hostel_rooms hr ON hb.hb_room_id = hr.hr_id
                    WHERE ha.ha_session = ?
                AND ha.ha_status = 'E'
                    AND (sc.sc_challan_status != 'PAID' OR sc.sc_challan_status IS NULL)
                    AND hr.hr_ht_id = ?
                ", [$sessionId, $type->ht_id]);

                $totalBeds = $capacity->total;
                $physicallyOccupiedCount = $physicalOccupied->total;
                $physicallyVacant = max(0, $totalBeds - $physicallyOccupiedCount);

                $confirmedPaid = $confirmed->total;
                $pendingUnpaid = $pending->total;
                $vacant = max(0, $totalBeds - $confirmedPaid - $pendingUnpaid);
                $occupancyRate = $totalBeds > 0 ? round((($confirmedPaid + $pendingUnpaid) / $totalBeds) * 100) : 0;
                $physicalOccupancyRate = $totalBeds > 0 ? round(($physicallyOccupiedCount / $totalBeds) * 100) : 0;

                $locationKey = strtoupper(str_replace('_', ' ', $type->ht_location ?? 'UNKNOWN'));

                $dashboardData[$locationKey][] = [
                    'id' => $type->ht_id,
                    'name' => $type->ht_name,
                    'location_label' => str_replace('_', ' ', $type->ht_location ?? ''),
                    'total_seats' => $totalBeds,
                    'reserved_unpaid' => $pendingUnpaid,
                    'confirmed_paid' => $confirmedPaid,
                    'available' => $vacant,
                    'occupancy_rate' => $occupancyRate,
                    // New Stats
                    'physical_occupied' => $physicallyOccupiedCount,
                    'physical_vacant' => $physicallyVacant,
                    'physical_occupancy_rate' => $physicalOccupancyRate
                ];

                // Accumulate Stats by Location
                if (isset($locationStats[$locationKey])) {
                    $locationStats[$locationKey]['capacity'] += $totalBeds;
                    $locationStats[$locationKey]['occupied'] += $physicallyOccupiedCount;
                }
            }

            // Calculate Net Available per Location
            foreach ($locationStats as $key => &$stats) {
                $physicalVacancy = max(0, $stats['capacity'] - $stats['occupied']);
                $stats['net_available'] = max(0, $physicalVacancy - $stats['paid_unassigned']);
            }

            $admissionScheduleId = $request->input('admission_schedule_id');

            // 4. Allowance Stats (Refined for Offers Control)
            // Objective: Calculate "Safe Remaining" capacity for new allowances.
            // Formula: Physical Vacancy - Total Active Offers (Allowed but not Bedded).

            // 4. Allowance Stats (Refined for Offers Control)
            // Objective: Calculate "Safe Remaining" capacity for new allowances.
            // Formula: Physical Vacancy - Total Active Offers (Allowed but not Bedded).

            // 4. Allowance Stats (Refined for Offers Control)
            // Objective: Calculate "Safe Remaining" capacity for new allowances.
            // Formula: Physical Vacancy - Total Active Offers (Allowed but not Bedded).

            $context = $request->input('context', 'applicant');
            $seatStats = app(HostelSeatStatsService::class);
            $allowanceStats = $seatStats->getAllowanceStats((int) $sessionId, $gender, $context);

            return view('Hostel.components.hostel_stats_dashboard', compact('dashboardData', 'locationStats', 'allowanceStats', 'gender', 'sessionId'))->render();

        } catch (Exception $e) {
            return '<div class="alert alert-danger">Error loading dashboard: ' . $e->getMessage() . '</div>';
        }
    }

    /**
     * Get the list of students for the drill-down modal
     */
    public function getDrillDownList(Request $request)
    {
        try {
            $sessionId = $request->input('session_id');
            $hostelId = $request->input('hostel_id');
            $type = $request->input('type'); // 'paid' or 'reserved'

            $sql = "
                SELECT 
                    ha.ha_enrolment_no,
                    ha.ha_aai_id,
                    sc.sc_challan_status,
                    hf.hf_name as floor_name,
                    hr.hr_room_no as room_no,
                    hb.hb_bed_number as bed_no,
                    COALESCE(
                        (SELECT api_name FROM admission_personal_info WHERE api_id = ha.ha_api_id),
                        (SELECT spi_name FROM students_personal_info spi 
                         JOIN students_apply_info sai ON spi.spi_id = sai.sai_spi_id 
                         WHERE UPPER(sai.sai_enrollment_no) = UPPER(ha.ha_enrolment_no) OR UPPER(sai.sai_enrollment_no) = UPPER(TO_CHAR(ha.ha_aai_id)))
                    ) as student_name,
                    COALESCE(
                        (SELECT api_email FROM admission_personal_info WHERE api_id = ha.ha_api_id),
                        (SELECT spi_email FROM students_personal_info spi 
                         JOIN students_apply_info sai ON spi.spi_id = sai.sai_spi_id 
                         WHERE UPPER(sai.sai_enrollment_no) = UPPER(ha.ha_enrolment_no) OR UPPER(sai.sai_enrollment_no) = UPPER(TO_CHAR(ha.ha_aai_id)))
                    ) as student_email
                FROM hostel_assign ha
                LEFT JOIN student_challan sc ON ha.ha_challan_id = sc.id
                JOIN hostel_beds hb ON ha.ha_hb_id = hb.hb_id
                JOIN hostel_rooms hr ON hb.hb_room_id = hr.hr_id
                JOIN hostel_floor hf ON hr.hr_hf_id = hf.hf_id
                WHERE ha.ha_session = ?
                AND hr.hr_ht_id = ?
            ";

            $params = [$sessionId, $hostelId];

            if ($type == 'paid') {
                $sql .= " AND sc.sc_challan_status = 'PAID'";
            } else {
                $sql .= " AND (sc.sc_challan_status != 'PAID' OR sc.sc_challan_status IS NULL)";
            }

            $students = DB::select($sql, $params);

            return response()->json([
                'success' => true,
                'students' => $students
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching details: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get dashboard stats as JSON for frontend validation
     */
    public function getStatsJson(Request $request)
    {
        try {
            $sessionId = $request->input('session_id');
            $gender = strtolower($request->input('gender', 'male'));
            $genderParam = ucfirst($gender);

            if (!$sessionId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Session ID is required'
                ]);
            }

            $context = $request->input('context', 'applicant');
            $seatStats = app(HostelSeatStatsService::class);
            $allowanceStats = $seatStats->getAllowanceStats((int) $sessionId, $gender, $context);

            foreach ($allowanceStats as $locKey => &$stat) {
                $stat['total_offers'] = $stat['allowed'];
                $stat['safe_remaining'] = $stat['remaining'];
            }
            unset($stat);

            return response()->json([
                'success' => true,
                'allowanceStats' => $allowanceStats
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching stats: ' . $e->getMessage()
            ]);
        }
    }
}
