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
        private readonly AttachmentRepository $attachmentRepository,
        private readonly StoreService $storeService,
    ) {
    }

    public function index(): AttachmentsResource
    {
        return new AttachmentsResource(
            $this->attachmentRepository->findAllByUser($this->loggedinUser())
        );
    }

    public function store(StoreRequest $storeRequest): AttachmentsResource
    {
        $crop = [
            $storeRequest->integer('crop.top', 0),
            $storeRequest->integer('crop.bottom', 0),
            $storeRequest->integer('crop.left', 0),
            $storeRequest->integer('crop.right', 0),
        ];

        /** @var array<int,\Illuminate\Http\UploadedFile> */
        $files = $storeRequest->file('files', []);
        foreach ($files as $file) {
            $attachment = $this->storeService->store($this->loggedinUser(), $file, $crop);
            try {
                UpdateFileInfo::dispatchSync($attachment);
            } catch (Throwable $throwable) {
                report($throwable);
            }
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
