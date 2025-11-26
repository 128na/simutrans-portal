<?php

declare(strict_types=1);

namespace App\Console\Commands\Article;

use App\Models\Attachment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

final class ReparsePakFilesCommand extends Command
{
    protected $signature = 'article:reparse-pak-files
                            {--limit= : Limit the number of files to process}
                            {--dry-run : Simulate the operation without making changes}';

    protected $description = 'Reparse all pak files to extract metadata';

    public function handle(): int
    {
        $limit = $this->option('limit') ? (int) $this->option('limit') : null;
        $dryRun = (bool) $this->option('dry-run');

        if ($dryRun) {
            $this->warn('Running in dry-run mode. No changes will be made.');
        }

        // Get all attachments with pak files that have FileInfo
        $query = Attachment::whereHas('fileInfo')
            ->where(function ($q): void {
                $q->where('original_name', 'like', '%.pak')
                    ->orWhere('original_name', 'like', '%.PAK');
            });

        if ($limit !== null) {
            $query->limit($limit);
        }

        $total = $query->count();

        if ($total === 0) {
            $this->info('No pak files found to reparse.');

            return self::SUCCESS;
        }

        $this->info(sprintf('Found %s pak file(s) to reparse.', $total));

        if ($dryRun) {
            $this->info('Dry-run mode: would reparse these files.');

            return self::SUCCESS;
        }

        $this->output->progressStart($total);

        $successCount = 0;
        $errorCount = 0;

        foreach ($query->cursor() as $attachment) {
            try {
                dispatch_sync(new \App\Jobs\Attachments\UpdateFileInfo($attachment));
                $successCount++;
            } catch (\Throwable $e) {
                $errorCount++;
                Log::error('Failed to reparse pak file', [
                    'attachment_id' => $attachment->id,
                    'filename' => $attachment->original_name,
                    'error' => $e->getMessage(),
                ]);
            }

            $this->output->progressAdvance();
        }

        $this->output->progressFinish();

        $this->info(sprintf('Completed: %d succeeded, %d failed.', $successCount, $errorCount));

        if ($errorCount > 0) {
            $this->warn('Check the logs for details on failed files.');

            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
