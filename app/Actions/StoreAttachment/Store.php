<?php

declare(strict_types=1);

namespace App\Actions\StoreAttachment;

use App\Enums\ImageFormat;
use App\Jobs\Attachments\JobGenerateThumbnail;
use App\Jobs\Attachments\UpdateFileInfo;
use App\Models\Attachment;
use App\Models\User;
use App\Rules\SafeAttachment;
use App\Services\Image\ImageMetadataStripper;
use App\Services\Image\MetadataStripFailedException;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use RuntimeException;
use Throwable;

class Store
{
    public function __construct(
        private FilesystemAdapter $filesystemAdapter,
        private ImageMetadataStripper $imageMetadataStripper = new ImageMetadataStripper,
    ) {}

    public function __invoke(User $user, UploadedFile $uploadedFile): Attachment
    {
        Validator::make(['file' => $uploadedFile], ['file' => ['required', new SafeAttachment]])->validate();

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

    /**
     * 保存前に一時ファイルから撮影情報を取り除く。
     * 解析できない画像は、撮影情報が残ったまま公開しないよう拒否する。
     *
     * @throws ValidationException
     */
    private function stripImageMetadata(UploadedFile $uploadedFile): void
    {
        $path = $uploadedFile->getPathname();
        $data = file_get_contents($path);
        if ($data === false) {
            throw new RuntimeException('Failed to read uploaded image');
        }

        try {
            $stripped = $this->imageMetadataStripper->strip($data);
        } catch (MetadataStripFailedException $metadataStripFailedException) {
            report($metadataStripFailedException);

            throw ValidationException::withMessages([
                'file' => __('validation.image', ['attribute' => 'file']),
            ]);
        }

        if ($stripped === $data) {
            return;
        }

        if (file_put_contents($path, $stripped) !== strlen($stripped)) {
            throw new RuntimeException('Failed to write stripped image');
        }

        clearstatcache(true, $path);
    }

    private function storeAsImage(User $user, UploadedFile $uploadedFile): Attachment
    {
        $this->stripImageMetadata($uploadedFile);

        try {
            $filepath = $this->filesystemAdapter->putFileAs(
                'user/'.$user->id,
                $uploadedFile,
                Str::random(40).'.'.strtolower($uploadedFile->getClientOriginalExtension()),
            );

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
            'path' => $this->filesystemAdapter->putFileAs(
                'user/'.$user->id,
                $uploadedFile,
                Str::random(40).'.'.strtolower($uploadedFile->getClientOriginalExtension()),
            ),
            'original_name' => $uploadedFile->getClientOriginalName(),
            'size' => $uploadedFile->getSize(),
        ]);
    }
}
