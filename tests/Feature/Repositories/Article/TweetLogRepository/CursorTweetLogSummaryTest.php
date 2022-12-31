<?php

declare(strict_types=1);

namespace Tests\Feature\Repositories\Article\TweetLogRepository;

use App\Models\Article;
use App\Models\Article\TweetLog;
use App\Repositories\Article\TweetLogRepository;
use Illuminate\Support\LazyCollection;
use Tests\TestCase;

class CursorTweetLogSummaryTest extends TestCase
{
    private TweetLogRepository $repository;

    private Article $article;

    private TweetLog $tweetLog1;

    private TweetLog $tweetLog2;

    private TweetLog $tweetLog3;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(TweetLogRepository::class);

        $this->article = Article::factory()->create();
        $this->tweetLog1 = TweetLog::factory()->create(['article_id' => $this->article->id]);
        $this->tweetLog2 = TweetLog::factory()->create(['article_id' => $this->article->id]);
        $this->tweetLog3 = TweetLog::factory()->create();
    }

    public function test()
    {
        $res = $this->repository->cursorTweetLogSummary([$this->article->id]);
        $this->assertInstanceOf(LazyCollection::class, $res);

        $items = $res->toArray();

        $this->assertCount(1, $items);
        $this->assertEquals($this->article->id, $items[0]->article_id);
        $this->assertEquals($this->tweetLog1->retweet_count + $this->tweetLog2->retweet_count, $items[0]->total_retweet_count);
        $this->assertEquals($this->tweetLog1->reply_count + $this->tweetLog2->reply_count, $items[0]->total_reply_count);
        $this->assertEquals($this->tweetLog1->like_count + $this->tweetLog2->like_count, $items[0]->total_like_count);
        $this->assertEquals($this->tweetLog1->quote_count + $this->tweetLog2->quote_count, $items[0]->total_quote_count);
    }
}
