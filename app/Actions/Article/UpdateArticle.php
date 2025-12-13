<?php

declare(strict_types=1);

namespace App\Actions\Article;

use App\Actions\Redirect\AddRedirect;
use App\Enums\ArticleStatus;
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

    /**
     * @param  array{should_notify?:bool,without_update_modified_at?:bool,follow_redirect?:bool,article:array{status:string,title:string,slug:string,post_type:string,published_at?:string,contents:mixed}}  $data
     */
    public function __invoke(Article $article, array $data): Article
    {
        $notYetPublished = is_null($article->published_at);
        $withoutUpdateModifiedAt = $data['without_update_modified_at'] ?? false;
        $followRedirect = $data['follow_redirect'] ?? false;
        if ($followRedirect) {
            $oldSlug = $article->slug;
        }

        $articleStatus = ArticleStatus::from($data['article']['status']);
        $publishedAt = $data['article']['published_at'] ?? null;

        $newData = [
            'title' => $data['article']['title'],
            'slug' => $data['article']['slug'],
            'status' => $articleStatus,
            'contents' => $data['article']['contents'],
        ];
        if ($article->is_reservation || $this->inactiveToPublish($article, $articleStatus)) {
            $newData['published_at'] = ($this->decidePublishedAt)($publishedAt, $articleStatus);
        }

        if ($this->shouldUpdateModifiedAt($withoutUpdateModifiedAt)) {
            $newData['modified_at'] = $this->now->toDateTimeString();
        }

        $this->articleRepository->update($article, $newData);

        ($this->syncRelatedModels)($article, $data);

        if ($followRedirect && $oldSlug !== $data['article']['slug']) {
            ($this->addRedirect)($article->user, $oldSlug, $data['article']['slug']);
        }

        dispatch(new \App\Jobs\Article\JobUpdateRelated($article->id));

        $shouldNotify = ($data['should_notify'] ?? false) && ! $withoutUpdateModifiedAt;
        event(new \App\Events\Article\ArticleUpdated($article, $shouldNotify, $notYetPublished));

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
