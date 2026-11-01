<?php

namespace App\Http\Controllers\Grader\TaskManagement;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{
    public function dashboard(Request $request){
    //    if((auth()->user()->username == 'moeedyusuf' || auth()->user()->username == 'sumair.shafique' ) && auth()->user()->group_id == 8){
    //        $staffMembers = $this->get_drop_down_users(auth()->user()->employee_id);
    //    }else{           
    //        $sql="select * from school_department
    //        where emp_status='E'
    //        and emp_school_id = '".auth()->user()->school_id."'";
    //        if(auth()->user()->username == 'salman.zafar'){
    //            $sql .= "and emp_dept_id in (21,42)";
    //        }else{
    //            $sql .= "and emp_dept_id = '".auth()->user()->dept_id."'";
    //        }            
    //        $sql .= "order by emp_f_name";
    //        $staffMembers = DB::select($sql);
    //    }
   
       $sql_tmc="select * from task_management_category
               where tmc_status='E'";
       $tmcRes = DB::select($sql_tmc);
       $staffMembers = $this->get_drop_down_users(auth()->user()->employee_id);
       return  view('grader.TaskManagement.createtask',['staffMembers'=>$staffMembers,'sql_tmc'=>$tmcRes]);
   }
   function get_drop_down_users($employee_id){
    //dd(auth()->user());
            $sql="SELECT
            employee_id     AS id,
            to_char(employee_id)     AS parent_id,
            prefix ||' '
            || emp_f_name
            || ' '
            || emp_l_name   
            ||' | '||
            school_code
            ||' | '||
            dept_name
            ||'  '||
            case 
                when emp_type=4 then 'Dean'
                when emp_type=5 then 'HOD'
                else '' end as name,
                '3'             AS ordername,
            'EMPLOYEE' as group_name
        FROM
            emp_employees
            left join schools on school_id = emp_school_id
            left join departments on dept_id = emp_employees.emp_dept_id 
            where emp_status='E'";
        if(auth()->user()->username == 'salman.zafar' || auth()->user()->username == 'moeedyusuf' || auth()->user()->username == 'orip' || auth()->user()->username == 'bilal.saleem' || auth()->user()->username == 'sumair.shafique' || auth()->user()->username == 'aizaz.hussain'){
            //$sql .= "and emp_dept_id in (21,42)";
        }else{
            if(auth()->user()->department == 'Information Technology Resource Center'){
                $sql .= "and emp_dept_id in (42,21)";
            }else{
                $sql .= "and emp_dept_id = '".auth()->user()->dept_id."'";
            }
        }   
        $sql .= " UNION ALL      
        SELECT
            ID     AS id,
        tmg_group_name    AS parent_id,
        task_management_group.tmg_group_name as name,  
                'G'             AS ordername,
                tmg_group_name as group_name
        FROM
        task_management_group
            where  task_management_group.tmg_entery_user_id=$employee_id";
        //echo $sql;exit;
        return $obj_users = DB::select($sql);
   }
   public function fetchStaffData(Request $request)
   {
       // Retrieve the selected staff ID from the request
       $selectedValue = $request->input('staffId');
       $values = explode('|', $selectedValue);
       $selectedId          = $values[0];
       
       $selectedEmployee    = $values[1];
       $ordername           = $values[2];

      // $selectArr = explode(',', $selectedId);
        //dd($selectedEmployee);
       $select_list = "prefix,employee_id,emp_f_name,emp_l_name,dept_name,school_code";
       if($ordername=="G"){
             $sql = "          SELECT
                       $select_list
         FROM
             emp_employees
             left join schools on school_id = emp_school_id
             left join departments on dept_id = emp_employees.emp_dept_id
             WHERE 1=1
             AND EMPLOYEE_ID IN (
                   select tmgu_employee_id from task_management_group_user
                   left join task_management_group tmg on tmg.id = TMGU_TMG_GROUP_ID 
                   where TMGU_TMG_GROUP_ID in ($selectedEmployee)
             )
         ";
         //".auth()->user()->employee_id."
       }else{
           $sql = "SELECT $select_list FROM EMP_EMPLOYEES 
           left join schools on school_id = emp_school_id
           left join departments on dept_id = emp_dept_id
            WHERE  emp_status = 'E' and employee_id in ($selectedEmployee)";
       }
       $sql.=" ORDER BY EMP_DEPT_ID "; 
       //echo $sql;exit;
       $staffData = DB::select($sql);
       return $staffData;
   }
   public function getGroupUser(Request $request){

    //dd($request->get('taskid'));
    $sql = " SELECT
    *
FROM
    task_management_resouces 
WHERE
   tmr_tm_id = '".$request->get('taskid')."'";
   return $res=DB::select($sql);



   }
   public function store(Request $request)
   {
    if($request->get('group_task') == 'on'){
        $groupStatus = 1;
    }else{
        $groupStatus = 0;
    }
    //echo "!@3";exit;
   // dd($request->get('tmc_name'));
    
    // foreach($request->get('tmc_name') as $tmc_name_key){  
    //     echo $tmc_name_key;
    // }
    // exit();
       // return $request->all();

     //return $request->input('user')[0];
       $task_status = request()->input('btn_status');
       if($request->get('edit_tm_id')){
        $get_task_user = $request->input('user')[0];
        $insertedId = DB::table('task_management')
        ->where('id', $request->get('edit_tm_id'))
        ->update([
            'tm_description'        => $request->get('task_title'),
            'tm_title'              => $request->get('description'),
            'task_client_id'        => ($groupStatus=='1')?'':$get_task_user,
            'task_type'             => 'Task',
            'tm_no_date'            => $request->get('noTime'),
            'tm_task_priority'      => $request->get('tasktype'),
            'tm_status'             => $request->get('btn_status'),//($task_status!='')?$task_status:'Pending',
            'task_category_id'      => $request->get('task_category_id'),
            'tm_project_category'   => $request->get('tmc_categ'),
            'tm_entry_user'         => auth()->user()->employee_id,
            'task_report_time'      => $request->get('completion_time'),
            'tm_completion_date'    => $request->get('completion_date'),
            'tm_group_task'         => $groupStatus
             ]);
    }else{
        
        // the bottom code will enter resouces against the task
        if($groupStatus == 1){
            $insertedId = DB::table('task_management')->insertGetId([
                'tm_description'        => $request->get('task_title'),
                'tm_title'              => $request->get('description'),
                'task_client_id'        => '',
                'tm_no_date'            => $request->get('noTime'),
                'task_type'             => 'Task',
                'tm_task_priority'      => $request->get('tasktype'),
                'tm_status'             => ($task_status!='')?$task_status:'Draft',
                'task_category_id'      => $request->get('task_category_id'),
                'tm_project_category'   => $request->get('tmc_categ'),
                'tm_entry_user'         => auth()->user()->employee_id,
                'task_report_time'      => $request->get('completion_time'),
                'tm_completion_date'    => $request->get('completion_date'),
                'tm_group_task'         => $groupStatus
            ]);

            
            //dd($request->get('tmc_name'));
            foreach($request->get('tmc_name') as $tmc_name_key){  
                //echo $tmc_name_key."__";
                $task_category_sql = DB::table('TASK_MANAGEMENT_TASK_CATEGORIES')->insert([
                    'tmc_task_id'        => $insertedId,
                    'tmc_category_id'    => $tmc_name_key,
                    'tmc_entry_user_id'  => auth()->user()->employee_id,
                ]);                
            }    

            foreach($request->get('user') as $key){
                //echo $insertedId;
                $sql="INSERT INTO TASK_MANAGEMENT_RESOUCES (tmr_tm_id,tmr_employee_id ) VALUES ($insertedId,$key)";
                //echo $sql;
                DB::insert($sql);
            

            }


            if($task_status == 'Pending'){
                // $this->notificationToUser($insertedId);
            }
        }else{
            foreach($request->get('user') as $key){  
                try {
                $insertedId = DB::table('task_management')->insertGetId([
                    'tm_description'        => $request->get('task_title'),
                    'tm_title'              => $request->get('description'),
                    'task_client_id'        => $key,
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
                //dd($request->get('tmc_name'));
                foreach($request->get('tmc_name') as $tmc_name_key){  
                    //echo $tmc_name_key."__";
                    $task_category_sql = DB::table('TASK_MANAGEMENT_TASK_CATEGORIES')->insert([
                        'tmc_task_id'        => $insertedId,
                        'tmc_category_id'    => $tmc_name_key,
                        'tmc_entry_user_id'  => auth()->user()->employee_id,
                    ]);                
                }    
                if($task_status == 'Pending'){
                    $this->notificationToUser($insertedId);
                }
                } catch (\Exception $e) { 
                    // do something here...
                }
            }
        }   
    
    }
    //echo $task_status;exit;
        if($task_status == 'Pending'){
            return response()->json(['status'=>true,'data' => $insertedId,'message'=>'Task Saved Successfully']);
        }else{
            return response()->json(['status'=>true,'data' => $insertedId,'message'=>'Task Drafted Successfully, Please go to Draft Tab']);
        }
           
       
    }
    function notificationToUser($id){
        $procedureName = 'task_notification_user';
        $bindings = [
             'task_id'  => $id,
         ];
        return $result = DB::executeProcedure($procedureName, $bindings);
    }
    function updateDraftStatus(Request $request){

        $id = $request->get('id');
        $upd = "update task_management set tm_status = 'Pending' where id = $id ";
        $res = DB::insert($upd);
        return response()->json(['message' => 'Task Assign successfully', 'data' => $res]);
    }
    function showTaskList(Request $request){
        //echo "123";exit;
        //return $request->input('task_filter_categories');
 
        //dd(auth()->user());
        // echo $request->input('task_filter_categories'); 
        //        return $sql_category="select * from task_management_task_categories where tmc_category_id in (".$request->input('task_filter_categories').")";
 
         $id = $request->get('id');
         $selectedValue = $request->get('sortId');
         $selectedValue2 = $request->get('chkbox');
         $whereCondition= "";
         if($selectedValue2=="LongTerm"){
             $whereCondition.=  " and TM_TASK_PRIORITY in ('Longterm') ";
         }elseif($selectedValue2=="Urgent"){
             $whereCondition.=  " and TM_TASK_PRIORITY in ('Urgent') ";
         }elseif($selectedValue2==""){
            echo "<center><b>No Record found</b></center>".$selectedValue2;
            exit();
         }
 
         if($request->input('task_filter_categories')!=""){
             $whereCondition.=" and task_management.id in (
                 select tmtc.tmc_task_id from task_management_task_categories tmtc
                 left join task_management tm on tm.id = tmtc.tmc_task_id
                 where tmtc.tmc_category_id in (".$request->input('task_filter_categories').")
               
                 ) 
                 and task_management.id in ( 
                     SELECT to_char(tm.id) as taskid  FROM  task_management tm where tm.tm_entry_user = '".auth()->user()->employee_id."'  or   tm.task_client_id = '".auth()->user()->employee_id."'
                         union all
                     select to_char(tmr_tm_id) as taskid from task_management_resouces tmr where tmr.tmr_employee_id = '".auth()->user()->employee_id."'
                 ) ";
         }
 
         // the bottom code will work when choose tasky type like MCM,BOG
         if($request->get('task_type')!="" and $request->get('task_type')!="undefined"){
             $whereCondition.=  " and tm_project_category=".$request->get('task_type')." ";
         }
         if($request->get('user_id')!=""){
             $selected_user_Value = $request->input('user_id');
             $values = explode('|', $selected_user_Value);
             $selectedId          = $values[0];
             $selectedEmployee    = $values[1];
             $ordername           = $values[2];
             if($selectedEmployee>0){
                 $whereCondition.=  " and task_client_id =".$selectedEmployee." ";
             }         
         }
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
           }else if($selectedValue === 'Deadline'){
             $sql_sort.= "order by tm_completion_date asc";
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
             ) AS total_task,tm_group_task
        FROM
            task_management
            left join CMS_CATEGORIES on CMS_CATEGORIES.id = task_management.TASK_CATEGORY_ID
            left join emp_employees ee_sender on ee_sender.employee_id = tm_entry_user
            left join emp_employees ee_to on ee_to.employee_id = TASK_CLIENT_ID
            LEFT JOIN schools ON ee_to.emp_school_id = school_id
            LEFT JOIN departments ON ee_to.emp_dept_id = dept_id
        
        where TASK_CATEGORY_ID in ('22','23','24','13') ";
         if($id == 'Completed'){
             $sql.= " and  tm_status='$id' and tm_status in ('Completed')
                 and task_type='".$request->input('type')."'";
         }elseif($id == 'AssignedOthers'){
             $sql.= " 
                 --and tm_entry_user = '".auth()->user()->employee_id."'
                -- and   task_client_id != '".auth()->user()->employee_id."'
                 and tm_status not in ('Completed','Draft')
                 and task_type='".$request->input('type')."' ";
         }elseif($id == 'AssignedMe'){
             $sql.= " --and  tm_entry_user != '".auth()->user()->employee_id."'
                 --and   task_client_id = '".auth()->user()->employee_id."'
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
             $sql.= "
                     and task_category_id=".$request->input('id')."
                     and tm_status in ('Pending')
                     and task_type='".$request->input('type')."' ";
                     //and task_category_id=".$request->input('id')."
         }
         // $sql.=  "and (
         //                 tm_entry_user = '".auth()->user()->employee_id."'
         //                 or
         //                 task_client_id = '".auth()->user()->employee_id."'
         //                 OR
         //                 tmr_employee_id = '".auth()->user()->employee_id."'
         //                ) ";
         // $sql .= " ";
         $sql.= $whereCondition;
         $sql.= $sql_sort;
 
         $people = array("822","42","1313","540");
         if(in_array(auth()->user()->employee_id, $people))
         {
         echo $sql;
         }
         //echo $sql_sort;
       // echo $sql;exit();
         $rec = DB::select($sql);
             //------------------------------------------------------------------------------------------------
         array_walk($rec, function ($obj, $index) {
             $sub_category = "select * from task_management_task_categories where tmc_task_id =".$obj->id;
             $obj_category = DB::select($sub_category);
             $obj->task_marked_category = $obj_category;           
         });
         $category = $this->getTaskCategory(request());
 
         return view('grader.TaskManagement.showTaskList', compact('rec','category'));
    }
    function getGroupTaskEmployee($id){

        $sql = "SELECT
                    emp_f_name || ' ' || emp_l_name as tmr_employee_id
                FROM
                    task_management_resouces
                    left join emp_employees on employee_id = tmr_employee_id
                WHERE
                    tmr_tm_id = $id";
       
        return $res = DB::select($sql);

    }
    function showTodoTaskList(Request $request){

        //return $request->input('task_filter_categories');
 
        // echo $request->input('task_filter_categories'); 
        //        return $sql_category="select * from task_management_task_categories where tmc_category_id in (".$request->input('task_filter_categories').")";
 
         $id = $request->get('id');
         $selectedValue = $request->get('sortId');
         $selectedValue2 = $request->get('chkbox');
         $whereCondition= "";
         if($selectedValue2=="LongTerm"){
             $whereCondition.=  " and TM_TASK_PRIORITY in ('Longterm') ";
         }elseif($selectedValue2=="Urgent"){
             $whereCondition.=  " and TM_TASK_PRIORITY in ('Urgent') ";
         }elseif($selectedValue2==""){
            echo "<center><b>No Record found</b></center>".$selectedValue2;
            exit();
         }
 
         if($request->input('task_filter_categories')!=""){
             $whereCondition.=" and task_management.id in (
                 select tmtc.tmc_task_id from task_management_task_categories tmtc
                 left join task_management tm on tm.id = tmtc.tmc_task_id
                 where tmtc.tmc_category_id in (".$request->input('task_filter_categories').")
                 and (tm.tm_entry_user = '".auth()->user()->employee_id."' or tm.task_client_id = '".auth()->user()->employee_id."')
                 ) ";
         }
 
         // the bottom code will work when choose tasky type like MCM,BOG
         if($request->get('task_type')!="" and $request->get('task_type')!="undefined"){
             $whereCondition.=  " and tm_project_category=".$request->get('task_type')." ";
         }
         if($request->get('user_id')!=""){
             $selected_user_Value = $request->input('user_id');
             $values = explode('|', $selected_user_Value);
             $selectedId          = $values[0];
             $selectedEmployee    = $values[1];
             $ordername           = $values[2];
             if($selectedEmployee>0){
                 $whereCondition.=  " and task_client_id =".$selectedEmployee." ";
             }         
         }
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
           }else if($selectedValue === 'Deadline'){
             $sql_sort.= "order by tm_completion_date asc";
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
        where 1=1 and TASK_CATEGORY_ID in ('22','23','24','13') ";
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
             $sql.= "
                     and task_category_id=".$request->input('id')."
                     and tm_status in ('Pending')
                     and task_type='".$request->input('type')."' ";
                     //and task_category_id=".$request->input('id')."
         }
         $sql.=  "and (
                         tm_entry_user = '".auth()->user()->employee_id."'
                         or
                         task_client_id = '".auth()->user()->employee_id."'
                        ) ";
         $sql.= $whereCondition;
         $sql.= $sql_sort;
 
         $people = array("42","1313","540");
         if(in_array(auth()->user()->employee_id, $people))
         {
         echo $sql;
         }
         //echo $sql_sort;
         //echo $sql;exit();
         $rec = DB::select($sql);
             //------------------------------------------------------------------------------------------------
         array_walk($rec, function ($obj, $index) {
             $sub_category = "select * from task_management_task_categories where tmc_task_id =".$obj->id;
             $obj_category = DB::select($sub_category);
             $obj->task_marked_category = $obj_category;           
         });
        //        return view('grader.teacher.course_site',compact('all_classes','co_id','course_criteria'));
         $category = $this->getTaskCategory(request());
         return view('grader.TaskManagement.showTodoTaskList', compact('rec','category'));
    }

    function showPersonalTaskList(Request $request){
        // echo "123";exit;
       //return $request->input('task_filter_categories');

       // echo $request->input('task_filter_categories'); 
       //        return $sql_category="select * from task_management_task_categories where tmc_category_id in (".$request->input('task_filter_categories').")";

        $id = $request->get('id');
        $selectedValue = $request->get('sortId');
        $selectedValue2 = $request->get('chkbox');
        $whereCondition= "";
        if($selectedValue2=="LongTerm"){
            $whereCondition.=  " and TM_TASK_PRIORITY in ('Longterm') ";
        }elseif($selectedValue2=="Urgent"){
            $whereCondition.=  " and TM_TASK_PRIORITY in ('Urgent') ";
        }elseif($selectedValue2==""){
           echo "<center><b>No Record found</b></center>".$selectedValue2;
           exit();
        }

        if($request->input('task_filter_categories')!=""){
            $whereCondition.=" and task_management.id in (
                select tmtc.tmc_task_id from task_management_task_categories tmtc
                left join task_management tm on tm.id = tmtc.tmc_task_id
                where tmtc.tmc_category_id in (".$request->input('task_filter_categories').")
                and (tm.tm_entry_user = '".auth()->user()->employee_id."' or tm.task_client_id = '".auth()->user()->employee_id."')
                ) ";
        }

        // the bottom code will work when choose tasky type like MCM,BOG
        if($request->get('task_type')!="" and $request->get('task_type')!="undefined"){
            $whereCondition.=  " and tm_project_category=".$request->get('task_type')." ";
        }
        if($request->get('user_id')!=""){
            $selected_user_Value = $request->input('user_id');
            $values = explode('|', $selected_user_Value);
            $selectedId          = $values[0];
            $selectedEmployee    = $values[1];
            $ordername           = $values[2];
            if($selectedEmployee>0){
                $whereCondition.=  " and task_client_id =".$selectedEmployee." ";
            }         
        }
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
    where TASK_CATEGORY_ID in ('21','25') ";
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
            $sql.= "
                    and task_category_id=".$request->input('id')."
                    and tm_status in ('Pending')
                    and task_type='".$request->input('type')."' ";
                    //and task_category_id=".$request->input('id')."
        }
        $sql.=  "and (
                        tm_entry_user = '".auth()->user()->employee_id."'
                        or
                        task_client_id = '".auth()->user()->employee_id."'
                       ) ";
        $sql.= $whereCondition;
        $sql.= $sql_sort;

        $people = array("822","42","1313","540");
        if(in_array(auth()->user()->employee_id, $people))
        {
       // echo $sql;
        }
        //echo $sql_sort;
        //echo $sql;exit();
        $rec = DB::select($sql);
            //------------------------------------------------------------------------------------------------
        array_walk($rec, function ($obj, $index) {
            $sub_category = "select * from task_management_task_categories where tmc_task_id =".$obj->id;
            $obj_category = DB::select($sub_category);
            $obj->task_marked_category = $obj_category;           
        });
//        return view('grader.teacher.course_site',compact('all_classes','co_id','course_criteria'));
        $category = $this->getTaskCategory(request());
        return view('grader.TaskManagement.showPersonalTaskList', compact('rec','category'));
    }

    function showImmediateTaskList(Request $request){
        // echo "123";exit;
       //return $request->input('task_filter_categories');

       // echo $request->input('task_filter_categories'); 
       //        return $sql_category="select * from task_management_task_categories where tmc_category_id in (".$request->input('task_filter_categories').")";

        $id = $request->get('id');
        $selectedValue = $request->get('sortId');
        $selectedValue2 = $request->get('chkbox');
        $whereCondition= "";
        if($selectedValue2=="LongTerm"){
            $whereCondition.=  " and TM_TASK_PRIORITY in ('Longterm') ";
        }elseif($selectedValue2=="Urgent"){
            $whereCondition.=  " and TM_TASK_PRIORITY in ('Urgent') ";
        }elseif($selectedValue2==""){
           echo "<center><b>No Record found</b></center>".$selectedValue2;
           exit();
        }

        if($request->input('task_filter_categories')!=""){
            $whereCondition.=" and task_management.id in (
                select tmtc.tmc_task_id from task_management_task_categories tmtc
                left join task_management tm on tm.id = tmtc.tmc_task_id
                where tmtc.tmc_category_id in (".$request->input('task_filter_categories').")
                and (tm.tm_entry_user = '".auth()->user()->employee_id."' or tm.task_client_id = '".auth()->user()->employee_id."')
                ) ";
        }

        // the bottom code will work when choose tasky type like MCM,BOG
        if($request->get('task_type')!="" and $request->get('task_type')!="undefined"){
            $whereCondition.=  " and tm_project_category=".$request->get('task_type')." ";
        }
        if($request->get('user_id')!=""){
            $selected_user_Value = $request->input('user_id');
            $values = explode('|', $selected_user_Value);
            $selectedId          = $values[0];
            $selectedEmployee    = $values[1];
            $ordername           = $values[2];
            if($selectedEmployee>0){
                $whereCondition.=  " and task_client_id =".$selectedEmployee." ";
            }         
        }
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
            where TASK_CATEGORY_ID in ('26','27') ";
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
            $sql.= "
                    and task_category_id=".$request->input('id')."
                    and tm_status in ('Pending')
                    and task_type='".$request->input('type')."' ";
                    //and task_category_id=".$request->input('id')."
        }
        $sql.=  "and (
                        tm_entry_user = '".auth()->user()->employee_id."'
                        or
                        task_client_id = '".auth()->user()->employee_id."'
                       ) ";
        $sql.= $whereCondition;
        $sql.= $sql_sort;

        $people = array("822","42","1313","540");
        if(in_array(auth()->user()->employee_id, $people))
        {
       // echo $sql;
        }
        //echo $sql_sort;
        //echo $sql;exit();
        $rec = DB::select($sql);
            //------------------------------------------------------------------------------------------------
        array_walk($rec, function ($obj, $index) {
            $sub_category = "select * from task_management_task_categories where tmc_task_id =".$obj->id;
            $obj_category = DB::select($sub_category);
            $obj->task_marked_category = $obj_category;           
        });
        // return view('grader.teacher.course_site',compact('all_classes','co_id','course_criteria'));
        $category = $this->getTaskCategory(request());
        return view('grader.TaskManagement.showImmediateTaskList', compact('rec','category'));
    }

    function getTaskCategory(Request $request){
        $sql_tasks = "SELECT
         *
       FROM
           cms_categories tm
        where tm.category_group='task_management'
       order by tm.id ";
       return $obj_tasks = DB::select($sql_tasks);
   }
}
