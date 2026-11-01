<?php

namespace App\Models\StoreProcurement;




use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpApprovalMaster extends Model
{
    use HasFactory;

    protected $table = 'sp_approval_masters'; // ✅ Your actual DB table name
    protected $primaryKey = 'id'; // adjust if your PK differs

    protected $fillable = [
        'type',
        'school_id',
        'dept_id',
        'min_price',
        'max_price',
        'approval_user_id',
        'active_status',
        'allowed_username',
        'role',
    ];
}
