<?php

declare(strict_types=1);

namespace App\Actions\Article;

use App\Actions\Article\Data\UpdateArticleData;
use App\Actions\Redirect\AddRedirect;
use App\Enums\ArticleStatus;
use App\Events\Article\ArticleUpdated;
use App\Jobs\Article\JobUpdateRelated;
use App\Models\Article;
use App\Repositories\ArticleRepository;
use Carbon\CarbonImmutable;

class UpdateArticle
{
    public function __construct(
        private ArticleRepository $articleRepository,
        private CarbonImmutable $now,
        private DecidePublishedAt $decidePublishedAt,
        private SyncRelatedModels $syncRelatedModels,
        private AddRedirect $addRedirect,
    ) {}

    public function __invoke(Article $article, UpdateArticleData $updateArticleData): Article
    {
        $notYetPublished = is_null($article->published_at);
        $withoutUpdateModifiedAt = $updateArticleData->withoutUpdateModifiedAt;
        $followRedirect = $updateArticleData->followRedirect;
        if ($followRedirect) {
            $oldSlug = $article->slug;
        }

        $articleStatus = ArticleStatus::from($updateArticleData->article->status);

        $newData = [
            'title' => $updateArticleData->article->title,
            'slug' => $updateArticleData->article->slug,
            'status' => $articleStatus,
            'contents' => $updateArticleData->article->contents,
        ];
        if ($article->is_reservation || $this->inactiveToPublish($article, $articleStatus)) {
            $newData['published_at'] = ($this->decidePublishedAt)($updateArticleData->article->publishedAt, $articleStatus);
        }

        if ($this->shouldUpdateModifiedAt($withoutUpdateModifiedAt)) {
            $newData['modified_at'] = $this->now->toDateTimeString();
        }

        $this->articleRepository->update($article, $newData);

        ($this->syncRelatedModels)($article, $updateArticleData->article);

        if ($followRedirect && $oldSlug !== $updateArticleData->article->slug && $article->user) {
            ($this->addRedirect)($article->user, $oldSlug, $updateArticleData->article->slug);
        }

        dispatch(new JobUpdateRelated($article->id));

        $shouldNotify = $updateArticleData->shouldNotify && ! $withoutUpdateModifiedAt;
        event(new ArticleUpdated($article, $shouldNotify, $notYetPublished));

        return $article->fresh() ?? $article;
    }

    /**
     * 初めての公開？
     */
    private function inactiveToPublish(Article $article, ArticleStatus $articleStatus): bool
    {
        return is_null($article->published_at)
            && $article->is_inactive
            && ($articleStatus === ArticleStatus::Publish || $articleStatus === ArticleStatus::Reservation);
    }

    private function shouldUpdateModifiedAt(bool $withoutUpdateModifiedAt): bool
    {
        return ! $withoutUpdateModifiedAt;
    }
}
