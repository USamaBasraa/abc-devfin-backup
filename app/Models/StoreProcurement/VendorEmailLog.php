<?php

namespace App\Models\StoreProcurement;

use Illuminate\Database\Eloquent\Model;

class VendorEmailLog extends Model
{
    protected $table = 'sp_vendor_email_log';
    protected $primaryKey = 've_id';
    public $timestamps = false;

    protected $fillable = [
        've_id',
        've_qr_id',
        've_qr_version',  // NEW: Track which request version email was sent for
        've_vendor_id',
        've_sent_by',
        've_sent_at',
        've_remarks',
        've_email_subject',
        've_status',
        've_rfq_no',
    ];

    // Relationship: Email log belongs to Quotation Request
    public function quotationRequest()
    {
        return $this->belongsTo(QuotationRequest::class, 've_qr_id', 'qr_id');
    }

    // Relationship: Email log belongs to Vendor
    public function vendor()
    {
        return $this->belongsTo(Sp_vendor::class, 've_vendor_id', 'vendor_id');
    }
}

