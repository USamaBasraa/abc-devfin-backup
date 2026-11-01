<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Grader\CourseAttendance;
use App\Models\Grader\CourseCriterias;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use DB;
use App\Models\Grader\CourseOfferingContents;
use App\Models\Grader\MasterTimetable;
use App\sessions;
use App\Models\Grader\CoursesOffered;
use Google_Client;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Google_Service_Drive;

class GoogleDriveController extends Controller
{


    public function uploadFileGD(Request $request)  {


        $validator = Validator::make($request->all(),
            [   'uploadfile' => "required|max:500000"   ],
            [   'uploadfile.required' => 'File upload must be less 500MB.!',    ]
        );


        try {

            $file = $request->file('file');
            // dd($file);
            $fileName = time().$file->getClientOriginalName();


            $file_name = Storage::disk('google')->putFileAs("", $file, $fileName);
            $url = Storage::disk('google')->url($file_name);
            return response()->json([
                'code' => 200,
                'url' =>  $url
            ]);

        } catch (Exception $e) {

           $res = array('status' => false, 'message' => 'An error occurred: '.$e->getMessage());
           return response()->json($res);
        }

    }
}
