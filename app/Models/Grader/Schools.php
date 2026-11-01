<?php

namespace App\Models\Grader;

use Illuminate\Database\Eloquent\Model;
use App\sessions;

class Schools extends Model
{
    protected $fillable = [
        'school_id', 'school_code', 'school_name',
    ];

    protected $hidden = [
        'school_id',
    ];

    public function fetch(){
        
        $sess = new sessions();
        $sess->startSession();

         // print_r($sess); exit;

        $directAccessGroup = [122,644,284];

        if($sess->emp_school_id && !(in_array($sess->emp_group_id,$directAccessGroup)) ){
            return self::where('school_id',$sess->emp_school_id)->pluck('school_name','school_id');
        }else{
            return self::orderBy('school_id','asc')->pluck("school_name","school_id");
        }

    }
  public function fetchSchools()
    {
        return self::get();
    }

}
