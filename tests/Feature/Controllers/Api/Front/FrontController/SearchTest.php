<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Front\FrontController;

use App\Models\Article;
use Tests\Feature\TestCase;

final class SearchTest extends TestCase
{
    private Article $article;

    protected function setUp(): void
    {
        parent::setUp();
        $this->article = Article::factory()->publish()->create();
    }

    public function test(): void
    {
        $testResponse = $this->get('api/front/search?word='.$this->article->title);

        $testResponse->assertOk();
        $testResponse->assertSee($this->article->title);
    }
}
