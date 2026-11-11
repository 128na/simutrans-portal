<?php

declare(strict_types=1);

namespace App\Actions\FrontArticle;

use App\Enums\CategoryType;
use App\Models\Article;
use App\Models\Category;
use App\Repositories\Article\FrontArticleRepository;
use App\Services\Front\ArticleListFilter;
use Illuminate\Pagination\LengthAwarePaginator;

class LatestAction
{
    public function __construct(private FrontArticleRepository $articleRepository) {}

    /**
     * @return LengthAwarePaginator<int, Article>
     */
    public function allPak(int $limit = 24): LengthAwarePaginator
    {
        return $this->articleRepository->getLatestAllPak($limit);
    }

    /**
     * @return LengthAwarePaginator<int, Article>
     */
    public function byPak(string $pak, int $limit = 24, ArticleListFilter $filter = new ArticleListFilter): LengthAwarePaginator
    {
        return $this->articleRepository->paginateLatest($pak, $limit, $filter);
    }

    /**
     * 新着カードの保存データ。post_type・categories・user は一覧のカード（ArticleCard）と同じ表示に使う。
     * paks は旧コードへ戻したときも pak のタグを出せるよう残す
     *
     * @return list<array{id: int, user_id: int, title: string, url: string, thumbnail_url: string, published_at: ?string, modified_at: ?string, paks: list<string>, post_type: string, categories: list<array{id: int, type: string, slug: string}>, user: array{name: string, nickname: ?string}}>
     */
    public function cardsByPak(string $pak, int $limit = 12): array
    {
        return array_values($this->articleRepository->getLatestForCards($pak, $limit)
            ->map(fn (Article $article): array => [
                'id' => $article->id,
                'user_id' => $article->user_id,
                'title' => $article->title,
                'url' => $article->showUrl(),
                'thumbnail_url' => $article->thumbnail_url,
                'published_at' => $article->published_at?->toIso8601String(),
                'modified_at' => $article->modified_at?->toIso8601String(),
                'paks' => array_values($article->categories
                    ->filter(fn (Category $category): bool => $category->type === CategoryType::Pak)
                    ->pluck('slug')->all()),
                'post_type' => $article->post_type->value,
                'categories' => array_values($article->categories->map(fn (Category $category): array => [
                    'id' => $category->id,
                    'type' => $category->type->value,
                    'slug' => $category->slug,
                ])->all()),
                'user' => ['name' => $article->user->name, 'nickname' => $article->user->nickname],
            ])->all());
    }

    /**
     * @return LengthAwarePaginator<int, Article>
     */
    public function others(int $limit = 24, ArticleListFilter $filter = new ArticleListFilter): LengthAwarePaginator
    {
        return $this->articleRepository->getLatestOther($limit, $filter);
    }
}
