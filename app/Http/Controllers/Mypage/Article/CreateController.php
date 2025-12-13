<?php

declare(strict_types=1);

namespace App\Http\Controllers\Mypage\Article;

use App\Actions\Article\StoreArticle;
use App\Http\Requests\Article\StoreRequest;
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
use OpenApi\Attributes as OA;

class CreateController extends Controller
{
    public function __construct(
        private readonly ArticleRepository $articleRepository,
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
            'relationalArticles' => $this->articleRepository->getForEdit(),
            'meta' => $this->metaOgpService->mypageArticleCreate(),
        ]);
    }

    /**
     * 新しい記事を作成
     *
     * @OA\Post(
     *     path="/api/v2/articles",
     *     summary="記事の作成",
     *     description="新しい記事を作成します",
     *     tags={"Articles"},
     *     security={{"sanctum": {}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"article"},
     *
     *             @OA\Property(
     *                 property="article",
     *                 type="object",
     *                 required={"status", "title", "slug", "post_type", "contents"},
     *                 @OA\Property(property="status", type="string", example="publish", description="ステータス", enum={"publish", "draft", "private"}),
     *                 @OA\Property(property="title", type="string", example="新しいアドオン", description="タイトル"),
     *                 @OA\Property(property="slug", type="string", example="new-addon", description="スラッグ"),
     *                 @OA\Property(property="post_type", type="string", example="addon-post", description="投稿タイプ"),
     *                 @OA\Property(property="published_at", type="string", format="date-time", example="2024-01-01T12:00", description="公開日時"),
     *                 @OA\Property(property="contents", type="object", description="コンテンツデータ"),
     *                 @OA\Property(property="categories", type="array", description="カテゴリID配列", @OA\Items(type="integer")),
     *                 @OA\Property(property="tags", type="array", description="タグID配列", @OA\Items(type="integer")),
     *                 @OA\Property(property="articles", type="array", description="関連記事ID配列", @OA\Items(type="integer")),
     *                 @OA\Property(property="attachments", type="array", description="添付ファイルID配列", @OA\Items(type="integer"))
     *             ),
     *             @OA\Property(property="should_notify", type="boolean", example=true, description="通知するかどうか")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="作成成功",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="article_id", type="integer", example=1, description="作成された記事ID")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="バリデーションエラー",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Validation error"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=403,
     *         description="権限エラー",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Forbidden")
     *         )
     *     )
     * )
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
