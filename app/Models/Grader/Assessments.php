<?php
/**
 * Created by PhpStorm.
 * User: bilal.saleem
 * Date: 09/07/2018
 * Time: 3:10 PM
 */

namespace App\Models\Grader;


use Illuminate\Database\Eloquent\Model;
use DB;

class Assessments extends Model
{
    protected $table = 'Assessments';

    public function fetchAssessmentss()
    {
        return self::get();
    }
    public function maxOrder()
    {
        return self::get()->max('assessment_order')+1;
    }
    public function insertAssessment(array $input)
    {
        $assess = null;
        if (isset($input['id'])) {
            $assess = self::find($input['id']);
        } else {
            $assess = new Assessments();
        }
        $assess -> assessment_name    = $input['assessment_name'];
        $assess -> assessment_desc    = $input['assessment_desc'];
        $assess -> assessment_status  = $input['assessment_status'];
        $assess -> assessment_short_name  = $input['assessment_short_name'];
        $assess -> assessment_order   = $input['assessment_order'];
        $assess->save();
        $response = ['success' => true, 'error' => false];
        return('1');
    }

    public function fetchAssessments($co_id,$course_id)
    {
      //  echo $course_id;exit();
        $query_select = "select co_school_id,co_dept_id from COURSES_OFFERED where co_id =$co_id ";
        $result_query_select   = DB::select($query_select);
        $co_school_id     = $result_query_select[0]->co_school_id;
        $co_dept_id       = $result_query_select[0]->co_dept_id;


        $query="SELECT
    assessments.id AS id,
    assessment_name,
    assessment_desc,
    sc_status,
    (
        SELECT
            course_contents.cc_weightage
        FROM
            course_contents
        WHERE
                cc_course_id = $course_id
            AND
                cc_assessments = assessments.id
    ) AS course_content_weightage,
    (
        SELECT
            COURSE_CRITERIAS.id
        FROM
            COURSE_CRITERIAS
        WHERE
                co_id = $co_id
            AND
                assessment_id = assessments.id
    ) AS course_criteria_id,
    (
        SELECT
            COURSE_CRITERIAS.CC_WEIGHTAGE
        FROM
            COURSE_CRITERIAS
        WHERE
                co_id = $co_id
            AND
                assessment_id = assessments.id
    ) AS course_criteria_weightage,
    (
        SELECT
            count(*)
        FROM
            COURSE_CRITERIAS
        WHERE
                co_id = $co_id
      ) AS course_criteria_status,
      $co_id as co_id
FROM
    school_assessments
    LEFT JOIN assessments ON assessment_id = assessments.id
           WHERE
                    1 = 1
                AND
                    sc_status = 'E'
                AND
                    school_id     = $co_school_id
                AND
                    department_id =  $co_dept_id
                    order by assessments.id";
       // echo $query;exit();
        $result_qry   = DB::select($query);
        return $result_qry;


    }
}
