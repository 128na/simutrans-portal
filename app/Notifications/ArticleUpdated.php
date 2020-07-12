<?php

namespace App\Notifications;

class ArticleUpdated extends ArticleNotification
{
    protected function getMessage():string
    {
        return "\":title\" Updated.\n:url\nby :name\nat :at\n:tags";
    }
}
