<?php

declare(strict_types=1);

namespace App\Http\Controllers\v2;

use App\Actions\Redirect\DoRedirectIfExists;
use App\Enums\ArticlePostType;
use App\Models\Article;
use App\Repositories\v2\ArticleRepository;
use App\Repositories\v2\CategoryRepository;
use App\Repositories\v2\TagRepository;
use App\Repositories\v2\UserRepository;
use App\Services\Front\MetaOgpService;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class FrontController extends Controller
{
    public function __construct(
        private readonly ArticleRepository $articleRepository,
        private readonly CategoryRepository $categoryRepository,
        private readonly TagRepository $tagRepository,
        private readonly UserRepository $userRepository,
        private readonly MetaOgpService $metaOgpService,
    ) {}

    public function top(): \Illuminate\Contracts\View\View
    {
        return view('v2.top.index', [
            'announces' => $this->articleRepository->getTopAnnounces(),
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
        return view('v2.announce.index', [
            'articles' => $this->articleRepository->getAnnounces(),
            'meta' => $this->metaOgpService->announce(),
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

    public function search(Request $request): \Illuminate\Contracts\View\View
    {
        $condition = $request->all();

        return view('v2.search.index', [
            'condition' => $condition,
            'options' => [
                'categories' => $this->categoryRepository->getForSearch(),
                'tags' => $this->tagRepository->getForSearch(),
                'users' => $this->userRepository->getForSearch(),
                'postTypes' => ArticlePostType::cases(),
            ],
            'articles' => $this->articleRepository->search($condition),
            'meta' => $this->metaOgpService->search(),
        ]);
    }

    public function fallbackShow(string $slugOrId): \Illuminate\Http\RedirectResponse
    {
        $article = is_numeric($slugOrId)
            ? Article::findOrFail($slugOrId)
            : Article::slug($slugOrId)->orderBy('id', 'asc')->firstOrFail();

        return redirect(route('articles.show', ['userIdOrNickname' => $article->user->nickname ?? $article->user_id, 'articleSlug' => $article->slug]), 302);
    }

    public function download(Article $article): StreamedResponse
    {
        abort_unless($article->is_publish, 404);
        abort_unless($article->is_addon_post, 404);
        abort_unless($article->has_file && $article->file, 404);

        if (Auth::check() === false || Auth::id() !== $article->user_id) {
            event(new \App\Events\ArticleConversion($article));
        }

        return $this->getPublicDisk()->download(
            $article->file->path,
            $article->file->original_name
        );
    }

    private function getPublicDisk(): FilesystemAdapter
    {
        return Storage::disk('public');
    }
}
