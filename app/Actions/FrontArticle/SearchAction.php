<?php

declare(strict_types=1);

namespace App\Actions\FrontArticle;

use App\Enums\ArticlePostType;
use App\Http\Resources\Frontend\ArticleList;
use App\Models\Article;
use App\Repositories\Article\FrontArticleRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\TagRepository;
use App\Repositories\UserRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class SearchAction
{
    public function __construct(
        private FrontArticleRepository $articleRepository,
        private CategoryRepository $categoryRepository,
        private TagRepository $tagRepository,
        private UserRepository $userRepository,
    ) {}

    /**
     * @param  array{word?: string, userIds?: array<int>, categoryIds?: array<int>, tagIds?: array<int>, postTypes?: array<string>}  $condition
     * @return array{condition: array{word?: string, userIds?: array<int>, categoryIds?: array<int>, tagIds?: array<int>, postTypes?: array<string>}, hasSearchConditions: bool, options: array{categories: mixed, tags: mixed, users: mixed, postTypes: array<ArticlePostType>}, articles: mixed}
     */
    public function __invoke(array $condition): array
    {
        $condition = $this->normalizeCondition($condition);

        return [
            'condition' => $condition,
            'hasSearchConditions' => $condition !== [],
            'options' => $this->options(),
            'articles' => ArticleList::collection($this->articleRepository->search($condition)),
        ];
    }

    /**
     * @return array{categories: mixed, tags: mixed, users: mixed, postTypes: array<ArticlePostType>}
     */
    public function options(): array
    {
        return [
            'categories' => $this->categoryRepository->getForSearch(),
            'tags' => $this->tagRepository->getForSearch(),
            'users' => $this->userRepository->getForSearch(),
            'postTypes' => ArticlePostType::cases(),
        ];
    }

    /**
     * @param  array{word?: string, userIds?: array<int>, categoryIds?: array<int>, tagIds?: array<int>, postTypes?: array<string>}  $condition
     * @return LengthAwarePaginator<int, Article>
     */
    public function search(array $condition, int $limit = 24): LengthAwarePaginator
    {
        $condition = $this->normalizeCondition($condition);

        return $this->articleRepository->search($condition, $limit);
    }

    /**
     * @param  array<string, mixed>  $condition
     * @return array<string, mixed>
     */
    private function normalizeCondition(array $condition): array
    {
        $condition = Arr::only($condition, ['word', 'userIds', 'categoryIds', 'tagIds', 'postTypes']);
        $word = is_string($condition['word'] ?? null) ? Str::trim($condition['word']) : '';
        if (Str::trim(str_replace([',', '、', '・'], ' ', $word)) === '') {
            unset($condition['word']);
        } else {
            $condition['word'] = $word;
        }

        foreach (['userIds', 'categoryIds', 'tagIds', 'postTypes'] as $key) {
            if (! array_key_exists($key, $condition)) {
                continue;
            }

            $value = $condition[$key];
            $values = is_array($value) ? array_values(array_filter($value, fn (mixed $item): bool => is_int($item)
                || (is_string($item) && Str::trim($item) !== ''))) : [];
            if ($values === []) {
                unset($condition[$key]);
            } else {
                $condition[$key] = $values;
            }
        }

        return $condition;
    }
}
