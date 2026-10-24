<?php

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class ArticleClosureDigest extends Notification
{
    public function __construct(public int $count, private string $html) {}

    /** @return array<string> */
    public function via(mixed $notifiable): array
    {
        return ['mail'];
    }

    public function mailerName(): string
    {
        return 'article_closure_digest';
    }

    public function toMail(mixed $notifiable): MailMessage
    {
        return (new MailMessage)->mailer($this->mailerName())
            ->subject(sprintf('リンク切れ等により%d件の記事を非公開にしました', $this->count))
            ->view('emails.article-closure-prepared', ['html' => new HtmlString($this->html)]);
    }
}
