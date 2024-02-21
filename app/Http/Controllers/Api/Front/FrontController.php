<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Front;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Article\ListRequest;
use App\Http\Requests\Api\Article\SearchRequest;
use App\Http\Resources\Api\Front\ArticleResource;
use App\Http\Resources\Api\Front\TagResource;
use App\Models\Tag;
use App\Models\User;
use App\Services\Front\ArticleService;
use App\Services\Front\FrontDescriptionService;
use App\Services\Front\TagService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class FrontController extends Controller
{
    public function __construct(
        private readonly ArticleService $articleService,
        private readonly TagService $tagService,
        private readonly FrontDescriptionService $frontDescriptionService
    ) {
    }

    public function show(string $userIdOrNickname, string $slug): ArticleResource
    {
        $user = is_numeric($userIdOrNickname)
            ? User::findOrFail($userIdOrNickname)
            : User::where('nickname', $userIdOrNickname)->firstOrFail();

        $article = $user->articles()->slug($slug)->firstOrFail();

        abort_unless($article->is_publish, 404);

        return new ArticleResource($this->articleService->loadArticle($article));
    }

    public function user(string $userIdOrNickname, ListRequest $request): AnonymousResourceCollection
    {
        $user = is_numeric($userIdOrNickname)
            ? User::findOrFail($userIdOrNickname)
            : User::where('nickname', $userIdOrNickname)->firstOrFail();

        $order = $request->string('order', 'modified_at')->toString();
        $articles = $this->articleService->paginateByUser($user, $order);

        return ArticleResource::collection($articles)
            ->additional($this->frontDescriptionService->user($user));
    }

    public function pages(ListRequest $request): AnonymousResourceCollection
    {
        $order = $request->string('order', 'modified_at')->toString();
        $articles = $this->articleService->paginatePages(false, $order);

        return ArticleResource::collection($articles)
            ->additional($this->frontDescriptionService->page());
    }

    public function announces(ListRequest $request): AnonymousResourceCollection
    {
        $order = $request->string('order', 'modified_at')->toString();
        $articles = $this->articleService->paginateAnnouces(false, $order);

        return ArticleResource::collection($articles)
            ->additional($this->frontDescriptionService->announces());
    }

    public function ranking(): AnonymousResourceCollection
    {
        $articles = $this->articleService->paginateRanking();

        return ArticleResource::collection($articles)
            ->additional($this->frontDescriptionService->ranking());
    }

    public function category(string $type, string $slug, ListRequest $request): AnonymousResourceCollection
    {
        $order = $request->string('order', 'modified_at')->toString();
        $articles = $this->articleService->paginateByCategory($type, $slug, false, $order);

        return ArticleResource::collection($articles)
            ->additional($this->frontDescriptionService->category($type, $slug));
    }

    public function categoryPakAddon(string $pakSlug, string $addonSlug, ListRequest $request): AnonymousResourceCollection
    {
        $order = $request->string('order', 'modified_at')->toString();
        $articles = $this->articleService->paginateByPakAddonCategory($pakSlug, $addonSlug, $order);

        return ArticleResource::collection($articles)
            ->additional($this->frontDescriptionService->categoryPakAddon($pakSlug, $addonSlug));
    }

    public function categoryPakNoneAddon(string $pakSlug, ListRequest $request): AnonymousResourceCollection
    {
        $order = $request->string('order', 'modified_at')->toString();
        $articles = $this->articleService->paginateByPakNoneAddonCategory($pakSlug, $order);

        return ArticleResource::collection($articles)
            ->additional($this->frontDescriptionService->categoryPakNoneAddon($pakSlug));
    }

    public function tag(Tag $tag, ListRequest $request): AnonymousResourceCollection
    {
        $order = $request->string('order', 'modified_at')->toString();
        $articles = $this->articleService->paginateByTag($tag, $order);

        return ArticleResource::collection($articles)
            ->additional($this->frontDescriptionService->tag($tag));
    }

    public function search(SearchRequest $request): AnonymousResourceCollection
    {
        $word = $request->string('word', '')->toString();
        $order = $request->string('order', 'modified_at')->toString();

        $articles = $this->articleService->paginateBySearch($word, $order);

        return ArticleResource::collection($articles)
            ->additional($this->frontDescriptionService->search($word));
    }

    public function tags(): AnonymousResourceCollection
    {
        $tags = $this->tagService->getAllTags();

        return TagResource::collection($tags);
    }
}
