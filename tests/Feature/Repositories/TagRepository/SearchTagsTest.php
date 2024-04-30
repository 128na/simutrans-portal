<?php

declare(strict_types=1);

namespace Tests\Feature\Repositories\TagRepository;

use App\Models\Tag;
use App\Repositories\TagRepository;
use Tests\Feature\TestCase;

final class SearchTagsTest extends TestCase
{
    private TagRepository $tagRepository;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->tagRepository = app(TagRepository::class);
    }

    public function test_マッチして文字数の少ない順(): void
    {
        Tag::factory()->create(['name' => 'dummy', 'description' => 'dummy']);
        $tag1 = Tag::factory()->create(['name' => 'foobarbaz', 'description' => 'dummy']);
        $tag2 = Tag::factory()->create(['name' => 'dummy', 'description' => 'foobarbaz']);
        $result = $this->tagRepository->searchTags('foo');

        $this->assertCount(2, $result);
        $this->assertSame($tag2->id, $result[0]->id);
        $this->assertSame($tag1->id, $result[1]->id);
    }
}
