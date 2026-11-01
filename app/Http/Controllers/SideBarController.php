<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
class SideBarController extends Controller
{

   
    public function sidebardata(Request $request ){
              $sqlll = "select * from groups_module_pages
                        left join modules on gmp_module_id = module_id
                        where gmp_group_id = '".auth()->user()->group_id."'
                        and gmp_project_name='LARAVEL'
                        order by gmp_module_name desc";
        $users = DB::select($sqlll);
        return json_encode($users);
 
    }


    public function checkSharePoinIp(Request $request){

        if (str_contains($request->ip(), '172.20')) {
            // abort(403, 'You are not allowed to access this resource.');
            return redirect('http://share.bnu.edu.pk/sites/facultynstaff');
    }
    else{
        abort(403, 'Campus outside are not allowed to access this resource.');
    }
    }


   public function sidebar(Request $request ){
        $query = "select * from groups_module_pages
        where gmp_group_id = '".auth()->user()->group_id."'
        order by gmp_module_id desc
       ";
      //  return DD(auth()->user());
       $items=DB::select($query);
       return view('staff.sidebar', compact('items'));

   }
}
