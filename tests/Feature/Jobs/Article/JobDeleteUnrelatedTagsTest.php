<?php

declare(strict_types=1);

namespace Tests\Feature\Jobs\Article;

use App\Jobs\Article\JobDeleteUnrelatedTags;
use App\Models\Tag;
use Tests\Feature\TestCase;

class JobDeleteUnrelatedTagsTest extends TestCase
{
    public function testTag(): void
    {
        $tag = Tag::factory()->create();

        $this->assertDatabaseHas('tags', [
            'name' => $tag->name,
        ]);

        JobDeleteUnrelatedTags::dispatchSync();

        $this->assertDatabaseMissing('tags', [
            'name' => $tag->name,
        ]);
    }
}
