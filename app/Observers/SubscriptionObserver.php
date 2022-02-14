<?php

namespace App\Observers;

use App\Models\User;
use App\Traits\ManagesEmailList;
use Laravel\Cashier\Subscription as StripeSubscription;

class SubscriptionObserver
{
    use ManagesEmailList;

    protected string $emailListName = 'Members';

    /**
     * Handle the subscription "saved" event.
     *
     * @param  StripeSubscription  $subscription
     */
    public function saved(StripeSubscription $subscription)
    {
        if ($subscription->valid() || $this->hasActiveSubscription($subscription->user)) {
            $this->addToEmailList($subscription->user);

            return;
        }

        $this->removeFromEmailList($subscription->user);
    }

    /**
     * Handle the subscription "deleted" event.
     *
     * @param  StripeSubscription  $subscription
     */
    public function deleted(StripeSubscription $subscription)
    {
        if ($this->hasActiveSubscription($subscription->user)) {
            return;
        }

        $this->removeFromEmailList($subscription->user);
    }

    protected function hasActiveSubscription(User $user)
    {
        return StripeSubscription
            ::where('user_id', $user->id)
            ->active()
            ->exists();
    }
}
