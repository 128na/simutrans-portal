<?php

namespace App\Policies;

use App\Models\Tag;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TagPolicy extends BasePolicy
{
    use HandlesAuthorization;

    public function update(User $user, Tag $tag)
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $tag->editable;
    }

    public function toggleEditable(User $user)
    {
        return $user->isAdmin();
    }
}
