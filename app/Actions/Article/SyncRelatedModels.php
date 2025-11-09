<?php

declare(strict_types=1);

namespace App\Actions\Article;

use App\Models\Article;
use App\Repositories\ArticleRepository;

final readonly class SyncRelatedModels
{
    public function __construct(
        private ArticleRepository $articleRepository,
    ) {}

    /**
     * @param  array<mixed>  $data
     */
    public function __invoke(Article $article, array $data): void
    {
        /** @var int[] */
        $ids = data_get($data, 'article.contents.sections.*.id', []);

        /** @var int[] */
        $attachmentIds = collect([
            data_get($data, 'article.contents.thumbnail'),
            data_get($data, 'article.contents.file'),
            ...$ids,
        ])
            ->filter()
            ->toArray();

        $this->articleRepository->syncAttachments($article, $attachmentIds);
        /** @var int[] */
        $articleIds = data_get($data, 'article.articles', []);
        $this->articleRepository->syncArticles($article, $articleIds);

        /** @var int[] */
        $categoryIds = data_get($data, 'article.categories', []);
        $this->articleRepository->syncCategories($article, $categoryIds);

        /** @var int[] */
        $tagIds = data_get($data, 'article.tags', []);
        $this->articleRepository->syncTags($article, $tagIds);
    }
}
