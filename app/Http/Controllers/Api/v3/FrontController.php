<?php

namespace App\Http\Controllers\Api\v3;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Article\SearchRequest;
use App\Http\Resources\Api\Front\ArticleResource;
use App\Http\Resources\Api\Front\PakAddonResource;
use App\Http\Resources\Api\Front\TagResource;
use App\Http\Resources\Api\Front\UserAddonResource;
use App\Models\Article;
use App\Models\Tag;
use App\Models\User;
use App\Services\Front\ArticleService;
use App\Services\Front\FrontDescriptionService;
use App\Services\Front\SidebarService;
use App\Services\Front\TagService;
use Illuminate\Http\Request;

class FrontController extends Controller
{
    public function __construct(
        private SidebarService $sidebarService,
        private ArticleService $articleService,
        private TagService $tagService,
        private FrontDescriptionService $frontDescriptionService
    ) {
    }

    public function sidebar()
    {
        return [
            'userAddonCounts' => new UserAddonResource($this->sidebarService->userAddonCounts()),
            'pakAddonCounts' => new PakAddonResource($this->sidebarService->pakAddonsCounts()),
        ];
    }

    public function show(Article $article)
    {
        abort_unless($article->is_publish, 404);

        return new ArticleResource($this->articleService->loadArticle($article));
    }

    public function user(User $user)
    {
        $articles = $this->articleService->paginateByUser($user);

        return ArticleResource::collection($articles)
            ->additional($this->frontDescriptionService->user($user));
    }

    public function pages(Request $request)
    {
        $articles = $this->articleService->paginatePages($request->has('simple'));

        return ArticleResource::collection($articles)
            ->additional($this->frontDescriptionService->page());
    }

    public function announces(Request $request)
    {
        $articles = $this->articleService->paginateAnnouces($request->has('simple'));

        return ArticleResource::collection($articles)
            ->additional($this->frontDescriptionService->announces());
    }

    public function ranking(Request $request)
    {
        $articles = $this->articleService->paginateRanking($request->has('simple'));

        return ArticleResource::collection($articles)
            ->additional($this->frontDescriptionService->ranking());
    }

    public function category(string $type, string $slug, Request $request)
    {
        $articles = $this->articleService->paginateByCategory($type, $slug, $request->has('simple'));

        return ArticleResource::collection($articles)
            ->additional($this->frontDescriptionService->category($type, $slug));
    }

    public function categoryPakAddon(string $pakSlug, string $addonSlug)
    {
        $articles = $this->articleService->paginateByPakAddonCategory($pakSlug, $addonSlug);

        return ArticleResource::collection($articles)
            ->additional($this->frontDescriptionService->categoryPakAddon($pakSlug, $addonSlug));
    }

    public function categoryPakNoneAddon(string $pakSlug)
    {
        $articles = $this->articleService->paginateByPakNoneAddonCategory($pakSlug);

        return ArticleResource::collection($articles)
            ->additional($this->frontDescriptionService->categoryPakNoneAddon($pakSlug));
    }

    public function tag(Tag $tag)
    {
        $articles = $this->articleService->paginateByTag($tag);

        return ArticleResource::collection($articles)
            ->additional($this->frontDescriptionService->tag($tag));
    }

    public function search(SearchRequest $request)
    {
        $word = $request->word ?? '';

        $articles = $this->articleService->paginateBySearch($word);

        return ArticleResource::collection($articles)
           ->additional($this->frontDescriptionService->search($word));
    }

    public function tags()
    {
        $tags = $this->tagService->getAllTags();

        return TagResource::collection($tags);
    }
}
