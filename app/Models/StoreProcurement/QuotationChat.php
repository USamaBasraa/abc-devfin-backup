<?php

namespace App\Models\StoreProcurement;

use Illuminate\Database\Eloquent\Model;

class QuotationChat extends Model
{
    protected $table = 'sp_quot_request_chat';
    protected $primaryKey = 'chat_id';
    public $timestamps = false;

    protected $fillable = [
        'qr_id',
        'sender_type',
        'entry_username',
        'message_text',
        'is_read',
        'read_at',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'read_at' => 'datetime',
        'is_read' => 'integer',
    ];

    public function quotationRequest()
    {
        return $this->belongsTo(QuotationRequest::class, 'qr_id', 'qr_id');
    }
}

