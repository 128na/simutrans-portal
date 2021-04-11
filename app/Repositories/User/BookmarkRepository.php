<?php

namespace App\Repositories\User;

use App\Models\User;
use App\Models\User\Bookmark;
use App\Repositories\BaseRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

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
        return $this->model
            ->where('is_public', true)
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
            ->with(['user', 'bookmarkItems.bookmarkItemable'])
            ->firstOrFail();
    }

    public function findAllByUser(User $user): Collection
    {
        return $user->bookmarks()
            ->orderBy('created_at', 'desc')
            ->with(['bookmarkItems.bookmarkItemable'])
            ->get();
    }
}
