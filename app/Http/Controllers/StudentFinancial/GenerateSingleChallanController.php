<?php

namespace App\Http\Controllers\StudentFinancial;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Grader\Schools;
use App\Models\Grader\Departments;
use DB;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\StudentFinancial\ScholarshipAssignController;
use DateTime;
use App\Models\Grader\SendSms;
use Response;

/**
 * Single Challan Generation (Student Financial).
 *
 * Flow:
 *  - index / searchStudentChallans / getFeeStructure  → screen data
 *  - submitSingleChallanGenerate                     → create/update challan (routes by fee_type)
 *  - cancelChallans / filterChallans                 → cancel & list filters
 *
 * Business conditions inside fee-type branches are intentional — do not alter without QA.
 */
class GenerateSingleChallanController extends Controller
{
    /**
     * Pending single-challan edit audit (previous snapshot + remarks).
     * Filled on verifyEdit; flushed on successful response (does not alter generate flow).
     *
     * @var array|null
     */
    protected $pendingEditAudit = null;

    // -------------------------------------------------------------------------
    // Shared helpers (edit id / challan_no)
    // -------------------------------------------------------------------------

    /**
     * Resolve student_challan by CMS id or printed challan_no (legacy rows differ).
     * Prefer id for updates; accept either so older UI values still work.
     */
    protected function resolveStudentChallanByEditId($editId)
    {
        if ($editId === null || $editId === '') {
            return null;
        }
        $row = DB::table('student_challan')->where('id', $editId)->first();
        if ($row) {
            return $row;
        }
        return DB::table('student_challan')->where('challan_no', $editId)->first();
    }

    /**
     * Snapshot fee/date fields used for edit audit trail.
     */
    protected function snapshotChallanForAudit($row): array
    {
        if (!$row) {
            return [];
        }
        return [
            'id' => $row->id ?? null,
            'challan_no' => $row->challan_no ?? null,
            'sc_challan_type' => $row->sc_challan_type ?? null,
            'sc_challan_status' => $row->sc_challan_status ?? null,
            'sc_enrolled_cr_hr' => $row->sc_enrolled_cr_hr ?? null,
            'sc_tuition_fee_amnt' => $row->sc_tuition_fee_amnt ?? null,
            'credit_hr_fee' => $row->sc_tuition_fee_amnt ?? null,
            'ffs_admission_amt' => $row->ffs_admission_amt ?? null,
            'ffs_security_amt' => $row->ffs_security_amt ?? null,
            'ffs_medical_amt' => $row->ffs_medical_amt ?? null,
            'sc_activity_amt' => $row->sc_activity_amt ?? null,
            'ffs_hostel_amt' => $row->ffs_hostel_amt ?? null,
            'invoiceamount' => $row->invoiceamount ?? null,
            'total_amount_payable' => $row->total_amount_payable ?? null,
            'sc_scholarship_amount' => $row->sc_scholarship_amount ?? null,
            'sc_due_date' => $row->sc_due_date ?? null,
            'sc_valid_date' => $row->sc_valid_date ?? null,
            'sc_installment_allowed' => $row->sc_installment_allowed ?? null,
            'sc_refrigerator' => $row->sc_refrigerator ?? null,
            'sc_conditioner' => $row->sc_conditioner ?? null,
            'sc_electric' => $row->sc_electric ?? null,
            'sc_manually_edited' => $row->sc_manually_edited ?? null,
        ];
    }

    /**
     * Require remarks on edit and start audit snapshot. Returns error response or null.
     * Fee Adjustment edits append "-manual" so manual adjustments can be counted in USER_REMARKS.
     */
    protected function beginChallanEditAudit(Request $request, $challan)
    {
        $remarks = trim((string) $request->input('editRemarks', $request->input('edit_remarks', '')));
        if ($remarks === '') {
            return response()->json([
                'success' => false,
                'status' => 'error',
                'message' => 'Remarks are required when editing a challan.',
                'errors' => ['editRemarks' => ['Remarks are required when editing a challan.']],
            ], 422);
        }

        // Tag Fee Adjustment edits (avoid duplicate; keep within 500 chars)
        if ($request->input('manualFeeAdjustment') === 'YES') {
            $remarks = $this->appendManualFeeAdjustmentTag($remarks);
        }
        $request->merge(['editRemarks' => $remarks]);

        if (!$challan) {
            return null;
        }
        $this->pendingEditAudit = [
            'id' => $challan->id,
            'challan_no' => $challan->challan_no ?? null,
            'previous' => $this->snapshotChallanForAudit($challan),
            'remarks' => $remarks,
            'username' => auth()->user()->username ?? null,
        ];
        return null;
    }

    /**
     * Append -manual for Fee Adjustment tracking without exceeding remarks max length.
     */
    protected function appendManualFeeAdjustmentTag(string $remarks, int $maxLen = 500): string
    {
        if (stripos($remarks, '-manual') !== false) {
            return $remarks;
        }
        $tag = '-manual';
        if (strlen($remarks) + strlen($tag) > $maxLen) {
            $remarks = rtrim(substr($remarks, 0, $maxLen - strlen($tag)));
        }
        return $remarks . $tag;
    }

    /**
     * New Full Semester generate: Fee Adjustment is not allowed (Surplus + Installments OK).
     * Enforced server-side so crafted POSTs cannot bypass the Fee Adjustment UI lock.
     */
    protected function enforceNewFullSemesterRestrictions(Request $request): void
    {
        if ($request->input('fee_type') !== 'Full_semester') {
            return;
        }
        if ($request->input('verifyEdit') == '1') {
            return; // edit mode — Fee Adj / installments remain available
        }
        // First-time generate: keep Fee Adjustment off; Allow Installment / split stays from request
        $request->merge([
            'manualFeeAdjustment' => 'NO',
        ]);
    }

    /**
     * Processing Fee: Surplus Adjustment is not allowed (generate / crafted POST).
     */
    protected function enforceProcessingFeeSurplusOff(Request $request): void
    {
        if ($request->input('fee_type') !== 'Processing_Fee') {
            return;
        }
        $request->merge([
            'surplusAdjustment' => 'NO',
            'surplus_apply_amount' => null,
            'surplus_ffa_id' => null,
        ]);
    }

    /**
     * Full Semester edit: scholarship challans cannot use Fee Adjustment (UI + crafted POST).
     */
    protected function enforceScholarshipBlocksFullSemesterFeeAdjustment(Request $request): void
    {
        if ((string) $request->input('verifyEdit') !== '1') {
            return;
        }
        if ($request->input('fee_type') !== 'Full_semester') {
            return;
        }
        $challan = $this->resolveStudentChallanByEditId($request->input('edit_id'));
        if (!$challan) {
            return;
        }
        if (floatval($challan->sc_scholarship_amount ?? 0) > 0) {
            $request->merge(['manualFeeAdjustment' => 'NO']);
        }
    }

    /**
     * Persist edit audit + manually-edited flag. Soft-fails if DDL not applied yet.
     */
    protected function persistEditAuditIfNeeded(): void
    {
        if (empty($this->pendingEditAudit) || empty($this->pendingEditAudit['id'])) {
            return;
        }
        $audit = $this->pendingEditAudit;
        $this->pendingEditAudit = null;

        try {
            $updated = DB::table('student_challan')->where('id', $audit['id'])->first();
            $updatedSnapshot = $this->snapshotChallanForAudit($updated);

            DB::table('FIN_SINGLE_CHALLAN_EDIT_AUDIT')->insert([
                'SC_ID' => $audit['id'],
                'CHALLAN_NO' => $audit['challan_no'] ?? ($updated->challan_no ?? null),
                'PREVIOUS_VALUES' => json_encode($audit['previous']),
                'UPDATED_VALUES' => json_encode($updatedSnapshot),
                'USER_REMARKS' => $audit['remarks'],
                'EDITED_BY' => $audit['username'],
                'EDITED_AT' => now(),
            ]);

            DB::table('student_challan')
                ->where('id', $audit['id'])
                ->update([
                    'SC_MANUALLY_EDITED' => 'YES',
                    'SC_UPDATED_BY_USERNAME' => $audit['username'],
                    'UPDATED_AT' => now(),
                ]);
        } catch (\Throwable $e) {
            \Log::warning('Single challan edit audit not saved: ' . $e->getMessage(), [
                'sc_id' => $audit['id'] ?? null,
            ]);
        }
    }

    /**
     * Validation-style JSON error (readable for FE — never a bare string in errors).
     */
    protected function challanClientError(string $message, int $status = 422)
    {
        $this->pendingEditAudit = null;
        return response()->json([
            'success' => false,
            'status' => 'error',
            'message' => $message,
            'errors' => ['error' => [$message]],
        ], $status);
    }

    /**
     * Success JSON — always includes success:true so FE never treats as silent success falsely.
     * Flushes pending edit audit after a successful edit.
     */
    protected function challanSuccess(string $message, array $extra = [], int $httpStatus = 200)
    {
        $this->persistEditAuditIfNeeded();
        return response()->json(array_merge([
            'success' => true,
            'status' => 'success',
            'message' => $message,
        ], $extra), $httpStatus);
    }

    /**
     * Rollback open txn (if any) then return client error.
     */
    protected function challanFailRollback(string $message, int $status = 422)
    {
        $this->pendingEditAudit = null;
        try {
            DB::rollBack();
        } catch (\Throwable $e) {
            // no open transaction
        }
        return $this->challanClientError($message, $status);
    }

    /**
     * Campus key from Session Type / HT_ID.
     * HT_ID: 1-2 = Regular (BNU), 3-4 = Safari, 5-6 = Bedian.
     * sess_type: Regular / Non-Regular / *_outside / *_outside_bedian.
     */
    protected function hostelCampusKeyFromHtId(int $htId): string
    {
        if (in_array($htId, [3, 4], true)) {
            return 'safari';
        }
        if (in_array($htId, [5, 6], true)) {
            return 'bedian';
        }
        return 'regular';
    }

    protected function hostelCampusKeyFromSessType(string $sessType): string
    {
        $s = strtolower(trim($sessType));
        if (strpos($s, 'outside_bedian') !== false || substr($s, -7) === '_bedian') {
            return 'bedian';
        }
        if (strpos($s, 'outside') !== false) {
            return 'safari';
        }
        return 'regular';
    }

    /**
     * Map hostel_assign.ha_hostel_session_type → dropdown / fhfs_session_type casing.
     * DB: regular, non-regular, regular_outside, …
     * UI: Regular, Non-Regular, Regular_outside, …
     */
    protected function normalizeHostelSessType(?string $haType): ?string
    {
        if ($haType === null || trim($haType) === '') {
            return null;
        }
        $map = [
            'regular' => 'Regular',
            'non-regular' => 'Non-Regular',
            'non_regular' => 'Non-Regular',
            'regular_outside' => 'Regular_outside',
            'non-regular_outside' => 'Non-Regular_outside',
            'non_regular_outside' => 'Non-Regular_outside',
            'regular_outside_bedian' => 'Regular_outside_bedian',
            'non-regular_outside_bedian' => 'Non-Regular_outside_bedian',
            'non_regular_outside_bedian' => 'Non-Regular_outside_bedian',
        ];
        $key = strtolower(trim($haType));

        return $map[$key] ?? trim($haType);
    }

    /**
     * Assigned hostel_assign.ha_hostel_session_type for student + session (UI casing), or null.
     */
    protected function resolveAssignedHostelSessType($studentId, $sessionId): ?string
    {
        if (empty($studentId) || empty($sessionId)) {
            return null;
        }
        $assigned = DB::table('hostel_assign as ha')
            ->where('ha.ha_session', $sessionId)
            ->where(function ($q) use ($studentId) {
                if (is_numeric($studentId)) {
                    $q->where('ha.ha_aai_id', $studentId);
                } else {
                    $q->whereRaw('UPPER(TRIM(ha.ha_enrolment_no)) = ?', [strtoupper(trim((string) $studentId))]);
                }
            })
            ->where('ha.ha_hb_id', '!=', null)
            ->orderByDesc('ha.ha_id')
            ->select('ha.ha_hostel_session_type')
            ->first();

        if (empty($assigned) || empty($assigned->ha_hostel_session_type)) {
            return null;
        }

        return $this->normalizeHostelSessType((string) $assigned->ha_hostel_session_type);
    }

    protected function isNonRegularHostelSessType(?string $sessType): bool
    {
        $s = strtolower(trim((string) $sessType));
        if ($s === '') {
            return false;
        }
        return strpos($s, 'non-regular') !== false || strpos($s, 'non_regular') !== false;
    }

    /**
     * Regular assign → Full Semester only; Non-Regular assign → Day Wise only.
     * Other Utilities is not restricted. Null when OK / not assigned.
     */
    protected function hostelRegularityChallanMismatchMessage(?string $feeTypeForHostel, ?string $assignedSessType, bool $hostelAssigned): ?string
    {
        if (!$hostelAssigned || $assignedSessType === null || trim((string) $assignedSessType) === '') {
            return null;
        }
        $nonRegular = $this->isNonRegularHostelSessType($assignedSessType);
        if ($feeTypeForHostel === 'Hostel_Full_semster' && $nonRegular) {
            return 'This student is assigned a Non-Regular (day-wise) hostel for the selected session. '
                . 'A Hostel Full Semester challan cannot be issued. Please generate a Hostel Day Wise challan.';
        }
        if ($feeTypeForHostel === 'Hostel_day_wise' && !$nonRegular) {
            return 'This student is assigned a Regular hostel for the selected session. '
                . 'A Hostel Day Wise challan cannot be issued. Please generate a Hostel Full Semester challan.';
        }
        return null;
    }

    protected function hostelCampusLabel(string $campusKey): string
    {
        $labels = [
            'regular' => 'Regular',
            'safari' => 'Safari',
            'bedian' => 'Bedian',
        ];
        return $labels[$campusKey] ?? ucfirst($campusKey);
    }

    /**
     * Whether hostel_assign has a row for this student + session.
     */
    protected function studentHasHostelAssignForSession($studentId, $sessionId): bool
    {
        if (empty($studentId) || empty($sessionId)) {
            return false;
        }
        return DB::table('hostel_assign')
            ->where('ha_session', $sessionId)
            ->where(function ($q) use ($studentId) {
                if (is_numeric($studentId)) {
                    $q->where('ha_aai_id', $studentId);
                } else {
                    $q->whereRaw('UPPER(TRIM(ha_enrolment_no)) = ?', [strtoupper(trim((string) $studentId))]);
                }
            })
            ->where('ha_hb_id', '!=', null)
            ->exists();
    }
    
    /**
     * Look up hostel_assign for student + session.
     * Returns campus key (regular|safari|bedian) or null if not assigned.
     */
    protected function resolveAssignedHostelCampus($studentId, $sessionId)
    {
        if (empty($studentId) || empty($sessionId)) {
            return null;
        }
        $assigned = DB::table('hostel_assign as ha')
            ->join('hostel_beds as hb', 'hb.hb_id', '=', 'ha.ha_hb_id')
            ->join('hostel_rooms as hr', 'hr.hr_id', '=', 'hb.hb_room_id')
            ->join('hostel_type as ht', 'ht.ht_id', '=', 'hr.hr_ht_id')
            ->where('ha.ha_session', $sessionId)
            ->where(function ($q) use ($studentId) {
                if (is_numeric($studentId)) {
                    $q->where('ha.ha_aai_id', $studentId);
                } else {
                    $q->whereRaw('UPPER(TRIM(ha.ha_enrolment_no)) = ?', [strtoupper(trim((string) $studentId))]);
                }
            })
            ->orderByDesc('ha.ha_id')
            ->select('ht.ht_id')
            ->first();

        if (empty($assigned)) {
            return null;
        }
        return $this->hostelCampusKeyFromHtId((int) $assigned->ht_id);
    }

    /**
     * Hostel Full Semester: selected sess_type must match hostel_assign campus for that session.
     * Returns error message on mismatch, null when OK / unassigned.
     */
    protected function hostelFullSemesterAssignMismatchMessage($studentId, $sessionId, $selectedSessType)
    {
        $assignedCampus = $this->resolveAssignedHostelCampus($studentId, $sessionId);
        if ($assignedCampus === null) {
            return null; // no assignment → do not block (manual sess_type allowed)
        }
        $selectedCampus = $this->hostelCampusKeyFromSessType((string) $selectedSessType);
        if ($assignedCampus === $selectedCampus) {
            return null;
        }
        $assignedLabel = $this->hostelCampusLabel($assignedCampus);
        $selectedLabel = $this->hostelCampusLabel($selectedCampus);
        return "Hostel assign mein {$assignedLabel} hai but aap {$selectedLabel} ka challan issue kar rahe ho.";
    }

    // -------------------------------------------------------------------------
    // Page bootstrap
    // -------------------------------------------------------------------------

    /**
     * Load Single Challan Generation screen.
     */
    public function index(Request $request)
    {
        $sessions = \App::call('App\Http\Controllers\Grader\AcademicSessionController@retrieve');
        $scholarships = DB::table('scholarship_type')
            ->select('scholarship_id', 'scholarship_name')
            ->get();
        $schools = Schools::all();
        $departments = array();
        $controller = new ScholarshipAssignController();
        $studentScholarshipRec = $controller->getStudentScholarshipRec();
        $query = 'select * from semesters order by SEMESTER_ID';
        $semesters = DB::select($query);

        return view(
            'StudentFinancial.generateSingleChalln',
            compact('scholarships', 'sessions', 'schools', 'departments', 'studentScholarshipRec', 'semesters')
        );
    }

    // -------------------------------------------------------------------------
    // Shared helpers (no business-condition changes)
    // -------------------------------------------------------------------------

    /**
     * Format date for Oracle timestamp bindings.
     */
    private function toOracleTimestamp($date)
    {
        return Carbon::parse($date)->format('d-M-y h.i.s.u A');
    }

    /**
     * Resolve student apply / admission record.
     * Enrollment (non-numeric) → students_apply_info; Applicant ID (numeric) → admission_apply_info.
     */
    private function findStudentApplyRecord($studentEnrollment)
    {
        $isCharacter = !is_numeric($studentEnrollment);

        if ($isCharacter) {
            return DB::table('students_apply_info')
                ->leftJoin(
                    'admission_apply_info',
                    'students_apply_info.sai_enrollment_no',
                    '=',
                    'admission_apply_info.cms_sai_enrollment_no'
                )
                ->where('students_apply_info.sai_enrollment_no', $studentEnrollment)
                ->first();
        }

        return DB::table('admission_apply_info')
            ->select(
                'aai_id',
                'cms_sai_enrollment_no as sai_enrollment_no',
                'aai_sdpo_school_id as sai_sdpo_school_id',
                'aai_sdpo_dept_id as sai_sdpo_dept_id',
                'aai_sdpo_prog_id as sai_sdpo_prog_id'
            )
            ->where('aai_id', $studentEnrollment)
            ->first();
    }

    /**
     * Convert installment due/validate date arrays from request to DB datetime strings.
     *
     * @return array{0: array, 1: array} [installmentDueDates, installmentValidateDates]
     */
    private function getInstallmentDatesFromRequest(Request $request)
    {
        $installmentDueDates = array_map(function ($date) {
            return Carbon::parse($date)->format('Y-m-d H:i:s');
        }, $request->input('installmentDueDate') ?? []);

        $installmentValidateDates = array_map(function ($date) {
            return Carbon::parse($date)->format('Y-m-d H:i:s');
        }, $request->input('installmentValidateDate') ?? []);

        return [$installmentDueDates, $installmentValidateDates];
    }

    /**
     * Resolve app id for FIN_NEW_STD_SINGLE_CHLN_UPDT (numeric = AAI_ID, else enrollment lookup).
     */
    protected function resolveAppIdForFeeAdjustment($studentEnrollment)
    {
        if (is_numeric($studentEnrollment)) {
            return $studentEnrollment;
        }
        return $this->getStudentAaiId($studentEnrollment);
    }

    /**
     * Tuition amount for manual fee adjustment.
     * Credit_hours / Short/Summer_Course → creditHrFee (fallback tuitionFee).
     * Full_semester → tuitionFee.
     */
    protected function resolveTuitionForFeeAdjustment($request, $feeType = null)
    {
        $feeType = $feeType ?: $request->input('fee_type');
        if (in_array($feeType, ['Credit_hours', 'Short/Summer_Course'], true)) {
            $creditHrFee = $request->input('creditHrFee');
            if ($creditHrFee !== null && $creditHrFee !== '') {
                return $creditHrFee;
            }
        }
        return $request->input('tuitionFee') ?? 0;
    }

    /**
     * Load fin_fee_structure row for Full_semester / Short/Summer fee adjustment validation.
     */
    protected function fetchFullSemesterFeeStructureRow($request): ?object
    {
        $feeType = $request->input('fee_type');
        if (!in_array($feeType, ['Full_semester', 'Short/Summer_Course'], true)) {
            return null;
        }

        $sessId = $request->input('sessionId');
        $schoolId = $request->input('st_school_id');
        $deptId = $request->input('st_dept_id');
        $progId = $request->input('st_prog_id');
        $studentType = $request->input('student_type');
        if (empty($sessId) || empty($schoolId) || empty($deptId) || empty($progId) || empty($studentType)) {
            return null;
        }

        $query = DB::table('fin_fee_structure')
            ->where('ffs_sess_id', $sessId)
            ->where('ffs_school_id', $schoolId)
            ->where('ffs_dept_id', $deptId)
            ->where('ffs_prog_id', $progId)
            ->where('ffs_student_type', $studentType)
            ->where('ffs_fee_type', $feeType)
            ->where('ffs_status', 'E');

        $roadmap = $request->input('roadmap_sess');
        if ($roadmap !== null && $roadmap !== '' && is_numeric($roadmap)) {
            $query->where('ffs_roadmap_session', (int) $roadmap);
        } else {
            $query->whereNull('ffs_roadmap_session');
        }

        $semester = $request->input('semester');
        if ($semester !== null && $semester !== '' && is_numeric($semester)) {
            $query->where('ffs_semester_no', (int) $semester);
        } else {
            $query->whereNull('ffs_semester_no');
        }

        $enrollment = strtolower(trim((string) $request->input('studentEnrollment')));
        if ($enrollment !== '') {
            $roadmapSess = DB::table('students_apply_info')
                ->whereRaw('LOWER(SAI_ENROLLMENT_NO) = ?', [$enrollment])
                ->value('SAI_SDPO_SESS_ID');
            if (!empty($roadmapSess) && is_numeric($roadmapSess)) {
                $roadmapSess = (int) $roadmapSess;
                $query->where(function ($q) use ($roadmapSess) {
                    $q->whereNull('ffs_intake_from_sess')
                        ->orWhere('ffs_intake_from_sess', '<=', $roadmapSess);
                });
                $query->where(function ($q) use ($roadmapSess) {
                    $q->whereNull('ffs_intake_to_sess')
                        ->orWhere('ffs_intake_to_sess', '>=', $roadmapSess);
                });
            }
        }

        return $query->first();
    }

    /**
     * Installment Fee Adjustment: total per fee head across all installments must not exceed structure.
     */
    protected function validateInstallmentFeeAdjustmentAgainstStructure($existingRecord, $request): ?string
    {
        $instNo = (int) ($existingRecord->check_fee_installments ?? 0);
        if ($instNo <= 0 || ($existingRecord->sc_challan_type ?? '') !== 'Full_semester') {
            return null;
        }

        $structure = $this->fetchFullSemesterFeeStructureRow($request);
        if (!$structure) {
            return null;
        }

        // Only tuition is split across installments. Admission / security / medical /
        // activity usually sit on the first installment and must not block a tuition edit.
        $structureTuition = (float) ($structure->ffs_tutionfee_amt ?? 0);
        if ($structureTuition <= 0) {
            return null;
        }

        $newTuition = (float) ($request->input('tuitionFee') ?? 0);
        $siblingTuition = (float) DB::table('student_challan')
            ->where('sc_app_id', $existingRecord->sc_app_id)
            ->where('sc_session_id', $existingRecord->sc_session_id)
            ->where('sc_challan_type', 'Full_semester')
            ->where('invoice_status', 'E')
            ->whereBetween('check_fee_installments', [1, 5])
            ->where('id', '!=', $existingRecord->id)
            ->sum(DB::raw('NVL(sc_installment_tuition_fee_amnt, NVL(sc_tuition_fee_amnt, 0))'));

        $groupTotal = $siblingTuition + $newTuition;
        if ($groupTotal > $structureTuition + 0.009) {
            return 'Tuition fee total across installments ('
                . number_format($groupTotal, 2, '.', ',')
                . ') cannot exceed fee structure amount ('
                . number_format($structureTuition, 2, '.', ',') . ').';
        }

        $singleCaps = [
            'admissionFee' => [(float) ($structure->ffs_admission_amt ?? 0), 'Admission fee', $request->input('admissionFee')],
            'securityFee' => [(float) ($structure->ffs_security_amt ?? 0), 'Security fee', $request->input('securityFee')],
            'medicalFee' => [(float) ($structure->ffs_medical_amt ?? 0), 'Medical fee', $request->input('medicalFee')],
            'activity' => [(float) ($structure->ffs_activity_amt ?? 0), 'Activity charges', $request->input('activity_charges_amount')],
        ];
        foreach ($singleCaps as $capRow) {
            [$cap, $label, $raw] = $capRow;
            if ($cap <= 0 || $raw === null || $raw === '') {
                continue;
            }
            $newAmt = (float) $raw;
            if ($newAmt > $cap + 0.009) {
                return $label . ' (' . number_format($newAmt, 2, '.', ',')
                    . ') cannot exceed fee structure amount ('
                    . number_format($cap, 2, '.', ',') . ').';
            }
        }

        return null;
    }

    /**
     * Manual fee adjustment via FIN_NEW_STD_SINGLE_CHLN_UPDT.
     */
    protected function executeFinNewStdSingleChlnUpdt(
        $challanNo,
        $appId,
        $sessionId,
        $newTuitionFee,
        $newActivityFee,
        $newSecurityFee,
        $newMedicalFee,
        $newAdmissionFee,
        $dueDate,
        $validDate,
        $request = null
    ): bool {
        // Preserve existing CR/repeat values when form fields are empty (avoid zeroing columns)
        $existing = DB::table('Student_challan')->where('id', $challanNo)->first() ?: (object) [];
        $credhrAmt = $request ? $request->input('creditHrFee') : null;
        $enrolledCr = $request ? $request->input('creditHr') : null;
        $installmentTuition = $request ? $request->input('tuitionFee') : null;

        $bindings = [
            'p_challan_no' => $challanNo,
            'p_new_tuition_fee' => $newTuitionFee ?? 0,
            'p_new_activity_fee' => $newActivityFee ?? 0,
            'p_new_security_fee' => $newSecurityFee ?? 0,
            'p_new_medicale_fee' => $newMedicalFee ?? 0,
            'p_new_admission_fee' => $newAdmissionFee ?? 0,
            'p_new_credhr_amt' => ($credhrAmt !== null && $credhrAmt !== '')
                ? $credhrAmt
                : ($existing->ffs_credhr_amt ?? 0),
            'p_new_installment_tuition_fee' => ($installmentTuition !== null && $installmentTuition !== '')
                ? $installmentTuition
                : ($newTuitionFee ?? ($existing->sc_installment_tuition_fee_amnt ?? 0)),
            'p_new_repeat_amount_5' => $existing->sc_repeat_amount_5 ?? 0,
            'p_new_enrolled_cr_hr' => ($enrolledCr !== null && $enrolledCr !== '')
                ? $enrolledCr
                : ($existing->sc_enrolled_cr_hr ?? 0),
            'p_session' => $sessionId,
            'p_app_id' => $appId,
            'p_due_date' => $dueDate,
            'p_valid_date' => $validDate,
            'p_updated_by_username' => auth()->user()->username,
            'p_remarks' => $request ? trim((string) ($request->input('editRemarks') ?? '')) : '',
        ];

        return (bool) DB::executeProcedure('FIN_NEW_STD_SINGLE_CHLN_UPDT', $bindings);
    }

    /**
     * When Surplus Adjustment checkbox is YES:
     *  - Edit + surplus_apply_amount + surplus_ffa_id → apply that amount to the challan (partial)
     *  - Otherwise → ADJUST_FEE_BY_ENROLLMENT (existing full adjust)
     */
    private function applySurplusAdjustmentIfEnabled($surplusAdjustment, $studentEnrollment, $sessionId, $request = null)
    {
        if (empty($surplusAdjustment) || $surplusAdjustment !== 'YES') {
            return;
        }

        $applyAmount = $request ? floatval($request->input('surplus_apply_amount')) : 0;
        $ffaId = $request ? $request->input('surplus_ffa_id') : null;
        $editId = $request ? $request->input('edit_id') : null;

        // Edit challan: apply user-selected amount only (cannot exceed FFA surplus)
        if ($request && $request->input('verifyEdit') == '1' && $applyAmount > 0 && !empty($ffaId) && !empty($editId)) {
            $this->applyPartialSurplusToChallan($ffaId, $applyAmount, $editId, $sessionId);
            return;
        }

        if (is_numeric($studentEnrollment)) {
            $student = DB::table('admission_apply_info')
                ->select('cms_sai_enrollment_no as enrollment_no')
                ->where('aai_id', $studentEnrollment)
                ->first();
            if (!$student || empty($student->enrollment_no)) {
                return;
            }
            $enrollment_no = $student->enrollment_no;
        } else {
            $enrollment_no = $studentEnrollment;
        }

        $surplus_procedurenotification = 'ADJUST_FEE_BY_ENROLLMENT';
        $surplus_bindings_notification = [
            'p_enrollment_no' => $enrollment_no,
            'p_sess_id' => $sessionId,
            'p_user_name' => auth()->user()->username,
        ];

        DB::executeProcedure($surplus_procedurenotification, $surplus_bindings_notification);
    }

    /**
     * Apply part of FIN_FEE_ADJUSTMENT surplus to one unpaid challan (same idea as Fee Adjustment apply).
     * Example: available 5000, apply 3000 → FFA remaining 2000, challan invoice reduced by 3000.
     */
    private function applyPartialSurplusToChallan($ffaId, $applyAmount, $editId, $sessionId)
    {
        $ffa = DB::table('FIN_FEE_ADJUSTMENT')->where('FFA_ID', $ffaId)->first();
        if (!$ffa) {
            throw new \Exception('Surplus adjustment record not found.');
        }

        $available = floatval($ffa->ffa_surplus_amount ?? $ffa->FFA_SURPLUS_AMOUNT ?? 0);
        if ($applyAmount <= 0) {
            throw new \Exception('Surplus apply amount must be greater than 0.');
        }
        if ($applyAmount > $available) {
            throw new \Exception('Amount cannot be greater than available surplus (' . $available . ').');
        }

        $challan = $this->resolveStudentChallanByEditId($editId);
        if (!$challan) {
            throw new \Exception('Challan not found for surplus adjustment.');
        }
        $editId = $challan->id;

        // Ownership: FFA must belong to same app id and/or enrollment as the challan
        $ffaAppId = $ffa->FFA_APP_ID ?? $ffa->ffa_app_id ?? null;
        $ffaEnr = $ffa->FFA_ENROLLMENT_NO ?? $ffa->ffa_enrollment_no ?? null;
        $challanAppId = $challan->sc_app_id ?? null;
        $challanEnr = $challan->sc_enrollment_no ?? null;
        $ownsByApp = !empty($ffaAppId) && !empty($challanAppId) && (string) $ffaAppId === (string) $challanAppId;
        $ownsByEnr = !empty($ffaEnr) && !empty($challanEnr)
            && strcasecmp(trim((string) $ffaEnr), trim((string) $challanEnr)) === 0;
        if (!$ownsByApp && !$ownsByEnr) {
            throw new \Exception('Surplus adjustment does not belong to this student/challan.');
        }

        $invoiceAmount = floatval($challan->invoiceamount ?? 0);
        $alreadyAdjusted = floatval($challan->sc_surplus_amount ?? 0);
        $username = auth()->user()->username;
        $updatedTime = now();

        if ($invoiceAmount <= 0) {
            throw new \Exception('Challan amount is 0 — cannot apply surplus.');
        }
        if ($applyAmount > $invoiceAmount) {
            throw new \Exception('Amount cannot exceed challan amount (' . $invoiceAmount . ').');
        }

        $newFfaSurplus = $available - $applyAmount;
        $ffaStatus = ($newFfaSurplus == 0) ? 'D' : 'E';
        $ffAmntStatus = ($newFfaSurplus == 0) ? 'D' : 'E';
        $amountStatus = ($newFfaSurplus == 0) ? 'Paid' : 'Pending';

        $newInvoice = $invoiceAmount - $applyAmount;
        $newChallanSurplus = $alreadyAdjusted + $applyAmount;
        $prevAdjust = floatval($ffa->FFA_ADJUST_AMOUNT ?? $ffa->ffa_adjust_amount ?? 0);

        $startedLocalTxn = false;
        try {
            if (DB::transactionLevel() === 0) {
                DB::beginTransaction();
                $startedLocalTxn = true;
            }

            DB::table('FIN_FEE_ADJUSTMENT')
                ->where('FFA_ID', $ffaId)
                ->update([
                    'FFA_SURPLUS_AMOUNT' => $newFfaSurplus,
                    'FFA_SC_CHALLAN_NO' => $editId,
                    'ffa_amount_status' => $amountStatus,
                    'FFA_STATUS' => $ffaStatus,
                    'FF_AMNT_STATUS' => $ffAmntStatus,
                    'FFA_ADJUST_AMOUNT' => $prevAdjust + $applyAmount,
                    'FFA_ENTRY_USERID' => auth()->user()->id,
                ]);

            DB::table('student_challan')
                ->where('id', $editId)
                ->update([
                    'sc_surplus_amount' => $newChallanSurplus,
                    'invoiceamount' => $newInvoice,
                    'SC_UPDATED_BY_USERNAME' => $username,
                    'UPDATED_AT' => $updatedTime,
                ]);

            if ($startedLocalTxn) {
                DB::commit();
            }
        } catch (\Throwable $e) {
            if ($startedLocalTxn) {
                DB::rollBack();
            }
            throw $e;
        }
    }

    /**
     * Bulk Surplus Adjust (table multi-select): apply amounts from one FFA row to multiple UNPAID challans.
     * Reuses applyPartialSurplusToChallan — edit/single Surplus path unchanged.
     */
    public function applySurplusToChallansBulk(Request $request)
    {
        $ffaId = $request->input('surplus_ffa_id');
        $items = $request->input('items', []);
        $feeAdjustment = $request->input('fee_adjustment', false);
        $feeAdjustmentRemarks = $request->input('fee_adjustment_remarks', '');

        if (empty($ffaId)) {
            return $this->challanClientError('Surplus adjustment record is required.');
        }
        if (!is_array($items) || count($items) === 0) {
            return $this->challanClientError('Select at least one challan with an apply amount.');
        }
        
        // Validate remarks when fee adjustment is enabled
        if ($feeAdjustment && empty(trim($feeAdjustmentRemarks))) {
            return $this->challanClientError('Remarks are required when fee adjustment is enabled.');
        }

        $normalized = [];
        $totalApply = 0;
        foreach ($items as $item) {
            $editId = $item['edit_id'] ?? $item['id'] ?? null;
            $amount = floatval($item['amount'] ?? 0);
            
            // If fee adjustment is enabled, include items even with 0 surplus
            // (they may have fee adjustments but no surplus applied)
            $shouldInclude = !empty($editId) && (
                $amount > 0 || 
                ($feeAdjustment && isset($item['tuition_fee']))
            );
            
            if (!$shouldInclude) {
                continue;
            }
            
            $row = [
                'edit_id' => $editId,
                'amount' => $amount,
            ];
            
                // Add fee adjustment data if enabled
            if ($feeAdjustment) {
                $row['tuition_fee'] = floatval($item['tuition_fee'] ?? 0);
                $row['admission_fee'] = floatval($item['admission_fee'] ?? 0);
                $row['security_fee'] = floatval($item['security_fee'] ?? 0);
                $row['medical_fee'] = floatval($item['medical_fee'] ?? 0);
                $row['activity_charges'] = floatval($item['activity_charges'] ?? 0);
                $row['adjusted_invoice_amount'] = floatval($item['adjusted_invoice_amount'] ?? 0);

                if ($row['tuition_fee'] < 0
                    || $row['admission_fee'] < 0
                    || $row['security_fee'] < 0
                    || $row['medical_fee'] < 0
                    || $row['activity_charges'] < 0
                    || $row['adjusted_invoice_amount'] < 0
                ) {
                    return $this->challanClientError(
                        'Fee amounts and invoice cannot be negative for challan ' . $editId . '.'
                    );
                }
            }
            
            $normalized[] = $row;
            $totalApply += $amount;
        }

        if (count($normalized) === 0) {
            $errorMsg = $feeAdjustment 
                ? 'No valid challans found for fee adjustment or surplus application.'
                : 'Enter amount greater than 0 for at least one challan.';
            return $this->challanClientError($errorMsg);
        }

        $ffa = DB::table('FIN_FEE_ADJUSTMENT')->where('FFA_ID', $ffaId)->first();
        if (!$ffa) {
            return $this->challanClientError('Surplus adjustment record not found.');
        }
        $available = floatval($ffa->ffa_surplus_amount ?? $ffa->FFA_SURPLUS_AMOUNT ?? 0);
        if ($totalApply > $available) {
            return $this->challanClientError(
                'Total apply amount (' . $totalApply . ') cannot exceed available surplus (' . $available . ').'
            );
        }

        $hostelTypes = ['Hostel_Full_semster', 'Hostel_day_wise', 'Hostel_other_utilities'];

        
        try {
            DB::beginTransaction();

            foreach ($normalized as $row) {
                $challan = $this->resolveStudentChallanByEditId($row['edit_id']);
                if (!$challan) {
                    throw new \Exception('Challan not found: ' . $row['edit_id']);
                }
                $status = strtoupper(trim((string) ($challan->sc_challan_status ?? '')));
                if ($status !== 'UNPAID') {
                    throw new \Exception('Challan ' . $challan->id . ' is not UNPAID — surplus cannot be applied.');
                }
                $feeType = $challan->sc_challan_type ?? '';
                $hostelFeeType = $challan->sc_hostel_fee_type ?? '';
                if (in_array($feeType, $hostelTypes, true) || in_array($hostelFeeType, $hostelTypes, true)) {
                    throw new \Exception('Challan ' . $challan->id . ' is hostel type — surplus not allowed.');
                }
                if ($feeType === 'Processing_Fee') {
                    throw new \Exception('Challan ' . $challan->id . ' is Processing Fee — surplus not allowed.');
                }
                
                // Check if scholarship exists when fee adjustment is enabled
                if ($feeAdjustment) {
                    $scholarship = floatval($challan->sc_scholarship_amount ?? 0);
                    if ($scholarship > 0) {
                        throw new \Exception('Challan ' . $challan->id . ' has scholarship applied — fee adjustment not allowed.');
                    }
                }

                $sessionId = $challan->sc_session_id ?? null;
                
                // Apply fee adjustment FIRST if enabled
                if ($feeAdjustment) {
                    $this->applyFeeAdjustmentToChallan($challan->id, $row, $feeAdjustmentRemarks);
                }
                
                // THEN apply surplus (only if amount > 0)
                if ($row['amount'] > 0) {
                    $this->applyPartialSurplusToChallan($ffaId, $row['amount'], $challan->id, $sessionId);
                }
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->challanClientError($e->getMessage() ?: 'Unable to apply surplus adjustment.');
        }

        $message = $feeAdjustment 
            ? 'Surplus and fee adjustments applied on ' . count($normalized) . ' challan(s).'
            : 'Surplus applied on ' . count($normalized) . ' challan(s).';
            
        return $this->challanSuccess(
            $message,
            ['applied_count' => count($normalized)]
        );
    }

    /**
     * Apply fee adjustment to a challan (update fee amounts and invoice).
     * This should be called BEFORE applying surplus.
     */
    private function applyFeeAdjustmentToChallan($challanId, $feeData, $remarks)
    {
        $challan = $this->resolveStudentChallanByEditId($challanId);
        if (!$challan) {
            throw new \Exception('Challan not found for fee adjustment.');
        }

        $tuitionFee = floatval($feeData['tuition_fee'] ?? 0);
        $admissionFee = floatval($feeData['admission_fee'] ?? 0);
        $securityFee = floatval($feeData['security_fee'] ?? 0);
        $medicalFee = floatval($feeData['medical_fee'] ?? 0);
        $activityCharges = floatval($feeData['activity_charges'] ?? 0);
        $adjustedInvoice = floatval($feeData['adjusted_invoice_amount'] ?? 0);

        // Prepare update data
        $updateData = [
            'sc_tuition_fee_amnt' => $tuitionFee,
            'sc_installment_tuition_fee_amnt' => $tuitionFee, // Also update installment column (used by print)
            'ffs_admission_amt' => $admissionFee,
            'ffs_security_amt' => $securityFee,
            'ffs_medical_amt' => $medicalFee,
            'sc_activity_amt' => $activityCharges,
            'SC_UPDATED_BY_USERNAME' => auth()->user()->username ?? 'system',
            'UPDATED_AT' => now(),
        ];

        // Set adjusted invoice amount (CRITICAL: this must be set BEFORE surplus is applied)
        if ($adjustedInvoice > 0) {
            $updateData['invoiceamount'] = $adjustedInvoice;
        }

        // Update challan
        DB::table('student_challan')
            ->where('id', $challan->id)
            ->update($updateData);
        
        // Store remarks in edit audit trail (if remarks provided)
        if (!empty($remarks)) {
            try {
                $remarksToSet = trim($remarks);
                if (stripos($remarksToSet, '-manual') === false) {
                    $remarksToSet .= ' -manual';
                }
                
                DB::table('FIN_SINGLE_CHALLAN_EDIT_AUDIT')->insert([
                    'SC_ID' => $challan->id,
                    'CHALLAN_NO' => $challan->challan_no ?? null,
                    'PREVIOUS_VALUES' => json_encode(['fee_adjustment' => 'before']),
                    'UPDATED_VALUES' => json_encode($updateData),
                    'USER_REMARKS' => $remarksToSet,
                    'EDITED_BY' => auth()->user()->username ?? 'system',
                    'EDITED_AT' => now(),
                ]);
            } catch (\Throwable $e) {
                \Log::warning('Fee adjustment remarks audit not saved: ' . $e->getMessage());
            }
        }
    }

    // -------------------------------------------------------------------------
    // Generate / update challan
    // -------------------------------------------------------------------------

    /**
     * Main generate entry. Routes Credit_hours / Refund / Processing_Fee to dedicated methods;
     * otherwise runs full-semester / hostel generation flow.
     */
    public function submitSingleChallanGenerate(Request $request)
    {
        // Each request starts clean — avoid flushing a stale edit audit from a prior failed attempt
        $this->pendingEditAudit = null;

        // New Full Semester generate: block Fee Adj only (Installments + Surplus still allowed)
        $this->enforceNewFullSemesterRestrictions($request);
        // Processing Fee: surplus not allowed
        $this->enforceProcessingFeeSurplusOff($request);
        // Full Semester edit + scholarship: Fee Adjustment off
        $this->enforceScholarshipBlocksFullSemesterFeeAdjustment($request);

        if ($request->verifyEdit == "1") {
            $editTarget = $this->resolveStudentChallanByEditId($request->input('edit_id'));
            if (!$editTarget) {
                return $this->challanClientError('Challan not found for edit.', 404);
            }
            $editAuditError = $this->beginChallanEditAudit($request, $editTarget);
            if ($editAuditError) {
                return $editAuditError;
            }
            if ($this->isAllowEdit($request) == false) {
                $this->pendingEditAudit = null;
                return $this->challanClientError('Challans with scholarship can not be edited.');
            }
        }

        // Normalize edit_id once (CMS id or printed challan_no) for all fee-type paths
        if (!empty($request->input('edit_id'))) {
            $resolvedEdit = $this->resolveStudentChallanByEditId($request->input('edit_id'));
            if ($resolvedEdit) {
                $request->merge(['edit_id' => $resolvedEdit->id]);
            }
        }

        // Due / Validate date required for ALL fee types
        $dateValidator = Validator::make($request->all(), [
            'dueDate' => 'required|date',
            'validateDate' => 'required|date|after_or_equal:dueDate',
        ], [
            'dueDate.required' => 'Due date is required.',
            'dueDate.date' => 'Due date is invalid.',
            'validateDate.required' => 'Validate date is required.',
            'validateDate.date' => 'Validate date is invalid.',
            'validateDate.after_or_equal' => 'Validate date must be on or after due date.',
        ]);
        if ($dateValidator->fails()) {
            return response()->json([
                'success' => false,
                'status' => 'error',
                'errors' => $dateValidator->errors()->toArray(),
            ], 422);
        }

        if ($request->input('fee_type') == 'Credit_hours') {
            return $this->generate_creditHr_challan($request);
        } elseif ($request->input('fee_type') == 'Credit_hours_Summer_Course') {
            return $this->generate_summer_creditHr_challan($request);
        } elseif ($request->input('fee_type') == 'Fee_Refund' || $request->input('fee_type') == 'Hostel_Fee_Refund') {
            return $this->fee_refund($request);
        } elseif ($request->input('fee_type') == 'Processing_Fee') {
            $enr = strtoupper(trim((string) $request->input('studentEnrollment')));
            if ($enr !== '' && (substr($enr, 0, 1) === 'F' || substr($enr, 0, 1) === 'S')) {
                return $this->challanClientError(
                    'Processing Fee is not allowed for enrollment numbers starting with F or S. Use Applicant ID instead.',
                    422
                );
            }
            return $this->processingFee_challan($request);
        } elseif (in_array($request->input('fee_type'), ['Hostel_Full_semster', 'Hostel_day_wise', 'Hostel_other_utilities'], true)) {
            return $this->generate_hostel_challan($request);
        }
        // Validate the request (hostel types already returned above — Full_semester tuition required)
        $hostelFeeTypes = ['Hostel', 'Hostel_day_wise', 'Hostel_other_utilities', 'Hostel_Full_semster'];
        $noOfInstallmentsReq = (int) $request->input('noOfInstallments');
        $validator = Validator::make($request->all(), [
            'studentEnrollment' => 'required',
            'sessionId' => 'required',
            'student_type' => 'required',
            'fee_type' => 'required',
            'student_status' => 'required',
            'tuitionFee' => in_array($request->input('fee_type'), $hostelFeeTypes, true)
                ? 'nullable'
                : 'required|numeric',
            'noOfInstallments' => 'nullable|integer|min:1|max:5',
            'installmentDueDate' => $noOfInstallmentsReq > 0 ? 'required|array|min:' . $noOfInstallmentsReq : 'nullable|array',
            'installmentDueDate.*' => $noOfInstallmentsReq > 0 ? 'required|date' : 'nullable|date',
            'installmentValidateDate' => $noOfInstallmentsReq > 0 ? 'required|array|min:' . $noOfInstallmentsReq : 'nullable|array',
            'installmentValidateDate.*' => $noOfInstallmentsReq > 0 ? 'required|date' : 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'status' => 'error', 'errors' => $validator->errors()->toArray()], 422);
        }

        // Retrieve inputs from the request
        $studentEnrollment = strtoupper($request->input('studentEnrollment'));
        $fee_type = $request->input('fee_type');
        $student_status = $request->input('student_status');
        $studentType = $request->input('student_type');
        $hostel_student_type = $request->input('hostel_student_type') ?: $studentType;
        $sessionId = $request->input('sessionId');
        $st_school_id = $request->input('st_school_id');
        $st_dept_id = $request->input('st_dept_id');
        $st_prog_id = $request->input('st_prog_id');
        $st_aai_id = $request->input('st_aai_id');
        $tuitionFee = $request->input('tuitionFee') ?? 0;
        $admissionFee = $request->input('admissionFee') ?? 0;
        $securityFee = $request->input('securityFee') ?? 0;
        $medicalFee = $request->input('medicalFee') ?? 0;
        $activityChargesAmount = $request->input('activity_charges_amount') ?? 0;
        $noOfInstallments = $request->input('noOfInstallments');
        $surplusAdjustment = $request->input('surplusAdjustment');
        $verifyEdit = $request->input('verifyEdit');
        $editId = $request->input('edit_id');
        $user_id = auth()->user()->id;
        $user_name = auth()->user()->username;

        $daysNo = $request->input('daysNo') ?? 0;
        $refrigeratorCheckBox = $request->input('refrigeratorCheckBox');
        $conditionerCheckBox = $request->input('conditionerCheckBox');
        $electricHeaterCheckBox = $request->input('electricHeaterCheckBox');
        $refrigeratorFee = $request->filled('refrigeratorFee') ? intval($request->input('refrigeratorFee')) : null;
        $electricFee     = $request->filled('electricFee')     ? intval($request->input('electricFee'))     : null;
        $conditionerFee  = $request->filled('conditionerFee')  ? intval($request->input('conditionerFee'))  : null;
        $hostelSecurityFee = $request->input('hostel_security_fee');
        $hostelRemarks = $request->input('hostelRemarks');




        $SinlgeChallanDueDate = $request->input('dueDate');
        $SinlgeChallanValidateDate = $request->input('validateDate');
        // $gymFee = $request->input('gymFee'); // YES or NO
        $studentInstallmentStatus = $request->input('studentInstallment'); // YES or NO
        $manualFeeAdjustment = $request->input('manualFeeAdjustment'); // YES or NO
        // Convert installment due/validate dates (same format as before)
        list($installmentDueDates, $installmentValidateDates) = $this->getInstallmentDatesFromRequest($request);

        // Enrollment (alpha) vs Applicant ID (numeric) — used by generate procedure branch below
        $isCharacter = !is_numeric($studentEnrollment);

        // Resolve student by enrollment no or applicant id
        $studentRecord = $this->findStudentApplyRecord($studentEnrollment);
        if (!$studentRecord) {
            return response()->json(['status' => 'error', 'error' => 'Record not found'], 404);
        }

        if (!empty($installmentDueDates) && !empty($installmentValidateDates)) {
            // Check if FIN_BLK_CHALLAN_GEN may agr entry ni pari to insertion kro
            $response = $this->insert_In_fin_blk_challan_gen(
                $sessionId,
                $st_school_id,
                $st_dept_id,
                $st_prog_id,
                $studentType,
                $fee_type,
                $SinlgeChallanDueDate,
                $SinlgeChallanValidateDate,
                $installmentDueDates,
                $installmentValidateDates,
                $request,
                $user_id,
                $student_status
            );
            // Check if FIN_BLK_CHALLAN_GEN may agr entry  pari to  4th and 5th installments ko update kroi
            $responseUpdateInstallmentDates = $this->updateFeeInstallmentsDates(
                $sessionId,
                $st_school_id,
                $st_dept_id,
                $st_prog_id,
                $studentType,
                $fee_type,
                $installmentDueDates,
                $installmentValidateDates
            );
        }




        //  dd($request->all());
        DB::beginTransaction();
        // dd($request->all());
        try {

            ////////////////////////////// Aik Challan ki amount/ fee adjustment k lie procedure //////////////////////////////////

            // Full_semester / Short/Summer_Course (+ Credit_hours if routed here): manual fee adjustment
            if ($verifyEdit == "1" && !empty($editId) && !empty($manualFeeAdjustment) && $manualFeeAdjustment === 'YES') {

                $existingRecord = DB::table('Student_challan')->where('id', $editId)->first();
                if (!$existingRecord) {
                    return $this->challanFailRollback('Challan record not found.', 404);
                }

                $aai_id = $this->resolveAppIdForFeeAdjustment($studentEnrollment);
                $tuitionFee = $this->resolveTuitionForFeeAdjustment($request, $fee_type);

                $capError = $this->validateInstallmentFeeAdjustmentAgainstStructure($existingRecord, $request);
                if ($capError) {
                    return $this->challanFailRollback($capError, 422);
                }

                $result = $this->executeFinNewStdSingleChlnUpdt(
                    $editId,
                    $aai_id,
                    $sessionId,
                    $tuitionFee,
                    $activityChargesAmount,
                    $securityFee,
                    $medicalFee,
                    $admissionFee,
                    $SinlgeChallanDueDate,
                    $SinlgeChallanValidateDate,
                    $request
                );

                if (!$result) {
                    return $this->challanFailRollback('Failed to update Fee Adjustment Challan.');
                }
                $this->sendNewChallanMessage($studentEnrollment);
            }

            if ($verifyEdit == "1" && !empty($editId)) {
                $existingRecord = DB::table('Student_challan')->where('id', $editId)->first();
                //////////////// agr challan  ka sttaus CANCEL FOR REISSUANCE ho to challan ko active krny k lie.////////////
                if ($existingRecord && $existingRecord->sc_challan_status === 'CANCEL FOR REISSUANCE') {


                    // Format dates for Oracle database
                    $formattedDueDate = Carbon::createFromFormat('Y-m-d', $SinlgeChallanDueDate)
                        ->format('d-M-y h.i.s A');

                    $formattedValidateDate = Carbon::createFromFormat('Y-m-d', $SinlgeChallanValidateDate)
                        ->format('d-M-y h.i.s A');

                    $updated = DB::table('student_challan')
                        ->where('id', $editId)
                        ->update([
                            'sc_challan_status' => 'UNPAID',
                            'INVOICE_STATUS' => 'E',
                            'sc_due_date' => DB::raw("TO_DATE('$formattedDueDate', 'DD-MON-YY HH:MI:SS AM')"),
                            'sc_valid_date' => DB::raw("TO_DATE('$formattedValidateDate', 'DD-MON-YY HH:MI:SS AM')"),
                            'SC_UPDATED_BY_USERNAME' => auth()->user()->username,
                            'UPDATED_AT' => now()
                        ]);

                    if (!$updated) {
                        return $this->challanFailRollback('Failed to update Challan. Record might not exist or update failed.');
                    }
                    DB::commit();
                    return $this->challanSuccess('Challan activated successfully');
                }
                /////////////////////////// challan ki installments banany k lie yha sy code start ho rha hy////////////////////////////////////
                if ($existingRecord && $noOfInstallments > 0) {
                    $challanNo = $existingRecord->id;
                    $procedureName = 'FIN_GENERATE_INSTALLMENTS';
                    $bindings = [
                        'p_installmentSelect' => $noOfInstallments,
                        'p_buyercodechallanno' => $challanNo,
                        'p_installmentOption' => $studentInstallmentStatus,
                        'p_1st_installment_due_date' => !empty($installmentDueDates[0]) ? $installmentDueDates[0] : now()->timezone('Asia/Karachi')->toDateTimeString(),
                        'p_2nd_installment_due_date' => !empty($installmentDueDates[1]) ? $installmentDueDates[1] : now()->timezone('Asia/Karachi')->toDateTimeString(),
                        'p_3rd_installment_due_date' => !empty($installmentDueDates[2]) ? $installmentDueDates[2] : now()->timezone('Asia/Karachi')->toDateTimeString(),
                        'p_4th_installment_due_date' => !empty($installmentDueDates[3]) ? $installmentDueDates[3] : now()->timezone('Asia/Karachi')->toDateTimeString(),
                        'p_5th_installment_due_date' => !empty($installmentDueDates[4]) ? $installmentDueDates[4] : now()->timezone('Asia/Karachi')->toDateTimeString(),

                        'p_1st_installment_valid_date' => !empty($installmentValidateDates[0]) ? $installmentValidateDates[0] : now()->timezone('Asia/Karachi')->toDateTimeString(),
                        'p_2nd_installment_valid_date' => !empty($installmentValidateDates[1]) ? $installmentValidateDates[1] : now()->timezone('Asia/Karachi')->toDateTimeString(),
                        'p_3rd_installment_valid_date' => !empty($installmentValidateDates[2]) ? $installmentValidateDates[2] : now()->timezone('Asia/Karachi')->toDateTimeString(),
                        'p_4th_installment_valid_date' => !empty($installmentValidateDates[3]) ? $installmentValidateDates[3] : now()->timezone('Asia/Karachi')->toDateTimeString(),
                        'p_5th_installment_valid_date' => !empty($installmentValidateDates[4]) ? $installmentValidateDates[4] : now()->timezone('Asia/Karachi')->toDateTimeString(),
                        'p_project_type' => 'CMS',
                        'p_entry_user' => $user_name
                    ];
                    $result = DB::executeProcedure($procedureName, $bindings);
                    if (!$result) {
                        return $this->challanFailRollback('There was an issue executing the procedure.');
                    }
                    DB::commit();
                    return $this->challanSuccess('Installment created successfully');
                }
                // dd('agr challan  ko update krna use ki due and valid date update krny k lie', $request->all());
                //////////////// dd('agr challan  ko update krna use ki due and valid date update krny k lie').////////////
                if ($existingRecord && $noOfInstallments == 0) {
                    $existingRecord = DB::table('Student_challan')->where('id', $editId)->first();
                    if (!$existingRecord) {
                        return $this->challanFailRollback('Student challan records not found', 404);
                    }
                    // Format dates for Oracle database
                    $formattedDueDate = Carbon::createFromFormat('Y-m-d', $SinlgeChallanDueDate)
                        ->format('d-M-y h.i.s A');

                    $formattedValidateDate = Carbon::createFromFormat('Y-m-d', $SinlgeChallanValidateDate)
                        ->format('d-M-y h.i.s A');



                    //  sc_tuition_fee_amnt  ffs_admission_amt  check_fee_installments sc_student_status
                    // ffs_security_amt   ffs_medical_amt    ffs_hostel_amt  sc_surplus_amount  sc_activity_amt
                    //
                    //
                    //
                    // Record ko update karein
                    $dateUpdatePayload = [
                        'sc_due_date' => DB::raw("TO_DATE('$formattedDueDate', 'DD-MON-YY HH:MI:SS AM')"),
                        'sc_valid_date' => DB::raw("TO_DATE('$formattedValidateDate', 'DD-MON-YY HH:MI:SS AM')"),
                        'SC_INSTALLMENT_ALLOWED' => $studentInstallmentStatus,
                        'SC_UPDATED_BY_USERNAME' => auth()->user()->username,
                        'UPDATED_AT' => now()
                    ];
                    // Credit_hours / Short/Summer_Course: persist editable SC_ENROLLED_CR_HR
                    if (in_array($fee_type, ['Credit_hours', 'Short/Summer_Course'], true)
                        && $request->filled('creditHr')) {
                        $dateUpdatePayload['SC_ENROLLED_CR_HR'] = $request->input('creditHr');
                    }
                    DB::table('Student_challan')
                        ->where('id', $editId)
                        ->update($dateUpdatePayload);

                    // DB::commit();
                    // return response()->json([
                    //     'status' => 'success',
                    //     'message' => 'Student challan  updated successfully'
                    // ]);

                }
            } else {
                if ($isCharacter) {
                    /////////Existing students ky challan k lie procedure----Define procedure for existing students/////////////////////
                    $procedureName = 'FIN_EXISTING_STD_BLK_CHLN_GEN';
                    $bindings = [
                        'v_sai_student_status' => $student_status,
                        'schol_id' => $st_school_id,
                        'dept_id' => $st_dept_id,
                        'prog_id' => $st_prog_id,
                        'session' => $sessionId,
                        'v_sai_enrollment_no' => $studentEnrollment,
                        'v_ffs_fee_type' => $fee_type,
                        'v_ffs_student_type' => $studentType,
                        'ddue_date' => $SinlgeChallanDueDate,
                        'vvalid_date' => $SinlgeChallanValidateDate,
                        'p_visible_date' => date('Y-m-d'), // Current date
                        'p_installmentOption' => $studentInstallmentStatus,
                        'p_roadmap' => $request->roadmap_sess,
                        'p_semester' => $request->semester,
                        'p_entry_username' => auth()->user()->username
                    ];
                    // dd('new challan creation exisiting student case', $bindings, 1, $request->roadmap_sess, $request->semester);
                } else {
                    /////////New students ky challan  k lie procedure----Define procedure for new students/////////////////////
                    $procedureName = 'FIN_NEW_STD_BLK_CHLN_GEN';
                    $bindings = [
                        'p_schol_id' => intval($st_school_id),
                        'p_dept_id' => intval($st_dept_id),
                        'p_prog_id' => intval($st_prog_id),
                        'p_session' => intval($sessionId),
                        'p_due_date' => $SinlgeChallanDueDate,
                        'p_valid_date' => $SinlgeChallanValidateDate,
                        'p_ffs_student_type' => $studentType,
                        'p_ffs_fee_type' => $fee_type,
                        'p_AAI_ID' => intval($studentEnrollment),
                        'p_visible_date' => date('Y-m-d'), // Current date
                        'p_installmentOption' => $studentInstallmentStatus,
                        'p_entry_username' => auth()->user()->username,
                        'p_roadmap' => $request->roadmap_sess,
                        'p_semester' => $request->semester
                    ];
                    //    dd('new challan creation new student case', $bindings, 2, $request->roadmap_sess, $request->semester );
                }
                //Execute the procedure
                $result = DB::executeProcedure($procedureName, $bindings);
                // dd($result);

                if (!$result) {
                    return $this->challanFailRollback('There was an issue executing the procedure.');
                }
            }


            // ==============================
            // Agar challan "PAID" hai to sirf software team (group_id == 644) usko edit kar sakti hai
            // Aur "PAID" status ko "UNPAID" me convert kiya ja sakta hai
            // ==============================
            if ($verifyEdit == "1" && !empty($editId) && auth()->user()->group_id == 644) {
                // Check if record exists
                $existingRecord = DB::table('student_challan')->where('id', $editId)->first();
                // Only allow editing if the status is PAID
                if ($existingRecord && $existingRecord->sc_challan_status === 'PAID') {
                    DB::table('student_challan')
                        ->where('id', $editId)
                        ->update([
                            'RESPONSE_NETAMOUNT' => null,
                            'callback_paid_on' => null,
                            'invoice_status' => 'E',
                            'response_status' => null,
                            'sc_challan_status' => 'UNPAID', // Changing from PAID to UNPAID
                            'callback_amount' => null,
                            'CALLBACK_AT' => null,
                            'sc_paytype' => null,
                            'RESPONSE_MESSAGE' => null,
                            'SC_UPDATED_BY_USERNAME' => auth()->user()->username,
                            'UPDATED_AT' => now()
                        ]);
                }
            }


            // Surplus Adjustment — only when checkbox is YES (same procedure/bindings)
            $this->applySurplusAdjustmentIfEnabled($surplusAdjustment, $studentEnrollment, $sessionId, $request);

            // Commit transaction
            DB::commit();
            $allData = DB::table('Student_challan')->where('sc_app_id', $st_aai_id)->get();
            return $this->challanSuccess('Data submitted successfully', ['all_data' => $allData]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'status' => 'error',
                'message' => 'An error occurred',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Credit-hour wise challan generate / edit / installments.
     */
    public function generate_creditHr_challan($request)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'studentEnrollment' => 'required',
            'sessionId' => 'required',
            'student_type' => 'required',
            'student_status' => 'required',
            'dueDate' => 'required|date',
            'validateDate' => 'required|date|after_or_equal:dueDate',
            'studentInstallment' => 'required',
            'creditHr' => 'required|numeric|min:0',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'status' => 'error',
                'errors' => $validator->getMessageBag()->toArray()
            ], 422);
        }

        $verifyEdit = $request->input('verifyEdit');
        $editId = $request->input('edit_id');
        $noOfInstallments = $request->input('noOfInstallments');
        $studentInstallmentStatus = $request->input('studentInstallment');
        list($installmentDueDates, $installmentValidateDates) = $this->getInstallmentDatesFromRequest($request);

        if ((int) $noOfInstallments > 0) {
            $instValidator = Validator::make($request->all(), [
                'installmentDueDate' => 'required|array|min:' . (int) $noOfInstallments,
                'installmentDueDate.*' => 'required|date',
                'installmentValidateDate' => 'required|array|min:' . (int) $noOfInstallments,
                'installmentValidateDate.*' => 'required|date',
            ]);
            if ($instValidator->fails()) {
                return response()->json([
                    'success' => false,
                    'status' => 'error',
                    'errors' => $instValidator->errors()->toArray(),
                ], 422);
            }
        }

        $dueDate = new DateTime($request->dueDate);
        $dueDate = $dueDate->format('Y-m-d');

        $validDate = new DateTime($request->validateDate);
        $validDate = $validDate->format('Y-m-d');

        $visibleDate = new DateTime(now());
        $visibleDate = $visibleDate->format('Y-m-d');

        try {
            DB::beginTransaction();
            if ($verifyEdit != '1' && empty($editId)) {
                $procedurenotification = 'fin_credit_hr_wise_challan';
                $bindings_notification = [
                    'p_enrollment_no' => strtoupper(trim((string) $request->studentEnrollment)),
                    'p_session_id' => $request->sessionId,
                    'p_std_status' => $request->student_status, //(Active or Expected-Graduate)
                    'p_ffs_student_type' => $request->student_type, //(Existing/New Admission FALL etc..)
                    'p_due_date' => $dueDate,
                    'p_valid_date' => $validDate,
                    'p_visible_date' => $visibleDate,
                    'p_Installment_allowed' => $request->studentInstallment ?: 'NO',
                    'P_CR_HR' => $request->creditHr,
                    'P_USER_NAME' => auth()->user()->username
                ];
                // dd($bindings_notification);
                // Execute stored procedure
                $result_notification = DB::executeProcedure($procedurenotification, $bindings_notification);
                if (!$result_notification) {
                    DB::rollBack();
                    return $this->challanClientError('There was an issue generating credit hour challan.');
                }

                DB::commit();
                $allData = DB::table('Student_challan')->where('sc_enrollment_no', $request->studentEnrollment)->get();
                return $this->challanSuccess('Data submitted successfully', ['all_data' => $allData]);
            } else {
                $existingRecord = DB::table('Student_challan')->where('id', $editId)->first();
                if (!$existingRecord) {
                    DB::rollBack();
                    return $this->challanClientError('Challan record not found for edit.', 404);
                }
                if ($existingRecord && $noOfInstallments > 0) {
                    $challanNo = $existingRecord->id;
                    $procedureName = 'FIN_GENERATE_INSTALLMENTS';
                    $bindings = [
                        'p_installmentSelect' => $noOfInstallments,
                        'p_buyercodechallanno' => $challanNo,
                        'p_installmentOption' => $studentInstallmentStatus,
                        'p_1st_installment_due_date' => !empty($installmentDueDates[0]) ? $installmentDueDates[0] : now()->timezone('Asia/Karachi')->toDateTimeString(),
                        'p_2nd_installment_due_date' => !empty($installmentDueDates[1]) ? $installmentDueDates[1] : now()->timezone('Asia/Karachi')->toDateTimeString(),
                        'p_3rd_installment_due_date' => !empty($installmentDueDates[2]) ? $installmentDueDates[2] : now()->timezone('Asia/Karachi')->toDateTimeString(),
                        'p_4th_installment_due_date' => !empty($installmentDueDates[3]) ? $installmentDueDates[3] : now()->timezone('Asia/Karachi')->toDateTimeString(),
                        'p_5th_installment_due_date' => !empty($installmentDueDates[4]) ? $installmentDueDates[4] : now()->timezone('Asia/Karachi')->toDateTimeString(),

                        'p_1st_installment_valid_date' => !empty($installmentValidateDates[0]) ? $installmentValidateDates[0] : now()->timezone('Asia/Karachi')->toDateTimeString(),
                        'p_2nd_installment_valid_date' => !empty($installmentValidateDates[1]) ? $installmentValidateDates[1] : now()->timezone('Asia/Karachi')->toDateTimeString(),
                        'p_3rd_installment_valid_date' => !empty($installmentValidateDates[2]) ? $installmentValidateDates[2] : now()->timezone('Asia/Karachi')->toDateTimeString(),
                        'p_4th_installment_valid_date' => !empty($installmentValidateDates[3]) ? $installmentValidateDates[3] : now()->timezone('Asia/Karachi')->toDateTimeString(),
                        'p_5th_installment_valid_date' => !empty($installmentValidateDates[4]) ? $installmentValidateDates[4] : now()->timezone('Asia/Karachi')->toDateTimeString(),
                        'p_project_type' => 'CMS',
                        'p_entry_user' => auth()->user()->username
                    ];
                    // dd($bindings);
                    $result = DB::executeProcedure($procedureName, $bindings);
                    if (!$result) {
                        DB::rollBack();
                        return $this->challanClientError('There was an issue executing the procedure.');
                    }
                    DB::commit();
                    return $this->challanSuccess('Installment created successfully');
                }
                // Fee adjustment — same FIN_NEW_STD_SINGLE_CHLN_UPDT as Full_semester / Short/Summer_Course
                $surplusAdjustment = $request->input('surplusAdjustment');
                $studentEnrollment = strtoupper($request->input('studentEnrollment'));
                $sessionId = $request->input('sessionId');
                $st_aai_id = $request->input('st_aai_id');
                $manualFeeAdjustment = $request->input('manualFeeAdjustment');

                if (!empty($manualFeeAdjustment) && $manualFeeAdjustment === 'YES') {
                    $aai_id = $this->resolveAppIdForFeeAdjustment($studentEnrollment);
                    $tuitionForAdj = $this->resolveTuitionForFeeAdjustment($request, 'Credit_hours');
                    $resultAdj = $this->executeFinNewStdSingleChlnUpdt(
                        $editId,
                        $aai_id,
                        $sessionId,
                        $tuitionForAdj,
                        $request->input('activity_charges_amount') ?? 0,
                        $request->input('securityFee') ?? 0,
                        $request->input('medicalFee') ?? 0,
                        $request->input('admissionFee') ?? 0,
                        $dueDate,
                        $validDate,
                        $request
                    );
                    if (!$resultAdj) {
                        DB::rollBack();
                        return $this->challanClientError('Failed to update Fee Adjustment Challan.');
                    }
                    $this->sendNewChallanMessage($studentEnrollment);
                }

                // Same surplus path as main generate (was building bindings but never executeProcedure)
                $this->applySurplusAdjustmentIfEnabled($surplusAdjustment, $studentEnrollment, $sessionId, $request);

                $creditHrUpdate = [
                    'SC_DUE_DATE' => $dueDate,
                    'SC_VALID_DATE' => $validDate,
                    'SC_UPDATED_BY_USERNAME' => auth()->user()->username,
                    'UPDATED_AT' => now()
                ];
                if ($request->filled('creditHr')) {
                    $creditHrUpdate['SC_ENROLLED_CR_HR'] = $request->input('creditHr');
                }
                DB::table('Student_challan')
                    ->where('ID', $editId)
                    ->update($creditHrUpdate);
                DB::commit();
                $allData = DB::table('Student_challan')->where('sc_app_id', $st_aai_id)->get();
                return $this->challanSuccess('Data submitted successfully', ['all_data' => $allData]);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'status' => 'error',
                'message' => 'An error occurred',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Credit hour wise Summer Course — same as GenerateCreditHrBulkChallanController::add_summerCredithr_challans
     * Procedure: FIN_PRO_SUMMER_CREDIT_HOUR (bindings must match bulk exactly).
     */
    public function generate_summer_creditHr_challan($request)
    {
        if ($request->input('verifyEdit') == '1' || !empty($request->input('edit_id'))) {
            return $this->challanClientError(
                'Edit is not available for Credit hour Summer Course. Generate a new challan if needed.'
            );
        }

        $validator = Validator::make($request->all(), [
            'studentEnrollment' => 'required',
            'sessionId' => 'required',
            'student_type' => 'required|in:Existing',
            'student_status' => 'required',
            'dueDate' => 'required|date',
            'validateDate' => 'required|date|after_or_equal:dueDate',
            'degree_level' => 'required|in:Undergraduate,Postgraduate,Graduate,Doctorate',
        ], [
            'student_type.in' => 'Credit hour Summer Course is only allowed for Existing students (same as bulk page).',
            'degree_level.required' => 'Degree Level is required for summer credit hour challan.',
            'dueDate.required' => 'Due date is required.',
            'validateDate.required' => 'Validate date is required.',
            'validateDate.after_or_equal' => 'Validate date must be on or after due date.',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->getMessageBag()->toArray(),
            ], 422);
        }

        $enrollment = strtoupper(trim((string) $request->input('studentEnrollment')));
        // Bulk summer always uses SAI_ENROLLMENT_NO — App ID not supported
        if ($enrollment === '' || preg_match('/^\d+$/', $enrollment)) {
            return response()->json([
                'success' => false,
                'recordIssue' => true,
                'message' => 'Enrollment number is required for Credit hour Summer Course (same as bulk). App ID is not allowed.',
            ], 422);
        }

        $studentType = $request->input('student_type');
        if ($studentType !== 'Existing') {
            return response()->json([
                'success' => false,
                'recordIssue' => true,
                'message' => 'Student type should be Existing. Credit hour for summer cannot be generated for new students.',
            ], 422);
        }

        $dueDate = (new DateTime($request->dueDate))->format('Y-m-d');
        $validDate = (new DateTime($request->validateDate))->format('Y-m-d');
        // Bulk uses request visible_date; Single has no field → today (same as credit-hr single)
        $visibleDate = (new DateTime(now()))->format('Y-m-d');

        $degreeLevel = trim((string) $request->input('degree_level'));
        if ($degreeLevel === '' && $request->filled('st_prog_id')) {
            $prog = DB::table('programs')->where('prog_id', $request->input('st_prog_id'))->first();
            $degreeLevel = $prog->prog_degree_level ?? '';
        }
        if ($degreeLevel === '') {
            return response()->json([
                'success' => false,
                'errors' => ['degree_level' => ['Degree Level is required for summer credit hour challan.']],
            ], 422);
        }

        try {
            // Exact keys/order as GenerateCreditHrBulkChallanController (lines 397–409)
            $procedurenotification = 'FIN_PRO_SUMMER_CREDIT_HOUR';
            $bindings_notification = [
                'p_enrollment_no' => $enrollment,
                'p_session_id' => $request->sessionId,
                'p_std_status' => $request->student_status,
                'p_ffs_student_type' => $studentType,
                'p_due_date' => $dueDate,
                'p_valid_date' => $validDate,
                'p_visible_date' => $visibleDate,
                'p_Installment_allowed' => 'NO',
                'p_degree_level' => $degreeLevel,
                'p_user_name' => auth()->user()->username,
            ];
            // dd($bindings_notification);
            $result_notification = DB::executeProcedure($procedurenotification, $bindings_notification);
            if (!$result_notification) {
                return $this->challanClientError('There was an issue generating summer credit hour challan.');
            }

            $surplusAdjustment = $request->input('surplusAdjustment');
            $this->applySurplusAdjustmentIfEnabled(
                $surplusAdjustment,
                $enrollment,
                $request->input('sessionId'),
                $request
            );

            $st_aai_id = $request->input('st_aai_id');
            $allData = !empty($st_aai_id)
                ? DB::table('Student_challan')->where('sc_app_id', $st_aai_id)->get()
                : DB::table('Student_challan')->whereRaw('LOWER(sc_enrollment_no) = ?', [strtolower($enrollment)])->get();

            return $this->challanSuccess('Data submitted successfully', [
                'all_data' => $allData,
                'procedure_result' => true,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'status' => 'error',
                'message' => 'An error occurred',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Fee refund / Hostel fee refund challan.
     */
    public function fee_refund($request)
    {

        if ($request->input('fee_type') == 'Fee_Refund') {
            $validator = Validator::make($request->all(), [
                'fee_type' => 'required',
                'student_type' => 'required',
                'tuitionFee' => 'required',
                'admissionFee' => 'required',
                'medicalFee' => 'required',
                'securityFee' => 'required',
                'activity_charges_amount' => 'required',
                'st_aai_id' => 'required',
                'sessionId' => 'required',
                'dueDate' => 'required|date',
                'validateDate' => 'required|date|after_or_equal:dueDate',
            ], [
                'dueDate.required' => 'Due date is required.',
                'validateDate.required' => 'Validate date is required.',
                'validateDate.after_or_equal' => 'Validate date must be on or after due date.',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'status' => 'error',
                    'errors' => $validator->getMessageBag()->toArray()
                ], 422);
            }
        } elseif ($request->input('fee_type') == 'Hostel_Fee_Refund') {
            $validator = Validator::make($request->all(), [
                'fee_type' => 'required',
                'student_type' => 'required',
                'hostelFee' => 'required',
                'hostel_security_fee' => 'required',
                'st_aai_id' => 'required',
                'sessionId' => 'required',
                'dueDate' => 'required|date',
                'validateDate' => 'required|date|after_or_equal:dueDate',
            ], [
                'dueDate.required' => 'Due date is required.',
                'validateDate.required' => 'Validate date is required.',
                'validateDate.after_or_equal' => 'Validate date must be on or after due date.',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'status' => 'error',
                    'errors' => $validator->getMessageBag()->toArray()
                ], 422);
            }
        }

        $dueDate = new DateTime($request->dueDate);
        $dueDate = $dueDate->format('Y-m-d');

        $validDate = new DateTime($request->validateDate);
        $validDate = $validDate->format('Y-m-d');

        try {
            $procedurenotification = 'FIN_FEE_REFUND_PRO';
            $bindings_notification = [
                'p_challan_type' => ($request->fee_type == 'Hostel_Fee_Refund') ? 'Fee_Refund' : $request->fee_type,
                'p_student_type' => $request->student_type,
                'p_new_tuition_fee' => $request->tuitionFee,
                'p_new_activity_fee' => $request->activity_charges_amount,
                'p_new_security_fee' => ($request->fee_type == 'Hostel_Fee_Refund') ? $request->hostel_security_fee : $request->securityFee,
                'p_new_medical_fee' => $request->medicalFee,
                'p_new_admission_fee' => $request->admissionFee,
                'p_new_hostel_amount' => $request->hostelFee,
                'p_session' => $request->sessionId,
                'p_app_id' => $request->st_aai_id,
                'p_due_date' => $dueDate,
                'p_valid_date' => $validDate,
                'p_entry_user' => auth()->user()->username,
                'p_is_edit' => !empty($request->refundEdit) ? $request->refundEdit : '0',
                'p_challan_no' => !empty($request->editChallan) ? $request->editChallan : '0'

            ];
            // dd($bindings_notification);
            // Execute stored procedure
            $result_notification = DB::executeProcedure($procedurenotification, $bindings_notification);

            $surplusAdjustment = $request->input('surplusAdjustment');
            $sessionId = $request->input('sessionId');
            $studentEnrollment = $request->input('studentEnrollment');
            $st_aai_id = $request->input('st_aai_id');

            // Same surplus path as main generate (was building bindings but never executeProcedure)
            $this->applySurplusAdjustmentIfEnabled($surplusAdjustment, $studentEnrollment, $sessionId, $request);

            $allData = DB::table('Student_challan')->where('sc_app_id', $st_aai_id)->get();
            // dd($allData);
            return $this->challanSuccess('Data submitted successfully', ['all_data' => $allData]);
        } catch (\Exception $e) {
            try {
                DB::rollBack();
            } catch (\Throwable $ignored) {
            }
            return response()->json([
                'success' => false,
                'status' => 'error',
                'message' => 'An error occurred',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    /**
     * Processing fee challan.
     * Same logic as Oracle procedure admission_challan_proc (PHP implementation).
     */
    public function processingFee_challan($request)
    {
        $validator = Validator::make($request->all(), [
            'st_aai_id' => 'required|numeric',
            'dueDate' => 'required|date',
            'validateDate' => 'required|date|after_or_equal:dueDate',
        ], [
            'st_aai_id.required' => 'Applicant ID (App ID) is required. Please search the student first.',
            'dueDate.required' => 'Due date is required.',
            'validateDate.required' => 'Validate date is required.',
            'validateDate.after_or_equal' => 'Validate date must be on or after due date.',
        ]);
        if ($validator->fails()) {
            return response()->json([
                    'success' => false,
                    'status' => 'error',
                    'errors' => $validator->getMessageBag()->toArray()
                ], 422);
        }

        $aaiId = $request->st_aai_id;
        $dueDate = (new DateTime($request->dueDate))->format('Y-m-d');
        $validDate = (new DateTime($request->validateDate))->format('Y-m-d');
        $userId = auth()->user()->id;
        $invoiceAmount = 4000; // same as procedure v_invoice_amount
        $now = now()->timezone('Asia/Karachi');

        try {
            DB::beginTransaction();

            // Active Processing Fee only (cancelled / inactive may regenerate)
            $existing = DB::table('Student_challan')
                ->where('sc_app_id', $aaiId)
                ->where('sc_challan_type', 'Processing_Fee')
                ->where(function ($q) {
                    $q->where('invoice_status', 'E')
                        ->orWhereNull('invoice_status');
                })
                ->where(function ($q) {
                    $q->whereNull('sc_challan_status')
                        ->orWhereRaw("UPPER(sc_challan_status) NOT IN ('CANCELLED')");
                })
                ->first();

            if ($existing) {
                DB::rollBack();
                $allData = DB::table('Student_challan')
                    ->where('sc_challan_type', 'Processing_Fee')
                    ->where('sc_app_id', $aaiId)
                    ->get();
                return response()->json([
                    'message' => 'Processing Fee challan already exists for this applicant. New challan was not generated.',
                    'all_data' => $allData,
                    'success' => false,
                    'recordIssue' => true,
                    'already_exists' => true,
                ]);
            }

            // Applicant personal + apply info (only if not yet enrolled)
            $applicant = DB::table('admission_personal_info')
                ->join('admission_apply_info', 'aai_api_id', '=', 'api_id')
                ->where('aai_id', $aaiId)
                ->whereNull('cms_sai_enrollment_no')
                ->select(
                    'api_name',
                    'api_fathername',
                    'api_guardian_cnic',
                    'api_id',
                    'aai_admission_sch_id'
                )
                ->first();

            if (!$applicant) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Applicant not found, or enrollment already assigned (cms_sai_enrollment_no is set).',
                ], 404);
            }

            $scheduleId = $applicant->aai_admission_sch_id ?? null;
            if (empty($scheduleId)) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Admission schedule not linked for this applicant.',
                ], 422);
            }

            $schedule = DB::table('admission_schedules')
                ->where('admission_sch_id', $scheduleId)
                ->select(
                    'admission_sch_sess_id',
                    'admission_sch_school_id',
                    'admission_sch_dept_id',
                    'admission_sch_prog_id',
                    'admission_sch_form_date'
                )
                ->first();

            if (!$schedule) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Admission schedule not found for this applicant.',
                ], 404);
            }

            $sessionId = $schedule->admission_sch_sess_id ?? null;
            $schoolId = $schedule->admission_sch_school_id ?? null;
            $deptId = $schedule->admission_sch_dept_id ?? null;
            $progId = $schedule->admission_sch_prog_id ?? null;

            if (empty($sessionId) || empty($schoolId) || empty($deptId) || empty($progId)) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Incomplete admission schedule (session/school/department/program).',
                ], 422);
            }

            $studentName = $applicant->api_name ?? null;
            $fatherName = $applicant->api_fathername ?? null;
            $guardianCnic = $applicant->api_guardian_cnic ?? null;

            DB::table('Student_challan')->insert([
                'sc_app_id' => $aaiId,
                'sc_session_id' => $sessionId,
                'sc_school_id' => $schoolId,
                'sc_department_id' => $deptId,
                'sc_program_id' => $progId,
                'sc_studentname' => $studentName,
                'sc_fathername' => $fatherName,
                'sc_guardian_cnic' => $guardianCnic,
                'sc_pro_entry_username' => $userId,
                'sc_challan_type' => 'Processing_Fee',
                'created_at' => $now,
                'sc_challan_status' => 'UNPAID',
                'sc_issu_date' => $now,
                'sc_due_date' => $dueDate,
                'sc_valid_date' => $validDate,
                'sc_visible_date' => $now,
                'invoiceamount' => $invoiceAmount,
                'sc_installment_allowed' => 'NO',
            ]);

            // Surplus before commit (same txn)
            $surplusAdjustment = $request->input('surplusAdjustment');
            $reqSessionId = $request->input('sessionId') ?: ($sessionId ?? null);
            $studentEnrollment = strtoupper((string) $request->input('studentEnrollment'));
            $this->applySurplusAdjustmentIfEnabled($surplusAdjustment, $studentEnrollment, $reqSessionId, $request);

            DB::commit();

            $allData = DB::table('Student_challan')
                ->where('sc_challan_type', 'Processing_Fee')
                ->where('sc_app_id', $aaiId)
                ->get();

            return $this->challanSuccess('Data submitted successfully', ['all_data' => $allData]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'An error occurred',
                'error' => $e->getMessage(),
                'success' => false,
                'status' => 'error',
            ], 500);
        }
    }
    /**
     * Format date as Y-m-d (helper).
     */
    private function formatDate($date)
    {

        $dueDate = new DateTime($date);
        return $dueDate = $dueDate->format('Y-m-d');

    }

    /**
     * Update 4th/5th installment dates on FIN_BLK_CHALLAN_GEN when present.
     */
    public function updateFeeInstallmentsDates($sessionId, $st_school_id, $st_dept_id, $st_prog_id, $studentType, $fee_type, $installmentDueDates, $installmentValidateDates)
    {
        $currentRecord = DB::table('FIN_BLK_CHALLAN_GEN')
            ->select(
                'FBC_4TH_INSTALLMENT_DUE_DATE',
                'FBC_4TH_INSTALLMENT_VALID_DATE',
                'FBC_5TH_INSTALLMENT_DUE_DATE',
                'FBC_5TH_INSTALLMENT_VALID_DATE'
            )
            ->where([
                ['fbc_session_id', $sessionId],
                ['fbc_school_id', $st_school_id],
                ['fbc_department_id', $st_dept_id],
                ['fbc_program_id', $st_prog_id],
                ['fbc_student_type', $studentType],
                ['fbc_fee_type', $fee_type]
            ])
            ->first();

        if (!$currentRecord) {
            return;
        }

        // Initialize an array to store update data
        $updateData = [];
        // Update 4th installment if the length condition is met and the column is empty or null
        if (count($installmentDueDates) > 3 && (empty($currentRecord->FBC_4TH_INSTALLMENT_DUE_DATE) || is_null($currentRecord->FBC_4TH_INSTALLMENT_DUE_DATE))) {
            $updateData['FBC_4TH_INSTALLMENT_DUE_DATE'] = $installmentDueDates[3];
        }
        if (count($installmentValidateDates) > 3 && (empty($currentRecord->FBC_4TH_INSTALLMENT_VALID_DATE) || is_null($currentRecord->FBC_4TH_INSTALLMENT_VALID_DATE))) {
            $updateData['FBC_4TH_INSTALLMENT_VALID_DATE'] = $installmentValidateDates[3] ?? null;
        }
        // Update 5th installment if the length condition is met and the column is empty or null
        if (count($installmentDueDates) > 4 && (empty($currentRecord->FBC_5TH_INSTALLMENT_DUE_DATE) || is_null($currentRecord->FBC_5TH_INSTALLMENT_DUE_DATE))) {
            $updateData['FBC_5TH_INSTALLMENT_DUE_DATE'] = $installmentDueDates[4];
        }
        if (count($installmentValidateDates) > 4 && (empty($currentRecord->FBC_5TH_INSTALLMENT_VALID_DATE) || is_null($currentRecord->FBC_5TH_INSTALLMENT_VALID_DATE))) {
            $updateData['FBC_5TH_INSTALLMENT_VALID_DATE'] = $installmentValidateDates[4] ?? null;
        }
        // Perform the update only if there's data to update
        if (!empty($updateData)) {
            $updatedRows = DB::table('FIN_BLK_CHALLAN_GEN')
                ->where('fbc_session_id', $sessionId)
                ->where('fbc_school_id', $st_school_id)
                ->where('fbc_department_id', $st_dept_id)
                ->where('fbc_program_id', $st_prog_id)
                ->where('fbc_student_type', $studentType)
                ->where('fbc_fee_type', $fee_type)
                ->update($updateData);

            // Check if the update was successful
            if (!$updatedRows) {
                return response()->json(['status' => 'failed', 'message' => 'No record was updated']);
            }
        }

    }
    // -------------------------------------------------------------------------
    // Student / challan lookup
    // -------------------------------------------------------------------------

    /**
     * Fetch student profile used by finance forms.
     */
    public function getStudentRec(Request $request)
    {
        $studentEnrollment = strtoupper($request->input('studentEnrollment'));
        $isCharacter = !is_numeric($studentEnrollment);
        // dd($studentEnrollment,  $isCharacter);

        if ($isCharacter) {
            //Existing query — bound enrollment (no string concat)
            $sql = " SELECT
                spi_name,
                school_code,
                school_name,
                dept_name,
                aai_id,
                CONCAT(sai_enrollment_no, '@bnu.edu.pk') AS spi_email,
                spi.spi_email as spi_personal_email,

                TO_CHAR(sai_student_status_datetime, 'DD-Mon-YYYY') AS student_status_datetime,
                prog_name,
                spi_mobile,
                sai_enrollment_no as username
                   ,school_id
                    ,dept_id,
                    prog_id,
                    sai_student_status,
                    p.prog_degree_level
            FROM students_apply_info sai
            LEFT JOIN students_personal_info spi ON sai.sai_spi_id = spi.spi_id
            LEFT JOIN admission_apply_info ON aai_api_id = sai.sai_api_id  and lower(CMS_SAI_ENROLLMENT_NO) = lower(SAI_ENROLLMENT_NO)
            LEFT JOIN schools s ON sai.sai_sdpo_school_id = s.school_id
            LEFT JOIN departments d ON sai.sai_sdpo_dept_id = d.dept_id
            LEFT JOIN programs p ON sai.sai_sdpo_prog_id = p.prog_id

            WHERE LOWER(sai.sai_enrollment_no) = LOWER(?)
        ";
            $data = DB::select($sql, [$studentEnrollment]);
        } else {
            $data = [];
        }

        // Check if data is not empty and return the data (its existing student data) first row
        if (!empty($data)) {
            return ($data[0]);
        } else if (!$isCharacter) {//if empty check from admission apply info
            $sql = "
        SELECT
            aai.aai_id,
            api_name AS spi_name,
            school_code,
            dept_name,
            api_imagename,
            school_name,
            prog_name,
            api_mobile as spi_mobile,
            api_email AS spi_email,
            aai_id AS aai_id,
            cms_sai_enrollment_no as username,
            school_id,
            dept_id,
            prog_id,
            aai_status AS sai_student_status,
            p.prog_degree_level
                FROM admission_apply_info aai
                LEFT JOIN admission_personal_info api ON aai.aai_api_id = api.api_id
                LEFT JOIN schools s ON aai.aai_sdpo_school_id = s.school_id
                LEFT JOIN departments d ON aai.aai_sdpo_dept_id = d.dept_id
                LEFT JOIN programs p ON aai.aai_sdpo_prog_id = p.prog_id
                WHERE aai.aai_id = ?
            ";
            $data = DB::select($sql, [$studentEnrollment]);
        } else {
            $data = [];
        }
        if (!empty($data)) {
            return ($data[0]);
        } else {
            // Return an error response if no data is found
            return ['success' => false, 'usernameError' => 'Invalid Student Enrollment Number'];
        }
    }

    protected function getFrom_feechallanwisesummary(Request $request)
    {
        // Initialize the query
        $data = DB::table('mv_feechallanwisesummary as mf')
            ->select(
                'mf.REGNO',
                'mf.SESSION_ID',
                'sess.SESS_NAME AS SESSION_NAME',
                'mf.PAIDON',
                DB::raw("CASE WHEN mf.PAIDON IS NOT NULL THEN 'PAID' ELSE 'UNPAID' END AS CHALLANSTATUS"),
                'mf.BUYERCODECHALLANNO',
                'mf.TOTALAMOUNTPAYABLE',
                'mf.PAIDAMT',
                'mf.ISSUEDATE',
                'mf.DUEDATE',
                'mf.VALIDDATE',
                'mf.INSTALLMENTS',
                'mf.PVNOSLIPNOSTUDENTID',
                'mf.CREATIONDATE',
                'mf.STUDENTNAME',
                'mf.PARENTGUARDIANNAME',
                'mf.PROGRAM',
                'mf.SCHOOL',
                'mf.DEPARTMENT',
                'mf.STUDENTTYPE',
                'mf.STUDENTTYPENEW',
                // 'mf.FEETYPESUMMARY', // Uncomment if needed
                'mf.PERMANENTADDRESS',
                'mf.SEMESTER',
                'mf.PARENTGUARDIANCNIC',
                DB::raw("123 as sai_student_status")
            )
            ->leftJoin('academic_sessions as sess', 'mf.SESSION_ID', '=', 'sess.sess_code');

        // Apply filters based on request parameters
        if ($request->filled('studentEnrollment')) {
            $studentEnrollment = strtoupper($request->input('studentEnrollment'));
            $data->where(function ($query) use ($studentEnrollment) {
                $query->where('mf.REGNO', $studentEnrollment)
                    ->orWhere('mf.PVNOSLIPNOSTUDENTID', $studentEnrollment);
            });
        }


        return $data->get();
    }




    public function getAppIdByEnrollmentNumber($enrollmentNumber)
    {
        $appIdObj = DB::table('students_apply_info')
            ->leftJoin('admission_apply_info', function ($join) {
                $join->on('students_apply_info.sai_api_id', '=', 'admission_apply_info.aai_api_id')
                    ->whereColumn('students_apply_info.sai_enrollment_no', '=', 'admission_apply_info.cms_sai_enrollment_no');
            })
            ->where('students_apply_info.sai_enrollment_no', $enrollmentNumber)
            ->selectRaw('COALESCE(admission_apply_info.aai_id, students_apply_info.sai_id) as aai_id')
            ->first();

        return $appIdObj ? $appIdObj->aai_id : null;
    }
    public function getEnrollmentNumberByAppId($appId)
    {
        $enrollmentObj = DB::table('admission_apply_info')
            ->leftJoin('students_apply_info', function ($join) {
                $join->on('admission_apply_info.aai_api_id', '=', 'students_apply_info.sai_api_id')
                    ->whereColumn('admission_apply_info.cms_sai_enrollment_no', '=', 'students_apply_info.sai_enrollment_no');
            })
            ->where('admission_apply_info.aai_id', $appId)
            ->selectRaw('COALESCE(students_apply_info.sai_enrollment_no, admission_apply_info.cms_sai_enrollment_no) as enrollment_no')
            ->first();


        return $enrollmentObj ? $enrollmentObj->enrollment_no : null;
    }



    /**
     * Search student + available challans for Single Challan screen.
     */
    public function searchStudentChallans(Request $request, $enrollmentNumber = null)
    {
        if ($enrollmentNumber === null) {
            $enrollmentNumber = strtoupper($request->input('studentEnrollment'));
        } else {
            $enrollmentNumber = strtoupper($enrollmentNumber);
        }

        $getStudentRec = $this->getStudentRec($request);
        // dd($getStudentRec);

        // Determine if the enrollment number is numeric or character-based
        $isCharacter = !is_numeric($enrollmentNumber);

        // If character-based, use the separate function to fetch the API ID
        if ($isCharacter) {
            $appId = $this->getAppIdByEnrollmentNumber($enrollmentNumber);
            if (!$appId) {
                return response()->json(['success' => false, 'message' => 'No APP ID Found']);
            }
        } else {
            // If numeric, use the enrollment number directly
            $appId = $enrollmentNumber;
        }
        // dd( $appId ,$enrollmentNumber);

        // Build the SQL query using parameter binding for safety
        $sql = "SELECT Student_challan.*, academic_sessions.sess_code
            FROM Student_challan
            LEFT JOIN academic_sessions ON Student_challan.sc_session_id = academic_sessions.sess_id
            WHERE Student_challan.sc_challan_type IN (
                'Full_semester','Hostel_Fee','Credit_hours','Fee_Refund','Hostel_Fee_Refund',
                'Short/Summer_Course','Processing_Fee','Credit_hours_Summer_Course'
              )
              AND invoice_status = 'E'
              AND Student_challan.sc_app_id = ?
            ORDER BY Student_challan.created_at DESC";

        $challans = DB::select($sql, [$appId]);
        // dd($challans);
        $getFrom_feechallanwisesummary = $this->getFrom_feechallanwisesummary($request);
        return response()->json([
            'studentRec' => $getStudentRec,
            'challans' => $challans,
            'getFrom_feechallanwisesummary' => $getFrom_feechallanwisesummary
        ]);
    }


    // -------------------------------------------------------------------------
    // Cancel / filter
    // -------------------------------------------------------------------------

    /**
     * Cancel one challan via fin_bulk_challan_cancellation procedure.
     * (Frontend loops this for multi-select.)
     */
    public function cancelChallans(Request $request)
    {
        try {
            $sc_challan_no = $request->input('id');
            $studentEnrollment = $request->input('enrollment_no');
            $app_id = DB::table('student_challan') // challan status from DB (page may be stale)
                ->select('sc_app_id')
                ->where('id', $sc_challan_no)
                ->first();

            if (!$app_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Challan not found.'
                ]);
            }

            $procedureName = 'fin_bulk_challan_cancellation';
            $bindings = [
                'P_SC_CHALLAN_ID' => $sc_challan_no,
                'P_ENROLLMENT_NO' => ctype_digit((string) $studentEnrollment) ? NULL : $studentEnrollment,
                'P_SC_APP_D' => $app_id->sc_app_id,
                'P_CANCELLED_USER' => auth()->user()->username,
                'P_REMARKS' => $request->remarks,
            ];

            $result = DB::executeProcedure($procedureName, $bindings);
            if (!$result) {
                return response()->json([
                    'status' => 'false',
                    'message' => 'Failed to Cancel Challan.'
                ]);
            }

            return response()->json(['success' => true, 'message' => 'Challan Cancelled Successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Filter challans by status (PAID / UNPAID / CANCELLED / VERSION).
     */
    public function filterChallans(Request $request)
    {
        // dd($request->all());
        // Retrieve the parameters from the request
        $status = $request->input('status');
        $enrollmentNumber = strtoupper($request->input('enrollmentNo'));
        $rawInput = $request->input('enrollmentNo'); // e.g. "F2024-0605 / 80806" or "N/A / 94448"

        // Split only on the LAST slash (the separator)
        $parts = explode(' / ', $rawInput); // safer because your separator always has spaces

        // Trim spaces
        $studentId = isset($parts[0]) ? trim($parts[0]) : null;
        $appId = isset($parts[1]) ? trim($parts[1]) : null;

        // Convert to uppercase after splitting
        $studentId = $studentId ? strtoupper($studentId) : null;

        // Handle N/A or missing
        if ($studentId === 'N/A' || $studentId === '') {
            $studentId = null;
        }
        if ($appId === '' || $appId === null) {
            $appId = null;
        }

        if (empty($appId)) {
            return response()->json([
                'success' => false,
                'status' => 'error',
                'message' => 'App ID is required for challan filter. Please search the student first.',
            ], 422);
        }

        // dd($studentId, $appId);


        // Determine which table to query and build the query accordingly
        if ($status === 'VERSION') {
            // dd('VERSION');
            $challans = DB::table('AUD_BNUCMS.Student_challan_AUD as Student_challan_aud')
                ->leftJoin('academic_sessions', 'Student_challan_aud.SC_SESSION_ID', '=', 'academic_sessions.sess_id')
                ->orderBy('Student_challan_aud.created_at', 'desc')
                ->select('Student_challan_aud.*', 'academic_sessions.sess_code')
                ->whereNotNull('Student_challan_aud.VERSION')
                ->where('Student_challan_aud.sc_app_id', $appId)
                ->get();
        } else if ($status === 'CANCELLED') {
            // dd('CANCELLED');
            // Query for cancelled challans
            $challans = DB::table('FIN_STUDENT_CANCEL_CHALLAN')
                ->leftJoin('academic_sessions', 'FIN_STUDENT_CANCEL_CHALLAN.sc_session_id', '=', 'academic_sessions.sess_id')
                ->orderBy('FIN_STUDENT_CANCEL_CHALLAN.created_at', 'desc')
                ->select('FIN_STUDENT_CANCEL_CHALLAN.*', 'academic_sessions.sess_code')
                ->where('FIN_STUDENT_CANCEL_CHALLAN.sc_app_id', $appId)
                // ->where('FIN_STUDENT_CANCEL_CHALLAN.sc_challan_status', $status)
                ->get();
        } else {
            // dd('else');
            // Query for other challans
            $query = DB::table('Student_challan')
                ->leftJoin('academic_sessions', 'Student_challan.sc_session_id', '=', 'academic_sessions.sess_id')
                ->orderBy('Student_challan.created_at', 'desc')
                ->select('Student_challan.*', 'academic_sessions.sess_code')
                ->where('Student_challan.sc_app_id', $appId);

            // Apply status filter if it is not 'ALL'
            if ($status !== 'ALL') {
                $query->where('Student_challan.sc_challan_status', $status);
            }

            $challans = $query->get();
        }
        // dd($challans);
        // Return the results as JSON
        return response()->json([
            'success' => true,
            'message' => 'Data retrieved successfully',
            'all_data' => $challans
        ]);
    }



    // -------------------------------------------------------------------------
    // Fee structure
    // -------------------------------------------------------------------------

    /**
     * Load fee structure amounts for selected fee type / program / session.
     */
    public function getFeeStructure(Request $request)
    {
        // dd($request->all());

        // Retrieve the request data
        $fee_type = $request->fee_type;
        $school_id = $request->school_id;
        $dept_id = $request->dept_id;
        $prog_id = $request->prog_id;
        $sess_id = $request->sess_id;
        $sess_type = empty($request->sess_type) ? 'Regular' : $request->sess_type;
        $roadmap = empty($request->roadmap) ? null : $request->roadmap;
        // Whitelist numeric roadmap/semester — never concatenate raw request into SQL
        if ($roadmap !== null && $roadmap !== '' && is_numeric($roadmap)) {
            $roadmapCondition = 'AND FFS_ROADMAP_SESSION = ' . (int) $roadmap;
        } else {
            $roadmap = null;
            $roadmapCondition = 'AND FFS_ROADMAP_SESSION is null';
        }
        $semester = empty($request->semester) ? null : $request->semester;
        if ($semester !== null && $semester !== '' && is_numeric($semester)) {
            $semesterCondition = ' AND FFS_SEMESTER_NO = ' . (int) $semester;
        } else {
            $semester = null;
            $semesterCondition = 'AND FFS_SEMESTER_NO is null';
        }
        $student_type = $request->student_type;
        $hostel_student_type = $request->hostel_student_type;

        // Intake-range: the student's roadmap session (SAI_SDPO_SESS_ID from
        // students_apply_info) must fall within the fee structure's intake range
        // (FFS_INTAKE_FROM_SESS .. FFS_INTAKE_TO_SESS). Rows without an intake
        // range (NULL columns) are not excluded.
        $intakeCondition = '';
        if (!empty($request->student_id)) {
            $roadmapSess = DB::table('students_apply_info')
                ->whereRaw('LOWER(SAI_ENROLLMENT_NO) = ?', [strtolower($request->student_id)])
                ->value('SAI_SDPO_SESS_ID');
            if (!empty($roadmapSess) && is_numeric($roadmapSess)) {
                $roadmapSess = (int) $roadmapSess;
                $intakeCondition = " AND (FFS_INTAKE_FROM_SESS IS NULL OR FFS_INTAKE_FROM_SESS <= $roadmapSess)
                                     AND (FFS_INTAKE_TO_SESS IS NULL OR FFS_INTAKE_TO_SESS >= $roadmapSess) ";
            }
        }

        // For hostel fee types, resolve the campus from the student's assigned
        // hostel and lock the Session Type to it. HT_ID: 1-2 = on-campus (BNU),
        // 3-4 = Safari (_outside), 5-6 = Bedian (_outside_bedian). Residency is
        // forced to Regular when assigned; only an unassigned student stays
        // manually editable.
        $hostel_assigned = false;
        $isHostelFee = ($fee_type == 'Hostel')
            || in_array($request->fee_type_for_hostel, ['Hostel_Full_semster', 'Hostel_day_wise', 'Hostel_other_utilities']);
        if ($isHostelFee && !empty($request->student_id)) {
            $normalizedAssign = $this->resolveAssignedHostelSessType($request->student_id, $sess_id);
            if ($normalizedAssign !== null) {
                $sess_type = $normalizedAssign;
                $hostel_assigned = true;
            }
        }
        $hostelRegularityMismatch = $this->hostelRegularityChallanMismatchMessage(
            $request->fee_type_for_hostel,
            $sess_type,
            $hostel_assigned
        );
        if ($fee_type != 'Processing_Fee') {
            // Credit-hour types use procedures (fin_credit_hr_wise_challan / FIN_PRO_SUMMER_CREDIT_HOUR)
            // — not fin_fee_structure rows
            if ($fee_type === 'Credit_hours' || $fee_type === 'Credit_hours_Summer_Course') {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Credit hour challan does not use fee structure.',
                    'data' => null,
                    'fee_type_selected' => $fee_type,
                    'sess_type' => $sess_type,
                    'hostel_assigned' => $hostel_assigned,
                    'hostel_regularity_mismatch' => $hostelRegularityMismatch,
                    'roadmap' => null,
                    'semester' => null,
                ]);
            }
            if ($fee_type == 'Full_semester' || $fee_type == 'Short/Summer_Course') {
                $sql = "
            SELECT *
            FROM fin_fee_structure
            WHERE ffs_sess_id = :sess_id
            AND ffs_school_id = :school_id
            AND ffs_dept_id = :dept_id
            AND ffs_prog_id = :prog_id
            AND ffs_student_type = :student_type
            AND ffs_fee_type = :fee_type
             $roadmapCondition
             $semesterCondition
             $intakeCondition
            and ffs_status='E'";
                // dd($sql, [
                //     'sess_id' => $sess_id,
                //     'school_id' => $school_id,
                //     'dept_id' => $dept_id,
                //     'prog_id' => $prog_id,
                //     'student_type' => $student_type,
                //     'fee_type' => $fee_type,
                //     'roadmap' => $roadmap,
                //     'semester' => $semester,
                // ]);
                // dd($sql);
                $result = DB::select($sql, [
                    'sess_id' => $sess_id,
                    'school_id' => $school_id,
                    'dept_id' => $dept_id,
                    'prog_id' => $prog_id,
                    'student_type' => $student_type,
                    'fee_type' => $fee_type
                ]);
            } else if ($fee_type != 'Hostel') {
                $sql = "
            SELECT *
            FROM fin_fee_structure
            WHERE ffs_sess_id = :sess_id
            AND ffs_school_id = :school_id
            AND ffs_dept_id = :dept_id
            AND ffs_prog_id = :prog_id
            AND ffs_student_type = :student_type
            AND ffs_fee_type = :fee_type
             $intakeCondition
            and ffs_status='E'";

                $result = DB::select($sql, [
                    'sess_id' => $sess_id,
                    'school_id' => $school_id,
                    'dept_id' => $dept_id,
                    'prog_id' => $prog_id,
                    'student_type' => $student_type,
                    'fee_type' => $fee_type
                ]);
            } else if ($fee_type == 'Hostel' || in_array($request->fee_type_for_hostel, ['Hostel_other_utilities', 'Hostel_day_wise', 'Hostel_Full_semster'], true)) {

                $sql = "
                SELECT *
                FROM fin_hostel_fee_structure
                WHERE fhfs_session = :sess_id
                AND UPPER(TRIM(fhfs_session_type)) = :sess_type
                ";

                $bindings = [
                    'sess_id'   => $sess_id,
                    'sess_type' => strtoupper(trim((string) $sess_type)),
                ];


                // Other Utilities: do not filter by hostel student type (UI field is hidden)
                $feeTypeForHostel = $request->fee_type_for_hostel;
                // dd($feeTypeForHostel, $hostel_student_type);
                if ($feeTypeForHostel !== 'Hostel_other_utilities' && !empty($hostel_student_type)) {
                    $sql .= " and fhfs_student_type = :student_type";
                    $bindings['student_type'] = $hostel_student_type;
                }

                $sql .= " and fhfs_status='E'";

                // dd($sql, $bindings);

                $result = DB::select($sql, $bindings);

                // dd($result);
            }
        } else {
            $result[0] = (object) [
                'feeType' => 'Processing_Fee',

            ];
        }
        // dd($result);
        if (!empty($result)) {
            return response()->json([
                'status' => 'success',
                'data' => $result[0],
                'sess_type' => $sess_type,
                'hostel_assigned' => $hostel_assigned,
                'hostel_regularity_mismatch' => $hostelRegularityMismatch,
                'roadmap' => $roadmap,
                'semester' => $semester,

            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'No fee structure found.',
                'fee_type_selected' => $fee_type,
                'sess_type' => $sess_type,
                'hostel_assigned' => $hostel_assigned,
                'hostel_regularity_mismatch' => $hostelRegularityMismatch,
                'roadmap' => $roadmap,
                'semester' => $semester,
            ]);
        }
    }



    // -------------------------------------------------------------------------
    // Bulk challan gen helpers / hostel utilities / edit rules
    // -------------------------------------------------------------------------

    /**
     * Insert FIN_BLK_CHALLAN_GEN row when missing (installment schedule seed).
     */
    public static function insert_In_fin_blk_challan_gen(


        $sessionId,
        $st_school_id,
        $st_dept_id,
        $st_prog_id,
        $studentType,
        $fee_type,
        $SinlgeChallanDueDate,
        $SinlgeChallanValidateDate,
        $installmentDueDates,
        $installmentValidateDates,
        $request,
        $user_id,
        $student_status
    ) {


        // dd(dd(1111111111));

        // Check for existing record
        $currentRecord = DB::table('FIN_BLK_CHALLAN_GEN')
            ->where([
                ['fbc_session_id', $sessionId],
                ['fbc_school_id', $st_school_id],
                ['fbc_department_id', $st_dept_id],
                ['fbc_program_id', $st_prog_id],
                ['fbc_student_type', $studentType],
                ['fbc_fee_type', $fee_type]
            ])
            ->first();
        // dd($currentRecord);
// dd($installmentDueDates[1],$installmentValidateDates[1]);
        // If no record exists, insert a new one
        if (is_null($currentRecord)) {
            DB::table('FIN_BLK_CHALLAN_GEN')->insert([
                'FBC_SESSION_ID' => $sessionId,
                'FBC_SCHOOL_ID' => $st_school_id,
                'FBC_DEPARTMENT_ID' => $st_dept_id,
                'FBC_PROGRAM_ID' => $st_prog_id,
                'FBC_DUE_DATE' => $SinlgeChallanDueDate,
                'FBC_VALID_DATE' => $SinlgeChallanValidateDate,
                'FBC_VISIBLE_DATE' => now()->format('Y-m-d'),
                'FBC_DEGREE_LEVEL' => $request->degree_level,
                'FBC_STUDENT_TYPE' => $studentType === 'New without Registration' ? 'New Admission FALL' : $studentType,
                'FBC_FEE_TYPE' => $fee_type,
                'FBC_STUDENT_STATUS' => $student_status,
                'fbc_2nd_installment_due_date' => $installmentDueDates[1] ?? null,
                'fbc_3rd_installment_due_date' => $installmentDueDates[2] ?? null,
                'fbc_4th_installment_due_date' => $installmentDueDates[3] ?? null,
                'fbc_5th_installment_due_date' => $installmentDueDates[4] ?? null,
                'fbc_2nd_installment_valid_date' => $installmentValidateDates[1] ?? null,
                'fbc_3rd_installment_valid_date' => $installmentValidateDates[2] ?? null,
                'fbc_4th_installment_valid_date' => $installmentValidateDates[3] ?? null,
                'fbc_5th_installment_valid_date' => $installmentValidateDates[4] ?? null,
                'FBC_ENTRY_USER_ID' => $user_id,
                'FBC_STATUS' => 'E',
            ]);
        }
    }

    /**
     * Resolve AAI id from enrollment / applicant input.
     */
    public function getStudentAaiId($studentEnrollment)
    {
        $studentRecord = DB::table('admission_apply_info')
            ->select('aai_id')
            ->where(DB::raw('upper("CMS_SAI_ENROLLMENT_NO")'), strtoupper($studentEnrollment))
            ->first();
        if(empty($studentRecord)){
            $studentRecord = DB::table('STUDENTS_APPLY_INFO')
            ->select('SAI_ID as aai_id')
            ->where(DB::raw('upper("SAI_ENROLLMENT_NO")'), strtoupper($studentEnrollment))
            ->first();
        }
        if ($studentRecord) {
            return $studentRecord->aai_id;
        } else {
            return null;
        }
    }

    private function sendNewChallanMessage($username)
    {


        if (!is_numeric($username)) {
            $username = strtolower($username);
            $userDetail = User::whereRaw('LOWER(username) = ?', [$username])->first();
        } else {
            $userDetail = DB::table('admission_apply_info as aai')
                ->leftJoin('admission_personal_info as api', 'aai.aai_api_id', '=', 'api.api_id')
                ->where('aai.aai_id', $username)
                ->select('api.api_mobile as mobile')
                ->first();
        }

        if (!$userDetail || empty($userDetail->mobile)) {
            return false;
        }

        $mobile = $userDetail->mobile;
        $message = "Dear Student, your fee challan has been updated. Please log in to your CMS account to view it before making the payment.";
        try {
            sendSMSMessage($mobile, $message);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Hostel challan generate / update
     * (Hostel_Full_semster, Hostel_day_wise, Hostel_other_utilities).
     * Uses fin_new_std_hostel_chln_gen / fin_new_std_hostel_chln_updt (same as prod).
     */
    public function generate_hostel_challan($request)
    {
        $validator = Validator::make($request->all(), [
            'studentEnrollment' => 'required',
            'sessionId' => 'required',
            'student_type' => 'required',
            'fee_type' => 'required',
            'student_status' => 'required',
            'dueDate' => 'required|date',
            'validateDate' => 'required|date|after_or_equal:dueDate',
            'sess_type' => 'required',
            'daysNo' => $request->input('fee_type') === 'Hostel_day_wise'
                ? 'required|integer|min:1|max:365'
                : 'nullable|integer|min:0|max:365',
        ], [
            'dueDate.required' => 'Due date is required.',
            'validateDate.required' => 'Validate date is required.',
            'validateDate.after_or_equal' => 'Validate date must be on or after due date.',
            'sess_type.required' => 'Session Type is required for Hostel challan.',
            'daysNo.required' => 'Number of days is required for Hostel day-wise.',
            'daysNo.max' => 'Days cannot be more than 365.',
            'daysNo.min' => 'Days must be at least 1.',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()->toArray(),
            ], 422);
        }

        $studentEnrollment = strtoupper($request->input('studentEnrollment'));
        $fee_type = $request->input('fee_type');
        $studentType = $request->input('student_type');
        $hostel_student_type = $request->input('hostel_student_type') ?: $studentType;
        $sessionId = $request->input('sessionId');
        $st_school_id = $request->input('st_school_id');
        $st_dept_id = $request->input('st_dept_id');
        $st_prog_id = $request->input('st_prog_id');
        $daysNo = (int) ($request->input('daysNo') ?? 0);
        $refrigeratorCheckBox = $request->input('refrigeratorCheckBox');
        $conditionerCheckBox = $request->input('conditionerCheckBox');
        $electricHeaterCheckBox = $request->input('electricHeaterCheckBox');
        $hostelSecurityFee = $request->input('hostel_security_fee');
        $hostelRemarks = $request->input('hostelRemarks');
        $SinlgeChallanDueDate = $request->input('dueDate');
        $SinlgeChallanValidateDate = $request->input('validateDate');
        $verifyEdit = $request->input('verifyEdit');
        $editId = $request->input('edit_id');
        $manualFeeAdjustment = $request->input('manualFeeAdjustment');

        // Normalize edit id early (accept CMS id or printed challan_no)
        $resolvedEditChallan = null;
        if (!empty($editId)) {
            $resolvedEditChallan = $this->resolveStudentChallanByEditId($editId);
            if ($resolvedEditChallan) {
                $editId = $resolvedEditChallan->id;
                // Keep request in sync for helpers that re-read edit_id
                $request->merge(['edit_id' => $editId]);
            }
        }

        if ($fee_type !== 'Hostel_other_utilities' && empty($request->input('hostel_student_type'))) {
            return response()->json([
                'success' => false,
                'recordIssue' => true,
                'message' => 'Hostel Student Type is required (Existing / New Applicant).',
            ], 422);
        }

        // Manual fee adjustment on utilities edit → dedicated helper
        if ($fee_type == 'Hostel_other_utilities' && $manualFeeAdjustment === 'YES' && $verifyEdit == "1" && !empty($editId)) {
            return $this->update_hostel_other_utilities_manual_fee_adjustment($request);
        }

        // Utilities edit, no utility checkbox → update dates only
        if ($fee_type === 'Hostel_other_utilities' && $verifyEdit == "1" && !empty($editId)) {
            if (
                $refrigeratorCheckBox !== 'on' &&
                $electricHeaterCheckBox !== 'on' &&
                $conditionerCheckBox !== 'on'
            ) {
                try {
                    if (!$resolvedEditChallan) {
                        return $this->challanClientError('Challan not found for date update.', 404);
                    }
                    if (strtoupper((string) ($resolvedEditChallan->sc_challan_status ?? '')) === 'PAID'
                        && (int) auth()->user()->group_id !== 644) {
                        return $this->challanClientError('Paid hostel utilities challans cannot be edited.');
                    }
                    $formattedDueDate = Carbon::createFromFormat('Y-m-d', $SinlgeChallanDueDate)->format('d-M-y h.i.s A');
                    $formattedValidateDate = Carbon::createFromFormat('Y-m-d', $SinlgeChallanValidateDate)->format('d-M-y h.i.s A');
                    $update = DB::table('student_challan')
                        ->where('id', $editId)
                        ->update([
                            'sc_due_date' => DB::raw("TO_DATE('$formattedDueDate', 'DD-MON-YY HH:MI:SS AM')"),
                            'sc_valid_date' => DB::raw("TO_DATE('$formattedValidateDate', 'DD-MON-YY HH:MI:SS AM')"),
                            'SC_UPDATED_BY_USERNAME' => auth()->user()->username,
                            'UPDATED_AT' => now(),
                        ]);
                    if ($update) {
                        $this->persistEditAuditIfNeeded();
                        return response()->json([
                            'success' => true,
                            'message' => 'Hostel utilities dates updated successfully',
                        ]);
                    }
                    return $this->challanClientError('Hostel utilities dates were not updated.', 404);
                } catch (\Exception $e) {
                    return response()->json([
                        'success' => false,
                        'message' => 'An error occurred',
                        'error' => $e->getMessage(),
                    ], 500);
                }
            }
            // utility checkbox checked without Fee Adjustment → require Fee Adjustment (same as prod)
            return $this->challanClientError(
                'To update amount, please check mark Fee Adjustment Checkbox',
                422
            );
        } elseif ($fee_type === 'Hostel_other_utilities' && $verifyEdit != "1") {
            if (
                $refrigeratorCheckBox !== 'on' &&
                $electricHeaterCheckBox !== 'on' &&
                $conditionerCheckBox !== 'on'
            ) {
                return $this->challanClientError('No utility selected. Check at least one Include checkbox.', 422);
            }
        }

        // Hostel challan generate: hostel_assign row must exist for this student + session
        if (!$this->studentHasHostelAssignForSession($studentEnrollment, $sessionId)) {
            return $this->challanClientError(
                'No hostel is assigned to this student for the selected session. Please assign a hostel first.'
            );
        }

        // New generate: Regular assign → Full Semester only; Non-Regular → Day Wise only
        if ((string) $verifyEdit !== '1'
            && in_array($fee_type, ['Hostel_Full_semster', 'Hostel_day_wise'], true)) {
            $assignedSessType = $this->resolveAssignedHostelSessType($studentEnrollment, $sessionId);
            $regularityMismatch = $this->hostelRegularityChallanMismatchMessage(
                $fee_type,
                $assignedSessType,
                $assignedSessType !== null
            );
            if ($regularityMismatch !== null) {
                return $this->challanClientError($regularityMismatch);
            }
        }

        // Hostel Full Semester: sess_type must match hostel_assign campus for this session
        // (Regular / Safari / Bedian). Unassigned students are not blocked.
        if ($fee_type === 'Hostel_Full_semster') {
            $assignMismatch = $this->hostelFullSemesterAssignMismatchMessage(
                $studentEnrollment,
                $sessionId,
                $request->input('sess_type')
            );
            if ($assignMismatch !== null) {
                return $this->challanClientError($assignMismatch);
            }
        }

        DB::beginTransaction();
        try {
            // Bindings must match prod GenerateSingleChallanController (lines 245-292)
            if (($fee_type == 'Hostel_Full_semster' || $fee_type == 'Hostel_day_wise') && ($verifyEdit == 1 || $verifyEdit == '1') && !empty($editId)) {
                $procedureName = 'fin_new_std_hostel_chln_updt';
                $bindings = [
                    'p_schol_id' => $st_school_id,
                    'p_dept_id' => $st_dept_id,
                    'p_prog_id' => $st_prog_id,
                    'p_session' => $sessionId,
                    'p_due_date' => $SinlgeChallanDueDate,
                    'p_valid_date' => $SinlgeChallanValidateDate,
                    'p_ffs_student_type' => $fee_type,
                    'p_hostel_security' => $hostelSecurityFee ?? 0,
                    // 'p_studentType' => ($studentType != 'Existing') ? 'New_Admission~' . $studentType : $studentType . '~' . 'Existing',
                    'p_hostel_studentType' => $hostel_student_type,
                    'p_no_of_days' => $daysNo ?? 0,
                    'refrigeratorCheckBox' => $refrigeratorCheckBox ?: 'NO',
                    'conditionerCheckBox' => $conditionerCheckBox ?: 'NO',
                    'electricHeaterCheckBox' => $electricHeaterCheckBox ?: 'NO',
                    'hostelRemarks' => $hostelRemarks ?? '',
                    'p_AAI_ID' => $studentEnrollment,
                    'session_type' => strtolower((string) $request->sess_type),
                    'p_challan_no' => $editId,
                    'p_entry_username' => auth()->user()->username,
                    'p_studentType' => $studentType,
                ];
} else {
                $procedureName = 'fin_new_std_hostel_chln_gen';
                $bindings = [
                    'p_schol_id' => $st_school_id,
                    'p_dept_id' => $st_dept_id,
                    'p_prog_id' => $st_prog_id,
                    'p_session' => $sessionId,
                    'p_due_date' => $SinlgeChallanDueDate,
                    'p_valid_date' => $SinlgeChallanValidateDate,
                    'p_ffs_student_type' => $fee_type,
                    'p_hostel_security' => $hostelSecurityFee ?? 0,
                    // 'p_studentType' => ($studentType != 'Existing') ? 'New_Admission~' . $studentType : $studentType . '~' . 'Existing',
                    'p_hostel_studentType' => $hostel_student_type,
                    'p_no_of_days' => $daysNo ?? 0,
                    'refrigeratorCheckBox' => $refrigeratorCheckBox ?: 'NO',
                    'conditionerCheckBox' => $conditionerCheckBox ?: 'NO',
                    'electricHeaterCheckBox' => $electricHeaterCheckBox ?: 'NO',
                    'hostelRemarks' => $hostelRemarks ?? '',
                    'p_AAI_ID' => $studentEnrollment,
                    'session_type' => strtolower((string) $request->sess_type),
                    'p_entry_username' => auth()->user()->username,
                    'p_studentType' => $studentType,
                ];
            }

            // dd($bindings);
$paramsString = implode(',', array_map(function ($param) {
                return ':' . $param;
            }, array_keys($bindings)));
            $sql = "begin {$procedureName}({$paramsString}); end;";
            $result = DB::statement($sql, $bindings);

            if (!$result) {
                DB::rollBack();
                return $this->challanClientError('Failed to Generate Hostel Challan.');
            }

            DB::commit();
            return $this->challanSuccess('Hostel Challan generated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'An error occurred',
                'error' => $e->getMessage(),
                'success' => false,
                'status' => 'error',
            ], 500);
        }
    }

    /**
     * Hostel Other Utilities + Fee Adjustment (edit).
     * Same amount logic as ManualScholarshipChallanController::calculateHostelUtilitiesAmounts
     * — only checked utilities count; update existing challan columns (no procedure).
     */
    public function update_hostel_other_utilities_manual_fee_adjustment($request)
    {
        try {
            $editId = $request->input('edit_id');
            $resolved = $this->resolveStudentChallanByEditId($editId);
            if (!$resolved) {
                return $this->challanClientError('Challan not found for utilities update.', 404);
            }
            if (strtoupper((string) ($resolved->sc_challan_status ?? '')) === 'PAID'
                && (int) auth()->user()->group_id !== 644) {
                return $this->challanClientError('Paid hostel utilities challans cannot be edited.');
            }
            $editId = $resolved->id;

            $SinlgeChallanDueDate = $request->input('dueDate');
            $SinlgeChallanValidateDate = $request->input('validateDate');
            $hostelRemarks = trim((string) $request->input('hostelRemarks', ''));

            $refrigeratorOn = $request->input('refrigeratorCheckBox') === 'on';
            $conditionerOn = $request->input('conditionerCheckBox') === 'on';
            $electricOn = $request->input('electricHeaterCheckBox') === 'on';

            if (!$refrigeratorOn && !$conditionerOn && !$electricOn) {
                return $this->challanClientError('No utility selected. Check at least one Include checkbox.', 422);
            }

            // Same as Manual Scholarship: only checked utilities are included
            $refrigeratorFee = $refrigeratorOn
                ? round((float) $request->input('refrigeratorFee', 0), 2)
                : 0;
            $conditionerFee = $conditionerOn
                ? round((float) $request->input('conditionerFee', 0), 2)
                : 0;
            $electricFee = $electricOn
                ? round((float) $request->input('electricFee', 0), 2)
                : 0;

            if ($refrigeratorOn && $refrigeratorFee <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Enter refrigerator fee amount greater than zero.',
                ], 422);
            }
            if ($conditionerOn && $conditionerFee <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Enter air conditioner fee amount greater than zero.',
                ], 422);
            }
            if ($electricOn && $electricFee <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Enter electric heater fee amount greater than zero.',
                ], 422);
            }

            $invoiceAmount = round($refrigeratorFee + $conditionerFee + $electricFee, 2);
            if ($invoiceAmount <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invoice amount must be greater than zero.',
                ], 422);
            }

            // Same fee-details string as ManualScholarshipChallanController::buildHostelUtilitiesFeeDetails
            $feeDetails = 'Other utilities charges';
            if ($refrigeratorOn) {
                $feeDetails .= ', Refrigerator charges';
            }
            if ($conditionerOn) {
                $feeDetails .= ', Conditioner charges';
            }
            if ($electricOn) {
                $feeDetails .= ', Electric heater charges';
            }
            if ($hostelRemarks !== '') {
                $feeDetails = $hostelRemarks;
            }

            $formattedDueDate = Carbon::createFromFormat('Y-m-d', $SinlgeChallanDueDate)->format('d-M-y h.i.s A');
            $formattedValidateDate = Carbon::createFromFormat('Y-m-d', $SinlgeChallanValidateDate)->format('d-M-y h.i.s A');

            $update = DB::table('student_challan')
                ->where('id', $editId)
                ->update([
                    'SC_REFRIGERATOR' => $refrigeratorFee,
                    'SC_CONDITIONER' => $conditionerFee,
                    'SC_ELECTRIC' => $electricFee,
                    'FFS_HOSTEL_AMT' => $invoiceAmount,
                    'INVOICEAMOUNT' => $invoiceAmount,
                    'TOTAL_AMOUNT_PAYABLE' => $invoiceAmount,
                    'SC_HOSTEL_FEE_DETAILS' => $feeDetails,
                    'sc_due_date' => DB::raw("TO_DATE('$formattedDueDate', 'DD-MON-YY HH:MI:SS AM')"),
                    'sc_valid_date' => DB::raw("TO_DATE('$formattedValidateDate', 'DD-MON-YY HH:MI:SS AM')"),
                    'SC_UPDATED_BY_USERNAME' => auth()->user()->username,
                    'UPDATED_AT' => now(),
                ]);

            if ($update) {
                $this->persistEditAuditIfNeeded();
                return response()->json([
                    'success' => true,
                    'message' => 'Hostel utilities and invoice amount updated successfully',
                    'data' => [
                        'mode' => 'hostel_utilities',
                        'refrigerator_fee' => $refrigeratorFee,
                        'conditioner_fee' => $conditionerFee,
                        'electric_fee' => $electricFee,
                        'invoice_amount' => $invoiceAmount,
                    ],
                ]);
            }

            throw new \Exception('Failed to update challan');
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating hostel utilities: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Scholarship / fee-edit guard used before verifyEdit generate.
     * Keep all branch conditions intact.
     */
    public function isAllowEdit($request)
    {
        // dd($request->all());
        $sc_challan_no = $request->edit_id;
        $sc_challan = $this->resolveStudentChallanByEditId($sc_challan_no);
        // Fail closed — missing challan must not bypass scholarship guard
        if (!$sc_challan) {
            return false;
        }
        // dd($sc_challan);
        // dd($request->tuitionFee,  $sc_challan->sc_installment_tuition_fee_amnt);
        // dd($sc_challan->sc_challan_type == 'Full_semester'  && empty($sc_challan->sc_enrolled_cr_hr) || $sc_challan->sc_challan_type == 'Short/Summer_Course' && empty($sc_challan->sc_enrolled_cr_hr));
        // dd($request->tuitionFee !=  $sc_challan->sc_installment_tuition_fee_amnt);
        if (empty($sc_challan->sc_scholarship_amount)) {//allow edit since its not a scholarship challan
            return true;
        } else {
            if (($sc_challan->sc_challan_type == 'Hostel_Fee')) {
                if ($sc_challan->sc_hostel_fee_type != 'Hostel_other_utilities') {
                    if ($request->hostelFee != $sc_challan->ffs_hostel_amt) {//dont allow since user has changed the hostel fee
                        return false;
                    } else {
                        return true;//allow editing hostel security fee only
                    }
                    return false; //dont allow since its scholarship challan and either hostel fullsemester or hostel day wise challan
                } else {
                    return true;//allow edit since its scholarship challan BUT a hostel utility challan
                }

            }
        }
        //CHECK for challans other than hostel fee challans
        if ($request->manualFeeAdjustment == 'YES' && !empty($request->edit_id)) {
            if (empty($sc_challan->sc_scholarship_amount)) {//allow edit since its not a scholarship challan
                return true;
            } else if (($sc_challan->sc_challan_type == 'Full_semester' && empty($sc_challan->sc_enrolled_cr_hr)) || ($sc_challan->sc_challan_type == 'Short/Summer_Course' && empty($sc_challan->sc_enrolled_cr_hr))) {//scholarship is present then check if (challan type is fullsemester or summer/short without enrolled credit hours ) OR (Challan type is Hostel Fee AND  is not Hostel_other_utilities)
                // dd('scholarship challans of full semester or summer/short without enrolled credit hours');
                if ($request->tuitionFee != $sc_challan->sc_installment_tuition_fee_amnt) {//dont allow since user has changed the tuition fee
                    return false;
                } else {
                    return true;
                }
            } else {//dont allow since it is scholarship challan
                return false;
            }

        } else {//allow since user is not editing any FEE
            return true;
        }
    }

    /**
     * Fetch FIN_FEE_ADJUSTMENT rows with surplus > 0 for an app id (edit Surplus UI).
     */
    public function getSurplusAdjustmentsByAppId(Request $request)
    {
        $appId = $request->input('app_id');
        $enrollmentNo = $request->input('enrollment_no');

        if (empty($appId) && empty($enrollmentNo)) {
            return response()->json([
                'success' => false,
                'message' => 'App ID or enrollment is required.',
            ], 422);
        }

        $query = DB::table('FIN_FEE_ADJUSTMENT as ffa')
            ->select(
                'ffa.FFA_ID',
                'ffa.FFA_SC_CHALLAN_NO',
                'ffa.FFA_ENROLLMENT_NO',
                'ffa.FFA_APP_ID',
                'ffa.FFA_SURPLUS_AMOUNT',
                'ffa.FFA_REMARKS',
                'ffa.ffa_amount_status',
                'ffa.FFA_STATUS',
                'ffa.FF_AMNT_STATUS',
                'ffac.FFAC_TITLE'
            )
            ->leftJoin('FIN_FEE_ADJUSTMENT_CATEGORY as ffac', 'ffac.FFAC_ID', '=', 'ffa.FFA_ADJUSTMENT_CATEGORY')
            ->where('ffa.FFA_SURPLUS_AMOUNT', '>', 0)
            ->where(function ($q) {
                $q->where('ffa.FFA_STATUS', 'E')
                    ->orWhereNull('ffa.FFA_STATUS');
            })
            ->where(function ($q) use ($appId, $enrollmentNo) {
                // Prefer App ID as primary identity. FE often sends both app_id + enrollment;
                // requiring AND hid FFA rows that only store FFA_APP_ID (UAT/surplus modal empty).
                // Do not OR across different students (security).
                if (!empty($appId)) {
                    $q->where('ffa.FFA_APP_ID', $appId);
                } elseif (!empty($enrollmentNo)) {
                    $q->whereRaw('LOWER(ffa.FFA_ENROLLMENT_NO) = ?', [strtolower($enrollmentNo)]);
                }
            })
            ->orderBy('ffa.FFA_CREATED_AT', 'desc');

        $adjustments = $query->get();

        return response()->json([
            'success' => true,
            'adjustments' => $adjustments,
        ]);
    }
}