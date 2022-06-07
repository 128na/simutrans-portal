<?php

namespace App\Services\TwitterAnalytics;

use App\Services\TwitterAnalytics\Exceptions\InvalidTweetDataException;

class TweetDataOldFormatSupport extends TweetData
{
    protected function parseText(): void
    {
        try {
            parent::parseText();
        } catch (InvalidTweetDataException $e) {
            try {
                $this->parseOldFormat1();
            } catch (InvalidTweetDataException $e) {
                $this->parseOldFormat2();
            }
        }
    }

    private function parseOldFormat1(): void
    {
        $reg = '/\A新規投稿アドオン「(.*)」 by/';

        preg_match($reg, $this->text, $matches);

        if (!isset($matches[1])) {
            throw new InvalidTweetDataException();
        }
        $this->title = $matches[1];
        $this->isNew = true;
    }

    private function parseOldFormat2(): void
    {
        $reg = '/\A新規投稿アドオン「(.*)」\n/';

        preg_match($reg, $this->text, $matches);

        if (!isset($matches[1])) {
            throw new InvalidTweetDataException();
        }
        $this->title = $matches[1];
        $this->isNew = true;
    }
}
