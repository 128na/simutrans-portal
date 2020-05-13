<?php

namespace App\Http\Controllers\Api\v2\Mypage;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Attachment\StoreRequest;
use App\Http\Resources\Api\Mypage\Attachments as AttachmentsResource;
use App\Models\Attachment;
use App\Services\AttachmentService;
use Illuminate\Support\Facades\Auth;

class AttachmentController extends Controller
{
    private AttachmentService $attachment_service;

    public function __construct(AttachmentService $attachment_service)
    {
        $this->attachment_service = $attachment_service;
    }

    public function index()
    {
        return new AttachmentsResource(
            $this->attachment_service->getAttachments(Auth::user())
        );
    }

    public function store(StoreRequest $request)
    {
        abort_unless($request->hasFile('file'), 400);

        $this->attachment_service->createFromFile(Auth::user(), $request);

        return $this->index();
    }

    public function destroy(Attachment $attachment)
    {
        abort_unless($attachment->user_id === Auth::id(), 403);

        $this->attachment_service->destroy($attachment);

        return $this->index();
    }
}
