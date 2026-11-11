<?php

declare(strict_types=1);

namespace App\Services\Front;

use App\Enums\ArticlePostType;
use App\Enums\CategoryType;
use Closure;
use DateTimeImmutable;
use Illuminate\Container\Attributes\Singleton;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * @phpstan-type PakAddonCounts array<array-key, array<string, array{total: int, addon_post: int}>>
 * @phpstan-type Snapshot array{latest: array<array-key, mixed>, announces: list<mixed>, pak_addon_counts: PakAddonCounts|null}
 */
#[Singleton]
class PublicArticleSnapshot
{
    public const array PAKS = ['128-japan', '128', '64'];

    /**
     * 2: pak × 種類の公開記事数（pak_addon_counts）を追加
     *
     * 新着カードの paks（記事の pak の slug）は任意項目として 2 のまま追加した。
     * 無い・壊れているカードは paks を空にして、pak のタグだけを出さない
     *
     * 一覧のカードと同じ表示に使う post_type・categories・user も任意項目として 2 のまま追加した（#679）。
     * 旧コードへ戻しても追加項目を無視して読めるので、版を上げて新着を消さない。
     * 無い・壊れているカードは入手の操作・タグ・作者だけを出さない
     */
    private const int VERSION = 2;

    /**
     * 旧形式（1）は新着・お知らせだけ読み、件数は無しとして扱う
     */
    private const array READABLE_VERSIONS = [1, 2];

    private const int MAX_AGE_SECONDS = 86400;

    private bool $mutating = false;

    /**
     * @return Snapshot
     */
    public function read(): array
    {
        $snapshot = $this->envelope();
        $generatedAt = $snapshot['generated_at'] ?? null;
        $now = now()->getTimestamp();

        if (! in_array($snapshot['version'] ?? null, self::READABLE_VERSIONS, true)
            || ! is_int($generatedAt) || $generatedAt > $now || $generatedAt <= $now - self::MAX_AGE_SECONDS) {
            return ['latest' => [], 'announces' => [], 'pak_addon_counts' => null];
        }

        return [...$this->articles($snapshot), 'pak_addon_counts' => $this->pakAddonCounts($snapshot['pak_addon_counts'] ?? null)];
    }

    /**
     * pak 別一覧の種類の切り替えに出す件数（種類の slug => 件数）。件数が無いときは null
     *
     * @return array<string, int>|null
     */
    public function addonCounts(string $pak, bool $addonPostOnly): ?array
    {
        $counts = $this->read()['pak_addon_counts'][$pak] ?? null;

        return $counts === null
            ? null
            : array_map(fn (array $count): int => $addonPostOnly ? $count['addon_post'] : $count['total'], $counts);
    }

    /**
     * @param  Closure(): Snapshot  $build
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
     * @param  array<string, mixed>  $snapshot
     * @return array{latest: array<array-key, mixed>, announces: list<mixed>}
     */
    private function articles(array $snapshot): array
    {
        $latest = $snapshot['latest'] ?? null;
        $announces = $snapshot['announces'] ?? null;

        if (! is_array($latest) || ! $this->validCards($announces, ['title', 'url', 'date'])) {
            return ['latest' => [], 'announces' => []];
        }

        $valid = [];
        foreach (self::PAKS as $pak) {
            $cards = $latest[$pak] ?? null;
            if (! $this->validCards($cards, ['title', 'url', 'thumbnail_url'])) {
                return ['latest' => [], 'announces' => []];
            }
            $valid[$pak] = [];
            foreach ($cards as $card) {
                if (! is_int($card['id'] ?? null)
                    || ! array_key_exists('published_at', $card) || ! array_key_exists('modified_at', $card)
                    || ! $this->validDate($card['published_at'] ?? null)
                    || ! $this->validDate($card['modified_at'] ?? null)) {
                    return ['latest' => [], 'announces' => []];
                }
                $valid[$pak][] = [
                    ...$card,
                    'paks' => $this->paks($card['paks'] ?? null),
                    'post_type' => $this->postType($card['post_type'] ?? null),
                    'categories' => $this->categories($card['categories'] ?? null),
                    'user' => $this->user($card['user'] ?? null),
                ];
            }
        }

        return ['latest' => $valid, 'announces' => $announces];
    }

    /**
     * カードの pak の slug。旧形式で無いとき・壊れているときは空にする
     *
     * @return list<string>
     */
    private function paks(mixed $paks): array
    {
        if (! is_array($paks) || ! array_is_list($paks)) {
            return [];
        }
        foreach ($paks as $pak) {
            if (! is_string($pak)) {
                return [];
            }
        }

        return $paks;
    }

    /**
     * カードの投稿形式。アドオン投稿・アドオン紹介以外（旧形式で無いときを含む）は null にして入手の操作を出さない
     */
    private function postType(mixed $postType): ?string
    {
        return in_array($postType, [ArticlePostType::AddonPost->value, ArticlePostType::AddonIntroduction->value], true)
            ? $postType
            : null;
    }

    /**
     * カードの pak・種類のカテゴリ。旧形式で無いとき・壊れているときは空にする
     *
     * @return list<array{id: int, type: string, slug: string}>
     */
    private function categories(mixed $categories): array
    {
        if (! is_array($categories) || ! array_is_list($categories)) {
            return [];
        }

        $valid = [];
        foreach ($categories as $category) {
            if (! is_array($category) || ! is_int($category['id'] ?? null) || ! is_string($category['slug'] ?? null)
                || ! in_array($category['type'] ?? null, [CategoryType::Pak->value, CategoryType::Addon->value], true)) {
                return [];
            }
            $valid[] = ['id' => $category['id'], 'type' => $category['type'], 'slug' => $category['slug']];
        }

        return $valid;
    }

    /**
     * カードの作者。旧形式で無いとき・壊れているときは null にして作者だけを出さない
     *
     * @return array{name: string, nickname: string|null}|null
     */
    private function user(mixed $user): ?array
    {
        if (! is_array($user) || ! is_string($user['name'] ?? null)
            || ! array_key_exists('nickname', $user) || ! ($user['nickname'] === null || is_string($user['nickname']))) {
            return null;
        }

        return ['name' => $user['name'], 'nickname' => $user['nickname']];
    }

    /**
     * 件数は新着・お知らせとは別に検証し、欠落・破損時は件数だけを null にする（旧形式も null）
     *
     * @return PakAddonCounts|null
     */
    private function pakAddonCounts(mixed $counts): ?array
    {
        if (! is_array($counts)) {
            return null;
        }

        $valid = [];
        foreach (self::PAKS as $pak) {
            $addons = $counts[$pak] ?? null;
            if (! is_array($addons)) {
                return null;
            }
            $valid[$pak] = [];
            foreach ($addons as $addon => $count) {
                if (! is_string($addon) || ! is_array($count)
                    || ! is_int($count['total'] ?? null) || ! is_int($count['addon_post'] ?? null)
                    || $count['addon_post'] < 0 || $count['addon_post'] > $count['total']) {
                    return null;
                }
                $valid[$pak][$addon] = ['total' => $count['total'], 'addon_post' => $count['addon_post']];
            }
        }

        return $valid;
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

    private function validDate(mixed $date): bool
    {
        if ($date === null) {
            return true;
        }
        if (! is_string($date) || str_contains($date, "\0")) {
            return false;
        }

        $parsed = DateTimeImmutable::createFromFormat(DATE_ATOM, $date);

        return $parsed !== false && $parsed->format(DATE_ATOM) === $date;
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
