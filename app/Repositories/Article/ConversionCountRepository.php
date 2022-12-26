<?php

namespace App\Repositories\Article;

use App\Models\Article\ViewCount;
use App\Repositories\BaseCountRepository;

/**
 * @extends BaseCountRepository<ViewCount>
 */
class ConversionCountRepository extends BaseCountRepository
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
