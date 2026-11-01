<?php

namespace App\Models\StoreProcurement;

use Illuminate\Database\Eloquent\Model;

class VendorQuotation extends Model
{
    protected $table = 'sp_vendor_quotations';
    protected $primaryKey = 'vq_id';

    protected $fillable = [
        'vendor_id',
        'quotation_request_id',
        'qr_version',        // NEW: Which request version this quotation is for
        'quantity_quoted',   // NEW: Quantity quoted by vendor
        'is_current',        // NEW: Is this the current/latest quotation
        'item_title',
        'brand',
        'model',
        'quantity',
        'rate',
        'advance_required',
        'advance_percent',
        'price',
        'currency',
        'delivery_time',
        'remarks',
        'warranty',
        'specifications',
        'attachment'
    ];


    public function request()
    {
        return $this->belongsTo(QuotationRequest::class, 'quotation_request_id', 'qr_id');
    }

    public function vendor()
    {
        return $this->belongsTo(Sp_vendor::class, 'vendor_id', 'vendor_id');
    }

    public function quotationRequest()
    {
        return $this->belongsTo(QuotationRequest::class, 'quotation_request_id', 'qr_id');
    }

    public function recommendations()
    {
        return $this->hasMany(Recommendation::class, 'vq_id', 'vq_id');
    }

    public function purchaseOrder()
    {
        return $this->hasOne(PurchaseOrder::class, 'vendor_quotation_id', 'vq_id');
    }

}
