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
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\LazyCollection;

final class ArticleRepository
{
    public function __construct(public Article $model) {}

    /**
     * @return Collection<int,Article>
     */
    public function getForEdit(?Article $article = null): Collection
    {
        return $this->model->query()
            ->select(['articles.id', 'articles.title', 'articles.user_id', 'u.name as user_name'])
            ->join('users as u', 'articles.user_id', '=', 'u.id')
            ->where('articles.status', ArticleStatus::Publish)
            ->when($article, fn($q) => $q->where('articles.id', '!=', $article->id))
            ->whereNull('articles.deleted_at')
            ->whereNull('u.deleted_at')
            ->latest('articles.modified_at')
            ->get();
    }

    /**
     * @return Collection<int,Article>
     */
    public function getForAnalyticsList(User $user): Collection
    {
        return $this->model->query()
            ->select(['articles.id', 'articles.title', 'articles.published_at', 'articles.modified_at'])
            ->where('articles.status', ArticleStatus::Publish)
            ->where('articles.user_id', $user->id)
            ->whereNull('articles.deleted_at')
            ->latest('articles.modified_at')
            ->get();
    }

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
            ->orderBy('articles.modified_at', 'desc')
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
     * @return LengthAwarePaginator<int,Article>
     */
    public function search(array $condition, int $limit = 24): LengthAwarePaginator
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
            ->orderByDesc('articles.modified_at')
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

        return $baseQuery->paginate($limit);
    }

    /**
     * @return LengthAwarePaginator<int,Article>
     */
    public function getLatestAllPak(int $limit = 24): LengthAwarePaginator
    {
        return $this->model->query()
            ->select(['articles.*'])
            ->withoutGlobalScopes()
            ->join('users', 'articles.user_id', '=', 'users.id')
            ->where('articles.status', ArticleStatus::Publish)
            ->whereIn('articles.post_type', [ArticlePostType::AddonIntroduction, ArticlePostType::AddonPost])
            ->whereNull('articles.deleted_at')
            ->whereNull('users.deleted_at')
            ->orderBy('articles.modified_at', 'desc')
            ->with('categories', 'tags', 'attachments', 'user.profile.attachments')
            ->paginate($limit);
    }

    /**
     * @return LengthAwarePaginator<int,Article>
     */
    public function getLatest(string $pak, int $limit = 24): LengthAwarePaginator
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
            ->orderBy('articles.modified_at', 'desc')
            ->with('categories', 'tags', 'attachments', 'user.profile.attachments')
            ->paginate($limit);
    }

    /**
     * @return LengthAwarePaginator<int,Article>
     */
    public function getPages(int $limit = 24): LengthAwarePaginator
    {
        return $this->model->query()
            ->select(['articles.*'])
            ->withoutGlobalScopes()
            ->join('users', 'articles.user_id', '=', 'users.id')
            ->join('article_category as ac', 'articles.id', '=', 'ac.article_id')
            ->join('categories as c', 'ac.category_id', '=', 'c.id')
            ->where('c.type', CategoryType::Page)
            ->where('c.slug', '!=', 'announce')
            ->where('articles.status', ArticleStatus::Publish)
            ->whereIn('articles.post_type', [ArticlePostType::Page, ArticlePostType::Markdown])
            ->whereNull('articles.deleted_at')
            ->whereNull('users.deleted_at')
            ->orderBy('articles.modified_at', 'desc')
            ->with('categories', 'tags', 'attachments', 'user.profile.attachments')
            ->paginate($limit);
    }

    /**
     * @return LengthAwarePaginator<int,Article>
     */
    public function getLatestOther(int $limit = 24): LengthAwarePaginator
    {
        $excludeSlugs = ['64', '128', '128-japan'];

        return $this->model->query()
            ->select('articles.*')
            ->withoutGlobalScopes()
            ->join('users', 'articles.user_id', '=', 'users.id')
            ->where('articles.status', ArticleStatus::Publish)
            ->whereIn('articles.post_type', [ArticlePostType::AddonIntroduction, ArticlePostType::AddonPost])
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
            ->orderByDesc('articles.modified_at')
            ->with(['categories', 'tags', 'attachments', 'user.profile.attachments'])
            ->paginate($limit);
    }

    /**
     * @return LengthAwarePaginator<int,Article>
     */
    public function getAnnounces(int $limit = 24): LengthAwarePaginator
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
            ->orderBy('articles.modified_at', 'desc')
            ->with(['categories', 'tags', 'attachments', 'user.profile.attachments'])
            ->paginate($limit);
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

    public function store(array $data): Article
    {
        return $this->model->create($data);
    }

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
}
