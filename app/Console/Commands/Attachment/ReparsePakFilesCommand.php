<?php

declare(strict_types=1);

namespace App\Console\Commands\Attachment;

use App\Jobs\Attachments\UpdateFileInfo;
use App\Models\Attachment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

final class ReparsePakFilesCommand extends Command
{
    protected $signature = 'attachment:reparse-pak-files
                            {id? : Specific attachment ID to reparse}
                            {--limit= : Limit the number of files to process}
                            {--max-size=100 : Maximum file size in MB (0 = unlimited)}
                            {--dry-run : Simulate the operation without making changes}';

    protected $description = 'Reparse all pak and zip files to extract metadata';

    public function handle(): int
    {
        $attachmentId = $this->argument('id') ? (int) $this->argument('id') : null;
        $limit = $this->option('limit') ? (int) $this->option('limit') : null;
        $maxSize = $this->option('max-size') ? (int) $this->option('max-size') : 100;
        $maxSizeMb = $maxSize > 0 ? $maxSize : null; // 0 = unlimited
        $dryRun = (bool) $this->option('dry-run');

        if ($dryRun) {
            $this->warn('Running in dry-run mode. No changes will be made.');
        }

        if ($maxSizeMb !== null) {
            $this->info(sprintf('Max file size: %d MB', $maxSizeMb));
        } else {
            $this->info('Max file size: unlimited');
        }

        // Build query
        $query = $this->buildQuery($attachmentId);

        if (! $query instanceof \Illuminate\Database\Eloquent\Builder) {
            return self::FAILURE; // Error already displayed
        }

        if ($limit !== null && $attachmentId === null) {
            $query->limit($limit);
        }

        $total = $query->count();

        if ($total === 0) {
            $this->info('No pak or zip files found to reparse.');

            return self::SUCCESS;
        }

        $this->info(sprintf('Found %s pak/zip file(s) to reparse.', $total));

        if ($dryRun) {
            $this->info('Dry-run mode: would reparse these files.');

            return self::SUCCESS;
        }

        return $this->processAttachments($query, $total, $maxSizeMb);
    }

    /**
     * Build query for attachments to reparse
     *
     * @return \Illuminate\Database\Eloquent\Builder<Attachment>|null
     */
    private function buildQuery(?int $attachmentId): ?\Illuminate\Database\Eloquent\Builder
    {
        if ($attachmentId !== null) {
            // Validate specific attachment
            $attachment = Attachment::with('fileInfo')->find($attachmentId);

            if ($attachment === null) {
                $this->error(sprintf('Attachment with ID %d not found.', $attachmentId));

                return null;
            }

            if ($attachment->fileInfo === null) {
                $this->error(sprintf('Attachment #%d does not have FileInfo.', $attachmentId));

                return null;
            }

            if (! $this->isPakOrZipFile($attachment->original_name)) {
                $this->error(sprintf(
                    'Attachment #%d (%s) is not a pak or zip file.',
                    $attachmentId,
                    $attachment->original_name
                ));

                return null;
            }

            $this->info(sprintf(
                'Processing attachment #%d: %s',
                $attachmentId,
                $attachment->original_name
            ));

            return Attachment::where('id', $attachmentId);
        }

        // Query all pak/zip files
        return Attachment::whereHas('fileInfo')
            ->where(function ($q): void {
                $q->where('original_name', 'like', '%.pak')
                    ->orWhere('original_name', 'like', '%.zip');
            })
            ->orderBy('size', 'asc');
    }

    /**
     * Process attachments and reparse them
     *
     * @param  \Illuminate\Database\Eloquent\Builder<Attachment>  $builder
     * @param  int|null  $maxSizeMb  Maximum file size in MB (null = unlimited)
     */
    private function processAttachments(\Illuminate\Database\Eloquent\Builder $builder, int $total, ?int $maxSizeMb): int
    {
        $this->output->progressStart($total);

        $successCount = 0;
        $errorCount = 0;
        $failedIds = [];

        // Eager load fileInfo to avoid N+1 queries
        foreach ($builder->with('fileInfo')->cursor() as $lazyCollection) {
            if ($this->reparseAttachment($lazyCollection, $maxSizeMb)) {
                $successCount++;
            } else {
                $errorCount++;
                $failedIds[] = $lazyCollection->id;
            }

            $this->output->progressAdvance();
        }

        $this->output->progressFinish();

        $this->info(sprintf('Completed: %d succeeded, %d failed.', $successCount, $errorCount));

        if ($errorCount > 0) {
            $this->error(sprintf('Failed attachment IDs: %s', implode(', ', $failedIds)));
            $this->warn('Check the logs for details on failed files.');

            return self::FAILURE;
        }

        return self::SUCCESS;
    }

    /**
     * Check if filename is a pak or zip file
     */
    private function isPakOrZipFile(string $filename): bool
    {
        $lower = strtolower($filename);

        return str_ends_with($lower, '.pak') || str_ends_with($lower, '.zip');
    }

    /**
     * Reparse a single attachment
     *
     * @param  int|null  $maxSizeMb  Maximum file size in MB (null = unlimited)
     */
    private function reparseAttachment(Attachment $attachment, ?int $maxSizeMb): bool
    {
        try {
            dispatch(new UpdateFileInfo($attachment, $maxSizeMb))->onQueue('parse');

            return true;
        } catch (\Throwable $throwable) {
            Log::error('Failed to reparse pak/zip file', [
                'attachment_id' => $attachment->id,
                'filename' => $attachment->original_name,
                'error' => $throwable->getMessage(),
                'trace' => $throwable->getTraceAsString(),
            ]);

            return false;
        }
    }
}
