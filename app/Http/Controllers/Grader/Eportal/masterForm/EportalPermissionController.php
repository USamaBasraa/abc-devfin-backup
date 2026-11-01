<?php
namespace App\Http\Controllers\Grader\Eportal\masterForm;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use DB;

class EportalPermissionController extends Controller
{

    public function showForm()
    {
    // Define an array of options
    $options = [
        'Approval',
        'Resolving Authority',
        'Viewer Authority',
        'Time',
        'Upload File',
        'Instruction',
        'Fee Challan Upload',
    ];
    //categories
    // Fetch data from the eportal_categories table and format it
    // $categoriesData = DB::table('eportal_categories')->select('id', 'EC_TITLE as name')->get();

    $categoriesData = DB::table('eportal_categories')
    ->leftJoin('departments', 'eportal_categories.ec_department_id', '=', 'departments.dept_id')
    ->select('eportal_categories.id', 'eportal_categories.EC_TITLE as name', 'departments.dept_name') // Include more columns as needed
    ->get();



    // dd( $categoriesData);
    // Convert the data to an array
    $categories = $categoriesData->toArray();
    $select_list = "prefix,employee_id as id,emp_f_name || ' ' || emp_l_name as full_name ,emp_dept_id,dept_name,school_code";

    $sql = "SELECT $select_list FROM EMP_EMPLOYEES
           left join schools on school_id = emp_school_id
           left join departments on dept_id = emp_dept_id
            WHERE  emp_status = 'E'  order by employee_id desc " 
            ;

    $users = DB::select($sql);
   // echo $sql;exit;

   $epcd = "SELECT EPORTAL_CATEGORIES_DETAILS.*, EPORTAL_CATEGORIES.EC_TITLE, departments.dept_name
    FROM EPORTAL_CATEGORIES_DETAILS
    LEFT JOIN EPORTAL_CATEGORIES ON EPORTAL_CATEGORIES.id = EPORTAL_CATEGORIES_DETAILS.ec_id
    LEFT JOIN departments ON EPORTAL_CATEGORIES.ec_department_id = departments.dept_id";

    $resEpcd = DB::select($epcd);

    $epcddetails = "SELECT
    empapp.emp_f_name
     || ' '
     || empapp.emp_l_name AS full_name_app,
    eportal_categories_details.*,
        empres.emp_f_name
     || ' '
     || empres.emp_l_name AS full_name_resolving,
        empview.emp_f_name
     || ' '
     || empview.emp_l_name AS full_name_viewer
   
FROM
    eportal_categories_details
    LEFT JOIN emp_employees empapp ON empapp.employee_id = ecd_approval_user
    LEFT JOIN emp_employees empres ON empres.employee_id = ecd_resolving_user
    LEFT JOIN emp_employees empview ON empview.employee_id = ecd_viewer_user";
    //echo $epcddetails;exit;
    $resEpcdetails = DB::select($epcddetails);
    //echo "!@3";exit;
    return view('ePortal.eportal_master_permission', compact( 'categories', 'users' ,'options','resEpcd','resEpcdetails'));

    }
    // storing the information in the eportal_category_detaile
    public function permistionGranted(Request $request){
        //  echo()
        // dd($request->input('user_id'));
        if($request->input('user_id')){
            $userDetail = explode("~", $request->input('user_id'));
            $employee_id = $userDetail[0];
            $emp_dept_id = $userDetail[1];
        }else{
            $employee_id = '';
            $emp_dept_id = '';

        }
        if($request->input('resolving_user')){
            $resolvinguserDetail = explode("~", $request->input('resolving_user'));
            $resolvingemployee_id = $resolvinguserDetail[0];
            $resolvingemp_dept_id = $resolvinguserDetail[1];
        }else{
            $resolvingemployee_id = '';
            $resolvingemp_dept_id = '';

        }
        if($request->input('viewing_user')){
            $viewinguserDetail = explode("~", $request->input('viewing_user'));
            $veiwingemployee_id = $viewinguserDetail[0];
            $veiwinggemp_dept_id = $viewinguserDetail[1];
        }else{
            $veiwingemployee_id = '';
            $veiwinggemp_dept_id = '';
        }
        $categ_id = $request->input('category_id');
        $sql = "select * from EPORTAL_REQUEST where ec_id =  $categ_id and er_request_status = 'Pending'";
        $res = DB::select($sql);

        if(count($res)>0){
            foreach($res as $row){
                $er_id = $row->id;
                $er_roll_no = $row->er_sai_enrollment_no;
                // $approval_user = 
                $ins = "insert into EPORTAL_REQUEST_details (er_id,erd_status,erd_sai_enrollment_no,er_ecd_approval_user,er_ecd_dep,er_ecd_resolving_user,er_ecd_viewer_user,er_ecd_viewer_user_dept) 
                values($er_id,'Pending','$er_roll_no','$employee_id','$emp_dept_id','$resolvingemployee_id','$veiwingemployee_id','$veiwinggemp_dept_id')";
                // echo $ins.'<br>';
                $resIns = DB::insert($ins);
            }
        }
        //echo $sql;exit;
       
        //print_r($request->all());
        $selectedOptionFeeChallan = '';
        $selectedOptionUpload = '';
        if($request->input('select_option') == 'Fee Challan Upload'){
            $selectedOptionFeeChallan = 1;
        }else if ($request->input('select_option') == 'Upload File'){
            $selectedOptionUpload = 1;
        }
        $data = [
            'EC_ID' => $request->input('category_id'),
            'ECD_APPROVAL_USER' => $employee_id,
            'ECD_DEP_ID' => $emp_dept_id,
            'ECD_INSTRUCTION_CATEGORIES' => $request->input('instruction'),
            'ECD_COMPLETION_DAYS' => $request->input('completion_days'),
            'ECD_COMPLETION_HOURS' => $request->input('completion_hours'),
            'ECD_INSTRUCTION_CATEGORIES' => $request->input('userInstruction'),
            'ECD_FEE' => $selectedOptionFeeChallan,
            'ECD_RESOLVING_USER' => $resolvingemployee_id,
            'ECD_VIEWER_USER' => $veiwingemployee_id,
            'ECD_UPLOAD_TYPE' => $selectedOptionUpload
            // Add other columns as needed
        ];

        // Insert data into the EPORTAL_CATEGORIES_DETAILS table
        $res = DB::table('EPORTAL_CATEGORIES_DETAILS')->insert($data);
        return response()->json(['data' => $res]);

    }

    // HOD/Dean Management Methods
    public function showHodDeanForm()
    {
        // Get schools
        $schools = DB::table('schools')->select('school_id as id', 'school_name as name')->get();
        
        // Departments will be loaded via API based on school selection
        
        // Get employees
        $employees = DB::select("
            SELECT employee_id as id, 
                   emp_f_name || ' ' || emp_l_name as full_name,
                   emp_dept_id,
                   dept_name
            FROM EMP_EMPLOYEES
            LEFT JOIN departments ON dept_id = emp_dept_id
            WHERE emp_status = 'E'
            ORDER BY employee_id DESC
        ");
        
        // Get existing HOD/Dean assignments
        $hodDeanList = DB::select("
            SELECT ehd.EHD_ID as ehd_id,
                   ehd.EHD_EMPLOYEE_ID as ehd_employee_id,
                   ehd.EHD_SCHOOL_ID as ehd_school_id,
                   ehd.EHD_DEPARTMENT_ID as ehd_department_id,
                   ehd.EHD_POSITION_TYPE as ehd_position_type,
                   ehd.EHD_CREATED_AT as ehd_created_at,
                   emp.emp_f_name || ' ' || emp.emp_l_name as employee_name,
                   s.school_name,
                   d.dept_name
            FROM EPORTAL_HOD_DEAN_LIST ehd
            LEFT JOIN EMP_EMPLOYEES emp ON ehd.EHD_EMPLOYEE_ID = emp.employee_id
            LEFT JOIN schools s ON ehd.EHD_SCHOOL_ID = s.school_id
            LEFT JOIN departments d ON ehd.EHD_DEPARTMENT_ID = d.dept_id
            ORDER BY ehd.EHD_CREATED_AT DESC
        ");
        
        return view('ePortal.hod_dean_management', compact('schools', 'employees', 'hodDeanList'));
    }
    
    public function addHodDean(Request $request)
    {
        try {
            $data = [
                'EHD_EMPLOYEE_ID' => $request->input('employee_id'),
                'EHD_SCHOOL_ID' => $request->input('school_id'),
                'EHD_DEPARTMENT_ID' => $request->input('department_id'),
                'EHD_POSITION_TYPE' => $request->input('position_type')
            ];
            
            $result = DB::table('EPORTAL_HOD_DEAN_LIST')->insert($data);
            
            return response()->json(['success' => true, 'message' => 'HOD/Dean assigned successfully!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }
    
    public function deleteHodDean(Request $request)
    {
        try {
            $id = $request->input('id');
            $result = DB::table('EPORTAL_HOD_DEAN_LIST')->where('EHD_ID', $id)->delete();
            
            return response()->json(['success' => true, 'message' => 'Record deleted successfully!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }



    // public function deleteEcdr(Request $request){cv
    //     //dd($request->all());
    //     $deleteId = $request->get('ecd_id');
    //     $sql = "delete from EPORTAL_CATEGORIES_DETAILS where id=$deleteId";
    //     $delSql = DB::insert($sql);
    //     return response()->json(['data' => $delSql]);
    // }

    public function deleteEcdr(Request $request){
        // dd($request->all());
        $deleteId = $request->get('ecd_id');
        $categ = $request->get('categ');
        $categ_id = $request->get('categ_id');
        // $categ_id = $request->input('category_id');
        $sql = "select eportal_request_details.id from eportal_request_details
        left join eportal_request on er_id = eportal_request.id
        where ec_id = $categ_id
        and (er_ecd_approval_user = $categ or er_ecd_resolving_user = $categ or eportal_request_details.ER_ECD_VIEWER_USER = $categ)";
        // echo $sql.'<br>';
        $res = DB::select($sql);
        foreach($res as $row){
            $id = $row->id;
            $del = "delete from eportal_request_details where id =$id";
            $resdel = DB::insert($del);
            // echo  $del.'<br>';
        }
        //dd($request->all());
        $sql = "delete from EPORTAL_CATEGORIES_DETAILS where id=$deleteId";
        $delSql = DB::insert($sql);
        return response()->json(['data' => $delSql]);
    }













}
