<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Repositories\User\BookmarkRepository;

class PublicBookmarkController extends Controller
{
    private BookmarkRepository $bookmarkRepository;

    public function __construct(BookmarkRepository $bookmarkRepository)
    {
        $this->bookmarkRepository = $bookmarkRepository;
    }

    public function index()
    {
        $items = $this->bookmarkRepository->paginatePublic();
        $data = [
            'items' => $items,
        ];

        return view('front.public-bookmarks.index', $data);
    }

    public function show(string $uuid)
    {
        $item = $this->bookmarkRepository->findOrFailByUuid($uuid);
        $data = [
            'item' => $item,
        ];

        return view('front.public-bookmarks.show', $data);
    }
}
