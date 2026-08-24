<?php

declare(strict_types=1);

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Repositories\Article\FrontArticleRepository;
use Illuminate\Contracts\View\View;

class TopController extends Controller
{
    public function __construct(
        private readonly FrontArticleRepository $articleRepository,
    ) {}

    public function top(): View
    {
        $articles = $this->articleRepository->getTopPageArticles(announcesLimit: 3);

        return view('pages.top.index', $articles);
    }
}
