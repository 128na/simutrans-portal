<?php

namespace App\Services;

use App\Http\Requests\Api\Article\BaseRequest;
use App\Http\Requests\Api\Article\StoreRequest;
use App\Http\Requests\Api\Article\UpdateRequest;
use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use App\Repositories\ArticleRepository;
use App\Repositories\CategoryRepository;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;

class ArticleEditorService extends Service
{
    public function __construct(
        private ArticleRepository $articleRepository,
        private CategoryRepository $categoryRepository,
        private CarbonImmutable $now
    ) {
    }

    /**
     * @return Collection<int, Article>
     */
    public function findArticles(User $user): Collection
    {
        return $this->articleRepository->findAllByUser($user, ArticleRepository::MYPAGE_RELATIONS);
    }

    /**
     * @return array<string, mixed>
     */
    public function getOptions(User $user): array
    {
        return [
            'categories' => $this->getSeparatedCategories($user),
            'statuses' => $this->getStatuses(),
            'post_types' => $this->getPostTypes(),
        ];
    }

    /**
     * @return SupportCollection<string, mixed>
     */
    public function getSeparatedCategories(User $user): SupportCollection
    {
        $categories = $this->categoryRepository->findAllByUser($user);

        return $this->separateCategories($categories);
    }

    /**
     * タイプ別に分類したカテゴリ一覧を返す.
     *
     * @param  Collection<int, Category>  $categories
     * @return SupportCollection<string, mixed>
     */
    private function separateCategories(Collection $categories): SupportCollection
    {
        /** @return array<string, mixed> */
        $fn = function (array $list, Category $item): array {
            if (!isset($list[$item->type])) {
                $list[$item->type] = [];
            }
            $list[$item->type][] = [
                'id' => $item->id,
                'name' => __("category.{$item->type}.{$item->slug}"),
                'type' => $item->type,
                'slug' => $item->slug,
            ];

            return $list;
        };

        return collect($categories->reduce($fn, []));
    }

    /**
     * @return SupportCollection<int, mixed>
     */
    public function getStatuses(): SupportCollection
    {
        /** @var array<string> */
        $status = config('status');

        return collect($status)->map(
            fn ($item): array => [
                'label' => __("statuses.{$item}"),
                'value' => $item,
            ]
        )->values();
    }

    /**
     * @return SupportCollection<int, mixed>
     */
    public function getPostTypes(): SupportCollection
    {
        /** @var array<string> */
        $postTypes = config('post_types');

        return collect($postTypes)->map(
            fn ($item) => [
                'label' => __("post_types.{$item}"),
                'value' => $item,
            ]
        )->values();
    }

    public function storeArticle(User $user, StoreRequest $request): Article
    {
        $data = [
            'post_type' => $request->input('article.post_type'),
            'title' => $request->input('article.title'),
            'slug' => $request->input('article.slug'),
            'status' => $request->input('article.status'),
            'contents' => $request->input('article.contents'),
            'published_at' => $this->getPublishedAt($request),
            'modified_at' => $this->now->toDateTimeString(),
        ];
        /** @var Article */
        $article = $this->articleRepository->storeByUser($user, $data);

        $this->syncRelated($article, $request);

        return $article->fresh() ?? $article;
    }

    private function getPublishedAt(StoreRequest|UpdateRequest $request): ?string
    {
        $status = $request->input('article.status');
        if ($status === config('status.publish')) {
            return $this->now->toDateTimeString();
        }

        if ($status === config('status.reservation')) {
            return $request->input('article.published_at');
        }

        return null;
    }

    public function updateArticle(Article $article, UpdateRequest $request): Article
    {
        $data = [
            'title' => $request->input('article.title'),
            'slug' => $request->input('article.slug'),
            'status' => $request->input('article.status'),
            'contents' => $request->input('article.contents'),
        ];
        if ($article->is_reservation) {
            $data['published_at'] = $this->getPublishedAt($request);
        }
        if ($this->inactiveToPublish($article, $request)) {
            $data['published_at'] = $this->getPublishedAt($request);
        }
        if ($this->shouldUpdateModifiedAt($request)) {
            $data['modified_at'] = $this->now->toDateTimeString();
        }
        $this->articleRepository->update($article, $data);

        $this->syncRelated($article, $request);

        return $article->fresh() ?? $article;
    }

    private function inactiveToPublish(Article $article, UpdateRequest $request): bool
    {
        return $article->is_inactive && ($request->input('article.status') === config('status.publish')
            || $request->input('article.status') === config('status.reservation')
        );
    }

    private function shouldUpdateModifiedAt(UpdateRequest $request): bool
    {
        return !$request->input('without_update_modified_at');
    }

    private function syncRelated(Article $article, BaseRequest $request): void
    {
        // 添付
        $attachmentIds = collect([
            $request->input('article.contents.thumbnail'),
            $request->input('article.contents.file'),
        ])
            ->merge($request->input('article.contents.sections.*.id', []))
            ->filter()
            ->toArray();
        $this->articleRepository->syncAttachments($article, $attachmentIds);

        // カテゴリ
        $categoryIds = $request->input('article.categories.*.id', []);
        $this->articleRepository->syncCategories($article, $categoryIds);

        // タグ
        $tagIds = $request->input('article.tags.*.id', []);
        $this->articleRepository->syncTags($article, $tagIds);
    }

    public function loadArticle(Article $article): Article
    {
        return $this->articleRepository->loadArticle($article);
    }
}
