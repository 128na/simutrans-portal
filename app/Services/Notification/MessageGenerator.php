<?php

declare(strict_types=1);

namespace App\Services\Notification;

use App\Models\Article;
use App\Models\User;
use App\Services\Service;
use Exception;

class MessageGenerator extends Service
{
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
        if ($article->user && $article->user->profile) {
            $url = route('articles.show', $article->slug);
            $now = now()->format('Y/m/d H:i');
            $name = $this->getDisaplayName($article->user);
            $tags = collect(['Simutrans'])
                ->merge($article->categoryPaks->pluck('name'))
                ->map(fn ($name) => str_replace('.', '', "#$name")) // ドットはハッシュタグに使用できない
                ->implode(' ');

            return ['title' => $article->title, 'url' => $url, 'name' => $name, 'at' => $now, 'tags' => $tags];
        }
        throw new Exception('missing user or profile');
    }

    private function getDisaplayName(User $user): string
    {
        if (! $user->profile?->has_twitter) {
            return $user->name;
        }
        $twitterName = $user->profile?->data->twitter ?? '';
        if (str_starts_with($twitterName, '@')) {
            return $twitterName;
        }

        return "@{$twitterName}";
    }
}
