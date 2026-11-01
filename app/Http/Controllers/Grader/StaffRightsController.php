<?php

namespace App\Http\Controllers\Grader;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserNotifications;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class StaffRightsController extends Controller
{
    
    public function retrive_faculty_schools($faculty_email)
    {  
        $sql = "select school_id,school_name from schools where school_id in (
            select distinct sr_school_id as school_id from staff_rights
            where sr_sis_id = '".$faculty_email."'
            and sr_school_id is not null
            )";
        return DB::select($sql);
    }
    public function retrive_faculty_departments($faculty_email,$department_id)
    {  
        $sql = "select * from departments where dept_id in (
            select distinct sr_dept_id as school_id from staff_rights
            where sr_sis_id = '".$faculty_email."'
            and sr_dept_id is not null
            )
            and dept_school_id =".$department_id;
        return DB::select($sql);
    }
    public function offered_course_faculty(Request $request ){

        $sql = "SELECT employee_id, 
								trim(emp_f_name) as EMP_F_NAME, 
								trim(emp_l_name) as EMP_L_NAME, 
								prefix, 
								birth_date, 
								nationality, 
								'' as country_name, 
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
								LEFT JOIN schools ON emp_school_id = school_id
								LEFT JOIN departments ON emp_dept_id = dept_id 
								WHERE 1=1 AND
										employee_id IN (
											SELECT DISTINCT
												fc_employee_id
											FROM
												faculty_courses
											WHERE
												 EMPLOYEE_ID not in (1605)
												and fc_co_sess_id ='154'  AND fc_co_school_id='3')  order by emp_school_id,emp_dept_id,emp_type";
            return DB::select($sql);                                    

    }

    
   
}
