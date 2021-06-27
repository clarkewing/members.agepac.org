<?php

namespace App\Listeners;

use App\Models\User;
use App\Notifications\UserPendingApproval;
use Illuminate\Auth\Events\Registered;

class NotifyAdminsOfNewUser
{
    /**
     * Handle the event.
     */
    public function handle(Registered $event): void
    {
        if ($event->user->isApproved()) {
            return;
        }

        User::permission('users.approve')->get()
            ->each->notify(new UserPendingApproval($event->user));
    }
}
