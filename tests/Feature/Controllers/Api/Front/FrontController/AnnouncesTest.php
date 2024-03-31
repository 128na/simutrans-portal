<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Front\FrontController;

use App\Models\Article;
use Tests\Feature\TestCase;

class AnnouncesTest extends TestCase
{
    private Article $article;

    protected function setUp(): void
    {
        parent::setUp();
        $this->article = $this->createAnnounce();
    }

    public function test(): void
    {
        $testResponse = $this->get('api/front/announces');

        $testResponse->assertOk();
        $testResponse->assertSee($this->article->title);
    }
}