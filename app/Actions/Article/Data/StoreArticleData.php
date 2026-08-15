<?php

declare(strict_types=1);

namespace App\Actions\Article\Data;

final readonly class StoreArticleData
{
    public function __construct(
        public ArticleData $article,
        public bool $shouldNotify = false,
    ) {}

    /**
     * @param  array{should_notify?:bool,article:array{status:string,title:string,slug:string,post_type?:string,published_at?:string,contents:mixed,categories?:array<int>,tags?:array<int>,articles?:array<int>}}  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            article: ArticleData::fromArray($data['article']),
            shouldNotify: $data['should_notify'] ?? false,
        );
    }
}
