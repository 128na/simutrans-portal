<?php

namespace App\Actions\SendSNS;

use App\Models\Article;
use Carbon\Carbon;

class GetArticleParam
{
    public function __construct(
        private readonly Carbon $carbon,
    ) {
    }

    /**
     * @return array<string,string>
     */
    public function __invoke(Article $article): array
    {
        $url = route('articles.show', ['userIdOrNickname' => $article->user->nickname ?? $article->user_id, 'articleSlug' => $article->slug]);
        $now = $this->carbon->format('Y/m/d H:i');
        $name = $article->user->name;
        $tags = collect(['simutrans'])
            ->merge($article->categoryPaks->pluck('slug'))
            ->map(fn ($slug): string => __('hash_tag.'.$slug))
            ->implode(' ');

        return ['title' => $article->title, 'url' => $url, 'name' => $name, 'at' => $now, 'tags' => $tags];
    }
}
