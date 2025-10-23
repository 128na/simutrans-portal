<?php

declare(strict_types=1);

namespace App\Repositories\v2;

use App\Enums\ArticlePostType;
use App\Enums\ArticleStatus;
use App\Enums\CategoryType;
use App\Models\Article;
use Illuminate\Contracts\Database\Eloquent\Builder;

final class ArticleRepository
{
    public function __construct(public Article $model) {}

    public function first(string $userIdOrNickname, string $slug): ?Article
    {
        $query = $this->model->query()
            ->select(['articles.*'])
            ->withoutGlobalScopes()
            ->join('users', 'articles.user_id', '=', 'users.id')
            ->where('articles.status', ArticleStatus::Publish)
            ->where('articles.slug', urlencode($slug))
            ->whereNull('articles.deleted_at')
            ->whereNull('users.deleted_at')
            ->orderBy('articles.published_at', 'desc')
            ->with('categories', 'tags', 'attachments.fileInfo', 'user.profile.attachments', 'articles.user', 'relatedArticles.user');

        if (is_numeric($userIdOrNickname)) {
            $query->where('articles.user_id', $userIdOrNickname);
        } else {
            $query->where('users.nickname', $userIdOrNickname);
        }

        return $query->first();
    }

    /**
     * @param array{
     *     word?: string,
     *     userIds?: int[],
     *     categoryIds?: int[],
     *     tagIds?: int[],
     *     postTypes?: string[]
     * } $condition
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator<int,Article>
     */
    public function search(array $condition): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        if ($condition === []) {
            return new \Illuminate\Pagination\LengthAwarePaginator([], 0, 30);
        }

        $baseQuery = $this->model->query()
            ->select(['articles.*'])
            ->withoutGlobalScopes()
            ->join('users', 'articles.user_id', '=', 'users.id')
            ->where('articles.status', ArticleStatus::Publish)
            ->whereNull('articles.deleted_at')
            ->whereNull('users.deleted_at')
            ->orderByDesc('articles.published_at')
            ->with('categories', 'tags', 'attachments', 'user.profile.attachments');

        // キーワード
        $word = $condition['word'] ?? '';
        if ($word) {
            $likeWord = sprintf('%%%s%%', $word);
            $baseQuery->where(fn($q) => $q
                ->orWhere('title', 'LIKE', $likeWord)
                ->orWhere('contents', 'LIKE', $likeWord)
                ->orWhereHas(
                    'attachments.fileInfo',
                    fn($q) => $q
                        ->where('data', 'LIKE', $likeWord)
                ));
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

        return $baseQuery->paginate(30);
    }

    /**
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator<int,Article>
     */
    public function getLatest(string $pak): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return $this->model->query()
            ->select(['articles.*'])
            ->withoutGlobalScopes()
            ->join('users', 'articles.user_id', '=', 'users.id')
            ->join('article_category as ac', 'articles.id', '=', 'ac.article_id')
            ->join('categories as c', 'ac.category_id', '=', 'c.id')
            ->where('c.type', CategoryType::Pak)
            ->where('c.slug', $pak)
            ->where('articles.status', ArticleStatus::Publish)
            ->whereIn('articles.post_type', [ArticlePostType::AddonIntroduction, ArticlePostType::AddonPost])
            ->whereNull('articles.deleted_at')
            ->whereNull('users.deleted_at')
            ->orderBy('articles.published_at', 'desc')
            ->with('categories', 'tags', 'attachments', 'user.profile.attachments')
            ->paginate(30);
    }

    /**
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator<int,Article>
     */
    public function getLatestOther(): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $excludeSlugs = ['64', '128', '128-japan'];

        return $this->model->query()
            ->select('articles.*')
            ->withoutGlobalScopes()
            ->join('users', 'articles.user_id', '=', 'users.id')
            ->where('articles.status', ArticleStatus::Publish)
            ->whereNull('articles.deleted_at')
            ->whereNull('users.deleted_at')
            ->whereNotExists(function ($q) use ($excludeSlugs): void {
                $q->selectRaw(1)
                    ->from('article_category as ac')
                    ->join('categories as c', 'ac.category_id', '=', 'c.id')
                    ->whereColumn('ac.article_id', 'articles.id')
                    ->where('c.type', CategoryType::Pak)
                    ->whereIn('c.slug', $excludeSlugs);
            })
            ->orderByDesc('articles.published_at')
            ->with(['categories', 'tags', 'attachments', 'user.profile.attachments'])
            ->paginate(30);
    }

    /**
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator<int,Article>
     */
    public function getAnnounces(): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return $this->queryAnnounces()
            ->with(['categories', 'tags', 'attachments', 'user.profile.attachments'])
            ->paginate(30);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int,Article>
     */
    public function getTopAnnounces(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->queryAnnounces()
            ->limit(3)
            ->get();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder<Article>
     */
    private function queryAnnounces(): Builder
    {
        return $this->model->query()
            ->select('articles.*', 'users.nickname as user_nickname')
            ->withoutGlobalScopes()
            ->join('article_category as ac', 'articles.id', '=', 'ac.article_id')
            ->join('categories as c', 'ac.category_id', '=', 'c.id')
            ->join('users', 'articles.user_id', '=', 'users.id')
            ->where('articles.status', ArticleStatus::Publish)
            ->whereIn('articles.post_type', [ArticlePostType::Page, ArticlePostType::Markdown])
            ->whereNull('articles.deleted_at')
            ->where('c.type', CategoryType::Page)
            ->where('c.slug', 'announce')
            ->whereNull('users.deleted_at')
            ->orderBy('articles.published_at', 'desc');
    }
}
