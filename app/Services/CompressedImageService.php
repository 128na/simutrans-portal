<?php
namespace App\Services;

use App\Models\Attachment;
use App\Models\CompressedImage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class CompressedImageService extends Service
{
    public function __construct(CompressedImage $model)
    {
        $this->model = $model;
    }

    public function getAttachmentsCursor()
    {
        return Attachment::select('path')->cursor();
    }

    public function compressIfNeeded(Attachment $attachment)
    {
        if (!Storage::disk('public')->exists($attachment->path)) {
            return 'missing';
        }
        if (!$attachment->is_png) {
            return 'not png';
        }
        if ($this->model->isCompressed($attachment->path)) {
            return 'already compressed';
        }
        $this->compress($attachment);
        return 'compressed';
    }

    private function compress(Attachment $attachment)
    {
        \Tinify\setKey(config('app.tinypng_api_key'));

        $path = $attachment->path;
        $backup_path = $path . '.bak';
        try {
            Storage::disk('public')->copy($path, $backup_path);
            $source = \Tinify\fromFile(Storage::disk('public')->path($path));
            $source->toFile(Storage::disk('public')->path($path));

            CompressedImage::create(['path' => $path]);
            Storage::disk('public')->delete($backup_path);
        } catch (\Throwable $e) {
            Storage::disk('public')->delete($path);
            Storage::disk('public')->move($backup_path, $path);
            throw $e;
        }
    }
}
