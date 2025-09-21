<?php

namespace App\Observers;

use App\Actions\SubscribeUserToNewsletterAction;
use App\Actions\UnsubscribeUserFromNewsletterAction;
use App\Actions\UpdateUserNewsletterEmailAction;
use App\Models\User;
use ClarkeWing\LegacySync\Enums\SyncDirection;
use ClarkeWing\LegacySync\Facades\LegacySync;

class UserObserver
{
    public function created(User $user)
    {
        app(SubscribeUserToNewsletterAction::class)->execute($user);

        LegacySync::syncRecord($user->getTable(), $user->getKey(), SyncDirection::LegacyToNew);
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
