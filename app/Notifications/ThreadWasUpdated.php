<?php

namespace App\Notifications;

use App\Models\Post;
use App\Models\Thread;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ThreadWasUpdated extends Notification
{
    use Queueable;

    /**
     * @var \App\Models\Thread
     */
    protected Thread $thread;

    /**
     * @var \App\Models\Post
     */
    protected Post $post;

    /**
     * Create a new notification instance.
     *
     * @param  \App\Models\Thread  $thread
     * @param  \App\Models\Post  $post
     */
    public function __construct(Thread $thread, Post $post)
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
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Réponse à une discussion')
            ->greeting('Bonjour ' . $notifiable->first_name . ' !')
            ->line($this->message())
            ->action('Voir la discussion', $this->post->path())
            ->line('Tu reçois cette notification car tu es abonné' . $notifiable->gender === 'F' ? 'e' : '' . ' à la discussion.');
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
