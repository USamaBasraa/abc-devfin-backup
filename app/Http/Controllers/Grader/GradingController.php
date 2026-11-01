<?php
/**
 * Created by PhpStorm.
 * User: Nabeel
 * Date: 8/11/2018
 * Time: 12:11 PM
 */
namespace App\Http\Controllers\Grader;

use App\Http\Controllers\Controller;

use App\Models\Grader\CourseMarksSummaries;
use DB;
use Illuminate\Support\Facades\Input;
use App\sessions;
use App\Models\Grader\SendSms;
use Illuminate\Http\Request;



class GradingController extends Controller
{
    public function index($var_co_id=0){
        $sessions_values = new Sessions();
        $sessions_values->startSession();
       // execute Relative_Grading(6880,800,'Preview','Relative');
        $sql_function_grading_type = "select GET_COURSE_GRADING_TYPE($var_co_id) as funciton_grading_type from dual";
        $obj_function_grading_type =  DB::select($sql_function_grading_type);
        $var_funciton_grading_type      = $obj_function_grading_type[0]->funciton_grading_type;
        $check_course_type="SELECT
    co_id,
    (
        SELECT
            COUNT(*)
        FROM
            student_courses
        WHERE
            sc_co_id IN (
                SELECT
                    co_id
                FROM
                    courses_offered
                WHERE
                    co_id = $var_co_id
                UNION ALL
                SELECT
                    co_id
                FROM
                    courses_offered
                WHERE
                    parent_co_id = $var_co_id
            )
    ) AS total_students,
    course_cd_name,
    course_grading_type,
    course_id,
    (
        SELECT
            COUNT(*)
        FROM
            students_apply_info
        WHERE
                sai_sdpo_sess_id >= 144
            AND
                sai_enrollment_no IN (
                    SELECT
                        student_courses.sc_sai_enrollment_no
                    FROM
                        student_courses
                    WHERE
                        sc_co_id IN (
                            SELECT
                                co_id
                            FROM
                                courses_offered
                            WHERE
                                co_id = $var_co_id
                            UNION ALL
                            SELECT
                                co_id
                            FROM
                                courses_offered
                            WHERE
                                parent_co_id = $var_co_id
                        )
                )
    ) AS total_new_students,
    course_cd_name
FROM
    courses_offered
    LEFT JOIN courses_two ON course_id = co_course_id
WHERE
    co_id = $var_co_id";
       // return $check_course_type;exit();
        $obj_check_course_type =  DB::select($check_course_type);
        $grading_total_students      = $obj_check_course_type[0]->total_students;
        $grading_course_type         = $obj_check_course_type[0]->course_grading_type;
        $total_new_students         = $obj_check_course_type[0]->total_new_students;
        $course_cd_name             = $obj_check_course_type[0]->course_cd_name;


        if($grading_course_type=='Absolute'){
            $grading_formula_type = "Absolute";
        }else{
            if($grading_total_students<=10){
                $grading_formula_type = "Absolute";
            }elseif($total_new_students==0){
                $grading_formula_type = "Absolute";
            }else{
                $grading_formula_type = "Relative";
            }
        }

      //  echo $total_new_students."___".$grading_course_type;exit();
        $procedureName = 'marks_percentage_update';

        $bindings = [
            'co_id'  => $var_co_id,
        ];



        $result = DB::executeProcedure($procedureName, $bindings);
        // return response()->json($result);

        //----------- Bottom Code check if upper procedue not work ---------------
        $sql_check_mhhistory="select count(*) as total_missing
                              from   MARKS_HISTORY
                              where MH_CONVERTED_MARKS is null
                              and mh_co_id =$var_co_id";
        $obj_total_missing      =  DB::select($sql_check_mhhistory);
        $total_missing          = $obj_total_missing[0]->total_missing;
        if($total_missing>0){
            exit();
        }

        //-------------- END OF UPPER CHECK ---------


        /// echo  $grading_formula_type;exit();
       // echo $var_co_id."____".$sessions_values->emp_user_id."___Preview".$var_funciton_grading_type;exit();

        $procedureGrading = 'Relative_Grading';

        $bindingsGrading = [
            'co_id'           => $var_co_id,
            'user'            => $sessions_values->emp_user_id,
            'grading_action'  =>'Preview',
            'grading_type'    => $var_funciton_grading_type
        ];

        //print_r($bindingsGrading);exit();

      $mesg='@'.date("l")." ". date("d-m-Y H:i:s");

                    //dd($message);

        $resultGrading = DB::executeProcedure($procedureGrading, $bindingsGrading);
        //  return $bindingsGrading;exit();
        $courseMarksSummaries = new CourseMarksSummaries();
        $courseMarkSummary = $courseMarksSummaries->getMarksGradingView($var_co_id);

       // return $courseMarkSummary;

       ////////////////////////////////send sms //////////////////////////
        //     $sms = new SendSms();
        //     $sess = new sessions();
        //     $sess->startSession();
        //     $login_id = $sess->emp_user_id;
        //     $emp_email=$sess->emp_email;
        //     $phone_no=$sess->phone_no;
        //     $countlength = strlen($phone_no);
        //     $chknumber = substr($phone_no,0,2);
        //     $get_course_teacher=$sms->get_teacher_course($var_co_id, $login_id);

        //          if($countlength == 12){
        //             if($chknumber == 92){
        //       //   $sms->send_sms($get_course_teacher.' '.$mesg,$phone_no);$emp_email
        //       }
        //    }
        //         $subject='Student Grade Post Alert !';
        //         $mess='Students Grades Posted  !'.$get_course_teacher.$mesg;
        //         $sms->teacher_marks_change_sendmail( 'zain1.ali@bnu.edu.pk',$subject,$mess);
        //         try {
        //             $sms->teacher_marks_change_sendmail( 'zain1.ali@bnu.edu.pk',$subject,$mess);

        //          } catch (\Exception $e) {
        //              //echo $e->getCode() ; exit;
        //             if ($e->getCode() == 1) {
        //                 //return 'ddddd';
        //                 return response()->json(array('status' => false,'data'=> [], 'message' => 'Kindly update the Email '));
        //                 exit;
        //             }
        //          }


              //


        ////////////////////////////////////////////////////////////////////////////////////////

        return view('grader.grading.grading_view',
            [
                'courseMarksSummaries'  => $courseMarkSummary,
                'var_co_id'             =>$var_co_id,
               /// 'marks_max_min'         => $marks_max_min,
              //  'student_grade'         => $student_grade,
                'grading_formula_type'  =>$var_funciton_grading_type,
               // 'grade_ranges'          =>$grade_ranges,
              //  'grade_details'         =>$grade_details,
              //  'get_total_records'     =>$get_total_records
            ]
        );
    }

    public function post_grade(Request $request,$var_co_id=0){

        $sessions_values = new Sessions();
        $sessions_values->startSession();

        $all_data  = $request->all();//  Input::all();
      //  return $all_data['grading_formula_type'];

        $procedureGrading = 'Relative_Grading';

        $bindingsGrading = [
            'co_id'           => $var_co_id,
            'user'            => $sessions_values->emp_user_id,
            'grading_action'  =>'Post',
            'grading_type'    => $all_data['grading_formula_type']
        ];

        $resultGrading = DB::executeProcedure($procedureGrading, $bindingsGrading);


    }
    public function index_old($var_co_id=0){
        error_reporting(0);
         $check_grading_query = "select
                                      count(*) as total_rec
                                      from GRADE_HISTORY
                                      where gh_grade!='W'
                                      and gh_co_id in (
                                                          select co_id from
                                                          courses_offered
                                                          where co_id = $var_co_id
                                                          and gh_status='E'
                                                          union all
                                                          select co_id from
                                                          courses_offered
                                                          where parent_co_id = $var_co_id
                                                           and gh_status='E'
                                )";
        $count_check_grading_query   =  DB::select($check_grading_query);
        $get_total_records           = $count_check_grading_query[0]->total_rec;

        $courseMarksSummaries = new CourseMarksSummaries();
        $courseMarkSummary = $courseMarksSummaries->getMarksGradingView($var_co_id);

        if($get_total_records==0) {

            $dell_query = "delete from MARKS_HISTORY where mh_co_id in (
                            select co_id from courses_offered
                            where co_id = $var_co_id
                            and   co_status='E'
                            union all
                            select co_id from courses_offered
                            where parent_co_id = $var_co_id
                            and co_status='E'
                            )";
            $dell_query_run = DB::delete($dell_query);
            $marks_history_qry = "insert into MARKS_HISTORY(MH_SAI_ID,MH_SAI_ENROLLMENT_NO,MH_MARKS,MH_CO_ID)
                            select sai_id,sai_enrollment_no,sum(OBTAINED_MARKS),sc_co_id from COURSE_MARKS_SUMMARIES
                            where co_id=$var_co_id
                            group by sai_enrollment_no,sai_id,sc_co_id ";
            $marks_history_add = DB::insert($marks_history_qry);

        }
        $marks_max_min = $this->grade_marks_max_min($var_co_id);
        $mydataaa =$this->marks_history($var_co_id);
      //  print_r($mydataaa);exit();
        $count_relative_students = 0;
        foreach ($mydataaa as $rec_courseMarksSummary){
            //echo $rec_courseMarksSummary->sai_sdpo_sess_id."<br>";
            if($rec_courseMarksSummary->sai_sdpo_sess_id>=144){
                $count_relative_students++;
            }
            $student_obtained_marks = $rec_courseMarksSummary->mh_marks;
            $student_marks = $this->grade_marks_percentage($student_obtained_marks,$var_co_id);

            if($get_total_records==0) {
                DB::table('marks_history')
                    ->where('mh_id', $rec_courseMarksSummary->mh_id)
                    ->update(['MH_CONVERTED_MARKS' => $student_marks]);
            }
        }
        $updated_marks_history = $this->marks_history_updated($var_co_id);
        $check_course_type="SELECT
                            co_id,
                            (
                                SELECT
                                    COUNT(*)
                                FROM
                                    student_courses
                                WHERE
                                    sc_co_id = ".$var_co_id."
                            ) AS total_students,
                            course_cd_name,
                            course_grading_type,
                            course_id
                        FROM
                            courses_offered
                            LEFT JOIN courses_two ON course_id = co_course_id
                        WHERE
                            co_id = ".$var_co_id."";
        //return $check_course_type;exit();
        $obj_check_course_type =  DB::select($check_course_type);
        $grading_total_students      = $obj_check_course_type[0]->total_students;
        $grading_course_type         = $obj_check_course_type[0]->course_grading_type;

        if($grading_course_type=='Absolute'){
                $grading_formula_type = "Absolute";
        }else{
            if($grading_total_students<=10){
                $grading_formula_type = "Absolute";
            }elseif($count_relative_students==0){
                $grading_formula_type = "Absolute";
            }else{
                $grading_formula_type = "Relative";
            }
        }
    //echo $grading_course_type."___".$grading_formula_type."___".$count_relative_students."___";
        $sql_ag_mh = "SELECT
						AVG(mh_converted_marks) AS c_avg,
						STDDEV(mh_converted_marks) AS c_sd,
						MAX(mh_converted_marks) AS maxmarks,
						MIN(mh_converted_marks) AS minmarks
					FROM marks_history
					WHERE mh_marks NOT IN ('I','W')
					AND mh_co_id IN ( SELECT co_id FROM courses_offered WHERE co_id = ".$var_co_id."
										UNION ALL
										SELECT co_id FROM courses_offered WHERE parent_co_id = ".$var_co_id."
									  )";
        $calculated_rec_marks   =  DB::select($sql_ag_mh);
        $maxmarks               = $calculated_rec_marks[0]->maxmarks;
        $min_marks              = $calculated_rec_marks[0]->minmarks;
        $get_avg                = $calculated_rec_marks[0]->c_avg;
        $get_c_sd               = $calculated_rec_marks[0]->c_sd;

       // echo $maxmarks."___".$min_marks."___".$get_avg."___>>".$get_c_sd."___";exit();

        #_________________ With Max Marks__________________________#
        $zscore = (($maxmarks-($get_avg))/$get_c_sd);
        $zscore_value = $zscore/5;
        // z schores grade wise
        /*$A_new=$zscore_value*5;
        $Am_new=$zscore_value*4;
        $Bp_new=$zscore_value*3;
        $B_new=$zscore_value*2;
        $Bm_new=$zscore_value*1;
        $Cp_new=-$zscore_value*1;
        $C_new=-($zscore_value*2);
        $Cm_new=-($zscore_value*3);
        $Dp_new=-($zscore_value*4);
        $D_new=-($zscore_value*5);*/

       /* $A_new=$zscore_value*5;
        $Am_new=$zscore_value*4;
        $Bp_new=$zscore_value*3;
        $B_new=$zscore_value*2;
        $Bm_new=$zscore_value*1;
        $Cp_new=-$zscore_value*0;
        $C_new=-($zscore_value*1);
        $Cm_new=-($zscore_value*2);
        $Dp_new=-($zscore_value*3);
        $D_new=-($zscore_value*4);*/

       $A_new=$zscore_value*4;
        $Am_new=$zscore_value*3;
        $Bp_new=$zscore_value*2;
        $B_new=$zscore_value*1;
        $Bm_new=$zscore_value*0;
        $Cp_new=-$zscore_value*1;
        $C_new=-($zscore_value*2);
        $Cm_new=-($zscore_value*3);
        $Dp_new=-($zscore_value*4);
        $D_new=-($zscore_value*5);


        $grade_details=array();
        $grade_details[] = array(
                                    "mean"              =>round($get_avg,10),
                                    "sd"                =>round($get_c_sd,10),
                                    "max_marks"         =>round($maxmarks,10),
                                    "min_marks"         =>round($min_marks,10),
                                    "z_score"           =>round($zscore,10),
                                    "z_score_unit"      =>round($zscore_value,10)
                                );

        $grade_ranges=array();
        $range_A_new=$get_avg+($A_new*$get_c_sd);
        $range_Am_new=$get_avg+($Am_new*$get_c_sd);
        $range_Bp_new=$get_avg+($Bp_new*$get_c_sd);
        $range_B_new=$get_avg+($B_new*$get_c_sd);
        $range_Bm_new=$get_avg+($Bm_new*$get_c_sd);	 # ----- check
        $range_Cp_new=$get_avg+($Cp_new*$get_c_sd);
        $range_C_new=$get_avg+($C_new*$get_c_sd);
        $range_Cm_new=$get_avg+($Cm_new*$get_c_sd);
        $range_Dp_new=$get_avg+($Dp_new*$get_c_sd);
        $range_D_new=$get_avg+($D_new*$get_c_sd);

        $grade_ranges=array();

        $GLOBALS['gA_new']  =   $range_A_new;
        $GLOBALS['gAm_new'] =   $range_Am_new;
        $GLOBALS['gBp_new'] =   $range_Bp_new;
        $GLOBALS['gB_new']  =   $range_B_new;
        $GLOBALS['gBm_new'] =   $range_Bm_new;	 # ----- check
        $GLOBALS['gCp_new'] =   $range_Cp_new;
        $GLOBALS['gC_new']  =   $range_C_new;
        $GLOBALS['gCm_new'] =   $range_Cm_new;
        $GLOBALS['gDp_new'] =   $range_Dp_new;
        $GLOBALS['gD_new']  =   $range_D_new;

       /*$grade_ranges[] = array("grade_name"=>'A',"start"=>$range_A_new,"end"=>'100');
       $grade_ranges[] = array("grade_name"=>'A-',"start"=>$range_A_new,"end"=>$range_Am_new);
       $grade_ranges[] = array("grade_name"=>'B+',"start"=>$range_Am_new,"end"=>$range_Bp_new);
       $grade_ranges[] = array("grade_name"=>'B',"start"=>$range_Bp_new,"end"=>$range_B_new);
       $grade_ranges[] = array("grade_name"=>'B-',"start"=>$range_B_new,"end"=>$range_Bm_new);
       $grade_ranges[] = array("grade_name"=>'C+',"start"=>$range_Bm_new,"end"=>$range_Cp_new);
       $grade_ranges[] = array("grade_name"=>'C',"start"=>$range_Cp_new,"end"=>$range_C_new);
       $grade_ranges[] = array("grade_name"=>'C-',"start"=>$range_C_new,"end"=>$range_Cm_new);
       $grade_ranges[] = array("grade_name"=>'D+',"start"=>$range_Cm_new,"end"=>$range_Dp_new);
       $grade_ranges[] = array("grade_name"=>'D',"start"=>$range_Dp_new,"end"=>$range_D_new);*/


        $grade_ranges[] = array("grade_name"=>'A',"start"=>$range_A_new,"end"=>'100');
        $grade_ranges[] = array("grade_name"=>'A-',"start"=>$range_Am_new,"end"=>$range_A_new);
        $grade_ranges[] = array("grade_name"=>'B+',"start"=>$range_Bp_new,"end"=>$range_Am_new);
        $grade_ranges[] = array("grade_name"=>'B',"start"=>$range_B_new,"end"=>$range_Bp_new);
        $grade_ranges[] = array("grade_name"=>'B-',"start"=>$range_Bm_new,"end"=>$range_B_new);
        $grade_ranges[] = array("grade_name"=>'C+',"start"=>$range_Cp_new,"end"=>$range_Bm_new);
        $grade_ranges[] = array("grade_name"=>'C',"start"=>$range_C_new,"end"=>$range_Cp_new);
        $grade_ranges[] = array("grade_name"=>'C-',"start"=>$range_Cm_new,"end"=>$range_C_new);
        $grade_ranges[] = array("grade_name"=>'D+',"start"=>$range_Dp_new,"end"=>$range_Cm_new);
        $grade_ranges[] = array("grade_name"=>'D',"start"=>$range_D_new,"end"=>$range_Dp_new);



     //   print_r($grade_ranges);exit();
       // $gF_new=$get_c_sd+($F_new*$get_c_sd);
        function general_grade($marks) {
            if($marks>=85)
            {
                $grade = "A";
            }
            else if($marks>=81.50)
            {
                $grade = "A-";
            }
            else if($marks>=78)
            {
                $grade = "B+";
            }
            else if($marks>=74.50)
            {
                $grade = "B";
            }
            else if($marks>=71)
            {
                $grade = "B-";
            }
            else if($marks>=67)
            {
                $grade = "C+";
            }
            else if($marks>=64)
            {
                $grade = "C";
            }
            else if($marks>=60.50)
            {
                $grade = "C-";
            }
            else if($marks>=57)
            {
                $grade = "D+";
            }
            else if($marks>=50)
            {
                $grade = "D";
            }
            else
            {
                $grade = "F";
            }
            return $grade;
        }
        function sd_grade_new($sd_marks) {

            if($sd_marks>$GLOBALS['gA_new'])
            {
                $sd_grade = "A";
            }
            else if($sd_marks>$GLOBALS['gAm_new'] && $sd_marks<=$GLOBALS['gA_new'])
            {
                $sd_grade = "A-";
            }
            else if($sd_marks>$GLOBALS['gBp_new'] && $sd_marks<=$GLOBALS['gAm_new'])
            {
                $sd_grade = "B+";
            }
            else if($sd_marks>$GLOBALS['gB_new'] && $sd_marks<=$GLOBALS['gBp_new'])
            {
                $sd_grade = "B";
            }
            else if($sd_marks>$GLOBALS['gBm_new'] && $sd_marks<=$GLOBALS['gB_new'])
            {
                $sd_grade = "B-";
            }
            else if($sd_marks>$GLOBALS['gCp_new'] && $sd_marks<=$GLOBALS['gBm_new'])
            {
                $sd_grade = "C+";
            }
            else if($sd_marks>$GLOBALS['gC_new'] && $sd_marks<=$GLOBALS['gCp_new'])
            {
                $sd_grade = "C";
            }
            else if($sd_marks>$GLOBALS['gCm_new'] && $sd_marks<=$GLOBALS['gC_new'] )
            {
                $sd_grade = "C-";
            }
            else if($sd_marks>$GLOBALS['gDp_new'] && $sd_marks<=$GLOBALS['gCm_new'])
            {
                $sd_grade = "D+";
            }
            else
            {
                $sd_grade = "D";
            }

            return $sd_grade;
        }
        $student_grade=array();
        foreach ($updated_marks_history as $rec_courseMarksSummary) {
            $student_obtained_marks = $rec_courseMarksSummary->mh_marks;
            $student_marks = $this->grade_marks_percentage($student_obtained_marks, $var_co_id);

           // echo $rec_courseMarksSummary->mh_converted_marks."<br>";

            if($grading_formula_type=='Relative'){
                if($rec_courseMarksSummary->mh_marks>49){
                    $grade = sd_grade_new($rec_courseMarksSummary->mh_converted_marks);
                }
                else{
                    $grade="F";
                }
            }else{
                $grade = general_grade($rec_courseMarksSummary->mh_marks);

            }
            $student_grade[$rec_courseMarksSummary->mh_sai_enrollment_no] = $grade;
        }
        return view('grading.grading_view',
            [
                'courseMarksSummaries'  => $courseMarkSummary,
                'var_co_id'             =>$var_co_id,
                'marks_max_min'         => $marks_max_min,
                'student_grade'         => $student_grade,
                'grading_formula_type'  =>$grading_formula_type,
                'grade_ranges'          =>$grade_ranges,
                'grade_details'         =>$grade_details,
                'get_total_records'     =>$get_total_records
            ]
        );
    }
    public function post_grade_old($var_co_id=0){

        $sessions_values = new Sessions();
        $sessions_values->startSession();

        $all_data  =   Input::all();
        //print_r($all_data);
       // exit();
        $all_grades   = $all_data['grade_name'];
        $grades_start = $all_data['start_range'];
        $grades_end   = $all_data['end_range'];
        if($all_data['grading_type']=="Relative") {
            $array_grade_number = 0;
            while ($array_grade_number < count($all_grades)) {
                $var_range_grade_name = $all_grades[$array_grade_number];
                $var_range_start = $grades_start[$array_grade_number];
                $var_range_end = $grades_end[$array_grade_number];
                //echo $var_range_grade_name."___".$var_range_start."___".$var_range_end."<br>";

                $sql_range = "INSERT INTO marks_grade_ranges (
					mgr_grade,
					mgr_start,
					mgr_end,
					mgr_co_id,
					mgr_order,
					mgr_status,
					mgr_entry_user_id,
					mgr_entry_datetime
				) VALUES (
					'" . $var_range_grade_name . "',
					'" . $var_range_start . "',
					'" . $var_range_end . "',
					'" . $var_co_id . "',
					'" . ($array_grade_number + 1) . "',
					'E',
					'" . $sessions_values->emp_user_id . "',
					current_timestamp
				)";

                DB::insert($sql_range);
                $array_grade_number++;
            }
            $sql_calculation = "INSERT INTO MARKS_GRADE_CALCULATION (
												mgc_co_id,
												mgc_mean,
												mgc_sd,
												MGC_MAX_MARKS,
												MGC_MIN_MARKS,
												MGC_Z_SCORE,
												MGC_Z_SCORE_UNIT,
												mgc_order,
												mgc_status,
												mgc_entry_user_id,
												mgc_entry_datetime
										) VALUES (
											'" . $var_co_id . "',
											'" . round($all_data['grade_mean'], 6) . "',
											'" . round($all_data['grade_sd'], 6) . "',
											'" . $all_data['grade_max_marks'] . "',
											'" . $all_data['grade_min_marks'] . "',
											'" . round($all_data['grade_z_score'], 6) . "',
											'" . round($all_data['grade_z_score_unit'], 6) . "',
											(select coalesce(max(mgc_order),0)+1 as mgc_order from MARKS_GRADE_CALCULATION),
											'E',
											'" . $sessions_values->emp_user_id . "',
											current_timestamp
										)";
            DB::insert($sql_calculation);

        }

        $sai_enrollment_no      =   $all_data['sai_enrollment_no'];
        $grade                  =   $all_data['grade'];
        $total_records = count($sai_enrollment_no);
        $array_number = 0;
        while($array_number<$total_records){
           $var_grade               = $grade[$array_number];
           $var_sai_enrollment_no = $sai_enrollment_no[$array_number];
           $sqlinsert= "INSERT INTO grade_history (
    gh_sai_id,
    gh_sai_enrollment_no,
    gh_sai_semester,
    gh_sai_sdpo_sess_id,
    gh_sai_sdpo_school_id,
    gh_sai_sdpo_dept_id,
    gh_sai_sdpo_prog_id,
    gh_sai_sdpo_shift_id,
    gh_co_id,
    gh_co_sess_id,
    gh_co_school_id,
    gh_co_dept_id,
    gh_co_course_id,
    gh_course_code,
    gh_cd_name,
    gh_co_course_cr,
    gh_co_section,
    gh_co_type,
    gh_grade,
    gh_gpa,
    gh_grade_point,
    gh_desc,
    gh_status,
    gh_order,
    gh_entry_user_id,
    gh_entry_datetime,
    gh_teacher_post,
    gh_teacher_post_old,
    gh_teacher_save,
    gh_teacher_comment,
    gh_teacher_datetime,
    gh_dean_post,
    gh_dean_post_old,
    gh_dean_save,
    gh_dean_comment,
    gh_dean_datetime,
    gh_exam_post,
    gh_exam_comment,
    gh_exam_datetime,
    gh_co_shift_id,
    gh_sc_id,
    gh_pc_id,
    gh_exam_fsubmit,
    gh_exam_save,
    gh_teacher_fsubmit,
    gh_hod_post,
    gh_hod_fsubmit,
    gh_dean_fsubmit,
    gh_grading_type
) SELECT
    sc_sai_id,
    sc_sai_enrollment_no,
    students_apply_info.sai_semester,
    sai_sdpo_sess_id,
    sai_sdpo_school_id,
    sai_sdpo_dept_id,
    sai_sdpo_prog_id,
    sai_sdpo_shift_id,
    co_id,
    co_sess_id,
    co_school_id,
    co_dept_id,
    co_course_id,
    course_code,
    course_cd_name,
    courses_offered.co_course_cr,
    courses_offered.co_section,
    courses_offered.co_type,
    '$var_grade',
    ( (
        SELECT
            eg_grade_point
        FROM
            exam_grades
        WHERE
            TRIM(eg_grade) = TRIM('$var_grade')
    ) * courses_offered.co_course_cr ) AS gh_gpa,
    (
        SELECT
            eg_grade_point
        FROM
            exam_grades
        WHERE
            TRIM(eg_grade) = TRIM('$var_grade')
    ) AS eg_grade_point,
    'Grader based' AS gh_desc,
    'E' AS gh_status,
    (
        SELECT
            nvl(MAX(gh_order),0) + 1
        FROM
            grade_history
    ) AS gh_order,
    766,
    current_timestamp,
    1,
    NULL,
    1,
    NULL,
    current_timestamp,
    1,
    1,
    1,
    NULL,
    current_timestamp,
    1,
    NULL,
    current_timestamp,
    co_shift_id,
    sc_id,
    sc_pc_id,
    1,
    1,
    1,
    1,
    1,
    1,
    '".$all_data['grading_type']."'
FROM
    student_courses
    LEFT JOIN courses_offered ON student_courses.sc_co_id = courses_offered.co_id
    LEFT JOIN students_apply_info ON student_courses.sc_sai_enrollment_no = students_apply_info.sai_enrollment_no
    LEFT JOIN courses_two ON co_course_id = course_id
WHERE
        1 = 1
    AND
(        student_courses.sc_co_id = ".$var_co_id."
    OR
        courses_offered.parent_co_id = ".$var_co_id.")
 and
sc_sai_enrollment_no = '".strtoupper($var_sai_enrollment_no)."' ";
           DB::insert($sqlinsert);
           $array_number++;
       }
    }
    function grade_marks_percentage($sd_marks,$var_co_id) {

        $sql_marks = "SELECT
					 MAX(to_number(mh_marks)) AS max_marks,
   					 MIN(to_number(mh_marks)) AS min_marks
				FROM
					marks_history
				WHERE
						mh_marks NOT IN ('I','W')
					AND
						mh_co_id IN (
							SELECT
								co_id
							FROM
								courses_offered
							WHERE
								co_id = ".$var_co_id."
							UNION
							SELECT
								co_id
							FROM
								courses_offered
							WHERE
								parent_co_id = ".$var_co_id."
						)";
        $rec_marks =  DB::select($sql_marks);
        $max_marks_for      = $rec_marks[0]->max_marks;
        $min_marks_for = $rec_marks[0]->min_marks;
        $result_marks = $sd_marks-($min_marks_for-1);
        $total_marks = $max_marks_for-($min_marks_for-1);
        $now_percentage = (($result_marks/$total_marks)*100);
       // return $max_marks_for."_".$min_marks_for."_".$result_marks."__".$total_marks."__".$now_percentage;
        return $now_percentage;

    }
    function grade_marks_max_min($var_co_id) {

        $sql_marks = "SELECT
					 MAX(to_number(mh_marks)) AS max_marks,
   					 MIN(to_number(mh_marks)) AS min_marks
				FROM
					marks_history
				WHERE
						mh_marks NOT IN ('I','W')
					AND
						mh_co_id IN (
							SELECT
								co_id
							FROM
								courses_offered
							WHERE
								co_id = ".$var_co_id."
							UNION
							SELECT
								co_id
							FROM
								courses_offered
							WHERE
								parent_co_id = ".$var_co_id."
						)";
        $rec_marks =  DB::select($sql_marks);
        return $rec_marks;
    }
    function marks_history($var_co_id) {

    $sql_marks = "SELECT
					 *
				FROM
					marks_history
					LEFT JOIN students_apply_info ON mh_sai_enrollment_no = sai_enrollment_no
				WHERE
						mh_marks NOT IN ('I','W')
					AND
						mh_co_id IN (
							SELECT
								co_id
							FROM
								courses_offered
							WHERE
								co_id = ".$var_co_id."
							UNION
							SELECT
								co_id
							FROM
								courses_offered
							WHERE
								parent_co_id = ".$var_co_id."
						) order by mh_marks desc ";
    $rec_marks =  DB::select($sql_marks);
    return $rec_marks;
}
    function marks_history_updated($var_co_id) {

        $sql_marks = "SELECT
					 *
				FROM
					marks_history
				WHERE
						mh_marks NOT IN ('I','W')
					AND
						mh_co_id IN (
							SELECT
								co_id
							FROM
								courses_offered
							WHERE
								co_id = ".$var_co_id."
							UNION
							SELECT
								co_id
							FROM
								courses_offered
							WHERE
								parent_co_id = ".$var_co_id."
						) order by mh_marks desc ";
        $rec_marks =  DB::select($sql_marks);
        return $rec_marks;
    }

    function get_process_details($co_id){
        $sql = "select * from (
            SELECT assessment_name,
              (SELECT COUNT(*)
              FROM course_criteria_details
              WHERE course_criteria_details.co_id = course_criterias.co_id
             and course_criteria_details.assessment_id = course_criterias.assessment_id
              )as status
            FROM course_criterias
            left join assessments on assessments.id = course_criterias.assessment_id
            WHERE co_id = $co_id
            and assessment_id not in (15)
            )
            where status = 0";
            return response()->json([
                'data' => DB::select($sql),
                'status' => true,
                'message' => ''
            ]);
    }
}
