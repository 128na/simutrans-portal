<?php

declare(strict_types=1);

namespace Tests\Unit\Notifications;

use App\Models\User;
use App\Notifications\UserInvited;
use Illuminate\Notifications\Messages\MailMessage;
use Tests\Unit\TestCase;

class UserInvitedTest extends TestCase
{
    public function test_mail_message_contains_expected_view_and_data(): void
    {
        $invited = new User(['name' => 'invited', 'email' => 'invitee@example.com']);
        $notifiable = new User(['name' => 'owner', 'email' => 'owner@example.com']);

        $notification = new UserInvited($invited);

        $this->assertSame(['mail'], $notification->via($notifiable));

        $mail = $notification->toMail($notifiable);

        $this->assertInstanceOf(MailMessage::class, $mail);
        $this->assertSame('ユーザー招待通知', $mail->subject);
        $this->assertSame('emails.invited', $mail->view);
        $this->assertSame([
            'user' => $notifiable,
            'invited' => $invited,
        ], $mail->viewData);
    }
}
