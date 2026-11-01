<?php

namespace App\Http\Controllers\Grader\Hostel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Carbon;



class HostelLeaveController extends Controller
{
    public function showPendingRequests()
    {



        // Retrieve pending, approved, and declined leave requests
        $pendingRequests = DB::table('hostel_night_leave')
        ->join('sisusers', 'hostel_night_leave.hnl_enrolment_no', '=', 'sisusers.username')
        ->select('sisusers.FULL_NAME', 'hostel_night_leave.*')
        ->where('hostel_night_leave.hnl_approve_status', 'Pending')
        ->get();


        // DB::table('hostel_night_leave')
        // ->where('hnl_approve_status', 'Pending')
        // ->get();

        // $processedRequests = DB::table('hostel_night_leave')
        // ->join('sisusers', 'hostel_night_leave.hnl_enrolment_no', '=', 'sisusers.username')
        // ->select('sisusers.FULL_NAME', 'hostel_night_leave.*')
        // ->where('hostel_night_leave.hnl_approve_status', ['Approved', 'Declined'])
        // ->get();
        $processedRequests = DB::table('hostel_night_leave')
        ->join('sisusers', 'hostel_night_leave.hnl_enrolment_no', '=', 'sisusers.username')
        ->select('sisusers.FULL_NAME', 'hostel_night_leave.*')
        ->whereIn('hostel_night_leave.hnl_approve_status', ['Approved', 'Declined'])
        ->orderBy('hostel_night_leave.HNL_APPROVED_DATE_TIME', 'desc')
        ->get();



        // DB::table('hostel_night_leave')
        //     ->whereIn('hnl_approve_status', ['Approved', 'Declined'])
        //     ->get();

    $pendingCount = $pendingRequests->count();
    $approvedCount = $processedRequests->where('hnl_approve_status', 'Approved')->count();
    $declinedCount = $processedRequests->where('hnl_approve_status', 'Declined')->count();


        return view('Hostel.aprovel_leave_requests', compact('pendingRequests', 'processedRequests', 'pendingCount', 'approvedCount', 'declinedCount'));

    }

    public function approveRequest(Request $request,$id)
    {
        // dd($request->approve_remarks);
                // Validate the reason for approval
       // $request->validate(['hnl_remarks' => 'required']);

        // Approve the request by updating the status and adding an approval reason
       $result = DB::table('hostel_night_leave')
            ->where('hnl_id', $id)
            ->update([
                'hnl_approve_status' => 'Approved',
                'hnl_approved_date_time' => DB::raw('CURRENT_TIMESTAMP'),
               'hnl_remarks' => $request->approve_remarks
            ]);

        return redirect()->route('leave-requests')->with('success', 'Request approved successfully.');
    }

    public function declineRequest(Request $request, $id)
    {

        // dd($request->decline_reason);

        // Validate the reason for declining
       // $request->validate(['hnl_remarks' => 'required']);

        // Decline the request by updating the status and adding a decline reason
        DB::table('hostel_night_leave')
            ->where('hnl_id', $id)
            ->update([
                'hnl_approve_status' => 'Declined',
                'hnl_approved_date_time' => DB::raw('CURRENT_TIMESTAMP'),
                'hnl_remarks' => $request->decline_reason
            ]);

        return redirect()->route('leave-requests')->with('success', 'Leave request declined successfully.');
    }



}
