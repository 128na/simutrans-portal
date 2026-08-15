<?php

declare(strict_types=1);

namespace App\Http\Controllers\Mypage;

use App\Actions\StoreAttachment\DeleteAttachment;
use App\Actions\StoreAttachment\Store;
use App\Http\Controllers\Controller;
use App\Http\Requests\Attachment\StoreRequest;
use App\Http\Resources\Mypage\AttachmentEdit;
use App\Models\Attachment;
use App\Services\Front\MetaOgpService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use OpenApi\Attributes as OA;

class AttachmentController extends Controller
{
    public function __construct(
        private readonly MetaOgpService $metaOgpService,
    ) {}

    public function index(): View
    {
        $user = $this->loggedinUser();

        return view('mypage.attachments', [
            'attachments' => AttachmentEdit::collection($user->myAttachments()->with('fileInfo', 'attachmentable')->get()),
            'meta' => $this->metaOgpService->mypageAttachments(),
        ]);
    }

    /**
     * 添付ファイルをアップロード
     */
    #[OA\Post(path: '/api/v2/attachments', description: '新しい添付ファイルをアップロードします', summary: '添付ファイルのアップロード', security: [['sanctum' => []]], requestBody: new OA\RequestBody(
        required: true,
        content: new OA\MediaType(
            mediaType: 'multipart/form-data',
            schema: new OA\Schema(
                required: ['file'],
                properties: [
                    new OA\Property(
                        property: 'file',
                        description: 'アップロードするファイル',
                        type: 'string',
                        format: 'binary'
                    ),
                ]
            )
        )
    ), tags: ['Attachments'], responses: [
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
    ])]
    public function store(StoreRequest $storeRequest, Store $store): AttachmentEdit
    {
        $this->authorize('store', Attachment::class);
        $user = $this->loggedinUser();

        /** @var UploadedFile|null */
        $file = $storeRequest->file('file');
        if (! $file) {
            abort(400);
        }

        $attachment = $store($user, $file);

        return new AttachmentEdit($attachment);
    }

    /**
     * 添付ファイルを削除
     */
    #[OA\Delete(path: '/api/v2/attachments/{attachment}', description: '指定された添付ファイルを削除します', summary: '添付ファイルの削除', security: [['sanctum' => []]], tags: ['Attachments'], parameters: [
        new OA\Parameter(
            name: 'attachment',
            description: '添付ファイルID',
            in: 'path',
            required: true,
            schema: new OA\Schema(type: 'integer')
        ),
    ], responses: [
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
    ])]
    public function destroy(Attachment $attachment, DeleteAttachment $deleteAttachment): Response
    {
        $this->authorize('update', $attachment);

        $deleteAttachment($attachment);

        return response('');
    }
}
