<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\ArticlePostType;
use App\Enums\ArticleStatus;
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
        private readonly ArticleRepository $articleRepository,
        private readonly CategoryRepository $categoryRepository,
        private readonly CarbonImmutable $now,
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
        $fn = function (array $list, Category $category): array {
            if (! isset($list[$category->type->value])) {
                $list[$category->type->value] = [];
            }

            $list[$category->type->value][] = [
                'id' => $category->id,
                'name' => __(sprintf('category.%s.%s', $category->type->value, $category->slug)),
                'type' => $category->type,
                'slug' => $category->slug,
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
        /** @var array<ArticleStatus> */
        $status = ArticleStatus::cases();

        return collect($status)->map(
            fn ($item): array => [
                'label' => __('statuses.'.$item->value),
                'value' => $item->value,
            ]
        )->values();
    }

    /**
     * @return SupportCollection<int, mixed>
     */
    public function getPostTypes(): SupportCollection
    {
        /** @var array<ArticlePostType> */
        $postTypes = ArticlePostType::cases();

        return collect($postTypes)->map(
            fn ($item): array => [
                'label' => __('post_types.'.$item->value),
                'value' => $item->value,
            ]
        )->values();
    }

    public function storeArticle(User $user, StoreRequest $storeRequest): Article
    {
        $data = [
            'post_type' => ArticlePostType::from((string) $storeRequest->string('article.post_type', '')),
            'title' => $storeRequest->input('article.title'),
            'slug' => $storeRequest->input('article.slug'),
            'status' => ArticleStatus::from((string) $storeRequest->string('article.status', '')),
            'contents' => $storeRequest->input('article.contents'),
            'published_at' => $this->getPublishedAt($storeRequest),
            'modified_at' => $this->now->toDateTimeString(),
        ];
        /** @var Article */
        $article = $this->articleRepository->storeByUser($user, $data);

        $this->syncRelated($article, $storeRequest);

        return $article->fresh() ?? $article;
    }

    private function getPublishedAt(StoreRequest|UpdateRequest $request): ?string
    {
        $articleStatus = ArticleStatus::from((string) $request->string('article.status', ''));
        if ($articleStatus === ArticleStatus::Publish) {
            return $this->now->toDateTimeString();
        }

        if ($articleStatus === ArticleStatus::Reservation) {
            return $request->input('article.published_at');
        }

        return null;
    }

    public function updateArticle(Article $article, UpdateRequest $updateRequest): Article
    {
        $data = [
            'title' => $updateRequest->input('article.title'),
            'slug' => $updateRequest->input('article.slug'),
            'status' => $updateRequest->input('article.status'),
            'contents' => $updateRequest->input('article.contents'),
        ];
        if ($article->is_reservation) {
            $data['published_at'] = $this->getPublishedAt($updateRequest);
        }

        if ($this->inactiveToPublish($article, $updateRequest)) {
            $data['published_at'] = $this->getPublishedAt($updateRequest);
        }

        if ($this->shouldUpdateModifiedAt($updateRequest)) {
            $data['modified_at'] = $this->now->toDateTimeString();
        }

        $this->articleRepository->update($article, $data);

        $this->syncRelated($article, $updateRequest);

        $article = $article->fresh() ?? $article;

        return $article;
    }

    private function inactiveToPublish(Article $article, UpdateRequest $updateRequest): bool
    {
        return $article->is_inactive
            && (ArticleStatus::from((string) $updateRequest->string('article.status', '')) === ArticleStatus::Publish
            || ArticleStatus::from((string) $updateRequest->string('article.status', '')) === ArticleStatus::Reservation
            );
    }

    private function shouldUpdateModifiedAt(UpdateRequest $updateRequest): bool
    {
        return ! $updateRequest->input('without_update_modified_at');
    }

    private function syncRelated(Article $article, BaseRequest $baseRequest): void
    {
        // 添付
        $attachmentIds = collect([
            $baseRequest->input('article.contents.thumbnail'),
            $baseRequest->input('article.contents.file'),
        ])
            ->merge($baseRequest->input('article.contents.sections.*.id', []))
            ->filter()
            ->toArray();
        $this->articleRepository->syncAttachments($article, $attachmentIds);
        $articleIds = $baseRequest->input('article.articles.*.id', []);
        logger('articleIds', [$articleIds]);
        $this->articleRepository->syncArticles($article, $articleIds);

        // カテゴリ
        $categoryIds = $baseRequest->input('article.categories.*.id', []);
        $this->articleRepository->syncCategories($article, $categoryIds);

        // タグ
        $tagIds = $baseRequest->input('article.tags.*.id', []);
        $this->articleRepository->syncTags($article, $tagIds);
    }

    public function loadArticle(Article $article): Article
    {
        return $this->articleRepository->loadArticle($article);
    }
}
