<?php

namespace App\Http\Controllers\Api\Mypage;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\BulkZipResource;
use App\Services\BulkZip\BulkZipService;
use Auth;

class BulkZipController extends Controller
{
    public function __construct(private BulkZipService $bulkZipService)
    {
    }

    public function user()
    {
        $bulkZip = $this->bulkZipService->findOrCreateAndDispatch(Auth::user());

        return response(new BulkZipResource($bulkZip), 200);
    }
}
