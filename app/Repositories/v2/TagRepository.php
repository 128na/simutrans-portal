<?php

declare(strict_types=1);

namespace App\Repositories\v2;

use App\Models\Tag;
use Illuminate\Database\Eloquent\Collection;

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
}
