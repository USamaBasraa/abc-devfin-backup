<?php
namespace App\Http\Controllers\Grader\Library;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\Console\Helper\Table;
use DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use App\sessions;
use App\Models\Schools;
use App\Models\BookItems;
use App\LibraryCirculation;
use App\LibraryCriteria;
use App\Models\BookCirculation;
use App\Models\Departments;
use App\Models\FacultyCourses;
use App\Models\EmpEmployees;
use Symfony\Component\HttpFoundation\Response;
use \Milon\Barcode\DNS1D;
use App\Models\User;

class CirculationDashBoardController extends Controller{




    function searchUser(Request $request){
        $query = $request->get('query');
        return  DB::table('sisusers')
                ->whereRaw("full_name || username like '%$query%' ")
                ->paginate(15);
        return response()->json(array('status' => true,'data'=> $users, 'message' => '' ));
    }


    function searchByFilter(Request $request){
        $username   = $request->get('username');
        $accesstion = $request->get('accesstion_user');    

        if($username != '' ){
            $data = $this->getUserPersonalInfo($request);
            if($data){
                return $data;
            }else{
                return  [];   
            }
        }elseif($accesstion != ''){
            $result = DB::select("SELECT SAI_ENROLLMENT_NO from library_circulation where LC_ACCESSION_NO = $accesstion and lc_book_status = 'Issued' ");
            if( isset($result[0]->sai_enrollment_no) ){
                $username = $result[0]->sai_enrollment_no;
                $request->request->add(array('username' => $username));
                return $this->getUserPersonalInfo($request);
            }else{
                return  [];     
            }
        }else{
            return [];
        }
    }


    function getUserPersonalInfo(Request $request){
        $username= $request->get('username');
        $query = "SELECT sisusers.id ,
                                username,
                                library_status, 
                                email ,
                                group_id ,
                                mobile,
                                emp_f_name,
                                emp_l_name ,
                                prefix, emp_f_name, emp_l_name emp_full_name,
                                emp_email ,
                                imagename emp_photo ,
                                emp_school_id ,
                                emp_sch.school_name AS emp_school_name,
                                emp_dept_id ,
                                emp_dept.dept_name AS emp_dept_name,
                                emp_type,
                                sai_spi_id,
                                COALESCE(sai_spi_id,0) AS spi_id ,
                                spi_enroll_prefix ,
                                spi_enroll_id ,
                                spi_name,
                                spi_imagename,
                                spi_fathername,
                                spi_presentaddress,
                                designation,
                                spi_sess_id,
                                sai_sdpo_sess_id   AS SPI_ENROLLMENT_SESSION,
                                sai_sdpo_school_id,
                                sai_cgpa,
                                sai_prog_total_credits,
                                sai_sdpo_dept_id   AS spi_dept_id ,
                                sai_sdpo_prog_id   AS spi_prog_id ,
                                sai_sdpo_shift_id,
                                sai_sdpo_shift_id AS spi_shift_id ,
                                sai_semester      AS sai_active_semester,
                                sai_enrollment_no AS SPI_ENROLLMENT_NO ,
								sai_student_status,
                                spi_mobile,
                                spi_email,
                                (SELECT MAX(sess_id)
                                FROM academic_sessions
                                WHERE sess_admission_active='Yes'
                                ) spi_active_sess_id,
                                sai_id,
                                (SELECT max(gpm_active_sessl_id) as gpm_active_sessl_id
                                FROM group_pages_management
                                WHERE gpm_school_id = sai_sdpo_school_id
                                ) AS active_enroll_sess,
								api_token                                 
                              FROM sisusers
                              LEFT JOIN emp_employees           ON LOWER(email)      = LOWER(emp_email)
                              LEFT JOIN schools emp_sch         ON emp_school_id     = emp_sch.school_id
                              LEFT JOIN departments emp_dept    ON emp_dept_id       = emp_dept.dept_id
                              LEFT JOIN students_apply_info     ON LOWER(username)   = LOWER(sai_enrollment_no)
                              LEFT JOIN students_personal_info  ON sai_spi_id        = spi_id
                                                               AND sai_student_status!='Transferred'
                          WHERE LOWER(username)=LOWER('".$username."') ";
          $result = DB::select($query);
    //   echo $query;exit;
          // print_r($result); exit;
          if(count($result) > 0){
              $row = $result[0];
              $data['id']             = $row->id;
              $data['user_full_name'] = null;
              $data['user_father_name']         = '';
              $data['username']       = $row->username;
              $data['email']          = $row->email;
              $data['group_id']       = $row->group_id;
			  $data['student_status']       = $row->sai_student_status;
              $data['user_type']      = ($row->group_id == 4)?'student':'staff';
              $data['image']          = null;
              $data['school']         = null;
              $data['school_id']      = null;
              $data['dept_id']        = null;
              $data['dept']           = null;
              $data['prog_id']        = null;
              $data['prog']       	  = null;
              $data['mobile_staff']   =   $row->mobile;
              $data['library_status'] = $row->library_status;
              $data['prog_degree_level']       	= null;
              $data['designation']              =  $row->designation;

              if(!($row->spi_name)){
                $data['user_full_name'] = $row->prefix.' '.$row->emp_f_name.' '.$row->emp_l_name;
                $data['image']          = "http://cmstest.bnu.edu.pk/staffimages/".strtoupper($row->username).".jpg";
                //      Route::post('get_image/{type}/{id}', 'ApiAuthController@get_user_image');
                //      $data['image']          = url('get_image/emp/'.$row->username);
                $data['school']         = $row->emp_school_name;
                $data['school_id']      = $row->emp_school_id;
                $data['dept']           = $row->emp_dept_name;
                $data['dept_id']        = $row->emp_dept_id;
              }else{
                // student side
                $data['user_full_name']             = $row->spi_name;
                $data['user_father_name']           = $row->spi_fathername;
                $data['image']                      = "http://stafftest.bnu.edu.pk/studentpics/".strtoupper($row->username).".jpg";
               // $data['image']          = url('get_image/student/'.$row->username);
                $data['school_id']      = $row->sai_sdpo_school_id;
                $data['enrollment_no']  = strtoupper($row->spi_enrollment_no);
                $data['dept_id']        = $row->spi_dept_id;
                $data['prog_id']        = $row->spi_prog_id;
                $data['sai_cgpa']        = $row->sai_cgpa;
                $data['spi_presentaddress']        = $row->spi_presentaddress;
                $data['sai_prog_total_credits']    = $row->sai_prog_total_credits;
                $data['sai_cgpa']        = $row->sai_cgpa;
                $data['api_token']        = $row->api_token;

                // SPI_ACTIVE_SESS_ID
                $data['active_sess_id']         =   $row->spi_active_sess_id;
                $data['mobile']                 =   $row->spi_mobile;
                $data['email']                  =   $row->spi_email;
                $sai_enrollment_no              =   ucfirst($row->spi_enrollment_no);
                $semester_no                    =   DB::selectOne("select get_semester_no('$sai_enrollment_no') as value from dual");
                $curr_semester                  =   explode("~",$semester_no->value);
                $data['current_session']        =   (isset($curr_semester[1]))?$curr_semester[1]:'Contact RO'; //      $curr_semester[1];
                $data['current_semester']       =   (isset($curr_semester[0]))?$curr_semester[0]:'Contact RO'; //$curr_semester[0]


              if($data['school_id']){
                  $school_res = DB::select("SELECT school_name FROM schools where school_id = ".$data['school_id']);
                  $data['school']         = (isset($school_res[0]->school_name))?$school_res[0]->school_name:'';
              }

              if($data['dept_id']){
                  $dept_res 				= DB::select("SELECT dept_name FROM departments where dept_id = ".$data['dept_id']);
                  $data['dept']           = (isset($dept_res[0]->dept_name))?$dept_res[0]->dept_name:'';
              }


              if($data['prog_id']){
                  $prog_res = DB::select("SELECT prog_title, prog_degree_level FROM programs where prog_id = ".$data['prog_id']);
                  if(count($prog_res) > 0){
                      $data['prog'] 				= $prog_res[0]->prog_title;
                      $data['prog_degree_level']  = $prog_res[0]->prog_degree_level;
                  }
              }
              }
              return $data;
          }else{
              return false;
          }
    }


    function userSearchSuggestionLibrary(Request $request){
        $name   =   $request->get('qry');
        return   User::selectRaw('  username as value, full_name as text ')->whereRaw(" lower(full_name || ' ' || username) like lower('%$name%') ")->limit(10)->get();
    }

    
   function userSearchSuggestion(Request $request){
        $getStudentRec = getStudentSuggestion($request->get('qry'));
        // dd($getStudentRec);
        $name_enrollmentNo_aaiId = $request->get('qry');
        // dd($name_enrollmentNo_aaiId);
        $studentType   =  $request->studentType;
        if($studentType == 'Existing'){
            $data = User::selectRaw('  username as value, full_name as text ')
            ->whereRaw(" lower(full_name || ' ' || username) like lower('%$name_enrollmentNo_aaiId%') ")
            ->limit(10)->get();
            return $data;
        }else{
            $data='';
            $student_sql = "select
                                aai.*
                            from admission_apply_info aai
                            where
                                aai.aai_id='$name_enrollmentNo_aaiId'";
                            //    echo $student_sql;exit;
            $student_data = DB::select($student_sql);
            // dd($student_data[0]);
            if(!empty($student_data[0])){
                // if(empty($student_data[0]->cms_sai_enrollment_no) && !empty($student_data[0]->aai_id)){
                    $sql = "SELECT
                        AAI_ID as value,
                        API_NAME as text
                    FROM
                        admission_apply_info
                    LEFT JOIN STUDENTS_APPLY_INFO ON aai_api_id = STUDENTS_APPLY_INFO.SAI_API_ID and STUDENTS_APPLY_INFO.sai_sdpo_prog_id=aai_sdpo_prog_id
                    LEFT JOIN admission_personal_info ON API_ID = AAI_API_ID
                    where  lower(API_NAME || ' ' || AAI_ID) LIKE lower('%$name_enrollmentNo_aaiId%')
                    FETCH FIRST 10 ROWS ONLY";
                    $data =  DB::select($sql);
                    // dd(1, $data);
                    return $data;
                }
                // dd(2, $data);
                // return $data;
            }
            // dd(3, $data);
            return $data;

    }
    function campusSearchSuggestion(Request $request){
        $name   =   strtolower($request->get('qry'));  
      
        $sql = "select  distinct sai_id rn ,sai_enrollment_no as value,spi_name||'  '||upper(CAR_POOLING.CP_VEHICAL_NO) as text
        from students_apply_info
        left join  students_personal_info on spi_id =sai_spi_id
        left join CAR_POOLING on (LOWER(CP_ENTRY_USERNAME) = LOWER(sai_enrollment_no) or LOWER(car_pooling.cp_partner) = LOWER(sai_enrollment_no) )
        --left join car_pooling cp_two ON lower(car_pooling.cp_partner) LIKE '%' || lower(students_apply_info.sai_enrollment_no) || '%'
        where students_apply_info.sai_student_status in ('Active','Expected-Graduate')
        and CAR_POOLING.cp_id is not null
        and lower(sai_enrollment_no||spi_name||CAR_POOLING.CP_VEHICAL_NO) like '%$name%'
        union all
        select employee_id,emp_ad_username,emp_f_name||' '||emp_l_name||' '||upper(CAR_POOLING.CP_VEHICAL_NO) as text from emp_employees  
        left join CAR_POOLING on LOWER(CP_ENTRY_USERNAME) = LOWER(emp_ad_username)
        left join car_pooling cp_two ON lower(car_pooling.cp_partner) LIKE '%' || lower(emp_ad_username) || '%'
        where  CAR_POOLING.cp_id is not null
        and lower(emp_ad_username||emp_f_name||CAR_POOLING.CP_VEHICAL_NO) like '%$name%'  
        ";
    
        return DB::select($sql);
      
    }



    function userIssuedBookHistory(Request $request){

        $username   = $request->get('username');
        $sql        = "SELECT library_author_name,library_author_mark,library_ddc_number,LIBRARY_CIRCULATION.id as id,lc_due_date ,
        lc_return_date ,LIBRARY_CIRCULATION.lc_accession_no as lc_accession_no,lc_return_date ,lc_issue_no_of_days,lc_issue_date ,
        lc_book_status ,lc_fine_total,sai_enrollment_no ,library_title ,library_books_id,lc_total_late_days,lc_fine_status
        FROM LIBRARY_CIRCULATION
        left join library_books on library_books_id = library_books.id
      --  LEFT JOIN library_accession_number ON library_accession_number.book_id = library_books.id
        where LIBRARY_CIRCULATION.SAI_ENROLLMENT_NO = LOWER('$username') order by lc_book_status ASC ";

        return DB::select($sql);

    }

    function eportalSearch(Request $request){
        $name   =   strtolower($request->get('qry'));   
        $status   =   $request->get('status');       
        $sql = "SELECT
         distinct EPORTAL_REQUEST.id rn ,EPORTAL_REQUEST.er_sai_enrollment_no as value,spi_name as text
        FROM EPORTAL_REQUEST
        Left JOIN sisusers ON lower(EPORTAL_REQUEST.ER_SAI_ENROLLMENT_NO) = lower(sisusers.USERNAME)
        LEFT JOIN students_apply_info ON lower(students_apply_info.sai_enrollment_no) = lower(eportal_request.er_sai_enrollment_no)
        LEFT JOIN students_personal_info ON spi_id = sai_spi_id
        left join eportal_categories on ec_id = eportal_categories.id
        left join eportal_request_details on er_id = eportal_request.id
        where   1=1 and er_ecd_resolving_user in (8996,822) 
        and (LOWER(er_sai_enrollment_no) LIKE '%' || LOWER('$name') || '%' or LOWER(spi_name) LIKE '%' || LOWER('$name') || '%')";
        if($status == 'Completed'){
            $sql .= " and eportal_request.ER_REQUEST_STATUS in ('$status','Declined')";
        }else{
            $sql .= " and eportal_request.ER_REQUEST_STATUS in ('$status','Forward')";
        }
       // echo $sql;exit();
        return DB::select($sql);
      
    }

    function bookTobeIssued(Request $request){

        // $username   = $request->get('username');
        // $sql        = "SELECT library_author_name,library_author_mark,library_ddc_number,LIBRARY_CIRCULATION.id as id,lc_due_date ,
        // lc_return_date ,LIBRARY_CIRCULATION.lc_accession_no as lc_accession_no ,lc_issue_no_of_days,lc_issue_date ,
        // lc_book_status ,lc_fine_total,sai_enrollment_no ,library_title ,library_books_id,lc_total_late_days
        // FROM LIBRARY_CIRCULATION
        // left join library_books on library_books_id = library_books.id
        // LEFT JOIN library_accession_number ON library_accession_number.book_id = library_books.id
        // -- where LIBRARY_CIRCULATION.SAI_ENROLLMENT_NO = '$username'
        // ";

        $sqlLibraryBooks = "SELECT library_title,library_author_name,accession_no,(select count(*)from library_accession_number where book_id = library_books.id) as total_books
        FROM LIBRARY_BOOKS   
        left join library_accession_number on book_id = library_books.id    
        where LIBRARY_BOOKS.id = '$id'
        and library_accession_number.accession_no = '$access_no'
        and book_status = 'Active'";

//        $view_sqlLibraryBooks = DB::select($sqlLibraryBooks);

        return DB::select($sqlLibraryBooks);

    }



    function getUserDetailPDF(Request $request){
       // $request->get('username');exit;
        $data = (Object) $this->getUserPersonalInfo($request);
       //print_r($data); exit;
        return view('grader.library.student_details',array('data' => $data));
    }

    function updateStatus(Request $request){
       // print_r($request->all()); exit;
        $status = $request->get('status');
        $id     = $request->get('username');
        $update_library_status = DB::table('SISUSERS') ->where('username', $id)->update([
            'LIBRARY_STATUS' => $status           
        ]);
        if($update_library_status){
            return response()->json(array('status' => true,'data'=> [], 'message' => 'Status Updated Successfully',
              'action' => [ 'type' => 'redirect', 'url' => 'abc' ]));
          }  
    }

  



}
