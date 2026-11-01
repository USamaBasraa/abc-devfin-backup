<?php

namespace App\Models\StoreProcurement;

use Illuminate\Database\Eloquent\Model;

class Sp_vendor extends Model
{
    protected $table = 'sp_vendors';
    protected $primaryKey = 'vendor_id';
    public $timestamps = false;

    protected $fillable = [
        'vendor_id',
        'pinnacle_supplier_id',
        'vendor_name',
        'vendor_contact_person',
        'vendor_phone',
        'vendor_email',
        'vendor_address',
        'vendor_category_id',
        'vendor_master_id',
        'vendor_registration_date',
        //bank info
        'supplier',
        'ntn_cnic',
        'attn',
        'beneficiary_name',
        'bank_name',
        'bank_iban',
        'is_edited',
        'edited_fields',
    ];

    protected $casts = [
        'is_edited' => 'integer',
    ];


    public function category()
    {
        return $this->belongsTo(VendorCategory::class, 'vendor_category_id', 'category_id');
    }

    public function masterCategory()
    {
        return $this->belongsTo(VendorMasterCategory::class, 'vendor_master_id', 'master_id');
    }



}
