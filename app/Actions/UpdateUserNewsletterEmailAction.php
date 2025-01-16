<?php

namespace App\Actions;

use App\Models\User;
use App\Services\Mailcoach\MailcoachApi;

class UpdateUserNewsletterEmailAction
{
    public function __construct(private MailcoachApi $mailcoachApi)
    {
    }

    public function execute(User $user): User
    {
        $subscriber = $this->mailcoachApi->getSubscriber($user->getOriginal('email'));

        if (! $subscriber) {
            return $user;
        }

        $this->mailcoachApi->update($subscriber, ['email' => $user->email]);

        return $user;
    }
}
