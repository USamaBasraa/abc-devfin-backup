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
use Illuminate\Support\Facades\Http;
use Response;
use Validator;
use DataTables;
use DateTime;

class ApiController extends Controller{

        // function modifyStudentInfo($enrollment, $groupName, $status, $start, $end=null){
        public function modifyStudentInfo(Request $request){
            // return response()->json([
            //     'data' =>  request->all(),
            // ], 400);
             // Extract parameters from the request body
            $user_id = $request->input('user_id');
            $gender = $request->input('gender');
            if (is_string($gender)) {
                $gender = trim($gender);
            }
            if ($gender === '' || (is_string($gender) && strtolower($gender) === 'null')) {
                $gender = null;
            }
            $groupName = $request->input('groupName');
            $status = $request->input('status');
            $start = $request->input('validity_start');
            $end = $request->input('validity_end'); // Optional
            $time_effective = $request->input('time_effective'); // Optional
            $verify_only = $request->input('verify_user_only'); // Optional
            // dd([
            //     'user_id'        => $user_id,
            //     'gender'         => $gender,
            //     'groupName'      => $groupName,
            //     'status'         => $status,
            //     'validity_start' => $start,
            //     'validity_end'   => $end,
            //     'time_effective' => $time_effective,
            //     'verify_only'    => $verify_only,
            // ]);
            // return response()->json([
            //     'request'=>request()->all(),
            //     'data' =>  [
            //         'user_id'        => $user_id,
            //         'gender'         => $gender,
            //         'groupName'      => $groupName,
            //         'status'         => $status,
            //         'validity_start' => $start,
            //         'validity_end'   => $end,
            //         'time_effective' => $time_effective,
            //         'verify_only'    => $verify_only,
            //     ],
            // ], 400);

            $response = []; // Initialize response array
            // return response()->json([
            //     'data' =>  str_replace(' ', 'T', $start)
            // ], 400);

             // Validate input
            if (!$user_id || !$groupName || !$status || !$start) {
                return response()->json([
                    'success' => false,
                    'message' => 'Missing required fields.',
                    'results' => $response,
                ], 400);
            }
            $machineDetails= $this->getUserACLGroup($user_id, $groupName);
            // dd($machineDetails);
            // return response()->json([
            //     'data' =>  $machineDetails
            // ], 400);
            if(sizeof($machineDetails) == 0){
                return response()->json([
                    'success' => false,
                    'message' => $groupName.' group not assigned or student status is not Active.',
                    'results' => $response,
                ], 400);
            }
            // dd($machineDetails);
            // dd($user_id, $groupName, $status, $start, $end);
            $data = [
                "UserInfo"=>[
                    // "employeeNo"=> $this->toMachineFormat($request->userId_user_id),
                    "employeeNo"=> strtoupper(str_replace('-', '', $user_id)),
                    // "employeeNo"=> $this->toMachineFormat('F2020-220'),
                    // "employeeNo"=> 'SUM24-0220',
                    /*required, string, employee No. (person ID)*/
                        // "name"=>"Laasma Saqib",
                    /*optional, string, person name*/
                    "userType"=>    $status,
                    /*required, string, person type=> "normal"-normal person (household), "visitor",
                    "blackList"-person in blocklist*/
                    /*"closeDelayEnabled"=> "false",
                    /*optional, boolean, whether to enable door close delay=> "true"-yes, "false"-
                    no*/
                    "Valid"=>[
                        /*required, parameters of the effective period, the effective period can be a
                        period of time between 1970-01-01 00:00:00 and 2037-12-31 23:59:59*/
                        "enable"=> $time_effective,
                        /*required, boolean, whether to enable the effective period=> "false"-disable,
                        "true"-enable. If this node is set to "false", the effective period is
                        permanent*/
                        "beginTime"=>(!empty($start) ? str_replace(' ', 'T', $start) : ''),
                        // "beginTime"=>"2024-10-10T11:00:00",
                        /*required, start time of the effective period (if timeType does not exist or
                        is "local", the beginTime is the device local time, e.g., 2017-08-01T17:30:08;
                        if timeType is "UTC", the beginTime is UTC time, e.g.,
                        2017-08-01T17:30:08+08:00)*/
                        "endTime"=>(!empty($end) ? str_replace(' ', 'T', $end) : ''),
                        // "endTime"=>"2024-10-11T12:00:00",
                        /*required, end time of the effective period (if timeType does not exist or is
                        "local", the endTime is the device local time, e.g., 2017-08-01T17:30:08; if
                        timeType is "UTC", the endTime is UTC time, e.g., 2017-08-01T17:30:08+08:00)*/
                        "timeType"=>"local",
                    ],
                    /*optional, string, time type: "local"- device local time, "UTC"- UTC time*/
                        // "belongGroup":""
                    /*optional, string, group*/
                        // "password"=>"123",
                    /*optional, string, password*/
                        // "doorRight":"",
                    /*optional, string, No. of the door or lock that has access permission, e.g.,
                    "1,3" indicates having permission to access door (lock) No. 1 and No. 3*/
                        // "RightPlan":[{
                        //     /*optional, door permission schedule (lock permission schedule)*/
                        //     "doorNo": 1,
                        //     /*optional, integer, door No. (lock ID)*/
                        //     "planTemplateNo":""
                        //     /*optional, string, schedule template No.*/
                        // }],
                    "maxOpenDoorTime"=> 0,
                    /*optional, integer, maximum authentication attempts, 0-unlimited*/
                     /*"openDoorTime": ,
                    /*optional, integer, read-only, authenticated attempts*/
                        /*"roomNumber": ,
                    /*optional, integer, room No.*/
                        /*"floorNumber": ,
                    /*optional, integer, floor No.*/
                        /*"doubleLockRight": ,
                    /*optional, boolean, whether to have the permission to open the double-locked
                    door: "true"-yes, "false"-no*/
                        /*"localUIRight": ,
                    /*optional, boolean, whether to have the permission to access the device local
                    UI: "true"-yes, "false"-no*/
                        /*"localUIUserType":"",
                    /*optional, string, user type of device local UI: "admin" (administrator),
                    "operator", "viewer" (normal user). This node is used to distinguish different
                    users with different operation permissions of device local UI*/
                    // "userVerifyMode"=>"cardAndPw",
                    /*optional, string, person authentication mode: "cardAndPw"-card+password,
                    "card"-card, "cardOrPw"-card or password, "fp"-fingerprint, "fpAndPw"-
                    fingerprint+password, "fpOrCard"-fingerprint or card, "fpAndCard"-fingerprint
                    +card, "fpAndCardAndPw"-fingerprint+card+password, "faceOrFpOrCardOrPw"-face or
                    fingerprint or card or password, "faceAndFp"-face+fingerprint, "faceAndPw"-face
                    +password, "faceAndCard"-face+card, "face"-face, "employeeNoAndPw"-employee No.
                    +password, "fpOrPw"-fingerprint or password, "employeeNoAndFp"-employee No.
                    +fingerprint, "employeeNoAndFpAndPw"-employee No.+fingerprint+password,
                    "faceAndFpAndCard"-face+fingerprint+card, "faceAndPwAndFp"-face+password
                    +fingerprint, "employeeNoAndFace"-employee No.+face, "faceOrfaceAndCard"-face
                    or face+card, "fpOrface"-fingerprint or face, "cardOrfaceOrPw"-card or face or
                    password, "cardOrFace"-card or face, "cardOrFaceOrFp"-card or face or
                    fingerprint, "cardOrFpOrPw"-card or fingerprint or password. The priority of
                    the person authentication mode is higher than that of the card reader
                    authentication mode*/
                    /*"checkUser": ,
                    /*optional, boolean, whether to verify the duplicated person information:
                    "false"-no, "true"-yes. If checkUser is not configured, the device will verify
                    the duplicated person information by default. When there is no person
                    information, you can set checkUser to "false" to speed up data applying;
                    otherwise, it is not recommended to configure this node*/
                    "addUser"=> false,
                    /*optional, boolean type, whether to add the person if the person information
                    being edited does not exist: "false"-no (if the device has checked that the
                    person information being edited does not exist, the failure response message
                    will be returned along with the error code), "true"-yes (if the device has
                    checked that the person information being edited does not exist, the success
                    response message will be returned, and the person will be added). If this node
                    is not configured, the person will not be added by default*/
                    /*"dynamicCode": "123456",
                    /*optional, string, dynamic permission code, this node is write-only*/
                    /*"callNumbers": ["","",""],
                    /*optional, string type, room No. list to be called, by default, its format is
                    X-X-X-X (e.g., 1-1-1-401), which is extended from roomNumber; for standard SIP,
                    it can be the SIP number*/
                    /*"floorNumbers": [ , ],
                    /*optional, integer type, floor No. list, which is extended from floorNumber*/
                    /*"numOfFace": ,
                    /*optional, read-only, number of linked face pictures. If this field is not
                    returned, it indicates that this function is not supported*/
                    /*"numOfFP": ,
                    /*optional, read-only, number of linked fingerprints. If this field is not
                    returned, it indicates that this function is not supported*/
                    /*"numOfCard": ,
                    /*optional, read-only, number of linked cards. If this field is not returned,
                    it indicates that this function is not supported*/
                    /*optional gender: merged below only when request includes a non-empty value
                    ("male", "female", "unknown" per device API)*/
                    /*"PersonInfoExtends":[{
                        /*optional, array of object, extended fields for the additional person
                        information. This node is used to configure the extended person information
                        displayed on the device's UI. For MinMoe series facial recognition terminals,
                        currently only one value node can be supported for displaying the employee No.
                        and the node id is not supported*/
                        /*"id":1,
                        /*optional, int, extended ID of the additional person information, value range:
                        [1,32]. It corresponds to the id in the message of the request URI /ISAPI/
                        AccessControl/personInfoExtendName?format=json and is used to link the value of
                        the node value and its name (the node name in the message of the request URI /
                        ISAPI/AccessControl/personInfoExtendName?format=json). If the node id does not
                        exist, the ID will start from 1 by default according to the array order*/
                        /*"value":""
                        /*optional, string, extended content of the additional person information*/
                    /*}],
                    "operateType": "byTerminal",
                    /*optional, string, operation type: "byTerminal"-by terminal*/
                    /*"terminalNoList": [1],
                    /*optional, array, terminal ID list, this node is required when operation type
                    is "byTerminal"; currently, only one terminal is supported*/
                    /*"groupId":1,
                    /*optional, int, department No. of local time and attendance*/
                    /*"localAtndPlanTemplateId":1
                    /*optional, int, schedule template of local time and attendance. If this node
                    exist, it indicates that there are shift schedule settings by individual. If id
                    is 0, it indicates canceling the shift schedule of the person*/
                ]
            ];
            if ($gender !== null && $gender !== '') {
                $data['UserInfo']['gender'] = $gender;
            }

            foreach ($machineDetails as $value) {
                $iP = $value->agd_machine_ip;
                $username = $value->agd_machine_username;
                $password = $value->agd_machine_pwd;
                // dd( $iP, $username , $password);
                try {
                    // Attempt to make the HTTP request with a timeout
                    $response[$iP] = Http::withDigestAuth($username, $password)
                        ->timeout(5) // Set timeout to 15 seconds
                        ->withHeaders([
                            'Accept' => 'application/json',
                        ])
                        ->put('http://' . $iP . '/ISAPI/AccessControl/UserInfo/Modify?format=json', $data)
                        ->json();
                } catch (\Illuminate\Http\Client\ConnectionException $e) {
                    // Log the error for debugging
                    \Log::error("Connection error with machine IP {$iP}: " . $e->getMessage());

                    // Add a user-friendly error message
                    $response[$iP] = [
                        'machine_ip' => $iP,
                        'status' => 'error',
                        'subStatusCode' => 'fail',
                        'message' => "Failed to connect to machine at IP {$iP}. Please check the network connection or machine configuration.",
                    ];
                } catch (\Illuminate\Http\Client\RequestException $e) {
                    // Handle request-specific errors, such as timeouts
                    \Log::error("Request error with machine IP {$iP}: " . $e->getMessage());

                    $response[$iP] = [
                        'machine_ip' => $iP,
                        'status' => 'error',
                        'subStatusCode' => 'fail',
                        'message' => "A request error occurred with machine at IP {$iP}. Timeout or invalid response.",
                    ];
                } catch (\Exception $e) {
                    // Catch any other exceptions
                    \Log::error("Error with machine IP {$iP}: " . $e->getMessage());

                    $response[$iP] = [
                        'machine_ip' => $iP,
                        'status' => 'error',
                        'subStatusCode' => 'fail',
                        'message' => "An unexpected error occurred with machine at IP {$iP}: " . $e->getMessage(),
                    ];
                }
            }
            // return $response;
            // Return the response
            return response()->json([
                'success' => true,
                'results' => $response,
            ]);
        }
        public function addStudent(Request $request){
            // dd($request->all());
            // Extract parameters from the request body
            $enrollment = $request->input('enrollment');
            $name = $request->input('name');
            $gender = $request->input('gender');
            $groupName = $request->input('groupName');
            $status = $request->input('status');
            $start = $request->input('start');
            $end = $request->input('end'); // Optional
            $response = []; // Initialize response array
             // Validate input
             if (!$enrollment || !$groupName || !$status || !$start || !$gender || !$name) {
                return response()->json([
                    'success' => false,
                    'message' => 'Missing required fields.',
                    'results' => $response,
                ], 400);
            }
            $machineDetails= $this->getUserACLGroup($enrollment, $groupName);
            // dd(sizeof($machineDetails));
            if(sizeof($machineDetails) == 0){
                return response()->json([
                    'success' => false,
                    'message' => $groupName.' group not assigned.',
                    'results' => $response,
                ], 400);
            }
            // dd($machineDetails);
            // dd($enrollment, $groupName, $status, $start, $end);
            $data = [
                "UserInfo"=>[
                    // "employeeNo"=> $this->toMachineFormat($request->userId_enrollment),
                    "employeeNo"=> strtoupper(str_replace('-', '', $enrollment)),
                    /*required, string, employee No. (person ID)*/
                     "name"=>$name,
                    /*optional, string, person name*/
                    "userType"=>($status == 'Active') ? 'normal' : 'blackList',
                    /*required, string, person type=> "normal"-normal person (household), "visitor",
                    "blackList"-person in blocklist*/
                    /*"closeDelayEnabled"=> "false",
                    /*optional, boolean, whether to enable door close delay=> "true"-yes, "false"-
                    no*/
                    "Valid"=>[
                        /*required, parameters of the effective period, the effective period can be a
                        period of time between 1970-01-01 00:00:00 and 2037-12-31 23:59:59*/
                        "enable"=> (empty($end) ? false : true),
                        /*required, boolean, whether to enable the effective period=> "false"-disable,
                        "true"-enable. If this node is set to "false", the effective period is
                        permanent*/
                        "beginTime"=>(!empty($start) ? str_replace(' ', 'T', $start) : ''),
                        // "beginTime"=>"2024-10-10T11:00:00",
                        /*required, start time of the effective period (if timeType does not exist or
                        is "local", the beginTime is the device local time, e.g., 2017-08-01T17:30:08;
                        if timeType is "UTC", the beginTime is UTC time, e.g.,
                        2017-08-01T17:30:08+08:00)*/
                        "endTime"=>(!empty($end) ? str_replace(' ', 'T', $end) : ''),
                        // "endTime"=>"2024-10-11T12:00:00",
                        /*required, end time of the effective period (if timeType does not exist or is
                        "local", the endTime is the device local time, e.g., 2017-08-01T17:30:08; if
                        timeType is "UTC", the endTime is UTC time, e.g., 2017-08-01T17:30:08+08:00)*/
                        "timeType"=>"local",
                    ],
                    /*optional, string, time type: "local"- device local time, "UTC"- UTC time*/
                        // "belongGroup":""
                    /*optional, string, group*/
                        // "password"=>"123",
                    /*optional, string, password*/
                        // "doorRight":"",
                    /*optional, string, No. of the door or lock that has access permission, e.g.,
                    "1,3" indicates having permission to access door (lock) No. 1 and No. 3*/
                        // "RightPlan":[{
                        //     /*optional, door permission schedule (lock permission schedule)*/
                        //     "doorNo": 1,
                        //     /*optional, integer, door No. (lock ID)*/
                        //     "planTemplateNo":""
                        //     /*optional, string, schedule template No.*/
                        // }],
                    // "maxOpenDoorTime"=> 0,
                    /*optional, integer, maximum authentication attempts, 0-unlimited*/
                        /*"openDoorTime": ,
                    /*optional, integer, read-only, authenticated attempts*/
                        /*"roomNumber": ,
                    /*optional, integer, room No.*/
                        /*"floorNumber": ,
                    /*optional, integer, floor No.*/
                        /*"doubleLockRight": ,
                    /*optional, boolean, whether to have the permission to open the double-locked
                    door: "true"-yes, "false"-no*/
                        /*"localUIRight": ,
                    /*optional, boolean, whether to have the permission to access the device local
                    UI: "true"-yes, "false"-no*/
                        /*"localUIUserType":"",
                    /*optional, string, user type of device local UI: "admin" (administrator),
                    "operator", "viewer" (normal user). This node is used to distinguish different
                    users with different operation permissions of device local UI*/
                    // "userVerifyMode"=>"cardAndPw",
                    /*optional, string, person authentication mode: "cardAndPw"-card+password,
                    "card"-card, "cardOrPw"-card or password, "fp"-fingerprint, "fpAndPw"-
                    fingerprint+password, "fpOrCard"-fingerprint or card, "fpAndCard"-fingerprint
                    +card, "fpAndCardAndPw"-fingerprint+card+password, "faceOrFpOrCardOrPw"-face or
                    fingerprint or card or password, "faceAndFp"-face+fingerprint, "faceAndPw"-face
                    +password, "faceAndCard"-face+card, "face"-face, "employeeNoAndPw"-employee No.
                    +password, "fpOrPw"-fingerprint or password, "employeeNoAndFp"-employee No.
                    +fingerprint, "employeeNoAndFpAndPw"-employee No.+fingerprint+password,
                    "faceAndFpAndCard"-face+fingerprint+card, "faceAndPwAndFp"-face+password
                    +fingerprint, "employeeNoAndFace"-employee No.+face, "faceOrfaceAndCard"-face
                    or face+card, "fpOrface"-fingerprint or face, "cardOrfaceOrPw"-card or face or
                    password, "cardOrFace"-card or face, "cardOrFaceOrFp"-card or face or
                    fingerprint, "cardOrFpOrPw"-card or fingerprint or password. The priority of
                    the person authentication mode is higher than that of the card reader
                    authentication mode*/
                    /*"checkUser": ,
                    /*optional, boolean, whether to verify the duplicated person information:
                    "false"-no, "true"-yes. If checkUser is not configured, the device will verify
                    the duplicated person information by default. When there is no person
                    information, you can set checkUser to "false" to speed up data applying;
                    otherwise, it is not recommended to configure this node*/
                    // "addUser"=> false
                    /*optional, boolean type, whether to add the person if the person information
                    being edited does not exist: "false"-no (if the device has checked that the
                    person information being edited does not exist, the failure response message
                    will be returned along with the error code), "true"-yes (if the device has
                    checked that the person information being edited does not exist, the success
                    response message will be returned, and the person will be added). If this node
                    is not configured, the person will not be added by default*/
                    /*"dynamicCode": "123456",
                    /*optional, string, dynamic permission code, this node is write-only*/
                    /*"callNumbers": ["","",""],
                    /*optional, string type, room No. list to be called, by default, its format is
                    X-X-X-X (e.g., 1-1-1-401), which is extended from roomNumber; for standard SIP,
                    it can be the SIP number*/
                    /*"floorNumbers": [ , ],
                    /*optional, integer type, floor No. list, which is extended from floorNumber*/
                    /*"numOfFace": ,
                    /*optional, read-only, number of linked face pictures. If this field is not
                    returned, it indicates that this function is not supported*/
                    /*"numOfFP": ,
                    /*optional, read-only, number of linked fingerprints. If this field is not
                    returned, it indicates that this function is not supported*/
                    /*"numOfCard": ,
                    /*optional, read-only, number of linked cards. If this field is not returned,
                    it indicates that this function is not supported*/
                    "gender"=> $gender,
                    /*optional, string, gender of the person in the face picture: "male", "female",
                    "unknown"*/
                    /*"PersonInfoExtends":[{
                        /*optional, array of object, extended fields for the additional person
                        information. This node is used to configure the extended person information
                        displayed on the device's UI. For MinMoe series facial recognition terminals,
                        currently only one value node can be supported for displaying the employee No.
                        and the node id is not supported*/
                        /*"id":1,
                        /*optional, int, extended ID of the additional person information, value range:
                        [1,32]. It corresponds to the id in the message of the request URI /ISAPI/
                        AccessControl/personInfoExtendName?format=json and is used to link the value of
                        the node value and its name (the node name in the message of the request URI /
                        ISAPI/AccessControl/personInfoExtendName?format=json). If the node id does not
                        exist, the ID will start from 1 by default according to the array order*/
                        /*"value":""
                        /*optional, string, extended content of the additional person information*/
                    /*}],
                    "operateType": "byTerminal",
                    /*optional, string, operation type: "byTerminal"-by terminal*/
                    /*"terminalNoList": [1],
                    /*optional, array, terminal ID list, this node is required when operation type
                    is "byTerminal"; currently, only one terminal is supported*/
                    /*"groupId":1,
                    /*optional, int, department No. of local time and attendance*/
                    /*"localAtndPlanTemplateId":1
                    /*optional, int, schedule template of local time and attendance. If this node
                    exist, it indicates that there are shift schedule settings by individual. If id
                    is 0, it indicates canceling the shift schedule of the person*/
                ]
            ];
            // dd($data);

            foreach ($machineDetails as $value) {
                $iP = $value->agd_machine_ip;
                $username = $value->agd_machine_username;
                $password = $value->agd_machine_pwd;
                // dd( $iP, $username , $password);
                try {
                    // Attempt to make the HTTP request with a timeout
                    $response[] = Http::withDigestAuth($username, $password)
                        ->timeout(15) // Set timeout to 15 seconds
                        ->withHeaders([
                            'Accept' => 'application/json',
                        ])
                        ->put('https://' . $iP . '/ISAPI/AccessControl/UserInfo/Record?format=json', $data)
                        ->json();
                } catch (\Illuminate\Http\Client\ConnectionException $e) {
                    // Log the error for debugging
                    \Log::error("Connection error with machine IP {$iP}: " . $e->getMessage());

                    // Add a user-friendly error message
                    $response[] = [
                        'machine_ip' => $iP,
                        'status' => 'error',
                        'subStatusCode' => 'fail',
                        'message' => "Failed to connect to machine at IP {$iP}. Please check the network connection or machine configuration.",
                    ];
                } catch (\Illuminate\Http\Client\RequestException $e) {
                    // Handle request-specific errors, such as timeouts
                    \Log::error("Request error with machine IP {$iP}: " . $e->getMessage());

                    $response[] = [
                        'machine_ip' => $iP,
                        'status' => 'error',
                        'subStatusCode' => 'fail',
                        'message' => "A request error occurred with machine at IP {$iP}. Timeout or invalid response.",
                    ];
                } catch (\Exception $e) {
                    // Catch any other exceptions
                    \Log::error("Error with machine IP {$iP}: " . $e->getMessage());

                    $response[] = [
                        'machine_ip' => $iP,
                        'status' => 'error',
                        'subStatusCode' => 'fail',
                        'message' => "An unexpected error occurred with machine at IP {$iP}: " . $e->getMessage(),
                    ];
                }
            }
            // Return the response
             // Return the response as JSON
            return response()->json([
                'success' => true,
                'results' => $response,
            ]);
        }
        public function searchStudentInfo(Request $request){
            // dd($request->machineIp);
             $data = [//searching
                "UserInfoSearchCond" => [
                    "searchID" => "0",
                    /*required, string type, search ID, which is used to confirm the upper-level
                    platform or system. If the platform or the system is the same one during two
                    searches, the search history will be saved in the memory to speed up next
                    searching*/
                    "searchResultPosition" => 0,
                    /*required, integer32 type, the start position of the search result in the
                    result list. When there are multiple records and you cannot get all search
                    results at a time, you can search for the records after the specified position
                    next time*/
                    "maxResults" => 1,
                    /*required, integer32 type, maximum number of search results. If maxResults
                    exceeds the range returned by the device capability, the device will return the
                    maximum number of search results according to the device capability and will
                    not return an error message*/
                    "EmployeeNoList" => [
                        [
                            /*optional, person ID list (if this node does not exist or is empty, it
                            indicates searching for all person information)*/
                            "employeeNo" => $this->formatUserId($request->userId)
                            /*optional, string type, employee No. (person ID)*/
                        ]
                    ],
                    "fuzzySearch" => "",
                    /*optional, string, key words for fuzzy search*/
                    "groupIdList" => [1, 2, 3, 4],
                    /*optional, array of int, range of the department No. of local time and
                    attendance*/
                    "arrangeType" => "personal"
                    /*optional, string, shift schedule type: "" (shift schedule by individual).
                    Currently only the shift schedule by individual is supported. If this node
                    exists, it indicates searching for all persons with shift schedule by
                    individual*/
                ]
            ];
            $credentials = $this->getMachineCredentials($request->machine_id);
            $username = $credentials->agd_machine_username;
            $password = $credentials->agd_machine_pwd;
            $iP = $request->machineIp;
            // dd( $iP, $username , $password, 'https://' . $iP . '/ISAPI/AccessControl/UserInfo/Search?format=json');
                try {
                    // Attempt to make the HTTP request with a timeout
                    $response[] = Http::withDigestAuth($username, $password)
                        ->timeout(5) // Set timeout to 15 seconds
                        ->withHeaders([
                            'Accept' => 'application/json',
                        ])
                        ->post('http://' . $iP . '/ISAPI/AccessControl/UserInfo/Search?format=json', $data)
                        ->json();
                } catch (\Illuminate\Http\Client\ConnectionException $e) {
                    // Log the error for debugging
                    \Log::error("Connection error with machine IP {$iP}: " . $e->getMessage());

                    // Add a user-friendly error message
                    $response[] = [
                        'machine_ip' => $iP,
                        'status' => 'error',
                        'responseStatusStrg' => 'fail',
                        'message' => "Failed to connect to machine at IP {$iP}. Please check the network connection or machine configuration.",
                    ];
                } catch (\Illuminate\Http\Client\RequestException $e) {
                    // Handle request-specific errors, such as timeouts
                    \Log::error("Request error with machine IP {$iP}: " . $e->getMessage());

                    $response[] = [
                        'machine_ip' => $iP,
                        'status' => 'error',
                        'responseStatusStrg' => 'fail',
                        'message' => "A request error occurred with machine at IP {$iP}. Timeout or invalid response.",
                    ];
                } catch (\Exception $e) {
                    // Catch any other exceptions
                    \Log::error("Error with machine IP {$iP}: " . $e->getMessage());

                    $response[] = [
                        'machine_ip' => $iP,
                        'status' => 'error',
                        'responseStatusStrg' => 'fail',
                        'message' => "An unexpected error occurred with machine at IP {$iP}: " . $e->getMessage(),
                    ];
                }

            return response()->json([
                'success' => true,
                'results' => $response,
            ]);
        }
        public function getUserACLGroup($enrollementNo, $groupName){
            // dd($enrollementNo, $groupName);
            // $enrollementNo = 'F2020-220';
            if($groupName == 'gate'){
                $sis_group = 'acl_gate';
                $group = 'Gate';
            }else if($groupName == 'gym'){
                $sis_group = 'acl_gym';
                $group = 'Gym';
            }else if($groupName == 'hostel'){
                $sis_group = 'acl_hostel';
                $group = 'Hostel';
            }else if($groupName == 'lab'){
                $sis_group = 'acl_lab';
                $group = 'Lab';
            }else{
                $sis_group = '';
                $group = '';
            }
            if(ctype_digit($enrollementNo)){//its an employee
                $data = DB::table('SISUSERS as sis')
                    ->select(
                        'sis.username',
                        'sis.'.$sis_group,
                    )
                    ->whereRaw('UPPER(sis.HR_BEAMS_ID) = ?', [strtoupper($enrollementNo)]) // Case-insensitive where
                    ->get();
                // dd($data, $sis_group, $group);
                $groupData = DB::table('ACL_GROUPS as ag')
                                ->select(
                                    'agd.AGD_MACHINE_IP',
                                    'agd.AGD_MACHINE_USERNAME',
                                    'agd.AGD_MACHINE_PWD'
                                )
                                ->join('ACL_GROUPS_DETAILS as agd', 'agd.AGD_AG_ID', '=', 'ag.AG_ID')
                                ->where('ag.AG_GROUP_NAME', '=',  $group)
                                ->get();
                return $groupData;
            }else{//its student
                $data = DB::table('SISUSERS as sis')
                        ->select(
                            'sis.username',
                            'sis.'.$sis_group,
                            'sai.SAI_STUDENT_STATUS'
                        )
                        ->leftJoin('STUDENTS_APPLY_INFO as sai', DB::raw('UPPER(sai.SAI_ENROLLMENT_NO)'), '=', DB::raw('UPPER(sis.username)')) // Case-insensitive join
                        ->whereRaw('UPPER(sis.username) = ?', [strtoupper($enrollementNo)]) // Case-insensitive where
                        ->get();
                // dd($data, $sis_group, $group);
                $groupData=[];
                if(!empty($data[0]->$sis_group) && $data[0]->$sis_group != 0 && $data[0]->sai_student_status != 'Left' && $data[0]->sai_student_status != 'Struck-off' && $data[0]->sai_student_status != 'Stuck-Off'){
                    $groupData = DB::table('ACL_GROUPS as ag')
                                ->select(
                                    'agd.AGD_MACHINE_IP',
                                    'agd.AGD_MACHINE_USERNAME',
                                    'agd.AGD_MACHINE_PWD'
                                )
                                ->join('ACL_GROUPS_DETAILS as agd', 'agd.AGD_AG_ID', '=', 'ag.AG_ID')
                                ->where('ag.AG_GROUP_NAME', '=',  $group)
                                ->where('agd.AGD_MACHINE_STATUS', '=',  'E')
                                ->get();

                }
                //dd($groupData);
                return $groupData;
            }

        }
        public function assignGroup(Request $request){
            //  dd($request->all());
            $enrollment = $request->input('enrollment');
            $groupName = $request->input('groupName');
            $status = $request->input('status');
            if($groupName == 'gate'){
                $sis_group = 'acl_gate';
                $group = 'Gate';
            }else if($groupName == 'gym'){
                $sis_group = 'acl_gym';
                $group = 'Gym';
            }else if($groupName == 'hostel'){
                $sis_group = 'acl_hostel';
                $group = 'Hostel';
            }else if($groupName == 'lab'){
                $sis_group = 'acl_lab';
                $group = 'Lab';
            }else{
                $sis_group = '';
                $group = '';
            }
            $updatedRows = DB::table('SISUSERS')
            ->whereRaw('UPPER(username) = ?', [strtoupper($enrollment)]) // Case-insensitive where
            ->update([
                $sis_group => $status,
            ]);
            if ($updatedRows > 0) {
                return response()->json([
                    'success' => true,
                    'message' => "$group assignment updated successfully.",
                ], 200); // HTTP status 200 for success
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'No matching user found, or no changes made.',
                ], 404); // HTTP status 404 for Not Found
            }
        }
        public function getUserGroups(Request $request){
            $sisusername = $request->input('sisusername');

            $groups = DB::table('SISUSERS')
                            ->select(
                                'ACL_GATE',
                                'ACL_GYM',
                                'ACL_HOSTEL',
                                'ACL_LAB'
                            )
            ->whereRaw('LOWER(username) = ?', [strtolower($sisusername)]) // Case-insensitive where
            ->first();
            if (!empty($groups)) {
                return response()->json([
                    'success' => true,
                    'message' => json_encode($groups),
                ], 200); // HTTP status 200 for success
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'No matching user found.',
                ], 404); // HTTP status 404 for Not Found
            }
        }
        public function formatUserId($userId){//f2020-079 into F2020079
            return strtoupper(str_replace('-', '', $userId));
        }
        public function getMachineCredentials($machine_id){
            $machineData = DB::table('ACL_GROUPS_DETAILS')
                            ->where('AGD_ID', $machine_id)
                            ->first();
            return $machineData;

        }
        // public function toMachineFormat($enrollmentNo_cms){
        //     // $enrollmentNo_cms = 'F2024-079';
        //     // F24079

        //     // $enrollmentNo_cms = 'S2024-0220';
        //     // S240220

        //     // $enrollmentNo_cms = 'SUM24-0220';
        //     // SU240220

        //     // $enrollmentNo_cms = '1116';

        //     $enrollmentParts = explode('-', $enrollmentNo_cms);
        //     // dd($enrollmentParts);
        //     $firstThreeChars = substr($enrollmentParts[0], 0, 3);
        //     if ($firstThreeChars !== "SUM") {
        //         if (preg_match('/[a-zA-Z]/', $firstThreeChars)) {//alphabetic character then its enrollment number
        //             $sessionPart = substr_replace($enrollmentParts[0], '', 1, 2);//replace empty at the index(1) and replace 2 characters from that index which is 20
        //             $enrollmentNo_machine = $sessionPart.$enrollmentParts[1];//concatenate the session part (modified as per machine) and roll no part
        //         } else {
        //             $enrollmentNo_machine =  $enrollmentNo_cms;
        //         }
        //     } else {
        //         $sessionPart = substr_replace($enrollmentParts[0], '', 2, 1);//replace empty at the index(2) and replace only 1 character from that index which is M
        //         $enrollmentNo_machine = $sessionPart.$enrollmentParts[1];//concatenate the session part (modified as per machine) and roll no part
        //     }

        //     return $enrollmentNo_machine;
        // }
        // public function toCMSFormat($enrollmentNo_machine){
        //     //  $enrollmentNo_machine = 'F24079';

        //     // $enrollmentNo_machine = 'S240220';

        //     // $enrollmentNo_machine = 'SU240220';

        //     // $enrollmentNo_machine = '1116';
        //     // Check if the string is purely numeric, return as is
        //     if (ctype_digit($enrollmentNo_machine)) {
        //         $enrollmentNo_cms = $enrollmentNo_machine;
        //         return $enrollmentNo_cms;
        //     }

        //     // Extract the first character to identify the type of enrollment
        //     $firstChar = $enrollmentNo_machine[0];

        //     // Handle the 'SU' case (SUM24- format)
        //     if (substr($enrollmentNo_machine, 0, 2) === 'SU') {
        //         // Insert 'M' back at the 3rd position
        //         $sessionPart = 'SUM' . substr($enrollmentNo_machine, 2, 2); // SU24 -> SUM24
        //         $rollNoPart = substr($enrollmentNo_machine, 4); // Remaining roll number
        //         $enrollmentNo_cms = $sessionPart . '-' . $rollNoPart; // Combine session part with roll number
        //     }
        //     // Handle the 'F' and 'S' cases
        //     elseif ($firstChar === 'F' || $firstChar === 'S') {
        //         // Extract the year and roll number
        //         $yearPart = '20' . substr($enrollmentNo_machine, 1, 2); // F24 -> F2024
        //         $rollNoPart = substr($enrollmentNo_machine, 3); // Remaining roll number
        //         $enrollmentNo_cms = $firstChar . $yearPart . '-' . $rollNoPart; // Combine session part with roll number
        //     }
        //     else {
        //         // If the string doesn't match the above patterns, return as is
        //         $enrollmentNo_cms = $enrollmentNo_machine;
        //     }
        //     // dd($enrollmentNo_cms);
        //     return $enrollmentNo_cms;
        // }


}
