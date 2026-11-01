<?php

namespace App\Listener;

use App\Event\Attendance;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use DB;
use App\Models\Grader\SendSms;
class Attendance_update //implements ShouldQueue
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
       
       $sms = new SendSms();
       $mess='Course Withdraw issue on  course ID  Please check & verify.';

          $final_submit = 'COURSE_ATTENDANCE_UPDATE';
                $parameters = [
                    'co_id'           =>$coID
                ];

              $resultAttendance = DB::executeProcedure($final_submit, $parameters);
            
              //if(!$resultAttendance){
//                if(!$sms->teacher_marks_change_sendmail('cmslog@bnu.edu.pk' ,'Attendance Update Issue',$mess)){
//                    $sms->sms($mess,'923104666023');
//                }
//
//              }else{
//                $sms->teacher_marks_change_sendmail('cmslog@bnu.edu.pk' ,'Attendance Update Issue',$mess);
//              }
     
      //   $this->release(60);
    }
    //public $tries = 2;
   // public $timeout = 120;
     public $delay = 30;
   // public $failOnTimeout = true;
}
