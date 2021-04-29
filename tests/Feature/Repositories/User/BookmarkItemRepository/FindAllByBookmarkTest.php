<?php

namespace Tests\Feature\Repositories\User\BookmarkItemRepository;

use App\Models\User;
use App\Models\User\Bookmark;
use App\Models\User\BookmarkItem;
use App\Repositories\User\BookmarkItemRepository;
use Illuminate\Support\Collection;
use Tests\TestCase;

class FindAllByBookmarkTest extends TestCase
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
        BookmarkItem::factory()->create();
    }

    public function test()
    {
        $res = $this->bookmarkItemRepository->findAllByBookmark($this->bookmark);
        $this->assertInstanceOf(Collection::class, $res);
        $this->assertCount(1, $res);
    }
}
