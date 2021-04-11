<?php

namespace App\Policies;

use App\Models\User;
use App\Models\User\Bookmark;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

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

    public function destroy(User $user, Bookmark $bookmark)
    {
        if ($user->id !== $bookmark->user_id) {
            return Response::deny();
        }
        if ($user->bookmarks()->count() <= 1) {
            return Response::deny('全てのブックマークは削除できません');
        }

        return Response::allow();
    }
}
