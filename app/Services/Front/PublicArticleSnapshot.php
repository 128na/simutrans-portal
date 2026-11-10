<?php

declare(strict_types=1);

namespace App\Services\Front;

use Closure;
use Illuminate\Container\Attributes\Singleton;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use RuntimeException;

#[Singleton]
class PublicArticleSnapshot
{
    public const array PAKS = ['128-japan', '128', '64'];

    private const int VERSION = 1;

    private const int MAX_AGE_SECONDS = 86400;

    private bool $mutating = false;

    /**
     * @return array{latest: array<array-key, mixed>, announces: list<mixed>}
     */
    public function read(): array
    {
        $snapshot = $this->envelope();
        $generatedAt = $snapshot['generated_at'] ?? null;
        $latest = $snapshot['latest'] ?? null;
        $announces = $snapshot['announces'] ?? null;
        $now = now()->getTimestamp();

        if (($snapshot['version'] ?? null) !== self::VERSION
            || ! is_int($generatedAt) || $generatedAt > $now || $generatedAt <= $now - self::MAX_AGE_SECONDS
            || ! is_array($latest) || ! $this->validCards($announces, ['title', 'url', 'date'])) {
            return ['latest' => [], 'announces' => []];
        }

        foreach (self::PAKS as $pak) {
            if (! $this->validCards($latest[$pak] ?? null, ['title', 'url', 'thumbnail_url'])) {
                return ['latest' => [], 'announces' => []];
            }
        }

        return ['latest' => $latest, 'announces' => $announces];
    }

    /**
     * @param  Closure(): array{latest: array<array-key, mixed>, announces: list<mixed>}  $build
     */
    public function refresh(Closure $build): bool
    {
        return $this->locked('refresh', function () use ($build): bool {
            $revision = $this->locked('state', fn (): mixed => $this->envelope()['revision'] ?? null);
            $snapshot = $build();

            return $this->locked('state', function () use ($revision, $snapshot): bool {
                if (($this->envelope()['revision'] ?? null) !== $revision) {
                    return false;
                }

                $this->replace([
                    'version' => self::VERSION,
                    'revision' => (string) Str::uuid(),
                    'generated_at' => now()->getTimestamp(),
                    ...$snapshot,
                ]);

                return true;
            });
        }, nonBlocking: true) === true;
    }

    /**
     * @template T
     *
     * @param  Closure(): T  $mutation
     * @return T
     */
    public function mutate(Closure $mutation): mixed
    {
        if ($this->mutating) {
            return $mutation();
        }

        return $this->locked('state', function () use ($mutation): mixed {
            $this->mutating = true;
            try {
                return $mutation();
            } finally {
                $this->mutating = false;
            }
        });
    }

    public function invalidate(): void
    {
        if (! $this->mutating) {
            throw new RuntimeException('Snapshot invalidation requires the mutation lock.');
        }

        $this->replace(['revision' => (string) Str::uuid()]);
    }

    /**
     * @return array<string, mixed>
     */
    private function envelope(): array
    {
        $json = @file_get_contents($this->path());
        $value = is_string($json) ? json_decode($json, associative: true) : null;

        return is_array($value) ? $value : [];
    }

    /**
     * @param  list<string>  $stringFields
     *
     * @phpstan-assert-if-true list<mixed> $cards
     */
    private function validCards(mixed $cards, array $stringFields): bool
    {
        if (! is_array($cards) || ! array_is_list($cards)) {
            return false;
        }

        foreach ($cards as $card) {
            if (! is_array($card)) {
                return false;
            }
            foreach ($stringFields as $field) {
                if (! is_string($card[$field] ?? null)) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * @param  array<string, mixed>  $snapshot
     */
    private function replace(array $snapshot): void
    {
        $path = $this->path();
        $directory = dirname($path);
        $json = json_encode($snapshot, JSON_THROW_ON_ERROR);
        $temporary = tempnam($directory, 'snapshot-');
        if ($temporary === false) {
            throw new RuntimeException('Could not allocate a temporary snapshot file.');
        }

        try {
            if (dirname($temporary) !== realpath($directory)
                || file_put_contents($temporary, $json) !== strlen($json)
                || ! rename($temporary, $path)) {
                throw new RuntimeException('Could not replace the public article snapshot.');
            }
        } finally {
            if (is_file($temporary)) {
                unlink($temporary);
            }
        }
    }

    /**
     * @template T
     *
     * @param  Closure(): T  $callback
     * @return ($nonBlocking is true ? T|null : T)
     */
    private function locked(string $name, Closure $callback, bool $nonBlocking = false): mixed
    {
        $path = $this->path();
        File::ensureDirectoryExists(dirname($path));
        $handle = fopen($path.'.'.$name.'.lock', 'c');
        if ($handle === false) {
            throw new RuntimeException('Could not open the public article snapshot lock.');
        }

        try {
            if (! flock($handle, $nonBlocking ? LOCK_EX | LOCK_NB : LOCK_EX, $busy)) {
                if ($nonBlocking && $busy === 1) {
                    return null;
                }

                throw new RuntimeException('Could not acquire the public article snapshot lock.');
            }

            return $callback();
        } finally {
            fclose($handle);
        }
    }

    private function path(): string
    {
        return Config::string('public-article-snapshot.path', storage_path('app/public-article-snapshot.json'));
    }
}
