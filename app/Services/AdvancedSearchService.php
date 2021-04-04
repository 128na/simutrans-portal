<?php

namespace App\Services;

use App\Repositories\ArticleRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\TagRepository;
use App\Repositories\UserRepository;
use Illuminate\Pagination\LengthAwarePaginator;

class AdvancedSearchservice extends Service
{
    private ArticleRepository $articleRepository;
    private CategoryRepository $categoryRepository;
    private TagRepository $tagRepository;
    private UserRepository $userRepository;

    public function __construct(
        ArticleRepository $articleRepository,
        CategoryRepository $categoryRepository,
        TagRepository $tagRepository,
        UserRepository $userRepository
    ) {
        $this->articleRepository = $articleRepository;
        $this->categoryRepository = $categoryRepository;
        $this->tagRepository = $tagRepository;
        $this->userRepository = $userRepository;
    }

    public function search(array $conditions = []): LengthAwarePaginator
    {
        $word = $conditions['word'] ?? null;

        $categoryAnd = $conditions['categoryAnd'] ?? null;
        $categoryIds = $conditions['categoryIds'] ?? null;
        $categories = empty($categoryIds) ? null : $this->categoryRepository->findByIds($categoryIds);

        $tagAnd = $conditions['tagAnd'] ?? null;
        $tagIds = $conditions['tagIds'] ?? null;
        $tags = empty($tagIds) ? null : $this->tagRepository->findByIds($tagIds);

        $userAnd = $conditions['userAnd'] ?? null;
        $userIds = $conditions['userIds'] ?? null;
        $users = empty($userIds) ? null : $this->userRepository->findByIds($userIds);

        $startAt = $conditions['startAt'] ?? null;
        $endAt = $conditions['endAt'] ?? null;

        $order = $conditions['order'] ?? 'updated_at';
        $direction = $conditions['direction'] ?? 'desc';

        return $this->articleRepository->paginateAdvancedSearch(
            $word, $categories, $categoryAnd, $tags, $tagAnd, $users, $userAnd, $startAt, $endAt, $order, $direction
        );
    }
}
