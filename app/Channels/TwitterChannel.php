<?php

declare(strict_types=1);

namespace App\Channels;

use App\Models\Article;
use App\Notifications\ArticleNotification;
use App\Repositories\Article\TweetLogRepository;
use App\Services\Twitter\Exceptions\InvalidTweetDataException;
use App\Services\Twitter\Exceptions\TweetFailedException;
use App\Services\Twitter\TweetService;
use Throwable;

class TwitterChannel
{
    public function __construct(private TweetService $tweetService, private TweetLogRepository $tweetLogRepository)
    {
    }

    /**
     * 指定された通知の送信
     *
     * @return void
     */
    public function send(Article $notifiable, ArticleNotification $notification)
    {
        try {
            $message = $notification->toTwitter($notifiable);
            $tweetData = $notifiable->has_thumbnail && $notifiable->thumbnail
                ? $this->tweetService->postMedia([$notifiable->thumbnail->full_path], $message)
                : $this->tweetService->post($message);

            if ($tweetData) {
                $this->tweetLogRepository->store([
                    'id' => $tweetData->id,
                    'article_id' => $notifiable->id,
                    'text' => $tweetData->text,
                    'retweet_count' => 0,
                    'reply_count' => 0,
                    'like_count' => 0,
                    'quote_count' => 0,
                    'impression_count' => 0,
                    'url_link_clicks' => 0,
                    'user_profile_clicks' => 0,
                    'tweet_created_at' => $tweetData->createdAt,
                ]);
            }
        } catch (TweetFailedException|InvalidTweetDataException $e) {
            report($e);
        } catch (Throwable $e) {
            report($e);
        }
    }
}
