<?php

declare(strict_types=1);

namespace App\Http\Controllers\Mypage\Article;

use App\Actions\Article\StoreArticle;
use App\Http\Requests\Article\StoreRequest;
use App\Http\Resources\Mypage\AttachmentEdit;
use App\Http\Resources\Mypage\TagEdit;
use App\Http\Resources\Mypage\UserShow;
use App\Models\Article;
use App\Repositories\Article\MypageArticleRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\TagRepository;
use App\Services\Front\MetaOgpService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CreateController extends Controller
{
    public function __construct(
        private readonly MypageArticleRepository $mypageArticleRepository,
        private readonly CategoryRepository $categoryRepository,
        private readonly TagRepository $tagRepository,
        private readonly MetaOgpService $metaOgpService,
    ) {}

    public function create(): View
    {
        $user = Auth::user();
        if ($user === null) {
            abort(401);
        }

        if ($user->cannot('store', Article::class)) {
            abort(403);
        }

        return view('mypage.article-create', [
            'user' => new UserShow($user),
            'attachments' => AttachmentEdit::collection($user->myAttachments()->with('fileInfo')->get()),
            'categories' => $this->categoryRepository->getForSearch()->groupBy('type'),
            'tags' => TagEdit::collection($this->tagRepository->getForEdit()),
            'relationalArticles' => $this->mypageArticleRepository->getForEdit(),
            'meta' => $this->metaOgpService->mypageArticleCreate(),
        ]);
    }

    /**
     * 新しい記事を作成
     *
     * OpenAPI定義は App\OpenApi\Paths\ArticleCreatePath を参照。
     */
    public function store(StoreRequest $storeRequest, StoreArticle $storeArticle): JsonResponse
    {
        $user = Auth::user();
        if ($user === null) {
            abort(401);
        }

        if ($user->cannot('store', Article::class)) {
            abort(403);
        }

        /**
         * @var array{should_notify?:bool,article:array{status:string,title:string,slug:string,post_type:string,published_at?:string,contents:mixed}}
         */
        $data = $storeRequest->validated();

        $article = DB::transaction(fn (): Article => $storeArticle($user, $data));

        return response()->json(['article_id' => $article->id], 200);
    }
}
