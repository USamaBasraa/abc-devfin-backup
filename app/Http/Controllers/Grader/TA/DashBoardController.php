<?php
namespace App\Http\Controllers\Grader\TA;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Grader\CourseAttendance;
use App\Models\Grader\CourseCriterias;
use App\Models\Grader\CourseCriteriaDetails;
use App\Models\Grader\CoursesOffered;
use App\Models\Grader\CourseContent;
use App\Models\Grader\FacultyCourses;
use DB;

class DashBoardController extends Controller
{
    private $facultyCourse;

    public function __construct(FacultyCourses $facultyCourses)
    {
        $this->facultyCourse = $facultyCourses;
        //$this->middleware('auth');
    }
    public function TagetStudentDetails(Request $request){
        // $course_id = my_simple_crypt($co_id, 'd' );
        // echo "===".$co_id;exit;
          $co_idd       = $request->get('co_id');       
          $course_id        = my_simple_crypt($co_idd, 'd');
          checkCourseAccess($course_id,$request->get('token'));
          // $course_id=my_simple_crypt($co_id, 'd' );
          $query = "SELECT
          co.co_id,
          co.co_course_id,
          co.co_section,
          co.co_course_cr,
          co.parent_co_id,
          (case when  co.parent_co_id is null then co.co_id else  co.parent_co_id end) as main_co_id,
          sc.sc_id,
          sc.sc_co_id,
          sai.sai_id,
          sai.sai_enrollment_no,
          spi.spi_name,
          --cam_id,
          -- obtained_marks,
          -- total_marks,
          -- cam_details,
          -- cam_marks,
          get_course_name(sc_co_id) as course_name,
          sc.attendance_percentage ,
          sai_student_status,
          sc_status  ,
          sai_remarks,
          ssr_semester_status
          
          FROM
              student_courses sc
              LEFT JOIN students_semester_record ON
              sc.sc_sai_enrollment_no = students_semester_record.sai_enrollment_no
              AND
              sc.sc_co_sess_id = students_semester_record.sess_id
              LEFT JOIN courses_offered co ON sc_co_id = co.co_id
              LEFT JOIN courses_two ON sc_course_id = course_id
              LEFT JOIN students_apply_info sai ON sc.sc_sai_enrollment_no = sai.sai_enrollment_no
              LEFT JOIN students_personal_info spi ON sai.sai_spi_id = spi.spi_id
          WHERE
                
                    students_semester_record.ssr_semester_status = 'Active'
                  AND sc.sc_enrollment_status = 1
                  AND (
                          sai.sai_student_status IN (
                              'Active','Expected-Graduate','Left','Struck off','Stuck-Off'
                          )
                      OR
                          sai.sai_semester_status = 'Defer'
                  )
              AND (
                      co.co_id = ".$course_id."
                  OR
                      co.parent_co_id = ".$course_id."
              )
              ORDER BY LOWER(spi.spi_name)";
          $result =DB::select($query);     
          // echo $query;exit;      
          return view(
              'grader.StudentDetail.student_detail',['studentsList' => $result]
          );
    }

    public function Tagettimetable(Request $request){
   
        // $fc_co_id=my_simple_crypt($coID, 'd' );

        $co_idd       = $request->get('co_id');       
        $fc_co_id        = my_simple_crypt($co_idd, 'd');
        checkCourseAccess($fc_co_id,$request->get('token'));
        // dd($fc_co_id);
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

    public function taLectures_Listing(Request $request){

        $co_idd       = $request->get('co_id');
        $co_id        = my_simple_crypt($co_idd, 'd');
        $course_name  = course_name($co_id);
        $tt_id        =$request->get('tt_id');
        //echo "123";exit;
        checkCourseAccess($co_id,$request->get('token'));
        # Get Course Lectures List Date Wise with Details
        $CourseAttendanceModel  = new CourseAttendance();
        $all_classes            = $CourseAttendanceModel->loadClassDatesModelDashboard($co_id, 0);
    
        if(count($all_classes)==0){
         // return 'Semester Timetable not Created';
        }
    
        # Get Course Contents Enter by Teacher in Each lectures
        $sqlcourseofferingcontent = "select * from COURSE_OFFERING_CONTENTS where coc_co_id =" . $co_id . " order by coc_id asc";
        $courseofferingcontent    = DB::select($sqlcourseofferingcontent);
    
        # Bootom will Attach Each Lectures content array in each row
        array_walk($all_classes, function ($row, $index) use ($course_name, $courseofferingcontent) {
          $content_keys = array_keys(array_column($courseofferingcontent, 'coc_tt_id'), $row->tt_id);
          $row->course_name = $course_name;
          $row->courseofferingcontent  = [];
          foreach ($content_keys as $cc_row) :
            $row->courseofferingcontent[] =  $courseofferingcontent[$cc_row];
          endforeach;
        });
    
        $courseCriteria   = new CourseCriterias();
        $course_criteria  = $courseCriteria->fetchCoursesCriteria($co_id);
        $content          = $this->course_content_record($co_id);
    
        $action = 'TA';
    
        if( isset($_GET['show-modal']) ){
           return view('grader/LectureScheduling.lecture_model', compact('co_id', 'all_classes', 'content', 'co_idd', 'course_criteria'));
        }
    
        return view('grader/LectureScheduling.lectures', compact('tt_id','co_id', 'all_classes', 'content', 'co_idd', 'course_criteria','action'));
    
    }

    function course_content_record($coID){
        return CourseContent::getCourseContentsByCoID($coID);
    }
 
    public function ta_course_content(Request $request){
        
        $co_idd = $request->get('co_id');
        $co_id        = my_simple_crypt($co_idd, 'd');
        checkCourseAccess($co_id,$request->get('token'));
        // dd(auth()->user());
        $course_idd         =   '';
        $sqlassessment      =   "select * from Assessments order by id";
        $sqlassessment      =   DB::select($sqlassessment);
        $teacher_courses    =   $this->facultyCourse->fetchDashboardCourses();
		$empId=auth()->user()->employee_id;
        $courses_offered = new CoursesOffered();
        $course_id = $courses_offered::find($co_id)->co_course_id;
        //dd(  $teacher_courses);
        // this function will check either the courses is offewred to this faculty or not
       
        $sql="select ASSESSMENTS.assessment_name,COURSE_CONTENTS.cc_weightage,cc_type,cc_lecture_content,COURSE_CONTENTS.id,COURSE_CONTENTS.final_status from COURSE_CONTENTS
                LEFT JOIN ASSESSMENTS ON COURSE_CONTENTS.CC_ASSESSMENTS = ASSESSMENTS.ID
                where COURSE_CONTENTS.CC_COURSE_ID=$course_id
                and cc_teacher_id = ".auth()->user()->employee_id." 
                order by ASSESSMENTS.id ";
              //  echo $sql;exit();

        $result         = DB::select($sql);
        $countcontents  = count($result);
        $course_name    = course_name($co_id);

        $sqlcontent         =   "select * from COURSE_CONTENTS where CC_COURSE_ID=$course_id and cc_type='content' and cc_teacher_id =$empId order by cc_order asc";
        $sqlcontent2         =   "select * from COURSE_CONTENTS where CC_COURSE_ID=$course_id and cc_type='content' and cc_teacher_id =$empId order by cc_order asc";
        $sqlfile         =   "select * from COURSE_CONTENTS where CC_COURSE_ID=$course_id and cc_type='file' order by cc_order asc";

        
        $criteria="select *from course_criterias
        left join assessments on assessments.id= assessment_id
        where co_id=$co_id";
        $sqlcriteria   =   DB::select($criteria);

       $sqlcontentresult   =   DB::select($sqlcontent);
       $sqlcontentresult2   =   DB::select($sqlcontent2);
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

    public function taonlineexam(Request $request){
        //echo "bilal".$adm_sch_id;exit;
        $co_idd = $request->get('co_id');
        //$co_id        = my_simple_crypt($co_idd, 'd');     
        $decrypt_co_id=$this->my_simple_crypt($co_idd, 'd' );
        checkCourseAccess($decrypt_co_id,$request->get('token'));
        //  $decrypt_course_id=$this->my_simple_crypt($course_id, 'd' );
        $adm_sch_name ='';
        $sql = "select id,
        CO_ID,
        TITLE,
        TOTAL_QUESTIONS,
        EXAM_PATTERN,
        EXAM_DATE,
        EXAM_START_TIME,
        EXAM_DURATION,
        ENTRY_DATE_TIME,
        ENTRY_USER,
        STATUS,
        EXAM_END_TIME,
        PERCENTAGE_STATUS,
        COURSE_CRITERIA_ID,
        ASSESSMENT_ID,
        ASSESSMENT_NO,
        FINAL_SUBMIT,
        NEGATIVE_MARKS,
        PAPER_ATTMP_TYPE,
        RE_ATTEMPT,
        RE_VIEW,
        EXAM_FILE,
        COPY_PASTE_ALLOWED,
        exam_paper_admission_sch_id,
        (select count(*) from
              EXAM_question_paper
             where exam_papers_id= exam_papers.ID
             ) as  student_exam_question,
             exam_answer_file,
             (select count(*) from exam_student_attendance where exam_id = exam_papers.ID
         ) as paper_start,
         move_to_cms,
         (SELECT count(*)
         FROM exam_student_paper
         WHERE course_exam_papers_id= exam_papers.ID
         ) AS student_paper
           from exam_papers where co_id=$decrypt_co_id
           order by id desc";
    
        $users  = DB::select($sql);
     
        $courseCriteriadetail = new CourseCriteriaDetails();
        $get_criteriadetail = $courseCriteriadetail->fetchCriteriaDetails($decrypt_co_id);
        //echo $sql;exit;
        $courseCriteria = new CourseCriterias();
        $get_criteria = $courseCriteria->fetchCoursesCriteria($decrypt_co_id);
     
        //dd($get_criteria);
        $course_name = DB::selectOne("select get_course_name($decrypt_co_id) as value from dual");
        $course_name=$course_name->value;
     
        return view('grader.teacher.online_exam',['course_id'=>$decrypt_co_id,'co_id'=>$co_idd,'result'=>$users,'course_criteria'=>$get_criteria,'course_name'=> $course_name,'adm_sch_id'=>'','adm_sch_name'=>'','criteria_detail'=>$get_criteriadetail]);
    }

    function my_simple_crypt( $string, $action = 'd' ) {
        // you may change these values to your own
        $secret_key = 'my_simple_secret_key';
        $secret_iv = 'my_simple_secret_iv';
    
        $output = false;
        $encrypt_method = "AES-256-CBC";
        $key = hash( 'sha256', $secret_key );
        $iv = substr( hash( 'sha256', $secret_iv ), 0, 16 );
    
        if( $action == 'e' ) {
            $output = base64_encode( openssl_encrypt( $string, $encrypt_method, $key, 0, $iv ) );
        }
        else if( $action == 'd' ){
            $output = openssl_decrypt( base64_decode( $string ), $encrypt_method, $key, 0, $iv );
        }
    
        return $output;
    }

    function TaStreamView(Request $request){      
        $co_idd       = $request->get('co_id');       
        $coID        = my_simple_crypt($co_idd, 'd');
        checkCourseAccess($coID,$request->get('token'));
        $data['course_name'] = DB::selectOne("select get_course_name($coID) as course from dual")->course;
        $data['coID']       = $coID; 
        return view('grader.chat.faculty_notification',$data);
    }

    public function ta_course_assessment_report(Request $request){
        // echo "!@3";exit;
        $co_idd = $request->get('co_id');
        $co_id        = my_simple_crypt($co_idd, 'd');
        checkCourseAccess($co_id,$request->get('token'));
        // $co_id=my_simple_crypt($request->get('co_id'), 'd' ); 
        $sessions       = \App::call('App\Http\Controllers\Grader\AcademicSessionController@retrieve');
        $sql_grading    ="SELECT
                                assessment_name,
                                CASE
                                    WHEN cc_method_id = 1 THEN 'Equal Distribution'
                                    WHEN cc_method_id = 2 THEN 'Student Wise'
                                    WHEN cc_method_id = 3 THEN 'Average Wise'
                                    WHEN cc_method_id = 4 THEN 'Instructior Selection'
                                END
                                AS method_name,
                                (
                                    SELECT
                                        COUNT(*)
                                    FROM
                                        course_criteria_details
                                    WHERE
                                            co_id = course_criterias.co_id
                                        AND
                                            assessment_id = course_criterias.assessment_id
                                ) AS total_enter,
                                course_criterias.best_of_no,
                                course_criterias.cc_weightage
                            FROM
                                course_criterias
                            LEFT JOIN ASSESSMENTS on course_criterias.ASSESSMENT_ID=ASSESSMENTS.id
                            WHERE course_criterias.co_id = $co_id";
        $res_grading =  DB::select($sql_grading); 
        //echo  $sql_grading ;exit; 
        $sql1   = "SELECT DISTINCT
                         course_assessment_marks.co_id,
                         assessments.assessment_short_name,
                         course_assessment_marks.assessment_id,
                         assessments.assessment_name,
                         course_assessment_marks.total_marks,
                         cam_number,
                         get_teachername($co_id) AS teacher_name,
                         dept_name,
                         school_name,
                         sess_name
                     FROM
                     course_assessment_marks
                     LEFT JOIN assessments ON assessments.id = course_assessment_marks.assessment_id
                     LEFT JOIN student_courses ON
                         course_assessment_marks.sai_enrollment_no = student_courses.sc_sai_enrollment_no
                         AND
                         course_assessment_marks.sc_co_id = student_courses.sc_co_id
                     LEFT JOIN courses_offered ON courses_offered.co_id = course_assessment_marks.co_id
                     LEFT JOIN departments ON departments.dept_id = courses_offered.co_dept_id
                     LEFT JOIN schools ON schools.school_id = courses_offered.co_school_id
                     LEFT JOIN academic_sessions ON academic_sessions.sess_id = courses_offered.co_sess_id
                     WHERE course_assessment_marks.co_id = $co_id
                     AND sc_status='A'";
        $sql1 .= " ORDER BY assessment_id,cam_number";
        $res1  =  DB::select($sql1);
 
        $sqlAppacdemicprof ="SELECT
                                student_courses.sc_sai_enrollment_no AS sai_enrollment_no,
                                assessment_name,
                                assessment_short_name,
                                total_marks,
                                obtained_marks,
                                ccd_status,
                                course_cd_name || '-' ||  co_course_cr || '-' || co_section as course_name_2,
                                (
                                    SELECT
                                        mh_marks
                                    FROM
                                        marks_history
                                    WHERE
                                            marks_history.mh_sai_enrollment_no = course_assessment_marks.sai_enrollment_no
                                        AND
                                            marks_history.mh_co_id = course_assessment_marks.sc_co_id
                                ) AS obtained_marks_history,
                                (
                                    SELECT
                                        gh_grade
                                    FROM
                                        grade_history
                                    WHERE
                                            grade_history.gh_sai_enrollment_no = course_assessment_marks.sai_enrollment_no
                                        AND
                                            grade_history.gh_co_id = course_assessment_marks.sc_co_id
                                ) AS student_grade,
                                (SELECT gh_grading_type
                                FROM grade_history
                                WHERE grade_history.gh_sai_enrollment_no = course_assessment_marks.sai_enrollment_no
                                AND grade_history.gh_co_id               = course_assessment_marks.sc_co_id
                                ) AS grader,
                                spi_name,
                                cam_number,
                                cam_status,
                                cam_grading_status,
                                ccd_grading_status,
                                get_course_name((CASE
                                WHEN courses_offered.parent_co_id is null  THEN courses_offered.co_id                                
                                ELSE courses_offered.parent_co_id END)) as course_name,
                                (SELECT count(*)
                                FROM grade_history
                                WHERE grade_history.gh_co_id = courses_offered.co_id
                                AND grade_history.gh_co_course_id               = courses_offered.co_course_id
                                and gh_grade not in ('W')
                                )as graded
                            FROM
                                student_courses
                                LEFT JOIN students_apply_info ON student_courses.sc_sai_enrollment_no = students_apply_info.sai_enrollment_no
                                LEFT JOIN students_personal_info ON sai_spi_id = spi_id
                                LEFT JOIN courses_offered ON sc_co_id = courses_offered.co_id
                                LEFT JOIN courses_two ON courses_two.course_id = courses_offered.co_course_id
                                LEFT JOIN course_criteria_details ON (
                                    CASE
                                        WHEN parent_co_id IS NULL THEN courses_offered.co_id
                                        ELSE parent_co_id
                                    END
                                ) = course_criteria_details.co_id
                                LEFT JOIN course_assessment_marks ON
                                    course_criteria_details.id = course_assessment_marks.course_criteria_detail_id
                                AND
                                    student_courses.sc_sai_enrollment_no = course_assessment_marks.sai_enrollment_no
                                LEFT JOIN assessments ON course_criteria_details.assessment_id = assessments.id
                            WHERE
                                    1 = 1
                                AND
                                    sc_status = 'A'
                                AND
                                    co_status = 'E'
                                AND (
                                        courses_offered.co_id = $co_id
                                    OR
                                        courses_offered.parent_co_id = $co_id
                                ) AND (
                                        students_apply_info.sai_student_status IN (
                                            'Active','Expected-Graduate','Left','Struck off','Stuck-Off'
                                        )
                                    OR
                                        students_apply_info.sai_semester_status = 'Defer'
                                ) AND (
                                        students_apply_info.sai_semester_status != 'Freeze'
                                    OR
                                        students_apply_info.sai_semester_status IS NULL
                                ) ";
        $sqlAppacdemicprof .= "ORDER BY student_courses.sc_sai_enrollment_no,assessments.id,cam_number";
 
        $rsAppacdemicprof = DB::select($sqlAppacdemicprof);
 
        return view('grader.reports.assessment_marks_report',
            [
                'sessions'                   =>$sessions,
                'rsAppacdemicprof'           =>$rsAppacdemicprof,
                'res1'                       =>$res1,
                'res_grading'                =>$res_grading
            ]
        );
    }

    public function taindex (Request $request){
       // $result = app('App\Http\Controllers\Auth\LoginController')->exampleFunction();
        $now = now();
        $code       = rand(1001, 9999);
        //  echo $userData['mobile']; exit;
        $code;
        $now->addMinutes(2);      
      
        $co_idd = $request->get('co_id');
        $co_id        = my_simple_crypt($co_idd, 'd');
        checkCourseAccess($co_id,$request->get('token'));
        //echo $co_id;exit();
        $courseCriteria = new CourseCriterias();
        $get_criteria = $courseCriteria->fetchCoursesCriteria($co_id);
        $course_name = course_name($co_id);

        $courseCriteriaDetails = new CourseCriteriaDetails();
        $get_criteriadetails = $courseCriteriaDetails->fetchCriteriaDetails($co_id);

        // print_r($get_criteriadetails);
        //exit();
        $approval_status = "select  get_final_approval_status($co_id) as status from dual";
        $res_approval_status = DB::select($approval_status);
        //  print_r($res_approval_status);exit;
        $checkCountFiles = DB::table('course_criteria_details')
        ->select(DB::raw('CASE WHEN COUNT(*) = COUNT(CASE WHEN google_drive_url IS NOT NULL AND google_drive_file_name IS NOT NULL THEN 1 END) THEN 1 ELSE 0 END AS result'))
        ->where('co_id', $co_id)
        ->whereNotIn('assessment_id', [15])
        ->value('result');
        $checkNullFiles = DB::table('course_criteria_details')
        ->select('course_criteria_details.assessment_id as assessment_id', 'course_criteria_details.ccd_details as ccd_details', 'assessments.assessment_name as assessment_name')
        ->leftJoin('assessments', 'course_criteria_details.assessment_id', '=', 'assessments.id')
        ->where('co_id', $co_id)
        ->whereNull('google_drive_url')
        ->whereNull('google_drive_file_name')
        ->whereNotIn('assessment_id', [15])
        ->get();

        return view('grader/LectureScheduling.course_evaluation',[
        'criteria'=>$get_criteria,
        'get_criteria_details'=>$get_criteriadetails,
        'get_criteria'=>$get_criteria,
        'course_criteria'=>$get_criteria,
        'criteria_details'=>$get_criteriadetails,
        'course_name'=>$course_name,
        'get_co_id'=>$co_id,
        'result'=>$checkCountFiles,
        'checkNullFiles'=>$checkNullFiles,
        'status' => $res_approval_status[0]->status
        ]);    
    }
}
