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

class AdmissionIndividualScholarshipCriteriaController extends Controller{


    function masterAdmissionSclrshpCriteriaForm(){
        $sessions = \App::call('App\Http\Controllers\Grader\AcademicSessionController@schlrshipSessionRetrieve');
        $schools =  Schools::all();
        $scholarships = DB::select("select * from SCHOLARSHIP_TYPE");
        $departments = DB::select("select * from DEPARTMENTS");
        $programs =  DB::select("select * from Programs");
        $academicYears =  DB::select("select * from ACADEMIC_YEARS where AY_STATUS = 'E' ORDER BY AY_ORDER desc");
        $query = 'select * from fin_fee_category';
        $feeCategories = DB::select($query);
        return view('StudentFinancial.master_admission_individualschlrshp_criteria',compact('sessions','schools','departments','scholarships','feeCategories','programs', 'academicYears'));

    }
    public function filter_admission_applicants(Request $request){
        $sessionId = $request->session_id;
        $schoolId = $request->school_id;
        $deptId = $request->department_id;
        $programId = $request->program_id;
        // dd($sessionId, $schoolId, $deptId, $programId);
        $sqlMeritList = "SELECT
                        aai_sdpo_sess_id,
                        aai_sdpo_school_id,
                        aai_sdpo_dept_id,
                        aai_sdpo_prog_id,
                        aai_test_marks,
                        aai_interview_marks,
                        api_id,
                        api_name,
                        api_fathername,
                        api_email,
                        api_imagename,
                        aai_id,
                        aai_api_form_id,
                        school_code,
                        aai_programid,
                        aai_degreecode,
                        aai_aggregate_required,
                        aai_test_aggregate,
                        aai_interview_aggregate,
                        (aai_aggregate_required + aai_test_aggregate + aai_interview_aggregate) AS total_aggregate,
                        aai_status,

                        API_NEEDBASE_SCHOLARSHIP,
                        API_TRANSGENDERBASE,
                        API_DEANSBASE,
                        API_SPORTSBASE,
                        API_BNUINSTITUTIONALPATRONAGE,
                        API_VCSCHOLARSHIP,
                        AQ_DEGREE_NAME,
                        CASE
                            WHEN AQ_DEGREE_NAME = 'A_Level' THEN ADQ_EQUAL_OBTAIN_MARKS
                            WHEN AQ_DEGREE_NAME = 'Intermediate' THEN AQ_OBTAINED_MARKS
                            ELSE NULL
                        END AS degree_specific_obtained_marks,
                        CASE
                            WHEN AQ_DEGREE_NAME = 'A_Level' THEN ADQ_EQUAL_TOTAL_MARKS
                            WHEN AQ_DEGREE_NAME = 'Intermediate' THEN AQ_TOTAL_MARKS
                            ELSE NULL
                        END AS degree_specific_total_marks
                        FROM admission_apply_info
                        LEFT JOIN admission_personal_info ON api_id = aai_api_id
                        LEFT JOIN admission_qualification ON api_id = aq_api_id
                        LEFT JOIN (
                            SELECT AQD_AQ_ID,
                                MAX(CASE WHEN AQD_DEGREE_NAME = 'A_Level' THEN ADQ_EQUAL_OBTAIN_MARKS END) AS ADQ_EQUAL_OBTAIN_MARKS,
                                MAX(CASE WHEN AQD_DEGREE_NAME = 'A_Level' THEN ADQ_EQUAL_TOTAL_MARKS END) AS ADQ_EQUAL_TOTAL_MARKS
                            FROM admission_qualification_detail
                            GROUP BY AQD_AQ_ID
                        ) AQD ON AQ_ID = AQD_AQ_ID
                        LEFT JOIN schools ON school_id = aai_sdpo_school_id
                        WHERE aai_api_id=api_id
                        and aai_pf_status='Fee Verified'
                        and aai_qualification_status = 'Accepted'
                        and AQ_DEGREE_NAME IN ('Intermediate', 'A_Level')";

        if(!empty($sessionId)){
        $sqlMeritList .= " and aai_sdpo_sess_id = '$sessionId'";
        }
        if(!empty($schoolId)){
        $sqlMeritList .=" and aai_sdpo_school_id = '$schoolId'";
        }

        if(!empty($deptId)){
        $sqlMeritList .=" and aai_sdpo_dept_id = '$deptId'";
        }
        if(!empty($programId)){
        $sqlMeritList .=" and aai_sdpo_prog_id = '$programId'";
        }
        // dd($sqlMeritList);
        $sqlMeritList .= " and ROWNUM <= 5000";
        // dd($sqlMeritList);
        $resSelectMeritList = DB::select($sqlMeritList);
        // dd($resSelectMeritList);
        return DataTables::of($resSelectMeritList)->toJson();
    }
    public function addAdmissionScholarshipCriteria(Request $request){
        // dd((float) $request->get('marks_percent'));
        $rules = [
            'scholarship' => 'required',
            'percentage' => 'required|numeric|min:1|max:100'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return Response::json([
                'success' => false,
                'errors' => $validator->getMessageBag()->toArray()
            ], 400);
        }
        $userId = auth()->user()->id;
        $data = [
			'FASC_AAI_ID' => $request->get('applicantId'),
			'FASC_SESSION_ID' =>$request->get('session'),
			'FASC_SCHOOL_ID' =>$request->get('school'),
			'FASC_DEPT_ID' => $request->get('department'),
			'FASC_PROG_ID' => $request->get('program'),
			'FASC_SCHOLARSHIP_ID' => $request->get('scholarship'),
            'FASC_ENTRY_USER_ID' => $userId,
            'FASC_OBTAINED_MARKS' => $request->get('obt_marks'),
            'FASC_TOTAL_MARKS' => $request->get('total_marks'),

			'FASC_SCHLRSHP_PERCENTAGE' => $request->get('percentage'),
            'FASC_STATUS' => 'E',
            'FASC_SCHOLARSHIP_STATUS' => 'Disapproved'
        ];
        // dd($data);
        // DB::table('FIN_ADMISSION_SCHOLARSHIP_CRITERIA')->insert($data);
        try {
            DB::table('FIN_ADMISSION_SCHOLARSHIP_CRITERIA')->insert($data);

            return response()->json([
                'success' => true,
                'msg' => 'Data inserted successfully'
            ]);
        } catch (\Exception $e) {
            // If there is an error, return a failure response
            return response()->json([
                'success' => false,
                'msg' => 'Data insertion failed: ' . $e->getMessage()
            ]);
        }

    }
    // private static function allProgramsInsertion($school_depart_prog, $request, $session){
    //     if(empty($request->mainId)){//insertion case
    //         foreach($school_depart_prog as $val){
    //             $schoolId = $val[0];
    //             $deptId = $val[1];
    //             $progId = $val[2];
    //             $duplicateCheck = DB::table('FIN_SCHOLARSHIP_CRITERIA')
    //                             ->select(
    //                                     'FSC_ID',
    //                                     'FSC_SESSION_ID',
    //                                     'FSC_SCHOOL_ID',
    //                                     'FSC_DEPT_ID',
    //                                     'FSC_PROG_ID',
    //                                     'FSC_SCHOLARSHIP_ID',
    //                                     'FSC_CGPA_FROM',
    //                                     'FSC_CGPA_TO'
    //                                     )
    //                                     ->where('FSC_SESSION_ID', $session)
    //                                     ->where('FSC_SCHOOL_ID', $schoolId)
    //                                     ->where('FSC_DEPT_ID', $deptId)
    //                                     ->where('FSC_PROG_ID', $progId)
    //                                     ->where('FSC_SCHOLARSHIP_ID', $request->get('scholarship'))
    //                                     // ->where('FSC_CGPA_FROM', $request->get('cgpa_min'))
    //                                     // ->where('FSC_CGPA_TO', $request->get('cgpa_max'))
    //                                     ->where(function($query) use ($request) {
    //                                         $cgpaMin = $request->get('cgpa_min');
    //                                         $cgpaMax = $request->get('cgpa_max');

    //                                         $query->where(function($query) use ($cgpaMin, $cgpaMax) {
    //                                             $query->where('FSC_CGPA_FROM', $cgpaMin)
    //                                                 ->orWhere('FSC_CGPA_FROM', $cgpaMax)
    //                                                 ->orWhere('FSC_CGPA_TO', $cgpaMin)
    //                                                 ->orWhere('FSC_CGPA_TO', $cgpaMax)
    //                                                 ->orWhereBetween('FSC_CGPA_FROM', [$cgpaMin, $cgpaMax])
    //                                                 ->orWhereBetween('FSC_CGPA_TO', [$cgpaMin, $cgpaMax])
    //                                                 ->orWhere(function($query) use ($cgpaMin, $cgpaMax) {
    //                                                     $query->where('FSC_CGPA_FROM', '>', $cgpaMin)
    //                                                           ->where('FSC_CGPA_TO', '<', $cgpaMax);
    //                                                 });
    //                                         });
    //                                     })
    //                                     ->where('FSC_STATUS', 'E')
    //                                     ->get();
    //                                     // dd($duplicateCheck);
    //             if(sizeof($duplicateCheck) == 0 ){
    //                 DB::table('FIN_SCHOLARSHIP_CRITERIA')->insert([
    //                     'FSC_SCHOOL_ID' => $schoolId,
    //                     'FSC_DEPT_ID' => $deptId,
    //                     'FSC_PROG_ID' => $progId,
    //                     'FSC_SESSION_ID' => $session,
    //                     // 'FSC_ACADEMIC_YEAR_ID' => $request->get('academic_year'),
    //                     'FSC_SCHOLARSHIP_ID' => $request->get('scholarship'),
    //                     'FSC_CGPA_FROM' => $request->get('cgpa_min'),
    //                     'FSC_CGPA_TO' => $request->get('cgpa_max'),
    //                     'FSC_PERCENTAGE' => $request->get('percentage'),
    //                     'FSC_EFF_FROM' => $request->get('effective_from'),
    //                     'FSC_EFF_TO' => $request->get('effective_to'),
    //                     'FSC_STATUS' => 'E',
    //                     'FSC_CREATED_AT' => now(),
    //                 ]);
    //             }else{
    //                 // DB::table('FIN_SCHOLARSHIP_CRITERIA')
    //                 // ->where('FSC_ID', $duplicateCheck[0]->fsc_id)
    //                 // ->update([
    //                 //     'FSC_SCHOOL_ID' => $schoolId,
    //                 //     'FSC_DEPT_ID' => $deptId,
    //                 //     'FSC_PROG_ID' => $progId,
    //                 //     'FSC_SESSION_ID' => $session,
    //                 //     'FSC_SCHOLARSHIP_ID' => $request->get('scholarship'),
    //                 //     'FSC_CGPA_FROM' => $request->get('cgpa_min'),
    //                 //     'FSC_CGPA_TO' => $request->get('cgpa_max'),
    //                 //     'FSC_PERCENTAGE' => $request->get('percentage'),
    //                 //     'FSC_EFF_FROM' => $request->get('effective_from'),
    //                 //     'FSC_EFF_TO' => $request->get('effective_to'),
    //                 //     'FSC_STATUS' => 'E',
    //                 //     'FSC_CREATED_AT' => now(),
    //                 // ]);
    //                 $resp[0]=false;
    //                 $resp[1]='CGPA conflict occured!';
    //                 return $resp;
    //             }


    //         }
    //         $resp[0]=true;
    //         $resp[1]='Saved Successfully';
    //         return $resp;
    //     }else if(sizeof($school_depart_prog) == 1){ //edit case
    //         if(!empty($school_depart_prog[0][0])){//checking if the allprograms option is selected and there is no program in it
    //             $schoolId = $school_depart_prog[0][0];
    //             $deptId = $school_depart_prog[0][1];
    //             $progId = $school_depart_prog[0][2];
    //             DB::table('FIN_SCHOLARSHIP_CRITERIA')
    //                     ->where('FSC_ID', $request->mainId)
    //                     ->update([
    //                         'FSC_SCHOOL_ID' => $schoolId,
    //                         'FSC_DEPT_ID' => $deptId,
    //                         'FSC_PROG_ID' => $progId,
    //                         'FSC_SESSION_ID' => $session,
    //                         // 'FSC_ACADEMIC_YEAR_ID' => $request->get('academic_year'),
    //                         'FSC_SCHOLARSHIP_ID' => $request->get('scholarship'),
    //                         'FSC_CGPA_FROM' => $request->get('cgpa_min'),
    //                         'FSC_CGPA_TO' => $request->get('cgpa_max'),
    //                         'FSC_PERCENTAGE' => $request->get('percentage'),
    //                         'FSC_EFF_FROM' => $request->get('effective_from'),
    //                         'FSC_EFF_TO' => $request->get('effective_to'),
    //                         'FSC_STATUS' => 'E',
    //                         'FSC_CREATED_AT' => now(),
    //                     ]);
    //         }
    //         $resp[0]=true;
    //         $resp[1]='Updated Successfully';
    //         return $resp;
    //     }else{
    //         $resp[0]=false;
    //         $resp[1]='You can edit one program at a time';
    //         return $resp;

    //     }


    // }
    // protected function getScholarshipCriteriaList(Request $request){

    //     $data = DB::table('FIN_SCHOLARSHIP_CRITERIA as fsc')
    //         ->select(
    //                 'fsc.FSC_ID',
    //                 'fsc.FSC_SESSION_ID',
    //                 'fsc.FSC_SCHOOL_ID',
    //                 'fsc.FSC_DEPT_ID',
    //                 'fsc.FSC_PROG_ID',
    //                 'fsc.FSC_SCHOLARSHIP_ID',
    //                 'fsc.FSC_CGPA_FROM',
    //                 'fsc.FSC_CGPA_TO',
    //                 'fsc.FSC_PERCENTAGE',
    //                 'fsc.FSC_EFF_FROM',
    //                 'fsc.FSC_EFF_TO',

    //                 'st.SCHOLARSHIP_NAME',
    //                 'acd.SESS_NAME',
    //                 'school.SCHOOL_NAME',
    //                 'dept.DEPT_NAME',
    //                 'prog.PROG_NAME'
    //                 )
    //                 ->join('SCHOLARSHIP_TYPE as st', 'st.SCHOLARSHIP_ID', 'fsc.FSC_SCHOLARSHIP_ID')
    //                 ->join('ACADEMIC_SESSIONS as acd', 'acd.SESS_ID', 'fsc.FSC_SESSION_ID')
    //                 ->join('SCHOOLS as school', 'school.SCHOOL_ID', 'fsc.FSC_SCHOOL_ID')
    //                 ->join('DEPARTMENTS as dept', 'dept.DEPT_ID', 'fsc.FSC_DEPT_ID')
    //                 ->join('PROGRAMS as prog', 'prog.PROG_ID', 'fsc.FSC_PROG_ID')
    //                 ->where('fsc.FSC_STATUS', 'E')
    //                 // ->join('fin_fee_category as fincateg', 'fincateg.id', 'ffs.FFS_FEECATEGORY')
    //                 ->orderBy('fsc.FSC_ID', 'desc')
    //                 ->get();
    //                 // dd($data);
    //     return DataTables::of($data)->toJson();
	// }
    // public function delete_scholarshipCriteria(Request $request){
    //     $affectedRows =  DB::table('FIN_SCHOLARSHIP_CRITERIA')
    //                         ->whereIn('FSC_ID', $request->mainId)
    //                         ->update([
    //                             'FSC_STATUS' => 'D'
    //                         ]);
    //     if ($affectedRows > 0) {
    //         return response()->json([
    //             'success' => true,
    //             'data' => 'Deleted Successfully'
    //     ]);
    //     } else {
    //         return response()->json([
    //             'success' => false,
    //             'data' => 'Some problem occured'
    //         ]);
    //     }

    // }
    // protected function getProgramFeeStructureForm(Request $request){
    //     $dept_id = json_decode($request->get('dept_id'));
    //     // dd($dept_id);
    //     $programs=[];
    //     if(!empty($dept_id)){
    //         if(is_array($dept_id)){//in case of all departments selected
    //             foreach($dept_id as $val){
    //                 $dept_idsss[] = explode('~~', $val)[1];
    //             }
    //             $programs = DB::table('programs')
    //                 ->whereIn('prog_dept_id', $dept_idsss)
    //                 ->where('PROG_STATUS','=', 'E')
    //                 ->orderBy('prog_name', 'asc')
    //                 ->get();
    //         }else{
    //             $programs = DB::table('programs')
    //                 ->where('prog_dept_id', $dept_id)
    //                 ->where('PROG_STATUS','=', 'E')
    //                 ->orderBy('prog_name', 'asc')
    //                 ->get();
    //         }
    //         // dd($programs);

    //     }
    //     return response()->json(['data' => $programs, 'status'=> true, 'message' => count($programs).' record(s) found!']);
    // }
    // protected function getDepartmentsFeeStructureForm(Request $request){
    //     // dd($request->get('school_id'));
    //     $school_id = json_decode($request->get('school_id'));
    //     $departments=[];
    //     if(!empty($school_id)){
    //         if(is_array($school_id)){
    //             $departments = DB::table('departments')
    //                 ->where('dept_status', '=', 'E')
    //                 ->whereIn('dept_school_id', $school_id)
    //                 ->orderBy('DEPT_NAME', 'asc')
    //                 ->get();
    //         }else{
    //             $departments = DB::table('departments')
    //                 ->where('dept_status', '=', 'E')
    //                 ->where('dept_school_id', $school_id)
    //                 ->orderBy('DEPT_NAME', 'asc')
    //                 ->get();
    //         }
    //         // dd($departments);

    //     }
    //     return response()->json(['data' => $departments, 'status'=> true, 'message' => count($departments).' record(s) found!']);
    // }

}

