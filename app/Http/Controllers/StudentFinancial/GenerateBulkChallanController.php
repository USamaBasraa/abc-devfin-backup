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

class GenerateBulkChallanController extends Controller{

        public function generateFeeChallan(){
            $sessions = \App::call('App\Http\Controllers\Grader\AcademicSessionController@retrieve');
            $schools =  Schools::where('school_id', '!=', 11)->get();
            $departments = array(); // $this->school->fetch();
            $facultyCourses = \App::call('App\Http\Controllers\Grader\FacultyCoursesController@retrieve');
            $offeredFaculty = \App::call('App\Http\Controllers\Grader\CoursesOfferedController@retrieveFaculty');
            $programs =  DB::select("select * from Programs where PROG_STATUS = 'E'");
            $query = 'select * from fin_fee_category';
            $feeCategories = DB::select($query);
            $Scholarship_Type = "select * from Scholarship_Type where scholarship_status = 'E' and SCHOLARSHIP_ID in (1,2)";
            $recScholarship_Type = DB::select($Scholarship_Type);
             $query = 'select * from semesters order by SEMESTER_ID';
            $semesters = DB::select($query);
            return view('StudentFinancial.generateFeeChallan',compact('sessions','schools','departments','facultyCourses','offeredFaculty','feeCategories','programs', 'recScholarship_Type','semesters'));

        }
        /**
         * FIN_FEE_STRUCTURE.FFS_STUDENT_TYPE never holds the "New without Registration" wording used
         * in the UI, the procedures expect "New Admission FALL/SPRING/SUMMER" for those.
         */
        private function resolveFfsStudentType($studentType){
            return str_replace('New without Registration', 'New Admission', (string) $studentType);
        }
        /**
         * Replicates the FIN_FEE_STRUCTURE lookup done inside FIN_NEW_STD_BLK_CHLN_GEN and
         * FIN_EXISTING_STD_BLK_CHLN_GEN. Both procedures skip a student silently when no matching fee
         * structure exists (by design, a bulk run spans programs where only some structures are
         * defined), so this tells the operator up front which students would produce no challan.
         */
        private function resolveFeeStructureAvailability(Request $request, array $studentIdArr){
            $studentIdArr = array_values(array_unique(array_filter(array_map('trim', $studentIdArr), 'strlen')));
            $isNewWithoutRegistration = strpos((string) $request->student_type, 'New without Registration') !== false;
            $ffsStudentType = $this->resolveFfsStudentType($request->student_type);

            $candidates = [];
            $seen = [];
            if(!empty($studentIdArr)){
                if($isNewWithoutRegistration){
                    $rows = DB::table('ADMISSION_APPLY_INFO as aai')
                            ->leftJoin('ADMISSION_PERSONAL_INFO as api', 'api.API_ID', 'aai.AAI_API_ID')
                            ->select(
                                    'aai.AAI_ID',
                                    'aai.AAI_STATUS',
                                    'aai.AAI_SDPO_SCHOOL_ID',
                                    'aai.AAI_SDPO_DEPT_ID',
                                    'aai.AAI_SDPO_PROG_ID',
                                    'aai.AAI_SDPO_SESS_ID',
                                    'aai.CMS_SAI_ENROLLMENT_NO',
                                    'api.API_NAME'
                            )
                            ->whereIn('aai.AAI_ID', $studentIdArr)
                            ->get();
                    foreach($rows as $row){
                        $seen[(string) $row->aai_id] = true;
                        $candidates[] = [
                            'id'             => $row->aai_id,
                            'name'           => $row->api_name,
                            'enrollment'     => empty($row->cms_sai_enrollment_no) ? 'Apply Id '.$row->aai_id : $row->cms_sai_enrollment_no,
                            'school_id'      => $row->aai_sdpo_school_id,
                            'dept_id'        => $row->aai_sdpo_dept_id,
                            'prog_id'        => $row->aai_sdpo_prog_id,
                            // the procedure matches the intake range on AAI_SDPO_SESS_ID
                            'intake_sess_id' => $row->aai_sdpo_sess_id,
                            // its opening SELECT INTO also demands these two, and raises NO_DATA_FOUND
                            // (silently, no challan) when they do not hold
                            'reason'         => ($row->aai_status == 'Selected' && (string) $row->aai_sdpo_sess_id === (string) $request->session)
                                                    ? null : 'student_record_mismatch',
                            'row'            => $row,
                        ];
                    }
                }else{
                    $rows = DB::table('STUDENTS_APPLY_INFO as sai')
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
                            ->whereIn('sai.SAI_ID', $studentIdArr)
                            ->get();
                    foreach($rows as $row){
                        $seen[(string) $row->sai_id] = true;
                        $candidates[] = [
                            'id'             => $row->sai_id,
                            'name'           => $row->spi_name,
                            'enrollment'     => $row->sai_enrollment_no,
                            'school_id'      => $row->sai_sdpo_school_id,
                            'dept_id'        => $row->sai_sdpo_dept_id,
                            'prog_id'        => $row->sai_sdpo_prog_id,
                            // the procedure matches the intake range on the student's OWN admission
                            // session (SAI_SDPO_SESS_ID), not on the session selected on the form
                            'intake_sess_id' => $row->sai_sdpo_sess_id,
                            // its opening SELECT INTO also demands this, and raises NO_DATA_FOUND
                            // (silently, no challan) when it does not hold
                            'reason'         => ($row->sai_student_status == $request->student_status) ? null : 'student_record_mismatch',
                            'row'            => $row,
                        ];
                    }
                }
            }
            // ids the student table did not return at all
            foreach($studentIdArr as $id){
                if(!isset($seen[(string) $id])){
                    $candidates[] = [
                        'id'             => $id,
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

            // probe FIN_FEE_STRUCTURE once per distinct school|dept|prog|intake-session key
            $probeCache = [];
            $eligibleRows = [];
            $eligibleIds = [];
            $missing = [];
            foreach($candidates as $candidate){
                if($candidate['reason'] === null){
                    $key = $candidate['school_id'].'|'.$candidate['dept_id'].'|'.$candidate['prog_id'].'|'.$candidate['intake_sess_id'];
                    if(!array_key_exists($key, $probeCache)){
                        $probeCache[$key] = $this->feeStructureExists(
                            $request,
                            $ffsStudentType,
                            $candidate['school_id'],
                            $candidate['dept_id'],
                            $candidate['prog_id'],
                            $candidate['intake_sess_id']
                        );
                    }
                    if($probeCache[$key]){
                        $eligibleRows[] = $candidate['row'];
                        $eligibleIds[] = $candidate['id'];
                        continue;
                    }
                    $candidate['reason'] = 'no_fee_structure';
                }
                $missing[] = $candidate;
            }

            return [
                'is_new_without_registration' => $isNewWithoutRegistration,
                'student_type_used' => $ffsStudentType,
                'total'          => count($candidates),
                'eligible_count' => count($eligibleIds),
                'missing_count'  => count($missing),
                'eligible_ids'   => $eligibleIds,
                'eligible_rows'  => $eligibleRows,
                'missing_groups' => $this->groupMissingFeeStructureStudents($missing),
            ];
        }
        /**
         * Intake ranges on FIN_FEE_STRUCTURE were only introduced from the session AFTER this one, so
         * they are meaningless for older sessions (mirrors StudentRecordController).
         */
        private const INTAKE_RANGE_FROM_SESSION = 167;
        /**
         * The FIN_FEE_STRUCTURE predicate shared by both bulk challan procedures.
         */
        private function feeStructureExists(Request $request, $ffsStudentType, $schoolId, $deptId, $progId, $intakeSessId){
            // same normalisation addChallan applies when binding p_roadmap / p_semester, so the check
            // and the procedure call can never disagree
            $roadmap  = empty($request->roadmap_session) ? null : $request->roadmap_session;
            $semester = empty($request->semester_sel) ? null : $request->semester_sel;

            $sql = "SELECT COUNT(*) AS CNT FROM BNUCMS.FIN_FEE_STRUCTURE
                    WHERE FFS_SCHOOL_ID = ?
                      AND FFS_DEPT_ID = ?
                      AND FFS_PROG_ID = ?
                      AND FFS_SESS_ID = ?
                      AND FFS_FEE_TYPE = ?
                      AND FFS_STUDENT_TYPE = ?
                      AND FFS_STATUS = 'E'";
            $bindings = [$schoolId, $deptId, $progId, $request->session, $request->fee_type, $ffsStudentType];

            // For sessions <= INTAKE_RANGE_FROM_SESSION the intake range is not compared at all (older
            // fee rows carry a range that would otherwise wrongly exclude them). For newer sessions the
            // student's intake session must fall inside the row's range, and rows with no range still match.
            if((int) $request->session > self::INTAKE_RANGE_FROM_SESSION){
                $sql .= " AND ((FFS_INTAKE_FROM_SESS <= ? AND FFS_INTAKE_TO_SESS >= ?) OR FFS_INTAKE_FROM_SESS IS NULL)";
                $bindings[] = $intakeSessId;
                $bindings[] = $intakeSessId;
            }

            // mirrors the procedures' "(V_X IS NULL AND col IS NULL) OR (V_X IS NOT NULL AND col = V_X)"
            if($roadmap === null){
                $sql .= " AND FFS_ROADMAP_SESSION IS NULL";
            }else{
                $sql .= " AND FFS_ROADMAP_SESSION = ?";
                $bindings[] = $roadmap;
            }
            if($semester === null){
                $sql .= " AND FFS_SEMESTER_NO IS NULL";
            }else{
                $sql .= " AND FFS_SEMESTER_NO = ?";
                $bindings[] = $semester;
            }
            // dd($bindings);
            $res = DB::select($sql, $bindings);
            // dd($res);
            return !empty($res) && $res[0]->cnt > 0;
        }
        /**
         * Collapses the skipped students into School / Department / Program / intake-session groups so
         * a bulk run of hundreds stays readable in the confirmation dialog.
         */
        private function groupMissingFeeStructureStudents(array $missing){
            if(empty($missing)){
                return [];
            }
            $schoolIds = []; $deptIds = []; $progIds = []; $sessIds = [];
            foreach($missing as $row){
                if(!empty($row['school_id']))      { $schoolIds[] = $row['school_id']; }
                if(!empty($row['dept_id']))        { $deptIds[]   = $row['dept_id']; }
                if(!empty($row['prog_id']))        { $progIds[]   = $row['prog_id']; }
                if(!empty($row['intake_sess_id'])) { $sessIds[]   = $row['intake_sess_id']; }
            }
            $schoolNames = $this->lookupNames('SCHOOLS', 'school_id', 'school_name', $schoolIds);
            $deptNames   = $this->lookupNames('DEPARTMENTS', 'dept_id', 'dept_name', $deptIds);
            $progNames   = $this->lookupNames('PROGRAMS', 'prog_id', 'prog_name', $progIds);
            $sessNames   = $this->lookupNames('ACADEMIC_SESSIONS', 'sess_id', 'sess_name', $sessIds);

            $groups = [];
            foreach($missing as $row){
                $key = $row['reason'].'|'.$row['school_id'].'|'.$row['dept_id'].'|'.$row['prog_id'].'|'.$row['intake_sess_id'];
                if(!isset($groups[$key])){
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
        private function lookupNames($table, $idColumn, $nameColumn, array $ids){
            $ids = array_values(array_unique($ids));
            if(empty($ids)){
                return [];
            }
            $map = [];
            foreach(DB::table($table)->select($idColumn, $nameColumn)->whereIn($idColumn, $ids)->get() as $row){
                $map[(string) $row->{$idColumn}] = $row->{$nameColumn};
            }
            return $map;
        }
        /**
         * Pre-check called by the Generate button before addChallan, so the operator can be told which
         * of the selected students have no fee structure and decide whether to proceed anyway.
         */
        public function checkFeeStructure(Request $request){
            $validator = Validator::make($request->all(),[
                'session' => 'required',
                'student_type' => 'required',
                'fee_type' => 'required',
                'student_status' => 'required',
                'students' => 'required',
            ]);
            if($validator->fails()) {
                return Response::json(array(
                    'success' => false,
                    'errors' => $validator->getMessageBag()->toArray()
                ), 400);
            }
            $check = $this->resolveFeeStructureAvailability($request, explode(',', $request->students));
            unset($check['eligible_rows']);// raw student rows are only useful server side
            return response()->json(['success' => true] + $check);
        }
        public function addChallan(Request $request){
            // dd($request->fee_type);
            // if($request->fee_type == 'Credit_hours'){
            //     return $this->add_creditHr_challans($request);
            // }
            // if($request->fee_type == 'Credit_hours_Summer_Course'){
            //     return $this->add_summerCredithr_challans($request);
            // }
            $validator = Validator::make($request->all(),[
                'session' => 'required',
                'school' => 'required',
                'department' => 'required',
                'degree_level' => 'required',
                'program' => 'required',
                'student_type' => 'required',
                'fee_type' => 'required',
                'student_status' => 'required',
                'due_date' => 'required',
                'valid_date' => 'required',
                'visible_date' => 'required',
                // 'second_installment_due_date' => 'required',
                // 'third_installment_due_date' => 'required',
                // 'second_installment_valid_date' => 'required',
                // 'third_installment_valid_date' => 'required',
            ]);
            if($validator->fails()) {
                return Response::json(array(
                    'success' => false,
                    'errors' => $validator->getMessageBag()->toArray()
                ), 400);
                // return response()->json(['success' => true, 'count'=> $generatdChallan_count]);
                // return response()->json(['success' => false, 'messege'=> 'Something went wrong. (Duplicate entries not allowed)', 'errors' => $e->getMessage()], 500);
            }
            // $second_installment_due_date = strtotime($request->get('second_installment_due_date'));
            // $third_installment_due_date = strtotime($request->get('third_installment_due_date'));
            // $second_installment_valid_date = strtotime($request->get('second_installment_valid_date'));
            // $third_installment_valid_date = strtotime($request->get('third_installment_valid_date'));
            // if ($second_installment_due_date > $third_installment_due_date) {
            //     return Response::json([
            //         'success' => false,
            //         'errors' => [
            //             'due_date' => ['The 2nd installment date must be before 3rd installment date.'],
            //         ],
            //     ], 400);
            // }
            ////////////////

            ///////////////////
            // try {
                // DB::beginTransaction();

            $dueDate = new DateTime($request->due_date);
            $dueDate = $dueDate->format('Y-m-d');

            $validDate = new DateTime($request->valid_date);
            $validDate = $validDate->format('Y-m-d');

            $visibleDate = new DateTime($request->visible_date);
            $visibleDate = $visibleDate->format('Y-m-d');
            $generatdChallan_count = 0;

            // $sqlSessionCode = "select * from academic_sessions where sess_id = $request->session";
            // $ressqlSessionCode = DB::select($sqlSessionCode);
            // $sess_name = explode(' ', $ressqlSessionCode[0]->sess_name)[0];
            $sessName='';

            // Both procedures skip a student silently when no fee structure matches, so resolve that
            // up front and let the operator decide before anything is generated.
            $feeStructureCheck = $this->resolveFeeStructureAvailability($request, explode(',', $request->students));
            if($feeStructureCheck['missing_count'] > 0 && !$request->boolean('confirm_proceed')){
                $payload = $feeStructureCheck;
                unset($payload['eligible_rows']);
                return response()->json(['success' => false, 'needs_confirmation' => true] + $payload, 409);
            }
            if($feeStructureCheck['eligible_count'] == 0){
                return response()->json(['success' => false, 'messege' => 'No fee structure is defined for any of the selected students.'], 400);
            }

            if($request->student_type == 'New without Registration FALL' || $request->student_type == 'New without Registration SPRING' || $request->student_type == 'New without Registration SUMMER'){//new admission without registration case
                $sessName = $this->resolveFfsStudentType($request->student_type);

                // students whose fee structure is defined, already fetched by the check above
                $studentData = $feeStructureCheck['eligible_rows'];
                foreach($studentData as $val){
                  //  $enrollement = $val->sai_enrollment_no;
                    $aai_id = $val->aai_id;
                    $schoolId = $val->aai_sdpo_school_id;
                    $deptId =  $val->aai_sdpo_dept_id;
                    $progId =  $val->aai_sdpo_prog_id;
                    // dd($schoolId, $deptId,$progId );
                    $procedurenotification = 'FIN_NEW_STD_BLK_CHLN_GEN';

                    $bindings_notification = [
                        'p_schol_id' => $schoolId,
                        'p_dept_id' => $deptId,
                        'p_prog_id' =>$progId,
                        'p_session' => $request->session,
                        'p_due_date' => $dueDate,
                        'p_valid_date' => $validDate,
                        'p_ffs_student_type' => $sessName, //($request->student_type=='New without Registration')?'New Admission FALL':$request->student_type,
                        'p_ffs_fee_type' => $request->fee_type,
                        'p_aai_id' => $aai_id,
                        'p_visible_date' => $visibleDate,
                        'p_installmentOption' => 'YES',
                        'p_entry_username'=> auth()->user()->username,
                        'p_roadmap' => empty($request->roadmap_session) ? null : $request->roadmap_session,
                        'p_semester' => empty($request->semester_sel) ? null : $request->semester_sel



                    ];
                    // dd($bindings_notification);
                    // Execute stored procedure
                    $result_notification = DB::executeProcedure($procedurenotification, $bindings_notification);




                    if($result_notification){
                        $generatdChallan_count++;
                    }
                }
            }else{// existing student and new admission with registration case

                $sessName  = $request->student_type;

                // students whose fee structure is defined, already fetched by the check above
                $studentData = $feeStructureCheck['eligible_rows'];

                foreach($studentData as $val){
                    //dd($val);
                    $enrollement = $val->sai_enrollment_no;
                    $schoolId = $val->sai_sdpo_school_id;
                    $deptId = $val->sai_sdpo_dept_id;
                    $progId = $val->sai_sdpo_prog_id;

                    $procedurenotification = 'FIN_EXISTING_STD_BLK_CHLN_GEN';
                    $bindings_notification = [
                        'v_sai_student_status' => $request->student_status,//
                        'schol_id' => $schoolId,
                        'dept_id' => $deptId,
                        'prog_id' =>$progId,
                        'session' => $request->session,
                        'v_sai_enrollment_no' => $enrollement,//
                        'v_ffs_fee_type' => $request->fee_type,
                        'v_ffs_student_type' =>  $sessName,//
                        'ddue_date' => $dueDate,
                        'vvalid_date' => $validDate,
                        'p_visible_date' => $visibleDate,
                        'p_installmentOption' => 'YES',
                        'p_roadmap' => empty($request->roadmap_session) ? null : $request->roadmap_session,
                        'p_semester' => empty($request->semester_sel) ? null : $request->semester_sel,
                        'p_entry_username'=> auth()->user()->username,

                    ];
                    // dd($bindings_notification);
                    // Execute Surplus stored procedure
                    $result_notification = DB::executeProcedure($procedurenotification, $bindings_notification);


                    $aai_id = DB::table('admission_apply_info')
                    ->where('CMS_SAI_ENROLLMENT_NO', $enrollement)
                    ->select('AAI_ID')
                    ->first();
                    $aai_id = $aai_id->aai_id;
                    //surplus procedure
                    $surplus_procedurenotification = 'ADJUST_FEE_BY_ENROLLMENT';
                    $surplus_bindings_notification = [
                        'p_enrollment_no' => $aai_id,//
                        'p_sess_id' => $request->session,
                        'p_user_name' => auth()->user()->username

                    ];
                    // dd($surplus_bindings_notification);
                    // Execute stored procedure
                    $surplus_result_notification = DB::executeProcedure($surplus_procedurenotification, $surplus_bindings_notification);

                    // $adjustmentCheck = DB::table('student_challan')
                    //                     ->select('id', 'CHECK_FEE_INSTALLMENTS', DB::raw("TO_CHAR(created_at, 'DD-MON-YY') as created_at"))
                    //                     ->where('sc_enrollment_no', $enrollement)
                    //                     ->where('sc_session_id', $request->session)
                    //                     ->get();
                    // $today = Carbon::today()->format('d-M-y');

                    // if(sizeof($adjustmentCheck) == 1 ){
                    //     if($adjustmentCheck[0]->check_fee_installments == 0 && $adjustmentCheck[0]->created_at == $today){
                    //         ////////ADJUSTMENT LOGIC////////////////
                    //         $sql = DB::select("SELECT SUM(ffa_surplus_amount) as v_surplus_amount,ffa_enrollment_no,ffa_id FROM fin_fee_adjustment
                    //         WHERE FF_AMNT_STATUS='E'   and  FFA_STATUS='E'  and lower(ffa_enrollment_no)=lower('$enrollement')
                    //         GROUP BY FFA_ENROLLMENT_NO,ffa_id");
                    //         //dd($sql);
                    //         if($sql){
                    //             $row=$sql[0];
                    //             // echo $sql;exit;

                    //             $surplus_amount = $row->v_surplus_amount;
                    //             //$enrollment_no = $row->ffa_enrollment_no;

                    //             $ffaStatus='';
                    //             $ffAmntStatus='';
                    //             $surplus_amount1='';

                    //             $record = DB::table('student_challan')
                    //             ->where('sc_enrollment_no', $enrollement)
                    //             ->where('sc_session_id', $request->session)
                    //             ->select('id', 'sc_enrollment_no', 'invoiceamount')
                    //             ->first();
                    //             $flag='pending';

                    //             if($record && $surplus_amount <= $record->invoiceamount){
                    //                 $flag='Paid';
                    //                 $ffaStatus='D';
                    //                 $ffAmntStatus='D';
                    //                 $invoiceamount1=$record->invoiceamount-$surplus_amount;
                    //                 $surplus_amount1=0;

                    //                 $sel1="update fin_fee_adjustment set ffa_sc_challan_no=$record->id,ffa_surplus_amount= $surplus_amount1 ,ffa_amount_status='$flag',ffa_status='$ffaStatus',ff_amnt_status='$ffAmntStatus'  where ffa_id = $row->ffa_id";
                    //                 $res=DB::insert( $sel1);
                    //                 $sel1="update student_challan set sc_surplus_amount=$surplus_amount,invoiceamount=$invoiceamount1 where sc_enrollment_no = '$enrollement' and sc_session_id=$request->session";
                    //                 $res=DB::insert( $sel1);

                    //             }else if($record && $surplus_amount > $record->invoiceamount){
                    //                 $ffaStatus='E';
                    //                 $ffAmntStatus='E';
                    //                 $surplus_amount1=$surplus_amount-$record->invoiceamount;
                    //                 if($surplus_amount1>0){
                    //                     $surplus_amount1=$surplus_amount-$surplus_amount1;

                    //                 }
                    //                 $invoiceamount1=0;

                    //                 $sel1="update fin_fee_adjustment set ffa_sc_challan_no=$record->id,ffa_surplus_amount= $surplus_amount-$record->invoiceamount,ffa_amount_status='$flag',ffa_status='$ffaStatus',ff_amnt_status='$ffAmntStatus'  where ffa_id = $row->ffa_id";
                    //                 $res=DB::insert( $sel1);
                    //                     $sel1="update student_challan set sc_surplus_amount=$surplus_amount1,invoiceamount=$invoiceamount1 where sc_enrollment_no = '$enrollement' and sc_session_id=$request->session";
                    //                 $res=DB::insert( $sel1);

                    //             }
                    //         }
                    //     ////////ADJUSTMENT LOGIC END////////////////
                    //     }
                    // }
                    if($result_notification ){
                        $generatdChallan_count++;
                    }


                }
                // Prepare procedure parameters
            }
            $programs_id = $request->program;
            $strings = explode(',', trim($programs_id, '[]'));
            $school_depart_prog = [];
            foreach ($strings as $str) {
                // Remove leading and trailing double quotes
                $str = trim($str, '"');
                // Split the string based on the "~~" separator
                $parts = explode('~~', $str);
                // Add the parts to the result array
                $school_depart_prog[] = $parts;
            }
            // dd($school_depart_prog);
            // Insert data into FIN_BLK_CHALLAN_GEN table
            $userid = auth()->user()->id;
            DB::beginTransaction();
            try {
                foreach($school_depart_prog as $val){
                    $schoolId = $val[0];
                    $deptId = $val[1];
                    $progId = $val[2];
                    DB::table('FIN_BLK_CHALLAN_GEN')->insert([
                        'FBC_SESSION_ID' => $request->session,
                        'FBC_SCHOOL_ID' => $schoolId,
                        'FBC_DEPARTMENT_ID' => $deptId,
                        'FBC_PROGRAM_ID' => $progId,
                        'FBC_DUE_DATE' => $dueDate,
                        'FBC_VALID_DATE' => $validDate,
                        'FBC_VISIBLE_DATE' => $visibleDate,

                        'FBC_DEGREE_LEVEL' => $request->degree_level,
                        'FBC_STUDENT_TYPE' => $sessName,
                        'FBC_FEE_TYPE' => $request->fee_type,
                        'FBC_STUDENT_STATUS' => $request->student_status,

                        // 'fbc_2nd_installment_due_date' => $request->second_installment_due_date,
                        // 'fbc_3rd_installment_due_date' => $request->third_installment_due_date,
                        // 'fbc_2nd_installment_valid_date' => $request->second_installment_valid_date,
                        // 'fbc_3rd_installment_valid_date' => $request->third_installment_valid_date,

                        'FBC_ENTRY_USER_ID' => $userid,
                        'FBC_STATUS' => 'E'
                    ]);
                }
                // Commit the transaction if all updates succeed
                DB::commit();
                // return response()->json(['success' => true, 'message' => 'Records Updated successfully'], 200);
            } catch (\Exception $e) {
                // Rollback the transaction if any exception occurs
                DB::rollBack();
                return response()->json(['success' => false, 'messege'=> 'Something went wrong. (Duplicate entries not allowed)', 'errors' => $e->getMessage()], 500);
            }
            return response()->json(['success' => true, 'count'=> $generatdChallan_count]);
            // } catch (\Exception $e) {
            //     DB::rollBack();
            //     return response()->json(['success' => false]);
            // }

        }
        // public function add_creditHr_challans($request){
        //     // dd($request->all());
        //     $validator = Validator::make($request->all(),[
        //         'session' => 'required',
        //         'student_type' => 'required',
        //         'fee_type' => 'required',
        //         'student_status' => 'required',
        //         'due_date' => 'required',
        //         'valid_date' => 'required',
        //         'visible_date' => 'required',
        //         // 'degree_level'   => 'required|not_in:null,Postgraduate,Doctorate',
        //         'degree_level'   => 'required|in:Undergraduate',
        //     ]);
        //     if($validator->fails()) {
        //         return Response::json(array(
        //             'success' => false,
        //             'errors' => $validator->getMessageBag()->toArray()
        //         ), 400);
        //     }
        //     $dueDate = new DateTime($request->due_date);
        //     $dueDate = $dueDate->format('Y-m-d');

        //     $validDate = new DateTime($request->valid_date);
        //     $validDate = $validDate->format('Y-m-d');

        //     $visibleDate = new DateTime($request->visible_date);
        //     $visibleDate = $visibleDate->format('Y-m-d');
        //     $degreeLevel = $request->degree_level;
        //     $generatdChallan_count=0;
        //     // try {
        //         if($request->student_type != 'New without Registration FALL' && $request->student_type != 'New without Registration SPRING' && $request->student_type != 'New without Registration SUMMER'){//only for exisitng  and new admission with registration
        //             $studentType  = $request->student_type;
        //             $studentIdArr = explode(',',$request->students);
        //             $enrollmentCreditData = json_decode($request->enrollment_credit_data, true);
        //             // $studentData = DB::table('STUDENTS_APPLY_INFO')
        //             //                 ->select(
        //             //                         'SAI_ID',
        //             //                         'SAI_ID',
        //             //                         'SAI_ENROLLMENT_NO',
        //             //                         // 'SAI_SDPO_SESS_ID',
        //             //                         'SAI_SDPO_SCHOOL_ID',
        //             //                         'SAI_SDPO_DEPT_ID',
        //             //                         'SAI_SDPO_PROG_ID',
        //             //                         // 'SAI_STUDENT_STATUS'
        //             //                 )
        //             //                 ->whereIn('SAI_ID', $studentIdArr)
        //             //                 ->get();
        //             // dd($studentData);
        //             foreach($enrollmentCreditData as $val){
        //                 //dd($val);
        //                 $enrollement = $val['enrollment'];
        //                 $student_crdithr = $val['credit_hour'];
        //                 // echo $enrollement;
        //                 // $sql = "SELECT SUM(SC_PC_COURSE_CR) AS total_credits
        //                 //         FROM student_courses
        //                 //         JOIN courses_offered co ON sc_cms_course_co_id = co.co_id
        //                 //         WHERE sc_sai_enrollment_no = '$enrollement'
        //                 //             AND SC_ENROLLMENT_STATUS = 1
        //                 //             AND SC_STATUS in('A','Withdrawal','Block')
        //                 //             and CO_STATUS='E'
        //                 //             AND SC_CO_SESS_ID = $request->session";
        //                 // $student_crdithr = DB::select($sql);
        //                 if($request->creditHourRange == 'lessthan'){//lest than 9.5 case
        //                     $result_notification = $this->challans_credit_lessthan($enrollement, $student_crdithr, $request, $dueDate, $validDate, $visibleDate);

        //                 }elseif ($request->creditHourRange == 'greaterthan'){//greater than 18 case
        //                    $result_notification =  $this->challans_credit_greaterthan($enrollement, $student_crdithr, $request, $dueDate, $validDate, $visibleDate);

        //                 }else{//between 9.5 and 18 case
        //                    $result_notification = $this->challans_credit_inbetween($enrollement, $student_crdithr, $request, $dueDate, $validDate, $visibleDate);

        //                 }
        //                 if($result_notification){
        //                     $generatdChallan_count++;
        //                 }
        //             }
        //             return response()->json(['success' => true, 'count'=> $generatdChallan_count]);
        //         }else{
        //             return response()->json(['success' => false, 'messege'=> 'Something went wrong. (credit hour wise challan can not be generated for students without registration)']);
        //         }
        //     // } catch (\Exception $e) {
        //     //     return response()->json(['success' => false, 'messege'=> 'Something went wrong.', 'errors' => $e->getMessage()], 500);
        //     // }

        // }
        public function challans_credit_lessthan($enrollement, $student_crdithr, $request, $dueDate, $validDate, $visibleDate){
            $procedurenotification = 'fin_credit_hr_wise_challan';
                    $bindings_notification = [
                    'p_enrollment_no' => $enrollement,
                    'p_session_id' => $request->session,
                    'p_std_status' => $request->student_status, //(Active or Expected-Graduate)
                    'p_ffs_student_type' =>$request->student_type, //(Existing/New Admission FALL etc..)
                    'p_due_date' => $dueDate,
                    'p_valid_date' => $validDate,
                    'p_visible_date' => $visibleDate,
                    'p_Installment_allowed' => 'NO',
                    'P_CR_HR' =>$student_crdithr,
                    'P_USER_NAME' => auth()->user()->username
                ];
                // dd($bindings_notification);
            return $result_notification = DB::executeProcedure($procedurenotification, $bindings_notification);
        }
        public function challans_credit_greaterthan($enrollement, $student_crdithr, $request, $dueDate, $validDate, $visibleDate){
            $procedurenotification = 'fin_credit_hr_wise_challan';
                    $bindings_notification = [
                    'p_enrollment_no' => $enrollement,
                    'p_session_id' => $request->session,
                    'p_std_status' => $request->student_status, //(Active or Expected-Graduate)
                    'p_ffs_student_type' =>$request->student_type, //(Existing/New Admission FALL etc..)
                    'p_due_date' => $dueDate,
                    'p_valid_date' => $validDate,
                    'p_visible_date' => $visibleDate,
                    'p_Installment_allowed' => 'NO',
                    'P_CR_HR' =>$student_crdithr,
                    'P_USER_NAME' => auth()->user()->username
                ];
                //  dd($bindings_notification);
            return $result_notification = DB::executeProcedure($procedurenotification, $bindings_notification);
        }
        public function challans_credit_inbetween($enrollement, $student_crdithr, $request, $dueDate, $validDate, $visibleDate){
            $procedurenotification = 'FIN_PRO_repeat_credit';
                $bindings_notification = [
                    'p_enrollment_no' => $enrollement,
                    'p_session_id' => $request->session,
                    'p_std_status' => $request->student_status, //(Active or Expected-Graduate)
                    'p_ffs_student_type' => $request->student_type, //(Existing/New Admission FALL etc..)
                    'p_due_date' => $dueDate,
                    'p_valid_date' => $validDate,
                    'p_visible_date' => $visibleDate,
                    'p_Installment_allowed' => 'NO',
                    'p_degree_level' => $request->degree_level,
                    'p_repeat_credit' => $student_crdithr,
                    'p_user_name' => auth()->user()->username
                ];
            return $result_notification = DB::executeProcedure($procedurenotification, $bindings_notification);
        }
        // public function add_summerCredithr_challans($request){
        //     // dd($request->all());
        //     $validator = Validator::make($request->all(),[
        //         'session' => 'required',
        //         'student_type' => 'required',
        //         'student_status' => 'required',
        //         'due_date' => 'required',
        //         'valid_date' => 'required',
        //         'visible_date' => 'required',
        //     ]);
        //     if($validator->fails()) {
        //         return Response::json(array(
        //             'success' => false,
        //             'errors' => $validator->getMessageBag()->toArray()
        //         ), 400);
        //     }
        //     $dueDate = new DateTime($request->due_date);
        //     $dueDate = $dueDate->format('Y-m-d');

        //     $validDate = new DateTime($request->valid_date);
        //     $validDate = $validDate->format('Y-m-d');

        //     $visibleDate = new DateTime($request->visible_date);
        //     $visibleDate = $visibleDate->format('Y-m-d');
        //     $generatdChallan_count=0;
        //     // try {
        //         if($request->student_type == 'Existing'){//only for exisitng
        //             $studentType  = $request->student_type;
        //             $studentIdArr = explode(',',$request->students);
        //             $studentData = DB::table('STUDENTS_APPLY_INFO')
        //                             ->select(
        //                                     'SAI_ID',
        //                                     'SAI_ID',
        //                                     'SAI_ENROLLMENT_NO',
        //                                     // 'SAI_SDPO_SESS_ID',
        //                                     'SAI_SDPO_SCHOOL_ID',
        //                                     'SAI_SDPO_DEPT_ID',
        //                                     'SAI_SDPO_PROG_ID',
        //                                     // 'SAI_STUDENT_STATUS'
        //                             )
        //                             ->whereIn('SAI_ID', $studentIdArr)
        //                             ->get();
        //             foreach($studentData as $val){
        //                 $enrollement = $val->sai_enrollment_no;
        //                 $procedurenotification = 'FIN_PRO_SUMMER_CREDIT_HOUR';
        //                 $bindings_notification = [
        //                     'p_enrollment_no' =>  $enrollement,
        //                     'p_session_id' => $request->session,
        //                     'p_std_status' => $request->student_status,
        //                     'p_ffs_student_type' => $studentType,
        //                     'p_due_date' => $dueDate,
        //                     'p_valid_date' =>  $validDate,
        //                     'p_visible_date' => $visibleDate,
        //                     'p_Installment_allowed' => 'NO',
        //                     'p_degree_level' => $request->degree_level,
        //                     'p_user_name' => auth()->user()->username
        //                 ];
        //                 // dd($bindings_notification);
        //                 // Execute stored procedure
        //                 $result_notification = DB::executeProcedure($procedurenotification, $bindings_notification);
        //                 if($result_notification){
        //                     $generatdChallan_count++;
        //                 }
        //             }
        //             return response()->json(['success' => true, 'count'=> $generatdChallan_count]);
        //         }else{
        //             return response()->json(['success' => false, 'messege'=> 'Something went wrong. (Student type should be existing, credit hour for summer can not be generated for new students)']);
        //         }
        //     // } catch (\Exception $e) {
        //     //     return response()->json(['success' => false, 'messege'=> 'Something went wrong.', 'errors' => $e->getMessage()], 500);
        //     // }

        // }
        public function get_challan_list(Request $request){
            // dd($request->all());
            // dd($request->feeType);
            //dd(json_decode($request->school_id), json_decode($request->department_id), json_decode($request->program_id), $request->program_id);
            // dd();
            // if(!is_array(json_decode($request->department))){
            //     dd('single dep', );
            // }else{
            //     dd('all dep');
            // }
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
                        ->where('sc.INVOICE_STATUS', 'E');
                        // Common filters apply the same way whether or not Unpaid is checked - the
                        // Unpaid checkbox only ADDS the UNPAID status filter on top (see below).
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
                            $data =  $data->where('sc.SC_CHALLAN_TYPE', $request->feeType);
                        }
                        if($request->unpaidCheckbox == 1){//Unpaid checked: keep all the above filters and additionally restrict to UNPAID challans
                            $data =  $data->where('sc.SC_CHALLAN_STATUS', 'UNPAID');
                        }

                        $data =  $data->get();
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
            $department= $request->department_id;
            $subqueryOptionalWhere ="";
            $sqlSessionCode = "select * from academic_sessions where sess_id = $request->session_id";
            $ressqlSessionCode = DB::select($sqlSessionCode);
            $sess_name = explode(' ', $ressqlSessionCode[0]->sess_name)[0];
            if(!empty($request->studentStatus)){
                $subqueryOptionalWhere = " and ssr_semester_status = '".$request->studentStatus."'";//this is for the subquery to concatenate the where clause of 'active' or 'expected graduate'
            }else{
                $subqueryOptionalWhere = " and ssr_semester_status IN ('Expected-Graduate', 'Active')";
            }
            if(!empty($request->feeType)){
                    $challanType_conditoin =  "and sc_challan_type = '".$request->feeType ."'";
                }else{
                    $challanType_conditoin =  "";
                }
            // dd($subqueryOptionalWhere);
            // dd("sai.sai_enrollment_no = (select sai_enrollment_nofrom students_semester_recordwhere students_semester_record.sai_enrollment_no = sai.sai_enrollment_noand sess_id =  ".$request->session_id."".$subqueryOptionalWhere.")");
            if(strpos($request->studentType, 'New without Registration') !== false){

                $string = $request->studentType;
                $search = 'New without Registration';
                $replace = 'New Admission';
                $student_status = str_replace($search, $replace, $string);///changes "New without Registration FALL/SPRING/SUMMER" to "New Admission FALL/SPRING/SUMMER"
                // dd($student_status);
                $challanCounts = DB::table('student_challan')
                        ->select('SC_APP_ID', DB::raw('COUNT(*) as challan_count'))
                        ->where('SC_STUDENT_STATUS', $student_status)
                        ->where('SC_SESSION_ID', $request->session_id)
                        ->whereNull('SC_ENROLLMENT_NO');
                if(!empty($request->feeType)){
                    $challanCounts->where('SC_CHALLAN_TYPE', $request->feeType);
                }
                $challanCounts->groupBy('SC_APP_ID');

                $data = DB::table('ADMISSION_APPLY_INFO as aai')
                        ->select(
                                'aai.AAI_ID',
                                'aai.AAI_STATUS',
                                'aai.cms_sai_enrollment_no',
                                'api.API_NAME',

                                'acd.SESS_NAME',
                                'school.SCHOOL_NAME',
                                'dept.DEPT_NAME',
                                'prog.PROG_NAME',
                                DB::raw('NVL(sc_counts.challan_count, 0) as challan_count')
                                )
                                ->leftjoin('ADMISSION_PERSONAL_INFO as api', 'api.API_ID', 'aai.AAI_API_ID')
                                ->leftjoin('ACADEMIC_SESSIONS as acd', 'acd.SESS_ID', 'aai.AAI_SDPO_SESS_ID')
                                ->leftjoin('SCHOOLS as school', 'school.SCHOOL_ID', 'aai.AAI_SDPO_SCHOOL_ID')
                                ->leftjoin('DEPARTMENTS as dept', 'dept.DEPT_ID', 'aai.AAI_SDPO_DEPT_ID')
                                ->leftjoin('PROGRAMS as prog', 'prog.PROG_ID', 'aai.AAI_SDPO_PROG_ID')
                                ->leftJoinSub($challanCounts, 'sc_counts', function($join) {
                                    $join->on('sc_counts.SC_APP_ID', '=', 'aai.AAI_ID');
                                })


                                ->whereNull('aai.cms_sai_enrollment_no')
                                ->where('aai.aai_status', 'Selected')
                                ->where('aai.aai_pf_status', 'Fee Verified')
                                ->where('aai.AAI_QUALIFICATION_STATUS', 'Accepted');

                                // if(!empty($request->scholrshipType)){
                                //     $data = $data->leftjoin('students_scholarships_details as ssd', 'ssd.SSD_APP_ID', 'aai.aai_id')
                                //                     ->where('ssd.ssd_type','=', $request->scholrshipType)
                                //                     ->where('ssd.SSD_SESS_ID','=', $request->session_id);
                                // }
                                if(!empty($request->scholrshipType)){
                                 $data = $data->leftJoin('students_scholarships_details as ssd', 'ssd.SSD_APP_ID', '=', 'aai.AAI_ID')
                                        ->leftJoin('FIN_STUDENTS_SCHOLARSHIPS_HOSTEL_DETAILS as sshd', 'sshd.SSHD_APP_ID', '=', 'aai.AAI_ID')
                                        ->whereRaw(
                                            '((ssd.SSD_TYPE = ? AND ssd.SSD_SESS_ID = ?) OR (sshd.SSHD_TYPE = ? AND sshd.SSHD_SESS_ID = ?))',
                                            [
                                                $request->scholrshipType,
                                                $request->session_id,
                                                $request->scholrshipType,
                                                $request->session_id,
                                            ]
                                        );

                                }
                                if(!empty($request->session_id)){
                                    $data = $data->where('aai.AAI_SDPO_SESS_ID','=', $request->session_id);
                                }
                                if(!empty($request->session_id)){
                                    $data = $data->where('aai.AAI_SDPO_SESS_ID','=', $request->session_id);
                                }
                                if(!empty($request->school_id && !is_array(json_decode($request->school_id)))){
                                    $data = $data->where('aai.AAI_SDPO_SCHOOL_ID', $request->school_id);
                                }
                                if(!empty($request->department_id && !is_array(json_decode($request->department_id)))){
                                    $data = $data->where('aai.AAI_SDPO_DEPT_ID', $request->department_id);
                                }
                                if(!empty($request->degree_level)){
                                    $data = $data->where('prog.PROG_DEGREE_LEVEL', $request->degree_level);
                                }
                                if(!empty($request->program_id) && !is_array(json_decode($request->program_id))){//condition will applied only when single program is selected if all program is slected then no need to apply condition
                                    //    dd('program condtion applied');
                                    $programs_id = $request->program_id;
                                    // dd($programs_id);
                                    $array_of_sch_dep_prog = explode('~~', $programs_id);
                                    // dd($array_of_sch_dep_prog[2]);
                                $data =  $data->where('aai.AAI_SDPO_PROG_ID', $array_of_sch_dep_prog[2]);
                                }
// dd($data->toSql());
                $data = $data->get();
                // dd($data);
            }else{
                //   dd('existing and with');
                $challanCounts = DB::table('student_challan')
                        ->select('SC_ENROLLMENT_NO', DB::raw('COUNT(*) as challan_count'))
                        ->where('SC_SESSION_ID', $request->session_id);
                if(!empty($request->feeType)){
                    $challanCounts->where('SC_CHALLAN_TYPE', $request->feeType);
                }
                $challanCounts->groupBy('SC_ENROLLMENT_NO');

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
                                DB::raw('NVL(sc_counts.challan_count, 0) as challan_count')
                                )
                                //->leftjoin('student_challan as sc', 'sc.SC_ENROLLMENT_NO', 'sai.SAI_ENROLLMENT_NO')
                                ->leftjoin('STUDENTS_PERSONAL_INFO as spi', 'spi.SPI_ID', 'sai.SAI_SPI_ID')
                                ->leftJoin('ADMISSION_APPLY_INFO as aai', function($join) {
                                    $join->on('aai.AAI_API_ID', '=', 'sai.SAI_API_ID')
                                        ->whereRaw('LOWER(aai.CMS_SAI_ENROLLMENT_NO) = LOWER(sai.SAI_ENROLLMENT_NO)');
                                })
                                ->leftjoin('ACADEMIC_SESSIONS as acd', 'acd.SESS_ID', 'sai.SAI_SDPO_SESS_ID')
                                ->leftjoin('SCHOOLS as school', 'school.SCHOOL_ID', 'sai.SAI_SDPO_SCHOOL_ID')
                                ->leftjoin('DEPARTMENTS as dept', 'dept.DEPT_ID', 'sai.SAI_SDPO_DEPT_ID')
                                ->leftjoin('PROGRAMS as prog', 'prog.PROG_ID', 'sai.SAI_SDPO_PROG_ID')
                                ->leftJoinSub($challanCounts, 'sc_counts', function($join) {
                                    $join->on('sc_counts.SC_ENROLLMENT_NO', '=', 'sai.SAI_ENROLLMENT_NO');
                                })

                                ->whereIn('sai.SAI_STUDENT_STATUS', ['Expected-Graduate', 'Active'])
                                ->where('sai.SAI_ENROLLMENT_NO', 'NOT LIKE', '%SUM%')
                                ->whereNotIn('sai.SAI_ENROLLMENT_NO', function($query) use ($request) {
                                    $query->select('SAI_ENROLLMENT_NO')
                                        ->from('STUDENTS_SEMESTER_RECORD')
                                        ->where('SESS_ID', $request->session_id)
                                        ->where('SSR_SEMESTER_STATUS', 'Freeze');
                                });
                                                                // ->where('sai.SAI_STUDENT_STATUS', 'Active')
                                // ->whereNull('sc.SC_ENROLLMENT_NO');
                                if(!empty($request->scholrshipType)){
                                 $data = $data->leftJoin('students_scholarships_details as ssd', 'ssd.SSD_APP_ID', '=', 'aai.AAI_ID')
                                        ->leftJoin('FIN_STUDENTS_SCHOLARSHIPS_HOSTEL_DETAILS as sshd', 'sshd.SSHD_APP_ID', '=', 'aai.AAI_ID')
                                        ->whereRaw(
                                            '((ssd.SSD_TYPE = ? AND ssd.SSD_SESS_ID = ?) OR (sshd.SSHD_TYPE = ? AND sshd.SSHD_SESS_ID = ?))',
                                            [
                                                $request->scholrshipType,
                                                $request->session_id,
                                                $request->scholrshipType,
                                                $request->session_id,
                                            ]
                                        );

                                }
                                if(!empty($request->session_id)){

                                    if(!empty($request->studentType)){
                                        if($request->studentType == 'Existing' && $sess_name == 'Spring'){//show students of all sessions before the selected session But skiping the last FALL session
                                            $data = $data->where('sai.SAI_SDPO_SESS_ID','<', $request->session_id-1);

                                        }elseif($request->studentType == 'New Admission FALL' && $sess_name == 'Spring'){//show students of session before the selected session
                                            $data = $data->where('sai.SAI_SDPO_SESS_ID','=', $request->session_id-1);

                                        }elseif($request->studentType == 'New Admission SPRING' && $sess_name == 'Spring'){//show students of session selected only
                                            $data = $data->where('sai.SAI_SDPO_SESS_ID','=', $request->session_id);

                                        }elseif($request->studentType == 'Existing' && $sess_name == 'Summer'){//show students of all sessions before the selected session
                                            $data = $data->where('sai.SAI_SDPO_SESS_ID','<', $request->session_id);

                                        }elseif($request->studentType == 'New Admission SUMMER' && $sess_name == 'Summer'){//show students of session selected only
                                                $data = $data->where('sai.SAI_SDPO_SESS_ID','=', $request->session_id);

                                        }elseif($request->studentType == 'Existing' && $sess_name == 'Fall'){//show students of all sessions before the selected session
                                                $data = $data->where('sai.SAI_SDPO_SESS_ID','<', $request->session_id);

                                        }elseif($request->studentType == 'New Admission FALL' && $sess_name == 'Fall'){//show students of session selected only
                                                $data = $data->where('sai.SAI_SDPO_SESS_ID','=', $request->session_id);

                                        }else{//Null case
                                            $data = $data->whereNull('sai.SAI_SDPO_SESS_ID');
                                        }
                                    }

                                }
                                if(!empty($request->school_id && !is_array(json_decode($request->school_id)))){
                                    $data = $data->where('sai.SAI_SDPO_SCHOOL_ID', $request->school_id);
                                }
                                if(!empty($request->department_id && !is_array(json_decode($request->department_id)))){
                                    $data = $data->where('sai.SAI_SDPO_DEPT_ID', $request->department_id);
                                }
                                if(!empty($request->degree_level)){
                                    $data = $data->where('prog.PROG_DEGREE_LEVEL', $request->degree_level);
                                }
                                if(!empty($request->program_id) && !is_array(json_decode($request->program_id))){//condition will applied only when single program is selected if all program is slected then no need to apply condition
                                    //    dd('program condtion applied');
                                    $programs_id = $request->program_id;
                                    // dd($programs_id);
                                    $array_of_sch_dep_prog = explode('~~', $programs_id);
                                    // dd($array_of_sch_dep_prog[2]);
                                $data =  $data->where('sai.SAI_SDPO_PROG_ID', $array_of_sch_dep_prog[2]);
                                }
                                if(!empty($request->studentStatus)){
                                    $data = $data->where('sai.SAI_STUDENT_STATUS', $request->studentStatus);
                                }
                $data = $data->get();
                // dd($data);
            }

                            //  dd($data);
            return DataTables::of($data)->toJson();
        }
        public function getStudents_creditHr_range($request){
            // dd($request->all());
            $students_credithr = $request->students_credithr;
            $sqlSessionCode = "select * from academic_sessions where sess_id = $request->session_id";
            $ressqlSessionCode = DB::select($sqlSessionCode);
            $sess_name = explode(' ', $ressqlSessionCode[0]->sess_name)[0];
            if($students_credithr == 'lessthan'){//less than 9.5 credithours
              $sql="SELECT
                    SUM(SC_PC_COURSE_CR) AS total_credits,
                    sc_sai_enrollment_no
                    FROM student_courses
                    JOIN courses_offered co ON sc_cms_course_co_id = co.co_id
                    WHERE  SC_ENROLLMENT_STATUS = 1
                    AND SC_STATUS in('A', 'Withdrawal', 'Block')
                    AND SC_CO_SESS_ID = $request->session_id
                    and CO_STATUS='E'
                    and co_course_id not in (10433)
                    group by sc_sai_enrollment_no
                    having SUM(SC_PC_COURSE_CR) < 9.5";
            }elseif($students_credithr == 'greaterthan'){//greater than 18 credithours
                $sql= "SELECT
                    SUM(SC_PC_COURSE_CR) AS total_credits,
                    sc_sai_enrollment_no
                    FROM student_courses
                    JOIN courses_offered co ON sc_cms_course_co_id = co.co_id
                    WHERE  SC_ENROLLMENT_STATUS = 1
                    AND SC_STATUS in('A', 'Withdrawal', 'Block')
                    AND SC_CO_SESS_ID = $request->session_id
                    and CO_STATUS='E'
                    and co_course_id not in (10433)
                    group by sc_sai_enrollment_no
                    having SUM(SC_PC_COURSE_CR) > 18";
            }elseif($students_credithr == 'inbetween'){//9.5 - 18
                $sql= "SELECT    sc_sai_enrollment_no,
                        SUM(sc_pc_course_cr) AS total_credits
                        FROM     student_courses e
                        JOIN     courses_offered co     ON e.sc_cms_course_co_id = co.co_id
                        WHERE
                            e.sc_enrollment_status = 1
                            AND e.SC_STATUS IN ('A', 'Block', 'Withdrawal')
                            AND co.CO_STATUS = 'E'
                            and co_course_id not in (10433)
                            AND e.SC_CO_SESS_ID=$request->session_id
                            -- Only include students with grade history in matching courses
                            AND EXISTS (
                                SELECT 1
                                FROM GRADE_HISTORY gh
                                WHERE gh.gh_sai_enrollment_no = e.sc_sai_enrollment_no
                                AND gh.gh_co_course_id = e.SC_COURSE_ID
                                AND gh.gh_co_sess_id >= 163
                                and gh.gh_co_sess_id!=$request->session_id
                            )
                            -- Exclude students whose total credit hours are < 9.5 or > 18 in session 165
                            AND e.sc_sai_enrollment_no NOT IN (
                                SELECT sc_sai_enrollment_no
                                FROM (
                                    SELECT
                                        sc_sai_enrollment_no,
                                        SUM(sc_pc_course_cr) AS total_credits
                                    FROM student_courses
                                    JOIN courses_offered co ON sc_cms_course_co_id = co.co_id
                                    WHERE SC_ENROLLMENT_STATUS = 1
                                    AND SC_STATUS IN ('A', 'Withdrawal', 'Block')
                                    AND SC_CO_SESS_ID = $request->session_id
                                    AND CO_STATUS = 'E'
                                    and co_course_id not in (10433)
                                    GROUP BY sc_sai_enrollment_no
                                ) credit_summary
                                WHERE total_credits < 9.5 OR total_credits > 18)
                                group by sc_sai_enrollment_no";
            }

            // dd($sql);
            $student_crdithr_data = DB::select($sql);
            // dd($student_crdithr_data);
            $enrollmentNumbers = collect($student_crdithr_data)->pluck('sc_sai_enrollment_no')->toArray();
            $enrollmentString = "('".implode("','",$enrollmentNumbers)."')";//formatting to use in WHere IN ()
            // dd($enrollmentString);
            //////Filter the enrollment nos where the fee structures is defined and the challan not issues
            // $feeStrcture_enrollment_sql = "WITH student_info AS (
            //                                     SELECT DISTINCT sai.sai_enrollment_no,
            //                                         sai.sai_sdpo_school_id,
            //                                         sai.sai_sdpo_dept_id,
            //                                         sai.sai_sdpo_prog_id
            //                                     FROM students_apply_info sai
            //                                     WHERE sai.sai_enrollment_no in $enrollmentString
            //                                     ),
            //                                 fee_structure AS (
            //                                     SELECT DISTINCT ffs.FFS_SCHOOL_ID,
            //                                                     ffs.FFS_DEPT_ID,
            //                                                     ffs.FFS_PROG_ID
            //                                     FROM FIN_FEE_STRUCTURE ffs
            //                                     JOIN student_info si
            //                                         ON si.sai_sdpo_school_id = ffs.FFS_SCHOOL_ID
            //                                         AND si.sai_sdpo_dept_id = ffs.FFS_DEPT_ID
            //                                         AND si.sai_sdpo_prog_id = ffs.FFS_PROG_ID
            //                                         AND ffs.FFS_FEE_TYPE ='Credit_hours'
            //                                     WHERE ffs.FFS_SESS_ID = $request->session_id
            //                                 ),
            //                                 challan_info AS (
            //                                     SELECT DISTINCT sc.SC_ENROLLMENT_NO
            //                                     FROM student_challan sc
            //                                     JOIN student_info si
            //                                         ON sc.SC_ENROLLMENT_NO = si.sai_enrollment_no
            //                                     WHERE sc.SC_ENROLLED_CR_HR IS NOT NULL
            //                                 )
            //                                 SELECT *
            //                                 FROM (
            //                                     SELECT si.sai_enrollment_no,
            //                                         CASE
            //                                             WHEN fs.FFS_PROG_ID IS NOT NULL AND ci.SC_ENROLLMENT_NO IS NULL
            //                                             THEN 'Fee Structure Defined, Challan Not Issued'
            //                                             ELSE 'Fee Structure Not Defined'
            //                                         END AS status
            //                                     FROM student_info si
            //                                     LEFT JOIN fee_structure fs
            //                                         ON si.sai_sdpo_school_id = fs.FFS_SCHOOL_ID
            //                                         AND si.sai_sdpo_dept_id = fs.FFS_DEPT_ID
            //                                         AND si.sai_sdpo_prog_id = fs.FFS_PROG_ID
            //                                     LEFT JOIN challan_info ci
            //                                         ON si.sai_enrollment_no = ci.SC_ENROLLMENT_NO
            //                                 )
            //                                 WHERE status = 'Fee Structure Defined, Challan Not Issued'";
            // $feeStrcture_enrollment = DB::select($feeStrcture_enrollment_sql);//extracting enrollmentno whose fee structures are defined
            // //  dd($feeStrcture_enrollment);
            //  $enrollmentNumbers = collect($feeStrcture_enrollment)->pluck('sai_enrollment_no')->toArray();
            //  dd($enrollmentNumbers);
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
                            ->whereIn('sai.SAI_STUDENT_STATUS', ['Expected-Graduate', 'Active'])
                            ->whereIn('sai.SAI_ENROLLMENT_NO', $enrollmentNumbers)
                            ->whereRaw("acd.SESS_NAME NOT LIKE ?", ['Summer%']);
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

                                    if(!empty($request->studentType)){
                                        if($request->studentType == 'Existing' && $sess_name == 'Spring'){//show students of all sessions before the selected session But skiping the last FALL session
                                            $data = $data->where('sai.SAI_SDPO_SESS_ID','<', $request->session_id-1);

                                        }elseif($request->studentType == 'New Admission FALL' && $sess_name == 'Spring'){//show students of session before the selected session
                                            $data = $data->where('sai.SAI_SDPO_SESS_ID','=', $request->session_id-1);

                                        }elseif($request->studentType == 'New Admission SPRING' && $sess_name == 'Spring'){//show students of session selected only
                                            $data = $data->where('sai.SAI_SDPO_SESS_ID','=', $request->session_id);

                                        }elseif($request->studentType == 'Existing' && $sess_name == 'Summer'){//show students of all sessions before the selected session
                                            $data = $data->where('sai.SAI_SDPO_SESS_ID','<', $request->session_id);

                                        }elseif($request->studentType == 'New Admission SUMMER' && $sess_name == 'Summer'){//show students of session selected only
                                                $data = $data->where('sai.SAI_SDPO_SESS_ID','=', $request->session_id);

                                        }elseif($request->studentType == 'Existing' && $sess_name == 'Fall'){//show students of all sessions before the selected session
                                                $data = $data->where('sai.SAI_SDPO_SESS_ID','<', $request->session_id);

                                        }elseif($request->studentType == 'New Admission FALL' && $sess_name == 'Fall'){//show students of session selected only
                                                $data = $data->where('sai.SAI_SDPO_SESS_ID','=', $request->session_id);

                                        }else{//Null case
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
                                $data = $data->where('sai.SAI_STUDENT_STATUS', $request->studentStatus);
                            }
                $data = $data->orderby('aai.aai_id')->get();
                $credits = collect($student_crdithr_data);
                // dd($data);
                // Map over students collection and add total_credits
                $updatedStudents = $data->map(function ($student) use ($credits) {
                    // Find matching credit record
                    $credit = $credits->firstWhere('sc_sai_enrollment_no', $student->sai_enrollment_no);

                    // Add total_credits if found, otherwise set to null
                    $student->total_credits = $credit->total_credits ?? '0';

                    return $student;
                });
                // dd($updatedStudents);
            return DataTables::of($updatedStudents)->toJson();
        }
        public function getStudents_summerCredithr($request){
            // dd($request->all());
            $sql=" SELECT  distinct sc_sai_enrollment_no
                        FROM student_courses
                        left join STUDENTS_APPLY_INFO on sc_sai_enrollment_no=SAI_ENROLLMENT_NO
                        WHERE  SC_ENROLLMENT_STATUS = 1
                        AND SC_STATUS in('A', 'Withdrawal', 'Block')
                        AND SC_CO_SESS_ID =  $request->session_id
                        AND sc_sai_enrollment_no not like '%SUM%'
                        and sc_course_id not in (1533,6123,6825,6824,9542)
                        and STUDENTS_APPLY_INFO.SAI_STUDENT_STATUS in ('Active','Expected-Graduate')
                    ";
            $student_crdithr_data = DB::select($sql);
            // dd($student_crdithr_data);
            $enrollmentNumbers = collect($student_crdithr_data)->pluck('sc_sai_enrollment_no')->toArray();
            $enrollmentString = "('".implode("','",$enrollmentNumbers)."')";//formatting to use in WHere IN ()
            // dd($enrollmentString);
            //////Filter the enrollment nos where the fee structures is defined and the challan not issues
            $feeStrcture_enrollment_sql = "WITH student_info AS (
                                                SELECT DISTINCT sai.sai_enrollment_no,
                                                    sai.sai_sdpo_school_id,
                                                    sai.sai_sdpo_dept_id,
                                                    sai.sai_sdpo_prog_id
                                                FROM students_apply_info sai
                                                WHERE sai.sai_enrollment_no in $enrollmentString
                                                ),
                                            fee_structure AS (
                                                SELECT DISTINCT ffs.FFS_SCHOOL_ID,
                                                                ffs.FFS_DEPT_ID,
                                                                ffs.FFS_PROG_ID
                                                FROM FIN_FEE_STRUCTURE ffs
                                                JOIN student_info si
                                                    ON si.sai_sdpo_school_id = ffs.FFS_SCHOOL_ID
                                                    AND si.sai_sdpo_dept_id = ffs.FFS_DEPT_ID
                                                    AND si.sai_sdpo_prog_id = ffs.FFS_PROG_ID
                                                    AND ffs.FFS_FEE_TYPE ='Short/Summer_Course'
                                                    AND ffs.ffs_credhr_amt > 0
                                                WHERE ffs.FFS_SESS_ID = $request->session_id
                                            ),
                                            challan_info AS (
                                                SELECT DISTINCT sc.SC_ENROLLMENT_NO
                                                FROM student_challan sc
                                                JOIN student_info si
                                                    ON sc.SC_ENROLLMENT_NO = si.sai_enrollment_no
                                                WHERE sc.SC_ENROLLED_CR_HR IS NOT NULL
                                            )
                                            SELECT *
                                            FROM (
                                                SELECT si.sai_enrollment_no,
                                                    CASE
                                                        WHEN fs.FFS_PROG_ID IS NOT NULL AND ci.SC_ENROLLMENT_NO IS NULL
                                                        THEN 'Fee Structure Defined, Challan Not Issued'
                                                        ELSE 'Fee Structure Not Defined'
                                                    END AS status
                                                FROM student_info si
                                                LEFT JOIN fee_structure fs
                                                    ON si.sai_sdpo_school_id = fs.FFS_SCHOOL_ID
                                                    AND si.sai_sdpo_dept_id = fs.FFS_DEPT_ID
                                                    AND si.sai_sdpo_prog_id = fs.FFS_PROG_ID
                                                LEFT JOIN challan_info ci
                                                    ON si.sai_enrollment_no = ci.SC_ENROLLMENT_NO
                                            )
                                            WHERE status = 'Fee Structure Defined, Challan Not Issued'";
                $feeStrcture_enrollment = DB::select($feeStrcture_enrollment_sql);//extracting enrollmentno whose fee structures are defined
            //  dd($feeStrcture_enrollment);
                $enrollmentNumbers = collect($feeStrcture_enrollment)->pluck('sai_enrollment_no')->toArray();
            //  dd($enrollmentNumbers);
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
                            ->whereIn('sai.SAI_STUDENT_STATUS', ['Expected-Graduate', 'Active'])
                            ->whereIn('sai.SAI_ENROLLMENT_NO', $enrollmentNumbers);
                            // ->whereRaw("acd.SESS_NAME NOT LIKE ?", ['Summer%']);
                            // if(!empty($request->studentType)){
                            //     if($request->studentType == 'Existing'){//existing students case
                            //         $data = $data->where('sai.SAI_SDPO_SESS_ID','=', $request->session_id);
                            //     }else{
                            //          $data = $data->whereNull('sai.SAI_SDPO_SESS_ID');//show no data if other than exisitng selected
                            //     }
                            // }
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
                $credits = collect($student_crdithr_data);
                // dd($data);
                // Map over students collection and add total_credits
                $updatedStudents = $data->map(function ($student) use ($credits) {
                    // Find matching credit record
                    $credit = $credits->firstWhere('sc_sai_enrollment_no', $student->sai_enrollment_no);

                    // Add total_credits if found, otherwise set to null
                    $student->total_credits = $credit->total_credits ?? '0';

                    return $student;
                });
                // dd($updatedStudents);
            return DataTables::of($updatedStudents)->toJson();
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
        private static function getSchool_dep_prog_inSeparatArrays($inputArray){
            $allSchools = [];
            $allDep = [];
            $allProg = [];

            foreach ($inputArray as $item) {
                if (!in_array($item[0], $allSchools)) {
                    $allSchools[] = $item[0];
                }
                if (!in_array($item[1], $allDep)) {
                    $allDep[] = $item[1];
                }
                if (!in_array($item[2], $allProg)) {
                    $allProg[] = $item[2];
                }
            }

            return [$allSchools, $allDep, $allProg];
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

////////////Scholarship Challan Generation
    public function handleMeritScholarshipChallan(Request $request){

        $sessions = \App::call('App\Http\Controllers\Grader\AcademicSessionController@retrieve');
        $schools =  Schools::all();
        $departments = array(); // $this->school->fetch();
        $facultyCourses = \App::call('App\Http\Controllers\Grader\FacultyCoursesController@retrieve');
        $offeredFaculty = \App::call('App\Http\Controllers\Grader\CoursesOfferedController@retrieveFaculty');
        $programs =  DB::select("select * from Programs where PROG_STATUS = 'E'");
        $query = 'select * from fin_fee_category';
        $feeCategories = DB::select($query);
        return view('StudentFinancial.meritBaseScholarship',compact('sessions','schools','departments','facultyCourses','offeredFaculty','feeCategories','programs'));

    }
    public function submitMeritScholarshipChallan(Request $request){
        // dd($request->all());
        $validator = Validator::make($request->all(),[
                'session' => 'required',
                // 'school' => 'required',
                // 'department' => 'required',
                'due_date' => 'required',
                // 'program' => 'required',
                'valid_date' => 'required',
                'student_type' => 'required'
            ]);
            if($validator->fails()) {
                return Response::json(array(
                    'success' => false,
                    'errors' => $validator->getMessageBag()->toArray()
                ), 400);

            }
        try {
            // Parse students data safely
            $students = is_array($request->students)
                ? $request->students
                : json_decode($request->students, true);

            if (!is_array($students)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid students data provided'
                ], 400);
            }
            // Collect all students (no duplicate filtering in this version)
            $uniqueStudents = [];
            foreach ($students as $student) {
                $uniqueStudents[] = $student;
            }

            // Format dates
            $dueDate = (new DateTime($request->due_date))->format('Y-m-d');
            $validDate = (new DateTime($request->valid_date))->format('Y-m-d');

            // Initialize counters
            $successCount = 0;
            $errors = [];

            // dd($uniqueStudents);
            foreach ($uniqueStudents as $student) {
                $studentType = $request->student_type;
                if(strpos($request->student_type, 'New without Registration') !== false){//NEW without registration case
                    $string = $request->student_type;
                    $search = 'New without Registration';
                    $replace = 'New Admission';
                    $studentType = str_replace($search, $replace, $string);

                }
                try {
                    $bindings = [
                        'p_app_id' => $student,
                        'p_session' => $request->session,
                        'p_arrear' => 0,
                        'p_ffs_student_type' => $studentType,
                        'p_due_date' => $dueDate,
                        'p_valid_date' => $validDate,
                        'p_entry_user'=>auth()->user()->username
                    ];
                    // dd($bindings);
                        $result = DB::executeProcedure('UPDATE_CHALLAN_FARHAN1', $bindings);
                        // dd($result);
                        if ($result) {
                            $successCount++;
                    }
                } catch (\Exception $e) {
                    $errorMsg = "Error processing app_id: {$student} - " . $e->getMessage();
                    $errors[] = $errorMsg;
                }

            }

            return response()->json([
                'success' => true,
                'message' => "$successCount challan(s) updated successfully",
                'processed_count' => count($uniqueStudents),
                'success_count' => $successCount,
                'errors' => $errors
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to process scholarship challans',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function get_student_meritScholarship_approved(Request $request) {
        // dd($request->all());
        if(strpos($request->studentType, 'New without Registration') !== false){//NEW without registration case
            $whereClause='';
            if (!empty($request->school_id) && !is_array(json_decode($request->school_id)) ) {
                $whereClause .= " AND aAI_SDPO_SCHOOL_ID = " . $request->school_id;
            }
            if (!empty($request->department_id) && !is_array(json_decode($request->department_id)) ) {
                $whereClause .= " AND aAI_SDPO_DEPT_ID = " . $request->department_id;
            }
            if (!empty($request->program_id) && !is_array(json_decode($request->program_id)) ) {
                $programs_id = $request->program_id;
                // dd($programs_id);
                $array_of_sch_dep_prog = explode('~~', $programs_id);

                $whereClause .= " AND aAI_SDPO_PROG_ID = " . $array_of_sch_dep_prog[2];
            }

            $map = [
                    'before' => 'SCH_PRO_NEW',
                    'after'  => 'AFTER24_SCH_POLICY',
                ];
            $ssd_project_condition = $map[$request->session_option] ?? '';
            // dd($ssd_project_condition );
               $data = "SELECT
                    api.api_name as student_name,
                    api.api_fathername as father_name,
                    api.api_GUARDIAN_CNIC as guardian_cnic,
                    aai.aAI_SDPO_SCHOOL_ID as school_id,
                    aai.aAI_SDPO_DEPT_ID as department_id,
                    aai.aAI_SDPO_PROG_ID as program_id,
                    aai.aAI_SDPO_SESS_ID as session_id,
                    'Active' as student_status,
                    --aai.aAI_STUDENT_STATUS,
                    aai.cms_SAI_ENROLLMENT_NO as enrollment_no,
                    aai.AAI_ID as aAI_ID,
                    -- aai.aAI_ID,
                    PROG_NAME as program_name,
                    sc.SC_SCH_APPROVAL_CHALLAN ,
                    --   cgpa(SAI_ENROLLMENT_NO) AS cal_cgpa,
                null as cal_cgpa,
                    SSD.SSD_PERCENTAGE,
                    CASE
                        WHEN sc.SC_SCH_APPROVAL_CHALLAN IS NULL THEN 'No Challan'
                        WHEN LOWER(sc.SC_SCH_APPROVAL_CHALLAN) LIKE '%approved%' THEN 'Approved'
                        WHEN LOWER(sc.SC_SCH_APPROVAL_CHALLAN) LIKE '%pending%' THEN 'Not Approved'
                        ELSE 'Other'
                    END AS CHALLAN_APPROVAL_STATUS,
                    CASE
                        WHEN SSD.UPDATE_DATETIME > sc.CREATED_AT THEN 'editable'
                        ELSE 'not_editable'
                    END AS EDITABLE_FLAG
                FROM admission_APPLY_INFO aai
                LEFT JOIN admission_PERSONAL_INFO api
                    ON aai.aai_api_id = api.api_id
                LEFT JOIN PROGRAMS prog
                    ON aai.aAI_SDPO_PROG_ID = prog.PROG_ID
                LEFT JOIN (
                    SELECT *
                    FROM (
                        SELECT SSD.*, ROW_NUMBER() OVER (PARTITION BY SSD_APP_ID ORDER BY SSD_ID DESC) rn
                        FROM STUDENTS_SCHOLARSHIPS_DETAILS SSD
                        WHERE SSD_PROJECT = '$ssd_project_condition'
                        AND STATUS = 'E'
                    )
                    WHERE rn = 1
                ) SSD
                    ON SSD.SSD_APP_ID = aai.AAI_ID
                LEFT JOIN (
                    SELECT *
                    FROM (
                        SELECT SC.*, ROW_NUMBER() OVER (PARTITION BY SC_APP_ID ORDER BY ID DESC) rn
                        FROM STUDENT_CHALLAN SC
                    )
                    WHERE rn = 1
                ) sc
                    ON sc.SC_APP_ID = aai.AAI_ID
                WHERE aai.AAI_ID IN (
                     SELECT SSD_APP_ID
                        FROM STUDENTS_SCHOLARSHIPS_DETAILS
                        WHERE SSD_PROJECT = '$ssd_project_condition'
                        AND STATUS = 'E'
                        AND SSD_SESS_ID = $request->session_id
                        AND SSD_SAI_ENROLLMENT_NO is null
                )
                and aai_status='Selected'  $whereClause";
        }else{//EXISITNG students case
            $whereClause='';
            if (!empty($request->school_id) && !is_array(json_decode($request->school_id)) ) {
                $whereClause .= " AND SAI_SDPO_SCHOOL_ID = " . $request->school_id;
            }
            if (!empty($request->department_id) && !is_array(json_decode($request->department_id)) ) {
                $whereClause .= " AND SAI_SDPO_DEPT_ID = " . $request->department_id;
            }
            if (!empty($request->program_id) && !is_array(json_decode($request->program_id)) ) {
                $programs_id = $request->program_id;
                // dd($programs_id);
                $array_of_sch_dep_prog = explode('~~', $programs_id);

                $whereClause .= " AND SAI_SDPO_PROG_ID = " . $array_of_sch_dep_prog[2];
            }
            if (!empty($request->studentStatus) ) {
                $whereClause .= " AND SAI_STUDENT_STATUS = '" . $request->studentStatus."'";
            }
            else{
                $whereClause .= " AND SAI_STUDENT_STATUS IN ('Expected-Graduate', 'Active')";
            }
            $map = [
                    'before' => 'SCH_PRO_NEW',
                    'after'  => 'AFTER24_SCH_POLICY',
                ];
            $ssd_project_condition = $map[$request->session_option] ?? '';
            // dd($ssd_project_condition );
             $data = "
                    SELECT
                        spi_name as student_name,
                        spi_fathername as father_name,
                        SPI_GUARDIAN_CNIC as guardian_cnic,
                        SAI_SDPO_SCHOOL_ID as school_id,
                        SAI_SDPO_DEPT_ID as department_id,
                        SAI_SDPO_PROG_ID as program_id,
                        SAI_SDPO_SESS_ID as session_id,
                        SAI_STUDENT_STATUS as student_status,
                        SAI_ENROLLMENT_NO as enrollment_no,
                        AAI_ID as aAI_ID,
                        sai.SAI_ID as SAI_ID,
                        PROG_NAME as program_name,
                        sc.SC_SCH_APPROVAL_CHALLAN,
                        cgpa(SAI_ENROLLMENT_NO) AS cal_cgpa,
                        SSD.SSD_PERCENTAGE,
                        CASE
                            WHEN sc.SC_SCH_APPROVAL_CHALLAN IS NULL THEN 'No Challan'
                            WHEN LOWER(sc.SC_SCH_APPROVAL_CHALLAN) LIKE '%approved%' THEN 'Approved'
                            WHEN LOWER(sc.SC_SCH_APPROVAL_CHALLAN) LIKE '%pending%' THEN 'Not Approved'
                            ELSE 'Other'
                        END AS CHALLAN_APPROVAL_STATUS,
                        CASE
                            WHEN SSD.UPDATE_DATETIME > sc.CREATED_AT THEN 'editable'
                            ELSE 'not_editable'
                        END AS EDITABLE_FLAG
                    FROM STUDENTS_APPLY_INFO sai
                    LEFT JOIN STUDENTS_PERSONAL_INFO spi
                        ON sai.sai_spi_id = spi.spi_id
                    LEFT JOIN PROGRAMS prog
                        ON sai.SAI_SDPO_PROG_ID = prog.PROG_ID
                    LEFT JOIN ADMISSION_APPLY_INFO aai
                        ON aai.aai_api_id = sai.SAI_API_ID
                        AND LOWER(aai.CMS_SAI_ENROLLMENT_NO) = LOWER(sai.SAI_ENROLLMENT_NO)
                    LEFT JOIN (
                        SELECT *
                        FROM (
                            SELECT SSD.*, ROW_NUMBER() OVER (PARTITION BY SSD_APP_ID ORDER BY SSD_ID DESC) rn
                            FROM STUDENTS_SCHOLARSHIPS_DETAILS SSD
                            WHERE SSD_PROJECT = '$ssd_project_condition'
                            AND STATUS = 'E'
                        )
                        WHERE rn = 1
                    ) SSD
                        ON SSD.SSD_APP_ID = aai.AAI_ID
                    LEFT JOIN (
                        SELECT *
                        FROM (
                            SELECT SC.*, ROW_NUMBER() OVER (PARTITION BY SC_APP_ID ORDER BY ID DESC) rn
                            FROM STUDENT_CHALLAN SC
                        )
                        WHERE rn = 1
                    ) sc
                        ON sc.SC_APP_ID = aai.AAI_ID
                    WHERE aai.AAI_ID IN (
                        SELECT SSD_APP_ID
                        FROM STUDENTS_SCHOLARSHIPS_DETAILS
                        WHERE SSD_PROJECT = '$ssd_project_condition'
                        AND STATUS = 'E'
                        AND SSD_SESS_ID = $request->session_id
                        AND SSD_SAI_ENROLLMENT_NO is not null
                    )
                    $whereClause
                ";
        }
        // dd($data);
        $result = DB::select($data);
        // dd($result);
        return DataTables::of($result)->toJson();
    }

////////////Scholarship Challan APPROVAL
    public function scholarshipChallanApproval(){

        $sessions = \App::call('App\Http\Controllers\Grader\AcademicSessionController@retrieve');
        $schools =  Schools::all();
        $departments = array(); // $this->school->fetch();
        $facultyCourses = \App::call('App\Http\Controllers\Grader\FacultyCoursesController@retrieve');
        $offeredFaculty = \App::call('App\Http\Controllers\Grader\CoursesOfferedController@retrieveFaculty');
        $programs =  DB::select("select * from Programs where PROG_STATUS = 'E'");
        $query = 'select * from fin_fee_category';
        $feeCategories = DB::select($query);
        return view('StudentFinancial.scholarshipChallanApproval',compact('sessions','schools','departments','facultyCourses','offeredFaculty','feeCategories','programs'));


        // return view('StudentFinancial.scholarshipChallanApproval');
    }
    public function submit_scholarship_challan_approval(Request $request){
        // dd($request->all());
        try {
            $studentIds = json_decode($request->students);
            $appIds = json_decode($request->app_ids);

            if (empty($studentIds) || !is_array($studentIds)) {
                return response()->json(['success' => false, 'messege' => 'No students selected']);
            }

            $count = 0;
            // Process each student
            for ($i = 0; $i < count($studentIds); $i++) {
                $studentId = $studentIds[$i];
                // dd($studentId);
                // Update student challan status from "Pending" to "Approved"
                $updated = DB::table('student_challan')
                ->where('id', $studentId)
                ->update([
                    'sc_sch_approval_challan' => 'Approved'.'~'.auth()->user()->username,
                    'sc_visible_date' => date('Y-m-d')
                ]);


                if ($updated) {
                    $count++;
                }
            }

            return response()->json([
                'success' => true,
                'message' => $count . ' scholarship challans have been approved successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'messege' => 'Error processing request: ' . $e->getMessage()
            ]);
        }
    }
    public function get_challan_approval(Request $request){
        //  dd($request->all());
        $whereClause='';
        if (!empty($request->session_id)) {
            $whereClause .= " AND SC_SESSION_ID = " . $request->session_id;
        }
        if (!empty($request->school_id) && !is_array(json_decode($request->school_id)) ) {
            $whereClause .= " AND SC_SCHOOL_ID = " . $request->school_id;
        }
        if (!empty($request->department_id) && !is_array(json_decode($request->department_id)) ) {
            $whereClause .= " AND SC_DEPARTMENT_ID = " . $request->department_id;
        }
        if (!empty($request->program_id) && !is_array(json_decode($request->program_id)) ) {
            $programs_id = $request->program_id;
            // dd($programs_id);
            $array_of_sch_dep_prog = explode('~~', $programs_id);

            $whereClause .= " AND SC_PROGRAM_ID = " . $array_of_sch_dep_prog[2];
        }
        if ($request->studentType == 'Existing') {
            $whereClause .= " AND SC_ENROLLMENT_NO is not null";
        }
        else{
             $whereClause .= " AND SC_ENROLLMENT_NO is null";
        }
        if($request->session_option == 'after'){
            $ssd_proj = 'AFTER24_SCH_POLICY';
        }else{
            $ssd_proj = 'SCH_PRO_NEW';
        }
        // dd($whereClause);
        $data = "
            SELECT
                CHALLAN_NO,
                SC_ENROLLMENT_NO,
                SC_APP_ID,
                SC_CHALLAN_TYPE,
                PROG_NAME,
                SC_SCH_APPROVAL_CHALLAN
            FROM STUDENT_CHALLAN

            LEFT JOIN programs
                ON SC_PROGRAM_ID = PROG_ID
            WHERE
            SC_SCH_APPROVAL_CHALLAN = 'Pending'
            $whereClause
            AND sc_app_id IN (
                SELECT SSD_APP_ID
                FROM STUDENTS_SCHOLARSHIPS_DETAILS
                WHERE SSD_PROJECT = '$ssd_proj'
            )

        ";
        // echo $data;exit;
        // dd($data);
        $result = DB::select($data);
        // dd($result);
        return DataTables::of($result)->toJson();
    }
///////////Scholarship Entry  RO/////////////////
    public function handleMeritScholarshipEntry(Request $request){

        $sessions = \App::call('App\Http\Controllers\Grader\AcademicSessionController@retrieve');
        // dd($sessions);
       $sessions_summerExcluded = $sessions->reject(function ($sessionName, $sessionId) {
                            return stripos($sessionName, 'Summer') !== false;
                        });
       $cohorts = DB::select("
                        SELECT
                            cohort_id,
                            LISTAGG(sess_name, ', ')
                                WITHIN GROUP (ORDER BY sess_id DESC) AS sessions
                        FROM academic_cohorts
                        WHERE sess_id >= 162
                        GROUP BY cohort_id
                        ORDER BY cohort_id DESC
                    ");//after F2024 include F2024 which is 162 session id
        $schools =  Schools::all();
        $departments = array(); // $this->school->fetch();
        $facultyCourses = \App::call('App\Http\Controllers\Grader\FacultyCoursesController@retrieve');
        $offeredFaculty = \App::call('App\Http\Controllers\Grader\CoursesOfferedController@retrieveFaculty');
        $programs =  DB::select("select * from Programs where PROG_STATUS = 'E'");
        $query = 'select * from fin_fee_category';
        $feeCategories = DB::select($query);
        // dd($cohorts);
        return view('StudentFinancial.meritScholarshipEntry',compact('sessions','schools','departments','facultyCourses','offeredFaculty','feeCategories','programs', 'cohorts','sessions_summerExcluded'));

    }
    public function addMeritScholarshipStudents(Request $request){
        // dd($request->all());
        if($request->session_option == 'after'){
            return $this->addMeritScholarshipStudents_after2024($request);
        }
        // dd($request->all());
        $validator = Validator::make($request->all(),[
                'session' => 'required',
                'session_option' => 'required',
                'school' => 'required',
                'department' => 'required',
                // 'degree_level' => 'required',
                'program' => 'required',
                // 'student_status' => 'required'
            ]);
            if($validator->fails()) {
                return Response::json(array(
                    'success' => false,
                    'errors' => $validator->getMessageBag()->toArray()
                ), 400);
                // return response()->json(['success' => true, 'count'=> $generatdChallan_count]);
                // return response()->json(['success' => false, 'messege'=> 'Something went wrong. (Duplicate entries not allowed)', 'errors' => $e->getMessage()], 500);
            }
        try {
                $school = '';
                $dept = '';
                $stu_status = '';
                $prog = '';
                if(!empty($request->school) && !is_array(json_decode($request->school))){//condition will applied only when single school is selected if all school is slected then no need to apply condition//array means all school is selected and so we apply not array condtion to apply where clause;
                    $school =  "AND SAI_SDPO_SCHOOL_ID = $request->school";
                }
                if(!empty($request->department) && !is_array(json_decode($request->department))){//condition will applied only when single department is selected if all department is slected then no need to apply condition//array means all department is selected and so we apply not array condtion to apply where clause;
                    $dept =  "AND SAI_SDPO_DEPT_ID = $request->department";
                }
                if(!empty($request->student_status)){
                    $stu_status =  "AND SAI_STUDENT_STATUS = '$request->student_status'";
                }else{
                    $stu_status = "AND SAI_STUDENT_STATUS = 'Active'";//for now we get only active no matter what
                }
                if(!empty($request->program) && !is_array(json_decode($request->program))){//condition will applied only when single program is selected if all program is slected then no need to apply condition//array means all program is selected and so we apply not array condtion to apply where clause;
                //    dd('program condtion applied');
                    $programs_id = $request->program;
                    // dd($programs_id);
                    $array_of_sch_dep_prog = explode('~~', $programs_id);
                    // dd($array_of_sch_dep_prog[2]);
                    $prog = "AND SAI_SDPO_PROG_ID = $array_of_sch_dep_prog[2]";
                }
                // dd($school, $dept, $stu_status, $prog);
                $target_sess = (int)$request->session - 2;
                $qry = "SELECT
                                distinct AAI_ID
                                FROM
                                    grade_history
                                    LEFT JOIN students_apply_info ON sai_id = gh_sai_id
                                    LEFT JOIN students_personal_info ON students_apply_info.sai_spi_id = students_personal_info.spi_id
                                    LEFT JOIN ADMISSION_APPLY_INFO ON aai_api_id = STUDENTS_APPLY_INFO.SAI_API_ID --and STUDENTS_APPLY_INFO.sai_sdpo_prog_id=aai_sdpo_prog_id
                                                                    and lower(CMS_SAI_ENROLLMENT_NO) = lower(SAI_ENROLLMENT_NO)
                                    LEFT JOIN academic_sessions ON sess_id = grade_history.gh_co_sess_id
                                    LEFT JOIN schools ON school_id = students_apply_info.sai_sdpo_school_id
                                    LEFT JOIN departments ON dept_id = students_apply_info.sai_sdpo_dept_id
                                    LEFT JOIN programs ON prog_id = sai_sdpo_prog_id
                                    LEFT JOIN academic_years ON sess_ay_id = ay_id
                                    LEFT JOIN session_degree_program_offerin ON sdpo_sess_id = students_apply_info.sai_sdpo_sess_id
                                                                                AND sdpo_school_id = sai_sdpo_school_id
                                                                                AND sdpo_dept_id = sai_sdpo_dept_id
                                                                                AND sdpo_prog_id = sai_sdpo_prog_id
                                                                                AND sdpo_shift_id = sai_sdpo_shift_id
                                                                                AND sdpo_status = 'E'
                                WHERE
                                        gh_status = 'E'
                                    AND ( gh_exemption_status != 'Yes'
                                        OR gh_exemption_status IS NULL )
                                    AND gh_exam_fsubmit = 1
                                    AND gh_sai_enrollment_no IN (
                                                            SELECT DISTINCT   sai_enrollment_no
                                                            FROM          students_apply_info
                                                            WHERE         1 = 1
                                                                AND sai_enrollment_no IN (
                                                                    SELECT DISTINCT
                                                                        gh_sai_enrollment_no
                                                                    FROM
                                                                        grade_history
                                                                    WHERE
                                                                            1 = 1
                                                                        AND gh_status = 'E'
                                                                        AND ( gh_co_sess_id >= {$target_sess}
                                                                            AND gh_co_sess_id <= {$target_sess})
                                                                )
                                                        )
                                    AND gh_co_sess_id = {$target_sess}
                                    and sai_sdpo_school_id not in (6)
                                    and gh_sai_enrollment_no not like '%SUM%'
                                    AND SAI_STUDENT_STATUS='Active'
                                    and SAI_SDPO_SESS_ID < 162
                                    $school
                                    $dept
                                    $stu_status
                                    $prog";
                    // dd($students);

                      $students =DB::select($qry);
                // $students = $students->get();
                // dd($students);
                // dd(sizeof($students) );
                // Initialize counters
                $successCount = 0;
                $errors = [];
                if(sizeof($students) != 0){
                    foreach ($students as $student) {
                        try {
                            $bindings = [
                                'p_app_id' => $student->aai_id,
                                'p_session' => $request->session,
                                'p_entry_user' => auth()->user()->username
                            ];
                        // dd($bindings);
                            $result = DB::executeProcedure('FIN_PRO_MERIT_SCH', $bindings);
                            if ($result) {
                                $successCount++;
                            }
                        } catch (\Exception $e) {
                            $errorMsg = "Entries already exist";
                            $errors[] = $errorMsg;
                        }
                    }
                    if ($successCount == 0 && !empty($errors)) {
                        return response()->json([
                            'success' => false,
                            'message' => "Entries already exist",
                            'messege' => "Entries already exist"
                        ], 400);
                    }
                    return response()->json([
                        'success' => true,
                        'message' => "Students added successfully",
                        'success_count' => $successCount,
                        'errors' => $errors
                    ]);
                }else{
                     return Response::json([
                        'success' => false,
                        'errors' => [
                            ['No students found for the selected criteria. Please check the session, school, department, program, and student status.']
                        ]
                    ], 400);
                }

            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Entries already exist',
                    'messege' => 'Entries already exist',
                    'error' => 'Entries already exist'
                ], 400);
            }
    }
    public function addMeritScholarshipStudents_after2024(Request $request){
        // dd($request->all());
        $validator = Validator::make($request->all(),[
                'session_option' => 'required',
                'cohort' => 'required',
                'session_after' => 'required',
            ]);
            if($validator->fails()) {
                return Response::json(array(
                    'success' => false,
                    'errors' => $validator->getMessageBag()->toArray()
                ), 400);
            }
            try {
                $sessIds = DB::table('academic_cohorts')
                        ->where('cohort_id', $request->cohort)
                        ->orderBy('sess_id', 'asc') // Correct placement of orderBy
                        ->pluck('sess_id')          // Replace with your actual column
                        ->toArray();
                $bindings = [
                    'p_session_id_1' => $sessIds[0],
                    'p_session_id_2' => $sessIds[1],
                    'p_sch_session_id' => $request->session_after,
                    'p_entry_user' => auth()->user()->username
                ];
                // dd($bindings);
                $result = DB::executeProcedure('FIN_PRO_MERIT_18_PERCENT', $bindings);
                if ($result) {
                    return response()->json([
                        'success' => true,
                        'message' => "Students added successfully"
                    ]);
                }

            } catch (\Exception $e) {
               $realError = $e->getMessage();
               return response()->json([
                    'success' => false,
                    'message' => $realError,
                    'messege' => $realError,
                    'error' => $realError
                ], 400);
            }

    }
    public function get_student_list_merit_scholarship(Request $request){
        $ssd_proj = ($request->session_option == 'after') ? 'From_Fall 2024' : 'Before_Fall 2024';

        $query = DB::table('FIN_MERIT_18_PERCENT_RESULT as res')
            ->select(
                'res.RESULT_ID as result_id',
                'res.SCH_SESSION_ID as sch_session_id',
                'res.SAI_ENROLLMENT_NO as ssd_sai_enrollment_no',
                'acd.sess_name',
                'acd.sess_code',
                'res.SCHOOL_CODE as school_code',
                'res.DEPT_NAME as dept_name',
                'res.PROG_NAME as prog_name',
                'res.COMBINED_PROGRAM_NAME as combined_program_name',
                'res.AAI_ID as ssd_app_id',
                'res.SPI_NAME as spi_name',
                'res.SPI_FATHERNAME as spi_fathername',
                'res.CALCULATED_CGPA as cal_cgpa',
                'res.SCHOLARSHIP_RANK as scholarship_rank',
                'res.TOTAL_STUDENTS as total_students',
                'res.CUTOFF_RANK as cutoff_rank',
                'res.MBS_SLAB as mbs_slab',
                'res.MBS_SLAB as ssd_percentage',
                'res.ENTRY_USER as entry_user',
                'res.ENTRY_DATETIME as entry_datetime',
                'ssd.SSD_ID as posted_ssd_id',
                'ssd.STATUS as posted_status'
            )
            ->leftJoin('ACADEMIC_SESSIONS as acd', 'acd.SESS_ID', '=', 'res.SCH_SESSION_ID')
            ->leftJoin('STUDENTS_SCHOLARSHIPS_DETAILS as ssd', function($join) use ($ssd_proj) {
                $join->on('ssd.SSD_SAI_ENROLLMENT_NO', '=', 'res.SAI_ENROLLMENT_NO')
                     ->on('ssd.SSD_SESS_ID', '=', DB::raw('TO_CHAR(res.SCH_SESSION_ID)'))
                     ->where('ssd.SSD_PROJECT', '=', $ssd_proj);
            })
            ->where('res.FLAG', '=', $ssd_proj);

        if ($request->session_option == 'before') {
            if (!empty($request->session_sel)) {
                $query->where('res.SCH_SESSION_ID', '=', $request->session_sel);
            }
        } else if ($request->session_option == 'after') {
            if (!empty($request->session_after)) {
                $query->where('res.SCH_SESSION_ID', '=', $request->session_after);
            }
        }

        if (!empty($request->school) && !is_array(json_decode($request->school))) {
            $query->where('res.SAI_SDPO_SCHOOL_ID', '=', $request->school);
        }
        if (!empty($request->department) && !is_array(json_decode($request->department))) {
            $query->where('res.SAI_SDPO_DEPT_ID', '=', $request->department);
        }

        $data = $query->get();

        $summaryQuery = DB::table('FIN_MERIT_18_PERCENT_RESULT as res')
            ->leftJoin('PROGRAMS as p', 'p.PROG_ID', '=', 'res.MAPPED_PROGRAM_ID')
            ->select(
                'res.MAPPED_PROGRAM_ID as mapped_program_id',
                DB::raw("COALESCE(p.PROG_NAME, res.COMBINED_PROGRAM_NAME, res.PROG_NAME, 'Unmapped') as program_name"),
                DB::raw('COUNT(res.RESULT_ID) as total_students')
            )
            ->groupBy('res.MAPPED_PROGRAM_ID', 'p.PROG_NAME', 'res.COMBINED_PROGRAM_NAME', 'res.PROG_NAME')
            ->where('res.FLAG', '=', $ssd_proj);

        if ($request->session_option == 'before') {
            if (!empty($request->session_sel)) {
                $summaryQuery->where('res.SCH_SESSION_ID', '=', $request->session_sel);
            }
        } else if ($request->session_option == 'after') {
            if (!empty($request->session_after)) {
                $summaryQuery->where('res.SCH_SESSION_ID', '=', $request->session_after);
            }
        }

        if (!empty($request->school) && !is_array(json_decode($request->school))) {
            $summaryQuery->where('res.SAI_SDPO_SCHOOL_ID', '=', $request->school);
        }
        if (!empty($request->department) && !is_array(json_decode($request->department))) {
            $summaryQuery->where('res.SAI_SDPO_DEPT_ID', '=', $request->department);
        }

        $programSummary = $summaryQuery->get();

        $dataTable = DataTables::of($data)->make(true);
        $json = json_decode($dataTable->getContent(), true);
        $json['program_summary'] = $programSummary;

        return response()->json($json);
    }

    public function postMeritScholarshipStudents(Request $request){
        $resultIds = $request->input('result_ids', []);
        if (empty($resultIds)) {
            return response()->json([
                'success' => false,
                'message' => 'Please select at least one student to post.'
            ], 400);
        }

        $ssd_proj = ($request->session_option == 'after') ? 'From_Fall 2024' : 'Before_Fall 2024';
        $username = auth()->user()->username ?? 'system';

        try {
            $records = DB::table('FIN_MERIT_18_PERCENT_RESULT')
                ->whereIn('RESULT_ID', $resultIds)
                ->get();

            $postedCount = 0;
            $skippedCount = 0;

            foreach ($records as $row) {
                $exists = DB::table('STUDENTS_SCHOLARSHIPS_DETAILS')
                    ->where('SSD_SAI_ENROLLMENT_NO', $row->sai_enrollment_no)
                    ->where('SSD_SESS_ID', (string)$row->sch_session_id)
                    ->where('SSD_PROJECT', $ssd_proj)
                    ->exists();

                if ($exists) {
                    $skippedCount++;
                    continue;
                }

                $percentage = (float) preg_replace('/[^0-9.]/', '', $row->mbs_slab ?? '0');

                DB::table('STUDENTS_SCHOLARSHIPS_DETAILS')->insert([
                    'SSD_SAI_ENROLLMENT_NO' => $row->sai_enrollment_no,
                    'SSD_SESS_ID'           => (string)$row->sch_session_id,
                    'STATUS'                => 'E',
                    'SCD_DESCRIPTION'       => 'Merit Scholarship' . (!empty($row->mbs_slab) ? ' (' . $row->mbs_slab . ')' : ''),
                    'SCD_ENTRY_USERNAME'    => $username,
                    'UPDATE_DATETIME'       => now(),
                    'ENTRY_DATETIME'        => now(),
                    'SSD_TYPE'              => 2,
                    'SSD_PERCENTAGE'        => $percentage,
                    'SSD_APP_ID'            => $row->aai_id,
                    'SSD_PROJECT'           => $ssd_proj,
                    'SSD_ARREAR'            => 0,
                    'SSD_CHALLAN_CATEGORY'  => 'Full_semester'
                ]);

                $postedCount++;
            }

            return response()->json([
                'success' => true,
                'message' => "Successfully posted {$postedCount} student(s) to Students Scholarships Details." . ($skippedCount > 0 ? " ({$skippedCount} already posted)." : ""),
                'posted_count' => $postedCount,
                'skipped_count' => $skippedCount
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to post merit scholarship students',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function approveMeritStudents(Request $request){
        // dd($request->all());
        $userId = auth()->user()->id;
        $affectedRows =  DB::table('STUDENTS_SCHOLARSHIPS_DETAILS')
                            ->whereIn('SSD_ID', $request->mainId)
                            ->update([
                                'STATUS' => 'E',
                                // 'FFS_ENTRY_USER_ID' => $userId,
                            ]);
        if ($affectedRows > 0) {
            return response()->json([
                'success' => true,
                'data' => 'Approved Successfully'
        ]);
        } else {
            return response()->json([
                'success' => false,
                'data' => 'Some problem occured'
            ]);
        }

    }
}
