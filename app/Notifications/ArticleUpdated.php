<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Article;

class ArticleUpdated extends ArticleNotification
{
    /**
     * @param  Article  $article
     * @return string
     */
    public function toTwitter($article)
    {
        return $this->messageGenerator->buildUpdatedMessage($article);
    }
}
