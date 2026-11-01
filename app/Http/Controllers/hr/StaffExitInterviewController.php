<?php

/**
 * Created by PhpStorm.
 * User: Maher Asad
 * Date: 5/14/2019
 * Time: 9:52 AM
 */

namespace App\Http\Controllers\hr;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Symfony\Component\Console\Helper\Table;
use DB;
use Illuminate\Support\Facades\Input;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use PDF;
class StaffExitInterviewController extends Controller
{
    public function index()
    {
        return view('hr.index');
    }

    public function create()
    {
        $this->authorizePmsReport();

        return view('hr.pms_report');
    }

    public function generatePmsExcel()
    {
        $this->authorizePmsReport();

        $rows = DB::select("SELECT username, category, priority, target_sem, goal_type, goals_data FROM fpms_goals");

        $exportRows = [];
        foreach ($rows as $row) {
            $row = (array) $row;
            $row = array_change_key_case($row, CASE_LOWER);

            $goalsData = $row['goals_data'] ?? '';
            if (is_resource($goalsData)) {
                $goalsData = stream_get_contents($goalsData);
            } elseif (is_object($goalsData) && method_exists($goalsData, 'load')) {
                $goalsData = $goalsData->load();
            } elseif (is_array($goalsData) || is_object($goalsData)) {
                $goalsData = json_encode($goalsData);
            }

            $exportRows[] = [
                $row['username'] ?? '',
                $row['category'] ?? '',
                $row['priority'] ?? '',
                $row['target_sem'] ?? '',
                $row['goal_type'] ?? '',
                $goalsData,
            ];
        }

        $fileName = 'PMS_Report_' . Carbon::now('Asia/Karachi')->format('Y-m-d_His') . '.xlsx';

        return Excel::download(new class($exportRows) implements \Maatwebsite\Excel\Concerns\FromArray, \Maatwebsite\Excel\Concerns\WithHeadings {
            protected $rows;

            public function __construct(array $rows)
            {
                $this->rows = $rows;
            }

            public function headings(): array
            {
                return ['Username', 'Category', 'Priority', 'Target Sem', 'Goal Type', 'Goals Data'];
            }

            public function array(): array
            {
                return $this->rows;
            }
        }, $fileName);
    }

    private function authorizePmsReport()
    {
        $username = strtolower(auth()->user()->username ?? '');
        if (!in_array($username, ['irfan.bajwa', 'bilal.saleem'], true)) {
            abort(403);
        }
    }
    public function staffExitInterview()
    {

        $username = strtolower(auth()->user()->username);
        // $sql = "SELECT
        //            *
        //        FROM
        //            sisusers
        //            where username = '$username' ";
        //    echo  $sql;exit;
        // $res = DB::select($sql);
        // dd($res);
        $sqlChkForm = "SELECT
*
FROM
    university_leaving_request
WHERE
    ulr_enrollment_no = '$username'";
        $sqlChkForm = DB::select($sqlChkForm);

        return view(
            'hr.staff_uni_exit_interview',
            [
                'sqlChkForm' => count($sqlChkForm)
            ]

        );
    }

    public function lowerStaffExitInterview()
    {
        return view(
            'hr.lower_staff_exist_form',
        );
    }


    public function filter(Request $request)
    {
        // $request->validate([
        //     'from_date' => 'required|date',
        //     'to_date' => 'required|date',
        // ]);

        $from_date = $request->input('from_date');
        $to_date = $request->input('to_date');

        $requests = DB::table('university_leaving_request')
            ->whereBetween('CREATED_AT', [$from_date, $to_date])
            ->where(function ($query) {
                $query->where('QUESTION', 'LIKE', '%Pay, benefits%')
                ->orWhere('QUESTION', 'LIKE', '%workload usually%')
                ->orWhere('QUESTION', 'LIKE', '%Principal reason%')
                ->orWhere('QUESTION', 'LIKE', '%What improvements (to%');
                    ;
            })

            //->where('question', 'Principal reason for leaving')
            ->get(['ULR_ENROLLMENT_NO', 'ANSWER', 'QUESTION', 'CREATED_AT']);

        return view('hr.index', compact('requests'));
    }

    public function generatePDF(Request $request)
    {
        $from_date = $request->input('from_date');
        $to_date = $request->input('to_date');

        $requests = DB::table('university_leaving_request')
            ->whereBetween('CREATED_AT', [$from_date, $to_date])
            ->where(function ($query) {
                $query->where('QUESTION', 'LIKE', '%Pay, benefits%')
                      ->orWhere('QUESTION', 'LIKE', '%workload usually%')
                      ->orWhere('QUESTION', 'LIKE', '%Principal reason%')
                      ->orWhere('QUESTION', 'LIKE', '%What improvements (to%');
            })
            ->get(['ULR_ENROLLMENT_NO', 'ANSWER', 'QUESTION', 'CREATED_AT']);


            // dd($requests);
        $pdf = PDF::loadView('hr.pdf', compact('requests', 'from_date', 'to_date'));

        return $pdf->download('University_Leaving_Requests.pdf');
    }





    public function showList()
    {
        // 1. Fetch existing employee exit requests from BNU_EMPLOYEE_EXIT_REQUEST_LIST
        $sql = "SELECT 
                    be.EE_ID,
                    be.EMPLOYEE_ID,
                    COALESCE(
                        be.EMPLOYEE_NAME,
                        su.full_name,
                        emp.emp_f_name || ' ' || emp.emp_l_name,
                        be.EMPLOYEE_ID
                    ) AS EMPLOYEE_NAME,
                    be.ENTRY_DATE,
                    COALESCE(
                        be.DESIGNATION,
                        su.designation,
                        (SELECT MAX(ulr.designation) FROM university_leaving_request ulr WHERE LOWER(TRIM(ulr.ulr_enrollment_no)) = LOWER(TRIM(be.EMPLOYEE_ID))),
                        'N/A'
                    ) AS DESIGNATION,
                    be.RECIVED_BY_HR
                FROM BNU_EMPLOYEE_EXIT_REQUEST_LIST be
                LEFT JOIN sisusers su ON LOWER(TRIM(su.username)) = LOWER(TRIM(be.EMPLOYEE_ID))
                LEFT JOIN emp_employees emp ON LOWER(TRIM(emp.emp_ad_username)) = LOWER(TRIM(be.EMPLOYEE_ID))
                ORDER BY be.EE_ID DESC";
        $res = DB::select($sql);

        // 2. Fetch missing employee exit requests from university_leaving_request
        // Exclude student roll numbers (e.g. F20..., S20..., SUM20..., etc.)
        $missingSql = "SELECT 
                            NULL AS EE_ID,
                            ulr.ulr_enrollment_no AS EMPLOYEE_ID,
                            COALESCE(
                                MAX(ulr.fullname),
                                MAX(su.full_name),
                                MAX(emp.emp_f_name || ' ' || emp.emp_l_name),
                                ulr.ulr_enrollment_no
                            ) AS EMPLOYEE_NAME,
                            COALESCE(
                                TO_CHAR(MAX(ulr.created_at), 'HH24:MI:SS DD-MM-YY'),
                                MAX(TO_CHAR(ulr.created_at)),
                                'N/A'
                            ) AS ENTRY_DATE,
                            COALESCE(
                                MAX(ulr.designation),
                                MAX(su.designation),
                                'N/A'
                            ) AS DESIGNATION,
                            NULL AS RECIVED_BY_HR
                        FROM university_leaving_request ulr
                        LEFT JOIN sisusers su ON LOWER(TRIM(su.username)) = LOWER(TRIM(ulr.ulr_enrollment_no))
                        LEFT JOIN emp_employees emp ON LOWER(TRIM(emp.emp_ad_username)) = LOWER(TRIM(ulr.ulr_enrollment_no))
                        WHERE 
                            ulr.ulr_enrollment_no IS NOT NULL
                            AND NOT REGEXP_LIKE(ulr.ulr_enrollment_no, '^((F|S|W|FA|SP|SU|SUM|WIN)[0-9])', 'i')
                            AND LOWER(TRIM(ulr.ulr_enrollment_no)) NOT IN (SELECT LOWER(TRIM(sai.sai_enrollment_no)) FROM students_apply_info sai WHERE sai.sai_enrollment_no IS NOT NULL)
                            AND LOWER(TRIM(ulr.ulr_enrollment_no)) NOT IN (SELECT LOWER(TRIM(s.username)) FROM sisusers s WHERE s.group_id = 4 AND s.username IS NOT NULL)
                            AND LOWER(TRIM(ulr.ulr_enrollment_no)) NOT IN (SELECT LOWER(TRIM(b.EMPLOYEE_ID)) FROM BNU_EMPLOYEE_EXIT_REQUEST_LIST b WHERE b.EMPLOYEE_ID IS NOT NULL)
                        GROUP BY ulr.ulr_enrollment_no";
        $missingRes = DB::select($missingSql);

        $existingEmpIds = [];
        $allEmployees = [];

        foreach ($res as $row) {
            $rowArr = (array) $row;
            $rowArr = array_change_key_case($rowArr, CASE_LOWER);

            $empId = trim($rowArr['employee_id'] ?? '');
            $empName = trim($rowArr['employee_name'] ?? '');
            $entryDate = trim($rowArr['entry_date'] ?? '');
            $designation = trim($rowArr['designation'] ?? '');
            $recivedByHr = $rowArr['recived_by_hr'] ?? null;

            $emp = (object) [
                'ee_id' => $rowArr['ee_id'] ?? null,
                'employee_id' => $empId,
                'employee_name' => $empName !== '' ? $empName : $empId,
                'entry_date' => $entryDate,
                'designation' => $designation !== '' ? $designation : 'N/A',
                'recived_by_hr' => $recivedByHr,
            ];

            $normId = strtolower($empId);
            if ($normId !== '') {
                $existingEmpIds[$normId] = true;
            }
            $allEmployees[] = $emp;
        }

        foreach ($missingRes as $row) {
            $rowArr = (array) $row;
            $rowArr = array_change_key_case($rowArr, CASE_LOWER);

            $empId = trim($rowArr['employee_id'] ?? '');
            $normId = strtolower($empId);

            if ($normId === '' || isset($existingEmpIds[$normId])) {
                continue;
            }

            // Exclude student roll numbers (e.g. F20..., S20..., SUM20..., etc.)
            if (preg_match('/^(F|S|W|FA|SP|SU|SUM|WIN)[0-9]/i', $normId)) {
                continue;
            }

            $empName = trim($rowArr['employee_name'] ?? '');
            $entryDate = trim($rowArr['entry_date'] ?? '');
            $designation = trim($rowArr['designation'] ?? '');

            $emp = (object) [
                'ee_id' => null,
                'employee_id' => $empId,
                'employee_name' => $empName !== '' ? $empName : $empId,
                'entry_date' => $entryDate !== '' ? $entryDate : 'N/A',
                'designation' => $designation !== '' ? $designation : 'N/A',
                'recived_by_hr' => null,
            ];

            $existingEmpIds[$normId] = true;
            $allEmployees[] = $emp;
        }

        // Helper to check if received by HR
        $isAccepted = function ($employee) {
            $val = $employee->recived_by_hr ?? null;
            if (empty($val)) {
                return false;
            }
            if (is_string($val)) {
                $lower = strtolower(trim($val));
                if ($lower === 'accepted' || $lower === '6163636570746564') {
                    return true;
                }
            }
            return bin2hex((string)$val) === bin2hex('accepted')
                || bin2hex((string)$val) === '6163636570746564';
        };

        // Separate accepted and pending lists
        $accepted = array_values(array_filter($allEmployees, function ($employee) use ($isAccepted) {
            if ($isAccepted($employee)) {
                $employee->recived_by_hr = 'Accepted';
                return true;
            }
            return false;
        }));

        $pending = array_values(array_filter($allEmployees, function ($employee) use ($isAccepted) {
            return !$isAccepted($employee);
        }));

        return view('hr.staff_request_exit_list', ['accepted' => $accepted, 'pending' => $pending]);
    }

    public function acceptReceivedStatus($id)
    {
        $id = trim($id);
        // Convert 'accepted' to its hexadecimal representation
        $acceptedHex = bin2hex('accepted');

        // Check if record already exists in BNU_EMPLOYEE_EXIT_REQUEST_LIST
        $exists = DB::select("SELECT EMPLOYEE_ID FROM BNU_EMPLOYEE_EXIT_REQUEST_LIST WHERE LOWER(TRIM(EMPLOYEE_ID)) = LOWER(TRIM(?))", [$id]);

        if (!empty($exists)) {
            $sql = "UPDATE BNU_EMPLOYEE_EXIT_REQUEST_LIST SET RECIVED_BY_HR = HEXTORAW(?) WHERE LOWER(TRIM(EMPLOYEE_ID)) = LOWER(TRIM(?))";
            DB::update($sql, [$acceptedHex, $id]);
        } else {
            // It was a missing record from university_leaving_request, so fetch details and insert into BNU_EMPLOYEE_EXIT_REQUEST_LIST
            $empDetails = DB::select("
                SELECT 
                    ulr.ulr_enrollment_no,
                    COALESCE(
                        MAX(ulr.fullname),
                        (SELECT MAX(s.full_name) FROM sisusers s WHERE LOWER(TRIM(s.username)) = LOWER(TRIM(ulr.ulr_enrollment_no))),
                        (SELECT MAX(e.emp_f_name || ' ' || e.emp_l_name) FROM emp_employees e WHERE LOWER(TRIM(e.emp_ad_username)) = LOWER(TRIM(ulr.ulr_enrollment_no))),
                        ulr.ulr_enrollment_no
                    ) AS full_name,
                    COALESCE(
                        TO_CHAR(MAX(ulr.created_at), 'HH24:MI:SS DD-MM-YY'),
                        MAX(TO_CHAR(ulr.created_at))
                    ) AS entry_date
                FROM university_leaving_request ulr
                WHERE LOWER(TRIM(ulr.ulr_enrollment_no)) = LOWER(TRIM(?))
                GROUP BY ulr.ulr_enrollment_no
            ", [$id]);

            $empName = !empty($empDetails) ? ($empDetails[0]->full_name ?? $id) : $id;
            $entryDate = !empty($empDetails) && !empty($empDetails[0]->entry_date) 
                ? $empDetails[0]->entry_date 
                : Carbon::now('Asia/Karachi')->format(' H:i:s d-m-y');

            $sql = "INSERT INTO BNU_EMPLOYEE_EXIT_REQUEST_LIST (EMPLOYEE_ID, EMPLOYEE_NAME, ENTRY_DATE, RECIVED_BY_HR) VALUES (?, ?, ?, HEXTORAW(?))";
            DB::insert($sql, [$id, $empName, $entryDate, $acceptedHex]);
        }

        return redirect('/staff-exit-request-list')->with('success', 'Received status accepted.');
    }




    public function staffRequestList()
    {
        $sql = "SELECT
           *
       FROM
       BNU_EMPLOYEE_EXIT_REQUEST_LIST ";
        $res = DB::select($sql);
        return view(
            'hr.staff_request_exit_list'
        );
    }




    public function store(Request $request)
    {

        $questions = [
            'q0' =>  "Principal reason for leaving",
            'q1' => "Fair and equal treatment",
            'q2' => "Recognition",
            'q3' => "Cooperation and teamwork",
            'q4' => "Training/Development Opportunities",
            'q5' => "Policies and procedures",
            'q6' => "Communication",
            'q7' => "Tenure/ Promotion opportunities",
            'q8' => "Decision making autonomy",
            'q9' => "Pay, benefits and incentives",
            'q10' => "Job Satisfaction",
            'q11' => "Salary",
            'q12' => "HoD",
            'q13' => "Dean",
            'q14' => "Overall rating of BNU",
            'q15' => "What was your workload usually?",
            'q16' => "Did any specific event trigger your decision to leave?",
            'q17' => "When did you begin searching for another job?",
            'q18' => "What makes the new job more attractive than your current position?",
            'q19' => "Have you spoken with anyone, either your director or any of your colleagues about your career goals?",
            'q20' => "Did you receive timely, useful, and adequate feedback on your performance?",
            'q21' => "Did you have clear goals and know what was expected of you in your job?",
            'q22' => "Did you have the resources and support necessary to accomplish your job?",
            'q23' => "Did you understand and feel a part of the accomplishment of the BNU mission?",
            'q24' => "What improvements (to BNU, your department, or to your job) would you suggest?",
            'q25' => "What recommendation would you have for making the BNU as a whole a better place to work?",
            'q26' => "What did you like most about your job?",
            'q27' => "What was the least exciting part of your job?",
            'q28' => "What makes BNU a good place to work?",
            'q29' => "What makes BNU a poor place to work?",
            'q30' => "In your opinion, have there been adequate career progression opportunities available within BNU?",
            'q31' => "What types of career opportunities are important to you? (Select all that apply)",

        ];
        // dd($questions);

        $employeeDepartmrntId = auth()->user()->dept_id;
        $employeeEmail = auth()->user()->email;
        $employeeMobile = auth()->user()->mobile;
        $employeeUsername = auth()->user()->username;
        $fullName = auth()->user()->full_name;
        $employee_id  = auth()->user()->employee_id;
        // dd($employeeUsername);

        try {
            for ($i = 0; $i <= 30; $i++) {

                $question = $questions["q$i"];
                $answer = $request->input("q$i");
                // dd('itration',$i);
                $insertIntoUniversityLeaveRequest = DB::table('university_leaving_request')->insert([
                    'answer' => $answer,
                    'question' => $question,

                    'ULR_ENROLLMENT_NO' => $employeeUsername,
                ]);
            }


            $request_leaving = DB::table('BNU_EMPLOYEE_EXIT_REQUEST_LIST')->insert([
                'EMPLOYEE_ID' => $employeeUsername,
                'EMPLOYEE_NAME' => $fullName,
                'ENTRY_DATE'  => Carbon::now('Asia/Karachi')->format(' H:i:s d-m-y')

            ]);


            DB::commit();
            if ($insertIntoUniversityLeaveRequest) {
                DB::commit();
                return response()->json(['message' => 'Data inserted successfully'], 201);
            } else {
                DB::rollback();
                return response()->json([
                    'message' => 'Insert into EPORTAL_REQUEST failed',
                    'status' => false
                ]);
                return 'Insert into EPORTAL_REQUEST failed';
            }
        } catch (Exception $e) {
            DB::rollback();
            //      dd($e);
            return response()->json([
                'message' => $e->getMessage(),
                'status' => false
            ]);
        }
    }

    public function lowerStaffStore(Request $request)
    {
        $questions = [
            'q0' =>  "Principal reason for leaving",
            'q1' => "Fair and equal treatment",
            'q2' => "Recognition",
            'q3' => "Cooperation and teamwork",
            'q4' => "Training/Development Opportunities",
            'q5' => "Policies and procedures",
            'q6' => "Communication",
            'q7' => "Tenure/ Promotion opportunities",
            'q8' => "Decision making autonomy",
            'q9' => "Pay, benefits and incentives",
            'q10' => "Job Satisfaction",
            'q11' => "Salary",
            'q12' => "HoD",
            'q13' => "Dean",
            'q14' => "Overall rating of BNU",
            'q15' => "What was your workload usually?",
            'q16' => "Did any specific event trigger your decision to leave?",
            'q17' => "When did you begin searching for another job?",
            'q18' => "What makes the new job more attractive than your current position?",
            'q19' => "Have you spoken with anyone, either your director or any of your colleagues about your career goals?",
            'q20' => "Did you receive timely, useful, and adequate feedback on your performance?",
            'q21' => "Did you have clear goals and know what was expected of you in your job?",
            'q22' => "Did you have the resources and support necessary to accomplish your job?",
            'q23' => "Did you understand and feel a part of the accomplishment of the BNU mission?",
            'q24' => "What improvements (to BNU, your department, or to your job) would you suggest?",
            'q25' => "What recommendation would you have for making the BNU as a whole a better place to work?",
            'q26' => "What did you like most about your job?",
            'q27' => "What was the least exciting part of your job?",
            'q28' => "What makes BNU a good place to work?",
            'q29' => "What makes BNU a poor place to work?",
            'q30' => "In your opinion, have there been adequate career progression opportunities available within BNU?",
            'q31' => "What types of career opportunities are important to you? (Select all that apply)",

        ];

        $employeeDepartmrntId = $request->department;
        $employeeEmail = $request->email;
        $employeeMobile = $request->mobile_no;
        $employeeUsername = $request->cnic;
        $fullName = $request->full_name;
        $employee_id  = $request->employee_id;
        $employeeCnic = $request->cnic;
        $employeeDesignation = $request->designation;

        // dd($request->all());

        try {
            for ($i = 0; $i <= 30; $i++) {

                $question = $questions["q$i"];
                $answer = $request->input("q$i");
                // dd('itration',$i);
                $insertIntoUniversityLeaveRequest = DB::table('university_leaving_request')->insert([
                    'answer' => $answer,
                    'question' => $question,
                    'ulr_enrollment_no' => $employeeUsername,
                    'cnic' => $employeeCnic,
                    'fullname' => $fullName,
                    'department' => $employeeDepartmrntId,
                    'designation' => $employeeDesignation,
                    'er_id' => $employee_id,
                ]);
            }


            $request_leaving = DB::table('BNU_EMPLOYEE_EXIT_REQUEST_LIST')->insert([
                'EMPLOYEE_ID' => $employeeUsername,
                'EMPLOYEE_NAME' => $fullName,
                'ENTRY_DATE'  => Carbon::now('Asia/Karachi')->format(' H:i:s d-m-y')

            ]);


            DB::commit();
            if ($insertIntoUniversityLeaveRequest) {
                DB::commit();
                return response()->json(['message' => 'Data inserted successfully'], 201);
            } else {
                DB::rollback();
                return response()->json([
                    'message' => 'Insert into EPORTAL_REQUEST failed',
                    'status' => false
                ]);
                return 'Insert into EPORTAL_REQUEST failed';
            }
        } catch (Exception $e) {
            DB::rollback();
            //      dd($e);
            return response()->json([
                'message' => $e->getMessage(),
                'status' => false
            ]);
        }
    }
}
