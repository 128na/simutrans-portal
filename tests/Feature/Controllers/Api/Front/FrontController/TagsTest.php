<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Front\FrontController;

use App\Models\Article;
use App\Models\Tag;
use Tests\Feature\TestCase;

final class TagsTest extends TestCase
{
    private Tag $tag;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $article = Article::factory()->publish()->create();
        $this->tag = Tag::factory()->create();
        $article->tags()->save($this->tag);
    }

    public function test(): void
    {
        $testResponse = $this->get('api/front/tags');

        $testResponse->assertOk();
        $testResponse->assertJsonFragment(['name' => $this->tag->name]);
    }
}
