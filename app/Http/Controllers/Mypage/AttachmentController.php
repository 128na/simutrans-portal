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
    public function destroy(Attachment $attachment, DeleteAttachment $deleteAttachment): Response
    {
        $this->authorize('update', $attachment);

        $deleteAttachment($attachment);

        return response('');
    }
}
