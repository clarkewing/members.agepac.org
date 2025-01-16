<?php

namespace App\Actions;

use App\Models\User;
use App\Services\Mailcoach\MailcoachApi;

class UnsubscribeUserFromMembersNewsletterAction
{
    public function __construct(private MailcoachApi $mailcoachApi)
    {
    }

    public function execute(User $user): User
    {
        $subscriber = $this->mailcoachApi->getSubscriber($user->email);

        if (! $subscriber) {
            return $user;
        }

        if (count($subscriber->tags) === 1 && $subscriber->tags[0] === 'members_newsletter') {
            $this->mailcoachApi->unsubscribe($subscriber);
        } else {
            $this->mailcoachApi->removeTag($subscriber, 'members_newsletter');
        }

        return $user;
    }
}
