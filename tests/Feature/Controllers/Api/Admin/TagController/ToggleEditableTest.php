<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Admin\TagController;

use App\Enums\UserRole;
use App\Models\Tag;
use App\Models\User;
use Tests\Feature\TestCase;

final class ToggleEditableTest extends TestCase
{
    private User $user;

    private Tag $tag;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->admin()->create();
        $this->tag = Tag::factory()->create(['editable' => true]);
    }

    public function test_未ログイン(): void
    {
        $url = sprintf('/api/admin/tags/%s/toggleEditable', $this->tag->id);

        $testResponse = $this->postJson($url);
        $testResponse->assertUnauthorized();
    }

    public function test_管理者以外(): void
    {
        $url = sprintf('/api/admin/tags/%s/toggleEditable', $this->tag->id);

        $this->user->update(['role' => UserRole::User]);

        $this->actingAs($this->user);
        $testResponse = $this->postJson($url);
        $testResponse->assertUnauthorized();
    }

    public function test_管理者(): void
    {
        $url = sprintf('/api/admin/tags/%s/toggleEditable', $this->tag->id);

        $this->actingAs($this->user);
        $testResponse = $this->postJson($url);
        $testResponse->assertOk();

        $tag = Tag::findOrFail($this->tag->id);
        $this->assertFalse($tag->editable);

        $testResponse = $this->postJson($url);
        $testResponse->assertOk();

        $tag = Tag::findOrFail($this->tag->id);
        $this->assertTrue($tag->editable);
    }
}
