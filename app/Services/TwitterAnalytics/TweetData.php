<?php

namespace App\Services\TwitterAnalytics;

use Carbon\Carbon;
use stdClass;

class TweetData
{
    public string $id;
    public string $text;
    public int $retweetCount;
    public int $replyCount;
    public int $likeCount;
    public int $quoteCount;
    public Carbon $createdAt;

    public string $title;
    public bool $isNew;
    public ?int $articleId = null;

    public function __construct(stdClass $data)
    {
        $this->id = $data->id;
        $this->text = $data->text;
        $this->retweetCount = $data->public_metrics->retweet_count;
        $this->replyCount = $data->public_metrics->reply_count;
        $this->likeCount = $data->public_metrics->like_count;
        $this->quoteCount = $data->public_metrics->quote_count;
        $this->createdAt = Carbon::parse($data->created_at);

        $this->parseText();
    }

    private function parseText()
    {
        $this->isNew = mb_stripos($this->text, '新規投稿') === 0;

        $reg = $this->isNew ? '/\A新規投稿「(.*)」\s/' : '/\A「(.*)」更新\s/';

        preg_match($reg, $this->text, $matches);

        $this->title = $matches[1] ?? '';
    }
}
