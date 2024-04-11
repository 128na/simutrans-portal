<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Front\FrontController;

use App\Models\Article;
use App\Models\Tag;
use Tests\Feature\TestCase;

final class TagTest extends TestCase
{
    private Article $article;

    private Tag $tag;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->article = Article::factory()->publish()->create();
        $this->tag = Tag::factory()->create();
        $this->article->tags()->save($this->tag);
    }

    public function test(): void
    {
        $testResponse = $this->get('api/front/tags/'.$this->tag->id);

        $testResponse->assertOk();
        $testResponse->assertSee($this->article->title);
    }

    public function test存在しない(): void
    {
        $testResponse = $this->get('api/front/tags/0');

        $testResponse->assertNotFound();
    }
}
