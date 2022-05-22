<?php

namespace Tests\Unit\Services\GoogleAnalytics;

use App\Models\Article;
use App\Repositories\ArticleRepository;
use App\Services\GoogleAnalytics\RankingService;
use Mockery\MockInterface;
use Tests\UnitTestCase;

class RankingServiceTest extends UnitTestCase
{
    private function getSUT(): RankingService
    {
        return app(RankingService::class);
    }

    public function test()
    {
        $this->mock(ArticleRepository::class, function (MockInterface $m) {
            $m->shouldReceive('updateRanking');
        });

        $service = $this->getSUT();
        $service->updateRanking(collect([new Article()]));
    }
}
