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
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;

class ArticleRepository extends BaseRepository
{
    private const COLUMNS = ['id', 'user_id', 'slug', 'title', 'post_type', 'contents', 'status', 'updated_at'];
    private const RELATIONS = ['user:id,name', 'attachments:id,attachmentable_id,attachmentable_type,path', 'categories:id,type,slug'];
    private const ORDER = ['updated_at', 'desc'];

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
    public function findAllByUser(User $user, array $columns = self::COLUMNS, array $relations = self::RELATIONS): Collection
    {
        return $user->articles()
            ->select($columns)
            ->with($relations)
            ->get();
    }

    private function basicQuery(array $columns = self::COLUMNS, array $relations = self::RELATIONS, array $order = self::ORDER): Builder
    {
        return $this->model->select($columns)
            ->active()
            ->withCache()
            ->with($relations)
            ->orderBy($order[0], $order[1]);
    }

    private function basicRelationQuery(Relation $query, array $columns = self::COLUMNS, array $relations = self::RELATIONS, array $order = self::ORDER): Relation
    {
        return $query->select($columns)
            ->active()
            ->withCache()
            ->with($relations)
            ->orderBy($order[0], $order[1]);
    }

    private function queryAnnouces(): Builder
    {
        return $this->basicQuery()
            ->announce();
    }

    /**
     * お知らせ記事一覧.
     */
    public function findAnnouces(?int $limit = null): Collection
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

    private function queryCommonArticles(): Builder
    {
        return $this->basicQuery()
            ->withoutAnnounce();
    }

    /**
     * 一般記事一覧.
     */
    public function findCommonArticles(?int $limit = null): Collection
    {
        return $this->queryCommonArticles()->limit($limit)->get();
    }

    /**
     * 一般記事一覧.
     */
    public function paginateCommonArticles(?int $limit = null): LengthAwarePaginator
    {
        return $this->queryCommonArticles()->paginate($limit);
    }

    private function queryPakArticles(string $pak): Builder
    {
        return $this->basicQuery()
            ->pak($pak)
            ->addon();
    }

    /**
     * pak別の投稿一覧.
     */
    public function findPakArticles(string $pak, ?int $limit = null): Collection
    {
        return $this->queryPakArticles($pak)->limit($limit)->get();
    }

    private function queryRankingArticles(array $excludes = []): Builder
    {
        return $this->basicQuery()
            ->addon()
            ->ranking()
            ->whereNotIn('articles.id', $excludes);
    }

    /**
     * アドオン投稿/紹介のデイリーPVランキング.
     */
    public function findRankingArticles(array $excludes = [], ?int $limit = null): Collection
    {
        return $this->queryRankingArticles($excludes)->limit($limit)->get();
    }

    /**
     * アドオン投稿/紹介のデイリーPVランキング.
     */
    public function paginateRankingArticles(array $excludes = [], ?int $limit = null): LengthAwarePaginator
    {
        return $this->queryRankingArticles($excludes)->paginate($limit);
    }

    private function queryAddonArticles(): Builder
    {
        return $this->basicQuery()
            ->addon()
            ->active();
    }

    /**
     * アドオン投稿/紹介の一覧.
     */
    public function paginateAddonArticles(?int $limit = null): LengthAwarePaginator
    {
        return $this->queryAddonArticles()->paginate($limit);
    }

    private function queryCategoryArtciles(Category $category): Relation
    {
        return $this->basicRelationQuery($category->articles());
    }

    /**
     * カテゴリの投稿一覧.
     */
    public function paginateCategoryArtciles(Category $category, ?int $limit = null): LengthAwarePaginator
    {
        return $this->queryCategoryArtciles($category)->paginate($limit);
    }

    private function queryPakAddonCategoryArtciles(Category $pak, Category $addon): Builder
    {
        return $this->basicQuery()
            ->whereHas('categories', fn ($query) => $query->where('id', $pak->id))
            ->whereHas('categories', fn ($query) => $query->where('id', $addon->id));
    }

    /**
     * カテゴリ(pak/addon)の投稿一覧.
     */
    public function paginatePakAddonCategoryArtciles(Category $pak, Category $addon, ?int $limit = null): LengthAwarePaginator
    {
        return $this->queryPakAddonCategoryArtciles($pak, $addon)->paginate($limit);
    }

    private function queryTagArticles(Tag $tag): Relation
    {
        return $this->basicRelationQuery($tag->articles());
    }

    /**
     * タグを持つ投稿記事一覧.
     */
    public function paginateTagArticles(Tag $tag, ?int $limit = null): LengthAwarePaginator
    {
        return $this->queryTagArticles($tag)->paginate($limit);
    }

    private function queryUserArticles(User $user): Relation
    {
        return $this->basicRelationQuery($user->articles());
    }

    /**
     * ユーザーの投稿記事一覧.
     */
    public function paginateUserArticles(User $user, ?int $limit = null): LengthAwarePaginator
    {
        return $this->queryUserArticles($user)->paginate($limit);
    }

    private function querySearchArticles(string $word): Builder
    {
        return $this->basicQuery()
            ->search($word)
            ->orderBy('updated_at', 'desc');
    }

    /**
     * 記事検索結果一覧.
     */
    public function paginateSearchArticles(string $word, ?int $limit = null): LengthAwarePaginator
    {
        return $this->querySearchArticles($word)->paginate($limit);
    }

    public function cursorCheckLinkArticles(): LazyCollection
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
    public function loadArticle(Article $article, bool $withCount = false): Article
    {
        $relations = $withCount ? [
            'user:id,name', 'attachments:id,attachmentable_id,attachmentable_type,path', 'categories:id,type,slug', 'tags:id,name',
            'totalViewCount:article_id,count', 'totalConversionCount:article_id,count',
        ] : [
            'user:id,name', 'attachments:id,attachmentable_id,attachmentable_type,path', 'categories:id,type,slug', 'tags:id,name',
        ];

        return $this->load($article, $relations);
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
    private function queryAdvancedSearch(
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
        $q = $this->basicQuery(self::COLUMNS, self::RELATIONS, [$order, $direction]);

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

    public function paginateAdvancedSearch(
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
        $q = $this->queryAdvancedSearch(
            $word, $categories, $categoryAnd, $tags, $tagAnd, $users, $userAnd, $startAt, $endAt, $order, $direction
        );

        return $q->paginate($limit);
    }
}
