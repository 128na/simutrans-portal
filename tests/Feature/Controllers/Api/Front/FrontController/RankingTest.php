<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Front\FrontController;

use App\Models\Article\Ranking;
use Tests\Feature\TestCase;

class RankingTest extends TestCase
{
    private Ranking $ranking;

    protected function setUp(): void
    {
        parent::setUp();
        $this->ranking = Ranking::factory()->create();
    }

    public function test(): void
    {
        $testResponse = $this->get('api/front/ranking');

        $testResponse->assertOk();
        $testResponse->assertSee($this->ranking->article->title);
    }
}
