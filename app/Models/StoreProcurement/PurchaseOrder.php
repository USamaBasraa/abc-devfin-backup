<?php

namespace App\Models\StoreProcurement;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PurchaseOrder extends Model
{


    protected $table = 'SP_PURCHASE_ORDER';
    protected $primaryKey = 'po_id';
    public $incrementing = true; // assuming Oracle sequence/trigger handles it
    protected $keyType = 'int';  // since PO_ID is NUMBER

    public $timestamps = true; // if you're using CREATED_AT/UPDATED_AT

    // protected $fillable = ['po_number', 'vendor_id', 'po_date', 'remarks'];
        protected $fillable =
        [
        'vendor_quotation_id',
        'quantity',
        'warranty',
        'delivery_time',
        'vendor_id',
        'remarks',
        'total_price',
        'unit_price',
        'budget_type',
        'approvel_status',
        'approvel_date',
        'reject_remarks',
        'approvel_date',
        'currency',
        'po_date',
        'qr_id',
        'entry_username',
        'po_status',
        'po_generated_at',
        'po_generated_by',
        'po_no',
        'delivered_quantity',
        'remaining_quantity',
        'budget_amount',

         'item_title' ,
         'brand',
         'model'
    ];


    // public function items()
    // {
    //     return $this->hasMany(PurchaseOrderItem::class, 'po_id', 'po_id');
    // }
    public function items()
    {
        return $this->hasManyThrough(
            BudgetItem::class,          // Final model
            QuotationRequest::class,    // Intermediate model
            'po_id',                    // FK on SP_QUOTATION_REQUEST
            'item_id',                  // FK on SP_BUDGET_ITEMS
            'po_id',                    // Local key on SP_PURCHASE_ORDER
            'item_id'                   // Local key on SP_QUOTATION_REQUEST
        );
    }


     public function vendor()
    {
        return $this->belongsTo(Sp_vendor::class, 'vendor_id', 'vendor_id');
    }

    public function vendorQuotation()
    {
        return $this->belongsTo(VendorQuotation::class, 'vendor_quotation_id', 'vq_id');
    }

    public function quotationRequests()
{
    return $this->hasMany(QuotationRequest::class, 'po_id', 'po_id');
}

 // 🔗 A PurchaseOrder has many GatePasses
    // public function gatePasses()
    // {
    //     return $this->hasMany(GatePass::class, 'PO_ID', 'PO_ID')
    //     ->where('DELIVERY_STATUS', 'Partially Delivered' or null);
    // }

    public function gatePasses()
    {
        return $this->hasMany(GatePass::class, 'PO_ID', 'PO_ID')
            ->where(function ($query) {
                $query->where('DELIVERY_STATUS', 'Partially Delivered')
                    ->orWhereNull('DELIVERY_STATUS');
            });
    }
    public function gatePass()
    {
        return $this->hasOne(GatePass::class, 'po_id', 'po_id');
    }


    // ✅ Approve this PO
    public function approve()
    {
        $this->APPROVEL_STATUS = 'approved';
        $this->APPROVEL_DATE = Carbon::now();
        $this->REJECT_REMARKS = null;
        return $this->save();
    }

    // ✅ Reject this PO
    public function reject($remarks = null)
    {
        $this->APPROVEL_STATUS = 'rejected';
        $this->APPROVEL_DATE = Carbon::now();
        $this->REJECT_REMARKS = $remarks;
        return $this->save();
    }

    // ✅ Reset (Pending)
    public function markPending()
    {
        $this->APPROVEL_STATUS = 'pending';
        $this->APPROVEL_DATE = null;
        $this->REJECT_REMARKS = null;
        return $this->save();
    }


}
