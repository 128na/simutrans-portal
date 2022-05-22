<?php

namespace Tests\Feature\Repositories\ArticleRepository;

use App\Repositories\ArticleRepository;
use Tests\ArticleTestCase;

class UpdateRankingTest extends ArticleTestCase
{
    private ArticleRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(ArticleRepository::class);
    }

    public function test()
    {
        $this->assertDatabaseCount('rankings', 0);
        $this->repository->updateRanking(collect([$this->article]));

        $this->assertDatabaseHas('rankings', [
            'article_id' => $this->article->id,
            'order' => 0,
        ]);
    }
}
