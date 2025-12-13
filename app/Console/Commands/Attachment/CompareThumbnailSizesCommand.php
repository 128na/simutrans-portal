<?php

declare(strict_types=1);

namespace App\Console\Commands\Attachment;

use App\Models\Attachment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CompareThumbnailSizesCommand extends Command
{
    protected $signature = 'attachment:compare-thumbnail-sizes
                            {--limit= : 比較する件数の上限}';

    protected $description = 'サムネイルと元画像のファイルサイズを比較';

    public function handle(): int
    {
        $query = Attachment::query()
            ->whereNotNull('thumbnail_path');

        if ($limit = $this->option('limit')) {
            $query->limit((int) $limit);
        }

        $attachments = $query->get()->filter(fn($attachment) => $attachment->isImage);

        if ($attachments->isEmpty()) {
            $this->info('サムネイルが存在する画像がありません。');

            return self::SUCCESS;
        }

        $disk = Storage::disk('public');
        $totalOriginalSize = 0;
        $totalThumbnailSize = 0;
        $count = 0;

        $this->info("対象件数: {$attachments->count()}件");
        $this->newLine();

        $bar = $this->output->createProgressBar($attachments->count());
        $bar->start();

        $data = [];

        foreach ($attachments as $attachment) {
            if (! $attachment->path || ! $disk->exists($attachment->path)) {
                $bar->advance();

                continue;
            }

            if (! $attachment->thumbnail_path || ! $disk->exists($attachment->thumbnail_path)) {
                $bar->advance();

                continue;
            }

            $originalSize = $disk->size($attachment->path);
            $thumbnailSize = $disk->size($attachment->thumbnail_path);
            $reduction = (1 - ($thumbnailSize / $originalSize)) * 100;

            $totalOriginalSize += $originalSize;
            $totalThumbnailSize += $thumbnailSize;
            $count++;

            $data[] = [
                'ID' => $attachment->id,
                '元画像' => $this->formatBytes($originalSize),
                'サムネイル' => $this->formatBytes($thumbnailSize),
                '削減率' => sprintf('%.1f%%', $reduction),
            ];

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        if ($count === 0) {
            $this->warn('比較可能なファイルがありませんでした。');

            return self::SUCCESS;
        }

        // 最初の10件を表示
        $displayData = array_slice($data, 0, 10);
        $this->table(
            ['ID', '元画像', 'サムネイル', '削減率'],
            $displayData
        );

        if (count($data) > 10) {
            $this->comment('（最初の10件のみ表示）');
            $this->newLine();
        }

        // 統計情報
        $totalReduction = (1 - ($totalThumbnailSize / $totalOriginalSize)) * 100;
        $avgReduction = $totalReduction;

        $this->info('=== 統計情報 ===');
        $this->line("比較件数: {$count}件");
        $this->line('元画像合計: ' . $this->formatBytes($totalOriginalSize));
        $this->line('サムネイル合計: ' . $this->formatBytes($totalThumbnailSize));
        $this->line('削減容量: ' . $this->formatBytes($totalOriginalSize - $totalThumbnailSize));
        $this->line(sprintf('平均削減率: %.1f%%', $avgReduction));

        return self::SUCCESS;
    }

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

        return $bytes . ' B';
    }
}
