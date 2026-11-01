<?php

namespace App\Http\Controllers\Grader;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class PagePermissionsController extends Controller
{
    public function index(Request $request)
    {


        $authGroup=auth()->user()->group_id;
     
        if ($authGroup != '644') {
           abort(403, 'Unauthorized action.');
        }
        // Handle AJAX request for group permissions
        if ($request->ajax() && $request->has('group_id')) {
            $groupId = $request->input('group_id');
            
            $permissions = DB::table('group_details')
                ->where('gd_group_id', $groupId)
                ->select([
                    'gd_id',
                    'gd_group_id',
                    'gd_page_id',
                    'GD_ALLOWED',
                    'gd_entry_by',
                    'gd_entry_datetime'
                ])
                ->get();

            return response()->json([
                'status' => 'success',
                'permissions' => $permissions
            ]);
        }

        // Regular view request - return minimal data for form
        $groups = DB::table('groups')->get();


        $users = DB::table('sisusers')
        ->leftJoin('emp_employees', 'sisusers.username', '=', 'emp_employees.emp_ad_username')
        ->leftJoin('groups', 'sisusers.group_id', '=', 'groups.group_id')
        ->where('sisusers.group_id', '!=', 4)
        ->where('emp_employees.emp_status', 'E')
        ->select(
            DB::raw("emp_employees.prefix || ' ' || emp_employees.emp_f_name || ' ' || emp_employees.emp_l_name AS full_name"),
            'sisusers.username',
            'sisusers.group_id',
            'groups.group_name',
            'emp_employees.emp_email'
        )
        ->get();
    
        return view('grader.page_permissions.page_permission', compact('groups', 'users'));
    }

    public function getPages()
    {
        $pages = DB::table('pages')
            ->select([
                'page_id',
                'page_name',
                'page_url',
            ])
       
            ->where('page_for_app','LARAVEL')
            ->orderBy('page_name', 'asc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $pages
        ]);
    }







    public function store(Request $request)
    {
        // Step 1: Validate input
        $request->validate([
            'assignment_type' => 'required|in:user,group',
            'group_id'        => 'required_if:assignment_type,group|exists:groups,group_id',
            'username'        => 'required_if:assignment_type,user|exists:sisusers,username',
            'page_ids'        => 'array',
            'page_ids.*'      => 'exists:pages,page_id',
        ]);
    
        // Step 2: Extract data
        $type     = $request->assignment_type;
        $groupId  = $request->group_id;
        $username = $request->username;
        $pageIds  = $request->page_ids ?? [];
    
        // Step 3: Determine column and value
        $fieldName  = $type === 'group' ? 'gd_group_id' : 'gd_allowed_username';
        $fieldValue = $type === 'group' ? $groupId : $username;
    
        try {
            // Step 4: Start transaction
            DB::beginTransaction();
    
            // Step 5: Get existing pages for current assignment
            $existingPages = DB::table('group_details')
                ->where($fieldName, $fieldValue)
                ->pluck('gd_page_id')
                ->toArray();
    
            // Step 6: Identify pages to add/remove
            $pagesToAdd    = array_diff($pageIds, $existingPages);
            $pagesToRemove = array_diff($existingPages, $pageIds);
    
            // Step 7: Filter out duplicates based on new logic
            if ($type === 'user' && $groupId) {
                // Get group-assigned pages for this group
                $groupPages = DB::table('group_details')
                    ->where('gd_group_id', $groupId)
                    ->whereNull('gd_allowed_username') // Ensure it's group-level
                    ->pluck('gd_page_id')
                    ->toArray();
    
                // ❌ Exclude group pages from user's personal pages
                $pagesToAdd = array_diff($pagesToAdd, $groupPages);
            }
    
            if ($type === 'group') {
                // Get pages assigned to individual users of this group
                $userPages = DB::table('group_details')
                    ->where('gd_group_id', $groupId)
                    ->whereNotNull('gd_allowed_username') // Only user-level rights
                    ->pluck('gd_page_id')
                    ->toArray();
    
                // ❌ Exclude user-level pages from group assignment
                $pagesToAdd = array_diff($pagesToAdd, $userPages);
            }
    
            // Step 8: Remove pages no longer allowed
            if (!empty($pagesToRemove)) {
                DB::table('group_details')
                    ->where($fieldName, $fieldValue)
                    ->whereIn('gd_page_id', $pagesToRemove)
                    ->delete();
            }
    
            // Step 9: Add new pages
            foreach ($pagesToAdd as $pageId) {
                DB::table('group_details')->insert([
                    'gd_group_id'         => $type === 'group' ? $groupId : null,
                    'gd_allowed_username' => $type === 'user' ? $username : null,
                    'gd_page_id'          => $pageId,
                    'GD_ALLOWED'          => 'True',
                    'gd_entry_by'         => auth()->user()->username ?? 'System',
                    'gd_entry_datetime'   => now(),
                    'gd_remarks'          => 'Added by bulk assignment',
                    'gd_created_at'       => now(),
                    'gd_updated_at'       => now(),
                ]);
            }
    
            // Step 10: Commit and respond
            DB::commit();
    
            return response()->json([
                'status'  => 'success',
                'message' => count($pagesToAdd) . ' pages added, ' . count($pagesToRemove) . ' pages removed.',
            ]);
    
        } catch (\Exception $e) {
            DB::rollBack();
    
            return response()->json([
                'status'  => 'error',
                'message' => 'Something went wrong: ' . $e->getMessage(),
            ], 500);
        }
    }
    











    // public function store(Request $request)
    // {
    //     // Step 1: Validate input data from the form
    //     $request->validate([
    //         'assignment_type' => 'required|in:user,group', // user ya group hona chahiye
    //         'group_id'        => 'required_if:assignment_type,group|exists:groups,group_id',
    //         'username'        => 'required_if:assignment_type,user|exists:sisusers,username',
    //         'page_ids'        => 'array', // pages ka array hona chahiye
    //         'page_ids.*'      => 'exists:pages,page_id', // har page valid hona chahiye
    //     ]);
    
    //     // Step 2: Get data from request
    //     $type     = $request->assignment_type; // user ya group
    //     $groupId  = $request->group_id;        // sirf jab group ho
    //     $username = $request->username;        // sirf jab user ho
    //     $pageIds  = $request->page_ids ?? [];  // agar pages nahi miley to khali array
    
    //     // Step 3: Decide which field to use (group ya username)
    //     $fieldName  = $type === 'group' ? 'gd_group_id' : 'gd_allowed_username';
    //     $fieldValue = $type === 'group' ? $groupId : $username;
    
    //     try {
    //         // Step 4: Start transaction
    //         DB::beginTransaction();
    
    //         // Step 5: Get already saved page_ids for this group/user
    //         $existingPages = DB::table('group_details')
    //             ->where($fieldName, $fieldValue)
    //             ->pluck('gd_page_id')
    //             ->toArray();
    
    //         // Step 6: Calculate which pages to add or remove
    //         $pagesToAdd    = array_diff($pageIds, $existingPages); // naye pages
    //         $pagesToRemove = array_diff($existingPages, $pageIds); // hataane wale pages
    
    //         // Step 7: Remove permissions if needed
    //         if (!empty($pagesToRemove)) {
    //             DB::table('group_details')
    //                 ->where($fieldName, $fieldValue)
    //                 ->whereIn('gd_page_id', $pagesToRemove)
    //                 ->delete();
    //         }
    
    //         // Step 8: Add new permissions if needed
    //         foreach ($pagesToAdd as $pageId) {
    //             DB::table('group_details')->insert([
    //                 'gd_group_id'         => $type === 'group' ? $groupId : null,
    //                 'gd_allowed_username' => $type === 'user' ? $username : null,
    //                 'gd_page_id'          => $pageId,
    //                 'GD_ALLOWED'          => 'True',
    //                 'gd_entry_by'         => auth()->user()->username ?? 'System',
    //                 'gd_entry_datetime'   => now(),
    //                 'gd_remarks'          => 'Added by bulk assignment',
    //                 'gd_created_at'       => now(),
    //                 'gd_updated_at'       => now(),
    //             ]);
    //         }
    
    //         // Step 9: Save changes
    //         DB::commit();
    
    //         // Step 10: Return success message
    //         return response()->json([
    //             'status'  => 'success',
    //             'message' => count($pagesToAdd) . ' pages added, ' . count($pagesToRemove) . ' pages removed.',
    //         ]);
    
    //     } catch (\Exception $e) {
    //         // Step 11: Handle errors
    //         DB::rollBack();
    
    //         return response()->json([
    //             'status'  => 'error',
    //             'message' => 'Something went wrong: ' . $e->getMessage(),
    //         ], 500);
    //     }
    // }
    














    public function show($id)
    {
        $permission = DB::table('group_details')
            ->leftJoin('pages', 'group_details.gd_page_id', '=', 'pages.page_id')
            ->leftJoin('groups', 'group_details.gd_group_id', '=', 'groups.group_id')
            ->where('group_details.gd_id', $id)
            ->select([
                'group_details.gd_id',
                'group_details.gd_group_id',
                'group_details.gd_page_id',
                'groups.group_name',
                'pages.page_name',
                'pages.page_url'
            ])
            ->first();

        if (!$permission) {
            return response()->json([
                'status' => 'error',
                'message' => 'Permission not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $permission
        ]);
    }

    public function update(Request $request, $id)
    {
        // Check if permission exists
        $existingPermission = DB::table('group_details')->where('gd_id', $id)->first();
        if (!$existingPermission) {
            return redirect()->back()->with('error', 'Permission not found');
        }

        // Handle status toggle only
        if ($request->has('status')) {
            try {
                $newStatus = $request->status ? 'Enabled' : 'Disabled';
                DB::table('group_details')
                    ->where('gd_id', $id)
                    ->update([
                        'GD_ALLOWED' => $newStatus,
                        'gd_updated_at' => now(),
                    ]);

                $statusText = $request->status ? 'enabled' : 'disabled';
                return redirect()->back()->with('success', "Permission {$statusText} successfully");
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Failed to update status: ' . $e->getMessage());
            }
        }

        return redirect()->back()->with('error', 'Invalid request');
    }

    public function destroy($id)
    {
        try {
            $permission = DB::table('group_details')->where('gd_id', $id)->first();
            
            if (!$permission) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Permission not found'
                ], 404);
            }

            DB::table('group_details')->where('gd_id', $id)->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Permission deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete permission: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getGroups()
    {
        $groups = DB::table('groups')->get();
        return response()->json($groups);
    }


    
    public function getGroupPages(Request $request)
    {
        $assignmentType = $request->input('assignment_type');
        $groupId = $request->input('group_id');
        $username = $request->input('username');
    
        // Start base query
        $query = DB::table('group_details')
            ->join('pages', 'group_details.gd_page_id', '=', 'pages.page_id')
            ->select(
                'group_details.gd_id',
                'group_details.gd_group_id',
                'group_details.gd_allowed_username',
                'group_details.gd_page_id',
                'pages.page_name',
                'pages.page_url',
                'group_details.GD_ALLOWED'
            );
        // Apply filters
        if ($assignmentType === 'group') {
            // Group-level only
            $query->where('group_details.gd_group_id', $groupId);
        } elseif ($assignmentType === 'user') {
            // User-level + Group-level both
            $query->where(function ($q) use ($username, $groupId) {
                $q->where('group_details.gd_allowed_username', $username)
                  ->orWhere('group_details.gd_group_id', $groupId);
            });
        }
        // Fetch and return
        $groupPages = $query->get();
        return response()->json($groupPages);
    }
    




} 