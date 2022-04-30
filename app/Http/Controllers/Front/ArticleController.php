<?php

namespace App\Http\Controllers\Front;

use App\Events\ArticleConversion;
use App\Events\ArticleShown;
use App\Http\Controllers\Controller;
use App\Http\Requests\Article\SearchRequest;
use App\Models\Article;
use App\Models\Tag;
use App\Models\User;
use App\Repositories\ArticleRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\TagRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ArticleController extends Controller
{
    private ArticleRepository $articleRepository;
    private CategoryRepository $categoryRepository;
    private TagRepository $tagRepository;

    public function __construct(
        ArticleRepository $articleRepository,
        CategoryRepository $categoryRepository,
        TagRepository $tagRepository
    ) {
        $this->articleRepository = $articleRepository;
        $this->categoryRepository = $categoryRepository;
        $this->tagRepository = $tagRepository;
    }

    /**
     * アドオン記事一覧.
     */
    public function addons()
    {
        $contents = ['articles' => $this->articleRepository->paginateAddons()];

        return view('front.articles.index', $contents);
    }

    /**
     * アドオンランキング一覧.
     */
    public function ranking()
    {
        $contents = ['articles' => $this->articleRepository->paginateRanking()];

        return view('front.articles.index', $contents);
    }

    /**
     * 一般記事一覧.
     */
    public function pages()
    {
        $contents = ['articles' => $this->articleRepository->paginatePages()];

        return view('front.articles.index', $contents);
    }

    /**
     * 一般記事一覧.
     */
    public function announces()
    {
        $contents = ['articles' => $this->articleRepository->paginateAnnouces()];

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

        $contents = ['article' => $this->articleRepository->loadArticle($article, $isOwner)];

        return view('front.articles.show', $contents);
    }

    /**
     * アドオンダウンロード.
     */
    public function download(Article $article)
    {
        abort_unless($article->is_publish, 404);
        abort_unless($article->post_type === 'addon-post', 404);

        if (Auth::check() === false || Auth::id() !== $article->user_id) {
            event(new ArticleConversion($article));
        }

        abort_unless($article->has_file, 404);

        return Storage::disk('public')->download(
            $article->file->path,
            $article->file->original_name
        );
    }

    /**
     * カテゴリ(slug)の投稿一覧画面.
     */
    public function category(string $type, string $slug)
    {
        $category = $this->categoryRepository->findOrFailByTypeAndSlug($type, $slug);
        $contents = [
            'articles' => $this->articleRepository->paginateByCategory($category),
            'category' => $category,
        ];

        return view('front.articles.index', $contents);
    }

    /**
     * カテゴリ(pak/addon)の投稿一覧画面.
     */
    public function categoryPakAddon(string $pakSlug, string $addonSlug)
    {
        $pak = $this->categoryRepository->findOrFailByTypeAndSlug('pak', $pakSlug);
        $addon = $this->categoryRepository->findOrFailByTypeAndSlug('addon', $addonSlug);

        $contents = [
            'articles' => $this->articleRepository->paginateByPakAddonCategory($pak, $addon),
            'categories' => ['pak' => $pak, 'addon' => $addon],
        ];

        return view('front.articles.index', $contents);
    }

    /**
     * カテゴリ(pak,addon指定なし)の投稿一覧画面.
     */
    public function categoryPakNoneAddon(string $pakSlug)
    {
        $pak = $this->categoryRepository->findOrFailByTypeAndSlug('pak', $pakSlug);

        $contents = [
            'articles' => $this->articleRepository->paginateByPakNoneAddonCategory($pak),
            'categories' => ['pak' => $pak],
        ];

        return view('front.articles.index', $contents);
    }

    /**
     * タグの投稿一覧画面.
     */
    public function tag(Tag $tag)
    {
        $contents = ['articles' => $this->articleRepository->paginateByTag($tag), 'tag' => $tag];

        return view('front.articles.index', $contents);
    }

    /**
     * ユーザーの投稿一覧画面.
     */
    public function user(User $user)
    {
        $contents = ['articles' => $this->articleRepository->paginateByUser($user), 'user' => $user];

        return view('front.articles.index', $contents);
    }

    /**
     * 検索結果一覧.
     */
    public function search(SearchRequest $request)
    {
        $word = $request->word;
        $contents = ['articles' => $this->articleRepository->paginateBySearch($word), 'request' => $request];

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
