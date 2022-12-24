<?php

namespace App\Services\Front;

use App\Models\Article;
use App\Models\Tag;
use App\Models\User;
use App\Repositories\ArticleRepository;
use App\Repositories\CategoryRepository;
use App\Services\Service;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\Paginator;

class ArticleService extends Service
{
    public function __construct(
        private ArticleRepository $articleRepository,
        private CategoryRepository $categoryRepository,
    ) {
    }

    public function loadArticle(Article $article): Article
    {
        return $this->articleRepository->loadArticle($article);
    }

    /**
     * @return LengthAwarePaginator<User>
     */
    public function paginateByUser(User $user): LengthAwarePaginator
    {
        return $this->articleRepository->paginateByUser($user);
    }

    /**
     * @return Paginator<User>
     */
    public function paginatePages(bool $simple = false): Paginator
    {
        return $this->articleRepository->paginatePages($simple);
    }

    /**
     * @return Paginator<User>
     */
    public function paginateAnnouces(bool $simple = false): Paginator
    {
        return $this->articleRepository->paginateAnnouces($simple);
    }

    /**
     * @return Paginator<User>
     */
    public function paginateRanking(bool $simple = false): Paginator
    {
        return $this->articleRepository->paginateRanking($simple);
    }

    /**
     * @return Paginator<User>
     */
    public function paginateByCategory(string $type, string $slug, bool $simple = false): Paginator
    {
        $category = $this->categoryRepository->findOrFailByTypeAndSlug($type, $slug);

        return $this->articleRepository->paginateByCategory($category, $simple);
    }

    /**
     * @return LengthAwarePaginator<User>
     */
    public function paginateByPakAddonCategory(string $pakSlug, string $addonSlug): LengthAwarePaginator
    {
        $pak = $this->categoryRepository->findOrFailByTypeAndSlug('pak', $pakSlug);
        $addon = $this->categoryRepository->findOrFailByTypeAndSlug('addon', $addonSlug);

        return $this->articleRepository->paginateByPakAddonCategory($pak, $addon);
    }

    /**
     * @return LengthAwarePaginator<User>
     */
    public function paginateByPakNoneAddonCategory(string $pakSlug): LengthAwarePaginator
    {
        $pak = $this->categoryRepository->findOrFailByTypeAndSlug('pak', $pakSlug);

        return $this->articleRepository->paginateByPakNoneAddonCategory($pak);
    }

    /**
     * @return LengthAwarePaginator<User>
     */
    public function paginateByTag(Tag $tag): LengthAwarePaginator
    {
        return $this->articleRepository->paginateByTag($tag);
    }

    /**
     * @return LengthAwarePaginator<User>
     */
    public function paginateBySearch(string $word): LengthAwarePaginator
    {
        return $this->articleRepository->paginateBySearch($word);
    }

    public function validateCategoryByTypeAndSlug(string $type, string $slug): void
    {
        $this->categoryRepository->findOrFailByTypeAndSlug($type, $slug);
    }

    public function validateCategoryByPakAndAddon(string $pakSlug, string $addonSlug): void
    {
        $this->categoryRepository->findOrFailByTypeAndSlug('pak', $pakSlug);
        $this->categoryRepository->findOrFailByTypeAndSlug('addon', $addonSlug);
    }
}
