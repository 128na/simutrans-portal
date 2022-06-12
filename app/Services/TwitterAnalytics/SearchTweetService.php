<?php

namespace App\Services\TwitterAnalytics;

use App\Services\TwitterAnalytics\Exceptions\InvalidTweetDataException;
use App\Services\TwitterAnalytics\Exceptions\PKCETokenException;
use App\Services\TwitterAnalytics\Exceptions\TooManyIdsException;
use Illuminate\Support\LazyCollection;

class SearchTweetService
{
    public function __construct(private TwitterV2Api $client)
    {
        try {
            $this->client->applyPKCEToken();
        } catch (PKCETokenException $e) {
            report($e);
        }
    }

    /**
     * @see https://developer.twitter.com/en/docs/twitter-api/tweets/search/api-reference/get-tweets-search-recent
     */
    public function searchTweetsByUsername(string $username): LazyCollection
    {
        $query = [
            'query' => "from:{$username}",
            'tweet.fields' => 'text,public_metrics,created_at,non_public_metrics',
            'max_results' => 100,
        ];

        return $this->execRequest('tweets/search/recent', $query);
    }

    /**
     * @see https://developer.twitter.com/en/docs/twitter-api/lists/list-tweets/api-reference/get-lists-id-tweets
     */
    public function searchTweetsByList(string $listId): LazyCollection
    {
        $query = [
            'tweet.fields' => 'text,public_metrics,created_at,non_public_metrics',
            'max_results' => 100,
        ];

        return $this->execRequest("lists/{$listId}/tweets", $query);
    }

    /**
     * @see https://developer.twitter.com/en/docs/twitter-api/tweets/search/api-reference/get-tweets-search-recent
     */
    public function searchTweetsByIds(array $ids): LazyCollection
    {
        if (count($ids) > 100) {
            throw new TooManyIdsException();
        }
        $query = [
            'ids' => implode(',', $ids),
            'tweet.fields' => 'text,public_metrics,created_at',
        ];

        return $this->execRequest('tweets', $query);
    }

    /**
     * @see https://developer.twitter.com/en/docs/twitter-api/lists/list-tweets/api-reference/get-lists-id-tweets
     */
    public function searchTweetsByTimeline(string $userId): LazyCollection
    {
        $query = [
            'tweet.fields' => 'text,public_metrics,created_at,non_public_metrics',
            'max_results' => 100,
        ];

        return $this->execRequest("users/{$userId}/tweets", $query);
    }

    private function execRequest(string $endpoint, array $query = []): LazyCollection
    {
        return LazyCollection::make(function () use ($endpoint, $query) {
            $paginationToken = null;
            do {
                if ($paginationToken) {
                    $query['pagination_token'] = $paginationToken;
                }
                $result = $this->client->get($endpoint, $query);
                logger($endpoint, [$result]);

                $data = $result->data ?? [];

                foreach ($data as $d) {
                    try {
                        yield new TweetData($d);
                    } catch (InvalidTweetDataException $e) {
                        report($e);
                    }
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
