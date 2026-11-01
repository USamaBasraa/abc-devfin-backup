<?php
/**
 * Created by PhpStorm.
 * User: Maher Asad
 * Date: 5/14/2019
 * Time: 9:52 AM
 */
namespace App\Http\Controllers\StudentFinancial;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\Console\Helper\Table;
use DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
use App\Models\Grader\Schools;
use App\Models\Grader\Departments;
use App\Models\Grader\FacultyCourses;
use App\Models\Grader\EmpEmployees;
use Response;
use Validator;
use DataTables;
use DateTime;

class ChallanReportController extends Controller{

        /**
         * Users who are limited to ONLY their own school. Everyone else may query
         * across all schools. Kept as a single source of truth so the page
         * dropdown and the data APIs enforce the exact same access rule.
         */
        private const SCHOOL_RESTRICTED_USERS = [
            'ijaz.hussain', 'bushra.rahman', 'shafaat.ahmed',
            'tahir.kamran', 'omer.hassan', 'rashid.rana', 'ruhi.khalid', 'amra.raza', 'shabana', 'zainabsanik',
        ];

        /**
         * If the logged-in user is school-restricted, force the school filter to
         * their own school regardless of what the client sent. This prevents a
         * restricted user from pulling another school's data via a crafted
         * request, while leaving unrestricted users (all-schools) untouched.
         */
        private function applySchoolAccessScope(Request $request): void
        {
            $user = optional(Auth::user())->username;
            if ($user !== null && in_array($user, self::SCHOOL_RESTRICTED_USERS, true)) {
                $schoolId = auth()->user()->school_id;
                if (!empty($schoolId)) {
                    $request->merge(['school_sel' => (string) $schoolId]);
                }
            }
        }

        function challanReport(){
            $sessions = \App::call('App\Http\Controllers\Grader\AcademicSessionController@retrieve');
            // dd($sessions);
            $user = Auth::user()->username;
            $school_id = auth()->user()->school_id;
            if (!in_array($user, self::SCHOOL_RESTRICTED_USERS, true)) {
                $schools = Schools::where('school_status', 'E')->get();
            } else {
                $schools = Schools::where('school_id', $school_id)->get();
            }

            $departments = array(); // $this->school->fetch();
            $programs =  DB::select("select * from Programs where PROG_STATUS = 'E'");
            $query = 'select * from fin_fee_category';
            return view('StudentFinancial.challan_report',compact('sessions','schools','departments','programs'));

        }
        protected function getChallanReport(Request $request){
            // dd($request->all());
            $this->applySchoolAccessScope($request);

            $data = DB::table('student_challan as sc')
            ->select(
                'sc.*',
                'status_summary.status',
                'acd1.SESS_NAME as SESS_NAME', // Session name for FFS_SESS_ID
                'school.SCHOOL_NAME',
                'dept.DEPT_NAME',
                'prog.PROG_NAME',
                )
                ->leftjoin('ACADEMIC_SESSIONS as acd1', 'acd1.SESS_ID', '=', 'sc.SC_SESSION_ID')
                ->leftjoin('SCHOOLS as school', 'school.SCHOOL_ID', 'sc.SC_SCHOOL_ID')
                ->leftjoin('DEPARTMENTS as dept', 'dept.DEPT_ID', 'sc.SC_DEPARTMENT_ID')
                ->leftjoin('PROGRAMS as prog', 'prog.PROG_ID', 'sc.SC_PROGRAM_ID')
                ->joinSub(
                    DB::table('student_challan')
                        ->select('sc_app_id')
                        ->selectRaw("
                            CASE
                                WHEN COUNT(CASE WHEN sc_challan_status = 'PAID' THEN 1 END) = COUNT(*) THEN 'fullypaid'
                                WHEN COUNT(CASE WHEN sc_challan_status = 'UNPAID' THEN 1 END) = COUNT(*) THEN 'fullyunpaid'
                                WHEN COUNT(CASE WHEN sc_challan_status = 'PAID' THEN 1 END) > 0 THEN 'partialpaid'
                                ELSE 'Mixed'
                            END AS status
                        ")
                        ->where('SC_SESSION_ID', $request->session_sel) // Check for SC_SESSION_ID
                        ->whereIn('SC_CHALLAN_TYPE', ['Processing_Fee','Full_semester','Fee', 'Credit_hours', 'Short/Summer_Course']) // Check for SC_CHALLAN_TYPE values
                        ->groupBy('sc_app_id'),
                    'status_summary', // alias for the subquery
                    'sc.sc_app_id', '=', 'status_summary.sc_app_id'
                );

                if(!empty($request->session_sel)){
                    $data =  $data->where('sc.SC_SESSION_ID', $request->session_sel);
                }
                if(!empty($request->school_sel) && !is_array(json_decode($request->school_sel))){//condition will applied only when single school is selected if all school is slected then no need to apply condition//array means all school is selected and so we apply not array condtion to apply where clause;
                    $data =  $data->where('sc.SC_SCHOOL_ID', $request->school_sel);
                }
                if(!empty($request->studentTypeOption)){
                    $studentType = $request->studentTypeOption;
                    if (strpos($studentType, '_withouReg') !== false) {//without reg means we will add additional check of enrollmentNumber = null other check remain same
                        $studentType = explode('_', $studentType);
                        $data =  $data->where('sc.SC_STUDENT_STATUS', $studentType[0])
                                        ->whereNull('sc.SC_ENROLLMENT_NO');
                    }else{
                        $data =  $data->where('sc.SC_STUDENT_STATUS', $studentType)
                                        ->whereNotNull('sc.SC_ENROLLMENT_NO');
                    }

                }
                if(!empty($request->feeType)){
                    if($request->feeType == 'Full_semester'){
                        $data =  $data->whereIn('sc.SC_CHALLAN_TYPE', ['Full_semester','Fee', 'Short/Summer_Course']);
                    }else{
                        $data =  $data->where('sc.SC_CHALLAN_TYPE', $request->feeType);//credit_hours// processing fee
                    }
                }else{
                    $data =  $data->whereIn('sc.SC_CHALLAN_TYPE', ['Processing_Fee', 'Full_semester', 'Fee', 'Credit_hours', 'Short/Summer_Course']);
                }
                if(!empty($request->department) && !is_array(json_decode($request->department))){//condition will applied only when single department is selected if all department is slected then no need to apply condition//array means all department is selected and so we apply not array condtion to apply where clause;
                    $data =  $data->where('sc.SC_DEPARTMENT_ID', $request->department);
                }
                if(!empty($request->program)&& !is_array(json_decode($request->program))){//condition will applied only when single program is selected if all program is slected then no need to apply condition//array means all program is selected and so we apply not array condtion to apply where clause;
                //    dd('program condtion applied');
                    $programs_id = $request->program;
                    // dd($programs_id);
                    $array_of_sch_dep_prog = explode('~~', $programs_id);
                    // dd($array_of_sch_dep_prog[2]);
                    $data =  $data->where('sc.SC_PROGRAM_ID', $array_of_sch_dep_prog[2]);
                }
                if(!empty($request->challanStatus)){
                    if($request->challanStatus == 'PAID'){
                        $data =  $data->where('status_summary.status','=', 'fullypaid');
                    }else if($request->challanStatus == 'partialpaid'){
                        $data =  $data->where('status_summary.status','=', 'partialpaid')->where('sc.SC_CHALLAN_STATUS', 'PAID');//from partial paid show only paid
                    }else if($request->challanStatus == 'UNPAID'){
                        $data =  $data->where('status_summary.status','=', 'fullyunpaid');
                    }else{
                        $data =  $data->where('sc.SC_CHALLAN_STATUS', $request->challanStatus);
                    }
                }
                $data =  $data->orderBy('sc.sc_app_id')
                                ->get();
                // dd($data);
            return DataTables::of($data)->toJson();
        }
        protected function getChallanSummary(Request $request){
            // dd($request->all());
            $this->applySchoolAccessScope($request);
            // All UI fee types (incl. Processing_Fee) use the same accounts-aligned
            // summary query. Previously Processing_Fee fell through to a legacy SQL
            // path that returned empty / wrong columns for School Wise — so the Fee
            // Type filter appeared broken when Processing Fee was selected.
            if (in_array($request->feeType, ['Full_semester', 'Credit_hours', 'Hostel_Fee', 'Processing_Fee'], true) || empty($request->feeType)) {
                return $this->getFullSemChallanSummary($request);
            }

            //  dd($request->all());

                $SC_session_condition='';
                $session_condition='';

                $SC_stdnt_status_condition='';
                $stdnt_status_condition='';

                $school_condition='';
                $dept_condition='';
                $prog_condition='';

                $SC_school_condition='';
                $SC_dept_condition='';
                $SC_prog_condition='';

                $SC_dateRange = '';
                $dateRange = '';

                $session_code = 'F2025';//default

                // Escape single quotes so request input cannot break out of the SQL
                // string literals (SQL-injection defence); legitimate values are
                // unaffected so existing results stay identical.
                $esc = static function ($value) {
                    return str_replace("'", "''", (string) $value);
                };

                $SC_challanType_condtion= "AND SC.SC_CHALLAN_TYPE IN ('Full_semester', 'Credit_hours', 'Fee', 'Short/Summer_Course')";
                $challanType_condtion= "AND SC_CHALLAN_TYPE IN ('Full_semester', 'Credit_hours', 'Fee', 'Short/Summer_Course')";

                if(!empty($request->session_sel)){
                    $safeSession = $esc($request->session_sel);
                    $SC_session_condition =  "AND SC.SC_SESSION_ID = '{$safeSession}'";
                    $session_condition =  "AND SC_SESSION_ID = '{$safeSession}'";
                    $sess_code = DB::table('academic_sessions')->select('sess_code')->where('sess_id', $request->session_sel)->first();
                    $session_code = !empty($sess_code->sess_code) ? $sess_code->sess_code : $session_code;

                }
                if(!empty($request->school_sel) && !is_array(json_decode($request->school_sel))){//condition will applied only when single school is selected if all school is slected then no need to apply condition//array means all school is selected and so we apply not array condtion to apply where clause;
                    $safeSchool = $esc($request->school_sel);
                    $SC_school_condition =  "AND SC.sc_school_id = '{$safeSchool}'";
                    $school_condition =  "AND sc_school_id = '{$safeSchool}'";
                }
                if(!empty($request->department) && !is_array(json_decode($request->department))){//condition will applied only when single department is selected if all department is slected then no need to apply condition//array means all department is selected and so we apply not array condtion to apply where clause;
                    $safeDept = $esc($request->department);
                    $SC_dept_condition =  "AND SC.sc_department_id = '{$safeDept}'";
                    $dept_condition =  "AND sc_department_id = '{$safeDept}'";
                }
                if(!empty($request->program)&& !is_array(json_decode($request->program))){//condition will applied only when single program is selected if all program is slected then no need to apply condition//array means all program is selected and so we apply not array condtion to apply where clause;
                    //dd('program condtion applied');
                    $programs_id = $request->program;
                    // dd($programs_id);
                    $array_of_sch_dep_prog = explode('~~', $programs_id);
                    //dd($array_of_sch_dep_prog[2]);
                    if(isset($array_of_sch_dep_prog[2])){
                        $safeProg = $esc($array_of_sch_dep_prog[2]);
                        $SC_prog_condition =  "AND SC.sc_program_id = '{$safeProg}'";
                        $prog_condition =  "AND sc_program_id = '{$safeProg}'";
                    }
                }
                $isValidDate = static function ($d) {
                    return is_string($d) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $d) === 1;
                };
                if($isValidDate($request->fromDate) && $isValidDate($request->toDate)){
                    $SC_dateRange =  "AND SC.SC_ISSU_DATE BETWEEN TO_DATE('{$request->fromDate}', 'YYYY-MM-DD') AND TO_DATE('{$request->toDate}', 'YYYY-MM-DD')";
                    $dateRange =  "AND SC_ISSU_DATE BETWEEN TO_DATE('{$request->fromDate}', 'YYYY-MM-DD') AND TO_DATE('{$request->toDate}', 'YYYY-MM-DD')";
                }else if($isValidDate($request->fromDate)){
                    $SC_dateRange = "AND SC.SC_ISSU_DATE >= TO_DATE('{$request->fromDate}', 'YYYY-MM-DD')";
                    $dateRange = "AND SC_ISSU_DATE >= TO_DATE('{$request->fromDate}', 'YYYY-MM-DD')";
                }else if($isValidDate($request->toDate)){
                    $SC_dateRange = "AND SC.SC_ISSU_DATE <= TO_DATE('{$request->toDate}', 'YYYY-MM-DD')";
                    $dateRange = "AND SC_ISSU_DATE <= TO_DATE('{$request->toDate}', 'YYYY-MM-DD')";
                }
                // dd($SC_dateRange,$dateRange);

                if(!empty($request->studentTypeOption)){
                    $studentType = $request->studentTypeOption;
                    if (strpos($studentType, '_withouReg') !== false) {//without reg means we will add additional check of enrollmentNumber = null other check remain same
                        $studentType = explode('_', $studentType);
                        $safeBaseType = $esc($studentType[0]);
                        $SC_stdnt_status_condition=   "AND SC.SC_STUDENT_STATUS = '{$safeBaseType}' AND aai.CMS_SAI_ENROLLMENT_NO IS NULL";//this for external condtion with SC  aliase
                        $stdnt_status_condition=   "AND SC_STUDENT_STATUS = '{$safeBaseType}' AND CMS_SAI_ENROLLMENT_NO IS NULL";//this for internal subquery condtion with no aliase
                    }else{
                        $safeType = $esc($studentType);
                        $SC_stdnt_status_condition=   "AND SC.SC_STUDENT_STATUS = '{$safeType}' AND aai.CMS_SAI_ENROLLMENT_NO IS NOT NULL";//this for external condtion with SC  aliase
                        $stdnt_status_condition=   "AND SC_STUDENT_STATUS = '{$safeType}' AND CMS_SAI_ENROLLMENT_NO IS NOT NULL";//this for internal subquery condtion with no aliase
                    }
                    if($studentType != 'Existing' && !empty($request->sch_round)){
                        $safeRound = $esc($request->sch_round);
                        $SC_stdnt_status_condition .= " AND LOWER(SC.SC_ADMISSION_ROUND) = LOWER('{$safeRound}')";
                        $stdnt_status_condition .= " AND LOWER(SC_ADMISSION_ROUND) = LOWER('{$safeRound}')";
                    }

                }
                // dd($SC_stdnt_status_condition,$stdnt_status_condition );
                if(!empty($request->feeType)){
                    if($request->feeType == 'Full_semester'){
                        $SC_challanType_condtion =  "AND SC.SC_CHALLAN_TYPE IN ('Full_semester', 'Fee', 'Short/Summer_Course')";
                        $challanType_condtion =  "AND SC_CHALLAN_TYPE IN ('Full_semester', 'Fee', 'Short/Summer_Course')";
                    }else{
                        $safeFeeType = $esc($request->feeType);
                        $SC_challanType_condtion =  "AND SC.SC_CHALLAN_TYPE = '{$safeFeeType}'";
                        $challanType_condtion =  "AND SC_CHALLAN_TYPE = '{$safeFeeType}'";
                    }

                }
                // dd($SC_session_condition, $session_condition, $SC_stdnt_status_condition, $stdnt_status_condition, $SC_challanType_condtion, $challanType_condtion);

            $data="SELECT
                    ACD1.SESS_NAME,
                    SCHOOL.SCHOOL_NAME,
                    SCHOOL.SCHOOL_CODE,
                    SC.SC_SCHOOL_ID,
                    DEPT.DEPT_NAME,
                    PROG.PROG_NAME,
                    COUNT(DISTINCT SC.SC_APP_ID) AS TOTAL_STUDENTS, -- Count of unique students
                    COUNT(DISTINCT CASE WHEN STATUS.CANCELLED_COUNT = 0 THEN SC.SC_APP_ID END) AS ACTIVE, -- Students with no cancelled challans
                    COUNT(DISTINCT CASE WHEN STATUS.CANCELLED_COUNT > 0 THEN SC.SC_APP_ID END) AS CANCELLED, -- Students with at least one cancelled challan
                    COUNT(DISTINCT CASE WHEN STATUS.UNPAID_COUNT = 0 AND STATUS.TOTAL_COUNT > 0 THEN SC.SC_APP_ID END) AS FULLPAID, -- Students with all challans paid
                    COUNT(DISTINCT CASE WHEN STATUS.UNPAID_COUNT > 0 AND STATUS.UNPAID_COUNT < STATUS.TOTAL_COUNT THEN SC.SC_APP_ID END) AS INSTALLMENT, -- Students with partially paid challans
                    --COUNT(DISTINCT CASE WHEN STATUS.UNPAID_COUNT > 0 THEN SC.SC_APP_ID END) AS UNPAID, -- Students with all challans unpaid
                    COUNT(DISTINCT CASE WHEN STATUS.UNPAID_COUNT = STATUS.TOTAL_COUNT THEN SC.SC_APP_ID END) AS UNPAID, -- Students with all challans unpaid
                    COUNT(DISTINCT CASE WHEN STATUS.UNPAID_COUNT = 0 AND STATUS.TOTAL_COUNT > 0 THEN SC.SC_APP_ID END) +
                    COUNT(DISTINCT CASE WHEN STATUS.UNPAID_COUNT > 0 AND STATUS.UNPAID_COUNT < STATUS.TOTAL_COUNT THEN SC.SC_APP_ID END) AS TOTALPAID -- Total students with any amount paid
                FROM
                    STUDENT_CHALLAN SC
                LEFT JOIN (
                                SELECT
                                    SC_APP_ID,
                                    COUNT(*) AS TOTAL_COUNT, -- Total challans per student
                                    COUNT(CASE WHEN SC_CHALLAN_STATUS = 'PAID' THEN 1 END) AS PAID_COUNT, -- Count of paid challans
                                    COUNT(CASE WHEN SC_CHALLAN_STATUS = 'UNPAID' THEN 1 END) AS UNPAID_COUNT, -- Count of unpaid challans
                                    COUNT(CASE WHEN SC_CHALLAN_STATUS = 'CANCELLED' THEN 1 END) AS CANCELLED_COUNT -- Count of cancelled challans
                                FROM STUDENT_CHALLAN
                                LEFT JOIN admission_apply_info ON aai_id = sc_app_id
                                WHERE 1=1
                                    $session_condition
                                    $stdnt_status_condition
                                    $challanType_condtion
                                    $school_condition
                                    $dept_condition
                                    $prog_condition
                                    $dateRange
                                    and (sc_enrollment_no like '%$session_code%' or sc_enrollment_no is null)
                                    AND aai_status = 'Selected'
                                GROUP BY SC_APP_ID
                        )STATUS ON SC.SC_APP_ID = STATUS.SC_APP_ID
                LEFT JOIN admission_apply_info  aai ON aai.aai_id = sc.sc_app_id
                LEFT JOIN ACADEMIC_SESSIONS ACD1
                    ON ACD1.SESS_ID = SC.SC_SESSION_ID
                LEFT JOIN SCHOOLS SCHOOL
                    ON SCHOOL.SCHOOL_ID = SC.SC_SCHOOL_ID
                LEFT JOIN DEPARTMENTS DEPT
                    ON DEPT.DEPT_ID = SC.SC_DEPARTMENT_ID
                LEFT JOIN PROGRAMS PROG
                    ON PROG.PROG_ID = SC.SC_PROGRAM_ID
                WHERE 1=1
                    $SC_session_condition
                    $SC_stdnt_status_condition
                    $SC_challanType_condtion
                    $SC_school_condition
                    $SC_dept_condition
                    $SC_prog_condition
                    $SC_dateRange
                    and (sc.sc_enrollment_no like '%$session_code%' or sc.sc_enrollment_no is null)
                    AND aai.aai_status = 'Selected'
                GROUP BY
                    ACD1.SESS_NAME,
                    SC.SC_SCHOOL_ID,
                    SCHOOL.SCHOOL_NAME,
                    SCHOOL.SCHOOL_CODE,
                    DEPT.DEPT_NAME,
                   PROG.PROG_NAME
                ORDER BY
                    ACD1.SESS_NAME ASC,
                    SCHOOL.SCHOOL_NAME ASC,
                    DEPT.DEPT_NAME ASC,
                   PROG.PROG_NAME ASC
                ";
                // echo $data;
                // exit;
            // dd($data);
            $data = DB::select($data);
            // dd($data);
            return DataTables::of($data)->toJson();
        }
        protected function getProgramFeeStructureForm(Request $request){
            $dept_id = json_decode($request->get('dept_id'));
            // dd($request->get('degreeLevel'));
            $programs=[];
            if(!empty($dept_id)){
                if(is_array($dept_id)){//in case of all departments selected
                    foreach($dept_id as $val){
                        $dept_idsss[] = explode('~~', $val)[1];
                    }
                    $programs = DB::table('programs')
                                ->where('PROG_STATUS','=', 'E')
                                ->whereIn('prog_dept_id', $dept_idsss);
                                if(!empty($request->get('degreeLevel'))){
                                    $programs = $programs->where('PROG_DEGREE_LEVEL','=', $request->get('degreeLevel'));
                                }
                    $programs =  $programs->orderBy('prog_name', 'asc')
                                ->get();
                }else{
                    $programs = DB::table('programs')
                        ->where('prog_dept_id', $dept_id)
                        ->where('PROG_STATUS', '=', 'E');
                        if(!empty($request->get('degreeLevel'))){
                            $programs = $programs->where('PROG_DEGREE_LEVEL','=', $request->get('degreeLevel'));
                        }
                    $programs =  $programs->orderBy('prog_name', 'asc')
                                ->get();
                }
                // dd($programs);

            }
            return response()->json(['data' => $programs, 'status'=> true, 'message' => count($programs).' record(s) found!']);
        }
        protected function getDepartmentsFeeStructureForm(Request $request){
            // dd($request->get('school_id'));
            $school_id = json_decode($request->get('school_id'));
            $departments=[];
            if(!empty($school_id)){
                if(is_array($school_id)){
                    $departments = DB::table('departments')
                        ->where('dept_status', '=', 'E')
                        ->whereIn('dept_school_id', $school_id)
                        ->orderBy('DEPT_NAME', 'asc')
                        ->get();
                }else{
                    $departments = DB::table('departments')
                        ->where('dept_status', '=', 'E')
                        ->where('dept_school_id', $school_id)
                        ->orderBy('DEPT_NAME', 'asc')
                        ->get();
                }
                // dd($departments);

            }
            return response()->json(['data' => $departments, 'status'=> true, 'message' => count($departments).' record(s) found!']);
        }
        protected function getFullSemChallanSummary(Request $request){
            // The challan-report page's summary tables (LoadSummary and LoadSummary2)
            // both render per-program rows (dept_name/prog_name), so this endpoint
            // always runs at the program view level regardless of what the client
            // sends. (The school-wise challan-summary-report page uses a different
            // endpoint, getChallanSummaryReport.)
            if (empty($request->summaryViewLevel)) {
                $request->merge(['summaryViewLevel' => 'program']);
            }

            $studentType = !empty($request->studentTypeOption) ? $request->studentTypeOption : null;
            $data = $this->runFullSemSummaryQuery($request, $studentType);

            return DataTables::of($data)->toJson();
        }
        protected function resolveSummaryViewLevel(Request $request): string
        {
            $level = strtolower(trim($request->summaryViewLevel ?? 'school'));

            return $level === 'program' ? 'program' : 'school';
        }

        protected function normalizeSummaryRows($rows)
        {
            return array_map(function ($row) {
                $normalized = [];
                foreach ((array) $row as $key => $value) {
                    $normalized[strtolower($key)] = $value;
                }

                return $normalized;
            }, $rows);
        }

        protected function runFullSemSummaryQuery(Request $request, ?string $studentType = null)
        {
            // Accounts-aligned summary for All Schools / single school:
            // 1) Classify Paid / Unpaid at student × school (ALL challans: main + installments)
            // 2) Attribute each student to ONE program via main challan (installments = 0)
            //    so program rows sum cleanly to the school total (no cross-school bleed,
            //    no double-count across programs).
            $session_condition = '';
            $school_condition = '';
            $dept_condition = '';
            $prog_condition = '';
            $studentTypeOption_condition = '';
            $admissionRound_condition = '';
            $date_condition = '';

            // Escape any single quotes so nothing from the request can break out of
            // the string literal (defence against SQL injection). Values are Oracle
            // string literals; escaping '=>'' keeps legitimate results identical.
            $esc = static function ($value) {
                return str_replace("'", "''", (string) $value);
            };

            if (!empty($request->session_sel)) {
                $session_condition = "AND sc.SC_SESSION_ID = '{$esc($request->session_sel)}'";
            }

            if (!empty($request->school_sel) && !is_array(json_decode($request->school_sel))) {
                $school_condition = "AND sc.SC_SCHOOL_ID = '{$esc($request->school_sel)}'";
            }

            if (!empty($request->department) && !is_array(json_decode($request->department))) {
                $dept_condition = "AND sc.SC_DEPARTMENT_ID = '{$esc($request->department)}'";
            }

            if (!empty($request->program) && !is_array(json_decode($request->program))) {
                $programs_id = $request->program;
                $array_of_sch_dep_prog = explode('~~', $programs_id);
                if (isset($array_of_sch_dep_prog[2])) {
                    $prog_condition = "AND sc.SC_PROGRAM_ID = '{$esc($array_of_sch_dep_prog[2])}'";
                }
            }

            if (!empty($request->sch_round)) {
                $safeRound = $esc($request->sch_round);
                $admissionRound_condition = "AND LOWER(TRIM(sc.SC_ADMISSION_ROUND)) = LOWER('{$safeRound}')";
            }

            // Challan issue-date range (BUG-02): the UI enables these fields for the
            // summary, so honour them here too. Only accept a strict YYYY-MM-DD to
            // avoid malformed / injected input; empty or invalid dates are ignored.
            $isValidDate = static function ($d) {
                return is_string($d) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $d) === 1;
            };
            $fromDate = $request->fromDate ?? null;
            $toDate = $request->toDate ?? null;
            if ($isValidDate($fromDate) && $isValidDate($toDate)) {
                $date_condition = "AND sc.SC_ISSU_DATE BETWEEN TO_DATE('{$fromDate}', 'YYYY-MM-DD') AND TO_DATE('{$toDate}', 'YYYY-MM-DD')";
            } elseif ($isValidDate($fromDate)) {
                $date_condition = "AND sc.SC_ISSU_DATE >= TO_DATE('{$fromDate}', 'YYYY-MM-DD')";
            } elseif ($isValidDate($toDate)) {
                $date_condition = "AND sc.SC_ISSU_DATE <= TO_DATE('{$toDate}', 'YYYY-MM-DD')";
            }

            if ($studentType !== null) {
                if (strpos($studentType, '_withouReg') !== false) {
                    $baseStudentType = explode('_', $studentType)[0];
                    $safeStudentType = $esc($baseStudentType);
                    $studentTypeOption_condition = "AND UPPER(TRIM(sc.SC_STUDENT_STATUS)) = UPPER('{$safeStudentType}')
                            AND EXISTS (
                                SELECT 1
                                FROM admission_apply_info aai
                                WHERE aai.aai_id = sc.SC_APP_ID
                                  AND aai.cms_sai_enrollment_no IS NULL
                            )";
                } else {
                    $safeStudentType = $esc($studentType);
                    $studentTypeOption_condition = "AND UPPER(TRIM(sc.SC_STUDENT_STATUS)) = UPPER('{$safeStudentType}')";
                }
            }

            $feeType = $request->feeType ?? null;
            if (!empty($feeType)) {
                $safeFeeType = $esc($feeType);
                $feeType_condition = "AND sc.SC_CHALLAN_TYPE = '{$safeFeeType}'";
            } else {
                $feeType_condition = "AND sc.SC_CHALLAN_TYPE IN ('Full_semester', 'Credit_hours', 'Hostel_Fee')";
            }

            $metricsSql = "
                COUNT(DISTINCT ps.SC_APP_ID) AS TOTAL_STUDENTS,
                COUNT(DISTINCT CASE WHEN NVL(t.paid_any, 0) = 0 AND NVL(t.unpaid_any, 0) = 0 THEN ps.SC_APP_ID END) AS CANCELLED,
                COUNT(DISTINCT CASE WHEN NVL(t.paid_any, 0) > 0 OR  NVL(t.unpaid_any, 0) > 0 THEN ps.SC_APP_ID END) AS ACTIVE,
                COUNT(DISTINCT CASE WHEN NVL(t.paid_any, 0) > 0 AND NVL(t.unpaid_any, 0) = 0 THEN ps.SC_APP_ID END) AS FULLPAID,
                COUNT(DISTINCT CASE WHEN NVL(t.paid_any, 0) > 0 AND NVL(t.unpaid_any, 0) > 0 THEN ps.SC_APP_ID END) AS INSTALLMENT,
                COUNT(DISTINCT CASE WHEN NVL(t.paid_any, 0) = 0 AND NVL(t.unpaid_any, 0) > 0 THEN ps.SC_APP_ID END) AS UNPAID,
                COUNT(DISTINCT CASE WHEN NVL(t.paid_any, 0) > 0 THEN ps.SC_APP_ID END) AS TOTALPAID,
                MAX(st.SCHOOL_TOTAL_STUDENTS) AS SCHOOL_TOTAL_STUDENTS
            ";

            $viewLevel = $this->resolveSummaryViewLevel($request);

            if ($viewLevel === 'school') {
                $populationCols = "sc.SC_APP_ID,
                        sc.SC_SESSION_ID,
                        sc.SC_SCHOOL_ID";
                $detailSelect = '';
                $detailJoins = '';
                $groupBySql = "ACD1.SESS_NAME,
                    SCHOOL.SCHOOL_NAME,
                    SCHOOL.SCHOOL_CODE,
                    ps.SC_SCHOOL_ID";
                $orderBySql = "ACD1.SESS_NAME ASC,
                    SCHOOL.SCHOOL_NAME ASC";
            } else {
                $populationCols = "sc.SC_APP_ID,
                        sc.SC_SESSION_ID,
                        sc.SC_SCHOOL_ID,
                        sc.SC_DEPARTMENT_ID,
                        sc.SC_PROGRAM_ID";
                $detailSelect = "DEPT.DEPT_NAME,
                    PROG.PROG_NAME,";
                $detailJoins = "LEFT JOIN DEPARTMENTS DEPT ON DEPT.DEPT_ID = ps.SC_DEPARTMENT_ID
                LEFT JOIN PROGRAMS PROG ON PROG.PROG_ID = ps.SC_PROGRAM_ID";
                $groupBySql = "ACD1.SESS_NAME,
                    SCHOOL.SCHOOL_NAME,
                    SCHOOL.SCHOOL_CODE,
                    ps.SC_SCHOOL_ID,
                    DEPT.DEPT_NAME,
                    PROG.PROG_NAME";
                $orderBySql = "ACD1.SESS_NAME ASC,
                    SCHOOL.SCHOOL_NAME ASC,
                    DEPT.DEPT_NAME ASC,
                    PROG.PROG_NAME ASC";
            }

            // Shared filter fragment for every STUDENT_CHALLAN subquery.
            $whereFilters = "
                        {$feeType_condition}
                        {$session_condition}
                        {$school_condition}
                        {$dept_condition}
                        {$prog_condition}
                        {$studentTypeOption_condition}
                        {$admissionRound_condition}
                        {$date_condition}";

            $sql = "SELECT
                    ACD1.SESS_NAME,
                    SCHOOL.SCHOOL_NAME,
                    SCHOOL.SCHOOL_CODE,
                    ps.SC_SCHOOL_ID,
                    {$detailSelect}
                    {$metricsSql}
                FROM (
                    -- One row per student × school. Prefer main challan (installments=0)
                    -- for program attribution; if none, fall back to latest installment
                    -- so Paid students who only have installment rows are not dropped.
                    SELECT
                        {$populationCols}
                    FROM (
                        SELECT
                            {$populationCols},
                            ROW_NUMBER() OVER (
                                PARTITION BY sc.SC_APP_ID, sc.SC_SCHOOL_ID
                                ORDER BY
                                    CASE WHEN NVL(sc.CHECK_FEE_INSTALLMENTS, 0) = 0 THEN 0 ELSE 1 END,
                                    sc.CHALLAN_NO DESC NULLS LAST
                            ) AS rn
                        FROM STUDENT_CHALLAN sc
                        WHERE 1=1
                            {$whereFilters}
                    ) sc
                    WHERE sc.rn = 1
                ) ps
                LEFT JOIN (
                    -- Payment flags at student × school (main + ALL installments)
                    SELECT
                        sc.SC_APP_ID,
                        sc.SC_SCHOOL_ID,
                        MAX(CASE WHEN UPPER(TRIM(sc.SC_CHALLAN_STATUS)) = 'PAID'   THEN 1 ELSE 0 END) AS paid_any,
                        MAX(CASE WHEN UPPER(TRIM(sc.SC_CHALLAN_STATUS)) = 'UNPAID' THEN 1 ELSE 0 END) AS unpaid_any
                    FROM STUDENT_CHALLAN sc
                    WHERE 1=1
                        {$whereFilters}
                    GROUP BY sc.SC_APP_ID, sc.SC_SCHOOL_ID
                ) t ON t.SC_APP_ID = ps.SC_APP_ID
                   AND t.SC_SCHOOL_ID = ps.SC_SCHOOL_ID
                LEFT JOIN (
                    -- School-level distinct active students (Paid + Unpaid)
                    SELECT
                        x.SC_SCHOOL_ID,
                        COUNT(DISTINCT x.SC_APP_ID) AS SCHOOL_TOTAL_STUDENTS
                    FROM (
                        SELECT
                            sc.SC_APP_ID,
                            sc.SC_SCHOOL_ID,
                            MAX(CASE WHEN UPPER(TRIM(sc.SC_CHALLAN_STATUS)) = 'PAID'   THEN 1 ELSE 0 END) AS paid_any,
                            MAX(CASE WHEN UPPER(TRIM(sc.SC_CHALLAN_STATUS)) = 'UNPAID' THEN 1 ELSE 0 END) AS unpaid_any
                        FROM STUDENT_CHALLAN sc
                        WHERE 1=1
                            {$whereFilters}
                        GROUP BY sc.SC_APP_ID, sc.SC_SCHOOL_ID
                    ) x
                    WHERE x.paid_any > 0 OR x.unpaid_any > 0
                    GROUP BY x.SC_SCHOOL_ID
                ) st ON st.SC_SCHOOL_ID = ps.SC_SCHOOL_ID
                LEFT JOIN ACADEMIC_SESSIONS ACD1 ON ACD1.SESS_ID = ps.SC_SESSION_ID
                LEFT JOIN SCHOOLS SCHOOL ON SCHOOL.SCHOOL_ID = ps.SC_SCHOOL_ID
                {$detailJoins}
                GROUP BY
                    {$groupBySql}
                ORDER BY
                    {$orderBySql}
                ";
            // dd($sql);
            return $this->normalizeSummaryRows(DB::select($sql));
        }

}
