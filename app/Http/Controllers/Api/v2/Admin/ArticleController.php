<?php

namespace App\Http\Controllers\Api\v2\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\ArticleUpdateRequest;
use App\Jobs\Article\JobUpdateRelated;
use App\Repositories\ArticleRepository;

class ArticleController extends Controller
{
    private ArticleRepository $articleRepository;

    public function __construct(ArticleRepository $articleRepository)
    {
        $this->articleRepository = $articleRepository;
    }

    public function index()
    {
        return $this->articleRepository->findAllWithTrashed();
    }

    public function update(ArticleUpdateRequest $request, int $id)
    {
        $article = $this->articleRepository->findOrFailWithTrashed($id);
        $this->articleRepository->update($article, $request->validated()['article']);

        JobUpdateRelated::dispatch();

        return $this->index();
    }

    public function destroy(int $id)
    {
        $article = $this->articleRepository->findOrFailWithTrashed($id);
        $this->articleRepository->toggleDelete($article);

        JobUpdateRelated::dispatch();

        return $this->index();
    }
}
