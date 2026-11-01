<?php

namespace App\Http\Controllers\Grader\TaskManagement;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Validator;

class TaskManagementController extends Controller
{
    function getTaskCategory(Request $request){
        $sql_tasks = "SELECT
         *
       FROM
           cms_categories tm
        where tm.category_group='task_management'
        AND id   IN (23,24,22 )
       order by tm.id ";
       return $obj_tasks = DB::select($sql_tasks);
    }
    function getPersonalTaskCategory(Request $request){
        $sql_tasks = "SELECT
         *
       FROM
           cms_categories tm
        where tm.category_group='task_management'
        AND id IN (21,25)
       order by tm.id ";
       return $obj_tasks = DB::select($sql_tasks);
    }
    function getImmediateTaskCategory(Request $request){
        $sql_tasks = "SELECT
         *
       FROM
           cms_categories tm
        where tm.category_group='task_management'
        AND id IN (26,27)
       order by tm.id ";
       return $obj_tasks = DB::select($sql_tasks);
    }
   function updateTask(Request $request){
        $id= $request->get('taskId');
        $selValue= $request->get('selValue');
             $sel="update TASK_MANAGEMENT set tm_status = 'Completed' where id =$id";
            $resources = DB::insert($sel);
            return response()->json($resources);
    }
    function updateTaskAll(Request $request){
        $checkboxValues= $request->get('checkboxValues');
        foreach($checkboxValues as $row){
            $sel="update TASK_MANAGEMENT set tm_status = 'Completed' where id =$row";
            $resources = DB::insert($sel);
           
        }
        return response()->json($resources);
        //dd($request->all());
    }
    function updateTaskAllTodo(Request $request){
        $checkboxValues= $request->get('checkboxValues');
        foreach($checkboxValues as $row){
            $sel="update TASK_MANAGEMENT set tm_status = 'Completed' where id =$row";
            $resources = DB::insert($sel);

        }
        return response()->json($resources);
        //dd($request->all());
    }
    function deleteSelected(Request $request){
        $checkboxValues= $request->get('checkboxValues');
        foreach($checkboxValues as $row){
            $sel="DELETE FROM TASK_MANAGEMENT where id =$row";
            $resources = DB::insert($sel);

        }
    }
    function updateTaskAllPersonalTodo(Request $request){
		//dd(1111);
        $checkboxValues= $request->get('checkboxValues');
        foreach($checkboxValues as $row){
            $sel="update TASK_MANAGEMENT set tm_status = 'Completed' where id =$row";
            $resources = DB::insert($sel);

        }
        return response()->json($resources);
        //dd($request->all());
    }
    
    function updateTaskAllImmediateTodo(Request $request){
        $checkboxValues= $request->get('checkboxValues');
        foreach($checkboxValues as $row){
            $sel="update TASK_MANAGEMENT set tm_status = 'Completed' where id =$row";
            $resources = DB::insert($sel);

        }
        return response()->json($resources);
        //dd($request->all());
    }
    function getData(Request $request){
        $getRes = DB::table('task_management')
            ->get();
            $categoryList = array("todo"=>"TO Do ", "task"=>"Task");
            $arr = array(
                'status'                =>  true,
                'data'                  =>  $getRes,
                'category'               =>  $categoryList,
                'message'               =>  'record found!'
            );
             return $arr;

    }
    function showTaskList(Request $request){

        $id = $request->get('id');
        $selectedValue = $request->get('sortId');
        $selectedValue2 = $request->get('chkbox');
        $whereCondition= "";
        if($selectedValue2=="LongTerm"){
            $whereCondition.=  " and TM_TASK_PRIORITY in ('Longterm') ";
        }elseif($selectedValue2=="Urgent"){
            $whereCondition.=  " and TM_TASK_PRIORITY in ('Urgent') ";
        // }elseif($selectedValue2=="Urgent,LongTerm"){
        //     echo "No Record found".$selectedValue2;
        //     exit();
        }elseif($selectedValue2==""){
           echo "<center><b>No Record found</b></center>".$selectedValue2;
           exit();
        }
        // the bottom code will work when choose tasky type like MCM,BOG
        if($request->get('task_type')!=""){
            $whereCondition.=  " and tm_project_category=".$request->get('task_type')." ";
        }
        


       // echo $selectedValue;exit();
            $sql_sort= "";
          if ($selectedValue === 'Latest') {
            // Handle "Latest to Oldest" option
            $sql_sort.= " order by tm_sorting_order,tm_entry_datetime desc ";
          }else if ($selectedValue === 'OldestFirst') {
            // Handle "Oldest to Latest" option
            $sql_sort.= " order by tm_entry_datetime asc ";
          }else if ($selectedValue === 'School') {
            // Handle "Oldest to Latest" option
            $sql_sort.= "order by school_id asc";
          }else if ($selectedValue === 'Department') {
            // Handle "Oldest to Latest" option
            $sql_sort.= "order by dept_id asc";
          }else if($selectedValue === 'User'){
            $sql_sort.= "order by ee_to.emp_f_name,ee_to.employee_id asc";
          }else{
            //$sql_sort.= "order by task_management.tm_sorting_order asc";
          }
          
          $sql = " SELECT
          category_title,
          (select count(*) from messages_bnu 
          where parent_message_id = task_management.id
          and type='task_management'
          AND (seen_record NOT LIKE '%".auth()->user()->employee_id."%' OR seen_record IS NULL)
          ) as total_message,
          tm_title,
          TM_DESCRIPTION,
          TM_COMPLETION_DATE,
          tm_completion_date as convert_tm_completion_date,
          TM_WORKING_STATUS,
          TM_STATUS,
          TM_ENTRY_DATETIME,
          TM_ENTRY_USER,
          TM_CLIENT_NAME,
          task_management.ID,
          TASK_CLIENT_ID,
          TASK_CATEGORY_ID,
          TM_TASK_PRIORITY,
          task_management.CREATED_AT,
          task_management.UPDATED_AT,
          tm_project_category,
          tm_sorting_order,
          TASK_TYPE,
          tm_no_date,
          TASK_REPORT_TIME,
          ee_sender.prefix||' '||ee_sender.emp_f_name||' '||ee_sender.emp_l_name as task_sender_name,
          ee_to.prefix||' '||ee_to.emp_f_name||' '||ee_to.emp_l_name as task_to_name,school_code,dept_name,
            (
                SELECT
                    COUNT(*)
                FROM
                    task_management
                WHERE
                        tm_status NOT IN (
                            'Completed','Draft'
                        )
                    AND
                        task_type = 'Task'
                    AND (
                            tm_entry_user = '".auth()->user()->employee_id."'
                        OR
                            task_client_id = '".auth()->user()->employee_id."'
                    )
            ) AS total_task
    FROM
        task_management
        left join CMS_CATEGORIES on CMS_CATEGORIES.id = task_management.TASK_CATEGORY_ID 
        left join emp_employees ee_sender on ee_sender.employee_id = tm_entry_user 
        left join emp_employees ee_to on ee_to.employee_id = TASK_CLIENT_ID 
        LEFT JOIN schools ON ee_to.emp_school_id = school_id
        LEFT JOIN departments ON ee_to.emp_dept_id = dept_id
    where 1=1";
        if($id == 'Completed'){
            $sql.= " and  tm_status='$id' and tm_status in ('Completed')
                and task_type='".$request->input('type')."'";
        }elseif($id == 'AssignedOthers'){
            $sql.= " and tm_entry_user = '".auth()->user()->employee_id."'
                and   task_client_id != '".auth()->user()->employee_id."'
                and tm_status not in ('Completed','Draft')
                and task_type='".$request->input('type')."' ";
        }elseif($id == 'AssignedMe'){
            $sql.= " and  tm_entry_user != '".auth()->user()->employee_id."'
                and   task_client_id = '".auth()->user()->employee_id."'
                and tm_status not in ('Completed','Draft')
                and task_type='".$request->input('type')."' ";
        }elseif($id == 'Draft'){
            $sql.= "and  tm_entry_user = '".auth()->user()->employee_id."'                    
                and tm_status in ('Draft')
                and tm_title is not null
                and task_type='".$request->input('type')."' ";
        }elseif($id == 'All'){
            $sql.= " and tm_status not in ('Completed','Draft')
                and task_type='".$request->input('type')."' ";                
        }else{
            $sql.= "and task_category_id=".$request->input('id')."
                    and tm_status in ('Pending')                                       
                    and task_type='".$request->input('type')."' ";
        }
        $sql.=  "and (
                        tm_entry_user = '".auth()->user()->employee_id."'
                        or
                        task_client_id = '".auth()->user()->employee_id."'
                       ) ";
        $sql.= $whereCondition;
        $sql.= $sql_sort;
        //echo $sql;
        //echo $sql;exit();
        $rec = DB::select($sql);
        $category = $this->getTaskCategory(request());
        return view('grader.TaskManagement.showTaskList', compact('rec','category'));
    }



    function ProjectBoard(Request $request){
        $sql = "select * from task_management order by tm_entry_datetime desc ";
        $rec = DB::select($sql);
        $category = $this->getTaskCategory(request());
        return view('grader.TaskManagement.ProjectBoard', compact('rec','category'));
    }

    function updateDraftStatus(Request $request){
        
        $id = $request->get('id');
        $upd = "update task_management set tm_status = 'Pending' where id = $id ";
        //echo $upd;exit;
        $res = DB::insert($upd);
        $this->notificationToUser($id);
        return response()->json(['message' => 'Task Assign successfully', 'data' => $res]);
    }

    function addDraft(){

        // $insert = "INSERT INTO task_management (tm_status)
        // VALUES ('Draft')";
        // $res = DB::insert($insert);
        $lastInsertId = DB::table('task_management')->insertGetId([
            'tm_status' => 'Draft',
            'task_type' => 'Task',
            'task_category_id' => '13', 
            'tm_entry_user' =>auth()->user()->employee_id
        ]);
    
        // Use the last insert ID as needed
        // ...
    
        return response()->json(['message' => 'Record inserted successfully', 'data' => $lastInsertId]);

    }


    function taskBoard(Request $request){
            $sql="select * from school_department 
                        where emp_type in (3,4,5)
                        and emp_status='E'
                        order by emp_f_name";
            $staffMembers = DB::select($sql);
            $array_walk = "SELECT
            tm.id,
            tm_status,
            tm.tm_title,
            tm_description      AS description,
            tm_completion_date  AS completion_date,
            tm.task_type,
            tm.task_report_time
        FROM
            task_management tm order by tm.tm_title,tm_completion_date";
                    $tasks = DB::select($array_walk);
                    foreach ($tasks as $task) {
                    $sql_r = "SELECT
                    tmr_employee_id  AS employee_id,
                    prefix
                    || ''
                    || emp_f_name
                    || ' '
                    || emp_l_name     AS employee_name,
                    imagename
                    FROM
                    task_management_resouces
                    left join emp_employees ON task_management_resouces.tmr_employee_id = emp_employees.employee_id
                    left join sisusers on emp_ad_username=username
                    WHERE tmr_tm_id = $task->id ";
                    $resources = DB::select($sql_r);

                    $sql_task_list = "select tm_description from task_management_list where parent_task_id = $task->id ";
                    $task_list = DB::select($sql_task_list);
                    $task->task_list = $task_list;
                    $task->resources = $resources;
                    }

                    $sql_sum = "select count(*) as tr,tm_status from task_management
                                group by tm_status";
                    $obj_summary = DB::select($sql_sum);
        return  view('grader.TaskManagement.taskboard',['staffMembers'=>$staffMembers,'tasks'=>$tasks,'obj_summary'=>$obj_summary]);
    }

        function updateTaskStatus(Request $request){
            $id= $request->get('id');
            $selValue= $request->get('selValue');
                 $sel="update TASK_MANAGEMENT set tm_status = '$selValue' where id =$id";
                $resources = DB::insert($sel);
                return response()->json($resources);
        }
        function getTaskDetailsbyid($task_client_id,$task_type){
             $sql_tasks = "SELECT
                tm.id,
                tm_status,
                tm.tm_title,
                tm.tm_client_name,
                tm.task_client_id,
                tm_description      AS description,
                tm_completion_date  AS completion_date,
                tm.task_type,
                tm.task_report_time
            FROM
                task_management tm
            where 1=1
            order by tm.tm_title,tm_completion_date ";

//task_client_id='$task_client_id'
//and tm.tm_status='$task_type'


            return $obj_tasks = DB::select($sql_tasks);
        }

    function getTaskBoard(Request $request){
        //return auth()->user()->employee_id;exit();
       $id= $request->get('id');
       $sql="select * from school_department 
       where emp_type in (3,4,5)
       and emp_status='E'
       order by emp_f_name";
        $staffMembers = DB::select($sql);
       $array_walk = "SELECT
        tm.id,
        tm_status,
        tm.tm_title,
        tm.tm_client_name,
        tm.task_client_id,
        tm_description      AS description,
        tm_completion_date  AS completion_date,
        tm.task_type,
        tm.task_report_time
    FROM
        task_management tm where task_type='$id'
        and tm.id in (
            SELECT to_char(tm.id) as taskid  FROM  task_management tm where tm.tm_entry_user = '".auth()->user()->employee_id."'  and tm.tm_entry_user is not null
                union all
            select to_char(tmr_tm_id) as taskid from task_management_resouces tmr where tmr.tmr_employee_id = '".auth()->user()->employee_id."'  and tmr_tm_id is not null
        )  order by tm.task_client_id,tm_completion_date ";
       // echo $array_walk;exit();
                $tasks = DB::select($array_walk);
                foreach ($tasks as $task) {
                $sql_r = "SELECT
                tmr_employee_id  AS employee_id,
                prefix
                || ''
                || emp_f_name
                || ' '
                || emp_l_name     AS employee_name,
                imagename
                FROM
                task_management_resouces
                left join emp_employees ON task_management_resouces.tmr_employee_id = emp_employees.employee_id
                left join sisusers on emp_ad_username=username
                WHERE tmr_tm_id = $task->id ";
                $resources = DB::select($sql_r);

                $sql_task_list = "select tm_description from task_management_list where parent_task_id = $task->id ";
                $task_list = DB::select($sql_task_list);
                $task->task_list = $task_list;
                $task->resources = $resources;
                }
            return  view('grader.TaskManagement.taskboardtemplate',['tasks'=>$tasks]);
        }
  
    public function fetchTasks(Request $request)
    {
        $array_walk = "select * from TASK_MANAGEMENT where task_type = 'ToDo'  order by TM_ENTRY_DATETIME desc";
        $tasks = DB::select($array_walk);
        // foreach ($tasks as $task) {
        //     $sql_r = "SELECT tmr_employee_id AS employee_id,prefix||''||emp_f_name||' '||emp_l_name AS employee_name
        //     FROM
        //         task_management_resouces
        //         JOIN emp_employees ON task_management_resouces.tmr_employee_id = emp_employees.employee_id
        //     WHERE tmr_tm_id = $task->id ";
        //    $resources = DB::select($sql_r);

        //    $sql_task_list = "select tm_description from task_management_list where parent_task_id = $task->id ";
        //    $task_list = DB::select($sql_task_list);
        //    $task->task_list = $task_list;
        //    $task->resources = $resources;
        // }
        return response()->json($tasks);
    }
    public function fetch_todo_list(Request $request)
    {
         $tasks = DB::select("select * from todo");

         return response()->json($tasks);
    }
    public function index(Request $request){

        $token = Auth::loginUsingId(1);
        return response()->json(['token' => $token, 'name'=>auth()->user()->name], 200);
    }
    public function dashboard(Request $request){
        //dd(auth()->user());
       if((auth()->user()->username == 'moeedyusuf' || auth()->user()->username == 'sumair.shafique') && auth()->user()->group_id == 8){
           $sql="select * from school_department
                   where emp_type in (3,4,5)
                   and emp_status='E'
                   order by emp_f_name";
           $staffMembers = DB::select($sql);
       }else{           
           $sql="select * from school_department
           where emp_status='E'
           and emp_school_id = '".auth()->user()->school_id."'";
           if(auth()->user()->username == 'salman.zafar'){
               $sql .= "and emp_dept_id in (21,42)";
           }else{
               $sql .= "and emp_dept_id = '".auth()->user()->dept_id."'";
           }            
           $sql .= "order by emp_f_name";
           $staffMembers = DB::select($sql);
       }
       $sql_tmc="select * from task_management_category
               where tmc_status='E'";
       $tmcRes = DB::select($sql_tmc);
       return  view('grader.TaskManagement.createtask',['staffMembers'=>$staffMembers,'sql_tmc'=>$tmcRes]);
   }
    public function dashboard_demand(Request $request){
        $sql="select * from school_department 
                        where emp_type in (3,4,5)
                        and emp_status='E'
                        order by emp_f_name";
        $staffMembers = DB::select($sql);
        return  view('grader.TaskManagement.demand',['staffMembers'=>$staffMembers]);
    }
    public function createTask(Request $request){
        $sql="select * from school_department 
                        where emp_type in (3,4,5)
                        and emp_status='E'
                        order by emp_f_name";
        $staffMembers = DB::select($sql);
        return  view('grader.TaskManagement.createtask_app',['staffMembers'=>$staffMembers]);
    }
    
    public function TodoDashboard(Request $request){
        $sql="select * from school_department 
                        where emp_type in (3,4,5)
                        and emp_status='E'
                        order by emp_f_name";
        $staffMembers = DB::select($sql);
        $sql_task = "select * from task_management
                where task_category_id=13
        order by tm_entry_datetime desc ";
        $rec = DB::select($sql_task);
        $category = $this->getTaskCategory(request());
        return view('grader.TaskManagement.createtodo', compact('rec','category','staffMembers'));
      //  return  view('grader.TaskManagement.createtodo',['staffMembers'=>$staffMembers]);
    }
    public function personalTodoDashboard(Request $request){
        $sql="select * from school_department
                        where emp_type in (3,4,5)
                        and emp_status='E'
                        order by emp_f_name";
        $staffMembers = DB::select($sql);
        $sql_task = "select * from task_management
                where task_category_id = 21
        order by tm_entry_datetime desc ";
        $rec = DB::select($sql_task);
        $category = $this->getPersonalTaskCategory(request());
        return view('grader.TaskManagement.personal_todo', compact('rec','category','staffMembers'));
      //  return  view('grader.TaskManagement.createtodo',['staffMembers'=>$staffMembers]);
    }
    public function immediateTodoDashboard(Request $request){
        $sql="select * from school_department
        where emp_type in (3,4,5)
        and emp_status='E'
        order by emp_f_name";
        $staffMembers = DB::select($sql);
        $sql_task = "select * from task_management
        where task_category_id in (26,27)
        order by tm_entry_datetime desc ";
        $rec = DB::select($sql_task);
        $category = $this->getImmediateTaskCategory(request());
        
        return view('grader.TaskManagement.immediate_todo', compact('rec','category','staffMembers'));
        
    }
    public function TodoDashboardMobile(Request $request){
        $sql="select * from school_department 
                        where emp_type in (3,4,5)
                        and emp_status='E'
                        order by emp_f_name";
        $staffMembers = DB::select($sql);
        $sql_task = "select * from task_management
                where task_category_id=13
        order by tm_entry_datetime desc ";
        $rec = DB::select($sql_task);
        $category = $this->getTaskCategory(request());
        return view('grader.TaskManagement.createtodoMobile', compact('rec','category','staffMembers'));
      //  return  view('grader.TaskManagement.createtodo',['staffMembers'=>$staffMembers]);
    }
    // TaskController.php
    public function fetchStaffData(Request $request)
    {
        //return $request->all();
        // Retrieve the selected staff ID from the request
        $selectedValue = $request->input('staffId');
       // $selectedValue = $_POST['staff'];
        $values = explode('|', $selectedValue);
        $selectedId = $values[0];
        $selectedEmployee = $values[1];
        $ordername = $values[2];

        //echo $selectedId;
        if($ordername=="3"){
              $sql = "SELECT * FROM EMP_EMPLOYEES WHERE employee_id = $selectedEmployee ";
        }else{
            $sql = "SELECT * FROM EMP_EMPLOYEES WHERE emp_school_id = $selectedId AND emp_status = 'E' and emp_type in (4,5)";
        }
       // echo  $sql;
        //print_r($selectedId);exit();

        // // Prepare the SQL query based on the selected group ID
        // if ($selectedGroupId === 'E') {
        //     $sql = "SELECT * FROM EMP_EMPLOYEES WHERE employee_id = $selectedId AND emp_status = 'E' and emp_type in (4,5)";
        // } else {
        //     $selectedSchoolId = $values[2]; // Assuming school_id is the third value in the array
        //     $sql = "SELECT * FROM EMP_EMPLOYEES WHERE emp_school_id = $selectedSchoolId AND emp_status = 'E' and emp_type in (4,5)";
        // }

        // Execute the SQL query to fetch the staff data from the database
        $staffData = DB::select($sql);

        // Return the staff data as a JSON response
        return response()->json([
            'data' => $staffData
        ]);
    }
    public function store(Request $request)
    {

        $selectedValue = $request->input('staff');
        $validator = Validator::make($request->all(), [
            'description' => 'required|max:120',
        ]);
        if ($validator->fails()) {
            // echo "!23";exit;
            // return response()->json(['message' => 'Your error message here', 'error_code' => 422], 422);
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $values = explode('|', $selectedValue);
        $selectedId = $values[0];
        $selectedEmployee = $values[1];
        $ordername = $values[2];
        $sendEmail=0;

        if($ordername==1){
        $school_sql  = "SELECT school_code as description FROM schools WHERE school_id = $selectedEmployee ";
        $school_data = DB::select($school_sql);
        $description = $school_data[0]->description;//['description'];
        }
        if($ordername==2){
        $dept_sql  = "SELECT dept_name as description FROM departments WHERE dept_id = $selectedEmployee ";
        $dept_data = DB::select($dept_sql);
        $description = $dept_data[0]->description;;
        }
        if($ordername==3){
        $employee_sql  = "SELECT prefix||' '||emp_f_name||' '||emp_l_name as description FROM emp_employees WHERE employee_id = $selectedEmployee ";
        $employee_data = DB::select($employee_sql);
        $description = $employee_data[0]->description;;
        }
        // dd($request->all());task_status
        $taskResources = request()->input('user_id');
        $task_status = request()->input('task_status');
            //if(!empty($taskResources))
            $updateID=$request->get('edit_tm_id');
            if($updateID!='' and $request->get('btn_status')=='Pending'){
                $sendEmail=1;
            }else if($request->get('btn_status')=='Pending'){
                $sendEmail=1;
            }
            if($request->get('edit_tm_id')){
                
                $insertedId = DB::table('task_management')
                ->where('id', $updateID)
                ->update([
                    'tm_description'        => $request->get('task_title'),
                    'tm_title'              => $request->get('description'),
                    'tm_client_name'        => $description,
                    'task_client_id'        => $selectedEmployee,
                    'task_type'             => 'Task',
                    'tm_no_date'            => $request->get('noTime'),
                    'tm_task_priority'      => $request->get('tasktype'),
                    'tm_status'             => $request->get('btn_status'),//($task_status!='')?$task_status:'Pending',
                    'task_category_id'      => $request->get('task_category_id'),
                    'tm_project_category'   => $request->get('tmc_categ'),
                    'tm_entry_user'         => auth()->user()->employee_id,
                    'task_report_time'      => $request->get('completion_time'),
                    'tm_completion_date'    => $request->get('completion_date')
                     ]);

            }elseif($request->get('btn_status') != 'Draft'){
                $insertedId = DB::table('task_management')->insertGetId([
                    'tm_description'        => $request->get('task_title'),
                    'tm_title'              => $request->get('description'),
                    'tm_client_name'        => $description,
                    'task_client_id'        => $selectedEmployee,
                    'tm_no_date'            => $request->get('noTime'),
                    'task_type'             => 'Task',
                    'tm_task_priority'      =>$request->get('tasktype'),
                    'tm_status'             => ($task_status!='')?$task_status:'Pending',
                    'task_category_id'      => $request->get('task_category_id'),
                    'tm_entry_user'         => auth()->user()->employee_id,
                    'tm_project_category'   => $request->get('tmc_categ'),
                    'task_report_time'      => $request->get('completion_time'),
                    'tm_completion_date'    => $request->get('completion_date')
                ]);
                 if($sendEmail){
                     $this->notificationToUser($insertedId);
                 }
                return response()->json(['status'=>true,'data' => $insertedId,'message'=>'Task Assigned Successfully, Please go to Assigned Task Tab']);
                // foreach(request()->input('user_id') as $key){
                // // $sql = "INSERT INTO `task_management` (`description`, `user_id`, `completion_date`, `created_at`, `updated_at`) VALUES ('task done', '55', NULL, '2023-06-20 16:19:32', '2023-06-27 16:19:32')";
                //     $sql="INSERT INTO TASK_MANAGEMENT_RESOUCES (tmr_tm_id,tmr_employee_id ) VALUES ($insertedId,$key)";
                //     DB::insert($sql);
                // };
            }else{
               
                $insertedId = DB::table('task_management')->insertGetId([
                    'tm_description'        => $request->get('task_title'),
                    'tm_title'              => $request->get('description'),
                    'tm_client_name'        => $description,
                    'task_client_id'        => $selectedEmployee,
                    'tm_no_date'            => $request->get('noTime'),
                    'task_type'             => 'Task',
                    'tm_task_priority'      => $request->get('tasktype'),
                    'tm_status'             => ($task_status!='')?$task_status:'Draft',
                    'task_category_id'      => $request->get('task_category_id'),
                    'tm_project_category'   => $request->get('tmc_categ'),
                    'tm_entry_user'         => auth()->user()->employee_id,
                    'task_report_time'      => $request->get('completion_time'),
                    'tm_completion_date'    => $request->get('completion_date')
                ]);
                 if($sendEmail){
                     $this->notificationToUser($insertedId);
                 }
                return response()->json(['status'=>true,'data' => $insertedId,'message'=>'Task Drafted Successfully, Please go to Draft Tab']);
                // return response()->json(['status'=>true,'data' => $insertedId,'message'=>'Task drafted Successfully']);
            }



        // $taskPointDesc = request()->input('task_point_desc');
        // if(!empty($taskPointDesc)) {
        //         $total_rows = count(request()->input('task_point_desc'));
        //         for($desc_index=0;$desc_index<$total_rows;$desc_index++){
        //             //echo request()->input('task_point_desc')[$desc_index];
        //             // echo request()->input('task_point_date')[$desc_index];
        //             // echo request()->input('task_point_time')[$desc_index];
        //             $sql_desc="insert into task_management_list
        //                 (
        //                 parent_task_id
        //                 ,tm_description
        //                 ,task_reporting_date
        //                 ,task_reporting_time
        //                 ) values
        //                 (
        //                     $insertedId
        //                     ,'".request()->input('task_point_desc')[$desc_index]."'
        //                     ,'".request()->input('task_point_date')[$desc_index]."'
        //                     ,'".request()->input('task_point_time')[$desc_index]."'
        //                 )";
        //             DB::insert($sql_desc);

        //         }
        // }
         if($sendEmail){
             $this->notificationToUser($insertedId);
         }

        // if($insertedId && $request->get('btn_status') == 'Draft'){
            
        // }
     
        exit();



    }

    function tmsortOrder(Request $request){
        $id = $request->get('id');
        // $sortOrder = $request->get('sortOrder');
        $cp = $request->get('currentOrder');        
        $np = $request->get('newSortOrder'); 


        // echo $cp.'____'.$np;

        if($cp > $np || $cp==""){
            $updSort = "update task_management set 
                tm_sorting_order = tm_sorting_order+1       
                where 1=1";
            if(isset($cp) && $cp!=""){
                $updSort.=" and tm_sorting_order < $cp ";
            }    
            $updSort.=" and tm_sorting_order >= $np 
                AND
                tm_status NOT IN (
                    'Completed','Draft'
                )
                AND
                    task_type = 'Task'
                AND (
                    tm_entry_user = '".auth()->user()->employee_id."'
                OR
                    task_client_id = '".auth()->user()->employee_id."')";

             //echo $updSort."____"; 
            $resupdSort= DB::insert($updSort);

            $updSortCp = "update task_management set 
            tm_sorting_order = $np      
            where id = $id";

            //echo $updSortCp; 
            $resupdSortCp= DB::insert($updSortCp);


        }
        if($cp < $np && $cp!=''){

            $updSortCp = "update task_management set 
            tm_sorting_order = $np      
            where id = $id";

            //echo $updSortCp."_____"; 
            $resupdSortCp= DB::insert($updSortCp);
            
            $updSort = "UPDATE task_management
            SET
                tm_sorting_order = tm_sorting_order - 1
            WHERE
                tm_sorting_order <= $np
            AND
                tm_sorting_order >= $cp
                and id not in $id
                AND
                tm_status NOT IN (
                    'Completed','Draft'
                )
                AND
                    task_type = 'Task'
                AND (
                    tm_entry_user = '".auth()->user()->employee_id."'
                OR
                    task_client_id = '".auth()->user()->employee_id."')";

            //echo $updSort; 
            $resupdSort= DB::insert($updSort);

            // $updSortCp = "update task_management set 
            // tm_sorting_order = $np      
            // where id = $id";

            // // echo $updSortCp; 
            // $resupdSortCp= DB::insert($updSortCp);


        }
        return response()->json(['status'=>true,'data' => $resupdSort,'message'=>'Task Sorted Successfully']);
        
        
        
        
        
        // exit;
        // $updSort = "update task_management set 
        //         tm_sorting_order = $sortOrder       
        //         where id = $id";
        // $resupdSort= DB::insert($updSort);
       
    }

    function notificationToUser($id){
        //dd($id);
        $procedureName = 'task_notification_user';
        $bindings = [
             'task_id'  => $id,
         ];
        return $result = DB::executeProcedure($procedureName, $bindings);
        
    }

    public function updateTaskRow(Request $request){
        //dd($request->all());
        $selectedValue = $request->input('staff');
        $values = explode('|', $selectedValue);
        $selectedId = $values[0];
        $selectedEmployee = $values[1];
        $ordername = $values[2];
       
        $upd = "update task_management set 
                tm_title              = '".$request->get('description')."',
                task_client_id        = $selectedEmployee,
                task_report_time      = '".$request->get('completion_time')."',
                tm_completion_date    = '".$request->get('completion_date')."',
                tm_description        = '".$request->get('task_title')."',
                task_category_id      = 13,
                tm_task_priority      = '".$request->get('tasktype')."'  
                where id = '".$request->get('edit_tm_id')."'";
        $resupd = DB::insert($upd);
        return response()->json(['status'=>true,'data' => $resupd,'message'=>'Task drafted Successfully']);


    }

    public function todostore(Request $request)
    {
         //dd($request->all());
        $selectedValue = $request->input('staff');
        $validator = Validator::make($request->all(), [
            'description' => 'required|min:1|max:2000',
        ]);

        if ($validator->fails()) {
            // echo "!23";exit;
           // return response()->json(['message' => 'Your error message here', 'error_code' => 422], 422);

             return response()->json(['errors' => $validator->errors()], 422);
        }
        if($request->input('tasktype') == "on"){
            $task_category = "Longterm";
        }else{
            $task_category = "Urgent";
        }


        $updateID=$request->get('edit_tm_id');
        if($request->get('edit_tm_id')){
            if($request->get('task_category_id') != 'All'){
                $affected = DB::table('task_management')
                ->where('id', $updateID)
                ->update([
                    'tm_description'        => $request->get('task_title'),
                    'tm_title'              => $request->get('description'),
                    'task_type'             => $request->get('task_type'),
                    'tm_client_name'        => 'bilal work',
                    'tm_task_priority'      =>$request->get('tasktype'),
                    'tm_status'             => 'Pending',
                    'task_category_id'      => $request->get('category_type'),
                    'tm_entry_user'         => auth()->user()->employee_id,
                    'task_report_time'      => $request->get('completion_time'),
                   	'tm_completion_date'    => date('Y-m-d', strtotime($request->get('completion_date')))
                    ]);
            }else{
                $affected = DB::table('task_management')
                ->where('id', $updateID)
                ->update([
                    'tm_description'        => $request->get('task_title'),
                    'tm_title'              => $request->get('description'),
                    'task_type'             => $request->get('task_type'),
                    'tm_client_name'        => 'bilal work',
                    'tm_task_priority'      =>$request->get('tasktype'),
                    'tm_status'             => 'Pending',
                    'task_category_id'      => $request->get('category_type'),
                    'tm_entry_user'         => auth()->user()->employee_id,
                    'task_report_time'      => $request->get('completion_time'),
                    'tm_completion_date'    => date('Y-m-d', strtotime($request->get('completion_date')))
                    ]);
            }
           
                 
        }else{

           // echo $request->get('completion_date')."__should be 23-JAN-01 year-month-day ";exit();


          // $due_date=date('Y-m-d', strtotime($request->get('completion_date')));
           // echo $due_date;exit();
            $data_array = [
                'tm_description' => $request->get('task_title'),
                'tm_title' => $request->get('description'),
                'tm_client_name' => 'bilal work',
                'tm_task_priority' => $request->input('tasktype'),
                'task_client_id' => rand(9999, 999999),
                'task_type' => $request->get('task_type'),
                'tm_status' => 'Pending',
                'tm_entry_user' => $request->get('entry_user_id'),
                'task_category_id' => $request->get('task_category_id'),
            ];
            
            // Add tm_completion_date and task_report_time only if completion_date is not null
            if ($request->has('completion_date') && $request->get('completion_date') !== null) {
                $data_array['tm_completion_date'] = date('Y-m-d', strtotime($request->get('completion_date')));
               // $data_array['tm_completion_date'] = '23-JAN-01';
            }
            if ($request->has('completion_time') && $request->get('completion_time') !== null) {
                $data_array['task_report_time'] = date('Y-m-d', strtotime($request->get('completion_date'))) . ' ' . $request->get('completion_time');
            }
            
            try {
                $insertedId = DB::table('task_management')->insertGetId($data_array);
            } catch (\Exception $e) {
                echo "Error Message: " . $e->getMessage();
                echo "Position: " . $e->getLine();
            }
            
        
        
        echo 'testing';exit();
                    // Get the SQL query string
                    //$sqlQuery = $insertedId->toSql();

                    // Print the SQL query
                   // echo $sqlQuery;
        

    }



        $taskPointDesc = request()->input('task_point_desc');
        if(!empty($taskPointDesc)) {
                $total_rows = count(request()->input('task_point_desc'));
                for($desc_index=0;$desc_index<$total_rows;$desc_index++){
                    //echo request()->input('task_point_desc')[$desc_index];
                    // echo request()->input('task_point_date')[$desc_index];
                    // echo request()->input('task_point_time')[$desc_index];
                    $sql_desc="insert into task_management_list
                        (
                        parent_task_id
                        ,tm_description
                        ,task_reporting_date
                        ,task_reporting_time
                        ) values
                        (
                            $insertedId
                            ,'".request()->input('task_point_desc')[$desc_index]."'
                            ,'".request()->input('task_point_date')[$desc_index]."'
                            ,'".request()->input('task_point_time')[$desc_index]."'
                        )";
                        echo $sql_desc;
                    DB::insert($sql_desc);

                }
            }
        exit();
    }


    public function reg(Request $request)
    {
       // echo 'love';exit();
//        print_r($request->all());exit();

        $userdata = $request->input();

        $rules = [
            "users.*.email" => "required|uniaue:users"
        ];

        $validator = Validator::make($userdata,$rules);

        if($validator->fails()){
            return response()->json($validator->errors(),422);
        }


        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'api_token' => Str::random(40),
            'password' => bcrypt($request->password),
        ]);

        $token = $user->createToken('lovepak')->accessToken;


        return response()->json(['token' => $token, 'name'=>$user->name ], 200);
    }

    public function deleteTask(Request $request){


        //dd($request->all());
        $taskId = $request->get('id');
        $del = DB::table('task_management')
        ->where('id', $taskId)
        ->delete();
        return response()->json(['status'=>true,'data' => $del,'message'=>'Task Deleted Successfully']);

    }
    public function rollBack(Request $request){
        
            $taskId = $request->get('id');
            $upd = "update task_management set tm_status='Pending' where id = $taskId";
            $res = DB::insert($upd);
            return response()->json(['status'=>true,'data' => $upd,'message'=>'Task Rolled Back Successfully']);


    }
    public function editTask(Request $request){

        $taskId = $request->get('id');
        $sel = "select * from task_management where id = $taskId";
        $res = DB::select($sel);
        return response()->json(['status'=>true,'data' => $res,'message'=>'Task Edit']);

    }
    public function secure(Request $request){
        $user = User::where('api_token', $request->all())->first();
        print_r($user);

     //  $user = User::where('email', $email)->first();




        $header = $request->header('Authorization');
       //echo $header;exit();
        if(empty($header)){
            return response()->json(['token' => 'header is empty', 'satus'=>false], 200);
        }else{
            if($header=="eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6Im1uYSIsImlhdCI6MTUxNjIzOTAyMn0.4OwfCLcwE1aEAHaxot_PeVlZy0iZCDvxcQ38hdQ35cg"){
        $token = Auth::loginUsingId(1);
        $tokenParts = explode(".", $header);
        return response()->json(['token' =>base64_decode($tokenParts[1]), 'name'=>auth()->user()->name], 200);
            }else{
                return response()->json(['token' => 'Authrorization is wrong', 'satus'=>false], 200);
            }
        }
    }
    public function loginme(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required|min:6'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => "Errors"], 403);
        }

        $data = [
            'email' => $request->email,'password' => $request->password
        ];


      //  return auth();
        if (auth()->attempt($data)) {
           // $token =  $request->session()->regenerate();
          //  echo 'ok';

           $token = Auth::loginUsingId(1);// auth::id();//user()->id; // auth()->user();//->user()->createToken($request->email)->accessToken;



            return response()->json(['token' => $token, 'name'=>auth()->user()->name], 200);
        } else {
            return response()->json([
                'errors' => "Something went wrong"
            ], 401);
        }
    }


    public function taskBoard2(){

        $sql="select * from school_department order by ordername asc";
        $staffMembers = DB::select($sql);
        $array_walk = "SELECT
        tm.id,
        tm.tm_title,
        tm_description      AS description,
        tm_completion_date  AS completion_date,
        tm.task_type,
        tm.task_report_time
    FROM
        task_management tm order by tm.tm_title,tm_completion_date";
$tasks = DB::select($array_walk);
foreach ($tasks as $task) {
$sql_r = "SELECT
tmr_employee_id  AS employee_id,
prefix
|| ''
|| emp_f_name
|| ' '
|| emp_l_name     AS employee_name,
imagename
FROM
task_management_resouces
left join emp_employees ON task_management_resouces.tmr_employee_id = emp_employees.employee_id
left join sisusers on emp_ad_username=username
WHERE tmr_tm_id = $task->id ";
$resources = DB::select($sql_r);

$sql_task_list = "select tm_description from task_management_list where parent_task_id = $task->id ";
$task_list = DB::select($sql_task_list);
$task->task_list = $task_list;
$task->resources = $resources;
}


        return  view('grader.TaskManagement.taskboard2',['staffMembers'=>$staffMembers,'tasks'=>$tasks]);

    }

      function SendMsg(Request $request){

   // Insert the form data into the admission_form table
   $sql = "SELECT
                *
            FROM
                emp_employees
                left join task_management on employee_id = tm_entry_user or employee_id = task_management.TASK_CLIENT_ID
            WHERE
                1 = 1
                and task_management.id = '".$request->input('taskIdInput')."'";
                if($request->input('to_user_id') != 'groupMessage'){
                    $sql .= "and employee_id = '".$request->input('to_user_id')."'";
                }
    $res = DB::select($sql);
    $toUserEmail = $res[0]->emp_email;
    $empName     = $res[0]->prefix.' '.$res[0]->emp_f_name.' '.$res[0]->emp_l_name;
    $tmTitle     = $res[0]->tm_title;
    $entryData = [
        'message' => $request->input('messageTextarea'),
        'parent_message_id' => $request->input('taskIdInput'),
        'entry_user_id' => auth()->user()->employee_id,
        'type' => "task_management",
        'created_at' => now() // Add current timestamp
    ];

            DB::table('messages_bnu')->insert($entryData);
            $to = $toUserEmail;
            $subject = $tmTitle;
            $message =  $request->input('messageTextarea').' . Sent By: '. auth()->user()->full_name;
            // $message =  $request->input('messageTextarea');

            if($request->input('to_user_id') == 'groupMessage'){
                $sql = "SELECT
                            emp_email
                        FROM
                            task_management_resouces
                            left join emp_employees on employee_id = tmr_employee_id
                        WHERE
                            tmr_tm_id = '".$request->input('taskIdInput')."'";
                            // echo $sql;exit;
                $res = DB::select($sql);
                foreach($res as $row){
                    $TogroupEmail = $row->emp_email;
                    $this->send_mail($TogroupEmail,$subject,$message,'Group Task Message');
                }
                $sqlSentUserEmail = "SELECT emp_email FROM task_management
                                    left join emp_employees on employee_id = tm_entry_user 
                                    WHERE id = '".$request->input('taskIdInput')."'";
                $resSentUserEmail = DB::select($sqlSentUserEmail);
                //echo $sqlSentUserEmail;exit;
                foreach($resSentUserEmail as $row){
                    $TogroupEmail = $row->emp_email;
                    $this->send_mail($TogroupEmail,$subject,$message,'Group Task Message');
                }
    
            }else{
                $this->send_mail($to,$subject,$message,'Task Message');
            }
            return response()->json(['status'=>true,
                'data' => 'Message send successfully'
            ]);






        try {
            DB::beginTransaction();

            $validatedData = $request->validate([
                'messageTextarea' => 'required',
                // 'student-photo' => 'required|image',
            ], [
                'messageTextarea.required' => 'The messageTextarea field is required.',
            ]);

            // Insert the form data into the admission_form table
            $entryData = [
                'message' => $request->input('messageTextarea'),
                'parent_message_id' => $request->input('taskIdInput'),
                'entry_user_id' => auth()->user()->employee_id,
                'created_at' => 'sysdate',
            ];


            DB::table('messages_bnu')->insert($entryData);

            DB::commit();
            return response()->json(['status'=>true,
            'data' => 'Message send successfully'

       ]);

            // The database transaction has been successfully committed
            // Any changes made within the transaction have been saved to the database
        } catch (ValidationException $e) {
            DB::rollBack();

            // Validation failed
            // Handle the validation exception
        } catch (\Exception $e) {
            DB::rollBack();

            // An error occurred
            // Handle the exception
        }


    }

    function send_mail($to,$subject,$message,$title){

        DB::table('testing')
        ->insert(
              ['description' => 'Task Management Send Messages || To: '.$to.' || Subject: '.$subject.' || Message: '.$message]
        );
        $procedurenotification = 'send_mail_one_by_one';
        $bindings_notification = [
        'to'           => $to,
        'subject'      => $subject ,
        'message'      => $message,
        'title'        => $title,
        ];      
        return $result_notification = DB::executeProcedure($procedurenotification, $bindings_notification); 
     }
    public function userMessages(Request $request){
        return view('grader.TaskManagement.sendToMessage');
    }
    public function getUserMessages(Request $request){
        $tmTitle = DB::table('task_management')->where('id',$request->input('taskid'))->value('tm_title');
        // dd($tmTitle);
         //dd($request->all());
        // $sql="select id,message,parent_message_id from messages_bnu where type = 'task_management'";
         $sql = "SELECT
        messages_bnu.id,
        (select  full_name from sisusers where  employee_id= messages_bnu.entry_user_id) as message_to,
        messages_bnu.message,
        messages_bnu.parent_message_id,
        task_management.tm_title,
        task_management.tm_description,
        task_management.tm_client_name,
        messages_bnu.entry_user_id,
        TO_CHAR(messages_bnu.created_at, 'DD-MM-YY')  || ' at ' || TO_CHAR(messages_bnu.created_at, 'HH:MI AM')  AS time_created
        --messages_bnu.*
        FROM
        messages_bnu
        left join task_management on task_management.id = messages_bnu.parent_message_id
        WHERE 1=1
        and  messages_bnu.type = 'task_management'
        and messages_bnu.parent_message_id='".$request->input('taskid')."'
         order by messages_bnu.created_at desc
        ";
        $sqlSelect=DB::select($sql);
        DB::table('messages_bnu')
            ->where('parent_message_id', $request->input('taskid'))
            ->update([
                'seen_record' => DB::raw("CONCAT(seen_record,',". auth()->user()->employee_id."')")
            ]);


        return response()->json(['status' => true, 'data' =>  $sqlSelect, 'tmTitle' =>  $tmTitle]);


    }

}
