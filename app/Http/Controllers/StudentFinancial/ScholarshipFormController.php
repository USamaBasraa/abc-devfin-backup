<?php
namespace App\Http\Controllers\StudentFinancial;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Grader\StudentRecord\StudentRecordController;
use App\Http\Controllers\Grader\StudentRecord\Concerns\ResolvesScholarshipFees;
use App\Http\Controllers\StudentFinancial\Concerns\BuildsScholarshipSnapshots;
use App\Http\Controllers\StudentFinancial\Concerns\PostsScholarshipSnapshots;
use App\Models\Grader\Schools;
use App\Services\Hostel\HostelAssignmentResolver;
use App\Services\StudentRecord\ChallanPosting\Support\SurplusAdjuster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ScholarshipFormController extends Controller
{
    use ResolvesScholarshipFees;
    use BuildsScholarshipSnapshots;
    use PostsScholarshipSnapshots;

    /**
     * The "Not Awarded" tab, switched off at the client's request.
     *
     * Everything behind it — unawardedStudentQuery(), fetchUnawardedStudentRows(),
     * countUnawardedStudents() and the tab's branch in getStudents() — is left in place,
     * so turning this back to true and un-hiding the tab button in scholarshipForm.blade.php
     * restores it. While it is false the count query is skipped on every list load.
     */
    public const NOT_AWARDED_TAB_ENABLED = false;

    /** @var array<string, bool> */
    private $schemaColumnCache = [];

    /** Memoised fee bases, keyed session|enrollment|challan_category. */
    private $feeBaseCache = [];

    /**
     * Memoised installment-date availability, keyed session|school|dept|prog.
     *
     * Request-scoped, never static: a static cache would survive into the next job on
     * a queue worker and answer for a session nobody asked about. A full staged list
     * is hundreds of rows across a few dozen programmes, so this turns one probe per
     * row into one probe per programme.
     */
    private $installmentProbeCache = [];

    /**
     * Columns schemaHasColumn() will even look for.
     *
     * NOT documentation — a gate. Anything absent from this list reports as MISSING
     * without the database ever being asked, so a column that really exists still reads
     * as absent and every feature guarded on it silently stays switched off. Add the
     * column here in the same commit that adds it to the DDL.
     *
     * @var array<string, array<int, string>>
     */
    private static $allowedSchemaColumns = [
        'students_scholarships_details' => [
            'ssd_amount', 'ssd_bulk_post_status', 'ssd_source_page', 'ssd_tuition_fee_actual',
            'ssd_list_ack_by', 'ssd_list_ack_at', 'ssd_list_ack_remarks',
        ],
        'fin_students_scholarships_hostel_details' => [
            'sshd_bulk_post_status', 'sshd_source_page', 'sshd_hostel_fee_actual',
            'sshd_list_ack_by', 'sshd_list_ack_at', 'sshd_list_ack_remarks',
        ],
    ];

    /**
     * Longest acknowledgement remark accepted, in CHARACTERS.
     *
     * Matches VARCHAR2(500 CHAR) in add_scholarship_list_ack_columns.sql. Enforced in PHP
     * as well as in the DDL because Oracle answers an over-long value with ORA-12899 and a
     * rolled-back transaction, and "your note was one character too long" is not something
     * a 500-row batch should find out the hard way.
     */
    private const LIST_ACK_REMARKS_MAX = 500;

    /**
     * Whether bulk-update columns exist (migration may not have run yet).
     */
    protected function schemaHasColumn(string $table, string $column): bool
    {
        $tableKey = strtolower(trim($table));
        $columnKey = strtolower(trim($column));
        $allowed = self::$allowedSchemaColumns[$tableKey] ?? null;
        if ($allowed === null || !in_array($columnKey, $allowed, true)) {
            return false;
        }

        $key = strtoupper($tableKey) . '.' . strtoupper($columnKey);
        if (array_key_exists($key, $this->schemaColumnCache)) {
            return $this->schemaColumnCache[$key];
        }

        try {
            $row = DB::selectOne("
                SELECT COUNT(*) AS cnt
                FROM user_tab_columns
                WHERE UPPER(table_name) = UPPER(:tbl)
                  AND UPPER(column_name) = UPPER(:col)
            ", ['tbl' => $tableKey, 'col' => $columnKey]);
            if (((int) ($row->cnt ?? $row->CNT ?? 0)) > 0) {
                return $this->schemaColumnCache[$key] = true;
            }
        } catch (\Throwable $e) {
            // fall through to probe
        }

        // Probe (handles synonym / casing edge cases user_tab_columns can miss).
        try {
            DB::selectOne("SELECT {$columnKey} AS probe_col FROM {$tableKey} WHERE 1 = 0");

            return $this->schemaColumnCache[$key] = true;
        } catch (\Throwable $e) {
            if (stripos($e->getMessage(), 'ORA-00904') !== false) {
                return $this->schemaColumnCache[$key] = false;
            }

            return $this->schemaColumnCache[$key] = false;
        }
    }

    protected function saveQueueResponseMeta(int $sessionId, bool $queuedForPost = false): array
    {
        $sessCode = DB::table('academic_sessions')->where('sess_id', $sessionId)->value('sess_code');

        return [
            'session_id' => $sessionId,
            'session_code' => $sessCode,
            'queued_for_post' => $queuedForPost,
            'post_url' => url('finance/scholarship-bulk-post?session_id=' . $sessionId),
            'schema_warning' => $this->bulkSchemaWarning(),
            'columns_ready' => $this->bulkColumnStatus(),
        ];
    }

    protected function bulkColumnStatus(): array
    {
        return [
            'ssd_amount' => $this->hasSsdAmountColumn(),
            'ssd_bulk_post_status' => $this->hasSsdBulkPostStatusColumn(),
            'ssd_source_page' => $this->hasSsdSourcePageColumn(),
            'sshd_bulk_post_status' => $this->hasSshdBulkPostStatusColumn(),
            'sshd_source_page' => $this->hasSshdSourcePageColumn(),
        ];
    }

    /**
     * Tags rows staged by this page so the Bulk Post queue can tell them apart
     * from rows staged by the Revised Student Record page ('RECORD_REVISED'),
     * which stages a frozen snapshot and posts through a different engine.
     *
     * Mirrors StudentRecordRevisedController::sourcePage().
     */
    protected function sourcePage(): string
    {
        return 'SCHOLARSHIP_BULK';
    }

    protected function canQueueCategory(string $category): bool
    {
        return strtolower(trim($category)) === 'hostel'
            ? $this->hasSshdBulkPostStatusColumn()
            : $this->hasSsdBulkPostStatusColumn();
    }

    protected function canQueueScholarshipPosts(): bool
    {
        return $this->hasSsdBulkPostStatusColumn() || $this->hasSshdBulkPostStatusColumn();
    }

    protected function hasSsdAmountColumn(): bool
    {
        return $this->schemaHasColumn('students_scholarships_details', 'ssd_amount');
    }

    protected function hasSsdBulkPostStatusColumn(): bool
    {
        return $this->schemaHasColumn('students_scholarships_details', 'ssd_bulk_post_status');
    }

    protected function hasSshdBulkPostStatusColumn(): bool
    {
        return $this->schemaHasColumn('fin_students_scholarships_hostel_details', 'sshd_bulk_post_status');
    }

    protected function hasSsdSourcePageColumn(): bool
    {
        return $this->schemaHasColumn('students_scholarships_details', 'ssd_source_page');
    }

    protected function hasSshdSourcePageColumn(): bool
    {
        return $this->schemaHasColumn('fin_students_scholarships_hostel_details', 'sshd_source_page');
    }

    /**
     * Columns recording that the session's scholarship list was reviewed and signed off.
     *
     * Distinct from SSD_ACK_BY / SSD_ACK_AT, which say "these FIGURES were approved for
     * posting" and are stamped at Post. These say "the person who ran this batch had
     * seen WHICH scholarships the session contains" and are stamped before Generate.
     *
     * Both are checked because the gate covers the whole session, academic and hostel
     * together — signing off half a list is not signing off the list.
     */
    protected function hasListAckColumns(): bool
    {
        return $this->schemaHasColumn('students_scholarships_details', 'ssd_list_ack_at')
            && $this->schemaHasColumn('students_scholarships_details', 'ssd_list_ack_by')
            && $this->schemaHasColumn('fin_students_scholarships_hostel_details', 'sshd_list_ack_at')
            && $this->schemaHasColumn('fin_students_scholarships_hostel_details', 'sshd_list_ack_by');
    }

    /**
     * Remarks are a separate check from the signature itself.
     *
     * Deliberately not folded into hasListAckColumns(): if the remarks column is missing
     * but the signature columns are there, signing must still work and simply not record
     * a note. Tying the two together would take the whole gate offline over one field.
     *
     * The note is REQUIRED on every signing, but that requirement is conditional on this
     * returning true — it is the one thing standing between "you must write a remark" and
     * "you must write a remark into a column that does not exist", which no operator
     * could satisfy. acknowledgeScholarshipList() and the client's syncAckGate() both key
     * their requirement on this.
     */
    protected function hasListAckRemarksColumns(): bool
    {
        return $this->schemaHasColumn('students_scholarships_details', 'ssd_list_ack_remarks')
            && $this->schemaHasColumn('fin_students_scholarships_hostel_details', 'sshd_list_ack_remarks');
    }

    /** The page whose scholarships this one must not touch. */
    protected function privilegedSourcePage(): string
    {
        return 'RECORD_REVISED';
    }

    /**
     * Students this page may look at but must not change.
     *
     * The single-student page (StudentRecordRevised) is the privileged one: it cancels and
     * re-posts challans as it saves, and it may edit anything, including rows this page
     * created. The reverse is not allowed — a bulk edit would silently overwrite that
     * work. Ownership is read from SSD_SOURCE_PAGE / SSHD_SOURCE_PAGE, which the single
     * page already stamps on every row it writes, on update as well as insert.
     *
     * Scope is the whole STUDENT for the session, not the individual row: a student
     * half-owned by each page is a confusing thing to present, and the caps and challan
     * are per student anyway.
     *
     * NULL is deliberately NOT owned. Almost every row predates the column, and treating
     * unstamped as privileged would freeze the entire page. The cost is that single-page
     * scholarships created before the column existed stay editable here — there is no
     * other provenance signal in the data to recover them from.
     *
     * @param array<int, string> $enrollments upper-cased and trimmed
     * @return array<string, bool> keyed by enrollment; absent means editable
     */
    protected function singlePageLockedStudents(int $sessionId, array $enrollments): array
    {
        $enrollments = array_values(array_filter(array_unique($enrollments)));
        if (empty($enrollments) || $sessionId <= 0) {
            return [];
        }

        $hasAcademic = $this->hasSsdSourcePageColumn();
        $hasHostel = $this->hasSshdSourcePageColumn();
        if (!$hasAcademic && !$hasHostel) {
            // No ownership recorded anywhere — nothing can be locked, and the page
            // behaves exactly as it did before this rule existed.
            return [];
        }

        $owner = $this->privilegedSourcePage();
        $locked = [];

        foreach (array_chunk($enrollments, 500) as $chunk) {
            $placeholders = implode(',', array_fill(0, count($chunk), '?'));
            $parts = [];
            $bindings = [];

            if ($hasAcademic) {
                $parts[] = "
                    SELECT UPPER(TRIM(NVL(aai.cms_sai_enrollment_no, ssd.ssd_sai_enrollment_no))) AS enrollment_no
                    FROM students_scholarships_details ssd
                    LEFT JOIN admission_apply_info aai ON aai.aai_id = ssd.ssd_app_id
                    WHERE NVL(ssd.status, 'E') = 'E'
                      AND ssd.ssd_sess_id = ?
                      AND UPPER(TRIM(NVL(ssd.ssd_source_page, ''))) = ?
                      AND UPPER(TRIM(NVL(aai.cms_sai_enrollment_no, ssd.ssd_sai_enrollment_no))) IN ({$placeholders})";
                $bindings = array_merge($bindings, [$sessionId, $owner], $chunk);
            }

            if ($hasHostel) {
                $parts[] = "
                    SELECT UPPER(TRIM(NVL(sshd.sshd_sai_enrollment_no, aai.cms_sai_enrollment_no))) AS enrollment_no
                    FROM fin_students_scholarships_hostel_details sshd
                    LEFT JOIN admission_apply_info aai ON aai.aai_id = sshd.sshd_app_id
                    WHERE NVL(TRIM(sshd.sshd_status), 'E') = 'E'
                      AND sshd.sshd_sess_id = ?
                      AND UPPER(TRIM(NVL(sshd.sshd_source_page, ''))) = ?
                      AND UPPER(TRIM(NVL(sshd.sshd_sai_enrollment_no, aai.cms_sai_enrollment_no))) IN ({$placeholders})";
                $bindings = array_merge($bindings, [$sessionId, $owner], $chunk);
            }

            try {
                foreach (DB::select(implode(' UNION ', $parts), $bindings) ?: [] as $row) {
                    $key = (string) ($row->enrollment_no ?? $row->ENROLLMENT_NO ?? '');
                    if ($key !== '') {
                        $locked[$key] = true;
                    }
                }
            } catch (\Throwable $e) {
                // Fail CLOSED would freeze the page on a transient error; fail open would
                // let a bulk edit through. The write guards run this same query again at
                // save time, so a read-side failure only costs the badge, never the rule.
                \Log::warning('Single-page ownership lookup failed: ' . $e->getMessage());
            }
        }

        return $locked;
    }

    /**
     * Stamp `single_page_locked` on a set of list rows, one lookup for the whole page.
     *
     * The flag is per STUDENT, so every row a locked student has carries it — including
     * rows that are themselves unstamped. That is the point: the student is managed
     * elsewhere, so none of their scholarships may be edited from here.
     *
     * @param array<int, object> $rows
     * @return array<int, object>
     */
    protected function attachSinglePageLock(array $rows, int $sessionId): array
    {
        if (empty($rows)) {
            return $rows;
        }

        $enrollments = [];
        foreach ($rows as $row) {
            $key = strtoupper(trim((string) ($row->enrollment_no ?? $row->ENROLLMENT_NO ?? '')));
            if ($key !== '') {
                $enrollments[$key] = true;
            }
        }

        $locked = $this->singlePageLockedStudents($sessionId, array_keys($enrollments));

        foreach ($rows as $row) {
            $key = strtoupper(trim((string) ($row->enrollment_no ?? $row->ENROLLMENT_NO ?? '')));
            $row->single_page_locked = !empty($locked[$key]) ? 1 : 0;
        }

        return $rows;
    }

    /**
     * Stamp each staged challan with whether the PREVIEW will find installment dates
     * for that student's programme, or silently date the document today + 7.
     *
     * Rides on the row the way snapshot_stale and single_page_locked do, so the page
     * has the answer before Generate is pressed and no round trip is needed at click
     * time.
     *
     * Fails open. A probe that throws stamps null, not false: dates_ok === false is a
     * claim the operator will act on, and we only make it when we actually looked.
     *
     * @param array<int, array> $challans  each already carrying fci_school_id/dept/prog
     * @return array<int, array>
     */
    protected function attachInstallmentDates(array $challans, int $sessionId): array
    {
        // Resolved through the SAME lookup Post and the single page use — SAI by
        // enrollment first, AAI by app id on a miss — rather than off columns joined
        // into the staged snapshot query. Those two ways of asking can disagree (the
        // snapshot's sai join is TRIMmed and keyed on NVL(cms_enrollment, ssd_enrollment),
        // the direct lookup is neither), and when they did, this page warned about a
        // programme the renderer had priced perfectly well.
        //
        // Batched into two queries rather than a lookup per student: a full session is a
        // few hundred students and this runs on every load of the page.
        $programmes = $this->resolveProgrammesForChallans($challans);

        foreach ($challans as &$challan) {
            try {
                $ids = $programmes[$this->challanStudentKey($challan)] ?? [
                    'school_id' => null, 'dept_id' => null, 'prog_id' => null, 'flag' => 'Admission',
                ];

                $challan['fci_school_id'] = $ids['school_id'];
                $challan['fci_dept_id']   = $ids['dept_id'];
                $challan['fci_prog_id']   = $ids['prog_id'];
                $challan['fci_flag']      = $ids['flag'];

                // Flag is part of the key: two students can share a programme and still
                // need different rows, one enrolled and one an applicant.
                $key = $sessionId . '|' . $ids['school_id'] . '|' . $ids['dept_id']
                     . '|' . $ids['prog_id'] . '|' . $ids['flag'];
                if (!array_key_exists($key, $this->installmentProbeCache)) {
                    $this->installmentProbeCache[$key] = $this->previewInstallmentAvailability(
                        $sessionId, $ids['school_id'], $ids['dept_id'], $ids['prog_id'], $ids['flag']
                    );
                }

                // Copied through as-is: true / false / null, where null means the probe
                // could not answer. Collapsing null to false here would warn about a
                // programme nobody actually checked.
                $probe = $this->installmentProbeCache[$key];
                $challan['dates_ok'] = $probe['ok'];
                $challan['dates_reason'] = isset($probe['reason']) ? $probe['reason'] : null;
            } catch (\Throwable $e) {
                \Log::warning('Installment date probe failed for a staged challan: ' . $e->getMessage());
                $challan['dates_ok'] = null;
                $challan['dates_reason'] = null;
            }
        }
        unset($challan);

        return $challans;
    }

    /** One student, one lookup — an academic and a hostel challan share an answer. */
    private function challanStudentKey(array $challan): string
    {
        $enrollment = strtolower(trim((string) ($challan['enrollment_no'] ?? '')));

        return $enrollment !== '' ? 'E:' . $enrollment : 'A:' . ($challan['aai_id'] ?? 0);
    }

    /**
     * Programme + installment flag for every student in the list, batched.
     *
     * Mirrors ResolvesScholarshipFees::previewProgrammeAndFlag() exactly, including its
     * untrimmed LOWER() comparison — that is the comparison the renderers make, and this
     * has to answer the way they do. Do not "improve" it with TRIM on one side only.
     *
     * @return array<string, array{school_id: ?int, dept_id: ?int, prog_id: ?int, flag: string}>
     */
    private function resolveProgrammesForChallans(array $challans): array
    {
        $enrollments = [];
        $aaiIds = [];
        foreach ($challans as $challan) {
            $enrollment = strtolower(trim((string) ($challan['enrollment_no'] ?? '')));
            if ($enrollment !== '') {
                $enrollments[$enrollment] = true;
            }
            $aaiId = (int) ($challan['aai_id'] ?? 0);
            if ($aaiId > 0) {
                $aaiIds[$aaiId] = true;
            }
        }

        $sai = [];
        // Oracle caps an IN list at 1000 expressions.
        foreach (array_chunk(array_keys($enrollments), 500) as $chunk) {
            foreach (DB::table('students_apply_info')
                ->select(
                    DB::raw('LOWER(SAI_ENROLLMENT_NO) as k'),
                    'SAI_SDPO_SCHOOL_ID as school_id',
                    'SAI_SDPO_DEPT_ID as dept_id',
                    'SAI_SDPO_PROG_ID as prog_id'
                )
                ->whereIn(DB::raw('LOWER(SAI_ENROLLMENT_NO)'), $chunk)
                ->get() as $row) {
                $sai[(string) ($row->k ?? $row->K ?? '')] = $row;
            }
        }

        $aai = [];
        foreach (array_chunk(array_keys($aaiIds), 500) as $chunk) {
            foreach (DB::table('admission_apply_info')
                ->select(
                    'AAI_ID as k',
                    'AAI_SDPO_SCHOOL_ID as school_id',
                    'AAI_SDPO_DEPT_ID as dept_id',
                    'AAI_SDPO_PROG_ID as prog_id'
                )
                ->whereIn('AAI_ID', $chunk)
                ->get() as $row) {
                $aai[(int) ($row->k ?? $row->K ?? 0)] = $row;
            }
        }

        $pick = function ($row, string $flag) {
            $get = function ($col) use ($row) {
                $v = $row->{$col} ?? $row->{strtoupper($col)} ?? null;

                return ($v !== null && $v !== '') ? (int) $v : null;
            };

            $ids = [
                'school_id' => $get('school_id'),
                'dept_id'   => $get('dept_id'),
                'prog_id'   => $get('prog_id'),
                'flag'      => $flag,
            ];

            // COMPLETE, not merely present — see previewProgrammeAndFlag(). A fresh
            // admission's students_apply_info row carries the enrollment number and
            // nothing else; taking it stranded the student on today + 7 with no
            // programme to name in the warning.
            return ($ids['school_id'] && $ids['dept_id'] && $ids['prog_id']) ? $ids : null;
        };

        $out = [];
        foreach ($challans as $challan) {
            $key = $this->challanStudentKey($challan);
            if (isset($out[$key])) {
                continue;
            }

            $enrollment = strtolower(trim((string) ($challan['enrollment_no'] ?? '')));
            $aaiId = (int) ($challan['aai_id'] ?? 0);

            $resolved = null;
            if ($enrollment !== '' && isset($sai[$enrollment])) {
                $resolved = $pick($sai[$enrollment], 'CMS');
            }
            if ($resolved === null && $aaiId > 0 && isset($aai[$aaiId])) {
                $resolved = $pick($aai[$aaiId], 'Admission');
            }

            $out[$key] = $resolved
                ?? ['school_id' => null, 'dept_id' => null, 'prog_id' => null, 'flag' => 'Admission'];
        }

        return $out;
    }

    /**
     * Collapse the challans whose preview will fall back to today + 7 into one entry
     * per school / department / programme, for the warning dialog.
     *
     * Ported from GenerateBulkChallanController::groupMissingFeeStructureStudents(),
     * which does the same job for a missing fee structure on the Generate Fee Challan
     * page. Copied rather than shared: that one is private, and the two pages should
     * be free to word their own dialogs.
     *
     * Counts STUDENTS, not rows. A student with both an academic and a hostel challan
     * is one person with one due date, and reporting them twice would overstate the
     * problem to the person signing off on it.
     *
     * @param array<int, array> $challans
     */
    protected function groupMissingInstallmentDates(array $challans): array
    {
        $groups = [];
        $schoolIds = [];
        $deptIds = [];
        $progIds = [];

        foreach ($challans as $challan) {
            if (($challan['dates_ok'] ?? null) !== false) {
                continue;   // true = fine; null = we did not look, so do not claim
            }

            $reason = ($challan['dates_reason'] ?? null) ?: 'no_installment';
            $schoolId = $challan['fci_school_id'] ?? null;
            $deptId = $challan['fci_dept_id'] ?? null;
            $progId = $challan['fci_prog_id'] ?? null;
            $key = $reason . '|' . $schoolId . '|' . $deptId . '|' . $progId;

            if (!isset($groups[$key])) {
                $groups[$key] = [
                    'reason' => $reason,
                    'school_id' => $schoolId,
                    'dept_id' => $deptId,
                    'prog_id' => $progId,
                    'school_name' => null,
                    'dept_name' => null,
                    'prog_name' => null,
                    'count' => 0,
                    'students' => [],
                ];
                if ($schoolId) { $schoolIds[$schoolId] = true; }
                if ($deptId) { $deptIds[$deptId] = true; }
                if ($progId) { $progIds[$progId] = true; }
            }

            // A blank enrollment is a new admission with no CMS number yet. Keying on
            // '' would fold every one of them into a single phantom student.
            $enrollment = strtoupper(trim((string) ($challan['enrollment_no'] ?? '')));
            $studentKey = $enrollment !== '' ? $enrollment : ('AAI:' . ($challan['aai_id'] ?? '0'));

            if (!isset($groups[$key]['students'][$studentKey])) {
                $groups[$key]['students'][$studentKey] = [
                    'enrollment' => $enrollment,
                    'name' => (string) ($challan['student_name'] ?? ''),
                ];
                $groups[$key]['count']++;
            }
        }

        if (empty($groups)) {
            return [];
        }

        $schoolNames = $this->lookupInstallmentNames('schools', 'school_id', 'school_name', array_keys($schoolIds));
        $deptNames = $this->lookupInstallmentNames('departments', 'dept_id', 'dept_name', array_keys($deptIds));
        $progNames = $this->lookupInstallmentNames('programs', 'prog_id', 'prog_name', array_keys($progIds));

        $out = [];
        foreach ($groups as $group) {
            // (int) on both sides — see lookupInstallmentNames() for why.
            $group['school_name'] = $schoolNames[(int) $group['school_id']] ?? null;
            $group['dept_name'] = $deptNames[(int) $group['dept_id']] ?? null;
            $group['prog_name'] = $progNames[(int) $group['prog_id']] ?? null;
            // A sample, not the roll. `count` above is the real figure and is what the
            // dialog shows; this rides along for support calls, and an uncapped list
            // would put every affected student on every staged-summary load.
            $group['students'] = array_slice(array_values($group['students']), 0, 20);
            $out[] = $group;
        }

        return $out;
    }

    /** id => name for one lookup table, in a single query. */
    private function lookupInstallmentNames(string $table, string $idColumn, string $nameColumn, array $ids): array
    {
        $ids = array_values(array_filter($ids));
        if (empty($ids)) {
            return [];
        }

        // The oci8 driver hands column names back in a different case than the query
        // spells them — lowercase here. Reading only the uppercase form raised
        // "Undefined property: stdClass::$SCHOOL_ID", the whole lookup was swallowed by
        // the catch below, and the dates dialog printed bare ids instead of names.
        // Same dance the $num/$str helpers in getStagedSummary() do, for the same reason.
        $pick = function ($row, string $column) {
            foreach ([$column, strtolower($column), strtoupper($column)] as $candidate) {
                if (isset($row->{$candidate})) {
                    return $row->{$candidate};
                }
            }

            return null;
        };

        try {
            $map = [];
            foreach (DB::table($table)->select($idColumn, $nameColumn)->whereIn($idColumn, $ids)->get() as $row) {
                $id = $pick($row, $idColumn);
                if ($id === null) {
                    continue;
                }

                // Keyed as int, and looked up as int by the caller. These id columns are
                // NUMBER(38,2), so the value can arrive as "3" or "3.00" depending on the
                // driver; "3.00" is not a numeric array key, so a string key would leave
                // a lookup by 3 missing it.
                $map[(int) $id] = $pick($row, $nameColumn);
            }

            return $map;
        } catch (\Throwable $e) {
            // A missing name is cosmetic — the dialog falls back to "Programme not on
            // record" and still reports the right count. Not worth failing the load.
            \Log::warning('Installment name lookup failed for ' . $table . ': ' . $e->getMessage());

            return [];
        }
    }

    /** Whether one student is owned by the single page in this session. */
    protected function studentIsSinglePageOwned(int $sessionId, string $enrollment): bool
    {
        $enrollment = strtoupper(trim($enrollment));
        if ($enrollment === '') {
            return false;
        }

        return !empty($this->singlePageLockedStudents($sessionId, [$enrollment])[$enrollment]);
    }

    /**
     * Turn the database errors we actually hit into something a user can act on.
     *
     * "Save failed. Please try again." is true but useless: a real deadlock and a broken
     * audit trigger produced the identical sentence, and finding out which cost a trip to
     * the Oracle log both times. The full message is still logged and still returned as
     * `error` in debug; this only decides what the toast says.
     */
    protected function humanDbFailure(\Throwable $e, string $fallback): string
    {
        $text = $e->getMessage();

        if (strpos($text, 'ORA-00060') !== false) {
            return 'Another save for this student was running at the same time, so the '
                . 'database rolled this one back. Nothing was changed — click Save Checked again.';
        }

        // A disabled or uncompilable audit trigger blocks every write to the table.
        if (strpos($text, 'ORA-04098') !== false || strpos($text, 'ORA-04045') !== false) {
            return 'A database trigger on the scholarship table is invalid, so the write was '
                . 'refused. This needs a DBA — the trigger name is in the application log.';
        }

        if (strpos($text, 'ORA-00001') !== false) {
            return 'That scholarship already exists for this student, session and fee type.';
        }

        if (strpos($text, 'ORA-01031') !== false) {
            return 'The database refused the write for lack of privileges. This needs a DBA.';
        }

        return $fallback;
    }

    /** The one sentence every refusal uses, so the user is always told where to go. */
    protected function singlePageLockedMessage(string $enrollment): string
    {
        return $enrollment . ' is locked — editable from the Single Scholarship Page only.';
    }

    /**
     * NULL is deliberately preserved rather than NVL'd to 0.
     *
     * They mean different things and need opposite responses: NULL is "never priced"
     * (a row saved before this page computed amounts), 0 is "priced, and the answer was
     * zero". Collapsing them made the list show a bare em dash for both.
     */
    protected function sqlAcademicScholarshipAmountExpr(string $alias = 'ssd'): string
    {
        if ($this->hasSsdAmountColumn()) {
            return "TO_NUMBER({$alias}.ssd_amount) AS scholarship_amount";
        }

        // Column absent — genuinely unknown, not zero.
        return 'CAST(NULL AS NUMBER) AS scholarship_amount';
    }

    protected function sqlAcademicBulkPostStatusExpr(string $alias = 'ssd'): string
    {
        if ($this->hasSsdBulkPostStatusColumn()) {
            return "TO_CHAR(NVL({$alias}.ssd_bulk_post_status, '')) AS bulk_post_status";
        }

        return "CAST('' AS VARCHAR2(20)) AS bulk_post_status";
    }

    protected function sqlHostelBulkPostStatusExpr(string $alias = 'sshd'): string
    {
        if ($this->hasSshdBulkPostStatusColumn()) {
            return "TO_CHAR(NVL({$alias}.sshd_bulk_post_status, '')) AS bulk_post_status";
        }

        return "CAST('' AS VARCHAR2(20)) AS bulk_post_status";
    }

    protected function sqlAcademicSourcePageExpr(string $alias = 'ssd'): string
    {
        if ($this->hasSsdSourcePageColumn()) {
            return "TO_CHAR(NVL({$alias}.ssd_source_page, '')) AS source_page";
        }

        return "CAST('' AS VARCHAR2(30)) AS source_page";
    }

    protected function sqlHostelSourcePageExpr(string $alias = 'sshd'): string
    {
        if ($this->hasSshdSourcePageColumn()) {
            return "TO_CHAR(NVL({$alias}.sshd_source_page, '')) AS source_page";
        }

        return "CAST('' AS VARCHAR2(30)) AS source_page";
    }

    /**
     * Percentage already used by the student's OTHER scholarships in the same
     * session and challan category — the headroom the inline % editor must respect.
     *
     * A correlated subquery rather than an analytic SUM() OVER(): the list is filtered
     * by scholarship type, so the student's other rows are usually NOT in the result
     * set and a window function would only ever see the visible ones. Mirrors the
     * (app_id OR enrollment) predicate used by updateRow() and saveStudentScholarships().
     */
    /**
     * Has this row ever been through the snapshot writer?
     *
     * Lets the list tell "never saved, so no amount was ever calculated" apart from
     * "saved, and the answer really was zero" — which look identical if you only look
     * at the amount, and need opposite reactions.
     */
    protected function sqlAcademicSnapshotBaseExpr(string $alias = 'ssd'): string
    {
        if ($this->schemaHasColumn('students_scholarships_details', 'ssd_tuition_fee_actual')) {
            return "TO_NUMBER({$alias}.ssd_tuition_fee_actual) AS snapshot_base";
        }

        return 'CAST(NULL AS NUMBER) AS snapshot_base';
    }

    protected function sqlHostelSnapshotBaseExpr(string $alias = 'sshd'): string
    {
        if ($this->schemaHasColumn('fin_students_scholarships_hostel_details', 'sshd_hostel_fee_actual')) {
            return "TO_NUMBER({$alias}.sshd_hostel_fee_actual) AS snapshot_base";
        }

        return 'CAST(NULL AS NUMBER) AS snapshot_base';
    }

    protected function sqlAcademicOtherPctExpr(string $alias = 'ssd'): string
    {
        return "(
            SELECT NVL(SUM(o.ssd_percentage), 0)
            FROM students_scholarships_details o
            WHERE o.ssd_sess_id = {$alias}.ssd_sess_id
              AND NVL(o.status, 'E') = 'E'
              AND o.ssd_id != {$alias}.ssd_id
              AND NVL(o.ssd_challan_category, 'Full_semester')
                  = NVL({$alias}.ssd_challan_category, 'Full_semester')
              AND (
                    ({$alias}.ssd_app_id IS NOT NULL AND o.ssd_app_id = {$alias}.ssd_app_id)
                 OR UPPER(TRIM(o.ssd_sai_enrollment_no)) = UPPER(TRIM({$alias}.ssd_sai_enrollment_no))
              )
        ) AS category_other_pct";
    }

    /**
     * Hostel has a single bucket per student/session — hostel rows are always Hostel_Fee,
     * so there is no per-category dimension to partition on.
     */
    protected function sqlHostelOtherPctExpr(string $alias = 'sshd'): string
    {
        return "(
            SELECT NVL(SUM(o.sshd_percentage), 0)
            FROM fin_students_scholarships_hostel_details o
            WHERE o.sshd_sess_id = {$alias}.sshd_sess_id
              AND NVL(TRIM(o.sshd_status), 'E') = 'E'
              AND o.sshd_id != {$alias}.sshd_id
              AND (
                    ({$alias}.sshd_app_id IS NOT NULL AND o.sshd_app_id = {$alias}.sshd_app_id)
                 OR UPPER(TRIM(o.sshd_sai_enrollment_no)) = UPPER(TRIM({$alias}.sshd_sai_enrollment_no))
              )
        ) AS category_other_pct";
    }

    /**
     * Every academic write in this controller goes through here, so the source
     * tag is stamped once rather than at each call site. Columns the install is
     * missing are dropped instead of raising ORA-00904.
     */
    protected function mergeAcademicRowPayload(array $payload): array
    {
        if (!$this->hasSsdAmountColumn()) {
            unset($payload['ssd_amount']);
        }
        if (!$this->hasSsdBulkPostStatusColumn()) {
            unset($payload['ssd_bulk_post_status']);
        }
        if ($this->hasSsdSourcePageColumn()) {
            $payload['ssd_source_page'] = $this->sourcePage();
        } else {
            unset($payload['ssd_source_page']);
        }

        return $payload;
    }

    /**
     * Is this payload row a queued deletion?
     *
     * MUST NOT be `!empty($row['remove'])`. The client sends `remove: false` on every row
     * it is NOT deleting, jQuery serialises that to the STRING "false", and PHP counts a
     * non-empty string as truthy — so `!empty("false")` is true and every row in the
     * payload was treated as a deletion. Saving a drawer edit therefore zeroed every
     * scholarship the student held, not just the one being removed, and the challan was
     * re-issued with no discount at all. Six students were zeroed this way on 2026-08-15.
     *
     * filter_var reads "false"/"0"/""/"off" as false and "true"/"1"/"on" as true, so it
     * survives both the JSON shape and the form-encoded one.
     */
    protected function rowIsRemoval($row): bool
    {
        return filter_var($row['remove'] ?? false, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Queue every award on one challan for re-posting.
     *
     * Used when a row is removed: the challan that gets re-issued is priced against what
     * the student still holds, so the siblings belong in the queue too. Without this only
     * the zeroed row was PENDING, and the Bulk Post page named a removal where it should
     * have named a recalculated challan.
     *
     * A row another page owns is left alone entirely — not re-queued and not re-stamped.
     * Queueing it would pull a challan the single Student Record page is responsible for
     * into this page's post run, and writing SOURCE_PAGE would hand over ownership
     * outright. Same rule refreshScholarshipSnapshot() follows via snapshotRowIsForeign():
     * a blank source is unclaimed and fair game, a foreign one is not.
     */
    protected function requeueChallanCategory(int $sessionId, ?int $aaiId, string $enrollment, string $challanCategory): void
    {
        $enrollment = strtoupper(trim($enrollment));
        if ($sessionId <= 0 || ($enrollment === '' && !$aaiId)) {
            return;
        }

        $ownedHere = function ($q, string $column, bool $exists) {
            if ($exists) {
                $q->whereRaw(
                    "UPPER(TRIM(NVL({$column}, ''))) IN ('', ?)",
                    [strtoupper($this->sourcePage())]
                );
            }
        };

        if ($challanCategory === 'Hostel_Fee') {
            $update = $this->filterExistingColumns('fin_students_scholarships_hostel_details', [
                'sshd_bulk_post_status' => 'PENDING',
            ]);
            if (empty($update)) {
                return;
            }

            $query = DB::table('fin_students_scholarships_hostel_details')
                ->where('sshd_sess_id', $sessionId)
                ->whereRaw("NVL(TRIM(sshd_status), 'E') = 'E'")
                ->where(function ($q) use ($aaiId, $enrollment) {
                    if ($aaiId) {
                        $q->where('sshd_app_id', $aaiId);
                    }
                    if ($enrollment !== '') {
                        $q->orWhereRaw('UPPER(TRIM(sshd_sai_enrollment_no)) = ?', [$enrollment]);
                    }
                });
            $ownedHere($query, 'sshd_source_page', $this->hasSshdSourcePageColumn());
            $query->update($update);

            return;
        }

        $update = $this->filterExistingColumns('students_scholarships_details', [
            'ssd_bulk_post_status' => 'PENDING',
        ]);
        if (empty($update)) {
            return;
        }

        $query = DB::table('students_scholarships_details')
            ->where('ssd_sess_id', $sessionId)
            ->where('status', 'E')
            ->whereRaw("NVL(ssd_challan_category, 'Full_semester') = ?", [$challanCategory])
            ->where(function ($q) use ($aaiId, $enrollment) {
                if ($aaiId) {
                    $q->where('ssd_app_id', $aaiId);
                }
                if ($enrollment !== '') {
                    $q->orWhereRaw('UPPER(TRIM(ssd_sai_enrollment_no)) = ?', [$enrollment]);
                }
            });
        $ownedHere($query, 'ssd_source_page', $this->hasSsdSourcePageColumn());
        $query->update($update);
    }

    protected function mergeHostelRowPayload(array $payload): array
    {
        if (!$this->hasSshdBulkPostStatusColumn()) {
            unset($payload['sshd_bulk_post_status']);
        }
        if ($this->hasSshdSourcePageColumn()) {
            $payload['sshd_source_page'] = $this->sourcePage();
        } else {
            unset($payload['sshd_source_page']);
        }

        return $payload;
    }

    /**
     * Upsert the four one-time-fee components into fin_other_scholarships for one
     * (student, session, scholarship type, challan category) — this page's own copy of
     * StudentRecordRevisedController::persistArrearsRow() (that method is `private` on
     * that controller, so it cannot be reused directly). findArrearsRow() and
     * getArrearsRowsForStudent() ARE shared, via ResolvesScholarshipFees.
     *
     * Rows are never deleted, only zeroed — same rule the rest of the app follows for
     * fin_other_scholarships. Returns the component sum, which the caller writes to
     * ssd_arrear.
     */
    protected function persistArrearsComponents(
        ?int $aaiId,
        string $enrollmentNo,
        int $sessionId,
        int $schType,
        string $challanCategory,
        array $components,
        string $username
    ): float {
        $columns = [
            'admission' => 'fos_admission_fee',
            'security' => 'fos_security_fee',
            'activity' => 'fos_activity_charges',
            'medical' => 'fos_medical_insurance',
        ];

        $existing = $this->findArrearsRow($aaiId, $enrollmentNo, $sessionId, $schType, $challanCategory);

        if ($existing) {
            $update = [];
            foreach ($columns as $component => $column) {
                $update[$column] = $components[$component] ?? 0;
            }
            DB::table('fin_other_scholarships')->where('id', $existing->id)->update($update);
        } elseif (array_sum($components) > 0) {
            $insert = [
                'fos_app_id' => $aaiId,
                'fos_enrollment_no' => $enrollmentNo !== '' ? $enrollmentNo : null,
                'fos_sess_id' => $sessionId,
                'fos_scholarship_type' => $schType,
                'fos_challan_category' => $challanCategory,
                'fos_stipend' => 0,
                'entry_by' => auth()->id(),
                'entry_username' => $username,
                'created_at' => now()->toDateTimeString(),
            ];
            foreach ($columns as $component => $column) {
                $insert[$column] = $components[$component] ?? 0;
            }
            DB::table('fin_other_scholarships')->insert($insert);
        }

        return array_sum($components);
    }

    protected function bulkSchemaWarning(): ?string
    {
        $missing = [];
        if (!$this->hasSsdAmountColumn()) {
            $missing[] = 'students_scholarships_details.ssd_amount';
        }
        if (!$this->hasSsdBulkPostStatusColumn()) {
            $missing[] = 'students_scholarships_details.ssd_bulk_post_status';
        }
        if (!$this->hasSshdBulkPostStatusColumn()) {
            $missing[] = 'fin_students_scholarships_hostel_details.sshd_bulk_post_status';
        }

        // Without the source tag this page and the Revised Student Record page
        // share one indistinguishable PENDING queue, and either can post the
        // other's rows through the wrong engine.
        $missingSourcePage = [];
        if (!$this->hasSsdSourcePageColumn()) {
            $missingSourcePage[] = 'students_scholarships_details.ssd_source_page';
        }
        if (!$this->hasSshdSourcePageColumn()) {
            $missingSourcePage[] = 'fin_students_scholarships_hostel_details.sshd_source_page';
        }

        if (empty($missing) && empty($missingSourcePage)) {
            return null;
        }

        $parts = [];
        if (!empty($missing)) {
            $parts[] = 'Missing Oracle column(s): ' . implode(', ', $missing)
                . '. Run scripts/add_scholarship_bulk_columns.sql on the CMS database (all 3 statements).';
        }
        if (!empty($missingSourcePage)) {
            $parts[] = 'Missing Oracle column(s): ' . implode(', ', $missingSourcePage)
                . '. Run scripts/add_scholarship_source_page_columns.sql — until then saved rows are not tagged '
                . 'SCHOLARSHIP_BULK and cannot be told apart from Record Revised rows on Bulk Post.';
        }

        return implode(' ', $parts);
    }

    public function index()
    {
        $sessions = DB::select("
            SELECT sess_id, sess_code, sess_name
            FROM academic_sessions
            WHERE sess_status = 'E'
            ORDER BY sess_id DESC
        ");

        $schools = Schools::where('school_status', 'E')
            ->whereNotIn('school_id', [10, 11])
            ->orderBy('school_name')
            ->get();

        $scholarshipTypes = DB::table('scholarship_type')
            ->select('scholarship_id', 'scholarship_name', 'scholarship_desc')
            ->where('scholarship_status', 'E')
            ->orderBy('scholarship_name')
            ->get();

        $schemaWarning = $this->bulkSchemaWarning();
        $columnStatus = $this->bulkColumnStatus();

        // Which session each one carries forward from, so the Import button can name it
        // without a round trip — the picker is the only place a session is chosen, and in
        // Add mode changing it never reaches the server at all.
        $previousSessions = $this->previousSessionMap(array_map(function ($s) {
            return (int) ($s->sess_id ?? $s->SESS_ID);
        }, $sessions));

        return response()
            ->view('StudentFinancial.scholarshipForm', compact(
                'sessions',
                'schools',
                'scholarshipTypes',
                'schemaWarning',
                'columnStatus',
                'previousSessions'
            ))
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    /**
     * Filtered list of students who already have an active scholarship.
     * Academic → students_scholarships_details
     * Hostel   → FIN_STUDENTS_SCHOLARSHIPS_HOSTEL_DETAILS
     */
    public function getStudents(Request $request)
    {
        $sessionId = (int) $request->input('session_id');
        if (!$sessionId) {
            return response()->json([
                'status' => false,
                'message' => 'Please select a session.',
                'students' => [],
            ], 422);
        }

        $schoolId = $this->normalizeId($request->input('school_id'));
        $deptId = $this->normalizeId($request->input('dept_id'));
        $progId = $this->extractProgramId($request->input('prog_id'));
        $scholarshipTypeId = $this->normalizeId($request->input('scholarship_type'));
        $studentId = strtoupper(trim((string) $request->input('student_id', '')));
        $allowedCategories = ['all', 'academic', 'hostel'];
        if (self::NOT_AWARDED_TAB_ENABLED) {
            $allowedCategories[] = 'unawarded';
        }

        $categoryFilter = strtolower(trim((string) $request->input('category', 'all')));
        if (!in_array($categoryFilter, $allowedCategories, true)) {
            $categoryFilter = 'all';
        }

        // Fee-type filter. The 100% cap and the fee base are both per challan category,
        // so this is the natural unit of work. Hostel rows are always Hostel_Fee, so an
        // academic selection implies academic-only and vice versa — it narrows the
        // All/Academic/Hostel tabs rather than fighting them.
        // A 0% row is a scholarship that was removed (the remove path zeroes rather than
        // deletes, so a live challan gets corrected) or one that was never filled in.
        // Hidden by default as clutter — EXCEPT while it is still staged, because then
        // the zero is a pending correction the user needs to see and post.
        $hideZero = filter_var($request->input('hide_zero', true), FILTER_VALIDATE_BOOLEAN);

        $challanCategoryFilter = trim((string) $request->input('challan_category', ''));
        if (!in_array($challanCategoryFilter, $this->allChallanCategories(), true)) {
            $challanCategoryFilter = '';
        }
        if ($challanCategoryFilter === 'Hostel_Fee' && $categoryFilter === 'academic') {
            $challanCategoryFilter = '';
        } elseif ($challanCategoryFilter !== '' && $challanCategoryFilter !== 'Hostel_Fee' && $categoryFilter === 'hostel') {
            $challanCategoryFilter = '';
        }

        $typeFilters = $this->resolveTypeFilters($scholarshipTypeId);
        $academicTypeId = $typeFilters['academic_type_id'];
        $hostelTypeId = $typeFilters['hostel_type_id'];
        $academicNameLike = $typeFilters['academic_name_like'];
        $hostelNameLike = $typeFilters['hostel_name_like'];
        $isHostelType = $typeFilters['is_hostel_type'];

        // Student status filter — same two values the rest of this page already treats
        // as eligible (eligibleStudentStatuses()): Active, or Expected-Graduate. Anything
        // else typed in is ignored rather than producing an empty list.
        $studentStatusFilter = strtoupper(trim((string) $request->input('student_status', '')));
        if (!in_array($studentStatusFilter, $this->eligibleStudentStatuses(), true)) {
            $studentStatusFilter = '';
        }

        $academic = [];
        $hostel = [];
        $errors = [];

        // A Hostel_Fee selection excludes academic outright, and any academic fee type
        // excludes hostel — skip the query entirely rather than filtering to nothing.
        $wantAcademic = ($challanCategoryFilter !== 'Hostel_Fee');
        $wantHostel = ($challanCategoryFilter === '' || $challanCategoryFilter === 'Hostel_Fee');

        if ($wantAcademic) {
            try {
                $academic = $this->fetchAcademicScholarshipRows(
                    $sessionId,
                    $schoolId,
                    $deptId,
                    $progId,
                    $academicTypeId,
                    $studentId,
                    $academicNameLike,
                    $challanCategoryFilter,
                    $hideZero,
                    // Same eligibility rule the Import and Add Students buttons apply.
                    // The list had no check at all, so a student who has since Left or
                    // been Struck-Off still appeared here and could be edited.
                    true,
                    $studentStatusFilter
                );
            } catch (\Throwable $e) {
                \Log::error('Scholarship form academic list failed: ' . $e->getMessage());
                $errors[] = 'Academic: ' . $e->getMessage();
            }
        }

        if ($wantHostel) {
            try {
                $hostel = $this->fetchHostelScholarshipRows(
                    $sessionId,
                    $schoolId,
                    $deptId,
                    $progId,
                    $hostelTypeId,
                    $studentId,
                    $hostelNameLike,
                    $hideZero,
                    true,
                    $studentStatusFilter
                );
            } catch (\Throwable $e) {
                \Log::error('Scholarship form hostel list failed: ' . $e->getMessage());
                $errors[] = 'Hostel: ' . $e->getMessage();
            }
        }

        if (!empty($errors) && empty($academic) && empty($hostel)) {
            return response()->json([
                'status' => false,
                'message' => 'Unable to load scholarship students. Please try again.',
                'error' => config('app.debug') ? implode(' | ', $errors) : null,
                'students' => [],
            ], 500);
        }

        // Exclude Scholarship Type(s) — drop a student entirely (both their academic and
        // hostel rows) if they ALSO hold any of these types this session, checked against
        // the SAME session the list is scoped to. e.g. Scholarship Type = Need Based,
        // Exclude = Danish Schools shows Need Based holders who do not also have Danish
        // Schools this session.
        $excludeTypeIds = array_values(array_unique(array_filter(
            array_map('intval', (array) $request->input('exclude_scholarship_types', []))
        )));
        if (!empty($excludeTypeIds)) {
            $excludedEnrollments = $this->fetchEnrollmentsHoldingTypes($sessionId, $excludeTypeIds);
            if (!empty($excludedEnrollments)) {
                $notExcluded = function ($row) use ($excludedEnrollments) {
                    $key = strtoupper(trim((string) ($row->enrollment_no ?? $row->ENROLLMENT_NO ?? '')));

                    return $key === '' || !isset($excludedEnrollments[$key]);
                };
                $academic = array_values(array_filter($academic ?: [], $notExcluded));
                $hostel = array_values(array_filter($hostel ?: [], $notExcluded));
            }
        }

        $academicCount = count($academic ?: []);
        $hostelCount = count($hostel ?: []);

        // ── students who do NOT yet hold the filtered scholarship ──
        //
        // Its own tab, so an edit to an existing scholarship can never be confused with
        // creating a new one. Needs a scholarship type: a bulk award is "give THIS
        // scholarship to these students", and without one there is nothing to award.
        //
        // Switched off — see NOT_AWARDED_TAB_ENABLED. The query helpers below it
        // (fetchUnawardedStudentRows / countUnawardedStudents / unawardedStudentQuery)
        // are kept intact so flipping the constant brings the tab straight back.
        $unawarded = [];
        $unawardedCount = 0;

        try {
            if (!self::NOT_AWARDED_TAB_ENABLED) {
                // Skipped entirely: with no tab to badge, counting is a wasted query on
                // every single list load.
                $unawardedCount = 0;
            } elseif ($categoryFilter === 'unawarded') {
                $unawarded = $this->fetchUnawardedStudentRows(
                    $sessionId,
                    $scholarshipTypeId,
                    $schoolId,
                    $deptId,
                    $progId,
                    $studentId,
                    $challanCategoryFilter
                );
                $unawardedCount = count($unawarded);
            } else {
                $unawardedCount = $this->countUnawardedStudents(
                    $sessionId,
                    $scholarshipTypeId,
                    $schoolId,
                    $deptId,
                    $progId,
                    $studentId,
                    $challanCategoryFilter
                );
            }
        } catch (\Throwable $e) {
            \Log::error('Unawarded student list failed: ' . $e->getMessage());
            $errors[] = 'Not Awarded: ' . $e->getMessage();
        }

        if ($categoryFilter === 'unawarded') {
            $rows = $this->attachDerivedAmounts($unawarded, $sessionId);

            // Awarding needs BOTH a scholarship type and a fee type. A hostel type
            // implies Hostel_Fee, so only academic types need the fee type chosen —
            // letting it default to Full_semester silently awarded against the wrong
            // challan. Enforced here as well as in the UI so a crafted request cannot
            // slip past it.
            $awardable = true;
            $awardNotice = null;

            if (!$scholarshipTypeId) {
                $awardable = false;
                $awardNotice = 'Showing students with no scholarship at all. '
                    . 'Choose a Scholarship Type to award one.';
            } elseif (!$isHostelType && $challanCategoryFilter === '') {
                $awardable = false;
                $awardNotice = 'Choose a Fee Type as well — a scholarship is applied to a '
                    . 'specific challan, so the fee type cannot be assumed.';
            }

            $blocked = 0;
            $blockReasons = [];
            foreach ($rows as $row) {
                $block = $row->award_block ?? null;
                if (!$block) {
                    continue;
                }
                $blocked++;
                $blockReasons[$block['message']] = ($blockReasons[$block['message']] ?? 0) + 1;
            }

            $message = $scholarshipTypeId
                ? $unawardedCount . ' student(s) without this scholarship'
                : $unawardedCount . ' student(s) with no scholarship at all';
            if ($awardable && $blocked > 0) {
                arsort($blockReasons);
                $message .= ' · ' . $blocked . ' cannot be awarded (' . key($blockReasons) . ')';
            }

            return response()->json([
                'status' => true,
                'message' => $message,
                'count' => $unawardedCount,
                'academic_count' => $academicCount,
                'hostel_count' => $hostelCount,
                'unawarded_count' => $unawardedCount,
                'students' => $rows,
                'awardable' => $awardable,
                'award_notice' => $awardNotice,
                'blocked_count' => $blocked,
                'warning' => !empty($errors) ? implode(' | ', $errors) : null,
                'schema_warning' => $this->bulkSchemaWarning(),
                'columns_ready' => $this->bulkColumnStatus(),
            ]);
        }

        if ($categoryFilter === 'academic') {
            $students = $academic ?: [];
        } elseif ($categoryFilter === 'hostel') {
            $students = $hostel ?: [];
        } else {
            $students = array_merge($academic ?: [], $hostel ?: []);
        }

        // SSD_AMOUNT is only written by this page; scholarships applied elsewhere store
        // the percentage alone. Derive the money from the percentage so the list shows a
        // figure for every row, not just the ones this page has saved.
        $students = $this->attachDerivedAmounts($students, $sessionId);
        $students = $this->attachSinglePageLock($students, $sessionId);

        usort($students, function ($a, $b) {
            $enA = strtoupper(trim((string) ($a->enrollment_no ?? $a->ENROLLMENT_NO ?? '')));
            $enB = strtoupper(trim((string) ($b->enrollment_no ?? $b->ENROLLMENT_NO ?? '')));
            if ($enA === $enB) {
                $catA = strtolower((string) ($a->category ?? $a->CATEGORY ?? ''));
                $catB = strtolower((string) ($b->category ?? $b->CATEGORY ?? ''));
                if ($catA === $catB) {
                    $nA = (string) ($a->scholarship_name ?? $a->SCHOLARSHIP_NAME ?? '');
                    $nB = (string) ($b->scholarship_name ?? $b->SCHOLARSHIP_NAME ?? '');

                    return strcmp($nA, $nB);
                }

                return $catA === 'academic' ? -1 : 1;
            }

            return strcmp($enA, $enB);
        });

        return response()->json([
            'status' => true,
            'message' => count($students) . ' shown · Academic ' . $academicCount . ' · Hostel ' . $hostelCount,
            'count' => count($students),
            'academic_count' => $academicCount,
            'hostel_count' => $hostelCount,
            'unawarded_count' => $unawardedCount,
            'students' => $students,
            'warning' => !empty($errors) ? implode(' | ', $errors) : null,
            'schema_warning' => $this->bulkSchemaWarning(),
            'columns_ready' => $this->bulkColumnStatus(),
        ]);
    }

    /**
     * Students being billed this session who do NOT hold the given scholarship.
     *
     * Population is students with a LIVE CHALLAN in the session, not
     * students_apply_info: that table has no session dimension, so it would return the
     * same students for every session. A challan also proves the student is being
     * charged, which is what makes a scholarship meaningful, and guarantees a fee base.
     *
     * Rows come back in the same shape as the scholarship list, with an empty record_id
     * so the client can tell an INSERT from an edit.
     *
     * @return array<int, object>
     */
    protected function fetchUnawardedStudentRows(
        int $sessionId,
        ?int $scholarshipTypeId,
        ?int $schoolId,
        ?int $deptId,
        ?int $progId,
        string $studentId,
        string $challanCategory
    ): array {
        [$sql, $bindings] = $this->unawardedStudentQuery(
            $sessionId, $scholarshipTypeId, $schoolId, $deptId, $progId, $studentId, $challanCategory, false
        );

        return DB::select($sql, $bindings);
    }

    protected function countUnawardedStudents(
        int $sessionId,
        ?int $scholarshipTypeId,
        ?int $schoolId,
        ?int $deptId,
        ?int $progId,
        string $studentId,
        string $challanCategory
    ): int {
        [$sql, $bindings] = $this->unawardedStudentQuery(
            $sessionId, $scholarshipTypeId, $schoolId, $deptId, $progId, $studentId, $challanCategory, true
        );

        $row = DB::selectOne($sql, $bindings);

        return (int) ($row->cnt ?? $row->CNT ?? 0);
    }

    /**
     * Shared body for the list and its tab count, so the two can never disagree.
     *
     * @return array{0: string, 1: array<string, mixed>}
     */
    protected function unawardedStudentQuery(
        int $sessionId,
        ?int $scholarshipTypeId,
        ?int $schoolId,
        ?int $deptId,
        ?int $progId,
        string $studentId,
        string $challanCategory,
        bool $countOnly
    ): array {
        // Two modes:
        //   type chosen  -> students who do not hold THAT scholarship, and the row can
        //                   be awarded by typing a percentage
        //   no type      -> students holding NO scholarship at all this session; there is
        //                   nothing to create, so the rows are informational
        $isHostel = false;
        if ($scholarshipTypeId) {
            $isHostel = $this->scholarshipTypeMeta($scholarshipTypeId)['desc'] === 'hostel';
        }

        $targetCategory = $isHostel
            ? 'Hostel_Fee'
            : (($challanCategory !== '' && $challanCategory !== 'Hostel_Fee') ? $challanCategory : 'Full_semester');

        $bindings = ['session_id' => $sessionId];
        if ($scholarshipTypeId) {
            $bindings['scholarship_type'] = $scholarshipTypeId;
        }

        // Match the challan to the fee type being awarded — an Addl Credit Hr award only
        // makes sense for a student who has that challan. With no type and no fee-type
        // filter, any live challan qualifies: the question is just "who has nothing?".
        if (!$scholarshipTypeId && $challanCategory === '') {
            $challanTypeFilter = '';
        } elseif ($targetCategory === 'Hostel_Fee') {
            $challanTypeFilter = " AND sc.sc_challan_type = 'Hostel_Fee'";
        } elseif ($targetCategory === 'additional_Full_semester') {
            $challanTypeFilter = " AND sc.sc_challan_type = 'Full_semester' AND NVL(sc.sc_enrolled_cr_hr, 0) > 0";
        } else {
            $challanTypeFilter = ' AND sc.sc_challan_type = :challan_type';
            $bindings['challan_type'] = $targetCategory;
        }

        $schoolFilter = $deptFilter = $progFilter = $studentFilter = '';
        if ($schoolId) {
            $schoolFilter = ' AND sai.sai_sdpo_school_id = :school_id';
            $bindings['school_id'] = $schoolId;
        }
        if ($deptId) {
            $deptFilter = ' AND sai.sai_sdpo_dept_id = :dept_id';
            $bindings['dept_id'] = $deptId;
        }
        if ($progId) {
            $progFilter = ' AND sai.sai_sdpo_prog_id = :prog_id';
            $bindings['prog_id'] = $progId;
        }
        if ($studentId !== '') {
            $studentFilter = ' AND UPPER(TRIM(sai.sai_enrollment_no)) LIKE :student_id';
            $bindings['student_id'] = '%' . $studentId . '%';
        }

        // "Does not hold it" = no ACTIVE row of this type, in this session, on this fee
        // type. A row zeroed to 0% still counts as held — it exists and can be edited,
        // so offering to create a second one would produce a duplicate.
        if (!$scholarshipTypeId) {
            // No type: the student must hold NOTHING this session, academic or hostel.
            $notExists = "
                AND NOT EXISTS (
                    SELECT 1 FROM students_scholarships_details x
                    WHERE x.ssd_sess_id = :session_id
                      AND NVL(x.status, 'E') = 'E'
                      AND UPPER(TRIM(x.ssd_sai_enrollment_no)) = UPPER(TRIM(sai.sai_enrollment_no))
                )
                AND NOT EXISTS (
                    SELECT 1 FROM fin_students_scholarships_hostel_details h
                    WHERE h.sshd_sess_id = :session_id
                      AND NVL(TRIM(h.sshd_status), 'E') = 'E'
                      AND UPPER(TRIM(h.sshd_sai_enrollment_no)) = UPPER(TRIM(sai.sai_enrollment_no))
                )";
        } elseif ($isHostel) {
            $notExists = "
                AND NOT EXISTS (
                    SELECT 1 FROM fin_students_scholarships_hostel_details x
                    WHERE x.sshd_sess_id = :session_id
                      AND NVL(TRIM(x.sshd_status), 'E') = 'E'
                      AND x.sshd_type = :scholarship_type
                      AND UPPER(TRIM(x.sshd_sai_enrollment_no)) = UPPER(TRIM(sai.sai_enrollment_no))
                )";
        } else {
            $notExists = "
                AND NOT EXISTS (
                    SELECT 1 FROM students_scholarships_details x
                    WHERE x.ssd_sess_id = :session_id
                      AND NVL(x.status, 'E') = 'E'
                      AND x.ssd_type = :scholarship_type
                      AND NVL(x.ssd_challan_category, 'Full_semester') = :target_category
                      AND UPPER(TRIM(x.ssd_sai_enrollment_no)) = UPPER(TRIM(sai.sai_enrollment_no))
                )";
            $bindings['target_category'] = $targetCategory;
        }

        $where = "
            WHERE sc.sc_session_id = :session_id
              AND UPPER(TRIM(NVL(sc.sc_challan_status, 'X'))) NOT IN ('CANCELLED', 'CANCEL FOR REISSUANCE')
              {$challanTypeFilter}
              {$schoolFilter}
              {$deptFilter}
              {$progFilter}
              {$studentFilter}
              {$notExists}
        ";

        if ($countOnly) {
            return ["
                SELECT COUNT(DISTINCT UPPER(TRIM(sai.sai_enrollment_no))) AS cnt
                FROM student_challan sc
                JOIN students_apply_info sai
                    ON UPPER(TRIM(sai.sai_enrollment_no)) = UPPER(TRIM(sc.sc_enrollment_no))
                {$where}
            ", $bindings];
        }

        $categoryLiteral = $isHostel ? 'hostel' : 'academic';
        $bindings['target_category_out'] = $targetCategory;

        // Real headroom for the bucket being awarded. Hard-coding 0 made every row claim
        // "max 100%" even when the student already held half of that fee type. Hostel is
        // a single bucket in its own table, so it needs its own sum — reading the academic
        // table for a hostel award would always return 0.
        if ($isHostel) {
            $otherPctSelect = "(
                    SELECT NVL(SUM(o.sshd_percentage), 0)
                    FROM fin_students_scholarships_hostel_details o
                    WHERE o.sshd_sess_id = :session_id
                      AND NVL(TRIM(o.sshd_status), 'E') = 'E'
                      AND UPPER(TRIM(o.sshd_sai_enrollment_no)) = UPPER(TRIM(sai.sai_enrollment_no))
                ) AS category_other_pct";
        } else {
            $otherPctSelect = "(
                    SELECT NVL(SUM(o.ssd_percentage), 0)
                    FROM students_scholarships_details o
                    WHERE o.ssd_sess_id = :session_id
                      AND NVL(o.status, 'E') = 'E'
                      AND NVL(o.ssd_challan_category, 'Full_semester') = :other_pct_category
                      AND UPPER(TRIM(o.ssd_sai_enrollment_no)) = UPPER(TRIM(sai.sai_enrollment_no))
                ) AS category_other_pct";
            $bindings['other_pct_category'] = $targetCategory;
        }

        if ($scholarshipTypeId) {
            $bindings['scholarship_type_out'] = $scholarshipTypeId;
            $typeIdSelect = 'TO_NUMBER(:scholarship_type_out) AS scholarship_id';
            $typeNameSelect = 'TO_CHAR(st.scholarship_name) AS scholarship_name';
            $typeJoin = 'JOIN scholarship_type st ON st.scholarship_id = :scholarship_type';
        } else {
            // Nothing to award — the row exists to report the gap, not to fill it.
            $typeIdSelect = 'CAST(NULL AS NUMBER) AS scholarship_id';
            $typeNameSelect = "CAST('No scholarship' AS VARCHAR2(100)) AS scholarship_name";
            $typeJoin = '';
        }

        $hostelAssignedPredicate = HostelAssignmentResolver::assignmentExistsSql('ha');

        // DISTINCT because a student can hold more than one challan of a type.
        return ["
            SELECT DISTINCT
                CAST('' AS VARCHAR2(20)) AS record_id,
                CAST('{$categoryLiteral}' AS VARCHAR2(20)) AS category,
                UPPER(TRIM(sai.sai_enrollment_no)) AS enrollment_no,
                TO_CHAR(NVL(spi.spi_name, api.api_name)) AS student_name,
                TO_CHAR(NVL(s.school_code, s.school_name)) AS school_name,
                TO_CHAR(s.school_name) AS school_full_name,
                TO_CHAR(d.dept_name) AS dept_name,
                TO_CHAR(p.prog_name) AS prog_name,
                {$typeIdSelect},
                {$typeNameSelect},
                CAST(0 AS NUMBER) AS scholarship_percentage,
                CAST(NULL AS NUMBER) AS scholarship_amount,
                -- The hostel fee off the challan this student is already being billed on:
                -- the same base resolveChallanScholarshipBase() uses when posting, so no
                -- extra lookup and no risk of pricing against a different figure.
                TO_NUMBER(NVL(sc.ffs_hostel_amt, 0)) AS hostel_fee,
                CAST('' AS VARCHAR2(20)) AS bulk_post_status,
                CAST('' AS VARCHAR2(30)) AS source_page,
                CAST(NULL AS NUMBER) AS snapshot_base,
                {$otherPctSelect},
                -- Which academic fee types the student already holds, for the
                -- combination rule. Comma separated; the caller splits it.
                (
                    SELECT LISTAGG(DISTINCT NVL(c.ssd_challan_category, 'Full_semester'), ',')
                               WITHIN GROUP (ORDER BY NVL(c.ssd_challan_category, 'Full_semester'))
                    FROM students_scholarships_details c
                    WHERE c.ssd_sess_id = :session_id
                      AND NVL(c.status, 'E') = 'E'
                      AND NVL(c.ssd_percentage, 0) > 0
                      AND UPPER(TRIM(c.ssd_sai_enrollment_no)) = UPPER(TRIM(sai.sai_enrollment_no))
                ) AS existing_categories,
                -- Mirrors studentHasHostelAssignment(): a hostel scholarship needs one.
                -- The predicate is interpolated from HostelAssignmentResolver rather than
                -- written out again, so this list and the save-time guard cannot drift.
                (
                    SELECT CASE WHEN COUNT(*) > 0 THEN 1 ELSE 0 END
                    FROM hostel_assign ha
                    WHERE ha.ha_session = :session_id
                      AND {$hostelAssignedPredicate}
                      AND (ha.ha_aai_id = aai.aai_id
                           OR UPPER(TRIM(ha.ha_enrolment_no)) = UPPER(TRIM(sai.sai_enrollment_no)))
                ) AS hostel_assigned,
                TO_CHAR(:target_category_out) AS challan_category,
                TO_CHAR(aai.aai_id) AS app_id,
                TO_NUMBER(:session_id) AS sess_id
            FROM student_challan sc
            JOIN students_apply_info sai
                ON UPPER(TRIM(sai.sai_enrollment_no)) = UPPER(TRIM(sc.sc_enrollment_no))
            {$typeJoin}
            LEFT JOIN students_personal_info spi ON spi.spi_id = sai.sai_spi_id
            LEFT JOIN admission_apply_info aai
                ON UPPER(TRIM(aai.cms_sai_enrollment_no)) = UPPER(TRIM(sai.sai_enrollment_no))
            LEFT JOIN admission_personal_info api ON api.api_id = aai.aai_api_id
            LEFT JOIN schools s ON s.school_id = sai.sai_sdpo_school_id
            LEFT JOIN departments d ON d.dept_id = sai.sai_sdpo_dept_id
            LEFT JOIN programs p ON p.prog_id = sai.sai_sdpo_prog_id
            {$where}
            ORDER BY enrollment_no
        ", $bindings];
    }

    /** Every challan category a scholarship row can carry, academic + hostel. */
    protected function allChallanCategories(): array
    {
        return [
            'Full_semester',
            'Credit_hours',
            'additional_Full_semester',
            'Short/Summer_Course',
            'Hostel_Fee',
        ];
    }

    protected function fetchAcademicScholarshipRows(
        $sessionId,
        $schoolId,
        $deptId,
        $progId,
        $scholarshipTypeId,
        $studentId,
        $typeNameLike = null,
        $challanCategory = '',
        $hideZero = false,
        $activeOnly = false,
        $studentStatus = ''
    ) {
        $bindings = ['session_id' => $sessionId];
        $schoolFilter = $deptFilter = $progFilter = $typeFilter = $studentFilter = $challanCategoryFilter = '';
        $zeroFilter = '';
        $activeFilter = $activeOnly ? $this->sqlStudentIsActiveExpr(
            'NVL(aai.cms_sai_enrollment_no, ssd.ssd_sai_enrollment_no)'
        ) : '';

        $studentStatusFilter = '';
        if ($studentStatus !== '') {
            $studentStatusFilter = ' AND UPPER(TRIM(sai.sai_student_status)) = :student_status';
            $bindings['student_status'] = $studentStatus;
        }

        if ($challanCategory !== '' && $challanCategory !== 'Hostel_Fee') {
            $challanCategoryFilter = " AND NVL(ssd.ssd_challan_category, 'Full_semester') = :challan_category";
            $bindings['challan_category'] = $challanCategory;
        }

        if ($hideZero) {
            // Keep a staged zero: it is a queued removal, not clutter.
            $zeroFilter = " AND (NVL(ssd.ssd_percentage, 0) > 0"
                . ($this->hasSsdBulkPostStatusColumn()
                    ? " OR UPPER(TRIM(NVL(ssd.ssd_bulk_post_status, ''))) = 'PENDING'"
                    : '')
                . ')';
        }

        if ($schoolId) {
            $schoolFilter = ' AND NVL(aai.aai_sdpo_school_id, sai.sai_sdpo_school_id) = :school_id';
            $bindings['school_id'] = $schoolId;
        }
        if ($deptId) {
            $deptFilter = ' AND NVL(aai.aai_sdpo_dept_id, sai.sai_sdpo_dept_id) = :dept_id';
            $bindings['dept_id'] = $deptId;
        }
        if ($progId) {
            $progFilter = ' AND NVL(aai.aai_sdpo_prog_id, sai.sai_sdpo_prog_id) = :prog_id';
            $bindings['prog_id'] = $progId;
        }
        if ($scholarshipTypeId) {
            $typeFilter = ' AND st.scholarship_id = :scholarship_type';
            $bindings['scholarship_type'] = $scholarshipTypeId;
        } elseif ($typeNameLike) {
            $typeFilter = ' AND UPPER(st.scholarship_name) LIKE :scholarship_type_name';
            $bindings['scholarship_type_name'] = '%' . strtoupper(trim((string) $typeNameLike)) . '%';
        }
        if ($studentId !== '') {
            $studentFilter = ' AND UPPER(TRIM(NVL(aai.cms_sai_enrollment_no, ssd.ssd_sai_enrollment_no))) LIKE :student_id';
            $bindings['student_id'] = '%' . $studentId . '%';
        }
        // Both scholarship pages stage into this table; only post what this one owns.
        $sourceScope = $this->bulkSourceScopeSql('ssd');

        $sql = "
            SELECT
                TO_CHAR(ssd.ssd_id) AS record_id,
                CAST('academic' AS VARCHAR2(20)) AS category,
                TO_CHAR(NVL(aai.cms_sai_enrollment_no, ssd.ssd_sai_enrollment_no)) AS enrollment_no,
                TO_CHAR(NVL(api.api_name, spi.spi_name)) AS student_name,
                TO_CHAR(NVL(s.school_code, s.school_name)) AS school_name,
                TO_CHAR(s.school_name) AS school_full_name,
                TO_CHAR(d.dept_name) AS dept_name,
                TO_CHAR(p.prog_name) AS prog_name,
                TO_CHAR(sess.sess_code) AS sess_code,
                TO_CHAR(sess.sess_name) AS sess_name,
                TO_NUMBER(st.scholarship_id) AS scholarship_id,
                TO_CHAR(st.scholarship_name) AS scholarship_name,
                TO_NUMBER(NVL(ssd.ssd_percentage, 0)) AS scholarship_percentage,
                {$this->sqlAcademicScholarshipAmountExpr('ssd')},
                TO_NUMBER(NVL(ssd.ssd_arrear, 0)) AS arrear,
                CASE WHEN sai.sai_enrollment_no IS NOT NULL THEN cgpa(sai.sai_enrollment_no) END AS cgpa,
                TO_CHAR(sai.sai_student_status) AS student_status,
                {$this->sqlAcademicBulkPostStatusExpr('ssd')},
                {$this->sqlAcademicSourcePageExpr('ssd')},
                {$this->sqlAcademicSnapshotBaseExpr('ssd')},
                {$this->sqlAcademicOtherPctExpr('ssd')},
                TO_CHAR(NVL(ssd.ssd_challan_category, 'Full_semester')) AS challan_category,
                TO_CHAR(ssd.ssd_app_id) AS app_id,
                TO_NUMBER(ssd.ssd_sess_id) AS sess_id
            FROM students_scholarships_details ssd
            JOIN scholarship_type st
                ON st.scholarship_id = ssd.ssd_type
            JOIN academic_sessions sess
                ON sess.sess_id = ssd.ssd_sess_id
            LEFT JOIN admission_apply_info aai
                ON aai.aai_id = ssd.ssd_app_id
            LEFT JOIN admission_personal_info api
                ON api.api_id = aai.aai_api_id
            LEFT JOIN students_apply_info sai
                ON UPPER(TRIM(sai.sai_enrollment_no)) = UPPER(TRIM(NVL(aai.cms_sai_enrollment_no, ssd.ssd_sai_enrollment_no)))
            LEFT JOIN students_personal_info spi
                ON spi.spi_id = sai.sai_spi_id
            LEFT JOIN schools s
                ON s.school_id = NVL(aai.aai_sdpo_school_id, sai.sai_sdpo_school_id)
            LEFT JOIN departments d
                ON d.dept_id = NVL(aai.aai_sdpo_dept_id, sai.sai_sdpo_dept_id)
            LEFT JOIN programs p
                ON p.prog_id = NVL(aai.aai_sdpo_prog_id, sai.sai_sdpo_prog_id)
            WHERE NVL(ssd.status, 'E') = 'E'
              AND ssd.ssd_sess_id = :session_id
              {$schoolFilter}
              {$deptFilter}
              {$progFilter}
              {$typeFilter}
              {$studentFilter}
              {$challanCategoryFilter}
              {$zeroFilter}
              {$activeFilter}
              {$studentStatusFilter}
            ORDER BY enrollment_no, scholarship_name
        ";

        return DB::select($sql, $bindings);
    }

    /** The only two student statuses a scholarship may be held or awarded under. */
    protected function eligibleStudentStatuses(): array
    {
        return ['ACTIVE', 'EXPECTED-GRADUATE'];
    }

    /**
     * "Is this student still on the books?" — the eligibility test for every list and
     * every award path on this page.
     *
     * Enrolment or a challan is deliberately NOT the test: a student can be perfectly
     * eligible for a scholarship before anything has been billed to them this session.
     * SAI_STUDENT_STATUS is the field that actually says whether they are still here.
     *
     * Phrased as NOT EXISTS(disqualifying row) rather than EXISTS(qualifying row), which
     * is not the same thing here. A scholarship can be awarded to a NEW ADMISSION student
     * who has an applicant id but no enrolment number yet, so no STUDENTS_APPLY_INFO row
     * exists to carry a status — 141 of the current session's 568 rows are exactly that.
     * EXISTS would hide every one of them as if they were ineligible. This form excludes
     * only students the table positively says have gone (Left, Stuck-Off, Convocated,
     * Dormant, Drop-Out, Defer) and leaves the not-yet-enrolled alone.
     *
     * Safe against duplicates: every enrolment in STUDENTS_APPLY_INFO is unique and
     * carries exactly one status (verified — 17,467 enrolments, one row each).
     *
     * @param string $enrollmentExpr the SQL expression naming the enrollment in the outer query
     */
    protected function sqlStudentIsActiveExpr(string $enrollmentExpr): string
    {
        $allowed = "'" . implode("', '", $this->eligibleStudentStatuses()) . "'";

        return "
              AND NOT EXISTS (
                  SELECT 1 FROM students_apply_info sai_act
                  WHERE UPPER(TRIM(sai_act.sai_enrollment_no)) = UPPER(TRIM({$enrollmentExpr}))
                    AND UPPER(TRIM(NVL(sai_act.sai_student_status, ''))) NOT IN ({$allowed})
              )";
    }

    protected function fetchHostelScholarshipRows(
        $sessionId,
        $schoolId,
        $deptId,
        $progId,
        $scholarshipTypeId,
        $studentId,
        $typeNameLike = null,
        $hideZero = false,
        $activeOnly = false,
        $studentStatus = ''
    ) {
        $bindings = ['session_id' => $sessionId];
        $schoolFilter = $deptFilter = $progFilter = $typeFilter = $studentFilter = '';
        $zeroFilter = '';
        $activeFilter = $activeOnly ? $this->sqlStudentIsActiveExpr(
            'NVL(sshd.sshd_sai_enrollment_no, aai.cms_sai_enrollment_no)'
        ) : '';

        $studentStatusFilter = '';
        if ($studentStatus !== '') {
            $studentStatusFilter = ' AND UPPER(TRIM(sai.sai_student_status)) = :student_status';
            $bindings['student_status'] = $studentStatus;
        }

        if ($hideZero) {
            $zeroFilter = " AND (NVL(sshd.sshd_percentage, 0) > 0"
                . ($this->hasSshdBulkPostStatusColumn()
                    ? " OR UPPER(TRIM(NVL(sshd.sshd_bulk_post_status, ''))) = 'PENDING'"
                    : '')
                . ')';
        }

        // Prefer resolved school/dept/prog; if master missing, still allow match via enrollment SAI.
        if ($schoolId) {
            $schoolFilter = "
                AND (
                    NVL(aai.aai_sdpo_school_id, sai.sai_sdpo_school_id) = :school_id
                    OR EXISTS (
                        SELECT 1 FROM students_apply_info sai_s
                        WHERE UPPER(TRIM(sai_s.sai_enrollment_no)) = UPPER(TRIM(NVL(sshd.sshd_sai_enrollment_no, aai.cms_sai_enrollment_no)))
                          AND sai_s.sai_sdpo_school_id = :school_id_ex
                    )
                )
            ";
            $bindings['school_id'] = $schoolId;
            $bindings['school_id_ex'] = $schoolId;
        }
        if ($deptId) {
            $deptFilter = "
                AND (
                    NVL(aai.aai_sdpo_dept_id, sai.sai_sdpo_dept_id) = :dept_id
                    OR EXISTS (
                        SELECT 1 FROM students_apply_info sai_d
                        WHERE UPPER(TRIM(sai_d.sai_enrollment_no)) = UPPER(TRIM(NVL(sshd.sshd_sai_enrollment_no, aai.cms_sai_enrollment_no)))
                          AND sai_d.sai_sdpo_dept_id = :dept_id_ex
                    )
                )
            ";
            $bindings['dept_id'] = $deptId;
            $bindings['dept_id_ex'] = $deptId;
        }
        if ($progId) {
            $progFilter = "
                AND (
                    NVL(aai.aai_sdpo_prog_id, sai.sai_sdpo_prog_id) = :prog_id
                    OR EXISTS (
                        SELECT 1 FROM students_apply_info sai_p
                        WHERE UPPER(TRIM(sai_p.sai_enrollment_no)) = UPPER(TRIM(NVL(sshd.sshd_sai_enrollment_no, aai.cms_sai_enrollment_no)))
                          AND sai_p.sai_sdpo_prog_id = :prog_id_ex
                    )
                )
            ";
            $bindings['prog_id'] = $progId;
            $bindings['prog_id_ex'] = $progId;
        }
        if ($scholarshipTypeId) {
            $typeFilter = ' AND st.scholarship_id = :scholarship_type';
            $bindings['scholarship_type'] = $scholarshipTypeId;
        } elseif ($typeNameLike) {
            // BEEF → match "BEEF Hostel", "BEEF HOSTEL", etc.
            $typeFilter = ' AND UPPER(NVL(st.scholarship_name, \'\')) LIKE :scholarship_type_name';
            $bindings['scholarship_type_name'] = '%' . strtoupper(trim((string) $typeNameLike)) . '%';
        }
        if ($studentId !== '') {
            $studentFilter = "
                AND (
                    UPPER(TRIM(sshd.sshd_sai_enrollment_no)) LIKE :student_id
                    OR UPPER(TRIM(aai.cms_sai_enrollment_no)) LIKE :student_id_aai
                    OR UPPER(TRIM(sai.sai_enrollment_no)) LIKE :student_id_sai
                )
            ";
            $bindings['student_id'] = '%' . $studentId . '%';
            $bindings['student_id_aai'] = '%' . $studentId . '%';
            $bindings['student_id_sai'] = '%' . $studentId . '%';
        }

        // FIN_STUDENTS_SCHOLARSHIPS_HOSTEL_DETAILS — same source as student detail drawer
        $sql = "
            SELECT
                TO_CHAR(sshd.sshd_id) AS record_id,
                CAST('hostel' AS VARCHAR2(20)) AS category,
                TO_CHAR(NVL(sshd.sshd_sai_enrollment_no, aai.cms_sai_enrollment_no)) AS enrollment_no,
                TO_CHAR(NVL(api.api_name, spi.spi_name)) AS student_name,
                TO_CHAR(NVL(s.school_code, s.school_name)) AS school_name,
                TO_CHAR(s.school_name) AS school_full_name,
                TO_CHAR(d.dept_name) AS dept_name,
                TO_CHAR(p.prog_name) AS prog_name,
                TO_CHAR(sess.sess_code) AS sess_code,
                TO_CHAR(sess.sess_name) AS sess_name,
                TO_NUMBER(st.scholarship_id) AS scholarship_id,
                TO_CHAR(NVL(st.scholarship_name, 'Hostel Scholarship')) AS scholarship_name,
                TO_NUMBER(NVL(sshd.sshd_percentage, 0)) AS scholarship_percentage,
                TO_NUMBER(NVL(sshd.sshd_amount, 0)) AS scholarship_amount,
                TO_NUMBER(NVL(sshd.sshd_hostel_fee, 0)) AS hostel_fee,
                CASE WHEN sai.sai_enrollment_no IS NOT NULL THEN cgpa(sai.sai_enrollment_no) END AS cgpa,
                TO_CHAR(sai.sai_student_status) AS student_status,
                {$this->sqlHostelBulkPostStatusExpr('sshd')},
                {$this->sqlHostelSourcePageExpr('sshd')},
                {$this->sqlHostelSnapshotBaseExpr('sshd')},
                {$this->sqlHostelOtherPctExpr('sshd')},
                CAST('Hostel_Fee' AS VARCHAR2(50)) AS challan_category,
                TO_CHAR(sshd.sshd_app_id) AS app_id,
                TO_NUMBER(sshd.sshd_sess_id) AS sess_id
            FROM FIN_STUDENTS_SCHOLARSHIPS_HOSTEL_DETAILS sshd
            LEFT JOIN scholarship_type st
                ON st.scholarship_id = sshd.sshd_type
            LEFT JOIN academic_sessions sess
                ON sess.sess_id = sshd.sshd_sess_id
            LEFT JOIN admission_apply_info aai
                ON aai.aai_id = sshd.sshd_app_id
            LEFT JOIN admission_personal_info api
                ON api.api_id = aai.aai_api_id
            LEFT JOIN students_apply_info sai
                ON UPPER(TRIM(sai.sai_enrollment_no)) = UPPER(TRIM(NVL(sshd.sshd_sai_enrollment_no, aai.cms_sai_enrollment_no)))
            LEFT JOIN students_personal_info spi
                ON spi.spi_id = sai.sai_spi_id
            LEFT JOIN schools s
                ON s.school_id = NVL(aai.aai_sdpo_school_id, sai.sai_sdpo_school_id)
            LEFT JOIN departments d
                ON d.dept_id = NVL(aai.aai_sdpo_dept_id, sai.sai_sdpo_dept_id)
            LEFT JOIN programs p
                ON p.prog_id = NVL(aai.aai_sdpo_prog_id, sai.sai_sdpo_prog_id)
            WHERE NVL(TRIM(sshd.sshd_status), 'E') = 'E'
              AND sshd.sshd_sess_id = :session_id
              {$schoolFilter}
              {$deptFilter}
              {$progFilter}
              {$typeFilter}
              {$studentFilter}
              {$zeroFilter}
              {$activeFilter}
              {$studentStatusFilter}
            ORDER BY enrollment_no, scholarship_name
        ";

        try {
            return DB::select($sql, $bindings);
        } catch (\Throwable $e) {
            // Fallback if percentage / status casting fails on some rows
            \Log::warning('Hostel list primary query failed, using fallback: ' . $e->getMessage());

            $fallbackSql = "
                SELECT
                    TO_CHAR(sshd.sshd_id) AS record_id,
                    CAST('hostel' AS VARCHAR2(20)) AS category,
                    TO_CHAR(NVL(sshd.sshd_sai_enrollment_no, aai.cms_sai_enrollment_no)) AS enrollment_no,
                    TO_CHAR(NVL(api.api_name, '—')) AS student_name,
                    TO_CHAR(NULL) AS school_name,
                    TO_CHAR(NULL) AS dept_name,
                    TO_CHAR(NULL) AS prog_name,
                    TO_CHAR(sess.sess_code) AS sess_code,
                    TO_CHAR(sess.sess_name) AS sess_name,
                    TO_NUMBER(sshd.sshd_type) AS scholarship_id,
                    TO_CHAR(NVL(st.scholarship_name, 'Hostel Scholarship')) AS scholarship_name,
                    TO_NUMBER(NVL(sshd.sshd_percentage, 0)) AS scholarship_percentage,
                    CAST('Hostel_Fee' AS VARCHAR2(50)) AS challan_category,
                    TO_CHAR(sshd.sshd_app_id) AS app_id,
                    TO_NUMBER(sshd.sshd_sess_id) AS sess_id
                FROM FIN_STUDENTS_SCHOLARSHIPS_HOSTEL_DETAILS sshd
                LEFT JOIN scholarship_type st ON st.scholarship_id = sshd.sshd_type
                LEFT JOIN academic_sessions sess ON sess.sess_id = sshd.sshd_sess_id
                LEFT JOIN admission_apply_info aai ON aai.aai_id = sshd.sshd_app_id
                LEFT JOIN admission_personal_info api ON api.api_id = aai.aai_api_id
                WHERE NVL(TRIM(sshd.sshd_status), 'E') = 'E'
                  AND sshd.sshd_sess_id = :session_id
                  {$typeFilter}
                  {$activeFilter}
                  " . ($studentId !== '' ? " AND UPPER(TRIM(NVL(sshd.sshd_sai_enrollment_no, aai.cms_sai_enrollment_no))) LIKE :student_id_fb" : "") . "
            ";
            $fbBindings = ['session_id' => $sessionId];
            if ($scholarshipTypeId) {
                $fbBindings['scholarship_type'] = $scholarshipTypeId;
            } elseif ($typeNameLike) {
                $fbBindings['scholarship_type_name'] = '%' . strtoupper(trim((string) $typeNameLike)) . '%';
            }
            if ($studentId !== '') {
                $fbBindings['student_id_fb'] = '%' . $studentId . '%';
            }

            return DB::select($fallbackSql, $fbBindings);
        }
    }

    private function scholarshipTypeMeta(?int $typeId): array
    {
        if (!$typeId) {
            return ['desc' => 'academic', 'name' => ''];
        }

        $row = DB::table('scholarship_type')
            ->select('scholarship_desc', 'scholarship_name')
            ->where('scholarship_id', $typeId)
            ->first();

        $desc = strtolower(trim((string) ($row->scholarship_desc ?? $row->SCHOLARSHIP_DESC ?? 'academic')));
        $name = trim((string) ($row->scholarship_name ?? $row->SCHOLARSHIP_NAME ?? ''));

        return [
            'desc' => $desc === 'hostel' ? 'hostel' : 'academic',
            'name' => $name,
        ];
    }

    private function scholarshipTypeDesc(?int $typeId): string
    {
        return $this->scholarshipTypeMeta($typeId)['desc'];
    }

    /**
     * Split one Scholarship Type selection into the per-table filters it implies.
     *
     * The type filter applies to BOTH categories, by ID on its own side and by related
     * name on the other:
     *   BEEF (academic) → academic ID + hostel name LIKE '%BEEF%'
     *   BEEF Hostel     → hostel ID   + academic name LIKE '%BEEF%'
     *
     * @return array{academic_type_id: ?int, hostel_type_id: ?int,
     *               academic_name_like: ?string, hostel_name_like: ?string,
     *               is_hostel_type: bool}
     */
    protected function resolveTypeFilters(?int $scholarshipTypeId): array
    {
        $out = [
            'academic_type_id' => null,
            'hostel_type_id' => null,
            'academic_name_like' => null,
            'hostel_name_like' => null,
            'is_hostel_type' => false,
        ];

        if (!$scholarshipTypeId) {
            return $out;
        }

        $typeMeta = $this->scholarshipTypeMeta($scholarshipTypeId);
        $typeDesc = $typeMeta['desc'];
        $out['is_hostel_type'] = ($typeDesc === 'hostel');

        $baseName = trim(preg_replace('/\bhostel\b/i', '', $typeMeta['name']));
        $baseName = trim(preg_replace('/\s+/', ' ', $baseName));

        if ($typeDesc === 'hostel') {
            $out['hostel_type_id'] = $scholarshipTypeId;
            if ($baseName !== '') {
                $out['academic_name_like'] = $baseName;
            }
        } else {
            $out['academic_type_id'] = $scholarshipTypeId;
            if ($baseName !== '') {
                $out['hostel_name_like'] = $baseName;
            }
        }

        return $out;
    }

    /**
     * Detail panel data for one student (all active scholarships in session).
     */
    public function getStudentDetail(Request $request)
    {
        $enrollmentNo = strtoupper(trim((string) $request->input('enrollment_no')));
        $sessionId = (int) $request->input('session_id');
        // A scholarship row is keyed by ssd_app_id, and 142 rows in F2026 belong to
        // applications whose CMS_SAI_ENROLLMENT_NO is NULL — the only link to
        // students_apply_info. Those students have no enrolment number to be opened by,
        // so the application id is accepted as an alternative identifier.
        $requestedAaiId = $this->normalizeId($request->input('aai_id'));

        if (($enrollmentNo === '' && !$requestedAaiId) || !$sessionId) {
            return response()->json([
                'status' => false,
                'message' => 'A student ID or application ID, plus the session, are required.',
            ], 422);
        }

        try {
        $profile = null;

        if ($enrollmentNo !== '') {
            $profile = DB::selectOne("
                SELECT
                    NVL(aai.cms_sai_enrollment_no, sai.sai_enrollment_no) AS enrollment_no,
                    NVL(api.api_name, spi.spi_name) AS student_name,
                    s.school_name,
                    d.dept_name,
                    p.prog_name,
                    sai.sai_student_status AS student_status,
                    aai.aai_id AS aai_id,
                    CASE WHEN sai.sai_enrollment_no IS NOT NULL THEN cgpa(sai.sai_enrollment_no) END AS cgpa
                FROM students_apply_info sai
                LEFT JOIN students_personal_info spi ON spi.spi_id = sai.sai_spi_id
                LEFT JOIN admission_apply_info aai
                    ON UPPER(TRIM(aai.cms_sai_enrollment_no)) = UPPER(TRIM(sai.sai_enrollment_no))
                LEFT JOIN admission_personal_info api ON api.api_id = aai.aai_api_id
                LEFT JOIN schools s ON s.school_id = sai.sai_sdpo_school_id
                LEFT JOIN departments d ON d.dept_id = sai.sai_sdpo_dept_id
                LEFT JOIN programs p ON p.prog_id = sai.sai_sdpo_prog_id
                WHERE UPPER(TRIM(sai.sai_enrollment_no)) = :enrollment_no
            ", ['enrollment_no' => $enrollmentNo]);
        }

        // No enrolment, or one that resolves to nothing: build the profile from the
        // application record instead. Its own SDPO columns carry school / department /
        // programme, so the drawer header reads the same either way.
        if (!$profile && $requestedAaiId) {
            $profile = DB::selectOne("
                SELECT
                    aai.cms_sai_enrollment_no AS enrollment_no,
                    api.api_name AS student_name,
                    s.school_name,
                    d.dept_name,
                    p.prog_name,
                    CAST(NULL AS VARCHAR2(50)) AS student_status,
                    aai.aai_id AS aai_id,
                    CAST(NULL AS NUMBER) AS cgpa
                FROM admission_apply_info aai
                LEFT JOIN admission_personal_info api ON api.api_id = aai.aai_api_id
                LEFT JOIN schools s ON s.school_id = aai.aai_sdpo_school_id
                LEFT JOIN departments d ON d.dept_id = aai.aai_sdpo_dept_id
                LEFT JOIN programs p ON p.prog_id = aai.aai_sdpo_prog_id
                WHERE aai.aai_id = :aai_id
            ", ['aai_id' => $requestedAaiId]);
        }

        // Reachable now that the page can open a student who has no scholarship yet —
        // say plainly that the identifier is unknown rather than opening a blank drawer.
        if (!$profile) {
            return response()->json([
                'status' => false,
                'message' => $enrollmentNo !== ''
                    ? 'No student found with enrollment "' . $enrollmentNo . '".'
                    : 'No application found with ID "' . $requestedAaiId . '".',
            ], 404);
        }

        $aaiId = $profile->aai_id ?? $profile->AAI_ID ?? null;
        $aaiId = ($aaiId !== null && $aaiId !== '') ? (int) $aaiId : $requestedAaiId;

        // The scholarship queries below match on enrolment OR app id; with no enrolment
        // the app id is the only handle, so it has to be the one that binds.
        $enrollmentNo = strtoupper(trim((string) ($profile->enrollment_no ?? $profile->ENROLLMENT_NO ?? $enrollmentNo)));

        $academicSql = "
            SELECT
                TO_CHAR(ssd.ssd_id) AS record_id,
                CAST('academic' AS VARCHAR2(20)) AS category,
                TO_NUMBER(st.scholarship_id) AS scholarship_id,
                TO_CHAR(st.scholarship_name) AS scholarship_name,
                TO_NUMBER(NVL(ssd.ssd_percentage, 0)) AS scholarship_percentage,
                {$this->sqlAcademicScholarshipAmountExpr('ssd')},
                TO_NUMBER(NVL(ssd.ssd_arrear, 0)) AS arrear,
                TO_CHAR(NVL(ssd.ssd_challan_category, 'Full_semester')) AS challan_category,
                CAST(NULL AS VARCHAR2(50)) AS hostel_amount,
                CAST(NULL AS VARCHAR2(50)) AS hostel_fee,
                TO_CHAR(sess.sess_code) AS sess_code
            FROM students_scholarships_details ssd
            JOIN scholarship_type st ON st.scholarship_id = ssd.ssd_type
            JOIN academic_sessions sess ON sess.sess_id = ssd.ssd_sess_id
            WHERE ssd.status = 'E'
              AND ssd.ssd_sess_id = :session_id
              AND (
                    UPPER(TRIM(ssd.ssd_sai_enrollment_no)) = :enrollment_no
                    " . ($aaiId ? " OR ssd.ssd_app_id = :aai_id" : "") . "
                  )
            ORDER BY st.scholarship_name
        ";
        $academicBindings = [
            'session_id' => $sessionId,
            'enrollment_no' => $enrollmentNo,
        ];
        if ($aaiId) {
            $academicBindings['aai_id'] = $aaiId;
        }
        $academic = DB::select($academicSql, $academicBindings);

        // Per-component arrears (fin_other_scholarships) for each academic row's own
        // (type, challan category) — the drawer's Arrears card edits these directly;
        // ssd_arrear above is just their persisted sum.
        $arrearsRows = $this->getArrearsRowsForStudent($aaiId, $sessionId, $enrollmentNo);
        $arrearsMap = [];
        foreach ($arrearsRows as $ar) {
            $key = ((int) $ar->fos_scholarship_type) . '|' . $ar->fos_challan_category;
            $arrearsMap[$key] = [
                'admission' => (float) $ar->fos_admission_fee,
                'security' => (float) $ar->fos_security_fee,
                'activity' => (float) $ar->fos_activity_charges,
                'medical' => (float) $ar->fos_medical_insurance,
            ];
        }
        $zeroArrears = ['admission' => 0, 'security' => 0, 'activity' => 0, 'medical' => 0];
        foreach ($academic as $row) {
            $key = ((int) ($row->scholarship_id ?? $row->SCHOLARSHIP_ID ?? 0)) . '|'
                . (string) ($row->challan_category ?? $row->CHALLAN_CATEGORY ?? 'Full_semester');
            $row->arrears = $arrearsMap[$key] ?? $zeroArrears;
        }

        // Hostel scholarships — FIN_STUDENTS_SCHOLARSHIPS_HOSTEL_DETAILS only
        $hostelSql = "
            SELECT
                TO_CHAR(sshd.sshd_id) AS record_id,
                CAST('hostel' AS VARCHAR2(20)) AS category,
                TO_NUMBER(st.scholarship_id) AS scholarship_id,
                TO_CHAR(st.scholarship_name) AS scholarship_name,
                TO_NUMBER(NVL(sshd.sshd_percentage, 0)) AS scholarship_percentage,
                CAST('Hostel_Fee' AS VARCHAR2(50)) AS challan_category,
                TO_CHAR(NVL(sshd.sshd_amount, 0)) AS hostel_amount,
                TO_CHAR(NVL(sshd.sshd_hostel_fee, 0)) AS hostel_fee,
                TO_CHAR(sess.sess_code) AS sess_code
            FROM fin_students_scholarships_hostel_details sshd
            JOIN scholarship_type st ON st.scholarship_id = sshd.sshd_type
            JOIN academic_sessions sess ON sess.sess_id = sshd.sshd_sess_id
            LEFT JOIN admission_apply_info aai ON aai.aai_id = sshd.sshd_app_id
            WHERE NVL(sshd.sshd_status, 'E') = 'E'
              AND sshd.sshd_sess_id = :session_id
              AND (
                    UPPER(TRIM(sshd.sshd_sai_enrollment_no)) = :enrollment_no
                    OR UPPER(TRIM(aai.cms_sai_enrollment_no)) = :enrollment_no_aai
                    " . ($aaiId ? " OR sshd.sshd_app_id = :aai_id" : "") . "
                  )
            ORDER BY st.scholarship_name
        ";
        $hostelBindings = [
            'session_id' => $sessionId,
            'enrollment_no' => $enrollmentNo,
            'enrollment_no_aai' => $enrollmentNo,
        ];
        if ($aaiId) {
            $hostelBindings['aai_id'] = $aaiId;
        }
        $hostel = DB::select($hostelSql, $hostelBindings);

        $scholarships = array_merge($academic ?: [], $hostel ?: []);

        foreach ($scholarships as $idx => $row) {
            $cat = strtolower(trim((string) ($row->category ?? $row->CATEGORY ?? 'academic')));
            if ($cat !== 'hostel') {
                $challanCat = trim((string) ($row->challan_category ?? $row->CHALLAN_CATEGORY ?? 'Full_semester')) ?: 'Full_semester';
                $locked = $this->isScholarshipRowLockedByChallan($challanCat, $aaiId, $enrollmentNo, $sessionId);
                $row->is_locked = $locked ? 1 : 0;
                $scholarships[$idx] = $row;
            }
        }

        $totalPercentage = 0;
        foreach ($scholarships as $row) {
            $totalPercentage += (float) ($row->scholarship_percentage ?? $row->SCHOLARSHIP_PERCENTAGE ?? 0);
        }

        $challanSummary = DB::select("
            SELECT
                sc.sc_challan_type AS fee_type,
                NVL(SUM(NVL(sc.invoiceamount, 0)), 0) AS fee_amount,
                NVL(SUM(NVL(sc.sc_scholarship_amount, 0)), 0) AS scholarship_amount
            FROM student_challan sc
            WHERE UPPER(TRIM(sc.sc_enrollment_no)) = :enrollment_no
              AND sc.sc_session_id = :session_id
              AND sc.sc_challan_status != 'CANCELLED'
              AND sc.invoice_status = 'E'
            GROUP BY sc.sc_challan_type
            ORDER BY sc.sc_challan_type
        ", [
            'enrollment_no' => $enrollmentNo,
            'session_id' => $sessionId,
        ]);

        $totalFee = 0;
        $totalScholarshipAmount = 0;
        foreach ($challanSummary as $row) {
            $totalFee += (float) ($row->fee_amount ?? $row->FEE_AMOUNT ?? 0);
            $totalScholarshipAmount += (float) ($row->scholarship_amount ?? $row->SCHOLARSHIP_AMOUNT ?? 0);
        }

        return response()->json([
            'status' => true,
            'profile' => $profile,
            'scholarships' => $scholarships,
            'challan_summary' => $challanSummary,
            // Managed on the single Student Record page: the drawer still shows the whole
            // picture, but every control in it goes read-only.
            'single_page_locked' => $this->studentIsSinglePageOwned($sessionId, $enrollmentNo),
            'totals' => [
                'scholarship_percentage' => $totalPercentage,
                'fee_amount' => $totalFee,
                'scholarship_amount' => $totalScholarshipAmount,
            ],
        ]);
        } catch (\Throwable $e) {
            \Log::error('Scholarship student-detail load failed: ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Unable to load student detail. Please try again.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Fee matrix for the scholarship-form drawer.
     *
     * Same resolver as Student Record Revised, but with fallbacks so Full Tuition
     * still shows when Master Fee Structure has NEW ADMISSION (not EXISTING), or
     * when only a challan / loose fee row is available for the program.
     */
    public function getFeeMatrix(Request $request)
    {
        $sessionId = (int) $request->input('session_id');
        $enrollmentNo = strtoupper(trim((string) $request->input('enrollment_no')));

        if ($enrollmentNo === '' || !$sessionId) {
            return response()->json([
                'status' => false,
                'message' => 'Enrollment and session are required.',
                'categories' => (object) [],
            ], 422);
        }

        try {
        $categories = [
            'Full_semester',
            'Credit_hours',
            'additional_Full_semester',
            'Short/Summer_Course',
        ];

        $typesToTry = ['Existing'];
        $sessName = DB::table('academic_sessions')->where('sess_id', $sessionId)->value('sess_name');
        $season = $sessName ? strtoupper(strtok(trim((string) $sessName), ' ')) : '';
        if ($season !== '') {
            $typesToTry[] = 'New Admission ' . $season;
        }

        $out = [];
        foreach ($categories as $category) {
            $bestFail = null;
            $resolved = null;

            foreach ($typesToTry as $studentType) {
                $res = $this->resolveAcademicFeeStructure($sessionId, $enrollmentNo, $studentType, $category);
                if (!empty($res['ok'])) {
                    $resolved = $res;
                    $resolved['source'] = 'fee_structure:' . $studentType;
                    break;
                }
                if ($bestFail === null) {
                    $bestFail = $res;
                }
            }

            if (($resolved === null || empty($resolved['ok'])) && $category === 'Full_semester') {
                $fallback = $this->resolveFullTuitionFallback($sessionId, $enrollmentNo);
                if (!empty($fallback['ok'])) {
                    $resolved = $fallback;
                }
            }

            if ($resolved !== null && !empty($resolved['ok'])) {
                $out[$category] = [
                    'ok' => true,
                    'reason' => null,
                    'switchTo' => null,
                    'message' => '1',
                    'referenceFee' => $resolved['referenceFee'],
                    'referenceLabel' => $resolved['referenceLabel'],
                    'amountBase' => $resolved['amountBase'],
                    'feeLineTotal' => $resolved['feeLineTotal'],
                    'creditHours' => $resolved['creditHours'],
                    'repeatCredits' => isset($resolved['repeatCredits']) ? $resolved['repeatCredits'] : 0,
                    'oneTimeFeeTotal' => $resolved['oneTimeFeeTotal'],
                    'arrearsApplicable' => $resolved['arrearsApplicable'],
                    'arrearComponents' => $resolved['arrearComponents'],
                    'source' => isset($resolved['source']) ? $resolved['source'] : null,
                ];
                continue;
            }

            $fail = $bestFail ?: ['reason' => 'no_fee_structure', 'message' => 'Fee structure could not be resolved.'];
            $out[$category] = [
                'ok' => false,
                'reason' => isset($fail['reason']) ? $fail['reason'] : null,
                'switchTo' => isset($fail['switchTo']) ? $fail['switchTo'] : null,
                'message' => isset($fail['message']) ? $fail['message'] : 'Fee structure could not be resolved.',
                'referenceFee' => null,
                'referenceLabel' => 'Tuition Fee',
                'amountBase' => 0,
                'feeLineTotal' => 0,
                'creditHours' => null,
                'repeatCredits' => 0,
                'oneTimeFeeTotal' => 0,
                'arrearsApplicable' => false,
                'arrearComponents' => ['admission' => 0, 'security' => 0, 'activity' => 0, 'medical' => 0],
            ];
        }

        return response()->json([
            'status' => true,
            'message' => '1',
            'categories' => $out,
        ]);
        } catch (\Throwable $e) {
            \Log::error('Scholarship fee-matrix load failed: ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Unable to resolve fee structure. Please try again.',
                'error' => config('app.debug') ? $e->getMessage() : null,
                'categories' => (object) [],
            ], 500);
        }
    }

    /**
     * Last-resort Full Tuition amount when EXISTING / NEW ADMISSION fee rows miss.
     */
    protected function resolveFullTuitionFallback($sessionId, $enrollmentNo)
    {
        $challan = DB::selectOne("
            SELECT NVL(SUM(NVL(sc.invoiceamount, 0)), 0) AS amt
            FROM student_challan sc
            WHERE UPPER(TRIM(sc.sc_enrollment_no)) = :enrollment_no
              AND sc.sc_session_id = :session_id
              AND sc.sc_challan_type = 'Full_semester'
              AND sc.sc_challan_status != 'CANCELLED'
              AND sc.invoice_status = 'E'
        ", [
            'enrollment_no' => $enrollmentNo,
            'session_id' => $sessionId,
        ]);

        $challanAmt = (float) ($challan->amt ?? $challan->AMT ?? 0);
        if ($challanAmt > 0) {
            return [
                'ok' => true,
                'reason' => null,
                'message' => '1',
                'feeType' => 'Full_semester',
                'referenceFee' => $challanAmt,
                'referenceLabel' => 'Tuition (from challan)',
                'amountBase' => $challanAmt,
                'feeLineTotal' => $challanAmt,
                'creditHours' => null,
                'repeatCredits' => 0,
                'oneTimeFeeTotal' => 0,
                'arrearsApplicable' => true,
                'arrearComponents' => ['admission' => 0, 'security' => 0, 'activity' => 0, 'medical' => 0],
                'source' => 'challan',
            ];
        }

        $stu = DB::table('students_apply_info')
            ->select(
                'SAI_SDPO_SCHOOL_ID as school_id',
                'SAI_SDPO_DEPT_ID as dept_id',
                'SAI_SDPO_PROG_ID as prog_id',
                'SAI_SDPO_SESS_ID as roadmap_sess'
            )
            ->whereRaw('LOWER(SAI_ENROLLMENT_NO) = ?', [strtolower((string) $enrollmentNo)])
            ->first();

        if (empty($stu)) {
            return ['ok' => false];
        }

        // Prefer EXISTING, then NEW ADMISSION*, then any row — ignore intake mismatch.
        $fee = DB::table('fin_fee_structure')
            ->select(
                'FFS_TUTIONFEE_AMT as fee_base',
                'FFS_ADMISSION_AMT as admission_amt',
                'FFS_SECURITY_AMT as security_amt',
                'FFS_ACTIVITY_AMT as activity_amt',
                'FFS_MEDICAL_AMT as medical_amt',
                'FFS_STUDENT_TYPE as student_type'
            )
            ->where('ffs_sess_id', $sessionId)
            ->where('ffs_school_id', $stu->school_id)
            ->where('ffs_dept_id', $stu->dept_id)
            ->where('ffs_prog_id', $stu->prog_id)
            ->where('ffs_fee_type', 'Full_semester')
            ->where('ffs_status', 'E')
            ->where('FFS_TUTIONFEE_AMT', '>', 0)
            ->orderByRaw("CASE
                WHEN UPPER(TRIM(ffs_student_type)) = 'EXISTING' THEN 0
                WHEN UPPER(TRIM(ffs_student_type)) LIKE 'NEW ADMISSION%' THEN 1
                ELSE 2
            END")
            ->orderByDesc('FFS_ID')
            ->first();

        if (empty($fee) || (float) $fee->fee_base <= 0) {
            return ['ok' => false];
        }

        $tuition = (float) $fee->fee_base;
        $components = [
            'admission' => (float) $fee->admission_amt,
            'security' => (float) $fee->security_amt,
            'activity' => (float) $fee->activity_amt,
            'medical' => (float) $fee->medical_amt,
        ];

        return [
            'ok' => true,
            'reason' => null,
            'message' => '1',
            'feeType' => 'Full_semester',
            'referenceFee' => $tuition,
            'referenceLabel' => 'Tuition Fee',
            'amountBase' => $tuition,
            'feeLineTotal' => $tuition,
            'creditHours' => null,
            'repeatCredits' => 0,
            'oneTimeFeeTotal' => array_sum($components),
            'arrearsApplicable' => true,
            'arrearComponents' => $components,
            'source' => 'fee_structure_loose:' . (string) ($fee->student_type ?? ''),
        ];
    }

    /**
     * Previous-session scholarships for a batch of students, for the Import button.
     *
     * Read-only. Mirrors the single page's "Import Previous Scholarships"
     * (StudentRecordRevised importPreviousScholarships / getPreviousArrears): the client
     * stages the result in the grid and NOTHING is written until Save Checked.
     *
     * Returns the CURRENT session rows alongside the previous ones because the list is
     * filtered by scholarship type — the client cannot see the student's whole set and
     * would otherwise merge against a partial picture.
     *
     * Deliberately NOT reusing StudentRecordController::getStudentAppliedScholarships():
     * that method interpolates the enrollment straight into the SQL string.
     */
    public function getPreviousScholarships(Request $request)
    {
        $sessionId = (int) $request->input('session_id');
        $enrollments = $request->input('enrollments', []);

        if (!$sessionId) {
            return response()->json(['status' => false, 'message' => 'Session is required.'], 422);
        }
        if (!is_array($enrollments) || empty($enrollments)) {
            return response()->json(['status' => false, 'message' => 'Select at least one student row.'], 422);
        }

        // Normalise + cap the batch so one click cannot ask for the whole university.
        $enrollments = array_values(array_unique(array_filter(array_map(function ($e) {
            return strtoupper(trim((string) $e));
        }, $enrollments))));
        if (empty($enrollments)) {
            return response()->json(['status' => false, 'message' => 'Select at least one student row.'], 422);
        }
        $limit = 500;
        $truncated = count($enrollments) > $limit;
        if ($truncated) {
            $enrollments = array_slice($enrollments, 0, $limit);
        }

        $prevSession = $this->resolvePreviousSession($sessionId);

        if (!$prevSession) {
            return response()->json([
                'status' => false,
                'message' => 'There is no session before the one you are viewing.',
            ], 422);
        }

        $prevSessionId = (int) ($prevSession->sess_id ?? $prevSession->SESS_ID);
        $prevSessionCode = (string) ($prevSession->sess_code ?? $prevSession->SESS_CODE ?? '');
        $prevSessionName = (string) ($prevSession->sess_name ?? $prevSession->SESS_NAME ?? '');

        try {
            $students = $this->buildPreviousImportPayload($sessionId, $prevSessionId, $enrollments);
        } catch (\Throwable $e) {
            \Log::error('Previous scholarship load failed: ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Unable to load previous-session scholarships.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }

        return response()->json([
            'status' => true,
            'prev_session_id' => $prevSessionId,
            'prev_session_code' => $prevSessionCode,
            'prev_session_name' => $prevSessionName,
            'students' => $students,
            'truncated' => $truncated,
            'message' => $truncated
                ? 'Only the first ' . $limit . ' students were loaded — narrow the filter and import the rest.'
                : null,
        ]);
    }

    /**
     * The session whose scholarships carry forward into the one being worked on.
     *
     * SUMMER IS SKIPPED. Sessions run Spring → Summer → Fall, so the row before Fall 2026
     * is Summer 2026 — a short optional term that most students never enrol in. Carrying
     * from it meant "Import From Last Session" on F2026 returned the handful of summer
     * holders instead of the Spring 2026 cohort, which is the set that actually continues.
     * F2026 now resolves to S2026 (166), not SUM26 (167).
     *
     * Both markers are tested because either could be the one that is maintained: the
     * code is SUM<yy> and the name is "Summer <year>" on every summer row in the table.
     * The filter is NOT LIKE rather than a list, so additional summers need no change.
     *
     * Skipping cannot strand a caller in practice — a summer session is always preceded
     * by a Spring — and if nothing non-summer exists earlier the callers already handle a
     * null with "There is no session before the one you are viewing."
     *
     * Otherwise unchanged: strictly SESS_ID ordering, because there is no date column
     * anywhere in this module, and still no SESS_STATUS filter, so the resolved session
     * can be one the picker does not list.
     */
    protected function resolvePreviousSession(int $sessionId)
    {
        return $this->carryForwardSessionsQuery()
            ->where('sess_id', '<', $sessionId)
            ->orderByDesc('sess_id')
            ->first();
    }

    /**
     * Sessions a carry-forward may read from — everything except summer.
     *
     * The ONE place the summer rule is expressed. resolvePreviousSession() and
     * previousSessionMap() both build on it so the button's label can never advertise a
     * different session from the one the import actually reads.
     */
    protected function carryForwardSessionsQuery()
    {
        return DB::table('academic_sessions')
            ->select('sess_id', 'sess_code', 'sess_name')
            ->whereRaw("UPPER(TRIM(NVL(sess_code, ''))) NOT LIKE 'SUM%'")
            ->whereRaw("UPPER(TRIM(NVL(sess_name, ''))) NOT LIKE 'SUMMER%'");
    }

    /**
     * For each session in the picker, the session its scholarships carry forward from.
     *
     * Feeds the Import button's label, which names the source session so the grader is
     * told where the students are coming from BEFORE clicking — F2026 importing from
     * S2026 rather than SUM26 is exactly the kind of thing that needs saying out loud.
     *
     * One query, then matched in PHP: resolving per session would be a round trip each.
     * The candidate list is not status-filtered, matching resolvePreviousSession() — the
     * source session can legitimately be one the picker does not offer.
     *
     * @param  array<int, int> $sessionIds
     * @return array<int, array{id:int, code:string, name:string}>
     */
    protected function previousSessionMap(array $sessionIds): array
    {
        if (empty($sessionIds)) {
            return [];
        }

        $candidates = $this->carryForwardSessionsQuery()->orderByDesc('sess_id')->get();
        $map = [];

        foreach ($sessionIds as $sessionId) {
            $sessionId = (int) $sessionId;
            foreach ($candidates as $candidate) {
                $candidateId = (int) ($candidate->sess_id ?? $candidate->SESS_ID);
                if ($candidateId < $sessionId) {
                    $map[$sessionId] = [
                        'id' => $candidateId,
                        'code' => (string) ($candidate->sess_code ?? $candidate->SESS_CODE ?? ''),
                        'name' => (string) ($candidate->sess_name ?? $candidate->SESS_NAME ?? ''),
                    ];
                    break;
                }
            }
        }

        return $map;
    }

    /**
     * The whole scholarship picture for a batch of students across two sessions.
     *
     * Returns, per enrollment: their COMPLETE previous-session set, their COMPLETE
     * current-session set, whether they hold a hostel this term, and the fee each row
     * will be priced against in the TARGET session.
     *
     * "Complete" matters: the caller may have found these students through a filtered
     * query (one scholarship type, one school), but the client has to merge against the
     * student's whole set or it would wipe the rows it cannot see.
     *
     * @param array<int, string> $enrollments already upper-cased, trimmed and unique
     * @param bool $repriceHostel re-derive the hostel base from the TARGET session
     *                            instead of carrying the previous session's fee across
     */
    protected function buildPreviousImportPayload(
        int $sessionId,
        int $prevSessionId,
        array $enrollments,
        bool $repriceHostel = false
    ): array {
        $students = [];
        foreach ($enrollments as $enrollment) {
            $students[$enrollment] = [
                'enrollment_no' => $enrollment,
                'aai_id' => $this->resolveStudentAaiId($enrollment),
                'current' => [],
                'previous' => [],
                'hostel_assigned' => false,
            ];
        }

        // Bind in chunks — Oracle caps an IN list at 1000 expressions.
        foreach (array_chunk($enrollments, 500) as $chunk) {
            foreach ([$sessionId => 'current', $prevSessionId => 'previous'] as $sess => $bucket) {
                // Per-component arrears (fin_other_scholarships), keyed the same way the
                // scholarship rows below are matched: "<type>|<challan_category>". Batched
                // once per chunk/bucket rather than per student — see
                // fetchArrearsRowsForEnrollments().
                $arrearsMap = [];
                foreach ($this->fetchArrearsRowsForEnrollments((int) $sess, $chunk) as $ar) {
                    $enKey = strtoupper(trim((string) ($ar->enrollment_no ?? $ar->ENROLLMENT_NO ?? '')));
                    $arKey = ((int) ($ar->scholarship_type ?? $ar->SCHOLARSHIP_TYPE ?? 0)) . '|'
                        . (string) ($ar->challan_category ?? $ar->CHALLAN_CATEGORY ?? 'Full_semester');
                    $arrearsMap[$enKey][$arKey] = [
                        'admission' => (float) ($ar->admission_fee ?? $ar->ADMISSION_FEE ?? 0),
                        'security' => (float) ($ar->security_fee ?? $ar->SECURITY_FEE ?? 0),
                        'activity' => (float) ($ar->activity_charges ?? $ar->ACTIVITY_CHARGES ?? 0),
                        'medical' => (float) ($ar->medical_insurance ?? $ar->MEDICAL_INSURANCE ?? 0),
                    ];
                }
                $zeroArrears = ['admission' => 0.0, 'security' => 0.0, 'activity' => 0.0, 'medical' => 0.0];

                foreach ($this->fetchScholarshipRowsForEnrollments((int) $sess, $chunk) as $row) {
                    $key = strtoupper(trim((string) ($row->enrollment_no ?? $row->ENROLLMENT_NO ?? '')));
                    if (!isset($students[$key])) {
                        continue;
                    }
                    $category = strtolower((string) ($row->category ?? $row->CATEGORY ?? 'academic'));
                    $scholarshipId = (int) ($row->scholarship_id ?? $row->SCHOLARSHIP_ID ?? 0);
                    $challanCategory = (string) ($row->challan_category ?? $row->CHALLAN_CATEGORY ?? 'Full_semester');
                    $arKey = $scholarshipId . '|' . $challanCategory;

                    $students[$key][$bucket][] = [
                        'record_id' => $row->record_id ?? $row->RECORD_ID ?? null,
                        'category' => $category,
                        'scholarship_id' => $scholarshipId,
                        'scholarship_name' => (string) ($row->scholarship_name ?? $row->SCHOLARSHIP_NAME ?? ''),
                        'percentage' => (float) ($row->percentage ?? $row->PERCENTAGE ?? 0),
                        'challan_category' => $challanCategory,
                        // The previous session's fee and amount ride along for reference
                        // only — what is imported is the PERCENTAGE, re-priced against
                        // this session's hostel fee below. (The single page imports the
                        // same way; it used to copy the amount, which quietly turned a
                        // 100% waiver into 98.11% when the fee moved.)
                        'hostel_fee' => (float) ($row->hostel_fee ?? $row->HOSTEL_FEE ?? 0),
                        'amount' => (float) ($row->amount ?? $row->AMOUNT ?? 0),
                        // Persisted arrears total (ssd_arrear) plus the four one-time-fee
                        // components (fin_other_scholarships) it is the sum of. Hostel rows
                        // never carry arrears — see arrearsChallanCategories().
                        'arrear' => $category === 'hostel' ? 0.0 : (float) ($row->arrear ?? $row->ARREAR ?? 0),
                        'arrears' => $category === 'hostel'
                            ? $zeroArrears
                            : ($arrearsMap[$key][$arKey] ?? $zeroArrears),
                        // Carried so import mode can badge an already-queued award the way
                        // the normal list does. A removal saved earlier survives as a 0%
                        // PENDING row, and the two modes must not describe it differently.
                        'bulk_post_status' => (string) ($row->bulk_post_status ?? $row->BULK_POST_STATUS ?? ''),
                    ];
                }
            }
        }

        // Batched for the whole list rather than one lookup per student.
        $targetHostelFees = $repriceHostel
            ? $this->resolveHostelFeeBases($sessionId, $enrollments)
            : [];

        // One student figure, not one per row — unlike arrears, CGPA and student status
        // do not vary by scholarship type or challan category.
        $studentFactsByEnrollment = $this->fetchStudentFactsForEnrollments($enrollments);

        // Hostel assignment is checked against the TARGET session — a hostel
        // scholarship cannot be imported onto a student with no hostel this term.
        // The fee each imported row will be priced against, in the TARGET session.
        //
        // Without this the client stages a row it cannot price, so the list shows an
        // empty Fee Base and "on save" until the row is saved and reloaded. The
        // percentage carries across; the money is always re-derived here, never
        // copied from the previous session.
        $feeBudget = 400;
        foreach ($students as $key => $student) {
            $students[$key]['hostel_assigned'] = $this->studentHasHostelAssignment(
                $sessionId,
                $student['aai_id'] ? (int) $student['aai_id'] : null,
                $key
            );
            $students[$key]['cgpa'] = $studentFactsByEnrollment[$key]['cgpa'] ?? null;
            $students[$key]['student_status'] = $studentFactsByEnrollment[$key]['student_status'] ?? '';

            $bases = [];
            $categories = [];
            $hasHostelRow = false;
            foreach (array_merge($student['current'], $student['previous']) as $row) {
                if ($row['category'] === 'hostel') {
                    // Hostel keeps its own fee; it rides on the row already.
                    $bases['Hostel_Fee'] = max(
                        $bases['Hostel_Fee'] ?? 0,
                        (float) ($row['hostel_fee'] ?? 0)
                    );
                    $hasHostelRow = true;
                    continue;
                }
                $categories[$row['challan_category'] ?: 'Full_semester'] = true;
            }

            // Importing prices hostel against THIS session's fee structure, resolved from
            // the student's assigned hostel — never last term's figure. When it cannot be
            // resolved the base is dropped and the REASON travels with the student, so the
            // row can say what is wrong rather than showing a number nothing supports.
            if ($repriceHostel && $hasHostelRow) {
                $resolved = $targetHostelFees[$key] ?? null;
                if ($resolved && $resolved['fee'] > 0) {
                    $bases['Hostel_Fee'] = $resolved['fee'];
                } else {
                    unset($bases['Hostel_Fee']);
                }
                $students[$key]['hostel_fee_source'] = $resolved['source'] ?? 'none';
                $students[$key]['hostel_fee_type'] = $resolved['fee_type'] ?? null;
                $students[$key]['hostel_fee_reason'] = $resolved['reason'] ?? null;
            }

            foreach (array_keys($categories) as $cat) {
                if ($feeBudget <= 0) {
                    break;
                }
                $feeBudget--;
                $base = $this->academicFeeBaseFor($sessionId, $key, $cat);
                if ($base > 0) {
                    $bases[$cat] = $base;
                }
            }

            // hostel_fee_source / _type / _reason are set above, and only for a student
            // who actually has a hostel row — there is nothing to report otherwise.
            $students[$key]['fee_bases'] = $bases;
        }

        return $students;
    }

    /**
     * This session's hostel fee per student, resolved the way the single page does.
     *
     * Batched mirror of StudentRecordController::getHostelFeeStructure(): the assigned
     * hostel decides the fee type, and the fee comes from FIN_HOSTEL_FEE_STRUCTURE —
     * NOT from a challan. Reading the challan was wrong: a scholarship is decided before
     * the term is billed, so "no challan yet" is the normal state, not a missing fee.
     *
     * Non-Regular students are the one exception, and they are the exception in the
     * single page too: their fee only exists on a generated challan.
     *
     * Every failure carries the reason, so the row can say what is actually wrong
     * instead of showing a number nothing supports.
     *
     * @param array<int, string> $enrollments already upper-cased and trimmed
     * @return array<string, array{fee: float, source: string, reason: ?string, fee_type: string}>
     */
    protected function resolveHostelFeeBases(int $sessionId, array $enrollments): array
    {
        if (empty($enrollments)) {
            return [];
        }

        // ── 1. The assigned hostel, which decides the campus half of the fee type.
        // HT_ID 1-2 = on-campus (BNU), 3-4 = Safari (_outside), 5-6 = Bedian
        // (unsupported) — the same mapping getHostelFeeStructure() applies.
        // HA_HOSTEL_SESSION_TYPE and HA_HOSTEL_LOCATION_PREFERENCE are read alongside
        // HT_ID and win over it — the assignment page writes them directly, they answer
        // BOTH the campus and the Regular/Non-Regular question, and they are populated on
        // rows where the bed chain is absent and HT_ID comes back NULL. Batched rather
        // than per-student because this runs over up to 500 enrollments at a time; the
        // single page calls HostelAssignmentResolver for the same facts.
        $assignments = [];
        $existsPredicate = HostelAssignmentResolver::assignmentExistsSql('ha');
        foreach (array_chunk($enrollments, 500) as $chunk) {
            $placeholders = implode(',', array_fill(0, count($chunk), '?'));
            $rows = DB::select("
                SELECT
                    UPPER(TRIM(NVL(ha.ha_enrolment_no, aai.cms_sai_enrollment_no))) AS enrollment_no,
                    MAX(NVL(ht.ht_id, 0)) KEEP (DENSE_RANK LAST ORDER BY ha.ha_id) AS ht_id,
                    MAX(ha.ha_hostel_session_type) KEEP (DENSE_RANK LAST ORDER BY ha.ha_id) AS ha_session_type,
                    MAX(ha.ha_hostel_location_preference) KEEP (DENSE_RANK LAST ORDER BY ha.ha_id) AS ha_location
                FROM hostel_assign ha
                LEFT JOIN hostel_beds hb ON hb.hb_id = ha.ha_hb_id
                LEFT JOIN hostel_rooms hr ON hr.hr_id = hb.hb_room_id
                LEFT JOIN hostel_type ht ON ht.ht_id = hr.hr_ht_id
                LEFT JOIN admission_apply_info aai ON aai.aai_id = ha.ha_aai_id
                WHERE ha.ha_session = ?
                  AND {$existsPredicate}
                  AND UPPER(TRIM(NVL(ha.ha_enrolment_no, aai.cms_sai_enrollment_no))) IN ({$placeholders})
                GROUP BY UPPER(TRIM(NVL(ha.ha_enrolment_no, aai.cms_sai_enrollment_no)))
            ", array_merge([$sessionId], $chunk));

            foreach ($rows ?: [] as $row) {
                $key = (string) ($row->enrollment_no ?? $row->ENROLLMENT_NO ?? '');
                if ($key !== '') {
                    $assignments[$key] = [
                        'ht_id'        => (int) ($row->ht_id ?? $row->HT_ID ?? 0),
                        'session_type' => (string) ($row->ha_session_type ?? $row->HA_SESSION_TYPE ?? ''),
                        'location'     => (string) ($row->ha_location ?? $row->HA_LOCATION ?? ''),
                    ];
                }
            }
        }

        // ── 2. Every hostel fee structure defined for this session, in one read.
        // Continuing students, so 'Existing' — the same value the drawer sends.
        $structures = [];
        foreach (DB::select("
            SELECT
                UPPER(TRIM(fhfs_session_type)) AS session_type,
                MAX(TO_NUMBER(NVL(fhfs_amount_per_semester, 0))) AS fee_amount
            FROM fin_hostel_fee_structure
            WHERE fhfs_session = ?
              AND UPPER(TRIM(fhfs_student_type)) = 'EXISTING'
              AND fhfs_status = 'E'
            GROUP BY UPPER(TRIM(fhfs_session_type))
        ", [$sessionId]) ?: [] as $row) {
            $structures[(string) ($row->session_type ?? $row->SESSION_TYPE ?? '')] =
                (float) ($row->fee_amount ?? $row->FEE_AMOUNT ?? 0);
        }

        // ── 3. Non-Regular students only: their fee lives on the hostel challan.
        //
        // Who counts as Non-Regular is settled HERE, before the challan lookup, because
        // that lookup has to cover exactly this set. It used to be the hardcoded list
        // alone; now the assignment's own HA_HOSTEL_SESSION_TYPE decides and the list is
        // only a fallback for a row that does not carry one. Resolving it after the
        // lookup would leave the students the list omits reporting "the hostel challan
        // has not been generated" when it has.
        $resolver = new HostelAssignmentResolver();
        $nonRegular = array_map('strtoupper', StudentRecordController::NON_REGULAR_HOSTEL_ENROLLMENTS);

        $nonRegularHere = [];
        foreach ($enrollments as $enrollment) {
            $assignedType = isset($assignments[$enrollment])
                ? $resolver->normalizeSessionType($assignments[$enrollment]['session_type'])
                : null;

            $isNonRegular = $assignedType !== null
                ? (stripos($assignedType, 'Non-Regular') !== false)
                : in_array($enrollment, $nonRegular, true);

            if ($isNonRegular) {
                $nonRegularHere[] = $enrollment;
            }
        }
        $nonRegularHere = array_values(array_unique($nonRegularHere));

        $challanFees = [];
        if (!empty($nonRegularHere)) {
            $placeholders = implode(',', array_fill(0, count($nonRegularHere), '?'));
            foreach (DB::select("
                SELECT
                    UPPER(TRIM(sc.sc_enrollment_no)) AS enrollment_no,
                    MAX(TO_NUMBER(NVL(sc.ffs_hostel_amt, 0))) AS fee_amount
                FROM student_challan sc
                WHERE sc.sc_session_id = ?
                  AND NVL(sc.sc_hostel_fee_type, 'X') != 'Hostel_other_utilities'
                  AND UPPER(TRIM(sc.sc_enrollment_no)) IN ({$placeholders})
                GROUP BY UPPER(TRIM(sc.sc_enrollment_no))
            ", array_merge([$sessionId], $nonRegularHere)) ?: [] as $row) {
                $key = (string) ($row->enrollment_no ?? $row->ENROLLMENT_NO ?? '');
                if ($key !== '') {
                    $challanFees[$key] = (float) ($row->fee_amount ?? $row->FEE_AMOUNT ?? 0);
                }
            }
        }

        $out = [];
        foreach ($enrollments as $enrollment) {
            if (!array_key_exists($enrollment, $assignments)) {
                $out[$enrollment] = [
                    'fee' => 0.0,
                    'source' => 'none',
                    'fee_type' => in_array($enrollment, $nonRegular, true) ? 'Non-Regular' : 'Regular',
                    'reason' => 'No hostel is assigned to this student in this session.',
                ];
                continue;
            }

            $assigned      = $assignments[$enrollment];
            $htId          = $assigned['ht_id'];
            $assignedType  = $resolver->normalizeSessionType($assigned['session_type']);
            $assignedLoc   = strtolower(trim($assigned['location']));

            // Residency, in order of authority: the assignment's own session type, then
            // the hardcoded list as a safety net for a row whose column is not set.
            // The column is why this now matches the single page — the list names 8
            // Non-Regular students, the column names every one of them.
            $isNonRegular = $assignedType !== null
                ? (stripos($assignedType, 'Non-Regular') !== false)
                : in_array($enrollment, $nonRegular, true);
            $residency = $isNonRegular ? 'Non-Regular' : 'Regular';

            $isBedian = ($assignedType !== null && stripos($assignedType, '_bedian') !== false)
                || in_array($htId, [5, 6], true);

            if ($isBedian) {
                $out[$enrollment] = [
                    'fee' => 0.0,
                    'source' => 'none',
                    'fee_type' => $residency,
                    'reason' => 'Assigned a Bedian hostel — Bedian hostel scholarships are not supported yet.',
                ];
                continue;
            }

            // Campus, in the same order: the session type carries it, else the location
            // column, else the old HT_ID bucketing (3-4 = Safari). Only when all three are
            // silent does it fall back to on-campus.
            if ($assignedType !== null) {
                $offCampus = stripos($assignedType, '_outside') !== false;
            } elseif ($assignedLoc !== '') {
                $offCampus = in_array($assignedLoc, ['off_campus', 'out_of_campus'], true);
            } else {
                $offCampus = in_array($htId, [3, 4], true);
            }

            $feeType = $offCampus ? $residency . '_outside' : $residency;

            if ($isNonRegular) {
                $fee = (float) ($challanFees[$enrollment] ?? 0);
                $out[$enrollment] = $fee > 0
                    ? ['fee' => $fee, 'source' => 'challan', 'fee_type' => $feeType, 'reason' => null]
                    : [
                        'fee' => 0.0,
                        'source' => 'none',
                        'fee_type' => $feeType,
                        'reason' => 'Non-Regular hostel student — their fee comes from the hostel challan, '
                            . 'which has not been generated for this session yet.',
                    ];
                continue;
            }

            $fee = (float) ($structures[strtoupper($feeType)] ?? 0);
            $out[$enrollment] = $fee > 0
                ? ['fee' => $fee, 'source' => 'structure', 'fee_type' => $feeType, 'reason' => null]
                : [
                    'fee' => 0.0,
                    'source' => 'none',
                    'fee_type' => $feeType,
                    'reason' => 'No hostel fee structure defined for "' . $feeType . '" in this session.',
                ];
        }

        return $out;
    }

    /**
     * Academic + hostel scholarship rows for a set of enrollments in one session,
     * in a single shape. Fully parameterized.
     *
     * @param array<int, string> $enrollments already upper-cased and trimmed
     */
    protected function fetchScholarshipRowsForEnrollments(int $sessionId, array $enrollments): array
    {
        if (empty($enrollments)) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($enrollments), '?'));
        $amountExpr = $this->hasSsdAmountColumn()
            ? 'TO_NUMBER(NVL(ssd.ssd_amount, 0))'
            : 'CAST(0 AS NUMBER)';

        $academic = DB::select("
            SELECT
                TO_CHAR(ssd.ssd_id) AS record_id,
                CAST('academic' AS VARCHAR2(20)) AS category,
                UPPER(TRIM(NVL(aai.cms_sai_enrollment_no, ssd.ssd_sai_enrollment_no))) AS enrollment_no,
                TO_NUMBER(st.scholarship_id) AS scholarship_id,
                TO_CHAR(st.scholarship_name) AS scholarship_name,
                TO_NUMBER(NVL(ssd.ssd_percentage, 0)) AS percentage,
                TO_CHAR(NVL(ssd.ssd_challan_category, 'Full_semester')) AS challan_category,
                CAST(0 AS NUMBER) AS hostel_fee,
                {$amountExpr} AS amount,
                TO_NUMBER(NVL(ssd.ssd_arrear, 0)) AS arrear,
                {$this->sqlAcademicBulkPostStatusExpr('ssd')}
            FROM students_scholarships_details ssd
            JOIN scholarship_type st ON st.scholarship_id = ssd.ssd_type
            LEFT JOIN admission_apply_info aai ON aai.aai_id = ssd.ssd_app_id
            WHERE NVL(ssd.status, 'E') = 'E'
              AND ssd.ssd_sess_id = ?
              AND UPPER(TRIM(NVL(aai.cms_sai_enrollment_no, ssd.ssd_sai_enrollment_no))) IN ({$placeholders})
        ", array_merge([$sessionId], $enrollments));

        $hostel = DB::select("
            SELECT
                TO_CHAR(sshd.sshd_id) AS record_id,
                CAST('hostel' AS VARCHAR2(20)) AS category,
                UPPER(TRIM(NVL(sshd.sshd_sai_enrollment_no, aai.cms_sai_enrollment_no))) AS enrollment_no,
                TO_NUMBER(NVL(sshd.sshd_type, 0)) AS scholarship_id,
                TO_CHAR(NVL(st.scholarship_name, 'Hostel Scholarship')) AS scholarship_name,
                TO_NUMBER(NVL(sshd.sshd_percentage, 0)) AS percentage,
                CAST('Hostel_Fee' AS VARCHAR2(50)) AS challan_category,
                TO_NUMBER(NVL(sshd.sshd_hostel_fee, 0)) AS hostel_fee,
                TO_NUMBER(NVL(sshd.sshd_amount, 0)) AS amount,
                CAST(0 AS NUMBER) AS arrear,
                {$this->sqlHostelBulkPostStatusExpr('sshd')}
            FROM fin_students_scholarships_hostel_details sshd
            LEFT JOIN scholarship_type st ON st.scholarship_id = sshd.sshd_type
            LEFT JOIN admission_apply_info aai ON aai.aai_id = sshd.sshd_app_id
            WHERE NVL(TRIM(sshd.sshd_status), 'E') = 'E'
              AND sshd.sshd_sess_id = ?
              AND UPPER(TRIM(NVL(sshd.sshd_sai_enrollment_no, aai.cms_sai_enrollment_no))) IN ({$placeholders})
        ", array_merge([$sessionId], $enrollments));

        return array_merge($academic ?: [], $hostel ?: []);
    }

    /**
     * Batched fin_other_scholarships rows (the per-component arrears grid) for many
     * students in one session — the Import batch mirror of getArrearsRowsForStudent(),
     * which is per-student and would mean one query per row for a bulk import.
     *
     * Keyed by enrollment the same way fetchScholarshipRowsForEnrollments() is, via
     * NVL(aai.cms_sai_enrollment_no, fos.fos_enrollment_no), so a row written with an
     * app id but no enrolment is still found here.
     */
    protected function fetchArrearsRowsForEnrollments(int $sessionId, array $enrollments): array
    {
        if (empty($enrollments)) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($enrollments), '?'));

        return DB::select("
            SELECT
                UPPER(TRIM(NVL(aai.cms_sai_enrollment_no, fos.fos_enrollment_no))) AS enrollment_no,
                TO_NUMBER(fos.fos_scholarship_type) AS scholarship_type,
                TO_CHAR(NVL(fos.fos_challan_category, 'Full_semester')) AS challan_category,
                TO_NUMBER(NVL(fos.fos_admission_fee, 0)) AS admission_fee,
                TO_NUMBER(NVL(fos.fos_security_fee, 0)) AS security_fee,
                TO_NUMBER(NVL(fos.fos_activity_charges, 0)) AS activity_charges,
                TO_NUMBER(NVL(fos.fos_medical_insurance, 0)) AS medical_insurance
            FROM fin_other_scholarships fos
            LEFT JOIN admission_apply_info aai ON aai.aai_id = fos.fos_app_id
            WHERE fos.fos_sess_id = ?
              AND UPPER(TRIM(NVL(aai.cms_sai_enrollment_no, fos.fos_enrollment_no))) IN ({$placeholders})
        ", array_merge([$sessionId], $enrollments)) ?: [];
    }

    /**
     * CGPA for many students in one query, via the same CGPA() Oracle function the
     * single Student Record page uses (StudentRecordController::getSchlrshipDetailRecord).
     * Not session-scoped — CGPA is a standing academic figure, not one that varies by the
     * session being awarded.
     *
     * @return array<string, float|null> enrollment (upper-cased) => cgpa
     */
    /**
     * CGPA plus student status for many students in one query — one query instead of
     * two, since both come off the same students_apply_info row.
     *
     * @return array<string, array{cgpa: float|null, student_status: string}>
     */
    protected function fetchStudentFactsForEnrollments(array $enrollments): array
    {
        if (empty($enrollments)) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($enrollments), '?'));

        $rows = DB::select("
            SELECT
                UPPER(TRIM(sai.sai_enrollment_no)) AS enrollment_no,
                cgpa(sai.sai_enrollment_no) AS cgpa,
                sai.sai_student_status AS student_status
            FROM students_apply_info sai
            WHERE UPPER(TRIM(sai.sai_enrollment_no)) IN ({$placeholders})
        ", $enrollments) ?: [];

        $map = [];
        foreach ($rows as $row) {
            $key = (string) ($row->enrollment_no ?? $row->ENROLLMENT_NO ?? '');
            if ($key === '') {
                continue;
            }
            $cgpa = $row->cgpa ?? $row->CGPA ?? null;
            $map[$key] = [
                'cgpa' => $cgpa !== null ? (float) $cgpa : null,
                'student_status' => (string) ($row->student_status ?? $row->STUDENT_STATUS ?? ''),
            ];
        }

        return $map;
    }

    /**
     * Which enrollments hold ANY of the given scholarship types in one session — checked
     * across both academic and hostel awards, since a "type" can be either. Backs the
     * Exclude Scholarship Type(s) filter: a student in this set is dropped from the list
     * regardless of which of their OTHER rows matched the main filters.
     *
     * Only rows with a live (> 0%) award count — a 0% row is a removed scholarship, and
     * excluding a student because of a discount they no longer hold would be wrong.
     *
     * @return array<string, true> upper-cased enrollment => true, for O(1) lookup
     */
    protected function fetchEnrollmentsHoldingTypes(int $sessionId, array $typeIds): array
    {
        $typeIds = array_values(array_unique(array_filter(array_map('intval', $typeIds))));
        if (empty($typeIds) || !$sessionId) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($typeIds), '?'));
        $set = [];

        $academic = DB::select("
            SELECT DISTINCT UPPER(TRIM(NVL(aai.cms_sai_enrollment_no, ssd.ssd_sai_enrollment_no))) AS enrollment_no
            FROM students_scholarships_details ssd
            LEFT JOIN admission_apply_info aai ON aai.aai_id = ssd.ssd_app_id
            WHERE NVL(ssd.status, 'E') = 'E'
              AND ssd.ssd_sess_id = ?
              AND NVL(ssd.ssd_percentage, 0) > 0
              AND ssd.ssd_type IN ({$placeholders})
        ", array_merge([$sessionId], $typeIds)) ?: [];

        foreach ($academic as $row) {
            $key = strtoupper(trim((string) ($row->enrollment_no ?? $row->ENROLLMENT_NO ?? '')));
            if ($key !== '') {
                $set[$key] = true;
            }
        }

        $hostel = DB::select("
            SELECT DISTINCT UPPER(TRIM(NVL(sshd.sshd_sai_enrollment_no, aai.cms_sai_enrollment_no))) AS enrollment_no
            FROM fin_students_scholarships_hostel_details sshd
            LEFT JOIN admission_apply_info aai ON aai.aai_id = sshd.sshd_app_id
            WHERE NVL(TRIM(sshd.sshd_status), 'E') = 'E'
              AND sshd.sshd_sess_id = ?
              AND NVL(sshd.sshd_percentage, 0) > 0
              AND sshd.sshd_type IN ({$placeholders})
        ", array_merge([$sessionId], $typeIds)) ?: [];

        foreach ($hostel as $row) {
            $key = strtoupper(trim((string) ($row->enrollment_no ?? $row->ENROLLMENT_NO ?? '')));
            if ($key !== '') {
                $set[$key] = true;
            }
        }

        return $set;
    }

    /**
     * Import From Last Session — the student list itself comes from the previous session.
     *
     * The old Import (getPreviousScholarships above) asked "who is on screen?" and then
     * fetched their history. That only helps for students already listed for the current
     * session, which is exactly the case that does not need importing. This one works the
     * other way round: it FINDS last session's scholarship holders through the School /
     * Department / Program / Scholarship Type filters, and offers them for this session.
     *
     * The Scholarship Type filter chooses WHO appears, not what comes across. Pick Merit
     * and you get everyone who held Merit last session, including students who also held
     * other awards — those ride along in the payload so the drawer can edit them and so
     * the client can merge against the student's whole set instead of a filtered slice.
     *
     * Read-only, like its predecessor. Nothing is written until Save Checked pushes the
     * staged drafts through saveStudentScholarships(), which validates, prices and queues.
     */
    public function importPreviousSessionStudents(Request $request)
    {
        $sessionId = (int) $request->input('session_id');
        if (!$sessionId) {
            return response()->json([
                'status' => false,
                'message' => 'Please select the session you are importing into.',
            ], 422);
        }

        $prevSession = $this->resolvePreviousSession($sessionId);
        if (!$prevSession) {
            return response()->json([
                'status' => false,
                'message' => 'There is no session before the one you are viewing.',
            ], 422);
        }

        $prevSessionId = (int) ($prevSession->sess_id ?? $prevSession->SESS_ID);
        $prevSessionCode = (string) ($prevSession->sess_code ?? $prevSession->SESS_CODE ?? '');
        $prevSessionName = (string) ($prevSession->sess_name ?? $prevSession->SESS_NAME ?? '');

        $schoolId = $this->normalizeId($request->input('school_id'));
        $deptId = $this->normalizeId($request->input('dept_id'));
        $progId = $this->extractProgramId($request->input('prog_id'));
        $scholarshipTypeId = $this->normalizeId($request->input('scholarship_type'));
        $studentId = strtoupper(trim((string) $request->input('student_id', '')));

        // Filters on the student's CURRENT status (sai_student_status is not
        // session-scoped) — narrows to students worth importing history for, same
        // reasoning as the always-on activeOnly=true eligibility check below.
        $studentStatusFilter = strtoupper(trim((string) $request->input('student_status', '')));
        if (!in_array($studentStatusFilter, $this->eligibleStudentStatuses(), true)) {
            $studentStatusFilter = '';
        }

        $categoryFilter = strtolower(trim((string) $request->input('category', 'all')));
        if (!in_array($categoryFilter, ['all', 'academic', 'hostel'], true)) {
            $categoryFilter = 'all';
        }

        // Fee Type narrows WHICH STUDENTS are found here, the same way it narrows the
        // normal list — a student is only pulled in if they held THIS fee type last
        // session. It does NOT trim what rides along once a student is matched:
        // buildPreviousImportPayload() below still fetches their COMPLETE current +
        // previous set unfiltered, because the client needs the whole picture to merge
        // against (see that method's own docblock) — dropping their other rows here
        // would silently corrupt the "+N others" / headroom picture for everything else
        // they hold.
        $challanCategoryFilter = trim((string) $request->input('challan_category', ''));
        if (!in_array($challanCategoryFilter, $this->allChallanCategories(), true)) {
            $challanCategoryFilter = '';
        }
        // The academic fetch takes '' to mean "no fee-type filter" — Hostel_Fee is not
        // one of ITS categories, it is the separate hostel fetch below.
        $academicChallanFilter = ($challanCategoryFilter !== '' && $challanCategoryFilter !== 'Hostel_Fee')
            ? $challanCategoryFilter
            : '';

        $typeFilters = $this->resolveTypeFilters($scholarshipTypeId);

        $wantAcademic = ($categoryFilter !== 'hostel') && ($challanCategoryFilter !== 'Hostel_Fee');
        $wantHostel = ($categoryFilter !== 'academic')
            && ($challanCategoryFilter === '' || $challanCategoryFilter === 'Hostel_Fee');

        $population = [];

        try {
            if ($wantAcademic) {
                // hideZero: a 0% row is a scholarship that was REMOVED last session.
                // Importing it back would re-award something deliberately taken away.
                $population = array_merge($population, $this->fetchAcademicScholarshipRows(
                    $prevSessionId,
                    $schoolId,
                    $deptId,
                    $progId,
                    $typeFilters['academic_type_id'],
                    $studentId,
                    $typeFilters['academic_name_like'],
                    $academicChallanFilter,
                    true,
                    true,
                    $studentStatusFilter
                ));
            }

            if ($wantHostel) {
                $population = array_merge($population, $this->fetchHostelScholarshipRows(
                    $prevSessionId,
                    $schoolId,
                    $deptId,
                    $progId,
                    $typeFilters['hostel_type_id'],
                    $studentId,
                    $typeFilters['hostel_name_like'],
                    true,
                    true,
                    $studentStatusFilter
                ));
            }
        } catch (\Throwable $e) {
            \Log::error('Previous-session import population failed: ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Unable to read last session\'s scholarships.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }

        // Exclude Scholarship Type(s) — same rule as the normal list, checked against the
        // PREVIOUS session (the one this population is read from): a student holding any
        // excluded type there is dropped from the results entirely.
        $excludeTypeIds = array_values(array_unique(array_filter(
            array_map('intval', (array) $request->input('exclude_scholarship_types', []))
        )));
        if (!empty($excludeTypeIds)) {
            $excludedEnrollments = $this->fetchEnrollmentsHoldingTypes($prevSessionId, $excludeTypeIds);
            if (!empty($excludedEnrollments)) {
                $population = array_values(array_filter($population, function ($row) use ($excludedEnrollments) {
                    $key = strtoupper(trim((string) ($row->enrollment_no ?? $row->ENROLLMENT_NO ?? '')));

                    return $key === '' || !isset($excludedEnrollments[$key]);
                }));
            }
        }

        // One entry per student, holding the display fields the grouped row needs. The
        // list is one row per STUDENT here, not one per scholarship.
        $meta = [];
        $matchedTypeIds = [];
        foreach ($population as $row) {
            $key = strtoupper(trim((string) ($row->enrollment_no ?? $row->ENROLLMENT_NO ?? '')));
            if ($key === '') {
                continue;
            }

            $typeId = (int) ($row->scholarship_id ?? $row->SCHOLARSHIP_ID ?? 0);
            if ($typeId) {
                $matchedTypeIds[$typeId] = true;
            }

            if (isset($meta[$key])) {
                continue;
            }
            $meta[$key] = [
                'student_name' => (string) ($row->student_name ?? $row->STUDENT_NAME ?? ''),
                'school_name' => (string) ($row->school_name ?? $row->SCHOOL_NAME ?? ''),
                'school_full_name' => (string) ($row->school_full_name ?? $row->SCHOOL_FULL_NAME ?? ''),
                'dept_name' => (string) ($row->dept_name ?? $row->DEPT_NAME ?? ''),
                'prog_name' => (string) ($row->prog_name ?? $row->PROG_NAME ?? ''),
            ];
        }

        $enrollments = array_keys($meta);
        if (empty($enrollments)) {
            return response()->json([
                'status' => true,
                'prev_session_id' => $prevSessionId,
                'prev_session_code' => $prevSessionCode,
                'prev_session_name' => $prevSessionName,
                'students' => (object) [],
                'matched_scholarship_ids' => [],
                'count' => 0,
                'truncated' => false,
                'message' => 'No student held a scholarship in ' . ($prevSessionCode ?: 'the previous session')
                    . ' under these filters.',
            ]);
        }

        // Same ceiling the legacy import uses — one click must not ask for the whole
        // university, and every student costs a fee-structure resolution below.
        $limit = 500;
        $truncated = count($enrollments) > $limit;
        if ($truncated) {
            $enrollments = array_slice($enrollments, 0, $limit);
        }

        try {
            $students = $this->buildPreviousImportPayload($sessionId, $prevSessionId, $enrollments, true);
        } catch (\Throwable $e) {
            \Log::error('Previous-session import build failed: ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Unable to load last session\'s scholarships.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }

        // Students managed on the single page are listed so the user can see they were
        // considered, but carry a flag that stops the client staging anything for them.
        $lockedStudents = $this->singlePageLockedStudents($sessionId, array_keys($students));

        foreach ($students as $key => $student) {
            $students[$key] = array_merge($student, $meta[$key] ?? []);
            $students[$key]['single_page_locked'] = !empty($lockedStudents[$key]);
        }

        return response()->json([
            'status' => true,
            'prev_session_id' => $prevSessionId,
            'prev_session_code' => $prevSessionCode,
            'prev_session_name' => $prevSessionName,
            'students' => $students,
            // Which scholarships the filter actually selected. With no type filter this is
            // every type present, and the client falls back to "the student's only one".
            'matched_scholarship_ids' => $scholarshipTypeId ? array_keys($matchedTypeIds) : [],
            'count' => count($students),
            'truncated' => $truncated,
            'message' => $truncated
                ? 'Only the first ' . $limit . ' students were loaded — narrow the filter and import the rest.'
                : null,
        ]);
    }

    /**
     * Typeahead for Add Students — active students, by name or enrolment number.
     *
     * Name and enrolment are matched as one concatenated string so "ahmed 2021" works,
     * the same trick SportsApplicantsController::autoSearch() uses. Eligibility goes
     * through sqlStudentIsActiveExpr() so this page has exactly ONE definition of who
     * may be awarded a scholarship, shared with the previous-session import.
     */
    public function searchActiveStudents(Request $request)
    {
        $term = trim((string) $request->input('term', ''));
        if (mb_strlen($term) < 2) {
            return response()->json(['status' => true, 'students' => []]);
        }

        $sessionId = (int) $request->input('session_id');
        $needle = '%' . strtolower($term) . '%';

        // The typeahead searches STUDENTS_APPLY_INFO directly, so unlike the list it can
        // require a positive status: a student with no row there has no enrolment number
        // to be found by in the first place.
        $allowedStatuses = "'" . implode("', '", $this->eligibleStudentStatuses()) . "'";

        try {
            $rows = DB::select("
                SELECT * FROM (
                    SELECT
                        UPPER(TRIM(sai.sai_enrollment_no)) AS enrollment_no,
                        TO_CHAR(NVL(spi.spi_name, api.api_name)) AS student_name,
                        TO_CHAR(NVL(s.school_code, s.school_name)) AS school_name,
                        TO_CHAR(p.prog_name) AS prog_name,
                        TO_CHAR(sai.sai_student_status) AS student_status
                    FROM students_apply_info sai
                    LEFT JOIN students_personal_info spi ON spi.spi_id = sai.sai_spi_id
                    LEFT JOIN admission_apply_info aai
                        ON UPPER(TRIM(aai.cms_sai_enrollment_no)) = UPPER(TRIM(sai.sai_enrollment_no))
                    LEFT JOIN admission_personal_info api ON api.api_id = aai.aai_api_id
                    LEFT JOIN schools s ON s.school_id = sai.sai_sdpo_school_id
                    LEFT JOIN programs p ON p.prog_id = sai.sai_sdpo_prog_id
                    WHERE UPPER(TRIM(NVL(sai.sai_student_status, ''))) IN ({$allowedStatuses})
                      AND LOWER(NVL(spi.spi_name, ' ') || ' ' || sai.sai_enrollment_no) LIKE :needle
                    ORDER BY sai.sai_enrollment_no
                ) WHERE ROWNUM <= 15
            ", ['needle' => $needle]);
        } catch (\Throwable $e) {
            \Log::error('Scholarship student search failed: ' . $e->getMessage());

            return response()->json(['status' => false, 'message' => 'Student search failed.'], 500);
        }

        // How many scholarships each suggestion already holds this session, so the user
        // can see it before adding rather than after.
        $held = [];
        $enrollments = array_values(array_filter(array_map(function ($r) {
            return (string) ($r->enrollment_no ?? $r->ENROLLMENT_NO ?? '');
        }, $rows ?: [])));

        if ($sessionId && !empty($enrollments)) {
            try {
                foreach ($this->fetchScholarshipRowsForEnrollments($sessionId, $enrollments) as $row) {
                    $key = strtoupper(trim((string) ($row->enrollment_no ?? $row->ENROLLMENT_NO ?? '')));
                    if ($key !== '') {
                        $held[$key] = ($held[$key] ?? 0) + 1;
                    }
                }
            } catch (\Throwable $e) {
                // A failure here costs a hint, not the search.
                \Log::warning('Scholarship search held-count failed: ' . $e->getMessage());
            }
        }

        $students = [];
        foreach ($rows ?: [] as $row) {
            $key = (string) ($row->enrollment_no ?? $row->ENROLLMENT_NO ?? '');
            $students[] = [
                'enrollment_no' => $key,
                'student_name' => (string) ($row->student_name ?? $row->STUDENT_NAME ?? ''),
                'school_name' => (string) ($row->school_name ?? $row->SCHOOL_NAME ?? ''),
                'prog_name' => (string) ($row->prog_name ?? $row->PROG_NAME ?? ''),
                'held_count' => (int) ($held[$key] ?? 0),
            ];
        }

        return response()->json(['status' => true, 'students' => $students]);
    }

    /**
     * Resolve one student into a single award row for the Add Students list.
     *
     * Returns the row in EXACTLY the shape normalizeListRow() already consumes, so the
     * client renders it with renderTable() and edits it with the machinery that was
     * built for the Not Awarded tab — no new row format, no second save path.
     *
     * Nothing is written. The row becomes a draft when a percentage is typed and reaches
     * the database only through Save Checked.
     */
    public function resolveStudentForAward(Request $request)
    {
        $sessionId = (int) $request->input('session_id');
        $enrollment = strtoupper(trim((string) $request->input('enrollment_no', '')));
        $scholarshipTypeId = $this->normalizeId($request->input('scholarship_type'));
        $challanCategory = trim((string) $request->input('challan_category', ''));

        if (!$sessionId) {
            return response()->json(['status' => false, 'message' => 'Please select a session.'], 422);
        }
        if ($enrollment === '') {
            return response()->json(['status' => false, 'message' => 'Please choose a student.'], 422);
        }
        if (!$scholarshipTypeId) {
            return response()->json(['status' => false, 'message' => 'Please choose a Scholarship Type first.'], 422);
        }
        if (!in_array($challanCategory, $this->allChallanCategories(), true)) {
            return response()->json(['status' => false, 'message' => 'Please choose a Fee Type first.'], 422);
        }

        $typeMeta = $this->scholarshipTypeMeta($scholarshipTypeId);
        $isHostelType = ($typeMeta['desc'] === 'hostel');

        // A hostel scholarship only exists against the hostel fee, and an academic one
        // never does. Refuse the mismatch here as well as in the UI.
        if ($isHostelType && $challanCategory !== 'Hostel_Fee') {
            return response()->json([
                'status' => false,
                'message' => '"' . $typeMeta['name'] . '" is a hostel scholarship — choose Fee Type "Hostel".',
            ], 422);
        }
        if (!$isHostelType && $challanCategory === 'Hostel_Fee') {
            return response()->json([
                'status' => false,
                'message' => '"' . $typeMeta['name'] . '" is an academic scholarship — choose an academic Fee Type.',
            ], 422);
        }

        $category = $isHostelType ? 'hostel' : 'academic';

        try {
            $student = DB::selectOne("
                SELECT
                    UPPER(TRIM(sai.sai_enrollment_no)) AS enrollment_no,
                    TO_CHAR(NVL(spi.spi_name, api.api_name)) AS student_name,
                    TO_CHAR(NVL(s.school_code, s.school_name)) AS school_name,
                    TO_CHAR(s.school_name) AS school_full_name,
                    TO_CHAR(d.dept_name) AS dept_name,
                    TO_CHAR(p.prog_name) AS prog_name,
                    TO_CHAR(aai.aai_id) AS app_id,
                    TO_CHAR(sai.sai_student_status) AS student_status,
                    CASE WHEN sai.sai_enrollment_no IS NOT NULL THEN cgpa(sai.sai_enrollment_no) END AS cgpa
                FROM students_apply_info sai
                LEFT JOIN students_personal_info spi ON spi.spi_id = sai.sai_spi_id
                LEFT JOIN admission_apply_info aai
                    ON UPPER(TRIM(aai.cms_sai_enrollment_no)) = UPPER(TRIM(sai.sai_enrollment_no))
                LEFT JOIN admission_personal_info api ON api.api_id = aai.aai_api_id
                LEFT JOIN schools s ON s.school_id = sai.sai_sdpo_school_id
                LEFT JOIN departments d ON d.dept_id = sai.sai_sdpo_dept_id
                LEFT JOIN programs p ON p.prog_id = sai.sai_sdpo_prog_id
                WHERE UPPER(TRIM(sai.sai_enrollment_no)) = :enrollment_no
            ", ['enrollment_no' => $enrollment]);

            if (!$student) {
                return response()->json([
                    'status' => false,
                    'message' => $enrollment . ' is not a student on record.',
                ], 422);
            }

            // The eligibility rule, stated once — a typed-in enrolment must not sidestep
            // the check the typeahead applies. A blank status is NOT a refusal: it means
            // the student is not enrolled yet, which sqlStudentIsActiveExpr() also allows.
            $status = strtoupper(trim((string) ($student->student_status ?? $student->STUDENT_STATUS ?? '')));
            if ($status !== '' && !in_array($status, $this->eligibleStudentStatuses(), true)) {
                return response()->json([
                    'status' => false,
                    'message' => $enrollment . ' is not an active student (status: '
                        . (($student->student_status ?? $student->STUDENT_STATUS) ?: 'unknown') . ').',
                ], 422);
            }

            // Owned by the single page: refuse before the row is ever built, so the
            // entry row shows the reason instead of adding something unsavable.
            if ($this->studentIsSinglePageOwned($sessionId, $enrollment)) {
                return response()->json([
                    'status' => false,
                    'message' => $this->singlePageLockedMessage($enrollment),
                    'locked_by_single_page' => true,
                ], 422);
            }

            // Their COMPLETE current-session set: drives the "already awarded" flag, the
            // exact-clash check, and the headroom left in this bucket.
            $existingRows = $this->fetchScholarshipRowsForEnrollments($sessionId, [$enrollment]);

            $existing = [];
            $clash = null;
            $otherPct = 0.0;
            $existingCategories = [];

            foreach ($existingRows as $row) {
                $rowCategory = strtolower((string) ($row->category ?? $row->CATEGORY ?? 'academic'));
                $rowChallan = (string) ($row->challan_category ?? $row->CHALLAN_CATEGORY ?? 'Full_semester');
                $rowTypeId = (int) ($row->scholarship_id ?? $row->SCHOLARSHIP_ID ?? 0);
                $rowPct = (float) ($row->percentage ?? $row->PERCENTAGE ?? 0);
                $rowId = (string) ($row->record_id ?? $row->RECORD_ID ?? '');

                $existing[] = [
                    'record_id' => $rowId,
                    'category' => $rowCategory,
                    'scholarship_id' => $rowTypeId,
                    'scholarship_name' => (string) ($row->scholarship_name ?? $row->SCHOLARSHIP_NAME ?? ''),
                    'percentage' => $rowPct,
                    'challan_category' => $rowChallan,
                ];

                if ($rowCategory !== 'hostel' && $rowPct > 0) {
                    $existingCategories[$rowChallan] = true;
                }

                // Same bucket? Then it counts against the cap — unless it IS the row we
                // are about to edit, whose own percentage is not "other".
                $sameBucket = ($rowCategory === $category)
                    && ($category === 'hostel' || $rowChallan === $challanCategory);
                if (!$sameBucket) {
                    continue;
                }

                if ($rowTypeId === $scholarshipTypeId) {
                    $clash = ['record_id' => $rowId, 'percentage' => $rowPct];
                } else {
                    $otherPct += $rowPct;
                }
            }

            $hostelAssigned = $this->studentHasHostelAssignment(
                $sessionId,
                ($student->app_id ?? $student->APP_ID) ? (int) ($student->app_id ?? $student->APP_ID) : null,
                $enrollment
            );

            $hostelFee = 0.0;
            $hostelReason = null;
            if ($category === 'hostel') {
                $resolved = $this->resolveHostelFeeBases($sessionId, [$enrollment])[$enrollment] ?? null;
                $hostelFee = (float) ($resolved['fee'] ?? 0);
                $hostelReason = $resolved['reason'] ?? null;
            }

            // Assembled as an object because attachDerivedAmounts() reads properties.
            $row = (object) [
                // An exact clash is an EDIT of the existing award, so it carries that
                // row's id and its current percentage. Everything else is a fresh row.
                'record_id' => $clash ? $clash['record_id'] : '',
                'category' => $category,
                'enrollment_no' => $enrollment,
                'student_name' => (string) ($student->student_name ?? $student->STUDENT_NAME ?? ''),
                'school_name' => (string) ($student->school_name ?? $student->SCHOOL_NAME ?? ''),
                'school_full_name' => (string) ($student->school_full_name ?? $student->SCHOOL_FULL_NAME ?? ''),
                'dept_name' => (string) ($student->dept_name ?? $student->DEPT_NAME ?? ''),
                'prog_name' => (string) ($student->prog_name ?? $student->PROG_NAME ?? ''),
                'cgpa' => $student->cgpa ?? $student->CGPA ?? null,
                'scholarship_id' => $scholarshipTypeId,
                'scholarship_name' => $typeMeta['name'],
                'scholarship_percentage' => $clash ? $clash['percentage'] : 0,
                'scholarship_amount' => null,
                'hostel_fee' => $hostelFee,
                'challan_category' => $challanCategory,
                'bulk_post_status' => '',
                'source_page' => '',
                'snapshot_base' => null,
                'category_other_pct' => round($otherPct, 2),
                'existing_categories' => implode(',', array_keys($existingCategories)),
                'hostel_assigned' => $hostelAssigned ? 1 : 0,
                'app_id' => $student->app_id ?? $student->APP_ID ?? null,
                'sess_id' => $sessionId,
            ];

            // Prices the row against THIS session's fee structure and decides, with the
            // same rules the save enforces, whether it can be awarded at all.
            $rows = $this->attachDerivedAmounts([$row], $sessionId);
            $row = $rows[0];

            // The hostel resolver knows exactly why a fee is missing; that beats the
            // generic "nothing to discount" the shared blocker would produce.
            if ($category === 'hostel' && $hostelReason && ($row->fee_base ?? 0) <= 0) {
                $row->award_block = ['reason' => 'hostel', 'message' => $hostelReason];
            }
        } catch (\Throwable $e) {
            \Log::error('Resolve student for award failed: ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Unable to load this student.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }

        return response()->json([
            'status' => true,
            'student' => $row,
            'existing' => $existing,
            'already_awarded' => !empty($existing),
            'is_edit' => (bool) $clash,
        ]);
    }

    // importScholarships() / importAcademicRow() / importHostelRow() /
    // assertImportCategoryCombination() were removed here. That flow pushed rows straight
    // into a DIFFERENT session's tables on click, skipped duplicates instead of
    // overwriting, and never set *_BULK_POST_STATUS — so imported scholarships were
    // invisible to Bulk Post and never reached a challan.
    //
    // Import is now getPreviousScholarships() above: read-only, staged on the client, and
    // committed through saveStudentScholarships(), which validates, prices and queues.

    /**
     * Inline update of scholarship percentage (and academic fee type) on a list row.
     */
    public function updateRow(Request $request)
    {
        $recordId = (int) $request->input('record_id');
        $category = strtolower(trim((string) $request->input('category', 'academic')));
        $challanCategory = trim((string) $request->input('challan_category', 'Full_semester'));

        if (!$recordId) {
            return response()->json(['status' => false, 'message' => 'Invalid record.'], 422);
        }

        // A blank percentage means "not entered yet", NOT zero. Coercing it with a bare
        // (float) cast is how live scholarships were being overwritten with 0% / 0 amount:
        // the list editor fires on every keystroke, including the transient empty string
        // while the user is retyping a value.
        $rawPercentage = $request->input('percentage');
        if ($rawPercentage === null || $rawPercentage === '' || !is_numeric($rawPercentage)) {
            return response()->json([
                'status' => false,
                'message' => 'Percentage is required and must be a number.',
            ], 422);
        }
        $percentage = (float) $rawPercentage;

        if ($percentage < 0 || $percentage > 100) {
            return response()->json(['status' => false, 'message' => 'Percentage must be between 0 and 100.'], 422);
        }

        $username = auth()->user()->username ?? 'system';
        $savedAmount = 0.0;
        $savedSessionId = (int) $request->input('session_id', 0);
        $snapshotWarnings = [];
        $snapshotEnrollment = '';
        $snapshotAaiId = null;

        try {
            if ($category === 'hostel') {
                $row = DB::table('fin_students_scholarships_hostel_details')
                    ->where('sshd_id', $recordId)
                    ->whereRaw("NVL(sshd_status, 'E') = 'E'")
                    ->first();

                if (!$row) {
                    return response()->json(['status' => false, 'message' => 'Hostel scholarship not found.'], 404);
                }

                $appId = $row->sshd_app_id ?? $row->SSHD_APP_ID ?? null;
                $sessId = $row->sshd_sess_id ?? $row->SSHD_SESS_ID ?? null;
                $savedSessionId = (int) $sessId;
                $enrollment = $row->sshd_sai_enrollment_no ?? $row->SSHD_SAI_ENROLLMENT_NO ?? null;
                if (trim((string) $enrollment) === '') {
                    // Same blank-enrolment case as the academic branch below.
                    $enrollment = $this->resolveEnrollmentForAppId($appId);
                }
                $hostelFee = (float) ($row->sshd_hostel_fee ?? $row->SSHD_HOSTEL_FEE ?? 0);

                // Owned by the single page — refuse. The session comes off the row, not
                // the request, so a crafted session_id cannot dodge the check.
                if ($this->studentIsSinglePageOwned((int) $sessId, (string) $enrollment)) {
                    return response()->json([
                        'status' => false,
                        'message' => $this->singlePageLockedMessage(strtoupper(trim((string) $enrollment))),
                        'locked_by_single_page' => true,
                    ], 422);
                }

                $otherQ = DB::table('fin_students_scholarships_hostel_details')
                    ->where('sshd_sess_id', $sessId)
                    ->whereRaw("NVL(sshd_status, 'E') = 'E'")
                    ->where('sshd_id', '!=', $recordId);
                $otherQ->where(function ($q) use ($appId, $enrollment) {
                    if ($appId) {
                        $q->where('sshd_app_id', $appId);
                    }
                    if ($enrollment) {
                        $q->orWhereRaw('UPPER(TRIM(sshd_sai_enrollment_no)) = ?', [strtoupper(trim((string) $enrollment))]);
                    }
                });
                $otherSum = (float) $otherQ->sum('sshd_percentage');

                if (($otherSum + $percentage) > 100) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Hostel scholarship total would exceed 100% ('
                            . $otherSum . '% already applied to this student).',
                    ], 422);
                }

                $enrollmentNorm = strtoupper(trim((string) $enrollment));

                // Same rules the drawer enforces — the inline % box used to bypass all of
                // them. Hostel assignment is only required when actually granting a
                // scholarship, so zeroing a legacy row stays possible.
                $validationError = $this->validateScholarshipRowsPayload(
                    [[
                        'category' => 'hostel',
                        'scholarship_id' => (int) ($row->sshd_type ?? $row->SSHD_TYPE ?? 0),
                        'percentage' => $percentage,
                    ]],
                    (int) $sessId,
                    $enrollmentNorm,
                    $appId ? (int) $appId : null,
                    [],
                    [$recordId],
                    $percentage > 0
                );
                if ($validationError !== null) {
                    return response()->json([
                        'status' => false,
                        'enrollment_no' => $enrollmentNorm,
                        'message' => ($enrollmentNorm !== '' ? $enrollmentNorm . ': ' : '') . $validationError,
                    ], 422);
                }

                // Amount is always derived server-side. The list has no per-student fee
                // matrix, so a client-sent amount was routinely 0 and clobbered a live figure.
                $feeBase = $hostelFee;
                if ($feeBase <= 0) {
                    // Recover the base this row was originally priced against.
                    $storedPct = (float) ($row->sshd_percentage ?? $row->SSHD_PERCENTAGE ?? 0);
                    $storedAmt = (float) ($row->sshd_amount ?? $row->SSHD_AMOUNT ?? 0);
                    if ($storedPct > 0 && $storedAmt > 0) {
                        $feeBase = round(($storedAmt / $storedPct) * 100, 2);
                    }
                }
                $hostelAmount = $feeBase > 0 ? round(($percentage / 100) * $feeBase, 2) : 0.0;
                $savedAmount = $hostelAmount;
                $snapshotEnrollment = $enrollmentNorm;
                $snapshotAaiId = $appId ? (int) $appId : null;

                // From here the write and the snapshot refresh must land together — a
                // committed percentage with a stale snapshot previews and posts the
                // wrong figures.
                DB::beginTransaction();
                DB::table('fin_students_scholarships_hostel_details')
                    ->where('sshd_id', $recordId)
                    ->update($this->mergeHostelRowPayload([
                        'sshd_percentage' => $percentage,
                        'sshd_amount' => $hostelAmount,
                        'sshd_bulk_post_status' => 'PENDING',
                        'sshd_entry_username' => $username,
                    ]));
            } else {
                $allowedCategories = [
                    'Full_semester',
                    'Credit_hours',
                    'additional_Full_semester',
                    'Short/Summer_Course',
                ];
                if (!in_array($challanCategory, $allowedCategories, true)) {
                    $challanCategory = 'Full_semester';
                }

                $row = DB::table('students_scholarships_details')
                    ->where('ssd_id', $recordId)
                    ->where('status', 'E')
                    ->first();

                if (!$row) {
                    return response()->json(['status' => false, 'message' => 'Academic scholarship not found.'], 404);
                }

                $appId = $row->ssd_app_id ?? $row->SSD_APP_ID ?? null;
                $sessId = $row->ssd_sess_id ?? $row->SSD_SESS_ID ?? null;
                $savedSessionId = (int) $sessId;
                $enrollmentForAmount = strtoupper(trim((string) ($row->ssd_sai_enrollment_no ?? $row->SSD_SAI_ENROLLMENT_NO ?? '')));
                if ($enrollmentForAmount === '') {
                    // Blank on rows keyed by applicant id — new admissions. Every fee
                    // lookup below takes the enrolment, so without this the row prices
                    // fine in the list (which resolves it via admission_apply_info) and
                    // then fails to save with "Student record not found".
                    $enrollmentForAmount = $this->resolveEnrollmentForAppId($appId);
                }

                // Owned by the single page — refuse. The session comes off the row, not
                // the request, so a crafted session_id cannot dodge the check.
                if ($this->studentIsSinglePageOwned((int) $sessId, $enrollmentForAmount)) {
                    return response()->json([
                        'status' => false,
                        'message' => $this->singlePageLockedMessage($enrollmentForAmount),
                        'locked_by_single_page' => true,
                    ], 422);
                }

                // Keyed on app_id OR enrollment, matching saveStudentScholarships(). Keying
                // on ssd_app_id alone made this a no-op whenever that column was NULL —
                // Oracle's `= NULL` matches nothing, so the cap silently passed.
                $otherSum = (float) DB::table('students_scholarships_details')
                    ->where('ssd_sess_id', $sessId)
                    ->where('status', 'E')
                    ->where('ssd_id', '!=', $recordId)
                    ->whereRaw("NVL(ssd_challan_category, 'Full_semester') = ?", [$challanCategory])
                    ->where(function ($q) use ($appId, $enrollmentForAmount) {
                        if ($appId) {
                            $q->where('ssd_app_id', $appId);
                        }
                        if ($enrollmentForAmount !== '') {
                            $q->orWhereRaw('UPPER(TRIM(ssd_sai_enrollment_no)) = ?', [$enrollmentForAmount]);
                        }
                    })
                    ->sum('ssd_percentage');

                if (($otherSum + $percentage) > 100) {
                    $label = $this->academicCategoryLabels()[$challanCategory] ?? $challanCategory;

                    return response()->json([
                        'status' => false,
                        'enrollment_no' => $enrollmentForAmount,
                        'message' => ($enrollmentForAmount !== '' ? $enrollmentForAmount . ': ' : '')
                            . "academic total for {$label} would exceed 100% "
                            . "({$otherSum}% already applied by this student's other scholarships).",
                        'other_percentage' => $otherSum,
                        'max_percentage' => max(0, 100 - $otherSum),
                    ], 422);
                }

                // Same rules the drawer enforces (fee-type combination legality, fee
                // resolvability) — the inline % box used to bypass all of them.
                $validationError = $this->validateScholarshipRowsPayload(
                    [[
                        'category' => 'academic',
                        'scholarship_id' => (int) ($row->ssd_type ?? $row->SSD_TYPE ?? 0),
                        'percentage' => $percentage,
                        'challan_category' => $challanCategory,
                    ]],
                    (int) $sessId,
                    $enrollmentForAmount,
                    $appId ? (int) $appId : null,
                    [$recordId],
                    []
                );
                if ($validationError !== null) {
                    return response()->json([
                        'status' => false,
                        'enrollment_no' => $enrollmentForAmount,
                        'message' => ($enrollmentForAmount !== '' ? $enrollmentForAmount . ': ' : '') . $validationError,
                    ], 422);
                }

                // Always priced server-side. The list has no per-student fee matrix, so the
                // client's amount was computed from whichever student the drawer last opened
                // (or from nothing at all) and was routinely 0.
                $academicAmount = $this->computeAcademicScholarshipAmount(
                    (int) $sessId,
                    $enrollmentForAmount,
                    $challanCategory,
                    $percentage
                );
                $savedAmount = $academicAmount;
                $snapshotEnrollment = $enrollmentForAmount;
                $snapshotAaiId = $appId ? (int) $appId : null;

                DB::beginTransaction();
                DB::table('students_scholarships_details')
                    ->where('ssd_id', $recordId)
                    ->update($this->mergeAcademicRowPayload([
                        'ssd_percentage' => $percentage,
                        'ssd_amount' => $academicAmount,
                        'ssd_bulk_post_status' => 'PENDING',
                        'ssd_challan_category' => $challanCategory,
                        'scd_entry_username' => $username,
                        'update_datetime' => now()->toDateTimeString(),
                    ]));
            }

            // Re-freeze the whole student: the per-challan figures describe a category,
            // so one row changing re-prices every row in it.
            $snapshot = $this->refreshScholarshipSnapshot(
                (int) $savedSessionId,
                $snapshotAaiId,
                $snapshotEnrollment
            );
            $snapshotWarnings = $snapshot['warnings'] ?? [];

            DB::commit();
        } catch (\Throwable $e) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
            \Log::error('Scholarship row update failed: ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => $this->humanDbFailure($e, 'Update failed. Please try again.'),
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }

        $queuedForPost = $this->canQueueCategory($category);
        $message = 'Scholarship updated successfully.';
        if (!empty($snapshotWarnings)) {
            $message .= ' ' . implode(' ', array_slice($snapshotWarnings, 0, 2));
        }

        return response()->json(array_merge([
            'status' => true,
            'message' => $message,
            'percentage' => $percentage,
            'amount' => $savedAmount,
            'challan_category' => $category === 'hostel' ? 'Hostel_Fee' : $challanCategory,
            'snapshot_warnings' => $snapshotWarnings,
        ], $this->saveQueueResponseMeta(
            $savedSessionId ?: (int) $request->input('session_id', 0),
            $queuedForPost
        )));
    }

    /** Mirrors StudentRecordRevisedController::allowedCategoryCombinations(). */
    protected function allowedCategoryCombinations(): array
    {
        return [
            [],
            ['Full_semester'],
            ['Credit_hours'],
            ['additional_Full_semester'],
            ['Short/Summer_Course'],
            ['Full_semester', 'additional_Full_semester'],
            ['Short/Summer_Course', 'additional_Full_semester'],
        ];
    }

    /**
     * Display wording for the academic challan categories.
     *
     * Must read identically to feeTypeLabel() in scholarshipForm.blade.php — a server
     * message and the dropdown the user picked from should not name the same fee type
     * two different ways.
     *
     * The KEYS are load-bearing: array_keys() of this drives the surplus walk order in
     * BuildsScholarshipSnapshots and PostsScholarshipSnapshots. Only reword the values.
     */
    protected function academicCategoryLabels(): array
    {
        return [
            'Full_semester' => 'Full Semester',
            'Credit_hours' => 'Credit Hours',
            'additional_Full_semester' => 'Additional Credit Hr',
            'Short/Summer_Course' => 'Short / Summer',
        ];
    }

    protected function assertLegalCategoryCombination(array $categories, array $exempt = []): void
    {
        $checkSet = array_values(array_diff(array_unique($categories), $exempt));
        sort($checkSet);

        foreach ($this->allowedCategoryCombinations() as $combo) {
            if ($combo === $checkSet) {
                return;
            }
        }

        $labels = $this->academicCategoryLabels();
        $named = [];
        foreach ($checkSet as $cat) {
            $named[] = $labels[$cat] ?? $cat;
        }

        // Spell the legal pairs from the same labels, so the rule cannot be described in
        // one wording and enforced against another.
        $pairs = [];
        foreach ($this->allowedCategoryCombinations() as $combo) {
            if (count($combo) !== 2) {
                continue;
            }
            $pairs[] = implode(' + ', array_map(function ($cat) use ($labels) {
                return $labels[$cat] ?? $cat;
            }, $combo));
        }

        throw new \InvalidArgumentException(
            '"' . implode('" and "', $named) . '" cannot be applied together in the same session. '
            . 'Only ' . implode(', or ', $pairs) . ', may be combined.'
        );
    }

    protected function studentHasHostelAssignment(int $sessionId, ?int $aaiId, string $enrollment): bool
    {
        return DB::table('hostel_assign')
            ->where('ha_session', $sessionId)
            // One shared definition of "really assigned" — see the fragment's docblock for
            // why a recorded session type counts and a location PREFERENCE does not.
            ->whereRaw(HostelAssignmentResolver::assignmentExistsSql('hostel_assign'))
            ->where(function ($q) use ($aaiId, $enrollment) {
                if ($aaiId) {
                    $q->where('ha_aai_id', $aaiId);
                }
                if ($enrollment !== '') {
                    $q->orWhereRaw('UPPER(TRIM(ha_enrolment_no)) = ?', [strtoupper(trim($enrollment))]);
                }
            })
            ->exists();
    }

    /**
     * Client + server validation mirror of student-record-revised rules.
     */
    protected function validateScholarshipRowsPayload(
        array $rows,
        int $sessionId,
        string $enrollmentNo,
        ?int $aaiId,
        array $excludeAcademicIds = [],
        array $excludeHostelIds = [],
        bool $checkHostelAssignment = true
    ): ?string {
        $catSums = [];
        $catTypeKeys = [];
        $payloadCategories = [];
        $hasHostel = false;
        $hostelSum = 0.0;
        $hostelTypeIds = [];
        $hasActiveRow = false;

        foreach ($rows as $row) {
            if ($this->rowIsRemoval($row)) {
                continue;
            }
            $hasActiveRow = true;
            $category = strtolower(trim((string) ($row['category'] ?? 'academic')));
            $typeId = (int) ($row['scholarship_id'] ?? 0);
            $pct = (float) ($row['percentage'] ?? 0);

            if ($category === 'hostel') {
                $hasHostel = true;
                if (!$typeId) {
                    return 'Select a scholarship type for every hostel row.';
                }
                if ($pct < 0 || $pct > 100) {
                    return 'Hostel percentage must be between 0 and 100.';
                }
                if (in_array($typeId, $hostelTypeIds, true)) {
                    return 'The same hostel scholarship type is used twice.';
                }
                $hostelTypeIds[] = $typeId;
                $hostelSum += $pct;
                continue;
            }

            $challan = trim((string) ($row['challan_category'] ?? 'Full_semester')) ?: 'Full_semester';
            if (!$typeId) {
                return 'Select a scholarship type for every academic row.';
            }
            if ($pct < 0 || $pct > 100) {
                $label = $this->academicCategoryLabels()[$challan] ?? $challan;

                return "Academic percentage must be between 0 and 100 in \"{$label}\".";
            }

            $typeKey = $challan . '|' . $typeId;
            if (isset($catTypeKeys[$typeKey])) {
                $label = $this->academicCategoryLabels()[$challan] ?? $challan;

                return "The same scholarship type is used twice in \"{$label}\".";
            }
            $catTypeKeys[$typeKey] = true;
            $catSums[$challan] = ($catSums[$challan] ?? 0) + $pct;
            $payloadCategories[$challan] = true;

            if ($pct > 0) {
                $feeErr = $this->assertAcademicCategoryResolvable($sessionId, $enrollmentNo, $challan);
                if ($feeErr !== null) {
                    return $feeErr;
                }
            }
        }

        if (!$hasActiveRow && empty(array_filter($rows, function ($r) {
            return $this->rowIsRemoval($r);
        }))) {
            return 'Add at least one scholarship row.';
        }

        foreach ($catSums as $challan => $sum) {
            if ($sum > 100) {
                $label = $this->academicCategoryLabels()[$challan] ?? $challan;

                return "Academic total % for {$label} exceeds 100% ({$sum}%).";
            }
        }
        if ($hostelSum > 100) {
            return "Hostel scholarship total exceeds 100% ({$hostelSum}%).";
        }

        if ($hasHostel && $checkHostelAssignment && !$this->studentHasHostelAssignment($sessionId, $aaiId, $enrollmentNo)) {
            return 'No hostel is assigned to this student for the selected session — hostel scholarship not allowed.';
        }

        $effectiveCategories = array_keys($payloadCategories);
        $existingRows = DB::table('students_scholarships_details')
            ->where('ssd_sess_id', $sessionId)
            ->where('status', 'E')
            ->where(function ($q) use ($aaiId, $enrollmentNo) {
                if ($aaiId) {
                    $q->where('ssd_app_id', $aaiId);
                }
                $q->orWhereRaw('UPPER(TRIM(ssd_sai_enrollment_no)) = ?', [$enrollmentNo]);
            })
            ->when(!empty($excludeAcademicIds), function ($q) use ($excludeAcademicIds) {
                $q->whereNotIn('ssd_id', $excludeAcademicIds);
            })
            ->get(['ssd_challan_category']);

        foreach ($existingRows as $existingRow) {
            $cat = trim((string) ($existingRow->ssd_challan_category ?? 'Full_semester')) ?: 'Full_semester';
            if (!isset($payloadCategories[$cat])) {
                $effectiveCategories[] = $cat;
            }
        }

        try {
            $this->assertLegalCategoryCombination($effectiveCategories);
        } catch (\InvalidArgumentException $e) {
            return $e->getMessage();
        }

        return null;
    }

    /**
     * Academic category must have a resolvable fee base (e.g. Addl Credit Hr needs challan credits).
     *
     * Goes through academicFeeBaseFor() so the gate that BLOCKS a save is the same one
     * that PRICES it. It used to call resolveAcademicFeeStructure(..., 'Existing', ...)
     * directly, which meant a programme carrying only a 'New Admission <SEASON>' fee row
     * was priced happily by the list and the snapshot and then refused here.
     */
    protected function assertAcademicCategoryResolvable(int $sessionId, string $enrollmentNo, string $challanCategory): ?string
    {
        $challanCategory = trim($challanCategory) ?: 'Full_semester';
        if ($challanCategory === 'Hostel_Fee') {
            return null;
        }

        if ($this->academicFeeBaseFor($sessionId, $enrollmentNo, $challanCategory) > 0) {
            return null;
        }

        // Nothing resolved — re-ask for the reason text, which explains the credit-hour
        // challan case in the wording the drawer already shows.
        $res = $this->resolveAcademicFeeStructure($sessionId, $enrollmentNo, 'Existing', $challanCategory);
        $label = $this->academicCategoryLabels()[$challanCategory] ?? $challanCategory;
        $message = trim((string) ($res['message'] ?? 'Fee structure unavailable for this student.'));

        // Name the inputs. "Student record not found" on its own cannot tell a genuinely
        // missing student apart from a blank or wrong enrolment arriving here, and the two
        // need completely different fixes — which cost a full debugging session once.
        $who = $enrollmentNo !== '' ? $enrollmentNo : '(no enrolment number was sent)';

        return "Cannot apply \"{$label}\" scholarship for {$who} in session {$sessionId}: {$message}";
    }

    /**
     * Save multiple scholarship lines for one student (add BEEF + KEEF etc.).
     * Matches student-record-revised: % per challan category capped at 100.
     */
    public function saveStudentScholarships(Request $request)
    {
        $enrollmentNo = strtoupper(trim((string) $request->input('enrollment_no')));
        $sessionId = (int) $request->input('session_id');
        $aaiId = $this->normalizeId($request->input('aai_id'));
        $rows = $request->input('rows', []);

        // An application id alone is enough. A scholarship row is keyed by ssd_app_id, and
        // the students whose CMS_SAI_ENROLLMENT_NO is NULL have no enrolment number to be
        // saved by — refusing them here is what made the drawer openable but useless.
        if (($enrollmentNo === '' && !$aaiId) || !$sessionId) {
            return response()->json([
                'status' => false,
                'message' => 'A student ID or application ID, plus the session, are required.',
            ], 422);
        }
        if (!is_array($rows) || empty($rows)) {
            return response()->json(['status' => false, 'message' => 'Add at least one scholarship row.'], 422);
        }

        // The permission boundary. The client greys these students out, but the client is
        // advisory — this is the check that actually holds. It sits before the
        // transaction, and before refreshScholarshipSnapshot(), which would otherwise
        // re-stamp ownership of every row this student has.
        if ($enrollmentNo !== '' && $this->studentIsSinglePageOwned($sessionId, $enrollmentNo)) {
            return response()->json([
                'status' => false,
                'message' => $this->singlePageLockedMessage($enrollmentNo),
                'locked_by_single_page' => true,
            ], 422);
        }

        $payloadAcademicIds = [];
        $payloadHostelIds = [];
        foreach ($rows as $row) {
            $rid = (int) ($row['record_id'] ?? 0);
            if (!$rid) {
                continue;
            }
            if (strtolower(trim((string) ($row['category'] ?? 'academic'))) === 'hostel') {
                $payloadHostelIds[] = $rid;
            } else {
                $payloadAcademicIds[] = $rid;
            }
        }

        $validationError = $this->validateScholarshipRowsPayload(
            $rows,
            $sessionId,
            $enrollmentNo,
            $aaiId,
            $payloadAcademicIds,
            $payloadHostelIds
        );
        if ($validationError !== null) {
            // Save Checked fires one request per student in parallel, so a bare message
            // gives the user no way to tell WHICH student was rejected.
            return response()->json([
                'status' => false,
                'enrollment_no' => $enrollmentNo,
                'message' => $enrollmentNo . ': ' . $validationError,
            ], 422);
        }

        // Cap academic % per challan category
        $catSums = [];
        $hostelSum = 0.0;
        foreach ($rows as $row) {
            if ($this->rowIsRemoval($row)) {
                continue;
            }
            $cat = strtolower(trim((string) ($row['category'] ?? 'academic')));
            $pct = (float) ($row['percentage'] ?? 0);
            if ($cat === 'hostel') {
                $hostelSum += $pct;
                continue;
            }
            $challan = trim((string) ($row['challan_category'] ?? 'Full_semester')) ?: 'Full_semester';
            $catSums[$challan] = ($catSums[$challan] ?? 0) + $pct;
        }
        foreach ($catSums as $challan => $sum) {
            if ($sum > 100) {
                return response()->json([
                    'status' => false,
                    'message' => "Academic total % for {$challan} exceeds 100% ({$sum}%).",
                ], 422);
            }
        }
        if ($hostelSum > 100) {
            return response()->json([
                'status' => false,
                'message' => "Hostel scholarship total exceeds 100% ({$hostelSum}%).",
            ], 422);
        }

        // Re-check against rows already in the DB but not part of this payload (e.g. added by
        // another admin/tab since this drawer was loaded) — the sums above only cover what the
        // client sent, so a stale drawer could otherwise push a category over 100%.
        foreach ($catSums as $challan => $sum) {
            $dbOtherSum = (float) DB::table('students_scholarships_details')
                ->where('status', 'E')
                // Session filter is essential: without it this summed the student's rows
                // across EVERY session, so 50% in S2026 + 50% in F2026 falsely blocked any
                // save in a third session.
                ->where('ssd_sess_id', $sessionId)
                ->whereRaw("NVL(ssd_challan_category, 'Full_semester') = ?", [$challan])
                ->where(function ($q) use ($aaiId, $enrollmentNo) {
                    if ($aaiId) {
                        $q->where('ssd_app_id', $aaiId);
                    }
                    $q->orWhereRaw('UPPER(TRIM(ssd_sai_enrollment_no)) = ?', [$enrollmentNo]);
                })
                ->when(!empty($payloadAcademicIds), function ($q) use ($payloadAcademicIds) {
                    $q->whereNotIn('ssd_id', $payloadAcademicIds);
                })
                ->sum('ssd_percentage');

            if (($dbOtherSum + $sum) > 100) {
                return response()->json([
                    'status' => false,
                    'message' => "Academic total % for {$challan} would exceed 100% ({$dbOtherSum}% already saved elsewhere + {$sum}% submitted). Reload this student and try again.",
                ], 422);
            }
        }

        if ($hostelSum > 0) {
            $dbHostelOtherSum = (float) DB::table('fin_students_scholarships_hostel_details')
                ->whereRaw("NVL(sshd_status, 'E') = 'E'")
                // Session filter — same bug as the academic re-check above.
                ->where('sshd_sess_id', $sessionId)
                ->where(function ($q) use ($aaiId, $enrollmentNo) {
                    if ($aaiId) {
                        $q->where('sshd_app_id', $aaiId);
                    }
                    $q->orWhereRaw('UPPER(TRIM(sshd_sai_enrollment_no)) = ?', [$enrollmentNo]);
                })
                ->when(!empty($payloadHostelIds), function ($q) use ($payloadHostelIds) {
                    $q->whereNotIn('sshd_id', $payloadHostelIds);
                })
                ->sum('sshd_percentage');

            if (($dbHostelOtherSum + $hostelSum) > 100) {
                return response()->json([
                    'status' => false,
                    'message' => "Hostel scholarship total would exceed 100% ({$dbHostelOtherSum}% already saved elsewhere + {$hostelSum}% submitted). Reload this student and try again.",
                ], 422);
            }
        }

        // Arrears cap check — the four one-time-fee components can never exceed the fee
        // structure's own admission/security/activity/medical amounts for that category
        // (mirrors StudentRecordRevisedController::runSave()'s per-component cap).
        // Resolved once per distinct challan_category present in the batch.
        $arrearsFeeCache = [];
        $arrearsLabels = [
            'admission' => 'Admission Fee',
            'security' => 'Security Fee',
            'activity' => 'Activity Charges',
            'medical' => 'Medical Insurance',
        ];
        foreach ($rows as $row) {
            if ($this->rowIsRemoval($row)) {
                continue;
            }
            if (strtolower(trim((string) ($row['category'] ?? 'academic'))) === 'hostel') {
                continue;
            }
            $arrearsInput = is_array($row['arrears'] ?? null) ? $row['arrears'] : [];
            if (empty($arrearsInput)) {
                continue;
            }
            $rowChallanCategory = trim((string) ($row['challan_category'] ?? 'Full_semester')) ?: 'Full_semester';
            if (!$this->challanCategoryHasArrears($rowChallanCategory)) {
                continue;
            }
            if (!array_key_exists($rowChallanCategory, $arrearsFeeCache)) {
                $res = $this->resolvePreviewFeeStructure($sessionId, $enrollmentNo, $rowChallanCategory);
                $arrearsFeeCache[$rowChallanCategory] = !empty($res['ok']) ? $res : null;
            }
            $feeRes = $arrearsFeeCache[$rowChallanCategory];

            foreach ($arrearsLabels as $component => $label) {
                $amount = (float) ($arrearsInput[$component] ?? 0);
                if ($amount < 0) {
                    return response()->json([
                        'status' => false,
                        'message' => $enrollmentNo . ': ' . $label . ' arrears cannot be negative.',
                    ], 422);
                }
                // No fee structure to cap against — let it through rather than blocking
                // a save over a category that cannot currently be priced.
                if (!$feeRes) {
                    continue;
                }
                $cap = (float) ($feeRes['arrearComponents'][$component] ?? 0);
                if ($amount > $cap) {
                    return response()->json([
                        'status' => false,
                        'message' => $enrollmentNo . ': ' . $label . ' arrears (' . $amount . ') exceed the fee '
                            . 'structure\'s ' . $label . ' amount (' . $cap . ') for "' . $rowChallanCategory . '".',
                    ], 422);
                }
            }
        }

        $username = auth()->user()->username ?? 'system';
        $saved = 0;
        $queuedForPost = false;
        $snapshotWarnings = [];

        DB::beginTransaction();
        try {
            foreach ($rows as $row) {
                $recordId = (int) ($row['record_id'] ?? 0);
                $category = strtolower(trim((string) ($row['category'] ?? 'academic')));
                $typeId = (int) ($row['scholarship_id'] ?? 0);
                $percentage = (float) ($row['percentage'] ?? 0);
                $challanCategory = trim((string) ($row['challan_category'] ?? 'Full_semester')) ?: 'Full_semester';
                $remove = $this->rowIsRemoval($row);

                if ($remove && $recordId) {
                    // Don't hard-delete: the row may already be reflected in a live challan
                    // (posted outside this draft, or in an earlier bulk-post run). Zero it out
                    // and re-queue it so the bulk-post step regenerates the challan without the
                    // discount, instead of silently leaving a stale discount on record.
                    if ($category === 'hostel') {
                        DB::table('fin_students_scholarships_hostel_details')
                            ->where('sshd_id', $recordId)
                            ->update($this->mergeHostelRowPayload([
                                'sshd_percentage' => 0,
                                'sshd_amount' => 0,
                                'sshd_bulk_post_status' => 'PENDING',
                                'sshd_entry_username' => $username,
                            ]));
                    } else {
                        DB::table('students_scholarships_details')
                            ->where('ssd_id', $recordId)
                            ->update($this->mergeAcademicRowPayload([
                                'ssd_percentage' => 0,
                                'ssd_amount' => 0,
                                'ssd_arrear' => 0,
                                'ssd_bulk_post_status' => 'PENDING',
                                'scd_entry_username' => $username,
                                'update_datetime' => now()->toDateTimeString(),
                            ]));

                        // The arrears grid follows the award it was attached to — remove the
                        // scholarship and its one-time-fee waiver goes with it.
                        if ($typeId && $this->challanCategoryHasArrears($challanCategory)) {
                            $this->persistArrearsComponents(
                                $aaiId,
                                $enrollmentNo,
                                $sessionId,
                                $typeId,
                                $challanCategory,
                                ['admission' => 0.0, 'security' => 0.0, 'activity' => 0.0, 'medical' => 0.0],
                                $username
                            );
                        }
                    }

                    // The removal changes the whole challan, not just this line: what gets
                    // re-issued is the fee minus whatever the student still holds. Queue
                    // every award on that challan so the post reads as one recalculated
                    // document rather than a single zeroed row with its siblings unnamed.
                    $this->requeueChallanCategory(
                        $sessionId,
                        $aaiId,
                        $enrollmentNo,
                        $category === 'hostel' ? 'Hostel_Fee' : $challanCategory
                    );

                    $saved++;
                    $queuedForPost = true;
                    continue;
                }

                if (!$typeId || $percentage < 0) {
                    continue;
                }

                if ($category === 'hostel') {
                    $hostelFee = (float) ($row['hostel_fee'] ?? 0);
                    $hostelAmount = (float) ($row['hostel_amount'] ?? 0);
                    if ($hostelFee > 0 && $hostelAmount <= 0 && $percentage > 0) {
                        $hostelAmount = ($percentage / 100) * $hostelFee;
                    }

                    // An untouched row must not be re-queued. The drawer always resends
                    // every scholarship a student holds — Apply to List needs the whole
                    // set to validate caps and combinations — so editing only the hostel
                    // award still submits the academic rows exactly as they already are.
                    // Without this check, every save stamped bulk_post_status = 'PENDING'
                    // on rows nobody changed, and they reappeared on Bulk Post uninvited.
                    if ($recordId) {
                        $existing = DB::table('fin_students_scholarships_hostel_details')
                            ->where('sshd_id', $recordId)
                            ->first(['sshd_type', 'sshd_percentage']);
                        if ($existing
                            && (int) $existing->sshd_type === $typeId
                            && abs((float) $existing->sshd_percentage - $percentage) < 0.005) {
                            continue;
                        }
                    }

                    $hostelPayload = $this->mergeHostelRowPayload([
                        'sshd_type' => $typeId,
                        'sshd_percentage' => $percentage,
                        'sshd_amount' => $hostelAmount,
                        'sshd_hostel_fee' => $hostelFee,
                        'sshd_bulk_post_status' => 'PENDING',
                        'sshd_entry_username' => $username,
                    ]);
                    if ($recordId) {
                        DB::table('fin_students_scholarships_hostel_details')
                            ->where('sshd_id', $recordId)
                            ->update($hostelPayload);
                    } else {
                        DB::table('fin_students_scholarships_hostel_details')->insert(array_merge($hostelPayload, [
                            // NULL, never '': an empty string here would look like a real
                            // enrolment number to every NVL()/TRIM() lookup downstream.
                            'sshd_sai_enrollment_no' => $enrollmentNo !== '' ? $enrollmentNo : null,
                            'sshd_app_id' => $aaiId,
                            'sshd_status' => 'E',
                            'sshd_sess_id' => $sessionId,
                            'sshd_entry_datetime' => now()->toDateTimeString(),
                        ]));
                    }
                } else {
                    $academicAmount = (float) ($row['scholarship_amount'] ?? $row['amount'] ?? 0);
                    if ($academicAmount <= 0 && $percentage > 0) {
                        $academicAmount = $this->computeAcademicScholarshipAmount(
                            $sessionId,
                            $enrollmentNo,
                            $challanCategory,
                            $percentage
                        );
                    }

                    // The one-time-fee arrears grid — admission/security/activity/medical —
                    // only applies to categories that can carry arrears at all (see
                    // arrearsChallanCategories()). Already capped against the fee structure
                    // above, before the transaction opened.
                    //
                    // "Sent" vs "not sent" matters: several callers resend a student's whole
                    // row set without ever having loaded the arrears grid (quick-remove from
                    // the list, an inline % edit elsewhere in this same batch). Treating an
                    // absent `arrears` key as "clear it" would silently zero out real arrears
                    // on every one of those saves. Only an explicit `arrears` object — even
                    // one that is all zeros — is taken as "this is what it should be now".
                    $arrearsSent = $this->challanCategoryHasArrears($challanCategory)
                        && array_key_exists('arrears', $row)
                        && is_array($row['arrears']);
                    $arrearComponents = null;
                    $arrearTotal = null;
                    if ($arrearsSent) {
                        $arrearComponents = ['admission' => 0.0, 'security' => 0.0, 'activity' => 0.0, 'medical' => 0.0];
                        foreach ($arrearComponents as $component => $_) {
                            $arrearComponents[$component] = max(0.0, (float) ($row['arrears'][$component] ?? 0));
                        }
                        $arrearTotal = array_sum($arrearComponents);
                    }

                    // Same guard as the hostel branch above: skip an academic row the
                    // payload merely resent unchanged, so it is not re-queued for post.
                    // Arrear only enters the comparison when this save actually sent one —
                    // otherwise every arrears-blind caller above would see a "change" on
                    // every row of theirs and re-queue challans nobody touched.
                    if ($recordId) {
                        $existing = DB::table('students_scholarships_details')
                            ->where('ssd_id', $recordId)
                            ->first(['ssd_type', 'ssd_percentage', 'ssd_challan_category', 'ssd_arrear']);

                        // Component by component, NOT against the SSD_ARREAR total. Moving
                        // 2,000 from Admission to Security leaves the total identical, and
                        // comparing totals skipped the row — persistArrearsComponents() was
                        // never called and FIN_OTHER_SCHOLARSHIPS kept the old split. That
                        // was invisible while the bulk snapshot discounted nothing; now that
                        // it writes per-component figures, the challan would print the wrong
                        // lines.
                        $arrearUnchanged = true;
                        if ($arrearsSent) {
                            $existingArrears = $this->findArrearsRow(
                                $aaiId,
                                $enrollmentNo,
                                $sessionId,
                                $typeId,
                                $challanCategory
                            );
                            $existingComponents = [
                                'admission' => (float) ($existingArrears->fos_admission_fee ?? 0),
                                'security' => (float) ($existingArrears->fos_security_fee ?? 0),
                                'activity' => (float) ($existingArrears->fos_activity_charges ?? 0),
                                'medical' => (float) ($existingArrears->fos_medical_insurance ?? 0),
                            ];
                            foreach ($arrearComponents as $component => $amount) {
                                if (abs($existingComponents[$component] - $amount) >= 0.005) {
                                    $arrearUnchanged = false;
                                    break;
                                }
                            }
                            // The row's own stamp has to agree too, or a challan posts with
                            // an SSD_ARREAR that its components no longer add up to.
                            if ($arrearUnchanged && abs((float) ($existing->ssd_arrear ?? 0) - $arrearTotal) >= 0.005) {
                                $arrearUnchanged = false;
                            }
                        }
                        if ($existing
                            && (int) $existing->ssd_type === $typeId
                            && abs((float) $existing->ssd_percentage - $percentage) < 0.005
                            && (trim((string) ($existing->ssd_challan_category ?? '')) ?: 'Full_semester') === $challanCategory
                            && $arrearUnchanged) {
                            continue;
                        }
                    }

                    if ($recordId) {
                        $updatePayload = [
                            'ssd_type' => $typeId,
                            'ssd_percentage' => $percentage,
                            'ssd_amount' => $academicAmount,
                            'ssd_bulk_post_status' => 'PENDING',
                            'ssd_challan_category' => $challanCategory,
                            'scd_entry_username' => $username,
                            'update_datetime' => now()->toDateTimeString(),
                        ];
                        // Omitted entirely when not sent, so the UPDATE leaves the column
                        // exactly as it is rather than writing a 0 over a real value.
                        if ($arrearsSent) {
                            $updatePayload['ssd_arrear'] = $arrearTotal;
                        }
                        DB::table('students_scholarships_details')
                            ->where('ssd_id', $recordId)
                            ->update($this->mergeAcademicRowPayload($updatePayload));
                    } else {
                        // Skip duplicate type+category
                        $dup = DB::table('students_scholarships_details')
                            ->where('ssd_sess_id', $sessionId)
                            ->where('ssd_type', $typeId)
                            ->where('status', 'E')
                            ->whereRaw("NVL(ssd_challan_category, 'Full_semester') = ?", [$challanCategory])
                            ->where(function ($q) use ($aaiId, $enrollmentNo) {
                                if ($aaiId) {
                                    $q->where('ssd_app_id', $aaiId);
                                }
                                $q->orWhereRaw('UPPER(TRIM(ssd_sai_enrollment_no)) = ?', [$enrollmentNo]);
                            })
                            ->exists();
                        if ($dup) {
                            continue;
                        }

                        DB::table('students_scholarships_details')->insert($this->mergeAcademicRowPayload([
                            // NULL, never '' — see the hostel insert above.
                            'ssd_sai_enrollment_no' => $enrollmentNo !== '' ? $enrollmentNo : null,
                            'ssd_app_id' => $aaiId,
                            'ssd_type' => $typeId,
                            'ssd_percentage' => $percentage,
                            'ssd_amount' => $academicAmount,
                            // A brand-new row has nothing to preserve — default to 0 when
                            // no arrears were sent, same as every other figure on this row.
                            'ssd_arrear' => $arrearsSent ? $arrearTotal : 0,
                            'ssd_bulk_post_status' => 'PENDING',
                            'ssd_sess_id' => $sessionId,
                            'ssd_project' => 'SCH_PRO_NEW',
                            'ssd_challan_category' => $challanCategory,
                            'scd_entry_username' => $username,
                            'status' => 'E',
                        ]));
                    }

                    if ($arrearsSent) {
                        $this->persistArrearsComponents(
                            $aaiId,
                            $enrollmentNo,
                            $sessionId,
                            $typeId,
                            $challanCategory,
                            $arrearComponents,
                            $username
                        );
                    }
                }
                $saved++;
                if ($this->canQueueCategory($category)) {
                    $queuedForPost = true;
                }
            }

            // Freeze the fee picture behind what was just written. Runs after the loop
            // because the per-challan figures describe the whole category and can only
            // be computed once every row's percentage is in place.
            $snapshot = $this->refreshScholarshipSnapshot($sessionId, $aaiId, $enrollmentNo);
            $snapshotWarnings = $snapshot['warnings'] ?? [];

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Scholarship form save failed: ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => $this->humanDbFailure($e, 'Save failed. Please try again.'),
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }

        $message = "{$saved} scholarship row(s) saved.";
        if (!empty($snapshotWarnings)) {
            // A category with no resolvable fee structure still saves, but it has no
            // priced snapshot — say so rather than letting it look fully staged.
            $message .= ' ' . implode(' ', array_slice($snapshotWarnings, 0, 3));
        }

        return response()->json(array_merge([
            'status' => true,
            'message' => $message,
            'saved' => $saved,
            'snapshot_warnings' => $snapshotWarnings,
        ], $this->saveQueueResponseMeta($sessionId, $queuedForPost)));
    }

    private function normalizeId($value): ?int
    {
        if ($value === null || $value === '' || $value === 'null') {
            return null;
        }

        // Ignore JSON "All Schools / All Departments" array values
        if (is_string($value) && strlen($value) > 0 && ($value[0] === '[' || $value[0] === '{')) {
            return null;
        }

        if (!is_numeric($value)) {
            return null;
        }

        return (int) $value;
    }

    private function extractProgramId($value): ?int
    {
        if ($value === null || $value === '' || $value === 'null') {
            return null;
        }

        if (is_string($value) && strlen($value) > 0 && ($value[0] === '[' || $value[0] === '{')) {
            return null;
        }

        if (is_string($value) && strpos($value, '~~') !== false) {
            $parts = explode('~~', $value);
            $value = end($parts);
        }

        return $this->normalizeId($value);
    }

    /**
     * The session this page is pinned to.
     *
     * Highest active SESS_ID. That is not a new rule — the picker this replaced listed
     * `sess_status = 'E' ORDER BY sess_id DESC` and pre-selected its first entry, so the
     * page already opened on exactly this session whenever it was reached without a
     * ?session_id. It is also the convention used elsewhere for "current session"
     * (HostelController::…, HostelReportController::…), which order by SESS_ID DESC and
     * take the first row. There is no flag column on ACADEMIC_SESSIONS to read instead.
     *
     * @return object{sess_id:int, sess_code:string}
     */
    protected function resolveCurrentSession()
    {
        $row = DB::selectOne("
            SELECT sess_id, sess_code, sess_name
            FROM (
                SELECT sess_id, sess_code, sess_name
                FROM academic_sessions
                WHERE sess_status = 'E'
                ORDER BY sess_id DESC
            )
            WHERE ROWNUM = 1
        ");

        return (object) [
            'sess_id' => (int) ($row->sess_id ?? $row->SESS_ID ?? 0),
            // Never blank: the page prints this as the session's name, and an empty
            // heading reads as "the page is broken" rather than "no session is set up".
            'sess_code' => trim((string) ($row->sess_code ?? $row->SESS_CODE ?? '')) ?: '—',
        ];
    }

    /**
     * Bulk scholarship posting page — review PENDING rows before challan regeneration.
     */
    public function postingIndex(Request $request)
    {
        // Fixed to the current session — there is no picker and ?session_id is
        // deliberately not honoured, so this page can only ever act on one session and
        // the figures on screen cannot belong to a different one than the button posts.
        $currentSession = $this->resolveCurrentSession();
        $currentSessionId = $currentSession->sess_id;
        $currentSessionCode = $currentSession->sess_code;

        $schemaWarning = $this->bulkSchemaWarning();
        $canLoadPending = $this->canQueueScholarshipPosts();

        return response()
            ->view('StudentFinancial.scholarshipBulkPost', compact(
                'currentSessionId',
                'currentSessionCode',
                'schemaWarning',
                'canLoadPending'
            ))
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    /**
     * List scholarship rows marked PENDING for bulk posting (Option A flag).
     */
    public function getPendingRecords(Request $request)
    {
        $sessionId = (int) $request->input('session_id');
        if (!$sessionId) {
            return response()->json([
                'status' => false,
                'message' => 'Please select a session.',
                'records' => [],
            ], 422);
        }

        if (!$this->canQueueScholarshipPosts()) {
            return response()->json([
                'status' => false,
                'message' => $this->bulkSchemaWarning(),
                'records' => [],
            ], 422);
        }

        $schoolId = $this->normalizeId($request->input('school_id'));
        $studentId = strtoupper(trim((string) $request->input('student_id', '')));

        try {
            $academic = $this->fetchPendingAcademicRows($sessionId, $schoolId, $studentId);
            $hostel = $this->fetchPendingHostelRows($sessionId, $schoolId, $studentId);
        } catch (\Throwable $e) {
            \Log::error('Pending scholarship list failed: ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Unable to load pending records.',
                'error' => config('app.debug') ? $e->getMessage() : null,
                'records' => [],
            ], 500);
        }

        $records = array_merge($academic ?: [], $hostel ?: []);
        usort($records, function ($a, $b) {
            $enA = strtoupper(trim((string) ($a->enrollment_no ?? '')));
            $enB = strtoupper(trim((string) ($b->enrollment_no ?? '')));

            return strcmp($enA, $enB);
        });

        return response()->json([
            'status' => true,
            'count' => count($records),
            'records' => $records,
            'session_id' => $sessionId,
            'schema_warning' => $this->bulkSchemaWarning(),
            'columns_ready' => $this->bulkColumnStatus(),
        ]);
    }

    /**
     * SQL predicate restricting a staged query to rows this page owns.
     *
     * Both scholarship pages write *_BULK_POST_STATUS = 'PENDING' into the same two
     * tables but post through different engines, so the queues must not mix. Applied
     * only when the column exists, otherwise the WHERE itself raises ORA-00904.
     */
    protected function bulkSourceScopeSql(string $alias, bool $hostel = false): string
    {
        $column = $hostel ? 'sshd_source_page' : 'ssd_source_page';
        $exists = $hostel ? $this->hasSshdSourcePageColumn() : $this->hasSsdSourcePageColumn();

        if (!$exists) {
            return '';
        }

        return " AND UPPER(TRIM(NVL({$alias}.{$column}, ''))) = '" . $this->sourcePage() . "'";
    }

    /**
     * Scope a session-wide read to what THIS page awarded (SCHOLARSHIP_BULK).
     *
     * The single-student page (RECORD_REVISED) was included here until 2026-08-24, on the
     * argument that its awards are posted from this screen too and leaving them out would
     * under-report. It was removed at the request of the finance team, who want the
     * checklist to name only the scholarships entered on this page.
     *
     * KNOWN CONSEQUENCE, recorded rather than argued: this screen can still post a
     * challan that carries a single-page award, and that award is no longer named on the
     * list being signed. The signature therefore describes the scholarships THIS page
     * entered, not everything the Generate button will issue. If a signed-for list ever
     * has to match the issued documents exactly, this is the line to revisit.
     *
     * NULL is excluded, and that is the whole point of the filter. An unstamped row
     * either predates the column or came from a legacy entry screen (Assign Scholarship,
     * Merit Entry, Other Scholarship), and naming those in a list captioned "the ones
     * this session should award" puts scholarships in front of the reader that nothing on
     * this page can post — the reader then either signs for work that is not theirs, or
     * refuses to sign over a name they cannot account for.
     *
     * Returns '' when the column is not deployed, so the caller falls back to every
     * scholarship in the session — exactly what it read before this filter existed.
     */
    protected function scholarshipPageSourceScopeSql(string $alias, bool $hostel = false): string
    {
        $column = $hostel ? 'sshd_source_page' : 'ssd_source_page';
        $exists = $hostel ? $this->hasSshdSourcePageColumn() : $this->hasSsdSourcePageColumn();

        if (!$exists) {
            return '';
        }

        // The literal comes from the sourcePage() accessor — a fixed string, never request
        // input — which is why it is interpolated rather than bound.
        $page = strtoupper(trim($this->sourcePage()));

        return " AND UPPER(TRIM(NVL({$alias}.{$column}, ''))) = '" . $page . "'";
    }

    /**
     * The staged snapshot for a whole session, one entry per CHALLAN
     * (= one student + one challan category), for the Bulk Post review table.
     *
     * ── SECOND COPY ──────────────────────────────────────────────────────────
     * The field mapping mirrors StudentRecordRevisedController::getStaged()
     * (around line 1412). Keep the two in step: if a line label or a total moves
     * there, it must move here, or the two pages will preview different figures.
     * ─────────────────────────────────────────────────────────────────────────
     *
     * Reads only stored snapshot columns — no fee resolution — so one pass covers
     * the whole session cheaply.
     */
    public function getStagedSummary(Request $request)
    {
        $sessionId = (int) $request->input('session_id');
        if (!$sessionId) {
            return response()->json(['status' => false, 'message' => 'Please select a session.', 'challans' => []], 422);
        }

        if (!$this->canQueueScholarshipPosts()) {
            return response()->json([
                'status' => false,
                'message' => $this->bulkSchemaWarning(),
                'challans' => [],
            ], 422);
        }

        $studentId = strtoupper(trim((string) $request->input('student_id', '')));

        try {
            $academic = $this->fetchStagedAcademicSnapshot($sessionId, $studentId);
            $hostel = $this->fetchStagedHostelSnapshot($sessionId, $studentId);
        } catch (\Throwable $e) {
            \Log::error('Staged scholarship summary failed: ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Unable to load the staged snapshot.',
                'error' => config('app.debug') ? $e->getMessage() : null,
                'challans' => [],
            ], 500);
        }

        $num = function ($row, $col) {
            $lower = $row->{$col} ?? null;
            $upper = $row->{strtoupper($col)} ?? null;

            return (float) ($lower !== null ? $lower : ($upper !== null ? $upper : 0));
        };
        $str = function ($row, $col) {
            $lower = $row->{$col} ?? null;
            $upper = $row->{strtoupper($col)} ?? null;

            return (string) ($lower !== null ? $lower : ($upper !== null ? $upper : ''));
        };

        // The row that carries the challan's own figures. Same rule, and the same reason,
        // as PostContext::head(): those figures repeat on every row of the group, but only
        // on rows this page has stamped — one written elsewhere with just a percentage
        // holds nothing, and picking it made the whole challan read as undiscounted.
        // Falls back to the first row so an unpriced group still reports itself as such.
        $pickHead = function (array $rows, string $actualColumn) use ($num) {
            foreach ($rows as $row) {
                if ($num($row, $actualColumn) > 0) {
                    return $row;
                }
            }

            return $rows[0];
        };

        $labels = $this->academicCategoryLabels();
        $challans = [];

        // ── academic: one challan per (student, challan category) ──
        $grouped = [];
        foreach ($academic as $row) {
            $enrollment = strtoupper(trim($str($row, 'enrollment_no')));
            $cat = trim($str($row, 'challan_category')) ?: 'Full_semester';
            $grouped[$enrollment . '|' . $cat][] = $row;
        }

        foreach ($grouped as $key => $rows) {
            $head = $pickHead($rows, 'ssd_total_fee_actual');
            [$enrollment, $cat] = explode('|', $key, 2);

            $lines = [];
            $map = [
                ['Admission Fee', 'ssd_admission_fee_actual', 'ssd_admission_disc_amt', 'ssd_admission_payable'],
                ['Security Fee', 'ssd_security_fee_actual', 'ssd_security_disc_amt', 'ssd_security_payable'],
                ['Tuition Fee', 'ssd_tuition_fee_actual', 'ssd_tuition_disc_amt', 'ssd_tuition_payable'],
                ['Medical Fee', 'ssd_medical_fee_actual', 'ssd_medical_disc_amt', 'ssd_medical_payable'],
                ['Activity Charges', 'ssd_activity_fee_actual', 'ssd_activity_disc_amt', 'ssd_activity_payable'],
            ];
            foreach ($map as $m) {
                $actual = $num($head, $m[1]);
                if ($actual <= 0) {
                    continue;
                }
                $lines[] = [
                    'label' => $m[0],
                    'actual' => $actual,
                    'discount' => $num($head, $m[2]),
                    'payable' => $num($head, $m[3]),
                ];
            }

            $scholarships = [];
            $recordIds = [];
            // Every row's own share, summed. This is what the awards ACTUALLY come to, as
            // against the challan-level total stamped on the head row — the page shows the
            // awards from this list, so a stale head row can no longer hide them.
            $scholarshipsTotal = 0.0;
            $tuitionActual = $num($head, 'ssd_tuition_fee_actual');
            foreach ($rows as $row) {
                $recordIds[] = (int) $num($row, 'record_id');

                // PERCENTAGE decides whether an award exists, not the stored amount.
                // SSD_AMOUNT is only filled in by a Save on this page; a row created by
                // the merit-entry or approval flows carries a percentage and nothing else,
                // and testing the amount silently dropped it — a student holding Merit
                // Base 50% alongside a removed Need Based showed no scholarship at all.
                $pct = $num($row, 'ssd_percentage');
                if ($pct <= 0) {
                    continue;
                }

                // Derive from the challan's own tuition base when the amount was never
                // written. Same arithmetic refreshScholarshipSnapshot() uses, so a re-save
                // produces the identical figure.
                $amount = $num($row, 'ssd_amount');
                if ($amount <= 0 && $tuitionActual > 0) {
                    $amount = round(($pct / 100) * $tuitionActual, 2);
                }

                $scholarshipsTotal += $amount;
                $scholarships[] = [
                    'name' => $str($row, 'scholarship_name') ?: ('Scholarship #' . $str($row, 'ssd_type')),
                    'pct' => $pct,
                    'amount' => $amount,
                ];
            }

            // The snapshot caps the discount at the fee; match it, or a student on 100%
            // plus a second award reads as permanently inconsistent. Kept in step with
            // stagedTotalsAgree() in PostsScholarshipSnapshots — the grid must not offer
            // Accept on something Post will refuse, nor withhold it on something Post
            // would take.
            if ($tuitionActual > 0) {
                $scholarshipsTotal = min($scholarshipsTotal, $tuitionActual);
            }

            $totalPayable = $num($head, 'ssd_total_payable');
            $surplusConsumed = $num($head, 'ssd_surplus_consumed');
            $totalDisc = $num($head, 'ssd_total_disc_amt');
            // Kept apart because they answer different questions. The tuition discount is
            // the one the percentages describe and the only one snapshot_stale can check;
            // the one-time discount is an absolute waiver out of FIN_OTHER_SCHOLARSHIPS
            // that no percentage predicts.
            $tuitionDisc = $num($head, 'ssd_tuition_disc_amt');
            $onetimeDisc = $num($head, 'ssd_onetime_disc_amt');

            $challans[] = [
                'key' => $enrollment . '|' . $cat,
                'enrollment_no' => $enrollment,
                'student_name' => $str($head, 'student_name'),
                'school_name' => $str($head, 'school_name'),
                'aai_id' => (int) $num($head, 'app_id'),
                'category' => $cat,
                'title' => ($labels[$cat] ?? $cat) . ' Challan',
                'lines' => $lines,
                'scholarships' => $scholarships,
                // Tuition and the one-time fees are kept apart on purpose: the
                // scholarship percentage applies to TUITION ONLY, so showing a single
                // combined fee makes the discount look like it does not add up.
                'tuition_actual' => $num($head, 'ssd_tuition_fee_actual'),
                'onetime_actual' => $num($head, 'ssd_onetime_fee_actual'),
                'total_actual' => $num($head, 'ssd_total_fee_actual'),
                'total_disc' => $totalDisc,
                // The tuition share of total_disc, and the one-time (arrears) share. The
                // grid shows them in separate columns: the percentages the scholarship
                // names carry explain the first and say nothing about the second.
                'tuition_disc' => $tuitionDisc,
                'onetime_disc' => $onetimeDisc,
                'scholarships_total' => round($scholarshipsTotal, 2),
                // The stored challan total and the awards it is supposed to describe do
                // not agree, so posting would issue the wrong discount. Post refuses this
                // outright (stagedTotalsAgree), so the grid says so and withholds Accept
                // rather than letting the user reach that refusal at the end of a batch.
                // Suppressed when unpriced — that is already blocked, and two errors on
                // one row explain less than one.
                //
                // Compared against the TUITION discount, not the total: the awards summed
                // above are percentages of tuition, and total_disc also carries the
                // one-time waiver, which no percentage here predicts. Comparing against
                // the total would mark every arrears student permanently stale.
                //
                // The second clause mirrors stagedTotalsAgree()'s: the posters write
                // SC_SCHOLARSHIP_AMOUNT straight from total_disc, so a head row whose
                // total is not tuition + one-time is refused at Post. Without it here the
                // grid would offer Accept on exactly that row.
                'snapshot_stale' => $tuitionActual > 0
                    && (abs($scholarshipsTotal - $tuitionDisc) > 0.5
                        || abs(($tuitionDisc + $onetimeDisc) - $totalDisc) > 0.5),
                'total_payable' => $totalPayable,
                'surplus_consumed' => $surplusConsumed,
                // Frozen at the last Save (refreshScholarshipSnapshot()) — compared below
                // against the LIVE fin_fee_adjustment balance to flag surplus_stale.
                'surplus_pending_frozen' => $num($head, 'ssd_surplus_pending'),
                // A zero invoice is MEANINGFUL — surplus covering the challan in full —
                // so only fall back when no surplus was consumed at all.
                'invoice_amount' => ($num($head, 'ssd_invoice_amount') > 0 || $surplusConsumed > 0)
                    ? $num($head, 'ssd_invoice_amount')
                    : $totalPayable,
                'record_ids' => $recordIds,
                // Nothing was priced — the fee structure did not resolve at Save. Such a
                // challan must not be acceptable; posting it would write a zero document.
                'priced' => $num($head, 'ssd_total_fee_actual') > 0,
            ];
        }

        // ── hostel: one challan per student ──
        $groupedHostel = [];
        foreach ($hostel as $row) {
            $enrollment = strtoupper(trim($str($row, 'enrollment_no')));
            $groupedHostel[$enrollment][] = $row;
        }

        foreach ($groupedHostel as $enrollment => $rows) {
            $head = $pickHead($rows, 'sshd_total_fee_actual');
            $actual = $num($head, 'sshd_hostel_fee_actual') ?: $num($head, 'sshd_hostel_fee');

            $scholarships = [];
            $recordIds = [];
            $scholarshipsTotal = 0.0;
            foreach ($rows as $row) {
                $recordIds[] = (int) $num($row, 'record_id');

                // Percentage decides, and the amount is derived when missing — same rule
                // and same reason as the academic loop above.
                $pct = $num($row, 'sshd_percentage');
                if ($pct <= 0) {
                    continue;
                }

                $amount = $num($row, 'sshd_amount');
                if ($amount <= 0 && $actual > 0) {
                    $amount = round(($pct / 100) * $actual, 2);
                }

                $scholarshipsTotal += $amount;
                $scholarships[] = [
                    'name' => $str($row, 'scholarship_name') ?: ('Scholarship #' . $str($row, 'sshd_type')),
                    'pct' => $pct,
                    'amount' => $amount,
                ];
            }

            if ($actual > 0) {
                $scholarshipsTotal = min($scholarshipsTotal, $actual);
            }

            $totalPayable = $num($head, 'sshd_total_payable');
            $surplusConsumed = $num($head, 'sshd_surplus_consumed');
            $totalDisc = $num($head, 'sshd_total_disc_amt');

            $challans[] = [
                'key' => $enrollment . '|Hostel_Fee',
                'enrollment_no' => $enrollment,
                'student_name' => $str($head, 'student_name'),
                'school_name' => $str($head, 'school_name'),
                'aai_id' => (int) $num($head, 'app_id'),
                'category' => 'Hostel_Fee',
                'title' => 'Hostel Challan',
                'lines' => [[
                    'label' => 'Hostel Fee',
                    'actual' => $actual,
                    'discount' => $num($head, 'sshd_hostel_disc_amt'),
                    'payable' => $num($head, 'sshd_hostel_payable'),
                ]],
                'scholarships' => $scholarships,
                // A hostel challan has no one-time components — the percentage applies
                // to the whole hostel fee.
                'tuition_actual' => $actual,
                'onetime_actual' => 0,
                'total_actual' => $num($head, 'sshd_total_fee_actual') ?: $actual,
                'total_disc' => $totalDisc,
                // No tuition/one-time split to make here, but the grid reads these on every
                // challan — omitting them would leave the hostel rows' Scholarship column
                // reading zero. The hostel fee IS the tuition analogue, and its arrears
                // (SSHD_ARREAR) are not part of this page.
                'tuition_disc' => $totalDisc,
                'onetime_disc' => 0,
                'scholarships_total' => round($scholarshipsTotal, 2),
                'snapshot_stale' => $actual > 0 && abs($scholarshipsTotal - $totalDisc) > 0.5,
                'total_payable' => $totalPayable,
                'surplus_consumed' => $surplusConsumed,
                'surplus_pending_frozen' => $num($head, 'sshd_surplus_pending'),
                'invoice_amount' => ($num($head, 'sshd_invoice_amount') > 0 || $surplusConsumed > 0)
                    ? $num($head, 'sshd_invoice_amount')
                    : $totalPayable,
                'record_ids' => $recordIds,
                'priced' => $actual > 0,
            ];
        }

        // ── surplus drift: does each student's frozen SSD_SURPLUS_PENDING (last Save)
        // still match what fin_fee_adjustment actually holds right now? ──
        //
        // Advisory only — this does NOT gate Post on its own. postAcceptedChallansForStudent()
        // already has the authoritative freeze check and refuses to post a drifted student
        // regardless of what this flag says. This exists so the grader sees the drift on the
        // grid, before Accept, and can press "Re-sync Surplus" instead of only discovering the
        // skip after Post runs — see resyncSurplusPending().
        //
        // Cheap probe first, scoped to app_ids actually on this page: most staged students
        // never touch surplus at all, and SurplusAdjuster::pendingSurplus() is a per-student
        // query, not a batched one, so this keeps the N+1 bounded to students who could
        // plausibly be drifted (a live balance now, or a nonzero frozen figure from before)
        // rather than every staged row on the grid.
        $sessionAaiIds = array_values(array_unique(array_filter(array_column($challans, 'aai_id'))));
        $liveSurplusAppIds = [];
        if (!empty($sessionAaiIds)) {
            try {
                $liveSurplusAppIds = array_flip(array_map('intval', DB::table('fin_fee_adjustment')
                    ->whereIn('ffa_app_id', $sessionAaiIds)
                    ->whereRaw('NVL(ffa_surplus_amount, 0) > 0')
                    ->distinct()
                    ->pluck('ffa_app_id')
                    ->all()));
            } catch (\Throwable $e) {
                // Advisory check — a failed probe just skips the highlight, it must never
                // block the staged list from loading.
                \Log::warning('Surplus drift probe failed: ' . $e->getMessage());
            }
        }

        $adjuster = new SurplusAdjuster();
        $liveSurplusCache = [];
        $liveSurplus = function ($aaiId, $enrollment) use ($adjuster, &$liveSurplusCache) {
            $cacheKey = $aaiId . '|' . $enrollment;
            if (!array_key_exists($cacheKey, $liveSurplusCache)) {
                $liveSurplusCache[$cacheKey] = $adjuster->pendingSurplus($aaiId, $enrollment ?: null);
            }

            return $liveSurplusCache[$cacheKey];
        };

        foreach ($challans as &$c) {
            $aaiId = (int) $c['aai_id'];
            $frozen = round((float) $c['surplus_pending_frozen'], 2);
            $c['surplus_stale'] = false;

            if (!$aaiId || (!$frozen && !isset($liveSurplusAppIds[$aaiId]))) {
                continue;
            }

            try {
                $live = round((float) $liveSurplus($aaiId, $c['enrollment_no']), 2);
            } catch (\Throwable $e) {
                \Log::warning('Surplus drift check failed for ' . $c['enrollment_no'] . ': ' . $e->getMessage());
                continue;
            }

            $c['surplus_pending_live'] = $live;
            $c['surplus_stale'] = abs($frozen - $live) > 0.005;
        }
        unset($c);

        // Whether each challan's preview will find installment dates. Stamped before
        // the sort so the grouping below sees every row exactly once.
        $challans = $this->attachInstallmentDates($challans, (int) $sessionId);

        usort($challans, function ($a, $b) {
            $cmp = strcmp($a['enrollment_no'], $b['enrollment_no']);

            return $cmp !== 0 ? $cmp : strcmp($a['category'], $b['category']);
        });

        $unpriced = count(array_filter($challans, function ($c) {
            return !$c['priced'];
        }));

        return response()->json(array_merge([
            'status' => true,
            'session_id' => $sessionId,
            'challans' => $challans,
            'count' => count($challans),
            'unpriced' => $unpriced,
            'staff_url' => rtrim((string) config('app.staff_url'), '/'),
            'schema_warning' => $this->bulkSchemaWarning(),
            // The Generate gate reads these. Sent whole rather than recomputed per
            // click: the selection is a subset, so the page filters these groups
            // down client-side rather than asking again.
            'installment_groups' => $this->groupMissingInstallmentDates($challans),
            'installment_fallback_date' => $this->previewFallbackDate(),
        // Carried on the load the page already does, so the Generate gate is correct
        // from the first paint rather than after a second round trip.
        ], $this->scholarshipListAckState($sessionId)));
    }

    /**
     * The distinct scholarship TYPES in play this session, and whether the list has been
     * signed off.
     *
     * Deliberately a list of NAMES, not of students: the question this answers is "what
     * kinds of award is this session about to issue", which is a short list a human can
     * actually read, where the per-student view runs to hundreds of rows. The counts are
     * there so a name that covers one student cannot be mistaken for one that covers two
     * hundred.
     *
     * Every active row of the session is counted, not only the staged ones — a scholarship
     * that was posted earlier in the session is still part of what this session awards, and
     * leaving it out would make the list read as complete when it was not.
     */
    public function getSessionScholarshipList(Request $request)
    {
        // Resolved here rather than taken from the request: the page is pinned to one
        // session, and a list that could be asked for a different session than the one
        // the Generate button acts on is a signature waiting to be applied to the wrong
        // thing.
        $session = $this->resolveCurrentSession();
        $sessionId = $session->sess_id;

        if ($sessionId <= 0) {
            return response()->json([
                'status' => false,
                'message' => 'No current session is configured.',
                'scholarships' => [],
            ], 422);
        }

        try {
            $rows = array_merge(
                $this->fetchSessionScholarshipNames($sessionId, false),
                $this->fetchSessionScholarshipNames($sessionId, true)
            );
        } catch (\Throwable $e) {
            \Log::error('Session scholarship list failed: ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Unable to read the session scholarship list.',
                'error' => config('app.debug') ? $e->getMessage() : null,
                'scholarships' => [],
            ], 500);
        }

        $num = function ($row, $col) {
            $v = $row->{$col} ?? $row->{strtoupper($col)} ?? 0;

            return (int) $v;
        };
        $str = function ($row, $col) {
            $v = $row->{$col} ?? $row->{strtoupper($col)} ?? '';

            return trim((string) $v);
        };

        $scholarships = [];
        $seen = [];
        foreach ($rows as $row) {
            $category = $str($row, 'category');
            $id = $num($row, 'scholarship_id');

            // Academic and hostel are read separately and a type could in principle be
            // used by both, so dedupe on the pair rather than trusting DISTINCT per query.
            $key = $category . '|' . $id;
            if (isset($seen[$key])) {
                continue;
            }
            $seen[$key] = true;

            $scholarships[] = [
                'scholarship_id' => $id,
                'name' => $str($row, 'scholarship_name') ?: ('Scholarship #' . $id),
                'category' => $category,
            ];
        }

        // Name order, hostel after academic — the modal groups by category and reads
        // top to bottom, so the sort has to agree with the grouping.
        usort($scholarships, function ($a, $b) {
            if ($a['category'] !== $b['category']) {
                return $a['category'] === 'academic' ? -1 : 1;
            }

            return strcasecmp($a['name'], $b['name']);
        });

        return response()->json(array_merge([
            'status' => true,
            'session_id' => $sessionId,
            'session_code' => $session->sess_code,
            'scholarships' => $scholarships,
            'count' => count($scholarships),
            // Whether the source-page filter actually ran. Both columns, because the
            // caption the client writes from this covers the whole list, and a
            // half-deployed schema would leave one section unfiltered while the wording
            // claimed otherwise.
            'source_scoped' => $this->hasSsdSourcePageColumn() && $this->hasSshdSourcePageColumn(),
        ], $this->scholarshipListAckState($sessionId)));
    }

    /**
     * One category's distinct scholarship NAMES for a session.
     *
     * Names only, deliberately. This list exists to be read and signed, and a figure
     * beside each name invites the reader to check arithmetic instead of checking the
     * names — which is the one thing the per-student table behind it already does well.
     *
     * Restricted to scholarships entered on THIS page — see
     * scholarshipPageSourceScopeSql(), which also records what that restriction costs.
     * A scholarship the legacy screens granted is a real award, but it is not one this
     * page can post, so it does not belong on a checklist that gates this page's
     * Generate button.
     *
     * @param bool $hostel which table to read
     * @return array<int, object>
     */
    protected function fetchSessionScholarshipNames(int $sessionId, bool $hostel): array
    {
        if ($hostel) {
            return DB::select("
                SELECT DISTINCT
                    TO_NUMBER(st.scholarship_id) AS scholarship_id,
                    TO_CHAR(NVL(st.scholarship_name, 'Hostel Scholarship')) AS scholarship_name,
                    CAST('hostel' AS VARCHAR2(20)) AS category
                FROM fin_students_scholarships_hostel_details sshd
                LEFT JOIN scholarship_type st ON st.scholarship_id = sshd.sshd_type
                WHERE NVL(TRIM(sshd.sshd_status), 'E') = 'E'
                  AND sshd.sshd_sess_id = :session_id
                  -- A zeroed row is a REMOVED scholarship. It is still on the table so
                  -- the challan can be re-issued without it, but naming it in a list of
                  -- what this session awards would be the opposite of the truth.
                  AND NVL(sshd.sshd_percentage, 0) > 0
                  {$this->scholarshipPageSourceScopeSql('sshd', true)}
                ORDER BY 2
            ", ['session_id' => $sessionId]);
        }

        return DB::select("
            SELECT DISTINCT
                TO_NUMBER(st.scholarship_id) AS scholarship_id,
                TO_CHAR(st.scholarship_name) AS scholarship_name,
                CAST('academic' AS VARCHAR2(20)) AS category
            FROM students_scholarships_details ssd
            JOIN scholarship_type st ON st.scholarship_id = ssd.ssd_type
            WHERE NVL(ssd.status, 'E') = 'E'
              AND ssd.ssd_sess_id = :session_id
              AND NVL(ssd.ssd_percentage, 0) > 0
              {$this->scholarshipPageSourceScopeSql('ssd')}
            ORDER BY 2
        ", ['session_id' => $sessionId]);
    }

    /**
     * Has the scholarship list been signed off for what is currently staged?
     *
     * The signature is stamped on the staged ROWS, which makes it self-invalidating: a
     * scholarship saved from the Bulk Update form after the sign-off arrives PENDING with
     * a NULL stamp, so the list stops being acknowledged the moment it stops being the
     * list that was read. Nothing has to watch for that or expire anything on a timer.
     *
     * With nothing staged there is nothing to generate, so the gate is reported as
     * satisfied rather than blocking a page that has no work on it.
     *
     * @return array{ack_supported:bool, acknowledged:bool, ack_by:?string, ack_at:?string,
     *               staged_rows:int, unacknowledged_rows:int}
     */
    protected function scholarshipListAckState(int $sessionId): array
    {
        if (!$this->hasListAckColumns() || !$this->canQueueScholarshipPosts()) {
            // Column not deployed — the page must keep working, so the gate is open and
            // the client is told why it is not being asked to sign anything.
            return [
                'ack_supported' => false,
                'acknowledged' => true,
                'ack_by' => null,
                'ack_at' => null,
                'ack_remarks' => null,
                'remarks_supported' => false,
                'staged_rows' => 0,
                'unacknowledged_rows' => 0,
            ];
        }

        $hasRemarks = $this->hasListAckRemarksColumns();
        $academicRemarks = $hasRemarks
            ? 'MAX(ssd_list_ack_remarks) AS ack_remarks'
            : 'CAST(NULL AS VARCHAR2(1)) AS ack_remarks';
        $hostelRemarks = $hasRemarks
            ? 'MAX(sshd_list_ack_remarks) AS ack_remarks'
            : 'CAST(NULL AS VARCHAR2(1)) AS ack_remarks';

        // `acknowledged` is decided by `unacked`, never by ack_by/ack_at — those two are
        // for the "Acknowledged by X on Y" line and nothing else. Plain MAX() on both:
        // in the ordinary case one person signs every row in one click and they agree,
        // and if two people signed different rows the name shown may not be the one who
        // set the latest timestamp. That is a caption being imprecise, which is worth far
        // less than the risk of a cleverer aggregate failing and taking the whole staged
        // summary down with it — this runs inside getStagedSummary().
        $academic = DB::selectOne("
            SELECT
                COUNT(*) AS staged_rows,
                SUM(CASE WHEN ssd_list_ack_at IS NULL THEN 1 ELSE 0 END) AS unacked,
                MAX(ssd_list_ack_at) AS ack_at,
                MAX(ssd_list_ack_by) AS ack_by,
                {$academicRemarks}
            FROM students_scholarships_details ssd
            WHERE NVL(status, 'E') = 'E'
              AND ssd_sess_id = :session_id
              AND UPPER(TRIM(NVL(ssd_bulk_post_status, ''))) = 'PENDING'
              {$this->bulkSourceScopeSql('ssd')}
        ", ['session_id' => $sessionId]);

        $hostel = DB::selectOne("
            SELECT
                COUNT(*) AS staged_rows,
                SUM(CASE WHEN sshd_list_ack_at IS NULL THEN 1 ELSE 0 END) AS unacked,
                MAX(sshd_list_ack_at) AS ack_at,
                MAX(sshd_list_ack_by) AS ack_by,
                {$hostelRemarks}
            FROM fin_students_scholarships_hostel_details sshd
            WHERE NVL(TRIM(sshd_status), 'E') = 'E'
              AND sshd_sess_id = :session_id
              AND UPPER(TRIM(NVL(sshd_bulk_post_status, ''))) = 'PENDING'
              {$this->bulkSourceScopeSql('sshd', true)}
        ", ['session_id' => $sessionId]);

        $read = function ($row, $col) {
            return $row->{$col} ?? $row->{strtoupper($col)} ?? null;
        };

        $staged = (int) $read($academic, 'staged_rows') + (int) $read($hostel, 'staged_rows');
        $unacked = (int) $read($academic, 'unacked') + (int) $read($hostel, 'unacked');

        $ackAt = $read($academic, 'ack_at') ?: $read($hostel, 'ack_at');
        $ackBy = $read($academic, 'ack_by') ?: $read($hostel, 'ack_by');
        $remarks = $read($academic, 'ack_remarks') ?: $read($hostel, 'ack_remarks');

        return [
            'ack_supported' => true,
            'acknowledged' => $staged === 0 || $unacked === 0,
            'ack_by' => $ackBy ? trim((string) $ackBy) : null,
            'ack_at' => $ackAt ? (string) $ackAt : null,
            // Blank and absent are the same thing to the caller: there is no note to show.
            'ack_remarks' => ($remarks !== null && trim((string) $remarks) !== '')
                ? trim((string) $remarks)
                : null,
            'remarks_supported' => $hasRemarks,
            'staged_rows' => $staged,
            'unacknowledged_rows' => $unacked,
        ];
    }

    /**
     * Record that this user read the session's scholarship list.
     *
     * Stamped on every row this page currently has staged — see scholarshipListAckState()
     * for why the signature lives on the rows rather than in a column of its own.
     */
    public function acknowledgeScholarshipList(Request $request)
    {
        $session = $this->resolveCurrentSession();
        $sessionId = $session->sess_id;

        if ($sessionId <= 0) {
            return response()->json(['status' => false, 'message' => 'No current session is configured.'], 422);
        }

        if (!$this->hasListAckColumns()) {
            return response()->json([
                'status' => false,
                'message' => 'The acknowledgement columns are not on this database yet. Run '
                    . 'scripts/add_scholarship_list_ack_columns.sql and confirm they are listed in '
                    . 'ScholarshipFormController::$allowedSchemaColumns, then try again.',
            ], 422);
        }

        // How many names the client had on screen. Echoed back in the confirmation so the
        // operator is told what they just signed for; NOT stored, and nothing is decided
        // from it — the authority is the row stamps written below.
        $seenCount = (int) $request->input('list_count', 0);
        $username = auth()->user()->username ?? 'system';
        $now = now()->toDateTimeString();

        // REQUIRED, on every signing — not just the first. A list goes unsigned again the
        // moment a scholarship is saved from the Bulk Update form, and the note that
        // covered the previous list says nothing about the new one.
        //
        // Stood down only where there is no column to write it to: on an install without
        // the remarks column the client renders no box, and demanding a note that has
        // nowhere to go would make the checklist impossible to sign and Generate
        // impossible to reach. Same reasoning as hasListAckRemarksColumns() itself.
        $remarks = trim((string) $request->input('remarks', ''));
        if ($this->hasListAckRemarksColumns() && $remarks === '') {
            return response()->json([
                'status' => false,
                'message' => 'A remark is required every time the scholarship checklist is signed. '
                    . 'Note what you checked, or anything you are relying on.',
            ], 422);
        }

        // Trimmed to the column's own limit rather than rejected: a note that ran long is
        // still worth keeping, and refusing the whole sign-off over its length would be a
        // worse outcome than storing the first 500 characters of it. mb_substr, not
        // substr — cutting a UTF-8 sequence in half is how you get an invalid string into
        // a column that then breaks every later read of the row.
        $remarksTruncated = mb_strlen($remarks) > self::LIST_ACK_REMARKS_MAX;
        if ($remarksTruncated) {
            $remarks = mb_substr($remarks, 0, self::LIST_ACK_REMARKS_MAX);
        }

        try {
            DB::beginTransaction();

            // Scoped to THIS page's queue, strictly — the same rule bulkSourceScopeSql()
            // applies, and therefore the same set of rows scholarshipListAckState()
            // counts and the grid displays. Deliberately NOT the looser
            // `IN ('', SCHOLARSHIP_BULK)` requeueChallanCategory() uses: a signature must
            // cover exactly what was on the page, so what is stamped and what is checked
            // have to be one set. Applied only when the column exists, or the WHERE
            // itself raises ORA-00904 on an install that is behind.
            $ownedHere = function ($query, string $column, bool $exists) {
                if ($exists) {
                    $query->whereRaw(
                        "UPPER(TRIM(NVL({$column}, ''))) = ?",
                        [strtoupper($this->sourcePage())]
                    );
                }

                return $query;
            };

            // filterExistingColumns() drops the remarks column on an install that has the
            // signature columns but not the note one, so signing still works there. '' is
            // written as NULL: an empty string and "no remark" are the same fact, and only
            // one of them should be able to appear in the data. Reachable only on that
            // install — where the column exists, the guard above has already refused an
            // empty remark.
            $academicSet = $this->filterExistingColumns('students_scholarships_details', [
                'ssd_list_ack_by' => $username,
                'ssd_list_ack_at' => $now,
                'ssd_list_ack_remarks' => $remarks !== '' ? $remarks : null,
            ]);
            $hostelSet = $this->filterExistingColumns('fin_students_scholarships_hostel_details', [
                'sshd_list_ack_by' => $username,
                'sshd_list_ack_at' => $now,
                'sshd_list_ack_remarks' => $remarks !== '' ? $remarks : null,
            ]);

            $academicQ = DB::table('students_scholarships_details')
                ->whereRaw("NVL(status, 'E') = 'E'")
                ->where('ssd_sess_id', $sessionId)
                ->whereRaw("UPPER(TRIM(NVL(ssd_bulk_post_status, ''))) = 'PENDING'");
            $ownedHere($academicQ, 'ssd_source_page', $this->hasSsdSourcePageColumn());
            $academic = $academicQ->update($academicSet);

            $hostelQ = DB::table('fin_students_scholarships_hostel_details')
                ->whereRaw("NVL(TRIM(sshd_status), 'E') = 'E'")
                ->where('sshd_sess_id', $sessionId)
                ->whereRaw("UPPER(TRIM(NVL(sshd_bulk_post_status, ''))) = 'PENDING'");
            $ownedHere($hostelQ, 'sshd_source_page', $this->hasSshdSourcePageColumn());
            $hostel = $hostelQ->update($hostelSet);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Scholarship list acknowledgement failed: ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => $this->humanDbFailure($e, 'Could not record the acknowledgement. Please try again.'),
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }

        return response()->json(array_merge([
            'status' => true,
            'message' => 'Scholarship checklist acknowledged for ' . $session->sess_code
                . ' — ' . ($academic + $hostel) . ' staged row(s) signed off'
                . ($seenCount ? ' against ' . $seenCount . ' scholarship name(s).' : '.')
                . ($remarksTruncated
                    ? ' Your remark was longer than ' . self::LIST_ACK_REMARKS_MAX
                        . ' characters and was stored truncated.'
                    : ''),
            'stamped' => $academic + $hostel,
            'remarks_truncated' => $remarksTruncated,
        ], $this->scholarshipListAckState($sessionId)));
    }

    protected function fetchStagedAcademicSnapshot(int $sessionId, string $studentId): array
    {
        if (!$this->hasSsdBulkPostStatusColumn()) {
            return [];
        }

        $bindings = ['session_id' => $sessionId];
        $studentFilter = '';
        if ($studentId !== '') {
            $studentFilter = ' AND UPPER(TRIM(NVL(aai.cms_sai_enrollment_no, ssd.ssd_sai_enrollment_no))) LIKE :student_id';
            $bindings['student_id'] = '%' . $studentId . '%';
        }
        // Scopes the PENDING trigger row, not the rows returned — see the EXISTS below.
        $pendingSourceScope = $this->bulkSourceScopeSql('p');

        return DB::select("
            SELECT
                TO_CHAR(ssd.ssd_id) AS record_id,
                UPPER(TRIM(NVL(aai.cms_sai_enrollment_no, ssd.ssd_sai_enrollment_no))) AS enrollment_no,
                TO_CHAR(NVL(api.api_name, spi.spi_name)) AS student_name,
                TO_CHAR(NVL(s.school_code, s.school_name)) AS school_name,
                TO_CHAR(ssd.ssd_app_id) AS app_id,
                TO_CHAR(ssd.ssd_type) AS ssd_type,
                TO_CHAR(st.scholarship_name) AS scholarship_name,
                TO_NUMBER(NVL(ssd.ssd_percentage, 0)) AS ssd_percentage,
                TO_NUMBER(NVL(ssd.ssd_amount, 0)) AS ssd_amount,
                TO_CHAR(NVL(ssd.ssd_challan_category, 'Full_semester')) AS challan_category,
                TO_NUMBER(NVL(ssd.ssd_admission_fee_actual, 0)) AS ssd_admission_fee_actual,
                TO_NUMBER(NVL(ssd.ssd_admission_disc_amt, 0)) AS ssd_admission_disc_amt,
                TO_NUMBER(NVL(ssd.ssd_admission_payable, 0)) AS ssd_admission_payable,
                TO_NUMBER(NVL(ssd.ssd_security_fee_actual, 0)) AS ssd_security_fee_actual,
                TO_NUMBER(NVL(ssd.ssd_security_disc_amt, 0)) AS ssd_security_disc_amt,
                TO_NUMBER(NVL(ssd.ssd_security_payable, 0)) AS ssd_security_payable,
                TO_NUMBER(NVL(ssd.ssd_tuition_fee_actual, 0)) AS ssd_tuition_fee_actual,
                TO_NUMBER(NVL(ssd.ssd_tuition_disc_amt, 0)) AS ssd_tuition_disc_amt,
                TO_NUMBER(NVL(ssd.ssd_tuition_payable, 0)) AS ssd_tuition_payable,
                TO_NUMBER(NVL(ssd.ssd_medical_fee_actual, 0)) AS ssd_medical_fee_actual,
                TO_NUMBER(NVL(ssd.ssd_medical_disc_amt, 0)) AS ssd_medical_disc_amt,
                TO_NUMBER(NVL(ssd.ssd_medical_payable, 0)) AS ssd_medical_payable,
                TO_NUMBER(NVL(ssd.ssd_activity_fee_actual, 0)) AS ssd_activity_fee_actual,
                TO_NUMBER(NVL(ssd.ssd_activity_disc_amt, 0)) AS ssd_activity_disc_amt,
                TO_NUMBER(NVL(ssd.ssd_activity_payable, 0)) AS ssd_activity_payable,
                TO_NUMBER(NVL(ssd.ssd_onetime_fee_actual, 0)) AS ssd_onetime_fee_actual,
                TO_NUMBER(NVL(ssd.ssd_onetime_disc_amt, 0)) AS ssd_onetime_disc_amt,
                TO_NUMBER(NVL(ssd.ssd_onetime_payable, 0)) AS ssd_onetime_payable,
                TO_NUMBER(NVL(ssd.ssd_total_fee_actual, 0)) AS ssd_total_fee_actual,
                TO_NUMBER(NVL(ssd.ssd_total_disc_amt, 0)) AS ssd_total_disc_amt,
                TO_NUMBER(NVL(ssd.ssd_total_payable, 0)) AS ssd_total_payable,
                TO_NUMBER(NVL(ssd.ssd_surplus_consumed, 0)) AS ssd_surplus_consumed,
                TO_NUMBER(NVL(ssd.ssd_surplus_pending, 0)) AS ssd_surplus_pending,
                TO_NUMBER(NVL(ssd.ssd_invoice_amount, 0)) AS ssd_invoice_amount
            FROM students_scholarships_details ssd
            LEFT JOIN scholarship_type st ON st.scholarship_id = ssd.ssd_type
            LEFT JOIN admission_apply_info aai ON aai.aai_id = ssd.ssd_app_id
            LEFT JOIN admission_personal_info api ON api.api_id = aai.aai_api_id
            LEFT JOIN students_apply_info sai
                ON UPPER(TRIM(sai.sai_enrollment_no)) = UPPER(TRIM(NVL(aai.cms_sai_enrollment_no, ssd.ssd_sai_enrollment_no)))
            LEFT JOIN students_personal_info spi ON spi.spi_id = sai.sai_spi_id
            LEFT JOIN schools s ON s.school_id = NVL(aai.aai_sdpo_school_id, sai.sai_sdpo_school_id)
            WHERE NVL(ssd.status, 'E') = 'E'
              AND ssd.ssd_sess_id = :session_id
              -- Every active row of a challan that has at least one row staged by this
              -- page — not just the staged rows. A challan's discount is the sum of all
              -- its scholarships, so listing only the re-saved one showed a payable with
              -- no scholarship lines behind it.
              AND EXISTS (
                    SELECT 1
                    FROM students_scholarships_details p
                    LEFT JOIN admission_apply_info paai ON paai.aai_id = p.ssd_app_id
                    WHERE p.ssd_sess_id = ssd.ssd_sess_id
                      AND NVL(p.status, 'E') = 'E'
                      AND NVL(p.ssd_challan_category, 'Full_semester')
                          = NVL(ssd.ssd_challan_category, 'Full_semester')
                      AND UPPER(TRIM(NVL(p.ssd_bulk_post_status, ''))) = 'PENDING'
                      {$pendingSourceScope}
                      -- Identity has to be resolved the SAME way the grouping key above
                      -- resolves it (NVL(aai.cms_sai_enrollment_no, ssd_sai_enrollment_no)).
                      -- Comparing the raw columns matched two rows only when they happened
                      -- to fill the same one: a row carrying just an app_id never joined a
                      -- staged row carrying just an enrolment, so a student's second
                      -- scholarship was left out of its own challan and the challan showed
                      -- a discount of zero.
                      AND (
                            (ssd.ssd_app_id IS NOT NULL AND p.ssd_app_id = ssd.ssd_app_id)
                         OR UPPER(TRIM(NVL(paai.cms_sai_enrollment_no, p.ssd_sai_enrollment_no)))
                            = UPPER(TRIM(NVL(aai.cms_sai_enrollment_no, ssd.ssd_sai_enrollment_no)))
                      )
              )
              {$studentFilter}
            ORDER BY enrollment_no, challan_category, ssd.ssd_id
        ", $bindings);
    }

    protected function fetchStagedHostelSnapshot(int $sessionId, string $studentId): array
    {
        if (!$this->hasSshdBulkPostStatusColumn()) {
            return [];
        }

        $bindings = ['session_id' => $sessionId];
        $studentFilter = '';
        if ($studentId !== '') {
            $studentFilter = ' AND UPPER(TRIM(NVL(sshd.sshd_sai_enrollment_no, aai.cms_sai_enrollment_no))) LIKE :student_id';
            $bindings['student_id'] = '%' . $studentId . '%';
        }
        $pendingSourceScope = $this->bulkSourceScopeSql('p', true);

        return DB::select("
            SELECT
                TO_CHAR(sshd.sshd_id) AS record_id,
                UPPER(TRIM(NVL(sshd.sshd_sai_enrollment_no, aai.cms_sai_enrollment_no))) AS enrollment_no,
                TO_CHAR(NVL(api.api_name, spi.spi_name)) AS student_name,
                TO_CHAR(NVL(s.school_code, s.school_name)) AS school_name,
                TO_CHAR(sshd.sshd_app_id) AS app_id,
                TO_CHAR(sshd.sshd_type) AS sshd_type,
                TO_CHAR(NVL(st.scholarship_name, 'Hostel Scholarship')) AS scholarship_name,
                TO_NUMBER(NVL(sshd.sshd_percentage, 0)) AS sshd_percentage,
                TO_NUMBER(NVL(sshd.sshd_amount, 0)) AS sshd_amount,
                TO_NUMBER(NVL(sshd.sshd_hostel_fee, 0)) AS sshd_hostel_fee,
                TO_NUMBER(NVL(sshd.sshd_hostel_fee_actual, 0)) AS sshd_hostel_fee_actual,
                TO_NUMBER(NVL(sshd.sshd_hostel_disc_amt, 0)) AS sshd_hostel_disc_amt,
                TO_NUMBER(NVL(sshd.sshd_hostel_payable, 0)) AS sshd_hostel_payable,
                TO_NUMBER(NVL(sshd.sshd_total_fee_actual, 0)) AS sshd_total_fee_actual,
                TO_NUMBER(NVL(sshd.sshd_total_disc_amt, 0)) AS sshd_total_disc_amt,
                TO_NUMBER(NVL(sshd.sshd_total_payable, 0)) AS sshd_total_payable,
                TO_NUMBER(NVL(sshd.sshd_surplus_consumed, 0)) AS sshd_surplus_consumed,
                TO_NUMBER(NVL(sshd.sshd_surplus_pending, 0)) AS sshd_surplus_pending,
                TO_NUMBER(NVL(sshd.sshd_invoice_amount, 0)) AS sshd_invoice_amount
            FROM fin_students_scholarships_hostel_details sshd
            LEFT JOIN scholarship_type st ON st.scholarship_id = sshd.sshd_type
            LEFT JOIN admission_apply_info aai ON aai.aai_id = sshd.sshd_app_id
            LEFT JOIN admission_personal_info api ON api.api_id = aai.aai_api_id
            LEFT JOIN students_apply_info sai
                ON UPPER(TRIM(sai.sai_enrollment_no)) = UPPER(TRIM(NVL(sshd.sshd_sai_enrollment_no, aai.cms_sai_enrollment_no)))
            LEFT JOIN students_personal_info spi ON spi.spi_id = sai.sai_spi_id
            LEFT JOIN schools s ON s.school_id = NVL(aai.aai_sdpo_school_id, sai.sai_sdpo_school_id)
            WHERE NVL(TRIM(sshd.sshd_status), 'E') = 'E'
              AND sshd.sshd_sess_id = :session_id
              -- All active hostel rows of a student whose hostel challan has at least
              -- one row staged here. One hostel challan covers every hostel scholarship.
              AND EXISTS (
                    SELECT 1
                    FROM fin_students_scholarships_hostel_details p
                    LEFT JOIN admission_apply_info paai ON paai.aai_id = p.sshd_app_id
                    WHERE p.sshd_sess_id = sshd.sshd_sess_id
                      AND NVL(TRIM(p.sshd_status), 'E') = 'E'
                      AND UPPER(TRIM(NVL(p.sshd_bulk_post_status, ''))) = 'PENDING'
                      {$pendingSourceScope}
                      -- Resolved identity, matching the grouping key — same reason as the
                      -- academic query above.
                      AND (
                            (sshd.sshd_app_id IS NOT NULL AND p.sshd_app_id = sshd.sshd_app_id)
                         OR UPPER(TRIM(NVL(p.sshd_sai_enrollment_no, paai.cms_sai_enrollment_no)))
                            = UPPER(TRIM(NVL(sshd.sshd_sai_enrollment_no, aai.cms_sai_enrollment_no)))
                      )
              )
              {$studentFilter}
            ORDER BY enrollment_no, sshd.sshd_id
        ", $bindings);
    }

    /**
     * Temporary challan preview for updated scholarship amounts (no DB writes).
     */
    public function previewChallan(Request $request)
    {
        $sessionId = (int) $request->input('session_id');
        $records = $request->input('records', []);

        if (!$sessionId) {
            return response()->json(['status' => false, 'message' => 'Session is required.'], 422);
        }
        if (!is_array($records) || empty($records)) {
            return response()->json(['status' => false, 'message' => 'No records to preview.'], 422);
        }

        $lines = [];
        $errors = [];

        try {
        foreach ($records as $item) {
            $enrollment = strtoupper(trim((string) ($item['enrollment_no'] ?? '')));
            $category = strtolower(trim((string) ($item['category'] ?? 'academic')));
            $challanCategory = trim((string) ($item['challan_category'] ?? 'Full_semester'));
            $percentage = (float) ($item['percentage'] ?? $item['scholarship_percentage'] ?? 0);
            $amount = isset($item['amount']) || isset($item['scholarship_amount'])
                ? (float) ($item['amount'] ?? $item['scholarship_amount'])
                : null;
            $scholarshipName = trim((string) ($item['scholarship_name'] ?? ''));

            if ($enrollment === '') {
                continue;
            }

            if ($category === 'hostel') {
                $feeBase = (float) ($item['hostel_fee'] ?? 0);
                if ($feeBase <= 0) {
                    $hostelRow = DB::table('fin_students_scholarships_hostel_details')
                        ->where('sshd_sess_id', $sessionId)
                        ->whereRaw('UPPER(TRIM(sshd_sai_enrollment_no)) = ?', [$enrollment])
                        ->whereRaw("NVL(sshd_status, 'E') = 'E'")
                        ->orderByDesc('sshd_id')
                        ->first();
                    $feeBase = (float) ($hostelRow->sshd_hostel_fee ?? $hostelRow->SSHD_HOSTEL_FEE ?? 0);
                }
                $schAmount = $amount !== null ? $amount : (($percentage / 100) * $feeBase);
                $breakdown = $this->buildChallanPreviewFeeBreakdown([
                    'ok' => $feeBase > 0,
                    'amountBase' => $feeBase,
                    'referenceLabel' => 'Hostel Fee',
                    'creditHours' => null,
                    'arrearComponents' => [],
                ], 'Hostel_Fee', 'hostel');
                $lines[] = [
                    'enrollment_no' => $enrollment,
                    'scholarship_name' => $scholarshipName ?: 'Hostel Scholarship',
                    'category' => 'hostel',
                    'challan_category' => 'Hostel_Fee',
                    'fee_base' => round($feeBase, 2),
                    'tuition_base' => round($breakdown['tuition_base'], 2),
                    'one_time_total' => 0,
                    'gross_total' => round($breakdown['gross_total'], 2),
                    'fee_components' => $breakdown['components'],
                    'scholarship_percentage' => $percentage,
                    'scholarship_amount' => round($schAmount, 2),
                    'net_amount' => round(max(0, $breakdown['gross_total'] - $schAmount), 2),
                ];
                continue;
            }

            if ($amount === null || $amount <= 0) {
                $amount = $this->computeAcademicScholarshipAmount($sessionId, $enrollment, $challanCategory, $percentage);
            }

            $feeRes = $this->resolvePreviewFeeStructure($sessionId, $enrollment, $challanCategory);
            $breakdown = $this->buildChallanPreviewFeeBreakdown($feeRes, $challanCategory, 'academic', true);

            if ($breakdown['gross_total'] <= 0) {
                $errors[] = $enrollment . ': fee structure unavailable for ' . $challanCategory;
            }

            $netTuition = max(0, $breakdown['tuition_base'] - $amount);

            $lines[] = [
                'enrollment_no' => $enrollment,
                'scholarship_name' => $scholarshipName ?: '—',
                'category' => 'academic',
                'challan_category' => $challanCategory,
                'fee_base' => round($breakdown['tuition_base'], 2),
                'tuition_base' => round($breakdown['tuition_base'], 2),
                'one_time_total' => 0,
                'gross_total' => round($breakdown['gross_total'], 2),
                'fee_components' => $breakdown['components'],
                'scholarship_percentage' => $percentage,
                'scholarship_amount' => round($amount, 2),
                'net_amount' => round($netTuition, 2),
            ];
        }
        } catch (\Throwable $e) {
            \Log::error('Scholarship challan preview failed: ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Unable to compute preview. Please try again.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }

        $grossTotal = 0;
        $schTotal = 0;
        $netTotal = 0;
        foreach ($lines as $line) {
            $grossTotal += (float) ($line['gross_total'] ?? $line['fee_base'] ?? 0);
            $schTotal += (float) ($line['scholarship_amount'] ?? 0);
            $netTotal += (float) ($line['net_amount'] ?? 0);
        }

        return response()->json([
            'status' => true,
            'lines' => $lines,
            'totals' => [
                'fee_base' => round($grossTotal, 2),
                'gross_total' => round($grossTotal, 2),
                'scholarship_amount' => round($schTotal, 2),
                'net_amount' => round($netTotal, 2),
            ],
            'warnings' => array_slice($errors, 0, 10),
        ]);
    }

    /**
     * Resolve fee structure for preview (Existing students only — bulk form enrollments).
     */
    protected function resolvePreviewFeeStructure(int $sessionId, string $enrollment, string $challanCategory): array
    {
        $feeRes = $this->resolveAcademicFeeStructure($sessionId, $enrollment, 'Existing', $challanCategory);
        if (!empty($feeRes['ok'])) {
            return $feeRes;
        }

        if ($challanCategory === 'Full_semester') {
            $fallback = $this->resolveFullTuitionFallback($sessionId, $enrollment);
            if (!empty($fallback['ok'])) {
                return $fallback;
            }
        }

        return is_array($feeRes) ? $feeRes : ['ok' => false];
    }

    /**
     * Fee lines for challan preview.
     *
     * For existing students, admission/security/activity/medical are arrears-only
     * (Student Record Revised) — not on the regular semester challan preview.
     */
    protected function buildChallanPreviewFeeBreakdown(
        array $feeRes,
        string $challanCategory,
        string $category = 'academic',
        bool $existingStudent = true
    ): array {
        if (empty($feeRes['ok'])) {
            return [
                'components' => [],
                'tuition_base' => 0,
                'one_time_total' => 0,
                'gross_total' => 0,
            ];
        }

        $tuition = (float) ($feeRes['amountBase'] ?? 0);
        $components = [];

        if ($category === 'hostel' || $challanCategory === 'Hostel_Fee') {
            $components[] = [
                'key' => 'hostel',
                'label' => 'Hostel Fee',
                'amount' => $tuition,
            ];

            return [
                'components' => $components,
                'tuition_base' => $tuition,
                'one_time_total' => 0,
                'gross_total' => $tuition,
            ];
        }

        $tuitionLabel = (string) ($feeRes['referenceLabel'] ?? 'Tuition Fee');
        if (isset($feeRes['creditHours']) && $feeRes['creditHours'] !== null && (float) $feeRes['creditHours'] > 0) {
            $tuitionLabel .= ' (' . (float) $feeRes['creditHours'] . ' cr)';
            if (!empty($feeRes['repeatCredits']) && (float) $feeRes['repeatCredits'] > 0) {
                $tuitionLabel .= ' + repeat ' . (float) $feeRes['repeatCredits'] . '/2';
            }
        } else {
            $feeTypeLabel = $this->academicCategoryLabels()[$challanCategory] ?? $challanCategory;
            if ($challanCategory !== 'Full_semester') {
                $tuitionLabel = $tuitionLabel . ' — ' . $feeTypeLabel;
            }
        }

        $components[] = [
            'key' => 'tuition',
            'label' => $tuitionLabel,
            'amount' => $tuition,
        ];

        $oneTime = 0.0;
        if (!$existingStudent) {
            $arrearLabels = [
                'admission' => 'Admission Fee',
                'security' => 'Security Fee',
                'activity' => 'Activity Charges',
                'medical' => 'Medical Insurance',
            ];
            $arrear = is_array($feeRes['arrearComponents'] ?? null) ? $feeRes['arrearComponents'] : [];
            foreach ($arrearLabels as $key => $label) {
                $amt = (float) ($arrear[$key] ?? 0);
                if ($amt > 0) {
                    $components[] = ['key' => $key, 'label' => $label, 'amount' => $amt];
                    $oneTime += $amt;
                }
            }
        }

        return [
            'components' => $components,
            'tuition_base' => $tuition,
            'one_time_total' => $oneTime,
            'gross_total' => $tuition + $oneTime,
        ];
    }

    /**
     * Post the ACCEPTED challans: cancel the live challan and re-issue it from the
     * frozen snapshot, through the same engine the single page uses.
     *
     * Only challans the user reviewed and accepted are written. `accepted` carries
     * "ENROLLMENT|Challan_Category" keys; anything absent stays PENDING. The empty
     * check below is the server-side half of the review gate, so a hand-crafted
     * request cannot post an unreviewed challan.
     */
    public function postPendingScholarships(Request $request)
    {
        $sessionId = (int) $request->input('session_id');
        $accepted = $request->input('accepted', []);

        if (!$sessionId) {
            return response()->json(['status' => false, 'message' => 'Session is required.'], 422);
        }

        if (!$this->canQueueScholarshipPosts()) {
            return response()->json([
                'status' => false,
                'message' => $this->bulkSchemaWarning(),
            ], 422);
        }

        if (!is_array($accepted) || empty($accepted)) {
            return response()->json([
                'status' => false,
                'message' => 'Review each challan and Accept it before posting. Nothing was accepted.',
            ], 422);
        }

        // The scholarship list gate. It is enforced on Generate in the browser, which is
        // where it belongs for the user; this is the copy that actually holds, because a
        // client-side gate is advisory. Reports itself satisfied when the columns are not
        // deployed, so posting never becomes impossible on an install that is behind.
        $ack = $this->scholarshipListAckState($sessionId);
        if (!$ack['acknowledged']) {
            return response()->json([
                'status' => false,
                'message' => 'The Scholarship Checklist for this session has not been acknowledged. '
                    . 'Open it, review the scholarship names and acknowledge them, then post.',
                'needs_list_ack' => true,
            ], 422);
        }

        // Every accepted challan comes back with a verdict. `results` is the contract the
        // page renders: one row per accepted key, never fewer, so a challan can no longer
        // vanish between Accept and the result banner the way 44 hostel ones did.
        $results = [];
        $addResult = function ($enrollment, $category, $status, $reason = null, array $messages = []) use (&$results) {
            $results[] = [
                'enrollment' => $enrollment,
                'category' => $category,
                'status' => $status,
                'reason' => $reason,
                'messages' => $messages,
            ];
        };

        // Group accepted keys by student: the categories are walked in the order Save
        // simulated the surplus split, and each one is its own transaction inside
        // postAcceptedChallansForStudent().
        $byStudent = [];
        foreach ($accepted as $key) {
            $parts = explode('|', (string) $key, 2);
            $enrollment = strtoupper(trim($parts[0] ?? ''));
            $category = trim($parts[1] ?? '');

            // These two used to `continue` in silence, so a malformed key was neither
            // posted nor counted nor mentioned.
            if ($enrollment === '' || $category === '') {
                $addResult((string) $key, '', 'skipped', 'Malformed challan key — could not read the student or the challan type from it.');
                continue;
            }
            if (!in_array($category, $this->allChallanCategories(), true)) {
                $addResult($enrollment, $category, 'skipped', 'Unrecognised challan type "' . $category . '" — nothing was posted for it.');
                continue;
            }
            $byStudent[$enrollment][] = $category;
        }

        if (empty($byStudent)) {
            return response()->json([
                'status' => false,
                'message' => 'No valid accepted challans were received.',
                'summary' => ['posted' => 0, 'skipped' => count($results), 'failed' => 0, 'students' => 0],
                'results' => $results,
            ], 422);
        }

        $postedStudents = [];

        foreach ($byStudent as $enrollment => $categories) {
            $categories = array_values(array_unique($categories));

            $aaiId = (int) ($this->resolveStudentAaiId($enrollment) ?? 0);
            if (!$aaiId) {
                foreach ($categories as $category) {
                    $addResult($enrollment, $category, 'failed',
                        'Could not resolve this student\'s application id — left PENDING for retry.');
                }
                continue;
            }

            try {
                // No transaction here on purpose. Each CATEGORY opens and closes its own
                // inside postAcceptedChallansForStudent(), so one failing challan can no
                // longer roll back a sibling that already wrote successfully.
                $studentResults = $this->postAcceptedChallansForStudent(
                    $aaiId,
                    $enrollment,
                    $sessionId,
                    'Existing',
                    $categories
                );

                foreach ($studentResults as $row) {
                    $addResult(
                        $enrollment,
                        $row['category'],
                        $row['status'],
                        $row['reason'],
                        isset($row['messages']) ? $row['messages'] : []
                    );
                    if ($row['status'] === 'posted') {
                        $postedStudents[$enrollment] = true;
                    }
                }
            } catch (\Throwable $e) {
                // Only reachable if the per-category handling itself blew up. Every
                // accepted category for this student is reported rather than dropped.
                foreach ($categories as $category) {
                    $addResult($enrollment, $category, 'failed', $e->getMessage());
                }
                \Log::error('Bulk scholarship post failed for student', [
                    'enrollment' => $enrollment,
                    'aai_id' => $aaiId,
                    'session_id' => $sessionId,
                    'categories' => $categories,
                    'exception' => get_class($e),
                    'message' => $e->getMessage(),
                    'file' => $e->getFile() . ':' . $e->getLine(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }
        }

        $posted = 0;
        $skipped = 0;
        $failed = 0;
        foreach ($results as $row) {
            if ($row['status'] === 'posted') {
                $posted++;
            } elseif ($row['status'] === 'skipped') {
                $skipped++;
            } else {
                $failed++;
            }
        }

        $summary = [
            'posted' => $posted,
            'skipped' => $skipped,
            'failed' => $failed,
            'students' => count($postedStudents),
        ];

        $message = "Posted {$posted} challan(s) across " . count($postedStudents) . ' student(s)';
        $unfinished = $skipped + $failed;
        $message .= $unfinished
            ? ", {$unfinished} not posted ({$failed} failed, {$skipped} skipped) — see the list below."
            : '.';

        // `results` is deliberately NOT truncated. It used to be array_slice(…, 0, 30) and
        // the failures, which arrive in student order rather than priority order, fell off
        // the end unannounced.
        return response()->json([
            'status' => $unfinished === 0,
            'message' => $message,
            'summary' => $summary,
            'results' => $results,
            // Kept for anything still reading the old shape.
            'posted' => count($postedStudents),
            'posted_challans' => $posted,
            'failed' => $failed,
            // 500 only when nothing posted AND something actually errored. A run where
            // every challan was legitimately SKIPPED (stale surplus, rows owned by another
            // page) is a valid answer, not a server fault — 500 would push it into the
            // client's error branch and lose the per-challan reasons.
        ], ($failed > 0 && $posted === 0) ? 500 : 200);
    }

    /**
     * Re-freeze SSD_SURPLUS_PENDING/CONSUMED against the live fin_fee_adjustment balance
     * for every student staged by this page whose surplus is currently spendable.
     *
     * Why this exists: refreshScholarshipSnapshot() (called by Save on the Bulk Update
     * form) is the only thing that ever writes SSD_SURPLUS_PENDING, and it only runs when
     * a grader re-saves that student. If the student's surplus balance moves afterwards —
     * another challan of theirs gets cancelled elsewhere, minting new credit, or an
     * earlier bulk post of theirs spends some — the frozen figure goes stale and
     * postAcceptedChallansForStudent()'s freeze check (PostsScholarshipSnapshots.php)
     * skips the student at Post with "Surplus changed since the challan was previewed",
     * telling the grader to go re-save them one at a time. This does the same
     * recomputation in bulk, scoped to students who actually have spendable surplus, so
     * they can be re-synced from this page without a manual round trip.
     *
     * Never writes to fin_fee_adjustment — refreshScholarshipSnapshot() only reads it
     * (via SurplusAdjuster::pendingSurplus()/simulate()); the balance itself is only ever
     * spent by SurplusAdjuster::apply() at actual Post time.
     */
    public function resyncSurplusPending(Request $request)
    {
        $sessionId = (int) $request->input('session_id');
        if (!$sessionId) {
            return response()->json(['status' => false, 'message' => 'Session is required.'], 422);
        }

        if (!$this->canQueueScholarshipPosts()) {
            return response()->json([
                'status' => false,
                'message' => $this->bulkSchemaWarning(),
            ], 422);
        }

        try {
            $students = $this->fetchSurplusPendingStudents($sessionId);
        } catch (\Throwable $e) {
            \Log::error('Surplus re-sync lookup failed: ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Unable to load students with a pending surplus balance.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }

        if (empty($students)) {
            return response()->json([
                'status' => true,
                'message' => 'No staged records currently have a live surplus balance to re-sync.',
                'summary' => ['resynced' => 0, 'failed' => 0, 'students' => 0],
                'results' => [],
            ]);
        }

        $results = [];
        $addResult = function ($enrollment, $status, $reason = null) use (&$results) {
            $results[] = ['enrollment' => $enrollment, 'status' => $status, 'reason' => $reason];
        };

        $resynced = 0;
        $failed = 0;
        // Students left alone because the single Student Record page owns them.
        $skipped = 0;

        foreach ($students as $row) {
            $enrollment = strtoupper(trim((string) ($row->enrollment_no ?? $row->ENROLLMENT_NO ?? '')));
            $aaiId = (int) ($row->app_id ?? $row->APP_ID ?? 0);

            if (!$aaiId) {
                $addResult($enrollment, 'failed', 'Could not resolve this student\'s application id.');
                $failed++;
                continue;
            }

            // The single-page ownership rule, which this endpoint was missing.
            //
            // refreshScholarshipSnapshot() walks EVERY active row of the student, not just
            // the ones this page staged, and rewrites SSD_AMOUNT as
            // round(ssd_percentage / 100 * base, 2) along with ~40 other snapshot columns.
            // snapshotRowIsForeign() only strips SSD_SOURCE_PAGE from the payload, so
            // ownership survives but the figures do not.
            //
            // That was harmless while both pages derived the amount the same lossy way.
            // It stopped being harmless when the single page gained an AMOUNT entry mode:
            // a scholarship entered there as exactly Rs 200,000 would be silently rewritten
            // here as 200,000.14, because re-deriving it from a 2-decimal percentage cannot
            // land back on the figure the donor authorised.
            //
            // The student qualifies for this loop on the strength of ONE bulk-staged row
            // (fetchSurplusPendingStudents scopes by bulkSourceScopeSql), so a mixed-owner
            // student reaches here routinely — this is not a rare path.
            //
            // KNOWN GAPS, deliberately left rather than papered over:
            //   - singlePageLockedStudents() is keyed on enrollment number, so a new
            //     applicant with a NULL CMS_SAI_ENROLLMENT_NO can never be locked. The
            //     same hole is already conceded at saveStudentScholarships()'s guard.
            //   - that lookup fails OPEN (it swallows its exception and returns []), so a
            //     transient Oracle error lifts the lock for that request.
            if ($enrollment !== '' && $this->studentIsSinglePageOwned($sessionId, $enrollment)) {
                $addResult($enrollment, 'skipped', $this->singlePageLockedMessage($enrollment));
                $skipped++;
                continue;
            }

            try {
                DB::beginTransaction();
                $this->refreshScholarshipSnapshot($sessionId, $aaiId, $enrollment);
                DB::commit();
                $addResult($enrollment, 'resynced');
                $resynced++;
            } catch (\Throwable $e) {
                DB::rollBack();
                $addResult($enrollment, 'failed', $e->getMessage());
                $failed++;
                \Log::error('Surplus re-sync failed for student', [
                    'enrollment' => $enrollment,
                    'aai_id' => $aaiId,
                    'session_id' => $sessionId,
                    'exception' => get_class($e),
                    'message' => $e->getMessage(),
                    'file' => $e->getFile() . ':' . $e->getLine(),
                ]);
            }
        }

        $message = "Re-synced {$resynced} student(s) against their current surplus balance";
        if ($skipped) {
            $message .= ", {$skipped} skipped (managed on the Student Record page)";
        }
        $message .= $failed ? ", {$failed} failed — see the list below." : '.';

        return response()->json([
            'status' => $failed === 0,
            'message' => $message,
            'summary' => ['resynced' => $resynced, 'skipped' => $skipped, 'failed' => $failed, 'students' => count($students)],
            'results' => $results,
        ], ($failed > 0 && $resynced === 0) ? 500 : 200);
    }

    /**
     * Distinct (enrollment, app_id) pairs staged by this page, for this session, that MIGHT
     * be drifted against fin_fee_adjustment — either direction.
     *
     * Mirrors fetchPendingAcademicRows()/fetchPendingHostelRows()'s filters (active row,
     * PENDING, this page's source tag) but scoped down to just the identity columns
     * resyncSurplusPending() needs, plus a candidate test that has to catch BOTH ways a
     * student can drift, or it silently repairs only one of them:
     *
     *   - live surplus > 0 now, whatever the frozen figure was (a cancellation elsewhere
     *     minted new credit since the last Save)
     *   - frozen SSD/SSHD_SURPLUS_PENDING > 0, whatever fin_fee_adjustment holds NOW —
     *     including nothing at all. Filtering on live surplus > 0 alone (the original cut
     *     of this method) missed exactly this case: a hostel student whose surplus had
     *     already been fully spent to zero on fin_fee_adjustment kept showing "surplus
     *     changed" forever, because a student with zero live rows can never appear inside
     *     `WHERE app_id IN (SELECT ... WHERE surplus_amount > 0)` no matter how many times
     *     Re-sync Surplus runs — there was no candidate query that could ever select them.
     *
     * This is the same two-part test getStagedSummary()'s drift computation already applies
     * in PHP (frozen > 0 OR live-app-id-set); expressed here as SQL so the candidate list
     * and the highlight agree on who is stale.
     *
     * Checked BOTH tables (academic and hostel) on purpose: a student holding ONLY a hostel
     * award (BEEF/PEEF/UMISAA Hostel, no academic scholarship at all) has no row in the
     * academic table to be found FROM, so scanning students_scholarships_details alone
     * silently skipped every one of them too. refreshScholarshipSnapshot() re-freezes both
     * tables together for whichever student is passed in, so a student found via either
     * table only needs to run once.
     */
    protected function fetchSurplusPendingStudents($sessionId)
    {
        $rows = [];

        if ($this->hasSsdBulkPostStatusColumn()) {
            $sourceScope = $this->bulkSourceScopeSql('ssd');
            $rows = array_merge($rows, DB::select("
                SELECT DISTINCT
                    TO_CHAR(NVL(aai.cms_sai_enrollment_no, ssd.ssd_sai_enrollment_no)) AS enrollment_no,
                    TO_CHAR(ssd.ssd_app_id) AS app_id
                FROM students_scholarships_details ssd
                LEFT JOIN admission_apply_info aai ON aai.aai_id = ssd.ssd_app_id
                WHERE NVL(ssd.status, 'E') = 'E'
                  AND ssd.ssd_sess_id = :session_id
                  AND UPPER(TRIM(NVL(ssd.ssd_bulk_post_status, ''))) = 'PENDING'
                  AND NVL(ssd.ssd_percentage, 0) > 0
                  {$sourceScope}
                  AND (
                        NVL(ssd.ssd_surplus_pending, 0) > 0
                     OR ssd.ssd_app_id IN (
                            SELECT ffa.ffa_app_id
                            FROM fin_fee_adjustment ffa
                            WHERE NVL(ffa.ffa_surplus_amount, 0) > 0
                        )
                  )
            ", ['session_id' => $sessionId]));
        }

        if ($this->hasSshdBulkPostStatusColumn()) {
            $sourceScope = $this->bulkSourceScopeSql('sshd', true);
            $rows = array_merge($rows, DB::select("
                SELECT DISTINCT
                    TO_CHAR(NVL(sshd.sshd_sai_enrollment_no, aai.cms_sai_enrollment_no)) AS enrollment_no,
                    TO_CHAR(sshd.sshd_app_id) AS app_id
                FROM fin_students_scholarships_hostel_details sshd
                LEFT JOIN admission_apply_info aai ON aai.aai_id = sshd.sshd_app_id
                WHERE NVL(TRIM(sshd.sshd_status), 'E') = 'E'
                  AND sshd.sshd_sess_id = :session_id
                  AND UPPER(TRIM(NVL(sshd.sshd_bulk_post_status, ''))) = 'PENDING'
                  AND NVL(sshd.sshd_percentage, 0) > 0
                  {$sourceScope}
                  AND (
                        NVL(sshd.sshd_surplus_pending, 0) > 0
                     OR sshd.sshd_app_id IN (
                            SELECT ffa.ffa_app_id
                            FROM fin_fee_adjustment ffa
                            WHERE NVL(ffa.ffa_surplus_amount, 0) > 0
                        )
                  )
            ", ['session_id' => $sessionId]));
        }

        // A student found via both tables must still only be re-synced once.
        $seen = [];
        $deduped = [];
        foreach ($rows as $row) {
            $enrollment = strtoupper(trim((string) ($row->enrollment_no ?? $row->ENROLLMENT_NO ?? '')));
            $appId = (int) ($row->app_id ?? $row->APP_ID ?? 0);
            $key = $enrollment !== '' ? $enrollment : ('APP:' . $appId);
            if (isset($seen[$key])) {
                continue;
            }
            $seen[$key] = true;
            $deduped[] = $row;
        }

        return $deduped;
    }

    protected function fetchPendingAcademicRows($sessionId, $schoolId, $studentId)
    {
        if (!$this->hasSsdBulkPostStatusColumn()) {
            return [];
        }

        $bindings = ['session_id' => $sessionId];
        $schoolFilter = $studentFilter = '';

        if ($schoolId) {
            $schoolFilter = ' AND NVL(aai.aai_sdpo_school_id, sai.sai_sdpo_school_id) = :school_id';
            $bindings['school_id'] = $schoolId;
        }
        if ($studentId !== '') {
            $studentFilter = ' AND UPPER(TRIM(NVL(aai.cms_sai_enrollment_no, ssd.ssd_sai_enrollment_no))) LIKE :student_id';
            $bindings['student_id'] = '%' . $studentId . '%';
        }

        // Both scholarship pages stage into this table; only queue what this one owns.
        // This assignment was missing — the SQL below interpolated {$sourceScope} while
        // nothing ever set it, so it resolved to '' and the academic queue listed (and
        // posted) rows belonging to the single Student Record page. fetchPendingHostelRows()
        // has always had it; this is the academic twin catching up.
        $sourceScope = $this->bulkSourceScopeSql('ssd');

        $sql = "
            SELECT
                TO_CHAR(ssd.ssd_id) AS record_id,
                CAST('academic' AS VARCHAR2(20)) AS category,
                TO_CHAR(NVL(aai.cms_sai_enrollment_no, ssd.ssd_sai_enrollment_no)) AS enrollment_no,
                TO_CHAR(NVL(api.api_name, spi.spi_name)) AS student_name,
                TO_CHAR(NVL(s.school_code, s.school_name)) AS school_name,
                TO_NUMBER(st.scholarship_id) AS scholarship_id,
                TO_CHAR(st.scholarship_name) AS scholarship_name,
                TO_NUMBER(NVL(ssd.ssd_percentage, 0)) AS scholarship_percentage,
                {$this->sqlAcademicScholarshipAmountExpr('ssd')},
                TO_CHAR(NVL(ssd.ssd_challan_category, 'Full_semester')) AS challan_category,
                TO_CHAR(ssd.ssd_app_id) AS app_id,
                TO_NUMBER(ssd.ssd_sess_id) AS sess_id,
                {$this->sqlAcademicBulkPostStatusExpr('ssd')},
                {$this->sqlAcademicSourcePageExpr('ssd')}
            FROM students_scholarships_details ssd
            JOIN scholarship_type st ON st.scholarship_id = ssd.ssd_type
            LEFT JOIN admission_apply_info aai ON aai.aai_id = ssd.ssd_app_id
            LEFT JOIN admission_personal_info api ON api.api_id = aai.aai_api_id
            LEFT JOIN students_apply_info sai
                ON UPPER(TRIM(sai.sai_enrollment_no)) = UPPER(TRIM(NVL(aai.cms_sai_enrollment_no, ssd.ssd_sai_enrollment_no)))
            LEFT JOIN students_personal_info spi ON spi.spi_id = sai.sai_spi_id
            LEFT JOIN schools s ON s.school_id = NVL(aai.aai_sdpo_school_id, sai.sai_sdpo_school_id)
            WHERE NVL(ssd.status, 'E') = 'E'
              AND ssd.ssd_sess_id = :session_id
              AND UPPER(TRIM(NVL(ssd.ssd_bulk_post_status, ''))) = 'PENDING'
              {$sourceScope}
              {$schoolFilter}
              {$studentFilter}
            ORDER BY enrollment_no, scholarship_name
        ";

        return DB::select($sql, $bindings);
    }

    protected function fetchPendingHostelRows($sessionId, $schoolId, $studentId)
    {
        if (!$this->hasSshdBulkPostStatusColumn()) {
            return [];
        }

        $bindings = ['session_id' => $sessionId];
        $schoolFilter = $studentFilter = '';

        if ($schoolId) {
            $schoolFilter = ' AND NVL(aai.aai_sdpo_school_id, sai.sai_sdpo_school_id) = :school_id';
            $bindings['school_id'] = $schoolId;
        }
        if ($studentId !== '') {
            $studentFilter = ' AND UPPER(TRIM(NVL(sshd.sshd_sai_enrollment_no, aai.cms_sai_enrollment_no))) LIKE :student_id';
            $bindings['student_id'] = '%' . $studentId . '%';
        }
        $sourceScope = $this->bulkSourceScopeSql('sshd', true);

        $sql = "
            SELECT
                TO_CHAR(sshd.sshd_id) AS record_id,
                CAST('hostel' AS VARCHAR2(20)) AS category,
                TO_CHAR(NVL(sshd.sshd_sai_enrollment_no, aai.cms_sai_enrollment_no)) AS enrollment_no,
                TO_CHAR(NVL(api.api_name, spi.spi_name)) AS student_name,
                TO_CHAR(NVL(s.school_code, s.school_name)) AS school_name,
                TO_NUMBER(st.scholarship_id) AS scholarship_id,
                TO_CHAR(NVL(st.scholarship_name, 'Hostel Scholarship')) AS scholarship_name,
                TO_NUMBER(NVL(sshd.sshd_percentage, 0)) AS scholarship_percentage,
                TO_NUMBER(NVL(sshd.sshd_amount, 0)) AS scholarship_amount,
                CAST('Hostel_Fee' AS VARCHAR2(50)) AS challan_category,
                TO_CHAR(sshd.sshd_app_id) AS app_id,
                TO_NUMBER(sshd.sshd_sess_id) AS sess_id,
                {$this->sqlHostelBulkPostStatusExpr('sshd')},
                {$this->sqlHostelSourcePageExpr('sshd')}
            FROM fin_students_scholarships_hostel_details sshd
            LEFT JOIN scholarship_type st ON st.scholarship_id = sshd.sshd_type
            LEFT JOIN admission_apply_info aai ON aai.aai_id = sshd.sshd_app_id
            LEFT JOIN admission_personal_info api ON api.api_id = aai.aai_api_id
            LEFT JOIN students_apply_info sai
                ON UPPER(TRIM(sai.sai_enrollment_no)) = UPPER(TRIM(NVL(sshd.sshd_sai_enrollment_no, aai.cms_sai_enrollment_no)))
            LEFT JOIN students_personal_info spi ON spi.spi_id = sai.sai_spi_id
            LEFT JOIN schools s ON s.school_id = NVL(aai.aai_sdpo_school_id, sai.sai_sdpo_school_id)
            WHERE NVL(TRIM(sshd.sshd_status), 'E') = 'E'
              AND sshd.sshd_sess_id = :session_id
              AND UPPER(TRIM(NVL(sshd.sshd_bulk_post_status, ''))) = 'PENDING'
              {$sourceScope}
              {$schoolFilter}
              {$studentFilter}
            ORDER BY enrollment_no, scholarship_name
        ";

        return DB::select($sql, $bindings);
    }


    protected function computeAcademicScholarshipAmount(
        int $sessionId,
        string $enrollmentNo,
        string $challanCategory,
        float $percentage
    ): float {
        if ($percentage <= 0 || $enrollmentNo === '') {
            return 0.0;
        }

        $base = $this->academicFeeBaseFor($sessionId, $enrollmentNo, $challanCategory);

        return $base > 0 ? round($base * ($percentage / 100), 2) : 0.0;
    }

    /**
     * The fee a scholarship percentage applies to, for one student and challan category.
     *
     * Memoised: a student usually holds several scholarships against the same challan,
     * and the list renders every one of them, so resolving per ROW would repeat the same
     * fee-structure lookup many times over.
     *
     * Ladder: Existing -> New Admission <SEASON> -> (Full Tuition only) the live challan
     * or a loose fee-structure row. Plenty of programs carry no 'Existing' row for a
     * session, so stopping at the first would under-price them.
     */
    protected function academicFeeBaseFor(int $sessionId, string $enrollmentNo, string $challanCategory): float
    {
        $enrollmentNo = strtoupper(trim($enrollmentNo));
        if ($enrollmentNo === '' || $sessionId <= 0) {
            return 0.0;
        }

        $key = $sessionId . '|' . $enrollmentNo . '|' . $challanCategory;
        if (array_key_exists($key, $this->feeBaseCache)) {
            return $this->feeBaseCache[$key];
        }

        $typesToTry = ['Existing'];
        $sessName = DB::table('academic_sessions')->where('sess_id', $sessionId)->value('sess_name');
        $season = $sessName ? strtoupper(strtok(trim((string) $sessName), ' ')) : '';
        if ($season !== '') {
            $typesToTry[] = 'New Admission ' . $season;
        }

        foreach ($typesToTry as $studentType) {
            $res = $this->resolveAcademicFeeStructure($sessionId, $enrollmentNo, $studentType, $challanCategory);
            if (!empty($res['ok']) && (float) ($res['amountBase'] ?? 0) > 0) {
                return $this->feeBaseCache[$key] = (float) $res['amountBase'];
            }
        }

        if ($challanCategory === 'Full_semester') {
            $fallback = $this->resolveFullTuitionFallback($sessionId, $enrollmentNo);
            if (!empty($fallback['ok']) && (float) ($fallback['amountBase'] ?? 0) > 0) {
                return $this->feeBaseCache[$key] = (float) $fallback['amountBase'];
            }
        }

        return $this->feeBaseCache[$key] = 0.0;
    }

    /**
     * Derive the money for each listed row from its PERCENTAGE.
     *
     * SSD_AMOUNT is not a dependable source: scholarships applied from other pages
     * store SSD_PERCENTAGE and leave the amount NULL, which is the case for the vast
     * majority of existing rows. The percentage is the field that is always written, so
     * the list prices from it against the same fee base Save would use.
     *
     * `amount_source` tells the client which it got: 'stored' (from the table) or
     * 'derived' (calculated here, not yet persisted).
     *
     * @param  array<int, object> $rows
     */
    /**
     * Why a Not Awarded row cannot be awarded, or null when it can.
     *
     * Every reason here is one the save path would raise anyway — the point is to say it
     * on screen BEFORE the user types a percentage into 88 rows, instead of after, one
     * failed request per student.
     *
     * @return array{reason: string, message: string}|null
     */
    protected function resolveAwardBlock($row, float $base, string $category): ?array
    {
        $label = $this->academicCategoryLabels()[
            trim((string) ($row->challan_category ?? $row->CHALLAN_CATEGORY ?? '')) ?: 'Full_semester'
        ] ?? 'this fee type';

        // Hostel needs an assignment — mirrors studentHasHostelAssignment().
        if ($category === 'hostel') {
            $assigned = (int) ($row->hostel_assigned ?? $row->HOSTEL_ASSIGNED ?? 0);
            if ($assigned !== 1) {
                return [
                    'reason' => 'hostel',
                    'message' => 'No hostel assigned for this session, so a hostel scholarship cannot be applied.',
                ];
            }
        }

        // No fee to take a percentage of. The Additional Credit Hr case lands here.
        if ($base <= 0) {
            return [
                'reason' => 'fee',
                'message' => $category === 'hostel'
                    ? 'No hostel fee on this student\'s challan, so there is nothing to discount.'
                    : 'The ' . $label . ' fee could not be resolved for this student'
                        . (($row->challan_category ?? '') === 'additional_Full_semester'
                            ? ' — generate the additional credit-hour challan first.'
                            : '.'),
            ];
        }

        // Already at the cap for this bucket.
        $otherPct = (float) ($row->category_other_pct ?? $row->CATEGORY_OTHER_PCT ?? 0);
        if ($otherPct >= 100) {
            return [
                'reason' => 'cap',
                'message' => 'Already at 100% for ' . ($category === 'hostel' ? 'hostel' : $label)
                    . ' — no headroom for another scholarship.',
            ];
        }

        // Illegal fee-type combination, judged by the same rule the save enforces.
        if ($category !== 'hostel') {
            $existing = array_filter(array_map('trim', explode(
                ',',
                (string) ($row->existing_categories ?? $row->EXISTING_CATEGORIES ?? '')
            )));
            $target = trim((string) ($row->challan_category ?? $row->CHALLAN_CATEGORY ?? 'Full_semester'));
            if (!empty($existing) && !in_array($target, $existing, true)) {
                try {
                    $this->assertLegalCategoryCombination(array_merge($existing, [$target]));
                } catch (\InvalidArgumentException $e) {
                    return ['reason' => 'combination', 'message' => $e->getMessage()];
                }
            }
        }

        return null;
    }

    protected function attachDerivedAmounts(array $rows, int $sessionId): array
    {
        // Resolving a fee structure is several queries; the memo collapses repeats per
        // student+category, but a wholly unfiltered list can still hold thousands of
        // distinct students. Stop resolving past this many and say so, rather than
        // hanging the page.
        $budget = 400;

        foreach ($rows as $row) {
            $stored = $row->scholarship_amount ?? $row->SCHOLARSHIP_AMOUNT ?? null;
            $storedAmount = ($stored !== null) ? (float) $stored : null;
            $pct = (float) ($row->scholarship_percentage ?? $row->SCHOLARSHIP_PERCENTAGE ?? 0);
            $category = strtolower(trim((string) ($row->category ?? $row->CATEGORY ?? 'academic')));

            $row->amount_source = null;
            $row->fee_base = null;

            // ── the fee the percentage applies to ──
            if ($category === 'hostel') {
                // Carried on the row itself, so this costs nothing.
                $base = (float) ($row->hostel_fee ?? $row->HOSTEL_FEE ?? 0);
            } else {
                $enrollment = strtoupper(trim((string) ($row->enrollment_no ?? $row->ENROLLMENT_NO ?? '')));
                $challanCategory = trim((string) ($row->challan_category ?? $row->CHALLAN_CATEGORY ?? 'Full_semester'))
                    ?: 'Full_semester';
                $key = $sessionId . '|' . $enrollment . '|' . $challanCategory;
                $cached = array_key_exists($key, $this->feeBaseCache);

                if (!$cached && $budget <= 0) {
                    // Out of resolution budget — say so rather than reporting a 0 base.
                    $row->amount_source = $storedAmount > 0 ? 'stored' : 'skipped';

                    // A Not Awarded row whose fee was never looked up must NOT fall
                    // through as awardable: unknown is not the same as fine. Block it
                    // with a reason that tells the user how to get an answer.
                    if (trim((string) ($row->record_id ?? $row->RECORD_ID ?? '')) === '') {
                        $row->award_block = [
                            'reason' => 'skipped',
                            'message' => 'Too many students listed to price this row. '
                                . 'Narrow the filter (a school or programme) to award it.',
                        ];
                    }
                    continue;
                }
                if (!$cached) {
                    $budget--;
                }
                $base = $this->academicFeeBaseFor($sessionId, $enrollment, $challanCategory);
            }

            // Nothing resolved, but a stored amount and percentage imply what the base
            // must have been — better than showing the column empty.
            if ($base <= 0 && $storedAmount > 0 && $pct > 0) {
                $base = round(($storedAmount / $pct) * 100, 2);
            }
            $row->fee_base = $base > 0 ? round($base, 2) : null;

            // ── the money ──
            if ($storedAmount !== null && $storedAmount > 0) {
                $row->amount_source = 'stored';
                continue;
            }
            // ── can this row actually be awarded? ──
            //
            // Only for Not Awarded rows (no record_id): an existing scholarship is edited,
            // not created, and its own gating already lives in updateRow(). Deciding it
            // here means the reason is the SAME one the save would give, computed from
            // the same resolver — the UI cannot invite an award the server will refuse.
            if (trim((string) ($row->record_id ?? $row->RECORD_ID ?? '')) === '') {
                $row->award_block = $this->resolveAwardBlock($row, $base, $category);
            }

            if ($pct > 0 && $base > 0) {
                $row->scholarship_amount = round($base * ($pct / 100), 2);
                $row->amount_source = 'derived';
            }
        }

        return $rows;
    }

    /**
     * The enrolment number for an applicant id.
     *
     * The mirror of resolveStudentAaiId(). A scholarship row keyed by SSD_APP_ID alone
     * carries a blank SSD_SAI_ENROLLMENT_NO — new-admission students are awarded before
     * an enrolment is written back to the row — and every fee lookup on this page takes
     * the enrolment, not the applicant id. The list queries already cope, via
     * NVL(aai.cms_sai_enrollment_no, ssd.ssd_sai_enrollment_no); this gives the same
     * fallback to the paths that read a single row.
     */
    protected function resolveEnrollmentForAppId($aaiId): string
    {
        $aaiId = (int) $aaiId;
        if ($aaiId <= 0) {
            return '';
        }

        $value = DB::table('admission_apply_info')
            ->where('aai_id', $aaiId)
            ->value('cms_sai_enrollment_no');

        return strtoupper(trim((string) $value));
    }

    protected function resolveStudentAaiId(string $enrollment): ?int
    {
        $enrollment = strtoupper(trim($enrollment));
        if ($enrollment === '') {
            return null;
        }

        $row = DB::table('admission_apply_info')
            ->select('aai_id')
            ->whereRaw('UPPER(TRIM(cms_sai_enrollment_no)) = ?', [$enrollment])
            ->first();

        if ($row) {
            return (int) ($row->aai_id ?? $row->AAI_ID);
        }

        $row = DB::selectOne("
            SELECT aai.aai_id
            FROM students_apply_info sai
            LEFT JOIN admission_apply_info aai
                ON UPPER(TRIM(aai.cms_sai_enrollment_no)) = UPPER(TRIM(sai.sai_enrollment_no))
            WHERE UPPER(TRIM(sai.sai_enrollment_no)) = :enrollment_no
              AND aai.aai_id IS NOT NULL
        ", ['enrollment_no' => $enrollment]);

        if ($row) {
            return (int) ($row->aai_id ?? $row->AAI_ID);
        }

        return null;
    }
}
