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

class EditorController extends Controller
{
    public function __construct(
        private ArticleEditorService $articleEditorService,
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

    public function store(StoreRequest $request): ArticlesResouce
    {
        $article = DB::transaction(fn () => $this->articleEditorService->storeArticle($this->loggedinUser(), $request));
        JobUpdateRelated::dispatch();
        event(new ArticleStored($article, $request->should_notify));

        return $this->index();
    }

    public function update(UpdateRequest $request, Article $article): ArticlesResouce
    {
        $notYetPublished = is_null($article->published_at);
        $article = DB::transaction(fn () => $this->articleEditorService->updateArticle($article, $request));
        JobUpdateRelated::dispatch();
        event(new ArticleUpdated($article, $request->should_notify, $request->without_update_modified_at, $notYetPublished));

        return $this->index();
    }
}
