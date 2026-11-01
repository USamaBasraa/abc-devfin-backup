<?php

namespace App\Models\StoreProcurement;

use Illuminate\Database\Eloquent\Model;


class SpAsset extends Model
{
    protected $table = 'sp_assets';
    protected $primaryKey = 'asset_id';
    public $timestamps = false; // since you're using TIMESTAMP default CURRENT_TIMESTAMP

    protected $fillable = [
        'asset_code','po_id','grn_id','name','category','brand','model',
        'order_quantity','received_quantity','received_status','inspection_status',
        'warranty_period','warranty_start_date','warranty_end_date','purchase_date',
        'vendor_id','status','created_at','updated_at','currency','qr_id', 'vq_id', 'bh_id', 'item_remarks', 
        'budget_year_id', 'budget_type', 'item_title',
        'total_price', 'vendor_id', 'warranty', 'vendor_quotation_id'
    ];

    // app/Models/SpAsset.php
public function items()
{
    return $this->hasMany(\App\Models\StoreProcurement\SpAssetItem::class, 'asset_id', 'asset_id');
}

}
