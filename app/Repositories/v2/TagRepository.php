<?php

declare(strict_types=1);

namespace App\Repositories\v2;

use App\Models\Tag;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

final class TagRepository
{
    public function __construct(public Tag $model) {}

    /**
     * @return Collection<int,Tag>
     */
    public function getForSearch(): Collection
    {
        return $this->model->query()
            ->select(['tags.id', 'tags.name'])
            ->orderBy('name', 'asc')
            ->get();
    }
    /**
     * @return Collection<int,Tag>
     */
    public function getForEdit(): Collection
    {
        return $this->model->query()
            ->select('tags.*', DB::raw('COUNT(at.article_id) AS articles_count'))
            ->leftJoin('article_tag as at', 'tags.id', '=', 'at.tag_id')
            ->groupBy('tags.id')
            ->with('createdBy:id,name', 'lastModifiedBy:id,name')
            ->get();
    }

    public function load(Tag $tag): Tag
    {
        return $tag
            ->loadCount('articles')
            ->loadMissing('createdBy:id,name', 'lastModifiedBy:id,name')
        ;
    }
    /**
     * @param {name:string,description:string|null, last_modified_at:Carbon\Carbon, last_modified_by:int}
     */
    public function store(array $data): Tag
    {
        return $this->model->create($data);
    }
    /**
     * @param {description:string|null, last_modified_at:Carbon\Carbon, last_modified_by:int}
     */
    public function update(Tag $tag, array $data): Tag
    {
        return tap($tag, fn($t) => $t->update($data));
    }
}
