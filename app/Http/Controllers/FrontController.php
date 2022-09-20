<?php

namespace App\Http\Controllers;

use App\Events\ArticleConversion;
use App\Http\Requests\Article\SearchRequest;
use App\Models\Article;
use App\Models\Tag;
use App\Models\User;
use App\Repositories\ArticleRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\TagRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class FrontController extends Controller
{
    public function __construct(
        private ArticleRepository $articleRepository,
        private CategoryRepository $categoryRepository,
        private TagRepository $tagRepository
    ) {
    }

    /**
     * SPA用フロント.
     */
    public function fallback()
    {
        return view('front.spa');
    }

    /**
     * 記事詳細.
     */
    public function show(Article $article)
    {
        abort_unless($article->is_publish, 404);

        return view('front.spa');
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

        return view('front.spa');
    }

    /**
     * カテゴリ(pak/addon)の投稿一覧画面.
     */
    public function categoryPakAddon(string $pakSlug, string $addonSlug)
    {
        $pak = $this->categoryRepository->findOrFailByTypeAndSlug('pak', $pakSlug);
        $addon = $this->categoryRepository->findOrFailByTypeAndSlug('addon', $addonSlug);

        return view('front.spa');
    }

    /**
     * カテゴリ(pak,addon指定なし)の投稿一覧画面.
     */
    public function categoryPakNoneAddon(string $pakSlug)
    {
        $pak = $this->categoryRepository->findOrFailByTypeAndSlug('pak', $pakSlug);

        return view('front.spa');
    }

    /**
     * タグの投稿一覧画面.
     */
    public function tag(Tag $tag)
    {
        return view('front.spa');
    }

    /**
     * ユーザーの投稿一覧画面.
     */
    public function user(User $user)
    {
        return view('front.spa');
    }

    /**
     * 検索結果一覧.
     */
    public function search(SearchRequest $request)
    {
        return view('front.spa');
    }
}
