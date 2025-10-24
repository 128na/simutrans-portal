<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Database\Eloquent\Model;

abstract class BasePolicy
{
    use HandlesAuthorization;

    protected function isSameUser(User $user, Model $model): bool
    {
        return isset($model->user_id) && $user->id === $model->user_id;
    }
}
