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
            'announces' => $this->articleRepository->getAnnouncesForTop(3),
        ]);
    }
}
