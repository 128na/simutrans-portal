<?php

namespace App\Services\TwitterAnalytics;

use Abraham\TwitterOAuth\TwitterOAuth;

class SearchTweetService
{
    public function __construct(private TwitterOAuth $client)
    {
        $client->setApiVersion('2');
    }

    /**
     * @see https://developer.twitter.com/en/docs/twitter-api/tweets/search/api-reference/get-tweets-search-recent
     *
     * @return TweetData[]
     */
    public function searchMyTweets(): array
    {
        $query = [
            'query' => 'from:'.config('app.twitter'),
            'tweet.fields' => 'text,public_metrics,created_at',
            'max_results' => 100,
        ];
        // 7日で2ページ分もツイート発生しないのでページングは考慮しない
        $data = $this->client->get('tweets/search/recent', $query)->data ?? [];

        return array_map(fn ($d) => new TweetData($d), $data);
    }
}
