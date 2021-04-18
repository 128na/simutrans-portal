<?php

namespace App\Http\Controllers\Api\v3;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\BulkZipResource;
use App\Models\User\Bookmark;
use App\Repositories\User\BookmarkRepository;
use App\Services\BulkZipService;
use Auth;

class BulkZipController extends Controller
{
    private BulkZipService $bulkZipService;
    private BookmarkRepository $bookmarkRepository;

    public function __construct(BulkZipService $bulkZipService, BookmarkRepository $bookmarkRepository)
    {
        $this->bulkZipService = $bulkZipService;
        $this->bookmarkRepository = $bookmarkRepository;
    }

    public function publicBookmark(string $uuid)
    {
        $bookmark = $this->bookmarkRepository->findOrFailByUuid($uuid, []);
        $bulkZip = $this->bulkZipService->findOrCreate($bookmark);

        return new BulkZipResource($bulkZip);
    }

    public function bookmark(Bookmark $bookmark)
    {
        $this->authorize('download', $bookmark);
        $bulkZip = $this->bulkZipService->findOrCreate($bookmark);

        return new BulkZipResource($bulkZip);
    }

    public function user()
    {
        $bulkZip = $this->bulkZipService->findOrCreate(Auth::user());

        return new BulkZipResource($bulkZip);
    }
}
