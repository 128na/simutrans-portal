<?php

namespace App\Services\TwitterAnalytics;

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
     * @param TweetData[] $resolved
     *
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
                    [
                        'article_id' => $tweetData->articleId,
                        'text' => $tweetData->text,
                        'retweet_count' => $tweetData->retweetCount,
                        'reply_count' => $tweetData->replyCount,
                        'like_count' => $tweetData->likeCount,
                        'quote_count' => $tweetData->quoteCount,
                        'tweet_created_at' => $tweetData->createdAt,
                    ]
                );
            }
        }

        return $articleIds;
    }

    /**
     * @param int[] $articleIds
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
                ]
            );
        }
    }
}
