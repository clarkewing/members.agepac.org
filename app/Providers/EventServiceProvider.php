<?php

namespace App\Providers;

use App\Events\PostCreated;
use App\Events\PostUpdated;
use App\Events\ThreadPublished;
use App\Events\UserApproved;
use App\Listeners\NotifyAdminsOfNewUser;
use App\Listeners\NotifyApprovedUser;
use App\Listeners\NotifyMentionedUsers;
use App\Listeners\NotifySubscribers;
use App\Listeners\ReconcileAttachments;
use App\Models\User;
use App\Observers\SubscriptionObserver;
use App\Observers\UserObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use Laravel\Cashier\Subscription;

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
            NotifyAdminsOfNewUser::class,
        ],
        UserApproved::class => [
            NotifyApprovedUser::class,
        ],
        ThreadPublished::class => [
            NotifyMentionedUsers::class,
        ],
        PostCreated::class => [
            ReconcileAttachments::class,
            NotifyMentionedUsers::class,
            NotifySubscribers::class,
        ],
        PostUpdated::class => [
            ReconcileAttachments::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        Subscription::observe(SubscriptionObserver::class);
        User::observe(UserObserver::class);
    }
}
