<?php

namespace App\Http\Controllers\Api\v2\Mypage;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Attachment\StoreRequest;
use App\Http\Resources\Api\Mypage\Attachments as AttachmentsResource;
use App\Models\Attachment;
use App\Repositories\AttachmentRepository;
use Illuminate\Support\Facades\Auth;

class AttachmentController extends Controller
{
    private AttachmentRepository $attachmentRepository;

    public function __construct(AttachmentRepository $attachmentRepository)
    {
        $this->attachmentRepository = $attachmentRepository;
    }

    public function index()
    {
        return new AttachmentsResource(
            $this->attachmentRepository->findAllByUser(Auth::user())
        );
    }

    public function store(StoreRequest $request)
    {
        abort_unless($request->hasFile('file'), 400);

        $this->attachmentRepository->createFromFile(Auth::user(), $request->file);

        return $this->index();
    }

    public function destroy(Attachment $attachment)
    {
        abort_unless($attachment->user_id === Auth::id(), 403);

        $this->attachmentRepository->delete($attachment);

        return $this->index();
    }
}
