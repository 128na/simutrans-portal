<?php

declare(strict_types=1);

namespace App\Repositories\Article;

use App\Models\Article\ViewCount;
use App\Repositories\BaseCountRepository;

/**
 * @extends BaseCountRepository<ViewCount>
 */
class ViewCountRepository extends BaseCountRepository
{
    public function __construct(ViewCount $viewCount)
    {
        parent::__construct($viewCount);
    }

    public function getTableName(): string
    {
        return 'view_counts';
    }
}
