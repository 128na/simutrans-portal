<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\ArticlePostType;
use App\Enums\ArticleStatus;
use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use App\Repositories\ArticleRepository;
use App\Repositories\CategoryRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;

final readonly class ArticleEditorService
{
    public function __construct(
        private ArticleRepository $articleRepository,
        private CategoryRepository $categoryRepository,
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
     * @return SupportCollection<int,array{label:string,value:string}>
     */
    public function getStatuses(): SupportCollection
    {
        /** @var array<ArticleStatus> */
        $status = ArticleStatus::cases();

        return collect($status)->map(
            fn (ArticleStatus $articleStatus): array => [
                'label' => (string) __('statuses.'.$articleStatus->value),
                'value' => $articleStatus->value,
            ]
        )->values();
    }

    /**
     * @return SupportCollection<int,array{label:string,value:string}>
     */
    public function getPostTypes(): SupportCollection
    {
        /** @var array<ArticlePostType> */
        $postTypes = ArticlePostType::cases();

        return collect($postTypes)->map(
            fn (ArticlePostType $articlePostType): array => [
                'label' => (string) __('post_types.'.$articlePostType->value),
                'value' => $articlePostType->value,
            ]
        )->values();
    }

    public function loadArticle(Article $article): Article
    {
        return $this->articleRepository->loadArticle($article);
    }
}
