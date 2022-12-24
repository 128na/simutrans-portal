<?php

namespace App\Services\Twitter;

use App\Repositories\Article\TweetLogRepository;
use App\Repositories\Article\TweetLogSummaryRepository;

class AggregateTweetLogService
{
    public function __construct(
        private TweetLogRepository $tweetLogRepository,
        private TweetLogSummaryRepository $tweetLogSummaryRepository
    ) {
    }

    /**
     * 値が0のフィールドを更新から除外する.
     * @param array<string,string|int> $data
     * @return array<string,string|int>
     */
    private function filterUpdatableFields(array $data): array
    {
        return array_filter($data, fn ($d) => ! is_numeric($d) || $d > 0);
    }

    /**
     * @param  TweetData[]  $resolved
     * @return int[] articleIds
     */
    public function updateOrCreateTweetLogs(array $resolved): array
    {
        $articleIds = [];
        foreach ($resolved as $tweetData) {
            if ($tweetData->articleId) {
                $articleIds[] = $tweetData->articleId;
                $this->tweetLogRepository->updateOrCreate(
                    ['id' => $tweetData->id],
                    $this->filterUpdatableFields([
                        'article_id' => $tweetData->articleId,
                        'text' => $tweetData->text,
                        'retweet_count' => $tweetData->retweetCount,
                        'reply_count' => $tweetData->replyCount,
                        'like_count' => $tweetData->likeCount,
                        'quote_count' => $tweetData->quoteCount,
                        'impression_count' => $tweetData->impressionCount,
                        'url_link_clicks' => $tweetData->urlLinkClicks,
                        'user_profile_clicks' => $tweetData->userProfileClicks,
                        'tweet_created_at' => $tweetData->createdAt,
                    ])
                );
            }
        }

        return $articleIds;
    }

    /**
     * @param  TweetData[]  $resolved
     * @return int[] articleIds
     */
    public function firstOrCreateTweetLogs(array $resolved): array
    {
        $articleIds = [];
        foreach ($resolved as $tweetData) {
            if ($tweetData->articleId) {
                $articleIds[] = $tweetData->articleId;
                $this->tweetLogRepository->firstOrCreate(
                    ['id' => $tweetData->id],
                    $this->filterUpdatableFields([
                        'article_id' => $tweetData->articleId,
                        'text' => $tweetData->text,
                        'retweet_count' => $tweetData->retweetCount,
                        'reply_count' => $tweetData->replyCount,
                        'like_count' => $tweetData->likeCount,
                        'quote_count' => $tweetData->quoteCount,
                        'impression_count' => $tweetData->impressionCount,
                        'url_link_clicks' => $tweetData->urlLinkClicks,
                        'user_profile_clicks' => $tweetData->userProfileClicks,
                        'tweet_created_at' => $tweetData->createdAt,
                    ])
                );
            }
        }

        return $articleIds;
    }

    /**
     * @param  int[]  $articleIds
     */
    public function updateOrCreateTweetLogSummary(array $articleIds): void
    {
        foreach ($this->tweetLogRepository->cursorTweetLogSummary($articleIds) as $summary) {
            $this->tweetLogSummaryRepository->updateOrCreate(
                ['article_id' => $summary->article_id],
                [
                    'total_retweet_count' => $summary->total_retweet_count,
                    'total_reply_count' => $summary->total_reply_count,
                    'total_like_count' => $summary->total_like_count,
                    'total_quote_count' => $summary->total_quote_count,
                    'total_impression_count' => $summary->total_impression_count,
                    'total_url_link_clicks' => $summary->total_url_link_clicks,
                    'total_user_profile_clicks' => $summary->total_user_profile_clicks,
                ]
            );
        }
    }
}
