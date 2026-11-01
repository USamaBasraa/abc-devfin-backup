<?php

namespace App\Models\StoreProcurement;

use Illuminate\Database\Eloquent\Model;

class QuotationRequest extends Model
{
    protected $table = 'sp_quotation_request';
    protected $primaryKey = 'qr_id';
    public $timestamps = true;
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
    'vendor_category_id',
    'item_name',
    'item_id',
    'bh_id',
    'model_no',
    'purchase_mode',
    'budget_type',
    'bt_id',
    'from_budget_id',
    'to_budget_id',
    'item_type',
    'remarks',
    'entry_user_id',
    'request_department_id',
    'request_school_id',
    'parent_department_id',
    'allow_decline_remarks',
    'department_head_approval',
    'visibility_to_vendors',
    'decline_remarks',
    'approval_status',
    'po_id',
    'model',
    'brand',
    'head_approval_remarks',
    'specifications_prnt_dept',
    'budget_quantity',
    'requested_quantity',
    'current_version',
    'is_revised',
    'cat_id',
    'request_type',
    'store_bh_id',
    'store_item_id'
];


   public function vendorCategory()
    {
        return $this->belongsTo(VendorCategory::class, 'vendor_category_id', 'category_id');
    }

    public function vendorQuotations()

    {

        // dd($this->hasMany(VendorQuotation::class, 'quotation_request_id', 'qr_id'));
        return $this->hasMany(VendorQuotation::class, 'quotation_request_id', 'qr_id');
    }

        // Relation with Budget Item
    public function budgetItem()
    {
        return $this->belongsTo(BudgetItem::class, 'item_id', 'item_id');
    }
    // Relation with Budget Head
    public function budgetHead()
    {

        return $this->belongsTo(BudgetHead::class, 'bh_id', 'bh_id');
    }

    // Requesting Department
    
    public function storeBudgetItem()
    {
        return $this->belongsTo(StoreBudgetItem::class, 'store_item_id', 'sbi_id');
    }

    public function storeBudgetHead()
    {
        return $this->belongsTo(StoreBudgetHead::class, 'store_bh_id', 'sbh_id');
    }
    public function requestDepartment()
    {
        return $this->belongsTo(\App\Models\Grader\Departments::class, 'request_department_id', 'dept_id');
    }

    // Parent Department
    public function parentDepartment()
    {
        return $this->belongsTo(\App\Models\Grader\Departments::class, 'parent_department_id', 'dept_id');
    }

    // QuotationRequest.php
        public function recommendations()
        {
            return $this->hasMany(Recommendation::class, 'qr_id', 'qr_id');
        }
        // public function quotationRequest()
        //     {
        //         return $this->belongsTo(QuotationRequest::class, 'qr_id', 'qr_id');
        //     }
        // In VendorQuotation.php
        public function quotationRequest()
        {
            return $this->belongsTo(QuotationRequest::class, 'vq_qr_id', 'qr_id');
        }
      public function purchaseOrder()
        {
            return $this->belongsTo(PurchaseOrder::class, 'PO_ID', 'PO_ID');
        }


        public function chatMessages()
        {
            return $this->hasMany(QuotationChat::class, 'qr_id', 'qr_id')->orderBy('created_at', 'asc');
        }
    


}
