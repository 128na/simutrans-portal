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

class AttachmentController extends Controller
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

        return view('mypage.attachments', [
            'attachments' => AttachmentEdit::collection($user->myAttachments()->with('fileInfo', 'attachmentable')->get()),
            'meta' => $this->metaOgpService->mypageAttachments(),
        ]);
    }

    /**
     * 添付ファイルをアップロード
     */
    #[OA\Post(
        path: '/api/v2/attachments',
        summary: '添付ファイルのアップロード',
        description: '新しい添付ファイルをアップロードします',
        tags: ['Attachments'],
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(
                    required: ['file'],
                    properties: [
                        new OA\Property(
                            property: 'file',
                            type: 'string',
                            format: 'binary',
                            description: 'アップロードするファイル'
                        ),
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'アップロード成功',
                content: new OA\JsonContent(ref: '#/components/schemas/Attachment')
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
    public function store(StoreRequest $storeRequest, Store $store): AttachmentEdit
    {
        $user = Auth::user();
        if ($user === null) {
            abort(401);
        }

        if ($user->cannot('store', Attachment::class)) {
            abort(403);
        }

        /** @var \Illuminate\Http\UploadedFile|null */
        $file = $storeRequest->file('file');
        if (! $file) {
            abort(400);
        }

        $attachment = $store($user, $file);
        try {
            $maxSizeMb = is_numeric(config('app.max_file_info_size'))
                ? (int) config('app.max_file_info_size')
                : 300;
            dispatch_sync(new \App\Jobs\Attachments\UpdateFileInfo($attachment, $maxSizeMb));
        } catch (Throwable $throwable) {
            report($throwable);
        }

        return new AttachmentEdit($attachment);
    }

    /**
     * 添付ファイルを削除
     */
    #[OA\Delete(
        path: '/api/v2/attachments/{attachment}',
        summary: '添付ファイルの削除',
        description: '指定された添付ファイルを削除します',
        tags: ['Attachments'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(
                name: 'attachment',
                in: 'path',
                required: true,
                description: '添付ファイルID',
                schema: new OA\Schema(type: 'integer')
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: '削除成功'
            ),
            new OA\Response(
                response: 403,
                description: '権限エラー',
                content: new OA\JsonContent(ref: '#/components/schemas/Error')
            ),
            new OA\Response(
                response: 404,
                description: '添付ファイルが見つかりません',
                content: new OA\JsonContent(ref: '#/components/schemas/Error')
            ),
        ]
    )]
    public function destroy(Attachment $attachment): \Illuminate\Http\Response
    {
        $user = Auth::user();
        if ($user === null) {
            abort(401);
        }

        if ($user->cannot('update', $attachment)) {
            abort(403);
        }

        $attachment->delete();

        return response('');
    }
}
