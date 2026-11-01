<?php

namespace App\Listener;

use App\Event\Attendance;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\Grader\SendSms as SMS;
use DB;


class SendSms implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct ()
    {
        //$this->sendsms = $sendsms;
    }
   // public $delay = 6;
    /**
     * Handle the event.
     *
     * @param  Attendance  $event
     * @return void
     */
    public function handle(Attendance $event)
    {
        $Send=new SMS();

       
         foreach($event->withdrawList as $rec){
            $ins = "INSERT INTO aaa (
                customer_id,
                customer_name,
                city
            ) VALUES (
                '1',
                'sms',
                '$rec->sc_sai_enrollment_no'
            )" ;
          //  $rec = DB::insert($ins);
           // $Send->send_sms('this is message for','923347280669');
        }

        
        
    }

    public $delay = 10;
}
