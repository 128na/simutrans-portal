<?php

namespace App\Repositories;

use App\Models\ViewCount;

class CoversionCountRepository extends BaseCountRepository
{
    /**
     * @var ViewCount
     */
    protected $model;

    public function __construct(ViewCount $model)
    {
        $this->model = $model;
    }

    public function getTableName(): string
    {
        return 'conversion_counts';
    }
}
