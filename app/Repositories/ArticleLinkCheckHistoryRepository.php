<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Article;
use App\Models\ArticleLinkCheckHistory;

final class ArticleLinkCheckHistoryRepository
{
    public function __construct(
        private ArticleLinkCheckHistory $articleLinkCheckHistory
    ) {}

    public function get(Article $article): int
    {
        $history = $this->articleLinkCheckHistory
            ->where('article_id', $article->id)
            ->first();

        return $history->failed_count ?? 0;
    }

    public function increment(Article $article): void
    {
        $history = $this->articleLinkCheckHistory
            ->where('article_id', $article->id)
            ->first();

        if ($history === null) {
            $this->articleLinkCheckHistory->create([
                'article_id' => $article->id,
                'failed_count' => 1,
                'last_checked_at' => now(),
            ]);
        } else {
            $history->increment('failed_count');
            $history->update(['last_checked_at' => now()]);
        }
    }

    public function clear(Article $article): void
    {
        $this->articleLinkCheckHistory
            ->where('article_id', $article->id)
            ->delete();
    }
}
