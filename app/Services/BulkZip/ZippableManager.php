<?php

namespace App\Services\BulkZip;

use App\Models\BulkZip;
use App\Models\User;
use App\Models\User\Bookmark;
use App\Services\Service;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class ZippableManager extends Service
{
    private const BOOKMARK_DEPTH_LIMIT = 2;

    public function getItems(BulkZip $model): array
    {
        switch ($model->bulk_zippable_type) {
            case User::class:
                return $this->getUserItems($model->bulkZippable);
            case Bookmark::class:
                return $this->getBookmarkItems($model->bulkZippable);
        }
        throw new Exception("unsupport type provided:{$model->bulk_zippable_type}", 1);
    }

    public function getUserItems(User $model): array
    {
        return $model
            ->articles()
            ->get()
            ->load(['categories', 'tags', 'attachments', 'user'])
            ->all();
    }

    public function getBookmarkItems(Bookmark $model, int $depth = 0): array
    {
        return $model
            ->bookmarkItems()
            ->get()
            ->loadMorph('bookmarkItemable', [
                Article::class => ['categories', 'tags', 'attachments', 'user'],
                Bookmark::class => ['bookmarkItems'],
            ])
            ->pluck('bookmarkItemable')
            ->reduce(fn (Collection $result, Model $model) => get_class($model) === Bookmark::class && $depth < self::BOOKMARK_DEPTH_LIMIT
                ? $result->merge($this->getBookmarkItems($model, $depth + 1))
                : $result->merge([$model]), collect([]))
            ->unique()
            ->all();
    }
}
