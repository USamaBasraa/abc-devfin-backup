<?php
/**
 * Created by PhpStorm.
 * User: Nabeel
 * Date: 9/14/2018
 * Time: 12:55 PM
 */

namespace App\Models\Grader;


use Illuminate\Database\Eloquent\Model;
use DB;
use Illuminate\Http\Request;
use App\sessions;

class StudentsApplyInfos extends Model
{
    protected $primaryKey = 'sai_enrollment_no';
    protected $table = 'students_apply_info';
    public $timestamps = false;

}