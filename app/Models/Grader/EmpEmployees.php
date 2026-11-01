<?php
/**
 * Created by PhpStorm.
 * User: Awais Khan
 * Date: 03-Sep-18
 * Time: 2:23 PM
 */

namespace App\Models\Grader;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


class EmpEmployees extends Model
{
    protected $primaryKey = 'employee_id';

    protected $fillable = [
        'employee_id', 'prefix', 'emp_f_name','emp_l_name','emp_cnic', 'emp_type', 'emp_email', 'emp_school_id', 'emp_dept_id','emp_contact_no',
    ];


    public function user()
    {
        //return 'ok';
        return $this->hasOne('App\Models\User', 'username', 'emp_ad_username');
    }

    public function checkHodExist($dept_id)
    {
        return DB::select('SELECT check_dept_hod('.$dept_id.') dept_hod_status FROM dual');
    }
    public function get_employee($dept_id)
    {
        Model::where('id', 1)
            ->pluck('name', 'surname')
            ->all();
       // return DB::select('SELECT check_dept_hod('.$dept_id.') dept_hod_status FROM dual');
    }



}