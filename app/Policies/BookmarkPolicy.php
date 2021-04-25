<?php

namespace App\Policies;

use App\Models\User;
use App\Models\User\Bookmark;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class BookmarkPolicy extends BasePolicy
{
    use HandlesAuthorization;

    public function show(User $user, Bookmark $bookmark)
    {
        return $this->isSameUser($user, $bookmark);
    }

    public function update(User $user, Bookmark $bookmark)
    {
        return $this->isSameUser($user, $bookmark);
    }

    public function download(User $user, Bookmark $bookmark)
    {
        return $this->isSameUser($user, $bookmark);
    }

    public function destroy(User $user, Bookmark $bookmark)
    {
        if (!$this->isSameUser($user, $bookmark)) {
            return Response::deny();
        }
        if ($user->bookmarks()->count() <= 1) {
            return Response::deny('全てのブックマークは削除できません');
        }

        return Response::allow();
    }
}
