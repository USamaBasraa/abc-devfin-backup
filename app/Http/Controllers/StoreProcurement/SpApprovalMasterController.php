<?php

namespace App\Http\Controllers\StoreProcurement;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\StoreProcurement\SpApprovalMaster;
use App\Models\Grader\Schools;
use App\Models\Grader\Departments;
use App\Models\User; // assuming users exist
use DB;
class SpApprovalMasterController extends Controller
{
    public function create()
    {
        // @dd("working");
        $select_list = "prefix, employee_id as id, emp_f_name || ' ' || emp_l_name as full_name, emp_dept_id, dept_name, school_code";

        $sql = "SELECT $select_list FROM EMP_EMPLOYEES
                LEFT JOIN schools ON school_id = emp_school_id
                LEFT JOIN departments ON dept_id = emp_dept_id
                WHERE emp_status = 'E'
                ORDER BY employee_id DESC";

        $users = DB::select($sql);
        $sqlschools = "select * from schools where school_id not in (22,8,9,10)";//Schools::all();
        $schools = DB::select($sqlschools);
        $adminDepartments = "select * from departments where dept_school_id not in (1,2,3,4,5,6,7,8,9,10,12,21,22)";
        $departments = DB::select($adminDepartments);
        // $departments = Departments::orderBy('dept_name')->get();
        

        return view('store_procurement.approval_master.create', compact('schools', 'departments', 'users'));
    }

    public function store(Request $request)
    {
        $rules = [
            'approval_type' => 'required|in:budget_permission,price_range_approval',
        ];

        // Conditional validation based on approval type
        if ($request->approval_type === 'price_range_approval') {
            $rules['type'] = 'required|in:academic,non_academic';
            $rules['min_price'] = 'required|numeric|min:0';
            $rules['max_price'] = 'required|numeric|gt:min_price';
            $rules['approval_user_id'] = 'required';
            $rules['role'] = 'required|in:hod,gm,vc,purchase_committee_chair_person,purchase_committee_line_department,purchase_committee_member';
        }

        if ($request->approval_type === 'budget_permission') {
            $rules['allowed_user_id'] = 'required';
        }

        $request->validate($rules);

        try {
            SpApprovalMaster::create([
                'type' => $request->approval_type,
                'min_price' => $request->min_price,
                'max_price' => $request->max_price,
                'approval_user_id' => $request->approval_user_id,
                'allowed_username' => $request->allowed_user_id,
                'active_status' => 'active',
                'school_id' => $request->school_id ?? $request->school_id_budget,
                'dept_id' => $request->department_id ?? $request->department_id_budget,
                'role' => $request->role,
                'entry_user_id' => auth()->user()->username ?? auth()->id(),
                'entry_date_time' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Approval configuration saved successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error saving configuration: ' . $e->getMessage()
            ], 500);
        }
    }

    public function list()
    {
        $data = DB::select("
            SELECT
                a.ID,
                a.TYPE,
                a.MIN_PRICE,
                a.MAX_PRICE,
                a.ROLE,
                a.ACTIVE_STATUS,
                s.SCHOOL_NAME,
                d.DEPT_NAME,
                e1.PREFIX || ' ' || e1.EMP_F_NAME || ' ' || e1.EMP_L_NAME AS approver_name,
                e1.EMP_AD_USERNAME AS user_id,
                e2.PREFIX || ' ' || e2.EMP_F_NAME || ' ' || e2.EMP_L_NAME AS allowed_person_name,
                e2.EMP_AD_USERNAME AS allowed_user_id
            FROM SP_APPROVAL_MASTERS a
            LEFT JOIN SCHOOLS s ON s.SCHOOL_ID = a.SCHOOL_ID
            LEFT JOIN DEPARTMENTS d ON d.DEPT_ID = a.DEPT_ID
            LEFT JOIN EMP_EMPLOYEES e1 ON e1.EMPLOYEE_ID = a.APPROVAL_USER_ID
            LEFT JOIN EMP_EMPLOYEES e2 ON e2.EMPLOYEE_ID = a.ALLOWED_USERNAME
            ORDER BY a.ID DESC
        ");

        return response()->json(['data' => $data]);
    }
    public function delete(Request $request)
    {
        try {
            $id = $request->id;

            $deleted = DB::table('SP_APPROVAL_MASTERS')->where('ID', $id)->delete();

            if ($deleted) {
                return response()->json([
                    'success' => true,
                    'message' => 'Record deleted successfully'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Record not found'
                ], 404);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting record: ' . $e->getMessage()
            ], 500);
        }
    }
}
