<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Mypage;

use App\Actions\Article\FindArticle;
use App\Actions\Article\GetOptions;
use App\Actions\Article\StoreArticle;
use App\Actions\Article\UpdateArticle;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Article\StoreRequest;
use App\Http\Requests\Api\Article\UpdateRequest;
use App\Http\Resources\Api\Mypage\Articles as ArticlesResouce;
use App\Models\Article;
use Illuminate\Support\Facades\DB;

final class EditorController extends Controller
{
    public function index(FindArticle $findArticle): ArticlesResouce
    {
        return $findArticle($this->loggedinUser());
    }

    /**
     * @return array<mixed>
     */
    public function options(GetOptions $getOptions): array
    {
        return $getOptions($this->loggedinUser());
    }

    public function store(StoreRequest $storeRequest, StoreArticle $storeArticle, FindArticle $findArticle): ArticlesResouce
    {
        /**
         * @var array{should_notify?:bool,article:array{status:string,title:string,slug:string,post_type:string,published_at?:string,contents:mixed}}
         */
        $data = $storeRequest->validated();

        DB::transaction(fn (): Article => $storeArticle($this->loggedinUser(), $data));

        return $this->index($findArticle);
    }

    public function update(UpdateRequest $updateRequest, Article $article, UpdateArticle $updateArticle, FindArticle $findArticle): ArticlesResouce
    {
        /**
         * @var array{should_notify?:bool,without_update_modified_at?:bool,follow_redirect?:bool,article:array{status:string,title:string,slug:string,post_type:string,published_at?:string,contents:mixed}}
         */
        $data = $updateRequest->validated();

        DB::transaction(fn (): Article => $updateArticle($article, $data));

        return $this->index($findArticle);
    }
}
