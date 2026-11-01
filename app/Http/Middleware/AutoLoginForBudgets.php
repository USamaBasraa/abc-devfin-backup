<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AutoLoginForBudgets
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            $username = env('AUTOLOGIN_USERNAME', 'zain.ali');
            $password = env('AUTOLOGIN_PASSWORD', 'mahib');

            $attempted = false;
            if (!empty($username) && !empty($password)) {
                $attempted = Auth::attempt(['username' => $username, 'password' => $password]);
            }

            if (!$attempted) {
                $user = User::where('username', $username)->first();
                if ($user) {
                    Auth::login($user);
                } else {
                    $id = (int) env('AUTOLOGIN_USER_ID', 0);
                    if ($id > 0) {
                        Auth::loginUsingId($id);
                    }
                }
            }
        }

        return $next($request);
    }
}


