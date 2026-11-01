<?php

namespace App\Http\Controllers\Grader;

use App\Http\Controllers\Controller;

use App\Models\Grader\CourseAssessmentMarks;
use Illuminate\Http\Request;
use App\Http\Requests\StoreAssessmentMarks;
use Illuminate\Support\Facades\DB;
use App\sessions;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Arr;


class CourseAssessmentMarksController extends Controller
{
    private $sortBy = 'id';
    private $sortDirection = 'ASC';

    private $courseMarks;

    public function __construct(CourseAssessmentMarks $courseMarks)
    {
        $this->courseMarks = $courseMarks;
    }

    public function index()
    {
        //TBD
    }
    public function save_marks(Request $request){
        dd($request->all());
    }
  public function update_totalmarks($ccd_id,$marks){

   $sqlupdateassignmentmarks = "UPDATE   Course_assessment_marks
                                             SET   TOTAL_MARKS = $marks
                                            WHERE  course_criteria_detail_id='$ccd_id'";

        $result = DB::update($sqlupdateassignmentmarks);
         return response()->json(array('status' => true, 'data' => [], 'message' => 'Record Update Successfully'));



    }

    public function assessment_marks($co_id,$ass_type,$num,$ccd_id){
        //dd('ff');
        $sqlCAM = "INSERT INTO course_assessment_marks
        (
        co_id,
        sc_co_id,
        assessment_id,
        course_criteria_id,
        TOTAL_MARKS,
        cam_number,
        obtained_marks,
        SAI_ENROLLMENT_NO,
        sai_id,
        course_criteria_detail_id,
        cam_status,       
        cam_marks_status,
        approval_status
        )
    SELECT
        '$co_id',
        sc_co_id,
        '$ass_type' as assessment_id,
        (select max(course_criteria_id) from course_assessment_marks where co_id = $co_id and assessment_id = $ass_type and cam_number = $num) as cc_id,
        (select max(total_marks) from course_assessment_marks where co_id = $co_id and assessment_id = $ass_type and cam_number = $num) as total_marks,
        '$num' as cam_number,
        '', 
        SC_SAI_ENROLLMENT_NO,
        sai_id,
        '$ccd_id',
        'E',          
        1,
        0
    FROM STUDENT_COURSES
    LEFT JOIN students_apply_info ON SC_SAI_ENROLLMENT_NO = SAI_ENROLLMENT_NO
    WHERE SC_CO_ID IN
    (SELECT CO_ID FROM COURSES_OFFERED WHERE CO_ID = $co_id
    OR PARENT_CO_ID = $co_id
    )
    AND SC_STATUS IN ('A','Block')
    and sc_enrollment_status = 1
    AND sc_sai_enrollment_no NOT IN (
    SELECT
        sai_enrollment_no
    FROM
        course_assessment_marks
    WHERE
        co_id = $co_id
        AND assessment_id = $ass_type
        AND cam_number = $num
     )
    and sai_id is not null";
//echo $sqlCAM;exit;
$resultCAM = DB::insert($sqlCAM);
  $studentsList = $this->courseMarks->fetchStudentsList($co_id,$ass_type,$num);

        // if($studentsList){
            //return $studentsList;
              return view('grader/teacher.course_assessmentmarks', ['studentsList' => $studentsList,'co_id'=>$co_id,'ccd_id'=>$ccd_id,'ass_type'=>$ass_type,'num'=>$num]);
//         }else{

//             return $studentsList;
// //            return $report = ([
// //                'success' => false,
// //                'error' => true,
// //                'error_details' => 'Error In Query'
// //            ]);
//         }



    }
    public function updateDescMarks(Request $request){

        $cam_details    = $request->input('cam_details');
        $cam_date       = $request->input('cam_date');
        $total_marks    = $request->input('total_marks');
        $ccd_id         = $request->input('course_criteria_id');

        $chk_total_marks = "select max(obtained_marks) as max_obtained_marks from course_assessment_marks
        left join student_courses on student_courses.sc_co_id=course_assessment_marks.sc_co_id and sc_sai_enrollment_no=sai_enrollment_no
        where course_criteria_detail_id = $ccd_id and sc_status in ('Block','A') and sc_enrollment_status=1";
        $rec = DB::select($chk_total_marks);
		$maxMarks = $rec[0]->max_obtained_marks;
    //    echo   $total_marks<$rec[0]->max_obtained_marks;
       if($rec[0]->max_obtained_marks <= $total_marks)
        
        {

            $update_total_marks = DB::table('course_assessment_marks')
            ->where('course_criteria_detail_id',$ccd_id)
            ->update(['total_marks' => $total_marks]);


            $update_title_Date = DB::table('course_criteria_details')
                ->where('id',$ccd_id)
                ->update(['ccd_date' => $cam_date,'ccd_details' => $cam_details]);
            if($update_title_Date || $update_total_marks){
                return response()->json(array('status' => true, 'data' =>$maxMarks, 'message' => 'Record Update Successfully'));
            }else{
                return 0;
            }
        }else{
            return response()->json(array('status' => true, 'data' => [], 'message' => 'Total Marks Cannot be less than Obtained Marks'));
        }

    }
    public function retrieveAssessmentList($co_id,$ass_id)
    {
        $assessmentList = $this->courseMarks->fetchAssessmentList($co_id,$ass_id);

        if($assessmentList){
            return $assessmentList;
        }else{
            return $report = ([
                'success' => 'Data saved Successfully',
                'error' => false,
                'error_details' => false
            ]);
        }
    }
    public function retrieveStudentsList($co_id,$ass_type,$num)
    {
        //echo 123;exit();
        $studentsList = $this->courseMarks->fetchStudentsList($co_id,$ass_type,$num);

       //dd($studentsList);
        if($studentsList){
            return $studentsList;
        }else{


            return $studentsList;
//            return $report = ([
//                'success' => false,
//                'error' => true,
//                'error_details' => 'Error In Query'
//            ]);
        }
    }
    public function storet(StoreAssessmentMarks $request){
        $data = $request->all();
       // print_r($data);exit();
        $sai_enrollment_no = $request->input('sai_enrollment_no');
        $marks_id = $request->input('marks_id');
        $obtained_marks = $request->input('obtained_marks');
        $co_id = $request->input('co_id');
        $assessment_id = $request->input('assessment_id');
        $course_criteria_id = $request->input('course_criteria_id');
        $cam_number = $request->input('cam_number');
        $total_marks = $request->input('total_marks');
        $course_criteria_details_id = $request->input('course_criteria_details_id');

        // print_r($sai_enrollment_no);exit();
        $array_index = 0;
        foreach($sai_enrollment_no as $rec_sai_enrollment_no){
//echo $rec_sai_enrollment_no;
////echo $marks_id[$array_index];
//echo "<br>";
//echo $obtained_marks[$array_index];
//echo $marks_id[$array_index];
            if($marks_id[$array_index]==""){
                	$sqlCAM = "INSERT INTO course_assessment_marks
                    (
                    co_id,
                    sc_co_id,
                    assessment_id,
                    course_criteria_id,
                    TOTAL_MARKS,
					OBTAINED_MARKS,
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
                        '$assessment_id' as assessment_id,
                        '$course_criteria_id' as cc_id,
                        '$total_marks' as total_marks,
						'$obtained_marks[$array_index]' as total_marks,
                        '$cam_number' as cam_number,
                        SC_SAI_ENROLLMENT_NO,
                        sai_id,
                        '$course_criteria_details_id',
                        'E',
                        'NO'
                    FROM STUDENT_COURSES
                    LEFT JOIN students_apply_info ON SC_SAI_ENROLLMENT_NO = SAI_ENROLLMENT_NO
                    WHERE SC_CO_ID IN
                    (SELECT CO_ID FROM COURSES_OFFERED WHERE CO_ID = $co_id
                    OR PARENT_CO_ID = $co_id
                    )
                    AND SAI_ENROLLMENT_NO='$rec_sai_enrollment_no'";
                 $resultCAM = DB::insert($sqlCAM);

            }else{
                if($obtained_marks[$array_index]!=""){
                $sqlupdateassignmentmarks = "UPDATE Course_assessment_marks
                                      SET OBTAINED_MARKS = '$obtained_marks[$array_index]',
                                          TEACHER_COMMENTS = 'comment',
                                          SUBMISSION_STATUS='GRADED'
                                      WHERE  id='$marks_id[$array_index]'";
                $result = DB::insert($sqlupdateassignmentmarks);
                }
            }
            $array_index++;
        }

    }
    public function send_sms($message){

        $to='923017169678';	//$to='923017169678';
       // $message='teacher change marks '.date("l")." ". date("h:m:ss d-m-Y");;

        $type = "xml";
        $id = "rchbnuro";
        $pass ='786_kareemallahpak';
        $lang = "English";
        $mask ="BNU";


        //$message='nadeem.alivi login @'.date("l")." ". date("d-m-Y");
        //$to='923017169678';
        $data = "id=".$id."&pass=".$pass."&msg=".urlencode($message)."&to=".$to."&lang=".$lang."&mask=".$mask."&type=".$type;
        // Send the POST request with cURL
        $ch = curl_init('http://www.outreach.pk/api/sendsms.php/sendsms/url');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch); //This is the result from Outreach
        curl_close($ch);


    }
    public function update_marks_all(Request $request){
		
		//return $request->all();
	//	exit();
    
        $get_all_ids        	= $request->get('marks_id');
        $get_all_marks      	= $request->get('user_enter_marks');
        $all_sai_enrollment_no  = $request->get('sai_enrollment_no');
		$co_id  				= $request->get('co_id');
        
        $total_records  = count($request->get('marks_id'));
        $array_index = 0;
        while($array_index<$total_records){

            $sai_enrollment_no  = $request->get('sai_enrollment_no')[$array_index];
            $id                 = $request->get('marks_id')[$array_index];
            $marks              = ($request->get('user_enter_marks')[$array_index]!=""?$request->get('user_enter_marks')[$array_index]:'');

            try {
				
				//echo 'in all code';exit();
              
				$this->update_marks($request,$id,$sai_enrollment_no,$marks,'TIME',$co_id);
				
              
              } catch (\Exception $e) {
              
                  //return $e->getMessage();
              }
            $array_index++;
        }

        return response()->json(array('status' => true, 'data' => [], 'message' => 'Obtained Marks Updated'));


    }
    public function checkAuthForMarksUpdate($co_id,$userToken){
		  $checkPermission="SELECT
     fc_co_id,
     fc_employee_id
 FROM
     faculty_courses left
     JOIN courses_offered ON co_id = fc_co_id
 WHERE
     1 = 1
     AND fc_co_id IN (
         SELECT
             CASE
                 WHEN parent_co_id IS NULL THEN co_id
                 WHEN parent_co_id = 0 THEN co_id
                 ELSE parent_co_id
             END AS main_co_id
         FROM
             courses_offered
         WHERE
             co_id = '".$co_id."'
     )
     AND fc_employee_id = (
         SELECT
             employee_id
         FROM
             sisusers
		 WHERE
     api_token = '".$userToken."'  and rownum = 1 ) ";
	// exit();
      
        if(count(DB::select($checkPermission))>0){
            return true;
        }else{
            return false;
        };
    }
    
    public function update_marks(Request $request,$id,$sai_enrollment_no,$marks,$updated_at,$co_id){

        $userToken = auth()->user()->api_token;//$request->bearerToken();
        $chekAuth=$this->checkAuthForMarksUpdate($co_id,$userToken);
		//echo $userToken."__".$chekAuth;exit();
        if($chekAuth>0){
				//echo  $userToken ."__".$chekAuth.'bilal in';exit();
            $rec = DB::select("select check_course_grading('$sai_enrollment_no','$id') as get_rec from dual");
            if((int)$rec[0]->get_rec > 0 ){
                return response()->json(array('status' => false, 'data' => [], 'message' => 'NOTALLOWED'));
            }
            $marks            = ($marks=='NAN'?'null':$marks);
            $cam_marks_status = (is_numeric($marks)=='1'?'0':'1');
             
            if($updated_at == 'undefined'){
                $sqlupdateassignmentmarks = "UPDATE  course_assessment_marks
                                                SET   OBTAINED_MARKS = $marks
                                                ,cam_marks_status = $cam_marks_status
                                                ,approval_status = 0
                                                ,updated_at = sysdate
                                                WHERE  id='$id'
                                                and  sai_enrollment_no='$sai_enrollment_no'
                                                and $marks <= TOTAL_MARKS";
    
            $result = DB::update($sqlupdateassignmentmarks);
            return response()->json(array('status' => true, 'data' => [], 'message' => 'Record Update Successfully'));
            }else{
            $sqlupdateassignmentmarks = "UPDATE  course_assessment_marks
                                              SET   OBTAINED_MARKS = $marks
                                              ,cam_marks_status = $cam_marks_status
                                              ,approval_status = 0
                                              ,change_datetime = sysdate
                                            WHERE  id='$id'
                                            and  sai_enrollment_no='$sai_enrollment_no'
                                            and $marks <= TOTAL_MARKS";
            $result = DB::update($sqlupdateassignmentmarks);
            return response()->json(array('status' => true, 'data' => [], 'message' => 'Record Update Successfully'));                            
            }
        }
        else{
        return 'Invalid User Request';
        }

     }

      public function storeMarks(StoreAssessmentMarks $request){

        $data = $request->all();
      //dd($data );
        $obt_marks =$data['obt_marks'];
         $old_obt_marks=$data['old_obt_marks'];
          $sai_enrollment_no=$data['sai_enrollment_no'];

        if($data['assessment_id']==3 || $data['assessment_id']==4)
        {
            UserNotificationsController::sendNotifications($data['co_id'],$data['assessment_id']);
        }

        if(CourseCriteriasController::checkLockStatus($data['co_id'])!=1){


            return $this->courseMarks->storeStudentsMarks($data);
        }else{
            return " Semester Working Locked For This Course";
        }
    }

    public function storeMarksApproval(Request $request)
    {
        $data = $request->all();
        return $this->courseMarks->storeStudentsApproval($data);
    }

    public function resultFinalization(Request $request)
    {
        $loginUser = Auth::user();
        $emp_user = $loginUser::find($loginUser->id);
        //echo $emp_user->school_id;
       // dd( $emp_user);
        if($emp_user->school_id==1)
        {
			
             $procedureName = 'ASSESSMENT_MARKS_SVAD';
			
            $bindings = [
                'co_id'  => $request->rf_co_id,
            ];

            $result = DB::executeProcedure($procedureName, $bindings);
            return response()->json($result);

        }else{

            $procedureName = 'assassment_marks';

            $bindings = [
                'co_id'  => $request->rf_co_id,
            ];

            $result1 = DB::executeProcedure('update_criteria_details', $bindings);
            $result2 = DB::executeProcedure('grading_method_management', $bindings);

            $result = DB::executeProcedure($procedureName, $bindings);
            return response()->json($result);
        }
    }

    public function updateCriteriaDetails(Request $request)
    {
        $loginUser = Auth::user();
        $sisuser_id = $loginUser::find($loginUser->id)->id;
        $procedureName = 'CRITERIAS';

        $bindings = [
            'co_id' => $request->rf_co_id,
            'user_id' => $sisuser_id,
        ];

        $result = DB::executeProcedure($procedureName, $bindings);
        return response()->json($result);

    }

    public function retrieveApprovalStudentsList($co_id,$ass_type,$num)
    {
        $studentsList = $this->courseMarks->fetchApprovalStudentsList($co_id,$ass_type,$num);

        if($studentsList){
            return $studentsList;
        }else{

            return $studentsList;
//            return $report = ([
//                'success' => false,
//                'error' => true,
//                'error_details' => 'Error In Query'
//            ]);
        }
    }

    public function deleteCourseMarksSummaries($co_id)
    {

        if($co_id!='')
        {
            $query_delete = "DELETE FROM course_marks_summaries WHERE
                                co_id IN (
                                    SELECT
                                        co_id
                                    FROM
                                        courses_offered
                                    WHERE
                                            co_id = ".$co_id."
                                        OR
                                            parent_co_id = ".$co_id."
                                )";
           // echo  $query_delete;exit();
            DB::delete($query_delete);

            $query_update      = "UPDATE student_courses SET sc_grading_status=NULL
                                  WHERE sc_co_id IN (
                                        SELECT
                                            co_id
                                        FROM
                                            courses_offered
                                        WHERE
                                                co_id = ".$co_id."
                                            OR
                                                parent_co_id = ".$co_id."
                                    )";
            DB::update($query_update);

            $query_lock_update  = "UPDATE course_criterias SET lock_status=NULL WHERE co_id = ".$co_id;
            DB::update($query_lock_update);

        }

    }

    public function mark_i_grade(Request $request)
    {
        $co_id = Arr::get($request, 'sel_co_id');
        $value = Arr::get($request, 'sc_id_list');
      //  echo $co_id;

        $sql_course_offered_type = "select co_offering_type from courses_offered where co_id = $co_id ";
        $rset = DB::select($sql_course_offered_type);
        $get_course_offering_type = $rset['0']->co_offering_type;
        if($value!=null)
        {
            $var=explode(',',$value);
            foreach($var as $row)
            {
                $query      = "UPDATE student_courses SET sc_grading_status='I' WHERE sc_id=".$row;
                $result     = DB::update($query);
            }
            if($result)
            {
                return 1;
            }
        }else{
            if($get_course_offering_type==1){
                $query = "SELECT
                            sc_id
                        FROM
                            student_courses
                            LEFT JOIN course_assessment_marks ON
                                student_courses.sc_co_id = course_assessment_marks.sc_co_id
                            AND
                                student_courses.sc_sai_enrollment_no = course_assessment_marks.sai_enrollment_no
                        WHERE
                                co_id = ".$co_id."
                            AND
                                assessment_id = 4
                            AND
                                obtained_marks IS NULL";
                $rs = DB::select($query);
            }else{
                $query = "SELECT
                            sc_id
                        FROM
                            student_courses
                            LEFT JOIN course_assessment_marks ON
                                student_courses.sc_co_id = course_assessment_marks.sc_co_id
                            AND
                                student_courses.sc_sai_enrollment_no = course_assessment_marks.sai_enrollment_no
                        WHERE
                                co_id = ".$co_id."
                         AND
                                obtained_marks IS NULL";
                $rs = DB::select($query);
            }

            $result = json_decode(json_encode($rs), true);


            foreach($rs as $row)
            {
                $query      = "UPDATE student_courses SET sc_grading_status='I' WHERE sc_id=".$row->sc_id;
                $result     = DB::update($query);
            }
            if($result)
            {
                return 1;
            }

        }

    }
    public function marks_enter(Request $request){

    
       $assessment_id        = $request->get('assessment_id');
       $co_id            = $request->get('co_id');
       $cam_details        = $request->get('cam_details');
       $total_marks        = $request->get('total_marks');
       $cam_date            = $request->get('cam_date');
       $cam_number            = $request->get('cam_number');


        $error_message      = 'Some Problem with the data, Please contact ITRC';

        $sess = new sessions();
        $sess->startSession();
        $login_id = $sess->emp_user_id;

       

        $DbRaw=" select id from COURSE_CRITERIAS where co_id=$co_id and ASSESSMENT_ID=$assessment_id ";
       // echo $DbRaw;exit;

        $RecDbRaw = DB::select($DbRaw);
        # New Entry in Course Criteria detaila and get the inserted it
        try{
            $course_criteria_id = $RecDbRaw[0]->id;
                $vars = [
                'co_id'              => $co_id,
                'ASSESSMENT_ID'      => $assessment_id,
                'COURSE_CRITERIA_ID' => $course_criteria_id,
                'CCD_NUMBER'         => $cam_number,
                'ccd_details'        => $cam_details,
                'CCD_STATUS'         => 'E',
                'CCD_WEIGHTAGE'      => '0',
                'CCD_DATE'           => $cam_date,
                'CCD_ENTRY_USER'     => $sess->emp_user_id
            ];
            $course_criteria_detail_id = DB::table('COURSE_CRITERIA_DETAILS')->insertGetId($vars);

        }catch (\Exception $e) {
            //echo '123';exit;
            return  $error_message;
            exit();
        }

        if($course_criteria_detail_id){
//echo '123';exit;
        $loginUser = Auth::user();
        $emp_user = $loginUser::find($loginUser->id)->id;

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
        cam_marks_status,
        approval_status,
        cam_entry_user,
        created_at
        )
        SELECT
            '$co_id',
             sc_co_id,
            '$assessment_id' as assessment_id,
            '$course_criteria_id' as cc_id,
            '$total_marks' as total_marks,
            '$cam_number' as cam_number,
            SC_SAI_ENROLLMENT_NO,
            sai_id,
            '$course_criteria_detail_id',
            'E',          
            1,
            0,
            $emp_user,
            sysdate

        FROM STUDENT_COURSES
        LEFT JOIN students_apply_info ON SC_SAI_ENROLLMENT_NO = SAI_ENROLLMENT_NO
        WHERE SC_CO_ID IN
        (SELECT CO_ID FROM COURSES_OFFERED WHERE CO_ID = $co_id
        OR PARENT_CO_ID = $co_id
        )
        AND SC_STATUS IN ('A','Block')
        and sc_enrollment_status = 1
         and sai_id is not null";
        //  echo $sqlCAM;exit;
        $resultCAM = DB::insert($sqlCAM);
           
            return 'A';
        }

    }

}
