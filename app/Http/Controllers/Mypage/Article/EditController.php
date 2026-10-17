<?php

declare(strict_types=1);

namespace App\Http\Controllers\Mypage\Article;

use App\Actions\Article\Data\UpdateArticleData;
use App\Actions\Article\UpdateArticle;
use App\Http\Controllers\Controller;
use App\Http\Requests\Article\UpdateRequest;
use App\Http\Resources\Mypage\ArticleEdit;
use App\Http\Resources\Mypage\AttachmentEdit;
use App\Http\Resources\Mypage\UserShow;
use App\Models\Article;
use App\Repositories\Article\MypageArticleRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\TagRepository;
use App\Services\Front\MetaOgpService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class EditController extends Controller
{
    public function __construct(
        private readonly MypageArticleRepository $mypageArticleRepository,
        private readonly CategoryRepository $categoryRepository,
        private readonly TagRepository $tagRepository,
        private readonly MetaOgpService $metaOgpService,
    ) {}

    public function edit(Article $article): View
    {
        $this->authorize('update', $article);
        $user = $this->loggedinUser();

        return view('mypage.article-edit', [
            'user' => new UserShow($user),
            'article' => new ArticleEdit($article->load('categories', 'tags', 'articles', 'attachments')),
            'attachments' => AttachmentEdit::collection($user->myAttachments()->with('fileInfo')->get()),
            'categories' => $this->categoryRepository->getForSearch()->groupBy('type'),
            'tags' => $this->tagRepository->getForEdit(),
            'relationalArticles' => $this->mypageArticleRepository->getForEdit($article),
            'meta' => $this->metaOgpService->mypageArticleEdit(),
        ]);
    }

    /**
     * 記事を更新
     */
    public function update(UpdateRequest $updateRequest, Article $article, UpdateArticle $updateArticle): JsonResponse
    {
        $this->authorize('update', $article);

        /**
         * @var array{should_notify?:bool,without_update_modified_at?:bool,follow_redirect?:bool,article:array{status:string,title:string,slug:string,post_type?:string,published_at?:string,contents:mixed,categories?:array<int>,tags?:array<int>,articles?:array<int>}} $validated
         */
        $validated = $updateRequest->validated();
        $updateArticleData = UpdateArticleData::fromArray($validated);

        $article = DB::transaction(fn (): Article => $updateArticle($article, $updateArticleData));

        return response()->json(['article_id' => $article->id], 200);
    }
}
