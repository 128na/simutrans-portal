<?php

namespace Tests\Feature\Jobs\Article;

use App\Jobs\Article\JobUpdateRelated;
use App\Models\PakAddonCount;
use App\Models\Tag;
use App\Models\UserAddonCount;
use Tests\TestCase;

class JobUpdateRelatedTest extends TestCase
{
    public function testPakAddonCount()
    {
        PakAddonCount::create([
            'pak_slug' => 'foo', 'addon_slug' => 'bar', 'count' => 334,
        ]);

        $this->assertDatabaseHas('pak_addon_counts', [
            'pak_slug' => 'foo', 'addon_slug' => 'bar', 'count' => 334,
        ]);

        JobUpdateRelated::dispatchSync();

        $this->assertDatabaseMissing('pak_addon_counts', [
            'pak_slug' => 'foo', 'addon_slug' => 'bar', 'count' => 334,
        ]);
    }

    public function testUserAddonCount()
    {
        UserAddonCount::create([
            'user_id' => $this->user->id, 'user_name' => 'hoge', 'count' => 72,
        ]);

        $this->assertDatabaseHas('user_addon_counts', [
            'user_id' => $this->user->id, 'user_name' => 'hoge', 'count' => 72,
        ]);

        JobUpdateRelated::dispatchSync();

        $this->assertDatabaseMissing('user_addon_counts', [
            'user_id' => $this->user->id, 'user_name' => 'hoge', 'count' => 72,
        ]);
    }

    public function testTag()
    {
        $tag = Tag::factory()->create();

        $this->assertDatabaseHas('tags', [
            'name' => $tag->name,
        ]);

        JobUpdateRelated::dispatchSync();

        $this->assertDatabaseMissing('tags', [
            'name' => $tag->name,
        ]);
    }
}
