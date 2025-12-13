<?php

declare(strict_types=1);

namespace Tests\Feature\Repositories\TagRepository;

use App\Models\Article;
use App\Models\Tag;
use App\Repositories\TagRepository;
use Tests\Feature\TestCase;

class GetForEditTest extends TestCase
{
    private TagRepository $tagRepository;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->tagRepository = app(TagRepository::class);
    }

    public function test(): void
    {
        $tag = Tag::factory()->create();
        $article = Article::factory()->create();
        $article->tags()->attach($tag->id);

        $tags = $this->tagRepository->getForEdit();

        $this->assertNotEmpty($tags);
        $first = $tags->first();
        $this->assertArrayHasKey('articles_count', $first->toArray());
    }
}
