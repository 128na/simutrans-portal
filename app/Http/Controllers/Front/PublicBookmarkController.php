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

    // TODO 公開ブックマーク一覧取得を実装する
    // TODO 公開ブックマーク一覧画面を実装する
    public function index()
    {
    }

    // TODO 公開ブックマーク取得を実装する
    // TODO 公開ブックマーク詳細画面を実装する
    public function show(string $uuid)
    {
    }
}
