<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ThreadWasUpdated extends Notification
{
    use Queueable;

    /**
     * @var \App\Thread
     */
    protected $thread;

    /**
     * @var \App\Post
     */
    protected $post;

    /**
     * Create a new notification instance.
     *
     * @param  \App\Thread $thread
     * @param  \App\Post $post
     * @return void
     */
    public function __construct($thread, $post)
    {
        $this->thread = $thread;
        $this->post = $post;
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
            'message' => $this->message(),
            'notifier' => $this->post->owner,
            'link' => $this->post->path(),
        ];
    }

    /**
     * Get a message string for the notification.
     */
    public function message()
    {
        return sprintf('%s a répondu à "%s"', $this->post->owner->name, $this->thread->title);
    }
}
