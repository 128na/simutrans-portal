<?php

namespace Tests\Feature\Controllers\Api\v2\Mypage\BookmarkController;

use App\Models\User;
use App\Models\User\Bookmark;
use Tests\TestCase;

class DestroyTest extends TestCase
{
    private Bookmark $bookmark;

    protected function setUp(): void
    {
        parent::setUp();
        $this->bookmark = Bookmark::factory()->create(['user_id' => $this->user->id]);
    }

    public function test未ログイン()
    {
        $url = route('api.v2.bookmarks.destroy', $this->bookmark);
        $response = $this->deleteJson($url);
        $response->assertStatus(401);
    }

    public function testメール未認証()
    {
        $this->user->update(['email_verified_at' => null]);
        $this->actingAs($this->user);

        $url = route('api.v2.bookmarks.destroy', $this->bookmark);
        $response = $this->deleteJson($url);
        $response->assertStatus(403);
    }

    public function test他人のはNG()
    {
        $this->actingAs(User::factory()->create());

        $url = route('api.v2.bookmarks.destroy', $this->bookmark);
        $response = $this->deleteJson($url);
        $response->assertStatus(403);
    }

    public function test最後の一つはNG()
    {
        $this->bookmark->delete();
        $this->actingAs($this->user);

        $defaultBookmark = $this->user->bookmarks()->first();
        $url = route('api.v2.bookmarks.destroy', $defaultBookmark);
        $response = $this->deleteJson($url);
        $response->assertStatus(403);
    }

    public function testOK()
    {
        $this->actingAs($this->user);

        $url = route('api.v2.bookmarks.destroy', $this->bookmark);
        $response = $this->deleteJson($url);
        $response->assertOk();
    }
}
