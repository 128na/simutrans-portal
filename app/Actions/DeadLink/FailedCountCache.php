<?php

declare(strict_types=1);

namespace App\Actions\DeadLink;

use App\Models\Article;
use Illuminate\Support\Facades\Cache;

final class FailedCountCache
{
    public function get(Article $article): int
    {
        $item = Cache::get($this->getKey($article), 0);
        assert(is_int($item));

        return $item;
    }

    public function update(Article $article, int $count): void
    {
        Cache::put($this->getKey($article), $count);
    }

    public function clear(Article $article): void
    {
        Cache::forget($this->getKey($article));
    }

    private function getKey(Article $article): string
    {
        return sprintf('dead_link_check_count_%d', $article->id);
    }
}
