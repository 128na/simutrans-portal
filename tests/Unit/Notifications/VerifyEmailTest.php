<?php

declare(strict_types=1);

namespace Tests\Unit\Notifications;

use App\Models\User;
use App\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Tests\Unit\TestCase;

class VerifyEmailTest extends TestCase
{
    public function test_mail_message_uses_custom_verification_url(): void
    {
        $notifiable = new User();
        $notifiable->id = 1;
        $notifiable->email = 'user@example.com';
        $notification = new class extends VerifyEmail {
            protected function verificationUrl($notifiable)
            {
                return 'https://example.com/verify';
            }
        };

        $this->assertSame(['mail'], $notification->via($notifiable));

        $mail = $notification->toMail($notifiable);

        $this->assertInstanceOf(MailMessage::class, $mail);
        $this->assertSame('登録メールアドレスの確認', $mail->subject);
        $this->assertSame('emails.verify', $mail->view);
        $this->assertSame('メールアドレスを確認する', $mail->actionText);
        $this->assertSame('https://example.com/verify', $mail->actionUrl);
        $this->assertSame([
            'user' => $notifiable,
        ], $mail->viewData);
    }
}
