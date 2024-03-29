<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Enums\CategoryType;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

/**
 * @extends BaseRepository<Category>
 */
class CategoryRepository extends BaseRepository
{
    /**
     * @var Category
     */
    protected $model;

    public function __construct(Category $category)
    {
        $this->model = $category;
    }

    /**
     * ユーザーが利用できるカテゴリ一覧を返す.
     */
    public function findAllByUser(User $user): Collection
    {
        return $this->model->forUser($user)->get();
    }

    public function findOrFailByTypeAndSlug(CategoryType $categoryType, string $slug): Category
    {
        return $this->model
            ->select('id', 'slug', 'type')
            ->type($categoryType)
            ->slug($slug)
            ->firstOrFail();
    }
}
