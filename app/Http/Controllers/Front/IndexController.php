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
        $announces = $this->articleRepository->findAnnouces(3);
        $pages = $this->articleRepository->findCommonArticles(3);
        $latest = [
            '128-japan' => $this->articleRepository->findPakArticles('128-japan', 6),
            '128' => $this->articleRepository->findPakArticles('128', 6),
            '64' => $this->articleRepository->findPakArticles('64', 6),
        ];
        $excludes = collect($latest)->flatten()->pluck('id')->unique()->toArray();
        $ranking = $this->articleRepository->findRankingArticles($excludes, 6);

        return [
            'announces' => $announces,
            'pages' => $pages,
            'latest' => $latest,
            'ranking' => $ranking,
        ];
    }
}
