<?php

namespace App\Models\Grader;

use Illuminate\Database\Eloquent\Model;
use DB;
use App\sessions;


class Chat extends Model{


    protected $primaryKey   = 'id';
    protected $table        = 'messages_bnu';
    protected $fillable     = [
        'message', 'entry_user_id', 'time','co_id','seen_record','sender_name','to_user_id','private_comment'
    ];



    public function setMessageAttribute($value){
        $this->attributes['message'] = strip_tags(htmlspecialchars_decode($value)); //strtolower($value);
    }
    

}
