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
    public function addWordSearch(Builder $query, string $word): void
    {
        $query->where(function ($q) use ($word) {
            $contents = new FullTextSearchQueryLike('contents');
            $title = new FullTextSearchQueryLike('title');
            if ($title->parse($word) && $contents->parse($word)) {
                $q->whereRaw($title->get_formatted_query());
                $q->orWhereRaw($contents->get_formatted_query());
            }
        });
    }

    public function addCategories(Builder $query, Collection $items, bool $and = true): void
    {
        $query->where(fn ($q) => $items->map(function (Category $item) use ($q, $and) {
            $and
                ? $q->whereHas('categories', fn ($q) => $q->where('id', $item->id))
                : $q->orWhereHas('categories', fn ($q) => $q->where('id', $item->id));
        }));
    }

    public function addTags(Builder $query, Collection $items, bool $and = true): void
    {
        $query->where(fn ($q) => $items->map(function (Tag $item) use ($q, $and) {
            $and
                ? $q->whereHas('tags', fn ($q) => $q->where('id', $item->id))
                : $q->orWhereHas('tags', fn ($q) => $q->where('id', $item->id));
        }));
    }

    public function addUsers(Builder $query, Collection $items, bool $and = true): void
    {
        $query->where(fn ($q) => $items->map(function (User $item) use ($q, $and) {
            $and
                ? $q->whereHas('user', fn ($q) => $q->where('id', $item->id))
                : $q->orWhereHas('user', fn ($q) => $q->where('id', $item->id));
        }));
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
