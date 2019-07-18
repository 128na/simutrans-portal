<?php

namespace App\Models;

use Abraham\TwitterOAuth\TwitterOAuth;

/**
 * ツイートする
 */
class Twitter
{
    private static $client = null;

    public function __construct()
    {
        self::$client = new TwitterOAuth(
            config('twitter.consumer_key'),
            config('twitter.consumer_secret'),
            config('twitter.access_token'),
            config('twitter.access_token_secret')
        );
    }

    public static function getClient()
    {
        if(is_null(self::$client)) {
            new self;
        }
        return self::$client;
    }

    /**
     * ツイートする（本番環境のみ）
     * それ以外ではログにメッセージを記録するだけ
     */
    public static function post($message)
    {
        if(\App::environment(['production'])) {
            return self::getClient()->post('statuses/update', ['status' => $message ]);
        }
        logger('Tweet: '.$message);
    }

    public static function articleCreated($article)
    {
        $url = route('articles.show', $article->slug);
        $now = now()->format('Y/m/d H:i');
        $message = __("New Article Published. \":title\"\nby :name\nat :at\n#simutrans", ['title' => $article->title, 'name' => $article->user->name, 'at' => $now]);
        return Twitter::post($message);
    }
    public static function articleUpdated($article)
    {
        $url = route('articles.show', $article->slug);
        $now = now()->format('Y/m/d H:i');
        $message = __("\":title\" Updated.\nby :name\nat :at\n#simutrans", ['title' => $article->title, 'name' => $article->user->name, 'at' => $now]);
        return Twitter::post($message);
    }

}
