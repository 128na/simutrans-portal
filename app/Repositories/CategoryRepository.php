<?php

namespace App\Repositories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Support\Collection;

class CategoryRepository extends BaseRepository
{
    /**
     * @var Category
     */
    protected $model;

    public function __construct(Category $model)
    {
        $this->model = $model;
    }

    /**
     * ユーザーが利用できるカテゴリ一覧を返す.
     */
    public function findAllByUser(User $user, array $column = ['*'], array $with = []): Collection
    {
        return $this->model->forUser($user)->get();
    }

    public function findOrFailByTypeAndSlug(string $type, string $slug): Category
    {
        return $this->model
            ->select('id', 'slug', 'type')
            ->type($type)
            ->slug($slug)
            ->firstOrFail();
    }
}
