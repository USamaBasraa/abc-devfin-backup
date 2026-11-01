<?php

namespace App\Http\Controllers\Grader;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaptopMaintenanceController extends Controller
{
    /**
     * Display all laptop maintenance requests for admin
     */
    public function adminIndex()
    {
        $requests = DB::table('laptop_maintenance_requests')
            ->whereNull('request_type')
            ->orderBy('id', 'DESC')
            ->get();

        return view('laptop_maintenance.laptop_index', compact('requests'));
    }

    /**
     * Mark request as complete with remarks
     */
    public function markComplete(Request $request)
    {
       
        // Server-side validation
        $request->validate([
            'request_id' => 'required|integer',
            'remarks' => 'required|string|max:1000',
        ]);

        try {
            // Check if request exists
            $maintenanceRequest = DB::table('laptop_maintenance_requests')
                ->where('id', $request->request_id)
                ->first();

            if (!$maintenanceRequest) {
                return back()->with('error', 'Request not found');
            }

            if ($maintenanceRequest->status == 'completed') {
                return back()->with('error', 'Request is already completed');
            }

            // Update the request
            DB::table('laptop_maintenance_requests')
                ->where('id', $request->request_id)
                ->update([
                    'status' => 'completed',
                    'resolver_remarks' => $request->remarks,
                    'resolved_by'=> auth()->user()->username,
                    'completed_at' => now(),
                    'updated_at' => now()
                ]);

            return redirect('/laptop-maintenance/admin')->with('success', 'Request marked as completed successfully');
            
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Get single request details
     */
    public function getRequestDetails($id)
    {
        try {
            $request = DB::table('laptop_maintenance_requests')
                ->where('id', $id)
                ->first();

            if (!$request) {
                return response()->json([
                    'success' => false,
                    'error' => 'Request not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $request
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

