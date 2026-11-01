<?php

namespace App\Http\Middleware;
use Illuminate\Support\Facades\Auth;

use Closure;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Gate;

class VerifyEndpointAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->isMethod('get')) {
            $user = Auth::user();
    
            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
    
            $routeName = $request->route()->getName();
            $routeUri = $request->route()->uri();
            if ($routeName=='' && $routeUri=='' ) {
                return $this->deny($request);
            }
             $routeName= $routeName?$routeName:$routeUri;
            if (!Gate::allows('access-route', $routeName)) {
                return $this->deny($request);
            }
        }
            return $next($request);
        }
        protected function deny(Request $request)
        {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['error' => 'Access denied.'], 403);
            }

            // For normal web requests, redirect or show custom view
            return redirect()->route('unauthorized')->with('error', 'You do not have permission to access this page.');
        }
}
