<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Enums\ArticleAnalyticsType;
use App\Enums\ArticleStatus;
use App\Enums\CategoryType;
use App\Models\Article;
use App\Models\Attachment;
use App\Models\Category;
use App\Models\Tag;
use App\Models\User;
use Carbon\CarbonImmutable;
use Closure;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\LazyCollection;

/**
 * @extends BaseRepository<Article>
 */
final class ArticleRepository extends BaseRepository
{
    public const array MYPAGE_RELATIONS = ['user', 'attachments.fileInfo', 'categories', 'tags', 'totalViewCount', 'totalConversionCount', 'articles'];

    public const array FRONT_RELATIONS = ['user.profile', 'attachments.fileInfo', 'categories', 'tags', 'articles', 'relatedArticles'];

    public const array SHOW_RELATIONS = ['user.profile', 'attachments.fileInfo', 'categories', 'tags', 'articles', 'relatedArticles'];

    public const int PER_PAGE_SIMPLE = 6;

    public function __construct(Article $article)
    {
        parent::__construct($article);
    }

    /**
     * 添付ファイルを関連付ける.
     *
     * @param  array<int|string>  $attachmentsIds
     */
    public function syncAttachments(Article $article, array $attachmentsIds): void
    {
        // add
        $attachments = $article->user->myAttachments()->find($attachmentsIds);
        $article->attachments()->saveMany($attachments);

        // remove
        /** @var Collection<int,Attachment> */
        $shouldDetach = $article->attachments()->whereNotIn('id', $attachmentsIds)->get();
        foreach ($shouldDetach as $attachment) {
            $attachment->attachmentable()->disassociate()->save();
        }
    }

    /**
     * 記事を関連付ける.
     *
     * @param  array<int|string>  $articleIds
     */
    public function syncArticles(Article $article, array $articleIds): void
    {
        $result = $article->articles()->sync($articleIds);
        logger('[ArticleRepository] syncArticles', $result);
    }

    /**
     * カテゴリを関連付ける.
     *
     * @param  array<int|string>  $categoryIds
     */
    public function syncCategories(Article $article, array $categoryIds): void
    {
        $article->categories()->sync($categoryIds);
    }

    /**
     * タグを関連付ける.
     *
     * @param  array<int|string>  $tagIds
     */
    public function syncTags(Article $article, array $tagIds): void
    {
        $article->tags()->sync($tagIds);
    }

    /**
     * アナリティクス用のデータ取得.
     *
     * @param  array<int|string>  $ids
     * @param  array<string>  $period
     * @return Collection<int, Article>
     */
    public function findAllForAnalytics(User $user, array $ids, ArticleAnalyticsType $articleAnalyticsType, array $period): Collection
    {
        $periodQuery = function ($query) use ($articleAnalyticsType, $period): void {
            $query->select('article_id', 'count', 'period')
                ->where('type', $articleAnalyticsType)->whereBetween('period', $period);
        };

        /** @var Collection<int, Article> */
        return $this->model
            ->query()
            ->select('id')
            ->where('user_id', $user->id)
            ->whereIn('id', $ids)
            ->with([
                'viewCounts' => $periodQuery,
                'conversionCounts' => $periodQuery,
            ])
            ->orderBy('published_at', 'desc')
            ->get();
    }

    /**
     * ユーザーに紐づくデータを返す.
     *
     * @param  array<int,string>  $relations
     * @return Collection<int, Article>
     */
    public function findAllByUser(User $user, array $relations = self::FRONT_RELATIONS, string $order = 'modified_at'): Collection
    {
        /** @var Collection<int, Article> */
        return $this->model
            ->query()
            ->select(['articles.*'])
            ->where('user_id', $user->id)
            ->with($relations)
            ->orderBy($order, 'desc')
            ->get();
    }

    /**
     * お知らせ記事一覧.
     *
     * @return Paginator<int,Article>
     */
    public function paginateAnnouces(bool $simple = false, string $order = 'modified_at'): Paginator
    {
        return $simple
            ? $this->queryAnnouces($order)->simplePaginate(self::PER_PAGE_SIMPLE)
            : $this->queryAnnouces($order)->paginate();
    }

    /**
     * 一般記事一覧.
     *
     * @return Paginator<int,Article>
     */
    public function paginatePages(bool $simple = false, string $order = 'modified_at'): Paginator
    {
        return $simple
            ? $this->queryPages($order)->simplePaginate(self::PER_PAGE_SIMPLE)
            : $this->queryPages($order)->paginate();
    }

    /**
     * アドオン投稿/紹介のデイリーPVランキング.
     *
     * @return Paginator<int,Article>
     */
    public function paginateRanking(bool $simple = false): Paginator
    {
        return $simple
            ? $this->queryRanking()->simplePaginate(self::PER_PAGE_SIMPLE)
            : $this->queryRanking()->paginate();
    }

    /**
     * カテゴリの投稿一覧.
     *
     * @return Paginator<int,Article>
     */
    public function paginateByCategory(Category $category, bool $simple = false, string $order = 'modified_at'): Paginator
    {
        $q = $category->articles()
            ->active()
            ->select(['articles.*'])
            ->with(self::FRONT_RELATIONS)
            ->orderBy($order, 'desc');

        return $simple
            ? $q->simplePaginate(self::PER_PAGE_SIMPLE)
            : $q->paginate();
    }

    /**
     * カテゴリ(pak/addon)の投稿一覧.
     *
     * @return LengthAwarePaginator<int,Article>
     */
    public function paginateByPakAddonCategory(Category $pak, Category $addon, string $order = 'modified_at'): LengthAwarePaginator
    {
        return $this->queryByPakAddonCategory($pak, $addon, $order)->paginate();
    }

    /**
     * カテゴリ(pak,addon指定なし)の投稿一覧.
     *
     * @return LengthAwarePaginator<int,Article>
     */
    public function paginateByPakNoneAddonCategory(Category $category, string $order = 'modified_at'): LengthAwarePaginator
    {
        return $category->articles()
            ->active()
            ->select(['articles.*'])
            ->with(self::FRONT_RELATIONS)
            ->whereDoesntHave('categories', fn ($query) => $query->where('type', CategoryType::Addon))
            ->orderBy($order, 'desc')
            ->paginate();
    }

    /**
     * タグを持つ投稿記事一覧.
     *
     * @return LengthAwarePaginator<int,Article>
     */
    public function paginateByTag(Tag $tag, string $order = 'modified_at'): LengthAwarePaginator
    {
        return $tag->articles()
            ->active()
            ->select(['articles.*'])
            ->with(self::FRONT_RELATIONS)
            ->orderBy($order, 'desc')
            ->paginate();
    }

    /**
     * ユーザーの投稿記事一覧.
     *
     * @return LengthAwarePaginator<int,Article>
     */
    public function paginateByUser(User $user, string $order = 'modified_at'): LengthAwarePaginator
    {
        return $user->articles()
            ->active()
            ->select(['articles.*'])
            ->with(self::FRONT_RELATIONS)
            ->orderBy($order, 'desc')
            ->paginate();
    }

    /**
     * 記事検索結果一覧.
     *
     * @return LengthAwarePaginator<int,Article>
     */
    public function paginateBySearch(string $word, string $order = 'modified_at'): LengthAwarePaginator
    {
        /** @var LengthAwarePaginator<int,Article> */
        return $this->queryBySearch($word, $order)->paginate();
    }

    /**
     * リンク切れチェック対象の記事.
     *
     * @return LazyCollection<int,Article>
     */
    public function cursorCheckLink(): LazyCollection
    {
        return $this->model
            ->active()
            ->linkCheckTarget()
            ->select('id', 'user_id', 'title', 'slug', 'post_type', 'contents')
            ->with('user:id,email')
            ->cursor();
    }

    /**
     * 記事表示.
     */
    public function loadArticle(Article $article): Article
    {
        return $this->load($article, self::SHOW_RELATIONS);
    }

    /**
     * 論理削除されているものも含めた一覧.
     *
     * @return Collection<int, Article>
     */
    public function findAllWithTrashed(): Collection
    {
        return $this->model
            ->withTrashed()
            ->withUserTrashed()
            ->with(['user' => fn ($q) => $q->withTrashed()])
            ->get();
    }

    /**
     * 論理削除されているものも含めて探す.
     */
    public function findOrFailWithTrashed(int $id): Article
    {
        return $this->model
            ->withTrashed()
            ->withUserTrashed()
            ->findOrFail($id);
    }

    /**
     * 論理削除状態を切り替える.
     */
    public function toggleDelete(Article $article): void
    {
        $article->trashed()
            ? $article->restore()
            : $article->delete();
    }

    /**
     * PV数順の記事
     *
     * @param  Closure(\Illuminate\Support\Collection<int,Article> $article, int $index):void  $fn
     */
    public function chunkAggregatedRanking(CarbonImmutable $datetime, int $size, Closure $fn): void
    {
        $this->model
            ->active()
            ->addon()
            ->select('articles.id')
            ->leftJoin('view_counts as d', fn (JoinClause $joinClause) => $joinClause
                ->on('d.article_id', 'articles.id')
                ->where('d.type', 1)
                ->where('d.period', $datetime->format('Ymd')))
            ->leftJoin('view_counts as m', fn (JoinClause $joinClause) => $joinClause
                ->on('m.article_id', 'articles.id')
                ->where('m.type', 2)
                ->where('m.period', $datetime->format('Ym')))
            ->leftJoin('view_counts as y', fn (JoinClause $joinClause) => $joinClause
                ->on('y.article_id', 'articles.id')
                ->where('y.type', 3)
                ->where('y.period', $datetime->format('Y')))
            ->leftJoin('view_counts as t', fn (JoinClause $joinClause) => $joinClause
                ->on('t.article_id', 'articles.id')
                ->where('t.type', 4)
                ->where('t.period', 'total'))
            ->orderBy('d.count', 'desc')
            ->orderBy('m.count', 'desc')
            ->orderBy('y.count', 'desc')
            ->orderBy('t.count', 'desc')
            ->chunk($size, $fn);
    }

    /**
     * 指定時刻を過ぎた予約記事
     *
     * @return LazyCollection<int,Article>
     */
    public function cursorReservations(CarbonImmutable $date): LazyCollection
    {
        return $this->model
            ->where('status', ArticleStatus::Reservation)
            ->where('published_at', '<=', $date)
            ->cursor();
    }

    /**
     * ランダムなPR記事
     */
    public function findRandomPR(): ?Article
    {
        return $this->model
            ->active()
            ->where('pr', 1)
            ->inRandomOrder()
            ->first();
    }

    /**
     * @return Builder<Article>
     */
    private function queryAnnouces(string $order): Builder
    {
        return $this->model
            ->active()
            ->announce()
            ->select(['articles.*'])
            ->with(self::FRONT_RELATIONS)
            ->orderBy($order, 'desc');
    }

    /**
     * @return Builder<Article>
     */
    private function queryPages(string $order): Builder
    {
        return $this->model
            ->active()
            ->withoutAnnounce()
            ->select(['articles.*'])
            ->with(self::FRONT_RELATIONS)
            ->orderBy($order, 'desc');
    }

    /**
     * @return Builder<Article>
     */
    private function queryRanking(): Builder
    {
        return $this->model
            ->active()
            ->select(['articles.*'])
            ->with(self::FRONT_RELATIONS)
            ->rankingOrder();
    }

    /**
     * @return Builder<Article>
     */
    private function queryByPakAddonCategory(Category $pak, Category $addon, string $order): Builder
    {
        return $this->model
            ->active()
            ->select(['articles.*'])
            ->with(self::FRONT_RELATIONS)
            ->whereHas('categories', fn ($query) => $query->where('id', $pak->id))
            ->whereHas('categories', fn ($query) => $query->where('id', $addon->id))
            ->orderBy($order, 'desc');
    }

    /**
     * @return Builder<Article>
     */
    private function queryBySearch(string $word, string $order): Builder
    {
        $word = trim($word);

        if ($word === '' || $word === '0') {
            return $this->model->select(['articles.*'])
                ->active()
                ->with(self::FRONT_RELATIONS)
                ->orderBy($order, 'desc');
        }

        $likeWord = sprintf('%%%s%%', $word);

        return $this->model->select(['articles.*'])
            ->active()
            ->where(fn ($q) => $q
                ->orWhere('title', 'LIKE', $likeWord)
                ->orWhere('contents', 'LIKE', $likeWord)
                ->orWhereHas('attachments.fileInfo', fn ($q) => $q
                    ->where('data', 'LIKE', $likeWord)))
            ->with(self::FRONT_RELATIONS)
            ->orderBy($order, 'desc');
    }
}
