<?php

declare(strict_types=1);

namespace App\Services\BulkZip;

use App\Models\BulkZip;
use App\Models\User;
use App\Repositories\ArticleRepository;
use Exception;

final readonly class ZippableManager
{
    public function __construct(private ArticleRepository $articleRepository) {}

    /**
     * @return array<\App\Models\Article>
     */
    public function getItems(BulkZip $bulkZip): array
    {
        return match (true) {
            $bulkZip->bulkZippable instanceof User => $this->getUserItems($bulkZip->bulkZippable),
            default => throw new Exception('unsupport type provided:' . $bulkZip->bulk_zippable_type, 1),
        };
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
