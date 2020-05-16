<?php

namespace App\Listeners;

use App\Events\ReplyPosted;

class NotifySubscribers
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\ReplyPosted  $event
     * @return void
     */
    public function handle(ReplyPosted $event): void
    {
        $event->post->thread->subscriptions
            ->where('user_id', '!=', $event->post->user_id)
            ->each->notify($event->post);
    }
}
