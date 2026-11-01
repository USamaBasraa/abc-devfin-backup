<?php

namespace App\Models\StoreProcurement;

use Illuminate\Database\Eloquent\Model;
class BudgetItem extends Model {
    protected $table = 'SP_BUDGET_ITEMS';
    protected $primaryKey = 'item_id';
    public $timestamps = true;
    protected $fillable = ['pin_id','bh_id','item_title','model',
    'brand','quantity','rate','remarks','budget_type','purchase_mode','ay_id','entry_dept_id',
    'entry_username','bh_cat_id','budget_from','budget_to','BUDGET_FROM','BUDGET_TO',
    'send_reappropriation_approval_reapprop',
'SEND_REAPPROPRIATION_APPROVAL_REAPPROP',
'REQUESTED_BUDGET_TYPE','REASON_FOR_APPROVAL','REAPPROPRIATION_APPROVAL',
'REAPPROPRIATION_APPROVAL_REMARKS','VC_BUDGET_UPDATE_FLAG'];

    public function getPurchaseModeAttribute($value): string
    {
        return $value ?? 'purchase';
    }

    public function getBudgetYearAttribute(): ?string
    {
        $from = $this->budget_from ?? $this->BUDGET_FROM ?? null;
        $to   = $this->budget_to   ?? $this->BUDGET_TO   ?? null;

        $fromYear = null;
        $toYear   = null;

        if (!empty($from)) {
            try {
                $fromYear = \Carbon\Carbon::parse($from)->format('Y');
            } catch (\Throwable $e) {
                if (preg_match('/\b(20\d\d|19\d\d)\b/', (string) $from, $m)) {
                    $fromYear = $m[1];
                }
            }
        }

        if (!empty($to)) {
            try {
                $toYear = \Carbon\Carbon::parse($to)->format('Y');
            } catch (\Throwable $e) {
                if (preg_match('/\b(20\d\d|19\d\d)\b/', (string) $to, $m)) {
                    $toYear = $m[1];
                }
            }
        }

        if ($fromYear && $toYear) {
            return $fromYear === $toYear ? $fromYear : "{$fromYear}-{$toYear}";
        }

        if ($fromYear) {
            return $fromYear;
        }

        if ($toYear) {
            return $toYear;
        }

        return null;
    }

    public function budgetHead()
    {
        return $this->belongsTo(BudgetHead::class, 'bh_id', 'bh_id');
    }

    public function quotationRequests()
    {
        return $this->hasMany(QuotationRequest::class, 'item_id', 'item_id');
    }

    public function department()
    {
        return $this->belongsTo(\App\Models\Grader\Departments::class, 'entry_dept_id', 'dept_id');
    }

    public function vendorCategory()
    {
        return $this->belongsTo(VendorCategory::class, 'bh_cat_id', 'category_id');
    }

}

