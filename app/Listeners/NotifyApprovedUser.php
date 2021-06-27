<?php

namespace App\Listeners;

use App\Events\UserApproved;
use App\Notifications\YourAccountIsApproved;

class NotifyApprovedUser
{
    /**
     * Handle the event.
     */
    public function handle(UserApproved $event): void
    {
        $event->user->notify(new YourAccountIsApproved);
    }
}
