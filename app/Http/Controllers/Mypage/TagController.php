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

class TagController extends Controller
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
     */
    #[OA\Post(
        path: '/api/v2/tags',
        summary: 'タグの作成',
        description: '新しいタグを作成します',
        tags: ['Tags'],
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'description'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'pak128.japan', description: 'タグ名'),
                    new OA\Property(property: 'description', type: 'string', example: 'pak128.japan用アドオン', description: '説明'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: '作成成功',
                content: new OA\JsonContent(ref: '#/components/schemas/Tag')
            ),
            new OA\Response(
                response: 400,
                description: 'バリデーションエラー',
                content: new OA\JsonContent(ref: '#/components/schemas/Error')
            ),
            new OA\Response(
                response: 403,
                description: '権限エラー',
                content: new OA\JsonContent(ref: '#/components/schemas/Error')
            ),
        ]
    )]
    public function store(StoreRequest $storeRequest): TagEdit
    {
        /** @var int $userId */
        $userId = Auth::id();

        $tag = $this->tagRepository->store([
            'name' => $storeRequest->string('name')->value(),
            'description' => $storeRequest->filled('description') ? $storeRequest->string('description')->value() : null,
            'created_by' => $userId,
            'last_modified_by' => $userId,
            'last_modified_at' => now(),
        ]);

        return new TagEdit($this->tagRepository->load($tag));
    }

    /**
     * タグを更新
     */
    #[OA\Post(
        path: '/api/v2/tags/{tag}',
        summary: 'タグの更新',
        description: '既存のタグを更新します',
        tags: ['Tags'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(
                name: 'tag',
                in: 'path',
                required: true,
                description: 'タグID',
                schema: new OA\Schema(type: 'integer')
            ),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['description'],
                properties: [
                    new OA\Property(property: 'description', type: 'string', example: '更新された説明', description: '説明'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: '更新成功',
                content: new OA\JsonContent(ref: '#/components/schemas/Tag')
            ),
            new OA\Response(
                response: 400,
                description: 'バリデーションエラー',
                content: new OA\JsonContent(ref: '#/components/schemas/Error')
            ),
            new OA\Response(
                response: 403,
                description: '権限エラー',
                content: new OA\JsonContent(ref: '#/components/schemas/Error')
            ),
            new OA\Response(
                response: 404,
                description: 'タグが見つかりません',
                content: new OA\JsonContent(ref: '#/components/schemas/Error')
            ),
        ]
    )]
    public function update(Tag $tag, UpdateRequest $updateRequest): TagEdit
    {
        $old = $tag->description;
        $user = Auth::user();
        if ($user === null) {
            abort(401);
        }

        if ($user->cannot('update', $tag)) {
            abort(403);
        }

        /** @var int $userId */
        $userId = Auth::id();

        $tag = $this->tagRepository->update($tag, [
            'description' => $updateRequest->filled('description') ? $updateRequest->string('description')->value() : null,
            'last_modified_by' => $userId,
            'last_modified_at' => now(),
        ]);
        event(new \App\Events\Tag\TagDescriptionUpdated($tag, $user, $old));

        return new TagEdit($this->tagRepository->load($tag));
    }
}
