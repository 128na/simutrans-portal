<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Mypage;

use App\Actions\Article\StoreArticle;
use App\Actions\Article\UpdateArticle;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Article\StoreRequest;
use App\Http\Requests\Api\Article\UpdateRequest;
use App\Http\Resources\Api\Mypage\Articles as ArticlesResouce;
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

    public function store(StoreRequest $storeRequest, StoreArticle $storeArticle): ArticlesResouce
    {
        /**
         * @var array{should_notify?:bool,article:array{status:string,title:string,slug:string,post_type:string,published_at?:string,contents:mixed}}
         */
        $data = $storeRequest->validated();

        DB::transaction(fn (): Article => $storeArticle($this->loggedinUser(), $data));

        return $this->index();
    }

    public function update(UpdateRequest $updateRequest, Article $article, UpdateArticle $updateArticle): ArticlesResouce
    {
        /**
         * @var array{should_notify?:bool,without_update_modified_at?:bool,article:array{status:string,title:string,slug:string,post_type:string,published_at?:string,contents:mixed}}
         */
        $data = $updateRequest->validated();

        DB::transaction(fn (): Article => $updateArticle($article, $data));

        return $this->index();
    }
}
