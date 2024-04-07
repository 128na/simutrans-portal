<?php

declare(strict_types=1);

namespace App\Repositories\Article;

use App\Models\Article\ConversionCount;
use App\Repositories\BaseCountRepository;

/**
 * @extends BaseCountRepository<ConversionCount>
 */
final class ConversionCountRepository extends BaseCountRepository
{
    public function __construct(ConversionCount $conversionCount)
    {
        parent::__construct($conversionCount);
    }

    public function getTableName(): string
    {
        return 'conversion_counts';
    }
}
