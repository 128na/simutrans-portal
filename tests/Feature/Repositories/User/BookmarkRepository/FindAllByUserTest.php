<?php

namespace Tests\Feature\Repositories\User\BookmarkRepository;

use App\Models\User\Bookmark;
use App\Repositories\User\BookmarkRepository;
use Illuminate\Support\Collection;
use Tests\TestCase;

class FindAllByUserTest extends TestCase
{
    private BookmarkRepository $bookmarkRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->bookmarkRepository = app(BookmarkRepository::class);
        Bookmark::factory()->create();
    }

    public function test()
    {
        $res = $this->bookmarkRepository->findAllByUser($this->user);
        $this->assertInstanceOf(Collection::class, $res);
        $this->assertCount(1, $res, 'デフォルトブックマークが取得されること');
    }
}
