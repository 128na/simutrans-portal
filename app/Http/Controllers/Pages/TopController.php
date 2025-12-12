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
        $articles = $this->articleRepository->getTopPageArticles(
            announcesLimit: 3,
            pak128JapanLimit: 5,
            pak128Limit: 5,
            pak64Limit: 5,
            pagesLimit: 5
        );

        return view('pages.top.index', $articles);
    }
}
