<?php

namespace App\Services;

use App\Models\User;
use App\Models\User\Bookmark;
use App\Repositories\User\BookmarkItemRepository;
use App\Repositories\User\BookmarkRepository;
use DB;
use Illuminate\Support\Collection;

class BookmarkService extends Service
{
    private BookmarkRepository $bookmarkRepository;
    private BookmarkItemRepository $bookmarkItemRepository;

    public function __construct(
        BookmarkRepository $bookmarkRepository,
        BookmarkItemRepository $bookmarkItemRepository
    ) {
        $this->bookmarkRepository = $bookmarkRepository;
        $this->bookmarkItemRepository = $bookmarkItemRepository;
    }

    public function findAllByUser(User $user): Collection
    {
        return $this->bookmarkRepository->findAllByUser($user);
    }

    public function store(User $user, array $dataBookmark): Bookmark
    {
        return $this->bookmarkRepository->storeByUser($user, $dataBookmark);
    }

    public function delete(Bookmark $bookmark): void
    {
        $this->bookmarkRepository->delete($bookmark);
    }

    public function update(Bookmark $bookmark, array $dataBookmark, array $dataBookmarkItems): void
    {
        DB::transaction(function () use ($bookmark, $dataBookmark, $dataBookmarkItems) {
            $this->bookmarkRepository->update($bookmark, $dataBookmark);

            $this->bookmarkItemRepository->deleteByBookmark($bookmark);
            foreach ($dataBookmarkItems as $dataBookmarkItem) {
                $this->bookmarkItemRepository->add($bookmark, $dataBookmarkItem);
            }
        });
    }
}
