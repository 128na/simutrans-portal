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
            ->orderBy('tags.name', 'asc')
            ->with('createdBy:id,name', 'lastModifiedBy:id,name')
            ->get();
    }
}
