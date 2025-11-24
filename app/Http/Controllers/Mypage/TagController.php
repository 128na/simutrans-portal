<?php

declare(strict_types=1);

namespace App\Http\Controllers\Mypage;

use App\Http\Requests\Tag\StoreRequest;
use App\Http\Requests\Tag\UpdateRequest;
use App\Http\Resources\Mypage\TagEdit;
use App\Models\Tag;
use App\Repositories\TagRepository;
use App\Services\Front\MetaOgpService;
use Illuminate\Contracts\View\View;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OA;

final class TagController extends Controller
{
    public function __construct(
        private readonly TagRepository $tagRepository,
        private readonly MetaOgpService $metaOgpService,
    ) {}

    public function index(): View
    {
        return view('mypage.tags', [
            'tags' => TagEdit::collection($this->tagRepository->getForEdit()),
            'meta' => $this->metaOgpService->mypageTags(),
        ]);
    }

    /**
     * 新しいタグを作成
     *
     * @OA\Post(
     *     path="/api/v2/tags",
     *     summary="タグの作成",
     *     description="新しいタグを作成します",
     *     tags={"Tags"},
     *     security={{"sanctum": {}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"name", "description"},
     *
     *             @OA\Property(property="name", type="string", example="pak128.japan", description="タグ名"),
     *             @OA\Property(property="description", type="string", example="pak128.japan用アドオン", description="説明")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="作成成功",
     *
     *         @OA\JsonContent(ref="#/components/schemas/Tag")
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
    public function store(StoreRequest $storeRequest): TagEdit
    {
        $tag = $this->tagRepository->store([
            'name' => $storeRequest->input('name'),
            'description' => $storeRequest->input('description'),
            'created_by' => Auth::id(),
            'last_modified_by' => Auth::id(),
            'last_modified_at' => now(),
        ]);

        return new TagEdit($this->tagRepository->load($tag));
    }

    /**
     * タグを更新
     *
     * @OA\Post(
     *     path="/api/v2/tags/{tag}",
     *     summary="タグの更新",
     *     description="既存のタグを更新します",
     *     tags={"Tags"},
     *     security={{"sanctum": {}}},
     *
     *     @OA\Parameter(
     *         name="tag",
     *         in="path",
     *         required=true,
     *         description="タグID",
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"description"},
     *
     *             @OA\Property(property="description", type="string", example="更新された説明", description="説明")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="更新成功",
     *
     *         @OA\JsonContent(ref="#/components/schemas/Tag")
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
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="タグが見つかりません",
     *
     *         @OA\JsonContent(ref="#/components/schemas/Error")
     *     )
     * )
     */
    public function update(Tag $tag, UpdateRequest $updateRequest): TagEdit
    {
        $old = $tag->description;
        if (Auth::user()->cannot('update', $tag)) {
            return abort(403);
        }

        $tag = $this->tagRepository->update($tag, [
            'description' => $updateRequest->input('description'),
            'last_modified_by' => Auth::id(),
            'last_modified_at' => now(),
        ]);
        event(new \App\Events\Tag\TagDescriptionUpdated($tag, Auth::user(), $old));

        return new TagEdit($this->tagRepository->load($tag));
    }
}
