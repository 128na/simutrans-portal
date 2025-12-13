<?php

declare(strict_types=1);

namespace App\Jobs\Attachments;

use App\Models\Attachment;
use App\Repositories\AttachmentRepository;
use App\Services\Image\ImageResizeService;
use App\Services\Image\ResizeFailedException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * 添付ファイルのサムネイルを生成するジョブ
 */
class JobGenerateThumbnail implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * リトライ回数
     */
    public int $tries = 3;

    /**
     * タイムアウト（秒）
     */
    public int $timeout = 180;

    /**
     * コンストラクタ
     */
    public function __construct(
        public Attachment $attachment,
    ) {}

    /**
     * ジョブを実行
     */
    public function handle(
        ImageResizeService $imageResizeService,
        AttachmentRepository $attachmentRepository,
    ): void {
        $filesystem = Storage::disk('public');

        // 画像でない場合はスキップ
        if ($this->attachment->type !== 'image') {
            Log::info('Skipped thumbnail generation for non-image attachment', [
                'attachment_id' => $this->attachment->id,
                'type' => $this->attachment->type,
            ]);

            return;
        }

        try {
            // 元画像のパスを取得
            $originalPath = $filesystem->path($this->attachment->path);
            if (! file_exists($originalPath)) {
                throw new ResizeFailedException('Original file not found');
            }

            // サムネイル生成
            $thumbnailWidth = (int) config('thumbnail.width', 300);
            $thumbnailFormat = (string) config('thumbnail.format', 'webp');
            $tempThumbnailPath = $imageResizeService->resize($originalPath, $thumbnailWidth, $thumbnailFormat);

            // 元画像と同じ幅の場合はサムネイル不要（そのまま返される）
            if ($tempThumbnailPath === $originalPath) {
                Log::info('Thumbnail generation skipped (original is smaller than target)', [
                    'attachment_id' => $this->attachment->id,
                ]);

                return;
            }

            // サムネイルを保存
            $thumbnailDirectory = (string) config('thumbnail.directory', 'thumbnails');
            $thumbnailExtension = $thumbnailFormat === 'jpeg' ? 'jpg' : $thumbnailFormat;
            $thumbnailFilename = pathinfo($this->attachment->path, PATHINFO_FILENAME).'_thumb.'.$thumbnailExtension;
            $thumbnailPath = $thumbnailDirectory.'/'.$thumbnailFilename;

            $filesystem->put(
                $thumbnailPath,
                file_get_contents($tempThumbnailPath)
            );

            // 一時ファイルを削除
            @unlink($tempThumbnailPath);

            // Attachmentモデルを更新
            $attachmentRepository->update($this->attachment, [
                'thumbnail_path' => $thumbnailPath,
            ]);

            Log::info('Thumbnail generated successfully', [
                'attachment_id' => $this->attachment->id,
                'thumbnail_path' => $thumbnailPath,
            ]);
        } catch (ResizeFailedException $resizeFailedException) {
            Log::error('Failed to generate thumbnail', [
                'attachment_id' => $this->attachment->id,
                'error' => $resizeFailedException->getMessage(),
            ]);

            throw $resizeFailedException;
        }
    }

    /**
     * ジョブ失敗時の処理
     */
    public function failed(?\Throwable $throwable): void
    {
        Log::error('JobGenerateThumbnail failed', [
            'attachment_id' => $this->attachment->id,
            'exception' => $throwable?->getMessage(),
        ]);
    }
}
