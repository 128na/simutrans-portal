<?php

declare(strict_types=1);

namespace App\Services\BulkZip;

use App\Contracts\Models\BulkZippableInterface;
use App\Jobs\BulkZip\JobCreateBulkZip;
use App\Jobs\BulkZip\JobDeleteExpiredBulkzip;
use App\Models\BulkZip;
use App\Repositories\BulkZipRepository;
use App\Services\Service;

class BulkZipService extends Service
{
    public function __construct(private readonly BulkZipRepository $bulkZipRepository)
    {
    }

    public function findOrCreateAndDispatch(BulkZippableInterface $model): BulkZip
    {
        $bulkZip = $this->bulkZipRepository->findByBulkZippable($model);
        if (is_null($bulkZip)) {
            $bulkZip = $this->bulkZipRepository->storeByBulkZippable($model);
            JobCreateBulkZip::dispatch($bulkZip);
        }
        JobDeleteExpiredBulkzip::dispatch();

        return $bulkZip;
    }
}
