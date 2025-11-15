<?php

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

final class ResetPassword extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(
        public readonly string $token,
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<string>
     */
    public function via(mixed $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail(mixed $notifiable): \Illuminate\Notifications\Messages\MailMessage
    {
        return new MailMessage()
            ->subject('パスワードリセットがリクエストされました')
            ->view('emails.reset', ['user' => $notifiable])
            ->action('パスワードリセット画面を開く', route('reset-password', $this->token));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<mixed>
     */
    public function toArray(mixed $notifiable): array
    {
        return [];
    }
}
