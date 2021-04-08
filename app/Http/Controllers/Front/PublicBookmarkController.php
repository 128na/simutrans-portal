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
    }

    public function show(string $uuid)
    {
    }
}
