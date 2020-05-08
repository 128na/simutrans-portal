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
use App\Services\PresentationService;
use Illuminate\Support\Facades\Auth;

class ArticleController extends Controller
{
    /**
     * @var ArticleService
     */
    private $article_service;
    /**
     * @var CategoryService
     */
    private $category_service;
    /**
     * @var PresentationService
     */
    private $presentation_service;

    public function __construct(
        ArticleService $article_service,
        CategoryService $category_service,
        PresentationService $presentation_service
    ) {
        $this->article_service = $article_service;
        $this->category_service = $category_service;
        $this->presentation_service = $presentation_service;
    }

    /**
     * アドオン記事一覧
     */
    public function addons()
    {
        $contents = ['articles' => $this->article_service->getAddonArticles()];
        $contents = array_merge($contents, $this->presentation_service->forList('Articles', $contents['articles']));

        return view('front.articles.index', $contents);
    }

    /**
     * アドオンランキング一覧
     */
    public function ranking()
    {
        $contents = ['articles' => $this->article_service->getRankingArticles()];
        $contents = array_merge($contents, $this->presentation_service->forList('Access Ranking', $contents['articles']));

        return view('front.articles.index', $contents);
    }

    /**
     * 一般記事一覧
     */
    public function pages()
    {
        $contents = ['articles' => $this->article_service->getCommonArticles()];
        $contents = array_merge($contents, $this->presentation_service->forList('Pages', $contents['articles']));

        return view('front.articles.index', $contents);
    }

    /**
     * 一般記事一覧
     */
    public function announces()
    {
        $contents = ['articles' => $this->article_service->getAnnouces()];
        $contents = array_merge($contents, $this->presentation_service->forList('Announces', $contents['articles']));

        return view('front.articles.index', $contents);
    }

    /**
     * 記事詳細
     */
    public function show(Article $article)
    {
        abort_unless($article->is_publish, 404);

        $is_owner = Auth::check() && Auth::user()->can('update', $article);

        if (!$is_owner) {
            event(new ArticleShown($article));
        }

        $contents = ['article' => $this->article_service->getArticle($article, $is_owner)];
        $contents = array_merge($contents, $this->presentation_service->forShow($article));

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
        $contents = ['articles' => $this->article_service->getCategoryArtciles($category)];
        $contents = array_merge($contents, $this->presentation_service->forCategory($category, $contents['articles']));

        return view('front.articles.index', $contents);
    }

    /**
     * カテゴリ(pak/addon)の投稿一覧画面
     */
    public function categoryPakAddon($pak_slug, $addon_slug)
    {
        $pak = $this->category_service->findOrFailByTypeAndSlug('pak', $pak_slug);
        $addon = $this->category_service->findOrFailByTypeAndSlug('addon', $addon_slug);

        $contents = ['articles' => $this->article_service->getPakAddonCategoryArtciles($pak, $addon)];
        $contents = array_merge($contents, $this->presentation_service->forPakAddon($pak, $addon, $contents['articles']));

        return view('front.articles.index', $contents);
    }

    /**
     * タグの投稿一覧画面
     */
    public function tag(Tag $tag)
    {
        $contents = ['articles' => $this->article_service->getTagArticles($tag)];
        $contents = array_merge($contents, $this->presentation_service->forTag($tag, $contents['articles']));

        return view('front.articles.index', $contents);
    }

    /**
     * ユーザーの投稿一覧画面
     */
    public function user(User $user)
    {
        $contents = ['articles' => $this->article_service->getUserArticles($user)];
        $contents = array_merge($contents, $this->presentation_service->forUser($user, $contents['articles']));

        return view('front.articles.index', $contents);
    }

    /**
     * 検索結果一覧
     */
    public function search(SearchRequest $request)
    {
        $contents = ['articles' => $this->article_service->getSearchArticles($request)];
        $contents = array_merge($contents, $this->presentation_service->forSearch($request, $contents['articles']));

        return view('front.articles.index', $contents);
    }
}
