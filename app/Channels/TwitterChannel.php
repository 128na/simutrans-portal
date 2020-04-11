<?php

namespace App\Channels;

use Abraham\TwitterOAuth\TwitterOAuth;
use Illuminate\Notifications\Notification;

class TwitterChannel
{
    /**
     * @var TwitterOAuth
     */
    private $client;

    public function __construct()
    {
        $this->client = new TwitterOAuth(
            config('twitter.consumer_key'),
            config('twitter.consumer_secret'),
            config('twitter.access_token'),
            config('twitter.access_token_secret')
        );
    }
    /**
     * 指定された通知の送信
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {
        $message = $notification->toTwitter($notifiable);

        // 通知を$notifiableインスタンスへ送信する…
        if ($message && \App::environment(['production'])) {
            $res = $this->client->post('statuses/update', ['status' => $message]);
            dd($res);
        }
        logger('Tweet: ' . $message);
    }

}
