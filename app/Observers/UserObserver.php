<?php

namespace App\Observers;

use App\Actions\SubscribeUserToNewsletterAction;
use App\Actions\UnsubscribeUserFromNewsletterAction;
use App\Actions\UpdateUserNewsletterEmailAction;
use App\Models\User;

class UserObserver
{
    public function created(User $user)
    {
        app(SubscribeUserToNewsletterAction::class)->execute($user);
    }

    public function updated(User $user)
    {
        if ($user->wasChanged('email')) {
            app(UpdateUserNewsletterEmailAction::class)->execute($user);
        }
    }

    public function deleted(User $user)
    {
        app(UnsubscribeUserFromNewsletterAction::class)->execute($user);
    }
}
