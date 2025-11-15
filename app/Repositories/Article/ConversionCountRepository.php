<?php

declare(strict_types=1);

namespace App\Repositories\Article;

use App\Repositories\BaseCountRepository;

final class ConversionCountRepository extends BaseCountRepository
{
    #[\Override]
    public function getTableName(): string
    {
        return 'conversion_counts';
    }
}
