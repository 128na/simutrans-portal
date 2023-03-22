<?php

declare(strict_types=1);

namespace App\Services\Attachment;

use App\Models\Attachment;
use App\Models\User;
use App\Services\Service;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StoreService extends Service
{
    public function __construct(
        private WebpConverter $webpConverter,
    ) {
    }

    public function store(User $user, UploadedFile $file): Attachment
    {
        if ($this->isImage($file)) {
            return $this->storeAsImage($user, $file);
        }

        return $this->storeAsFile($user, $file);
    }

    private function isImage(UploadedFile $file): bool
    {
        $mime = $file->getMimeType() ?? '';

        return Str::contains($mime, [
            'image',
            'gif',
            'png',
            'jpeg',
        ], false);
    }

    private function storeAsImage(User $user, UploadedFile $file): Attachment
    {
        try {
            $filepath = $this->webpConverter->create($user, $file);

            return Attachment::create([
                'user_id' => $user->id,
                'path' => $filepath,
                'original_name' => $file->getClientOriginalName(),
            ]);
        } catch (ConvertFailedException $e) {
            report($e);

            return $this->storeAsFile($user, $file);
        }
    }

    private function storeAsFile(User $user, UploadedFile $file): Attachment
    {
        return Attachment::create([
            'user_id' => $user->id,
            'path' => Storage::disk('public')->putFile('user/'.$user->id, $file),
            'original_name' => $file->getClientOriginalName(),
        ]);
    }
}
