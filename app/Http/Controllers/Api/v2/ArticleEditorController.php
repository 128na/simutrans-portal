<?php

namespace App\Http\Controllers\Api\v2;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Article\StoreRequest;
use App\Http\Requests\Api\Article\UpdateRequest;
use App\Http\Resources\Article as ArticleResouce;
use App\Models\Article;
use App\Services\ArticleEditorService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ArticleEditorController extends Controller
{
    /**
     * @var ArticleEditorService
     */
    private $article_editor_service;

    public function __construct(ArticleEditorService $article_editor_service)
    {
        $this->article_editor_service = $article_editor_service;
    }

    public function index()
    {
        return [
            'categories' => $this->article_editor_service->getSeparatedCategories(Auth::user()),
            'statuses' => $this->article_editor_service->getStatuses(),
            'post_types' => $this->article_editor_service->getPostTypes(),
        ];
    }

    public function store(StoreRequest $request)
    {
        DB::beginTransaction();
        $article = $this->article_editor_service->storeArticle(Auth::user(), $request);

        if ($request->has('preview')) {
            return $this->createPreview($article);
        }
        DB::commit();

        if ($article->is_publish && $request->filled('should_tweet')) {
            Twitter::articleCreated($article);
        }

        return new ArticleResouce($article);
    }

    public function update(UpdateRequest $request, Article $article)
    {
        DB::beginTransaction();
        $article = $this->article_editor_service->updateArticle($article, $request);

        if ($request->has('preview')) {
            return $this->createPreview($article);
        }
        DB::commit();

        if ($article->is_publish && $request->filled('should_tweet')) {
            Twitter::articleUpdated($article);
        }

        return new ArticleResouce($article);
    }

    private function createPreview(Article $article)
    {
        DB::rollback();
        $preview = true;

        return response(
            view('front.articles.show', compact('article', 'preview'))
        );
    }
}
