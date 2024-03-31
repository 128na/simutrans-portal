<?php

declare(strict_types=1);

namespace App\Services\Notification;

use App\Models\Article;
use App\Services\Service;
use Carbon\Carbon;
use Exception;

class MessageGenerator extends Service
{
    public function __construct(private readonly Carbon $carbon)
    {

    }

    public function buildPublishedMessage(Article $article): string
    {
        return __('notification.article.create', $this->getParams($article));
    }

    public function buildUpdatedMessage(Article $article): string
    {
        return __('notification.article.update', $this->getParams($article));
    }

    public function buildSimplePublishedMessage(Article $article): string
    {
        return __('notification.simple_article.create', $this->getParams($article));
    }

    public function buildSimpleUpdatedMessage(Article $article): string
    {
        return __('notification.simple_article.update', $this->getParams($article));
    }

    /**
     * @return array<string>
     */
    private function getParams(Article $article): array
    {
        if ($article->user) {
            $url = route('articles.show', ['userIdOrNickname' => $article->user->nickname ?? $article->user_id, 'articleSlug' => $article->slug]);
            $now = $this->carbon->format('Y/m/d H:i');
            $name = $article->user->name;
            $tags = collect(['simutrans'])
                ->merge($article->categoryPaks->pluck('slug'))
                ->map(fn ($slug): string => __('hash_tag.'.$slug))
                ->implode(' ');

            return ['title' => $article->title, 'url' => $url, 'name' => $name, 'at' => $now, 'tags' => $tags];
        }

        throw new Exception('missing user');
    }
}
