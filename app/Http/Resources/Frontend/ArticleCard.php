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
            'download_url' => $this->when($this->resource->post_type === ArticlePostType::AddonPost, fn (): string => route('articles.download', ['article' => $this->resource->id])),
            'addon_page_url' => $this->when($this->resource->post_type === ArticlePostType::AddonIntroduction, fn (): string => route('articles.conversion', ['article' => $this->resource->id])),
            'thumbnail' => $this->resource->thumbnail_url,
            'categories' => $this->resource->categories->map(fn (Category $category): array => [
                'id' => $category->id,
                'type' => $category->type,
                'slug' => $category->slug,
            ]),
            'user' => [
                'id' => $this->resource->user->id,
                'name' => $this->resource->user->name,
                'url' => route('users.show', ['userIdOrNickname' => $this->resource->user->nickname ?? $this->resource->user->id]),
            ],
            'published_at' => $this->resource->published_at?->format('Y/m/d H:i'),
            'modified_at' => $this->resource->modified_at?->format('Y/m/d H:i'),
        ];
    }
}
