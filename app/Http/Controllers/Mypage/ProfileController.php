<?php

declare(strict_types=1);

namespace App\Http\Controllers\Mypage;

use App\Actions\User\UpdateProfile;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\UpdateRequest;
use App\Http\Resources\Mypage\AttachmentEdit;
use App\Http\Resources\Mypage\ProfileEdit;
use App\Services\Front\MetaOgpService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class ProfileController extends Controller
{
    public function __construct(
        private readonly MetaOgpService $metaOgpService,
    ) {}

    public function index(): View
    {
        $user = $this->loggedinUser();

        return view('mypage.profile', [
            'user' => new ProfileEdit($user->load('profile')),
            'attachments' => AttachmentEdit::collection($user->myAttachments()->with('fileInfo')->get()),
            'meta' => $this->metaOgpService->mypageProfile(),
        ]);
    }

    /**
     * プロフィールを更新
     */
    #[OA\Post(path: '/api/v2/profile', description: 'ユーザーのプロフィール情報を更新します', summary: 'プロフィールの更新', security: [['sanctum' => []]], requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'nickname', description: '表示名', type: 'string', example: '新しいニックネーム'),
                new OA\Property(
                    property: 'profile',
                    properties: [
                        new OA\Property(property: 'data', description: 'プロフィール本文', type: 'string', example: 'プロフィール本文'),
                        new OA\Property(
                            property: 'attachments',
                            description: '添付ファイルID配列',
                            type: 'array',
                            items: new OA\Items(type: 'integer')
                        ),
                    ],
                    type: 'object'
                ),
            ]
        )
    ), tags: ['Profile'], responses: [
        new OA\Response(
            response: 200,
            description: '更新成功',
            content: new OA\JsonContent(type: 'object')
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
    public function update(UpdateRequest $updateRequest, UpdateProfile $updateProfile): JsonResponse
    {
        $user = $this->loggedinUser();

        $updateProfile($user, $updateRequest);

        return response()->json();
    }
}
