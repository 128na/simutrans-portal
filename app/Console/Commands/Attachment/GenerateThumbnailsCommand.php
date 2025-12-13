<?php

declare(strict_types=1);

namespace App\Console\Commands\Attachment;

use App\Models\Attachment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

/**
 * アップロード済み画像のサムネイルを一括生成するコマンド
 */
class GenerateThumbnailsCommand extends Command
{
    /**
     * コマンドのシグネチャ
     *
     * @var string
     */
    protected $signature = 'attachment:generate-thumbnails
                            {--force : サムネイル既存の場合も再生成する}
                            {--limit= : 処理する件数の上限}
                            {--sync : 同期実行（キューを使わない）}';

    /**
     * コマンドの説明
     *
     * @var string
     */
    protected $description = 'アップロード済み画像のサムネイルを一括生成します';

    /**
     * コマンドを実行
     */
    public function handle(): int
    {
        $force = $this->option('force');
        $limit = $this->option('limit') ? (int) $this->option('limit') : null;
        $sync = $this->option('sync');

        $this->info('サムネイル生成を開始します...');

        // 添付ファイルを取得
        $query = Attachment::query();

        // forceオプションがない場合は、サムネイルが未生成のもののみ
        if (! $force) {
            $query->whereNull('thumbnail_path');
        }

        // 件数制限
        if ($limit) {
            $query->limit($limit);
        }

        $attachments = $query->get();

        // 画像のみをフィルタリング（isImageアクセサを使用）
        /** @var \Illuminate\Support\Collection<int, \App\Models\Attachment> $imageAttachments */
        $imageAttachments = $attachments->filter(fn (\App\Models\Attachment $attachment) => $attachment->isImage);

        if ($imageAttachments->isEmpty()) {
            $this->info('処理対象の画像がありません。');

            return self::SUCCESS;
        }

        $this->info(sprintf('処理対象: %d件', $imageAttachments->count()));

        $progressBar = $this->output->createProgressBar($imageAttachments->count());
        $progressBar->start();

        $dispatched = 0;
        $totalOriginalSize = 0;
        $totalThumbnailSize = 0;
        $disk = Storage::disk('public');

        foreach ($imageAttachments as $imageAttachment) {
            if ($sync) {
                // 同期実行
                try {
                    // 元のサイズを記録
                    $originalSize = $disk->exists($imageAttachment->path) ? $disk->size($imageAttachment->path) : 0;

                    dispatch_sync(new \App\Jobs\Attachments\JobGenerateThumbnail($imageAttachment));
                    $dispatched++;

                    // サムネイルのサイズを記録
                    $imageAttachment->refresh();
                    if ($imageAttachment->thumbnail_path && $disk->exists($imageAttachment->thumbnail_path)) {
                        $thumbnailSize = $disk->size($imageAttachment->thumbnail_path);
                        $totalOriginalSize += $originalSize;
                        $totalThumbnailSize += $thumbnailSize;
                    }
                } catch (\Throwable $throwable) {
                    $this->newLine();
                    $this->error('Failed to generate thumbnail for attachment ID: '.$imageAttachment->id);
                    $this->error($throwable->getMessage());
                }
            } else {
                // キューに投入
                dispatch(new \App\Jobs\Attachments\JobGenerateThumbnail($imageAttachment));
                $dispatched++;
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        if ($sync) {
            $this->info(sprintf('✓ %d件のサムネイルを生成しました。', $dispatched));

            // サイズ削減情報を表示
            if ($totalOriginalSize > 0) {
                $reduction = $totalOriginalSize - $totalThumbnailSize;
                $reductionPercent = ($reduction / $totalOriginalSize) * 100;

                $this->newLine();
                $this->line('=== サイズ削減情報 ===');
                $this->line('元画像合計: '.$this->formatBytes($totalOriginalSize));
                $this->line('サムネイル合計: '.$this->formatBytes($totalThumbnailSize));
                $this->line('削減容量: '.$this->formatBytes($reduction).' ('.sprintf('%.1f%%', $reductionPercent).'削減)');
            }
        } else {
            $this->info(sprintf('✓ %d件のジョブをキューに投入しました。', $dispatched));
            $this->comment('キューワーカーが実行中であることを確認してください: php artisan queue:work');
            $this->comment('サイズ削減情報を確認するには --sync オプションを使用してください。');
        }

        return self::SUCCESS;
    }

    /**
     * バイト数を人間が読みやすい形式にフォーマット
     */
    private function formatBytes(int $bytes): string
    {
        if ($bytes >= 1073741824) {
            return sprintf('%.2f GB', $bytes / 1073741824);
        }

        if ($bytes >= 1048576) {
            return sprintf('%.2f MB', $bytes / 1048576);
        }

        if ($bytes >= 1024) {
            return sprintf('%.2f KB', $bytes / 1024);
        }

        return $bytes.' B';
    }
}
