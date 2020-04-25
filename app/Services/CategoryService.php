<?php
namespace App\Services;

use App\Models\Category;

class CategoryService extends Service
{
    public function __construct(Category $model)
    {
        $this->model = $model;
    }

    public function findOrFailByTypeAndSlug($type, $slug)
    {
        return $this->model
            ->select('id', 'slug', 'type')
            ->type($type)
            ->slug($slug)
            ->firstOrFail();
    }
}
