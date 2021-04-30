<?php

namespace Tests\Feature\Repositories\User\BookmarkItemRepository;

use App\Models\User\Bookmark;
use App\Models\User\BookmarkItem;
use App\Repositories\User\BookmarkItemRepository;
use Tests\TestCase;

class AddTest extends TestCase
{
    private BookmarkItemRepository $bookmarkItemRepository;
    private Bookmark $bookmark;

    protected function setUp(): void
    {
        parent::setUp();

        $this->bookmarkItemRepository = app(BookmarkItemRepository::class);
        $this->bookmark = Bookmark::factory()->create();
    }

    public function test()
    {
        $data = [
            'bookmark_itemable_type' => User::class,
            'bookmark_itemable_id' => $this->user->id,
            'memo' => 'test memo',
            'order' => 334,
        ];

        $this->assertDatabaseMissing('bookmark_items', [
            'bookmark_itemable_type' => User::class,
            'bookmark_itemable_id' => $this->user->id,
        ]);
        $res = $this->bookmarkItemRepository->add($this->bookmark, $data);
        $this->assertInstanceOf(BookmarkItem::class, $res);

        $this->assertDatabaseHas('bookmark_items', [
            'bookmark_itemable_type' => User::class,
            'bookmark_itemable_id' => $this->user->id,
            'memo' => 'test memo',
            'order' => 334,
        ]);
    }
}
