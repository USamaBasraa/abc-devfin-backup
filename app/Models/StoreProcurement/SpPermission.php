<?php

namespace App\Models\StoreProcurement;

use Illuminate\Database\Eloquent\Model;

class SpPermission extends Model
{
    protected $table = 'SP_PERMISSIONS';
    protected $primaryKey = 'sp_id';
    public $timestamps = false;

    protected $fillable = [
        'SCHOOL_ID',
        'DEPT_ID',
        'USER_ID',
        'PERMISSION_TYPE',
        'STATUS',
        'REMARKS',
        'CREATED_AT',
        'UPDATED_AT'
    ];
}
