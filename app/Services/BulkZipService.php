<?php

namespace App\Services;

use App\Jobs\BulkZip\CreateBulkZip;
use App\Models\BulkZip;
use App\Repositories\BulkZipRepository;
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
        if (!method_exists($model, 'bulkZipable')) {
            throw new ModelNotFoundException('invalid model provided: '.get_class($model));
        }

        $bulkZip = $this->bulkZipRepository->findByBulkZippable($model);
        if (is_null($bulkZip)) {
            $bulkZip = $this->bulkZipRepository->storeByBulkZippable($model);
            CreateBulkZip::dispatchAfterResponse($bulkZip);
        }

        return $bulkZip;
    }

    public function findOrFail(string $uuid): BulkZip
    {
        return $this->bulkZipRepository->findOrFailByUuid($uuid);
    }

    public function createZip(BulkZip $bulkZip): void
    {
        // create
        $path = $this->create();

        $this->bulkZipRepository->update($bulkZip, ['generated' => true, 'path' => $path]);
    }

    private function create(): string
    {
        return '';
    }
}
