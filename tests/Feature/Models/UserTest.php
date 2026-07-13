<?php

declare(strict_types=1);

namespace Tests\Feature\Models;

use App\Models\Tag;
use App\Models\User;
use Tests\Feature\TestCase;

class UserTest extends TestCase
{
    public function test_last_modified_by_relation_はlast_modified_byカラムで紐づく(): void
    {
        $user = User::factory()->create();
        $tag = Tag::factory()->create(['last_modified_by' => $user->id]);
        Tag::factory()->create(['last_modified_by' => null]);

        $result = $user->lastModifiedBy;

        $this->assertCount(1, $result);
        $this->assertSame($tag->id, $result->first()->id);
    }
}
