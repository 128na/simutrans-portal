<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\BookmarkItem\StoreRequest;
use App\Repositories\User\BookmarkItemRepository;
use App\Repositories\User\BookmarkRepository;

class BookmarkItemController extends Controller
{
    private BookmarkItemRepository $bookmarkItemRepository;
    private BookmarkRepository $bookmarkRepository;

    public function __construct(
        BookmarkRepository $bookmarkRepository,
        BookmarkItemRepository $bookmarkItemRepository
    ) {
        $this->bookmarkRepository = $bookmarkRepository;
        $this->bookmarkItemRepository = $bookmarkItemRepository;
    }

    public function store(StoreRequest $request)
    {
        $validated = $request->validated();

        $bookmark = $this->bookmarkRepository->findOrFail($validated['bookmarkItem']['bookmark_id']);
        $this->authorize('update', $bookmark);

        if ($this->bookmarkItemRepository->exists(
            $bookmark,
            $validated['bookmarkItem']['bookmark_itemable_type'],
            $validated['bookmarkItem']['bookmark_itemable_id']
        )) {
            session()->flash('error', '既に追加されています');

            return redirect()->back();
        }
        $this->bookmarkItemRepository->add($bookmark, $validated['bookmarkItem']);
        session()->flash('status', 'ブックマークに追加しました');

        return redirect()->back();
    }
}
