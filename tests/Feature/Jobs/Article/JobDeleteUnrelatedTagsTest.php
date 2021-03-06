<?php

namespace Tests\Feature\Jobs\Article;

use App\Jobs\Article\JobDeleteUnrelatedTags;
use App\Models\Tag;
use Tests\TestCase;

class JobDeleteUnrelatedTagsTest extends TestCase
{
    public function testTag()
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
