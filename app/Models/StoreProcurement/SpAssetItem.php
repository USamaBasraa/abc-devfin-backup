<?php



namespace App\Models\StoreProcurement;

use Illuminate\Database\Eloquent\Model;


class SpAssetItem extends Model
{
    protected $table = 'sp_asset_items';
    protected $primaryKey = 'asset_item_id';
    public $timestamps = false;

    protected $fillable = [
        'asset_id','serial_number','allocation_status','allocation_department',
        'allocation_location','allocation_date','return_date','scrap_status',
        'scrap_date','remarks','status','created_at','updated_at','assign_to',
        'inspection_status','unique_asset_code','physical_condition','item_packing',
        'damage_found','specification_meets','inspection_remarks',
    ];
}
