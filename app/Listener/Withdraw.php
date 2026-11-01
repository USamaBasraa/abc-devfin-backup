<?php

namespace App\Listener;

use App\Event\Attendance;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use DB;
use App\Models\Grader\SendSms;

class Withdraw implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }
    // public $delay = 0;
    /**
     * Handle the event.
     *
     * @param  Attendance  $event
     * @return void
     */
    public function handle(Attendance $event)
    {
       $coID='';

       if(isset( $event->withdrawList[0]->sc_cms_course_co_id) &&  $event->withdrawList[0]->sc_cms_course_co_id !=''){
          $coID=$event->withdrawList[0]->sc_cms_course_co_id;
       }else{
         $coID=$event->CoID;
      }
     
      
           
        $procedurenotification = 'course_withdraw';
        $bindings_notification = [
              'co_id'  =>  $coID    

        ];
     $result_notification = DB::executeProcedure($procedurenotification, $bindings_notification);
     $sms = new SendSms();
     $mess='Course Withdraw issue on  course Please check & verify.';

        // if(!$result_notification){
//          if(!$sms->teacher_marks_change_sendmail('cmslog@bnu.edu.pk' ,'Attendance Update Issue',$mess)){
//             $sms->sms($mess,'923104666023');
//
//          }
//
//         }else{
//           $sms->teacher_marks_change_sendmail('cmslog@bnu.edu.pk' ,'Attendance Update Issue',$mess);
//         }
     
    }

    public $delay = 60;
}
