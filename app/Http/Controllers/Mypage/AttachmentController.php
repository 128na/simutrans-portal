<?php

declare(strict_types=1);

namespace App\Http\Controllers\Mypage;

use App\Actions\StoreAttachment\Store;
use App\Http\Controllers\Controller;
use App\Http\Requests\Attachment\StoreRequest;
use App\Http\Resources\Mypage\AttachmentEdit;
use App\Models\Attachment;
use App\Services\Front\MetaOgpService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OA;
use Throwable;

final class AttachmentController extends Controller
{
    public function __construct(
        private readonly MetaOgpService $metaOgpService,
    ) {}

    public function index(): View
    {
        $user = Auth::user();

        return view('mypage.attachments', [
            'attachments' => AttachmentEdit::collection($user->myAttachments()->with('fileInfo', 'attachmentable')->get()),
            'meta' => $this->metaOgpService->mypageAttachments(),
        ]);
    }

    /**
     * 添付ファイルをアップロード
     *
     * @OA\Post(
     *     path="/v2/attachments",
     *     summary="添付ファイルのアップロード",
     *     description="新しい添付ファイルをアップロードします",
     *     tags={"Attachments"},
     *     security={{"sanctum": {}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *
     *             @OA\Schema(
     *                 required={"file"},
     *
     *                 @OA\Property(
     *                     property="file",
     *                     type="string",
     *                     format="binary",
     *                     description="アップロードするファイル"
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="アップロード成功",
     *
     *         @OA\JsonContent(ref="#/components/schemas/Attachment")
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
    public function store(StoreRequest $storeRequest, Store $store): AttachmentEdit
    {
        $user = Auth::user();
        if ($user->cannot('store', Attachment::class)) {
            return abort(403);
        }

        /** @var \Illuminate\Http\UploadedFile|null */
        $file = $storeRequest->file('file');
        if (! $file) {
            abort(400);
        }

        $attachment = $store($user, $file, []);
        try {
            dispatch_sync(new \App\Jobs\Attachments\UpdateFileInfo($attachment));
        } catch (Throwable $throwable) {
            report($throwable);
        }

        return new AttachmentEdit($attachment);
    }

    /**
     * 添付ファイルを削除
     *
     * @OA\Delete(
     *     path="/v2/attachments/{attachment}",
     *     summary="添付ファイルの削除",
     *     description="指定された添付ファイルを削除します",
     *     tags={"Attachments"},
     *     security={{"sanctum": {}}},
     *
     *     @OA\Parameter(
     *         name="attachment",
     *         in="path",
     *         required=true,
     *         description="添付ファイルID",
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="削除成功"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="権限エラー",
     *
     *         @OA\JsonContent(ref="#/components/schemas/Error")
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="添付ファイルが見つかりません",
     *
     *         @OA\JsonContent(ref="#/components/schemas/Error")
     *     )
     * )
     */
    public function destroy(Attachment $attachment): \Illuminate\Http\Response
    {
        $user = Auth::user();
        if ($user->cannot('update', $attachment)) {
            return abort(403);
        }

        $attachment->delete();

        return response('');
    }
}
