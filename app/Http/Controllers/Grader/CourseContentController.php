<?php
/**
 * Created by PhpStorm.
 * User: bilal.saleem
 * Date: 03/03/2020
 * Time: 2:26 PM
 */

namespace App\Http\Controllers\Grader;
use App\Http\Controllers\Controller;
use App\Models\Grader\FacultyCourses;
use DB;
use App\Models\Grader\CoursesOffered;
use Illuminate\Http\Request;
use App\Models\Grader\CourseContent;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Validator;
use phpDocumentor\Reflection\Element;
use PhpOffice\PhpWord\Element\AbstractContainer;
use PhpOffice\PhpWord\Element\Text;
use PhpOffice\PhpWord\IOFactory as WordIOFactory;
use Illuminate\Support\Facades\Input;
use Storage;
use Hypweb\Flysystem\GoogleDrive\GoogleDriveAdapter;
use Google_Client;

class CourseContentController extends Controller
{
    private $facultyCourse;

    public function __construct(FacultyCourses $facultyCourses)
    {
        $this->facultyCourse = $facultyCourses;
        //$this->middleware('auth');
    }
    public function updtcontent(Request $request,$coc_id){
        $updtleccont = $request->get('text');

       // dd( $request->all());
          $upd = "update COURSE_OFFERING_CONTENTS set coc_details = '$updtleccont' where coc_id = $coc_id ";
        return $res2 = DB::update($upd);

    }
	
		 public function course_filePost(Request $request){
        
         $fc_co_id = $request->get('fc_co_id');
         $course_id = $request->get('course_id');
         $fc_co_sess = $request->get('fc_co_sess');
         $employeeID = auth()->user()->employee_id;
         $result='';
        date_default_timezone_set('Asia/karachi');
         $curr_time      = date("His");

       

        if ($request->hasFile('fileCon')) { /* FILE UPLOAD */
            $file               = $request->file('fileCon');
            $file_name          = $file->getClientOriginalName();
            $name               = $file->getClientOriginalName();
            $extention          =$file->getClientOriginalExtension();

            $file_full_name     = $request->get('course_id') .'_' .$curr_time.'_'. $employeeID . '.' . $file->getClientOriginalExtension();
            $image['filePath']  = $name;
        }
        $validator = Validator::make($request->all(), [
           
             'fileCon' => "required|mimes:doc,pdf,PDF,docx|max:20000"

         ],[
            'fileCon.mimes' => 'File need to be upload in docx,doc,pdf',
             'fileCon.max' => 'File size is not allow more then 20MB',
             'fileCon.required' => 'upload File need to be  filled in !'
         ]);
 
         if ($validator->fails()) {
             // 406 is for content not found from user agent
             return response()->json(['status' => false, 'message' => $validator->errors()->first()],406);
         }

        try {
       
           $upload= Storage::disk('google')->putFileAs("",$request->file("fileCon"),$name);
            //$upload=$request->file("uploadfile")->store("",'google');
            $url = Storage::disk('google')->url($upload);
             $details=Storage::disk("google")->getMetadata($upload);
            $file_name=$details['path'];
    
            } catch (Exception $e) {
            
            $res = array('status' => false, 'message' => 'An error occurred: '.$e->getMessage());
            return response()->json($res);
        }

                    try {
                        //dd('hh');
                        $result=DB::table('course_contents')->insert([
                            'CC_CO_ID' => $fc_co_id,
                            'CC_COURSE_ID' =>  $course_id,
                            'CC_LECTURE_NO' =>  '',
                            'CC_SESSION_ID' =>  $fc_co_sess,
                            'CC_TEACHER_ID' =>  $employeeID,
                            'CC_LECTURE_CONTENT' =>'file',
                            'CC_ENTRY_USER_ID' =>  $employeeID,
                            'CC_FILE_ORIGINAL_NAME' =>  $file_name,
                            'CC_FILE_NAME' =>  $url,
                            'CC_ENTRY_DATETIME' => Carbon::now(),
                            'CC_STATUS' => 'E',
                            'CC_LECTURE_Description' => $extention,
                            'CC_TYPE' => 'file'

                        ]);

                    }catch(\Illuminate\Database\QueryException $ex){
                    //dd($ex->getMessage());
                        if ($ex->getCode() == 1) {
                         return response()->json(['status' => false, 'message' => 'File Already Uploaded only one file are allowed!'],406);
                        }else{
                            //$ex->ErrorMessage
                            return response()->json(['status' => false, 'message' => $ex->getMessage()]);
                        }
                    }
           
                    if($result){
                        $res = array('status' => true, 'message' => 'File added Successfully!');
                        return response()->json($res);
                    }
            
    

    }
public function course_fileremove($attachmentID){

    $obj = DB::table('course_contents')->where('ID', '=', $attachmentID)->first();
	
    //dd($obj);
    $file=Storage::disk('google')->exists($obj->cc_file_original_name);
    if($file){

        try {
            $delete = Storage::disk('google')->delete($obj->cc_file_original_name);
            DB::table('course_contents')->where('ID', '=', $attachmentID)->delete();


          } catch (Exception $e) {

            $res = array('status' => false, 'message' => 'An error occurred: '.$e->getMessage());
            return response()->json($res);
          }

          $res = array('status' => true, 'message' => 'File Delete successfully!');
        return response()->json($res);
    }
}
    public function course_content($co_id){

        $course_idd         =   '';
        $sqlassessment      =   "select * from Assessments order by id";
        $sqlassessment      =   DB::select($sqlassessment);
        $teacher_courses    =   $this->facultyCourse->fetchDashboardCourses();
		$empId=auth()->user()->employee_id;
        $courses_offered = new CoursesOffered();
        $course_id = $courses_offered::find($co_id)->co_course_id;
        //dd(  $teacher_courses);
        $sql="select ASSESSMENTS.assessment_name,COURSE_CONTENTS.cc_weightage,cc_type,cc_lecture_content,COURSE_CONTENTS.id,COURSE_CONTENTS.final_status from COURSE_CONTENTS
                LEFT JOIN ASSESSMENTS ON COURSE_CONTENTS.CC_ASSESSMENTS = ASSESSMENTS.ID
                where COURSE_CONTENTS.CC_COURSE_ID=$course_id and COURSE_CONTENTS.cc_co_id = $co_id order by ASSESSMENTS.id ";
                //echo $co_id;exit();

        $result         = DB::select($sql);
        foreach($result as $key=>$val){
           $val->cc_lecture_content = iconv('UTF-8', 'UTF-8//IGNORE', $val->cc_lecture_content);
        }
        $countcontents  = count($result);
        $course_name    = course_name($co_id);

        $sqlcontent         =   "select * from COURSE_CONTENTS where CC_COURSE_ID=$course_id and COURSE_CONTENTS.cc_co_id = $co_id and cc_type='content' and cc_teacher_id =$empId order by cc_order asc";
        $sqlcontent2         =   "select * from COURSE_CONTENTS where CC_COURSE_ID=$course_id and COURSE_CONTENTS.cc_co_id = $co_id and cc_type='content' and cc_teacher_id =$empId order by cc_order asc";
        $sqlfile         =   "select * from COURSE_CONTENTS where CC_COURSE_ID=$course_id and COURSE_CONTENTS.cc_co_id = $co_id and cc_type='file' order by cc_order asc";

        
        $criteria="select *from course_criterias
        left join assessments on assessments.id= assessment_id
        where co_id=$co_id";
        $sqlcriteria   =   DB::select($criteria);

       $sqlcontentresult   =   DB::select($sqlcontent);
       // Use iconv to remove invalid UTF-8 characters that can cause empty strings or JSON errors when rendered.
       // For example, if the DB contains a malformed sequence like "\xE0\x80\x80" (invalid 3-byte sequence)
       // or Microsoft specific characters copied from Word, iconv with //IGNORE will strip them out so the rest of the text displays correctly.
       foreach($sqlcontentresult as $key=>$val){
           $sqlcontentresult[$key]->cc_lecture_content = iconv('UTF-8', 'UTF-8//IGNORE', $val->cc_lecture_content);
       }

       $sqlcontentresult2   =   DB::select($sqlcontent2);
       foreach($sqlcontentresult2 as $key=>$val){
           $sqlcontentresult2[$key]->cc_lecture_content = iconv('UTF-8', 'UTF-8//IGNORE', $val->cc_lecture_content);
       }

       $resfile   =   DB::select($sqlfile);
       if(isset($resfile[0]) && $resfile[0] !=''){
        $resfile=$resfile[0];
        //dd('jj' );
       }else{
       
        $resfile=0;
       }
       
        return view('grader/teacher.course_content',[
            'teacher_courses'   => $teacher_courses,
            'courses_content'   => $result,
            'countcontents'     => $countcontents,
             'co_id'         => $co_id,
            'assessments'       => $sqlassessment,
            'sqlcontentresult'  => $sqlcontentresult,
            'sqlcontentresult2'  => $sqlcontentresult2,
            'course_name'       => $course_name,
            'criteria'          =>$sqlcriteria,
            'resfile'          =>$resfile
        ]);
    }

public function course_DescPost(Request $request){


    $validator = Validator::make($request->all(), [
        'description' => 'required|max:4000',
    ]);
    
    if ($validator->fails()) {
        return response()->json(array('status' => false,'data'=> [], 'message' => $validator->errors()->first()) );
    }
    $file_full_name='';
    $file_name='';
    $fc_co_id = $request->get('fc_co_id');
    $description = $request->get('description');
    $course_id = $request->get('course_id');
    $fc_co_sess = $request->get('fc_co_sess');
    $Lectno = $request->get('Lectno');
    $lec_content = $request->get('lec_content');
    $id = $request->get('id');

    $con_id = $request->get('con_id');

    $login_id = getLoginUser('employee_id');
    $sess_id = auth()->user()->profile_settings->active_session;

    //dd($request->all());
    if($description!='' && $id==''){
        //dd($description);
        $sqlcourseassessment = "select *from COURSE_CONTENTS where cc_co_id='$fc_co_id' and cc_type='description'";
        // echo $sqlcourseweightage;exit;
      $result = DB::select($sqlcourseassessment);
        $totassessment = count($result);

         if($totassessment>0){
            $res = array('status' => false, 'message' => 'Description already added!');
             return response()->json($res);
            exit();
        }
		
		$result=DB::table('course_contents')->insert([
                    'CC_CO_ID' => $fc_co_id,
                    'CC_COURSE_ID' =>  $course_id,
                    'CC_LECTURE_NO' =>  $Lectno,
                    'CC_SESSION_ID' =>  $sess_id,
                    'CC_TEACHER_ID' =>  $login_id,
                    'CC_LECTURE_CONTENT' => $description,
                    'CC_ENTRY_USER_ID' =>  $login_id,
                    'CC_FILE_ORIGINAL_NAME' =>  $file_full_name,
                    'CC_FILE_NAME' =>  $file_name,
                    'CC_ENTRY_DATETIME' => Carbon::now(),
                    'CC_STATUS' => 'E',
                    'CC_LECTURE_Description' => $description,
                    'CC_TYPE' => 'description'
                    
                ]);
        

        $res = array('status' => true, 'message' => 'Description added Successfully!');
        return response()->json($res);
         //echo $sqlcourseContent;exit();
    }
    if ($id!=''&& $description!='') {
       // dd('des edit');
	   $affected = DB::table('COURSE_CONTENTS')
              ->where('id', $id)
              ->update(['CC_LECTURE_NO' => $Lectno,
						'CC_LECTURE_CONTENT'=>$description
			  ]);
        /*$sqlupdatecontent = "UPDATE COURSE_CONTENTS
                              SET CC_LECTURE_NO = '$Lectno',
                                  CC_LECTURE_CONTENT = '".$description."'

                              WHERE  id='$id'";*/
        //$result = DB::insert($sqlupdatecontent);
        $res = array('status' => true, 'message' => 'Description Updated Successfully.');
        return response()->json($res);
    }
}

    public function course_contentPost(Request $request){
        $rulesArr   = [
            'fc_co_id' => 'required',
            'course_id' => 'required',
        ];
        if($request->has('content_file')){
            $rulesArr['content_file']   = 'required'; // size:1000
        }else{
             $rulesArr['lec_content']    = 'required|min:3|max:4000';
        }
       $validator = Validator::make($request->all(), $rulesArr,[ 'lec_content.required' => 'Lecture Content is required']);
    
    if ($validator->fails()) {
        return response()->json(array('status' => false,'data'=> [], 'message' => $validator->errors()->first()))->setStatusCode(400,$validator->errors()->first());
    }
       

       
        $file       = $request->file('uploadfile');

        $file_full_name='';
        $file_name='';
        $fc_co_id = $request->get('fc_co_id');
        $description = $request->get('description');
        $course_id = $request->get('course_id');
        $fc_co_sess = $request->get('fc_co_sess');
        $Lectno = $request->get('Lectno');
        $lec_content = $request->get('lec_content');
        $id = $request->get('id');

        $con_id = $request->get('con_id');
        $assessment= $request->get('assessment');
        $weightage = $request->get('weightage');
        $login_id = getLoginUser('employee_id');
        $sess_id = auth()->user()->profile_settings->active_session;
        $importData_arr = [];
        $record_arr =[];
   
         $file = $request->file('content_file');
     
        if( $request->hasFile('content_file') ){
            ////////File Uplaod Code////////
                $file_name  = $fc_co_id.'-'.time().'-'.$file->getClientOriginalName();
               //  $name       = $file->getClientOriginalName();
                //$file_full_name = $request->get('file_course_id') . '_'.'_' .'_'. $sess->emp_user_id . '.' . $file->getClientOriginalExtension();
                $image['filePath'] = $file_name;

                $file->move(public_path() . '/docs/', $file_name);
                $filePath =( public_path() . '/docs/'. $file_name);
            ////////File Uplaod Code End Here////////
            $ext = $file->getClientOriginalExtension();
            if($ext == 'csv'){
                $importData_arr = $this->getCourseContentFromFilePath($filePath);
            }elseif($ext == 'docx'){
                $importData_arr = $this->getCourseContentFrom_Word_FilePath($filePath);
				// print_r($importData_arr);exit;
                if(!$importData_arr){
                    return response()->json(['status' => false, 'message' => 'Invalid File Format.'])->setStatusCode(400,'Invalid File Format.Kindly follow sample format.!');

                }

            }
        }else {
            if($con_id==''){
                $importData_arr[] = $lec_content;
            }
           
        }

        

        //print_r($importData_arr);exit;
    $sel="select max(cc_order) as rec from course_contents where CC_CO_ID=$fc_co_id ";
    $res = DB::select($sel);

        if( count($importData_arr )  > 0 ){
            $i = 0;
           
            $count= $res[0]->rec+1;
        
        foreach ($importData_arr as $importData) {
//!isset($importData[$i])
              if($importData_arr[$i] == '' )
			  { 
			    return response()->json(['status' => false, 'message' => 'Invalid File Format.'])->setStatusCode(400,'Invalid File Format.Kindly follow sample format.!');
			  }

             //if($importData_arr[$i] == ''){ continue; }
             try {
               
                //$lecContent=strtolower($importData_arr[$i]);
                $lecContent=$importData_arr[$i];
                $result=DB::table('course_contents')->insert([
                    'CC_CO_ID' => $fc_co_id,
                    'CC_COURSE_ID' =>  $course_id,
                    'CC_LECTURE_NO' =>  $Lectno,
                    'CC_SESSION_ID' =>  $sess_id,
                    'CC_TEACHER_ID' =>  $login_id,
                    'CC_LECTURE_CONTENT' => $lecContent,
                    'CC_ENTRY_USER_ID' =>  $login_id,
                    'CC_FILE_ORIGINAL_NAME' =>  $file_full_name,
                    'CC_FILE_NAME' =>  $file_name,
                    'CC_ENTRY_DATETIME' => Carbon::now(),
                    'CC_STATUS' => 'E',
                    'CC_LECTURE_Description' => $description,
                    'CC_TYPE' => 'content',
                    'cc_order'=>$count
                ]);
            }catch(\Illuminate\Database\QueryException $ex){ 
              // $ex->getMessage()
              $record_arr[]=$importData_arr[$i];
                 if ($ex->getCode() == 1) {
                //  return response()->json(['status' => false, 'message' => 'Content Already added!'])->setStatusCode(400,'Already added!');
                 }
            }

            $i++;
            $count++;
        }
            
       if($record_arr){
        $res = array('status' => true, 'data' =>$record_arr,'message' => '');

       }else{
        $res = array('status' => true, 'data' =>'','message' => 'Content Upload Successfully!');

       }
        //$res = array('status' => true, 'data' =>$record_arr,'message' => 'Content Upload Successfully!');
        return response()->json($res);
    }

         

         
           
            if($con_id!=''){
                //dd($request->all());
                
                if($request->order_id > $request->new_order){
                
                      $sel="UPDATE COURSE_CONTENTS 
                                    SET CC_ORDER = (CC_ORDER+1)
                                    WHERE CC_CO_ID = $fc_co_id
                                    AND CC_ORDER <= $request->order_id
                                 AND  CC_ORDER >= $request->new_order
                                 AND cc_type !='description' 
                                    ";
                // echo "one";
                 //echo $sel;

//                 ($request->new_order>0?$request->new_order:$request->order_id)

                   $res = DB::insert($sel);

                    /*$sqlupdatecontent = "UPDATE COURSE_CONTENTS
                                        SET CC_LECTURE_NO = '$Lectno',
                                            CC_LECTURE_CONTENT = '$lec_content',
                                            CC_order = '$request->new_order'
                                        WHERE  id='$con_id'";*/
					 $sqlupdatecontent = DB::table('COURSE_CONTENTS')
								  ->where('id', $con_id)
								  ->update(['CC_LECTURE_NO' => $Lectno,
											'CC_LECTURE_CONTENT'=>$lec_content,
											'CC_order'=>$request->new_order
								  ]);					
                   // echo $sqlupdatecontent;exit;
                    //$result = DB::insert($sqlupdatecontent);
                }else{

                   // echo 'two';
                    
                      $sel="UPDATE COURSE_CONTENTS 
                                SET CC_ORDER = (CC_ORDER-1)
                                WHERE CC_CO_ID = $fc_co_id
                                AND CC_ORDER >= $request->order_id
                                AND  CC_ORDER <= $request->new_order
                                AND cc_type !='description' 
                                ";
                  $result = DB::insert($sel);
                // --------------------------////
                    
                $sqlupdatecontent = DB::table('COURSE_CONTENTS')
								  ->where('id', $con_id)
								  ->update(['CC_LECTURE_NO' => $Lectno,
											'CC_LECTURE_CONTENT'=>$lec_content,
											'CC_order'=>$request->new_order
								  ]);	
                }
                $res = array('status' => true, 'message' => 'Content updated Successfully!');
                 return response()->json($res);
            }

    }



    function getCourseContentFromFilePath($filepath){
        $file = fopen($filepath, "r");
        $importData_arr = array();
        $i = 0;
        while (($filedata = fgetcsv($file, 4000,',' )) !== FALSE) {
            $num = count($filedata);
            for ($c = 0; $c < $num; $c++) {
                // print_r($filedata [$c]); exit;
                $importData_arr[] = $filedata [$c];
            }
            $i++;
        }
        fclose($file);
       //  print_r($importData_arr); exit;
        return $importData_arr;
    }





public function assessment(){
    if($assessment!=''){
        $sqlcourseassessment = "select *from COURSE_CONTENTS where cc_course_id='$course_id' and cc_assessments='$assessment'";

       // echo $sqlcourseassessment;exit;
            $result = DB::select($sqlcourseassessment);
                $totassessment = count($result);
                if($totassessment>0){
                    $res = array('status' => true, 'message' => 'Assessment already added!');
                     return response()->json($res);
                    exit();
                }


                $sqlcourseweightage = "INSERT INTO COURSE_CONTENTS(


														CC_CO_ID,
														CC_COURSE_ID,
														CC_LECTURE_NO,
														CC_SESSION_ID,
														CC_TEACHER_ID,
														CC_LECTURE_CONTENT,
														CC_ENTRY_USER_ID,
														CC_FILE_ORIGINAL_NAME,
														CC_FILE_NAME,
														CC_ENTRY_DATETIME,
														CC_STATUS,
														CC_LECTURE_Description,
														CC_TYPE,
														CC_WEIGHTAGE,
														CC_ASSESSMENTS



												  ) VALUES (

														'$fc_co_id',
														'$course_id',
														'$Lectno',
														'$sess_id',
														'$login_id',
														'$lec_content',
														'$login_id',
														'$file_full_name',
														'$file_name',
														CURRENT_TIMESTAMP,
														'E',
														'$description',
														'assessment',
														'$weightage',
														'$assessment'
														)";
                $result = DB::insert($sqlcourseweightage);
                // echo $sqlcourseContent;exit();

            }
           
}
    /** 
    **  FILE UPLOAD AGAINST COUSE CONTENT
    **  
    **/

    function notification($co_id,$message,$user_id,$target_page){
        // echo $co_id."___".$message."__".$user_id;exit();
        try {
         $procedurenotification = 'notification';
         $bindings_notification = [
               'co_id'       => $co_id,
               'desc'        => $message,
               'user_id'     =>$user_id,
             'target_page'  =>$target_page,
 
           ];
      return $result_notification = DB::executeProcedure($procedurenotification, $bindings_notification);    
     } catch (Exception $e) {
         //echo $e;
        // echo 'no';
         // This calls the render() method of `App\Exceptions\Handler`
     }
    }


    function uploadFileWithApi(Request $request){
        
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
            
            DB::table('faculty_course_uploads')->insert([
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


          $this->notification($course_id,'File Uploaded : '.$title,'42','coursematerial');


            $data = array('status' => true, 'message' => 'File Upload Successfully!' );
        }else{
            $data = array('status' => false, 'message' => 'No Uploaded File Due to Network! Please contact with ITRC!');
        }
        return response()->json($data);

    }



    function getCourseContent(Request $request){
        $coID = $request->get('co_id');
        return response()->json(['status' => true, 'data' => CourseContent::getCourseContentsByCoID($coID)]);
    }

    function getCourseContentMain(Request $request){
        $coID = $request->get('co_id');
        return response()->json(['status' => true, 'data' => CourseContent::getCourseContentsByCoID($coID)]);
    }


    function getCourseStudents(Request $request){
        $where = ['sc_co_id' => $request->get('co_id')];
        return [
        'data' => DB::table('student_courses')->where()->get($where),
        'status' => true
        ];
    }


    function getCourseContentFrom_Word_FilePath($filePath=''){
        #help :: https://github.com/PHPOffice/PHPWord/issues/2106
     //    $filePath   =   (public_path() . '/docs/read-word-file11.docx');
	
        $phpWord    =   \PhpOffice\PhpWord\IOFactory::load($filePath);
 
        $objReader  =   WordIOFactory::createReader('Word2007');
        $phpWord    =   $objReader->load($filePath); // instance of \PhpOffice\PhpWord\PhpWord
        $text       =   '';
        $contentArr =   [];
		// print_r($phpWord); exit;
	  //dd($phpWord);
       // dd($phpWord->getSections()[0]);
        foreach ($phpWord->getSections()[0]->getElements()  as $element) {
            if ($element instanceof \PhpOffice\PhpWord\Element\Table) {
                foreach ($element->getrows() as $row) {
                    if ($row instanceof \PhpOffice\PhpWord\Element\Row) {
                        foreach ($row->getcells() as $cell) {
                            if (($cell instanceof \PhpOffice\PhpWord\Element\Cell)) {
                                $content = '';
                                foreach ($cell->getElements() as $child1_Cell) {
                                    if ($child1_Cell instanceof \PhpOffice\PhpWord\Element\TextRun) {
                                        foreach ($child1_Cell->getElements() as $child2_Cell) {
											    try {
													  $content .= $child2_Cell->getText();
													  
												 } catch (\Throwable $e) {
													// dd($e);
													 if($e->getcode()==0){
													 	return 0;
														exit;
													 }

												 }
										
										
										
                                          
                                        }
                                    } // Element
                                }
                                $contentArr[] = $content;
                            }
                        } // foreach
                    }
                } // foreach
            }
        }
       //  echo $text;
        //print_r($contentArr); exit;

      return $contentArr;
    }




}
