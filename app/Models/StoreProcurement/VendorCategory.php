<?php

namespace App\Models\StoreProcurement;

use Illuminate\Database\Eloquent\Model;


class VendorCategory extends Model
{
       protected $table = 'sp_vendor_category';
    protected $primaryKey = 'category_id';
    public $timestamps = false;

    protected $fillable = [
        'pinnacle_account_id',
        'category_name',
        'category_description',
        'parent_department_id',
        'budget_type',
        'q_parent_dept_required',
        'q_head_approval_required',
        'q_parent_dept_users',
        'q_head_approval_users',
        'entry_usrname'
    ];

    // Relationship: VendorCategory has many QuotationRequests
    public function quotationRequests()
    {
        return $this->hasMany(QuotationRequest::class, 'bh_id', 'category_id');
    }

    public function parentDepartment()
    {
        return $this->belongsTo(\App\Models\Grader\Departments::class, 'parent_department_id', 'dept_id');
    }

    // Accessor to get parent department name or "By Self"
    public function getParentDepartmentNameAttribute()
    {
        if ($this->parent_department_id == 0 || $this->parent_department_id === null) {
            return 'By Self';
        }
        return $this->parentDepartment ? $this->parentDepartment->dept_name : '-';
    }

    // Intentionally no creator relation since we now store username string in `entry_usrname`
}
