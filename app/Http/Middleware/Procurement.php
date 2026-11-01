<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Procurement
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if user is authenticated
        if (!Auth::check()) { 
            return redirect()->route('login')->with('error', 'Please login to access this page.');
        }

        $user = auth()->user();
       
        $employeeId = $user->employee_id;
        $groupId = $user->group_id;

        // Allow if user's group_id is 644
        if ($groupId == 644) {
            return $next($request);
        }

        // Check if user has permission in SP_APPROVAL_MASTERS table
        $hasPermission = DB::table('sp_approval_masters')
            ->where('allowed_username', $employeeId)
            ->where('type', 'budget_permission')
            ->exists();
      
        // If user has permission, allow access
        if ($hasPermission) {
            // dd('Inside IF - should proceed');
            return $next($request);
        }

        // dd('After IF - will abort');
        
        // If no permission, deny access
        abort(403, 'You do not have permission to access Store Procurement module.');
    }
}

