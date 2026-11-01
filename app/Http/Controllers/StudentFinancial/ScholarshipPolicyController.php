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

class ScholarshipPolicyController extends Controller{



    //////////////NEW PAGE//////////////
    function masterSclrshpPolicyForm(){

        $sessions = \App::call('App\Http\Controllers\Grader\AcademicSessionController@schlrshipSessionRetrieve');
        $schools = Schools::where('school_status', 'E')->get();
        $scholarships = DB::select("select * from SCHOLARSHIP_TYPE");
        $departments = array(); // $this->school->fetch();
        $programs =  DB::select("select * from Programs where PROG_STATUS = 'E'");
        $academicYears =  DB::select("select * from ACADEMIC_YEARS where AY_STATUS = 'E' ORDER BY AY_ORDER desc");
        $query = 'select * from fin_fee_category';
        $feeCategories = DB::select($query);
        return view('StudentFinancial.master_schlrshp_policy',compact('sessions','schools','departments','scholarships','feeCategories','programs', 'academicYears'));

    }
    public function addScholarshipPolicy(Request $request){
        // dd($request->all());
        // dd(is_array(json_decode($request->school)));
        $validator = Validator::make($request->all(), [

            // ===== Required base fields =====
            'scholarship_category'   => 'required',
            'scholarship_type'       => 'required',
            'scholarship'            => 'required',
            'school'                 => 'required',
            'department'             => 'required',
            'degree_level'           => 'required',
            'program'                => 'required',
            'gender'                 => 'nullable|string',
            'no_of_scholarships'     => 'required|numeric',

            // ===== Fee fields: either percentage or amount required =====
            // 'security_fee_percent'   => 'nullable|numeric|required_without:security_fee_amount',
            // 'security_fee_amount'    => 'nullable|numeric|required_without:security_fee_percent',

            // 'admission_fee_percent'  => 'nullable|numeric|required_without:admission_fee_amount',
            // 'admission_fee_amount'   => 'nullable|numeric|required_without:admission_fee_percent',

            'tuition_fee_percent'    => 'nullable|numeric|required_without:tuition_fee_amount',
            'tuition_fee_amount'     => 'nullable|numeric|required_without:tuition_fee_percent',

            // 'activity_fee_percent'   => 'nullable|numeric|required_without:activity_fee_amount',
            // 'activity_fee_amount'    => 'nullable|numeric|required_without:activity_fee_percent',

            // 'hostel_coverage_percent'=> 'nullable|numeric|required_without:hostel_coverage_amount',
            // 'hostel_coverage_amount' => 'nullable|numeric|required_without:hostel_coverage_percent',

            // 'stipend_percent'        => 'nullable|numeric|required_without:stipend_amount',
            // 'stipend_amount'         => 'nullable|numeric|required_without:stipend_percent',

            // 'additional_credit_hour_fee_percent' => 'nullable|numeric|required_without:additional_credit_hour_fee_amount',
            // 'additional_credit_hour_fee_amount'  => 'nullable|numeric|required_without:additional_credit_hour_fee_percent',

            // 'other_incidental_fee_percent' => 'nullable|numeric|required_without:other_incidental_fee_amount',
            // 'other_incidental_fee_amount'  => 'nullable|numeric|required_without:other_incidental_fee_percent',

            // ===== Other required info =====
            'invoice_terms'          => 'required|string',
            'contract_tenure'        => 'nullable|numeric',
            'renewel_terms'          => 'required|string',
            'focal_person'           => 'required|string',
            'payment_release_tenure' => 'required|string',
            'remarks'                => 'required|string',

            // ===== Optional but typed fields =====
            'cgpa_min_limit'         => 'nullable|numeric',
            'cgpa_alert_limit'       => 'nullable|numeric',
            'age_restriction'        => 'nullable|numeric',
            'attendance_requirement' => 'nullable|numeric',
            'domicile'               => 'nullable|string',
            'roadmap'                => 'nullable|string',
            'roadmap_session_sel'    => 'nullable|string',
            'effective_from'         => 'nullable|date',
            'effective_to'           => 'nullable|date',

        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        if(!empty($request->get('cgpa_min_limit'))){
            if($request->get('cgpa_min_limit') > 4 || $request->get('cgpa_alert_limit') > 4 || $request->get('cgpa_min_limit') < 1 || $request->get('cgpa_alert_limit') < 1){
                return Response::json(array(
                    'success' => false,
                    'msg' => 'The CGPA values must be between 1 and 4.',
                ), 400);
            }
            if($request->get('cgpa_min_limit') > $request->get('cgpa_alert_limit')){
                return Response::json(array(
                    'success' => false,
                    'msg' => 'CGPA Threhold limit can not be greater than CGPA Alert limit',
                ), 400);
            }
        }
            $programs_id = $request->program;
            // dd($programs_id);
            if(is_array(json_decode($programs_id))){
                 $progId = null;
            }else{
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
                $progId = $school_depart_prog[0][2];
            }
            // dd($progId);
        // dd($request->all());
        $data = [
            // === BASIC INFORMATION ===
            'FSP_SCHOLARSHIP_ID'     => $request->input('scholarship'),
            'FSP_SPONSOR_BODY'       => $request->input('sponsor_body'),
            'FSP_SCHOOL_ID'          => is_array(json_decode($request->school)) ? null : $request->input('school'),//IF ALL SCHOOLS SELECT THEN CONSIDER NO RESTRICTION ON SCHOOL ID SO WE DONT INSERT ANY SCHOOL ID IN THAT CASE
            'FSP_DEPARTMENT_ID'      => is_array(json_decode($request->department)) ? null : $request->input('department'),//IF ALL departmentS SELECT THEN CONSIDER NO RESTRICTION ON department ID SO WE DONT INSERT ANY department ID IN THAT CASE
            'FSP_DEGREE_LEVEL'       => $request->input('degree_level'),
            'FSP_PROGRAM_ID'         => $progId ,
            'FSP_ROADMAP'            => $request->input('roadmap'),
            'FSP_ROADMAP_SESSION_SEL'=> $request->input('roadmap_session_sel'),

            // === ELIGIBILITY CRITERIA ===
            'FSP_CGPA_MIN_LIMIT'          => $request->input('cgpa_min_limit'),
            'FSP_CGPA_ALERT_LIMIT'        => $request->input('cgpa_alert_limit'),
            'FSP_GENDER'                  => $request->input('gender'),
            'FSP_NO_OF_SCHOLARSHIPS'      => $request->input('no_of_scholarships'),
            'FSP_AGE_RESTRICTION'         => $request->input('age_restriction'),
            'FSP_ATTENDANCE_REQUIREMENT'  => $request->input('attendance_requirement'),
            'FSP_DOMICILE'                => $request->input('domicile'),

            // === FINANCIAL COVERAGE ===
            'FSP_SECURITY_FEE_AMOUNT'           => $request->input('security_fee_amount'),
            'FSP_SECURITY_FEE_PERCENT'          => $request->input('security_fee_percent'),
            'FSP_ADMISSION_FEE_AMOUNT'          => $request->input('admission_fee_amount'),
            'FSP_ADMISSION_FEE_PERCENT'         => $request->input('admission_fee_percent'),
            'FSP_TUITION_FEE_AMOUNT'            => $request->input('tuition_fee_amount'),
            'FSP_TUITION_FEE_PERCENT'           => $request->input('tuition_fee_percent'),
            'FSP_ACTIVITY_FEE_AMOUNT'           => $request->input('activity_fee_amount'),
            'FSP_ACTIVITY_FEE_PERCENT'          => $request->input('activity_fee_percent'),
            'FSP_HOSTEL_COVERAGE_AMOUNT'        => $request->input('hostel_coverage_amount'),
            'FSP_HOSTEL_COVERAGE_PERCENT'       => $request->input('hostel_coverage_percent'),
            'FSP_STIPEND_AMOUNT'                => $request->input('stipend_amount'),
            'FSP_STIPEND_PERCENT'               => $request->input('stipend_percent'),
            'FSP_ADDITIONAL_CREDIT_HOUR_AMOUNT' => $request->input('additional_credit_hour_fee_amount'),
            'FSP_ADDITIONAL_CREDIT_HOUR_PERCENT'=> $request->input('additional_credit_hour_fee_percent'),
            'FSP_OTHER_INCIDENTAL_FEE_AMOUNT'   => $request->input('other_incidental_fee_amount'),
            'FSP_OTHER_INCIDENTAL_FEE_PERCENT'  => $request->input('other_incidental_fee_percent'),

            // === RENEWAL & INVOICE DETAILS ===
            'FSP_INVOICE_TERMS'           => $request->input('invoice_terms'),
            'FSP_CONTRACT_TENURE'         => $request->input('contract_tenure'),
            'FSP_RENEWAL_TERMS'           => $request->input('renewel_terms'),

            // === ADMINISTRATIVE INFORMATION ===
            'FSP_FOCAL_PERSON'            => $request->input('focal_person'),
            'FSP_PAYMENT_RELEASE_TENURE'  => $request->input('payment_release_tenure'),
            'FSP_REMARKS'                 => $request->input('remarks'),

            // === META INFORMATION ===
            // 'FSP_EFFECTIVE_FROM'          => $request->input('effective_from'),
            // 'FSP_EFFECTIVE_TO'            => $request->input('effective_to'),
            'FSP_CREATED_AT'              => now(),
            'FSP_ENTRY_USER'              => auth()->user()->username,
        ];
        // dd($data);
        if(empty($request->mainId)){//insertion case
            DB::table('FIN_SCHOLARSHIP_POLICY')->insert($data);
        }else{
            DB::table('FIN_SCHOLARSHIP_POLICY')
                ->where('FSP_ID', $request->mainId)
                ->update($data);
        }


        return response()->json([
            'success' => true,
            'msg' => 'Scholarship policy added successfully.'
        ]);
    }
    protected function getScholarshipPolicyList(Request $request){
        // dd($request->all());
        //////////////TO get the sessions/////////////////
        // $validityType = $request->get('validity_type');
        // // dd($validityType);
        // $session = [];
        // if ($validityType == 'session'  && !empty($request->get('session_sel'))) {//validity type is session and when session is selected
        //     $session[] =  $request->get('session_sel');
        // }else if($validityType == 'annual' && !empty($request->get('year_sel'))) {//validity type is yearly and when yearly is selected get the sessions under that year
        //     $acdmic_year_sessions = DB::table('ACADEMIC_SESSIONS')
        //                             ->select('SESS_ID', 'SESS_CODE')
        //                             ->where('SESS_AY_ID', $request->get('year_sel'))
        //                             ->where('SESS_STATUS', 'E')
        //                             ->get();
        //             //   dd($acdmic_year_sessions);
        //     if(!empty($acdmic_year_sessions)){
        //         foreach($acdmic_year_sessions as $val){
        //             $session[]= $val->sess_id;
        //         }

        //     }
        // }
        /////////////END of to get the sessions//////////////////////
        $data = DB::table('FIN_SCHOLARSHIP_POLICY')
                ->select(
                    'FSP_ID',
                    'FSP_SCHOLARSHIP_ID',
                    'FSP_SPONSOR_BODY',
                    'FSP_SCHOOL_ID',
                    'FSP_DEPARTMENT_ID',
                    'FSP_DEGREE_LEVEL',
                    'FSP_PROGRAM_ID',
                    'FSP_ROADMAP',
                    'FSP_ROADMAP_SESSION_SEL',
                    'FSP_CGPA_MIN_LIMIT',
                    'FSP_CGPA_ALERT_LIMIT',
                    'FSP_GENDER',
                    'FSP_NO_OF_SCHOLARSHIPS',
                    'FSP_AGE_RESTRICTION',
                    'FSP_ATTENDANCE_REQUIREMENT',
                    'FSP_DOMICILE',
                    'FSP_SECURITY_FEE_AMOUNT',
                    'FSP_SECURITY_FEE_PERCENT',
                    'FSP_ADMISSION_FEE_AMOUNT',
                    'FSP_ADMISSION_FEE_PERCENT',
                    'FSP_TUITION_FEE_AMOUNT',
                    'FSP_TUITION_FEE_PERCENT',
                    'FSP_ACTIVITY_FEE_AMOUNT',
                    'FSP_ACTIVITY_FEE_PERCENT',
                    'FSP_HOSTEL_COVERAGE_AMOUNT',
                    'FSP_HOSTEL_COVERAGE_PERCENT',
                    'FSP_STIPEND_AMOUNT',
                    'FSP_STIPEND_PERCENT',
                    'FSP_ADDITIONAL_CREDIT_HOUR_AMOUNT',
                    'FSP_ADDITIONAL_CREDIT_HOUR_PERCENT',
                    'FSP_OTHER_INCIDENTAL_FEE_AMOUNT',
                    'FSP_OTHER_INCIDENTAL_FEE_PERCENT',
                    'FSP_INVOICE_TERMS',
                    'FSP_CONTRACT_TENURE',
                    'FSP_RENEWAL_TERMS',
                    'FSP_FOCAL_PERSON',
                    'FSP_PAYMENT_RELEASE_TENURE',
                    'FSP_REMARKS',
                    'FSP_CREATED_AT',
                    'FSP_UPDATED_AT',
                    'FSP_ENTRY_USER',
                    'SCHOLARSHIP_TYPE.SCHOLARSHIP_NAME',
                    'SCHOLARSHIP_TYPE.SCHOLARSHIP_DESC',
                    'SCHOLARSHIP_TYPE.SCHOLARSHIP_CATEGORY',
                )
                ->join('SCHOLARSHIP_TYPE', 'SCHOLARSHIP_TYPE.SCHOLARSHIP_ID', '=', 'FIN_SCHOLARSHIP_POLICY.FSP_SCHOLARSHIP_ID')
                ->get();
                    // dd($data);
        return DataTables::of($data)->toJson();
	}
    public function delete_scholarshipPolicy(Request $request){
        $userId = auth()->user()->id;
      $affectedRows = DB::table('FIN_SCHOLARSHIP_POLICY')
                    ->where('FSP_ID', $request->mainId)
                    ->delete();

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
    // function masterSclrshpCriteriaForm(){
    //     $sessions = \App::call('App\Http\Controllers\Grader\AcademicSessionController@schlrshipSessionRetrieve');
    //     $schools = Schools::where('school_status', 'E')->get();
    //     $scholarships = DB::select("select * from SCHOLARSHIP_TYPE where SCHOLARSHIP_STATUS = 'E'");
    //     $departments = array(); // $this->school->fetch();
    //     $programs =  DB::select("select * from Programs where PROG_STATUS = 'E'");
    //     $academicYears =  DB::select("select * from ACADEMIC_YEARS where AY_STATUS = 'E' ORDER BY AY_ORDER desc");
    //     $query = 'select * from fin_fee_category';
    //     $feeCategories = DB::select($query);
    //     return view('StudentFinancial.master_schlrshp_criteria',compact('sessions','schools','departments','scholarships','feeCategories','programs', 'academicYears'));

    // }

    // private static function allProgramsInsertion($school_depart_prog, $request, $session,$roadmap_session){
    //     $userId = auth()->user()->id;
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
    //                                     'FSC_ROADMAP_SESSION_ID',
    //                                     'FSC_DEPT_ID',
    //                                     'FSC_PROG_ID',
    //                                     'FSC_DEGREE_LEVEL',
    //                                     'FSC_SCHOLARSHIP_ID',
    //                                     'FSC_CGPA_FROM',
    //                                     'FSC_CGPA_TO'
    //                                     )
    //                                     ->where('FSC_SESSION_ID', $session)
    //                                     ->where('FSC_SCHOOL_ID', $schoolId)
    //                                     ->where('FSC_DEPT_ID', $deptId)
    //                                     ->where('FSC_PROG_ID', $progId)
    //                                     ->where('FSC_ROADMAP_SESSION_ID', $roadmap_session)
    //                                     ->where('FSC_DEGREE_LEVEL', $request->get('degree_level'))
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
    //                     'FSC_DEGREE_LEVEL' => $request->get('degree_level'),
    //                     'FSC_ENTRY_USER_ID' => $userId,
    //                     'FSC_ROADMAP_SESSION_ID' => $roadmap_session,
    //                     'FSC_ROADMAP' => $request->get('roadmap'),

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
    //             $duplicateCheck = DB::table('FIN_SCHOLARSHIP_CRITERIA')
    //                                 ->select(
    //                                         'FSC_ID',
    //                                         'FSC_SESSION_ID',
    //                                         'FSC_SCHOOL_ID',
    //                                         'FSC_DEPT_ID',
    //                                         'FSC_PROG_ID',
    //                                         'FSC_ROADMAP_SESSION_ID',
    //                                         'FSC_DEGREE_LEVEL',
    //                                         'FSC_SCHOLARSHIP_ID',
    //                                         'FSC_CGPA_FROM',
    //                                         'FSC_CGPA_TO'
    //                                         )
    //                                         ->where('FSC_SESSION_ID', $session)
    //                                         ->where('FSC_SCHOOL_ID', $schoolId)
    //                                         ->where('FSC_ROADMAP_SESSION_ID', $roadmap_session)
    //                                         ->where('FSC_DEPT_ID', $deptId)
    //                                         ->where('FSC_PROG_ID', $progId)
    //                                         ->where('FSC_DEGREE_LEVEL', $request->get('degree_level'))
    //                                         ->where('FSC_SCHOLARSHIP_ID', $request->get('scholarship'))
    //                                         // ->where('FSC_CGPA_FROM', $request->get('cgpa_min'))
    //                                         // ->where('FSC_CGPA_TO', $request->get('cgpa_max'))
    //                                         ->where('FSC_STATUS', 'E')
    //                                         ->where('FSC_ID', '!=', $request->mainId)
    //                                         ->where(function($query) use ($request) {
    //                                             $cgpaMin = $request->get('cgpa_min');
    //                                             $cgpaMax = $request->get('cgpa_max');

    //                                             $query->where(function($query) use ($cgpaMin, $cgpaMax) {
    //                                                 $query->where('FSC_CGPA_FROM', $cgpaMin)
    //                                                     ->orWhere('FSC_CGPA_FROM', $cgpaMax)
    //                                                     ->orWhere('FSC_CGPA_TO', $cgpaMin)
    //                                                     ->orWhere('FSC_CGPA_TO', $cgpaMax)
    //                                                     ->orWhereBetween('FSC_CGPA_FROM', [$cgpaMin, $cgpaMax])
    //                                                     ->orWhereBetween('FSC_CGPA_TO', [$cgpaMin, $cgpaMax])
    //                                                     ->orWhere(function($query) use ($cgpaMin, $cgpaMax) {
    //                                                         $query->where('FSC_CGPA_FROM', '>', $cgpaMin)
    //                                                               ->where('FSC_CGPA_TO', '<', $cgpaMax);
    //                                                     });
    //                                             });
    //                                         })
    //                                         ->get();
    //             if(sizeof($duplicateCheck) == 0 ){
    //                 try {
    //                     DB::table('FIN_SCHOLARSHIP_CRITERIA')
    //                             ->where('FSC_ID', $request->mainId)
    //                             ->update([
    //                                 'FSC_SCHOOL_ID' => $schoolId,
    //                                 'FSC_DEPT_ID' => $deptId,
    //                                 'FSC_PROG_ID' => $progId,
    //                                 'FSC_SESSION_ID' => $session,
    //                                 'FSC_DEGREE_LEVEL' => $request->get('degree_level'),
    //                                 'FSC_ENTRY_USER_ID' => $userId,
    //                                 'FSC_ROADMAP_SESSION_ID' => $roadmap_session,
    //                                 'FSC_ROADMAP' => $request->get('roadmap'),

    //                                 // 'FSC_ACADEMIC_YEAR_ID' => $request->get('academic_year'),
    //                                 'FSC_SCHOLARSHIP_ID' => $request->get('scholarship'),
    //                                 'FSC_CGPA_FROM' => $request->get('cgpa_min'),
    //                                 'FSC_CGPA_TO' => $request->get('cgpa_max'),
    //                                 'FSC_PERCENTAGE' => $request->get('percentage'),
    //                                 'FSC_EFF_FROM' => $request->get('effective_from'),
    //                                 'FSC_EFF_TO' => $request->get('effective_to'),
    //                                 'FSC_STATUS' => 'E',
    //                                 'FSC_CREATED_AT' => now(),
    //                             ]);
    //                     $resp[0] = true;
    //                     $resp[1] = 'Updated Successfully';
    //                     } catch (\Exception $e) {
    //                         $resp[0] = false;
    //                         $resp[1] = 'Uniqueness conflict occurred';
    //                         // Optionally log the exception for debugging
    //                         // Log::error('Error updating FIN_FEE_POLICY: ' . $e->getMessage());
    //                     }
    //                     return $resp;
    //             }else{
    //                 $resp[0]=false;
    //                 $resp[1]='Criteria already exist or CGPA conflic occured';
    //                 return $resp;
    //             }
    //         }
    //     }else{
    //         $resp[0]=false;
    //         $resp[1]='You can edit one program at a time';
    //         return $resp;
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

