<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Tag;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

final class TagPolicy extends BasePolicy
{
    use HandlesAuthorization;

    public function store(User $user, Tag $tag): bool
    {
        return true;
    }

    public function update(User $user, Tag $tag): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $tag->editable;
    }

    public function toggleEditable(User $user): bool
    {
        return $user->isAdmin();
    }
}
