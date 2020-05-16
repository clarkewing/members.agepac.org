<?php

namespace App\Providers;

use App\Events\ReplyPosted;
use App\Events\ThreadPublished;
use App\Listeners\NotifyMentionedUsers;
use App\Listeners\NotifySubscribers;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

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
        ThreadPublished::class => [
            NotifyMentionedUsers::class,
        ],
        ReplyPosted::class => [
            NotifyMentionedUsers::class,
            NotifySubscribers::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
