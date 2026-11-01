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

class ScholarshipCriteriaController extends Controller{


    function masterSclrshpCriteriaForm(){
        $sessions = \App::call('App\Http\Controllers\Grader\AcademicSessionController@schlrshipSessionRetrieve');
        $schools = Schools::where('school_status', 'E')->get();
        $scholarships = DB::select("select * from SCHOLARSHIP_TYPE");
        $departments = array(); // $this->school->fetch();
        $programs =  DB::select("select * from Programs where PROG_STATUS = 'E'");
        $academicYears =  DB::select("select * from ACADEMIC_YEARS where AY_STATUS = 'E' ORDER BY AY_ORDER desc");
        $query = 'select * from fin_fee_category';
        $feeCategories = DB::select($query);
        return view('StudentFinancial.master_schlrshp_criteria',compact('sessions','schools','departments','scholarships','feeCategories','programs', 'academicYears'));

    }
    public function addScholarshipCriteria(Request $request){
        // dd($request->all());
        $validityType = $request->get('validity-type');

        $rules = [
            'school' => 'required',
            'department' => 'required',
            'program' => 'required',
            'roadmap' => 'required',
            'degree_level' => 'required',
            'session' => 'numeric', // Default to numeric, will adjust based on validity-type
            'academic_year' => 'numeric', // Default to numeric, will adjust based on validity-type
            'scholarship' => 'required',
            // 'effective_from' => 'required',
            // 'effective_to' => 'required',
            'percentage' => 'required|numeric|min:1|max:100',
            'cgpa_min' => 'required|min:1|max:4',
            'cgpa_max' => 'required|min:1|max:4',
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
        if($request->get('cgpa_min') > 4 || $request->get('cgpa_max') > 4 || $request->get('cgpa_min') < 1 || $request->get('cgpa_max') < 1){
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
        if($request->roadmap == 'new'){
            $roadmap_session = 162;//Fall 2024
        }else{
            $roadmap_session = 160;//Spring 2024
        }

        // dd($request->roadmap, $roadmap_session);
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
            $resp = $this->allProgramsInsertion($school_depart_prog, $request, $session,$roadmap_session);

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
                        $resp = $this->allProgramsInsertion($school_depart_prog, $request, $session,$roadmap_session);
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
    private static function allProgramsInsertion($school_depart_prog, $request, $session,$roadmap_session){
        $userId = auth()->user()->id;
        if(empty($request->mainId)){//insertion case
            foreach($school_depart_prog as $val){
                $schoolId = $val[0];
                $deptId = $val[1];
                $progId = $val[2];
                $duplicateCheck = DB::table('FIN_SCHOLARSHIP_CRITERIA')
                                ->select(
                                        'FSC_ID',
                                        'FSC_SESSION_ID',
                                        'FSC_SCHOOL_ID',
                                        'FSC_ROADMAP_SESSION_ID',
                                        'FSC_DEPT_ID',
                                        'FSC_PROG_ID',
                                        'FSC_DEGREE_LEVEL',
                                        'FSC_SCHOLARSHIP_ID',
                                        'FSC_CGPA_FROM',
                                        'FSC_CGPA_TO'
                                        )
                                        ->where('FSC_SESSION_ID', $session)
                                        ->where('FSC_SCHOOL_ID', $schoolId)
                                        ->where('FSC_DEPT_ID', $deptId)
                                        ->where('FSC_PROG_ID', $progId)
                                        ->where('FSC_ROADMAP_SESSION_ID', $roadmap_session)
                                        ->where('FSC_DEGREE_LEVEL', $request->get('degree_level'))
                                        ->where('FSC_SCHOLARSHIP_ID', $request->get('scholarship'))
                                        // ->where('FSC_CGPA_FROM', $request->get('cgpa_min'))
                                        // ->where('FSC_CGPA_TO', $request->get('cgpa_max'))
                                        ->where(function($query) use ($request) {
                                            $cgpaMin = $request->get('cgpa_min');
                                            $cgpaMax = $request->get('cgpa_max');

                                            $query->where(function($query) use ($cgpaMin, $cgpaMax) {
                                                $query->where('FSC_CGPA_FROM', $cgpaMin)
                                                    ->orWhere('FSC_CGPA_FROM', $cgpaMax)
                                                    ->orWhere('FSC_CGPA_TO', $cgpaMin)
                                                    ->orWhere('FSC_CGPA_TO', $cgpaMax)
                                                    ->orWhereBetween('FSC_CGPA_FROM', [$cgpaMin, $cgpaMax])
                                                    ->orWhereBetween('FSC_CGPA_TO', [$cgpaMin, $cgpaMax])
                                                    ->orWhere(function($query) use ($cgpaMin, $cgpaMax) {
                                                        $query->where('FSC_CGPA_FROM', '>', $cgpaMin)
                                                              ->where('FSC_CGPA_TO', '<', $cgpaMax);
                                                    });
                                            });
                                        })
                                        ->where('FSC_STATUS', 'E')
                                        ->get();
                                        // dd($duplicateCheck);
                if(sizeof($duplicateCheck) == 0 ){
                    DB::table('FIN_SCHOLARSHIP_CRITERIA')->insert([
                        'FSC_SCHOOL_ID' => $schoolId,
                        'FSC_DEPT_ID' => $deptId,
                        'FSC_PROG_ID' => $progId,
                        'FSC_SESSION_ID' => $session,
                        'FSC_DEGREE_LEVEL' => $request->get('degree_level'),
                        'FSC_ENTRY_USER_ID' => $userId,
                        'FSC_ROADMAP_SESSION_ID' => $roadmap_session,
                        'FSC_ROADMAP' => $request->get('roadmap'),

                        // 'FSC_ACADEMIC_YEAR_ID' => $request->get('academic_year'),
                        'FSC_SCHOLARSHIP_ID' => $request->get('scholarship'),
                        'FSC_CGPA_FROM' => $request->get('cgpa_min'),
                        'FSC_CGPA_TO' => $request->get('cgpa_max'),
                        'FSC_PERCENTAGE' => $request->get('percentage'),
                        'FSC_EFF_FROM' => $request->get('effective_from'),
                        'FSC_EFF_TO' => $request->get('effective_to'),
                        'FSC_STATUS' => 'E',
                        'FSC_CREATED_AT' => now(),
                    ]);
                }else{
                    // DB::table('FIN_SCHOLARSHIP_CRITERIA')
                    // ->where('FSC_ID', $duplicateCheck[0]->fsc_id)
                    // ->update([
                    //     'FSC_SCHOOL_ID' => $schoolId,
                    //     'FSC_DEPT_ID' => $deptId,
                    //     'FSC_PROG_ID' => $progId,
                    //     'FSC_SESSION_ID' => $session,
                    //     'FSC_SCHOLARSHIP_ID' => $request->get('scholarship'),
                    //     'FSC_CGPA_FROM' => $request->get('cgpa_min'),
                    //     'FSC_CGPA_TO' => $request->get('cgpa_max'),
                    //     'FSC_PERCENTAGE' => $request->get('percentage'),
                    //     'FSC_EFF_FROM' => $request->get('effective_from'),
                    //     'FSC_EFF_TO' => $request->get('effective_to'),
                    //     'FSC_STATUS' => 'E',
                    //     'FSC_CREATED_AT' => now(),
                    // ]);
                    $resp[0]=false;
                    $resp[1]='CGPA conflict occured!';
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
                $duplicateCheck = DB::table('FIN_SCHOLARSHIP_CRITERIA')
                                    ->select(
                                            'FSC_ID',
                                            'FSC_SESSION_ID',
                                            'FSC_SCHOOL_ID',
                                            'FSC_DEPT_ID',
                                            'FSC_PROG_ID',
                                            'FSC_ROADMAP_SESSION_ID',
                                            'FSC_DEGREE_LEVEL',
                                            'FSC_SCHOLARSHIP_ID',
                                            'FSC_CGPA_FROM',
                                            'FSC_CGPA_TO'
                                            )
                                            ->where('FSC_SESSION_ID', $session)
                                            ->where('FSC_SCHOOL_ID', $schoolId)
                                            ->where('FSC_ROADMAP_SESSION_ID', $roadmap_session)
                                            ->where('FSC_DEPT_ID', $deptId)
                                            ->where('FSC_PROG_ID', $progId)
                                            ->where('FSC_DEGREE_LEVEL', $request->get('degree_level'))
                                            ->where('FSC_SCHOLARSHIP_ID', $request->get('scholarship'))
                                            // ->where('FSC_CGPA_FROM', $request->get('cgpa_min'))
                                            // ->where('FSC_CGPA_TO', $request->get('cgpa_max'))
                                            ->where('FSC_STATUS', 'E')
                                            ->where('FSC_ID', '!=', $request->mainId)
                                            ->where(function($query) use ($request) {
                                                $cgpaMin = $request->get('cgpa_min');
                                                $cgpaMax = $request->get('cgpa_max');

                                                $query->where(function($query) use ($cgpaMin, $cgpaMax) {
                                                    $query->where('FSC_CGPA_FROM', $cgpaMin)
                                                        ->orWhere('FSC_CGPA_FROM', $cgpaMax)
                                                        ->orWhere('FSC_CGPA_TO', $cgpaMin)
                                                        ->orWhere('FSC_CGPA_TO', $cgpaMax)
                                                        ->orWhereBetween('FSC_CGPA_FROM', [$cgpaMin, $cgpaMax])
                                                        ->orWhereBetween('FSC_CGPA_TO', [$cgpaMin, $cgpaMax])
                                                        ->orWhere(function($query) use ($cgpaMin, $cgpaMax) {
                                                            $query->where('FSC_CGPA_FROM', '>', $cgpaMin)
                                                                  ->where('FSC_CGPA_TO', '<', $cgpaMax);
                                                        });
                                                });
                                            })
                                            ->get();
                if(sizeof($duplicateCheck) == 0 ){
                    try {
                        DB::table('FIN_SCHOLARSHIP_CRITERIA')
                                ->where('FSC_ID', $request->mainId)
                                ->update([
                                    'FSC_SCHOOL_ID' => $schoolId,
                                    'FSC_DEPT_ID' => $deptId,
                                    'FSC_PROG_ID' => $progId,
                                    'FSC_SESSION_ID' => $session,
                                    'FSC_DEGREE_LEVEL' => $request->get('degree_level'),
                                    'FSC_ENTRY_USER_ID' => $userId,
                                    'FSC_ROADMAP_SESSION_ID' => $roadmap_session,
                                    'FSC_ROADMAP' => $request->get('roadmap'),

                                    // 'FSC_ACADEMIC_YEAR_ID' => $request->get('academic_year'),
                                    'FSC_SCHOLARSHIP_ID' => $request->get('scholarship'),
                                    'FSC_CGPA_FROM' => $request->get('cgpa_min'),
                                    'FSC_CGPA_TO' => $request->get('cgpa_max'),
                                    'FSC_PERCENTAGE' => $request->get('percentage'),
                                    'FSC_EFF_FROM' => $request->get('effective_from'),
                                    'FSC_EFF_TO' => $request->get('effective_to'),
                                    'FSC_STATUS' => 'E',
                                    'FSC_CREATED_AT' => now(),
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
                    $resp[1]='Criteria already exist or CGPA conflic occured';
                    return $resp;
                }
            }
        }else{
            $resp[0]=false;
            $resp[1]='You can edit one program at a time';
            return $resp;
        }
    }
    protected function getScholarshipCriteriaList(Request $request){
        // dd($request->all());
        //////////////TO get the sessions/////////////////
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
        $data = DB::table('FIN_SCHOLARSHIP_CRITERIA as fsc')
            ->select(
                    'fsc.FSC_ID',
                    'fsc.FSC_SESSION_ID',
                    'fsc.FSC_ROADMAP_SESSION_ID',
                    'fsc.FSC_ROADMAP',
                    'fsc.FSC_SCHOOL_ID',
                    'fsc.FSC_DEPT_ID',
                    'fsc.FSC_PROG_ID',
                    'fsc.FSC_DEGREE_LEVEL',
                    'fsc.FSC_SCHOLARSHIP_ID',
                    'fsc.FSC_CGPA_FROM',
                    'fsc.FSC_CGPA_TO',
                    'fsc.FSC_PERCENTAGE',
                    'fsc.FSC_EFF_FROM',
                    'fsc.FSC_EFF_TO',
                    DB::raw('(SELECT SESS_NAME FROM ACADEMIC_SESSIONS WHERE SESS_ID = fsc.FSC_ROADMAP_SESSION_ID) as FSC_ROADMAP_SESSION_NAME'),

                    'st.SCHOLARSHIP_NAME',
                    'acd.SESS_NAME',
                    'school.SCHOOL_NAME',
                    'dept.DEPT_NAME',
                    'prog.PROG_NAME'
                    )
                    ->join('SCHOLARSHIP_TYPE as st', 'st.SCHOLARSHIP_ID', 'fsc.FSC_SCHOLARSHIP_ID')
                    ->join('ACADEMIC_SESSIONS as acd', 'acd.SESS_ID', 'fsc.FSC_SESSION_ID')
                    ->join('SCHOOLS as school', 'school.SCHOOL_ID', 'fsc.FSC_SCHOOL_ID')
                    ->join('DEPARTMENTS as dept', 'dept.DEPT_ID', 'fsc.FSC_DEPT_ID')
                    ->join('PROGRAMS as prog', 'prog.PROG_ID', 'fsc.FSC_PROG_ID')
                    ->where('fsc.FSC_STATUS', 'E');
                    // ->join('fin_fee_category as fincateg', 'fincateg.id', 'ffs.FFS_FEECATEGORY')
                    if(!empty($session)){
                        $data =  $data->whereIn('fsc.FSC_SESSION_ID', $session);
                    }
                    if(!empty($request->school_sel) && !is_array(json_decode($request->school_sel))){//condition will applied only when single school is selected if all school is slected then no need to apply condition//array means all school is selected and so we apply not array condtion to apply where clause;
                       $data =  $data->where('fsc.FSC_SCHOOL_ID', $request->school_sel);
                    }
                    if(!empty($request->department) && !is_array(json_decode($request->department))){//condition will applied only when single department is selected if all department is slected then no need to apply condition//array means all department is selected and so we apply not array condtion to apply where clause;
                        $data =  $data->where('fsc.FSC_DEPT_ID', $request->department);
                    }
                    if(!empty($request->program) && !is_array(json_decode($request->program))){//condition will applied only when single program is selected if all program is slected then no need to apply condition//array means all program is selected and so we apply not array condtion to apply where clause;
                        //    dd('program condtion applied');
                            $programs_id = $request->program;
                            // dd($programs_id);
                            $array_of_sch_dep_prog = explode('~~', $programs_id);
                            // dd($array_of_sch_dep_prog[2]);
                           $data =  $data->where('fsc.FSC_PROG_ID', $array_of_sch_dep_prog[2]);
                        }
                    if(!empty($request->scholarship_id)){
                       $data =  $data->where('fsc.FSC_SCHOLARSHIP_ID', $request->scholarship_id);
                    }
                    if(!empty($request->degree_level)){
                       $data =  $data->where('fsc.FSC_DEGREE_LEVEL', $request->degree_level);
                    }
                    if(!empty($request->percent)){
                       $data =  $data->where('fsc.FSC_PERCENTAGE', $request->percent);
                    }
                    if(!empty($request->cgpaFrom)){
                       $data =  $data->where('fsc.FSC_CGPA_FROM', $request->cgpaFrom);
                    }
                    if(!empty($request->cgpaTo)){
                       $data =  $data->where('fsc.FSC_CGPA_TO', $request->cgpaTo);
                    }
                    if(!empty($request->roadmap)){
                        $data =  $data->where('fsc.FSC_ROADMAP', $request->roadmap);
                     }
                    $data = $data->orderBy('fsc.FSC_ID', 'desc')
                            ->get();
                    // dd($data);
        return DataTables::of($data)->toJson();
	}
    public function delete_scholarshipCriteria(Request $request){
        $userId = auth()->user()->id;
        $affectedRows =  DB::table('FIN_SCHOLARSHIP_CRITERIA')
                            ->whereIn('FSC_ID', $request->mainId)
                            ->update([
                                'FSC_STATUS' => 'D',
                                'FSC_ENTRY_USER_ID' => $userId,
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

