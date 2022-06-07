<?php

namespace App\Services\TwitterAnalytics;

use App\Services\TwitterAnalytics\Exceptions\InvalidTweetDataException;

class ImportTweetService
{
    public function __construct()
    {
    }

    /**
     * @return TweetData[]
     */
    public function importJson(string $path): array
    {
        $data = json_decode(file_get_contents($path));

        $tweets = [];
        foreach ($data as $d) {
            try {
                $tweets[] = new TweetDataOldFormatSupport($d);
            } catch (InvalidTweetDataException $e) {
                logger('InvalidTweetDataException', [$d]);
            }
        }

        return $tweets;
    }
}
