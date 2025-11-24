<?php

declare(strict_types=1);

namespace App\Http\Controllers\Pages\Article;

use App\Actions\FrontArticle\FallbackShowAction;
use App\Actions\Redirect\DoRedirectIfExists;
use App\Http\Resources\Frontend\ArticleShow;
use App\Models\Article;
use App\Repositories\ArticleRepository;
use App\Services\Front\MetaOgpService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

final class ShowController extends Controller
{
    public function __construct(
        private readonly ArticleRepository $articleRepository,
        private readonly MetaOgpService $metaOgpService,
    ) {}

    public function show(string $userIdOrNickname, string $slug, Request $request, DoRedirectIfExists $doRedirectIfExists): RedirectResponse|View
    {
        $article = $this->articleRepository->first($userIdOrNickname, $slug);
        if (! $article instanceof Article) {
            return $doRedirectIfExists($request->fullUrl());
        }

        if (Auth::check() === false || Auth::id() !== $article->user_id) {
            event(new \App\Events\ArticleShown($article));
        }

        return view('pages.show.index', [
            'article' => new ArticleShow($article),
            'meta' => $this->metaOgpService->frontArticleShow($article->user, $article),
        ]);
    }

    public function fallbackShow(string $slugOrId, FallbackShowAction $fallbackShowAction): RedirectResponse
    {
        return $fallbackShowAction($slugOrId);
    }
}
