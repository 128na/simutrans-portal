<?php

namespace App\Services\TwitterAnalytics;

use Abraham\TwitterOAuth\TwitterOAuth;

class SearchTweetService
{
    public function __construct(private TwitterOAuth $client)
    {
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

        $tweetDataArray = array_map(function ($d) {
            try {
                return new TweetData($d);
            } catch (InvalidTweetDataException $e) {
                return null;
            }
        }, $data);

        return array_filter($tweetDataArray, fn (?TweetData $d) => !is_null($d));
    }
}
