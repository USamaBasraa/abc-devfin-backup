<?php
namespace App\Models\StoreProcurement;

use Illuminate\Database\Eloquent\Model;

class GatePassItem extends Model
{
    protected $table = 'SP_GATE_PASS_ITEMS';
    protected $primaryKey = 'GPI_ID';
    public $timestamps = true;

    protected $fillable = [
        'GP_ID', 'ITEM_ID', 'SERIAL_NUMBER',
        'QUANTITY', 'INSPECTED_FLAG', 'INSPECTION_REMARKS'
    ];

    public function gatePass()
    {
        return $this->belongsTo(GatePass::class, 'GP_ID', 'GP_ID');
    }

    public function purchaseOrderItem()
    {
        return $this->belongsTo(PurchaseOrderItem::class, 'ITEM_ID', 'POI_ID');
    }
}
