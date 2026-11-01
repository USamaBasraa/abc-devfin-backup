<?php

namespace App\Listener;

use App\Event\Attendance;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use DB;
use App\Models\Grader\SendSms;

class SendEmail implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
 
    // public $delay = 0;
    //public $withdraw;

    public function __construct()
    {
      // $this->withdraw=$withdrawList;
    }
   public $delay = 10;
    /**
     * Handle the event.
     *
     * @param  Attendance  $event
     * @return void
     */
    public function handle(Attendance $event)
    {
  
        $count=0;
        $sms = new SendSms();
        foreach($event->withdrawList as $rec){
            $absents = $rec->absents;
            $allowed_absents = $rec->allowed_absents;
            $warning = $allowed_absents-$absents;
			$enrollment= strtolower($rec->sc_sai_enrollment_no);
			$desc = '';

           $courseName=$rec->course_name;
           
            if($warning == 1){
                $desc = 'Warning! In case of one more absent your course '.$courseName.' reach at end limit of absent';
            }
			if($absents == $allowed_absents){
                $desc = 'Red Zone! In case of one more absent your course '.$courseName.' will automatically withdraw';
            }
			if($absents > $allowed_absents){
                $desc = 'Your Course '.$courseName.' has been withdraw on short Attdendance';
            }
			
            $procedureWithdraw = 'send_mail_one_by_one';
            $bindings_notification = [
                  'to'  => $enrollment.'@bnu.edu.pk',
                  'subject'  => 'withdraw details',
                  'message'  => "'.$desc.'",//$event->email['co_id']             
    
            ];
            $result_notification = DB::executeProcedure($procedureWithdraw, $bindings_notification); 
            
            if(!$result_notification){
                $count++;
            }
          
        }

        if($count>0){
            $sms->sms('Send Email issue on attendance withdrawal','923104666023');
        }
      

    }
    
}
