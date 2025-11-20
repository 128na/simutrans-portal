<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Article;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

final class ArticlePolicy extends BasePolicy
{
    use HandlesAuthorization;

    public function store(User $user): bool
    {
        return true;
    }

    public function update(User $user, Article $article): bool
    {
        return $this->isSameUser($user, $article);
    }

    public function download(?User $user, Article $article): bool
    {
        return $article->is_publish && $article->is_addon_post && $article->has_file && $article->file;
    }
}
