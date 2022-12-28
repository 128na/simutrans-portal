<?php

namespace App\Repositories;

use App\Models\Article;
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
class ArticleRepository extends BaseRepository
{
    public const MYPAGE_RELATIONS = ['user', 'attachments.fileInfo', 'categories', 'tags', 'tweetLogSummary', 'totalViewCount', 'totalConversionCount'];

    public const FRONT_RELATIONS = ['user.profile', 'attachments.fileInfo', 'categories', 'tags'];

    public const SHOW_RELATIONS = ['user.profile', 'attachments.fileInfo', 'categories', 'tags'];

    public const PER_PAGE_SIMPLE = 6;

    /**
     * @var Article
     */
    protected $model;

    public function __construct(Article $model)
    {
        $this->model = $model;
    }

    /**
     * 添付ファイルを関連付ける.
     *
     * @param  array<int|string>  $attachmentsIds
     */
    public function syncAttachments(Article $article, array $attachmentsIds): void
    {
        if ($article->user) {
            $attachments = $article->user->myAttachments()->find($attachmentsIds);
            $article->attachments()->saveMany($attachments);
        }
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
     * @return Collection<int, Article>
     */
    public function findAllForAnalytics(User $user, array $ids, Closure $periodQuery): Collection
    {
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
     * @param  array<mixed>  $relations
     * @return Collection<int, Article>
     */
    public function findAllByUser(User $user, array $relations = self::FRONT_RELATIONS): Collection
    {
        /** @var Collection<int, Article> */
        return $this->model
            ->query()
            ->select(['articles.*'])
            ->where('user_id', $user->id)
            ->with($relations)
            ->orderBy('published_at', 'desc')
            ->get();
    }

    private function queryAnnouces(): Builder
    {
        return $this->model
            ->active()
            ->announce()
            ->select(['articles.*'])
            ->with(self::FRONT_RELATIONS)
            ->orderBy('modified_at', 'desc');
    }

    /**
     * お知らせ記事一覧.
     *
     * @return Paginator<Article>
     */
    public function paginateAnnouces(bool $simple = false): Paginator
    {
        /** @var Paginator<Article> */
        return $simple
            ? $this->queryAnnouces()->simplePaginate(self::PER_PAGE_SIMPLE)
            : $this->queryAnnouces()->paginate();
    }

    private function queryPages(): Builder
    {
        return $this->model
            ->active()
            ->withoutAnnounce()
            ->select(['articles.*'])
            ->with(self::FRONT_RELATIONS)
            ->orderBy('modified_at', 'desc');
    }

    /**
     * 一般記事一覧.
     *
     * @return Paginator<Article>
     */
    public function paginatePages(bool $simple = false): Paginator
    {
        /** @var Paginator<Article> */
        return $simple
            ? $this->queryPages()->simplePaginate(self::PER_PAGE_SIMPLE)
            : $this->queryPages()->paginate();
    }

    private function queryRanking(): Builder
    {
        return $this->model
            ->active()
            ->select(['articles.*'])
            ->with(self::FRONT_RELATIONS)
            ->rankingOrder();
    }

    /**
     * アドオン投稿/紹介のデイリーPVランキング.
     *
     * @return Paginator<Article>
     */
    public function paginateRanking(bool $simple = false): Paginator
    {
        /** @var Paginator<Article> */
        return $simple
            ? $this->queryRanking()->simplePaginate(self::PER_PAGE_SIMPLE)
            : $this->queryRanking()->paginate();
    }

    /**
     * カテゴリの投稿一覧.
     *
     * @return Paginator<Article>
     */
    public function paginateByCategory(Category $category, bool $simple = false): Paginator
    {
        $q = $category->articles()
            ->active()
            ->select(['articles.*'])
            ->with(self::FRONT_RELATIONS)
            ->orderBy('modified_at', 'desc');

        return $simple
            ? $q->simplePaginate(self::PER_PAGE_SIMPLE)
            : $q->paginate();
    }

    private function queryByPakAddonCategory(Category $pak, Category $addon): Builder
    {
        return $this->model
            ->active()
            ->select(['articles.*'])
            ->with(self::FRONT_RELATIONS)
            ->orderBy('modified_at', 'desc')
            ->whereHas('categories', fn ($query) => $query->where('id', $pak->id))
            ->whereHas('categories', fn ($query) => $query->where('id', $addon->id));
    }

    /**
     * カテゴリ(pak/addon)の投稿一覧.
     *
     * @return LengthAwarePaginator<Article>
     */
    public function paginateByPakAddonCategory(Category $pak, Category $addon): LengthAwarePaginator
    {
        /** @var LengthAwarePaginator<Article> */
        return $this->queryByPakAddonCategory($pak, $addon)->paginate();
    }

    /**
     * カテゴリ(pak,addon指定なし)の投稿一覧.
     *
     * @return LengthAwarePaginator<Article>
     */
    public function paginateByPakNoneAddonCategory(Category $pak): LengthAwarePaginator
    {
        return $pak->articles()
            ->active()
            ->select(['articles.*'])
            ->with(self::FRONT_RELATIONS)
            ->whereDoesntHave('categories', fn ($query) => $query->where('type', 'addon'))
            ->orderBy('modified_at', 'desc')
            ->paginate();
    }

    /**
     * タグを持つ投稿記事一覧.
     *
     * @return LengthAwarePaginator<Article>
     */
    public function paginateByTag(Tag $tag): LengthAwarePaginator
    {
        return $tag->articles()
            ->active()
            ->select(['articles.*'])
            ->with(self::FRONT_RELATIONS)
            ->orderBy('modified_at', 'desc')
            ->paginate();
    }

    /**
     * ユーザーの投稿記事一覧.
     *
     * @return LengthAwarePaginator<Article>
     */
    public function paginateByUser(User $user): LengthAwarePaginator
    {
        return $user->articles()
            ->active()
            ->select(['articles.*'])
            ->with(self::FRONT_RELATIONS)
            ->orderBy('modified_at', 'desc')
            ->paginate();
    }

    private function queryBySearch(string $word): Builder
    {
        $word = trim($word);

        if (! $word) {
            return $this->model->select(['articles.*'])
                ->active()
                ->with(self::FRONT_RELATIONS)
                ->orderBy('modified_at', 'desc');
        }

        $likeWord = "%{$word}%";

        return $this->model->select(['articles.*'])
            ->active()
            ->where(fn ($q) => $q
                ->orWhere('title', 'LIKE', $likeWord)
                ->orWhere('contents', 'LIKE', $likeWord)
                ->orWhereHas('attachments.fileInfo', fn ($q) => $q
                    ->where('data', 'LIKE', $likeWord)))
            ->with(self::FRONT_RELATIONS)
            ->orderBy('modified_at', 'desc');
    }

    /**
     * 記事検索結果一覧.
     *
     * @return LengthAwarePaginator<Article>
     */
    public function paginateBySearch(string $word): LengthAwarePaginator
    {
        /** @var LengthAwarePaginator<Article> */
        return $this->queryBySearch($word)->paginate();
    }

    public function cursorCheckLink(): LazyCollection
    {
        return $this->model
            ->active()
            ->linkCheckTarget()
            ->select('id', 'user_id', 'title', 'post_type', 'contents')
            ->with('user:id,email')
            ->cursor();
    }

    public function findAllFeedItems(): Collection
    {
        return $this->model
            ->active()
            ->addon()
            ->select('id', 'user_id', 'title', 'slug', 'post_type', 'contents', 'modified_at')
            ->limit(24)
            ->with('user:id,name')
            ->orderBy('modified_at', 'desc')
            ->get();
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
     * @param  array<string>  $titles
     * @return Collection<int, Article>
     */
    public function findByTitles(array $titles): Collection
    {
        /** @var Collection<int, Article> */
        return $this->model->active()->whereIn('title', $titles)->get();
    }

    /**
     * @return LazyCollection<Article>
     */
    public function fetchAggregatedRanking(CarbonImmutable $datetime): LazyCollection
    {
        return $this->model
            ->addon()
            ->select('articles.*')
            ->leftJoin('view_counts as d', fn (JoinClause $join) => $join
                ->on('d.article_id', 'articles.id')
                ->where('d.type', 1)
                ->where('d.period', $datetime->format('Ymd')))
            ->leftJoin('view_counts as m', fn (JoinClause $join) => $join
                ->on('m.article_id', 'articles.id')
                ->where('m.type', 2)
                ->where('m.period', $datetime->format('Ym')))
            ->leftJoin('view_counts as y', fn (JoinClause $join) => $join
                ->on('y.article_id', 'articles.id')
                ->where('y.type', 3)
                ->where('y.period', $datetime->format('Y')))
            ->leftJoin('view_counts as t', fn (JoinClause $join) => $join
                ->on('t.article_id', 'articles.id')
                ->where('t.type', 4)
                ->where('t.period', 'total'))
            ->orderBy('d.count', 'desc')
            ->orderBy('m.count', 'desc')
            ->orderBy('y.count', 'desc')
            ->orderBy('t.count', 'desc')
            ->cursor();
    }

    /**
     * @return LazyCollection<Article>
     */
    public function cursorReservations(CarbonImmutable $date): LazyCollection
    {
        return $this->model
            ->where('status', config('status.reservation'))
            ->where('published_at', '<=', $date)
            ->cursor();
    }
}
