<?php

namespace App\Models\StoreProcurement;

use Illuminate\Database\Eloquent\Model;
class SpAssetAllocation extends Model
{
    protected $table = 'sp_asset_allocations';

    protected $fillable = [
        'asset_item_id',
        'serial_number',
        'department_id',
        'allocated_to_user_id',
        'allocated_location',
        'allocation_date',
        'return_date',
        'allocation_status',
        'remarks',
    ];

    public function assetItem()
    {
        return $this->belongsTo(SpAssetItem::class, 'asset_item_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'allocated_to_user_id');
    }
}
