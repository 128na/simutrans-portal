<?php

declare(strict_types=1);

namespace App\Repositories\Article;

use App\Repositories\AbstractBaseCountRepository;

final class ViewCountRepository extends AbstractBaseCountRepository
{
    #[\Override]
    public function getTableName(): string
    {
        return 'view_counts';
    }
}
