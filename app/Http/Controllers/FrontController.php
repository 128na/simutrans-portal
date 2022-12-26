<?php

namespace App\Http\Controllers;

use App\Events\ArticleConversion;
use App\Http\Requests\Article\SearchRequest;
use App\Models\Article;
use App\Models\Tag;
use App\Models\User;
use App\Services\Front\ArticleService;
use App\Services\Front\MetaOgpService;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FrontController extends Controller
{
    public function __construct(
        private ArticleService $articleService,
        private MetaOgpService $metaOgpService,
    ) {
    }

    /**
     * SPA用フロント.
     */
    public function fallback(): Renderable
    {
        return view('front.spa');
    }

    /**
     * 記事詳細.
     */
    public function show(Article $article): Renderable
    {
        abort_unless($article->is_publish, 404);
        $meta = $this->metaOgpService->show($article);

        return view('front.spa', ['meta' => $meta]);
    }

    /**
     * アドオンダウンロード.
     */
    public function download(Article $article): StreamedResponse
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
    public function category(string $type, string $slug): Renderable
    {
        $this->articleService->validateCategoryByTypeAndSlug($type, $slug);

        $meta = $this->metaOgpService->category($type, $slug);

        return view('front.spa', ['meta' => $meta]);
    }

    /**
     * カテゴリ(pak/addon)の投稿一覧画面.
     */
    public function categoryPakAddon(string $pakSlug, string $addonSlug): Renderable
    {
        $this->articleService->validateCategoryByPakAndAddon($pakSlug, $addonSlug);
        $meta = $this->metaOgpService->categoryPakAddon($pakSlug, $addonSlug);

        return view('front.spa', ['meta' => $meta]);
    }

    /**
     * カテゴリ(pak,addon指定なし)の投稿一覧画面.
     */
    public function categoryPakNoneAddon(string $pakSlug): Renderable
    {
        $this->articleService->validateCategoryByTypeAndSlug('pak', $pakSlug);
        $meta = $this->metaOgpService->categoryPakNoneAddon($pakSlug);

        return view('front.spa', ['meta' => $meta]);
    }

    /**
     * タグの投稿一覧画面.
     */
    public function tag(Tag $tag): Renderable
    {
        $meta = $this->metaOgpService->tag($tag);

        return view('front.spa', ['meta' => $meta]);
    }

    /**
     * ユーザーの投稿一覧画面.
     */
    public function user(User $user): Renderable
    {
        $meta = $this->metaOgpService->user($user);

        return view('front.spa', ['meta' => $meta]);
    }

    /**
     * 検索結果一覧.
     */
    public function search(SearchRequest $request): Renderable
    {
        return view('front.spa');
    }

    public function error(int|string $status): Renderable
    {
        $statuses = [400, 404, 500, 503];
        $status = in_array(intval($status), $statuses, true) ? $status : 404;

        $path = "errors.{$status}";
        if (View::exists($path)) {
            return view($path);
        }

        return view('errors.404');
    }
}
