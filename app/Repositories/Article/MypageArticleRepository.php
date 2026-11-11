<?php

declare(strict_types=1);

namespace App\Repositories\Article;

use App\Enums\ArticleAnalyticsType;
use App\Enums\ArticleStatus;
use App\Models\Article;
use App\Models\User;
use App\Repositories\Concerns\ArticleQueryConcern;
use Illuminate\Database\Eloquent\Collection;

class MypageArticleRepository
{
    use ArticleQueryConcern;

    public function __construct(public Article $model) {}

    /**
     * 編集用記事一覧取得
     *
     * @return Collection<int,Article>
     */
    public function getForEdit(?Article $article = null): Collection
    {
        $builder = $this->model->query()
            ->select(['articles.id', 'articles.title', 'articles.user_id', 'users.name as user_name']);

        $this->joinActiveUsers($builder);
        $this->wherePublished($builder);
        $this->orderByLatest($builder);

        return $builder
            ->when($article, fn ($q, Article $article) => $q->where('articles.id', '!=', $article->id))
            ->get();
    }

    /**
     * マイページ用記事一覧取得
     *
     * @return Collection<int,Article>
     */
    public function getForMypageList(User $user): Collection
    {
        $builder = $this->model->query()
            ->select(['id', 'title', 'slug', 'status', 'post_type', 'published_at', 'modified_at'])
            ->where('articles.user_id', $user->id);

        $this->orderByLatest($builder);

        return $builder
            ->with('totalConversionCount', 'totalViewCount')
            ->get();
    }

    /**
     * マイページトップ用に最近更新した記事（ゴミ箱を除く）を件数を絞って取得
     *
     * @return Collection<int,Article>
     */
    public function getRecentForMypage(User $user, int $limit): Collection
    {
        $builder = $this->model->query()
            ->select(['id', 'user_id', 'title', 'slug', 'status', 'modified_at'])
            ->where('articles.user_id', $user->id)
            ->where('articles.status', '!=', ArticleStatus::Trash);

        $this->orderByLatest($builder);

        $articles = $builder
            ->limit($limit)
            ->with('totalConversionCount', 'totalViewCount')
            ->get();

        // 記事URLの生成で記事ごとに投稿者を読み直さないよう、投稿者を渡しておく
        return $articles->each(fn (Article $article): Article => $article->setRelation('user', $user));
    }

    /**
     * 公開中で最近更新した記事のIDを件数を絞って取得（マイページトップの推移の対象）
     *
     * @return list<int>
     */
    public function getRecentPublishedIds(User $user, int $limit): array
    {
        $builder = $this->model->query()
            ->select(['articles.id'])
            ->where('articles.user_id', $user->id);

        $this->wherePublished($builder);
        $this->orderByLatest($builder);

        /** @var list<int> */
        return $builder->limit($limit)->pluck('articles.id')->all();
    }

    /**
     * アナリティクス用記事一覧取得
     *
     * @return Collection<int,Article>
     */
    public function getForAnalyticsList(User $user): Collection
    {
        $builder = $this->model->query()
            ->select(['articles.id', 'articles.title', 'articles.slug', 'articles.published_at', 'articles.modified_at'])
            ->where('articles.user_id', $user->id);

        $this->wherePublished($builder);
        $this->orderByLatest($builder);

        return $builder
            ->with('totalConversionCount', 'totalViewCount')
            ->get();
    }

    /**
     * アナリティクス用のデータ取得
     *
     * @param  array<int|string>  $ids
     * @param  array<string>  $period
     * @return Collection<int, Article>
     */
    public function findAllForAnalytics(User $user, array $ids, ArticleAnalyticsType $articleAnalyticsType, array $period): Collection
    {
        $periodQuery = function ($query) use ($articleAnalyticsType, $period): void {
            $query->select(['article_id', 'count', 'period'])
                ->where('type', $articleAnalyticsType)->whereBetween('period', $period);
        };
        [$from, $to] = $period;
        $sumQuery = function ($q) use ($articleAnalyticsType, $from): void {
            $q->where('type', $articleAnalyticsType)
                ->where('period', '<', $from);
        };

        /** @var Collection<int, Article> */
        return $this->model
            ->query()
            ->select(['id', 'post_type', 'published_at'])
            ->where('user_id', $user->id)
            ->whereIn('id', $ids)
            ->with([
                'viewCounts' => $periodQuery,
                'conversionCounts' => $periodQuery,
            ])
            ->withSum(['viewCounts as past_view_count' => $sumQuery], 'count')
            ->withSum(['conversionCounts as past_conversion_count' => $sumQuery], 'count')
            ->orderBy('published_at', 'desc')
            ->get();
    }
}
