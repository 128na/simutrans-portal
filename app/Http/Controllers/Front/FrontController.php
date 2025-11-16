<?php

declare(strict_types=1);

namespace App\Http\Controllers\Front;

use App\Actions\FrontArticle\DownloadAction;
use App\Actions\FrontArticle\FallbackShowAction;
use App\Actions\FrontArticle\SearchAction;
use App\Actions\Redirect\DoRedirectIfExists;
use App\Models\Article;
use App\Repositories\ArticleRepository;
use App\Repositories\UserRepository;
use App\Services\Front\MetaOgpService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class FrontController extends Controller
{
    public function __construct(
        private readonly ArticleRepository $articleRepository,
        private readonly UserRepository $userRepository,
        private readonly MetaOgpService $metaOgpService,
    ) {}

    public function top(): \Illuminate\Contracts\View\View
    {
        return view('v2.top.index', [
            'announces' => $this->articleRepository->getAnnounces(3),
        ]);
    }

    public function pak128jp(): \Illuminate\Contracts\View\View
    {
        return view('v2.pak.index', [
            'pak' => '128-japan',
            'articles' => $this->articleRepository->getLatest('128-japan'),
            'meta' => $this->metaOgpService->pak('128-japan'),
        ]);
    }

    public function pak128(): \Illuminate\Contracts\View\View
    {
        return view('v2.pak.index', [
            'pak' => '128',
            'articles' => $this->articleRepository->getLatest('128'),
            'meta' => $this->metaOgpService->pak('128'),
        ]);
    }

    public function pak64(): \Illuminate\Contracts\View\View
    {
        return view('v2.pak.index', [
            'pak' => '64',
            'articles' => $this->articleRepository->getLatest('64'),
            'meta' => $this->metaOgpService->pak('64'),
        ]);
    }

    public function pakOthers(): \Illuminate\Contracts\View\View
    {
        return view('v2.pak.index', [
            'pak' => 'other-pak',
            'articles' => $this->articleRepository->getLatestOther(),
            'meta' => $this->metaOgpService->pak('others'),
        ]);
    }

    public function announces(): \Illuminate\Contracts\View\View
    {
        return view('v2.announces.index', [
            'articles' => $this->articleRepository->getAnnounces(),
            'meta' => $this->metaOgpService->announces(),
        ]);
    }

    public function pages(): \Illuminate\Contracts\View\View
    {
        return view('v2.pages.index', [
            'articles' => $this->articleRepository->getPages(),
            'meta' => $this->metaOgpService->pages(),
        ]);
    }

    public function show(string $userIdOrNickname, string $slug, Request $request, DoRedirectIfExists $doRedirectIfExists): \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
    {
        $article = $this->articleRepository->first($userIdOrNickname, $slug);
        if (! $article instanceof \App\Models\Article) {
            return $doRedirectIfExists($request->fullUrl());
        }

        if (Auth::check() === false || Auth::id() !== $article->user_id) {
            event(new \App\Events\ArticleShown($article));
        }

        return view('v2.show.index', [
            'article' => $article,
            'meta' => $this->metaOgpService->show($article->user, $article),
        ]);
    }

    public function users(): \Illuminate\Contracts\View\View
    {
        return view('v2.users.index', [
            'users' => $this->userRepository->getForList(),
            'meta' => $this->metaOgpService->users(),
        ]);
    }

    public function search(Request $request, SearchAction $searchAction): View
    {
        $condition = $request->all();

        return view('v2.search.index', [
            ...$searchAction($condition),
            'meta' => $this->metaOgpService->search(),
        ]);
    }

    public function fallbackShow(string $slugOrId, FallbackShowAction $fallbackShowAction): RedirectResponse
    {
        return $fallbackShowAction($slugOrId);
    }

    public function download(Article $article, DownloadAction $downloadAction): StreamedResponse
    {
        return $downloadAction($article, Auth::user());
    }
}
