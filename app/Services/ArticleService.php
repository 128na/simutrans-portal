<?php

namespace App\Services;

use App\Http\Requests\Article\SearchRequest;
use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
use App\Models\User;
use App\Repositories\ArticleRepository;
use Illuminate\Support\Collection;

class ArticleService extends Service
{
    private ArticleRepository $articleRepository;

    public function __construct(ArticleRepository $articleRepository)
    {
        $this->articleRepository = $articleRepository;
    }

    public function getTopContents(): array
    {
        $announces = $this->getAnnouces(3);
        $pages = $this->getCommonArticles(3);
        $latest = [
            '128-japan' => $this->getPakArticles('128-japan', 6),
            '128' => $this->getPakArticles('128', 6),
            '64' => $this->getPakArticles('64', 6),
        ];
        $excludes = collect($latest)->flatten()->pluck('id')->unique()->toArray();
        $ranking = $this->getRankingArticles($excludes, 6);

        return [
            'announces' => $announces,
            'pages' => $pages,
            'latest' => $latest,
            'ranking' => $ranking,
        ];
    }

    /**
     * お知らせ記事一覧.
     */
    public function getAnnouces(?int $limit = null): Collection
    {
        return $this->articleRepository->queryAnnouces()->limit($limit)->get();
    }

    /**
     * 一般記事一覧.
     */
    public function getCommonArticles(?int $limit = null): Collection
    {
        return $this->articleRepository->queryCommonArticles()->limit($limit)->get();
    }

    /**
     * pak別の投稿一覧.
     */
    public function getPakArticles(string $pak, ?int $limit = null): Collection
    {
        return $this->articleRepository->queryPakArticles($pak)->limit($limit)->get();
    }

    /**
     * アドオン投稿/紹介のデイリーPVランキング.
     */
    public function getRankingArticles(array $excludes = [], ?int $limit = null): Collection
    {
        return $this->articleRepository->queryRankingArticles($excludes)->limit($limit)->get();
    }

    /**
     * アドオン投稿/紹介の一覧.
     */
    public function getAddonArticles(?int $limit = null): Collection
    {
        return $this->articleRepository->queryAddonArticles()->limit($limit)->get();
    }

    /**
     * カテゴリの投稿一覧.
     */
    public function getCategoryArtciles(Category $category, ?int $limit = null): Collection
    {
        return $this->articleRepository->queryCategoryArtciles($category)->limit($limit)->get();
    }

    /**
     * カテゴリ(pak/addon)の投稿一覧.
     */
    public function getPakAddonCategoryArtciles(Category $pak, Category $addon, ?int $limit = null): Collection
    {
        return $this->articleRepository->queryPakAddonCategoryArtciles($pak, $addon)->limit($limit)->get();
    }

    /**
     * タグを持つ投稿記事一覧.
     */
    public function getTagArticles(Tag $tag, ?int $limit = null): Collection
    {
        return $this->articleRepository->queryTagArticles($tag)->limit($limit)->get();
    }

    /**
     * ユーザーの投稿記事一覧.
     */
    public function getUserArticles(User $user, ?int $limit = null): Collection
    {
        return $this->articleRepository->queryUserArticles($user)->limit($limit)->get();
    }

    /**
     * 記事検索結果一覧.
     */
    public function getSearchArticles(SearchRequest $request, ?int $limit = null): Collection
    {
        $word = $request->input('word');

        return $this->articleRepository->querySearchArticles($word)->limit($limit)->get();
    }

    /**
     * 記事表示.
     */
    public function getArticle(Article $article, bool $withCount = false): Article
    {
        $relations = $withCount ? [
            'user:id,name', 'attachments:id,attachmentable_id,attachmentable_type,path', 'categories:id,type,slug', 'tags:id,name',
            'totalViewCount:article_id,count', 'totalConversionCount:article_id,count',
        ] : [
            'user:id,name', 'attachments:id,attachmentable_id,attachmentable_type,path', 'categories:id,type,slug', 'tags:id,name',
        ];

        return $this->articleRepository->load($article, $relations);
    }
}
