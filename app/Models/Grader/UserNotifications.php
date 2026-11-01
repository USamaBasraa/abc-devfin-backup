<?php

namespace App\Models\Grader;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use DB;
use App\sessions;

class UserNotifications extends Model{
    protected $primaryKey = 'un_id';
    
    public function fetch_notification($UN_TO_USER_ID=0){

        $sess = new sessions();
        $sess->startSession();
      return   DB::table('user_notifications')
      ->where('un_to_user_id', $sess->emp_user_id )
   
      ->orderBy('un_id','DESC')
      ->get()->each(function ($item, $key) {
     // print_r($item); exit;
                 return   $item->displayDate =  Carbon::create($item->un_entry_datetime)->format('m:h a,  M d , Y');
              });
    }
}

/*

    [un_id] => 100694
    [un_to_user_id] => 42
    [un_description] => S2020-102 Upload Assginment by student
    [un_entry_datetime] => 2020-03-28 18:57:01
    [un_page_id] => 
    [un_type] => 
    [un_entry_user_id] => 
    [un_status] => 
    [un_seen_status] => 
    [un_page_name] => 
    [un_rmp_sess_id] => 
    [un_school_id] => 
    [un_dept_id] => 
    [un_prog_id] => 
    [un_shift_id] => 
    [un_group_id] => 
    [un_message_id] => 
    [created_at] => 2020-03-28 18:57:01
    [updated_at] => 2020-03-28 18:57:01
    [un_co_id] => 13775
    [is_email] => 0
    [target_page] => 
    [viewed_notification] => 

*/
