<?php

namespace App\Http\Controllers\StudentFinancial;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Grader\Schools;
use App\Models\Grader\Departments;
use DB;
use Carbon\Carbon;
use App\Http\Controllers\StudentFinancial\GenerateSingleChallanController;

class ScholarshipAssignController extends Controller
{
    /**
     * Challan categories that can carry arrears (one-time fee waivers).
     * additional_Full_semester is deliberately excluded — it has no arrears.
     * Must stay in sync with the arrears-applicable categories in
     * StudentRecordController::resolveAcademicFeeStructure().
     */
    public const ARREARS_CHALLAN_CATEGORIES = [
        'Full_semester',
        'Credit_hours',
        'Short/Summer_Course',
    ];

    /** Rows written before FOS_CHALLAN_CATEGORY existed are Full_semester arrears. */
    private const DEFAULT_CHALLAN_CATEGORY = 'Full_semester';

    //
    function index(Request $request){
        $sessions = \App::call('App\Http\Controllers\Grader\AcademicSessionController@retrieve');
        // dd($sessions);
        $scholarships = DB::table('fin_scholarship_budget')
         ->select('fin_scholarship_budget.FSB_SCHLR_SHIP_ID', 'fin_scholarship_budget.FSB_REMAINIG_AMOUNT','fin_scholarship_budget.fsb_amount', 'scholarship_type.scholarship_name')
         ->leftJoin('scholarship_type', 'fin_scholarship_budget.FSB_SCHLR_SHIP_ID', '=', 'scholarship_type.scholarship_id')
         ->get();
        //  dd($scholarships);
        $schools =  Schools::all();
        $departments = array();
        return view('StudentFinancial.assignScholarshipIndex',compact('scholarships','sessions','schools','departments'));
    }

    public function getStudentRec(Request $request){
        $studentEnrollment = strtoupper($request->input('studentEnrollment'));
        $sql="

            SELECT sisusers.id, username, email,group_id,
            emp_f_name,emp_l_name , prefix, emp_f_name, emp_contact_no, emp_l_name emp_full_name,emp_email ,imagename emp_photo ,emp_school_id ,emp_sch.school_name AS emp_school_name,emp_dept.dept_name AS emp_dept_name,
            sai_spi_id, emp_employees.*,
            COALESCE(sai_spi_id,0) AS spi_id ,
            spi_enroll_prefix , spi_enroll_id , spi_name, spi_imagename, spi_fathername,
            spi_sess_id,
            sai_sdpo_sess_id   AS SPI_ENROLLMENT_SESSION,
            sai_sdpo_school_id,
            emp_employees.emp_type as sis_emp_type,
            sai_sdpo_dept_id   AS spi_dept_id ,
            sai_sdpo_prog_id   AS spi_prog_id ,
            sai_sdpo_shift_id,
            sai_sdpo_shift_id AS spi_shift_id ,
            sai_semester      AS sai_active_semester,
            sai_enrollment_no AS SPI_ENROLLMENT_NO ,
            sai_student_status, spi_mobile,  spi_email,
             (select school_code from schools where sai_sdpo_school_id=schools.school_id) as school_code,
            (select dept_name from departments where sai_sdpo_dept_id=departments.dept_id) as dept_name,
            (select prog_name from programs where sai_sdpo_prog_id=programs.prog_id) as prog_name,
            (SELECT
            MAX(sc_co_sess_id)
            FROM
                student_courses) spi_active_sess_id,
            (SELECT max(gpm_active_sessl_id) as gpm_active_sessl_id FROM group_pages_management WHERE gpm_school_id = sai_sdpo_school_id ) AS active_enroll_sess, api_token
            FROM sisusers
            LEFT JOIN emp_employees           ON LOWER(email)      = LOWER(emp_email)
            LEFT JOIN schools emp_sch         ON emp_school_id     = emp_sch.school_id
            LEFT JOIN departments emp_dept    ON emp_dept_id       = emp_dept.dept_id
            LEFT JOIN students_apply_info     ON LOWER(username)   = LOWER(sai_enrollment_no)
            LEFT JOIN students_personal_info  ON sai_spi_id        = spi_id
            AND sai_student_status!='Transferred'
            WHERE lower(sisusers.username)=lower('$studentEnrollment')  ";

            // echo $sql;exit;
         $data=DB::select($sql);
        //  dd($data);
         if (!empty($data)) {
            $data = $data[0]; // Assuming you want to retrieve the first row
        }
        else {
            // Return an error response if no data is found
            return response()->json(['success' => false, 'usernameError' => 'Invalid Student Enrollment Number']);
        }


        $scholarships = DB::table('fin_scholarship_budget')
        ->select('fin_scholarship_budget.FSB_SCHLR_SHIP_ID', 'fin_scholarship_budget.FSB_REMAINIG_AMOUNT', 'fin_scholarship_budget.fsb_amount', 'scholarship_type.scholarship_name')
        ->leftJoin('scholarship_type', 'fin_scholarship_budget.FSB_SCHLR_SHIP_ID', '=', 'scholarship_type.scholarship_id')
        ->where('fin_scholarship_budget.fsb_status', '=', 'E')
        ->get();



         $feeChallanAmount = DB::table('student_challan')
                            ->where('sc_enrollment_no', $studentEnrollment)
                            ->orderBy('id', 'desc')
                            ->limit(1)
                            ->first();


            $getStudentScholarshipRec= $this->getStudentScholarshipRec($studentEnrollment);

        // dd($data, $scholarships, $feeChallanAmount, $getStudentScholarshipRec);
         return response()->json(['success' => true, 'getStudentRec' => $data,'scholarships'=>$scholarships,'feeChallanAmount'=>$feeChallanAmount,'studentScholarship'=> $getStudentScholarshipRec]);

    }




    public function submitStudentScholarship(Request $request)
{
    $fields = $request->only([
        'enrollment_no',
        'scholarshipOption',
        'availableAmount',
        'searchType',
        'scholarshipPercentage',
        'scholarshipAmount',
        'givenScholarshipPercentage',
        'givenScholarshipAmount',
        'remainingAmount',
        'fromDate',
        'toDate',
        'verifyEdit',
        'session_sel',
        'edit_id',
        'givenScholarshipAmount',
        'givenPreviousScholarshipAmount'
    ]);

    $scholarship = $fields;
    $scholarshipPercentage = $request->scholarshipPercentage;

    $totalScholarship = DB::table('STUDENTS_SCHOLARSHIPS_DETAILS')
    ->where('ssd_sai_enrollment_no', strtoupper($scholarship['enrollment_no']))
    ->where('ssd_sess_id', $scholarship['session_sel']);

        // Exclude scholarshipOption if it exists
        if (isset($scholarship['scholarshipOption'])) {
            $totalScholarship->whereNotIn('ssd_type', [$scholarship['scholarshipOption']]);
        }

        $totalScholarshipPercentage = $totalScholarship->sum('ssd_percentage');


    // dd($totalScholarshipPercentage);

    // Check if total scholarship + new scholarship is greater than 100
    if (($totalScholarshipPercentage + $scholarshipPercentage) > 100) {
        // If greater than 100%, return "Overall Scholarship Percentage is greater than 100%"
        return response()->json(['message' => 'Overall Scholarship Percentage is greater than 100%'], 400);
    }

// dd(1111);


    // Fetch current remaining amount for the given scholarship option
    $currentBalance = DB::table('fin_scholarship_budget')
        ->select('fsb_schlr_ship_id', 'fsb_remainig_amount', 'fsb_amount')
        ->where('fsb_schlr_ship_id', $scholarship['scholarshipOption'])
        ->first();

    if (!$currentBalance) {
        return response()->json(['message' => 'Scholarship option not found'], 404);
    }

    // Calculate the new remaining amount
    $remainingAmount = $currentBalance->fsb_remainig_amount;
    if (isset($scholarship['verifyEdit']) && $scholarship['verifyEdit'] == '1') {
        // Calculate the remaining amount for edit scenario
        $remainingAmount = $remainingAmount + $scholarship['givenPreviousScholarshipAmount'] - $scholarship['givenScholarshipAmount'];
    } else {
        // Calculate the remaining amount for new submission
        $remainingAmount -= $scholarship['givenScholarshipAmount'];
    }

    if ($remainingAmount <= 0) {
        return response()->json(['message' => 'Insufficient scholarship funds'], 400);
    }
// dd( $remainingAmount);
    DB::beginTransaction();

    try {
        if (isset($scholarship['verifyEdit']) && $scholarship['verifyEdit'] == '1') {
            // Prepare data for update
            $updateData = [
                'fsa_percentage' => $scholarship['givenScholarshipPercentage'],
                'fsa_amount' => $scholarship['scholarshipAmount'],
                'fsa_given_scholarship_amount' => $scholarship['givenScholarshipAmount'],
                'fsa_remainingamount' => $remainingAmount,
                'fsa_entry_username' => auth()->user()->username,
                'fsa_entry_user_id' => auth()->user()->id,
                'fsa_search_type' => $scholarship['searchType'],
                'fsa_ro_status' => 'Pending',
                'fsa_final_submit_status' => '1'
            ];

            if (isset($scholarship['session_sel'])) {
                $updateData['fsa_sess_id'] = $scholarship['session_sel'];
            }
            if (isset($scholarship['fromDate'])) {
                $updateData['fsa_from_date'] = $scholarship['fromDate'];
            }
            if (isset($scholarship['toDate'])) {
                $updateData['fsa_to_date'] = $scholarship['toDate'];
            }

            $updated = DB::table('fin_scholarship_assigned')
                ->where('fsa_id', $scholarship['edit_id'])
                ->update($updateData);

            if (!$updated) {
                throw new \Exception('Failed to update scholarship');
            }
            // else{
            //     return response()->json(['message' => 'Scholarship updated successfully'], 200);
            // }
        } else {
            // Check if the scholarship already exists for the same session, enrollment number, and scholarship ID
            $existingScholarship = DB::table('fin_scholarship_assigned')
                ->where('fsa_enrollment_no', strtoupper($scholarship['enrollment_no']))
                ->where('fsa_schlr_ship_id', $scholarship['scholarshipOption'])
                ->where('fsa_sess_id', $scholarship['session_sel'])
                ->first();

            if ($existingScholarship) {
                DB::rollBack();
                return response()->json(['message' => 'Selected scholarship already assigned for this session'], 400);
            }

            // Prepare data for insertion
            $insertData = [
                'fsa_schlr_ship_id' => $scholarship['scholarshipOption'],
                'fsa_enrollment_no' => strtoupper($scholarship['enrollment_no']),
                'fsa_percentage' => $scholarship['givenScholarshipPercentage'],
                'fsa_amount' => $scholarship['scholarshipAmount'],
                'fsa_entry_username' => auth()->user()->username,
                'fsa_entry_user_id' => auth()->user()->id,
                'fsa_available_amount' => $scholarship['availableAmount'],
                'fsa_search_type' => $scholarship['searchType'],
                'fsa_given_scholarship_amount' => $scholarship['givenScholarshipAmount'],
                'fsa_remainingamount' => $remainingAmount,
                'fsa_ro_status' => 'Pending',
                'fsa_final_submit_status' => '1'
            ];

            if (isset($scholarship['session_sel'])) {
                $insertData['fsa_sess_id'] = $scholarship['session_sel'];
            }
            if (isset($scholarship['fromDate'])) {
                $insertData['fsa_from_date'] = $scholarship['fromDate'];
            }
            if (isset($scholarship['toDate'])) {
                $insertData['fsa_to_date'] = $scholarship['toDate'];
            }

            $inserted = DB::table('fin_scholarship_assigned')->insert($insertData);

            if (!$inserted) {
                throw new \Exception('Failed to submit scholarship');
            }
        }

        //Update the remaining amount in the scholarship budget table
        $updateAmount = DB::table('fin_scholarship_budget')
            ->where('fsb_schlr_ship_id', $scholarship['scholarshipOption'])
            ->update(['fsb_remainig_amount' => $remainingAmount]);

        if (!$updateAmount) {
            throw new \Exception('Failed to update scholarship amount');
        }

        DB::commit();

        return response()->json(['message' => 'Scholarship processed successfully'], 200);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['message' => $e->getMessage()], 500);
    }
}




    public function getStudentScholarshipRec($FSA_ENROLLMENT_NO = null) {
        $scholarshipsSql = " SELECT *
                    FROM
                        FIN_SCHOLARSHIP_ASSIGNED
                    LEFT JOIN
                scholarship_type ON FIN_SCHOLARSHIP_ASSIGNED.FSA_SCHLR_SHIP_ID = scholarship_type.scholarship_id
                left join academic_sessions on FIN_SCHOLARSHIP_ASSIGNED.fsa_sess_id=academic_sessions.sess_id
                ";


        if ($FSA_ENROLLMENT_NO) {
            $scholarshipsSql .= " WHERE FIN_SCHOLARSHIP_ASSIGNED.FSA_ENROLLMENT_NO = :FSA_ENROLLMENT_NO";
            $scholarships = DB::select($scholarshipsSql, ['FSA_ENROLLMENT_NO' => $FSA_ENROLLMENT_NO]);
        } else {
            $scholarships = DB::select($scholarshipsSql);
        }

        return $scholarships;
    }



    public function deleteStudentScholarshipRec(Request $request)
    {
        $id = $request->input('id');
        // return response()->json(['success' => true, 'message' => 'Record deleted successfully.']);

        try {
            // Execute the raw SQL query to delete the record
            $deleted = DB::delete('DELETE FROM FIN_SCHOLARSHIP_ASSIGNED WHERE fsa_id = ?', [$id]);

            if ($deleted) {
                return response()->json(['success' => true, 'message' => 'Record deleted successfully.']);
            } else {
                return response()->json(['success' => false, 'message' => 'Record not found or already deleted.']);
            }
        } catch (\Throwable $th) {
            // Log the exception for debugging
            \Log::error('Failed to delete record: ' . $th->getMessage());

            // Return a JSON response with the error message
            return response()->json(['success' => false, 'message' => 'An error occurred while deleting the record.']);
        }
    }

    public function scholarshipStudentList(Request $request){
        // Assuming scholarshipId is passed as a parameter in the array
        $scholarshipId = $request->scholarshipId;

        // Fetch scholarship details using the query
        $scholarshipDetails = DB::table('fin_scholarship_assigned')
                                ->select(
                                    'academic_sessions.sess_code',
                                    'scholarship_type.scholarship_name',
                                    'fin_scholarship_assigned.fsa_entry_username',
                                    'fin_scholarship_assigned.fsa_entry_date',
                                    'fin_scholarship_assigned.fsa_given_scholarship_amount',
                                    'fin_scholarship_assigned.fsa_search_type',
                                    'fin_scholarship_assigned.fsa_percentage',
                                    'fin_scholarship_assigned.fsa_ro_status',
                                    'fin_scholarship_assigned.fsa_enrollment_no'
                                )
                                ->leftJoin('scholarship_type', 'fin_scholarship_assigned.fsa_schlr_ship_id', '=', 'scholarship_type.scholarship_id')
                                ->leftJoin('academic_sessions', 'fin_scholarship_assigned.fsa_sess_id', '=', 'academic_sessions.sess_id')
                                ->where('fsa_schlr_ship_id', $scholarshipId)
                                ->orderBy('fsa_id', 'desc')
                                ->get();

        // Return scholarship details as JSON response with status code
        return response()->json(['students' => $scholarshipDetails], 200);
    }





    public function indexOtherScholarship(Request $request)
    {
        $ro_users=['somia.maqsood', 'haider.raza', 'talha.sarfraz', 'abdul.waheed'];
        $admission_users=['asbah.rehman', 'sumaira.arshad', 'sidra.tanveer'];
        $usernames_ro_str = "'" . implode("','", $ro_users) . "'";
        $usernames_adm_str = "'" . implode("','", $admission_users) . "'";

        $scholarshipsData = DB::table('fin_other_scholarships')
                        ->leftJoin('ACADEMIC_SESSIONS', 'fin_other_scholarships.fos_sess_id', '=', 'ACADEMIC_SESSIONS.sess_id')
                        ->leftJoin('scholarship_type', 'fin_other_scholarships.fos_scholarship_type', '=', 'scholarship_type.scholarship_id');

        if (in_array(auth()->user()->username, $ro_users)) {
            $scholarshipsData= $scholarshipsData->whereIn('fin_other_scholarships.ENTRY_USERNAME',  $ro_users);
        } else  if (in_array(auth()->user()->username, $admission_users)) {
            $scholarshipsData= $scholarshipsData->whereIn('fin_other_scholarships.ENTRY_USERNAME', $admission_users);
        }
        $scholarshipsData= $scholarshipsData->get();

        return view('StudentFinancial.otherScholarship', ['scholarshipsData' => $scholarshipsData, 'firstload' => 1]);
    }



    function getStudentProfile(Request $request){


        $getStudent = new GenerateSingleChallanController();
        $getStudentRec = $getStudent->getStudentRec($request);

        // Retrieve sessions using the AcademicSessionController
        $sessions = \App::call('App\Http\Controllers\Grader\AcademicSessionController@retrieve');
        $sessions = $sessions->toArray();


        // dd($sessions);

        // Retrieve scholarships from the database
        // $scholarships = DB::table('fin_scholarship_budget')
        //     ->select('fin_scholarship_budget.FSB_SCHLR_SHIP_ID', 'fin_scholarship_budget.FSB_REMAINIG_AMOUNT', 'fin_scholarship_budget.fsb_amount', 'scholarship_type.scholarship_name')
        //     ->leftJoin('scholarship_type', 'fin_scholarship_budget.FSB_SCHLR_SHIP_ID', '=', 'scholarship_type.scholarship_id')
        //     ->get();
        $scholarships = DB::select("select SCHOLARSHIP_ID, SCHOLARSHIP_NAME from Scholarship_Type where scholarship_doner_type is null");

            $getScholarshipsByEnrollmentNo = $this->getScholarshipsByEnrollmentNo($request, $getStudentRec);

        // dd($getScholarshipsByEnrollmentNo);
        // Initialize an empty array for departments (if you plan to use this in the future)
        $departments = array();
        return response()->json([
            'status' => 'success',
            'studentRec' => $getStudentRec,
            'sessions' => $sessions,
            'scholarships' => $scholarships,
            'getScholarshipsByEnrollmentNo' => $getScholarshipsByEnrollmentNo
        ]);


    }

    public function submitScholarship(Request $request)
    {
        // dd($request->all());
        $userid = auth()->user()->id;
        $username = auth()->user()->username;

            // Validate the input data
            $request->validate([
                'fos_app_id' => 'required|integer',
                'fos_enrollment_no' => 'nullable|string|max:255',
                'sessionId' => 'required|integer',
                'scholarshipId' => 'required|integer',
                'challan_category' => 'required|in:' . implode(',', self::ARREARS_CHALLAN_CATEGORIES),
                'admission_fee' => 'required|numeric',
                'security_fee' => 'required|numeric',
                'activity_charges' => 'required|numeric',
                'medical_insurance' => 'required|numeric',
                'stipend_amount' => 'required|numeric',
            ]);

            $challanCategory = $request->challan_category;

            // Prepare the data for insertion or update
            $data = [
                'fos_app_id' => $request->fos_app_id,
                'fos_enrollment_no' => $request->fos_enrollment_no,
                'fos_sess_id' => $request->sessionId,
                'fos_scholarship_type' => $request->scholarshipId,
                'fos_challan_category' => $challanCategory,
                'fos_admission_fee' => $request->admission_fee,
                'fos_security_fee' => $request->security_fee,
                'fos_activity_charges' => $request->activity_charges,
                'fos_medical_insurance' => $request->medical_insurance,
                'fos_stipend' => $request->stipend_amount,
                'entry_by' => $userid,
                'entry_username' => $username,
                'created_at' => Carbon::now('Asia/Karachi'), // Karachi timezone
            ];

            // Arrears are unique per (student, session, scholarship type, CHALLAN
            // CATEGORY). The check now runs on UPDATE too — previously an edit could
            // silently create a collision that the insert path would have blocked.
            // The student match mirrors the READ logic (app id OR enrollment): keying
            // on fos_app_id alone let a student with a missing/0 app id accumulate
            // duplicate rows, which getStudentArrearsBreakdown() then SUMs.
            $duplicate = DB::table('fin_other_scholarships')
                ->where('fos_sess_id', $request->sessionId)
                ->where('fos_scholarship_type', $request->scholarshipId)
                ->whereRaw("NVL(fos_challan_category, ?) = ?", [self::DEFAULT_CHALLAN_CATEGORY, $challanCategory])
                ->where(function ($q) use ($request) {
                    $q->where('fos_app_id', $request->fos_app_id);
                    if (!empty($request->fos_enrollment_no)) {
                        $q->orWhereRaw('UPPER(TRIM(fos_enrollment_no)) = ?', [strtoupper(trim($request->fos_enrollment_no))]);
                    }
                })
                ->when($request->updateId, function ($q) use ($request) {
                    $q->where('id', '!=', $request->updateId);   // don't collide with itself
                })
                ->exists();

            if ($duplicate) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Duplicate record found. Arrears for this scholarship already exist for this student, session and challan category ('
                        . $challanCategory . ').',
                ]);
            }

            if ($request->updateId) {
                DB::table('fin_other_scholarships')->where('id', $request->updateId)->update($data);
                $message = 'Other Scholarship data updated successfully.';
            } else {
                DB::table('fin_other_scholarships')->insert($data);
                $message = 'Other Scholarship data added as a new record.';
            }
            // Return a JSON response
            return response()->json(['status' => 'success', 'message' => $message]);
    }

    /**
     * Resolve app id and enrollment from student record (handles Oracle upper/lowercase keys).
     */
    private function resolveStudentIdentifiers($studentRec): array
    {
        $appId = null;
        $enrollmentNo = null;

        if (!is_object($studentRec) || isset($studentRec->usernameError)) {
            return [$appId, $enrollmentNo];
        }

        foreach ((array) $studentRec as $key => $value) {
            if ($value === null || $value === '') {
                continue;
            }

            $normalizedKey = strtolower($key);

            if (in_array($normalizedKey, ['aai_id', 'fos_app_id'], true)) {
                $appId = (int) $value;
            }

            if (in_array($normalizedKey, ['username', 'sai_enrollment_no', 'cms_sai_enrollment_no', 'fos_enrollment_no'], true)) {
                $enrollmentNo = strtoupper((string) $value);
            }
        }

        return [$appId, $enrollmentNo];
    }

    public function getScholarshipsByEnrollmentNo(Request $request, $studentRec = null)
    {
        $request->validate([
            'studentEnrollment' => 'required|string|max:255',
        ]);

        $studentEnrollment = trim($request->studentEnrollment);

        if ($studentRec === null) {
            $getStudent = new GenerateSingleChallanController();
            $studentRec = $getStudent->getStudentRec($request);
        }

        $appIds = [];
        $enrollmentNos = [];

        if (is_numeric($studentEnrollment)) {
            $appIds[] = (int) $studentEnrollment;
        } else {
            $enrollmentNos[] = strtoupper($studentEnrollment);
        }

        [$resolvedAppId, $resolvedEnrollmentNo] = $this->resolveStudentIdentifiers($studentRec);

        if ($resolvedAppId !== null) {
            $appIds[] = $resolvedAppId;
        }

        if ($resolvedEnrollmentNo !== null) {
            $enrollmentNos[] = $resolvedEnrollmentNo;
        }

        $appIds = array_values(array_unique(array_filter($appIds)));
        $enrollmentNos = array_values(array_unique(array_filter($enrollmentNos)));

        if (empty($appIds) && empty($enrollmentNos)) {
            return collect();
        }

        return DB::table('fin_other_scholarships')
            ->leftJoin('ACADEMIC_SESSIONS', 'fin_other_scholarships.fos_sess_id', '=', 'ACADEMIC_SESSIONS.sess_id')
            ->leftJoin('scholarship_type', 'fin_other_scholarships.fos_scholarship_type', '=', 'scholarship_type.scholarship_id')
            ->where(function ($q) use ($appIds, $enrollmentNos) {
                $hasCondition = false;

                if (!empty($appIds)) {
                    $q->where(function ($sub) use ($appIds) {
                        $sub->whereIn('fin_other_scholarships.fos_app_id', $appIds);
                    });
                    $hasCondition = true;
                }

                if (!empty($enrollmentNos)) {
                    if ($hasCondition) {
                        $q->orWhere(function ($sub) use ($enrollmentNos) {
                            foreach ($enrollmentNos as $enrollmentNo) {
                                $sub->orWhereRaw('UPPER(fin_other_scholarships.fos_enrollment_no) = ?', [$enrollmentNo]);
                            }
                        });
                    } else {
                        $q->where(function ($sub) use ($enrollmentNos) {
                            foreach ($enrollmentNos as $enrollmentNo) {
                                $sub->orWhereRaw('UPPER(fin_other_scholarships.fos_enrollment_no) = ?', [$enrollmentNo]);
                            }
                        });
                    }
                }
            })
            ->get();
    }

        public function editScholarship($id)
        {
            // Find the scholarship record by ID
            $scholarship = DB::table('fin_other_scholarships')
                ->where('id', $id)  // Make sure 'id' is in lowercase
                ->first();
            if ($scholarship) {
                // Return the scholarship details as a response
                return response()->json([
                    'status' => 'success',
                    'data' => $scholarship
                ]);
            } else {
                // If no scholarship found, return error response
                return response()->json([
                    'status' => 'error',
                    'message' => 'Scholarship not found'
                ]);
            }
        }


}
