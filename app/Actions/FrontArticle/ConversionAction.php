<?php

declare(strict_types=1);

namespace App\Actions\FrontArticle;

use App\Events\ArticleConversion;
use App\Models\Article;
use App\Models\User;

class ConversionAction
{
    public function __invoke(Article $article, ?User $user): void
    {
        // ログインしていて自身の記事ならカウントしない
        if (is_null($user) || $user->id !== $article->user_id) {
            event(new ArticleConversion($article));
        }
    }
}
