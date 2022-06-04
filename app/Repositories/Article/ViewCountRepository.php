<?php

namespace App\Repositories\Article;

use App\Models\Article\ConversionCount;
use App\Repositories\BaseCountRepository;

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
