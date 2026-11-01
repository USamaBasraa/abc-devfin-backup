<?php

namespace App\Http\Controllers\Grader\GoogleDrive;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use App;
use App\Models\Grader\GoogleDrives;
use App\Models\Grader\CourseAssessmentMarks;
use Illuminate\Support\Facades\Validator;
use Storage;
use Hypweb\Flysystem\GoogleDrive\GoogleDriveAdapter;
use Google_Client;
use Symfony\Component\HttpFoundation\Response;
class GoogleDrivesController extends Controller
{
   
    function uploadFileGoogle(Request $request){

        
         if($request->get('id')=='student'){
            return $this->StudentFileupload($request);
         }else{
            return $this->FacultyFileUpload($request);
    
       
         }
     }

     function FacultyFileUpload(Request $request){
        $validator = Validator::make($request->all(), [
            'file_title' => 'required',
             'uploadfile' => "required|max:2100500"
            ],
            [
                'uploadfile.required' => 'File upload must be less 2GB.!',
            ]);

            if ($validator->fails()) {
                return response()->json(array('status' => false,'data'=> [], 'message' => $validator->errors()->first()) );
            }

            $file=$request->file("uploadfile");
            $extention=$file->getClientOriginalExtension();

            $name=$request->file("uploadfile")->getClientOriginalName();
            $employeeID = $request->get('file_empID');//auth()->user()->employee_id;
            $title = $request->get('file_title');
            $file_lec_no = $request->get('file_lec_no');
            $file_mtt_id = $request->get('file_mtt_id');
            $co_id = $request->get('file_course_id');
            $CurrTime=time();
            date_default_timezone_set("Asia/Karachi");

            $name = $CurrTime.'_'. $employeeID . '.' . $file->getClientOriginalExtension();
            $url='';
            $dir = '/';
            $recursive = false; 

            error_reporting(E_ALL);
            ini_set("display_errors", 1);

             try {
                $upload= Storage::disk('google')->putFileAs("",$request->file("uploadfile"),$name);
                //$upload= $request->file("uploadfile")->store("",'google');
                $url = Storage::disk('google')->url($upload);
                 $details=Storage::disk("google")->getMetadata($upload);
                $file_name=$details['path'];
        } catch (Exception $e) {
        
           $res = array('status' => false, 'message' => 'An error occurred: '.$e->getMessage());
           return response()->json($res);
       }

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
                $material='coursematerial-'.$result.'-'.$file_mtt_id;
               // $this->notification($co_id,'File Uploaded : '.$title,$employeeID, $material);
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


     function StudentFileupload(Request $request)
     {
        //dd($request->all());
         $co_id = $request->get('co_id');
         $std_enrollment= $request->get('sai_enrollment');
         $ccd_id = $request->input('ccd_id');
         $description = $request->input('title_upload');
         $file = $request->file('uploadfile');
         $extention=$file->getClientOriginalExtension();
         $url='';
 
         $validator = Validator::make($request->all(), [
             'title_upload' => 'required',
              'uploadfile' => "required|max:2100500"
         ],
         [
             'uploadfile.required' => 'File upload must be less 2GB.!',
         ]
         );
 
         if ($validator->fails()) {
             return response()->json(array('status' => false,'data'=> [], 'message' => $validator->errors()->first()) );
         }
     
         $name = $file->getClientOriginalName();//.'.'.$file->getClientOriginalExtension();
         $disallow_words = array("'",':',';','\\','/','"','','*','delete','drop','sleep','-','table','where','=','truncate','alter','update','alter table');
         $name = str_replace($disallow_words,'',$name);
         $CurrTime=time();
         date_default_timezone_set("Asia/Karachi");
         $file_name = $request->input('ccd_id').$CurrTime.'_'. $std_enrollment . '.' . $file->getClientOriginalExtension();
 
         try {
                 //$upload= $request->file("uploadfile")->store("",'google');
                 $upload= Storage::disk('google')->putFileAs("",$request->file("uploadfile"),$file_name);
                 $url = Storage::disk('google')->url($upload);
                 $details=Storage::disk("google")->getMetadata($upload);
                 $file_name=$details['path'];
              
                $data['message'] = 'File Upload Successfully!';
                 $data['filepath'] = $url;
                 $data['extension'] =  $extention;
             } catch (Exception $e) {
 
                $res = array('status' => false, 'message' => 'An error occurred: '.$e->getMessage());
                return response()->json($res);
            }
 
            if($upload){
                 $studentFile=new GoogleDrives;
                $student_data         = $studentFile->student_file($url,$std_enrollment,$ccd_id,$file_name,$description,$co_id);
                
               if($student_data==1){
                $data = array('status' => true, 'data'=>$data);
                return response()->json($data);
               }else{
                 $data = array('status' => false, 'data'=>'');
                return response()->json($data);
               }
             }else{
                 $data = array('status' => false, 'data'=>'');
                   return response()->json($data);
              }
              $data = array('status' => false, 'data'=>'');
              return response()->json($data);
         ////////File Uplaod Code End Here////////////////////////////
         // $image['filePath'] = $name;
         // $name = $request->input('ccd_id').'_'. $std_enrollment . '.' . $file->getClientOriginalExtension();
 
         // $file->move(public_path() . '/docs/', $name);
 
         // if(file_exists(public_path() . '/docs/'.$name)) {
 
         //     $std_file=new StudentCourses;
         //    return  $student_data         = $std_file->student_file($url,$std_enrollment,$ccd_id,$name,$description,$co_id);
 
         // }else{
 
         //     return 0;
         // }
 
 
         ////////File Uplaod Code////////
     }
 
 
}
