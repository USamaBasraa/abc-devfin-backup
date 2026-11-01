<?php

namespace App\Models\StoreProcurement;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpAssetAllocationHistory extends Model
{
    use HasFactory;

    protected $table = 'sp_asset_allocation_history';
    protected $primaryKey = 'id';
    public $timestamps = true; // maps to created_at & updated_at

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

    /**
     * Relationship to Asset Item
     */
    public function assetItem()
    {
        return $this->belongsTo(SpAssetItem::class, 'asset_item_id', 'asset_item_id');
    }

    /**
     * Relationship to Department
     */
    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'dept_id');
    }

    /**
     * Relationship to User (optional: if tracking allocation per user)
     */
    public function allocatedUser()
    {
        return $this->belongsTo(User::class, 'allocated_to_user_id', 'id');
    }
}
