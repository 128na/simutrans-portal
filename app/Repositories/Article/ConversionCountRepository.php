<?php

declare(strict_types=1);

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

    public function __construct(ViewCount $viewCount)
    {
        $this->model = $viewCount;
    }

    public function getTableName(): string
    {
        return 'conversion_counts';
    }
}
