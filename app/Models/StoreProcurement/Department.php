<?php

namespace App\Models\StoreProcurement;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $table = 'departments';  // new table you should create
    protected $primaryKey = 'rec_id';
    public $timestamps = true;

    // protected $fillable = [
    //     'qr_id',
    //     'department_id',
    //     'recommended_flag',
    //     'remarks',
    //     'entry_user_id'
    // ];


}
