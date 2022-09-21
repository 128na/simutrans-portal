<?php

namespace App\Services\Front;

use App\Models\Article;
use App\Services\Service;

class MetaOgpService extends Service
{
    public function __construct()
    {
    }

    public function forShow(Article $article): array
    {
        return [
            'title' => $article->title.' - '.config('app.name'),
            'description' => $this->trimDescription($article->contents->getDescription()),
            'image' => $article->has_thumbnail ? $article->thumbnail_url : null,
            'canonical' => route('articles.show', $article->slug),
            'card_type' => 'summary_large_image',
        ];
    }

    private function trimDescription($str): string
    {
        $str = str_replace(["\n", "\r"], '', $str);
        $str = mb_strimwidth($str, 0, 200, '…');

        return $str;
    }
}
