<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Front\FrontController;

use App\Models\Article;
use Tests\Feature\TestCase;

final class PagesTest extends TestCase
{
    private Article $article;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->article = Article::factory()
            ->publish()
            ->page()
            ->create();
    }

    public function test(): void
    {
        $testResponse = $this->get('api/front/pages');

        $testResponse->assertOk();
        $testResponse->assertSee($this->article->title);
    }
}
