<?php

declare(strict_types=1);

namespace App\Http\Controllers\Mypage;

use App\Actions\StoreAttachment\Store;
use App\Http\Controllers\Controller;
use App\Http\Requests\Attachment\StoreRequest;
use App\Http\Resources\AttachmentEdit;
use App\Models\Attachment;
use App\Services\Front\MetaOgpService;
use Illuminate\Support\Facades\Auth;
use Throwable;

final class AttachmentController extends Controller
{
    public function __construct(
        private readonly MetaOgpService $metaOgpService,
    ) {}

    public function index(): \Illuminate\Contracts\View\View
    {
        $user = Auth::user();

        return view('v2.mypage.attachments', [
            'attachments' => AttachmentEdit::collection($user->myAttachments()->with('fileInfo', 'attachmentable')->get()),
            'meta' => $this->metaOgpService->attachments(),
        ]);
    }

    public function store(StoreRequest $storeRequest, Store $store): AttachmentEdit
    {
        if (Auth::user()->cannot('store', Attachment::class)) {
            return abort(403);
        }

        /** @var Illuminate\Http\UploadedFile|null */
        $file = $storeRequest->file('file');
        if (!$file) {
            abort(400);
        }

        $attachment = $store(Auth::user(), $file, []);
        try {
            dispatch_sync(new \App\Jobs\Attachments\UpdateFileInfo($attachment));
        } catch (Throwable $throwable) {
            report($throwable);
        }

        return new AttachmentEdit($attachment);
    }

    public function destroy(Attachment $attachment): \Illuminate\Http\Response
    {
        if (Auth::user()->cannot('update', $attachment)) {
            return abort(403);
        }

        $attachment->delete();

        return response('');
    }
}
