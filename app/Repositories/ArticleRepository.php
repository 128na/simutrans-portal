<?php

namespace App\Repositories;

use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
use App\Models\User;
use App\QueryBuilders\AdvancedSearchQueryBuilder;
use Carbon\CarbonImmutable;
use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;

class ArticleRepository extends BaseRepository
{
    public const MYPAGE_RELATIONS = ['user', 'attachments.fileInfo', 'categories', 'tags', 'tweetLogSummary', 'totalViewCount', 'totalConversionCount'];
    public const FRONT_RELATIONS = ['user', 'attachments', 'categories', 'tags'];
    public const SHOW_RELATIONS = ['user.profile', 'attachments.fileInfo', 'categories', 'tags'];

    /**
     * @var Article
     */
    protected $model;

    private AdvancedSearchQueryBuilder $advancedSearchQueryBuilder;

    public function __construct(Article $model, AdvancedSearchQueryBuilder $advancedSearchQueryBuilder)
    {
        $this->model = $model;
        $this->advancedSearchQueryBuilder = $advancedSearchQueryBuilder;
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
        return $user->articles()
            ->select('id')
            ->whereIn('id', $ids)
            ->with([
                'viewCounts' => $periodQuery,
                'conversionCounts' => $periodQuery,
            ])->get();
    }

    /**
     * ユーザーに紐づくデータを返す.
     */
    public function findAllByUser(User $user, array $relations = self::FRONT_RELATIONS): Collection
    {
        return $user->articles()
            ->select(['articles.*'])
            ->with($relations)
            ->get();
    }

    private function basicQuery(array $relations = self::FRONT_RELATIONS): Builder
    {
        return $this->model->select(['articles.*'])
            ->active()
            ->withCache()
            ->with($relations)
            ->orderBy('updated_at', 'desc');
    }

    private function basicRelationQuery(Relation $query, array $relations = self::FRONT_RELATIONS): Relation
    {
        return $query->select(['articles.*'])
            ->active()
            ->withCache()
            ->with($relations)
            ->orderBy('updated_at', 'desc');
    }

    private function queryAnnouces(): Builder
    {
        return $this->basicQuery()
            ->announce();
    }

    /**
     * お知らせ記事一覧.
     */
    public function findAllAnnouces(?int $limit = null): Collection
    {
        return $this->queryAnnouces()->limit($limit)->get();
    }

    /**
     * お知らせ記事一覧.
     */
    public function paginateAnnouces(?int $limit = null): LengthAwarePaginator
    {
        return $this->queryAnnouces()->paginate($limit);
    }

    private function queryPages(): Builder
    {
        return $this->basicQuery()
            ->withoutAnnounce();
    }

    /**
     * 一般記事一覧.
     */
    public function findAllPages(?int $limit = null): Collection
    {
        return $this->queryPages()->limit($limit)->get();
    }

    /**
     * 一般記事一覧.
     */
    public function paginatePages(?int $limit = null): LengthAwarePaginator
    {
        return $this->queryPages()->paginate($limit);
    }

    private function queryByPak(string $pak): Builder
    {
        return $this->basicQuery()
            ->pak($pak)
            ->addon();
    }

    /**
     * pak別の投稿一覧.
     */
    public function findAllByPak(string $pak, ?int $limit = null): Collection
    {
        return $this->queryByPak($pak)->limit($limit)->get();
    }

    private function queryRanking(): Builder
    {
        return $this->model->select(['articles.*'])
            ->withCache()
            ->active()
            ->with(self::FRONT_RELATIONS)
            ->rankingOrder();
    }

    /**
     * アドオン投稿/紹介のデイリーPVランキング.
     */
    public function findAllRanking(?int $limit = null): Collection
    {
        return $this->queryRanking()->limit($limit)->get();
    }

    /**
     * アドオン投稿/紹介のデイリーPVランキング.
     */
    public function paginateRanking(?int $limit = null): LengthAwarePaginator
    {
        return $this->queryRanking()->paginate($limit);
    }

    private function queryAddon(): Builder
    {
        return $this->basicQuery()
            ->addon()
            ->active();
    }

    /**
     * アドオン投稿/紹介の一覧.
     */
    public function paginateAddons(?int $limit = null): LengthAwarePaginator
    {
        return $this->queryAddon()->paginate($limit);
    }

    private function queryByCategory(Category $category): Relation
    {
        return $this->basicRelationQuery($category->articles());
    }

    /**
     * カテゴリの投稿一覧.
     */
    public function paginateByCategory(Category $category, ?int $limit = null): LengthAwarePaginator
    {
        return $this->queryByCategory($category)->paginate($limit);
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
    public function paginateByPakAddonCategory(Category $pak, Category $addon, ?int $limit = null): LengthAwarePaginator
    {
        return $this->queryByPakAddonCategory($pak, $addon)->paginate($limit);
    }

    /**
     * カテゴリ(pak,addon指定なし)の投稿一覧.
     */
    public function paginateByPakNoneAddonCategory(Category $pak, ?int $limit = null): LengthAwarePaginator
    {
        return $this->queryByCategory($pak)
            ->whereDoesntHave('categories', fn ($query) => $query->where('type', 'addon'))
            ->paginate($limit);
    }

    private function queryByTag(Tag $tag): Relation
    {
        return $this->basicRelationQuery($tag->articles());
    }

    /**
     * タグを持つ投稿記事一覧.
     */
    public function paginateByTag(Tag $tag, ?int $limit = null): LengthAwarePaginator
    {
        return $this->queryByTag($tag)->paginate($limit);
    }

    private function queryByUser(User $user): Relation
    {
        return $this->basicRelationQuery($user->articles());
    }

    /**
     * ユーザーの投稿記事一覧.
     */
    public function paginateByUser(User $user, ?int $limit = null): LengthAwarePaginator
    {
        return $this->queryByUser($user)->paginate($limit);
    }

    private function queryBySearch(string $word): Builder
    {
        return $this->basicQuery()
            ->search($word)
            ->orderBy('updated_at', 'desc');
    }

    /**
     * 記事検索結果一覧.
     */
    public function paginateBySearch(string $word, ?int $limit = null): LengthAwarePaginator
    {
        return $this->queryBySearch($word)->paginate($limit);
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
            ->select('id', 'user_id', 'title', 'slug', 'post_type', 'contents', 'updated_at')
            ->active()
            ->addon()
            ->limit(24)
            ->with('user:id,name')
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

    /**
     * 詳細検索.
     */
    private function queryByAdvancedSearch(
        ?string $word = null,
        ?Collection $categories = null,
        ?bool $categoryAnd = true,
        ?Collection $tags = null,
        ?bool $tagAnd = true,
        ?Collection $users = null,
        ?bool $userAnd = true,
        ?CarbonImmutable $startAt = null,
        ?CarbonImmutable $endAt = null,
        string $order = 'updated_at',
        string $direction = 'desc'
    ): Builder {
        $q = $this->model->select(['articles.*'])
            ->active()
            ->withCache()
            ->with(self::FRONT_RELATIONS)
            ->orderBy($order, $direction);

        if ($word) {
            $this->advancedSearchQueryBuilder->addWordSearch($q, $word);
        }
        if ($categories) {
            $this->advancedSearchQueryBuilder->addCategories($q, $categories, $categoryAnd);
        }
        if ($tags) {
            $this->advancedSearchQueryBuilder->addTags($q, $tags, $tagAnd);
        }
        if ($users) {
            $this->advancedSearchQueryBuilder->addUsers($q, $users, $userAnd);
        }
        if ($startAt) {
            $this->advancedSearchQueryBuilder->addStartAt($q, $startAt);
        }
        if ($endAt) {
            $this->advancedSearchQueryBuilder->addEndAt($q, $endAt);
        }
        $this->advancedSearchQueryBuilder->addOrder($q, $order, $direction);

        return $q;
    }

    public function paginateByAdvancedSearch(
        ?string $word = null,
        ?Collection $categories = null,
        ?bool $categoryAnd = true,
        ?Collection $tags = null,
        ?bool $tagAnd = true,
        ?Collection $users = null,
        ?bool $userAnd = true,
        ?CarbonImmutable $startAt = null,
        ?CarbonImmutable $endAt = null,
        string $order = 'updated_at',
        string $direction = 'desc',
        int $limit = 50
    ): LengthAwarePaginator {
        $q = $this->queryByAdvancedSearch(
            $word,
            $categories,
            $categoryAnd,
            $tags,
            $tagAnd,
            $users,
            $userAnd,
            $startAt,
            $endAt,
            $order,
            $direction
        );

        return $q->paginate($limit);
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
}
