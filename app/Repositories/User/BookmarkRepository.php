<?php

namespace App\Repositories\User;

use App\Models\User\Bookmark;
use App\Repositories\BaseRepository;
use Illuminate\Pagination\LengthAwarePaginator;

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

    public function paginatePublic(): LengthAwarePaginator
    {
        // return $this->model
        //     ->where('is_public', true)
        //     ->has('bookmarkItems')
        //     ->with(['user'])
        //     ->orderBy('updated_at', 'desc')
        //     ->withCount('bookmarkItems')
        //     ->paginate(50);

        return $this->model
            ->distinct()
            ->select('bookmarks.*')
            ->where('is_public', true)
            ->leftJoin('bookmark_items', 'bookmarks.id', '=', 'bookmark_items.bookmark_id')
            ->whereNotNull('bookmark_items.bookmark_itemable_id')
            ->with(['user'])
            ->orderBy('updated_at', 'desc')
            ->withCount('bookmarkItems')
            ->paginate(50);
    }

    public function findOrFailByUuid(string $uuid): Bookmark
    {
        return $this->model
            ->where('is_public', true)
            ->where('uuid', $uuid)
            ->with(['user', 'bookmarkItems.bookmarkItemables'])
            ->firstOrFail();
    }
}
