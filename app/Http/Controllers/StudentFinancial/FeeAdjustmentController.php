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

class FeeAdjustmentController extends Controller{

    function masterFeeAdjustmentForm(){
        // $sessions = \App::call('App\Http\Controllers\Grader\AcademicSessionController@retrieve');
        // $schools =  Schools::all();
        // $scholarships = DB::select('select * from SCHOLARSHIP_TYPE');
        $feeFineCategory = DB::select("select * from FIN_FEE_CATEGORY WHERE STATUS = 'E'");
        // $employees = DB::select('select * from EMP_EMPLOYEES');
        // dd($sessions, $scholarships, $employees);
        // $departments = array(); // $this->school->fetch();
        // $facultyCourses = \App::call('App\Http\Controllers\Grader\FacultyCoursesController@retrieve');
        // $offeredFaculty = \App::call('App\Http\Controllers\Grader\CoursesOfferedController@retrieveFaculty');
        // $programs =  DB::select('select * from Programs');;
        // $query = 'select * from fin_fee_category';
        // $feeCategories = DB::select($query);
        return view('StudentFinancial.masterFeeAdjustmentForm',compact('feeFineCategory'));

    }
    public function addMasterFeeAdjustment(Request $request){
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'fee_category' => 'required|numeric'
        ], [
            'title.required' => 'The title field is required.',
            'fee_category.required' => 'The fee category field is required.',
            'fee_category.numeric' => 'The fee category must be a number.',
        ]);
        if($validator->fails()) {
            return Response::json(array(
                'success' => false,
                'errors' => $validator->getMessageBag()->toArray()
            ), 400);
        }
        $userId = auth()->user()->id;
        $data = [
			'FFAC_TITLE' => $request->title,
			'FFAC_DESCRIPTION' =>$request->description,
			'FFAC_FEE_CTGRY' =>$request->fee_category,
			'FFAC_ENTRY_USER_ID' =>$userId,
			'FFAC_CREATED_AT' => now(),
			'FFAC_STATUS' => 'E',


        ];
        DB::beginTransaction();

        try {
            if (empty($request->mainId)) {
                $duplicateCheck = DB::table('FIN_FEE_ADJUSTMENT_CATEGORY')
                                ->select(
                                        'FFAC_ID',
                                        'FFAC_TITLE',
                                        'FFAC_FEE_CTGRY'
                                        )
                                        ->where('FFAC_TITLE', $request->title)
                                        ->where('FFAC_FEE_CTGRY', $request->fee_category)
                                        ->where('FFAC_STATUS', 'E')
                                        ->get();
                if(sizeof($duplicateCheck) == 0 ){
                    // INSERTION CASE
                    $insert = DB::table('FIN_FEE_ADJUSTMENT_CATEGORY')->insert($data);
                    $mesg = 'Saved Successfully';
                }else{
                    //Update Case
                    $affectedRows = DB::table('FIN_FEE_ADJUSTMENT_CATEGORY')
                                ->where('FFAC_ID', $duplicateCheck[0]->ffac_id)
                                ->update($data);
                    if ($affectedRows > 0) {
                        $mesg = 'Updated Successfully';
                    }
                }
            }else{
                // Edit CASE
                $affectedRows = DB::table('FIN_FEE_ADJUSTMENT_CATEGORY')
                                ->where('FFAC_ID', $request->mainId)
                                ->update($data);
                if ($affectedRows > 0) {
                    $mesg = 'Updated Successfully';
                }
            }
            // Commit the transaction
            DB::commit();

            // If no errors occurred, return a success response
            return response()->json([
                'success' => true,
                'msg' => $mesg,
            ]);
        } catch (\Exception $e) {
            // Something went wrong, rollback the transaction
            DB::rollback();

            // Log the error
            // \Log::error('Error: ' . $e->getMessage());

            // Return a failure response
            return response()->json([
                'success' => false,
                'msg' => 'Something went wrong',
            ]);
        }
    }
    public function getMasterFeeAdjustmentList(Request $request){
        $data = DB::table('FIN_FEE_ADJUSTMENT_CATEGORY as ffac')
            ->select(
                    'ffac.FFAC_ID',
                    'ffac.FFAC_TITLE',
                    'ffac.FFAC_DESCRIPTION',
                    'ffc.NAME',
                    'ffc.ID as feecategoryid',
                    )
                    ->join('FIN_FEE_CATEGORY as ffc', 'ffc.ID', 'ffac.FFAC_FEE_CTGRY')
                    ->where('ffac.FFAC_STATUS', 'E')
                    ->orderBy('ffac.FFAC_CREATED_AT', 'desc')
                    ->get();
                // dd($data);
            return DataTables::of($data)->toJson();

    }
    public function delete_masterFeeAdjustment(Request $request){
        $userId = auth()->user()->id;
        $affectedRows =  DB::table('FIN_FEE_ADJUSTMENT_CATEGORY')
                            ->whereIn('FFAC_ID', $request->mainId)
                            ->update([
                                'FFAC_STATUS' => 'D',
                                'FFAC_ENTRY_USER_ID' =>$userId,
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
    ////////////////////////////////////////////////
    function feeAdjustment(){
        $sessions = \App::call('App\Http\Controllers\Grader\AcademicSessionController@retrieve');
        $scholarships = DB::table('scholarship_type')
        ->select('scholarship_id', 'scholarship_name')
        ->get();
        $schools =  Schools::all();
        $departments = array();

        return view('StudentFinancial.feeAdjustment',compact('scholarships','sessions','schools','departments'));

    }
    public function getEnrollmentRec(Request $request){
        // dd($request->all());
        $searchBy = $this->checkEnrollmentORaai_id($request->studentEnrollmentNo);
        // dd($searchBy);
        if($searchBy == 'enrollment'){
            $data = DB::table('STUDENTS_APPLY_INFO as sai')
                    ->select(
                            'sai.SAI_ID',
                            'sai.SAI_STUDENT_STATUS',
                            'sai.SAI_ENROLLMENT_NO',
                            'sai.SAI_APPLY_DATE',

                            'spi.SPI_NAME',
                            'aai.aai_id',
                            'acd.SESS_NAME',
                            'school.SCHOOL_NAME',
                            'dept.DEPT_NAME',
                            'prog.PROG_NAME',
                            'api.API_IMAGENAME',
                            DB::raw("'true' as with_aai_id"),
                            'sc.*'
                            )
                            ->leftjoin('STUDENTS_PERSONAL_INFO as spi', 'spi.SPI_ID', 'sai.SAI_SPI_ID')
                            ->leftJoin('ADMISSION_APPLY_INFO as aai', function($join) {
                                $join->on('aai.aai_api_id', '=', 'sai.SAI_API_ID')
                                    ->whereRaw('LOWER(aai.CMS_SAI_ENROLLMENT_NO) = LOWER(sai.SAI_ENROLLMENT_NO)');
                            })
                            ->leftjoin('ADMISSION_PERSONAL_INFO as api', 'api.API_ID', 'aai.AAI_API_ID')
                            ->leftjoin('ACADEMIC_SESSIONS as acd', 'acd.SESS_ID', 'sai.SAI_SDPO_SESS_ID')
                            ->leftjoin('SCHOOLS as school', 'school.SCHOOL_ID', 'sai.SAI_SDPO_SCHOOL_ID')
                            ->leftjoin('DEPARTMENTS as dept', 'dept.DEPT_ID', 'sai.SAI_SDPO_DEPT_ID')
                            ->leftjoin('PROGRAMS as prog', 'prog.PROG_ID', 'sai.SAI_SDPO_PROG_ID')
                            ->leftjoin('STUDENT_CHALLAN as sc', 'sc.SC_APP_ID', 'aai.aai_id')
                            ->whereRaw('LOWER(SAI_ENROLLMENT_NO) = ?', [strtolower($request->studentEnrollmentNo)])
                            // ->whereIn('sai.SAI_STUDENT_STATUS', ['Expected-Graduate', 'Active', 'Left','Struck off','Stuck-Off'])
                            ->whereRaw("TRIM(sc.sc_challan_type) IN ('Full_semester','Hostel_Fee', 'Credit_hours','Fee_Refund','Short/Summer_Course')")
                            ->where('sc.sc_challan_status', 'UNPAID')
                            ->where('sc.INVOICEAMOUNT', '>', 0)
                            ->get()->toArray();
                            // dd($data);
            if(empty($data)){
                $data="SELECT
                            SAI.SAI_ID,
                            SAI.SAI_STUDENT_STATUS,
                            SAI.SAI_ENROLLMENT_NO,
                            SAI.SAI_APPLY_DATE,
                            SPI.SPI_NAME,
                            SAI.SAI_ID as aai_id,
                            ACD.SESS_NAME,
                            SCHOOL.SCHOOL_NAME,
                            DEPT.DEPT_NAME,
                            PROG.PROG_NAME,
                            SPI.SPI_IMAGENAME,
                            'false' as with_aai_id,
                            SC.*
                        FROM STUDENTS_APPLY_INFO SAI

                        LEFT JOIN STUDENTS_PERSONAL_INFO SPI
                            ON SPI.SPI_ID = SAI.SAI_SPI_ID

                        LEFT JOIN ACADEMIC_SESSIONS ACD
                            ON ACD.SESS_ID = SAI.SAI_SDPO_SESS_ID

                        LEFT JOIN SCHOOLS SCHOOL
                            ON SCHOOL.SCHOOL_ID = SAI.SAI_SDPO_SCHOOL_ID

                        LEFT JOIN DEPARTMENTS DEPT
                            ON DEPT.DEPT_ID = SAI.SAI_SDPO_DEPT_ID

                        LEFT JOIN PROGRAMS PROG
                            ON PROG.PROG_ID = SAI.SAI_SDPO_PROG_ID

                        LEFT JOIN STUDENT_CHALLAN SC
                            ON SC.SC_APP_ID = SAI.SAI_ID

                        WHERE LOWER(SAI.SAI_ENROLLMENT_NO) = LOWER('$request->studentEnrollmentNo')
                        AND TRIM(SC.SC_CHALLAN_TYPE) IN (
                                'Full_semester',
                                'Hostel_Fee',
                                'Credit_hours',
                                'Fee_Refund',
                                'Short/Summer_Course'
                            )

                        AND SC.SC_CHALLAN_STATUS = 'UNPAID'
                        AND SC.INVOICEAMOUNT > '0'
                        ";
                $data= DB::select($data);
                // dd($data);
            }
                ////////////////////////////////
                $newAdjustmentData = DB::table('STUDENTS_APPLY_INFO as sai')
                    ->select(
                            'sai.SAI_ID',
                            'sai.SAI_STUDENT_STATUS',
                            'sai.SAI_ENROLLMENT_NO',
                            'sai.SAI_APPLY_DATE',

                            'spi.SPI_NAME',
                            'aai.aai_id',
                            'acd.SESS_NAME',
                            'school.SCHOOL_NAME',
                            'dept.DEPT_NAME',
                            'prog.PROG_NAME',
                            DB::raw("'true' as with_aai_id"),
                            'api.API_IMAGENAME',
                            )
                            ->leftjoin('STUDENTS_PERSONAL_INFO as spi', 'spi.SPI_ID', 'sai.SAI_SPI_ID')
                            ->leftJoin('ADMISSION_APPLY_INFO as aai', function($join) {
                                $join->on('aai.aai_api_id', '=', 'sai.SAI_API_ID')
                                    ->whereRaw('LOWER(aai.CMS_SAI_ENROLLMENT_NO) = LOWER(sai.SAI_ENROLLMENT_NO)');
                            })
                            ->leftjoin('ADMISSION_PERSONAL_INFO as api', 'api.API_ID', 'aai.AAI_API_ID')
                            ->leftjoin('ACADEMIC_SESSIONS as acd', 'acd.SESS_ID', 'sai.SAI_SDPO_SESS_ID')
                            ->leftjoin('SCHOOLS as school', 'school.SCHOOL_ID', 'sai.SAI_SDPO_SCHOOL_ID')
                            ->leftjoin('DEPARTMENTS as dept', 'dept.DEPT_ID', 'sai.SAI_SDPO_DEPT_ID')
                            ->leftjoin('PROGRAMS as prog', 'prog.PROG_ID', 'sai.SAI_SDPO_PROG_ID')
                            ->whereRaw('LOWER(SAI_ENROLLMENT_NO) = ?', [strtolower($request->studentEnrollmentNo)])
                            // ->whereIn('sai.SAI_STUDENT_STATUS', ['Expected-Graduate', 'Active', 'Left','Struck off','Stuck-Off'])
                            ->get();
                if(empty($newAdjustmentData[0]->aai_id)){
                    $newAdjustmentData="SELECT
                                            SAI.SAI_ID,
                                            SAI.SAI_STUDENT_STATUS,
                                            SAI.SAI_ENROLLMENT_NO,
                                            SAI.SAI_APPLY_DATE,

                                            SPI.SPI_NAME,
                                            SAI.SAI_ID as aai_id,

                                            ACD.SESS_NAME,
                                            SCHOOL.SCHOOL_NAME,
                                            DEPT.DEPT_NAME,
                                            PROG.PROG_NAME,
                                            'false' as with_aai_id,
                                            SPI.SPI_IMAGENAME

                                        FROM STUDENTS_APPLY_INFO SAI

                                        LEFT JOIN STUDENTS_PERSONAL_INFO SPI
                                            ON SPI.SPI_ID = SAI.SAI_SPI_ID


                                        LEFT JOIN ACADEMIC_SESSIONS ACD
                                            ON ACD.SESS_ID = SAI.SAI_SDPO_SESS_ID

                                        LEFT JOIN SCHOOLS SCHOOL
                                            ON SCHOOL.SCHOOL_ID = SAI.SAI_SDPO_SCHOOL_ID

                                        LEFT JOIN DEPARTMENTS DEPT
                                            ON DEPT.DEPT_ID = SAI.SAI_SDPO_DEPT_ID

                                        LEFT JOIN PROGRAMS PROG
                                            ON PROG.PROG_ID = SAI.SAI_SDPO_PROG_ID

                                        WHERE LOWER(SAI.SAI_ENROLLMENT_NO) = LOWER('$request->studentEnrollmentNo')";
                        $newAdjustmentData= DB::select($newAdjustmentData);
                    }

        }else{
            $data="SELECT
                    SAI.SAI_ID,
                    SAI.SAI_STUDENT_STATUS,
                    SAI.SAI_ENROLLMENT_NO,
                    SAI.SAI_APPLY_DATE,
                    SPI.SPI_NAME,
                    AAI.AAI_ID,
                    ACD.SESS_NAME,
                    SCHOOL.SCHOOL_NAME,
                    DEPT.DEPT_NAME,
                    PROG.PROG_NAME,
                    API.API_IMAGENAME,
                    'true' as with_aai_id,
                    SC.*
                FROM
                    ADMISSION_APPLY_INFO AAI
                    LEFT JOIN ADMISSION_PERSONAL_INFO API
                        ON API.API_ID = AAI.AAI_API_ID
                    LEFT JOIN STUDENTS_APPLY_INFO SAI
                        ON SAI.SAI_API_ID = API.API_ID and lower(sai_enrollment_no)= lower(cms_sai_enrollment_no)
                    LEFT JOIN STUDENTS_PERSONAL_INFO SPI
                        ON SPI.API_ID = API.API_ID
                    LEFT JOIN ACADEMIC_SESSIONS ACD
                        ON ACD.SESS_ID = AAI.AAI_SDPO_SESS_ID
                    LEFT JOIN SCHOOLS SCHOOL
                        ON SCHOOL.SCHOOL_ID = AAI.AAI_SDPO_SCHOOL_ID
                    LEFT JOIN DEPARTMENTS DEPT
                        ON DEPT.DEPT_ID = AAI.AAI_SDPO_DEPT_ID
                    LEFT JOIN PROGRAMS PROG
                        ON PROG.PROG_ID = AAI.AAI_SDPO_PROG_ID
                    LEFT JOIN STUDENT_CHALLAN SC
                        ON SC.SC_APP_ID = AAI.AAI_ID
                WHERE
                    TRIM(SC.SC_CHALLAN_TYPE) IN ('Full_semester', 'Hostel_Fee', 'Credit_hours', 'Fee_Refund', 'Short/Summer_Course')
                    AND SC.SC_CHALLAN_STATUS = 'UNPAID'
                    AND SC.INVOICEAMOUNT > 0
                    AND AAI.AAI_ID = $request->studentEnrollmentNo
                    -- AND (
                    --     SAI.SAI_STUDENT_STATUS IN ('Expected-Graduate', 'Active','Left','Struck off','Stuck-Off')
                    --     OR SAI.SAI_STUDENT_STATUS IS NULL
                    -- )
                    ";
                $data= DB::select($data);
                ///////////////////////////////////////
                $newAdjustmentData="SELECT
                    SAI.SAI_ID,
                    SAI.SAI_STUDENT_STATUS,
                    SAI.SAI_ENROLLMENT_NO,
                    SAI.SAI_APPLY_DATE,
                    SPI.SPI_NAME,
                    AAI.AAI_ID,
                    ACD.SESS_NAME,
                    SCHOOL.SCHOOL_NAME,
                    DEPT.DEPT_NAME,
                    PROG.PROG_NAME,
                    API.API_IMAGENAME,
                    'true' as with_aai_id
                FROM
                    ADMISSION_APPLY_INFO AAI
                    LEFT JOIN ADMISSION_PERSONAL_INFO API
                        ON API.API_ID = AAI.AAI_API_ID
                    LEFT JOIN STUDENTS_APPLY_INFO SAI
                        ON SAI.SAI_API_ID = API.API_ID and lower(sai_enrollment_no)= lower(cms_sai_enrollment_no)
                    LEFT JOIN STUDENTS_PERSONAL_INFO SPI
                        ON SPI.API_ID = API.API_ID
                    LEFT JOIN ACADEMIC_SESSIONS ACD
                        ON ACD.SESS_ID = AAI.AAI_SDPO_SESS_ID
                    LEFT JOIN SCHOOLS SCHOOL
                        ON SCHOOL.SCHOOL_ID = AAI.AAI_SDPO_SCHOOL_ID
                    LEFT JOIN DEPARTMENTS DEPT
                        ON DEPT.DEPT_ID = AAI.AAI_SDPO_DEPT_ID
                    LEFT JOIN PROGRAMS PROG
                        ON PROG.PROG_ID = AAI.AAI_SDPO_PROG_ID
                WHERE
                    AAI.AAI_ID = $request->studentEnrollmentNo
                    -- AND (
                    --     SAI.SAI_STUDENT_STATUS IN ('Expected-Graduate', 'Active','Left','Struck off','Stuck-Off')
                    --     OR SAI.SAI_STUDENT_STATUS IS NULL
                    -- )
                    ";
                $newAdjustmentData= DB::select($newAdjustmentData);
        }
        // dd($newAdjustmentData, $data);
        $adjustmentData = DB::table('FIN_FEE_ADJUSTMENT_CATEGORY')
                        ->select(
                                'FFAC_ID',
                                'FFAC_TITLE',
                                'FFAC_DESCRIPTION',
                                )
                                ->where('FFAC_STATUS', 'E')
                                ->get();
                        // dd($data, $adjustmentData, $newAdjustmentData);
        // dd($data);
         if (!empty($data[0]) || !empty($newAdjustmentData[0])) {
            // dd(1);
            return response()->json(['success' => true, 'enrollmentData' => $data, 'adjustmentData' => $adjustmentData, 'newAdjustmentData' => $newAdjustmentData]);
        }else{
            // dd(2);
            return response()->json(['success' => false, 'enrollmentData' => $data, 'adjustmentData' => $adjustmentData, 'newAdjustmentData' => $newAdjustmentData]);
        }


    }
    public function getChallanRec(Request $request){
        // dd($request->all());
        $data = DB::table('student_challan')
                ->select(
                        'ID',
                        'SC_ENROLLMENT_NO',
                        'CHALLAN_NO',
                        'SC_CHALLAN_STATUS',
                        'SC_CHALLAN_TYPE',
                        'SC_APP_ID',
                        'INVOICEAMOUNT',
                        'INVOICE_STATUS',
                        'SC_SAI_STUDENT_STATUS',
                        'SC_STUDENT_STATUS',
                        'SC_SCHOLARSHIP_AMOUNT',
                        'SC_TUITION_FEE_AMNT as tuitionfee',
                        'SC_ISSU_DATE',
                        'sc_installment_tuition_fee_amnt as actual_amount',

                        'SC_SESSION_ID',
                        'SC_SCHOOL_ID',
                        'SC_DEPARTMENT_ID',
                        'SC_PROGRAM_ID',
                        )
                        ->where('ID', $request->studentChallanNo)
                        ->whereRaw("TRIM(sc_challan_type) IN ('Full_semester','Hostel_Fee', 'Credit_hours','Fee_Refund','Short/Summer_Course')")
                        ->where('SC_CHALLAN_STATUS', 'UNPAID')
                        ->where('INVOICEAMOUNT', '>', 0)
                        ->first();
        // dd($data);
        $adjustmentData = DB::table('FIN_FEE_ADJUSTMENT_CATEGORY')
                        ->select(
                                'FFAC_ID',
                                'FFAC_TITLE',
                                'FFAC_DESCRIPTION',
                                )
                                ->where('FFAC_STATUS', 'E')
                                ->get();
                        // dd($data, $adjustmentData);
         if (!empty($data)) {
            return response()->json(['success' => true, 'challanData' => $data, 'adjustmentData' => $adjustmentData]);
        }else{
            return response()->json(['success' => false, 'challanData' => $data, 'adjustmentData' => $adjustmentData]);
        }


    }
    public function addfeeAdjustment(Request $request){
        // dd($request->all());
        if($request->submitType == 'add_new'){
           return $this->addNewfeeAdjustment($request);
        }
        $validator = Validator::make($request->all(),[
            'surplus_amount' => 'required',
            'CategoryOption' => 'required'
        ]);
        if($validator->fails()) {
            return Response::json(array(
                'success' => false,
                'errors' => $validator->getMessageBag()->toArray()
            ), 400);
        }
        $userId = auth()->user()->id;

        DB::beginTransaction();
        $mesg='';

       try {
           $duplicateCheck = DB::table('FIN_FEE_ADJUSTMENT')
                                ->select('FFA_ID', 'FFA_SURPLUS_AMOUNT')
                                ->where(function ($query) use ($request) {
                                    $query->where('FFA_ENROLLMENT_NO', $request->enrollment_no)
                                        ->orWhere('FFA_APP_ID', $request->app_id);
                                })
                                ->get();
            // dd($request->surplus_amount + (isset($duplicateCheck[0]->ffa_surplus_amount) ? $duplicateCheck[0]->ffa_surplus_amount : 0));
            $existingSurplus = (isset($duplicateCheck[0]->ffa_surplus_amount) ? $duplicateCheck[0]->ffa_surplus_amount : 0);
            $newSurplus = (isset($request->surplus_amount) ? $request->surplus_amount : 0);
            // dd($existingSurplus, $newSurplus);
            if($newSurplus <= $existingSurplus){
                $finalSurplus = $existingSurplus - $newSurplus;
            }else{
                $finalSurplus=0;
                return Response::json(array(
                    'success' => false,
                    'errors' => array(
                        'adjustment' => array('Not enough Surplus amount to adjust the amount entered')
                    )
                ), 400);
            }
            // dd($existingSurplus, $newSurplus, $finalSurplus);
            $data = [
                // 'FFA_SC_CHALLAN_NO' => (!empty($request->challan_no)? $request->challan_no : NULL),//
                'FFA_ENROLLMENT_NO' =>(!empty($request->enrollment_no) && $request->enrollment_no !== 'null'? $request->enrollment_no : NULL),
                'FFA_APP_ID' =>(!empty($request->app_id) && $request->app_id !== 'null'? $request->app_id : NULL),
                'FFA_ADJUSTMENT_CATEGORY' =>$request->CategoryOption,
                'FFA_ENTRY_USERID' =>$userId,
                'FFA_ACTUAL_AMOUNT' =>(!empty($request->actual_amount)? ($request->actual_amount != 'null' ? $request->actual_amount : 0) : 0),//
                'FFA_INVOICE_AMOUNT' =>(!empty($request->invoice_amount)? ($request->invoice_amount != 'null' ? $request->invoice_amount : 0) : 0),//
                // 'FFA_SURPLUS_AMOUNT' => $finalSurplus,
                'FFA_SCHOLARSHIP_AMOUNT' =>(!empty($request->scholarship_amount) && $request->scholarship_amount !== 'null'? $request->scholarship_amount : NULL),
                'FFA_REMARKS' =>$request->remarks,
                'FFA_CHALLAN_STATUS' =>(!empty($request->challan_status)? $request->challan_status : NULL),//
                'FFA_CHALLAN_TYPE' =>(!empty($request->challan_type)? $request->challan_type : 'fee'),//
                'FFA_STUDENT_STATUS' =>((!empty($request->student_status) && $request->student_status !== 'null')? $request->student_status : NULL),
                'FFA_CREATED_AT' => now(),
                'FFA_STATUS'=>'E',
                'FF_AMNT_STATUS'=>'E',
            ];
            //    dd($data);

            if(sizeof($duplicateCheck) == 0){
                //INSERT CASE
                $check = DB::table('FIN_FEE_ADJUSTMENT')->insert($data);
                if($check){
                    $mesg = 'Saved Successfully';
                }

            }else{
                //UPDATE CASE

                $check = DB::table('FIN_FEE_ADJUSTMENT')
                        ->where('FFA_ID', $duplicateCheck[0]->ffa_id)
                        ->update($data);
                if($check){
                    $mesg = 'Updated Successfully';
                }
            }
            // dd($mesg);
            // Commit the transaction
            // DB::commit();
            // dd("SELECT SUM(ffa_surplus_amount) as v_surplus_amount,ffa_enrollment_no,ffa_app_id,ffa_id FROM fin_fee_adjustment
            // WHERE FF_AMNT_STATUS='E'   and  FFA_STATUS='E'  and (lower(ffa_enrollment_no)=lower('$request->enrollment_no') OR ffa_app_id=$request->app_id)
            // GROUP BY ffa_enrollment_no,ffa_app_id,ffa_id");

            $sql = DB::select("SELECT SUM(ffa_surplus_amount) as v_surplus_amount,ffa_enrollment_no,ffa_app_id,ffa_id FROM fin_fee_adjustment
            WHERE FF_AMNT_STATUS='E'   and  FFA_STATUS='E'  and (lower(ffa_enrollment_no)=lower('$request->enrollment_no') OR ffa_app_id=$request->app_id)
            GROUP BY ffa_enrollment_no,ffa_app_id,ffa_id");

           $existingChallan = DB::table('Student_challan')
                                ->whereRaw("TRIM(sc_challan_type) IN ('Full_semester', 'Hostel_Fee', 'Credit_hours','Fee_Refund','Short/Summer_Course')")
                                ->where('sc_challan_status', 'UNPAID')
                                ->where('id', $request->challan_no)
                                ->where(function ($query) use ($request) {
                                    $query->whereRaw('lower(sc_enrollment_no) = ?', [strtolower($request->enrollment_no)])
                                        ->orWhere('sc_app_id', $request->app_id);
                                })
                                ->first();
            $username = auth()->user()->username;
            $updatedTime = now();

            // dd($sql,$existingChallan);
            if($sql && $existingChallan){
                // echo $sql;exit;
                $row= $sql[0];
                $surplus_amount = $row->v_surplus_amount;//sum of all coming from DB
                $enrollment_no = $row->ffa_enrollment_no;
                $app_id = $row->ffa_app_id;
                $adjustedAmount= $existingChallan->sc_surplus_amount? $existingChallan->sc_surplus_amount : 0;
                // dd($adjustedAmount);
                $ffaStatus='';
                $ffAmntStatus='';

                $flag='pending';
                if($existingChallan && $request->surplus_amount <= $existingChallan->invoiceamount){
                    // dd('jo surplus add kr rhy hain wo challan ki invoice amount sy kam hai ya barabar hai');
                    if($request->surplus_amount == $surplus_amount){
                        $flag='Paid';
                        $ffaStatus='D';
                        $ffAmntStatus='D';
                        $sql="update fin_fee_adjustment set ffa_sc_challan_no=$existingChallan->id,ffa_surplus_amount=$finalSurplus,ffa_amount_status='$flag',ffa_status='$ffaStatus',ff_amnt_status='$ffAmntStatus'  where ffa_id = $row->ffa_id";
                    }else{
                        $sql="update fin_fee_adjustment set ffa_sc_challan_no=$existingChallan->id,ffa_surplus_amount=$finalSurplus  where ffa_id = $row->ffa_id";
                    }
                    $updatedAmount=$existingChallan->invoiceamount-$request->surplus_amount;//$request->surplus_amount;//$surplus_amount
                    $resSql=DB::insert($sql);
                    $sel="update student_challan set sc_surplus_amount=($request->surplus_amount+$adjustedAmount),invoiceamount=$updatedAmount, SC_UPDATED_BY_USERNAME='$username', UPDATED_AT='$updatedTime' where sc_app_id = '$app_id' and sc_session_id=$existingChallan->sc_session_id  and id=$existingChallan->id";
                    // dd($sel);
                    $resSel=DB::insert( $sel);

                }else if($existingChallan && $surplus_amount > $existingChallan->invoiceamount){
                    // dd('bachy k surplus me pari amount '.$surplus_amount.' challan ki invoice '.$existingChallan->invoiceamount.' sy zada hai');
                    $ffaStatus='E';
                    $ffAmntStatus='E';
                    $surplusAmount = $surplus_amount-$existingChallan->invoiceamount;
                    if($surplusAmount>0){
                        $surplusAmount=$surplus_amount-$surplusAmount;

                    }
                    $newAmount=0;

                    $sel="update fin_fee_adjustment set ffa_sc_challan_no=$existingChallan->id,ffa_surplus_amount= $surplus_amount-$existingChallan->invoiceamount,ffa_amount_status='$flag',ffa_status='$ffaStatus',ff_amnt_status='$ffAmntStatus'  where ffa_id = $row->ffa_id";
                $resSel=DB::insert( $sel);
                $sql="update student_challan set sc_surplus_amount=($surplusAmount+$adjustedAmount),invoiceamount=$newAmount, SC_UPDATED_BY_USERNAME='$username', UPDATED_AT='$updatedTime' where sc_app_id = '$app_id' and sc_session_id=$existingChallan->sc_session_id and id=$existingChallan->id";
                    $resSql=DB::insert( $sql);

                }
                DB::commit();
                $response =[
                    'success' => true,
                    'msg' => 'Saved successfully'
                ];
            }else{
               return Response::json(array(
                    'success' => false,
                    'errors' => array(
                        'adjustment' => array('Some problem occured')
                    )
                ), 400);
            }

            // dd($existingChallan);
            return response()->json($response);
        } catch (\Exception $e) {
            // Something went wrong, rollback the transaction
            DB::rollback();

            // Log the error
            // \Log::error('Error: ' . $e->getMessage());

            // Return a failure response
            return response()->json([
                'success' => false,
                'msg' => 'Something went wrong',
            ]);
        }
    }
    public function addNewfeeAdjustment($request){
        // dd($request->all());
        $validator = Validator::make($request->all(),[
                    'surplus_amount' => 'required',
                    'CategoryOption' => 'required',
                    'app_id' => 'required',
                    'student_status' => 'required'
                ]);
                if($validator->fails()) {
                    return Response::json(array(
                        'success' => false,
                        'errors' => $validator->getMessageBag()->toArray()
                    ), 400);
                }
                $userId = auth()->user()->id;

                DB::beginTransaction();
                $mesg='';

            try {
                $duplicateCheck = DB::table('FIN_FEE_ADJUSTMENT')
                                        ->select('FFA_ID', 'FFA_SURPLUS_AMOUNT')
                                        ->where(function ($query) use ($request) {
                                            $query->where('FFA_ENROLLMENT_NO', $request->enrollment_no)
                                                ->orWhere('FFA_APP_ID', $request->app_id);
                                        })
                                        ->get();

                    // dd($existingSurplus, $newSurplus, $finalSurplus);
                    $data = [
                        'FFA_ENROLLMENT_NO' =>(!empty($request->enrollment_no) && $request->enrollment_no !== 'null'? $request->enrollment_no : NULL),
                        'FFA_APP_ID' =>(!empty($request->app_id) && $request->app_id !== 'null'? $request->app_id : NULL),
                        'FFA_ADJUSTMENT_CATEGORY' =>$request->CategoryOption,
                        'FFA_SURPLUS_AMOUNT' =>$request->surplus_amount,
                        'FFA_ENTRY_USERID' =>$userId,
                        'FFA_REMARKS' =>$request->remarks,
                        'FFA_STUDENT_STATUS' =>((!empty($request->student_status) && $request->student_status !== 'null')? $request->student_status : NULL),
                        'FFA_CREATED_AT' => now(),
                        'FFA_STATUS'=>'E',
                        'FF_AMNT_STATUS'=>'E',
                    ];
                    //    dd($duplicateCheck, $data);

                    if(sizeof($duplicateCheck) == 0){
                        // dd(1);
                        //INSERT CASE
                        $check = DB::table('FIN_FEE_ADJUSTMENT')->insert($data);
                         DB::commit();
                        if($check){
                            $response =[
                                'success' => true,
                                'msg' => 'Saved successfully'
                            ];
                            return response()->json($response);
                        }

                    }else{
                        // dd(2);
                        //UPDATE CASE

                        // $check = DB::table('FIN_FEE_ADJUSTMENT')
                        //         ->where('FFA_ID', $duplicateCheck[0]->ffa_id)
                        //         ->update($data);
                        // if($check){
                        //      $response =[
                        //         'success' => true,
                        //         'msg' => 'Updated successfully'
                        //     ];
                        //     return response()->json($response);
                        // }

                        return Response::json(array(
                            'success' => false,
                            'errors' => array(
                                'adjustment' => array('Duplication not allowed')
                            )
                        ), 400);
                    }

            } catch (\Exception $e) {
                // Something went wrong, rollback the transaction
                DB::rollback();

                // Log the error
                // \Log::error('Error: ' . $e->getMessage());

                // Return a failure response
                return response()->json([
                    'success' => false,
                    'msg' => 'Something went wrong',
                ]);
            }
    }
    ///TEMPORARY CHNAGES
    // public function addfeeAdjustment(Request $request){
    //     // dd($request->all());
    //     $validator = Validator::make($request->all(),[
    //         'surplus_amount' => 'required',
    //         'CategoryOption' => 'required'
    //     ]);
    //     if($validator->fails()) {
    //         return Response::json(array(
    //             'success' => false,
    //             'errors' => $validator->getMessageBag()->toArray()
    //         ), 400);
    //     }
    //     $userId = auth()->user()->id;

    //     DB::beginTransaction();
    //     $mesg='';

    //    try {
    //         $duplicateCheck = DB::table('FIN_FEE_ADJUSTMENT')
    //                         ->select(
    //                                 'FFA_ID','FFA_SURPLUS_AMOUNT'
    //                                 )
    //                                 ->where('FFA_ENROLLMENT_NO', $request->enrollment_no)
    //                                // ->where('FFA_STATUS', 'E')
    //                                 ->get();

        // $data = [
    //         'FFA_SC_CHALLAN_NO' => (!empty($request->challan_no)? $request->challan_no : NULL),//
    //         'FFA_ENROLLMENT_NO' =>(!empty($request->enrollment_no) && $request->enrollment_no !== 'null'? $request->enrollment_no : NULL),
    //         'FFA_APP_ID' =>(!empty($request->app_id) && $request->app_id !== 'null'? $request->app_id : NULL),
    //         'FFA_ADJUSTMENT_CATEGORY' =>$request->CategoryOption,
    //         'FFA_ENTRY_USERID' =>$userId,
    //         'FFA_ACTUAL_AMOUNT' =>(!empty($request->actual_amount)? ($request->actual_amount != 'null' ? $request->actual_amount : 0) : 0),//
    //         'FFA_INVOICE_AMOUNT' =>(!empty($request->invoice_amount)? ($request->invoice_amount != 'null' ? $request->invoice_amount : 0) : 0),//
    //         'FFA_SURPLUS_AMOUNT' => $request->surplus_amount + (isset($duplicateCheck[0]->ffa_surplus_amount) ? $duplicateCheck[0]->ffa_surplus_amount : 0),
    //         // 'FFA_SURPLUS_AMOUNT' => $request->surplus_amount,
    //         'FFA_SCHOLARSHIP_AMOUNT' =>(!empty($request->scholarship_amount) && $request->scholarship_amount !== 'null'? $request->scholarship_amount : NULL),
    //         'FFA_REMARKS' =>$request->remarks,
    //         'FFA_CHALLAN_STATUS' =>(!empty($request->challan_status)? $request->challan_status : NULL),//
    //         'FFA_CHALLAN_TYPE' =>(!empty($request->challan_type)? $request->challan_type : 'fee'),//
    //         'FFA_STUDENT_STATUS' =>$request->student_status,
    //         'FFA_CREATED_AT' => now(),
    //         'FFA_STATUS'=>'E',
    //         'FF_AMNT_STATUS'=>'E',
    //     ];
        //    dd($data);

    //         if(sizeof($duplicateCheck) == 0){
    //             //INSERT CASE
    //             $check = DB::table('FIN_FEE_ADJUSTMENT')->insert($data);
    //             if($check){
    //                 $mesg = 'Saved Successfully';
    //             }

    //         }else{
    //             //UPDATE CASE

    //             $check = DB::table('FIN_FEE_ADJUSTMENT')
    //                     ->where('FFA_ID', $duplicateCheck[0]->ffa_id)
    //                     ->update($data);
    //             if($check){
    //                 $mesg = 'Updated Successfully';
    //             }
    //         }
    //         // Commit the transaction
    //         DB::commit();


    //         $sql = DB::select("SELECT SUM(ffa_surplus_amount) as v_surplus_amount,ffa_enrollment_no,ffa_id FROM fin_fee_adjustment
    //         WHERE FF_AMNT_STATUS='E'   and  FFA_STATUS='E'  and lower(ffa_enrollment_no)=lower('$request->enrollment_no')
    //         GROUP BY FFA_ENROLLMENT_NO,ffa_id");


    //      $existingChallan = DB::table('Student_challan')
    //                         ->whereRaw("TRIM(sc_challan_type) IN ('Full_semester', 'Hostel_Fee', 'Credit_hours','Fee_Refund','Short/Summer_Course')")
    //                         ->where('sc_challan_status', 'UNPAID')
    //                         ->where('id', $request->challan_no)
    //                         ->whereRaw('lower(sc_enrollment_no) = ?', [strtolower($request->enrollment_no)])
    //                         ->first();

    //     // dd($sql,$existingChallan);
    //         if($sql && $existingChallan){
    //         // echo $sql;exit;
    //         $row= $sql[0];
    //         $surplus_amount = $row->v_surplus_amount;//sum of all coming from DB
    //         $enrollment_no = $row->ffa_enrollment_no;
    //         $adjustedAmount=$existingChallan->sc_surplus_amount;

    //         $ffaStatus='';
    //         $ffAmntStatus='';
    //         $surplus_amount1='';

    //         $flag='pending';
    //         // dd('surplus_amount-> '.$surplus_amount, 'existingChallan->invoiceamount-> '.$existingChallan->invoiceamount, 'request->surplus_amount-> '.$request->surplus_amount );
    //         if($existingChallan && $request->surplus_amount <= $existingChallan->invoiceamount){
    //             // dd(1);
    //             $flag='Paid';
    //             $ffaStatus='D';
    //             $ffAmntStatus='D';
    //             $updatedAmount=$existingChallan->invoiceamount-$request->surplus_amount;
    //             $surplus_amount1=0;

    //              $sql="update fin_fee_adjustment set ffa_sc_challan_no=$existingChallan->id,ffa_surplus_amount=0 ,ffa_amount_status='$flag',ffa_status='$ffaStatus',ff_amnt_status='$ffAmntStatus'  where ffa_id = $row->ffa_id";
    //              $resSql=DB::insert( $sql);
    //             //  $sel="update student_challan set sc_surplus_amount=($surplus_amount+$adjustedAmount),invoiceamount=$updatedAmount where sc_enrollment_no = '$enrollment_no' and sc_session_id=$existingChallan->sc_session_id  and id=$existingChallan->id";
    //              $sel="update student_challan set sc_surplus_amount=($request->surplus_amount+$adjustedAmount),invoiceamount=$updatedAmount where sc_enrollment_no = '$enrollment_no' and sc_session_id=$existingChallan->sc_session_id  and id=$existingChallan->id";
    //              $resSel=DB::insert( $sel);

    //         }else if($existingChallan && $request->surplus_amount > $existingChallan->invoiceamount){
    //             // dd(2);
    //             $ffaStatus='E';
    //             $ffAmntStatus='E';
    //             $surplusAmount=$surplus_amount-$existingChallan->invoiceamount;
    //             if($surplusAmount>0){
    //                 $surplusAmount=$surplus_amount-$surplusAmount;

    //             }
    //             $newAmount=0;

    //             $sel="update fin_fee_adjustment set ffa_sc_challan_no=$existingChallan->id,ffa_surplus_amount= $surplus_amount-$existingChallan->invoiceamount,ffa_amount_status='$flag',ffa_status='$ffaStatus',ff_amnt_status='$ffAmntStatus'  where ffa_id = $row->ffa_id";
    //            $resSel=DB::insert( $sel);
    //             // $sql="update student_challan set sc_surplus_amount=($surplusAmount+$adjustedAmount),invoiceamount=$newAmount where sc_enrollment_no = '$enrollment_no' and sc_session_id=$existingChallan->sc_session_id and id=$existingChallan->id";
    //             $sql="update student_challan set sc_surplus_amount=($request->surplus_amount+$adjustedAmount),invoiceamount=$newAmount where sc_enrollment_no = '$enrollment_no' and sc_session_id=$existingChallan->sc_session_id and id=$existingChallan->id";
    //             $resSql=DB::insert( $sql);

    //         }

    //     }

    //     // dd($existingChallan);
    //         return response()->json([
    //             'success' => true,
    //             'msg' => $mesg
    //         ]);
    //    } catch (\Exception $e) {
    //         // Something went wrong, rollback the transaction
    //         DB::rollback();

    //         // Log the error
    //         // \Log::error('Error: ' . $e->getMessage());

    //         // Return a failure response
    //         return response()->json([
    //             'success' => false,
    //             'msg' => 'Something went wrong',
    //         ]);
    //     }
    // }
    public function delete_adjustment(Request $request){
        $userId = auth()->user()->id;
        $affectedRows =  DB::table('FIN_FEE_ADJUSTMENT')
                            ->whereIn('FFA_ID', $request->mainId)
                            ->update([
                                'FFA_STATUS' => 'D',
                                'FFA_ENTRY_USERID' =>$userId
                            ]);
        if ($affectedRows > 0) {
            return response()->json([
                'success' => true,
                'messege' => 'Deleted Successfully'
        ]);
        } else {
            return response()->json([
                'success' => false,
                'messege' => 'Some problem occured'
            ]);
        }

    }
    public function getFeeAdjustmentList(){
        $data = DB::table('FIN_FEE_ADJUSTMENT as ffa')
            ->select(
                    'ffa.FFA_ID',
                    'ffa.FFA_SC_CHALLAN_NO',
                    DB::raw('COALESCE(sai.sai_enrollment_no, sai_legacy.sai_enrollment_no, ffa.FFA_ENROLLMENT_NO) as ffa_enrollment_no'),
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
                    ->leftJoin('ADMISSION_APPLY_INFO as aai', 'ffa.FFA_APP_ID', '=', 'aai.aai_id')
                    ->leftJoin('ADMISSION_PERSONAL_INFO as api', 'api.API_ID', '=', 'aai.AAI_API_ID')
                    ->leftJoin('STUDENTS_APPLY_INFO as sai', function($join) {
                        $join->on('sai.SAI_API_ID', '=', 'api.API_ID')
                            ->whereRaw('LOWER(sai.sai_enrollment_no) = LOWER(aai.CMS_SAI_ENROLLMENT_NO)');
                    })
                    /** Legacy: FFA_APP_ID stores SAI_ID when student has no admission_apply_info row */
                    ->leftJoin('STUDENTS_APPLY_INFO as sai_legacy', 'ffa.FFA_APP_ID', '=', 'sai_legacy.SAI_ID')
                    ->leftJoin('STUDENTS_PERSONAL_INFO as spi', 'spi.API_ID', '=', 'api.API_ID')
                    //->where('FFA_STATUS', 'E')
                    ->orderBy('ffa.FFA_CREATED_AT', 'desc')
                    ->get();
                // dd($data);
            return DataTables::of($data)->toJson();
    }
    public function getAudFeeAdjustmentList(Request $request){
        // dd($request->challanStatus);
        $data = DB::table('AUD_BNUCMS.FIN_FEE_ADJUSTMENT_VER as ffaa')
            ->select(
                    'ffaa.FFA_ID',
                    'ffaa.FFA_SC_CHALLAN_NO',
                    DB::raw('COALESCE(sai.sai_enrollment_no, sai_legacy.sai_enrollment_no, ffaa.FFA_ENROLLMENT_NO) as ffa_enrollment_no'),
                    'ffaa.FFA_APP_ID',
                    'ffaa.FFA_SURPLUS_AMOUNT',
                    'ffaa.OLD_SURPLUS_AMNT',
                    'ffaa.FFA_USED_AMOUNT',
                    'ffaa.FFA_ADJUST_AMOUNT',
                    'ffaa.FFA_STATUS_REMARKS as FFA_REMARKS',
                    'ffaa.CHANGED_ON',
                    // 'ffaa.FFA_ACTUAL_AMOUNT',
                    // 'ffaa.FFA_INVOICE_AMOUNT',
                    // 'ffaa.FFA_CHALLAN_STATUS',
                    // 'ffaa.FFA_STUDENT_STATUS',
                    'ffaa.FFA_AMOUNT_STATUS',
                    'ffaa.FFA_CHALLAN_TYPE',
                    // 'ffaa.FFA_SCHOLARSHIP_AMOUNT',

                    'ffac.FFAC_ID',
                    'ffac.FFAC_TITLE',
                    'ffc.NAME'
                    )
                    ->leftjoin('FIN_FEE_ADJUSTMENT_CATEGORY as ffac', 'ffac.FFAC_ID', 'ffaa.FFA_ADJUSTMENT_CATEGORY')
                    ->leftjoin('FIN_FEE_CATEGORY as ffc', 'ffc.ID', 'ffac.FFAC_FEE_CTGRY')
                    ->leftJoin('ADMISSION_APPLY_INFO as aai', 'ffaa.FFA_APP_ID', '=', 'aai.aai_id')
                    ->leftJoin('ADMISSION_PERSONAL_INFO as api', 'api.API_ID', '=', 'aai.AAI_API_ID')
                    ->leftJoin('STUDENTS_APPLY_INFO as sai', function($join) {
                        $join->on('sai.SAI_API_ID', '=', 'api.API_ID')
                            ->whereRaw('LOWER(sai.sai_enrollment_no) = LOWER(aai.CMS_SAI_ENROLLMENT_NO)');
                    })
                    ->leftJoin('STUDENTS_APPLY_INFO as sai_legacy', 'ffaa.FFA_APP_ID', '=', 'sai_legacy.SAI_ID')
                    ->leftJoin('STUDENTS_PERSONAL_INFO as spi', 'spi.API_ID', '=', 'api.API_ID');
                    //->where('FFA_STATUS', 'E')
                    // ->orderBy('ffaa.FFA_CREATED_AT', 'desc')
                if(!empty($request->challanStatus)){
                    $data = $data->where('ffaa.FFA_AMOUNT_STATUS', $request->challanStatus);
                }

               $data =  $data->get();
                // dd($data);
            return DataTables::of($data)->toJson();
    }
    public function changeStatusFeeAdjustment(Request $request){
        // dd($request->all());
        $userId = auth()->user()->id;
        $validator = Validator::make($request->all(),[
            'status' => 'required',
            'amount' => 'required|numeric'
        ]);
        if($validator->fails()) {
            return Response::json(array(
                'success' => false,
                'errors' => $validator->getMessageBag()->toArray()
            ), 400);
        }
         $surplusAmount = DB::table('FIN_FEE_ADJUSTMENT')
                ->where('FFA_ID', $request->main_id)
                ->value('FFA_SURPLUS_AMOUNT');

        if($request->status != 'Unpaid'){//subtracting in all other cases then unpaid
             if($surplusAmount < $request->amount){
                return response()->json([
                    'success' => false,
                    'data' => ['Not enough Surplus amount present']
                ]);
            }
            $newsurplusAmount = $surplusAmount - $request->amount;
             $data=['ffa_amount_status' => ($newsurplusAmount == 0) ? 'Paid':'Pending',
                    'FFA_SURPLUS_AMOUNT' => $newsurplusAmount,
                    'FFA_ADJUST_AMOUNT' => $request->amount,
                    'FFA_STATUS_REMARKS' => $request->status_remarks,
                    'FF_AMNT_STATUS' => ($newsurplusAmount == 0) ? 'D':'E',
                    'FFA_STATUS' => ($newsurplusAmount == 0) ? 'D':'E',
                    'FFA_ENTRY_USERID' =>$userId
                ];
        }else{//Adding in unpaid case
            $newsurplusAmount = $surplusAmount + $request->amount;
            $data=['ffa_amount_status' => 'Pending',
                    'FFA_SURPLUS_AMOUNT' => $newsurplusAmount,
                    'FFA_ADJUST_AMOUNT' => $request->amount,
                    'FFA_STATUS_REMARKS' => $request->status_remarks,
                      'FF_AMNT_STATUS' => 'E',
                    'FFA_STATUS' => 'E',
                    'FFA_ENTRY_USERID' =>$userId
                ];
        }
        // dd($surplusAmount,  $request->amount, $newsurplusAmount);
        $affectedRows =  DB::table('FIN_FEE_ADJUSTMENT')
                            ->where('FFA_ID', $request->main_id)
                            ->update($data);
        if ($affectedRows > 0) {
            return response()->json([
                'success' => true,
                'data' => 'Updated Successfully'
        ]);
        } else {
            return response()->json([
                'success' => false,
                'data' => ['Some problem occured']
            ]);
        }

    }
    function checkEnrollmentORaai_id($rollNo_OR_aai_id){
        $containsHyphen = strpos($rollNo_OR_aai_id, '-') !== false;// to check if contains hyphen
        $containsLetter = false;
        for ($i = 0; $i < strlen($rollNo_OR_aai_id); $i++) {//to check if contain alphabet
            if (ctype_alpha($rollNo_OR_aai_id[$i])) {
                $containsLetter = true;
                break;
            }
        }
        $containsOnlyNumbers = ctype_digit($rollNo_OR_aai_id); // to check if the only numbers
        if ($containsHyphen && $containsLetter) {
           return 'enrollment';
        } elseif ($containsOnlyNumbers) {
           return 'aaiId';
        } else {
            return 'invalid';
        }
    }

}
