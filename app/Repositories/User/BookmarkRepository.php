<?php

namespace App\Repositories\User;

use App\Models\User\BookmarkItem;
use App\Repositories\BaseRepository;

class BookmarkItemRepository extends BaseRepository
{
    /**
     * @var BookmarkItem
     */
    protected $model;

    public function __construct(BookmarkItem $model)
    {
        $this->model = $model;
    }
}
