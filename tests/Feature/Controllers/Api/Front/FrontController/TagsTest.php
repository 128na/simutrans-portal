<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Front\FrontController;

use App\Models\Article;
use App\Models\Tag;
use Tests\Feature\TestCase;

class TagsTest extends TestCase
{
    private Article $article;

    private Tag $tag;

    protected function setUp(): void
    {
        parent::setUp();
        $this->article = Article::factory()->publish()->create();
        $this->tag = Tag::factory()->create();
        $this->article->tags()->save($this->tag);
    }

    public function test(): void
    {
        $testResponse = $this->get('api/front/tags');

        $testResponse->assertOk();
        $testResponse->assertJsonFragment(['name' => $this->tag->name]);
    }
}
