<?php

namespace App\Notifications;

class ArticlePublished extends ArticleNotification
{
    protected function getMessage():string
    {
        return "New Article Published. \":title\"\n:url\nby :name\nat :at\n:tags";
    }
}
