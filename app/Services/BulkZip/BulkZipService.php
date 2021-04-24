<?php

namespace App\Services\BulkZip;

use App\Jobs\BulkZip\JobCreateBulkZip;
use App\Jobs\BulkZip\JobDeleteExpiredBulkzip;
use App\Models\BulkZip;
use App\Repositories\BulkZipRepository;
use App\Services\Service;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class BulkZipService extends Service
{
    private BulkZipRepository $bulkZipRepository;

    public function __construct(BulkZipRepository $bulkZipRepository)
    {
        $this->bulkZipRepository = $bulkZipRepository;
    }

    public function findOrCreate(Model $model): BulkZip
    {
        if (!method_exists($model, 'bulkZippable')) {
            throw new ModelNotFoundException('invalid model provided: '.get_class($model));
        }

        $bulkZip = $this->bulkZipRepository->findByBulkZippable($model);
        if (is_null($bulkZip)) {
            $bulkZip = $this->bulkZipRepository->storeByBulkZippable($model);
            JobCreateBulkZip::dispatchAfterResponse($bulkZip);
            JobDeleteExpiredBulkzip::dispatchAfterResponse();
        }

        return $bulkZip;
    }
}
