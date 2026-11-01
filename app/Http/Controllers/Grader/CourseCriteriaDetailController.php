<?php
/**
 * Created by PhpStorm.
 * User: Awais Khan
 * Date: 13-Aug-18
 * Time: 4:47 PM
 */

namespace App\Http\Controllers\Grader;
use App\Http\Controllers\Controller;
use App\Models\Grader\CourseCriteriaDetails;
use App\Models\Grader\CourseCriterias;


class CourseCriteriaDetailController extends Controller
{
    private $ccd;

    public function __construct(CourseCriteriaDetails $courseCriteriaDetails)
    {
        $this->ccd = $courseCriteriaDetails;
    }

    public function index()
    {
        $this->ccd->fetchAllCriteriaDetails();
    }

    public function retrieveCriteriaDetails($co_id)
    {
        return $this->ccd->fetchCriteriaDetails($co_id);
    }

    public static function storeCriteriaDetails($ccd_date,$ccd_details,$co_id,$assessment_id,$criteria_id,$cam_number,$criteria_weightage,$action,$ccd_id)
    {
        //echo $ccd_date."--".$ccd_details."--".$co_id."--".$assessment_id."--".$criteria_id."--".$cam_number."--".$criteria_weightage."-*-".$action."-*-".$ccd_id;exit();
        $ccd = new CourseCriteriaDetails();
        return $ccd->saveCriteriaDetails($ccd_date,$ccd_details,$co_id,$assessment_id,$criteria_id,$cam_number,$criteria_weightage,$action,$ccd_id);

    }
    public function assessment_criteria_details($co_id,$assessmentID){

        $data['details']  = $this->ccd->get_assessment_criteria_details($co_id,$assessmentID);
        $data['criteria'] = CourseCriterias::where(['co_id'=>$co_id, 'assessment_id' => $assessmentID])->first();
        return response()->json($data);

    }

}