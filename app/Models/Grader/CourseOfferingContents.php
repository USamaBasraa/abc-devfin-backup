<?php

namespace App\Models\Grader;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;

class CourseOfferingContents extends Model
{

    protected $primaryKey   = 'coc_tt_id';
    protected $table        = 'course_offering_contents';
    public    $timestamps   = false;

    public function lecture_contents($slot_id)
    {
        return  CourseOfferingContents::where('coc_tt_id', '=', $slot_id)
        ->select('coc_details')
        ->get();
    }

}


