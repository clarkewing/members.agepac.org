<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class VerificationToken extends Notification
{
    /**
     * @var string
     */
    protected $token;

    /**
     * Create a new notification instance.
     *
     * @param  string  $token
     * @return void
     */
    public function __construct(string $token)
    {
        $this->token = $token;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
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
            ->subject('Ton code de vérification')
            ->greeting('Bonjour ' . $notifiable->first_name . ' !')
            ->line('Voici ton code de vérification :')
            ->line(new HtmlString('<strong>' . $this->token . '</strong>'))
            ->salutation(new HtmlString('Des questions ? Un souci ? Envoie-nous un mail à <a href="mailto:bonjour@agepac.org">bonjour@agepac.org</a>'));
    }
}
