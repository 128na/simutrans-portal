<?php

declare(strict_types=1);

namespace App\Actions\Article;

use App\Enums\ArticlePostType;
use App\Enums\ArticleStatus;
use App\Events\Article\ArticleStored;
use App\Jobs\Article\JobUpdateRelated;
use App\Models\Article;
use App\Models\User;
use Carbon\CarbonImmutable;

final readonly class StoreArticle
{
    public function __construct(
        private CarbonImmutable $now,
        private DecidePublishedAt $decidePublishedAt,
        private SyncRelatedModels $syncRelatedModels,
    ) {

    }

    /**
     * @param  array{should_notify:bool,article:array{status:string,title:string,slug:string,post_type:string,published_at:string,contents:mixed}}  $data
     */
    public function __invoke(User $user, array $data): Article
    {
        $articleStatus = ArticleStatus::from($data['article']['status']);
        $publishedAt = $data['article']['published_at'];
        $newData = [
            'post_type' => ArticlePostType::from($data['article']['post_type']),
            'title' => $data['article']['title'],
            'slug' => $data['article']['slug'],
            'status' => $articleStatus,
            'contents' => $data['article']['contents'],
            'published_at' => ($this->decidePublishedAt)($publishedAt, $articleStatus),
            'modified_at' => $this->now->toDateTimeString(),
        ];
        /** @var Article */
        $article = $user->articles()->create($newData);

        ($this->syncRelatedModels)($article, $data);

        JobUpdateRelated::dispatch();
        ArticleStored::dispatch($article, $data['should_notify']);

        return $article->fresh() ?? $article;
    }
}
