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
use App\Repositories\CategoryRepository;
use App\Repositories\TagRepository;
use App\Services\ArticleService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ArticleController extends Controller
{
    private ArticleService $articleService;
    private CategoryRepository $categoryRepository;
    private TagRepository $tagRepository;

    public function __construct(
        ArticleService $articleService,
        CategoryRepository $categoryRepository,
        TagRepository $tagRepository
    ) {
        $this->articleService = $articleService;
        $this->categoryRepository = $categoryRepository;
        $this->tagRepository = $tagRepository;
    }

    /**
     * アドオン記事一覧.
     */
    public function addons()
    {
        $contents = ['articles' => $this->articleService->getAddonArticles()];

        return view('front.articles.index', $contents);
    }

    /**
     * アドオンランキング一覧.
     */
    public function ranking()
    {
        $contents = ['articles' => $this->articleService->getRankingArticles()];

        return view('front.articles.index', $contents);
    }

    /**
     * 一般記事一覧.
     */
    public function pages()
    {
        $contents = ['articles' => $this->articleService->getCommonArticles()];

        return view('front.articles.index', $contents);
    }

    /**
     * 一般記事一覧.
     */
    public function announces()
    {
        $contents = ['articles' => $this->articleService->getAnnouces()];

        return view('front.articles.index', $contents);
    }

    /**
     * 記事詳細.
     */
    public function show(Article $article)
    {
        abort_unless($article->is_publish, 404);

        $isOwner = Auth::check() && Auth::user()->can('update', $article);

        if (!$isOwner) {
            event(new ArticleShown($article));
        }

        $contents = ['article' => $this->articleService->getArticle($article, $isOwner)];

        return view('front.articles.show', $contents);
    }

    /**
     * アドオンダウンロード.
     */
    public function download(Article $article)
    {
        abort_unless($article->is_publish, 404);

        if (Auth::check() === false || Auth::id() !== $article->user_id) {
            event(new ArticleConversion($article));
        }

        abort_unless($article->has_file, 404);

        return response()->download(
            Storage::disk('public')->path($article->file->path),
            $article->file->original_name
        );
    }

    /**
     * カテゴリ(slug)の投稿一覧画面.
     */
    public function category($type, $slug)
    {
        $category = $this->categoryRepository->findOrFailByTypeAndSlug($type, $slug);
        $contents = [
            'articles' => $this->articleService->getCategoryArtciles($category),
            'category' => $category,
        ];

        return view('front.articles.index', $contents);
    }

    /**
     * カテゴリ(pak/addon)の投稿一覧画面.
     */
    public function categoryPakAddon($pak_slug, $addon_slug)
    {
        $pak = $this->categoryRepository->findOrFailByTypeAndSlug('pak', $pak_slug);
        $addon = $this->categoryRepository->findOrFailByTypeAndSlug('addon', $addon_slug);

        $contents = [
            'articles' => $this->articleService->getPakAddonCategoryArtciles($pak, $addon),
            'categories' => ['pak' => $pak, 'addon' => $addon],
        ];

        return view('front.articles.index', $contents);
    }

    /**
     * タグの投稿一覧画面.
     */
    public function tag(Tag $tag)
    {
        $contents = ['articles' => $this->articleService->getTagArticles($tag), 'tag' => $tag];

        return view('front.articles.index', $contents);
    }

    /**
     * ユーザーの投稿一覧画面.
     */
    public function user(User $user)
    {
        $contents = ['articles' => $this->articleService->getUserArticles($user), 'user' => $user];

        return view('front.articles.index', $contents);
    }

    /**
     * 検索結果一覧.
     */
    public function search(SearchRequest $request)
    {
        $contents = ['articles' => $this->articleService->getSearchArticles($request), 'request' => $request];

        return view('front.articles.index', $contents);
    }

    /**
     * タグ一覧.
     */
    public function tags()
    {
        $contents = ['tags' => $this->tagRepository->getAllTags()];

        return view('front.tags', $contents);
    }
}
