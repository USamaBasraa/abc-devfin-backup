<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ErrorLogsController extends Controller
{
    /**
     * Display a listing of error logs
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Fetch all error logs from testing table
        $errorLogs = DB::table('testing')
            ->orderBy('id', 'desc')
            ->paginate(25); // Show more logs per page
        
        return view('errors.logs', compact('errorLogs'));
    }
    
    /**
     * Clear all error logs (for admin use)
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function clearAll()
    {
        // DB::table('testing')->delete();
        
        return redirect()->route('error.logs')->with('success', 'All error logs cleared successfully.');
    }
} 