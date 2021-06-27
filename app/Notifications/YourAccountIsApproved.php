<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class YourAccountIsApproved extends Notification
{
    use Queueable;

    /**
     * Get the notification's delivery channels.
     */
    public function via(User $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(User $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Ton compte AGEPAC est approuvé !')
            ->greeting('Bonjour ' . $notifiable->first_name . ' !')
            ->line('Le Bureau a procédé à la vérification de ton éligibilité et ton compte est approuvé.')
            ->action('Découvrir l’AGEPAC', route('login'));
    }
}
