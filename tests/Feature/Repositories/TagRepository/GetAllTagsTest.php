<?php

declare(strict_types=1);

namespace Tests\Feature\Repositories\TagRepository;

use App\Models\Article;
use App\Models\Tag;
use App\Repositories\TagRepository;
use Tests\Feature\TestCase;

final class GetAllTagsTest extends TestCase
{
    private TagRepository $tagRepository;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->tagRepository = app(TagRepository::class);
    }

    public function test_記事に紐づき記事数の多い順(): void
    {
        Tag::factory()->create();
        $tag1 = Tag::factory()->create();
        $tag2 = Tag::factory()->create();
        tap(Article::factory()->publish()->create(), fn (Article $a) => $a->tags()->save($tag2));
        tap(Article::factory()->publish()->create(), fn (Article $a) => $a->tags()->saveMany([$tag1, $tag2]));
        $result = $this->tagRepository->getAllTags();

        $this->assertCount(2, $result);
        $this->assertSame($tag2->id, $result[0]->id);
        $this->assertSame($tag1->id, $result[1]->id);
    }
}
