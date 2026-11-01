<?php

namespace App\Models\StoreProcurement;

use Illuminate\Database\Eloquent\Model;

class Building extends Model
{
    protected $table = 'buildings';
    protected $primaryKey = 'building_id';
    public $timestamps = false;

    protected $fillable = [
        'building_name',
        'building_code'
    ];

    /**
     * Each building can have many asset items allocated to it.
     */
    public function assetItems()
    {
        return $this->hasMany(\App\Models\StoreProcurement\SpAssetItem::class, 'allocation_location', 'building_id');
    }
}
