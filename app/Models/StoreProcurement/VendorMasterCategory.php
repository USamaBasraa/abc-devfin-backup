<?php

namespace App\Models\StoreProcurement;

use Illuminate\Database\Eloquent\Model;

class VendorMasterCategory extends Model
{
    protected $table = 'sp_vendor_master_category';
    protected $primaryKey = 'master_id';
    public $timestamps = false;

    protected $fillable = [
        'master_id',
        'title',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
    ];
}
