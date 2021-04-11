<?php

namespace Tests\Feature\Controllers\Front\PublicBookmarkController;

use App\Models\User\Bookmark;
use Tests\TestCase;

class IndexTest extends TestCase
{
    public function test()
    {
        $bookmark = Bookmark::factory()->create(['is_public' => true]);
        $url = route('publicBookmarks.index');
        $response = $this->get($url);
        $response->assertOk();
        $response->assertSeeText($bookmark->title);
    }

    public function test非公開()
    {
        $bookmark = Bookmark::factory()->create(['is_public' => false]);
        $url = route('publicBookmarks.index');
        $response = $this->get($url);
        $response->assertOk();
        $response->assertDontSeeText($bookmark->title);
    }

    public function test削除済みユーザー()
    {
        $bookmark = Bookmark::factory()->create(['is_public' => true]);
        $bookmark->user->delete();
        $url = route('publicBookmarks.index');
        $response = $this->get($url);
        $response->assertOk();
        $response->assertDontSeeText($bookmark->title);
    }
}
