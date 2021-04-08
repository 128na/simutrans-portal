<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\BookmarkItem\StoreRequest;
use App\Models\User\Bookmark;
use App\Repositories\User\BookmarkItemRepository;

class BookmarkItemController extends Controller
{
    private BookmarkItemRepository $bookmarkItemRepository;

    public function __construct(BookmarkItemRepository $bookmarkItemRepository)
    {
        $this->bookmarkItemRepository = $bookmarkItemRepository;
    }

    public function store(Bookmark $bookmark, StoreRequest $request)
    {
        $this->authorize('update', $bookmark);

        $validated = $request->validated();

        $this->bookmarkItemRepository->addItem($bookmark, $validated['bookmarkItem']);

        return redirect()->intended();
    }
}
