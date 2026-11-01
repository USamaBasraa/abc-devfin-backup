<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AcademicSessions;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class ProfileSettingsController extends Controller{



    function showProfileSetting(){
        $user = User::where('id', getLoginUser('id') )->first();
        return view('grader.profile.settings',['user' => $user]);
    }


    function saveProfileSetting(Request $request){

        $validator = Validator::make($request->all(), [
            // 'mobile_code' => [
            //     'required',
            //     function ($attribute, $value, $fail) {
            //         if ($value != session('mobile_code')) {
            //             $fail('Token not match.');
            //         }
            //     }
            // ]
            'otp_verify' => ['required']
        ]);

        if ($validator->fails()) {
            return redirect('profile/setting')
            ->withErrors($validator)
            ->withInput();
        }
        User::where('id', auth()->user()->id)->update(['otp_verify' => $request->get('otp_verify')]);
        return redirect('/profile/setting')->with('status', 'Profile Setting updated!');
    }

   

}