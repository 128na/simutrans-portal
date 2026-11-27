<?php

declare(strict_types=1);

namespace App\Console\Commands\Attachment;

use App\Enums\ImageFormat;
use App\Models\Attachment;
use App\Repositories\AttachmentRepository;
use App\Services\Image\ImageMetadataStripper;
use App\Services\Image\MetadataStripFailedException;
use Illuminate\Console\Command;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

/**
 * アップロード済み画像から撮影情報（EXIF・XMP・IPTC）を一括で取り除くコマンド
 *
 * 指定がなければ対象を数えるだけで何も書き換えない。
 * --execute を付けると、元ファイルを非公開の local ディスクへ退避してから書き換える。
 * 戻すときは storage/app/image-metadata-backup/ の中身を storage/app/public/ へ上書きコピーする。
 */
class StripImageMetadataCommand extends Command
{
    public const BACKUP_DIRECTORY = 'image-metadata-backup';

    protected $signature = 'attachment:strip-image-metadata
                            {--execute : 実際に書き換える（指定がなければ数えるだけ）}
                            {--id=* : 対象の添付ファイルID（複数指定可）}';

    protected $description = 'アップロード済み画像から撮影情報（EXIF・XMP・IPTC）を取り除きます';

    public function handle(
        ImageMetadataStripper $imageMetadataStripper,
        AttachmentRepository $attachmentRepository,
    ): int {
        $execute = (bool) $this->option('execute');
        $ids = (array) $this->option('id');

        $disk = Storage::disk('public');
        $images = 0;
        $missing = 0;
        $targets = [];
        $failedIds = [];

        $query = Attachment::query();
        if ($ids !== []) {
            $query->whereIn('id', $ids);
        }

        foreach ($query->lazyById() as $attachment) {
            if (! $disk->exists($attachment->path)) {
                $missing++;

                continue;
            }

            $format = ImageFormat::tryFrom((string) $disk->mimeType($attachment->path));
            if ($format === null) {
                continue;
            }

            $images++;

            try {
                $original = (string) $disk->get($attachment->path);
                $stripped = $imageMetadataStripper->strip($original);
                if ($stripped === $original) {
                    continue;
                }

                if ($execute) {
                    $this->rewrite($disk, $attachment->path, $original, $stripped);
                    $attachmentRepository->update($attachment, ['size' => strlen($stripped)]);
                }

                $targets[$format->value] = ($targets[$format->value] ?? 0) + 1;
            } catch (MetadataStripFailedException|RuntimeException $exception) {
                report($exception);
                $failedIds[] = $attachment->id;
                $this->warn(sprintf('#%d: %s', $attachment->id, $exception->getMessage()));
            }
        }

        $this->info(sprintf('画像: %d件 / 実体のないファイル: %d件', $images, $missing));
        $this->info(sprintf('%s: %d件', $execute ? '書き換えた画像' : '書き換え対象', array_sum($targets)));
        foreach ($targets as $format => $count) {
            $this->line(sprintf('  %s: %d件', $format, $count));
        }

        if (! $execute) {
            $this->comment('数えただけで書き換えていません。書き換えるには --execute を付けてください。');
        } elseif ($targets !== []) {
            $this->comment('元ファイルの退避先: '.Storage::disk('local')->path(self::BACKUP_DIRECTORY));
        }

        if ($failedIds !== []) {
            $this->error('処理できなかった添付ファイルID: '.implode(', ', $failedIds));

            return self::FAILURE;
        }

        return self::SUCCESS;
    }

    /**
     * 元ファイルを退避してから、一時ファイル経由で置き換える
     */
    private function rewrite(FilesystemAdapter $disk, string $path, string $original, string $stripped): void
    {
        $before = @getimagesizefromstring($original);
        $after = @getimagesizefromstring($stripped);
        if ($before !== false && ($after === false || $before[0] !== $after[0] || $before[1] !== $after[1])) {
            throw new RuntimeException('除去後の画像サイズが一致しません');
        }

        $backup = Storage::disk('local');
        $backupPath = self::BACKUP_DIRECTORY.'/'.$path;
        // 既存の退避ファイルは最初の原本なので上書きしない
        if (! $backup->exists($backupPath) && ! $backup->put($backupPath, $original)) {
            throw new RuntimeException('元ファイルを退避できません');
        }

        if ($backup->get($backupPath) !== $original) {
            throw new RuntimeException('退避ファイルが元ファイルと一致しません');
        }

        $temporaryPath = $path.'.stripping';
        if (! $disk->put($temporaryPath, $stripped) || ! $disk->move($temporaryPath, $path)) {
            $disk->delete($temporaryPath);

            throw new RuntimeException('ファイルを書き換えられません');
        }
    }
}
