<?php

namespace App\Models\StoreProcurement;

use Illuminate\Database\Eloquent\Model;

class StoreItemRequest extends Model
{
    protected $table = 'SP_STORE_ITEM_REQUESTS';
    protected $primaryKey = 'sir_id';
    public $timestamps = true;
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'store_item_id',
        'item_title',
        'unit',
        'requested_qty',
        'issued_qty',
        'request_dept_id',
        'request_dept_name',
        'requested_by',
        'request_remarks',
        'status',
        'approval_remarks',
        'issued_by',
        'issued_at',
        'acknowledged_by',
        'acknowledged_at',
        'acknowledge_remarks',
        'entry_username',
        'entry_dept_id',
    ];

    public function storeBudgetItem()
    {
        return $this->belongsTo(StoreBudgetItem::class, 'store_item_id', 'sbi_id');
    }
}
