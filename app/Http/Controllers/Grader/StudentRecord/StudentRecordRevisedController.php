<?php

namespace App\Http\Controllers\Grader\StudentRecord;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Grader\StudentRecord\Concerns\ResolvesCarryForwardSession;
use App\Http\Controllers\Grader\StudentRecord\Concerns\ResolvesScholarshipFees;
use App\Services\Hostel\HostelAssignmentResolver;
use App\Services\StudentRecord\Fees\CreditHourCaseResolver;
use App\Services\StudentRecord\ChallanPosting\ChallanPosterFactory;
use App\Services\StudentRecord\ChallanPosting\PostContext;
use App\Services\StudentRecord\ChallanPosting\Support\ChallanCanceller;
use App\Services\StudentRecord\ChallanPosting\Support\SurplusAdjuster;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;

/**
 * Student Record Management — REVISED grid page.
 *
 * A parallel implementation of the scholarship entry screen. The original
 * StudentRecordController and student_record.blade.php are frozen and keep
 * running unchanged; this controller backs the new spreadsheet-style grid
 * (resources/views/grader/StudentRecord/StudentRecordRevised.blade.php).
 *
 * The page REUSES the existing read endpoints for everything it can:
 *   GET  get-student-profile/{roll}/{studentType}/{session}   (profile, saved
 *                                          scholarships, arrears, prev session)
 *   GET  /api/get-hostel-feeStructure/...
 *   GET  /api/get-hostelScholarship-student/...
 *   POST /api/get-scholarship-record                          (lower DataTable)
 *   POST /api/sync-scholarship-enrollment-from-admission
 *
 * Only three things are genuinely new and live here:
 *   getFeeMatrix()       one call resolving ALL four challan categories, plus the
 *                        per-component arrears values the grid needs
 *   getPreviousArrears() previous-session arrears for the Import button
 *   saveAndPost()        the grid save: combination rule, arrears written to
 *                        fin_other_scholarships, then the same challan procedures
 */
class StudentRecordRevisedController extends Controller
{
    use ResolvesCarryForwardSession;
    use ResolvesScholarshipFees;

    /**
     * Who gets which due / valid dates out of FIN_CHALLAN_INSTALLMENTS.
     *
     * The two offices issue challans off different installment rows for the same student:
     * the Registrar office off the programme's own row, Admission off the row for the
     * student's admission round (FCI_FLAG = 'Admission'). The grader's account is what
     * decides, not the student, so it has to be resolved from the session.
     *
     * Arrays rather than a chain of `||` so adding somebody is one line. Deliberately
     * constants and not config: this deployment runs with a cached config, where a config
     * file stays invisible until `php artisan config:cache` is re-run — a constant ships
     * with the code and cannot be half-deployed.
     *
     * Only ADMISSION_OFFICE_USERS is actually tested. REGISTRAR_OFFICE_USERS records who
     * the default is meant for; anybody not named in either list also gets the Registrar
     * path, which is what keeps the page working for every other account.
     *
     * KEEP IN STEP with $srrRegistrarUsers / $srrAdmissionUsers at the top of
     * StudentRecordRevised.blade.php, which gate which students each account may process.
     * Two questions, one membership: an account restricted to admission students should
     * be issuing admission deadlines. They were allowed to drift once — asbah.rehman and
     * zainabsanik were admission-only in the UI while their challans took the Registrar
     * dates — and nothing surfaced it, because neither list is wrong on its own.
     */
    private const REGISTRAR_OFFICE_USERS = [
        'farzana',
        'hassan.farooq',
        'talha.sarfraz',
        'abdul.waheed',
        'tayyaba.nawaz',
    ];

    private const ADMISSION_OFFICE_USERS = [
        'sidra.tanveer',
        'muhammad.munim',
        'hur.hussain',
        'asbah.rehman',
        'zainabsanik',
    ];

    /**
     * The four academic challan categories, in display order.
     *
     * Wording is shared with the Scholarship Bulk pages
     * (ScholarshipFormController::academicCategoryLabels() and feeTypeLabel() in their
     * blades) so the same fee type is never named two different ways across the system.
     * The KEYS are load-bearing — array_keys() of this drives the surplus walk order —
     * so only the values may be reworded.
     */
    private function academicCategories()
    {
        return [
            'Full_semester'            => 'Full Semester',
            'Credit_hours'             => 'Credit Hours',
            'additional_Full_semester' => 'Additional Credit Hr',
            'Short/Summer_Course'      => 'Short / Summer',
        ];
    }

    /**
     * This page reimplements FIN_PRO_SUMMER_CREDIT_HOUR, so summer is priced course by
     * course at each course's OFFERING school rate. See the trait for why it is opt-in.
     */
    protected function usesPerCourseSummerPricing()
    {
        return true;
    }

    /**
     * Additional credit hours from this page's own staged snapshot.
     *
     * Save cancels the additional-credit-hour challan and fin_bulk_challan_cancellation
     * DELETEs it, taking SC_ENROLLED_CR_HR with it — FIN_STUDENT_CANCEL_CHALLAN has no
     * such column. Without this the page would refuse to price the category between Save
     * and Post, for a student whose figures are staged and waiting.
     *
     * Reads only what THIS page staged (PENDING + RECORD_REVISED, via
     * stagedAcademicQuery()), so a row left behind by another page can never feed it.
     */
    /**
     * Split whatever the grader typed in "Apply Id / Enroll #" into BOTH keys.
     *
     * The box accepts either an enrollment no (F2025-0839) or an AAI id (101110), and the
     * page needs both no matter which was typed: every write is keyed on the AAI id
     * (SSD_APP_ID, SC_APP_ID, FFA_APP_ID …) while the enrollment no is what the fee
     * structure, the course tables and the SC_ENROLLMENT_NO fallbacks are keyed on.
     *
     * This replaces the `is_numeric($rawId) ? null : $rawId` idiom that used to sit at
     * five separate call sites. That idiom only ever DISCARDED a numeric value — it never
     * resolved it — so a student searched by AAI id was saved with a NULL
     * SSD_SAI_ENROLLMENT_NO / FOS_ENROLLMENT_NO despite having an enrollment number, and
     * every `OR SC_ENROLLMENT_NO = …` branch silently dropped out.
     *
     * CMS_SAI_ENROLLMENT_NO on the admission row is the bridge between the two, and is the
     * same column the enrollment↔id lookups elsewhere in this controller already use.
     * Either side may come back null — a new applicant genuinely has no enrollment no yet
     * — so callers must keep treating a null enrollment as "not enrolled", not as an error.
     *
     * @return array{aai_id: int|null, enrollment: string|null}
     */
    protected function resolveTypedIdentifier($typed): array
    {
        $typed = trim((string) $typed);

        if ($typed === '') {
            return ['aai_id' => null, 'enrollment' => null];
        }

        // Digits only means an AAI id; anything carrying a letter or a hyphen is an
        // enrollment no. Same rule as checkEnrollmentORaai_id() in the frozen controller.
        if (ctype_digit($typed)) {
            $enrollment = DB::table('admission_apply_info')
                ->where('AAI_ID', $typed)
                ->value('CMS_SAI_ENROLLMENT_NO');

            $enrollment = trim((string) $enrollment);

            return [
                'aai_id'     => (int) $typed,
                'enrollment' => $enrollment !== '' ? strtoupper($enrollment) : null,
            ];
        }

        $enrollment = strtoupper($typed);

        $aaiId = DB::table('admission_apply_info')
            ->whereRaw('UPPER(TRIM(CMS_SAI_ENROLLMENT_NO)) = ?', [$enrollment])
            ->value('AAI_ID');

        return [
            'aai_id'     => !empty($aaiId) ? (int) $aaiId : null,
            'enrollment' => $enrollment,
        ];
    }

    protected function stagedAdditionalCreditHours($session, $studentId)
    {
        if (empty($session) || empty($studentId)) {
            return 0.0;
        }

        // The caller has an enrollment number or an AAI id; the snapshot is keyed on the
        // AAI id, so resolve one from the other when needed.
        $aaiId = is_numeric($studentId)
            ? $studentId
            : DB::table('admission_apply_info')
                ->whereRaw('UPPER(TRIM(CMS_SAI_ENROLLMENT_NO)) = ?', [strtoupper(trim((string) $studentId))])
                ->value('AAI_ID');

        if (empty($aaiId)
            || !$this->tableColumnExists('Students_Scholarships_Details', 'SSD_CREDIT_HOURS')) {
            return 0.0;
        }

        $hours = $this->stagedAcademicQuery($aaiId, $session)
            ->whereRaw("NVL(ssd.SSD_CHALLAN_CATEGORY, 'Full_semester') = ?", ['additional_Full_semester'])
            ->whereRaw('NVL(ssd.SSD_CREDIT_HOURS, 0) > 0')
            ->orderByDesc('ssd.SSD_ID')
            ->value('ssd.SSD_CREDIT_HOURS');

        return (float) $hours;
    }

    /**
     * Every challan category this Save touches — academic categories in the batch, plus
     * Hostel_Fee when a hostel row is present. These are the challans Save retires.
     *
     * Dormant categories are added too when the student still holds a challan for one:
     * a student who is now a credit-hour case must not be left holding a Full Semester
     * challan alongside the Credit Hr one. Those are cancelled and NOT replaced — Post
     * skips dormant categories — which is why runSave() reports them separately.
     */
    private function categoriesBeingSaved(array $academicRows, array $hostelRows, array $dormant = [])
    {
        $categories = [];
        foreach (array_keys($this->academicCategories()) as $cat) {
            if ($this->categoryIsInBatch($academicRows, $cat) || in_array($cat, $dormant, true)) {
                $categories[] = $cat;
            }
        }
        if (!empty($hostelRows)) {
            $categories[] = 'Hostel_Fee';
        }

        return $categories;
    }

    /**
     * Academic categories that no longer apply to this student.
     *
     * A student billed Full Semester on 12 credit hours who drops to 9 becomes a
     * credit-hour case: the Credit Hour challan page cancels their Full Semester challan
     * and issues a Credit Hours one. Their saved Full Semester scholarship rows stay on
     * record, but they can no longer be edited, saved, previewed or posted — otherwise the
     * combination rule refuses every subsequent save with
     * '"Credit Hr" and "Full Semester" cannot be applied together', which the grader has
     * no way to resolve.
     *
     * The ONE place the question is asked, so getFeeMatrix(), runSave(), getStaged() and
     * postSnapshot() can never disagree about which rows are live.
     *
     * New admissions have no enrollment number and no course rows, so nothing is dormant
     * for them — resolve() returns not-a-case on a zero credit count.
     *
     * @return array{categories:array<string>, message:string|null}
     */
    private function dormantCategoryInfo($session, $enrollment, $progId = null)
    {
        $empty = ['categories' => [], 'message' => null];

        if (empty($session) || empty($enrollment)) {
            return $empty;
        }

        $degreeLevel = !empty($progId)
            ? DB::table('programs')->where('prog_id', $progId)->value('PROG_DEGREE_LEVEL')
            : null;

        $case = (new CreditHourCaseResolver())->resolve($session, $enrollment, $degreeLevel);

        if (empty($case['isCreditHourCase'])) {
            return $empty;
        }

        return ['categories' => $case['supersedes'], 'message' => $case['message']];
    }

    /** Just the category keys — the common case. */
    private function dormantCategories($session, $enrollment, $progId = null)
    {
        $info = $this->dormantCategoryInfo($session, $enrollment, $progId);

        return $info['categories'];
    }

    /**
     * The student's programme, needed for the degree level the dormancy ladder keys on.
     * Enrolled students only; a new admission has no course enrollment to judge anyway.
     */
    private function programIdForEnrollment($enrollment)
    {
        if (empty($enrollment)) {
            return null;
        }

        return DB::table('students_apply_info')
            ->whereRaw('LOWER(SAI_ENROLLMENT_NO) = ?', [strtolower(trim((string) $enrollment))])
            ->value('SAI_SDPO_PROG_ID');
    }

    /**
     * @var array<string, array<string>> memo for dormantCategoriesForStudent(). Save,
     *      preview and Post can each ask more than once in a request, and the answer
     *      cannot change within one.
     */
    private $dormantCache = [];

    /**
     * Dormant categories when all that is to hand is the AAI id.
     *
     * getStaged() and postSnapshot() are keyed on the application id, but the dormancy
     * ladder counts course enrollments, which are keyed on the enrollment number. A
     * student with no enrollment number is a new admission with no courses, so nothing is
     * dormant for them.
     */
    private function dormantCategoriesForStudent($aai_id, $sessions)
    {
        $key = $aai_id . '|' . $sessions;
        if (array_key_exists($key, $this->dormantCache)) {
            return $this->dormantCache[$key];
        }

        $enrollment = DB::table('admission_apply_info')
            ->where('AAI_ID', $aai_id)
            ->value('CMS_SAI_ENROLLMENT_NO');

        if (empty($enrollment)) {
            return $this->dormantCache[$key] = [];
        }

        return $this->dormantCache[$key] = $this->dormantCategories(
            $sessions,
            $enrollment,
            $this->programIdForEnrollment($enrollment)
        );
    }

    /**
     * The hostel attributes the replacement challan has to inherit.
     *
     * fin_bulk_challan_cancellation copies the challan into FIN_STUDENT_CANCEL_CHALLAN
     * and then DELETEs it — but that table carries none of these columns, so once the
     * cancellation runs they are gone for good. Captured here, immediately before it.
     *
     * The utility flags matter: the hostel procedure hard-coded them to 'NO' on
     * regeneration, silently dropping charges the student was being billed for.
     */
    private function captureHostelChallanCarry($aai_id, $enrollment, $sessions)
    {
        $challan = $this->findLiveChallan($aai_id, $enrollment, $sessions, 'Hostel_Fee');
        if (empty($challan)) {
            return [];
        }

        $pick = function ($column) use ($challan) {
            return (isset($challan->{$column}) && $challan->{$column} !== '') ? $challan->{$column} : null;
        };

        return [
            'SSHD_HOSTEL_FEE_TYPE' => $pick('sc_hostel_fee_type'),
            'SSHD_HOSTEL_CATEGORY' => $pick('sc_hostel_category'),
            'SSHD_HOSTEL_DAYS'     => $pick('sc_hostel_days'),
            'SSHD_SESSION_TYPE'    => $pick('sc_session_type'),
            'SSHD_REFRIGERATOR'    => $pick('sc_refrigerator'),
            'SSHD_CONDITIONER'     => $pick('sc_conditioner'),
            'SSHD_ELECTRIC'        => $pick('sc_electric'),
        ];
    }

    /** Is this category represented in the batch being saved? */
    private function categoryIsInBatch(array $academicRows, $category)
    {
        foreach ($academicRows as $row) {
            if (isset($row['challan_category']) && $row['challan_category'] === $category) {
                return true;
            }
        }

        return false;
    }

    /**
     * The hostel challan's payable BEFORE surplus, or null when no hostel row is being
     * saved. Mirrors what the hostel persist loop writes to SSHD_TOTAL_PAYABLE:
     * the hostel fee less the total hostel discount, floored at 0.
     */
    private function hostelPayableForSurplus(array $hostelRows)
    {
        if (empty($hostelRows)) {
            return null;
        }

        $fee = 0.0;
        $disc = 0.0;
        foreach ($hostelRows as $row) {
            // Every row carries the same hostel fee; the discounts are per row.
            $fee = max($fee, (float) (isset($row['hostelfee']) ? $row['hostelfee'] : 0));
            $disc += (float) (isset($row['sch_amount']) ? $row['sch_amount'] : 0);
        }

        return round(max(0.0, $fee - min($disc, $fee)), 2);
    }

    /**
     * Surplus that staged-but-unposted rows in the sections this Save is NOT touching
     * have already frozen for themselves.
     *
     * The one thing a section-scoped Save cannot work out on its own. pendingSurplus()
     * reports the real fin_fee_adjustment balance, and nothing is spent until Post — so
     * two saves in a row each see the FULL balance and each freeze a share of it:
     *
     *   Save academic  -> pending 50,000, academic freezes 50,000, invoice 0
     *   Save hostel    -> pending STILL 50,000 (academic has not posted), hostel
     *                     freezes 50,000 too, invoice 0
     *   Post both      -> academic spends 50,000; hostel finds nothing left
     *
     * No money is lost — SurplusAdjuster::apply() caps every spend at what really exists
     * — but the hostel challan would print an invoice the grader never accepted, which is
     * exactly what the preview-and-accept gate exists to prevent. Subtracting the prior
     * claim here means each save only ever divides up surplus nobody else is holding.
     *
     * The figures describe the CHALLAN and repeat identically on every row of it, so they
     * are taken per category with MAX and never SUM()med across rows.
     */
    private function surplusClaimedByStagedSections($aai_id, $sessions, $skipAcademic, $skipHostel)
    {
        $claimed = 0.0;

        if ($skipAcademic
            && $this->tableColumnExists('Students_Scholarships_Details', 'SSD_SURPLUS_CONSUMED')) {
            $rows = $this->stagedAcademicQuery($aai_id, $sessions)
                ->selectRaw("NVL(ssd.SSD_CHALLAN_CATEGORY, 'Full_semester') as cat")
                ->selectRaw('MAX(NVL(ssd.SSD_SURPLUS_CONSUMED, 0)) as consumed')
                ->groupByRaw("NVL(ssd.SSD_CHALLAN_CATEGORY, 'Full_semester')")
                ->get();

            foreach ($rows as $row) {
                $claimed += (float) $row->consumed;
            }
        }

        if ($skipHostel
            && $this->tableColumnExists('fin_students_scholarships_hostel_details', 'SSHD_SURPLUS_CONSUMED')) {
            // Hostel stages exactly one challan, so one figure — no grouping needed.
            $claimed += (float) $this->stagedHostelQuery($aai_id, $sessions)
                ->selectRaw('MAX(NVL(sshd.SSHD_SURPLUS_CONSUMED, 0)) as consumed')
                ->value('consumed');
        }

        return round($claimed, 2);
    }

    /**
     * A category's total payable BEFORE surplus — the figure
     * FIN_PRO_ADJUST_CHALLAN_SURPLUS computed from the challan's own columns:
     *
     *   (tuition - scholarship) + (security + medical + admission + activity) - arrear
     *
     * Expressed here in the snapshot's own terms, which comes out identical because the
     * challan carries the ACTUAL fee and SC_SSD_ARREAR is the one-time discount. Floored
     * at 0, exactly as the procedure does before comparing against the surplus.
     */
    private function categoryPayable($fee, array $tuitionDisc, array $componentDisc, $category)
    {
        if (empty($fee) || empty($fee['ok'])) {
            return 0.0;
        }

        $base  = (float) $fee['amountBase'];
        $tDisc = min(isset($tuitionDisc[$category]) ? (float) $tuitionDisc[$category] : 0.0, $base);

        $disc = isset($componentDisc[$category])
            ? $componentDisc[$category]
            : ['admission' => 0.0, 'security' => 0.0, 'medical' => 0.0, 'activity' => 0.0];

        $oneTimeActual = 0.0;
        $oneTimeDisc   = 0.0;
        foreach ($fee['arrearComponents'] as $component => $actual) {
            $oneTimeActual += (float) $actual;
            $oneTimeDisc   += min((float) $disc[$component], (float) $actual);
        }

        return round(max(0.0, ($base - $tDisc) + ($oneTimeActual - $oneTimeDisc)), 2);
    }

    /**
     * The only academic challan-category sets a student may hold in one session.
     *
     * Full Semester + Additional Credit Hr and Short Summer + Additional Credit Hr
     * are the two legal pairs; everything else is a single category on its own.
     * Each entry is kept sorted so it can be compared directly.
     */
    private function allowedCategoryCombinations()
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

    /** Arrears grid component -> fin_other_scholarships column. */
    private function arrearsComponentColumns()
    {
        return [
            'admission' => 'fos_admission_fee',
            'security'  => 'fos_security_fee',
            'activity'  => 'fos_activity_charges',
            'medical'   => 'fos_medical_insurance',
        ];
    }

    private function arrearsComponentLabels()
    {
        return [
            'admission' => 'Admission Fee',
            'security'  => 'Security',
            'activity'  => 'Activity charges',
            'medical'   => 'Medical',
        ];
    }

    // ================================================================== //
    // View
    // ================================================================== //

    /**
     * The profile lookup for THIS page, accepting an AAI id as well as an enrollment no.
     *
     * StudentRecordController::getprofileMgt() branches on Student Type, never on the
     * shape of what was typed: for Existing and the three New Admission types it runs one
     * query, `students_apply_info WHERE sai_enrollment_no = UPPER(<typed>)`, which an AAI
     * id can never match. That is why searching by App Id returned "No record found" even
     * though the page prints that id beside the enrollment no.
     *
     * Rather than duplicate ~130 lines of profile SQL, this normalises the identifier and
     * hands the canonical one straight to that method. The frozen student_record page
     * keeps its own route and its own behaviour untouched, which is the point of the
     * separate endpoint — nothing about the legacy lookup changes.
     *
     * Enrolled types are looked up by enrollment no, New without Registration by AAI id,
     * exactly as before. When the bridge lookup finds nothing we pass the raw value
     * through, so an unrecognised entry still produces the same "not found" answer it
     * always did instead of a different error.
     */
    public function getStudentProfileRevised($roll = null, $studentType = null, $session = null, $sch_type = null)
    {
        $ids = $this->resolveTypedIdentifier($roll);

        $lookup = $this->isEnrolledStudentType($studentType)
            ? $ids['enrollment']
            : $ids['aai_id'];

        $response = app(StudentRecordController::class)
            ->getprofileMgt($lookup !== null ? $lookup : $roll, $studentType, $session, $sch_type);

        return $this->attachAcademicAmounts($response, $session);
    }

    /**
     * Put SSD_AMOUNT and SSD_ENTRY_MODE back onto the academic scholarship rows.
     *
     * getprofileMgt() returns academic rows with `scholarship_amount` set to a literal
     * NULL — its own comment says "Academic rows have no stored amount (it is computed
     * from the fee structure at display time)". That was true of the frozen page. It is
     * not true of this one, which has written SSD_AMOUNT on every row it saves since the
     * snapshot columns were added, and it is precisely why an exact award could not
     * survive a reload: the client had nothing but the percentage to rebuild from, so
     * 200,000 came back as 200,000.14 and the next save stored the drift.
     *
     * Done HERE rather than in getprofileMgt() so the frozen page keeps the exact payload
     * it has always had — StudentRecordController is not touched by any of this.
     *
     * Also reports a page-level `entry_mode`, taken from the most recently saved row of
     * the student, which is what the radio should open on. The per-row mode still governs
     * how each row is read back; this is only the starting position for the NEXT save.
     */
    private function attachAcademicAmounts($response, $session)
    {
        if (!($response instanceof \Illuminate\Http\JsonResponse) || empty($session)) {
            return $response;
        }

        $payload = $response->getData(true);
        if (!is_array($payload) || empty($payload['scholarships']) || !is_array($payload['scholarships'])) {
            return $response;
        }

        // Only the academic rows need this: hostel already returns sshd_amount, and
        // arrears never had a percentage to be rebuilt from in the first place.
        $rowIds = [];
        foreach ($payload['scholarships'] as $row) {
            if (strtoupper((string) (isset($row['scholarship_type']) ? $row['scholarship_type'] : '')) === 'ACADEMIC'
                && !empty($row['row_id'])) {
                $rowIds[] = (int) $row['row_id'];
            }
        }

        if (empty($rowIds)) {
            return $response;
        }

        $hasAmount = $this->tableColumnExists('Students_Scholarships_Details', 'SSD_AMOUNT');
        $hasMode   = $this->tableColumnExists('Students_Scholarships_Details', 'SSD_ENTRY_MODE');

        if (!$hasAmount && !$hasMode) {
            return $response;   // DDL not run — leave the payload exactly as it was
        }

        $columns = ['SSD_ID'];
        if ($hasAmount) { $columns[] = 'SSD_AMOUNT'; }
        if ($hasMode)   { $columns[] = 'SSD_ENTRY_MODE'; }

        $saved = DB::table('Students_Scholarships_Details')
            ->select($columns)
            ->whereIn('SSD_ID', $rowIds)
            ->get()
            ->keyBy(function ($r) {
                return (int) (isset($r->ssd_id) ? $r->ssd_id : 0);
            });

        foreach ($payload['scholarships'] as $i => $row) {
            if (strtoupper((string) (isset($row['scholarship_type']) ? $row['scholarship_type'] : '')) !== 'ACADEMIC') {
                continue;
            }
            $stored = $saved->get((int) (isset($row['row_id']) ? $row['row_id'] : 0));
            if (empty($stored)) {
                continue;
            }

            $mode = strtoupper(trim((string) (isset($stored->ssd_entry_mode) ? $stored->ssd_entry_mode : '')));
            $mode = $mode === $this->entryModeAmount() ? $this->entryModeAmount() : $this->entryModePercent();

            $payload['scholarships'][$i]['scholarship_entry_mode'] = $mode;

            // The stored amount is offered ONLY for a row that was entered as one. A
            // percentage-mode row keeps scholarship_amount = null so the client re-prices
            // it against this session's fee structure exactly as it does today — which is
            // deliberate: if the Master Fee Structure is corrected, those rows should move.
            if ($mode === $this->entryModeAmount() && isset($stored->ssd_amount) && (float) $stored->ssd_amount > 0) {
                $payload['scholarships'][$i]['scholarship_amount'] = (string) (float) $stored->ssd_amount;
            }
        }

        $payload['entry_mode'] = $this->lastUsedEntryMode($rowIds);

        $response->setData($payload);

        return $response;
    }

    /**
     * The mode the radio should open on: whichever the student's most recently saved
     * academic row used. Falls back to PERCENT, which is also what every row written
     * before this column existed reports.
     */
    private function lastUsedEntryMode(array $rowIds)
    {
        if (empty($rowIds)
            || !$this->tableColumnExists('Students_Scholarships_Details', 'SSD_ENTRY_MODE')) {
            return $this->entryModePercent();
        }

        $q = DB::table('Students_Scholarships_Details')
            ->select('SSD_ENTRY_MODE')
            ->whereIn('SSD_ID', $rowIds)
            ->whereNotNull('SSD_ENTRY_MODE');

        // SSD_SAVED_AT is itself one of the columns that may not exist yet; without it the
        // id order is the best available stand-in for "most recent".
        $q = $this->tableColumnExists('Students_Scholarships_Details', 'SSD_SAVED_AT')
            ? $q->orderByDesc('SSD_SAVED_AT')->orderByDesc('SSD_ID')
            : $q->orderByDesc('SSD_ID');

        $mode = strtoupper(trim((string) $q->value('SSD_ENTRY_MODE')));

        return $mode === $this->entryModeAmount() ? $this->entryModeAmount() : $this->entryModePercent();
    }

    /**
     * Same view data as StudentRecordController::getRecord(), rendered into the
     * revised blade.
     */
    public function getRecord()
    {
        $sql = "SELECT
                ssd_id,
                ssd_sai_enrollment_no,
                ssd_type,
                ssd_percentage,
                ssd_details,
                sess_name,
                scholarship_name,
                spi_name,
                prog_name,
                school_name
                FROM students_scholarships_details
                left join SCHOLARSHIP_TYPE on scholarship_id = ssd_type
                left join academic_sessions on sess_id = ssd_sess_id
                left join students_apply_info on sai_enrollment_no = ssd_sai_enrollment_no
                left join students_personal_info on sai_spi_id = spi_id
                left join programs on sai_sdpo_prog_id = prog_id
                left join schools on sai_sdpo_school_id = school_id
                where 1=1
                ORDER BY ssd_id DESC";
        $rec = DB::select($sql);

        $recScholarship_Type = DB::select("select * from Scholarship_Type where scholarship_status = 'E'");
        $recsqlAcademicSessions = DB::select("select * from academic_sessions order by sess_id desc");

        return view('grader.StudentRecord.StudentRecordRevised', [
            'studentRecord'    => $rec,
            'Scholarship_Type' => $recScholarship_Type,
            'academic_session' => $recsqlAcademicSessions,
            // Tells the preview URL to ask chalanfinPreview.php for the Admission-office
            // date rule. Resolved here, not in the blade, so the one list of usernames
            // stays the only place that decides it.
            'srrAdmissionDates' => $this->usesAdmissionInstallmentDates(),
        ]);
    }

    // ================================================================== //
    // Fee basis — the SINGLE source of truth
    // ================================================================== //

    /**
     * The student's live challan for a category — the very row Post updates.
     * Same matching rules as resolveScholarshipChallanExecutionContext().
     */
    private function findLiveChallan($aai_id, $enrollment, $sessions, $category)
    {
        $q = DB::table('student_challan')
            ->where('SC_SESSION_ID', $sessions)
            ->where(function ($w) use ($aai_id, $enrollment) {
                $w->where('SC_APP_ID', $aai_id);
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

    /*
     * NOTE ON THE FEE BASIS.
     *
     * fin_fee_structure is the source; student_challan is the OUTPUT. The grid and
     * the snapshot are priced from the fee structure, and Post writes those figures
     * into the challan. (An earlier revision priced off the live challan instead —
     * that was reverted, because with Post writing the challan the dependency would
     * be circular.)
     */

    // ================================================================== //
    // Reads
    // ================================================================== //

    /**
     * Resolve every academic challan category in ONE request.
     *
     * The grid shows all four rows at once, so four separate calls would mean four
     * round trips and four error toasts on load. A category that cannot be resolved
     * is NOT an error for the page: its entry carries ok=false plus the same message
     * the old page would have shown, and the grid renders that row disabled.
     */
    public function getFeeMatrix(Request $request, $session = null, $student_id = null)
    {
        $studentType = $request->query('student_type');

        if (empty($session) || empty($student_id)) {
            return response()->json([
                'status'     => false,
                'message'    => 'Session and student are required.',
                'categories' => (object) [],
            ]);
        }

        // Resolved once, up front: the box takes an enrollment no OR an AAI id, and
        // everything below needs the enrollment no. Shape-testing it here — which is what
        // this used to do — silently skipped the dormancy check and handed the fee
        // resolver an AAI id it would look up as an enrollment no and never find.
        $enrollment = $this->resolveTypedIdentifier($student_id)['enrollment'];

        // A new applicant has no enrollment no at all; the AAI id is then the right basis,
        // which is the same fallback runSave() makes.
        $feeBasis = $enrollment !== null ? $enrollment : $student_id;

        // Categories the student has outgrown. Reported alongside `ok` rather than folded
        // into it: the fee structure still resolves perfectly well, so the saved
        // percentages keep showing real amounts instead of collapsing to zero. The grid
        // renders these read-only and never submits them.
        $dormantInfo = empty($enrollment)
            ? ['categories' => [], 'message' => null]
            : $this->dormantCategoryInfo($session, $enrollment, $this->programIdForEnrollment($enrollment));

        $categories = [];
        foreach (array_keys($this->academicCategories()) as $category) {
            $res = $this->resolveAcademicFeeStructure($session, $feeBasis, $studentType, $category);

            $isDormant = in_array($category, $dormantInfo['categories'], true);

            if (empty($res['ok'])) {
                $categories[$category] = [
                    'ok'                => false,
                    'dormant'           => $isDormant,
                    'dormantMessage'    => $isDormant ? $dormantInfo['message'] : null,
                    'reason'            => isset($res['reason']) ? $res['reason'] : null,
                    'switchTo'          => isset($res['switchTo']) ? $res['switchTo'] : null,
                    'message'           => isset($res['message']) ? $res['message'] : 'Fee structure could not be resolved.',
                    'referenceFee'      => null,
                    'referenceLabel'    => 'Tuition Fee',
                    'amountBase'        => 0,
                    'feeLineTotal'      => 0,
                    'creditHours'       => null,
                    'repeatCredits'     => 0,
                    'repeatFee'         => 0,
                    'repeatCourses'     => [],
                    'oneTimeFeeTotal'   => 0,
                    'arrearsApplicable' => false,
                    'arrearComponents'  => ['admission' => 0, 'security' => 0, 'activity' => 0, 'medical' => 0],
                ];
                continue;
            }

            $categories[$category] = [
                'ok'                => true,
                'dormant'           => $isDormant,
                'dormantMessage'    => $isDormant ? $dormantInfo['message'] : null,
                'reason'            => null,
                'switchTo'          => null,
                'message'           => '1',
                'referenceFee'      => $res['referenceFee'],
                'referenceLabel'    => $res['referenceLabel'],
                'amountBase'        => $res['amountBase'],
                'feeLineTotal'      => $res['feeLineTotal'],
                'creditHours'       => $res['creditHours'],
                'repeatCredits'     => isset($res['repeatCredits']) ? $res['repeatCredits'] : 0,
                // The repeat surcharge and the courses behind it. Each repeated course is
                // priced at its OFFERING school's rate, which is a figure the grader cannot
                // look up anywhere else on this page — so the grid itemises it.
                'repeatFee'         => isset($res['repeatFee']) ? $res['repeatFee'] : 0,
                'repeatCourses'     => isset($res['repeatCourses']) ? $res['repeatCourses'] : [],
                'oneTimeFeeTotal'   => $res['oneTimeFeeTotal'],
                'arrearsApplicable' => $res['arrearsApplicable'],
                'arrearComponents'  => $res['arrearComponents'],
                // Summer only: courses whose offering school has no
                // Short/Summer_Course fee structure. They are charged NOTHING, so the
                // tuition figure above is short by their fee — say so while the grader
                // can still get the fee structure defined.
                'unpricedCourses'   => isset($res['unpricedCourses']) ? $res['unpricedCourses'] : [],
            ];
        }

        // The student's spendable surplus, shown on the profile card so the grader
        // knows before entering anything that this challan will be reduced. Rides on
        // this call because it already fires on every search — no extra round trip.
        $aaiId = $request->query('aai_id');
        $surplus = null;
        $liveChallans = [];
        $installmentDates = null;
        if (!empty($aaiId)) {
            $surplus = (new SurplusAdjuster())->pendingSurplus($aaiId, $enrollment);

            // Which challans Save is going to CANCEL. The page asks for consent before
            // sending, because between Save and Post the student has none at all.
            foreach (array_merge(array_keys($this->academicCategories()), ['Hostel_Fee']) as $category) {
                if (!empty($this->findLiveChallan($aaiId, $enrollment, $session, $category))) {
                    $liveChallans[] = $category;
                }
            }

            // Whether the preview will find installment dates or fall back to today+7.
            // Rides on this call for the same reason the surplus does: it already fires
            // on every search, so Save & Preview can gate on it without a round trip.
            // Never allowed to break the fee matrix — a null here means "do not block".
            try {
                $installmentDates = $this->previewDatesForStudent($session, $student_id, $aaiId, $studentType);
            } catch (\Throwable $e) {
                $installmentDates = null;
            }
        }

        return response()->json([
            'status'            => true,
            'message'           => '1',
            'categories'        => $categories,
            'surplus'           => $surplus,
            'liveChallans'      => $liveChallans,
            'installment_dates' => $installmentDates,
        ]);
    }

    /**
     * Previous-session arrears, for the "Import Previous Scholarships" button.
     * Staged in the grid only — nothing is written until Save & Post.
     */
    public function getPreviousArrears(Request $request, $session = null, $student_id = null)
    {
        $aaiId = $request->query('aai_id');

        if (empty($session) || (empty($aaiId) && empty($student_id))) {
            return response()->json(['status' => false, 'message' => 'Session and student are required.', 'rows' => []]);
        }

        // Must resolve to the SAME session the profile card used for the scholarship
        // list, or the imported arrears would come from a different term. Summer is
        // skipped in both — see ResolvesCarryForwardSession.
        $prevSession = $this->resolveCarryForwardSession($session);

        if (empty($prevSession)) {
            return response()->json(['status' => true, 'message' => 'No previous session.', 'rows' => [], 'prev_session_name' => null]);
        }

        $enrollment = $this->resolveTypedIdentifier($student_id)['enrollment'];

        $rows = $this->getArrearsRowsForStudent($aaiId, $prevSession->sess_id, $enrollment);

        // A previous-session row tagged against a category that cannot carry arrears —
        // Additional Credit Hr above all — must not be offered for import. Save would
        // refuse it anyway, but the grid should never show a waiver it cannot accept.
        $rows = array_values(array_filter($rows, function ($row) {
            $cat = isset($row->fos_challan_category) && $row->fos_challan_category !== ''
                ? $row->fos_challan_category
                : $this->defaultChallanCategory();

            return $this->challanCategoryHasArrears($cat);
        }));

        return response()->json([
            'status'            => true,
            'message'           => '1',
            'prev_session_id'   => $prevSession->sess_id,
            'prev_session_name' => $prevSession->sess_name,
            'rows'              => $rows,
        ]);
    }

    // ================================================================== //
    // Save & Post
    // ================================================================== //

    /** Tags rows staged by this page so the Bulk Post queue can filter them. */
    private function sourcePage()
    {
        return 'RECORD_REVISED';
    }

    /**
     * Which side of the percentage/amount pair the grader actually typed.
     *
     * AMOUNT  — the money is what was awarded; the percentage is DERIVED and is a display
     *           value only. Nothing may recompute the amount from it, ever.
     * PERCENT — the historical behaviour: the percentage is what was awarded and the
     *           amount is derived from the fee structure.
     *
     * Why this exists: a donor scholarship of exactly Rs 200,000 against a tuition base of
     * 292,000 is 68.49315068493151%. The page stored that percentage rounded, and every
     * save and every reload rebuilt the amount as round(pct / 100 * base, 2) — which comes
     * back as 200,000.14. Fourteen paisa the donor never authorised, and re-saving kept the
     * drifted figure. Adding decimal places only shrinks that error; it cannot remove it,
     * because the reconstruction is lossy by construction. Recording WHICH side was typed
     * is what removes it.
     *
     * Stored per ROW, not per page, and read back per row. A save may cover only one
     * section (see $scope below), so one student can legitimately hold academic rows
     * entered as amounts beside hostel rows entered as percentages. Judging the reload
     * per row is what keeps each of them exact.
     *
     * NULL / anything unrecognised means PERCENT. That is deliberate: every row written
     * before this column existed reads as PERCENT and keeps its behaviour byte for byte,
     * so no backfill is needed and nothing already on record moves.
     */
    private function entryModeAmount()
    {
        return 'AMOUNT';
    }

    private function entryModePercent()
    {
        return 'PERCENT';
    }

    /** The page's entry mode for this request, normalised. Unknown values fall back to PERCENT. */
    private function resolveEntryMode(Request $request)
    {
        $mode = strtoupper(trim((string) $request->input('entry_mode')));

        return $mode === $this->entryModeAmount()
            ? $this->entryModeAmount()
            : $this->entryModePercent();
    }

    /** Value of SSD_BULK_POST_STATUS / SSHD_BULK_POST_STATUS while staged. */
    private function pendingStatus()
    {
        return 'PENDING';
    }

    /**
     * Terminal status for a staged row whose category the student has outgrown.
     *
     * Deliberately NOT 'POSTED': these rows were never posted and must not read as though
     * they were. They are simply taken out of the staging queue so a challan the student
     * is no longer due can never be previewed or issued from them.
     */
    private function supersededStatus()
    {
        return 'SUPERSEDED';
    }

    /**
     * Take dormant-category rows out of the staging queue.
     *
     * Only the staging flag moves. SSD_PERCENTAGE, SSD_AMOUNT and every snapshot column
     * are left exactly as they are — the scholarship stays on record, it just stops being
     * something Post could act on.
     */
    private function retireDormantStagedRows($aai_id, $sessions, array $dormantCategories)
    {
        if (empty($dormantCategories)
            || !$this->tableColumnExists('Students_Scholarships_Details', 'SSD_BULK_POST_STATUS')) {
            return 0;
        }

        // whereRaw rather than whereIn: the column is an NVL expression, and the category
        // list is built here from a fixed set, never from request input.
        $placeholders = implode(',', array_fill(0, count($dormantCategories), '?'));

        $q = DB::table('Students_Scholarships_Details')
            ->where('SSD_APP_ID', $aai_id)
            ->where('SSD_SESS_ID', $sessions)
            ->whereRaw("UPPER(TRIM(NVL(SSD_BULK_POST_STATUS, ''))) = ?", [$this->pendingStatus()])
            ->whereRaw("NVL(SSD_CHALLAN_CATEGORY, 'Full_semester') IN ($placeholders)", array_values($dormantCategories));

        if ($this->tableColumnExists('Students_Scholarships_Details', 'SSD_SOURCE_PAGE')) {
            $q->whereRaw("UPPER(TRIM(NVL(SSD_SOURCE_PAGE, ''))) = ?", [strtoupper($this->sourcePage())]);
        }

        return $q->update($this->filterExistingColumns('Students_Scholarships_Details', [
            'SSD_BULK_POST_STATUS' => $this->supersededStatus(),
            'UPDATE_DATETIME'      => now()->toDateTimeString(),
        ]));
    }

    /**
     * STEP 1 — Save.
     *
     * Writes a static picture of the whole page into the two intermediate tables:
     * the entered values AND the fee-structure figures behind them, so the amounts
     * the grader acknowledges survive a later edit to the Master Fee Structure.
     * Nothing touches student_challan here.
     */
    public function saveSnapshot(Request $request)
    {
        return $this->runSave($request);
    }

    /**
     * RETIRED — the original combined endpoint.
     *
     * It saved and posted in one shot, which is exactly the bypass the
     * Save & Preview -> Accept -> Post flow exists to prevent. It stays registered so
     * an old cached page gets a clear message instead of a 404, but it no longer
     * writes anything.
     */
    public function saveAndPost(Request $request)
    {
        return response()->json([
            'success' => false,
            'messege' => 'This page now requires the challan preview to be accepted before posting. '
                . 'Please reload the page and use Save & Preview, then Accept, then Post.',
        ], 410);
    }

    /**
     * Save the whole grid into the scholarship tables. Never touches student_challan.
     *
     * Order matters — the arrears figures are derived BEFORE the cap checks,
     * because they are both the numbers that get capped and the numbers written
     * into SSD_ARREAR.
     */
    private function runSave(Request $request)
    {
        $sessions     = $request->input('academic_session');
        $aai_id       = $request->input('sch_aai_id');
        $enrollmentNo = $request->input('sch_enrollment_no');   // enrollment no OR aai id (raw)
        $studentType  = $request->input('student_type');

        $academicRows = $request->input('academic');
        $hostelRows   = $request->input('hostel');
        $arrearsRows  = $request->input('arrears');
        $deletedRows  = $request->input('deleted_rows');
        $arrearsCategory = trim((string) $request->input('arrears_challan_category'));

        // Which side of each pair the grader typed — see resolveEntryMode(). Read once
        // here and threaded through every persist below, so the figure that gets stored
        // and the flag that says how to read it back can never disagree.
        $entryMode  = $this->resolveEntryMode($request);
        $amountMode = ($entryMode === $this->entryModeAmount());

        // The grader's consent to retire the live challan. Absent means "no live challan
        // to retire" — the client only omits it when findLiveChallan found nothing, and
        // cancelWhere is a no-op in that case anyway.
        $cancelExisting = filter_var($request->input('cancel_existing', true), FILTER_VALIDATE_BOOLEAN);

        $academicRows = is_array($academicRows) ? $academicRows : [];
        $hostelRows   = is_array($hostelRows) ? $hostelRows : [];
        $arrearsRows  = is_array($arrearsRows) ? $arrearsRows : [];
        $deletedRows  = is_array($deletedRows) ? $deletedRows : [];

        // ---------------------------------------------------------------- //
        // Which SECTIONS this save covers.
        //
        // A grader who changed only the hostel figures must not have their academic
        // challan cancelled and reissued as a side effect — between Save and Post the
        // student would be left without a document nobody asked to touch.
        //
        // Same vocabulary as postSnapshot(): an absent list means "both", so an older
        // cached page keeps working exactly as it did.
        //
        // Everything downstream — the bucketing, the caps, categoriesBeingSaved() and so
        // the cancellation, the surplus walk, both persist loops, and therefore what
        // getStaged() later reports as staged — reads these four arrays. Emptying the
        // out-of-scope ones here is what narrows all of it, in one place.
        // ---------------------------------------------------------------- //
        $scope = $request->input('save_scope');
        $scope = is_array($scope) ? array_map('strtolower', $scope) : ['academic', 'hostel'];
        $scope = array_values(array_intersect($scope, ['academic', 'hostel']));

        if (empty($scope)) {
            return response()->json([
                'success' => false,
                'messege' => 'Tick at least one section to save.',
            ], 400);
        }

        $doAcademic = in_array('academic', $scope, true);
        $doHostel   = in_array('hostel', $scope, true);

        // Arrears belong to the academic challan — they discount its one-time fee lines —
        // so they travel with it. An empty $arrearsCategory leaves
        // fin_other_scholarships completely alone.
        if (!$doAcademic) {
            $academicRows    = [];
            $arrearsRows     = [];
            $arrearsCategory = '';
        }
        if (!$doHostel) {
            $hostelRows = [];
        }

        $deletedRows = array_values(array_filter($deletedRows, function ($d) use ($doAcademic, $doHostel) {
            $isHostel = strtolower(isset($d['category']) ? $d['category'] : '') === 'hostel';

            return $isHostel ? $doHostel : $doAcademic;
        }));

        $enrollment = $this->resolveTypedIdentifier($enrollmentNo)['enrollment'];

        if (empty($sessions) || empty($aai_id)) {
            return response()->json(['success' => false, 'messege' => 'Session and student are required.'], 400);
        }
        if (empty($academicRows) && empty($hostelRows) && empty($deletedRows) && $arrearsCategory === '') {
            return response()->json(['success' => false, 'messege' => 'No scholarships to save.'], 400);
        }

        // Save writes only the scholarship tables, so this transaction is genuinely
        // atomic — no Oracle procedure runs here.
        DB::beginTransaction();
        try {
            // ---------------------------------------------------------- //
            // 1. What the student already has (legacy NULL category = Full_semester)
            // ---------------------------------------------------------- //
            $existingAcademic = DB::table('Students_Scholarships_Details')
                ->select(
                    'SSD_ID',
                    'SSD_TYPE',
                    'SSD_PERCENTAGE',
                    'SSD_APP_ID',
                    'SSD_SAI_ENROLLMENT_NO',
                    'SSD_SESS_ID',
                    DB::raw("NVL(SSD_CHALLAN_CATEGORY, 'Full_semester') as ssd_challan_category")
                )
                ->where('SSD_APP_ID', $aai_id)
                ->where('SSD_SESS_ID', $sessions)
                ->where('STATUS', 'E')
                ->get();

            $academicDeleteIds = [];
            $hostelDeleteIds   = [];
            foreach ($deletedRows as $d) {
                $id = isset($d['id']) ? $d['id'] : null;
                if (empty($id)) {
                    continue;
                }
                if (strtolower(isset($d['category']) ? $d['category'] : '') === 'hostel') {
                    $hostelDeleteIds[] = $id;
                } else {
                    $academicDeleteIds[] = $id;
                }
            }

            // Categories the student has outgrown — see dormantCategoryInfo(). Their saved
            // rows stay exactly as they are, but they must not be written to and must not
            // count towards the combination rule, or a student who moved from Full Semester
            // to Credit Hr could never save again.
            $dormantCategories = $this->dormantCategories(
                $sessions,
                $enrollment,
                $this->programIdForEnrollment($enrollment)
            );

            // Categories that are LOCKED (their challan already carries a scholarship
            // amount). They cannot be deleted, so the combination rule must treat them
            // as pinned — otherwise a student who already holds an illegal pair from
            // the old page could never save anything again.
            $pinnedCategories = [];
            foreach ($existingAcademic as $row) {
                $cat = $row->ssd_challan_category;
                if (isset($pinnedCategories[$cat])) {
                    continue;
                }
                if ($this->isScholarshipRowLockedByChallan($cat, $row->ssd_app_id, $row->ssd_sai_enrollment_no, $row->ssd_sess_id)) {
                    $pinnedCategories[$cat] = true;
                }
            }

            // ---------------------------------------------------------- //
            // 2. Bucket the batch
            // ---------------------------------------------------------- //
            $academicUpdateIds    = [];
            $academicCategories   = [];   // distinct categories needing a procedure run
            $academicCategorySums = [];   // category => batch % sum
            // Amount mode's twin of the above. Kept separate rather than overloading the
            // % sum, because the two are capped against different things: 100, versus the
            // category's own fee base. The base is not resolved until $feeFor exists
            // further down, so the cap itself waits for step 5.
            $academicCategoryAmountSums = [];
            $hostelUpdateIds      = [];
            $hostelBatchSum       = 0.0;

            foreach ($academicRows as $index => $row) {
                $cat = isset($row['challan_category']) ? trim((string) $row['challan_category']) : 'Full_semester';
                if (!array_key_exists($cat, $this->academicCategories())) {
                    throw new \Exception('Unknown challan category "' . $cat . '".');
                }
                if (in_array($cat, $dormantCategories, true)) {
                    // The grid disables these, but a disabled attribute is not a safety
                    // mechanism — this is the guard that actually holds.
                    throw new \Exception('"' . $this->academicCategories()[$cat] . '" no longer applies to this student: '
                        . 'they are billed on a Credit Hr challan for this session. The rows already saved against it '
                        . 'are kept on record, but nothing further can be saved there.');
                }
                if (empty($row['sch_type'])) {
                    throw new \Exception('A scholarship type is required for every checked row (' . $this->academicCategories()[$cat] . ').');
                }
                $pct = (float) (isset($row['sch_percentage']) ? $row['sch_percentage'] : 0);
                if ($pct < 0 || $pct > 100) {
                    throw new \Exception('Percentage must be between 0 and 100 (' . $this->academicCategories()[$cat] . ').');
                }

                // In amount mode the money is what was awarded, so it is validated on its
                // own terms. The upper bound is the category's fee base and is checked at
                // step 5, where $feeFor has resolved it; only the sign can be judged here.
                if ($amountMode) {
                    $amt = (float) (isset($row['sch_amount']) ? $row['sch_amount'] : 0);
                    if ($amt < 0) {
                        throw new \Exception('Amount cannot be negative (' . $this->academicCategories()[$cat] . ').');
                    }
                    // Whole rupees. The grid refuses a decimal point, so anything arriving
                    // here with paise came from somewhere else — refuse it rather than
                    // rounding, because silently adjusting a donor's figure by even one
                    // paisa is the exact failure this entry mode exists to prevent.
                    if (abs($amt - round($amt)) > 0.0001) {
                        throw new \Exception('Amount must be a whole rupee figure ('
                            . $this->academicCategories()[$cat] . ' received ' . $amt . ').');
                    }
                    $academicCategoryAmountSums[$cat] =
                        (isset($academicCategoryAmountSums[$cat]) ? $academicCategoryAmountSums[$cat] : 0) + $amt;
                }

                $academicRows[$index]['challan_category'] = $cat;
                $academicCategories[$cat]   = true;
                $academicCategorySums[$cat] = (isset($academicCategorySums[$cat]) ? $academicCategorySums[$cat] : 0) + $pct;
                if (!empty($row['id'])) {
                    $academicUpdateIds[] = $row['id'];
                }
            }

            foreach ($hostelRows as $index => $row) {
                if (empty($row['sch_type'])) {
                    throw new \Exception('A scholarship type is required for every checked hostel row.');
                }
                $hostelFee = (float) (isset($row['hostelfee']) ? $row['hostelfee'] : 0);
                $amount    = (float) (isset($row['sch_amount']) ? $row['sch_amount'] : 0);
                if ($hostelFee <= 0) {
                    throw new \Exception('Hostel fee could not be resolved — hostel scholarship not allowed.');
                }
                // Hostel has always been amount-canonical — SSHD_PERCENTAGE is NUMBER(5,2)
                // and physically cannot hold a precise one — so amount mode is simply what
                // this grid already did: recompute the percentage server-side rather than
                // trusting whatever the browser sent.
                //
                // Percentage mode reverses it, so the grid obeys the same page-wide radio
                // as the other two. The amount is then the derived side and is written back
                // into the row, because everything below (the surplus payable, the persist
                // loop, SSHD_HOSTEL_DISC_AMT) reads sch_amount.
                if ($amountMode) {
                    // Whole rupees, as everywhere the grader types money. The hostel Amount
                    // cell has always carried srr-int, so this only catches a value that
                    // did not come from the grid.
                    if (abs($amount - round($amount)) > 0.0001) {
                        throw new \Exception('Hostel amount must be a whole rupee figure (received ' . $amount . ').');
                    }
                    $pct = round(($amount * 100) / $hostelFee, 2);
                } else {
                    $pct    = (float) (isset($row['sch_percentage']) ? $row['sch_percentage'] : 0);
                    if ($pct < 0 || $pct > 100) {
                        throw new \Exception('Hostel percentage must be between 0 and 100.');
                    }
                    // Rounded to whole rupees, NOT to 2 dp: the hostel challan is billed in
                    // whole rupees and onHostelBlur() derives it the same way, so rounding
                    // to paise here would save a figure the grader was never shown.
                    $amount = round(($pct / 100) * $hostelFee, 0);
                    $hostelRows[$index]['sch_amount'] = $amount;
                }

                $hostelRows[$index]['sch_percentage'] = $pct;
                $hostelBatchSum += $pct;
                if (!empty($row['id'])) {
                    $hostelUpdateIds[] = $row['id'];
                }
            }

            foreach ($deletedRows as $d) {
                if (strtolower(isset($d['category']) ? $d['category'] : '') !== 'hostel') {
                    // A removed scholarship changes what the student owes, so its
                    // challan must be regenerated too.
                    $academicCategories[isset($d['challan_category']) ? $d['challan_category'] : 'Full_semester'] = true;
                }
            }

            $academicExcludeIds = array_merge($academicUpdateIds, $academicDeleteIds);
            $hostelExcludeIds   = array_merge($hostelUpdateIds, $hostelDeleteIds);

            // ---------------------------------------------------------- //
            // 3. Combination rule
            // ---------------------------------------------------------- //
            $effective = [];
            foreach (array_keys($academicCategorySums) as $cat) {
                $effective[$cat] = true;
            }
            foreach ($existingAcademic as $row) {
                if (in_array($row->ssd_id, $academicDeleteIds)) {
                    continue;   // going away in this transaction
                }
                $effective[$row->ssd_challan_category] = true;
            }

            // Pinned categories are exempt: the user cannot remove them, so holding
            // them must never block an otherwise legal save. Dormant categories are exempt
            // for the same reason and then some — the student has moved off them entirely,
            // so their history must not veto the category they are actually billed under.
            //
            // A hostel-only Save writes nothing academic, so it is exempt wholesale: an
            // empty set is one of the allowed combinations and passes trivially. Judging
            // the student's existing academic pairing here could only ever REFUSE a save
            // that does not touch it — and a student already holding an illegal pair from
            // the old page would then be unable to save a hostel scholarship at all, with
            // nothing on this page able to clear the block.
            $checkSet = $doAcademic
                ? array_values(array_diff(
                    array_keys($effective),
                    array_keys($pinnedCategories),
                    $dormantCategories
                ))
                : [];
            sort($checkSet);

            $legal = false;
            foreach ($this->allowedCategoryCombinations() as $combo) {
                if ($combo === $checkSet) {
                    $legal = true;
                    break;
                }
            }
            if (!$legal) {
                $labels = $this->academicCategories();
                $named = [];
                foreach ($checkSet as $cat) {
                    $named[] = isset($labels[$cat]) ? $labels[$cat] : $cat;
                }
                // Legal pairs spelled from the same labels, so the rule cannot be
                // described in one wording and enforced against another.
                $pairs = [];
                foreach ($this->allowedCategoryCombinations() as $combo) {
                    if (count($combo) !== 2) {
                        continue;
                    }
                    $pairs[] = implode(' + ', array_map(function ($cat) use ($labels) {
                        return isset($labels[$cat]) ? $labels[$cat] : $cat;
                    }, $combo));
                }

                throw new \Exception('"' . implode('" and "', $named) . '" cannot be applied together in the same session. '
                    . 'Only ' . implode(', or ', $pairs) . ', may be combined.');
            }

            // ---------------------------------------------------------- //
            // 4. Derive the post-save arrears state (nothing written yet)
            // ---------------------------------------------------------- //
            $componentColumns = $this->arrearsComponentColumns();
            $componentLabels  = $this->arrearsComponentLabels();

            // key "<type>|<category>" => ['id'=>?, 'components'=>[...], 'stipend'=>n, 'dirty'=>bool]
            //
            // The type is normalised through (int) on BOTH sides: Oracle can hand a
            // NUMBER column back as "12" or "12.0" while the browser sends 12, and an
            // unnormalised key would then miss the existing row and insert a duplicate
            // that getStudentArrearsBreakdown() would SUM.
            $arrearsState = [];
            foreach ($this->getArrearsRowsForStudent($aai_id, $sessions, $enrollment) as $r) {
                $key = ((int) $r->fos_scholarship_type) . '|' . $r->fos_challan_category;
                $arrearsState[$key] = [
                    'type'       => (int) $r->fos_scholarship_type,
                    'category'   => $r->fos_challan_category,
                    'id'         => $r->id,
                    'components' => [
                        'admission' => (float) $r->fos_admission_fee,
                        'security'  => (float) $r->fos_security_fee,
                        'activity'  => (float) $r->fos_activity_charges,
                        'medical'   => (float) $r->fos_medical_insurance,
                    ],
                    'stipend'    => (float) $r->fos_stipend,
                    'dirty'      => false,
                ];
            }

            // Fee structure per challan category, resolved once and reused for both
            // the arrears cap and the snapshot columns.
            //
            // $feeReason keeps the resolver's own explanation for a category that could
            // not be priced. Without it the only diagnostic was thrown away by the
            // `empty($res['ok']) ? null` below, which is how a whole challan could be
            // saved as zeros with nothing to say why.
            $feeCache  = [];
            $feeReason = [];
            $feeFor = function ($category) use (&$feeCache, &$feeReason, $sessions, $enrollment, $aai_id, $studentType) {
                if (!array_key_exists($category, $feeCache)) {
                    // Same basis the grid used, so the snapshot stores exactly the
                    // figures the grader saw and Post will write to the challan.
                    $res = $this->resolveAcademicFeeStructure(
                        $sessions,
                        $enrollment ? $enrollment : $aai_id,
                        $studentType,
                        $category
                    );
                    $feeCache[$category]  = empty($res['ok']) ? null : $res;
                    $feeReason[$category] = empty($res['ok'])
                        ? (isset($res['message']) ? $res['message'] : 'The fee structure could not be resolved.')
                        : null;
                }

                return $feeCache[$category];
            };

            if ($arrearsCategory !== '') {
                if (!$this->challanCategoryHasArrears($arrearsCategory)) {
                    throw new \Exception('Arrears are not applicable to the "' . $arrearsCategory . '" challan category.');
                }

                $feeRes = $this->resolveAcademicFeeStructure(
                    $sessions,
                    $enrollment ? $enrollment : $aai_id,
                    $studentType,
                    $arrearsCategory
                );
                $feeCache[$arrearsCategory]  = empty($feeRes['ok']) ? null : $feeRes;
                $feeReason[$arrearsCategory] = empty($feeRes['ok'])
                    ? (isset($feeRes['message']) ? $feeRes['message'] : 'The fee structure could not be resolved.')
                    : null;
                if (empty($feeRes['ok'])) {
                    if (!empty($arrearsRows)) {
                        throw new \Exception('Arrears cannot be saved: ' . $feeRes['message']);
                    }
                    $feeRes = null;
                }

                // Every type currently checked in the grid, with its four components.
                $submitted = [];
                foreach ($arrearsRows as $ar) {
                    $component = strtolower(trim((string) (isset($ar['component']) ? $ar['component'] : '')));
                    if (!isset($componentColumns[$component])) {
                        throw new \Exception('Unknown arrears component "' . $component . '".');
                    }
                    if (empty($ar['sch_type'])) {
                        throw new \Exception('A scholarship type is required for the "' . $componentLabels[$component] . '" arrears row.');
                    }
                    $amount = (float) (isset($ar['amount']) ? $ar['amount'] : 0);
                    if ($amount < 0) {
                        throw new \Exception('Arrears amount cannot be negative (' . $componentLabels[$component] . ').');
                    }
                    // Whole rupees when the grader typed the money — same rule and same
                    // reasoning as the academic rows above. In percentage mode the amount
                    // is derived, so it keeps whatever precision the derivation produced.
                    if ($amountMode && abs($amount - round($amount)) > 0.0001) {
                        throw new \Exception('Arrears amount must be a whole rupee figure ('
                            . $componentLabels[$component] . ' received ' . $amount . ').');
                    }
                    // Each component is capped by its OWN fee-structure value, so the
                    // auto-calculated percentage can never exceed 100%.
                    if ($feeRes) {
                        $componentBase = (float) $feeRes['arrearComponents'][$component];
                        if ($amount > $componentBase) {
                            throw new \Exception($componentLabels[$component] . ' arrears (' . $amount . ') exceed the fee structure\'s '
                                . $componentLabels[$component] . ' amount (' . $componentBase . ') for "' . $arrearsCategory . '".');
                        }
                    }
                    $type = (int) $ar['sch_type'];
                    if (!isset($submitted[$type])) {
                        $submitted[$type] = ['admission' => 0.0, 'security' => 0.0, 'activity' => 0.0, 'medical' => 0.0];
                    }
                    $submitted[$type][$component] += $amount;
                }

                // Apply the submitted values, and ZERO any type that previously held
                // arrears in this category but is no longer checked. Rows are never
                // deleted — there is no delete for fin_other_scholarships anywhere in
                // the app, and none is being added here.
                foreach ($arrearsState as $key => $state) {
                    if ($state['category'] !== $arrearsCategory) {
                        continue;
                    }
                    if (!isset($submitted[$state['type']])) {
                        $arrearsState[$key]['components'] = ['admission' => 0.0, 'security' => 0.0, 'activity' => 0.0, 'medical' => 0.0];
                        $arrearsState[$key]['dirty'] = true;
                    }
                }
                foreach ($submitted as $type => $components) {
                    $key = $type . '|' . $arrearsCategory;
                    if (isset($arrearsState[$key])) {
                        $arrearsState[$key]['components'] = $components;
                        $arrearsState[$key]['dirty'] = true;
                    } else {
                        $arrearsState[$key] = [
                            'type'       => $type,
                            'category'   => $arrearsCategory,
                            'id'         => null,
                            'components' => $components,
                            'stipend'    => 0.0,   // stipend is not managed by this page
                            'dirty'      => true,
                        ];
                    }
                }

                // Cap the WHOLE category, not just this batch: every fin_other_scholarships
                // row for the student in this category counts, because that is what
                // getStudentArrearsBreakdown() sums.
                if ($feeRes) {
                    $categoryTotal = 0.0;
                    foreach ($arrearsState as $state) {
                        if ($state['category'] !== $arrearsCategory) {
                            continue;
                        }
                        $categoryTotal += array_sum($state['components']) + $state['stipend'];
                    }
                    $oneTime = (float) $feeRes['oneTimeFeeTotal'];
                    if ($categoryTotal > $oneTime) {
                        throw new \Exception('Arrears Total (' . $categoryTotal . ') for "' . $arrearsCategory
                            . '" exceeds that fee structure\'s one-time fee (' . $oneTime . ').');
                    }
                }
            }

            // Post-save arrears total per "<type>|<category>" — this is what goes into
            // SSD_ARREAR, and it matches getStudentArrearsBreakdown()'s `total`
            // (all four components PLUS stipend).
            //
            // A row whose category CANNOT carry arrears is skipped outright. Additional
            // Credit Hr is the case that matters: it bills only the extra credit hours on
            // a full-semester challan, whose one-time fees are already waived by that
            // challan's own arrears — so an arrears row tagged against it is bad data, not
            // a waiver. Left in, it would be written to SSD_ARREAR and printed as an
            // arrears line by the ledger, while SC_SSD_ARREAR on the challan stayed 0.
            //
            // The rows themselves are left untouched in fin_other_scholarships; they are
            // reported below so somebody can fix the source.
            $arrearTotals = [];
            $skippedArrearsCategories = [];
            foreach ($arrearsState as $key => $state) {
                if (!$this->challanCategoryHasArrears($state['category'])) {
                    $arrearTotals[$key] = 0;
                    $labels = $this->academicCategories();
                    $skippedArrearsCategories[$state['category']] = isset($labels[$state['category']])
                        ? $labels[$state['category']]
                        : $state['category'];
                    continue;
                }
                $arrearTotals[$key] = array_sum($state['components']) + $state['stipend'];
            }

            // An arrears waiver can be given under a scholarship head that carries NO
            // tuition percentage. The grid produces no academic row for such a head, so
            // no SSD row would exist, and SSD_ARREAR — the only thing that reaches
            // FIN_SCHOLARSHIP_DISPLAY_HISTORY.FSD_ARREARS — would never be written. The
            // record of WHICH head funded the waiver would be lost.
            //
            // The procedures avoid this because their cursor reads SSD directly and
            // simply finds a row with ssd_percentage = 0 and ssd_arrear > 0
            // (FIN_SCH_FULL_SEMESTER.sql:208-239). Synthesise that row here so it flows
            // through the same persist path: same head + tuition => ONE row carrying
            // both figures; different heads => a separate 0% row per head.
            //
            // ONLY when the academic section is in scope. $arrearsState is read from
            // fin_other_scholarships for the WHOLE student, not from this batch, so a
            // student with any saved arrears at all would otherwise have a 0% row
            // synthesised for them on a hostel-only Save — which is exactly the leak the
            // note below describes: categoryIsInBatch() turns true, the academic challan
            // is cancelled and restaged, and the persist loop writes the synthetic
            // sch_percentage of 0 straight over the tuition scholarship the grader
            // already had. Nothing academic is being written on a hostel-only Save, so
            // there is no SSD_ARREAR to keep a record on either.
            $batchedKeys = [];
            foreach ($academicRows as $r) {
                if (!empty($r['sch_type'])) {
                    $cat = isset($r['challan_category']) ? $r['challan_category'] : $this->defaultChallanCategory();
                    $batchedKeys[((int) $r['sch_type']) . '|' . $cat] = true;
                }
            }
            foreach (($doAcademic ? $arrearsState : []) as $key => $state) {
                // The <= 0 test also closes the Additional Credit Hr leak: its total was
                // forced to 0 above, so no synthetic row is invented for a category that
                // cannot carry arrears. That matters more than it looks — such a row would
                // make categoryIsInBatch() true, which CANCELS that challan and stages it
                // for posting, all from an arrears row the grader never touched, and all
                // after the combination rule and the percentage caps have already run.
                if (isset($batchedKeys[$key]) || $arrearTotals[$key] <= 0) {
                    continue;
                }
                // sch_details is deliberately absent: the persist loop below falls back
                // to the page's default remark, and $defaultRemark is not in scope yet.
                $academicRows[] = [
                    'id'               => null,
                    'challan_category' => $state['category'],
                    'sch_type'         => $state['type'],
                    'sch_percentage'   => 0,      // arrears only — no tuition waiver
                ];
            }

            // ---------------------------------------------------------- //
            // 5. Percentage caps and the hostel guard
            // ---------------------------------------------------------- //
            // In amount mode the percentage is a 2-decimal display value derived from the
            // money, so it cannot carry this cap: against a 292,000 base, one hundredth of
            // a percent is 29 rupees, and a batch that is exactly at the base can read as
            // 100.01% or 99.99% purely from rounding. The money is capped against the money.
            //
            // The existing rows are measured the same way, amount-first with a percentage
            // fallback — the idiom already used by applyAcademicScholarshipToStudentChallan()
            // in ResolvesScholarshipFees. The fallback is what keeps rows written before
            // this column existed (SSD_AMOUNT null, percentage-mode) counting correctly.
            //
            // STATUS = 'E' is load-bearing on both paths (invariant #10): removal ZEROES a
            // row rather than deleting it, so dropping the filter would count retired rows.
            foreach ($academicCategorySums as $cat => $batchSum) {
                if ($amountMode) {
                    $fee  = $feeFor($cat);
                    $base = $fee ? (float) $fee['amountBase'] : 0.0;

                    $existingRows = DB::table('Students_Scholarships_Details')
                        ->select('SSD_AMOUNT', 'SSD_PERCENTAGE')
                        ->where('SSD_APP_ID', $aai_id)
                        ->where('SSD_SESS_ID', $sessions)
                        ->where('STATUS', 'E')
                        ->whereRaw("NVL(SSD_CHALLAN_CATEGORY, 'Full_semester') = ?", [$cat])
                        ->when(!empty($academicExcludeIds), function ($q) use ($academicExcludeIds) {
                            $q->whereNotIn('SSD_ID', $academicExcludeIds);
                        })
                        ->get();

                    $existingAmount = 0.0;
                    foreach ($existingRows as $er) {
                        $stored = isset($er->ssd_amount) ? (float) $er->ssd_amount : 0.0;
                        $existingAmount += $stored > 0
                            ? $stored
                            : round(((float) (isset($er->ssd_percentage) ? $er->ssd_percentage : 0) / 100) * $base, 2);
                    }

                    $batchAmount = isset($academicCategoryAmountSums[$cat]) ? $academicCategoryAmountSums[$cat] : 0.0;

                    if ($base > 0 && round($existingAmount + $batchAmount, 2) > round($base, 2)) {
                        throw new \Exception('Academic Scholarship Amount exceeds the fee structure for "'
                            . $cat . '" (existing Rs ' . number_format($existingAmount, 2)
                            . ' + this batch Rs ' . number_format($batchAmount, 2)
                            . ' is more than Rs ' . number_format($base, 2) . ').');
                    }

                    continue;
                }

                $existingSum = DB::table('Students_Scholarships_Details')
                    ->where('SSD_APP_ID', $aai_id)
                    ->where('SSD_SESS_ID', $sessions)
                    ->where('STATUS', 'E')
                    ->whereRaw("NVL(SSD_CHALLAN_CATEGORY, 'Full_semester') = ?", [$cat])
                    ->when(!empty($academicExcludeIds), function ($q) use ($academicExcludeIds) {
                        $q->whereNotIn('SSD_ID', $academicExcludeIds);
                    })
                    ->sum('SSD_PERCENTAGE');
                if (($existingSum + $batchSum) > 100) {
                    throw new \Exception('Academic Scholarship Percentage Limit Exceeds 100% for "'
                        . $cat . '" (existing ' . $existingSum . '% + this batch ' . $batchSum . '%).');
                }
            }

            $hostelExisting = DB::table('fin_students_scholarships_hostel_details')
                ->where('SSHD_APP_ID', $aai_id)
                ->where('SSHD_SESS_ID', $sessions)
                ->where('SSHD_STATUS', 'E')
                ->when(!empty($hostelExcludeIds), function ($q) use ($hostelExcludeIds) {
                    $q->whereNotIn('SSHD_ID', $hostelExcludeIds);
                })
                ->sum('SSHD_PERCENTAGE');
            if (($hostelExisting + $hostelBatchSum) > 100) {
                throw new \Exception('Hostel Scholarship Percentage Limit Exceeds 100%');
            }

            if (!empty($hostelRows)) {
                $hasHostelAssign = DB::table('hostel_assign')
                    ->where('ha_session', $sessions)
                    // Same definition the bulk page and the fee resolver use, so a student
                    // the grid says is assigned can never be refused here.
                    ->whereRaw(HostelAssignmentResolver::assignmentExistsSql('hostel_assign'))
                    ->where(function ($q) use ($aai_id, $enrollment) {
                        $q->where('ha_aai_id', $aai_id);
                        if (!empty($enrollment)) {
                            $q->orWhereRaw('UPPER(TRIM(ha_enrolment_no)) = ?', [strtoupper(trim($enrollment))]);
                        }
                    })
                    ->exists();
                if (!$hasHostelAssign) {
                    throw new \Exception('No hostel is assigned to this student for the selected session — hostel scholarship not allowed.');
                }
            }

            // ---------------------------------------------------------- //
            // 6. Deletions first, so delete + re-add in one post is safe
            // ---------------------------------------------------------- //
            // Removal ZEROES the row; it never deletes it. See zeroScholarshipRow().
            // The zeroed rows are then folded back into the batch below, so the category
            // they belonged to still gets its challan cancelled and its snapshot rewritten.
            $deletedCount = 0;
            $zeroedAcademic = [];
            $zeroedHostel = [];

            foreach ($deletedRows as $d) {
                $id = isset($d['id']) ? $d['id'] : null;
                if (empty($id)) {
                    continue;
                }

                if (strtolower(isset($d['category']) ? $d['category'] : '') === 'hostel') {
                    $hRow = DB::table('fin_students_scholarships_hostel_details')
                        ->where('SSHD_ID', $id)
                        ->first();
                    if (empty($hRow)) {
                        continue;   // already gone
                    }
                    // Hostel had no lock check at all while this was a delete — the same
                    // protection academic rows get applies just as much here.
                    if ($this->isScholarshipRowLockedByChallan(
                            'Hostel_Fee',
                            isset($hRow->sshd_app_id) ? $hRow->sshd_app_id : null,
                            isset($hRow->sshd_sai_enrollment_no) ? $hRow->sshd_sai_enrollment_no : null,
                            isset($hRow->sshd_sess_id) ? $hRow->sshd_sess_id : null)) {
                        throw new \Exception('This hostel scholarship\'s challan already carries a '
                            . 'scholarship amount, so it can no longer be edited or removed.');
                    }

                    $deletedCount += $this->zeroScholarshipRow($id, 'hostel', $this->sourcePage());
                    $zeroedHostel[] = [
                        'id'             => $id,
                        'sch_type'       => isset($hRow->sshd_type) ? $hRow->sshd_type : null,
                        'sch_percentage' => 0,
                        'sch_amount'     => 0,
                        'hostelfee'      => isset($hRow->sshd_hostel_fee) ? (float) $hRow->sshd_hostel_fee : 0,
                    ];
                    continue;
                }

                $row = DB::table('Students_Scholarships_Details')->where('SSD_ID', $id)->first();
                if (empty($row)) {
                    continue;   // already gone
                }
                if ($this->isScholarshipRowLockedByChallan(
                        isset($row->ssd_challan_category) ? $row->ssd_challan_category : null,
                        isset($row->ssd_app_id) ? $row->ssd_app_id : null,
                        isset($row->ssd_sai_enrollment_no) ? $row->ssd_sai_enrollment_no : null,
                        isset($row->ssd_sess_id) ? $row->ssd_sess_id : null)) {
                    throw new \Exception('This scholarship was applied against "' . $row->ssd_challan_category
                        . '" and its challan already carries a scholarship amount, so it can no longer be edited or removed.');
                }

                $deletedCount += $this->zeroScholarshipRow($id, 'academic', $this->sourcePage());
                $zeroedAcademic[] = [
                    'id'               => $id,
                    'sch_type'         => isset($row->ssd_type) ? $row->ssd_type : null,
                    'sch_percentage'   => 0,
                    'challan_category' => !empty($row->ssd_challan_category)
                        ? $row->ssd_challan_category
                        : $this->defaultChallanCategory(),
                ];
            }

            // ── fold the zeroed rows back into the batch ──────────────────────────────
            //
            // Everything downstream that works per category reads $academicRows /
            // $hostelRows: $categoryTuitionDisc, categoryIsInBatch(), categoriesBeingSaved()
            // (which drives the challan cancellation in step 7a), the snapshot loop, and
            // therefore what postSnapshot() later finds staged.
            //
            // While removal was a DELETE, a category whose LAST scholarship was removed
            // dropped out of all of them: its challan was never cancelled, no replacement
            // was ever posted, and the discount stayed on the printed challan for good —
            // while the page reported "N deleted". Re-adding the rows at 0% puts the
            // category back in every one of those lists, so the challan is corrected and
            // every row of it gets a consistent snapshot.
            //
            // Placed here on purpose: after the validation and cap sections above, so no
            // rule is re-triggered, and before $categoryTuitionDisc is built below.
            if (!empty($zeroedAcademic)) {
                $academicRows = array_merge($academicRows, $zeroedAcademic);
            }
            if (!empty($zeroedHostel)) {
                $hostelRows = array_merge($hostelRows, $zeroedHostel);
            }

            // ── the scope invariant, checked rather than assumed ──────────────────────
            //
            // Everything destructive from here on — which challans get cancelled, which
            // rows get rewritten — is derived from these two arrays. A section left out
            // of scope must therefore reach this point with NOTHING in it.
            //
            // Checked because it has already been broken once: the arrears synthesis in
            // step 4 appended to $academicRows from the student's whole
            // fin_other_scholarships history rather than from this batch, so a hostel-only
            // Save cancelled the academic challan and overwrote a live 50% scholarship
            // with the synthetic row's 0%. Silent data loss, and nothing between here and
            // the write would have caught it. Fail loudly and roll back instead.
            if ((!$doAcademic && !empty($academicRows)) || (!$doHostel && !empty($hostelRows))) {
                throw new \Exception('Internal error: rows were prepared for a section this save '
                    . 'is not covering (' . implode(' + ', $scope) . '). Nothing has been saved — '
                    . 'please report this.');
            }

            // ---------------------------------------------------------- //
            // 7. Write the arrears rows
            // ---------------------------------------------------------- //
            $arrearsWritten = 0;
            foreach ($arrearsState as $state) {
                if (empty($state['dirty'])) {
                    continue;
                }
                $arrearsWritten += $this->persistArrearsRow($state, $aai_id, $enrollment, $sessions, $entryMode);
            }

            // ---------------------------------------------------------- //
            // 7. RESOLVE EVERY FEE NOW — before a single challan is cancelled.
            //
            // additional_Full_semester is the ONE category whose fee basis is read out of
            // student_challan: resolveAcademicFeeStructure() takes SC_ENROLLED_CR_HR off a
            // live UNPAID challan. Step 7a below cancels that very challan, and
            // fin_bulk_challan_cancellation DELETEs the row — FIN_STUDENT_CANCEL_CHALLAN
            // does not even carry SC_ENROLLED_CR_HR, so the figure is gone for good.
            //
            // $feeFor is lazy, and its first call used to be in step 8, AFTER the
            // cancellation. It therefore found nothing, cached null, and every snapshot
            // column silently fell to 0: the grid showed "17,500 x 3 cr = 52,500" and the
            // preview showed an Additional Credit Hr challan of Rs 0.
            //
            // Priming the cache here is what makes the comment on $feeFor true — the
            // snapshot stores the figures the grader actually saw. Step 7a must NOT move:
            // it has to run before pendingSurplus() so a cancelled paid challan's money is
            // already in fin_fee_adjustment.
            // ---------------------------------------------------------- //
            foreach (array_keys($this->academicCategories()) as $primeCategory) {
                $feeFor($primeCategory);
            }

            // A category the grader entered rows against but which cannot be priced would
            // otherwise be written as a Rs 0 challan — and a Rs 0 additional-credit-hour
            // challan carries SC_ENROLLED_CR_HR = 0, which matches NEITHER poster's
            // cancellation criteria and can never be retired again. Refuse instead, before
            // anything is written and before any challan is cancelled.
            // $academicRows, not the submitted batch: by now it also carries the zeroed
            // rows folded back in, and those rewrite their category's snapshot and reissue
            // its challan just the same. An unpriced one would reissue at Rs 0.
            foreach (array_keys($this->academicCategories()) as $cat) {
                if (!$this->categoryIsInBatch($academicRows, $cat) || $feeFor($cat) !== null) {
                    continue;
                }
                throw new \Exception('"' . $this->academicCategories()[$cat] . '" cannot be priced: '
                    . (isset($feeReason[$cat]) ? $feeReason[$cat] : 'the fee structure could not be resolved.')
                    . ' Nothing has been saved.');
            }

            // ---------------------------------------------------------- //
            // 7a. Retire the existing challans — Save, not Post
            //
            // A saved snapshot must never sit alongside a live challan that says
            // something different. Cancelling here means the moment the figures
            // change, the old document stops being the student's live challan.
            //
            // The student then has NO challan until Post issues the replacement;
            // the page shows a standing warning for that window. The client takes
            // consent before sending, and `cancel_existing` carries it.
            //
            // Order matters: the hostel carry-over values are read off the live
            // challan and fin_bulk_challan_cancellation DELETEs that row, so they
            // are captured FIRST.
            // ---------------------------------------------------------- //
            $hostelCarry = [];
            $cancelledCategories = [];
            // Dormant challans that were cancelled. Kept apart from $cancelledCategories
            // because Post never re-issues these — the page must not tell the grader to
            // wait for a replacement that is not coming.
            $retiredCategories = [];

            if ($cancelExisting) {
                $factory   = new ChallanPosterFactory();
                $canceller = new ChallanCanceller();
                $entryUser = auth()->user()->username;

                // Dormant categories are academic, so they ride with the academic scope.
                // A hostel-only Save must not retire an academic challan as a side effect.
                $dormantToRetire = $doAcademic ? $dormantCategories : [];

                foreach ($this->categoriesBeingSaved($academicRows, $hostelRows, $dormantToRetire) as $cat) {
                    $poster = $factory->make($cat);
                    if ($poster === null) {
                        continue;
                    }

                    $isDormant = in_array($cat, $dormantCategories, true);

                    if ($cat === 'Hostel_Fee') {
                        $hostelCarry = $this->captureHostelChallanCarry($aai_id, $enrollment, $sessions);
                    }

                    $result = $canceller->cancelWhere(
                        $aai_id, $sessions, $enrollment,
                        $poster->cancellationCriteria(),
                        $entryUser,
                        $isDormant
                            ? 'Cancelled on Save from Record Management (Revised) — student is now billed per credit hour, not reissued'
                            : 'Cancelled on Save from Record Management (Revised) — replacement to be posted'
                    );

                    if ($result['count'] > 0) {
                        if ($isDormant) {
                            $retiredCategories[] = $cat;
                        } else {
                            $cancelledCategories[] = $cat;
                        }
                    }
                }
            }

            // Dormant rows left PENDING by an earlier round would otherwise resurface in a
            // later preview and be posted as a challan the student is no longer due. Retire
            // the staging flag only — the percentage and amount stay untouched on record.
            if ($doAcademic) {
                $this->retireDormantStagedRows($aai_id, $sessions, $dormantCategories);
            }

            // ---------------------------------------------------------- //
            // 8. Persist the scholarship rows
            // ---------------------------------------------------------- //
            $defaultRemark = 'Record Management (Revised)';
            $savedAt  = Carbon::now('Asia/Karachi');
            $savedBy  = auth()->user()->username;
            $sdpo     = $this->resolveStudentSdpo($studentType, $enrollment ? $enrollment : $aai_id);

            // The due / valid pair this save is accepting, frozen onto every row below.
            // Throws on a bad operator-entered pair, which is why it sits before the
            // persist loops rather than inside them — a rejected date must not leave half
            // the rows written.
            $acceptedDates = $this->acceptedChallanDates($request, $aai_id, $enrollment, $sessions, $studentType);

            // Per-category totals. These describe the CHALLAN, not the row, so they
            // repeat identically on every row of the same category — never SUM() them.
            // Tuition discount: every scholarship row in the category.
            $categoryTuitionDisc = [];
            foreach ($academicRows as $row) {
                $cat  = $row['challan_category'];
                $fee  = $feeFor($cat);
                $base = $fee ? (float) $fee['amountBase'] : 0.0;
                $pct  = (float) (isset($row['sch_percentage']) ? $row['sch_percentage'] : 0);
                // This total becomes SSD_TUITION_DISC_AMT / SSD_TOTAL_DISC_AMT, which is
                // the figure the PRINTED CHALLAN carries — so in amount mode it has to be
                // the money the grader typed, not a reconstruction of it. Taking the
                // percentage here was half of the 200,000 -> 200,000.14 drift; the other
                // half is the SSD_AMOUNT line further down.
                $categoryTuitionDisc[$cat] = (isset($categoryTuitionDisc[$cat]) ? $categoryTuitionDisc[$cat] : 0.0)
                    + ($amountMode
                        ? round((float) (isset($row['sch_amount']) ? $row['sch_amount'] : 0), 2)
                        : round(($pct / 100) * $base, 2));
            }

            // One-time discount per component: every arrears row in the category,
            // across ALL scholarship types (a challan line is discounted once, in total).
            $categoryComponentDisc = [];
            foreach ($arrearsState as $state) {
                $cat = $state['category'];
                if (!isset($categoryComponentDisc[$cat])) {
                    $categoryComponentDisc[$cat] = ['admission' => 0.0, 'security' => 0.0, 'medical' => 0.0, 'activity' => 0.0];
                }
                foreach ($categoryComponentDisc[$cat] as $component => $ignored) {
                    $categoryComponentDisc[$cat][$component] += (float) $state['components'][$component];
                }
            }

            // ---------------------------------------------------------- //
            // 8a. Surplus, frozen here so the preview shows the challan as it
            //     will exist AFTER posting.
            //
            //     Replaces FIN_PRO_ADJUST_CHALLAN_SURPLUS. Read-only at this point:
            //     nothing is spent until Post. The walk order must match the one
            //     postSnapshot() uses, or a two-challan split would preview
            //     differently from how it posts — both use academicCategories().
            // ---------------------------------------------------------- //
            $payableByCategory = [];
            foreach (array_keys($this->academicCategories()) as $cat) {
                if (!isset($categoryTuitionDisc[$cat]) && !$this->categoryIsInBatch($academicRows, $cat)) {
                    continue;
                }
                $payableByCategory[$cat] = $this->categoryPayable($feeFor($cat), $categoryTuitionDisc, $categoryComponentDisc, $cat);
            }

            // Hostel shares the SAME balance — fin_fee_adjustment is keyed on the
            // student, with no academic/hostel dimension — so it joins the simulation
            // rather than being excluded. It is walked LAST, matching the order
            // postSnapshot() posts in ($work appends Hostel_Fee after the academic
            // categories); with a partial surplus that means academic is covered first.
            $hostelPayableForSurplus = $this->hostelPayableForSurplus($hostelRows);
            if ($hostelPayableForSurplus !== null) {
                $payableByCategory['Hostel_Fee'] = $hostelPayableForSurplus;
            }

            $surplusByCategory = [];
            $surplusPending    = 0.0;
            if (!empty($payableByCategory)) {
                $adjuster = new SurplusAdjuster();

                // The REAL balance, no prediction involved. Step 7a above has already
                // cancelled the existing challans, so any surplus a cancelled paid
                // challan released is already sitting in fin_fee_adjustment by now.
                //
                // This is why cancelling at Save simplifies things: the figure frozen
                // here and the figure Post re-reads are measured the same way, so the
                // "did somebody else move it?" check is exact.
                $surplusPending = $adjuster->pendingSurplus($aai_id, $enrollment);

                // What a section this Save is not touching is already holding. Only the
                // rest is ours to divide up — see surplusClaimedByStagedSections().
                //
                // SSD_SURPLUS_PENDING below is deliberately frozen as the RAW pending
                // figure, not this reduced one: Post compares it against a fresh
                // pendingSurplus() to detect somebody else moving the student's surplus,
                // and that check only works if both sides measure the same thing.
                $claimedElsewhere = $this->surplusClaimedByStagedSections(
                    $aai_id, $sessions, !$doAcademic, !$doHostel
                );

                $surplusByCategory = $adjuster->simulate(
                    max(0.0, $surplusPending - $claimedElsewhere),
                    $payableByCategory
                );
            }

            foreach ($academicRows as $row) {
                $cat = $row['challan_category'];
                $key = ((int) $row['sch_type']) . '|' . $cat;
                $fee = $feeFor($cat);
                $pct = (float) (isset($row['sch_percentage']) ? $row['sch_percentage'] : 0);
                $surplus = isset($surplusByCategory[$cat])
                    ? $surplusByCategory[$cat]
                    : ['available' => 0.0, 'consumed' => 0.0, 'invoice' => null, 'remaining' => 0.0];

                // Recomputed server-side — never trusted from the browser.
                //
                // Amount mode inverts which of the pair is the recomputation: the money is
                // what was awarded, so it is taken as given (clamped to the base, which is
                // the only thing the browser could get wrong that matters), and the
                // PERCENTAGE becomes the derived value. Rounded to 2 dp because it is a
                // display figure from here on — the challan prints it as "68.49%" and the
                // reports read the column raw, so more precision would only add noise.
                // Nothing recomputes the amount from it, which is the whole point.
                $base = $fee ? (float) $fee['amountBase'] : 0.0;

                if ($amountMode) {
                    $amount = round((float) (isset($row['sch_amount']) ? $row['sch_amount'] : 0), 2);
                    if ($base > 0 && $amount > $base) {
                        $amount = round($base, 2);
                    }
                    $pct = $base > 0 ? round(($amount * 100) / $base, 2) : 0;
                } else {
                    $amount = round(($pct / 100) * $base, 2);
                }

                // ── the printed challan, line by line ──
                $actual = [
                    'admission' => $fee ? (float) $fee['arrearComponents']['admission'] : 0.0,
                    'security'  => $fee ? (float) $fee['arrearComponents']['security']  : 0.0,
                    'medical'   => $fee ? (float) $fee['arrearComponents']['medical']   : 0.0,
                    'activity'  => $fee ? (float) $fee['arrearComponents']['activity']  : 0.0,
                ];
                $disc = isset($categoryComponentDisc[$cat])
                    ? $categoryComponentDisc[$cat]
                    : ['admission' => 0.0, 'security' => 0.0, 'medical' => 0.0, 'activity' => 0.0];

                // A discount can never exceed its own line, so payable never goes negative.
                $lineDisc    = [];
                $linePayable = [];
                $linePct     = [];
                foreach ($actual as $component => $lineActual) {
                    $d = min((float) $disc[$component], $lineActual);
                    $lineDisc[$component]    = round($d, 2);
                    $linePayable[$component] = round($lineActual - $d, 2);
                    $linePct[$component]     = $lineActual > 0 ? round(($d * 100) / $lineActual, 4) : 0;
                }

                $tuitionDisc    = min(isset($categoryTuitionDisc[$cat]) ? $categoryTuitionDisc[$cat] : 0.0, $base);
                $tuitionPayable = round($base - $tuitionDisc, 2);
                $tuitionPct     = $base > 0 ? round(($tuitionDisc * 100) / $base, 4) : 0;

                $oneTimeActual  = array_sum($actual);
                $oneTimeDisc    = array_sum($lineDisc);
                $oneTimePayable = array_sum($linePayable);
                $oneTimePct     = $oneTimeActual > 0 ? round(($oneTimeDisc * 100) / $oneTimeActual, 4) : 0;

                $totalActual  = round($base + $oneTimeActual, 2);
                $totalDisc    = round($tuitionDisc + $oneTimeDisc, 2);
                $totalPayable = round($tuitionPayable + $oneTimePayable, 2);
                $totalPct     = $totalActual > 0 ? round(($totalDisc * 100) / $totalActual, 4) : 0;

                $this->persistScholarshipRow([
                    'category'         => 'academic',
                    'id'               => isset($row['id']) ? $row['id'] : null,
                    'sch_type'         => $row['sch_type'],
                    'sch_percentage'   => $pct,
                    // SSD_ARREAR is the post-save fin_other_scholarships total for this
                    // (type, category). Rows this batch does not touch keep whatever
                    // arrears they already had — never recomputed, never zeroed.
                    'sch_arrear'       => isset($arrearTotals[$key]) ? $arrearTotals[$key] : 0,
                    'sch_details'      => !empty($row['sch_details']) ? $row['sch_details'] : $defaultRemark,
                    'challan_category' => $cat,
                ], $aai_id, $enrollment, $sessions, [
                    'SSD_AMOUNT'              => $amount,
                    // Which side of the pair the grader typed. Read back per row on load so
                    // an amount-entered award is never re-derived from its own percentage.
                    // Dropped silently until add_scholarship_entry_mode_columns.sql is run,
                    // which reads as PERCENT — i.e. exactly today's behaviour.
                    'SSD_ENTRY_MODE'          => $entryMode,
                    'SSD_BULK_POST_STATUS'    => $this->pendingStatus(),
                    'SSD_SOURCE_PAGE'         => $this->sourcePage(),
                    'SSD_SAVED_BY'            => $savedBy,
                    'SSD_SAVED_AT'            => $savedAt,
                    // On EVERY row of the group, not just one: the preview renderer picks
                    // its head row by SSD_TOTAL_FEE_ACTUAL > 0, so a pair written to a
                    // single row could sit on a row it never looks at. Dropped silently
                    // until add_scholarship_challan_date_columns.sql has been run.
                    'SSD_DUE_DATE'            => $acceptedDates['due'],
                    'SSD_VALID_DATE'          => $acceptedDates['valid'],
                    'SSD_STUDENT_TYPE'        => $studentType,
                    'SSD_SCHOOL_ID'           => $sdpo ? $sdpo->school_id : null,
                    'SSD_DEPT_ID'             => $sdpo ? $sdpo->dept_id : null,
                    'SSD_PROG_ID'             => $sdpo ? $sdpo->prog_id : null,
                    'SSD_FFS_ID'              => $fee ? $fee['ffsId'] : null,
                    'SSD_FEE_TYPE'            => $fee ? $fee['feeType'] : null,
                    'SSD_TUITION_FEE'         => ($fee && $fee['referenceLabel'] === 'Tuition Fee') ? $fee['referenceFee'] : null,
                    'SSD_PER_CREDIT_FEE'      => ($fee && $fee['referenceLabel'] === 'Per Credit Fee') ? $fee['referenceFee'] : null,
                    'SSD_CREDIT_HOURS'        => $fee ? $fee['creditHours'] : null,
                    'SSD_REPEAT_CREDIT_HOURS' => $fee ? $fee['repeatCredits'] : null,

                    // actual (undiscounted) fee per printed challan line
                    'SSD_TUITION_FEE_ACTUAL'   => $base,
                    'SSD_ADMISSION_FEE_ACTUAL' => $actual['admission'],
                    'SSD_SECURITY_FEE_ACTUAL'  => $actual['security'],
                    'SSD_MEDICAL_FEE_ACTUAL'   => $actual['medical'],
                    'SSD_ACTIVITY_FEE_ACTUAL'  => $actual['activity'],
                    'SSD_ONETIME_FEE_ACTUAL'   => round($oneTimeActual, 2),
                    'SSD_TOTAL_FEE_ACTUAL'     => $totalActual,

                    // discount percentage per line
                    'SSD_TUITION_DISC_PCT'     => $tuitionPct,
                    'SSD_ADMISSION_DISC_PCT'   => $linePct['admission'],
                    'SSD_SECURITY_DISC_PCT'    => $linePct['security'],
                    'SSD_MEDICAL_DISC_PCT'     => $linePct['medical'],
                    'SSD_ACTIVITY_DISC_PCT'    => $linePct['activity'],
                    'SSD_ONETIME_DISC_PCT'     => $oneTimePct,
                    'SSD_TOTAL_DISC_PCT'       => $totalPct,

                    // discount amount per line
                    'SSD_TUITION_DISC_AMT'     => round($tuitionDisc, 2),
                    'SSD_ADMISSION_DISC_AMT'   => $lineDisc['admission'],
                    'SSD_SECURITY_DISC_AMT'    => $lineDisc['security'],
                    'SSD_MEDICAL_DISC_AMT'     => $lineDisc['medical'],
                    'SSD_ACTIVITY_DISC_AMT'    => $lineDisc['activity'],
                    'SSD_ONETIME_DISC_AMT'     => round($oneTimeDisc, 2),
                    'SSD_TOTAL_DISC_AMT'       => $totalDisc,

                    // payable per line — what the challan prints
                    'SSD_TUITION_PAYABLE'      => $tuitionPayable,
                    'SSD_ADMISSION_PAYABLE'    => $linePayable['admission'],
                    'SSD_SECURITY_PAYABLE'     => $linePayable['security'],
                    'SSD_MEDICAL_PAYABLE'      => $linePayable['medical'],
                    'SSD_ACTIVITY_PAYABLE'     => $linePayable['activity'],
                    'SSD_ONETIME_PAYABLE'      => round($oneTimePayable, 2),
                    'SSD_TOTAL_PAYABLE'        => $totalPayable,

                    // surplus, frozen — SSD_TOTAL_PAYABLE stays PRE-surplus so the
                    // line-by-line maths and the arrears caps are unaffected;
                    // SSD_INVOICE_AMOUNT is the figure that reaches INVOICEAMOUNT.
                    'SSD_SURPLUS_AVAILABLE'    => round((float) $surplus['available'], 2),
                    // Pending-only, excluding credit predicted from a cancellation —
                    // the figure Post compares against. See the note at step 8a.
                    'SSD_SURPLUS_PENDING'      => round($surplusPending, 2),
                    'SSD_SURPLUS_CONSUMED'     => round((float) $surplus['consumed'], 2),
                    'SSD_INVOICE_AMOUNT'       => $surplus['invoice'] === null
                        ? $totalPayable
                        : round((float) $surplus['invoice'], 2),

                    // summer per-course pricing (NULL for every other category)
                    'SSD_COURSE_COUNT'         => ($fee && isset($fee['courseCount'])) ? $fee['courseCount'] : null,
                    'SSD_REPEAT_FEE'           => ($fee && isset($fee['repeatFee'])) ? $fee['repeatFee'] : null,
                ]);
            }

            $hostelType = !empty($hostelRows)
                ? $this->resolveHostelAssignmentType($aai_id, $enrollment, $sessions)
                : null;
            $hostelStructureType = trim((string) $request->input('hostel_fee_structure_type'));
            $hostelTotalDiscount = 0.0;
            foreach ($hostelRows as $row) {
                $hostelTotalDiscount += (float) (isset($row['sch_amount']) ? $row['sch_amount'] : 0);
            }

            // Hostel's slice of the shared balance, simulated above alongside academic.
            $hostelSurplus = isset($surplusByCategory['Hostel_Fee'])
                ? $surplusByCategory['Hostel_Fee']
                : ['available' => 0.0, 'consumed' => 0.0, 'invoice' => null, 'remaining' => 0.0];

            foreach ($hostelRows as $row) {
                $hostelFee     = (float) (isset($row['hostelfee']) ? $row['hostelfee'] : 0);
                $hostelDisc    = min($hostelTotalDiscount, $hostelFee);
                $hostelPayable = round($hostelFee - $hostelDisc, 2);
                $hostelPct     = $hostelFee > 0 ? round(($hostelDisc * 100) / $hostelFee, 4) : 0;

                // $hostelCarry is merged in below: the attributes of the challan this
                // Save just cancelled, which the replacement must inherit.
                $this->persistScholarshipRow([
                    'category'       => 'hostel',
                    'id'             => isset($row['id']) ? $row['id'] : null,
                    'sch_type'       => $row['sch_type'],
                    'sch_percentage' => $row['sch_percentage'],
                    'sch_amount'     => (float) (isset($row['sch_amount']) ? $row['sch_amount'] : 0),
                    'hostelfee'      => $hostelFee,
                    'sch_arrear'     => 0,
                    'sch_details'    => !empty($row['sch_details']) ? $row['sch_details'] : $defaultRemark,
                ], $aai_id, $enrollment, $sessions, [
                    // See SSD_ENTRY_MODE above — same meaning, same silent-drop guard.
                    'SSHD_ENTRY_MODE'        => $entryMode,
                    'SSHD_BULK_POST_STATUS'  => $this->pendingStatus(),
                    'SSHD_SOURCE_PAGE'       => $this->sourcePage(),
                    'SSHD_SAVED_BY'          => $savedBy,
                    'SSHD_SAVED_AT'          => $savedAt,
                    // Same pair as the academic rows — one Save accepts one deadline for
                    // every document it stages. See the SSD_ twins above.
                    'SSHD_DUE_DATE'          => $acceptedDates['due'],
                    'SSHD_VALID_DATE'        => $acceptedDates['valid'],
                    'SSHD_STUDENT_TYPE'      => $studentType,
                    'SSHD_SCHOOL_ID'         => $sdpo ? $sdpo->school_id : null,
                    'SSHD_DEPT_ID'           => $sdpo ? $sdpo->dept_id : null,
                    'SSHD_PROG_ID'           => $sdpo ? $sdpo->prog_id : null,
                    'SSHD_FEE_STRUCTURE_TYPE' => $hostelStructureType !== '' ? $hostelStructureType : null,
                    'SSHD_HOSTEL_TYPE_ID'    => $hostelType ? $hostelType->ht_id : null,
                    'SSHD_HOSTEL_TYPE_NAME'  => $hostelType ? $hostelType->ht_name : null,

                    // the hostel challan, as it will print
                    'SSHD_HOSTEL_FEE_ACTUAL' => $hostelFee,
                    'SSHD_HOSTEL_DISC_PCT'   => $hostelPct,
                    'SSHD_HOSTEL_DISC_AMT'   => round($hostelDisc, 2),
                    'SSHD_HOSTEL_PAYABLE'    => $hostelPayable,
                    'SSHD_TOTAL_FEE_ACTUAL'  => $hostelFee,
                    'SSHD_TOTAL_DISC_AMT'    => round($hostelDisc, 2),
                    'SSHD_TOTAL_PAYABLE'     => $hostelPayable,

                    // Surplus, frozen — same meaning as the SSD_ set. SSHD_TOTAL_PAYABLE
                    // stays PRE-surplus; SSHD_INVOICE_AMOUNT is what reaches INVOICEAMOUNT.
                    'SSHD_SURPLUS_AVAILABLE' => round((float) $hostelSurplus['available'], 2),
                    'SSHD_SURPLUS_PENDING'   => round($surplusPending, 2),
                    'SSHD_SURPLUS_CONSUMED'  => round((float) $hostelSurplus['consumed'], 2),
                    'SSHD_INVOICE_AMOUNT'    => $hostelSurplus['invoice'] === null
                        ? $hostelPayable
                        : round((float) $hostelSurplus['invoice'], 2),
                ] + $hostelCarry);
            }

            // Save NEVER touches student_challan. Post does that, from the frozen
            // snapshot written above — see App\Services\StudentRecord\ChallanPosting.

            DB::commit();

            // Row tallies were noise — the grader reviews the grid, not a count. The
            // counts are still returned below for any caller that wants them.
            $savedCount = count($academicRows) + count($hostelRows);
            $wroteSomething = ($savedCount > 0 || $arrearsWritten > 0 || $deletedCount > 0);

            $message = ($wroteSomething ? 'Saved.' : 'Nothing to save.')
                . ' Review the Final Amounts, then click Post.';

            // The student is now uncovered for these categories until Post runs. Say so
            // plainly; the client keeps a standing warning up for the same reason.
            if (!empty($cancelledCategories)) {
                $labels = $this->academicCategories();
                $named = array_map(function ($c) use ($labels) {
                    return $c === 'Hostel_Fee' ? 'Hostel' : (isset($labels[$c]) ? $labels[$c] : $c);
                }, $cancelledCategories);

                $message .= ' The existing ' . implode(' and ', $named)
                    . ' challan has been CANCELLED — the student has no challan until you Post.';
            }

            // Retired challans are a different story from cancelled ones: nothing replaces
            // them, so the grader must not be left waiting for a Post that will not deliver.
            if (!empty($retiredCategories)) {
                $labels = $this->academicCategories();
                $namedRetired = array_map(function ($c) use ($labels) {
                    return isset($labels[$c]) ? $labels[$c] : $c;
                }, $retiredCategories);

                $message .= ' The ' . implode(' and ', $namedRetired)
                    . ' challan no longer applies to this student and has been cancelled without a replacement.';
            }

            // A course whose OFFERING school has no fee structure is charged NOTHING and
            // would otherwise pass unnoticed — the saved tuition is short by its fee. Both
            // per-course paths report them: summer tuition, and credit-hour repeats.
            $unpriced = [];
            foreach (array_keys($this->academicCategories()) as $cat) {
                $fee = $feeFor($cat);
                if (!empty($fee['unpricedCourses'])) {
                    $unpriced = array_merge($unpriced, $fee['unpricedCourses']);
                }
            }
            if (!empty($unpriced)) {
                $message .= ' Not priced (the offering school has no fee structure for these '
                    . 'courses): ' . implode(', ', array_unique($unpriced)) . '.';
            }

            // Named rather than swallowed: these are arrears rows sitting against a category
            // that cannot carry arrears, which is a data problem somebody has to fix at the
            // source. They were ignored here, not deleted.
            if (!empty($skippedArrearsCategories)) {
                $message .= ' Ignored arrears held against ' . implode(' and ', $skippedArrearsCategories)
                    . ', which cannot carry arrears — those belong on the Full Semester challan.';
            }

            return response()->json([
                'success'       => true,
                'message'       => $message,
                'staged'        => true,
                'saved_rows'    => $savedCount,
                'arrears_rows'  => $arrearsWritten,
                'deleted_rows'  => $deletedCount,
                // Categories whose challan Save just retired. The student has none for
                // these until Post issues the replacement.
                'cancelled'     => $cancelledCategories,
                // Dormant categories whose challan was cancelled and will NOT be reissued.
                'retired'       => $retiredCategories,
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['success' => false, 'messege' => $e->getMessage()], 500);
        }
    }

    /**
     * Staged-row queries.
     *
     * The PENDING / source-page filters are applied ONLY when those columns exist.
     * If the DDL has not been run, Save silently drops them (filterExistingColumns),
     * so filtering on them here would find nothing and the page would report
     * "nothing was staged" for rows that were in fact saved. Degrading to "all
     * enabled rows for this student + session" keeps the page usable until the
     * columns are added.
     */
    private function stagedAcademicQuery($aai_id, $sessions)
    {
        $q = DB::table('Students_Scholarships_Details as ssd')
            ->where('ssd.SSD_APP_ID', $aai_id)
            ->where('ssd.SSD_SESS_ID', $sessions)
            ->whereRaw("NVL(ssd.status, 'E') = 'E'");

        if ($this->tableColumnExists('Students_Scholarships_Details', 'SSD_BULK_POST_STATUS')) {
            $q->whereRaw("UPPER(TRIM(NVL(ssd.SSD_BULK_POST_STATUS, ''))) = ?", [$this->pendingStatus()]);
        }
        if ($this->tableColumnExists('Students_Scholarships_Details', 'SSD_SOURCE_PAGE')) {
            $q->whereRaw("UPPER(TRIM(NVL(ssd.SSD_SOURCE_PAGE, ''))) = ?", [$this->sourcePage()]);
        }

        return $q;
    }

    private function stagedHostelQuery($aai_id, $sessions)
    {
        $q = DB::table('fin_students_scholarships_hostel_details as sshd')
            ->where('sshd.SSHD_APP_ID', $aai_id)
            ->where('sshd.SSHD_SESS_ID', $sessions)
            ->whereRaw("NVL(TRIM(sshd.SSHD_STATUS), 'E') = 'E'");

        if ($this->tableColumnExists('fin_students_scholarships_hostel_details', 'SSHD_BULK_POST_STATUS')) {
            $q->whereRaw("UPPER(TRIM(NVL(sshd.SSHD_BULK_POST_STATUS, ''))) = ?", [$this->pendingStatus()]);
        }
        if ($this->tableColumnExists('fin_students_scholarships_hostel_details', 'SSHD_SOURCE_PAGE')) {
            $q->whereRaw("UPPER(TRIM(NVL(sshd.SSHD_SOURCE_PAGE, ''))) = ?", [$this->sourcePage()]);
        }

        return $q;
    }

    /**
     * Which snapshot columns this install actually has. Returned with the staged
     * payload so a missing ALTER shows up as a clear message instead of an empty
     * table with no explanation.
     */
    private function snapshotSchemaReport()
    {
        $academic = ['SSD_AMOUNT', 'SSD_BULK_POST_STATUS', 'SSD_SOURCE_PAGE',
            'SSD_TUITION_FEE_ACTUAL', 'SSD_TOTAL_PAYABLE', 'SSD_TOTAL_DISC_AMT',
            // Surplus + summer per-course pricing. Without these the preview cannot
            // show the post-surplus invoice and the summer repeat fee is lost.
            'SSD_SURPLUS_AVAILABLE', 'SSD_SURPLUS_PENDING', 'SSD_SURPLUS_CONSUMED',
            'SSD_INVOICE_AMOUNT', 'SSD_COURSE_COUNT', 'SSD_REPEAT_FEE',
            // The accepted deadlines. Without these the pair the grader confirmed is not
            // recorded at all, and Post silently re-derives one instead.
            'SSD_DUE_DATE', 'SSD_VALID_DATE'];
        $hostel = ['SSHD_BULK_POST_STATUS', 'SSHD_SOURCE_PAGE',
            'SSHD_HOSTEL_FEE_ACTUAL', 'SSHD_TOTAL_PAYABLE',
            'SSHD_SURPLUS_AVAILABLE', 'SSHD_SURPLUS_PENDING', 'SSHD_SURPLUS_CONSUMED',
            'SSHD_INVOICE_AMOUNT', 'SSHD_DUE_DATE', 'SSHD_VALID_DATE'];

        $missing = [];
        foreach ($academic as $c) {
            if (!$this->tableColumnExists('Students_Scholarships_Details', $c)) { $missing[] = $c; }
        }
        foreach ($hostel as $c) {
            if (!$this->tableColumnExists('fin_students_scholarships_hostel_details', $c)) { $missing[] = $c; }
        }

        return $missing;
    }

    /**
     * STEP 1b — the staged snapshot, read back from the DB.
     *
     * The tabular summary is rendered from THIS, not from the browser's working
     * copy, so the summary, the challan preview and what Post writes are all the
     * same numbers read from the same rows. Previously the summary recomputed the
     * maths client-side and could disagree with the challan.
     */
    public function getStaged(Request $request, $session = null, $student_id = null)
    {
        // Accept both the path segments and the query string, so a mismatch in one
        // does not silently produce an empty result.
        $sessions = $request->query('academic_session') ?: $session;
        $aai_id   = $request->query('sch_aai_id') ?: $student_id;

        if (empty($sessions) || empty($aai_id)) {
            return response()->json([
                'status'     => false,
                'message'    => 'Session and student are required.',
                // Same shape as the success path — the client reads sections.academic /
                // sections.hostel, and an empty ARRAY here reads as "nothing staged"
                // with no explanation.
                'sections'   => ['academic' => [], 'hostel' => []],
                'diagnostic' => 'The page did not send a session (' . var_export($sessions, true)
                    . ') and/or a student id (' . var_export($aai_id, true) . '). Search the student again.',
            ]);
        }

        $labels = $this->academicCategories();

        $academic = $this->stagedAcademicQuery($aai_id, $sessions)
            ->leftJoin('scholarship_type as st', 'st.scholarship_id', '=', 'ssd.ssd_type')
            ->select('ssd.*', DB::raw('st.scholarship_name as scholarship_name'))
            ->orderBy('ssd.SSD_ID')
            ->get();

        $hostel = $this->stagedHostelQuery($aai_id, $sessions)
            ->leftJoin('scholarship_type as st', 'st.scholarship_id', '=', 'sshd.sshd_type')
            ->select('sshd.*', DB::raw('st.scholarship_name as scholarship_name'))
            ->orderBy('sshd.SSHD_ID')
            ->get();

        $num = function ($row, $col) {
            return isset($row->{$col}) ? (float) $row->{$col} : 0.0;
        };

        // Categories the student has outgrown never reach the preview: a challan they are
        // no longer due must not be reviewable, acceptable or postable. Rows left PENDING
        // by an earlier round are retired on the next Save, but the filter has to hold here
        // too — the grader may open the preview before saving anything.
        $dormant = $this->dormantCategoriesForStudent($aai_id, $sessions);

        // One block per academic challan category, in printed-challan line order.
        $byCategory = [];
        foreach ($academic as $row) {
            $cat = !empty($row->ssd_challan_category) ? $row->ssd_challan_category : $this->defaultChallanCategory();
            if (in_array($cat, $dormant, true)) {
                continue;
            }
            $byCategory[$cat][] = $row;
        }

        $academicBlocks = [];
        foreach ($byCategory as $cat => $rows) {
            $head = $rows[0];

            $lines = [];
            $map = [
                ['Admission Fee',    'ssd_admission_fee_actual', 'ssd_admission_disc_amt', 'ssd_admission_payable'],
                ['Security Fee',     'ssd_security_fee_actual',  'ssd_security_disc_amt',  'ssd_security_payable'],
                ['Tuition Fee',      'ssd_tuition_fee_actual',   'ssd_tuition_disc_amt',   'ssd_tuition_payable'],
                ['Medical Fee',      'ssd_medical_fee_actual',   'ssd_medical_disc_amt',   'ssd_medical_payable'],
                ['Activity Charges', 'ssd_activity_fee_actual',  'ssd_activity_disc_amt',  'ssd_activity_payable'],
            ];
            foreach ($map as $m) {
                $actual = $num($head, $m[1]);
                if ($actual <= 0) {
                    continue;
                }
                $lines[] = [
                    'label'    => $m[0],
                    'actual'   => $actual,
                    'discount' => $num($head, $m[2]),
                    'payable'  => $num($head, $m[3]),
                ];
            }

            $scholarships = [];
            foreach ($rows as $row) {
                if ($num($row, 'ssd_amount') <= 0) {
                    continue;
                }
                $scholarships[] = [
                    'name'   => $row->scholarship_name ?: ('Scholarship #' . $row->ssd_type),
                    'pct'    => $num($row, 'ssd_percentage'),
                    'amount' => $num($row, 'ssd_amount'),
                ];
            }

            $academicBlocks[] = [
                'title'            => (isset($labels[$cat]) ? $labels[$cat] : $cat) . ' Challan',
                'category'         => $cat,
                'lines'            => $lines,
                'scholarships'     => $scholarships,
                'total_actual'     => $num($head, 'ssd_total_fee_actual'),
                'total_disc'       => $num($head, 'ssd_total_disc_amt'),
                'total_payable'    => $num($head, 'ssd_total_payable'),
                // Surplus frozen at Save. invoice_amount is what INVOICEAMOUNT will be.
                //
                // A zero here is MEANINGFUL — it is the surplus covering the challan in
                // full, which is the whole point of the feature — so it must not be
                // treated as "column absent". Fall back to the pre-surplus total only
                // when no surplus was consumed, i.e. when the DDL has not been run.
                // Same guard as chalanfinPreview.php.
                'surplus_consumed' => $num($head, 'ssd_surplus_consumed'),
                'invoice_amount'   => ($num($head, 'ssd_invoice_amount') > 0 || $num($head, 'ssd_surplus_consumed') > 0)
                    ? $num($head, 'ssd_invoice_amount')
                    : $num($head, 'ssd_total_payable'),
            ];
        }

        $hostelBlocks = [];
        if ($hostel->isNotEmpty()) {
            $head = $hostel[0];
            $actual = $num($head, 'sshd_hostel_fee_actual') ?: $num($head, 'sshd_hostel_fee');

            $scholarships = [];
            foreach ($hostel as $row) {
                if ($num($row, 'sshd_amount') <= 0) {
                    continue;
                }
                $scholarships[] = [
                    'name'   => $row->scholarship_name ?: ('Scholarship #' . $row->sshd_type),
                    'pct'    => $num($row, 'sshd_percentage'),
                    'amount' => $num($row, 'sshd_amount'),
                ];
            }

            $hostelBlocks[] = [
                'title'         => 'Hostel Challan',
                'category'      => 'Hostel_Fee',
                'lines'         => [[
                    'label'    => 'Hostel Fee',
                    'actual'   => $actual,
                    'discount' => $num($head, 'sshd_hostel_disc_amt'),
                    'payable'  => $num($head, 'sshd_hostel_payable'),
                ]],
                'scholarships'     => $scholarships,
                'total_actual'     => $num($head, 'sshd_total_fee_actual') ?: $actual,
                'total_disc'       => $num($head, 'sshd_total_disc_amt'),
                'total_payable'    => $num($head, 'sshd_total_payable'),
                // Hostel takes surplus too — same shared balance as academic.
                'surplus_consumed' => $num($head, 'sshd_surplus_consumed'),
                'invoice_amount'   => ($num($head, 'sshd_invoice_amount') > 0 || $num($head, 'sshd_surplus_consumed') > 0)
                    ? $num($head, 'sshd_invoice_amount')
                    : $num($head, 'sshd_total_payable'),
            ];
        }

        $missingColumns = $this->snapshotSchemaReport();

        // When nothing came back, say WHY: no rows at all for this student/session,
        // versus rows that exist but carry none of the snapshot figures because the
        // ALTER has not been run.
        $diagnostic = null;
        if (empty($academicBlocks) && empty($hostelBlocks)) {
            $anyRows = DB::table('Students_Scholarships_Details')
                ->where('SSD_APP_ID', $aai_id)->where('SSD_SESS_ID', $sessions)
                ->whereRaw("NVL(status, 'E') = 'E'")->count();

            if (!empty($missingColumns)) {
                $diagnostic = 'The snapshot columns are missing from the database, so the saved rows carry no amounts. '
                    . 'Run the ALTER TABLE script, then Save again. Missing: ' . implode(', ', $missingColumns) . '.';
            } elseif ($anyRows > 0) {
                $diagnostic = $anyRows . ' scholarship row(s) exist for this student and session but none is staged '
                    . '(SSD_BULK_POST_STATUS = PENDING and SSD_SOURCE_PAGE = ' . $this->sourcePage() . '). Save again to stage them.';
            } else {
                $diagnostic = 'No scholarship rows exist for this student and session.';
            }
        }

        return response()->json([
            'status'          => true,
            'message'         => '1',
            'sections'        => ['academic' => $academicBlocks, 'hostel' => $hostelBlocks],
            'diagnostic'      => $diagnostic,
            'missing_columns' => $missingColumns,
        ]);
    }

    /**
     * STEP 2 — Post.
     *
     * Reads the rows Save staged in the two intermediate tables and applies them to
     * student_challan. It deliberately does NOT take the grid payload: the figures
     * posted are the ones the grader acknowledged, not whatever the browser is
     * currently showing.
     *
     * Academic and hostel are separate challans and are posted separately — the
     * request names which of them to write. Anything left out keeps its PENDING
     * snapshot untouched, so it can be reviewed and posted on its own later.
     */
    public function postSnapshot(Request $request)
    {
        $sessions     = $request->input('academic_session');
        $aai_id       = $request->input('sch_aai_id');
        $enrollmentNo = $request->input('sch_enrollment_no');
        $studentType  = $request->input('student_type');
        // The client cannot reach Post without an accepted challan preview; this is the
        // server-side half of that gate, so a hand-crafted request cannot skip it either.
        $accepted = filter_var($request->input('accepted'), FILTER_VALIDATE_BOOLEAN);

        // Which challans to write. Absent means "both", so an older client keeps working.
        $requested = $request->input('sections');
        $requested = is_array($requested) ? array_map('strtolower', $requested) : ['academic', 'hostel'];
        $requested = array_values(array_intersect($requested, ['academic', 'hostel']));

        // The narrower authority: Full Semester and Additional Credit Hr are separate
        // challans inside the SAME section, so the section alone cannot say which of them
        // to post. An empty list means "every staged challan in the requested sections",
        // which is how a client that does not send categories keeps working.
        $requestedCategories = $request->input('categories');
        $requestedCategories = is_array($requestedCategories)
            ? array_values(array_filter(array_map('trim', $requestedCategories), 'strlen'))
            : [];

        if (empty($sessions) || empty($aai_id)) {
            return response()->json(['success' => false, 'messege' => 'Session and student are required.'], 400);
        }
        if (!$accepted) {
            return response()->json([
                'success' => false,
                'messege' => 'Preview the challan and click Accept before posting.',
            ], 400);
        }
        if (empty($requested)) {
            return response()->json([
                'success' => false,
                'messege' => 'Tick at least one challan to post.',
            ], 400);
        }

        $doAcademic = in_array('academic', $requested, true);
        $doHostel   = in_array('hostel', $requested, true);

        $enrollment = $this->resolveTypedIdentifier($enrollmentNo)['enrollment'];

        // The FULL staged rows — the posters read every snapshot column off them,
        // so selecting just the ids would post zeroes. Only the requested sections
        // are loaded; the other one is left staged and untouched.
        $academicStaged = $doAcademic
            ? $this->stagedAcademicQuery($aai_id, $sessions)
                ->select('ssd.*', DB::raw("NVL(ssd.SSD_CHALLAN_CATEGORY, 'Full_semester') as ssd_challan_category"))
                ->orderBy('ssd.SSD_ID')
                ->get()
            : collect();

        $hostelStaged = $doHostel
            ? $this->stagedHostelQuery($aai_id, $sessions)
                ->select('sshd.*')
                ->orderBy('sshd.SSHD_ID')
                ->get()
            : collect();

        // Narrow to the ticked challans. Done in PHP rather than in the query because the
        // hostel side has no category column — its whole staged set IS 'Hostel_Fee'.
        if (!empty($requestedCategories)) {
            $academicStaged = $academicStaged->filter(function ($row) use ($requestedCategories) {
                return in_array($row->ssd_challan_category, $requestedCategories, true);
            })->values();

            if (!in_array('Hostel_Fee', $requestedCategories, true)) {
                $hostelStaged = collect();
            }
        }

        if ($academicStaged->isEmpty() && $hostelStaged->isEmpty()) {
            return response()->json([
                'success' => false,
                'messege' => 'Nothing is staged for the selected challan. Click Save first, then Post.',
            ], 400);
        }

        // A category the student has outgrown is never posted, even if a stale PENDING row
        // survived from an earlier round. Without this the student would end up holding a
        // Full Semester challan alongside their Credit Hr one — the very pair
        // allowedCategoryCombinations() exists to forbid — and the surplus-freeze check
        // below would be governed by that stale row's frozen figures.
        $dormantForPost = $this->dormantCategoriesForStudent($aai_id, $sessions);

        $categories = [];
        foreach ($academicStaged as $row) {
            if (in_array($row->ssd_challan_category, $dormantForPost, true)) {
                continue;
            }
            $categories[$row->ssd_challan_category] = true;
        }

        $now  = Carbon::now('Asia/Karachi');
        $user = auth()->user()->username;

        DB::beginTransaction();
        try {
            // Stamp the acknowledgement before applying, so an aborted post still
            // records who authorised the figures. An empty array leaves that table
            // alone, which is how a section the grader did not tick stays untouched.
            // The academic challans actually being posted — $academicStaged is already
            // narrowed to the ticked ones and $categories drops the dormant ones, so the
            // stamp cannot reach a challan the grader left unticked.
            $ackCategories = array_keys($categories);

            $this->stampStagedRows($aai_id, $sessions,
                $doAcademic ? ['SSD_ACK_BY' => $user, 'SSD_ACK_AT' => $now] : [],
                $doHostel   ? ['SSHD_ACK_BY' => $user, 'SSHD_ACK_AT' => $now] : [],
                $ackCategories
            );

            // Write the challans from the frozen snapshot. No FIN_SCH_* procedure
            // runs — each category is handled by its own poster in
            // App\Services\StudentRecord\ChallanPosting, which documents the
            // procedure it replaces. fin_bulk_challan_cancellation is the only
            // procedure still called (from ChallanCanceller).
            $factory  = new ChallanPosterFactory();
            $student  = $this->resolveStudentForChallan($studentType, $enrollment, $aai_id, $sessions);
            $messages = [];
            $warnings = [];

            // Walked in academicCategories() order, which is the SAME order Save used
            // when it simulated the surplus split. Any other order and a two-challan
            // post would spend the surplus differently from the accepted preview.
            $work = [];
            foreach (array_keys($this->academicCategories()) as $challanCategory) {
                if (!isset($categories[$challanCategory])) {
                    continue;
                }
                $work[$challanCategory] = $academicStaged->filter(function ($r) use ($challanCategory) {
                    return $r->ssd_challan_category === $challanCategory;
                })->values();
            }
            if ($hostelStaged->isNotEmpty()) {
                $work['Hostel_Fee'] = $hostelStaged;
            }

            $adjuster = new SurplusAdjuster();

            // ── the surplus freeze check, done ONCE, before anything is cancelled ──
            //
            // Compares the pending balance Save saw against the pending balance now.
            // Both exclude credit that a cancellation will mint, so the comparison is
            // unaffected by walk order, by which challans were ticked, and by whether a
            // paid challan is about to be cancelled — it detects only what it is meant
            // to detect: somebody else moving this student's surplus in between.
            $frozenPending = null;
            foreach ($work as $challanCategory => $rows) {
                if ($rows->isEmpty()) {
                    continue;
                }
                $head = $rows[0];
                $column = ($challanCategory === 'Hostel_Fee') ? 'sshd_surplus_pending' : 'ssd_surplus_pending';
                if (isset($head->{$column})) {
                    $frozenPending = (float) $head->{$column};
                    break;
                }
            }

            if ($frozenPending !== null) {
                $livePending = $adjuster->pendingSurplus($aai_id, $enrollment);
                if (round($frozenPending, 2) !== round($livePending, 2)) {
                    throw new \Exception(
                        'This student\'s surplus changed since you previewed the challan '
                        . '(previewed Rs ' . number_format($frozenPending, 2)
                        . ', now Rs ' . number_format($livePending, 2) . '). '
                        . 'Click Save & Preview again to review the corrected amounts.'
                    );
                }
            }

            foreach ($work as $challanCategory => $rows) {
                $poster = $factory->make($challanCategory);
                if ($poster === null) {
                    throw new \Exception('No challan poster is defined for "' . $challanCategory . '".');
                }

                $existing = $this->findLiveChallan($aai_id, $enrollment, $sessions, $challanCategory);

                // The snapshot is the authority. Save froze the pair the grader accepted
                // onto these very rows, so re-deriving it here could hand the student a
                // deadline nobody approved — the installment row may have been edited in
                // the meantime, or a second lookup may simply disagree with the first.
                //
                // Falls back to resolving only when the snapshot carries no pair: rows
                // staged before add_scholarship_challan_date_columns.sql was run, which
                // must keep posting exactly as they did when they were staged.
                $dates = $this->snapshotChallanDates($rows, $challanCategory === 'Hostel_Fee')
                    ?: $this->resolveChallanDates($aai_id, $enrollment, $sessions, $studentType, $existing);

                $result = $poster->post(new PostContext([
                    'aaiId'           => $aai_id,
                    'enrollment'      => $enrollment,
                    'enrollmentRaw'   => $enrollmentNo,
                    'sessionId'       => $sessions,
                    'studentType'     => $studentType,
                    'category'        => $challanCategory,
                    'rows'            => $rows,
                    'student'         => $student,
                    'dueDate'         => $dates['due'],
                    'validDate'       => $dates['valid'],
                    'entryUser'       => $user,
                    'entryUserId'     => auth()->id(),
                    'sourcePage'      => $this->sourcePage(),
                    'existingChallan' => $existing,
                ]));

                // A poster refuses (e.g. a PAID credit-hour challan blocks reissue) —
                // that is fatal, the grader must resolve it before posting.
                if (!$result->ok) {
                    throw new \Exception($result->message);
                }

                $messages[] = $result->message;
                $warnings = array_merge($warnings, $result->warnings);

                // ── surplus (replaces FIN_PRO_ADJUST_CHALLAN_SURPLUS) ──
                // Applies to hostel as well as academic: fin_fee_adjustment is keyed on
                // the student with no academic/hostel dimension, so one balance serves
                // both. Save simulated the split in this same order.
                if ($result->challanId !== null) {
                    $isHostel = ($challanCategory === 'Hostel_Fee');
                    $head     = $rows->isNotEmpty() ? $rows[0] : null;

                    $payableCol  = $isHostel ? 'sshd_total_payable'     : 'ssd_total_payable';
                    $availCol    = $isHostel ? 'sshd_surplus_available' : 'ssd_surplus_available';
                    $consumedCol = $isHostel ? 'sshd_surplus_consumed'  : 'ssd_surplus_consumed';

                    $payable = ($head && isset($head->{$payableCol})) ? (float) $head->{$payableCol} : 0.0;
                    $frozen  = ($head && isset($head->{$availCol})) ? (float) $head->{$availCol} : null;

                    // Spend exactly what the grader accepted, not a fresh recomputation.
                    // This is what keeps a PARTIAL post honest: if only the hostel
                    // challan is posted, it must still take the slice the preview showed
                    // it taking, not swallow the academic challan's share as well.
                    $planned = ($head && isset($head->{$consumedCol}))
                        ? (float) $head->{$consumedCol}
                        : null;

                    $applied = $adjuster->apply(
                        $aai_id, $enrollment, $result->challanId, $payable, $user, auth()->id(), $planned
                    );

                    DB::table('student_challan')
                        ->where('id', $result->challanId)
                        ->update([
                            'SC_SURPLUS_AMOUNT'      => $applied['consumed'],
                            'INVOICEAMOUNT'          => $applied['invoice'],
                            'SC_UPDATED_BY_USERNAME' => $user,
                            'UPDATED_AT'             => $now,
                        ]);

                    // Keep the snapshot honest: the preview reads THESE columns, so if
                    // what was applied differs at all from what was frozen, the preview
                    // would otherwise keep showing the stale number.
                    if ($isHostel) {
                        $this->stampStagedHostel($aai_id, $sessions, [
                            'SSHD_SURPLUS_CONSUMED' => $applied['consumed'],
                            'SSHD_INVOICE_AMOUNT'   => $applied['invoice'],
                        ]);
                    } else {
                        $this->stampStagedCategory($aai_id, $sessions, $challanCategory, [
                            'SSD_SURPLUS_CONSUMED' => $applied['consumed'],
                            'SSD_INVOICE_AMOUNT'   => $applied['invoice'],
                        ]);
                    }

                    if ($applied['consumed'] > 0) {
                        $messages[] = 'Adjusted Rs ' . number_format($applied['consumed'], 2)
                            . ' of surplus against the ' . $poster->label() . ' challan.';
                    }
                    if ($frozen !== null && round($frozen, 2) !== round($applied['available'], 2)) {
                        // Save measured the balance AFTER cancelling, and Post measures it
                        // the same way, so these should agree exactly. A mismatch means
                        // something else moved the balance in between. Not fatal — the
                        // accepted figure is still what got spent — but worth saying.
                        // Known cause: fin_bulk_challan_cancellation sets EVERY
                        // fin_fee_adjustment row for the student to the running total, so
                        // a student holding more than one row has their balance
                        // multiplied on each cancellation.
                        $messages[] = 'Surplus available at Post was Rs '
                            . number_format($applied['available'], 2) . ', previewed Rs '
                            . number_format($frozen, 2) . '.';
                    }
                }

                $this->stampPostedChallanNo(
                    $aai_id, $sessions, $challanCategory,
                    $challanCategory === 'Hostel_Fee', $result->challanId, $result->challanNo
                );
            }

            if (!empty($warnings)) {
                $messages[] = 'Note: ' . implode('; ', array_unique($warnings)) . '.';
            }

            // Staging cleared only on success, and only for the CHALLANS just posted —
            // same semantics as ScholarshipFormController::clearBulkPostStatus().
            // Anything left out keeps SSD/SSHD_BULK_POST_STATUS = 'PENDING', which is what
            // lets the grader post Full Semester now and Additional Credit Hr later.
            $this->stampStagedRows($aai_id, $sessions,
                $doAcademic ? [
                    'SSD_BULK_POST_STATUS' => null,
                    'SSD_POSTED_BY'        => $user,
                    'SSD_POSTED_AT'        => $now,
                ] : [],
                $doHostel ? [
                    'SSHD_BULK_POST_STATUS' => null,
                    'SSHD_POSTED_BY'        => $user,
                    'SSHD_POSTED_AT'        => $now,
                ] : [],
                $ackCategories
            );

            DB::commit();

            $posted = [];
            if ($academicStaged->isNotEmpty()) { $posted[] = $academicStaged->count() . ' academic'; }
            if ($hostelStaged->isNotEmpty())   { $posted[] = $hostelStaged->count() . ' hostel'; }

            $message = 'Posted ' . implode(' and ', $posted) . ' row(s) to the student challan.';
            if (!empty($messages)) {
                $message .= ' ' . implode(' ', array_unique($messages));
            }

            return response()->json(['success' => true, 'message' => $message], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['success' => false, 'messege' => $e->getMessage()], 500);
        }
    }

    /**
     * Update the staged academic rows of ONE challan category.
     *
     * Used to write back what Post actually applied, so the snapshot the preview reads
     * cannot drift from the posted challan. Columns that do not exist yet are dropped.
     */
    private function stampStagedCategory($aai_id, $sessions, $challanCategory, array $update)
    {
        $update = $this->filterExistingColumns('Students_Scholarships_Details', $update);
        if (empty($update)) {
            return;
        }

        DB::table('Students_Scholarships_Details')
            ->where('SSD_APP_ID', $aai_id)
            ->where('SSD_SESS_ID', $sessions)
            ->whereRaw("NVL(SSD_CHALLAN_CATEGORY, ?) = ?", [$this->defaultChallanCategory(), $challanCategory])
            ->update($update);
    }

    /**
     * Due / valid dates of the challan this Save most recently cancelled, if any.
     *
     * Scoped to the student and session and taken newest-first, so a re-issue inherits
     * the deadlines of the challan it is replacing rather than being handed a fresh
     * installment pair. Returns null when nothing was cancelled or the table is absent.
     */
    private function lastCancelledChallanDates($aai_id, $enrollment, $sessions)
    {
        try {
            $row = DB::table('fin_student_cancel_challan')
                ->select('sc_due_date', 'sc_valid_date')
                ->where('sc_session_id', $sessions)
                ->where(function ($q) use ($aai_id, $enrollment) {
                    $q->where('sc_app_id', $aai_id);
                    if (!empty($enrollment)) {
                        $q->orWhereRaw('UPPER(TRIM(sc_enrollment_no)) = ?', [strtoupper(trim((string) $enrollment))]);
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

    /** The hostel equivalent of stampStagedCategory(). */
    private function stampStagedHostel($aai_id, $sessions, array $update)
    {
        $update = $this->filterExistingColumns('fin_students_scholarships_hostel_details', $update);
        if (empty($update)) {
            return;
        }

        DB::table('fin_students_scholarships_hostel_details')
            ->where('SSHD_APP_ID', $aai_id)
            ->where('SSHD_SESS_ID', $sessions)
            ->update($update);
    }

    /**
     * What the challan PREVIEW will show for due / valid date, and — when it will
     * fall back to today + 7 — enough context to name the programme in the warning.
     *
     * Deliberately NOT resolveChallanDates() below. That method is the posting rule
     * and consults the live challan and the cancelled challan before it ever reaches
     * fin_challan_installments; chalanfinPreview.php consults neither (its own comment
     * says "student_challan is deliberately NEVER queried"). This predicts the
     * preview, so it consults only what the preview consults.
     *
     * Identity resolution follows the renderer's order too — SAI by enrollment first,
     * AAI by app id on a miss (chalanfinPreview.php ~lines 1527-1563) — rather than
     * branching on student type the way resolveChallanDates() does.
     */
    private function previewDatesForStudent($sessions, $student_id, $aaiId, $studentType = null): array
    {
        if ($this->usesAdmissionInstallmentDates()) {
            // Same lookup Post will run for this grader, so the warning and the written
            // challan can never describe different rows.
            $lookup   = $this->admissionInstallmentLookup($aaiId, $sessions, $studentType);
            $schoolId = $lookup['school_id'];
            $deptId   = $lookup['dept_id'];
            $progId   = $lookup['prog_id'];

            // No applicant row at all is a failed lookup, not a claim that no installment
            // exists — so it reports ok => null and the page does not warn on it. When the
            // row is there, the availability probe's own three-valued answer and reason
            // pass straight through.
            $result = $lookup['found']
                ? $lookup['installment']
                : ['ok' => null, 'reason' => 'no_program'];
        } else {
            // Same derivation runSave() uses (see the $rawId lines there), so the value
            // matched here is the one Save will actually write and the preview will read.
            $enrollment = $this->resolveTypedIdentifier($student_id)['enrollment'];

            // Same resolver Post uses, so the warning and the written challan can never
            // describe different rows.
            $ids = $this->previewProgrammeAndFlag($enrollment, $aaiId);
            $schoolId = $ids['school_id'];
            $deptId   = $ids['dept_id'];
            $progId   = $ids['prog_id'];

            $result = $this->previewInstallmentAvailability($sessions, $schoolId, $deptId, $progId, $ids['flag']);
        }

        return [
            // Three-valued, deliberately: the page warns only on an explicit false.
            // A null (the lookup failed) must not be turned into a claim.
            'ok'            => $result['ok'],
            'reason'        => isset($result['reason']) ? $result['reason'] : null,
            'due_date'      => isset($result['due_date']) ? $result['due_date'] : null,
            'valid_date'    => isset($result['valid_date']) ? $result['valid_date'] : null,
            'fallback_date' => $this->previewFallbackDate(),
            'session_name'  => DB::table('academic_sessions')->where('sess_id', $sessions)->value('sess_name'),
            'school_name'   => $schoolId ? DB::table('schools')->where('school_id', $schoolId)->value('school_name') : null,
            'dept_name'     => $deptId ? DB::table('departments')->where('dept_id', $deptId)->value('dept_name') : null,
            'prog_name'     => $progId ? DB::table('programs')->where('prog_id', $progId)->value('prog_name') : null,
        ];
    }

    /**
     * Due / valid dates for a challan being written — resolved EXACTLY as the preview
     * resolves them, by the one shared method both now call.
     *
     * Two sources, chosen by WHO IS LOGGED IN, never by the student:
     *   - Admission office accounts  → admissionInstallmentDates(), FCI_FLAG = 'Admission'
     *   - everybody else             → challanDatesLikePreview(), unchanged
     * See ADMISSION_OFFICE_USERS for why that split exists.
     *
     * previewDatesForStudent() applies the identical branch, which is the whole point:
     * the pair shown at Save & Preview has to be the pair Post writes, or the grader
     * approves one document and issues another.
     *
     * Two of the three rules this used to apply are still gone on purpose, because the
     * preview applies neither and the posted challan must not disagree with the document
     * the grader approved: inheriting $existing's dates, and inheriting the cancelled
     * challan's dates on a re-issue. $existing is kept in the signature for the call
     * sites but is not consulted.
     *
     * The third — resolveFutureInstallmentPair(), narrowing by FCI_STUDENT_TYPE and the
     * admission round — is back, but only on the Admission branch, which is why
     * $studentType is read again.
     */
    private function resolveChallanDates($aai_id, $enrollment, $sessions, $studentType, $existing)
    {
        if ($this->usesAdmissionInstallmentDates()) {
            return $this->admissionInstallmentDates($aai_id, $sessions, $studentType);
        }

        return $this->challanDatesLikePreview($sessions, $enrollment, $aai_id);
    }

    /**
     * The due / valid pair Save froze onto these staged rows, or null when there is none.
     *
     * Any row of the group will do — Save writes the same pair to every one of them — but
     * they are scanned rather than reading rows[0], because a row re-staged by an older
     * Save (or by a page that predates these columns) can carry NULL while its siblings
     * carry the pair.
     *
     * Oracle hands DATE columns back in whatever NLS shape the connection carries, so the
     * value is normalised to Y-m-d here: that is the shape challanDatesLikePreview()
     * returns, and therefore the shape SC_DUE_DATE / SC_VALID_DATE already receive.
     *
     * @return array{due: string, valid: string}|null
     */
    private function snapshotChallanDates($rows, bool $isHostel)
    {
        if (empty($rows) || $rows->isEmpty()) {
            return null;
        }

        $dueCol   = $isHostel ? 'sshd_due_date'   : 'ssd_due_date';
        $validCol = $isHostel ? 'sshd_valid_date' : 'ssd_valid_date';

        foreach ($rows as $row) {
            $due   = $this->normaliseStoredDate($row, $dueCol);
            $valid = $this->normaliseStoredDate($row, $validCol);

            if ($due !== null && $valid !== null) {
                return ['due' => $due, 'valid' => $valid];
            }
        }

        return null;
    }

    /** One stored DATE off a staged row as Y-m-d, whatever case and NLS shape it arrives in. */
    private function normaliseStoredDate($row, $column)
    {
        $value = null;
        foreach ([$column, strtoupper($column)] as $candidate) {
            if (isset($row->{$candidate}) && trim((string) $row->{$candidate}) !== '') {
                $value = $row->{$candidate};
                break;
            }
        }

        if ($value === null) {
            return null;
        }

        try {
            return Carbon::parse($value)->toDateString();
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * The due / valid pair this Save is accepting, to be frozen onto the snapshot.
     *
     * Normally the resolved pair — the same one Post would have computed — so the dates
     * become part of the accepted snapshot for EVERY grader, not just the ones who see the
     * dialog. Recording it is the point: preview and Post then read one stored answer
     * instead of each re-deriving it and risking a different one.
     *
     * Admission-office accounts may overtype it in the Save & Preview dialog. That dialog
     * is client-side and its own docblock calls it advisory, so nothing it sends may be
     * trusted here: the override is honoured ONLY for an account this server agrees is an
     * Admission account, and the pair is re-validated regardless of what the browser
     * already checked.
     *
     * @throws \Exception on a pair the rules reject — caught by runSave()'s handler, which
     *                    rolls back, so a bad date can never leave a half-written snapshot.
     * @return array{due: string|null, valid: string|null} ISO Y-m-d
     */
    private function acceptedChallanDates(Request $request, $aai_id, $enrollment, $sessions, $studentType): array
    {
        $resolved = $this->resolveChallanDates($aai_id, $enrollment, $sessions, $studentType, null);

        if (!$this->usesAdmissionInstallmentDates()) {
            return $resolved;
        }

        $due   = $this->normaliseDateInput($request->input('due_date'));
        $valid = $this->normaliseDateInput($request->input('valid_date'));

        // Nothing usable submitted — keep the resolved pair rather than refusing the save.
        // An older cached page that predates the dialog still saves correctly.
        if ($due === null || $valid === null) {
            return $resolved;
        }

        $today = Carbon::now('Asia/Karachi')->startOfDay();

        // A past DUE date is accepted deliberately. The challan carries "Fine @ Rs.100/-
        // per day after the due date", and a challan re-issued after its original deadline
        // must KEEP that deadline — moving it forward would wipe the fine the student has
        // already accrued. So the operator is trusted with this one.
        //
        // The VALID date is the last day the bank will accept the challan, so one already
        // expired cannot be paid at all and is still refused. Today counts as ahead.
        if (Carbon::parse($valid)->startOfDay()->lt($today)) {
            throw new \Exception('The valid date cannot be in the past.');
        }

        // Strictly later, not merely not-earlier: an equal pair leaves a challan whose
        // deadline is also its last payable day, which is never what was meant.
        if (Carbon::parse($valid)->startOfDay()->lte(Carbon::parse($due)->startOfDay())) {
            throw new \Exception('The valid date must be later than the due date.');
        }

        return ['due' => $due, 'valid' => $valid];
    }

    /**
     * An operator-supplied date as ISO Y-m-d, or null when it is absent or unparsable.
     *
     * Deliberately strict about the shape rather than handing anything to Carbon::parse():
     * the field arrives from <input type="date">, which always emits Y-m-d, and a
     * slash-separated value would be read in American order by everything downstream.
     */
    private function normaliseDateInput($value)
    {
        $value = trim((string) $value);
        if ($value === '') {
            return null;
        }

        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            return null;
        }

        try {
            return Carbon::createFromFormat('Y-m-d', $value)->toDateString();
        } catch (\Throwable $e) {
            return null;
        }
    }

    /** Is the signed-in grader one of the Admission office accounts? */
    private function usesAdmissionInstallmentDates(): bool
    {
        $username = strtolower(trim((string) (auth()->user()->username ?? '')));

        return $username !== '' && in_array($username, self::ADMISSION_OFFICE_USERS, true);
    }

    /**
     * Due / valid dates as the Admission office issues them.
     *
     * Same table as the Registrar path, different row: FCI_FLAG = 'Admission', narrowed by
     * the page's Student Type and by the student's own admission round —
     * ADMISSION_APPLY_INFO.AAI_ADMISSION_SCH_ID → ADMISSION_SCHEDULES.ADMISSION_SCH_ROUND,
     * where 1 = early, 2 = regular, 3 = rolling. Both the raw round and the mapped word are
     * offered as candidates because FCI_ADMISSION_TYPE has been seen holding either.
     *
     * The flag is forced to 'Admission' rather than derived from the student the way
     * previewProgrammeAndFlag() derives it: which office's dates apply is a fact about the
     * grader, so an Existing student processed by Admission still gets Admission dates.
     *
     * School / dept / prog come from AAI_SDPO_* because that is the programme the admission
     * round was scheduled against, and it is the pairing FIN_CHALLAN_INSTALLMENTS is keyed
     * on for these rows.
     *
     * No match falls through to today + 7 — the same fallback the Registrar path uses, so a
     * missing installment row never blocks the grader.
     */
    private function admissionInstallmentDates($aai_id, $sessions, $studentType): array
    {
        $lookup = $this->admissionInstallmentLookup($aai_id, $sessions, $studentType);

        if (!empty($lookup['installment']['ok'])) {
            return [
                'due'   => $lookup['installment']['due_date'],
                'valid' => $lookup['installment']['valid_date'],
            ];
        }

        $fallback = $this->previewFallbackDate();

        return ['due' => $fallback, 'valid' => $fallback];
    }

    /**
     * The FCI_STUDENT_TYPE to match for this page's Student Type.
     *
     * The dropdown value is NOT what the column holds. Admission office accounts only see
     * "New without Registration", while the installment rows are stored against
     * 'New Admission FALL' — comparing the two matches nothing, which is exactly how a
     * programme with perfectly good dates reported "Due & Valid Dates Not Defined" and
     * fell back to today + 7.
     *
     * resolveFeeStructureStudentType() already performs this mapping for the fee
     * structure, including deriving the season from the selected session, so it is reused
     * rather than duplicated. It returns upper case; the caller compares case-insensitively.
     */
    private function admissionStudentType($studentType, $sessions)
    {
        return $this->resolveFeeStructureStudentType($studentType, $sessions);
    }

    /**
     * The raw Admission-office lookup: which programme was used, and what it found.
     *
     * Split out so the Post path and the Save & Preview warning run the SAME query. If
     * they diverged the page would promise one deadline and the challan would carry
     * another, which is the failure this whole branch exists to avoid.
     *
     * `found` is false when the student has no ADMISSION_APPLY_INFO row at all — a
     * different thing from having one with no matching installment, and the preview
     * reports the two differently.
     */
    private function admissionInstallmentLookup($aai_id, $sessions, $studentType): array
    {
        $info = DB::table('admission_apply_info')
            ->select(
                'AAI_SDPO_SCHOOL_ID as school_id', 'AAI_SDPO_DEPT_ID as dept_id',
                'AAI_SDPO_PROG_ID as prog_id', 'AAI_ADMISSION_SCH_ID as admission_sch_id'
            )
            ->where('AAI_ID', $aai_id)
            ->first();

        if (!$info) {
            return [
                'found' => false, 'school_id' => null, 'dept_id' => null,
                'prog_id' => null, 'installment' => null,
            ];
        }

        return [
            'found'       => true,
            'school_id'   => $info->school_id,
            'dept_id'     => $info->dept_id,
            'prog_id'     => $info->prog_id,
            // previewInstallmentAvailability(), NOT resolveFutureInstallmentPair(). The
            // latter takes ->first() off an unordered result and screens only the VALID
            // date, so with two rows it picks whichever Oracle returns first and can hand
            // back a due date already in the past — which fines the student the day they
            // receive the challan. This one reads every row's five pairs, requires BOTH
            // dates to still be ahead, and takes the earliest due date.
            'installment' => $this->previewInstallmentAvailability(
                $sessions,
                $info->school_id,
                $info->dept_id,
                $info->prog_id,
                'Admission',
                $this->admissionStudentType($studentType, $sessions),
                $this->admissionTypeCandidates($info->admission_sch_id)
            ),
        ];
    }

    /**
     * FCI_ADMISSION_TYPE values to accept for one admission schedule.
     *
     * Returns both the raw ADMISSION_SCH_ROUND and its word, e.g. ['2', 'regular'] — the
     * same pair the bulk page and the stored-procedure path build, so a site that stored
     * the number and a site that stored the word both match.
     */
    private function admissionTypeCandidates($admissionSchId): array
    {
        $round = DB::table('admission_schedules')
            ->where('ADMISSION_SCH_ID', $admissionSchId)
            ->value('ADMISSION_SCH_ROUND');

        // ADMISSION_SCH_ROUND is a NUMBER, and the driver may hand it back as 2, '2' or
        // '2.0' depending on NLS. Normalise before the map lookup: '2.0' would miss it,
        // leave 'regular' out of the candidates and silently strand the challan on
        // today + 7 with nothing to show why.
        $round = is_numeric($round) ? (string) (int) $round : trim((string) $round);

        $map = ['1' => 'early', '2' => 'regular', '3' => 'rolling'];

        return array_values(array_filter(array_unique([
            $round,
            isset($map[$round]) ? $map[$round] : $round,
        ]), 'strlen'));
    }

    /**
     * Header details for a challan: identity from the student tables, never from
     * student_challan, so a challan can be written (and previewed) for a student who
     * has none yet.
     */
    private function resolveStudentForChallan($studentType, $enrollment, $aai_id, $sessions)
    {
        $sessionName = DB::table('academic_sessions')->where('sess_id', $sessions)->value('sess_name');

        $row = null;
        if ($this->isEnrolledStudentType($studentType) && !empty($enrollment)) {
            $row = DB::table('students_apply_info as sai')
                ->leftJoin('students_personal_info as spi', 'sai.sai_spi_id', '=', 'spi.spi_id')
                ->leftJoin('schools as s', 'sai.sai_sdpo_school_id', '=', 's.school_id')
                ->leftJoin('departments as d', 'sai.sai_sdpo_dept_id', '=', 'd.dept_id')
                ->leftJoin('programs as p', 'sai.sai_sdpo_prog_id', '=', 'p.prog_id')
                ->whereRaw('LOWER(sai.sai_enrollment_no) = ?', [strtolower((string) $enrollment)])
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
                ->where('aai.aai_id', $aai_id)
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
            'name'            => ($row && $row->name) ? $row->name : '',
            'father'          => ($row && $row->father) ? $row->father : '',
            'guardian_cnic'   => ($row && $row->guardian_cnic) ? $row->guardian_cnic : '',
            'enrollment'      => ($row && $row->enrollment) ? $row->enrollment : $enrollment,
            'app_id'          => $aai_id,
            'programme'       => ($row && $row->prog_name) ? $row->prog_name : '',
            'school'          => ($row && $row->school_name) ? $row->school_name : '',
            'department'      => ($row && $row->dept_name) ? $row->dept_name : '',
            'session_name'    => $sessionName ?: '',
            'status'          => ($row && $row->student_status) ? $row->student_status : 'Active',
            // SC_ADMISSION_ROUND: the procedures mapped ADMISSION_SCH_ROUND 1/2/3 to
            // Early/Regular/Rolling before writing it onto the challan.
            'admission_round' => $this->resolveAdmissionRoundText($aai_id),
        ];
    }

    /** ADMISSION_SCH_ROUND 1/2/3 -> Early/Regular/Rolling, as the procedures did. */
    private function resolveAdmissionRoundText($aai_id)
    {
        $round = DB::table('admission_apply_info as aai')
            ->leftJoin('admission_schedules as sch', 'sch.admission_sch_id', '=', 'aai.aai_admission_sch_id')
            ->where('aai.aai_id', $aai_id)
            ->value('sch.admission_sch_round');

        $map = ['1' => 'Early', '2' => 'Regular', '3' => 'Rolling'];

        return isset($map[(string) $round]) ? $map[(string) $round] : null;
    }

    /** Record the challan each staged row produced, for traceability. */
    private function stampPostedChallanNo($aai_id, $sessions, $challanCategory, $isHostel, $newId, $challanNo)
    {
        // No number to stamp when the trigger assigned none. Writing NULL over the
        // staging rows would be worse than leaving them: the preview reads this column
        // to say which document the scholarships ended up on.
        if ($challanNo === null) {
            return;
        }

        if ($isHostel) {
            $update = $this->filterExistingColumns('fin_students_scholarships_hostel_details', ['SSHD_CHALLAN_NO' => $challanNo]);
            if (!empty($update)) {
                DB::table('fin_students_scholarships_hostel_details')
                    ->where('SSHD_APP_ID', $aai_id)
                    ->where('SSHD_SESS_ID', $sessions)
                    ->whereRaw("UPPER(TRIM(NVL(SSHD_SOURCE_PAGE, ''))) = ?", [strtoupper($this->sourcePage())])
                    ->update($update);
            }
            return;
        }

        $update = $this->filterExistingColumns('Students_Scholarships_Details', ['CHALLAN_NO' => $challanNo]);
        if (!empty($update)) {
            DB::table('Students_Scholarships_Details')
                ->where('SSD_APP_ID', $aai_id)
                ->where('SSD_SESS_ID', $sessions)
                ->whereRaw("NVL(SSD_CHALLAN_CATEGORY, 'Full_semester') = ?", [$challanCategory])
                ->whereRaw("UPPER(TRIM(NVL(SSD_SOURCE_PAGE, ''))) = ?", [strtoupper($this->sourcePage())])
                ->update($update);
        }
    }

    /**
     * Apply the same stamp to every staged row of this student + session.
     * Columns that do not exist yet (DDL not run) are dropped silently.
     */
    private function stampStagedRows($aai_id, $sessions, array $academicUpdate, array $hostelUpdate, array $academicCategories = [])
    {
        // Same guard as stagedAcademicQuery(): filter on the staging columns only
        // when they exist, otherwise the WHERE itself raises ORA-00904.
        $academicUpdate = $this->filterExistingColumns('Students_Scholarships_Details', $academicUpdate);
        if (!empty($academicUpdate)) {
            $q = DB::table('Students_Scholarships_Details')
                ->where('SSD_APP_ID', $aai_id)
                ->where('SSD_SESS_ID', $sessions);
            if ($this->tableColumnExists('Students_Scholarships_Details', 'SSD_BULK_POST_STATUS')) {
                $q->whereRaw("UPPER(TRIM(NVL(SSD_BULK_POST_STATUS, ''))) = ?", [$this->pendingStatus()]);
            }
            if ($this->tableColumnExists('Students_Scholarships_Details', 'SSD_SOURCE_PAGE')) {
                $q->whereRaw("UPPER(TRIM(NVL(SSD_SOURCE_PAGE, ''))) = ?", [$this->sourcePage()]);
            }
            // Narrow to the challans actually being posted. Without this, posting only Full
            // Semester would stamp the acknowledgement onto the Additional Credit Hr rows
            // too — recording that the grader authorised a challan they never accepted, and
            // (for the POSTED stamp) taking it out of the queue unposted. An empty list
            // keeps the old section-wide behaviour for callers that do not narrow.
            if (!empty($academicCategories)) {
                $placeholders = implode(',', array_fill(0, count($academicCategories), '?'));
                $q->whereRaw("NVL(SSD_CHALLAN_CATEGORY, 'Full_semester') IN ($placeholders)",
                    array_values($academicCategories));
            }
            $q->update($academicUpdate);
        }

        $hostelUpdate = $this->filterExistingColumns('fin_students_scholarships_hostel_details', $hostelUpdate);
        if (!empty($hostelUpdate)) {
            $q = DB::table('fin_students_scholarships_hostel_details')
                ->where('SSHD_APP_ID', $aai_id)
                ->where('SSHD_SESS_ID', $sessions);
            if ($this->tableColumnExists('fin_students_scholarships_hostel_details', 'SSHD_BULK_POST_STATUS')) {
                $q->whereRaw("UPPER(TRIM(NVL(SSHD_BULK_POST_STATUS, ''))) = ?", [$this->pendingStatus()]);
            }
            if ($this->tableColumnExists('fin_students_scholarships_hostel_details', 'SSHD_SOURCE_PAGE')) {
                $q->whereRaw("UPPER(TRIM(NVL(SSHD_SOURCE_PAGE, ''))) = ?", [$this->sourcePage()]);
            }
            $q->update($hostelUpdate);
        }
    }

    /**
     * School / department / program for the snapshot. Mirrors the lookup inside
     * resolveAcademicFeeStructure(): enrolled types by enrollment no, everyone else
     * by AAI_ID.
     */
    private function resolveStudentSdpo($studentType, $student_id)
    {
        if ($this->isEnrolledStudentType($studentType)) {
            return DB::table('students_apply_info')
                ->select('SAI_SDPO_SCHOOL_ID as school_id', 'SAI_SDPO_DEPT_ID as dept_id', 'SAI_SDPO_PROG_ID as prog_id')
                ->whereRaw('LOWER(SAI_ENROLLMENT_NO) = ?', [strtolower((string) $student_id)])
                ->first();
        }

        return DB::table('admission_apply_info')
            ->select('AAI_SDPO_SCHOOL_ID as school_id', 'AAI_SDPO_DEPT_ID as dept_id', 'AAI_SDPO_PROG_ID as prog_id')
            ->where('AAI_ID', $student_id)
            ->first();
    }

    /**
     * The hostel type actually assigned this session, for the snapshot. Same join
     * chain getHostelFeeStructure() uses to auto-detect the fee structure.
     */
    private function resolveHostelAssignmentType($aai_id, $enrollment, $sessions)
    {
        $assignment = (new HostelAssignmentResolver())->resolve($sessions, $aai_id, $enrollment);

        if (!$assignment['assigned']) {
            return null;
        }

        // Same shape the callers already read. HT_ID / HT_NAME can be null when the
        // assignment carries no bed — that is recorded honestly rather than guessed at,
        // and the fee type no longer depends on them.
        return (object) [
            'ht_id'   => $assignment['htId'],
            'ht_name' => $assignment['htName'],
        ];
    }

    /**
     * Insert / update one fin_other_scholarships row.
     *
     * On UPDATE only the four amount columns are written. entry_by / entry_username
     * / created_at are deliberately left alone: the Other Scholarship page filters
     * its listing by ENTRY_USERNAME against role groups, so re-stamping an existing
     * row with this grader's username would make it disappear for its original
     * owner. FOS_STIPEND is not managed by this page and is never touched.
     *
     * @return int 1 when a row was written, 0 when there was nothing to do.
     */
    private function persistArrearsRow($state, $aai_id, $enrollment, $sessions, $entryMode = null)
    {
        $columns = $this->arrearsComponentColumns();

        $existing = $this->findArrearsRow($aai_id, $enrollment, $sessions, $state['type'], $state['category']);

        // Arrears have always been money-only — fin_other_scholarships has no percentage
        // column at all — so the entry mode changes nothing about how they are computed.
        // It is recorded so the page can tell how the row was entered, and so the whole
        // save carries one consistent answer rather than two out of three tables.
        //
        // Unlike persistScholarshipRow(), this method writes raw: every other column here
        // has existed since the table did. FOS_ENTRY_MODE is the first that may not, so
        // both branches now go through filterExistingColumns() — without it, saving before
        // add_scholarship_entry_mode_columns.sql has run would fail with ORA-00904 rather
        // than degrading the way the rest of the page does.
        $modeColumn = $entryMode !== null
            ? $this->filterExistingColumns('fin_other_scholarships', ['FOS_ENTRY_MODE' => $entryMode])
            : [];

        if (!empty($existing)) {
            $update = [];
            foreach ($columns as $component => $column) {
                $update[$column] = $state['components'][$component];
            }
            // On the UPDATE path too: a row re-saved in the other mode must not keep the
            // mode it was first written under.
            DB::table('fin_other_scholarships')->where('id', $existing->id)->update($update + $modeColumn);
            return 1;
        }

        // Nothing to insert when every component is zero — do not create empty rows.
        if (array_sum($state['components']) <= 0) {
            return 0;
        }

        $insert = [
            'fos_app_id'            => $aai_id,
            'fos_enrollment_no'     => $enrollment,
            'fos_sess_id'           => $sessions,
            'fos_scholarship_type'  => $state['type'],
            'fos_challan_category'  => $state['category'],
            'fos_stipend'           => 0,
            'entry_by'              => auth()->user()->id,
            'entry_username'        => auth()->user()->username,
            'created_at'            => Carbon::now('Asia/Karachi'),
        ];
        foreach ($columns as $component => $column) {
            $insert[$column] = $state['components'][$component];
        }

        DB::table('fin_other_scholarships')->insert($insert + $modeColumn);
        return 1;
    }
}
