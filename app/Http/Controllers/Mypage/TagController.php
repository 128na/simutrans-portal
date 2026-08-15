<?php

declare(strict_types=1);

namespace App\Http\Controllers\Mypage;

use App\Events\Tag\TagDescriptionUpdated;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tag\StoreRequest;
use App\Http\Requests\Tag\UpdateRequest;
use App\Http\Resources\Mypage\TagEdit;
use App\Models\Tag;
use App\Repositories\TagRepository;
use App\Services\Front\MetaOgpService;
use Illuminate\Contracts\View\View;
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
    #[OA\Post(path: '/api/v2/tags', description: '新しいタグを作成します', summary: 'タグの作成', security: [['sanctum' => []]], requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ['name', 'description'],
            properties: [
                new OA\Property(property: 'name', description: 'タグ名', type: 'string', example: 'pak128.japan'),
                new OA\Property(property: 'description', description: '説明', type: 'string', example: 'pak128.japan用アドオン'),
            ]
        )
    ), tags: ['Tags'], responses: [
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
    ])]
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
    #[OA\Post(path: '/api/v2/tags/{tag}', description: '既存のタグを更新します', summary: 'タグの更新', security: [['sanctum' => []]], requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ['description'],
            properties: [
                new OA\Property(property: 'description', description: '説明', type: 'string', example: '更新された説明'),
            ]
        )
    ), tags: ['Tags'], parameters: [
        new OA\Parameter(
            name: 'tag',
            description: 'タグID',
            in: 'path',
            required: true,
            schema: new OA\Schema(type: 'integer')
        ),
    ], responses: [
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
    ])]
    public function update(Tag $tag, UpdateRequest $updateRequest): TagEdit
    {
        $old = $tag->description;
        $this->authorize('update', $tag);
        $user = $this->loggedinUser();

        /** @var int $userId */
        $userId = Auth::id();

        $tag = $this->tagRepository->update($tag, [
            'description' => $updateRequest->filled('description') ? $updateRequest->string('description')->value() : null,
            'last_modified_by' => $userId,
            'last_modified_at' => now(),
        ]);
        event(new TagDescriptionUpdated($tag, $user, $old));

        return new TagEdit($this->tagRepository->load($tag));
    }
}
