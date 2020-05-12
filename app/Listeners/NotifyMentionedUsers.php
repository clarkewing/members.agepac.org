<?php

namespace App\Listeners;

use App\Notifications\YouWereMentioned;
use Symfony\Contracts\EventDispatcher\Event;

class NotifyMentionedUsers
{
    /**
     * Handle the event.
     *
     * @param  \Symfony\Contracts\EventDispatcher\Event  $event
     * @return void
     */
    public function handle(Event $event): void
    {
        $subject = $event->post ?? $event->thread;

        $subject->mentionedUsers()
            ->each->notify(new YouWereMentioned($subject));
    }
}
