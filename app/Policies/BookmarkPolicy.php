<?php

namespace App\Policies;

use App\Models\User;
use App\Models\User\Bookmark;
use Illuminate\Auth\Access\HandlesAuthorization;

class BookmarkPolicy
{
    use HandlesAuthorization;

    public function show(User $user, Bookmark $bookmark)
    {
        return $user->id === $bookmark->user_id;
    }

    public function update(User $user, Bookmark $bookmark)
    {
        return $user->id === $bookmark->user_id;
    }
}
