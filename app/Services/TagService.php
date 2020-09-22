<?php
namespace App\Services;

use App\Models\Tag;

class TagService extends Service
{
    public function __construct(Tag $model)
    {
        $this->model = $model;
        $this->per_page = 10;
    }

    public function searchTags($name)
    {
        return $this->model->select('name')->where('name', 'like', "%{$name}%")
            ->orderByRaw('LENGTH(name) asc')
            ->limit($this->per_page)
            ->get()
            ->pluck('name');
    }

    public function getTags()
    {
        return $this->model->select('name')->limit($this->per_page)
            ->get()
            ->pluck('name');
    }

    public function getAllTags()
    {
        return $this->model->select('id', 'name')
            ->whereHas('articles', function ($query) {
                $query->active();
            })
            ->withCount(['articles' => function ($query) {
                $query->active();
            }])
            ->orderBy('articles_count', 'desc')
            ->withCache()
            ->get();
    }
}
