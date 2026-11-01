<?php

namespace App\Models\StoreProcurement;

use Illuminate\Database\Eloquent\Model;

class StoreBudgetItem extends Model
{
    protected $table = 'SP_STORE_BUDGET_ITEMS';
    protected $primaryKey = 'sbi_id';
    public $timestamps = true;

    protected $fillable = [
        'sbh_id', 'pinnacle_item_id', 'item_title', 'item_type', 'unit', 'app_unit',
        'fin_acc01_id', 'fin_acc01_title', 'quantity', 'rate', 'budget_type',
        'purchase_mode', 'remarks', 'ay_id', 'entry_dept_id', 'entry_username'
    ];

    protected $appends = ['item_id', 'brand', 'model'];

    public function head()
    {
        return $this->belongsTo(StoreBudgetHead::class, 'sbh_id', 'sbh_id');
    }

    public function quotationRequests()
    {
        return $this->hasMany(QuotationRequest::class, 'store_item_id', 'sbi_id');
    }

    public function getItemIdAttribute()
    {
        return $this->sbi_id;
    }

    public function getBrandAttribute()
    {
        return null;
    }

    public function getModelAttribute()
    {
        return null;
    }
}
