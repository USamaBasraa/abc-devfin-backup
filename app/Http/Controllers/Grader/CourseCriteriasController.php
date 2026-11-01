<?php


namespace App\Http\Controllers\Grader;

use App\Http\Controllers\Controller;

use App\Models\Grader\CourseCriteriaDetails;
use Illuminate\Http\Request;
use App\Models\Grader\CourseCriterias;
use App\Http\Requests\StoreCourseCriteria;
use App\Models\Grader\Assessments;
use Illuminate\Support\Facades\DB;


class CourseCriteriasController extends Controller
{
    private $sortBy = 'id';
    private $sortDirection = 'ASC';

    private $courseCriteria;

    public function __construct(CourseCriterias $courseCriteria)
    {
        $this->courseCriteria = $courseCriteria;
    }

    public function index()
    {
        //TBD
    }

    public function dashboardCoursesCriteria($co_id)
    {

        $response = $this->courseCriteria->fetchCoursesCriteria($co_id);
        if($response!='[]'){
            return $response;
        }else{
            return 'Zero';
        }
    }

    public function load_criteria($get_co_id=0,$get_course_id=0)
    {
        //echo $get_co_id;exit();
        $assessmnet = new Assessments();
        $get_assessments = $assessmnet->fetchAssessments($get_co_id,$get_course_id);
        $inserted_assessmnet = $this->courseCriteria->fetchCoursesCriteria($get_co_id);
        //dd($get_assessments);
        return view('grader.teacher.coursecriteria_load',['assesment'=>$get_assessments,'inserted_assessmnet'=>$inserted_assessmnet,'co_id'=>$get_co_id]);
    }
    public function criteria_store($get_assessment_name,$co_id)
    {
        $store_attendance =  $this->courseCriteria->store($get_assessment_name,$co_id);
        return $store_attendance;
    }

    public function criteria_save(StoreCourseCriteria $request){

        $get_all_inputs = $request->all();
        $all_assessment_id = $get_all_inputs['assessment_id'];
        $co_id = $get_all_inputs['co_id'];

        $data = DB::select('select * from course_criterias where co_id ='. $co_id);

        $already_entered_assessments = count($data);
        $updated_entered_assessments = 0;
        foreach($data as $rec){
            if(in_array($rec->assessment_id,$all_assessment_id)){
                $updated_entered_assessments++;
            }
        }

        if($updated_entered_assessments<$already_entered_assessments){
                return response()->json(['status' => false, 'message' => 'Invalid Data']);
        }

         $get_total_waitage = array_sum($request->input('weightage'));

         if($get_total_waitage!=100){

            return response()->json(['status' => false, 'message' => 'Accumulative Waitage must be equal to 100']);
         }


         for($array_id = 0;$array_id<count($get_all_inputs['weightage']);$array_id++) {
             $waitage                    = $get_all_inputs['weightage'][$array_id];
             $assessment_id              = $get_all_inputs['assessment_id'][$array_id];
             $course_criteria_id         = $get_all_inputs['course_criteria_id'][$array_id];

            if( !($waitage) ){
                return response()->json(['status' => false, 'message' => 'Some Weitage are empty']);
             }

         }

        if($get_total_waitage==100) {
            $result = $this->courseCriteria->store($request->all());
            return response()->json(['status' => true, 'message' => 'Data Save Successfully']);
        }else{
            return response()->json(['status' => false, 'message' => 'Waitage must be equal to 100']);
        }
    }


    public static function checkLockStatus($co_id)
    {
        $cc         = new CourseCriterias();
        $cc_data    = $cc->course_lock_status($co_id);

        return $cc_data[0]['lock_status'];
    }
    public function get_course_criterias($courseOfferID){

        $query_update      = "update COURSE_ASSESSMENT_MARKS set cam_grading_status='E'
									where  co_id = ".$courseOfferID;
        DB::update($query_update);
        $query_update_criteria_details      = " update COURSE_CRITERIA_DETAILS set
													   CCD_WEIGHTAGE=0,
													   student_wise_weightage=0,
													   course_wise_weightage=0,
													   instructor_wise_weightage=0,
													   ccd_method_id=1,
													   ccd_grading_status='E',
													   ccd_status='E'
													   where co_id =  ".$courseOfferID;
        DB::update($query_update_criteria_details);
        $query_update_criteria      = "update COURSE_CRITERIAS
								   set CC_METHOD_ID=1,
									   best_of_no=null
								   where co_id = ".$courseOfferID;
        DB::update($query_update_criteria);

        $sql_school_id = "select co_school_id from COURSES_OFFERED where CO_ID =".$courseOfferID;
        $obj_school_id =DB::select($sql_school_id);
        // echo $obj_school_id[0]->co_school_id;

        $obj        = new CourseCriterias();
        //  $results    = $obj->fetchCoursesCriteria($courseOfferID);

        $data['data'] = $obj->fetchCoursesCriteria($courseOfferID);
        $data['school_id'] = $obj_school_id[0]->co_school_id;

        // $results = CourseCriterias::where('CO_ID',$courseOfferID)->get();
        return response()->json($data);
    }
    function coursecriteria_selected($courseID = ''){
        $resArr['check_count']  = 0;
        $resArr['data']         = CourseCriterias::coursecriteria_selected($courseID);
        $resArr['message']      = 'OK';
        $resArr['next_status']  = false;
        return response()->json($resArr);
    }
    public function get_assessment_count($co_id,$assessment_id){
        $count  = CourseCriteriaDetails::where('assessment_id',$assessment_id)->where('co_id',$co_id)->get()->count();
        $res    = CourseCriterias::where(['assessment_id'=>$assessment_id, 'co_id'=>$co_id])->first();
        return response()->json(['status'=>true,'count'=>$count,'best_of' => $res->best_of_no]);
    }
    function update_criteria_best_of(Request $request){

        $co_id          = $request->get('co_id');
        $assessment_id  = $request->get('assessment_id');
        $best_of        = $request->get('best_of');
        $method         = $request->get('method_id');


        /* GET THE TOTAL WEIGHTAGE & SET BEST OF  */
        $courseCri                  = CourseCriterias::where('co_id',$co_id)->where('assessment_id',$assessment_id)->first();
        $courseCri->best_of_no      = $best_of;
        $total_weightage = $courseCri->cc_weightage;
        $courseCri->save();

        /* CALCULATE WEIGHTAGE  */
        $best_of_val    = $total_weightage/$best_of;
        $updateArr      = array();
        if($method == 2){
            //  $updateArr['student_wise_weightage'] = $best_of_val;
            $updateArr['course_wise_weightage']  = 0;
        }elseif ($method == 3){
            $updateArr['student_wise_weightage'] = 0;
            // $updateArr['course_wise_weightage']  = $best_of_val;
        }
        CourseCriteriaDetails::where(['co_id'=>$co_id, 'assessment_id'=>$assessment_id])->update($updateArr);

        /* RETURN  */
        return response()->json(true);
    }
    function update_instructor_select_criteria(Request $request){
        // print_r($request->all());
        $input =  $request->except('iselect_cb');
        foreach($input as $key => $val){

            $value = (float)$val;

            // echo $key." - ".$value; exit;
            $obj = CourseCriteriaDetails::find($key);
            $obj->instructor_wise_weightage = $value;
            if($value == 0){
                $obj->ccd_grading_status = 'D'; // CCD_STATUS
            }else{
                $obj->ccd_grading_status = 'E';
            }
            $obj->save();
        }
        return response()->json(true);
        // CourseCriteriaDetails::find();
    }
    function update_single_criteria_method(Request $request){
        $co_id           = $request->get('co_id');
        $asses_id        = $request->get('asses_id');
        $method_id       = $request->get('method_id');

        $courseCri = CourseCriterias::where(['co_id'=>$co_id, 'assessment_id'=>$asses_id])->first();
        $courseCri->cc_method_id = $method_id;
        $courseCri->save();


        $ccdUpdateArr['ccd_method_id'] = $method_id;

        if($method_id == 1){
            $ccdUpdateArr['instructor_wise_weightage'] = '0';
            $ccdUpdateArr['student_wise_weightage'] = '0';
            $ccdUpdateArr['course_wise_weightage'] = '0';
        }elseif ($method_id == 2){ // student
            $ccdUpdateArr['instructor_wise_weightage'] = '0';
            $ccdUpdateArr['course_wise_weightage'] = '0';
            $ccdUpdateArr['ccd_weightage'] = '0';
        }elseif ($method_id == 3){ // course
            $ccdUpdateArr['instructor_wise_weightage'] = '0';
            $ccdUpdateArr['student_wise_weightage'] = '0';
            $ccdUpdateArr['ccd_weightage'] = '0';
        }elseif ($method_id == 4){ // instructor
            $ccdUpdateArr['student_wise_weightage'] = '0';
            $ccdUpdateArr['course_wise_weightage'] = '0';
            $ccdUpdateArr['ccd_weightage'] = '0';
        }


        CourseCriteriaDetails::where(['co_id'=>$co_id, 'assessment_id'=>$asses_id])->update($ccdUpdateArr);
        return response()->json(true);
    }


}
