<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Enums\ArticleAnalyticsType;
use App\Enums\ArticlePostType;
use App\Enums\ArticleStatus;
use App\Enums\CategoryType;
use App\Models\Article;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\LazyCollection;

final class ArticleRepository
{
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
            ->when($article, fn($q, Article $article) => $q->where('articles.id', '!=', $article->id))
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
            ->select('id', 'title', 'slug', 'status', 'post_type', 'published_at', 'modified_at')
            ->where('articles.user_id', $user->id);

        $this->orderByLatest($builder);

        return $builder
            ->with('totalConversionCount', 'totalViewCount')
            ->get();
    }

    /**
     * アナリティクス用記事一覧取得
     *
     * @return Collection<int,Article>
     */
    public function getForAnalyticsList(User $user): Collection
    {
        $builder = $this->model->query()
            ->select(['articles.id', 'articles.title', 'articles.published_at', 'articles.modified_at'])
            ->where('articles.user_id', $user->id);

        $this->wherePublished($builder);
        $this->orderByLatest($builder);

        return $builder
            ->with('totalConversionCount', 'totalViewCount')
            ->get();
    }

    /**
     * 個別記事取得
     */
    public function first(string $userIdOrNickname, string $slug): ?Article
    {
        $query = $this->model->query()
            ->select(['articles.*'])
            ->withoutGlobalScopes()
            ->where('articles.slug', urlencode($slug));

        $this->joinActiveUsers($query);
        $this->wherePublished($query);
        $this->orderByLatest($query);

        $query->with('categories', 'tags', 'attachments.fileInfo', 'user.profile.attachments', 'articles.user', 'relatedArticles.user');

        if (is_numeric($userIdOrNickname)) {
            $query->where('articles.user_id', $userIdOrNickname);
        } else {
            $query->where('users.nickname', $userIdOrNickname);
        }

        return $query->first();
    }

    /**
     * 検索
     *
     * @param array{
     *     word?: string,
     *     userIds?: int[],
     *     categoryIds?: int[],
     *     tagIds?: int[],
     *     postTypes?: string[]
     * } $condition
     * @return LengthAwarePaginator<int,Article>
     */
    public function search(array $condition, int $limit = 24): LengthAwarePaginator
    {
        if ($condition === []) {
            return new LengthAwarePaginator([], 0, 30);
        }

        $baseQuery = $this->model->query()
            ->select(['articles.*'])
            ->distinct()
            ->withoutGlobalScopes();

        $this->joinActiveUsers($baseQuery);
        $this->wherePublished($baseQuery);
        $this->orderByLatest($baseQuery);
        $this->withStandardRelations($baseQuery);

        // キーワード
        $rawWord = $condition['word'] ?? '';
        $words = array_filter(explode(
            ' ',
            str_replace(['　', ',', '、', '・'], ' ', $rawWord)
        ));
        if ($words !== []) {
            $queryString = implode(' ', array_map(fn(string $w): string => '+' . $w, $words));
            $baseQuery->join('article_search_index as idx', function (JoinClause $joinClause) use ($queryString): void {
                $joinClause->on('idx.article_id', '=', 'articles.id')
                    ->whereRaw('MATCH(idx.text) AGAINST (? IN BOOLEAN MODE)', [$queryString]);
            });
        }

        // ユーザー(OR)
        $userIds = $condition['userIds'] ?? [];
        if ($userIds !== []) {
            $baseQuery->whereIn('articles.user_id', $userIds);
        }

        // カテゴリ(AND)
        $categoryIds = $condition['categoryIds'] ?? [];
        if ($categoryIds !== []) {
            $baseQuery->whereIn('articles.id', function ($q) use ($categoryIds): void {
                $q->select('article_id')
                    ->from('article_category')
                    ->whereIn('category_id', $categoryIds)
                    ->groupBy('article_id')
                    ->havingRaw('COUNT(DISTINCT category_id) = ?', [count($categoryIds)]);
            });
        }

        // タグ(OR)
        $tagIds = $condition['tagIds'] ?? [];
        if ($tagIds !== []) {
            $baseQuery->whereExists(function ($q) use ($tagIds): void {
                $q->selectRaw(1)
                    ->from('article_tag as at')
                    ->whereColumn('at.article_id', 'articles.id')
                    ->whereIn('at.tag_id', $tagIds);
            });
        }

        // 投稿形式(OR)
        $postTypes = $condition['postTypes'] ?? [];
        if ($postTypes !== []) {
            $baseQuery->whereIn('articles.post_type', $postTypes);
        }

        return $baseQuery->paginate($limit);
    }

    /**
     * 新着記事一覧取得(PAK全体)
     *
     * @return LengthAwarePaginator<int,Article>
     */
    public function getLatestAllPak(int $limit = 24): LengthAwarePaginator
    {
        $query = $this->model->query()
            ->select(['articles.*'])
            ->distinct()
            ->withoutGlobalScopes();

        $this->joinActiveUsers($query);
        $this->wherePublished($query);
        $this->whereAddonPostTypes($query);
        $this->orderByLatest($query);
        $this->withStandardRelations($query);

        return $query->paginate($limit);
    }

    /**
     * 新着記事一覧取得(PAKごと)
     *
     * @return LengthAwarePaginator<int,Article>
     */
    public function getLatest(string $pak, int $limit = 24): LengthAwarePaginator
    {
        $query = $this->model->query()
            ->select(['articles.*'])
            ->distinct()
            ->withoutGlobalScopes();

        $this->joinActiveUsers($query);

        $query->join('article_category', 'articles.id', '=', 'article_category.article_id')
            ->join('categories', function (JoinClause $joinClause) use ($pak): void {
                $joinClause->on('article_category.category_id', '=', 'categories.id')
                    ->where('categories.type', CategoryType::Pak)
                    ->where('categories.slug', $pak);
            });

        $this->wherePublished($query);
        $this->whereAddonPostTypes($query);
        $this->orderByLatest($query);
        $this->withStandardRelations($query);

        return $query->paginate($limit);
    }

    /**
     * 一般記事一覧取得
     *
     * @return LengthAwarePaginator<int,Article>
     */
    public function getPages(int $limit = 24): LengthAwarePaginator
    {
        $query = $this->model->query()
            ->select(['articles.*'])
            ->distinct()
            ->withoutGlobalScopes();

        $this->joinActiveUsers($query);

        $query->join('article_category', 'articles.id', '=', 'article_category.article_id')
            ->join('categories', function (JoinClause $joinClause): void {
                $joinClause->on('article_category.category_id', '=', 'categories.id')
                    ->where('categories.type', CategoryType::Page)
                    ->where('categories.slug', '!=', 'announce');
            });

        $this->wherePublished($query);
        $this->wherePagePostTypes($query);
        $this->orderByLatest($query);
        $this->withStandardRelations($query);

        return $query->paginate($limit);
    }

    /**
     * その他のPAK記事一覧取得
     *
     * @return LengthAwarePaginator<int,Article>
     */
    public function getLatestOther(int $limit = 24): LengthAwarePaginator
    {
        $excludeSlugs = ['64', '128', '128-japan'];

        $query = $this->model->query()
            ->select('articles.*')
            ->withoutGlobalScopes();

        $this->joinActiveUsers($query);
        $this->wherePublished($query);
        $this->whereAddonPostTypes($query);

        $query->whereNotExists(function ($q) use ($excludeSlugs): void {
            $q->selectRaw(1)
                ->from('article_category as ac')
                ->join('categories as c', 'ac.category_id', '=', 'c.id')
                ->whereColumn('ac.article_id', 'articles.id')
                ->where('c.type', CategoryType::Pak)
                ->whereIn('c.slug', $excludeSlugs);
        });

        $this->orderByLatest($query);
        $this->withStandardRelations($query);

        return $query->paginate($limit);
    }

    /**
     * トップページ用アナウンス記事一覧取得
     *
     * @return Collection<int,Article>
     */
    public function getAnnouncesForTop(int $limit = 3): Collection
    {
        $query = $this->model->query()
            ->select('articles.*', 'users.nickname as user_nickname')
            ->distinct()
            ->withoutGlobalScopes();

        $this->joinActiveUsers($query);

        $query->join('article_category', 'articles.id', '=', 'article_category.article_id')
            ->join('categories', function (JoinClause $joinClause): void {
                $joinClause->on('article_category.category_id', '=', 'categories.id')
                    ->where('categories.type', CategoryType::Page)
                    ->where('categories.slug', 'announce');
            });

        $this->wherePublished($query);
        $this->wherePagePostTypes($query);
        $this->orderByLatest($query);

        return $query->limit($limit)->get();
    }

    /**
     * アナウンス記事一覧取得
     *
     * @return LengthAwarePaginator<int,Article>
     */
    public function getAnnounces(int $limit = 24): LengthAwarePaginator
    {
        $query = $this->model->query()
            ->select(['articles.*'])
            ->distinct()
            ->withoutGlobalScopes();

        $this->joinActiveUsers($query);

        $query->join('article_category', 'articles.id', '=', 'article_category.article_id')
            ->join('categories', function (JoinClause $joinClause): void {
                $joinClause->on('article_category.category_id', '=', 'categories.id')
                    ->where('categories.type', CategoryType::Page)
                    ->where('categories.slug', 'announce');
            });

        $this->wherePublished($query);
        $this->wherePagePostTypes($query);
        $this->orderByLatest($query);
        $this->withStandardRelations($query);

        return $query->paginate($limit);
    }

    /**
     * タグ別記事一覧取得
     *
     * @return LengthAwarePaginator<int,Article>
     */
    public function getByTag(int $tagId, int $limit = 24): LengthAwarePaginator
    {
        $query = $this->model->query()
            ->select(['articles.*'])
            ->distinct()
            ->withoutGlobalScopes();

        $this->joinActiveUsers($query);

        $query->join('article_tag', function (JoinClause $joinClause) use ($tagId): void {
            $joinClause->on('articles.id', '=', 'article_tag.article_id')
                ->where('article_tag.tag_id', $tagId);
        });

        $this->wherePublished($query);
        $this->whereAddonPostTypes($query);
        $this->orderByLatest($query);
        $this->withStandardRelations($query);

        return $query->paginate($limit);
    }

    /**
     * タグ別記事一覧取得
     *
     * @return LengthAwarePaginator<int,Article>
     */
    public function getForPakAddon(int $pakId, int $addonId, int $limit = 24): LengthAwarePaginator
    {
        $query = $this->model->query()
            ->select(['articles.*'])
            ->distinct()
            ->withoutGlobalScopes();

        $this->joinActiveUsers($query);

        $query->join('article_category as pak', function (JoinClause $joinClause) use ($pakId): void {
            $joinClause->on('pak.article_id', '=', 'articles.id')
                ->where('pak.category_id', $pakId);
        })
            ->join('article_category as addon', function (JoinClause $joinClause) use ($addonId): void {
                $joinClause->on('addon.article_id', '=', 'articles.id')
                    ->where('addon.category_id', $addonId);
            });

        $this->wherePublished($query);
        $this->whereAddonPostTypes($query);
        $this->orderByLatest($query);
        $this->withStandardRelations($query);

        return $query->paginate($limit);
    }

    /**
     * ユーザー別記事一覧取得
     *
     * @return LengthAwarePaginator<int,Article>
     */
    public function getByUser(int $userId, int $limit = 24): LengthAwarePaginator
    {
        $query = $this->model->query()
            ->select(['articles.*'])
            ->distinct()
            ->withoutGlobalScopes()
            ->where('articles.user_id', $userId);

        $this->joinActiveUsers($query);
        $this->wherePublished($query);
        $this->whereAddonPostTypes($query);
        $this->orderByLatest($query);
        $this->withStandardRelations($query);

        return $query->paginate($limit);
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
        [$from, $to] = $period;
        $sumQuery = function ($q) use ($articleAnalyticsType, $from): void {
            $q->where('type', $articleAnalyticsType)
                ->where('period', '<', $from);
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
            ->withSum(['viewCounts as past_view_count' => $sumQuery], 'count')
            ->withSum(['conversionCounts as past_conversion_count' => $sumQuery], 'count')
            ->orderBy('published_at', 'desc')
            ->get();
    }

    /**
     * @param  mixed[]  $data
     */
    public function store(array $data): Article
    {
        return $this->model->create($data);
    }

    /**
     * @param  mixed[]  $data
     */
    public function update(Article $article, array $data): Article
    {
        $article->update($data);

        return $article;
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
        /** @var Collection<int,\App\Models\Attachment> */
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
     * 削除済みユーザーを除外したusers JOINを追加
     *
     * @param  Builder<Article>  $builder
     * @return Builder<Article>
     */
    private function joinActiveUsers(Builder $builder): Builder
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
    private function wherePublished(Builder $builder): Builder
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
    private function whereAddonPostTypes(Builder $builder): Builder
    {
        return $builder->whereIn('articles.post_type', [ArticlePostType::AddonIntroduction, ArticlePostType::AddonPost]);
    }

    /**
     * ページ投稿タイプの条件を追加
     *
     * @param  Builder<Article>  $builder
     * @return Builder<Article>
     */
    private function wherePagePostTypes(Builder $builder): Builder
    {
        return $builder->whereIn('articles.post_type', [ArticlePostType::Page, ArticlePostType::Markdown]);
    }

    /**
     * 標準的な関連データの読み込み
     *
     * @param  Builder<Article>  $builder
     * @return Builder<Article>
     */
    private function withStandardRelations(Builder $builder): Builder
    {
        return $builder->with('categories', 'tags', 'attachments', 'user.profile.attachments');
    }

    /**
     * modified_atで降順ソート
     *
     * @param  Builder<Article>  $builder
     * @return Builder<Article>
     */
    private function orderByLatest(Builder $builder): Builder
    {
        return $builder->latest('articles.modified_at');
    }
}
