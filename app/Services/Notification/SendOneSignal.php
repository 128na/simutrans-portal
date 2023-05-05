<?php

declare(strict_types=1);

namespace App\Services\Notification;

use App\Models\Article;
use App\Services\Service;
use Berkayk\OneSignal\OneSignalFacade;

class SendOneSignal extends Service
{
    public function __construct(private MessageGenerator $messageGenerator)
    {
    }

    public function sendArticlePublishedNotification(Article $article): void
    {
        if ($this->featureEnabled()) {
            OneSignalFacade::sendNotificationToAll(
                $this->messageGenerator->buildPublishedMessage($article),
                route('articles.show', $article->slug),
            );
        }
    }

    public function sendArticleUpdatedNotification(Article $article): void
    {
        if ($this->featureEnabled()) {
            OneSignalFacade::sendNotificationToAll(
                $this->messageGenerator->buildUpdatedMessage($article),
                route('articles.show', $article->slug),
            );
        }
    }

    private function featureEnabled(): bool
    {
        return config('onesignal.app_id') && config('onesignal.rest_api_key');
    }
}
