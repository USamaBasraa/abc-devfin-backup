<?php

namespace App\Models\Grader;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Grader\CourseOfferingContents;

class MasterTimetable extends Model
{

    protected $primaryKey   = 'tt_id';
    protected $table        = 'master_timetable';
    public    $timestamps   = false;

    public function lecture_contents()
    {
        return $this->hasMany(CourseOfferingContents::class, 'coc_tt_id', 'tt_id');
    }

}


