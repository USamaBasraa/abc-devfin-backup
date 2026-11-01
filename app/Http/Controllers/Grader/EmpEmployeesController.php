<?php
/**
 * Created by PhpStorm.
 * User: Awais Khan
 * Date: 06-Sep-18
 * Time: 3:34 PM
 */
namespace App\Http\Controllers\Grader;
use App\Models\Grader\EmpEmployees;
use DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Validation\ValidationException;

class EmpEmployeesController extends Controller
{
    private $employees;

    //    public function __construct(EmpEmployees $EmpEmployees)
//    {
//        $this->employees = $EmpEmployees;
//    }

    public function index()
    {
        //TBD
    }

    public function checkHod($dept_id)
    {
        //return $this->employees->checkHodExist($dept_id);
        //Only for Testing Bases
        $test = new EmpEmployees();
        return $test->checkHodExist($dept_id);

    }
    public function get_current_login_employee(Request $request)
    {

        $query = "select
        id,
        full_name,
        email,
        imagename,
        department,
        username,
        designation
        from sisusers where email='" . $request->input('email') . "'
        ";
        $emp_data = DB::select($query);
        return $emp_data;
    }

    public function addNetFaculty(Request $request)
    {

        $sql = "
        INSERT INTO faculty_courses (
            fc_id,
            fc_employee_id,
            fc_co_id,
            fc_co_active_sess_id,
            fc_co_sess_id,
            fc_co_school_id,
            fc_co_dept_id,
            fc_co_shift_id,
            fc_co_course_id,
            fc_co_section,
            fc_co_course_cr,
            fc_type,
            fc_contact_hour,
            fc_desc,
            fc_status,
            fc_order,
            fc_entry_user_id,
            fc_faculty_status,
            fc_entry_datetime
        ) SELECT
          fc_sequence.nextval,
          '" . $request->input('new_faculty_id') . "',
             co_id,
            co_sess_id,
            co_sess_id,
            co_school_id,
            co_dept_id,
            co_shift_id,
            co_course_id,
            co_section,
            co_course_cr,
            co_type,
            '',
            '',
            'E',
            (SELECT COUNT(*)+1 FROM faculty_courses) AS fc_order,
            '',
            'Regular',
            CURRENT_TIMESTAMP

        FROM
            courses_offered
            where co_id='" . $request->input('co_id') . "'";
        return DB::insert($sql);
        // DB::table('faculty_courses')->insert($sql);
//            DB::statement("INSERT INTO table (SELECT)");
    }
    public function get_employee_data(Request $request)
    {
        // dd(auth()->user()->profile_settings->active_session);
        $sessId = auth()->user()->profile_settings->active_session;
        //return auth()->user()->employee_id;
        // return $request->all();


        $query = "select
         employee_id , faculty_courses.fc_employee_id,
                  fc_id,
                  prefix||' '||emp_f_name || ' ' ||emp_l_name as employee_name,
                  emp_type,
                  fc_approval_status,
                  emp_status,
                  emp_ad_username,
                  school_code,
                  emp_email,
                  dept_name,
                  get_teachername(fc_co_id) as teacher_name,
                  get_course_name(fc_co_id) as  course_name,
                  academic_sessions.sess_name
         from faculty_courses
         left join emp_employees on  employee_id = faculty_courses.fc_employee_id
              left join academic_sessions on sess_id = fc_co_sess_id
              left join schools on school_id = emp_school_id
              left join departments on dept_id = emp_dept_id
              where employee_id IS NOT NULL
         AND faculty_courses.fc_employee_id IS NOT NULL ";

        if ($request->input('approval_status') != '') {
            $query .= " and fc_approval_status = " . $request->input('approval_status');
        }
        if ($request->input('co_id') != '') {
            $query .= " and fc_co_id ='" . $request->input('co_id') . "'
                      and emp_employees.employee_id not in (" . ($request->input('user_id') == '' ? auth()->user()->employee_id : $request->input('user_id')) . ")
                      and fc_co_id in
                                (
                                    select fc.fc_co_id from faculty_courses fc
                                    where fc.fc_employee_id = " . auth()->user()->employee_id . "
                                    and fc.fc_status='E'
                                )
                                and emp_type = 6
                      ";
        }
        if ($request->input('emp_id') != '') {
            $query .= "and emp_status='E' and employee_id='" . $request->input('emp_id') . "'";
        }
        if ($request->input('all_enabled') != '') {
            $query .= "and emp_status='E'";
        }
        // if($request->input('sess_id')!=''){
        $query .= " and fc_co_sess_id = (select max(fc_co_sess_id) from faculty_courses)
            and fc_co_id in (

                select co_id from courses_offered where co_dept_id = " . auth()->user()->dept_id . "

                )";//(select max(fc_co_sess_id) from faculty_courses)";//157";//.$request->input('sess_id');
        // }
        $query .= " order by fc_id desc";
        //echo $query;exit();
        // pakistan
        $emp_data = DB::select($query);
        return response()->json(['data' => $emp_data]);

    }
    public function ta_rights_assign(Request $request)
    {
        // return $request->all();
        # this select will check the entry user is authorized for this course or not
        $sql = "select fc.fc_co_id from faculty_courses fc
            where fc.fc_employee_id = " . auth()->user()->employee_id . "
            and fc.fc_status='E'
            and fc_co_id=" . $request->input('co_id') . "
            ";
        $rec = DB::select($sql);
        if (count($rec) == 0) {
            return response()->json(['data' => '[]']);
            // if user is not registerd in the course system will not allow him to enter data
        }
        ;
        if ($request->input('status') == '0') {
            $dataSet = [
                'categ_name' => $request->input('categ_id'),
                'entry_user_id' => auth()->user()->employee_id,
                'co_id' => $request->input('co_id'),
                'employee_id' => $request->input('emp_id'),
            ];

            DB::table('ta_rolls')->insert($dataSet);
        } else if ($request->input('status') == '1') {
            $delete_parems = [
                'categ_name' => $request->input('categ_id'),
                'co_id' => $request->input('co_id'),
            ];
            DB::table('ta_rolls')
                ->where($delete_parems)->delete();
        }
        $query = "
            SELECT
                cms_categories.id,
                category_icon,
                category_title,
                (select count(*) from ta_rolls
                    where ta_rolls.categ_name=cms_categories.id
                    AND ta_rolls.co_id = '" . $request->input('co_id') . "'
                    and ta_rolls.employee_id = '" . $request->input('emp_id') . "'
                ) as selected_status
            FROM
                cms_categories
                where CATEGORY_GROUP = 'faculty_dashboard'
                and id not in (5)";
        //echo $query;exit;
        $result = DB::select($query);
        //    return $query;
        return response()->json(['data' => $result]);
    }

    public function update_TA(Request $request)
    {

        //return $request->all();

        if (1 == 1) {
            // if (auth()->user()->username=='haider.raza'){
            $provided_data = [
                'fc_approval_status' => $request->input('approval_status'),
                'fc_status' => ($request->input('approval_status') == 'Approved' ? 'E' : 'D')
            ];
            DB::table('faculty_courses')
                ->where('fc_id', $request->input('fc_id'))
                ->update($provided_data);
            return response()->json(['data' => 'Updated ']);
            // }
        }

    }

    public function employeeDetails(Request $request)
    {
        $get_groups_qry = ("select GROUP_ID,GROUP_NAME from groups where group_id = 644 order by group_id");
        $school_sql = "select school_id,school_code,school_name from schools where school_status='E'";
        $allowed_schools = array("644", "181", "121", "884", "404", "1346");
        if (!in_array(auth()->user()->group_id, $allowed_schools)) {
            $school_sql .= " and school_id = '" . auth()->user()->school_id . "' ";
        }
        $designation_sql = "select distinct(designation) from sisusers where group_id not in 4";
        $school_data = DB::select($school_sql);
        $designation_data = DB::select($designation_sql);
        $obj_groups = DB::select($get_groups_qry);
        $SelectFaculty = $this->getEmpData($request);
        // dd($SelectFaculty);
        return view('grader.EmpEmployee.EmpEmployee', [
            'SelectFaculty' => $SelectFaculty,
            'obj_groups' => $obj_groups,
            'school_data' => $school_data,
            'designation' => $designation_data,
        ]);
    }


    public function getDept(Request $request)
    {
        $schoolId = $request->input('school_id');
        $departments = DB::select('SELECT dept_id, dept_name FROM departments WHERE dept_school_id = ?', [$schoolId]);

        return response()->json($departments);
    }


    public function getEmpData(Request $request)
    {
        //   dd(auth()->user());
        // dd($request->input('employee_id'));


        $sqlSelectFaculty = "
    SELECT
    emp_employees.employee_id,
    trim(emp_employees.emp_f_name) as emp_f_name,
    trim(emp_employees.emp_l_name) as emp_l_name,
    emp_employees.prefix,
    emp_employees.birth_date,
    emp_employees.nationality,
    '' as country_name,
    emp_employees.emp_gender,
    emp_employees.desig_id,
    emp_employees.department_id,
    emp_employees.company_id,
    emp_employees.emp_father_name,
    emp_employees.emp_nic,
    emp_employees.emp_pref_name,
    REGEXP_REPLACE(emp_employees.emp_address, '[^0-9A-Za-z]', '') as emp_address,
    emp_employees.emp_status,
    emp_employees.emp_order,
    emp_employees.emp_type,
    emp_employees.emp_email,

    emp_employees.emp_contact_no,
    emp_employees.emp_ad_username,
    emp_employees.emp_school_id,
    schools.school_code,
    emp_employees.emp_dept_id,
    departments.dept_name,
    s.designation,
    schools.school_name,
    trim(emp_employees.EMP_IMAGEFILE) as emp_imagefile,
    emp_employees.emp_nic_pic,
    emp_employees.hod,
    emp_employees.dean,
    s.hr_beams_id,
    s.id as sis_id,
    groups.group_id,
    groups.group_name
    FROM emp_employees
    LEFT JOIN sisusers s ON emp_employees.emp_email = s.email
    LEFT JOIN schools ON emp_employees.emp_school_id = schools.school_id
    LEFT JOIN departments ON emp_employees.emp_dept_id = departments.dept_id
    LEFT JOIN groups on s.GROUP_ID=groups.group_id
    WHERE 1=1 and emp_employees.emp_status in ('E','D') ";
        $allowed_groups = array("644", "2", "284", "181", "121", "884", "404", "1346");
        if (!in_array(auth()->user()->group_id, $allowed_groups)) {
            $sqlSelectFaculty .= " and emp_employees.emp_school_id = '" . auth()->user()->school_id . "' ";
        }

        // echo $sqlSelectFaculty;
        // exit;
        if ($request->input('employee_id')) {
            $sqlSelectFaculty .= " AND emp_employees.employee_id=" . $request->input('employee_id');
            // echo $sqlSelectFaculty; exit();
        }
        $sqlSelectFaculty .= " order by employee_id desc ";
        $SelectFaculty = DB::select($sqlSelectFaculty);
        return $SelectFaculty;
    }


    public function addEmpEmployee(Request $request)
    {
        try {
            $userId = auth()->user()->group_id;


            // dd($request->all());
            // Define validation rules
            $validationRules = [
                'prefix' => 'required',
                'emp_f_name' => 'required',
                'emp_l_name' => 'required',
                'emp_gender' => 'required',
                'emp_email' => 'required|regex:/^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$/',
                'emp_contact_no' => 'required|regex:/^923\d{9}$/',
                'emp_username' => 'required',
                'designation' => 'required',
                'school' => 'required',
                'empType' => 'required',
                'department' => 'required',
                'emp_cnic' => 'required|regex:/^\d{13}$/'
            ];

            // Define validation messages
            $validationMessages = [
                'emp_email.required' => 'The Email field is required.',
                'emp_email.regex' => 'The Email field must be in a valid email format. Example: example@mail.com',
                'emp_contact_no.regex' => 'Mobile number format: 923xxxxxx (12 digits).',
                'prefix.required' => 'The Prefix field is required.',
                'emp_f_name.required' => 'The First name field is required.',
                'emp_l_name.required' => 'The Last name field is required.',
                'emp_gender.required' => 'The Gender field is required.',
                'designation.required' => 'The designation field is required.',
                'emp_username.required' => 'The Username field is required.',
                'empType.required' => 'The empType field is required.',
                'school.required' => 'The School field is required.',
                'department.required' => 'The Department field is required.',
                'emp_cnic.required' => 'CNIC format: 34101812xxxxxxx without dashes (13 digits)'
            ];

            // Add additional validation rule for certain user IDs
            if ($userId == 644 || $userId == 2) {
                $validationRules['empType'] = 'required';
                $validationMessages['empType.required'] = 'The Employee Type field is required.';
            }

            // Validate the request data
            $validatedData = $request->validate($validationRules, $validationMessages);

            $employeeId = $request->input('employee_id');
            $empFName = $request->input('emp_f_name');
            $department = $request->input('department');
            $school = $request->input('school');
            $empLName = $request->input('emp_l_name');
            $prefix = $request->input('prefix');
            $empEmail = strtolower($request->input('emp_email'));
            $empGender = $request->input('emp_gender');
            $empCnic = $request->input('emp_cnic');
            $empContactNo = $request->input('emp_contact_no');
            $empUsername = strtolower($request->input('emp_username'));
            $empType = $request->input('empType');
            $empDesignation = $request->input('designation');
            $usernamFromDB = $request->input('db_username');
            $hod = $request->input('hod');
            $dean = $request->input('dean');
            $hris_id = $request->input('hris_id');
            $group_id = '';
            if ($empType == 2 || $empType == 3) {
                $group_id = '222';
            } else if ($empType == 6) {
                $group_id = '825';
            } else if ($empType == 4 || $empType == 5) {
                $group_id = '223';
            } else if ($empType == 1) {
                $group_id = '86';
            }

            if (!is_null($employeeId)) {
                // dd($empDesignation);
                // Update existing employee records
                // dd($request->input('designation'));
                // return $request->all();
                $empUpdateResult = DB::table('emp_employees')
                    ->where('emp_ad_username', $usernamFromDB)
                    ->update([
                        'emp_f_name' => $empFName,
                        'emp_l_name' => $empLName,
                        'prefix' => $prefix,
                        'emp_email' => $empEmail,
                        'emp_gender' => $empGender,
                        'emp_contact_no' => $empContactNo,
                        'emp_ad_username' => $empUsername,
                        'emp_dept_id' => $department,
                        'emp_school_id' => $school,
                        'emp_type' => $empType,
                        'emp_nic' => $empCnic,
                        'hod' => $hod,
                        'dean' => $dean,
                        'entered_by' => auth()->user()->employee_id
                    ]);

                if ($request->hasFile('emp_image')) {
                    if ($employeeId) {
                        $image = $request->file('emp_image');
                        $imageName = $employeeId . '.jpg';
                        $image->move(public_path() . '/staffimages', $imageName);
                        DB::table('emp_employees')
                            ->where('employee_id', $employeeId)
                            ->update(['EMP_IMAGEFILE' => $imageName]);
                        
                        $sisUser = DB::table('sisusers')->where('employee_id', $employeeId)->first();
                        if ($sisUser) {
                            DB::table('sisusers')
                                ->where('id', $sisUser->id)
                                ->update(['imagename' => $imageName]);
                        }
                    }
                }

                $sisData = [
                    'email' => $empEmail,
                    'employee_id' => $employeeId,
                    'mobile' => $empContactNo,
                    'username' => $empUsername,
                    'designation' => $empDesignation,
                    'hr_beams_id' => $hris_id,
                    'entry_user_id' => auth()->user()->employee_id
                ];

                // Removed redundant image logic here as it's handled above

                // Only update group_id if empType is NOT 1
                if ($empType != 1) {
                    $sisData['group_id'] = $group_id;
                }

                $sisUpdateResult = DB::table('sisusers')
                    ->where('username', $usernamFromDB)
                    ->update($sisData);

                if ($empUpdateResult && $sisUpdateResult) {
                    // Records updated successfully
                    return response()->json([
                        'SelectFaculty' => $this->getEmpData($request),
                        'message' => 'Records updated successfully',
                        'status' => true
                    ]);

                } else {
                    // Error updating records
                    return response()->json([
                        'message' => 'Error updating records',
                        'status' => false
                    ]);
                }
            } else {

                // Insert new employee records

                $empEmailExists = DB::table('emp_employees')
                    ->where('emp_email', $empEmail)
                    ->exists();

                $sisEmailExists = DB::table('sisusers')
                    ->where('email', $empEmail)
                    ->exists();

                $empUsernameExists = DB::table('emp_employees')
                    ->where('emp_ad_username', $empUsername)
                    ->exists();

                $sisUsernameExists = DB::table('sisusers')
                    ->where('username', $empUsername)
                    ->exists();

                if ($empEmailExists || $sisEmailExists) {
                    // Email already exists, return error response
                    return response()->json([
                        'emp_email' => 'Email already exists',
                        'status' => false
                    ]);
                }

                if ($empUsernameExists || $sisUsernameExists) {
                    // Username already exists, return error response
                    return response()->json([
                        'emp_username' => 'Username already exists',
                        'status' => false
                    ]);
                }
                $chec_ = "select max(sess_id) as sess_id  from academic_sessions ";
                $resChec = DB::select($chec_);

                $sessionWithDate = '{ "active_session" : "' . $resChec[0]->sess_id . '"}';
                // Perform the insertions
                $empTypeValue = ($userId == 644 || $userId == 2) ? $empType : null;
                $groupByValue = ($userId == 644 || $userId == 2) ? $empType : 'null';
                // Find the maximum value of employee_id
                $maxEmployeeId = DB::table('emp_employees')
                    ->max('employee_id');

                // Calculate the next interval
                $nextInterval = $maxEmployeeId + 1;
                // Assign the next interval to $employeeId
                $employeeId = $nextInterval;
                $empInsertResult = DB::table('emp_employees')
                    ->insert([
                        'employee_id' => $employeeId,
                        'emp_f_name' => $empFName,
                        'emp_l_name' => $empLName,
                        'prefix' => $prefix,
                        'emp_email' => $empEmail,
                        'emp_gender' => $empGender,
                        'emp_contact_no' => $empContactNo,
                        'emp_ad_username' => $empUsername,
                        'emp_dept_id' => $department,
                        'emp_school_id' => $school,
                        'emp_status' => 'E',
                        'emp_nic' => $empCnic,
                        'emp_type' => $empType,
                        'hod' => $hod,
                        'dean' => $dean,
                        'entered_by' => auth()->user()->employee_id
                    ]);

                // $newIdFromDB = DB::table('sisusers')
                //     ->selectRaw('COALESCE(MAX(id), 0) + 1 AS new_id')
                //     ->value('new_id');

                $sisData = [
                    // 'id' => $newIdFromDB, // Manually assign ID if MAX+1 is the pattern
                    'email' => $empEmail,
                    'username' => $empUsername,
                    'employee_id' => $employeeId,
                    'designation' => $empDesignation,
                    'mobile' => $empContactNo,
                    'hr_beams_id' => $hris_id,
                    'group_id' => $group_id,
                    'entry_user_id' => auth()->user()->employee_id,
                    'profile_settings' => $sessionWithDate
                ];

                $sisInsertResult = DB::table('sisusers')
                    ->insert($sisData);

                // $sis_id = $newIdFromDB;

                if ($request->hasFile('emp_image') && $sisInsertResult) {
                    if ($employeeId) {
                        $image = $request->file('emp_image');
                        $imageName = $employeeId . '.jpg';
                        $image->move(public_path() . '/staffimages', $imageName);
                        DB::table('emp_employees')
                            ->where('employee_id', $employeeId)
                            ->update(['EMP_IMAGEFILE' => $imageName]);
                        
                        DB::table('sisusers')
                            ->where('id', $sis_id)
                            ->update(['imagename' => $imageName]);
                    }
                }


                if ($empInsertResult && $sisInsertResult) {
                    // Records inserted successfully
                    return response()->json([
                        'SelectFaculty' => $this->getEmpData($request),
                        'message' => 'Records inserted successfully',
                        'status' => true
                    ]);
                } else {
                    // Error inserting records
                    return response()->json([
                        'message' => 'Error inserting records',
                        'status' => false
                    ]);
                }
            }
        } catch (ValidationException $e) {
            // Validation errors occurred
            $errors = $e->validator->errors()->toArray();
            return response()->json([
                'status' => false,
                'errors' => $errors
            ]);
        } catch (Exception $e) {
            // Other exceptions occurred
            $res = [
                'status' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ];
            return response()->json($res);
        }


    }


    public function updateEmpStatus(Request $request)
    {

        // $request->input('emp_status')
        // dd($request->all());

        $sqlUpdateStatus = "update emp_employees set emp_status='" . $request->input('emp_status') . "' where employee_id='" . $request->input('employee_id') . "'";
        echo $sqlUpdateStatus;
        exit();
        $updateStatus = DB::update($sqlUpdateStatus);

        $res = [
            'status' => true,
            'message' => 'Employee Status Updated'
        ];
        return response()->json($res);

    }

    function createTa(Request $request)
    {
        $student_id = $request->get('studentId');
        $sql_ta = "INSERT INTO emp_employees (
        employee_id,
    prefix,
    emp_f_name,
    emp_l_name,
    nationality,
    emp_gender,
    desig_id,
    department_id,
    company_id,
    emp_father_name,
    emp_nic,
    emp_pref_name,
    emp_address,
    emp_status,
    emp_email,
    emp_type,
    emp_order,
    emp_ad_username,
    emp_school_id,
    emp_dept_id,
    emp_contact_no
    )
    select
    (select max(employee_id)+1 from emp_employees) as id,
    case when students_personal_info.spi_gender='Female' then 'Miss.' else 'Mr.' end as prefix,
    spi_name,
    ' (TA) ',
    students_personal_info.spi_nationality,
    students_personal_info.spi_gender,
    '4',
    sai_sdpo_dept_id,
    '1',
    students_personal_info.spi_fathername,
    students_personal_info.spi_nic,
    students_personal_info.spi_fathername,
    REGEXP_REPLACE(students_personal_info.spi_presentaddress, '[^0-9A-Za-z]', '') as spi_presentaddress,
    'E',
    upper(sai_enrollment_no)||'@bnu.edu.pk' as spi_email,
    '6' as emp_type,
    (select count(*)+1 from emp_employees) as emp_order,
    upper(sai_enrollment_no) as emp_ad_username,
    sai_sdpo_school_id as emp_school_id,
    sai_sdpo_dept_id as emp_dept_id,
    spi_mobile as emp_contact_no

    from students_apply_info
    left join students_personal_info on spi_id = sai_spi_id
    where lower(sai_enrollment_no)='" . strtolower($student_id) . "' ";
        //echo $sql_ta;exit;

        try {
            $resinserTA = DB::insert($sql_ta);
        }
        //catch exception
        catch (Exception $e) {
            echo 'Message: ' . $e->getMessage();
        }
        //echo 'Teach Assistant Created Successfully';

        $sql_update = "update sisusers set employee_id=
    (select employee_id from emp_employees where lower(emp_ad_username) = '" . strtolower($student_id) . "'
    )
    where lower(username) = '" . strtolower($student_id) . "'";
        // echo $sql_ta;exit;
        $resTA = DB::insert($sql_update);
        if ($resTA && $resinserTA) {
            echo '1';
        }
    }

    public function printEmployeeCard($employee_id)
    {
        $sql = "SELECT
            e.employee_id,
            trim(e.emp_f_name) as emp_f_name,
            trim(e.emp_l_name) as emp_l_name,
            d.dept_name,
            s.designation,
            s.hr_beams_id,
            s.id as sis_id,
            trim(e.EMP_IMAGEFILE) as emp_imagefile
            FROM emp_employees e
            LEFT JOIN sisusers s ON e.emp_email = s.email
            LEFT JOIN departments d ON e.emp_dept_id = d.dept_id
            WHERE e.employee_id = '" . $employee_id . "'";

        $employee = DB::select($sql);
        // dd($sql);

        if (empty($employee)) {
            return "Employee not found.";
        }

        $emp = $employee[0];
       

        require_once(public_path('fpdf/fpdf.php'));

        $pdf = new \FPDF('P', 'mm', array(54, 86));
        $pdf->SetMargins(0, 0, 0);
        $pdf->SetAutoPageBreak(false);
        $pdf->AddPage();

        /* ================= BACKGROUND ================= */
        $bgPath = public_path('staffimages/bnu_card_bg.jpg');
        if (file_exists($bgPath)) {
            $pdf->Image($bgPath, 0, 0, 54, 86);
        }

        /* ================= HEADER ================= */
        // Black Top Bar
        $pdf->SetFillColor(0, 0, 0);
        $pdf->Rect(0, 0, 54, 6, 'F');

        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFont('Arial', 'B', 7);
        $pdf->SetXY(0, 1.5);
        $pdf->Cell(54, 3, "BEACONHOUSE NATIONAL UNIVERSITY", 0, 0, 'C');

        // Red Line
        $pdf->SetDrawColor(180, 0, 0);
        $pdf->SetLineWidth(0.8);
        $pdf->Line(0, 7.5, 54, 7.5);

        /* ================= LOGO ================= */
        $logoPath = public_path('staffimages/bnuLogo.png');
        if (file_exists($logoPath)) {
            $pdf->Image($logoPath, 18, 10, 18); // Moved up slightly
        }
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetXY(0, 19.5); // Moved up to clear photo border
        // $pdf->Cell(54, 4, "BNU", 0, 0, 'C');

        /* ================= PHOTO ================= */
        // Configuration for image handling in pixels (Converted to mm for FPDF at 96 DPI)
        $pxToMm = 25.4 / 96;

        $photoX_px = 68;      // Approx 18mm
        $photoY_px = 115;     // Approx 30.5mm
        $photoWidth_px = 77;  // Approx 19mm
        $photoHeight_px = 84; // Approx 21mm

        $photoX = $photoX_px * $pxToMm;
        $photoY = $photoY_px * $pxToMm;
        $photoWidth = $photoWidth_px * $pxToMm;
        $photoHeight = $photoHeight_px * $pxToMm;

        // Double Border effect for photo
        $pdf->SetDrawColor(100, 100, 100);
        $pdf->SetLineWidth(0.4);
        $pdf->Rect($photoX - 0.5, $photoY - 0.5, $photoWidth + 1, $photoHeight + 1);
        
        $pdf->SetDrawColor(0, 0, 0);
        $pdf->SetLineWidth(0.2);
        $pdf->Rect($photoX, $photoY, $photoWidth, $photoHeight);

        $photoName = $emp->emp_imagefile ?: ($emp->employee_id . '.jpg');
        $photoPath = public_path('staffimages/' . $photoName);
        
        // Fallback to students_pics if not found in staffimages
        // if (!file_exists($photoPath)) {
        //     $photoPath = public_path('students_pics/' . ($emp->employee_id . '.jpg'));
        // }

        if (file_exists($photoPath)) {
            $type = '';
            $info = @getimagesize($photoPath);
            if ($info) {
                if ($info[2] == IMAGETYPE_JPEG) $type = 'JPG';
                elseif ($info[2] == IMAGETYPE_PNG) $type = 'PNG';
                elseif ($info[2] == IMAGETYPE_GIF) $type = 'JPEG';
            }

            try {
                $pdf->Image($photoPath, $photoX, $photoY, $photoWidth, $photoHeight, $type);
            } catch (\Exception $e) {
                $pdf->SetFont('Arial', '', 7);
                $pdf->SetTextColor(150, 150, 150);
                $pdf->SetXY($photoX, $photoY + 11);
                $pdf->Cell($photoWidth, 5, "IMG ERROR", 0, 0, 'C');
            }
        } else {
            $pdf->SetFont('Arial', '', 7);
            $pdf->SetTextColor(150, 150, 150);
            $pdf->SetXY($photoX, $photoY + 11);
            $pdf->Cell($photoWidth, 5, "NO PHOTO", 0, 0, 'C');
        }

        /* ================= DETAILS ================= */
        $pdf->SetTextColor(0, 0, 0);
        
        // Name
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->SetY(53.5); // Precisely positioned after photo
        $fullName = $emp->emp_f_name . ' ' . $emp->emp_l_name;
        $pdf->Cell(54, 5, $fullName, 0, 1, 'C');

        // Designation
        $pdf->SetFont('Arial', 'B', 9);

        $designation = $emp->designation ?: 'STAFF';

        // Save current X position
        $x = $pdf->GetX();
        $y = $pdf->GetY();

        // Draw MultiCell (auto wraps)
        $pdf->MultiCell(54, 4, $designation, 0, 'C');

        // Set cursor to next line properly
        $pdf->SetXY($x, $y + max(4, $pdf->GetY() - $y));

        // Department/school
        $x = $pdf->GetX();
        $y = $pdf->GetY();
        $pdf->MultiCell(54, 4, $emp->dept_name ?: '', 0, 'C');

        // HRIS ID
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(54, 5, "HRIS ID : " . $emp->hr_beams_id, 0, 1, 'C');

        /* ================= FOOTER ================= */
        // Red Line
        $pdf->SetDrawColor(180, 0, 0);
        $pdf->SetLineWidth(0.8);
        $pdf->Line(0, 79.5, 54, 79.5);

        // Black Footer Bar
        $pdf->SetFillColor(0, 0, 0);
        $pdf->Rect(0, 80.5, 54, 5.5, 'F');

        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFont('Arial', 'B', 5);
        $pdf->SetXY(1, 81);
        $pdf->MultiCell(52, 2.2, "13-km from Thokar Niaz Baig,\noff Raiwind Road, Near Tarogil Village, Lahore", 0, 'C');

        // Output PDF
        $pdf->Output('I', 'Employee_Card_' . $emp->employee_id . '.pdf');
        exit;
    }



    public function searchEmployee(Request $request)

    {
        $search = strtolower($request->input('query'));
        if ($search == '') {
            $employees = DB::table('emp_employees')
                ->select('employee_id', 'emp_ad_username', DB::raw("(emp_f_name || ' ' || emp_l_name) as name"))
                ->limit(5)
                ->get();
        } else {
            $employees = DB::table('emp_employees')
                ->select(
                    'employee_id',
                    'emp_ad_username',
                    DB::raw("TRIM(emp_f_name) || ' ' || TRIM(emp_l_name) as name")
                )
                ->whereRaw(
                    "LOWER(TRIM(emp_f_name) || ' ' || TRIM(emp_l_name)) LIKE LOWER(?)",
                    ['%' . $search . '%']
                )
                ->limit(20)
                ->get();
        }

        $response = array();
        foreach ($employees as $employee) {
            $response[] = array(
                "value" => $employee->employee_id,
                "label" => $employee->name,
                "username" => $employee->emp_ad_username
            );
        }

        return response()->json($response);
    }

}
