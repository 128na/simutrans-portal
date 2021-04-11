<?php

namespace App\Http\Controllers\Api\v2\Mypage;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Bookmark\StoreRequest;
use App\Http\Requests\Api\Bookmark\UpdateRequest;
use App\Http\Resources\Api\Mypage\BookmarkResource;
use App\Models\User\Bookmark;
use App\Services\BookmarkService;
use Illuminate\Support\Facades\Auth;

class BookmarkController extends Controller
{
    private BookmarkService $bookmarkService;

    public function __construct(BookmarkService $bookmarkService)
    {
        $this->bookmarkService = $bookmarkService;
    }

    public function index()
    {
        $items = $this->bookmarkService->findAllByUser(Auth::user());

        return BookmarkResource::collection($items);
    }

    public function store(StoreRequest $request)
    {
        $validated = $request->validated();
        $this->bookmarkService->store(Auth::user(), $validated['bookmark']);

        return $this->index();
    }

    public function update(UpdateRequest $request, Bookmark $bookmark)
    {
        $this->authorize('update', $bookmark);
        $validated = $request->validated();
        $this->bookmarkService->update($bookmark, $validated['bookmark'], $validated['bookmarkItems'] ?? []);

        return $this->index();
    }

    public function destroy(Bookmark $bookmark)
    {
        $this->authorize('destroy', $bookmark);
        $this->bookmarkService->delete($bookmark);

        return $this->index();
    }
}
