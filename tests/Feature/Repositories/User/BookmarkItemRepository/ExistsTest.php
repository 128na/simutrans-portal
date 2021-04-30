<?php

namespace Tests\Feature\Repositories\User\BookmarkItemRepository;

use App\Models\User;
use App\Models\User\Bookmark;
use App\Models\User\BookmarkItem;
use App\Repositories\User\BookmarkItemRepository;
use Tests\TestCase;

class ExistsTest extends TestCase
{
    private BookmarkItemRepository $bookmarkItemRepository;
    private Bookmark $bookmark1;
    private Bookmark $bookmark2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->bookmarkItemRepository = app(BookmarkItemRepository::class);
        $this->bookmark1 = Bookmark::factory()->create();
        BookmarkItem::factory()->create([
            'bookmark_id' => $this->bookmark1->id,
            'bookmark_itemable_type' => User::class,
            'bookmark_itemable_id' => $this->user->id,
            'memo' => 'test memo',
            'order' => 334,
        ]);
        $this->bookmark2 = Bookmark::factory()->create();
    }

    public function test_true()
    {
        $res = $this->bookmarkItemRepository->exists($this->bookmark1, User::class, $this->user->id);
        $this->assertTrue($res);
    }

    public function test_false()
    {
        $res = $this->bookmarkItemRepository->exists($this->bookmark2, User::class, $this->user->id);
        $this->assertFalse($res);
    }
}
