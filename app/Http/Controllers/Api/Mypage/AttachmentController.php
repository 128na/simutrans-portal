<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Mypage;

use App\Actions\StoreAttachment\Store;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Attachment\StoreRequest;
use App\Http\Resources\Api\Mypage\Attachment as MypageAttachment;
use App\Jobs\Attachments\UpdateFileInfo;
use App\Models\Attachment;
use App\Repositories\AttachmentRepository;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Throwable;

final class AttachmentController extends Controller
{
    public function __construct(
        private readonly AttachmentRepository $attachmentRepository,
    ) {}

    public function index(): AnonymousResourceCollection
    {
        return MypageAttachment::collection(
            $this->attachmentRepository->findAllByUser($this->loggedinUser())
        );
    }

    public function store(StoreRequest $storeRequest, Store $store): AnonymousResourceCollection
    {
        $crop = $storeRequest->has('crop') ? [
            $storeRequest->integer('crop.top', 0),
            $storeRequest->integer('crop.bottom', 0),
            $storeRequest->integer('crop.left', 0),
            $storeRequest->integer('crop.right', 0),
        ] : [];

        /** @var array<int,\Illuminate\Http\UploadedFile> */
        $files = $storeRequest->file('files', []);
        foreach ($files as $file) {
            $attachment = $store($this->loggedinUser(), $file, $crop);
            try {
                UpdateFileInfo::dispatchSync($attachment);
            } catch (Throwable $throwable) {
                report($throwable);
            }
        }

        return $this->index();
    }

    public function destroy(Attachment $attachment): AnonymousResourceCollection
    {
        abort_unless($attachment->user_id === Auth::id(), 403);

        $this->attachmentRepository->delete($attachment);

        return $this->index();
    }
}
