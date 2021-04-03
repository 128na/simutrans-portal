<?php

namespace App\QueryBuilders;

use App\Models\Category;
use App\Models\Tag;
use App\Models\User;
use Carbon\CarbonImmutable;
use FullTextSearchQueryLike;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class AdvancedSearchQueryBuilder
{
    private FullTextSearchQueryLike $fullTextSearchQueryLike;

    public function __construct(FullTextSearchQueryLike $fullTextSearchQueryLike)
    {
        $this->fullTextSearchQueryLike = $fullTextSearchQueryLike;
    }

    public function addWordSearch(Builder $query, string $word): void
    {
        if ($this->fullTextSearchQueryLike->parse($word)) {
            $query->whereRaw($this->fullTextSearchQueryLike->get_formatted_query());
        }
    }

    public function addCategories(Builder $query, Collection $items, bool $and = true): void
    {
        $items->map(function (Category $item) use ($query, $and) {
            $and
                ? $query->whereHas('categories', fn ($q) => $q->where('id', $item->id))
                : $query->orWhereHas('categories', fn ($q) => $q->where('id', $item->id));
        });
    }

    public function addTags(Builder $query, Collection $items, bool $and = true): void
    {
        $items->map(function (Tag $item) use ($query, $and) {
            $and
                ? $query->whereHas('tags', fn ($q) => $q->where('id', $item->id))
                : $query->orWhereHas('tags', fn ($q) => $q->where('id', $item->id));
        });
    }

    public function addUsers(Builder $query, Collection $items, bool $and = true): void
    {
        $items->map(function (User $item) use ($query, $and) {
            $and
                ? $query->whereHas('user', fn ($q) => $q->where('id', $item->id))
                : $query->orWhereHas('user', fn ($q) => $q->where('id', $item->id));
        });
    }

    public function addStartAt(Builder $query, CarbonImmutable $date): void
    {
        $query->whereDate('updated_at', '>=', $date);
    }

    public function addEndAt(Builder $query, CarbonImmutable $date): void
    {
        $query->whereDate('updated_at', '<=', $date);
    }

    public function addOrder(Builder $query, string $column, string $direction): void
    {
        $query->orderBy($column, $direction);
    }
}
