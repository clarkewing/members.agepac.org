<?php

namespace App\Observers;

use App\Models\User;
use Laravel\Cashier\Subscription;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;

class SubscriptionObserver
{
    protected string $emailListName = 'Members';

    /**
     * Handle the subscription "saved" event.
     *
     * @param  Subscription  $subscription
     */
    public function saved(Subscription $subscription)
    {
        if ($subscription->valid() || $this->hasActiveSubscription($subscription->user)) {
            $this->addToEmailList($subscription->user);

            return;
        }

        $this->removeFromEmailList($subscription->user);
    }

    /**
     * @return \Spatie\Mailcoach\Domain\Audience\Models\EmailList
     */
    protected function emailList(): EmailList
    {
        return EmailList::where('name', $this->emailListName)->firstOrFail();
    }

    /**
     * Handle the subscription "deleted" event.
     *
     * @param  Subscription  $subscription
     */
    public function deleted(Subscription $subscription)
    {
        if ($this->hasActiveSubscription($subscription->user)) {
            return;
        }

        $this->removeFromEmailList($subscription->user);
    }

    /**
     * @param  \App\Models\User  $user
     */
    protected function addToEmailList(User $user): void
    {
        Subscriber::createWithEmail($user->email, $user->only([
            'first_name',
            'last_name',
            'class_course',
            'class_year',
            'gender',
            'birthdate',
            'phone',
        ]))
            ->subscribeTo($this->emailList());
    }

    /**
     * @param  \App\Models\User  $user
     */
    protected function removeFromEmailList(User $user): void
    {
        Subscriber::findForEmail($user->email, $this->emailList())
            ->delete();
    }

    protected function hasActiveSubscription(User $user)
    {
        return Subscription
            ::where('user_id', $user->id)
            ->active()
            ->exists();
    }
}
