<?php

namespace App\Notifications;

use App\Channels\TwitterChannel;
use App\Models\Article;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ArticleUpdated extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [TwitterChannel::class];
    }

    /**
     * @param  Article  $article
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toTwitter($article)
    {
        $article->loadMissing('user.profile');
        $url = route('articles.show', $article->slug);
        $now = now()->format('Y/m/d H:i');
        $name = $article->user->profile->has_twitter
        ? '@' . $article->user->profile->data->twitter
        : $article->user->name;

        $message = __("\":title\" Updated.\n:url\nby :name\nat :at\n#simutrans",
            ['title' => $article->title, 'url' => $url, 'name' => $name, 'at' => $now]);
        return $message;
    }
}
