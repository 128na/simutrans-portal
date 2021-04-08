<?php

namespace App\Repositories\User;

use App\Models\User\Bookmark;
use App\Models\User\BookmarkItem;
use App\Repositories\BaseRepository;

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

    public function addItem(Bookmark $bookmark, array $data): BookmarkItem
    {
        return $bookmark->bookmarkItems()->create($data);
    }
}
