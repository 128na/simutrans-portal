<?php

declare(strict_types=1);

namespace App\Actions\FrontArticle;

use App\Enums\ArticlePostType;
use App\Http\Resources\Frontend\ArticleList;
use App\Repositories\ArticleRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\TagRepository;
use App\Repositories\UserRepository;

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
        return [
            'condition' => $condition,
            'options' => [
                'categories' => $this->categoryRepository->getForSearch(),
                'tags' => $this->tagRepository->getForSearch(),
                'users' => $this->userRepository->getForSearch(),
                'postTypes' => ArticlePostType::cases(),
            ],
            'articles' => ArticleList::collection($this->articleRepository->search($condition)),
        ];
    }
}
