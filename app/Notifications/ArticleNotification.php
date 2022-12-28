<?php

namespace App\Notifications;

use App\Channels\TwitterChannel;
use App\Models\Article;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

abstract class ArticleNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array<string>
     */
    public function via($notifiable)
    {
        return [TwitterChannel::class];
    }

    /**
     * @param  Article  $article
     * @return string
     */
    public function toTwitter($article)
    {
        if ($article->user && $article->user->profile) {
            $url = route('articles.show', $article->slug);
            $now = now()->format('Y/m/d H:i');
            $name = $article->user->profile->has_twitter
                ? '@'.$article->user->profile->data->twitter
                : $article->user->name;
            $tags = collect(['Simutrans'])
                ->merge($article->categoryPaks->pluck('name'))
                ->map(fn ($name) => str_replace('.', '', "#$name")) // ドットはハッシュタグに使用できない
                ->implode(' ');

            $message = __(
                $this->getMessage(),
                ['title' => $article->title, 'url' => $url, 'name' => $name, 'at' => $now, 'tags' => $tags]
            );

            return $message;
        }
        throw new Exception('missing user or profile');
    }

    abstract protected function getMessage(): string;
}
