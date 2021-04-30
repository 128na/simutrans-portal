<?php

namespace Tests\Feature\Repositories\User\BookmarkItemRepository;

use App\Models\User;
use App\Models\User\Bookmark;
use App\Models\User\BookmarkItem;
use App\Repositories\User\BookmarkItemRepository;
use Tests\TestCase;

class DeleteByBookmarkTest extends TestCase
{
    private BookmarkItemRepository $bookmarkItemRepository;
    private Bookmark $bookmark;

    protected function setUp(): void
    {
        parent::setUp();

        $this->bookmarkItemRepository = app(BookmarkItemRepository::class);
        $this->bookmark = Bookmark::factory()->create();
        BookmarkItem::factory()->create([
            'bookmark_id' => $this->bookmark->id,
            'bookmark_itemable_type' => User::class,
            'bookmark_itemable_id' => $this->user->id,
            'memo' => 'test memo',
            'order' => 334,
        ]);
    }

    public function test()
    {
        $this->assertDatabaseHas('bookmark_items', [
            'bookmark_itemable_type' => User::class,
            'bookmark_itemable_id' => $this->user->id,
            'memo' => 'test memo',
            'order' => 334,
        ]);

        $this->bookmarkItemRepository->deleteByBookmark($this->bookmark);

        $this->assertDatabaseMissing('bookmark_items', [
            'bookmark_itemable_type' => User::class,
            'bookmark_itemable_id' => $this->user->id,
        ]);
    }
}
