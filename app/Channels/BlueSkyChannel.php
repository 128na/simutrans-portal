<?php

declare(strict_types=1);

namespace App\Channels;

use App\Models\Article;
use App\Notifications\ArticleNotification;
use App\Notifications\ArticlePublished;
use App\Notifications\ArticleUpdated;
use App\Services\BlueSky\BlueSkyApiClient;
use App\Services\Notification\MessageGenerator;
use Exception;
use potibm\Bluesky\Feed\Post;
use Throwable;

class BlueSkyChannel extends BaseChannel
{
    public function __construct(
        private BlueSkyApiClient $blueSkyApiClient,
        private MessageGenerator $messageGenerator,
    ) {
    }

    public function send(Article $notifiable, ArticleNotification $notification): void
    {
        try {
            $post = $this->buildMessage($notifiable, $notification);
            $result = $this->blueSkyApiClient->send($post);
            logger('blueSky', [$result->getUri()]);
        } catch (Throwable $e) {
            report($e);
        }
    }

    private function buildMessage(Article $notifiable, ArticleNotification $notification): Post
    {
        $text = match (true) {
            $notification instanceof ArticlePublished => $this->messageGenerator->buildSimplePublishedMessage($notifiable),
            $notification instanceof ArticleUpdated => $this->messageGenerator->buildSimpleUpdatedMessage($notifiable),
            default => throw new Exception(sprintf('unsupport notification "%s" provided', get_class($notification))),
        };
        $post = Post::create($text);

        return $this->blueSkyApiClient->addWebsiteCard($post, $notifiable);
    }

    public static function featureEnabled(): bool
    {
        return (bool) config('services.bluesky.user');
    }
}
