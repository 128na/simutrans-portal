<?php

declare(strict_types=1);

namespace App\Http\Controllers\Pages\Article;

use App\Http\Resources\Frontend\ArticleList;
use App\Repositories\ArticleRepository;
use App\Services\Front\MetaOgpService;
use Illuminate\Contracts\View\View;
use Illuminate\Routing\Controller;

final class PakController extends Controller
{
    public function __construct(
        private readonly ArticleRepository $articleRepository,
        private readonly MetaOgpService $metaOgpService,
    ) {}

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
}
