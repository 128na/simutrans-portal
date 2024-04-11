<?php

declare(strict_types=1);

namespace App\Actions\Article;

use App\Enums\ArticlePostType;
use App\Enums\ArticleStatus;
use App\Models\Category;
use App\Models\User;
use App\Repositories\CategoryRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;

final readonly class GetOptions
{
    public function __construct(
        private CategoryRepository $categoryRepository,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function __invoke(User $user): array
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
    private function getSeparatedCategories(User $user): SupportCollection
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
    private function getStatuses(): SupportCollection
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
    private function getPostTypes(): SupportCollection
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
}
