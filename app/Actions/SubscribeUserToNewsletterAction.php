<?php

namespace App\Actions;

use App\Models\User;
use App\Services\Mailcoach\MailcoachApi;

class SubscribeUserToNewsletterAction
{
    public function __construct(private MailcoachApi $mailcoachApi)
    {
    }

    public function execute(User $user): User
    {
        $subscriber = $this->mailcoachApi->getSubscriber($user->email);

        if (! $subscriber) {
            $subscriber = $this->mailcoachApi->subscribe(
                strtolower($user->email),
                $user->first_name,
                $user->last_name,
                [
                    'class_course' => $user->class_course,
                    'class_year' => $user->class_year,
                ],
                skipConfirmation: true,
            );
        }

        if ($subscriber) {
            $this->mailcoachApi->addTags($subscriber, ['newsletter']);
        }

        return $user;
    }
}
