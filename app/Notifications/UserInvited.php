<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserInvited extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(private User $invited)
    {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<string>
     */
    public function via(mixed $notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @return MailMessage
     */
    public function toMail(mixed $notifiable)
    {
        return (new MailMessage())
            ->subject('ユーザー招待通知')
            ->view('emails.invited', ['user' => $notifiable, 'invited' => $this->invited]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<mixed>
     */
    public function toArray(mixed $notifiable)
    {
        return [];
    }
}
