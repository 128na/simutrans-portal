<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Services\BulkZip\BulkZipService;
use Storage;

class BulkZipController extends Controller
{
    private BulkZipService $bulkZipService;

    public function __construct(BulkZipService $bulkZipService)
    {
        $this->bulkZipService = $bulkZipService;
    }

    public function download(string $uuid)
    {
        $bulkZip = $this->bulkZipService->findOrFail($uuid);
        $disk = Storage::disk('public');
        $path = $disk->path($bulkZip->path);
        $name = sprintf('%s %s.zip', config('app.name'), $bulkZip->uuid);

        return response()->download($path, $name);
    }
}
