<?php

declare(strict_types=1);

namespace App\Actions\Article;

use App\Actions\Article\Data\StoreArticleData;
use App\Enums\ArticlePostType;
use App\Enums\ArticleStatus;
use App\Events\Article\ArticleStored;
use App\Jobs\Article\JobUpdateRelated;
use App\Models\Article;
use App\Models\User;
use App\Repositories\ArticleRepository;
use Carbon\CarbonImmutable;
use LogicException;

class StoreArticle
{
    public function __construct(
        private ArticleRepository $articleRepository,
        private CarbonImmutable $now,
        private DecidePublishedAt $decidePublishedAt,
        private SyncRelatedModels $syncRelatedModels,
    ) {}

    public function __invoke(User $user, StoreArticleData $storeArticleData): Article
    {
        $articleStatus = ArticleStatus::from($storeArticleData->article->status);
        $postType = $storeArticleData->article->postType ?? throw new LogicException('post_type is required to store an article.');
        $newData = [
            'user_id' => $user->id,
            'post_type' => ArticlePostType::from($postType),
            'title' => $storeArticleData->article->title,
            'slug' => $storeArticleData->article->slug,
            'status' => $articleStatus,
            'contents' => $storeArticleData->article->contents,
            'published_at' => ($this->decidePublishedAt)($storeArticleData->article->publishedAt, $articleStatus),
            'modified_at' => $this->now->toDateTimeString(),
        ];
        $article = $this->articleRepository->store($newData);

        ($this->syncRelatedModels)($article, $storeArticleData->article);

        dispatch(new JobUpdateRelated($article->id));
        event(new ArticleStored($article, $storeArticleData->shouldNotify));

        return $article->fresh() ?? $article;
    }
}
