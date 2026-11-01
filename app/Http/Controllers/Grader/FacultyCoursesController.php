<?php
namespace App\Http\Controllers\Grader;


use App\Http\Controllers\Controller;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Illuminate\Http\Request;
use App\Models\Grader\FacultyCourses;
use App\Models\Grader\CourseAttendance;
use App\Models\Grader\CourseAssessmentMarks;
use App\sessions;
use App\Models\Grader\Schools;
use App\Models\Grader\User;
use App\Models\Grader\EmpEmployees;
use App\Models\Grader\Departments;
use App\Models\Grader\UserNotifications;
use DB;
use Carbon\Carbon;

use Illuminate\Support\Facades\Session;
use App\Models\Grader\SendSms;
use App\Models\Grader\CourseCriterias;
use App\Models\Grader\CoursesOffered;
use App\Models\Grader\CourseCriteriaDetails;
Use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use App\Models\Grader\Flight;

use Validator;

class FacultyCoursesController extends Controller
{
    private $sortBy = 'id';
    private $sortDirection = 'ASC';

    private $facultyCourse;

    public function __construct(FacultyCourses $facultyCourses)
    {
        $this->facultyCourse = $facultyCourses;
        //$this->middleware('auth');
    }





    function notification($co_id,$message,$user_id,$target_page){
        // echo $co_id."___".$message."__".$user_id;exit();
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
    public function retrieve($sess_id=0,$dept_id=0,$email='')
    {
        return $this->facultyCourse->fetch($sess_id,$dept_id,$email);
    }

    public function retrieveCourses()
    {
        return $this->facultyCourse->fetchDashboardCourses();
    }


	function redirectUserByApiToken(Request $request){
        //dd($request->get('token'));
        //$this->guard()->logout();
        Auth::logout();
		auth()->logout();
		$request->session()->invalidate();
		Session::flush();
        return Redirect::to('http://devstudent.bnu.edu.pk/login-user-by-users?token='.$request->get('token'));
		exit();

    }

    public function check_assignment($course_criteria_detail_id,$co_id)
    {
        $courseCriteria = new CourseCriterias();
        $get_criteria = $courseCriteria->fetchCoursesCriteria($co_id);
        $course_name = course_name($co_id);
        $employee_id = auth()->user()->employee_id;
        $my_course_attendance = new CourseAssessmentMarks();
        $student_data         = $my_course_attendance->AssessmentChecking($course_criteria_detail_id,$co_id,$employee_id);

        return view('grader/teacher.check_assignment_stu',
        [
            'course_criteria'=>$get_criteria,
            'show_student_data'=>$student_data,
            'course_name'=>$course_name,
            'new_assessment_entry'=>$course_criteria_detail_id
        ]);

      // return $student_data;




//        $my_course_attendance = new CourseAttendance();
//        $student_data                  = $my_course_attendance->show_students(210979,11166,'NO');
//        return view('teacher.check_assignment_stu',['show_student_data'=>$student_data]);
    }

    /**
     * @return FacultyCourses
     */

    ////delete content

    function delete_faculity_course_content($id,$co_id)
    {
        $obj = DB::table('course_contents')->where('ID', '=', $id)->delete();

        $sel="update COURSE_CONTENTS
                    set cc_order = (
                        select count(*) from COURSE_CONTENTS cc
                        where cc.cc_co_id = COURSE_CONTENTS.cc_co_id
                        and cc.cc_order   <= COURSE_CONTENTS.cc_order
                        and cc.cc_type not in ('description')
                        )
            where cc_co_id = $co_id AND cc_type!= 'description'";

            $result = DB::insert($sel);

        $res = array('status' => true, 'message' => 'Content Delete successfully!');
        return response()->json($res);


    }
    public function course_content_record($co_id)
    {
        $course = "select co_course_id from courses_offered where co_id = $co_id ";
        $resultcourse = DB::select($course);
        $my_course_id =  $resultcourse[0]->co_course_id;
        // exit();
        $sql="
				select * from (
				SELECT id,cc_type,
				cc_lecture_no,
				cc_lecture_content,
					id as coc_id,
					 1 as type,
					  (SELECT COUNT(*)
					FROM course_offering_contents
					WHERE coc_course_content_id = COURSE_CONTENTS.id
					and course_offering_contents.coc_continuation=1
					and coc_content_type = 1
					AND coc_co_id = $co_id
					)
					AS coc_continuation,
					  (SELECT COUNT(*)
					FROM course_offering_contents
					WHERE coc_course_content_id = COURSE_CONTENTS.id
					and course_offering_contents.coc_continuation=1
					and course_offering_contents.coc_covered = 1
					AND coc_co_id = $co_id
					and coc_content_type = 1)  AS coc_covered,
					 1 as coc_content_type

				FROM COURSE_CONTENTS
				where cc_course_id = $my_course_id
				  union all
				 SELECT
					(SELECT MIN(id) FROM COURSE_CONTENTS WHERE cc_co_id = $co_id
					)         AS tt_id,
					'content' AS cc_type,
					(SELECT MIN(id) FROM COURSE_CONTENTS WHERE cc_co_id = $co_id
					)                AS cc_lecture_no,
					COC_DETAILS AS cc_lecture_content,
					coc_course_content_id AS coc_id,
					2 as type,
					to_number(coc_continuation),
					COALESCE (to_number(coc_covered),0),
					2 as coc_content_type
				  FROM COURSE_OFFERING_CONTENTS
				  WHERE coc_co_id       = $co_id
				  AND COC_DETAILS IS NOT NULL
				  and coc_content_type = 2
					and coc_content_entry_type = 2


				  )

				order by id


				";

        $result = DB::select($sql);

        // echo $sql;exit;
        $teacher_courses = $this->facultyCourse->fetchDashboardCourses($co_id);

        return $result;
    }





    /**
     * @return stdupdatemarks_assignment
     */
    ////////// update markx /////
     public function checkAssessment($co_id, $assessment_id)
    {

        $sqlcontent="select * from messages_bnu where co_id=$co_id and COURSE_ASSESSMENT_ID='$assessment_id' order by id";
        return $sqlcontentresult = DB::select($sqlcontent);


    }
    public function stdupdatemarks_assignment(Request $request)
    {

    //    dd($request->all());
       

       $co_id=$request->input('co_id');
        $userToken = Auth()->user()->api_token;
        $chekAuth=checkCourseAccess($co_id,$userToken);
        $sess = new sessions();
        $sess->startSession();
        $flag=0;
        $login_id   = $sess->emp_user_id;
        $obmarks = $request->get('ob-marks');
        $comment = $request->get('comment');
        $idkl = $request->get('idkl');
        $course_criteria_id = $request->get('course_criteria_id');
        $co_id = $request->get('co_id');
        $sai_enrollment = $request->get('sai_enrollment');
        $total_marks= $request->get('total_marks');
        $assess= $request->get('assess_id');
        $cam_number= $request->get('assess_number');
        $file = $request->file('uploadfile');

        $sql_assessment = "select assessment_name,ccd_number from course_criteria_details
        left join assessments on course_criteria_details.assessment_id = assessments.id
        where co_id = $co_id and course_criteria_id = $course_criteria_id and ccd_number = $cam_number and assessment_id = $assess";
        $result_assessment = DB::select($sql_assessment);
        $assessment_name = $result_assessment[0]->assessment_name;
        $camnumber = $result_assessment[0]->ccd_number;
   ////////////////////////////////////////////////////////////////
         $query="select OBTAINED_MARKS from Course_assessment_marks WHERE  id='$idkl'";
        $rset = DB::select($query);
         $get_markx = $rset['0']->obtained_marks;
         if($get_markx !=''){
                $flag=1;
         }

/////////////////////////////////////////////////////////////////////////

          $sqlupdateassignmentmarks = "UPDATE Course_assessment_marks
                                      SET OBTAINED_MARKS = '$obmarks',
                                          TEACHER_COMMENTS = '$comment',
                                          SUBMISSION_STATUS='GRADED'
                                      WHERE  id='$idkl'";
      $result = DB::insert($sqlupdateassignmentmarks);

      /////send sms to faculty ////////////////////////////////////
       $sms = new SendSms();
       $get_course_teacher=$sms->get_course_teacher($co_id, $login_id);

  if(CourseCriteriasController::checkLockStatus($co_id)!=1 && $flag==1){

           //$get_course_teacher=$sms->get_course_teacher($co_id, $login_id);
            $employee_email = $sess->emp_email;
             $phone_no=$sess->phone_no;
            $countlength = strlen($phone_no);
            $chknumber = substr($phone_no,0,2);

         if($countlength == 12){
            if($chknumber == 92){
                $to=$employee_email;
                $subject='Student Marks Changed Alert !'.$get_course_teacher;
                $mess=' Student Marks Changed '.$sai_enrollment .' in '.$assessment_name.' no '.$camnumber.' ( '.$obmarks.'/'.$total_marks. ' ) '.$get_course_teacher;
          //$sms->send_sms('Marks change Alert ! ID '. $sai_enrollment .' in '.$assessment_name.' no '.$camnumber.' '.$obmarks.'/'.$total_marks. ' '.$get_course_teacher,$phone_no);
           // $sms->teacher_marks_change_sendmail($to ,$subject,$mess);
            $flag=0;
              }
                //$to=$employee_email;
                $subject='Student Marks Enter Alert !'.$get_course_teacher;
                $mess=' Student Marks Entered '.$sai_enrollment .' in '.$assessment_name.' no '.$camnumber.' ( '.$obmarks.'/'.$total_marks. ' ) '.$get_course_teacher;
             //$sms->teacher_marks_change_sendmail($to ,$subject,$mess);


        }
/////send sms to faculty //////
        $procedurenotification = 'student_notification';
        $bindings_notification = [
            'main_co_id'           => $co_id,
            'message'              => $obmarks.' / '.$total_marks.' Updated by teacher in'.' '.$assessment_name.' no '.$camnumber,
            'short_message'        =>'Marks Enter',
            'student_enrollment_no'=>$sai_enrollment,
            'entry_user_id'        =>$login_id,
            'targe_page'           =>'assignment',
            'send_type'            =>'student',

        ];

     //$result_notification = DB::executeProcedure($procedurenotification, $bindings_notification);
   // print_r($bindings_notification);exit;

        return response()->json(['success'=>'Marks Saved Successfully.']);

    }

    }


    public function course_discussion($co_id)
    {

        $sql_student_data="SELECT
                            *
                        FROM
                            student_courses
                            LEFT JOIN courses_offered ON sc_co_id = co_id
                            LEFT JOIN courses_two on course_id = courses_offered.CO_COURSE_ID
                            LEFT JOIN students_apply_info ON student_courses.sc_sai_enrollment_no = students_apply_info.sai_enrollment_no
                            LEFT JOIN students_personal_info ON students_apply_info.sai_spi_id = students_personal_info.spi_id
                        WHERE
                                1 = 1
                            AND
                                sc_enrollment_status=1
                            AND
                                 sc_status not in ('Withdrawal')
                            AND (
                                    sc_co_id = $co_id
                                OR
                                    parent_co_id = $co_id
                                )
                            AND  sai_enrollment_no is not null
                        ORDER BY spi_name";

        $users=DB::select($sql_student_data);

       //  print_r($users); exit;

        $sqlselect = "select distinct un_description,emp_employees.emp_imagefile from user_notifications
                        left join emp_employees on un_entry_user_id = employee_id
                        where un_co_id='$co_id'
						and un_type ='notification' ";


        $resselectun = DB::select($sqlselect);



        //$users=array();
        return view('grader.course.course_discussion',['student_data'=>$users,'getnotification'=>$resselectun,'co_id'=>$co_id]);
    }
       public function classdiscussion(Request $request,$co_id){


        $sess       = new sessions();
        $sess->startSession();
        $login_id   = $sess->emp_user_id;
        $discussion = $request->get('discussion');


        $insertAr['un_description']     = $discussion;
        $insertAr['un_co_id']           = $co_id;
        $insertAr['un_entry_user_id']   = $login_id;
        $insertAr['un_type']            = 'notification';
        DB::table('user_notifications')->insert($insertAr);

		$this->notification($co_id,$discussion,$login_id,'notification');

		$sqlStudentApplication = "INSERT INTO messages_bnu(
                                                   message,
                                                   co_id,
                                                   entry_user_id) VALUES (
                                                     '".$discussion."',
                                                   '$co_id',
                                                   '".$login_id."'
                                                   )";
        $result = DB::insert($sqlStudentApplication);

        return response()->json(array('success' => 'Data Saved Successfully.'));

    }
    function sendMessageToStudentByTeacher(Request $request){

        $sess = new sessions();
        $sess->startSession();
        $login_id = $sess->emp_user_id;
        $discussion = $request->get('message');

       //  $sql    = "insert into user_notifications( un_description,un_co_id,un_entry_user_id) VALUES ( '$discussion', '$co_id', '$login_id')";
        $sis_id = DB::table('sisusers')->select('id')->where('username','=',strtolower($request->get('username')))->first();

        DB::table('user_notifications')->insert([
            'un_description' => $sess->emp_user_name." message : ".$discussion,
            'un_to_user_id' => $sis_id->id,
            'un_entry_user_id' => $login_id,
            'un_co_id' => $request->get('co_id'),
            'un_message_id' => $request->get('username'),
            'un_type' => 'message'
        ]);


        return response()->json(['success' => 'Data Saved Successfully.']);

    }



    function getMessageByTeacher(Request $request){

        $sess = new sessions();
        $sess->startSession();
        $sis_id         = DB::table('sisusers')->select('id')->where('username','=',strtolower($request->get('student-id')))->first();
        // print_r($sis_id); exit;
        $whereArr       = array('un_to_user_id' => $sis_id->id, 'un_entry_user_id' => $sess->emp_user_id);
        $notifications  = DB::table('user_notifications')->select('*')
        ->where($whereArr)
        ->get();

        return response()->json(['status' => true, 'message' => 'Data!', 'data' => $notifications]);
    }



    // public function course_content($coc_tt_id){
    //     $sqlcourseofferingcontent = "select * from COURSE_OFFERING_CONTENTS where coc_tt_id =".$coc_tt_id;
    //    return $courseofferingcontent = DB::select($sqlcourseofferingcontent);
    // }

    public function course_site($get_co_id)
    {

        $co_id = my_simple_crypt($get_co_id, 'd' );
        $course_name = course_name($co_id);
        $CourseAttendanceModel  = new CourseAttendance();
        $all_classes = $CourseAttendanceModel->loadClassDatesModelDashboard($co_id,0);
        $sqlcourseofferingcontent = "select * from COURSE_OFFERING_CONTENTS where coc_co_id =".$co_id;
        $courseofferingcontent = DB::select($sqlcourseofferingcontent);

        array_walk($all_classes,function($row,$index) use ($course_name,$courseofferingcontent){
        $content_keys = array_keys(array_column($courseofferingcontent,'coc_tt_id'),$row->tt_id);
        $row->course_name = $course_name;
        $row->courseofferingcontent  = [];
            foreach($content_keys as $cc_row):
                $row->courseofferingcontent [] =  $courseofferingcontent[$cc_row];
            endforeach;
         });

         $courseCriteria = new CourseCriterias();
         $get_criteria = $courseCriteria->fetchCoursesCriteria($co_id);

         return view('grader.teacher.course_site',compact('all_classes','co_id','course_criteria'));
    }
    public function course_site_old($get_co_id)
    {


        $co_id = my_simple_crypt($get_co_id, 'd' );
        $course_name = course_name($co_id);
        $CourseAttendanceModel  = new CourseAttendance();
        $all_classes = $CourseAttendanceModel->loadClassDatesModelDashboard($co_id,0);
        $sqlcourseofferingcontent = "select * from COURSE_OFFERING_CONTENTS where coc_co_id =".$co_id;
        $courseofferingcontent = DB::select($sqlcourseofferingcontent);

        $result = DB::select('select get_course_name(co_id) as course_name from courses_offered where co_id =17543');
       // $result = DB::Table('courses_offered')->select('get_course_name(co_id)')->where('co_id',$co_id)->get();


        /// echo $co_id;exit();
        $sess = new sessions();
        $sess->startSession();
     //    $login_id = $sess->emp_user_id;

        $obj = DB::table('faculty_courses')->where('FC_CO_ID', '=', $co_id)->where('FC_EMPLOYEE_ID', '=', $sess->emp_user_id)->first();


        if(!($obj)){
            abort(404, 'Course not found!.'); exit;
        }


        $courseCriteriaDetails = new CourseCriteriaDetails();
        // $get_criteriadetails = $courseCriteriaDetails->fetchCriteriaDetails(8267);
        $get_criteriadetails = $courseCriteriaDetails->fetchCriteriaDetails($co_id);
       // return $get_criteriadetails;exit();

        //return view('teacher.course_site'
        $sql = "select ccd_details,ccd_number,due_date,assessment_name,get_course_name($co_id) as course_name,co_id ,course_criteria_details.id,marks
        ,(select max(total_marks) from course_assessment_marks
        where course_assessment_marks.co_id = course_criteria_details.co_id
        and course_criteria_details.id = course_assessment_marks.course_criteria_detail_id
        ) as abc,description,file_original_name,course_criteria_details.late_submission_allowed,course_criteria_details.re_submission_allowed,file_name
        from course_criteria_details
        left join assessments on assessment_id = assessments.id
        where co_id = $co_id
        AND FACULTY_COURSE_UPLOAD_ENTRY = '1'

        order by ccd_number";
       // echo $sql;exit();
        $res = DB::select($sql);
        $countres = count($res);



        $sql2 = "select title,
        entry_date_time,
        co_id,
        FACULTY_COURSE_UPLOADS.id
    from FACULTY_COURSE_UPLOADS where co_id =  $co_id";
        $resfiles = DB::select($sql2);
        // return $res2;


        $countsql=" select count(assessment_id) as count,assessment_id,assessment_name
                    from course_criteria_details
                    left join assessments on course_criteria_details.assessment_id = assessments.id
                    where co_id = $co_id
                     AND FACULTY_COURSE_UPLOAD_ENTRY = '1'
                    group by assessment_id,assessment_name";
        $rescountsql = DB::select($countsql);

        $courseCriteria = new CourseCriterias();
        $get_criteria = $courseCriteria->fetchCoursesCriteria($co_id);

     //   return view('teacher.view_assignment',['all_files'=>$resfiles,'all_uploads'=>$res,'criteria'=>$get_criteria,'count'=>$rescountsql,'chkassess'=>$countres]);






       $coursecontent = $this->course_content_record($co_id);

        $sql_uploads = "select * from FACULTY_COURSE_UPLOADS where co_id = $co_id";
        $course_handouts = DB::select($sql_uploads);

        $CourseAttendanceModel  = new CourseAttendance();
        $get_slot_list = $CourseAttendanceModel->loadClassDatesModelDashboard($co_id,0);
        //return $get_slot_list;exit();
        // return view('teacher.course_site');

        $courseCriteria = new CourseCriterias();
        $get_criteria = $courseCriteria->fetchCoursesCriteria($co_id);
    // $get_criteria = $courseCriteria->fetchCoursesCriteria($co_id);


        $courseAssessmentMarks =  new CourseAssessmentMarks();
        $studentsList = $courseAssessmentMarks->fetchStudentsListNew($co_id);

        $sqlcourseofferingcontent = "select * from COURSE_OFFERING_CONTENTS where coc_co_id =  $co_id";
        $courseofferingcontent = DB::select($sqlcourseofferingcontent);



       return view('grader.teacher.course_site',['course_handouts'=>$course_handouts,
                                                'all_files'=>$resfiles,
                                                'all_uploads'=>$res,
                                                'criteria'=>$get_criteria,
                                                'count'=>$rescountsql,
                                                'chkassess'=>$countres,
                                                'studentsList'=>$studentsList,
                                                'get_criteria_details'=>$get_criteriadetails,
                                                'get_criteria'=>$get_criteria,
                                                'all_classes'=>$get_slot_list,
                                                'course_criteria'=>$get_criteria,
                                                'criteria_details'=>$get_criteriadetails,
                                                'content'=>$coursecontent,
                                                'courseofferingcontent'=>$resfilescourseofferingcontent]);
    }






    public function video_save(Request $request)
    {

    //    dd($request->all());

        $sess = new sessions();
        $sess->startSession();
        $login_id = $sess->emp_user_id;
        //dd( 'grgrg');
        $match='';
        $title = $request->get('video_title');
        $video_link = $request->get('videolink');
        $video_lec_no = $request->get('video_lec_no');
        $video_mtt_id = $request->get('video_mtt_id');
        $course_id = $request->get('course_id');
        $co_id = $request->get('co_id');

        if($video_link!=''){
           // dd('link add');
		   	$result=DB::table('FACULTY_COURSE_UPLOADS')->insert([
                    'TITLE' => $title,
                    'DESCRIPTION' =>  '',
                    'DUE_DATE' =>  '',
                    'MARKS' =>  '',
                    'LATE_SUBMISSION_ALLOWED' =>  '',
                    'RE_SUBMISSION_ALLOWED' => '',
                    'FILE_NAME' =>  $video_link,
                    'FILE_ORIGINAL_NAME' =>  $video_link,
                    'ENTRY_USER_ID' =>  $login_id,
                    'TYPE' => 443,
                    'CO_ID' => $co_id,
                    'ENTRY_DATE_TIME' => Carbon::now(),
                    'mtt_id' => $video_mtt_id,
					'lecture_no' => $video_lec_no,
                    'video_status' => 0

                ]);


            return response()->json(['success'=>'You have successfully upload file.']);
        }else if($video_link==''){

          // dd($request->file('file1'));
		    $tim=time();
            if ($request->file('file1')) {
                ////////File Uplaod Code////////
                $file = $request->file('file1');
				    $extention=$file->getClientOriginalExtension();

                $file_name = $file->getClientOriginalName();
                $name = $file->getClientOriginalName();
                $file_full_name = $request->get('course_id').'_'.$video_mtt_id.$tim.'.'.$extention;
                $image['filePath'] = $name;
                $file->move(public_path() . '/docs/',$file_full_name);
                ////////File Uplaod Code End Here////////

            }

				$result=DB::table('FACULTY_COURSE_UPLOADS')->insert([
                    'TITLE' => $title,
                    'DESCRIPTION' =>  '',
                    'DUE_DATE' =>  '',
                    'MARKS' =>  '',
                    'LATE_SUBMISSION_ALLOWED' =>  '',
                    'RE_SUBMISSION_ALLOWED' => '',
                    'FILE_NAME' =>  $file_full_name,
                    'FILE_ORIGINAL_NAME' =>  $file_name,
                    'ENTRY_USER_ID' =>  $login_id,
                    'TYPE' => 444,
                    'CO_ID' => $co_id,
                    'ENTRY_DATE_TIME' => Carbon::now(),
                    'mtt_id' => $video_mtt_id,
					'lecture_no' => $video_lec_no,
                    'video_status' => 0

                ]);

            return response()->json(['success'=>'You have successfully upload file.']);
        }

        exit();


    }






    public function send_sms($message){
        $sess = new sessions();
        $sess->startSession();
        $login_id = $sess->emp_user_id;

        //$message=' login @ grader '.date("l")." ". date("d-m-Y");

        $to = "";
        if($login_id=='3555' || $login_id=='42' || $login_id=='459' || $login_id=='539' ||  $login_id=='401' ||  $login_id=='239'  ||  $login_id=='475' ||  $login_id=='4055'){

            if($login_id=='459'){//'natash.ali'
                $to='923219464209,923017169678';
            }elseif($login_id=='539'){//'saad.saleem'
                $to='923364850065,923017169678';
            }elseif($login_id=='401'){//'shazia'
                $to='923454223437,923017169678';
            }elseif($login_id=='239'){//'huda.sarfraz'
                $to='923214513897,923017169678';
            }elseif($login_id=='475'){//'amna.humayun'
                $to='923244789674,923017169678';
            }elseif($login_id=='4055'){//nadeem_alvi
                $to='923414000140,923017169678';
            }elseif($login_id=='3555'){//zain.ali
                $to='923347280669,923017169678';
            }else{
                $to='923017169678';	//$to='923017169678';
                $message=' mna login @ grader '.date("l")." ". date("d-m-Y");;
            }
        }



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

    /////////////////upload file &video///////////////////
    
public function retrieveDashboardCourses2(Request $request){
    // dd("retrieveDashboardCourses2");
    $sess       = new sessions();
    $sess->startSession();
    // dd($sess);
    $emp_user   = $sess->emp_dept_id;
    if($sess->emp_group_id == 4){
        return '<h1>Limited Access</h1> <br> <a href="'.url('/new_logout').'"> Logout </>';
    }
    //   if(getLoginUser('id') == 5193 or getLoginUser('id') == 5195 or $emp_user==13 or $emp_user==81 or $emp_user==101 or $emp_user==23 or $emp_user==42){
    $login_name     = $sess->emp_user_name;
    $login_ip       = $request->ip();
    $mac            = '';
    $description    = 'login on Grader ';
    $pc             = $_SERVER['HTTP_USER_AGENT'];
    $client_id      = 2;
    $query_log      = "insert into login_log(user_id,password,login_ip,status,pc,mac,description) values ('" . $login_name . "','','" . $login_ip . "','Success','" . $pc . "','" . $mac . "','" . $description . "')";
    $row_teacher_courses = DB::insert($query_log);
    // print_r($sess); exit;
    $CourseAttendanceModel  = new CourseAttendance();
    $get_slot_list          = $CourseAttendanceModel->loadClassDatesModel(10972, 0);
    $faculty_school         = new Schools();
    $faculty_department     = new Departments();
    $profile                = new EmpEmployees();
    $employee_profile       = EmpEmployees::with('user')->where('employee_id', '=', $sess->emp_user_id)->first();
   
$get_all_notifications = DB::select("
    SELECT
        ID AS \"id\",
        TITLE AS \"title\",
        CATEGORY AS \"category\",
        DESCRIPTION AS \"description\",
        IS_READ AS \"is_read\",
        CREATED_AT AS \"created_at\",
        TO_CHAR(
            CREATED_AT,
            'DD Mon YYYY, HH12:MI AM'
        ) AS \"display_date\"
    FROM NOTIFICATIONS
        WHERE STATUS = 1
      AND CATEGORY IN ('Faculty', 'Faculty & Staff')
    ORDER BY CREATED_AT DESC, ID DESC
");
    // $all_notifications      = new UserNotifications();
    // dd($all_notifications);
    // $get_all_notifications  = $all_notifications->fetch_notification(766);
    // $get_all_notifications  = $all_notifications->fetch_notification($sess->emp_user_id);
    // dd($get_all_notifications);
    $profile = array(
        'full_name' => $sess->emp_user_name,
        'school' => $faculty_school->fetch()[$sess->emp_school_id],

        'emp_imagefile' => (isset($employee_profile->emp_imagefile))?$employee_profile->emp_imagefile:'',
        'email' => $employee_profile->emp_email,
        'mobile' => $employee_profile->emp_contact_no
    );
    if($sess->emp_dept_id){
        $profile['department'] = $faculty_department->fetch()[$sess->emp_dept_id];
    }else{
        $profile['department'] = '';
    }
    $teacher_courses = $this->facultyCourse->fetchDashboardCourses();
    $teacher_timetable = "SELECT
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
                                    left join courses_offered on mtt_co_id = courses_offered.co_id
                                WHERE
                                        1 = 1
                                    AND
                                        substr(tt_class_date,1,10) = substr(
                                            TRIM(SYSDATE),
                                            1,
                                            10
                                        )
                                    AND
                                        mtt_co_id IN (
                                            SELECT
                                                faculty_courses.fc_co_id
                                            FROM
                                                faculty_courses
                                            WHERE  fc_co_sess_id = ".auth()->user()->profile_settings->active_session."
                                            AND    faculty_courses.fc_employee_id = ".auth()->user()->employee_id."
                                            AND    fc_status = 'E'
                                        )
                                    AND
                                        tt_status = 'E'
                                    AND
                                        courses_offered.co_status = 'E'
                                ORDER BY tt_class_date";
    $row_teacher_courses = DB::select($teacher_timetable);
    $checkAppSql = "select count(*) as count from SISUSERS_DEVICE_TOKENS where DEVICE_TOKEN is not null and DEVICE_TOKEN not like '%web_login%'
    and lower(username) ='".auth()->user()->username."'";
    $resCheckApp = DB::select($checkAppSql);

    $chkMarkAttendance = "SELECT
                            count(*) as countatt
                        FROM
                            ta_rolls
                        WHERE
                        ta_rolls.employee_id = ".auth()->user()->employee_id."
                        and categ_name = 11";
    // echo $chkMarkAttendance;exit;
    $res = DB::select($chkMarkAttendance);
    $sql_sum = "select count(*) as tr,tm_status,task_type from task_management where TM_ENTRY_USER= ".auth()->user()->employee_id."
    group by tm_status,task_type";
$obj_summary = DB::select($sql_sum);

$ramzan_survey_record='';
 $ramzan_survey = "select id from STUDENT_FEEDBACKS where SF_TYPE= 'ramzan_timing' and SAI_ENROLLMENT_NO='".auth()->user()->username."'";
 $ramzan_survey = DB::select($ramzan_survey);
 if(!empty($ramzan_survey)){
    $ramzan_survey_record = $ramzan_survey[0]->id;
 }



    return response()->view(
        'grader.teacher.teacher_dashboard2',
        [
            'teacher_courses' => $teacher_courses,
            'all_classes' => $get_slot_list,
            'profile' => $profile,
            'timetable' => $row_teacher_courses,
            'all_notifications' => $get_all_notifications,
            'checkApp' =>$resCheckApp,
            'res'=>$res,
            'obj_summary'=>$obj_summary,
            'ramzan_survey'=>$ramzan_survey_record
        ]
        )->header('Cache-Control','no-cache, no-store, max-age=0, must-revalidate')
        ->header('Pragma','no-cache')
        ->header('Expires','Sun, 02 Jan 1990 00:00:00 GMT');




}

public function notificationDetail($id)
{
    $notification = DB::table('user_notifications')
        ->where('un_id', $id)
        ->first();

    if($notification){

        $notification->displayDate = date(
            'h:i a, M d, Y',
            strtotime($notification->un_entry_datetime)
        );
    }

    return view(
        'grader.teacher.notificationsDetail',
        compact('notification')
    );
}
public function allNotifications()
{
    $notifications = DB::select("
        SELECT
            ID AS \"id\",
            TITLE AS \"title\",
            CATEGORY AS \"category\",
            DESCRIPTION AS \"description\",
            STATUS AS \"status\",
            IS_READ AS \"is_read\",
            CREATED_AT AS \"created_at\",
            UPDATED_AT AS \"updated_at\"
        FROM NOTIFICATIONS
        WHERE STATUS = 1
          AND CATEGORY IN ('Faculty', 'Faculty & Staff')
        ORDER BY ID DESC
    ");

    return view(
        'grader.teacher.allNotifications',
        compact('notifications')
    );
}
public function addNotifications()
{
    $sess = new sessions();
    $sess->startSession();

    $notifications = new UserNotifications();

    $all_notifications = $notifications
        ->fetch_notification($sess->emp_user_id);

    return view(
        'grader.teacher.addNotifications',
        compact('all_notifications')
    );
}

function ta_rolls($fc_co_id,$emp_id){
    // echo $fc_co_id."====".$emp_id;exit;
    $sql="SELECT
    cms_categories.id,
    category_icon,
    category_title,
    (
        SELECT
            COUNT(*)
        FROM
            ta_rolls
        WHERE
                ta_rolls.categ_name = cms_categories.id
            AND
                ta_rolls.co_id = '$fc_co_id'
            AND
                ta_rolls.employee_id = '$emp_id'
    ) AS selected_status,
    (
     SELECT
            count(*) as mark_attendance
        FROM
            ta_rolls
        WHERE

                ta_rolls.co_id = '$fc_co_id'
            AND
                ta_rolls.employee_id =  '$emp_id'
                and categ_name = 11
    )as mark_attendance,
    route_name,route_href
    FROM
    cms_categories
    left join ta_rolls on categ_name = cms_categories.id
    where  ta_rolls.co_id = '$fc_co_id'
    ";
    //echo $sql;exit;
    return $res = DB::select($sql);
    return view('grader.teacher.ta_courses',['get_rolls'=>$res]);
}

function UpdateDetails(Request $request){
    $loginID=auth()->user()->username;

    $sel="select *from sisusers where username='$loginID'";
    $res=DB::select($sel);
    //dd($res[0]->temp1);
    $affected = DB::table('sisusers')
    ->where('username', auth()->user()->username)
    ->update(['mobile' => $res[0]->temp1]);

    $res = array('status' => true, 'message' => 'Mobile Update Sucessfully');
    return response()->json($res);
}

    function createCode(Request $request){



     //   print_r($request->all());

        $validator = Validator::make($request->all(), [
            'mobile' => 'required|regex:/^92[1-9]\d{9}$/|min:9'
        ]);

        //dd($validator->fails(),$validator->errors()->first());

    if ($validator->fails()) {
       // return back()->with('error', 'User created successfully.');
        return view('grader.profile.profile')->with('error',$validator->errors()->first().'Please follow the format (92334xxxxxxx)');
      //  return Redirect::back();//->withErrors($validator->errors()->first())->withInput()->with('errors', $validator->errors()->first());
    }

        $now = now();
        $code       = rand(1001, 9999);
        $bytes      = random_bytes(2);
		$emailOtp   = bin2hex($bytes);
       // dd($request->all());
        $sms = new SendSms();
        $mess='Your OTP for Login is '.$code.'. Do not share your OTP with anyone.';
        $message='Your OTP for Login is '.$emailOtp.'. Do not share your OTP with anyone.';
        // $sms->teacher_marks_change_sendmail($request->get('email'),'CMS LOGIN OTP',$message);
       // $sms->send_sms($mess, $request->get('mobile'));
       Session::put('mobileCode', $code);
       Session::put('emailCode', $emailOtp);

        $affected = DB::table('sisusers')
              ->where('username', auth()->user()->username)
              ->update(['temp1' => $request->get('mobile'),
                        'temp2' => $request->get('Email')]);

        //return view('grader.profile.otpVerify');
        return view('grader.profile.otpVerify')->with('success',  "OTP has been sent on Your Register Email & Number.");
        $res = array('status' => true, 'data'=>[$code.'_'.$emailOtp],'message' => 'OTP Send to Register Email/Mobile');
        return response()->json($res);

    }

    public function set_session(Request $request ){
        $sess = new sessions();
        $sess->startSession();

        $selected_active_session = $request->get('sel_active_session');
       $session_id =  substr($selected_active_session,0,3);
//    echo $session_id;
//     exit;
        $user = User::find($sess->sisuser_id);
        $user->saveProfileSetting($sess->sisuser_id,'active_session',$session_id);
        // PROFILE_SETTINGS
        // $user->profile_settings->color = 'blue';



       $user->save();
       echo '1';
    }

    public function retrieveDashboardCourses(Request $request){

       // dd('dd');
        $sess = new sessions();
        $sess->startSession();
        $login_id = $sess->emp_user_id;

        //  echo  $group_id = $sess->emp_dept_id;exit;
        $login_name=$sess->emp_user_name;
        $login_ip=$sess->login_ip;
        $mac = '';
        $description = 'login on Grader ';
        $pc = $_SERVER['HTTP_USER_AGENT'];
        $client_id = 2;
        $query_log="insert into login_log(user_id,password,login_ip,status,pc,mac,description) values ('".$login_name."','','".$login_ip."','Success','".$pc."','".$mac."','".$description."')";
        $row_teacher_courses = DB::insert($query_log);
        $co_id = $request->get('co_id');

        //echo 'ok';exit();

        $courseCriteria = new CourseCriterias();
        $get_criteria = $courseCriteria->fetchCoursesCriteria($co_id);


        if($co_id != ''){
            $courseList = $this->facultyCourse->fetchDashboardCourses($co_id);
        }else{
            $courseList = $this->facultyCourse->fetchDashboardCourses();
        }



        //return $courseList;exit();
        $report = ([
            'success' => 'Data saved Successfully',
            'error' => false,
            'error_details' => false
        ]);
        // echo "as"; exit;
        return view('grader.teacher.courses', compact('courseList','report','get_criteria'));
    }

    public function retrieveDepartmentFaculty()
    {
        $facultyList = $this->facultyCourse->fetchDepartmentFaculty($sessId=0);
        $getSession= DB::table('ACADEMIC_SESSIONS')->take(5)->orderBy('sess_id', 'desc')->get();

        return view('grader.teacher.course_hod_approval', compact('facultyList','getSession'));
    }
    public function retrieveTeacherCourses($sess_id,$fc_emp_id)
    {
        return $coursesList = $this->facultyCourse->fetchTeacherCourses($sess_id,$fc_emp_id);
    }

    public function uploadPostAssessment(Request $request,$fcu_id)
    {

        // dd($request->all());

        $sess = new sessions();
        $sess->startSession();
        $login_id = $sess->emp_user_id;


        ////////File Uplaod Code////////
        if ($request->hasFile('uploadfile2')) {
            $this->validate($request, [
                'uploadfile2'=>'required|max:50000|mimes:xlsx,docx'
            ]);
            $file = $request->file('uploadfile2');
           // echo $file->getClientOriginalExtension();
            $file_name = $file->getClientOriginalName();
            $name = $file->getClientOriginalName();
            $image['filePath'] = $name;
            $file->move(public_path() . '/docs/', $name);
            $file_full_name = $request->get('co_id') . '_' . $sess->emp_user_id . '_' . $file->getClientOriginalName();
        }else{
            $file_full_name = '';
            $file_name = '';


        }


        ////////File Uplaod Code End Here////////

        $title = $request->get('title2');
        $description = $request->get('description');
        $date = $request->get('duedate');
        $marks = $request->get('marks');
        $latesub = $request->get('latesub');
        $co_id = $request->get('co_id');
        $resub = $request->get('resub');
        $type = $request->get('assessment_id');
        if($fcu_id == 'insert'){
        $parts = explode('`', $type);

        $ccd_id = $parts[1];
         $assess_id = $parts[0];
        }
     //  echo $co_id = $request->get('co_id');
	 //  exit();
        $timestamp = strtotime($date);
        $newDate = date('Y-M-d', $timestamp);
        //$newDate = date('y-M-d', $timestamp);
        if($fcu_id == 'insert') {

                $sqlselect = "select * from course_criteria_details where co_id = $co_id and assessment_id = $assess_id";
                $resultselect = DB::select($sqlselect);
                $countresult = count($resultselect)+1;



                $sqlCCD = "INSERT INTO course_criteria_details(
                                                        CCD_DETAILS,
														DESCRIPTION,
														DUE_DATE,
														MARKS,
														LATE_SUBMISSION_ALLOWED,
														RE_SUBMISSION_ALLOWED,
														FILE_NAME,
														FILE_ORIGINAL_NAME,
													    co_id,
														assessment_id,
														course_criteria_id,
														ccd_number,
														ccd_weightage,
														FACULTY_COURSE_UPLOAD_ENTRY,
														ccd_status

												  ) VALUES (
												        '$title',
														'$description',
														'$newDate',
														'$marks',
														'$latesub',
														'$resub',
														'$file_full_name',
														'$file_name',												       													'$co_id',
														'$assess_id',
														'$ccd_id',
														'$countresult',
														'0',
														'1',
														'E'

														)";
                $resultCCD = DB::insert($sqlCCD);
            $mess = 'Upload Assessment : '.$title;

           # this call function of notificaiton to send notificaotn in this page
            $this->notification($co_id,$mess,$sess->emp_user_id);
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
                       // echo $sqlCAM;exit;
                    }
                }
           // echo $sqlUploadAssignment;exit;
            return response()->json(['success' => 'Data Saved Successfully.']);
        }else{

            $mess = 'Assessment Modified : '.$title;

            # this call function of notificaiton to send notificaotn in this page
            $this->notification($co_id,$mess,$sess->emp_user_id);
            #----------- end of notificaton funciton ------------------------

            if($file_full_name == '' and $file_name == '') {


                $updtsql = "update course_criteria_details
                        set   CCD_DETAILS              =  '$title',
                              DESCRIPTION              = '$description',
                              DUE_DATE                 = '$newDate',
                              MARKS                    = '$marks',
                              LATE_SUBMISSION_ALLOWED  = '$latesub',
                              RE_SUBMISSION_ALLOWED    = '$resub',
                              ASSESSMENT_ID            = '$type'
                        where id                       = $fcu_id";
                //echo $updtsql;exit;
                $res = DB::update($updtsql);

            }else {

                $updtsql = "update course_criteria_details
                        set   CCD_DETAILS              =  '$title',
                              DESCRIPTION              = '$description',
                              DUE_DATE                 = '$newDate',
                              MARKS                    = '$marks',
                              LATE_SUBMISSION_ALLOWED  = '$latesub',
                              RE_SUBMISSION_ALLOWED    = '$resub',
                              FILE_NAME                = '$file_full_name',
                              FILE_ORIGINAL_NAME       = '$file_name',
                              ASSESSMENT_ID            = '$type'
                        where id                       = $fcu_id";

                $res = DB::update($updtsql);

            }
            if($res){

                 $sqlcheckstdmarks = "select MAX(obtained_marks) as obtained
                                    from course_assessment_marks
                                    where co_id = $co_id
                                    and course_criteria_detail_id = $fcu_id ";
                //echo $sqlcheckstdmarks;exit;
                $rescheckstdmarks = DB::select($sqlcheckstdmarks);
                $entered_obtain_marks = $rescheckstdmarks[0]->obtained;

                if($entered_obtain_marks <= $marks or $entered_obtain_marks =='') {

                    $updtcammarks = "update course_assessment_marks
                        set   TOTAL_MARKS              =  '$marks'
                        where course_criteria_detail_id                       = $fcu_id";

                    $rescammarks = DB::update($updtcammarks);
                    return 1;
                }else{
                    return 0;
                }

            }


            return response()->json(['success' => 'Data Update Successfully.']);
        }
    }
    public function EditAssessment($id){

//        $ora = "select id,title,description,file_name,marks,late_submission_allowed,re_submission_allowed,type,TO_CHAR(due_date,'YYYY-MM-DD') as due_date from faculty_course_uploads where id = $id ";
//        $rec_ora = DB::select($ora);
//        return $rec_ora;

$ora = "select id,ccd_details,description,file_name,file_original_name,marks,is_assignment
,late_submission_allowed,re_submission_allowed,assessment_id
,TO_CHAR(due_date,'YYYY-MM-DD') as due_date
,(select max(total_marks) from course_assessment_marks
where course_assessment_marks.co_id = course_criteria_details.co_id
and course_assessment_marks.course_criteria_detail_id = course_criteria_details.id
) as total_marks
 from course_criteria_details where id = $id ";
$rec_ora = DB::select($ora);

return $rec_ora;
    }

    public function viewAssessmenttest($co_id){

       // $CourseAttendanceModel  = new CourseAttendance();
        //$get_slot_list = $CourseAttendanceModel->loadClassDatesModel(10972,0);
        $sess = new sessions();
        $sess->startSession();
        $faculty_school         = new Schools();
        $faculty_department     = new Departments();
        $profile                = new EmpEmployees();
        // echo 'love';

        $employee_profile = EmpEmployees::with('user')->where('employee_id','=',$sess->emp_user_id)->first();
        // dd($employee_profile);

        // echo $sess->emp_user_id;exit();


        $all_notifications = new UserNotifications();
        $get_all_notifications = $all_notifications->fetch_notification($sess->emp_user_id);

        $profile = array(
            'full_name'         => $sess->emp_user_name,
            'school'            => $faculty_school->fetch()[$sess->emp_school_id],
            'department'        => $faculty_department->fetch()[$sess->emp_dept_id],
            'emp_imagefile'     => $employee_profile->emp_imagefile,
            'email'             => $employee_profile->emp_email,
            'mobile'            => $employee_profile->emp_contact_no,

        );

        $courseCriteria = new CourseCriterias();
        $get_criteria = $courseCriteria->fetchCoursesCriteria($co_id);
       // $get_criteria = $courseCriteria->fetchCoursesCriteria($co_id);

        $courseCriteriaDetails = new CourseCriteriaDetails();
       // $get_criteriadetails = $courseCriteriaDetails->fetchCriteriaDetails(8267);
        $get_criteriadetails = $courseCriteriaDetails->fetchCriteriaDetails($co_id);

       // dd($get_criteriadetails);
      //  return $get_criteriadetails;
       // return $co_id;
     // exit();

        $sql_uploads = "select * from FACULTY_COURSE_UPLOADS";
        $course_handouts = DB::select($sql_uploads);

        $courseAssessmentMarks =  new CourseAssessmentMarks();
        $studentsList = $courseAssessmentMarks->fetchStudentsListNew($co_id);
       // return $studentsList;exit();
        // return $studentsList = $this->courseMarks->fetchStudentsList($co_id,$ass_type,$num);
       // exit();
        $teacher_courses = $this->facultyCourse->fetchDashboardCourses();
        return view('grader.teacher.view_study_material',
            [
                'teacher_courses'=>$teacher_courses,
                 'course_handouts'=>$course_handouts,
                'studentsList'=>$studentsList,
                'get_criteria_details'=>$get_criteriadetails,
                'get_criteria'=>$get_criteria,
                'all_notifications'=>$get_all_notifications
            ]);

    }
    public function viewAssessment($co_id){

        $sql = "select ccd_details,due_date,assessment_name,get_course_name($co_id) as course_name,co_id ,course_criteria_details.id,marks
                from course_criteria_details
                left join assessments on assessment_id = assessments.id
                where co_id = $co_id
                AND FACULTY_COURSE_UPLOAD_ENTRY = '1'
                and ccd_details is not null";
       // echo $sql;exit();
        $res = DB::select($sql);
        $countres = count($res);

        $sql2 = "select title,
    entry_date_time,
      co_id,
    FACULTY_COURSE_UPLOADS.id
    from FACULTY_COURSE_UPLOADS where co_id =  $co_id";
        $resfiles = DB::select($sql2);
       // return $res2;
        //exit();

        $countsql=" select count(assessment_id) as count,assessment_id,assessment_name
                    from course_criteria_details
                    left join assessments on course_criteria_details.assessment_id = assessments.id
                    where co_id = $co_id
                     AND FACULTY_COURSE_UPLOAD_ENTRY = '1'
                    group by assessment_id,assessment_name";
        $rescountsql = DB::select($countsql);

        $courseCriteria = new CourseCriterias();
        $get_criteria = $courseCriteria->fetchCoursesCriteria($co_id);

        return view('grader.teacher.view_assignment',['all_files'=>$resfiles,'all_uploads'=>$res,'criteria'=>$get_criteria,'count'=>$rescountsql,'chkassess'=>$countres]);

    }


    public function insertlecturecontent(Request $request,$tt_id){
        // it will be delete // because of it move to another controller // LectureContentController
        // echo $tt_id;exit;
        //dd($request->all());
        $getcontent = $request->get('getcontent');
        $id = $request->get('cc_id');
        $coc_content_type = $request->get('coc_content_type');
        $mtt_co_id = $request->get('mtt_co_id');
        $desc = $request->get('desc');

        $description=$getcontent.' '.$desc;


        $sqlcontinuation = "update COURSE_OFFERING_CONTENTS
        set coc_covered = 1,coc_continuation = 1
        where coc_co_id = '$mtt_co_id'
        and coc_continuation = 1
        and coc_covered is null
        and coc_continuation is not null";
        //echo $sqlcontinuation;exit;
        $res2 = DB::update($sqlcontinuation);


        $sql_cc_id = "update COURSE_OFFERING_CONTENTS
        set coc_covered =null,coc_continuation = 1
        where coc_co_id = '$mtt_co_id'
        and COC_COURSE_CONTENT_ID=".$id."
        and coc_content_type=".$coc_content_type;
        //echo $sqlcontinuation;exit;
        $res_cc_id = DB::update($sql_cc_id);


        $sqlinsertcontent = "INSERT INTO COURSE_OFFERING_CONTENTS(
                                                      COC_DETAILS,
                                                       COC_TT_ID,
                                                       coc_co_id,
                                                       coc_continuation,
                                                       COC_COURSE_CONTENT_ID,
                                                       coc_content_type,
                                                       coc_content_entry_type

												  ) VALUES (
												      '$description',
												      '$tt_id',
												      '$mtt_co_id',
												      '1',
												      $id,
												      '".$coc_content_type."',
												      '1'

												  )";
         echo  $sqlinsertcontent;exit;
        return $resultCCD = DB::insert($sqlinsertcontent);


    }




    public function upadtenewlecturecontent(Request $request,$tt_id){



// it will be delete // because of it move to another controller // LectureContentController


        $add_new_desc = $request->get('desc');
        $mtt_co_id = $request->get('co_id');

      // dd($request->all());
        $sqlnewlecturecontent="select count(*)+1 as total from course_offering_contents where coc_content_type = 2";
        $ressqlnewlecturecontent = DB::select($sqlnewlecturecontent);
        $total_count =  $ressqlnewlecturecontent[0]->total;

        $sqlcontinuation = "update COURSE_OFFERING_CONTENTS
        set coc_covered = 1,coc_continuation = 1
        where coc_co_id = '$mtt_co_id'
        and coc_continuation = 1
        and coc_covered is null
        and coc_continuation is not null";
        //echo $sqlcontinuation;
        $res2 = DB::update($sqlcontinuation);


        $sqladdnewinsertcontent = "INSERT INTO COURSE_OFFERING_CONTENTS(
                                                      COC_DETAILS,
                                                       COC_TT_ID,
                                                       coc_co_id,
                                                       coc_content_type,
                                                       coc_course_content_id,
                                                       coc_continuation

												  ) VALUES (
												      '$add_new_desc',
												      '$tt_id',
												      '$mtt_co_id',
												      '2',
												      '$total_count',
												      '1'


														)";
         //echo  $sqladdnewinsertcontent;exit;
        return $resultaddnewinsertcontent = DB::insert($sqladdnewinsertcontent);






    }
    public static function delete_criteria_assessment($co_id,$assessment_id)
    {

        $query_delete = "DELETE FROM COURSE_ASSESSMENT_MARKS
                                WHERE assessment_id = $assessment_id
                                and  co_id IN (
                                    SELECT
                                        co_id
                                    FROM
                                        courses_offered
                                    WHERE
                                            co_id = ".$co_id."
                                        OR
                                            parent_co_id = ".$co_id."
                                ) ";
        // echo  $query_delete;exit();
        DB::delete($query_delete);

        $course_criterias  = "delete from course_criterias  WHERE co_id = $co_id  and assessment_id=$assessment_id ";
        DB::update($course_criterias);
        $COURSE_CRITERIA_DETAILS  = "delete from COURSE_CRITERIA_DETAILS  WHERE co_id = $co_id  and assessment_id=$assessment_id ";
        DB::update($COURSE_CRITERIA_DETAILS);


    }
    public function delete_course_content($coc_id,$coc_tt_id){

        $delcoursecontent = "delete from course_offering_contents where coc_id = $coc_id and coc_tt_id = $coc_tt_id";
        $resultcoursecontent = DB::delete($delcoursecontent);
        return 1;
    }
    public function saveRamzanResponse(Request $request){
        
        $user_name = auth()->user()->username;
        // dd($user_id, $request->timing);
        DB::table('STUDENT_FEEDBACKS')->insert([
            'SFD_TITLE' => 'Which timing option do you prefer',
            'SFD_DESC' => $request->timing,
            'SF_TYPE' => 'ramzan_timing',
            'SAI_ENROLLMENT_NO' => $user_name
        ]);
        return response()->json([
            'success' => true,
            'message' => 'Your response has been recorded successfully!',
        ]);
        

    }

}