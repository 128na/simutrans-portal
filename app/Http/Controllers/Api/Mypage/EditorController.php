<?php

namespace App\Http\Controllers\Api\Mypage;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Article\StoreRequest;
use App\Http\Requests\Api\Article\UpdateRequest;
use App\Http\Resources\Api\Mypage\Articles as ArticlesResouce;
use App\Jobs\Article\JobUpdateRelated;
use App\Models\Article;
use App\Notifications\ArticlePublished;
use App\Notifications\ArticleUpdated;
use App\Services\ArticleEditorService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EditorController extends Controller
{
    private ArticleEditorService $articleEditorService;

    public function __construct(ArticleEditorService $articleEditorService)
    {
        $this->articleEditorService = $articleEditorService;
    }

    public function index(): ArticlesResouce
    {
        return new ArticlesResouce(
            $this->articleEditorService->findArticles(Auth::user())
        );
    }

    /**
     * @return array<mixed>
     */
    public function options(): array
    {
        return $this->articleEditorService->getOptions(Auth::user());
    }

    public function store(StoreRequest $request): ArticlesResouce
    {
        $article = DB::transaction(fn () => $this->articleEditorService->storeArticle(Auth::user(), $request));
        JobUpdateRelated::dispatchAfterResponse();

        if ($article->is_publish && $request->should_tweet) {
            $article->notify(new ArticlePublished());
        }

        return $this->index();
    }

    public function update(UpdateRequest $request, Article $article): ArticlesResouce
    {
        $notYetPublished = is_null($article->published_at);
        $article = DB::transaction(fn () => $this->articleEditorService->updateArticle($article, $request));
        JobUpdateRelated::dispatchAfterResponse();

        $this->handleTweet($article, $request, $notYetPublished);

        return $this->index();
    }

    private function handleTweet(Article $article, UpdateRequest $request, bool $notYetPublished = true): void
    {
        // 公開以外
        if (! $article->is_publish) {
            return;
        }
        // ツイートを希望しない
        if (! $request->should_tweet) {
            return;
        }
        // 更新日を更新しない
        if ($request->without_update_modified_at) {
            return;
        }

        // published_atがnullから初めて変わった場合は新規投稿扱い
        if ($notYetPublished) {
            $article->notify(new ArticlePublished());
        } else {
            $article->notify(new ArticleUpdated());
        }
    }
}
