<?php

declare(strict_types=1);

namespace Tests\Feature\Repositories\TagRepository;

use App\Models\Tag;
use App\Repositories\TagRepository;
use Tests\Feature\TestCase;

final class GetTagsTest extends TestCase
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
        $result = $this->tagRepository->getTags();

        $this->assertCount(1, $result);
        $this->assertSame($tag->id, $result[0]->id);
    }
}
