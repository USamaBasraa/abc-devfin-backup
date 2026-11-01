<?php
namespace App\Http\Controllers\Grader\Alumni;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\sessions;
use App\Models\User;
use Illuminate\Support\Facades\View;
use App\Models\Grader\Chat as ChatModel;
use App\Http\Requests\ChatRequest;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Messaging\CloudMessage;
use App\Models\Grader\AcademicSessions;
use DB;
use Auth;


class AlumniController extends Controller{

    public function index(){
        $resSess = $this->getSessions();
        $resSchools = $this->getSchools();
        $resDeprtments = $this->getDepartments();

        return view("grader.alumni.index",compact('resSess','resSchools','resDeprtments'));
    }


    public function getSessions(){
        $sqlSess = " SELECT SESS_ID, SESS_CODE, SESS_NAME, SESS_AY_ID, AY_NAME, SESS_AY_NAME, SESS_DESC, SESS_ORDER, SESS_STATUS, SESS_ENTRY_USER_ID, SESS_ENTRY_DATETIME
        FROM academic_sessions
        LEFT JOIN academic_years ON SESS_AY_ID = AY_ID
        WHERE SESS_STATUS='E'";
        //echo $emp_full_name;
        if(isset($emp_full_name) and $emp_full_name!="admin")
        {
            $sqlSess.= " and SESS_ID IN ( select distinct SR_SESS_ID from staff_rights where sr_sis_id='$login_user_email')";

        }
        /*if(isset(request()->sel_sess_id) and request()->sel_sess_id!="")
        {
            $sqlSess.= " and SESS_ID =".request()->sel_sess_id;

        }*/




        if(isset(request()->ay_id) and request()->ay_id!=""){
        $ay_id_name   = explode("~", request()->ay_id);
        $ay_id   = $ay_id_name[0];
        $sqlSess.= "  AND AY_ID=".$ay_id." ";
        }
        if(isset(request()->acfr_ay_id) and request()->acfr_ay_id!=""){
        //$ay_id_name   = explode("~", request()->ay_id);
        //$ay_id   = $ay_id_name[0];
        $sqlSess.= "  AND AY_ID=".request()->acfr_ay_id." ";
        $sqlSess.= " AND sess_code NOT LIKE 'SUM%' ";
        }
        if(isset(request()->sess_admission_active) and request()->sess_admission_active!=""){
        $sqlSess.= "  AND sess_admission_active='".request()->sess_admission_active."' ";
        }
        $sqlSess.= " ORDER BY sess_order DESC";
        //echo $sqlSess;
        //echo '<br/>'.$_SESSIONemp_full_name.'<br/>';
        $resSess = DB::select($sqlSess);
        return $resSess;
    }
    public function getSchools(){

        $sqlSchools = "SELECT school_id, school_code, school_name
        FROM schools
        WHERE school_status = 'E'";

        if(isset($mysess->login_user_email) and $emp_full_name!="admin"){
            if(isset($request->showall) and $_REQUEST['showall']=="Y"){

            } else {
            $sqlSchools.= " and SCHOOL_ID IN ( select distinct SR_SCHOOL_ID from staff_rights where sr_sis_id='$mysess->login_user_email')";
            }
        }

        if(isset($request->sdpo_sess_id) and $_REQUEST['sdpo_sess_id']!=""){
            $sdpo_sess_id_name   = explode("~", $_REQUEST['sdpo_sess_id']);
            $sdpo_sess_id   = $sdpo_sess_id_name[0];
        }

        if(isset($request->checkhod) and $_REQUEST['checkhod']=="yes"){
        $sqlSelectHODRights = "select emp_dept_id, emp_school_id from emp_employees where emp_email='" . $mysess->login_user_email . "' and emp_type=5";
        $resSelectHODRights = $mydb->query_select($sqlSelectHODRights);
        $checkHODExist      = count($resSelectHODRights);
        // in case of vc
        if($checkHODExist>0 && $mysess->employee_id=="996"){

        $sqlSchools.= " and SCHOOL_ID=".$resSelectHODRights[0]['EMP_SCHOOL_ID'];
        }

        }
        $sqlSchools.= " ORDER BY school_order";
        //echo $sqlSchools;
        return $resSchools = DB::select($sqlSchools);
    }
    public function getDepartments(){
        $sql = "SELECT * FROM departments WHERE dept_school_id BETWEEN 1 AND 7";
        $res = DB::Select($sql);
        return $res;


    }




    public function getPrograms(Request $request){

        $sql= "SELECT PROG_ID,PROG_NAME  FROM Programs where prog_dept_id ='".$request->department."'";
    // $sql ="SELECT
    //     programs.prog_id,
    //     programs.prog_name,
    //     session_degree_program_offerin.sdpo_id

    //     FROM
    //         session_degree_program_offerin
    //         JOIN programs ON session_degree_program_offerin.sdpo_prog_id = programs.prog_id";
    //     if($request->session!='' || $request->school!='' || $request->department!=''){
    //         $sql .=" WHERE ";
    //     }
    //     if($request->school!=''){
    //         $sql .= " session_degree_program_offerin.sdpo_school_id = '$request->school'";
    //     }
    //     if($request->school!='' && $request->session!=''){
    //         $sql .= " AND ";


    //     }


    //     if($request->session!=''){
    //         $sql .= " session_degree_program_offerin.sdpo_sess_id = '$request->session'";
    //     }
    //     if($request->session!='' && $request->department!=''){
    //         $sql .= " AND ";
    //     }
    //     if( $request->department!=''){
    //         $sql .= " session_degree_program_offerin.sdpo_dept_id = '$request->department'";
    //     }
    //      echo $sql;
        // dd($request->input(),$request->department!='');
        $res = DB::select($sql);
        return response()->json($res);
    }
    public function getSelectDepartments(Request $request){

        $sql = "SELECT * FROM departments WHERE dept_school_id = '$request->dept_school_id'";
        $res = DB::Select($sql);
        return response()->json($res);


    }
    public function getAlumnis(Request $request){


        $sql ="SELECT * FROM students_apply_info LEFT JOIN students_personal_info ON students_apply_info.sai_spi_id = students_personal_info.spi_id  WHERE sai_student_status LIKE '%Convocated%'";



        if($request->school!='' ){
            $sql .= " AND ";
        }
        if($request->school!=''){
            $sql .= " students_apply_info.sai_sdpo_school_id = '$request->school'";
        }

        if($request->session!=''){
            $sql .= " AND ";
        }
        if($request->session!=''){
            $sql .= " students_apply_info.sai_sdpo_sess_id = '$request->session'";
        }

        if($request->department!=''){
            $sql .= " AND ";
        }
        if( $request->department!=''){
            $sql .= " students_apply_info.sai_sdpo_dept_id = '$request->department'";
        }
        if( $request->program!=''){
            $sql .= " AND ";
        }
        if( $request->program!=''){
            $sql .= " students_apply_info.sai_sdpo_prog_id = '$request->program'";
        }
        // echo $sql;exit;
        $sql .=" ORDER BY students_apply_info.sai_sdpo_prog_id DESC";
        $res = DB::select($sql);
        return response()->json($res);

    }


}
