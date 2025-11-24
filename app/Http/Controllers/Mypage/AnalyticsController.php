<?php

declare(strict_types=1);

namespace App\Http\Controllers\Mypage;

use App\Actions\Analytics\FindArticles;
use App\Http\Requests\ArticleAnalytics\SearchRequest;
use App\Http\Resources\Mypage\ArticleAnalytic;
use App\Repositories\ArticleRepository;
use App\Services\Front\MetaOgpService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OA;

final class AnalyticsController extends Controller
{
    public function __construct(
        private readonly ArticleRepository $articleRepository,
        private readonly MetaOgpService $metaOgpService,
    ) {}

    public function index(): \Illuminate\Contracts\View\View
    {
        $user = Auth::user();

        return view('mypage.analytics', [
            'articles' => $this->articleRepository->getForAnalyticsList($user),
            'meta' => $this->metaOgpService->mypageAnalytics(),
        ]);
    }

    /**
     * アナリティクスデータを取得
     *
     * @OA\Post(
     *     path="/v2/analytics",
     *     summary="アナリティクスの取得",
     *     description="記事のアナリティクスデータを取得します",
     *     tags={"Analytics"},
     *     security={{"sanctum": {}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="article_ids", type="array", description="記事ID配列", @OA\Items(type="integer")),
     *             @OA\Property(property="start_date", type="string", format="date", example="2024-01-01", description="開始日"),
     *             @OA\Property(property="end_date", type="string", format="date", example="2024-12-31", description="終了日")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="取得成功",
     *
     *         @OA\JsonContent(
     *             type="array",
     *
     *             @OA\Items(
     *
     *                 @OA\Property(property="article_id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="記事タイトル"),
     *                 @OA\Property(property="views", type="integer", example=100),
     *                 @OA\Property(property="downloads", type="integer", example=50)
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="バリデーションエラー",
     *
     *         @OA\JsonContent(ref="#/components/schemas/Error")
     *     ),
     *
     *     @OA\Response(
     *         response=403,
     *         description="権限エラー",
     *
     *         @OA\JsonContent(ref="#/components/schemas/Error")
     *     )
     * )
     */
    public function show(SearchRequest $searchRequest, FindArticles $findArticles): AnonymousResourceCollection
    {
        $user = Auth::user();

        return ArticleAnalytic::collection(
            $findArticles($user, $searchRequest)
        );
    }
}
