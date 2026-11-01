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

class FeeInstallmentController extends Controller{

    public function installmentForm(){
        $authz = $this->assertCanManageCmsFeeInstallments();
        if ($authz['success'] === false) {
            abort(403, $authz['msg']);
        }

        $sessions = \App::call('App\Http\Controllers\Grader\AcademicSessionController@retrieve');
        $schools = Schools::whereNotIn('school_id', [22, 10, 9, 8])->get();
        $departments = array(); // $this->school->fetch();
        $facultyCourses = \App::call('App\Http\Controllers\Grader\FacultyCoursesController@retrieve');
        $offeredFaculty = \App::call('App\Http\Controllers\Grader\CoursesOfferedController@retrieveFaculty');
        $programs =  DB::select("select * from Programs where PROG_STATUS = 'E'");
        $query = 'select * from fin_fee_category';
        $feeCategories = DB::select($query);
        return view('StudentFinancial.feeInstallment',compact('sessions','schools','departments','facultyCourses','offeredFaculty','feeCategories','programs'));

    }
    public function addInstallment(Request $request){
        $authz = $this->assertCanManageCmsFeeInstallments();
        if ($authz['success'] === false) {
            return response()->json($authz, 403);
        }

        // dd($request->all());
        $validator = Validator::make($request->all(),[
            'session' => 'required',
            'school' => 'required',
            'department' => 'required',
            'program' => 'required',
            // 'student_type' => 'required',
            'first_installment_due_date' => 'required',
            'first_installment_valid_date' => 'required',
            // 'first_installment_visible_date' => 'required',

            'second_installment_due_date' => 'required',
            'second_installment_valid_date' => 'required',
            // 'second_installment_visible_date' => 'required',

            'third_installment_due_date' => 'required',
            'third_installment_valid_date' => 'required',
            // 'third_installment_visible_date' => 'required',

            'forth_installment_due_date' => 'required',
            'forth_installment_valid_date' => 'required',
            // 'forth_installment_visible_date' => 'required',

            'fifth_installment_due_date' => 'required',
            'fifth_installment_valid_date' => 'required',
            // 'fifth_installment_visible_date' => 'required',
        ]);
        if($validator->fails()) {
            return Response::json(array(
                'success' => false,
                'errors' => $validator->getMessageBag()->toArray()
            ), 400);
            // return response()->json(['success' => true, 'count'=> $generatdChallan_count]);
            // return response()->json(['success' => false, 'messege'=> 'Something went wrong. (Duplicate entries not allowed)', 'errors' => $e->getMessage()], 500);
        }
        // Date chain: due >= today, valid >= its own due, and each due >= previous valid.
        $installment_dates = [
            '1st' => ['due' => $request->get('first_installment_due_date'),  'valid' => $request->get('first_installment_valid_date')],
            '2nd' => ['due' => $request->get('second_installment_due_date'), 'valid' => $request->get('second_installment_valid_date')],
            '3rd' => ['due' => $request->get('third_installment_due_date'),  'valid' => $request->get('third_installment_valid_date')],
            '4th' => ['due' => $request->get('forth_installment_due_date'),  'valid' => $request->get('forth_installment_valid_date')],
            '5th' => ['due' => $request->get('fifth_installment_due_date'),  'valid' => $request->get('fifth_installment_valid_date')],
        ];

        $today = strtotime(Carbon::now('Asia/Karachi')->toDateString());
        $errors = [];
        $prev_valid = null;
        $prev_label = null;

        foreach ($installment_dates as $label => $row) {
            $due = strtotime($row['due']);
            $valid = strtotime($row['valid']);

            if ($due === false || $valid === false) {
                $errors['due_date'][] = "The {$label} installment dates are invalid.";
                continue;
            }

            if ($due < $today) {
                $errors['due_date'][] = "The {$label} installment due date cannot be earlier than today.";
            }
            if ($valid < $due) {
                $errors['valid_date'][] = "The {$label} installment valid date must be on or after its due date.";
            }
            if ($prev_valid !== null && $due < $prev_valid) {
                $errors['due_date'][] = "The {$label} installment due date must be on or after the {$prev_label} installment valid date.";
            }

            $prev_valid = $valid;
            $prev_label = $label;
        }

        if (!empty($errors)) {
            return Response::json([
                'success' => false,
                'errors' => $errors,
            ], 400);
        }
        ///////////////////////////
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
        $resp = $this->allProgramsInsertion($school_depart_prog, $request);
        // return response()->json(['success' => true]);
        return response()->json([
            'success' => $resp[0],
            'msg' => $resp[1],
        ]);
    }
    private static function allProgramsInsertion($school_depart_prog, $request){
        // dd($request->all());
        $userId = auth()->user()->id;
        $resp = [];
        $first_installment_due_date = $request->get('first_installment_due_date');
        $first_installment_valid_date = $request->get('first_installment_valid_date');
        // $first_installment_visible_date = !empty($request->get('first_installment_visible_date'))? $request->get('first_installment_visible_date') : Carbon::now('Asia/Karachi');

        $second_installment_due_date = $request->get('second_installment_due_date');
        $second_installment_valid_date = $request->get('second_installment_valid_date');
        // $second_installment_visible_date = !empty($request->get('second_installment_visible_date'))? $request->get('second_installment_visible_date') : Carbon::now('Asia/Karachi');

        $third_installment_due_date = $request->get('third_installment_due_date');
        $third_installment_valid_date = $request->get('third_installment_valid_date');
        // $third_installment_visible_date = !empty($request->get('third_installment_visible_date'))? $request->get('third_installment_visible_date') : Carbon::now('Asia/Karachi');

        $forth_installment_due_date = $request->get('forth_installment_due_date');
        $forth_installment_valid_date = $request->get('forth_installment_valid_date');
        // $forth_installment_visible_date = !empty($request->get('forth_installment_visible_date'))? $request->get('forth_installment_visible_date') : Carbon::now('Asia/Karachi');

        $fifth_installment_due_date = $request->get('fifth_installment_due_date');
        $fifth_installment_valid_date = $request->get('fifth_installment_valid_date');
        // $fifth_installment_visible_date = !empty($request->get('fifth_installment_visible_date'))? $request->get('fifth_installment_visible_date') : Carbon::now('Asia/Karachi');
        // dd($tuitionFee, $admissionFee, $securityFee, $medicalFee, $credithrFee);
        if(empty($request->mainId)){//insertion case
            foreach($school_depart_prog as $val){
                $schoolId = $val[0];
                $deptId = $val[1];
                $progId = $val[2];
                $duplicateCheck = DB::table('fin_challan_installments')
                                ->select(
                                        'FCI_ID',
                                        'FCI_SESSION_ID',
                                        'FCI_SCHOOL_ID',
                                        'FCI_DEPT_ID',
                                        'FCI_PROG_ID',
                                        // 'FCI_ADMISSION_TYPE',
                                        // 'FCI_STUDENT_TYPE',
                                        'FCI_FLAG',
                                        )
                                        ->where('FCI_SESSION_ID', $request->get('session'))
                                        ->where('FCI_SCHOOL_ID', $schoolId)
                                        ->where('FCI_DEPT_ID', $deptId)
                                        ->where('FCI_PROG_ID', $progId)
                                        ->where('FCI_STATUS', 'E')
                                        // ->where('FCI_STUDENT_TYPE', $request->get('student_type'))
                                        ->where('FCI_FLAG', 'CMS')
                                        ->get();
                                    //  dd($duplicateCheck);
                if(sizeof($duplicateCheck) == 0 ){
                    DB::table('fin_challan_installments')->insert([
                        'FCI_SESSION_ID' => $request->get('session'),
                        'FCI_SCHOOL_ID' => $schoolId,
                        'FCI_DEPT_ID' => $deptId,
                        'FCI_PROG_ID' => $progId,
                        // 'FCI_ADMISSION_TYPE' => $userId,
                        // 'FCI_FLAG' => $userId,

                        'FCI_FIRST_DUEDATE' => $first_installment_due_date,
                        'FCI_FIRST_VALIDDATE' => $first_installment_valid_date,
                        // 'FCI_FIRST_VISIBLEDATE' =>  $first_installment_visible_date,

                        'FCI_SECOND_DUEDATE' =>   $second_installment_due_date,
                        'FCI_SECOND_VALIDDATE' =>  $second_installment_valid_date,
                        // 'FCI_SECOND_VISIBLEDATE' =>  $second_installment_visible_date,

                        'FCI_THIRD_DUEDATE' => $third_installment_due_date,
                        'FCI_THIRD_VALIDDATE' => $third_installment_valid_date,
                        // 'FCI_THIRD_VISIBLEDATE' => $third_installment_visible_date,

                        'FCI_FOURTH_DUEDATE' => $forth_installment_due_date,
                        'FCI_FOURTH_VALIDDATE' => $forth_installment_valid_date,
                        // 'FCI_FOURTH_VISIBLEDATE' => $forth_installment_visible_date,

                        'FCI_FIFTH_DUEDATE' => $fifth_installment_due_date,
                        'FCI_FIFTH_VALIDDATE' => $fifth_installment_valid_date,
                        // 'FCI_FIFTH_VISIBLEDATE' => $fifth_installment_visible_date,

                        // 'FCI_STUDENT_TYPE' => $request->get('student_type'),
                        'FCI_FLAG' => 'CMS',
                        'FCI_STATUS' => 'E',
                        'FCI_ENTRY_USERID' => $userId,
                        'FCI_CREATED_AT' => now('Asia/Karachi')->format('Y-m-d H:i:s')
                    ]);
                }else{
                    // DB::table('fin_challan_installments')
                    // ->where('FCI_ID', $duplicateCheck[0]->fci_id)
                    // ->update([
                    //     'FCI_SESSION_ID' => $request->get('session'),
                    //     'FCI_SCHOOL_ID' => $schoolId,
                    //     'FCI_DEPT_ID' => $deptId,
                    //     'FCI_PROG_ID' => $progId,
                    //     // 'FCI_ADMISSION_TYPE' => $userId,
                    //     // 'FCI_FLAG' => $userId,

                    //     'FCI_FIRST_DUEDATE' => $first_installment_due_date,
                    //     'FCI_FIRST_VALIDDATE' => $first_installment_valid_date,
                    //     'FCI_FIRST_VISIBLEDATE' =>  $first_installment_visible_date,

                    //     'FCI_SECOND_DUEDATE' =>   $second_installment_due_date,
                    //     'FCI_SECOND_VALIDDATE' =>  $second_installment_valid_date,
                    //     'FCI_SECOND_VISIBLEDATE' =>  $second_installment_visible_date,

                    //     'FCI_THIRD_DUEDATE' => $third_installment_due_date,
                    //     'FCI_THIRD_VALIDDATE' => $third_installment_valid_date,
                    //     'FCI_THIRD_VISIBLEDATE' => $third_installment_visible_date,

                    //     'FCI_STATUS' => 'E',
                    //     'FCI_ENTRY_USERID' => $userId,
                    //     'FCI_UPDATED_AT' => now('Asia/Karachi')->format('Y-m-d H:i:s')
                    // ]);
                    $resp[0]=false;
                    $resp[1]='Entry Already Exist';
                    return $resp;
                }

            }
            $resp[0]=true;
            $resp[1]='Saved Successfully';
            return $resp;
        }else if(sizeof($school_depart_prog) == 1){ //edit case
            if(!empty($school_depart_prog[0][0])){//checking if the allprograms option is selected and there is no program in it
                $schoolId = $school_depart_prog[0][0];
                $deptId = $school_depart_prog[0][1];
                $progId = $school_depart_prog[0][2];

                $duplicateCheck = DB::table('fin_challan_installments')
                                ->select(
                                        'FCI_ID',
                                        'FCI_SESSION_ID',
                                        'FCI_SCHOOL_ID',
                                        'FCI_DEPT_ID',
                                        'FCI_PROG_ID',
                                        'FCI_ADMISSION_TYPE',
                                        'FCI_FLAG',
                                        )
                                        ->where('FCI_SESSION_ID', $request->get('session'))
                                        ->where('FCI_SCHOOL_ID', $schoolId)
                                        ->where('FCI_DEPT_ID', $deptId)
                                        ->where('FCI_PROG_ID', $progId)
                                        ->where('FCI_STATUS', 'E')
                                        ->where('FCI_ID', '!=', $request->mainId)
                                        ->get();
                        //  dd($duplicateCheck);
                if(sizeof($duplicateCheck) == 0 ){
                    try {
                        DB::table('fin_challan_installments')
                        ->where('FCI_ID', $request->mainId)
                        ->update([
                            'FCI_SESSION_ID' => $request->get('session'),
                            'FCI_SCHOOL_ID' => $schoolId,
                            'FCI_DEPT_ID' => $deptId,
                            'FCI_PROG_ID' => $progId,
                            // 'FCI_ADMISSION_TYPE' => $userId,
                            // 'FCI_FLAG' => $userId,

                            'FCI_FIRST_DUEDATE' => $first_installment_due_date,
                            'FCI_FIRST_VALIDDATE' => $first_installment_valid_date,
                            // 'FCI_FIRST_VISIBLEDATE' =>  $first_installment_visible_date,

                            'FCI_SECOND_DUEDATE' =>   $second_installment_due_date,
                            'FCI_SECOND_VALIDDATE' =>  $second_installment_valid_date,
                            // 'FCI_SECOND_VISIBLEDATE' =>  $second_installment_visible_date,

                            'FCI_THIRD_DUEDATE' => $third_installment_due_date,
                            'FCI_THIRD_VALIDDATE' => $third_installment_valid_date,
                            // 'FCI_THIRD_VISIBLEDATE' => $third_installment_visible_date,

                            'FCI_FOURTH_DUEDATE' => $forth_installment_due_date,
                            'FCI_FOURTH_VALIDDATE' => $forth_installment_valid_date,
                            // 'FCI_FOURTH_VISIBLEDATE' => $forth_installment_visible_date,

                            'FCI_FIFTH_DUEDATE' => $fifth_installment_due_date,
                            'FCI_FIFTH_VALIDDATE' => $fifth_installment_valid_date,
                            // 'FCI_FIFTH_VISIBLEDATE' => $fifth_installment_visible_date,

                            'FCI_STATUS' => 'E',
                            'FCI_ENTRY_USERID' => $userId,
                            'FCI_UPDATED_AT' => now('Asia/Karachi')->format('Y-m-d H:i:s')
                        ]);
                        // $resp[0]=true;
                        // $resp[1]='Updated Successfully';
                        // return $resp;
                        $resp[0] = true;
                        $resp[1] = 'Updated Successfully';
                    } catch (\Exception $e) {
                        $resp[0] = false;
                        $resp[1] = 'Uniqueness conflict occurred';

                        // Optionally log the exception for debugging
                        // Log::error('Error updating FIN_FEE_POLICY: ' . $e->getMessage());
                    }
                    return $resp;
                }else{
                    $resp[0]=false;
                    $resp[1]='Data already exists';
                    return $resp;
                }
            }

        }else{
            $resp[0]=false;
            $resp[1]='You can edit one program at a time';
            return $resp;
        }


    }
    public function getInstallmentDates(Request $request){
        $authz = $this->assertCanManageCmsFeeInstallments();
        if ($authz['success'] === false) {
            return response()->json($authz, 403);
        }

        // dd($request->all());
        $data = DB::table('fin_challan_installments as fci')
            ->select(
                    'fci.*',
                    'acd1.SESS_NAME as SESS_NAME', // Session name for FFS_SESS_ID
                    'school.SCHOOL_NAME',
                    'dept.DEPT_NAME',
                    'prog.PROG_NAME'
                    )
                    ->join('ACADEMIC_SESSIONS as acd1', 'acd1.SESS_ID', '=', 'fci.FCI_SESSION_ID')
                    ->join('SCHOOLS as school', 'school.SCHOOL_ID', 'fci.FCI_SCHOOL_ID')
                    ->join('DEPARTMENTS as dept', 'dept.DEPT_ID', 'fci.FCI_DEPT_ID')
                    ->join('PROGRAMS as prog', 'prog.PROG_ID', 'fci.FCI_PROG_ID')
                    ->where('fci.FCI_STATUS', '=', 'E')
                    ->where('fci.FCI_FLAG', '=', 'CMS') ;

                    // ->join('fin_fee_category as fincateg', 'fincateg.id', 'ffs.FFS_FEECATEGORY')
                    if(!empty($request->session_sel)){
                        $data =  $data->where('fci.FCI_SESSION_ID', $request->session_sel);
                    }
                    // if(!empty($request->student_type)){
                    //     $data =  $data->where('fci.FCI_STUDENT_TYPE', $request->student_type);
                    // }
                    if(!empty($request->school_sel) && !is_array(json_decode($request->school_sel))){//condition will applied only when single school is selected if all school is slected then no need to apply condition//array means all school is selected and so we apply not array condtion to apply where clause;
                    $data =  $data->where('fci.FCI_SCHOOL_ID', $request->school_sel);
                    }
                    if(!empty($request->department) && !is_array(json_decode($request->department))){//condition will applied only when single department is selected if all department is slected then no need to apply condition//array means all department is selected and so we apply not array condtion to apply where clause;
                        $data =  $data->where('fci.FCI_DEPT_ID', $request->department);
                    }
                    if(!empty($request->program)&& !is_array(json_decode($request->program))){//condition will applied only when single program is selected if all program is slected then no need to apply condition//array means all program is selected and so we apply not array condtion to apply where clause;
                    //    dd('program condtion applied');
                        $programs_id = $request->program;
                        // dd($programs_id);
                        $array_of_sch_dep_prog = explode('~~', $programs_id);
                        // dd($array_of_sch_dep_prog[2]);
                    $data =  $data->where('fci.FCI_PROG_ID', $array_of_sch_dep_prog[2]);
                    }
                    // dd('prgram condition not applied');
                    if(!empty($request->first_dueDate)){
                        $data =  $data->where('fci.FCI_FIRST_DUEDATE', $request->first_dueDate);
                    }
                    if(!empty($request->first_validDate)){
                        $data =  $data->where('fci.FCI_FIRST_VALIDDATE', $request->first_validDate);
                    }
                    if(!empty($request->second_dueDate)){
                        $data =  $data->where('fci.FCI_SECOND_DUEDATE', $request->second_dueDate);
                    }
                    if(!empty($request->second_validDate)){
                        $data =  $data->where('fci.FCI_SECOND_VALIDDATE', $request->second_validDate);
                    }
                    if(!empty($request->third_dueDate)){
                        $data =  $data->where('fci.FCI_THIRD_DUEDATE', $request->third_dueDate);
                    }
                    if(!empty($request->third_validDate)){
                        $data =  $data->where('fci.FCI_THIRD_VALIDDATE', $request->third_validDate);
                    }
                    if(!empty($request->forth_dueDate)){
                        $data =  $data->where('fci.FCI_FOURTH_DUEDATE', $request->forth_dueDate);
                    }
                    if(!empty($request->forth_validDate)){
                        $data =  $data->where('fci.FCI_FOURTH_VALIDDATE', $request->forth_validDate);
                    }
                    if(!empty($request->fifth_dueDate)){
                        $data =  $data->where('fci.FCI_FIFTH_DUEDATE', $request->fifth_dueDate);
                    }
                    if(!empty($request->fifth_validDate)){
                        $data =  $data->where('fci.FCI_FIFTH_VALIDDATE', $request->fifth_validDate);
                    }

                    $data =  $data->orderBy('fci.fci_id', 'desc')->get();

                    //exit;
                    // dd($data);
                    // $sql = "SELECT * from FIN_FEE_STRUCTURE";
                    // //echo $sql;exit;
                    // $res = DB::select($sql);
                    // dd($data);
        return DataTables::of($data)->toJson();
    }
    public function deleteInstallmentDates(Request $request){
        $authz = $this->assertCanManageCmsFeeInstallments();
        if ($authz['success'] === false) {
            return response()->json($authz, 403);
        }

        $user = auth()->user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'msg' => 'Unauthorized.',
            ], 403);
        }

        $ids = is_array($request->mainId) ? $request->mainId : [$request->mainId];
        $ids = array_values(array_filter($ids, function ($id) {
            return $id !== null && $id !== '';
        }));
        if (empty($ids)) {
            return response()->json([
                'success' => false,
                'data' => 'No installment record selected.',
            ], 400);
        }

        // Soft-delete only active CMS master rows (never Admission / other flags).
        $affectedRows = DB::table('FIN_CHALLAN_INSTALLMENTS')
                            ->whereIn('FCI_ID', $ids)
                            ->where('FCI_FLAG', 'CMS')
                            ->where('FCI_STATUS', 'E')
                            ->update([
                                'FCI_STATUS' => 'D',
                                'FCI_ENTRY_USERID' => $user->id,
                            ]);
        if ($affectedRows > 0) {
            return response()->json([
                'success' => true,
                'data' => 'Deleted Successfully'
        ]);
        } else {
            return response()->json([
                'success' => false,
                'data' => 'Some problem occured'
            ]);
        }

    }

    /**
     * Same allowlist as sidebar Master Installment Form (CMS).
     * Soft menu ACL is not enough for mass-update APIs.
     */
    private function assertCanManageCmsFeeInstallments()
    {
        $user = auth()->user();
        if (!$user) {
            return [
                'success' => false,
                'msg' => 'Unauthorized.',
            ];
        }

        $allowedUsernames = [
            'saqib.jamil',
            'esha.abid',
            'ahmad.arfeen',
            'asif_nadeem',
            'muhammad.nadeem',
            'qasim.ali',
        ];

        if ((int) $user->group_id === 644 || in_array($user->username, $allowedUsernames, true)) {
            return ['success' => true];
        }

        return [
            'success' => false,
            'msg' => 'You are not authorized to manage installment challan dates.',
        ];
    }

    /**
     * Same allowlist as sidebar Master Installment Form (Admission).
     */
    private function assertCanManageAdmissionFeeInstallments()
    {
        $user = auth()->user();
        if (!$user) {
            return [
                'success' => false,
                'msg' => 'Unauthorized.',
            ];
        }

        $allowedUsernames = [
            'sidra.tanveer',
            'muhammad.munim',
            'naureen.mirza',
            'maheen.james',
        ];

        if ((int) $user->group_id === 644 || in_array($user->username, $allowedUsernames, true)) {
            return ['success' => true];
        }

        return [
            'success' => false,
            'msg' => 'You are not authorized to manage admission installment dates.',
        ];
    }

    /** True when school filter is empty / "All" (session-wide blast radius). */
    private function isSessionWideChallanScope(Request $request)
    {
        if (empty($request->school)) {
            return true;
        }

        $decoded = json_decode($request->school, true);
        return is_array($decoded);
    }

    /** Normalize Oracle/PHP date values to a unix timestamp (date-only). */
    private function toYmdTimestamp($value)
    {
        if ($value === null || $value === '') {
            return null;
        }

        if ($value instanceof \DateTimeInterface) {
            return strtotime($value->format('Y-m-d'));
        }

        $s = trim((string) $value);
        if (preg_match('/^\d{4}-\d{2}-\d{2}/', $s)) {
            return strtotime(substr($s, 0, 10));
        }

        $ts = strtotime($s);
        return $ts === false ? null : $ts;
    }

    /**
     * Installment breakdown of UNPAID installment challans for the selected session scope.
     * Also returns count of unpaid challans with no installments (check_fee_installments null/0).
     */
    public function getUnpaidInstallmentScopeStats(Request $request)
    {
        $authz = $this->assertCanManageCmsFeeInstallments();
        if ($authz['success'] === false) {
            return response()->json($authz, 403);
        }

        $validator = Validator::make($request->all(), [
            'session' => 'required',
            'school' => 'nullable',
            'department' => 'nullable',
            'program' => 'nullable',
            'student_status' => 'nullable',
            'admission_round' => 'nullable|integer|in:1,2,3',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->getMessageBag()->toArray(),
                'msg' => 'Please select Session in Academic Scope first.',
            ], 400);
        }

        $query = DB::table('student_challan as sc')
            ->where('sc.sc_challan_status', 'UNPAID')
            ->where('sc.invoice_status', 'E')
            ->whereBetween('sc.check_fee_installments', [1, 5]);
        $this->applyChallanScopeFilters($query, $request);

        $rows = $query->select(
                'sc.check_fee_installments as inst_no',
                DB::raw('COUNT(*) as cnt'),
                DB::raw("TO_CHAR(MIN(sc.sc_due_date), 'YYYY-MM-DD') as min_due"),
                DB::raw("TO_CHAR(MAX(sc.sc_due_date), 'YYYY-MM-DD') as max_due")
            )
            ->groupBy('sc.check_fee_installments')
            ->orderBy('sc.check_fee_installments')
            ->get();

        $byInstallment = [];
        $maxInstallment = 0;
        $totalUnpaid = 0;

        foreach ($rows as $row) {
            $instNo = (int) $this->readRowValue($row, 'inst_no');
            if ($instNo < 1 || $instNo > 5) {
                continue;
            }

            $count = (int) $this->readRowValue($row, 'cnt');
            $totalUnpaid += $count;
            $byInstallment[(string) $instNo] = [
                'count' => $count,
                'min_due' => $this->readRowValue($row, 'min_due'),
                'max_due' => $this->readRowValue($row, 'max_due'),
            ];

            if ($instNo > $maxInstallment) {
                $maxInstallment = $instNo;
            }
        }

        // Without-installments: Full_semester + check_fee_installments null/0.
        // Same academic scope filters as installment rows so counts match what Update will touch.
        $noInstQuery = DB::table('student_challan as sc')
            ->where('sc.sc_challan_status', 'UNPAID')
            ->where('sc.invoice_status', 'E')
            ->whereRaw("UPPER(TRIM(sc.sc_challan_type)) = 'FULL_SEMESTER'")
            ->where(function ($q) {
                $q->whereNull('sc.check_fee_installments')
                    ->orWhere('sc.check_fee_installments', 0);
            });
        $this->applyChallanScopeFilters($noInstQuery, $request);

        $noInstRow = $noInstQuery->select(
                DB::raw('COUNT(*) as cnt'),
                DB::raw("TO_CHAR(MIN(sc.sc_due_date), 'YYYY-MM-DD') as min_due"),
                DB::raw("TO_CHAR(MAX(sc.sc_due_date), 'YYYY-MM-DD') as max_due")
            )
            ->first();

        $noInstallmentCount = (int) $this->readRowValue($noInstRow, 'cnt');
        if ($noInstallmentCount > 0) {
            $byInstallment['0'] = [
                'count' => $noInstallmentCount,
                'min_due' => $this->readRowValue($noInstRow, 'min_due'),
                'max_due' => $this->readRowValue($noInstRow, 'max_due'),
            ];
        }

        $sessionName = DB::table('academic_sessions')
            ->where('sess_id', $request->session)
            ->value('sess_name');

        return response()->json([
            'success' => true,
            'session_id' => $request->session,
            'session_name' => $sessionName,
            'max_installment' => $maxInstallment,
            'total_unpaid' => $totalUnpaid,
            'no_installment_count' => $noInstallmentCount,
            'by_installment' => $byInstallment,
        ]);
    }

    /** ADMISSION_SCH_ROUND 1/2/3 -> label shown in the Admission Type filter. */
    private static $admissionRoundLabels = [
        1 => 'Early Round',
        2 => 'Regular Round',
        3 => 'Rolling Round',
    ];

    /**
     * Distinct admission rounds available for the selected academic scope.
     * Narrows down by School / Department / Program the same way the challan
     * filters do: "All" selections arrive as JSON arrays and are skipped.
     */
    public function getAdmissionRounds(Request $request)
    {
        $authz = $this->assertCanManageCmsFeeInstallments();
        if ($authz['success'] === false) {
            return response()->json($authz, 403);
        }

        if (empty($request->session)) {
            return response()->json(['success' => true, 'rounds' => []]);
        }

        $query = DB::table('admission_schedules as ads')
            ->where('ads.admission_sch_sess_id', $request->session);

        if (!empty($request->school) && !is_array(json_decode($request->school))) {
            $query->where('ads.admission_sch_school_id', $request->school);
        }

        if (!empty($request->department) && !is_array(json_decode($request->department))) {
            $query->where('ads.admission_sch_dept_id', $request->department);
        }

        if (!empty($request->program) && !is_array(json_decode($request->program))) {
            $parts = explode('~~', $request->program);
            if (isset($parts[2]) && $parts[2] !== '') {
                $query->where('ads.admission_sch_prog_id', $parts[2]);
            }
        }

        $rows = $query->select('ads.admission_sch_round as round_no')
            ->distinct()
            ->orderBy('ads.admission_sch_round')
            ->get();

        $rounds = [];
        foreach ($rows as $row) {
            $roundNo = $this->readRowValue($row, 'round_no');
            if ($roundNo === null || $roundNo === '') {
                continue;
            }

            $roundNo = (int) $roundNo;
            if (!isset(self::$admissionRoundLabels[$roundNo])) {
                continue;
            }

            $rounds[] = [
                'value' => $roundNo,
                'label' => self::$admissionRoundLabels[$roundNo],
            ];
        }

        return response()->json(['success' => true, 'rounds' => $rounds]);
    }

    /**
     * Preview count of UNPAID installment challans that would get Due+Valid updated.
     * Does not modify any rows.
     */
    public function previewUpdateUnpaidInstallmentChallanDates(Request $request)
    {
        $authz = $this->assertCanManageCmsFeeInstallments();
        if ($authz['success'] === false) {
            return response()->json($authz, 403);
        }

        $validated = $this->validateUpdateUnpaidInstallmentChallanDatesRequest($request);
        if ($validated['success'] === false) {
            return response()->json($validated, 400);
        }

        $installmentNo = (int) $request->installment_no;

        // installment_no = 0 → Full_semester main challan (no installments). No sibling-order rules.
        if ($installmentNo === 0) {
            $matchedIds = $this->getMatchingUnpaidInstallmentChallanIds($request, 0);
            return response()->json([
                'success' => true,
                'matched_count' => count($matchedIds),
                'eligible_count' => count($matchedIds),
                'skipped_order_count' => 0,
                'msg' => count($matchedIds) . ' unpaid Full Semester (no installment) challan(s) can be updated.',
            ]);
        }

        $orderCheck = $this->assertLaterInstallmentsWillBeExtended($request, $installmentNo, $request->due_date);
        if ($orderCheck['success'] === false) {
            return response()->json($orderCheck, 400);
        }

        $matchedIds = $this->getMatchingUnpaidInstallmentChallanIds($request, $installmentNo);
        $eligibleIds = $this->filterChallanIdsByInstallmentDateOrder(
            $matchedIds,
            $installmentNo,
            $request->due_date,
            $request
        );

        $skipped = count($matchedIds) - count($eligibleIds);
        if ($skipped > 0) {
            return response()->json([
                'success' => false,
                'matched_count' => count($matchedIds),
                'eligible_count' => 0,
                'skipped_order_count' => $skipped,
                'must_update_installments' => $orderCheck['required_later'] ?? [],
                'msg' => 'Update blocked. Extending installment ' . $installmentNo
                    . ' would push it past a later installment for some students. '
                    . 'Also select and extend the later installment(s) (2nd/3rd/…) with dates on or after this new due date.',
            ], 400);
        }

        return response()->json([
            'success' => true,
            'matched_count' => count($matchedIds),
            'eligible_count' => count($eligibleIds),
            'skipped_order_count' => 0,
            'msg' => count($eligibleIds) . ' unpaid installment challan(s) can be updated.',
        ]);
    }

    /**
     * Update Due Date + Valid Date on UNPAID student_challan rows for a selected installment no.
     * installment_no = 0 updates Full_semester challans with no installments (null/0).
     * Existing Save / filter / master schedule flows are untouched.
     */
    public function updateUnpaidInstallmentChallanDates(Request $request)
    {
        $authz = $this->assertCanManageCmsFeeInstallments();
        if ($authz['success'] === false) {
            return response()->json($authz, 403);
        }

        $validated = $this->validateUpdateUnpaidInstallmentChallanDatesRequest($request);
        if ($validated['success'] === false) {
            return response()->json($validated, 400);
        }

        $installmentNo = (int) $request->installment_no;

        if ($installmentNo === 0) {
            $matchedIds = $this->getMatchingUnpaidInstallmentChallanIds($request, 0);
            if (empty($matchedIds)) {
                return response()->json([
                    'success' => false,
                    'msg' => 'No unpaid Full Semester (no installment) challans found for the selected filters.',
                ], 404);
            }

            $formattedDueDate = Carbon::createFromFormat('Y-m-d', $request->due_date)
                ->format('d-M-y h.i.s A');
            $formattedValidDate = Carbon::createFromFormat('Y-m-d', $request->valid_date)
                ->format('d-M-y h.i.s A');
            $username = auth()->user()->username ?? (string) auth()->user()->id;
            $dueYmd = $request->due_date;

            try {
                DB::beginTransaction();
                $updated = 0;
                foreach (array_chunk($matchedIds, 900) as $chunkIds) {
                    $updated += DB::table('student_challan')
                        ->whereIn('id', $chunkIds)
                        ->where('sc_challan_status', 'UNPAID')
                        ->where('invoice_status', 'E')
                        ->whereRaw("UPPER(TRIM(sc_challan_type)) = 'FULL_SEMESTER'")
                        ->where(function ($q) {
                            $q->whereNull('check_fee_installments')
                                ->orWhere('check_fee_installments', 0);
                        })
                        // Never pull an individual challan's due date backward
                        ->whereRaw("TRUNC(sc_due_date) <= TO_DATE('$dueYmd', 'YYYY-MM-DD')")
                        ->update([
                            'sc_due_date' => DB::raw("TO_DATE('$formattedDueDate', 'DD-MON-YY HH:MI:SS AM')"),
                            'sc_valid_date' => DB::raw("TO_DATE('$formattedValidDate', 'DD-MON-YY HH:MI:SS AM')"),
                            'SC_UPDATED_BY_USERNAME' => $username,
                            'UPDATED_AT' => now(),
                        ]);
                }
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                \Log::error('updateUnpaidInstallmentChallanDates (inst 0) failed: ' . $e->getMessage());
                return response()->json([
                    'success' => false,
                    'msg' => 'Failed to update challan dates. Please try again or contact support.',
                ], 500);
            }

            return response()->json([
                'success' => true,
                'matched_count' => count($matchedIds),
                'updated_count' => (int) $updated,
                'skipped_order_count' => 0,
                'msg' => $updated . ' unpaid Full Semester (no installment) challan(s) updated successfully.',
            ]);
        }

        $orderCheck = $this->assertLaterInstallmentsWillBeExtended($request, $installmentNo, $request->due_date);
        if ($orderCheck['success'] === false) {
            return response()->json($orderCheck, 400);
        }

        $matchedIds = $this->getMatchingUnpaidInstallmentChallanIds($request, $installmentNo);
        if (empty($matchedIds)) {
            return response()->json([
                'success' => false,
                'msg' => 'No unpaid installment challans found for the selected filters.',
            ], 404);
        }

        $eligibleIds = $this->filterChallanIdsByInstallmentDateOrder(
            $matchedIds,
            $installmentNo,
            $request->due_date,
            $request
        );

        // All-or-nothing: never partially update when later installments would be violated
        if (count($eligibleIds) !== count($matchedIds)) {
            return response()->json([
                'success' => false,
                'matched_count' => count($matchedIds),
                'updated_count' => 0,
                'skipped_order_count' => count($matchedIds) - count($eligibleIds),
                'must_update_installments' => $orderCheck['required_later'] ?? [],
                'msg' => 'Update blocked. New due date for installment ' . $installmentNo
                    . ' is after a later installment date for some students. '
                    . 'Also extend 2nd/3rd (and further) installment dates, otherwise no challan will be updated.',
            ], 400);
        }

        if (empty($eligibleIds)) {
            return response()->json([
                'success' => false,
                'matched_count' => count($matchedIds),
                'updated_count' => 0,
                'msg' => 'No challans updated. New due date violates installment order.',
            ], 400);
        }

        $formattedDueDate = Carbon::createFromFormat('Y-m-d', $request->due_date)
            ->format('d-M-y h.i.s A');
        $formattedValidDate = Carbon::createFromFormat('Y-m-d', $request->valid_date)
            ->format('d-M-y h.i.s A');

        $username = auth()->user()->username ?? (string) auth()->user()->id;
        $dueYmd = $request->due_date;

        try {
            DB::beginTransaction();
            $updated = 0;
            foreach (array_chunk($eligibleIds, 900) as $chunkIds) {
                $updated += DB::table('student_challan')
                    ->whereIn('id', $chunkIds)
                    ->where('sc_challan_status', 'UNPAID')
                    ->where('invoice_status', 'E')
                    ->where('check_fee_installments', $installmentNo)
                    ->whereRaw("TRUNC(sc_due_date) <= TO_DATE('$dueYmd', 'YYYY-MM-DD')")
                    ->update([
                        'sc_due_date' => DB::raw("TO_DATE('$formattedDueDate', 'DD-MON-YY HH:MI:SS AM')"),
                        'sc_valid_date' => DB::raw("TO_DATE('$formattedValidDate', 'DD-MON-YY HH:MI:SS AM')"),
                        'SC_UPDATED_BY_USERNAME' => $username,
                        'UPDATED_AT' => now(),
                    ]);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('updateUnpaidInstallmentChallanDates failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'msg' => 'Failed to update challan dates. Please try again or contact support.',
            ], 500);
        }

        return response()->json([
            'success' => true,
            'matched_count' => count($matchedIds),
            'updated_count' => (int) $updated,
            'skipped_order_count' => 0,
            'msg' => $updated . ' unpaid installment challan(s) updated successfully.',
        ]);
    }

    /**
     * If new due for installment N is after an existing unpaid later installment due in this scope,
     * the request must also provide a new due date for that later installment (>= new due).
     * Otherwise block (do not partially update).
     */
    private function assertLaterInstallmentsWillBeExtended(Request $request, $installmentNo, $newDueDate)
    {
        if ((int) $installmentNo === 0) {
            return [
                'success' => true,
                'required_later' => [],
            ];
        }

        $neighborKeys = [
            1 => 'first_installment_due_date',
            2 => 'second_installment_due_date',
            3 => 'third_installment_due_date',
            4 => 'forth_installment_due_date',
            5 => 'fifth_installment_due_date',
        ];
        $labels = [1 => '1st', 2 => '2nd', 3 => '3rd', 4 => '4th', 5 => '5th'];
        $newDueTs = $this->toYmdTimestamp($newDueDate);
        $requiredLater = [];

        for ($later = $installmentNo + 1; $later <= 5; $later++) {
            $q = DB::table('student_challan as sc')
                ->where('sc.sc_challan_status', 'UNPAID')
                ->where('sc.invoice_status', 'E')
                ->where('sc.check_fee_installments', $later);
            $this->applyChallanScopeFilters($q, $request);

            $minRow = $q->select(DB::raw("TO_CHAR(MIN(sc.sc_due_date), 'YYYY-MM-DD') as min_due"))->first();
            $minDue = $this->readRowValue($minRow, 'min_due');
            if (empty($minDue)) {
                continue;
            }

            $minDueTs = $this->toYmdTimestamp($minDue);
            // New earlier due is still on/before this later installment's earliest due — OK
            if ($newDueTs === null || $minDueTs === null || $newDueTs <= $minDueTs) {
                continue;
            }

            $laterKey = $neighborKeys[$later];
            $laterFormDue = $request->filled($laterKey) ? $request->get($laterKey) : null;
            $laterFormTs = $this->toYmdTimestamp($laterFormDue);

            // Later installment must also be selected/extended with due >= this new due
            if ($laterFormTs === null || $laterFormTs < $newDueTs) {
                $requiredLater[] = $later;
            }
        }

        if (!empty($requiredLater)) {
            $names = array_map(function ($n) use ($labels) {
                return $labels[$n] . ' Installment';
            }, $requiredLater);

            return [
                'success' => false,
                'must_update_installments' => $requiredLater,
                'msg' => 'Installment ' . $installmentNo . ' due date is being extended beyond existing later installment date(s). '
                    . 'You must also select and extend: ' . implode(', ', $names)
                    . ' (new due dates must be on or after ' . $newDueDate . '). Update cancelled.',
            ];
        }

        return [
            'success' => true,
            'required_later' => [],
        ];
    }

    private function validateUpdateUnpaidInstallmentChallanDatesRequest(Request $request)
    {
        $today = Carbon::now('Asia/Karachi')->toDateString();

        // Due date may be in the past. Valid date must be today or later, and on/after due date.
        $validator = Validator::make($request->all(), [
            'session' => 'required',
            'school' => 'nullable',
            'department' => 'nullable',
            'program' => 'nullable',
            'student_status' => 'nullable',
            'admission_round' => 'nullable|integer|in:1,2,3',
            'installment_no' => 'required|integer|min:0|max:5',
            'due_date' => 'required|date_format:Y-m-d',
            'valid_date' => 'required|date_format:Y-m-d|after_or_equal:' . $today . '|after_or_equal:due_date',
            'confirm_session_wide' => 'nullable|in:0,1',
        ], [
            'valid_date.after_or_equal' => 'New Valid Date cannot be earlier than today (' . $today . ') or the New Due Date.',
            'due_date.date_format' => 'New Due Date must be a valid date in YYYY-MM-DD format.',
            'valid_date.date_format' => 'New Valid Date must be a valid date in YYYY-MM-DD format.',
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'errors' => $validator->getMessageBag()->toArray(),
            ];
        }

        // Do not allow New Due earlier than the earliest unpaid due currently in scope.
        $installmentNoForFloor = (int) $request->installment_no;
        $floorQuery = DB::table('student_challan as sc')
            ->where('sc.sc_challan_status', 'UNPAID')
            ->where('sc.invoice_status', 'E');
        if ($installmentNoForFloor === 0) {
            $floorQuery->whereRaw("UPPER(TRIM(sc.sc_challan_type)) = 'FULL_SEMESTER'")
                ->where(function ($q) {
                    $q->whereNull('sc.check_fee_installments')
                        ->orWhere('sc.check_fee_installments', 0);
                });
        } else {
            $floorQuery->where('sc.check_fee_installments', $installmentNoForFloor);
        }
        $this->applyChallanScopeFilters($floorQuery, $request);
        $minDueRow = $floorQuery->select(DB::raw("TO_CHAR(MIN(sc.sc_due_date), 'YYYY-MM-DD') as min_due"))->first();
        $minDueFloor = $this->readRowValue($minDueRow, 'min_due');
        if (!empty($minDueFloor) && (string) $request->due_date < (string) $minDueFloor) {
            return [
                'success' => false,
                'msg' => 'New Due Date cannot be earlier than current due date in scope (' . $minDueFloor . ').',
            ];
        }

        // Session-wide mass update needs explicit confirm (school empty / All)
        if ($this->isSessionWideChallanScope($request) && (string) $request->get('confirm_session_wide') !== '1') {
            return [
                'success' => false,
                'needs_session_wide_confirm' => true,
                'msg' => 'School is not selected. This would update unpaid challans across the entire session. Confirm session-wide update to continue.',
            ];
        }

        // Neighbor installment dates from form (if provided) — 1st cannot be after 2nd, etc.
        // Skip for installment_no = 0 (Full Semester without installments).
        $installmentNo = (int) $request->installment_no;
        if ($installmentNo === 0) {
            return ['success' => true];
        }
        $newDue = $this->toYmdTimestamp($request->due_date);
        $neighborKeys = [
            1 => 'first_installment_due_date',
            2 => 'second_installment_due_date',
            3 => 'third_installment_due_date',
            4 => 'forth_installment_due_date',
            5 => 'fifth_installment_due_date',
        ];

        if ($installmentNo > 1) {
            $prevKey = $neighborKeys[$installmentNo - 1];
            if ($request->filled($prevKey)) {
                $prevDue = $this->toYmdTimestamp($request->get($prevKey));
                if ($prevDue !== null && $newDue !== null && $newDue < $prevDue) {
                    return [
                        'success' => false,
                        'msg' => "Installment {$installmentNo} due date cannot be earlier than installment " . ($installmentNo - 1) . " due date.",
                    ];
                }
            }
        }

        if ($installmentNo < 5) {
            $nextKey = $neighborKeys[$installmentNo + 1];
            if ($request->filled($nextKey)) {
                $nextDue = $this->toYmdTimestamp($request->get($nextKey));
                if ($nextDue !== null && $newDue !== null && $newDue > $nextDue) {
                    return [
                        'success' => false,
                        'msg' => "Installment {$installmentNo} due date cannot be later than installment " . ($installmentNo + 1) . " due date.",
                    ];
                }
            }
        }

        return ['success' => true];
    }

    private function getMatchingUnpaidInstallmentChallanIds(Request $request, $installmentNo)
    {
        $installmentNo = (int) $installmentNo;

        // 0 = Full_semester main challan (no installment split)
        if ($installmentNo === 0) {
            $query = DB::table('student_challan as sc')
                ->where('sc.sc_challan_status', 'UNPAID')
                ->where('sc.invoice_status', 'E')
                ->whereRaw("UPPER(TRIM(sc.sc_challan_type)) = 'FULL_SEMESTER'")
                ->where(function ($q) {
                    $q->whereNull('sc.check_fee_installments')
                        ->orWhere('sc.check_fee_installments', 0);
                });
            $this->applyChallanScopeFilters($query, $request);

            if ($request->filled('due_date') && preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $request->due_date)) {
                $dueYmd = $request->due_date;
                $query->whereRaw("TRUNC(sc.sc_due_date) <= TO_DATE('$dueYmd', 'YYYY-MM-DD')");
            }

            return $query->pluck('sc.id')->map(function ($id) {
                return (int) $id;
            })->all();
        }

        $query = DB::table('student_challan as sc')
            ->where('sc.sc_challan_status', 'UNPAID')
            ->where('sc.invoice_status', 'E')
            ->where('sc.check_fee_installments', $installmentNo);
        $this->applyChallanScopeFilters($query, $request);

        if ($request->filled('due_date') && preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $request->due_date)) {
            $dueYmd = $request->due_date;
            $query->whereRaw("TRUNC(sc.sc_due_date) <= TO_DATE('$dueYmd', 'YYYY-MM-DD')");
        }

        return $query->pluck('sc.id')->map(function ($id) {
            return (int) $id;
        })->all();
    }

    /**
     * Session / School / Dept / Program / Student Type / Admission Round filters
     * shared by stats, preview and update.
     * "All" selections arrive as JSON arrays and intentionally skip the filter.
     */
    private function applyChallanScopeFilters($query, Request $request)
    {
        $query->where('sc.sc_session_id', $request->session);

        if (!empty($request->student_status)) {
            $query->where('sc.sc_student_status', $request->student_status);
        }

        // Admission round lives on the applicant's schedule, not on the challan.
        // EXISTS keeps this a semi-join so COUNT/MIN/MAX cannot be inflated.
        if (!empty($request->admission_round)) {
            $round = (int) $request->admission_round;
            $query->whereExists(function ($sub) use ($round) {
                $sub->select(DB::raw(1))
                    ->from('admission_apply_info as aai')
                    ->join('admission_schedules as ads', 'ads.admission_sch_id', '=', 'aai.aai_admission_sch_id')
                    ->whereColumn('aai.aai_id', 'sc.sc_app_id')
                    ->where('ads.admission_sch_round', $round);
            });
        }

        if (!empty($request->school) && !is_array(json_decode($request->school))) {
            $query->where('sc.sc_school_id', $request->school);
        }

        if (!empty($request->department) && !is_array(json_decode($request->department))) {
            $query->where('sc.sc_department_id', $request->department);
        }

        if (!empty($request->program) && !is_array(json_decode($request->program))) {
            $parts = explode('~~', $request->program);
            if (isset($parts[2]) && $parts[2] !== '') {
                $query->where('sc.sc_program_id', $parts[2]);
            }
        }

        return $query;
    }

    /** Oracle driver can return column keys in either case. */
    private function readRowValue($row, $column)
    {
        if (isset($row->{$column})) {
            return $row->{$column};
        }

        $upper = strtoupper($column);
        return isset($row->{$upper}) ? $row->{$upper} : null;
    }

    /**
     * Keep only challans whose new due date respects sibling installment order
     * (prev installment due <= new due <= next installment due) for same app+session.
     * When the form also provides planned neighbor dues (multi-select update), those
     * planned dates take precedence over live DB sibling dues so coordinated extends work.
     */
    private function filterChallanIdsByInstallmentDateOrder(array $challanIds, $installmentNo, $newDueDate, ?Request $request = null)
    {
        if (empty($challanIds)) {
            return [];
        }
        if ((int) $installmentNo === 0) {
            return $challanIds;
        }

        $newDueTs = $this->toYmdTimestamp($newDueDate);
        if ($newDueTs === null) {
            return [];
        }

        $neighborKeys = [
            1 => 'first_installment_due_date',
            2 => 'second_installment_due_date',
            3 => 'third_installment_due_date',
            4 => 'forth_installment_due_date',
            5 => 'fifth_installment_due_date',
        ];

        $plannedPrevTs = null;
        $plannedNextTs = null;
        if ($request) {
            if ($installmentNo > 1 && $request->filled($neighborKeys[$installmentNo - 1])) {
                $plannedPrevTs = $this->toYmdTimestamp($request->get($neighborKeys[$installmentNo - 1]));
            }
            if ($installmentNo < 5 && $request->filled($neighborKeys[$installmentNo + 1])) {
                $plannedNextTs = $this->toYmdTimestamp($request->get($neighborKeys[$installmentNo + 1]));
            }
        }

        // Planned neighbors already validated as a set — no need to re-check live DB siblings.
        if ($plannedPrevTs !== null && $newDueTs < $plannedPrevTs) {
            return [];
        }
        if ($plannedNextTs !== null && $newDueTs > $plannedNextTs) {
            return [];
        }
        if ($plannedPrevTs !== null && $plannedNextTs !== null) {
            return $challanIds;
        }

        $rows = collect();
        foreach (array_chunk($challanIds, 900) as $chunkIds) {
            $chunkRows = DB::table('student_challan')
                ->whereIn('id', $chunkIds)
                ->select('id', 'sc_app_id', 'sc_session_id')
                ->get();
            $rows = $rows->merge($chunkRows);
        }

        if ($rows->isEmpty()) {
            return [];
        }

        $appIds = $rows->pluck('sc_app_id')->unique()->filter()->values()->all();
        $sessionIds = $rows->pluck('sc_session_id')->unique()->filter()->values()->all();

        $prevMap = [];
        $nextMap = [];

        // Only load DB prev when planned prev was NOT provided
        if ($plannedPrevTs === null && $installmentNo > 1 && !empty($appIds) && !empty($sessionIds)) {
            foreach (array_chunk($appIds, 900) as $appChunk) {
                $prevRows = DB::table('student_challan')
                    ->select(
                        'sc_app_id',
                        'sc_session_id',
                        DB::raw("TO_CHAR(MAX(sc_due_date), 'YYYY-MM-DD') as max_due")
                    )
                    ->whereIn('sc_app_id', $appChunk)
                    ->whereIn('sc_session_id', $sessionIds)
                    ->where('check_fee_installments', $installmentNo - 1)
                    ->where('invoice_status', 'E')
                    ->whereRaw("UPPER(TRIM(sc_challan_status)) IN ('PAID', 'UNPAID')")
                    ->groupBy('sc_app_id', 'sc_session_id')
                    ->get();

                foreach ($prevRows as $p) {
                    $prevMap[$p->sc_app_id . '|' . $p->sc_session_id] = $this->readRowValue($p, 'max_due');
                }
            }
        }

        // Only load DB next when planned next was NOT provided
        if ($plannedNextTs === null && $installmentNo < 5 && !empty($appIds) && !empty($sessionIds)) {
            foreach (array_chunk($appIds, 900) as $appChunk) {
                $nextRows = DB::table('student_challan')
                    ->select(
                        'sc_app_id',
                        'sc_session_id',
                        DB::raw("TO_CHAR(MAX(sc_due_date), 'YYYY-MM-DD') as max_due")
                    )
                    ->whereIn('sc_app_id', $appChunk)
                    ->whereIn('sc_session_id', $sessionIds)
                    ->where('check_fee_installments', $installmentNo + 1)
                    ->where('invoice_status', 'E')
                    ->whereRaw("UPPER(TRIM(sc_challan_status)) IN ('PAID', 'UNPAID')")
                    ->groupBy('sc_app_id', 'sc_session_id')
                    ->get();

                foreach ($nextRows as $n) {
                    $nextMap[$n->sc_app_id . '|' . $n->sc_session_id] = $this->readRowValue($n, 'max_due');
                }
            }
        }

        $eligible = [];
        foreach ($rows as $row) {
            $key = $row->sc_app_id . '|' . $row->sc_session_id;
            $ok = true;

            if ($plannedPrevTs === null && $installmentNo > 1 && !empty($prevMap[$key])) {
                $prevTs = $this->toYmdTimestamp($prevMap[$key]);
                if ($prevTs !== null && $newDueTs < $prevTs) {
                    $ok = false;
                }
            }

            if ($ok && $plannedNextTs === null && $installmentNo < 5 && !empty($nextMap[$key])) {
                $nextTs = $this->toYmdTimestamp($nextMap[$key]);
                if ($nextTs !== null && $newDueTs > $nextTs) {
                    $ok = false;
                }
            }

            if ($ok) {
                $eligible[] = (int) $row->id;
            }
        }

        return $eligible;
    }

    ////////////////////ADMISSION///////////////////////
    public function installmentForm_admission(){
        $authz = $this->assertCanManageAdmissionFeeInstallments();
        if ($authz['success'] === false) {
            abort(403, $authz['msg']);
        }

        $sessions = \App::call('App\Http\Controllers\Grader\AcademicSessionController@retrieve');
        $schools = Schools::whereNotIn('school_id', [22, 10, 9, 8])->get();
        $departments = array(); // $this->school->fetch();
        $facultyCourses = \App::call('App\Http\Controllers\Grader\FacultyCoursesController@retrieve');
        $offeredFaculty = \App::call('App\Http\Controllers\Grader\CoursesOfferedController@retrieveFaculty');
        $programs =  DB::select("select * from Programs where PROG_STATUS = 'E'");
        $query = 'select * from fin_fee_category';
        $feeCategories = DB::select($query);
        return view('StudentFinancial.feeInstallment_admission',compact('sessions','schools','departments','facultyCourses','offeredFaculty','feeCategories','programs'));

    }
    public function addInstallment_admission(Request $request){
        $authz = $this->assertCanManageAdmissionFeeInstallments();
        if ($authz['success'] === false) {
            return response()->json($authz, 403);
        }

        // dd($request->all());
        $validator = Validator::make($request->all(),[
            'session' => 'required',
            'school' => 'required',
            'department' => 'required',
            'program' => 'required',
            'student_type' => 'required',
            'admission_type' => 'required',
            'first_installment_due_date' => 'required|date',
            'first_installment_valid_date' => 'required|date',
            'second_installment_due_date' => 'required|date',
            'second_installment_valid_date' => 'required|date',
            'third_installment_due_date' => 'required|date',
            'third_installment_valid_date' => 'required|date',
            'forth_installment_due_date' => 'nullable|date',
            'forth_installment_valid_date' => 'nullable|date',
            'fifth_installment_due_date' => 'nullable|date',
            'fifth_installment_valid_date' => 'nullable|date',
        ]);
        if($validator->fails()) {
            return Response::json(array(
                'success' => false,
                'errors' => $validator->getMessageBag()->toArray()
            ), 400);
            // return response()->json(['success' => true, 'count'=> $generatdChallan_count]);
            // return response()->json(['success' => false, 'messege'=> 'Something went wrong. (Duplicate entries not allowed)', 'errors' => $e->getMessage()], 500);
        }
        ///logic: next installment due must be after previous installment valid date;
        /// and each installment valid date must be on/after its own due date.
        $installments = [
            '1st' => [
                'due' => $request->get('first_installment_due_date'),
                'valid' => $request->get('first_installment_valid_date'),
            ],
            '2nd' => [
                'due' => $request->get('second_installment_due_date'),
                'valid' => $request->get('second_installment_valid_date'),
            ],
            '3rd' => [
                'due' => $request->get('third_installment_due_date'),
                'valid' => $request->get('third_installment_valid_date'),
            ],
            '4th' => [
                'due' => $request->get('forth_installment_due_date'),
                'valid' => $request->get('forth_installment_valid_date'),
            ],
            '5th' => [
                'due' => $request->get('fifth_installment_due_date'),
                'valid' => $request->get('fifth_installment_valid_date'),
            ],
        ];

        $errors = [];
        $prevValidTs = null;
        $prevLabel = null;
        foreach ($installments as $label => $dates) {
            $due = !empty($dates['due']) ? strtotime($dates['due']) : null;
            $valid = !empty($dates['valid']) ? strtotime($dates['valid']) : null;

            if ($due === null && $valid === null) {
                continue;
            }
            if ($due !== null && $valid === null) {
                $errors['due_date'][] = "The {$label} installment valid date is required when due date is set.";
                continue;
            }
            if ($due === null && $valid !== null) {
                $errors['due_date'][] = "The {$label} installment due date is required when valid date is set.";
                continue;
            }
            if ($valid < $due) {
                $errors['due_date'][] = "The {$label} installment valid date must be on or after its due date.";
            }
            if ($prevValidTs !== null && $due <= $prevValidTs) {
                $errors['due_date'][] = "The {$label} installment due date must be after the {$prevLabel} installment valid date.";
            }
            $prevValidTs = $valid;
            $prevLabel = $label;
        }

        if (!empty($errors)) {
            return Response::json([
                'success' => false,
                'errors' => $errors,
            ], 400);
        }
        ///////////////////////////
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
        $resp = $this->allProgramsInsertion_adm($school_depart_prog, $request);
        $payload = [
            'success' => $resp[0],
            'msg' => $resp[1],
        ];
        foreach (['has_challan', 'paid_count', 'unpaid_count', 'total_count', 'locked_ids'] as $key) {
            if (array_key_exists($key, $resp)) {
                $payload[$key] = $resp[$key];
            }
        }
        return response()->json($payload);
    }
    private function allProgramsInsertion_adm($school_depart_prog, $request){
        // dd($request->all());
        $userId = auth()->user()->id;
        $resp = [];
        $first_installment_due_date = $request->get('first_installment_due_date');
        $first_installment_valid_date = $request->get('first_installment_valid_date');
        // $first_installment_visible_date = !empty($request->get('first_installment_visible_date'))? $request->get('first_installment_visible_date') : Carbon::now('Asia/Karachi');

        $second_installment_due_date = $request->get('second_installment_due_date');
        $second_installment_valid_date = $request->get('second_installment_valid_date');
        // $second_installment_visible_date = !empty($request->get('second_installment_visible_date'))? $request->get('second_installment_visible_date') : Carbon::now('Asia/Karachi');

        $third_installment_due_date = $request->get('third_installment_due_date');
        $third_installment_valid_date = $request->get('third_installment_valid_date');
        // $third_installment_visible_date = !empty($request->get('third_installment_visible_date'))? $request->get('third_installment_visible_date') : Carbon::now('Asia/Karachi');

        $forth_installment_due_date = $request->get('forth_installment_due_date');
        $forth_installment_valid_date = $request->get('forth_installment_valid_date');
        // $forth_installment_visible_date = !empty($request->get('forth_installment_visible_date'))? $request->get('forth_installment_visible_date') : Carbon::now('Asia/Karachi');

        $fifth_installment_due_date = $request->get('fifth_installment_due_date');
        $fifth_installment_valid_date = $request->get('fifth_installment_valid_date');
        // $fifth_installment_visible_date = !empty($request->get('fifth_installment_visible_date'))? $request->get('fifth_installment_visible_date') : Carbon::now('Asia/Karachi');
        // dd($tuitionFee, $admissionFee, $securityFee, $medicalFee, $credithrFee);
        if(empty($request->mainId)){//insertion case
            $savedCount = 0;
            $challanBlockedCount = 0;
            $lockedIds = [];
            $totalPaid = 0;
            $totalUnpaid = 0;
            foreach($school_depart_prog as $val){
                $schoolId = $val[0];
                $deptId = $val[1];
                $progId = $val[2];
                $duplicateCheck = DB::table('fin_challan_installments')
                                ->select(
                                        'FCI_ID',
                                        'FCI_SESSION_ID',
                                        'FCI_SCHOOL_ID',
                                        'FCI_DEPT_ID',
                                        'FCI_PROG_ID',
                                        'FCI_ADMISSION_TYPE',
                                        'FCI_STUDENT_TYPE',
                                        'FCI_FLAG',
                                        )
                                        ->where('FCI_SESSION_ID', $request->get('session'))
                                        ->where('FCI_SCHOOL_ID', $schoolId)
                                        ->where('FCI_DEPT_ID', $deptId)
                                        ->where('FCI_PROG_ID', $progId)
                                        ->where('FCI_STATUS', 'E')
                                        ->where('FCI_STUDENT_TYPE', $request->get('student_type'))
                                        ->where('FCI_ADMISSION_TYPE', $request->get('admission_type'))
                                        ->where('FCI_FLAG', 'Admission')
                                        ->get();
                                    //  dd($duplicateCheck);
                if(sizeof($duplicateCheck) == 0 ){
                    DB::table('fin_challan_installments')->insert([
                        'FCI_SESSION_ID' => $request->get('session'),
                        'FCI_SCHOOL_ID' => $schoolId,
                        'FCI_DEPT_ID' => $deptId,
                        'FCI_PROG_ID' => $progId,
                        // 'FCI_ADMISSION_TYPE' => $userId,
                        // 'FCI_FLAG' => $userId,

                        'FCI_FIRST_DUEDATE' => $first_installment_due_date,
                        'FCI_FIRST_VALIDDATE' => $first_installment_valid_date,
                        // 'FCI_FIRST_VISIBLEDATE' =>  $first_installment_visible_date,

                        'FCI_SECOND_DUEDATE' =>   $second_installment_due_date,
                        'FCI_SECOND_VALIDDATE' =>  $second_installment_valid_date,
                        // 'FCI_SECOND_VISIBLEDATE' =>  $second_installment_visible_date,

                        'FCI_THIRD_DUEDATE' => $third_installment_due_date,
                        'FCI_THIRD_VALIDDATE' => $third_installment_valid_date,
                        // 'FCI_THIRD_VISIBLEDATE' => $third_installment_visible_date,

                        'FCI_FOURTH_DUEDATE' => $forth_installment_due_date,
                        'FCI_FOURTH_VALIDDATE' => $forth_installment_valid_date,
                        // 'FCI_FOURTH_VISIBLEDATE' => $forth_installment_visible_date,

                        'FCI_FIFTH_DUEDATE' => $fifth_installment_due_date,
                        'FCI_FIFTH_VALIDDATE' => $fifth_installment_valid_date,
                        // 'FCI_FIFTH_VISIBLEDATE' => $fifth_installment_visible_date,

                        'FCI_STUDENT_TYPE' => $request->get('student_type'),
                        'FCI_ADMISSION_TYPE' => $request->get('admission_type'),
                        'FCI_FLAG' => 'Admission',
                        'FCI_STATUS' => 'E',
                        'FCI_ENTRY_USERID' => $userId,
                        'FCI_CREATED_AT' => now('Asia/Karachi')->format('Y-m-d H:i:s')
                    ]);
                    $savedCount++;
                }else{
                    $existingId = $duplicateCheck[0]->fci_id;
                    $challanSummary = $this->getInstallmentAdmissionChallanSummary($existingId);
                    if (!empty($challanSummary['has_challan'])) {
                        $challanBlockedCount++;
                        $lockedIds[] = $existingId;
                        $totalPaid += $challanSummary['paid_count'];
                        $totalUnpaid += $challanSummary['unpaid_count'];
                        continue;
                    }
                    DB::table('fin_challan_installments')
                    ->where('FCI_ID', $existingId)
                    ->update([
                        'FCI_SESSION_ID' => $request->get('session'),
                        'FCI_SCHOOL_ID' => $schoolId,
                        'FCI_DEPT_ID' => $deptId,
                        'FCI_PROG_ID' => $progId,
                        // 'FCI_ADMISSION_TYPE' => $userId,
                        // 'FCI_FLAG' => $userId,

                        'FCI_FIRST_DUEDATE' => $first_installment_due_date,
                        'FCI_FIRST_VALIDDATE' => $first_installment_valid_date,
                        // 'FCI_FIRST_VISIBLEDATE' =>  $first_installment_visible_date,

                        'FCI_SECOND_DUEDATE' =>   $second_installment_due_date,
                        'FCI_SECOND_VALIDDATE' =>  $second_installment_valid_date,
                        // 'FCI_SECOND_VISIBLEDATE' =>  $second_installment_visible_date,

                        'FCI_THIRD_DUEDATE' => $third_installment_due_date,
                        'FCI_THIRD_VALIDDATE' => $third_installment_valid_date,
                        // 'FCI_THIRD_VISIBLEDATE' => $third_installment_visible_date,

                        'FCI_FOURTH_DUEDATE' => $forth_installment_due_date,
                        'FCI_FOURTH_VALIDDATE' => $forth_installment_valid_date,
                        // 'FCI_FOURTH_VISIBLEDATE' => $forth_installment_visible_date,

                        'FCI_FIFTH_DUEDATE' => $fifth_installment_due_date,
                        'FCI_FIFTH_VALIDDATE' => $fifth_installment_valid_date,
                        // 'FCI_FIFTH_VISIBLEDATE' => $fifth_installment_visible_date,

                        'FCI_STUDENT_TYPE' => $request->get('student_type'),
                        'FCI_ADMISSION_TYPE' => $request->get('admission_type'),
                        'FCI_FLAG' => 'Admission',
                        'FCI_STATUS' => 'E',
                        'FCI_ENTRY_USERID' => $userId,
                        'FCI_UPDATED_AT' => now('Asia/Karachi')->format('Y-m-d H:i:s')
                    ]);
                    $savedCount++;
                }

            }
            if ($savedCount > 0 && $challanBlockedCount === 0) {
                $resp[0] = true;
                $resp[1] = 'Saved Successfully';
            } elseif ($savedCount > 0) {
                $resp[0] = true;
                $resp[1] = 'Saved/updated '.$savedCount.' record(s). '.$challanBlockedCount.' record(s) could not be overwritten because challan(s) already exist.';
                $resp['has_challan'] = true;
                $resp['paid_count'] = $totalPaid;
                $resp['unpaid_count'] = $totalUnpaid;
                $resp['total_count'] = $totalPaid + $totalUnpaid;
                $resp['locked_ids'] = $lockedIds;
            } elseif ($challanBlockedCount > 0) {
                $resp[0] = false;
                $resp[1] = 'Overwrite not allowed. Challan(s) already exist against selected installment setup(s).';
                $resp['has_challan'] = true;
                $resp['paid_count'] = $totalPaid;
                $resp['unpaid_count'] = $totalUnpaid;
                $resp['total_count'] = $totalPaid + $totalUnpaid;
                $resp['locked_ids'] = $lockedIds;
            } else {
                $resp[0] = true;
                $resp[1] = 'Saved Successfully';
            }
            return $resp;

        }else if(sizeof($school_depart_prog) == 1){ //edit case
            if(!empty($school_depart_prog[0][0])){//checking if the allprograms option is selected and there is no program in it
                $schoolId = $school_depart_prog[0][0];
                $deptId = $school_depart_prog[0][1];
                $progId = $school_depart_prog[0][2];

                if ($this->installmentAdmissionHasChallanById($request->mainId)) {
                    $challanSummary = $this->getInstallmentAdmissionChallanSummary($request->mainId);
                    $resp[0] = false;
                    $resp[1] = 'Cannot edit: challan(s) already exist against this installment setup';
                    $resp['has_challan'] = true;
                    $resp['paid_count'] = $challanSummary['paid_count'];
                    $resp['unpaid_count'] = $challanSummary['unpaid_count'];
                    $resp['total_count'] = $challanSummary['paid_count'] + $challanSummary['unpaid_count'];
                    $resp['locked_ids'] = [$request->mainId];
                    return $resp;
                }

                $duplicateCheck = DB::table('fin_challan_installments')
                                ->select(
                                        'FCI_ID',
                                        'FCI_SESSION_ID',
                                        'FCI_SCHOOL_ID',
                                        'FCI_DEPT_ID',
                                        'FCI_PROG_ID',
                                        'FCI_ADMISSION_TYPE',
                                        'FCI_STUDENT_TYPE',
                                        'FCI_FLAG',
                                        )
                                        ->where('FCI_SESSION_ID', $request->get('session'))
                                        ->where('FCI_SCHOOL_ID', $schoolId)
                                        ->where('FCI_DEPT_ID', $deptId)
                                        ->where('FCI_PROG_ID', $progId)
                                        ->where('FCI_STATUS', 'E')
                                        ->where('FCI_STUDENT_TYPE', $request->get('student_type'))
                                        ->where('FCI_ADMISSION_TYPE', $request->get('admission_type'))
                                        ->where('FCI_FLAG', 'Admission')
                                        ->where('FCI_ID', '!=', $request->mainId)
                                        ->get();
                        //  dd($duplicateCheck);
                if(sizeof($duplicateCheck) == 0 ){
                    try {
                        DB::table('fin_challan_installments')
                        ->where('FCI_ID', $request->mainId)
                        ->update([
                            'FCI_SESSION_ID' => $request->get('session'),
                            'FCI_SCHOOL_ID' => $schoolId,
                            'FCI_DEPT_ID' => $deptId,
                            'FCI_PROG_ID' => $progId,
                            // 'FCI_ADMISSION_TYPE' => $userId,
                            // 'FCI_FLAG' => $userId,

                            'FCI_FIRST_DUEDATE' => $first_installment_due_date,
                            'FCI_FIRST_VALIDDATE' => $first_installment_valid_date,
                            // 'FCI_FIRST_VISIBLEDATE' =>  $first_installment_visible_date,

                            'FCI_SECOND_DUEDATE' =>   $second_installment_due_date,
                            'FCI_SECOND_VALIDDATE' =>  $second_installment_valid_date,
                            // 'FCI_SECOND_VISIBLEDATE' =>  $second_installment_visible_date,

                            'FCI_THIRD_DUEDATE' => $third_installment_due_date,
                            'FCI_THIRD_VALIDDATE' => $third_installment_valid_date,
                            // 'FCI_THIRD_VISIBLEDATE' => $third_installment_visible_date,

                            'FCI_FOURTH_DUEDATE' => $forth_installment_due_date,
                            'FCI_FOURTH_VALIDDATE' => $forth_installment_valid_date,
                            // 'FCI_FOURTH_VISIBLEDATE' => $forth_installment_visible_date,

                            'FCI_FIFTH_DUEDATE' => $fifth_installment_due_date,
                            'FCI_FIFTH_VALIDDATE' => $fifth_installment_valid_date,
                            // 'FCI_FIFTH_VISIBLEDATE' => $fifth_installment_visible_date,

                            'FCI_STUDENT_TYPE' => $request->get('student_type'),
                            'FCI_ADMISSION_TYPE' => $request->get('admission_type'),
                            'FCI_FLAG' => 'Admission',
                            'FCI_STATUS' => 'E',
                            'FCI_ENTRY_USERID' => $userId,
                            'FCI_UPDATED_AT' => now('Asia/Karachi')->format('Y-m-d H:i:s')
                        ]);
                        // $resp[0]=true;
                        // $resp[1]='Updated Successfully';
                        // return $resp;
                        $resp[0] = true;
                        $resp[1] = 'Updated Successfully';
                    } catch (\Exception $e) {
                        $resp[0] = false;
                        $resp[1] = 'Uniqueness conflict occurred';

                        // Optionally log the exception for debugging
                        // Log::error('Error updating FIN_FEE_POLICY: ' . $e->getMessage());
                    }
                    return $resp;
                }else{
                    $resp[0]=false;
                    $resp[1]='Data already exists';
                    return $resp;
                }
            }

        }else{
            $resp[0]=false;
            $resp[1]='You can edit one program at a time';
            return $resp;
        }


    }
    public function getInstallmentDates_admission(Request $request){
        $authz = $this->assertCanManageAdmissionFeeInstallments();
        if ($authz['success'] === false) {
            return response()->json($authz, 403);
        }

        // dd($request->all());
        $data = DB::table('fin_challan_installments as fci')
            ->select(
                    'fci.*',
                    'acd1.SESS_NAME as SESS_NAME', // Session name for FFS_SESS_ID
                    'school.SCHOOL_NAME',
                    'dept.DEPT_NAME',
                    'prog.PROG_NAME'
                    )
                    ->join('ACADEMIC_SESSIONS as acd1', 'acd1.SESS_ID', '=', 'fci.FCI_SESSION_ID')
                    ->join('SCHOOLS as school', 'school.SCHOOL_ID', 'fci.FCI_SCHOOL_ID')
                    ->join('DEPARTMENTS as dept', 'dept.DEPT_ID', 'fci.FCI_DEPT_ID')
                    ->join('PROGRAMS as prog', 'prog.PROG_ID', 'fci.FCI_PROG_ID')
                    ->where('fci.FCI_STATUS', '=', 'E')
                    ->where('fci.FCI_FLAG', '=', 'Admission') ;

                    // ->join('fin_fee_category as fincateg', 'fincateg.id', 'ffs.FFS_FEECATEGORY')
                    if(!empty($request->session_sel)){
                        $data =  $data->where('fci.FCI_SESSION_ID', $request->session_sel);
                    }
                    if(!empty($request->student_type)){
                        $data =  $data->where('fci.FCI_STUDENT_TYPE', $request->student_type);
                    }
                    if(!empty($request->admission_type)){
                        $data =  $data->where('fci.FCI_ADMISSION_TYPE', $request->admission_type);
                    }
                    if(!empty($request->school_sel) && !is_array(json_decode($request->school_sel))){//condition will applied only when single school is selected if all school is slected then no need to apply condition//array means all school is selected and so we apply not array condtion to apply where clause;
                    $data =  $data->where('fci.FCI_SCHOOL_ID', $request->school_sel);
                    }
                    if(!empty($request->department) && !is_array(json_decode($request->department))){//condition will applied only when single department is selected if all department is slected then no need to apply condition//array means all department is selected and so we apply not array condtion to apply where clause;
                        $data =  $data->where('fci.FCI_DEPT_ID', $request->department);
                    }
                    if(!empty($request->program)&& !is_array(json_decode($request->program))){//condition will applied only when single program is selected if all program is slected then no need to apply condition//array means all program is selected and so we apply not array condtion to apply where clause;
                    //    dd('program condtion applied');
                        $programs_id = $request->program;
                        // dd($programs_id);
                        $array_of_sch_dep_prog = explode('~~', $programs_id);
                        // dd($array_of_sch_dep_prog[2]);
                    $data =  $data->where('fci.FCI_PROG_ID', $array_of_sch_dep_prog[2]);
                    }
                    // dd('prgram condition not applied');
                    if(!empty($request->first_dueDate)){
                        $data =  $data->where('fci.FCI_FIRST_DUEDATE', $request->first_dueDate);
                    }
                    if(!empty($request->first_validDate)){
                        $data =  $data->where('fci.FCI_FIRST_VALIDDATE', $request->first_validDate);
                    }
                    if(!empty($request->second_dueDate)){
                        $data =  $data->where('fci.FCI_SECOND_DUEDATE', $request->second_dueDate);
                    }
                    if(!empty($request->second_validDate)){
                        $data =  $data->where('fci.FCI_SECOND_VALIDDATE', $request->second_validDate);
                    }
                    if(!empty($request->third_dueDate)){
                        $data =  $data->where('fci.FCI_THIRD_DUEDATE', $request->third_dueDate);
                    }
                    if(!empty($request->third_validDate)){
                        $data =  $data->where('fci.FCI_THIRD_VALIDDATE', $request->third_validDate);
                    }
                    if(!empty($request->forth_dueDate)){
                        $data =  $data->where('fci.FCI_FOURTH_DUEDATE', $request->forth_dueDate);
                    }
                    if(!empty($request->forth_validDate)){
                        $data =  $data->where('fci.FCI_FOURTH_VALIDDATE', $request->forth_validDate);
                    }
                    if(!empty($request->fifth_dueDate)){
                        $data =  $data->where('fci.FCI_FIFTH_DUEDATE', $request->fifth_dueDate);
                    }
                    if(!empty($request->fifth_validDate)){
                        $data =  $data->where('fci.FCI_FIFTH_VALIDDATE', $request->fifth_validDate);
                    }

                    $data =  $data->orderBy('fci.fci_id', 'desc')->get();

                    //exit;
                    // dd($data);
                    // $sql = "SELECT * from FIN_FEE_STRUCTURE";
                    // //echo $sql;exit;
                    // $res = DB::select($sql);
                    // dd($data);
        return DataTables::of($data)->toJson();
    }
    public function deleteInstallmentDates_admission(Request $request){
        $authz = $this->assertCanManageAdmissionFeeInstallments();
        if ($authz['success'] === false) {
            return response()->json($authz, 403);
        }

        $user = auth()->user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'msg' => 'Unauthorized.',
            ], 403);
        }
        $userId = $user->id;
        $ids = is_array($request->mainId) ? $request->mainId : [$request->mainId];
        $ids = array_values(array_filter($ids, function ($id) {
            return $id !== null && $id !== '';
        }));
        if (empty($ids)) {
            return response()->json([
                'success' => false,
                'data' => 'No installment record selected.',
            ], 400);
        }
        $lockedIds = [];
        $totalPaid = 0;
        $totalUnpaid = 0;
        foreach ($ids as $id) {
            $summary = $this->getInstallmentAdmissionChallanSummary($id);
            if (!empty($summary['has_challan'])) {
                $lockedIds[] = $id;
                $totalPaid += $summary['paid_count'];
                $totalUnpaid += $summary['unpaid_count'];
            }
        }
        if (count($lockedIds) === count($ids)) {
            return response()->json([
                'success' => false,
                'data' => 'Cannot delete: challan(s) already exist against selected installment setup(s)',
                'has_challan' => true,
                'locked_ids' => $lockedIds,
                'paid_count' => $totalPaid,
                'unpaid_count' => $totalUnpaid,
                'total_count' => $totalPaid + $totalUnpaid,
            ]);
        }
        $deletableIds = array_values(array_diff($ids, $lockedIds));
        $affectedRows = DB::table('FIN_CHALLAN_INSTALLMENTS')
                            ->whereIn('FCI_ID', $deletableIds)
                            ->where('FCI_FLAG', 'Admission')
                            ->where('FCI_STATUS', 'E')
                            ->update([
                                'FCI_STATUS' => 'D',
                                'FCI_ENTRY_USERID' => $userId,
                            ]);
        if ($affectedRows > 0) {
            $msg = 'Deleted Successfully';
            if (count($lockedIds) > 0) {
                $msg .= '. '.count($lockedIds).' record(s) skipped because challan(s) exist';
            }
            return response()->json([
                'success' => true,
                'data' => $msg,
                'locked_ids' => $lockedIds,
                'has_challan' => count($lockedIds) > 0,
                'paid_count' => $totalPaid,
                'unpaid_count' => $totalUnpaid,
                'total_count' => $totalPaid + $totalUnpaid,
            ]);
        }
        return response()->json([
            'success' => false,
            'data' => 'Some problem occured'
        ]);
    }

    public function checkInstallmentAdmissionChallan(Request $request)
    {
        $authz = $this->assertCanManageAdmissionFeeInstallments();
        if ($authz['success'] === false) {
            return response()->json($authz, 403);
        }

        $ids = is_array($request->mainId) ? $request->mainId : [$request->mainId];
        $ids = array_values(array_filter($ids, function ($id) {
            return $id !== null && $id !== '';
        }));
        $lockedIds = [];
        $details = [];
        $totalPaid = 0;
        $totalUnpaid = 0;
        foreach ($ids as $id) {
            $summary = $this->getInstallmentAdmissionChallanSummary($id);
            if (!empty($summary['has_challan'])) {
                $lockedIds[] = $id;
                $totalPaid += $summary['paid_count'];
                $totalUnpaid += $summary['unpaid_count'];
                $details[] = [
                    'fci_id' => $id,
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

    private function installmentAdmissionHasChallanById($fciId)
    {
        $summary = $this->getInstallmentAdmissionChallanSummary($fciId);
        return !empty($summary['has_challan']);
    }

    /**
     * Match PAID/UNPAID Full_semester challans by session + school + dept + program
     * + student type (SC_STUDENT_STATUS) + admission round (SC_ADMISSION_ROUND).
     */
    private function getInstallmentAdmissionChallanSummary($fciId)
    {
        $row = DB::table('fin_challan_installments')
            ->where('FCI_ID', $fciId)
            ->where('FCI_STATUS', 'E')
            ->where('FCI_FLAG', 'Admission')
            ->first();
        if (!$row) {
            return [
                'has_challan' => false,
                'paid_count' => 0,
                'unpaid_count' => 0,
            ];
        }

        $sessionId = $row->fci_session_id ?? $row->FCI_SESSION_ID ?? null;
        $schoolId = $row->fci_school_id ?? $row->FCI_SCHOOL_ID ?? null;
        $deptId = $row->fci_dept_id ?? $row->FCI_DEPT_ID ?? null;
        $progId = $row->fci_prog_id ?? $row->FCI_PROG_ID ?? null;
        $studentType = $row->fci_student_type ?? $row->FCI_STUDENT_TYPE ?? null;
        $admissionType = $row->fci_admission_type ?? $row->FCI_ADMISSION_TYPE ?? null;

        $query = DB::table('STUDENT_CHALLAN')
            ->where('SC_CHALLAN_TYPE', 'Full_semester')
            ->where('SC_SESSION_ID', $sessionId)
            ->where('SC_SCHOOL_ID', $schoolId)
            ->where('SC_DEPARTMENT_ID', $deptId)
            ->where('SC_PROGRAM_ID', $progId)
            ->where('SC_STUDENT_STATUS', $studentType)
            ->whereRaw("UPPER(TRIM(SC_CHALLAN_STATUS)) IN ('PAID', 'UNPAID')");

        if ($admissionType === null || $admissionType === '') {
            $query->where(function ($q) {
                $q->whereNull('SC_ADMISSION_ROUND')
                    ->orWhereRaw("TRIM(SC_ADMISSION_ROUND) = ''");
            });
        } else {
            $query->whereRaw(
                'UPPER(TRIM(SC_ADMISSION_ROUND)) = ?',
                [strtoupper(trim($admissionType))]
            );
        }

        $counts = $query->selectRaw("
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
}
