<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class ThreadWasUpdated extends Notification
{
    use Queueable;

    /**
     * @var \App\Thread
     */
    protected $thread;

    /**
     * @var \App\Reply
     */
    protected $reply;

    /**
     * Create a new notification instance.
     *
     * @param  \App\Thread $thread
     * @param  \App\Reply $reply
     * @return void
     */
    public function __construct($thread, $reply)
    {
        $this->thread = $thread;
        $this->reply = $reply;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'message' => $this->reply->owner->name . ' a répondu à ' . $this->thread->title,
            'link' => $this->reply->path(),
        ];
    }
}
