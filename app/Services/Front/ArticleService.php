<?php

namespace App\Services\Front;

use App\Models\Article;
use App\Models\Tag;
use App\Models\User;
use App\Repositories\ArticleRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\TagRepository;
use App\Services\Service;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ArticleService extends Service
{
    public function __construct(
        private ArticleRepository $articleRepository,
        private CategoryRepository $categoryRepository,
        private TagRepository $tagRepository,
    ) {
    }

    public function loadArticle(Article $article): Article
    {
        return $this->articleRepository->loadArticle($article);
    }

    public function paginateByUser(User $user): LengthAwarePaginator
    {
        return $this->articleRepository->paginateByUser($user);
    }

    public function paginatePages(): LengthAwarePaginator
    {
        return $this->articleRepository->paginatePages();
    }

    public function paginateAnnouces(): LengthAwarePaginator
    {
        return $this->articleRepository->paginateAnnouces();
    }

    public function paginateRanking(): LengthAwarePaginator
    {
        return $this->articleRepository->paginateRanking();
    }

    public function paginateByCategory(string $type, string $slug): LengthAwarePaginator
    {
        $category = $this->categoryRepository->findOrFailByTypeAndSlug($type, $slug);

        return $this->articleRepository->paginateByCategory($category);
    }

    public function paginateByPakAddonCategory(string $pakSlug, string $addonSlug): LengthAwarePaginator
    {
        $pak = $this->categoryRepository->findOrFailByTypeAndSlug('pak', $pakSlug);
        $addon = $this->categoryRepository->findOrFailByTypeAndSlug('addon', $addonSlug);

        return $this->articleRepository->paginateByPakAddonCategory($pak, $addon);
    }

    public function paginateByPakNoneAddonCategory(string $pakSlug): LengthAwarePaginator
    {
        $pak = $this->categoryRepository->findOrFailByTypeAndSlug('pak', $pakSlug);

        return $this->articleRepository->paginateByPakNoneAddonCategory($pak);
    }

    public function paginateByTag(Tag $tag): LengthAwarePaginator
    {
        return $this->articleRepository->paginateByTag($tag);
    }

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
