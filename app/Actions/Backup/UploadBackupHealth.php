<?php

declare(strict_types=1);

namespace App\Actions\Backup;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Throwable;

class UploadBackupHealth
{
    private const int STALE_SECONDS = 21600;

    public function recordSuccess(string $destination): void
    {
        Cache::forever($this->key($destination, 'last-complete'), now()->getTimestamp());
        Cache::forget($this->key($destination, 'alerted'));
    }

    /** @return list<string> */
    public function check(): array
    {
        $stale = [];
        foreach (['dropbox', 'local_backup'] as $destination) {
            $baselineKey = $this->key($destination, 'first-observed');
            Cache::add($baselineKey, now()->getTimestamp(), null);
            $lastComplete = (int) Cache::get($this->key($destination, 'last-complete'), Cache::get($baselineKey));
            if (now()->getTimestamp() - $lastComplete < self::STALE_SECONDS) {
                continue;
            }

            $stale[] = $destination;
            $alertKey = $this->key($destination, 'alerted');
            if (! Cache::add($alertKey, true, self::STALE_SECONDS)) {
                continue;
            }

            try {
                Log::error('Upload backup has not completed for at least six hours.', [
                    'destination' => $destination,
                    'last_complete_or_first_observed_at' => $lastComplete,
                ]);
            } catch (Throwable $exception) {
                Cache::forget($alertKey);
                throw $exception;
            }
        }

        return $stale;
    }

    private function key(string $destination, string $field): string
    {
        return 'backup-sync-uploads-health:'.$destination.':'.$field;
    }
}
