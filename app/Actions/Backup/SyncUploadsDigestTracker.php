<?php

declare(strict_types=1);

namespace App\Actions\Backup;

use Illuminate\Support\Facades\Cache;

class SyncUploadsDigestTracker
{
    private const int CACHE_TTL_DAYS = 2;

    public function recordTransferred(int $count): void
    {
        $this->increment('transferred', $count);
    }

    public function recordError(): void
    {
        $this->increment('errors', 1);
    }

    public function transferredToday(): int
    {
        return (int) Cache::get($this->key('transferred'), 0);
    }

    public function errorsToday(): int
    {
        return (int) Cache::get($this->key('errors'), 0);
    }

    private function increment(string $type, int $amount): void
    {
        if ($amount === 0) {
            return;
        }

        $key = $this->key($type);
        Cache::add($key, 0, now()->addDays(self::CACHE_TTL_DAYS));
        Cache::increment($key, $amount);
    }

    private function key(string $type): string
    {
        return "backup-sync-uploads-digest:{$type}:".now()->toDateString();
    }
}
