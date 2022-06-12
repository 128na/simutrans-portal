<?php

namespace App\Services\Twitter;

use App\Services\Twitter\Exceptions\InvalidTweetDataException;
use App\Services\Twitter\Exceptions\TooManyIdsException;
use Illuminate\Support\LazyCollection;

class SearchTweetService
{
    public const USE_PKCE_TOKEN = 'USE_PKCE_TOKEN';
    public const USE_APP_ONLY_TOKEN = 'USE_APP_ONLY_TOKEN';

    public function __construct(private TwitterV2Api $client)
    {
    }

    /**
     * @see https://developer.twitter.com/en/docs/twitter-api/tweets/search/api-reference/get-tweets-search-recent
     */
    public function searchTweetsByUsername(string $username, string $token = self::USE_PKCE_TOKEN): LazyCollection
    {
        $query = [
            'query' => "from:{$username}",
            'tweet.fields' => 'text,public_metrics,created_at,non_public_metrics',
            'max_results' => 100,
        ];

        return $this->execRequest('tweets/search/recent', $query, $token);
    }

    /**
     * @see https://developer.twitter.com/en/docs/twitter-api/lists/list-tweets/api-reference/get-lists-id-tweets
     */
    public function searchTweetsByList(string $listId, string $token = self::USE_PKCE_TOKEN): LazyCollection
    {
        $query = [
            'tweet.fields' => 'text,public_metrics,created_at,non_public_metrics',
            'max_results' => 100,
        ];

        return $this->execRequest("lists/{$listId}/tweets", $query, $token);
    }

    /**
     * @see https://developer.twitter.com/en/docs/twitter-api/tweets/search/api-reference/get-tweets-search-recent
     */
    public function searchTweetsByIds(array $ids, string $token = self::USE_PKCE_TOKEN): LazyCollection
    {
        if (count($ids) > 100) {
            throw new TooManyIdsException();
        }
        $query = [
            'ids' => implode(',', $ids),
            'tweet.fields' => 'text,public_metrics,created_at',
        ];

        return $this->execRequest('tweets', $query, $token);
    }

    /**
     * @see https://developer.twitter.com/en/docs/twitter-api/lists/list-tweets/api-reference/get-lists-id-tweets
     */
    public function searchTweetsByTimeline(string $userId, string $token = self::USE_PKCE_TOKEN): LazyCollection
    {
        $query = [
            'tweet.fields' => 'text,public_metrics,created_at,non_public_metrics',
            'max_results' => 100,
        ];

        return $this->execRequest("users/{$userId}/tweets", $query, $token);
    }

    private function execRequest(string $endpoint, array $query, string $token): LazyCollection
    {
        if ($token === self::USE_PKCE_TOKEN) {
            $this->client->applyPKCEToken();
        }

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
