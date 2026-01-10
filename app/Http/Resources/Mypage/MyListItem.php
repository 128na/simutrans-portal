<?php

declare(strict_types=1);

namespace App\Http\Resources\Mypage;

use App\Constants\DefaultThumbnail;
use App\Models\Article;
use App\Models\MyListItem as ModelsMyListItem;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class MyListItem extends JsonResource
{
    /** @var ModelsMyListItem */
    public $resource;

    /**
     * @param  \Illuminate\Http\Request  $request
     * @return array<string, mixed>
     */
    #[\Override]
    public function toArray($request)
    {
        $article = $this->resource->article;

        return [
            'id' => $this->resource->id,
            'note' => $this->resource->note,
            'position' => $this->resource->position,
            'created_at' => $this->resource->created_at?->format('Y/m/d H:i'),
            'article' => $this->resource->article->status === \App\Enums\ArticleStatus::Publish
                ? $this->publicArticle($article)
                : $this->privateArticle($article),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function publicArticle(Article $article): array
    {
        return [
            'id' => $article->id,
            'title' => $article->title,
            'url' => route('articles.show', [
                'userIdOrNickname' => $article->user->nickname ?? $article->user_id,
                'articleSlug' => $article->slug,
            ]),
            'thumbnail' => $article->thumbnail_url ?? Storage::url(DefaultThumbnail::NO_THUMBNAIL),
            'user' => [
                'name' => $article->user->name,
                'avatar' => $article->user->profile->avatar->thumbnail ?? Storage::url(DefaultThumbnail::NO_AVATAR),
            ],
            'published_at' => $article->published_at?->format('Y/m/d H:i'),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function privateArticle(Article $article): array
    {
        return [
            'id' => $article->id,
            'title' => '非公開記事',
        ];
    }
}
