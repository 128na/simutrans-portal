<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Repositories\BulkZipRepository;
use Storage;

class BulkZipController extends Controller
{
    private BulkZipRepository $bulkZipRepository;

    public function __construct(BulkZipRepository $bulkZipRepository)
    {
        $this->bulkZipRepository = $bulkZipRepository;
    }

    public function download(string $uuid)
    {
        $bulkZip = $this->bulkZipRepository->findOrFailByUuid($uuid);
        $disk = Storage::disk('public');
        $path = $disk->path($bulkZip->path);
        $name = sprintf('%s %s.zip', config('app.name'), $bulkZip->uuid);

        return response()->download($path, $name);
    }
}
