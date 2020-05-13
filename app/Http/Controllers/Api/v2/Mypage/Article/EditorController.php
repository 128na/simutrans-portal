<?php

namespace App\Http\Controllers\Api\v2\Mypage\Article;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Article\StoreRequest;
use App\Http\Requests\Api\Article\UpdateRequest;
use App\Http\Resources\Api\Mypage\Articles as ArticlesResouce;
use App\Models\Article;
use App\Notifications\ArticlePublished;
use App\Notifications\ArticleUpdated;
use App\Services\ArticleEditorService;
use App\Services\ArticleService;
use App\Services\PresentationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EditorController extends Controller
{
    private ArticleService $article_service;
    private ArticleEditorService $article_editor_service;

    public function __construct(
        ArticleEditorService $article_editor_service,
        ArticleService $article_service,
        PresentationService $presentation_service
    ) {
        $this->article_editor_service = $article_editor_service;
        $this->article_service = $article_service;
        $this->presentation_service = $presentation_service;
    }

    public function index()
    {
        return new ArticlesResouce(
            $this->article_editor_service->getArticles(Auth::user())
        );
    }

    public function options()
    {
        return $this->article_editor_service->getOptions(Auth::user());
    }

    public function store(StoreRequest $request)
    {
        DB::beginTransaction();
        $article = $this->article_editor_service->storeArticle(Auth::user(), $request);

        if ($request->preview) {
            return $this->createPreview($article);
        }
        DB::commit();

        if ($article->is_publish && $request->should_tweet) {
            $article->notify(new ArticlePublished);
        }

        return $this->index();
    }

    public function update(UpdateRequest $request, Article $article)
    {
        DB::beginTransaction();
        $article = $this->article_editor_service->updateArticle($article, $request);

        if ($request->preview) {
            return $this->createPreview($article);
        }
        DB::commit();

        if ($article->is_publish && $request->should_tweet) {
            $article->notify(new ArticleUpdated);
        }

        return $this->index();
    }

    private function createPreview(Article $article)
    {
        $article = $this->article_service->getArticle($article, true);
        DB::rollback();

        $contents = ['preview' => true, 'article' => $article];
        $contents = array_merge($contents, $this->presentation_service->forShow($article));

        return response(view('front.articles.show', $contents));
    }
}
