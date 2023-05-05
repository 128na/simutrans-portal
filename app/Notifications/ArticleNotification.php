<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Channels\TwitterChannel;
use App\Models\Article;
use App\Services\Notification\MessageGenerator;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

abstract class ArticleNotification extends Notification
{
    use Queueable;

    protected MessageGenerator $messageGenerator;

    public function __construct()
    {
        $this->messageGenerator = app(MessageGenerator::class);
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array<string>
     */
    public function via($notifiable)
    {
        return [
            TwitterChannel::class,
        ];
    }

    /**
     * @param  Article  $article
     * @return string
     */
    abstract public function toTwitter($article);
}
