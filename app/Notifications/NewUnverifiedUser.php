<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewUnverifiedUser extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        User $user
    ) {
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(mixed $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(mixed $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New User Awaiting Approval')
            ->greeting('Hey ' . $notifiable->first_name . ' !')
            ->line($this->user->name . '(' . $this->user->class . ') has just registered for an AGEPAC account.')
            ->line('Their account must be approved in order for them to gain full access.')
            ->action('View Pending Approvals', ); // TODO: Add URL
    }
}
