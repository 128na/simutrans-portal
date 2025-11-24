<?php

declare(strict_types=1);

namespace App\Http\Controllers\Front;

use App\Actions\FrontArticle\ConversionAction;
use App\Actions\FrontArticle\DownloadAction;
use App\Actions\FrontArticle\FallbackShowAction;
use App\Actions\FrontArticle\SearchAction;
use App\Actions\Redirect\DoRedirectIfExists;
use App\Http\Resources\Frontend\ArticleList;
use App\Http\Resources\Frontend\ArticleShow;
use App\Models\Article;
use App\Models\Contents\AddonIntroductionContent;
use App\Repositories\ArticleRepository;
use App\Services\Front\MetaOgpService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class ArticleController extends Controller
{
    public function __construct(
        private readonly ArticleRepository $articleRepository,
        private readonly MetaOgpService $metaOgpService,
    ) {}

    public function top(): View
    {
        return view('pages.top.index', [
            'announces' => $this->articleRepository->getAnnounces(3),
        ]);
    }

    public function pak128jp(): View
    {
        return view('pages.pak.index', [
            'pak' => '128-japan',
            'articles' => ArticleList::collection($this->articleRepository->getLatest('128-japan')),
            'meta' => $this->metaOgpService->frontPak('128-japan'),
        ]);
    }

    public function pak128(): View
    {
        return view('pages.pak.index', [
            'pak' => '128',
            'articles' => ArticleList::collection($this->articleRepository->getLatest('128')),
            'meta' => $this->metaOgpService->frontPak('128'),
        ]);
    }

    public function pak64(): View
    {
        return view('pages.pak.index', [
            'pak' => '64',
            'articles' => ArticleList::collection($this->articleRepository->getLatest('64')),
            'meta' => $this->metaOgpService->frontPak('64'),
        ]);
    }

    public function pakOthers(): View
    {
        return view('pages.pak.index', [
            'pak' => 'other-pak',
            'articles' => ArticleList::collection($this->articleRepository->getLatestOther()),
            'meta' => $this->metaOgpService->frontPak('others'),
        ]);
    }

    public function announces(): View
    {
        return view('pages.announces.index', [
            'articles' => ArticleList::collection($this->articleRepository->getAnnounces()),
            'meta' => $this->metaOgpService->frontAnnounces(),
        ]);
    }

    public function pages(): View
    {
        return view('pages.static.index', [
            'articles' => ArticleList::collection($this->articleRepository->getPages()),
            'meta' => $this->metaOgpService->frontPages(),
        ]);
    }

    public function search(Request $request, SearchAction $searchAction): View
    {
        $condition = $request->all();

        return view('pages.search.index', [
            ...$searchAction($condition),
            'meta' => $this->metaOgpService->frontSearch(),
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

        return view('pages.show.index', [
            'article' => new ArticleShow($article),
            'meta' => $this->metaOgpService->frontArticleShow($article->user, $article),
        ]);
    }

    public function fallbackShow(string $slugOrId, FallbackShowAction $fallbackShowAction): RedirectResponse
    {
        return $fallbackShowAction($slugOrId);
    }

    public function download(Article $article, DownloadAction $downloadAction): StreamedResponse
    {
        if (Gate::denies('download', $article)) {
            abort(404);
        }

        return $downloadAction($article, Auth::user());
    }

    public function conversion(Article $article, ConversionAction $conversionAction): RedirectResponse
    {
        if (Gate::allows('conversion', $article)) {
            $conversionAction($article, Auth::user());
        }

        assert($article->contents instanceof AddonIntroductionContent);
        if ($article->contents->link) {
            return redirect($article->contents->link, Response::HTTP_FOUND);
        }

        abort(404);
    }
}
