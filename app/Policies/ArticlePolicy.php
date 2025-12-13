<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\ArticlePostType;
use App\Models\Article;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ArticlePolicy extends BasePolicy
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
        /** @var ArticlePostType $postType */
        $postType = $article->post_type;

        return $article->is_publish && $postType === ArticlePostType::AddonPost && $article->has_file && $article->file;
    }

    public function conversion(?User $user, Article $article): bool
    {
        /** @var ArticlePostType $postType */
        $postType = $article->post_type;

        return $article->is_publish && $postType === ArticlePostType::AddonIntroduction;
    }
}
