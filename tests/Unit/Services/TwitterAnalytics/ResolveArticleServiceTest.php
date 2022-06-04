<?php

namespace Tests\Unit\Services\TwitterAnalytics;

use App\Models\Article;
use App\Repositories\ArticleRepository;
use App\Services\TwitterAnalytics\ResolveArticleService;
use App\Services\TwitterAnalytics\TweetData;
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

        $data = new stdClass();
        $data->id = '123';
        $data->text = "新規投稿「dummy」\n";
        $data->created_at = '2022-01-01T23:59:59+09:00';
        $data->public_metrics = $publicMetrics;

        return new TweetData($data);
    }

    public function testTitleToArticles()
    {
        $this->mock(ArticleRepository::class, function (MockInterface $m) {
            $article = new Article(['title' => 'dummy']);
            $article->id = 123;
            $m->shouldReceive('findByTitles')
                ->withArgs([['dummy']])
                ->andReturn(collect([$article]));
        });

        $service = $this->getSUT();

        $data = [$this->createMockData()];

        $response = $service->titleToArticles($data);

        $this->assertCount(1, $response);

        $this->assertEquals(123, $response[0]->articleId);
    }
}
