<?php

namespace App\Http\Controllers\Api\v3;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Article\SearchRequest;
use App\Http\Resources\Api\Front\ArticleResource;
use App\Http\Resources\Api\Front\PakAddonResource;
use App\Http\Resources\Api\Front\TagResource;
use App\Http\Resources\Api\Front\UserAddonResource;
use App\Http\Resources\Api\Front\UserProfileResource;
use App\Models\Article;
use App\Models\Tag;
use App\Models\User;
use App\Services\Front\ArticleService;
use App\Services\Front\SidebarService;
use App\Services\Front\TagService;

class FrontController extends Controller
{
    public function __construct(
        private SidebarService $sidebarService,
        private ArticleService $articleService,
        private TagService $tagService,
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
            ->additional(([
                'title' => sprintf('%sさんの投稿', $user->name),
                'profile' => new UserProfileResource($user),
            ]));
    }

    public function pages()
    {
        $articles = $this->articleService->paginatePages();

        return ArticleResource::collection($articles)
            ->additional((['title' => '一般記事']));
    }

    public function announces()
    {
        $articles = $this->articleService->paginateAnnouces();

        return ArticleResource::collection($articles)
            ->additional((['title' => 'お知らせ']));
    }

    public function ranking()
    {
        $articles = $this->articleService->paginateRanking();

        return ArticleResource::collection($articles)
            ->additional((['title' => 'アクセスランキング']));
    }

    public function category(string $type, string $slug)
    {
        $articles = $this->articleService->paginateByCategory($type, $slug);

        return ArticleResource::collection($articles)
            ->additional((['title' => sprintf('%sの投稿', __("category.{$type}.{$slug}"))]));
    }

    public function categoryPakAddon(string $pakSlug, string $addonSlug)
    {
        $articles = $this->articleService->paginateByPakAddonCategory($pakSlug, $addonSlug);

        return ArticleResource::collection($articles)
            ->additional((['title' => sprintf('%s、%sの投稿', __("category.pak.{$pakSlug}"), __("category.addon.{$addonSlug}"))]));
    }

    public function categoryPakNoneAddon(string $pakSlug)
    {
        $articles = $this->articleService->paginateByPakNoneAddonCategory($pakSlug);

        return ArticleResource::collection($articles)
            ->additional((['title' => sprintf('%s、%sの投稿', __("category.pak.{$pakSlug}"), __('category.addon.none'))]));
    }

    public function tag(Tag $tag)
    {
        $articles = $this->articleService->paginateByTag($tag);

        return ArticleResource::collection($articles)
           ->additional((['title' => sprintf('%sタグを含む投稿', $tag->name)]));
    }

    public function search(SearchRequest $request)
    {
        $word = $request->word;

        $articles = $this->articleService->paginateBySearch($word);

        return ArticleResource::collection($articles)
           ->additional((['title' => sprintf('%sの検索結果', $word)]));
    }

    public function tags()
    {
        $tags = $this->tagRepository->getAllTags();

        return TagResource::collection($tags);
    }
}
