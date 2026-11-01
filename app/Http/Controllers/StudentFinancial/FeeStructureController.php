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

class FeeStructureController extends Controller{

        function masterFeeForm(){
            $sessions = \App::call('App\Http\Controllers\Grader\AcademicSessionController@retrieve');
            $schools = Schools::whereNotIn('school_id', [22, 10, 9, 8])->get();
            $departments = array(); // $this->school->fetch();
            $facultyCourses = \App::call('App\Http\Controllers\Grader\FacultyCoursesController@retrieve');
            $offeredFaculty = \App::call('App\Http\Controllers\Grader\CoursesOfferedController@retrieveFaculty');
            $programs =  DB::select("select * from Programs where PROG_STATUS = 'E'");
            $query = 'select * from semesters order by SEMESTER_ID';
            $semesters = DB::select($query);
            return view('StudentFinancial.master_fee_structure',compact('sessions','schools','departments','facultyCourses','offeredFaculty','semesters','programs'));

        }
        public function add(Request $request){
            // dd($request->all());
            $intakeFromName = DB::table('ACADEMIC_SESSIONS')->where('SESS_ID', $request->get('intake_from'))->value('SESS_NAME');
            $this->normalizeFeeAmountInputs($request);
            $amountRules = $this->feeAmountValidationRules($request);
            $validator = Validator::make($request->all(), array_merge([
                'school' => 'required',
                'department' => 'required',
                'degree_level' => 'required',
                'program' => 'required',
                'session' => 'required',
                // 'roadmap_session' => 'required',
                // 'semester_sel' => 'required',
                'student_type' => 'required',
                'fee_type' => 'required',
                'intake_from' => 'required',
                'intake_to' => 'required|gte:intake_from',
                // 'effective_from' => 'required',
                // 'effective_to' => 'required',
            ], $amountRules),[
                'intake_to.gte' => 'Intake To must be the same or a later session than '.($intakeFromName ?: 'Intake From').'.',
            ]);
            if($validator->fails()) {
                return Response::json(array(
                    'success' => false,
                    'error' => $validator->getMessageBag()->toArray()
                ), 400);
            }
            // dd($request->progId);
            // dd(json_decode($request->progId, true));
            // $programs_id = json_decode($request->progId);
            $programs_id = $request->program;
            // dd($programs_id);
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
            // dd($programs_id, $school_depart_prog);

            $resp = $this->allProgramsInsertion($school_depart_prog, $request);
            return response()->json([
                'success' => $resp['success'],
                'msg' => $resp['msg'],
                'needs_conflict_action' => $resp['needs_conflict_action'] ?? false,
                'conflicts' => $resp['conflicts'] ?? [],
            ]);

        }
        private function allProgramsInsertion($school_depart_prog, $request){
            // dd($request->all());
            $userId = auth()->user()->id;
            $resp = [
                'success' => false,
                'msg' => '',
                'needs_conflict_action' => false,
                'conflicts' => [],
            ];
            $tuitionFee = $this->toFeeAmount($request->get('tution_amount'));
            $admissionFee = $this->toFeeAmount($request->get('admission_amount'));
            $securityFee = $this->toFeeAmount($request->get('security_amount'));
            $medicalFee = $this->toFeeAmount($request->get('medical_amount'));
            $activity_fee = $this->toFeeAmount($request->get('activity_charges_amount'));
            $credithrFee = $this->toFeeAmount($request->get('per_credit_fee'));
            $hostelFee = $this->toFeeAmount($request->get('hostel_amount'));
            $athleteFee = $this->toFeeAmount($request->get('student_athlete_amount'));
            $intakeFrom = $request->get('intake_from');
            $intakeTo = $request->get('intake_to');
            $conflictAction = strtolower(trim((string) $request->get('conflict_action', ''))); // overwrite | skip
            $feePayload = [
                'tuition' => $tuitionFee,
                'admission' => $admissionFee,
                'security' => $securityFee,
                'medical' => $medicalFee,
                'activity' => $activity_fee,
                'credithr' => $credithrFee,
                'hostel' => $hostelFee,
                'athlete' => $athleteFee,
            ];
            // dd($tuitionFee, $admissionFee, $securityFee, $medicalFee, $credithrFee);
            if(empty($request->mainId)){//insertion case
                // First pass: find programs that already have an active entry
                // (including roadmap-wise), regardless of current roadmap selection.
                $conflicts = [];
                foreach($school_depart_prog as $val){
                    if(empty($val[0]) || empty($val[2])){
                        continue;
                    }
                    $schoolId = $val[0];
                    $deptId = $val[1];
                    $progId = $val[2];
                    $existingRows = $this->findExistingProgramFeeRows($request, $schoolId, $deptId, $progId, $intakeFrom, $intakeTo);
                    if($existingRows->isEmpty()){
                        continue;
                    }
                    $progName = DB::table('PROGRAMS')->where('PROG_ID', $progId)->value('PROG_NAME');
                    $deptName = DB::table('DEPARTMENTS')->where('DEPT_ID', $deptId)->value('DEPT_NAME');
                    foreach($existingRows as $ex){
                        $roadmapName = null;
                        $intakeFromName = null;
                        $intakeToName = null;
                        if(!empty($ex->ffs_roadmap_session)){
                            $roadmapName = DB::table('ACADEMIC_SESSIONS')->where('SESS_ID', $ex->ffs_roadmap_session)->value('SESS_NAME');
                        }
                        if(!empty($ex->ffs_intake_from_sess)){
                            $intakeFromName = DB::table('ACADEMIC_SESSIONS')->where('SESS_ID', $ex->ffs_intake_from_sess)->value('SESS_NAME');
                        }
                        if(!empty($ex->ffs_intake_to_sess)){
                            $intakeToName = DB::table('ACADEMIC_SESSIONS')->where('SESS_ID', $ex->ffs_intake_to_sess)->value('SESS_NAME');
                        }
                        $challanSummary = $this->getFeeStructureChallanSummary($ex->ffs_id);
                        $conflicts[] = [
                            'ffs_id' => $ex->ffs_id,
                            'school_id' => $schoolId,
                            'dept_id' => $deptId,
                            'prog_id' => $progId,
                            'dept_name' => $deptName,
                            'prog_name' => $progName,
                            'roadmap_session' => $ex->ffs_roadmap_session,
                            'roadmap_session_name' => $roadmapName,
                            'semester_no' => $ex->ffs_semester_no,
                            'intake_from' => $ex->ffs_intake_from_sess,
                            'intake_to' => $ex->ffs_intake_to_sess,
                            'intake_from_name' => $intakeFromName,
                            'intake_to_name' => $intakeToName,
                            'tuition' => $ex->ffs_tutionfee_amt,
                            'fee_type' => $ex->ffs_fee_type,
                            'has_challan' => $challanSummary['has_challan'],
                            'paid_count' => $challanSummary['paid_count'],
                            'unpaid_count' => $challanSummary['unpaid_count'],
                        ];
                    }
                }

                if(count($conflicts) > 0 && !in_array($conflictAction, ['overwrite', 'skip'], true)){
                    $challanLockedCount = 0;
                    foreach($conflicts as $c){
                        if(!empty($c['has_challan'])){
                            $challanLockedCount++;
                        }
                    }
                    $resp['success'] = false;
                    $resp['needs_conflict_action'] = true;
                    $resp['conflicts'] = $conflicts;
                    $resp['challan_locked_count'] = $challanLockedCount;
                    $resp['msg'] = count($conflicts).' existing fee structure record(s) found for selected program(s). Choose Overwrite or Skip.';
                    if($challanLockedCount > 0){
                        $resp['msg'] .= ' '.$challanLockedCount.' record(s) have challan(s) and cannot be overwritten.';
                    }
                    return $resp;
                }

                $conflictProgIds = [];
                $challanLockedProgIds = [];
                foreach($conflicts as $c){
                    $conflictProgIds[strval($c['prog_id'])] = true;
                    if(!empty($c['has_challan'])){
                        $challanLockedProgIds[strval($c['prog_id'])] = true;
                    }
                }

                $insertedCount = 0;
                $updatedCount = 0;
                $skippedCount = 0;
                $challanBlockedCount = 0;
                foreach($school_depart_prog as $val){
                    if(empty($val[0]) || empty($val[2])){
                        continue;
                    }
                    $schoolId = $val[0];
                    $deptId = $val[1];
                    $progId = $val[2];
                    $progKey = strval($progId);
                    $isConflictProg = isset($conflictProgIds[$progKey]);

                    if($isConflictProg && $conflictAction === 'skip'){
                        $skippedCount++;
                        continue;
                    }

                    if($isConflictProg && $conflictAction === 'overwrite'){
                        // Do not overwrite if any challan exists against this program fee structure
                        if(isset($challanLockedProgIds[$progKey])){
                            $challanBlockedCount++;
                            $skippedCount++;
                            continue;
                        }
                        $existingRows = $this->findExistingProgramFeeRows($request, $schoolId, $deptId, $progId, $intakeFrom, $intakeTo);
                        if($existingRows->isEmpty()){
                            // race fallback -> insert
                            $this->insertFeeStructureRow($request, $schoolId, $deptId, $progId, $userId, $feePayload, $intakeFrom, $intakeTo);
                            $insertedCount++;
                            continue;
                        }
                        // Re-check each existing row before overwrite (server-side safety)
                        $hasChallanOnAny = false;
                        foreach($existingRows as $ex){
                            if($this->feeStructureHasChallanById($ex->ffs_id)){
                                $hasChallanOnAny = true;
                                break;
                            }
                        }
                        if($hasChallanOnAny){
                            $challanBlockedCount++;
                            $skippedCount++;
                            continue;
                        }
                        // Keep latest row, overwrite it with form values; soft-delete older duplicates
                        $primary = $existingRows->sortByDesc('ffs_id')->first();
                        foreach($existingRows as $ex){
                            if($ex->ffs_id == $primary->ffs_id){
                                continue;
                            }
                            DB::table('FIN_FEE_STRUCTURE')
                                ->where('FFS_ID', $ex->ffs_id)
                                ->update([
                                    'FFS_STATUS' => 'D',
                                    'FFS_ENTRY_USER_ID' => $userId,
                                    'FFS_ENTRY_DATETIME' => now(),
                                ]);
                        }
                        $this->updateFeeStructureRow($primary->ffs_id, $request, $schoolId, $deptId, $progId, $userId, $feePayload, $intakeFrom, $intakeTo);
                        $updatedCount++;
                        continue;
                    }

                    // No conflict (or same-key exact path): keep previous exact/overlap behavior for same roadmap
                    $overlap = DB::table('FIN_FEE_STRUCTURE')
                                    ->select('FFS_ID', 'FFS_INTAKE_FROM_SESS', 'FFS_INTAKE_TO_SESS')
                                    ->where('FFS_SESS_ID', $request->get('session'))
                                    ->where('FFS_ROADMAP_SESSION', $request->get('roadmap_session'))
                                    ->where('ffs_semester_no', $request->get('semester_sel'))
                                    ->where('FFS_SCHOOL_ID', $schoolId)
                                    ->where('FFS_DEPT_ID', $deptId)
                                    ->where('FFS_DEGREE_LEVEL', $request->get('degree_level'))
                                    ->where('FFS_PROG_ID', $progId)
                                    ->where('FFS_STUDENT_TYPE', $request->get('student_type'))
                                    ->where('FFS_FEE_TYPE', $request->get('fee_type'))
                                    ->where('FFS_STATUS', 'E')
                                    ->where('FFS_INTAKE_FROM_SESS', '<=', $intakeTo)
                                    ->where('FFS_INTAKE_TO_SESS', '>=', $intakeFrom)
                                    ->get();
                    $exactMatch = null;
                    foreach($overlap as $ovRow){
                        if($ovRow->ffs_intake_from_sess == $intakeFrom && $ovRow->ffs_intake_to_sess == $intakeTo){
                            $exactMatch = $ovRow;
                            break;
                        }
                    }
                    if($exactMatch !== null){//exact duplicate (incl. intake range) -> overwrite amounts
                        $this->updateFeeStructureRow($exactMatch->ffs_id, $request, $schoolId, $deptId, $progId, $userId, $feePayload, $intakeFrom, $intakeTo);
                        $updatedCount++;
                    }else if(sizeof($overlap) > 0){//overlapping but not identical -> skip to avoid conflict
                        $skippedCount++;
                    }else{//no overlap -> insert new record
                        $this->insertFeeStructureRow($request, $schoolId, $deptId, $progId, $userId, $feePayload, $intakeFrom, $intakeTo);
                        $insertedCount++;
                    }
                }
                if($skippedCount == 0){
                    $resp['success'] = true;
                    $resp['msg'] = 'Saved Successfully';
                }else if(($insertedCount + $updatedCount) > 0){
                    $resp['success'] = true;
                    $otherSkippedCount = max(0, $skippedCount - $challanBlockedCount);
                    $resp['msg'] = 'Saved/updated '.($insertedCount + $updatedCount).' record(s).';
                    if($otherSkippedCount > 0){
                        $resp['msg'] .= ' '.$otherSkippedCount.' record(s) skipped.';
                    }
                    if($challanBlockedCount > 0){
                        $resp['msg'] .= ' '.$challanBlockedCount.' record(s) could not be overwritten because challan(s) already exist.';
                    }
                }else{
                    $resp['success'] = false;
                    if($challanBlockedCount > 0){
                        $resp['msg'] = 'Overwrite not allowed. '.$challanBlockedCount.' existing fee structure record(s) have challan(s). Please skip those program(s) or cancel challans first.';
                    }else{
                        $resp['msg'] = 'Nothing saved. Selected program(s) were skipped or overlapped an existing intake range.';
                    }
                }
                return $resp;
            }else if(sizeof($school_depart_prog) == 1){ //edit case
                if(!empty($school_depart_prog[0][0])){//checking if the allprograms option is selected and there is no program in it
                    $schoolId = $school_depart_prog[0][0];
                    $deptId = $school_depart_prog[0][1];
                    $progId = $school_depart_prog[0][2];
                    if($this->feeStructureHasChallanById($request->mainId)){
                        $resp['success'] = false;
                        $resp['msg'] = 'Cannot edit: challan(s) already exist against this fee structure';
                        return $resp;
                    }
                    $overlap = DB::table('FIN_FEE_STRUCTURE')
                                    ->select('FFS_ID', 'FFS_INTAKE_FROM_SESS', 'FFS_INTAKE_TO_SESS')
                                            ->where('FFS_SESS_ID', $request->get('session'))
                                            ->where('FFS_ROADMAP_SESSION', $request->get('roadmap_session'))
                                            ->where('ffs_semester_no', $request->get('semester_sel'))
                                            ->where('FFS_SCHOOL_ID', $schoolId)
                                            ->where('FFS_DEPT_ID', $deptId)
                                            ->where('FFS_DEGREE_LEVEL', $request->get('degree_level'))
                                            ->where('FFS_PROG_ID', $progId)
                                            ->where('FFS_STUDENT_TYPE', $request->get('student_type'))
                                            ->where('FFS_FEE_TYPE', $request->get('fee_type'))
                                            ->where('FFS_STATUS', 'E')
                                            ->where('FFS_ID', '!=', $request->mainId)
                                            ->where('FFS_INTAKE_FROM_SESS', '<=', $intakeTo)
                                            ->where('FFS_INTAKE_TO_SESS', '>=', $intakeFrom)
                                            ->get();
                    $hasExact = false;
                    foreach($overlap as $ovRow){
                        if($ovRow->ffs_intake_from_sess == $intakeFrom && $ovRow->ffs_intake_to_sess == $intakeTo){
                            $hasExact = true;
                            break;
                        }
                    }
                    if($hasExact){
                        $resp['success']=false;
                        $resp['msg']='Data already exists';
                        return $resp;
                    }else if(sizeof($overlap) > 0){
                        $resp['success']=false;
                        $resp['msg']='Intake range overlaps an existing record';
                        return $resp;
                    }else{
                        try {
                        $this->updateFeeStructureRow($request->mainId, $request, $schoolId, $deptId, $progId, $userId, $feePayload, $intakeFrom, $intakeTo);
                            $resp['success'] = true;
                            $resp['msg'] = 'Updated Successfully';
                        } catch (\Exception $e) {
                            $resp['success'] = false;
                            $resp['msg'] = 'Uniqueness conflict occurred';

                            // Optionally log the exception for debugging
                            // Log::error('Error updating FIN_FEE_POLICY: ' . $e->getMessage());
                        }
                        return $resp;
                    }
                }

            }else{
                $resp['success']=false;
                $resp['msg']='You can edit one program at a time';
                return $resp;
            }

            return $resp;
        }

        private function findExistingProgramFeeRows($request, $schoolId, $deptId, $progId, $intakeFrom, $intakeTo){
            // Any active row for same program key (roadmap ignored) with intake overlap
            return DB::table('FIN_FEE_STRUCTURE')
                ->select(
                    'FFS_ID',
                    'FFS_ROADMAP_SESSION',
                    'ffs_semester_no',
                    'FFS_INTAKE_FROM_SESS',
                    'FFS_INTAKE_TO_SESS',
                    'FFS_TUTIONFEE_AMT',
                    'FFS_FEE_TYPE'
                )
                ->where('FFS_SESS_ID', $request->get('session'))
                ->where('FFS_SCHOOL_ID', $schoolId)
                ->where('FFS_DEPT_ID', $deptId)
                ->where('FFS_PROG_ID', $progId)
                ->where('FFS_STUDENT_TYPE', $request->get('student_type'))
                ->where('FFS_FEE_TYPE', $request->get('fee_type'))
                ->where('FFS_STATUS', 'E')
                ->where('FFS_INTAKE_FROM_SESS', '<=', $intakeTo)
                ->where('FFS_INTAKE_TO_SESS', '>=', $intakeFrom)
                ->orderBy('FFS_ID', 'desc')
                ->get();
        }

        private function insertFeeStructureRow($request, $schoolId, $deptId, $progId, $userId, $feePayload, $intakeFrom, $intakeTo){
            DB::table('FIN_FEE_STRUCTURE')->insert([
                'FFS_SCHOOL_ID' => $schoolId,
                'FFS_DEPT_ID' => $deptId,
                'FFS_PROG_ID' => $progId,
                'FFS_SESS_ID' => $request->get('session'),
                'FFS_ROADMAP_SESSION' => $request->get('roadmap_session'),
                'ffs_semester_no' => $request->get('semester_sel'),
                'FFS_DEGREE_LEVEL' => $request->get('degree_level'),
                'FFS_STUDENT_TYPE' => $request->get('student_type'),
                'FFS_FEE_TYPE' => $request->get('fee_type'),
                'FFS_INTAKE_FROM_SESS' => $intakeFrom,
                'FFS_INTAKE_TO_SESS' => $intakeTo,
                'FFS_ENTRY_USER_ID' => $userId,
                'FFS_TUTIONFEE_AMT' => $feePayload['tuition'],
                'FFS_ADMISSION_AMT' => $feePayload['admission'],
                'FFS_SECURITY_AMT' => $feePayload['security'],
                'FFS_MEDICAL_AMT' => $feePayload['medical'],
                'FFS_ACTIVITY_AMT' => $feePayload['activity'],
                'FFS_CREDHR_AMT' => $feePayload['credithr'],
                'FFS_HOSTEL_AMT' => $feePayload['hostel'],
                'FFS_STUDENT_ATHLETE_AMT' => $feePayload['athlete'],
                'FFS_STATUS' => 'E',
                'FFS_ENTRY_DATETIME' => now(),
            ]);
        }

        private function updateFeeStructureRow($ffsId, $request, $schoolId, $deptId, $progId, $userId, $feePayload, $intakeFrom, $intakeTo){
            DB::table('FIN_FEE_STRUCTURE')
                ->where('FFS_ID', $ffsId)
                ->update([
                    'FFS_SCHOOL_ID' => $schoolId,
                    'FFS_DEPT_ID' => $deptId,
                    'FFS_PROG_ID' => $progId,
                    'FFS_SESS_ID' => $request->get('session'),
                    'FFS_ROADMAP_SESSION' => $request->get('roadmap_session'),
                    'ffs_semester_no' => $request->get('semester_sel'),
                    'FFS_DEGREE_LEVEL' => $request->get('degree_level'),
                    'FFS_STUDENT_TYPE' => $request->get('student_type'),
                    'FFS_FEE_TYPE' => $request->get('fee_type'),
                    'FFS_INTAKE_FROM_SESS' => $intakeFrom,
                    'FFS_INTAKE_TO_SESS' => $intakeTo,
                    'FFS_ENTRY_USER_ID' => $userId,
                    'FFS_TUTIONFEE_AMT' => $feePayload['tuition'],
                    'FFS_ADMISSION_AMT' => $feePayload['admission'],
                    'FFS_SECURITY_AMT' => $feePayload['security'],
                    'FFS_MEDICAL_AMT' => $feePayload['medical'],
                    'FFS_ACTIVITY_AMT' => $feePayload['activity'],
                    'FFS_CREDHR_AMT' => $feePayload['credithr'],
                    'FFS_HOSTEL_AMT' => $feePayload['hostel'],
                    'FFS_STUDENT_ATHLETE_AMT' => $feePayload['athlete'],
                    'FFS_STATUS' => 'E',
                    'FFS_ENTRY_DATETIME' => now(),
                ]);
        }
        protected function getMasterFeeList(Request $request){
            // dd(json_decode($request->school_sel), json_decode($request->department), json_decode($request->program), $request->program);
            // dd();
            // if(!is_array(json_decode($request->department))){
            //     dd('single dep', );
            // }else{
            //     dd('all dep');
            // }
            $data = DB::table('FIN_FEE_STRUCTURE as ffs')
                ->select(
                         'ffs.FFS_STATUS',
                        'ffs.ffs_id',
                        'ffs.FFS_FEE',
                        'ffs.FFS_STUDENT_TYPE',
                        'ffs.FFS_FEE_TYPE',
                        'ffs.FFS_EFF_FROM',
                        'ffs.FFS_EFF_TO',
                        'acd1.SESS_NAME as SESS_NAME', // Session name for FFS_SESS_ID
                        'acd2.SESS_NAME as ROADMAP_SESSION_NAME', // Session name for FFS_ROADMAP_SESSION
                        'school.SCHOOL_NAME',
                        'dept.DEPT_NAME',
                        'prog.PROG_NAME',
                        'ffs.FFS_TUTIONFEE_AMT',
                        'ffs.FFS_ADMISSION_AMT',
                        'ffs.FFS_SECURITY_AMT',
                        'ffs.FFS_MEDICAL_AMT',
                        'ffs.FFS_HOSTEL_AMT',
                        'ffs.FFS_CREDHR_AMT',
                        'ffs.FFS_ACTIVITY_AMT',
                        'ffs.FFS_STUDENT_ATHLETE_AMT',
                        'ffs.ffs_eff_from',
                        'ffs.FFS_SESS_ID',
                        'ffs.FFS_ROADMAP_SESSION',
                        'ffs.ffs_semester_no',
                        'sem.SEMESTER_NAME',
                        'ffs.FFS_SCHOOL_ID',
                        'ffs.FFS_DEPT_ID',
                        'ffs.FFS_DEGREE_LEVEL',
                        'ffs.FFS_PROG_ID',
                        'ffs.FFS_INTAKE_FROM_SESS',
                        'ffs.FFS_INTAKE_TO_SESS',
                        'acd3.SESS_NAME as INTAKE_FROM_NAME', // Session name for FFS_INTAKE_FROM_SESS
                        'acd4.SESS_NAME as INTAKE_TO_NAME' // Session name for FFS_INTAKE_TO_SESS
                        )
                        ->join('ACADEMIC_SESSIONS as acd1', 'acd1.SESS_ID', '=', 'ffs.FFS_SESS_ID') // Join for FFS_SESS_ID
                        ->leftJoin('ACADEMIC_SESSIONS as acd2', 'acd2.SESS_ID', '=', 'ffs.FFS_ROADMAP_SESSION') // Join for FFS_ROADMAP_SESSION
                        ->leftJoin('ACADEMIC_SESSIONS as acd3', 'acd3.SESS_ID', '=', 'ffs.FFS_INTAKE_FROM_SESS') // Join for FFS_INTAKE_FROM_SESS
                        ->leftJoin('ACADEMIC_SESSIONS as acd4', 'acd4.SESS_ID', '=', 'ffs.FFS_INTAKE_TO_SESS') // Join for FFS_INTAKE_TO_SESS
                        ->leftJoin('SEMESTERS as sem', 'sem.SEMESTER_ID', '=', 'ffs.ffs_semester_no') // Join for semester
                        ->join('SCHOOLS as school', 'school.SCHOOL_ID', 'ffs.FFS_SCHOOL_ID')
                        ->join('DEPARTMENTS as dept', 'dept.DEPT_ID', 'ffs.FFS_DEPT_ID')
                        ->join('PROGRAMS as prog', 'prog.PROG_ID', 'ffs.FFS_PROG_ID')
                        ->where('ffs.FFS_STATUS', '=', 'E') ;// Ensure case sensitivity

                        // ->join('fin_fee_category as fincateg', 'fincateg.id', 'ffs.FFS_FEECATEGORY')
                        if(!empty($request->session_sel)){
                            $data =  $data->where('ffs.FFS_SESS_ID', $request->session_sel);
                        }
                        if(!empty($request->intake_from)){
                            $data =  $data->where('ffs.FFS_INTAKE_FROM_SESS', $request->intake_from);
                        }
                        if(!empty($request->intake_to)){
                            $data =  $data->where('ffs.FFS_INTAKE_TO_SESS', $request->intake_to);
                        }
                        if(!empty($request->school_sel) && !is_array(json_decode($request->school_sel))){//condition will applied only when single school is selected if all school is slected then no need to apply condition//array means all school is selected and so we apply not array condtion to apply where clause;
                        $data =  $data->where('ffs.FFS_SCHOOL_ID', $request->school_sel);
                        }
                        if(!empty($request->studentTypeOption)){
                        $data =  $data->where('ffs.FFS_STUDENT_TYPE', $request->studentTypeOption);
                        }
                        if(!empty($request->feeType)){
                        $data =  $data->where('ffs.FFS_FEE_TYPE', $request->feeType);
                        }
                        if(!empty($request->department) && !is_array(json_decode($request->department))){//condition will applied only when single department is selected if all department is slected then no need to apply condition//array means all department is selected and so we apply not array condtion to apply where clause;
                            $data =  $data->where('ffs.FFS_DEPT_ID', $request->department);
                        }
                        if(!empty($request->degree_level)){
                        $data =  $data->where('ffs.FFS_DEGREE_LEVEL', $request->degree_level);
                        }
                        if(!empty($request->program)&& !is_array(json_decode($request->program))){//condition will applied only when single program is selected if all program is slected then no need to apply condition//array means all program is selected and so we apply not array condtion to apply where clause;
                        //    dd('program condtion applied');
                            $programs_id = $request->program;
                            // dd($programs_id);
                            $array_of_sch_dep_prog = explode('~~', $programs_id);
                            // dd($array_of_sch_dep_prog[2]);
                        $data =  $data->where('ffs.FFS_PROG_ID', $array_of_sch_dep_prog[2]);
                        }
                        // dd('prgram condition not applied');
                        if(!empty($request->tutionAmt)){
                        $data =  $data->where('ffs.FFS_TUTIONFEE_AMT', $request->tutionAmt);
                        }
                        if(!empty($request->admissionAmt)){
                        $data =  $data->where('ffs.FFS_ADMISSION_AMT', $request->admissionAmt);
                        }
                        if(!empty($request->securityAmt)){
                        $data =  $data->where('ffs.FFS_SECURITY_AMT', $request->securityAmt);
                        }
                        if(!empty($request->medicalAmt)){
                        $data =  $data->where('ffs.FFS_MEDICAL_AMT', $request->medicalAmt);
                        }
                        if(!empty($request->credFee)){
                        $data =  $data->where('ffs.FFS_CREDHR_AMT', $request->credFee);
                        }
                        if(!empty($request->activitychargesAmt)){
                        $data =  $data->where('ffs.FFS_ACTIVITY_AMT', $request->activitychargesAmt);
                        }
                        if(!empty($request->studentAthleteAmt)){
                        $data =  $data->where('ffs.FFS_STUDENT_ATHLETE_AMT', $request->studentAthleteAmt);
                        }

                        $data =  $data->orderBy('ffs.ffs_id', 'desc')->get();

                        //exit;
                    //    dd($data);
                        // $sql = "SELECT * from FIN_FEE_STRUCTURE";
                        // //echo $sql;exit;
                        // $res = DB::select($sql);
                        // dd($data);
            return DataTables::of($data)->toJson();
        }
        public function delete_feeData(Request $request){
            $userId = auth()->user()->id;
            $ids = is_array($request->mainId) ? $request->mainId : [$request->mainId];
            $lockedIds = [];
            foreach($ids as $id){
                if($this->feeStructureHasChallanById($id)){
                    $lockedIds[] = $id;
                }
            }
            if(count($lockedIds) === count($ids)){
                return response()->json([
                    'success' => false,
                    'data' => 'Cannot delete: challan(s) already exist against selected fee structure(s)'
                ]);
            }
            $deletableIds = array_values(array_diff($ids, $lockedIds));
            $affectedRows =  DB::table('FIN_FEE_STRUCTURE')
                                ->whereIn('FFS_ID', $deletableIds)
                                ->update([
                                    'FFS_STATUS' => 'D',
                                    'FFS_ENTRY_USER_ID' => $userId,
                                ]);
            if ($affectedRows > 0) {
                $msg = 'Deleted Successfully';
                if(count($lockedIds) > 0){
                    $msg .= '. '.count($lockedIds).' record(s) skipped because challan(s) exist';
                }
                return response()->json([
                    'success' => true,
                    'data' => $msg
            ]);
            } else {
                return response()->json([
                    'success' => false,
                    'data' => 'Some problem occured'
                ]);
            }

        }

        public function checkFeeStructureChallan(Request $request){
            $ids = is_array($request->mainId) ? $request->mainId : [$request->mainId];
            $ids = array_values(array_filter($ids, function($id){
                return $id !== null && $id !== '';
            }));
            $lockedIds = [];
            $details = [];
            $totalPaid = 0;
            $totalUnpaid = 0;
            foreach($ids as $id){
                $summary = $this->getFeeStructureChallanSummary($id);
                if($summary['has_challan']){
                    $lockedIds[] = $id;
                    $totalPaid += $summary['paid_count'];
                    $totalUnpaid += $summary['unpaid_count'];
                    $details[] = [
                        'ffs_id' => $id,
                        'paid_count' => $summary['paid_count'],
                        'unpaid_count' => $summary['unpaid_count'],
                        'total_count' => $summary['paid_count'] + $summary['unpaid_count'],
                    ];
                }
            }
            return response()->json([
                'success' => true,
                'has_challan' => count($lockedIds) > 0,
                'locked_ids' => $lockedIds,
                'paid_count' => $totalPaid,
                'unpaid_count' => $totalUnpaid,
                'total_count' => $totalPaid + $totalUnpaid,
                'details' => $details,
            ]);
        }

        private function feeStructureHasChallanById($ffsId){
            $summary = $this->getFeeStructureChallanSummary($ffsId);
            return $summary['has_challan'];
        }

        private function getFeeStructureChallanSummary($ffsId){
            $row = DB::table('FIN_FEE_STRUCTURE')
                ->where('FFS_ID', $ffsId)
                ->where('FFS_STATUS', 'E')
                ->first();
            if(!$row){
                return [
                    'has_challan' => false,
                    'paid_count' => 0,
                    'unpaid_count' => 0,
                ];
            }
            // Only PAID / UNPAID — cancelled challans are excluded
            $counts = DB::table('STUDENT_CHALLAN')
                ->where('SC_SESSION_ID', $row->ffs_sess_id)
                ->where('SC_SCHOOL_ID', $row->ffs_school_id)
                ->where('SC_DEPARTMENT_ID', $row->ffs_dept_id)
                ->where('SC_PROGRAM_ID', $row->ffs_prog_id)
                ->where('SC_STUDENT_STATUS', $row->ffs_student_type)
                ->where('SC_CHALLAN_TYPE', $row->ffs_fee_type)
                ->whereRaw("UPPER(TRIM(SC_CHALLAN_STATUS)) IN ('PAID', 'UNPAID')")
                ->selectRaw("
                    SUM(CASE WHEN UPPER(TRIM(SC_CHALLAN_STATUS)) = 'PAID' THEN 1 ELSE 0 END) as paid_count,
                    SUM(CASE WHEN UPPER(TRIM(SC_CHALLAN_STATUS)) = 'UNPAID' THEN 1 ELSE 0 END) as unpaid_count
                ")
                ->first();

            $paid = (int) ($counts->paid_count ?? $counts->PAID_COUNT ?? 0);
            $unpaid = (int) ($counts->unpaid_count ?? $counts->UNPAID_COUNT ?? 0);

            return [
                'has_challan' => ($paid + $unpaid) > 0,
                'paid_count' => $paid,
                'unpaid_count' => $unpaid,
            ];
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

        private function feeAmountInputKeys(){
            return [
                'tution_amount' => 'Tuition Fee',
                'admission_amount' => 'Admission Fee',
                'security_amount' => 'Security Fee',
                'medical_amount' => 'Medical Fee',
                'activity_charges_amount' => 'Activity Charges',
                'student_athlete_amount' => 'Student Athlete Fee',
                'per_credit_fee' => 'Per Credit Fee',
                'hostel_amount' => 'Hostel Fee',
            ];
        }

        private function toFeeAmount($value){
            if(is_array($value)){
                $value = end($value);
            }
            if($value === null || $value === ''){
                return 0;
            }
            return is_numeric($value) ? $value + 0 : 0;
        }

        private function normalizeFeeAmountInputs(Request $request){
            $merged = [];
            foreach(array_keys($this->feeAmountInputKeys()) as $field){
                $merged[$field] = $this->toFeeAmount($request->get($field));
            }
            $request->merge($merged);
        }

        private function feeAmountValidationRules(Request $request){
            $rules = [];
            foreach($this->feeAmountInputKeys() as $field => $label){
                $rules[$field] = 'nullable|numeric|min:0';
            }
            $feeType = $request->get('fee_type');
            $studentType = $request->get('student_type');
            $required = [];
            if($feeType === 'Full_semester'){
                $required = ['tution_amount', 'medical_amount', 'activity_charges_amount', 'student_athlete_amount'];
                if($studentType !== 'Existing'){
                    $required[] = 'admission_amount';
                    $required[] = 'security_amount';
                }
            }else if($feeType === 'Credit_hours'){
                $required = ['per_credit_fee', 'medical_amount', 'activity_charges_amount', 'student_athlete_amount'];
            }
            foreach($required as $field){
                $rules[$field] = 'required|numeric|min:0';
            }
            return $rules;
        }

        protected function getDepartmentsFeeStructureForm(Request $request){
            // dd($request->get('school_id'));
            $school_id = json_decode($request->get('school_id'));
            $departments=[];
            $managementSchoolId = 11;
            $managementDeptIds = [20, 23];
            if(!empty($school_id)){
                $query = DB::table('departments')
                    ->where('dept_status', '=', 'E');
                if(is_array($school_id)){
                    $query->whereIn('dept_school_id', $school_id)
                        ->where(function($q) use ($managementSchoolId, $managementDeptIds) {
                            $q->where('dept_school_id', '!=', $managementSchoolId)
                                ->orWhere(function($q2) use ($managementSchoolId, $managementDeptIds) {
                                    $q2->where('dept_school_id', $managementSchoolId)
                                        ->whereIn('dept_id', $managementDeptIds);
                                });
                        });
                }else{
                    $query->where('dept_school_id', $school_id);
                    if((int) $school_id === $managementSchoolId){
                        $query->whereIn('dept_id', $managementDeptIds);
                    }
                }
                $departments = $query->orderBy('DEPT_NAME', 'asc')->get();
                // dd($departments);

            }
            return response()->json(['data' => $departments, 'status'=> true, 'message' => count($departments).' record(s) found!']);
        }

}
