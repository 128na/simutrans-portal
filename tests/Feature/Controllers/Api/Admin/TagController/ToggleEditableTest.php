<?php

declare(strict_types=1);

namespace Tests\OldFeature\Controllers\Api\Admin;

use App\Enums\UserRole;
use App\Models\Tag;
use Tests\TestCase;

class TagControllerTest extends TestCase
{
    private Tag $tag;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tag = Tag::factory()->create(['editable' => true]);
    }

    public function testtoggleEditable認証(): void
    {
        $url = sprintf('/api/admin/tags/%s/toggleEditable', $this->tag->id);

        $res = $this->postJson($url);
        $res->assertUnauthorized();

        $this->actingAs($this->user);
        $res = $this->postJson($url);
        $res->assertUnauthorized();
    }

    public function testtoggleEditable(): void
    {
        $this->assertDatabaseHas('tags', ['id' => $this->tag->id, 'editable' => 1]);
        $url = sprintf('/api/admin/tags/%s/toggleEditable', $this->tag->id);

        $this->user->update(['role' => UserRole::Admin]);
        $this->actingAs($this->user);

        $testResponse = $this->postJson($url);
        $testResponse->assertOk();
        $this->assertDatabaseHas('tags', ['id' => $this->tag->id, 'editable' => 0]);

        $this->postJson($url);
        $this->assertDatabaseHas('tags', ['id' => $this->tag->id, 'editable' => 1]);
    }
}
