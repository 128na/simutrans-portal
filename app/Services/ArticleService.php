<?php
namespace App\Services;

use App\Http\Requests\Article\SearchRequest;
use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
use App\Models\User;

class ArticleService extends Service
{
    private $relations_for_listing = ['user:id,name', 'attachments:id,attachmentable_id,attachmentable_type,path', 'categories:id,type,slug'];
    private $article_columns = ['id', 'user_id', 'slug', 'title', 'post_type', 'contents', 'updated_at'];

    public function __construct(Article $model)
    {
        $this->model = $model;
        $this->per_page = 24;
    }

    public function getTopContents()
    {
        $announces = $this->getAnnouces(3);
        $pages = $this->getCommonArticles(3);
        $latest = [
            '64' => $this->getPakArticles('64', 6),
            '128' => $this->getPakArticles('128', 6),
            '128-japan' => $this->getPakArticles('128-japan', 6),
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
    private function limitOrPaginate($query, $limit = null)
    {
        return $limit ? $query->limit($limit)->get() : $query->paginate($this->per_page);
    }

    /**
     * お知らせ記事一覧
     */
    public function getAnnouces($limit = null)
    {
        $query = $this->model->select($this->article_columns)
            ->announce()
            ->active()
            ->withCache()
            ->with($this->relations_for_listing);

        return $this->limitOrPaginate($query, $limit);
    }
    /**
     * 一般記事一覧
     */
    public function getCommonArticles($limit = null)
    {
        $query = $this->model->select($this->article_columns)
            ->withoutAnnounce()
            ->active()
            ->withCache()
            ->with($this->relations_for_listing);

        return $this->limitOrPaginate($query, $limit);
    }
    /**
     * pak別の投稿一覧
     */
    public function getPakArticles($pak, $limit = null)
    {
        $query = $this->model->select($this->article_columns)
            ->pak($pak)
            ->addon()
            ->active()
            ->withCache()
            ->with($this->relations_for_listing);

        return $this->limitOrPaginate($query, $limit);
    }

    /**
     * アドオン投稿/紹介のデイリーPVランキング
     */
    public function getRankingArticles($excludes = [], $limit = null)
    {
        $query = $this->model->select($this->article_columns)
            ->addon()
            ->ranking()
            ->active()
            ->whereNotIn('articles.id', $excludes)
            ->withCache()
            ->with($this->relations_for_listing);

        return $this->limitOrPaginate($query, $limit);
    }

    /**
     * アドオン投稿/紹介の一覧
     */
    public function getAddonArticles($limit = null)
    {
        $query = $this->model->select($this->article_columns)
            ->addon()
            ->active()
            ->withCache()
            ->with($this->relations_for_listing);

        return $this->limitOrPaginate($query, $limit);
    }

    /**
     * カテゴリの投稿一覧
     */
    public function getCategoryArtciles(Category $category, $limit = null)
    {
        $query = $category->articles()
            ->select($this->article_columns)
            ->active()
            ->withCache()
            ->with($this->relations_for_listing);

        return $this->limitOrPaginate($query, $limit);
    }
    /**
     * カテゴリ(pak/addon)の投稿一覧
     */
    public function getPakAddonCategoryArtciles(Category $pak, Category $addon, $limit = null)
    {
        $query = $this->model
            ->select($this->article_columns)
            ->active()
            ->whereHas('categories', fn ($query) =>$query->where('id', $pak->id))
            ->whereHas('categories', fn ($query) =>$query->where('id', $addon->id))
            ->withCache()
            ->with($this->relations_for_listing);

        return $this->limitOrPaginate($query, $limit);
    }

    /**
     * タグを持つ投稿記事一覧
     */
    public function getTagArticles(Tag $tag, $limit = null)
    {
        $query = $tag->articles()
            ->select($this->article_columns)
            ->active()
            ->withCache()
            ->with($this->relations_for_listing);

        return $this->limitOrPaginate($query, $limit);
    }

    /**
     * ユーザーの投稿記事一覧
     */
    public function getUserArticles(User $user, $limit = null)
    {
        $query = $user->articles()
            ->select($this->article_columns)
            ->active()
            ->withCache()
            ->with($this->relations_for_listing);

        return $this->limitOrPaginate($query, $limit);
    }

    /**
     * 記事検索結果一覧
     */
    public function getSearchArticles(SearchRequest $request, $limit = null)
    {
        $query = $this->model
            ->select($this->article_columns)
            ->active()
            ->search($request->word)
            ->orderBy('updated_at', 'desc')
            ->withCache()
            ->with($this->relations_for_listing);

        return $this->limitOrPaginate($query, $limit);
    }

    /**
     * 記事表示
     */
    public function getArticle(Article $article, $with_count = false)
    {
        $relations = $with_count ? [
            'user:id,name', 'attachments:id,attachmentable_id,attachmentable_type,path', 'categories:id,type,slug', 'tags:id,name',
            'totalViewCount:article_id,count', 'totalConversionCount:article_id,count',
        ] : [
            'user:id,name', 'attachments:id,attachmentable_id,attachmentable_type,path', 'categories:id,type,slug', 'tags:id,name',
        ];

        return $article->load($relations);
    }
}
