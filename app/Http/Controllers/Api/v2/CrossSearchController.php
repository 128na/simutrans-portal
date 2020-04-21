<?php

namespace App\Http\Controllers\Api\v2;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\CrossSearchArticle;
use App\Models\Article;
use App\Services\ArticleService;
use Illuminate\Http\Request;

class CrossSearchController extends Controller
{
    /**
     * @var ArticleService
     */
    private $article_service;

    public function __construct(ArticleService $article_service)
    {
        $this->article_service = $article_service;
    }

    private function checkToken($request)
    {
        $token = $request->token ?? null;
        abort_unless($token === config('auth.simutrans_search_token', false), 403);
    }

    public function index(Request $request)
    {
        $this->checkToken($request);

        return $this->article_service
            ->getAddonArticles(65535)
            ->map(function ($article) {
                return route('articles.show', $article->slug);
            });
    }

    public function show(Request $request, Article $article)
    {
        $this->checkToken($request);

        return new CrossSearchArticle(
            $this->article_service->getArticle($article)
        );
    }
}
