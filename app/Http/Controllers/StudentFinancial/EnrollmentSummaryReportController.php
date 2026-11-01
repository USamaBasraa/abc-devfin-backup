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
use Illuminate\Support\Str;

class EnrollmentSummaryReportController extends Controller{

        public function enrollmentSummaryReport(){
            $sessions = \App::call('App\Http\Controllers\Grader\AcademicSessionController@retrieve');
            $schools =  Schools::all();
            $departments = array(); // $this->school->fetch();
            $facultyCourses = \App::call('App\Http\Controllers\Grader\FacultyCoursesController@retrieve');
            $offeredFaculty = \App::call('App\Http\Controllers\Grader\CoursesOfferedController@retrieveFaculty');
            $programs =  DB::select("select * from Programs where PROG_STATUS = 'E'");
            $query = 'select * from fin_fee_category';
            $feeCategories = DB::select($query);
            $Scholarship_Type = "select * from Scholarship_Type where scholarship_status = 'E' and SCHOLARSHIP_ID in (1,2)";
            $recScholarship_Type = DB::select($Scholarship_Type);
             $query = 'select * from semesters order by SEMESTER_ID';
            $semesters = DB::select($query);
            return view('StudentFinancial.enrollmentSummaryReport',compact('sessions','schools','departments','facultyCourses','offeredFaculty','feeCategories','programs', 'recScholarship_Type','semesters'));

        }

        public function get_student_list(Request $request){
            // dd($request->all());
            $sqlSessionCode = "select * from academic_sessions where sess_id = $request->session_id";
            $ressqlSessionCode = DB::select($sqlSessionCode);
            $sess_name = explode(' ', $ressqlSessionCode[0]->sess_name)[0];
           // initialize all condition snippets
            $session_condition        = '';
            $school_condition         = '';
            $department_condition     = '';
            $degree_level_condition   = '';
            $program_condition        = '';

            // 1) Session condition
            if (!empty($request->session_id)) {
                $session_id        = $request->session_id; // sanitize if needed
                $session_condition = " AND fscd.FSCD_STD_ENROLLMENT_SESSION = '{$session_id}'";
            }

            // 2) School condition (only when NOT an array / "all")
            if (!empty($request->school_id)) {
                $schoolDecoded = json_decode($request->school_id, true);

                // if it's NOT an array, treat as single school id
                if (!is_array($schoolDecoded)) {
                    $school_id       = $request->school_id;
                    $school_condition = " AND sai.SAI_SDPO_SCHOOL_ID = '{$school_id}'";
                }
            }

            // 3) Department condition (only when NOT an array / "all")
            if (!empty($request->department_id)) {
                $deptDecoded = json_decode($request->department_id, true);

                if (!is_array($deptDecoded)) {
                    $department_id       = $request->department_id;
                    $department_condition = " AND sai.SAI_SDPO_DEPT_ID = '{$department_id}'";
                }
            }

            // 4) Degree level condition
            if (!empty($request->degree_level)) {
                $degree_level        = $request->degree_level;
                $degree_level_condition = " AND prog.PROG_DEGREE_LEVEL = '{$degree_level}'";
            }

            // 5) Program condition (only when NOT an array / "all")
            if (!empty($request->program_id)) {
                $progDecoded = json_decode($request->program_id, true);

                // condition will apply only when single program is selected
                if (!is_array($progDecoded)) {
                    $programs_id           = $request->program_id;
                    $array_of_sch_dep_prog = explode('~~', $programs_id);

                    if (isset($array_of_sch_dep_prog[2]) && $array_of_sch_dep_prog[2] !== '') {
                        $prog_id          = $array_of_sch_dep_prog[2];
                        $program_condition = " AND prog.PROG_ID = '{$prog_id}'";
                    }
                }
            }

            $sql = "SELECT
                        spi.SPI_NAME,
                        sai.SAI_ENROLLMENT_NO,
                        fscd.FSCD_APP_ID,
                        fscd.FSCD_STD_ALLOWED_CREDITS,
                        fscd.FSCD_STD_ENROLLED_CREDITS,
                        fscd.FSCD_STD_EARNED_CREDITS,
                        fscd.FSCD_STD_CURRENT_SEMESTER,
                        fscd.FSCD_SUM_CHALLAN_AMOUNT,
                        fscd.FSCD_SUM_SCHOLARSHIP_PERCENT,
                        fscd.FSCD_SUM_CHALLAN_ARREARS
                        -- Scholarship info, concatenated (using name)
                        -- (
                        --     SELECT LISTAGG(st.SCHOLARSHIP_NAME || '~~' || ssd.SSD_PERCENTAGE || '%', ',')
                        --         WITHIN GROUP (ORDER BY st.SCHOLARSHIP_NAME)
                        --     FROM STUDENTS_SCHOLARSHIPS_DETAILS ssd
                        --     LEFT JOIN SCHOLARSHIP_TYPE st ON st.SCHOLARSHIP_ID = ssd.SSD_TYPE
                        --     WHERE ssd.SSD_APP_ID = fscd.FSCD_APP_ID AND SSD_SESS_ID =165
                        -- ) AS SCHOLARSHIPS
                    FROM FIN_STUDENTS_CREDITS_DATA fscd

                    JOIN STUDENTS_APPLY_INFO sai ON sai.SAI_ENROLLMENT_NO = fscd.FSCD_REGNO
                    LEFT JOIN STUDENTS_PERSONAL_INFO spi ON spi.SPI_ID = sai.SAI_SPI_ID
                    --LEFT JOIN STUDENT_CHALLAN sc ON sc.SC_APP_ID = fscd.FSCD_APP_ID
                        --AND sc.SC_CHALLAN_TYPE IN ('Full_semester','Short/Summer_Course','Credit_hours')
                    LEFT JOIN ACADEMIC_SESSIONS acd_enroll_session ON acd_enroll_session.SESS_ID = fscd.FSCD_STD_ENROLLMENT_SESSION
                    LEFT JOIN ACADEMIC_SESSIONS acd_roadmap_session ON acd_roadmap_session.SESS_ID = fscd.FSCD_STD_ROADMAP_SESSION
                    LEFT JOIN SCHOOLS school ON school.SCHOOL_ID = fscd.FSCD_STD_SCHOOL_ID
                    LEFT JOIN DEPARTMENTS dept ON dept.DEPT_ID = fscd.FSCD_STD_DEPT_ID
                    LEFT JOIN PROGRAMS prog ON prog.PROG_ID = fscd.FSCD_STD_PROG_ID

                    WHERE 1 = 1
                        $session_condition
                        $school_condition
                        $department_condition
                        $degree_level_condition
                        $program_condition
                    ORDER BY fscd.FSCD_APP_ID
                ";

                // dd($sql);
                $data = DB::select($sql);
                // dd($data);
            // $sql = "SELECT
            //             sai.SAI_ID,
            //             fscd.*,
            //             sc.*,
            //             ssd.*,
            //             spi.SPI_NAME,
            //             acd_enroll_session.SESS_NAME AS enrollment_session,
            //             acd_roadmap_session.SESS_NAME AS roadmap_session,
            //             school.SCHOOL_NAME,
            //             dept.DEPT_NAME,
            //             prog.PROG_NAME

            //         FROM FIN_STUDENTS_CREDITS_DATA fscd

            //         -- INNER JOIN
            //         JOIN STUDENTS_APPLY_INFO sai
            //             ON sai.SAI_ENROLLMENT_NO = fscd.FSCD_REGNO

            //         -- LEFT JOIN STUDENTS_PERSONAL_INFO
            //         LEFT JOIN STUDENTS_PERSONAL_INFO spi
            //             ON spi.SPI_ID = sai.SAI_SPI_ID

            //         -- LEFT JOIN STUDENT_CHALLAN with filtered challan types
            //         LEFT JOIN STUDENT_CHALLAN sc
            //             ON sc.SC_APP_ID = fscd.FSCD_APP_ID
            //         AND sc.SC_CHALLAN_TYPE IN (
            //                 'Full_semester',
            //                 'Short/Summer_Course',
            //                 'Credit_hours'
            //         )
            //         -- LEFT JOIN STUDENTS_SCHOLARSHIPS_DETAILS
            //         LEFT JOIN STUDENTS_SCHOLARSHIPS_DETAILS ssd
            //             ON ssd.SSD_APP_ID = fscd.FSCD_APP_ID

            //         LEFT JOIN SCHOLARSHIP_TYPE st
            //             ON st.SCHOLARSHIP_ID = ssd.SSD_TYPE

            //         -- LEFT JOIN SESSIONS
            //         LEFT JOIN ACADEMIC_SESSIONS acd_enroll_session
            //             ON acd_enroll_session.SESS_ID = fscd.FSCD_STD_ENROLLMENT_SESSION

            //         LEFT JOIN ACADEMIC_SESSIONS acd_roadmap_session
            //             ON acd_roadmap_session.SESS_ID = fscd.FSCD_STD_ROADMAP_SESSION

            //         -- LEFT JOIN SCHOOL, DEPT, PROG
            //         LEFT JOIN SCHOOLS school
            //             ON school.SCHOOL_ID = fscd.FSCD_STD_SCHOOL_ID

            //         LEFT JOIN DEPARTMENTS dept
            //             ON dept.DEPT_ID = fscd.FSCD_STD_DEPT_ID

            //         LEFT JOIN PROGRAMS prog
            //             ON prog.PROG_ID = fscd.FSCD_STD_PROG_ID
            //             WHERE 1 = 1
            //             $session_condition
            //             $school_condition
            //             $department_condition
            //             $degree_level_condition
            //             $program_condition
            //         ORDER BY fscd.FSCD_APP_ID
            //     ";

            //     // dd($sql);
            //     $data = DB::select($sql);
                // dd($data);
            return DataTables::of($data)->toJson();
        }
        ///
        //////
        ///////////
        public function addChallan(Request $request){
            // dd($request->all());
            if($request->fee_type == 'Credit_hours'){
                return $this->add_creditHr_challans($request);
            }
            if($request->fee_type == 'Credit_hours_Summer_Course'){
                return $this->add_summerCredithr_challans($request);
            }

        }
        public function add_creditHr_challans($request){
            // dd($request->all());
            $validator = Validator::make($request->all(),[
                'session' => 'required',
                'student_type' => 'required',
                'fee_type' => 'required',
                'student_status' => 'required',
                'due_date' => 'required',
                'valid_date' => 'required',
                'visible_date' => 'required',
                // 'degree_level'   => 'required|not_in:null,Postgraduate,Doctorate',
                'degree_level'   => 'required|in:Undergraduate,Postgraduate,Graduate',
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
            $degreeLevel = $request->degree_level;
            $generatdChallan_count=0;
            // $fscd_ids = $request->fscd_ids;


            try {
                if($request->student_type != 'New without Registration FALL' && $request->student_type != 'New without Registration SPRING' && $request->student_type != 'New without Registration SUMMER'){//only for exisitng  and new admission with registration
                    $studentType  = $request->student_type;
                    $studentIdArr = explode(',',$request->students);
                    $fscd_ids = explode(',', $request->fscd_ids);   // convert to array
                    $chunks = array_chunk($fscd_ids, 1000);         // split into 1000-sized array
                    $whereClauses = [];
                    $params = [];
                    // dd($chunks);
                    foreach ($chunks as $chunk) {
                        $placeholders = implode(',', array_fill(0, count($chunk), '?'));
                        $whereClauses[] = "FSCD_ID IN ($placeholders)";
                        $params = array_merge($params, $chunk);
                    }
                    $sql = "
                        SELECT *
                        FROM FIN_STUDENTS_CREDITS_DATA
                        WHERE (" . implode(' OR ', $whereClauses) . ")
                    ";
                    $fcsd_data = DB::select($sql, $params);
                    foreach($fcsd_data as $val){
                        //dd($val);
                        $enrollement = $val->fscd_regno;
                        $degree_level = $val->fscd_std_degree_lvl;
                        $enrolled_credits = $val->fscd_std_enrolled_credits;
                        $earned_credits = $val->fscd_std_earned_credits;
                        $enrolled_plus_earned_credits = $earned_credits + $enrolled_credits;

                        $repeat_credits = $val->fscd_std_repeat_credits;
                        $allowed_credits = $val->fscd_std_allowed_credits;
                        $max_policy_credits = $val->fscd_ffp_max_cr;

                        $current_semester = $val->fscd_std_current_semester;
                        $allowed_semester = $val->fscd_std_allowed_semester;
                        $procedure = $val->fscd_procedure_name;
                        $enrollment = $val->fscd_regno;
                        $fee_type = $val->fscd_ffs_fee_type;

                        $data = [
                            'enrollment' => $enrollment,
                            'fee_type' => $fee_type,
                            'degree_level' => $degree_level,
                            'current_semester' => 'current_semester '.$current_semester,
                            'allowed_semester' => 'allowed_semester '.$allowed_semester,
                            'enrolled_credt' => 'enrolled_credt '.$enrolled_credits,
                            'allowed_credits' => 'allowed_credits '.$allowed_credits,
                            'repeat_credits' => 'repeat_credits '.$repeat_credits
                        ];
                        $concatenated = implode('----', $data);
                        // dd($concatenated);
                        $result_notification='';
                        // dd($concatenated);
                        if($degree_level == 'Undergraduate'){
                            if($current_semester <= $allowed_semester){ //From 1st to 8th semester:

                                if($enrolled_credits <= 9 || $enrolled_credits > 18){//Credit Hrs upto 9 OR exceeding 18

                                    //cancle only for upto 9 credit
                                    if($enrolled_credits <= 9){
                                        $this->cancel_challan($val, $request);
                                    }
                                    $result_notification = $this->procedure_credit_hr_wise($val, $request, $dueDate, $validDate, $visibleDate);
                                    $this->adjust_surplus($val, $request);

                                }else if($enrolled_credits > 9 && $enrolled_credits <= 18 && $repeat_credits > 0){//Enrolled Credit Hrs between 9.5 to 18 and is repeate credithr

                                    // no need to cancel
                                    $result_notification = $this->procedure_repeat_credit_hr($val, $request, $dueDate, $validDate, $visibleDate);
                                    $this->adjust_surplus($val, $request);

                                }else{
                                    DB::table('Testing')->insert([
                                    'description' => $concatenated,
                                    'value_one'=> 'credit hour page Undergraduate within semester range'
                                    ]);
                                }
                            }else if($current_semester > $allowed_semester){//exceeding allowed semester
                                // dd(3);
                                if($enrolled_plus_earned_credits >= $allowed_credits){//equal to allowed credits

                                    $this->cancel_challan($val, $request);
                                    $result_notification = $this->procedure_credit_hr_wise($val, $request, $dueDate, $validDate, $visibleDate);
                                    $this->adjust_surplus($val, $request);

                                }else if($enrolled_credits <= 9 || $enrolled_credits > 18){//Credit Hrs upto 9 OR exceeding 18
                                    //cancle only for upto 9 credit

                                    if($enrolled_credits <= 9){
                                        $this->cancel_challan($val, $request);
                                    }
                                    $result_notification = $this->procedure_credit_hr_wise($val, $request, $dueDate, $validDate, $visibleDate);
                                    $this->adjust_surplus($val, $request);

                                }else if($enrolled_credits > 9 && $enrolled_credits <= 18 && $repeat_credits > 0){//Enrolled Credit Hrs between 9.5 to 18
                                    // dd(6);
                                    //no need to cancel
                                    $result_notification = $this->procedure_repeat_credit_hr($val, $request, $dueDate, $validDate, $visibleDate);
                                    $this->adjust_surplus($val, $request);

                                }else{
                                    DB::table('Testing')->insert([
                                    'description' => $concatenated,
                                    'value_one'=> 'credit hour page Undergraduate exceeding semester range'
                                    ]);
                                }
                            }
                        }
                        if($degree_level == 'Graduate' || $degree_level == 'Postgraduate'){
                            if($current_semester <= $allowed_semester && $repeat_credits > 0 ){ //From 1st to 4th semester:

                                //no need to cancel
                                $result_notification = $this->procedure_repeat_credit_hr($val, $request, $dueDate, $validDate, $visibleDate);
                                 $this->adjust_surplus($val, $request);

                            }else if($current_semester > $allowed_semester){//From 5 th Semester onwards

                                if($enrolled_plus_earned_credits >= $allowed_credits){//equal to allowed credits

                                    $this->cancel_challan($val, $request);
                                    $result_notification = $this->procedure_credit_hr_wise($val, $request, $dueDate, $validDate, $visibleDate);
                                    $this->adjust_surplus($val, $request);

                                }else if($enrolled_credits <= 9 || $enrolled_credits > 12){//Credit Hrs upto 9 OR exceeding 12
                                    //cancle only for upto 9 credit
                                    if($enrolled_credits <= 9){
                                        $this->cancel_challan($val, $request);
                                    }
                                    $result_notification = $this->procedure_credit_hr_wise($val, $request, $dueDate, $validDate, $visibleDate);
                                    $this->adjust_surplus($val, $request);

                                }else if($enrolled_credits > 9 && $enrolled_credits <= 12 && $repeat_credits > 0){//Enrolled Credit Hrs between 9.5 to 12

                                    //  no need to cancel
                                    $result_notification = $this->procedure_repeat_credit_hr($val, $request, $dueDate, $validDate, $visibleDate);
                                     $this->adjust_surplus($val, $request);
                                }else{
                                    DB::table('Testing')->insert([
                                    'description' => $concatenated,
                                    'value_one'=> 'credit hour page Graduate/Post graduate exceeding semester range'
                                    ]);
                                }
                            }else{
                                 DB::table('Testing')->insert([
                                    'description' => $concatenated,
                                    'value_one'=> 'credit hour page Graduate/Post graduate external'
                                    ]);
                                }
                        }

                        if($result_notification){
                            $generatdChallan_count++;
                        }
                    }
                    return response()->json(['success' => true, 'count'=> $generatdChallan_count]);
                }else{
                    return response()->json(['success' => false, 'messege'=> 'Something went wrong. (credit hour wise challan can not be generated for students without registration)']);
                }
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'messege'=> 'Something went wrong.', 'errors' => $e->getMessage()], 500);
            }

        }
        public function procedure_credit_hr_wise($fscd_data, $request_data, $dueDate, $validDate, $visibleDate){
            // dd('procedure_credit_hr_wise',$fscd_data, $request_data->all());
             $procedurenotification = 'fin_credit_hr_wise_challan';
                    $bindings_notification = [
                    'p_enrollment_no' => $fscd_data->fscd_regno,
                    'p_session_id' => $request_data->session,
                    'p_std_status' => $fscd_data->fscd_std_status,
                    'p_ffs_student_type' => $request_data->student_type,
                    'p_due_date' => $dueDate,
                    'p_valid_date' => $validDate,
                    'p_visible_date' => $visibleDate,
                    'p_Installment_allowed' => 'NO',
                    'P_CR_HR' => $fscd_data->fscd_std_enrolled_credits,//askable
                    'P_USER_NAME' => auth()->user()->username
                ];

                // dd($bindings_notification);
            return $result_notification = DB::executeProcedure($procedurenotification, $bindings_notification);
        }
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
                            'P_CANCELLED_USER' => auth()->user()->username,
                            'P_REMARKS' => 'Due to issuance of credit hour challan'

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
                        ->where('sc.SC_ENROLLED_CR_HR', '>', 0);
                        if($request->unpaidCheckbox == 1){//unpaid checkbox is CHECKED apply only session filter, challanStatus=UNPAID and challantype filter and IGNORE other fields
                            if(!empty($request->session_id)){
                                $data =  $data->where('sc.SC_SESSION_ID', $request->session_id)
                                                ->where('sc.SC_CHALLAN_TYPE', $request->feeType)
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
                            if(!empty($request->studentType)){
                                if(strpos($request->studentType, 'New without Registration') !== false){//if 'New without Registration' exists in student type
                                    $string = $request->studentType;
                                    $search = 'New without Registration';
                                    $replace = 'New Admission';
                                    $studentType = str_replace($search, $replace, $string);
                                    // dd('New without Registration', $studentType);
                                    $data =  $data->where('sc.SC_STUDENT_STATUS', $studentType)->whereNull('sc.SC_ENROLLMENT_NO');
                                }else{
                                    // dd('New with Registration', $request->studentType);
                                    $data =  $data->where('sc.SC_STUDENT_STATUS', $request->studentType)->whereNotNull('sc.SC_ENROLLMENT_NO');
                                }
                            }
                            if(!empty($request->feeType)){
                                if($request->feeType == 'Credit_hours'){
                                    $data =  $data->whereIn('sc.SC_CHALLAN_TYPE', ['Credit_hours', 'Full_semester']);
                                }
                                if($request->feeType == 'Credit_hours_Summer_Course'){
                                    $data =  $data->where('sc.SC_CHALLAN_TYPE', 'Short/Summer_Course');
                                }

                            }
                        }

                        $data =  $data->get();
                        // dd($data);
            return DataTables::of($data)->toJson();

        }

}
