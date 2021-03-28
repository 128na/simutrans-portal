<?php

namespace App\Http\Controllers\Api\v2\Mypage\Article;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Article\StoreRequest;
use App\Http\Requests\Api\Article\UpdateRequest;
use App\Http\Resources\Api\Mypage\Articles as ArticlesResouce;
use App\Jobs\Article\JobUpdateRelated;
use App\Models\Article;
use App\Notifications\ArticlePublished;
use App\Notifications\ArticleUpdated;
use App\Services\ArticleEditorService;
use App\Services\ArticleService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EditorController extends Controller
{
    private ArticleService $articleService;
    private ArticleEditorService $articleEditorService;

    public function __construct(
        ArticleEditorService $articleEditorService,
        ArticleService $articleService
    ) {
        $this->articleEditorService = $articleEditorService;
        $this->articleService = $articleService;
    }

    public function index()
    {
        return new ArticlesResouce(
            $this->articleEditorService->getArticles(Auth::user())
        );
    }

    public function options()
    {
        return $this->articleEditorService->getOptions(Auth::user());
    }

    public function store(StoreRequest $request)
    {
        DB::beginTransaction();
        $article = $this->articleEditorService->storeArticle(Auth::user(), $request);

        if ($request->preview) {
            $preview = $this->createPreview($article);
            DB::rollback();

            return $preview;
        }
        dispatch_now(app(JobUpdateRelated::class));
        DB::commit();

        if ($article->is_publish && $request->should_tweet) {
            $article->notify(new ArticlePublished());
        }

        return $this->index();
    }

    public function update(UpdateRequest $request, Article $article)
    {
        DB::beginTransaction();
        $article = $this->articleEditorService->updateArticle($article, $request);

        if ($request->preview) {
            $preview = $this->createPreview($article);
            DB::rollback();

            return $preview;
        }
        dispatch_now(app(JobUpdateRelated::class));
        DB::commit();

        if ($article->is_publish && $request->should_tweet) {
            $article->notify(new ArticleUpdated());
        }

        return $this->index();
    }

    private function createPreview(Article $article)
    {
        $article = $this->articleService->getArticle($article, true);

        $contents = ['preview' => true, 'article' => $article];

        return view('front.articles.show', $contents);
    }
}
