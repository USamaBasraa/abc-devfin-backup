<?php
/**
 * Created by PhpStorm.
 * User: bilal.saleem
 * Date: 27/08/2018
 * Time: 3:11 PM
 */

namespace App\Models\Grader;


use Illuminate\Database\Eloquent\Model;
use DB;

class SchoolAssessments extends Model
{
  //  protected $table = 'SchoolAssessments';
    protected $fillable = [ 'assessment_id','school_id','department_id','sc_status','sc_order' ];

    public function fetchSchoolAssessment()
    {
        //  return self::get();
       $schoolassessment="SELECT school_assessments.id as sc_id,
                          school_name,
                          dept_name,
                          sc_status,
                          sc_order,
                          assessment_name,
                          assessment_id,
                          school_assessments.school_id,
                          department_id
                        FROM school_assessments
                        LEFT JOIN assessments ON school_assessments.assessment_id=assessments.id
                        LEFT JOIN schools ON school_assessments.school_id = schools.school_id
                        LEFT JOIN departments ON school_assessments.department_id = departments.dept_id
                        order by sc_order asc";

       $records = DB::select($schoolassessment);
       return $records;
    }

    public function maxOrder()
    {
        return self::get()->max('sc_order')+1;
    }


    public function insertSchoolAssessment(array $input)
    {
        $schoolassessment = null;
        if (isset($input['id'])) {
            $schoolassessment = self::find($input['id']);
        } else {
            $schoolassessment = new SchoolAssessments();
        }
        $schoolassessment -> id            = $input['id'];
        $schoolassessment -> assessment_id = $input['assessment_id'];
        $schoolassessment -> school_id     = $input['school_id'];
        $schoolassessment -> department_id = $input['dept_id'];
        $schoolassessment -> sc_status     = $input['sc_status'];
        $schoolassessment -> sc_order      = $input['sc_order'];
        try {
            $schoolassessment->save();
            $response = ['success' => true, 'error' => false];
            return ('1');
        }catch (Exception $e) {
            return "2";
        }
    }
}