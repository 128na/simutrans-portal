<?php

namespace App\Channels;

use Abraham\TwitterOAuth\TwitterOAuth;
use Illuminate\Notifications\Notification;
use App\Services\TweetService;
use App\Models\Article;

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
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return void
     */
    public function send(Article $notifiable, Notification $notification)
    {
        $message = $notification->toTwitter($notifiable);

        if ($notifiable->has_thumbnail) {
            $media_paths = [$notifiable->thumbnail->full_path];
            $this->tweet_service->postMedia($media_paths, $message);
        } else {
            $this->tweet_service->post($message);
        }
    }
}
