<?php

namespace App\Services;

use Abraham\TwitterOAuth\TwitterOAuth;

class TweetService
{
    private TwitterOAuth $client;
    private bool $is_prod;

    public function __construct()
    {
        $this->client = new TwitterOAuth(
            config('twitter.consumer_key'),
            config('twitter.consumer_secret'),
            config('twitter.access_token'),
            config('twitter.access_token_secret')
        );
        $this->is_prod = \App::environment(['production']);
    }

    public function post($message = '')
    {
        if ($this->is_prod) {
            $params = [
                'status' => $message,
            ];
            $this->handleResponse($this->client->post('statuses/update', $params));
        }
        logger(sprintf('Tweet %s', $message));
    }

    public function postMedia($media_paths = [], $message = '')
    {
        $media_paths = collect($media_paths);

        if ($this->is_prod) {
            $media = $this->uploadMedia($media_paths);
            $params = [
                'status' => $message,
                'media_ids' => $media->pluck('media_id_string')->implode(','),
            ];
            $this->handleResponse($this->client->post('statuses/update', $params));
        }
        logger(sprintf('Tweet with media %s, %s', $message, $media_paths->implode(', ')));
    }

    private function uploadMedia($media_paths)
    {
        return $media_paths->map(function ($media_path) {
            return $this->handleResponse($this->client->upload('media/upload', ['media' => $media_path]));
        });
    }

    private function handleResponse($res)
    {
        if (isset($res->errors)) {
            $msg = 'Tweet failed';
            foreach ($res->errors as $error) {
                $msg .= $error->message;
            }
            throw new \Exception($msg, 1);
        }
        return $res;
    }
}
