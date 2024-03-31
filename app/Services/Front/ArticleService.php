<?php

declare(strict_types=1);

namespace App\Services\Front;

use App\Enums\CategoryType;
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
    public const ORDER_BY_PUBLISHED_AT = 'published_at';

    public const ORDER_BY_MODIFIED_AT = 'modified_at';

    public function __construct(
        private readonly ArticleRepository $articleRepository,
        private readonly CategoryRepository $categoryRepository,
    ) {
    }

    public function loadArticle(Article $article): Article
    {
        return $this->articleRepository->loadArticle($article);
    }

    /**
     * @return LengthAwarePaginator<Article>
     */
    public function paginateByUser(User $user, string $order = self::ORDER_BY_MODIFIED_AT): LengthAwarePaginator
    {
        return $this->articleRepository->paginateByUser($user, $order);
    }

    /**
     * @return Paginator<Article>
     */
    public function paginatePages(bool $simple = false, string $order = self::ORDER_BY_MODIFIED_AT): Paginator
    {
        return $this->articleRepository->paginatePages($simple, $order);
    }

    /**
     * @return Paginator<Article>
     */
    public function paginateAnnouces(bool $simple = false, string $order = self::ORDER_BY_MODIFIED_AT): Paginator
    {
        return $this->articleRepository->paginateAnnouces($simple, $order);
    }

    /**
     * @return Paginator<Article>
     */
    public function paginateRanking(bool $simple = false): Paginator
    {
        return $this->articleRepository->paginateRanking($simple);
    }

    /**
     * @return Paginator<Article>
     */
    public function paginateByCategory(CategoryType $categoryType, string $slug, bool $simple = false, string $order = self::ORDER_BY_MODIFIED_AT): Paginator
    {
        $category = $this->categoryRepository->findOrFailByTypeAndSlug($categoryType, $slug);

        return $this->articleRepository->paginateByCategory($category, $simple, $order);
    }

    /**
     * @return LengthAwarePaginator<Article>
     */
    public function paginateByPakAddonCategory(string $pakSlug, string $addonSlug, string $order = self::ORDER_BY_MODIFIED_AT): LengthAwarePaginator
    {
        $category = $this->categoryRepository->findOrFailByTypeAndSlug(CategoryType::Pak, $pakSlug);
        $addon = $this->categoryRepository->findOrFailByTypeAndSlug(CategoryType::Addon, $addonSlug);

        return $this->articleRepository->paginateByPakAddonCategory($category, $addon, $order);
    }

    /**
     * @return LengthAwarePaginator<Article>
     */
    public function paginateByPakNoneAddonCategory(string $pakSlug, string $order = self::ORDER_BY_MODIFIED_AT): LengthAwarePaginator
    {
        $category = $this->categoryRepository->findOrFailByTypeAndSlug(CategoryType::Pak, $pakSlug);

        return $this->articleRepository->paginateByPakNoneAddonCategory($category, $order);
    }

    /**
     * @return LengthAwarePaginator<Article>
     */
    public function paginateByTag(Tag $tag, string $order = self::ORDER_BY_MODIFIED_AT): LengthAwarePaginator
    {
        return $this->articleRepository->paginateByTag($tag, $order);
    }

    /**
     * @return LengthAwarePaginator<Article>
     */
    public function paginateBySearch(string $word, string $order = self::ORDER_BY_MODIFIED_AT): LengthAwarePaginator
    {
        return $this->articleRepository->paginateBySearch($word, $order);
    }

    public function validateCategoryByTypeAndSlug(CategoryType $categoryType, string $slug): void
    {
        $this->categoryRepository->findOrFailByTypeAndSlug($categoryType, $slug);
    }

    public function validateCategoryByPakAndAddon(string $pakSlug, string $addonSlug): void
    {
        $this->categoryRepository->findOrFailByTypeAndSlug(CategoryType::Pak, $pakSlug);
        $this->categoryRepository->findOrFailByTypeAndSlug(CategoryType::Addon, $addonSlug);
    }

    public function prArticle(): ?Article
    {
        return $this->articleRepository->getRandomPr();
    }
}
