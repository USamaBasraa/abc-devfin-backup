<?php
namespace App\Models\StoreProcurement;

use Illuminate\Database\Eloquent\Model;

class GatePass extends Model
{
    protected $table = 'SP_GATE_PASS';
    protected $primaryKey = 'gp_id';
    public $timestamps = true;

    protected $fillable = [
        'PO_ID', 'DELIVERY_PERSON', 'CNIC', 'CONTACT_NO',
        'VEHICLE_NO', 'REMARKS', 'ENTRY_USER_ID','DELIVERED_QUANTITY' ,
        'DELIVERY_REMARKS' ,
        'DELIVERY_STATUS',
    ];

    public function items()
    {
        return $this->hasMany(GatePassItem::class, 'gp_id', 'gp_id');
    }

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class, 'PO_ID', 'PO_ID');
    }
}
