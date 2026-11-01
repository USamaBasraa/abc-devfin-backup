<?php

namespace App\Listener;

use App\Event\Attendance;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Auth;
use App\User;
use DB;
use App\Models\Grader\SendSms;

class Notification implements ShouldQueue
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
 
    /**
     * Handle the event.
     *
     * @param  Attendance  $event
     * @return void
     */
    public function handle(Attendance $event)
    {
        $userID=$event->userID;
        foreach($event->withdrawList as $rec){
            $absents = $rec->absents;
            $allowed_absents = $rec->allowed_absents;
            $warning = $allowed_absents-$absents;
            $desc 	= '';
            $courseName=$rec->course_name;
            $offeringSource=$rec->offering_source ?? null;
            $grade = ($offeringSource == 'Student Roadmapwise Enrollment Bulk') ? 'F' : 'W';

            if($warning == 1){                
                $desc = 'Warning: You have nearly exhausted your allowed quota of leaves for the course '.$courseName.'. You are now allowed to take only one more leave in this course.';
            }
            // if($absents == $allowed_absents){               
            //     $desc = 'Warning: In case of one more leave in the course of '.$courseName.', you will get a W grade in it.';
            // }
            // if($absents > $allowed_absents){                
            //     $desc = 'Owing to your short attendance in the course of '.$courseName.' you have been awarded a W grade in it.';
            // }
            if($absents == $allowed_absents){               
                $desc = 'Warning: In case of one more leave in the course of '.$courseName.', you will get a '.$grade.' grade in it.';
            }
            if($absents > $allowed_absents){                
                $desc = 'Owing to your short attendance in the course of '.$courseName.' you have been awarded a '.$grade.' grade in it.';
            }
          
            $sms = new SendSms();
          
        if($desc){
		  
		   $ins = "INSERT INTO user_notifications (
                un_to_user_id,
                un_description,
                un_entry_user_id,
				target_page,
                created_at,
                updated_at
            ) VALUES ( 
                '$rec->sc_sai_enrollment_no',
                '$desc',
                '$userID',
				'notification',
                CURRENT_TIMESTAMP,
                CURRENT_TIMESTAMP
            )" ;
            $rec = DB::insert($ins);

		   if(!$rec){
                $sms->send_sms('issue in this students'.$enrollment,'923347280669');
            }
            
		  }
           // $Send->send_sms('this is message for','923347280669');
        }


    }
    public $delay = 10;
}
