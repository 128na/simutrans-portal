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
        return LazyCollection::make(function () use ($username) {
            $query = [
                'query' => "from:{$username}",
                'tweet.fields' => 'text,public_metrics,created_at,non_public_metrics',
                'max_results' => 100,
            ];
            // 7日で2ページ分もツイート発生しないのでページングは考慮しない
            $result = $this->client->get('tweets/search/recent', $query);
            logger('searchTweetsByUsername', [$result]);

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
                'tweet.fields' => 'text,public_metrics,created_at,non_public_metrics',
                'max_results' => 100,
            ];

            $paginationToken = null;
            do {
                if ($paginationToken) {
                    $query['pagination_token'] = $paginationToken;
                }
                $result = $this->client->get("lists/{$listId}/tweets", $query);
                logger('searchTweetsByList', [$result]);

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

    /**
     * @see https://developer.twitter.com/en/docs/twitter-api/tweets/search/api-reference/get-tweets-search-recent
     */
    public function searchTweetsByIds(array $ids): LazyCollection
    {
        if (count($ids) > 100) {
            throw new TooManyIdsException();
        }

        return LazyCollection::make(function () use ($ids) {
            $query = [
                'ids' => implode(',', $ids),
                'tweet.fields' => 'text,public_metrics,created_at',
            ];
            $result = $this->client->get('tweets', $query);
            logger('searchTweetsByIds', [$result]);

            foreach ($result->data ?? [] as $d) {
                try {
                    yield new TweetData($d);
                } catch (InvalidTweetDataException $e) {
                    report($e);
                }
            }
        });
    }
}
