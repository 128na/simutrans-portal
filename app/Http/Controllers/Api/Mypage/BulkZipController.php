<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Mypage;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\BulkZipResource;
use App\Services\BulkZip\BulkZipService;
use Auth;
use Illuminate\Http\JsonResponse;

class BulkZipController extends Controller
{
    public function __construct(private BulkZipService $bulkZipService)
    {
    }

    public function user(): JsonResponse
    {
        /**
         * @var \App\Models\User
         */
        $user = Auth::user();
        $bulkZip = $this->bulkZipService->findOrCreateAndDispatch($user);

        return response()->json(new BulkZipResource($bulkZip), 200);
    }
}
