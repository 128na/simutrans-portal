<?php

namespace App\Http\Controllers\Front;

use App\Events\ArticleConversion;
use App\Events\ArticleShown;
use App\Http\Controllers\Controller;
use App\Http\Requests\Article\SearchRequest;
use App\Models\Article;
use App\Models\Breadcrumb;
use App\Models\Category;
use App\Models\Tag;
use App\Models\User;
use App\Services\ArticleService;
use App\Services\CategoryService;
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

    public function __construct(ArticleService $article_service, CategoryService $category_service)
    {
        $this->article_service = $article_service;
        $this->category_service = $category_service;
    }

    /**
     * アドオン記事一覧
     */
    public function addons()
    {
        $contents = [
            'articles' => $this->article_service->getAddonArticles(),
            'title' => __('Articles'),
            'breadcrumb' => Breadcrumb::forList('Articles'),
        ];
        $contents = array_merge($contents, $this->article_service->getHeaderContents());

        return view('front.articles.index', $contents);
    }

    /**
     * アドオンランキング一覧
     */
    public function ranking()
    {
        $contents = [
            'articles' => $this->article_service->getRankingArticles(),
            'title' => __('Ranking'),
            'breadcrumb' => Breadcrumb::forList('Ranking'),
        ];
        $contents = array_merge($contents, $this->article_service->getHeaderContents());

        return view('front.articles.index', $contents);
    }

    /**
     * 一般記事一覧
     */
    public function pages()
    {
        $contents = [
            'articles' => $this->article_service->getCommonArticles(),
            'title' => __('Pages'),
            'breadcrumb' => Breadcrumb::forList('Pages'),
        ];
        $contents = array_merge($contents, $this->article_service->getHeaderContents());

        return view('front.articles.index', $contents);
    }

    /**
     * 一般記事一覧
     */
    public function announces()
    {
        $contents = [
            'articles' => $this->article_service->getAnnouces(),
            'title' => __('Announces'),
            'breadcrumb' => Breadcrumb::forList('Announces'),
        ];
        $contents = array_merge($contents, $this->article_service->getHeaderContents());

        return view('front.articles.index', $contents);
    }

    /**
     * 記事詳細
     */
    public function show(Article $article)
    {
        abort_unless($article->is_publish, 404);

        if (Auth::check() === false || Auth::id() !== $article->user_id) {
            event(new ArticleShown($article));
        }

        $contents = [
            'article' => $this->article_service->getArticle($article),
            'breadcrumb' => Breadcrumb::forShow($article),
            'canonical_url' => route('articles.show', $article->slug),
        ];
        $contents = array_merge($contents, $this->article_service->getHeaderContents());

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
            'title' => __('Category :name', ['name' => __("category.{$type}.{$slug}")]),
            'breadcrumb' => Breadcrumb::forCategory($type, $slug),
        ];
        $contents = array_merge($contents, $this->article_service->getHeaderContents());

        return view('front.articles.index', $contents);
    }

    /**
     * カテゴリ(pak/addon)の投稿一覧画面
     */
    public function categoryPakAddon($pak_slug, $addon_slug)
    {
        $pak_category = $this->category_service->findOrFailByTypeAndSlug('pak', $pak_slug);
        $addon_category = $this->category_service->findOrFailByTypeAndSlug('addon', $addon_slug);

        $contents = [
            'articles' => $this->article_service->getPakAddonCategoryArtciles($pak_category, $addon_category),
            'title' => __(':pak, :addon', [
                'pak' => __('category.pak.' . $pak_slug),
                'addon' => __('category.addon.' . $addon_slug),
            ]),
            'breadcrumb' => Breadcrumb::forPakAddon($pak_slug, $addon_slug),
        ];
        $contents = array_merge($contents, $this->article_service->getHeaderContents());

        return view('front.articles.index', $contents);
    }

    /**
     * タグの投稿一覧画面
     */
    public function tag(Tag $tag)
    {
        $contents = [
            'articles' => $this->article_service->getTagArticles($tag),
            'title' => __('Tag :name', ['name' => $tag->name]),
            'breadcrumb' => Breadcrumb::forTag($tag->name),
        ];
        $contents = array_merge($contents, $this->article_service->getHeaderContents());

        return view('front.articles.index', $contents);
    }

    /**
     * ユーザーの投稿一覧画面
     */
    public function user(User $user)
    {
        $contents = [
            'articles' => $this->article_service->getUserArticles($user),
            'title' => __('User :name', ['name' => $user->name]),
            'breadcrumb' => Breadcrumb::forUser($user),
            'user' => $user->load('profile', 'profile.attachments'),
        ];
        $contents = array_merge($contents, $this->article_service->getHeaderContents());

        return view('front.articles.index', $contents);
    }

    /**
     * 検索結果一覧
     */
    public function search(SearchRequest $request)
    {
        $contents = [
            'articles' => $this->article_service->getSearchArticles($request),
            'title' => __('Search results by :word', ['word' => $request->word]),
            'breadcrumb' => Breadcrumb::forSearch($request->word),
        ];
        $contents = array_merge($contents, $this->article_service->getHeaderContents());

        return view('front.articles.index', $contents);
    }
}
