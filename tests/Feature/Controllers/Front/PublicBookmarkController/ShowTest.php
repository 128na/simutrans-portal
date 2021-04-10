<?php

namespace Tests\Feature\Controllers\Front\PublicBookmarkController;

use App\Models\User\Bookmark;
use Tests\TestCase;

class ShowTest extends TestCase
{
    public function test()
    {
        $bookmark = Bookmark::factory()->create(['is_public' => true]);
        $url = route('publicBookmarks.show', $bookmark->uuid);
        $response = $this->get($url);
        $response->assertOk();
    }

    public function test非公開()
    {
        $bookmark = Bookmark::factory()->create(['is_public' => false]);
        $url = route('publicBookmarks.show', $bookmark->uuid);
        $response = $this->get($url);
        $response->assertNotFound();
    }

    public function test削除済みユーザー()
    {
        $bookmark = Bookmark::factory()->create(['is_public' => true]);
        $bookmark->user->delete();
        $url = route('publicBookmarks.show', $bookmark->uuid);
        $response = $this->get($url);
        $response->assertNotFound();
    }

    public function test404()
    {
        $url = route('publicBookmarks.show', 0);
        $response = $this->get($url);
        $response->assertNotFound();
    }
}
