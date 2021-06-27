<?php

namespace App\Notifications;

use App\Models\User;
use App\Nova\Filters\UserMembershipState;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserPendingApproval extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        protected User $user
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
            ->line($this->user->name . ' (' . $this->user->class . ') has just registered for an AGEPAC account.')
            ->line('Their account must be approved in order for them to gain full access.')
            ->action('View Pending Approvals', $this->usersPendingApprovalsUrl());
    }

    protected function usersPendingApprovalsUrl(): string
    {
        $filtersString = base64_encode(json_encode(
            [
                ['class' => UserMembershipState::class, 'value' => 'pending-approval'],
            ]
        ));

        return url("/nova/resources/users?users_filter=$filtersString");
    }
}
