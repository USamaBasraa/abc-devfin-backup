<?php

namespace App\Models\StoreProcurement;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrderItem extends Model
{

    protected $table = 'sp_purchase_order_items';
    protected $primaryKey = 'poi_id';
    public $timestamps = false;

    protected $fillable = ['po_id', 'item_name', 'quantity', 'unit_price', 'total_price'];

}
