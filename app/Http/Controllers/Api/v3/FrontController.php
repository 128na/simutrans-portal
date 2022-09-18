<?php

namespace App\Http\Controllers\Api\v3;

use App\Events\ArticleShown;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Article\SearchRequest;
use App\Http\Resources\Front\ArticleResource;
use App\Http\Resources\Front\PakAddonResource;
use App\Http\Resources\Front\TagResource;
use App\Http\Resources\Front\UserAddonResource;
use App\Models\Article;
use App\Models\Tag;
use App\Models\User;
use App\Repositories\ArticleRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\TagRepository;
use App\Services\Front\SidebarService;
use Auth;

class FrontController extends Controller
{
    public function __construct(
        private SidebarService $sidebarService,
        private ArticleRepository $articleRepository,
        private CategoryRepository $categoryRepository,
        private TagRepository $tagRepository,
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

        $isOwner = Auth::check() && Auth::user()->can('update', $article);
        if (!$isOwner) {
            event(new ArticleShown($article));
            $contents['gtag'] = $article->user->profile->data->gtag;
        }

        $article = $this->articleRepository->loadArticle($article);

        return new ArticleResource($article);
    }

    public function user(User $user)
    {
        $articles = $this->articleRepository->paginateByUser($user);

        return ArticleResource::collection($articles);
    }

    public function pages()
    {
        $articles = $this->articleRepository->paginatePages();

        return ArticleResource::collection($articles);
    }

    public function announces()
    {
        $articles = $this->articleRepository->paginateAnnouces();

        return ArticleResource::collection($articles);
    }

    public function ranking()
    {
        $articles = $this->articleRepository->paginateRanking();

        return ArticleResource::collection($articles);
    }

    public function category(string $type, string $slug)
    {
        $category = $this->categoryRepository->findOrFailByTypeAndSlug($type, $slug);
        $articles = $this->articleRepository->paginateByCategory($category);

        return ArticleResource::collection($articles);
    }

    public function categoryPakAddon(string $pakSlug, string $addonSlug)
    {
        $pak = $this->categoryRepository->findOrFailByTypeAndSlug('pak', $pakSlug);
        $addon = $this->categoryRepository->findOrFailByTypeAndSlug('addon', $addonSlug);
        $articles = $this->articleRepository->paginateByPakAddonCategory($pak, $addon);

        return ArticleResource::collection($articles);
    }

    public function categoryPakNoneAddon(string $pakSlug)
    {
        $pak = $this->categoryRepository->findOrFailByTypeAndSlug('pak', $pakSlug);

        $articles = $this->articleRepository->paginateByPakNoneAddonCategory($pak);

        return ArticleResource::collection($articles);
    }

    public function tag(Tag $tag)
    {
        $articles = $this->articleRepository->paginateByTag($tag);

        return ArticleResource::collection($articles);
    }

    public function search(SearchRequest $request)
    {
        $word = $request->word;

        $articles = $this->articleRepository->paginateBySearch($word);

        return ArticleResource::collection($articles);
    }

    public function tags()
    {
        $tags = $this->tagRepository->getAllTags();

        return TagResource::collection($tags);
    }
}
