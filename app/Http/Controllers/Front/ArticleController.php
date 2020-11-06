<?php

namespace App\Http\Controllers\Front;

use App\Events\ArticleConversion;
use App\Events\ArticleShown;
use App\Http\Controllers\Controller;
use App\Http\Requests\Article\SearchRequest;
use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
use App\Models\User;
use App\Services\ArticleService;
use App\Services\CategoryService;
use App\Services\TagService;
use Illuminate\Support\Facades\Auth;

class ArticleController extends Controller
{
    private ArticleService $article_service;
    private CategoryService $category_service;
    private TagService $tag_service;

    public function __construct(
        ArticleService $article_service,
        CategoryService $category_service,
        TagService $tag_service
    ) {
        $this->article_service = $article_service;
        $this->category_service = $category_service;
        $this->tag_service = $tag_service;
    }

    /**
     * アドオン記事一覧
     */
    public function addons()
    {
        $contents = ['articles' => $this->article_service->getAddonArticles()];
        return view('front.articles.index', $contents);
    }

    /**
     * アドオンランキング一覧
     */
    public function ranking()
    {
        $contents = ['articles' => $this->article_service->getRankingArticles()];
        return view('front.articles.index', $contents);
    }

    /**
     * 一般記事一覧
     */
    public function pages()
    {
        $contents = ['articles' => $this->article_service->getCommonArticles()];
        return view('front.articles.index', $contents);
    }

    /**
     * 一般記事一覧
     */
    public function announces()
    {
        $contents = ['articles' => $this->article_service->getAnnouces()];
        return view('front.articles.index', $contents);
    }

    /**
     * 記事詳細
     */
    public function show(Article $article)
    {
        abort_unless($article->user, 404);
        abort_unless($article->is_publish, 404);

        $is_owner = Auth::check() && Auth::user()->can('update', $article);

        if (!$is_owner) {
            event(new ArticleShown($article));
        }

        $contents = ['article' => $this->article_service->getArticle($article, $is_owner)];
        return view('front.articles.show', $contents);
    }

    /**
     * アドオンダウンロード
     */
    public function download(Article $article)
    {
        abort_unless($article->is_publish, 404);

        if (Auth::check() === false || Auth::id() !== $article->user_id) {
            event(new ArticleConversion($article));
        }

        abort_unless($article->has_file, 404);

        return response()
            ->download(
                public_path('storage/' . $article->file->path),
                $article->file->original_name
            );
    }

    /**
     * カテゴリ(slug)の投稿一覧画面
     */
    public function category($type, $slug)
    {
        $category = $this->category_service->findOrFailByTypeAndSlug($type, $slug);
        $contents = [
            'articles' => $this->article_service->getCategoryArtciles($category),
            'category' => $category
        ];
        return view('front.articles.index', $contents);
    }

    /**
     * カテゴリ(pak/addon)の投稿一覧画面
     */
    public function categoryPakAddon($pak_slug, $addon_slug)
    {
        $pak = $this->category_service->findOrFailByTypeAndSlug('pak', $pak_slug);
        $addon = $this->category_service->findOrFailByTypeAndSlug('addon', $addon_slug);

        $contents = [
            'articles' => $this->article_service->getPakAddonCategoryArtciles($pak, $addon),
            'categories' => ['pak' => $pak, 'addon' => $addon]
        ];
        return view('front.articles.index', $contents);
    }

    /**
     * タグの投稿一覧画面
     */
    public function tag(Tag $tag)
    {
        $contents = ['articles' => $this->article_service->getTagArticles($tag), 'tag' => $tag];
        return view('front.articles.index', $contents);
    }

    /**
     * ユーザーの投稿一覧画面
     */
    public function user(User $user)
    {
        $contents = ['articles' => $this->article_service->getUserArticles($user), 'user' => $user];
        return view('front.articles.index', $contents);
    }

    /**
     * 検索結果一覧
     */
    public function search(SearchRequest $request)
    {
        $contents = ['articles' => $this->article_service->getSearchArticles($request), 'request' => $request];
        return view('front.articles.index', $contents);
    }

    /**
     * タグ一覧
     */
    public function tags()
    {
        $contents = ['tags' => $this->tag_service->getAllTags()];
        return view('front.tags', $contents);
    }
}
