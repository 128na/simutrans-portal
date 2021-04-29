<?php

namespace Tests\Feature\Repositories\User\BookmarkRepository;

use App\Models\User\Bookmark;
use App\Repositories\User\BookmarkRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tests\TestCase;

class FindOrFailByUuidTest extends TestCase
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
        $res = $this->bookmarkRepository->findOrFailByUuid($this->bookmark->uuid);

        $this->assertEquals($this->bookmark->id, $res->id);
    }

    public function test_非公開()
    {
        $this->expectException(ModelNotFoundException::class);
        $this->bookmark->update(['is_public' => false]);
        $this->bookmarkRepository->findOrFailByUuid($this->bookmark->uuid);
    }

    public function test_存在しない()
    {
        $this->expectException(ModelNotFoundException::class);
        $this->bookmarkRepository->findOrFailByUuid('not-exixts');
    }
}
