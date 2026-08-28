<?php

declare(strict_types=1);

namespace App\Actions\Backup;

use Illuminate\Support\Facades\Cache;

class SyncUploadsDigestTracker
{
    private const int CACHE_TTL_DAYS = 2;

    public function recordTransferred(int $count): void
    {
        $this->increment('transferred', $count, now()->toDateString());
    }

    public function recordError(): void
    {
        $this->increment('errors', 1, now()->toDateString());
    }

    public function transferredOn(string $date): int
    {
        return (int) Cache::get($this->key('transferred', $date), 0);
    }

    public function errorsOn(string $date): int
    {
        return (int) Cache::get($this->key('errors', $date), 0);
    }

    private function increment(string $type, int $amount, string $date): void
    {
        if ($amount === 0) {
            return;
        }

        $key = $this->key($type, $date);
        Cache::add($key, 0, now()->addDays(self::CACHE_TTL_DAYS));
        Cache::increment($key, $amount);
    }

    private function key(string $type, string $date): string
    {
        return "backup-sync-uploads-digest:{$type}:{$date}";
    }
}
