<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail as BaseVerifyEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

final class VerifyEmail extends BaseVerifyEmail implements ShouldQueue
{
    use Queueable;

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array<string>
     */
    #[\Override]
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return MailMessage
     */
    #[\Override]
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('登録メールアドレスの確認')
            ->view('emails.verify', ['user' => $notifiable])
            ->action('メールアドレスを確認する', $this->verificationUrl($notifiable));
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

    /**
     * ユニットテストでの認証URL取得用.
     */
    public function getVerificationUrl(User $user): string
    {
        return $this->verificationUrl($user);
    }
}
