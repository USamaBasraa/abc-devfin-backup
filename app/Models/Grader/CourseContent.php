<?php

namespace App\Models\Grader;

use Illuminate\Database\Eloquent\Model;
use DB;
use Illuminate\Http\Request;
use App\sessions;
use Illuminate\Support\Facades\Auth;

class CourseContent extends Model{


    protected   $primaryKey   = 'id';
    protected   $table        = 'course_contents';
  //   public      $timestamps   = false;
    // protected   $fillable = [
    //     'co_id', 'co_sess_id', 'co_school_id',
    // ];

    // protected $hidden = [
    //     'co_id',
    // ];


    
  public static function getCourseContentsByCoID($co_id){

    $course       =   "select co_course_id from courses_offered where co_id = $co_id ";
    $resultcourse =   DB::select($course);
    $my_course_id =   (isset($resultcourse[0]->co_course_id))?$resultcourse[0]->co_course_id:0;
    
    
    $sql = "SELECT * from ( SELECT id,cc_type, cc_lecture_no, cc_lecture_content, id as coc_id, 1 as type,
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
and cc_teacher_id='".auth()->user()->employee_id."'
and cc_type != 'description'
  union all
 SELECT
    (SELECT MIN(id) FROM COURSE_CONTENTS WHERE cc_co_id = $co_id ) AS tt_id, 'content' AS cc_type,
    (SELECT MIN(id) FROM COURSE_CONTENTS WHERE cc_co_id = $co_id) AS cc_lecture_no,
    COC_DETAILS AS cc_lecture_content, coc_course_content_id AS coc_id, 2 as type,
    to_number(coc_continuation),
    COALESCE (to_number(coc_covered),0), 2 as coc_content_type
  FROM COURSE_OFFERING_CONTENTS
  WHERE coc_co_id = $co_id
  AND COC_DETAILS IS NOT NULL AND coc_content_type = 2 and coc_content_entry_type = 2
  )
  order by id";

  //echo  $sql; exit;

   
    return DB::select($sql);
  }
  


}
