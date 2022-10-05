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
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;

class ArticleRepository extends BaseRepository
{
    public const MYPAGE_RELATIONS = ['user', 'attachments.fileInfo', 'categories', 'tags', 'tweetLogSummary', 'totalViewCount', 'totalConversionCount'];
    public const FRONT_RELATIONS = ['user.profile', 'attachments.fileInfo', 'categories', 'tags'];
    public const SHOW_RELATIONS = ['user.profile', 'attachments.fileInfo', 'categories', 'tags'];

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
     */
    public function syncAttachments(Article $article, array $attachmentsIds): void
    {
        $article->attachments()->saveMany(
            $article->user->myAttachments()->find($attachmentsIds)
        );
    }

    /**
     * カテゴリを関連付ける.
     */
    public function syncCategories(Article $article, array $categoryIds): void
    {
        $article->categories()->sync($categoryIds);
    }

    /**
     * タグを関連付ける.
     */
    public function syncTags(Article $article, array $tagIds): void
    {
        $article->tags()->sync($tagIds);
    }

    /**
     * アナリティクス用のデータ取得.
     */
    public function findAllForAnalytics(User $user, array $ids, Closure $periodQuery): Collection
    {
        return $this->model
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
     */
    public function findAllByUser(User $user, array $relations = self::FRONT_RELATIONS): Collection
    {
        return $this->model
            ->select(['articles.*'])
            ->where('user_id', $user->id)
            ->with($relations)
            ->orderBy('published_at', 'desc')
            ->get();
    }

    private function basicQuery(array $relations = self::FRONT_RELATIONS): Builder
    {
        return $this->model
            ->select(['articles.*'])
            ->active()
            ->with($relations)
            ->orderBy('modified_at', 'desc');
    }

    private function basicRelationQuery(Relation $query, array $relations = self::FRONT_RELATIONS): Relation
    {
        return $query->select(['articles.*'])
            ->active()
            ->with($relations)
            ->orderBy('modified_at', 'desc');
    }

    private function queryAnnouces(): Builder
    {
        return $this->basicQuery()
            ->announce();
    }

    /**
     * お知らせ記事一覧.
     */
    public function paginateAnnouces(bool $simple = false): Paginator
    {
        return $simple
            ? $this->queryAnnouces()->simplePaginate()
            : $this->queryAnnouces()->paginate();
    }

    private function queryPages(): Builder
    {
        return $this->basicQuery()
            ->withoutAnnounce();
    }

    /**
     * 一般記事一覧.
     */
    public function paginatePages(bool $simple = false): Paginator
    {
        return $simple
            ? $this->queryPages()->simplePaginate()
            : $this->queryPages()->paginate();
    }

    private function queryRanking(): Builder
    {
        return $this->model
            ->select(['articles.*'])
            ->active()
            ->with(self::FRONT_RELATIONS)
            ->rankingOrder();
    }

    /**
     * アドオン投稿/紹介のデイリーPVランキング.
     */
    public function paginateRanking(bool $simple = false): Paginator
    {
        return $simple
            ? $this->queryRanking()->simplePaginate()
            : $this->queryRanking()->paginate();
    }

    private function queryByCategory(Category $category): Relation
    {
        return $this->basicRelationQuery($category->articles());
    }

    /**
     * カテゴリの投稿一覧.
     */
    public function paginateByCategory(Category $category, bool $simple = false): Paginator
    {
        return $simple
            ? $this->queryByCategory($category)->simplePaginate()
            : $this->queryByCategory($category)->paginate();
    }

    private function queryByPakAddonCategory(Category $pak, Category $addon): Builder
    {
        return $this->basicQuery()
            ->whereHas('categories', fn ($query) => $query->where('id', $pak->id))
            ->whereHas('categories', fn ($query) => $query->where('id', $addon->id));
    }

    /**
     * カテゴリ(pak/addon)の投稿一覧.
     */
    public function paginateByPakAddonCategory(Category $pak, Category $addon): LengthAwarePaginator
    {
        return $this->queryByPakAddonCategory($pak, $addon)->paginate();
    }

    /**
     * カテゴリ(pak,addon指定なし)の投稿一覧.
     */
    public function paginateByPakNoneAddonCategory(Category $pak): LengthAwarePaginator
    {
        return $this->queryByCategory($pak)
            ->whereDoesntHave('categories', fn ($query) => $query->where('type', 'addon'))
            ->paginate();
    }

    private function queryByTag(Tag $tag): Relation
    {
        return $this->basicRelationQuery($tag->articles());
    }

    /**
     * タグを持つ投稿記事一覧.
     */
    public function paginateByTag(Tag $tag): LengthAwarePaginator
    {
        return $this->queryByTag($tag)->paginate();
    }

    private function queryByUser(User $user): Relation
    {
        return $this->basicRelationQuery($user->articles());
    }

    /**
     * ユーザーの投稿記事一覧.
     */
    public function paginateByUser(User $user): LengthAwarePaginator
    {
        return $this->queryByUser($user)->paginate();
    }

    private function queryBySearch(string $word): Builder
    {
        $word = trim($word);
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
     */
    public function paginateBySearch(string $word): LengthAwarePaginator
    {
        return $this->queryBySearch($word)->paginate();
    }

    public function cursorCheckLink(): LazyCollection
    {
        return $this->model
            ->select('id', 'user_id', 'title', 'post_type', 'contents')
            ->active()
            ->linkCheckTarget()
            ->with('user:id,email')
            ->cursor();
    }

    public function findAllFeedItems(): Collection
    {
        return $this->model
            ->select('id', 'user_id', 'title', 'slug', 'post_type', 'contents', 'modified_at')
            ->active()
            ->addon()
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

    public function findByTitles(array $titles): Collection
    {
        return $this->model->active()->whereIn('title', $titles)->get();
    }

    /**
     * @return LazyCollection<Article>
     */
    public function fetchAggregatedRanking(CarbonImmutable $datetime): LazyCollection
    {
        return $this->model
            ->select('articles.*')
            ->addon()
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
