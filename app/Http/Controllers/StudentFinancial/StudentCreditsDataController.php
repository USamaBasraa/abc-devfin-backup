<?php

namespace App\Http\Controllers\StudentFinancial;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Validator;
use Response;

/**
 * Single-student FIN_STUDENTS_CREDITS_DATA lookup and update.
 * Separate from bulk credit-hour challan generation.
 */
class StudentCreditsDataController extends Controller
{
    public function index()
    {
        $sessions = \App::call('App\Http\Controllers\Grader\AcademicSessionController@retrieve');

        return view('StudentFinancial.studentCreditsDataEntry', compact('sessions'));
    }

    /**
     * Load student profile, enrolled courses, calculated credits, and existing FSCD row.
     */
    public function lookup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'enrollment_no' => 'required|string|max:80',
            'session_id' => 'required',
        ]);
        if ($validator->fails()) {
            return Response::json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $enrollment = strtoupper(trim($request->enrollment_no));
        $sessionId = $request->session_id;

        try {
            $studentRequest = new Request(['studentEnrollment' => $enrollment]);
            $singleChallan = app(GenerateSingleChallanController::class);
            $studentRec = $singleChallan->getStudentRec($studentRequest);

            if (is_array($studentRec) && ! empty($studentRec['usernameError'])) {
                return Response::json([
                    'success' => false,
                    'message' => $studentRec['usernameError'],
                ], 404);
            }

            $cgpa = 'N/A';
            try {
                $cgpaRow = DB::select("SELECT cgpa(?) AS student_cgpa FROM dual", [$enrollment]);
                if (! empty($cgpaRow)) {
                    $rowArr = (array) $cgpaRow[0];
                    $val = reset($rowArr);
                    if ($val !== null && $val !== '' && $val !== 'N/A') {
                        $cgpa = is_numeric($val) ? number_format((float) $val, 2) : (string) $val;
                    }
                }
            } catch (\Exception $e) {
                $cgpa = 'N/A';
            }

            if (is_array($studentRec)) {
                $studentRec['cgpa'] = $cgpa;
            } elseif (is_object($studentRec)) {
                $studentRec->cgpa = $cgpa;
            }

            $creditChallan = app(GenerateCreditHrBulkChallanController::class);
            $coursesRequest = new Request([
                'enrollment_no' => $enrollment,
                'session_id' => $sessionId,
            ]);
            $coursesResponse = $creditChallan->getEnrollmentStudentCourses($coursesRequest);
            $coursesPayload = json_decode($coursesResponse->getContent(), true);
            if (empty($coursesPayload['success'])) {
                return Response::json([
                    'success' => false,
                    'message' => $coursesPayload['message'] ?? 'Could not load courses for this enrollment.',
                ], 500);
            }

            $calculated = $this->calculateStudentCredits($enrollment, $sessionId);
            if ($calculated === null) {
                return Response::json([
                    'success' => false,
                    'message' => "No active/enrolled courses found for student '$enrollment' in the selected session.",
                    'studentRec' => $studentRec,
                    'courses' => $coursesPayload['data'] ?? [],
                ], 404);
            }

            $existing = $this->fetchExistingCreditsRow($enrollment, $sessionId);

            return Response::json([
                'success' => true,
                'studentRec' => $studentRec,
                'courses' => $coursesPayload['data'] ?? [],
                'calculated' => $calculated,
                'existing' => $existing,
            ]);
        } catch (\Exception $e) {
            return Response::json([
                'success' => false,
                'message' => 'An error occurred while loading student data.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Insert or update FIN_STUDENTS_CREDITS_DATA for one student + session.
     */
    public function update(Request $request)
    {
        $allowedUsernames = ['saqib.jamil', 'esha.abid', 'ahmad.arfeen', 'asif_nadeem', 'muhammad.nadeem', 'qasim.ali'];
        $user = auth()->user();
        if ($user && (int) $user->group_id !== 644 && ! in_array($user->username, $allowedUsernames, true)) {
            return Response::json([
                'success' => false,
                'message' => 'Unauthorized.',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'enrollment_no' => 'required|string|max:80',
            'session_id' => 'required',
        ]);
        if ($validator->fails()) {
            return Response::json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $enrollment = strtoupper(trim($request->enrollment_no));
        $sessionId = $request->session_id;

        try {
            $calculated = $this->calculateStudentCredits($enrollment, $sessionId);
            if ($calculated === null) {
                return Response::json([
                    'success' => false,
                    'message' => "No active/enrolled courses found for student '$enrollment' in the selected session.",
                ], 404);
            }

            $existing = $this->fetchExistingCreditsRow($enrollment, $sessionId);
            $action = 'updated';

            DB::beginTransaction();

            if ($existing === null) {
                $this->insertCreditsViaProcedure($calculated, $sessionId);
                $action = 'inserted';
            } elseif ($this->creditsRowChanged($existing, $calculated)) {
                $this->updateCreditsRow($enrollment, $sessionId, $calculated);
            } else {
                $action = 'no_change';
            }

            $this->refreshDerivedCreditsFields($enrollment, $sessionId);

            DB::commit();

            $row = $this->fetchExistingCreditsRow($enrollment, $sessionId);

            $msg = $action === 'inserted'
                ? 'Credits data inserted successfully.'
                : ($action === 'no_change'
                    ? 'Credits data is already up to date. Derived fields refreshed.'
                    : 'Credits data updated successfully.');

            return Response::json([
                'success' => true,
                'message' => $msg,
                'action' => $action,
                'existing' => $row,
                'calculated' => $calculated,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return Response::json([
                'success' => false,
                'message' => 'Process failed. Transaction rolled back.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    /**
     * Generate one credit-hour challan from the Student Credits Data screen
     * without opening the bulk challan page. The actual challan rules remain
     * inside GenerateCreditHrBulkChallanController.
     */
    public function generateChallan(Request $request)
    {
        $allowedUsernames = ['saqib.jamil', 'esha.abid', 'ahmad.arfeen', 'asif_nadeem', 'muhammad.nadeem', 'qasim.ali'];
        $user = auth()->user();
        if ($user && (int) $user->group_id !== 644 && ! in_array($user->username, $allowedUsernames, true)) {
            return Response::json([
                'success' => false,
                'message' => 'Unauthorized.',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'enrollment_no' => 'required|string|max:80',
            'session_id'    => 'required',
            'due_date'      => 'required|date',
            'valid_date'    => 'required|date|after_or_equal:due_date',
            'visible_date'  => 'required|date',
        ]);

        if ($validator->fails()) {
            return Response::json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors'  => $validator->errors(),
            ], 422);
        }

        $enrollment = strtoupper(trim($request->enrollment_no));
        $sessionId = $request->session_id;

        try {
            $student = DB::table('STUDENTS_APPLY_INFO')
                ->whereRaw('LOWER(SAI_ENROLLMENT_NO) = ?', [strtolower($enrollment)])
                ->first();

            if (! $student) {
                return Response::json([
                    'success' => false,
                    'message' => 'Student record not found.',
                ], 404);
            }

            $creditRow = $this->fetchExistingCreditsRow($enrollment, $sessionId);
            if (! $creditRow) {
                return Response::json([
                    'success' => false,
                    'message' => 'Credit data does not exist for this student/session. Please click Update first.',
                ], 422);
            }

            $fscdId = $this->getProp($creditRow, 'fscd_id');
            $degreeLevel = $this->getProp($creditRow, 'fscd_std_degree_lvl');
            $studentStatus = $this->getProp($creditRow, 'fscd_std_status');

            if (! $fscdId) {
                return Response::json([
                    'success' => false,
                    'message' => 'FSCD ID could not be found for this student/session.',
                ], 422);
            }

            if (! $degreeLevel) {
                return Response::json([
                    'success' => false,
                    'message' => 'Student degree level could not be determined.',
                ], 422);
            }

            if (! $studentStatus) {
                return Response::json([
                    'success' => false,
                    'message' => 'Student status could not be determined.',
                ], 422);
            }

            $challanRequest = Request::create('/student-credits-data/generate-challan-internal', 'POST', [
                'session'          => $sessionId,
                'student_type'     => 'Existing',
                'fee_type'         => 'Credit_hours',
                'student_status'   => $studentStatus,
                'degree_level'     => $degreeLevel,
                'due_date'         => $request->due_date,
                'valid_date'       => $request->valid_date,
                'visible_date'     => $request->visible_date,
                'students'         => (string) $student->sai_id,
                'fscd_ids'         => (string) $fscdId,
                // Match the original bulk page: these two controls are hidden and
                // remain empty in the current implementation.
                'roadmap_session'  => null,
                'semester_sel'     => null,
                'confirm_proceed'  => 0,
            ]);

            // Keep the original authenticated user/resolver on the internal request.
            $challanRequest->setUserResolver(function () {
                return auth()->user();
            });

            $challanController = app(GenerateCreditHrBulkChallanController::class);
            return $challanController->addChallan($challanRequest);

        } catch (\Exception $e) {
            return Response::json([
                'success' => false,
                'message' => 'Challan generation failed.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Same enrolled / repeat credit logic as bulk creditHr_data_entry.
     */
    protected function calculateStudentCredits(string $enrollment, $sessionId): ?object
    {
        $sql = "WITH all_students AS (
                SELECT
                    sc_sai_enrollment_no,
                    sai_student_status,
                    PROG_DEGREE_LEVEL,
                    SUM(SC_PC_COURSE_CR) AS total_credits
                FROM student_courses
                JOIN courses_offered co ON sc_cms_course_co_id = co.co_id
                JOIN students_apply_info sai ON sai.sai_enrollment_no = sc_sai_enrollment_no
                JOIN programs p ON sai.sai_sdpo_prog_id = p.prog_id
                WHERE
                    SC_ENROLLMENT_STATUS = 1
                    AND SC_STATUS IN ('A', 'Withdrawal', 'Block')
                    AND SC_CO_SESS_ID = ?
                    AND LOWER(sc_sai_enrollment_no) = LOWER(?)
                    AND sc_sai_enrollment_no NOT LIKE '%SUM%'
                    AND CO_STATUS = 'E'
                    AND co_course_id NOT IN (SELECT CE_COURSE_ID FROM course_exempted)
                GROUP BY sc_sai_enrollment_no, sai_student_status, PROG_DEGREE_LEVEL
            ),
            repeat_cred AS (
                SELECT
                    e.sc_sai_enrollment_no,
                    SUM(SC_PC_COURSE_CR) AS total_credits
                FROM student_courses e
                JOIN courses_offered co ON e.sc_cms_course_co_id = co.co_id
                JOIN students_apply_info sai ON sai.sai_enrollment_no = e.sc_sai_enrollment_no
                JOIN programs p ON sai.sai_sdpo_prog_id = p.prog_id
                WHERE
                    e.SC_ENROLLMENT_STATUS = 1
                    AND e.SC_STATUS IN ('A', 'Withdrawal', 'Block')
                    AND e.SC_CO_SESS_ID = ?
                    AND LOWER(e.sc_sai_enrollment_no) = LOWER(?)
                    AND co.CO_STATUS = 'E'
                    AND e.sc_sai_enrollment_no NOT LIKE '%SUM%'
                    AND co.co_course_id NOT IN (SELECT CE_COURSE_ID FROM course_exempted)
                    AND EXISTS (
                        SELECT 1
                        FROM grade_history gh
                        WHERE gh.gh_sai_enrollment_no = e.sc_sai_enrollment_no
                          AND gh.gh_co_course_id = co.co_course_id
                          AND gh.gh_co_sess_id >= 163
                          AND gh.gh_co_sess_id != ?
                    )
                GROUP BY e.sc_sai_enrollment_no
            )
            SELECT
                ec.sc_sai_enrollment_no AS regno,
                ec.total_credits AS total_enrolled_credits,
                NVL(rc.total_credits, 0) AS total_repeated_credits,
                ec.sai_student_status,
                ec.PROG_DEGREE_LEVEL AS prog_degree_level
            FROM all_students ec
            LEFT JOIN repeat_cred rc ON ec.sc_sai_enrollment_no = rc.sc_sai_enrollment_no";

        $rows = DB::select($sql, [$sessionId, $enrollment, $sessionId, $enrollment, $sessionId]);

        return ! empty($rows) ? $rows[0] : null;
    }

    protected function fetchExistingCreditsRow(string $enrollment, $sessionId): ?object
    {
        $rows = DB::select("
            SELECT
                f.*,
                acd_enroll.SESS_NAME AS enrollment_session_name,
                acd_roadmap.SESS_NAME AS roadmap_session_name,
                sch.SCHOOL_NAME,
                dept.DEPT_NAME,
                prog.PROG_NAME
            FROM FIN_STUDENTS_CREDITS_DATA f
            LEFT JOIN ACADEMIC_SESSIONS acd_enroll ON acd_enroll.SESS_ID = f.FSCD_STD_ENROLLMENT_SESSION
            LEFT JOIN ACADEMIC_SESSIONS acd_roadmap ON acd_roadmap.SESS_ID = f.FSCD_STD_ROADMAP_SESSION
            LEFT JOIN SCHOOLS sch ON sch.SCHOOL_ID = f.FSCD_STD_SCHOOL_ID
            LEFT JOIN DEPARTMENTS dept ON dept.DEPT_ID = f.FSCD_STD_DEPT_ID
            LEFT JOIN PROGRAMS prog ON prog.PROG_ID = f.FSCD_STD_PROG_ID
            WHERE LOWER(f.FSCD_REGNO) = LOWER(?)
              AND f.FSCD_STD_ENROLLMENT_SESSION = ?
        ", [$enrollment, $sessionId]);

        return ! empty($rows) ? $rows[0] : null;
    }

    protected function insertCreditsViaProcedure(object $calculated, $sessionId): void
    {
        DB::executeProcedure('fin_credit_hr_wise_challan_dummy', [
            'regnoo' => $calculated->regno,
            'session_id' => $sessionId,
            'v_enrolled' => $calculated->total_enrolled_credits,
            'v_repeate' => $calculated->total_repeated_credits,
            'v_status' => $calculated->sai_student_status,
            'v_prog_degree_level' => $calculated->prog_degree_level,
        ]);
    }

    protected function getProp($obj, string $prop)
    {
        if ($obj === null) {
            return null;
        }
        if (isset($obj->$prop)) {
            return $obj->$prop;
        }
        $upper = strtoupper($prop);
        if (isset($obj->$upper)) {
            return $obj->$upper;
        }
        $lower = strtolower($prop);
        if (isset($obj->$lower)) {
            return $obj->$lower;
        }
        return null;
    }

    protected function creditsRowChanged(object $existing, object $calculated): bool
    {
        $existingEnrolled = $this->getProp($existing, 'fscd_std_enrolled_credits');
        $calcEnrolled = $this->getProp($calculated, 'total_enrolled_credits');
        if (abs((float) ($existingEnrolled ?? 0) - (float) ($calcEnrolled ?? 0)) > 0.00001) {
            return true;
        }

        $existingRepeat = $this->getProp($existing, 'fscd_std_repeat_credits');
        $calcRepeat = $this->getProp($calculated, 'total_repeated_credits');
        if (abs((float) ($existingRepeat ?? 0) - (float) ($calcRepeat ?? 0)) > 0.00001) {
            return true;
        }

        $existingStatus = (string) ($this->getProp($existing, 'fscd_std_status') ?? '');
        $calcStatus = (string) ($this->getProp($calculated, 'sai_student_status') ?? '');
        if (strtolower(trim($existingStatus)) !== strtolower(trim($calcStatus))) {
            return true;
        }

        $existingDegree = (string) ($this->getProp($existing, 'fscd_std_degree_lvl') ?? '');
        $calcDegree = (string) ($this->getProp($calculated, 'prog_degree_level') ?? '');
        if (strtolower(trim($existingDegree)) !== strtolower(trim($calcDegree))) {
            return true;
        }

        return false;
    }

    protected function updateCreditsRow(string $enrollment, $sessionId, object $calculated): void
    {
        DB::table('FIN_STUDENTS_CREDITS_DATA')
            ->whereRaw('LOWER(FSCD_REGNO) = ?', [strtolower($enrollment)])
            ->where('FSCD_STD_ENROLLMENT_SESSION', $sessionId)
            ->update([
                'FSCD_STD_ENROLLED_CREDITS' => $calculated->total_enrolled_credits,
                'FSCD_STD_REPEAT_CREDITS' => $calculated->total_repeated_credits,
                'FSCD_STD_STATUS' => $calculated->sai_student_status,
                'FSCD_STD_DEGREE_LVL' => $calculated->prog_degree_level,
            ]);
    }

    /**
     * Refresh earned credits, scholarship %, and challan totals for one student (bulk update logic scoped).
     */
    protected function refreshDerivedCreditsFields(string $enrollment, $sessionId): void
    {
        $query = "UPDATE FIN_STUDENTS_CREDITS_DATA f
                SET
                    f.FSCD_STD_ENROLLED_CREDITS =
                        NVL((
                            SELECT SUM(sc.SC_PC_COURSE_CR)
                            FROM STUDENT_COURSES sc
                            WHERE sc.SC_SAI_ENROLLMENT_NO = f.FSCD_REGNO
                            AND sc.SC_CO_SESS_ID = :session_id
                        ), 0),

                    f.FSCD_STD_EARNED_CREDITS =
                        NVL((
                            SELECT SUM(x.GH_CO_COURSE_CR)
                            FROM (
                                SELECT
                                    c.*,
                                    RANK() OVER (
                                        PARTITION BY c.GH_SAI_ENROLLMENT_NO,
                                                    c.GH_CO_COURSE_ID
                                        ORDER BY c.GH_ID DESC
                                    ) RNK
                                FROM GRADE_HISTORY c
                                WHERE c.GH_SAI_ENROLLMENT_NO = f.FSCD_REGNO
                                AND NVL(c.GH_GRADE, 'X') NOT IN ('W', 'F')
                                AND gh_co_sess_id != :session_id
                            ) x
                            WHERE x.RNK = 1
                        ), 0),

                    f.FSCD_SUM_SCHOLARSHIP_PERCENT =
                        NVL((
                            SELECT SUM(s.SSD_PERCENTAGE)
                            FROM BNUCMS.STUDENTS_SCHOLARSHIPS_DETAILS s
                            WHERE s.SSD_APP_ID = f.FSCD_APP_ID
                            AND s.SSD_SESS_ID = f.FSCD_STD_ENROLLMENT_SESSION
                        ), 0),

                    f.FSCD_SUM_CHALLAN_AMOUNT =
                        NVL((
                            SELECT SUM(c.INVOICEAMOUNT)
                            FROM STUDENT_CHALLAN c
                            WHERE c.SC_APP_ID = f.FSCD_APP_ID
                            AND c.SC_SESSION_ID = f.FSCD_STD_ENROLLMENT_SESSION
                            AND c.SC_CHALLAN_TYPE IN ('Full_semester', 'Credit_hours')
                        ), 0),

                    f.FSCD_SUM_CHALLAN_ARREARS =
                        NVL((
                            SELECT SUM(c.SC_SSD_ARREAR)
                            FROM STUDENT_CHALLAN c
                            WHERE c.SC_APP_ID = f.FSCD_APP_ID
                            AND c.SC_SESSION_ID = f.FSCD_STD_ENROLLMENT_SESSION
                            AND c.SC_CHALLAN_TYPE IN ('Full_semester', 'Credit_hours')
                        ), 0)

                WHERE f.FSCD_STD_ENROLLMENT_SESSION = :session_id
                  AND LOWER(f.FSCD_REGNO) = LOWER(:enrollment)";

        DB::update($query, [
            'session_id' => $sessionId,
            'enrollment' => $enrollment,
        ]);
    }
}
