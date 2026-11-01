<?php

namespace App\Http\Controllers\Grader\StudentFines;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;
class StudentFineController extends Controller
{

    public function index(Request $request)
    {
        $entryUserId = auth()->user()->id;
        $groupId = auth()->user()->group_id;

        if ($groupId == 644 || auth()->user()->username == 'asif_nadeem') {
            $query = DB::table('fine_category')->select('id', 'fc_category_name')->where('fc_status', 'E');
        } else {
            $query = DB::table('fine_category_permissions')
                ->leftJoin('fine_category', 'fine_category_permissions.fc_id', '=', 'fine_category.id')
                ->select('fine_category.id', 'fine_category.fc_category_name')
                ->where('FCP_STATUS', 'E')
                ->where('fine_category_permissions.fcp_allowed_user', $entryUserId);
        }

        $fine_categories = $query->get();

        return view('grader.StudentFines.masterForm', ['fine_categories' => $fine_categories]);
    }

    public function submitFine(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fineCategoryOption' => 'required',
            'violationType' => 'required',
            'fineAmount' => 'required',
        ], [
            'fineCategoryOption.required' => 'Category is required',
            'violationType.required' => 'Violation Type is required',
            'fineAmount.required' => 'Fine Amount is required',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return response()->json(['success' => false, 'errors' => $errors], 422);
        }

        try {
            // Validate the request data as needed
            $fineCategoryOption = $request->fineCategoryOption;
            $violationType = $request->violationType;
            $fineAmount = $request->fineAmount;
            $id = $request->id;

            // Use the auth() helper to get the authenticated user's ID
            $entryUserId = auth()->user()->employee_id; // Replace 'employee_id' with the actual user ID field in your users table

            // Perform the database operation
            if ($request->verifyEdit == '1') {
                // Update existing fine record
                $updateData = [
                    'FC_ID' => $fineCategoryOption,
                    'fcd_violation_type' => $violationType,
                    'fcd_fine_amount' => $fineAmount,
                    'entry_user_id' => $entryUserId,
                    'fcd_status' => 'E',
                ];

                $updated = DB::table('fine_category_details')
                    ->where('id', $id)
                    ->update($updateData);

                if (!$updated) {
                    return response()->json(['message' => 'Failed to update fine'], 500);
                } else {
                    return response()->json(['success' => true, 'message' => 'Fine updated successfully', 'challanRec' => $this->getFinesData()]);
                }
            } else {
                // Insert new fine record
                $dbInsert = DB::table('fine_category_details')->insert([
                    'fc_id' => $fineCategoryOption,
                    'fcd_violation_type' => $violationType,
                    'fcd_fine_amount' => $fineAmount,
                    'entry_user_id' => $entryUserId,
                    'fcd_status' => 'E'
                ]);

                if (!$dbInsert) {
                    throw new \Exception('DB Insertion Failed');
                } else {
                    return response()->json(['success' => true, 'message' => 'Fine inserted successfully', 'challanRec' => $this->getFinesData()]);
                }
            }
        } catch (\Exception $e) {
            // Handle exceptions
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }




    public function fineChallanGeneratedList(Request $request)
    {
        $roUsernamesFines = ['somia.maqsood', 'talha.sarfraz', 'zonia.akbar', 'haider.raza', 'abdul.waheed'];
        if (in_array(auth()->user()->username, $roUsernamesFines)) {
            $departCheck = 'AND fine_category.fc_dept_id = 23';
        } else {
            $departCheck = '';
        }
        $sql = "SELECT
                            student_challan.sc_enrollment_no,
                            academic_sessions.sess_name,
                            fine_category.fc_category_name,
                            fine_category_details.fcd_violation_type,
                            student_challan.sc_amount,
                            student_challan.sc_challan_status ,
                            student_challan.invoiceamount,
                            student_challan.sc_challan_type,
                            student_challan.sc_entry_user_id,
                            student_challan.sc_entry_user_name AS fined_by,
                            student_challan.id as challan_id,
                            sc_issu_date,
                            (select sai_student_status from students_apply_info where sai_enrollment_no = sc_enrollment_no) as student_status
                        FROM
                            student_challan
                        LEFT JOIN
                            fine_category ON student_challan.sc_category_id = fine_category.id
                        LEFT JOIN
                            academic_sessions ON student_challan.sc_session_id = academic_sessions.sess_id
                        LEFT JOIN
                            fine_category_details ON student_challan.sc_violation_type_id = fine_category_details.id
                        WHERE
                            student_challan.sc_challan_type = 'Fine'
                            $departCheck
                        order by student_challan.sc_issu_date desc";
        // echo $sql;exit;
        $challansRec = DB::select($sql);

        $fine_categories = DB::table('fine_category')
            ->select('id', 'fc_category_name', 'FC_DESCRIPTION')
            ->where('fc_status', 'E')
            ->orderBy('id', 'desc')
            ->get();
        // dd($fine_categories);
        $sessions = DB::table('student_challan')
            ->select('sc_session_id', 'SESS_NAME')
            ->join('academic_sessions', 'academic_sessions.sess_id', '=', 'student_challan.sc_session_id')
            ->where('sc_challan_type', 'Fine')
            ->distinct()
            ->orderBy('sc_session_id', 'desc')
            ->get();
        // dd($sessions);
        $sql = "SELECT distinct emp_ad_username, EMPLOYEE_ID FROM EMP_EMPLOYEES
            join student_challan on student_challan.SC_ENTRY_USER_ID = EMP_EMPLOYEES.EMPLOYEE_ID
                WHERE student_challan.sc_challan_type='Fine' order by emp_ad_username"
        ;
        $users = DB::select($sql);
        return view('grader.StudentFines.fineChallanGenerated', compact('challansRec', 'fine_categories', 'sessions', 'users'));
    }

    function blockedStudentFineList()
    {

        return view('grader.StudentFines.blockedStudents');
    }


    function getBlockedStudents()
    {
        $sql = "SELECT DISTINCT
                    sc_sai_enrollment_no,spi_name,sc_status
                FROM
                    student_courses
                    left join students_apply_info on sai_enrollment_no = sc_sai_enrollment_no
                    left join students_personal_info on sai_spi_id = spi_id

                WHERE
                    sc_co_sess_id = 162
                    AND sc_status = 'Block'";
        $res = DB::select($sql);
        // $response = [
        //     'blockedSTudents' => $res,
        // ];
        return response()->json($res);
    }





    public function insertData(Request $request)
    {
        // Validate the request
        $request->validate([
            'action' => 'required|string',
        ]);

        $username = auth()->user()->username;
        // Allowed usernames and group IDs
        $allowedUsernames = ['asif_nadeem']; // Add all allowed usernames
        $allowedGroupIds = [644]; // Add all allowed group IDs


        //    $response = in_array($username, $allowedUsernames) || in_array(auth()->user()->group_id, $allowedGroupIds)
        //    ? $this->getChallanStudents()
        //    : $this->getChallanStudentsExcel();
        $fromIssueDate = $request->from_issue_date ?? null;
        $toIssueDate   = $request->to_issue_date ?? null;
        $studentEnrollment   = $request->studentEnrollment ?? null;
        $challanNo   = $request->challanNo ?? null;
        if ($request->action === 'export_without_version') {

             $response = $this->getChallanStudentsWithoutVersion(
                $fromIssueDate,
                $toIssueDate,
                $studentEnrollment,
                $challanNo
            );
        } else {
            $response = $this->getChallanStudents(
                $fromIssueDate,
                $toIssueDate,
                $studentEnrollment,
                $challanNo
            );
        }


        $data = json_decode($response->getContent(), true);





        // Check if data is empty
        if (empty($data)) {
            return response()->json(['error' => 'No data returned from getChallanStudents.'], 400);
        }

        // Separate columns into two arrays: general and scholarships
        $columnNames = [];
        $scholarshipNames = [];

        foreach ($data as &$row) {
            // Add main keys of the row to column names, excluding 'scholarships'
            foreach (array_keys($row) as $key) {
                if ($key !== 'scholarships') {
                    $sanitizedKey = strtolower(preg_replace('/[^A-Za-z0-9_]/', '_', $key));
                    if (!in_array($sanitizedKey, $columnNames)) {
                        $columnNames[] = $sanitizedKey;
                    }
                }
            }

            // dd($columnNames);
            // Handle scholarships dynamically
            if (isset($row['scholarships']) && is_array($row['scholarships'])) {
                foreach ($row['scholarships'] as $scholarship) {
                    if (isset($scholarship['scholarship_name'])) {
                        $scholarshipColumn = strtolower(preg_replace('/[^A-Za-z0-9_]/', '_', $scholarship['scholarship_name']));
                        if (!in_array($scholarshipColumn, $scholarshipNames)) {
                            $scholarshipNames[] = $scholarshipColumn;
                        }

                        // Add the discounted amount under the scholarship column
                        $row[$scholarshipColumn] = $scholarship['fsd_discounted_amount'] ?? null;
                    }
                }
                unset($row['scholarships']); // Remove the scholarships array
            }
        }
        unset($row);

        // Combine columns for insertion/export
        $allColumns = array_merge($columnNames, $scholarshipNames);

        // dd($allColumns);

        // Handle insert action
        if ($request->action === 'insert') {
            $tableName = 'cms_fee.challan_data_new';
            $secondaryDb = DB::connection('oracle_secondary');

            // Drop the table if it exists
            $dropTableSql = "BEGIN
                            EXECUTE IMMEDIATE 'DROP TABLE $tableName';
                         EXCEPTION
                            WHEN OTHERS THEN
                                IF SQLCODE != -942 THEN
                                    RAISE;
                                END IF;
                         END;";
            $secondaryDb->statement($dropTableSql);

            // Create the table dynamically
            $createTableSql = "CREATE TABLE $tableName (id NUMBER GENERATED BY DEFAULT AS IDENTITY PRIMARY KEY";
            foreach ($allColumns as $column) {
                $createTableSql .= ", $column VARCHAR2(2500)";
            }
            $createTableSql .= ")";
            $secondaryDb->statement($createTableSql);

            // Insert data into the table
            foreach ($data as $row) {
                $mappedRow = array_fill_keys($allColumns, "NULL");

                foreach ($row as $key => $value) {
                    $sanitizedKey = strtolower(preg_replace('/[^A-Za-z0-9_]/', '_', $key));
                    if (in_array($sanitizedKey, $allColumns)) {
                        $mappedRow[$sanitizedKey] = $value === null ? "NULL" : "'" . str_replace("'", "''", (string) $value) . "'";
                    }
                }

                $columns = implode(', ', array_keys($mappedRow));
                $values = implode(', ', array_values($mappedRow));

                $insertSql = "INSERT INTO $tableName ($columns) VALUES ($values)";
                $secondaryDb->statement($insertSql);
            }

            // Update specific rows
            $sel = "UPDATE challan_data_new
        SET valid_date = CASE
                        WHEN challan_no IN ('13211','13209','13207','13199')
    and version=1 THEN TO_DATE('31-JAN-2025', 'DD-MON-YYYY')
                        END,due_date = CASE
                            WHEN challan_no IN ('13211','13209','13207','13199') THEN TO_DATE('17-JAN-2025', 'DD-MON-YYYY')
                        END
            WHERE challan_no IN ('13211','13209','13207','13199')
    and version=1";

            $secondaryDb->statement($sel);

            $sql = "UPDATE challan_data_new
        SET SCHOLARSHIP_ARREAR =0
            WHERE SCHOLARSHIP_ARREAR > 0";

            $secondaryDb->statement($sql);

            $sql1 = "UPDATE challan_data_new
                SET
                    app_id = CASE
                        WHEN challan_type = 'shortcourse' THEN 'SC' || app_id
                        WHEN challan_type = 'donation' THEN 'D' || app_id
                        WHEN challan_type = 'misc_fee' THEN 'M' || app_id
                        ELSE app_id
                    END,
                    CHANGE_TIME = CASE
                        WHEN CHANGE_TIME IS NULL THEN created_at
                        ELSE CHANGE_TIME
                    END
                WHERE challan_type IN ('shortcourse', 'donation', 'misc_fee')";

            $secondaryDb->statement($sql1);

            $grantSql1 = "GRANT SELECT ON $tableName TO FEE_DB_LINK";
            $grantSql2 = "GRANT SELECT ON $tableName TO cms_fee_view";
            $secondaryDb->statement($grantSql1);
            $secondaryDb->statement($grantSql2);

            return response()->json(['success' => 'Data export successfully in Middleware database.']);
        }


        // Define the folder path for CSV export
        $folderPath = public_path('student_challan_utility');
        if (!File::exists($folderPath)) {
            File::makeDirectory($folderPath, 0755, true);
        }

        // Generate a file name with the current date and time
        $username = auth()->user()->username;
        $versionSuffix = ($request->action === 'export_without_version') ? '_without_version' : '_with_version';
        $fileName = 'challan_data' . $versionSuffix . '_' . $username . '_' . Carbon::now()->format('Ymd_His') . '.csv';
        $filePath = $folderPath . '/' . $fileName;

        // Column mapping for headers
        $columnMapping = [
            'issue_date' => 'Issue Date',
            'due_date' => 'Due Date',
            'valid_date' => 'Valid Date',
            'challan_no' => 'Challan No',
            'sess_id' => 'Session',
            'challan_type' => 'Challan Type',
            'installments' => 'Installments',
            'regno' => 'Reg No',
            'app_id' => 'Student ID',
            'studentname' => 'Name',
            'sess_code' => 'Session',
            'schl_code' => 'School',
            'prog_name' => 'Program',
            'admission_amt' => 'Adm Amt',
            'security_amt' => 'Sec Amt',
            'medical_amt' => 'Med Amt',
            'activity_amt' => 'Act Amt',
            'tuition_fee_amnt' => 'Tui Amt',
            'installment_amnt' => 'Installment Amt',
            'surplus_amount' => 'Sur Amt',
            'hostel_amount' => 'Hostel Amount',
            'invoice_amount' => 'Invoice',
            'fine' => 'Fine',
            'paid_amount' => 'Total Paid',
            'enrolled_credits' => 'Enrolled Credits',
            'challan_status' => 'Status'
        ];

        // Add scholarships to column mapping
        foreach ($scholarshipNames as $scholarship) {
            $columnMapping[$scholarship] = ucwords(str_replace('_', ' ', $scholarship));
        }
        // Ensure common Middleware columns have readable Excel headers
        if (!isset($columnMapping['scholarship_arrear'])) {
            $columnMapping['scholarship_arrear'] = 'Scholarship Arrear';
        }
        if (!isset($columnMapping['challan_sch'])) {
            $columnMapping['challan_sch'] = 'Challan Scholarship';
        }

        // Same columns as Middleware insert ($allColumns) — do not drop fields for any user
        $exportColumns = $allColumns;
        if ($request->action === 'export_with_version' && !in_array('version', $exportColumns, true)) {
            $exportColumns[] = 'version';
        }
        if ($request->action === 'export_without_version' && !in_array('change_time', $exportColumns, true)) {
            $exportColumns[] = 'change_time';
        }
        $headers = array_map(function ($col) use ($columnMapping) {
            return $columnMapping[$col] ?? $col;
        }, $exportColumns);

        // Open the file for writing
        $file = fopen($filePath, 'w');

        // Write headers
        fputcsv($file, $headers);

        // Write rows
        foreach ($data as $row) {
            $rowData = [];
            foreach ($exportColumns as $column) {

                if ($column === 'change_time' && !empty($row[$column])) {
                    try {
                        $rowData[] = \Carbon\Carbon::parse($row[$column])->format('d/m/Y');
                    } catch (\Exception $e) {
                        $rowData[] = '';
                    }
                } else {
                    $rowData[] = $row[$column] ?? '';
                }
            }
            fputcsv($file, $rowData);
        }


        fclose($file);

        // Return the file URL
        return response()->json([
            'success' => 'The Excel file has been generated successfully and is ready for download.',
            'file_url' => url('student_challan_utility/' . $fileName),
        ]);
    }



    public function insertDataSummer(Request $request)
    {
        $request->validate([
            'action' => 'required|string',
            'session_id' => 'required|string',
        ]);


        $username = auth()->user()->username;
        // $allowedUsernames = ['asif_nadeem'];
        // $allowedGroupIds = [644];

        // Fetch data from updated SQL function
        $response = $this->getChallanStudentsSummer($request->session_id);
        $data = json_decode($response->getContent(), true);

        if (empty($data)) {
            return response()->json(['error' => 'No data returned from getChallanStudentsSummer.'], 400);
        }
        // dd($data);

        // Get all unique column names from result
        $allColumns = [];
        foreach ($data as $row) {
            foreach (array_keys($row) as $key) {
                $sanitizedKey = strtolower(preg_replace('/[^A-Za-z0-9_]/', '_', $key));
                if (!in_array($sanitizedKey, $allColumns)) {
                    $allColumns[] = $sanitizedKey;
                }
            }
        }

        if ($request->action === 'insert') {
            $tableName = 'cms_fee.summer_challan_breakup';
            $secondaryDb = DB::connection('oracle_secondary');

            $dropTableSql = "BEGIN
                            EXECUTE IMMEDIATE 'DROP TABLE $tableName';
                         EXCEPTION
                            WHEN OTHERS THEN
                                IF SQLCODE != -942 THEN
                                    RAISE;
                                END IF;
                         END;";
            $secondaryDb->statement($dropTableSql);

            $createTableSql = "CREATE TABLE $tableName (id NUMBER GENERATED BY DEFAULT AS IDENTITY PRIMARY KEY";
            foreach ($allColumns as $column) {
                $createTableSql .= ", $column VARCHAR2(3000)";
            }
            $createTableSql .= ")";
            $secondaryDb->statement($createTableSql);

            foreach ($data as $row) {
                $mappedRow = array_fill_keys($allColumns, "NULL");
                foreach ($row as $key => $value) {
                    $sanitizedKey = strtolower(preg_replace('/[^A-Za-z0-9_]/', '_', $key));
                    if (in_array($sanitizedKey, $allColumns)) {
                        $mappedRow[$sanitizedKey] = $value === null ? "NULL" : "'" . str_replace("'", "''", (string) $value) . "'";
                    }
                }

                $columns = implode(', ', array_keys($mappedRow));
                $values = implode(', ', array_values($mappedRow));
                $insertSql = "INSERT INTO $tableName ($columns) VALUES ($values)";
                $secondaryDb->statement($insertSql);
            }

            $grantSql1 = "GRANT SELECT ON $tableName TO FEE_DB_LINK";
            $grantSql2 = "GRANT SELECT ON $tableName TO cms_fee_view";
            $secondaryDb->statement($grantSql1);
            $secondaryDb->statement($grantSql2);

            return response()->json(['success' => 'Summer Data exported successfully to Middleware database.']);
        }

        // CSV Export (no scholarship logic)
        $folderPath = public_path('student_challan_utility');
        if (!File::exists($folderPath)) {
            File::makeDirectory($folderPath, 0755, true);
        }

        $fileName = 'summer_challan_breakup_' . $username . '_' . Carbon::now()->format('Ymd_His') . '.csv';
        $filePath = $folderPath . '/' . $fileName;

        $file = fopen($filePath, 'w');

        // Header
        fputcsv($file, $allColumns);

        // Data rows
        foreach ($data as $row) {
            $rowData = [];
            foreach ($allColumns as $column) {
                $originalKey = array_keys($row)[array_search($column, array_map(function ($k) {
                    return strtolower(preg_replace('/[^A-Za-z0-9_]/', '_', $k));
                }, array_keys($row))) ?? -1] ?? null;

                $rowData[] = $originalKey !== null ? $row[$originalKey] : '';
            }
            fputcsv($file, $rowData);
        }

        fclose($file);

        return response()->json([
            'success' => 'The Summer Challan Breakup file has been generated successfully.',
            'file_url' => url('student_challan_utility/' . $fileName),
        ]);
    }

    function students_fee_challan()
    {

        $sessions = $this->getSessions();


        // Summer sessions from Summer 2026 onward only
        $summerSessions = collect($sessions)->filter(function ($session) {
            $name = $session->sess_name ?? '';
            if (stripos($name, 'summer') === false) {
                return false;
            }
            if (!preg_match('/\b(20\d{2})\b/', $name, $matches)) {
                return false;
            }

            return (int) $matches[1] >= 2026;
        })->values();



        return view('grader.StudentFines.feeChallans', ['sessions' => $summerSessions]);
    }

    function getChallanStudents($fromIssueDate = null, $toIssueDate = null , $studentEnrollment = null , $challanNo = null)
    {
        $sql = "
            SELECT *
            FROM fee_challan_data f
            WHERE 1 = 1
        ";

        $bindings = [];

        /* Change Time Date Filters */
        if ($fromIssueDate) {
            $sql .= " AND TRUNC(f.change_time) >= TO_DATE(:from_issue_date, 'YYYY-MM-DD')";
            $bindings['from_issue_date'] = $fromIssueDate;
        }

        if ($toIssueDate) {
            $sql .= " AND TRUNC(f.change_time) <= TO_DATE(:to_issue_date, 'YYYY-MM-DD')";
            $bindings['to_issue_date'] = $toIssueDate;
        }

        /* Student Enrollment Logic */
        if ($studentEnrollment) {

            // Numeric only → APP_ID
            if (ctype_digit($studentEnrollment)) {
                $sql .= " AND f.app_id = :app_id";
                $bindings['app_id'] = $studentEnrollment;
            }
            // Alphanumeric / hyphen → REGNO
            else {
                $sql .= " AND f.regno = :regno";
                $bindings['regno'] = $studentEnrollment;
            }
        }

        /* Challan No */
        if ($challanNo) {
            $sql .= " AND f.challan_no = :challan_no";
            $bindings['challan_no'] = $challanNo;
        }

        $res = DB::select($sql, $bindings);

        foreach ($res as $key => $row) {
            $this->attachExportScholarshipsToRow($row);
        }

        return response()->json($res);
    }

    function getChallanStudentsWithoutVersion($fromIssueDate = null, $toIssueDate = null , $studentEnrollment = null , $challanNo = null)
    {
      $sql = "
            SELECT *
            FROM fee_challan_data f
            WHERE (f.challan_no, f.version) IN (
                SELECT challan_no, MAX(version)
                FROM fee_challan_data
                GROUP BY challan_no
            )
        ";


        $bindings = [];
        $concat_challan_condition='';
        if ($fromIssueDate) {
            $sql .= " AND TRUNC(f.change_time) >= TO_DATE(:from_issue_date, 'YYYY-MM-DD')";
            $bindings['from_issue_date'] = $fromIssueDate;
        }

        if ($toIssueDate) {
            $sql .= " AND TRUNC(f.change_time) <= TO_DATE(:to_issue_date, 'YYYY-MM-DD')";
            $bindings['to_issue_date'] = $toIssueDate;
        }
        /* Student Enrollment Logic */
        if ($studentEnrollment) {

            // Numeric only → APP_ID
            if (ctype_digit($studentEnrollment)) {
                $sql .= " AND f.app_id = :app_id";
                $bindings['app_id'] = $studentEnrollment;
            }
            // Alphanumeric / hyphen → REGNO
            else {
                $sql .= " AND f.regno = :regno";
                $bindings['regno'] = $studentEnrollment;
            }
        }

        /* Challan No */
        if ($challanNo) {
            $sql .= " AND f.challan_no = :challan_no";
            $bindings['challan_no'] = $challanNo;
            $concat_challan_condition = 'AND fsd.CHALLAN_NO='.$challanNo;
        }
        $res = DB::select($sql, $bindings);
        // dd($res);

        foreach ($res as $key => $row) {
            // Same scholarship mapping as Middleware / export_with_version
            $this->attachExportScholarshipsToRow($row);
        }

        return response()->json($res);
    }

    /**
     * Shared scholarship attach for Middleware insert + Excel (with/without version).
     * Live discounts for regular/hostel; HISTORY mapped by challan for Credit_hours
     * and additional Full_semester (enrolled_credits > 0).
     */
    protected function attachExportScholarshipsToRow($row): void
    {
        $res2 = [];
        $hasSch = isset($row->challan_sch) && (float) $row->challan_sch > 0;
        $hasArrear = isset($row->scholarship_arrear) && (float) $row->scholarship_arrear > 0;
        $hasScholarshipSignal = $hasSch || $hasArrear;

        if (
            in_array($row->challan_type, ['Full_semester', 'Fee', 'Short/Summer_Course'], true)
            && (float) ($row->enrolled_credits ?? 0) == 0
            && $hasScholarshipSignal
        ) {
            $res2 = $this->fetchRegularHistoryScholarships($row);
            if (empty($res2)) {
                $sql2 = "SELECT
                            st.scholarship_name,
                            CASE
                                WHEN fsd.fsd_sch_instal_amnt IS NULL
                                THEN (fsd.fsd_discounted_amount + COALESCE((
                                    SELECT SUM(ssd.SSD_ARREAR)
                                    FROM students_scholarships_details ssd
                                    WHERE ssd.SSD_TYPE = fsd.fsd_scholarship_type
                                      AND ssd.ssd_sess_id = :session_id
                                      AND ssd.ssd_app_id = :enrollment_no
                                      AND ssd.STATUS = 'E'
                                      AND ssd.SSD_ARREAR > 0
                                ), 0))
                                ELSE (ROUND(fsd.fsd_sch_instal_amnt) + COALESCE((
                                    SELECT SUM(ssd.SSD_ARREAR)
                                    FROM students_scholarships_details ssd
                                    WHERE ssd.SSD_TYPE = fsd.fsd_scholarship_type
                                      AND ssd.ssd_sess_id = :session_id
                                      AND ssd.ssd_app_id = :enrollment_no
                                      AND ssd.STATUS = 'E'
                                      AND ssd.SSD_ARREAR > 0
                                ), 0))
                            END AS fsd_discounted_amount
                        FROM fin_scholarship_discounts fsd
                        LEFT JOIN scholarship_type st
                            ON st.scholarship_id = fsd.fsd_scholarship_type
                        WHERE fsd.fsd_aai_id = :enrollment_no
                          AND fsd.fsd_sesion_id = :session_id
                          AND fsd.FSD_SCH_TYPE IS NULL";

                $res2 = DB::select($sql2, [
                    'enrollment_no' => $row->app_id,
                    'session_id' => $row->sess_id,
                ]);
            }
        } elseif ($row->challan_type == 'Hostel_Fee' && $hasScholarshipSignal) {
            $sql2 = "SELECT
                        st.scholarship_name,
                        CASE
                            WHEN fsd.fsd_sch_instal_amnt IS NULL
                            THEN (fsd.fsd_discounted_amount + COALESCE((
                                SELECT SUM(ssd.SShD_ARREAR)
                                FROM FIN_STUDENTS_SCHOLARSHIPS_HOSTEL_DETAILS ssd
                                WHERE ssd.SShD_TYPE = fsd.fsd_scholarship_type
                                  AND ssd.sshd_sess_id = :session_id
                                  AND ssd.sshd_app_id = :enrollment_no
                                  AND ssd.sshd_STATUS = 'E'
                                  AND ssd.SShD_ARREAR > 0
                            ), 0))
                            ELSE (ROUND(fsd.fsd_sch_instal_amnt) + COALESCE((
                                SELECT SUM(ssd.SShD_ARREAR)
                                FROM FIN_STUDENTS_SCHOLARSHIPS_HOSTEL_DETAILS ssd
                                WHERE ssd.SShD_TYPE = fsd.fsd_scholarship_type
                                  AND ssd.sshd_sess_id = :session_id
                                  AND ssd.sshd_app_id = :enrollment_no
                                  AND ssd.sshd_STATUS = 'E'
                                  AND ssd.SShD_ARREAR > 0
                            ), 0))
                        END AS fsd_discounted_amount
                    FROM fin_scholarship_discounts fsd
                    LEFT JOIN scholarship_type st
                        ON st.scholarship_id = fsd.fsd_scholarship_type
                    WHERE fsd.fsd_aai_id = :enrollment_no
                      AND fsd.fsd_sesion_id = :session_id
                      AND fsd.FSD_SCH_TYPE = 'hostel'";

            $res2 = DB::select($sql2, [
                'enrollment_no' => $row->app_id,
                'session_id' => $row->sess_id,
            ]);
        } elseif ($this->shouldFetchCreditHourHistoryScholarships($row) && $hasScholarshipSignal) {
            $res2 = $this->fetchCreditHourHistoryScholarships($row);
            // If challan row has no scholarship_arrear, fill from history fsd_arrears (Excel + Middleware column)
            if ((!isset($row->scholarship_arrear) || (float) $row->scholarship_arrear <= 0) && !empty($res2)) {
                $historyArrearTotal = 0;
                foreach ($res2 as $scholarship) {
                    $historyArrearTotal += (float) ($scholarship->fsd_arrears ?? 0);
                }
                if ($historyArrearTotal > 0) {
                    $row->scholarship_arrear = $historyArrearTotal;
                }
            }
        }

        $row->scholarships = !empty($res2) ? array_map(function ($scholarship) {
            return [
                'scholarship_name' => $scholarship->scholarship_name ?? null,
                'fsd_discounted_amount' => $scholarship->fsd_discounted_amount ?? null,
            ];
        }, $res2) : null;
    }

    /**
     * History by challan for regular Full_semester / Fee / Short/Summer export rows.
     */
    protected function fetchRegularHistoryScholarships($row): array
    {
        $historyChallanIds = $this->resolveHistoryChallanIds($row);
        if (empty($historyChallanIds)) {
            return [];
        }

        if ($row->challan_type === 'Full_semester') {
            $historyCategorySql = "(fsd.ssd_challan_category IN ('Full_semester', 'additional_Full_semester') OR fsd.ssd_challan_category IS NULL)";
            $bindings = [
                'enrollment_no' => $row->app_id,
                'session_id' => $row->sess_id,
            ];
        } else {
            $historyCategorySql = '(fsd.ssd_challan_category = :challan_category OR fsd.ssd_challan_category IS NULL)';
            $bindings = [
                'enrollment_no' => $row->app_id,
                'session_id' => $row->sess_id,
                'challan_category' => $row->challan_type,
            ];
        }

        $sql = "SELECT
                    st.scholarship_name,
                    CASE
                        WHEN fsd.fsd_sch_instal_amnt IS NULL
                        THEN (fsd.fsd_discounted_amount + COALESCE(fsd.fsd_arrears, COALESCE((
                            SELECT SUM(ssd.SSD_ARREAR)
                            FROM students_scholarships_details ssd
                            WHERE ssd.SSD_TYPE = fsd.fsd_scholarship_type
                              AND ssd.ssd_sess_id = :session_id
                              AND ssd.ssd_app_id = :enrollment_no
                              AND ssd.STATUS = 'E'
                              AND ssd.SSD_ARREAR > 0
                        ), 0)))
                        ELSE (ROUND(fsd.fsd_sch_instal_amnt) + COALESCE(fsd.fsd_arrears, COALESCE((
                            SELECT SUM(ssd.SSD_ARREAR)
                            FROM students_scholarships_details ssd
                            WHERE ssd.SSD_TYPE = fsd.fsd_scholarship_type
                              AND ssd.ssd_sess_id = :session_id
                              AND ssd.ssd_app_id = :enrollment_no
                              AND ssd.STATUS = 'E'
                              AND ssd.SSD_ARREAR > 0
                        ), 0)))
                    END AS fsd_discounted_amount
                FROM FIN_SCHOLARSHIP_DISPLAY_HISTORY fsd
                LEFT JOIN scholarship_type st
                    ON st.scholarship_id = fsd.fsd_scholarship_type
                WHERE fsd.fsd_aai_id = :enrollment_no
                AND fsd.fsd_sesion_id = :session_id
                AND {$historyCategorySql}
                AND fsd.challan_id = :challanId
                AND fsd.FSD_SCH_TYPE IS NULL";

        return $this->selectHistoryScholarshipsByChallanIds($sql, $bindings, $historyChallanIds);
    }

    /**
     * Additional Full_semester (enrolled credits) and Credit_hours challans
     * use FIN_SCHOLARSHIP_DISPLAY_HISTORY for middleware export.
     */
    protected function shouldFetchCreditHourHistoryScholarships($row): bool
    {
        if ($row->challan_type === 'Credit_hours') {
            return true;
        }

        return $row->challan_type === 'Full_semester'
            && (float) ($row->enrolled_credits ?? 0) > 0;
    }

    /**
     * FIN_SCHOLARSHIP_DISPLAY_HISTORY may store student_challan.id or challan_no in challan_id.
     *
     * @return int[]
     */
    protected function resolveHistoryChallanIds($row): array
    {
        $rowArray = is_array($row) ? $row : get_object_vars($row);
        $candidates = [];

        foreach (['challan_id', 'challan_no'] as $field) {
            foreach ([$field, strtoupper($field)] as $key) {
                if (!isset($rowArray[$key]) || $rowArray[$key] === null || $rowArray[$key] === '') {
                    continue;
                }

                $candidate = (int) $rowArray[$key];
                if ($candidate > 0 && !in_array($candidate, $candidates, true)) {
                    $candidates[] = $candidate;
                }
            }
        }

        return $candidates;
    }

    protected function selectHistoryScholarshipsByChallanIds(string $sql, array $bindings, array $historyChallanIds): array
    {
        foreach ($historyChallanIds as $historyChallanId) {
            $queryBindings = array_merge($bindings, ['challanId' => $historyChallanId]);
            $scholarshipRows = DB::select($sql, $queryBindings);

            if (!empty($scholarshipRows)) {
                return $scholarshipRows;
            }
        }

        return [];
    }

    protected function fetchCreditHourHistoryScholarships($row): array
    {
        $historyChallanIds = $this->resolveHistoryChallanIds($row);
        if (empty($historyChallanIds)) {
            return [];
        }

        $isAdditionalFullSemester = $row->challan_type === 'Full_semester'
            && (float) ($row->enrolled_credits ?? 0) > 0;

        if ($isAdditionalFullSemester) {
            // Additional credit-hr challan → only additional_Full_semester history
            $historyCategorySql = "fsd.ssd_challan_category = 'additional_Full_semester'";
            $bindings = [
                'enrollment_no' => $row->app_id,
                'session_id' => $row->sess_id,
            ];
        } else {
            // Credit_hours challan → only Credit_hours history
            $historyCategorySql = 'fsd.ssd_challan_category = :challan_category';
            $bindings = [
                'enrollment_no' => $row->app_id,
                'session_id' => $row->sess_id,
                'challan_category' => $row->challan_type,
            ];
        }

        // History stores arrear on the same row (fsd_arrears) — use it directly.
        // Do not re-query students_scholarships_details (category mismatch made arrears null).
        $sql = "SELECT
                    st.scholarship_name,
                    CASE
                        WHEN fsd.fsd_sch_instal_amnt IS NULL
                        THEN (fsd.fsd_discounted_amount + COALESCE(fsd.fsd_arrears, 0))
                        ELSE (ROUND(fsd.fsd_sch_instal_amnt) + COALESCE(fsd.fsd_arrears, 0))
                    END AS fsd_discounted_amount,
                    COALESCE(fsd.fsd_arrears, 0) AS fsd_arrears
                FROM FIN_SCHOLARSHIP_DISPLAY_HISTORY fsd
                LEFT JOIN scholarship_type st
                    ON st.scholarship_id = fsd.fsd_scholarship_type
                WHERE fsd.fsd_aai_id = :enrollment_no
                AND fsd.fsd_sesion_id = :session_id
                AND {$historyCategorySql}
                AND fsd.challan_id = :challanId
                AND fsd.FSD_SCH_TYPE IS NULL";

        return $this->selectHistoryScholarshipsByChallanIds($sql, $bindings, $historyChallanIds);
    }

    function getChallanStudentsSummer($session_id)
    {
        $sql = "
            WITH student_prog_level AS (
                SELECT DISTINCT
                       sai.SAI_ENROLLMENT_NO,
                       prog.PROG_DEGREE_LEVEL
                FROM students_apply_info sai
                JOIN programs prog
                  ON sai.SAI_SDPO_PROG_ID = prog.PROG_ID
            ),
            valid_challan AS (
                SELECT *
                FROM (
                    SELECT sc.*,
                           ROW_NUMBER() OVER (
                               PARTITION BY sc.SC_ENROLLMENT_NO,
                                            sc.SC_SESSION_ID
                               ORDER BY sc.ID DESC
                           ) AS rn
                    FROM BNUCMS.STUDENT_CHALLAN sc
                    WHERE sc.SC_CHALLAN_TYPE = 'Short/Summer_Course'
                      AND sc.SC_ENROLLED_CR_HR IS NOT NULL
                      AND sc.SC_SESSION_ID = :session_id
                )
                WHERE rn = 1
            ),
            valid_fee_structure AS (
                SELECT *
                FROM (
                    SELECT fs.*,
                           ROW_NUMBER() OVER (
                               PARTITION BY fs.FFS_SCHOOL_ID,
                                            fs.FFS_SESS_ID,
                                            fs.FFS_DEGREE_LEVEL
                               ORDER BY fs.FFS_ID DESC
                           ) AS rn
                    FROM fin_fee_structure fs
                    JOIN student_prog_level spl
                      ON fs.FFS_DEGREE_LEVEL = spl.PROG_DEGREE_LEVEL
                    WHERE fs.FFS_FEE_TYPE = 'Short/Summer_Course'
                      AND fs.FFS_STATUS = 'E'
                      AND fs.FFS_CREDHR_AMT > 0
                      AND fs.FFS_SESS_ID = :session_id
                )
                WHERE rn = 1
            ),
            valid_student_courses AS (
                SELECT DISTINCT
                       sc.sc_sai_enrollment_no,
                       sc.sc_co_id,
                       sc.SC_CMS_COURSE_CO_ID,
                       sc.sc_course_id
                FROM student_courses sc
                WHERE sc.sc_enrollment_status = 1
                  AND sc.sc_status IN ('A', 'Block', 'Withdrawal')
                  AND NOT EXISTS (
                      SELECT 1
                      FROM course_exempted ce
                      WHERE ce.ce_course_id = sc.sc_course_id
                        AND ce.ce_session_id = :session_id
                        AND (
                            ce.ce_enrollment_no = sc.sc_sai_enrollment_no
                            OR (
                                (ce.ce_enrollment_no IS NULL
                                 OR TRIM(ce.ce_enrollment_no) IS NULL)
                                AND NOT EXISTS (
                                    SELECT 1
                                    FROM course_exempted ce_specific
                                    WHERE ce_specific.ce_session_id = :session_id
                                      AND ce_specific.ce_enrollment_no = sc.sc_sai_enrollment_no
                                      AND ce_specific.ce_course_id = sc.sc_course_id
                                )
                            )
                        )
                  )
            )
            SELECT
                   scs.sc_sai_enrollment_no       AS Reg_no,
                   spl.PROG_DEGREE_LEVEL          AS DEGREE_LEVEL,
                   c2.course_code,
                   c2.course_cd_name               AS course_name,
                   sc.id                           AS challan_no,
                   sc.invoiceamount                AS Total_challan_amount,
                   sc.SC_ACTIVITY_AMT              AS Activity_amount,
                   co.co_course_cr                 AS credit_hours,
                   co.co_school_id                 AS CMS_school_id,
                   scho.SCHOOL_CODE                AS OFFERED_SCHOOL,
                   scho.RODB_SCHOOL_ID             AS SCHOOL_ID,
                   ROUND(
                       co.co_course_cr * fs.ffs_credhr_amt,
                       0
                   ) AS course_fee
            FROM valid_student_courses scs
            JOIN courses_offered co
              ON scs.SC_CMS_COURSE_CO_ID = co.co_id
            LEFT JOIN schools scho
              ON co.CO_SCHOOL_ID = scho.SCHOOL_ID
            LEFT JOIN courses_two c2
              ON co.co_course_id = c2.COURSE_ID
            JOIN valid_challan sc
              ON sc.sc_enrollment_no = scs.sc_sai_enrollment_no
             AND sc.sc_session_id = co.co_sess_id
            JOIN student_prog_level spl
              ON scs.sc_sai_enrollment_no = spl.SAI_ENROLLMENT_NO
            JOIN valid_fee_structure fs
              ON fs.ffs_school_id = co.co_school_id
             AND fs.ffs_sess_id = co.co_sess_id
             AND fs.ffs_degree_level = spl.PROG_DEGREE_LEVEL
            WHERE co.co_sess_id = :session_id
            ORDER BY scs.sc_sai_enrollment_no ASC
        ";

        $res = DB::select($sql, ['session_id' => $session_id]);
        return response()->json($res);
    }






    function getChallanStudentsExcel()
    {
        $sql = "SELECT
            sc_issu_date as issue_date,
            sc_due_date as due_date,
            sc_valid_date as valid_date,
            challan_no as challan_no ,
            sc_session_id as sess_id,
            sc_challan_type as challan_type,
            check_fee_installments as installments,
            sc_enrollment_no as regno ,
            sc_app_id as app_id ,
            sc_studentname as studentname,
            sess_name as sess_code,
            prog_name as prog_name,
            school_code as schl_code,
            ffs_admission_amt as admission_amt,
            ffs_security_amt as security_amt,
            ffs_medical_amt as medical_amt,
            sc_activity_amt as activity_amt,
            sc_installment_tuition_fee_amnt as tuition_fee_amnt,
            sc_surplus_amount as surplus_amount,
            ffs_hostel_amt as hostel_amount,
            invoiceamount as invoice_amount,
            fine as fine,
            callback_amount as paid_amount,
            case when sc_enrolled_cr_hr is null then 0 else sc_enrolled_cr_hr end  AS enrolled_credits,
            sc_challan_status as challan_status
        FROM
            student_challan
        left join academic_sessions on sess_id = sc_session_id
        left join schools on school_id = sc_school_id
        left join programs on prog_id = sc_program_id
        WHERE
            sc_challan_type IN ('Full_semester', 'Fee', ' Full_semester','Hostel_Fee','Credit_hours')
            AND sc_session_id > 162";

        $res = DB::select($sql);

        // Loop through the main results to fetch additional scholarship data
        foreach ($res as $key => $row) {
            // Check the challan type and fetch scholarships accordingly
            if (in_array($row->challan_type, ['Full_semester', 'Fee', 'Short/Summer_Course']) && $row->enrolled_credits == 0) {
                $sql2 = "SELECT
                                scholarship_name,
                                CASE
                                    WHEN fsd_sch_instal_amnt IS NULL
                                    THEN fsd_discounted_amount
                                    ELSE ROUND(fsd_sch_instal_amnt)
                                END AS fsd_discounted_amount
                            FROM fin_scholarship_discounts
                            LEFT JOIN scholarship_type ON scholarship_id = fsd_scholarship_type
                            WHERE fsd_aai_id = :enrollment_no
                             and fsd_sesion_id = :session_id
                            AND FSD_SCH_TYPE is null";

                $res2 = DB::select($sql2, ['enrollment_no' => $row->app_id, 'session_id' => $row->sess_id]);
            } elseif ($row->challan_type == 'Hostel_Fee') {
                $sql2 = "SELECT
                                scholarship_name,
                                CASE
                                    WHEN fsd_sch_instal_amnt IS NULL
                                    THEN fsd_discounted_amount
                                    ELSE ROUND(fsd_sch_instal_amnt)
                                END AS fsd_discounted_amount
                            FROM fin_scholarship_discounts
                            LEFT JOIN scholarship_type ON scholarship_id = fsd_scholarship_type
                            WHERE fsd_aai_id = :enrollment_no
                              and fsd_sesion_id = :session_id
                            AND FSD_SCH_TYPE = 'hostel'";

                $res2 = DB::select($sql2, ['enrollment_no' => $row->app_id, 'session_id' => $row->sess_id]);
            } else {
                continue; // Skip to the next iteration if no conditions match
            }

            // Store scholarship details if found
            $row->scholarships = !empty($res2) ? array_map(function ($scholarship) {
                return [
                    'scholarship_name' => $scholarship->scholarship_name ?? null,
                    'fsd_discounted_amount' => $scholarship->fsd_discounted_amount ?? null,
                ];
            }, $res2) : null;
        }

        return response()->json($res);
    }



    public function deleteFine(Request $request)
    {
        DB::beginTransaction();

        try {
            // DB::table('fine_category_details')
            //     ->where('ID', $request->mainId)
            //     ->delete();

            DB::table('fine_category_details')
                ->where('ID', $request->mainId)
                ->update(['fcd_status' => 'D']);

            DB::commit();
            return response()->json([
                'success' => true,
                'data' => 'Deleted Successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'data' => 'Some problem occured'
            ]);
            // Handle the exception as needed
        }

    }

    public function getFinesData()
    {
        $sql = "select fine_category_details.id as fcd_id, fine_category.id,fine_category.fc_category_name,fine_category_details.fcd_violation_type,fine_category_details.fcd_fine_amount
        from fine_category_details
        left join fine_category on fine_category.id=fine_category_details.fc_id
        where fcd_status = 'E'
        order by fine_category_details.created_at desc
        ";
        $data = DB::select($sql);
        return $data;
    }
    public function getFineCategories()
    {
        // dd(auth()->user());
        $entryUserId = auth()->user()->employee_id;
        $groupId = auth()->user()->group_id;

        if ($groupId == 644 || auth()->user()->username == 'asif_nadeem') {
            $query = DB::table('fine_category')
                ->select('id', 'fc_category_name', 'FC_DESCRIPTION')->where('fc_status', 'E')->orderBy('id', 'desc');
        } else {
            $query = DB::table('fine_category_permissions')
                ->leftJoin('fine_category', 'fine_category_permissions.fc_id', '=', 'fine_category.id')
                ->select('fine_category.id', 'fine_category.fc_category_name')
                ->where('FCP_STATUS', 'E')
                ->where('fine_category_permissions.fcp_allowed_user', $entryUserId);
        }

        return $fine_categories = $query->get();
    }
    public function studentFineCategory(Request $request)
    {
        $sql = "select dept_id,dept_name from departments";

        $sql = "select id,fc_category_name, fc_description from fine_category where fc_status='E'";
        $categories = DB::select($sql);
        $getUsers = $this->getUsers();
        // dd($getUsers);
        $fine_categories = DB::select($sql);
        $fineCategoryUser = DB::table('fine_category_permissions')
            ->select('id', 'fc_id', 'employee_id', 'prefix', 'emp_f_name', 'emp_l_name', 'entry_user_id', 'fcp_effective_from') // Selecting specific columns including 'id'
            ->leftJoin('emp_employees', 'fine_category_permissions.fcp_allowed_user', '=', 'emp_employees.employee_id')
            ->where('fcp_status', 'E')
            ->get();

        // echo $sql;exit;
        // return   $fine_categories;
        return view('grader.StudentFines.addFineCategory', compact('categories', 'getUsers', 'fineCategoryUser'));
    }



    public function fineCategoryPermission(Request $request)
    {
        // Validate and sanitize inputs as needed
        $validatedData = $request->validate([
            'category' => 'required|integer',
            'user' => 'required|integer',
            'effective_from' => 'required|date'

        ]);

        // dd($request->all());
        // Insert data using Eloquent ORM
        $result = DB::table('fine_category_permissions')->insert([
            'fc_id' => $validatedData['category'],
            'fcp_status' => 'E',
            'fcp_allowed_user' => $validatedData['user'],
            'fcp_effective_from' => $validatedData['effective_from'],
            'entry_user_id' => auth()->user()->employee_id,
        ]);

        if ($result) {
            return response()->json(['status' => true, 'message' => 'Data inserted successfully']);
        } else {
            return response()->json(['status' => false, 'message' => 'Failed to insert data'], 500);
        }
    }

    public function deleteFineCategoryPermission(Request $request)
    {
        // Validate and sanitize inputs as needed for deletion
        $validatedData = $request->validate([
            'id' => 'required|integer',
        ]);

        // Delete data from fine_category_permissions table
        // $deleted = DB::table('fine_category_permissions')
        //             ->where('id', $validatedData['id'])
        //             ->delete();

        $deletedRow = DB::table('fine_category_permissions')
            ->where('id', $validatedData['id'])
            ->update(['fcp_status' => 'D']);

        if ($deletedRow > 0) {
            return response()->json(['status' => true, 'message' => 'Data deleted successfully']);
        } else {
            return response()->json(['status' => false, 'message' => 'Failed to delete data'], 500);
        }
    }

    function getUsers()
    {
        $epcddetails = "select employee_id,prefix,emp_f_name,emp_l_name from emp_employees";
        //echo $epcddetails;exit;
        return $resEpcdetails = DB::select($epcddetails);
    }

    public function addFineCategory(Request $request)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'categoryName' => 'required',
        ], [
            'categoryName.required' => 'Category is required',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return response()->json(['success' => false, 'errors' => $errors], 422);
        }

        try {
            $categoryName = $request->input('categoryName');
            $description = $request->input('description');
            $entryUserId = auth()->user()->employee_id;

            if ($request->input('verifyEdit') == '1') {
                $id = $request->input('id'); // Ensure 'id' is passed in the request for update

                $updateData = [
                    'fc_category_name' => $categoryName,
                    'fc_description' => $description,
                    'entry_user_id' => $entryUserId,
                    'entry_user_id' => $entryUserId,
                    'fc_status' => 'E'
                ];

                $updated = DB::table('fine_category')
                    ->where('id', $id)
                    ->update($updateData);

                if (!$updated) {
                    return response()->json(['message' => 'Failed to update fine'], 500);
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Fine updated successfully',
                    'challanRec' => $this->getFineCategories()
                ]);
            } else {
                $dbInsert = DB::table('fine_category')->insert([
                    'fc_category_name' => $categoryName,
                    'fc_description' => $description,
                    'entry_user_id' => $entryUserId,
                    'fc_status' => 'E'
                ]);

                if (!$dbInsert) {
                    throw new \Exception('DB Insertion Failed');
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Fine category inserted successfully',
                    'challanRec' => $this->getFineCategories()
                ]);
            }
        } catch (QueryException $e) {
            return response()->json(['success' => false, 'message' => 'Database Error: ' . $e->getMessage()]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function deleteFineCategory(Request $request)
    {
        DB::beginTransaction();

        try {
            // First, delete related records in the child table
            // DB::table('fine_category_details')
            // ->where('FC_ID', $request->mainId)
            // ->delete();

            // DB::table('fine_category_permissions')
            //     ->where('FC_ID', $request->mainId)
            //     ->delete();


            // Then, delete the record in the parent table
            // DB::table('fine_category')
            //     ->where('ID', $request->mainId)
            //     ->delete();

            DB::table('fine_category_details')
                ->where('FC_ID', $request->mainId)
                ->update(['fcd_status' => 'D']);

            DB::table('fine_category_permissions')
                ->where('FC_ID', $request->mainId)
                ->update(['fcp_status' => 'D']);

            DB::table('fine_category')
                ->where('ID', $request->mainId)
                ->update(['fc_status' => 'D']);

            DB::commit();
            return response()->json([
                'success' => true,
                'data' => 'Deleted Successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'data' => 'Some problem occured'
            ]);
            // Handle the exception as needed
        }

    }
    public function getStudentFineRec(Request $request)
    {
        $studentEnrollment = $request->studentEnrollment;
        $sql = "

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
        $data = DB::select($sql);
        $data = $data[0];


        $entryUserId = auth()->user()->id;
        $groupIds = [644];//privelliged group ids
        $usernames = ['asif_nadeem', 'esha.abid'];//privelliged usernames

        $groupId = auth()->user()->group_id;//current user id
        $username = auth()->user()->username;//current username

        $query = "
             SELECT
                 id,
                 sc_enrollment_no,
                 sc_app_id,
                 (SELECT sess_code FROM academic_sessions WHERE sess_id = sc_session_id) AS sess_name,
                 (SELECT sess_id FROM academic_sessions WHERE sess_id = sc_session_id) AS sess_id,
                 (SELECT fc_category_name FROM fine_category WHERE id = sc_category_id) AS category_name,
                (SELECT id FROM fine_category WHERE id = sc_category_id) AS category_id,
                 (SELECT fcd_violation_type FROM fine_category_details
                     WHERE fc_id = sc_category_id AND id = sc_violation_type_id) AS violation_type,
                (SELECT id FROM fine_category_details
                     WHERE fc_id = sc_category_id AND id = sc_violation_type_id) AS violation_id,
                 sc_amount,
                 created_at,
                 sc_challan_status,
                 sc_paid_on,
                 sc_feechallanno,
                 sc_validate_date,
                 SC_DUE_DATE
             FROM
                 student_challan
             WHERE
                 sc_enrollment_no = UPPER('$studentEnrollment')
                 and sc_challan_type='Fine'
                 ";

        if (!in_array($groupId, $groupIds) && !in_array($username, $usernames)) {
            $query .= " AND sc_entry_user_id = '" . auth()->user()->employee_id . "'";
        }

        $query .= " ORDER BY created_at DESC";
        //  echo $query;exit;

        $student_challan = DB::select($query);
        //  dd($data, $student_challan);






        return response()->json(['success' => true, 'getFinesData' => $data, 'student_challan' => $student_challan, 'fineCategories' => $this->getFineCategories(), 'sessions' => $this->getSessions()]);

    }
    public function StudentFine(Request $request)
    {

        return view('grader.StudentFines.studentFine');
    }
    protected function getSessions()
    {
        $sessions = DB::table('ACADEMIC_SESSIONS')
            ->select('*')
            ->where('SESS_STATUS', 'E')
            ->orderBy('SESS_ID', 'desc')
            ->get();
        return $sessions;
    }
    public function violationFines(Request $request)
    {
        // dd($request->all());
        $fine = DB::table('fine_category_details')
            ->select('fcd_fine_amount')
            ->where('id', $request->violationType)
            ->where('fc_id', $request->fineCategoryOption)
            ->first();
        return $fine;
    }

    public function violationtypes(Request $request)
    {
        try {
            $fine = DB::table('fine_category_details')
                ->where('fc_id', $request->fineCategoryOption)
                ->where('fcd_status', 'E')
                ->get();
            return response()->json(['success' => true, 'data' => $fine]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error fetching violation types', 'error' => $e->getMessage()]);
        }
    }

    public function generateFineChallan(Request $request)
    {
        try {
            $userGroupId = auth()->user()->group_id;
            $requireSessionOnlyWhen = in_array($userGroupId, [644, 324]);//software team and accounts team
            $validator = Validator::make($request->all(), [
                'enrollment_no' => 'required',
                'session' => $requireSessionOnlyWhen ? 'required' : 'nullable',
                //'active_sess_id' => 'required',
                'fineCategoryOption' => 'required',
                'violationType' => 'required',
                'fineAmount' => 'required',
            ], [
                'enrollment_no.required' => 'enrollment no is required',
                'session.required' => 'Session is required',
                // 'active_sess_id.required' => 'active session id is required',
                'fineCategoryOption.required' => 'Category Option is required',
                'violationType.required' => 'Violation Type is required',
                'fineAmount.required' => 'Fine Amount is required',
            ]);

            // Check if validation fails
            if ($validator->fails()) {
                $errors = $validator->errors()->toArray();
                return response()->json(['success' => false, 'errors' => $errors], 422);
            }


            $enrollment_no = strtoupper($request->enrollment_no);
            $active_sess_id = $request->active_sess_id;
            if ($requireSessionOnlyWhen) {
                $session = $request->session;
            } else {
                $session = DB::table('student_courses')
                    ->max('sc_co_sess_id');
            }
            $fineCategoryOption = $request->fineCategoryOption;
            $violationType = $request->violationType;
            $fineAmount = $request->fineAmount;

            // Category 38 = Gym (monthly). Only generate when the student has a monthly gym
            // subscription (SP_ID = 1) for the selected session whose challan was cancelled - i.e. the
            // old challan is present in FIN_STUDENT_CANCEL_CHALLAN (SUBSCRIPTIONS.CHALLAN_ID =
            // FIN_STUDENT_CANCEL_CHALLAN.CHALLAN_NO) - or that never had a challan yet (CHALLAN_ID null).
            $isGym = ((string) $fineCategoryOption === '38');
            $gymSubscription = null;
            if ($isGym) {
                $gymSubscription = DB::table('SUBSCRIPTIONS')
                    ->where('SP_ID', 1)
                    ->where('SESS_ID', $session)
                    ->whereRaw('LOWER(USER_ID) = ?', [strtolower($enrollment_no)])
                    ->where(function ($q) {
                        $q->whereNull('CHALLAN_ID')
                          ->orWhereIn('CHALLAN_ID', function ($sub) {
                              $sub->select('CHALLAN_NO')->from('FIN_STUDENT_CANCEL_CHALLAN');
                          });
                    })
                    ->first();

                if (!$gymSubscription) {
                    return response()->json(['success' => false, 'message' => 'No cancelled gym (monthly) subscription found for this student in the selected session.']);
                }
            }

            $challanType = $isGym ? 'Gym' : 'Fine';


            // $api_id = DB::table('students_apply_info')
            // ->leftJoin('admission_apply_info', 'students_apply_info.sai_api_id', '=', 'admission_apply_info.aai_api_id')
            // ->where('students_apply_info.sai_enrollment_no', $enrollment_no)
            // ->select('admission_apply_info.aai_id')
            // ->first();

            $api_id = DB::table('students_apply_info')
                ->leftJoin('admission_apply_info', function ($join) {
                    $join->on('students_apply_info.sai_api_id', '=', 'admission_apply_info.aai_api_id')
                        ->whereColumn('students_apply_info.sai_enrollment_no', '=', 'admission_apply_info.cms_sai_enrollment_no');
                })
                ->where('students_apply_info.sai_enrollment_no', $enrollment_no)
                ->selectRaw('COALESCE(admission_apply_info.aai_id, students_apply_info.sai_id) as aai_id')
                ->first();

            // dd($api_id);
            if (!$api_id) {
                return response()->json(['success' => false, 'message' => 'No API ID Found']);
            }

            // Use auth()->user()->employee_id instead of $entryUserId
            $entryUserId = auth()->user()->employee_id;

            $sql = "select sai_sdpo_school_id,
                    sai_sdpo_dept_id,
                    sai_sdpo_prog_id,
                    spi_name,
                    spi_fathername,
                    spi_guardian_cnic,
                    sai_student_status
                    from students_apply_info
                    left join students_personal_info on sai_spi_id = spi_id
                    where sai_enrollment_no = '$enrollment_no'";
            $res = DB::select($sql);

            //  dd($request->all()); // Uncomment this line for debugging if needed
            $dbInsert = DB::table('student_challan')->insertGetId([
                'sc_enrollment_no' => $enrollment_no,
                'sc_session_id' => $session,
                'sc_app_id' => $api_id->aai_id,
                'sc_category_id' => $fineCategoryOption,
                'sc_violation_type_id' => $violationType,
                'sc_amount' => $fineAmount,
                'invoiceamount' => $fineAmount,
                'sc_challan_type' => $challanType,
                'sc_installment_allowed' => 'NO',
                'sc_student_status' => 'Existing',
                'sc_entry_user_id' => auth()->user()->employee_id,
                'sc_entry_user_name' => auth()->user()->username,
                'sc_updated_by_username' => auth()->user()->username,
                'sc_payType' => 'ZindigiAPP',
                'sc_school_id' => $res[0]->sai_sdpo_school_id,
                'sc_department_id' => $res[0]->sai_sdpo_dept_id,
                'sc_program_id' => $res[0]->sai_sdpo_prog_id,
                'sc_studentname' => $res[0]->spi_name,
                'sc_fathername' => $res[0]->spi_fathername,
                'sc_guardian_cnic' => $res[0]->spi_guardian_cnic,
                'sc_sai_student_status' => $res[0]->sai_student_status,
                'sc_valid_date' => Carbon::now()->addMonths(6),
                'sc_due_date' => Carbon::now()->addDays(10)

            ]);

            $affected = DB::table('student_challan')
                ->where('id', $dbInsert)
                ->update([
                        'challan_no' => $dbInsert
                    ]);

            // Point the gym subscription at the newly generated challan and mark it unpaid
            if ($isGym && $gymSubscription) {
                DB::table('SUBSCRIPTIONS')
                    ->where('ID', $gymSubscription->id)
                    ->update([
                        'CHALLAN_ID' => $dbInsert,
                        'CHALLAN_STATUS' => 'UNPAID',
                        'UPDATED_AT' => now()->format('Y-m-d H:i:s'),
                    ]);
            }

            if (!$dbInsert) {
                return response()->json(['success' => false, 'message' => 'Challan Generation failed']);
            } else {
                return response()->json(['success' => true, 'message' => 'Challan Generated']);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error generating fine challan', 'error' => $e->getMessage()]);
        }
    }
    public function updateFineChallan(Request $request)
    {
        // dd($request->all());
        try {

            $validator = Validator::make($request->all(), [
                'edit_sess_id' => 'required',
                'edit_fineAmount' => 'required',
                'challan_id' => 'required',

            ], [
                'edit_sess_id.required' => 'session is required',
                'edit_fineAmount.required' => 'Fine amount is required',
                'challan_id.required' => 'Challan no is required',

            ]);

            // Check if validation fails
            if ($validator->fails()) {
                $errors = $validator->errors()->toArray();
                return response()->json(['success' => false, 'errors' => $errors], 422);
            }


            $session = $request->edit_sess_id;
            $fine = $request->edit_fineAmount;
            $challan_id = $request->challan_id;

            //  dd($request->all()); // Uncomment this line for debugging if needed
            $dbUpdateRow = DB::table('student_challan')
                ->where('id', $challan_id) // Add the condition to match the row by its ID
                ->update([
                    'sc_session_id' => $session,
                    'sc_amount' => $fine,
                    'invoiceamount' => $fine,
                ]);

            if ($dbUpdateRow > 0) {
                return response()->json(['success' => true, 'message' => 'Challan Updated']);
            } else {
                return response()->json(['success' => false, 'message' => 'Challan Generation failed']);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error generating fine challan', 'error' => $e->getMessage()]);
        }
    }
    public function fineChallanStudentList(Request $request)
    {
        // dd(1111111111111);
        $student_challan = DB::select("
                        select
                        (prefix || ' ' || emp_f_name || ' ' ||emp_l_name) as entry_username,
                        id,
                        sc_enrollment_no,
                        (select sess_code from academic_sessions where sess_id=sc_session_id) as sess_name,
                        (select fc_category_name from fine_category where id=sc_category_id) as category_name,
                        (select fcd_violation_type from fine_category_details where fc_id=sc_category_id and id=sc_violation_type_id) violation_type ,
                        sc_amount,
                        created_at,sc_challan_status,sc_paid_on,
                        challan_no as sc_feechallanno
                        ,sc_validate_date
                        from student_challan st
                        left join emp_employees on st.sc_entry_user_id=emp_employees.employee_id
                ");
        $sql = " SELECT
                *
                 FROM
                fine_category";
        $resSql = DB::select($sql);

        return view('grader.StudentFines.studentList', compact('student_challan', 'resSql'));
    }

    public function getStudentFineList(Request $request)
    {

        $studentID = $request->studentID; // Assuming $request is an instance of Illuminate\Http\Request
        $challanNO = $request->get('challanNO');
        $fromDate = $request->get('fromDate'); // Assuming 'fromDate' is the parameter for the starting date                ,

        $toDate = $request->get('toDate');
        $paymentType = $request->get('paymenttype');
        $catgoryType = $request->get('catgoryType');
        // dd($paymentType);
        $studentQuery = "
           SELECT
               (prefix || ' ' || emp_f_name || ' ' ||emp_l_name) AS entry_username,
               id,
               sc_app_id,
               tranfee,
               callback_amount,
               callback_at,

                 (
                    CASE
                        WHEN sc_challan_type = 'Fee' THEN  (
                            SELECT
                                validdate
                            FROM
                                mv_feechallanwisesummary
                            WHERE
                                buyercodechallanno = challan_no

                        ) else sc_valid_date
                    END
                ) AS sc_validate_date,
                (
                    CASE
                        WHEN sc_challan_type = 'Fee' THEN  (
                        SELECT
                            duedate
                        FROM
                            mv_feechallanwisesummary
                        WHERE
                            buyercodechallanno = challan_no
                        ) else sc_due_date
                    end
                ) AS duedate,
               student_challan.sc_enrollment_no,
               (SELECT sess_code FROM academic_sessions WHERE sess_id=sc_session_id) AS sess_name,
               (SELECT fc_category_name FROM fine_category WHERE id = sc_category_id) AS category_name,
               sc_challan_type,
                (SELECT fcd_violation_type FROM fine_category_details WHERE fc_id=sc_category_id AND id=sc_violation_type_id) AS violation_type,
                 CASE
                    WHEN sc_amount IS NOT NULL THEN sc_amount
                    ELSE CASE
                        WHEN REGEXP_LIKE(invoiceamount, '^[0-9]+(\.[0-9]+)?$') THEN TO_NUMBER(invoiceamount)
                        ELSE NULL
                    END
                END AS sc_amount,
               created_at,
               sc_challan_status,
               response_paidon,
               challan_no as sc_feechallanno,
               response_status,
               sc_paytype,
               CASE
                    WHEN REGEXP_LIKE(invoiceamount, '^[0-9]+(\.[0-9]+)?$') THEN TO_NUMBER(invoiceamount)
                    ELSE NULL
                END AS invoiceamount,
               fine,
               sc_payment_mode,
                (select course_title from   SC_SHORTCOURSE_APPLIED
                 LEFT JOIN SC_SHORTCOURSES ON SC_SHORTCOURSES.id = course_id
                where     to_char(applicant_id) = student_challan.sc_enrollment_no and course_id is not null and rownum = 1) as course_name

           FROM
               student_challan
           LEFT JOIN
               emp_employees ON student_challan.sc_entry_user_id=emp_employees.employee_id
           WHERE
                sc_challan_type in ('Full_semester','Admission_Fee','Fine','Fee','Processing_Fee','short_Fee','Hostel_Fee','Success_Lab','Summer_School','Gym','Credit_hours','Short/Summer_Course','Fee_Refund','shortcourse', 'donation', 'misc_fee') and sc_challan_status ='PAID'
                and invoice_status = 'E'";

        $bindings = [];

        if (isset($studentID) && $studentID !== null) {
            //    $studentQuery .= " AND sc_enrollment_no = ?";
            //    $bindings[] = $studentID;
            $studentQuery .= " AND (sc_enrollment_no = '$studentID' OR to_char(sc_app_id) = '$studentID')";
        }
        if (isset($paymentType) && $paymentType !== null) {
            // $studentQuery .= " AND sc_paytype = '$paymentType'";
            // $bindings[] = $paymentType;

            if ($paymentType == 'Askari') {
                $studentQuery .= " AND sc_paytype IN ('Askari', 'ASKRI')";
            } elseif (!empty($paymentType)) {
                $studentQuery .= " AND sc_paytype = ?";
                $bindings[] = $paymentType;
            }
        }
        if (isset($challanNO) && $challanNO !== null) {
            $studentQuery .= " AND challan_no = ? ";
            $bindings[] = $challanNO;
        }
        if (isset($catgoryType) && $catgoryType !== null) {
            $studentQuery .= " AND sc_category_id = ? ";
            $bindings[] = $catgoryType;
        }

        if (isset($fromDate) && isset($toDate)) {
            // Assuming 'fromDate' and 'toDate' are in 'Y-m-d' format
            // $studentQuery .= " AND callback_at BETWEEN ? AND ?";
            // $bindings[] = Carbon::parse($fromDate)->format('Y-m-d');
            // $bindings[] = Carbon::parse($toDate)->format('Y-m-d');

            $date = Carbon::createFromFormat('Y-m-d', $fromDate);
            $formattedDate = $date->format('d-M-y');
            $finalFromDate = strtoupper($formattedDate); // Output: 03-Apr-24
            $date = Carbon::createFromFormat('Y-m-d', $toDate);
            $formattedDate2 = $date->format('d-M-y');
            $finalToDate = strtoupper($formattedDate2);


            $studentQuery .= " AND TRUNC(callback_at) BETWEEN TO_DATE('$finalFromDate', 'DD-MON-RR') AND TO_DATE('$finalToDate', 'DD-MON-RR')";
        }

        //  echo $studentQuery;exit;

        $student = DB::select($studentQuery, $bindings);

        return response()->json(['success' => true, 'student_challan' => $student]);

    }

    public function reSyncnStudentList(Request $request)
    {
        $challanNO = $request->get('challanNO');

        $validator = Validator::make($request->all(), [
            'fromDate' => 'required',
            'toDate' => 'required',
        ]);

        if ($request->get('type') == 'ubl_card') {
            return $this->handleUBLsync($fromDate, $toDate);
        }

        // Check if validation fails
        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return response()->json(['success' => false, 'errors' => $errors], 422);
        }
        $fromDate = Carbon::parse($request->get('fromDate'))->format("Y-m-d");
        $toDate = Carbon::parse($request->get('toDate'))->format("Y-m-d");

        $blinqToken = $this->getFinalToken();
        $blinqInvoice = $this->genInvoiceOnBlinq($blinqToken, $fromDate, $toDate);



        if (count($blinqInvoice) > 0) {

            foreach ($blinqInvoice as $rec) {

                $blinqInvoiceStatus = $this->genInvoiceStatusOnBlinq($blinqToken, $rec["InvoiceNumber"]);
                //dd( $blinqInvoiceStatus[0]["PaymentCode"],$blinqInvoiceStatus );
                //  $sel= DB::select("select * from student_challan where challan_no='79388'");
                $callbackDate = (isset($blinqInvoiceStatus[0]["InvoicePaidOn"]) && $blinqInvoiceStatus[0]["InvoicePaidOn"] != "") ? $blinqInvoiceStatus[0]["InvoicePaidOn"] : $blinqInvoiceStatus[0]["InvoiceIssueDate"];

                // if($sel[0]->id){
                //    $affected = DB::table('student_challan')
                //    ->where('paymentcode',  $blinqInvoiceStatus[0]["PaymentCode"])
                //    ->update([
                //     'invoice_status'=>'E',
                //     'sc_payment_mode'=> $blinqInvoiceStatus[0]["PaymentMode"],
                //     'response_status'=>'Success',
                //     'sc_paytype'=>'blinq_app',
                //     'callback_at' => $callbackDate->format('Y-m-d H:i:s'),
                //     'callback_paid_on' => $callbackDate->format('Y-m-d H:i:s'),
                //     'callback_amount' =>$blinqInvoiceStatus[0]["InvoiceAmount"]

                //    ]);
                $amount = $rec["AmountPaid"];
                $paymentCode = $blinqInvoiceStatus[0]["PaymentCode"];
                $paymentMode = $blinqInvoiceStatus[0]["PaymentMode"];
                $InvoiceNumber = $rec["InvoiceNumber"];



                $sql = "UPDATE student_challan
                       SET
                           callback_paid_on = to_Date('$callbackDate', 'Day, DD Month YYYY HH24:MI:SS'),
                           callback_amount ='$amount',
                           sc_payment_mode='$paymentMode',
                           CALLBACK_AT = to_Date('$callbackDate', 'Day, DD Month YYYY HH24:MI:SS'),
                           response_status ='Success',
                           sc_paytype='blinq_app',
                           invoice_status='E',
                           sc_challan_status ='PAID'
                       WHERE paymentcode ='$paymentCode'";

                // echo $sql;exit;
                $res = DB::insert($sql);

                $studentRec = DB::table('student_challan')
                    ->select('*')
                    ->where('paymentcode', $paymentCode)
                    ->first();
                echo print_r($InvoiceNumber, true) . '<br>';
                if ($studentRec && $studentRec->sc_enrollment_no) {

                    $procedurenotification = 'UNBLOCK_STUDENT_ONLINE_PAY';
                    $bindings_notification = [
                        'enrollment_No' => $studentRec->sc_enrollment_no,
                        'challan_no' => $InvoiceNumber
                    ];

                    // $result_notification = DB::executeProcedure($procedurenotification, $bindings_notification);

                }




                // };
            }


        }

        if (!$sql) {
            return response()->json(['success' => false, 'message' => 'Challan Sync failed']);
        } else {
            return response()->json(['success' => true, 'message' => 'Challan Sync']);
        }

    }


    private function handleUBLsync($fromDate, $toDate)
    {


        // Fetch challans within date range
        $results = DB::table('student_challan')
            ->orWhere('clicktopayurl', 'like', '%https://ipg.comtrust.ae/%')
            ->whereNull('callback_paid_on')
            ->orWhere('sc_paytype', 'ubl_card')
            ->where('invoice_status', 'E')
            ->get();


        foreach ($results as $challan) {
            $transactionID = $challan->invoicenumber; // fallback or make dynamic

            $payload = [
                "QueryTransaction" => [
                    "TransactionID" => $transactionID,
                    "Customer" => "BEACONHOUSE UNIVERSITY",
                    "UserName" => "BHU_Zain",
                    "Password" => "BeAcon(}@House{){BHU"
                ]
            ];

            try {
                $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ])->post('https://ipg.comtrust.ae:2443', $payload);

                $json = $response->json();

                if ($response->status() === 200 && isset($json['Transaction']['Status']['Name']) && in_array($json['Transaction']['Status']['Name'], ['Closed', 'Open'])) {
                    $date = $json['Transaction']['Details']['Detail'][2]['Date'] ?? null;
                    $amount = $json['Transaction']['Transaction']['Amount']['Value'] ?? null;
                    $invoiceNumber = $json['Transaction']['Transaction']['OrderID'] ?? null;



                    $sql = "UPDATE student_challan
                SET
                    callback_paid_on = TO_TIMESTAMP(?, 'YYYY-MM-DD HH24:MI:SS'),
                    callback_amount = ?,
                    callback_at = TO_TIMESTAMP(?, 'YYYY-MM-DD HH24:MI:SS'),
                    response_status = 'Success',
                    sc_challan_status = 'PAID',
                    sc_paytype = 'ubl_card',
                    invoice_status = 'E'
                WHERE id = ?";


                    $res = DB::update($sql, [$date, $amount, $date, $invoiceNumber]);
                    //dd( $date, $amount,$invoiceNumber,$res);
                }



            } catch (\Exception $e) {
                $results[] = [
                    'challan_no' => $challan->invoicenumber,
                    'transaction_id' => $transactionID,
                    'error' => $e->getMessage(),
                ];
            }
        }

        return response()->json([
            'success' => true,
            'data' => $results
        ]);
        return response()->json(['success' => true, 'message' => 'Challan Sync (UBL) - logic pending']);
    }


    protected function getFinalToken()
    {
        $blinqToken = '';
        $blinq = $this->getTokenFromDB();
        $this->getTokenFromDB();
        $valid_till = Carbon::parse($blinq->valid_till);
        // dd($valid_till,Carbon::now().',Carbon::parse($valid_till)->lessThan(Carbon::now()));
        if ($valid_till != null && !Carbon::parse($valid_till)->lessThan(Carbon::now())) {
            $blinqToken = $blinq->auth_token;
        } else {
            $blinqToken = $this->getToken();
            if (!$blinqToken->original['error']) {
                $blinqToken = $blinqToken->original['token'];
            } else {
                return false;
            }
        }
        return $blinqToken;
    }

    protected function getTokenFromDB()
    {
        $sql = "Select * from epay where provider  = 'Blinq' and account_type='forward_charges' ";

        $res = DB::select($sql);
        return $res[0];
    }


    protected function genInvoiceOnBlinq($token, $from, $to)
    {


        // dd(response()->json($data));
        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'token' => $token
        ])->get('https://api.blinq.pk/invoice/getpaidinvoices?startDate=' . $from . '&endDate=' . $to);

        if ($response->successful()) {

            return $response['ResponseDetail'];
        }

    }

    protected function genInvoiceStatusOnBlinq($token, $invoiceNumber)
    {

        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'token' => $token
        ])->get('https://api.blinq.pk/invoice/getstatus?InvoiceNumber=' . $invoiceNumber);
        if ($response->successful()) {
            //dd($response['ResponseDetail']);
            return $response['ResponseDetail'];
        }

    }

    protected function getToken()
    {
        $sql = "Select * from epay where provider  = 'Blinq' and account_type='forward_charges'";
        $res = DB::select($sql);

        $clientID = $res[0]->clientid;
        $clientSecret = $res[0]->clientsecret;

        $response = Http::withHeaders([
            // Any additional headers you may need
            'Accept' => 'application/json',
        ])->post('https://api.blinq.pk/api/Auth', [
                    'ClientID' => $clientID,
                    'ClientSecret' => $clientSecret,
                    // Any other query parameters required by the API
                ]);
        $validTill = $this->updateToken($response->header('token'), $response->header('TokenExpiry'));

        if ($response->successful()) {
            return response()->json([
                'error' => false,
                'token' => $response->header('token'),
                'valid_till' => $validTill

            ]);
        } else {
            return false;
        }
    }
    protected function updateToken($token, $time)
    {
        $currentTime = Carbon::now();
        $validTill = $currentTime->addMinutes($time);
        $validTill = $validTill->format('y-M-d h:i:s');
        // dd($validTill);
        $sql = "UPDATE epay set AUTH_TOKEN = ' $token ', VALID_TILL = ' $validTill ' where provider = 'Blinq' and account_type='forward_charges'";
        // echo $sql ; exit;
        $res = DB::update($sql);
        return $validTill;
    }
    public function cancelChallans(Request $request)
    {
        // Get the challan ID and remarks from the request
        // dd($request->all());
        $id = $request->challanId;
        $remarks = $request->remarks;

        try {
            // Start a database transaction
            DB::beginTransaction();

            // Update the challan status to 'CANCELLED'
            DB::table('student_challan')
                ->where('id', $id)
                ->update([
                        'SC_CHALLAN_STATUS' => 'CANCELLED',
                        'UPDATED_AT' => Carbon::now(),
                        'sc_cancelled_by_user' => auth()->user()->username,
                        'sc_cancel_remarks' => $remarks
                    ]);

            // Commit the transaction
            DB::commit();

            // Return a successful response
            return response()->json(['success' => true, 'message' => 'Challan Cancelled Successfully']);
        } catch (Exception $e) {
            // Rollback the transaction in case of an error
            DB::rollBack();

            // Return an error response with the exception message
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function activateChallans(Request $request)
    {
        // dd($request->challanId);
        $id = $request->challanId;
        // $user_id = auth()->user()->username;
        try {
            // Start a database transaction
            DB::beginTransaction();
            // Update the challan status to 'CANCELLED'
            DB::table('student_challan')
                ->where('id', $id)
                ->update([
                        'SC_CHALLAN_STATUS' => 'UNPAID',
                        'UPDATED_AT' => Carbon::now(),
                        'sc_cancelled_by_user' => auth()->user()->username,
                    ]);
            // Commit the transaction
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Challan Activated Successfully']);
        } catch (Exception $e) {
            // Rollback the transaction on error
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
