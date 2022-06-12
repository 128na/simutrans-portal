<?php

namespace App\Services\Twitter;

use App\Services\Twitter\Exceptions\InvalidTweetDataException;
use Carbon\Carbon;
use stdClass;

class TweetData
{
    /**
     * ツイートID.
     */
    public string $id;
    /**
     * ツイート本文.
     */
    public string $text;
    /**
     * リツイート回数.
     */
    public int $retweetCount;
    /**
     * 返信数.
     */
    public int $replyCount;
    /**
     * いいね数.
     */
    public int $likeCount;
    /**
     * 引用RT回数.
     */
    public int $quoteCount;
    /**
     * ツイート表示回数.
     */
    public int $impressionCount;
    /**
     * ツイート内のURLクリック回数.
     */
    public int $urlLinkClicks;
    /**
     * ツイートのプロフィールクリック回数.
     */
    public int $userProfileClicks;
    /**
     * ツイート日時
     */
    public Carbon $createdAt;

    public string $title;
    public bool $isNew;
    public ?int $articleId = null;

    /**
     * @throws InvalidTweetDataException
     */
    public function __construct(stdClass $data)
    {
        $this->id = $data->id_str;
        $this->text = $data->text;
        $this->retweetCount = $data->public_metrics->retweet_count;
        $this->replyCount = $data->public_metrics->reply_count;
        $this->likeCount = $data->public_metrics->like_count;
        $this->quoteCount = $data->public_metrics->quote_count;
        $this->impressionCount = $data?->non_public_metrics->impression_count ?? 0;
        $this->urlLinkClicks = $data?->non_public_metrics->url_link_clicks ?? 0;
        $this->userProfileClicks = $data?->non_public_metrics->user_profile_clicks ?? 0;
        $this->createdAt = Carbon::parse($data->created_at);

        $this->parseText();
    }

    protected function parseText(): void
    {
        $this->isNew = mb_stripos($this->text, '新規投稿') === 0;

        $reg = $this->isNew ? '/\A新規投稿「(.*)」\s/' : '/\A「(.*)」更新\s/';

        preg_match($reg, $this->text, $matches);

        if (!isset($matches[1])) {
            throw new InvalidTweetDataException();
        }
        $this->title = $matches[1];
    }
}
