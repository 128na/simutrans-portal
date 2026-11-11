<?php

declare(strict_types=1);

namespace App\Http\Resources\Frontend;

use App\Enums\ArticlePostType;
use App\Models\Article;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * 公開側のアドオン一覧カード
 *
 * サムネイル・タイトル・pak・種類・作者・日付と、入手導線（ダウンロード／掲載先）だけを返す。
 * 入手導線は既存の CV 経路（articles.download / articles.conversion）を使う。
 */
class ArticleCard extends JsonResource
{
    /** @var Article */
    public $resource;

    /**
     * @param  Request  $request
     * @return array<string, mixed>
     */
    #[\Override]
    public function toArray($request): array
    {
        return [
            'id' => $this->resource->id,
            'title' => $this->resource->title,
            'url' => $this->resource->showUrl(),
            'post_type' => $this->resource->post_type,
            ...self::acquisitionUrls($this->resource->id, $this->resource->post_type),
            'thumbnail' => $this->resource->thumbnail_url,
            'categories' => $this->resource->categories->map(fn (Category $category): array => [
                'id' => $category->id,
                'type' => $category->type,
                'slug' => $category->slug,
            ]),
            'user' => [
                'id' => $this->resource->user->id,
                'name' => $this->resource->user->name,
                'url' => self::userUrl($this->resource->user->id, $this->resource->user->nickname),
            ],
            'published_at' => $this->resource->published_at?->format('Y/m/d H:i'),
            'modified_at' => $this->resource->modified_at?->format('Y/m/d H:i'),
        ];
    }

    /**
     * 入手の操作の URL（CV 経路）。新着カード（LatestArticleCards）と共通にして、一覧と同じ経路を使う
     *
     * @return array{download_url?: string, addon_page_url?: string}
     */
    public static function acquisitionUrls(int $articleId, ?ArticlePostType $postType): array
    {
        return match ($postType) {
            ArticlePostType::AddonPost => ['download_url' => route('articles.download', ['article' => $articleId])],
            ArticlePostType::AddonIntroduction => ['addon_page_url' => route('articles.conversion', ['article' => $articleId])],
            default => [],
        };
    }

    /**
     * 作者のページの URL。表示名があれば表示名、無ければ ID で開く
     */
    public static function userUrl(int $userId, ?string $nickname): string
    {
        return route('users.show', ['userIdOrNickname' => $nickname ?? $userId]);
    }
}
