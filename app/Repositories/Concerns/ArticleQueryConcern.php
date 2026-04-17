<?php

declare(strict_types=1);

namespace App\Repositories\Concerns;

use App\Enums\ArticlePostType;
use App\Enums\ArticleStatus;
use App\Models\Article;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;

/**
 * ArticleRepository 系クラスで共有するクエリビルダーヘルパー
 */
trait ArticleQueryConcern
{
    /**
     * 削除済みユーザーを除外した users JOIN を追加
     *
     * @param  Builder<Article>  $builder
     * @return Builder<Article>
     */
    protected function joinActiveUsers(Builder $builder): Builder
    {
        return $builder->join('users', function (JoinClause $joinClause): void {
            $joinClause->on('users.id', '=', 'articles.user_id')
                ->whereNull('users.deleted_at');
        });
    }

    /**
     * 公開済み記事の基本条件を追加
     *
     * @param  Builder<Article>  $builder
     * @return Builder<Article>
     */
    protected function wherePublished(Builder $builder): Builder
    {
        return $builder->where('articles.status', ArticleStatus::Publish)
            ->whereNull('articles.deleted_at');
    }

    /**
     * アドオン投稿タイプの条件を追加
     *
     * @param  Builder<Article>  $builder
     * @return Builder<Article>
     */
    protected function whereAddonPostTypes(Builder $builder): Builder
    {
        return $builder->whereIn('articles.post_type', [ArticlePostType::AddonIntroduction, ArticlePostType::AddonPost]);
    }

    /**
     * ページ投稿タイプの条件を追加
     *
     * @param  Builder<Article>  $builder
     * @return Builder<Article>
     */
    protected function wherePagePostTypes(Builder $builder): Builder
    {
        return $builder->whereIn('articles.post_type', [ArticlePostType::Page, ArticlePostType::Markdown]);
    }

    /**
     * 標準的な関連データの読み込み
     *
     * @param  Builder<Article>  $builder
     * @return Builder<Article>
     */
    protected function withStandardRelations(Builder $builder): Builder
    {
        return $builder->with('categories', 'tags', 'attachments', 'user.profile.attachments');
    }

    /**
     * modified_at で降順ソート
     *
     * @param  Builder<Article>  $builder
     * @return Builder<Article>
     */
    protected function orderByLatest(Builder $builder): Builder
    {
        return $builder->latest('articles.modified_at');
    }
}
