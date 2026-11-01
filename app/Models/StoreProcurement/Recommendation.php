<?php

namespace App\Models\StoreProcurement;

use Illuminate\Database\Eloquent\Model;

class Recommendation extends Model
{
    protected $table = 'SP_RECOMMENDATIONS';  // new table you should create
    protected $primaryKey = 'rec_id';
    public $timestamps = true;

    protected $fillable = [
        'qr_id',
        'vq_id',
        'department_id',
        'recommended_flag',
        'remarks',
        'entry_user_id',
        'entry_username'
    ];

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'dept_id');
    }

    // link back to QuotationRequest
    public function quotationRequest()
    {
        return $this->belongsTo(QuotationRequest::class, 'qr_id', 'qr_id');
    }

    // through QuotationRequest → get VendorQuotations
    public function vendorQuotations()
    {
        return $this->hasMany(VendorQuotation::class, 'quotation_request_id', 'qr_id');
    }


    public function vendorQuotation()
    {
        return $this->belongsTo(VendorQuotation::class, 'vq_id', 'vq_id');
    }



    // public function department()
    //  {
    //     return $this->belongsTo(Department::class, 'dept_id', 'dept_id');
    //  }


}
