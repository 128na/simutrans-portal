<?php

declare(strict_types=1);

namespace App\Repositories\Article;

use App\Enums\ArticlePostType;
use App\Enums\ArticleStatus;
use App\Enums\CategoryType;
use App\Models\Article;
use App\Repositories\Concerns\ArticleQueryConcern;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Pagination\LengthAwarePaginator;

class FrontArticleRepository
{
    use ArticleQueryConcern;

    public function __construct(public Article $model) {}

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
            $queryString = implode(' ', array_map(fn (string $w): string => '+'.$w, $words));
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
     * 新着記事一覧取得(PAKごと・ページネーション)
     *
     * @return LengthAwarePaginator<int,Article>
     */
    public function paginateLatest(string $pak, int $limit = 24): LengthAwarePaginator
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
     * 新着記事一覧取得(PAKごと)
     *
     * @return Collection<int,Article>
     */
    public function getLatest(string $pak, int $limit = 24): Collection
    {
        $query = $this->model->query()
            ->select(['articles.*', 'users.nickname as user_nickname'])
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
        $query->limit($limit);

        return $query->get();
    }

    /**
     * 一般記事一覧取得(ページネーション)
     *
     * @return LengthAwarePaginator<int,Article>
     */
    public function paginatePages(int $limit = 24): LengthAwarePaginator
    {
        $query = $this->model->query()
            ->select(['articles.*'])
            ->distinct()
            ->withoutGlobalScopes();

        $this->joinActiveUsers($query);

        $query->whereNotExists(function ($subQuery): void {
            $subQuery->select('article_category.article_id')
                ->from('article_category')
                ->join('categories', 'article_category.category_id', '=', 'categories.id')
                ->whereColumn('article_category.article_id', 'articles.id')
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
     * 一般記事一覧取得
     *
     * @return Collection<int,Article>
     */
    public function getPages(int $limit = 24): Collection
    {
        $query = $this->model->query()
            ->select(['articles.*', 'users.nickname as user_nickname'])
            ->distinct()
            ->withoutGlobalScopes();

        $this->joinActiveUsers($query);

        $query->whereNotExists(function ($subQuery): void {
            $subQuery->select('article_category.article_id')
                ->from('article_category')
                ->join('categories', 'article_category.category_id', '=', 'categories.id')
                ->whereColumn('article_category.article_id', 'articles.id')
                ->where('categories.type', CategoryType::Page)
                ->where('categories.slug', 'announce');
        });

        $this->wherePublished($query);
        $this->wherePagePostTypes($query);
        $this->orderByLatest($query);
        $query->limit($limit);

        return $query->get();
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
     * トップページ用アナウンス記事のみを取得
     *
     * @return array{announces: Collection<int,Article>}
     */
    public function getTopPageArticles(int $announcesLimit = 3): array
    {
        $results = $this->buildTopPageQuery(
            'announces',
            CategoryType::Page,
            'announce',
            '=',
            [ArticlePostType::Page, ArticlePostType::Markdown],
            $announcesLimit
        )->get();

        return [
            'announces' => $results->where('article_type', 'announces')->values(),
        ];
    }

    /**
     * アナウンス記事一覧取得
     *
     * @return Collection<int,Article>
     */
    public function getAnnounces(int $limit = 3): Collection
    {
        $query = $this->model->query()
            ->select(['articles.*', 'users.nickname as user_nickname'])
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
     * アナウンス記事一覧取得(ページネーション)
     *
     * @return LengthAwarePaginator<int,Article>
     */
    public function paginateAnnounces(int $limit = 24): LengthAwarePaginator
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
     * PAK+アドオンカテゴリ別記事一覧取得
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
     * トップページ用の個別クエリを構築
     *
     * @param  array<ArticlePostType>  $postTypes
     * @return Builder<Article>
     */
    private function buildTopPageQuery(
        string $articleType,
        CategoryType $categoryType,
        string $categorySlug,
        string $categoryOperator,
        array $postTypes,
        int $limit
    ): Builder {
        return $this->model->query()
            ->select(['articles.id', 'articles.title', 'articles.user_id', 'articles.modified_at', 'articles.published_at', 'articles.slug', 'users.nickname as user_nickname'])
            ->selectRaw('? as article_type', [$articleType])
            ->distinct()
            ->withoutGlobalScopes()
            ->join('users', 'articles.user_id', '=', 'users.id')
            ->join('article_category', 'articles.id', '=', 'article_category.article_id')
            ->join('categories', function (JoinClause $joinClause) use ($categoryType, $categorySlug, $categoryOperator): void {
                $joinClause->on('article_category.category_id', '=', 'categories.id')
                    ->where('categories.type', $categoryType)
                    ->where('categories.slug', $categoryOperator, $categorySlug);
            })
            ->where('articles.status', ArticleStatus::Publish)
            ->whereNull('articles.deleted_at')
            ->whereNull('users.deleted_at')
            ->whereIn('articles.post_type', $postTypes)
            ->latest('articles.modified_at')
            ->limit($limit);
    }
}
