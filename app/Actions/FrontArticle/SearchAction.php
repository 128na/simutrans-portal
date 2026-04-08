<?php

declare(strict_types=1);

namespace App\Actions\FrontArticle;

use App\Enums\ArticlePostType;
use App\Http\Resources\Frontend\ArticleList;
use App\Repositories\ArticleRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\TagRepository;
use App\Repositories\UserRepository;
use Illuminate\Pagination\LengthAwarePaginator;

class SearchAction
{
    public function __construct(
        private ArticleRepository $articleRepository,
        private CategoryRepository $categoryRepository,
        private TagRepository $tagRepository,
        private UserRepository $userRepository,
    ) {}

    /**
     * @param  array{word?: string, userIds?: array<int>, categoryIds?: array<int>, tagIds?: array<int>, postTypes?: array<string>}  $condition
     * @return array{condition: array{word?: string, userIds?: array<int>, categoryIds?: array<int>, tagIds?: array<int>, postTypes?: array<string>}, options: array{categories: mixed, tags: mixed, users: mixed, postTypes: array<ArticlePostType>}, articles: mixed}
     */
    public function __invoke(array $condition): array
    {
        $condition = $this->normalizeCondition($condition);

        return [
            'condition' => $condition,
            'options' => $this->options(),
            'articles' => ArticleList::collection($this->search($condition)),
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
     * @return LengthAwarePaginator<int, \App\Models\Article>
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
        if (array_key_exists('word', $condition)) {
            $word = trim((string) $condition['word']);
            if ($word === '') {
                unset($condition['word']);
            } else {
                $condition['word'] = $word;
            }
        }

        foreach (['userIds', 'categoryIds', 'tagIds', 'postTypes'] as $key) {
            if (! array_key_exists($key, $condition)) {
                continue;
            }

            $value = $condition[$key];
            if (! is_array($value) || $value === []) {
                unset($condition[$key]);
            }
        }

        return $condition;
    }
}
