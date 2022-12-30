<?php

declare(strict_types=1);

namespace App\Services\Twitter;

use App\Services\Twitter\Exceptions\TweetFailedException;
use Illuminate\Support\Collection;

class TweetService
{
    public function __construct(
        private TwitterV1Api $client,
        private bool $isProd
    ) {
    }

    public function post(string $message = ''): ?TweetDataV1
    {
        if ($this->isProd) {
            $params = [
                'status' => $message,
            ];
            $res = $this->handleResponse($this->client->post('statuses/update', $params));

            return new TweetDataV1($res);
        }
        logger(sprintf('Tweet %s', $message));

        return null;
    }

    /**
     * @param  array<string>  $mediaPathes
     */
    public function postMedia(array $mediaPathes = [], string $message = ''): ?TweetDataV1
    {
        $mediaPathes = collect($mediaPathes);

        if ($this->isProd) {
            $media = $this->uploadMedia($mediaPathes);
            $params = [
                'status' => $message,
                'media_ids' => $media->pluck('media_id_string')->implode(','),
            ];
            $res = $this->handleResponse($this->client->post('statuses/update', $params));

            return new TweetDataV1($res);
        }
        logger(sprintf('Tweet with media %s, %s', $message, $mediaPathes->implode(', ')));

        return null;
    }

    /**
     * @param  Collection<int, string>  $mediaPathes
     * @return Collection<int, object> $mediaPathes
     */
    private function uploadMedia(Collection $mediaPathes): Collection
    {
        return $mediaPathes->map(function ($mediaPath) {
            return $this->handleResponse($this->client->upload('media/upload', ['media' => $mediaPath]));
        });
    }

    /**
     * @param  array<mixed>|object  $res
     */
    private function handleResponse(array|object $res): object
    {
        if (isset($res->errors)) {
            logger()->error('tweet failed', [$res]);
            throw new TweetFailedException();
        }

        return (object) $res;
    }
}
