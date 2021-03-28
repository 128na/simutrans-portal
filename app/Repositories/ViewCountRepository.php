<?php

namespace App\Repositories;

use App\Models\ConversionCount;

class ViewCountRepository extends BaseCountRepository
{
    /**
     * @var ConversionCount
     */
    protected $model;

    public function __construct(ConversionCount $model)
    {
        $this->model = $model;
    }

    public function getTableName(): string
    {
        return 'view_counts';
    }
}
