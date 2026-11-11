<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Enums\CategoryType;
use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

class CategoryRepository
{
    public function __construct(public Category $model) {}

    /**
     * @return Collection<int,Category>
     */
    public function getForSearch(): Collection
    {
        return $this->model->query()
            ->select(['categories.id', 'categories.type', 'categories.slug', 'categories.need_admin'])
            ->orderBy('order', 'asc')
            ->get();
    }

    public function getByTypeSlug(CategoryType $categoryType, string $slug): Category
    {
        return $this->model->query()
            ->where('type', $categoryType->value)
            ->where('slug', $slug)
            ->firstOrFail();
    }

    /**
     * @param  array<int,string>  $slugs
     * @return Collection<int,Category>
     */
    public function getByExcludeTypeSlug(CategoryType $categoryType, array $slugs): Collection
    {
        return $this->model->query()
            ->where('type', $categoryType->value)
            ->whereNotIn('slug', $slugs)
            ->orderBy('order', 'asc')
            ->get();
    }
}
