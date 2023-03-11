<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Mypage;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Attachment\StoreRequest;
use App\Http\Resources\Api\Mypage\Attachments as AttachmentsResource;
use App\Jobs\Attachments\UpdateFileInfo;
use App\Models\Attachment;
use App\Repositories\AttachmentRepository;
use App\Services\Attachment\StoreService;
use Illuminate\Support\Facades\Auth;
use Throwable;

class AttachmentController extends Controller
{
    public function __construct(
        private AttachmentRepository $attachmentRepository,
        private StoreService $storeService,
    ) {
    }

    public function index(): AttachmentsResource
    {
        return new AttachmentsResource(
            $this->attachmentRepository->findAllByUser($this->loggedinUser())
        );
    }

    public function store(StoreRequest $request): AttachmentsResource
    {
        abort_unless($request->hasFile('file'), 400);

        $attachment = $this->storeService->store($this->loggedinUser(), $request->file);

        try {
            UpdateFileInfo::dispatchSync($attachment);
        } catch (Throwable $th) {
            report($th);
        }

        return $this->index();
    }

    public function destroy(Attachment $attachment): AttachmentsResource
    {
        abort_unless($attachment->user_id === Auth::id(), 403);

        $this->attachmentRepository->delete($attachment);

        return $this->index();
    }
}
