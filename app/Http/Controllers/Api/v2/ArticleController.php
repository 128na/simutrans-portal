<?php

namespace App\Http\Controllers\Api\v2;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Article\SearchRequest;
use App\Http\Resources\Api\Articles as ArticlesResource;
use App\Models\Category;
use App\Models\Tag;
use App\Models\User;
use App\Services\ArticleService;

class ArticleController extends Controller
{
    private ArticleService $article_service;

    public function __construct(ArticleService $article_service)
    {
        $this->article_service = $article_service;
    }

    public function latest()
    {
        return new ArticlesResource(
            $this->article_service->getAddonArticles()
        );
    }

    public function search(SearchRequest $request)
    {
        return new ArticlesResource(
            $this->article_service->getSearchArticles($request)
        );
    }

    public function user(User $user)
    {
        return new ArticlesResource(
            $this->article_service->getUserArticles($user)
        );
    }

    public function category(Category $category)
    {
        return new ArticlesResource(
            $this->article_service->getCategoryArtciles($category)
        );
    }

    public function tag(Tag $tag)
    {
        return new ArticlesResource(
            $this->article_service->getTagArticles($tag)
        );
    }
}
