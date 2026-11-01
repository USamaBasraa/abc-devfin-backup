<?php

namespace App\Models\Grader;

use Illuminate\Database\Eloquent\Model;

class Departments extends Model
{
    protected $fillable = [
        'dept_id', 'dept_name',
    ];

    protected $hidden = [
        'id',
    ];

    public function fetch($id=0)
    {
        if($id){
            return self::where('dept_school_id',$id)->pluck('dept_name','dept_id');
        }else{
            return self::orderBy('dept_id','asc')->pluck("dept_name","dept_id");
        }

    }
    public function fetchDepartments()
    {
        return self::get();
    }
}
