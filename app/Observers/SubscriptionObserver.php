<?php

namespace App\Observers;

use App\Actions\SubscribeUserToMembersNewsletterAction;
use App\Actions\UnsubscribeUserFromMembersNewsletterAction;
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
            // Self-hosted Mailcoach
            $this->addToEmailList($subscription->user);

            // Mailcoach Cloud
            app(SubscribeUserToMembersNewsletterAction::class)->execute($subscription->user);

            return;
        }

        // Self-hosted Mailcoach
        $this->removeFromEmailList($subscription->user);

        // Mailcoach Cloud
        app(UnsubscribeUserFromMembersNewsletterAction::class)->execute($subscription->user);
    }

    /**
     * Handle the subscription "deleted" event.
     *
     * @param  StripeSubscription  $subscription
     */
    public function deleted(StripeSubscription $subscription)
    {
        if ($this->hasActiveSubscription($subscription->user)) {
            // Mailcoach Cloud
            app(SubscribeUserToMembersNewsletterAction::class)->execute($subscription->user);

            return;
        }

        // Self-hosted Mailcoach
        $this->removeFromEmailList($subscription->user);

        // Mailcoach Cloud
        app(UnsubscribeUserFromMembersNewsletterAction::class)->execute($subscription->user);
    }

    protected function hasActiveSubscription(User $user)
    {
        return StripeSubscription
            ::where('user_id', $user->id)
            ->active()
            ->exists();
    }
}
