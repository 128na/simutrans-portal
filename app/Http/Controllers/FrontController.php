<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\CategoryType;
use App\Http\Requests\Article\SearchRequest;
use App\Models\Article;
use App\Models\Tag;
use App\Models\User;
use App\Services\Front\ArticleService;
use App\Services\Front\MetaOgpService;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FrontController extends Controller
{
    public function __construct(
        private readonly ArticleService $articleService,
        private readonly MetaOgpService $metaOgpService,
    ) {
    }

    /**
     * SPA用フロント.
     */
    public function fallback(): Renderable
    {
        return view('front.spa');
    }

    public function social(): Renderable
    {
        $meta = $this->metaOgpService->social();

        return view('front.spa', ['meta' => $meta]);
    }

    /**
     * 記事詳細.
     */
    public function show(string $userIdOrNickname, string $slug): Renderable
    {
        $user = is_numeric($userIdOrNickname)
            ? User::findOrFail($userIdOrNickname)
            : User::where('nickname', $userIdOrNickname)->firstOrFail();

        $article = $user->articles()->slug($slug)->firstOrFail();
        abort_unless($article->is_publish, 404);
        $meta = $this->metaOgpService->show($user, $article);

        return view('front.spa', ['meta' => $meta]);
    }

    /**
     * アドオンダウンロード.
     */
    public function download(Article $article): StreamedResponse
    {
        abort_unless($article->is_publish, 404);
        abort_unless($article->is_addon_post, 404);

        if (Auth::check() === false || Auth::id() !== $article->user_id) {
            ArticleConversion:dispatch($article);
        }

        abort_unless($article->has_file && $article->file, 404);

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
        $categoryType = CategoryType::tryFrom($type);
        abort_unless($categoryType instanceof CategoryType, 404);
        $this->articleService->validateCategoryByTypeAndSlug($categoryType, $slug);

        $meta = $this->metaOgpService->category($categoryType, $slug);

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
        $this->articleService->validateCategoryByTypeAndSlug(CategoryType::Pak, $pakSlug);
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
    public function user(string $userIdOrNickname): Renderable
    {
        $user = is_numeric($userIdOrNickname)
            ? User::findOrFail($userIdOrNickname)
            : User::where('nickname', $userIdOrNickname)->firstOrFail();

        $meta = $this->metaOgpService->user($user);

        return view('front.spa', ['meta' => $meta]);
    }

    /**
     * 検索結果一覧.
     */
    public function search(SearchRequest $searchRequest): Renderable
    {
        return view('front.spa');
    }

    public function error(int|string $status): Response|ResponseFactory
    {
        $status = (int) $status;
        $statuses = [401, 403, 404, 418, 419, 422, 429, 500];
        $status = in_array($status, $statuses, true) ? $status : 404;

        return response(view('front.spa'), $status);
    }

    public function fallbackShow(string $slugOrId): RedirectResponse
    {
        $article = is_numeric($slugOrId)
            ? Article::findOrFail($slugOrId)
            : Article::slug($slugOrId)->orderBy('id', 'asc')->firstOrFail();

        return redirect(route('articles.show', ['userIdOrNickname' => $article->user?->nickname ?? $article->user_id, 'articleSlug' => $article->slug]), Response::HTTP_MOVED_PERMANENTLY);
    }
}
