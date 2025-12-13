<?php

declare(strict_types=1);

namespace Tests\Feature\Repositories\TagRepository;

use App\Models\Tag;
use App\Repositories\TagRepository;
use Tests\Feature\TestCase;

class GetForSearchTest extends TestCase
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
        Tag::factory()->create(['name' => 'aaa']);
        Tag::factory()->create(['name' => 'bbb']);

        $tags = $this->tagRepository->getForSearch();

        $this->assertCount(2, $tags);
        $this->assertSame(['aaa', 'bbb'], $tags->pluck('name')->all());
    }
}
