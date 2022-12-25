<?php

namespace App\Services\Twitter;

use App\Services\Twitter\Exceptions\InvalidTweetDataException;
use Illuminate\Support\LazyCollection;

class SearchTweetService
{
    public const USE_PKCE_TOKEN = 'USE_PKCE_TOKEN';

    public const USE_APP_ONLY_TOKEN = 'USE_APP_ONLY_TOKEN';

    public function __construct(private TwitterV2Api $client)
    {
    }

    private function createFields(string $token): string
    {
        return $token === self::USE_PKCE_TOKEN
            ? 'text,public_metrics,created_at,non_public_metrics'
            : 'text,public_metrics,created_at';
    }

    /**
     * @see https://developer.twitter.com/en/docs/twitter-api/lists/list-tweets/api-reference/get-lists-id-tweets
     *
     * @return LazyCollection<int, TweetDataOldFormatSupport>
     */
    public function searchTweetsByTimeline(string $userId, string $token = self::USE_PKCE_TOKEN): LazyCollection
    {
        $query = [
            'tweet.fields' => $this->createFields($token),
            'max_results' => 100,
        ];

        return $this->execRequest("users/{$userId}/tweets", $query, $token);
    }

    /**
     * @param  array<string, mixed>  $query
     * @return LazyCollection<int, TweetDataOldFormatSupport>
     */
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
                        yield new TweetDataOldFormatSupport($d);
                    } catch (InvalidTweetDataException $e) {
                        logger()->warning('invalid format', [$d]);
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
