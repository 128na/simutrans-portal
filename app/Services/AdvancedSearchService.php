<?php

namespace App\Services;

use App\Repositories\ArticleRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\TagRepository;
use App\Repositories\UserAddonCountRepository;
use App\Repositories\UserRepository;
use Carbon\CarbonImmutable;
use Illuminate\Pagination\LengthAwarePaginator;

class AdvancedSearchService extends Service
{
    private ArticleRepository $articleRepository;
    private CategoryRepository $categoryRepository;
    private TagRepository $tagRepository;
    private UserRepository $userRepository;
    private UserAddonCountRepository $userAddonCountRepository;

    public function __construct(
        ArticleRepository $articleRepository,
        CategoryRepository $categoryRepository,
        TagRepository $tagRepository,
        UserRepository $userRepository,
        UserAddonCountRepository $userAddonCountRepository
    ) {
        $this->articleRepository = $articleRepository;
        $this->categoryRepository = $categoryRepository;
        $this->tagRepository = $tagRepository;
        $this->userRepository = $userRepository;
        $this->userAddonCountRepository = $userAddonCountRepository;
    }

    public function search(array $conditions = []): LengthAwarePaginator
    {
        $word = $conditions['word'] ?? null;

        $categoryAnd = $conditions['categoryAnd'] ?? true;
        $categoryIds = $conditions['categoryIds'] ?? null;
        $categories = empty($categoryIds) ? null : $this->categoryRepository->findByIds($categoryIds);

        $tagAnd = $conditions['tagAnd'] ?? true;
        $tagIds = $conditions['tagIds'] ?? null;
        $tags = empty($tagIds) ? null : $this->tagRepository->findByIds($tagIds);

        $userAnd = $conditions['userAnd'] ?? true;
        $userIds = $conditions['userIds'] ?? null;
        $users = empty($userIds) ? null : $this->userRepository->findByIds($userIds);

        $startAt = is_null($conditions['startAt'] ?? null) ? null : CarbonImmutable::parse($conditions['startAt']);
        $endAt = is_null($conditions['endAt'] ?? null) ? null : CarbonImmutable::parse($conditions['endAt']);

        $order = $conditions['order'] ?? 'updated_at';
        $direction = $conditions['direction'] ?? 'desc';

        return $this->articleRepository->paginateByAdvancedSearch(
            $word,
            $categories,
            $categoryAnd,
            $tags,
            $tagAnd,
            $users,
            $userAnd,
            $startAt,
            $endAt,
            $order,
            $direction
        );
    }

    public function getOptions(): array
    {
        return [
            'categories' => $this->categoryRepository->findAll(['id', 'slug', 'type']),
            'tags' => $this->tagRepository->findAll(['id', 'name']),
            'userAddonCounts' => $this->userAddonCountRepository->findAll(['user_id', 'user_name']),
        ];
    }
}
