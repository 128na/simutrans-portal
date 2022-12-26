<?php

namespace App\Services\BulkZip;

use App\Models\BulkZip;
use App\Models\User;
use App\Repositories\ArticleRepository;
use App\Services\Service;
use Exception;

class ZippableManager extends Service
{
    public function __construct(private ArticleRepository $articleRepository)
    {
    }

    /**
     * @return array<\App\Models\Article>
     */
    public function getItems(BulkZip $model): array
    {
        switch ($model->bulk_zippable_type) {
            case User::class:
                return $this->getUserItems($model->bulkZippable);
        }
        throw new Exception("unsupport type provided:{$model->bulk_zippable_type}", 1);
    }

    /**
     * @return array<\App\Models\Article>
     */
    private function getUserItems(User $user): array
    {
        return $this->articleRepository->findAllByUser($user, [])
            ->load(['categories', 'tags', 'attachments', 'user'])
            ->all();
    }
}
