<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Front;

use App\Enums\CategoryType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Article\ListRequest;
use App\Http\Requests\Api\Article\SearchRequest;
use App\Http\Resources\Api\Front\ArticleResource;
use App\Http\Resources\Api\Front\PrArticleResource;
use App\Http\Resources\Api\Front\TagResource;
use App\Models\Tag;
use App\Models\User;
use App\Services\Front\ArticleService;
use App\Services\Front\FrontDescriptionService;
use App\Services\Front\TagService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class FrontController extends Controller
{
    public function __construct(
        private readonly ArticleService $articleService,
        private readonly TagService $tagService,
        private readonly FrontDescriptionService $frontDescriptionService
    ) {}

    public function show(string $userIdOrNickname, string $slug): ArticleResource
    {
        $user = is_numeric($userIdOrNickname)
            ? User::findOrFail($userIdOrNickname)
            : User::where('nickname', $userIdOrNickname)->firstOrFail();

        $article = $user->articles()->slug($slug)->firstOrFail();

        abort_unless($article->is_publish, 404);

        return new ArticleResource($this->articleService->loadArticle($article));
    }

    public function user(string $userIdOrNickname, ListRequest $listRequest): AnonymousResourceCollection
    {
        $user = is_numeric($userIdOrNickname)
            ? User::findOrFail($userIdOrNickname)
            : User::where('nickname', $userIdOrNickname)->firstOrFail();

        $order = $listRequest->string('order', 'modified_at')->toString();
        $lengthAwarePaginator = $this->articleService->paginateByUser($user, $order);
        $pr = $this->articleService->prArticle();

        return ArticleResource::collection($lengthAwarePaginator)
            ->additional([
                'pr' => $pr instanceof \App\Models\Article ? new PrArticleResource($pr) : null,
                ...$this->frontDescriptionService->user($user),
            ]);
    }

    public function pages(ListRequest $listRequest): AnonymousResourceCollection
    {
        $order = $listRequest->string('order', 'modified_at')->toString();
        $paginator = $this->articleService->paginatePages(false, $order);
        $pr = $this->articleService->prArticle();

        return ArticleResource::collection($paginator)
            ->additional([
                'pr' => $pr instanceof \App\Models\Article ? new PrArticleResource($pr) : null,
                ...$this->frontDescriptionService->page(),
            ]);
    }

    public function announces(ListRequest $listRequest): AnonymousResourceCollection
    {
        $order = $listRequest->string('order', 'modified_at')->toString();
        $paginator = $this->articleService->paginateAnnouces(false, $order);
        $pr = $this->articleService->prArticle();

        return ArticleResource::collection($paginator)
            ->additional([
                'pr' => $pr instanceof \App\Models\Article ? new PrArticleResource($pr) : null,
                ...$this->frontDescriptionService->announces(),
            ]);
    }

    public function ranking(): AnonymousResourceCollection
    {
        $paginator = $this->articleService->paginateRanking();
        $pr = $this->articleService->prArticle();

        return ArticleResource::collection($paginator)
            ->additional([
                'pr' => $pr instanceof \App\Models\Article ? new PrArticleResource($pr) : null,
                ...$this->frontDescriptionService->ranking(),
            ]);
    }

    public function category(string $type, string $slug, ListRequest $listRequest): AnonymousResourceCollection
    {
        $categoryType = CategoryType::tryFrom($type);
        abort_unless($categoryType instanceof CategoryType, 404);
        $order = $listRequest->string('order', 'modified_at')->toString();
        $paginator = $this->articleService->paginateByCategory($categoryType, $slug, false, $order);
        $pr = $this->articleService->prArticle();

        return ArticleResource::collection($paginator)
            ->additional([
                'pr' => $pr instanceof \App\Models\Article ? new PrArticleResource($pr) : null,
                ...$this->frontDescriptionService->category($categoryType, $slug),
            ]);
    }

    public function categoryPakAddon(string $pakSlug, string $addonSlug, ListRequest $listRequest): AnonymousResourceCollection
    {
        $order = $listRequest->string('order', 'modified_at')->toString();
        $lengthAwarePaginator = $this->articleService->paginateByPakAddonCategory($pakSlug, $addonSlug, $order);
        $pr = $this->articleService->prArticle();

        return ArticleResource::collection($lengthAwarePaginator)
            ->additional([
                'pr' => $pr instanceof \App\Models\Article ? new PrArticleResource($pr) : null,
                ...$this->frontDescriptionService->categoryPakAddon($pakSlug, $addonSlug),
            ]);
    }

    public function categoryPakNoneAddon(string $pakSlug, ListRequest $listRequest): AnonymousResourceCollection
    {
        $order = $listRequest->string('order', 'modified_at')->toString();
        $lengthAwarePaginator = $this->articleService->paginateByPakNoneAddonCategory($pakSlug, $order);
        $pr = $this->articleService->prArticle();

        return ArticleResource::collection($lengthAwarePaginator)
            ->additional([
                'pr' => $pr instanceof \App\Models\Article ? new PrArticleResource($pr) : null,
                ...$this->frontDescriptionService->categoryPakNoneAddon($pakSlug),
            ]);
    }

    public function tag(Tag $tag, ListRequest $listRequest): AnonymousResourceCollection
    {
        $order = $listRequest->string('order', 'modified_at')->toString();
        $lengthAwarePaginator = $this->articleService->paginateByTag($tag, $order);
        $pr = $this->articleService->prArticle();

        return ArticleResource::collection($lengthAwarePaginator)
            ->additional([
                'pr' => $pr instanceof \App\Models\Article ? new PrArticleResource($pr) : null,
                ...$this->frontDescriptionService->tag($tag),
            ]);
    }

    public function search(SearchRequest $searchRequest): AnonymousResourceCollection
    {
        $word = $searchRequest->string('word', '')->toString();
        $order = $searchRequest->string('order', 'modified_at')->toString();

        $lengthAwarePaginator = $this->articleService->paginateBySearch($word, $order);
        $pr = $this->articleService->prArticle();

        return ArticleResource::collection($lengthAwarePaginator)
            ->additional([
                'pr' => $pr instanceof \App\Models\Article ? new PrArticleResource($pr) : null,
                ...$this->frontDescriptionService->search($word),
            ]);
    }

    public function tags(): AnonymousResourceCollection
    {
        $allTags = $this->tagService->getAllTags();

        return TagResource::collection($allTags);
    }
}
