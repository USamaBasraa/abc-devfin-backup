<?php

namespace App\Http\Controllers\Grader\Hostel;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdmissionNewApplicantsHostelReportController extends Controller
{
    private const ON_CAMPUS_SESSION_TYPES = ['regular', 'non-regular'];

    private const OFF_CAMPUS_SESSION_TYPES = [
        'regular_outside',
        'non-regular_outside',
        'regular_outside_bedian',
        'non-regular_outside_bedian',
    ];

    public function sessionPage()
    {
        $sessions = DB::table('academic_sessions')
            ->select('sess_id', 'sess_name')
            ->where('sess_id', '>', 164)
            ->orderBy('sess_id', 'desc')
            ->get();

        return view('reports.hostel_session_select', compact('sessions'));
    }

    public function studentsAjax(Request $request, $sessionId)
    {
        try {
            $sessionId = (int) $sessionId;
            $filters = [
                'student_type' => strtolower((string) $request->input('student_type', 'all')),
                'challan_status' => strtolower((string) $request->input('challan_status', 'all')),
                'hostel_location' => strtolower((string) $request->input('hostel_location', 'all')),
                'gender' => strtolower((string) $request->input('gender', 'all')),
            ];

            // 1. Fetch complete unfiltered data for session summary stats
            $sumary_filters = [
                'student_type' => 'all',
                'challan_status' => 'all',
                'hostel_location' => 'all',
                'gender' => 'all',
            ];

            $sumary_data = $this->fetchNewAdmissionRows($sessionId, $sumary_filters);
            $sumary_data = $sumary_data->merge($this->fetchExistingStudentRows($sessionId, $sumary_filters));

            $summary = $this->buildSummaryData($sumary_data);

            // 2. Fetch filtered rows for student detail table
            $rows = collect();

            if (in_array($filters['student_type'], ['all', 'new'], true)) {
                $rows = $rows->merge($this->fetchNewAdmissionRows($sessionId, $filters));
            }

            if (in_array($filters['student_type'], ['all', 'existing'], true)) {
                $rows = $rows->merge($this->fetchExistingStudentRows($sessionId, $filters));
            }

            $rows = $this->applyPostFilters($rows, $filters)
                ->sortBy(fn ($row) => strtolower((string) ($row->student_name ?? '')))
                ->values();

            $stats = $this->buildStats($rows);
            $sessionName = DB::table('academic_sessions')
                ->where('sess_id', $sessionId)
                ->value('sess_name');

            return view('reports.partials.hostel_students', compact('rows', 'stats', 'summary', 'filters', 'sessionId', 'sessionName'));
        } catch (Exception $e) {
            Log::error('Hostel report error: ' . $e->getMessage(), [
                'session_id' => $sessionId,
                'filters' => $request->all(),
            ]);

            return response()->view('reports.partials.hostel_students_error', [
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Legacy route support: /hostel-report/{sessionId}/students/{type}
     */
    public function students(Request $request, $sessionId, $type)
    {
        $typeMap = [
            'new' => 'new',
            'existing' => 'existing',
            'continuing' => 'existing',
            'old' => 'existing',
        ];

        $request->merge([
            'student_type' => $typeMap[strtolower((string) $type)] ?? 'all',
        ]);

        return $this->studentsAjax($request, $sessionId);
    }

    private function fetchNewAdmissionRows(int $sessionId, array $filters): Collection
    {
        $sql = "
            SELECT
                aai.aai_id AS record_id,
                'New Admission' AS student_type,
                api.api_name AS student_name,
                aai.aai_id AS applicant_id,
                aai.cms_sai_enrollment_no AS enrollment_no,
                aai.cms_sai_enrollment_no AS roll_no,

                api.api_ishostel,
                api.api_gender AS gender,
                schools.school_name,
                departments.dept_name,
                programs.prog_name AS program_name,
                api.api_permanentcity AS city,
                api.api_permanentaddress AS permanent_address,
                api.api_mobile AS mobile,

                NVL(api.api_allow_hostel, 'Not Allowed') AS allowed_status,
                aai.aai_status AS admission_status,

                ha.ha_hostel_location_preference AS location_preference,

                sc.id AS challan_id,
                sc.challan_no,
                sc.sc_challan_status AS challan_status,
                NVL(sc.sc_amount, sc.invoiceamount) AS challan_amount,
                sc.sc_scholarship_amount AS scholarship_amount,
                sc.sc_session_type AS session_type,
                sc.sc_hostel_category AS hostel_category,
                sc.sc_due_date AS due_date,
                sc.sc_paid_on AS paid_date,

                ht.ht_name AS hostel_name,
                hf.hf_name AS floor_name,
                hr.hr_room_no AS room_no,
                hb.hb_bed_number AS bed_no

            FROM student_challan sc

            INNER JOIN admission_apply_info aai
                ON sc.sc_app_id = aai.aai_id
                AND aai.aai_sdpo_sess_id = :session_id_1

            LEFT JOIN admission_personal_info api
                ON aai.aai_api_id = api.api_id

            LEFT JOIN schools
                ON schools.school_id = aai.aai_sdpo_school_id

            LEFT JOIN departments
                ON departments.dept_id = aai.aai_sdpo_dept_id

            LEFT JOIN programs
                ON programs.prog_id = aai.aai_sdpo_prog_id

            LEFT JOIN hostel_assign ha
                ON sc.sc_app_id = ha.ha_aai_id
                AND ha.ha_session = :session_id_2
                AND NVL(ha.ha_status, 'E') = 'E'

            LEFT JOIN hostel_beds hb
                ON ha.ha_hb_id = hb.hb_id

            LEFT JOIN hostel_rooms hr
                ON hb.hb_room_id = hr.hr_id

            LEFT JOIN hostel_floor hf
                ON hr.hr_hf_id = hf.hf_id

            LEFT JOIN hostel_type ht
                ON hr.hr_ht_id = ht.ht_id

            WHERE sc.sc_challan_type = 'Hostel_Fee'
                AND sc.sc_session_id = :session_id_3
                AND NVL(sc.sc_challan_status, 'X') != 'CANCELLED'
                AND NVL(sc.invoice_status, 'E') = 'E'
                AND NVL(sc.sc_school_id, 0) != 22
                AND aai.aai_sdpo_sess_id = :session_id_4
        ";

        $rows = collect(DB::select($sql, [
            'session_id_1' => $sessionId,
            'session_id_2' => $sessionId,
            'session_id_3' => $sessionId,
            'session_id_4' => $sessionId,
        ]));

        return $rows;
    }

    private function fetchExistingStudentRows(int $sessionId, array $filters): Collection
    {
        $sql = "
           SELECT
                happ.happ_enrollment_no AS record_id,
                'Existing Student' AS student_type,
                spi.spi_name AS student_name,
                sai.sai_id AS applicant_id,
                happ.happ_enrollment_no AS enrollment_no,
                sai.sai_enrollment_no AS roll_no,
                spi.spi_gender AS gender,
                schools.school_name,
                departments.dept_name,
                programs.prog_name AS program_name,
                spi.spi_permanentcity AS city,
                spi.spi_permanentaddress AS permanent_address,
                spi.spi_mobile AS mobile,
                NVL(happ.happ_allowed_status, 'Allowed') AS allowed_status,
                NVL(sai.sai_student_status, 'Active') AS admission_status,
                happ.happ_hostel_location_preference AS location_preference,
                sc.id AS challan_id,
                sc.challan_no,
                sc.sc_challan_status AS challan_status,
                NVL(sc.sc_amount, sc.invoiceamount) AS challan_amount,
                sc.sc_scholarship_amount AS scholarship_amount,
                sc.sc_session_type AS session_type,
                sc.sc_hostel_category AS hostel_category,
                sc.sc_due_date AS due_date,
                sc.sc_paid_on AS paid_date,
                ht.ht_name AS hostel_name,
                hf.hf_name AS floor_name,
                hr.hr_room_no AS room_no,
                hb.hb_bed_number AS bed_no
            FROM hostel_applicants happ
            LEFT JOIN students_apply_info sai
                ON UPPER(happ.happ_enrollment_no) = UPPER(sai.sai_enrollment_no)
            LEFT JOIN students_personal_info spi
                ON spi.spi_id = sai.sai_spi_id
            LEFT JOIN schools ON schools.school_id = sai.sai_sdpo_school_id
            LEFT JOIN departments ON departments.dept_id = sai.sai_sdpo_dept_id
            LEFT JOIN programs ON programs.prog_id = sai.sai_sdpo_prog_id
            LEFT JOIN hostel_assign ha
                ON UPPER(ha.ha_enrolment_no) = UPPER(happ.happ_enrollment_no)
               AND ha.ha_session = :session_id_1
               AND NVL(ha.ha_status, 'E') = 'E'
            LEFT JOIN hostel_beds hb ON ha.ha_hb_id = hb.hb_id
            LEFT JOIN hostel_rooms hr ON hb.hb_room_id = hr.hr_id
            LEFT JOIN hostel_floor hf ON hr.hr_hf_id = hf.hf_id
            LEFT JOIN hostel_type ht ON hr.hr_ht_id = ht.ht_id
            LEFT JOIN student_challan sc
                ON UPPER(sc.sc_enrollment_no) = UPPER(happ.happ_enrollment_no)
               AND sc.sc_challan_type = 'Hostel_Fee'
               AND sc.sc_session_id = :session_id_2
               AND NVL(sc.sc_challan_status, 'X') != 'CANCELLED'
               AND NVL(sc.invoice_status, 'E') = 'E'
               AND NVL(sc.sc_school_id, 0) != 22
            WHERE happ.happ_session = :session_id_3
              AND (
                    sc.id IS NOT NULL
                 OR (ha.ha_hb_id IS NOT NULL AND hb.hb_id IS NOT NULL)
              )
        ";

        $rows = collect(DB::select($sql, [
            'session_id_1' => $sessionId,
            'session_id_2' => $sessionId,
            'session_id_3' => $sessionId,
        ]));

        return $rows;
    }

    private function buildSummaryData(Collection $summaryRows): array
    {
        $grouped = $summaryRows->groupBy(function ($row) {
            $type = strtolower((string) ($row->student_type ?? ''));
            $id = strtolower((string) ($row->enrollment_no ?? $row->applicant_id ?? $row->record_id ?? ''));
            return $type . '|' . $id;
        });

        $initStats = fn () => [
            'total'        => 0,
            'paid'         => 0,
            'unpaid'       => 0,
            'no_challan'   => 0,
            'assigned'     => 0,
            'not_assigned' => 0,
        ];

        $groups = [
            'new_male'        => $initStats(),
            'new_female'      => $initStats(),
            'new_other'       => $initStats(),
            'existing_male'   => $initStats(),
            'existing_female' => $initStats(),
            'existing_other'  => $initStats(),
        ];

        foreach ($grouped as $key => $studentRows) {
            $first = $studentRows->first();
            $isNew = ($first->student_type ?? '') === 'New Admission';
            $prefix = $isNew ? 'new' : 'existing';

            $genderRaw = '';
            foreach ($studentRows as $r) {
                if (filled($r->gender ?? null)) {
                    $genderRaw = strtolower(trim((string) $r->gender));
                    break;
                }
            }

            if (in_array($genderRaw, ['male', 'm', 'boy', 'boys'], true) || str_starts_with($genderRaw, 'm')) {
                $gender = 'male';
            } elseif (in_array($genderRaw, ['female', 'f', 'girl', 'girls'], true) || str_starts_with($genderRaw, 'f')) {
                $gender = 'female';
            } else {
                $gender = 'other';
            }

            $groupKey = "{$prefix}_{$gender}";
            if (!isset($groups[$groupKey])) {
                $groups[$groupKey] = $initStats();
            }

            // Determine Paid / Unpaid / No Challan
            $hasPaid = $studentRows->contains(function ($r) {
                return strtoupper(trim((string) ($r->challan_status ?? ''))) === 'PAID';
            });
            $hasUnpaid = $studentRows->contains(function ($r) {
                return strtoupper(trim((string) ($r->challan_status ?? ''))) === 'UNPAID';
            });
            $hasChallan = $studentRows->contains(function ($r) {
                return filled($r->challan_id ?? null) || filled($r->challan_no ?? null);
            });

            // Determine Hostel Assignment (Bed Assigned vs Not Assigned)
            $isAssigned = $studentRows->contains(function ($r) {
                return filled($r->bed_no ?? null) || filled($r->room_no ?? null) || filled($r->hostel_name ?? null);
            });

            $groups[$groupKey]['total']++;

            if ($hasPaid) {
                $groups[$groupKey]['paid']++;
            } elseif ($hasUnpaid) {
                $groups[$groupKey]['unpaid']++;
            } elseif (!$hasChallan) {
                $groups[$groupKey]['no_challan']++;
            } else {
                $groups[$groupKey]['unpaid']++;
            }

            if ($isAssigned) {
                $groups[$groupKey]['assigned']++;
            } else {
                $groups[$groupKey]['not_assigned']++;
            }
        }

        $sumFields = function (array ...$statArrays) use ($initStats) {
            $res = $initStats();
            foreach ($statArrays as $arr) {
                foreach ($res as $k => $v) {
                    $res[$k] += ($arr[$k] ?? 0);
                }
            }
            return $res;
        };

        $newSubtotal = $sumFields($groups['new_male'], $groups['new_female'], $groups['new_other']);
        $existingSubtotal = $sumFields($groups['existing_male'], $groups['existing_female'], $groups['existing_other']);
        $grandTotal = $sumFields($newSubtotal, $existingSubtotal);

        return [
            'new_male'          => $groups['new_male'],
            'new_female'        => $groups['new_female'],
            'new_other'         => $groups['new_other'],
            'new_subtotal'      => $newSubtotal,
            'existing_male'     => $groups['existing_male'],
            'existing_female'   => $groups['existing_female'],
            'existing_other'    => $groups['existing_other'],
            'existing_subtotal' => $existingSubtotal,
            'grand_total'       => $grandTotal,
        ];
    }

    private function applyPostFilters(Collection $rows, array $filters): Collection
    {
        if (in_array($filters['gender'], ['male', 'boys'], true)) {
            $rows = $rows->filter(fn ($row) => strtolower((string) ($row->gender ?? '')) === 'male');
        } elseif (in_array($filters['gender'], ['female', 'girls'], true)) {
            $rows = $rows->filter(fn ($row) => strtolower((string) ($row->gender ?? '')) === 'female');
        }

        if ($filters['challan_status'] === 'paid') {
            $rows = $rows->filter(fn ($row) => strtoupper((string) ($row->challan_status ?? '')) === 'PAID');
        } elseif ($filters['challan_status'] === 'unpaid') {
            $rows = $rows->filter(fn ($row) => strtoupper((string) ($row->challan_status ?? '')) === 'UNPAID');
        } elseif ($filters['challan_status'] === 'no_challan') {
            $rows = $rows->filter(fn ($row) => blank($row->challan_id));
        } elseif ($filters['challan_status'] === 'with_challan') {
            $rows = $rows->filter(fn ($row) => filled($row->challan_id));
        }

        if ($filters['hostel_location'] === 'on_campus') {
            $rows = $rows->filter(fn ($row) => $this->isOnCampusSessionType($row->session_type ?? null));
        } elseif (in_array($filters['hostel_location'], ['off_campus', 'out_of_campus'], true)) {
            $rows = $rows->filter(fn ($row) => $this->isOffCampusSessionType($row->session_type ?? null));
        }

        return $rows->values();
    }

    private function isOnCampusSessionType(?string $sessionType): bool
    {
        $value = strtolower(trim((string) $sessionType));

        return $value === '' || in_array($value, self::ON_CAMPUS_SESSION_TYPES, true);
    }

    private function isOffCampusSessionType(?string $sessionType): bool
    {
        $value = strtolower(trim((string) $sessionType));

        return in_array($value, self::OFF_CAMPUS_SESSION_TYPES, true);
    }

    private function buildStats(Collection $rows): array
    {
        $grouped = $rows->groupBy(function ($row) {
            return strtolower((string) ($row->student_type ?? '')) . '|' . strtolower((string) ($row->enrollment_no ?? $row->applicant_id ?? $row->record_id ?? ''));
        });

        // A student is considered PAID if they have at least one PAID hostel challan
        $paidStudents = $grouped->filter(function ($group) {
            return $group->contains(fn ($row) => strtoupper(trim((string) ($row->challan_status ?? ''))) === 'PAID');
        });

        // A student is UNPAID if they have NOT paid (mutually exclusive with PAID)
        $unpaidStudents = $grouped->filter(function ($group) {
            return !$group->contains(fn ($row) => strtoupper(trim((string) ($row->challan_status ?? ''))) === 'PAID');
        });

        $noChallanStudents = $grouped->filter(function ($group) {
            return $group->every(fn ($row) => blank($row->challan_id));
        });

        $bedAssignedStudents = $grouped->filter(function ($group) {
            return $group->contains(fn ($row) => filled($row->bed_no) || filled($row->room_no) || filled($row->hostel_name));
        });

        $multiChallanStudents = $grouped->filter(function ($group) {
            return $group->filter(fn ($row) => filled($row->challan_id))->count() > 1;
        });

        $newAdmissionStudents = $grouped->filter(function ($group) {
            return ($group->first()->student_type ?? '') === 'New Admission';
        });

        $existingStudents = $grouped->filter(function ($group) {
            return ($group->first()->student_type ?? '') === 'Existing Student';
        });

        return [
            'total_rows' => $rows->count(),
            'total_students' => $grouped->count(),
            'new_admission' => $newAdmissionStudents->count(),
            'existing_students' => $existingStudents->count(),
            'paid' => $paidStudents->count(),
            'unpaid' => $unpaidStudents->count(),
            'no_challan' => $noChallanStudents->count(),
            'with_scholarship' => $grouped->filter(function ($group) {
                return $group->contains(fn ($row) => ((float) ($row->scholarship_amount ?? 0)) > 0);
            })->count(),
            'bed_assigned' => $bedAssignedStudents->count(),
            'multi_challan' => $multiChallanStudents->count(),
            'paid_rows' => $rows->filter(fn ($r) => strtoupper(trim((string) ($r->challan_status ?? ''))) === 'PAID')->count(),
            'unpaid_rows' => $rows->filter(fn ($r) => strtoupper(trim((string) ($r->challan_status ?? ''))) === 'UNPAID')->count(),
        ];
    }

    public static function formatSessionType(?string $sessionType): string
    {
        $value = strtolower(trim((string) $sessionType));

        if ($value === '' || in_array($value, self::ON_CAMPUS_SESSION_TYPES, true)) {
            return $value === '' ? 'N/A' : 'On Campus';
        }

        if (in_array($value, self::OFF_CAMPUS_SESSION_TYPES, true)) {
            return 'Off Campus';
        }

        return ucwords(str_replace('_', ' ', $sessionType));
    }

    public static function formatHostelCategory(?string $category): string
    {
        $value = strtoupper(trim((string) $category));

        if ($value === 'BH') {
            return 'Boys';
        }

        if ($value === 'GH') {
            return 'Girls';
        }

        return $category ?: 'N/A';
    }
}
