<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use App\Event\Attendance;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Listener\SendEmail;
use App\Listener\SendSms;
use App\Listener\Notification;
use App\Listener\Withdraw;




class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */

    

    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
   
        'App\Event\Attendance' => [
            'App\Listener\SendEmail',
            //'App\Listener\SendSms',
            'App\Listener\Notification',
            'App\Listener\Withdraw',
            'App\Listener\Attendance_update',
        ],
    ];




    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
