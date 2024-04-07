<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Mypage;

use App\Events\Article\ArticleStored;
use App\Events\Article\ArticleUpdated;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Article\StoreRequest;
use App\Http\Requests\Api\Article\UpdateRequest;
use App\Http\Resources\Api\Mypage\Articles as ArticlesResouce;
use App\Jobs\Article\JobUpdateRelated;
use App\Models\Article;
use App\Services\ArticleEditorService;
use Illuminate\Support\Facades\DB;

final class EditorController extends Controller
{
    public function __construct(
        private readonly ArticleEditorService $articleEditorService,
    ) {
    }

    public function index(): ArticlesResouce
    {
        return new ArticlesResouce(
            $this->articleEditorService->findArticles($this->loggedinUser())
        );
    }

    /**
     * @return array<mixed>
     */
    public function options(): array
    {
        return $this->articleEditorService->getOptions($this->loggedinUser());
    }

    public function store(StoreRequest $storeRequest): ArticlesResouce
    {
        $article = DB::transaction(fn (): \App\Models\Article => $this->articleEditorService->storeArticle($this->loggedinUser(), $storeRequest));
        JobUpdateRelated::dispatch();
        ArticleStored::dispatch($article, $storeRequest->boolean('should_notify', false));

        return $this->index();
    }

    public function update(UpdateRequest $updateRequest, Article $article): ArticlesResouce
    {
        $notYetPublished = is_null($article->published_at);
        $article = DB::transaction(fn (): \App\Models\Article => $this->articleEditorService->updateArticle($article, $updateRequest));
        JobUpdateRelated::dispatch();

        $shouldNotify = $updateRequest->boolean('should_notify', false) && ! $updateRequest->boolean('without_update_modified_at', false);
        ArticleUpdated::dispatch(
            $article,
            $shouldNotify,
            $notYetPublished
        );

        return $this->index();
    }
}
