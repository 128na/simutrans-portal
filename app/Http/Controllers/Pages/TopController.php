<?php

declare(strict_types=1);

namespace App\Http\Controllers\Pages;

use App\Repositories\ArticleRepository;
use Illuminate\Contracts\View\View;
use Illuminate\Routing\Controller;

final class TopController extends Controller
{
    public function __construct(
        private readonly ArticleRepository $articleRepository,
    ) {}

    public function top(): View
    {
        return view('pages.top.index', [
            'announces' => $this->articleRepository->getAnnounces(3),
            'pak128Japan' => $this->articleRepository->getLatest('128-japan', 5),
            'pak128' => $this->articleRepository->getLatest('128', 5),
            'pak64' => $this->articleRepository->getLatest('64', 5),
            'pages' => $this->articleRepository->getPages(5),
        ]);
    }
}
