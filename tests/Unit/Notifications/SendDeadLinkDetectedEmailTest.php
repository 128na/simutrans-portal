<?php

declare(strict_types=1);

namespace Tests\Unit\Notifications;

use App\Models\Article;
use App\Models\User;
use App\Notifications\SendDeadLinkDetectedEmail;
use Tests\Unit\TestCase;

class SendDeadLinkDetectedEmailTest extends TestCase
{
    public function test_notification_uses_mail_channel(): void
    {
        // Arrange
        $article = (new Article)->forceFill(['id' => 1]);
        $notification = new SendDeadLinkDetectedEmail;

        // Act
        $channels = $notification->via($article);

        // Assert
        $this->assertEquals(['mail'], $channels);
    }

    public function test_to_mail_returns_mail_message(): void
    {
        // Arrange
        $user = (new User)->forceFill(['id' => 1, 'name' => 'Test User']);
        $article = (new Article)->forceFill([
            'id' => 1,
            'title' => 'Test Article Title',
        ]);
        $article->setRelation('user', $user);
        $notification = new SendDeadLinkDetectedEmail;

        // Act
        $mailMessage = $notification->toMail($article);

        // Assert
        $this->assertInstanceOf(\Illuminate\Notifications\Messages\MailMessage::class, $mailMessage);
        $this->assertEquals('「Test Article Title」のダウンロード先URLがリンク切れになっています', $mailMessage->subject);
    }

    public function test_to_mail_uses_correct_view(): void
    {
        // Arrange
        $user = (new User)->forceFill(['id' => 1, 'name' => 'Test User']);
        $article = (new Article)->forceFill([
            'id' => 1,
            'title' => 'Test Article',
        ]);
        $article->setRelation('user', $user);
        $notification = new SendDeadLinkDetectedEmail;

        // Act
        $mailMessage = $notification->toMail($article);

        // Assert
        $reflection = new \ReflectionClass($mailMessage);
        $viewProperty = $reflection->getProperty('view');
        $viewProperty->setAccessible(true);
        $this->assertEquals('emails.deadlink-detected', $viewProperty->getValue($mailMessage));
    }

    public function test_to_array_returns_empty_array(): void
    {
        // Arrange
        $article = (new Article)->forceFill(['id' => 1]);
        $notification = new SendDeadLinkDetectedEmail;

        // Act
        $array = $notification->toArray($article);

        // Assert
        $this->assertIsArray($array);
        $this->assertEmpty($array);
    }

    public function test_notification_implements_should_queue(): void
    {
        // Arrange
        $notification = new SendDeadLinkDetectedEmail;

        // Assert
        $this->assertInstanceOf(\Illuminate\Contracts\Queue\ShouldQueue::class, $notification);
    }

    public function test_subject_includes_article_title(): void
    {
        // Arrange
        $user = (new User)->forceFill(['id' => 1, 'name' => 'Test User']);
        $article = (new Article)->forceFill([
            'id' => 1,
            'title' => '特殊文字&<>を含むタイトル',
        ]);
        $article->setRelation('user', $user);
        $notification = new SendDeadLinkDetectedEmail;

        // Act
        $mailMessage = $notification->toMail($article);

        // Assert
        $this->assertStringContainsString('特殊文字&<>を含むタイトル', $mailMessage->subject);
    }
}
