<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Repositories\ArticleRepository;

/**
 * トップページ.
 */
class IndexController extends Controller
{
    private ArticleRepository $articleRepository;

    public function __construct(ArticleRepository $articleRepository)
    {
        $this->articleRepository = $articleRepository;
    }

    public function index()
    {
        $contents = $this->getContents();

        return view('front.index', $contents);
    }

    private function getContents(): array
    {
        $announces = $this->articleRepository->findAllAnnouces(3);
        $pages = $this->articleRepository->findAllPages(3);
        $latest = [
            '128-japan' => $this->articleRepository->findAllByPak('128-japan', 6),
            '128' => $this->articleRepository->findAllByPak('128', 6),
            '64' => $this->articleRepository->findAllByPak('64', 6),
        ];
        $excludes = collect($latest)->flatten()->pluck('id')->unique()->toArray();
        $ranking = $this->articleRepository->findAllRanking($excludes, 6);

        return [
            'announces' => $announces,
            'pages' => $pages,
            'latest' => $latest,
            'ranking' => $ranking,
        ];
    }
}
