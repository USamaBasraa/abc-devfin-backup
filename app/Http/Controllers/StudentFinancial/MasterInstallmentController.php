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

class MasterInstallmentController extends Controller{

        public function masterInstallmentForm(){
            $sessions = \App::call('App\Http\Controllers\Grader\AcademicSessionController@retrieve');
            $schools =  Schools::all();
            $departments = array();
            $programs =  DB::select("select * from Programs where PROG_STATUS = 'E'");
            $query = 'select * from fin_fee_category';
            $feeCategories = DB::select($query);
            return view('StudentFinancial.masterInstallmentform',compact('sessions','schools','departments','feeCategories','programs'));

        }
        public function addInstallment(Request $request){
            // dd($request->all());

            $validator = Validator::make($request->all(),[
                'session' => 'required',
                'school' => 'required',
                'department' => 'required',
                'degree_level' => 'required',
                'program' => 'required',
                'student_type' => 'required',
                'second_installment_due_date' => 'required',
                'third_installment_due_date' => 'required',
                'second_installment_valid_date' => 'required',
                'third_installment_valid_date' => 'required',
            ]);
            if($validator->fails()) {
                return Response::json(array(
                    'success' => false,
                    'errors' => $validator->getMessageBag()->toArray()
                ), 400);
            }
            $second_installment_due_date = strtotime($request->get('second_installment_due_date'));
            $third_installment_due_date = strtotime($request->get('third_installment_due_date'));
            $second_installment_valid_date = strtotime($request->get('second_installment_valid_date'));
            $third_installment_valid_date = strtotime($request->get('third_installment_valid_date'));
            if ($second_installment_due_date > $third_installment_due_date) {
                return Response::json([
                    'success' => false,
                    'errors' => [
                        'due_date' => ['The 2nd installment date must be before 3rd installment date.'],
                    ],
                ], 400);
            }
           
            $studentType='';


            if($request->student_type == 'New without Registration FALL' || $request->student_type == 'New without Registration SPRING' || $request->student_type == 'New without Registration SUMMER'){//new admission without registration case
                $string = $request->student_type;
                $search = 'New without Registration';
                $replace = 'New Admission';
                $studentType = str_replace($search, $replace, $string);
            }else{// existing student and new admission with registration case
                $studentType  = $request->student_type;
            }
            // dd($studentType);
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
            // dd($request->session, $school_depart_prog, $request->degree_level,$studentType );
            // updte data into FIN_BLK_CHALLAN_GEN table
            $userid = auth()->user()->id;
            DB::beginTransaction();
            try {
                foreach($school_depart_prog as $val){
                    $schoolId = $val[0];
                    $deptId = $val[1];
                    $progId = $val[2];
                    // dd($request->session, $schoolId, $deptId, $progId, $request->degree_level,$studentType );
                    $updatedRow = DB::table('FIN_BLK_CHALLAN_GEN')
                        ->where('FBC_SESSION_ID', $request->session)
                        ->where('FBC_SCHOOL_ID', $schoolId)
                        ->where('FBC_DEPARTMENT_ID', $deptId)
                        ->where('FBC_PROGRAM_ID', $progId)
                        ->where('FBC_DEGREE_LEVEL', $request->degree_level)
                        ->where('FBC_STUDENT_TYPE', $studentType)
                        ->where('FBC_STATUS', 'E')
                        ->update([
                            'fbc_2nd_installment_due_date' => $request->second_installment_due_date,
                            'fbc_3rd_installment_due_date' => $request->third_installment_due_date,
                            'fbc_2nd_installment_valid_date' => $request->second_installment_valid_date,
                            'fbc_3rd_installment_valid_date' => $request->third_installment_valid_date,
                            'FBC_ENTRY_USER_ID' => $userid
                        ]);
                }
                // Commit the transaction if all updates succeed
                DB::commit();
                // return response()->json(['success' => true, 'message' => 'Records Updated successfully'], 200);
            } catch (\Exception $e) {
                // Rollback the transaction if any exception occurs
                DB::rollBack();
                return response()->json(['success' => false, 'messege'=> 'Something went wrong.', 'errors' => $e->getMessage()], 500);
            }
            return response()->json(['success' => true, 'mesg'=> 'Installment dates updated']);
           

        }
        // public function get_challan_list(Request $request){
        //     // dd($request->all());
        //     //dd(json_decode($request->school_id), json_decode($request->department_id), json_decode($request->program_id), $request->program_id);
        //     // dd();
        //     // if(!is_array(json_decode($request->department))){
        //     //     dd('single dep', );
        //     // }else{
        //     //     dd('all dep');
        //     // }
        //     $data = DB::table('student_challan as sc')
        //         ->select(
        //                 'sc.ID',
        //                 'sc.SC_ENROLLMENT_NO',
        //                 'sc.SC_APP_ID',
        //                 'SC_STUDENTNAME',
        //                 'acd.SESS_NAME',
        //                 'school.SCHOOL_NAME',
        //                 'dept.DEPT_NAME',
        //                 'prog.PROG_NAME',
        //                 'sc.SC_TUITION_FEE_AMNT',
        //                 'sc.FFS_ADMISSION_AMT',
        //                 'sc.FFS_SECURITY_AMT',
        //                 'sc.FFS_MEDICAL_AMT',
        //                 'sc.FFS_CREDHR_AMT',
        //                 'sc.SC_ACTIVITY_AMT',
        //                 'sc.SC_CHALLAN_STATUS',
        //                 DB::raw('TO_CHAR(sc.SC_ISSU_DATE, \'DD-MM-YYYY\') as SC_ISSU_DATE'),
        //                 DB::raw('TO_CHAR(sc.SC_DUE_DATE, \'DD-MM-YYYY\') as SC_DUE_DATE'),
        //                 DB::raw('TO_CHAR(sc.SC_VALID_DATE, \'DD-MM-YYYY\') as SC_VALID_DATE'),
        //                 'sc.INVOICEAMOUNT',

        //                 'sc.SC_SESSION_ID',
        //                 'sc.SC_SCHOOL_ID',
        //                 'sc.SC_DEPARTMENT_ID',
        //                 'sc.SC_PROGRAM_ID'
        //                 )
        //                 ->join('ACADEMIC_SESSIONS as acd', 'acd.SESS_ID', 'sc.SC_SESSION_ID')
        //                 ->join('SCHOOLS as school', 'school.SCHOOL_ID', 'sc.SC_SCHOOL_ID')
        //                 ->join('DEPARTMENTS as dept', 'dept.DEPT_ID', 'sc.SC_DEPARTMENT_ID')
        //                 ->join('PROGRAMS as prog', 'prog.PROG_ID', 'sc.SC_PROGRAM_ID')
        //                 ->where('sc.INVOICE_STATUS', 'E');
        //                 if($request->unpaidCheckbox == 1){//unpaid checkbox is CHECKED apply only session filter and unpaid and semester fees IGNORE other fields
        //                     if(!empty($request->session_id)){
        //                         $data =  $data->where('sc.SC_SESSION_ID', $request->session_id)
        //                                         ->whereRaw("TRIM(sc.SC_CHALLAN_TYPE) = 'Full_semester'")
        //                                         ->where('sc.SC_CHALLAN_STATUS', 'UNPAID');
        //                     }
        //                 }else{//unpaid checkbox is NOT CHECKED apply all filters as selected
        //                     if(!empty($request->session_id)){
        //                         $data =  $data->where('sc.SC_SESSION_ID', $request->session_id);
        //                     }
        //                     if(!empty($request->school_id) && !is_array(json_decode($request->school_id))){//condition will applied only when single school is selected if all school is slected then no need to apply condition//array means all school is selected and so we apply not array condtion to apply where clause;
        //                     $data =  $data->where('sc.SC_SCHOOL_ID', $request->school_id);
        //                     }
        //                     if(!empty($request->department_id) && !is_array(json_decode($request->department_id))){//condition will applied only when single department is selected if all department is slected then no need to apply condition//array means all department is selected and so we apply not array condtion to apply where clause;
        //                         $data =  $data->where('sc.SC_DEPARTMENT_ID', $request->department_id);
        //                     }
        //                     if(!empty($request->program_id) && !is_array(json_decode($request->program_id))){//condition will applied only when single program is selected if all program is slected then no need to apply condition//array means all program is selected and so we apply not array condtion to apply where clause;
        //                     //    dd('program condtion applied');
        //                         $programs_id = $request->program_id;
        //                         // dd($programs_id);
        //                         $array_of_sch_dep_prog = explode('~~', $programs_id);
        //                         // dd($array_of_sch_dep_prog[2]);
        //                     $data =  $data->where('sc.SC_PROGRAM_ID', $array_of_sch_dep_prog[2]);
        //                     }
        //                     if(!empty($request->studentType)){
        //                         if(strpos($request->studentType, 'New without Registration') !== false){//if 'New without Registration' exists in student type
        //                             $string = $request->studentType;
        //                             $search = 'New without Registration';
        //                             $replace = 'New Admission';
        //                             $studentType = str_replace($search, $replace, $string);
        //                             // dd('New without Registration', $studentType);
        //                             $data =  $data->where('sc.SC_STUDENT_STATUS', $studentType)->whereNull('sc.SC_ENROLLMENT_NO');
        //                         }else{
        //                             // dd('New with Registration', $request->studentType);
        //                             $data =  $data->where('sc.SC_STUDENT_STATUS', $request->studentType)->whereNotNull('sc.SC_ENROLLMENT_NO');
        //                         }
        //                     }
        //                     if(!empty($request->fee_type)){
        //                         $data =  $data->where('sc.SC_CHALLAN_TYPE', $request->fee_type);
        //                     }
        //                 }

        //                 $data =  $data->get();
        //     return DataTables::of($data)->toJson();

        // }
        // public function get_student_list(Request $request){
        //     //dd($request->all());
        //     // $school = $request->school_id;
        //     $department= $request->department_id;
        //     $subqueryOptionalWhere ="";
        //     $sqlSessionCode = "select * from academic_sessions where sess_id = $request->session_id";
        //     $ressqlSessionCode = DB::select($sqlSessionCode);
        //     $sess_name = explode(' ', $ressqlSessionCode[0]->sess_name)[0];
        //     if(!empty($request->studentStatus)){
        //         $subqueryOptionalWhere = " and ssr_semester_status = '".$request->studentStatus."'";//this is for the subquery to concatenate the where clause of 'active' or 'expected graduate'
        //     }else{
        //         $subqueryOptionalWhere = " and ssr_semester_status IN ('Expected-Graduate', 'Active')";
        //     }
        //     // dd($subqueryOptionalWhere);
        //     // dd("sai.sai_enrollment_no = (select sai_enrollment_nofrom students_semester_recordwhere students_semester_record.sai_enrollment_no = sai.sai_enrollment_noand sess_id =  ".$request->session_id."".$subqueryOptionalWhere.")");
        //     if(strpos($request->studentType, 'New without Registration') !== false){
        //         $challanType='New Admission SPRING';
        //         $data = DB::table('ADMISSION_APPLY_INFO as aai')
        //                 ->select(
        //                         'aai.AAI_ID',
        //                         'aai.AAI_STATUS',
        //                         'aai.cms_sai_enrollment_no',
        //                         'api.API_NAME',

        //                         'acd.SESS_NAME',
        //                         'school.SCHOOL_NAME',
        //                         'dept.DEPT_NAME',
        //                         'prog.PROG_NAME',
        //                         DB::raw("(SELECT COUNT(*) FROM student_challan sc WHERE sc.SC_APP_ID = aai.AAI_ID and sc_student_status = '.$challanType.' and sc_enrollment_no IS NULL) as challan_count") // Raw query to count records
        //                         )
        //                         ->leftjoin('ADMISSION_PERSONAL_INFO as api', 'api.API_ID', 'aai.AAI_API_ID')
        //                         ->leftjoin('ACADEMIC_SESSIONS as acd', 'acd.SESS_ID', 'aai.AAI_SDPO_SESS_ID')
        //                         ->leftjoin('SCHOOLS as school', 'school.SCHOOL_ID', 'aai.AAI_SDPO_SCHOOL_ID')
        //                         ->leftjoin('DEPARTMENTS as dept', 'dept.DEPT_ID', 'aai.AAI_SDPO_DEPT_ID')
        //                         ->leftjoin('PROGRAMS as prog', 'prog.PROG_ID', 'aai.AAI_SDPO_PROG_ID')
        //                         ->whereNull('aai.cms_sai_enrollment_no')
        //                         ->where('aai.aai_status', 'Selected')
        //                         ->where('aai.aai_pf_status', 'Fee Verified')
        //                         ->where('aai.AAI_QUALIFICATION_STATUS', 'Accepted');

        //                         if(!empty($request->session_id)){
        //                             $data = $data->where('aai.AAI_SDPO_SESS_ID','=', $request->session_id);
        //                         }
        //                         if(!empty($request->school_id && !is_array(json_decode($request->school_id)))){
        //                             $data = $data->where('aai.AAI_SDPO_SCHOOL_ID', $request->school_id);
        //                         }
        //                         if(!empty($request->department_id && !is_array(json_decode($request->department_id)))){
        //                             $data = $data->where('aai.AAI_SDPO_DEPT_ID', $request->department_id);
        //                         }
        //                         if(!empty($request->degree_level)){
        //                             $data = $data->where('prog.PROG_DEGREE_LEVEL', $request->degree_level);
        //                         }
        //                         if(!empty($request->program_id) && !is_array(json_decode($request->program_id))){//condition will applied only when single program is selected if all program is slected then no need to apply condition
        //                             //    dd('program condtion applied');
        //                             $programs_id = $request->program_id;
        //                             // dd($programs_id);
        //                             $array_of_sch_dep_prog = explode('~~', $programs_id);
        //                             // dd($array_of_sch_dep_prog[2]);
        //                         $data =  $data->where('aai.AAI_SDPO_PROG_ID', $array_of_sch_dep_prog[2]);
        //                         }
        //         $data = $data->get();
        //     }else{
        //         $data = DB::table('STUDENTS_APPLY_INFO as sai')
        //                 ->select(
        //                         'sai.SAI_ID',
        //                         'sai.SAI_STUDENT_STATUS',
        //                         'sai.SAI_ENROLLMENT_NO',
        //                         // 'sai.SAI_PROGRAMID',
        //                         // 'sai.SAI_DEGREECODE',

        //                         'spi.SPI_NAME',

        //                         'acd.SESS_NAME',
        //                         'school.SCHOOL_NAME',
        //                         'dept.DEPT_NAME',
        //                         'prog.PROG_NAME',
        //                         DB::raw('(SELECT COUNT(*) FROM student_challan sc WHERE sc.SC_ENROLLMENT_NO = sai.SAI_ENROLLMENT_NO) as challan_count') // Raw query to count records
        //                         )
        //                         //->leftjoin('student_challan as sc', 'sc.SC_ENROLLMENT_NO', 'sai.SAI_ENROLLMENT_NO')
        //                         ->leftjoin('STUDENTS_PERSONAL_INFO as spi', 'spi.SPI_ID', 'sai.SAI_SPI_ID')
        //                         ->leftjoin('ACADEMIC_SESSIONS as acd', 'acd.SESS_ID', 'sai.SAI_SDPO_SESS_ID')
        //                         ->leftjoin('SCHOOLS as school', 'school.SCHOOL_ID', 'sai.SAI_SDPO_SCHOOL_ID')
        //                         ->leftjoin('DEPARTMENTS as dept', 'dept.DEPT_ID', 'sai.SAI_SDPO_DEPT_ID')
        //                         ->leftjoin('PROGRAMS as prog', 'prog.PROG_ID', 'sai.SAI_SDPO_PROG_ID')
        //                         ->whereIn('sai.SAI_STUDENT_STATUS', ['Expected-Graduate', 'Active']);
        //                         // ->where('sai.SAI_STUDENT_STATUS', 'Active')
        //                         // ->whereNull('sc.SC_ENROLLMENT_NO');
        //                         if(!empty($request->session_id)){
        //                             // if(!empty($request->studentType)){
        //                             //     if($request->studentType == 'Existing'){//existing students case
        //                             //         $data = $data->where('sai.SAI_SDPO_SESS_ID','<', $request->session_id);
        //                             //     }else{//new student case
        //                             //         $data = $data->where('sai.SAI_SDPO_SESS_ID','=', $request->session_id);
        //                             //     }
        //                             // }
        //                             ///////////////////
        //                             // $data = $data->whereRaw("sai.sai_enrollment_no = (
        //                             //                             select sai_enrollment_no
        //                             //                                 from students_semester_record
        //                             //                                     where students_semester_record.sai_enrollment_no = sai.sai_enrollment_no
        //                             //                                     and sess_id =  ".$request->session_id."".$subqueryOptionalWhere.")");
        //                             // if($request->studentType == 'Existing'){//existing students case
        //                             //     $data = $data->where('sai.SAI_SDPO_SESS_ID','<', $request->session_id);
        //                             // }elseif($request->studentType == 'New Admission FALL' && $sess_name == 'Spring'){//new student case
        //                             //     $data = $data->where('sai.SAI_SDPO_SESS_ID','=', $request->session_id-1);
        //                             // }else{//new student case
        //                             //     $data = $data->where('sai.SAI_SDPO_SESS_ID','=', $request->session_id);
        //                             // }
        //                             if(!empty($request->studentType)){
        //                                 if($request->studentType == 'Existing' && $sess_name == 'Spring'){//existing students case
        //                                     $data = $data->where('sai.SAI_SDPO_SESS_ID','<', $request->session_id-1);

        //                                 }elseif($request->studentType == 'New Admission FALL' && $sess_name == 'Spring'){//new student case
        //                                     $data = $data->where('sai.SAI_SDPO_SESS_ID','=', $request->session_id-1);
        //                                 }else{//new student case
        //                                     $data = $data->where('sai.SAI_SDPO_SESS_ID','=', $request->session_id);
        //                                 }
        //                             }

        //                         }
        //                         if(!empty($request->school_id && !is_array(json_decode($request->school_id)))){
        //                             $data = $data->where('sai.SAI_SDPO_SCHOOL_ID', $request->school_id);
        //                         }
        //                         if(!empty($request->department_id && !is_array(json_decode($request->department_id)))){
        //                             $data = $data->where('sai.SAI_SDPO_DEPT_ID', $request->department_id);
        //                         }
        //                         if(!empty($request->degree_level)){
        //                             $data = $data->where('prog.PROG_DEGREE_LEVEL', $request->degree_level);
        //                         }
        //                         if(!empty($request->program_id) && !is_array(json_decode($request->program_id))){//condition will applied only when single program is selected if all program is slected then no need to apply condition
        //                             //    dd('program condtion applied');
        //                             $programs_id = $request->program_id;
        //                             // dd($programs_id);
        //                             $array_of_sch_dep_prog = explode('~~', $programs_id);
        //                             // dd($array_of_sch_dep_prog[2]);
        //                         $data =  $data->where('sai.SAI_SDPO_PROG_ID', $array_of_sch_dep_prog[2]);
        //                         }
        //                         if(!empty($request->studentStatus)){
        //                             $data = $data->where('sai.SAI_STUDENT_STATUS', $request->studentStatus);
        //                         }
        //         $data = $data->get();
        //         // dd($data);
        //     }

        //                     //  dd($data);
        //     return DataTables::of($data)->toJson();
        // }

        // public function get_feeAdjusted_list(Request $request){
        //     $data = DB::table('FIN_FEE_ADJUSTMENT as ffa')
        //     ->select(
        //             'ffa.FFA_ID',
        //             'ffa.FFA_SC_CHALLAN_NO',
        //             'ffa.FFA_ENROLLMENT_NO',
        //             'ffa.FFA_APP_ID',
        //             'ffa.FFA_SURPLUS_AMOUNT',
        //             'ffa.FFA_REMARKS',
        //             'ffa.FFA_ACTUAL_AMOUNT',
        //             'ffa.FFA_INVOICE_AMOUNT',
        //             'ffa.FFA_CHALLAN_STATUS',
        //             'ffa.FFA_STUDENT_STATUS',
        //             'ffa.ffa_amount_status',
        //             'ffa.FFA_CHALLAN_TYPE',
        //             'ffa.FFA_SCHOLARSHIP_AMOUNT',

        //             'ffac.FFAC_ID',
        //             'ffac.FFAC_TITLE',
        //             'ffc.NAME'
        //             )
        //             ->leftjoin('FIN_FEE_ADJUSTMENT_CATEGORY as ffac', 'ffac.FFAC_ID', 'ffa.FFA_ADJUSTMENT_CATEGORY')
        //             ->leftjoin('FIN_FEE_CATEGORY as ffc', 'ffc.ID', 'ffac.FFAC_FEE_CTGRY')
        //             ->where('FFA_STATUS', 'E')
        //             ->orderBy('ffa.FFA_CREATED_AT', 'desc')
        //             ->get();
        //         // dd($data);
        //     return DataTables::of($data)->toJson();
        // }
        // private static function getSchool_dep_prog_inSeparatArrays($inputArray){
        //     $allSchools = [];
        //     $allDep = [];
        //     $allProg = [];

        //     foreach ($inputArray as $item) {
        //         if (!in_array($item[0], $allSchools)) {
        //             $allSchools[] = $item[0];
        //         }
        //         if (!in_array($item[1], $allDep)) {
        //             $allDep[] = $item[1];
        //         }
        //         if (!in_array($item[2], $allProg)) {
        //             $allProg[] = $item[2];
        //         }
        //     }

        //     return [$allSchools, $allDep, $allProg];
        // }
        // function print_challan(Request $request){
        //     // dd($request->all());
        //     $challan_no = $request->challanNo;
        //     // Get Personal Info and fees other then courses
        //     //$data_qry='select * from MV_feechallanwisesummary where BuyerCodeChallanNo='.$challan_no;
        //     $data_qry='select
        //     scholarship_type.scholarship_name,
        //     schools.school_name,
        //     dept_name,
        //     programs.prog_name,
        //     sess_name,
        //     student_challan.*,
        //     fin_scholarship_discounts.*,
        //     sc_surplus_amount,
        //     sc_activity_amt,
        //     sc_studentname,
        //     sc_fathername,

        //     (select FBC_VISIBLE_DATE from FIN_BLK_CHALLAN_GEN where fbc_school_id = sc_school_id and fbc_department_id = sc_department_id and fbc_program_id = sc_program_id and rownum =1) as visible_date
        //     from student_challan
        //     LEFT join fin_scholarship_discounts on  sc_app_id=fin_scholarship_discounts.fsd_aai_id
        //     LEFT JOIN scholarship_type ON fin_scholarship_discounts.fsd_scholarship_type=scholarship_type.scholarship_id
        //     LEFT JOIN fin_fee_adjustment ON fin_fee_adjustment.ffa_enrollment_no = student_challan.sc_enrollment_no
        //     left join schools on school_id = sc_school_id
        //     left join departments on dept_id = sc_department_id
        //     left join programs on prog_id = sc_program_id
        //     left join academic_sessions on sess_id = sc_session_id
        //     where student_challan.id='.$challan_no;
        //     echo $data_qry;exit;
        //         $data = DB::select($data_qry);
        //         // Get all courses with fee --fin_scholarship_discounts.fsd_enrollment_no=student_challan.sc_enrollment_no
        //         //$data_det_qry='select * from feechallanwisesummary_det@acc where "BuyerCodeChallanNo"='.$challan_no.' order by "srno" asc';
        //         $data_det_qry='select * from student_challan
        //         LEFT join fin_scholarship_discounts on sc_app_id=fin_scholarship_discounts.fsd_aai_id
        //         and fin_scholarship_discounts.fsd_sesion_id = student_challan.sc_session_id
        //         LEFT JOIN scholarship_type ON fin_scholarship_discounts.fsd_scholarship_type=scholarship_type.scholarship_id
        //         where id='.$challan_no.' order by id asc';
        //         $data_detail = DB::select($data_det_qry);
        //     // echo $data_qry;exit;
        //         //    dd($data, $data_detail);
        //         return view('grader.financial.print_challan_preview',array('obj' => $data,'details' => $data_detail));

        // }
}
