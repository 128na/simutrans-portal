<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Front;

use App\Models\Tag;
use Tests\Feature\TestCase;

final class TagControllerTest extends TestCase
{
    public function test_tags(): void
    {
        $testResponse = $this->get(route('tags.index'));

        $testResponse->assertOk();
    }

    public function test_tag(): void
    {
        $tag = Tag::factory()->create();
        $testResponse = $this->get(route('tags.show', ['tag' => $tag]));

        $testResponse->assertOk();
    }
}
