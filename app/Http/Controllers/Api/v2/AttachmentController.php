<?php

namespace App\Http\Controllers\Api\v2;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Attachment\StoreRequest;
use App\Http\Resources\api\Attachments as AttachmentsResource;
use App\Models\Article;
use App\Models\Attachment;
use App\Services\AttachmentService;
use Illuminate\Support\Facades\Auth;

class AttachmentController extends Controller
{
    /**
     * @var AttachmentService
     */
    private $attachment_service;

    public function __construct(AttachmentService $attachment_service)
    {
        $this->attachment_service = $attachment_service;
    }

    public function index(Article $article = null)
    {
        $attachments = $article
        ? $this->attachment_service->getUpdateArchiveAttachments(Auth::user(), $article)
        : $this->attachment_service->getCreateArchiveAttachments(Auth::user());

        return new AttachmentsResource($attachments);
    }

    public function store(StoreRequest $request, Article $article = null)
    {
        abort_unless($request->hasFile('file'), 400);

        $this->attachment_service->createFromFile(Auth::user(), $request);

        $attachments = $article
        ? $this->attachment_service->getUpdateArchiveAttachments(Auth::user(), $article)
        : $this->attachment_service->getCreateArchiveAttachments(Auth::user());

        return new AttachmentsResource($attachments);
    }

    public function destroy(Attachment $attachment, Article $article = null)
    {
        abort_unless($attachment->user_id === Auth::id(), 403);

        $this->attachment_service->destroy($attachment);

        $attachments = $article
        ? $this->attachment_service->getUpdateArchiveAttachments(Auth::user(), $article)
        : $this->attachment_service->getCreateArchiveAttachments(Auth::user());

        return new AttachmentsResource($attachments);
    }
}
