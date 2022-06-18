<?php

namespace Tests\Unit\Services\Twitter;

use App\Models\Article;
use App\Models\Article\TweetLog;
use App\Repositories\Article\TweetLogRepository;
use App\Repositories\ArticleRepository;
use App\Services\Twitter\ResolveArticleService;
use App\Services\Twitter\TweetData;
use Mockery\MockInterface;
use stdClass;
use Tests\UnitTestCase;

class ResolveArticleServiceTest extends UnitTestCase
{
    private function getSUT(): ResolveArticleService
    {
        return app(ResolveArticleService::class);
    }

    private function createMockData(): TweetData
    {
        $publicMetrics = new stdClass();
        $publicMetrics->retweet_count = 1;
        $publicMetrics->reply_count = 2;
        $publicMetrics->like_count = 3;
        $publicMetrics->quote_count = 4;

        $nonPublicMetrics = new stdClass();
        $nonPublicMetrics->impression_count = 5;
        $nonPublicMetrics->url_link_clicks = 6;
        $nonPublicMetrics->user_profile_clicks = 7;

        $data = new stdClass();
        $data->id = '123';
        $data->text = "新規投稿「dummy」\n";
        $data->created_at = '2022-01-01T23:59:59+09:00';
        $data->public_metrics = $publicMetrics;
        $data->non_public_metrics = $nonPublicMetrics;

        return new TweetData($data);
    }

    public function testresolveByTweetDatas登録済みでタイトル不一致()
    {
        $this->mock(TweetLogRepository::class, function (MockInterface $m) {
            $tweetLog = new TweetLog(['article_id' => 456]);
            $tweetLog->id = '123';
            $m->shouldReceive('findByIds')
                ->withArgs([['123']])
                ->once()
                ->andReturn(collect([$tweetLog]));
        });
        $this->mock(ArticleRepository::class, function (MockInterface $m) {
            $m->shouldReceive('findByTitles')
                ->withArgs([['dummy']])
                ->once()
                ->andReturn(collect());
        });

        $service = $this->getSUT();

        $data = [$this->createMockData()];

        $response = $service->resolveByTweetDatas($data);

        $this->assertCount(1, $response);

        $this->assertEquals(456, $response[0]->articleId);
    }

    public function testresolveByTweetDatasタイトル一致()
    {
        $this->mock(TweetLogRepository::class, function (MockInterface $m) {
            $m->shouldReceive('findByIds')
                ->withArgs([['123']])
                ->once()
                ->andReturn(collect());
        });
        $this->mock(ArticleRepository::class, function (MockInterface $m) {
            $article = new Article(['title' => 'dummy']);
            $article->id = 456;
            $m->shouldReceive('findByTitles')
                ->withArgs([['dummy']])
                ->once()
                ->andReturn(collect([$article]));
        });

        $service = $this->getSUT();

        $data = [$this->createMockData()];

        $response = $service->resolveByTweetDatas($data);

        $this->assertCount(1, $response);

        $this->assertEquals(456, $response[0]->articleId);
    }

    public function testresolveByTweetDatas解決できない()
    {
        $this->mock(TweetLogRepository::class, function (MockInterface $m) {
            $m->shouldReceive('findByIds')
                ->withArgs([['123']])
                ->once()
                ->andReturn(collect());
        });
        $this->mock(ArticleRepository::class, function (MockInterface $m) {
            $m->shouldReceive('findByTitles')
                ->withArgs([['dummy']])
                ->once()
                ->andReturn(collect());
        });

        $service = $this->getSUT();

        $data = [$this->createMockData()];

        $response = $service->resolveByTweetDatas($data);

        $this->assertCount(1, $response);

        $this->assertNull($response[0]->articleId);
    }
}
