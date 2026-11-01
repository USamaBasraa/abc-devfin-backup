<?php

namespace App\Models\StoreProcurement;

use Illuminate\Database\Eloquent\Model;

class SpPoApproval extends Model
{


    protected $table = 'sp_po_approvals';
    protected $primaryKey = 'id';
    public $timestamps = false; // since we’re using custom timestamp columns

    protected $fillable = [
        'po_id',
        'approver_id',
        'qr_id',
        'vendor_id',
        'slab_order',
        'role',
        'remarks',
        'status',
        'entry_user_id',
        'entry_datetime',
        'updated_user_id',
        'updated_datetime',
    ];

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class, 'po_id', 'po_id');
    }

    public function approver()
    {
        return $this->belongsTo(\App\Models\User::class, 'approver_id', 'id');
    }
}
