<?php

namespace App\Repositories\Article;

use App\Models\Article\TweetLog;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\LazyCollection;

class TweetLogRepository extends BaseRepository
{
    /**
     * @var TweetLog
     */
    protected $model;

    public function __construct(TweetLog $model)
    {
        $this->model = $model;
    }

    /**
     * @param array<int|string> $articleIds
     * @return LazyCollection<\stdClass>
     */
    public function cursorTweetLogSummary(array $articleIds = []): LazyCollection
    {
        return DB::table('tweet_logs')
            ->select(DB::raw('
                article_id,
                sum(retweet_count) as total_retweet_count,
                sum(reply_count) as total_reply_count,
                sum(like_count) as total_like_count,
                sum(quote_count) as total_quote_count,
                sum(impression_count) as total_impression_count,
                sum(url_link_clicks) as total_url_link_clicks,
                sum(user_profile_clicks) as total_user_profile_clicks
            '))
            ->whereIn('article_id', $articleIds)
            ->groupBy('article_id')
            ->cursor();
    }
}
