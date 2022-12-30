<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Admin;

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

    public function testtoggleEditable認証()
    {
        $url = "/api/admin/tags/{$this->tag->id}/toggleEditable";

        $res = $this->postJson($url);
        $res->assertUnauthorized();

        $this->actingAs($this->user);
        $res = $this->postJson($url);
        $res->assertUnauthorized();
    }

    public function testtoggleEditable()
    {
        $this->assertDatabaseHas('tags', ['id' => $this->tag->id, 'editable' => 1]);
        $url = "/api/admin/tags/{$this->tag->id}/toggleEditable";

        $this->user->update(['role' => 'admin']);
        $this->actingAs($this->user);

        $res = $this->postJson($url);
        $res->assertOk();
        $this->assertDatabaseHas('tags', ['id' => $this->tag->id, 'editable' => 0]);

        $this->postJson($url);
        $this->assertDatabaseHas('tags', ['id' => $this->tag->id, 'editable' => 1]);
    }
}
