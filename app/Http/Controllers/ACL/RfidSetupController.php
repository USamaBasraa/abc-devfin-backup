<?php

namespace App\Http\Controllers\ACL;

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

class RfidSetupController extends Controller{

    function rfidAssignmentForm(){
         $groups =  DB::select("select * from ACL_GROUPS where AG_STATUS = 'E'");
        return view('acl.rfidAssignmentForm',compact('groups'));
    }
    function autoSearch(Request $request){
        // dd($request->all());
        $username_enrollmnt   =   $request->get('searchParam');

        return  DB::table('SISUSERS')
            ->select(DB::raw('USERNAME as value, FULL_NAME as text'))
            ->whereRaw("lower(FULL_NAME || ' ' || USERNAME) like lower(?)", ["%$username_enrollmnt%"])
            ->limit(10)
            ->get();
    }
    function searchUser(Request $request){

        $username= $request->get('username');
        // Use preg_match to find the string between square brackets
        if (preg_match('/\[(.*?)\]/', $username, $matches)) {
            // $matches[1] will contain the string inside the brackets
            $username = $matches[1];

        }

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
                                rfid,
                                rfid_status,
                                rfid_description,
                                acl_gate,
                                acl_gym,
                                acl_hostel,
                                acl_lab,
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
        // echo $query;exit;
        //   print_r($result); exit;
          if(count($result) > 0){
                $row = $result[0];
                $data['id']             = $row->id;
                $data['user_full_name'] = null;
                $data['user_father_name']         = '';
                $data['username']       = $row->username;
                $data['email']          = $row->email;
                $data['rfid']          = $row->rfid;
                $data['rfid_status']   = $row->rfid_status;
                $data['rfid_description'] = $row->rfid_description;
                $data['gate_group'] = $row->acl_gate;
                $data['gym_group'] = $row->acl_gym;
                $data['hostel_group'] = $row->acl_hostel;
                $data['lab_group'] = $row->acl_lab;
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
                    $data['image']          = "https://cms.bnu.edu.pk/staffimages/".strtoupper($row->username).".jpg";
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
                    $data['image']                      = "https://staff.bnu.edu.pk/studentpics/".strtoupper($row->username).".jpg";
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
            //   dd($data);

                return response()->json(['success' => true, 'profileData' => $data]);
            }else{
                $data='';
                return response()->json(['success' => false, 'profileData' => $data]);
            }
    }
    public function addRfid(Request $request){
        // dd($request->all());
        $validator = Validator::make($request->all(),[
            'rfid' => 'required',
            'rfid_status' => 'required',
            'description' => 'max:400'
        ]);
        if($validator->fails()) {
            return Response::json(array(
                'success' => false,
                'errors' => $validator->getMessageBag()->toArray()
            ), 400);
        }
        // dd(auth()->user()->employee_id);
        $userId = auth()->user()->id;
        $data = [
            'rfid' => $request->get('rfid'),
            'rfid_status' =>$request->get('rfid_status'),
            'rfid_description' =>$request->get('description'),
            'acl_gate' =>$request->get('acl_gate'),
            'acl_gym' =>$request->get('acl_gym'),
            'acl_hostel' =>$request->get('acl_hostel'),
            'acl_lab' =>$request->get('acl_lab'),
            'rfid_entry_user_id' => $userId,
        ];
        // DB::beginTransaction();
        $mesg = '';
        $success = '';

        $affectedRows = DB::table('SISUSERS')
                        ->where('ID', $request->get('sisId'))
                        ->update($data);

        if ($affectedRows > 0) {
            $mesg = 'Updated Successfully';
            $success = true;
        }else{
            $mesg = 'Some Problem occured';
            $success = false;
        }

        return response()->json([
            'success' => $success,
            'msg' => $mesg,
        ]);
    }
    protected function getMachineList(Request $request){
        // dd($request->all());
        $groupCondition = "";
        if(!empty($request->machine_group)){
            $groupCondition = " and agd.AGD_AG_ID = $request->machine_group ";
        }
        $sql = "SELECT

                    agd.AGD_ID,
                    agd.AGD_AG_ID,
                    agd.AGD_MACHINE_IP,
                    agd.AGD_MACHINE_STATUS,
                    agd.AGD_MACHINE_DESCRIPTION,
                    ag.AG_GROUP_NAME

                FROM
                    ACL_GROUPS_DETAILS agd
                    join ACL_GROUPS ag on ag.AG_ID = agd.AGD_AG_ID

                WHERE
                    agd.AGD_MACHINE_STATUS = 'E'
                    $groupCondition
                    order by agd.AGD_ID";
                    // dd( $sql);
            $data = DB::select($sql);


        //    dd($data);
        return DataTables::of($data)->toJson();
    }
    function searchUser_fromMachine(Request $request){
        // dd($request->all());
        $userId= $request->get('user_id');
        $machineIp= $request->get('machine_ip');
        $machine_id= $request->get('machine_id');
        // Use preg_match to find the string between square brackets
        if (preg_match('/\[(.*?)\]/', $userId, $matches)) {
            // $matches[1] will contain the string inside the brackets
            $userId = $matches[1];

        }
        //  $api_data = [
        //         'userId' => $userId,
        //         'machineIp' => $machineIp,
        //         'machine_id' => $machine_id,
        //         ];
        //  // Create a new request instance
        //     $request = Request::create('/api/search-student-info', 'POST', $api_data);

        //     // Call the target controller method
        //     $response = app(ApiController::class)->searchStudentInfo($request);
        //     // dd($response->getData()->results);
        //     $response = $response->getData()->results;
        //     // dd($response[0]);
        //     // Check if the external API call was successful
        //     if(!empty($response[0] && !empty($response[0]->responseStatusStrg))){
        //         $value = $response[0];
        //             if ($value->responseStatusStrg !== 'OK') {
        //                 // Handle the error case
        //                 $mesg = 'Student may not registered on machine '.(!empty($value->machine_ip) ? $value->machine_ip : '').' or the machine is offline';
        //                 $success = false;
        //                 return response()->json([
        //                     'success' => $success,
        //                     'profileData' => [],
        //                     'msg' => $mesg,
        //                 ]);

        //             }
        //         $userInfo = $value->UserInfo;

        //         return response()->json(['success' => true, 'profileData' => $userInfo, 'msg' => '']);
        //     }else{
        //         $value = $response[0];
        //         $data='';
        //         return response()->json(['success' => false, 'profileData' => [], 'msg' => $value->responseStatusStrg]);
        //     }
        $api_data = [
            'userId'     => $userId,
            'machineIp'  => $machineIp,
            'machine_id' => $machine_id,
        ];

        try {
            // Create a new request instance
            $request = Request::create('/api/search-student-info', 'POST', $api_data);

            // Call the target controller method
            $response = app(ApiController::class)->searchStudentInfo($request);
            // Get results safely
            $response = $response->getData()->results ?? null;
            // dd($response);
            if (empty($response) || !is_array($response) || !isset($response[0])) {
                throw new \Exception('No valid response received from machine(s).');
            }

            $value = $response[0];
            // dd($value);
            if (!is_object($value) || !empty($value->UserInfoSearch->responseStatusStrg)) {
                if ($value->UserInfoSearch->responseStatusStrg == 'OK') {
                     // dd($value->UserInfoSearch->UserInfo[0]);
                    $userInfo = $value->UserInfoSearch->UserInfo[0] ?? null;

                    if (empty($userInfo)) {
                        throw new \Exception('User info is missing from machine response.');
                    }

                    return response()->json([
                        'success'     => true,
                        'profileData' => $userInfo,
                        'msg'         => '',
                    ]);
                    //

                }else if ($value->UserInfoSearch->responseStatusStrg == 'NO MATCH'){
                    $value = $response[0];
                    return response()->json(['success' => false, 'profileData' => [], 'msg' => $value->UserInfoSearch->responseStatusStrg]);

                }else{
                    $mesg = 'Student may not be registered on machine '
                        . (!empty($value->machine_ip) ? $value->machine_ip : '')
                        . ' or the machine is offline.';
                    return response()->json([
                        'success'     => false,
                        'profileData' => [],
                        'msg'         => $mesg,
                    ]);
                }

            }else{
                //  throw new \Exception('Invalid response structure from machine(s).');
                return response()->json(['success' => false, 'profileData' => [], 'msg' => $value->message]);
            }



        } catch (\Throwable $e) {
            // Log error for debugging
            \Log::error("Error in student info lookup: " . $e->getMessage());

            return response()->json([
                'success'     => false,
                'profileData' => [],
                'msg'         => $e->getMessage(), // or a generic msg if you don't want to expose details
            ]);
        }

    }
    function updateUser_inMachine(Request $request){
        // dd($request->all());
        $userId= $request->get('user_id');
        $machineIp= $request->get('machine_ip');
        $machine_id= $request->get('machine_id');
        // Use preg_match to find the string between square brackets
        if (preg_match('/\[(.*?)\]/', $userId, $matches)) {
            // $matches[1] will contain the string inside the brackets
            $userId = $matches[1];

        }
        $api_data = [
                'machine_ip' => $request->machine_ip,
                'user_id' => $userId,
                'user_machine_id' => $request->user_machine_id,
                'groupName' => strtolower($request->group),
                'gender' =>  $request->gender,
                'status' => $request->user_machine_status,
                'validity_start' => $request->validity_start,
                'validity_end' => $request->validity_end,
                'time_effective' => $request->has('time_effective'),
                'verify_user_only' => $request->has('verify_user_only')
                ];

        try {
            // Create a new request instance
            $request = Request::create('/api/modify-student-info', 'POST', $api_data);
            $rawResponse = app(ApiController::class)->modifyStudentInfo($request);
            // dd($rawResponse);
            $results = $rawResponse->getData()->results ?? null;
             if (empty($results)) {
                if($rawResponse->getData()->success === false){
                     $results[] = [
                        'machine_ip' => '',
                        'success'    => false,
                        'msg'        => $rawResponse->getData()->msg ?? 'Failed to update user on machine(s).'
                    ];
                    return response()->json([
                        'success' => false,
                        'msg'     => $rawResponse->getData()->msg ?? 'Failed to update user on machine(s).',
                        'results' => $results,
                    ]);

                }
                throw new \Exception('No valid response received from machine(s).');
            }
            $normalized = [];

            foreach ($results as $key => $value) {
                // Case 1: Already structured with machine_ip, status, etc.
                if (is_object($value) && isset($value->machine_ip)) {
                    $normalized[] = [
                        'machine_ip'    => trim($value->machine_ip),
                        'status'        => $value->status ?? 'unknown',
                        'subStatusCode' => $value->subStatusCode ?? 'unknown',
                        'message'       => $value->message ?? '',
                    ];
                }
                // Case 2: IP as key, with status object inside
                elseif (is_object($value) && isset($value->statusCode)) {
                    $normalized[] = [
                        'machine_ip'    => trim($key),
                        'status'        => $value->statusString ?? 'unknown',
                        'subStatusCode' => $value->subStatusCode ?? 'unknown',
                        'message'       => $value->statusString ?? '',
                    ];
                }
                // Case 3: IP as key but null value
                else {
                    $normalized[] = [
                        'machine_ip'    => trim($key),
                        'status'        => 'error',
                        'subStatusCode' => 'fail',
                        'message'       => 'No response from machine.',
                    ];
                }
            }
            // dd($normalized);
            // � Now handle errors first
           $results = [];
            foreach ($normalized as $entry) {
                if ($entry['subStatusCode'] === 'ok') {
                    $results[] = [
                        'machine_ip' => $entry['machine_ip'],
                        'success'    => true,
                        'msg'        => 'Updated successfully on machine ' . $entry['machine_ip'],
                    ];
                } else {
                    $results[] = [
                        'machine_ip' => $entry['machine_ip'],
                        'success'    => false,
                        'msg'        => 'Student may not be registered on machine ' . $entry['machine_ip'] .
                                        ' or the machine is offline. (' . $entry['message'] . ')',
                    ];
                }
            }

            return response()->json([
                'success' => true,   // overall call was successful
                'results' => $results
            ]);
        } catch (\Throwable $e) {
            \Log::error("Error in student info lookup: " . $e->getMessage());

            return response()->json([
                'success'     => false,
                'profileData' => [],
                'msg'         => $e->getMessage(),
            ]);
        }

    }

}
