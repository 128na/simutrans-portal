<?php

declare(strict_types=1);

namespace App\Repositories\Article;

use App\Models\Article\ConversionCount;
use App\Repositories\BaseCountRepository;

/**
 * @extends BaseCountRepository<ConversionCount>
 */
class ViewCountRepository extends BaseCountRepository
{
    /**
     * @var ConversionCount
     */
    protected $model;

    public function __construct(ConversionCount $conversionCount)
    {
        $this->model = $conversionCount;
    }

    public function getTableName(): string
    {
        return 'view_counts';
    }
}
