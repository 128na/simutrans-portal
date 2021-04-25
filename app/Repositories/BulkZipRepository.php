<?php

namespace App\Repositories;

use App\Models\BulkZip;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\LazyCollection;

class BulkZipRepository extends BaseRepository
{
    /**
     * @var BulkZip
     */
    protected $model;

    public function __construct(BulkZip $model)
    {
        $this->model = $model;
    }

    public function findOrFailByUuid(string $uuid): BulkZip
    {
        return $this->model
            ->where('uuid', $uuid)
            ->where('generated', true)
            ->whereNotNull('path')
            ->firstOrFail();
    }

    public function findByBulkZippable(Model $model): ?BulkZip
    {
        return $model->bulkZippable()->first();
    }

    public function storeByBulkZippable(Model $model, array $data = []): BulkZip
    {
        return $model->bulkZippable()->create($data);
    }

    public function cursorExpired(): LazyCollection
    {
        $expiredAt = now()->modify('-1 days');

        return $this->model->whereDate('created_at', '<=', $expiredAt)->cursor();
    }
}
