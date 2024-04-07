<?php

declare(strict_types=1);

namespace App\Services\Attachment;

use App\Enums\CroppableFormat;
use App\Models\Attachment;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class StoreService
{
    public function __construct(
        private readonly ImageCropper $imageCropper,
    ) {
    }

    /**
     * @param  array<int,int>  $crop
     */
    public function store(User $user, UploadedFile $uploadedFile, array $crop = []): Attachment
    {
        if ($this->isImage($uploadedFile)) {
            return $this->storeAsImage($user, $uploadedFile, $crop);
        }

        return $this->storeAsFile($user, $uploadedFile);
    }

    private function isImage(UploadedFile $uploadedFile): bool
    {
        return $this->getMime($uploadedFile) instanceof CroppableFormat;
    }

    private function getMime(UploadedFile $uploadedFile): ?CroppableFormat
    {
        return CroppableFormat::tryFrom($uploadedFile->getMimeType() ?? '');
    }

    /**
     * @param  array<int,int>  $crop
     */
    private function storeAsImage(User $user, UploadedFile $uploadedFile, array $crop = []): Attachment
    {
        try {
            $filepath = Storage::disk('public')->put('user/'.$user->id, $uploadedFile);
            if ($crop && $fullpath = realpath(storage_path('app/public/'.$filepath))) {
                logger('crop', $crop);
                try {
                    $this->imageCropper->crop($fullpath, ...$crop);
                } catch (ConvertFailedException) {
                    // need not report
                } catch (\Throwable $th) {
                    report($th);
                }
            }

            return Attachment::create([
                'user_id' => $user->id,
                'path' => $filepath,
                'original_name' => $uploadedFile->getClientOriginalName(),
            ]);
        } catch (ConvertFailedException $convertFailedException) {
            report($convertFailedException);

            return $this->storeAsFile($user, $uploadedFile);
        }
    }

    private function storeAsFile(User $user, UploadedFile $uploadedFile): Attachment
    {
        return Attachment::create([
            'user_id' => $user->id,
            'path' => Storage::disk('public')->put('user/'.$user->id, $uploadedFile),
            'original_name' => $uploadedFile->getClientOriginalName(),
        ]);
    }
}
