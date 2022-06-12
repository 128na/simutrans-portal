<?php

namespace App\Services\Twitter;

use App\Services\Twitter\Exceptions\TweetFailedException;

class TweetService
{
    public function __construct(
        private TwitterV1Api $client,
        private bool $isProd
    ) {
    }

    public function post($message = ''): ?TweetDataV1
    {
        if ($this->isProd) {
            $params = [
                'status' => $message,
            ];
            $res = $this->handleResponse($this->client->post('statuses/update', $params));

            return new TweetDataV1($res);
        }
        logger(sprintf('Tweet %s', $message));
    }

    public function postMedia($mediaPathes = [], $message = ''): ?TweetDataV1
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
    }

    private function uploadMedia($mediaPathes)
    {
        return $mediaPathes->map(function ($media_path) {
            return $this->handleResponse($this->client->upload('media/upload', ['media' => $media_path]));
        });
    }

    private function handleResponse($res)
    {
        if (isset($res->errors)) {
            logger()->error('tweet failed', [$res]);
            throw new TweetFailedException();
        }

        return $res;
    }
}
