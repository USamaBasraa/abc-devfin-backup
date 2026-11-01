<?php

namespace App\Http\Controllers\StudentFinancial;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Throwable;

class ManualScholarshipChallanController extends Controller
{
    /**
     * Fees entered manually on the page.
     * Surplus is intentionally excluded from this module.
     */
    /**
     * Full fee map still used when writing student_challan columns.
     * UI currently shows only hostel_fee.
     */
    private const FEE_FIELDS = [
        'tuition_fee'      => ['label' => 'Tuition Fee',      'column' => 'SC_TUITION_FEE_AMNT'],
        'admission_fee'    => ['label' => 'Admission Fee',    'column' => 'FFS_ADMISSION_AMT'],
        'security_fee'     => ['label' => 'Security Fee',     'column' => 'FFS_SECURITY_AMT'],
        'medical_fee'      => ['label' => 'Medical Fee',      'column' => 'FFS_MEDICAL_AMT'],
        'activity_fee'     => ['label' => 'Activity Charges', 'column' => 'SC_ACTIVITY_AMT'],
        'hostel_fee'       => ['label' => 'Hostel Fee',       'column' => 'FFS_HOSTEL_AMT'],
        'credit_hour_fee'  => ['label' => 'Credit Hour Fee',  'column' => 'FFS_CREDHR_AMT'],
        'withholding_tax'  => ['label' => 'Withholding Tax',  'column' => 'SC_WITHHOLDING_TAX'],
        'sales_tax'        => ['label' => 'Sales Tax',        'column' => 'SC_SALES_TAX'],
        'refrigerator_fee' => ['label' => 'Refrigerator Fee', 'column' => 'SC_REFRIGERATOR'],
        'conditioner_fee'  => ['label' => 'Air Conditioner',  'column' => 'SC_CONDITIONER'],
        'electric_fee'     => ['label' => 'Electric Heater',  'column' => 'SC_ELECTRIC'],
        'repeat_amount'    => ['label' => 'Repeat Amount',    'column' => 'SC_REPEAT_AMOUNT_5'],
        'convocation_fee'  => ['label' => 'Convocation Fee',  'column' => 'SC_CONVOCATION_AMT'],
        'gym_fee'          => ['label' => 'Gym Fee',          'column' => 'SC_GYM_FEE'],
        'transport_fee'    => ['label' => 'Transport Fee',    'column' => 'SC_TRANSPORT_FEE'],
    ];

    /** Fields shown on this page for Hostel Fee challan. */
    private const DISPLAY_FEE_FIELDS = [
        'hostel_fee'   => ['label' => 'Hostel Fee', 'column' => 'FFS_HOSTEL_AMT'],
        'security_fee' => ['label' => 'Hostel Security Fee', 'column' => 'FFS_SECURITY_AMT'],
    ];

    private const SCHOLARSHIP_FEE_TYPES = [
        'Hostel_Fee',
    ];

    public function index()
    {
        $sessions = \App::call('App\\Http\\Controllers\\Grader\\AcademicSessionController@retrieve');

        // Prefer hostel scholarship types (Record Management / procedure use these).
        $scholarships = DB::table('scholarship_type')
            ->select('scholarship_id', 'scholarship_name')
            ->whereRaw("LOWER(TRIM(scholarship_desc)) = 'hostel'")
            ->orderBy('scholarship_name')
            ->get();

        if ($scholarships->isEmpty()) {
            $scholarships = DB::table('scholarship_type')
                ->select('scholarship_id', 'scholarship_name')
                ->orderBy('scholarship_name')
                ->get();
        }

        $feeFields = self::DISPLAY_FEE_FIELDS;

        return view('StudentFinancial.manualScholarshipChallan', compact(
            'sessions',
            'scholarships',
            'feeFields'
        ));
    }

    /**
     * Uses the same student-search pattern as GenerateSingleChallanController.
     */
    public function searchStudent(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'studentEnrollment' => ['required', 'string', 'max:100'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $studentInput = strtoupper(trim((string) $request->studentEnrollment));
        $isEnrollment = !is_numeric($studentInput);

        if ($isEnrollment) {
            $student = DB::table('students_apply_info as sai')
                ->leftJoin('students_personal_info as spi', 'sai.sai_spi_id', '=', 'spi.spi_id')
                ->leftJoin('admission_apply_info as aai', function ($join) {
                    $join->on('aai.aai_api_id', '=', 'sai.sai_api_id')
                        ->whereColumn('aai.cms_sai_enrollment_no', '=', 'sai.sai_enrollment_no');
                })
                ->leftJoin('schools as s', 'sai.sai_sdpo_school_id', '=', 's.school_id')
                ->leftJoin('departments as d', 'sai.sai_sdpo_dept_id', '=', 'd.dept_id')
                ->leftJoin('programs as p', 'sai.sai_sdpo_prog_id', '=', 'p.prog_id')
                ->whereRaw('LOWER(sai.sai_enrollment_no) = LOWER(?)', [$studentInput])
                ->select([
                    DB::raw('COALESCE(aai.aai_id, sai.sai_id) as aai_id'),
                    'sai.sai_enrollment_no as enrollment_no',
                    'spi.spi_name as student_name',
                    'spi.spi_fathername as father_name',
                    'spi.spi_guardian_cnic as guardian_cnic',
                    'spi.spi_email as email',
                    'spi.spi_mobile as mobile',
                    's.school_id',
                    's.school_name',
                    'd.dept_id',
                    'd.dept_name',
                    'p.prog_id',
                    'p.prog_name',
                    'sai.sai_student_status as student_status',
                ])
                ->first();
        } else {
            $student = DB::table('admission_apply_info as aai')
                ->leftJoin('admission_personal_info as api', 'aai.aai_api_id', '=', 'api.api_id')
                ->leftJoin('schools as s', 'aai.aai_sdpo_school_id', '=', 's.school_id')
                ->leftJoin('departments as d', 'aai.aai_sdpo_dept_id', '=', 'd.dept_id')
                ->leftJoin('programs as p', 'aai.aai_sdpo_prog_id', '=', 'p.prog_id')
                ->where('aai.aai_id', (int) $studentInput)
                ->select([
                    'aai.aai_id',
                    'aai.cms_sai_enrollment_no as enrollment_no',
                    'api.api_name as student_name',
                    'api.api_fathername as father_name',
                    'api.api_guardian_cnic as guardian_cnic',
                    'api.api_email as email',
                    'api.api_mobile as mobile',
                    's.school_id',
                    's.school_name',
                    'd.dept_id',
                    'd.dept_name',
                    'p.prog_id',
                    'p.prog_name',
                    'aai.aai_status as student_status',
                ])
                ->first();
        }

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid Enrollment Number or Applicant ID.',
            ], 404);
        }

        $student->student_name = $this->formatChallanPersonName($student->student_name ?? null) ?? '';
        $student->father_name = $this->formatChallanPersonName($student->father_name ?? null) ?? '';

        return response()->json([
            'success' => true,
            'student' => $student,
        ]);
    }

    /**
     * Route store by fee / challan type to a dedicated function.
     */
    public function store(Request $request)
    {
        $feeType = (string) $request->input('fee_type');

        if ($feeType === 'Hostel_other_utilities') {
            return $this->generateHostelUtilitiesChallan($request);
        }

        if (in_array($feeType, self::SCHOLARSHIP_FEE_TYPES, true)) {
            return $this->generateManualScholarshipChallan($request);
        }

        return response()->json([
            'success' => false,
            'errors' => [
                'fee_type' => ['Select a valid challan / fee type.'],
            ],
        ], 422);
    }

    /**
     * Hostel Fee challan + optional hostel scholarship/arrears.
     * Mirrors fin_new_std_hostel_chln_gen Hostel_Full_semster branch (native PHP).
     */
    private function generateManualScholarshipChallan(Request $request)
    {
        $validator = $this->validateManualScholarshipRequest($request);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $calc = $this->calculateManualScholarshipAmounts($request);
        $fees = $calc['fees'];
        $grossAmount = $calc['gross_amount'];
        $scholarshipBase = $calc['scholarship_base'];
        $scholarshipPercentage = $calc['scholarship_percentage'];
        $scholarshipAmount = $calc['scholarship_amount'];
        $arrearsAmount = $calc['arrears_amount'];
        $totalScholarshipDeduction = $calc['total_deduction'];
        $invoiceAmount = $calc['invoice_amount'];
        $summerFeeBasis = $calc['summer_fee_basis'];
        $enrolledCreditHours = $calc['enrolled_credit_hours'];

        $studentInput = strtoupper(trim((string) $request->student_input));
        $enrollmentNo = trim((string) $request->enrollment_no);
        if ($enrollmentNo === '') {
            $enrollmentNo = is_numeric($studentInput) ? null : $studentInput;
        }

        // Mirror fin_new_std_hostel_chln_gen (Hostel_Full_semster):
        // FSD_SCH_TYPE='hostel', SSD_CHALLAN_CATEGORY stays NULL (never Credit_hours/Full_semester).
        $hostelFeeType = 'Hostel_Full_semster';
        $mainScholarshipType = (int) $request->input('scholarship_type_id', 0);
        $arrearsScholarshipType = $arrearsAmount > 0
            ? (int) ($request->input('arrears_scholarship_type_id') ?: $mainScholarshipType)
            : null;
        $sameScholarshipHead = $arrearsAmount > 0
            && $mainScholarshipType > 0
            && $mainScholarshipType === $arrearsScholarshipType;

        $userRemarks = trim((string) $request->remarks);
        // Short note for SSHD_DETAILS (VARCHAR2(100)).
        $hostelScholarshipDetails = $this->buildHostelScholarshipDetailsNote(
            $scholarshipPercentage,
            $scholarshipAmount,
            $arrearsAmount,
            $invoiceAmount
        );

        $hostelCategory = $this->resolveHostelCategoryFromGender(
            (int) $request->st_aai_id,
            (string) $request->student_input
        );
        // Same as procedure session_type + sample row (e.g. regular).
        $sessionType = strtolower(trim((string) $request->input('sess_type', '')));
        $hostelDays = (int) $request->input('hostel_days', 0);

        // Columns/values match fin_new_std_hostel_chln_gen Hostel_Full_semster INSERT
        // (+ minimal fields needed for unpaid manual challans / reports).
        // Invoice = (hostel_fee - scholarship) + security - arrears
        $insert = [
            'SC_ENROLLMENT_NO'       => $enrollmentNo,
            'SC_SCHOOL_ID'           => (int) $request->st_school_id,
            'SC_DEPARTMENT_ID'       => (int) $request->st_dept_id,
            'SC_PROGRAM_ID'          => (int) $request->st_prog_id,
            'SC_SESSION_ID'          => (int) $request->session_id,
            'SC_STUDENT_STATUS'      => $this->resolveHostelChallanStudentType($request),
            'INVOICEAMOUNT'          => round($invoiceAmount, 2),
            'SC_CHALLAN_TYPE'        => 'Hostel_Fee',
            'SC_APP_ID'              => (int) $request->st_aai_id,
            'SC_DUE_DATE'            => Carbon::parse($request->due_date),
            'SC_VALID_DATE'          => Carbon::parse($request->valid_date),
            'FFS_HOSTEL_AMT'         => (float) $fees['hostel_fee'],
            'SC_HOSTEL_FEE_TYPE'     => $hostelFeeType,
            'SC_HOSTEL_FEE_DETAILS'  => 'Hostel Fee',
            'SC_HOSTEL_CATEGORY'     => $hostelCategory,
            'SC_SCHOLARSHIP_AMOUNT'  => $scholarshipAmount,
            'SC_SAI_STUDENT_STATUS'  => 'Active',
            'SC_STUDENTNAME'         => $this->formatChallanPersonName($request->student_name),
            'SC_FATHERNAME'          => $this->formatChallanPersonName($request->father_name),
            'SC_GUARDIAN_CNIC'       => $request->guardian_cnic,
            'SC_VISIBLE_DATE'        => Carbon::parse($request->visible_date ?: now()),
            'SC_INSTALLMENT_ALLOWED' => 'NO',
            'SC_HOSTEL_DAYS'         => $hostelDays,
            'SC_SESSION_TYPE'        => $sessionType !== '' ? $sessionType : null,
            'SC_SSD_ARREAR'          => $arrearsAmount,
            'FFS_SECURITY_AMT'       => (float) $fees['security_fee'],
            'SC_PRO_ENTRY_USERNAME'  => auth()->user()->username,
            'SC_REMARKS'             => $userRemarks !== '' ? $userRemarks : null,
            // Operational (sample/procedure unpaid + report visibility)
            'SC_CHALLAN_STATUS'      => 'UNPAID',
            'INVOICE_STATUS'         => 'E',
            'FLAG'                   => 'F',
            'SC_CHALLAN_SOURCE'      => 'MANUAL_SCHOLARSHIP_PAGE',
            'SC_ENTRY_USER_ID'       => auth()->id(),
            'SC_ENTRY_USER_NAME'     => auth()->user()->username,
            'CREATED_AT'             => now(),
            'UPDATED_AT'             => now(),
        ];

        DB::beginTransaction();

        try {
            $sequence = trim((string) env('STUDENT_CHALLAN_SEQUENCE', ''));
            if ($sequence !== '') {
                if (!preg_match('/^[A-Za-z0-9_$.]+$/', $sequence)) {
                    throw new \RuntimeException('Invalid STUDENT_CHALLAN_SEQUENCE value.');
                }

                $nextIdRow = DB::selectOne("SELECT {$sequence}.NEXTVAL AS ID FROM DUAL");
                $insert['ID'] = $nextIdRow->id ?? $nextIdRow->ID;
            }

            DB::table('student_challan')->insert($insert);

            $created = DB::table('student_challan')
                ->where('sc_app_id', (int) $request->st_aai_id)
                ->where('sc_session_id', (int) $request->session_id)
                ->where('sc_challan_source', 'MANUAL_SCHOLARSHIP_PAGE')
                ->where('sc_entry_user_name', auth()->user()->username)
                ->orderByDesc('created_at')
                ->first();

            if (!$created || empty($created->id)) {
                throw new \RuntimeException('Challan inserted but generated challan ID could not be retrieved.');
            }

            $challanReference = !empty($created->challan_no)
                ? (int) $created->challan_no
                : (int) $created->id;

            $historyRows = [];
            $applyScholarship = $mainScholarshipType > 0
                && $scholarshipPercentage > 0
                && $scholarshipAmount > 0;
            $applyArrears = $arrearsAmount > 0 && !empty($arrearsScholarshipType);

            if ($applyScholarship || $applyArrears) {
                // 1) SSHD first (procedure reads FIN_STUDENTS_SCHOLARSHIPS_HOSTEL_DETAILS).
                if ($applyScholarship) {
                    $this->persistHostelScholarshipDetails(
                        (int) $request->st_aai_id,
                        $enrollmentNo,
                        (int) $request->session_id,
                        $mainScholarshipType,
                        $scholarshipPercentage,
                        $scholarshipAmount,
                        (float) $fees['hostel_fee'],
                        $sameScholarshipHead ? $arrearsAmount : 0,
                        $hostelScholarshipDetails
                    );
                }

                if ($applyArrears && !$sameScholarshipHead) {
                    $this->persistHostelScholarshipDetails(
                        (int) $request->st_aai_id,
                        $enrollmentNo,
                        (int) $request->session_id,
                        (int) $arrearsScholarshipType,
                        0,
                        $arrearsAmount,
                        (float) $fees['hostel_fee'],
                        $arrearsAmount,
                        mb_substr(sprintf('Hostel arrears %.2f', $arrearsAmount), 0, 100)
                    );
                }

                // History rows mirror procedure loop over SSHD rows.
                if ($applyScholarship) {
                    $historyRows[] = [
                        'type' => $mainScholarshipType,
                        'percentage_fraction' => round($scholarshipPercentage / 100, 10),
                        'discount_amount' => $scholarshipAmount,
                    ];
                }

                if ($applyArrears && !$sameScholarshipHead) {
                    $historyRows[] = [
                        'type' => $arrearsScholarshipType,
                        'percentage_fraction' => 0,
                        'discount_amount' => $arrearsAmount,
                    ];
                }

                // 2) Discounts + display history (procedure: upsert FSD then insert history, CHALLAN_ID linked).
                foreach ($historyRows as $row) {
                    $this->upsertHostelScholarshipDiscount(
                        $enrollmentNo,
                        (int) $row['type'],
                        (float) $row['percentage_fraction'],
                        (float) $row['discount_amount'],
                        (int) $request->session_id,
                        (int) $request->st_aai_id,
                        $challanReference
                    );

                    // Same columns as fin_new_std_hostel_chln_gen history insert + CHALLAN_ID.
                    $this->insertHostelDisplayHistory([
                        'FSD_ENROLLMENT_NO'       => $enrollmentNo,
                        'FSD_SCHOLARSHIP_TYPE'    => $row['type'],
                        'FSD_DISCOUNT_PERCENTAGE' => $row['percentage_fraction'],
                        'FSD_DISCOUNTED_AMOUNT'   => $row['discount_amount'],
                        'FSD_SESION_ID'           => (int) $request->session_id,
                        'FSD_AAI_ID'              => (int) $request->st_aai_id,
                        'FSD_SCH_TYPE'            => 'hostel',
                        'CHALLAN_ID'              => (int) $created->id,
                        'ENTRY_DATETIME'          => now(),
                        'FSDH_VERSION'            => 1,
                        'FSDH_UPDATED_BY'         => auth()->user()->username,
                        'FSDH_UPDATED_AT'         => now(),
                        'FLAG'                    => 'F',
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Hostel fee challan created successfully.',
                'data' => [
                    'mode'                  => 'hostel_fee',
                    'challan_id'            => $created->id,
                    'hostel_fee'            => $fees['hostel_fee'],
                    'security_fee'          => $fees['security_fee'],
                    'gross_amount'          => $grossAmount,
                    'scholarship_base'      => $scholarshipBase,
                    'scholarship_amount'    => $scholarshipAmount,
                    'arrears_amount'        => $arrearsAmount,
                    'total_deduction'       => $totalScholarshipDeduction,
                    'invoice_amount'        => $invoiceAmount,
                    'hostel_days'           => (int) $request->input('hostel_days', 0),
                    'history_rows'          => count($historyRows),
                    'challan_type'          => 'Hostel_Fee',
                    'hostel_fee_type'       => $hostelFeeType,
                    'summer_fee_basis'      => $summerFeeBasis,
                    'enrolled_credit_hours' => $enrolledCreditHours,
                ],
            ]);
        } catch (Throwable $e) {
            DB::rollBack();
            report($e);

            return response()->json([
                'success' => false,
                'message' => 'Challan could not be created.',
                'error' => config('app.debug') ? $e->getMessage() : 'Database error occurred.',
            ], 500);
        }
    }

    /**
     * Hostel other utilities challan.
     * Logic ported from fin_new_std_hostel_chln_gen (Hostel_other_utilities branch).
     * Amounts come from checked utility inputs on this page (not procedure call).
     */
    private function generateHostelUtilitiesChallan(Request $request)
    {
        $validator = $this->validateHostelUtilitiesRequest($request);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $calc = $this->calculateHostelUtilitiesAmounts($request);

        try {
            DB::beginTransaction();

            $result = $this->persistHostelUtilitiesChallan($request, $calc);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Hostel utilities challan generated successfully.',
                'data' => [
                    'mode'             => 'hostel_utilities',
                    'fee_type'         => 'Hostel_other_utilities',
                    'challan_id'       => $result['challan_id'],
                    'refrigerator_fee' => $calc['refrigerator_fee'],
                    'conditioner_fee'  => $calc['conditioner_fee'],
                    'electric_fee'     => $calc['electric_fee'],
                    'gross_amount'     => $calc['invoice_amount'],
                    'invoice_amount'   => $calc['invoice_amount'],
                    'hostel_days'      => (int) $request->input('hostel_days', 0),
                ],
            ]);
        } catch (Throwable $e) {
            DB::rollBack();
            report($e);

            return response()->json([
                'success' => false,
                'message' => 'Hostel utilities challan could not be created.',
                'error' => config('app.debug') ? $e->getMessage() : 'Database error occurred.',
            ], 500);
        }
    }

    /**
     * Always inserts a new Hostel_other_utilities challan so separate utility
     * generations (e.g. AC then refrigerator) do not overwrite each other.
     */
    private function persistHostelUtilitiesChallan(Request $request, array $calc): array
    {
        $aaiId = (int) $request->st_aai_id;
        $sessionId = (int) $request->session_id;
        $feeType = 'Hostel_other_utilities';
        $sessionType = strtolower(trim((string) $request->input('sess_type', '')));
        $remarks = (string) ($request->input('remarks') ?: '');
        $entryUsername = auth()->user()->username;
        $studentType = $this->resolveHostelChallanStudentType($request);

        $enrollmentNo = trim((string) $request->enrollment_no);
        if ($enrollmentNo === '') {
            $studentInput = strtoupper(trim((string) $request->student_input));
            $enrollmentNo = is_numeric($studentInput) ? null : $studentInput;
        }

        $hostelCategory = $this->resolveHostelCategoryFromGender($aaiId, (string) $request->student_input);
        $hostelFeeDetails = $this->buildHostelUtilitiesFeeDetails($calc);
        $totalAmount = (float) $calc['invoice_amount'];

        $insert = [
            'SC_ENROLLMENT_NO'       => $enrollmentNo,
            'SC_SCHOOL_ID'           => (int) $request->st_school_id,
            'SC_DEPARTMENT_ID'       => (int) $request->st_dept_id,
            'SC_PROGRAM_ID'          => (int) $request->st_prog_id,
            'SC_SESSION_ID'          => $sessionId,
            'SC_STUDENT_STATUS'      => $studentType,
            'INVOICEAMOUNT'          => round($totalAmount, 2),
            'TOTAL_AMOUNT_PAYABLE'   => $totalAmount,
            'INVOICE_STATUS'         => 'E',
            'SC_CHALLAN_TYPE'        => 'Hostel_Fee',
            'SC_APP_ID'              => $aaiId,
            'SC_ISSU_DATE'           => Carbon::today(),
            'SC_DUE_DATE'            => Carbon::parse($request->due_date),
            'SC_VALID_DATE'          => Carbon::parse($request->valid_date),
            'SC_VALIDATE_DATE'       => Carbon::parse($request->valid_date),
            'FFS_HOSTEL_AMT'         => $totalAmount,
            'SC_HOSTEL_FEE_TYPE'     => $feeType,
            'SC_HOSTEL_FEE_DETAILS'  => $hostelFeeDetails,
            'SC_HOSTEL_CATEGORY'     => $hostelCategory,
            'SC_SCHOLARSHIP_AMOUNT'  => 0,
            'SC_SAI_STUDENT_STATUS'  => $request->student_status ?: 'Active',
            'SC_STUDENTNAME'         => $this->formatChallanPersonName($request->student_name),
            'SC_FATHERNAME'          => $this->formatChallanPersonName($request->father_name),
            'SC_GUARDIAN_CNIC'       => $request->guardian_cnic,
            'SC_VISIBLE_DATE'        => Carbon::parse($request->visible_date ?: now()),
            'SC_INSTALLMENT_ALLOWED' => 'NO',
            'SC_HOSTEL_DAYS'         => (int) $request->input('hostel_days', 0),
            'SC_SESSION_TYPE'        => $sessionType !== '' ? $sessionType : null,
            'SC_SSD_ARREAR'          => 0,
            'SC_REFRIGERATOR'        => $calc['refrigerator_fee'],
            'SC_CONDITIONER'         => $calc['conditioner_fee'],
            'SC_ELECTRIC'            => $calc['electric_fee'],
            'SC_PRO_ENTRY_USERNAME'  => $entryUsername,
            'SC_REMARKS'             => $remarks,
            'SC_CHALLAN_STATUS'      => 'UNPAID',
            'SC_ENTRY_USER_ID'       => auth()->id(),
            'SC_ENTRY_USER_NAME'     => $entryUsername,
            'SC_CHALLAN_SOURCE'      => 'MANUAL_SCHOLARSHIP_PAGE',
            'CREATED_AT'             => now(),
            'UPDATED_AT'             => now(),
            'FLAG'                   => 'F',
        ];

        $sequence = trim((string) env('STUDENT_CHALLAN_SEQUENCE', ''));
        if ($sequence !== '') {
            if (!preg_match('/^[A-Za-z0-9_$.]+$/', $sequence)) {
                throw new \RuntimeException('Invalid STUDENT_CHALLAN_SEQUENCE value.');
            }

            $nextIdRow = DB::selectOne("SELECT {$sequence}.NEXTVAL AS ID FROM DUAL");
            $insert['ID'] = $nextIdRow->id ?? $nextIdRow->ID;
        }

        DB::table('student_challan')->insert($insert);

        $created = DB::table('student_challan')
            ->where('sc_app_id', $aaiId)
            ->where('sc_session_id', $sessionId)
            ->where('sc_hostel_fee_type', $feeType)
            ->where('sc_challan_source', 'MANUAL_SCHOLARSHIP_PAGE')
            ->where('sc_pro_entry_username', $entryUsername)
            ->orderByDesc('created_at')
            ->first();

        if (!$created || empty($created->id)) {
            throw new \RuntimeException('Hostel utilities challan inserted but ID could not be retrieved.');
        }

        return [
            'challan_id' => $created->id,
        ];
    }

    /**
     * Hostel page hides Student Type. Prefer posted value; else map Hostel Student Type.
     */
    private function resolveHostelChallanStudentType(Request $request): string
    {
        $studentType = trim((string) $request->input('student_type', ''));
        if ($studentType !== '') {
            return $studentType;
        }

        $hostelStudentType = trim((string) $request->input('hostel_student_type', ''));
        if ($hostelStudentType === 'New_Admission') {
            return 'New Admission FALL';
        }

        if ($hostelStudentType === 'Existing') {
            return 'Existing';
        }

        return 'Existing';
    }

    /**
     * Same gender → hostel mapping as fin_new_std_hostel_chln_gen.
     */
    private function resolveHostelCategoryFromGender(int $aaiId, string $studentInput): string
    {
        $gender = null;
        $isEnrollment = !preg_match('/^\d+$/', trim($studentInput));

        if ($isEnrollment) {
            $row = DB::table('students_apply_info as sai')
                ->leftJoin('students_personal_info as spi', 'sai.sai_spi_id', '=', 'spi.spi_id')
                ->whereRaw('LOWER(sai.sai_enrollment_no) = LOWER(?)', [strtoupper(trim($studentInput))])
                ->select('spi.spi_gender as gender')
                ->first();
            $gender = $row->gender ?? null;
        } else {
            $row = DB::table('admission_apply_info as aai')
                ->leftJoin('admission_personal_info as api', 'aai.aai_api_id', '=', 'api.api_id')
                ->where('aai.aai_id', $aaiId)
                ->select('api.api_gender as gender')
                ->first();
            $gender = $row->gender ?? null;
        }

        $gender = strtoupper(trim((string) $gender));

        return in_array($gender, ['FEMALE', 'F'], true) ? 'GH' : 'BH';
    }

    /**
     * Challan name casing like existing procedure rows
     * (e.g. "Muhammad Arqam Zafar", not "hamna javed").
     */
    private function formatChallanPersonName($name): ?string
    {
        $name = trim((string) $name);
        if ($name === '') {
            return null;
        }

        return ucwords(strtolower($name));
    }

    private function buildHostelUtilitiesFeeDetails(array $calc): string
    {
        $details = 'Other utilities charges';

        if (!empty($calc['refrigerator_on'])) {
            $details .= ', Refrigerator charges';
        }
        if (!empty($calc['conditioner_on'])) {
            $details .= ', Conditioner charges';
        }
        if (!empty($calc['electric_on'])) {
            $details .= ', Electric heater charges';
        }

        return $details;
    }

    private function validateManualScholarshipRequest(Request $request)
    {
        $feeKeys = array_keys(self::FEE_FIELDS);

        $rules = [
            'student_input'                => ['required', 'string', 'max:100'],
            'st_aai_id'                    => ['required', 'integer'],
            'st_school_id'                 => ['required', 'integer'],
            'st_dept_id'                   => ['required', 'integer'],
            'st_prog_id'                   => ['required', 'integer'],
            'student_name'                 => ['required', 'string', 'max:500'],
            'father_name'                  => ['nullable', 'string', 'max:500'],
            'guardian_cnic'                => ['nullable', 'string', 'max:50'],
            'session_id'                   => ['required', 'integer'],
            // Hostel UI hides Student Type; value is synced / defaulted server-side.
            'student_type'                 => ['nullable', 'string', 'max:100'],
            'student_status'               => ['nullable', 'string', 'max:50'],
            'fee_type'                     => ['required', Rule::in(self::SCHOLARSHIP_FEE_TYPES)],
            'sess_type'                    => ['nullable', 'string', 'max:100'],
            'due_date'                     => ['required', 'date'],
            'valid_date'                   => ['required', 'date', 'after_or_equal:due_date'],
            'visible_date'                 => ['nullable', 'date'],
            'installment_allowed'          => ['nullable', Rule::in(['YES', 'NO'])],
            'no_of_installments'           => ['nullable', 'integer', 'min:1', 'max:5'],
            'hostel_fee'                   => ['required', 'integer', 'min:1'],
            'security_fee'                 => ['nullable', 'integer', 'min:0'],
            'hostel_days'                  => ['nullable', 'integer', 'min:0'],
            'scholarship_type_id'          => ['nullable', 'integer'],
            'scholarship_percentage'       => ['nullable', 'numeric', 'min:0', 'max:100'],
            'scholarship_columns'          => ['nullable', 'array'],
            'scholarship_columns.*'        => ['nullable', Rule::in($feeKeys)],
            'arrears_amount'               => ['nullable', 'numeric', 'min:0'],
            'arrears_scholarship_type_id'  => ['nullable', 'integer'],
            'remarks'                      => ['nullable', 'string', 'max:1200'],
        ];

        foreach ($feeKeys as $feeKey) {
            if (in_array($feeKey, ['hostel_fee', 'security_fee'], true)) {
                continue;
            }
            $rules[$feeKey] = ['nullable', 'numeric', 'min:0'];
        }

        $validator = Validator::make($request->all(), $rules);

        $validator->after(function ($validator) use ($request) {
            $hasType = $request->filled('scholarship_type_id');
            $percentage = (float) $request->input('scholarship_percentage', 0);
            $hostelFee = (float) $request->input('hostel_fee', 0);

            if ($hasType && $percentage <= 0) {
                $validator->errors()->add(
                    'scholarship_percentage',
                    'Enter scholarship percentage greater than zero, or clear scholarship type.'
                );
            }

            if ($percentage > 0 && !$hasType) {
                $validator->errors()->add(
                    'scholarship_type_id',
                    'Select hostel scholarship type when percentage is entered.'
                );
            }

            if ($hasType && $percentage > 0 && $hostelFee <= 0) {
                $validator->errors()->add(
                    'hostel_fee',
                    'Hostel fee is required to calculate hostel scholarship amount.'
                );
            }

            $arrearsAmount = (float) $request->input('arrears_amount', 0);
            if ($arrearsAmount > 0) {
                $arrearsType = $request->input('arrears_scholarship_type_id') ?: $request->input('scholarship_type_id');
                if (empty($arrearsType)) {
                    $validator->errors()->add(
                        'arrears_scholarship_type_id',
                        'Select scholarship type for arrears (or select main hostel scholarship type).'
                    );
                }
            }
        });

        return $validator;
    }

    private function validateHostelUtilitiesRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'student_input'           => ['required', 'string', 'max:100'],
            'st_aai_id'               => ['required', 'integer'],
            'st_school_id'            => ['required', 'integer'],
            'st_dept_id'              => ['required', 'integer'],
            'st_prog_id'              => ['required', 'integer'],
            'student_name'            => ['nullable', 'string', 'max:500'],
            'father_name'             => ['nullable', 'string', 'max:500'],
            'guardian_cnic'           => ['nullable', 'string', 'max:50'],
            'session_id'              => ['required', 'integer'],
            'student_status'          => ['nullable', 'string', 'max:50'],
            // Hostel UI hides Student Type; mapped from hostel_student_type when empty.
            'student_type'            => ['nullable', 'string', 'max:100'],
            'sess_type'               => ['nullable', 'string', 'max:100'],
            'hostel_student_type'     => ['nullable', Rule::in(['Existing', 'New_Admission'])],
            'hostel_days'             => ['nullable', 'integer', 'min:0'],
            'due_date'                => ['required', 'date'],
            'valid_date'              => ['required', 'date', 'after_or_equal:due_date'],
            'visible_date'            => ['nullable', 'date'],
            'remarks'                 => ['nullable', 'string', 'max:1200'],
            'refrigerator_checkbox'   => ['nullable', Rule::in(['on'])],
            'conditioner_checkbox'    => ['nullable', Rule::in(['on'])],
            'electric_checkbox'       => ['nullable', Rule::in(['on'])],
            'hostel_refrigerator_fee' => ['nullable', 'numeric', 'min:0'],
            'hostel_conditioner_fee'  => ['nullable', 'numeric', 'min:0'],
            'hostel_electric_fee'     => ['nullable', 'numeric', 'min:0'],
        ]);

        $validator->after(function ($validator) use ($request) {
            $fridgeOn = $request->input('refrigerator_checkbox') === 'on';
            $acOn = $request->input('conditioner_checkbox') === 'on';
            $heaterOn = $request->input('electric_checkbox') === 'on';

            if (!$fridgeOn && !$acOn && !$heaterOn) {
                $validator->errors()->add(
                    'hostel_utilities',
                    'Select at least one utility (Refrigerator, Conditioner, or Electric Heater).'
                );
            }

            if ($fridgeOn && (float) $request->input('hostel_refrigerator_fee', 0) <= 0) {
                $validator->errors()->add('hostel_refrigerator_fee', 'Enter refrigerator fee amount greater than zero.');
            }

            if ($acOn && (float) $request->input('hostel_conditioner_fee', 0) <= 0) {
                $validator->errors()->add('hostel_conditioner_fee', 'Enter air conditioner fee amount greater than zero.');
            }

            if ($heaterOn && (float) $request->input('hostel_electric_fee', 0) <= 0) {
                $validator->errors()->add('hostel_electric_fee', 'Enter electric heater fee amount greater than zero.');
            }
        });

        return $validator;
    }

    /**
     * Upsert fin_scholarship_discounts for hostel — same delete+insert as fin_new_std_hostel_chln_gen.
     */
    private function upsertHostelScholarshipDiscount(
        ?string $enrollmentNo,
        int $scholarshipType,
        float $percentageFraction,
        float $discountedAmount,
        int $sessionId,
        int $aaiId,
        int $challanReference
    ): void {
        DB::table('fin_scholarship_discounts')
            ->where('FSD_AAI_ID', $aaiId)
            ->where('fsd_sesion_id', $sessionId)
            ->where('fsd_scholarship_type', $scholarshipType)
            ->where('FSD_SCH_TYPE', 'hostel')
            ->delete();

        DB::table('fin_scholarship_discounts')->insert([
            'fsd_enrollment_no'       => $enrollmentNo,
            'fsd_scholarship_type'    => $scholarshipType,
            'fsd_discount_percentage' => $percentageFraction,
            'fsd_discounted_amount'   => $discountedAmount,
            'fsd_sesion_id'           => $sessionId,
            'FSD_AAI_ID'              => $aaiId,
            'FSD_SCH_TYPE'            => 'hostel',
            'CHALLAN_NO'              => $challanReference,
            'FSD_ENTRY_DATETIME'      => now(),
            'VERSION'                 => 1,
        ]);
    }

    /**
     * Clean SC_REMARKS for hostel fee challan.
     */
    private function buildHostelFeeRemarks(
        string $userRemarks,
        float $hostelFee,
        float $securityFee,
        float $scholarshipPercentage,
        float $scholarshipAmount,
        float $arrearsAmount,
        float $invoiceAmount
    ): string {
        $parts = [
            'Hostel Fee challan',
            sprintf('Hostel Fee: %s', number_format($hostelFee, 2, '.', '')),
            sprintf('Security: %s', number_format($securityFee, 2, '.', '')),
        ];

        if ($scholarshipAmount > 0) {
            $parts[] = sprintf(
                'Scholarship: %s%% (%s)',
                rtrim(rtrim(number_format($scholarshipPercentage, 4, '.', ''), '0'), '.'),
                number_format($scholarshipAmount, 2, '.', '')
            );
        }

        if ($arrearsAmount > 0) {
            $parts[] = sprintf('Arrears: %s', number_format($arrearsAmount, 2, '.', ''));
        }

        $parts[] = sprintf('Invoice: %s', number_format($invoiceAmount, 2, '.', ''));

        $autoRemarks = implode('; ', $parts) . '.';

        if ($userRemarks !== '') {
            $autoRemarks = $userRemarks . ' | ' . $autoRemarks;
        }

        return mb_substr($autoRemarks, 0, 2000);
    }

    /**
     * Short SSHD_DETAILS value (VARCHAR2(100)).
     */
    private function buildHostelScholarshipDetailsNote(
        float $scholarshipPercentage,
        float $scholarshipAmount,
        float $arrearsAmount,
        float $invoiceAmount
    ): string {
        $note = sprintf(
            'Hostel sch %s%% amt %s',
            rtrim(rtrim(number_format($scholarshipPercentage, 2, '.', ''), '0'), '.'),
            number_format($scholarshipAmount, 2, '.', '')
        );

        if ($arrearsAmount > 0) {
            $note .= sprintf('; arr %s', number_format($arrearsAmount, 2, '.', ''));
        }

        $note .= sprintf('; inv %s', number_format($invoiceAmount, 2, '.', ''));

        return mb_substr($note, 0, 100);
    }

    /**
     * Upsert FIN_STUDENTS_SCHOLARSHIPS_HOSTEL_DETAILS
     * (same column set as Student Record Management hostel scholarship).
     */
    private function persistHostelScholarshipDetails(
        int $aaiId,
        ?string $enrollmentNo,
        int $sessionId,
        int $scholarshipType,
        float $percentage,
        float $amount,
        float $hostelFee,
        float $arrear,
        string $details
    ): void {
        if ($scholarshipType <= 0) {
            return;
        }

        $existing = DB::table('fin_students_scholarships_hostel_details')
            ->where('SSHD_APP_ID', $aaiId)
            ->where('SSHD_SESS_ID', $sessionId)
            ->where('sshd_type', $scholarshipType)
            ->first();

        $username = auth()->user()->username;
        // Column BNUCMS.FIN_STUDENTS_SCHOLARSHIPS_HOSTEL_DETAILS.SSHD_DETAILS is VARCHAR2(100).
        $details = mb_substr($details, 0, 100);

        try {
            if (!empty($existing)) {
                // Update path matches StudentRecordController::persistScholarshipRow
                DB::table('fin_students_scholarships_hostel_details')
                    ->where('sshd_id', $existing->sshd_id)
                    ->update([
                        'sshd_type'              => $scholarshipType,
                        'sshd_percentage'        => $percentage,
                        'SSHD_AMOUNT'            => $amount,
                        'SSHD_HOSTEL_FEE'        => $hostelFee,
                        'SSHD_ARREAR'            => $arrear,
                        'sshd_details'           => $details,
                        'SSHD_STATUS'            => 'E',
                        'SSHD_entry_username'    => $username,
                        'SSHD_UPDATE_DATETIME' => Carbon::now('Asia/Karachi'),
                    ]);

                return;
            }

            // Insert path matches StudentRecordController (no UPDATE_DATETIME on insert).
            DB::table('fin_students_scholarships_hostel_details')->insert([
                'sshd_sai_enrollment_no' => $enrollmentNo ? strtoupper($enrollmentNo) : null,
                'sshd_type'              => $scholarshipType,
                'sshd_percentage'        => $percentage,
                'SSHD_AMOUNT'            => $amount,
                'SSHD_HOSTEL_FEE'        => $hostelFee,
                'SSHD_ARREAR'            => $arrear,
                'SSHD_APP_ID'            => $aaiId,
                'SSHD_STATUS'            => 'E',
                'SSHD_DETAILS'           => $details,
                'SSHD_SESS_ID'           => $sessionId,
                'SSHD_entry_username'    => $username,
                'SSHD_ENTRY_DATETIME'    => Carbon::now('Asia/Karachi'),
            ]);
        } catch (Throwable $e) {
            $message = $e->getMessage();

            // ORA-04098: audit trigger is INVALID in Oracle — DBA must recompile it.
            if (stripos($message, 'ORA-04098') !== false
                || stripos($message, 'FIN_STUDENTS_SCHOLARSHIPS_HOSTEL_DETAILS_AUD_TRG') !== false
            ) {
                throw new \RuntimeException(
                    'Hostel scholarship detail could not be saved because Oracle trigger '
                    . 'BNUCMS.FIN_STUDENTS_SCHOLARSHIPS_HOSTEL_DETAILS_AUD_TRG is INVALID. '
                    . 'Ask DBA to recompile it, e.g. '
                    . 'ALTER TRIGGER BNUCMS.FIN_STUDENTS_SCHOLARSHIPS_HOSTEL_DETAILS_AUD_TRG COMPILE; '
                    . 'Original error: ' . $message
                );
            }

            throw $e;
        }
    }

    /**
     * Hostel Fee challan calculation (procedure formula):
     * scholarship_base = hostel_fee
     * scholarship = hostel_fee * percentage
     * invoice = (hostel_fee - scholarship) + security_fee - arrears
     */
    private function calculateManualScholarshipAmounts(Request $request): array
    {
        $fees = [];
        foreach (self::FEE_FIELDS as $input => $definition) {
            $fees[$input] = round((float) $request->input($input, 0), 2);
        }

        $feeType = (string) $request->fee_type;
        $hostelFee = $fees['hostel_fee'];
        $securityFee = $fees['security_fee'];
        $grossAmount = round($hostelFee + $securityFee, 2);

        $hasScholarship = $request->filled('scholarship_type_id')
            && (float) $request->input('scholarship_percentage', 0) > 0;

        $scholarshipPercentage = $hasScholarship
            ? round((float) $request->scholarship_percentage, 4)
            : 0;
        $scholarshipBase = $hasScholarship ? $hostelFee : 0;
        $scholarshipAmount = round($scholarshipBase * ($scholarshipPercentage / 100), 2);
        $arrearsAmount = round((float) $request->input('arrears_amount', 0), 2);
        $totalScholarshipDeduction = round($scholarshipAmount + $arrearsAmount, 2);
        $invoiceAmount = max(
            0,
            round(($hostelFee - $scholarshipAmount) + $securityFee - $arrearsAmount, 2)
        );

        return [
            'fees'                   => $fees,
            'gross_amount'           => $grossAmount,
            'scholarship_base'       => $scholarshipBase,
            'scholarship_percentage' => $scholarshipPercentage,
            'scholarship_amount'     => $scholarshipAmount,
            'arrears_amount'         => $arrearsAmount,
            'total_deduction'        => $totalScholarshipDeduction,
            'invoice_amount'         => $invoiceAmount,
            'summer_fee_basis'       => null,
            'enrolled_credit_hours'  => 0,
            'student_challan_type'   => $feeType,
            'is_credit_hour_based'   => false,
        ];
    }

    /**
     * Hostel utilities calculation:
     * only checked utilities are included; invoice = sum of those amounts.
     */
    private function calculateHostelUtilitiesAmounts(Request $request): array
    {
        $refrigeratorOn = $request->input('refrigerator_checkbox') === 'on';
        $conditionerOn = $request->input('conditioner_checkbox') === 'on';
        $electricOn = $request->input('electric_checkbox') === 'on';

        $refrigeratorFee = $refrigeratorOn
            ? round((float) $request->input('hostel_refrigerator_fee', 0), 2)
            : 0;
        $conditionerFee = $conditionerOn
            ? round((float) $request->input('hostel_conditioner_fee', 0), 2)
            : 0;
        $electricFee = $electricOn
            ? round((float) $request->input('hostel_electric_fee', 0), 2)
            : 0;

        $invoiceAmount = round($refrigeratorFee + $conditionerFee + $electricFee, 2);

        return [
            'refrigerator_on'  => $refrigeratorOn,
            'conditioner_on'   => $conditionerOn,
            'electric_on'      => $electricOn,
            'refrigerator_fee' => $refrigeratorFee,
            'conditioner_fee'  => $conditionerFee,
            'electric_fee'     => $electricFee,
            'gross_amount'     => $invoiceAmount,
            'invoice_amount'   => $invoiceAmount,
        ];
    }

    /**
     * Hostel scholarship display history.
     * Matches fin_new_std_hostel_chln_gen:
     *   INSERT (... FSD_SCH_TYPE) VALUES (..., 'hostel')
     *   then link CHALLAN_ID.
     * Never writes SSD_CHALLAN_CATEGORY (Credit_hours / Full_semester).
     */
    private function insertHostelDisplayHistory(array $historyData): void
    {
        $historyData['FSD_SCH_TYPE'] = 'hostel';
        // Procedure does not set this column — omit so Oracle stores NULL.
        unset($historyData['SSD_CHALLAN_CATEGORY']);

        $historySequence = trim((string) env('FIN_SCHOLARSHIP_HISTORY_SEQUENCE', ''));

        if ($historySequence !== '') {
            if (!preg_match('/^[A-Za-z0-9_$.]+$/', $historySequence)) {
                throw new \RuntimeException('Invalid FIN_SCHOLARSHIP_HISTORY_SEQUENCE value.');
            }

            $nextHistoryId = DB::selectOne("SELECT {$historySequence}.NEXTVAL AS ID FROM DUAL");
            $historyData['ID'] = $nextHistoryId->id ?? $nextHistoryId->ID;
        }

        DB::table('FIN_SCHOLARSHIP_DISPLAY_HISTORY')->insert($historyData);
    }
}
