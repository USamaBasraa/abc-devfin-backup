<?php

namespace App\Http\Controllers\Grader\LectureScheduling;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use App;
use App\Models\Grader\FacultyCourses;
use Illuminate\Support\Facades\Validator;
use Storage;
use Hypweb\Flysystem\GoogleDrive\GoogleDriveAdapter;
use Google_Client;



class StudyMaterialController extends Controller{

    function notification($co_id,$message,$user_id,$target_page){
         //echo $co_id."___".$message."__".$user_id;exit();
        try {
         $procedurenotification = 'notification';
         $bindings_notification = [
               'co_id'  => $co_id,
               'desc'  => $message,
               'user_id'  =>$user_id,
               'target_page'=>$target_page

           ];
      return $result_notification = DB::executeProcedure($procedurenotification, $bindings_notification);
     } catch (Exception $e) {
         //echo $e;
       //  echo 'no';
         // This calls the render() method of `App\Exceptions\Handler`
     }
    }

    public function course_material($get_co_id){

        $co_id              = my_simple_crypt($get_co_id, 'd' );
        // $sql_uploads        =  "select * from FACULTY_COURSE_UPLOADS where co_id = ".$co_id;
        $course_handouts    = DB::table('FACULTY_COURSE_UPLOADS')->where('co_id',$co_id)->orderBy('lecture_no','DESC')->get();
       // print_r($course_handouts); exit;
        $course_name        = course_name($co_id);
        $action = 'Faculty';

        return view('grader/LectureScheduling.course_material',compact('course_handouts','course_name','get_co_id','action'));
    }


    function delete_faculity_course_material_by_id($attachmentID){


        $obj = DB::table('faculty_course_uploads')->where('ID', '=', $attachmentID)->first();
        $file=Storage::disk('google')->exists($obj->file_original_name);
        if($file){

            try {
                $delete = Storage::disk('google')->delete($obj->file_original_name);

              } catch (Exception $e) {

                $res = array('status' => false, 'message' => 'An error occurred: '.$e->getMessage());
                return response()->json($res);
              }

              //DB::table('faculty_course_uploads')->where('ID', '=', $attachmentID)->delete();
        }
        if($obj->type == 442){
            $path = public_path() . '/docs/' . $obj->file_original_name;
            @unlink($path);
        }else{

            $path = public_path() . '/docs/' . $obj->file_name;
            @unlink($path);
        }

      DB::table('faculty_course_uploads')->where('ID', '=', $attachmentID)->delete();

        $res = array('status' => true, 'message' => 'Content Delete successfully!');
        return response()->json($res);
    }


    function lecture_UploadVideoView(Request $request,$co_id,$lec_id,$tt_id){

        $course_data        = '';
        $fc                 = new FacultyCourses();
        $teacher_courses    = $fc->fetchDashboardCourses();
        $subjectTitle       = '';
        foreach($teacher_courses as $teacher_course){
                $course_id=$teacher_course->fc_co_course_id;
              $sql_uploads = "select * from FACULTY_COURSE_UPLOADS where (co_id ='$course_id' or co_id='$co_id' )";
                 //echo $sql_uploads;exit;
                $course_data = DB::select($sql_uploads);

                if($teacher_course->fc_co_id == $co_id){
                    $subjectTitle = $teacher_course->course_cd_name;
                }
        }
        return view('grader.teacher.course_videoupload',['teacher_courses'=>$teacher_courses,'subjectTitle' => $subjectTitle,'fc_co_id'=>$co_id,'course_data'=>$course_data,'lec_id'=>$lec_id,'tt_id'=>$tt_id]);
    }


    function add_faculity_course_material_by_id($co_id,$attachmentID){
       $emp_user_id=auth()->user()->employee_id;
         $sql_update="UPDATE FACULTY_COURSE_UPLOADS
                SET CO_ID = '$co_id',
                    video_status = 1
                 where id='$attachmentID'";

        $course_update = DB::insert($sql_update);

        $sel="select *from FACULTY_COURSE_UPLOADS where id='$attachmentID'";
        $getRes = DB::select($sel);
        $material='coursematerial-'.$attachmentID.'-'.$getRes[0]->mtt_id;

        $this->notification($co_id,'Video Uploaded  ',$emp_user_id,$material);
        $res = array('status' => true, 'message' => 'Video Post successfully!');
        return response()->json($res);
    }
    function remove_faculity_course_material_by_id($co_id,$attachmentID){
        $sql_update="UPDATE FACULTY_COURSE_UPLOADS
                SET
                    video_status = 0
                 where id='$attachmentID'";

        $course_update = DB::insert($sql_update);


        $res = array('status' => true, 'message' => 'Attachment Video remove successfully!');
        return response()->json($res);
    }




    function uploadFileWithApi(Request $request){



        $validator = Validator::make($request->all(), [
           // 'lec_content' => 'required',
            'course_file' => "required|mimes:doc,pdf,docx,txt,zip,jpeg,ppt,pptx,jpg,png,csv,mp3|max:20000"
            // 'fc_co_id' => 'required',
            // 'course_id' => 'required'
        ],[
            'course_file.max' => 'File size is not allow more then 20MB'
        ]);

        if ($validator->fails()) {
            // 406 is for content not found from user agent
            return response()->json(['status' => false, 'message' => $validator->errors()->first()],406);
        }



        $coID           = $request->get('co_id');
        $title          = $request->get('file_title');
        $file_mtt_id    = $request->get('file_mtt_id');
        $file_lec_no    = $request->get('file_lec_no');
        $course_id      = $request->get('file_course_id');
        $employeeID = auth()->user()->employee_id;

        date_default_timezone_set('Asia/karachi');
        $curr_time      = date("His");



        $count = DB::table('faculty_course_uploads')->where('co_id',$course_id)->where('type',442)->count();

        if ($request->hasFile('course_file')) { /* FILE UPLOAD */
            $file               = $request->file('course_file');
            $file_name          = $file->getClientOriginalName();
            $name               = $file->getClientOriginalName();
            $file_full_name     = $request->get('file_course_id') . '_'.$count.'_' .$curr_time.'_'. $employeeID . '.' . $file->getClientOriginalExtension();
            $image['filePath']  = $name;
        }


        $file->move(public_path() . '/docs/', $file_full_name);
        if(file_exists(public_path() . '/docs/'.$file_full_name)) {

           $result= DB::table('faculty_course_uploads')->insertGetId([
                'title' => $title,
                'description' => '',
                'due_date' => '',
                'marks' => '',
                'late_submission_allowed' => '',
                're_submission_allowed' => '',
                'file_name' => @$file_name,
                'file_original_name' => $file_full_name,
                'entry_user_id' => $employeeID,
                'type' => '442',
                'co_id' => $coID,
                'entry_date_time' => date('Y-m-d h:i:s'),
                'lecture_no' => $file_lec_no,
                'mtt_id' => $file_mtt_id
            ]);
			if($result){
				 //This Code is sending Push notificaition (Helper Function)   
                 DB::table('testing')
                 ->insert(
                      ['description' => 'Update Step 1 co_id = '.$coID.'__Title : '.$title. gethostname().'------'.$_SERVER['SERVER_ADDR'].'-- 114__Resonse After Update starts']
                 );
				 $push = sendPush($coID,$title,'Course Material Upload:','coursematerial');
				  DB::table('testing')
                 ->insert(
                      ['description' => 'Update Step 4 co_id = '.$coID.'__Title : '.$title. gethostname().'------'.$_SERVER['SERVER_ADDR'].'-- 114__Resonse '.$push]
                 );
                // Push notification code Ends here
				
				
			}

            // if (App::environment('local')) {

            // }else{
               // echo '123';exit;
            //   $material='coursematerial-'.$result.'-'.$file_mtt_id;
            //   $this->notification($course_id,'File Uploaded : '.$title,$employeeID, $material);
               // $this->notification($course_id,'File Uploaded : '.$title,'42','coursematerial');
           // }




            $data = array('status' => true, 'message' => 'File Upload Successfully!' );
        }else{
            $data = array('status' => false, 'message' => 'No Uploaded File Due to Network! Please contact with ITRC!');
        }
        return response()->json($data);

    }

    function uploadGoogleDrive($file,$name){

        try {
            //$dir=Storage::disk('google')->makedirectory('zain');

                //$upload= $request->file("uploadfile")->store("",'google');
                $upload= Storage::disk('google')->putFileAs("",$file,$name);

                $url = Storage::disk('google')->url($upload);
                 $details=Storage::disk("google")->getMetadata($upload);
                $file_name=$details['path'];

            } catch (Exception $e) {

               $res = array('status' => false, 'message' => 'An error occurred: '.$e->getMessage());
               return response()->json($res);
           }
           $res = array('status' => true, 'data'=>$url,'message' => 'Upload Successfully');
           return response()->json($res);
    }


    function uploadFileGoogle(Request $request){

        $validator = Validator::make($request->all(), [
            'file_title' => 'required',
             'uploadfile' => "required|max:2100500"

        ],
        [
            'uploadfile.required' => 'File upload must be less 2GB.!',
        ]
    );

    if ($validator->fails()) {
        return response()->json(array('status' => false,'data'=> [], 'message' => $validator->errors()->first()) );
    }

   // echo 'heloo';exit;
    $file=$request->file("uploadfile");
    $extention=$file->getClientOriginalExtension();

    $name=$request->file("uploadfile")->getClientOriginalName();
    $employeeID = auth()->user()->employee_id;
    $title = $request->get('file_title');
    $file_lec_no = $request->get('file_lec_no');
    $file_mtt_id = $request->get('file_mtt_id');
    $co_id = $request->get('file_course_id');
    $CurrTime=time();
    date_default_timezone_set("Asia/Karachi");

    $name = $CurrTime.'_'. $employeeID . '.' . $file->getClientOriginalExtension();

    //dd($request->all());
    $url='';
    $dir = '/';
    $recursive = false; 
	// Get subdirectories also?
    //$contents = \Storage::disk('google')->listContents($dir, $recursive);
    //$id=Storage::disk("google")->putFileAs("",$file,$ddd);
    //$id=Storage::disk("google")->put(file_get_contents($file),$name);
    // $fi=Storage::disk('google')->makedirectory('hh');
    //$fi=Storage::disk('google')->directories();
    //$diec=$fi;
    //$delete = Storage::disk('google')->putFileAs($diec,$file,$name);
    error_reporting(E_ALL);
    ini_set("display_errors", 1);

    try {
       
                $upload= Storage::disk('google')->putFileAs("",$request->file("uploadfile"),$name);
                //$upload= $request->file("uploadfile")->store("",'google');
                $url = Storage::disk('google')->url($upload);
                 $details=Storage::disk("google")->getMetadata($upload);
                $file_name=$details['path'];
                // $client = new \Google_Client();
                // $client->setClientId(config('filesystems.disks.google.clientId'));
                // $client->setClientSecret(config('filesystems.disks.google.clientSecret'));
                // $client->refreshToken(config('filesystems.disks.google.refreshToken'));

                // $service = new \Google_Service_Drive($client);
                
                //     $fileMetadata = new \Google_Service_Drive_DriveFile(array(
                //         'name' => 'your folder1',
                //         'parents' => array(config('filesystems.disks.google.folderId')),
                //         'mimeType' => 'application/vnd.google-apps.folder'));
                       
                // $folderId = $service->files->create($fileMetadata, array(
                //         'fields' => 'id'));
                       
                //     $file_name = $request->file('uploadfile')->getClientOriginalName();

                // $fileMetadata = new \Google_Service_Drive_DriveFile(array(
                //         'name' => $file_name,
                //         'parents' => array($folderId->id)
                //     ));
                //    // dd($fileMetadata);
                // $file = file_get_contents($request->file('uploadfile'));

                // $fileId= $service->files->create($fileMetadata, array(
                //         'data' => $file,
                //         'mimeType' => 'image/jpeg',
                //         'uploadType' => 'multipart',
                //         'fields' => 'id'));

                //         $url='https://drive.google.com/open?id='.$fileId->id;
                  

                  
        } catch (Exception $e) {
        
           $res = array('status' => false, 'message' => 'An error occurred: '.$e->getMessage());
           return response()->json($res);
       }



    // try {
    //     //$dir=Storage::disk('google')->makedirectory('zain');
    //      		$upload= Storage::disk('google')->putFileAs("",$request->file("uploadfile"),$name);

    //         //$upload= $request->file("uploadfile")->store("",'google');
    //         $url = Storage::disk('google')->url($upload);
    //          $details=Storage::disk("google")->getMetadata($upload);
    //         $file_name=$details['path'];

    //     } catch (Exception $e) {

    //        $res = array('status' => false, 'message' => 'An error occurred: '.$e->getMessage());
    //        return response()->json($res);
    //    }

					   $result=DB::table('faculty_course_uploads')->insertGetId([
						'title' => $title,
						'description' => '',
						'due_date' => '',
						'marks' => '',
						'late_submission_allowed' => '',
						're_submission_allowed' => '',
						'file_name' => '',
						'file_original_name' => $file_name,
						'entry_user_id' => $employeeID,
						'type' => '445',
						'co_id' => $co_id,
						'entry_date_time' => date('Y-m-d h:i:s'),
						'lecture_no' => $file_lec_no,
						'mtt_id' => $file_mtt_id,
						'google_drive'=>$url
					]);
					
					
				
         try {
			 	 //This Code is sending Push notificaition (Helper Function)   
                 DB::table('testing')
                 ->insert(
                      ['description' => 'Update Step 1 co_id = '.$co_id.'__Title : '.$title. gethostname().'------'.$_SERVER['SERVER_ADDR'].'-- 114__Resonse After Update starts']
                 );
				 $push = sendPush($co_id,$title,'Course Material Upload on Google Drive:','coursematerial');
				  DB::table('testing')
                 ->insert(
                      ['description' => 'Update Step 4 co_id = '.$co_id.'__Title : '.$title. gethostname().'------'.$_SERVER['SERVER_ADDR'].'-- 114__Resonse '.$push]
                 );
                // Push notification code Ends here
               // $material='coursematerial-'.$result.'-'.$file_mtt_id;
              //  $this->notification($co_id,'File Uploaded : '.$title,$employeeID, $material);
            } catch (Exception $e) {

                // $res = array('status' => false, 'message' => 'File Uploaded Successfully: '.$e->getMessage());
                // return response()->json($res);
            }

            $data['message'] = 'File Upload Successfully!';
            $data['filepath'] = $url;
            $data['extension'] =  $extention;
        if($result){
            $data = array('status' => true, 'data'=>$data);
            return response()->json($data);
        }

    }










}
