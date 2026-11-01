<?php

namespace App\Models\StoreProcurement;

use Illuminate\Database\Eloquent\Model;

class StoreBudgetHead extends Model
{
    protected $table = 'SP_STORE_BUDGET_HEADS';
    protected $primaryKey = 'sbh_id';
    public $timestamps = true;

    protected $fillable = [
        'dept_id', 'ay_id', 'head_title', 'remarks', 'entry_username', 'entry_dept_id'
    ];

    public function items()
    {
        return $this->hasMany(StoreBudgetItem::class, 'sbh_id', 'sbh_id');
    }

    public function department()
    {
        return $this->belongsTo(\App\Models\Grader\Departments::class, 'dept_id', 'dept_id');
    }

    public function academicYear()
    {
        return $this->belongsTo(\App\Models\AcademicYear::class, 'ay_id', 'ay_id');
    }

    public function getHeadTitleAttribute($value)
    {
        return $value ?: 'Store Items';
    }
}
