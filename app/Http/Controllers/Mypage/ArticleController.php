<?php

declare(strict_types=1);

namespace App\Http\Controllers\Mypage;

use App\Actions\Article\StoreArticle;
use App\Actions\Article\UpdateArticle;
use App\Http\Requests\Article\StoreRequest;
use App\Http\Requests\Article\UpdateRequest;
use App\Http\Resources\Mypage\ArticleEdit;
use App\Http\Resources\Mypage\AttachmentEdit;
use App\Http\Resources\Mypage\TagEdit;
use App\Http\Resources\Mypage\UserShow;
use App\Models\Article;
use App\Repositories\ArticleRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\TagRepository;
use App\Services\Front\MetaOgpService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

final class ArticleController extends Controller
{
    public function __construct(
        private readonly ArticleRepository $articleRepository,
        private readonly CategoryRepository $categoryRepository,
        private readonly TagRepository $tagRepository,
        private readonly MetaOgpService $metaOgpService,
    ) {}

    public function index(): View
    {
        $user = Auth::user();

        return view('v2.mypage.articles', [
            'user' => $user->only(['id', 'name', 'nickname']),
            'articles' => $user
                ->articles()
                ->select('id', 'title', 'slug', 'status', 'post_type', 'published_at', 'modified_at')
                ->with('attachments', 'totalConversionCount', 'totalViewCount')
                ->get(),
            'meta' => $this->metaOgpService->mypageArticles(),
        ]);
    }

    public function create(): View
    {
        $user = Auth::user();
        if ($user->cannot('store', Article::class)) {
            return abort(403);
        }

        return view('v2.mypage.article-create', [
            'user' => new UserShow($user),
            'attachments' => AttachmentEdit::collection($user->myAttachments()->with('fileInfo')->get()),
            'categories' => $this->categoryRepository->getForSearch()->groupBy('type'),
            'tags' => TagEdit::collection($this->tagRepository->getForEdit()),
            'relationalArticles' => $this->articleRepository->getForEdit(),
            'meta' => $this->metaOgpService->mypageArticleCreate(),
        ]);
    }

    public function store(StoreRequest $storeRequest, StoreArticle $storeArticle): JsonResponse
    {
        $user = Auth::user();
        if ($user->cannot('store', Article::class)) {
            return abort(403);
        }

        /**
         * @var array{should_notify?:bool,article:array{status:string,title:string,slug:string,post_type:string,published_at?:string,contents:mixed}}
         */
        $data = $storeRequest->validated();

        $article = DB::transaction(fn (): Article => $storeArticle($user, $data));

        return response()->json(['article_id' => $article->id], 200);
    }

    public function edit(Article $article): View
    {
        $user = Auth::user();
        if ($user->cannot('update', $article)) {
            return abort(403);
        }

        return view('v2.mypage.article-edit', [
            'user' => new UserShow($user),
            'article' => new ArticleEdit($article->load('categories', 'tags', 'articles', 'attachments')),
            'attachments' => AttachmentEdit::collection($user->myAttachments()->with('fileInfo')->get()),
            'categories' => $this->categoryRepository->getForSearch()->groupBy('type'),
            'tags' => $this->tagRepository->getForEdit(),
            'relationalArticles' => $this->articleRepository->getForEdit($article),
            'meta' => $this->metaOgpService->mypageArticleEdit(),
        ]);
    }

    public function update(UpdateRequest $updateRequest, Article $article, UpdateArticle $updateArticle): JsonResponse
    {
        $user = Auth::user();
        if ($user->cannot('update', $article)) {
            return abort(403);
        }

        /**
         * @var array{should_notify?:bool,article:array{status:string,title:string,slug:string,post_type:string,published_at?:string,contents:mixed}}
         */
        $data = $updateRequest->validated();

        $article = DB::transaction(fn (): Article => $updateArticle($article, $data));

        return response()->json(['article_id' => $article->id], 200);
    }
}
