<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use DB;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

          Gate::define('access-route', function ($user, $routeName) {
            // dd($user->group_id,$routeName);
           $check = DB::table('group_details as gd')
                ->join('pages as p', 'gd.gd_page_id', '=', 'p.page_id')
                ->where(function ($query) use ($user) {
                    $query->where('gd.gd_group_id', intval($user->group_id))
                          ->orWhere('gd.gd_allowed_username', $user->username);
                })
                 ->where('p.page_url', $routeName)
                ->where('gd.gd_allowed', 'True')
                ->exists();
                return $check;
        });
  
    }
}
