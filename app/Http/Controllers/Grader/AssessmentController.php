<?php
/**
 * Created by PhpStorm.
 * User: bilal.saleem
 * Date: 09/07/2018
 * Time: 11:46 AM
 */

namespace App\Http\Controllers\Grader;
use App\Http\Requests\StoreAssessment;
use App\Models\Grader\Assessments;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use DB;

use App\Http\Controllers\Controller;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use App\Models\Grader\FacultyCourses;
use App\Models\Grader\CourseAttendance;
use App\Models\Grader\CourseAssessmentMarks;
use App\sessions;
use App\Models\Grader\Schools;
use App\Models\Grader\EmpEmployees;
use App\Models\Grader\Departments;
use App\Models\Grader\UserNotifications;
use App\Models\Grader\CourseCriterias;
use App\Models\Grader\CourseCriteriaDetails;
Use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;


class AssessmentController extends Controller
{
    public function __construct(Assessments $assess)
    {
        $this->assess = $assess;
    }
    public function retrieveAssessments()
    {
        $assess =  $this->assess->fetchAssessmentss();
        return view('grader.common.assessment', compact('assess'));
    }
    public function retrievemaxOrder()
    {
        return  $this->assess->maxOrder();
        // return view('common.assessment', compact('maxorder'));
    }
    public function create(StoreAssessment $request)
    {
        $data = $request->all();
        return $this->assess->insertAssessment( $data);
    }

    function uploadPostAssessment(Request $request){
     // echo "===".$request->get('fcu_id');exit;

        $sess       = new sessions();
        $sess->startSession();
        $login_id   = $sess->emp_user_id;

        $rules = [
            'marks'         => 'required',
            'title2'        => 'required:max:100',
            'duedate'       =>'required',
            'description'   =>'required|max:1000',
            'assessment_id' => 'required'
        ];
        if($request->get('duedate')){
            $rules['duedate'] =  'required|date_format:Y-m-d|after:yesterday';// . date('Y-m-d');
        }

        if ($request->hasFile('uploadfile2')) {
            if( $request->get('is_file') == 'yes' ){
                $rules['uploadfile2'] =  'required|max:204800|mimes:xlsx,docx,pptx,jpg,JPG,jpeg,pdf,png,zip,inp';
				
				$allowed = array( 'accdb');
				$filename = $request->file('uploadfile2');
				$ext=$filename->getClientOriginalExtension();
				if (!in_array($ext, $allowed)) {
				  return response()->json(array('status' => false,'data'=> [], 'message' => 'The answer file must be a file of type: xlsx, docx, pptx, jpg, jpeg, pdf, accdb, png, zip.'));
				}		
				
				
            }
        }

        $messages = [
            'duedate.required' => ' The Due Date needs to be filled in formate Day-Month-Year.',
            'marks.required' => ' Marks Field to be filled in',
            'description.required' => ' Description Field to be filled in',
            'title2.required' => ' Title Field to be filled in',
            'duedate.date_format' => ' The Due Date needs to be filled in formate Day-Month-Year.',
            'duedate.after' => 'Only Current OR Coming Date Are Allowed.',
            'uploadfile2.max' => 'File size must be less then 200MB!',
            'uploadfile2.mimes' => 'File have only allow xlsx,docx,pptx,jpg,jpeg,pdf and png.'
        ];


//dd($request->all());
        ////////File Uplaod Code End Here////////
        $title          = $request->get('title2');
        $description    = $request->get('description');
        $date           = $request->get('duedate');
        $marks          = $request->get('marks');
        $latesub        = $request->get('latesub');
        $co_id          = $request->get('co_id');
        $resub          = $request->get('resub');
        $type           = $request->get('assessment_id');
        $resultCCD      =0;
        $go             =0;
        $is_assignment  = 1;//($request->get('is_assignment') != '')?1:0;

        //dd($co_id);


        // if( $request->get('is_file') != 'yes' ){
        //     $latesub        = 'No';
        //     $resub          = 'No';
        //     $description = null;
        //     $date = null;
        // }

        $fcu_id=$request->get('fcu_id');

        if($fcu_id == 'insert'){
            $parts      = explode('`', $type);
            $ccd_id     = $parts[1];
            $assess_id  = $parts[0];
        }

        $validator = Validator::make($request->all(), $rules, $messages );
        if ($validator->fails()) {
            return response()->json(['status'=>false, 'message' => $validator->errors()->first()])->setStatusCode(400,$validator->errors()->first());
        }
        ////////File Uplaod Code////////
        if ($request->hasFile('uploadfile2')) {
            $file               = $request->file('uploadfile2');
            $newFileNameArr     = array( $request->get('co_id'), $login_id, time() );
            $fileStr            = implode('-',$newFileNameArr);
            // echo $fileStr; exit;
            $file_name          = $file->getClientOriginalName();
            $file_full_name     = $fileStr.'.'.$file->getClientOriginalExtension();
            $file->move(public_path() . '/docs/', $file_full_name);
        }else{
            $file_full_name         = '';
            $file_name              = '';
        }

        if( trim($date) != '' ){
            $timestamp  = strtotime($date);
            $newDate    = date('Y-M-d', $timestamp);
        }else{
            $newDate    = null;
        }


        if($fcu_id == 'insert') {

                $sqlselect = "select * from course_criteria_details where co_id = $co_id and assessment_id = $assess_id";
                $resultselect = DB::select($sqlselect);
                $countresult = count($resultselect)+1;

                if($assess_id==4 and isset($resultselect[0]->assessment_id) and $resultselect[0]->assessment_id==4){
                    $go=0;
                }else{
                    $go=1;
                }

            if($go==1){

                $resultCCD = DB::table('course_criteria_details')->insert([
                    'CCD_DETAILS'   => $title,
                    'DESCRIPTION'   => $description,
                    'DUE_DATE'      => $newDate,
                    'MARKS'         => $marks,
                    'LATE_SUBMISSION_ALLOWED'   =>    $latesub,
                    'RE_SUBMISSION_ALLOWED'     =>    $resub,
                    'FILE_NAME'                 => 	  $file_full_name,
                    'FILE_ORIGINAL_NAME'        => 	  $file_name,
                    'co_id'                     =>    $co_id,
                    'assessment_id'             => 	  $assess_id,
                    'course_criteria_id'        => 	  $ccd_id,
                    'ccd_number'                => 	  $countresult,
                    'is_assignment'             =>    $is_assignment,
                    'ccd_weightage'             => 		0,
                    'FACULTY_COURSE_UPLOAD_ENTRY'   => 	'1',
                    'ccd_status'                    => 	'E'
                ]);

            }else{
                return response()->json(['status' => false, 'message' => 'Final term already exist.', 'data' =>array()]);
            }
               

                $mess = 'Upload Assessment : '.$title;

           # this call function of notificaiton to send notificaotn in this page
            $this->notification($co_id,$mess,$sess->emp_user_id,'assignment');
           #----------- end of notificaton funciton ------------------------


                if($resultCCD){
                    $sqlcoursecriteriadetail = "select id from course_criteria_details
                       where co_id = $co_id
                       and assessment_id = $assess_id
                       and ccd_number=$countresult";
                    $resultcoursecriteriadetail = DB::select($sqlcoursecriteriadetail);
                    //$resultcoursecriteriadetail[0]->id;
                    foreach($resultcoursecriteriadetail as $rec){
                        $co_criteria_det_id = $rec->id;
                    }
                    if($resultcoursecriteriadetail) {

                        $sqlCAM = "INSERT INTO course_assessment_marks
                    (
                    co_id,
                    sc_co_id,
                    assessment_id,
                    course_criteria_id,
                    TOTAL_MARKS,
                    cam_number,
                    SAI_ENROLLMENT_NO,
                    sai_id,
                    course_criteria_detail_id,
                    cam_status,
                    SUBMISSION_STATUS
                    )
                    SELECT
                        '$co_id',
                         sc_co_id,
                        '$assess_id' as assessment_id,
                        '$ccd_id' as cc_id,
                        '$marks' as total_marks,
                        '$countresult' as cam_number,
                        SC_SAI_ENROLLMENT_NO,
                        sai_id,
                        '$co_criteria_det_id',
                        'E',
                        'NO'

                    FROM STUDENT_COURSES
                    LEFT JOIN students_apply_info ON SC_SAI_ENROLLMENT_NO = SAI_ENROLLMENT_NO
                    WHERE SC_CO_ID IN
                    (SELECT CO_ID FROM COURSES_OFFERED WHERE CO_ID = $co_id
                    OR PARENT_CO_ID = $co_id
                    )
                    AND SC_STATUS IN ('A','Block')
                     and sai_id is not null";
                    $resultCAM = DB::insert($sqlCAM);
                     //  echo $sqlCAM;exit;
                    }
                }
			
         //  echo $sqlUploadAssignment;exit;
            // return response()->json(['success' => 'Data Saved Successfully.']);
            return response()->json(['status' => true, 'message' => 'Data Saved Successfully.', 'data' =>array()]);
        }else{
            $mess = 'Assessment Modified : '.$title;
			

            # this call function of notificaiton to send notificaotn in this page
            $this->notification($co_id,$mess,$sess->emp_user_id,'assignment');
            #----------- end of notificaton funciton ------------------------


            $courseCriteriaArr = [
                'CCD_DETAILS'              =>  $title,
                'DESCRIPTION'              => $description,
                'DUE_DATE'                 => $newDate,
                'MARKS'                    => $marks,
                'LATE_SUBMISSION_ALLOWED'  => $latesub,
                'is_assignment'            => $is_assignment,
                'RE_SUBMISSION_ALLOWED'    => $resub,
                'ASSESSMENT_ID'            => $type
            ];
            if($file_full_name != '' and $file_name != '') {
                $courseCriteriaArr['FILE_NAME'] = $file_full_name;
                $courseCriteriaArr['FILE_ORIGINAL_NAME'] = $file_name;
            }

            $res = DB::table('course_criteria_details')->where('id', $fcu_id)->update($courseCriteriaArr); //->toSQL();

            if($res){
                // check if any assignment submit by student and its marks
                $sqlcheckstdmarks       = "select MAX(obtained_marks) as obtained from course_assessment_marks where co_id = $co_id and course_criteria_detail_id = $fcu_id ";
                $rescheckstdmarks       = DB::select($sqlcheckstdmarks);
                $entered_obtain_marks   = $rescheckstdmarks[0]->obtained;

                if($entered_obtain_marks <= $marks or $entered_obtain_marks =='') {
                    DB::table('course_assessment_marks')->where('course_criteria_detail_id', $fcu_id)->update(['TOTAL_MARKS' => $marks]);
                }else{
                    return response()->json(['status' => false, 'message' => 'Total Marks should be greater than Obtained Marks', 'data' =>array()]);
                }
                //This Code is sending Push notificaition (Helper Function)   
                 DB::table('testing')
                 ->insert(
                      ['description' => 'Update Step 1 co_id = '.$co_id.'__Title : '.$title. gethostname().'------'.$_SERVER['SERVER_ADDR'].'-- 114__Resonse After Update starts']
                 );
				// $push = sendPush($co_id,$title,'Uploaded Assignment Edited:','assignment');
			
                // Push notification code Ends here
            }


            return response()->json(['status' => true, 'message' => 'Data Update Successfully.', 'data' =>array(),  'success' => 'Data Update Successfully.']);
        }
    }

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







}
