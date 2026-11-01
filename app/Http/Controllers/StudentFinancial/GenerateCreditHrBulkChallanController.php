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
use Carbon\Carbon;
use Illuminate\Support\Str;

class GenerateCreditHrBulkChallanController extends Controller{

        public function generateFeeChallan(){
            $sessions = \App::call('App\Http\Controllers\Grader\AcademicSessionController@retrieve');
            $schools =  Schools::all();
            $departments = array(); // $this->school->fetch();
            $facultyCourses = \App::call('App\Http\Controllers\Grader\FacultyCoursesController@retrieve');
            $offeredFaculty = \App::call('App\Http\Controllers\Grader\CoursesOfferedController@retrieveFaculty');
            $programs =  DB::select("select * from Programs where PROG_STATUS = 'E'");
            $query = 'select * from fin_fee_category';
            $feeCategories = DB::select($query);
            $exempt_query = "select * from course_exempted where CE_STATUS = 'E' and CE_SESSION_ID = (select max(sess_id) from academic_sessions)";
            $exempt_course = DB::select($exempt_query);
            $Scholarship_Type = "select * from Scholarship_Type where scholarship_status = 'E' and SCHOLARSHIP_ID in (1,2)";
            $recScholarship_Type = DB::select($Scholarship_Type);
             $query = 'select * from semesters order by SEMESTER_ID';
            $semesters = DB::select($query);
            return view('StudentFinancial.generateCreditrHrChallan',compact('sessions','schools','departments','facultyCourses','offeredFaculty','feeCategories','programs', 'recScholarship_Type','semesters', 'exempt_course'));

        }
        public function addChallan(Request $request){
            // dd($request->all());
            if($request->fee_type == 'Credit_hours'){
                return $this->add_creditHr_challans($request);
            }
            if($request->fee_type == 'Credit_hours_Summer_Course'){
                return $this->add_summerCredithr_challans($request);
            }

        }

        /**
         * Pre-flight: which selected students have FIN_FEE_STRUCTURE for
         * session + school + dept + program + student type + fee type.
         */
        public function checkFeeStructure(Request $request){
            $validator = Validator::make($request->all(),[
                'session' => 'required',
                'student_type' => 'required',
                'fee_type' => 'required',
                'students' => 'required',
            ]);
            if($validator->fails()) {
                return Response::json(array(
                    'success' => false,
                    'errors' => $validator->getMessageBag()->toArray()
                ), 400);
            }
            $check = $this->resolveCreditFeeStructureAvailability($request);
            unset($check['eligible_rows']);
            return response()->json(['success' => true] + $check);
        }

        private function resolveFfsStudentType($studentType){
            return str_replace('New without Registration', 'New Admission', (string) $studentType);
        }

        /**
         * Same FIN_FEE_STRUCTURE match used by bulk fee challan procedures:
         * school, dept, program, session, student type, fee type (+ intake / roadmap / semester).
         */
        private function resolveCreditFeeStructureAvailability(Request $request){
            $studentIdArr = array_values(array_unique(array_filter(array_map('trim', explode(',', (string) $request->students)), 'strlen')));
            $fscdIdArr = array_values(array_filter(array_map('trim', explode(',', (string) $request->fscd_ids)), 'strlen'));
            $saiToFscd = [];
            foreach ($studentIdArr as $i => $saiId) {
                if (isset($fscdIdArr[$i]) && $fscdIdArr[$i] !== '') {
                    $saiToFscd[(string) $saiId] = $fscdIdArr[$i];
                }
            }

            $ffsStudentType = $this->resolveFfsStudentType($request->student_type);
            $candidates = [];
            $seen = [];

            if (!empty($studentIdArr)) {
                $chunks = array_chunk($studentIdArr, 1000);
                $rows = collect();
                foreach ($chunks as $chunk) {
                    $part = DB::table('STUDENTS_APPLY_INFO as sai')
                        ->leftJoin('STUDENTS_PERSONAL_INFO as spi', 'spi.SPI_ID', 'sai.SAI_SPI_ID')
                        ->select(
                            'sai.SAI_ID',
                            'sai.SAI_ENROLLMENT_NO',
                            'sai.SAI_STUDENT_STATUS',
                            'sai.SAI_SDPO_SCHOOL_ID',
                            'sai.SAI_SDPO_DEPT_ID',
                            'sai.SAI_SDPO_PROG_ID',
                            'sai.SAI_SDPO_SESS_ID',
                            'spi.SPI_NAME'
                        )
                        ->whereIn('sai.SAI_ID', $chunk)
                        ->get();
                    $rows = $rows->merge($part);
                }

                foreach ($rows as $row) {
                    $seen[(string) $row->sai_id] = true;
                    $statusOk = empty($request->student_status)
                        || (string) $row->sai_student_status === (string) $request->student_status;
                    $candidates[] = [
                        'id'             => $row->sai_id,
                        'fscd_id'        => isset($saiToFscd[(string) $row->sai_id]) ? $saiToFscd[(string) $row->sai_id] : null,
                        'name'           => $row->spi_name,
                        'enrollment'     => $row->sai_enrollment_no,
                        'school_id'      => $row->sai_sdpo_school_id,
                        'dept_id'        => $row->sai_sdpo_dept_id,
                        'prog_id'        => $row->sai_sdpo_prog_id,
                        'intake_sess_id' => $row->sai_sdpo_sess_id,
                        'reason'         => $statusOk ? null : 'student_record_mismatch',
                        'row'            => $row,
                    ];
                }
            }

            foreach ($studentIdArr as $id) {
                if (!isset($seen[(string) $id])) {
                    $candidates[] = [
                        'id'             => $id,
                        'fscd_id'        => isset($saiToFscd[(string) $id]) ? $saiToFscd[(string) $id] : null,
                        'name'           => null,
                        'enrollment'     => null,
                        'school_id'      => null,
                        'dept_id'        => null,
                        'prog_id'        => null,
                        'intake_sess_id' => null,
                        'reason'         => 'student_record_mismatch',
                        'row'            => null,
                    ];
                }
            }

            $probeCache = [];
            $eligibleRows = [];
            $eligibleIds = [];
            $eligibleFscdIds = [];
            $missing = [];

            foreach ($candidates as $candidate) {
                if ($candidate['reason'] === null) {
                    $key = $candidate['school_id'].'|'.$candidate['dept_id'].'|'.$candidate['prog_id'].'|'.$candidate['intake_sess_id'];
                    if (!array_key_exists($key, $probeCache)) {
                        $probeCache[$key] = $this->creditFeeStructureExists(
                            $request,
                            $ffsStudentType,
                            $candidate['school_id'],
                            $candidate['dept_id'],
                            $candidate['prog_id'],
                            $candidate['intake_sess_id']
                        );
                    }
                    if ($probeCache[$key]) {
                        $eligibleRows[] = $candidate['row'];
                        $eligibleIds[] = $candidate['id'];
                        if (!empty($candidate['fscd_id'])) {
                            $eligibleFscdIds[] = $candidate['fscd_id'];
                        }
                        continue;
                    }
                    $candidate['reason'] = 'no_fee_structure';
                }
                $missing[] = $candidate;
            }

            return [
                'student_type_used' => $ffsStudentType,
                'fee_type_checked'  => $this->resolveFeeStructureFeeType($request->fee_type),
                'total'             => count($candidates),
                'eligible_count'    => count($eligibleIds),
                'missing_count'     => count($missing),
                'eligible_ids'      => $eligibleIds,
                'eligible_fscd_ids' => $eligibleFscdIds,
                'eligible_rows'     => $eligibleRows,
                'missing_groups'    => $this->groupMissingCreditFeeStructureStudents($missing),
            ];
        }

        /**
         * Map challan form fee type to the FIN_FEE_STRUCTURE.FFS_FEE_TYPE value.
         * Summer credit challans use Short/Summer_Course structures.
         */
        private function resolveFeeStructureFeeType($feeType){
            if ($feeType === 'Credit_hours_Summer_Course') {
                return 'Short/Summer_Course';
            }
            return $feeType;
        }

        private function creditFeeStructureExists(Request $request, $ffsStudentType, $schoolId, $deptId, $progId, $intakeSessId){
            $roadmap  = empty($request->roadmap_session) ? null : $request->roadmap_session;
            $semester = empty($request->semester_sel) ? null : $request->semester_sel;
            $feeType = $this->resolveFeeStructureFeeType($request->fee_type);

            $sql = "SELECT COUNT(*) AS CNT FROM BNUCMS.FIN_FEE_STRUCTURE
                    WHERE FFS_SCHOOL_ID = ?
                      AND FFS_DEPT_ID = ?
                      AND FFS_PROG_ID = ?
                      AND FFS_SESS_ID = ?
                      AND FFS_FEE_TYPE = ?
                      AND FFS_STUDENT_TYPE = ?
                      AND FFS_STATUS = 'E'";
            $bindings = [$schoolId, $deptId, $progId, $request->session, $feeType, $ffsStudentType];

            // Always enforce intake range when defined on the fee structure.
            // (Bulk fee page skips this for session <= 167; Summer 2026 IS 167, so that
            // gate wrongly allowed F2021 students against Fall-2023+ structures.)
            $sql .= " AND (
                        FFS_INTAKE_FROM_SESS IS NULL
                        OR (FFS_INTAKE_FROM_SESS <= ? AND FFS_INTAKE_TO_SESS >= ?)
                      )";
            $bindings[] = $intakeSessId;
            $bindings[] = $intakeSessId;

            if ($roadmap === null) {
                $sql .= " AND FFS_ROADMAP_SESSION IS NULL";
            } else {
                $sql .= " AND FFS_ROADMAP_SESSION = ?";
                $bindings[] = $roadmap;
            }
            if ($semester === null) {
                $sql .= " AND FFS_SEMESTER_NO IS NULL";
            } else {
                $sql .= " AND FFS_SEMESTER_NO = ?";
                $bindings[] = $semester;
            }

            $res = DB::select($sql, $bindings);
            return !empty($res) && $res[0]->cnt > 0;
        }

        private function groupMissingCreditFeeStructureStudents(array $missing){
            if (empty($missing)) {
                return [];
            }
            $schoolIds = []; $deptIds = []; $progIds = []; $sessIds = [];
            foreach ($missing as $row) {
                if (!empty($row['school_id'])) { $schoolIds[] = $row['school_id']; }
                if (!empty($row['dept_id'])) { $deptIds[] = $row['dept_id']; }
                if (!empty($row['prog_id'])) { $progIds[] = $row['prog_id']; }
                if (!empty($row['intake_sess_id'])) { $sessIds[] = $row['intake_sess_id']; }
            }
            $schoolNames = $this->lookupCreditNames('SCHOOLS', 'school_id', 'school_name', $schoolIds);
            $deptNames   = $this->lookupCreditNames('DEPARTMENTS', 'dept_id', 'dept_name', $deptIds);
            $progNames   = $this->lookupCreditNames('PROGRAMS', 'prog_id', 'prog_name', $progIds);
            $sessNames   = $this->lookupCreditNames('ACADEMIC_SESSIONS', 'sess_id', 'sess_name', $sessIds);

            $groups = [];
            foreach ($missing as $row) {
                $key = $row['reason'].'|'.$row['school_id'].'|'.$row['dept_id'].'|'.$row['prog_id'].'|'.$row['intake_sess_id'];
                if (!isset($groups[$key])) {
                    $groups[$key] = [
                        'reason'           => $row['reason'],
                        'school_id'        => $row['school_id'],
                        'school_name'      => isset($schoolNames[(string) $row['school_id']]) ? $schoolNames[(string) $row['school_id']] : 'NA',
                        'dept_id'          => $row['dept_id'],
                        'dept_name'        => isset($deptNames[(string) $row['dept_id']]) ? $deptNames[(string) $row['dept_id']] : 'NA',
                        'prog_id'          => $row['prog_id'],
                        'prog_name'        => isset($progNames[(string) $row['prog_id']]) ? $progNames[(string) $row['prog_id']] : 'NA',
                        'intake_sess_id'   => $row['intake_sess_id'],
                        'intake_sess_name' => isset($sessNames[(string) $row['intake_sess_id']]) ? $sessNames[(string) $row['intake_sess_id']] : 'NA',
                        'count'            => 0,
                        'students'         => [],
                    ];
                }
                $groups[$key]['count']++;
                $groups[$key]['students'][] = [
                    'id'         => $row['id'],
                    'name'       => empty($row['name']) ? 'NA' : $row['name'],
                    'enrollment' => empty($row['enrollment']) ? 'NA' : $row['enrollment'],
                ];
            }
            return array_values($groups);
        }

        private function lookupCreditNames($table, $idColumn, $nameColumn, array $ids){
            $ids = array_values(array_unique($ids));
            if (empty($ids)) {
                return [];
            }
            $map = [];
            foreach (DB::table($table)->select($idColumn, $nameColumn)->whereIn($idColumn, $ids)->get() as $row) {
                $map[(string) $row->{$idColumn}] = $row->{$nameColumn};
            }
            return $map;
        }

        public function add_creditHr_challans($request){
            // dd($request->all());
            $validator = Validator::make($request->all(),[
                'session' => 'required',
                'student_type' => 'required',
                'fee_type' => 'required',
                'student_status' => 'required',
                'due_date' => 'required',
                'valid_date' => 'required',
                'visible_date' => 'required',
                // 'degree_level'   => 'required|not_in:null,Postgraduate,Doctorate',
                'degree_level'   => 'required|in:Undergraduate,Postgraduate,Graduate',
            ]);
            if($validator->fails()) {
                return Response::json(array(
                    'success' => false,
                    'errors' => $validator->getMessageBag()->toArray()
                ), 400);
            }
            $dueDate = new DateTime($request->due_date);
            $dueDate = $dueDate->format('Y-m-d');

            $validDate = new DateTime($request->valid_date);
            $validDate = $validDate->format('Y-m-d');

            $visibleDate = new DateTime($request->visible_date);
            $visibleDate = $visibleDate->format('Y-m-d');
            $degreeLevel = $request->degree_level;
            $generatdChallan_count=0;
            // $fscd_ids = $request->fscd_ids;

            // Up-front fee structure check (school/dept/program/student type/fee type)
            $feeStructureCheck = $this->resolveCreditFeeStructureAvailability($request);
            if ($feeStructureCheck['missing_count'] > 0 && !$request->boolean('confirm_proceed')) {
                $payload = $feeStructureCheck;
                unset($payload['eligible_rows']);
                return response()->json(['success' => false, 'needs_confirmation' => true] + $payload, 409);
            }
            if ($feeStructureCheck['eligible_count'] == 0) {
                return response()->json(['success' => false, 'messege' => 'No fee structure is defined for any of the selected students.'], 400);
            }

            try {
                if($request->student_type != 'New without Registration FALL' && $request->student_type != 'New without Registration SPRING' && $request->student_type != 'New without Registration SUMMER'){//only for exisitng  and new admission with registration
                    $studentType  = $request->student_type;
                    $studentIdArr = $feeStructureCheck['eligible_ids'];
                    $fscd_ids = !empty($feeStructureCheck['eligible_fscd_ids'])
                        ? $feeStructureCheck['eligible_fscd_ids']
                        : array_values(array_filter(array_map('trim', explode(',', (string) $request->fscd_ids)), 'strlen'));
                    if (empty($fscd_ids)) {
                        return response()->json(['success' => false, 'messege' => 'No eligible students with credit data found to generate challan.'], 400);
                    }
                    $chunks = array_chunk($fscd_ids, 1000);         // split into 1000-sized array
                    $whereClauses = [];
                    $params = [];
                    // dd($chunks);
                    foreach ($chunks as $chunk) {
                        $placeholders = implode(',', array_fill(0, count($chunk), '?'));
                        $whereClauses[] = "FSCD_ID IN ($placeholders)";
                        $params = array_merge($params, $chunk);
                    }
                    $sql = "
                        SELECT *
                        FROM FIN_STUDENTS_CREDITS_DATA
                        WHERE (" . implode(' OR ', $whereClauses) . ")
                    ";
                    $fcsd_data = DB::select($sql, $params);
                    foreach($fcsd_data as $val){
                        //dd($val);
                        $enrollement = $val->fscd_regno;
                        $degree_level = $val->fscd_std_degree_lvl;
                        $enrolled_credits = $val->fscd_std_enrolled_credits;
                        // Skip students with 0 / null enrolled credit hours
                        if ($enrolled_credits === null || (float) $enrolled_credits <= 0) {
                            continue;
                        }
                        $earned_credits = $val->fscd_std_earned_credits;
                        $enrolled_plus_earned_credits = $earned_credits + $enrolled_credits;

                        $repeat_credits = $val->fscd_std_repeat_credits;
                        $allowed_credits = $val->fscd_std_allowed_credits;
                        $max_policy_credits = $val->fscd_ffp_max_cr;

                        $current_semester = $val->fscd_std_current_semester;
                        $allowed_semester = $val->fscd_std_allowed_semester;
                        $procedure = $val->fscd_procedure_name;
                        $enrollment = $val->fscd_regno;
                        $fee_type = $val->fscd_ffs_fee_type;

                        $data = [
                            'enrollment' => $enrollment,
                            'fee_type' => $fee_type,
                            'degree_level' => $degree_level,
                            'current_semester' => 'current_semester '.$current_semester,
                            'allowed_semester' => 'allowed_semester '.$allowed_semester,
                            'enrolled_credt' => 'enrolled_credt '.$enrolled_credits,
                            'allowed_credits' => 'allowed_credits '.$allowed_credits,
                            'repeat_credits' => 'repeat_credits '.$repeat_credits
                        ];
                        $concatenated = implode('----', $data);
                        // dd($concatenated);
                        $result_notification='';
                        // dd($concatenated);
                        if($degree_level == 'Undergraduate'){
                            if($current_semester <= $allowed_semester){ //From 1st to 8th semester:

                                if($enrolled_credits <= 9 || $enrolled_credits > 18){//Credit Hrs upto 9 OR exceeding 18
                                    //cancle only for upto 9 credit
                                    if($enrolled_credits <= 9){
                                        $this->cancel_challan($val, $request);
                                    }
                                    $result_notification = $this->procedure_credit_hr_wise($val, $request, $dueDate, $validDate, $visibleDate);
                                    $this->applyCreditChallanSurplus($val, $request);

                                }else if($enrolled_credits > 9 && $enrolled_credits <= 18 && $repeat_credits > 0){//Enrolled Credit Hrs between 9.5 to 18 and is repeate credithr

                                    // no need to cancel
                                    $result_notification = $this->procedure_repeat_credit_hr($val, $request, $dueDate, $validDate, $visibleDate);
                                    $this->applyCreditChallanSurplus($val, $request);

                                }else{
                                    DB::table('Testing')->insert([
                                    'description' => $concatenated,
                                    'value_one'=> 'credit hour page Undergraduate within semester range'
                                    ]);
                                }
                            }else if($current_semester > $allowed_semester){//exceeding allowed semester

                                if($enrolled_plus_earned_credits >= $allowed_credits){//equal to allowed credits

                                    $this->cancel_challan($val, $request);
                                    $result_notification = $this->procedure_credit_hr_wise($val, $request, $dueDate, $validDate, $visibleDate);
                                    $this->applyCreditChallanSurplus($val, $request);

                                }else if($enrolled_credits <= 9 || $enrolled_credits > 18){//Credit Hrs upto 9 OR exceeding 18
                                    //cancle only for upto 9 credit
                                    if($enrolled_credits <= 9){
                                        $this->cancel_challan($val, $request);
                                    }
                                    $result_notification = $this->procedure_credit_hr_wise($val, $request, $dueDate, $validDate, $visibleDate);
                                    $this->applyCreditChallanSurplus($val, $request);

                                }else if($enrolled_credits > 9 && $enrolled_credits <= 18 && $repeat_credits > 0){//Enrolled Credit Hrs between 9.5 to 18

                                    //no need to cancel
                                    $result_notification = $this->procedure_repeat_credit_hr($val, $request, $dueDate, $validDate, $visibleDate);
                                    $this->applyCreditChallanSurplus($val, $request);

                                }else{
                                    DB::table('Testing')->insert([
                                    'description' => $concatenated,
                                    'value_one'=> 'credit hour page Undergraduate exceeding semester range'
                                    ]);
                                }
                            }
                        }
                        if($degree_level == 'Graduate' || $degree_level == 'Postgraduate'){
                            if($current_semester <= $allowed_semester && $repeat_credits > 0){ //From 1st to 4th semester:

                                //no need to cancel
                                $result_notification = $this->procedure_repeat_credit_hr($val, $request, $dueDate, $validDate, $visibleDate);
                                 $this->applyCreditChallanSurplus($val, $request);

                            }else if($current_semester > $allowed_semester){//From 5 th Semester onwards

                                if($enrolled_plus_earned_credits >= $allowed_credits){//equal to allowed credits

                                    $this->cancel_challan($val, $request);
                                    $result_notification = $this->procedure_credit_hr_wise($val, $request, $dueDate, $validDate, $visibleDate);
                                    $this->applyCreditChallanSurplus($val, $request);

                                }else if($enrolled_credits <= 9 || $enrolled_credits > 12){//Credit Hrs upto 9 OR exceeding 12
                                    //cancle only for upto 9 credit
                                    if($enrolled_credits <= 9){
                                        $this->cancel_challan($val, $request);
                                    }
                                    $result_notification = $this->procedure_credit_hr_wise($val, $request, $dueDate, $validDate, $visibleDate);
                                    $this->applyCreditChallanSurplus($val, $request);

                                }else if($enrolled_credits > 9 && $enrolled_credits <= 12 && $repeat_credits > 0){//Enrolled Credit Hrs between 9.5 to 12

                                    //  no need to cancel
                                    $result_notification = $this->procedure_repeat_credit_hr($val, $request, $dueDate, $validDate, $visibleDate);
                                     $this->applyCreditChallanSurplus($val, $request);
                                }else{
                                    DB::table('Testing')->insert([
                                    'description' => $concatenated,
                                    'value_one'=> 'credit hour page Graduate/Post graduate exceeding semester range'
                                    ]);
                                }
                            }else{
                                 DB::table('Testing')->insert([
                                    'description' => $concatenated,
                                    'value_one'=> 'credit hour page Graduate/Post graduate external'
                                    ]);
                                }
                        }

                        if($result_notification){
                            // Summer rows do not carry FSCD_APP_ID here. The helper resolves the
                            // generated challan by enrollment/session and uses its actual SC_APP_ID.
                            $summerSurplusData = (object) [
                                'fscd_app_id' => null,
                                'fscd_regno'  => $enrollment,
                            ];
                            $this->applyCreditChallanSurplus($summerSurplusData, $request);
                            $generatdChallan_count++;
                        }
                    }
                    return response()->json(['success' => true, 'count'=> $generatdChallan_count]);
                }else{
                    return response()->json(['success' => false, 'messege'=> 'Something went wrong. (credit hour wise challan can not be generated for students without registration)']);
                }
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'messege'=> 'Something went wrong.', 'errors' => $e->getMessage()], 500);
            }

        }
        public function procedure_credit_hr_wise($fscd_data, $request_data, $dueDate, $validDate, $visibleDate){
            // dd('procedure_credit_hr_wise',$fscd_data, $request_data->all());
             $procedurenotification = 'fin_credit_hr_wise_challan';
                    $bindings_notification = [
                    'p_enrollment_no' => $fscd_data->fscd_regno,
                    'p_session_id' => $request_data->session,
                    'p_std_status' => $fscd_data->fscd_std_status,
                    'p_ffs_student_type' => $request_data->student_type,
                    'p_due_date' => $dueDate,
                    'p_valid_date' => $validDate,
                    'p_visible_date' => $visibleDate,
                    'p_Installment_allowed' => 'NO',
                    'P_CR_HR' => $fscd_data->fscd_std_enrolled_credits,//askable
                    'P_USER_NAME' => auth()->user()->username
                ];

                // dd('fin_credit_hr_wise_challan', $bindings_notification);
            return $result_notification = DB::executeProcedure($procedurenotification, $bindings_notification);
        }
        public function procedure_repeat_credit_hr($fscd_data, $request_data, $dueDate, $validDate, $visibleDate){
            // dd('procedure_repeat_credit_hr',$fscd_data, $request_data->all());
             $procedurenotification = 'FIN_PRO_REPEAT_CREDIT';
                $bindings_notification = [
                    'p_enrollment_no' =>  $fscd_data->fscd_regno,
                    'p_session_id' =>  $request_data->session,
                    'p_std_status' => $fscd_data->fscd_std_status, //(Active or Expected-Graduate)
                    'p_ffs_student_type' => $request_data->student_type, //(Existing/New Admission FALL etc..)
                    'p_due_date' => $dueDate,
                    'p_valid_date' => $validDate,
                    'p_visible_date' => $visibleDate,
                    'p_Installment_allowed' => 'NO',
                    'p_degree_level' => $fscd_data->fscd_std_degree_lvl,
                    'p_repeat_credit' => $fscd_data->fscd_std_repeat_credits,
                    'p_user_name' => auth()->user()->username
                ];
                // dd('FIN_PRO_REPEAT_CREDIT', $bindings_notification);
            return $result_notification = DB::executeProcedure($procedurenotification, $bindings_notification);
        }
        public function cancel_challan($fscd_data, $request_data){
            // dd($fscd_data);
            $exisitng_challan = DB::table('student_challan')
                    ->select('challan_no')
                    ->where('SC_APP_ID', $fscd_data->fscd_app_id)
                    ->where('sc_session_id', $request_data->session)
                    ->whereIn('SC_CHALLAN_TYPE', ['Full_semester', 'Short/Summer_Course'])
                    ->where('invoice_status', 'E')
                    ->get();
            // dd($exisitng_challan);
            if(!empty($exisitng_challan)){
                foreach($exisitng_challan as $val){
                    $procedureName = 'fin_bulk_challan_cancellation';
                    $bindings = [
                            'P_SC_CHALLAN_ID' => $val->challan_no,
                            'P_ENROLLMENT_NO' => $fscd_data->fscd_regno,
                            'P_SC_APP_D' => $fscd_data->fscd_app_id,
                            'P_CANCELLED_USER' => auth()->user()->username,
                             'P_REMARKS' => 'Due to issuance of credit hour challan'

                        ];
                    // dd($bindings);
                    $result = DB::executeProcedure($procedureName, $bindings);
                }

            }
        }
        /*
         * LEGACY SURPLUS CODE - intentionally disabled.
         *
         * IMPORTANT: Do not enable this together with applyCreditChallanSurplus().
         * Running both can consume the same student's surplus twice.
         *
         * public function adjust_surplus($fscd_data, $request_data){
         *     $exisitng_surplus = DB::table('FIN_FEE_ADJUSTMENT')
         *         ->select('FFA_SURPLUS_AMOUNT')
         *         ->where('FFA_APP_ID', $fscd_data->fscd_app_id)
         *         ->first();
         *
         *     if(!empty($exisitng_surplus) && $exisitng_surplus->ffa_surplus_amount > 0){
         *         DB::executeProcedure('ADJUST_FEE_BY_ENROLLMENT', [
         *             'p_enrollment_no' => $fscd_data->fscd_regno,
         *             'p_sess_id'       => $request_data->session,
         *             'p_user_name'     => auth()->user()->username,
         *         ]);
         *     }
         * }
         */

        /**
         * Apply pending student surplus to the newest generated credit-hour challan.
         *
         * The logic is kept directly inside this controller with no external surplus-service dependency.
         * Pending surplus rows are consumed oldest-first. NULL status values are treated
         * as active/pending, matching the reviewed surplus implementation.
         */
        protected function applyCreditChallanSurplus($fscd_data, $request_data)
        {
            $enrollment = isset($fscd_data->fscd_regno) ? trim((string) $fscd_data->fscd_regno) : '';
            $appId = isset($fscd_data->fscd_app_id) ? $fscd_data->fscd_app_id : null;

            if ($enrollment === '' || empty($request_data->session)) {
                return;
            }

            $challanTypes = ($request_data->fee_type === 'Credit_hours_Summer_Course')
                ? ['Short/Summer_Course']
                : ['Credit_hours'];

            /*
             * Resolve the exact newly generated challan first. For normal credit-hour
             * processing we also have FSCD_APP_ID; for Summer processing we intentionally
             * allow enrollment/session lookup and then take the real SC_APP_ID from the
             * challan itself rather than assuming SAI_ID == application ID.
             */
            $challanQuery = DB::table('student_challan')
                ->select('id', 'sc_app_id', 'challan_no', 'invoiceamount', 'sc_surplus_amount')
                ->where('SC_SESSION_ID', $request_data->session)
                ->whereRaw('LOWER(TRIM(SC_ENROLLMENT_NO)) = ?', [strtolower($enrollment)])
                ->where('INVOICE_STATUS', 'E')
                ->whereIn('SC_CHALLAN_TYPE', $challanTypes);

            if (!empty($appId)) {
                $challanQuery->where('SC_APP_ID', $appId);
            }

            $challan = $challanQuery->orderByDesc('ID')->first();

            if (!$challan) {
                return;
            }

            $appId = $challan->sc_app_id;
            $payable = round(max(0.0, (float) $challan->invoiceamount), 2);

            if (empty($appId) || $payable <= 0) {
                return;
            }

            DB::transaction(function () use ($appId, $enrollment, $challan, $payable) {
                $rows = DB::table('fin_fee_adjustment')
                    ->select('ffa_id', 'ffa_surplus_amount', 'ffa_adjust_amount', 'ffa_app_id', 'ffa_enrollment_no')
                    ->where(function ($q) use ($appId, $enrollment) {
                        $q->where('ffa_app_id', $appId)
                          ->orWhereRaw('LOWER(TRIM(ffa_enrollment_no)) = ?', [strtolower($enrollment)]);
                    })
                    ->whereRaw("(ffa_amount_status IS NULL OR UPPER(TRIM(ffa_amount_status)) = 'PENDING')")
                    ->whereRaw("(ffa_status IS NULL OR UPPER(TRIM(ffa_status)) = 'E')")
                    ->whereRaw("(ff_amnt_status IS NULL OR UPPER(TRIM(ff_amnt_status)) = 'E')")
                    ->whereRaw('NVL(ffa_surplus_amount, 0) > 0')
                    ->orderBy('ffa_id')
                    ->lockForUpdate()
                    ->get();

                if ($rows->isEmpty()) {
                    return;
                }

                $available = 0.0;
                foreach ($rows as $row) {
                    $available += (float) $row->ffa_surplus_amount;
                }
                $available = round(max(0.0, $available), 2);

                $consumed = round(min($available, $payable), 2);
                if ($consumed <= 0) {
                    return;
                }

                $newInvoice = round($payable - $consumed, 2);
                $leftToConsume = $consumed;
                $entryUser = auth()->user()->username ?? 'system';
                $entryUserId = auth()->id();
                $now = Carbon::now('Asia/Karachi');

                foreach ($rows as $row) {
                    if ($leftToConsume <= 0) {
                        break;
                    }

                    $rowAvailable = round((float) $row->ffa_surplus_amount, 2);
                    $take = round(min($rowAvailable, $leftToConsume), 2);
                    $newBalance = round($rowAvailable - $take, 2);
                    $exhausted = ($newBalance <= 0);

                    $update = [
                        'FFA_SURPLUS_AMOUNT' => $newBalance,
                        'FFA_SC_CHALLAN_NO'  => $challan->id,
                        'FFA_AMOUNT_STATUS'  => $exhausted ? 'Paid' : 'Pending',
                        'FFA_STATUS'         => $exhausted ? 'D' : 'E',
                        'FF_AMNT_STATUS'     => $exhausted ? 'D' : 'E',
                        'FFA_ADJUST_AMOUNT'  => round((float) ($row->ffa_adjust_amount ?? 0) + $take, 2),
                        'FFA_UPDATED_BY'     => $entryUser,
                        'FFA_UPDATED_AT'     => $now,
                    ];

                    if ($entryUserId !== null) {
                        $update['FFA_ENTRY_USERID'] = $entryUserId;
                    }

                    $affected = DB::table('fin_fee_adjustment')
                        ->where('ffa_id', $row->ffa_id)
                        ->update($update);

                    if ($affected < 1) {
                        throw new \RuntimeException(
                            'Surplus adjustment did not update FIN_FEE_ADJUSTMENT row ' .
                            $row->ffa_id . '. Nothing has been committed.'
                        );
                    }

                    $leftToConsume = round($leftToConsume - $take, 2);
                }

                if ($leftToConsume > 0.005) {
                    throw new \RuntimeException(
                        'Surplus could not be fully consumed from FIN_FEE_ADJUSTMENT. Nothing has been committed.'
                    );
                }

                $alreadyApplied = round((float) ($challan->sc_surplus_amount ?? 0), 2);

                DB::table('student_challan')
                    ->where('ID', $challan->id)
                    ->update([
                        'INVOICEAMOUNT'     => $newInvoice,
                        'SC_SURPLUS_AMOUNT' => round($alreadyApplied + $consumed, 2),
                    ]);
            });
        }
        public function add_summerCredithr_challans($request){
            // dd($request->all());
            $validator = Validator::make($request->all(),[
                'session' => 'required',
                'student_type' => 'required',
                'student_status' => 'required',
                'due_date' => 'required',
                'valid_date' => 'required',
                'visible_date' => 'required',
                'degree_level' => 'required',
            ]);
            if($validator->fails()) {
                return Response::json(array(
                    'success' => false,
                    'errors' => $validator->getMessageBag()->toArray()
                ), 400);
            }
            $dueDate = new DateTime($request->due_date);
            $dueDate = $dueDate->format('Y-m-d');

            $validDate = new DateTime($request->valid_date);
            $validDate = $validDate->format('Y-m-d');

            $visibleDate = new DateTime($request->visible_date);
            $visibleDate = $visibleDate->format('Y-m-d');
            $generatdChallan_count=0;

            // Up-front fee structure check (school/dept/program/student type/fee type)
            $feeStructureCheck = $this->resolveCreditFeeStructureAvailability($request);
            if ($feeStructureCheck['missing_count'] > 0 && !$request->boolean('confirm_proceed')) {
                $payload = $feeStructureCheck;
                unset($payload['eligible_rows']);
                return response()->json(['success' => false, 'needs_confirmation' => true] + $payload, 409);
            }
            if ($feeStructureCheck['eligible_count'] == 0) {
                return response()->json(['success' => false, 'messege' => 'No fee structure is defined for any of the selected students.'], 400);
            }

            // try {
                if($request->student_type == 'Existing'){//only for exisitng
                    $exemptErr = $this->storeSummerUncheckedCourseExemptions($request);
                    if ($exemptErr !== null) {
                        return response()->json([
                            'success' => false,
                            'messege' => $exemptErr,
                        ], 422);
                    }
                    $studentType  = $request->student_type;
                    $studentIdArr = $feeStructureCheck['eligible_ids'];
                    // dd($studentIdArr);
                    $searchEnrollmentNo = strtolower(trim((string) $request->input('enrollment_no', '')));
                    $studentDataQuery = DB::table('STUDENTS_APPLY_INFO')
                                    ->select(
                                            'SAI_ID',
                                            'SAI_ID',
                                            'SAI_ENROLLMENT_NO',
                                            // 'SAI_SDPO_SESS_ID',
                                            'SAI_SDPO_SCHOOL_ID',
                                            'SAI_SDPO_DEPT_ID',
                                            'SAI_SDPO_PROG_ID',
                                            // 'SAI_STUDENT_STATUS'
                                    )
                                    ->whereIn('SAI_ID', $studentIdArr);
                    // Optional single-enrollment search box still narrows further when filled
                    if ($searchEnrollmentNo !== '') {
                        $studentDataQuery->whereRaw('LOWER(SAI_ENROLLMENT_NO) = ?', [$searchEnrollmentNo]);
                    }
                    $studentData = $studentDataQuery->get();
                    // One procedure call PER SELECTED STUDENT, passing that student's own
                    // enrollment number. `enrollment_no` on the request is only the
                    // optional search box (#credit_challan_enrollment_no) and is empty
                    // whenever the user filters by session/degree instead of typing an
                    // enrollment — which left p_enrollment_no blank for every call.
                    // This mirrors procedure_credit_hr_wise(), which passes fscd_regno.
                    // dd($studentData);
                    foreach($studentData as $val){
                        $enrollment = $val->sai_enrollment_no;
                        if (empty($enrollment)) {
                            continue;   // no enrollment number -> nothing to bill
                        }
                        $procedurenotification = 'FIN_PRO_SUMMER_CREDIT_HOUR';
                        $bindings_notification = [
                            'p_enrollment_no' =>  $enrollment,
                            'p_session_id' => $request->session,
                            'p_std_status' => $request->student_status,
                            'p_ffs_student_type' => $studentType,
                            'p_due_date' => $dueDate,
                            'p_valid_date' =>  $validDate,
                            'p_visible_date' => $visibleDate,
                            'p_Installment_allowed' => 'NO',
                            'p_degree_level' => $request->degree_level,
                            'p_user_name' => auth()->user()->username
                        ];
                        // dd($bindings_notification);
                        // Execute stored procedure
                        $result_notification = DB::executeProcedure($procedurenotification, $bindings_notification);
                        if($result_notification){
                            $generatdChallan_count++;
                        }
                    }
                    return response()->json(['success' => true, 'count'=> $generatdChallan_count]);
                }else{
                    return response()->json(['success' => false, 'messege'=> 'Something went wrong. (Student type should be existing, credit hour for summer can not be generated for new students)']);
                }
            // } catch (\Exception $e) {
            //     return response()->json(['success' => false, 'messege'=> 'Something went wrong.', 'errors' => $e->getMessage()], 500);
            // }

        }

        /**
         * Persist courses the user left unchecked on the enrollment lookup table (summer credit challan only).
         * Returns null on success, or an error message string.
         */
        protected function storeSummerUncheckedCourseExemptions(Request $request): ?string
        {
            $raw = $request->input('summer_unchecked_exemptions', '[]');
            if ($raw === '' || $raw === null) {
                return null;
            }
            $rows = is_string($raw) ? json_decode($raw, true) : $raw;
            if (! is_array($rows) || count($rows) === 0) {
                return null;
            }

            $username = auth()->user()->username ?? 'system';
            $now = Carbon::now();
            $requestEnrollmentNo = trim((string) ($request->input('enrollment_no')
                ?: $request->input('credit_challan_enrollment_no', '')));

            foreach ($rows as $row) {
                if (! is_array($row)) {
                    continue;
                }
                $sessionId = $row['ce_session_id'] ?? $request->session;
                $coId = $row['ce_co_id'] ?? null;
                $courseId = $row['ce_course_id'] ?? null;
                $courseName = $row['ce_course_name'] ?? '';
                $rowEnrollmentNo = trim((string) ($row['ce_enrollment_no'] ?? ''));
                if ($rowEnrollmentNo === '') {
                    $rowEnrollmentNo = $requestEnrollmentNo;
                }
                if ($rowEnrollmentNo === '') {
                    return 'Enter the enrollment number in the search field.';
                }

                if ($sessionId === null || $sessionId === '' || $courseId === null || $courseId === '') {
                    continue;
                }

                $existsQuery = DB::table('course_exempted')
                    ->where('CE_SESSION_ID', $sessionId)
                    ->where('CE_COURSE_ID', $courseId)
                    ->where('CE_STATUS', 'E');
                if ($rowEnrollmentNo !== '') {
                    $existsQuery->whereRaw('LOWER(TRIM(CE_ENROLLMENT_NO)) = ?', [strtolower($rowEnrollmentNo)]);
                } else {
                    $existsQuery->where(function ($q) {
                        $q->whereNull('CE_ENROLLMENT_NO')->orWhere('CE_ENROLLMENT_NO', '');
                    });
                }

                if ($existsQuery->exists()) {
                    continue;
                }

                try {
                    DB::table('course_exempted')->insert([
                        'CE_SESSION_ID' => $sessionId,
                        'CE_CO_ID' => $coId,
                        'CE_COURSE_ID' => $courseId,
                        'CE_COURSE_NAME' => $courseName,
                        'CE_ENROLLMENT_NO' => $rowEnrollmentNo,
                        'CE_STATUS' => 'E',
                        'CE_ENTRY_USERNAME' => $username,
                        'CE_CREATED_AT' => $now,
                        'CE_UPDATED_AT' => $now,
                    ]);
                } catch (\Exception $e) {
                    return 'Could not save course exemption: '.$e->getMessage();
                }
            }

            return null;
        }

        public function get_challan_list(Request $request){
            // dd($request->all());
            $data = DB::table('student_challan as sc')
                ->select(
                        'sc.ID',
                        'sc.SC_ENROLLMENT_NO',
                        'sc.SC_APP_ID',
                        'SC_STUDENTNAME',
                        'acd.SESS_NAME',
                        'school.SCHOOL_NAME',
                        'dept.DEPT_NAME',
                        'prog.PROG_NAME',
                        'sc.SC_TUITION_FEE_AMNT',
                        'sc.FFS_ADMISSION_AMT',
                        'sc.FFS_SECURITY_AMT',
                        'sc.FFS_MEDICAL_AMT',
                        'sc.FFS_CREDHR_AMT',
                        'sc.SC_ACTIVITY_AMT',
                        'sc.SC_CHALLAN_STATUS',
                        'sc.SC_REPEAT_AMOUNT_5',
                        'sc.SC_ENROLLED_CR_HR',
                        DB::raw('TO_CHAR(sc.SC_ISSU_DATE, \'DD-MM-YYYY\') as SC_ISSU_DATE'),
                        DB::raw('TO_CHAR(sc.SC_DUE_DATE, \'DD-MM-YYYY\') as SC_DUE_DATE'),
                        DB::raw('TO_CHAR(sc.SC_VALID_DATE, \'DD-MM-YYYY\') as SC_VALID_DATE'),
                        'sc.INVOICEAMOUNT',

                        'sc.SC_SESSION_ID',
                        'sc.SC_SCHOOL_ID',
                        'sc.SC_DEPARTMENT_ID',
                        'sc.SC_PROGRAM_ID'
                        )
                        ->join('ACADEMIC_SESSIONS as acd', 'acd.SESS_ID', 'sc.SC_SESSION_ID')
                        ->join('SCHOOLS as school', 'school.SCHOOL_ID', 'sc.SC_SCHOOL_ID')
                        ->join('DEPARTMENTS as dept', 'dept.DEPT_ID', 'sc.SC_DEPARTMENT_ID')
                        ->join('PROGRAMS as prog', 'prog.PROG_ID', 'sc.SC_PROGRAM_ID')
                        ->where('sc.INVOICE_STATUS', 'E')
                        ->where('sc.SC_ENROLLED_CR_HR', '>', 0);
                        if($request->unpaidCheckbox == 1){//unpaid checkbox is CHECKED apply only session filter, challanStatus=UNPAID and challantype filter and IGNORE other fields
                            if(!empty($request->session_id)){
                                $data =  $data->where('sc.SC_SESSION_ID', $request->session_id)
                                                ->where('sc.SC_CHALLAN_TYPE', $request->feeType)
                                                ->where('sc.SC_CHALLAN_STATUS', 'UNPAID');
                            }
                        }else{//unpaid checkbox is NOT CHECKED apply all filters as selected
                            if(!empty($request->session_id)){
                                $data =  $data->where('sc.SC_SESSION_ID', $request->session_id);
                            }
                            if(!empty($request->school_id) && !is_array(json_decode($request->school_id))){//condition will applied only when single school is selected if all school is slected then no need to apply condition//array means all school is selected and so we apply not array condtion to apply where clause;
                            $data =  $data->where('sc.SC_SCHOOL_ID', $request->school_id);
                            }
                            if(!empty($request->department_id) && !is_array(json_decode($request->department_id))){//condition will applied only when single department is selected if all department is slected then no need to apply condition//array means all department is selected and so we apply not array condtion to apply where clause;
                                $data =  $data->where('sc.SC_DEPARTMENT_ID', $request->department_id);
                            }
                            if(!empty($request->program_id) && !is_array(json_decode($request->program_id))){//condition will applied only when single program is selected if all program is slected then no need to apply condition//array means all program is selected and so we apply not array condtion to apply where clause;
                            //    dd('program condtion applied');
                                $programs_id = $request->program_id;
                                // dd($programs_id);
                                $array_of_sch_dep_prog = explode('~~', $programs_id);
                                // dd($array_of_sch_dep_prog[2]);
                            $data =  $data->where('sc.SC_PROGRAM_ID', $array_of_sch_dep_prog[2]);
                            }
                            if(!empty($request->studentType)){
                                if(strpos($request->studentType, 'New without Registration') !== false){//if 'New without Registration' exists in student type
                                    $string = $request->studentType;
                                    $search = 'New without Registration';
                                    $replace = 'New Admission';
                                    $studentType = str_replace($search, $replace, $string);
                                    // dd('New without Registration', $studentType);
                                    $data =  $data->where('sc.SC_STUDENT_STATUS', $studentType)->whereNull('sc.SC_ENROLLMENT_NO');
                                }else{
                                    // dd('New with Registration', $request->studentType);
                                    $data =  $data->where('sc.SC_STUDENT_STATUS', $request->studentType)->whereNotNull('sc.SC_ENROLLMENT_NO');
                                }
                            }
                            if(!empty($request->feeType)){
                                if($request->feeType == 'Credit_hours'){
                                    $data =  $data->whereIn('sc.SC_CHALLAN_TYPE', ['Credit_hours', 'Full_semester']);
                                }
                                if($request->feeType == 'Credit_hours_Summer_Course'){
                                    $data =  $data->where('sc.SC_CHALLAN_TYPE', 'Short/Summer_Course');
                                }

                            }
                        }

                        $data =  $data->get();
                        // dd($data);
            return DataTables::of($data)->toJson();

        }
        public function get_student_list(Request $request){
            // dd($request->all());
            if($request->feeType == 'Credit_hours'){
                return $this->getStudents_creditHr_range($request);
            }
            if($request->feeType == 'Credit_hours_Summer_Course'){
                return $this->getStudents_summerCredithr($request);
            }
            // $school = $request->school_id;
            // $department= $request->department_id;
            // $subqueryOptionalWhere ="";
            // $sqlSessionCode = "select * from academic_sessions where sess_id = $request->session_id";
            // $ressqlSessionCode = DB::select($sqlSessionCode);
            // $sess_name = explode(' ', $ressqlSessionCode[0]->sess_name)[0];
            // if(!empty($request->studentStatus)){
            //     $subqueryOptionalWhere = " and ssr_semester_status = '".$request->studentStatus."'";//this is for the subquery to concatenate the where clause of 'active' or 'expected graduate'
            // }else{
            //     $subqueryOptionalWhere = " and ssr_semester_status IN ('Expected-Graduate', 'Active')";
            // }
            // if(!empty($request->feeType)){
            //         $challanType_conditoin =  "and sc_challan_type = '".$request->feeType ."'";
            //     }else{
            //         $challanType_conditoin =  "";
            //     }
            // // dd($subqueryOptionalWhere);
            // // dd("sai.sai_enrollment_no = (select sai_enrollment_nofrom students_semester_recordwhere students_semester_record.sai_enrollment_no = sai.sai_enrollment_noand sess_id =  ".$request->session_id."".$subqueryOptionalWhere.")");
            // if(strpos($request->studentType, 'New without Registration') !== false){

            //     $string = $request->studentType;
            //     $search = 'New without Registration';
            //     $replace = 'New Admission';
            //     $student_status = str_replace($search, $replace, $string);///changes "New without Registration FALL/SPRING/SUMMER" to "New Admission FALL/SPRING/SUMMER"
            //     // dd($student_status);
            //     $data = DB::table('ADMISSION_APPLY_INFO as aai')
            //             ->select(
            //                     'aai.AAI_ID',
            //                     'aai.AAI_STATUS',
            //                     'aai.cms_sai_enrollment_no',
            //                     'api.API_NAME',

            //                     'acd.SESS_NAME',
            //                     'school.SCHOOL_NAME',
            //                     'dept.DEPT_NAME',
            //                     'prog.PROG_NAME',
            //                     DB::raw("(SELECT COUNT(*) FROM student_challan sc WHERE sc.SC_APP_ID = aai.AAI_ID and sc_student_status = '".$student_status." and SC_SESSION_ID = ".$request->session_id."' ".$challanType_conditoin." and sc_enrollment_no IS NULL) as challan_count") // Raw query to count records
            //                     )
            //                     ->leftjoin('ADMISSION_PERSONAL_INFO as api', 'api.API_ID', 'aai.AAI_API_ID')
            //                     ->leftjoin('ACADEMIC_SESSIONS as acd', 'acd.SESS_ID', 'aai.AAI_SDPO_SESS_ID')
            //                     ->leftjoin('SCHOOLS as school', 'school.SCHOOL_ID', 'aai.AAI_SDPO_SCHOOL_ID')
            //                     ->leftjoin('DEPARTMENTS as dept', 'dept.DEPT_ID', 'aai.AAI_SDPO_DEPT_ID')
            //                     ->leftjoin('PROGRAMS as prog', 'prog.PROG_ID', 'aai.AAI_SDPO_PROG_ID')


            //                     ->whereNull('aai.cms_sai_enrollment_no')
            //                     ->where('aai.aai_status', 'Selected')
            //                     ->where('aai.aai_pf_status', 'Fee Verified')
            //                     ->where('aai.AAI_QUALIFICATION_STATUS', 'Accepted');

            //                     // if(!empty($request->scholrshipType)){
            //                     //     $data = $data->leftjoin('students_scholarships_details as ssd', 'ssd.SSD_APP_ID', 'aai.aai_id')
            //                     //                     ->where('ssd.ssd_type','=', $request->scholrshipType)
            //                     //                     ->where('ssd.SSD_SESS_ID','=', $request->session_id);
            //                     // }
            //                     if(!empty($request->scholrshipType)){
            //                      $data = $data->leftJoin('students_scholarships_details as ssd', 'ssd.SSD_APP_ID', '=', 'aai.AAI_ID')
            //                             ->leftJoin('FIN_STUDENTS_SCHOLARSHIPS_HOSTEL_DETAILS as sshd', 'sshd.SSHD_APP_ID', '=', 'aai.AAI_ID')
            //                             ->whereRaw(
            //                                 '((ssd.SSD_TYPE = ? AND ssd.SSD_SESS_ID = ?) OR (sshd.SSHD_TYPE = ? AND sshd.SSHD_SESS_ID = ?))',
            //                                 [
            //                                     $request->scholrshipType,
            //                                     $request->session_id,
            //                                     $request->scholrshipType,
            //                                     $request->session_id,
            //                                 ]
            //                             );

            //                     }
            //                     if(!empty($request->session_id)){
            //                         $data = $data->where('aai.AAI_SDPO_SESS_ID','=', $request->session_id);
            //                     }
            //                     if(!empty($request->session_id)){
            //                         $data = $data->where('aai.AAI_SDPO_SESS_ID','=', $request->session_id);
            //                     }
            //                     if(!empty($request->school_id && !is_array(json_decode($request->school_id)))){
            //                         $data = $data->where('aai.AAI_SDPO_SCHOOL_ID', $request->school_id);
            //                     }
            //                     if(!empty($request->department_id && !is_array(json_decode($request->department_id)))){
            //                         $data = $data->where('aai.AAI_SDPO_DEPT_ID', $request->department_id);
            //                     }
            //                     if(!empty($request->degree_level)){
            //                         $data = $data->where('prog.PROG_DEGREE_LEVEL', $request->degree_level);
            //                     }
            //                     if(!empty($request->program_id) && !is_array(json_decode($request->program_id))){//condition will applied only when single program is selected if all program is slected then no need to apply condition
            //                         //    dd('program condtion applied');
            //                         $programs_id = $request->program_id;
            //                         // dd($programs_id);
            //                         $array_of_sch_dep_prog = explode('~~', $programs_id);
            //                         // dd($array_of_sch_dep_prog[2]);
            //                     $data =  $data->where('aai.AAI_SDPO_PROG_ID', $array_of_sch_dep_prog[2]);
            //                     }
            //     $data = $data->get();
            //     // dd($data);
            // }else{
            //     //  dd('existing and with');
            //     $data = DB::table('STUDENTS_APPLY_INFO as sai')
            //             ->select(
            //                     'sai.SAI_ID',
            //                     'aai.aai_id',
            //                     'sai.SAI_STUDENT_STATUS',
            //                     'sai.SAI_ENROLLMENT_NO',
            //                     // 'sai.SAI_PROGRAMID',
            //                     // 'sai.SAI_DEGREECODE',

            //                     'spi.SPI_NAME',

            //                     'acd.SESS_NAME',
            //                     'school.SCHOOL_NAME',
            //                     'dept.DEPT_NAME',
            //                     'prog.PROG_NAME',
            //                     DB::raw("(SELECT COUNT(*) FROM student_challan sc WHERE sc.SC_ENROLLMENT_NO = sai.SAI_ENROLLMENT_NO and SC_SESSION_ID = ".$request->session_id." ".$challanType_conditoin.") as challan_count") // Raw query to count records
            //                     )
            //                     //->leftjoin('student_challan as sc', 'sc.SC_ENROLLMENT_NO', 'sai.SAI_ENROLLMENT_NO')
            //                     ->leftjoin('STUDENTS_PERSONAL_INFO as spi', 'spi.SPI_ID', 'sai.SAI_SPI_ID')
            //                     ->leftJoin('ADMISSION_APPLY_INFO as aai', function($join) {
            //                         $join->on('aai.AAI_API_ID', '=', 'sai.SAI_API_ID')
            //                             ->whereRaw('LOWER(aai.CMS_SAI_ENROLLMENT_NO) = LOWER(sai.SAI_ENROLLMENT_NO)');
            //                     })
            //                     ->leftjoin('ACADEMIC_SESSIONS as acd', 'acd.SESS_ID', 'sai.SAI_SDPO_SESS_ID')
            //                     ->leftjoin('SCHOOLS as school', 'school.SCHOOL_ID', 'sai.SAI_SDPO_SCHOOL_ID')
            //                     ->leftjoin('DEPARTMENTS as dept', 'dept.DEPT_ID', 'sai.SAI_SDPO_DEPT_ID')
            //                     ->leftjoin('PROGRAMS as prog', 'prog.PROG_ID', 'sai.SAI_SDPO_PROG_ID')
            //                     ->whereIn('sai.SAI_STUDENT_STATUS', ['Expected-Graduate', 'Active']);
            //                     // ->where('sai.SAI_STUDENT_STATUS', 'Active')
            //                     // ->whereNull('sc.SC_ENROLLMENT_NO');
            //                     if(!empty($request->scholrshipType)){
            //                      $data = $data->leftJoin('students_scholarships_details as ssd', 'ssd.SSD_APP_ID', '=', 'aai.AAI_ID')
            //                             ->leftJoin('FIN_STUDENTS_SCHOLARSHIPS_HOSTEL_DETAILS as sshd', 'sshd.SSHD_APP_ID', '=', 'aai.AAI_ID')
            //                             ->whereRaw(
            //                                 '((ssd.SSD_TYPE = ? AND ssd.SSD_SESS_ID = ?) OR (sshd.SSHD_TYPE = ? AND sshd.SSHD_SESS_ID = ?))',
            //                                 [
            //                                     $request->scholrshipType,
            //                                     $request->session_id,
            //                                     $request->scholrshipType,
            //                                     $request->session_id,
            //                                 ]
            //                             );

            //                     }
            //                     if(!empty($request->session_id)){

            //                         if(!empty($request->studentType)){
            //                             if($request->studentType == 'Existing' && $sess_name == 'Spring'){//show students of all sessions before the selected session But skiping the last FALL session
            //                                 $data = $data->where('sai.SAI_SDPO_SESS_ID','<', $request->session_id-1);

            //                             }elseif($request->studentType == 'New Admission FALL' && $sess_name == 'Spring'){//show students of session before the selected session
            //                                 $data = $data->where('sai.SAI_SDPO_SESS_ID','=', $request->session_id-1);

            //                             }elseif($request->studentType == 'New Admission SPRING' && $sess_name == 'Spring'){//show students of session selected only
            //                                 $data = $data->where('sai.SAI_SDPO_SESS_ID','=', $request->session_id);

            //                             }elseif($request->studentType == 'Existing' && $sess_name == 'Summer'){//show students of all sessions before the selected session
            //                                 $data = $data->where('sai.SAI_SDPO_SESS_ID','<', $request->session_id);

            //                             }elseif($request->studentType == 'New Admission SUMMER' && $sess_name == 'Summer'){//show students of session selected only
            //                                     $data = $data->where('sai.SAI_SDPO_SESS_ID','=', $request->session_id);

            //                             }elseif($request->studentType == 'Existing' && $sess_name == 'Fall'){//show students of all sessions before the selected session
            //                                     $data = $data->where('sai.SAI_SDPO_SESS_ID','<', $request->session_id);

            //                             }elseif($request->studentType == 'New Admission FALL' && $sess_name == 'Fall'){//show students of session selected only
            //                                     $data = $data->where('sai.SAI_SDPO_SESS_ID','=', $request->session_id);

            //                             }else{//Null case
            //                                 $data = $data->whereNull('sai.SAI_SDPO_SESS_ID');
            //                             }
            //                         }

            //                     }
            //                     if(!empty($request->school_id && !is_array(json_decode($request->school_id)))){
            //                         $data = $data->where('sai.SAI_SDPO_SCHOOL_ID', $request->school_id);
            //                     }
            //                     if(!empty($request->department_id && !is_array(json_decode($request->department_id)))){
            //                         $data = $data->where('sai.SAI_SDPO_DEPT_ID', $request->department_id);
            //                     }
            //                     if(!empty($request->degree_level)){
            //                         $data = $data->where('prog.PROG_DEGREE_LEVEL', $request->degree_level);
            //                     }
            //                     if(!empty($request->program_id) && !is_array(json_decode($request->program_id))){//condition will applied only when single program is selected if all program is slected then no need to apply condition
            //                         //    dd('program condtion applied');
            //                         $programs_id = $request->program_id;
            //                         // dd($programs_id);
            //                         $array_of_sch_dep_prog = explode('~~', $programs_id);
            //                         // dd($array_of_sch_dep_prog[2]);
            //                     $data =  $data->where('sai.SAI_SDPO_PROG_ID', $array_of_sch_dep_prog[2]);
            //                     }
            //                     if(!empty($request->studentStatus)){
            //                         $data = $data->where('sai.SAI_STUDENT_STATUS', $request->studentStatus);
            //                     }
            //     $data = $data->get();
            //     // dd($data);
            // }

            //                 //  dd($data);
            // return DataTables::of($data)->toJson();
        }
        public function getStudents_creditHr_range($request){
            // dd($request->all());
            // $students_credithr = $request->students_credithr;
            $sqlSessionCode = "select * from academic_sessions where sess_id = $request->session_id";
            $ressqlSessionCode = DB::select($sqlSessionCode);
            $sess_name = explode(' ', $ressqlSessionCode[0]->sess_name)[0];
            $data = DB::table('FIN_STUDENTS_CREDITS_DATA as fscd')
                    ->select(
                            'sai.SAI_ID',
                            'fscd.*',
                            'spi.SPI_NAME',
                            'acd_enroll_session.SESS_NAME as enrollment_session',
                            'acd_roadmap_session.SESS_NAME as roadmap_session',
                            'school.SCHOOL_NAME',
                            'dept.DEPT_NAME',
                            'prog.PROG_NAME',

                            // DB::raw('(SELECT COUNT(*) FROM student_challan sc WHERE sc.SC_ENROLLMENT_NO = sai.SAI_ENROLLMENT_NO) as challan_count') // Raw query to count records
                            )
                            ->join('STUDENTS_APPLY_INFO as sai', 'sai.SAI_ENROLLMENT_NO','fscd.FSCD_REGNO')
                            ->leftjoin('STUDENTS_PERSONAL_INFO as spi', 'spi.SPI_ID', 'sai.SAI_SPI_ID')

                            ->leftjoin('ACADEMIC_SESSIONS as acd_enroll_session', 'acd_enroll_session.SESS_ID', 'fscd.FSCD_STD_ENROLLMENT_SESSION')
                            ->leftjoin('ACADEMIC_SESSIONS as acd_roadmap_session', 'acd_roadmap_session.SESS_ID', 'fscd.FSCD_STD_ROADMAP_SESSION')
                            ->leftjoin('SCHOOLS as school', 'school.SCHOOL_ID', 'fscd.FSCD_STD_SCHOOL_ID')
                            ->leftjoin('DEPARTMENTS as dept', 'dept.DEPT_ID', 'fscd.FSCD_STD_DEPT_ID')
                            ->leftjoin('PROGRAMS as prog', 'prog.PROG_ID', 'fscd.FSCD_STD_PROG_ID')
                            ->where('fscd.FSCD_STD_ENROLLMENT_SESSION', '=', $request->session_id)
                            ->whereIn('fscd.fscd_std_status', ['Defer', 'Active', 'Stuck-Off', 'Left']);
                            // ->whereIn('sai.SAI_ENROLLMENT_NO', $enrollmentNumbers)
                            // ->whereRaw("acd.SESS_NAME NOT LIKE ?", ['Summer%']);
                            // if(!empty($request->studentType)){
                            //     if($request->studentType == 'Existing'){//existing students case
                            //         $data = $data->where('sai.SAI_SDPO_SESS_ID','<', 162);//162 = Fall2024


                            //     }elseif($request->studentType == 'New Admission FALL'){//new student case
                            //         $data = $data->where('sai.SAI_SDPO_SESS_ID','>=', 162);
                            //     }else{

                            //     }
                            // }
                            ////
                            if(!empty($request->session_id)){
                                // dd($request->studentType,$sess_name );
                                    if(!empty($request->studentType)){
                                        if($request->studentType == 'Existing' && $sess_name == 'Spring'){//show students of all sessions before the selected session But skiping the last FALL session
                                            //   dd(1);
                                            $data = $data->where('sai.SAI_SDPO_SESS_ID','<', $request->session_id-1);

                                        }elseif($request->studentType == 'New Admission FALL' && $sess_name == 'Spring'){//show students of session before the selected session
                                            // dd(2);
                                            $data = $data->where('sai.SAI_SDPO_SESS_ID','=', $request->session_id-1);

                                        }elseif($request->studentType == 'New Admission SPRING' && $sess_name == 'Spring'){//show students of session selected only
                                            // dd(3);
                                            $data = $data->where('sai.SAI_SDPO_SESS_ID','=', $request->session_id);

                                        }elseif($request->studentType == 'Existing' && $sess_name == 'Summer'){//show students of all sessions before the selected session
                                            // dd(4);
                                            $data = $data->where('sai.SAI_SDPO_SESS_ID','<', $request->session_id);

                                        }elseif($request->studentType == 'New Admission SUMMER' && $sess_name == 'Summer'){//show students of session selected only
                                            // dd(5);
                                            $data = $data->where('sai.SAI_SDPO_SESS_ID','=', $request->session_id);

                                        }elseif($request->studentType == 'Existing' && $sess_name == 'Fall'){//show students of all sessions before the selected session
                                            // dd(6);
                                            $data = $data->where('sai.SAI_SDPO_SESS_ID','<', $request->session_id);

                                        }elseif($request->studentType == 'New Admission FALL' && $sess_name == 'Fall'){//show students of session selected only
                                            // dd(7);
                                            $data = $data->where('sai.SAI_SDPO_SESS_ID','=', $request->session_id);

                                        }else{//Null case
                                            // dd(8);
                                            $data = $data->whereNull('sai.SAI_SDPO_SESS_ID');
                                        }
                                    }

                                }
                            /////
                            if(!empty($request->school_id && !is_array(json_decode($request->school_id)))){
                                $data = $data->where('sai.SAI_SDPO_SCHOOL_ID', $request->school_id);

                            }
                            if(!empty($request->department_id && !is_array(json_decode($request->department_id)))){
                                $data = $data->where('sai.SAI_SDPO_DEPT_ID', $request->department_id);
                            }
                            if(!empty($request->degree_level)){
                                $data = $data->where('prog.PROG_DEGREE_LEVEL', $request->degree_level);
                            }
                            if(!empty($request->studentStatus)){
                                $data = $data->where('fscd.fscd_std_status', $request->studentStatus);
                            }
                $updatedStudents = $data = $data->orderby('fscd.FSCD_APP_ID')->get();
                // dd($updatedStudents);
                // $credits = collect($student_crdithr_data);
                // // dd($data);
                // // Map over students collection and add total_credits
                // $updatedStudents = $data->map(function ($student) use ($credits) {
                //     // Find matching credit record
                //     $credit = $credits->firstWhere('sc_sai_enrollment_no', $student->sai_enrollment_no);

                //     // Add total_credits if found, otherwise set to null
                //     $student->total_credits = $credit->total_credits ?? '0';
                //     $student->credit_flag = $credit->credit_flag ?? 'N/A';

                //     return $student;
                // });
                // dd($updatedStudents);
            return DataTables::of($updatedStudents)->toJson();
        }
       public function getStudents_summerCredithr($request){
            $data = DB::table('STUDENTS_APPLY_INFO as sai')
                    ->select(
                            'sai.SAI_ID',
                            'aai.aai_id',

                            'sai.SAI_STUDENT_STATUS',
                            'sai.SAI_ENROLLMENT_NO',
                            // 'sai.SAI_PROGRAMID',
                            // 'sai.SAI_DEGREECODE',
                            'spi.SPI_NAME',
                            'acd.SESS_NAME',
                            'school.SCHOOL_NAME',
                            'dept.DEPT_NAME',
                            'prog.PROG_NAME',
                            'fscd.FSCD_STD_ENROLLED_CREDITS',
                            // DB::raw('(SELECT COUNT(*) FROM student_challan sc WHERE sc.SC_ENROLLMENT_NO = sai.SAI_ENROLLMENT_NO) as challan_count') // Raw query to count records
                            )
                            //->leftjoin('student_challan as sc', 'sc.SC_ENROLLMENT_NO', 'sai.SAI_ENROLLMENT_NO')
                            ->leftjoin('STUDENTS_PERSONAL_INFO as spi', 'spi.SPI_ID', 'sai.SAI_SPI_ID')
                            ->leftjoin('ADMISSION_PERSONAL_INFO as api', 'api.API_ID', '=', 'spi.API_ID')
                            ->leftjoin('ADMISSION_APPLY_INFO as aai', function ($join) {
                                $join->on('aai.AAI_API_ID', '=', 'api.API_ID')
                                        ->whereColumn('aai.CMS_SAI_ENROLLMENT_NO', 'sai.SAI_ENROLLMENT_NO');
                            })
                            // ->leftjoin('ADMISSION_APPLY_INFO as aai', 'aai.AAI_API_ID', '=', 'api.API_ID')
                            ->leftjoin('ACADEMIC_SESSIONS as acd', 'acd.SESS_ID', 'sai.SAI_SDPO_SESS_ID')
                            ->leftjoin('SCHOOLS as school', 'school.SCHOOL_ID', 'sai.SAI_SDPO_SCHOOL_ID')
                            ->leftjoin('DEPARTMENTS as dept', 'dept.DEPT_ID', 'sai.SAI_SDPO_DEPT_ID')
                            ->leftjoin('PROGRAMS as prog', 'prog.PROG_ID', 'sai.SAI_SDPO_PROG_ID')
                            // Only list students who actually have credits data for the
                            // selected session (from #session_sel). The session predicate
                            // lives in the JOIN, so this is an inner-style match by intent
                            // and stays honest about dropping students with no credits row.
                            ->join('FIN_STUDENTS_CREDITS_DATA as fscd', function ($join) use ($request) {
                                $join->on('fscd.FSCD_REGNO', '=', 'sai.SAI_ENROLLMENT_NO')
                                     ->where('fscd.FSCD_STD_ENROLLMENT_SESSION', '=', $request->session_id);
                            })
                            ->whereIn('sai.SAI_STUDENT_STATUS', ['Expected-Graduate', 'Active', 'Left']);
                            if(!empty($request->school_id && !is_array(json_decode($request->school_id)))){
                                $data = $data->where('sai.SAI_SDPO_SCHOOL_ID', $request->school_id);

                            }
                            if(!empty($request->department_id && !is_array(json_decode($request->department_id)))){
                                $data = $data->where('sai.SAI_SDPO_DEPT_ID', $request->department_id);
                            }
                            if(!empty($request->studentStatus)){
                                $data = $data->where('sai.SAI_STUDENT_STATUS', $request->studentStatus);
                            }
                $data = $data->orderby('aai.aai_id')->get();

            return DataTables::of($data)->toJson();
        }
        public function get_feeAdjusted_list(Request $request){
            $data = DB::table('FIN_FEE_ADJUSTMENT as ffa')
            ->select(
                    'ffa.FFA_ID',
                    'ffa.FFA_SC_CHALLAN_NO',
                    'ffa.FFA_ENROLLMENT_NO',
                    'ffa.FFA_APP_ID',
                    'ffa.FFA_SURPLUS_AMOUNT',
                    'ffa.FFA_REMARKS',
                    'ffa.FFA_ACTUAL_AMOUNT',
                    'ffa.FFA_INVOICE_AMOUNT',
                    'ffa.FFA_CHALLAN_STATUS',
                    'ffa.FFA_STUDENT_STATUS',
                    'ffa.ffa_amount_status',
                    'ffa.FFA_CHALLAN_TYPE',
                    'ffa.FFA_SCHOLARSHIP_AMOUNT',

                    'ffac.FFAC_ID',
                    'ffac.FFAC_TITLE',
                    'ffc.NAME'
                    )
                    ->leftjoin('FIN_FEE_ADJUSTMENT_CATEGORY as ffac', 'ffac.FFAC_ID', 'ffa.FFA_ADJUSTMENT_CATEGORY')
                    ->leftjoin('FIN_FEE_CATEGORY as ffc', 'ffc.ID', 'ffac.FFAC_FEE_CTGRY')
                    ->where('FFA_STATUS', 'E')
                    ->orderBy('ffa.FFA_CREATED_AT', 'desc')
                    ->get();
                // dd($data);
            return DataTables::of($data)->toJson();
        }
        function print_challan(Request $request){
                // dd($request->all());
                $challan_no = $request->challanNo;
                // Get Personal Info and fees other then courses
                //$data_qry='select * from MV_feechallanwisesummary where BuyerCodeChallanNo='.$challan_no;
                $data_qry='select
                scholarship_type.scholarship_name,
                schools.school_name,
                dept_name,
                programs.prog_name,
                sess_name,
                student_challan.*,
                fin_scholarship_discounts.*,
                sc_surplus_amount,
                sc_activity_amt,
                sc_studentname,
                sc_fathername,

                (select FBC_VISIBLE_DATE from FIN_BLK_CHALLAN_GEN where fbc_school_id = sc_school_id and fbc_department_id = sc_department_id and fbc_program_id = sc_program_id and rownum =1) as visible_date
                from student_challan
                LEFT join fin_scholarship_discounts on  sc_app_id=fin_scholarship_discounts.fsd_aai_id
                LEFT JOIN scholarship_type ON fin_scholarship_discounts.fsd_scholarship_type=scholarship_type.scholarship_id
                LEFT JOIN fin_fee_adjustment ON fin_fee_adjustment.ffa_enrollment_no = student_challan.sc_enrollment_no
                left join schools on school_id = sc_school_id
                left join departments on dept_id = sc_department_id
                left join programs on prog_id = sc_program_id
                left join academic_sessions on sess_id = sc_session_id
                where student_challan.id='.$challan_no;
            // echo $data_qry;exit;
                $data = DB::select($data_qry);
                // Get all courses with fee --fin_scholarship_discounts.fsd_enrollment_no=student_challan.sc_enrollment_no
                //$data_det_qry='select * from feechallanwisesummary_det@acc where "BuyerCodeChallanNo"='.$challan_no.' order by "srno" asc';
                $data_det_qry='select * from student_challan
                LEFT join fin_scholarship_discounts on sc_app_id=fin_scholarship_discounts.fsd_aai_id
                and fin_scholarship_discounts.fsd_sesion_id = student_challan.sc_session_id
                LEFT JOIN scholarship_type ON fin_scholarship_discounts.fsd_scholarship_type=scholarship_type.scholarship_id
                where id='.$challan_no.' order by id asc';
                $data_detail = DB::select($data_det_qry);
            // echo $data_qry;exit;
                //    dd($data, $data_detail);
                return view('grader.financial.print_challan_preview',array('obj' => $data,'details' => $data_detail));

        }
        function creditHr_data_entry(Request $request){
            // dd($request->all());
            $session_id = $request->session_id;
            $action = $request->action;
            if($action == 'update'){
                DB::beginTransaction();
                try {
                    $this->update_credits_data($session_id);
                    DB::commit();

                    return response()->json([
                        'status' => true,
                        'message' => 'Data processed successfully.'
                    ]);

                } catch (\Exception $e) {

                    DB::rollBack();

                    return response()->json([
                        'status' => false,
                        'message' => 'Process failed. Transaction rolled back.',
                        'error' => $e->getMessage()
                    ], 500);
                }
            }
            // Validate input
            if (!$session_id) {
                return response()->json([
                    'status' => false,
                    'message' => 'Session ID is required.'
                ], 400);
            }
            $exists = DB::table('FIN_STUDENTS_CREDITS_DATA')
                ->where('FSCD_STD_ENROLLMENT_SESSION', $session_id)
                ->exists();

            if ($exists) {
                return response()->json([
                    'status' => false,
                    'message' => 'Entry with this session already present.'
                ]);
            }
            // Insert new record
            $students_query = "WITH all_students AS (
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
                    AND SC_CO_SESS_ID = $session_id
                    AND sc_sai_enrollment_no NOT LIKE '%SUM%'
                    AND CO_STATUS = 'E'
                    AND co_course_id NOT IN (select CE_COURSE_ID from course_exempted)
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
                    AND e.SC_CO_SESS_ID = $session_id
                    AND co.CO_STATUS = 'E'
                    AND e.sc_sai_enrollment_no NOT LIKE '%SUM%'
                    AND co.co_course_id NOT IN (select CE_COURSE_ID from course_exempted)
                    AND EXISTS (
                        SELECT 1
                        FROM grade_history gh
                        WHERE gh.gh_sai_enrollment_no = e.sc_sai_enrollment_no
                        AND gh.gh_co_course_id = co.co_course_id
                        AND gh.gh_co_sess_id >= 163
                        AND gh.gh_co_sess_id != $session_id
                    )
                GROUP BY e.sc_sai_enrollment_no
            )
            SELECT
                ec.sc_sai_enrollment_no AS regno,
                ec.total_credits AS total_enrolled_credits,
                NVL(rc.total_credits, 0) AS total_repeated_credits,
                ec.sai_student_status,
                ec.PROG_DEGREE_LEVEL
            FROM all_students ec
            LEFT JOIN repeat_cred rc
                ON ec.sc_sai_enrollment_no = rc.sc_sai_enrollment_no
        ";

        $students = DB::select($students_query);

       if (empty($students)) {
            return response()->json([
                'status' => false,
                'message' => 'No students found for the selected session.'
            ]);
        }

        DB::beginTransaction();

        try {

            foreach ($students as $student) {

                $procedureName = 'fin_credit_hr_wise_challan_dummy';

                $bindings = [
                    'regnoo' => $student->regno,
                    'session_id' => $session_id,
                    'v_enrolled' => $student->total_enrolled_credits,
                    'v_repeate' => $student->total_repeated_credits,
                    'v_status' => $student->sai_student_status,
                    'v_prog_degree_level' => $student->prog_degree_level
                ];

                DB::executeProcedure($procedureName, $bindings);
            }
            $this->update_credits_data($session_id);
            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Data processed successfully.'
            ]);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Process failed. Transaction rolled back.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function getExemptCourses(Request $request)
    {
        $session_id = $request->sessionId;
        $exempt_course = DB::select("
            select *
            from course_exempted
            where CE_STATUS = 'E'
            and CE_SESSION_ID = ?
        ", [$session_id]);

        return response()->json($exempt_course);
    }

    /**
     * Student course rows for credit-challan page (enrollment lookup).
     * Joins courses_offered; optional school / department / program filters match get_challan_list (skip when "All").
     */
    public function getEnrollmentStudentCourses(Request $request)
    {
        // dd($request->all());
                $validator = Validator::make($request->all(), [
            'enrollment_no' => 'required|string|max:80',
            'session_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $enrollment = trim($request->enrollment_no);
        $sessionId = $request->session_id;
        $enrollmentLower = strtolower($enrollment);

        $conditions = [
            'LOWER(e.sc_sai_enrollment_no) = ?',
            'e.sc_enrollment_status = 1',
            "e.SC_STATUS IN ('A', 'Block', 'Withdrawal')",
            'e.SC_CO_SESS_ID = ?',
            "co.CO_STATUS = 'E'",
        ];
        // SQL ? order: (1) EXISTS enrollment, (2) WHERE enrollment, (3) WHERE session
        $bindings = [$enrollmentLower, $enrollmentLower, $sessionId];

        // if (! empty($request->school_id) && ! is_array(json_decode($request->school_id))) {
        //     $conditions[] = 'e.SC_CO_SCHOOL_ID = ?';
        //     $bindings[] = $request->school_id;
        // }
        // if (! empty($request->department_id) && ! is_array(json_decode($request->department_id))) {
        //     $conditions[] = 'e.SC_CO_DEPT_ID = ?';
        //     $bindings[] = $request->department_id;
        // }
        // if (! empty($request->program_id) && ! is_array(json_decode($request->program_id))) {
        //     $programs_id = $request->program_id;
        //     $array_of_sch_dep_prog = explode('~~', $programs_id);
        //     $progId = $array_of_sch_dep_prog[2] ?? $programs_id;
        //     $conditions[] = 'e.SC_CO_PROG_ID = ?';
        //     $bindings[] = $progId;
        // }

        $sql = '
            SELECT
                e.SC_COURSE_ID,
                e.sc_co_id,
                e.SC_PC_COURSE_CR AS credit_hours,
                co.co_school_id AS school_id,
                e.sc_cms_course_co_id,
                co.co_course_id AS co_course_id,
                e.SC_STATUS,
                co.co_sess_id,
                crs.COURSE_CODE,
                crs.COURSE_CD_NAME AS course_name,
                sch.SCHOOL_NAME,
                d.DEPT_NAME,
                p.PROG_NAME,
                sess.SESS_NAME,
                CASE WHEN EXISTS (
                    SELECT 1 FROM course_exempted exm
                    WHERE exm.CE_SESSION_ID = e.SC_CO_SESS_ID
                    AND LOWER(TRIM(exm.CE_ENROLLMENT_NO)) = ?
                    AND exm.CE_COURSE_ID = co.co_course_id
                    AND exm.CE_STATUS = \'E\'
                ) THEN 1 ELSE 0 END AS has_course_exemption
            FROM student_courses e
            INNER JOIN courses_offered co ON e.sc_cms_course_co_id = co.co_id
            LEFT JOIN courses_two crs ON co.co_course_id = crs.course_id
            LEFT JOIN schools sch ON e.SC_CO_SCHOOL_ID = sch.SCHOOL_ID
            LEFT JOIN departments d ON e.SC_CO_DEPT_ID = d.DEPT_ID
            LEFT JOIN programs p ON e.SC_CO_PROG_ID = p.PROG_ID
            LEFT JOIN academic_sessions sess ON e.SC_CO_SESS_ID = sess.SESS_ID
            WHERE '.implode(' AND ', $conditions).'
            ORDER BY crs.COURSE_CODE NULLS LAST, e.sc_co_id
        ';
        // dd($bindings);
        // echo $sql;exit;
        try {
            $rows = DB::select($sql, $bindings);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Could not load courses.',
                'error' => $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'success' => true,
            'data' => $rows,
        ]);
    }

    /**
     * Delete one student-specific course_exempted row (group 644 only).
     */
    public function deleteEnrollmentCourseExemption(Request $request)
    {
        if ((int) auth()->user()->group_id !== 644) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized.',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'session_id' => 'required',
            'enrollment_no' => 'required|string|max:255',
            'ce_course_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $sessionId = $request->session_id;
        $enrollmentLower = strtolower(trim((string) $request->enrollment_no));
        $courseId = $request->ce_course_id;

        $query = DB::table('course_exempted')
            ->where('CE_SESSION_ID', $sessionId)
            ->whereRaw('LOWER(TRIM(CE_ENROLLMENT_NO)) = ?', [$enrollmentLower])
            ->where('CE_COURSE_ID', $courseId)
            ->where('CE_STATUS', 'E');

        if ($request->filled('ce_co_id')) {
            $query->where('CE_CO_ID', $request->ce_co_id);
        }

        try {
            $deleted = $query->delete();
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Could not delete exemption: '.$e->getMessage(),
            ], 500);
        }

        return response()->json([
            'success' => true,
            'deleted' => $deleted,
        ]);
    }

    public function update_credits_data($session_id){
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

                WHERE f.FSCD_STD_ENROLLMENT_SESSION = :session_id";

                DB::update($query, [
                    'session_id' => $session_id
                ]);
            return true;
    }
}
