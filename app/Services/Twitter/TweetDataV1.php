<?php

namespace App\Services\Twitter;

use App\Services\Twitter\Exceptions\InvalidTweetDataException;
use Carbon\Carbon;
use stdClass;

class TweetDataV1 extends TweetData
{
    /**
     * @throws InvalidTweetDataException
     */
    public function __construct(stdClass $data)
    {
        $this->id = $data->id_str;
        $this->text = $data->text;
        $this->retweetCount = $data->retweet_count;
        $this->replyCount = $data->reply_count;
        $this->likeCount = $data->favorite_count;
        $this->quoteCount = $data->quote_count;
        $this->impressionCount = 0;
        $this->urlLinkClicks = 0;
        $this->userProfileClicks = 0;
        $this->createdAt = Carbon::parse($data->created_at);

        $this->parseText();
    }
}
