<?php

declare(strict_types=1);

namespace App\Repositories\v2;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

final class CategoryRepository
{
    public function __construct(public Category $model) {}

    /**
     * @return Collection<int,Category>
     */
    public function getForSearch(): Collection
    {
        return $this->model->query()
            ->select(['categories.id', 'categories.type', 'categories.slug'])
            ->orderBy('order', 'asc')
            ->get();
    }
}
