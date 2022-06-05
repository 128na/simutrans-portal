<?php

namespace App\Services\TwitterAnalytics;

use Illuminate\Support\LazyCollection;

class SearchTweetService
{
    public function __construct(private TwitterV2Api $client)
    {
    }

    /**
     * @see https://developer.twitter.com/en/docs/twitter-api/tweets/search/api-reference/get-tweets-search-recent
     */
    public function searchTweetsByUsername(string $username): LazyCollection
    {
        return LazyCollection::make(function () use ($username) {
            $query = [
                'query' => "from:{$username}",
                'tweet.fields' => $this->client->isPkceToken()
                    ? 'text,public_metrics,created_at,non_public_metrics'
                    : 'text,public_metrics,created_at',
                'max_results' => 100,
            ];
            // 7日で2ページ分もツイート発生しないのでページングは考慮しない
            $result = $this->client->get('tweets/search/recent', $query);

            foreach ($result->data ?? [] as $d) {
                try {
                    yield new TweetData($d);
                } catch (InvalidTweetDataException $e) {
                }
            }
        });
    }

    /**
     * @see https://developer.twitter.com/en/docs/twitter-api/lists/list-tweets/api-reference/get-lists-id-tweets
     */
    public function searchTweetsByList(string $listId): LazyCollection
    {
        return LazyCollection::make(function () use ($listId) {
            $query = [
                'tweet.fields' => $this->client->isPkceToken()
                    ? 'text,public_metrics,created_at,non_public_metrics'
                    : 'text,public_metrics,created_at',
                'max_results' => 100,
            ];

            $paginationToken = null;
            do {
                if ($paginationToken) {
                    $query['pagination_token'] = $paginationToken;
                }
                $result = $this->client->get("lists/{$listId}/tweets", $query);

                $data = $result->data ?? [];

                foreach ($data as $d) {
                    yield new TweetData($d);
                }

                if (isset($result->meta->next_token)) {
                    $paginationToken = $result->meta->next_token;
                    sleep(1);
                } else {
                    $paginationToken = null;
                }
            } while ($paginationToken);
        });
    }
}
