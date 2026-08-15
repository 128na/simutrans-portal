<?php

declare(strict_types=1);

namespace App\Actions\Article\Data;

/**
 * StoreRequest/UpdateRequest の `article` サブ配列を表すDTO。
 * `post_type` はUpdateRequestではバリデーション対象外（更新時は変更不可のため未送信）
 * であるため、更新フローでは常にnullになりうる点に注意。
 */
final readonly class ArticleData
{
    /**
     * @param  array<int>  $categories
     * @param  array<int>  $tags
     * @param  array<int>  $articles
     */
    public function __construct(
        public string $status,
        public string $title,
        public string $slug,
        public ?string $postType,
        public ?string $publishedAt,
        public mixed $contents,
        public array $categories = [],
        public array $tags = [],
        public array $articles = [],
    ) {}

    /**
     * @param  array{status:string,title:string,slug:string,post_type?:string,published_at?:string,contents:mixed,categories?:array<int>,tags?:array<int>,articles?:array<int>}  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            status: $data['status'],
            title: $data['title'],
            slug: $data['slug'],
            postType: $data['post_type'] ?? null,
            publishedAt: $data['published_at'] ?? null,
            contents: $data['contents'] ?? null,
            categories: $data['categories'] ?? [],
            tags: $data['tags'] ?? [],
            articles: $data['articles'] ?? [],
        );
    }
}
