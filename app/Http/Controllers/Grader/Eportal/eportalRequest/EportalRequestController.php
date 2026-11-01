<?php

namespace App\Http\Controllers\Grader\Eportal\eportalRequest;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;

class EportalRequestController extends Controller
{
    //
    public function index(Request $request){
        $sql = "SELECT * FROM schools WHERE 1=1 ";
        $userGroupId = auth()->user()->dept_id;
        // dd($userGroupId);
        // $allowedGroupIds = [21, 72, 50, 56, 53, 52, 12, 16, 17, 10, 36, 43, 524, 944, 784, 464, 344];
        $allowedGroupIds = [23,24,122,141,61,41];

        if (!in_array($userGroupId, $allowedGroupIds)) {
            $sql= "select * from departments  where  dept_id in (61,23,24,141)";
        } else {
            $sql .= ' AND school_id NOT IN (8, 9, 10, 11)';
        }
        // echo $sql;exit;
            $schools=DB::select($sql);
        return view('ePortal.ePortalRequest.ePortalRequest',compact('schools'),

    [

        'CountPending'=>$this->countPendingRequest(),
        'CountCompleted'=>$this->countCompletedRequest(),
        'CountDeclined'=>$this->countDeclinedRequest()
    ]

    );

    }

    public function calculateDaysDifference($userInputDate)
    {
        // Parse the user input into a Carbon instance
        $userDate = Carbon::createFromFormat('Y-m-d', $userInputDate);

        // Get the current system date
        $currentDate = Carbon::now();
        // $currentDate = '2024-02-12';

        // Calculate the difference in days
        $daysDifference = $userDate->diffInDays($currentDate);
        // dd($daysDifference);

        return $daysDifference;
    }

    

    public function submitRrequest(Request $request){

        // dd($request->all());
            $requestDays=0;
            $requestDay=0;
            $id = $request->input('requestId');
            $request_to_departments = $request->input('departments');
            $request_to_school_id = $request->input('school_id');
            $request_to_dept = $request->input('empDeptId');
            $request_to = strtoupper($request->input('employees'));
            $StudentDepartmrntId = auth()->user()->dept_id;
            $request_from = strtoupper($request->input('request_from'));
            $message = $request->input('message');
            date_default_timezone_set("Asia/Karachi");
            $currentDateTime = Carbon::now('Asia/Karachi')->format('Y-m-d H:i:s');
            ///days calculate

            $getDays = DB::table('EPORTAL_CATEGORIES_DETAILS')
            ->where('ec_id', $id)
            ->whereNotNull('ecd_completion_days')
            ->get();
            // Convert the updated timestamp to a human-readable date and time
            if ($request->requiredDays=='') {



                if(isset($getDays) && count($getDays) >0){
    
                    $requestDays=$getDays[0]->ecd_completion_days*24;
                    $requestDay=$getDays[0]->ecd_completion_days;
                }
                $timePortion = substr($currentDateTime, 11);
                $dateWithoutTime=$this->getHolidays();
                if($requestDay==1){
                    $getDate=$this->calculateNextWorkingDayTime($currentDateTime,$timePortion,$timePortion);
                    $newDate = $this->removeWeekHolidays($getDate,$dateWithoutTime);
    
                }else{
                    $getDate=$this->calculateNextWorkingDayTime($currentDateTime,$timePortion,$timePortion);
                    $newDate = $this->removeWeekHolidays($getDate,$dateWithoutTime);
                    $dateTime = new \DateTime($newDate);
                    // $requestDay = $requestDay - 1;
                    $requestDay = $requestDay;
                    // Add the specified number of days
                    $interval = \DateInterval::createFromDateString("{$requestDay} days");
                    $dateTime->add($interval);
                    // Format the result as a string
                    $newDatetime = $dateTime->format('Y-m-d H:i:s');
                    $newDate = $this->removeWeekHolidays($newDatetime,$dateWithoutTime);
                }
                    # code...
    }else{
        $timePortion = substr($currentDateTime, 11);
        $getDate=$this->calculateNextWorkingDayTime($request->requiredDays,$timePortion,$timePortion);
        $newDate=$getDate;
        $requestDay=$this->calculateDaysDifference($request->requiredDays);
    }
            $validationRules = [
                'message' => 'required|min:10',
                'employees' => 'required',
            ];
            // Define validation messages
            $validationMessages = [
                'message.required' => 'The Description field is required.',
                'employees.required' => 'Please Select DEAN or HOD.',
            ];
              // Validate the request data
              $validatedData = $request->validate($validationRules, $validationMessages);
            // Check if a file is present in the request
            $fileName='';
            if ($request->hasFile('file')) {
                $validationRules['file'] = 'required|mimes:pdf,jpg,jpeg,png,pptx,ppt,,docx,xlsx,doc,xls|max:10240';// 10240 kilobytes = 10 megabytes

                $validationMessages['file.required'] = 'The file field is required.';
                $validationMessages['file.mimes'] = 'The file must be a PDF, JPG, PNG, or JPEG.';
                $validationMessages['file.max'] = 'The file size must not exceed 10MB.';

                $validatedData = $request->validate($validationRules, $validationMessages);
                // Get the file extension
                $fileExtension = $request->file('file')->getClientOriginalExtension();
                // Generate a unique filename
                $fileName = uniqid().'.'.$fileExtension;

                $fileUpload=$request->file->move(public_path() . '/docs/', $fileName);

                if (!$fileUpload) {
                    return response()->json([
                        'message' => 'File not uploaded!',
                        'status' => false
                    ]);
                    return 'Insert into EPORTAL_REQUEST failed';
                }
            }
  
            // $currentDateTime = now()->format('Y-m-d H:i:s');
            DB::beginTransaction();
    try {

        $pattern = '/[^a-zA-Z0-9]+/';
        $message = preg_replace($pattern, ' ', $message);

        // Trim only the first and last space
        $message = preg_replace('/^\s+/', '', $message);
        $message = preg_replace('/\s+$/', '', $message);

            $insertIntoEePortalRequest = DB::table('EPORTAL_REQUEST')
            ->insert([
                'ec_id' => $id,
                'ER_SAI_ENROLLMENT_NO'  => $request_from,
                'ER_DESCRIPTION'        => $message,
                'ER_ENTRY_USER_ID'      => $request_from,
                'ER_ENTRY_DATETIME'     => $currentDateTime,
                'ER_REQUEST_FILE'       => $fileName,
                'er_to_school_id'       => $request_to_school_id,
                'er_to_dept_id'         => $request_to_dept,
                'er_escalation_time'    =>  $newDate
            ]);

        if (!$insertIntoEePortalRequest) {
            return response()->json([
                'message' => 'Insert into EPORTAL_REQUEST failed',
                'status' => false
            ]);
            return 'Insert into EPORTAL_REQUEST failed';
            // throw new Exception('Insert into EPORTAL_REQUEST failed');

        }
        $latestErId = DB::table('eportal_request')
        ->orderBy('id', 'desc')
        ->value('id');

    // If no records are found, you may want to handle this case accordingly
    if ($latestErId === null) {
        return response()->json([
            'message' => 'No records found in eportal_request',
            'status' => false
        ]);
    }
      // Check if records exist in EPORTAL_CATEGORIES_DETAILS table before inserting
            $recordsExist = DB::table('EPORTAL_CATEGORIES_DETAILS')
            ->where('ec_id', $id)
            ->exists();

            if (!$recordsExist) {
                return response()->json([
                    'message' => 'No records found in EPORTAL_CATEGORIES_DETAILS',
                    'status' => false
                ]);
            }

            $insertIntoEePortalRequestDetailsSql = "
            INSERT INTO eportal_request_details (
                er_id,
                er_ecd_type,
                ER_ECD_APPROVAL_USER,
                er_ecd_status,
                er_ecd_dep,
                er_ecd_category_type,
                er_ecd_completion_days,
                er_ecd_completion_hours,
                er_ecd_instruction_categories,
                er_ecd_input_form_type,
                er_ecd_fee_challan_uplodes,
                er_ecd_fee,
                er_ecd_upload_type,
                erd_sai_enrollment_no,
                erd_status,
                er_ECD_RESOLVING_USER,
                er_ecd_viewer_user
            )
            SELECT
                '".$latestErId."',
                ecd_type,
                ECD_APPROVAL_USER,
                ecd_status,
                ecd_dep_id,
                ecd_category_type,
                ecd_completion_days,
                ecd_completion_hours,
                ecd_instruction_categories,
                ecd_input_form_type,
                ecd_fee_challan_uplodes,
                ecd_fee,
                ecd_upload_type,
                '".$request_from."',
                'Pending',
                ECD_RESOLVING_USER,
                ecd_viewer_user
            FROM
                EPORTAL_CATEGORIES_DETAILS
            WHERE
                ec_id = '". $id."'
        ";
        // echo $insertIntoEePortalRequestDetailsSql;
        $insertIntoEePortalRequestDetails = DB::statement($insertIntoEePortalRequestDetailsSql);

        $insertIntoEePortalRequestDetailsSql2 = "
            INSERT INTO eportal_request_details (
                er_id,
                er_ecd_type,
                ER_ECD_APPROVAL_USER,
                er_ecd_status,
                er_ecd_dep,
                er_ecd_category_type,
                er_ecd_completion_days,
                er_ecd_completion_hours,
                er_ecd_instruction_categories,
                er_ecd_input_form_type,
                er_ecd_fee_challan_uplodes,
                er_ecd_fee,
                er_ecd_upload_type,
                erd_sai_enrollment_no,
                erd_status,
                er_ECD_RESOLVING_USER,
                er_ecd_viewer_user
            )
            SELECT
                '".$latestErId."',
                ecd_type,
                ECD_APPROVAL_USER,
                ecd_status,
                ecd_dep_id,
                ecd_category_type,
                '',
                ecd_completion_hours,
                ecd_instruction_categories,
                ecd_input_form_type,
                ecd_fee_challan_uplodes,
                ecd_fee,
                ecd_upload_type,
                '".$request_from."',
                'Pending',
                '".$request_to."',
                ecd_viewer_user
            FROM
                EPORTAL_CATEGORIES_DETAILS
            WHERE
                ec_id = '". $id."'
        ";
        // echo $insertIntoEePortalRequestDetailsSql;
        $insertIntoEePortalRequestDetails2 = DB::statement($insertIntoEePortalRequestDetailsSql2);

        if (!$insertIntoEePortalRequestDetails2) {
            return response()->json([
                'message' => 'Insert into EPORTAL_REQUEST_DETAILS failed',
                'status' => false
            ]);
            // return 'Insert into EPORTAL_REQUEST_DETAILS failed';
            // throw new Exception('Insert into EPORTAL_REQUEST_DETAILS failed');
        }

        DB::commit();
        // send mail or request
        $to = $request_from;
        $subject = 'MCM Portal Request from '.$request_from;
        $description = 'Description from '.$request_from.': '.$message;
        $this->send_email_to_one($to,$subject,$description);


            return response()->json([
                    'message' => 'Request submitted successfully',
                    'status' => true
                ]);
                } catch (Exception $e) {
                    DB::rollback();

                    return response()->json([
                        'message' => $e->getMessage(),
                        'status' => false
                    ]);
                }

        }


    public function getHolidays() {
        $holidayDates = DB::table('holidays')
        ->pluck('holiday_date')
        ->toArray();

        foreach($holidayDates as $rec){
           $dateTime = new \DateTime($rec);
           // Format the datetime without the time part
           $dateWithoutTime[] = $dateTime->format('Y-m-d');
        }
    return $dateWithoutTime;
    }

    public function removeWeekHolidays($date, $holidays) {

        //$dateTime = new DateTime($date);
        $dateTime = new \DateTime($date);
        // Get the day of the week (0 = Sunday, 1 = Monday, ..., 6 = Saturday)
        $dayOfWeek = (int)$dateTime->format('w');
        // Define an array of weekend days (Saturday and Sunday)
        $weekendDays = [0, 6];
        while (in_array((int)$dateTime->format('w'), $weekendDays) || in_array($dateTime->format('Y-m-d'), $holidays)) {
            // If it's a weekend or holiday, add 1 day to find the next day
            $dateTime->add(new \DateInterval('P1D'));
        }
        // Get the new date as a string
        $newDate = $dateTime->format('Y-m-d H:i:s');

        return $newDate;
    }
    public function calculateNextWorkingDayTime($date, $startTime, $endTime) {
        // Create a Carbon instance for the provided date
        $currentDate = Carbon::parse($date);

        // Set the target time (5 PM)
        $targetTime = '17:30:00';

        // Check if the start time is between 12 AM and 11 AM
        if ($startTime >= '00:00:00' && $startTime < '11:00:00') {
            // If yes, add one extra day
        }
        if ($startTime >= '11:00:00' && $startTime <= '23:59:59') {
            // If yes, add one extra day
            $currentDate->addDay();
        }
        // Set the target time
        $currentDate->setTimeFromTimeString($targetTime);

        // Format the result as a string
        $nextWorkingDayTime = $currentDate->format('Y-m-d H:i:s');

        return $nextWorkingDayTime;
    }

    public function getDept(Request $request){
// dd($request->all());
$dept = DB::table('emp_employees')
    ->select('emp_dept_id')
    ->where('employee_id', $request->empId)
    ->first();
return $dept;

    }

    public function getUserData(Request $request){

    // if ($request->schoolId) {
    //     $departments=DB::select("select * from departments
    //     where dept_school_id='".$request->schoolId."'");
    //     return $departments;
    // }
    $sql = "
        SELECT DISTINCT
            emp_employees.employee_id,
            emp_employees.emp_ad_username AS username,
            emp_employees.emp_email AS user_email,
            (  select dept_name from departments  where dept_id=emp_dept_id ) as emp_dept_name,
            (emp_employees.prefix || ' ' || emp_employees.emp_f_name || ' ' || emp_employees.emp_l_name) AS emp_name
        FROM
            emp_employees
            INNER JOIN sisusers ON emp_employees.employee_id = sisusers.employee_id
        WHERE
            1 = 1
            AND emp_employees.emp_status = 'E'

    ";
    if ($request->schoolId) {
        $sql .= " AND emp_school_id = $request->schoolId";
        if ($request->schoolId == 11) {
            $sql .= " AND emp_dept_id in (41,61,23)";
        }
        if ($request->schoolId != 11) {
            // $sql .= " AND sisusers.group_id IN (21, 72, 50, 56, 53, 52, 12, 16, 17, 10, 36, 43, 524, 944,784,344,464)";
            if ($request->schoolId == 1 ||$request->schoolId == 5 || $request->schoolId == 3 || $request->schoolId == 11 || $request->schoolId == 6) {
                $sql .= " AND emp_type =4";
            }
            else {
                $sql .= " AND emp_type =5";
            }
        }
    }
    if ($request->deptId) {
        $sql .= " AND emp_dept_id = $request->deptId  and emp_employees.emp_type=5";
    }


    // echo $sql;exit;
    $employees=DB::select($sql);
    return $employees;

}

public function pedningRequst(Request $request){

    $username=strtoupper(auth()->user()->username);
    $sql = "select
    eportal_categories.id,
    eportal_request.id as request_id,
    er_description,
    (select school_code from schools where school_id=eportal_request.er_to_school_id) as school_name,
    'DEAN' as emp_name,
    ec_title,eportal_request.er_request_status,
    eportal_categories.ec_department_id
    from eportal_request
    left join eportal_categories on eportal_request.ec_id=eportal_categories.id
    where er_entry_user_id='".$username."'
    and eportal_request.er_request_status='Pending'
    ";
        $res = DB::select($sql);
        return view('ePortal.ePortalRequest.PendingRequest',
        [
            'res'=>$res,
            'CountPending'=>$this->countPendingRequest(),
            'CountCompleted'=>$this->countCompletedRequest(),
            'CountDeclined'=>$this->countDeclinedRequest()
            ]
        );

}

public function completedRequest(Request $request){


    $username=strtoupper(auth()->user()->username);
    $sql = "select
    eportal_categories.id,
    eportal_request. GOOGLE_DRIVE_FILE_NAME,
    eportal_request.GOOGLE_DRIVE_URL,
    eportal_request.id as request_id,
      (select school_code from schools where school_id=eportal_request.er_to_school_id) as school_name,
    (SELECT prefix  || ' ' || emp_f_name || ' ' || emp_l_name  FROM     emp_employees
     WHERE   employee_id=
     ( SELECT
     max(er_ecd_resolving_user)
 FROM
     eportal_request_details
 WHERE
     er_id = eportal_request.id
     and eportal_request_details.er_ecd_completion_days is null
     and eportal_request_details.er_ecd_vc_forward_time is not null)) as emp_name,

    ec_title,eportal_request.er_request_status,
    eportal_request.er_remarks,
    eportal_categories.ec_department_id
    from eportal_request
    left join eportal_categories on eportal_request.ec_id=eportal_categories.id
    where er_entry_user_id='".$username."'
    and eportal_request.er_request_status in ('Declined','Completed')
    ";

    // echo $sql;exit;

    $res = DB::select($sql);

    return view('ePortal.ePortalRequest.CompletedRequest',
    [     'res'=>$res,
    'CountPending'=>$this->countPendingRequest(),
    'CountCompleted'=>$this->countCompletedRequest(),
    'CountDeclined'=>$this->countDeclinedRequest()
    ]);


}



public function countPendingRequest(){
    $username=strtoupper(auth()->user()->username);
    $sqlCountPending="select count(*) as total_pending from eportal_request where er_entry_user_id='".$username."' and eportal_request.er_request_status='Pending'";
    $CountPending = DB::select($sqlCountPending);
    return $CountPending;

}
public function countCompletedRequest(){
    $username=strtoupper(auth()->user()->username);
    $sqlCountCompleted="select count(*) as total_completed from eportal_request where er_entry_user_id='".$username."' and eportal_request.er_request_status in ('Completed')";
   $CountCompleted = DB::select($sqlCountCompleted);
   return $CountCompleted;

}
public function countDeclinedRequest(){
    $username=strtoupper(auth()->user()->username);
    $sqlCountCompleted="select count(*) as total_declined from eportal_request where er_entry_user_id='".$username."' and eportal_request.er_request_status in ('Declined')";
   $CountCompleted = DB::select($sqlCountCompleted);
   return $CountCompleted;

}




public function send_email_to_one($to,$subject,$message){
    $procedurenotification = 'send_mail_one_by_one';
    $bindings_notification = [
        //'to'           => 'muhammad.hamid@bnu.edu.pk',
        'to'           => $to,
        'subject'      => $subject,
        'message'      =>$message,
    ];
    DB::insert("insert into testing (description)  values('$message')");
    // dd( $bindings_notification);
    //execute send_mail_one_by_one('muhammad.hamid@bnu.edu.pk','marks','asd')
 return $result_notification = DB::executeProcedure($procedurenotification, $bindings_notification);

}
}
