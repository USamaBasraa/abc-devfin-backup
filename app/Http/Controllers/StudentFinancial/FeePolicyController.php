<?php

namespace App\Http\Controllers\StudentFinancial;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Grader\Schools;
use App\Models\Grader\Departments;
use DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use DataTables;
use Response;



class FeePolicyController extends Controller
{

    function index(Request $request)
    {
        $sessions = \App::call('App\Http\Controllers\Grader\AcademicSessionController@retrieve');
        $academicYears =  DB::select("select * from ACADEMIC_YEARS where AY_STATUS = 'E' ORDER BY AY_ORDER desc");
        $schools = Schools::where('school_status', 'E')->get();
        $departments = Departments::where('dept_status', 'E')->get();
        $semesters = DB::select("select * from SEMESTERS ORDER BY SEMESTER_ID");
        return view('StudentFinancial.feePolicy', compact('sessions', 'schools', 'departments', 'academicYears','semesters'));
    }



public function storeFeePolicy(Request $request){
    // dd($request->all());
    $validityType = $request->get('validity-type');

    $rules = [
        'session' => 'numeric', // Default to numeric, will adjust based on validity-type
        'academic_year' => 'numeric', // Default to numeric, will adjust based on validity-type
        'school' => 'required|string',
        'department' => 'required',
        'degree_level' => 'required',
        'program' => 'required',
        'semester' => 'required',
        'fee_Type' => 'required|string',
        'min_range' => 'required_if:fee_Type,cr_wise|nullable|numeric',
        'max_range' => 'required_if:fee_Type,cr_wise|nullable|numeric',
    ];

    if ($validityType == 'session') {
        $rules['session'] = 'required|numeric';
        $rules['academic_year'] = 'nullable|numeric';
    } elseif ($validityType == 'annual') {
        $rules['session'] = 'nullable|numeric';
        $rules['academic_year'] = 'required|numeric';
    }

    $validator = Validator::make($request->all(), $rules);
    if ($validator->fails()) {
        return Response::json([
            'success' => false,
            'errors' => $validator->getMessageBag()->toArray()
        ], 400);
    }
    if($request->get('min_range') > $request->get('max_range')){
        return Response::json(array(
            'success' => false,
            'msg' => 'Min value can not be greater than Max',
        ), 400);
    }
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
    if ($validityType == 'session') {//when session is selected then insert data against that one session
        $session =  $request->get('session');
        $resp = $this->allProgramsInsertion($school_depart_prog, $request, $session);

    } elseif ($validityType == 'annual') {//when yearly is selected then first get the sessions under that year then insert data againt each session in that year except SUMMER session

        $acdmic_year_sessions = DB::table('ACADEMIC_SESSIONS')
                                ->select('SESS_ID', 'SESS_CODE')
                                ->where('SESS_AY_ID', $request->get('academic_year'))
                                ->where('SESS_STATUS', 'E')
                                ->get();
        if(!empty($acdmic_year_sessions)){
            foreach($acdmic_year_sessions as $val){
                // $firstThreeChars = substr(trim($val->sess_code), 0, 3);
                // if($firstThreeChars != 'SUM'){
                    $session = $val->sess_id;
                    $resp = $this->allProgramsInsertion($school_depart_prog, $request, $session);
                // }
            }
        }
    }
    return response()->json([
        'success' => $resp[0],
        'msg' => $resp[1],
    ]);

    /////////////////////
    // $validatedPolicyData = $validator->validate();
    // // $validatedData = $validator->validate();
    // $validatedData = $this->handling_acamdemicYearOrSession($validityType, $validatedPolicyData);
    // // dd($validatedData);
    // // try {


    //     // Start database transaction
    //     DB::beginTransaction();
    //     if ($request->verifyEdit == '1') {
    //         foreach($validatedData['session_sel'] as $sessionId){////this is to handle multiple updates against academic year sessions
    //             $success = $this->updateFeePolicy($validatedData, $request->edit_id, $sessionId);
    //         }


    //         if (!$success) {
    //             DB::rollBack();
    //             return response()->json(['status' => false, 'message' => 'Failed to update policy'], 500);
    //         }
    //         DB::commit();
    //         return response()->json(['status' => true, 'message' => 'Policy updated successfully'], 200);
    //     } else {


    //         // Determine the insertion strategy based on user input
    //         $insertionStrategy = $this->determineInsertionStrategy($validatedData);
    //         // dd($validatedData);

    //         // Perform insertion based on the determined strategy
    //         switch ($insertionStrategy) {
    //             case 'allSchoolsAllDeptsAllProgs':
    //                 foreach($validatedData['session_sel'] as $sessionId){////this is to handle multiple updates against academic year sessions
    //                     $success = $this->bulkInsertAllSchoolsAllDeptsAllProgs($validatedData, $sessionId);
    //                 }
    //                 $message = "All schools, all departments, all programs inserted successfully.";
    //                 break;
    //             case 'specificSchoolAllDeptsAllProgs':
    //                 foreach($validatedData['session_sel'] as $sessionId){////this is to handle multiple updates against academic year sessions
    //                     $success = $this->bulkInsertSpecificSchoolAllDeptsAllProgs($validatedData, $sessionId);
    //                 }
    //                 $message = "Specific school, all departments, all programs inserted successfully.";
    //                 break;
    //             case 'specificSchoolSpecificDeptAllProgs':
    //                 foreach($validatedData['session_sel'] as $sessionId){////this is to handle multiple updates against academic year sessions
    //                     $success = $this->bulkInsertSpecificSchoolSpecificDeptAllProgs($validatedData, $sessionId);
    //                 }
    //                 $message = "Specific school, specific department, all programs inserted successfully.";
    //                 break;
    //             case 'specificSchoolSpecificDeptSpecificProg':
    //                 foreach($validatedData['session_sel'] as $sessionId){////this is to handle multiple updates against academic year sessions
    //                     $success = $this->insertSpecificSchoolSpecificDeptSpecificProg($validatedData, $sessionId);
    //                 }
    //                 $message = "Specific school, specific department, specific programs inserted successfully.";
    //                 break;
    //             default:
    //                 DB::rollBack();
    //                 return response()->json(['status' => false, 'message' => 'Invalid insertion strategy'], 400);
    //         }

    //         if ($success) {
    //             DB::commit();
    //             return response()->json(['status' => true, 'message' => $message], 200);
    //         } else {
    //             DB::rollBack();
    //             return response()->json(['status' => false, 'message' => 'Failed to insert data'], 500);
    //         }
    //     }
    // } catch (\Exception $e) {
    //     DB::rollBack();
    //     return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
    // }
}
private static function allProgramsInsertion($school_depart_prog, $request, $session){
    $userId = auth()->user()->id;
    if(empty($request->mainId)){//insertion case
        foreach($school_depart_prog as $val){
            $schoolId = $val[0];
            $deptId = $val[1];
            $progId = $val[2];
            // dd('session->'.$session, 'school->'.$schoolId, 'dept_id->'.$deptId, 'program->'.$progId,  $request->get('degree_level'), $request->get('fee_Type'));
            $duplicateCheck = DB::table('FIN_FEE_POLICY')
                            ->select(
                                    'FFP_ID',
                                    'FFP_SESS_ID',
                                    'FFP_SCHOOL_ID',
                                    'FFP_DEPT_ID',
                                    'FFP_PROG_ID',
                                    'FFP_DEGREE_LEVEL',
                                    'FFS_TYPE',
                                    'FFP_MIN_CR',
                                    'FFP_MAX_CR'
                                    )
                                    ->where('FFP_SESS_ID', $session)
                                    ->where('FFP_SCHOOL_ID', $schoolId)
                                    ->where('FFP_DEPT_ID', $deptId)
                                    ->where('FFP_PROG_ID', $progId)
                                    ->where('FFP_DEGREE_LEVEL', $request->get('degree_level'))
                                    ->where('FFS_TYPE', $request->get('fee_Type'))
                                    ->where('FFP_SEMESTER', $request->get('semester'))
                                    ->where('FFP_STATUS', 'E')
                                    ->get();
                                    // dd($duplicateCheck);
            if(sizeof($duplicateCheck) == 0 ){
                DB::table('FIN_FEE_POLICY')->insert([
                    'FFP_SCHOOL_ID' => $schoolId,
                    'FFP_DEPT_ID' => $deptId,
                    'FFP_PROG_ID' => $progId,
                    'FFP_SESS_ID' => $session,
                    'FFP_ENTRY_USER_ID' => $userId,
                    'FFP_DEGREE_LEVEL' => $request->get('degree_level'),
                    'FFS_TYPE' => $request->get('fee_Type'),
                    'FFP_SEMESTER' => $request->get('semester'),
                    'FFP_MIN_CR' => $request->get('min_range'),
                    'FFP_MAX_CR' => $request->get('max_range'),
                    'FFP_STATUS' => 'E',
                    'FFP_CREATED_AT' => now(),
                ]);
            }else{
                DB::table('FIN_FEE_POLICY')
                ->where('FFP_ID', $duplicateCheck[0]->ffp_id)
                ->update([
                    'FFP_SCHOOL_ID' => $schoolId,
                    'FFP_DEPT_ID' => $deptId,
                    'FFP_PROG_ID' => $progId,
                    'FFP_SESS_ID' => $session,
                    'FFP_ENTRY_USER_ID' => $userId,
                    'FFP_DEGREE_LEVEL' => $request->get('degree_level'),
                    'FFS_TYPE' => $request->get('fee_Type'),
                    'FFP_SEMESTER' => $request->get('semester'),
                    'FFP_MIN_CR' => $request->get('min_range'),
                    'FFP_MAX_CR' => $request->get('max_range'),
                    'FFP_STATUS' => 'E',
                    'FFP_CREATED_AT' => now(),
                ]);
            }
        }
        $resp[0]=true;
        $resp[1]='Saved Successfully';
        return $resp;
    }else if(sizeof($school_depart_prog) == 1){ //edit case
        // dd($school_depart_prog,$request->mainId );
        if(!empty($school_depart_prog[0][0])){//checking if the allprograms option is selected and there is no program in it
            $schoolId = $school_depart_prog[0][0];
            $deptId = $school_depart_prog[0][1];
            $progId = $school_depart_prog[0][2];
            $duplicateCheck = DB::table('FIN_FEE_POLICY')
                                ->select(
                                        'FFP_ID',
                                        'FFP_SESS_ID',
                                        'FFP_SCHOOL_ID',
                                        'FFP_DEPT_ID',
                                        'FFP_PROG_ID',
                                        'FFP_DEGREE_LEVEL',
                                        'FFS_TYPE',
                                        'FFP_MIN_CR',
                                        'FFP_MAX_CR'
                                        )
                                        ->where('FFP_SESS_ID', $session)
                                        ->where('FFP_SCHOOL_ID', $schoolId)
                                        ->where('FFP_DEPT_ID', $deptId)
                                        ->where('FFP_PROG_ID', $progId)
                                        ->where('FFP_DEGREE_LEVEL', $request->get('degree_level'))
                                        ->where('FFS_TYPE', $request->get('fee_Type'))
                                        ->where('FFP_SEMESTER', $request->get('semester'))
                                        ->where('FFP_STATUS', 'E')
                                        ->where('FFP_ID', '!=', $request->mainId)
                                        ->get();
                                        // dd($duplicateCheck);
            if(sizeof($duplicateCheck) == 0){
                try {
                    DB::table('FIN_FEE_POLICY')
                        ->where('FFP_ID', $request->mainId)
                        ->update([
                            'FFP_SCHOOL_ID' => $schoolId,
                            'FFP_DEPT_ID' => $deptId,
                            'FFP_PROG_ID' => $progId,
                            'FFP_SESS_ID' => $session,
                            'FFP_ENTRY_USER_ID' => $userId,
                            'FFP_DEGREE_LEVEL' => $request->get('degree_level'),
                            'FFS_TYPE' => $request->get('fee_Type'),
                            'FFP_SEMESTER' => $request->get('semester'),
                            'FFP_MIN_CR' => $request->get('min_range'),
                            'FFP_MAX_CR' => $request->get('max_range'),
                            'FFP_STATUS' => 'E',
                            'FFP_CREATED_AT' => now(),
                        ]);

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
                $resp[1]='Policy already exists';
                return $resp;
            }
        }
        // dd(1);
    }else{
        $resp[0]=false;
        $resp[1]='You can edit one program at a time';
        return $resp;

    }


}




// private function updateFeePolicy($data, $edit_id, $session_id)
// {

//     try {

//         $updateData = [
//             'ffp_school_id' => $data['school_sel'],
//             'ffp_dept_id' => $data['department'],
//             'ffp_degree_level' => $data['degree_level'],
//             'ffp_prog_id' => $data['course'],
//             'ffp_sess_id' => $session_id,
//             'ffp_criteria' => $data['min_range'] . '-' . $data['max_range'],
//             'ffs_type' => $data['fee_Type'],
//         ];

//         $updated = DB::table('fin_fee_policy')
//             ->where('ffp_id', $edit_id)
//             ->update($updateData);

//         return $updated !== false;
//     } catch (\Exception $e) {
//     //    echo $e;
//         return false;
//     }
// }

// Other functions remain the same...


// private function determineInsertionStrategy($data)
// {

//     // dd($data);
//     if ($data['school_sel'] === 'allSchools' && $data['department'] === 'allDepts' && $data['course'] === 'allProgs') {
//         return 'allSchoolsAllDeptsAllProgs';
//     } elseif ($data['school_sel'] !== 'allSchools' && $data['department'] === 'allDepts' && $data['course'] === 'allProgs') {
//         return 'specificSchoolAllDeptsAllProgs';
//     } elseif ($data['school_sel'] !== 'allSchools' && $data['department'] !== 'allDepts' && $data['course'] === 'allProgs') {
//         return 'specificSchoolSpecificDeptAllProgs';
//     } elseif ($data['school_sel'] !== 'allSchools' && $data['department'] !== 'allDepts' && $data['course'] !== 'allProgs') {
//         return 'specificSchoolSpecificDeptSpecificProg';
//     } else {
//         return 'invalidInsertionStrategy';
//     }
// }


// private function bulkInsertAllSchoolsAllDeptsAllProgs($data, $sessionId)
// {
//     try {
//         $schools = $this->getActiveSchools();

//         foreach ($schools as $school) {
//             $departments = $this->getActiveDepartments($school->school_id);

//             foreach ($departments as $department) {
//                 $programs = $this->getActivePrograms($school->school_id, $department->dept_id);

//                 foreach ($programs as $program) {
//                     $criteria = $data['min_range'] . '-' . $data['max_range'];
//                     $user_id = auth()->user()->username;

//                     $updateData = [
//                         'ffp_school_id' => $school->school_id,
//                         'ffp_dept_id' => $department->dept_id,
//                         'ffp_degree_level' => $data['degree_level'],
//                         'ffp_prog_id' => $program->prog_id,
//                         'ffp_sess_id' => $sessionId,
//                         'ffp_criteria' => $criteria,
//                         'ffs_type' => $data['fee_Type'],
//                         'ffp_entry_user_id' => $user_id
//                     ];

//                     if ($this->checkRecordExists($sessionId, $school->school_id, $department->dept_id, $program->prog_id, $criteria)) {
//                         DB::table('fin_fee_policy')
//                             ->where('ffp_sess_id', $sessionId)
//                             ->where('ffp_school_id', $school->school_id)
//                             ->where('ffp_dept_id', $department->dept_id)
//                             ->where('ffp_degree_level', $data['degree_level'])
//                             ->where('ffp_prog_id', $program->prog_id)
//                             ->update($updateData);
//                     } else {
//                         DB::table('fin_fee_policy')->insert($updateData);
//                     }
//                 }
//             }
//         }

//         return true;
//     } catch (\Exception $e) {
//         return false;
//     }
// }


// private function bulkInsertSpecificSchoolAllDeptsAllProgs($data, $sessionId)
// {
//     try {
//         $departments = $this->getActiveDepartments($data['school_sel']);

//         foreach ($departments as $department) {
//             $programs = $this->getActivePrograms($data['school_sel'], $department->dept_id);

//             foreach ($programs as $program) {
//                 $criteria = $data['min_range'] . '-' . $data['max_range'];
//                 $user_id = auth()->user()->username;

//                 $updateData = [
//                     'ffp_school_id' => $data['school_sel'],
//                     'ffp_dept_id' => $department->dept_id,
//                     'ffp_degree_level' => $data['degree_level'],
//                     'ffp_prog_id' => $program->prog_id,
//                     'ffp_sess_id' => $sessionId,
//                     'ffp_criteria' => $criteria,
//                     'ffs_type' => $data['fee_Type'],
//                     'ffp_entry_user_id' => $user_id
//                 ];

//                 if ($this->checkRecordExists($sessionId, $data['school_sel'], $department->dept_id, $program->prog_id, $criteria)) {
//                     DB::table('fin_fee_policy')
//                         ->where('ffp_sess_id', $sessionId)
//                         ->where('ffp_school_id', $data['school_sel'])
//                         ->where('ffp_dept_id', $department->dept_id)
//                         ->where('ffp_degree_level', $data['degree_level'])
//                         ->where('ffp_prog_id', $program->prog_id)
//                         ->update($updateData);
//                 } else {
//                     DB::table('fin_fee_policy')->insert($updateData);
//                 }
//             }
//         }

//         return true;
//     } catch (\Exception $e) {
//         return false;
//     }
// }


// private function bulkInsertSpecificSchoolSpecificDeptAllProgs($data, $sessionId)
// {
//     try {
//         $programs = $this->getActivePrograms($data['school_sel'], $data['department']);

//         foreach ($programs as $program) {
//             $criteria = $data['min_range'] . '-' . $data['max_range'];
//             $user_id = auth()->user()->username;

//             $updateData = [
//                 'ffp_school_id' => $data['school_sel'],
//                 'ffp_dept_id' => $data['department'],
//                 'ffp_degree_level' => $data['degree_level'],
//                 'ffp_prog_id' => $program->prog_id,
//                 'ffp_sess_id' => $sessionId,
//                 'ffp_criteria' => $criteria,
//                 'ffs_type' => $data['fee_Type'],
//                 'ffp_entry_user_id' => $user_id
//             ];

//             if ($this->checkRecordExists($sessionId, $data['school_sel'], $data['department'], $program->prog_id, $criteria)) {
//                 DB::table('fin_fee_policy')
//                     ->where('ffp_sess_id', $sessionId)
//                     ->where('ffp_school_id', $data['school_sel'])
//                     ->where('ffp_dept_id', $data['department'])
//                     ->where('ffp_degree_level', $data['degree_level'])
//                     ->where('ffp_prog_id', $program->prog_id)
//                     ->update($updateData);
//             } else {
//                 DB::table('fin_fee_policy')->insert($updateData);
//             }
//         }

//         return true;
//     } catch (\Exception $e) {
//         return false;
//     }
// }

// private function insertSpecificSchoolSpecificDeptSpecificProg($data, $sessionId)
// {
//     try {
//         $criteria = $data['min_range'] . '-' . $data['max_range'];
//         $user_id = auth()->user()->username;

//         $updateData = [
//             'ffp_school_id' => $data['school_sel'],
//             'ffp_dept_id' => $data['department'],
//             'ffp_degree_level' => $data['degree_level'],
//             'ffp_prog_id' => $data['course'],
//             'ffp_sess_id' => $sessionId,
//             'ffp_criteria' => $criteria,
//             'ffs_type' => $data['fee_Type'],
//             'ffp_entry_user_id' => $user_id
//         ];

//         if ($this->checkRecordExists($sessionId, $data['school_sel'], $data['department'], $data['course'],$criteria)) {
//             DB::table('fin_fee_policy')
//                 ->where('ffp_sess_id', $sessionId)
//                 ->where('ffp_school_id', $data['school_sel'])
//                 ->where('ffp_dept_id', $data['department'])
//                 ->where('ffp_degree_level', $data['degree_level'])
//                 ->where('ffp_prog_id', $data['course'])
//                 ->update($updateData);
//         } else {
//             DB::table('fin_fee_policy')->insert($updateData);
//         }

//         return true;
//     } catch (\Exception $e) {
//         return false;
//     }
// }
// private function handling_acamdemicYearOrSession($validityType, $policyData){
//     $session = [];
//     if ($validityType == 'session') {//when session is selected then insert data against that one session

//         $session[] =  $policyData['session_sel'];

//     } elseif ($validityType == 'annual') {//when yearly is selected then first get the sessions under that year then insert data againt each session in that year except SUMMER session

//         $acdmic_year_sessions = DB::table('ACADEMIC_SESSIONS')
//                                 ->select('SESS_ID', 'SESS_CODE')
//                                 ->where('SESS_AY_ID', $policyData['academic_year'])
//                                 ->where('SESS_STATUS', 'E')
//                                 ->get();
//         if(!empty($acdmic_year_sessions)){
//             foreach($acdmic_year_sessions as $val){

//                     $session[] = $val->sess_id;


//             }
//         }
//     }
//     $policyData['session_sel'] = $session;
//     return $policyData;
// }



function getPolicyRec(Request $request) {
        // dd($request->all());
        //////////////TO get the sessions/////////////////
        $validityType = $request->get('validity_type');
        // dd($request->get('session_sel'));
        $session = [];
        if ($validityType == 'session'  && !empty($request->get('session_sel'))) {//validity type is session and when session is selected

            $session[] =  $request->get('session_sel');
        }else if($validityType == 'annual' && !empty($request->get('year_sel'))) {//validity type is yearly and when yearly is selected get the sessions under that year
            $acdmic_year_sessions = DB::table('ACADEMIC_SESSIONS')
                                    ->select('SESS_ID', 'SESS_CODE')
                                    ->where('SESS_AY_ID', $request->get('year_sel'))
                                    ->where('SESS_STATUS', 'E')
                                    ->get();
                    //   dd($acdmic_year_sessions);
            if(!empty($acdmic_year_sessions)){
                foreach($acdmic_year_sessions as $val){
                    $session[]= $val->sess_id;
                }

            }
        }
        /////////////END of to get the sessions//////////////////////

        //  dd($session);
    $data = DB::table('fin_fee_policy')
        ->select([
            'fin_fee_policy.ffp_id',
            'fin_fee_policy.ffp_school_id',
            'fin_fee_policy.ffp_dept_id',
            'fin_fee_policy.ffp_degree_level',
            'fin_fee_policy.ffp_prog_id',
            'fin_fee_policy.ffp_sess_id',
            'fin_fee_policy.ffp_criteria',
            'fin_fee_policy.ffp_min_cr',
            'fin_fee_policy.ffp_max_cr',

            'fin_fee_policy.FFP_SEMESTER',
            'fin_fee_policy.ffs_type',
            'schools.school_name',
            'departments.dept_name',
            'programs.prog_name',
            'academic_sessions.sess_name',
            'semesters.SEMESTER_NAME'
        ])
        ->join('schools', 'fin_fee_policy.ffp_school_id', '=', 'schools.school_id')
        ->join('departments', 'fin_fee_policy.ffp_dept_id', '=', 'departments.dept_id')
        ->join('programs', 'fin_fee_policy.ffp_prog_id', '=', 'programs.prog_id')
        ->join('academic_sessions', 'fin_fee_policy.ffp_sess_id', '=', 'academic_sessions.sess_id')
        ->leftjoin('semesters', 'fin_fee_policy.FFP_SEMESTER', '=', 'semesters.SEMESTER_ID');
        if(!empty($session)){
            $data =  $data->whereIn('fin_fee_policy.FFP_SESS_ID', $session);
        }
        if(!empty($request->school_sel) && !is_array(json_decode($request->school_sel))){//condition will applied only when single school is selected if all school is slected then no need to apply condition
           $data =  $data->where('fin_fee_policy.FFP_SCHOOL_ID', $request->school_sel);
        }
        if(!empty($request->department) && !is_array(json_decode($request->department))){//condition will applied only when single department is selected if all department is slected then no need to apply condition
            $data =  $data->where('fin_fee_policy.FFP_DEPT_ID', $request->department);
        }
        if(!empty($request->program) && !is_array(json_decode($request->program))){//condition will applied only when single program is selected if all program is slected then no need to apply condition
              //    dd('program condtion applied');
              $programs_id = $request->program;
              // dd($programs_id);
              $array_of_sch_dep_prog = explode('~~', $programs_id);
              // dd($array_of_sch_dep_prog[2]);
            $data =  $data->where('fin_fee_policy.FFP_PROG_ID', $array_of_sch_dep_prog[2]);
        }
        if(!empty($request->degree_level)){
           $data = $data->where('fin_fee_policy.FFP_DEGREE_LEVEL', $request->degree_level);
        }
        if(!empty($request->fee_Type)){
            $data = $data->where('fin_fee_policy.FFS_TYPE', $request->fee_Type);
        }
        if(!empty($request->semester)){
           $data =  $data->where('fin_fee_policy.FFP_SEMESTER', $request->semester);
        }
        if (!empty($request->min_range)) {
            // $data = $data->whereRaw("REGEXP_SUBSTR(fin_fee_policy.FFP_CRITERIA, '^[^-]+') = ?", [$request->min_range]);
          $data = $data->where('fin_fee_policy.FFP_MIN_CR', '=', $request->min_range);
        }

        if (!empty($request->max_range)) {
            // $data = $data->whereRaw("REGEXP_SUBSTR(fin_fee_policy.FFP_CRITERIA, '[^-]+$') = ?", [$request->max_range]);
            $data = $data->where("fin_fee_policy.FFP_MAX_CR", '=', $request->max_range);
        }
        $data = $data->orderByDesc('fin_fee_policy.ffp_id')
                ->get();
        // dd($data);
        return DataTables::of($data)->toJson();
}


public function deleteFeePolicy(Request $request)
{
    // dd($request->get('mainId'));
    $idArray=$request->get('mainId');
    try {
        $deleted = DB::table('fin_fee_policy')->whereIn('ffp_id', $idArray)->delete();

        if ($deleted) {
            // Return a success message and the updated list of fee policies
            return response()->json([
                'status' => true,
                'message' => 'Policy deleted successfully',
                'data' => $this->getPolicyRec($request) // Assuming getPolicyRec returns the updated list
            ], 200);
        } else {
            return response()->json(['status' => false, 'message' => 'Failed to delete policy'], 500);
        }
    } catch (\Exception $e) {
        return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
    }
}



private function checkRecordExists($sessionId, $schoolId, $deptId, $progId,$criteria)
{
    return DB::table('fin_fee_policy')
        ->where('ffp_sess_id', $sessionId)
        ->where('ffp_school_id', $schoolId)
        ->where('ffp_dept_id', $deptId)
        ->where('ffp_prog_id', $progId)
        ->where('ffp_criteria', $criteria)
        ->exists();
}

private function getActiveSchools()
{
    return DB::table('schools')->where('school_status', 'E')->get();
}

private function getActiveDepartments($schoolId)
{
    return DB::table('departments')
        ->where('dept_school_id', $schoolId)
        ->where('dept_status', 'E')
        ->get();
}

private function getActivePrograms($schoolId, $deptId)
{
    return DB::table('programs')
        ->where('prog_school_id', $schoolId)
        ->where('prog_dept_id', $deptId)
        ->where('prog_status', 'E')
        ->get();
}

}
