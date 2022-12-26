<?php

namespace App\Policies;

use App\Models\Article;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ArticlePolicy extends BasePolicy
{
    use HandlesAuthorization;

    public function update(User $user, Article $article): bool
    {
        return $this->isSameUser($user, $article);
    }
}
