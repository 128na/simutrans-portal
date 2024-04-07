<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Article;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

final class ArticlePolicy extends BasePolicy
{
    use HandlesAuthorization;

    public function update(User $user, Article $article): bool
    {
        return $this->isSameUser($user, $article);
    }
}
