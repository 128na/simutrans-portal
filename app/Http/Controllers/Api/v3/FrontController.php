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
use Illuminate\Http\Request;

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
            ->additional([
                'description' => [
                    'title' => sprintf('%sさんの投稿', $user->name),
                    'type' => 'profile',
                    'profile' => new UserProfileResource($user),
                ],
            ]);
    }

    public function pages(Request $request)
    {
        $articles = $this->articleService->paginatePages($request->has('simple'));

        return ArticleResource::collection($articles)
            ->additional([
                'description' => [
                    'title' => __('category.page.common'),
                    'type' => 'message',
                    'message' => __('category.description.page.common'),
                    ],
                ]);
    }

    public function announces(Request $request)
    {
        $articles = $this->articleService->paginateAnnouces($request->has('simple'));

        return ArticleResource::collection($articles)
            ->additional([
                'description' => [
                'title' => __('category.page.announce'),
                    'type' => 'message',
                    'message' => __('category.description.page.announce'),
                ],
            ]);
    }

    public function ranking(Request $request)
    {
        $articles = $this->articleService->paginateRanking($request->has('simple'));

        return ArticleResource::collection($articles)
            ->additional([
                'description' => [
                    'title' => 'アクセスランキング',
                    'type' => 'message',
                    'message' => '本日のアクセス数の多い記事ランキングです。',
                ],
            ]);
    }

    public function category(string $type, string $slug, Request $request)
    {
        $articles = $this->articleService->paginateByCategory($type, $slug, $request->has('simple'));
        $description = $type === 'license'
            ? ['type' => 'url', 'url' => __("category.description.{$type}.{$slug}")]
            : ['type' => 'message', 'message' => __("category.description.{$type}.{$slug}")];

        return ArticleResource::collection($articles)
            ->additional([
                'description' => array_merge(
                    ['title' => sprintf('%sの投稿', __("category.{$type}.{$slug}"))],
                    $description,
                ),
            ]);
    }

    public function categoryPakAddon(string $pakSlug, string $addonSlug)
    {
        $articles = $this->articleService->paginateByPakAddonCategory($pakSlug, $addonSlug);

        return ArticleResource::collection($articles)
            ->additional([
                'description' => [
                    'title' => sprintf('%s、%sの投稿', __("category.pak.{$pakSlug}"), __("category.addon.{$addonSlug}")),
                    'type' => 'message',
                    'message' => __("category.description.addon.{$addonSlug}"),
                ],
            ]);
    }

    public function categoryPakNoneAddon(string $pakSlug)
    {
        $articles = $this->articleService->paginateByPakNoneAddonCategory($pakSlug);

        return ArticleResource::collection($articles)
            ->additional([
                'description' => [
                    'title' => sprintf('%s、%sの投稿', __("category.pak.{$pakSlug}"), __('category.addon.none')),
                    'type' => 'message',
                    'message' => __('category.description.addon.none'),
                ],
            ]);
    }

    public function tag(Tag $tag)
    {
        $articles = $this->articleService->paginateByTag($tag);

        return ArticleResource::collection($articles)
            ->additional([
                'description' => [
                    'type' => 'tag',
                    'title' => sprintf('%sタグを含む投稿', $tag->name),
                    'message' => $tag->description,
                    'name' => $tag->name,
                    'id' => $tag->id,
                    'editable' => $tag->editable,
                    'createdBy' => $tag->createdBy?->name,
                    'lastModifiedBy' => $tag->lastModifiedBy?->name,
                    'createdAt' => $tag->created_at->toDateTimeString(),
                    'updatedAt' => $tag->updated_at->toDateTimeString(),
                ],
            ]);
    }

    public function search(SearchRequest $request)
    {
        $word = $request->word ?? '';

        $articles = $this->articleService->paginateBySearch($word);

        return ArticleResource::collection($articles)
           ->additional(['title' => $word ? sprintf('%sの検索結果', $word) : '全ての記事']);
    }

    public function tags()
    {
        $tags = $this->tagService->getAllTags();

        return TagResource::collection($tags);
    }
}
