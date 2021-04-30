<?php

namespace Tests\Feature\Repositories\User\BookmarkRepository;

use App\Models\User\Bookmark;
use App\Repositories\User\BookmarkRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\TestCase;

class PaginatePublicTest extends TestCase
{
    private BookmarkRepository $bookmarkRepository;
    private Bookmark $bookmark;

    protected function setUp(): void
    {
        parent::setUp();

        $this->bookmarkRepository = app(BookmarkRepository::class);
        $this->bookmark = Bookmark::factory()->create(['is_public' => true]);
    }

    public function test()
    {
        $res = $this->bookmarkRepository->paginatePublic();
        $this->assertInstanceOf(LengthAwarePaginator::class, $res);
        $this->assertCount(1, $res);
    }

    public function test_非公開()
    {
        $this->bookmark->update(['is_public' => false]);
        $res = $this->bookmarkRepository->paginatePublic();
        $this->assertInstanceOf(LengthAwarePaginator::class, $res);
        $this->assertCount(0, $res);
    }
}
