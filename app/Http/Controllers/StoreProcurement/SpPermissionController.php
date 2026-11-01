<?php

namespace App\Http\Controllers\StoreProcurement;

use App\Http\Controllers\Controller;
use App\Models\StoreProcurement\SpPermission;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SpPermissionController extends Controller
{
    private function ensureAdmin(): void
    {
        if (auth()->user()->group_id != 644) {
            abort(403, 'Only administrators can manage department budget rights.');
        }
    }

    private function getFormData(): array
    {
        $schools = DB::select("SELECT * FROM schools WHERE school_id NOT IN (22, 8, 9, 10) ORDER BY school_name");

        $users = DB::select("
            SELECT prefix, employee_id AS id, emp_f_name || ' ' || emp_l_name AS full_name, emp_dept_id, dept_name, school_code
            FROM EMP_EMPLOYEES
            LEFT JOIN schools ON school_id = emp_school_id
            LEFT JOIN departments ON dept_id = emp_dept_id
            WHERE emp_status = 'E'
            ORDER BY employee_id DESC
        ");

        return compact('schools', 'users');
    }

    public function index()
    {
        $this->ensureAdmin();

        return view('store_procurement.permissions.index', $this->getFormData());
    }

    public function create()
    {
        return redirect()->route('permissions.index');
    }

    public function list()
    {
        $this->ensureAdmin();

        $rows = DB::select("
            SELECT
                p.SP_ID AS id,
                p.SCHOOL_ID,
                p.DEPT_ID,
                p.USER_ID,
                p.PERMISSION_TYPE,
                p.STATUS,
                p.REMARKS,
                s.SCHOOL_NAME,
                d.DEPT_NAME,
                e.PREFIX || ' ' || e.EMP_F_NAME || ' ' || e.EMP_L_NAME AS user_name
            FROM SP_PERMISSIONS p
            LEFT JOIN schools s ON s.SCHOOL_ID = p.SCHOOL_ID
            LEFT JOIN departments d ON d.DEPT_ID = p.DEPT_ID
            LEFT JOIN EMP_EMPLOYEES e ON e.EMPLOYEE_ID = p.USER_ID
            ORDER BY p.SP_ID DESC
        ");

        return response()->json(['data' => $rows]);
    }

    public function store(Request $request)
    {
        $this->ensureAdmin();

        $request->validate([
            'school_id' => 'required|numeric',
            'department_id' => 'required|numeric',
            'user_id' => 'required|numeric',
            'status' => 'required|in:Active,Inactive',
            'remarks' => 'nullable|string|max:500',
        ]);

        $exists = DB::table('SP_PERMISSIONS')
            ->where('USER_ID', $request->user_id)
            ->where('DEPT_ID', $request->department_id)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'This user already has rights for the selected department.',
            ], 422);
        }

        SpPermission::create([
            'SCHOOL_ID' => $request->school_id,
            'DEPT_ID' => $request->department_id,
            'USER_ID' => $request->user_id,
            'PERMISSION_TYPE' => 'BUDGET_VIEW',
            'STATUS' => $request->status,
            'REMARKS' => $request->remarks,
            'CREATED_AT' => Carbon::now(),
            'UPDATED_AT' => Carbon::now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Department budget right assigned successfully.',
        ]);
    }

    public function update(Request $request, $id)
    {
        $this->ensureAdmin();

        $request->validate([
            'status' => 'required|in:Active,Inactive',
            'remarks' => 'nullable|string|max:500',
        ]);

        $permission = SpPermission::findOrFail($id);

        $permission->update([
            'STATUS' => $request->status,
            'REMARKS' => $request->remarks,
            'UPDATED_AT' => Carbon::now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Permission updated successfully.',
        ]);
    }

    public function destroy($id)
    {
        $this->ensureAdmin();

        SpPermission::findOrFail($id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Permission removed successfully.',
        ]);
    }
}
