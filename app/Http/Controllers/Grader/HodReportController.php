<?php
namespace App\Http\Controllers\Grader;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DB;



class HodReportController extends Controller{

    private $sortBy = 'id';
    private $sortDirection = 'ASC';
    private $facultyCourse;

 
    function loginWithID(){
        if(!(Auth::id())){
            Auth::loginUsingId(766);
        }
        

        if(Auth::id()){
            echo "<pre>";
            // print_r(auth()->user());
            echo "</pre>";
        }else{
            echo "User Not Found!";
        }

        echo "End!"; exit;

    }


    // function checkUser(){
    //    // print_r(getLoginUser()->email);
    // }

    function easyui(){
        return view('grader.reports.easyui');
    }

    function sessions(){
        return DB::table('ACADEMIC_SESSIONS')->orderBy('sess_id', 'desc')->get();
    }

    
    function roadmapsessions(){
        return DB::table('ROADMAP_SESSIONS')->orderBy('sess_id', 'desc')->get();
    }

   
    function masterCourses() {
        return DB::table('courses_two')
                ->where('course_status', 'E')
                ->get();
    }

    function courseType() {
        return DB::table('course_types')
                ->select('ct_id', 'ct_code', 'ct_name', 'ct_status', 'ct_level', 'ct_order')
                ->get();
    }



    function shift(){
        return DB::table('shifts')->orderBy('shift_id', 'asc')->get();
    }

    function get_top_sessions(){
        $query = "
        SELECT COUNT(*) as allocated_courses,
          fc_co_sess_id,sess_name
        FROM faculty_courses
        left join academic_sessions on fc_co_sess_id = sess_id
        WHERE fc_employee_id = 350
        and fc_status = 'E'
        GROUP BY fc_co_sess_id,sess_name
        order by fc_co_sess_id desc";



        $query2 = "SELECT
        sess_id,
        sess_code,
        sess_name
    FROM
        academic_sessions
    WHERE
        sess_status = 'E'
    ORDER BY sess_id DESC FETCH NEXT 6 ROWS ONLY";

        return $result =DB::select($query);
    }

    function schools(Request $request){
        $SchId=auth()->user()->school_id;
        return DB::table('SCHOOLS')->orderBy('school_id', 'asc')->get();
        // return DB::table('SCHOOLS')->where('school_id','=',$SchId)->orderBy('school_id', 'asc')->get();
    }

     function adm_schedules(Request $request){
        $school_id = $request->get('school_id');
        $sess_id = $request->get('sess_id');
        $prog_id = $request->get('prog_id');
        $sqlAdmissionSch = "select admission_sch_round from admission_schedules where 1=1 and admission_sch_status='E'";
									
		
        if(isset($sess_id) and ($sess_id !='' )){				
            $sqlAdmissionSch .= " and admission_sch_sess_id='$sess_id'";
        }
        if(isset($school_id) and ($school_id !='')){
        $sqlAdmissionSch .=" and admission_sch_school_id='$school_id'";
        }
        if(isset($prog_id) and ($prog_id !='')){
            $sqlAdmissionSch .=" and admission_sch_prog_id='$prog_id'";
        }
            
        $sqlAdmissionSch .=" group  by admission_sch_round";
        $resSelectAdmissionSch = DB::select($sqlAdmissionSch);
        // echo $sqlAdmissionSch;exit;

        foreach ($resSelectAdmissionSch as $rowAdmissionSchedules){
				if($rowAdmissionSchedules->admission_sch_round==1){
					$admission_round='Early Admissions';
					$admissionID=1;
				}else if($rowAdmissionSchedules->admission_sch_round==2){
					// $admission_round='Early Admissions Round 2';
					$admission_round='Regular Admissions';
					$admissionID=2;
				}else{
					// $admission_round='Regular Admissions';
					$admission_round='Rolling Admissions';
					$admissionID=3;
				}		
		}

        $output = [];

        foreach ($resSelectAdmissionSch as $row) {
            switch ($row->admission_sch_round) {
                case 1:
                    $name = 'Early Admissions';
                    break;
                case 2:
                    $name = 'Regular Admissions';
                    break;
                default:
                    $name = 'Rolling Admissions';
            }

            $output[] = [
                'admission_sch_id' => $row->admission_sch_round,
                'admission_sch_name' => $name,
            ];
        }

        return response()->json($output);
	
    }


    function schoolsloginUserOnly(Request $request){
        $SchId = auth()->user()->school_id;
        $EmpId = auth()->user()->employee_id;
        // dd($EmpId);
        if ($EmpId == 350) {
            $query = "SELECT * FROM SCHOOLS 
                    WHERE school_id IN (?, 12, 21) 
                    AND school_id NOT IN (8, 9, 10, 11) 
                    ORDER BY school_id ASC";
            return DB::select($query, [$SchId]);
        }elseif($EmpId == 1026 || $EmpId == 3455 || $EmpId == 3475){
            $query = "SELECT * FROM SCHOOLS 
           
            where school_id NOT IN (8, 9, 10, 11) 
            ORDER BY school_id ASC";
            // echo $query;exit;
            return DB::select($query);    


        }else {
            if (in_array($SchId, [12, 21])) {
                $query = "SELECT * FROM SCHOOLS 
                        WHERE school_id IN (12, 21) 
                        AND school_id NOT IN (8, 9, 10, 11) 
                        ORDER BY school_id ASC";

                return DB::select($query);
            } else {
                $query = "SELECT * FROM SCHOOLS 
                        WHERE school_id = ? 
                        AND school_id NOT IN (8, 9, 10, 11) 
                        ORDER BY school_id ASC";

                return DB::select($query, [$SchId]);
            }
        }
    }

   


    function departmentbySchool(Request $request){
        //dd($request->all());
        $schoolId = $request->get('school_id');
        $departmentSql = "select * from departments where dept_school_id = $schoolId";
        return $resdepartmentSql = DB::select($departmentSql);

    }

     function departmentbyPrograms(Request $request){
         //    dd($request->all());
        // $deptId = $request->get('dept_id');
        // $programsSql = "select * from programs where prog_dept_id = $deptId";
        // return $resprogramsSql = DB::select($programsSql);

         //    dd($request->all());   
        $deptId = $request->get('dept_id');
        $rdmapsession = $request->get('rdmapsession');
        // $programsSql = "select * from programs where prog_dept_id = $deptId";
        $programsSql = "SELECT
            distinct prog_name,prog_id
        FROM
            students_apply_info
            left join programs on sai_sdpo_prog_id  = prog_id
        WHERE
                sai_sdpo_sess_id = $rdmapsession
            AND sai_sdpo_dept_id = $deptId
            AND sai_student_status in ('Active','Expected-Graduate')";
            // echo $programsSql;exit;
        return $resprogramsSql = DB::select($programsSql);

    }

    function departments(Request $request){
        $where = [];
        $where['dept_status'] = 'E';
        if($request->get('school_id') != ''){
            $where['dept_school_id'] = $request->get('school_id');
        }
        return DB::table('departments')->where($where)->orderBy('dept_name', 'asc')->get();
    }

    function programs(Request $request){
        
        $where = [];
        $where['prog_status'] = 'E';
        if($request->get('dept_id') != ''){
            $where['prog_dept_id'] = $request->get('dept_id');
        }
        return DB::table('programs')->where($where)->orderBy('prog_name', 'asc')->get();
    }

    function activeschools(Request $request){
        $SchId=auth()->user()->school_id;
      
        return DB::table('SCHOOLS')
            ->whereNotIn('school_id', [8, 9, 10, 11])
            ->orderBy('school_id', 'asc')
            ->get();
    }

    function activedepartments(Request $request){

        $sql = "SELECT
                    *
                FROM
                    departments
                WHERE
                    
                     dept_school_id = '".$request->get('school_id')."'";
                //  echo   $sql ;exit;
        return $res = DB::select($sql);
        // $sql = "SELECT
        //             *
        //         FROM
        //             departments
        //         WHERE
        //             dept_id IN (
        //                 SELECT
        //                     sai_sdpo_dept_id
        //                 FROM
        //                     students_apply_info
        //                 WHERE
        //                     sai_student_status = 'Active'
        //                       and sai_sdpo_sess_id = 162
        //             )
        //             and dept_school_id = '".$request->get('school_id')."'";
        //            // echo   $sql ;exit;
        // return $res = DB::select($sql);
    }

    function activeprograms(Request $request){
        $sql = "SELECT
                    *
                FROM
                    programs
                WHERE
                    prog_id IN (
                        SELECT
                            sai_sdpo_prog_id
                        FROM
                            students_apply_info
                        WHERE
                            sai_student_status = 'Active'
                              --and sai_sdpo_sess_id = 162
                    )
                    and prog_dept_id = '".$request->get('dept_id')."'
                    and prog_status = 'E'";
        return $res = DB::select($sql);
    }

    function studentStatus(Request $request){
        $sql = "SELECT
                distinct sai_student_status as sai_student_status
            FROM
                students_apply_info
            WHERE
                sai_student_status like '%Convocated%'";
        return $res = DB::select($sql);
    }

    function teacher(Request $request){
        // $where = [];
        // $where['teacher_status'] = 'E';
        // if($request->get('dept_id') != ''){
        //     $where['prog_dept_id'] = $request->get('dept_id');
        // }
		$sessId=$request->get('sess_id');
        $sqlFacutly = "SELECT 
		 trim(emp_f_name) || ' ' || trim(emp_l_name) as emp_f_name,
         employee_id,
        trim(emp_l_name) AS EMP_L_NAME,
        prefix,
        birth_date,
      --  nationality,
      --  country_name,
        emp_gender,
        desig_id,
        department_id,
        company_id,
        emp_father_name,
        emp_nic,
        emp_pref_name,
        emp_address,
        emp_status,
        emp_order,
        emp_type,
        emp_email,
        emp_contact_no,
        emp_ad_username,
        emp_school_id,
        school_code,
        emp_dept_id,
        dept_name,
        EMP_IMAGEFILE,
        emp_nic_pic
      FROM emp_employees
    --  LEFT JOIN countries ON nationality = country_id
      LEFT JOIN schools   ON emp_school_id = school_id
      LEFT JOIN departments  ON emp_dept_id   = dept_id
      WHERE 1          =1
      AND employee_id IN
        ( SELECT DISTINCT fc_employee_id
        FROM faculty_courses
        WHERE fc_EMPLOYEE_ID NOT IN (1605)
        AND fc_co_sess_id      ='$sessId'
        )
     ";

if($request->get('school_id')!=''){
    //$sqlCoursesOffered .= "AND co_id = ".$_REQUEST['sel_frm_co_id']." ";
    $sqlFacutly .= " AND EMP_SCHOOL_ID='".$request->get('school_id')."'";
}
$sqlFacutly .= " ORDER BY emp_school_id,
emp_dept_id,
emp_type";
         $result = DB::select($sqlFacutly);
        return  $result;
    }

    public function course_offering_exam(Request $request){
    
            $sql = "SELECT CO_ID ,
            CO_SESS_ID ,
            SESS_NAME ,
            CO_SCHOOL_ID ,
            SCHOOL_CODE ,
            SCHOOL_NAME ,
            CO_DEPT_ID ,
            DEPT_NAME ,
            CO_SHIFT_ID ,
            SHIFT_NAME ,
            CO_COURSE_ID ,
            courses_two.COURSE_CODE ,
            courses_two.COURSE_CD_NAME ,
            CO_SECTION ,
            CO_COURSE_CR ,
            CO_CAPACITY ,
            CO_TYPE ,
            CT_NAME ,
            CO_CUSTOM ,
            CO_DESC ,
            CO_STATUS ,
            CO_ORDER ,
            CO_REQUEST_STATUS ,
            CO_REQUEST_PARENT_ID ,
            CO_ENTRY_USER_ID ,
            CO_ENTRY_DATETIME ,
            PARENT_CO_ID ,
            CO_APPROVED_CAPACITY ,
            (SELECT DECODE(COUNT(*), 0, 'N', 'Y') rec_exists
            FROM course_facilities
            WHERE cf_co_id = co_id
            ) FACILITIES_EXIST ,
            (SELECT DECODE(COUNT(*), 0, 'N', 'Y') rec_exists
            FROM faculty_courses
            WHERE fc_co_id = co_id
            ) FACULTY_EXIST ,
            (SELECT DECODE(COUNT(*), 0, 'N', 'Y') rec_exists
            FROM timetable
            WHERE tt_co_id = co_id
            ) TIMETABLE_EXIST ,
            (SELECT DECODE(COUNT(*), 0, 'N', 'Y') rec_exists
            FROM program_courses
            WHERE pc_co_id = co_id
            ) ROADMAP_EXIST ,
            (
            CASE
                (SELECT co_request_status
                FROM courses_offered t2
                WHERE t2.co_id = courses_offered.co_id
                )
              WHEN 'Requested'
              THEN '1'
              WHEN 'Approved'
              THEN '2'
              WHEN 'ParApproved'
              THEN '3'
              WHEN 'Rejected'
              THEN '4'
              ELSE '0'
            END) com_parent_request_status ,
            (SELECT DECODE(COUNT(co2.co_id), 0, 'N', 'Y')
            FROM courses_offered co2
            WHERE co2.co_request_parent_id=courses_offered.co_id
            ) child_exist ,
            (SELECT DECODE(COUNT(*), 0, 'N', 'Y')
            FROM courses_offered co2
            WHERE co2.co_request_parent_id=COURSES_OFFERED.co_id
            ) parent_exist
          FROM courses_offered
          LEFT JOIN academic_sessions
          ON co_sess_id = sess_id
          LEFT JOIN schools
          ON co_school_id = school_id
          LEFT JOIN departments
          ON co_dept_id = dept_id
          LEFT JOIN shifts
          ON co_shift_id = shift_id
          LEFT JOIN courses_two
          ON co_course_id = course_id
          LEFT JOIN course_types
          ON co_type     = ct_id
          WHERE 1        =1
          AND co_sess_id = '".$request->get('sel_sess_id')."'
          AND co_status  = 'E'
          ";


if($request->get('sel_fc_emp_email')!=''){
    //$sqlCoursesOffered .= "AND co_id = ".$_REQUEST['sel_frm_co_id']." ";
    $sql .= "AND co_id in(SELECT FC_CO_ID
                                         FROM FACULTY_COURSES
                                        LEFT JOIN emp_employees ON fc_employee_id = employee_id
                                        WHERE emp_email = '".$request->get('sel_fc_emp_email')."' and fc_status = 'E')";
}
$sql .= "ORDER BY co_sess_id,
co_school_id,
co_dept_id,
co_shift_id,
co_type,
co_section";
//echo $sql;exit;
$result = DB::select($sql);
return  $result;


    }

 public function course_offering(Request $request) {

    $sqlCoursesOffered = "SELECT CO_ID
								  ,CO_SESS_ID
								  ,SESS_NAME
								  ,CO_SCHOOL_ID
								  ,SCHOOL_CODE
								  ,SCHOOL_NAME
								  ,CO_DEPT_ID
								  ,DEPT_NAME
								  ,CO_SHIFT_ID
								  ,SHIFT_NAME
								  ,CO_COURSE_ID
								  ,courses_two.COURSE_CODE
								  ,courses_two.COURSE_CD_NAME
								  ,CO_SECTION
								  ,CO_COURSE_CR
								  ,CO_CAPACITY
								  ,CO_TYPE
								  ,CT_NAME
								  ,CO_CUSTOM
								  ,CO_DESC
								  ,CO_STATUS
								  ,CO_ORDER
								  ,CO_TOTAL_CLASSES
								  ,CO_REQ_CLASSES
								  ,CO_ATTENDANCE_TYPE
								  ,CO_IP_RESTRICTIONS_YESNO
								  ,CO_REQUEST_STATUS
								  ,CO_REQUEST_PARENT_ID
								  ,CO_ENTRY_USER_ID
								  ,CO_ENTRY_DATETIME
								  ,PARENT_CO_ID
								  ,CO_APPROVED_CAPACITY
								  ,(SELECT DECODE(COUNT(*), 0, 'N', 'Y') rec_exists 
									FROM course_facilities 
									WHERE cf_co_id = co_id
								   ) FACILITIES_EXIST
								  ,(SELECT DECODE(COUNT(*), 0, 'N', 'Y') rec_exists 
									FROM faculty_courses 
									WHERE fc_co_id = co_id
								   ) FACULTY_EXIST
								    ,(SELECT DECODE(COUNT(*), 0, 'N', 'Y') rec_exists 
									FROM timetable 
									WHERE tt_co_id = co_id
								   ) TIMETABLE_EXIST
								   ,(SELECT DECODE(COUNT(*), 0, 'N', 'Y') rec_exists 
									FROM program_courses 
									WHERE pc_co_id = co_id
								   ) ROADMAP_EXIST
								   ,
								  (CASE
									  (SELECT co_request_status
								  FROM courses_offered t2
								  WHERE t2.co_id = courses_offered.co_id
								  )
									WHEN 'Requested'
									THEN '1'
									WHEN 'Approved'
									THEN '2'
									WHEN 'ParApproved'
									THEN '3'
									WHEN 'Rejected'
    								THEN '4'
									ELSE '0'
								  END) com_parent_request_status
								  ,(SELECT DECODE(COUNT(co2.co_id), 0, 'N', 'Y') FROM courses_offered co2 
								  WHERE co2.co_request_parent_id=courses_offered.co_id) child_exist
								  ,(select DECODE(COUNT(*), 0, 'N', 'Y') 
								  from courses_offered co2 
								  where co2.co_request_parent_id=COURSES_OFFERED.co_id) parent_exist,
								  GET_COURSE_CAPACITY(co_id) as rem_capacity,
    case 
    when parent_co_id is null and co_request_status is null then '1'
     when parent_co_id is null and co_request_status='Approved'then '2'
    when parent_co_id is not null and co_request_status='Approved' then '3'
    else '0'
    END as ORDER_TYPE
							FROM courses_offered
							LEFT JOIN academic_sessions ON co_sess_id   = sess_id
							LEFT JOIN schools           ON co_school_id = school_id
							LEFT JOIN departments       ON co_dept_id   = dept_id
							LEFT JOIN shifts            ON co_shift_id  = shift_id
							LEFT JOIN courses_two       ON co_course_id = course_id 
							LEFT JOIN course_types      ON co_type      = ct_id
							WHERE 1=1
							
							";
	if(isset($_REQUEST['sel_sess_id']) and $_REQUEST['sel_sess_id']!=''){
		$sqlCoursesOffered .= "AND co_sess_id = ".$_REQUEST['sel_sess_id']." ";	
	}
	if(isset($_REQUEST['sel_school_id']) and $_REQUEST['sel_school_id']!=''){
		$sqlCoursesOffered .= "AND co_school_id = ".$_REQUEST['sel_school_id']." ";	
	}
	if(isset($_REQUEST['sel_dept_id']) and $_REQUEST['sel_dept_id']!=''){
		$sqlCoursesOffered .= "AND co_dept_id = ".$_REQUEST['sel_dept_id']." ";	
	}
	if(isset($_REQUEST['co_status']) and $_REQUEST['co_status']!=''){	
		$sqlCoursesOffered .= " AND co_status = '".$_REQUEST['co_status']."' ";	
	}
	if(isset($_REQUEST['sel_shift_id']) and $_REQUEST['sel_shift_id']!=''){
		$sqlCoursesOffered .= "AND co_shift_id = ".$_REQUEST['sel_shift_id']." ";
			
	}
	if(isset($_REQUEST['timetable']) == 'time_table'){
		$sqlCoursesOffered .= "and co_id in (select mtt_co_id from master_timetable where mtt_co_id  = co_id and tt_status = 'E') ";
	}
	if(isset($_REQUEST['co_course_id']) and  $_REQUEST['co_course_id']!=''){
		$sqlCoursesOffered .= "AND CO_COURSE_ID = ".$_REQUEST['co_course_id']." ";	
	}
	if(isset($_REQUEST['pc_course_id']) and  $_REQUEST['pc_course_id']=='1'){
		$sqlCoursesOffered .= "AND CO_TYPE = 1 ";	
	}
	if(isset($_REQUEST['co_id']) and  $_REQUEST['co_id']!=''){
		$sqlCoursesOffered .= "AND co_id = ".$_REQUEST['co_id']." ";	
	}
	if(isset($_REQUEST['only_parent']) and $_REQUEST['only_parent']== 1){
		$sqlCoursesOffered .= "AND ((PARENT_CO_ID IS NULL) or (parent_co_id = '0'))"; 	
	}
	if(isset($_REQUEST['sel_frm_co_id']) and $_REQUEST['sel_frm_co_id']!=''){
		$sqlCoursesOffered .= "AND co_id = ".$_REQUEST['sel_frm_co_id']." ";
	}
	if(isset($_REQUEST['sel_sess_co_id']) and $_REQUEST['sel_sess_co_id']!=''){
		$sqlCoursesOffered .= "AND co_id = ".$_REQUEST['sel_sess_co_id']." ";
	}
	if(isset($_REQUEST['status']) and $_REQUEST['status']== 1){
			$sqlCoursesOffered .= "AND (CO_REQUEST_STATUS = 'Approved' OR CO_REQUEST_STATUS = 'Requested') "; 	
	}
	if(isset($_REQUEST['no_chalid']) and $_REQUEST['no_chalid']=='yes'){
			//$sqlCoursesOffered .= "AND parent_co_id is not null"; 	
	}
	if(isset($_REQUEST['sel_fc_emp_email']) and $_REQUEST['sel_fc_emp_email']!=''){
		//$sqlCoursesOffered .= "AND co_id = ".$_REQUEST['sel_frm_co_id']." ";
		$sqlCoursesOffered .= "AND co_id in(SELECT FC_CO_ID
 											FROM FACULTY_COURSES
											LEFT JOIN emp_employees ON fc_employee_id = employee_id
											WHERE emp_email = '".$_REQUEST['sel_fc_emp_email']."' and fc_status='E')";
	}
	if(isset($_REQUEST['faculty_based_courses']) and $_REQUEST['faculty_based_courses']=='YES'){
		//$sqlCoursesOffered .= "AND co_id = ".$_REQUEST['sel_frm_co_id']." ";
		$sqlCoursesOffered .= "AND co_id in(SELECT FC_CO_ID
 											FROM FACULTY_COURSES
											LEFT JOIN emp_employees ON fc_employee_id = employee_id
											and fc_status='E'
											)";
	}
	if(isset($_REQUEST['sel_parent_co_id']) and $_REQUEST['sel_parent_co_id']!=''){
		$sqlCoursesOffered .= "AND parent_co_id = ".$_REQUEST['sel_parent_co_id']." ";
	} else if(isset($_REQUEST['show_all_courses']) and $_REQUEST['show_all_courses']==1) {
		$sqlCoursesOffered .= " ";
	}else{
		//$sqlCoursesOffered .= " AND parent_co_id = 0 ";
	}
	if(isset($_REQUEST['sel_cc_co_id']) and $_REQUEST['sel_cc_co_id']!= ''){
			$sqlCoursesOffered .= "AND co_request_parent_id = ".$_REQUEST['sel_cc_co_id']." ";	
	}
	if(isset($_REQUEST['sel_request_status']) and $_REQUEST['sel_request_status']!= ''){
			$sqlCoursesOffered .= "AND co_request_status = '".$_REQUEST['sel_request_status']."' ";	
	}
	
	$sqlCoursesOffered .= " ORDER BY ORDER_TYPE,COURSE_CD_NAME--parent_co_id desc,COURSE_CD_NAME";
	
	//echo $sqlCoursesOffered;exit();
	$resCoursesOffered = $mydb->query_select($sqlCoursesOffered);
 }

public function page_name(){

    $sql_page_name = "select GMP_PAGE_ID,GMP_PAGE_NAME from groups_module_pages where gmp_group_id=4 and GMP_STATUS='R' and gmp_page_id in (321,381)";
    return $rec = DB::select($sql_page_name);
 }
}
