<?php

namespace App\Http\Controllers\StudentFinancial;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Grader\Schools;
use App\Models\Grader\Departments;
use DB;
use DataTables;

class AdmissionScholarshipApprovalController extends Controller
{
    //
    function index(Request $request){

        $sessions = \App::call('App\Http\Controllers\Grader\AcademicSessionController@schlrshipSessionRetrieve');
        // dd($sessions);
        $scholarships = DB::table('scholarship_type')
        ->select('scholarship_id', 'scholarship_name')
        ->get();
        $schools =  Schools::all();
        $departments = array();
        // dd($scholarships);
        return view('StudentFinancial.admissionScholarshipApproval',compact('scholarships','sessions','schools','departments'));
    }
    public function filter_admission_schlrshp_applicants(Request $request){
        $sessionId = $request->session_id;
        $schoolId = $request->school_id;
        $deptId = $request->department_id;
        $programId = $request->program_id;
        $scholarship_id = $request->scholarship_id;
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
                        aai_status,
                        FASC_ID,
                        FASC_SCHLRSHP_PERCENTAGE,
                        FASC_SCHOLARSHIP_STATUS,
                        SCHOLARSHIP_NAME,
                        FASC_SCHOLARSHIP_ID


                        FROM FIN_ADMISSION_SCHOLARSHIP_CRITERIA
                        LEFT JOIN admission_apply_info ON FASC_AAI_ID = aai_id
                        LEFT JOIN admission_personal_info ON api_id = aai_api_id
                        LEFT JOIN SCHOLARSHIP_TYPE ON SCHOLARSHIP_ID = FASC_SCHOLARSHIP_ID
                        LEFT JOIN schools ON school_id = aai_sdpo_school_id
                        WHERE aai_api_id=api_id
                        and FASC_STATUS='E'"
                        ;

        if(!empty($sessionId)){
        $sqlMeritList .= " and FASC_SESSION_ID = '$sessionId'";
        }
        if(!empty($schoolId)){
        $sqlMeritList .=" and FASC_SCHOOL_ID = '$schoolId'";
        }

        if(!empty($deptId)){
        $sqlMeritList .=" and FASC_DEPT_ID = '$deptId'";
        }
        if(!empty($programId)){
        $sqlMeritList .=" and FASC_PROG_ID = '$programId'";
        }
        if(!empty($scholarship_id)){
        $sqlMeritList .=" and FASC_SCHOLARSHIP_ID = '$scholarship_id'";
        }
        // dd($sqlMeritList);
        $resSelectMeritList = DB::select($sqlMeritList);
        // dd($resSelectMeritList);
        return DataTables::of($resSelectMeritList)->toJson();
    }

    public function updateApprovalStatus(Request $request)
    {
        $aai_id = $request->aai_id;
        $session = $request->sess_id;
        $schlrship_id = $request->schlrship_id;
        /////
        $procedurenotification = 'FIN_NEW_ADM_UPDATE_CHALLAN';
        $bindings_notification = [
            'p_aai_id' => $aai_id,
            'p_session' => $session,
            'p_sch_type' => $schlrship_id,
        ];
        /////
        $userId = auth()->user()->id;
        $affectedRows = DB::table('FIN_ADMISSION_SCHOLARSHIP_CRITERIA')
                        ->where('FASC_ID', $request->fasc_id)
                        ->update([
                            'FASC_SCHOLARSHIP_STATUS' => 'Approved',
                            'FASC_APPROVEDBY_USER_ID' => $userId
                        ]);

        if ($affectedRows > 0) {
            $result_notification = DB::executeProcedure($procedurenotification, $bindings_notification);
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
