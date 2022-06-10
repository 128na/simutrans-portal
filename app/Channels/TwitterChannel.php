<?php

namespace App\Channels;

use App\Models\Article;
use App\Notifications\ArticleNotification;
use App\Services\TweetFailedException;
use App\Services\TweetService;

class TwitterChannel
{
    private TweetService $tweet_service;

    public function __construct(TweetService $tweet_service)
    {
        $this->tweet_service = $tweet_service;
    }

    /**
     * 指定された通知の送信
     *
     * @param mixed $notifiable
     *
     * @return void
     */
    public function send(Article $notifiable, ArticleNotification $notification)
    {
        $message = $notification->toTwitter($notifiable);

        try {
            if ($notifiable->has_thumbnail) {
                $media_paths = [$notifiable->thumbnail->full_path];
                $this->tweet_service->postMedia($media_paths, $message);
            } else {
                $this->tweet_service->post($message);
            }
        } catch (TweetFailedException $e) {
            report($e);
        }
    }
}
