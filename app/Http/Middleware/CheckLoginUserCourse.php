<?php

namespace App\Http\Middleware;
use Illuminate\Support\Facades\Auth;

use Closure;
use Illuminate\Http\Request;
use DB;

class CheckLoginUserCourse
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
        // $coID =  $request->route('co_id');

        // if($coID != ''){
        //     if( ! (is_numeric($coID)) ){
        //         $coID = my_simple_crypt($coID,'d');
        //     }
        //     $courseData = (array) DB::table('faculty_courses')->where('fc_employee_id', auth()->user()->employee_id)->pluck('fc_co_id')->toArray();
        //     if(in_array($coID, $courseData)){
        //         return $next($request);
        //     }else{
        //         return redirect('/')->withErrors('Your are not register with this course');
        //     }
        // }else{
        //     return $next($request);
        // }
        $coID =  $request->route('co_id');
        $user = Auth::user();

        if ($coID != '' && !is_numeric($coID)) {
            $coID = my_simple_crypt($coID, 'd');
        }

        if ($user->group_id == 223 || in_array($user->username, ['muddassar.javed','noreen','anam.amin']) ) {
            $coID = my_simple_crypt($coID,'d');
            $schoolCourseIDs = DB::table('faculty_courses')
                ->where('fc_co_school_id', $user->school_id)
                ->pluck('fc_co_id')  // or 'co_id' depending on column name
                ->toArray();
            $coIDCourse = DB::table('faculty_courses')
            ->where('FC_EMPLOYEE_ID', $user->employee_id)
            ->pluck('fc_co_id')  // or 'co_id' depending on column name
            ->toArray();
            if ($coID == '' || in_array($coID, $schoolCourseIDs) || in_array($coID, $coIDCourse)) {
            // if ($coID == '' || in_array($coID, $schoolCourseIDs)) {
                return $next($request);
            } else {
                return redirect('/')->withErrors('You are not allowed to access this course.');
            }
        }else if($coID != ''){
            if( ! (is_numeric($coID)) ){
                $coID = my_simple_crypt($coID,'d');
            }

            $courseData = (array) DB::table('faculty_courses')->where('fc_employee_id', auth()->user()->employee_id)->pluck('fc_co_id')->toArray();       
                if(in_array($coID, $courseData)){
                    return $next($request);
                }else{
                    return redirect('/')->withErrors('Your are not register with this course');
                }
                }else{
            return $next($request);
        }
        
    }
}
