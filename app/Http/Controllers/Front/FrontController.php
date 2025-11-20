<?php

declare(strict_types=1);

namespace App\Http\Controllers\Front;

use App\Actions\FrontArticle\DownloadAction;
use App\Actions\FrontArticle\FallbackShowAction;
use App\Actions\FrontArticle\SearchAction;
use App\Actions\Redirect\DoRedirectIfExists;
use App\Models\Article;
use App\Models\Tag;
use App\Repositories\ArticleRepository;
use App\Repositories\TagRepository;
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
        private readonly TagRepository $tagRepository,
        private readonly UserRepository $userRepository,
        private readonly MetaOgpService $metaOgpService,
    ) {}

    public function top(): View
    {
        return view('v2.top.index', [
            'announces' => $this->articleRepository->getAnnounces(3),
        ]);
    }

    public function pak128jp(): View
    {
        return view('v2.pak.index', [
            'pak' => '128-japan',
            'articles' => $this->articleRepository->getLatest('128-japan'),
            'meta' => $this->metaOgpService->frontPak('128-japan'),
        ]);
    }

    public function pak128(): View
    {
        return view('v2.pak.index', [
            'pak' => '128',
            'articles' => $this->articleRepository->getLatest('128'),
            'meta' => $this->metaOgpService->frontPak('128'),
        ]);
    }

    public function pak64(): View
    {
        return view('v2.pak.index', [
            'pak' => '64',
            'articles' => $this->articleRepository->getLatest('64'),
            'meta' => $this->metaOgpService->frontPak('64'),
        ]);
    }

    public function pakOthers(): View
    {
        return view('v2.pak.index', [
            'pak' => 'other-pak',
            'articles' => $this->articleRepository->getLatestOther(),
            'meta' => $this->metaOgpService->frontPak('others'),
        ]);
    }

    public function announces(): View
    {
        return view('v2.announces.index', [
            'articles' => $this->articleRepository->getAnnounces(),
            'meta' => $this->metaOgpService->frontAnnounces(),
        ]);
    }

    public function pages(): View
    {
        return view('v2.pages.index', [
            'articles' => $this->articleRepository->getPages(),
            'meta' => $this->metaOgpService->frontPages(),
        ]);
    }

    public function show(string $userIdOrNickname, string $slug, Request $request, DoRedirectIfExists $doRedirectIfExists): RedirectResponse|View
    {
        $article = $this->articleRepository->first($userIdOrNickname, $slug);
        if (! $article instanceof Article) {
            return $doRedirectIfExists($request->fullUrl());
        }

        if (Auth::check() === false || Auth::id() !== $article->user_id) {
            event(new \App\Events\ArticleShown($article));
        }

        return view('v2.show.index', [
            'article' => $article,
            'meta' => $this->metaOgpService->frontArticleShow($article->user, $article),
        ]);
    }

    public function users(): View
    {
        return view('v2.users.index', [
            'users' => $this->userRepository->getForList(),
            'meta' => $this->metaOgpService->frontUsers(),
        ]);
    }

    public function user(string $userIdOrNickname): View
    {
        $user = $this->userRepository->firstOrFailByIdOrNickname($userIdOrNickname);

        return view('v2.users.show', [
            'user' => $user,
            'articles' => $this->articleRepository->getByUser($user->id),
            'meta' => $this->metaOgpService->frontUser($user),
        ]);
    }

    public function tags(): View
    {
        return view('v2.tags.index', [
            'tags' => $this->tagRepository->getForList(),
            'meta' => $this->metaOgpService->frontTags(),
        ]);
    }

    public function tag(Tag $tag): View
    {
        return view('v2.tags.show', [
            'tag' => $tag,
            'articles' => $this->articleRepository->getByTag($tag->id),
            'meta' => $this->metaOgpService->frontTag($tag),
        ]);
    }

    public function search(Request $request, SearchAction $searchAction): View
    {
        $condition = $request->all();

        return view('v2.search.index', [
            ...$searchAction($condition),
            'meta' => $this->metaOgpService->frontSearch(),
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
