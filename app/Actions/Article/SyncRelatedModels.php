<?php

declare(strict_types=1);

namespace App\Actions\Article;

use App\Actions\Article\Data\ArticleData;
use App\Models\Article;
use App\Repositories\ArticleRepository;

class SyncRelatedModels
{
    public function __construct(
        private ArticleRepository $articleRepository,
    ) {}

    public function __invoke(Article $article, ArticleData $articleData): void
    {
        /** @var int[] */
        $ids = data_get($articleData->contents, 'sections.*.id', []);

        /** @var int[] */
        $attachmentIds = collect([
            data_get($articleData->contents, 'thumbnail'),
            data_get($articleData->contents, 'file'),
            ...$ids,
        ])
            ->filter()
            ->toArray();

        $this->articleRepository->syncAttachments($article, $attachmentIds);
        $this->articleRepository->syncArticles($article, $articleData->articles);
        $this->articleRepository->syncCategories($article, $articleData->categories);
        $this->articleRepository->syncTags($article, $articleData->tags);
    }
}
