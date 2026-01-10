<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\MyList;
use App\Models\User;

class MyListPolicy
{
    /**
     * リストを閲覧できるか（所有者のみ）
     */
    public function view(User $user, MyList $myList): bool
    {
        return $user->id === $myList->user_id;
    }

    /**
     * リストを更新できるか（所有者のみ）
     */
    public function update(User $user, MyList $myList): bool
    {
        return $user->id === $myList->user_id;
    }

    /**
     * リストを削除できるか（所有者のみ）
     */
    public function delete(User $user, MyList $myList): bool
    {
        return $user->id === $myList->user_id;
    }
}
