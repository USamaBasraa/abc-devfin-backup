<?php

namespace App\Http\Controllers\StudentFinancial;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Grader\StudentRecord\Concerns\ResolvesScholarshipFees;
use App\Http\Controllers\StudentFinancial\Support\MeritSnapshotRefresher;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Throwable;

/**
 * 18% Merit Scholarship calculation.
 *
 * Two inputs drive everything:
 *   1. Cohort              -> which intake sessions compete as ONE pool
 *                             (read from the ACADEMIC_COHORTS view)
 *   2. Scholarship Session -> which term the scholarship is being awarded for.
 *                             The results semester actually evaluated is
 *                             scholarship_session - EVAL_SESSION_OFFSET,
 *                             which is the "168 - 2" from the original query.
 *
 * Ranking is DENSE_RANK on CGPA within each grouped programme, so students on
 * the same CGPA always share a position and therefore always land in the same
 * scholarship slab.
 */
class MeritCalculationController extends Controller
{
    /**
     * Pricing for "Post Scholarships" comes from here rather than from a copy.
     *
     * resolveAcademicFeeStructure() is the SAME code StudentRecordRevisedController
     * and the Scholarship Bulk page price with, so a merit scholarship and a
     * hand-entered one on the same student in the same session can never disagree
     * about the tuition base. filterExistingColumns() is what lets the snapshot
     * columns be written without caring whether a DDL patch has been run yet.
     */
    use ResolvesScholarshipFees;

    /** Results semester = scholarship session - this. The "168 - 2" in the original query. */
    private const EVAL_SESSION_OFFSET = 2;

    /** Minimum graded credit hours in the evaluated semester. */
    private const MIN_CR_UNDERGRAD = 15;
    private const MIN_CR_OTHER     = 9;

    /** School excluded from merit scholarship. */
    protected const EXCLUDED_SCHOOL_ID = 6;

    /**
     * Where a verified merit list is stored. This is the table the old
     * PL/SQL procedure wrote to, one row per selected student.
     */
    private const RESULT_TABLE = 'BNUCMS.FIN_MERIT_18_PERCENT_RESULT';

    /**
     * The one and only source of programme grouping. Old + new scheme codes of
     * the same degree share a GRP_KEY and compete as a single pool; a group of
     * one is just a programme competing alone under a curated GRP_NAME. Edited
     * from the Combined Programmes page -- there is no hard-coded list any more.
     *
     * DeanHonorController still carries its own $combinedPrograms array
     * (L643-737); that module has not been moved to this table yet.
     */
    protected const MAP_TABLE = 'BNUCMS.FIN_MERIT_COMBINED_PROGRAMS';

    /**
     * Where "Post Scholarships" writes: the same table every other scholarship
     * page writes to, so a merit award reaches the challan exactly as a
     * hand-entered one does.
     */
    private const SSD_TABLE = 'students_scholarships_details';

    /** SCHOLARSHIP_TYPE.SCHOLARSHIP_ID for "Merit Base". */
    private const SSD_MERIT_TYPE = 2;

    /**
     * Merit is a tuition discount on the full-semester challan. Per-credit and
     * summer categories are never merit-awarded, so the category is fixed rather
     * than resolved per student.
     */
    private const SSD_CHALLAN_CATEGORY = 'Full_semester';

    /**
     * SSD_SOURCE_PAGE stamp — which page OWNS these rows once they are posted.
     *
     * 'SCHOLARSHIP_BULK' is ScholarshipFormController::sourcePage(), and sharing
     * it is deliberate: on that page ownership is what decides whether a row can
     * be WORKED ON, not merely displayed. Three rules read this column, and all
     * three skip anything carrying a foreign tag:
     *
     *   ScholarshipFormController::bulkSourceScopeSql()      (~L5144)
     *       AND UPPER(TRIM(NVL(ssd_source_page,''))) = 'SCHOLARSHIP_BULK'
     *       -> the Bulk Post queue and the list sign-off / acknowledgement
     *
     *   ScholarshipFormController::requeueChallanCategory()  (~L905)
     *       UPPER(TRIM(NVL(ssd_source_page,''))) IN ('', 'SCHOLARSHIP_BULK')
     *       -> re-queues a student's other awards when one is removed, so the
     *          re-issued challan is priced against what they actually still hold
     *
     *   BuildsScholarshipSnapshots::snapshotRowIsForeign()   (~L459)
     *       -> keeps the stamp, instead of standing off the row as somebody
     *          else's, whenever that page recomputes the student's snapshot
     *
     * A merit-specific tag ('MERIT_CALC') would still LIST on that page —
     * fetchAcademicScholarshipRows() filters on session, never on source page —
     * but every one of those three rules would then pass the row by, which is
     * the opposite of being able to check and work with it there.
     *
     * NOT 'RECORD_REVISED': that is ScholarshipFormController's
     * privilegedSourcePage(), and it would make the whole STUDENT read-only on
     * the bulk page, merit row and everything else they hold.
     *
     * Sharing the tag loses no provenance — SSD_PROJECT ('Merit_F2026'),
     * SSD_TYPE (2 = Merit Base) and SSD_DETAILS each name the merit run, and
     * FIN_MERIT_18_PERCENT_RESULT still holds the verified list behind it.
     *
     * The column is VARCHAR2(30); this value is 16 characters.
     */
    private const SSD_SOURCE_PAGE = 'SCHOLARSHIP_BULK';

    /**
     * SSD_PROJECT prefix. The session's own code is appended, so Fall 2026 posts
     * as 'Merit_F2026' -- one project per scholarship session, which is what
     * makes "has this session already been posted?" answerable with a COUNT.
     */
    private const SSD_PROJECT_PREFIX = 'Merit_';

    /**
     * SSD_BULK_POST_STATUS value that puts a row on the Bulk Post screen.
     *
     * Same string as ScholarshipFormController's pendingStatus(); that page's
     * queue compares it literally, so it is not ours to reword.
     */
    private const SSD_PENDING_STATUS = 'PENDING';

    /**
     * One row per programme id, straight off the mapping table.
     *
     * ROW_NUMBER is what keeps a programme that was listed in two groups by
     * mistake from multiplying StudentData rows: lowest GRP_KEY wins, which is
     * the same row loadStoredMapping() keeps, so the SQL and the PHP-side
     * diagnosis can never disagree about which group a student competes in.
     *
     * GRP_PROG_ID is the group's lowest programme id -- a NUMBER standing in for
     * the GRP_KEY, which is a string like '1026_1228'. RESULT_TABLE's
     * MAPPED_PROGRAM_ID is a NUMBER column that GenerateBulkChallanController
     * joins to PROGRAMS.PROG_ID, so the key itself can never be stored there.
     * MIN() is taken over the whole table, before the rn = 1 filter, so every
     * member of a group gets the same number however the duplicates fall.
     */
    private const PROG_GROUP_MAP_SQL = 'SELECT grp_key, grp_name, prog_id, grp_prog_id
      FROM (
          SELECT grp_key,
                 grp_name,
                 prog_id,
                 MIN(prog_id) OVER (PARTITION BY grp_key) AS grp_prog_id,
                 ROW_NUMBER() OVER (
                     PARTITION BY prog_id ORDER BY grp_key, sort_order
                 ) AS rn
          FROM ' . self::MAP_TABLE . '
      )
     WHERE rn = 1';

    /**
     * A student is on the merit list when the rank is inside the programme
     * cut-off AND the CGPA floor is met. Kept as one fragment because the
     * on-screen query and the INSERT ... SELECT must agree exactly -- a student
     * may never be shown one slab and saved with another.
     */
    private const MERIT_FILTER_SQL = 'embs.scholarship_rank <= embs.cutoff_rank
                                      AND embs.meets_floor = 1';

    private const SLAB_CASE_SQL = "CASE
                  WHEN embs.scholarship_rank <= embs.slab_75_limit THEN '75%'
                  WHEN embs.scholarship_rank <= embs.slab_50_limit THEN '50%'
                  ELSE '25%'
              END";

    private const DEFAULT_TOP_PCT     = 18;
    private const DEFAULT_SLAB_75_PCT = 15;
    private const DEFAULT_SLAB_50_PCT = 25;
    private const DEFAULT_CGPA_FLOOR  = 3;

    /**
     * Oldest cohort offered in the dropdown. ACADEMIC_COHORTS goes back to
     * Cohort_2022; merit scholarships are only run for 2024 onwards.
     * A cohort whose year cannot be parsed is kept rather than hidden.
     */
    protected const MIN_COHORT_YEAR = 2024;

    /**
     * Merit scholarship is awarded once a year, in the Fall term, so only Fall
     * sessions are offered as the Scholarship Session. This also keeps the
     * EVAL_SESSION_OFFSET reliable: every Fall session minus 2 is the preceding
     * Spring (Summer sits in between).
     *   Fall 2026 (168) -> Spring 2026 (166)
     *   Fall 2025 (165) -> Spring 2025 (163)
     *   Fall 2024 (162) -> Spring 2024 (160)
     *
     * MIN_SCHOLARSHIP_YEAR was 2024 while the page was being built, which put
     * Fall 2024 and Fall 2025 in the dropdown as well. Those two awards were
     * made by the old PL/SQL procedure and are not to be run again from here, so
     * the floor is 2026: Fall 2026 is the only session offered today, and a
     * later Fall is picked up automatically the year it is created.
     *
     * The floor gates the ENDPOINTS too, not just the dropdown -- see
     * resolveParams(), which rejects a scholarship session sessionList() does not
     * offer. Both read this constant, so widening it widens both together.
     */
    protected const SCHOLARSHIP_SESSION_PREFIX = 'FALL';
    protected const MIN_SCHOLARSHIP_YEAR       = 2026;

    /** @var array<string, array{ids: int[], name: string}>|null */
    protected $combinedProgramsCache = null;

    /**
     * Resolved fee structures, keyed session|enrollment.
     *
     * A post runs resolveAcademicFeeStructure() once per student; the cache is
     * what keeps a re-run of the same request from paying for it twice.
     *
     * @var array<string, array{fee: array|null, student_type: string, message: string}>
     */
    private $meritFeeCache = [];

    public function index()
    {
        $cohorts = $this->cohortMap();

        return view('StudentFinancial.meritCalculation', [
            'cohorts'        => $cohorts,
            'sessions'       => $this->sessionList(),        // Fall only, for Scholarship Session
            'resultSessions' => $this->resultSessionList(),  // all, for the override
            'sessionNames'   => $this->sessionNameMap(),
            'defaults' => [
                'top_pct'     => self::DEFAULT_TOP_PCT,
                'slab_75_pct' => self::DEFAULT_SLAB_75_PCT,
                'slab_50_pct' => self::DEFAULT_SLAB_50_PCT,
                'cgpa_floor'  => self::DEFAULT_CGPA_FLOOR,
                'floor_mode'  => 'after',
                'offset'      => self::EVAL_SESSION_OFFSET,
            ],
        ]);
    }

    public function calculate(Request $request)
    {
        $resolved = $this->resolveParams($request);

        if (isset($resolved['error'])) {
            return $resolved['error'];
        }

        $p       = $resolved['params'];
        $cohorts = $resolved['cohorts'];

        try {
            $rows = $this->runMeritQuery(
                $p['sess_ids'],
                $p['eval_session'],
                $p['top_pct'],
                $p['slab_75_pct'],
                $p['slab_50_pct'],
                $p['cgpa_floor'],
                $p['floor_mode']
            );
        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'success' => false,
                'message' => 'Calculation failed: ' . $e->getMessage(),
            ], 500);
        }

        $sessionNames = $this->sessionNameMap();
        $programs     = $this->buildProgramSummary($rows);
        $flag         = $this->defaultFlag($p['cohort']);

        return response()->json([
            'success' => true,
            'context' => [
                // Session ids stay in the payload for traceability but the page
                // renders the *_name fields -- ids are never shown to the user.
                'cohort'                   => str_replace('_', ' ', $p['cohort']),
                'cohort_label'             => $cohorts[$p['cohort']]['label'],
                'cohort_sessions'          => $p['sess_ids'],
                'cohort_session_names'     => $cohorts[$p['cohort']]['session_names'],
                'scholarship_session'      => $p['scholarship_session'],
                'scholarship_session_name' => $sessionNames[(string) $p['scholarship_session']]
                                              ?? ('Session ' . $p['scholarship_session']),
                'eval_session'             => $p['eval_session'],
                'eval_session_name'        => $sessionNames[(string) $p['eval_session']]
                                              ?? ('Session ' . $p['eval_session']),
                'top_pct'             => round($p['top_pct'] * 100, 2),
                'slab_75_pct'         => round($p['slab_75_pct'] * 100, 2),
                'slab_50_pct'         => round($p['slab_50_pct'] * 100, 2),
                'cgpa_floor'          => $p['cgpa_floor'],
                'floor_mode'          => $p['floor_mode'],
                // What "Verified - Add Students" would write, and what is
                // already sitting in the result table for that same key.
                'flag'                => $flag,
                'saved_count'         => $this->savedCount($p['scholarship_session'], $flag),
            ],
            // Every cohort in the dropdown against THIS scholarship session, so
            // the page can say which ones are still outstanding and lock the
            // ones already verified. Re-sent on every calculate so the panel can
            // never go stale behind a save someone else made.
            'cohort_status' => $this->cohortStatus($p['scholarship_session']),
            'summary'  => $this->buildSummary($rows, $programs),
            'programs' => $programs,
            'rows'     => $rows,
        ]);
    }

    /**
     * "Verified -- Add Students": store the merit list in RESULT_TABLE.
     *
     * The list is NOT taken from the browser. The same calculation is re-run
     * here and written straight to the table with one INSERT ... SELECT, so
     * what gets stored is always what the SQL says, never what a page posted.
     *
     * Rows written: only students on the merit list (rank inside the programme
     * cut-off and CGPA floor met) -- i.e. exactly the "ON MERIT LIST" tile.
     */
    public function saveVerified(Request $request)
    {
        $resolved = $this->resolveParams($request);

        if (isset($resolved['error'])) {
            return $resolved['error'];
        }

        $p = $resolved['params'];

        $flagValidator = Validator::make($request->all(), [
            'flag' => ['nullable', 'string', 'max:100'],
        ]);

        if ($flagValidator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $flagValidator->errors()->first(),
            ], 422);
        }

        $flag = trim((string) $request->input('flag', ''));
        if ($flag === '') {
            $flag = $this->defaultFlag($p['cohort']);
        }

        $entryUser = substr((string) (auth()->user()->username ?? 'system'), 0, 100);

        // ------------------------------------------------------------------
        // A cohort is verified ONCE per scholarship session. There is no
        // "replace" path on purpose: the saved list is what the scholarships
        // are posted from, and re-running the calculation months later against
        // drifted CGPAs would silently award a different set of students than
        // the one that was signed off.
        //
        // Undoing a verification is therefore a deliberate database action:
        //   DELETE FROM BNUCMS.FIN_MERIT_18_PERCENT_RESULT
        //    WHERE SCH_SESSION_ID = <session> AND FLAG = '<flag>';
        // ------------------------------------------------------------------
        $existing = $this->savedCount($p['scholarship_session'], $flag);

        if ($existing > 0) {
            $status = $this->cohortStatusFor($p['scholarship_session'], $p['cohort']);

            return response()->json([
                'success'  => false,
                'code'     => 'already_verified',
                'existing' => $existing,
                'flag'     => $flag,
                'status'   => $status,
                'message'  => $this->alreadyVerifiedMessage($status),
            ], 409);
        }

        [$cte, $bindings] = $this->buildMeritCte(
            $p['sess_ids'],
            $p['eval_session'],
            $p['top_pct'],
            $p['slab_75_pct'],
            $p['slab_50_pct'],
            $p['cgpa_floor'],
            $p['floor_mode']
        );

        $filter = self::MERIT_FILTER_SQL;
        $slab   = self::SLAB_CASE_SQL;
        $table  = self::RESULT_TABLE;

        // The bind order is the order the placeholders appear in the text:
        // the whole WITH block first, then the three literals in the SELECT
        // list of the INSERT.
        $bindings[] = $p['scholarship_session'];
        $bindings[] = $entryUser;
        $bindings[] = $flag;

        $sql = <<<SQL
INSERT INTO {$table}
(
    SCH_SESSION_ID, ORIGINAL_PROG_ID, MAPPED_PROGRAM_ID, PROG_NAME, COMBINED_PROGRAM_NAME,
    SAI_ENROLLMENT_NO, SAI_SDPO_SCHOOL_ID, SCHOOL_CODE, SAI_SDPO_DEPT_ID, DEPT_NAME,
    SAI_SDPO_SESS_ID, SAI_STUDENT_STATUS, AAI_ID, SPI_NAME, SPI_FATHERNAME,
    SPI_GUARDIAN_CNIC, CALCULATED_CGPA, SCHOLARSHIP_RANK, TOTAL_STUDENTS, CUTOFF_RANK,
    MBS_SLAB, ENTRY_USER, FLAG
)
{$cte}
SELECT
    ?,
    embs.original_prog_id,
    -- NOT mapped_program_id: that is the GRP_KEY string ('1026_1228') and this
    -- column is a NUMBER, so it used to fail the whole INSERT with ORA-01722.
    embs.mapped_prog_num,
    embs.prog_name,
    embs.combined_program_name,
    embs.sai_enrollment_no,
    embs.sai_sdpo_school_id,
    embs.school_code,
    embs.sai_sdpo_dept_id,
    embs.dept_name,
    embs.sai_sdpo_sess_id,
    embs.sai_student_status,
    embs.aai_id,
    embs.spi_name,
    embs.spi_fathername,
    embs.spi_guardian_cnic,
    embs.calculated_cgpa,
    embs.scholarship_rank,
    embs.total_students,
    embs.cutoff_rank,
    {$slab},
    ?,
    ?
FROM EligibleMBS embs
WHERE {$filter}
SQL;

        try {
            DB::beginTransaction();

            DB::insert($sql, $bindings);

            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            report($e);

            return response()->json([
                'success' => false,
                'message' => 'Save failed: ' . $e->getMessage(),
            ], 500);
        }

        // Counted back from the table rather than trusting the driver's
        // affected-row count, which oci8 does not always report.
        $saved = $this->savedCount($p['scholarship_session'], $flag);

        return response()->json([
            'success'       => true,
            'saved'         => $saved,
            'flag'          => $flag,
            'entry_user'    => $entryUser,
            'cohort_status' => $this->cohortStatus($p['scholarship_session']),
            'message'       => $saved . ' student(s) verified and saved. This cohort is now '
                             . 'locked for this scholarship session.',
        ]);
    }

    /**
     * "Post Scholarships": turn a verified merit list into scholarship rows.
     *
     * One row per saved student in STUDENTS_SCHOLARSHIPS_DETAILS, priced the
     * same way StudentRecordRevisedController prices a hand-entered award:
     *
     *   fee structure (Full_semester) -> amountBase
     *   SSD_AMOUNT = slab% / 100 * amountBase
     *
     * The percentage comes from the SAVED list, never from the browser and never
     * from a fresh calculation -- posting must award exactly what was verified.
     *
     * Idempotent: a student who already has a merit row for this session and
     * project is skipped, so a half-finished run can simply be repeated.
     */
    public function postScholarship(Request $request)
    {
        $resolved = $this->resolveParams($request);

        if (isset($resolved['error'])) {
            return $resolved['error'];
        }

        $p = $resolved['params'];

        $flag = trim((string) $request->input('flag', ''));
        if ($flag === '') {
            $flag = $this->defaultFlag($p['cohort']);
        }

        $session = (int) $p['scholarship_session'];
        $rows    = $this->savedMeritRows($session, $flag);

        if (empty($rows)) {
            return response()->json([
                'success' => false,
                'code'    => 'nothing_saved',
                'message' => 'Nothing to post for this cohort. Click "Verified — Add Students" first.',
            ], 422);
        }

        $project    = $this->scholarshipProject($session);
        $entryUser  = substr((string) (auth()->user()->username ?? 'system'), 0, 100);
        $savedAt    = Carbon::now('Asia/Karachi');
        $cohortName = str_replace('_', ' ', $p['cohort']);

        $skipped  = 0;
        $unpriced = 0;
        $overCap  = 0;
        $warnings = [];
        $work     = [];

        // What each student already holds on the full-semester challan, so a
        // merit award that pushes them past 100% is reported rather than found
        // later on the Scholarship Bulk page, which enforces that cap itself.
        $heldPct = $this->heldCategoryPercentages($session);

        // ------------------------------------------------------------------
        // Pass 1 -- read only. Pricing a few hundred students runs several
        // queries each, and none of it writes anything, so it is deliberately
        // done BEFORE the transaction opens: an open transaction held for the
        // whole run would lock rows for no reason and is far more likely to be
        // cut short by max_execution_time half way through.
        // ------------------------------------------------------------------
        foreach ($rows as $row) {
            $enrollment = strtoupper(trim((string) $row['sai_enrollment_no']));
            $percentage = $this->slabPercentage($row['mbs_slab']);

            if ($enrollment === '' || $percentage <= 0) {
                $warnings[] = ($enrollment !== '' ? $enrollment : 'a saved row')
                            . ': no usable scholarship slab — skipped.';
                $skipped++;
                continue;
            }

            if ($this->meritScholarshipExists($session, $project, $enrollment)) {
                $skipped++;
                continue;
            }

            $fee = $this->resolveMeritFee($session, $enrollment);

            if ($fee['fee'] === null) {
                $unpriced++;
                // Posted anyway, with the percentage but no amount: the award is
                // real, only its rupee value is unknown until the fee structure
                // exists. Same call the bulk page makes.
                $warnings[] = $enrollment . ': ' . $fee['message'] . ' (posted, but not priced).';
            }

            // Reported, never blocked: the merit list is policy and it is not this
            // page's place to decide which of a student's awards gives way. The
            // challan itself is safe either way -- every consumer caps the tuition
            // discount at the tuition base -- but the record needs a human look.
            $held = (float) ($heldPct[$enrollment] ?? 0);
            if ($held > 0 && ($held + $percentage) > 100) {
                $overCap++;
                $warnings[] = $enrollment . ': already holds ' . $this->trimNumber($held)
                            . '% on the full-semester challan; +' . $this->trimNumber($percentage)
                            . '% merit takes it to ' . $this->trimNumber($held + $percentage)
                            . '% (posted — review on the Scholarship Bulk page).';
            }

            $work[] = ['row' => $row, 'percentage' => $percentage, 'fee' => $fee];
        }

        // ------------------------------------------------------------------
        // Pass 2 -- the writes, all or nothing. A half-posted cohort would be
        // recoverable (the run is idempotent) but it would still show a wrong
        // "x of y posted" on the page until somebody noticed.
        // ------------------------------------------------------------------
        $posted = 0;

        try {
            DB::beginTransaction();

            // Rows an earlier run wrote are skipped by the loop below — they are
            // already posted — so this is the only place they can be brought up
            // to what the Scholarship Bulk page needs. Re-running Post repairs
            // them; it never re-posts them.
            $repaired = $this->alignMeritRowsForBulkPage($session, $project);

            foreach ($work as $item) {
                $this->insertMeritScholarshipRow(
                    $item['row'], $session, $project, $item['percentage'],
                    $item['fee'], $entryUser, $savedAt, $cohortName
                );

                $posted++;
            }

            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            report($e);

            return response()->json([
                'success' => false,
                'message' => 'Posting failed: ' . $e->getMessage(),
            ], 500);
        }

        // ------------------------------------------------------------------
        // Pass 3 -- re-freeze each student's challan snapshot.
        //
        // A merit row changes figures that describe the WHOLE challan, and they
        // cannot be computed while inserting one row: SSD_TUITION_DISC_AMT and
        // SSD_TOTAL_* are sums over every award the student holds. Without this
        // the Bulk Post screen shows the merit students as "total mismatch"
        // (awards say 75%, the stamp still says the 50% they had before) or
        // "cannot post" (merit-only students had no SSD_TOTAL_FEE_ACTUAL at all).
        //
        // Runs over the WHOLE saved list, not just the rows inserted a moment
        // ago, because rows an earlier run wrote have the same gap.
        //
        // Deliberately outside the transaction above, and one student at a time:
        // the money is already committed, and a snapshot that fails for one
        // student is a figure to recompute, not a reason to unpost a cohort.
        // ------------------------------------------------------------------
        $snapshot = $this->refreshChallanSnapshots($session, $rows, $warnings);

        $message = $posted . ' scholarship(s) posted'
                 . ($skipped > 0 ? ', ' . $skipped . ' already posted and skipped' : '')
                 . ($unpriced > 0 ? ', ' . $unpriced . ' without a fee structure (percentage only)' : '')
                 . ($overCap > 0 ? ', ' . $overCap . ' now over 100% with an existing scholarship' : '')
                 . ($repaired['restamped'] > 0
                     ? ', ' . $repaired['restamped'] . ' earlier row(s) handed to the Scholarship Bulk page' : '')
                 . ($repaired['queued'] > 0
                     ? ', ' . $repaired['queued'] . ' earlier row(s) added to its post queue' : '')
                 . '. ' . $snapshot['refreshed'] . ' challan snapshot(s) recomputed'
                 . ($snapshot['failed'] > 0 ? ', ' . $snapshot['failed'] . ' could not be' : '')
                 . '.';

        return response()->json([
            'success'       => true,
            'posted'        => $posted,
            'skipped'       => $skipped,
            'unpriced'      => $unpriced,
            'over_cap'      => $overCap,
            'restamped'     => $repaired['restamped'],
            'queued'        => $repaired['queued'],
            'refreshed'     => $snapshot['refreshed'],
            'refresh_failed' => $snapshot['failed'],
            'project'       => $project,
            'flag'          => $flag,
            // Capped so a whole cohort missing its fee structure cannot return a
            // few hundred identical lines. The page scrolls this list and shows
            // warning_count alongside, so a trimmed list is never mistaken for
            // the full one.
            'warnings'      => array_slice($warnings, 0, 100),
            'warning_count' => count($warnings),
            'cohort_status' => $this->cohortStatus($session),
            'message'       => $message,
        ]);
    }

    /**
     * Per-cohort verify / post state for one scholarship session.
     *
     * Answers the two questions the page asks: which cohorts in the dropdown
     * still have to be run for this session, and which are already locked.
     *
     * Driven off RESULT_TABLE.FLAG, which defaultFlag() derives from the cohort,
     * so a cohort that has never been verified simply has no rows and comes back
     * as 'pending'. Rows left by the old PL/SQL procedure carry a flag no cohort
     * maps to ('From_Fall 2024') and are ignored rather than shown as a mystery
     * cohort.
     *
     * @return array<int, array>
     */
    private function cohortStatus(int $scholarshipSession): array
    {
        $cohorts = $this->cohortMap();
        $project = $this->scholarshipProject($scholarshipSession);

        // flag -> saved / posted / who / when, in one pass over the saved rows.
        $byFlag = [];

        try {
            $sql = 'SELECT r.flag AS flag,
                           COUNT(*) AS saved,
                           SUM(CASE WHEN ssd.ssd_id IS NULL THEN 0 ELSE 1 END) AS posted,
                           MAX(r.entry_user) AS entry_user,
                           -- Formatted in SQL: the driver hands TIMESTAMPs back in
                           -- whatever NLS format the session is set to, which is not
                           -- something a PHP date parser should be guessing at.
                           TO_CHAR(MAX(r.entry_datetime), \'DD-Mon-YYYY HH24:MI\') AS entry_datetime
                      FROM ' . self::RESULT_TABLE . ' r
                      LEFT JOIN ' . self::SSD_TABLE . ' ssd
                        ON UPPER(TRIM(ssd.ssd_sai_enrollment_no)) = UPPER(TRIM(r.sai_enrollment_no))
                       -- SSD_SESS_ID is VARCHAR2; compared as text so a stray
                       -- non-numeric row can never raise ORA-01722 here.
                       AND TRIM(ssd.ssd_sess_id) = TO_CHAR(r.sch_session_id)
                       AND ssd.ssd_type    = ?
                       AND ssd.ssd_project = ?
                       AND ssd.status      = ?
                     WHERE r.sch_session_id = ?
                     GROUP BY r.flag';

            foreach (DB::select($sql, [self::SSD_MERIT_TYPE, $project, 'E', $scholarshipSession]) as $row) {
                $r = array_change_key_case((array) $row, CASE_LOWER);
                $byFlag[trim((string) $r['flag'])] = [
                    'saved'          => (int) $r['saved'],
                    'posted'         => (int) $r['posted'],
                    'entry_user'     => trim((string) ($r['entry_user'] ?? '')),
                    'entry_datetime' => $r['entry_datetime'] ?? null,
                ];
            }
        } catch (Throwable $e) {
            // A missing table must never take the calculation page down; the
            // panel then simply reports every cohort as not verified.
            report($e);
        }

        $status = [];

        foreach ($cohorts as $key => $cohort) {
            $flag = $this->defaultFlag($key);
            $hit  = $byFlag[$flag] ?? ['saved' => 0, 'posted' => 0, 'entry_user' => '', 'entry_datetime' => null];

            $saved  = (int) $hit['saved'];
            $posted = (int) $hit['posted'];

            if ($saved === 0) {
                $state = 'pending';
            } elseif ($posted === 0) {
                $state = 'verified';
            } elseif ($posted >= $saved) {
                $state = 'posted';
            } else {
                $state = 'part_posted';
            }

            $status[] = [
                'cohort'      => $key,
                'name'        => str_replace('_', ' ', $key),
                'label'       => $cohort['label'],
                'flag'        => $flag,
                'saved'       => $saved,
                'posted'      => $posted,
                'state'       => $state,
                'verified_by' => $hit['entry_user'],
                'verified_at' => $this->formatStamp($hit['entry_datetime']),
            ];
        }

        return $status;
    }

    /** One cohort's row out of cohortStatus(), or a pending stub if it is unknown. */
    private function cohortStatusFor(int $scholarshipSession, string $cohort): array
    {
        foreach ($this->cohortStatus($scholarshipSession) as $entry) {
            if ($entry['cohort'] === $cohort) {
                return $entry;
            }
        }

        return [
            'cohort'      => $cohort,
            'name'        => str_replace('_', ' ', $cohort),
            'label'       => str_replace('_', ' ', $cohort),
            'flag'        => $this->defaultFlag($cohort),
            'saved'       => 0,
            'posted'      => 0,
            'state'       => 'pending',
            'verified_by' => '',
            'verified_at' => '',
        ];
    }

    /** The refusal the page shows when someone tries to verify a locked cohort. */
    private function alreadyVerifiedMessage(array $status): string
    {
        $who  = $status['verified_by'] !== '' ? ' by ' . $status['verified_by'] : '';
        $when = $status['verified_at'] !== '' ? ' on ' . $status['verified_at'] : '';

        return $status['name'] . ' is already verified for this scholarship session — '
             . $status['saved'] . ' student(s) saved' . $who . $when . '. '
             . 'A cohort can only be verified once per session, so there is nothing more to add.';
    }

    /**
     * Read-only, on-demand version of the panel: the page calls this the moment a
     * scholarship session is picked, before anything has been calculated.
     */
    public function verificationStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'scholarship_session' => ['required', 'integer', 'min:1'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $session      = (int) $request->input('scholarship_session');
        $sessionNames = $this->sessionNameMap();

        return response()->json([
            'success'                  => true,
            'scholarship_session'      => $session,
            'scholarship_session_name' => $sessionNames[(string) $session] ?? ('Session ' . $session),
            'project'                  => $this->scholarshipProject($session),
            'cohort_status'            => $this->cohortStatus($session),
        ]);
    }

    /**
     * SSD_PROJECT for a scholarship session: 'Merit_' + the session's own code,
     * e.g. Fall 2026 (code F2026) -> 'Merit_F2026'.
     *
     * The id is the fallback for a session with no code, so the value is always
     * unique per session and never collides with the 'SCH_PRO_NEW' /
     * 'AFTER24_SCH_POLICY' / 'From_Fall 2024' projects other pages own.
     */
    private function scholarshipProject(int $scholarshipSession): string
    {
        $code = '';

        try {
            $code = strtoupper(trim((string) DB::table('academic_sessions')
                ->where('sess_id', $scholarshipSession)
                ->value('sess_code')));
        } catch (Throwable $e) {
            report($e);
        }

        if ($code === '') {
            $code = (string) $scholarshipSession;
        }

        return substr(self::SSD_PROJECT_PREFIX . $code, 0, 255);
    }

    /** The verified merit list for one session + flag, exactly as it was saved. */
    private function savedMeritRows(int $scholarshipSession, string $flag): array
    {
        try {
            $rows = DB::select(
                'SELECT sai_enrollment_no, aai_id, mbs_slab, sai_sdpo_school_id,
                        sai_sdpo_dept_id, original_prog_id, prog_name, combined_program_name,
                        scholarship_rank, calculated_cgpa
                   FROM ' . self::RESULT_TABLE . '
                  WHERE sch_session_id = ? AND flag = ?
                  ORDER BY combined_program_name, scholarship_rank, sai_enrollment_no',
                [$scholarshipSession, $flag]
            );
        } catch (Throwable $e) {
            report($e);

            return [];
        }

        return array_map(function ($row) {
            return array_change_key_case((array) $row, CASE_LOWER);
        }, $rows);
    }

    /**
     * '75%' -> 75.0. MBS_SLAB is VARCHAR2 and has been written both ways over the
     * years ('75' by the old procedure, '75%' by this page), so the digits are
     * what is read rather than the format.
     */
    private function slabPercentage($slab): float
    {
        $digits = preg_replace('/[^0-9.]/', '', (string) $slab);

        return $digits === '' ? 0.0 : (float) $digits;
    }

    /**
     * Re-freeze the challan snapshot for every student on the saved merit list.
     *
     * Delegated to ScholarshipFormController's own refreshScholarshipSnapshot()
     * through MeritSnapshotRefresher rather than reimplemented here — see that
     * class for why the two screens must not carry separate arithmetic.
     *
     * A student on 100% who also wins merit is NOT an error here. The refresh
     * caps the tuition discount at the tuition base, and getStagedSummary() caps
     * the awards it sums the same way, so the two agree and the Bulk Post grid
     * stays clean. The over-100% case is reported once, from the cap check in
     * pass 1, which is the only place it is genuinely a finding.
     *
     * Failures are collected per student, never thrown: the scholarships are
     * already committed by the time this runs, and one student whose fee
     * structure has since gone missing must not take the whole response down.
     * Re-running Post recomputes them — this pass is idempotent.
     *
     * @param  array<int, array>  $rows      the saved merit list
     * @param  array<int, string> $warnings  appended to in place
     * @return array{refreshed: int, failed: int}
     */
    private function refreshChallanSnapshots(int $session, array $rows, array &$warnings): array
    {
        // A few hundred students, each re-priced across every category they hold.
        // The work is committed and idempotent, so running past the default limit
        // is safe; being cut off half way is what is not.
        if (function_exists('set_time_limit')) {
            @set_time_limit(0);
        }

        // ScholarshipFormController's OWN refreshScholarshipSnapshot(), reached by
        // subclassing it rather than reimplemented here — see MeritSnapshotRefresher
        // for why the two screens must not carry separate arithmetic, and for what
        // has to travel when this is deployed (it is a new file in a new directory).
        $refresher = new MeritSnapshotRefresher();

        $refreshed = 0;
        $failed    = 0;
        $seen      = [];

        foreach ($rows as $row) {
            $enrollment = strtoupper(trim((string) $row['sai_enrollment_no']));

            // One refresh per STUDENT: it rewrites every row they hold, so a
            // student listed twice would only repeat identical work.
            if ($enrollment === '' || isset($seen[$enrollment])) {
                continue;
            }
            $seen[$enrollment] = true;

            $aaiId = $row['aai_id'] !== null ? (int) $row['aai_id'] : null;

            try {
                $result = $refresher->refreshFor($session, $aaiId, $enrollment);

                // "saved, but not priced" — the award stands, its rupee value does
                // not. Reported next to the post warnings rather than swallowed.
                foreach (($result['warnings'] ?? []) as $warning) {
                    $warnings[] = $warning;
                }

                $refreshed++;
            } catch (Throwable $e) {
                report($e);
                $failed++;
                $warnings[] = $enrollment . ': challan totals could not be recomputed — '
                            . $e->getMessage() . ' (the scholarship is posted; press Post '
                            . 'again once the cause is fixed).';
            }
        }

        return ['refreshed' => $refreshed, 'failed' => $failed];
    }

    /**
     * Bring merit rows written by an earlier run up to what the Scholarship Bulk
     * page needs, so re-running Post repairs the ones already in the table
     * instead of leaving them stranded. Two separate repairs:
     *
     *   re-stamped : SSD_SOURCE_PAGE moved to SCHOLARSHIP_BULK (rows written
     *                before that stamp was adopted)
     *   queued     : SSD_BULK_POST_STATUS set to PENDING (rows written before
     *                the queue entry was added)
     *
     * Both are scoped as tightly as they can be — this session, SSD_TYPE =
     * Merit Base, and OUR project string — so they can only ever touch rows this
     * endpoint wrote. Rows already correct are not updated.
     *
     * The queue repair additionally requires SSD_POSTED_AT IS NULL. A blank
     * status means one of two opposite things, because the Bulk Post page clears
     * the status back to NULL once it has posted a row; without that guard this
     * would put every already-posted merit challan back in the queue.
     *
     * Note TRIM(col) IS NULL rather than NVL(col,'') = '': in Oracle the empty
     * string IS NULL, so NVL(NULL,'') is still NULL and never equals anything.
     *
     * Runs inside the caller's transaction.
     *
     * @return array{restamped: int, queued: int}
     */
    private function alignMeritRowsForBulkPage(int $session, string $project): array
    {
        $result = ['restamped' => 0, 'queued' => 0];

        $ours = function () use ($session, $project) {
            return DB::table(self::SSD_TABLE)
                ->whereRaw('TRIM(ssd_sess_id) = ?', [(string) $session])
                ->where('ssd_type', self::SSD_MERIT_TYPE)
                ->where('ssd_project', $project);
        };

        // Counted first rather than trusting update()'s return: oci8 does not
        // always report an affected-row count, the same reason savedCount() is
        // read back after the verify INSERT.
        if ($this->tableColumnExists(self::SSD_TABLE, 'ssd_source_page')) {
            $scope = function () use ($ours) {
                return $ours()->whereRaw(
                    "UPPER(TRIM(NVL(ssd_source_page, ''))) <> ?", [self::SSD_SOURCE_PAGE]
                );
            };

            $result['restamped'] = (int) $scope()->count();

            if ($result['restamped'] > 0) {
                $scope()->update(['ssd_source_page' => self::SSD_SOURCE_PAGE]);
            }
        }

        if ($this->tableColumnExists(self::SSD_TABLE, 'ssd_bulk_post_status')) {
            $scope = function () use ($ours) {
                $q = $ours()->whereRaw('TRIM(ssd_bulk_post_status) IS NULL');

                // Without the posted marker there is no way to tell "never
                // queued" from "already posted", so nothing is re-queued at all.
                return $this->tableColumnExists(self::SSD_TABLE, 'ssd_posted_at')
                    ? $q->whereNull('ssd_posted_at')
                    : $q->whereRaw('1 = 0');
            };

            $result['queued'] = (int) $scope()->count();

            if ($result['queued'] > 0) {
                $scope()->update(['ssd_bulk_post_status' => self::SSD_PENDING_STATUS]);
            }
        }

        return $result;
    }

    /**
     * Scholarship percentage each student already holds on the FULL-SEMESTER
     * challan for this session, keyed by upper-cased enrollment number.
     *
     * Mirrors the sum behind ScholarshipFormController::sqlAcademicOtherPctExpr()
     * — same table, same status rule, same per-challan-category partition — which
     * is the figure that page caps at 100%. Read once for the whole session
     * rather than per student: a post runs over several hundred of them.
     *
     * Matched on enrollment number only, where that expression also allows a
     * match on SSD_APP_ID. Every merit student is enrolled and therefore has one,
     * so the difference can only under-report a row written with an app id and no
     * enrollment — and this drives a warning, never a decision.
     *
     * @return array<string, float>
     */
    private function heldCategoryPercentages(int $session): array
    {
        $held = [];

        try {
            $rows = DB::select(
                'SELECT UPPER(TRIM(ssd_sai_enrollment_no)) AS enrollment_no,
                        NVL(SUM(ssd_percentage), 0) AS held_pct
                   FROM ' . self::SSD_TABLE . '
                  WHERE TRIM(ssd_sess_id) = ?
                    AND NVL(status, ?) = ?
                    AND NVL(ssd_challan_category, ?) = ?
                    AND ssd_sai_enrollment_no IS NOT NULL
                  GROUP BY UPPER(TRIM(ssd_sai_enrollment_no))',
                [
                    (string) $session,
                    'E', 'E',
                    self::SSD_CHALLAN_CATEGORY, self::SSD_CHALLAN_CATEGORY,
                ]
            );
        } catch (Throwable $e) {
            report($e);

            return [];
        }

        foreach ($rows as $row) {
            $r   = array_change_key_case((array) $row, CASE_LOWER);
            $key = trim((string) ($r['enrollment_no'] ?? ''));

            if ($key !== '') {
                $held[$key] = (float) ($r['held_pct'] ?? 0);
            }
        }

        return $held;
    }

    /** Has this student already been posted for this session under our project? */
    private function meritScholarshipExists(int $session, string $project, string $enrollment): bool
    {
        return DB::table(self::SSD_TABLE)
            ->whereRaw('TRIM(ssd_sess_id) = ?', [(string) $session])
            ->where('ssd_type', self::SSD_MERIT_TYPE)
            ->where('ssd_project', $project)
            ->whereRaw('UPPER(TRIM(ssd_sai_enrollment_no)) = ?', [$enrollment])
            ->exists();
    }

    /**
     * The fee basis a merit award is priced against.
     *
     * Mirrors BuildsScholarshipSnapshots::resolveSnapshotFee()'s ladder: merit
     * students are all enrolled, so 'Existing' is tried first, but plenty of
     * programmes carry only a 'New Admission <SEASON>' row for a session and a
     * bare 'Existing' lookup would leave those students unpriced.
     *
     * @return array{fee: array|null, student_type: string, message: string}
     */
    private function resolveMeritFee(int $session, string $enrollment): array
    {
        $key = $session . '|' . $enrollment;

        if (isset($this->meritFeeCache[$key])) {
            return $this->meritFeeCache[$key];
        }

        $typesToTry = ['Existing'];

        $sessName = DB::table('academic_sessions')->where('sess_id', $session)->value('sess_name');
        $season   = $sessName ? strtoupper(strtok(trim((string) $sessName), ' ')) : '';
        if ($season !== '') {
            $typesToTry[] = 'New Admission ' . $season;
        }

        $firstMessage = null;

        foreach ($typesToTry as $type) {
            $res = $this->resolveAcademicFeeStructure(
                $session, $enrollment, $type, self::SSD_CHALLAN_CATEGORY
            );

            if (!empty($res['ok']) && (float) ($res['amountBase'] ?? 0) > 0) {
                return $this->meritFeeCache[$key] = [
                    'fee' => $res, 'student_type' => $type, 'message' => '',
                ];
            }

            if ($firstMessage === null) {
                $firstMessage = trim((string) ($res['message'] ?? ''));
            }
        }

        return $this->meritFeeCache[$key] = [
            'fee'          => null,
            'student_type' => 'Existing',
            'message'      => ($firstMessage !== null && $firstMessage !== '')
                ? $firstMessage
                : 'fee structure could not be resolved',
        ];
    }

    /**
     * One scholarship row, shaped so the Scholarship Bulk page can pick it up.
     *
     * SSD_AMOUNT is this row's OWN discount -- percentage of the tuition base --
     * which is the same figure BuildsScholarshipSnapshots writes per row (see
     * its academic loop: `$payload['SSD_AMOUNT'] = round(($pct / 100) * $base, 2)`).
     *
     * The cross-row challan figures (SSD_TUITION_DISC_*, SSD_ONETIME_*,
     * SSD_TOTAL_*, SSD_SURPLUS_*) are deliberately NOT written here. Each of
     * them describes the WHOLE challan, i.e. every scholarship row the student
     * holds in the session across all types, so they cannot be computed from a
     * merit row alone -- writing them would understate the discount of any
     * student who also holds a need-based or employee award.
     *
     * Because the rows are stamped SCHOLARSHIP_BULK, the page that CAN see the
     * whole picture fills them in: ScholarshipFormController's
     * refreshScholarshipSnapshot() re-reads every active row for the student and
     * writes those totals, and it now treats these rows as its own rather than
     * standing off them. So the gap closes the first time that page touches the
     * student, and until then nothing downstream reads a half-built total.
     *
     * SSD_BULK_POST_STATUS = 'PENDING' is what puts the row on the Bulk Post
     * page. That page's queue (ScholarshipFormController::fetchPendingAcademicRows())
     * asks for BOTH:
     *
     *     AND UPPER(TRIM(NVL(ssd.ssd_bulk_post_status, ''))) = 'PENDING'
     *     AND UPPER(TRIM(NVL(ssd.ssd_source_page,        ''))) = 'SCHOLARSHIP_BULK'
     *
     * so the source stamp alone is not enough -- with a blank status the rows
     * appear on the Scholarship Bulk LIST but the Bulk Post screen stays empty.
     *
     * This is a real queue entry: the merit students are now part of that
     * session's next Generate/Post run, which is how the discount reaches their
     * challans. After a successful post that page sets the status back to NULL
     * and stamps SSD_POSTED_AT (PostsScholarshipSnapshots, "clear staging for
     * THIS category"), so a blank status means either "never queued" or
     * "already posted" -- SSD_POSTED_AT is what tells the two apart, and it is
     * what alignMeritRowsForBulkPage() keys on so a posted row is never
     * silently re-queued.
     */
    private function insertMeritScholarshipRow(
        array $row,
        int $session,
        string $project,
        float $percentage,
        array $fee,
        string $entryUser,
        $savedAt,
        string $cohortName
    ): void {
        $priced = $fee['fee'];
        $base   = $priced ? (float) ($priced['amountBase'] ?? 0) : 0.0;
        $amount = $priced ? round(($percentage / 100) * $base, 2) : null;

        $slabLabel = rtrim(rtrim(number_format($percentage, 2, '.', ''), '0'), '.') . '%';

        $payload = [
            'ssd_sai_enrollment_no' => strtoupper(trim((string) $row['sai_enrollment_no'])),
            'ssd_app_id'            => $row['aai_id'] !== null ? (int) $row['aai_id'] : null,
            // VARCHAR2 column; every other page stores the plain id as text.
            'ssd_sess_id'           => (string) $session,
            'ssd_type'              => self::SSD_MERIT_TYPE,
            'ssd_percentage'        => $percentage,
            'ssd_arrear'            => 0,
            'ssd_project'           => $project,
            'ssd_challan_category'  => self::SSD_CHALLAN_CATEGORY,
            'status'                => 'E',
            'ssd_details'           => substr('Merit Scholarship ' . $slabLabel . ' — ' . $cohortName, 0, 300),
            'scd_description'       => substr('Merit Scholarship (' . $slabLabel . ')', 0, 500),
            'scd_entry_username'    => $entryUser,
            'entry_datetime'        => $savedAt,
            'update_datetime'       => $savedAt,

            // ---- what priced it, so the award is traceable back to a fee row ----
            'SSD_AMOUNT'             => $amount,
            // Merit is always awarded as a percentage; the amount above is derived
            // from it, never the other way round.
            'SSD_ENTRY_MODE'         => 'PERCENT',
            'SSD_SOURCE_PAGE'        => self::SSD_SOURCE_PAGE,
            'SSD_BULK_POST_STATUS'   => self::SSD_PENDING_STATUS,
            'SSD_SAVED_BY'           => $entryUser,
            'SSD_SAVED_AT'           => $savedAt,
            'SSD_STUDENT_TYPE'       => $fee['student_type'],
            'SSD_SCHOOL_ID'          => $row['sai_sdpo_school_id'] !== null ? (int) $row['sai_sdpo_school_id'] : null,
            'SSD_DEPT_ID'            => $row['sai_sdpo_dept_id'] !== null ? (int) $row['sai_sdpo_dept_id'] : null,
            'SSD_PROG_ID'            => $row['original_prog_id'] !== null ? (int) $row['original_prog_id'] : null,
            'SSD_FFS_ID'             => $priced['ffsId'] ?? null,
            'SSD_FEE_TYPE'           => $priced['feeType'] ?? null,
            'SSD_TUITION_FEE'        => ($priced && ($priced['referenceLabel'] ?? '') === 'Tuition Fee')
                                        ? $priced['referenceFee'] : null,
            'SSD_PER_CREDIT_FEE'     => ($priced && ($priced['referenceLabel'] ?? '') === 'Per Credit Fee')
                                        ? $priced['referenceFee'] : null,
            'SSD_CREDIT_HOURS'       => $priced['creditHours'] ?? null,
            'SSD_TUITION_FEE_ACTUAL' => $priced ? $base : null,
        ];

        // Snapshot columns ship behind their own DDL patches, so anything that is
        // not on this database is dropped rather than failing the whole post.
        DB::table(self::SSD_TABLE)->insert(
            $this->filterExistingColumns(self::SSD_TABLE, $payload)
        );
    }

    /** Already formatted by TO_CHAR in cohortStatus(); this only flattens NULL. */
    private function formatStamp($value): string
    {
        return trim((string) $value);
    }

    /**
     * "Why is this student not on the merit list?" -- one enrollment number run
     * against every criterion, in the order the SQL applies them.
     *
     * This endpoint exists because a missing student has several completely
     * different possible causes and the merit list itself can show none of
     * them: anyone who fails a pool gate is filtered out inside the StudentData
     * CTE and never reaches the screen at all. Reading the SQL by hand to find
     * out which gate it was is exactly what this replaces.
     *
     * Parameters are resolved exactly as calculate() resolves them, so the
     * answer always describes the run the user is looking at.
     */
    public function diagnose(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'enrollment_no' => ['required', 'string', 'max:50'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $resolved = $this->resolveParams($request);

        if (isset($resolved['error'])) {
            return $resolved['error'];
        }

        $p            = $resolved['params'];
        $cohorts      = $resolved['cohorts'];
        $sessionNames = $this->sessionNameMap();
        $enrollment   = strtoupper(trim((string) $request->input('enrollment_no')));

        $context = [
            'cohort'                   => str_replace('_', ' ', $p['cohort']),
            'cohort_session_names'     => $cohorts[$p['cohort']]['session_names'],
            'scholarship_session_name' => $sessionNames[(string) $p['scholarship_session']]
                                          ?? ('Session ' . $p['scholarship_session']),
            'eval_session_name'        => $sessionNames[(string) $p['eval_session']]
                                          ?? ('Session ' . $p['eval_session']),
            'cgpa_floor'               => $p['cgpa_floor'],
            'floor_mode'               => $p['floor_mode'],
            'top_pct'                  => round($p['top_pct'] * 100, 2),
        ];

        try {
            $records = $this->studentFacts($enrollment, $p['eval_session']);

            if (empty($records)) {
                return response()->json([
                    'success'       => true,
                    'found'         => false,
                    'enrollment_no' => $enrollment,
                    'context'       => $context,
                    'verdict'       => [
                        'code'     => 'not_found',
                        'headline' => 'No student found with this enrollment number.',
                        'detail'   => 'STUDENTS_APPLY_INFO has no row for ' . $enrollment
                                    . '. Check the spelling -- the format is like F2024-0318.',
                    ],
                ]);
            }

            $diagnosed = [];
            foreach ($records as $record) {
                $diagnosed[] = $this->diagnoseRecord($record, $p, $cohorts);
            }

            return response()->json([
                'success'       => true,
                'found'         => true,
                'enrollment_no' => $enrollment,
                'context'       => $context,
                'records'       => $diagnosed,
                // Session-by-session credits are what make a short credit load
                // obvious at a glance, and the course list shows exactly which
                // subject brought the total down.
                'sessions'      => $this->studentSessionSummary($enrollment, $sessionNames),
                'eval_courses'  => $this->evalCourseRows($enrollment, $p['eval_session']),
            ]);
        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'success' => false,
                'message' => 'Check failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Every criterion for one STUDENTS_APPLY_INFO row, plus the verdict.
     *
     * The checks are listed in the order the SQL applies them, so the first
     * failing blocking check IS the answer to "why is this student missing".
     */
    private function diagnoseRecord(array $r, array $p, array $cohorts): array
    {
        $checks   = [];
        $sessIds  = array_map('intval', $p['sess_ids']);
        $sessId   = (int) $r['sai_sdpo_sess_id'];

        // --- 1. intake session must be one the selected cohort pools ----------
        $inCohort = in_array($sessId, $sessIds, true);
        $note     = '';

        if (!$inCohort) {
            // Naming the cohort the student DOES belong to turns a dead end into
            // a one-click fix, and picking the wrong cohort is the easiest
            // mistake to make on this page.
            $owner = null;
            foreach ($cohorts as $key => $entry) {
                if (in_array($sessId, array_map('intval', $entry['sessions']), true)) {
                    $owner = str_replace('_', ' ', $key);
                    break;
                }
            }

            $note = $owner !== null
                ? 'This student belongs to ' . $owner . ' -- select that cohort instead.'
                : 'This intake session is not mapped to any cohort in ACADEMIC_COHORTS.';
        }

        $checks[] = [
            'key'         => 'cohort_session',
            'label'       => 'Intake session is in the selected cohort',
            'status'      => $inCohort ? 'pass' : 'fail',
            'requirement' => implode(', ', $cohorts[$p['cohort']]['session_names']),
            'actual'      => $r['cohort_session_name'] !== '' ? $r['cohort_session_name'] : ('Session ' . $sessId),
            'note'        => $note,
            'blocking'    => true,
            // Reads as the tail of "Not in the eligible pool -- ...", which the
            // label itself cannot do without sounding like broken English.
            'because'     => 'the intake session is not in the selected cohort',
        ];

        // --- 2. student status ------------------------------------------------
        $status   = (string) $r['sai_student_status'];
        $statusOk = in_array($status, ['Active'], true);

        $checks[] = [
            'key'         => 'student_status',
            'label'       => 'Student status',
            'status'      => $statusOk ? 'pass' : 'fail',
            'requirement' => 'Active',
            'actual'      => $status !== '' ? $status : '(none)',
            'note'        => $statusOk ? '' : 'Only these two statuses compete for the merit scholarship.',
            'blocking'    => true,
            'because'     => 'the student status is not eligible',
        ];

        // --- 3. excluded school ----------------------------------------------
        $schoolId = (int) $r['sai_sdpo_school_id'];
        $schoolOk = $schoolId !== self::EXCLUDED_SCHOOL_ID;
        $school   = trim($r['school_name'] . ($r['school_code'] !== '' ? ' (' . $r['school_code'] . ')' : ''));

        $checks[] = [
            'key'         => 'school',
            'label'       => 'School is not excluded',
            'status'      => $schoolOk ? 'pass' : 'fail',
            'requirement' => 'Any school except school id ' . self::EXCLUDED_SCHOOL_ID,
            'actual'      => $school !== '' ? $school : ('School id ' . $schoolId),
            'note'        => $schoolOk
                ? ''
                : 'This whole school is excluded from the merit scholarship, so every '
                  . 'programme under it is filtered out regardless of CGPA.',
            'blocking'    => true,
            'because'     => 'the school is excluded from the merit scholarship',
        ];

        // --- 4. results posted in the results session -------------------------
        $resultRows = (int) $r['result_rows'];
        $resultsOk  = $resultRows > 0;

        $checks[] = [
            'key'         => 'results_posted',
            'label'       => 'Results posted in the results session',
            'status'      => $resultsOk ? 'pass' : 'fail',
            'requirement' => 'At least one finally-submitted, non-exempt enrolled course',
            'actual'      => $resultRows . ' such course(s)',
            'note'        => $resultsOk
                ? ''
                : 'Either the student was not enrolled that term, or the grades are not '
                  . 'finally submitted yet (GH_EXAM_FSUBMIT = 1).',
            'blocking'    => true,
            'because'     => 'no results are posted for the results session',
        ];

        // --- 5. minimum graded credit hours ----------------------------------
        // NULL degree level falls to the 'Other' branch, matching the
        // NVL(p.prog_degree_level, 'Other') in the workload HAVING clause.
        $level      = (string) $r['prog_degree_level'];
        $isUnderGrad = $level === 'Undergraduate';
        $requiredCr  = $isUnderGrad ? self::MIN_CR_UNDERGRAD : self::MIN_CR_OTHER;
        $gradedCr   = (float) $r['graded_cr'];
        $creditsOk  = $gradedCr >= $requiredCr;
        $shortBy    = $requiredCr - $gradedCr;

        $checks[] = [
            'key'         => 'min_credits',
            'label'       => 'Minimum graded credit hours in the results session',
            'status'      => $creditsOk ? 'pass' : 'fail',
            'requirement' => '>= ' . $requiredCr . ' cr (' . ($level !== '' ? $level : 'degree level not set') . ')',
            'actual'      => $this->trimNumber($gradedCr) . ' cr',
            'note'        => $creditsOk
                ? ''
                : 'Short by ' . $this->trimNumber($shortBy) . ' credit hour(s). W, I and P '
                  . 'grades and exempted courses do not count towards this total.',
            'blocking'    => true,
            'because'     => 'the credit load in the results session is below the minimum ('
                             . $this->trimNumber($gradedCr) . ' of ' . $requiredCr . ' credit hours)',
        ];

        // --- 6. CGPA floor ----------------------------------------------------
        // Whether this removes the student from the pool or only from the merit
        // list depends on floor_mode, which is exactly what makes it worth
        // spelling out rather than showing a bare pass/fail.
        $cgpa    = $r['calculated_cgpa'] === null ? null : (float) $r['calculated_cgpa'];
        $floorOk = $cgpa !== null && $cgpa >= $p['cgpa_floor'];

        if ($p['floor_mode'] === 'none') {
            $floorCheck = [
                'key'         => 'cgpa_floor',
                'label'       => 'CGPA floor',
                'status'      => 'skipped',
                'requirement' => 'No floor applied for this run',
                'actual'      => $cgpa === null ? 'no CGPA' : $this->trimNumber($cgpa),
                'note'        => '',
                'blocking'    => false,
                'because'     => 'the CGPA is below the floor',
            ];
        } else {
            $floorCheck = [
                'key'         => 'cgpa_floor',
                'label'       => 'CGPA floor (' . $p['floor_mode'] . ' ranking)',
                'status'      => $floorOk ? 'pass' : 'fail',
                'requirement' => 'CGPA >= ' . $this->trimNumber($p['cgpa_floor']),
                'actual'      => $cgpa === null ? 'no CGPA returned' : $this->trimNumber($cgpa),
                'note'        => $floorOk
                    ? ''
                    : ($p['floor_mode'] === 'before'
                        ? 'With the floor applied BEFORE ranking, this removes the student '
                          . 'from the eligible pool entirely.'
                        : 'With the floor applied AFTER ranking, the student stays in the '
                          . 'eligible pool but cannot reach the merit list.'),
                // 'before' shrinks the pool; 'after' only trims the merit list.
                'blocking'    => $p['floor_mode'] === 'before',
                'because'     => 'the CGPA of ' . ($cgpa === null ? 'n/a' : $this->trimNumber($cgpa))
                                 . ' is below the floor of ' . $this->trimNumber($p['cgpa_floor']),
            ];
        }

        $checks[] = $floorCheck;

        // --- verdict ----------------------------------------------------------
        $firstFail = null;
        foreach ($checks as $check) {
            if ($check['status'] === 'fail' && $check['blocking']) {
                $firstFail = $check;
                break;
            }
        }

        $group = $this->programGroupFor((int) $r['sai_sdpo_prog_id']);

        $record = [
            'sai_id'                => (int) $r['sai_id'],
            'enrollment_no'         => $r['sai_enrollment_no'],
            'name'                  => $r['spi_name'],
            'father_name'           => $r['spi_fathername'],
            'prog_id'               => (int) $r['sai_sdpo_prog_id'],
            'prog_name'             => $r['prog_name'],
            'combined_program_name' => $group['name'] ?? $r['prog_name'],
            'grouped_with'          => $group['ids'],
            'prog_degree_level'     => $level,
            'school'                => $school,
            'dept_name'             => $r['dept_name'],
            'session_name'          => $r['cohort_session_name'],
            'status'                => $status,
            'cgpa'                  => $cgpa === null ? null : $this->trimNumber($cgpa),
            'graded_cr'             => $this->trimNumber($gradedCr),
            'required_cr'           => $requiredCr,
            'checks'                => $checks,
        ];

        if ($firstFail !== null) {
            $record['in_pool'] = false;
            $record['verdict'] = [
                'code'     => 'not_in_pool',
                'headline' => 'Not in the eligible pool — ' . $firstFail['because'] . '.',
                'detail'   => $firstFail['label'] . '. Required: ' . $firstFail['requirement']
                            . '. Actual: ' . $firstFail['actual'] . '.'
                            . ($firstFail['note'] !== '' ? ' ' . $firstFail['note'] : ''),
                'failed'   => $firstFail['key'],
            ];

            return $record;
        }

        // All pool gates passed, so the student is in the pool and the ranking
        // is the only thing left that can keep them off the list. That needs the
        // real calculation -- a rank only means something against the whole pool.
        $row = $this->meritRowFor((string) $r['sai_enrollment_no'], $p);

        if ($row === null) {
            // Should not happen: every gate passed but the CTE has no row. Say so
            // plainly rather than inventing a reason.
            $record['in_pool'] = false;
            $record['verdict'] = [
                'code'     => 'unexpected',
                'headline' => 'Every criterion passed, but the calculation returned no row.',
                'detail'   => 'This is unexpected -- re-run the calculation and, if it '
                            . 'persists, the criteria above and the merit SQL have drifted apart.',
                'failed'   => null,
            ];

            return $record;
        }

        $rank      = (int) $row['scholarship_rank'];
        $cutoff    = (int) $row['cutoff_rank'];
        $total     = (int) $row['total_students'];
        $onList    = $rank <= $cutoff && (int) $row['meets_floor'] === 1;
        $rankInOk  = $rank <= $cutoff;

        $checks[] = [
            'key'         => 'rank_cutoff',
            'label'       => 'Rank inside the programme cut-off',
            'status'      => $rankInOk ? 'pass' : 'fail',
            'requirement' => 'Rank <= ' . $cutoff . ' (top ' . round($p['top_pct'] * 100, 2)
                             . '% of ' . $total . ' students in this programme group)',
            'actual'      => 'Rank ' . $rank . ' of ' . $total,
            'note'        => $rankInOk
                ? ''
                : 'Ranking is DENSE_RANK on CGPA within the programme group, so this is '
                  . 'purely a matter of the CGPA being lower than the students above.',
            'blocking'    => false,
            'because'     => 'the rank is outside the programme cut-off',
        ];

        $record['checks']           = $checks;
        $record['in_pool']          = true;
        $record['scholarship_rank'] = $rank;
        $record['cutoff_rank']      = $cutoff;
        $record['total_students']   = $total;
        $record['pool_cgpa']        = $this->trimNumber((float) $row['calculated_cgpa']);
        $record['slab']             = $onList ? $this->slabFor($rank, $row) : null;

        if ($onList) {
            $record['verdict'] = [
                'code'     => 'on_merit_list',
                'headline' => 'On the merit list — ' . $record['slab'] . ' scholarship.',
                'detail'   => 'Rank ' . $rank . ' of ' . $total . ' in ' . $record['combined_program_name']
                            . ', cut-off rank ' . $cutoff . '. If the list on screen does not show '
                            . 'this student, re-run the calculation.',
                'failed'   => null,
            ];
        } elseif (!$rankInOk) {
            $record['verdict'] = [
                'code'     => 'pool_off_list',
                'headline' => 'In the eligible pool, but outside the merit cut-off.',
                'detail'   => 'Rank ' . $rank . ' of ' . $total . ' in ' . $record['combined_program_name']
                            . ' — only ranks 1 to ' . $cutoff . ' make the list.',
                'failed'   => 'rank_cutoff',
            ];
        } else {
            $record['verdict'] = [
                'code'     => 'pool_off_list',
                'headline' => 'In the eligible pool, but the CGPA floor is not met.',
                'detail'   => 'Rank ' . $rank . ' is inside the cut-off of ' . $cutoff
                            . ', but the CGPA is below ' . $this->trimNumber($p['cgpa_floor'])
                            . ' and the floor is applied after ranking.',
                'failed'   => 'cgpa_floor',
            ];
        }

        return $record;
    }

    /**
     * Raw facts for one enrollment number: the STUDENTS_APPLY_INFO row(s) plus
     * the two grade_history aggregates the eligibility gates depend on.
     *
     * One row per SAI record -- a re-admitted student legitimately has more than
     * one, and each is diagnosed separately rather than picking one silently.
     *
     * The counts deliberately repeat the conditions from buildMeritCte()'s two
     * EXISTS clauses instead of reusing them: here they have to report a NUMBER
     * ("14 credit hours"), not just whether a row existed.
     */
    private function studentFacts(string $enrollment, int $evalSession): array
    {
        $sql = <<<SQL
SELECT
    sai.sai_id,
    sai.sai_enrollment_no,
    sai.sai_sdpo_prog_id,
    p.prog_name,
    p.prog_degree_level,
    sai.sai_sdpo_school_id,
    s.school_code,
    s.school_name,
    d.dept_name,
    sai.sai_sdpo_sess_id,
    acs.sess_name AS cohort_session_name,
    sai.sai_student_status,
    spi.spi_name,
    spi.spi_fathername,
    ROUND(TO_NUMBER(CGPA_TEST(sai.sai_enrollment_no)), 2) AS calculated_cgpa,
    (
        SELECT COUNT(*)
        FROM grade_history gh
        WHERE gh.gh_sai_enrollment_no = sai.sai_enrollment_no
          AND gh.gh_co_sess_id = ?
          AND gh.gh_status = 'E'
          AND (gh.gh_exemption_status <> 'Yes' OR gh.gh_exemption_status IS NULL)
          AND gh.gh_exam_fsubmit = 1
    ) AS result_rows,
    NVL((
        SELECT SUM(gw.gh_co_course_cr)
        FROM grade_history gw
        WHERE gw.gh_sai_enrollment_no = sai.sai_enrollment_no
          AND gw.gh_co_sess_id = ?
          AND gw.gh_status = 'E'
          AND (gw.gh_exemption_status <> 'Yes' OR gw.gh_exemption_status IS NULL)
          AND gw.gh_exam_fsubmit = 1
          AND gw.gh_grade NOT IN ('W', 'I', 'P')
    ), 0) AS graded_cr
FROM students_apply_info sai
LEFT JOIN students_personal_info spi ON sai.sai_spi_id = spi.spi_id
LEFT JOIN schools s                  ON sai.sai_sdpo_school_id = s.school_id
LEFT JOIN departments d              ON sai.sai_sdpo_dept_id = d.dept_id
LEFT JOIN programs p                 ON sai.sai_sdpo_prog_id = p.prog_id
LEFT JOIN academic_sessions acs      ON acs.sess_id = sai.sai_sdpo_sess_id
WHERE UPPER(TRIM(sai.sai_enrollment_no)) = ?
ORDER BY sai.sai_sdpo_sess_id DESC
SQL;

        $rows = DB::select($sql, [$evalSession, $evalSession, $enrollment]);

        return array_map(function ($row) {
            $r = array_change_key_case((array) $row, CASE_LOWER);

            // Every consumer below reads these as strings or numbers, so NULLs
            // are flattened once here rather than guarded at each use.
            foreach (['prog_name', 'prog_degree_level', 'school_code', 'school_name',
                      'dept_name', 'cohort_session_name', 'sai_student_status',
                      'spi_name', 'spi_fathername'] as $key) {
                $r[$key] = trim((string) ($r[$key] ?? ''));
            }

            return $r;
        }, $rows);
    }

    /**
     * The student's own row out of the real calculation -- the only way to get a
     * rank, because a rank is meaningless without the whole pool behind it.
     *
     * Runs the same CTE calculate() runs, so the rank reported here and the rank
     * on the merit list cannot disagree.
     */
    private function meritRowFor(string $enrollment, array $p): ?array
    {
        [$cte, $bindings] = $this->buildMeritCte(
            $p['sess_ids'],
            $p['eval_session'],
            $p['top_pct'],
            $p['slab_75_pct'],
            $p['slab_50_pct'],
            $p['cgpa_floor'],
            $p['floor_mode']
        );

        // The enrollment placeholder sits after the whole WITH block, so it is
        // appended after the CTE's own bindings.
        $bindings[] = strtoupper(trim($enrollment));

        $sql = <<<SQL
{$cte}
SELECT
    embs.calculated_cgpa,
    embs.scholarship_rank,
    embs.total_students,
    embs.cutoff_rank,
    embs.slab_75_limit,
    embs.slab_50_limit,
    embs.meets_floor
FROM EligibleMBS embs
WHERE UPPER(TRIM(embs.sai_enrollment_no)) = ?
SQL;

        $rows = DB::select($sql, $bindings);

        if (empty($rows)) {
            return null;
        }

        return array_change_key_case((array) $rows[0], CASE_LOWER);
    }

    /**
     * Which slab a rank falls in. Mirrors SLAB_CASE_SQL -- both read the same
     * two limits off the same row, so they cannot drift.
     */
    private function slabFor(int $rank, array $row): string
    {
        if ($rank <= (int) $row['slab_75_limit']) {
            return '75%';
        }

        if ($rank <= (int) $row['slab_50_limit']) {
            return '50%';
        }

        return '25%';
    }

    /**
     * Session-by-session credit summary for one student. Not a criterion in
     * itself -- it is what makes a short credit load obvious, and it shows at a
     * glance whether the results session picked for the run is the right one.
     */
    private function studentSessionSummary(string $enrollment, array $sessionNames): array
    {
        $sql = <<<SQL
SELECT
    gh.gh_co_sess_id,
    COUNT(*) AS courses,
    SUM(CASE WHEN gh.gh_status = 'E' AND gh.gh_exam_fsubmit = 1
             AND (gh.gh_exemption_status <> 'Yes' OR gh.gh_exemption_status IS NULL)
             AND gh.gh_grade NOT IN ('W', 'I', 'P')
             THEN gh.gh_co_course_cr ELSE 0 END) AS graded_cr
FROM grade_history gh
WHERE UPPER(TRIM(gh.gh_sai_enrollment_no)) = ?
GROUP BY gh.gh_co_sess_id
ORDER BY gh.gh_co_sess_id
SQL;

        $rows = DB::select($sql, [strtoupper(trim($enrollment))]);

        return array_map(function ($row) use ($sessionNames) {
            $r    = array_change_key_case((array) $row, CASE_LOWER);
            $id   = (string) (int) $r['gh_co_sess_id'];

            return [
                'session_name' => $sessionNames[$id] ?? ('Session ' . $id),
                'courses'      => (int) $r['courses'],
                'graded_cr'    => $this->trimNumber((float) $r['graded_cr']),
            ];
        }, $rows);
    }

    /**
     * The courses behind the credit count in the results session, so a total of
     * 14 instead of 15 can be traced to the 2-credit course that caused it.
     */
    private function evalCourseRows(string $enrollment, int $evalSession): array
    {
        $sql = <<<SQL
SELECT
    gh.gh_course_code,
    gh.gh_cd_name,
    gh.gh_co_course_cr,
    gh.gh_grade,
    gh.gh_grade_point,
    gh.gh_status,
    gh.gh_exam_fsubmit,
    gh.gh_exemption_status
FROM grade_history gh
WHERE UPPER(TRIM(gh.gh_sai_enrollment_no)) = ?
  AND gh.gh_co_sess_id = ?
ORDER BY gh.gh_course_code
SQL;

        $rows = DB::select($sql, [strtoupper(trim($enrollment)), $evalSession]);

        return array_map(function ($row) {
            $r     = array_change_key_case((array) $row, CASE_LOWER);
            $grade = trim((string) ($r['gh_grade'] ?? ''));

            // Same rule as the workload HAVING clause: this is what decides
            // whether the course's credits reached the minimum-load total.
            $counts = (string) ($r['gh_status'] ?? '') === 'E'
                   && (int) ($r['gh_exam_fsubmit'] ?? 0) === 1
                   && !in_array($grade, ['W', 'I', 'P'], true)
                   && trim((string) ($r['gh_exemption_status'] ?? '')) !== 'Yes';

            return [
                'course_code'  => trim((string) ($r['gh_course_code'] ?? '')),
                'course_name'  => trim((string) ($r['gh_cd_name'] ?? '')),
                'credit_hours' => $this->trimNumber((float) ($r['gh_co_course_cr'] ?? 0)),
                'grade'        => $grade,
                'grade_point'  => $r['gh_grade_point'] === null
                                  ? '' : $this->trimNumber((float) $r['gh_grade_point']),
                'counts'       => $counts,
            ];
        }, $rows);
    }

    /**
     * The mapping, read from FIN_MERIT_COMBINED_PROGRAMS.
     *
     * A group of one is kept, not dropped: those rows exist so a programme that
     * competes alone can still be shown under a curated name instead of whatever
     * PROGRAMS.PROG_NAME happens to say. A programme id listed in two groups is
     * awarded to the first group in (grp_key, sort_order) order -- the same row
     * PROG_GROUP_MAP_SQL keeps, so both sides of the calculation agree.
     *
     * An unreadable or empty table is not an error: it means nothing is grouped,
     * so every programme is ranked on its own.
     *
     * @return array<string, array{ids: int[], name: string}>
     */
    private function loadStoredMapping(): array
    {
        try {
            $rows = DB::select(
                'SELECT grp_key, grp_name, prog_id, sort_order FROM '
                . self::MAP_TABLE . ' ORDER BY grp_key, sort_order, prog_id'
            );
        } catch (Throwable $e) {
            report($e);

            return [];
        }

        $map     = [];
        $claimed = [];

        foreach ($rows as $row) {
            $r    = array_change_key_case((array) $row, CASE_LOWER);
            $key  = trim((string) ($r['grp_key'] ?? ''));
            $name = trim((string) ($r['grp_name'] ?? ''));
            $id   = (int) ($r['prog_id'] ?? 0);

            if ($key === '' || $name === '' || $id <= 0 || isset($claimed[$id])) {
                continue;
            }

            $claimed[$id] = true;

            if (!isset($map[$key])) {
                $map[$key] = ['ids' => [], 'name' => $name];
            }

            $map[$key]['ids'][] = $id;
        }

        return $map;
    }

    /**
     * Live mapping used by the student check and by the mapping page. Read once
     * per request; the calculation SQL joins the same table directly.
     *
     * @return array<string, array{ids: int[], name: string}>
     */
    protected function combinedPrograms(): array
    {
        if ($this->combinedProgramsCache === null) {
            $this->combinedProgramsCache = $this->loadStoredMapping();
        }

        return $this->combinedProgramsCache;
    }

    /**
     * Which mapping group a programme id belongs to, resolved in PHP from the
     * same table the SQL is built from. Returns nulls for a programme that is
     * not in the mapping at all.
     *
     * @return array{key: ?string, name: ?string, ids: array}
     */
    private function programGroupFor(int $progId): array
    {
        foreach ($this->combinedPrograms() as $key => $cfg) {
            if (in_array($progId, $cfg['ids'], true)) {
                return ['key' => $key, 'name' => $cfg['name'], 'ids' => $cfg['ids']];
            }
        }

        return ['key' => null, 'name' => null, 'ids' => []];
    }

    /**
     * 15.0 -> '15', 3.77 -> '3.77'. Credit hours and CGPAs are shown all over
     * the diagnosis and a trailing '.0' on a whole number reads like a bug.
     */
    private function trimNumber(float $value): string
    {
        return rtrim(rtrim(number_format($value, 2, '.', ''), '0'), '.');
    }

    /**
     * Validate and resolve the inputs shared by calculate(), saveVerified(),
     * postScholarship() and diagnose(). All four must see EXACTLY the same
     * numbers, so this parsing lives in one place only -- in particular, a
     * diagnosis has to describe the run the user is actually looking at.
     *
     * Returns ['error' => JsonResponse] or ['params' => [...], 'cohorts' => [...]].
     */
    private function resolveParams(Request $request): array
    {
        $validator = Validator::make($request->all(), [
            'cohort'              => ['required', 'string', 'max:200'],
            'scholarship_session' => ['required', 'integer', 'min:1'],
            'eval_session'        => ['nullable', 'integer', 'min:1'],
            'top_pct'             => ['nullable', 'numeric', 'min:0.1', 'max:100'],
            'slab_75_pct'         => ['nullable', 'numeric', 'min:0', 'max:100'],
            'slab_50_pct'         => ['nullable', 'numeric', 'min:0', 'max:100'],
            'cgpa_floor'          => ['nullable', 'numeric', 'min:0', 'max:4'],
            'floor_mode'          => ['nullable', 'in:before,after,none'],
        ]);

        if ($validator->fails()) {
            return ['error' => response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422)];
        }

        $cohorts = $this->cohortMap();
        $cohort  = $request->input('cohort');

        if (!isset($cohorts[$cohort])) {
            return ['error' => response()->json([
                'success' => false,
                'message' => 'Unknown cohort. Reload the page and try again.',
            ], 422)];
        }

        $sessIds = $cohorts[$cohort]['sessions'];

        if (empty($sessIds)) {
            return ['error' => response()->json([
                'success' => false,
                'message' => 'This cohort has no sessions mapped in ACADEMIC_COHORTS.',
            ], 422)];
        }

        $scholarshipSession = (int) $request->input('scholarship_session');

        // The dropdown offers exactly one session; without this the endpoints
        // would still accept any id that reached them, and an older Fall could
        // be calculated, verified and posted from a stale page or a hand-made
        // request. Same shape as the cohort check above, and it reads the same
        // sessionList() the dropdown is built from, so the two cannot drift.
        $offered = [];
        foreach ($this->sessionList() as $session) {
            $offered[(int) $session['id']] = $session['name'];
        }

        if (!isset($offered[$scholarshipSession])) {
            return ['error' => response()->json([
                'success' => false,
                'message' => 'This scholarship session is not open for merit calculation. '
                           . 'Available: ' . (empty($offered) ? 'none' : implode(', ', $offered)) . '.',
            ], 422)];
        }

        return [
            'cohorts' => $cohorts,
            'params'  => [
                'cohort'              => $cohort,
                'sess_ids'            => $sessIds,
                'scholarship_session' => $scholarshipSession,
                'eval_session'        => $request->filled('eval_session')
                    ? (int) $request->input('eval_session')
                    : $scholarshipSession - self::EVAL_SESSION_OFFSET,
                'top_pct'     => (float) ($request->input('top_pct', self::DEFAULT_TOP_PCT)) / 100,
                'slab_75_pct' => (float) ($request->input('slab_75_pct', self::DEFAULT_SLAB_75_PCT)) / 100,
                'slab_50_pct' => (float) ($request->input('slab_50_pct', self::DEFAULT_SLAB_50_PCT)) / 100,
                'cgpa_floor'  => (float) ($request->input('cgpa_floor', self::DEFAULT_CGPA_FLOOR)),
                'floor_mode'  => $request->input('floor_mode', 'after'),
            ],
        ];
    }

    /**
     * FLAG marks which intake the saved rows came from -- the old procedure
     * hard-coded 'From_Fall 2024'. Here it is derived from the cohort, so
     * 'Cohort_2024' is stored as 'From_Cohort 2024'.
     *
     * The page does not send a flag, so this is what it always gets. A 'flag'
     * input is still honoured if one is posted, which keeps a one-off run with
     * a different convention possible without a code change.
     */
    private function defaultFlag(string $cohort): string
    {
        return substr('From_' . str_replace('_', ' ', $cohort), 0, 100);
    }

    /**
     * How many rows RESULT_TABLE already holds for one scholarship session and
     * flag. Never throws: a missing table must not break the calculation page.
     */
    private function savedCount(int $scholarshipSession, string $flag): int
    {
        try {
            $rows = DB::select(
                'SELECT COUNT(*) AS cnt FROM ' . self::RESULT_TABLE
                . ' WHERE SCH_SESSION_ID = ? AND FLAG = ?',
                [$scholarshipSession, $flag]
            );

            $row = array_change_key_case((array) ($rows[0] ?? []), CASE_LOWER);

            return (int) ($row['cnt'] ?? 0);
        } catch (Throwable $e) {
            report($e);

            return 0;
        }
    }

    /**
     * Every enabled session, newest first. Used for the Result Session Override
     * in Advanced settings, which has to be able to reach Spring sessions --
     * the results term is a Spring one whenever the scholarship term is Fall.
     *
     * The oci8 driver's column-name casing is not worth depending on, so rows
     * are flattened to plain arrays with known keys. Names only -- session ids
     * are internal and are never shown on the page.
     */
    private function resultSessionList(): array
    {
        return DB::table('ACADEMIC_SESSIONS')
            ->select('SESS_ID', 'SESS_NAME')
            ->where('SESS_STATUS', 'E')
            ->orderBy('SESS_ID', 'desc')
            ->get()
            ->map(function ($row) {
                $r = array_change_key_case((array) $row, CASE_LOWER);

                return [
                    'id'   => (int) ($r['sess_id'] ?? 0),
                    'name' => trim((string) ($r['sess_name'] ?? '')),
                ];
            })
            ->filter(function ($session) {
                return $session['id'] > 0 && $session['name'] !== '';
            })
            ->values()
            ->all();
    }

    /**
     * Sessions offered as the Scholarship Session: Fall only, 2024 onwards.
     * A Fall session whose year cannot be parsed is kept rather than hidden.
     */
    private function sessionList(): array
    {
        $prefix = self::SCHOLARSHIP_SESSION_PREFIX;

        return collect($this->resultSessionList())
            ->filter(function ($session) use ($prefix) {
                if (strpos(strtoupper($session['name']), $prefix) !== 0) {
                    return false;
                }

                if (preg_match('/(\d{4})/', $session['name'], $m)) {
                    return (int) $m[1] >= self::MIN_SCHOLARSHIP_YEAR;
                }

                return true;
            })
            ->values()
            ->all();
    }

    /**
     * sess_id => sess_name for every session, disabled ones included, so that
     * a derived results session can always be shown by name. Keys are strings
     * because this is handed to the front end as JSON.
     */
    private function sessionNameMap(): array
    {
        $map = [];

        foreach (DB::select('SELECT sess_id, sess_name FROM academic_sessions') as $row) {
            $r = array_change_key_case((array) $row, CASE_LOWER);

            if (!isset($r['sess_id']) || $r['sess_id'] === null) {
                continue;
            }

            $map[(string) (int) $r['sess_id']] = trim((string) ($r['sess_name'] ?? ''));
        }

        return $map;
    }

    /**
     * Cohort -> intake sessions, read from the ACADEMIC_COHORTS view.
     *
     * That view is (SESS_ID NUMBER, SESS_NAME VARCHAR2, COHORT_ID VARCHAR2),
     * where COHORT_ID holds a label such as 'Cohort_2024'. Rows are still run
     * through array_change_key_case because the oci8 driver's column-name
     * casing is not worth depending on.
     */
    private function cohortMap(): array
    {
        $rows = DB::select(
            'SELECT sess_id, sess_name, cohort_id FROM academic_cohorts ORDER BY sess_id DESC'
        );

        $map = [];

        foreach ($rows as $row) {
            $r = array_change_key_case((array) $row, CASE_LOWER);

            if (!isset($r['sess_id']) || $r['sess_id'] === null) {
                continue;
            }

            $sessId = (int) $r['sess_id'];
            $cohort = trim((string) ($r['cohort_id'] ?? ''));
            $label  = trim((string) ($r['sess_name'] ?? ''));

            if ($cohort === '') {
                $cohort = 'Unmapped';
            }
            if ($label === '') {
                $label = 'Session ' . $sessId;
            }

            // Cohort_2023 and older are never used for merit scholarships.
            // An unparseable cohort label is kept rather than silently dropped.
            if (preg_match('/(\d{4})/', $cohort, $m) && (int) $m[1] < self::MIN_COHORT_YEAR) {
                continue;
            }

            if (!isset($map[$cohort])) {
                $map[$cohort] = [
                    'cohort'         => $cohort,
                    'sessions'       => [],
                    'session_names'  => [],
                ];
            }

            if (!in_array($sessId, $map[$cohort]['sessions'], true)) {
                $map[$cohort]['sessions'][]      = $sessId;
                // rows arrive newest-first, so this list is already newest-first
                $map[$cohort]['session_names'][] = $label;
            }
        }

        foreach ($map as $key => $entry) {
            // 'Cohort_2024' + [Spring 2025, Fall 2024]
            //   -> 'Cohort 2024 - (Spring 2025, Fall 2024)'
            $entry['label'] = str_replace('_', ' ', $entry['cohort'])
                            . ' - (' . implode(', ', $entry['session_names']) . ')';

            $entry['max_sess'] = max($entry['sessions']);

            // sessions/session_names must stay index-aligned -- both are handed
            // to the front end as cohort_sessions/cohort_session_names, and SQL
            // IN(...) doesn't care about order, so there is nothing to sort for.
            $map[$key] = $entry;
        }

        // newest cohort first
        uasort($map, function ($a, $b) {
            return $b['max_sess'] <=> $a['max_sess'];
        });

        return $map;
    }

    /**
     * The whole calculation, in one Oracle statement, fully bound.
     *
     * Only the WITH block is built here -- everything up to and including the
     * EligibleMBS CTE. runMeritQuery() puts a display SELECT on top of it and
     * saveVerified() puts an INSERT ... SELECT on top of it, which is how the
     * screen and the stored rows are kept identical by construction.
     *
     * @return array{0: string, 1: array} [$cte, $bindings]
     */
    private function buildMeritCte(
        array $sessIds,
        int $evalSession,
        float $topPct,
        float $slab75Pct,
        float $slab50Pct,
        float $cgpaFloor,
        string $floorMode
    ): array {
        $bindings = [];

        // The grouping map is joined straight off FIN_MERIT_COMBINED_PROGRAMS,
        // so a mapping saved on the Combined Programmes page is live on the next
        // Calculate with nothing to redeploy. An empty table simply matches
        // nothing in the LEFT JOIN and every programme is ranked on its own.
        $progGroupMap = self::PROG_GROUP_MAP_SQL;

        // --- cohort session list ---------------------------------------------
        $sessPlaceholders = implode(', ', array_fill(0, count($sessIds), '?'));
        foreach ($sessIds as $sessId) {
            $bindings[] = $sessId;
        }

        $bindings[] = self::EXCLUDED_SCHOOL_ID;

        // --- CGPA floor BEFORE ranking (shrinks the pool and the quota) -------
        $floorBefore = '';
        if ($floorMode === 'before') {
            $floorBefore = 'AND TO_NUMBER(CGPA_TEST(sai.sai_enrollment_no)) >= ?';
            $bindings[]  = $cgpaFloor;
        }

        $bindings[] = $evalSession;            // gh_current
        $bindings[] = $evalSession;            // gh_workload
        $bindings[] = self::MIN_CR_UNDERGRAD;
        $bindings[] = self::MIN_CR_OTHER;
        $bindings[] = $topPct;                 // cutoff_rank
        $bindings[] = $slab75Pct;              // slab_75_count
        $bindings[] = $slab50Pct;              // slab_50_count
        $bindings[] = $slab75Pct;              // slab_75_limit
        $bindings[] = $slab75Pct;              // slab_50_limit part 1
        $bindings[] = $slab50Pct;              // slab_50_limit part 2

        // --- CGPA floor AFTER ranking (trims the tail, quota unchanged) -------
        // Evaluated once per row as a flag rather than as a WHERE, because the
        // result set now carries the WHOLE eligible pool, not just the winners.
        // The page needs the pool so that clicking "Eligible Pool" can list it.
        if ($floorMode === 'after') {
            $meetsFloorExpr = 'CASE WHEN TO_NUMBER(CGPA_TEST(t.sai_enrollment_no)) >= ? THEN 1 ELSE 0 END';
            $bindings[]     = $cgpaFloor;
        } else {
            // 'before' already removed them from the pool; 'none' has no floor.
            $meetsFloorExpr = '1';
        }

        $cte = <<<SQL
WITH prog_group_map AS (
    {$progGroupMap}
),
StudentData AS (
    SELECT
        sai.sai_sdpo_prog_id                          AS original_prog_id,
        NVL(m.grp_key, TO_CHAR(sai.sai_sdpo_prog_id)) AS mapped_program_id,
        -- Same grouping as mapped_program_id, but numeric: this is what goes
        -- into RESULT_TABLE.MAPPED_PROGRAM_ID (a NUMBER column). Ranking and
        -- the on-screen grouping keep using the string key.
        NVL(m.grp_prog_id, sai.sai_sdpo_prog_id)      AS mapped_prog_num,
        NVL(m.grp_name, p.prog_name)                  AS combined_program_name,
        p.prog_name                                   AS prog_name,
        p.prog_degree_level                           AS prog_degree_level,
        sai.sai_enrollment_no                         AS sai_enrollment_no,
        sai.sai_sdpo_school_id                        AS sai_sdpo_school_id,
        s.school_code                                 AS school_code,
        sai.sai_sdpo_dept_id                          AS sai_sdpo_dept_id,
        d.dept_name                                   AS dept_name,
        sai.sai_sdpo_sess_id                          AS sai_sdpo_sess_id,
        acs.sess_name                                 AS cohort_session_name,
        sai.sai_student_status                        AS sai_student_status,
        aai.aai_id                                    AS aai_id,
        spi.spi_name                                  AS spi_name,
        spi.spi_fathername                            AS spi_fathername,
        spi.spi_guardian_cnic                         AS spi_guardian_cnic,
        ROUND(TO_NUMBER(CGPA_TEST(sai.sai_enrollment_no)), 2) AS calculated_cgpa
    FROM students_apply_info sai
    LEFT JOIN students_personal_info spi
        ON sai.sai_spi_id = spi.spi_id
    LEFT JOIN admission_apply_info aai
        ON aai.aai_api_id = sai.sai_api_id
       AND LOWER(aai.cms_sai_enrollment_no) = LOWER(sai.sai_enrollment_no)
    LEFT JOIN schools s
        ON sai.sai_sdpo_school_id = s.school_id
    LEFT JOIN departments d
        ON sai.sai_sdpo_dept_id = d.dept_id
    LEFT JOIN programs p
        ON sai.sai_sdpo_prog_id = p.prog_id
    LEFT JOIN academic_sessions acs
        ON acs.sess_id = sai.sai_sdpo_sess_id
    LEFT JOIN prog_group_map m
        ON m.prog_id = sai.sai_sdpo_prog_id
    WHERE sai.sai_sdpo_sess_id IN ({$sessPlaceholders})
      AND sai.sai_student_status IN ('Active')
      AND sai.sai_sdpo_school_id <> ?
      {$floorBefore}
      AND EXISTS (
          SELECT 1
          FROM grade_history gh_current
          WHERE gh_current.gh_sai_enrollment_no = sai.sai_enrollment_no
            AND gh_current.gh_co_sess_id = ?
            AND gh_current.gh_status = 'E'
            AND (gh_current.gh_exemption_status <> 'Yes'
                 OR gh_current.gh_exemption_status IS NULL)
            AND gh_current.gh_exam_fsubmit = 1
      )
      AND EXISTS (
          SELECT 1
          FROM grade_history gh_workload
          WHERE gh_workload.gh_sai_enrollment_no = sai.sai_enrollment_no
            AND gh_workload.gh_co_sess_id = ?
            AND gh_workload.gh_status = 'E'
            AND (gh_workload.gh_exemption_status <> 'Yes'
                 OR gh_workload.gh_exemption_status IS NULL)
            AND gh_workload.gh_exam_fsubmit = 1
            AND gh_workload.gh_grade NOT IN ('W', 'I', 'P')
          GROUP BY gh_workload.gh_sai_enrollment_no
          HAVING (p.prog_degree_level = 'Undergraduate'
                  AND SUM(gh_workload.gh_co_course_cr) >= ?)
              OR (NVL(p.prog_degree_level, 'Other') <> 'Undergraduate'
                  AND SUM(gh_workload.gh_co_course_cr) >= ?)
      )
),
RankedStudents AS (
    SELECT
        sd.*,
        DENSE_RANK() OVER (
            PARTITION BY sd.mapped_program_id
            ORDER BY sd.calculated_cgpa DESC
        ) AS scholarship_rank,
        COUNT(*) OVER (PARTITION BY sd.mapped_program_id) AS total_students,
        COUNT(*) OVER ()                                  AS overall_pool
    FROM StudentData sd
),
TopPercent AS (
    SELECT rs.*, CEIL(rs.total_students * ?) AS cutoff_rank
    FROM RankedStudents rs
),
EligibleMBS AS (
    SELECT
        t.*,
        CEIL(t.cutoff_rank * ?) AS slab_75_count,
        CEIL(t.cutoff_rank * ?) AS slab_50_count,
        CEIL(t.cutoff_rank * ?) AS slab_75_limit,
        CEIL(t.cutoff_rank * ?)
        + CEIL(t.cutoff_rank * ?) AS slab_50_limit,
        {$meetsFloorExpr}        AS meets_floor
    FROM TopPercent t
)
SQL;

        return [$cte, $bindings];
    }

    /**
     * The eligible pool for the screen: every student who met the criteria,
     * each flagged with whether they made the merit list and on which slab.
     */
    private function runMeritQuery(
        array $sessIds,
        int $evalSession,
        float $topPct,
        float $slab75Pct,
        float $slab50Pct,
        float $cgpaFloor,
        string $floorMode
    ): array {
        [$cte, $bindings] = $this->buildMeritCte(
            $sessIds,
            $evalSession,
            $topPct,
            $slab75Pct,
            $slab50Pct,
            $cgpaFloor,
            $floorMode
        );

        $filter = self::MERIT_FILTER_SQL;
        $slab   = self::SLAB_CASE_SQL;

        $sql = <<<SQL
{$cte}
SELECT
    embs.*,
    CASE WHEN {$filter}
         THEN 'Y' ELSE 'N' END AS on_merit_list,
    CASE WHEN {$filter}
         THEN {$slab}
    END AS mbs_slab
FROM EligibleMBS embs
ORDER BY embs.combined_program_name,
         embs.scholarship_rank,
         embs.calculated_cgpa DESC,
         embs.sai_sdpo_sess_id,
         embs.sai_enrollment_no
SQL;

        $result = DB::select($sql, $bindings);

        // Oracle hands back upper-case column names; normalise for the front end.
        return array_map(function ($row) {
            return array_change_key_case((array) $row, CASE_LOWER);
        }, $result);
    }

    private function buildSummary(array $rows, array $programs): array
    {
        $slabCounts = ['75%' => 0, '50%' => 0, '25%' => 0];
        $selected   = 0;

        foreach ($rows as $row) {
            if (($row['on_merit_list'] ?? 'N') !== 'Y') {
                continue;
            }

            $selected++;

            $slab = $row['mbs_slab'] ?? null;
            if ($slab !== null && isset($slabCounts[$slab])) {
                $slabCounts[$slab]++;
            }
        }

        // Every returned row is now part of the eligible pool, selected or not.
        $pool = count($rows);

        return [
            'eligible_pool'  => $pool,
            'selected'       => $selected,
            'selected_pct'   => $pool > 0 ? round($selected / $pool * 100, 1) : 0,
            'slab_75'        => $slabCounts['75%'],
            'slab_50'        => $slabCounts['50%'],
            'slab_25'        => $slabCounts['25%'],
            'program_groups' => count($programs),
        ];
    }

    private function buildProgramSummary(array $rows): array
    {
        $programs = [];

        foreach ($rows as $row) {
            $key = $row['mapped_program_id'];

            if (!isset($programs[$key])) {
                $programs[$key] = [
                    'mapped_program_id'     => $key,
                    'combined_program_name' => $row['combined_program_name'],
                    'total_students'        => (int) $row['total_students'],
                    'cutoff_rank'           => (int) $row['cutoff_rank'],
                    'selected'              => 0,
                    'slab_75'               => 0,
                    'slab_50'               => 0,
                    'slab_25'               => 0,
                    'prog_ids'              => [],
                    'sessions'              => [],
                    'cutoff_cgpa'           => null,
                ];
            }

            $onMerit = ($row['on_merit_list'] ?? 'N') === 'Y';

            if ($onMerit) {
                $programs[$key]['selected']++;

                switch ($row['mbs_slab']) {
                    case '75%':
                        $programs[$key]['slab_75']++;
                        break;
                    case '50%':
                        $programs[$key]['slab_50']++;
                        break;
                    default:
                        $programs[$key]['slab_25']++;
                }
            }

            $progId = (int) $row['original_prog_id'];
            if (!in_array($progId, $programs[$key]['prog_ids'], true)) {
                $programs[$key]['prog_ids'][] = $progId;
            }

            $sessId = (int) $row['sai_sdpo_sess_id'];
            if (!in_array($sessId, $programs[$key]['sessions'], true)) {
                $programs[$key]['sessions'][] = $sessId;
            }

            // Cut-off CGPA is the lowest CGPA that still made the merit list.
            if ($onMerit) {
                $cgpa = (float) $row['calculated_cgpa'];
                if ($programs[$key]['cutoff_cgpa'] === null || $cgpa < $programs[$key]['cutoff_cgpa']) {
                    $programs[$key]['cutoff_cgpa'] = $cgpa;
                }
            }
        }

        foreach ($programs as $key => $program) {
            sort($programs[$key]['prog_ids']);
            sort($programs[$key]['sessions']);
            $programs[$key]['selected_pct'] = $program['total_students'] > 0
                ? round($program['selected'] / $program['total_students'] * 100, 1)
                : 0;
        }

        usort($programs, function ($a, $b) {
            return strcmp($a['combined_program_name'], $b['combined_program_name']);
        });

        return $programs;
    }
}
