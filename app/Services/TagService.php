<?php
namespace App\Services;

use App\Models\Tag;

class TagService extends Service
{
    public function __construct(Tag $model)
    {
        $this->model = $model;
    }

    public function searchTags($name)
    {
        return $this->model->where('name', 'like', "%{$name}%")
            ->orderByRaw('LENGTH(name) asc')
            ->limit($this->per_page)
            ->get()
            ->pluck('name');
    }

    public function getTags()
    {
        return $this->model->limit($this->per_page)
            ->get()
            ->pluck('name');
    }
}
