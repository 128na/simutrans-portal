<?php

declare(strict_types=1);

namespace App\Notifications;

class ArticlePublished extends ArticleNotification
{
    protected function getMessage(): string
    {
        return "新規投稿「:title」\n:url\nby :name\nat :at\n:tags";
    }
}
