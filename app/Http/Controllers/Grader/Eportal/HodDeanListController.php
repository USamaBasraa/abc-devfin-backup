<?php

namespace App\Http\Controllers\Grader\Eportal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Grader\Eportal\HodDeanList;
use App\Models\Grader\EmpEmployees;
use DB;
use Exception;

class HodDeanListController extends Controller
{
    /**
     * Display the HOD/Dean List management page
     */
    public function index()
    {
        return view('ePortal.HodDeanList.index');
    }

    /**
     * Get all HOD/Dean records with names
     */
    public function getAllHodDeanList()
    {
        try {
            $hodDeanList = DB::table('eportal_hod_deans_list as hdl')
                ->leftJoin('emp_employees as emp', 'hdl.employee_id', '=', 'emp.employee_id')
                ->leftJoin('schools as sch', 'hdl.school_id', '=', 'sch.school_id')
                ->leftJoin('departments as dept', 'hdl.dept_id', '=', 'dept.dept_id')
                ->leftJoin('programs as prog', 'hdl.PROGRAM_ID', '=', 'prog.prog_id')
                ->select(
                    'hdl.id as id',
                    'hdl.school_id as school_id',
                    'sch.school_name as school_name',
                    'hdl.dept_id as dept_id',
                    'dept.dept_name as dept_name',
                    'hdl.PROGRAM_ID as program_id',
                    'prog.prog_name as program_name',
                    'hdl.employee_id as employee_id',
                    DB::raw("emp.emp_f_name || ' ' || emp.emp_l_name as employee_name"),
                    'hdl.employee_username as employee_username',
                    'hdl.employee_type as employee_type',
                    'hdl.entry_username as entry_username',
                    DB::raw("TO_CHAR(hdl.entry_datetime, 'DD-MON-YYYY HH24:MI') as entry_datetime"),
                    DB::raw("TO_CHAR(hdl.updated_at, 'DD-MON-YYYY HH24:MI') as updated_at")
                )
                ->orderBy('hdl.id', 'DESC')
                ->get();

            return response()->json([
                'status' => true,
                'data' => $hodDeanList
            ])->header('Cache-Control', 'no-cache, no-store, must-revalidate, max-age=0')
              ->header('Pragma', 'no-cache')
              ->header('Expires', '0');

        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error fetching HOD/Dean list: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get employees by school ID
     */
    public function getEmployeesBySchool($schoolId)
    {
        try {
            // If school ID is 12 or 21, show employees from both schools
            $schoolIds = ($schoolId == 12 || $schoolId == 21) ? [12, 21] : [$schoolId];
            
            $employees = EmpEmployees::select(
                    'employee_id',
                    DB::raw("emp_f_name || ' ' || emp_l_name as employee_name"),
                    'emp_ad_username',
                    'emp_email',
                    'emp_dept_id'
                )
                ->whereIn('emp_school_id', $schoolIds)
                ->where('emp_status', 'E')
                ->orderBy('emp_f_name')
                ->get();

            return response()->json([
                'status' => true,
                'data' => $employees
            ])->header('Cache-Control', 'no-cache, no-store, must-revalidate, max-age=0')
              ->header('Pragma', 'no-cache')
              ->header('Expires', '0');
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error fetching employees: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check if HOD/Dean already exists and get pending requests
     */
    public function checkHodDean(Request $request)
    {
        try {
            $schoolId = $request->input('school_id');
            $deptId = $request->input('dept_id');
            $programId = $request->input('program_id');
            $positionType = $request->input('position_type');

            // Check if HOD/Dean already exists
            if ($positionType === 'DEAN') {
                // For DEAN: check by school + employee_type only
                $existsQuery = DB::table('eportal_hod_deans_list')
                    ->where('SCHOOL_ID', $schoolId)
                    ->where('EMPLOYEE_TYPE', $positionType);
            } else {
                // For HOD: check by school + dept + employee_type (+ program if provided)
                $existsQuery = DB::table('eportal_hod_deans_list')
                    ->where('SCHOOL_ID', $schoolId)
                    ->where('DEPT_ID', $deptId)
                    ->where('EMPLOYEE_TYPE', $positionType);
                
                // Include program in check if provided
                if ($programId) {
                    $existsQuery->where('PROGRAM_ID', $programId);
                }
            }
            
            $exists = $existsQuery->first();


            // Get employees for this school (for reference only)
            $employees = EmpEmployees::select('employee_id')
                ->where('emp_status', 'E')
                ->where('emp_school_id', $schoolId)
                ->get();
              
            $employeeIds = $employees->pluck('employee_id')->toArray();
        
          

            // Check pending requests - Find ALL DISTINCT pending requests for this school/dept
            // Where BOTH eportal_request.er_request_status AND eportal_request_details.erd_status are 'Pending'
            $pendingRequestsQuery = DB::table('eportal_request')
                ->join('eportal_request_details', 'eportal_request.id', '=', 'eportal_request_details.er_id')
                ->leftJoin('students_apply_info', 'eportal_request.er_sai_enrollment_no', '=', 'students_apply_info.sai_enrollment_no')
                ->leftJoin('emp_employees', 'eportal_request_details.er_ecd_approval_user', '=', 'emp_employees.employee_id')
                ->leftJoin('eportal_categories', 'eportal_request.ec_id', '=', 'eportal_categories.id')
                ->select(
                    'eportal_request_details.id as erd_id',
                    'eportal_request.id as er_id',
                    'eportal_request.er_sai_enrollment_no as enrollment_no',
                    'eportal_request.er_request_status as request_status',
                    'eportal_request_details.er_ecd_approval_user as approval_user',
                    'eportal_categories.ec_title as request_title',
                    DB::raw("emp_employees.emp_f_name || ' ' || emp_employees.emp_l_name as approver_name")
                )
                ->where('students_apply_info.sai_sdpo_school_id', $schoolId)
                ->where('eportal_request_details.er_ecd_dep', $deptId)
                ->where('eportal_request.er_request_status', 'Pending')
                ->where('eportal_request_details.erd_status', 'Pending')
                ->whereNotNull('eportal_request_details.er_ecd_approval_user')
                ->whereIn('eportal_request_details.er_ecd_approval_user', $employeeIds);
                
            // Filter by program if provided
            if ($programId) {
                $pendingRequestsQuery->where('students_apply_info.sai_sdpo_prog_id', $programId);
            }
            
            $pendingRequests = $pendingRequestsQuery->distinct()->get();

            // Get distinct enrollment numbers count
            // Where BOTH statuses are 'Pending'
            $pendingCountQuery = DB::table('eportal_request')
                ->join('eportal_request_details', 'eportal_request.id', '=', 'eportal_request_details.er_id')
                ->leftJoin('students_apply_info', 'eportal_request.er_sai_enrollment_no', '=', 'students_apply_info.sai_enrollment_no')
                ->where('students_apply_info.sai_sdpo_school_id', $schoolId)
                ->where('eportal_request_details.er_ecd_dep', $deptId)
                ->where('eportal_request.er_request_status', 'Pending')
                ->where('eportal_request_details.erd_status', 'Pending')
                ->whereNotNull('eportal_request_details.er_ecd_approval_user')
                ->whereIn('eportal_request_details.er_ecd_approval_user', $employeeIds);
                
            // Filter by program if provided
            if ($programId) {
                $pendingCountQuery->where('students_apply_info.sai_sdpo_prog_id', $programId);
            }
            
            $pendingCount = $pendingCountQuery->distinct()->count('eportal_request.er_sai_enrollment_no');

            // Group by approver and get unique requests per approver
            $groupedByApprover = $pendingRequests->groupBy('approval_user');
            $approverDetails = [];
            
            foreach ($groupedByApprover as $approverId => $requests) {
                // Get unique requests based on er_id (enrollment_no might have multiple approvers)
                $uniqueRequests = $requests->unique('er_id');
                
                $requestDetails = $uniqueRequests->map(function($req) {
                    return [
                        'erd_id' => $req->erd_id,
                        'er_id' => $req->er_id,
                        'enrollment_no' => $req->enrollment_no,
                        'status' => $req->request_status,
                        'request_title' => $req->request_title
                    ];
                })->values();
                
                $approverDetails[] = [
                    'approver_id' => $approverId,
                    'approver_name' => $requests->first()->approver_name,
                    'pending_count' => $uniqueRequests->count(),
                    'request_ids' => $uniqueRequests->pluck('erd_id')->toArray(),
                    'requests' => $requestDetails->toArray()
                ];
            }

            if ($exists) {
                return response()->json([
                    'status' => true,
                    'exists' => true,
                    'message' => $positionType . ' already exists for this school and department',
                    'data' => $exists,
                    'pending_requests' => [
                        'total_count' => $pendingCount,
                        'approvers' => $approverDetails
                    ]
                ])->header('Cache-Control', 'no-cache, no-store, must-revalidate, max-age=0')
                  ->header('Pragma', 'no-cache')
                  ->header('Expires', '0');
            } else {
                return response()->json([
                    'status' => true,
                    'exists' => false,
                    'message' => 'No ' . $positionType . ' found for this school and department',
                    'pending_requests' => [
                        'total_count' => $pendingCount,
                        'approvers' => $approverDetails
                    ]
                ])->header('Cache-Control', 'no-cache, no-store, must-revalidate, max-age=0')
                  ->header('Pragma', 'no-cache')
                  ->header('Expires', '0');
            }
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error checking HOD/Dean: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store or Update HOD/Dean in HodDeanList (handles both entry and pending requests transfer)
     */
    public function store(Request $request)
    {
        try {
            $schoolId = $request->input('school_id');
            $deptId = $request->input('dept_id');
            $programId = $request->input('program_id');
            $employeeId = $request->input('employee_id');
            $positionType = $request->input('position_type');
            $updatePendingRequests = $request->input('update_pending_requests', 0);
            
            // Validation
            $request->validate([
                'school_id' => 'required|integer',
                'employee_id' => 'required|integer',
                'position_type' => 'required|in:HOD,DEAN',
                'dept_id' => 'required_if:position_type,HOD|nullable|integer',
                'program_id' => 'nullable|integer'
            ]);
            
            // Start transaction to ensure BOTH updates succeed or BOTH fail
            DB::beginTransaction();
            
            // Get authenticated user
            $username = auth()->user()->username ?? 'system';
            
            // Get employee username
            $employee = EmpEmployees::where('employee_id', $employeeId)->first();
            $employeeUsername = $employee ? $employee->emp_ad_username : null;
           
            
            // Prepare data for insert/update in EPORTAL_HOD_DEANS_LIST
            $data = [
                'SCHOOL_ID' => $schoolId,
                'EMPLOYEE_ID' => $employeeId,
                'EMPLOYEE_TYPE' => $positionType,
                'EMPLOYEE_USERNAME' => $employeeUsername
            ];
            
            // Add program_id if provided
            if ($programId) {
                $data['PROGRAM_ID'] = $programId;
            }
            
            // Determine unique keys based on position type
            if ($positionType === 'DEAN') {
                // For DEAN: school_id + employee_type
                $uniqueKeys = [
                    'SCHOOL_ID' => $schoolId,
                    'EMPLOYEE_TYPE' => $positionType
                ];
                $data['DEPT_ID'] = null; // Dean is school-level, no dept
            } else {
                // For HOD: school_id + dept_id + employee_type (+ program_id if provided)
                $uniqueKeys = [
                    'SCHOOL_ID' => $schoolId,
                    'DEPT_ID' => $deptId,
                    'EMPLOYEE_TYPE' => $positionType
                ];
                
                // Add program_id to unique keys if provided
                if ($programId) {
                    $uniqueKeys['PROGRAM_ID'] = $programId;
                }
                
                $data['DEPT_ID'] = $deptId;
            }
            
            // Step 1: Check if record exists and update/insert in EPORTAL_HOD_DEANS_LIST
            $existing = DB::table('eportal_hod_deans_list')->where($uniqueKeys)->first();
            
            if ($existing) {
                // Update existing record using DB
                DB::table('eportal_hod_deans_list')
                    ->where($uniqueKeys)
                    ->update($data);
                $message = $positionType . ' updated successfully';
            } else {
                // Insert new record using DB
                $data['ENTRY_USERNAME'] = $username;
                $data['ENTRY_DATETIME'] = now();
           
                DB::table('eportal_hod_deans_list')->insert($data);
                $message = $positionType . ' added successfully';
            }
            
            // Step 2: If user wants to also update pending requests
            $updatedRequestsCount = 0;
            if ($updatePendingRequests == 1) {
                // Find all pending requests for this school/dept
                // Where BOTH eportal_request.er_request_status AND eportal_request_details.erd_status are 'Pending'
                $pendingRequestsQuery = DB::table('eportal_request')
                    ->join('eportal_request_details', 'eportal_request.id', '=', 'eportal_request_details.er_id')
                    ->join('students_apply_info', 'eportal_request.er_sai_enrollment_no', '=', 'students_apply_info.sai_enrollment_no')
                    ->where('students_apply_info.sai_sdpo_school_id', $schoolId)
                    ->where('eportal_request.er_request_status', 'Pending')
                    ->where('eportal_request_details.erd_status', 'Pending')
                    ->whereNotNull('eportal_request_details.er_ecd_approval_user');
                
                // For HOD, filter by department too
                if ($positionType === 'HOD' && $deptId) {
                    $pendingRequestsQuery->where('eportal_request_details.er_ecd_dep', $deptId);
                }
                
                // Filter by program if provided
                if ($programId) {
                    $pendingRequestsQuery->where('students_apply_info.sai_sdpo_prog_id', $programId);
                }
                
                $requestDetailIds = $pendingRequestsQuery->pluck('eportal_request_details.id')->toArray();
                
                // Update ONLY the eportal_request_details IDs that have BOTH statuses as 'Pending'
                if (!empty($requestDetailIds)) {
                    $updatedRequestsCount = DB::table('eportal_request_details')
                        ->whereIn('id', $requestDetailIds)
                        ->where('erd_status', 'Pending')
                        ->update([
                            'er_ecd_approval_user' => $employeeId
                        ]);
                    
                    $message .= '. ' . $updatedRequestsCount . ' pending request(s) transferred';
                }
            }
            
            // Commit the transaction - BOTH updates successful
            DB::commit();
            
            return response()->json([
                'status' => true,
                'message' => $message,
                'updated_requests_count' => $updatedRequestsCount
            ])->header('Cache-Control', 'no-cache, no-store, must-revalidate, max-age=0')
              ->header('Pragma', 'no-cache')
              ->header('Expires', '0');
              
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollback();
            return response()->json([
                'status' => false,
                'message' => 'Validation failed: ' . implode(', ', $e->errors())
            ], 422);
        } catch (Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => false,
                'message' => 'Error saving HOD/Dean: ' . $e->getMessage()
            ], 500);
        }
    }
}