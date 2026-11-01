<?php

namespace App\Http\Controllers\Grader\LectureScheduling;

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
//use Google_Client; 
use App\Models\User;
use Illuminate\Support\Facades\Validator;

//use Google_Service_Drive;
class TimeTableController extends Controller
{
   

    public function uploadFile(Request $request)  {


        $validator = Validator::make($request->all(), [
            'file_title' => 'required',
             'uploadfile' => "required|max:500000"
           
        ],
        [
            'uploadfile.required' => 'File upload must be less 500MB.!',
        ]
    );

    // if ($validator->fails()) {
    //     return response()->json(array('status' => false,'data'=> [], 'message' => $validator->errors()->first()) );
    // }
  
        $sess = new sessions();
        $sess->startSession();
       //dd($request->all());
       $file=$request->file("uploadfile");
        $name=$request->file("uploadfile")->getClientOriginalName();
       
        $login_id = $sess->emp_user_id;
        
        $title = $request->get('file_title');
        $video_lec_no = $request->get('file_lec_no');
        $video_mtt_id = $request->get('file_mtt_id');
        $co_id = $request->get('file_course_id');

        $service = new \Google_Service_Drive($this->gClient);
      $sel="select get_teachername(fc_co_id)from faculty_courses
      where fc_co_id=$co_id";
      //$res=DB::select($sel);


        //$id=Storage::disk("google")->putFileAs("",$file,$ddd);
       //$id=Storage::disk("google")->put(file_get_contents($file),$name);
      // $fi=Storage::disk('google')->makedirectory('hh');
       //$fi=Storage::disk('google')->directories();
       //$diec=$fi;
      //$delete = Storage::disk('google')->putFileAs($diec,$file,$name);
     
       try {
        //$dir=Storage::disk('google')->makedirectory('zain');
       
            $upload= $request->file("uploadfile")->store("",'google');
            $url = Storage::disk('google')->url($upload);
            
             $details=Storage::disk("google")->getMetadata($upload);
                 $path=$details['path'];
        

                
        } catch (Exception $e) {
        
           $res = array('status' => false, 'message' => 'An error occurred: '.$e->getMessage());
           return response()->json($res);
       }
   
        //////////////////////////

        // $fileMetadata = new \Google_Service_Drive_DriveFile(array(
        //     'name' => 'ExpertPHP',
        //     'mimeType' => 'application/vnd.google-apps.folder'));
        // $folder = $service->files->create($fileMetadata, array(
        //     'fields' => 'id'));
        // printf("Folder ID: %s\n", $folder->id);
           
       
       //$google_oauthV2 = new \Google_Service_Oauth2($this->gClient);
       //if ($request->get('code')){
         //  $request->session()->put('token', $this->gClient->getAccessToken());
       //}
 //
        // $file = new \Google_Service_Drive_DriveFile(array(
        //                 'name' => 'cdrfile.jpg'
        //                 //'parents' => array($folder->id)
        //             ));
        //             dd($file);
        // $result = $service->files->create($file, array(
        //   'data' => file_get_contents(public_path('images/1.jpg')),
        //   'mimeType' => 'application/octet-stream',
        //   'uploadType' => 'media'
        // ));
       // get url of uploaded file
       // $url='https://drive.google.com/open?id='.$result->id;
       // dd('kk');


      
            
        $sqlUploadvideo = "INSERT INTO FACULTY_COURSE_UPLOADS(

            TITLE,
            DESCRIPTION,
            DUE_DATE,
            MARKS,
            LATE_SUBMISSION_ALLOWED,
            RE_SUBMISSION_ALLOWED,
            FILE_NAME,
            FILE_ORIGINAL_NAME,
            ENTRY_USER_ID,
            TYPE,
            CO_ID,
            ENTRY_DATE_TIME	,
            mtt_id,
            lecture_no,
            video_status,
            google_drive

      ) VALUES (

            '$title',
            '',
            '',
            '',
            '',
            '',
            '',
            '$path',
            '$login_id',
            '444',
            '$co_id',
            CURRENT_TIMESTAMP,
            '$video_mtt_id',
            '$video_lec_no',
            '0',
            '$url'
            )";
       // echo $sqlUploadvideo;exit;
   
        $result = DB::insert($sqlUploadvideo);

        return response()->json(['status'=>true,'message'=>'You have successfully upload file.']);
        dd($url);





        $dir = '/';
        $recursive = false; // Get subdirectories also?
        $contents = \Storage::disk('google')->listContents($dir, $recursive);
        //dd($request->file('uploadfile'));
        $file=$request->file('uploadfile')->getClientOriginalName();
        $upload=$request->file('uploadfile')->store("",'google');
        //\Storage::disk('google')->put('test12.txt', 'Hello World');
        //$folder=\Storage::disk('google')->makeDirectory("zain");
        //\Storage::disk('google')->('', $request->file('uploadfile'), $file);
        //$folder->$request->file('uploadfile')->store(" ",'google');
       
       
       // $request->file('uploadfile')->store(" ",'google');

    }




    public function uploadFileUsingAccessToken(){
        $service = new \Google_Service_Drive($this->gClient);
        $user=User::find(3695);
       // dd($this->gClient->setAccessToken($user->access_token,true));
        $this->gClient->setAccessToken($user->access_token,true);
        if ($this->gClient->isAccessTokenExpired()) {
           
            // save refresh token to some variable
            $refreshTokenSaved = $this->gClient->getRefreshToken();
            // update access token
            $this->gClient->fetchAccessTokenWithRefreshToken($refreshTokenSaved);               
            // // pass access token to some variable
            $updatedAccessToken = $this->gClient->getAccessToken();
            // // append refresh token
            $updatedAccessToken['refresh_token'] = $refreshTokenSaved;
            //Set the new acces token
            $this->gClient->setAccessToken($updatedAccessToken);
            
            $user->access_token=$updatedAccessToken;
            $user->save();                
        }
        
       $fileMetadata = new \Google_Service_Drive_DriveFile(array(
            'name' => 'ExpertPHP',
            'mimeType' => 'application/vnd.google-apps.folder'));
        $folder = $service->files->create($fileMetadata, array(
            'fields' => 'id'));
        printf("Folder ID: %s\n", $folder->id);
           
        
        $file = new \Google_Service_Drive_DriveFile(array(
                        'name' => 'cdrfile.jpg',
                        'parents' => array($folder->id)
                    ));
        $result = $service->files->create($file, array(
          'data' => file_get_contents(public_path('images/myimage.jpg')),
          'mimeType' => 'application/octet-stream',
          'uploadType' => 'media'
        ));
        // get url of uploaded file
        $url='https://drive.google.com/open?id='.$result->id;
        dd($result);
            
    }
    function deleteFile($service, $fileId) {
        try {
          $service->files->delete($fileId);
        } catch (Exception $e) {
          print "An error occurred: " . $e->getMessage();
        }
      }
    public function getTimeTable_Data(Request $request){
        $userID=$request->facultyID;
        $empID=auth()->user()->employee_id;
        $StudentSessID= auth()->user()->profile_settings->active_session;
    //dd(auth()->user()->employee_id);
        $query_log = " SELECT
        get_teachername(mtt_co_id) AS teacher_name,
        get_course_name(mtt_co_id) AS course_name,
        mtt_co_id AS co_course_id,
        tt_subject_day,
        tt_subject_time_from AS start_time,
        tt_subject_time_to AS end_time,
        tt_class_date,
        mtt_co_id
    FROM
        master_timetable
    WHERE
            1 = 1
        AND
            substr(tt_class_date,1,10) >= substr(
                TRIM(SYSDATE),
                1,
                10
            )
        AND
            mtt_co_id IN (
                SELECT
                    fc_co_id
                FROM
                    faculty_courses
                WHERE
                        faculty_courses.fc_employee_id = $empID 
                    AND
                        fc_co_sess_id = 153
            )
        AND
            tt_status = 'E'
         ORDER BY tt_class_date OFFSET 0 ROWS FETCH NEXT 100 ROWS ONLY";
        
    
            return DB::select($query_log);
    
               
    }
    
    public function gettimetable(Request $request,$co_id){
        // @dd("123");
     $fc_co_id=my_simple_crypt($co_id, 'd' );

				$teacher_timetable  = "SELECT
                                        get_teachername(mtt_co_id) AS teacher_name,
                                        get_course_name(mtt_co_id) AS course_name,
                                        tt_subject_day,
                                        tt_subject_time_from,
                                        tt_subject_time_to,
                                        tt_class_date,
                                        room_name
                                    FROM
                                        master_timetable
                                        left join building_rooms on tt_room_name=room_id
                                    WHERE
                                            1 = 1
                                        AND
                                            mtt_co_id IN (
                                                SELECT
                                                    faculty_courses.fc_co_id
                                                FROM
                                                    faculty_courses
                                                WHERE 
                                                      1=1
                                                AND
                                                  fc_co_sess_id = ".auth()->user()->profile_settings->active_session."
                                                AND  
                                                  faculty_courses.fc_employee_id = ".auth()->user()->employee_id."
                                                AND 
                                                   faculty_courses.fc_co_id=".$fc_co_id."
                                                AND   
                                                    fc_status = 'E'
                                            )
                                        AND
                                            tt_status = 'E'
                                    ORDER BY tt_class_date,course_name,TT_SUBJECT_TIME_FROM desc";
                                   
        $row_teacher_courses = DB::select($teacher_timetable);
       // echo $teacher_timetable;  exit;
                         $sel="SELECT
                                    *
                                FROM
                                    faculty_courses
                                    left join TIMETABLE on tt_co_id=faculty_courses.FC_CO_ID
                                WHERE
                                        fc_co_sess_id =".auth()->user()->profile_settings->active_session."
                                       
                                        AND
                                            fc_employee_id = ".auth()->user()->employee_id." 
                                        AND
                                             fc_status='E'
                                        AND  
                                            faculty_courses.fc_co_id=$fc_co_id     
                                            ORDER BY 
                                            CASE
                                            WHEN tt_subject_day = 'Sunday' THEN 1
                                            WHEN tt_subject_day = 'Monday' THEN 2
                                            WHEN tt_subject_day = 'Tuesday' THEN 3
                                            WHEN tt_subject_day = 'Wednesday' THEN 4
                                            WHEN tt_subject_day = 'Thursday' THEN 5
                                            WHEN tt_subject_day = 'Friday' THEN 6
                                            WHEN tt_subject_day = 'Saturday' THEN 7
                                        END ASC, timetable.TT_SUBJECT_TIME_FROM desc  ";
                                                $row_sel = DB::select($sel);

                        //dd($row_sel);
                        //echo $sel ;exit;
            return view(
                        'grader.TimeTable.timetable',
                        [
                            'timetable' => $row_teacher_courses,
                            'weekly'=>$row_sel
                            
                        ]);
    }

    public function getGuidline(Request $request){
       // dd($request->get('key'));
       $key=explode(",",$request->get('key'));
       $ids = join("','",$key); 
       //dd($ids);
        $sql="select *from cms_guidline where key in ('$ids')";
        return $row_sel = DB::select($sql);

      }

      function getStudentTable(){
    // dd("123");
    $sel="SELECT
                    *
                FROM
                    faculty_courses
                    left join TIMETABLE on tt_co_id=faculty_courses.FC_CO_ID
                    left join courses_offered on tt_co_id = co_id
                WHERE
                        fc_co_sess_id =".auth()->user()->profile_settings->active_session."
                    
                        AND
                            fc_employee_id = ".auth()->user()->employee_id." 
                        AND
                            fc_status='E'
                        AND
                            co_status='E'
                        ORDER BY 
                        CASE
                        WHEN tt_subject_day = 'Sunday' THEN 1
                        WHEN tt_subject_day = 'Monday' THEN 2
                        WHEN tt_subject_day = 'Tuesday' THEN 3
                        WHEN tt_subject_day = 'Wednesday' THEN 4
                        WHEN tt_subject_day = 'Thursday' THEN 5
                        WHEN tt_subject_day = 'Friday' THEN 6
                        WHEN tt_subject_day = 'Saturday' THEN 7
                    END ASC,  timetable.TT_SUBJECT_TIME_FROM_id desc";
                $row_sel = DB::select($sel);

           

            return view(
                'grader.TimeTable.semester_timetable',
                [
                    'weekly' => $row_sel,
                    
                ]);
    }


   
   
}
