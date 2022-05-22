<?php

namespace Tests\Unit\Services\GoogleAnalytics;

use App\Repositories\ArticleRepository;
use App\Services\GoogleAnalytics\ResolveArticleService;
use Mockery\MockInterface;
use Tests\UnitTestCase;

class ResolveArticleServiceTest extends UnitTestCase
{
    private function getSUT(): ResolveArticleService
    {
        return app(ResolveArticleService::class);
    }

    public function test()
    {
        $this->mock(ArticleRepository::class, function (MockInterface $m) {
            $m->shouldReceive('findBySlugs')->withArgs([['dummy']])->andReturn(collect());
        });

        $service = $this->getSUT();
        $service->pathToArticles(['/articles/dummy']);
    }
}
