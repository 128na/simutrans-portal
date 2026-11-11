<?php

declare(strict_types=1);

namespace App\Services\Front;

use App\Enums\ArticlePostType;
use Illuminate\Support\Facades\Date;

/**
 * 保存済みの新着カード（PublicArticleSnapshot）を、一覧のカード（Http\Resources\Frontend\ArticleCard）と同じ形にする。
 *
 * 表示時に DB を使わないよう、URL は保存した ID・slug から組み立てる。入手の操作は一覧と同じ CV 経路
 * （articles.download / articles.conversion）を使う。is_updated は新着の「新規／更新」の見出しに使う
 */
class LatestArticleCards
{
    /**
     * 公開日より後の日に、公開から 60 秒より後に更新されたものを「更新」とする（公開と同じ日の更新や、公開処理で数秒ずれただけのものは「新規」）
     */
    private const int UPDATED_THRESHOLD_SECONDS = 60;

    /**
     * @param  iterable<array<string, mixed>>  $cards
     * @return list<array<string, mixed>>
     */
    public function present(iterable $cards): array
    {
        $presented = [];
        foreach ($cards as $card) {
            $presented[] = $this->card($card);
        }

        return $presented;
    }

    /**
     * @param  array<string, mixed>  $card
     * @return array<string, mixed>
     */
    private function card(array $card): array
    {
        /** @var int $id */
        $id = $card['id'];
        $publishedAt = is_string($card['published_at'] ?? null) ? $card['published_at'] : null;
        $modifiedAt = is_string($card['modified_at'] ?? null) ? $card['modified_at'] : null;
        $postType = $card['post_type'] ?? null;

        return [
            'id' => $id,
            'title' => $card['title'],
            'url' => $card['url'],
            'post_type' => $postType,
            ...match ($postType) {
                ArticlePostType::AddonPost->value => ['download_url' => route('articles.download', ['article' => $id])],
                ArticlePostType::AddonIntroduction->value => ['addon_page_url' => route('articles.conversion', ['article' => $id])],
                default => [],
            },
            'thumbnail' => $card['thumbnail_url'],
            'categories' => $card['categories'] ?? [],
            'user' => $this->user($card),
            'published_at' => $this->format($publishedAt),
            'modified_at' => $this->format($modifiedAt),
            'is_updated' => $this->isUpdated($publishedAt, $modifiedAt),
        ];
    }

    /**
     * @param  array<string, mixed>  $card
     * @return array{id: int, name: string, url: string}|null
     */
    private function user(array $card): ?array
    {
        $user = $card['user'] ?? null;
        $userId = $card['user_id'] ?? null;
        if (! is_array($user) || ! is_string($user['name'] ?? null) || ! is_int($userId)) {
            return null;
        }
        $nickname = $user['nickname'] ?? null;

        return [
            'id' => $userId,
            'name' => $user['name'],
            'url' => route('users.show', ['userIdOrNickname' => is_string($nickname) ? $nickname : $userId]),
        ];
    }

    /**
     * 一覧のカードと同じ「Y/m/d H:i」にする
     */
    private function format(?string $date): ?string
    {
        return $date === null ? null : Date::parse($date)->format('Y/m/d H:i');
    }

    private function isUpdated(?string $publishedAt, ?string $modifiedAt): bool
    {
        if ($modifiedAt === null) {
            return false;
        }
        if ($publishedAt === null) {
            return true;
        }

        return substr($modifiedAt, 0, 10) > substr($publishedAt, 0, 10)
            && Date::parse($modifiedAt)->getTimestamp() - Date::parse($publishedAt)->getTimestamp() > self::UPDATED_THRESHOLD_SECONDS;
    }
}
