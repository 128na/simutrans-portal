<?php

declare(strict_types=1);

namespace App\Actions\StoreAttachment;

use App\Enums\ImageFormat;
use App\Jobs\Attachments\JobGenerateThumbnail;
use App\Jobs\Attachments\UpdateFileInfo;
use App\Models\Attachment;
use App\Models\User;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\UploadedFile;
use Throwable;

class Store
{
    public function __construct(
        private FilesystemAdapter $filesystemAdapter,
    ) {}

    public function __invoke(User $user, UploadedFile $uploadedFile): Attachment
    {
        $attachment = $this->isImage($uploadedFile)
            ? $this->storeAsImage($user, $uploadedFile)
            : $this->storeAsFile($user, $uploadedFile);

        $this->dispatchFileInfoUpdate($attachment);

        return $attachment;
    }

    private function dispatchFileInfoUpdate(Attachment $attachment): void
    {
        try {
            $maxSizeMb = is_numeric(config('app.max_file_info_size'))
                ? (int) config('app.max_file_info_size')
                : 300;
            dispatch_sync(new UpdateFileInfo($attachment, $maxSizeMb));
        } catch (Throwable $throwable) {
            report($throwable);
        }
    }

    private function isImage(UploadedFile $uploadedFile): bool
    {
        return $this->getMime($uploadedFile) instanceof ImageFormat;
    }

    private function getMime(UploadedFile $uploadedFile): ?ImageFormat
    {
        return ImageFormat::tryFrom($uploadedFile->getMimeType() ?? '');
    }

    private function storeAsImage(User $user, UploadedFile $uploadedFile): Attachment
    {
        try {
            $filepath = $this->filesystemAdapter->put('user/'.$user->id, $uploadedFile);

            $attachment = Attachment::create([
                'user_id' => $user->id,
                'path' => $filepath,
                'original_name' => $uploadedFile->getClientOriginalName(),
                'size' => $uploadedFile->getSize(),
            ]);

            // サムネイル生成ジョブをディスパッチ
            dispatch(new JobGenerateThumbnail($attachment));

            return $attachment;
        } catch (ConvertFailedException $convertFailedException) {
            report($convertFailedException);

            return $this->storeAsFile($user, $uploadedFile);
        }
    }

    private function storeAsFile(User $user, UploadedFile $uploadedFile): Attachment
    {
        return Attachment::create([
            'user_id' => $user->id,
            'path' => $this->filesystemAdapter->put('user/'.$user->id, $uploadedFile),
            'original_name' => $uploadedFile->getClientOriginalName(),
            'size' => $uploadedFile->getSize(),
        ]);
    }
}
