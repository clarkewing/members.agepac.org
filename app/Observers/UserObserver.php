<?php

namespace App\Observers;

use App\Actions\SubscribeUserToNewsletterAction;
use App\Actions\UnsubscribeUserFromNewsletterAction;
use App\Actions\UpdateUserNewsletterEmailAction;
use App\Models\User;
use App\Traits\ManagesEmailList;

class UserObserver
{
    use ManagesEmailList;

    protected string $emailListName = 'All Users';

    public function created(User $user)
    {
        // Self-hosted Mailcoach
        $this->addToEmailList($user);

        // Mailcoach Cloud
        app(SubscribeUserToNewsletterAction::class)->execute($user);
    }

    public function updated(User $user)
    {
        if ($user->wasChanged('email')) {
            // Self-hosted Mailcoach
            $this->updateEmailListEmail($user->getOriginal('email'), $user->email);

            // Mailcoach Cloud
            app(UpdateUserNewsletterEmailAction::class)->execute($user);
        }
    }

    public function deleted(User $user)
    {
        // Self-hosted Mailcoach
        $this->removeFromEmailList($user);

        // Mailcoach Cloud
        app(UnsubscribeUserFromNewsletterAction::class)->execute($user);
    }
}
