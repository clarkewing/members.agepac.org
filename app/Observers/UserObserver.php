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
        // app(SubscribeUserToNewsletterAction::class)->execute($user);

        LegacySync::syncRecord($user->getTable(), $user->getKey(), SyncDirection::LegacyToNew);
    }

    public function updated(User $user)
    {
        // Propagate updates to the new app. This app owns columns like password
        // and anything edited via Nova or the user's settings; without this,
        // those changes never reach the new app. Cashier-managed columns
        // (stripe_id, pm_type, pm_last_four, trial_ends_at) are excluded from
        // this direction in legacy_sync.php because the new app owns them via
        // the Stripe webhooks only it receives.
        LegacySync::syncRecord($user->getTable(), $user->getKey(), SyncDirection::LegacyToNew);

        // if ($user->wasChanged('email')) {
        //     app(UpdateUserNewsletterEmailAction::class)->execute($user);
        // }
    }

    public function deleted(User $user)
    {
        // app(UnsubscribeUserFromNewsletterAction::class)->execute($user);
    }
}
