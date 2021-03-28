<?php

namespace App\Repositories;

use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
use App\Models\User;
use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
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
    public function finaAllByUser(User $user, array $columns = self::COLUMNS, array $relations = self::RELATIONS): Collection
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

    /**
     * お知らせ記事一覧.
     */
    public function queryAnnouces(): Builder
    {
        return $this->basicQuery()
            ->announce();
    }

    /**
     * 一般記事一覧.
     */
    public function queryCommonArticles(): Builder
    {
        return $this->basicQuery()
            ->withoutAnnounce();
    }

    /**
     * pak別の投稿一覧.
     */
    public function queryPakArticles(string $pak): Builder
    {
        return $this->basicQuery()
            ->pak($pak)
            ->addon();
    }

    /**
     * アドオン投稿/紹介のデイリーPVランキング.
     */
    public function queryRankingArticles(array $excludes = []): Builder
    {
        return $this->basicQuery()
            ->addon()
            ->ranking()
            ->whereNotIn('articles.id', $excludes);
    }

    /**
     * アドオン投稿/紹介の一覧.
     */
    public function queryAddonArticles(): Builder
    {
        return $this->basicQuery()
            ->addon()
            ->active();
    }

    /**
     * カテゴリの投稿一覧.
     */
    public function queryCategoryArtciles(Category $category): Relation
    {
        return $this->basicRelationQuery($category->articles());
    }

    /**
     * カテゴリ(pak/addon)の投稿一覧.
     */
    public function queryPakAddonCategoryArtciles(Category $pak, Category $addon): Builder
    {
        return $this->basicQuery()
            ->whereHas('categories', fn ($query) => $query->where('id', $pak->id))
            ->whereHas('categories', fn ($query) => $query->where('id', $addon->id));
    }

    /**
     * タグを持つ投稿記事一覧.
     */
    public function queryTagArticles(Tag $tag): Relation
    {
        return $this->basicRelationQuery($tag->articles());
    }

    /**
     * ユーザーの投稿記事一覧.
     */
    public function queryUserArticles(User $user): Relation
    {
        return $this->basicRelationQuery($user->articles());
    }

    /**
     * 記事検索結果一覧.
     */
    public function querySearchArticles(string $word): Builder
    {
        return $this->basicQuery()
            ->search($word)
            ->orderBy('updated_at', 'desc');
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
}
