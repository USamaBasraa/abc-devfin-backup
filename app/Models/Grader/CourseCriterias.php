<?php

namespace App\Models\Grader;

use Illuminate\Database\Eloquent\Model;
use DB;
use App\sessions;


class CourseCriterias extends Model
{
    protected $fillable = [
        'id', 'co_id', 'assessment_id', 'criteria_type_id', 'cc_weightage', 'cc_status'
    ];

    protected $hidden = [
        'id',
    ];
    public function fetchCoursesCriteria($co_id)
    {
     // echo '1230';exit;
        if ($co_id) {
            return self::select('COURSE_CRITERIAS.id',
                'COURSE_CRITERIAS.id as cc_new_id',
                'course_criterias.co_id',
                'courses_two.course_code',
                'courses_two.course_cd_name',
                'courses_offered.co_course_cr',
                'courses_offered.co_section',
                'course_criterias.assessment_id',
                'assessments.assessment_name',
                'assessments.assessment_icon',
                'criteria_type_id',
                'co_school_id',
                'cc_weightage',
                'cc_desc',
                'cc_status', // CC_METHOD_ID
                'cc_method_id',
                'cc_order',
				'lock_status',
                DB::raw('GET_ASSESSMENT_COUNT(course_criterias.co_id,course_criterias.assessment_id) as total_assessments'),
                DB::raw('GET_MID_APPROVAL_STATUS(course_criterias.co_id) as mid_approval_status'),
                DB::raw('GET_FINAL_APPROVAL_STATUS(course_criterias.co_id) as final_approval_status'),
                DB::raw('GET_MARKS_SUMMARY_CONVERSION_STATUS(course_criterias.co_id) as marks_conversion_status'),
                DB::raw('GET_GRADING_STATUS(course_criterias.co_id) as grading_status'))
                ->join('courses_offered', 'course_criterias.co_id', '=', 'courses_offered.co_id')
                ->join('courses_two', 'courses_offered.co_course_id', '=', 'courses_two.course_id')
                ->join('ASSESSMENTS', 'COURSE_CRITERIAS.ASSESSMENT_ID', '=', 'ASSESSMENTS.id')
                ->where([
                    ['course_criterias.co_id', $co_id],
                    ['cc_status', 'E'],
                    ['assessment_id', '!=', '15'],
                ])
                ->orderby('assessment_id', 'asc')
                ->get();
            //  ->toSql();
        } else {
            return response()->json([
                'success' => false,
                'error' => true,
                'error_details' => 'CO ID Missing'
            ]);
        }
    }
    public function getCourseCriteria(){

        return 1;

    }
    public function fetchCoursesCriteriaRptg($co_id)
    {
       
        if($co_id){
            return $result=self::select('COURSE_CRITERIAS.id',
                'course_criterias.co_id',
                'courses_two.course_code',
                'courses_two.course_cd_name',
                'courses_offered.co_course_cr',
                'courses_offered.co_section',
                'course_criterias.assessment_id',
                'course_criterias.best_of_no',
                'grading_methods.name AS method_name',
                'assessments.assessment_name',
                'assessments.assessment_icon',
                'criteria_type_id',
                'cc_weightage',
                'cc_desc',
                'cc_status',
                'cc_order',
                DB::raw('GET_FINAL_APPROVAL_STATUS(course_criterias.co_id) as approval_status'),
                DB::raw('(SELECT COUNT(*) FROM course_criteria_details WHERE co_id = course_criterias.co_id AND assessment_id = course_criterias.assessment_id) as total_enter'),
                DB::raw('GET_MARKS_SUMMARY_CONVERSION_STATUS(course_criterias.co_id) as marks_conversion_status'))
                ->leftJoin('courses_offered', 'course_criterias.co_id', '=', 'courses_offered.co_id')
                ->leftJoin('courses_two', 'courses_offered.co_course_id', '=', 'courses_two.course_id')
                ->leftJoin('ASSESSMENTS', 'COURSE_CRITERIAS.ASSESSMENT_ID', '=', 'ASSESSMENTS.id')
                ->leftJoin('grading_methods','COURSE_CRITERIAS.cc_method_id', '=', 'grading_methods.id')
                ->where([
                    ['course_criterias.co_id', $co_id],
                    ['cc_status', 'E']
                ])
                ->orderby('assessment_id', 'asc')
              ->get();
           


        }else{
            return response()->json([
                'success' => false,
                'error' => true,
                'error_details' => 'CO ID Missing'
            ]);
        }

    }
    public function store($get_all_inputs){
        $sess = new sessions();
        $sess->startSession();




      //  print_r($get_all_inputs);exit();

        $co_id = $get_all_inputs['co_id'];
        $course_criteria = new self;
        for($array_id = 0;$array_id<count($get_all_inputs['weightage']);$array_id++) {
            // value recieved
            $waitage                    = $get_all_inputs['weightage'][$array_id];
            $assessment_id              = $get_all_inputs['assessment_id'][$array_id];
            $course_criteria_id         = $get_all_inputs['course_criteria_id'][$array_id];
            // update process
         //   echo $waitage."_".$assessment_id."_".$course_criteria_id."<br>";

           // $affectedRows = DB:: Post::where("id",$course_criteria_id)->update(["cc_desc" => "Updated title"]);

     if($course_criteria_id>1){

         DB::table('course_criterias')
             ->where('id',$course_criteria_id)
             ->update(['cc_weightage' => $waitage]);
     }else{
         $criteria = new self;
         $criteria->fill(
             [
                 'co_id'         =>$co_id,
                 'assessment_id' =>$assessment_id,
                 'cc_weightage' =>$waitage,
                 'cc_entry_user' =>$sess->sisuser_id,
                 'cc_status'     =>'E'
             ]
         );
         $criteria->save();
     }



        }
    }
    public function course_lock_status($co_id)
    {
      //  echo "===".$co_id;exit;
        if($co_id){
            return self::select('lock_status')
                        ->where([
                            ['course_criterias.co_id', $co_id],
                            ['cc_status', 'E']
                        ])
                        ->get();

        }
    }
    static function coursecriteria_selected( $course_id){

        $student_marks="select 
assessment_name,
name,
total_missing_marks,
total_entered,
best_of_no,
cc_weightage,
inst_count,
cc_method_id,
case 
when cc_method_id=1 then total_entered
when cc_method_id=2 then best_of_no
when cc_method_id=3 then best_of_no
when cc_method_id=4 then inst_count
end as show_weightage
from (
SELECT assessment_name, name,
        GET_ASSESSMENT_MISSING_MARKS(course_criterias.co_id,course_criterias.ASSESSMENT_ID) AS total_missing_marks,
        (SELECT COUNT(*) FROM course_criteria_details WHERE co_id = course_criterias.co_id AND assessment_id = course_criterias.assessment_id) AS total_entered,
        course_criterias.best_of_no, course_criterias.cc_weightage,
        (select count(*) from COURSE_CRITERIA_DETAILS where COURSE_CRITERIA_DETAILS.co_id = course_criterias.co_id
        and course_criteria_details.CCD_GRADING_STATUS='E' 
        and COURSE_CRITERIA_DETAILS.ASSESSMENT_ID=course_criterias.ASSESSMENT_ID) AS inst_count,
        course_criterias.CC_METHOD_ID
        FROM course_criterias
        LEFT JOIN assessments ON course_criterias.assessment_id = assessments.id
        LEFT JOIN grading_methods ON course_criterias.cc_method_id = grading_methods.id
        WHERE course_criterias.co_id  =$course_id
        ) ";

        return DB::select($student_marks);

    }

}
