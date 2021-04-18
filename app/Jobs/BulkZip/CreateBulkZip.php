<?php

namespace App\Jobs\BulkZip;

use App\Models\BulkZip;
use App\Services\BulkZipService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class CreateBulkZip implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private BulkZip $bulkZip;

    public function __construct(BulkZip $bulkZip)
    {
        $this->bulkZip = $bulkZip;
    }

    public function handle(BulkZipService $bulkZipService)
    {
        logger('CreateBulkZip::handle', $this->bulkZip->toArray());
        $bulkZipService->createZip($this->bulkZip);
    }

    public function failed(Throwable $exception)
    {
        $this->bulkZip->delete();
    }
}
