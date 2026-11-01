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

class AdmissionScholarshipCriteriaController extends Controller{


    function masterAdmissionSclrshpCriteriaForm(){
        $sessions = \App::call('App\Http\Controllers\Grader\AcademicSessionController@schlrshipSessionRetrieve');
        $schools =  Schools::all();
        $scholarships = DB::select("SELECT * FROM SCHOLARSHIP_TYPE WHERE SCHOLARSHIP_NAME = 'Merit Base'");
        $departments = array(); // $this->school->fetch();
        $programs =  DB::select("select * from Programs where PROG_STATUS = 'E'");
        $academicYears =  DB::select("select * from ACADEMIC_YEARS where AY_STATUS = 'E' ORDER BY AY_ORDER desc");
        $query = 'select * from fin_fee_category';
        $feeCategories = DB::select($query);
        return view('StudentFinancial.master_admission_schlrshp_criteria',compact('sessions','schools','departments','scholarships','feeCategories','programs', 'academicYears'));

    }
    public function addAdmissionScholarshipCriteria(Request $request){
        // dd($request->all());
        $validityType = $request->get('validity-type');

        $rules = [
            'school' => 'required',
            'department' => 'required',
            'program' => 'required',
            'degree_level' => 'required',
            'session' => 'numeric', // Default to numeric, will adjust based on validity-type
            'academic_year' => 'numeric', // Default to numeric, will adjust based on validity-type
            'scholarship' => 'required',
            // 'effective_from' => 'required',
            // 'effective_to' => 'required',
            'percentage' => 'required|numeric|min:1|max:100',
            'cgpa_min' => 'required|min:1|max:100',
            'cgpa_max' => 'required|min:1|max:100',
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
        if($request->get('cgpa_min') > 100 || $request->get('cgpa_max') > 100 || $request->get('cgpa_min') < 1 || $request->get('cgpa_max') < 1){
            return Response::json(array(
                'success' => false,
                'msg' => 'The CGPA values must be between 1 and 4.',
            ), 400);
        }
        if($request->get('cgpa_min') > $request->get('cgpa_max')){
            return Response::json(array(
                'success' => false,
                'msg' => 'Min value cant be greater than Max',
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
                    $firstThreeChars = substr(trim($val->sess_code), 0, 3);
                    if($firstThreeChars != 'SUM'){
                        $session = $val->sess_id;
                        $resp = $this->allProgramsInsertion($school_depart_prog, $request, $session);
                    }
                }
            }
        }
        // return response()->json(['success' => true]);
        return response()->json([
            'success' => $resp[0],
            'msg' => $resp[1],
        ]);

    }
    private static function allProgramsInsertion($school_depart_prog, $request, $session){
        $userId = auth()->user()->id;
        if(empty($request->mainId)){//insertion case
            foreach($school_depart_prog as $val){
                $schoolId = $val[0];
                $deptId = $val[1];
                $progId = $val[2];
                $duplicateCheck = DB::table('FIN_ADMISSION_SCHOLARSHIP_CRITERIA')
                                ->select(
                                        'FASC_ID',
                                        'FASC_SESSION_ID',
                                        'FASC_SCHOOL_ID',
                                        'FASC_DEPT_ID',
                                        'FASC_PROG_ID',
                                        'FASC_DEGREE_LEVEL',
                                        'FASC_SCHOLARSHIP_ID',
                                        'FASC_PERCENT_FROM',
                                        'FASC_PERCENT_TO'
                                        )
                                        ->where('FASC_SESSION_ID', $session)
                                        ->where('FASC_SCHOOL_ID', $schoolId)
                                        ->where('FASC_DEPT_ID', $deptId)
                                        ->where('FASC_PROG_ID', $progId)
                                        ->where('FASC_DEGREE_LEVEL', $request->get('degree_level'))
                                        ->where('FASC_SCHOLARSHIP_ID', $request->get('scholarship'))
                                        // ->where('FASC_PERCENT_FROM', $request->get('cgpa_min'))
                                        // ->where('FASC_PERCENT_TO', $request->get('cgpa_max'))
                                        ->where(function($query) use ($request) {
                                            $cgpaMin = $request->get('cgpa_min');
                                            $cgpaMax = $request->get('cgpa_max');

                                            $query->where(function($query) use ($cgpaMin, $cgpaMax) {
                                                $query->where('FASC_PERCENT_FROM', $cgpaMin)
                                                    ->orWhere('FASC_PERCENT_FROM', $cgpaMax)
                                                    ->orWhere('FASC_PERCENT_TO', $cgpaMin)
                                                    ->orWhere('FASC_PERCENT_TO', $cgpaMax)
                                                    ->orWhereBetween('FASC_PERCENT_FROM', [$cgpaMin, $cgpaMax])
                                                    ->orWhereBetween('FASC_PERCENT_TO', [$cgpaMin, $cgpaMax])
                                                    ->orWhere(function($query) use ($cgpaMin, $cgpaMax) {
                                                        $query->where('FASC_PERCENT_FROM', '>', $cgpaMin)
                                                              ->where('FASC_PERCENT_TO', '<', $cgpaMax);
                                                    });
                                            });
                                        })
                                        ->where('FASC_STATUS', 'E')
                                        ->where('FASC_AAI_ID', NULL)
                                        ->get();
                                        // dd($duplicateCheck);
                if(sizeof($duplicateCheck) == 0 ){
                    DB::table('FIN_ADMISSION_SCHOLARSHIP_CRITERIA')->insert([
                        'FASC_SCHOOL_ID' => $schoolId,
                        'FASC_DEPT_ID' => $deptId,
                        'FASC_PROG_ID' => $progId,
                        'FASC_SESSION_ID' => $session,
                        'FASC_DEGREE_LEVEL' => $request->get('degree_level'),
                        'FASC_ENTRY_USER_ID' => $userId,
                        // 'FASC_ACADEMIC_YEAR_ID' => $request->get('academic_year'),
                        'FASC_SCHOLARSHIP_ID' => $request->get('scholarship'),
                        'FASC_PERCENT_FROM' => $request->get('cgpa_min'),
                        'FASC_PERCENT_TO' => $request->get('cgpa_max'),
                        'FASC_SCHLRSHP_PERCENTAGE' => $request->get('percentage'),
                        // 'FASC_EFF_FROM' => $request->get('effective_from'),
                        // 'FASC_EFF_TO' => $request->get('effective_to'),
                        'FASC_STATUS' => 'E',
                        'FASC_CREATED_AT' => now(),
                    ]);
                }else{
                    // DB::table('FIN_ADMISSION_SCHOLARSHIP_CRITERIA')
                    // ->where('FASC_ID', $duplicateCheck[0]->fasc_id)
                    // ->update([
                    //     'FASC_SCHOOL_ID' => $schoolId,
                    //     'FASC_DEPT_ID' => $deptId,
                    //     'FASC_PROG_ID' => $progId,
                    //     'FASC_SESSION_ID' => $session,
                    //     'FASC_SCHOLARSHIP_ID' => $request->get('scholarship'),
                    //     'FASC_PERCENT_FROM' => $request->get('cgpa_min'),
                    //     'FASC_PERCENT_TO' => $request->get('cgpa_max'),
                    //     'FASC_SCHLRSHP_PERCENTAGE' => $request->get('percentage'),
                    //     'FASC_EFF_FROM' => $request->get('effective_from'),
                    //     'FASC_EFF_TO' => $request->get('effective_to'),
                    //     'FASC_STATUS' => 'E',
                    //     'FASC_CREATED_AT' => now(),
                    // ]);
                    $resp[0]=false;
                    $resp[1]='Percentage conflict occured!';
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
                $duplicateCheck = DB::table('FIN_ADMISSION_SCHOLARSHIP_CRITERIA')
                                    ->select(
                                            'FASC_ID',
                                            'FASC_SESSION_ID',
                                            'FASC_SCHOOL_ID',
                                            'FASC_DEPT_ID',
                                            'FASC_PROG_ID',
                                            'FASC_DEGREE_LEVEL',
                                            'FASC_SCHOLARSHIP_ID',
                                            'FASC_PERCENT_FROM',
                                            'FASC_PERCENT_TO'
                                            )
                                            ->where('FASC_SESSION_ID', $session)
                                            ->where('FASC_SCHOOL_ID', $schoolId)
                                            ->where('FASC_DEPT_ID', $deptId)
                                            ->where('FASC_PROG_ID', $progId)
                                            ->where('FASC_DEGREE_LEVEL', $request->get('degree_level'))
                                            ->where('FASC_SCHOLARSHIP_ID', $request->get('scholarship'))
                                            // ->where('FASC_PERCENT_FROM', $request->get('cgpa_min'))
                                            // ->where('FASC_PERCENT_TO', $request->get('cgpa_max'))
                                            ->where('FASC_STATUS', 'E')
                                            ->where('FASC_AAI_ID', NULL)
                                            ->where('FASC_ID', '!=', $request->mainId)
                                            ->where(function($query) use ($request) {
                                                $cgpaMin = $request->get('cgpa_min');
                                                $cgpaMax = $request->get('cgpa_max');

                                                $query->where(function($query) use ($cgpaMin, $cgpaMax) {
                                                    $query->where('FASC_PERCENT_FROM', $cgpaMin)
                                                        ->orWhere('FASC_PERCENT_FROM', $cgpaMax)
                                                        ->orWhere('FASC_PERCENT_TO', $cgpaMin)
                                                        ->orWhere('FASC_PERCENT_TO', $cgpaMax)
                                                        ->orWhereBetween('FASC_PERCENT_FROM', [$cgpaMin, $cgpaMax])
                                                        ->orWhereBetween('FASC_PERCENT_TO', [$cgpaMin, $cgpaMax])
                                                        ->orWhere(function($query) use ($cgpaMin, $cgpaMax) {
                                                            $query->where('FASC_PERCENT_FROM', '>', $cgpaMin)
                                                                  ->where('FASC_PERCENT_TO', '<', $cgpaMax);
                                                        });
                                                });
                                            })
                                            ->get();
                                            // dd($duplicateCheck);
                if(sizeof($duplicateCheck) == 0 ){
                    try {
                        DB::table('FIN_ADMISSION_SCHOLARSHIP_CRITERIA')
                                ->where('FASC_ID', $request->mainId)
                                ->update([
                                    'FASC_SCHOOL_ID' => $schoolId,
                                    'FASC_DEPT_ID' => $deptId,
                                    'FASC_PROG_ID' => $progId,
                                    'FASC_SESSION_ID' => $session,
                                    'FASC_DEGREE_LEVEL' => $request->get('degree_level'),
                                    'FASC_ENTRY_USER_ID' => $userId,
                                    // 'FASC_ACADEMIC_YEAR_ID' => $request->get('academic_year'),
                                    'FASC_SCHOLARSHIP_ID' => $request->get('scholarship'),
                                    'FASC_PERCENT_FROM' => $request->get('cgpa_min'),
                                    'FASC_PERCENT_TO' => $request->get('cgpa_max'),
                                    'FASC_SCHLRSHP_PERCENTAGE' => $request->get('percentage'),
                                    // 'FASC_EFF_FROM' => $request->get('effective_from'),
                                    // 'FASC_EFF_TO' => $request->get('effective_to'),
                                    'FASC_STATUS' => 'E',
                                    'FASC_CREATED_AT' => now(),
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
                    $resp[1]='Criteria already exist or percentage conflict occured';
                    return $resp;
                }
            }
            $resp[0]=true;
            $resp[1]='Updated Successfully';
            return $resp;
        }else{
            $resp[0]=false;
            $resp[1]='You can edit one program at a time';
            return $resp;
        }
    }
    protected function getScholarshipCriteriaList(Request $request){
        ////////////TO get the sessions/////////////////
        $validityType = $request->get('validity_type');
        // dd($validityType);
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
        // dd($session);
        $data = DB::table('FIN_ADMISSION_SCHOLARSHIP_CRITERIA as fasc')
            ->select(
                    'fasc.FASC_ID',
                    'fasc.FASC_SESSION_ID',
                    'fasc.FASC_SCHOOL_ID',
                    'fasc.FASC_DEPT_ID',
                    'fasc.FASC_PROG_ID',
                    'fasc.FASC_DEGREE_LEVEL',
                    'fasc.FASC_SCHOLARSHIP_ID',
                    'fasc.FASC_PERCENT_FROM',
                    'fasc.FASC_PERCENT_TO',
                    'fasc.FASC_SCHLRSHP_PERCENTAGE',
                    // 'fasc.FASC_EFF_FROM',
                    // 'fasc.FASC_EFF_TO',

                    'st.SCHOLARSHIP_NAME',
                    'acd.SESS_NAME',
                    'school.SCHOOL_NAME',
                    'dept.DEPT_NAME',
                    'prog.PROG_NAME'
                    )
                    ->join('SCHOLARSHIP_TYPE as st', 'st.SCHOLARSHIP_ID', 'fasc.FASC_SCHOLARSHIP_ID')
                    ->join('ACADEMIC_SESSIONS as acd', 'acd.SESS_ID', 'fasc.FASC_SESSION_ID')
                    ->join('SCHOOLS as school', 'school.SCHOOL_ID', 'fasc.FASC_SCHOOL_ID')
                    ->join('DEPARTMENTS as dept', 'dept.DEPT_ID', 'fasc.FASC_DEPT_ID')
                    ->join('PROGRAMS as prog', 'prog.PROG_ID', 'fasc.FASC_PROG_ID')
                    ->where('fasc.FASC_STATUS', 'E')
                    ->where('fasc.FASC_AAI_ID', NULL);//because same table is used to keep individual schlrships and aaid is present for those who are given individual schlrshp but we want here only general criteiras
                    // ->join('fin_fee_category as fincateg', 'fincateg.id', 'ffs.FFS_FEECATEGORY')
                    if(!empty($session)){
                        $data =  $data->whereIn('fasc.FASC_SESSION_ID', $session);
                    }
                    if(!empty($request->school_sel) && !is_array(json_decode($request->school_sel))){//condition will applied only when single school is selected if all school is slected then no need to apply condition//array means all school is selected and so we apply not array condtion to apply where clause;
                       $data =  $data->where('fasc.FASC_SCHOOL_ID', $request->school_sel);
                    }
                    if(!empty($request->department) && !is_array(json_decode($request->department))){//condition will applied only when single department is selected if all department is slected then no need to apply condition//array means all department is selected and so we apply not array condtion to apply where clause;
                        $data =  $data->where('fasc.FASC_DEPT_ID', $request->department);
                    }
                    if(!empty($request->program) && !is_array(json_decode($request->program))){//condition will applied only when single program is selected if all program is slected then no need to apply condition//array means all program is selected and so we apply not array condtion to apply where clause;
                        //    dd('program condtion applied');
                            $programs_id = $request->program;
                            // dd($programs_id);
                            $array_of_sch_dep_prog = explode('~~', $programs_id);
                            // dd($array_of_sch_dep_prog[2]);
                           $data =  $data->where('fasc.FASC_PROG_ID', $array_of_sch_dep_prog[2]);
                        }
                    if(!empty($request->scholarship_id)){
                       $data =  $data->where('fasc.FASC_SCHOLARSHIP_ID', $request->scholarship_id);
                    }
                    if(!empty($request->degree_level)){
                       $data =  $data->where('fasc.FASC_DEGREE_LEVEL', $request->degree_level);
                    }
                    if(!empty($request->percent)){
                       $data =  $data->where('fasc.FASC_SCHLRSHP_PERCENTAGE', $request->percent);
                    }
                    if(!empty($request->cgpaFrom)){
                       $data =  $data->where('fasc.FASC_PERCENT_FROM', $request->cgpaFrom);
                    }
                    if(!empty($request->cgpaTo)){
                       $data =  $data->where('fasc.FASC_PERCENT_TO', $request->cgpaTo);
                    }
                    $data = $data->orderBy('fasc.FASC_ID', 'desc')
                                    ->get();
                    // dd($data);
        return DataTables::of($data)->toJson();
	}
    public function delete_scholarshipCriteria(Request $request){
        $userId = auth()->user()->id;
        $affectedRows =  DB::table('FIN_ADMISSION_SCHOLARSHIP_CRITERIA')
                            ->whereIn('FASC_ID', $request->mainId)
                            ->update([
                                'FASC_STATUS' => 'D',
                                'FASC_ENTRY_USER_ID' => $userId,
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
    protected function getProgramFeeStructureForm(Request $request){
        $dept_id = json_decode($request->get('dept_id'));
        // dd($dept_id);
        $programs=[];
        if(!empty($dept_id)){
            if(is_array($dept_id)){//in case of all departments selected
                foreach($dept_id as $val){
                    $dept_idsss[] = explode('~~', $val)[1];
                }
                $programs = DB::table('programs')
                    ->whereIn('prog_dept_id', $dept_idsss)
                    ->where('PROG_STATUS','=', 'E')
                    ->orderBy('prog_name', 'asc')
                    ->get();
            }else{
                $programs = DB::table('programs')
                    ->where('prog_dept_id', $dept_id)
                    ->where('PROG_STATUS','=', 'E')
                    ->orderBy('prog_name', 'asc')
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

}

