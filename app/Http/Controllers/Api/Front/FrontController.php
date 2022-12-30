<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Front;

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
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class FrontController extends Controller
{
    public function __construct(
        private SidebarService $sidebarService,
        private ArticleService $articleService,
        private TagService $tagService,
        private FrontDescriptionService $frontDescriptionService
    ) {
    }

    /**
     * @return array<AnonymousResourceCollection>
     */
    public function top(): array
    {
        return [
            'pak128japan' => ArticleResource::collection($this->articleService->paginateByCategory('pak', '128-japan', true)),
            'pak128' => ArticleResource::collection($this->articleService->paginateByCategory('pak', '128', true)),
            'pak64' => ArticleResource::collection($this->articleService->paginateByCategory('pak', '64', true)),
            'rankings' => ArticleResource::collection($this->articleService->paginateRanking(true)),
            'pages' => ArticleResource::collection($this->articleService->paginatePages(true)),
            'announces' => ArticleResource::collection($this->articleService->paginateAnnouces(true)),
        ];
    }

    /**
     * @return array<UserAddonResource|PakAddonResource>
     */
    public function sidebar(): array
    {
        return [
            'userAddonCounts' => new UserAddonResource($this->sidebarService->userAddonCounts()),
            'pakAddonCounts' => new PakAddonResource($this->sidebarService->pakAddonsCounts()),
        ];
    }

    public function show(Article $article): ArticleResource
    {
        abort_unless($article->is_publish, 404);

        return new ArticleResource($this->articleService->loadArticle($article));
    }

    public function user(User $user): AnonymousResourceCollection
    {
        $articles = $this->articleService->paginateByUser($user);

        return ArticleResource::collection($articles)
            ->additional($this->frontDescriptionService->user($user));
    }

    public function pages(): AnonymousResourceCollection
    {
        $articles = $this->articleService->paginatePages();

        return ArticleResource::collection($articles)
            ->additional($this->frontDescriptionService->page());
    }

    public function announces(): AnonymousResourceCollection
    {
        $articles = $this->articleService->paginateAnnouces();

        return ArticleResource::collection($articles)
            ->additional($this->frontDescriptionService->announces());
    }

    public function ranking(): AnonymousResourceCollection
    {
        $articles = $this->articleService->paginateRanking();

        return ArticleResource::collection($articles)
            ->additional($this->frontDescriptionService->ranking());
    }

    public function category(string $type, string $slug): AnonymousResourceCollection
    {
        $articles = $this->articleService->paginateByCategory($type, $slug);

        return ArticleResource::collection($articles)
            ->additional($this->frontDescriptionService->category($type, $slug));
    }

    public function categoryPakAddon(string $pakSlug, string $addonSlug): AnonymousResourceCollection
    {
        $articles = $this->articleService->paginateByPakAddonCategory($pakSlug, $addonSlug);

        return ArticleResource::collection($articles)
            ->additional($this->frontDescriptionService->categoryPakAddon($pakSlug, $addonSlug));
    }

    public function categoryPakNoneAddon(string $pakSlug): AnonymousResourceCollection
    {
        $articles = $this->articleService->paginateByPakNoneAddonCategory($pakSlug);

        return ArticleResource::collection($articles)
            ->additional($this->frontDescriptionService->categoryPakNoneAddon($pakSlug));
    }

    public function tag(Tag $tag): AnonymousResourceCollection
    {
        $articles = $this->articleService->paginateByTag($tag);

        return ArticleResource::collection($articles)
            ->additional($this->frontDescriptionService->tag($tag));
    }

    public function search(SearchRequest $request): AnonymousResourceCollection
    {
        $word = $request->word ?? '';

        $articles = $this->articleService->paginateBySearch($word);

        return ArticleResource::collection($articles)
            ->additional($this->frontDescriptionService->search($word));
    }

    public function tags(): AnonymousResourceCollection
    {
        $tags = $this->tagService->getAllTags();

        return TagResource::collection($tags);
    }
}
