<?php

namespace App\Notifications;

use App\Models\Post;
use App\Models\Thread;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class YouWereMentioned extends Notification
{
    use Queueable;

    /**
     * @var string
     */
    protected $subjectTitle;

    /**
     * @var \App\Models\User
     */
    protected $subjectOwner;

    /**
     * @var string
     */
    protected string $subjectPath;

    /**
     * Create a new notification instance.
     *
     * @param  \App\Models\Post|\App\Models\Thread  $subject
     * @return void
     * @throws \Exception
     */
    public function __construct($subject)
    {
        if ($subject instanceof Post) {
            $this->subjectTitle = $subject->thread->title;
            $this->subjectOwner = $subject->owner;
        } elseif ($subject instanceof Thread) {
            $this->subjectTitle = $subject->title;
            $this->subjectOwner = $subject->creator;
        } else {
            throw new \Exception('Unhandled model passed.');
        }

        $this->subjectPath = $subject->path();
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable): array
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
            ->subject('Tu as été mentionné' . $notifiable->gender === 'F' ? 'e' : '')
            ->greeting('Bonjour ' . $notifiable->first_name . ' !')
            ->line($this->message())
            ->action('Voir la discussion', $this->subjectPath);
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
            'notifier' => $this->subjectOwner,
            'link' => $this->subjectPath,
        ];
    }

    /**
     * Get a message string for the notification.
     */
    public function message()
    {
        return sprintf('%s t\'a mentionné dans "%s"', $this->subjectOwner->name, $this->subjectTitle);
    }
}
