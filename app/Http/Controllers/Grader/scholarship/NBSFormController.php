<?php

namespace App\Http\Controllers\Grader\Scholarship;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use App\Http\Controllers\StudentFinancial\GenerateSingleChallanController;
use Illuminate\Support\Facades\Schema; // ✅ Add this line


class NBSFormController extends Controller
{
    protected $AdmissionApprovalUsernames = ['sidra.tanveer', 'maheen.james'];
    protected $RoApprovalUsernames = ['hassan.farooq', 'talha.sarfraz'];

    public function index()
    {
        // This is for RO Approval (Registrar Office)
        return view('grader.scholarship.RO_nbs', [
            'isAdmissionApprovalUser' => false,
        ]);
    }

    public function saveFinalPercentage(Request $request)
    {
        try {
            $request->validate([
                'applicant_id' => 'required',
                'percentage' => 'required|numeric|min:0|max:100',
                'remarks' => 'nullable|string'
            ]);

            DB::table('scholarship_percentage_details')->updateOrInsert(
                ['AAI_ID' => $request->applicant_id],
                [
                    'final_decision' => $request->percentage,
                    'final_decision_remarks' => $request->remarks,
                    'updated_at' => now(),
                    'created_at' => now()
                ]
            );

            return response()->json([
                'status' => true,
                'message' => 'Saved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function index_admission()
    {
        // This is for Admission Approval
        return view('grader.scholarship.Admission_nbs', [
            'isAdmissionApprovalUser' => true,
        ]);
    }
    public function hod_approval()
    {


        return view('grader.scholarship.HOD_approval_nbs');
    }

    public function getNBSFormDataHodApproval(Request $request)
    {

        $school_id = $request->input('ad_school_id');
        $student_type = $request->input('student_type');
        $admissionSch = $request->input('admission_sch');
        $application_sess_id = $request->input('ad_sess_id');
        $username = auth()->user()->username;

        // dd($student_type);
        $sqlNeedBased = "SELECT

        (SELECT percentage FROM scholarship_percentage_details WHERE aai_id = applicant_scholarship_form.aai_id and entry_username = '$username') as percentage,


            'Ref-' || applicant_scholarship_form.id as ref_id,
             applicant_scholarship_form.id as form_id,
             
             applicant_scholarship_form.*
        FROM
            applicant_scholarship_form
            LEFT JOIN admission_apply_info ON admission_apply_info.aai_id = applicant_scholarship_form.aai_id
            LEFT JOIN admission_schedules ON admission_sch_id = aai_admission_sch_id
            --LEFT JOIN scholarship_percentage_details ON scholarship_percentage_details.aai_id = applicant_scholarship_form.aai_id
        WHERE
            admin_ro_approval = 'Approved' 
            AND applicant_scholarship_form.sess_id = '$application_sess_id'
      
     ";
        //  echo $sqlNeedBased;exit;

        // Correct logic for student_type
        if ($student_type == 'existing') {
            $sqlNeedBased .= " AND sai_enrollment_no IS NOT NULL";
        } elseif ($student_type == 'new_applicants') {
            $sqlNeedBased .= " AND sai_enrollment_no IS NULL";
        }

        // Correct logic for admission schedule round
        if ($admissionSch == '1') {
            $sqlNeedBased .= " AND admission_sch_round = 1";
        } elseif ($admissionSch == '2') {
            $sqlNeedBased .= " AND admission_sch_round = 2";
        } elseif ($admissionSch == '3') {
            $sqlNeedBased .= " AND admission_sch_round = 3";
        }


        //  echo $sqlNeedBased;exit;
        $data = DB::select($sqlNeedBased);

        return response()->json([
            'status' => true,
            'data' => $data
        ]);
    }


    public function getNBSFormData(Request $request)
    {
        $request_type = $request->input('request_type');

        $application_sess_id = $request->input('application_sess_id');

        // Fill missing Ref/order_number for this session before listing
        $this->assignMissingOrderNumbers($application_sess_id);


        if ($request_type === 'ADMISSION') {
            return $this->getNBSFormDataAdmission($request);
        } else {
            return $this->getNBSFormDataRo($request);
        }
    }

    /**
     * Assign order_number (Ref) for Selected applicants in a session
     * only when order_number is still null (avoids Ref-N/A in the UI).
     */
    private function assignMissingOrderNumbers($sessId)
    {
        if (empty($sessId)) {
            return;
        }

        $nullCount = DB::selectOne("
            SELECT COUNT(*) AS cnt
            FROM APPLICANT_SCHOLARSHIP_FORM ASF
            JOIN admission_apply_info AAI ON ASF.aai_id = AAI.aai_id
            WHERE ASF.sess_id = ?
              AND ASF.sai_enrollment_no IS NULL
              AND ASF.order_number IS NULL
              AND AAI.aai_status = 'Selected'
        ", [$sessId]);

        if (!$nullCount || (int) $nullCount->cnt === 0) {
            return;
        }

        DB::statement("
            MERGE INTO APPLICANT_SCHOLARSHIP_FORM tgt
            USING (
              SELECT ASF.ROWID AS rid,
                     ROW_NUMBER() OVER (ORDER BY AAI.aai_id) + NVL((
                       SELECT MAX(order_number)
                       FROM APPLICANT_SCHOLARSHIP_FORM
                       WHERE sess_id = ?
                     ), 0) AS rn
              FROM APPLICANT_SCHOLARSHIP_FORM ASF
              JOIN admission_apply_info AAI ON ASF.aai_id = AAI.aai_id
              WHERE ASF.sess_id = ?
                AND ASF.sai_enrollment_no IS NULL
                AND ASF.order_number IS NULL
                AND AAI.aai_status = 'Selected'
            ) src
            ON (tgt.ROWID = src.rid)
            WHEN MATCHED THEN
              UPDATE SET tgt.order_number = src.rn
        ", [$sessId, $sessId]);
    }

    private function getNBSFormDataAdmission(Request $request)
    {
        $ad_sess_id = $request->input('ad_sess_id');
        $ad_school_id = $request->input('ad_school_id');
        $admission_sch = $request->input('admission_sch');
        $application_sess_id = $request->input('application_sess_id');

        // Admission Logic: New applicants (no enrollment number yet) and status is Selected
        $where = " WHERE asf.sai_enrollment_no IS NULL AND aai.aai_status = 'Selected'";
        $order_by = " ORDER BY asf.order_number ASC";

        $bindings = [];

        if ($ad_sess_id) {
            $where .= " AND aai.aai_sdpo_sess_id = ?";
            $bindings[] = $ad_sess_id;
        }
        if ($application_sess_id) {
            $where .= " AND asf.sess_id = ?";
            $bindings[] = $application_sess_id;

            // Logic Explanation:
            // Before Session 166 (Spring 2026), the scholarship form process was not step-wise,
            // so there was no need to filter by 'Final Submit'.
            // Starting from Session 166, the form is step-wise. Thus, we specifically check 
            // `FINAL_SUBMIT = 1` to retrieve only those applicants who have completed and 
            // submitted the entire form.
            if ($application_sess_id >= 166) {
                $where .= " AND asf.FINAL_SUBMIT = 1";
            }
        }
        if ($ad_school_id) {
            $where .= " AND aai.aai_sdpo_school_id = ?";
            $bindings[] = $ad_school_id;
        }
        if ($admission_sch) {
            $where .= " AND asd.ADMISSION_SCH_ROUND = ?";
            $bindings[] = $admission_sch;
        }

        $sqlNeedBased = "
            SELECT 
                asf.id AS form_id,
                asf.id AS form_id_order,
                asf.order_number AS order_number,
                asf.sai_enrollment_no AS enrollment_no,
                aai.aai_id,
                asf.father_name,
                asf.student_name,
                cgpa(asf.sai_enrollment_no) AS cgpa,
                asf.sess_id AS sess_id,
                asf.admin_ro_approval,
                asf.already_availed_nbs_scholarship,
                 (
                SELECT MAX(spd.final_decision)
                FROM scholarship_percentage_details spd
                WHERE spd.aai_id = aai.aai_id
                ) AS final_decision,
                (
                    SELECT MAX(semester_no)
                    FROM (
                        SELECT 
                            ROW_NUMBER() OVER (ORDER BY gh.sc_co_sess_id) AS semester_no
                        FROM student_courses gh
                        JOIN academic_sessions sess
                            ON gh.sc_co_sess_id = sess.sess_id
                        WHERE gh.sc_sai_enrollment_no = asf.sai_enrollment_no
                        AND (sess.sess_name LIKE 'Fall%' OR sess.sess_name LIKE 'Spring%')
                        GROUP BY gh.sc_co_sess_id, sess.sess_name
                    )
                ) AS semester_number,
                s.school_name,
                p.prog_name,
                api.api_email,
                NVL(avg_electricity_bill,0) 
                + NVL(avg_gas_bill,0) 
                + NVL(avg_ptcl_bill,0) 
                + NVL(avg_grocery_expense,0) 
                + NVL(avg_medical_expense,0) AS total_expenses,
                round(TOTAL_INCOME/12,0) as monthly_income
            FROM applicant_scholarship_form asf
            LEFT JOIN admission_apply_info aai ON asf.aai_id = aai.aai_id
            LEFT JOIN ADMISSION_SCHEDULES asd ON aai.aai_admission_sch_id = asd.admission_sch_id
            LEFT JOIN academic_sessions acs ON asf.sess_id = acs.sess_id
            LEFT JOIN admission_personal_info api ON aai.aai_api_id = api.api_id
            LEFT JOIN schools s ON aai.aai_sdpo_school_id = s.school_id
            LEFT JOIN programs p ON aai.aai_sdpo_prog_id = p.prog_id
            $where
            $order_by
        ";

        $data = DB::select($sqlNeedBased, $bindings);

        return response()->json([
            'status' => true,
            'data' => $data
        ]);
    }

    private function getNBSFormDataRo(Request $request)
    {
        $ad_sess_id = $request->input('ad_sess_id');
        $ad_school_id = $request->input('ad_school_id');
        $admission_sch = $request->input('admission_sch');
        $application_sess_id = $request->input('application_sess_id');
        $already_availed_nbs = $request->input('already_availed_nbs');

        // RO Logic: Existing students (have enrollment number)
        $where = " WHERE asf.sai_enrollment_no IS NOT NULL";
        $order_by = " ORDER BY asf.id ASC";

        $bindings = [];

        if ($ad_sess_id) {
            $where .= " AND aai.aai_sdpo_sess_id = ?";
            $bindings[] = $ad_sess_id;
        }
        if ($application_sess_id) {
            $where .= " AND asf.sess_id = ?";
            $bindings[] = $application_sess_id;

            // Logic Explanation:
            // Before Session 166 (Spring 2026), the scholarship form process was not step-wise,
            // so there was no need to filter by 'Final Submit'.
            // Starting from Session 166, the form is step-wise. Thus, we specifically check 
            // `final_submit = 1` to retrieve only those applicants who have completed and 
            // submitted the entire form.
            if ($application_sess_id >= 166) {
                $where .= " AND asf.final_submit = 1";
            }
        }
        if ($ad_school_id) {
            $where .= " AND aai.aai_sdpo_school_id = ?";
            $bindings[] = $ad_school_id;
        }
        if ($admission_sch) {
            $where .= " AND asd.ADMISSION_SCH_ROUND = ?";
            $bindings[] = $admission_sch;
        }
        if ($already_availed_nbs) {
            $where .= " AND asf.already_availed_nbs_scholarship = ?";
            $bindings[] = $already_availed_nbs;
        }

        $sqlNeedBased = "
            SELECT 
                asf.id AS form_id,
                asf.id AS form_id_order,
                asf.order_number AS order_number,
                asf.sai_enrollment_no AS enrollment_no,
                aai.aai_id,
                asf.father_name,
                asf.student_name,
                cgpa(asf.sai_enrollment_no) AS cgpa,
                asf.sess_id AS sess_id,
                asf.admin_ro_approval,
                asf.already_availed_nbs_scholarship,
                (
                    SELECT MAX(semester_no)
                    FROM (
                        SELECT 
                            ROW_NUMBER() OVER (ORDER BY gh.sc_co_sess_id) AS semester_no
                        FROM student_courses gh
                        JOIN academic_sessions sess
                            ON gh.sc_co_sess_id = sess.sess_id
                        WHERE gh.sc_sai_enrollment_no = asf.sai_enrollment_no
                        AND (sess.sess_name LIKE 'Fall%' OR sess.sess_name LIKE 'Spring%')
                        GROUP BY gh.sc_co_sess_id, sess.sess_name
                    )
                ) AS semester_number,
                s.school_name,
                p.prog_name,
                api.api_email,
                NVL(avg_electricity_bill,0) 
                + NVL(avg_gas_bill,0) 
                + NVL(avg_ptcl_bill,0) 
                + NVL(avg_grocery_expense,0) 
                + NVL(avg_medical_expense,0) AS total_expenses,
                round(TOTAL_INCOME/12,0) as monthly_income
            FROM applicant_scholarship_form asf
            LEFT JOIN admission_apply_info aai ON asf.aai_id = aai.aai_id
            LEFT JOIN ADMISSION_SCHEDULES asd ON aai.aai_admission_sch_id = asd.admission_sch_id
            LEFT JOIN academic_sessions acs ON asf.sess_id = acs.sess_id
            LEFT JOIN admission_personal_info api ON aai.aai_api_id = api.api_id
            LEFT JOIN schools s ON aai.aai_sdpo_school_id = s.school_id
            LEFT JOIN programs p ON aai.aai_sdpo_prog_id = p.prog_id
            $where
            $order_by
        ";

        $data = DB::select($sqlNeedBased, $bindings);

        return response()->json([
            'status' => true,
            'data' => $data
        ]);
    }


    public function approveScholarshipApplicant(Request $request)
    {
        $form_id = $request->input('form_id');
        $remarks = $request->input('remarks');
        $username = auth()->user()->username;

        if (empty($form_id)) {
            return response()->json(['status' => false, 'message' => 'Form ID is required.']);
        }

        $updateData = [
            'admin_ro_approval' => 'Approved',
            'remarks_by_admin_ro_approval' => $remarks,
            'approved_by_admin_ro_approval' => $username,
            'admin_ro_approved_at' => Carbon::now('Asia/Karachi'),
        ];


        $updated = DB::table('applicant_scholarship_form')
            ->where('id', $form_id)
            ->update($updateData);

        if ($updated === 0) {
            return response()->json(['status' => false, 'message' => 'Scholarship approval failed.']);
        }

        return response()->json(['status' => true, 'message' => 'Scholarship approval updated successfully.']);
    }

    public function disapproveScholarshipApplicant(Request $request)
    {
        $form_id = $request->input('form_id');
        $remarks = $request->input('remarks');
        $username = auth()->user()->username;

        if (empty($form_id)) {
            return response()->json(['status' => false, 'message' => 'Form ID is required.']);
        }

        $updateData = [
            'admin_ro_approval' => 'Disapproved',
            'remarks_by_admin_ro_approval' => $remarks,
            'approved_by_admin_ro_approval' => $username,
            'admin_ro_approved_at' => Carbon::now('Asia/Karachi'),
        ];


        $updated = DB::table('applicant_scholarship_form')
            ->where('id', $form_id)
            ->update($updateData);

        if ($updated === 0) {
            return response()->json(['status' => false, 'message' => 'Scholarship disapproval failed.']);
        }

        return response()->json(['status' => true, 'message' => 'Scholarship disapproval updated successfully.']);
    }


    public function getScholarshipRemarks(Request $request)
    {
        $spdId = $request->input('spd_id');
        $remarks = DB::table('scholarship_percentage_details')->where('id', $spdId)->first();
        return response()->json($remarks);
    }


    public function hodApproveScholarship(Request $request)
    {
        // dd($request->all());
        $validated = $request->validate([
            'applicant_id' => 'required|numeric',
            'percentage' => 'required|string|max:55',
            //'hod_remarks' => 'nullable|string|max:600',
        ]);

        $applicantId = $validated['applicant_id'];
        $username = auth()->user()->username;

        // Check if this user has already entered data for this applicant
        $existing = DB::table('scholarship_percentage_details')
            ->where('aai_id', $applicantId)
            ->where('entry_username', $username)
            ->first();
        // dd($existing);
        if ($existing) {
            // Update the existing record for this user and applicant
            DB::table('scholarship_percentage_details')
                ->where('id', $existing->id)
                ->update([
                    'percentage' => $validated['percentage'],
                    // 'remarks' => $validated['hod_remarks'],
                    'sess_id' => $request->get('sessionId'),
                    'updated_at' => Carbon::now(),
                ]);
        } else {
            // Insert a new record for this user and applicant
            DB::table('scholarship_percentage_details')->insert([
                'aai_id' => $applicantId,
                'percentage' => $validated['percentage'],
                // 'remarks' => $validated['hod_remarks'],
                'sess_id' => $request->get('sessionId'),
                'entry_username' => $username,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }

        return response()->json(['status' => true, 'message' => 'Data saved successfully.']);

        $spdId = $request->input('spd_id');
        $percentage = $request->input('percentage');
        $remarks = $request->input('hod_remarks');
        $action = $request->input('action'); // approve or disapprove
        $username = auth()->user()->username;

        if (!$spdId || !$action) {
            return response()->json(['status' => false, 'message' => 'Invalid request parameters.'], 400);
        }

        $updateData = [
            'hod_remarks' => $remarks,
            'hod_approved_at' => Carbon::now()->setTimezone('Asia/Karachi')
        ];

        if ($action === 'approve') {
            $updateData['percentage'] = $percentage;
            $updateData['hod_approved'] = 'Yes';
        } elseif ($action === 'disapprove') {
            $updateData['percentage'] = ''; // or NULL if required
            $updateData['hod_approved'] = 'No';
        } else {
            return response()->json(['status' => false, 'message' => 'Invalid action.'], 400);
        }

        DB::table('scholarship_percentage_details')
            ->where('id', $spdId)
            ->update($updateData);

        return response()->json([
            'status' => true,
            'message' => $action === 'approve' ? 'HOD approval successful.' : 'Scholarship disapproved successfully.'
        ]);
    }

    public function getApplicantDocuments(Request $request)
    {
        $applicantId = $request->input('applicant_id');
        $enrollmentNo = $request->input('enrollment_no');

        if (!$applicantId) {
            return response()->json([
                'status' => false,
                'message' => 'Applicant ID is required'
            ], 400);
        }

        $aaiApiId = DB::table('admission_apply_info')
            ->select('aai_api_id')
            ->where('aai_id', $applicantId)
            ->first();

        if (!$aaiApiId) {
            return response()->json([
                'status' => false,
                'message' => 'Applicant not found'
            ], 404);
        }

        try {
            // Get documents for the applicant
            $query = DB::table('admission_document')
                ->select('*')
                ->where('ad_api_id', $aaiApiId->aai_api_id)
                ->where('ad_type', 'like', '%needbased_form_documents%');

            if ($enrollmentNo && $enrollmentNo != 'null') {
                $query->where('cms_enrollment_no', $enrollmentNo);
            }

            $admissionDocuments = $query->get();

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error fetching documents: ' . $e->getMessage()
            ], 500);
        }

        return response()->json([
            'status' => true,
            'data' => $admissionDocuments
        ]);
    }


    public function updateProfileIndex(Request $request)
    {
        $applicantId = $request->input('applicant_id');
        $enrollmentNo = $request->input('enrollment_no');
        $sessionId = $request->input('session_id');

        // Fetch data
        $applicantData = DB::table('applicant_scholarship_form')
            ->where('aai_id', $applicantId)
            ->first();

        // dd($applicantData);
        $resaqlSiblings = DB::select("SELECT * FROM needbase_form_siblings WHERE aai_id = ?", [$applicantId]);

        $ressqlEmployed = DB::select("SELECT * FROM needbase_form_employed WHERE aai_id = ?", [$applicantId]);

        $ressqlunEmployed = DB::select("SELECT * FROM needbase_form_unemployed WHERE aai_id = ?", [$applicantId]);

        $ressqltransport = DB::select("SELECT * FROM needbase_form_transport WHERE aai_id = ?", [$applicantId]);

        // Pass everything to the view
        return view('grader.scholarship.profile_index', compact(
            'applicantId',
            'enrollmentNo',
            'sessionId',
            'applicantData',
            'resaqlSiblings',
            'ressqlEmployed',
            'ressqlunEmployed',
            'ressqltransport'
        ));
    }



    public function updateSiblingsData(Request $request)
    {
        $siblingIds = $request->input('sibling_id');

        if (!$siblingIds || !is_array($siblingIds)) {
            return response()->json([
                'status' => false,
                'message' => 'No siblings data provided'
            ]);
        }

        // Get all column names from the sibling table
        $columns = Schema::getColumnListing('needbase_form_siblings');

        foreach ($siblingIds as $index => $id) {
            $data = [
                'sibling_name' => $request->input('sibling_name')[$index] ?? null,
                'sibling_age' => $request->input('sibling_age')[$index] ?? null,
                'sibling_marital_status' => $request->input('sibling_marital_status')[$index] ?? null,
                'school_college_name' => $request->input('school_college_name')[$index] ?? null,
                'annual_fee' => $request->input('annual_fee')[$index] ?? null,
                'annual_scholarship' => $request->input('annual_scholarship')[$index] ?? null,
                'annual_parents_contribution' => $request->input('annual_parents_contribution')[$index] ?? null,
            ];

            // Only keep keys that match actual columns
            $filtered = collect($data)
                ->only($columns)
                ->toArray();

            // Update record
            DB::table('needbase_form_siblings')
                ->where('id', $id)
                ->update($filtered);
        }

        return response()->json([
            'status' => true,
            'message' => 'Siblings data updated successfully'
        ]);
    }




    public function updateProfileData(Request $request)
    {
        $formId = $request->input('id');

        if (!$formId) {
            return response()->json([
                'status' => false,
                'message' => 'Form ID is missing'
            ]);
        }

        // Get all column names from the table
        $columns = Schema::getColumnListing('applicant_scholarship_form');

        // Filter request data to only include valid columns
        $filteredData = collect($request->all())
            ->only($columns)
            ->except(['id']) // avoid changing primary key
            ->toArray();

        if (empty($filteredData)) {
            return response()->json([
                'status' => false,
                'message' => 'No valid fields to update'
            ]);
        }

        // ✅ Build raw SQL for debugging
        $setPart = collect($filteredData)
            ->map(function ($value, $key) {
                return "`$key` = '" . addslashes($value) . "'";
            })
            ->implode(', ');

        $sql = "UPDATE `applicant_scholarship_form` SET {$setPart} WHERE `id` = {$formId};";

        // ✅ Echo or Log the query (for debugging)
        // echo $sql; // Uncomment if you want to show in browser

        // ✅ Perform the actual update
        DB::table('applicant_scholarship_form')
            ->where('id', $formId)
            ->update($filteredData);

        return response()->json([
            'status' => true,
            'message' => 'Profile data updated successfully'
        ]);
    }






    public function updateEmployedData(Request $request)
    {
        $employedIds = $request->input('employed_id');

        if (!$employedIds || !is_array($employedIds)) {
            return response()->json([
                'status' => false,
                'message' => 'No employed data provided'
            ]);
        }

        // Get actual DB column names from the employed table
        $columns = Schema::getColumnListing('needbase_form_employed');

        foreach ($employedIds as $index => $id) {
            $data = [
                'employee_consent' => $request->input('employee_consent')[$index] ?? null,
                'employed_name' => $request->input('employed_name')[$index] ?? null,
                'employed_age' => $request->input('employed_age')[$index] ?? null,
                'employeed_marital_status' => $request->input('employeed_marital_status')[$index] ?? null,
                'profession' => $request->input('profession')[$index] ?? null,
                'annual_income' => $request->input('annual_income')[$index] ?? null,
                'annual_contribution' => $request->input('annual_contribution')[$index] ?? null,
            ];

            $filtered = collect($data)->only($columns)->toArray();

            DB::table('needbase_form_employed')
                ->where('id', $id)
                ->update($filtered);
        }

        return response()->json([
            'status' => true,
            'message' => 'Employed data updated successfully'
        ]);
    }



    public function updateUnemployedData(Request $request)
    {
        $ids = $request->input('unemployed_id');
        $names = $request->input('unemployed_name');
        $ages = $request->input('unemployed_age');
        $maritals = $request->input('unemployed_marital_status');
        $reasons = $request->input('unemployment_reason');

        if (!$ids || !is_array($ids)) {
            return response()->json([
                'status' => false,
                'message' => 'No unemployed sibling data provided.'
            ]);
        }

        foreach ($ids as $index => $id) {
            DB::table('needbase_form_unemployed')
                ->where('id', $id)
                ->update([
                    'unemployed_name' => $names[$index],
                    'unemployed_age' => $ages[$index],
                    'unemployed_marital_status' => $maritals[$index],
                    'unemployment_reason' => $reasons[$index],
                ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Unemployed sibling data updated successfully.'
        ]);
    }





    public function updateTransport(Request $request)
    {
        $ids = $request->input('transport_id');
        $types = $request->input('vehicle_type');
        $models = $request->input('vehicle_model');
        $years = $request->input('vehicle_year');
        $costs = $request->input('vehicle_fuel_cost');

        if (!$ids || !is_array($ids)) {
            return response()->json([
                'status' => false,
                'message' => 'No transport data provided.'
            ]);
        }

        foreach ($ids as $index => $id) {
            DB::table('needbase_form_transport')
                ->where('id', $id)
                ->update([
                    'vehicle_type' => $types[$index],
                    'vehicle_model' => $models[$index],
                    'vehicle_year' => $years[$index],
                    'vehicle_fuel_cost' => $costs[$index],
                ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Transport information updated successfully.'
        ]);
    }




    public function getApplicantHodPreview(Request $request)
    {

        $applicantId = $request->input('applicant_id');

        $applicantData = DB::table('applicant_scholarship_form')
            ->where('aai_id', $applicantId)
            ->first();
        $resaqlSiblings = DB::select("SELECT * FROM needbase_form_siblings WHERE aai_id = ?", [$applicantId]);

        $ressqlEmployed = DB::select("SELECT * FROM needbase_form_employed WHERE aai_id = ?", [$applicantId]);

        $ressqlunEmployed = DB::select("SELECT * FROM needbase_form_unemployed WHERE aai_id = ?", [$applicantId]);

        $ressqltransport = DB::select("SELECT * FROM needbase_form_transport WHERE aai_id = ?", [$applicantId]);

        return response()->json([
            'status' => true,
            'data' => $applicantData,
            'siblings' => $resaqlSiblings,
            'employed' => $ressqlEmployed,
            'unemployed' => $ressqlunEmployed,
            'transport' => $ressqltransport
        ]);
    }






    public function deleteSectionRow(Request $request)
    {
        try {
            $validated = $request->validate([
                'section_name' => 'required|string',
                'row_id' => 'required|integer',
                'applicant_id' => 'required|integer'
            ]);

            $tableName = '';
            switch ($validated['section_name']) {
                case 'siblings':
                    $tableName = 'needbase_form_siblings';
                    break;
                case 'employed':
                    $tableName = 'needbase_form_employed';
                    break;
                case 'unemployed':
                    $tableName = 'needbase_form_unemployed';
                    break;
                case 'transport':
                    $tableName = 'needbase_form_transport';
                    break;
                default:
                    return response()->json(['status' => false, 'message' => 'Invalid section name'], 400);
            }

            DB::table($tableName)
                ->where('id', $validated['row_id'])
                ->where('aai_id', $validated['applicant_id'])
                ->delete();

            return response()->json([
                'status' => true,
                'message' => 'Row deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to delete row: ' . $e->getMessage()
            ], 500);
        }
    }


}
