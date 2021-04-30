<?php

namespace App\Repositories\User;

use App\Models\User\Bookmark;
use App\Models\User\BookmarkItem;
use App\Repositories\BaseRepository;
use Illuminate\Support\Collection;

class BookmarkItemRepository extends BaseRepository
{
    /**
     * @var Bookmark
     */
    protected $model;

    public function __construct(Bookmark $model)
    {
        $this->model = $model;
    }

    public function add(Bookmark $bookmark, array $data): BookmarkItem
    {
        return $bookmark->bookmarkItems()->create($data);
    }

    public function exists(Bookmark $bookmark, string $bookmarkItemableType, int $bookmarkItemableId): bool
    {
        return $bookmark->bookmarkItems()
            ->where('bookmark_itemable_type', $bookmarkItemableType)
            ->where('bookmark_itemable_id', $bookmarkItemableId)
            ->exists();
    }

    public function deleteByBookmark(Bookmark $bookmark): void
    {
        $bookmark->bookmarkItems()->delete();
    }

    public function findAllByBookmark(Bookmark $bookmark): Collection
    {
        return $bookmark
            ->bookmarkItems()
            ->get();
    }
}
