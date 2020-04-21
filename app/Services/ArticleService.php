<?php
namespace App\Services;

use App\Http\Requests\Article\SearchRequest;
use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
use App\Models\User;

class ArticleService extends Service
{
    private $relations_for_listing = ['user', 'attachments', 'categories', 'tags'];

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

    /**
     * お知らせ記事一覧
     */
    public function getAnnouces($limit = null)
    {
        $query = $this->model->announce()->active()->with($this->relations_for_listing);

        return $limit ? $query->limit(3)->get() : $query->paginate($this->per_page);
    }
    /**
     * 一般記事一覧
     */
    public function getCommonArticles($limit = null)
    {
        $query = $this->model->withoutAnnounce()->active()->with($this->relations_for_listing);

        return $limit ? $query->limit($limit)->get() : $query->paginate($this->per_page);
    }
    /**
     * pak別の投稿一覧
     */
    public function getPakArticles($pak, $limit = null)
    {
        $query = $this->model->pak($pak)->addon()->active()->with($this->relations_for_listing);

        return $limit ? $query->limit($limit)->get() : $query->paginate($this->per_page);
    }

    /**
     * アドオン投稿/紹介のデイリーPVランキング
     */
    public function getRankingArticles($excludes = [], $limit = null)
    {
        $query = $this->model->addon()->ranking()->active()->whereNotIn('articles.id', $excludes)->with($this->relations_for_listing);

        return $limit ? $query->limit($limit)->get() : $query->paginate($this->per_page);
    }

    /**
     * アドオン投稿/紹介の一覧
     */
    public function getAddonArticles()
    {
        return $this->model->addon()->active()->with($this->relations_for_listing)->paginate($this->per_page);
    }

    /**
     * カテゴリの投稿一覧
     */
    public function getCategoryArtciles(Category $category)
    {
        return $category->articles()
            ->active()
            ->with($this->relations_for_listing)
            ->paginate($this->per_page);
    }
    /**
     * カテゴリ(pak/addon)の投稿一覧
     */
    public function getPakAddonCategoryArtciles(Category $pak, Category $addon)
    {
        return $this->model
            ->active()
            ->with($this->relations_for_listing)
            ->whereHas('categories', function ($query) use ($pak) {
                $query->where('id', $pak->id);
            })
            ->whereHas('categories', function ($query) use ($addon) {
                $query->where('id', $addon->id);
            })
            ->paginate($this->per_page);
    }

    /**
     * タグを持つ投稿記事一覧
     */
    public function getTagArticles(Tag $tag)
    {
        return $tag->articles()
            ->active()
            ->with($this->relations_for_listing)
            ->paginate($this->per_page);
    }

    /**
     * ユーザーの投稿記事一覧
     */
    public function getUserArticles(User $user)
    {
        return $user->articles()
            ->active()
            ->with($this->relations_for_listing)
            ->paginate($this->per_page);

    }

    /**
     * 記事検索結果一覧
     */
    public function getSearchArticles(SearchRequest $request)
    {
        return $this->model
            ->active()
            ->search($request->word)
            ->with($this->relations_for_listing)
            ->orderBy('updated_at', 'desc')
            ->paginate($this->per_page);
    }

    /**
     * 記事表示
     */
    public function getArticle(Article $article, $with_count = false)
    {
        $relations = $with_count ? [
            'user', 'attachments', 'categories', 'tags',
            'totalViewCount', 'totalConversionCount',
        ] : [
            'user', 'attachments', 'categories', 'tags',
        ];
        return $article->load($relations);
    }

}
