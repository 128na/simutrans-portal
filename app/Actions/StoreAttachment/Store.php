<?php

declare(strict_types=1);

namespace App\Actions\StoreAttachment;

use App\Enums\ImageFormat;
use App\Models\Attachment;
use App\Models\User;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\UploadedFile;

final readonly class Store
{
    public function __construct(
        private FilesystemAdapter $filesystemAdapter,
    ) {}

    public function __invoke(User $user, UploadedFile $uploadedFile): Attachment
    {
        if ($this->isImage($uploadedFile)) {
            return $this->storeAsImage($user, $uploadedFile);
        }

        return $this->storeAsFile($user, $uploadedFile);
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
            dispatch(new \App\Jobs\Attachments\JobGenerateThumbnail($attachment));

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
