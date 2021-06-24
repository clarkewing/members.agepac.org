<?php

namespace App\Listeners;

use App\Models\User;
use App\Notifications\NewUnverifiedUser;
use Illuminate\Auth\Events\Registered;

class NotifyAdminsOfNewUser
{
    /**
     * Handle the event.
     */
    public function handle(Registered $event): void
    {
        if ($event->user->isVerified()) {
            return;
        }

        User::permission('users.verify')->get()
            ->each->notify(new NewUnverifiedUser($event->user));
    }
}
