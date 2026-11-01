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
use App\Imports\StudentSecurityDeposits;
use Illuminate\Support\Facades\Auth;
use App\Models\Grader\Schools;
use App\Models\Grader\Departments;
use Response;
use Validator;
use DataTables;
use DateTime;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use \Maatwebsite\Excel\Validators\ValidationException;
use Illuminate\Support\Facades\Storage;


class GenerateConvocationBulkChallanController extends Controller{

        public function convocationChallanPage(){
            $sessions = \App::call('App\Http\Controllers\Grader\AcademicSessionController@retrieve');
            $schools =  Schools::all();
            $departments = array(); // $this->school->fetch();
            $programs =  DB::select("select * from Programs where PROG_STATUS = 'E'");
            $query = 'select * from fin_fee_category';
            $feeCategories = DB::select($query);
            $Scholarship_Type = "select * from Scholarship_Type where scholarship_status = 'E' and SCHOLARSHIP_ID in (1,2)";
            $recScholarship_Type = DB::select($Scholarship_Type);
             $query = 'select * from semesters order by SEMESTER_ID';
            $semesters = DB::select($query);
            return view('StudentFinancial.generateConvocationChallan',compact('sessions','schools','departments','feeCategories','programs', 'recScholarship_Type','semesters'));

        }
        public function importStudentSecurityData(Request $request){
            $request->validate([
                'file_name' => 'required|file|mimes:xlsx,xls,csv'
            ]);

            try {
                if ($request->hasFile('file_name')) {
                    $path = $request->file('file_name')->store('temp');
                    $filepath = storage_path('app/' . $path);

                    // Import with batch/chunk for better performance
                    Excel::import(new \App\Imports\StudentSecurityDeposits($filepath), $filepath);

                    Storage::delete($path);

                    // Return JSON response for AJAX
                    return response()->json([
                        'status' => 'success',
                        'message' => 'Student Security Deposits imported successfully!'
                    ]);
                }

                return response()->json([
                    'status' => 'error',
                    'message' => 'No file uploaded.'
                ], 400);
            } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
                $errors = [];
                foreach ($e->failures() as $failure) {
                    $errors[] = "Row {$failure->row()}: " . implode(', ', $failure->errors());
                }

                return response()->json([
                    'status' => 'error',
                    'message' => implode("\n", $errors)
                ], 422);
            } catch (\Exception $e) {
                \Log::error('Student Security Deposit Import Error', [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ]);

                return response()->json([
                    'status' => 'error',
                    'message' => 'Import failed: ' . $e->getMessage()
                ], 500);
            }
        }
        public function get_student_list(Request $request){
            //  dd($request->all());
            // $students_credithr = $request->students_credithr;
            $sqlSessionCode = "select * from academic_sessions where sess_id = $request->session_id";
            $ressqlSessionCode = DB::select($sqlSessionCode);
            $sess_name = explode(' ', $ressqlSessionCode[0]->sess_name)[0];
            $data = DB::table('STUDENT_SECURITY_DEPOSITS as ssd')
                    ->select(
                            'ssd.SSD_ID',
                            'ssd.SSD_STUDENT_ID',
                            'ssd.SSD_ENROLLMENT_NO',
                            'ssd.SSD_STUDENT_NAME',
                            'ssd.SSD_SECURITY_AMOUNT',
                            'ssd.SSD_DESC',
                            'ssd.CHALLAN_ID',

                            'sai.SAI_ID',
                            'sai.sai_enrollment_no',
                            'spi.SPI_NAME',
                            // 'acd_enroll_session.SESS_NAME as enrollment_session',
                            'acd_roadmap_session.SESS_NAME as roadmap_session',
                            'school.SCHOOL_NAME',
                            'dept.DEPT_NAME',
                            'prog.PROG_NAME',
                            'aai.AAI_ID',


                            // DB::raw('(SELECT COUNT(*) FROM student_challan sc WHERE sc.SC_ENROLLMENT_NO = sai.SAI_ENROLLMENT_NO) as challan_count') // Raw query to count records
                            )
                            // ->leftjoin('ADMISSION_APPLY_INFO as aai', function ($join) {
                            //     $join->on('aai.AAI_API_ID', '=', 'api.API_ID')
                            //             ->whereColumn('aai.CMS_SAI_ENROLLMENT_NO', 'sai.SAI_ENROLLMENT_NO');
                            // })
                            ->join('ADMISSION_APPLY_INFO as aai', 'aai.AAI_ID','ssd.SSD_STUDENT_ID')
                            ->leftjoin('ADMISSION_PERSONAL_INFO as api', 'api.API_ID', '=', 'aai.AAI_API_ID')
                            ->leftjoin('STUDENTS_APPLY_INFO as sai', 'sai.SAI_API_ID','api.API_ID')
                            ->leftjoin('STUDENTS_PERSONAL_INFO as spi', 'spi.SPI_ID', 'sai.SAI_SPI_ID')



                            // ->leftjoin('ACADEMIC_SESSIONS as acd_enroll_session', 'acd_enroll_session.SESS_ID', 'fscd.FSCD_STD_ENROLLMENT_SESSION')
                            ->leftjoin('ACADEMIC_SESSIONS as acd_roadmap_session', 'acd_roadmap_session.SESS_ID', 'sai.SAI_SDPO_SESS_ID')
                            ->leftjoin('SCHOOLS as school', 'school.SCHOOL_ID', 'sai.SAI_SDPO_SCHOOL_ID')
                            ->leftjoin('DEPARTMENTS as dept', 'dept.DEPT_ID', 'sai.SAI_SDPO_DEPT_ID')
                            ->leftjoin('PROGRAMS as prog', 'prog.PROG_ID', 'sai.SAI_SDPO_PROG_ID');
                            if(!empty($request->roadmap)){
                                $data = $data->where('sai.SAI_SDPO_SESS_ID', $request->roadmap);
                                // dd($request->studentType,$sess_name );
                                    // if(!empty($request->studentType)){
                                    //     if($request->studentType == 'Existing' && $sess_name == 'Spring'){//show students of all sessions before the selected session But skiping the last FALL session
                                    //         //   dd(1);
                                    //         $data = $data->where('sai.SAI_SDPO_SESS_ID','<', $request->session_id-1);

                                    //     }elseif($request->studentType == 'New Admission FALL' && $sess_name == 'Spring'){//show students of session before the selected session
                                    //         // dd(2);
                                    //         $data = $data->where('sai.SAI_SDPO_SESS_ID','=', $request->session_id-1);

                                    //     }elseif($request->studentType == 'New Admission SPRING' && $sess_name == 'Spring'){//show students of session selected only
                                    //         // dd(3);
                                    //         $data = $data->where('sai.SAI_SDPO_SESS_ID','=', $request->session_id);

                                    //     }elseif($request->studentType == 'Existing' && $sess_name == 'Summer'){//show students of all sessions before the selected session
                                    //         // dd(4);
                                    //         $data = $data->where('sai.SAI_SDPO_SESS_ID','<', $request->session_id);

                                    //     }elseif($request->studentType == 'New Admission SUMMER' && $sess_name == 'Summer'){//show students of session selected only
                                    //         // dd(5);
                                    //         $data = $data->where('sai.SAI_SDPO_SESS_ID','=', $request->session_id);

                                    //     }elseif($request->studentType == 'Existing' && $sess_name == 'Fall'){//show students of all sessions before the selected session
                                    //         // dd(6);
                                    //         $data = $data->where('sai.SAI_SDPO_SESS_ID','<', $request->session_id);

                                    //     }elseif($request->studentType == 'New Admission FALL' && $sess_name == 'Fall'){//show students of session selected only
                                    //         // dd(7);
                                    //         $data = $data->where('sai.SAI_SDPO_SESS_ID','=', $request->session_id);

                                    //     }else{//Null case
                                    //         // dd(8);
                                    //         $data = $data->whereNull('sai.SAI_SDPO_SESS_ID');
                                    //     }
                                    // }

                                }
                            /////
                            if(!empty($request->school_id && !is_array(json_decode($request->school_id)))){
                                $data = $data->where('sai.SAI_SDPO_SCHOOL_ID', $request->school_id);

                            }
                            if(!empty($request->department_id && !is_array(json_decode($request->department_id)))){
                                $data = $data->where('sai.SAI_SDPO_DEPT_ID', $request->department_id);
                            }
                            if(!empty($request->degree_level)){
                                $data = $data->where('prog.PROG_DEGREE_LEVEL', $request->degree_level);
                            }
                            if(!empty($request->studentStatus)){
                                $data = $data->where('sai.SAI_STUDENT_STATUS', $request->studentStatus);
                            }
                $updatedStudents = $data = $data->orderby('ssd.SSD_ID')->get();
                // $credits = collect($student_crdithr_data);
                // // dd($data);
                // // Map over students collection and add total_credits
                // $updatedStudents = $data->map(function ($student) use ($credits) {
                //     // Find matching credit record
                //     $credit = $credits->firstWhere('sc_sai_enrollment_no', $student->sai_enrollment_no);

                //     // Add total_credits if found, otherwise set to null
                //     $student->total_credits = $credit->total_credits ?? '0';
                //     $student->credit_flag = $credit->credit_flag ?? 'N/A';

                //     return $student;
                // });
                // dd($updatedStudents);
            return DataTables::of($updatedStudents)->toJson();
        }
        public function addConvocationChallan(Request $request){
            // dd($request->all());
            $validator = Validator::make($request->all(),[
                'session' => 'required',
                'challan_amount' => 'required',
                'remarks' => 'required',
                'action_type' => 'required',
                'student_status' => 'required',
                'due_date' => 'required',
                'valid_date' => 'required',
                'visible_date' => 'required',
                // 'degree_level'   => 'required',
            ]);
            if($validator->fails()) {
                return Response::json(array(
                    'success' => false,
                    'errors' => $validator->getMessageBag()->toArray()
                ), 400);
            }
            $dueDate = new DateTime($request->due_date);
            $dueDate = $dueDate->format('Y-m-d');

            $validDate = new DateTime($request->valid_date);
            $validDate = $validDate->format('Y-m-d');

            $visibleDate = new DateTime($request->visible_date);
            $visibleDate = $visibleDate->format('Y-m-d');
            $generatdChallan_count=0;
            // $fscd_ids = $request->fscd_ids;


            try {
                // if($request->student_type != 'New without Registration FALL' && $request->student_type != 'New without Registration SPRING' && $request->student_type != 'New without Registration SUMMER'){//only for exisitng  and new admission with registration
                    $studentType  = $request->student_type;
                    $studentIdArr = explode(',',$request->students);
                    $main_ids = explode(',', $request->main_ids);   // convert to array
                    // dd($main_ids, $studentIdArr);
                    $chunks = array_chunk($main_ids, 1000);         // split into 1000-sized array
                    $whereClauses = [];
                    $params = [];
                    // dd($chunks);
                    foreach ($chunks as $chunk) {
                        $placeholders = implode(',', array_fill(0, count($chunk), '?'));
                        $whereClauses[] = "SSD_ID IN ($placeholders)";
                        $params = array_merge($params, $chunk);
                    }
                    $sql = "
                        SELECT *
                        FROM STUDENT_SECURITY_DEPOSITS 
                        WHERE (" . implode(' OR ', $whereClauses) . ")
                    ";
                    $data = DB::select($sql, $params);
                    
                    foreach($data as $val){
                        //dd($val);
                        $result_notification='';
                        // dd($concatenated);
                        $result_notification = $this->procedure_convocation_chln_gen($val,$request, $dueDate, $validDate, $visibleDate);

                        if($result_notification){
                            $generatdChallan_count++;
                        }
                    }
                    return response()->json(['success' => true, 'count'=> $generatdChallan_count]);
                // }else{
                //     return response()->json(['success' => false, 'messege'=> 'Something went wrong. (credit hour wise challan can not be generated for students without registration)']);
                // }
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'messege'=> 'Something went wrong.', 'errors' => $e->getMessage()], 500);
            }

        }
        public function procedure_convocation_chln_gen($data, $request_data,  $dueDate, $validDate, $visibleDate){
            // dd('procedure_credit_hr_wise',$data, $request_data->all());
             $procedurenotification = 'fin_convo_chln_gen';
                    $bindings_notification = [
                    'p_session' => $request_data->session,
                    'p_AAI_ID' => $data->ssd_student_id,
                    'p_amnt' => $request_data->challan_amount,
                    'p_remarks' => $request_data->remarks,
                    'p_type' => $request_data->action_type,
                    'p_due_date' => $dueDate,
                    'p_valid_date' => $validDate,
                    'P_USER_NAME' => auth()->user()->username
                ];

                // dd($bindings_notification);
            return $result_notification = DB::executeProcedure($procedurenotification, $bindings_notification);
        }
        public function get_challan_list(Request $request){
            // dd($request->all());
            $data = DB::table('student_challan as sc')
                ->select(
                        'sc.ID',
                        'sc.SC_ENROLLMENT_NO',
                        'sc.SC_APP_ID',
                        'SC_STUDENTNAME',
                        'acd.SESS_NAME',
                        'school.SCHOOL_NAME',
                        'dept.DEPT_NAME',
                        'prog.PROG_NAME',
                        'sc.SC_TUITION_FEE_AMNT',
                        'sc.FFS_ADMISSION_AMT',
                        'sc.FFS_SECURITY_AMT',
                        'sc.FFS_MEDICAL_AMT',
                        'sc.FFS_CREDHR_AMT',
                        'sc.SC_ACTIVITY_AMT',
                        'sc.SC_CHALLAN_STATUS',
                        'sc.SC_REPEAT_AMOUNT_5',
                        'sc.SC_ENROLLED_CR_HR',
                        DB::raw('TO_CHAR(sc.SC_ISSU_DATE, \'DD-MM-YYYY\') as SC_ISSU_DATE'),
                        DB::raw('TO_CHAR(sc.SC_DUE_DATE, \'DD-MM-YYYY\') as SC_DUE_DATE'),
                        DB::raw('TO_CHAR(sc.SC_VALID_DATE, \'DD-MM-YYYY\') as SC_VALID_DATE'),
                        'sc.INVOICEAMOUNT',

                        'sc.SC_SESSION_ID',
                        'sc.SC_SCHOOL_ID',
                        'sc.SC_DEPARTMENT_ID',
                        'sc.SC_PROGRAM_ID'
                        )
                        ->join('ACADEMIC_SESSIONS as acd', 'acd.SESS_ID', 'sc.SC_SESSION_ID')
                        ->join('SCHOOLS as school', 'school.SCHOOL_ID', 'sc.SC_SCHOOL_ID')
                        ->join('DEPARTMENTS as dept', 'dept.DEPT_ID', 'sc.SC_DEPARTMENT_ID')
                        ->join('PROGRAMS as prog', 'prog.PROG_ID', 'sc.SC_PROGRAM_ID')
                        ->where('sc.INVOICE_STATUS', 'E')
                        ->where('sc.SC_CHALLAN_TYPE', 'Convocation_Fee');
                        if($request->unpaidCheckbox == 1){//unpaid checkbox is CHECKED apply only session filter, challanStatus=UNPAID and challantype filter and IGNORE other fields
                            if(!empty($request->session_id)){
                                $data =  $data->where('sc.SC_SESSION_ID', $request->session_id)
                                                ->where('sc.SC_CHALLAN_STATUS', 'UNPAID');
                            }
                        }else{//unpaid checkbox is NOT CHECKED apply all filters as selected
                            if(!empty($request->session_id)){
                                $data =  $data->where('sc.SC_SESSION_ID', $request->session_id);
                            }
                            if(!empty($request->school_id) && !is_array(json_decode($request->school_id))){//condition will applied only when single school is selected if all school is slected then no need to apply condition//array means all school is selected and so we apply not array condtion to apply where clause;
                            $data =  $data->where('sc.SC_SCHOOL_ID', $request->school_id);
                            }
                            if(!empty($request->department_id) && !is_array(json_decode($request->department_id))){//condition will applied only when single department is selected if all department is slected then no need to apply condition//array means all department is selected and so we apply not array condtion to apply where clause;
                                $data =  $data->where('sc.SC_DEPARTMENT_ID', $request->department_id);
                            }
                            if(!empty($request->program_id) && !is_array(json_decode($request->program_id))){//condition will applied only when single program is selected if all program is slected then no need to apply condition//array means all program is selected and so we apply not array condtion to apply where clause;
                            //    dd('program condtion applied');
                                $programs_id = $request->program_id;
                                // dd($programs_id);
                                $array_of_sch_dep_prog = explode('~~', $programs_id);
                                // dd($array_of_sch_dep_prog[2]);
                                $data =  $data->where('sc.SC_PROGRAM_ID', $array_of_sch_dep_prog[2]);
                            }
                            if(!empty($request->degree_level)){
                                $data =  $data->where('prog.PROG_DEGREE_LEVEL', $request->degree_level);
                            }
                            // if(!empty($request->studentType)){
                            //     if(strpos($request->studentType, 'New without Registration') !== false){//if 'New without Registration' exists in student type
                            //         $string = $request->studentType;
                            //         $search = 'New without Registration';
                            //         $replace = 'New Admission';
                            //         $studentType = str_replace($search, $replace, $string);
                            //         // dd('New without Registration', $studentType);
                            //         $data =  $data->where('sc.SC_STUDENT_STATUS', $studentType)->whereNull('sc.SC_ENROLLMENT_NO');
                            //     }else{
                            //         // dd('New with Registration', $request->studentType);
                            //         $data =  $data->where('sc.SC_STUDENT_STATUS', $request->studentType)->whereNotNull('sc.SC_ENROLLMENT_NO');
                            //     }
                            // }
                            // if(!empty($request->feeType)){
                            //     if($request->feeType == 'Credit_hours'){
                            //         $data =  $data->whereIn('sc.SC_CHALLAN_TYPE', ['Credit_hours', 'Full_semester']);
                            //     }
                            //     if($request->feeType == 'Credit_hours_Summer_Course'){
                            //         $data =  $data->where('sc.SC_CHALLAN_TYPE', 'Short/Summer_Course');
                            //     }

                            // }
                        }

                        $data =  $data->get();
                        // dd($data);
            return DataTables::of($data)->toJson();

        }
        function print_challan(Request $request){
                // dd($request->all());
                $challan_no = $request->challanNo;
                // Get Personal Info and fees other then courses
                //$data_qry='select * from MV_feechallanwisesummary where BuyerCodeChallanNo='.$challan_no;
                $data_qry='select
                scholarship_type.scholarship_name,
                schools.school_name,
                dept_name,
                programs.prog_name,
                sess_name,
                student_challan.*,
                fin_scholarship_discounts.*,
                sc_surplus_amount,
                sc_activity_amt,
                sc_studentname,
                sc_fathername,

                (select FBC_VISIBLE_DATE from FIN_BLK_CHALLAN_GEN where fbc_school_id = sc_school_id and fbc_department_id = sc_department_id and fbc_program_id = sc_program_id and rownum =1) as visible_date
                from student_challan
                LEFT join fin_scholarship_discounts on  sc_app_id=fin_scholarship_discounts.fsd_aai_id
                LEFT JOIN scholarship_type ON fin_scholarship_discounts.fsd_scholarship_type=scholarship_type.scholarship_id
                LEFT JOIN fin_fee_adjustment ON fin_fee_adjustment.ffa_enrollment_no = student_challan.sc_enrollment_no
                left join schools on school_id = sc_school_id
                left join departments on dept_id = sc_department_id
                left join programs on prog_id = sc_program_id
                left join academic_sessions on sess_id = sc_session_id
                where student_challan.id='.$challan_no;
            // echo $data_qry;exit;
                $data = DB::select($data_qry);
                // Get all courses with fee --fin_scholarship_discounts.fsd_enrollment_no=student_challan.sc_enrollment_no
                //$data_det_qry='select * from feechallanwisesummary_det@acc where "BuyerCodeChallanNo"='.$challan_no.' order by "srno" asc';
                $data_det_qry='select * from student_challan
                LEFT join fin_scholarship_discounts on sc_app_id=fin_scholarship_discounts.fsd_aai_id
                and fin_scholarship_discounts.fsd_sesion_id = student_challan.sc_session_id
                LEFT JOIN scholarship_type ON fin_scholarship_discounts.fsd_scholarship_type=scholarship_type.scholarship_id
                where id='.$challan_no.' order by id asc';
                $data_detail = DB::select($data_det_qry);
            // echo $data_qry;exit;
                //    dd($data, $data_detail);
                return view('grader.financial.print_challan_preview',array('obj' => $data,'details' => $data_detail));

        }
        ////////////
        public function procedure_repeat_credit_hr($fscd_data, $request_data, $dueDate, $validDate, $visibleDate){
            // dd('procedure_repeat_credit_hr',$fscd_data, $request_data->all());
             $procedurenotification = 'FIN_PRO_REPEAT_CREDIT';
                $bindings_notification = [
                    'p_enrollment_no' =>  $fscd_data->fscd_regno,
                    'p_session_id' =>  $request_data->session,
                    'p_std_status' => $fscd_data->fscd_std_status, //(Active or Expected-Graduate)
                    'p_ffs_student_type' => $request_data->student_type, //(Existing/New Admission FALL etc..)
                    'p_due_date' => $dueDate,
                    'p_valid_date' => $validDate,
                    'p_visible_date' => $visibleDate,
                    'p_Installment_allowed' => 'NO',
                    'p_degree_level' => $fscd_data->fscd_std_degree_lvl,
                    'p_repeat_credit' => $fscd_data->fscd_std_repeat_credits,
                    'p_user_name' => auth()->user()->username
                ];
                // dd($bindings_notification);
            return $result_notification = DB::executeProcedure($procedurenotification, $bindings_notification);
        }
        public function cancel_challan($fscd_data, $request_data){
            // dd($fscd_data);
            $exisitng_challan = DB::table('student_challan')
                    ->select('challan_no')
                    ->where('SC_APP_ID', $fscd_data->fscd_app_id)
                    ->where('sc_session_id', $request_data->session)
                    ->whereIn('SC_CHALLAN_TYPE', ['Full_semester', 'Short/Summer_Course'])
                    ->where('invoice_status', 'E')
                    ->get();
            // dd($exisitng_challan);
            if(!empty($exisitng_challan)){
                foreach($exisitng_challan as $val){
                    $procedureName = 'fin_bulk_challan_cancellation';
                    $bindings = [
                            'P_SC_CHALLAN_ID' => $val->challan_no,
                            'P_ENROLLMENT_NO' => $fscd_data->fscd_regno,
                            'P_SC_APP_D' => $fscd_data->fscd_app_id,
                            'P_CANCELLED_USER' => auth()->user()->username

                        ];
                    // dd($bindings);
                    $result = DB::executeProcedure($procedureName, $bindings);
                }

            }
        }
        public function adjust_surplus($fscd_data, $request_data){
            $exisitng_surplus = DB::table('FIN_FEE_ADJUSTMENT')
                            ->select('FFA_SURPLUS_AMOUNT')
                            ->where('FFA_APP_ID', $fscd_data->fscd_app_id)
                            ->first();
            // dd($exisitng_surplus->ffa_surplus_amount > 0);
            if(!empty($exisitng_surplus)){
                if($exisitng_surplus->ffa_surplus_amount > 0){
                    $surplus_procedurenotification = 'ADJUST_FEE_BY_ENROLLMENT';
                    $surplus_bindings_notification = [
                        'p_enrollment_no' => $fscd_data->fscd_regno,//
                        'p_sess_id' => $request_data->session
                    ];
                    // dd($surplus_bindings_notification);
                    // Execute stored procedure
                    $surplus_result_notification = DB::executeProcedure($surplus_procedurenotification, $surplus_bindings_notification);
                }
            }
        }
        public function add_summerCredithr_challans($request){
            // dd($request->all());
            $validator = Validator::make($request->all(),[
                'session' => 'required',
                'student_type' => 'required',
                'student_status' => 'required',
                'due_date' => 'required',
                'valid_date' => 'required',
                'visible_date' => 'required',
            ]);
            if($validator->fails()) {
                return Response::json(array(
                    'success' => false,
                    'errors' => $validator->getMessageBag()->toArray()
                ), 400);
            }
            $dueDate = new DateTime($request->due_date);
            $dueDate = $dueDate->format('Y-m-d');

            $validDate = new DateTime($request->valid_date);
            $validDate = $validDate->format('Y-m-d');

            $visibleDate = new DateTime($request->visible_date);
            $visibleDate = $visibleDate->format('Y-m-d');
            $generatdChallan_count=0;
            // try {
                if($request->student_type == 'Existing'){//only for exisitng
                    $studentType  = $request->student_type;
                    $studentIdArr = explode(',',$request->students);
                    $studentData = DB::table('STUDENTS_APPLY_INFO')
                                    ->select(
                                            'SAI_ID',
                                            'SAI_ID',
                                            'SAI_ENROLLMENT_NO',
                                            // 'SAI_SDPO_SESS_ID',
                                            'SAI_SDPO_SCHOOL_ID',
                                            'SAI_SDPO_DEPT_ID',
                                            'SAI_SDPO_PROG_ID',
                                            // 'SAI_STUDENT_STATUS'
                                    )
                                    ->whereIn('SAI_ID', $studentIdArr)
                                    ->get();
                    foreach($studentData as $val){
                        $enrollement = $val->sai_enrollment_no;
                        $procedurenotification = 'FIN_PRO_SUMMER_CREDIT_HOUR';
                        $bindings_notification = [
                            'p_enrollment_no' =>  $enrollement,
                            'p_session_id' => $request->session,
                            'p_std_status' => $request->student_status,
                            'p_ffs_student_type' => $studentType,
                            'p_due_date' => $dueDate,
                            'p_valid_date' =>  $validDate,
                            'p_visible_date' => $visibleDate,
                            'p_Installment_allowed' => 'NO',
                            'p_degree_level' => $request->degree_level,
                            'p_user_name' => auth()->user()->username
                        ];
                        // dd($bindings_notification);
                        // Execute stored procedure
                        $result_notification = DB::executeProcedure($procedurenotification, $bindings_notification);
                        if($result_notification){
                            $generatdChallan_count++;
                        }
                    }
                    return response()->json(['success' => true, 'count'=> $generatdChallan_count]);
                }else{
                    return response()->json(['success' => false, 'messege'=> 'Something went wrong. (Student type should be existing, credit hour for summer can not be generated for new students)']);
                }
            // } catch (\Exception $e) {
            //     return response()->json(['success' => false, 'messege'=> 'Something went wrong.', 'errors' => $e->getMessage()], 500);
            // }

        }
      


        public function getStudents_summerCredithr($request){
            // dd($request->all());
            $sql=" SELECT  distinct sc_sai_enrollment_no
                        FROM student_courses
                        left join STUDENTS_APPLY_INFO on sc_sai_enrollment_no=SAI_ENROLLMENT_NO
                        WHERE  SC_ENROLLMENT_STATUS = 1
                        AND SC_STATUS in('A', 'Withdrawal', 'Block')
                        AND SC_CO_SESS_ID =  $request->session_id
                        AND sc_sai_enrollment_no not like '%SUM%'
                        and sc_course_id not in (1533,6123,6825,6824,9542)
                        and STUDENTS_APPLY_INFO.SAI_STUDENT_STATUS in ('Active','Expected-Graduate')
                    ";
            $student_crdithr_data = DB::select($sql);
            // dd($student_crdithr_data);
            $enrollmentNumbers = collect($student_crdithr_data)->pluck('sc_sai_enrollment_no')->toArray();
            $enrollmentString = "('".implode("','",$enrollmentNumbers)."')";//formatting to use in WHere IN ()
            // dd($enrollmentString);
            //////Filter the enrollment nos where the fee structures is defined and the challan not issues
            $feeStrcture_enrollment_sql = "WITH student_info AS (
                                                SELECT DISTINCT sai.sai_enrollment_no,
                                                    sai.sai_sdpo_school_id,
                                                    sai.sai_sdpo_dept_id,
                                                    sai.sai_sdpo_prog_id
                                                FROM students_apply_info sai
                                                WHERE sai.sai_enrollment_no in $enrollmentString
                                                ),
                                            fee_structure AS (
                                                SELECT DISTINCT ffs.FFS_SCHOOL_ID,
                                                                ffs.FFS_DEPT_ID,
                                                                ffs.FFS_PROG_ID
                                                FROM FIN_FEE_STRUCTURE ffs
                                                JOIN student_info si
                                                    ON si.sai_sdpo_school_id = ffs.FFS_SCHOOL_ID
                                                    AND si.sai_sdpo_dept_id = ffs.FFS_DEPT_ID
                                                    AND si.sai_sdpo_prog_id = ffs.FFS_PROG_ID
                                                    AND ffs.FFS_FEE_TYPE ='Short/Summer_Course'
                                                    AND ffs.ffs_credhr_amt > 0
                                                WHERE ffs.FFS_SESS_ID = $request->session_id
                                            ),
                                            challan_info AS (
                                                SELECT DISTINCT sc.SC_ENROLLMENT_NO
                                                FROM student_challan sc
                                                JOIN student_info si
                                                    ON sc.SC_ENROLLMENT_NO = si.sai_enrollment_no
                                                WHERE sc.SC_ENROLLED_CR_HR IS NOT NULL
                                            )
                                            SELECT *
                                            FROM (
                                                SELECT si.sai_enrollment_no,
                                                    CASE
                                                        WHEN fs.FFS_PROG_ID IS NOT NULL AND ci.SC_ENROLLMENT_NO IS NULL
                                                        THEN 'Fee Structure Defined, Challan Not Issued'
                                                        ELSE 'Fee Structure Not Defined'
                                                    END AS status
                                                FROM student_info si
                                                LEFT JOIN fee_structure fs
                                                    ON si.sai_sdpo_school_id = fs.FFS_SCHOOL_ID
                                                    AND si.sai_sdpo_dept_id = fs.FFS_DEPT_ID
                                                    AND si.sai_sdpo_prog_id = fs.FFS_PROG_ID
                                                LEFT JOIN challan_info ci
                                                    ON si.sai_enrollment_no = ci.SC_ENROLLMENT_NO
                                            )
                                            WHERE status = 'Fee Structure Defined, Challan Not Issued'";
                $feeStrcture_enrollment = DB::select($feeStrcture_enrollment_sql);//extracting enrollmentno whose fee structures are defined
            //  dd($feeStrcture_enrollment);
                $enrollmentNumbers = collect($feeStrcture_enrollment)->pluck('sai_enrollment_no')->toArray();
            //  dd($enrollmentNumbers);
            $data = DB::table('STUDENTS_APPLY_INFO as sai')
                    ->select(
                            'sai.SAI_ID',
                            'aai.aai_id',

                            'sai.SAI_STUDENT_STATUS',
                            'sai.SAI_ENROLLMENT_NO',
                            // 'sai.SAI_PROGRAMID',
                            // 'sai.SAI_DEGREECODE',
                            'spi.SPI_NAME',
                            'acd.SESS_NAME',
                            'school.SCHOOL_NAME',
                            'dept.DEPT_NAME',
                            'prog.PROG_NAME',
                            // DB::raw('(SELECT COUNT(*) FROM student_challan sc WHERE sc.SC_ENROLLMENT_NO = sai.SAI_ENROLLMENT_NO) as challan_count') // Raw query to count records
                            )
                            //->leftjoin('student_challan as sc', 'sc.SC_ENROLLMENT_NO', 'sai.SAI_ENROLLMENT_NO')
                            ->leftjoin('STUDENTS_PERSONAL_INFO as spi', 'spi.SPI_ID', 'sai.SAI_SPI_ID')
                            ->leftjoin('ADMISSION_PERSONAL_INFO as api', 'api.API_ID', '=', 'spi.API_ID')
                            ->leftjoin('ADMISSION_APPLY_INFO as aai', function ($join) {
                                $join->on('aai.AAI_API_ID', '=', 'api.API_ID')
                                        ->whereColumn('aai.CMS_SAI_ENROLLMENT_NO', 'sai.SAI_ENROLLMENT_NO');
                            })
                            // ->leftjoin('ADMISSION_APPLY_INFO as aai', 'aai.AAI_API_ID', '=', 'api.API_ID')
                            ->leftjoin('ACADEMIC_SESSIONS as acd', 'acd.SESS_ID', 'sai.SAI_SDPO_SESS_ID')
                            ->leftjoin('SCHOOLS as school', 'school.SCHOOL_ID', 'sai.SAI_SDPO_SCHOOL_ID')
                            ->leftjoin('DEPARTMENTS as dept', 'dept.DEPT_ID', 'sai.SAI_SDPO_DEPT_ID')
                            ->leftjoin('PROGRAMS as prog', 'prog.PROG_ID', 'sai.SAI_SDPO_PROG_ID')
                            ->whereIn('sai.SAI_STUDENT_STATUS', ['Expected-Graduate', 'Active'])
                            ->whereIn('sai.SAI_ENROLLMENT_NO', $enrollmentNumbers);
                            // ->whereRaw("acd.SESS_NAME NOT LIKE ?", ['Summer%']);
                            // if(!empty($request->studentType)){
                            //     if($request->studentType == 'Existing'){//existing students case
                            //         $data = $data->where('sai.SAI_SDPO_SESS_ID','=', $request->session_id);
                            //     }else{
                            //          $data = $data->whereNull('sai.SAI_SDPO_SESS_ID');//show no data if other than exisitng selected
                            //     }
                            // }
                            if(!empty($request->school_id && !is_array(json_decode($request->school_id)))){
                                $data = $data->where('sai.SAI_SDPO_SCHOOL_ID', $request->school_id);

                            }
                            if(!empty($request->department_id && !is_array(json_decode($request->department_id)))){
                                $data = $data->where('sai.SAI_SDPO_DEPT_ID', $request->department_id);
                            }
                            if(!empty($request->studentStatus)){
                                $data = $data->where('sai.SAI_STUDENT_STATUS', $request->studentStatus);
                            }
                $data = $data->orderby('aai.aai_id')->get();
                $credits = collect($student_crdithr_data);
                // dd($data);
                // Map over students collection and add total_credits
                $updatedStudents = $data->map(function ($student) use ($credits) {
                    // Find matching credit record
                    $credit = $credits->firstWhere('sc_sai_enrollment_no', $student->sai_enrollment_no);

                    // Add total_credits if found, otherwise set to null
                    $student->total_credits = $credit->total_credits ?? '0';

                    return $student;
                });
                // dd($updatedStudents);
            return DataTables::of($updatedStudents)->toJson();
        }
        public function get_feeAdjusted_list(Request $request){
            $data = DB::table('FIN_FEE_ADJUSTMENT as ffa')
            ->select(
                    'ffa.FFA_ID',
                    'ffa.FFA_SC_CHALLAN_NO',
                    'ffa.FFA_ENROLLMENT_NO',
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
                    ->where('FFA_STATUS', 'E')
                    ->orderBy('ffa.FFA_CREATED_AT', 'desc')
                    ->get();
                // dd($data);
            return DataTables::of($data)->toJson();
        }
        
}
