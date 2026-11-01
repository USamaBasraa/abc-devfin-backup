<?php

namespace App\Http\Controllers\Grader\antiInspect;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AllowedInspectUserController extends Controller
{
    /**
     * Display the list of allowed inspection users
     */
    public function index()
    {
        $allowedUsers = DB::table('allowed_inspect_users')
            ->orderBy('user_id')
            ->get();
            
        return view('grader.antiInspect.allowed_anti_inspect-', compact('allowedUsers'));
    }

    /**
     * Store a new allowed inspection user or update existing one
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|string|max:40',
            'reason' => 'required|string|max:255',
            'status' => 'required|in:E,D',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                    'message' => 'Validation failed'
                ], 422);
            }
            
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Validation failed. Please check the form.');
        }
        
        try {
            // Check if it's an update (id is present) or a new record
            if ($request->filled('id')) {
                $id = intval($request->input('id'));

                // Check if record exists before updating
                $record = DB::table('allowed_inspect_users')->where('id', $id)->first();
                if (!$record) {
                    if ($request->ajax()) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Record not found for update.'
                        ], 404);
                    }
                    
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'Record not found for update.');
                }

                // Update existing record
                $updated = DB::table('allowed_inspect_users')
                    ->where('id', $id)
                    ->update([
                        'user_id' => $request->user_id,
                        'reason' => $request->reason,
                        'status' => $request->status
                    ]);
                
                if (!$updated) {
                    if ($request->ajax()) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Update failed. No changes were made.'
                        ], 500);
                    }
                    
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'Update failed. No changes were made.');
                }
                
                $message = 'User updated successfully.';
            } else {
                // Check if user already exists (case-insensitive)
                $existingUser = DB::table('allowed_inspect_users')
                    ->whereRaw('LOWER(user_id) = ?', [strtolower($request->user_id)])
                    ->first();
                
                if ($existingUser) {
                    $status = $existingUser->status == 'E' ? 'Enabled' : 'Disabled';
                    $errorMessage = "This user is already in the allowed list with status '{$status}'. Please edit the existing record.";
                    
                    if ($request->ajax()) {
                        return response()->json([
                            'success' => false,
                            'message' => $errorMessage
                        ], 422);
                    }
                    
                    return redirect()->back()
                        ->withInput()
                        ->with('error', $errorMessage);
                }
                
                // Insert new record
                $inserted = DB::table('allowed_inspect_users')->insert([
                    'user_id' => $request->user_id,
                    'reason' => $request->reason,
                    'status' => $request->status,
                    'created_at' => now()
                ]);
                
                if (!$inserted) {
                    if ($request->ajax()) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Failed to insert record.'
                        ], 500);
                    }
                    
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'Failed to insert record.');
                }
                
                $message = 'User added successfully.';
            }
            
            // If it's an AJAX request, return JSON response
            if ($request->ajax()) {
                // Get the updated list of users
                $allowedUsers = DB::table('allowed_inspect_users')
                    ->orderBy('user_id')
                    ->get();
                    
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'data' => $allowedUsers
                ]);
            }
            
            // For regular form submission
            return redirect()->route('allowed.inspect.users')
                ->with('success', $message);
                
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
    
    /**
     * Delete an allowed inspection user
     */
    public function delete(Request $request, $id)
    {
        try {
            $id = intval($id);
            
            // Check if record exists
            $record = DB::table('allowed_inspect_users')->where('id', $id)->first();
            if (!$record) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Record not found.'
                    ], 404);
                }
                
                return redirect()->back()->with('error', 'Record not found.');
            }
            
            // Delete the record
            DB::table('allowed_inspect_users')->where('id', $id)->delete();
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'User removed successfully.'
                ]);
            }
            
            return redirect()->route('allowed.inspect.users')
                ->with('success', 'User removed successfully.');
                
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
} 