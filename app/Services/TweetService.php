<?php

namespace App\Services;

use App\Services\TwitterAnalytics\TwitterV1Api;

class TweetService
{
    public function __construct(
        private TwitterV1Api $client,
        private bool $isProd
    ) {
    }

    public function post($message = '')
    {
        if ($this->isProd) {
            $params = [
                'status' => $message,
            ];
            $this->handleResponse($this->client->post('statuses/update', $params));
        }
        logger(sprintf('Tweet %s', $message));
    }

    public function postMedia($mediaPathes = [], $message = '')
    {
        $mediaPathes = collect($mediaPathes);

        if ($this->isProd) {
            $media = $this->uploadMedia($mediaPathes);
            $params = [
                'status' => $message,
                'media_ids' => $media->pluck('media_id_string')->implode(','),
            ];
            $this->handleResponse($this->client->post('statuses/update', $params));
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
