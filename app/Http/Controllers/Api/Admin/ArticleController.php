<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\ArticleUpdateRequest;
use App\Repositories\ArticleRepository;
use Illuminate\Database\Eloquent\Collection;

final class ArticleController extends Controller
{
    public function __construct(private readonly ArticleRepository $articleRepository) {}

    /**
     * @return Collection<int, \App\Models\Article>
     */
    public function index(): Collection
    {
        return $this->articleRepository->findAllWithTrashed();
    }

    /**
     * @return Collection<int, \App\Models\Article>
     */
    public function update(ArticleUpdateRequest $articleUpdateRequest, int $id): Collection
    {
        $article = $this->articleRepository->findOrFailWithTrashed($id);
        /**
         * @var array{article:array<mixed>}
         */
        $validated = $articleUpdateRequest->validated();
        $this->articleRepository->update($article, $validated['article']);

        dispatch_sync(new \App\Jobs\Article\JobUpdateRelated);

        return $this->index();
    }

    /**
     * @return Collection<int, \App\Models\Article>
     */
    public function destroy(int $id): Collection
    {
        $article = $this->articleRepository->findOrFailWithTrashed($id);
        $this->articleRepository->toggleDelete($article);

        dispatch_sync(new \App\Jobs\Article\JobUpdateRelated);

        return $this->index();
    }
}
