<?php

declare(strict_types=1);

namespace App\Http\Controllers\Mypage;

use App\Actions\User\UpdateProfile;
use App\Http\Requests\User\UpdateRequest;
use App\Http\Resources\Mypage\AttachmentEdit;
use App\Http\Resources\Mypage\ProfileEdit;
use App\Services\Front\MetaOgpService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OA;

final class ProfileController extends Controller
{
    public function __construct(
        private readonly MetaOgpService $metaOgpService,
    ) {}

    public function index(): View
    {
        $user = Auth::user();
        if ($user === null) {
            abort(401);
        }

        return view('mypage.profile', [
            'user' => new ProfileEdit($user->load('profile')),
            'attachments' => AttachmentEdit::collection($user->myAttachments()->with('fileInfo')->get()),
            'meta' => $this->metaOgpService->mypageProfile(),
        ]);
    }

    /**
     * プロフィールを更新
     *
     * @OA\Post(
     *     path="/api/v2/profile",
     *     summary="プロフィールの更新",
     *     description="ユーザーのプロフィール情報を更新します",
     *     tags={"Profile"},
     *     security={{"sanctum": {}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="nickname", type="string", example="新しいニックネーム", description="表示名"),
     *             @OA\Property(
     *                 property="profile",
     *                 type="object",
     *                 @OA\Property(property="data", type="string", example="プロフィール本文", description="プロフィール本文"),
     *                 @OA\Property(property="attachments", type="array", description="添付ファイルID配列", @OA\Items(type="integer"))
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="更新成功",
     *
     *         @OA\JsonContent(
     *             type="object"
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
    public function update(UpdateRequest $updateRequest, UpdateProfile $updateProfile): JsonResponse
    {
        $user = Auth::user();
        if ($user === null) {
            abort(401);
        }

        $updateProfile($user, $updateRequest);

        return response()->json();
    }
}
