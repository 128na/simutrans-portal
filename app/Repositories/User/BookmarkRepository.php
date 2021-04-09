<?php

namespace App\Repositories\User;

use App\Models\User\Bookmark;
use App\Repositories\BaseRepository;

class BookmarkRepository extends BaseRepository
{
    /**
     * @var Bookmark
     */
    protected $model;

    public function __construct(Bookmark $model)
    {
        $this->model = $model;
    }
}
